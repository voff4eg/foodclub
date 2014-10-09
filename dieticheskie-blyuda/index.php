<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("keywords", "диетические блюда рецепты, диетические рецепты фото, диетические рецепты с фото, диетические рецепты для похудения, диетические блюда рецепты фото, диетические блюда рецепты с фото, диетические супы рецепты, диетические салаты рецепты, диетические рецепты мультиварка, рецепт диетического печенья, диетическое питание рецепты, вкусные диетические рецепты, диетический рецепт грудки");
$APPLICATION->SetPageProperty("description", "На этой странице мы собрали сотни разнообразных диетических рецептов! Конечно, при выборе надо учитывать требования именно вашей диеты, но в основном в этот раздел попали блюда с невысокой калорийностью, пониженным содержанием простых сахаров, нежирные и с щадящими способами приготовления. И обратите внимание, какая красочная и аппетитная получилась подборка! С нашими диетическими блюдами Ваша диета будет праздником!");
$APPLICATION->SetTitle("Диетические блюда на Foodclub.ru");
?> 
<h1> Диетические блюда</h1>
 
<p>На&nbsp;этой странице мы&nbsp;собрали сотни разнообразных диетических рецептов! Конечно, при выборе надо учитывать требования именно вашей диеты, но&nbsp;в&nbsp;основном в&nbsp;этот раздел попали блюда с&nbsp;невысокой калорийностью, пониженным содержанием простых сахаров, нежирные и&nbsp;с&nbsp;щадящими способами приготовления. И&nbsp;обратите внимание, какая красочная и&nbsp;аппетитная получилась подборка! С&nbsp;нашими диетическими блюдами Ваша диета будет праздником!</p>
 
<br />

<div>
	<div class="b-social-buttons__item b-fb-like">
		<div class="fb-share-button" data-href="http://www.foodclub.ru/dieticheskie-blyuda/" data-type="button_count"></div>
	</div>
	<div class="b-social-buttons__item b-vk-like">
		<div id="vk_like"></div>
		<script type="text/javascript">
		VK.Widgets.Like("vk_like", {type: "mini", height: 20, pageUrl: "http://www.foodclub.ru/dieticheskie-blyuda/"});
		</script>
	</div>
	<div class="i-clearfix"></div>
</div>
 
<div><?$APPLICATION->IncludeComponent(
	"custom:recipes.list", 
	".default", 
	array(
		"IBLOCK_TYPE" => "-",
		"IBLOCK_ID" => "5",
		"TITLE" => "Рецепты диетических блюд",
		"KITCHEN_LIST" => array(
		),
		"MAIN_INGREDIENT_LIST" => array(
		),
		"DISHTYPE_LIST" => array(
		),
		"INGREDIENT_ID" => array(
		),
		"TAG_LIST" => array(
			0 => "диетические",
			1 => "низкокалорийные",
			2 => "постные блюда",
			3 => "вегетарианское",
			4 => "Диета Дюкана",
			5 => "низкоуглеводные блюда",
		),
		"NEWS_COUNT" => "50",
		"SORT_BY1" => "ACTIVE_FROM",
		"SORT_ORDER1" => "DESC",
		"SORT_BY2" => "SORT",
		"SORT_ORDER2" => "ASC",
		"FILTER_NAME" => "",
		"FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"PROPERTY_CODE" => array(
			0 => "",
			1 => "",
		),
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
		"PAGER_TEMPLATE" => "recipes",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"PAGER_TITLE" => "Новости",
		"PAGER_SHOW_ALWAYS" => "Y",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "Y",
		"DISPLAY_DATE" => "N",
		"DISPLAY_NAME" => "Y",
		"DISPLAY_PICTURE" => "Y",
		"DISPLAY_PREVIEW_TEXT" => "Y",
		"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
);?></div>
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>