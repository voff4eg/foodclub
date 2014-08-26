<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "Некоторые считают, что готовить мороженое дома нет смысла, ведь столько разного мороженого продается в магазинах. Но если вчитаться в состав на упаковке, то холодное лакомство начинает казаться не столь желанным: уж больно много всего написано и далеко не все хотелось бы есть. А домашнее мороженое позволяет использовать только лучшие, натуральные продукты, да к тому же тут такой простор для фантазии, что можно все лето экспериментировать с сезонными фруктами и ягодами, а зимой — со специями, шоколадом и вареньем.");
$APPLICATION->SetPageProperty("keywords", "рецепт мороженого, рецепт домашнего морожена, домашние рецепты морожена, домашнее мороженое рецепт,  домашние мороженое рецепт, рецепт морожена в домашних условиях, рецепт мороженого в домашних условиях, молочное мороженое рецепт, мороженое рецепт фото,");
$APPLICATION->SetTitle("Рецепты мороженого на Foodclub.ru");
?> 
<h1> Рецепты мороженого</h1>
 
<p>Некоторые считают, что готовить мороженое дома нет смысла, ведь столько разного мороженого продается в&nbsp;магазинах. Но&nbsp;если вчитаться в&nbsp;состав на&nbsp;упаковке, то&nbsp;холодное лакомство начинает казаться не&nbsp;столь желанным: уж&nbsp;больно много всего написано и&nbsp;далеко не&nbsp;все хотелось&nbsp;бы есть. А&nbsp;домашнее мороженое позволяет использовать только лучшие, натуральные продукты, да&nbsp;к&nbsp;тому&nbsp;же тут такой простор для фантазии, что можно все лето экспериментировать с&nbsp;сезонными фруктами и&nbsp;ягодами, а&nbsp;зимой&nbsp;&mdash; со&nbsp;специями, шоколадом и&nbsp;вареньем.</p>
 
<p> 
  <br />
 </p>

<div>
	<div class="b-social-buttons__item b-fb-like">
		<div class="fb-share-button" data-href="http://www.foodclub.ru/icecream/" data-type="button_count"></div>
	</div>
	<div class="b-social-buttons__item b-vk-like">
		<div id="vk_like"></div>
		<script type="text/javascript">
		VK.Widgets.Like("vk_like", {type: "mini", height: 20, pageUrl: "http://www.foodclub.ru/icecream/"});
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
		"TITLE" => "Рецепты мороженого",
		"KITCHEN_LIST" => array(),
		"MAIN_INGREDIENT_LIST" => array(),
		"DISHTYPE_LIST" => array(0=>"38766",),
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