<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeTemplateLangFile(__FILE__);
?>
<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/xhtml"
      xmlns:og="http://ogp.me/ns#1"
      xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=LANG_CHARSET;?>">
<?$APPLICATION->ShowMeta("robots")?>
<?$APPLICATION->ShowMeta("keywords")?>
<?$APPLICATION->ShowMeta("description")?>
<title><?$APPLICATION->ShowTitle()?></title>
<?$APPLICATION->ShowCSS();?>
<script type="text/javascript" src="/js/jquery-1.8.3.min.js?<?=filectime($_SERVER["DOCUMENT_ROOT"]."/js/jquery-1.8.3.min.js")?>"></script>
<?$APPLICATION->ShowHeadStrings()?>
<?$APPLICATION->ShowHeadScripts()?>
<?//$APPLICATION->ShowHead(true)?>
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
<?//$APPLICATION->AddHeadScript("/js/jquery-1.8.3.min.js");?>
<?
$APPLICATION->SetAdditionalCSS("/css/profile.css");
$APPLICATION->SetAdditionalCSS("/css/elem.css");
if(strpos($APPLICATION->GetCurDir(), "recipe/") !== false){
$APPLICATION->SetAdditionalCSS("/css/admin/styles.css",true);
$APPLICATION->SetAdditionalCSS("/css/helper.css",true);

$APPLICATION->AddHeadScript("/js/jscript.min.js");
$APPLICATION->AddHeadScript("/js/admin/jscript.js");
$APPLICATION->AddHeadScript("/js/helper.js");
//$APPLICATION->AddHeadScript("/js/history/scripts/bundled/html4+html5/jquery.history.js");

} else {

$APPLICATION->AddHeadScript("/js/elem.js");
$APPLICATION->AddHeadScript("/js/jscript.min.js");

$APPLICATION->AddHeadScript("/recipe_links.js");
$APPLICATION->AddHeadScript("/js/helper.js");
//$APPLICATION->AddHeadScript("/js/history/scripts/bundled/html4+html5/jquery.history.js");

$APPLICATION->SetAdditionalCSS("/css/helper.css",true);
?>
<meta name="title" content="<?$APPLICATION->ShowTitle();?>" />
<?}?>
<?//$APPLICATION->AddHeadScript("https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js");?>
<?if($APPLICATION->GetCurDir() == "/recipes/"):?>
<?$APPLICATION->AddHeadScript("/js/ss_data.js");?>
<?endif;?>

<!-- Vkontakte -->
<script type="text/javascript" src="http://userapi.com/js/api/openapi.js?47"></script>
<script type="text/javascript">
  VK.init({apiId: 2404991, onlyWidgets: true});
</script>
</head>
<?php include_once($_SERVER["DOCUMENT_ROOT"]."/analytics.php") ?>
<?
CModule::IncludeModule("iblock");
require_once($_SERVER["DOCUMENT_ROOT"].'/classes/main.class.php');?>
<body data-site-id="<?=SITE_ID?>">

<script type='text/javascript'>var _merchantSettings=_merchantSettings || [];_merchantSettings.push(['AT', '1l3vmcn']);(function(){var autolink=document.createElement('script');autolink.type='text/javascript';autolink.async=true; autolink.src= ('https:' == document.location.protocol) ? 'https://autolinkmaker.itunes.apple.com/js/itunes_autolinkmaker.js' : 'http://autolinkmaker.itunes.apple.com/js/itunes_autolinkmaker.js';var s=document.getElementsByTagName('script')[0];s.parentNode.insertBefore(autolink, s);})();</script>

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
	<div class="body">
	<?$APPLICATION->IncludeComponent(
		"bitrix:main.user.link",
		"top",
		Array(
			"CACHE_TYPE" => "A",
			"CACHE_TIME" => "7200",
			"ID" => CUser::GetID(),
			"SHOW_LOGIN" => "Y",
			"USE_THUMBNAIL_LIST" => "Y",
			"SHOW_FIELDS" => array("PERSONAL_BIRTHDAY", "PERSONAL_ICQ", "PERSONAL_PHOTO", "PERSONAL_CITY", "WORK_COMPANY", "WORK_POSITION"),
			"USER_PROPERTY" => array(),
			"PATH_TO_SONET_USER_PROFILE" => "",
			"PROFILE_URL" => "",
			"DATE_TIME_FORMAT" => "d.m.Y H:i:s",
			"SHOW_YEAR" => "Y"
		)
	);?>
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
