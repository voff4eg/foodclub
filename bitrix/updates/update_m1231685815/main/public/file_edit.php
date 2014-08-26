<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_js.php");

$addUrl = 'lang='.LANGUAGE_ID.($logical == "Y"?'&logical=Y':'');

$bFromComponent = $_REQUEST['from'] == 'main.include' || $_REQUEST['from'] == 'includefile' || $_REQUEST['from'] == 'includecomponent';
$bDisableEditor = !CModule::IncludeModule('fileman') || ($_REQUEST['noeditor'] == 'Y');

if (!($USER->CanDoOperation('fileman_admin_files') || $USER->CanDoOperation('fileman_edit_existent_files')))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

function finalLPAreplacer($str)
{
	$str = str_replace(array("<?","?>"),array("&lt;?","?&gt;"), $str);
	$str = ereg_replace('<(script[^>]*language[[:space:]]*=[[:space:]]*["\']*php["\']*[^>]*)>(.*)</script>', "&lt;\\1&gt;\\2&lt;/script&gt;", $str);
	return $str;
}

function LPAComponentChecker(&$arParams, &$arPHPparams, $parentParamName = false)
{
	//all php fragments wraped by ={}
	foreach ($arParams as $param_name => $paramval)
	{
		if (substr($param_name, 0, 2) == '={' && substr($param_name, -1) == '}')
		{
			$key = substr($param_name, 2, -1);
			if (strval($key) !== strval(intval($key)))
			{
				unset($arParams[$param_name]);
				continue;
			}
		}

		if (is_array($paramval))
		{
			LPAComponentChecker($paramval, $arPHPparams, $param_name);
			$arParams[$param_name] = $paramval;
		}
		elseif (substr($paramval, 0, 2) == '={' && substr($paramval, -1) == '}')
		{
			$arPHPparams[] = $parentParamName ? $parentParamName : $param_name;
		}
	}
}

function _replacer($str)
{
	$str = preg_replace("/[^a-zA-Z0-9_:\.]/i", "", $str);
	return $str;
}

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/fileman/include.php");

IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/fileman/admin/fileman_html_edit.php");
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/public/file_edit.php");

$strWarning = "";
$site_template = false;
$rsSiteTemplates = CSite::GetTemplateList($site);
while($arSiteTemplate = $rsSiteTemplates->Fetch())
{
	if(strlen($arSiteTemplate["CONDITION"])<=0)
	{
		$site_template = $arSiteTemplate["TEMPLATE"];
		break;
	}
}

while (($l=strlen($path))>0 && $path[$l-1]=="/")
	$path = substr($path, 0, $l-1);

$bVarsFromForm = false;	// флаг, указывающий, откуда брать контент из файла или из запостченой формы
$bSessIDRefresh = false;	// флаг, указывающий, нужно ли обновлять ид сессии на клиенте
$editor_name = (isset($_REQUEST['editor_name'])) ? htmlspecialcharsex($_REQUEST['editor_name']) : 'filesrc_pub';

if (strlen($filename)>0 && ($mess = CFileMan::CheckFileName($filename)) !== true)
{
	$filename2 = $filename;
	$filename = '';
	$strWarning = $mess;
	$bVarsFromForm = true;
}

$path = Rel2Abs("/", $path);
$path = urldecode($path);

$site = CFileMan::__CheckSite($site);
if(!$site)
	$site = CSite::GetSiteByFullPath($_SERVER["DOCUMENT_ROOT"].$path);

$DOC_ROOT = CSite::GetSiteDocRoot($site);
$abs_path = $DOC_ROOT.$path;

$arPath = Array($site, $path);

if(!file_exists($abs_path))
{
	$p = strrpos($path, "/");
	if($p!==false)
	{
		$new = "Y";
		$filename = substr($path, $p+1);
		$path = substr($path, 0, $p);
	}
}

$NEW_ROW_CNT = 1;

