<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("keywords", "песто, соус песто, песто рецепт, соус песто рецепт, паста песто, песто фото, соус песто фото, песто рецепт с фото");
$APPLICATION->SetPageProperty("description", "Соусы обладают волшебным свойством дополнять и украшать вкус простых блюд так, что они становятся совершенно не простыми! Название соуса песто произошло от слова Pesto (растираю, давлю), которое передает суть технологии приготовления соуса. Самым известным и классическим видом песто считается генуэзский песто, который готовится с использованием базилика, кедровых орехов, пармезана и оливкового масла. Но по такой же технологии можно готовить песто из разных видов зелени, сочетая их с орехами, можно делать и десертные варианты соусов. На этой странице вы найдете рецепты песто и блюда с использованием соуса.");
$APPLICATION->SetTitle("Песто на Foodclub.ru");
?> 
<h1> Песто</h1>
 
<p>Соусы обладают волшебным свойством дополнять и&nbsp;украшать вкус простых блюд так, что они становятся совершенно не&nbsp;простыми! Название соуса песто произошло от&nbsp;слова Pesto (растираю, давлю), которое передает суть технологии приготовления соуса. Самым известным и&nbsp;классическим видом песто считается генуэзский песто, который готовится с&nbsp;использованием базилика, кедровых орехов, пармезана и&nbsp;оливкового масла. Но&nbsp;по&nbsp;такой&nbsp;же технологии можно готовить песто из&nbsp;разных видов зелени, сочетая их&nbsp;с&nbsp;орехами, можно делать и&nbsp;десертные варианты соусов. На&nbsp;этой странице вы&nbsp;найдете рецепты песто и&nbsp;блюда с&nbsp;использованием соуса.</p>
 
<p> 
  <br />
 </p>
 
<div> 	
  <div class="b-social-buttons__item b-fb-like"> 		
    <div class="fb-share-button" data-href="http://www.foodclub.ru/pesto/" data-type="button_count"></div>
   	</div>
 	
  <div class="b-social-buttons__item b-vk-like"> 		
    <div id="vk_like"></div>
   		
<script type="text/javascript">
		VK.Widgets.Like("vk_like", {type: "mini", height: 20, pageUrl: "http://www.foodclub.ru/pesto/"});
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
		"TITLE" => "Рецепты с соусом песто",
		"KITCHEN_LIST" => array(),
		"MAIN_INGREDIENT_LIST" => array(),
		"DISHTYPE_LIST" => array(),
		"INGREDIENT_ID" => array(),
		"TAG_LIST" => array(0=>"песто",),
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