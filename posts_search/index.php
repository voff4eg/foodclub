<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Поиск");
?>
<p><?$APPLICATION->IncludeComponent("bitrix:search.page", "posts", array(
	"RESTART" => "N",
	"CHECK_DATES" => "N",
	"USE_TITLE_RANK" => "N",
	"arrWHERE" => array(
		0 => "blog",
		1 => "socialnetwork",
	),
	"arrFILTER" => array(
		0 => "blog",
		1 => "socialnetwork",
	),
	"arrFILTER_blog" => array(
		0 => "all",
	),
	"SHOW_WHERE" => "Y",
	"PAGE_RESULT_COUNT" => "10",
	"AJAX_MODE" => "N",
	"AJAX_OPTION_SHADOW" => "Y",
	"AJAX_OPTION_JUMP" => "N",
	"AJAX_OPTION_STYLE" => "Y",
	"AJAX_OPTION_HISTORY" => "N",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "3600",
	"PAGER_TITLE" => "Результаты поиска",
	"PAGER_SHOW_ALWAYS" => "Y",
	"PAGER_TEMPLATE" => "blogs",
	"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
);?></p>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