$arParsedPath = CFileMan::ParsePath(Array($site, $path), true, false, "", false);
$isScriptExt = in_array(CFileman::GetFileExtension($path), CFileMan::GetScriptFileExt());

//Check access to file
if(
	(
		strlen($new) > 0 &&
		!(
			$USER->CanDoOperation('fileman_admin_files') &&
			$USER->CanDoFileOperation('fm_create_new_file', $arPath)
		)
	)
	||
	(
		strlen($new) < 0 &&
		!(
			$USER->CanDoOperation('fileman_edit_existent_files') &&
			$USER->CanDoFileOperation('fm_edit_existent_file',$arPath)
		)
	)
)
{
	$strWarning = GetMessage("ACCESS_DENIED");
}
else
{
	if(!$USER->IsAdmin() && substr(CFileman::GetFileName($abs_path), 0, 1)==".")
	{
		$strWarning = GetMessage("FILEMAN_FILEEDIT_BAD_FNAME")." ";
		$bEdit = false;
		$bVarsFromForm = true;
		$path = Rel2Abs("/", $arParsedPath["PREV"]);
		$arParsedPath = CFileMan::ParsePath($path, true, false, "", $logical == "Y");
		$abs_path = $DOC_ROOT.$path;
	}
	elseif($new == 'Y')
	{
		$bEdit = false;
	}
	else
	{
		if(!is_file($abs_path))
			$strWarning = GetMessage("FILEMAN_FILEEDIT_FOLDER_EXISTS")." ";
		else
			$bEdit = true;
	}

	$limit_php_access = ($USER->CanDoFileOperation('fm_lpa', $arPath) && !$USER->CanDoOperation('edit_php'));
	if ($limit_php_access)
	{
		//OFP - 'original full path' used for restorin' php code fragments in limit_php_access mode
		if (!isset($_SESSION['arOFP']))
			$_SESSION['arOFP'] = Array();

		if(isset($_POST['ofp_id']))
		{
			$ofp_id = $_POST['ofp_id'];
		}
		else
		{
			$ofp_id = substr(md5($site.'|'.$path),0,8);
			if(!isset($_SESSION['arOFP'][$ofp_id]))
				$_SESSION['arOFP'][$ofp_id] = $path;
		}
	}
}

