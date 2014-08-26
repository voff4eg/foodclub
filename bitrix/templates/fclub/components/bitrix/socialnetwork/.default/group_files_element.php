<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

?><?$APPLICATION->IncludeComponent("bitrix:socialnetwork.group_menu", "", Array(
		"GROUP_VAR" => $arResult["ALIASES"]["group_id"],
		"PAGE_VAR" => $arResult["ALIASES"]["page"],
		"PATH_TO_GROUP" => $arResult["PATH_TO_GROUP"],
		"PATH_TO_GROUP_MODS" => $arResult["PATH_TO_GROUP_MODS"],
		"PATH_TO_GROUP_USERS" => $arResult["PATH_TO_GROUP_USERS"],
		"PATH_TO_GROUP_EDIT" => $arResult["PATH_TO_GROUP_EDIT"],
		"PATH_TO_GROUP_REQUEST_SEARCH" => $arResult["PATH_TO_GROUP_REQUEST_SEARCH"],
		"PATH_TO_GROUP_REQUESTS" => $arResult["PATH_TO_GROUP_REQUESTS"],
		"PATH_TO_GROUP_BAN" => $arResult["PATH_TO_GROUP_BAN"],
		"PATH_TO_GROUP_BLOG" => $arResult["PATH_TO_GROUP_BLOG"],
		"PATH_TO_GROUP_PHOTO" => $arResult["PATH_TO_GROUP_PHOTO"],
		"PATH_TO_GROUP_FORUM" => $arResult["PATH_TO_GROUP_FORUM"],
		"PATH_TO_GROUP_CALENDAR" => $arResult["PATH_TO_GROUP_CALENDAR"],
		"PATH_TO_GROUP_FILES" => $arResult["PATH_TO_GROUP_FILES"],
		"PATH_TO_GROUP_TASKS" => $arResult["PATH_TO_GROUP_TASKS"],
		"GROUP_ID" => $arResult["VARIABLES"]["group_id"],
		"PAGE_ID" => "group_files"),
	$component
);
?>
<br />
<?
$APPLICATION->IncludeComponent(
	"bitrix:socialnetwork.group", 
	"short", 
	Array(
		"PATH_TO_USER" => $arParams["PATH_TO_USER"],
		"PATH_TO_GROUP" => $arResult["PATH_TO_GROUP"],
		"PATH_TO_GROUP_EDIT" => $arResult["PATH_TO_GROUP_EDIT"],
		"PATH_TO_GROUP_CREATE" => $arResult["PATH_TO_GROUP_CREATE"],
		"PATH_TO_GROUP_REQUEST_SEARCH" => $arResult["PATH_TO_GROUP_REQUEST_SEARCH"],
		"PATH_TO_USER_REQUEST_GROUP" => $arResult["PATH_TO_USER_REQUEST_GROUP"],
		"PATH_TO_GROUP_REQUESTS" => $arResult["PATH_TO_GROUP_REQUESTS"],
		"PATH_TO_GROUP_MODS" => $arResult["PATH_TO_GROUP_MODS"],
		"PATH_TO_GROUP_USERS" => $arResult["PATH_TO_GROUP_USERS"],
		"PATH_TO_USER_LEAVE_GROUP" => $arResult["PATH_TO_USER_LEAVE_GROUP"],
		"PATH_TO_GROUP_DELETE" => $arResult["PATH_TO_GROUP_DELETE"],
		"PATH_TO_GROUP_FEATURES" => $arResult["PATH_TO_GROUP_FEATURES"],
		"PATH_TO_GROUP_BAN" => $arResult["PATH_TO_GROUP_BAN"],
		"PATH_TO_SEARCH" => $arResult["PATH_TO_SEARCH"],
		"PATH_TO_MESSAGE_TO_GROUP" => $arResult["PATH_TO_MESSAGE_TO_GROUP"], 
		"PAGE_VAR" => $arResult["ALIASES"]["page"],
		"USER_VAR" => $arResult["ALIASES"]["user_id"],
		"GROUP_VAR" => $arResult["ALIASES"]["group_id"],
		"SET_TITLE" => "N", 
		"SHORT_FORM" => "Y",
		"USER_ID" => $arResult["VARIABLES"]["user_id"],
		"GROUP_ID" => $arResult["VARIABLES"]["group_id"],
		"ITEMS_COUNT" => $arParams["ITEM_MAIN_COUNT"],
	),
	$component 
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
	"IBLOCK_TYPE"	=>	$arParams["FILES_GROUP_IBLOCK_TYPE"],
	"IBLOCK_ID"	=>	$arParams["FILES_GROUP_IBLOCK_ID"],
	"ROOT_SECTION_ID"	=>	$arResult["VARIABLES"]["ROOT_SECTION_ID"],
	"ELEMENT_ID"	=>	$arResult["VARIABLES"]["ELEMENT_ID"],
	"SECTION_ID"	=>	$arResult["VARIABLES"]["SECTION_ID"],
	"PERMISSION"	=>	$arResult["VARIABLES"]["PERMISSION"],
	"PAGE_NAME" => "ELEMENT",
	"BASE_URL"	=>	$arResult["VARIABLES"]["BASE_URL"],
	
	"SECTIONS_URL" => $arResult["~PATH_TO_GROUP_FILES"],
	"SECTION_EDIT_URL" => $arResult["~PATH_TO_GROUP_FILES_SECTION_EDIT"],
	"ELEMENT_EDIT_URL" => $arResult["~PATH_TO_GROUP_FILES_ELEMENT_EDIT"],
	"ELEMENT_HISTORY_URL" => $arResult["~PATH_TO_GROUP_FILES_ELEMENT_HISTORY"],
	"ELEMENT_UPLOAD_URL" => $arResult["~PATH_TO_GROUP_FILES_ELEMENT_UPLOAD"],
	"HELP_URL" => $arResult["~PATH_TO_GROUP_FILES_HELP"],
	"USER_VIEW_URL" => $arResult["~PATH_TO_USER"],
	
	"FORUM_ID" => false, 
	"USE_COMMENTS"	=>	"N",
	
	"STR_TITLE" => $arResult["VARIABLES"]["STR_TITLE"]),
	$component
);
?><?
?><?$arInfo = $APPLICATION->IncludeComponent("bitrix:webdav.element.view", ".default", Array(
	"IBLOCK_TYPE"	=>	$arParams["FILES_GROUP_IBLOCK_TYPE"],
	"IBLOCK_ID"	=>	$arParams["FILES_GROUP_IBLOCK_ID"],
	"ROOT_SECTION_ID"	=>	$arResult["VARIABLES"]["ROOT_SECTION_ID"],
	"ELEMENT_ID"	=>	$arResult["VARIABLES"]["ELEMENT_ID"],
	"SECTION_ID"	=>	$arResult["VARIABLES"]["SECTION_ID"],
	"PERMISSION"	=>	$arResult["VARIABLES"]["PERMISSION"],
	"NAME_FILE_PROPERTY"	=>	$arParams["NAME_FILE_PROPERTY"],
	"REPLACE_SIMBOLS"	=>	$arParams["REPLACE_SIMBOLS"],
	
	"SECTIONS_URL" => $arResult["~PATH_TO_GROUP_FILES"],
	"SECTION_EDIT_URL" => $arResult["~PATH_TO_GROUP_FILES_SECTION_EDIT"],
	"ELEMENT_URL" => $arResult["~PATH_TO_GROUP_FILES_ELEMENT"],
	"ELEMENT_EDIT_URL" => $arResult["~PATH_TO_GROUP_FILES_ELEMENT_EDIT"],
	"ELEMENT_HISTORY_URL" => $arResult["~PATH_TO_GROUP_FILES_ELEMENT_HISTORY"],
	"ELEMENT_HISTORY_GET_URL" => $arResult["~PATH_TO_GROUP_FILES_ELEMENT_HISTORY_GET"],
	"HELP_URL" => $arResult["~PATH_TO_GROUP_FILES_HELP"],
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
		"URL_TEMPLATES_DETAIL" => $arResult["~PATH_TO_GROUP_FILES_ELEMENT"],
		
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
<?endif
?>