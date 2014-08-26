<?php
$_SERVER["DOCUMENT_ROOT"] = "/home/webserver/www";
//$_SERVER["DOCUMENT_ROOT"] = "/var/www/vhosts/foodclub/site/foodclub/public_html";
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule("iblock");

global $USER,$APPLICATION;

require_once($_SERVER['DOCUMENT_ROOT']."/classes/foodshot.class.php");
$CFooshot = CFoodshot::getInstance();
$arFoodshotList = $CFooshot->getList(array(),0);
if(!empty($arFoodshotList["elems"])){
	foreach($arFoodshotList["elems"] as $i => $arFoodshot){

		$header = '<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/xhtml"
      xmlns:og="http://ogp.me/ns#"
      xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
<meta name="verify-v1" content="IFNqswFktC+hhGa2ZKs6Ale87GxdIORrcVznFXPdEh4=" >
<meta name="yandex-verification" content="7e14af38f0152a84" />
<meta name="yandex-verification" content="4606e113f2b24cf7" />
<meta name="apple-itunes-app" content="app-id=445878711, affiliate-data=1l3vmcn" />
<link rel="icon" href="http://www.foodclub.ru/favicon.ico">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
#KEYWORDS#
#DESCRIPTION#
<link href="/bitrix/js/main/core/css/core.css?14031809518964" type="text/css"  rel="stylesheet" />
<link href="/bitrix/templates/fclub/template_styles.css?1387805814126965" type="text/css"  data-template-style="true"  rel="stylesheet" />
<link href="/css/form.css?139109240429235" type="text/css"  rel="stylesheet" />
<link href="/foodshot/foodshot.css?13938274806059" type="text/css"  rel="stylesheet" />
<script type="text/javascript">if(!window.BX)window.BX={message:function(mess){if(typeof mess=="object") for(var i in mess) BX.message[i]=mess[i]; return true;}};</script>
<script type="text/javascript">(window.BX||top.BX).message({"JS_CORE_LOADING":"Загрузка...","JS_CORE_NO_DATA":"- Нет данных -","JS_CORE_WINDOW_CLOSE":"Закрыть","JS_CORE_WINDOW_EXPAND":"Развернуть","JS_CORE_WINDOW_NARROW":"Свернуть в окно","JS_CORE_WINDOW_SAVE":"Сохранить","JS_CORE_WINDOW_CANCEL":"Отменить","JS_CORE_H":"ч","JS_CORE_M":"м","JS_CORE_S":"с","JSADM_AI_HIDE_EXTRA":"Скрыть лишние","JSADM_AI_ALL_NOTIF":"Показать все","JSADM_AUTH_REQ":"Требуется авторизация!","JS_CORE_WINDOW_AUTH":"Войти","JS_CORE_IMAGE_FULL":"Полный размер"});</script>
<script type="text/javascript">(window.BX||top.BX).message({"LANGUAGE_ID":"ru","FORMAT_DATE":"DD.MM.YYYY","FORMAT_DATETIME":"DD.MM.YYYY HH:MI:SS","COOKIE_PREFIX":"BITRIX_SM","SERVER_TZ_OFFSET":"14400","SITE_ID":"s1","USER_ID":"","SERVER_TIME":"1406633119","USER_TZ_OFFSET":"0","USER_TZ_AUTO":"Y","bitrix_sessid":"58ed15c15617a5c529ffa49143e1ee6e"});</script>


<script type="text/javascript" src="/bitrix/js/main/core/core.js?140318099779888"></script>
<script type="text/javascript" src="/bitrix/js/main/core/core_ajax.js?140318099430654"></script>
<script type="text/javascript" src="/bitrix/js/main/session.js?14031809942880"></script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>

<script type="text/javascript">
bxSession.Expand(907072, "58ed15c15617a5c529ffa49143e1ee6e", false, "db140413ea22570185a24c0f365b1f26");
</script>
<!-- The XDomainRequest Transport is included for cross-domain file deletion for IE8+ -->
<!--[if gte IE 8]><script src="js/cors/jquery.xdr-transport.js"></script><![endif]-->
<link rel="stylesheet" type="text/css" href="/bitrix/components/custom/header.search.v2/templates/.default/additional.css">
<link href="/bitrix/components/custom/store.banner.horizontal/templates/.default/store.css"  type="text/css" rel="stylesheet" />
<script type="text/javascript" src="/js/file-upload/js/vendor/jquery.ui.widget.js?136575489115201"></script>
<script type="text/javascript" src="/js/file-upload/js/jquery.iframe-transport.js?13657548919087"></script>
<script type="text/javascript" src="/js/file-upload/js/jquery.fileupload.js?136575489149950"></script>
<script type="text/javascript" src="/js/file-upload/js/jquery.fileupload-fp.js?13657548918519"></script>
<script type="text/javascript" src="/js/file-upload/js/jquery.fileupload-ui.js?136575489131709"></script>
<script type="text/javascript" src="/bitrix/components/custom/profile_avatar/templates/.default/jquery.fileupload-ui.js?137465480632241"></script>
<script type="text/javascript" src="/bitrix/components/custom/header.search.v2/templates/.default/script.js?137119404015810"></script>
<script type="text/javascript" src="/bitrix/components/custom/header.search.v2/templates/.default/add-script.js?13657548869278"></script>
<script type="text/javascript" src="/js/elem.js?14004789925827"></script>
#TITLE#
<link rel="stylesheet" type="text/css" href="/css/helper.css">
<script src="/js/jscript.js" type="text/javascript"></script>
<script src="/recipe_links.js" type="text/javascript"></script>
<script src="/js/helper.js" type="text/javascript"></script>
<script src="/js/ss_data.js?1406577603" type="text/javascript"></script>
<script src="/js/history/scripts/bundled/html4+html5/jquery.history.js"></script>
#META_TITLE#

#OG#

<!-- Vkontakte -->
<script type="text/javascript" src="http://userapi.com/js/api/openapi.js?47"></script>
<script type="text/javascript">
  VK.init({apiId: 2404991, onlyWidgets: true});
</script>
</head>

<body data-site-id="s1">

<script>
  (function(i,s,o,g,r,a,m){i["GoogleAnalyticsObject"]=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,"script","//www.google-analytics.com/analytics.js","ga");

  ga("create", "UA-43906683-1", "foodclub.ru");
  ga("send", "pageview");

</script>

<script type="text/javascript">var _merchantSettings=_merchantSettings || [];_merchantSettings.push(["AT", "1l3vmcn"]);(function(){var autolink=document.createElement("script");autolink.type="text/javascript";autolink.async=true; autolink.src= ("https:" == document.location.protocol) ? "https://autolinkmaker.itunes.apple.com/js/itunes_autolinkmaker.js" : "http://autolinkmaker.itunes.apple.com/js/itunes_autolinkmaker.js";var s=document.getElementsByTagName("script")[0];s.parentNode.insertBefore(autolink, s);})();</script>

<style>
#android-banner {
	display: none;
}
.i-android-banner #android-banner {
	display: block;
	text-align: center;
}
.i-android #top_panel {
	position: static;
}
.i-android #top_spacer {
	height: 0;
}
.b-ab__close {
	width: 8%;
	position: absolute;
	top: 15px;
	right: 15px;
}
html.i-android {
	overflow-y: none;
}
</style>

