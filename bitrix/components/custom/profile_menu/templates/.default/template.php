<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult)):?>
<menu class="b-personal-page__menu">

<?
foreach($arResult as $arItem):
	if(intval($_REQUEST["u"])){		
		if(strpos($arItem["LINK"],"profile") !== false){
			$arItem["LINK"] = str_replace("/profile/", "/profile/".intval($_REQUEST["u"])."/", $arItem["LINK"]);
		}elseif(strpos($arItem["LINK"],"personal") !== false){
			$arItem["LINK"] = $arItem["LINK"]."?u=".intval($_REQUEST["u"]);
		}
		if(strpos($arItem["LINK"],"subscribe") !== false && (intval($_REQUEST["u"]) != CUser::GetID())){
			$arItem["LINK"] = "";
		}
	}
	if($arParams["MAX_LEVEL"] == 1 && $arItem["DEPTH_LEVEL"] > 1) 
		continue;
?><?if($arItem["LINK"]):?>
	<?if($arItem["LINK"] == $APPLICATION->GetCurDir() || $arItem["LINK"] == $APPLICATION->GetCurPage()):?>
		<li class="b-personal-page__menu__item"><span class="b-personal-page__menu__item__link"><?=$arItem["TEXT"]?></span></li>
	<?else:?>
		<li class="b-personal-page__menu__item"><a href="<?=$arItem["LINK"]?>" class="b-personal-page__menu__item__link"><?=$arItem["TEXT"]?></a></li>		
	<?endif?>
	<?endif;?>
<?endforeach?>

</menu>
<?endif?>