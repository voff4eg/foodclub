<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

$APPLICATION->RestartBuffer();
?>
<html>
<head>
<?$APPLICATION->ShowHead();?>
<title><?$APPLICATION->ShowTitle()?></title>
</head>
<body class="socnet-chat">
<?
$APPLICATION->IncludeComponent(
	"bitrix:socialnetwork.messages_chat", 
	"", 
	Array(
		"PATH_TO_USER" => $arResult["PATH_TO_USER"],
		"PATH_TO_MESSAGES_USERS_MESSAGES" => $arResult["PATH_TO_MESSAGES_USERS_MESSAGES"],
		"PATH_TO_SMILE" => $arResult["PATH_TO_SMILE"],
		"PAGE_VAR" => $arResult["ALIASES"]["page"],
		"USER_VAR" => $arResult["ALIASES"]["user_id"],
		"SET_TITLE" => "Y", 
		"DATE_TIME_FORMAT" => $arResult["DATE_TIME_FORMAT"],
		"USER_ID" => $arResult["VARIABLES"]["user_id"],
		"MESSAGE_ID" => $arResult["VARIABLES"]["message_id"],
	),
	$component 
);
?>
</body>
</html>
<?
die();
?>