<script>
$(function() {
	if(navigator.userAgent.toLowerCase().search("android") == -1) return;
	$("html").addClass("i-android");
	
	$(".b-ab__close").click(function(e) {
		e.stopPropagation();
		$("#android-banner").slideUp(500, function() {
			$("body").removeClass("i-android");
		});
		$.cookie("android_banner", "false", { expires: null, path: "/" });
		return false;
	});	
	$(".b-ab").bind("click", function() {
		ga("send", "event", "Android App banner", "Переход на Google Play");
	});
	$(".b-ab__close").bind("click", function() {
		ga("send", "event", "Android App banner", "Продолжить просмотр сайта");
	});
	
	if($.cookie("android_banner") && $.cookie("android_banner") == "false") return;
	
	$("html").addClass("i-android-banner");
});
</script>

<div id="android-banner">
	<div class="i-relative"><a href="#" class="b-ab__close">
		<img src="/images/android/close.png" width="100%" alt="" />
	</a></div>
	<a href="https://play.google.com/store/apps/details?id=com.app.foodclub" target="_blank" class="b-ab"><img src="/images/android/android_small_banner.jpg" width="100%" alt=""></a>
</div>
<div id="fb-root"></div>
<a href="https://plus.google.com/118442365793857710655" rel="publisher" style="font-size:0; display:none;">Google+</a>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/ru_RU/all.js#xfbml=1&appId=140629199390639";
  fjs.parentNode.insertBefore(js, fjs);
}(document, "script", "facebook-jssdk"));</script>
    <div id="top_panel">
	<div class="body">           
    <noindex><a class="sign_in" href="/auth/?backurl=/foodshot/index.php">Войти</a><a class="reg" href="/registration/">Зарегистрироваться</a></noindex>
  	</div>
