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
		"PATH_TO_USER" => $arResult["PATH_TO_USER"],
		"PATH_TO_MESSAGES_USERS" => $arResult["PATH_TO_MESSAGES_USERS"],
		"PATH_TO_USER_BAN" => $arResult["PATH_TO_USER_BAN"],
		"PATH_TO_LOG" => $arResult["PATH_TO_LOG"],
		"PATH_TO_SUBSCRIBE" => $arResult["PATH_TO_SUBSCRIBE"],
		"PAGE_ID" => "messages_output"
	),
	$component
);
?>

<br />

<?
$APPLICATION->IncludeComponent(
	"bitrix:socialnetwork.messages_output", 
	"", 
	Array(
		"PATH_TO_USER" => $arResult["PATH_TO_USER"],
		"PATH_TO_MESSAGE_FORM" => $arResult["PATH_TO_MESSAGE_FORM"],
		"PATH_TO_MESSAGES_OUTPUT" => $arResult["PATH_TO_MESSAGES_OUTPUT"],
		"PATH_TO_MESSAGES_OUTPUT_USER" => $arResult["PATH_TO_MESSAGES_OUTPUT_USER"],
		"PATH_TO_SMILE" => $arResult["PATH_TO_SMILE"],
		"PAGE_VAR" => $arResult["ALIASES"]["page"],
		"USER_VAR" => $arResult["ALIASES"]["user_id"],
		"SET_TITLE" => "Y", 
		"DATE_TIME_FORMAT" => $arResult["DATE_TIME_FORMAT"],
		"USER_ID" => $arResult["VARIABLES"]["user_id"],
		"ITEMS_COUNT" => $arParams["ITEM_DETAIL_COUNT"],
	),
	$component 
);
?>