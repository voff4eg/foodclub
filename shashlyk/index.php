<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("keywords", "шашлыки, шашлык из свинины, рецепт шашлыка, маринад для шашлыка, как замариновать шашлык, шашлык из курицы, шашлык из свинины рецепт, шашлык фото, как приготовить шашлык");
$APPLICATION->SetPageProperty("description", "Какой шашлык вы предпочитаете? Классический из баранины или беспроигрышный и доступный из свиной шеи? А может быть, нежирный, из любимой спортсменами куриной грудки или диетической индейки? Какой бы рецепт шашлыка вы ни выбрали, готовьте с вдохновением и любовью, учитывайте особенности имеющихся у вас продуктов и все получится замечательно!");
$APPLICATION->SetTitle("Рецепты шашлыков на Foodclub.ru");
?> 
<h1> Шашлыки</h1>
 
<p>Какой шашлык вы&nbsp;предпочитаете? Классический из&nbsp;баранины или беспроигрышный и&nbsp;доступный из&nbsp;свиной шеи? А&nbsp;может быть, нежирный, из&nbsp;любимой спортсменами куриной грудки или диетической индейки? Какой&nbsp;бы рецепт шашлыка вы&nbsp;ни&nbsp;выбрали, готовьте с&nbsp;вдохновением и&nbsp;любовью, учитывайте особенности имеющихся у&nbsp;вас продуктов и&nbsp;все получится замечательно!</p>
 
<br />
 
<div>
	<div class="b-social-buttons__item b-fb-like">
		<div class="fb-share-button" data-href="http://www.foodclub.ru/shashlyk/" data-type="button_count"></div>
	</div>
	<div class="b-social-buttons__item b-vk-like">
		<div id="vk_like"></div>
		<script type="text/javascript">
		VK.Widgets.Like("vk_like", {type: "mini", height: 20, pageUrl: "http://www.foodclub.ru/shashlyk/"});
		</script>
	</div>
	<div class="i-clearfix"></div>
</div>
<div><?$APPLICATION->IncludeComponent(
	"custom:recipes.list",
	".default",
	Array(
		"IBLOCK_TYPE" => "-",
		"IBLOCK_ID" => "5",
		"TITLE" => "Рецепты шашлыков",
		"KITCHEN_LIST" => array(),
		"MAIN_INGREDIENT_LIST" => array(),
		"DISHTYPE_LIST" => array(0=>"95",),
		"TAG_LIST" => array(),
		"NEWS_COUNT" => "60",
		"SORT_BY1" => "ACTIVE_FROM",
		"SORT_ORDER1" => "DESC",
		"SORT_BY2" => "SORT",
		"SORT_ORDER2" => "ASC",
		"FILTER_NAME" => "",
		"FIELD_CODE" => array(0=>"",1=>"",),
		"PROPERTY_CODE" => array(0=>"",1=>"",),
		"CHECK_DATES" => "Y",
		"DETAIL_URL" => "",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "N",
		"PREVIEW_TRUNCATE_LEN" => "",
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"SET_TITLE" => "N",
		"SET_STATUS_404" => "N",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "Y",
		"ADD_SECTIONS_CHAIN" => "Y",
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"PARENT_SECTION" => "",
		"PARENT_SECTION_CODE" => "",
		"INCLUDE_SUBSECTIONS" => "Y",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"PAGER_TITLE" => "Новости",
		"PAGER_SHOW_ALWAYS" => "Y",
		"PAGER_TEMPLATE" => "recipes",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "Y",
		"DISPLAY_DATE" => "N",
		"DISPLAY_NAME" => "Y",
		"DISPLAY_PICTURE" => "Y",
		"DISPLAY_PREVIEW_TEXT" => "Y",
		"AJAX_OPTION_ADDITIONAL" => ""
	)
);?></div>
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>