</div>
<div id="top_spacer"></div>
<div id="top_banner" style="background-image:url(/images/infoblock/top_banner_bg.gif);">
	<noindex><a href="/bitrix/rk.php?id=6&amp;event1=banner&amp;event2=click&amp;event3=3+%2F+%5B6%5D+%5Btop_banner%5D+%D0%A1%D0%BE%D0%BD%D0%BD%D0%B0%D1%8F+%D0%B0%D0%BB%D0%BB%D0%B5%D1%8F+%D0%BF%D0%B5%D1%80%D0%B5%D1%82%D1%8F%D0%B6%D0%BA%D0%B0&amp;goto=http%3A%2F%2Fsleepymall.ru%2F&amp;af=eaf04566c44e40b3baadfe03b78af991" target="_blank" ><img alt="Сонная аллея продажа матрасов"  title="Сонная аллея продажа матрасов" src="/upload/rk/3a5/sleepymall.jpg" width="900" height="90" border="0" /></a><script type="text/javascript" src="http://guru.by/js/gz.js"> </script></noindex>
</div><div id="bg1"><div id="bg2">
<div id="body">
	<div class="padding">
		<div id="head">
			<div id="logo">
			<a href="/" title="Кулинарные рецепты с фотографиями"><img src="/images/foodclub_logo.gif" width="143" height="102" alt="Кулинарные рецепты с фотографиями"></a>
            <strong>Рецепты<br>с пошаговыми<br>фотографиями</strong>
			</div>
        <div id="recipeSearch" class="b-recipe-search">
	<form action="/search/" method="post">
		<div class="b-form-field b-recipe-search__form-field">
			<a href="#" class="b-recipe-search__delete" title="Очистить поле"></a>
			<input type="text" class="b-input-text b-recipe-search__input" value="" autocomplete="off" data-placeholder="Я ищу">
		</div>
		<button class="b-recipe-search__button"></button>
		<div class="i-clearfix"></div>
	</form>
	<p class="b-recipe-search__helper"><noindex><a href="#" id="search_helper_link" class="b-recipe-search__helper__link">Помощник</a></noindex></p>
</div>

<script type="text/html" id="recipe_search_items">
	<li class="b-rs__item">
		<a href="<%=url%>" class="b-rs__item__link">
			<% var imageRegExp = new RegExp("\.(gif)|(jpeg)|(jpg)|(png)$", "gi"); %>
			<% if(imageRegExp.test(image)) { %>
			<span class="b-rs__item__image-wrapper" data-image="<%=image%>"></span>
			<% } else { %>
			<span class="b-rs__item__image-wrapper b-rs__item__image-wrapper__type_empty"></span>
			<% } %>
			<span class="b-rs__item__text b-rs-str"><%=title%></span>
			<span class="b-rs__item__info">
				<span class="b-rs__item__time"><% if(time.hours && time.hours > 0) {%>
				<% function recipeWord(num) {
					if (/(10|11|12|13|14|15|16|17|18|19)$/.test(num)) {return "часов";}
					else if (/.*1$/.test(num)) {return "час";}
					else if (/[2-4]$/.test(num)) {return "часа";}
					else {return "часов";}
				} %>
				<%=time.hours%> <%=recipeWord(time.hours)%><% } %><% if(time.minutes && time.minutes > 0) {%> <%=time.minutes%> мин<% } %></span>
				<span class="b-rs__item__nutrition"><%=nutrition%></span>
			</span>
			<span class="clear"></span>
		</a>
		<span class="b-rs__item__hr"></span>
	</li>