if(strlen($strWarning)<=0)
{
	if($bEdit)
	{
		$filesrc_tmp = $APPLICATION->GetFileContent($abs_path);
	}
	else
	{
		$arTemplates = CFileman::GetFileTemplates(LANGUAGE_ID, array($site_template));
		if(strlen($template) > 0)
			for ($i=0; $i<count($arTemplates); $i++)
			{
				if($arTemplates[$i]["file"] == $template)
				{
					$filesrc_tmp = CFileman::GetTemplateContent($arTemplates[$i]["file"],LANGUAGE_ID, array($site_template));
					break;
				}
			}
		else
			$filesrc_tmp = CFileman::GetTemplateContent($arTemplates[0]["file"], LANGUAGE_ID, array($site_template));
	}

	if($_SERVER["REQUEST_METHOD"] == "POST" && $_REQUEST['save'] == 'Y')
	{
		$filesrc = $filesrc_pub;
		if(!check_bitrix_sessid())
		{
			$strWarning = GetMessage("FILEMAN_SESSION_EXPIRED");
			$bVarsFromForm = true;
			$bSessIDRefresh = true;
		}
		elseif((CFileman::IsPHP($filesrc) || $isScriptExt) && !($USER->CanDoOperation('edit_php') || $limit_php_access)) //check rights
		{
			$strWarning = GetMessage("FILEMAN_FILEEDIT_CHANGE");
			$bVarsFromForm = true;
		}
		else
		{
			if($limit_php_access)
			{
				// ofp - original full path :)
				$ofp = $_SESSION['arOFP'][$ofp_id];
				$ofp = Rel2Abs("/", $ofp);
				$abs_ofp = $DOC_ROOT.$ofp;
				$old_filesrc_tmp = $APPLICATION->GetFileContent($abs_ofp);
				$old_res = CFileman::ParseFileContent($old_filesrc_tmp);
				$old_filesrc = $old_res["CONTENT"];

				// We gonna to find all php fragments in saved source and:
				// 1. Kill all non-component 2.0 fragments
				// 2. Get and check params of components
				$arPHP = PHPParser::ParseFile($filesrc);
				$l = count($arPHP);
				if ($l > 0)
				{
					$new_filesrc = '';
					$end = 0;
					$php_count = 0;
					for ($n = 0; $n<$l; $n++)
					{
						$start = $arPHP[$n][0];
						$new_filesrc .= finalLPAreplacer(substr($filesrc,$end,$start-$end));
						$end = $arPHP[$n][1];

						//Trim php tags
						$src = $arPHP[$n][2];
						if (substr($src, 0, 5) == "<?php")
							$src = '<?'.substr($src, 5);

						//If it's Component 2 - we handle it's params, non components2 will be erased
						$comp2_begin = '<?$APPLICATION->INCLUDECOMPONENT(';
						if (strtoupper(substr($src,0, strlen($comp2_begin))) == $comp2_begin)
						{
							$arRes = PHPParser::CheckForComponent2($src);
							if ($arRes)
							{
								$comp_name = _replacer($arRes['COMPONENT_NAME']);
								$template_name = _replacer($arRes['TEMPLATE_NAME']);
								$arParams = $arRes['PARAMS'];
								$arPHPparams = Array();
								//all php fragments wraped by ={}
								LPAComponentChecker($arParams, $arPHPparams);

								$len = count($arPHPparams);
								$br = "\r\n";
								// If exist at least one parameter with php code inside
								if (count($arParams) > 0)
								{
									// Get array with description of component params
									$arCompParams = CComponentUtil::GetComponentProps($comp_name);
									$arTemplParams = CComponentUtil::GetTemplateProps($comp_name,$template_name,$template);

									$arParameters = array();
									if (isset($arCompParams["PARAMETERS"]) && is_array($arCompParams["PARAMETERS"]))
										$arParameters = $arParameters + $arCompParams["PARAMETERS"];
									if (is_array($arTemplParams))
										$arParameters = $arParameters + $arTemplParams;

									// Replace values from 'DEFAULT'
									for ($e = 0; $e < $len; $e++)
									{
										$par_name = $arPHPparams[$e];
										$arParams[$par_name] = isset($arParameters[$par_name]['DEFAULT']) ? $arParameters[$par_name]['DEFAULT'] : '';
									}

									CComponentUtil::PrepareVariables($arParams);
									//ReturnPHPStr
									$params = PHPParser::ReturnPHPStr2($arParams, $arParameters);
									$code =  '$APPLICATION->IncludeComponent('.$br.
										"\t".'"'.$comp_name.'",'.$br.
										"\t".'"'.$template_name.'",'.$br.
										"\t".'Array('.$br.
										"\t".$params.$br.
										"\t".')'.$br.
										');';
									$code = '<?'.$code.'?>';
									$new_filesrc .= $code;
								}
								else
								{
									$code =  '$APPLICATION->IncludeComponent('.$br.
										"\t".'"'.$comp_name.'",'.$br.
										"\t".'"'.$template_name.'",'.$br.
										"\t".'Array()'.$br.
										');';
									$code = '<?'.$code.'?>';
									$new_filesrc .= $code;
								}
							}
						}
					}

					$new_filesrc .= finalLPAreplacer(substr($filesrc,$end));
					$filesrc = $new_filesrc;
				}
				else
				{
					$filesrc = finalLPAreplacer($filesrc);
				}

				// Get array of PHP scripts from old saved file
				$arPHP = PHPParser::ParseFile($old_filesrc);
				$arPHPscripts = Array();
				$l = count($arPHP);
				if ($l > 0)
				{
					$new_filesrc = '';
					$end = 0;
					$php_count = 0;
					for ($n = 0; $n<$l; $n++)
					{
						$start = $arPHP[$n][0];
						$new_filesrc .= substr($old_filesrc,$end,$start-$end);
						$end = $arPHP[$n][1];

						//Trim php tags
						$src = $arPHP[$n][2];
						if (SubStr($src, 0, 5) == "<?"."php")
							$src = SubStr($src, 5);
						else
							$src = SubStr($src, 2);
						$src = SubStr($src, 0, -2);

						$comp2_begin = '$APPLICATION->INCLUDECOMPONENT(';
						if (strtoupper(substr($src,0, strlen($comp2_begin))) != $comp2_begin)
							$arPHPscripts[] = $src;
					}
				}

				// Ok, so we already have array of php scripts lets check our new content
				// LPA-users CAN delete PHP fragments and swap them but CAN'T add new or modify existent:
				$pattern = '/#PHP\d{4}#/i';
				while (preg_match($pattern,$filesrc,$res,PREG_OFFSET_CAPTURE))
				{
					$php_begin = $res[0][1];
					$php_fr_num = intval(substr($filesrc,$php_begin+4,4)) - 1; // Number of PHP fragment from #PHPXXXX# conctruction
					if (isset($arPHPscripts[$php_fr_num]))
						$filesrc = substr($filesrc,0,$php_begin).'<?'.$arPHPscripts[$php_fr_num].'?>'.substr($filesrc,$php_begin+9);
					else
						$filesrc = substr($filesrc,0,$php_begin).substr($filesrc,$php_begin+9);
				}
			}

			$res = CFileman::ParseFileContent($filesrc_tmp);
			$prolog = CFileman::SetTitle($res["PROLOG"], $title);
			for ($i = 0; $i<=$maxind; $i++)
			{
				if(strlen(Trim($_POST["CODE_".$i]))>0)
				{
					if($_POST["CODE_".$i] != $_POST["H_CODE_".$i])
					{
						$prolog = CFileman::SetProperty($prolog, Trim($_POST["H_CODE_".$i]), "");
						$prolog = CFileman::SetProperty($prolog, Trim($_POST["CODE_".$i]), Trim($_POST["VALUE_".$i]));
					}
					else
						$prolog = CFileman::SetProperty($prolog, Trim($_POST["CODE_".$i]), Trim($_POST["VALUE_".$i]));
				}
				else
					$prolog = CFileman::SetProperty($prolog, Trim($_POST["H_CODE_".$i]), "");
			}
			$epilog = $res["EPILOG"];
			$filesrc_for_save = $prolog.$filesrc.$epilog;
		}

		if(strlen($strWarning) <= 0)
		{
			if (!CFileMan::CheckOnAllowedComponents($filesrc_for_save))
			{
				$str_err = $APPLICATION->GetException();
				if($str_err && ($err = $str_err ->GetString()))
					$strWarning .= $err;
				$bVarsFromForm = true;
			}
		}

		if(strlen($strWarning) <= 0)
		{
			if(!$APPLICATION->SaveFileContent($abs_path, $filesrc_for_save))
			{
				if($str_err = $APPLICATION->GetException())
				{
					if ($err = $str_err ->GetString())
						$strWarning = $err;

					$bVarsFromForm = true;
					$path = Rel2Abs("/", $arParsedPath["PREV"]);
					$arParsedPath = CFileMan::ParsePath($path, true, false, "", $logical == "Y");
					$abs_path = $DOC_ROOT.$path;
				}

				if (empty($strWarning))
					$strWarning = GetMessage("FILEMAN_FILE_SAVE_ERROR")." ";
			}
		}

		if(strlen($strWarning)<=0)
		{
?>
<script>
top.ShowWaitWindow();
var new_href =
<?if (isset($_REQUEST["back_url"]) && strlen($_REQUEST["back_url"]) > 0):?>
	'<?=CUtil::JSEscape($_REQUEST["back_url"])?>'
<?else:?>
	top.location.href;
<?endif?>

var hashpos = new_href.indexOf('#');
if (hashpos != -1)
	new_href = new_href.substr(0, hashpos);

new_href += (new_href.indexOf('?') == -1 ? '?' : '&') + 'clear_cache=Y';
top.location.href = new_href;
top.jsPopup.CloseDialog();
</script>
<?
		}
		else
		{
?>
<script>
top.CloseWaitWindow();
top.jsPopup.ShowError('<?=CUtil::JSEscape($strWarning)?>');

<?if ($bSessIDRefresh):?>
top.BXSetSessionID('<?=CUtil::JSEscape(bitrix_sessid())?>');
<?endif;?>
</script>
<?
		}
		die();
	}
}
else
{
?>
<script>
top.CloseWaitWindow();
top.jsPopup.ShowError('<?=CUtil::JSEscape($strWarning)?>');
</script>
<?
}

