<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("keywords", "десерт, рецепты десертов, Горячий шоколад, чизкейк, ореховый десерт, десерты фото, вкусные десерты,");
$APPLICATION->SetPageProperty("description", "Десерт — это блюдо, которым принято завершать трапезу. Десерт должен быть небольшим по объему, но идеальным на вкус. Как правило, на десерт подают сладости, но встречаются и несладкие десерты.");
$APPLICATION->SetTitle("Десерт на Foodclub.ru");
?> 
<h1>Десерт</h1>
 
<p>Десерт&nbsp;&mdash; это блюдо, которым принято завершать трапезу. Десерт должен быть небольшим по&nbsp;объему, но&nbsp;идеальным на&nbsp;вкус. Как правило, на&nbsp;десерт подают сладости, но&nbsp;встречаются и&nbsp;несладкие десерты. Чтобы вам легче было ориентироваться в&nbsp;мире десертов, мы&nbsp;собрали в&nbsp;одном месте рецепты десертов, которые были добавлены на&nbsp;сайт пользователями Foodclub.ru</p>
 
<br />

<div>
	<div class="b-social-buttons__item b-fb-like">
		<div class="fb-share-button" data-href="http://www.foodclub.ru/desert/" data-type="button_count"></div>
	</div>
	<div class="b-social-buttons__item b-vk-like">
		<div id="vk_like"></div>
		<script type="text/javascript">
		VK.Widgets.Like("vk_like", {type: "mini", height: 20, pageUrl: "http://www.foodclub.ru/desert/"});
		</script>
	</div>
	<div class="i-clearfix"></div>
</div>

<p><?$APPLICATION->IncludeComponent(
	"custom:recipes.list",
	".default",
	Array(
		"IBLOCK_TYPE" => "-",
		"IBLOCK_ID" => "5",
		"TITLE" => "Рецепты десертов",
		"KITCHEN_LIST" => array(),
		"MAIN_INGREDIENT_LIST" => array(),
		"DISHTYPE_LIST" => array(0=>"3",),
		"TAG_LIST" => array(),
		"NEWS_COUNT" => "40",
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
);?></p>
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>