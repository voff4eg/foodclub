<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if(!empty($arResult["badges"])):?>
<div class="b-personal-page__badges">
	<h5 class="b-personal-page__badges__heading b-banner-h5"><span class="b-banner-h5__content">Бейджи</span></h5>
	<div class="b-personal-page__badges__list b-badges-preview__list">
		<?foreach($arResult["badges"] as $badge):?>
		<?if(intval($badge["PREVIEW_PICTURE"])):
		$src = "";
		$src = CFile::GetPath(intval($badge["PREVIEW_PICTURE"]));?>
		<div class="b-badges-preview__list__item"><img src="<?=$src?>" width="64" height="64" alt="<?=$badge["NAME"]?>" title="<?=$badge["NAME"]?>"></div>
		<?endif;?>
		<?endforeach;?>		
		<div class="i-clearfix"></div>
	</div>
	<?if(intval($_REQUEST["u"])):?>
	<div class="b-personal-page__badges__all"><a href="/profile/<?=intval($_REQUEST["u"])?>/badges/">Все бейджи</a></div>
	<?else:?>
	<div class="b-personal-page__badges__all"><a href="/profile/badges/">Все бейджи</a></div>
	<?endif;?>
</div>
<?endif;?>