<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @global CDatabase $DB */
/** @global CUser $USER */
/** @global CMain $APPLICATION */


CPageOption::SetOptionString("main", "nav_page_in_session", "N");

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;

$arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"]);
if(strlen($arParams["IBLOCK_TYPE"])<=0)
	$arParams["IBLOCK_TYPE"] = "news";

$arParams["ELEMENT_ID"] = intval($arParams["~ELEMENT_ID"]);
if($arParams["ELEMENT_ID"] > 0 && $arParams["ELEMENT_ID"]."" != $arParams["~ELEMENT_ID"])
{
	ShowError(GetMessage("T_NEWS_DETAIL_NF"));
	@define("ERROR_404", "Y");
	if($arParams["SET_STATUS_404"]==="Y")
		CHTTP::SetStatus("404 Not Found");
	return;
}

$arParams["CHECK_DATES"] = $arParams["CHECK_DATES"]!="N";
if(!is_array($arParams["FIELD_CODE"]))
	$arParams["FIELD_CODE"] = array();
foreach($arParams["FIELD_CODE"] as $key=>$val)
	if(!$val)
		unset($arParams["FIELD_CODE"][$key]);
if(!is_array($arParams["PROPERTY_CODE"]))
	$arParams["PROPERTY_CODE"] = array();
foreach($arParams["PROPERTY_CODE"] as $k=>$v)
	if($v==="")
		unset($arParams["PROPERTY_CODE"][$k]);

$arParams["IBLOCK_URL"]=trim($arParams["IBLOCK_URL"]);

$arParams["META_KEYWORDS"]=trim($arParams["META_KEYWORDS"]);
if(strlen($arParams["META_KEYWORDS"])<=0)
	$arParams["META_KEYWORDS"] = "-";
$arParams["META_DESCRIPTION"]=trim($arParams["META_DESCRIPTION"]);
if(strlen($arParams["META_DESCRIPTION"])<=0)
	$arParams["META_DESCRIPTION"] = "-";
$arParams["BROWSER_TITLE"]=trim($arParams["BROWSER_TITLE"]);
if(strlen($arParams["BROWSER_TITLE"])<=0)
	$arParams["BROWSER_TITLE"] = "-";

$arParams["INCLUDE_IBLOCK_INTO_CHAIN"] = $arParams["INCLUDE_IBLOCK_INTO_CHAIN"]!="N";
$arParams["ADD_SECTIONS_CHAIN"] = $arParams["ADD_SECTIONS_CHAIN"]!="N"; //Turn on by default
$arParams["ADD_ELEMENT_CHAIN"] = (isset($arParams["ADD_ELEMENT_CHAIN"]) && $arParams["ADD_ELEMENT_CHAIN"] == "Y");
$arParams["SET_TITLE"]=$arParams["SET_TITLE"]!="N";
$arParams["SET_BROWSER_TITLE"] = (isset($arParams["SET_BROWSER_TITLE"]) && $arParams["SET_BROWSER_TITLE"] === 'N' ? 'N' : 'Y');
$arParams["SET_META_KEYWORDS"] = (isset($arParams["SET_META_KEYWORDS"]) && $arParams["SET_META_KEYWORDS"] === 'N' ? 'N' : 'Y');
$arParams["SET_META_DESCRIPTION"] = (isset($arParams["SET_META_DESCRIPTION"]) && $arParams["SET_META_DESCRIPTION"] === 'N' ? 'N' : 'Y');
$arParams["ACTIVE_DATE_FORMAT"] = trim($arParams["ACTIVE_DATE_FORMAT"]);
if(strlen($arParams["ACTIVE_DATE_FORMAT"])<=0)
	$arParams["ACTIVE_DATE_FORMAT"] = $DB->DateFormatToPHP(CSite::GetDateFormat("SHORT"));

$arParams["DISPLAY_TOP_PAGER"] = $arParams["DISPLAY_TOP_PAGER"]=="Y";
$arParams["DISPLAY_BOTTOM_PAGER"] = $arParams["DISPLAY_BOTTOM_PAGER"]!="N";
$arParams["PAGER_TITLE"] = trim($arParams["PAGER_TITLE"]);
$arParams["PAGER_SHOW_ALWAYS"] = $arParams["PAGER_SHOW_ALWAYS"]!="N";
$arParams["PAGER_TEMPLATE"] = trim($arParams["PAGER_TEMPLATE"]);
$arParams["PAGER_SHOW_ALL"] = $arParams["PAGER_SHOW_ALL"]!=="N";

