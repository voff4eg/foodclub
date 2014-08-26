<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeTemplateLangFile(__FILE__);
?>
<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/xhtml"
      xmlns:og="http://ogp.me/ns#"
      xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
<title><?$APPLICATION->ShowTitle()?></title>
<?$APPLICATION->ShowHead(true)?>
<meta name="verify-v1" content="IFNqswFktC+hhGa2ZKs6Ale87GxdIORrcVznFXPdEh4=" >
<meta name='yandex-verification' content='7e14af38f0152a84' />
<meta name='yandex-verification' content='4606e113f2b24cf7' />
<meta name="apple-itunes-app" content="app-id=445878711, affiliate-data=1l3vmcn" />
<link rel="icon" href="http://www.foodclub.ru/favicon.ico">
<?global $APPLICATION;?>
<?if(($_SERVER["SCRIPT_NAME"] == "/foodshot/index.php" || $_SERVER["REAL_FILE_PATH"] == "/foodshot/index.php") && !isset($_REQUEST["_escaped_fragment_"])){
echo '<meta name="fragment" content="!">';
echo '<script>
var value = document.location.href.replace("http://'.$_SERVER['HTTP_HOST'].'","");
if(value == "/foodshot/"){
	window.location.href = "http://'.$_SERVER['HTTP_HOST'].'/foodshot/#!foodshot";
}
</script>';
}?>
<?if(strpos($APPLICATION->GetCurDir(), "recipe/") !== false && $APPLICATION->GetCurPage() != "/recipe/public/detail_opt.php"){
$APPLICATION->SetAdditionalCSS("/css/admin/styles.css");
$APPLICATION->SetAdditionalCSS("/css/helper.css");?>
<script src="/js/jscript.js" type="text/javascript"></script>
<script src="/js/admin/jscript.js" type="text/javascript"></script>
<script src="/js/helper.js" type="text/javascript"></script>
<script src="/js/history/scripts/bundled/html4+html5/jquery.history.js"></script>
<?} else {?>
<?/*$file = file_get_contents('http://foodclub.ru/css/styles.css', true);
echo '<link rel="stylesheet" type="text/css" href="/css/styles.v'.crc32($file).'.css">';*/
?>
<?
$APPLICATION->AddHeadScript("/js/elem.js");
$APPLICATION->AddHeadScript("/js/jscript.js");
?>
<link rel="stylesheet" type="text/css" href="/css/helper.css">
<script src="/recipe_links.js" type="text/javascript"></script>
<script src="/js/helper.js" type="text/javascript"></script>
<script src="/js/history/scripts/bundled/html4+html5/jquery.history.js"></script>
<meta name="title" content="<?$APPLICATION->ShowTitle();?>" />
<?//echo $APPLICATION->AddBufferContent("setHeaderContent");?>
<?}?>
<?if($APPLICATION->GetCurDir() == "/recipes/"):?>
<script src="/js/ss_data.js?<?=filectime($_SERVER["DOCUMENT_ROOT"]."/js/ss_data.js")?>" type="text/javascript"></script>
<?endif;?>
<?$APPLICATION->AddHeadScript("https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js");?>

<!-- Vkontakte -->
<script type="text/javascript" src="http://userapi.com/js/api/openapi.js?47"></script>
<script type="text/javascript">
  VK.init({apiId: 2404991, onlyWidgets: true});
</script>
</head>
<?
CModule::IncludeModule("iblock");
require_once($_SERVER["DOCUMENT_ROOT"].'/classes/main.class.php');?>

<body data-site-id="<?=SITE_ID?>">

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-43906683-1', 'foodclub.ru');
  ga('send', 'pageview');

</script>

<script type='text/javascript'>var _merchantSettings=_merchantSettings || [];_merchantSettings.push(['AT', '1l3vmcn']);(function(){var autolink=document.createElement('script');autolink.type='text/javascript';autolink.async=true; autolink.src= ('https:' == document.location.protocol) ? 'https://autolinkmaker.itunes.apple.com/js/itunes_autolinkmaker.js' : 'http://autolinkmaker.itunes.apple.com/js/itunes_autolinkmaker.js';var s=document.getElementsByTagName('script')[0];s.parentNode.insertBefore(autolink, s);})();</script>

