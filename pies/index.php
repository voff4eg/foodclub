<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("keywords", "пироги рецепты, пироги рецепты с фото, яблочный пирог рецепт, простой рецепт пирога, рецепт пирога просто, пирог с капустой рецепт,  рецепт пирога с вареньем");
$APPLICATION->SetPageProperty("description", "На свете существует огромное количество разнообразных пирогов, на любой вкус. Сладкие и несладкие пироги с начинками из овощей, фруктов, мяса, яиц, зелени, грибов, сыров, открытые и закрытые пироги, дрожжевые и бездрожжевые, сдобные, постные, с разными видами муки и разными технологиями приготовления.");
$APPLICATION->SetTitle("Рецепты пирогов");
?> 
<h1> Рецепты пирогов</h1>
 
<p>На&nbsp;свете существует огромное количество разнообразных пирогов, на&nbsp;любой вкус. Сладкие и&nbsp;несладкие пироги с&nbsp;начинками из&nbsp;овощей, фруктов, мяса, яиц, зелени, грибов, сыров, открытые и&nbsp;закрытые пироги, дрожжевые и&nbsp;бездрожжевые, сдобные, постные, с&nbsp;разными видами муки и&nbsp;разными технологиями приготовления.</p>
 
<p>Чтобы помочь вам сориентироваться в&nbsp;этом многообразии, мы&nbsp;создали отдельную подборку рецептов пирогов.</p>
 
<br />

<div>
	<div class="b-social-buttons__item b-fb-like">
		<div class="fb-share-button" data-href="http://www.foodclub.ru/pies/" data-type="button_count"></div>
	</div>
	<div class="b-social-buttons__item b-vk-like">
		<div id="vk_like"></div>
		<script type="text/javascript">
		VK.Widgets.Like("vk_like", {type: "mini", height: 20, pageUrl: "http://www.foodclub.ru/pies/"});
		</script>
	</div>
	<div class="i-clearfix"></div>
</div>
 
<div><?$APPLICATION->IncludeComponent("custom:recipes.list", ".default", array(
	"IBLOCK_TYPE" => "-",
	"IBLOCK_ID" => "5",
	"TITLE" => "Рецепты пирогов",
	"KITCHEN_LIST" => array(
	),
	"MAIN_INGREDIENT_LIST" => array(
	),
	"DISHTYPE_LIST" => array(
		0 => "36",
		1 => "6715",
	),
	"TAG_LIST" => array(
	),
	"NEWS_COUNT" => "20",
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
	),
	false
);?></div>
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>