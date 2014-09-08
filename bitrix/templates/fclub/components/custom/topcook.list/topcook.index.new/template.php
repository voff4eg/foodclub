<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<h2>
	<a href="/specialists/">Наши кулинары</a>
</h2>
<ul>
<?foreach($arResult["USERS"] as $key => $arItem):?>
<li>
	<?if(strlen($arItem["NAME"]) > 0 && strlen($arItem["LAST_NAME"]) > 0){
     	$name = $arItem["NAME"]." ".$arItem["LAST_NAME"];
 	}else{
 		$name = $arItem["LOGIN"];
 	}?>
	<?if(strlen($arItem["PERSONAL_PHOTO"]) > 0): $file = CFile::ResizeImageGet($arItem["PERSONAL_PHOTO"], array('width'=>50, 'height'=>50), BX_RESIZE_IMAGE_PROPORTIONAL, true);?><a class="icon" href="/profile/<?=$arItem['ID']?>/"><img width="50" height="50" alt="<?=$name?>" src="<?=$file["src"]?>"></a><?else:?><a class="icon" href="/profile/<?=$arItem['ID']?>/"><img width="50" height="50" alt="<?=$arItem["LOGIN"]?>" src="/images/avatar/avatar.jpg"></a><?endif;?>
	<div class="author"><a href="/profile/<?=$arItem['ID']?>/"><?=$name?></a></div>
	<?if(strlen($arItem['UF_ABOUT_SELF'])):?><div class="intro"><?if(strlen($arItem['UF_ABOUT_SELF']) > 100){echo substr($arItem['UF_ABOUT_SELF'], 0 ,100)."...";}else{echo $arItem['UF_ABOUT_SELF'];}?></div><?endif;?>
	<?if($arItem["UF_RAITING"] > 0):?><div class="rating"><span><?=$arItem["UF_RAITING"]?></span></div><?endif;?>
</li>
<?endforeach;?>
</ul>