<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("keywords", "пирамидки из кокоса, кокосовое печенье, печенье, Выпечка, рецепт с фото, свекла, печенье со свеклой, сметана, выпечка для детей, красивая выпечка, кокос, выпечка, домашняя выпечка, сладкая выпечка, домашнее печенье, печенье с кокосом, Пирожное, шишки, новогоднее блюдо, зимнее настроение, рецепт, к чаю, сгущёнка, сгущённое молоко, грецкие орехи, песочное тесто, праздничный стол, Печенье \"Вишенка\", песочное печенье, печенье с кремом, десерт, печень рецепт, рецепт печенья, печенье для детей, вкусное печенье, фигурное печенье, оригинальное печенье, Выпечка без яиц, Постное печенье рецепт, постные рецепты, постная выпечка, мука, яйцо, масло сливочное, пирог к чаю, пирог с начинкой, выпечка с фруктами, готовим с фруктами, готовим детям, ананас, пецепт, сахарная пудра, слоеное тесто, Спекулатиус, рождественское печенье, немецкая кухня, печенье с орехами, фундук, печенье в сковороде, печенье сердце рецепт, печенье в форме сердца, домашнее печенье в форме сердца, день влюбленных, сюрпризы на день влюбленных, что приготовить на день влюбленных");
$APPLICATION->SetPageProperty("description", "Рецепты печенья с пошаговыми фотографиями.");
$APPLICATION->SetTitle("Рецепты печенья с фотографиями на Foodclub.ru");
?> 
<h1> Рецепты печенья</h1>
 
<p>Печенье&nbsp;&mdash; это не&nbsp;только сладость к&nbsp;чаю или детская радость. Красивое печенье можно преподнести в&nbsp;качестве милого сувенира, несладкое может быть отличной самостоятельной закуской, да&nbsp;и&nbsp;вообще его можно брать с&nbsp;собой как легкий и&nbsp;удобный перекус. Ознакомьтесь с&nbsp;подборкой рецептов печенья, созданных нашими кулинарами и&nbsp;выберите свое любимое!</p>
 
<br />
 
<div>
	<div class="b-social-buttons__item b-fb-like">
		<div class="fb-share-button" data-href="http://www.foodclub.ru/recepty-pecheniya/" data-type="button_count"></div>
	</div>
	<div class="b-social-buttons__item b-vk-like">
		<div id="vk_like"></div>
		<script type="text/javascript">
		VK.Widgets.Like("vk_like", {type: "mini", height: 20, pageUrl: "http://www.foodclub.ru/recepty-pecheniya/"});
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
		"TITLE" => "Печенье рецепты",
		"KITCHEN_LIST" => array(),
		"MAIN_INGREDIENT_LIST" => array(),
		"DISHTYPE_LIST" => array(0=>"100",),
		"TAG_LIST" => array(),
		"NEWS_COUNT" => "32",
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