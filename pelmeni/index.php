<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("keywords", "пельмени, пельмени рецепт, пельмени домашние, рецепт теста для пельменей, домашние пельмени рецепт");
$APPLICATION->SetTitle("Пельмени на Foodclub.ru");
?> 
<h1> Пельмени</h1>
 
<p>Человечество придумало невероятное количество блюд, состоящих из&nbsp;тонкого теста с&nbsp;начинкой, и&nbsp;неудивительно, ведь просто для фантазии просто огромен! Можно менять состав теста, но&nbsp;еще больше вариантов начинки. А&nbsp;от&nbsp;начинки и&nbsp;от&nbsp;формы зависит даже название блюда. Пельмени, хинкали, манты, тортелли и&nbsp;тортеллини, гёдза, вареники, курзе, равиоли, вонтоны, димсамы&hellip; </p>
 
<p>Просто попробуйте приготовить хотя&nbsp;бы несколько вариантов и&nbsp;выберите свой любимый. А&nbsp;может быть, это окажется так увлекательно, что вам не&nbsp;захочется прекращать эксперименты.</p>

<br />

<div>
	<div class="b-social-buttons__item b-fb-like">
		<div class="fb-share-button" data-href="http://www.foodclub.ru/pelmeni/" data-type="button_count"></div>
	</div>
	<div class="b-social-buttons__item b-vk-like">
		<div id="vk_like"></div>
		<script type="text/javascript">
		VK.Widgets.Like("vk_like", {type: "mini", height: 20, pageUrl: "http://www.foodclub.ru/pelmeni/"});
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
		"TITLE" => "Рецепты пельменей",
		"KITCHEN_LIST" => array(),
		"MAIN_INGREDIENT_LIST" => array(),
		"DISHTYPE_LIST" => array(0=>"92",),
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