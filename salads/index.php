<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Рецепт салатов на Foodclub.ru");
?> 
<h1> Рецепты салатов</h1>
 
<p>Салат считается одним из&nbsp;самых незатейливых блюд, приготовить его может каждый, достаточно лишь порезать ингредиенты, заправить соусом и&nbsp;хорошо перемешать. Может поэтому на&nbsp;свете существует такое количество рецептов салатов, а&nbsp;у&nbsp;каждого ещё и&nbsp;по&nbsp;нескольку вариантов. Однако и&nbsp;в&nbsp;этом, казалось&nbsp;бы, простом деле есть свои тонкости, сложившиеся правила, интересные приемы. Например, к&nbsp;какому набору ингредиентов какую подобрать заправку&nbsp;&mdash; дело не&nbsp;только вкуса, но&nbsp;и&nbsp;большого кулинарного опыта. Как правильно соус приготовить, нужно&nbsp;ли перемешивать, как выкладывать ингредиенты. Стоит изменить хотя&nbsp;бы один штрих и&nbsp;вы&nbsp;получите совершенно иное блюдо.</p>
 
<br />
 
<div>
	<div class="b-social-buttons__item b-fb-like">
		<div class="fb-share-button" data-href="http://www.foodclub.ru/salads/" data-type="button_count"></div>
	</div>
	<div class="b-social-buttons__item b-vk-like">
		<div id="vk_like"></div>
		<script type="text/javascript">
		VK.Widgets.Like("vk_like", {type: "mini", height: 20, pageUrl: "http://www.foodclub.ru/salads/"});
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
		"TITLE" => "Рецепты салатов",
		"KITCHEN_LIST" => array(),
		"MAIN_INGREDIENT_LIST" => array(),
		"DISHTYPE_LIST" => array(0=>"38",),
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