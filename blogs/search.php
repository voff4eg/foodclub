<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Title");
?><?$APPLICATION->IncludeComponent(
	"bitrix:search.tags.cloud",
	".default",
	Array(
		"SORT" => "NAME",
		"PAGE_ELEMENTS" => "150",
		"PERIOD" => "",
		"URL_SEARCH" => "/blogs/search.php",
		"TAGS_INHERIT" => "Y",
		"CHECK_DATES" => "N",
		"arrFILTER" => array(0=>"blog",),
		"arrFILTER_blog" => array(0=>"all",),
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600",
		"FONT_MAX" => "50",
		"FONT_MIN" => "10",
		"COLOR_NEW" => "3E74E6",
		"COLOR_OLD" => "C0C0C0",
		"PERIOD_NEW_TAGS" => "",
		"SHOW_CHAIN" => "Y",
		"COLOR_TYPE" => "Y",
		"WIDTH" => "100%"
	)
);?><?$APPLICATION->IncludeComponent(
	"bitrix:search.page",
	"",
	Array(
		"AJAX_MODE" => "N",
		"RESTART" => "N",
		"CHECK_DATES" => "N",
		"USE_TITLE_RANK" => "N",
		"arrWHERE" => Array("blog"),
		"arrFILTER" => Array("blog"),
		"SHOW_WHERE" => "Y",
		"PAGE_RESULT_COUNT" => "50",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600",
		"PAGER_TITLE" => "Результаты поиска",
		"PAGER_SHOW_ALWAYS" => "Y",
		"PAGER_TEMPLATE" => "",
		"arrFILTER_blog" => "all",
		"AJAX_OPTION_SHADOW" => "Y",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_ADDITIONAL" => ""
	),
false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>