<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");?>
<?
//$APPLICATION->AddHeadString('<link rel="stylesheet" type="text/css" href="/css/form.css">');
?>
<link rel="stylesheet" type="text/css" href="/css/form.css">
<?
$APPLICATION->AddHeadScript("/js/form.js");
?>
<?$APPLICATION->IncludeComponent(
	"custom:profile.edit",
	"custom",
	Array(
		"USER_PROPERTY_NAME" => "",
		"SET_TITLE" => "Y",
		"AJAX_MODE" => "N",
		"USER_PROPERTY" => array(),
		"SEND_INFO" => "N",
		"CHECK_RIGHTS" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_ADDITIONAL" => ""
	),
false
);?>
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