if(!$bVarsFromForm)
{
	$res = CFileman::ParseFileContent($filesrc_tmp);
	$filesrc = $res["CONTENT"];

	// ###########  L  P  A  ############
	if ($limit_php_access)
	{
		$arPHP = PHPParser::ParseFile($filesrc);
		$l = count($arPHP);
		if ($l > 0)
		{
			$new_filesrc = '';
			$end = 0;
			$php_count = 0;
			for ($n = 0; $n<$l; $n++)
			{
				$start = $arPHP[$n][0];
				$new_filesrc .= substr($filesrc,$end,$start-$end);
				$end = $arPHP[$n][1];

				//Trim php tags
				$src = $arPHP[$n][2];
				if (SubStr($src, 0, 5) == "<?"."php")
					$src = SubStr($src, 5);
				else
					$src = SubStr($src, 2);
				$src = SubStr($src, 0, -2);

				//If it's Component 2, keep the php code. If it's component 1 or ordinary PHP - than replace code by #PHPXXXX# (XXXX - count of PHP scripts)
				$comp2_begin = '$APPLICATION->INCLUDECOMPONENT(';
				if (strtoupper(substr($src,0, strlen($comp2_begin))) == $comp2_begin)
					$new_filesrc .= $arPHP[$n][2];
				else
					$new_filesrc .= '#PHP'.str_pad(++$php_count, 4, "0", STR_PAD_LEFT).'#';
			}
			$new_filesrc .= substr($filesrc,$end);
			$filesrc = $new_filesrc;
		}
	}

	$bEditProps = (strpos($res["PROLOG"], "prolog_before")>0 || strpos($res["PROLOG"], "header.php")>0);
	$title = $res["TITLE"];

	if((CFileman::IsPHP($filesrc) || $isScriptExt) && !($USER->CanDoOperation('edit_php') || $limit_php_access))
		$strWarning = GetMessage("FILEMAN_FILEEDIT_CHANGE_ACCESS");
}