</script>
<div id="iphone_link">
	<div class="pic"></div>
	<a href="/iphone/"><b>Foodclub НD</b><br />Кулинарная книга для вашего iPhone и iPad</a>
	<div class="clear"></div>
	</div>
	<div class="clear"></div>
</div>
<div id="topbar" class="nosubmenu"><div class="menu">
<div class="item"><a href="/recipe-of-month/"><span>Рецепт месяца</span></a></div>
<div class="item"><a href="/recipes/"><span>Рецепты</span></a></div>
<div class="item"><a href="/blogs/"><span>Клубы</span></a></div>
<div class="item"><a href="/specialists/"><span>Кулинары</span></a></div>
<div class="item"><span><span>Фудшот</span></span></div>
<div class="item"><a href="/lavka/"><span>Лавка</span></a></div>
<div class="item new"><a href="/best/"><span>Самое интересное</span></a></div>
<div class="clear"></div>
</div>
</div>
<div id="top_search_list"><ul class="top_search_list"></ul></div><!--for IE-->
<script type="text/javascript" src="/js/form.js"></script>
<script type="text/javascript" src="/foodshot/script.js"></script>
<script type="text/javascript" src="/foodshot/jquery.fileupload-ui.js"></script>
<script type="text/javascript">
(function(d){
  var f = d.getElementsByTagName("SCRIPT")[0], p = d.createElement("SCRIPT");
  p.type = "text/javascript";
  p.async = true;
  p.src = "//assets.pinterest.com/js/pinit.js";
  f.parentNode.insertBefore(p, f);
}(document));
</script>';

$og = '<meta property="og:title" content="Миндальный пирог с абрикосами"/>
<meta property="og:type" content="food"/>
<meta property="og:image" content="http://www.foodclub.ru/images/foodclub_logo.gif" />
<meta property="og:url" content="http://www.foodclub.ru/foodshot/#!foodshot" />
<meta property="og:site_name" content="Кулинарные рецепты с пошаговыми фотографиями"/>
<meta property="og:description" content="Из абрикосов получается очень вкусная выпечка, особенно если готовить ее в сезон, когда абрикосы наиболее спелые и ароматные. Абрикосы отлично сочетаются с миндалем, абрикосовую выпечку часто готовят, например, с миндальным кремом на основе растертого миндаля — франжипаном. 
В этом пироге рассыпчатое и благоухающее миндальное тесто, абрикосы, и сливочно-ореховая заливка, помогающая объединить пирог в гармоничное целое. Одновременно использование заливки вместо франжипана немного уменьшает жирность калорийность пирога.
Обратите внимание, что в выпечке многие сорта абрикосов становятся кислее, поэтому берите сладкие сорта. Если таких найти не удалось, можно предварительно карамелизовать половинки абрикосов на сковороде с 20 г масла и парой ложек сахара.
"/>';

$keywords = '<meta name="keywords" content="Foodstyling, Food design, фуд фотография, фуд дизайн, фуд фото" />';
$description = '<meta name="description" content="Быстрый и простой способ добавить рецепт." />';
$title = '<title>Фудшот — красивые фотографии еды и простые рецепты</title>';
$meta_title = '<meta name="title" content="Фудшот — красивые фотографии еды и простые рецепты" />';


		$keywords = '<meta name="keywords" content="'.$arFoodshot["name"].', Foodstyling, Food design, фуд фотография, фуд дизайн, фуд фото" />';
		$description = '<meta name="description" content="'.$arFoodshot["text"].'" />';
		$title = '<title>Фудшот — '.$arFoodshot["name"].'</title>';
		$meta_title = '<meta name="title" content="Фудшот — '.$arFoodshot["name"].'" />';
		$og = '<meta property="og:title" content="Фудшот — '.$arFoodshot["name"].'"/>
