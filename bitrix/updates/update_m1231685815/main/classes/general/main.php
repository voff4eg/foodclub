<?
##############################################
# Bitrix Site Manager                        #
# Copyright (c) 2002-2007 Bitrix             #
# http://www.bitrixsoft.com                  #
# mailto:admin@bitrixsoft.com                #
##############################################

define('BX_VALID_FILENAME_SYMBOLS', '\x20-\x21\x2B-\x2E\x30-\x39\x41-\x5A\x5F\x61-\x7A\x7B\x7C\x7E');
define('BX_SPREAD_SITES', 2);
define('BX_SPREAD_DOMAIN', 4);

global $BX_CACHE_DOCROOT;
$BX_CACHE_DOCROOT = Array();
global $MODULE_PERMISSIONS;
$MODULE_PERMISSIONS = Array();

class CAllMain
{
	var $ma, $mapos;
	var $sDocPath2, $sDirPath, $sUriParam;
	var $sDocTitle;
	var $arPageProperties = array();
	var $arDirProperties = array();
	var $bDirProperties = false;
	var $sLastError;
	var $sPath2css = array();
	var $arHeadStrings = array();
	var $arHeadScripts = array();
	var $version;
	var $arAdditionalChain = array();
	var $FILE_PERMISSION_CACHE = array();
	var $arPanelButtons = array();
	var $ShowLogout = false;
	var $ShowPanel = NULL, $PanelShowed = false;
	var $arrSPREAD_COOKIE = array();
	var $buffer_content = array();
	var $buffer_content_type = array();
	var $buffer_man = false;
	var $auto_buffer_cleaned, $buffered = false;
	var $LAST_ERROR = false;
	var $ERROR_STACK = array();
	var $arIncludeDebug = array();
	var $includeAreaIndex = array();
	var $includeLevel = -1;
	var $aCachedComponents = array();

	function __construct()
	{
		$this->CMain();
	}

	function CMain()
	{
		global $QUERY_STRING;
		$this->sDocPath2 = GetPagePath();
		$this->sDirPath = GetDirPath($this->sDocPath2);
		$this->sUriParam = (strlen($_SERVER["QUERY_STRING"])>0) ? $_SERVER["QUERY_STRING"] : $QUERY_STRING;
	}

	function GetCurUri($addParam="")
	{
		$page = $this->GetCurPage();
		$param = $this->GetCurParam();
		if(strlen($param)>0)
			$url .= $page."?".$param.($addParam!=""?"&".$addParam:"");
		else
			$url .= $page.($addParam!=""?"?".$addParam:"");
		return $url;
	}

	function GetCurParam()
	{
		return $this->sUriParam;
	}

	function GetCurPage()
	{
		return substr($this->sDocPath2, 0, strlen($this->sDocPath2));
	}

	function SetCurPage($page, $param=false)
	{
		$this->sDocPath2 = GetPagePath($page);
		$this->sDirPath = GetDirPath($this->sDocPath2);
		if($param!==false) $this->sUriParam = $param;
	}

	function GetCurDir()
	{
		return $this->sDirPath;
	}

	function GetFileRecursive($strFileName, $strDir=false)
	{
		global $DOCUMENT_ROOT;

		if($strDir===false)
			$strDir = $this->GetCurDir();

		$strDir = str_replace("\\", "/", $strDir);
		while(strlen($strDir)>0 && $strDir[strlen($strDir)-1]=="/")
			$strDir = substr($strDir, 0, strlen($strDir)-1);

		while(!file_exists($DOCUMENT_ROOT.$strDir."/".$strFileName))
		{
			$p = bxstrrpos($strDir, "/");
			if($p===false) break;
			$strDir = substr($strDir, 0, $p);
		}
		if($p===false)
			return false;

		return $strDir."/".$strFileName;
	}

	function GetCurPageParam($strParam = "", $arParamKill = array(), $get_index_page=true)
	{
		$sUrlPath = GetPagePath(false, $get_index_page);
		$strNavQueryString = DeleteParam($arParamKill);
		if($strNavQueryString <> "" && $strParam <> "")
			$strNavQueryString = "&".$strNavQueryString;
		if($strNavQueryString == "" && $strParam == "")
			return $sUrlPath;
		else
			return $sUrlPath."?".$strParam.$strNavQueryString;
	}

