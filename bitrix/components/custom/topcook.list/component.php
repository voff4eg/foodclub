<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

CPageOption::SetOptionString("main", "nav_page_in_session", "N");

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;

$arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"]);
if(strlen($arParams["IBLOCK_TYPE"])<=0)
 	$arParams["IBLOCK_TYPE"] = "news";
$arParams["IBLOCK_ID"] = trim($arParams["IBLOCK_ID"]);
$arParams["PARENT_SECTION"] = intval($arParams["PARENT_SECTION"]);
$arParams["INCLUDE_SUBSECTIONS"] = $arParams["INCLUDE_SUBSECTIONS"]!="N";

$arParams["SORT_BY1"] = trim($arParams["SORT_BY1"]);
if(strlen($arParams["SORT_BY1"])<=0)
	$arParams["SORT_BY1"] = "ACTIVE_FROM";
$arParams["SORT_ORDER1"] = strtoupper($arParams["SORT_ORDER1"]);
if($arParams["SORT_ORDER1"]!="ASC")
	 $arParams["SORT_ORDER1"]="DESC";
if(strlen($arParams["SORT_BY2"])<=0)
	$arParams["SORT_BY2"] = "SORT";
$arParams["SORT_ORDER2"] = strtoupper($arParams["SORT_ORDER2"]);
if($arParams["SORT_ORDER2"]!="DESC")
	 $arParams["SORT_ORDER2"]="ASC";

