<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("keywords", "Постные рецепты, постные блюда, постные рецепты с фото, постное меню рецепты");
$APPLICATION->SetPageProperty("keywords", "постные рецепты, постные блюда, постные блюда рецепты, постное меню, постные салаты, постная кухня, постный стол, постное меню рецепты, постная кухня рецепты, постные салаты рецепты, постная еда, постная пища, постный стол рецепты, постные блюда с фото, борщ постный, постные рецепты с фото, постные щи, рецепты постных блюд с фото, 
постные вареники, постная кулинария");
$APPLICATION->SetPageProperty("description", "Разнообразные блюда посного стола с пошаговыми фотографиями этапов приготовления.");
$APPLICATION->SetTitle("Постные рецепты с фото, блюда для постного стола. Foodclub.ru");
?> 
<div id="content"> 
<style type="text/css">
div.pages_recipes div.item {
	padding-bottom:10px;}
div.pages_recipes {margin:20px 0 0 0;}
div.pages_recipes div.item div.link {display:block;}
div.pages_recipes div.photo {
	float:left;
	width:50px;
	padding:0 12px 0 0;}
div.pages_recipes div.big_photo {
	position:relative;
	display:none;}
div.pages_recipes div.big_photo div {
	position:absolute;
	top:-3px;
	left:-3px;
	z-index:4;}
div.pages_recipes h2 {
	color:#333333;
	margin:40px 0 15px;}

div.recipes_blocks {margin:30px 0 10px 0;}
div.recipes_blocks h2 {
	margin-bottom:20px;
	color:#333333;}
div.recipes_blocks p {margin:10px 0;}
div.recipes_blocks div.item {
	float:left;
	width:200px;
	display:inline;
	margin:0 30px 30px 0;}
div.recipes_blocks div.item h3 {margin:10px 0;}
div.recipes_blocks div.item p {
	font-size:10pt;
	margin:0;}
</style>
 			 
  <h1>Постные рецепты с фото</h1>
 			 
  <p style="width: 700px;">Великий пост в 2014 году начинается <span style="text-align: justify;">с 3 марта по 20 апреля</span> включительно. В период Великого поста христиане не едят животной пищи &mdash; мясо, яйца, молочные продукты, масло. Растительное масло можно добавлять в пищу по субботам и воскресеньям, за исключением субботы на последней, Страстной неделе. Подборка постных рецептов на Foodclub'е содержит в основном рецепты с использованием растительного масла, поэтому пригодится в выходные дни, а так же в дни церковных праздников. С другой стороны, многие рецепты могут быть приготовлены и без масла, используя главную идею сочетания прдуктов. Однако, стоит помнить, что постная пища не должна быть слишком изысканной.</p>
 			 
  <p style="width: 700px;">Как правило, на время Великого поста попадает два больших церковных праздника, в которые постящимся разрешается употребление в пищу рыбы — Благовещение Пресвятой Богородицы (7 апреля) и Вход Господень в Иерусалим (Вербное воскресенье), который отмечается за неделю до Пасхи. Поэтому в список постных блюд включены также блюда из рыбы, которые можно приготовить в эти праздники.</p>
 			 
  <p style="width: 700px;">Поделиться своими рецептами постных блюд Вы можете, добавив их с помощью <a href="/recipe/add/" >формы добавления рецепта</a>.</p>
 			 
  <p style="width: 700px;">Источник информации о правилах соблюдения поста — <a href="http://www.bogoslovy.ru/post.htm" target="_blank" >http://www.bogoslovy.ru</a></p>
 
  <div><?$APPLICATION->IncludeComponent("custom:recipes.list", ".default", array(
	"IBLOCK_TYPE" => "-",
	"IBLOCK_ID" => "5",
	"TITLE" => "Постные рецепты",
	"KITCHEN_LIST" => array(
	),
	"MAIN_INGREDIENT_LIST" => array(
	),
	"DISHTYPE_LIST" => array(
	),
	"TAG_LIST" => array(
		0 => "постные блюда",
	),
	"NEWS_COUNT" => "64",
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
	),
	false
);?></div>
		 </div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>