	function IncludeAdminFile($strTitle, $filepath)
	{
		//define all global vars
		$keys = array_keys($GLOBALS);
		for($i=0; $i<count($keys); $i++)
			if($keys[$i]!="i" && $keys[$i]!="GLOBALS" && $keys[$i]!="strTitle" && $keys[$i]!="filepath")
				global ${$keys[$i]};

		//title
		$APPLICATION->SetTitle($strTitle);

		//в зависимости от параметров покажем форму
		include($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");
		include($filepath);
		include($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");
		die();
	}

	function AuthForm($mess, $show_prolog=true, $show_epilog=true, $not_show_links="N")
	{
		//сдалаем все глобальные переменные видимыми здесь
		$keys = array_keys($GLOBALS);
		for($i=0; $i<count($keys); $i++)
			if($keys[$i]!="i" && $keys[$i]!="GLOBALS" && $keys[$i]!="mess")
				global ${$keys[$i]};

		if(substr($this->GetCurDir(), 0, strlen(BX_ROOT."/admin/")) == BX_ROOT."/admin/" || (defined("ADMIN_SECTION") && ADMIN_SECTION===true))
			$isAdmin = "_admin";
		else
			$isAdmin = "";

		if(isset($this->arAuthResult) && $this->arAuthResult !== true && (is_array($this->arAuthResult) || strlen($this->arAuthResult)>0))
			$arAuthResult = $this->arAuthResult;
		else
			$arAuthResult = $mess;

		//заголовок страницы
		$APPLICATION->SetTitle(GetMessage("AUTH_TITLE"));

		//вытащим из cookies последнее удачное имя входа
		$last_login = ${COption::GetOptionString("main", "cookie_name", "BITRIX_SM")."_LOGIN"};

		$inc_file = "";
		$comp_name = "";
		if($forgot_password=="yes")
		{
			//форма высылки пароля
			$APPLICATION->SetTitle(GetMessage("AUTH_TITLE_SEND_PASSWORD"));
			$comp_name = "system.auth.forgotpasswd";
			$inc_file = "forgot_password.php";
		}
		elseif($change_password=="yes")
		{
			//форма изменения пароля
			$APPLICATION->SetTitle(GetMessage("AUTH_TITLE_CHANGE_PASSWORD"));
			$comp_name = "system.auth.changepasswd";
			$inc_file = "change_password.php";
		}
		elseif($register=="yes" && $isAdmin==""	&& COption::GetOptionString("main", "new_user_registration", "N")=="Y")
		{
			//форма регистрации
			$APPLICATION->SetTitle(GetMessage("AUTH_TITLE_REGISTER"));
			$comp_name = "system.auth.registration";
			$inc_file = "registration.php";
		}
		elseif(($confirm_registration === "yes") && ($isAdmin === "") && (COption::GetOptionString("main", "new_user_registration_email_confirmation", "N") === "Y"))
		{
			//confirm registartion
			$APPLICATION->SetTitle(GetMessage("AUTH_TITLE_CONFIRM"));
			$comp_name = "system.auth.confirmation";
			$inc_file = "confirmation.php";
		}
		elseif($authorize_registration=="yes" && $isAdmin=="")
		{
			//форма авторизации и регистрации
			$inc_file = "authorize_registration.php";
		}
		else
		{
			//форма авторизации
			$comp_name = "system.auth.authorize";
			$inc_file = "authorize.php";
		}

		if($show_prolog)
		{
			if($isAdmin=="" && COption::GetOptionString("main", "buffer_content", "Y")=="Y" && (!defined("BX_BUFFER_USED") || BX_BUFFER_USED!==true))
			{
				ob_start(Array(&$APPLICATION, "EndBufferContent"));
				define("BX_BUFFER_USED", true);
			}
			define("BX_AUTH_FORM", true);
			include($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog".$isAdmin. "_after.php");
		}

		if($isAdmin == "")
		{
			// если пользуем вторые компоненты и есть что подключать - подключаем
			if(COption::GetOptionString("main", "auth_comp2", "N") == "Y" && $comp_name <> "")
			{
				$this->IncludeComponent("bitrix:".$comp_name, "", array(
					"AUTH_RESULT" => $arAuthResult,
					"NOT_SHOW_LINKS" => $not_show_links,
				));
			}
			else
			{
				$this->IncludeFile("main/auth/".$inc_file, Array("last_login"=>$last_login, "arAuthResult"=>$arAuthResult, "not_show_links" => $not_show_links));
			}
		}
		else
		{
			include($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/interface/auth/".$inc_file);
		}

		if($show_epilog)
		{
			include($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog".$isAdmin.".php");
		}
		die();
	}

	function GetMenuHtml($type="left", $bMenuExt=false, $template = false, $sInitDir = false)
	{
		$menu = $this->GetMenu($type, $bMenuExt, $template, $sInitDir);
		return $menu->GetMenuHtml();
	}

	function GetMenuHtmlEx($type="left", $bMenuExt=false, $template = false, $sInitDir = false)
	{
		$menu = $this->GetMenu($type, $bMenuExt, $template, $sInitDir);
		return $menu->GetMenuHtmlEx();
	}

	function GetMenu($type="left", $bMenuExt=false, $template = false, $sInitDir = false)
	{
		$menu = new CMenu($type);
		if($sInitDir===false)
			$sInitDir = $this->GetCurDir();
		if(!$menu->Init($sInitDir, $bMenuExt, $template))
			$menu->MenuDir = $sInitDir;
		return $menu;
	}

	function IsHTTPS()
	{
		return ($_SERVER["SERVER_PORT"]==443 || strtolower($_SERVER["HTTPS"])=="on");
	}

	function GetTitle($property_name = false, $strip_tags = false)
	{
		if($property_name!==false && strlen($this->GetProperty($property_name))>0)
			$res = $this->GetProperty($property_name);
		else
			$res = $this->sDocTitle;
		if($strip_tags)
			return strip_tags($res);
		return $res;
	}

	function SetTitle($title)
	{
		$this->sDocTitle = $title;
	}

	function ShowTitle($property_name="title", $strip_tags = true)
	{
		$this->AddBufferContent(Array(&$this, "GetTitle"), $property_name, $strip_tags);
	}

	function SetPageProperty($PROPERTY_ID, $PROPERTY_VALUE)
	{
		$this->arPageProperties[strtoupper($PROPERTY_ID)] = $PROPERTY_VALUE;
	}

	function GetPageProperty($PROPERTY_ID, $default_value = false)
	{
		if(is_set($this->arPageProperties, strtoupper($PROPERTY_ID)))
			return $this->arPageProperties[strtoupper($PROPERTY_ID)];
		return $default_value;
	}

	function ShowProperty($PROPERTY_ID, $default_value = false)
	{
		$this->AddBufferContent(Array(&$this, "GetProperty"), $PROPERTY_ID, $default_value);
	}

	function GetProperty($PROPERTY_ID, $default_value = false)
	{
		$propVal = $this->GetPageProperty($PROPERTY_ID);
		if($propVal !== false)
			return $propVal;

		$propVal = $this->GetDirProperty($PROPERTY_ID);

		if($propVal !== false)
			return $propVal;

		return $default_value;
	}

	function GetPagePropertyList()
	{
		return $this->arPageProperties;
	}

	function SetDirProperty($PROPERTY_ID, $PROPERTY_VALUE)
	{
		$this->arDirProperties[strtoupper($PROPERTY_ID)] = $PROPERTY_VALUE;
	}

	function InitPathVars(&$site, &$path)
	{
		$site = false;
		if(is_array($path))
		{
			$site = $path[0];
			$path = $path[1];
		}
		return $path;
	}

	function InitDirProperties($path)
	{
		CMain::InitPathVars($site, $path);
		$DOC_ROOT = CSite::GetSiteDocRoot($site);

		if($this->bDirProperties)
			return true;

		if($path===false)
			$path = $this->GetCurDir();

		while (true)		// будем до корня искать
		{
			// отрежем / в конце
			while (strlen($path)>0 && $path[strlen($path)-1]=="/")
				$path = substr($path, 0, strlen($path)-1);

			$section_file_name = $DOC_ROOT.$path."/.section.php";

			if(file_exists($section_file_name))
			{
				$arDirProperties = false;
				include($section_file_name);
				if(is_array($arDirProperties))
				{
					foreach($arDirProperties as $prid=>$prval)
						if(!is_set($this->arDirProperties, strtoupper($prid)))
							$this->arDirProperties[strtoupper($prid)] = $prval;
				}
			}

			if(strlen($path)<=0)
				break;

			// найдем имя файла или папки
			$pos = bxstrrpos($path, "/");
			if($pos===false)
				break;

			//найдем папку-родителя
			$path = substr($path, 0, $pos+1);
		}

		$this->bDirProperties = true;
		return true;
	}

	function GetDirProperty($PROPERTY_ID, $path=false, $default_value = false)
	{
		CMain::InitPathVars($site, $path);
		$DOC_ROOT = CSite::GetSiteDocRoot($site);

		if($this->bDirProperties)
		{
			if(is_set($this->arDirProperties, strtoupper($PROPERTY_ID)))
				return $this->arDirProperties[strtoupper($PROPERTY_ID)];
			return $default_value;
		}

		if($path===false)
			$path = $this->GetCurDir();

		$this->InitDirProperties(Array($site, $path));

		if(is_set($this->arDirProperties, strtoupper($PROPERTY_ID)))
			return $this->arDirProperties[strtoupper($PROPERTY_ID)];

		return $default_value;
	}

	function GetDirPropertyList($path=false)
	{
		CMain::InitPathVars($site, $path);
		$DOC_ROOT = CSite::GetSiteDocRoot($site);

		if($this->bDirProperties)
			return $this->arDirProperties;

		if($path===false)
			$path = $this->GetCurDir();

		$this->InitDirProperties(Array($site, $path));

		if(is_array($this->arDirProperties))
			return $this->arDirProperties;

		return false;
	}

	function GetMeta($id, $meta_name=false)
	{
		if($meta_name==false)
			$meta_name=$id;
		$val = $this->GetProperty($id);
		if(!empty($val))
			return '<meta name="'.htmlspecialchars($meta_name).'" content="'.htmlspecialchars($val).'" />'."\n";
		return '';
	}

	function ShowBanner($type, $html_before="", $html_after="")
	{
		if(!CModule::IncludeModule("advertising"))
			return false;

		global $APPLICATION;
		$APPLICATION->AddBufferContent(Array("CAdvBanner", "Show"), $type, $html_before, $html_after);
	}

	function ShowMeta($id, $meta_name=false)
	{
		$this->AddBufferContent(Array(&$this, "GetMeta"), $id, $meta_name);
	}

	function SetAdditionalCSS($Path2css)
	{
		$this->sPath2css[] = $Path2css;
	}
	function GetAdditionalCSS()
	{
		if(count($this->sPath2css)>0)
			return $this->sPath2css[count($this->sPath2css)-1];
		return false;
	}
	function GetCSS($bExternal = true)
	{
		$res = "";
		$arCSS = $this->sPath2css;
		global $USER;
		if(isset($_GET['bx_template_preview_mode']) && $_GET['bx_template_preview_mode'] == 'Y' && $USER->CanDoOperation('edit_other_settings'))
		{
			$path = BX_PERSONAL_ROOT."/tmp/templates/__bx_preview/";
			$arCSS[] = $path."styles.css";
			$arCSS[] = $path."template_styles.css";
		}
		elseif(defined("SITE_TEMPLATE_ID"))
		{
			$path = BX_PERSONAL_ROOT."/templates/".SITE_TEMPLATE_ID;
			$arCSS[] = $path."/styles.css";
			$arCSS[] = $path."/template_styles.css";
		}
		else
		{
			$path = BX_PERSONAL_ROOT."/templates/.default";
			$arCSS[] = $path."/styles.css";
			$arCSS[] = $path."/template_styles.css";
		}

		$arCSS = array_unique($arCSS);
		foreach($arCSS as $css_path)
		{
			//explicit link
			$bLink = (strncmp($css_path, 'http://', 7) == 0
				|| strncmp($css_path, 'https://', 8) == 0
				|| strpos($css_path, '?') !== false
			);

			$filename = $_SERVER["DOCUMENT_ROOT"].$css_path;
			if(($bExternal || $bLink) && strncmp($css_path, '/bitrix/modules/', 16) != 0)
			{
				if($bLink || file_exists($filename))
					$res .= '<link href="'.$css_path.'" type="text/css" rel="stylesheet" />'."\n";
			}
			elseif(!$bLink && file_exists($filename))
			{
				if($handle = fopen($filename, "r"))
				{
					$contents = fread($handle, filesize($filename));
					fclose($handle);
				}
				$res .= "<style type='text/css'>\n".$contents."\n</style>\n";
			}
		}
		return $res;
	}
	function ShowCSS($bExternal = true)
	{
		$this->AddBufferContent(Array(&$this, "GetCSS"), $bExternal);
	}

	function AddHeadString($str, $bUnique=false)
	{
		if($bUnique)
		{
			$check_sum = md5($str);
			if(!array_key_exists($check_sum, $this->arHeadStrings))
				$this->arHeadStrings[$check_sum] = $str;
		}
		else
			$this->arHeadStrings[] = $str;
	}
	function GetHeadStrings()
	{
		return implode("\n", $this->arHeadStrings)."\n";
	}
	function ShowHeadStrings()
	{
		$this->AddBufferContent(Array(&$this, "GetHeadStrings"));
	}

	function AddHeadScript($src)
	{
		$this->arHeadScripts[] = $src;
	}
	function GetHeadScripts()
	{
		$arScripts = array_unique($this->arHeadScripts);
		$res = "";
		foreach($arScripts as $src)
			$res .= '<script type="text/javascript" src="'.$src.'"></script>'."\n";
		return $res;
	}
	function ShowHeadScripts()
	{
		$this->AddBufferContent(array(&$this, "GetHeadScripts"));
	}

	function ShowHead()
	{
		echo '<meta http-equiv="Content-Type" content="text/html; charset='.LANG_CHARSET.'" />'."\n";
		$this->ShowMeta("robots");
		$this->ShowMeta("keywords");
		$this->ShowMeta("description");
		$this->ShowCSS();
		$this->ShowHeadStrings();
		$this->ShowHeadScripts();
	}

	function SetShowIncludeAreas($bShow=true)
	{
		$_SESSION["SESS_INCLUDE_AREAS"] = $bShow;
	}

	function GetShowIncludeAreas()
	{
		if(!$GLOBALS["USER"]->IsAuthorized())
			return false;
		return $_SESSION["SESS_INCLUDE_AREAS"];
	}

	function SetPublicShowMode($mode)
	{
		if($mode == "edit" || $mode == "configure")
			$_SESSION["SESS_PUBLIC_SHOW_MODE"] = $mode;
		else
			$_SESSION["SESS_PUBLIC_SHOW_MODE"] = "view";
	}

	function GetPublicShowMode()
	{
		if(isset($_SESSION["SESS_PUBLIC_SHOW_MODE"]) && $_SESSION["SESS_PUBLIC_SHOW_MODE"] <> "")
			return $_SESSION["SESS_PUBLIC_SHOW_MODE"];
		return "view";
	}

	function DrawIcons($arIcons, $arParams=array())
	{
		if(!is_array($arIcons) || count($arIcons)==0)
			return "";

		$res = '';
		$res .= '<script type="text/javascript">'."\r\n";
		$arJSIcons = array();
		foreach ($arIcons as $arIcon)
		{
			if(isset($arIcon['SEPARATOR']))
			{
				$arJSIcons[] = array('SEPARATOR' => 'Y');
			}
			else
			{
				$url = $arIcon['URL'];
				if(strtolower(substr($url, 0, 11)) == 'javascript:')
					$url = substr($url, 11);
				else
					$url = 'jsUtils.Redirect(arguments, \''.CUtil::JSEscape($url).'\')';

				$jsIcon = array(
					'ICONCLASS' => $arIcon['ICON'],
					'ONCLICK' => $url,
					'TITLE' => $arIcon['ALT'],
					'TEXT' => $arIcon['TITLE'],
				);
				if(isset($arIcon['DEFAULT']) && $arIcon['DEFAULT'] == true)
					$jsIcon['DEFAULT'] = true;
				if(isset($arIcon['IMAGE']))
					$jsIcon['IMAGE'] = $arIcon['IMAGE'];
				elseif(isset($arIcon['SRC']))
					$jsIcon['IMAGE'] = $arIcon['SRC'];

				$arJSIcons[] = $jsIcon;
			}
		}

		$areaId = $this->__GetAreaId();
		$res .= 'var arItems_'.$areaId.' = '.CUtil::PhpToJSObject($arJSIcons).';'."\n";
		$res .= 'var obMenu_'.$areaId.' = new PopupMenu(\'menu_'.$areaId.'\');'."\n";
		$res .= '</script>';

		$res .= '
<div class="bx-component-panel"'.($this->includeLevel > 0? ' style="top:'.(-26+26*$this->includeLevel).'px;"':'').' id="bx_incl_area_panel_'.$areaId.'" title="">
<table cellspacing="0">
	<tr>
		<td class="bx-panel-left" onmousedown="jsPopup.DragPanel(arguments[0], this);"><div class="empty"></div></td>
		<td class="bx-panel-middle"><div class="empty left"></div></td>
		<td class="bx-panel-middle"><div class="bx-panel-icon-cont" onmouseover="this.className = this.className.replace(/\s*bx-panel-icon-cont/ig, \'bx-panel-icon-cont-hover\')" onmouseout="this.className = this.className.replace(/\s*bx-panel-icon-cont-hover/ig, \'bx-panel-icon-cont\')" onclick="'.(isset($arParams["TOOLTIP"])? 'oBXHint'.$areaId.'.Freeze();':'').'obMenu_'.$areaId.'.ShowMenu(this, arItems_'.$areaId.', false, false, function(){oBXHint'.$areaId.'.UnFreeze();})"><div class="bx-panel-icon '.(isset($arParams["ICON"])? $arParams["ICON"]:'parameters-all').'"></div></div></td>
		<td class="bx-panel-middle"><div class="empty bx-panel-right"></div></td>
		<td class="bx-panel-right"><div class="empty"></div></td>
	</tr>
</table>
';
		if(isset($arParams["TOOLTIP"]))
		{
			$res .= '
<script type="text/javascript">
var oBXHint'.$areaId.' = new BXHint(\''.CUtil::JSEscape($arParams["TOOLTIP"]).'\', document.getElementById("bx_incl_area_panel_'.$areaId.'"), {width: false});
</script>';
		}
		$res .= '</div>';

		return $res;
	}

	function __GetAreaId()
	{
		return implode("_", array_slice($this->includeAreaIndex, 0, $this->includeLevel+1));
	}

	function IncludeStringBefore($arIcons=false)
	{
		$this->includeLevel++;
		$this->includeAreaIndex[$this->includeLevel] = intval($this->includeAreaIndex[$this->includeLevel])+1;
		unset($this->includeAreaIndex[$this->includeLevel+1]);

		$areaId = $this->__GetAreaId();

		$res = '<div class="bx-component-border" onmouseover="this.className=\'bx-component-border bx-component-border-over\'" onmouseout="this.className=\'bx-component-border\'"><div id="bx_incl_area_'.$areaId.'">';
		$res .= $this->DrawIcons($arIcons);
		return $res;
	}

	function IncludeStringAfter($arIcons=false, $arParams=array())
	{
		$res = '';
		$res .= '</div>';
		$res .= $this->DrawIcons($arIcons, $arParams);
		$res .= '</div>';
		$areaId = $this->__GetAreaId();
		if(is_array($arIcons))
		{
			foreach($arIcons as $arIcon)
			{
				if(isset($arIcon['DEFAULT']) && $arIcon['DEFAULT'] == true)
				{
					$url = $arIcon['URL'];
					if (strtolower(substr($url, 0, 11)) == 'javascript:')
						$url = substr($url, 11);
					else
						$url = 'jsUtils.Redirect(arguments, \''.CUtil::JSEscape($url).'\')';
					$res .= '<script type="text/javascript">
					var bx_incl_area = document.getElementById("bx_incl_area_'.$areaId.'");
					if(bx_incl_area)
					{
						bx_incl_area.title = \''.CUtil::JSEscape(GetMessage("main_incl_area_doubleclick").' - '.$arIcon['TITLE']).'\';
						bx_incl_area.ondblclick = function(e){if (!e) e = window.event; e.cancelBubble=true; '.$url.';};
					}
					</script>';
					break;
				}
			}
		}

		$this->includeLevel--;
		return $res;
	}

	function IncludeString($string, $arIcons=false)
	{
		$res  = $this->IncludeStringBefore($arIcons);
		$res .= $string;
		$res .= $this->IncludeStringAfter();
		return $res;
	}

	function GetTemplatePath($rel_path)
	{
		if(substr($rel_path, 0, 1)!="/")
		{
			$path = BX_PERSONAL_ROOT."/templates/".SITE_TEMPLATE_ID."/".$rel_path;
			if(file_exists($_SERVER["DOCUMENT_ROOT"].$path))
				return $path;

			$path = BX_PERSONAL_ROOT."/templates/.default/".$rel_path;
			if(file_exists($_SERVER["DOCUMENT_ROOT"].$path))
				return $path;

			$module_id = substr($rel_path, 0, strpos($rel_path, "/"));
			if(strlen($module_id)>0)
			{
				$path = "/bitrix/modules/".$module_id."/install/templates/".$rel_path;
				if(file_exists($_SERVER["DOCUMENT_ROOT"].$path))
					return $path;
			}

			return false;
		}

		return $rel_path;
	}

	function SetTemplateCSS($rel_path)
	{
		if($path = $this->GetTemplatePath($rel_path))
			$this->SetAdditionalCSS($path);
	}

	// >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
	// COMPONENTS 2.0 >>>>>
	function IncludeComponent($componentName, $componentTemplate, $arParams = array(), $parentComponent = null, $arFunctionParams = array())
	{
		global $APPLICATION, $USER;

		$componentRelativePath = CComponentEngine::MakeComponentPath($componentName);
		if (StrLen($componentRelativePath) <= 0)
			return False;

		if($_SESSION["SESS_SHOW_INCLUDE_TIME_EXEC"]=="Y" && ($USER->CanDoOperation('edit_php') || $_SESSION["SHOW_SQL_STAT"]=="Y"))
		{
			$debug = new CDebugInfo();
			$debug->Start();
		}

		if ($parentComponent)
		{
			if (strtolower(get_class($parentComponent)) != "cbitrixcomponent")
				$parentComponent = null;
		}

		$bDrawIcons = false;
		$bSrcFound = false;
		$sSrcFile = "";
		$iSrcLine = 0;
		if((!array_key_exists("HIDE_ICONS", $arFunctionParams) || $arFunctionParams["HIDE_ICONS"] != "Y")
			&& $APPLICATION->GetShowIncludeAreas())
		{
			if(function_exists("debug_backtrace"))
			{
				$aTrace = debug_backtrace();

				$sSrcFile = str_replace("\\", "/", $aTrace[0]["file"]);
				$iSrcLine = intval($aTrace[0]["line"]);

				if($iSrcLine > 0 && $sSrcFile <> "")
				{
					// try to covert absolute path to file within DOCUMENT_ROOT
					$doc_root = strtolower(str_replace("\\", "/", realpath($_SERVER["DOCUMENT_ROOT"])));
					if(strpos(strtolower($sSrcFile), $doc_root) === 0)
					{
						//within
						$sSrcFile = substr($sSrcFile, strlen($doc_root));
						$bSrcFound = true;
					}
					else
					{
						//outside
						$sRealBitrix = strtolower(str_replace("\\", "/", realpath($_SERVER["DOCUMENT_ROOT"]."/bitrix")));
						if(strpos(strtolower($sSrcFile), $sRealBitrix) === 0)
						{
							$sSrcFile = "/bitrix".substr($sSrcFile, strlen($sRealBitrix));
							$bSrcFound = true;
						}
					}
				}
			}

			$bDrawIcons = ($USER->CanDoOperation('edit_php')
				|| $USER->CanDoOperation('cache_control')
				|| $bSrcFound && $USER->CanDoFileOperation('fm_lpa', array(SITE_ID, $sSrcFile)));
		}
		if($bDrawIcons)
			echo $this->IncludeStringBefore();

		if ($arParams['AJAX_MODE'] == 'Y')
		{
			$obAjax = new CComponentAjax($componentName, $componentTemplate, $arParams, $parentComponent);
		}

		$result = null;
		$component = new CBitrixComponent();
		if ($component->InitComponent($componentName))
			$result = $component->IncludeComponent($componentTemplate, $arParams, $parentComponent);

		if ($arParams['AJAX_MODE'] == 'Y')
		{
			$obAjax->Process();
		}

		if($_SESSION["SESS_SHOW_INCLUDE_TIME_EXEC"]=="Y" && ($USER->CanDoOperation('edit_php') || $_SESSION["SHOW_SQL_STAT"]=="Y"))
			echo $debug->Output($componentName, "/bitrix/components".$componentRelativePath."/component.php");

		if($bDrawIcons)
		{
			$arIcons = array();
			$arPanelParams = array();

			$arComponentDescription = CComponentUtil::GetComponentDescr($componentName);
			$bComponentAccess = ($USER->CanDoOperation('edit_php') || $bSrcFound && $USER->CanDoFileOperation('fm_lpa', array(SITE_ID, $sSrcFile)));

			$showMode = $APPLICATION->GetPublicShowMode();
			if($showMode == 'edit' || $showMode == 'configure')
				$arPanelParams["ICON"] = $showMode.'-icon';

			if($bComponentAccess && !$parentComponent && $bSrcFound && $showMode == "configure")
			{
				$url = $APPLICATION->GetPopupLink(
					array(
						'URL' => "/bitrix/admin/component_props.php?".
						"component_name=".urlencode(CUtil::addslashes($componentName)). //$rel_path
						"&component_template=".urlencode(CUtil::addslashes($componentTemplate)).
						"&template_id=".urlencode(CUtil::addslashes(SITE_TEMPLATE_ID)).
						"&lang=".urlencode(CUtil::addslashes(LANGUAGE_ID)).
						"&src_path=".urlencode(CUtil::addslashes($sSrcFile)).
						"&src_line=".$iSrcLine.
						"&src_page=".urlencode(CUtil::addslashes($APPLICATION->GetCurPage())).
						"&src_site=".urlencode(CUtil::addslashes(SITE_ID)),
						"PARAMS" => Array("min_width" => 450)
					)
				);
				$arIcons[] = array(
					'URL'=>'javascript:'.$url,
					'ICON'=>"parameters-2",
					'TITLE'=>GetMessage("main_incl_file_comp_param"),
					'DEFAULT'=>true,
				);

				//panel button
				$buttonID = "components";
				$APPLICATION->AddPanelButton(array(
					"ID"=>$buttonID,
					"ICON"=>"icon-components",
					"ALT"=>GetMessage("main_comp_button_title"),
					"TEXT"=>GetMessage("main_comp_button"),
					"MAIN_SORT"=>"800",
					"SORT"=>10,
					"MODE"=>array("configure"),
				));
				$aMenuItem =  array(
					"TEXT"=>$arComponentDescription["NAME"],
					"TITLE"=>GetMessage("main_comp_button_menu_title").' &quot;'.$componentName.'&quot;',
					"ICON"=>"parameters-2",
					"ACTION"=>$url,
				);
				$APPLICATION->AddPanelButtonMenu($buttonID, $aMenuItem);
			}

			if($bComponentAccess && $showMode == "configure")
			{
				$template = & $component->GetTemplate();
				if(is_null($template))
				{
					if($component->InitComponentTemplate())
						$template = & $component->GetTemplate();
				}

				if(!is_null($template))
				{
					$urlCopy = '';
					if($bSrcFound && $template->IsInTheme() == false)
					{
						//copy template dialog
						$urlCopy = "/bitrix/admin/template_copy.php?".
							"lang=".urlencode(CUtil::addslashes(LANGUAGE_ID)).
							"&component_name=".urlencode(CUtil::addslashes($componentName)).
							"&component_template=".urlencode(CUtil::addslashes($componentTemplate)).
							"&template_id=".urlencode(CUtil::addslashes(SITE_TEMPLATE_ID)).
							"&template_site_template=".urlencode(CUtil::addslashes($template->GetSiteTemplate())).
							"&src_path=".urlencode(CUtil::addslashes($sSrcFile)).
							"&src_line=".$iSrcLine.
							"&src_site=".urlencode(CUtil::addslashes(SITE_ID)).
							"&edit_file=".urlencode($template->GetPageName()).
							"&back_path=".urlencode($_SERVER["REQUEST_URI"]);
						$arIcons[] = array(
							'URL'=>'javascript:'.$APPLICATION->GetPopupLink(
									array(
										'URL' => $urlCopy,
										"PARAMS" => Array("min_width" => 450)
									)
								),
							'ICON'=>"copy-2",
							'TITLE'=>GetMessage("main_comp_copy_templ")
						);
					}
					if(strlen($template->GetSiteTemplate()) > 0)
					{
						//edit template copied to site template
						$arIcons[] = array(
							'URL' => 'javascript:'.$APPLICATION->GetPopupLink(array(
									'URL' => "/bitrix/admin/public_file_edit_src.php?site=".SITE_ID."&".'path='.urlencode($template->GetFile())."&back_url=".urlencode($_SERVER["REQUEST_URI"])."&lang=".LANGUAGE_ID,
									'PARAMS' => array(
										'width' => 770,
										'height' => 570,
										'resize' => true
									)
								)
							),
							'ICON' => 'edit-2',
							'TITLE' => GetMessage("main_comp_edit_templ")
						);
						if(StrLen($template->GetFolder()) > 0)
						{
							if(file_exists($_SERVER["DOCUMENT_ROOT"].$template->GetFolder()."/style.css"))
							{
								//edit template CSS copied to site template
								$arIcons[] = array(
									'URL' => 'javascript:'.$APPLICATION->GetPopupLink(array(
											'URL' => "/bitrix/admin/public_file_edit_src.php?site=".SITE_ID."&".'path='.urlencode($template->GetFolder()."/style.css")."&back_url=".urlencode($_SERVER["REQUEST_URI"])."&lang=".LANGUAGE_ID,
											'PARAMS' => array(
												'width' => 770,
												'height' => 570,
												'resize' => true
											)
										)
									),
									'ICON' => 'edit-css',
									'TITLE' => GetMessage("main_comp_edit_css")
								);
							}
						}
					}
					elseif($urlCopy <> '')
					{
						//copy template for future editing
						$urlCopy .= '&system_template=Y';
						$arIcons[] = array(
							'URL'=>'javascript:'.$APPLICATION->GetPopupLink(
									array(
										'URL' => $urlCopy,
										"PARAMS" => Array("min_width" => 450)
									)
								),
							'ICON'=>"edit-2",
							'TITLE'=>GetMessage("main_comp_edit_templ"),
							'ALT'=>GetMessage("main_comp_copy_title"),
						);
					}
				}
			}

			$aAddIcons = array();
			if($arComponentDescription && is_array($arComponentDescription))
			{
				//component bar tooltip
				$arPanelParams['TOOLTIP'] = '<b>'.$arComponentDescription["NAME"].'</b><br>'.
					'('.GetMessage('main_incl_comp_component').' '.$componentName.')'.
					(isset($arComponentDescription["DESCRIPTION"]) && $arComponentDescription["DESCRIPTION"] <> ""? '<br>'.$arComponentDescription["DESCRIPTION"]:'');

				//component bar icon
				if($showMode == 'edit' && $arComponentDescription['ICON_EDIT'] <> '')
					$arPanelParams['ICON'] = $arComponentDescription['ICON_EDIT'];
				elseif($showMode == 'configure' && $arComponentDescription['ICON_CONFIGURE'] <> '')
					$arPanelParams['ICON'] = $arComponentDescription['ICON_CONFIGURE'];

				//clear cache
				if(array_key_exists("CACHE_PATH", $arComponentDescription) && $showMode == "configure" && $USER->CanDoOperation('cache_control'))
				{
					if(StrLen($arComponentDescription["CACHE_PATH"]) > 0)
					{
						$arIcons[] = array(
							"URL" => "javascript:jsPopup.ClearCache('component_name=".urlencode(CUtil::addslashes($componentName))."&site_id=".SITE_ID."');",
							"ICON" => "del-cache",
							"TITLE" => GetMessage("MAIN_BX_COMPONENT_CACHE_CLEAR")
						);
						$this->aCachedComponents[] = $componentName;
					}
				}

				//additional buttons from component description
				if(array_key_exists("AREA_BUTTONS", $arComponentDescription))
				{
					foreach($arComponentDescription["AREA_BUTTONS"] as $value)
					{
						if(array_key_exists("MODE", $value))
						{
							if(is_array($value["MODE"]))
							{
								if(!in_array($showMode, $value["MODE"]))
									continue;
							}
							elseif($value["MODE"] != $showMode)
								continue;
						}
						if (array_key_exists("SRC", $value))
							$value["SRC"] = "/bitrix/components".$componentRelativePath.$value["SRC"];
						$aAddIcons[] = $value;
					}
				}
			}

			if(!empty($arIcons) && !empty($aAddIcons))
				$arIcons[] = array("SEPARATOR"=>true);
			$arIcons = array_merge($arIcons, $aAddIcons);

			$aAddIcons = $component->GetIncludeAreaIcons();
			foreach($aAddIcons as $key=>$value)
			{
				if(array_key_exists("MODE", $value))
				{
					if(is_array($value["MODE"]))
					{
						if(!in_array($showMode, $value["MODE"]))
							unset($aAddIcons[$key]);
					}
					elseif($value["MODE"] != $showMode)
						unset($aAddIcons[$key]);
				}
			}

			if(!empty($arIcons) && !empty($aAddIcons))
				$arIcons[] = array("SEPARATOR"=>true);
			$arIcons = array_merge($arIcons, $aAddIcons);

			echo $this->IncludeStringAfter($arIcons, $arPanelParams);
		}

		return $result;
	}

	function OnChangeFileComponent($path, $site)
	{
		require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/php_parser.php");

		global $APPLICATION;

		$docRoot = CSite::GetSiteDocRoot($site);

		CUrlRewriter::Delete(
			array("SITE_ID" => $site, "PATH" => $path, "ID" => "NULL")
		);

		$fileSrc = $APPLICATION->GetFileContent($docRoot.$path);
		$arComponents = PHPParser::ParseScript($fileSrc);
		for ($i = 0, $cnt = count($arComponents); $i < $cnt; $i++)
		{
			if (isset($arComponents[$i]["DATA"]["PARAMS"]) && is_array($arComponents[$i]["DATA"]["PARAMS"]))
			{
				if (array_key_exists("SEF_MODE", $arComponents[$i]["DATA"]["PARAMS"])
					&& $arComponents[$i]["DATA"]["PARAMS"]["SEF_MODE"] == "Y")
				{
					CUrlRewriter::Add(
						array(
							"SITE_ID" => $site,
							"CONDITION" => "#^".$arComponents[$i]["DATA"]["PARAMS"]["SEF_FOLDER"]."#",
							"ID" => $arComponents[$i]["DATA"]["COMPONENT_NAME"],
							"PATH" => $path
						)
					);
				}
			}
		}
	}
	// <<<<< COMPONENTS 2.0
	// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

	// $arParams - не переименовывать !
	function IncludeFile($rel_path, $arParams = Array(), $arFunctionParams = Array())
	{
		global $APPLICATION, $USER, $DB, $MESS, $DOCUMENT_ROOT;

		if($_SESSION["SESS_SHOW_INCLUDE_TIME_EXEC"]=="Y" && ($USER->CanDoOperation('edit_php') || $_SESSION["SHOW_SQL_STAT"]=="Y"))
		{
			$debug = new CDebugInfo();
			$debug->Start();
		}

		$sType = "TEMPLATE";
		$bComponent = false;
		if(substr($rel_path, 0, 1)!="/")
		{
			$bComponent = true;
			$path = BX_PERSONAL_ROOT."/templates/".SITE_TEMPLATE_ID."/".$rel_path;
			if(!file_exists($_SERVER["DOCUMENT_ROOT"].$path))
			{
				$sType = "DEFAULT";
				$path = BX_PERSONAL_ROOT."/templates/.default/".$rel_path;
				if(!file_exists($_SERVER["DOCUMENT_ROOT"].$path))
				{
					$path = BX_PERSONAL_ROOT."/templates/".SITE_TEMPLATE_ID."/".$rel_path;
					$module_id = substr($rel_path, 0, strpos($rel_path, "/"));
					if(strlen($module_id)>0)
					{
						$path = "/bitrix/modules/".$module_id."/install/templates/".$rel_path;
						$sType = "MODULE";
						if(!file_exists($_SERVER["DOCUMENT_ROOT"].$path))
						{
							$sType = "TEMPLATE";
							$path = BX_PERSONAL_ROOT."/templates/".SITE_TEMPLATE_ID."/".$rel_path;
						}
					}
				}
			}
		}
		else
			$path = $rel_path;

		if($arFunctionParams["WORKFLOW"] && !IsModuleInstalled("workflow"))
			$arFunctionParams["WORKFLOW"] = false;
		elseif($sType!="TEMPLATE" && $arFunctionParams["WORKFLOW"])
			$arFunctionParams["WORKFLOW"] = false;

		$bDrawIcons =
			(
				($arFunctionParams["SHOW_BORDER"] !== false)
				&& $APPLICATION->GetShowIncludeAreas()
				&& (($FM_RIGHT = $APPLICATION->GetGroupRight("fileman"))=="W" || $FM_RIGHT=="F") /*WRITE && FULL ACCESS*/
				&& ($APPLICATION->GetFileAccessPermission($path)>="W"
				|| ($arFunctionParams["WORKFLOW"] && $APPLICATION->GetFileAccessPermission($path)>="U")
				)
			);

		if($bDrawIcons)
		{
			$path_url = "path=".$path;

			if (!in_array($arFunctionParams['MODE'], array('html', 'text', 'php')))
			{
				$arFunctionParams['MODE'] = $bComponent ? 'php' : 'html';
			}

			if ($sType != 'TEMPLATE')
			{
				switch ($arFunctionParams['MODE'])
				{
					case 'html':
						$editor = "/bitrix/admin/fileman_html_edit.php?site=".SITE_ID."&";
					break;
					case 'text':
						$editor = "/bitrix/admin/fileman_file_edit.php?site=".SITE_ID."&";
					break;
					case 'php':
						$editor = "/bitrix/admin/fileman_file_edit.php?full_src=Y&site=".SITE_ID."&";
					break;
				}
			}
			else
			{
				switch ($arFunctionParams['MODE'])
				{
					case 'html':
						$editor = '/bitrix/admin/public_file_edit.php?from=includefile&';
						$resize = 'false';
					break;

					case 'text':
						$editor = '/bitrix/admin/public_file_edit.php?from=includefile&noeditor=Y&';
						$resize = 'true';
					break;

					case 'php':
						$editor = '/bitrix/admin/public_file_edit_src.php?from=includefile&';
						$resize = 'true';
					break;
				}
			}

			if($arFunctionParams["TEMPLATE"])
				$arFunctionParams["TEMPLATE"] = "&template=".urlencode($arFunctionParams["TEMPLATE"]);

			if($arFunctionParams["BACK_URL"])
				$arFunctionParams["BACK_URL"] = "&back_url=".urlencode($arFunctionParams["BACK_URL"]);
			else
				$arFunctionParams["BACK_URL"] = "&back_url=".urlencode($_SERVER["REQUEST_URI"]);

			if($arFunctionParams["LANG"])
				$arFunctionParams["LANG"] = "&lang=".urlencode($arFunctionParams["LANG"]);
			else
				$arFunctionParams["LANG"] = "&lang=".LANGUAGE_ID;

			$arIcons = array();
			$arPanelParams = array();

			$bDefaultExists = false;
			if($USER->CanDoOperation('edit_php') && $bComponent && function_exists("debug_backtrace"))
			{
				$bDefaultExists = true;
				$arPanelParams["TOOLTIP"] = '<b>'.GetMessage("main_incl_component1").'</b><br>'.$rel_path;

				$aTrace = debug_backtrace();

				$sSrcFile = $aTrace[0]["file"];
				$iSrcLine = intval($aTrace[0]["line"]);
				$arIcons[] = array(
					'URL'=>"javascript:jsPopup.ShowDialog('/bitrix/admin/component_props.php?".
						"path=".urlencode(CUtil::addslashes($rel_path)).
						"&template_id=".urlencode(CUtil::addslashes(SITE_TEMPLATE_ID)).
						"&lang=".LANGUAGE_ID.
						"&src_path=".urlencode(CUtil::addslashes($sSrcFile)).
						"&src_line=".$iSrcLine.
						"');",
					'ICON'=>"parameters",
					'TITLE'=>GetMessage("main_incl_file_comp_param"),
					'DEFAULT'=>true
				);
			}

			if($sType == "MODULE")
			{
				$arIcons[] = Array(
					'URL'=>'javascript:if(confirm(\''.GetMessage("MAIN_INC_BLOCK_MODULE").'\'))window.location=\''.$editor.'&path='.urlencode(BX_PERSONAL_ROOT.'/templates/'.SITE_TEMPLATE_ID.'/'.$rel_path).$arFunctionParams["BACK_URL"].$arFunctionParams["LANG"].'&template='.$path.'\';',
					'ICON'=>'copy',
					'TITLE'=>str_replace("#MODE#", $arFunctionParams["MODE"], str_replace("#BLOCK_TYPE#", (!is_set($arFunctionParams, "NAME")? GetMessage("MAIN__INC_BLOCK"): $arFunctionParams["NAME"]), GetMessage("main_incl_file_edit_copy")))
				);
			}
			elseif($sType == "DEFAULT")
			{
				$arIcons[] = Array(
					'URL'=>'javascript:if(confirm(\''.GetMessage("MAIN_INC_BLOCK_COMMON").'\'))window.location=\''.$editor.$path_url.$arFunctionParams["BACK_URL"].$arFunctionParams["LANG"].$arFunctionParams["TEMPLATE"].'\';',
					'ICON'=>'edit-common',
					'TITLE'=>str_replace("#MODE#", $arFunctionParams["MODE"], str_replace("#BLOCK_TYPE#", (!is_set($arFunctionParams, "NAME")? GetMessage("MAIN__INC_BLOCK"): $arFunctionParams["NAME"]), GetMessage("MAIN_INC_BLOCK_EDIT")))
				);

				$arIcons[] = Array(
					'URL'=>$editor.'&path='.urlencode(BX_PERSONAL_ROOT.'/templates/'.SITE_TEMPLATE_ID.'/'.$rel_path).$arFunctionParams["BACK_URL"].$arFunctionParams["LANG"].'&template='.$path,
					'ICON'=>'copy',
					'TITLE'=>str_replace("#MODE#", $arFunctionParams["MODE"], str_replace("#BLOCK_TYPE#", (!is_set($arFunctionParams, "NAME")? GetMessage("MAIN__INC_BLOCK"): $arFunctionParams["NAME"]), GetMessage("MAIN_INC_BLOCK_COMMON_COPY")))
				);
			}
			else
			{
				$arPanelParams["TOOLTIP"] = '<b>'.GetMessage('main_incl_file').'</b><br>'.$path;

				$arIcons[] = Array(
					//'URL'=>'javascript:jsPopup.ShowDialog(\'/bitrix/admin/public_file_edit_src.php?from=includefile&\' + (jsUtils.IsEditor() ? \'\' : \'noeditor=Y&\') + \''.$path_url.$arFunctionParams["BACK_URL"].$arFunctionParams["LANG"].$arFunctionParams["TEMPLATE"].'\', {width: 800, height: 570, resize: false})',
					'URL' => 'javascript:'.$APPLICATION->GetPopupLink(
						array(
							'URL' => $editor.$path_url.$arFunctionParams["BACK_URL"].$arFunctionParams["LANG"].$arFunctionParams["TEMPLATE"],
							"PARAMS" => array(
								'width' => 770,
								'height' => 570,
								'resize' => $resize
							)
						)
					),
					//'URL'=>'javascript:jsPopup.ShowDialog(\''.$editor.$path_url.$arFunctionParams["BACK_URL"].$arFunctionParams["LANG"].$arFunctionParams["TEMPLATE"].'\', {width: 770, height: 570, resize: '.$resize.'})',
					'ICON'=>'edit',
					'TITLE'=>str_replace("#MODE#", $arFunctionParams["MODE"], str_replace("#BLOCK_TYPE#", (!is_set($arFunctionParams, "NAME")? GetMessage("MAIN__INC_BLOCK") : $arFunctionParams["NAME"]), GetMessage("MAIN_INC_ED"))),
					'DEFAULT'=>!$bDefaultExists
				);

				if($arFunctionParams["WORKFLOW"])
				{
					$arIcons[] = Array(
						'URL'=>'/bitrix/admin/workflow_edit.php?'.$arFunctionParams["LANG"].'&fname='.urlencode($path).$arFunctionParams["TEMPLATE"].$arFunctionParams["BACK_URL"],
						'ICON'=>'edit-wf',
						'TITLE'=>str_replace("#BLOCK_TYPE#", (!is_set($arFunctionParams, "NAME")? GetMessage("MAIN__INC_BLOCK"): $arFunctionParams["NAME"]), GetMessage("MAIN_INC_ED_WF"))
					);
				}
			}

			echo $this->IncludeStringBefore();
		}

		$res = null;
		if(is_file($_SERVER["DOCUMENT_ROOT"].$path))
		{
			if(is_array($arParams))
				extract($arParams);

			$res = include($_SERVER["DOCUMENT_ROOT"].$path);
		}

		if($_SESSION["SESS_SHOW_INCLUDE_TIME_EXEC"]=="Y" && ($USER->CanDoOperation('edit_php') || $_SESSION["SHOW_SQL_STAT"]=="Y"))
			echo $debug->Output($rel_path, $path);

		if($bDrawIcons)
			echo $this->IncludeStringAfter($arIcons, $arPanelParams);

		return $res;
	}

	function AddChainItem($title, $link="", $bUnQuote=true)
	{
		if($bUnQuote)
			$title = str_replace(array("&amp;", "&quot;", "&#039;", "&lt;", "&gt;"), array("&", "\"", "'", "<", ">"), $title);
		$this->arAdditionalChain[] = array("TITLE"=>$title, "LINK"=>htmlspecialchars($link));
	}

	function GetNavChain($path=false, $iNumFrom=0, $sNavChainPath=false, $bIncludeOnce=false, $bShowIcons = true)
	{
		global $APPLICATION;
		if($APPLICATION->GetProperty("NOT_SHOW_NAV_CHAIN")=="Y")
			return "";

		CMain::InitPathVars($site, $path);
		$DOC_ROOT = CSite::GetSiteDocRoot($site);

		if($path===false)
			$path = $this->GetCurDir();

		$arChain = Array();
		$strChainTemplate = $DOC_ROOT.BX_PERSONAL_ROOT."/templates/".SITE_TEMPLATE_ID."/chain_template.php";
		if(!file_exists($strChainTemplate))
			$strChainTemplate = $DOC_ROOT.BX_PERSONAL_ROOT."/templates/.default/chain_template.php";
		$i = -1;

		while(true)//будем до корня искать
		{
			//отрежем / в конце
			$path = rtrim($path, "/");

			$chain_file_name = $DOC_ROOT.$path."/.section.php";
			$section_template_init = false;
			if(file_exists($chain_file_name))
			{
				$sChainTemplate = "";
				$sSectionName = "";
				include($chain_file_name);
				if(strlen($sSectionName)>0)
					$arChain[] = Array("TITLE"=>$sSectionName, "LINK"=>$path."/");
				if(strlen($sChainTemplate)>0 && !$section_template_init)
				{
					$section_template_init = true;
					$strChainTemplate = $sChainTemplate;
				}
			}

			if(strlen($path)<=0)
				break;

			//найдем имя файла или папки
			$pos = bxstrrpos($path, "/");
			if($pos===false)
				break;

			//найдем папку-родителя
			$path = substr($path, 0, $pos+1);
		}

		if($sNavChainPath!==false)
			$strChainTemplate = $DOC_ROOT.$sNavChainPath;

		$arChain = array_reverse($arChain);
		$arChain = array_merge($arChain, $this->arAdditionalChain);
		if($iNumFrom>0)
			$arChain = array_slice($arChain, $iNumFrom);

		return $this->_mkchain($arChain, $strChainTemplate, $bIncludeOnce, $bShowIcons);
	}

	function _mkchain($arChain, $strChainTemplate, $bIncludeOnce=false, $bShowIcons = true)
	{
		$strChain = $sChainProlog = $sChainEpilog = "";
		if(file_exists($strChainTemplate))
		{
			$ITEM_COUNT = count($arChain);
			$arCHAIN = $arChain;
			$arCHAIN_LINK = &$arChain;
			$arResult = &$arChain; // for component 2.0
			if($bIncludeOnce)
			{
				$strChain = include($strChainTemplate);
			}
			else
			{
				for($i=0; $i<count($arChain); $i++)
				{
					$ITEM_INDEX = $i;
					$TITLE = $arChain[$i]["TITLE"];
					$LINK = $arChain[$i]["LINK"];
					$sChainBody = "";
					include($strChainTemplate);
					$strChain .= $sChainBody;
					if($i==0)
						$strChain = $sChainProlog . $strChain;
				}
				if(count($arChain)>0)
					$strChain .= $sChainEpilog;
			}
		}

		global $APPLICATION, $USER;
		if($APPLICATION->GetShowIncludeAreas() && $USER->CanDoOperation('edit_php') && $bShowIcons)
		{
			$site = CSite::GetSiteByFullPath($strChainTemplate);
			$DOC_ROOT = CSite::GetSiteDocRoot($site);

			if(strpos($strChainTemplate, $DOC_ROOT)===0)
			{
				$path = substr($strChainTemplate, strlen($DOC_ROOT));

				$templ_perm = $APPLICATION->GetFileAccessPermission($path);
				if((!defined("ADMIN_SECTION") || ADMIN_SECTION!==true) && $templ_perm>="W")
				{
					$arIcons = Array();
					$arIcons[] = Array(
						"URL"=>"/bitrix/admin/fileman_file_edit.php?lang=".LANGUAGE_ID."&site=".$site."&back_url=".urlencode($_SERVER["REQUEST_URI"])."&full_src=Y&path=".urlencode($path),
						"ICON"=>"nav-template",
						"TITLE"=>GetMessage("MAIN_INC_ED_NAV")
					);

					$strChain = $APPLICATION->IncludeString($strChain, $arIcons);
				}
			}
		}
		return $strChain;
	}

	function ShowNavChain($path=false, $iNumFrom=0, $sNavChainPath=false)
	{
		$this->AddBufferContent(Array(&$this, "GetNavChain"), $path, $iNumFrom, $sNavChainPath);
	}

	function ShowNavChainEx($path=false, $iNumFrom=0, $sNavChainPath=false)
	{
		$this->AddBufferContent(Array(&$this, "GetNavChain"), $path, $iNumFrom, $sNavChainPath, true);
	}

	/*****************************************************/

	function SetFileAccessPermission($path, $arPermissions, $bOverWrite=true)
	{
		CMain::InitPathVars($site, $path);
		$DOC_ROOT = CSite::GetSiteDocRoot($site);

		$path = rtrim($path, "/");

		if(strlen($path) <= 0)
			$path="/";
		if(($p = bxstrrpos($path, "/"))!==false)
		{
			$path_file = substr($path, $p+1);
			$path_dir = substr($path, 0, $p);
		}
		else
			return false;

		if($path_file=="" && $path_dir=="")
			$path_file = "/";

		$PERM = Array();
		if(file_exists($DOC_ROOT.$path_dir."/.access.php"))
			@include($DOC_ROOT.$path_dir."/.access.php");

		$FILE_PERM = $PERM[$path_file];
		if(!is_array($FILE_PERM))
			$FILE_PERM=Array();

		if(!$bOverWrite && count($FILE_PERM)>0)
			return true;

		$bDiff = false;

		$str="<?\n";
		foreach($arPermissions as $group=>$perm)
		{
			if(strlen($perm) > 0)
				$str.="\$PERM[\"".$path_file."\"][\"".$group."\"]=\"".str_replace("\"", "\\\"", $perm)."\";\n";
			if(!$bDiff && $FILE_PERM[$group]!=$perm)
				$bDiff=true;
		}

		foreach($PERM as $file=>$arPerm)
		{
			if(strval($file) !==$path_file)
				foreach($arPerm as $group=>$perm)
					$str.="\$PERM[\"".$file."\"][\"".$group."\"]=\"".str_replace("\"", "\\\"", $perm)."\";\n";
		}

		if(!$bDiff)
		{
			foreach($FILE_PERM as $group=>$perm)
				if($arPermissions[$group]!=$perm)
				{
					$bDiff = true;
					break;
				}
		}

		$str.="?".">";

		$this->SaveFileContent($DOC_ROOT.$path_dir."/.access.php", $str);
		$GLOBALS["CACHE_MANAGER"]->CleanDir("menu");
		unset($this->FILE_PERMISSION_CACHE[$site."|".$path_dir."/.access.php"]);

		if($bDiff)
		{
			$db_events = GetModuleEvents("main", "OnChangePermissions");
			while($arEvent = $db_events->Fetch())
				ExecuteModuleEvent($arEvent, Array($site, $path), $arPermissions, $FILE_PERM);
		}
		return true;
	}


	function RemoveFileAccessPermission($path, $arGroups=false)
	{
		CMain::InitPathVars($site, $path);
		$DOC_ROOT = CSite::GetSiteDocRoot($site);

		if(($p = bxstrrpos($path, "/"))!==false)
		{
			$path_file = substr($path, $p+1);
			$path_dir = substr($path, 0, $p);
		}
		else
			return false;

		$PERM = Array();
		if(!file_exists($DOC_ROOT.$path_dir."/.access.php"))
			return true;

		@include($DOC_ROOT.$path_dir."/.access.php");

		$FILE_PERM = $PERM[$path_file];

		$str="<?\n";
		foreach($PERM as $file=>$arPerm)
			if($file!=$path_file || $arGroups!==false)
				foreach($arPerm as $group=>$perm)
					if($file!=$path_file || (!in_array($group, $arGroups)))
						$str.="\$PERM[\"".$file."\"][\"".$group."\"]=\"".str_replace("\"", "\\\"", $perm)."\";\n";

		$str.="?".">";

		$this->SaveFileContent($DOC_ROOT.$path_dir."/.access.php", $str);
		$GLOBALS["CACHE_MANAGER"]->CleanDir("menu");
		unset($this->FILE_PERMISSION_CACHE[$site."|".$path_dir."/.access.php"]);

		$db_events = GetModuleEvents("main", "OnChangePermissions");
		while($arEvent = $db_events->Fetch())
			ExecuteModuleEvent($arEvent, Array($site, $path), Array());

		return true;
	}


	function CopyFileAccessPermission($path_from, $path_to, $bOverWrite=false)
	{
		CMain::InitPathVars($site_from, $path_from);
		$DOC_ROOT_FROM = CSite::GetSiteDocRoot($site_from);

		CMain::InitPathVars($site_to, $path_to);
		$DOC_ROOT_TO = CSite::GetSiteDocRoot($site_to);

		//выберем вышележащие .access.php
		if(($p = bxstrrpos($path_from, "/"))!==false)
		{
			$path_from_file = substr($path_from, $p+1);
			$path_from_dir = substr($path_from, 0, $p);
		}
		else
			return false;

		if(!file_exists($DOC_ROOT_FROM.$path_from_dir."/.access.php"))
			return true;

		$PERM = array();
		@include($DOC_ROOT_FROM.$path_from_dir."/.access.php");
		$FILE_PERM = $PERM[$path_from_file];
		if(count($FILE_PERM)>0)
			return $this->SetFileAccessPermission(Array($site_to, $path_to), $FILE_PERM, $bOverWrite);

		return true;
	}


	function GetFileAccessPermission($path, $groups = false, $task_mode = false) // task_mode - new access mode
	{
		global $USER;
		if ($groups===false)
		{
			if (!is_object($USER))
				$groups = Array(2);
			else
				$groups = $USER->GetUserGroupArray();
		}

		CMain::InitPathVars($site, $path);
		$DOC_ROOT = CSite::GetSiteDocRoot($site);
		$add_tasks = Array();

		if (trim($path, "/") != "")
		{
			$path = Rel2Abs("/", $path);
			if ($path == "")
				return (!$task_mode) ? 'D' : Array(CTask::GetIdByLetter('D', 'main', 'file'));
		}

		if(COption::GetOptionString("main", "controller_member", "N") == "Y" && COption::GetOptionString("main", "~controller_limited_admin", "N") == "Y")
		{
			$bAdminM = (is_object($USER)) ? $USER->IsAdmin() : false;
		}
		else
			$bAdminM = in_array("1", $groups);

		if(substr($path, -12)=="/.access.php" && !$bAdminM)
			return (!$task_mode) ? 'D' : Array(CTask::GetIdByLetter('D', 'main', 'file'));

		if($bAdminM)
			return (!$task_mode) ? 'X' : Array(CTask::GetIdByLetter('X', 'main', 'file'));

		if(substr($path, -10)=="/.htaccess" && !$task_mode)
			return (!$task_mode) ? 'D' : Array(CTask::GetIdByLetter('D', 'main', 'file'));

		$max_perm = "D";
		$arGroupTask = Array();

		//к списку групп добавим * === "любая группа"
		$groups[] = "*";
		while(true)//будем до корня искать
		{
			$path = rtrim($path, "\0");
			//отрежем / в конце
			$path = rtrim($path, "/");

			if(strlen($path)<=0)
			{
				$access_file_name="/.access.php";
				$Dir = "/";
			}
			else
			{
				//найдем имя файла или папки
				$pos = strrpos($path, "/");
				if($pos===false)
					break;
				$Dir = substr($path, $pos+1);

				//security fix: under Windows "my." == "my"
				$Dir = rtrim($Dir, "\0.\\/+ ");

				//найдем папку-родителя
				$path = substr($path, 0, $pos+1);

				$access_file_name=$path.".access.php";
			}

			if(array_key_exists($site."|".$access_file_name, $this->FILE_PERMISSION_CACHE))
				$PERM = $this->FILE_PERMISSION_CACHE[$site."|".$access_file_name];
			else
			{
				$PERM = Array();
				//подключим файл с правами если он есть
				if(file_exists($DOC_ROOT.$access_file_name))
					include($DOC_ROOT.$access_file_name);
				$this->FILE_PERMISSION_CACHE[$site."|".$access_file_name] = $PERM;
			}

			//проверим - заданы ли права на этот файл/папку для данных групп в этом файле
			$dir_perm = $PERM[$Dir];

			if(is_array($dir_perm))
			{
				foreach($groups as $key => $group_id)
				{
					$perm = $dir_perm[$group_id];
					if(isset($perm))
					{
						if ($task_mode)
						{
							if (substr($perm, 0, 2) == 'T_')
								$tid = intval(substr($perm, 2));
							elseif(($tid = CTask::GetIdByLetter($perm, 'main', 'file')) === false)
								continue;

							$arGroupTask[$group_id] = $tid;
						}
						else
						{
							if (substr($perm, 0, 2) == 'T_')
							{
								$tid = intval(substr($perm, 2));
								$perm = CTask::GetLetter($tid);
								if (strlen($perm) == 0)
									$perm = 'D';
							}

							if($max_perm=="" || $perm>$max_perm)
							{
								$max_perm = $perm;
								if($perm =="W")
									break 2;
							}
						}

						if($group_id == "*")
							break 2;

						//удалим эту группу из списка, т.к. мы уже нашли для нее права
						unset($groups[$key]);

						if(count($groups) == 1 && in_array("*", $groups))
							break 2;
					}
				}

				if(count($groups)<=1)
					break;
			}

			if(strlen($path)<=0)
				break;
		}

		if ($task_mode)
		{
			$arTasks = array_unique(array_values($arGroupTask));
			if (empty($arTasks))
				return Array(CTask::GetIdByLetter('D', 'main', 'file'));
			sort($arTasks);
			return $arTasks;
		}
		else
			return $max_perm;
	}


	/***********************************************/

	function SaveFileContent($abs_path, $strContent)
	{
		$aMsg = array();
		$file = array();
		$this->ResetException();
		CheckDirPath($abs_path);

		if(file_exists($abs_path))
		{
			$file["exists"] = true;
			if (!is_writable($abs_path))
				@chmod($abs_path, BX_FILE_PERMISSIONS);
			$file["size"] = intVal(filesize($abs_path));
		}

		/****************************** QUOTA ******************************/
		if (COption::GetOptionInt("main", "disk_space") > 0)
		{
			$quota = new CDiskQuota();
			if (false === $quota->checkDiskQuota(array("FILE_SIZE" => intVal(strLen($strContent) - intVal($file["size"])))))
			{
				$this->ThrowException($quota->LAST_ERROR, "BAD_QUOTA");
				return false;
			}
		}
		/****************************** QUOTA ******************************/
		$fd = @fopen($abs_path, "wb");
		if(!$fd)
		{
			if ($file["exists"])
				$this->ThrowException(GetMessage("MAIN_FILE_NOT_CREATE"), "FILE_NOT_CREATE");
			else
				$this->ThrowException(GetMessage("MAIN_FILE_NOT_OPENED"), "FILE_NOT_OPEN");
			return false;
		}

		if(false === fwrite($fd, $strContent))
		{
			fclose($fd);
			$this->ThrowException(GetMessage("MAIN_FILE_NOT_WRITE"), "FILE_NOT_WRITE");
			return false;
		}

		fclose($fd);
		@chmod($abs_path, BX_FILE_PERMISSIONS);

		$site = CSite::GetSiteByFullPath($abs_path);
		$DOC_ROOT = CSite::GetSiteDocRoot($site);
		if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
		{
			//Fix for name case under Windows
			$abs_path = strtolower($abs_path);
			$DOC_ROOT = strtolower($DOC_ROOT);
		}

		if(strpos($abs_path, $DOC_ROOT)===0 && $site!==false)
		{
			$DOC_ROOT = rtrim($DOC_ROOT, "/\\");
			$path = "/".ltrim(substr($abs_path, strlen($DOC_ROOT)), "/\\");

			$db_events = GetModuleEvents("main", "OnChangeFile");
			while($arEvent = $db_events->Fetch())
				ExecuteModuleEvent($arEvent, $path, $site);
		}
		/****************************** QUOTA ******************************/
		if (COption::GetOptionInt("main", "disk_space") > 0)
		{
			CDiskQuota::updateDiskQuota("files", intVal(filesize($abs_path) - intVal($file["size"])), "update");
		}
		/****************************** QUOTA ******************************/
		return true;
	}

	function GetFileContent($path)
	{
		clearstatcache();
		if(!file_exists($path) || !is_file($path))
			return false;
		if(filesize($path)<=0)
			return "";
		$fd = fopen($path, "rb");
		$contents = fread ($fd, filesize($path));
		fclose ($fd);
		return $contents;
	}

	function GetLangSwitcherArray()
	{
		return CMain::GetSiteSwitcherArray();
	}

	function GetSiteSwitcherArray()
	{
		global $DB, $REQUEST_URI, $DOCUMENT_ROOT;

		$cur_dir = $this->GetCurDir();
		$cur_page = $this->GetCurPage();
		$bAdmin = (substr($cur_dir, 0, strlen(BX_ROOT."/admin/")) == BX_ROOT."/admin/"); //если раздел администрирования

		$db_res = CSite::GetList($by, $order, array("ACTIVE"=>"Y","ID"=>LANG));
		if(($ar = $db_res->Fetch()) && strpos($cur_page, $ar["DIR"])===0)
		{
			$path_without_lang = substr($cur_page, strlen($ar["DIR"])-1);
			$path_without_lang = LTrim($path_without_lang, "/");
			$path_without_lang_tmp = RTrim($path_without_lang, "/");
		}

		$result = Array();
		$db_res = CSite::GetList($by="SORT", $order="ASC", Array("ACTIVE"=>"Y"));
		while($ar = $db_res->Fetch())
		{
			$ar["NAME"] = htmlspecialchars($ar["NAME"]);
			$ar["SELECTED"] = ($ar["LID"]==LANG);

			if($bAdmin)
			{
				global $QUERY_STRING;
				$p = ereg_replace("lang=[^&]*&*", "", $QUERY_STRING);
				$ar["PATH"] = $this->GetCurPage()."?lang=".$ar["LID"]."&".$p;
			}
			else
			{
				$ar["PATH"] = "";

				if(strlen($path_without_lang)>1 && file_exists($ar["ABS_DOC_ROOT"]."/".$ar["DIR"]."/".$path_without_lang_tmp))
					$ar["PATH"] = $ar["DIR"].$path_without_lang;

				if(strlen($ar["PATH"])<=0)
					$ar["PATH"] = $ar["DIR"];

				if($ar["ABS_DOC_ROOT"]!==$_SERVER["DOCUMENT_ROOT"])
					$ar["FULL_URL"] = (CMain::IsHTTPS() ? "https://" : "http://").$ar["SERVER_NAME"].$ar["PATH"];
				else
					$ar["FULL_URL"] = $ar["PATH"];
			}

			$result[] = $ar;
		}
		return $result;
	}

	/*
	Возвращает массив ролей, задаваемых в настройках модуля
	W - роль с максимальными правами (администратор)
	D - минимальная роль (доступ закрыт)

	$module_id - идентификатор модуля
	$arGroups - массив ID групп, если не задан, то берется массив групп текущего пользователя
	$use_default_role - "Y" - при определении ролей использовать уровень "по умолчанию"
	$max_role_for_super_admin - "Y" - если в массиве групп пользователя присутствует группа с ID=1 то вернуть максимальную роль
	*/
	function GetUserRoles($module_id, $arGroups=false, $use_default_role="Y", $max_role_for_super_admin="Y")
	{
		global $DB, $USER, $MODULE_ROLES;
		$err_mess = (CAllMain::err_mess())."<br>Function: GetUserRoles<br>Line: ";
		$arRoles = array();
		$min_role = "D";
		$max_role = "W";
		if($arGroups===false)
		{
			if(is_object($USER)) $arGroups = $USER->GetUserGroupArray();
			if(!is_array($arGroups)) $arGroups[] = 2;
		}
		$key = $use_default_role."_".$max_role_for_super_admin;
		if(is_array($arGroups) && count($arGroups)>0)
		{
			$groups = implode(",",$arGroups);
			$key .= "_".$groups;
		}
		if(is_set($MODULE_ROLES[$module_id], $key))
			$arRoles = $MODULE_ROLES[$module_id][$key];
		else
		{
			if(is_array($arGroups) && count($arGroups)>0)
			{
				if(in_array(1,$arGroups) && $max_role_for_super_admin=="Y") $arRoles[] = $max_role;

				$strSql = "SELECT MG.G_ACCESS FROM b_group G ".
				"LEFT JOIN b_module_group MG ON (G.ID = MG.GROUP_ID AND MG.MODULE_ID = '".$DB->ForSql($module_id,50)."') ".
				"WHERE G.ID in (".$groups.") AND G.ACTIVE = 'Y'";

				/*$strSql = "
					SELECT
						MG.G_ACCESS
					FROM
						b_module_group MG
					INNER JOIN b_group G ON (MG.GROUP_ID = G.ID)
					WHERE
						MG.MODULE_ID = '".$DB->ForSql($module_id,50)."'
					and MG.GROUP_ID in ($groups)
					and G.ACTIVE = 'Y'
					";*/

				//echo "<pre>".$strSql."</pre>";
				$t = $DB->Query($strSql, false, $err_mess.__LINE__);

				if($use_default_role=="Y")
					$default_role = COption::GetOptionString($module_id, "GROUP_DEFAULT_RIGHT", $min_role);

				while ($tr = $t->Fetch())
				{
					if ($tr["G_ACCESS"] !== null)
					{
						$arRoles[] = $tr["G_ACCESS"];
					}
					else
					{
						if($use_default_role=="Y")
							$arRoles[] = $default_role;
					}
				}

			}
			//if($use_default_role=="Y")
			//{
			//	$arRoles[] = COption::GetOptionString($module_id, "GROUP_DEFAULT_RIGHT", $min_role);
			//}
			$arRoles = array_unique($arRoles);
			$MODULE_ROLES[$module_id][$key] = $arRoles;
		}
		return $arRoles;
	}

	/*
	Возвращает уровень права доступа к модулю, задаваемого в настройках модуля
	W - максимальный уровень доступа
	D - минимальный уровень доступа (доступ закрыт)

	$module_id - идентификатор модуля
	$arGroups - массив ID групп, если не задан, то берется массив групп текущего пользователя
	$use_default_level - "Y" - при определении права использовать уровень "по умолчанию"
	$max_right_for_super_admin - "Y" - если в массиве групп пользователя присутствует группа с ID=1 то вернуть максимальное право
	*/
	function GetUserRight($module_id, $arGroups=false, $use_default_level="Y", $max_right_for_super_admin="Y")
	{
		global $DB, $USER, $MODULE_PERMISSIONS;
		$err_mess = (CAllMain::err_mess())."<br>Function: GetUserRight<br>Line: ";
		$min_right = "D";
		$max_right = "W";
		$cur_admin = false;
		if($arGroups===false)
		{
			if(is_object($USER))
			{
				$arGroups = $USER->GetUserGroupArray();
				if($USER->IsAdmin())
					return $max_right;
			}
			if(!is_array($arGroups))
				$arGroups = array(2);
		}

		$key = $use_default_level."_".$max_right_for_super_admin;
		if(is_array($arGroups) && count($arGroups)>0)
		{
			$groups = implode(",", $arGroups);
			$key .= "_".$groups;
		}

		if(!is_array($MODULE_PERMISSIONS[$module_id]))
			$MODULE_PERMISSIONS[$module_id] = array();

		$right = "";
		if(is_set($MODULE_PERMISSIONS[$module_id], $key))
			$right = $MODULE_PERMISSIONS[$module_id][$key];
		else
		{
			if(is_array($arGroups) && count($arGroups)>0)
			{
				if(in_array(1, $arGroups) && $max_right_for_super_admin=="Y" && (COption::GetOptionString("main", "controller_member", "N") != "Y" || COption::GetOptionString("main", "~controller_limited_admin", "N") != "Y"))
					$right = $max_right;
				else
				{
					$strSql = "
						SELECT
							max(MG.G_ACCESS) G_ACCESS
						FROM
							b_module_group MG
						INNER JOIN b_group G ON (MG.GROUP_ID = G.ID)
						WHERE
							MG.MODULE_ID = '".$DB->ForSql($module_id,50)."'
						and MG.GROUP_ID in (".$groups.")
						and G.ACTIVE = 'Y'
						";
					//echo "<pre>".$strSql."</pre>";
					$t = $DB->Query($strSql, false, $err_mess.__LINE__);
					$tr = $t->Fetch();
					$right = $tr["G_ACCESS"];
				}
			}

			if($right == "" && $use_default_level=="Y")
				$right = COption::GetOptionString($module_id, "GROUP_DEFAULT_RIGHT", $min_right);

			if($right <> "")
			{
				if(!is_array($MODULE_PERMISSIONS[$module_id]))
					$MODULE_PERMISSIONS[$module_id] = array();
				$MODULE_PERMISSIONS[$module_id][$key] = $right;
			}
		}
		return $right;
	}

	function GetGroupRightList($arFilter)
	{
		global $DB;

		$strSqlWhere = "";
		if (array_key_exists("MODULE_ID", $arFilter))
			$strSqlWhere .= " AND MODULE_ID = '".$DB->ForSql($arFilter["MODULE_ID"])."' ";
		if (array_key_exists("GROUP_ID", $arFilter))
			$strSqlWhere .= " AND GROUP_ID = ".IntVal($arFilter["GROUP_ID"])." ";
		if (array_key_exists("G_ACCESS", $arFilter))
			$strSqlWhere .= " AND G_ACCESS = '".$DB->ForSql($arFilter["G_ACCESS"])."' ";

		$dbRes = $DB->Query(
			"SELECT ID, MODULE_ID, GROUP_ID, G_ACCESS ".
			"FROM b_module_group ".
			"WHERE 1 = 1 ".
			$strSqlWhere
		);

		return $dbRes;
	}

	function GetGroupRight($module_id, $arGroups=false, $use_default_level="Y", $max_right_for_super_admin="Y")
	{
		return CMain::GetUserRight($module_id, $arGroups, $use_default_level, $max_right_for_super_admin);
	}

	function SetGroupRight($module_id, $group_id, $right)
	{
		global $DB;
		$err_mess = (CAllMain::err_mess())."<br>Function: SetGroupRight<br>Line: ";
		$arFields = Array(
			"MODULE_ID"	=> "'".$DB->ForSql($module_id,50)."'",
			"GROUP_ID"	=> intval($group_id),
			"G_ACCESS"	=> "'".$DB->ForSql($right,255)."'"
			);
		$rows = $DB->Update("b_module_group",$arFields,"WHERE MODULE_ID='".$DB->ForSql($module_id,50)."' and GROUP_ID='".intval($group_id)."'",$err_mess.__LINE__);
		if(intval($rows)<=0)
		{
			$DB->Insert("b_module_group",$arFields, $err_mess.__LINE__);
		}
	}

	function DelGroupRight($module_id="", $arGroups=array())
	{
		global $DB;
		$err_mess = (CAllMain::err_mess())."<br>Function:  DelGroupRight<br>Line: ";
		if(strlen($module_id)>0)
		{
			if(is_array($arGroups) && count($arGroups)>0)
			{
				$strSql = "DELETE FROM b_module_group WHERE MODULE_ID='".$DB->ForSql($module_id,50)."' and GROUP_ID in (".implode(",",$arGroups).")";
			}
			else $strSql = "DELETE FROM b_module_group WHERE MODULE_ID='".$DB->ForSql($module_id,50)."'";
		}
		elseif(is_array($arGroups) && count($arGroups)>0)
		{
			$strSql = "DELETE FROM b_module_group WHERE GROUP_ID in (".implode(",",$arGroups).")";
		}

		if(strlen($strSql)>0)
			$DB->Query($strSql, false, $err_mess.__LINE__);
	}

	function GetMainRightList()
	{
		$arr = array(
			"reference_id" => array(
				"D",
				"P",
				"R",
				"T",
				"V",
				"W"),
			"reference" => array(
				"[D] ".GetMessage("OPTION_DENIED"),
				"[P] ".GetMessage("OPTION_PROFILE"),
				"[R] ".GetMessage("OPTION_READ"),
				"[T] ".GetMessage("OPTION_READ_PROFILE_WRITE"),
				"[V] ".GetMessage("OPTION_READ_OTHER_PROFILES_WRITE"),
				"[W] ".GetMessage("OPTION_WRITE"))
			);
		return $arr;
	}

	function GetDefaultRightList()
	{
		$arr = array(
			"reference_id" => array("D","R","W"),
			"reference" => array(
				"[D] ".GetMessage("OPTION_DENIED"),
				"[R] ".GetMessage("OPTION_READ"),
				"[W] ".GetMessage("OPTION_WRITE"))
			);
		return $arr;
	}

	function err_mess()
	{
		return "<br>Class: CAllMain<br>File: ".__FILE__;
	}

	/*
	Возвращает значение кука по заданному имени переменной

	$name			: имя кука (без префикса)
	$name_prefix	: префикс для имени кука (если не задан, то берется из настроек главного модуля)
	*/
	function get_cookie($name, $name_prefix=false)
	{
		global $HTTP_COOKIE_VARS, $_COOKIE;
		if($name_prefix===false)
			$name = COption::GetOptionString("main", "cookie_name", "BITRIX_SM")."_".$name;
		else
			$name = $name_prefix."_".$name;
		$value = $HTTP_COOKIE_VARS[$name];
		if(strlen($value)<=0)
			$value = $_COOKIE[$name];
		if(strlen($value)<=0)
		{
			global $$name;
			$value = $$name;
		}
		return $value;
	}

	/*
	Устанавливает кук и при необходимости запоминает параметры установленного кука в массиве для дальнейшего распостранения по доменам

	$name			: имя кука (без префикса)
	$value			: значение переменной
	$time			: дата после которой кук истекает
	$folder			: каталог действия кука
	$domain			: домен действия кука
	$secure			: флаг secure для кука (1 - secure)
	$spread			: Y - распостранить кук на все сайты и их домены
	$name_prefix	: префикс для имени кука (если не задан, то берется из настроек главного модуля)
	*/
	function set_cookie($name, $value, $time=false, $folder="/", $domain=false, $secure=false, $spread=true, $name_prefix=false)
	{
		if($time===false)
			$time = time()+60*60*24*30*12; // 30 суток * 12 ~ 1 год
		if($name_prefix===false)
			$name = COption::GetOptionString("main", "cookie_name", "BITRIX_SM")."_".$name;
		else
			$name = $name_prefix."_".$name;

		if($domain === false)
			$domain = $this->GetCookieDomain();

		if($spread==="Y" || $spread===true)
			$spread_mode = BX_SPREAD_DOMAIN | BX_SPREAD_SITES;
		elseif($spread>=1)
			$spread_mode = $spread;
		else
			$spread_mode = BX_SPREAD_DOMAIN;

		//echo "-$name<br>\r\n";

		//if(!headers_sent())
		if($spread_mode & BX_SPREAD_DOMAIN)
		{
			setcookie($name, $value, $time, $folder, $domain, $secure);
			//echo "BX_SPREAD_DOMAIN<br>\r\n";
		}

		if($spread_mode & BX_SPREAD_SITES)
		{
			$this->arrSPREAD_COOKIE[$name] = array("V" => $value, "T" => $time, "F" => $folder, "D" => $domain, "S" => $secure);
			//echo "BX_SPREAD_SITES<br>\r\n";
		}
	}

	function GetCookieDomain()
	{
		static $bCache = false;
		static $cache  = false;
		if($bCache)
			return $cache;

		global $DB;
		if(CACHED_b_lang_domain===false)
		{
			$strSql = "
				SELECT
					DOMAIN
				FROM
					b_lang_domain
				WHERE
					'".$DB->ForSql($_SERVER["HTTP_HOST"])."' like ".$DB->Concat("'%.'", "DOMAIN")."
				ORDER BY
					".$DB->Length("DOMAIN")."
				";
			$res = $DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
			if($ar = $res->Fetch())
			{
				$cache = $ar['DOMAIN'];
			}
		}
		else
		{
			global $CACHE_MANAGER;
			if($CACHE_MANAGER->Read(CACHED_b_lang_domain, "b_lang_domain", "b_lang_domain"))
			{
				$arLangDomain = $CACHE_MANAGER->Get("b_lang_domain");
			}
			else
			{
				$arLangDomain = array("DOMAIN"=>array(), "LID"=>array());
				$res = $DB->Query("SELECT * FROM b_lang_domain ORDER BY ".$DB->Length("DOMAIN"));
				while($ar = $res->Fetch())
				{
					$arLangDomain["DOMAIN"][]=$ar;
					$arLangDomain["LID"][$ar["LID"]][]=$ar;
				}
				$CACHE_MANAGER->Set("b_lang_domain", $arLangDomain);
			}
			//$strSql = "'".$DB->ForSql($_SERVER["HTTP_HOST"])."' like ".$DB->Concat("'%.'", "DOMAIN")."";
			foreach($arLangDomain["DOMAIN"] as $ar)
			{
				if(strcasecmp(substr($_SERVER["HTTP_HOST"], -(strlen($ar['DOMAIN'])+1)), ".".$ar['DOMAIN']) == 0)
				{
					$cache = $ar['DOMAIN'];
					break;
				}
			}
		}

		$bCache = true;
		return $cache;
	}

	// выводит набор IFRAME'ов для распостранения куков на ряд доменов
	function GetSpreadCookieHTML()
	{
		static $showed_already;
		$res = "";
		if($showed_already!="Y" && COption::GetOptionString("main", "ALLOW_SPREAD_COOKIE", "Y")=="Y")
		{
			if(is_array($this->arrSPREAD_COOKIE) && count($this->arrSPREAD_COOKIE)>0)
			{
				$params = "";
				reset($this->arrSPREAD_COOKIE);
				while (list($name,$ar)=each($this->arrSPREAD_COOKIE))
				{
					$ar["D"] = ""; // domain must be empty
					$params .= $name.chr(1).$ar["V"].chr(1).$ar["T"].chr(1).$ar["F"].chr(1).$ar["D"].chr(1).$ar["S"].chr(2);
				}
				$params = "s=".urlencode(base64_encode($params))."&k=".urlencode(md5($params.LICENSE_KEY));
				$arrDomain = array();
				$arrDomain[] = $_SERVER["HTTP_HOST"];
				$rs = CSite::GetList(($v1="sort"), ($v2="asc"), array("ACTIVE" => "Y"));
				while($ar = $rs->Fetch())
				{
					//$arrDomain[] = $ar["SERVER_NAME"];
					$arD = array();
					$arD = explode("\n", str_replace("\r", "\n", $ar["DOMAINS"]));
					if(is_array($arD) && count($arD)>0)
						foreach($arD as $d)
							if(strlen(trim($d))>0)
								$arrDomain[] = $d;
				}

				if(count($arrDomain)>0)
				{
					$arUniqDomains = array();
					$arrDomain = array_unique($arrDomain);
					$arrDomain2 = array_unique($arrDomain);
					foreach($arrDomain as $domain1)
					{
						$bGood = true;
						foreach($arrDomain2 as $domain2)
						{
							if(strlen($domain1)>strlen($domain2) && substr($domain1, -(strlen($domain2)+1)) == ".".$domain2)
							{
								$bGood = false;
								break;
							}
						}
						if($bGood)
							$arUniqDomains[] = $domain1;
					}

					$protocol = (CMain::IsHTTPS()) ? "https://" : "http://";
					$arrCurUrl = parse_url($protocol.$_SERVER["HTTP_HOST"]."/".$_SERVER["REQUEST_URI"]);
					foreach($arUniqDomains as $domain)
					{
						if(strlen(trim($domain))>0)
						{
							$url = $protocol.$domain."/bitrix/spread.php?".$params;
							$arrUrl = parse_url($url);
							if($arrUrl["host"] != $arrCurUrl["host"])
								$res .= '<img src="'.htmlspecialchars($url).'" alt="" style="width:0px; height:0px; position:absolute; left:-1px; top:-1px;" />'."\n";
						}
					}
//					if(strlen($res)>0)
//						$res .= '<script type="text/javascript">loaded=true;</script>';
				}
				$showed_already = "Y";
			}
		}
		return $res;
	}

	function ShowSpreadCookieHTML()
	{
		$this->AddBufferContent(Array(&$this, "GetSpreadCookieHTML"));
	}

	function AddPanelButton($arButton, $bReplace=false)
	{
		if(is_array($arButton) && count($arButton)>0)
		{
			if(isset($arButton["ID"]) && $arButton["ID"] <> "")
			{
				if(!isset($this->arPanelButtons[$arButton["ID"]]))
				{
					$this->arPanelButtons[$arButton["ID"]] = $arButton;
				}
				elseif($bReplace)
				{
					if(is_array($this->arPanelButtons[$arButton["ID"]]["MENU"]))
					{
						if(!is_array($arButton["MENU"]))
							$arButton["MENU"] = array();
						$arButton["MENU"] = array_merge($this->arPanelButtons[$arButton["ID"]]["MENU"], $arButton["MENU"]);
					}
					$this->arPanelButtons[$arButton["ID"]] = $arButton;
				}
			}
			else
			{
				$this->arPanelButtons[] = $arButton;
			}
		}
	}

	function AddPanelButtonMenu($button_id, $arMenuItem)
	{
		if(isset($this->arPanelButtons[$button_id]))
		{
			if(!is_array($this->arPanelButtons[$button_id]['MENU']))
				$this->arPanelButtons[$button_id]['MENU'] = array();
			$this->arPanelButtons[$button_id]['MENU'][] = $arMenuItem;
		}
	}

	function GetPanel()
	{
		echo $this->__GetPanel();
	}

	function __GetPanel()
	{
		global $DOCUMENT_ROOT, $APPLICATION, $USER, $MESS; //don't remove!
		if(isset($USER) && is_object($USER) && $USER->IsAuthorized())
		{
			if(file_exists($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/php_interface/include/add_top_panel.php"))
				include($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/php_interface/include/add_top_panel.php");
			include_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/public/top_panel.php");
			return GetPanelHtml();
		}
	}

	function ShowPanel()
	{
		$this->AddBufferContent(Array(&$this, "__GetPanel"));
	}

	function GetSiteByDir($cur_dir=false, $cur_host=false)
	{
		return $this->GetLang($cur_dir, $cur_host);
	}

	function AddBufferContent($callback)
	{
		$args = Array();
		$args_num = func_num_args();
		if($args_num>1)
			for($i=1; $i<$args_num; $i++)
				$args[] = func_get_arg($i);

		if(!defined("BX_BUFFER_USED") || BX_BUFFER_USED!==true)
		{
			echo call_user_func_array($callback, $args);
			return;
		}
		//var_dump(ob_get_length());
		$this->buffer_content[] = ob_get_contents();
		$this->buffer_content[] = "";
		$this->buffer_content_type[] = Array("F"=>$callback, "P"=>$args);
		$this->buffer_man = true;
		$this->auto_buffer_cleaned = false;
		ob_end_clean();
		$this->buffer_man = false;
		$this->buffered = true;
		if($this->auto_buffer_cleaned) // cross buffer fix
			ob_start(Array(&$this, "EndBufferContent"));
		else
			ob_start();
	}

	function RestartBuffer()
	{
		$this->buffer_man = true;
		ob_end_clean();
		$this->buffer_man = false;
		$this->buffer_content_type = Array();
		$this->buffer_content = Array();

		if(function_exists("getmoduleevents"))
		{
			$db_events = GetModuleEvents("main", "OnBeforeRestartBuffer");
			while($arEvent = $db_events->Fetch())
				ExecuteModuleEvent($arEvent);
		}

		ob_start(Array(&$this, "EndBufferContent"));
	}

	function &EndBufferContentMan()
	{
		if(!$this->buffered)
			return;
		$content = ob_get_contents();
		$this->buffer_man = true;
		ob_end_clean();
		$this->buffered = false;
		$this->buffer_man = false;

		$res = $this->EndBufferContent($content);
		$this->buffer_content_type = Array();
		$this->buffer_content = Array();
		return $res;
	}

	function EndBufferContent($content="")
	{
		if($this->buffer_man)
		{
			$this->auto_buffer_cleaned = true;
			return '';
		}
		if(is_object($GLOBALS["APPLICATION"])) //php 5.1.6 fix: http://bugs.php.net/bug.php?id=40104
		{
			$cnt = count($this->buffer_content_type);
			for($i=0; $i<$cnt; $i++)
				$this->buffer_content[$i*2+1] = call_user_func_array($this->buffer_content_type[$i]["F"], $this->buffer_content_type[$i]["P"]);
		}

		$content = implode('', $this->buffer_content).$content;

		if(function_exists("getmoduleevents"))
		{
			$db_events = GetModuleEvents("main", "OnEndBufferContent");
			while($arEvent = $db_events->Fetch())
				ExecuteModuleEvent($arEvent, &$content);
		}

		return $content;
	}

	function ResetException()
	{
		if($this->LAST_ERROR)
			$this->ERROR_STACK[] = $this->LAST_ERROR;
		$this->LAST_ERROR = false;
	}

	function ThrowException($msg, $id = false)
	{
		$this->ResetException();
		if(is_object($msg) && (is_subclass_of($msg, 'CApplicationException') || (strtolower(get_class($msg))=='capplicationexception')))
			$this->LAST_ERROR = $msg;
		else
			$this->LAST_ERROR = new CApplicationException($msg, $id);
	}

	function GetException()
	{
		return $this->LAST_ERROR;
	}

	function ConvertCharset($string, $charset_in, $charset_out)
	{
		$this->ResetException();

		if(!defined("BX_ICONV_DISABLE") || BX_ICONV_DISABLE!==true)
		{
			$utf_string = false;
			if(strtoupper($charset_in) == "UTF-16")
			{
				$ch = substr($string, 0, 1);
				if(($ch != "\xFF") || ($ch != "\xFE"))
					$utf_string = "\xFF\xFE".$string;
			}
			if(function_exists('iconv'))
			{
				if($utf_string)
					$res = iconv($charset_in, $charset_out."//IGNORE", $utf_string);
				else
					$res = iconv($charset_in, $charset_out."//IGNORE", $string);
				if(!$res)
					$this->ThrowException("iconv error", "ERR_CHAR_ICONV_CONVERT");
				return $res;
			}
			elseif(function_exists('libiconv'))
			{
				if($utf_string)
					$res = libiconv($charset_in, $charset_out, $utf_string);
				else
					$res = libiconv($charset_in, $charset_out, $string);
				if(!$res)
					$this->ThrowException("libiconv error", "ERR_CHAR_LIBICONV_CONVERT");
				return $res;
			}
		}

		if((strlen($string) > 0) && extension_loaded("mbstring"))
		{
			//For UTF-16 we have to detect the order of bytes
			//Default for mbstring extension is Big endian
			//Little endian have to pointed explicitly
			if(strtoupper($charset_in) == "UTF-16")
			{
				$ch = substr($string, 0, 1);
				//If Little endian found - cutoff BOF bytes and point mbstring to this fact explicitly
				if($ch == "\xFF" && substr($string, 1, 1) == "\xFE")
					return mb_convert_encoding(substr($string, 2), $charset_out, "UTF-16LE");
				//If it is Big endian, just remove BOF bytes
				elseif($ch == "\xFE" && substr($string, 1, 1) == "\xFF")
					return mb_convert_encoding(substr($string, 2), $charset_out, $charset_in);
				//Otherwise assime Little endian without BOF
				else
					return mb_convert_encoding($string, $charset_out, "UTF-16LE");
			}
			else
			{
				$res = mb_convert_encoding($string, $charset_out, $charset_in);
				if(strlen($res) > 0)
					return $res;
			}
		}

		if(!$this->pCharsetConverter)
		{
			include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/charset_converter.php");
			$this->pCharsetConverter = new CharsetConverter();
		}

		$res = $this->pCharsetConverter->Convert($string, $charset_in, $charset_out);
		if(!$res)
			$this->ThrowException($this->pCharsetConverter->errorMessage, "ERR_CHAR_BX_CONVERT");

		return $res;
	}

	function CaptchaGetCode()
	{
		include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/captcha.php");

		$cpt = new CCaptcha();
		$cpt->SetCode();

		return $cpt->GetSID();
	}

	function CaptchaCheckCode($captcha_word, $captcha_sid)
	{
		include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/captcha.php");

		$cpt = new CCaptcha();
		if ($cpt->CheckCode($captcha_word, $captcha_sid))
			return True;
		else
			return False;
	}

	function LoadClass($name)
	{
		if (file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/".$GLOBALS[$DBType]."/".$name.".php"))
			include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/".$GLOBALS[$DBType]."/".$name.".php");
		elseif (file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/".$name.".php"))
			include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/".$name.".php");
	}

	function UnJSEscape($str)
	{
		if(strpos($str, "%u")!==false)
		{
			$str = preg_replace_callback("'%u([0-9A-F]{2})([0-9A-F]{2})'i", create_function('$ch', '$res = chr(hexdec($ch[2])).chr(hexdec($ch[1])); return $GLOBALS["APPLICATION"]->ConvertCharset($res, "UTF-16", LANG_CHARSET);'), $str);
		}
		return $str;
	}

	// DEPRECATED !!!
	function ShowFileSelectDialog($event, $arResultDest, $arPath = array(), $fileFilter = "", $bAllowFolderSelect = False, $arOptions = Array())
	{
		$functionError = "";

		$event = preg_replace("/[^a-zA-Z0-9_]/i", "", $event);
		if (strlen($event) <= 0)
			$functionError .= GetMessage("MAIN_BFS_NO_EVENT").". ";

		$resultDest = "";
		if (!isset($arResultDest) || !is_array($arResultDest))
		{
			$functionError .= GetMessage("MAIN_BFS_NO_RETURN_PRM").". ";
		}
		else
		{
			if (isset($arResultDest["FUNCTION_NAME"]) && strlen($arResultDest["FUNCTION_NAME"]) > 0)
			{
				$arResultDest["FUNCTION_NAME"] = preg_replace("/[^a-zA-Z0-9_]/i", "", $arResultDest["FUNCTION_NAME"]);
				if (strlen($arResultDest["FUNCTION_NAME"]) <= 0)
					$functionError .= GetMessage("MAIN_BFS_NO_RETURN_FNC").". ";
				else
					$resultDest = "FUNCTION";
			}
			elseif (isset($arResultDest["FORM_NAME"]) && strlen($arResultDest["FORM_NAME"]) > 0
				&& isset($arResultDest["FORM_ELEMENT_NAME"]) && strlen($arResultDest["FORM_ELEMENT_NAME"]) > 0)
			{
				$arResultDest["FORM_NAME"] = preg_replace("/[^a-zA-Z0-9_]/i", "", $arResultDest["FORM_NAME"]);
				$arResultDest["FORM_ELEMENT_NAME"] = preg_replace("/[^a-zA-Z0-9_]/i", "", $arResultDest["FORM_ELEMENT_NAME"]);
				if (strlen($arResultDest["FORM_NAME"]) <= 0 || strlen($arResultDest["FORM_ELEMENT_NAME"]) <= 0)
					$functionError .= GetMessage("MAIN_BFS_NO_RETURN_FRM").". ";
				else
					$resultDest = "FORM";
			}
			elseif (isset($arResultDest["ELEMENT_ID"]) && strlen($arResultDest["ELEMENT_ID"]) > 0)
			{
				$arResultDest["ELEMENT_ID"] = preg_replace("/[^a-zA-Z0-9_]/i", "", $arResultDest["ELEMENT_ID"]);
				if (strlen($arResultDest["ELEMENT_ID"]) <= 0)
					$functionError .= GetMessage("MAIN_BFS_NO_RETURN_ID").". ";
				else
					$resultDest = "ID";
			}
			else
			{
				$functionError .= GetMessage("MAIN_BFS_BAD_RETURN").". ";
			}
		}

		if (strlen($functionError) <= 0)
		{
			$filemanPerms = $GLOBALS["APPLICATION"]->GetGroupRight("fileman");
			if ($filemanPerms <= "D")
			{
				$functionError .= GetMessage("MAIN_BFS_NO_PERMS").". ";
				?>
				<script>
				window.<?= $event ?> = function()
				{
					alert("<?= htmlspecialchars(GetMessage("MAIN_BFS_NO_PERMS")) ?>");
				}
				</script>
				<?
			}
		}

		$strOptions = '';
		foreach($arOptions as $key => $value)
			$strOptions .= '&'.urlencode($key).'='.urlencode($value);

		if(strlen($functionError) <= 0)
		{
			$bAllowFolderSelect = ($bAllowFolderSelect ? True : False);
			?>
			<script>
			window.<?= $event ?> = function()
			{
				var args = new Array();
				pWnd = window.open('/bitrix/tools/file_dialog/fd.php?function_name=<?=$event?>Result&file_filter=<?= UrlEncode($fileFilter) ?>&lang=<?= UrlEncode(LANG) ?>&site=<?= ((isset($arPath["SITE"]) && strlen($arPath["SITE"]) > 0) ? UrlEncode($arPath["SITE"]) : SITE_ID) ?>&path=<?= ((isset($arPath["PATH"]) && strlen($arPath["PATH"]) > 0) ? UrlEncode($arPath["PATH"]) : "") ?><?=$strOptions?>&folder_select=<?= ($bAllowFolderSelect ? "Y" : "N") ?>', 'BXFileSelectDialog', 'height=550,width=750,toolbar=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,modal=yes,alwaysRaised=yes,dialog=yes');
				pWnd.resizeTo(750, 550);
				pWnd.dialogArguments = args;
				if (window.focus) pWnd.focus();
			}

			window.<?= $event ?>Result = function(filename, path, site)
			{
				<?
				if ($resultDest == "FUNCTION")
					echo $arResultDest["FUNCTION_NAME"]."(filename,path,site);";
				elseif ($resultDest == "FORM")
					echo "document.".$arResultDest["FORM_NAME"].".".$arResultDest["FORM_ELEMENT_NAME"].".value=".($bAllowFolderSelect ? "" : "path+'/'+")."filename;";
				elseif ($resultDest == "ID")
					echo "document.getElementById('".$arResultDest["ELEMENT_ID"]."').value=".($bAllowFolderSelect ? "" : "path+'/'+")."filename;";
				?>
			}
			</script>
			<?
		}
		else
		{
			echo "<font color=\"#FF0000\">".htmlspecialchars($functionError)."</font>";
		}
	}

	/*
	array(
		"URL"=> 'url to open'
		"PARAMS"=> array('param' => 'value') - additional params, 2nd argument of jsPopup.ShowDialog()
	),
	*/
	function GetPopupLink($arUrl, $jsPopupSuffix = '')
	{
		//User options
		require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/interface/init_admin.php");

		// get popup size from user options
		if (class_exists('CUserOptions') && (!is_array($arUrl['PARAMS']) || $arUrl['PARAMS']['resize'] !== false))
		{
			$pos = strpos($arUrl['URL'], '?');
			if ($pos === false)
				$check_url = $arUrl['URL'];
			else
				$check_url = substr($arUrl['URL'], 0, $pos);

			$arPos = CUserOptions::GetOption(
				'jsPopup',
				'size_'.$check_url,
				array('width' => $arUrl['PARAMS']['width'], 'height' => $arUrl['PARAMS']['height'])
			);

			if ($arPos['width'])
			{
				if (!is_array($arUrl['PARAMS']))
					$arUrl['PARAMS'] = array();

				$arUrl['PARAMS']['width'] = $arPos['width'];
				$arUrl['PARAMS']['height'] = $arPos['height'];
			}

			if ($arPos['iheight'])
				$arUrl['URL'] .= ($pos === false ? '?' : '&').'bxpiheight='.intval($arPos['iheight']);
		}

		$jsPopup = 'jsPopup';
		if ($jsPopupSuffix !== '')
			$jsPopup .= '_'.$jsPopupSuffix;

		return $jsPopup.".ShowDialog('".CUtil::JSEscape($arUrl['URL'])."'".(is_array($arUrl['PARAMS']) ? ', '.CUtil::PhpToJsObject($arUrl['PARAMS']) : '').")";
	}
}

class CAllFile
{
	function SaveForDB(&$arFields, $field, $dir)
	{
		$arFile = $arFields[$field];
		if(isset($arFile) && is_array($arFile))
		{
			if($arFile["name"] <> '' || $arFile["del"] <> '' || $arFile["description"] <> '')
			{
				$res = CFile::SaveFile($arFile, $dir);
				if($res !== false)
				{
					$arFields[$field] = (intval($res) > 0? $res : false); 
					return true;
				}
			}
		}
		unset($arFields[$field]);
		return false;	
	}

	function SaveFile($arFile, $strSavePath, $bForceMD5=false, $bSkipExt=false)
	{
		global $DB;
		$strFileName = basename($arFile["name"]);	/* filename.gif */
		$upload_dir = COption::GetOptionString("main", "upload_dir", "upload");

		if(strlen($arFile["del"]) > 0)
		{
			CFile::DoDelete($arFile["old_file"]);
			if(strlen($strFileName) <= 0)
				return "NULL";
		}

		if(strlen($arFile["name"]) <= 0 || strlen($arFile["type"]) <= 0)
		{
			if(is_set($arFile, "description") && intval($arFile["old_file"])>0)
				CFile::UpdateDesc($arFile["old_file"], $arFile["description"]);
			return false;
		}

		if(is_set($arFile, "content") && !is_set($arFile, "size"))
			$arFile["size"] = strlen($arFile["content"]);
			
		/****************************** QUOTA ******************************/
		if (COption::GetOptionInt("main", "disk_space") > 0)
		{
			$quota = new CDiskQuota();
			if (!$quota->checkDiskQuota($arFile))
				return false;
		}
		/****************************** QUOTA ******************************/
			
		if($bForceMD5 != true && COption::GetOptionString("main", "save_original_file_name", "N")=="Y")
		{
			if(COption::GetOptionString("main", "convert_original_file_name", "Y")=="Y")
				$strFileName = preg_replace('/([^'.BX_VALID_FILENAME_SYMBOLS.'])/e', "chr(rand(97, 122))", $strFileName);
				
			$dir_add = "";
			$i=0;
			while(true)
			{
				$dir_add = substr(md5(uniqid(mt_rand(), true)), 0, 3);
				if(!file_exists($_SERVER["DOCUMENT_ROOT"]."/".$upload_dir."/".$strSavePath."/".$dir_add."/".$strFileName))
					break;
				if($i>=25)
				{
					$dir_add = md5(uniqid(mt_rand(), true));
					break;
				}
				$i++;
			}
			if(substr($strSavePath, -1, 1) <> "/")
				$strSavePath .= "/".$dir_add;
			else
				$strSavePath .= $dir_add."/";

			$newName = $strFileName;
		}
		else
		{
			$strFileExt = ($bSkipExt == true? '' : strrchr($arFile["name"], "."));
			while(true)
			{
				$newName = md5(uniqid(mt_rand(), true)).$strFileExt;
				if(substr($strSavePath, -1, 1) <> "/")
					$strSavePath .= "/".substr($newName, 0, 3);
				else
					$strSavePath .= substr($newName, 0, 3)."/";
					
				if(!file_exists($_SERVER["DOCUMENT_ROOT"]."/".$upload_dir."/".$strSavePath."/".$newName))
					break;
			}
		}

		//check for double extension vulnerability
		$newName = RemoveScriptExtension($newName);

		$strDirName = $_SERVER["DOCUMENT_ROOT"]."/".$upload_dir."/".$strSavePath."/";
		$strDbFileNameX = $strDirName.$newName;

		CheckDirPath($strDirName);

		if(is_set($arFile, "content"))
		{
			$f = @fopen($strDbFileNameX, "ab");
			if(!$f)
				return false;
			if(!fwrite($f, $arFile["content"]))
				return false;
			fclose($f);
		}
		elseif(!@copy($arFile["tmp_name"], $strDbFileNameX))
		{
			CFile::DoDelete($arFile["old_file"]);
			return false;
		}

		CFile::DoDelete($arFile["old_file"]);

		@chmod($strDbFileNameX, BX_FILE_PERMISSIONS);
		$imgArray = @getimagesize($strDbFileNameX);

		if(is_array($imgArray))
		{
			$intWIDTH = $imgArray[0];
			$intHEIGHT = $imgArray[1];
		}
		else
		{
			$intWIDTH = 0;
			$intHEIGHT = 0;
		}

		if($arFile["type"]=="image/pjpeg")
			$arFile["type"]="image/jpeg";

		/****************************** QUOTA ******************************/
		if (COption::GetOptionInt("main", "disk_space") > 0)
		{
			CDiskQuota::updateDiskQuota("file", $arFile["size"], "insert");
		}
		/****************************** QUOTA ******************************/

		$NEW_IMAGE_ID = CFile::DoInsert(array(
			"HEIGHT" => $intHEIGHT, 
			"WIDTH" => $intWIDTH, 
			"FILE_SIZE" => $arFile["size"], 
			"CONTENT_TYPE" => $arFile["type"], 
			"SUBDIR" => $strSavePath, 
			"FILE_NAME" => $newName, 
			"MODULE_ID" => $arFile["MODULE_ID"], 
			"ORIGINAL_NAME" => $strFileName, 
			"DESCRIPTION" => $arFile["description"],
		));

		CFile::CleanCache($NEW_IMAGE_ID);
		return $NEW_IMAGE_ID;
	}

	function DoInsert($arFields)
	{
		global $DB;
		$strSql =
			"INSERT INTO b_file(HEIGHT, WIDTH, FILE_SIZE, CONTENT_TYPE, SUBDIR, FILE_NAME, MODULE_ID, ORIGINAL_NAME, DESCRIPTION) ".
			"VALUES('".intval($arFields["HEIGHT"])."', '".intval($arFields["WIDTH"])."', '".intval($arFields["FILE_SIZE"])."', '".
				$DB->ForSql($arFields["CONTENT_TYPE"], 255)."' , '".$DB->ForSql($arFields["SUBDIR"], 255)."', '".
				$DB->ForSQL($arFields["FILE_NAME"], 255)."', '".$DB->ForSQL($arFields["MODULE_ID"], 50)."', '".
				$DB->ForSql($arFields["ORIGINAL_NAME"], 255)."', '".$DB->ForSQL($arFields["DESCRIPTION"], 255)."') ";
		$DB->Query($strSql);
		return $DB->LastID();
	}

	function CleanCache($ID)
	{
		$ID = intval($ID);
		if(CACHED_b_file!==false)
		{
			$bucket_size = intval(CACHED_b_file_bucket_size);
			if($bucket_size<=0) $bucket_size = 10;
			$bucket = intval($ID/$bucket_size);
			$GLOBALS["CACHE_MANAGER"]->Clean("b_file".$bucket, "b_file");
		}
	}

	function GetByID($FILE_ID)
	{
		global $DB, $CACHE_MANAGER;
		$FILE_ID = intval($FILE_ID);
		if(CACHED_b_file===false)
		{
			$strSql = "SELECT f.*,".$DB->DateToCharFunction("f.TIMESTAMP_X")." as TIMESTAMP_X FROM b_file f WHERE f.ID=".$FILE_ID;
			$z = $DB->Query($strSql, false, "FILE: ".__FILE__."<br>LINE: ".__LINE__);
		}
		else
		{
			$bucket_size = intval(CACHED_b_file_bucket_size);
			if($bucket_size<=0) $bucket_size = 10;

			$bucket = intval($FILE_ID/$bucket_size);
			if($CACHE_MANAGER->Read(CACHED_b_file, $cache_id="b_file".$bucket, "b_file"))
				$arFiles = $CACHE_MANAGER->Get($cache_id);
			else
			{
				$arFiles = array();
				$rs = $DB->Query("
					SELECT f.*,".$DB->DateToCharFunction("f.TIMESTAMP_X")." as TIMESTAMP_X FROM b_file f
					WHERE f.ID between ".($bucket*$bucket_size)." AND ".(($bucket+1)*$bucket_size-1)
				);
				while($ar = $rs->Fetch())
					$arFiles[$ar["ID"]]=$ar;
				$CACHE_MANAGER->Set($cache_id, $arFiles);
			}
			$z = new CDBResult;
			$z->InitFromArray(array_key_exists($FILE_ID, $arFiles)?array($arFiles[$FILE_ID]):array());
		}
		return $z;
	}

	function GetList($arOrder = Array(), $arFilter = Array(), $arParams = Array())
	{
		global $DB;
		$arSqlSearch = Array();
		$arSqlOrder = Array();
		$strSqlSearch = "";

		$filter_keys = (!is_array($arFilter) ? Array() : array_keys($arFilter));
		for($i =0; $i<count($filter_keys); $i++)
		{
			$key = strToUpper($filter_keys[$i]);
			$val = $DB->ForSql($arFilter[$filter_keys[$i]]);

			if(strlen($val)<=0) continue;

			if (substr($key, 0, 1)=="@")
			{
				$key = substr($key, 1);
				$strOperation = "IN";
			}

			switch($key)
			{
				case "MODULE":
				case "ID":
					if ($strOperation == "IN")
						$arSqlSearch[] = "f.".$key." IN (".$val.")";
					else
						$arSqlSearch[] = "f.".$key." = '".$val."'";
				break;
			}
		}
		if (count($arSqlSearch) > 0)
			$strSqlSearch = " WHERE (".implode(") AND (", $arSqlSearch).")";

		$strSql =
			"SELECT f.*,".$DB->DateToCharFunction("f.TIMESTAMP_X")." as TIMESTAMP_X ".
			"FROM b_file f ".
			$strSqlSearch." ".
			"ORDER BY f.ID ASC";

		$res = $DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);

		return $res;
	}

	function GetFileArray($FILE_ID)
	{
		$arReturn = false;

		if (intval($FILE_ID) > 0)
		{
			$res = CFile::GetByID($FILE_ID);
			if ($arFile = $res->GetNext())
			{
				$src = "/".(COption::GetOptionString("main", "upload_dir", "upload"))."/".$arFile["SUBDIR"]."/".$arFile["FILE_NAME"];
				$src = str_replace("//","/",$src);
				if(defined("BX_IMG_SERVER"))
					$src = BX_IMG_SERVER.$src;
				$arReturn = $arFile + Array("SRC" => $src);
			}
		}
		return $arReturn;
	}

	function CopyFile($FILE_ID)
	{
		global $DOCUMENT_ROOT, $DB;
		$err_mess = "FILE: ".__FILE__."<br>LINE: ";
		$z = CFile::GetByID($FILE_ID);
		if($zr = $z->Fetch())
		{
			/****************************** QUOTA ******************************/
			if (COption::GetOptionInt("main", "disk_space") > 0)
			{
				$quota = new CDiskQuota();
				if (!$quota->checkDiskQuota($zr))
					return false;
			}
			/****************************** QUOTA ******************************/

			$strDirName = $DOCUMENT_ROOT."/".(COption::GetOptionString("main", "upload_dir", "upload"));

			$strOldFile = $strDirName."/".$zr["SUBDIR"]."/".$zr["FILE_NAME"];
			$strOldFile = str_replace("//","/",$strOldFile);

			$ext = strrchr($zr["FILE_NAME"], ".");
			$newName = md5(uniqid(mt_rand())).$ext;
			$strNewFile = $strDirName."/".$zr["SUBDIR"]."/".$newName;
			$strNewFile = str_replace("//","/",$strNewFile);
			if(!@copy($strOldFile, $strNewFile))
			{
				return false;
			}
			else
			{
				$arFields = array(
					"TIMESTAMP_X"	=> $DB->GetNowFunction(),
					"MODULE_ID"		=> "'".$zr["MODULE_ID"]."'",
					"HEIGHT"		=> "'".$zr["HEIGHT"]."'",
					"WIDTH"			=> "'".$zr["WIDTH"]."'",
					"FILE_SIZE"		=> "'".$zr["FILE_SIZE"]."'",
					"ORIGINAL_NAME"	=> "'".$DB->ForSql($zr["ORIGINAL_NAME"], 255)."'",
					"DESCRIPTION"	=> "'".$DB->ForSql($zr["DESCRIPTION"], 255)."'",
					"CONTENT_TYPE"	=> "'".$zr["CONTENT_TYPE"]."'",
					"SUBDIR"		=> "'".$zr["SUBDIR"]."'",
					"FILE_NAME"		=> "'".$DB->ForSql($newName,255)."'"
					);
				$NEW_FILE_ID = $DB->Insert("b_file",$arFields, $err_mess.__LINE__);

				/****************************** QUOTA ******************************/
				if (COption::GetOptionInt("main", "disk_space") > 0)
				{
					CDiskQuota::updateDiskQuota("file", $zr["FILE_SIZE"], "copy");
				}
				/****************************** QUOTA ******************************/

				CFile::CleanCache($NEW_FILE_ID);
			}
		}
		return intval($NEW_FILE_ID);
	}

	function UpdateDesc($ID, $desc)
	{
		global $DB;
		$DB->Query("UPDATE b_file SET DESCRIPTION='".$DB->ForSql($desc, 255)."' WHERE ID=".intval($ID));
		CFile::CleanCache($ID);
	}

	function InputFile($strFieldName, $int_field_size, $strImageID, $strImageStorePath=false, $int_max_file_size=0, $strFileType="IMAGE", $field_file="class=typefile", $description_size=0, $field_text="class=typeinput", $field_checkbox="", $bShowNotes = True)
	{
		if($strImageStorePath===false)
			$strImageStorePath = COption::GetOptionString("main", "upload_dir", "upload");

		$strReturn1 = "";
		$strReturn2 = "";

		if($int_max_file_size != 0)
			$strReturn1 = $strReturn."<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"".$int_max_file_size."\" /> ";

		$strReturn1 = $strReturn1.' <input name="'.$strFieldName.'" '.$field_file.'  size="'.$int_field_size.'" type="file" />';
		global $DOCUMENT_ROOT, $DB;
		$strDescription = "";
		$strImageID=IntVal($strImageID);
		if($strImageID > 0)
		{
			$db_img = CFile::GetByID($strImageID);
			$db_img_arr = $db_img->Fetch();
			if($db_img_arr)
			{
				$strDescription = $db_img_arr["DESCRIPTION"];
				$sImagePath = "/".$strImageStorePath."/".$db_img_arr["SUBDIR"]."/".$db_img_arr["FILE_NAME"];
				$sImagePath = str_replace("//","/",$sImagePath);
				if(($p=strpos($strFieldName, "["))>0)
				{
					$strDelName = substr($strFieldName, 0, $p)."_del".substr($strFieldName, $p);
					$strOldName = substr($strFieldName, 0, $p)."_old".substr($strFieldName, $p);
				}
				else
				{
					$strDelName = $strFieldName."_del";
					$strOldName = $strFieldName."_old";
				}
				//$strReturn = $strReturn."<input type=\"hidden\" name=\"".$strOldName."\" value=\"".$strImageID."\">";

				if($bShowNotes)
				{
					if(file_exists($DOCUMENT_ROOT.$sImagePath))
					{
						$strReturn2 = $strReturn."<br>&nbsp;".GetMessage("FILE_TEXT").": ".$sImagePath;
						if(strtoupper($strFileType)=="IMAGE")
						{
							$intWidth = intval($db_img_arr["WIDTH"]);
							$intHeight = intval($db_img_arr["HEIGHT"]);
							if($intWidth>0 && $intHeight>0)
							{
								$strReturn2 = $strReturn2."<br>&nbsp;".GetMessage("FILE_WIDTH").": $intWidth";
								$strReturn2 = $strReturn2."<br>&nbsp;".GetMessage("FILE_HEIGHT").": $intHeight";
							}
						}
						$a = array("b", "Kb", "Mb", "Gb");
						$pos = 0;
						$size = $db_img_arr["FILE_SIZE"];
						while($size>=1024) {$size /= 1024; $pos++;}
						$intSize = round($size,2)." ".$a[$pos];
						$strReturn2 = $strReturn2."<br>&nbsp;".GetMessage("FILE_SIZE").": $intSize";
					}
					else
					{
						$strReturn2 = $strReturn2."<br>".GetMessage("FILE_NOT_FOUND").": ".$sImagePath;
					}
				}
				$strReturn2 = $strReturn2."<br><input ".$field_checkbox." type=\"checkbox\" name=\"".$strDelName."\" value=\"Y\" id=\"".$strDelName."\" /> <label for=\"".$strDelName."\">".GetMessage("FILE_DELETE")."</label>";
			}
		}

		$strReturn =
				$strReturn1.
				(
					$description_size>0
				?
					'<br>'.
					/*'Описание: '.*/'<input type="text" value="'.htmlspecialchars($strDescription).'" name="'.$strFieldName.'_descr" '.$field_text.' size="'.$description_size.'" title="'.GetMessage("MAIN_FIELD_FILE_DESC").'" />'
				:
					''
				).
				$strReturn2;
		return $strReturn;
	}

	function GetImageExtensions()
	{
		return "jpg,bmp,jpeg,jpe,gif,png";
	}

	function GetFlashExtensions()
	{
		return "swf";
	}

	function IsImage($filename, $mime_type=false)
	{
		$filename = trim($filename, ". \r\n\t");
		$arr = explode(".", $filename);
		$ext = strtoupper($arr[count($arr)-1]);
		if(strlen($ext)>0)
		{
			if(in_array($ext, explode(",", strtoupper(CFile::GetImageExtensions()))))
				if(strpos($mime_type, "image/")!==false || $mime_type===false) return true;
		}
		return false;
	}

	function CheckImageFile($arFile, $iMaxSize=0, $iMaxWidth=0, $iMaxHeight=0, $access_typies=array())
	{
		if(strlen($arFile["name"])<=0)
			return "";

		$file_type = GetFileType($arFile["name"]);
		// если тип файла не входит в массив допустимых типов то
		// присваиваем ему тип IMAGE по умолчанию
		if(!in_array($file_type, $access_typies))
			$file_type = "IMAGE";

		switch ($file_type)
		{
			case "FLASH":
				$res = CFile::CheckFile($arFile, $iMaxSize, "application/x-shockwave-flash", CFile::GetFlashExtensions());
				break;
			default:
				$res = CFile::CheckFile($arFile, $iMaxSize, "image/", CFile::GetImageExtensions());
		}

		if(strlen($res)>0)
			return $res;

		$imgArray = @getimagesize($arFile["tmp_name"]);

		if(is_array($imgArray))
		{
/*
			$imgfname = $arFile["tmp_name"];
			$imghandle = fopen($imgfname, "rb");
			$imgcontents = fread($imghandle, filesize($imgfname));
			fclose($imghandle);
			'<[a-z0-9]([\x0B\x00][a-z0-9]*)[ \r\n\t\x00\x0B>]'
			if(preg_match("'<script'i", $imgcontents) || )
				return GetMessage("FILE_BAD_FILE_TYPE").".<br>";
*/
			$intWIDTH = $imgArray[0];
			$intHEIGHT = $imgArray[1];
		}
		else
			return GetMessage("FILE_BAD_FILE_TYPE").".<br>";

		//проверка на максимальный размер картинки (ширина/высота)
		if($iMaxWidth > 0 && ($intWIDTH > $iMaxWidth || $intWIDTH == 0) || $iMaxHeight > 0 && ($intHEIGHT > $iMaxHeight || $intHEIGHT == 0))
			return GetMessage("FILE_BAD_MAX_RESOLUTION")." (".$iMaxWidth.", ".$iMaxHeight.").<br>";
	}

	function CheckFile($arFile, $intMaxSize=0, $strMimeType=false, $strExt=false)
	{
		/****************************** QUOTA ******************************/
		if (COption::GetOptionInt("main", "disk_space") > 0)
		{
			$quota = new CDiskQuota;
			if (!$quota->checkDiskQuota($arFile))
				return $quota->LAST_ERROR;
		}
		/****************************** QUOTA ******************************/
		if(strlen($arFile["name"])<=0)
			return "";

		if(COption::GetOptionString("main", "save_original_file_name", "N")=="Y" && COption::GetOptionString("main", "convert_original_file_name", "Y")!="Y")
		{
			$filename = basename($arFile["name"]);
			if(preg_match('/[^'.BX_VALID_FILENAME_SYMBOLS.']/', $filename))
				return GetMessage("MAIN_BAD_FILENAME");
		}

		if($intMaxSize>0 && intval($arFile["size"])>$intMaxSize)
		{
			return GetMessage("FILE_BAD_SIZE")." (".$intMaxSize.")!";
		}

		if($strExt)
		{
			$strFileExt = strrchr($arFile["name"], ".");
			if(strlen($strFileExt) <= 0 )
				return GetMessage("FILE_BAD_TYPE");
		}

		//Check mime_type and ext
		if($strMimeType!==false && substr($arFile["type"], 0, strlen($strMimeType)) != $strMimeType)
			return GetMessage("FILE_BAD_TYPE")."!";

		if($strExt===false)
			return "";

		$IsExtCorrect = true;
		if($strExt)
		{
			$IsExtCorrect=false;
			$tok = strtok($strExt,",");
			while($tok)
			{
				if(".".strtoupper(trim($tok)) == strtoupper(trim($strFileExt)))
				{
					$IsExtCorrect=true;
					break;
				}
				$tok = strtok(",");
			}
		}

		if($IsExtCorrect)
			return "";

		return GetMessage("FILE_BAD_TYPE")." (".$strFileExt.")!";
	}

	function ShowFile($iFileID, $max_file_size=0, $iMaxW=0, $iMaxH=0, $bPopup=false, $sParams="border=0", $sPopupTitle=false, $iSizeWHTTP=0, $iSizeHHTTP=0)
	{
		global $DB;
		$iFileID = IntVal($iFileID);
		$strResult = "";
		if($iFileID>0)
		{
			$res = CFile::GetByID($iFileID);
			if($ar = $res->Fetch())
			{
				$strFile = "/".(COption::GetOptionString("main", "upload_dir", "upload"))."/".$ar["SUBDIR"]."/".$ar["FILE_NAME"];
				$strFile = str_replace("//", "/", $strFile);

				$max_file_size = IntVal($max_file_size);
				if($max_file_size<=0)
					$max_file_size = 1000000000;
				$ct = $ar["CONTENT_TYPE"];
				if($max_file_size>=$ar["FILE_SIZE"] && (substr($ct, 0, 6) == "video/" || substr($ct, 0, 6) == "audio/"))
					$strResult =
						'<OBJECT ID="WMP64" WIDTH="'.($iMaxW>0?$iMaxW:'250').'" HEIGHT="'.(substr($ct, 0, 6) == "audio/"?'45':($iMaxH>0?$iMaxH:'220')).'" CLASSID="CLSID:22D6f312-B0F6-11D0-94AB-0080C74C7E95" STANDBY="Loading Windows Media Player components..." TYPE="application/x-oleobject"> '.
						'<PARAM NAME="AutoStart" VALUE="false"> '.
						'<PARAM NAME="ShowDisplay" VALUE="false">'.
						'<PARAM NAME="ShowControls" VALUE="true" >'.
						'<PARAM NAME="ShowStatusBar" VALUE="0">'.
						'<PARAM NAME="FileName" VALUE="'.$strFile.'"> '.
						'</OBJECT>';
				elseif($max_file_size>=$ar["FILE_SIZE"] && substr($ct, 0, 6) == "image/")
					$strResult = ShowImage($strFile, $iMaxW, $iMaxH, $sParams, "", $bPopup, $sPopupTitle, $iSizeWHTTP, $iSizeHHTTP);
				else
					$strResult = ' [ <a href="'.$strFile.'" title="'.GetMessage("FILE_FILE_DOWNLOAD").'">'.GetMessage("FILE_DOWNLOAD").'</a> ] ';
			}
			return $strResult;
		}
		return "";
	}

	function DisableJSFunction($b=true)
	{
		global $SHOWIMAGEFIRST;
		$SHOWIMAGEFIRST = $b;
	}

	function OutputJSImgShw()
	{
		global $SHOWIMAGEFIRST;
		if(!defined("ADMIN_SECTION") && $SHOWIMAGEFIRST!==true)
		{
			echo
'<script type="text/javascript">
function ImgShw(ID, width, height, alt)
{
	var scroll = "no";
	var top=0, left=0;
	if(width > screen.width-10 || height > screen.height-28) scroll = "yes";
	if(height < screen.height-28) top = Math.floor((screen.height - height)/2-14);
	if(width < screen.width-10) left = Math.floor((screen.width - width)/2-5);
	width = Math.min(width, screen.width-10);
	height = Math.min(height, screen.height-28);
	var wnd = window.open("","","scrollbars="+scroll+",resizable=yes,width="+width+",height="+height+",left="+left+",top="+top);
	wnd.document.write(
		"<html><head>"+
		"<"+"script type=\"text/javascript\">"+
		"function KeyPress()"+
		"{"+
		"	if(window.event.keyCode == 27) "+
		"		window.close();"+
		"}"+
		"</"+"script>"+
		"<title>"+(alt == ""? "'.GetMessage("main_js_img_title").'":alt)+"</title></head>"+
		"<body topmargin=\"0\" leftmargin=\"0\" marginwidth=\"0\" marginheight=\"0\" onKeyPress=\"KeyPress()\">"+
		"<img src=\""+ID+"\" border=\"0\" alt=\""+alt+"\" />"+
		"</body></html>"
	);
	wnd.document.close();
}
</script>';

			$SHOWIMAGEFIRST=true;
		}
	}

	function _GetImgParams($strImage, $iSizeWHTTP=0, $iSizeHHTTP=0)
	{
		global $DB;
		if(strlen($strImage) <= 0)
			return false;
		$strAlt = "";
		if(IntVal($strImage)>0)
		{
			$db_img = CFile::GetByID($strImage);
			$db_img_arr = $db_img->Fetch();
			if($db_img_arr)
			{
				$strImage = "/".(COption::GetOptionString("main", "upload_dir", "upload"))."/".$db_img_arr["SUBDIR"]."/".$db_img_arr["FILE_NAME"];
				$strImage = str_replace("//","/",$strImage);
				if(defined("BX_IMG_SERVER"))
					$strImage = BX_IMG_SERVER.$strImage;
				$intWidth = intval($db_img_arr["WIDTH"]);
				$intHeight = intval($db_img_arr["HEIGHT"]);
				$strAlt = $db_img_arr["DESCRIPTION"];
			}
		}
		else
		{
			if(substr(strtolower($strImage), 0, 7)!="http://")
			{
				if(is_file($_SERVER["DOCUMENT_ROOT"].$strImage))
				{
					$arSize = getimagesize($_SERVER["DOCUMENT_ROOT"].$strImage);
					$intWidth = intval($arSize[0]);
					$intHeight = intval($arSize[1]);
				}
				else
					return false;
			}
			else
			{
				$intWidth = intval($iSizeWHTTP);
				$intHeight = intval($iSizeHHTTP);
			}
		}
		return Array("SRC"=>$strImage, "WIDTH"=>$intWidth, "HEIGHT"=>$intHeight, "ALT"=>$strAlt);
	}

	function GetPath($img_id)
	{
		$res = CFile::_GetImgParams($img_id);
		return $res["SRC"];
	}

	function ShowImage($strImage, $iMaxW=0, $iMaxH=0, $sParams=null, $strImageUrl="", $bPopup=false, $sPopupTitle=false, $iSizeWHTTP=0, $iSizeHHTTP=0)
	{
		global $DOCUMENT_ROOT, $DB;

		if(!($arImgParams = CFile::_GetImgParams($strImage, $iSizeWHTTP, $iSizeHHTTP)))
			return "";

		if($sParams === null || $sParams === false)
			$sParams = ' border="0" ';

		$iMaxW = intval($iMaxW);
		$iMaxH = intval($iMaxH);

		$strImage = htmlspecialchars($arImgParams["SRC"]);
		$intWidth = $arImgParams["WIDTH"];
		$intHeight = $arImgParams["HEIGHT"];
		$strAlt = $arImgParams["ALT"];

		if($sPopupTitle===false)
			$sPopupTitle=GetMessage("FILE_ENLARGE");

		$file_type = GetFileType($strImage);
		switch($file_type):
			case "FLASH":
				$iWidth = $intWidth;
				$iHeight = $intHeight;
				if($iMaxW>0 && $iMaxH>0 && ($intWidth > $iMaxW || $intHeight > $iMaxH))
				{
					$coeff = ($intWidth/$iMaxW > $intHeight/$iMaxH? $intWidth/$iMaxW : $intHeight/$iMaxH);
					$iWidth = intval(roundEx($intHeight/$coeff));
					$iHeight = intval(roundEx($intWidth/$coeff));
				}
				$strReturn = '
					<object
						classid="clsid:D27CDB6E-AE6D-11CF-96B8-444553540000"
						codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0"
						id="banner"
						WIDTH="'.$iWidth.'"
						HEIGHT="'.$iHeight.'"
						ALIGN="">
							<PARAM NAME="movie" VALUE="'.$strImage.'" />
							<PARAM NAME="quality" VALUE="high" />
							<PARAM NAME="bgcolor" VALUE="#FFFFFF" />
							<embed
								src="'.$strImage.'"
								quality="high"
								bgcolor="#FFFFFF"
								WIDTH="'.$iWidth.'"
								HEIGHT="'.$iHeight.'"
								NAME="banner"
								ALIGN=""
								TYPE="application/x-shockwave-flash"
								PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer">
							</embed>
					</object>
					';
				return $bPopup? $strReturn : print_url($strImageUrl, $strReturn);

			default:
				$strReturn = "<img src=\"".$strImage."\" ".$sParams." width=\"".$intWidth."\" height=\"".$intHeight."\" alt=\"".htmlspecialchars($strAlt)."\" />";
				if($iMaxW > 0 && $iMaxH > 0) //need to check scale, maybe show actual size in the popup window
				{
					//check for max dimensions exceeding
					if($intWidth > $iMaxW || $intHeight > $iMaxH)
					{
						$coeff = ($intWidth/$iMaxW > $intHeight/$iMaxH? $intWidth/$iMaxW : $intHeight/$iMaxH);
						$strReturn = "<img src=\"".$strImage."\" ".$sParams." width=\"".intval(roundEx($intWidth/$coeff))."\" height=\"".intval(roundEx($intHeight/$coeff))."\" alt=\"".htmlspecialchars($strAlt)."\" />";

						if($bPopup) //show in JS window
						{
							if(strlen($strImageUrl)>0)
							{
								$strReturn =
									'<a href="'.$strImageUrl.'" title="'.$sPopupTitle.'" target="_blank">'.
									'<img src="'.$strImage.'" '.$sParams.' width="'.intval(roundEx($intWidth/$coeff)).'" height="'.intval(roundEx($intHeight/$coeff)).' alt="'.htmlspecialchars($sPopupTitle).'" />'.
									'</a>';
							}
							else
							{
								CFile::OutputJSImgShw();

								$strReturn =
									"<a title=\"".$sPopupTitle."\" onClick=\"ImgShw('".AddSlashes($strImage)."','".$intWidth."','".$intHeight."', '".AddSlashes(htmlspecialcharsex(htmlspecialcharsex($strAlt)))."'); return false;\" href=\"".$strImage."\" target=\"_blank\">".
									"<img src=\"".$strImage."\" ".$sParams." width=\"".intval(roundEx($intWidth/$coeff))."\" height=\"".intval(roundEx($intHeight/$coeff))."\" /></a>";
							}
						}
					}
				}
				return $bPopup? $strReturn : print_url($strImageUrl, $strReturn);

		endswitch;

		return $bPopup? $strReturn : print_url($strImageUrl, $strReturn);
	}

	function Show2Images($strImage1, $strImage2, $iMaxW=0, $iMaxH=0, $sParams="border=0", $sPopupTitle=false, $iSizeWHTTP=0, $iSizeHHTTP=0)
	{
		global $DOCUMENT_ROOT, $DB;

		if($sPopupTitle===false)
			$sPopupTitle=GetMessage("FILE_ENLARGE");

		if(!($arImgParams = CFile::_GetImgParams($strImage1, $iSizeWHTTP, $iSizeHHTTP)))
			return "";

		$strImage1 = $arImgParams["SRC"];
		$intWidth = $arImgParams["WIDTH"];
		$intHeight = $arImgParams["HEIGHT"];
		$strAlt = $arImgParams["ALT"];

		$coeff = 1;
		if($iMaxW > 0 && $iMaxH > 0 && ($intWidth > $iMaxW || $intHeight > $iMaxH))
		{
			$coeff = ($intWidth/$iMaxW > $intHeight/$iMaxH? $intWidth/$iMaxW : $intHeight/$iMaxH);
			$strReturn = "<img src=\"".$strImage1."\" ".$sParams." width=".intval(roundEx($intWidth/$coeff))." height=".intval(roundEx($intHeight/$coeff))." alt=\"".htmlspecialchars($strAlt)."\" />";
		}

		if($arImgParams = CFile::_GetImgParams($strImage2, $iSizeWHTTP, $iSizeHHTTP))
		{
			$strImage2 = $arImgParams["SRC"];
			$intWidth2 = $arImgParams["WIDTH"];
			$intHeight2 = $arImgParams["HEIGHT"];
			$strAlt2 = $arImgParams["ALT"];

			CFile::OutputJSImgShw();

			$strReturn =
				"<a title=\"".$sPopupTitle."\" onClick=\"ImgShw('".AddSlashes($strImage2)."','".$intWidth2."','".$intHeight2."', '".AddSlashes(htmlspecialcharsex(htmlspecialcharsex($strAlt2)))."'); return false;\" href=\"".$strImage2."\" target=_blank>".
				"<img src=\"".$strImage1."\" ".$sParams." width=".intval(roundEx($intWidth/$coeff))." height=".intval(roundEx($intHeight/$coeff))." /></a>";
		}

		return $strReturn;
	}

	function MakeFileArray($path, $mimetype=false)
	{
		$arFile = Array();
		if(intval($path)>0)
		{
			$res = CFile::GetByID($path);
			if($ar = $res->Fetch())
			{
				$arFile["name"] = (strlen($ar['ORIGINAL_NAME'])>0?$ar['ORIGINAL_NAME']:$ar['FILE_NAME']);
				$arFile["size"] = $ar['FILE_SIZE'];
				$arFile["tmp_name"] = ereg_replace("[\\/]+", "/", $_SERVER['DOCUMENT_ROOT'].'/'.(COption::GetOptionString('main', 'upload_dir', 'upload')).'/'.$ar['SUBDIR'].'/'.$ar['FILE_NAME']);
				$arFile["type"] = $ar['CONTENT_TYPE'];
				$arFile["description"] = $ar['DESCRIPTION'];
				return $arFile;
			}
		}
		if (strpos($path, "http://")===false &&
			strpos($path, "ftp://")===false &&
			strpos($path, "php://")===false)
		{
			$path = ereg_replace("[\\/]+", "/", $path);
			if(!file_exists($path)) return NULL;
			$arFile["name"] = basename($path);
			$arFile["size"] = filesize($path);
			$arFile["tmp_name"] = $path;
			$arFile["type"] = $mimetype;
			if(strlen($arFile["type"])<=0 && function_exists("mime_content_type"))
				$arFile["type"] = mime_content_type($path);

			if(strlen($arFile["type"])<=0 && function_exists("image_type_to_mime_type"))
			{
				$arTmp = getimagesize($path);
				$arFile["type"] = $arTmp["mime"];
			}

			if(strlen($arFile["type"])<=0)
			{
				$arTypes = Array("jpeg"=>"image/jpeg", "jpe"=>"image/jpeg", "jpg"=>"image/jpeg", "png"=>"image/png", "gif"=>"image/gif", "bmp"=>"image/bmp");
				$arFile["type"]= $arTypes[strtolower(substr($path, bxstrrpos($path, ".")+1))];
			}
		}
		else
		{
			$content = "";
			if ($fp = @fopen($path,"rb"))
			{
				while (!feof($fp)) $content .= fgets($fp,1024);
				if (strlen($content)>0)
				{
					$file_name = basename($path);
					$bname = $_SERVER["DOCUMENT_ROOT"]."/".(COption::GetOptionString("main", "upload_dir", "upload"))."/tmp";
					while(true)
					{
						$dir_add = substr(md5(uniqid(mt_rand(), true)), 0, 3);
						$temp_path = $bname."/".$dir_add."/".$file_name;
						if(!file_exists($temp_path)) break;
						if($i>=25)
						{
							$dir_add = md5(uniqid(mt_rand(), true));
							$temp_path = $bname."/".$dir_add."/".$file_name;
							break;
						}
					}
					if (RewriteFile($temp_path, $content)) $arFile = CFile::MakeFileArray($temp_path);
				}
				fclose($fp);
			}
		}

		if(strlen($arFile["type"])<=0)
			$arFile["type"] = "unknown";

		return $arFile;
	}

	function ChangeSubDir($module_id, $old_subdir, $new_subdir)
	{
		global $DB;

		if ($old_subdir!=$new_subdir)
		{
			$strSql = "
				UPDATE b_file
				SET SUBDIR = REPLACE(SUBDIR,'".$DB->ForSQL($old_subdir)."','".$DB->ForSQL($new_subdir)."')
				WHERE MODULE_ID='".$DB->ForSQL($module_id)."'
			";

			if($rs = $DB->Query($strSql, false, $err_mess.__LINE__))
			{
				$from = "/".COption::GetOptionString("main", "upload_dir", "upload")."/".$old_subdir;
				$to = "/".COption::GetOptionString("main", "upload_dir", "upload")."/".$new_subdir;
				CopyDirFiles($_SERVER["DOCUMENT_ROOT"].$from, $_SERVER["DOCUMENT_ROOT"].$to, true, true, true);
				//Reset All b_file cache
				$GLOBALS["CACHE_MANAGER"]->CleanDir("b_file");
			}
		}
	}
}

global $MAIN_LANGS_CACHE;
$MAIN_LANGS_CACHE = Array();

global $MAIN_LANGS_ADMIN_CACHE;
$MAIN_LANGS_ADMIN_CACHE = Array();


class CAllSite
{
	function InDir($strDir)
	{
		global $APPLICATION;
		return (substr($APPLICATION->GetCurPage(), 0, strlen($strDir))==$strDir);
	}

	function InPeriod($iUnixTimestampFrom, $iUnixTimestampTo)
	{
		if($iUnixTimestampFrom>0 && time()<$iUnixTimestampFrom)
			return false;
		if($iUnixTimestampTo>0 && time()>$iUnixTimestampTo)
			return false;

		return true;
	}

	function InGroup($arGroups)
	{
		global $USER;
		$arUserGroups = $USER->GetUserGroupArray();
		if (count(array_intersect($arUserGroups,$arGroups))>0)
			return true;
		return false;
	}

	function GetDateFormat($type="FULL", $lang=false, $bSearchInSitesOnly = false)
	{
		if($lang===false)
			$lang = LANG;

		if(!$bSearchInSitesOnly && defined("ADMIN_SECTION") && ADMIN_SECTION===true)
		{
			global $MAIN_LANGS_ADMIN_CACHE;
			if(!is_set($MAIN_LANGS_ADMIN_CACHE, $lang))
			{
				$res = CLanguage::GetByID($lang);
				if($res = $res->Fetch())
					$MAIN_LANGS_ADMIN_CACHE[$res["LID"]]=$res;
			}

			if(is_set($MAIN_LANGS_ADMIN_CACHE, $lang))
			{
				if(strtoupper($type)=="FULL")
					return strtoupper($MAIN_LANGS_ADMIN_CACHE[$lang]["FORMAT_DATETIME"]);
				return strtoupper($MAIN_LANGS_ADMIN_CACHE[$lang]["FORMAT_DATE"]);
			}
		}

		// if LANG is not found in LangAdmin:
		global $MAIN_LANGS_CACHE;
		if(!is_set($MAIN_LANGS_CACHE, $lang))
		{
			$res = CLang::GetByID($lang);
			$res = $res->Fetch();
			$MAIN_LANGS_CACHE[$res["LID"]]=$res;
    		if(defined("ADMIN_SECTION") && ADMIN_SECTION===true)
				$MAIN_LANGS_ADMIN_CACHE[$res["LID"]]=$res;
		}

		if(strtoupper($type)=="FULL")
		{
			$format = strtoupper($MAIN_LANGS_CACHE[$lang]["FORMAT_DATETIME"]);
			if (strlen($format)<=0) $format = "DD.MM.YYYY HH:MI:SS";
		}
		else
		{
			$format = strtoupper($MAIN_LANGS_CACHE[$lang]["FORMAT_DATE"]);
			if (strlen($format)<=0) $format = "DD.MM.YYYY";
		}
		return $format;
	}

	function CheckFields($arFields, $ID=false)
	{
		global $DB;
		$this->LAST_ERROR = "";
		$arMsg = Array();

		if(is_set($arFields, "NAME") && strlen($arFields["NAME"])<2)
		{
			$this->LAST_ERROR .= GetMessage("BAD_SITE_NAME")." ";
			$arMsg[] = array("id"=>"NAME", "text"=> GetMessage("BAD_SITE_NAME"));
		}
		if($ID===false && is_set($arFields, "LID") && strlen($arFields["LID"])!=2)
		{
			$this->LAST_ERROR .= GetMessage("BAD_SITE_LID")." ";
			$arMsg[] = array("id"=>"LID", "text"=> GetMessage("BAD_SITE_LID"));
		}
		if(is_set($arFields, "DIR") && strlen($arFields["DIR"])<=0)
		{
			$this->LAST_ERROR .= GetMessage("BAD_LANG_DIR")." ";
			$arMsg[] = array("id"=>"DIR", "text"=> GetMessage("BAD_LANG_DIR"));
		}
		if($ID===false && !is_set($arFields, "LANGUAGE_ID"))
		{
			$this->LAST_ERROR .= GetMessage("MAIN_BAD_LANGUAGE_ID")." ";
			$arMsg[] = array("id"=>"LANGUAGE_ID", "text"=> GetMessage("MAIN_BAD_LANGUAGE_ID"));
		}
		elseif($ID!==false && is_set($arFields, "LANGUAGE_ID"))
		{
			$dbl_check = CLanguage::GetByID($arFields["LANGUAGE_ID"]);
			if(!$dbl_check->Fetch())
			{
				$this->LAST_ERROR .= GetMessage("MAIN_BAD_LANGUAGE_ID_BAD")." ";
				$arMsg[] = array("id"=>"LANGUAGE_ID", "text"=> GetMessage("MAIN_BAD_LANGUAGE_ID_BAD"));
			}
		}

		if(is_set($arFields, "SORT") && strlen($arFields["SORT"])<=0)
		{
			$this->LAST_ERROR .= GetMessage("BAD_SORT")." ";
			$arMsg[] = array("id"=>"SORT", "text"=> GetMessage("BAD_SORT"));
		}
		if(is_set($arFields, "FORMAT_DATE") && strlen($arFields["FORMAT_DATE"])<=0)
		{
			$this->LAST_ERROR .= GetMessage("BAD_FORMAT_DATE")." ";
			$arMsg[] = array("id"=>"FORMAT_DATE", "text"=> GetMessage("BAD_FORMAT_DATE"));
		}
		if(is_set($arFields, "FORMAT_DATETIME") && strlen($arFields["FORMAT_DATETIME"])<=0)
		{
			$this->LAST_ERROR .= GetMessage("BAD_FORMAT_DATETIME")." ";
			$arMsg[] = array("id"=>"FORMAT_DATETIME", "text"=> GetMessage("BAD_FORMAT_DATETIME"));
		}
		if(is_set($arFields, "CHARSET") && strlen($arFields["CHARSET"])<=0)
		{
			$this->LAST_ERROR .= GetMessage("BAD_CHARSET")." ";
			$arMsg[] = array("id"=>"CHARSET", "text"=> GetMessage("BAD_CHARSET"));
		}

/*
		if($ID===false && !is_set($arFields, "TEMPLATE"))
		{
			$this->LAST_ERROR .= GetMessage("MAIN_BAD_TEMPLATE_NA");
			$arMsg[] = array("id"=>"TEMPLATE", "text"=> GetMessage("MAIN_BAD_TEMPLATE_NA"));
		}
*/
		if(is_set($arFields, "TEMPLATE"))
		{
			$isOK = false;
			$check_templ = Array();
			foreach($arFields["TEMPLATE"] as $val)
			{
				if(strlen($val["TEMPLATE"])>0 && file_exists($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".$val["TEMPLATE"]))
				{
					if(in_array($val["TEMPLATE"].", ".$val["CONDITION"], $check_templ))
						$this->LAST_ERROR = GetMessage("MAIN_BAD_TEMPLATE_DUP");
					$check_templ[] = $val["TEMPLATE"].", ".$val["CONDITION"];
					$isOK = true;
				}
			}
			if(!$isOK)
			{
				$this->LAST_ERROR .= GetMessage("MAIN_BAD_TEMPLATE");
				$arMsg[] = array("id"=>"SITE_TEMPLATE", "text"=> GetMessage("MAIN_BAD_TEMPLATE"));
			}
		}

		if(!empty($arMsg))
		{
			$e = new CAdminException($arMsg);
			$GLOBALS["APPLICATION"]->ThrowException($e);
		}

		if(strlen($this->LAST_ERROR)>0)
			return false;

		if($ID===false)
		{
			$r = $DB->Query("SELECT 'x' FROM b_lang WHERE LID='".$DB->ForSQL($arFields["LID"], 2)."'");
			if($r->Fetch())
			{
				$this->LAST_ERROR .= GetMessage("BAD_SITE_DUP")." ";
				$e = new CAdminException(Array(Array("id" => "LID", "text" => GetMessage("BAD_SITE_DUP"))));
				$GLOBALS["APPLICATION"]->ThrowException($e);
				return false;
			}
		}

		return true;
	}

	function Add($arFields)
	{
		global $DB, $DOCUMENT_ROOT, $CACHE_MANAGER;

		if(!$this->CheckFields($arFields))
			return false;
		if(CACHED_b_lang!==false) $CACHE_MANAGER->CleanDir("b_lang");

		if(is_set($arFields, "ACTIVE") && $arFields["ACTIVE"]!="Y")
			$arFields["ACTIVE"]="N";

		if(is_set($arFields, "DEF"))
		{
			if($arFields["DEF"]=="Y")
				$DB->Query("UPDATE b_lang SET DEF='N' WHERE DEF='Y'");
			else
				$arFields["DEF"]="N";
		}

		$arInsert = $DB->PrepareInsert("b_lang", $arFields);

		$strSql =
			"INSERT INTO b_lang(".$arInsert[0].") ".
			"VALUES(".$arInsert[1].")";

		$DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);

		if(is_set($arFields, "DIR"))
			CheckDirPath($DOCUMENT_ROOT.$arFields["DIR"]);

		if(is_set($arFields, "DOMAINS"))
		{
			if(CACHED_b_lang_domain!==false) $CACHE_MANAGER->CleanDir("b_lang_domain");
			$DB->Query("DELETE FROM b_lang_domain WHERE LID='".$DB->ForSQL($arFields["LID"])."'");
			$DOMAINS = str_replace("\r", "\n", $arFields["DOMAINS"]);
			$arDOMAINS = explode("\n", $DOMAINS);
			$bIsDomain = false;
			for($i=0; $i<count($arDOMAINS); $i++)
			{
				if(strlen(trim($arDOMAINS[$i]))>0)
				{
					$DB->Query("INSERT INTO b_lang_domain(LID, DOMAIN) VALUES('".$DB->ForSQL($arFields["LID"])."', '".$DB->ForSQL(trim($arDOMAINS[$i]), 255)."')");
					$bIsDomain = true;
				}
			}
			$strSql = "UPDATE b_lang SET DOMAIN_LIMITED='".($bIsDomain?"Y":"N")."' WHERE LID='".$DB->ForSql($arFields["LID"], 2)."'";
			$DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
		}

		if(is_set($arFields, "TEMPLATE"))
		{
			global $CACHE_MANAGER;
			if(CACHED_b_site_template!==false) $CACHE_MANAGER->Clean("b_site_template");
			$DB->Query("DELETE FROM b_site_template WHERE SITE_ID='".$DB->ForSQL($ID)."'");
			foreach($arFields["TEMPLATE"] as $arTemplate)
			{
				if(strlen(trim($arTemplate["TEMPLATE"]))>0)
				{
					$DB->Query(
						"INSERT INTO b_site_template(SITE_ID, ".CMain::__GetConditionFName().", SORT, TEMPLATE) ".
						"VALUES('".$DB->ForSQL($arFields["LID"])."', '".$DB->ForSQL(trim($arTemplate["CONDITION"]), 255)."', ".IntVal($arTemplate["SORT"]).", '".$DB->ForSQL(trim($arTemplate["TEMPLATE"]), 255)."')");
				}
			}
		}

		return $arFields["LID"];
	}


	function Update($ID, $arFields)
	{
		global $DB, $MAIN_LANGS_CACHE, $MAIN_LANGS_ADMIN_CACHE, $CACHE_MANAGER;
		UnSet($MAIN_LANGS_CACHE[$ID]);
		UnSet($MAIN_LANGS_ADMIN_CACHE[$ID]);

		if(!$this->CheckFields($arFields, $ID))
			return false;
		if(CACHED_b_lang!==false) $CACHE_MANAGER->CleanDir("b_lang");

		if(is_set($arFields, "ACTIVE") && $arFields["ACTIVE"]!="Y")
			$arFields["ACTIVE"]="N";

		if(is_set($arFields, "DEF"))
		{
			if($arFields["DEF"]=="Y")
				$DB->Query("UPDATE b_lang SET DEF='N' WHERE DEF='Y'");
			else
				$arFields["DEF"]="N";
		}

		$strUpdate = $DB->PrepareUpdate("b_lang", $arFields);
		$strSql = "UPDATE b_lang SET ".$strUpdate." WHERE LID='".$DB->ForSql($ID, 2)."'";
		$DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);

		global $BX_CACHE_DOCROOT;
		unset($BX_CACHE_DOCROOT[$ID]);

		if(is_set($arFields, "DIR"))
			CheckDirPath($DOCUMENT_ROOT.$arFields["DIR"]);

		if(is_set($arFields, "DOMAINS"))
		{
			if(CACHED_b_lang_domain!==false) $CACHE_MANAGER->CleanDir("b_lang_domain");
			$DB->Query("DELETE FROM b_lang_domain WHERE LID='".$DB->ForSQL($ID)."'");
			$DOMAINS = str_replace("\r", "\n", $arFields["DOMAINS"]);
			$arDOMAINS = explode("\n", $DOMAINS);
			$bIsDomain = false;
			for($i=0; $i<count($arDOMAINS); $i++)
			{
				if(strlen(trim($arDOMAINS[$i]))>0)
				{
					$DB->Query("INSERT INTO b_lang_domain(LID, DOMAIN) VALUES('".$DB->ForSQL($ID)."', '".$DB->ForSQL(trim($arDOMAINS[$i]), 255)."')");
					$bIsDomain = true;
				}
			}
			$strSql = "UPDATE b_lang SET DOMAIN_LIMITED='".($bIsDomain?"Y":"N")."' WHERE LID='".$DB->ForSql($ID, 2)."'";
			$DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
		}

		if(is_set($arFields, "TEMPLATE"))
		{
			if(CACHED_b_site_template!==false) $CACHE_MANAGER->Clean("b_site_template");
			$DB->Query("DELETE FROM b_site_template WHERE SITE_ID='".$DB->ForSQL($ID)."'");
			foreach($arFields["TEMPLATE"] as $arTemplate)
			{
				if(strlen(trim($arTemplate["TEMPLATE"]))>0)
				{
					$DB->Query(
						"INSERT INTO b_site_template(SITE_ID, ".CMain::__GetConditionFName().", SORT, TEMPLATE) ".
						"VALUES('".$DB->ForSQL($ID)."', '".$DB->ForSQL(trim($arTemplate["CONDITION"]), 255)."', ".IntVal($arTemplate["SORT"]).", '".$DB->ForSQL(trim($arTemplate["TEMPLATE"]), 255)."')");
				}
			}
		}

		return true;
	}

	function Delete($ID)
	{
		global $DB, $APPLICATION, $CACHE_MANAGER;

		$APPLICATION->ResetException();
		//проверка - оставил ли тут кто-нибудь обработчик на OnBeforeDelete
		$db_events = GetModuleEvents("main", "OnBeforeLangDelete");
		while($arEvent = $db_events->Fetch())
			if(ExecuteModuleEvent($arEvent, $ID)===false)
			{
				$err = GetMessage("MAIN_BEFORE_DEL_ERR").' '.$arEvent['TO_NAME'];
				if($ex = $APPLICATION->GetException())
					$err .= ': '.$ex->GetString();
				$APPLICATION->throwException($err);
				return false;
			}

		$db_events = GetModuleEvents("main", "OnBeforeSiteDelete");
		while($arEvent = $db_events->Fetch())
			if(ExecuteModuleEvent($arEvent, $ID)===false)
			{
				$err = GetMessage("MAIN_BEFORE_DEL_ERR").' '.$arEvent['TO_NAME'];
				if($ex = $APPLICATION->GetException())
					$err .= ': '.$ex->GetString();
				$APPLICATION->throwException($err);
				return false;
			}

		//проверка - оставил ли тут какой-нибудь модуль обработчик на OnDelete
		$events = GetModuleEvents("main", "OnLangDelete");
		while($arEvent = $events->Fetch())
			ExecuteModuleEvent($arEvent, $ID);

		$events = GetModuleEvents("main", "OnSiteDelete");
		while($arEvent = $events->Fetch())
			ExecuteModuleEvent($arEvent, $ID);

		if(!$DB->Query("DELETE FROM b_event_message_site WHERE SITE_ID='".$DB->ForSQL($ID, 2)."'"))
			return false;

		if(!$DB->Query("DELETE FROM b_lang_domain WHERE LID='".$DB->ForSQL($ID, 2)."'"))
			return false;
		if(CACHED_b_lang_domain!==false) $CACHE_MANAGER->CleanDir("b_lang_domain");

		if(!$DB->Query("UPDATE b_event_message SET LID=NULL WHERE LID='".$DB->ForSQL($ID, 2)."'"))
			return false;

		if(!$DB->Query("DELETE FROM b_site_template WHERE SITE_ID='".$DB->ForSQL($ID, 2)."'"))
			return false;
		if(CACHED_b_site_template!==false) $CACHE_MANAGER->Clean("b_site_template");

		if(CACHED_b_lang!==false) $CACHE_MANAGER->CleanDir("b_lang");
		return $DB->Query("DELETE FROM b_lang WHERE LID='".$DB->ForSQL($ID, 2)."'", true);
	}

	function GetTemplateList($site_id)
	{
		global $DB;
		$strSql =
				"SELECT * ".
				"FROM b_site_template ".
				"WHERE SITE_ID='".$DB->ForSQL($site_id, 2)."' ".
				"ORDER BY SORT";

		$dbr = $DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
		return $dbr;
	}

	///////////////////////////////////////////////////////////////////
	//Функция выборки списка сайтов в порядке приоритета
	///////////////////////////////////////////////////////////////////
	function GetDefList()
	{
		global $DB;
		$strSql = "SELECT L.*, L.LID as ID, L.LID as SITE_ID FROM b_lang L WHERE ACTIVE='Y' ORDER BY DEF desc, SORT";
		$sl = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		return $sl;
	}

	function GetSiteDocRoot($site)
	{
		if($site === false)
			$site = SITE_ID;

		global $BX_CACHE_DOCROOT;
		if(!is_set($BX_CACHE_DOCROOT, $site))
		{
			$res = CSite::GetByID($site);
			if(($ar = $res->Fetch()) && strlen($ar["DOC_ROOT"])>0)
			{
				$BX_CACHE_DOCROOT[$site] = Rel2Abs($_SERVER["DOCUMENT_ROOT"], $ar["DOC_ROOT"]);
			}
			else
				$BX_CACHE_DOCROOT[$site] = RTrim($_SERVER["DOCUMENT_ROOT"], "/\\");
		}

		return $BX_CACHE_DOCROOT[$site];
	}

	function GetSiteByFullPath($path, $bOneResult = true)
	{
		$res = Array();

		if(realpath($path))
			$path = realpath($path);
		$path = str_replace("\\", "/", $path);
		$path = strtoupper($path);

		$db_res = CSite::GetList($by="lendir", $order="desc");
		while($ar_res = $db_res->Fetch())
		{
			$abspath = $ar_res["ABS_DOC_ROOT"].$ar_res["DIR"];
			if(realpath($abspath))
				$abspath = realpath($abspath);
			$abspath = str_replace("\\", "/", $abspath);
			$abspath = strtoupper($abspath);
			if(strpos($path, $abspath)===0)
			{
				if($bOneResult)
					return $ar_res["ID"];
				$res[] = $ar_res["ID"];
			}
		}

		if(count($res)>0)
			return $res;

		return false;
	}

	///////////////////////////////////////////////////////////////////
	//Функция выборки списка языков
	///////////////////////////////////////////////////////////////////
	function GetList(&$by, &$order, $arFilter=Array())
	{
		global $DB, $CACHE_MANAGER;

		if(CACHED_b_lang!==false)
		{
			$cacheId = "b_lang".md5($by.".".$order.".".serialize($arFilter));
			if($CACHE_MANAGER->Read(CACHED_b_lang, $cacheId, "b_lang"))
			{
				$arResult = $CACHE_MANAGER->Get($cacheId);

				$res = new CDBResult;
				$res->InitFromArray($arResult);
				$res = new _CLangDBResult($res);
				return $res;
			}
		}

		$strSqlSearch = " 1=1\n";
		$bIncDomain = false;
		if(is_array($arFilter))
		{
			foreach($arFilter as $key=>$val)
			{
				if(strlen($val)<=0) continue;
				$val = $DB->ForSql($val);
				switch(strtoupper($key))
				{
					case "ACTIVE":
						if($val=="Y" || $val=="N")
							$strSqlSearch .= " AND L.ACTIVE='".$val."'\n";
						break;
					case "DEFAULT":
						if($val=="Y" || $val=="N")
							$strSqlSearch .= " AND L.DEF='".$val."'\n";
						break;
					case "NAME":
						$strSqlSearch .= " AND UPPER(L.NAME) LIKE UPPER('".$val."')\n";
						break;
					case "DOMAIN":
						$bIncDomain = true;
						$strSqlSearch .= " AND UPPER(D.DOMAIN) LIKE UPPER('".$val."')\n";
						break;
					case "IN_DIR":
						$strSqlSearch .= " AND UPPER('".$val."') LIKE ".$DB->Concat("UPPER(L.DIR)", "'%'")."\n";
						break;
					case "ID":
					case "LID":
						$strSqlSearch .= " AND L.LID='".$val."'\n";
						break;
					case "LANGUAGE_ID":
						$strSqlSearch .= " AND L.LANGUAGE_ID='".$val."'\n";
						break;
				}
			}
		}

		$strSql = "
			SELECT ".($bIncDomain ? " DISTINCT " : "")."
				L.*,
				L.LID ID,
				".$DB->Length("L.DIR")."
			FROM
				b_lang L
				".($bIncDomain ? " LEFT JOIN b_lang_domain D ON D.LID=L.LID " : "")."
			WHERE
				".$strSqlSearch."
			";
		$by = strtolower($by);
		if($by == "lid" || $by=="id")	$strSqlOrder = " ORDER BY L.LID ";
		elseif($by == "active")			$strSqlOrder = " ORDER BY L.ACTIVE ";
		elseif($by == "name")			$strSqlOrder = " ORDER BY L.NAME ";
		elseif($by == "dir")			$strSqlOrder = " ORDER BY L.DIR ";
		elseif($by == "lendir")			$strSqlOrder = " ORDER BY ".$DB->Length("L.DIR");
		elseif($by == "def")			$strSqlOrder = " ORDER BY L.DEF ";
		else
		{
			$strSqlOrder = " ORDER BY L.SORT ";
			$by = "sort";
		}

		$order = strtolower($order);
		if($order=="desc")
			$strSqlOrder .= " desc ";
		else
			$order = "asc";

		$strSql .= $strSqlOrder;
		if(CACHED_b_lang===false)
		{
			$res = $DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
		}
		else
		{
			$arResult = array();
			$res = $DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
			while($ar = $res->Fetch())
				$arResult[]=$ar;

			$CACHE_MANAGER->Set($cacheId, $arResult);

			$res = new CDBResult;
			$res->InitFromArray($arResult);
		}
		//echo "<pre>".$strSql."</pre>";
		$res = new _CLangDBResult($res);
		return $res;
	}

	///////////////////////////////////////////////////////////////////
	//Функция выборки одного языка по коду
	///////////////////////////////////////////////////////////////////
	function GetByID($ID)
	{
		return CSite::GetList($ord, $by, Array("LID"=>$ID));
	}

	function GetDefSite($LID = false)
	{
		if(strlen($LID)>0)
		{
			$dbSite = CSite::GetByID($LID);
			if($dbSite->Fetch())
				return $LID;
		}

		$dbDefSites = CSite::GetDefList();
		if($arDefSite = $dbDefSites->Fetch())
			return $arDefSite["LID"];

		return false;
	}

	function IsDistinctDocRoots($arFilter=Array())
	{
		$s = false;
		$res = CSite::GetList($by, $order, $arFilter);
		while($ar = $res->Fetch())
		{
			if($s!==false && $s!=$ar["ABS_DOC_ROOT"])
				return true;
			$s = $ar["ABS_DOC_ROOT"];
		}
		return false;
	}


	///////////////////////////////////////////////////////////////////
	// Returns drop down list with langs
	///////////////////////////////////////////////////////////////////
	function SelectBox($sFieldName, $sValue, $sDefaultValue="", $sFuncName="", $field="class=\"typeselect\"")
	{
		$l = CLang::GetList(($by="sort"), ($order="asc"));
		$s = '<select name="'.$sFieldName.'" '.$field;
		if(strlen($sFuncName)>0) $s .= ' OnChange="'.$sFuncName.'"';
		$s .= '>'."\n";
		$found = false;
		while(($l_arr = $l->Fetch()))
		{
			$found = ($l_arr["LID"] == $sValue);
			$s1 .= '<option value="'.$l_arr["LID"].'"'.($found ? ' selected':'').'>['.htmlspecialcharsex($l_arr["LID"]).']&nbsp;'.htmlspecialcharsex($l_arr["NAME"]).'</option>'."\n";
		}
		if(strlen($sDefaultValue)>0)
			$s .= "<option value='NOT_REF' ".($found ? "" : "selected").">".htmlspecialcharsex($sDefaultValue)."</option>";
		return $s.$s1.'</select>';
	}

	function SelectBoxMulti($sFieldName, $Value)
	{
		$l = CLang::GetList(($by="sort"), ($order="asc"));
		if(is_array($Value))
			$arValue = $Value;
		else
			$arValue = Array($Value);

		$s = '';
		while($l_arr = $l->Fetch())
		{
			$s .=
				'<input type="checkbox" name="'.$sFieldName.'[]" value="'.htmlspecialcharsex($l_arr["LID"]).'" id="'.htmlspecialcharsex($l_arr["LID"]).'" class="typecheckbox"'.(in_array($l_arr["LID"], $arValue)?' checked':'').'>'.
				'<label for="'.htmlspecialcharsex($l_arr["LID"]).'">['.htmlspecialcharsex($l_arr["LID"]).']&nbsp;'.htmlspecialcharsex($l_arr["NAME"]).'</label>'.
				'<br>';
		}

		return $s;
	}
}

class _CLangDBResult extends CDBResult
{

	function _CLangDBResult($res)
	{
		parent::CDBResult($res);
	}

	function Fetch()
	{
		if($res = parent::Fetch())
		{
			global $DB, $CACHE_MANAGER;
			static $arCache;
			if(!is_array($arCache))
				$arCache = Array();
			if(is_set($arCache, $res["LID"]))
				 $res["DOMAINS"] = $arCache[$res["LID"]];
			else
			{
				if(CACHED_b_lang_domain===false)
				{
					$res["DOMAINS"] = "";
					$db_res = $DB->Query("SELECT * FROM b_lang_domain WHERE LID='".$res["LID"]."'");
					while($ar_res = $db_res->Fetch())
						$res["DOMAINS"] .= $ar_res["DOMAIN"]."\r\n";
				}
				else
				{
					if($CACHE_MANAGER->Read(CACHED_b_lang_domain, "b_lang_domain", "b_lang_domain"))
					{
						$arLangDomain = $CACHE_MANAGER->Get("b_lang_domain");
					}
					else
					{
						$arLangDomain = array("DOMAIN"=>array(), "LID"=>array());
						$rs = $DB->Query("SELECT * FROM b_lang_domain ORDER BY ".$DB->Length("DOMAIN"));
						while($ar = $rs->Fetch())
						{
							$arLangDomain["DOMAIN"][]=$ar;
							$arLangDomain["LID"][$ar["LID"]][]=$ar;
						}
						$CACHE_MANAGER->Set("b_lang_domain", $arLangDomain);
					}
					$res["DOMAINS"] = "";
					if(is_array($arLangDomain["LID"][$res["LID"]]))
						foreach($arLangDomain["LID"][$res["LID"]] as $ar_res)
							$res["DOMAINS"] .= $ar_res["DOMAIN"]."\r\n";
				}
				$res["DOMAINS"] = Trim($res["DOMAINS"]);
				$arCache[$res["LID"]] = $res["DOMAINS"];
			}

			if(trim($res["DOC_ROOT"])=="")
				$res["ABS_DOC_ROOT"] = $_SERVER["DOCUMENT_ROOT"];
			else
				$res["ABS_DOC_ROOT"] = Rel2Abs($_SERVER["DOCUMENT_ROOT"], $res["DOC_ROOT"]);

			if($res["ABS_DOC_ROOT"]!==$_SERVER["DOCUMENT_ROOT"])
				$res["SITE_URL"] = (CMain::IsHTTPS() ? "https://" : "http://").$res["SERVER_NAME"];
		}
		return $res;
	}

}

class CAllLanguage
{
	///////////////////////////////////////////////////////////////////
	//Функция выборки списка языков
	///////////////////////////////////////////////////////////////////
	function GetList(&$by, &$order, $arFilter=Array())
	{
		global $DB;
		$arSqlSearch = Array();

		if(!is_array($arFilter))
			$filter_keys = Array();
		else
			$filter_keys = array_keys($arFilter);

		for($i=0; $i<count($filter_keys); $i++)
		{
			$val = $DB->ForSql($arFilter[$filter_keys[$i]]);
			if(strlen($val)<=0) continue;
			switch(strtoupper($filter_keys[$i]))
			{
			case "ACTIVE":
				if($val=="Y" || $val=="N")
					$arSqlSearch[] = "L.ACTIVE='".$val."'";
				break;
			case "NAME":
				$arSqlSearch[] = "UPPER(L.NAME) LIKE UPPER('".$val."')";
				break;
			case "ID":
			case "LID":
				$arSqlSearch[] = "L.LID='".$val."'";
				break;
			}
		}

		$strSqlSearch = "";
		for($i=0; $i<count($arSqlSearch); $i++)
		{
			if($i>0)
				$strSqlSearch .= " AND ";
			else
				$strSqlSearch = " WHERE ";

			$strSqlSearch .= " (".$arSqlSearch[$i].") ";
		}

		$strSql =
			"SELECT L.*, L.LID as ID, L.LID as LANGUAGE_ID ".
			"FROM b_language L ".
				$strSqlSearch;

		if($by == "lid" || $by=="id")	$strSqlOrder = " ORDER BY L.LID ";
		elseif($by == "active")			$strSqlOrder = " ORDER BY L.ACTIVE ";
		elseif($by == "name")			$strSqlOrder = " ORDER BY L.NAME ";
		elseif($by == "def")			$strSqlOrder = " ORDER BY L.DEF ";
		else
		{
			$strSqlOrder = " ORDER BY L.SORT ";
			$by = "sort";
		}

		if($order=="desc")
			$strSqlOrder .= " desc ";
		else
			$order = "asc";

		$strSql .= $strSqlOrder;
		$res = $DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);

		return $res;
	}

	///////////////////////////////////////////////////////////////////
	//Функция выборки одного языка по коду
	///////////////////////////////////////////////////////////////////
	function GetByID($ID)
	{
		return CLanguage::GetList($o, $b, Array("LID"=>$ID));
	}

	function CheckFields($arFields, $ID=false)
	{
		global $DB;
		$this->LAST_ERROR = "";
		$arMsg = Array();

		if(is_set($arFields, "NAME") && strlen($arFields["NAME"])<2)
		{
			$this->LAST_ERROR .= GetMessage("BAD_LANG_NAME")." ";
			$arMsg[] = array("id"=>"NAME", "text"=> GetMessage("BAD_LANG_NAME"));
		}
		if($ID===false && is_set($arFields, "LID") && strlen($arFields["LID"])!=2)
		{
			$this->LAST_ERROR .= GetMessage("BAD_LANG_LID")." ";
			$arMsg[] = array("id"=>"LID", "text"=> GetMessage("BAD_LANG_LID"));
		}
		if($ID===false && is_set($arFields, "SORT") && intval($arFields["SORT"])<=0)
		{
			$this->LAST_ERROR .= GetMessage("BAD_LANG_SORT")." ";
			$arMsg[] = array("id"=>"SORT", "text"=> GetMessage("BAD_LANG_SORT"));
		}
		if($ID===false && is_set($arFields, "FORMAT_DATE") && strlen($arFields["FORMAT_DATE"])<2)
		{
			$this->LAST_ERROR .= GetMessage("BAD_LANG_FORMAT_DATE")." ";
			$arMsg[] = array("id"=>"FORMAT_DATE", "text"=> GetMessage("BAD_LANG_FORMAT_DATE"));
		}
		if($ID===false && is_set($arFields, "FORMAT_DATETIME") && strlen($arFields["FORMAT_DATETIME"])<2)
		{
			$this->LAST_ERROR .= GetMessage("BAD_LANG_FORMAT_DATETIME")." ";
			$arMsg[] = array("id"=>"FORMAT_DATETIME", "text"=> GetMessage("BAD_LANG_FORMAT_DATETIME"));
		}
		if($ID===false && is_set($arFields, "CHARSET") && strlen($arFields["CHARSET"])<2)
		{
			$this->LAST_ERROR .= GetMessage("BAD_LANG_CHARSET")." ";
			$arMsg[] = array("id"=>"CHARSET", "text"=> GetMessage("BAD_LANG_CHARSET"));
		}

		if(!empty($arMsg))
		{
			$e = new CAdminException($arMsg);
			$GLOBALS["APPLICATION"]->ThrowException($e);
		}

		if(strlen($this->LAST_ERROR)>0)
			return false;

		if($ID===false)
		{
			$r = $DB->Query("SELECT 'x' FROM b_language WHERE LID='".$DB->ForSQL($arFields["LID"], 2)."'");
			if($r->Fetch())
			{
				$this->LAST_ERROR .= GetMessage("BAD_LANG_DUP")." ";
				$e = new CAdminException(Array(Array("id"=>"LID", "text" =>GetMessage("BAD_LANG_DUP"))));
				$GLOBALS["APPLICATION"]->ThrowException($e);
				return false;
			}
		}

		return true;
	}

	function Add($arFields)
	{
		global $DB, $DOCUMENT_ROOT;

		if(!$this->CheckFields($arFields))
			return false;

		if(is_set($arFields, "ACTIVE") && $arFields["ACTIVE"]!="Y")
			$arFields["ACTIVE"]="N";

		if(is_set($arFields, "DIRECTION") && $arFields["DIRECTION"]!="Y")
			$arFields["DIRECTION"]="N";

		$arInsert = $DB->PrepareInsert("b_language", $arFields);

		if(is_set($arFields, "DEF"))
		{
			if($arFields["DEF"]=="Y")
				$DB->Query("UPDATE b_language SET DEF='N' WHERE DEF='Y'");
			else
				$arFields["DEF"]="N";
		}

		$strSql =
			"INSERT INTO b_language(".$arInsert[0].") ".
			"VALUES(".$arInsert[1].")";
		$DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
		return $arFields["LID"];
	}


	function Update($ID, $arFields)
	{
		global $DB, $MAIN_LANGS_CACHE, $MAIN_LANGS_ADMIN_CACHE;
		UnSet($MAIN_LANGS_CACHE[$ID]);
		UnSet($MAIN_LANGS_ADMIN_CACHE[$ID]);

		if(!$this->CheckFields($arFields, $ID))
			return false;

		if(is_set($arFields, "ACTIVE") && $arFields["ACTIVE"]!="Y")
			$arFields["ACTIVE"]="N";

		if(is_set($arFields, "DIRECTION") && $arFields["DIRECTION"]!="Y")
			$arFields["DIRECTION"]="N";

		if(is_set($arFields, "DEF"))
		{
			if($arFields["DEF"]=="Y")
				$DB->Query("UPDATE b_language SET DEF='N' WHERE DEF='Y'");
			else
				$arFields["DEF"]="N";
		}

		$strUpdate = $DB->PrepareUpdate("b_language", $arFields);
		$strSql = "UPDATE b_language SET ".$strUpdate." WHERE LID='".$DB->ForSql($ID, 2)."'";
		$DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);

		return true;
	}

	function Delete($ID)
	{
		global $DB;

		$db_res = CLang::GetList(($b=""), ($o=""), Array("LANGUAGE_ID" => $ID));
		if($db_res->Fetch())
			return false;

		//проверка - оставил ли тут кто-нибудь обработчик на OnBeforeDelete
		$bCanDelete = true;
		$db_events = GetModuleEvents("main", "OnBeforeLanguageDelete");
		while($arEvent = $db_events->Fetch())
			if(ExecuteModuleEvent($arEvent, $ID)===false)
			{
				$err = GetMessage("MAIN_BEFORE_DEL_ERR").' '.$arEvent['TO_NAME'];
				if($ex = $APPLICATION->GetException())
					$err .= ': '.$ex->GetString();
				$APPLICATION->throwException($err);
				return false;
			}

		//проверка - оставил ли тут какой-нибудь модуль обработчик на OnDelete
		$events = GetModuleEvents("main", "OnLanguageDelete");
		while($arEvent = $events->Fetch())
			ExecuteModuleEvent($arEvent, $ID);

		return $DB->Query("DELETE FROM b_language WHERE LID='".$DB->ForSQL($ID, 2)."'", true);
	}

	///////////////////////////////////////////////////////////////////
	//Функция строит выпадающий список языков
	///////////////////////////////////////////////////////////////////
	function SelectBox($sFieldName, $sValue, $sDefaultValue="", $sFuncName="", $field="class=\"typeselect\"")
	{
		$l = CLanguage::GetList(($by="sort"), ($order="asc"));
		$s = '<select name="'.$sFieldName.'" '.$field;
		if(strlen($sFuncName)>0) $s .= ' OnChange="'.$sFuncName.'"';
		$s .= '>'."\n";
		$found = false;
		while(($l_arr = $l->Fetch()))
		{
			$found = ($l_arr["LID"] == $sValue);
			$s1 .= '<option value="'.$l_arr["LID"].'"'.($found ? ' selected':'').'>['.htmlspecialcharsex($l_arr["LID"]).']&nbsp;'.htmlspecialcharsex($l_arr["NAME"]).'</option>'."\n";
		}
		if(strlen($sDefaultValue)>0)
			$s .= "<option value='' ".($found ? "" : "selected").">".htmlspecialcharsex($sDefaultValue)."</option>";
		return $s.$s1.'</select>';
	}

	function GetLangSwitcherArray()
	{
		global $DB, $REQUEST_URI, $DOCUMENT_ROOT, $APPLICATION;

		$result = Array();
		$db_res = $DB->Query("SELECT * FROM b_language WHERE ACTIVE='Y' ORDER BY SORT");
		while($ar = $db_res->Fetch())
		{
			$ar["NAME"] = htmlspecialchars($ar["NAME"]);
			$ar["SELECTED"] = ($ar["LID"]==LANG);

			global $QUERY_STRING;
			$p = ereg_replace("lang=[^&]*&*", "", $QUERY_STRING);
			$ar["PATH"] = $APPLICATION->GetCurPage()."?lang=".$ar["LID"]."&amp;".htmlspecialchars($p);

			$result[] = $ar;
		}
		return $result;
	}
}

class CLanguage extends CAllLanguage
{
}

class CLangAdmin extends CLanguage
{
}

$SHOWIMAGEFIRST=false;

function ShowImage($PICTURE_ID, $iMaxW=0, $iMaxH=0, $sParams="border=0", $strImageUrl="", $bPopup=false, $strPopupTitle=false,$iSizeWHTTP=0, $iSizeHHTTP=0)
{
	return CFile::ShowImage($PICTURE_ID, $iMaxW, $iMaxH, $sParams, $strImageUrl, $bPopup, $strPopupTitle,$iSizeWHTTP, $iSizeHHTTP);
}


class CAllFilterQuery
{
	var $cnt = 0;
	var $m_query;
	var $m_words;
	var $m_fields;
	var $m_kav;
	var $default_query_type;
	var $rus_bool_lang;
	var $error;
	var $procent;
	var $ex_sep;
	var $clob;
	var $div_fields;

	function __construct($default_query_type = "and", $rus_bool_lang = "yes", $procent="Y", $ex_sep = array(), $clob="N", $div_fields="Y", $clob_upper="N")
	{
		$this->CFilterQuery($default_query_type, $rus_bool_lang, $procent, $ex_sep, $clob, $div_fields, $clob_upper);
	}

	/*
	$default_query_type - логика для пробелов по умолчанию
	$rus_bool_lang - разрешать ли русскую логику - не, и, или
	$ex_sep - массив символов которые НЕ надо считать разделителями слов
	*/
	function CFilterQuery($default_query_type = "and", $rus_bool_lang = "yes", $procent="Y", $ex_sep = array(), $clob="N", $div_fields="Y", $clob_upper="N")
	{
		$this->m_query  = "";
		$this->m_fields = "";
		$this->default_query_type = $default_query_type;
		$this->rus_bool_lang = $rus_bool_lang;
		$this->m_kav = array();
		$this->error = "";
		$this->procent = $procent;
		$this->ex_sep = $ex_sep;
		$this->clob = $clob;
		$this->clob_upper = $clob_upper;
		$this->div_fields = $div_fields;
	}

	function GetQueryString($fields, $query)
	{
		$this->m_words = Array();
		if($this->div_fields=="Y")
			$this->m_fields = explode(",", $fields);
		else
			$this->m_fields = $fields;
		if(!is_array($this->m_fields))
			$this->m_fields=array($this->m_fields);

		$query = $this->CutKav($query);
		$query = $this->ParseQ($query);
		if($query == "( )" || strlen($query)<=0)
		{
			$this->error=GetMessage("FILTER_ERROR3");
			$this->errorno=3;
			return false;
		}
		$query = $this->PrepareQuery($query);

		return $query;
	}

	function CutKav($query)
	{
		$bdcnt = 0;
		while (eregi("\"([^\"]*)\"",$query,$pt))
		{
			$res = $pt[1];
			if(strlen(trim($pt[1]))>0)
			{
				$trimpt = $bdcnt."cut5";
				$this->m_kav[$trimpt] = $res;
				$query = str_replace("\"".$pt[1]."\"", " ".$trimpt." ", $query);
			}
			else
			{
				$query = str_replace("\"".$pt[1]."\"", " ", $query);
			}
			$bdcnt++;
			if($bdcnt>100) break;
		}

		$bdcnt = 0;
		while (eregi("'([^']*)'",$query,$pt))
		{
			$res = $pt[1];
			if(strlen(trim($pt[1]))>0)
			{
				$trimpt = $bdcnt."cut6";
				$this->m_kav[$trimpt] = $res;
				$query = str_replace("'".$pt[1]."'", " ".$trimpt." ", $query);
			}
			else
			{
				$query = str_replace("'".$pt[1]."'", " ", $query);
			}
			$bdcnt++;
			if($bdcnt>100) break;
		}
		return $query;
	}

	function ParseQ($q)
	{
		if(ereg_replace(" ","",$q)=='')
			return '';

		$q=$this->ParseStr($q);

		$q=eregi_replace("\&"," && ",$q);
		$q=eregi_replace("\|"," || ",$q);
		$q=eregi_replace("\~"," ! ",$q);
		$q=ereg_replace("\("," ( ",$q);
		$q=ereg_replace("\)"," ) ",$q);
		$q="( $q )";
		$q=ereg_replace(" {1,}"," ",$q);

		return $q;
	}

	function ParseStr($qwe)
	{
		$sep = array();

		/*
		// если нужно искать вхождение то
		if ($this->procent=="Y")
		{
			// определим массив разделителей
			$sep = array("!", "@", "#", "$", "^", "*", "=", "\\", "{", "}", "[", "]", ";", "'", ":", "\"", "<", ">", "?", "/", ",", ".");
			$str = "";
			foreach ($sep as $s)
			{
				if(is_array($this->ex_sep) && !in_array($s, $this->ex_sep)) $str .= "\\".$s;
			}
			if(strlen($str)>0)
				$qwe = preg_replace("/[".$str."]{1,}/"," ",$qwe);
		}
		*/

		$qwe=trim($qwe);

		$qwe=eregi_replace(" {0,}\| {0,}","|",$qwe);
		$qwe=eregi_replace(" {0,}\+ {0,}","&",$qwe);
		$qwe=eregi_replace(" {0,}\~ {0,}","~",$qwe);

		$qwe=ereg_replace(" {0,}\( {0,}","(",$qwe);
		$qwe=ereg_replace(" {0,}\) {0,}",")",$qwe);

		// default query type is and
		if(strtolower($this->default_query_type) == 'or')
		{
			$qwe=ereg_replace(" {1,}","|",$qwe);
			$qwe=ereg_replace("\&\|{1,}","|",$qwe);
			$qwe=ereg_replace("\|\&{1,}","|",$qwe);
		}
		else
		{
			$qwe=ereg_replace(" {1,}","&",$qwe);
			$qwe=ereg_replace("\&\|{1,}","&",$qwe);
			$qwe=ereg_replace("\|\&{1,}","&",$qwe);
		}

		// remove unnesessary boolean operators
		$qwe=ereg_replace("\|{1,}","|",$qwe);
		$qwe=ereg_replace("&{1,}","&",$qwe);
		$qwe=ereg_replace("~{1,}","~",$qwe);
		$qwe=ereg_replace("\|\&\|","&",$qwe);
		$qwe=ereg_replace("[\|\&\~]{1,}$","",$qwe);
		$qwe=ereg_replace("^[\|\&]{1,}","",$qwe);

		// transform "w1 ~w2" -> "w1 default_op ~ w2"
		// ") ~w" -> ") default_op ~w"
		// "w ~ (" -> "w default_op ~("
		// ") w" -> ") default_op w"
		// "w (" -> "w default_op ("
		// ")(" -> ") default_op ("
		if(strtolower($this->default_query_type) == 'or')
		{
			$qwe=ereg_replace("([^\&\~\|\(\)]+)~([^\&\~\|\(\)]+)","\\1|~\\2",$qwe);
			$qwe=ereg_replace("\)~{1,}",")|~",$qwe);
			$qwe=ereg_replace("~{1,}\(","~|(",$qwe);
			$qwe=ereg_replace("\)([^\&\~\|\(\)]+)",")|\\1",$qwe);
			$qwe=ereg_replace("([^\&\~\|\(\)]+)\(","\\1|(",$qwe);
			$qwe=ereg_replace("\) *\(",")|(",$qwe);
		}
		else
		{
			$qwe=ereg_replace("([^\&\~\|\(\)]+)~([^\&\~\|\(\)]+)","\\1&~\\2",$qwe);
			$qwe=ereg_replace("\)~{1,}",")&~",$qwe);
			$qwe=ereg_replace("~{1,}\(","&~(",$qwe);
			$qwe=ereg_replace("\)([^\&\~\|\(\)]+)",")&\\1",$qwe);
			$qwe=ereg_replace("([^\&\~\|\(\)]+)\(","\\1&(",$qwe);
			$qwe=ereg_replace("\) *\(",")&(",$qwe);
		}

		// remove unnesessary boolean operators
		$qwe=ereg_replace("\|{1,}","|",$qwe);
		$qwe=ereg_replace("&{1,}","&",$qwe);

		// remove errornous format of query - ie: '(&', '&)', '(|', '|)', '~&', '~|', '~)'
		$qwe=ereg_replace("\(\&{1,}","(",$qwe);
		$qwe=ereg_replace("\&{1,}\)",")",$qwe);
		$qwe=ereg_replace("\~{1,}\)",")",$qwe);
		$qwe=ereg_replace("\(\|{1,}","(",$qwe);
		$qwe=ereg_replace("\|{1,}\)",")",$qwe);
		$qwe=ereg_replace("\~{1,}\&{1,}","&",$qwe);
		$qwe=ereg_replace("\~{1,}\|{1,}","|",$qwe);

		$qwe=ereg_replace("^[\|\&]{1,}","",$qwe);
		$qwe=ereg_replace("[\|\&\~]{1,}$","",$qwe);
		$qwe=ereg_replace("\|\&","&",$qwe);
		$qwe=ereg_replace("\&\|","|",$qwe);

		return($qwe);
	}

	function PrepareQuery($q)
	{
		$state = 0;
		$qu = "";
		$n = 0;
		$this->error = "";

		$t=strtok($q," ");

		while (($t!="") && ($this->error==""))
		{
			switch ($state)
			{
			case 0:
				if(($t=="||") || ($t=="&&") || ($t==")"))
				{
					$this->error=GetMessage("FILTER_ERROR2")." ".$t;
					$this->errorno=2;
				}
				elseif($t=="!")
				{
					$state=0;
					$qu="$qu NOT ";
					break;
				}
				elseif($t=="(")
				{
					$n++;
					$state=0;
					$qu="$qu(";
				}
				else
				{
					$state=1;
					$qu="$qu ".$this->BuildWhereClause($t)." ";
				}
				break;

			case 1:
				if(($t=="||") || ($t=="&&"))
				{
					$state=0;
					if($t=='||') $qu="$qu OR ";
					else $qu="$qu AND ";
				}
				elseif($t==")")
				{
					$n--;
					$state=1;
					$qu="$qu)";
				}
				else
				{
					$this->error=GetMessage("FILTER_ERROR2")." ".$t;
					$this->errorno=2;
				}
				break;
			}
			$t=strtok(" ");
		}

		if(($this->error=="") && ($n != 0))
		{
			$this->error=GetMessage("FILTER_ERROR1");
			$this->errorno=1;
		}
		if($this->error!="") return 0;

		return $qu;
	}
}

class CAllLang extends CAllSite
{
}

class CSiteTemplate
{
	function GetList($arOrder = Array(), $arFilter = Array())
	{
		global $APPLICATION;
		$arRes = Array();
		$path = $_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates";
		$handle  = @opendir($path);
		while(false !== ($file = @readdir($handle)))
		{
			if($file == "." || $file == ".." || !is_dir($path."/".$file)) continue;
			$arTemplate = Array("DESCRIPTION"=>"");
			//if(strlen($arFilter["ID"])>0 && $arFilter["ID"]!=$file) continue;
			if(is_set($arFilter, "ID") && $arFilter["ID"]!=$file) continue;
			if(file_exists($path."/".$file."/description.php"))
			{
				include($path."/".$file."/description.php");
			}
			if($file==".default")
			{
				continue;
				$arTemplate["DEFAULT"] = "Y";
			}
			else
				$arTemplate["DEFAULT"] = "N";

			$arTemplate["ID"]=$file;
			if(!is_set($arTemplate, "NAME"))
				$arTemplate["NAME"]=$file;
			if(file_exists($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".$file."/screen.gif"))
				$arTemplate["SCREENSHOT"] = BX_PERSONAL_ROOT."/templates/".$file."/screen.gif";
			else
				$arTemplate["SCREENSHOT"] = false;
			$arTemplate["CONTENT"] = $APPLICATION->GetFileContent($path."/".$file."/header.php")."#WORK_AREA#".$APPLICATION->GetFileContent($path."/".$file."/footer.php");

			if(file_exists($path."/".$file."/styles.css"))
			{
				$arTemplate["STYLES"] = $APPLICATION->GetFileContent($path."/".$file."/styles.css");
				$arTemplate["STYLES_TITLE"] = CSiteTemplate::__GetByStylesTitle($path."/".$file."/.styles.php");
			}

			if(file_exists($path."/".$file."/template_styles.css"))
				$arTemplate["TEMPLATE_STYLES"] = $APPLICATION->GetFileContent($path."/".$file."/template_styles.css");

			$arRes[] = $arTemplate;
		}
		$db_res = new CDBResult;
		$db_res->InitFromArray($arRes);

		return $db_res;
	}

	function __GetByStylesTitle($file)
	{
		if(file_exists($file))
			return include($file);
		return false;
	}

	function GetByID($ID)
	{
		return CSiteTemplate::GetList(array(), array("ID"=>$ID));
	}

	function CheckFields($arFields, $ID=false)
	{
		global $DB;
		$this->LAST_ERROR = "";
		$arMsg = Array();

		if($ID===false)
		{
			if(strlen($arFields["ID"])<=0)
				$this->LAST_ERROR .= GetMessage("MAIN_ENTER_TEMPLATE_ID")." ";
			elseif(file_exists($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".$arFields["ID"]))
				$this->LAST_ERROR .= GetMessage("MAIN_TEMPLATE_ID_EX")." ";

			if(!is_set($arFields, "CONTENT"))
				$this->LAST_ERROR .= GetMessage("MAIN_TEMPLATE_CONTENT_NA")." ";
		}

		if(is_set($arFields, "CONTENT") && strlen($arFields["CONTENT"])<=0)
		{
			$this->LAST_ERROR .= GetMessage("MAIN_TEMPLATE_CONTENT_NA")." ";
			$arMsg[] = array("id"=>"CONTENT", "text"=> GetMessage("MAIN_TEMPLATE_CONTENT_NA"));
		}
		elseif(is_set($arFields, "CONTENT") && strpos($arFields["CONTENT"], "#WORK_AREA#")===false)
		{
			$this->LAST_ERROR .= GetMessage("MAIN_TEMPLATE_WORKAREA_NA")." ";
			$arMsg[] = array("id"=>"CONTENT", "text"=> GetMessage("MAIN_TEMPLATE_WORKAREA_NA"));
		}

		if(!empty($arMsg))
		{
			$e = new CAdminException($arMsg);
			$GLOBALS["APPLICATION"]->ThrowException($e);
		}

		if(strlen($this->LAST_ERROR)>0)
			return false;

		return true;
	}

	function Add($arFields)
	{
		if(!$this->CheckFields($arFields))
			return false;

		global $APPLICATION;
		CheckDirPath($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".$arFields["ID"]);
		if(is_set($arFields, "CONTENT"))
		{
			$p = strpos($arFields["CONTENT"], "#WORK_AREA#");
			$header = substr($arFields["CONTENT"], 0, $p);
			$APPLICATION->SaveFileContent($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".$arFields["ID"]."/header.php", $header);
			$footer = substr($arFields["CONTENT"], $p + strlen("#WORK_AREA#"));
			$APPLICATION->SaveFileContent($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".$arFields["ID"]."/footer.php", $footer);
		}
		if(is_set($arFields, "STYLES"))
		{
			$APPLICATION->SaveFileContent($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".$arFields["ID"]."/styles.css", $arFields["STYLES"]);
		}

		if(is_set($arFields, "TEMPLATE_STYLES"))
		{
			$APPLICATION->SaveFileContent($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".$arFields["ID"]."/template_styles.css", $arFields["TEMPLATE_STYLES"]);
		}

		if(is_set($arFields, "NAME") || is_set($arFields, "DESCRIPTION"))
		{
			$APPLICATION->SaveFileContent($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".$arFields["ID"]."/description.php",
				'<'.'?'.
				'$arTemplate = Array("NAME"=>"'.EscapePHPString($arFields['NAME']).'", "DESCRIPTION"=>"'.EscapePHPString($arFields['DESCRIPTION']).'");'.
				'?'.'>'
				);
		}

		return $arFields["ID"];
	}


	function Update($ID, $arFields)
	{
		global $APPLICATION;

		if(!$this->CheckFields($arFields, $ID))
			return false;

		CheckDirPath($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".$ID);
		if(is_set($arFields, "CONTENT"))
		{
			$p = strpos($arFields["CONTENT"], "#WORK_AREA#");
			$header = substr($arFields["CONTENT"], 0, $p);
			$APPLICATION->SaveFileContent($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".$ID."/header.php", $header);
			$footer = substr($arFields["CONTENT"], $p + strlen("#WORK_AREA#"));
			$APPLICATION->SaveFileContent($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".$ID."/footer.php", $footer);
		}
		if(is_set($arFields, "STYLES"))
		{
			$APPLICATION->SaveFileContent($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".$ID."/styles.css", $arFields["STYLES"]);
		}

		if(is_set($arFields, "TEMPLATE_STYLES"))
		{
			$APPLICATION->SaveFileContent($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".$ID."/template_styles.css", $arFields["TEMPLATE_STYLES"]);
		}

		if(is_set($arFields, "NAME") || is_set($arFields, "DESCRIPTION"))
		{
			$db_t = CSiteTemplate::GetList(array(), array("ID"=>$ID));
			$ar_t = $db_t->Fetch();
			if(!is_set($arFields, "NAME"))
				$arFields["NAME"] = $ar_t["NAME"];
			if(!is_set($arFields, "DESCRIPTION"))
				$arFields["DESCRIPTION"] = $ar_t["DESCRIPTION"];
			$APPLICATION->SaveFileContent($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".$ID."/description.php",
				'<'.'?'.
				'$arTemplate = Array("NAME"=>"'.EscapePHPString($arFields['NAME']).'", "DESCRIPTION"=>"'.EscapePHPString($arFields['DESCRIPTION']).'");'.
				'?'.'>'
				);
		}

		return true;
	}

	function Delete($ID)
	{
		global $DB;
		if($ID==".default")
			return false;
		DeleteDirFilesEx(BX_PERSONAL_ROOT."/templates/".$ID);
		return true;
	}



	function GetContent($ID)
	{
		if(strlen($ID)<=0)
			$arRes = Array();
		else
			$arRes = CSiteTemplate::DirsRecursive($ID);
		$db_res = new CDBResult;
		$db_res->InitFromArray($arRes);
		return $db_res;
	}


	function DirsRecursive($ID, $path="", $depth=0, $maxDepth=1)
	{
		$arRes = Array();
		$depth++;

		GetDirList(BX_PERSONAL_ROOT."/templates/".$ID."/".$path, $arDirsTmp, $arResTmp);
		foreach($arResTmp as $file)
		{
			switch($file["NAME"])
			{
			case "chain_template.php":
				$file["DESCRIPTION"] = GetMessage("MAIN_TEMPLATE_NAV");
				break;
			case "":
				$file["DESCRIPTION"] = "";
				break;
			default:
				if(($p=strpos($file["NAME"], ".menu_template.php"))!==false)
					$file["DESCRIPTION"] = str_replace("#MENU_TYPE#", substr($file["NAME"], 0, $p), GetMessage("MAIN_TEMPLATE_MENU"));
				elseif(($p=strpos($file["NAME"], "authorize_registration.php"))!==false)
					$file["DESCRIPTION"] = GetMessage("MAIN_TEMPLATE_AUTH_REG");
				elseif(($p=strpos($file["NAME"], "forgot_password.php"))!==false)
					$file["DESCRIPTION"] = GetMessage("MAIN_TEMPLATE_SEND_PWD");
				elseif(($p=strpos($file["NAME"], "change_password.php"))!==false)
					$file["DESCRIPTION"] = GetMessage("MAIN_TEMPLATE_CHN_PWD");
				elseif(($p=strpos($file["NAME"], "authorize.php"))!==false)
					$file["DESCRIPTION"] = GetMessage("MAIN_TEMPLATE_AUTH");
				elseif(($p=strpos($file["NAME"], "registration.php"))!==false)
					$file["DESCRIPTION"] = GetMessage("MAIN_TEMPLATE_REG");
			}
			$arRes[] = $file;
		}

		$nTemplateLen = strlen(BX_PERSONAL_ROOT."/templates/".$ID."/");
		foreach($arDirsTmp as $dir)
		{
			$arDir = $dir;
			$arDir["DEPTH_LEVEL"] = $depth;
			$arRes[] = $arDir;

			if($depth < $maxDepth)
			{
				$dirPath = substr($arDir["ABS_PATH"], $nTemplateLen);
				$arRes = array_merge($arRes, CSiteTemplate::DirsRecursive($ID, $dirPath, $depth, $maxDepth));
			}
		}
		return $arRes;
	}
}

class CApplicationException
{
	var $msg, $id;
	function CApplicationException($msg, $id = false)
	{
		$this->msg = $msg;
		$this->id = $id;
	}

	function GetString()
	{
		return $this->msg;
	}

	function GetID()
	{
		return $this->id;
	}
}

class CAdminException extends CApplicationException
{
	var $messages;
	function CAdminException($messages, $id = false)
	{
		//array("id"=>"", "text"=>""), array(...), ...
		$this->messages = $messages;
		$s = "";
		foreach($this->messages as $msg)
			$s .= $msg["text"]."<br>";
		parent::CApplicationException($s, $id);
	}

	function GetMessages()
	{
		return $this->messages;
	}

	function AddMessage($message)
	{
		$this->messages[]=$message;
		$this->msg.=$message["text"]."<br>";
	}
}

class CCaptchaAgent
{
	function DeleteOldCaptcha($sec = 3600)
	{
		global $DB;

		$sec = intval($sec);

		$time = $DB->CharToDateFunction(GetTime(time()-$sec,"FULL"));
		if (!$DB->Query("DELETE FROM b_captcha WHERE DATE_CREATE <= ".$time))
			return false;

		return "CCaptchaAgent::DeleteOldCaptcha(".$sec.");";
	}
}

class CDebugInfo
{
	var $inc_time, $cnt_query, $query_time;
	var	$arQueryDebugSave;

	function Start()
	{
		global $DB;

		$this->inc_time = getmicrotime();
		if($DB->ShowSqlStat)
		{
			$this->cnt_query = $DB->cntQuery;
			$this->query_time = $DB->timeQuery;
			$this->arQueryDebugSave = $DB->arQueryDebug;
			$DB->arQueryDebug = array();
		}
	}

	function Output($rel_path="", $path="")
	{
		global $DB, $APPLICATION;

		$result = "";

		$this->inc_time = round(getmicrotime()-$this->inc_time, 4);
		$result .= '<div class="bx-component-debug">';
		$result .= ($rel_path<>""? $rel_path.": ":"")."<nobr>".$this->inc_time." ".GetMessage("main_incl_file_sec")."</nobr>";
		if($DB->ShowSqlStat)
		{
			if(($DB->cntQuery - $this->cnt_query)>0)
			{
				$result .= '; <a title="'.GetMessage("main_incl_file_sql_stat").'" href="javascript:jsDebugWindow.Show(\'BX_DEBUG_INFO_'.count($APPLICATION->arIncludeDebug).'\')">'.GetMessage("main_incl_file_sql").' '.($DB->cntQuery-$this->cnt_query).' ('.round($DB->timeQuery - $this->query_time, 4).' '.GetMessage("main_incl_file_sec").')</a>';
				$APPLICATION->arIncludeDebug[]=array(
					"PATH"=>$path,
					"QUERY_COUNT"=>$DB->cntQuery - $this->cnt_query,
					"QUERY_TIME"=>round($DB->timeQuery - $this->query_time, 4),
					"QUERIES"=>$DB->arQueryDebug,
					"TIME"=>$this->inc_time
				);
			}
			//$DB->arQueryDebug = array_merge($this->arQueryDebugSave, $DB->arQueryDebug);
			$DB->arQueryDebug = $this->arQueryDebugSave;
			$DB->cntQuery = $this->cnt_query;
			$DB->timeQuery = $this->query_time;
		}
		$result .= "</div>";

		return $result;
	}
}
?>