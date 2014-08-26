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
		"PAGE_ID" => "user_forum"
	),
	$component
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
?>
<br />
<?$arInfo = $APPLICATION->IncludeComponent(
	"bitrix:socialnetwork.forum.topic.new",
	"",
	array(
		"FID"	=>	$arParams["FORUM_ID"],
		"MID" => $arResult["VARIABLES"]["message_id"],
		"MESSAGE_TYPE" => $arResult["VARIABLES"]["action"],
		"USER_ID" => $arResult["VARIABLES"]["user_id"],
		
		"URL_TEMPLATES_TOPIC_LIST" =>  $arResult["~PATH_TO_USER_FORUM"],
		"URL_TEMPLATES_MESSAGE" =>  $arResult["~PATH_TO_USER_FORUM_MESSAGE"],
		"URL_TEMPLATES_USER" =>  $arResult["~PATH_TO_USER"],
		
		"DATE_TIME_FORMAT" =>  $arResult["DATE_TIME_FORMAT"],
		"PATH_TO_SMILE" => $arParams["PATH_TO_FORUM_SMILE"],
		"PATH_TO_ICON"	=>	$arParams["PATH_TO_FORUM_ICON"],
		"AJAX_TYPE" => $arParams["AJAX_TYPE"],
		
		"SET_TITLE" => $arResult["SET_TITLE"],
		"CACHE_TIME" => $arResult["CACHE_TIME"],
		"CACHE_TYPE" => $arResult["CACHE_TYPE"]
	),
	$component,
	array("HIDE_ICONS" => "Y"));
?><?
if (!empty($arInfo) && $arInfo["PERMISSION"] > "I"):
?><?
$APPLICATION->IncludeComponent(
	"bitrix:socialnetwork.forum.post_form", 
	"", 
	Array(
		"FID"	=>	$arParams["FORUM_ID"],
		"TID"	=>	$arResult["VARIABLES"]["topic_id"],
		"MID"	=>	$arResult["VARIABLES"]["message_id"],
		"USER_ID" => $arResult["VARIABLES"]["user_id"], 
		"PAGE_NAME"	=>	"user_forum_topic_edit",
		"MESSAGE_TYPE"	=>	$_REQUEST["MESSAGE_TYPE"],
		"FORUM" => $arInfo["FORUM"],
		"TOPIC" => $arInfo["TOPIC"],
		"PERMISSION" => $arInfo["PERMISSION"],
		"bVarsFromForm" => $arInfo["bVarsFromForm"],
		"ERROR_MESSAGE" => $arInfo["ERROR_MESSAGE"],
		
		"URL_TEMPLATES_TOPIC_LIST" =>  $arResult["~PATH_TO_USER_FORUM"],
		"URL_TEMPLATES_MESSAGE" => $arResult["~PATH_TO_USER_FORUM_MESSAGE"],
		
		"PATH_TO_SMILE"	=>	$arParams["PATH_TO_FORUM_SMILE"],
		"PATH_TO_ICON"	=>	$arParams["PATH_TO_FORUM_ICON"],
		"SMILE_TABLE_COLS" => $arParams["SMILE_TABLE_COLS"],
		"AJAX_TYPE" => $arParams["AJAX_TYPE"],
		
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		
		"SHOW_TAGS" => $arParams["SHOW_TAGS"],
	),
	$component,
	array("HIDE_ICONS" => "Y"));
?><?
endif;
?>