if($arParams["DISPLAY_TOP_PAGER"] || $arParams["DISPLAY_BOTTOM_PAGER"])
{
	$arNavParams = array(
		"nPageSize" => 1,
		"bShowAll" => $arParams["PAGER_SHOW_ALL"],
	);
	$arNavigation = CDBResult::GetNavParams($arNavParams);
}
else
{
	$arNavigation = false;
}

$arParams["SHOW_WORKFLOW"] = $_REQUEST["show_workflow"]=="Y";

$arParams["USE_PERMISSIONS"] = $arParams["USE_PERMISSIONS"]=="Y";
if(!is_array($arParams["GROUP_PERMISSIONS"]))
	$arParams["GROUP_PERMISSIONS"] = array(1);

$bUSER_HAVE_ACCESS = !$arParams["USE_PERMISSIONS"];
if($arParams["USE_PERMISSIONS"] && isset($USER) && is_object($USER))
{
	$arUserGroupArray = $USER->GetUserGroupArray();
	foreach($arParams["GROUP_PERMISSIONS"] as $PERM)
	{
		if(in_array($PERM, $arUserGroupArray))
		{
			$bUSER_HAVE_ACCESS = true;
			break;
		}
	}
}
if(!$bUSER_HAVE_ACCESS)
{
	ShowError(GetMessage("T_NEWS_DETAIL_PERM_DEN"));
	return 0;
}

if(isset($_REQUEST['f'])){
	if($USER->IsAuthorized())
	{
		if($_REQUEST['f'] == "y")
		{
			if(!CModule::IncludeModule("iblock")){
				$this->AbortResultCache();
				ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
				return;
			}
			if(intval($arParams["ELEMENT_ID"])){
				$rsRecipe = CIBlockElement::GetList(array(),array("IBLOCK_CODE" => "recipe", "SITE_ID" => SITE_ID, "ID" => intval($arParams["ELEMENT_ID"])), false, false, array("ID","CREATED_BY"));
			}elseif($arParams["ELEMENT_CODE"]){
				$rsRecipe = CIBlockElement::GetList(array(),array("IBLOCK_CODE" => "recipe", "SITE_ID" => SITE_ID, "CODE" => $arParams["ELEMENT_CODE"]), false, false, array("ID","CREATED_BY"));
			}else{
				$rsRecipe = false;	
			}
			
			if($rsRecipe){
				if($arRecipe = $rsRecipe->Fetch()){
					$isOwner = false;
					if($USER->IsAuthorized()){
						if($arRecipe['CREATED_BY'] == $USER->GetID()){
							$isOwner = true;
						}
					}
					$Favorite = new CFavorite;
					$Favorite->add($arRecipe["ID"]);

					if(!$isOwner){
						$CMark = new CMark;
						$CMark->updateUserRait($arRecipe["CREATED_BY"],$way = "up","r_favorite");
					}
					$cache_dir = "/recipe_favorite_".(strlen($arParams["ELEMENT_CODE"]) ? $arParams["ELEMENT_CODE"] : $arParams["ELEMENT_ID"]);
					BXClearCache(true, $cache_dir);
					//global $CACHE_MANAGER;
					//$CACHE_MANAGER->ClearByTag("profile_".$USER->GetId()."_favorites");
					LocalRedirect($APPLICATION->GetCurPageParam("", array("r", "c", "f","place")));
				}
			}
		}
		elseif($_REQUEST['f'] == "n")
		{
			if(!CModule::IncludeModule("iblock")){
				$this->AbortResultCache();
				ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
				return;
			}
			if(intval($arParams["ELEMENT_ID"])){
				$rsRecipe = CIBlockElement::GetList(array(),array("IBLOCK_CODE" => "recipe", "SITE_ID" => SITE_ID, "ID" => intval($arParams["ELEMENT_ID"])), false, false, array("ID","CREATED_BY"));
			}elseif($arParams["ELEMENT_CODE"]){
				$rsRecipe = CIBlockElement::GetList(array(),array("IBLOCK_CODE" => "recipe", "SITE_ID" => SITE_ID, "CODE" => $arParams["ELEMENT_CODE"]), false, false, array("ID","CREATED_BY"));
			}else{
				$rsRecipe = false;	
			}
			if($rsRecipe){
				if($arRecipe = $rsRecipe->Fetch()){
					$isOwner = false;
					if($USER->IsAuthorized()){
						if($arRecipe['CREATED_BY'] == $USER->GetID()){
							$isOwner = true;
						}
					}
					$Favorite = new CFavorite;
					$Favorite->delete($arRecipe["ID"]);				
					if(!$isOwner){
						$CMark = new CMark;
						$CMark->updateUserRait($arRecipe["CREATED_BY"],$way = "low","r_favorite");
					}
					$cache_dir = "/recipe_favorite_".(strlen($arParams["ELEMENT_CODE"]) ? $arParams["ELEMENT_CODE"] : $arParams["ELEMENT_ID"]);
					BXClearCache(true, $cache_dir);
					//global $CACHE_MANAGER;
					//$CACHE_MANAGER->ClearByTag("profile_".$USER->GetId()."_favorites");
					LocalRedirect($APPLICATION->GetCurPageParam("", array("r", "c", "f","place")));
				}
			}
		}
	}
	else
	{
		LocalRedirect('/auth/?backurl='.$APPLICATION->GetCurDir().'?f='.$_REQUEST['f']);
	}
}