$obJSPopup = new CJSPopup("lang=".urlencode($_GET["lang"])."&site=".urlencode($_GET["site"])."&back_url=".urlencode($_GET["back_url"])."&path=".urlencode($_GET["path"])."&name=".urlencode($_GET["name"]));

$obJSPopup->ShowTitlebar(GetMessage('PUBLIC_EDIT_TITLE'.($bFromComponent ? '_COMP' : '')).': '.htmlspecialcharsex($_GET['path']));

$obJSPopup->StartContent(
	array(
		'style' => "0px; height: 500px; overflow: hidden;",
		'class' => "bx-content-editor"
	)
);
?>
</form>
<iframe src="javascript:void(0)" name="file_edit_form_target" height="0" width="0" style="display: none;"></iframe>
<form action="/bitrix/admin/public_file_edit.php" name="editor_form" method="post" enctype="multipart/form-data" target="file_edit_form_target">
<?=bitrix_sessid_post()?>
<input type="submit" name="submitbtn" style="display: none;" />
<input type="hidden" name="mode" id="mode" value="public" />
<input type="hidden" name="save" id="save" value="Y" />
<input type="hidden" name="site" id="site" value="<?=htmlspecialcharsex($site)?>" />
<input type="hidden" name="template" id="template" value="<?echo htmlspecialcharsex($template)?>" />
<?if (is_set($_REQUEST, 'back_url')):?>
	<input type="hidden" name="back_url" value="<?=htmlspecialcharsex($_REQUEST['back_url'])?>" />
