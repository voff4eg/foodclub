<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if(!empty($arResult["badges"])):?>
<div class="b-personal-page__all-badges">
	<h3 class="b-hr-bg b-personal-page__heading">
		<span class="b-hr-bg__content">Бейджи</span>
	</h3>
	<div class="b-all-badges__list" data-url="http://<?=$_SERVER["SERVER_NAME"]?><?=$arParams["URL"]?>">
		<?foreach($arResult["badges"] as $badge):?>
		<div class="b-all-badges__item" data-balloon='{"title": "<?=$badge["NAME"]?>", "text": "<?=$badge["PREVIEW_TEXT"]?>", "id": "<?=$badge["ID"]?>"}'>
			<span class="b-all-badges__item__link">
				<?if(intval($badge["DETAIL_PICTURE"])):
				$src = "";
				$src = CFile::GetPath(intval($badge["DETAIL_PICTURE"]));?>
				<span class="b-all-badges__item__image">
					<img src="<?=$src?>" width="130" height="130" alt="<?=$badge["NAME"]?>">
				</span>
				<?endif;?>
				<span class="b-all-badges__item__title"><?=$badge["NAME"]?></span>
			</span>
		</div>
		<?endforeach;?>		
		<div class="i-clearfix"></div>
	</div>
</div>
<?endif;?>