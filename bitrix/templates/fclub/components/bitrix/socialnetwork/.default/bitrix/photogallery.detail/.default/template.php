<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeAJAX();
$GLOBALS['APPLICATION']->AddHeadString('<script src="/bitrix/js/main/utils.js"></script>', true);
$GLOBALS['APPLICATION']->AddHeadString('<script src="/bitrix/components/bitrix/photogallery/templates/.default/script.js"></script>', true);
$temp = array("STRING" => preg_replace("/[^0-9]/is", "/", $arParams["THUMBS_SIZE"]));
list($temp["WIDTH"], $temp["HEIGHT"]) = explode("/", $temp["STRING"]);
$arParams["THUMBS_SIZE"] = (intVal($temp["WIDTH"]) > 0 ? intVal($temp["WIDTH"]) : 300);
// EbK
$GLOBALS['APPLICATION']->IncludeComponent("bitrix:main.calendar", "", array("SILENT" => "Y"), $component, array("HIDE_ICONS" => "Y"));
if ($arParams["SHOW_TAGS"] == "Y" && IsModuleInstalled("search")):
$GLOBALS['APPLICATION']->IncludeComponent("bitrix:search.tags.input", "", array("SILENT" => "Y"), $component, array("HIDE_ICONS" => "Y"));
endif;
if ($arParams["BEHAVIOUR"] == "USER"):
?><div class="photo-user<?=($arResult["GALLERY"]["CREATED_BY"] == $GLOBALS["USER"]->GetId() && $GLOBALS["USER"]->IsAuthorized() ? 
	" photo-user-my" : "")?>"><?
endif;

?><div class="photo-controls  photo-action"><?
if (!empty($arResult["SECTION"]["BACK_LINK"])):
	?><a href="<?=$arResult["SECTION"]["BACK_LINK"]?>" title="<?=GetMessage("P_GO_TO_SECTION")?>" class="photo-action back-to-album" <?
	?>><?=GetMessage("P_UP")?></a><?
endif;

if (!empty($arResult["SECTION"]["UPLOAD_LINK"])):
	?><a href="<?=$arResult["SECTION"]["UPLOAD_LINK"]?>" title="<?=GetMessage("P_UPLOAD_TITLE")?>" class="photo-action photo-upload" <?
	?>><?=GetMessage("P_UPLOAD")?></a></b><?
endif;

?></div><?
?><div class="empty-clear"></div><?

?><div class="photo-detail">

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr valign="top"><td width="1%"  align="center"><?

if (!empty($arResult["ELEMENT"]["REAL_PICTURE"]["SRC"])):
?>
<a href="<?=$arResult["SLIDE_SHOW"]?>"><?
endif;

?><div class="photo-photo"><?
	?><div class="photo-img"><?
			?><?=CFile::ShowImage($arResult["ELEMENT"]["PICTURE"]["SRC"], 
				$arParams["THUMBS_SIZE"], 
				$arParams["THUMBS_SIZE"], 
				"border=\"0\" vspace=\"0\" hspace=\"0\" alt=\"".$arResult["ELEMENT"]["CODE"]."\" title=\"".htmlspecialchars($arResult["ELEMENT"]["~NAME"])."\"");?><?	
	?></div><?
?></div><?

if (!empty($arResult["ELEMENT"]["REAL_PICTURE"]["SRC"])):
?></a><?
endif;

$ii = ($arResult["ELEMENT"]["CURRENT"]["NO"] - 1);

?><div id="photo_navigation">
	<table cellpadding="5" cellspacing="0" border="0">
		<tr><td><?
	if (!empty($arResult["ELEMENTS_LIST"][$ii-1]["DETAIL_PAGE_URL"])):
		?><a href="<?=$arResult["ELEMENTS_LIST"][$ii-1]["DETAIL_PAGE_URL"]?>">
		<div id="photo_go_to_prev" title="<?=GetMessage("P_GO_TO_PREV")?>"></div>
		</a><?
	else :
		?><div id="photo_go_to_prev_disabled" title="<?=GetMessage("P_GO_TO_PREV")?>"></div><?
	endif;
