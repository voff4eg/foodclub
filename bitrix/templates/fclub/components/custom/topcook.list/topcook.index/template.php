<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<noindex>
<h3>Самые активные кулинары</h3>
<table>
<?foreach($arResult["USERS"] as $key => $arItem):?>
<tr>
	<td class="number"><?=$key+1?>.</td>
	<td class="cook">
	<a href="/profile/<?=$arItem['ID']?>/" class="nickname"><?=$arItem["LOGIN"]?></a>
	</td>
	<td class="mark"><?if($arItem["UF_RAITING"] > 0){echo $arItem["UF_RAITING"];}else{echo "0";}?></td>
</tr>
<?endforeach;?>
</table>
</noindex>