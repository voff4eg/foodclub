<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$frame = $this->createFrame()->begin();?>
<?
require_once($_SERVER['DOCUMENT_ROOT']."/classes/mark.class.php");

if(strlen($arResult["FatalError"])>0)
{
	?>
	<noindex><a class="sign_in" href="/auth/?backurl=<?=$APPLICATION->GetCurPage()?>">Войти</a><a class="reg" href="/registration/">Зарегистрироваться</a></noindex>
	<?
}
else
{
	if(strlen($arResult["User"]["NAME"]) > 0 && strlen($arResult["User"]["LAST_NAME"]) > 0){
	 	$name = $arResult["User"]["NAME"]." ".$arResult["User"]["LAST_NAME"];
	}else{
		$name = $arResult["User"]["LOGIN"];
	}
	$file = CFile::ResizeImageGet($arResult["User"]['PERSONAL_PHOTO'], array('width'=>30, 'height'=>30), BX_RESIZE_IMAGE_PROPORTIONAL, true);
	?>
	<div class="person">
		<a class="user" href="/profile/"><?if(intval($arResult["User"]["PERSONAL_PHOTO"]) > 0):?><img width="30" height="30" alt="" src="<?=$file['src']?>"><?else:?><img width="30" height="30" alt="" src="/images/avatar/avatar.jpg"><?endif;?><span><?=(strlen($name) > 10 ? substr($name,0,10)."..." : $name )?></span></a>
	    <a href="?logout=yes" class="sign_out" title="Выйти"></a>
		<span title="Рейтинг" class="rating"><?=(strlen($arResult["User"]["UF_RAITING"]) > 0 ? $arResult["User"]["UF_RAITING"] : 0)?></span>
	</div>
	<?require_once($_SERVER["DOCUMENT_ROOT"]."/classes/favorite.class.php");
	$Favorite = new CFavorite;?>
	<a href="/profile/favorites/" class="b-top-panel__favorites" title="Избранные рецепты"><?=$Favorite->getCount($arResult["User"]["ID"]);?></a>

	<div class="add">
		<span class="submenu">
			<span class="body">
				<span class="pointer"></span>
				<a class="first" href="/recipe/add/">Рецепт с пошаговыми фото</a>
				<a class="last" href="/foodshot/add/">Фудшот</a>
			</span>
		</span>
		<a class="button" href="#"><span>Добавить</span></a>
	</div>
	<div class="menu">			
		<span class="kitchen">
			<span class="submenu">
				<span class="submenu_pointer"></span>
				<a class="first" href="/profile/recipes/">Рецепты</a>
				<a href="/profile/topics/">Записи</a>
				<a href="/profile/comments/">Комментарии</a>
				<a href="/profile/lenta/">Моя лента</a>
				<a class="subscription" href="/profile/subscribe/">Подписка</a>
				<a class="last" href="/profile/opinions/">Отзывы</a>
			</span>
			<a href="#"><span class="up"><span>Моя кухня</span></span></a>
		</span>
		<div class="clear"></div>
	</div>
	<div class="clear"></div>
<?}?>
<?$frame->end();?>