<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->createFrame()->begin('<img src="/images/preloader.gif" width="100%" alt="">');?>
<h5 title='Последние комментарии'>Последние комментарии</h5>
<ul>
<?foreach($arResult["ITEMS"] as $arComment){?>	
	<li>
	<?if(strlen($arComment["TEXT_FORMATED"]) > 0):?>
		<?if(strlen($arComment['arUser']["NAME"]) > 0 && strlen($arComment['arUser']["LAST_NAME"]) > 0){
	     	$name = $arComment['arUser']["NAME"]." ".$arComment['arUser']["LAST_NAME"];
	 	}else{
	 		$name = $arComment['arUser']["LOGIN"];
	 	}?>
		<a class="comment" href="/blogs/group/<?=$arComment['BLOG_SOCNET_GROUP_ID']?>/blog/<?=$arComment['POST_ID']?>/<?=$arComment['urlToComment']?>"><?=$arComment['TEXT_FORMATED']?></a>
		<div class="info">
			<a class="author" href="/profile/<?=$arComment['arUser']['ID']?>/"><?=$name?></a> <span class="date"><?=$arComment['DATE_CREATE_FORMATED']?></span>
		</div>
	<?elseif(strlen($arComment["~PREVIEW_TEXT"]) > 0):?>
		<?if(strlen($arComment['USER']["NAME"]) > 0 && strlen($arComment['USER']["LAST_NAME"]) > 0){
	     	$name = $arComment['USER']["NAME"]." ".$arComment['USER']["LAST_NAME"];
	 	}else{
	 		$name = $arComment['USER']["LOGIN"];
	 	}?>
		<?if($arResult["RECIPES"][ $arComment['PROPERTY_RECIPE_VALUE'] ]):?><a class="comment" href="/detail/<?=$arResult["RECIPES"][ $arComment['PROPERTY_RECIPE_VALUE'] ]?>/?ID=<?=$arComment['ID']?>#<?=$arComment['ID']?>"><?else:?><a class="comment" href="/detail/<?=$arComment['PROPERTY_RECIPE_VALUE']?>/?ID=<?=$arComment['ID']?>#<?=$arComment['ID']?>"><?endif;?><?if(strlen($arComment['~PREVIEW_TEXT']) > 100){echo substr($arComment['~PREVIEW_TEXT'], 0 ,100)."...";}else{echo $arComment['~PREVIEW_TEXT'];}?></a>
		<div class="info">
			<a class="author" href="/profile/<?=$arComment["USER"]["ID"]?>/"><?=$name?></a> <span class="date"><?=$arComment['DATE_CREATE']?></span>
		</div>
	<?endif;?>
	</li>
<?}?>
</ul>