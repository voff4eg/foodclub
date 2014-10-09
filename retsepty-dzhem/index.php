<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("keywords", "рецепт джем, яблочный джем рецепт, джем из яблок рецепт, рецепт джема на зиму, джемы рецепты с фото, яблочный джем рецепт на зиму, рецепт джема в мультиварке, рецепт джема из груш");
$APPLICATION->SetTitle("Рецепты джемов с пошаговыми фотографиями");
?> 
<h1>Рецепты джемов</h1>
 
<p>Приготовление джема это не&nbsp;просто создание вкусной сладости к&nbsp;чаю, но&nbsp;и&nbsp;способ сохранения фруктов и&nbsp;ягод. Современные джемы, как правило, содержат пониженное количество сахара и&nbsp;готовятся с&nbsp;добавлением пектина. </p>

<p>Если вы&nbsp;хотите приготовить джем на&nbsp;зиму, то&nbsp;не&nbsp;уменьшайте количество сахара в&nbsp;рецепте: обязательно используйте стерильные банки и&nbsp;закрывайте джем в&nbsp;горячем виде. Удобно готовить джем в&nbsp;мультиварке. </p>

<p>Одними из&nbsp;самых популярных видов джема на&nbsp;зиму являются яблочный джем, грушевый джем и&nbsp;ягодные джемы.</p>
 
<br />

<br />
 
<div> 	 
  <div class="b-social-buttons__item b-fb-like"> 		 
    <div class="fb-share-button" data-href="http://www.foodclub.ru/retsepty-dzhem/" data-type="button_count"></div>
   	</div>
 	 
  <div class="b-social-buttons__item b-vk-like"> 		 
    <div id="vk_like"></div>
   		 
<script type="text/javascript">
		VK.Widgets.Like("vk_like", {type: "mini", height: 20, pageUrl: "http://www.foodclub.ru/retsepty-dzhem/"});
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
		"TITLE" => "Рецепты джемов",
		"KITCHEN_LIST" => array(),
		"MAIN_INGREDIENT_LIST" => array(),
		"DISHTYPE_LIST" => array(),
		"INGREDIENT_ID" => array(),
		"TAG_LIST" => array(0=>"джем",),
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