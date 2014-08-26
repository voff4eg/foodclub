<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?include($_SERVER['DOCUMENT_ROOT']."/bitrix/templates/fclub/new.register.header.php");?>
<?$APPLICATION->IncludeComponent(
	"custom:main.register",
	"new",
	Array(
		"USER_PROPERTY_NAME" => "",
		"SEF_MODE" => "Y",
		"SHOW_FIELDS" => Array("PERSONAL_WWW"),
		"REQUIRED_FIELDS" => Array(),
		"AUTH" => "Y",
		"USE_BACKURL" => "Y",
		"SUCCESS_PAGE" => "",
		"SET_TITLE" => "Y",
		"USER_PROPERTY" => Array(),
		"SEF_FOLDER" => "/registration2/",
		"VARIABLE_ALIASES" => Array(
		)
	)
);?>
<?include($_SERVER['DOCUMENT_ROOT']."/bitrix/templates/fclub/new.register.footer.php"); die;?>