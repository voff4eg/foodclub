<?
class CComponentAjax
{
	var $componentID = '';

	var $bAjaxSession = false;
	var $bIFrameMode = false;

	var $componentName;
	var $componentTemplate;
	var $arParams;

	var $arCSSList;
	var $arHeadScripts;

	var $bShadow = true;
	var $bJump = true;
	var $bStyle = true;
	var $bHistory = true;

	var $bWrongRedirect = false;

	var $buffer_start_counter;
	var $buffer_finish_counter;

	var $bRestartBufferCalled;
	var $RestartBufferHandlerId;
	var $LocalRedirectHandlerId;

	var $currentUrl = false;
	var $dirname_currentUrl = false;
	var $basename_currentUrl = false;

	var $__nav_params = null;

	function CComponentAjax($componentName, $componentTemplate, &$arParams, $parentComponent)
	{
		if ($GLOBALS['USER']->IsAdmin())
		{
			if ($_GET['bitrix_disable_ajax'] == 'N')
			{
				unset($_SESSION['bitrix_disable_ajax']);
			}

			if ($_GET['bitrix_disable_ajax'] == 'Y' || $_SESSION['bitrix_disable_ajax'] == 'Y')
			{
				$_SESSION['bitrix_disable_ajax'] = 'Y';
				return;
			}
		}

		global $APPLICATION;

		if ($parentComponent)
			return false;

		$this->componentName = $componentName;
		$this->componentTemplate = $componentTemplate;
		$this->arParams = $arParams;

		$this->bShadow = $this->arParams['AJAX_OPTION_SHADOW'] != 'N';
		$this->bJump = $this->arParams['AJAX_OPTION_JUMP'] != 'N';
		$this->bStyle = $this->arParams['AJAX_OPTION_STYLE'] != 'N';
		$this->bHistory = $this->arParams['AJAX_OPTION_HISTORY'] != 'N';

		if (!$this->CheckSession())
			return false;

		CAjax::Init();

		$arParams['AJAX_ID'] = $this->componentID;

		if ($this->bAjaxSession)
		{
			// dirty hack: try to get breadcrumb call params
			for ($i = 0, $cnt = count($APPLICATION->buffer_content_type); $i < $cnt; $i++)
			{
				if ($APPLICATION->buffer_content_type[$i]['F'][1] == 'GetNavChain')
				{
					$this->__nav_params = $APPLICATION->buffer_content_type[$i]['P'];
				}
			}

			$APPLICATION->RestartBuffer();

			define('PUBLIC_AJAX_MODE', 1);

			if (is_set($_REQUEST, 'AJAX_CALL'))
			{
				$this->bIFrameMode = true;
			}
		}

		if ($this->bStyle)
			$this->arCSSList = $APPLICATION->sPath2css;

		$this->arHeadScripts = $APPLICATION->arHeadScripts;

		if (!$this->bAjaxSession)
			$APPLICATION->AddBufferContent(array($this, '__BufferDelimiter'));

		$this->buffer_start_counter = count($APPLICATION->buffer_content);

		$this->LocalRedirectHandlerId = AddEventHandler('main', 'OnBeforeLocalRedirect', array($this, "LocalRedirectHandler"));
		$this->RestartBufferHandlerId = AddEventHandler('main', 'OnBeforeRestartBuffer', array($this, 'RestartBufferHandler'));
	}

	function __BufferDelimiter()
	{
		return '';
	}

	function __removeHandlers()
	{
		RemoveEventHandler('main', 'OnBeforeRestartBuffer', $this->RestartBufferHandlerId);
		RemoveEventHandler('main', 'OnBeforeLocalRedirect', $this->LocalRedirectHandlerId);
	}

	function RestartBufferHandler()
	{
		global $APPLICATION;
		$this->bRestartBufferCalled = true;
		//ob_end_clean();

		$APPLICATION->AddBufferContent(array($this, '__BufferDelimiter'));
		$this->buffer_start_counter = count($APPLICATION->buffer_content);

		$this->__removeHandlers();
	}

	function LocalRedirectHandler(&$url)
	{
		if (!$this->bAjaxSession) return;

		if ($this->__isAjaxURL($url))
		{
			if (!$this->bIFrameMode)
				Header('X-Bitrix-Ajax-Status: OK');
		}
		else
		{
			if (!$this->bRestartBufferCalled)
				ob_end_clean();

			if (!$this->bIFrameMode)
				Header('X-Bitrix-Ajax-Status: Redirect');

			$this->bWrongRedirect = true;

			echo '<script type="text/javascript">'.($this->bIFrameMode ? 'top.' : 'window.').'location.href = \''.CUtil::JSEscape($url).'\'</script>';

			require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_after.php");
			exit();
		}

		$url = CAjax::AddSessionParam($url, $this->componentID);

		$this->__removeHandlers();
	}

