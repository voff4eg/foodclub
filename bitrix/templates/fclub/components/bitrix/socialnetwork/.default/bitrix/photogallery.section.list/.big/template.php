<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$GLOBALS['APPLICATION']->AddHeadString('<script src="/bitrix/js/main/utils.js"></script>', true);
$GLOBALS['APPLICATION']->AddHeadString('<script src="/bitrix/components/bitrix/photogallery/templates/.default/script.js"></script>', true);

if ($arParams["PERMISSION"] >= "W")
{
	// EbK
	$GLOBALS['APPLICATION']->IncludeComponent("bitrix:main.calendar", "", array("SILENT" => "Y"), $component, array("HIDE_ICONS" => "Y"));
}
if ($arParams["BEHAVIOUR"] == "USER"):
?><div class="photo-user<?=($arResult["GALLERY"]["CREATED_BY"] == $GLOBALS["USER"]->GetId() ? " photo-user-my" : "")?>"><?
endif;

if (!empty($arResult["SECTION"]["BACK_LINK"]) || !empty($arResult["SECTION"]["NEW_LINK"]) || !empty($arResult["SECTION"]["UPLOAD_LINK"])):
?><div class="photo-controls photo-action"><?
if (!empty($arResult["SECTION"]["BACK_LINK"])):
	?><a href="<?=$arResult["SECTION"]["BACK_LINK"]?>" title="<?=GetMessage("P_UP_TITLE")?>" class="photo-action back-to-album" <?
	?>><?=GetMessage("P_UP")?></a><?
endif;

if (!empty($arResult["SECTION"]["NEW_LINK"])):
	?><a href="<?=$arResult["SECTION"]["NEW_LINK"]?>" title="<?=GetMessage("P_ADD_ALBUM_TITLE")?>" class="photo-action new-album"<?
	?>onclick="EditAlbum('<?=CUtil::JSEscape($arResult["SECTION"]["~NEW_LINK"])?>'); return false;"<?
	?>><?=GetMessage("P_ADD_ALBUM")?></a><?
endif;

if (!empty($arResult["SECTION"]["UPLOAD_LINK"])):
	?><a href="<?=$arResult["SECTION"]["UPLOAD_LINK"]?>" title="<?=GetMessage("P_UPLOAD_TITLE")?>" class="photo-action photo-upload"<?
	?>><?=GetMessage("P_UPLOAD")?></a><?
endif;
?></div><?
?><div class="empty-clear"></div><?
endif;
if (empty($arResult["SECTIONS"])):
?><div class="empty-data"><?=GetMessage("P_EMPTY_DATA")?></div><?
else:
	foreach($arResult["SECTIONS"] as $res):
	?><table width="100%" cellpadding="0" cellspacing="0" border="0" class="photo-album" id="photo_album_info_<?=$res["ID"]?>"><?
	?><tr><td width="1%"><?
		?><div class="photo-album-img"><?
			?><table cellpadding="0" cellspacing="0" class="shadow"><?
				?><tr class="t"><td colspan="2" rowspan="2"><?
					?><div class="outer" style="width:<?=($arParams["ALBUM_PHOTO_SIZE"] + 38)?>px;"><?
						?><div class="tool" style="height:<?=$arParams["ALBUM_PHOTO_SIZE"]?>px;"></div><?
						?><div class="inner"><a href="<?=$res["LINK"]?>">
						<div class="photo-album-cover" id="photo_album_cover_<?=$res["ID"]?>" <?
							?>style="width:<?=$arParams["ALBUM_PHOTO_SIZE"]?>px; height:<?=$arParams["ALBUM_PHOTO_SIZE"]?>px;<?
							if (!empty($res["DETAIL_PICTURE"]["SRC"])):
								?>background-image:url('<?=$res["DETAIL_PICTURE"]["SRC"]?>');<?
							endif;
							?>" title="<?=htmlspecialchars($res["~NAME"])?>"></div><?
						?></a></div><?
					?></div><?
				?></td><td class="t-r">
				<div class="empty"></div></td></tr><?
				?><tr class="m"><td class="m-r"><div class="empty"></div></td></tr><?
				?><tr class="b">
					<td class="b-l"><div class="empty"></div></td>
					<td class="b-c"><div class="empty"></div></td>
					<td class="b-r"><div class="empty"></div></td></tr><?
			?></table><?
		?></div><?
	?></td><?
	?><td><?
		?><div class="photo-album-info"><?
		
			?><a href="<?=$res["LINK"]?>"><?
				?><div class="password" id="photo_album_password_<?=$res["ID"]?>" title="<?=GetMessage("P_PASSWORD")?>" <?
				if (empty($res["PASSWORD"])):
					?>style="display:none;"<?
				endif;
				?>></div><?
				?><div class="name<?=($res["ACTIVE"] != "Y" ? " nonactive" : "")?>" id="photo_album_name_<?=$res["ID"]?>"><?
					?><?=$res["NAME"]?><?
				?></div><?
			?></a><?
			
			
			?><div class="description" id="photo_album_description_<?=$res["ID"]?>"><?=$res["DESCRIPTION"]?></div><?
			
			?><div class="date" id="photo_album_date_<?=$res["ID"]?>"><?
				?><?=$res["DATE"]?><?
			?></div><?
			
			?><div class="photos"><?=GetMessage("P_PHOTOS_CNT")?>: <a href="<?=$res["LINK"]?>"><?=$res["ELEMENTS_CNT"]?></a></div><?
			
			if (intVal($res["SECTIONS_CNT"]) > 0):
				?><div class="photo-album-cnt-album"><?=GetMessage("P_ALBUMS_CNT")?>: <a href="<?=$res["LINK"]?>"><?=$res["SECTIONS_CNT"]?></a></div><?
			endif;
		
			
			?><div class="photo-controls photo-album-controls"><?
			
			if (!empty($res["EDIT_LINK"])):
				?><a href="<?=$res["EDIT_LINK"]?>" class="photo-action album-edit" <?
					?>onclick="EditAlbum('<?=CUtil::JSEscape($res["EDIT_LINK"])?>'); return false;"><?
					?><?=GetMessage("P_SECTION_EDIT")?></a><?
			endif;
		
			if (!empty($res["EDIT_ICON_LINK"])):
				?><a href="<?=$res["EDIT_ICON_LINK"]?>" class="photo-action album-edit-icon" <?
					?>onclick="EditAlbum('<?=CUtil::JSEscape($res["EDIT_ICON_LINK"])?>'); return false;"><?
					?><?=GetMessage("P_EDIT_ICON")?></a><?
			endif;
			
			if (!empty($res["DROP_LINK"])):
				?><a href="<?=$res["DROP_LINK"]?>" class="photo-action album-delete" <?
					?>onclick="return confirm('<?=GetMessage('P_SECTION_DELETE_ASK')?>');" class="edit"><?
					?><?=GetMessage("P_SECTION_DELETE")?></a><?
			endif;
			
			?></div><?
			
		?></div><?
	?></td></tr></table><?
	endforeach;
endif;
?><div class="photo-navigation"><?=$arResult["NAV_STRING"]?></div>
<div class="empty-clear"></div><?
if ($arParams["BEHAVIOUR"] == "USER"):
	?></div><?
endif;
?>