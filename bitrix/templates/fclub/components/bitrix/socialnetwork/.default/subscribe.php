<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
$APPLICATION->IncludeComponent(
	"bitrix:socialnetwork.messages_menu",
	"",
	Array(
		"USER_VAR" => $arResult["ALIASES"]["user_id"],
		"PAGE_VAR" => $arResult["ALIASES"]["page"],
		"PATH_TO_MESSAGES_INPUT" => $arResult["PATH_TO_MESSAGES_INPUT"],
		"PATH_TO_MESSAGES_OUTPUT" => $arResult["PATH_TO_MESSAGES_OUTPUT"],
		"PATH_TO_MESSAGES_USERS" => $arResult["PATH_TO_MESSAGES_USERS"],
		"PATH_TO_USER_BAN" => $arResult["PATH_TO_USER_BAN"],
		"PATH_TO_USER" => $arResult["PATH_TO_USER"],
		"PATH_TO_LOG" => $arResult["PATH_TO_LOG"],
		"PATH_TO_SUBSCRIBE" => $arResult["PATH_TO_SUBSCRIBE"],
		"PAGE_ID" => "subscribe"
	),
	$component
);
?>

<br />

<?
$APPLICATION->IncludeComponent(
	"bitrix:socialnetwork.subscribe_list", 
	"", 
	Array(
		"PAGE_VAR" => $arResult["ALIASES"]["page"],
		"USER_VAR" => $arResult["ALIASES"]["user_id"],
		"GROUP_VAR" => $arResult["ALIASES"]["group_id"],
		"SET_TITLE" => "Y",
		"SET_NAVCHAIN" => "Y",
		"PATH_TO_USER" => $arResult["PATH_TO_USER"],
		"PATH_TO_GROUP" => $arResult["PATH_TO_GROUP"],
		"PATH_TO_GROUP_SUBSCRIBE" => $arResult["PATH_TO_GROUP_SUBSCRIBE"],
		"PATH_TO_USER_SUBSCRIBE" => $arResult["PATH_TO_USER_SUBSCRIBE"],
		"ITEMS_COUNT" => $arParams["ITEM_DETAIL_COUNT"],
	),
	$component 
);
?>