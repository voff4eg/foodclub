<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if(!empty($arResult['arRecipe']["ITEMS"])):?>
<div id="text_space">
	<div class="b-personal-page__recipes">
		<h3 class="b-hr-bg b-personal-page__heading">
			<span class="b-hr-bg__content"><?=(CUser::GetID() == $arParams["USER"]["ID"] ? "Моё избранное" : "Избранное")?></span>
		</h3>
		<div class="b-recipes-list">		
			<?foreach($arResult['arRecipe']["ITEMS"] as $Recipe):?>				
				<div class="b-recipes-list__item b-recipe-preview">
					<?if(CUser::GetID() == $arParams["USER"]["ID"]):?>
					<div class="i-relative">
						<a href="<?=SITE_DIR?>detail/<?=($Recipe['CODE'] ? $Recipe['CODE'] : $Recipe['ID'])?>/?f=n" class="b-favorite-button i-remove-favorite" title="Удалить из избранного">
							<span class="b-favorite-button__text">Удалить</span>
						</a>
					</div>
					<?endif;?>
					<div class="b-recipe-preview__photo"><a href="/detail/<?=($Recipe['CODE'] ? $Recipe['CODE'] : $Recipe['ID'])?>/" title="<?=$Recipe['NAME']?>" class="b-recipe-preview__photo__link"><img src="<?=$Recipe['PREVIEW_PICTURE']['SRC']?>" width="170" alt="<?=$Recipe['NAME']?>" class="b-recipe-preview__photo__image" /></a></div>
					<div class="b-recipe-preview__heading b-h5"><a href="/detail/<?=($Recipe['CODE'] ? $Recipe['CODE'] : $Recipe['ID'])?>/" class="b-recipe-preview__heading__link"><?=$Recipe['NAME']?></a></div>
					<div class="b-recipe-preview__author">От: <?=$arResult["arRecipe"]['ITEMS'][ $Recipe['ID'] ]["USER"]["FULLNAME"]?></div>
					<div class="b-recipe-preview__info"><noindex>
						<a href="/detail/<?=($Recipe['CODE'] ? $Recipe['CODE'] : $Recipe['ID'])?>/#comments" class="b-recipe-preview__comments b-comments-preview" title="Оставить отзыв"><span class="b-comments-preview__icon"></span><span class="b-comments-preview__num"><?=intval($Recipe['PROPERTY_COMMENT_COUNT_VALUE'])?></span></a>
						<a href="<?=$APPLICATION->GetCurDir()?>?f=n&r=<?=$Recipe['ID']?>" class="b-favorite-button i-remove-favorite" title="Убрать из избранного"></a>
					</noindex></div>
				</div>
			<?endforeach;?>			
			<div class="i-clearfix"></div>
		</div>
		
	</div>	
	<?if(isset($arRecipe["NAV_STRING"])){echo $arRecipe["NAV_STRING"];}?>	
</div>
<?else:?>
<h2>Вы пока не добавили в избранное ни одного рецепта. Вы можете <a href="/all/">добавить</a> любой рецепт из каталога.</h2>
<?endif;?>