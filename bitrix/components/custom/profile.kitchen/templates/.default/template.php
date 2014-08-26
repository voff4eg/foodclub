<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
global $USER;
?>





<!--script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script-->

<link rel="stylesheet" type="text/css" href="/bitrix/components/custom/profile.kitchen/templates/.default/style.css">
<!--script src="/bitrix/components/custom/profile.kitchen/templates/.default/script.js" type="text/javascript"></script-->

<!--link rel="stylesheet" type="text/css" href="/bitrix/components/custom/profile.kitchen.form/templates/.default/style.css">
<script src="/bitrix/components/custom/profile.kitchen.form/templates/.default/script.js" type="text/javascript"></script-->

<!--script src="/js/elem.js" type="text/javascript"></script-->
<br>
<br>
<br>
<br>
<?//print_r($arResult['ITEMS']);?>
<div class="b-personal-page__kitchen">
						<h3 class="b-hr-bg b-personal-page__heading">
							<span class="b-hr-bg__content">Кухня</span>
						</h3>
						<div class="b-personal-page__text"><?=$arResult['UserKitchen'];?></div>
						
						<div class="b-kitchen__list" data-url="http://<?echo $_SERVER['HTTP_HOST']."/profile/".$_REQUEST['u']."/kitchen/"; ?>" data-delete-ajax-url="/php/delete-kitchen-equipment.php" >

<script type="text/html" id="kitchen-equipment-template">
<div class="b-kitchen__item" data-balloon='{"title": "<%=title.name%>", "brand": "<%=brand.name%>", "text": "<%=text%>", "rating": "<%=rating%>", "price": "<%=price%>"}' data-id="<%=id%>">
	<div class="b-kitchen__item__admin-butoons b-admin-buttons">
		<div class="b-admin-buttons__block">
			<div class="b-delete-icon" title="Удалить технику"></div>
			<div class="b-edit-icon" title="Редактировать"></div>
		</div>
	</div>
	<a href="#" class="b-kitchen__item__link">
		<span class="b-kitchen__item__image">
			<img src="<%=image.src%>" width="<%=image.width%>" height="<%=image.height%>" alt="<%=image.alt%>">
		</span>
		<span class="b-kitchen__item__title" data-id="<%=title.id%>"><%=title.name%></span>
		<span class="b-kitchen__item__brand" data-id="<%=brand.id%>"><%=brand.name%></span>
		<span class="b-kitchen__item__model" data-id="<%=model.id%>"><%=model.name%></span>
	</a>
</div>
</script>


							
						<div class="b-kitchen__block i-tech">
								<h5 class="b-kitchen__block__heading">Использую технику:</h5>
								
								<pre>
								<? //print_r($arResult['ITEMS']);?>
								</pre>
								<?foreach($arResult['ITEMS'] as $itemId=>$arItem):?>
								<div class="b-kitchen__item" data-balloon='{"title": "<?=$arItem["TECH"]?>", "brand": "<?=$arItem["BRAND"]?>", "text": "<?=str_replace("\n", " ", str_replace("<br />", " ",$arItem["COMMENTS"]))?>", "rating": "<?=$arItem["RATING_RESULT"]?>", "price": "<?=$arItem["COST"]?>", "user":"<?=$USER->getId();?>"}' data-id='<?=$arItem["ID"]?>' data-ajax='{"oldId": "<?=$arItem["ID"]?>", "olduser":"<?=$USER->getId();?>", "param3": "data3"}'>
									<?if(intval($_REQUEST['u'])==$USER->GetId()):?>
									<div class="b-kitchen__item__admin-butoons b-admin-buttons">
										<div class="b-admin-buttons__block">
											<div class="b-delete-icon" title="Удалить технику"></div>
											<div class="b-edit-icon" title="Редактировать"></div>
										</div>
									</div>
									<?endif?>


									<span  class="b-kitchen__item__link">
										<span class="b-kitchen__item__image">
											<?if($arItem["PREVIEW_PICTURE"]['SRC']): ?><img src="<?=$arItem["PREVIEW_PICTURE"]['SRC']?>" width="155" height="160"  alt="<?=$arItem["TECH"]?> <?=$arItem["BRAND"]?>">
											<?else:?><img src="/images/icons/kitchen.png" width="155"  alt="<?=$arItem["TECH"]?> <?=$arItem["BRAND"]?>">
											<?endif?>
										</span>
										<span class="b-kitchen__item__title" data-id="<?=$arItem["TECH_ID"]?>"><?=$arItem["TECH"]?></span>
										<span class="b-kitchen__item__brand" data-id="<?=$arItem["BRAND_ID"]?>"><?=$arItem["BRAND"]?></span>
										<span class="b-kitchen__item__model" data-id="<?=$arItem["MODEL_ID"]?>"><?=$arItem["MODEL"]?></span>
									</span>
								</div>
								<?endforeach;?>
								
								<?if($arResult['bOwnerPage']): ?>
								<div class="b-kitchen__item b-kitchen__item__type_empty">
									<a href="#" class="b-button__type_2" data-popup-id="kitchen-equipment-add-form">Добавить технику</a>
								</div>
								<?endif;?>
								<div class="i-clearfix"></div>
							</div>
							</div>
							</div>
			
	



















