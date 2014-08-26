<?
/*
##############################################
# Bitrix Site Manager                        #
# Copyright (c) 2002-2007 Bitrix             #
# http://www.bitrixsoft.com                  #
# mailto:admin@bitrixsoft.com                #
##############################################
*/
require_once(substr(__FILE__, 0, strlen(__FILE__) - strlen("/include.php"))."/bx_root.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/start.php");
//определяем язык
$APPLICATION = new CMain;

if(defined("SITE_ID"))
	define("LANG", SITE_ID);

if(defined("LANG"))
{
	if(defined("ADMIN_SECTION") && ADMIN_SECTION===true)
		$db_lang = CLangAdmin::GetByID(LANG);
	else
		$db_lang = CLang::GetByID(LANG);

	$arLang = $db_lang->Fetch();
}
else
{
	$arLang = $APPLICATION->GetLang(); //определим переменную lang будто она пришла от пользователя (если действительно не пришла)
	define("LANG", $arLang["LID"]);
}

$lang = $arLang["LID"];
define("SITE_ID", $arLang["LID"]);
define("SITE_DIR", $arLang["DIR"]);
define("SITE_SERVER_NAME", $arLang["SERVER_NAME"]);
define("SITE_CHARSET", $arLang["CHARSET"]);
define("FORMAT_DATE", $arLang["FORMAT_DATE"]);
define("FORMAT_DATETIME", $arLang["FORMAT_DATETIME"]);
define("LANG_DIR", $arLang["DIR"]);
define("LANG_CHARSET", $arLang["CHARSET"]);
define("LANG_ADMIN_LID", $arLang["LANGUAGE_ID"]);
define("LANGUAGE_ID", $arLang["LANGUAGE_ID"]);

error_reporting(COption::GetOptionInt("main", "error_reporting", E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR|E_PARSE));

if($domain = $APPLICATION->GetCookieDomain())
	ini_set("session.cookie_domain", $domain);

IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/classes/general/main.php");

require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/filter_tools.php");
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/ajax_tools.php");

IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/classes/general/database.php");
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/tools.php");

/***************************/
global $arCustomTemplateEngines;
$arCustomTemplateEngines = array();
/***************************/

require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/classes/general/urlrewriter.php");

//классы зависящие от языка
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/classes/".$DBType."/agent.php");	//агенты
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/classes/".$DBType."/user.php"); 	//пользователь
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/classes/".$DBType."/event.php");	//события системы
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/classes/general/menu.php"); 	//меню
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/classes/general/module.php"); //подключаемые модули
AddEventHandler("main", "OnEpilog", array("CCacheManager", "_Finalize"));
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/classes/".$DBType."/usertype.php");

