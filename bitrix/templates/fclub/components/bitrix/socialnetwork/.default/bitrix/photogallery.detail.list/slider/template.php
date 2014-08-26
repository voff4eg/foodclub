<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!function_exists("__GetIndex"))
{
	function __GetIndex()
	{
		static $arIndex = array();
		do 
		{
			$index = rand();
		} while (in_array($index, $arIndex));
		$arIndex[] = $index;
		return $index;
	}
}
$index = __GetIndex();
$arParams["SLIDER_COUNT_CELL"] = intVal($arParams["SLIDER_COUNT_CELL"]);
$panelHeight = 
	$arResult["MAX_VAL"]["HEIGHT"] 
	+ 1*2 		// image border
	+ 1*2 + 5*2	// anchor border + padding
	+ 2*2		// td padding
	+ 5*2		// main td padding
	+ 1 + 5;	// panel border + rate
$cellWidth = $arResult["MAX_VAL"]["WIDTH"] 
	+ 1*2 		// image border
	+ 1*2 + 5*2	// anchor border + padding
	+ 2*2 + 5;	// td padding + rate
$cellHeight = $arResult["MAX_VAL"]["HEIGHT"] 
	+ 1*2 		// image border
	+ 1*2 + 5*2	// anchor border + padding
	+ 2*2 + 5;	// td padding + rate
if ($arParams["BEHAVIOUR"] == "USER"):
?><div class="photo-user<?=($arResult["GALLERY"]["CREATED_BY"] == $GLOBALS["USER"]->GetId() && $GLOBALS["USER"]->IsAuthorized() ? 
	" photo-user-my" : "")?>"><?
endif;

?><div class="empty-clear"></div><?
if (($arParams["SHOW_PAGE_NAVIGATION"] == "top" || $arParams["SHOW_PAGE_NAVIGATION"] == "both") && !empty($arResult["NAV_STRING"])):
	?><div class="photo-navigation"><?=$arResult["NAV_STRING"]?></div><?
endif;

?><div class="photo-photos"><?
if ($arParams["SHOW_DESCRIPTION"] != "N")
{
	?><div class="photo-header"><?=GetMessage("P_ALL_PHOTO")?>:</div><br /><?
}

