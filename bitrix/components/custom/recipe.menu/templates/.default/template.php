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
$Favorite = new CFavorite;
?>
<?$APPLICATION->AddHeadScript("/bitrix/components/custom/recipe.menu/templates/.default/script.js");?>
<style>
@media print {
body, #logo strong, div.bar div.date, div.bar div.date span {color:#000000;}
#top_panel, #top_banner, #recipe_search, #iphone_link, #topbar, #text_space ul.recipe_menu, #text_space div.scales, #opinion_block, div.bar div.comments, div.bar div.favourite, div.bar div.share, div.other_recipes, #bottom, #bottom_nav, #banner_space, div.recipe div.image div.screen {display:none;}
a {
text-decoration:none;
color:#000000;}
div.bar {padding-bottom:70px;}
#body, #content {width:700px;}
#body div.padding {padding:0;}
}
</style>
<?if(isset($_REQUEST["cant_delete"])):
	if(!$arResult["bAllowEdit"]){
		echo "<div class='b-error-message'>
			<div class='b-error-message__pointer'>
				<div class='b-error-message__pointer__div'></div>
			</div>
			Вам нельзя удалять этот рецепт, т.к. он был добавлен более, чем 3 дня назад.
		</div>
		<div class='i-clearfix'></div>";
	}
endif;
if(isset($_REQUEST["cant_edit"])):
	if(!$arResult["bAllowEdit"]){
		echo "<div class='b-error-message'>
			<div class='b-error-message__pointer'>
				<div class='b-error-message__pointer__div'></div>
			</div>
			Вам нельзя редактировать этот рецепт, т.к. он был добавлен более, чем 3 дня назад.
		</div>
		<div class='i-clearfix'></div>";
	}
endif;?>
<div id="text_space">
<?$frame = $this->createFrame()->begin();?>
	<div class="b-recipe-menu">
	<? if($USER->IsAuthorized()){
		if($Favorite->status($arResult["ID"]))
		{
			if(intval($_REQUEST["r"])){?>
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
			if(intval($_REQUEST["r"])){?>
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
	{
		if(intval($_REQUEST["r"])){?>
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
	<div id="fb-root"></div>
<script>(function(d, s, id) {
var js, fjs = d.getElementsByTagName(s)[0];
if (d.getElementById(id)) return;
js = d.createElement(s); js.id = id;
js.src = "//connect.facebook.net/ru_RU/all.js#xfbml=1";
fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
	<div class="b-recipe-menu__item b-recipe-menu__like b-fb-button"><div class="fb-share-button" data-href="http://<?=$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']?>" data-type="button_count"></div></div>
	<div class="b-recipe-menu__item b-recipe-menu__like b-vk-button">
		<div id="vk_like"></div>
		<script type="text/javascript">
		VK.Widgets.Like("vk_like", {type: "mini", height: 20, pageUrl: "<?="http://".$_SERVER["SERVER_NAME"].$APPLICATION->GetCurDir();?>"});
		</script>
	</div>
	<div class="clear"></div>
</div>
<?$frame->end();?>