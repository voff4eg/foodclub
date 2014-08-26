<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "Фарш из мяса, птицы или рыбы — очень интересный кулинарный полуфабрикат, из которого можно приготовить совершенно разные блюда на любой вкус. Блюда из фарша прекрасно подходят для детского стола, для пожилых людей, ведь их удобно есть! Фаршем можно начинять овощи, заворачивать его в тесто, готовить котлеты, тефтели, фрикадельки и даже сосиски и колбасу. А еще блюда из фарша позволяют утилизировать обрезки и остатки мяса.");
$APPLICATION->SetPageProperty("keywords", "блюда из фарша, блюда из фарша рецепты, блюда из фарша с фото, вкусные блюда из фарша, быстрые блюда из фарша, блюда из фарша быстро, блюда из фарша в духовке, блюда из куриного фарша");
$APPLICATION->SetTitle("Блюда из фарша на Foodclub.ru");
?> 
<h1> Блюда из фарша</h1>
 Фарш из&nbsp;мяса, птицы или рыбы&nbsp;&mdash; очень интересный кулинарный полуфабрикат, из&nbsp;которого можно приготовить совершенно разные блюда на&nbsp;любой вкус. Блюда из&nbsp;фарша прекрасно подходят для детского стола, для пожилых людей, ведь их&nbsp;удобно есть! 
<div>Фаршем можно начинять овощи, заворачивать его в&nbsp;тесто, готовить котлеты, тефтели, фрикадельки и&nbsp;даже сосиски и&nbsp;колбасу. А&nbsp;еще блюда из&nbsp;фарша позволяют утилизировать обрезки и&nbsp;остатки мяса. </div>
 
<div>  
  <br />

  <div>
	<div class="b-social-buttons__item b-fb-like">
		<div class="fb-share-button" data-href="http://www.foodclub.ru/bluda-iz-farsha/" data-type="button_count"></div>
	</div>
	<div class="b-social-buttons__item b-vk-like">
		<div id="vk_like"></div>
		<script type="text/javascript">
		VK.Widgets.Like("vk_like", {type: "mini", height: 20, pageUrl: "http://www.foodclub.ru/bluda-iz-farsha/"});
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
		"TITLE" => "Рецепты блюд из фарша",
		"KITCHEN_LIST" => array(),
		"MAIN_INGREDIENT_LIST" => array(),
		"DISHTYPE_LIST" => array(),
		"TAG_LIST" => array(0=>"фарш",),
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
 </div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>