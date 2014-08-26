<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

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
		"PAGE_ID" => "user_files"
	),
	$component
);?>

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
<br class="sn-br" />
<?$APPLICATION->IncludeComponent("bitrix:webdav.menu", ".default", Array(
	"IBLOCK_TYPE"	=>	$arParams["FILES_USER_IBLOCK_TYPE"],
	"IBLOCK_ID"	=>	$arParams["FILES_USER_IBLOCK_ID"],
	"ROOT_SECTION_ID"	=>	$arResult["VARIABLES"]["ROOT_SECTION_ID"],
	"SECTION_ID"	=>	$arResult["VARIABLES"]["SECTION_ID"],
	"ELEMENT_ID"	=>	$arResult["VARIABLES"]["ELEMENT_ID"],
	"PERMISSION"	=>	$arResult["VARIABLES"]["PERMISSION"],
	"PAGE_NAME" => "ELEMENT",
	"BASE_URL"	=>	$arResult["VARIABLES"]["BASE_URL"],
	
	"SECTIONS_URL" => $arResult["~PATH_TO_USER_FILES"],
	"SECTION_EDIT_URL" => $arResult["~PATH_TO_USER_FILES_SECTION_EDIT"],
	"ELEMENT_EDIT_URL" => $arResult["~PATH_TO_USER_FILES_ELEMENT_EDIT"],
	"ELEMENT_HISTORY_URL" => $arResult["~PATH_TO_USER_FILES_ELEMENT_HISTORY"],
	"ELEMENT_UPLOAD_URL" => $arResult["~PATH_TO_USER_FILES_ELEMENT_UPLOAD"],
	"HELP_URL" => $arResult["~PATH_TO_USER_FILES_HELP"],
	"USER_VIEW_URL" => $arResult["~PATH_TO_USER"],
	
	"FORUM_ID" => false, 
	"USE_COMMENTS"	=>	"N",
	
	"STR_TITLE" => $arResult["VARIABLES"]["STR_TITLE"]),
	$component
);
?><?
?><?$arInfo = $APPLICATION->IncludeComponent("bitrix:webdav.element.view", ".default", Array(
	"IBLOCK_TYPE"	=>	$arParams["FILES_USER_IBLOCK_TYPE"],
	"IBLOCK_ID"	=>	$arParams["FILES_USER_IBLOCK_ID"],
	"ROOT_SECTION_ID"	=>	$arResult["VARIABLES"]["ROOT_SECTION_ID"],
	"SECTION_ID"	=>	$arResult["VARIABLES"]["SECTION_ID"],
	"ELEMENT_ID"	=>	$arResult["VARIABLES"]["ELEMENT_ID"],
	"PERMISSION"	=>	$arResult["VARIABLES"]["PERMISSION"],
	"NAME_FILE_PROPERTY"	=>	$arParams["NAME_FILE_PROPERTY"],
	
	"SECTIONS_URL" => $arResult["~PATH_TO_USER_FILES"],
	"SECTION_EDIT_URL" => $arResult["~PATH_TO_USER_FILES_SECTION_EDIT"],
	"ELEMENT_URL" => $arResult["~PATH_TO_USER_FILES_ELEMENT"],
	"ELEMENT_EDIT_URL" => $arResult["~PATH_TO_USER_FILES_ELEMENT_EDIT"],
	"ELEMENT_HISTORY_URL" => $arResult["~PATH_TO_USER_FILES_ELEMENT_HISTORY"],
	"ELEMENT_HISTORY_GET_URL" => $arResult["~PATH_TO_USER_FILES_ELEMENT_HISTORY_GET"],
	"HELP_URL" => $arResult["~PATH_TO_USER_FILES_HELP"],
	"USER_VIEW_URL" => $arResult["~PATH_TO_USER"],
	
	"SET_TITLE"	=>	$arParams["SET_TITLE"],
	"STR_TITLE" => $arResult["VARIABLES"]["STR_TITLE"],
	"DISPLAY_PANEL"	=>	$arParams["DISPLAY_PANEL"],
	"CACHE_TYPE"	=>	$arParams["CACHE_TYPE"],
	"CACHE_TIME"	=>	$arParams["CACHE_TIME"], 
	
	"SHOW_WORKFLOW" => "N"),
	$component,
	array("HIDE_ICONS" => "Y")
);
?><?if(!empty($arInfo) && is_array($arInfo) && $arInfo["ELEMENT_ID"] && $arParams["FILES_USE_COMMENTS"]=="Y" && IsModuleInstalled("forum")):?>
<hr />
<?$APPLICATION->IncludeComponent(
	"bitrix:forum.topic.reviews",
	"",
	Array(
		"FORUM_ID" => $arParams["FILES_FORUM_ID"],
		"ELEMENT_ID" => $arInfo["ELEMENT_ID"],
		
		"URL_TEMPLATES_READ" => "",
		"URL_TEMPLATES_PROFILE_VIEW" => str_replace("#USER_ID#", "#UID#", $arResult["~PATH_TO_USER"]),
		"URL_TEMPLATES_DETAIL" => $arResult["~PATH_TO_USER_FILES_ELEMENT"],
		
		"POST_FIRST_MESSAGE" => "Y", 
		"POST_FIRST_MESSAGE_TEMPLATE" => GetMessage("WD_TEMPLATE_MESSAGE"), 
		"SUBSCRIBE_AUTHOR_ELEMENT" => "Y", 
		"IMAGE_SIZE" => false, 
		"MESSAGES_PER_PAGE" => 20,
		"DATE_TIME_FORMAT" => false, 
		"USE_CAPTCHA" => $arParams["FILES_USE_CAPTCHA"],
		"PREORDER" => $arParams["FILES_PREORDER"],
		"PAGE_NAVIGATION_TEMPLATE" => false, 
		"DISPLAY_PANEL" => "N", 
		
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		
		"PATH_TO_SMILE" => $arParams["FILES_PATH_TO_SMILE"],
		"SHOW_LINK_TO_FORUM" => "N",
	),
	$component,
	array("HIDE_ICONS" => "Y")
);?>
<?endif?>