<?$APPLICATION->IncludeFile("/include/an.php", Array(), Array("MODE"=>"html"))?>
<div id="fb-root"></div>
<a href="https://plus.google.com/118442365793857710655" rel="publisher" style="font-size:0; display:none;">Google+</a>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/ru_RU/all.js#xfbml=1&appId=140629199390639";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
    <?$APPLICATION->ShowPanel();?>
        <div id="top_panel">
	<div class="body"><!-- <div class="i-relative"><div class="b-top-panel__decor"></div></div> -->
             <?global $USER;
             require_once($_SERVER['DOCUMENT_ROOT']."/classes/mark.class.php");
             $CFUser = new CFUser;
             $arUser = $CFUser->getById($USER->GetID());
             if ($USER->IsAuthorized()):
             	if(strlen($arUser["NAME"]) > 0 && strlen($arUser["LAST_NAME"]) > 0){
	             	$name = $arUser["NAME"]." ".$arUser["LAST_NAME"];
             	}else{
             		$name = $arUser["LOGIN"];
             	}			
             	?>
		<div class="person">
			<a class="user" href="/profile/"><?if(intval($arUser["PERSONAL_PHOTO"]) > 0):?><img width="30" height="30" alt="" src="<?=$arUser['photo']['SRC']?>"><?else:?><img width="30" height="30" alt="" src="/images/avatar/avatar.jpg"><?endif;?><span><?=(strlen($name) > 10 ? substr($name,0,10)."..." : $name )?></span></a>
            <a href="?logout=yes" class="sign_out" title="Выйти"></a>
			<span title="Рейтинг" class="rating"><?=(strlen($arUser["UF_RAITING"]) > 0 ? $arUser["UF_RAITING"] : 0)?></span>
            <!--span title="Статус" class="status"><!--(--><?/*(strlen($arUser["UF_STATUS"]) > 0 ? $arUser["status_name"] : "Новичок")*/?><!--)--></span-->
		</div>
		<?require_once($_SERVER["DOCUMENT_ROOT"]."/classes/favorite.class.php");
		$Favorite = new CFavorite;?>
		<a href="/profile/favorites/" class="b-top-panel__favorites" title="Избранные рецепты"><?=$Favorite->getCount($arUser["ID"]);?></a>
		
		<div class="add">
			<span class="submenu">
				<span class="body">
					<span class="pointer"></span>
					<a class="first" href="/recipe/add/">Рецепт с пошаговыми фото</a>
					<!-- <a class="last" href="/blogs/group/14/blog/edit/new/">Простой рецепт</a> -->
					<a class="last" href="/foodshot/add/">Фудшот</a>					
					<!--<a class="last" href="/blogs/group/">Запись в клуб</a>-->
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
					
					<!--<a class="last" href="/profile/opinions/">Отзывы к моим рецептам</a>-->
				</span>
				<a href="#"><span class="up"><span>Моя кухня</span></span></a>
			</span>
			<!--a class="subscription" href="/profile/subscribe/">Подписка</a-->
			<div class="clear"></div>
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
	<noindex><?=str_replace("padding:0; margin:0;", "padding:auto; margin:auto;", $strBanner)?></noindex>
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
        <?$APPLICATION->IncludeComponent("custom:header.search.v2",
		".default",
		Array(),
		false
		);?>
		<div id="iphone_link">
			<div class="pic"></div>
			<a href="/iphone/"><b>Foodclub НD</b><br />Кулинарная книга для вашего iPhone и iPad</a>
			<div class="clear"></div>
		</div>

			<div class="clear"></div>
		</div>
	<?include 'menu.inc.php';?>
	
	
	<?
	$cur_page = $APPLICATION->GetCurPage(true);
	if($cur_page == "/index.php" || $cur_page == "/fr/index.php"){
		if(include_once $_SERVER["DOCUMENT_ROOT"].'/classes/main.class.php'){
			$arRecipes = CFClub::getInstance()->getLastFiveLibRecipes();
			//echo "<pre>";print_R($arRecipes);echo "</pre>";
			if(!empty($arRecipes)):
		    ?>
		    <div id="recipe_line_block">
				<h2><a href="<?=SITE_DIR?>recipes/">Рецепты с пошаговыми фотографиями</a></h2>
				<?foreach($arRecipes['ITEMS'] as $k=>$E){?>		
					<div class="item recipe_list_item">
						<div class="photo">
							<a href="<?=SITE_DIR?>detail/<?=($E['CODE'] ? $E['CODE'] : $E['ID'])?>/" title="<?=$E["NAME"]?>"><!--  113х170  -->
								<img src="<?=$E['PREVIEW_PICTURE']['SRC']?>" width="170" alt="<?=$E["NAME"]?>" />
							</a>
						</div>
						<h5>
							<a href="<?=SITE_DIR?>detail/<?=($E['CODE'] ? $E['CODE'] : $E['ID'])?>/"><?=$E["NAME"]?></a>
						</h5>
						<?if(strlen($E['USER']["NAME"]) > 0 && strlen($E['USER']["LAST_NAME"]) > 0){
			             	$name = $E['USER']["NAME"]." ".$E['USER']["LAST_NAME"];
		             	}else{
		             		$name = $E['USER']["LOGIN"];
		             	}?>
						<p class="author">От: <?=$name?></p>
						<p class="info"><span title="Оставить отзыв" class="comments_icon"><a href="/detail/<?=($E['CODE'] ? $E['CODE'] : $E['ID'])?>/#comments"><?if(intval($E['PROPERTY_COMMENT_COUNT_VALUE']) > 0):?><?=intval($E['PROPERTY_COMMENT_COUNT_VALUE'])?><?endif;?></a></span></p>
					</div>
				<?}?>
				<div class="clear"></div>
		    </div>
		    <?endif;?>
	    <?}
	}?>
	<div id="top_search_list"><ul class="top_search_list"></ul></div><!--for IE-->