<?endif;?>
<?if(!$bEdit):?>
	<input type="hidden" name="new" id="new" value="Y" />
	<input type="hidden" name="filename" id="filename" value="<?echo htmlspecialcharsex($filename)?>" />
	<input type="hidden" name="path" id="path" value="<?=htmlspecialcharsex($path.'/'.$filename)?>" />
<?else:?>
	<input type="hidden" name="title" value="<?=htmlspecialcharsex($title)?>" />
	<input type="hidden" name="path" id="path" value="<?=htmlspecialcharsex($path)?>" />
<?endif;?>

<script>
function BXFormSubmit()
{
	ShowWaitWindow();
	var obForm = document.forms["editor_form"];
	obForm.elements["submitbtn"].click();
}

function BXSetSessionID(new_sessid)
{
	document.forms.editor_form.sessid.value = new_sessid;
}
</script>

<?
if (!$bDisableEditor):
	function CustomizeEditor()
	{
?>
<script>
var _bEdit = true;
arButtons['save_and_exit'] = ['BXButton',
	{
		id : 'save_and_exit',
		iconkit : '_global_iconkit.gif',
		codeEditorMode : true,
		name : '<?=CUtil::JSEscape(GetMessage('PUBLIC_EDIT_SAVE'))?>',
		title : '<?=CUtil::JSEscape(GetMessage('PUBLIC_EDIT_SAVE_TITLE'))?>',
		show_name : true,
		handler : BXFormSubmit
	}
];

arButtons['exit'] = ['BXButton',
	{
		id : 'exit',
		iconkit : '_global_iconkit.gif',
		codeEditorMode : true,
		name : BX_MESS.TBExit,
		handler : function ()
		{
			var need_to_ask = (this.pMainObj.IsChanged() && !this.pMainObj.isSubmited);
			if(need_to_ask)
			{
				this.pMainObj.OpenEditorDialog("asksave", false, 600, {window: window, savetype: _bEdit ? 'save' : 'saveas', popupMode: true}, true);
			}
			else
			{
				this.pMainObj.SetFullscreen(false);
				top.jsPopup.CloseDialog();
			}
		}
	}
];

if (arGlobalToolbar[1][1].id != 'save_and_exit')
	arGlobalToolbar = ['line_begin', arButtons['save_and_exit'], arButtons['exit']].concat(arGlobalToolbar.slice(1));

if (!BXHTMLEditor.prototype.SetFullscreen_)
	BXHTMLEditor.prototype.SetFullscreen_ = BXHTMLEditor.prototype.SetFullscreen;
var arPos = null;
var offset = null;

var scroll = 0;
var overflow = '';

var bFirstResize = false;

BXHTMLEditor.prototype.SetFullscreen = function (bFull)
{
	var obDiv = document.getElementById('bx_popup_content');
	window.obDiv_original_height = 500;
	if (bFull)
	{
		scroll = Math.max(document.body.scrollTop, document.documentElement.scrollTop);
		var obDocumentElement = (jsUtils.IsIE() && document.documentElement) ? document.documentElement : document.body;

		overflow = jsUtils.GetStyleValue(obDocumentElement, 'overflow');
		obDocumentElement.style.overflow = 'hidden';

		function __doResize()
		{
			if (null == arPos)
				arPos = jsUtils.GetRealPos(obDiv);
			if (null == offset)
				offset = [obDiv.offsetLeft, obDiv.offsetTop];

			obDiv.style.position = 'absolute';
			obDiv.style.zIndex = 9994;

			var obWindowSize = jsUtils.GetWindowSize();

			obDiv.style.left = '-' + (arPos.left - offset[0] + 6 - obWindowSize.scrollLeft) + 'px';
			obDiv.style.top = '-' + (arPos.top - offset[1] + 6 - obWindowSize.scrollTop) + 'px';

			obDiv.style.height = obWindowSize.innerHeight + 'px';
			obDiv.style.width = obWindowSize.innerWidth + 'px';
		}

		if (!bFirstResize)
		{
			this.AddEventHandler('OnFullResize', __doResize, this);
			bFirstResize = true;
		}
		//window.onresize = __doResize;

		// IE is not capable to determine window size correctly after overflow = hidden. Needs timeout.
		if (jsUtils.IsIE())
			setTimeout(__doResize, 30);
		else
			__doResize();
	}
	else
	{
		var obDocumentElement = (jsUtils.IsIE() && document.documentElement) ? document.documentElement : document.body;
		obDocumentElement.style.overflow = overflow;

		document.body.scrollTop = scroll;
		document.documentElement.scrollTop = scroll;

		obDiv.style.position = 'static';
		obDiv.style.top = '0px';

		obDiv.style.height = window.obDiv_original_height + 'px';
		obDiv.style.width = 'auto';

		//window.onresize = null;
		arPos = null;
		offset = null;
	}

	this.SetFullscreen_(bFull);
};
</script>
<?
	} // function CustomizeEditor()

	AddEventHandler("fileman", "OnIncludeHTMLEditorScript", "CustomizeEditor");
	CFileman::ShowHTMLEditControl($editor_name, $filesrc, Array(
		"site" => $site,
		"templateID" => $templateID,
		"bUseOnlyDefinedStyles" => COption::GetOptionString("fileman", "show_untitled_styles", "N")!="Y",
		"bWithoutPHP" => (!$USER->CanDoOperation('edit_php')),
		"arToolbars" => Array("manage", "standart", "style", "formating", "source", "table"),
		"arTaskbars" => Array("BXComponentsTaskbar", "BXComponents2Taskbar", "BXPropertiesTaskbar", "BXSnippetsTaskbar"),
		"sBackUrl" => $back_url,
		"fullscreen" => false,
		"path" => $path,
		"limit_php_access" => $limit_php_access,
		'height' => '490',
		'width' => '100%',
		'light_mode' => true,
	));
