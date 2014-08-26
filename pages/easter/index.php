<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("keywords", "кулич рецепт, пасха рецепт, рецепт пасхального кулича, рецепт пасхи творожной");
$APPLICATION->SetPageProperty("description", "Русские куличи и пасхи традиционно очень сдобные, готовятся к празднику Пасхи.");
$APPLICATION->SetTitle("Рецепт кулича, пасхальный кулич рецепт. Foodclub");
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
 			
  <h1>Рецепт кулича</h1>
 			
  <p style="width: 700px;">Русский пасхальный кулич по своему составу, структуре и вкусу является совершенно уникальным изделием. Традиционно в тесто кладут как можно больше сдобы, сладких добавок, орехов. Вымешивают тесто особо тщательно и долго, поэтому конечное изделие отличается от обычного сдобного дрожжевого пирога и совсем не похоже на традиционную выпечку, принятую у католической церкви, например. Рецепты куличей отличаются по количеству сдобных ингредиентов, иногда кулич подкрашивают шафраном, чтобы придать ему золотистый, царственный оттенок.</p>
 			
  <div><?$APPLICATION->IncludeComponent("custom:recipes.list", ".default", array(
	"IBLOCK_TYPE" => "-",
	"IBLOCK_ID" => "5",
	"TITLE" => "Пасхальные рецепты",
	"KITCHEN_LIST" => array(
	),
	"MAIN_INGREDIENT_LIST" => array(
	),
	"DISHTYPE_LIST" => array(
	),
	"TAG_LIST" => array(
		0 => "пасха",
	),
	"NEWS_COUNT" => "32",
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
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>