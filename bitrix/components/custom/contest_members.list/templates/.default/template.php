<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?global $USER;?>
<?if(count($arResult["ITEMS"])):?>
<div class="members">
<h2>Участники</h2>
<?foreach($arResult["ITEMS"] as $key=>$arItem):?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>
	<div class="recipe_item" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
		<?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arItem["PREVIEW_PICTURE"])):?>
			<?if(!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])):?>
				<div class="image"><a href="/detail/<?=$arItem["ID"]?>/"><img class="preview_picture" border="0" src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" width="<?=$arItem["PREVIEW_PICTURE"]["WIDTH"]?>" height="<?=$arItem["PREVIEW_PICTURE"]["HEIGHT"]?>" alt="<?=$arItem["NAME"]?>" title="<?=$arItem["NAME"]?>" /></a></div>
			<?else:?>
				<div class="image"><img class="preview_picture" border="0" src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" width="<?=$arItem["PREVIEW_PICTURE"]["WIDTH"]?>" height="<?=$arItem["PREVIEW_PICTURE"]["HEIGHT"]?>" alt="<?=$arItem["NAME"]?>" title="<?=$arItem["NAME"]?>" /></div>
			<?endif;?>
		<?endif?>
		<?if(intval($arItem["VOTES_COUNT"])):?><div class="votes">
			<div><?=intval($arItem["VOTES_COUNT"]);?></div>
		</div><?endif;?>
		<?if($arParams["DISPLAY_NAME"]!="N" && $arItem["NAME"]):?>
			<?if(!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])):?>
				<h3><a href="/detail/<?=$arItem["ID"]?>/"><?echo $arItem["NAME"]?></a></h3>
			<?else:?>
				<h3><?echo $arItem["NAME"]?></h3>
			<?endif;?>
		<?endif;?>
		<div class="author">От: <a href="/profile/<?=$arItem["CREATED_BY"]?>/"><?=$arItem["LOGIN"]["LOGIN"]?></a></div>
		<!--<?if($USER->IsAuthorized()):?>
			<?if(!in_array($arItem['ID'],$arResult["VOTED"])):?>
				<a class="vote" href="?vote=<?=$arItem["ID"]?>">Проголосовать</a>
			<?else:?>
				<span class="vote">Спасибо!</span>
			<?endif;?>
		<?else:?>
			<p>Для голосования <a href="/auth/">авторизуйтесь</a>!</p>
		<?endif;?>-->
	</div>
	<?if($key %2 != 0):?><hr><?endif;?>
<?endforeach;?>
<div class="clear"></div>
</div>
<?endif;?>