?>
<script>

arEditorFastDialogs['asksave'] = function(pObj)
{
	var str = '<table height="100%" width="100%" id="t1" border="0">' +
	'<tr>' +
		'<td colspan="3">' +
			'<table height="100%" width="100%" id="t1" border="0" style="font-size:14px;">' +
			'<tr>' +
			'<td></td>' +
			'<td>' + BX_MESS.DIALOG_EXIT_ACHTUNG + '</td>' +
			'</tr>' +
			'</table>' +
		'</td>' +
	'</tr>' +
	'<tr id="buttonsSec" valign="top">' +
		'<td align="center"><input style="font-size:12px; height: 25px; width: 180px;" type="button" id="asksave_b1" value="' + BX_MESS.DIALOG_SAVE_BUT + '"></td>' +
		'<td align="center"><input style="font-size:12px; height: 25px; width: 180px;" type="button" id="asksave_b2" value="' + BX_MESS.DIALOG_EXIT_BUT + '"></td>' +
		'<td align="center"><input style="font-size:12px; height: 25px; width: 180px;" type="button" id="asksave_b3" value="' + BX_MESS.DIALOG_EDIT_BUT + '"></td>' +
	'</tr>' +
'</table>';

	var OnClose = function(){pObj.Close();};
	var OnSave = function()
	{
		pObj.pMainObj.isSubmited = true;
		if(pObj.params.savetype == 'save')
			BXFormSubmit();
		OnClose();
	};
	var OnExit = function()
	{
		pObj.pMainObj.isSubmited = true;
		pObj.pMainObj.SetFullscreen(false);
		jsPopup.CloseDialog();
		OnClose();
	};
	return {
		title: BX_MESS.EDITOR,
		innerHTML : str,
		OnLoad: function()
		{
			document.getElementById("asksave_b1").focus();
			document.getElementById("asksave_b1").onclick = OnSave;
			document.getElementById("asksave_b2").onclick = OnExit;
			document.getElementById("asksave_b3").onclick = OnClose;
		}
	};
};