?></td>
		<td nowrap="nowrap"><?=GetMessage("NO_OF_COUNT",array("#NO#"=>$arResult["ELEMENT"]["CURRENT"]["NO"],"#TOTAL#"=>$arResult["ELEMENT"]["CURRENT"]["COUNT"]))?></td>
		<td><?
	if (!empty($arResult["ELEMENTS_LIST"][$ii+1]["DETAIL_PAGE_URL"])):
	?><a href="<?=$arResult["ELEMENTS_LIST"][$ii+1]["DETAIL_PAGE_URL"]?>">
	<div id="photo_go_to_next" title="<?=GetMessage("P_GO_TO_NEXT")?>"></div>
	</a><?
	else:
	?><div id="photo_go_to_next_disabled" title="<?=GetMessage("P_GO_TO_NEXT")?>"></div><?
	endif;
?>
		</td></tr></table>
</div>


</td><td align="left">


<div id="photo_text_description">
	<div class="photo-title" id="photo_title"><?=$arResult["ELEMENT"]["NAME"]?></div>
	<div class="photo-date" id="photo_date"><?=$arResult["ELEMENT"]["DATE_CREATE"]?></div><?
	
	if ($arParams["SHOW_TAGS"] == "Y" && !empty($arResult["ELEMENT"]["TAGS_LIST"])):
		?><div class="photo-tags" id="photo_tags"><?
		
		foreach ($arResult["ELEMENT"]["TAGS_LIST"] as $key => $val):
			?><a href="<?=$val["TAGS_URL"]?>"><?=$val["TAGS_NAME"]?></a><?
			if ($key != (count($arResult["ELEMENT"]["TAGS_LIST"]) - 1)):
			?>, <?
			endif;
		endforeach;
		
		?></div><?
	elseif ($arParams["SHOW_TAGS"] == "Y"):
		?><div class="photo-tags" id="photo_tags"><?=$arResult["ELEMENT"]["TAGS"]?></div><?
	endif;

	?><div class="photo-description" id="photo_description"><?=$arResult["ELEMENT"]["DETAIL_TEXT"]?></div><?
	
	?><div class="photo-controls photo-view"><?
if (!empty($arResult["SLIDE_SHOW"])):
	?><a href="<?=$arResult["SLIDE_SHOW"]?>" class="photo-view slide-show" title="<?=GetMessage("P_SLIDE_SHOW_TITLE")?>"><?
			?><?=GetMessage("P_SLIDE_SHOW")?></a><?
endif;

if (!empty($arResult["ELEMENT"]["REAL_PICTURE"]["SRC"])):
	?><a href="http://<?=str_replace("//", "/", $_SERVER['HTTP_HOST']."/".$arResult["ELEMENT"]["REAL_PICTURE"]["SRC"])?>" <?
			?>title="<?=GetMessage("P_ORIGINAL_TITLE")?>" 
			onclick="ShowOriginal('http://<?=CUtil::JSEscape(str_replace("//", "/", $_SERVER['HTTP_HOST']."/".$arResult["ELEMENT"]["REAL_PICTURE"]["SRC"]))?>', '<?=CUtil::JSEscape($arResult["ELEMENT"]["NAME"])?>'); return false;" class="photo-view original"><?
			?><?=GetMessage("P_ORIGINAL")?></a><?
endif;
	?></div><?

	?><div class="photo-controls"><?
if (!empty($arResult["ELEMENT"]["EDIT_URL"])):
	?><a href="<?=$arResult["ELEMENT"]["EDIT_URL"]?>" title="<?=GetMessage("P_EDIT_TITLE")?>" <?
		?>onclick="EditPhoto('<?=CUtil::JSEscape($arResult["ELEMENT"]["~EDIT_URL"])?>'); return false;" id="photo_edit" <?
		?>class="photo-action edit"><?=GetMessage("P_EDIT")?></a><?
endif;
if (!empty($arResult["ELEMENT"]["DROP_URL"])):
		?><a href="<?=$arResult["ELEMENT"]["DROP_URL"]?>" title="<?=GetMessage("P_DROP_TITLE")?>" <?
			?>onclick="return confirm('<?=GetMessage("P_DROP_CONFIM")?>');" id="photo_drop" class="photo-action delete"><?=GetMessage("P_DROP")?></a><br /><br /><?
endif;
	?></div><?
?>
<br />
<div class="photo-controls"><div id="photo_vote"></div></div>
</div>
</td></tr>
</table></div>
<?
if ($arParams["BEHAVIOUR"] == "USER"):
?></div><?
endif;
?>