if(strlen($arParams["FILTER_NAME"])<=0 || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"]))
{
	$arrFilter = array();
}
else
{
	$arrFilter = $GLOBALS[$arParams["FILTER_NAME"]];
	if(!is_array($arrFilter))
		$arrFilter = array();
}

$arParams["CHECK_DATES"] = $arParams["CHECK_DATES"]!="N";

if(!is_array($arParams["FIELD_CODE"]))
	$arParams["FIELD_CODE"] = array();
foreach($arParams["FIELD_CODE"] as $key=>$val)
	if(!$val)
		unset($arParams["FIELD_CODE"][$key]);

if(!is_array($arParams["PROPERTY_CODE"]))
	$arParams["PROPERTY_CODE"] = array();
foreach($arParams["PROPERTY_CODE"] as $key=>$val)
	if($val==="")
		unset($arParams["PROPERTY_CODE"][$key]);

$arParams["DETAIL_URL"]=trim($arParams["DETAIL_URL"]);

$arParams["NEWS_COUNT"] = intval($arParams["NEWS_COUNT"]);
if($arParams["NEWS_COUNT"]<=0)
	$arParams["NEWS_COUNT"] = 20;

$arParams["CACHE_FILTER"] = $arParams["CACHE_FILTER"]=="Y";
if(!$arParams["CACHE_FILTER"] && count($arrFilter)>0)
	$arParams["CACHE_TIME"] = 0;

$arParams["SET_TITLE"] = $arParams["SET_TITLE"]!="N";
$arParams["ADD_SECTIONS_CHAIN"] = $arParams["ADD_SECTIONS_CHAIN"]!="N"; //Turn on by default
$arParams["INCLUDE_IBLOCK_INTO_CHAIN"] = $arParams["INCLUDE_IBLOCK_INTO_CHAIN"]!="N";
$arParams["ACTIVE_DATE_FORMAT"] = trim($arParams["ACTIVE_DATE_FORMAT"]);
if(strlen($arParams["ACTIVE_DATE_FORMAT"])<=0)
	$arParams["ACTIVE_DATE_FORMAT"] = $DB->DateFormatToPHP(CSite::GetDateFormat("SHORT"));
$arParams["PREVIEW_TRUNCATE_LEN"] = intval($arParams["PREVIEW_TRUNCATE_LEN"]);
$arParams["HIDE_LINK_WHEN_NO_DETAIL"] = $arParams["HIDE_LINK_WHEN_NO_DETAIL"]=="Y";

$arParams["DISPLAY_TOP_PAGER"] = $arParams["DISPLAY_TOP_PAGER"]=="Y";
$arParams["DISPLAY_BOTTOM_PAGER"] = $arParams["DISPLAY_BOTTOM_PAGER"]!="N";
$arParams["PAGER_TITLE"] = trim($arParams["PAGER_TITLE"]);
$arParams["PAGER_SHOW_ALWAYS"] = $arParams["PAGER_SHOW_ALWAYS"]!="N";
$arParams["PAGER_TEMPLATE"] = trim($arParams["PAGER_TEMPLATE"]);
$arParams["PAGER_DESC_NUMBERING"] = $arParams["PAGER_DESC_NUMBERING"]=="Y";
$arParams["PAGER_DESC_NUMBERING_CACHE_TIME"] = intval($arParams["PAGER_DESC_NUMBERING_CACHE_TIME"]);
$arParams["PAGER_SHOW_ALL"] = $arParams["PAGER_SHOW_ALL"]!=="N";

if($arParams["SET_TITLE"])
{
    $APPLICATION->SetTitle("Кулинары", $arTitleOptions);
}

if($arParams["DISPLAY_TOP_PAGER"] || $arParams["DISPLAY_BOTTOM_PAGER"])
{
	$arNavParams = array(
		"nPageSize" => $arParams["NEWS_COUNT"],
		"bDescPageNumbering" => $arParams["PAGER_DESC_NUMBERING"],
		"bShowAll" => $arParams["PAGER_SHOW_ALL"],
	);
	$arNavigation = CDBResult::GetNavParams($arNavParams);
	if($arNavigation["PAGEN"]==0 && $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"]>0)
		$arParams["CACHE_TIME"] = $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"];
}
else
{
	$arNavParams = array(
		"nTopCount" => $arParams["NEWS_COUNT"],
		"bDescPageNumbering" => $arParams["PAGER_DESC_NUMBERING"],
	);
	$arNavigation = false;
}

$arParams["USE_PERMISSIONS"] = $arParams["USE_PERMISSIONS"]=="Y";
if(!is_array($arParams["GROUP_PERMISSIONS"]))
	$arParams["GROUP_PERMISSIONS"] = array(1);

$bUSER_HAVE_ACCESS = !$arParams["USE_PERMISSIONS"];
if($arParams["USE_PERMISSIONS"] && isset($GLOBALS["USER"]) && is_object($GLOBALS["USER"]))
{
	$arUserGroupArray = $GLOBALS["USER"]->GetUserGroupArray();
	foreach($arParams["GROUP_PERMISSIONS"] as $PERM)
	{
		if(in_array($PERM, $arUserGroupArray))
		{
			$bUSER_HAVE_ACCESS = true;
			break;
		}
	}
}

if(!CModule::IncludeModule("iblock"))
{
	$this->AbortResultCache();
	ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
	return;
}
if(is_numeric($arParams["IBLOCK_ID"]))
{
	$rsIBlock = CIBlock::GetList(array(), array(
		"ACTIVE" => "Y",
		"ID" => $arParams["IBLOCK_ID"],
	));
}
else
{
	$rsIBlock = CIBlock::GetList(array(), array(
		"ACTIVE" => "Y",
		"CODE" => $arParams["IBLOCK_ID"],
		"SITE_ID" => SITE_ID,
	));
}
if($arResult = $rsIBlock->GetNext())
{
	$arResult["USER_HAVE_ACCESS"] = $bUSER_HAVE_ACCESS;
	//SELECT
	$arSelect = array_merge($arParams["FIELD_CODE"], array(
		"ID",
		"IBLOCK_ID",
		"IBLOCK_SECTION_ID",
		"NAME",
		"ACTIVE_FROM",
		"DETAIL_PAGE_URL",
		"DETAIL_TEXT",
		"DETAIL_TEXT_TYPE",
		"PREVIEW_TEXT",
		"PREVIEW_TEXT_TYPE",
		"PREVIEW_PICTURE",
	));
	$bGetProperty = count($arParams["PROPERTY_CODE"])>0;
	if($bGetProperty)
		$arSelect[]="PROPERTY_*";
	//WHERE
	$arFilter = array (
		"IBLOCK_ID" => $arResult["ID"],
		"IBLOCK_LID" => SITE_ID,
		"ACTIVE" => "Y",
		"CHECK_PERMISSIONS" => "Y",
	);

	if($arParams["CHECK_DATES"])
		$arFilter["ACTIVE_DATE"] = "Y";

	$arParams["PARENT_SECTION"] = CIBlockFindTools::GetSectionID(
		$arParams["PARENT_SECTION"],
		$arParams["PARENT_SECTION_CODE"],
		array(
			"GLOBAL_ACTIVE" => "Y",
			"IBLOCK_ID" => $arResult["ID"],
		)
	);

	if($arParams["PARENT_SECTION"]>0)
	{
		$arFilter["SECTION_ID"] = $arParams["PARENT_SECTION"];
		if($arParams["INCLUDE_SUBSECTIONS"])
			$arFilter["INCLUDE_SUBSECTIONS"] = "Y";

		$arResult["SECTION"]= array("PATH" => array());
		$rsPath = GetIBlockSectionPath($arResult["ID"], $arParams["PARENT_SECTION"]);
		$rsPath->SetUrlTemplates("", $arParams["SECTION_URL"]);
		while($arPath=$rsPath->GetNext())
		{
			$arResult["SECTION"]["PATH"][] = $arPath;
		}
	}
	else
	{
		$arResult["SECTION"]= false;
	}
	//ORDER BY
	/*$arSort = array(
		$arParams["SORT_BY1"]=>$arParams["SORT_ORDER1"],
		$arParams["SORT_BY2"]=>$arParams["SORT_ORDER2"],
	);*/

	include_once $_SERVER["DOCUMENT_ROOT"].'/classes/main.class.php';
	if($APPLICATION->GetCurDir() == "/"){
							
		$arResult = CFClub::getInstance()->getTopRatedCookList(10,true);
			
	}else{
		
		$arResult = CFClub::getInstance()->getTopRatedCookList(20,false);			
	}

	$this->IncludeComponentTemplate();
}
else
{
	$this->AbortResultCache();
	ShowError(GetMessage("T_NEWS_NEWS_NA"));
	@define("ERROR_404", "Y");
	if($arParams["SET_STATUS_404"]==="Y")
		CHTTP::SetStatus("404 Not Found");
}

?>