<meta property="og:type" content="food"/>
<meta property="og:image" content="http://www.foodclub.ru'.$arFoodshot["detail_image"]["src"].'" />
<meta property="og:url" content="http://www.foodclub.ru/foodshot/'.$arFoodshot["id"].'/#!foodshot" />
<meta property="og:site_name" content="Кулинарные рецепты с пошаговыми фотографиями"/>
<meta property="og:description" content="'.strip_tags($arFoodshot["text"]).'"/>';

		$strFoodshot = '';
		$strFoodshot = '<div id="content">
	<h1><span class="b-h1-heading">'.$arFoodshot["name"].'</span> <span class="b-h1-choice"><a href="#" class="b-h1-choice__item b-h1-choice__add-foodshot" id="add-foodshot-button">Добавить фудшот</a></span><div class="i-clearfix"></div></h1>
	
	<link rel="stylesheet" type="text/css" href="/foodshot/foodshot.css">
	<div class="b-foodshot-detail" id="foodshotDetail" style="display: block; position: static;">
		<div class="b-foodshot-detail__image">
			<img width="'.$arFoodshot["detail_image"]["width"].'" height="'.$arFoodshot["detail_image"]["height"].'" alt="'.$arFoodshot["name"].'" src="'.$arFoodshot["detail_image"]["src"].'" style="margin-top: 50px;">
		</div>
		
		<div class="b-foodshot-detail__like">
			<span class="b-like">
				
				<a title="Мне нравится" class="b-like-icon b-like-icon__type-button" href="#"></a>
				
				<span class="b-like-num">'.$arFoodshot["likeNum"].'</span>
			</span>			
		</div>
		<div class="b-foodshot-detail__description b-comment b-comment__type-big-userpic">
			<div class="b-comment__userpic">
				<a class="b-userpic" href="'.$arFoodshot["author"]["href"].'">
					<span class="b-userpic__layer"></span>
					<img width="100" height="100" class="b-userpic__image" alt="'.$arFoodshot["author"]["name"].'" src="'.$arFoodshot["author"]["src"].'">
				</a>
			</div>
			<div class="b-comment__content">
				<div class="b-comment__author">
					<a href="'.$arFoodshot["author"]["href"].'">'.$arFoodshot["author"]["name"].'</a>
				</div>
				<div class="b-comment__text">'.$arFoodshot["text"].'</div>';
				if($arFoodshot["source"]){
					$strFoodshot .= '<div class="b-source b-foodshot-detail__description__source">
						Источник: <a target="_blank" href="'.$arFoodshot["source"].'">'.$arFoodshot["source"].'</a>
					</div>';
				}
			$strFoodshot .= '</div>
			<div class="i-clearfix"></div>
		</div>';
		
		if(!empty($arFoodshot["comments"]["visible"])){
			$strFoodshot .= '<div class="b-foodshot-detail__comments b-comment-block-list">';
			foreach($arFoodshot["comments"]["visible"] as $comment){

				$strFoodshot .= '<div class="b-comment-block b-comment__user" data-id="'.$comment["ID"].'">
					<div class="i-relative">
						<div class="b-comment-block__pointer"></div>
						<a href="'.$comment["author"]["href"].'" class="b-userpic b-comment-block__userpic">
							<img src="'.$comment["author"]["src"].'" width="30" height="30" alt="'.$comment["author"]["name"].'" class="b-userpic__image">
						</a>
					</div>
					
					<div class="b-comment-block__content">
						<div class="b-comment-block__author">
							<a href="'.$comment["author"]["href"].'">'.$comment["author"]["name"].'</a>
						</div>
						<div class="b-comment-block__text">'.$comment["text"].'</div>
						<div class="b-comment-block__date">'.$comment["date"].'</div>
					</div>
					<div class="i-clearfix"></div>
				</div>';
			}
			$strFoodshot .= '</div>';
		}
		$strFoodshot .= '</div>';
		$folder = $_SERVER["DOCUMENT_ROOT"]."/foodshot/html/".$arFoodshot["id"]."/";
		if(!is_dir($folder)){
			if (!mkdir($folder, 0755)) {
			    die('Не удалось создать директории...');
			}
		}
		$content = '';

		$header = str_replace("#KEYWORDS#", $keywords, $header);
		$header = str_replace("#DESCRIPTION#", $description, $header);
		$header = str_replace("#TITLE#", $title, $header);
		$header = str_replace("#META_TITLE#", $meta_title, $header);
		$header = str_replace("#OG#", $og, $header);

		$content = $header.$strFoodshot."</body></html>";
		$fp = fopen($folder."index.html", 'w+');
		fwrite($fp, $content);
		fclose($fp);
	}
}
die;
?>