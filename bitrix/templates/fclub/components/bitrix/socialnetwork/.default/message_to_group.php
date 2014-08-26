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
		"PATH_TO_GROUP" => $arResult["PATH_TO_GROUP"],
		"PATH_TO_USER" => $arResult["PATH_TO_USER"],
		"PATH_TO_SMILE" => $arResult["PATH_TO_SMILE"],
		"PAGE_VAR" => $arResult["ALIASES"]["page"],
		"GROUP_VAR" => $arResult["ALIASES"]["group_id"],
		"SET_TITLE" => "Y", 
		"DATE_TIME_FORMAT" => $arResult["DATE_TIME_FORMAT"],
		"GROUP_ID" => $arResult["VARIABLES"]["group_id"],
	),
	$component 
);
?>
</body>
</html>
<?
die();
?>