if(isset($_REQUEST["cant_delete"]) || isset($_REQUEST["cant_edit"])){
	$cache_dir = "/recipe_favorite_".(strlen($arParams["ELEMENT_CODE"]) ? $arParams["ELEMENT_CODE"] : $arParams["ELEMENT_ID"]);
	BXClearCache(true, $cache_dir);
}

$cache_dir = "/recipe_favorite_".(strlen($arParams["ELEMENT_CODE"]) ? $arParams["ELEMENT_CODE"] : $arParams["ELEMENT_ID"]);
require_once($_SERVER["DOCUMENT_ROOT"]."/classes/favorite.class.php");

if($arParams["SHOW_WORKFLOW"] || $this->StartResultCache(false, array(($arParams["CACHE_GROUPS"]==="N"? false: $USER->GetGroups()),$bUSER_HAVE_ACCESS, $arNavigation), $cache_dir))
{

	if(!CModule::IncludeModule("iblock"))
	{
		$this->AbortResultCache();
		ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
		return;
	}

	if(intval($arParams["ELEMENT_ID"])){
		$rsElement = CIBlockElement::GetList(array(),array("IBLOCK_CODE" => "recipe", "ACTIVE" => "Y", "SITE_ID" => SITE_ID, "ID" => intval($arParams["ELEMENT_ID"])), false, false, array("ID", "CODE", "CREATED_BY", "PROPERTY_edit_deadline"));
	}elseif($arParams["ELEMENT_CODE"]){
		$rsElement = CIBlockElement::GetList(array(),array("IBLOCK_CODE" => "recipe", "ACTIVE" => "Y", "SITE_ID" => SITE_ID, "CODE" => $arParams["ELEMENT_CODE"]), false, false, array("ID", "CODE", "CREATED_BY", "PROPERTY_edit_deadline"));
	}else{
		$this->AbortResultCache();
		ShowError(GetMessage("T_NEWS_DETAIL_NF"));
		@define("ERROR_404", "Y");
		if($arParams["SET_STATUS_404"]==="Y")
			CHTTP::SetStatus("404 Not Found");
		return;
	}
	
	$rsElement->SetUrlTemplates($arParams["DETAIL_URL"], "", $arParams["IBLOCK_URL"]);
	if($arElement = $rsElement->GetNext())
	{
		$arResult = $arElement;

		if($arResult["PROPERTY_EDIT_DEADLINE_VALUE"]){
			$bAllowEdit = ((!($USER->IsAdmin()) && (MakeTimeStamp($arResult["PROPERTY_EDIT_DEADLINE_VALUE"]) >= time())) || $USER->IsAdmin());
		}else{
			$bAllowEdit = ($USER->IsAdmin());
		}

		$arResult["bAllowEdit"] = $bAllowEdit;

		$this->SetResultCacheKeys(array(
			"ID",
			"CODE",
			"CREATED_BY",
			"bAllowEdit"
		));

		$this->IncludeComponentTemplate();
	}
	else
	{
		$this->AbortResultCache();
		ShowError(GetMessage("T_NEWS_DETAIL_NF"));
		@define("ERROR_404", "Y");
		if($arParams["SET_STATUS_404"]==="Y")
			CHTTP::SetStatus("404 Not Found");
	}
}

if(isset($arResult["ID"]))
{
	return $arResult["ID"];
}
else
{
	return 0;
}
?>