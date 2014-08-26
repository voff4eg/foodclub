<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$strNavQueryString = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"]."&amp;" : "");
$strNavQueryStringFull = ($arResult["NavQueryString"] != "" ? "?".$arResult["NavQueryString"] : "");

if($arResult['NavPageNomer'] <= 6){
	$Low = 1;
	if($arResult['NavPageCount'] >= 11){
		$Hight = 10;
	} else {
		$Hight = $arResult['NavPageCount'];
	}
} elseif($arResult['NavPageNomer'] > 6){
	
	$Low = $arResult['NavPageNomer'] - 5;
	$Hight = $arResult['NavPageNomer'] + 5;
	
	if($Hight > $arResult['NavPageCount']){
		$Hight = $arResult['NavPageCount'];
		$Low =$Low - (5 - ($Hight - $arResult['NavPageNomer']));
		if($Low <= 0){
			$Low = 1;
		}
	}
}

if($arResult['NavPageCount'] > 1){
?>
<div class="pager">
	<?if ($arResult["NavPageNomer"] > 1):?>
		<?if($arResult["NavPageNomer"] > 1):?>
			<link rel="prev" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]-1)?>" id="PrevLink" />
		<?endif?>
	<?endif?>
	<?if($arResult["NavPageNomer"] < $arResult["NavPageCount"]):?>
		<link rel="next" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]+1)?>" id="NextLink" />
	<?endif?>
	<script type="text/javascript">document.onkeydown = NavigateThrough;</script>
	
	<?if ($arResult["NavPageNomer"] > 1):?>
		<?if($arResult["NavPageNomer"] > 1):?>
			<div class="backward pointer"><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]-1)?>"><img width="38" height="50" alt="" src="/images/spacer.gif"/></a></div>
		<?endif?>
	<?endif?>
	<ul>
	<?
	$bFirst = true;
	for($i = $Low; $i <= $Hight; $i++){?>
		<?if ($i == $arResult["NavPageNomer"]):?>
			<li <?if($bFirst){echo 'class="first"'; $bFirst = false;}?>><span><?=$i?></span></li>
		<?else:?>
			<li <?if($bFirst){echo 'class="first"'; $bFirst = false;}?>><a title="Кулинарные рецепты с фотографиями" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$i?>"><?=$i?></a></li>
		<?endif?>
	<?}?>
	</ul>
	<?if($arResult["NavPageNomer"] < $arResult["NavPageCount"]):?>
		<div class="forward pointer"><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]+1)?>"><img width="38" height="50" alt="" src="/images/spacer.gif"/></a></div>
	<?endif?>
</div>
<?}?>