if (!empty($arResult["ELEMENTS_CURR"])):
?><table class="photo-navigation" cellpadding="0" cellspacing="0" border="0" style="height:<?=$panelHeight?>px;">
	<tr>
		<td width="20px">
			<table class="photo-left" cellpadding="0" cellspacing="0" border="0" style="height:<?=$panelHeight?>px;">
				<tr style="height:<?=($panelHeight - 28)?>px;">
					<td	class="photo-left-top" id="photo_prev_<?=$index?>"
						onmouseover="this.className='photo-left-top photo-left-top-over'" 
						onmouseout="this.className='photo-left-top'"
						onmousedown="this.className='photo-left-top photo-left-top-active'" 
						onmouseup="this.className='photo-left-top photo-left-top-over'">
						<div class="empty"></div></td></tr>
				<tr style="height:28px;">
					<td class="photo-left-bottom" id="photo_first_<?=$index?>"
						onmouseover="this.className='photo-left-bottom photo-left-bottom-over'" 
						onmouseout="this.className='photo-left-bottom'"
						onmousedown="this.className='photo-left-bottom photo-left-bottom-active'" 
						onmouseup="this.className='photo-left-bottom  photo-left-bottom-over'">
						<div class="empty"></div></td></tr>
			</table>
		</td>
		<td id="slider_window_<?=$index?>" class="slider_window" style="width:<?=($cellWidth*$arParams["SLIDER_COUNT_CELL"] + 10)?>px;"><?
			?><table id="table_photo_photos_<?=$index?>" class="table_photo_photos" border="0" cellpadding="0" cellspacing="0" style="height:<?=($panelHeight-10)?>px;"><tbody><tr><?
	foreach ($arResult["ELEMENTS_CURR"] as $res):
		?><td id="td_<?=$res["id"]?>_<?=$index?>" style="width:<?=$cellWidth?>px;"><?
			?><a href="<?=htmlspecialchars($res["url"])?>" title="<?=$res["title"]?>" <?
				?>style="width:<?=($res["width"] + 12)?>px;"<?
				?><?=($res["active"] == "Y" ? " class='active'" : "")?>><?
				?><img src="<?=$res["src"]?>" <?
					?>width="<?=$res["width"]?>" height="<?=$res["height"]?>" <?
					?>alt="<?=$res["alt"]?>" title="<?=$res["title"]?>" <?
					?>border="0" /><?
			?></a><?
		?></td><?
	endforeach;
	?></tr></tbody></table></td>
	
		<td width="20px">
			<table class="photo-right" cellpadding="0" cellspacing="0" border="0" style="height:<?=$panelHeight?>px;">
				<tr style="height:<?=($panelHeight - 28)?>px;">
					<td	class="photo-right-top" id="photo_next_<?=$index?>" 
						onmouseover="this.className='photo-right-top photo-right-top-over'" 
						onmouseout="this.className='photo-right-top'"
						onmousedown="this.className='photo-right-top photo-right-top-active'" 
						onmouseup="this.className='photo-right-top photo-right-top-over'">
						<div class="empty"></div></td></tr>
				<tr style="height:28px;">
					<td class="photo-right-bottom" id="photo_last_<?=$index?>"
						onmouseover="this.className='photo-right-bottom photo-right-bottom-over'" 
						onmouseout="this.className='photo-right-bottom'"
						onmousedown="this.className='photo-right-bottom photo-right-bottom-active'" 
						onmouseup="this.className='photo-right-bottom  photo-right-bottom-over'">
						<div class="empty"></div></td></tr>
			</table>
		</td>
	</tr>
</table>
</div>
<div id="show_debug">
</div>
<script>
window.b_active_is_fined = '<?=$arParams["B_ACTIVE_IS_FINED"]?>';
if (typeof PhotoTape != "object")
	var PhotoTape = {};
PhotoTape["<?=$index?>"] = null;
function to_init_<?=$index?>()
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
		PhotoTape["<?=$index?>"] = new PhotoConstructor({
				"curr" : <?=CUtil::PhpToJSObject($arResult["ELEMENTS_CURR"])?>,
				"prev" : <?=CUtil::PhpToJSObject($arResult["ELEMENTS_PREV"])?>,
				"next" : <?=CUtil::PhpToJSObject($arResult["ELEMENTS_NEXT"])?>});
		params = {
			"prev" : document.getElementById('photo_prev_<?=$index?>'),
			"first" : document.getElementById('photo_first_<?=$index?>'),
			"next" : document.getElementById('photo_next_<?=$index?>'),
			"last" : document.getElementById('photo_last_<?=$index?>'),
			"width" : <?=$cellWidth?>,
			"height" : <?=$cellHeight?>,
			"index" : <?=$index?>
		};
		PhotoTape["<?=$index?>"].Init(document.getElementById('table_photo_photos_<?=$index?>'), params);
	}
	else
	{
		setTimeout(to_init_<?=$index?>, 100);
	}
}
if (window.attachEvent) 
	window.attachEvent("onload", to_init_<?=$index?>);
else if (window.addEventListener) 
	window.addEventListener("load", to_init_<?=$index?>, false);
else
	setTimeout(to_init_<?=$index?>, 100);
</script><?
endif;

if (($arParams["SHOW_PAGE_NAVIGATION"] == "bottom" || $arParams["SHOW_PAGE_NAVIGATION"] == "both") && !empty($arResult["NAV_STRING"])):
	?><div class="photo-navigation"><?=$arResult["NAV_STRING"]?></div><?
endif;
?><div class="empty-clear"></div><?
if ($arParams["BEHAVIOUR"] == "USER"):
?></div><?
endif;
?>