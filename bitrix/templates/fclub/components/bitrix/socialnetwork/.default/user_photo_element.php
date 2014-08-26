<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$APPLICATION->IncludeComponent(
	"bitrix:socialnetwork.user_menu",
	"",
	Array(
		"USER_VAR" => $arResult["ALIASES"]["user_id"],
		"PAGE_VAR" => $arResult["ALIASES"]["page"],
		"PATH_TO_USER" => $arResult["PATH_TO_USER"],
		"PATH_TO_USER_EDIT" => $arResult["PATH_TO_USER_PROFILE_EDIT"],
		"PATH_TO_USER_FRIENDS" => $arResult["PATH_TO_USER_FRIENDS"],
		"PATH_TO_USER_GROUPS" => $arResult["PATH_TO_USER_GROUPS"],
		"PATH_TO_USER_FRIENDS_ADD" => $arResult["PATH_TO_USER_FRIENDS_ADD"],
		"PATH_TO_USER_FRIENDS_DELETE" => $arResult["PATH_TO_USER_FRIENDS_DELETE"],
		"PATH_TO_MESSAGES_INPUT" => $arResult["PATH_TO_MESSAGES_INPUT"],
		"PATH_TO_MESSAGE_FORM" => $arResult["PATH_TO_MESSAGE_FORM"],
		"PATH_TO_USER_BLOG" => $arResult["PATH_TO_USER_BLOG"],
		"PATH_TO_USER_PHOTO" => $arResult["PATH_TO_USER_PHOTO"],
		"PATH_TO_USER_FORUM" => $arResult["PATH_TO_USER_FORUM"],
		"PATH_TO_USER_CALENDAR" => $arResult["PATH_TO_USER_CALENDAR"],
		"PATH_TO_USER_FILES" => $arResult["PATH_TO_USER_FILES"],
		"PATH_TO_USER_TASKS" => $arResult["PATH_TO_USER_TASKS"],
		"ID" => $arResult["VARIABLES"]["user_id"],
		"PAGE_ID" => "user_photo"
	),
	$component,
	array("HIDE_ICONS" => "Y")
);
?>

<br />

<?$APPLICATION->IncludeComponent(
	"bitrix:socialnetwork.user_profile", 
	"short", 
	Array(
		"PATH_TO_USER" => $arResult["PATH_TO_USER"],
		"PATH_TO_USER_EDIT" => $arResult["PATH_TO_USER_PROFILE_EDIT"],
		"PATH_TO_USER_FRIENDS" => $arResult["PATH_TO_USER_FRIENDS"],
		"PATH_TO_USER_GROUPS" => $arResult["PATH_TO_USER_GROUPS"],
		"PATH_TO_USER_FRIENDS_ADD" => $arResult["PATH_TO_USER_FRIENDS_ADD"],
		"PATH_TO_USER_FRIENDS_DELETE" => $arResult["PATH_TO_USER_FRIENDS_DELETE"],
		"PATH_TO_MESSAGE_FORM" => $arResult["PATH_TO_MESSAGE_FORM"],
		"PATH_TO_MESSAGES_CHAT" => $arResult["PATH_TO_MESSAGES_CHAT"],
		"PATH_TO_MESSAGES_USERS_MESSAGES" => $arResult["PATH_TO_MESSAGES_USERS_MESSAGES"],
		"PATH_TO_USER_SETTINGS_EDIT" => $arResult["PATH_TO_USER_SETTINGS_EDIT"],
		"PATH_TO_GROUP" => $arParams["PATH_TO_GROUP"],
		"PATH_TO_GROUP_CREATE" => $arResult["PATH_TO_GROUP_CREATE"],
		"PATH_TO_USER_FEATURES" => $arResult["PATH_TO_USER_FEATURES"],
		"PAGE_VAR" => $arResult["ALIASES"]["page"],
		"USER_VAR" => $arResult["ALIASES"]["user_id"],
		"SET_TITLE" => "N", 
		"USER_PROPERTY_MAIN" => $arResult["USER_PROPERTY_MAIN"],
		"USER_PROPERTY_CONTACT" => $arResult["USER_PROPERTY_CONTACT"],
		"USER_PROPERTY_PERSONAL" => $arResult["USER_PROPERTY_PERSONAL"],
		"USER_FIELDS_MAIN" => $arResult["USER_FIELDS_MAIN"],
		"USER_FIELDS_CONTACT" => $arResult["USER_FIELDS_CONTACT"],
		"USER_FIELDS_PERSONAL" => $arResult["USER_FIELDS_PERSONAL"],
		"PATH_TO_USER_FEATURES" => $arResult["PATH_TO_USER_FEATURES"],
		"DATE_TIME_FORMAT" => $arResult["DATE_TIME_FORMAT"],
		"SHORT_FORM" => "Y",
		"ITEMS_COUNT" => $arParams["ITEM_MAIN_COUNT"],
		"ID" => $arResult["VARIABLES"]["user_id"],
		"PATH_TO_GROUP_REQUEST_GROUP_SEARCH" => $arResult["PATH_TO_GROUP_REQUEST_GROUP_SEARCH"], 
		"PATH_TO_CONPANY_DEPARTMENT" => $arParams["PATH_TO_CONPANY_DEPARTMENT"], 
	),
	$component,
	array("HIDE_ICONS" => "Y") 
);
?><?
if ($arParams["FATAL_ERROR"] == "Y"):
	if (!empty($arParams["ERROR_MESSAGE"])):
		ShowError($arParams["ERROR_MESSAGE"]);
	else:
		ShowNote($arParams["NOTE_MESSAGE"], "notetext-simple");
	endif;
	return false;
