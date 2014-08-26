<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="pager">
	<ul>
<?while($arResult["nStartPage"] <= $arResult["nEndPage"]):?>
	<?if ($arResult["nStartPage"] == $arResult["NavPageNomer"]):?>
		<li <?if($arResult["nStartPage"] == "1"){echo "class='first'";}?>><span><?=$arResult["nStartPage"]?></span></li>
	<?elseif($arResult["nStartPage"] == 1 && $arResult["bSavePage"] == false):?>
		<li><a href="/search/<?=$_REQUEST['s']?>/page/<?=$arResult["nStartPage"]?>/"><?=$arResult["nStartPage"]?></a></li>
	<?else:?>
		<li><a href="/search/<?=$_REQUEST['s']?>/page/<?=$arResult["nStartPage"]?>/"><?=$arResult["nStartPage"]?></a></li>
	<?endif?>
	<?$arResult["nStartPage"]++?>
<?endwhile?>
	</ul>
</div>