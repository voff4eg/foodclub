<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if (!empty($arResult["ELEMENTS_LIST"])):
$GLOBALS['APPLICATION']->RestartBuffer();
$arParams["ELEMENT_ID"] = intVal($arParams["ELEMENT_ID"]);
?><html><head>
<link href="/bitrix/components/bitrix/photogallery.detail.list/templates/slide_show/style.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="/bitrix/components/bitrix/photogallery/templates/.default/script.js"></script>
<script type="text/javascript" src="/bitrix/components/bitrix/photogallery.detail.list/templates/slide_show/script.js"></script>
<script src="/bitrix/js/main/utils.js"></script>
<script language="JavaScript" type="text/javascript">
function SetBackGround(div)
{
		if (!div){return false;}
		document.body.style.backgroundColor = div.style.backgroundColor;
}
</script>
<title><?
if (!empty($arResult["SECTION"])):
	?><?=$arResult["SECTION"]["NAME"]?><?
else:
	?><?=GetMessage("P_TITLE")?><?
endif;
?></title>
</head>
<body class="photo-slide-show">
<div class="image-upload" id="image-upload"><?=GetMessage("P_LOADING")?></div>
<table width="100%" height="100%" border=0 cellpadding=0 cellspacing=0>
<tr><td align=right width="0">
<table align=center cellpadding=0 cellspacing=2 border=0>
<tr><td><div style="width:18px; height:18px; background-color:#FFFFFF;" onmouseover="SetBackGround(this);"></div></td></tr>
<tr><td><div style="width:18px; height:18px; background-color:#E5E5E5;" onmouseover="SetBackGround(this);"></div></td></tr>
<tr><td><div style="width:18px; height:18px; background-color:#CCCCCC;" onmouseover="SetBackGround(this);"></div></td></tr>
<tr><td><div style="width:18px; height:18px; background-color:#B3B3B3;" onmouseover="SetBackGround(this);"></div></td></tr>
<tr><td><div style="width:18px; height:18px; background-color:#999999;" onmouseover="SetBackGround(this);"></div></td></tr>
<tr><td><div style="width:18px; height:18px; background-color:#808080;" onmouseover="SetBackGround(this);"></div></td></tr>
<tr><td><div style="width:18px; height:18px; background-color:#666666;" onmouseover="SetBackGround(this);"></div></td></tr>
<tr><td><div style="width:18px; height:18px; background-color:#4D4D4D;" onmouseover="SetBackGround(this);"></div></td></tr>
<tr><td><div style="width:18px; height:18px; background-color:#333333;" onmouseover="SetBackGround(this);"></div></td></tr>
<tr><td><div style="width:18px; height:18px; background-color:#1A1A1A;" onmouseover="SetBackGround(this);"></div></td></tr>
<tr><td><div style="width:18px; height:18px; background-color:#000000;" onmouseover="SetBackGround(this);"></div></td></tr>
</table></td>
<td align="center" width="100%" valign="center" style="padding-top:5px;"><div id="image_div"><img src="" width="0" height="0" id="image" /></div></td>
</tr></table>
<div id="control_container">
<div id="navigator_container">
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr valign="middle">
		<td align="left" width="40%"><div id="title"><?=GetMessage("P_NAME")?></div></td>
		<td align="center" width="20%">
			<table cellpadding="0" cellspacing="0" border="0" class="inner">
				<tr><td><div id="prev"></div></td>
				<td><div id="play"></div></td>
				<td><div id="next"></div></td></tr>
			</table>
		</td>
		<td align="center" width="15%"><span id="counter">1</span> <?=GetMessage("P_OF")?> <?=count($arResult['ELEMENT_FOR_JS'])?> </td>
		<td align="center" width="15%">
				<table cellpadding="0" cellspacing="0" border="0" class="inner">
					<tr><td><div id="time_minus"></div></td>
					<td><div id="time_container"></div></td><td><?=GetMessage("P_SEK")?></td>
					<td><div id="time_plus"></div></td></tr>
				</table></td>
		<td  width="10%" valign="top" align="right"><div id="stop"></div></td>
	</tr>
</table></div>
</div>
<script>
function to_init()
{
	var is_loaded = false;
	try
	{
		if (bPhotoUtilsLoad == true)
			is_loaded = true;
	}
	catch(e){}
	
	if (is_loaded)
	{
//		var div = document.getElementById('title');
//		div.style.left = parseInt(document.body.scrollLeft + document.body.clientWidth/2 - div.offsetWidth/2) + "px";
		
		window.params = {'x' : 0, 'y' : 0};
		
		function Show(e)
		{
			div = document.getElementById('navigator_container');
			div.style.top = "-35px";
			div.style.left = parseInt(document.body.scrollLeft + document.body.clientWidth/2 - 600/2) + "px";
			div.style.display = 'block';
			if (typeof e == "object")
			{
		        var windowSize = GetWindowSize();
				params['x'] = e.clientX + windowSize.scrollLeft;
				params['y'] = e.clientY + windowSize.scrollTop;
			}
			
//			setTimeout(new Function("if ((" + params['x'] + " == params['x']) && (" + params['y'] + " == params['y'])) {document.getElementById('navigator_container').style.display = 'none';}"), 5000);
		}
		
		function CheckShow(e)
		{
	        var windowSize = GetWindowSize();
	        var x = e.clientX + windowSize.scrollLeft;
	        var y = e.clientY + windowSize.scrollTop;
	        if (params['x'] != x || params['y'] != y)
	        {
	        	params['x'] = x;
	        	params['y'] = y;
	        	Show();
	        }
		}
		
		Show();
		jsUtils.addEvent(document, "mousemove", CheckShow);
		jsUtils.addEvent(document, "click", Show);
		jsUtils.addEvent(window, "resize", Show);

		SlideShow = new PhotoClassNavigator();
		SlideShow.Init(
			<?=CUtil::PhpToJSObject($arResult['ELEMENT_FOR_JS'])?>, 
			{
				'iActive' : <?=intVal($arParams["ELEMENT_ID"])?>,
				'sImage' : 'image',
				'sImageTitleDiv' : 'title',
				'sCounterDiv' : 'counter',
				'next' : 'next',
				'prev' : 'prev',
				'pause' : 'play',
				'stop' : 'stop',
				'plus' : 'time_plus',
				'minus' : 'time_minus',
				'time' : 'time_container',
				'index' : 'SlideShow',
				'url' : '<?=CUtil::JSEscape(!empty($arParams["BACK_URL"]) ? $arParams["BACK_URL"] : $arParams["DETAIL_URL_FOR_JS"])?>'});
//		jsUtils.addEvent(document, "keydown", SlideShow.Stop);
	}
	else
		setTimeout(to_init, 100);
}
if (window.attachEvent) 
	window.attachEvent("onload", to_init);
else if (window.addEventListener) 
	window.addEventListener("load", to_init, false);
else
	setTimeout(to_init, 100);
</script>
</body></html><?
else:
	LocalRedirect("");
endif;

die();
?>