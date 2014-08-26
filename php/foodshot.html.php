<?php
$_SERVER["DOCUMENT_ROOT"] = "/home/webserver/www";
//$_SERVER["DOCUMENT_ROOT"] = "/var/www/vhosts/foodclub/site/foodclub/public_html";
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule("iblock");
$strFoodshotBoardHTML = "";
require_once($_SERVER['DOCUMENT_ROOT']."/classes/foodshot.class.php");
$CFooshot = CFoodshot::getInstance();
$arFoodshotList = $CFooshot->getList(array(),20);
if(!empty($arFoodshotList["elems"])){
	foreach($arFoodshotList["elems"] as $i => $arFoodshot){
		$strFoodshotBoardHTML .= '<div class="b-foodshot-board__item" data-id="'.$arFoodshot["id"].'" data-index="'.$i.'" style="float:left; position:static;">
	<div class="b-foodshot-board__item-content">		
		<a href="'.$arFoodshot["href"].'" class="b-foodshot-board__item-content-image" style="height: 255px;" sl-processed="1"><img src="'.$arFoodshot["image"]["src"].'" width="'.$arFoodshot["image"]["width"].'" height="'.$arFoodshot["image"]["height"].'" alt="'.$arFoodshot["name"].'"></a>
		<div class="b-foodshot-board__item-content-text">'.$arFoodshot["text"].'</div>
		<div class="b-recipe-author b-foodshot-board__item-content-author">от '.$arFoodshot["author"]["name"].'</div>
	</div>
	
	<div class="b-foodshot-board__item-action">
		<div class="b-foodshot-board__item-action_comment_hidden b-form-comments">
			<form action="" method="get">
				<div class="b-form-field b-comment b-comment__type-short b-form-field__type-comment">
					<a href="'.$arFoodshot["author"]["href"].'" class="b-comment__userpic b-userpic" sl-processed="1">
						<span class="b-userpic__layer"></span>
						<img src="'.$arFoodshot["author"]["src"].'" width="30" height="30" alt="'.$arFoodshot["author"]["name"].'" class="b-userpic__image">
					</a>
					<div class="b-comment__content">
						<textarea cols="30" rows="3" name="comment" required=""></textarea>
					</div>
					<div class="i-clearfix"></div>
				</div>
				<a href="#" class="b-form-field__type-comment__button i-frame-bg" sl-processed="1">
					<span class="i-frame-bg_left">
						<span class="i-frame-bg_right">
							<span class="i-frame-bg_bg">
								<span class="i-frame-bg_content">Комментировать</span>
							</span>
						</span>
					</span>
				</a>
				<div class="i-clearfix"></div>
			</form>
		</div>
		
		<div class="b-foodshot-board__item-action_like">
			<span class="b-like">
				<a href="#" class="b-like-icon b-like-icon__type-button" title="Мне нравится" sl-processed="1"></a>
				<span class="b-like-num">0</span>
			</span>
		</div>
		
		<div class="b-foodshot-board__item-action_comment_visible">
			<a href="#" class="b-comment-icon b-comment-icon__type-button" title="Комментировать" sl-processed="1"></a>
		</div>
		
		<div class="i-clearfix"></div>
	</div>
	
</div>';
	}

$header = '
<!DOCTYPE HTML>
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
<meta name="keywords" content="Foodstyling, Food design, фуд фотография, фуд дизайн, фуд фото" />
<meta name="description" content="Быстрый и простой способ добавить рецепт." />
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
<title>Фудшот — красивые фотографии еды и простые рецепты</title>
<link rel="stylesheet" type="text/css" href="/css/helper.css">
<script src="" type="text/javascript"></script>
<script src="/js/jscript.js" type="text/javascript"></script>
<script src="/recipe_links.js" type="text/javascript"></script>
<script src="/js/helper.js" type="text/javascript"></script>
<script src="/js/ss_data.js?1406577603" type="text/javascript"></script>
<script src="/js/history/scripts/bundled/html4+html5/jquery.history.js"></script>
<meta name="title" content="Фудшот — красивые фотографии еды и простые рецепты" />


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
	<div class="body"><!-- <div class="i-relative"><div class="b-top-panel__decor"></div></div> -->
             
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
</script>		<div id="iphone_link">
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
<script type="text/javascript" src="/js/history.js"></script>
<script type="text/javascript" src="/foodshot/foodshot.js"></script>
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
</script>
<h1><span class="b-h1-heading">Фудшот</span> <span class="b-h1-choice">
</span><div class="i-clearfix"></div></h1>
<script type="text/html" id="foodshot-detail-template">
<div id="foodshotDetail" class="b-foodshot-detail">
	<div class="b-foodshot-detail__close"><a href="#" class="b-close-icon" title="Закрыть"></a></div>

	<% var imgWidth = image.width;
		if(imgWidth > 600) {
			imgWidth = 600;
		}
	%>
	<% var imgHeight = Math.floor((image.height * imgWidth) / image.width); %>
	<div class="b-foodshot-detail__image">
		<img src="<%=image.src%>" width="<%=imgWidth%>" height="<%=imgHeight%>" alt="<%=name%>">
	</div>
	<% if(deleteIcon) { %>
	<div class="b-foodshot-detail__admin-buttons b-admin-buttons">
		<div class="b-admin-buttons__block">
			<div class="b-delete-icon" title="Удалить фудшот"></div>
			<div class="b-edit-icon" title="Редактировать"></div>
		</div>
	</div>
	<% } %>
	<div class="b-foodshot-detail__like">
		<span class="b-like">
			<a href="#" class="b-like-icon b-like-icon__type-button<% if(user_liked) { %> b-like-icon__type-active<% } %>" title="Мне нравится"></a>
			<span class="b-like-num"><%=likeNum%></span>
		</span>
		<span class="b-foodshot-detail__like__item i-vkontakte"></span>
		<span class="b-foodshot-detail__like__item i-facebook"></span>
		<span class="b-foodshot-detail__like__item i-twitter"></span>
		<span class="b-foodshot-detail__like__item i-pinterest"></span>
	</div>
	<div class="b-foodshot-detail__description b-comment b-comment__type-big-userpic">
		<div class="b-comment__userpic">
			<a href="<%=description.author.href%>" class="b-userpic">
				<span class="b-userpic__layer"></span>
				<img src="<%=description.author.src%>" width="100" height="100" alt="<%=description.author.name%>" class="b-userpic__image">
			</a>			
		</div>
		<div class="b-comment__content">
			<div class="b-comment__author">
				<a href="<%=description.author.href%>"><%=description.author.name%></a>
			</div>
			<div class="b-comment__text"><%=description.text%></div>
			
			<% if(description.source != "") { %>
			
			<div class="b-source b-foodshot-detail__description__source">
				Источник: <a href="<%=description.source%>" target="_blank"><%=description.source%></a>
			</div>
			
			<% } %>
			
		</div>
		<div class="i-clearfix"></div>
	</div>
	
	<% if(comments && comments.length > 0) { %>
				
	<div class="b-foodshot-detail__comments b-comment-block-list">
	
		<% for(var i = 0; i < comments.length; i++) { %>
		<div class="b-comment-block<% if(window.userObject && userObject.id == comments[i].author.id) { %> i-mine<% } %> b-comment__user" data-id="<%=comments[i].id%>">
			<div class="i-relative">
				<div class="b-comment-block__pointer"></div>
				<a href="<%=comments[i].author.href%>" class="b-userpic b-comment-block__userpic">
					<img src="<%=comments[i].author.src%>" width="30" height="30" alt="<%=comments[i].author.name%>" class="b-userpic__image">
				</a>
				<% if(window.userObject && (userObject.isAdmin || userObject.id == comments[i].author.id)) { %>
				<div class="b-comment-block__admin-panel b-admin-panel">
					<a href="" class="b-admin-panel__delete" title="Удалить"></a>
				</div>
				<% } %>
			</div>
			
			<div class="b-comment-block__content">
				<div class="b-comment-block__author">
					<a href="<%=comments[i].author.href%>"><%=comments[i].author.name%></a>
				</div>
				<div class="b-comment-block__text">
					<%=comments[i].text%>
				</div>
				<div class="b-comment-block__date"><%=comments[i].date%></div>
			</div>
			<div class="i-clearfix"></div>
		</div>
		<% } %>
		
	</div>
	<% } %>
	
	<% if(window.userObject) { %>
	<div class="b-form-comments">
		<form action="" method="get">
			<div class="b-form-field b-form-field__type-comment b-comment">
				<a href="<%=userObject.href%>" class="b-comment__userpic b-userpic">
					<span class="b-userpic__layer"></span>
					<img src="<%=userObject.src%>" width="30" height="30" alt="<%=userObject.name%>" class="b-userpic__image">
				</a>
				<div class="b-comment__content">
					<textarea cols="30" rows="3" name="comment" class="b-form-field__textarea b-foodshot-detail__comments__textarea" required></textarea>
				</div>
				<div class="i-clearfix"></div>
			</div>
			<div class="b-button b-form-field__type-comment__button">Комментировать</div>
			<div class="i-clearfix"></div>
		</form>
	</div>
	<% } %>
</div>
</script>';
	global $USER,$APPLICATION;
	ob_start();
	echo $header;
	?>
	<div id="" class="b-foodshot-board">
	<?=$strFoodshotBoardHTML?>
	</div>
	<?$APPLICATION->IncludeFile("/foodshot/add_foodshot.php", Array());?>
	<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_before.php");?>	
	<?
	$content = ob_get_contents();
	ob_end_clean();
	$fp = fopen($_SERVER["DOCUMENT_ROOT"]."/foodshot/index.html", 'w+');
	fwrite($fp, $content);
	fclose($fp);
	die;
}
?>