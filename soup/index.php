<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "Суп — начало всякого обеда. Потому должен быть приготовлен с любовью, добрыми намерениями и в радостном настроении. А появится доброе расположение как только Вы увидите рецепт супа, представленный в подробных пошаговых фотографиях. Готовить станет легко и очень увлекательно.");
$APPLICATION->SetPageProperty("keywords", "супы рецепты, рецепты супов с фото, суп харчо рецепт, гороховый суп рецепт, рецепты гороховых супов, гороховой суп рецепт, суп +с фрикадельками рецепт, сырный суп рецепт, суп пюре рецепты, грибной суп рецепт, куриный суп рецепт, рецепты куриных супов, суп с клецками рецепт");
$APPLICATION->SetTitle("Рецепты супов с фото на Foodclub.ru");
?> 
<h1> Рецепты супов</h1>
 
<p>Суп&nbsp;&mdash; начало всякого обеда. Потому должен быть приготовлен с&nbsp;любовью, добрыми намерениями и&nbsp;в&nbsp;радостном настроении. А&nbsp;появится доброе расположение как только Вы&nbsp;увидите рецепт супа, представленный в&nbsp;подробных пошаговых фотографиях. Готовить станет легко и&nbsp;очень увлекательно. Радуйте своих близких и&nbsp;не&nbsp;забывайте оставлять свои отзывы к&nbsp;понравившимся блюдам! А&nbsp;если в&nbsp;Вашей коллекции рецептов есть новый, ещё не&nbsp;представленный на&nbsp;сайте суп, добавьте его с&nbsp;помощью формы<a href="http://www.foodclub.ru/recipe/add/" target="_blank" > добавления рецепта</a>.</p>
 
<p>Узнайте о каких рецептах супов и полезных советах <a href="http://www.foodclub.ru/posts_search/?q=%D0%A1%D1%83%D0%BF%D1%8B" target="_blank" >пишут в наших кулинарных клубах</a>.</p>
 
<br />
 
<div>
	<div class="b-social-buttons__item b-fb-like">
		<div class="fb-share-button" data-href="http://www.foodclub.ru/soup/" data-type="button_count"></div>
	</div>
	<div class="b-social-buttons__item b-vk-like">
		<div id="vk_like"></div>
		<script type="text/javascript">
		VK.Widgets.Like("vk_like", {type: "mini", height: 20, pageUrl: "http://www.foodclub.ru/soup/"});
		</script>
	</div>
	<div class="i-clearfix"></div>
</div>
<div><?$APPLICATION->IncludeComponent("custom:recipes.list", ".default", array(
	"IBLOCK_TYPE" => "-",
	"IBLOCK_ID" => "5",
	"TITLE" => "Рецепты супов",
	"KITCHEN_LIST" => array(
	),
	"MAIN_INGREDIENT_LIST" => array(
	),
	"DISHTYPE_LIST" => array(
		0 => "1",
		1 => "59986",
		2 => "59985",
	),
	"INGREDIENT_ID" => array(
	),
	"TAG_LIST" => array(
	),
	"NEWS_COUNT" => "120",
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
	),
	false
);?></div>
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>