if (file_exists($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/classes/general/update_db_updater.php"))
{
	$US_HOST_PROCESS_MAIN = False;
	include($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/classes/general/update_db_updater.php");
}

if(file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/init.php"))
	include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/init.php");

if(file_exists($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/php_interface/init.php"))
	include_once($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/php_interface/init.php");

if(file_exists($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/php_interface/".SITE_ID."/init.php"))
	include_once($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/php_interface/".SITE_ID."/init.php");

CModule::AddAutoloadClasses(
	"main",
	array(
		"CBitrixComponent" => "classes/general/component.php",
		"CComponentEngine" => "classes/general/component_engine.php",
		"CComponentAjax" => "classes/general/component_ajax.php",
		"CBitrixComponentTemplate" => "classes/general/component_template.php",
		"CComponentUtil" => "classes/general/component_util.php",
		"CControllerClient" => "classes/general/controller_member.php",
		"PHPParser" => "classes/general/php_parser.php",
		"CDiskQuota" => "classes/".$DBType."/quota.php",
		"CEventLog" => "classes/general/event_log.php",
	)
);

if(COption::GetOptionString('main', 'auth_openid', 'N') == 'Y')
	require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/classes/general/openid.php");

if(COption::GetOptionString('main', 'auth_liveid', 'N') == 'Y')
	require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/classes/general/liveid.php");

if(!defined("BX_FILE_PERMISSIONS"))
	define("BX_FILE_PERMISSIONS", 0777);
if(!defined("BX_DIR_PERMISSIONS"))
	define("BX_DIR_PERMISSIONS", 0777);

//global var, is used somewhere
$sDocPath = $APPLICATION->GetCurPage();

if((!(defined("STATISTIC_ONLY") && STATISTIC_ONLY && substr($APPLICATION->GetCurPage(), 0, strlen(BX_ROOT."/admin/"))!=BX_ROOT."/admin/")) && COption::GetOptionString("main", "include_charset", "Y")=="Y" && strlen(LANG_CHARSET)>0)
	header("Content-Type: text/html; charset=".LANG_CHARSET);

if(COption::GetOptionString("main", "set_p3p_header", "Y")=="Y")
	header("P3P: policyref=\"/bitrix/p3p.xml\", CP=\"NON DSP COR CUR ADM DEV PSA PSD OUR UNR BUS UNI COM NAV INT DEM STA\"");

//licence key
$LICENSE_KEY = "";
if(file_exists($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/license_key.php"))
	include($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/license_key.php");
if($LICENSE_KEY=="" || strtoupper($LICENSE_KEY)=="DEMO")
	define("LICENSE_KEY", "DEMO");
else
	define("LICENSE_KEY", $LICENSE_KEY);

header("X-Powered-CMS: Bitrix Site Manager (".(LICENSE_KEY == "DEMO"? "DEMO" : md5("BITRIX".LICENSE_KEY."LICENCE")).")");

define("BX_CRONTAB_SUPPORT", defined("BX_CRONTAB"));

if(COption::GetOptionString("main", "check_agents", "Y")=="Y")
	CAgent::CheckAgents();

$db_events = GetModuleEvents("main", "OnPageStart");
while($arEvent = $db_events->Fetch())
	ExecuteModuleEvent($arEvent);

session_start();

//определяем пользователя
$USER = new CUser;
$arPolicy = $USER->GetSecurityPolicy();
if(
	(
		$_SESSION['SESS_IP']
		&&
		strlen($arPolicy["SESSION_IP_MASK"])>0
		&&
		(
			(ip2long($arPolicy["SESSION_IP_MASK"]) & ip2long($_SESSION['SESS_IP']))
			!=
			(ip2long($arPolicy["SESSION_IP_MASK"]) & ip2long($_SERVER['REMOTE_ADDR']))
		)
	)
	||
	(
		$arPolicy["SESSION_TIMEOUT"]>0
		&&
		$_SESSION['SESS_TIME']>0
		&&
		mktime()-$arPolicy["SESSION_TIMEOUT"]*60 > $_SESSION['SESS_TIME']
	)
)
{
	$_SESSION = array();
	/*
	if(isset($_COOKIE[session_name()]))
		setcookie(session_name(), '', time()-42000, '/');
	*/
	@session_destroy();
	session_id(md5(uniqid(rand(), true)));
	session_start();
	$USER = new CUser;
}
$_SESSION['SESS_IP'] = $_SERVER['REMOTE_ADDR'];
$_SESSION['SESS_TIME'] = mktime();

define("BX_STARTED", true);

if(!defined("NOT_CHECK_PERMISSIONS") || NOT_CHECK_PERMISSIONS!==true)
{
	if(strtolower($_REQUEST["logout"])=="yes" && $USER->IsAuthorized())
		$USER->Logout();

	// авторизуем если нужно из данных cookie
	$cookie_login = ${COption::GetOptionString("main", "cookie_name", "BITRIX_SM")."_LOGIN"};
	$cookie_md5pass = ${COption::GetOptionString("main", "cookie_name", "BITRIX_SM")."_UIDH"};

	if(COption::GetOptionString("main", "store_password", "Y")=="Y"
		&& strlen($cookie_login)>0
		&& strlen($cookie_md5pass)>0
		&& !$USER->IsAuthorized()
		&& strtolower($logout)!="yes"
		&& $_SESSION["SESS_PWD_HASH_TESTED"] != md5($cookie_login."|".$cookie_md5pass)
		)
	{
		$USER->LoginByHash($cookie_login, $cookie_md5pass);
		$_SESSION["SESS_PWD_HASH_TESTED"] = md5($cookie_login."|".$cookie_md5pass);
	}

	// Authorize user, if it is http standart authorization, with no remembering
	if (isset($_SERVER["PHP_AUTH_USER"]))
	{
		if (strlen($_SERVER["PHP_AUTH_USER"]) > 0 and
			strlen($_SERVER["PHP_AUTH_PW"]) > 0)
		{
			$arAuthResult = $USER->Login($_SERVER["PHP_AUTH_USER"], $_SERVER["PHP_AUTH_PW"], "N");
			$APPLICATION->arAuthResult = $arAuthResult;
		}
	}

	//Авторизуем пользователя, если идет пост с формы авторизации
	$strAuthRes="";
	if(strlen($_REQUEST["AUTH_FORM"])>0)
	{
		if(!defined("ADMIN_SECTION") || ADMIN_SECTION !== true)
			$USER_LID = LANG;
		else
			$USER_LID = false;

		if($TYPE=="AUTH")
		{
			$arAuthResult = $USER->Login($USER_LOGIN, $USER_PASSWORD, $USER_REMEMBER);
		}
		elseif($TYPE=="SEND_PWD")
		{
			$arAuthResult = $USER->SendPassword($USER_LOGIN, $USER_EMAIL, $USER_LID);
		}
		elseif($_SERVER['REQUEST_METHOD']=='POST' && $TYPE=="CHANGE_PWD")
		{
			$arAuthResult = $USER->ChangePassword($USER_LOGIN, $USER_CHECKWORD, $USER_PASSWORD, $USER_CONFIRM_PASSWORD, $USER_LID);
		}
		elseif(COption::GetOptionString("main", "new_user_registration", "N")=="Y" && $_SERVER['REQUEST_METHOD']=='POST' && $TYPE=="REGISTRATION" && (!defined("ADMIN_SECTION") || ADMIN_SECTION!==true))
		{
			$arAuthResult = $USER->Register($USER_LOGIN, $USER_NAME, $USER_LAST_NAME, $USER_PASSWORD, $USER_CONFIRM_PASSWORD, $USER_EMAIL, $USER_LID, $captcha_word, $captcha_sid);
		}

		$APPLICATION->arAuthResult = $arAuthResult;
	}
}

//define the site template
if(!defined("ADMIN_SECTION") || ADMIN_SECTION !== true)
{
	if(array_key_exists("bitrix_preview_site_template", $_REQUEST) && $_REQUEST["bitrix_preview_site_template"] <> "" && $USER->CanDoOperation('view_other_settings'))
	{
		//preview of site template
		$aTemplates = CSiteTemplate::GetByID($_REQUEST["bitrix_preview_site_template"]);
		if($template = $aTemplates->Fetch())
			define("SITE_TEMPLATE_ID", $template["ID"]);
		else
			define("SITE_TEMPLATE_ID", CSite::GetCurTemplate());
	}
	else
		define("SITE_TEMPLATE_ID", CSite::GetCurTemplate());

	define("SITE_TEMPLATE_PATH", BX_PERSONAL_ROOT.'/templates/'.SITE_TEMPLATE_ID);
}

//magic parameters: show page creation time
if($_GET["show_page_exec_time"]=="Y" || $_GET["show_page_exec_time"]=="N")
	$_SESSION["SESS_SHOW_TIME_EXEC"] = $_GET["show_page_exec_time"];

//magic parameters: show included file processing time
if($_GET["show_include_exec_time"]=="Y" || $_GET["show_include_exec_time"]=="N")
	$_SESSION["SESS_SHOW_INCLUDE_TIME_EXEC"] = $_GET["show_include_exec_time"];

//magic parameters: show include areas
if(isset($_GET["bitrix_include_areas"]) && $_GET["bitrix_include_areas"] <> "")
	$APPLICATION->SetShowIncludeAreas($_GET["bitrix_include_areas"]=="Y");

//magic parameters: set view/edit/configure mode
if(isset($_GET["bitrix_show_mode"]) && $_GET["bitrix_show_mode"] <> "")
	$APPLICATION->SetPublicShowMode($_GET["bitrix_show_mode"]);

$db_events = GetModuleEvents("main", "OnBeforeProlog");
while($arEvent = $db_events->Fetch())
	ExecuteModuleEvent($arEvent);

if(!defined("NOT_CHECK_PERMISSIONS") || NOT_CHECK_PERMISSIONS!==true)
{
	$real_path = $APPLICATION->GetCurPage();
	if (isset($_SERVER["REAL_FILE_PATH"]) && $_SERVER["REAL_FILE_PATH"] != "")
		$real_path = $_SERVER["REAL_FILE_PATH"];

	if(!$USER->CanDoFileOperation('fm_view_file', Array(SITE_ID, $real_path)) || (defined("NEED_AUTH") && NEED_AUTH && !$USER->IsAuthorized()))
	{
		if($USER->IsAuthorized() && strlen($arAuthResult["MESSAGE"])<=0)
			$arAuthResult = array("MESSAGE"=>GetMessage("ACCESS_DENIED").' '.GetMessage("ACCESS_DENIED_FILE", array("#FILE#"=>$real_path)), "TYPE"=>"ERROR");

		if(defined("ADMIN_SECTION") && ADMIN_SECTION==true)
		{
			if ($_REQUEST["mode"]=="list")
			{
				echo "<script>window.location='".$APPLICATION->GetCurPage()."?".DeleteParam(array("mode"))."';</script>";
				die();
			}
			elseif ($_REQUEST["mode"]=="frame")
			{
				echo "<script type=\"text/javascript\">
					var w = (opener? opener.window:parent.window);
					w.location='".$APPLICATION->GetCurPage()."?".DeleteParam(array("mode"))."';
				</script>";
				die();
			}
			elseif ($_REQUEST["mode"]=="public")
			{
				require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/interface/popup_auth.php");
				die();
			}
		}

		$APPLICATION->AuthForm($arAuthResult);
	}
}

if(isset($REDIRECT_STATUS) && $REDIRECT_STATUS==404)
{
	if(COption::GetOptionString("main", "header_200", "N")=="Y")
		CHTTP::SetStatus("200 OK");
}
?>