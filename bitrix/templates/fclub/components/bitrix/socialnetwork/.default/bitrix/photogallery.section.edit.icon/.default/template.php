<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if ($arParams["BEHAVIOUR"] == "USER"):
?><div class="photo-user<?=($arResult["GALLERY"]["CREATED_BY"] == $GLOBALS["USER"]->GetId() && $GLOBALS["USER"]->IsAuthorized() ? " photo-user-my" : "")?>"><?
endif;

if ($arParams["AJAX_CALL"] != "Y"):
	?><div class="photo-controls"><?
	if (!empty($arResult["SECTION"]["SECTION_LINK"])):
		?><a href="<?=$arResult["SECTION"]["SECTION_LINK"]?>" title="<?=GetMessage("P_BACK_UP_TITLE")?>" class="photo-action back-to-album" <?
		?>><?=GetMessage("P_BACK_UP")?></a><?
	endif;
	if (!empty($arResult["SECTION"]["BACK_LINK"])):
		?><a href="<?=$arResult["SECTION"]["BACK_LINK"]?>" title="<?=GetMessage("P_UP_TITLE")?>" <?
		?>><?=GetMessage("P_UP")?></a><?
	endif;
	?></div><?
endif;

if ($arParams["AJAX_CALL"] == "Y"):
	$APPLICATION->RestartBuffer();
endif;

?><div class="photo-window-edit" id="photo_section_edit_form">
<form method="post" action="<?=POST_FORM_ACTION_URI?>" name="form_photo" id="form_photo" onsubmit="return CheckFormEditIcon(this);" class="photo-form">
	<input type="hidden" name="edit" value="Y" />
	<input type="hidden" name="sessid" value="<?=bitrix_sessid()?>" />
	<input type="hidden" name="IBLOCK_SECTION_ID" value="<?=$arResult["FORM"]["IBLOCK_SECTION_ID"]?>" />
	
<table cellpadding="0" cellspacing="0" border="0" class="photo-popup">
<?if ($arParams["AJAX_CALL"] == "Y"):?>
	<thead>
		<tr>
			<td><?=$arResult["PAGE_TITLE"]?></td>
		</tr>
	</thead>
<?endif;?>
	<tbody>
		<tr><td class="table-body">
			<div class="inner"><?	
		if (!empty($arResult["ERROR_MESSAGE"]))
		{
			ShowError($arResult["ERROR_MESSAGE"]);
			?><br /><?
		}
		?><?=GetMessage("P_SELECT_PHOTO")?><br /><?

if (count($arResult["ITEMS"]) > 0):
	foreach ($arResult["ITEMS"]	as $key => $arItem):
		if (is_array($arItem)):
		?><div class="photo-photo" style="height:<?=($arResult["ELEMENTS"]["MAX_HEIGHT"] + 10)?>px;"><?
			if(is_array($arItem["PICTURE"])):
				?><input type="checkbox" name="photos[]" id="photo_<?=$arItem["ID"]?>" value="<?=$arItem["ID"]?>"<?
					if (is_array($_REQUEST["photos"]) && in_array($arItem["ID"], $_REQUEST["photos"])):
						?> checked="checked" <?
					endif;
					?> /><?
				?><img border="0" src="<?=$arItem["PICTURE"]["SRC"]?>"  <?
					?>alt="<?=htmlspecialchars($arItem["~NAME"])?>" title="<?=htmlspecialchars($arItem["~NAME"])?>" <?
					?>onclick="document.getElementById('photo_<?=$arItem["ID"]?>').checked = !(document.getElementById('photo_<?=$arItem["ID"]?>').checked);" <?
					?>id="photo_img_<?=$arItem["ID"]?>" /><?
			endif;
		?></div><?
		endif;
	endforeach;
endif;

			?></div>
		</td></tr>
	</tbody>
	<tfoot>
		<tr><td class="table-controls">
			<input type="submit" name="name_submit" value="<?=GetMessage("P_SUBMIT");?>" />
			<input type="button" name="name_cancel" value="<?=GetMessage("P_CANCEL");?>" onclick="CheckFormEditIconCancel(this)" />
		</td></tr>
	</tfoot>
</table>
</form>
</div><?

if ($arParams["AJAX_CALL"] == "Y"):
	die();
else:
?><script>
function CheckFormEditIconCancel(pointer)
{
	if (pointer.form)
	{
		pointer.form.edit.value = 'cancel'; 
		pointer.form.submit();
	}
	return false;
}
function CheckFormEditIcon()
{
	return true;
}
</script><?
endif;
if ($arParams["BEHAVIOUR"] == "USER"):
?></div><?
endif;
?>