	function CheckSession()
	{
		if ($this->componentID = CAjax::GetComponentID($this->componentName, $this->componentTemplate))
		{
			if ($current_session = CAjax::GetSession())
			{
				if ($this->componentID == $current_session)
				{
					$this->bAjaxSession = true;
					return true;
				}
				else
				{
					return false;
				}
			}
			return true;
		}
		return false;
	}

	function __GetSEFRealUrl($url)
	{
		$arResult = CUrlRewriter::GetList(array('QUERY' => $url));

		if (is_array($arResult) && count($arResult) > 0)
			return $arResult[0]['PATH'];
		else
			return false;
	}

	function __isAjaxURL($url)
	{
		global $APPLICATION;

		static $currentUrl = false;
		static $basename_currentUrl = false;
		static $dirname_currentUrl = false;

		if (strncmp($url, '#', 1) === 0) return false;
		if (strncmp($url, 'mailto:', 7) === 0) return false;
		if (strncmp($url, 'javascript:', 11) === 0) return false;

		if (strpos($url, '://') !== false) return false;

		if ($this->arParams['SEF_MODE'] == 'Y')
		{
			if ($url == POST_FORM_ACTION_URI)
				return true;

			$test_str = '/bitrix/urlrewrite.php?SEF_APPLICATION_CUR_PAGE_URL=';
			if (strncmp($url, $test_str, 52) === 0)
			{
				$url = urldecode(substr($url, 52));
			}

			$url = $this->__GetSEFRealUrl($url);

			if ($url === false)
				return false;
		}
		else
		{
			if (strpos($url, '?') !== false)
				$url = substr($url, 0, strpos($url, '?'));

			if (substr($url, -4) != '.php')
			{
				if (substr($url, -1) != '/')
					$url .= '/';

				$url .= 'index.php';
			}
		}

		if (!$currentUrl)
		{
			$currentUrl = $APPLICATION->GetCurPage();

			if ($this->arParams['SEF_MODE'] == 'Y')
				$currentUrl = $this->__getSEFRealUrl($currentUrl);

			if (strpos($currentUrl, '?') !== false)
				$currentUrl = substr($currentUrl, 0, strpos($currentUrl, '?'));

			if (substr($currentUrl, -4) != '.php')
			{
				if (substr($currentUrl, -1) != '/')
					$currentUrl .= '/';

				$currentUrl .= 'index.php';
			}

			$dirname_currentUrl = dirname($currentUrl);
			$basename_currentUrl = basename($currentUrl);
		}

		$dirname = dirname($url);
		if (
			(
				$dirname == $dirname_currentUrl
				||
				$dirname == ''
				||
				$dirname == '.'
			)
			&&
			basename($url) == $basename_currentUrl
		)
			return true;

		return false;
	}

