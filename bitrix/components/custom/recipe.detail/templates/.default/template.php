<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
//$this->setFrameMode(true);?>
<?//$this->createFrame("recipe_block", false)->begin('<img src="/images/preloader.gif" width="100%" alt="">');
$Favorite = new CFavorite;
$Factory = new CFactory;
$CMark = new CMark;
$CFClub = CFClub::getInstance();
?>
<?if(!empty($arResult)){?>
<div class="recipe hrecipe" id="<?=$arResult["ID"]?>">
	<?$frame = $this->createFrame()->begin();?>
	<div class="title">
		<div class="body">			
		<div class="chain_path">
			<div class="author"><div class="author_photo"><div class="big_photo" style="display: none;"><div><img width="100" height="100" alt="<?=$arResult["AUTHOR"]["FULLNAME"]?>" src="<?=$arResult["AUTHOR"]["avatar"]?>"></div></div><img width="30" height="30" alt="<?=$arResult["AUTHOR"]["FULLNAME"]?>" src="<?=$arResult["AUTHOR"]["small_avatar"]?>"></div><a class="nickname" href="/profile/<?=$arResult["AUTHOR"]["ID"]?>/" title="<?=$arResult["AUTHOR"]["FULLNAME"]?>"><?if(strlen($arResult["AUTHOR"]["FULLNAME"]) > 10):?><?=substr($arResult["AUTHOR"]["FULLNAME"],0,10)?>...<?else:?><?=$arResult["AUTHOR"]["FULLNAME"]?><?endif;?></a></div>
			предлагает приготовить:	<span class="tags"><a class="sub-category" href="/search/<?=$arResult["KITCHEN"]['NAME']?>/" rel="tag"><?=$arResult["KITCHEN"]['NAME']?></a>/<a href="/search/<?=$arResult["DISH_TYPE"]['NAME']?>/" class="category" rel="tag"><?=$arResult["DISH_TYPE"]['NAME']?></a><?if(intval($arResult["PROPERTIES"]["main_ingredient"]["VALUE"]) > 0):?>/<a href="/search/<?=$arResult["MAIN_INGREDIENT"]["NAME"]?>/" class="category" rel="tag"><?=$arResult["MAIN_INGREDIENT"]["NAME"]?></a><?endif;?></span>
		</div>
		<?if($USER->isAdmin() || $USER->GetID() == $arResult['CREATED_BY']):?>
			<div class="admin_panel">
			<noindex>
			<a id="html_code" href="#">HTML-код</a>
			<?if($arResult["bAllowEdit"]):?><a title="Редактировать запись" href="<?=SITE_DIR?>recipe/edit/<?=$arResult["ID"]?>/" class="edit">Редактировать запись</a>
			<a title="Удалить запись" class="delete" href="<?=SITE_DIR?>recipe/delete/<?=$arResult["ID"]?>/">Удалить запись</a><?endif;?>
			</noindex>
			</div>
		<?endif;?>
		<h1 class="fn"><?=$arResult['NAME']?></h1>
		<?if(strlen($arResult["TAGS"]) > 0){echo "<!-- TAGS:".$arResult['TAGS']." -->";}?>
		<?if(intval($arResult["kkals"]) > 0 || intval($arResult["PROPERTIES"]["portion"]["VALUE"]) > 0 || intval($arResult["PROPERTIES"]["cooking_time"]["VALUE"]) > 0):?>
		<?$cooking_time_hours = $arResult["PROPERTIES"]["cooking_time"]["VALUE"]/60;
		$cooking_time_minutes = $arResult["PROPERTIES"]["cooking_time"]["VALUE"]%60;?>
		<div class="recipe_info">
			<table>
				<tbody><tr>
					<td class="time"><?if(intval($arResult["PROPERTIES"]["cooking_time"]["VALUE"]) > 0):?><span>Время приготовления:</span> <?=(intval($cooking_time_hours) > 0 ? intval($cooking_time_hours)." ".$Factory->plural_form(intval($cooking_time_hours),array("час","часа","часов"))." " : "")?><?=(intval($cooking_time_minutes) > 0 ? intval($cooking_time_minutes)." мин" : "")?><?endif;?></td>
					<td class="yield"><?if(intval($arResult["PROPERTIES"]["portion"]["VALUE"]) > 0):?><span>Порций:</span> <?=$arResult["PROPERTIES"]["portion"]["VALUE"]?><?endif;?></td>
					<td class="nutrition"><?if(intval($arResult["kkals"]) > 0):?><span>Калорийность:</span> <?if(intval($arResult["PROPERTIES"]["portion"]["VALUE"]) > 0){echo intval($arResult["kkals"]/intval($arResult["PROPERTIES"]["portion"]["VALUE"]));}else{echo intval($arResult["kkals"]);}?> кКал на порцию<?endif;?></td>
				</tr>
			</tbody></table>
		</div>
		<?endif;?>
		<?if(IntVal($arResult['PREVIEW_PICTURE']) > 0):?>
			<div class="image"><div class="screen"><div style="width: <?=$arResult["DETAIL_PICTURE"]['WIDTH']?>px; height: <?=$arResult["DETAIL_PICTURE"]['HEIGHT']?>px;"></div></div><img class="result-photo" src="<?=$arResult["DETAIL_PICTURE"]['SRC']?>" alt="<?=(strlen($arResult["DETAIL_PICTURE"]['ALT']) > 0 ? $arResult["DETAIL_PICTURE"]['ALT'] :$arResult['NAME'])?>" title="<?=(strlen($arResult["DETAIL_PICTURE"]['TITLE']) > 0 ? $arResult["DETAIL_PICTURE"]['TITLE'] : $arResult['NAME'])?>" width="<?=$arResult["DETAIL_PICTURE"]['WIDTH']?><?//=$arMainFile['WIDTH']?>" height="<?=$arResult["DETAIL_PICTURE"]['HEIGHT']?><?//=$arMainFile['HEIGHT']?>"></div>
		<?endif;?>
		<?if(count($arResult["units"]) > 0 || count($arResult["TECHNICS"]) > 0){?>
		<div class="needed">
			<h2>Вам понадобится:</h2>
			<?if(!empty($arResult['TECHNICS'])):?>
				<div class="tools">
					<?foreach($arResult['TECHNICS'] as $technic):?>
						<span class="item">
							<?if(strlen($technic["PICTURE"]) > 0):?>
								<img width="100" height="100" alt="<?=$technic["NAME"]?>" src="<?=$technic["PICTURE"]?>">
							<?endif;?>
							<?if(strlen($technic["PROPERTY_LINK_VALUE"]) > 0):?>
								<span class="p"><a target="_blank" href="<?=$technic["PROPERTY_LINK_VALUE"]?>"><?=$technic["NAME"]?></a></span>
							<?else:?>
								<span class="p"><?=$technic["NAME"]?></span>
							<?endif;?>
						</span>
					<?endforeach;?>
				</div>
			<?endif;?>
			<div class="scales"><a title="Таблица мер" href="#"></a></div>
			<table>
				<?
				$bF = true;
				foreach($arResult["units"] as $arItem){
					$intUnitCount = 0;
					foreach($arItem as $arUnit){
						ob_start(); eval("echo ".$arUnit['VALUE'].";"); $i = ob_get_contents(); ob_end_clean();
						$intUnitCount += FloatVal($i);
					}
					if($bF == true){ echo "<tr>";};
				?>
					<td class="ing_name <?if($bF == false){echo "border";}?>">
					<?if($arItem[0]['LINK']):?>
					<span class="ingredient"><a href="/ingredient/<?=($arItem[0]['CODE'] ? $arItem[0]['CODE'] : $arItem[0]['ID'])?>/"><?=$arItem[0]['NAME']?></a></span>						
					<?else:?>
					<span class="ingredient"><?=$arItem[0]['NAME']?></span>
					<?endif;?>						
					</td>
					<td class="ing_amount"><?=str_replace(Array("0.5", "0.25", "0.75"), Array("&frac12;","&frac14;","&frac34;"), $intUnitCount)?> <?=$arItem[0]['UNIT']?></td>
				<?
					if($bF == false){ echo "</tr>"; $bF = true;} else { $bF = false; };
				}
				if($bF == false){
					echo '<td class="ing_name border"></td><td class="ing_amount"></td></tr>';
				}
				?>
			</table>
		</div>
		<?} //if?>
		</div>

		<div class="clear"></div>
	</div>

	<?=$arResult["stages"]?>
	<?$frame->end();?>

	<div class="date"><span class="published"><?=substr($arResult['DATE_ACTIVE_FROM'], 0, strlen($arResult['DATE_ACTIVE_FROM'])-9);?></span><span class="time"><?=substr($arResult['DATE_ACTIVE_FROM'], 11, 9);?></span></div>
		<div class="bar">
			<div class="padding">
				<div class="author">
					<div class="author_photo">
						<div class="big_photo">
							<div><img src="<?=$arResult["AUTHOR"]["avatar"]?>" width="100" height="100" alt="<?=$arResult["AUTHOR"]['FULLNAME']?>"></div>
						</div>
						<img src="<?=$arResult["AUTHOR"]["small_avatar"]?>" width="30" height="30" alt="<?=$arResult["AUTHOR"]['FULLNAME']?>">
					</div>
					<a class="nickname" href="/profile/<?=$arResult["AUTHOR"]['ID']?>/" title="<?=$arResult["AUTHOR"]['FULLNAME']?>"><?=$arResult["AUTHOR"]['FULLNAME']?></a>
				</div>
				<?if($USER->IsAuthorized()){?>
				<div class="comments"><a href="#add_opinion">Комментировать</a><span class="number">(<a href="#add_opinion"><?=IntVal($arResult["ccount"])?></a>)</span></div>
				<?}?>
			</div>
		</div>
	</div>
	<div class="b-recipe-menu b-recipe-menu__type-block-under">
		<? if($USER->IsAuthorized()){
		if($Favorite->status($arResult["ID"]))
		{
		?>
			<?if(intval($_REQUEST["r"])){?>
			<a href="<?=SITE_DIR?>detail/<?=$arResult["ID"]?>/?f=n" class="b-recipe-menu__item b-recipe-menu__button b-favorite-button i-remove-favorite" title="Удалить из избранного">
				<span class="b-favorite-button__text">Удалить</span>
			</a>
			<?}elseif(strval($_REQUEST["c"])){?>
			<a href="<?=SITE_DIR?>detail/<?=$arResult["CODE"]?>/?f=n" class="b-recipe-menu__item b-recipe-menu__button b-favorite-button i-remove-favorite" title="Удалить из избранного">
				<span class="b-favorite-button__text">Удалить</span>
			</a>
			<?}
		}
		else
		{
		?>
			<?if(intval($_REQUEST["r"])){?>
			<a href="<?=SITE_DIR?>detail/<?=$arResult["ID"]?>/?f=y" class="b-recipe-menu__item b-recipe-menu__button b-favorite-button">
				<span class="b-favorite-button__text">В избранное</span>
			</a>
			<?}elseif(strval($_REQUEST["c"])){?>
			<a href="<?=SITE_DIR?>detail/<?=$arResult["CODE"]?>/?f=y" class="b-recipe-menu__item b-recipe-menu__button b-favorite-button">
				<span class="b-favorite-button__text">В избранное</span>
			</a>
			<?}
		}
	}
	else
	{?>
		<?if(intval($_REQUEST["r"])){?>
		<a href="<?=SITE_DIR?>detail/<?=$arResult["ID"]?>/?f=y" class="b-recipe-menu__item b-recipe-menu__button b-favorite-button">
			<span class="b-favorite-button__text">В избранное</span>
		</a>
		<?}elseif(strval($_REQUEST["c"])){?>
		<a href="<?=SITE_DIR?>detail/<?=$arResult["CODE"]?>/?f=y" class="b-recipe-menu__item b-recipe-menu__button b-favorite-button">
			<span class="b-favorite-button__text">В избранное</span>
		</a>
		<?}
	}?>
		<a href="#" class="b-recipe-menu__item b-recipe-menu__button b-recipe-menu__button__type-print">
			<span class="b-print-button" title="Распечатать рецепт"></span>
		</a>
		<div class="i-clearfix"></div>
	</div>
	<div class="b-social-buttons">
		<div class="b-social-buttons__item b-vk-like">
			<div id="vk_like1"></div>
			<script type="text/javascript">
				VK.Widgets.Like("vk_like1", {type: "mini", height: 20, pageUrl: "<?="http://".$_SERVER["SERVER_NAME"].$APPLICATION->GetCurDir();?>"});
			</script>
		</div>
		<div class="b-social-buttons__item b-twitter-like">
			<a href="https://twitter.com/share" class="twitter-share-button">Tweet</a>
			<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>				
		</div>
		<div class="b-social-buttons__item b-fb-like"><fb:like send="false" layout="button_count" width="50" show_faces="false"></fb:like></div>
		<div class="b-social-buttons__item b-surf-like">
			<a target="_blank" class="surfinbird__like_button" data-surf-config="{'layout': 'common', 'width': '100', 'height': '20'}" href="http://surfingbird.ru/share">Серф</a>
			<script type="text/javascript" charset="UTF-8" src="http://surfingbird.ru/share/share.min.js"></script>
		</div>
		<div class="b-social-buttons__item b-pin-like">
			<a target="_blank"  href="http://pinterest.com/pin/create/button/?url=foodclub.ru<?=$APPLICATION->GetCurPage()?>&media=http://foodclub.ru/upload/<?=$arResult["MainFile"]["SUBDIR"]?>/<?=$arResult["MainFile"]["FILE_NAME"]?>&description=<?=urlencode($arResult["~PREVIEW_TEXT"])?>" class="pin-it-button" count-layout="horizontal"><img border="0" src="//assets.pinterest.com/images/PinExt.png" title="Pin It" /></a>
		</div>
		<div class="b-social-buttons__item b-ya-share">
			<script type="text/javascript" src="//yandex.st/share/share.js" charset="utf-8"></script>
			<div class="yashare-auto-init" data-yashareL10n="ru" data-yashareType="icon" data-yashareQuickServices="yaru,vkontakte,facebook,twitter,odnoklassniki"></div> 
		</div>
		<div class="i-clearfix"></div>
	</div>
<?}?>