<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<?if(strlen($arResult["banner"])):?>
<div class="b-collection-block b-store-block b-store-block__type_aside">

	<div class="b-collection-block__heading">
		<span class="b-collection-block__heading__content"><a href="/lavka/">Лавка</a></span>
	</div>
	
	<noindex>
	<div class="b-store-block__content">
	<?=$arResult["banner"]?>
	</div>
	</noindex>
</div>
<?endif;?>