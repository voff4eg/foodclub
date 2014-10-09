<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "В сезон сбора урожая овощи и фрукты оказываются в значительном избытке, причем отличного качества и по самым низким ценам. Этим моментом обязательно нужно воспользоваться, чтобы сохранить свежие плоды на зимний перирд. Закуски на зиму — один из способов сохранения овощей, который позволяет сберечь не только продукты, но и ваши силы. Ведь намного проще один раз приготовить, например, баклажаны на зиму в большом количестве, а потом постепенно их есть, чем каждый раз готовить что-то новое, причем из тепличных (дорогих и не таких вкусных) овощей. Закуски на зиму могут быть в виде разнообразных солений и маринадов, составных стерилизованных блюд, сушеных и вяленых овощей.");
$APPLICATION->SetPageProperty("keywords", "закуски на зиму, закуска из помидоров на зиму, рецепты закусок на зиму, закуска из баклажанов на зиму, закуска из кабачков на зиму, острые закуски на зиму, закуска на зиму из огурцов");
$APPLICATION->SetTitle("Закуски на зиму — рецепты с пошаговыми фотографиями");
?> 
<h1>Закуски на зиму</h1>
 
<p>В&nbsp;сезон сбора урожая овощи и&nbsp;фрукты оказываются в&nbsp;значительном избытке, причем отличного качества и&nbsp;по&nbsp;самым низким ценам. Этим моментом обязательно нужно воспользоваться, чтобы сохранить свежие плоды на&nbsp;зимний перирд. </p>

<p>Закуски на&nbsp;зиму&nbsp;&mdash; один из&nbsp;способов сохранения овощей, который позволяет сберечь не&nbsp;только продукты, но&nbsp;и&nbsp;ваши силы. Ведь намного проще один раз приготовить, например, баклажаны на&nbsp;зиму в&nbsp;большом количестве, а&nbsp;потом постепенно их&nbsp;есть, чем каждый раз готовить <nobr>что-то</nobr> новое, причем из&nbsp;тепличных (дорогих и&nbsp;не&nbsp;таких вкусных) овощей. </p>

<p>Закуски на&nbsp;зиму могут быть в&nbsp;виде разнообразных солений и&nbsp;маринадов, составных стерилизованных блюд, сушеных и&nbsp;вяленых овощей.</p>
 
<br />

<br />
 
<div> 	 
  <div class="b-social-buttons__item b-fb-like"> 		 
    <div class="fb-share-button" data-href="http://www.foodclub.ru/zakuski-na-zimu/" data-type="button_count"></div>
   	</div>
 	 
  <div class="b-social-buttons__item b-vk-like"> 		 
    <div id="vk_like"></div>
   		 
<script type="text/javascript">
		VK.Widgets.Like("vk_like", {type: "mini", height: 20, pageUrl: "http://www.foodclub.ru/zakuski-na-zimu/"});
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
		"TITLE" => "Закуски на зиму &mdash; рецепты",
		"KITCHEN_LIST" => array(),
		"MAIN_INGREDIENT_LIST" => array(),
		"DISHTYPE_LIST" => array(),
		"INGREDIENT_ID" => array(),
		"TAG_LIST" => array(0=>"закуски на зиму",),
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