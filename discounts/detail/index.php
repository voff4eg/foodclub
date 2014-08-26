<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");



if (CModule::IncludeModule("iblock") && $USER->IsAuthorized()){
    if (CModule::IncludeModule("advertising")){
        $strBanner = CAdvBanner::Show("right_banner");
    }

    $Element = intval($_REQUEST['p']);
    if($Element > 0)
    {
        $rsElement = CIBlockElement::GetById($Element);
        $arElement = $rsElement->GetNext();
        $APPLICATION->SetTitle($arElement['NAME']);
    }
} else {
   LocalRedirect("/auth/?backurl=/discounts/");
}


?>
<div id="content">
	<div id="text_space">
		<h1><?=$arElement['NAME']?></h1>
		<div class="padding_text">
			<?=$arElement['DETAIL_TEXT']?>
			<div class="clear"></div>
		</div>
		<div class="other_discounts"><a href="/discounts/">Скидки от других компаний</a></div>
	</div>
	<div id="banner_space">
		<?
		$rsRandomDoUKnow = CIBlockElement::GetList(array("rand"=>"asc"),array("IBLOCK_CODE"=>"do_u_know"),false,false,array("ID","PREVIEW_TEXT"));
		if($arRandomDoUKnow = $rsRandomDoUKnow->Fetch()){?>
		<div id="do-you-know-that" class="b-facts">
			<div class="b-facts__heading">Знаете ли вы что:</div>
			<div class="b-facts__content">
				<div class="b-facts__item" data-id="<?=$arRandomDoUKnow["ID"]?>">
					<?=$arRandomDoUKnow["PREVIEW_TEXT"]?>
				</div>
			</div>
			<div class="b-facts__more">
				<a href="#" class="b-facts__more__link">Еще</a>
			</div>
		</div>
		<?}?>
		<?if(strlen($strBanner) > 0){?><div class="banner"><?=$strBanner?></div><?}?>
		<?require($_SERVER["DOCUMENT_ROOT"]."/facebook_box.html");?>
	</div>
	<div class="clear"></div>
</div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>

