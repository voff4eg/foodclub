<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$bF = true;
?>
<div class="pager">
	<ul>
<?while($arResult["nStartPage"] <= $arResult["nEndPage"]):?>
	<?if ($arResult["nStartPage"] == $arResult["NavPageNomer"]):?>
		<li <?if($bF){echo "class='first'";}?>><span><?=$arResult["nStartPage"]?></span></li>
	<?elseif($arResult["nStartPage"] == 1 && $arResult["bSavePage"] == false):?>
		<li <?if($bF){echo "class='first'";}?>><a href="./?PAGEN_1=<?=$arResult["nStartPage"]?>"><?=$arResult["nStartPage"]?></a></li>
	<?else:?>
		<li <?if($bF){echo "class='first'";}?>><a href="./?PAGEN_1=<?=$arResult["nStartPage"]?>"><?=$arResult["nStartPage"]?></a></li>
	<?endif?>
	<?$arResult["nStartPage"]++; $bF = false;?>
<?endwhile?>
	</ul>
</div>
