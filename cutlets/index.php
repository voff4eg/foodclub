<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "У каждой хозяйки наверняка есть свой любимый рецепт котлет, которые она готовит чаще всего. И этих рецептов так много, что мы собрали их на одной странице и теперь вы сможете легко выбрать что-то для себя. Здесь есть и традиционные домашние котлеты из смешанного фарша с размоченным в молоке хлебом, и легчайшие диетические котлеты из куриного филе или индейки с овощами, есть котлеты из рыбы и даже совсем постные овощные котлеты.  Смотрите внимательно и вы обязательно найдете то, что вам по душе!");
$APPLICATION->SetPageProperty("keywords", "котлеты, котлеты рецепт, куриные котлеты, курин котлеты, котлеты из фарша, рыбные котлеты, котлеты фото, как приготовить котлеты, котлеты в духовке, котлеты рецепт с фото, котлеты в мультиварке, постные котлеты,");
$APPLICATION->SetTitle("Рецепты котлет c пошаговыми фотографиями на Foodclub.ru");
?> 
<h1> Рецепты котлет</h1>
 
<p>У&nbsp;каждой хозяйки наверняка есть свой любимый рецепт котлет, которые она готовит чаще всего. И&nbsp;этих рецептов так много, что мы&nbsp;собрали их&nbsp;на&nbsp;одной странице и&nbsp;теперь вы&nbsp;сможете легко выбрать <nobr>что-то</nobr> для себя. Здесь есть и&nbsp;традиционные домашние котлеты из&nbsp;смешанного фарша с&nbsp;размоченным в&nbsp;молоке хлебом, и&nbsp;легчайшие диетические котлеты из&nbsp;куриного филе или индейки с&nbsp;овощами, есть котлеты из&nbsp;рыбы и&nbsp;даже совсем постные овощные котлеты. 
  <br />
 </p>
 
<p>Смотрите внимательно и&nbsp;вы&nbsp;обязательно найдете то, что вам по&nbsp;душе!</p>
<br>
<div>
	<div class="b-social-buttons__item b-fb-like">
		<div class="fb-share-button" data-href="http://www.foodclub.ru/cutlets/" data-type="button_count"></div>
	</div>
	<div class="b-social-buttons__item b-vk-like">
		<div id="vk_like"></div>
		<script type="text/javascript">
		VK.Widgets.Like("vk_like", {type: "mini", height: 20, pageUrl: "http://www.foodclub.ru/cutlets/"});
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
		"TITLE" => "Рецепты котлет",
		"KITCHEN_LIST" => array(),
		"MAIN_INGREDIENT_LIST" => array(),
		"DISHTYPE_LIST" => array(0=>"4013",),
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
	)
);?></div>
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>