	function __PrepareLinks(&$data)
	{
		$add_param = CAjax::GetSessionParam($this->componentID);
		$global_offset = 0;
		$link_offset = 0;

		$regexp_links = '/<a([^>]*)>[\s]*(.*?(?:[\s]*<\/a>))/i'.BX_UTF_PCRE_MODIFIER;
		$regexp_params = '/([\w]+)=\"([^\"]*)\"/i'.BX_UTF_PCRE_MODIFIER;

		preg_match_all($regexp_links, $data, $links, PREG_OFFSET_CAPTURE);

		foreach ($links[0] as $key => $arValue)
		{
			$link = $arValue[0];

			if (defined('BX_UTF') && BX_UTF === true)
			{
				$link_length = mb_strlen($link, LANG_CHARSET);
				$link_offset = mb_strpos($data, $link, $link_offset, LANG_CHARSET) - $global_offset;
			}
			else
			{
				$link_length = strlen($link);
				$link_offset = $arValue[1];
			}

			$params = $links[1][$key][0];

			$strAdditional = ' ';

			preg_match_all($regexp_params, $params, $arLinkParams);

			$url_key = -1;
			$bIgnoreLink = false;

			$arIgnoreAttributes = array('onclick' => '', 'target' => '');

			foreach ($arLinkParams[0] as $pkey => $value)
			{
				if ($value == '') continue;
				$param_name = strtolower($arLinkParams[1][$pkey]);

				if ($param_name === 'href')
					$url_key = $pkey;
				elseif (array_key_exists($param_name, $arIgnoreAttributes))
				{
					$bIgnoreLink = true;
					break;
				}
				else
					$strAdditional .= $value.' ';
			}

			if ($url_key >= 0 && !$bIgnoreLink)
			{
				$url = str_replace('&amp;', '&', $arLinkParams[2][$url_key]);

				$url = str_replace(array(
					$add_param.'&',
					$add_param,
					'AJAX_CALL=Y&',
					'AJAX_CALL=Y'
				), '', $url);

				if ($this->__isAjaxURL($url))
				{
					$real_url = $url;

					$pos = strpos($url, '#');
					if ($pos !== false)
						$real_url = substr($real_url, 0, $pos);

					$real_url .= strpos($url, '?') === false ? '?' : '&';
					$real_url .= $add_param;

					$link_text = substr($links[2][$key][0], 0, -4);

					$url_str = CAjax::GetLinkEx($real_url, $url, $link_text, 'comp_'.$this->componentID, $strAdditional, true, $this->bShadow);
					$new_len = function_exists("mb_strlen") ? mb_strlen($url_str, LANG_CHARSET) : strlen($url_str);//strlen($url_str);

					$data =
						substr($data, 0, $link_offset + $global_offset)
						.$url_str
						.substr($data, $link_offset + $global_offset + $link_length);

					$global_offset += ($new_len - $link_length);
				}
			}
		}
	}

	function __PrepareForms(&$data)
	{
		if (preg_match_all('/<form([^>]*)>/i', $data, $arResult))
		{
			foreach ($arResult[0] as $key => $tag)
			{
				preg_match_all('/action=(["\']{1})(.*?)\1/i', $arResult[1][$key], $arAction);
				$url = $arAction[2][0];

				if ($url === '' || $this->__isAjaxURL($url))
				{
					$new_tag = CAjax::GetForm($arResult[1][$key], 'comp_'.$this->componentID, $this->componentID, true, $this->bShadow);
				}
				else
				{
					$new_url = str_replace(CAjax::GetSessionParam($ajax_id), '', $url);
					$new_tag = str_replace($url, $new_url, $tag);
				}

				$data = str_replace($tag, $new_tag, $data);
			}
		}
	}

	function __prepareScripts(&$data)
	{
		$regexp = '/(<script([^>]*)?>)([\S\s]*?)(<\/script>)/i';

		$scripts_num = preg_match_all($regexp, $data, $out);

		$arScripts = array();

		if (false !== $scripts_num)
		{
			for ($i = 0; $i < $scripts_num; $i++)
			{
				$data = str_replace($out[0][$i], '', $data);

				if (strlen($out[2][$i]) > 0 && strpos($out[2][$i], 'src=') !== false)
				{
					$regexp_src = '/src="([^"]*)?"/i';
					if (preg_match($regexp_src, $out[2][$i], $out1) != 0)
					{
						$arScripts[] = array(
							'TYPE' => 'SCRIPT_SRC',
							'DATA' => $out1[1],
						);
					}
				}
				else
				{
					$out[3][$i] = str_replace('<!--', '', $out[3][$i]);
					$arScripts[] = array(
						'TYPE' => 'SCRIPT',
						'DATA' => $out[3][$i],
					);
				}
			}
		}

		if (count($arScripts) > 0)
		{
			$data .= '<script type="text/javascript">top.jsAjaxUtil.EvalPack('.CUtil::PhpToJsObject($arScripts).');</script>';
		}
	}

