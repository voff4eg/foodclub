<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
global $USER;?>
<?if(count($arResult["ITEMS"])):?>
<div class="recipe_carousel">
<?foreach($arResult["ITEMS"] as $key=>$arItem):?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>
	<div class="item" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
		<?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arItem["PREVIEW_PICTURE"])):?>
			<a href="/detail/<?=$arItem["ID"]?>/" class="photo"><img class="preview_picture" border="0" src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" width="<?=$arItem["PREVIEW_PICTURE"]["WIDTH"]?>" height="<?=$arItem["PREVIEW_PICTURE"]["HEIGHT"]?>" alt="<?=$arItem["NAME"]?>" title="<?=$arItem["NAME"]?>" /></a>
		<?endif?>
		<?if($arParams["DISPLAY_NAME"]!="N" && $arItem["NAME"]):?>
			<?if(!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])):?>
				<h3><a href="/detail/<?=$arItem["ID"]?>/"><?echo $arItem["NAME"]?></a></h3>
			<?else:?>
				<h3><?echo $arItem["NAME"]?></h3>
			<?endif;?>
		<?endif;?>
		<div class="author">От: <a href="/profile/<?=$arItem["CREATED_BY"]?>/"><?=$arItem["LOGIN"]["LOGIN"]?></a></div>
		<div class="vote"><span class="num"><?=intval($arItem["VOTES_COUNT"]);?></span></div>
		<!--<div class="vote">
			<?if(!$USER->IsAuthorized()):?>	
				<a class="button sign_in" href="#"></a>
				<?if(intval($arItem["VOTES_COUNT"])):?>
					<span class="num"><?=intval($arItem["VOTES_COUNT"]);?></span>
				<?endif;?>
				<span class="reg">Чтобы проголосовать <a href="/registration/">зарегистрируйтесь</a> или <a class="sign_in" href="#">авторизуйтесь</a></span>
			<?else:?>
				<?if(!in_array($arItem['ID'],$arResult["VOTED"])):?><a class="button" href="?vote=<?=$arItem["ID"]?>"></a><?else:?><span class="button"><span>Спасибо</span></span><?endif;?><?if(intval($arItem["VOTES_COUNT"])):?>
					<span class="num"><?=intval($arItem["VOTES_COUNT"]);?></span>
				<?endif;?>
			<?endif;?>
		</div>-->
	</div>
<?endforeach;?>
<div class="clear"></div>
</div>
<?endif;?>