function _BXOnBeforeCloseDialog(arParams, dialog_suffix)
{
	if (dialog_suffix && dialog_suffix.length > 0 )
		return;

	var pMainObj = GLOBAL_pMainObj['<?=$editor_name?>'];
	var need_to_ask = (pMainObj.IsChanged() && !pMainObj.isSubmited);
	if (need_to_ask)
	{
		pMainObj.OpenEditorDialog("asksave", false, 600, {window: window, savetype: _bEdit ? 'save' : 'saveas', popupMode: true}, true);
		jsPopup.bDenyClose = true;
	}
	else
	{
		jsUtils.onCustomEvent('OnBeforeCloseDialog_');
		jsUtils.removeEvent(pMainObj.pEditorDocument, "keypress", window.JCPopup_OnKeyPress);
		jsUtils.removeCustomEvent('OnBeforeCloseDialog', _BXOnBeforeCloseDialog);
		jsPopup.bDenyClose = false;
	}
}

function CheckEditorFinish()
{
	var pMainObj = GLOBAL_pMainObj['<?=$editor_name?>'];
	if (!pMainObj.bLoadFinish)
		return setTimeout('CheckEditorFinish()', 100);

	jsPopup.AllowClose();
	jsUtils.addEvent(pMainObj.pEditorDocument, "keypress", window.JCPopup_OnKeyPress);
}
CheckEditorFinish();
jsUtils.addCustomEvent('OnBeforeCloseDialog', _BXOnBeforeCloseDialog);
</script>
<?
else: //if ($bDisableEditor)
?>
<?
	require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/interface/init_admin.php");
	$arPos = array($editor_name.'_width' => '745', $editor_name.'_height' => '480');
	if (class_exists('CUserOptions'))
	{
		$arPos = CUserOptions::GetOption(
			'jsPopup',
			'size_'.$APPLICATION->GetCurPage(),
			$arPos
		);
	}
?>
<textarea name="<?=$editor_name?>" id="<?=$editor_name?>" style="height: <?=intval($arPos[$editor_name.'_height'])?>px; width: <?=intval($arPos[$editor_name.'_width'])?>px;"><?=htmlspecialcharsex($filesrc)?></textarea>
<script>
document.getElementById('<?=$editor_name?>').BXResizeCacheID = '<?=$editor_name?>';
jsPopup.addAdditionalResize('<?=$editor_name?>');
</script>
<?
endif; //if (!$bDisableEditor)
$obJSPopup->StartButtons();
?>
	<input type="button" id="btn_popup_save" name="btn_popup_save" value="<?=GetMessage("JSPOPUP_SAVE_CAPTION")?>" onclick="BXFormSubmit();" title="<?=GetMessage("JSPOPUP_SAVE_CAPTION")?>" />
<?
$obJSPopup->ShowStandardButtons(array('cancel'));
$obJSPopup->EndButtons();
if (!$bDisableEditor):
?>
<script>jsPopup.DenyClose();</script>
<?
endif;
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin_js.php");
?>