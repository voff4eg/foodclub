<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta name="verify-v1" content="IFNqswFktC+hhGa2ZKs6Ale87GxdIORrcVznFXPdEh4=" >
<meta name='yandex-verification' content='7e14af38f0152a84' />  
<?$APPLICATION->ShowHead()?>
<title><?$APPLICATION->ShowTitle()?></title>

<?if(strpos($APPLICATION->GetCurDir(), "recipe/") !== false){?>
<link rel="stylesheet" type="text/css" href="/css/styles.css">
<link rel="stylesheet" type="text/css" href="/css/admin/styles.css">
<link rel="stylesheet" type="text/css" href="/css/helper.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js" type="text/javascript"></script>
<script src="/js/admin/jscript.js" type="text/javascript"></script>
<script src="/js/jscript.js" type="text/javascript"></script>
<script src="/recipe_links.js" type="text/javascript"></script>
<script src="/js/stepcarousel.js" type="text/javascript"></script>
<script src="/js/helper.js" type="text/javascript"></script>

<?} else {?>
<script src="/js/stepcarousel.js" type="text/javascript"></script>

<link rel="stylesheet" type="text/css" href="/css/styles.css">
<link rel="stylesheet" type="text/css" href="/css/helper.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js" type="text/javascript"></script>
<script src="/js/jscript.js" type="text/javascript"></script>
<script src="/recipe_links.js" type="text/javascript"></script>
<script src="/js/stepcarousel.js" type="text/javascript"></script>
<script src="/js/helper.js" type="text/javascript"></script>

<?echo $APPLICATION->AddBufferContent("setHeaderContent");?>
<?}?>
</head>
<?
CModule::IncludeModule("iblock");
require($_SERVER["DOCUMENT_ROOT"].'/classes/main.class.php');?>
<body>
<?if (CModule::IncludeModule("advertising")){ $strBanner = CAdvBanner::Show("top_banner"); }?>
<?$APPLICATION->ShowPanel();?>
<?if($strBanner){?><div id="top_banner" style="background:url(/images/infoblock/top_banner_bg.gif) repeat-x left bottom;"><div class="body"><?=$strBanner?></div></div><?}?>
<div id="body">
	<div class="padding">
		<div id="head">
			<div id="logo">
			<?if($APPLICATION->GetCurDir() != "/"){?>
				<a href="/?PAGEN_1=1" title="Кулинарные рецепты с фотографиями"><img src="/images/foodclub_logo.gif" width="143" height="102" alt="Кулинарные рецепты с фотографиями"></a>
			<?} else {?>
				<h1><img src="/images/foodclub_logo.gif" width="143" height="102" alt="Кулинарные рецепты с фотографиями" title="Кулинарные рецепты с фотографиями"></h1>
			<?}?>
			</div>
			<div id="recipe_search">
				<div class="search_field"><div class="search_delete" title="Очистить поле"></div><form name="recipe_search" action="" method="post"><input type="text" class="text" id="recipe_search_field" value="Я ищу"><input type="image" src="/images/search_button.gif" width="49" height="49" alt="Найти" title="Найти" class="button"><div class="clear"></div></form></div>
				<p><a href="#" class="no_link" id="search_helper_link">Помощник</a> (новый сервис поиска)</p>
			</div>
			<div class="clear"></div>
		</div>
	<?include 'menu.inc.php';?>
	
	
	<?if($APPLICATION->GetCurPage(true) == "/index.php"){?>
	    <?
	    $obLast = new CPageCache;
	    if($USER->IsAdmin() || $obLast->StartDataCache((3*60*60), "main_last_recipe")):
	        $CFClub = new CFClub();
            $arRecipes = $CFClub->getList(30);
	    ?>
	    <script type="text/javascript">
		    stepcarousel.setup({
			    galleryid: 'recipe_line', //id of carousel DIV
			    beltclass: 'belt', //class of inner "belt" DIV containing all the panel DIVs
			    panelclass: 'item', //class of panel DIVs each holding content
			    autostep: {enable:false, moveby:1, pause:3000},
			    panelbehavior: {speed:500, wraparound:false, persist:true},
			    defaultbuttons: {enable: false, moveby: 1, leftnav: ['http://i34.tinypic.com/317e0s5.gif', -5, 80], rightnav: ['http://i38.tinypic.com/33o7di8.gif', -20, 80]},
			    statusvars: ['statusA', 'statusB', 'statusC'], //register 3 variables that contain current panel (start), current panel (last), and total panels
			    contenttype: ['inline'] //content setting ['inline'] or ['external', 'path_to_external_file']
		    })
	    </script>

	    <div id="recipe_line_block">
		    <div id="recipe_line">
			    <div class="belt">
                <?foreach($arRecipes['ITEMS'] as $k=>$E){?>		
				    <div class="item">
					    <div class="photo" id="rlb_photo<?=$k?>"><div class="big_photo" id="rlb_photo<?=$k?>_big"><div><table class="frame"><tr><td class="tl"><img src="/images/spacer.gif" width="11" height="11" alt=""></td><td class="top"><img src="/images/spacer.gif" width="1" height="11" alt=""></td><td class="tr"><img src="/images/spacer.gif" width="14" height="11" alt=""></td></tr><tr><td class="left"><img src="/images/spacer.gif" width="11" height="1" alt=""></td><td class="middle"><a href="/detail/<?=$E['ID']?>/"><img src="<?=$E['PREVIEW_PICTURE']['SRC']?>" width="<?=$E['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$E['PREVIEW_PICTURE']['HEIGHT']?>" alt=""></a></td><td class="right"><img src="/images/spacer.gif" width="14" height="1" alt=""></td></tr><tr><td class="bl"><img src="/images/spacer.gif" width="11" height="14" alt=""></td><td class="bottom"><img src="/images/spacer.gif" width="1" height="14" alt=""></td><td class="br"><img src="/images/spacer.gif" width="14" height="14" alt=""></td></tr></table></div></div><img src="<?=$E['PREVIEW_PICTURE']['SRC']?>" width="36" height="24" alt=""></div>
					    <div class="name"><a href="/detail/<?=$E['ID']?>/"><?=$E['NAME']?></a>&nbsp;<span class="comments">(<a href="/detail/<?=$E['ID']?>/#comments"><?=intval($E['PROPERTY_COMMENT_COUNT_VALUE'])?></a>)</span><span class="author">От: <?=$E['USER']['LOGIN']?></span></div>
				    </div>
                <?}?>
			    </div>
		    </div>
		    <div class="relative">

			    <div class="backward pointer"></div>
			    <div class="forward"><div class="pointer"></div></div>
			    <div id="big_recipe_photos"></div>
		    </div>
	    </div>
	    <?
	    $obLast->EndDataCache();
	    endif;
	    ?>
    <?}?>
	</div>
