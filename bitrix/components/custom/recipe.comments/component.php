<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

require_once($_SERVER["DOCUMENT_ROOT"].'/classes/comment.class.php');
if($_REQUEST['delete_comment_id'])
{
	if(CModule::IncludeModule("iblock"))
	{
		
		preg_match("/^root([0-9]+)_id/", $_REQUEST['delete_comment_id'], $matches, PREG_OFFSET_CAPTURE);

		if(intval($matches[1][0]))
		{
			$intComment = intval($matches[1][0]);
			$rsC = CIBlockElement::GetByID($intComment);
			$arC = $rsC->Fetch();
			
			if($USER->IsAdmin() || $arC['CREATED_BY'] == $USER->GetID())
			{
				echo $intComment." ".$_REQUEST['recipe'];
				$obComment = CFClubComment::getInstance();
				if($obComment->delete($intComment)){
					//LocalRedirect("/detail/".IntVal($_REQUEST['recipe'])."/#add_opinion");
				} else {
					echo '<div class="error_message">&mdash; При удалении комментария произошла ошибка.</div>';
				}
			}
		}
		
	}	
}


if(intval($arParams["RECIPE_ID"]) <= 0){
	ShowError("Ошибка");
	@define("ERROR_404", "Y");
	if($arParams["SET_STATUS_404"]==="Y")
		CHTTP::SetStatus("404 Not Found");
}

//$APPLICATION->AddHeadString('<link rel="stylesheet" type="text/css" href="/bitrix/components/custom/recipe.comments/templates/.default/style.css">');
$APPLICATION->SetAdditionalCSS("/bitrix/components/custom/recipe.comments/templates/custom/static/style.css",true);

CPageOption::SetOptionString("main", "nav_page_in_session", "N");

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;

$arParams["NEWS_COUNT"] = intval($arParams["NEWS_COUNT"]);
if($arParams["NEWS_COUNT"]<=0)
	$arParams["NEWS_COUNT"] = 20;

$arParams["CACHE_FILTER"] = $arParams["CACHE_FILTER"]=="Y";
if(!$arParams["CACHE_FILTER"] && count($arrFilter)>0)
	$arParams["CACHE_TIME"] = 0;

$arParams["SET_TITLE"] = $arParams["SET_TITLE"]!="N";

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

$arResult = array();
$arResult["ID"] = $arParams["RECIPE_ID"];
$cache_time = 3600;
$comment_cache_id = "comments_id".$arParams["RECIPE_ID"]."_".intval(CUser::GetID());
$comment_cache_dir = "/recipes_comments_cache/id".$arParams["RECIPE_ID"]."/".intval(CUser::GetID())."/";
$obCache = new CPHPCache;
if($obCache->InitCache($cache_time, $comment_cache_id, $comment_cache_dir)){
	$arComments = $obCache->GetVars();
}elseif(CModule::IncludeModule("iblock") && $obCache->StartDataCache()){
	global $CACHE_MANAGER;
	$CACHE_MANAGER->StartTagCache($comment_cache_dir);
	require_once($_SERVER["DOCUMENT_ROOT"].'/classes/comment.class.php');
	CModule::IncludeModule("iblock");
	$obComment = CFClubComment::getInstance();
	$arComments = $obComment->getList($arParams["RECIPE_ID"]);
	$arLikes = $obComment->getLikes($arComments["COMMENT_ID"]);
	$arComments["LIKES"] = $arLikes;	
	if(CUser::IsAuthorized()){
		$arUserLikes = $obComment->getUserLikes(CUser::GetID(),$arComments["COMMENT_ID"]);
		$arComments["USER_LIKES"] = $arUserLikes;
	}
	if($arComments !== false){
		foreach($arComments["COMMENTS"] as $arComment){
	 		$CACHE_MANAGER->RegisterTag("recipe_comments#".$arParams["RECIPE_ID"]."_comment#".$arComment["ID"]);
	 	}				
	}else{
	 	$arComments = array();
	}
	$CACHE_MANAGER->RegisterTag("recipe_comments#".$arParams["RECIPE_ID"]);
	$CACHE_MANAGER->EndTagCache();
	$obCache->EndDataCache($arComments);
}else{
	$arComments = array();
}




if(!empty($arComments)){
	$arResult["COMMENTS"] = $arComments["COMMENTS"];
	$arResult["IDS"] = $arComments["IDS"];
	$arResult["LIKES"] = $arComments["LIKES"];
	$arResult["USER_LIKES"] = $arComments["USER_LIKES"];
}

$this->IncludeComponentTemplate();

?>