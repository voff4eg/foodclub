<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>
<div id="text_space">
<?if($arParams["DISPLAY_NAME"]!="N" && $arResult["NAME"]):?>	
	<h1><?=$arResult['NAME']?></h1>
<?endif;?>
		
<?if($arParams["DISPLAY_PREVIEW_TEXT"]!="N" && $arResult["FIELDS"]["PREVIEW_TEXT"]):?>
	<?=$arResult["FIELDS"]["PREVIEW_TEXT"];unset($arResult["FIELDS"]["PREVIEW_TEXT"]);?>
<?endif;?>
<?if($arResult["NAV_RESULT"]):?>
	<?if($arParams["DISPLAY_TOP_PAGER"]):?><?=$arResult["NAV_STRING"]?><br /><?endif;?>
	<?echo $arResult["NAV_TEXT"];?>
	<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?><br /><?=$arResult["NAV_STRING"]?><?endif;?>
<?elseif(strlen($arResult["DETAIL_TEXT"])>0):?>
	<?echo $arResult["DETAIL_TEXT"];?>
<?else:?>
	<?echo $arResult["PREVIEW_TEXT"];?>
<?endif?>
	
<?if(array_key_exists("USE_SHARE", $arParams) && $arParams["USE_SHARE"] == "Y")
{
	?>
	<div class="news-detail-share">
		<noindex>
		<?
		$APPLICATION->IncludeComponent("bitrix:main.share", "", array(
				"HANDLERS" => $arParams["SHARE_HANDLERS"],
				"PAGE_URL" => $arResult["~DETAIL_PAGE_URL"],
				"PAGE_TITLE" => $arResult["~NAME"],
				"SHORTEN_URL_LOGIN" => $arParams["SHARE_SHORTEN_URL_LOGIN"],
				"SHORTEN_URL_KEY" => $arParams["SHARE_SHORTEN_URL_KEY"],
				"HIDE" => $arParams["SHARE_HIDE"],
			),
			$component,
			array("HIDE_ICONS" => "Y")
		);
		?>
		</noindex>
	</div>
	<?
}
?><?$title = ($arResult["PROPERTIES"]["ablative"]["VALUE"] ? $arResult["PROPERTIES"]["ablative"]["VALUE"] : $arResult["NAME"]);?>
<?$APPLICATION->IncludeComponent("custom:recipes.list", "three.line", array(
	"IBLOCK_TYPE" => "-",
	"IBLOCK_ID" => "5",
	"TITLE" => "Рецепты с {$title}",
	"KITCHEN_LIST" => "",
	"MAIN_INGREDIENT_LIST" => "",
	"DISHTYPE_LIST" => "",
	"INGREDIENT_ID" => "{$arResult['ID']}",
	"TAG_LIST" => "",
	"NEWS_COUNT" => "60",
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
	false,
	array(
	"ACTIVE_COMPONENT" => "Y"
	)
);?>
</div>