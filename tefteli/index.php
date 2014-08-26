<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "Тефтели — небольшие шарики из мясного или рыбного фарша, есть в разных национальных кухнях мира, и под влиянием традиций этих кухонь родилось огромное разнообразие рецептов тефтелей. От котлет тефтели отличаются не только размером и формой, но и способом приготовления. Котлеты чаще всего жарят, а тефтели преимущественно тушат в соусе, запекают, готовят на пару. Эксперименты с фаршем, использование соусов и подлив дает большой простор для фантазии и позволяет готовить самые разнообразные рецепты тефтелей.");
$APPLICATION->SetPageProperty("keywords", "тефтели, тефтели рецепт, тефтели с подливкой, тефтели с рисом, тефтели в мультиварке, тефтели в духовке, как приготовить тефтели, фото тефтели, тефтели в томатном, тефтели в томатном соусе, тефтели рецепт с фото, тефтели с рисом рецепт, тефтели из фарша, приготовление тефтелей, тефтели вкусные");
$APPLICATION->SetTitle("Рецепты тефтелей");
?> 
<h1> Тефтели</h1>
 
<div>Тефтели &mdash; небольшие шарики из мясного или рыбного фарша, есть в разных национальных кухнях мира, и под влиянием традиций этих кухонь родилось огромное разнообразие рецептов тефтелей. От котлет тефтели отличаются не только размером и формой, но и способом приготовления. Котлеты чаще всего жарят, а тефтели преимущественно тушат в соусе, запекают, готовят на пару. Эксперименты с фаршем, использование соусов и подлив дает большой простор для фантазии и позволяет готовить самые разнообразные рецепты тефтелей.</div>
 
<br />

<div>
	<div class="b-social-buttons__item b-fb-like">
		<div class="fb-share-button" data-href="http://www.foodclub.ru/tefteli/" data-type="button_count"></div>
	</div>
	<div class="b-social-buttons__item b-vk-like">
		<div id="vk_like"></div>
		<script type="text/javascript">
		VK.Widgets.Like("vk_like", {type: "mini", height: 20, pageUrl: "http://www.foodclub.ru/tefteli/"});
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
		"TITLE" => "Рецепты тефтелей",
		"KITCHEN_LIST" => array(),
		"MAIN_INGREDIENT_LIST" => array(),
		"DISHTYPE_LIST" => array(),
		"TAG_LIST" => array(0=>"тефтели",),
		"NEWS_COUNT" => "20",
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