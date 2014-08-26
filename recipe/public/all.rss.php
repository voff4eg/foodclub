<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("RSS");
?><?$APPLICATION->IncludeComponent(
	"custom:rss.out",
	"",
	Array(
		"IBLOCK_TYPE" => "recipes", 
		"IBLOCK_ID" => "5", 
		"SECTION_ID" => "", 
		"NUM_NEWS" => "20", 
		"NUM_DAYS" => "30", 
		"RSS_TTL" => "60", 
		"YANDEX" => "N", 
		"SORT_BY1" => "ACTIVE_FROM", 
		"SORT_ORDER1" => "DESC", 
		"SORT_BY2" => "SORT", 
		"SORT_ORDER2" => "ASC", 
		"CACHE_TYPE" => "A", 
		"CACHE_TIME" => "3600" 
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>