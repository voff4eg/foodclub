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
		"PAGE_ID" => "user_calendar"
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
<br class="sn-br" />
<?
$ownerId = $arResult["VARIABLES"]["user_id"];
if (CSocNetFeatures::IsActiveFeature(SONET_ENTITY_USER, $ownerId, "calendar"))
{
	$APPLICATION->IncludeComponent(
		"bitrix:intranet.event_calendar",
		".default",
		Array(
			"IBLOCK_TYPE" => $arParams['CALENDAR_IBLOCK_TYPE'], 
			"IBLOCK_ID" => $arParams['CALENDAR_USER_IBLOCK_ID'],
			"OWNER_ID" => $ownerId,
			"OWNER_TYPE" => 'USER', // 'USER', 'GROUP' or '' for standart mode
			"INIT_DATE" => "", 
			"WEEK_HOLIDAYS" => $arParams['CALENDAR_WEEK_HOLIDAYS'],
			"YEAR_HOLIDAYS" => $arParams['CALENDAR_YEAR_HOLIDAYS'], 
			"LOAD_MODE" => "ajax", 
			"USE_DIFFERENT_COLORS" => "Y",
			"EVENT_COLORS" => "", 
			"ADVANCED_MODE_SETTINGS" => "Y",
			"SET_TITLE" => 'Y',
			"SET_NAV_CHAIN" => 'Y',
			"PATH_TO_USER" => $arResult["PATH_TO_USER"],
			"WORK_TIME_START" => $arParams['CALENDAR_WORK_TIME_START'],
			"WORK_TIME_END" => $arParams['CALENDAR_WORK_TIME_END'],
			"PATH_TO_USER_CALENDAR" => $arResult["PATH_TO_USER_CALENDAR"],
			"ALLOW_SUPERPOSE" => $arParams['CALENDAR_ALLOW_SUPERPOSE'],
			"SUPERPOSE_GROUPS_CALS" => $arParams['CALENDAR_SUPERPOSE_GROUPS_CALS'],
			"SUPERPOSE_USERS_CALS" => $arParams['CALENDAR_SUPERPOSE_USERS_CALS'],
			"SUPERPOSE_CUR_USER_CALS" => $arParams['CALENDAR_SUPERPOSE_CUR_USER_CALS'],
			"SUPERPOSE_CAL_IDS" => $arParams['CALENDAR_SUPERPOSE_CAL_IDS'],
			"SUPERPOSE_GROUPS_IBLOCK_ID" => $arParams['CALENDAR_SUPERPOSE_GROUPS_IBLOCK_ID'],
			"SUPERPOSE_USERS_IBLOCK_ID" => $arParams['CALENDAR_USER_IBLOCK_ID'],
			"USERS_IBLOCK_ID" => $arParams['CALENDAR_USER_IBLOCK_ID']
		)
	);
}
?>