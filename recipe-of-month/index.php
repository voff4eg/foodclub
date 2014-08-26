<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("keywords", "рецепт месяца, рецепт");
$APPLICATION->SetPageProperty("description", "Каждый месяц приглашенный эксперт будет выбирать лучший из рецептов, опубликованных на нашем сайте.");
$APPLICATION->SetTitle("Рецепт месяца на Foodclub.ru");
?>

<?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_SHOW" => "page",
		"AREA_FILE_SUFFIX" => "inc",
		"EDIT_TEMPLATE" => ""
	),
false
);?>
<link href="/bitrix/components/custom/store.banner.horizontal/templates/.default/store.css"  type="text/css" rel="stylesheet" />
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>