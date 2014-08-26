<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "Сегодня множество тортов и пирожных продается в магазинах, но разве может сравниться душевный домашний торт, сделанный собственноручно из отборных продуктов, с магазинным? Торты дома готовить не так уж сложно, просто нужен проверенный рецепт и хорошее настроение. Рецепты тортов мы подобрали, добавьте немного настроения и готовьте на радость близким!");
$APPLICATION->SetPageProperty("keywords", "торты рецепты, торты рецепты с фото, торт наполеон рецепт, домашний торт рецепт, рецепты домашних тортов, рецепты тортов в домашних условиях, печеночный торт рецепт, простые торты рецепты, бисквитный торт рецепт, пошаговый рецепт торта, шоколадный торт рецепт, рецепт вкусного торта, классические рецепты тортов, торт сметанный рецепт, торт наполеон рецепт с фото, классические рецепты тортов, фото рецепты тортов домашних условиях, бисквитный торт рецепт с фото, шоколадный торт рецепт с фото, творожный торт рецепт, рецепты тортов пошагово, блинный торт рецепт, рецепты блинных тортов, торт со сметаны рецепт");
$APPLICATION->SetTitle("Рецепт тортов c поашговыми фотографиями Foodclub.ru");
?> 
<h1> Рецепты тортов</h1>
 
<p>Сегодня множество тортов и&nbsp;пирожных продается в&nbsp;магазинах, но&nbsp;разве может сравниться душевный домашний торт, сделанный собственноручно из&nbsp;отборных продуктов, с&nbsp;магазинным? Торты дома готовить не&nbsp;так уж&nbsp;сложно, просто нужен проверенный рецепт и&nbsp;хорошее настроение. Рецепты тортов мы&nbsp;подобрали, добавьте немного настроения и&nbsp;готовьте на&nbsp;радость близким!</p>
<br>
<div>
	<div class="b-social-buttons__item b-fb-like">
		<div class="fb-share-button" data-href="http://www.foodclub.ru/cakes/" data-type="button_count"></div>
	</div>
	<div class="b-social-buttons__item b-vk-like">
		<div id="vk_like"></div>
		<script type="text/javascript">
		VK.Widgets.Like("vk_like", {type: "mini", height: 20, pageUrl: "http://www.foodclub.ru/cakes/"});
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
		"TITLE" => "Торты рецепты",
		"KITCHEN_LIST" => array(),
		"MAIN_INGREDIENT_LIST" => array(),
		"DISHTYPE_LIST" => array(0=>"6715",),
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