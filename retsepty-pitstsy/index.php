<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "Пиццу любят, наверное, все. Это итальянское изобретение распространилось по всему миру, попутно адаптируясь к местным условиям, вкусам и продуктам, заодно вбирая в себя элементы гастрономической культуры разных стран.  Чтобы самая вкусная домашняя пицца была и у вас на столе, отыщите свой любимый рецепт теста, а потом экспериментируйте с начинками. Вдохновение вы сможете найти в нашей подборке рецептов пиццы.");
$APPLICATION->SetPageProperty("keywords", "пицца рецепт, теста для пиццы, домашняя пицца, пицца в домашних условиях, как приготовить пиццу, пицца без дрожжей, домашняя пицца рецепт, домашние рецепты пиццы");
$APPLICATION->SetTitle("Пицца рецепты c фотографиями на Foodclub.ru");
?> 
<h1>Пицца</h1>
 
<p>Пиццу любят, наверное, все. Это итальянское изобретение распространилось по&nbsp;всему миру, попутно адаптируясь к&nbsp;местным условиям, вкусам и&nbsp;продуктам, заодно вбирая в&nbsp;себя элементы гастрономической культуры разных стран.</p>

<p>Чтобы самая вкусная домашняя пицца была и&nbsp;у&nbsp;вас на&nbsp;столе, отыщите свой любимый рецепт теста, а&nbsp;потом экспериментируйте с&nbsp;начинками. Вдохновение вы&nbsp;сможете найти в&nbsp;нашей подборке рецептов пиццы.</p>
 
<br />
 
<div> 
  <br />
 
  <div> 	 
    <div class="b-social-buttons__item b-fb-like"> 		 
      <div class="fb-share-button" data-href="http://www.foodclub.ru/retsepty-pitstsy/" data-type="button_count"></div>
     	</div>
   	 
    <div class="b-social-buttons__item b-vk-like"> 		 
      <div id="vk_like"></div>
     		 
<script type="text/javascript">
		VK.Widgets.Like("vk_like", {type: "mini", height: 20, pageUrl: "http://www.foodclub.ru/retsepty-pitstsy/"});
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
		"TITLE" => "Рецепты пиццы",
		"KITCHEN_LIST" => array(),
		"MAIN_INGREDIENT_LIST" => array(),
		"DISHTYPE_LIST" => array(0=>"102",),
		"INGREDIENT_ID" => array(),
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
 </div>
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>