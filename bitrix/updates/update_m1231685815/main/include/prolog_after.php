<?
/*
##############################################
# Bitrix Site Manager                        #
# Copyright (c) 2002-2007 Bitrix             #
# http://www.bitrixsoft.com                  #
# mailto:admin@bitrixsoft.com                #
##############################################
*/

if(!headers_sent())
	header("Content-type: text/html; charset=".LANG_CHARSET);

if(defined("DEMO") && DEMO=="Y" && ($SiteExpireDate>mktime(0,0,0,Date("m"),Date("d")+30,Date("Y")) || $SiteExpireDate <= mktime(0,0,0,Date("m"),Date("d"),Date("Y"))))
	echo GetMessage("expire_mess1");
if(defined("DEMO") && DEMO=="Y" && OLDSITEEXPIREDATE!=SITEEXPIREDATE)
	die(GetMessage("expire_mess2"));

if(COption::GetOptionString("main", "site_stopped", "N")=="Y" && !$GLOBALS["USER"]->CanDoOperation('edit_other_settings'))
{
	if(file_exists($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/php_interface/".LANG."/site_closed.php"))
		include($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/php_interface/".LANG."/site_closed.php");
	elseif(file_exists($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/php_interface/include/site_closed.php"))
		include($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/php_interface/include/site_closed.php");
	else
		include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/site_closed.php");
	die();
}

if (isset($_GET['bx_template_preview_mode']) && $_GET['bx_template_preview_mode'] == 'Y' && $USER->CanDoOperation('edit_other_settings'))
	@include_once($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/tmp/templates/__bx_preview/header.php");
else
	include_once($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".SITE_TEMPLATE_ID."/header.php");

/* Draw edit menu for whole content */
global $BX_GLOBAL_AREA_EDIT_ICON;
$BX_GLOBAL_AREA_EDIT_ICON = false;
if($GLOBALS['APPLICATION']->GetShowIncludeAreas() && $GLOBALS['APPLICATION']->GetPublicShowMode() == "edit")
{
	$documentRoot = CSite::GetSiteDocRoot(SITE_ID);
	if(isset($_SERVER["REAL_FILE_PATH"]) && $_SERVER["REAL_FILE_PATH"] != "")
		$currentFilePath = $_SERVER["REAL_FILE_PATH"];
	else
		$currentFilePath = $GLOBALS['APPLICATION']->GetCurPage();

	$bCanEdit = false;
	if(is_file($documentRoot.$currentFilePath) && $GLOBALS["USER"]->CanDoFileOperation("fm_edit_existent_file", array(SITE_ID, $currentFilePath)))
		$bCanEdit = true;

	if(IsModuleInstalled("fileman") && $bCanEdit)
		$bCanEdit = ($GLOBALS["USER"]->CanDoOperation("fileman_admin_files") && $GLOBALS["USER"]->CanDoOperation("fileman_edit_existent_files"));

	if($bCanEdit)
	{
		echo $GLOBALS['APPLICATION']->IncludeStringBefore();
		$BX_GLOBAL_AREA_EDIT_ICON = true;
	}
}
?>