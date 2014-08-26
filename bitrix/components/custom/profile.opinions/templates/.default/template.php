<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if(!empty($arResult["Opinions"])):?>
<div id="text_space">
	<h3 class="b-hr-bg b-personal-page__heading">
		<span class="b-hr-bg__content"><?=(CUser::GetID() == $arParams["USER"]["ID"] ? "Мои отзывы" : "Отзывы")?></span>
	</h3>
	<ul class="comments_list">
		<?foreach($arResult["Opinions"] as $opinion):?>
		<li><p><?=nl2br($opinion['PREVIEW_TEXT'])?></p>
		<p class="sign">
			<?$arDate = explode(" ", $opinion['DATE_CREATE']);?>
			<?=CFactory::humanDate($arDate[0])?> к рецепту 
			<a href="/detail/<?=($arResult["Recipes"][ $opinion['PROPERTY_RECIPE_VALUE'] ]["CODE"] ? $arResult["Recipes"][ $opinion['PROPERTY_RECIPE_VALUE'] ]["CODE"] : $arResult["Recipes"][ $opinion['PROPERTY_RECIPE_VALUE'] ]["ID"])?>/"><?=$arResult["Recipes"][ $opinion['PROPERTY_RECIPE_VALUE'] ]['NAME']?></a>
		</p></li>
		<?endforeach;?>
	</ul>
	<?if(isset($arResult["NavString"])){echo $arResult["NavString"];}?>
</div>
<?else:?>
<h2>У меня пока нет отзывов.</h2>
<?endif;?>