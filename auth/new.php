<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
include($_SERVER['DOCUMENT_ROOT']."/bitrix/templates/fclub/new.register.header.php");
?>
<?$APPLICATION->IncludeComponent(
	"custom:system.auth.form",
	"",
	Array(
		"REGISTER_URL" => "/registration/",
		"PROFILE_URL" => "/profile/",
		"SHOW_ERRORS" => "Y"
	),
false
);?>
<?
include($_SERVER['DOCUMENT_ROOT']."/bitrix/templates/fclub/new.register.footer.php");
?>
