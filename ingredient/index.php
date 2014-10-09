<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "Переоценить значение пшеничной муки в питании человека, наверное, невозможно. Из пшеничной муки или с ее добавлением готовится подавляющее большинство видов хлебобулочных изделий, блины и оладьи, пироги, торты и пирожные, пицца, паста (правда, пасту делают из особого вида пшеничной муки), некоторые запеканки.");
$APPLICATION->SetPageProperty("keywords", "рецепты из пшеничной муки, рецепт хлеба из пшеничной муки, выпечка без пшеничной муки рецепты, блины рецепт из пшеничной муки");
$APPLICATION->SetTitle("Мука пшеничная");
if (CModule::IncludeModule("advertising")){
  $strBanner = CAdvBanner::Show("right_banner");
  $strBookBanner = CAdvBanner::Show("book_banner");
  $strSecond_banner = CAdvBanner::Show("second_right_banner");
}
?>
<div id="content"> 
<?$APPLICATION->IncludeComponent("custom:ingredient.detail", ".default", array(
	"IBLOCK_TYPE" => "recipes",
	"IBLOCK_ID" => "3",
	"ELEMENT_ID" => $_REQUEST["ID"],
	"ELEMENT_CODE" => $_REQUEST["CODE"],
	"CHECK_DATES" => "Y",
	"FIELD_CODE" => array(
		0 => "",
		1 => "",
	),
	"PROPERTY_CODE" => array(
		0 => "ablative",
		1 => "",
	),
	"IBLOCK_URL" => "",
	"AJAX_MODE" => "N",
	"AJAX_OPTION_JUMP" => "N",
	"AJAX_OPTION_STYLE" => "Y",
	"AJAX_OPTION_HISTORY" => "N",
	"CACHE_TYPE" => "N",
	"CACHE_TIME" => "36000000",
	"CACHE_GROUPS" => "Y",
	"META_KEYWORDS" => "-",
	"META_DESCRIPTION" => "-",
	"BROWSER_TITLE" => "-",
	"SET_STATUS_404" => "N",
	"SET_TITLE" => "Y",
	"INCLUDE_IBLOCK_INTO_CHAIN" => "Y",
	"ADD_SECTIONS_CHAIN" => "Y",
	"ADD_ELEMENT_CHAIN" => "N",
	"ACTIVE_DATE_FORMAT" => "d.m.Y",
	"USE_PERMISSIONS" => "N",
	"PAGER_TEMPLATE" => "",
	"DISPLAY_TOP_PAGER" => "N",
	"DISPLAY_BOTTOM_PAGER" => "Y",
	"PAGER_TITLE" => "Страница",
	"PAGER_SHOW_ALL" => "Y",
	"DISPLAY_DATE" => "Y",
	"DISPLAY_NAME" => "Y",
	"DISPLAY_PICTURE" => "Y",
	"DISPLAY_PREVIEW_TEXT" => "Y",
	"USE_SHARE" => "N",
	"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
);?>  
  <div id="banner_space">    
    <?if(strlen($strBanner) > 0){?>
      <div class="banner">
        <h5>Реклама</h5>
        <?=$strBanner?>
      </div>
    <?}?>
    <?$APPLICATION->IncludeComponent("custom:store.banner.vertical", "", Array(),false);?>
    <?if(strlen($strBookBanner) > 0){?>
      <div class="auxiliary_block book">
        <?=$strBookBanner?>
        <div class="clear"></div>
      </div>
    <?}?>    
    <?require($_SERVER["DOCUMENT_ROOT"]."/facebook_box.html");?>
    <?if(strlen($strSecond_banner) > 0){
      $APPLICATION->AddHeadString('<link rel="stylesheet" type="text/css" href="/css/floating_banner.css">');
      $APPLICATION->AddHeadScript('/js/floating_banner.js');?>
      <div class="clear"></div>
      <div class="banner" data-floating="true">
        <h5>Реклама</h5>
        <?=$strSecond_banner?>
      </div>
    <?}?>
  </div>
  <div class="clear"></div>
</div>
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>