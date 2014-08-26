<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
global $USER;
?>
<?if(!empty($arResult["arRecipe"]["ITEMS"])):?>
<div id="text_space">
	<div class="b-personal-page__recipes">
		<h3 class="b-hr-bg b-personal-page__heading">
			<span class="b-hr-bg__content"><?=(CUser::GetID() == $arParams["USER"]["ID"] ? "Мои рецепты" : "Рецепты")?></span>
		</h3>
		<div class="b-recipes-list">

			<?foreach($arResult["arRecipe"]["ITEMS"] as $recipe):
			$bAllowEdit = (!($USER->IsAdmin()) && (MakeTimeStamp($recipe["DATE_CREATE"]) <= (time() - 3600*24*3)));?>
			<div class="b-recipes-list__item b-recipe-preview">
				<div class="b-recipe-preview__photo"><a href="<?=SITE_DIR?>detail/<?=($recipe["CODE"] ? $recipe["CODE"] : intval($recipe["ID"]))?>/" title="<?=$recipe["NAME"]?>" class="b-recipe-preview__photo__link"><img src="<?=$recipe["PREVIEW_PICTURE"]["SRC"]?>" width="170" alt="<?=$recipe["NAME"]?>" class="b-recipe-preview__photo__image" /></a></div>
				<div class="b-recipe-preview__heading b-h5"><a href="<?=SITE_DIR?>detail/<?=($recipe["CODE"] ? $recipe["CODE"] : intval($recipe["ID"]))?>/" class="b-recipe-preview__heading__link"><?=$recipe["NAME"]?></a></div>
				<div class="b-recipe-preview__info"><noindex>
					<a href="<?=SITE_DIR?>detail/<?=($recipe["CODE"] ? $recipe["CODE"] : intval($recipe["ID"]))?>/#comments" class="b-recipe-preview__comments b-comments-preview" title="Оставить отзыв"><span class="b-comments-preview__icon"></span><span class="b-comments-preview__num"><?=intval($recipe["PROPERTY_COMMENT_COUNT_VALUE"])?></span></a>
					<?if(!$bAllowEdit):?><a href="<?=SITE_DIR?>recipe/edit/<?=intval($recipe["ID"])?>/" class="b-edit-button" title="Редактировать рецепт"></a><?endif;?>					
				</noindex></div>
			</div>

			<?endforeach;?>			
			
			<div class="i-clearfix"></div>
		</div>
		
	</div>
	<?if(isset($arResult["arRecipe"]["NAV_STRING"])){echo $arResult["arRecipe"]["NAV_STRING"];}?>	
</div>
<?elseif(CUser::GetID() == $arParams["USER_ID"]):?>
<h2>У вас нет ни одного рецепта. Вы можете <a href="/recipe/add/">добавить</a> новый рецепт.</h2>
<?else:?>
<h2>У меня пока нет рецептов.</h2>
<?endif;?>