	function _PrepareAdditionalData()
	{
		global $APPLICATION;

		// get CSS changes list
		if ($this->bStyle)
		{
			$arCSSList = $APPLICATION->sPath2css;

			$cnt_old = count($this->arCSSList);
			$cnt_new = count($arCSSList);
			$arCSSNew = array();

			if ($cnt_old != $cnt_new)
				for ($i = $cnt_old; $i<$cnt_new; $i++)
					$arCSSNew[] = $arCSSList[$i];
		}

		// get scripts changes list
		$arHeadScripts = $APPLICATION->arHeadScripts;

		$cnt_old = count($this->arHeadScripts);
		$cnt_new = count($arHeadScripts);
		$arHeadScriptsNew = array();

		if ($cnt_old != $cnt_new)
			for ($i = $cnt_old; $i<$cnt_new; $i++)
				$arHeadScriptsNew[] = $arHeadScripts[$i];

		// prepare additional data
		$arAdditionalData = array();
		$arAdditionalData['TITLE'] = htmlspecialcharsback($APPLICATION->GetTitle());
		$arAdditionalData['SCRIPTS'] = $arHeadScriptsNew;

		if (null !== $this->__nav_params)
		{
			$arAdditionalData['NAV_CHAIN'] = $APPLICATION->GetNavChain($this->__nav_params[0], $this->__nav_params[1], $this->__nav_params[2], $this->__nav_params[3], $this->__nav_params[4]);
		}

		if ($this->bStyle)
			$arAdditionalData["CSS"] = $arCSSNew;

		$additional_data = '<script type="text/javascript">'."\n";
		$additional_data .= 'var arAjaxPageData = '.CUtil::PhpToJSObject($arAdditionalData).";\r\n";
		$additional_data .= ($this->bIFrameMode ? 'top.' : '').'jsAjaxUtil.UpdatePageData(arAjaxPageData)'.";\r\n";
		if (!$this->bIFrameMode && $this->bHistory)
			$additional_data .= 'jsAjaxHistory.put(\'comp_'.$this->componentID.'\', \''.CUtil::JSEscape(CAjax::encodeURI($APPLICATION->GetCurPageParam('', array(BX_AJAX_PARAM_ID), false))).'\')'.";\r\n";

		if ($this->bJump)
		{
			if ($this->bIFrameMode)
				$additional_data .= 'top.setTimeout(\'jsAjaxUtil.ScrollToNode("comp_'.$this->componentID.'")\', 100)'.";\r\n";
			else
				$additional_data .= 'jsAjaxUtil.ScrollToNode(\'comp_'.$this->componentID.'\')'.";\r\n";
		}

		$additional_data .= '</script>';

		echo $additional_data;
	}

	function _PrepareData()
	{
		global $APPLICATION;

		if ($this->bWrongRedirect)
			return;

		$arBuffer = array_slice($APPLICATION->buffer_content, $this->buffer_start_counter, $this->buffer_finish_counter - $this->buffer_start_counter);

		$data = implode('###AJAX_DELIMITER###', $arBuffer);

		$this->__PrepareLinks($data);
		$this->__PrepareForms($data);

		if (!$this->bAjaxSession)
		{
			$data = '<div id="comp_'.$this->componentID.'">'.$data.'</div>';

			if ($this->bHistory)
			{
				$data =
					'<script type="text/javascript">if (window.location.hash != \'\' && window.location.hash != \'#\') jsAjaxHistory.checkRedirectStart(\''.CUtil::JSEscape(BX_AJAX_PARAM_ID).'\', \''.CUtil::JSEscape($this->componentID).'\')</script>'
					.$data
					.'<script type="text/javascript">if (jsAjaxHistory.bHashCollision) jsAjaxHistory.checkRedirectFinish(\''.CUtil::JSEscape(BX_AJAX_PARAM_ID).'\', \''.CUtil::JSEscape($this->componentID).'\');</script>'
					.'<script type="text/javascript">jsEvent.addEvent(window, \'load\', function() {jsAjaxHistory.init(\'comp_'.$this->componentID.'\');})</script>';
			}
		}
		else
		{
			if ($this->bIFrameMode)
			{
				$this->__PrepareScripts($data);

				// fix IE bug;
				$data = '<html><head></head><body>'.$data.'</body></html>';
			}
		}

		$arBuffer = explode('###AJAX_DELIMITER###', $data);
		for ($i = 0, $cnt = count($arBuffer); $i < $cnt; $i++)
		{
			$APPLICATION->buffer_content[$this->buffer_start_counter + $i] = $arBuffer[$i];
		}

		return '';
	}

	function Process()
	{
		global $APPLICATION;

		if (strlen($this->componentID) <= 0)
			return;

		$this->buffer_finish_counter = count($APPLICATION->buffer_content)+1;

		$APPLICATION->AddBufferContent(array($this, '_PrepareData'));

		$this->__removeHandlers();

		if ($this->bAjaxSession)
		{
			AddEventHandler('main', 'onAfterAjaxResponse', array($this, '_PrepareAdditionalData'));

			$APPLICATION->AddBufferContent(array('CComponentAjax', 'ExecuteEvents'));

			require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_after.php");
			exit();
		}
	}

	// will be called as delay function and not in class entity context
	function ExecuteEvents()
	{
		$dbEvents = GetModuleEvents('main', 'onAfterAjaxResponse');
		while ($arEvent = $dbEvents->Fetch())
		{
			echo ExecuteModuleEvent($arEvent);
		}
	}
}
?>