endif;

?>
<?$APPLICATION->IncludeComponent(
	"bitrix:photogallery.user",
	".default",
	Array(
		"IBLOCK_TYPE" => $arParams["PHOTO_USER_IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["PHOTO_USER_IBLOCK_ID"],
		"PAGE_NAME" => "INDEX",
		"USER_ALIAS" => $arResult["VARIABLES"]["GALLERY"]["CODE"],
		"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
		"PERMISSION" => $arResult["VARIABLES"]["PERMISSION"],
		
		"SORT_BY" => $arParams["PHOTO"]["ALL"]["SECTION_SORT_BY"],
		"SORT_ORD" => $arParams["PHOTO"]["ALL"]["SECTION_SORT_ORD"],
		
		"INDEX_URL" => $arResult["~PATH_TO_USER_PHOTO"],
		"GALLERY_URL" => $arResult["~PATH_TO_USER_PHOTO"],
		"GALLERIES_URL" => $arResult["~PATH_TO_USER_PHOTO_GALLERIES"],
		"GALLERY_EDIT_URL" => $arResult["~PATH_TO_USER_PHOTO_GALLERY_EDIT"],
		"UPLOAD_URL" => $arResult["~PATH_TO_USER_PHOTO_ELEMENT_UPLOAD"],
		
		"ONLY_ONE_GALLERY" => $arParams["PHOTO"]["ALL"]["ONLY_ONE_GALLERY"],
		"GALLERY_GROUPS" => $arParams["PHOTO"]["ALL"]["GALLERY_GROUPS"],
		"GALLERY_SIZE" => $arParams["PHOTO"]["ALL"]["GALLERY_SIZE"],
		
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"DISPLAY_PANEL" => $arParams["DISPLAY_PANEL"],
		
		"GALLERY_AVATAR_SIZE"	=>	$arParams["GALLERY_AVATAR_SIZE"]
	),
	$component,
	array("HIDE_ICONS" => "Y")
);?>
<br />
<?$ElementID = $APPLICATION->IncludeComponent(
	"bitrix:photogallery.detail",
	"",
	Array(
		"IBLOCK_TYPE" => $arParams["PHOTO_USER_IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["PHOTO_USER_IBLOCK_ID"],
		"USER_ALIAS" => $arResult["VARIABLES"]["GALLERY"]["CODE"],
		"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
		"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
 		"ELEMENT_ID" => $arResult["VARIABLES"]["ELEMENT_ID"],
 		"ELEMENT_CODE" => $arResult["VARIABLES"]["ELEMENT_CODE"],
		"PERMISSION" => $arResult["VARIABLES"]["PERMISSION"],
		"BEHAVIOUR" => "USER",
		
		"ELEMENT_SORT_FIELD" => $arParams["PHOTO"]["ALL"]["ELEMENT_SORT_FIELD"],
		"ELEMENT_SORT_ORDER" => $arParams["PHOTO"]["ALL"]["ELEMENT_SORT_ORDER"],
		
		"GALLERY_URL" => $arResult["~PATH_TO_USER_PHOTO"],
		"SECTION_URL" => $arResult["~PATH_TO_USER_PHOTO_SECTION"],
		"SECTION_EDIT_URL" => $arResult["~PATH_TO_USER_PHOTO_SECTION_EDIT"],
		"SECTION_EDIT_ICON_URL" => $arResult["~PATH_TO_USER_PHOTO_SECTION_EDIT_ICON"],
		"DETAIL_URL" => $arResult["~PATH_TO_USER_PHOTO_ELEMENT"],
		"DETAIL_EDIT_URL" => $arResult["~PATH_TO_USER_PHOTO_ELEMENT_EDIT"],
		"DETAIL_SLIDE_SHOW_URL" => $arResult["~PATH_TO_USER_PHOTO_ELEMENT_SLIDE_SHOW"],
		"UPLOAD_URL" => $arResult["~PATH_TO_USER_PHOTO_ELEMENT_UPLOAD"],
		"SEARCH_URL" => $arResult["~PATH_TO_USER_PHOTO_SEARCH"],
		
 		"DATE_TIME_FORMAT" => $arParams["PHOTO"]["ALL"]["DATE_TIME_FORMAT_DETAIL"],
 		"COMMENTS_TYPE" => $arParams["PHOTO"]["ALL"]["COMMENTS_TYPE"],
 		"THUMBS_SIZE" => $arParams["PHOTO"]["ALL"]["PREVIEW_SIZE"],
		"GALLERY_SIZE" => $arParams["PHOTO"]["ALL"]["GALLERY_SIZE"],
		
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"SET_TITLE" => $arParams["SET_TITLE"],
		"ADD_CHAIN_ITEM" => "N",
		"DISPLAY_PANEL" => $arParams["DISPLAY_PANEL"],
		
		"SHOW_TAGS" => $arParams["PHOTO"]["ALL"]["SHOW_TAGS"]),
	$component,
	array("HIDE_ICONS" => "Y")
);
?><?

if($arParams["PHOTO"]["ALL"]["USE_RATING"]=="Y"):
?><div id="photo_vote_source" style="display:none;"><?
$APPLICATION->IncludeComponent(
	"bitrix:iblock.vote",
	"ajax",
	Array(
		"IBLOCK_TYPE" => $arParams["PHOTO_USER_IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["PHOTO_USER_IBLOCK_ID"],
		"ELEMENT_ID" => $ElementID,
		"MAX_VOTE" => $arParams["PHOTO"]["ALL"]["MAX_VOTE"],
		"VOTE_NAMES" => $arParams["PHOTO"]["ALL"]["VOTE_NAMES"],
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"DISPLAY_AS_RATING" => "rating"
	),
	$component,
	array("HIDE_ICONS" => "Y")
);
?></div>
<script>
function to_show_vote()
{
	if (document.getElementById('photo_vote') && document.getElementById('vote_<?=$ElementID?>'))
	{
		var _div = document.getElementById('vote_<?=$ElementID?>');
		var div = document.getElementById('vote_<?=$ElementID?>').cloneNode(true);
		_div.id = 'temp';
		document.getElementById('photo_vote').appendChild(div);
	}
	else
	{
		document.getElementById('photo_vote_source').style.display = '';
	}
	
}
setTimeout(to_show_vote, 100);
</script><?
endif;

// SLIDER
?><?$APPLICATION->IncludeComponent(
	"bitrix:photogallery.detail.list", 
	"slider", 
	Array(
		"IBLOCK_TYPE" => $arParams["PHOTO_USER_IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["PHOTO_USER_IBLOCK_ID"],
		"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
		"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
		"USER_ALIAS" => $arResult["VARIABLES"]["GALLERY"]["CODE"],
		"PERMISSION" => $arResult["VARIABLES"]["PERMISSION"],
		"BEHAVIOUR" => "USER",
 		"ELEMENT_ID" => $ElementID,
 		
		"ELEMENTS_LAST_COUNT" => "",
		"ELEMENT_LAST_TIME" => "",
		"ELEMENT_SORT_FIELD" => $arParams["PHOTO"]["ALL"]["ELEMENT_SORT_FIELD"],
		"ELEMENT_SORT_ORDER" => $arParams["PHOTO"]["ALL"]["ELEMENT_SORT_ORDER"],
		"ELEMENT_SORT_FIELD1" => "",
		"ELEMENT_SORT_ORDER1" => "",
		"ELEMENT_FILTER" => array(),
		
		"GALLERY_URL" => $arResult["~PATH_TO_USER_PHOTO"],
		"DETAIL_URL" => $arResult["~PATH_TO_USER_PHOTO_ELEMENT"],
		"SEARCH_URL"	=>	$arResult["~PATH_TO_USER_PHOTO_SEARCH"],
		"DETAIL_SLIDE_SHOW_URL"	=>	$arResult["~PATH_TO_USER_PHOTO_ELEMENT_SLIDE_SHOW"],
		
		"USE_PERMISSIONS" => $arParams["PHOTO"]["ALL"]["USE_PERMISSIONS"],
		"GROUP_PERMISSIONS" => $arParams["PHOTO"]["ALL"]["GROUP_PERMISSIONS"],
		
		"USE_DESC_PAGE" => $arParams["PHOTO"]["ALL"]["ELEMENTS_USE_DESC_PAGE"],
		"PAGE_ELEMENTS" => "0",
		"PAGE_NAVIGATION_TEMPLATE" => $arParams["PHOTO"]["ALL"]["PAGE_NAVIGATION_TEMPLATE"],
		
 		"DATE_TIME_FORMAT" => $arParams["PHOTO"]["ALL"]["DATE_TIME_FORMAT_DETAIL"],
		"COMMENTS_TYPE" => $arParams["PHOTO"]["ALL"]["COMMENTS_TYPE"],
		
		"ADDITIONAL_SIGHTS" => $arParams["PHOTO"]["ALL"]["~ADDITIONAL_SIGHTS"],
		"PICTURES_SIGHT" => "",
		"GALLERY_SIZE" => $arParams["PHOTO"]["ALL"]["GALLERY_SIZE"],
		"GET_GALLERY_INFO" => "N",
		
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"SET_TITLE" => "N",

		
		"THUMBS_SIZE"	=>	$arParams["PHOTO"]["ALL"]["THUMBS_SIZE"],
		"SHOW_PAGE_NAVIGATION"	=>	"none",
		"ELEMENT_ID" => $ElementID,
		"SLIDER_COUNT_CELL" => $arParams["PHOTO"]["TEMPLATE"]["SLIDER_COUNT_CELL"],
		"SHOW_DESCRIPTION" => "Y"
	),
	$component,
	array("HIDE_ICONS" => "Y")
);

// COMMENTS
if ($arParams["PHOTO"]["ALL"]["USE_COMMENTS"] == "Y" && $arParams["PHOTO"]["ALL"]["COMMENTS_TYPE"] != "none"):
	?><div class="empty-clear before-comment"></div><?
	$arCommentsParams = Array(
		"IBLOCK_TYPE" => $arParams["PHOTO_USER_IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["PHOTO_USER_IBLOCK_ID"],
 		"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
		"PERMISSION" => $arResult["VARIABLES"]["PERMISSION"],
 		"ELEMENT_ID" => $ElementID,
 		"COMMENTS_TYPE" => $arParams["PHOTO"]["ALL"]["COMMENTS_TYPE"],
		"BEHAVIOUR" => "USER",
		
		"DETAIL_URL" => $arResult["~PATH_TO_USER_PHOTO_ELEMENT"],
		
 		"COMMENTS_COUNT" => $arParams["PHOTO"]["ALL"]["COMMENTS_COUNT"],
		"PATH_TO_SMILE" => $arParams["PHOTO"]["ALL"]["PATH_TO_SMILE"],
		
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"]);

	$arCommentsParams["COMMENTS_TYPE"] = (strToLower($arParams["PHOTO"]["ALL"]["COMMENTS_TYPE"]) == "forum" ? "forum" : "blog");
	
	if ($arCommentsParams["COMMENTS_TYPE"] != "forum")
	{
		$arCommentsParams["COMMENTS_TYPE"] = "blog";
		$arCommentsParams["BLOG_URL"] = $arParams["PHOTO"]["ALL"]["BLOG_URL"];
		$arCommentsParams["PATH_TO_USER"] = $arParams["PHOTO"]["ALL"]["PATH_TO_USER"];
		$arCommentsParams["PATH_TO_BLOG"] = $arParams["PHOTO"]["ALL"]["PATH_TO_BLOG"];
	}
	else
	{
		$arCommentsParams["FORUM_ID"] = $arParams["PHOTO"]["ALL"]["FORUM_ID"];
		$arCommentsParams["USE_CAPTCHA"] = $arParams["PHOTO"]["ALL"]["USE_CAPTCHA"];
		$arCommentsParams["URL_TEMPLATES_READ"] = $arParams["PHOTO"]["ALL"]["URL_TEMPLATES_READ"];
		$arCommentsParams["PREORDER"] = ($arParams["PHOTO"]["ALL"]["PREORDER"] != "N" ? "Y" : "N");
		$arCommentsParams["SHOW_LINK_TO_FORUM"] = ($arParams["PHOTO"]["ALL"]["SHOW_LINK_TO_FORUM"] == "Y" ? "Y" : "N");
	}
	$APPLICATION->IncludeComponent(
		"bitrix:photogallery.detail.comment", 
		"", 
		$arCommentsParams,
		$component,
		array("HIDE_ICONS" => "Y"));
endif;
?>