<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if (!empty($arResult)):?>
<div id="bottom_nav">
<?foreach($arResult as $k=>$arItem):?><?if(intval($k)>0){?><span class="separator">|</span><?}?><?if($arItem["SELECTED"]):?><span><?=$arItem["TEXT"]?></span><?else:?><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a><?endif?><?endforeach?>
</div>
<?endif?>
