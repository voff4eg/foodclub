<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$APPLICATION->IncludeComponent(
	"bitrix:socialnetwork.group_menu",
	"",
	Array(
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
		"PAGE_ID" => "group_calendar",
	),
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
?>
<br class="sn-br" />
<?
$ownerId = $arResult["VARIABLES"]["group_id"];
if (CSocNetFeatures::IsActiveFeature(SONET_ENTITY_GROUP, $ownerId, "calendar"))
{
	$APPLICATION->IncludeComponent(
		"bitrix:intranet.event_calendar",
		".default",
		Array(
			"IBLOCK_TYPE" => $arParams['CALENDAR_IBLOCK_TYPE'], 
			"IBLOCK_ID" => $arParams['CALENDAR_GROUP_IBLOCK_ID'],
			"OWNER_ID" => $ownerId,
			"OWNER_TYPE" => 'GROUP', // 'USER', 'GROUP' or 'NONE' for standart mode
			"MULTIPLE_MODE" => 'Y', // multiple calendars
			"INIT_DATE" => "", 
			"WEEK_HOLIDAYS" => $arParams['CALENDAR_WEEK_HOLIDAYS'], 
			"YEAR_HOLIDAYS" => $arParams['CALENDAR_YEAR_HOLIDAYS'], 
			"LOAD_MODE" => "ajax", 
			"USE_DIFFERENT_COLORS" => "Y",
			"EVENT_COLORS" => "", 
			"ADVANCED_MODE_SETTINGS" => "Y",
			"SET_TITLE" => 'Y',
			"SET_NAV_CHAIN" => 'Y',
			"WORK_TIME_START" => $arParams['CALENDAR_WORK_TIME_START'],
			"WORK_TIME_END" => $arParams['CALENDAR_WORK_TIME_END'],
			"PATH_TO_USER" => $arParams["PATH_TO_USER"],
			"PATH_TO_GROUP" => $arResult["PATH_TO_GROUP"],
			"PATH_TO_GROUP_CALENDAR" => $arResult["PATH_TO_GROUP_CALENDAR"],
			"ALLOW_SUPERPOSE" => $arParams['CALENDAR_ALLOW_SUPERPOSE'],
			"SUPERPOSE_GROUPS_CALS" => $arParams['CALENDAR_SUPERPOSE_GROUPS_CALS'],
			"SUPERPOSE_USERS_CALS" => $arParams['CALENDAR_SUPERPOSE_USERS_CALS'],
			"SUPERPOSE_CUR_USER_CALS" => $arParams['CALENDAR_SUPERPOSE_CUR_USER_CALS'],
			"SUPERPOSE_CAL_IDS" => $arParams['CALENDAR_SUPERPOSE_CAL_IDS'],
			"SUPERPOSE_GROUPS_IBLOCK_ID" => $arParams['CALENDAR_GROUP_IBLOCK_ID'],
			"SUPERPOSE_USERS_IBLOCK_ID" => $arParams['CALENDAR_USER_IBLOCK_ID'],
			"USERS_IBLOCK_ID" => $arParams['CALENDAR_USER_IBLOCK_ID']
		)
	);
}
?>