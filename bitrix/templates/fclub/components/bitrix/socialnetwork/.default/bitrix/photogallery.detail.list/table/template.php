<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/********************************************************************
				Input params
********************************************************************/
$temp = array("STRING" => preg_replace("/[^0-9]/is", "/", $arParams["THUMBS_SIZE"]));
list($temp["WIDTH"], $temp["HEIGHT"]) = explode("/", $temp["STRING"]);
$arParams["THUMBS_SIZE"] = (intVal($temp["WIDTH"]) > 0 ? intVal($temp["WIDTH"]) : 120);
if ($arParams["PICTURES_SIGHT"] != "standart" && intVal($arParams["PICTURES"][$arParams["PICTURES_SIGHT"]]["size"]) > 0)
	$arParams["THUMBS_SIZE"] = $arParams["PICTURES"][$arParams["PICTURES_SIGHT"]]["size"];

$arParams["SHOW_PAGE_NAVIGATION"] = (in_array($arParams["SHOW_PAGE_NAVIGATION"], array("none", "top", "bottom", "both")) ? 
		$arParams["SHOW_PAGE_NAVIGATION"] : "none");
$arParams["SHOW_CONTROLS"] = ($arParams["SHOW_CONTROLS"] == "Y" ? "Y" : "N");
$arParams["SHOW_RATING"] = ($arParams["SHOW_RATING"] == "Y" ? "Y" : "N");
$arParams["SHOW_SHOWS"] = ($arParams["SHOW_SHOWS"] == "Y" ? "Y" : "N");
$arParams["SHOW_COMMENTS"] = ($arParams["SHOW_COMMENTS"] == "Y" ? "Y" : "N");
$arParams["SHOW_TAGS"] = ($arParams["SHOW_TAGS"] == "Y" ? "Y" : "N");
/********************************************************************
				Input params
********************************************************************/
$arResult["ELEMENTS"]["MAX_HEIGHT"] = $arParams["THUMBS_SIZE"];

if ($arParams["BEHAVIOUR"] == "USER"):
?><div class="photo-user<?=($arResult["GALLERY"]["CREATED_BY"] == $GLOBALS["USER"]->GetId() && $GLOBALS["USER"]->IsAuthorized() ? 
	" photo-user-my" : "")?>"><?
endif;

if (!empty($arResult["ERROR_MESSAGE"])):
?><div class="photo-error"><?=ShowError($arResult["ERROR_MESSAGE"])?></div><?
endif;

if (!empty($arResult["ELEMENTS_LIST"]) && is_array($arResult["ELEMENTS_LIST"])):

?><div class="empty-clear"></div><?

if (($arParams["SHOW_PAGE_NAVIGATION"] == "top" || $arParams["SHOW_PAGE_NAVIGATION"] == "both") && !empty($arResult["NAV_STRING"])):
	?><div class="photo-navigation"><?=$arResult["NAV_STRING"]?></div><?
endif;

if ($arParams["SHOW_CONTROLS"] == "Y"):
	?><div class="photo-controls photo-view"><?
	if ($arResult["USER_HAVE_ACCESS"] == "Y"):
		?><a href="<?=$arResult["SLIDE_SHOW"]?>" class="photo-view slide-show"><?=GetMessage("P_SLIDE_SHOW")?></a><?
	endif;
	if (!empty($arParams["PICTURES"]))
	{
		$arRes = array_merge(
			array("standart" => array("title" => GetMessage("P_STANDARD"))),
			$arParams["PICTURES"]);
		?><span class="photo-view sights"><?=GetMessage("P_PICTURES_SIGHT")?>: <?
		?><select name="picture" onchange="ChangeText(this);" title="<?=GetMessage("P_PICTURES_SIGHT_TITLE")?>"><?
		foreach ($arRes as $key => $val):
			?><option value="<?=$key?>"<?=($key."" == $arParams["PICTURES_SIGHT"]."" ? " selected" : "")?>><?=$val["title"]?></option><?
		endforeach;
		?></select></span><?
	}
	?></div><?

	if ($arParams["DetailListViewMode"] == "edit" && $arParams["PERMISSION"] >= "W"):
	IncludeAJAX();
	?><div class="photo-controls">
		<a href="<?=$APPLICATION->GetCurPageParam("view_mode=view&".bitrix_sessid_get(), array("view_mode", "sessid"), false)?>"<?
			?> class="photo-action go-to-view"<?
			?> title="<?=GetMessage("P_VIEW_TITLE")?>"><?=GetMessage("P_VIEW")?></a>
		<a href="javascript:void(0);" onmousedown="Delete();" class="photo-action delete"><?=GetMessage("P_DELETE_SELECTED")?></a>
		<a href="javascript:void(0);" onmousedown="Move();" class="photo-action move"><?=GetMessage("P_MOVE_SELECTED")?></a>
		<div id="photo_detail_list_edit" class="photo-window-edit" style="display:none;">
			<table cellpadding="0" cellspacing="0" border="0" class="photo-popup">
				<thead>
					<tr>
						<td><?=GetMessage("P_ELEMENTS_MOVE")?></td>
					</tr>
				</thead>
				<tbody>
					<tr><td class="table-body">
						<div class="inner">
							<?=GetMessage("P_SELECT_ALBUM")?>
							<select id="TO_SECTION_ID" name="TO_SECTION_ID"><?
							foreach ($arResult["SECTIONS_LIST"] as $key => $val):
								?><option value="<?=$key?>" <?
									?> <?=((intVal($arParams["SECTION_ID"]) == intVal($key)) ? " selected='selected'" : "")?>><?=$val?></option><?
							endforeach;
							?></select>
						</div>
					</td></tr>
				</tbody>
				<tfoot>
					<tr><td class="table-controls">
						<input type="button" name="name_submit" value="<?=GetMessage("P_MOVE");?>" onclick="Move('send')" />
						<input type="button" name="name_cancel" value="<?=GetMessage("P_CANCEL");?>" onclick="CancelSubmit(this)" />
					</td></tr>
				</tfoot>
			</table>
		</div>
	</div>
	<div class="empty-clear"></div>
	
	<form action="<?=POST_FORM_ACTION_URI?>" method="post" id="photoForm">
		<input type="hidden" name="sessid" value="<?=bitrix_sessid()?>" />
		<input type="hidden" name="detail_list_edit" value="Y" />
		<input type="hidden" name="ACTION" id="ACTION" value="Y" />
		<input type="hidden" name="SECTION_ID" value="<?=$arParams["SECTION_ID"]?>" />
		<input type="hidden" name="IBLOCK_ID" value="<?=$arParams["IBLOCK_ID"]?>" />
		<input type="hidden" name="REDIRECT_URL" value="<?=htmlspecialchars($APPLICATION->GetCurPageParam("", array(), false))?>" />
		<div class="photo-action select-all"><input type="checkbox" value="Y" id="select_all1" name="select_all" onclick="for (var ii = 0; ii < this.form.elements.length; ii++){if (this.form.elements[ii].name == 'items[]' || this.form.elements[ii].name == 'select_all'){this.form.elements[ii].checked = this.checked;}}" /> <label for="select_all1"><?=GetMessage("P_SELECT_ALL")?></label></div><?
	elseif ($arParams["PERMISSION"] >= "W"):
	?><div class="photo-controls"><?
		?><a href="<?=$APPLICATION->GetCurPageParam("view_mode=edit&".bitrix_sessid_get(), array("view_mode", "sessid"), false)?>" <?
		?> title="<?=GetMessage("P_EDIT_TITLE")?>"<?
		?> class="photo-action go-to-edit"><?=GetMessage("P_EDIT")?></a><?
	?></div><?
	endif;
endif;

$counter = 0;

?><div class="empty-clear"></div><?

foreach ($arResult["ELEMENTS_LIST"]	as $key => $arItem):

	if (!is_array($arItem))
		continue;
	$counter++;
	if ($arParams["CELL_COUNT"] > 0 && $counter > $arParams["CELL_COUNT"]):
		?><div class="empty-clear"></div><?
		$counter = 1;
	endif;
	
	?><table cellpadding="0" cellspacing="0" border="0" class="result" style="width:<?=intVal($arParams["THUMBS_SIZE"] + 36/* 3*2 + 10*2 + 5*2*/)?>px; height:<?=intVal($arParams["THUMBS_SIZE"] + 26 + 50)?>px;" onmouseover="ShowDescription('<?=$arItem["ID"]?>')" onmouseout="HideDescription('<?=$arItem["ID"]?>')" <?
		if ($arParams["DetailListViewMode"] == "edit")
		{
			?> onclick="document.getElementById('items_<?=$arItem["ID"]?>').checked = !document.getElementById('items_<?=$arItem["ID"]?>').checked; document.getElementById('select_all1').checked=false;" style="cursor:pointer; cursor:hand;"<?
		}
		?>><tr><th class="result_image" align="center"><?
		
		if($arResult["USER_HAVE_ACCESS"] == "Y" && ($arParams["SHOW_CONTROLS"] == "Y" && $arParams["DetailListViewMode"] == "edit")):
			?><div style="position:relative;"><?
		endif;
			?><table class="shadow" cellpadding="0" cellspacing="0" border="0" width="0"><?
				?><tr valign="middle"><td colspan="2" class="photo-image" align="center" <?
					?>style="width:<?=($arParams["THUMBS_SIZE"]+14)?>px; height:<?=($arParams["THUMBS_SIZE"]+14)?>px;"><?
	if(is_array($arItem["PICTURE"])):
		if($arResult["USER_HAVE_ACCESS"] == "Y"):
			if ($arParams["SHOW_CONTROLS"] == "Y" && $arParams["DetailListViewMode"] == "edit"):
				?><input type="checkbox" value="<?=$arItem["ID"]?>" name="items[]" <?=(($arResult["bVarsFromForm"] == "Y" && in_array($arItem["ID"], $_REQUEST["items"])) ? "checked" : "")?> id="items_<?=$arItem["ID"]?>" /><?
				?><?=CFile::ShowImage($arItem["PICTURE"]["SRC"], $arParams["THUMBS_SIZE"], $arParams["THUMBS_SIZE"], "border=\"0\" vspace=\"0\" hspace=\"0\" alt=\"".htmlspecialchars($arItem["CODE"])."\" title=\"".htmlspecialchars($arItem["~NAME"])."\"");?><?
			else: 
				?><a href="<?=$arItem["URL"]?>"><?
					?><?=CFile::ShowImage($arItem["PICTURE"]["SRC"], $arParams["THUMBS_SIZE"], $arParams["THUMBS_SIZE"], "border=\"0\" vspace=\"0\" hspace=\"0\" alt=\"".htmlspecialchars($arItem["CODE"])."\" title=\"".htmlspecialchars($arItem["~NAME"])."\"");?><?
				?></a><?
			endif;
		else:
		?><?=CFile::ShowImage($arItem["PICTURE"]["SRC"], $arParams["THUMBS_SIZE"], $arParams["THUMBS_SIZE"], "border=\"0\" vspace=\"0\" hspace=\"0\" alt=\"".htmlspecialchars($arItem["CODE"])."\" title=\"".htmlspecialchars($arItem["~NAME"])."\"");?><?
		endif;
	else:
	?><div style="width:<?=$arParams["THUMBS_SIZE"]?>px; height:<?=$arParams["THUMBS_SIZE"]?>px;"></div><?
	endif;
	
				?></td></tr><?
				?><tr class="b"><?
					?><td class="l"><div class="empty"></div></td><?
					?><td class="r"><div class="empty"></div></td><?
				?></tr><?
			?></table><?
		if($arResult["USER_HAVE_ACCESS"] == "Y" && ($arParams["SHOW_CONTROLS"] == "Y" && $arParams["DetailListViewMode"] == "edit")):
			?></div><?
		endif;
			
		?></th></tr><?
		?><tr><td class="result_text"><?
		
		if (!empty($arItem) && $arParams["DetailListViewMode"] != "edit"):
		?><div style="position:relative;"><?
			?><div class="photo-image-inner" id="item_<?=$arItem["ID"]?>"<?
				if (PhotoGetBrowser() == "opera"):
					?> style="overflow:auto; height:150px;"<?
				endif;
				?>><?
				if ($arResult["USER_HAVE_ACCESS"] == "Y"):
				?><div class="photo-title"><a href="<?=$arItem["URL"]?>"><?=$arItem["NAME"]?></a></div><?
				?><div class="photo-controls photo-view"><a class="photo-view original" href="<?=$arItem["SLIDE_SHOW_URL"]?>" <?
					?>title="<?=GetMessage("P_FULL_SCREEN_TITLE")?>"><?=GetMessage("P_FULL_SCREEN")?></a></div><?
				else:
				?><div class="photo-title"><?=$arItem["NAME"]?></div><?
				endif;
			
				?><div class="photo-date"><?=$arItem["DATE_CREATE"]?></div><?
			
		if ($arParams["SHOW_TAGS"] == "Y"):
			?><div class="photo-tags"><?
			if (!empty($arItem["TAGS_LIST"]))
			{
				$first = true;
				foreach ($arItem["TAGS_LIST"] as $tags):
					if (!$first)
					{
						?>, <?
					}
					?><a href="<?=$tags["TAGS_URL"]?>"><?=$tags["TAGS_NAME"]?></a><?
					$first = false;
				endforeach;
			}
				?></div><?
		endif;
		
		if ($arParams["SHOW_RATING"] == "Y"):
		?><div class="photo-rating"><table border="0" cellspacing="0" cellpadding="0"><tr><?
			foreach($arResult["VOTE_NAMES"] as $i=>$name):
				if(round($arItem["PROPERTIES"]["rating"]["VALUE"]) > $i):
					?><td><div class="star-voted" title="<?=$name?>"></div></td><?
				else:
					?><td><div class="star-empty" title="<?=$name?>"></div></td><?
				endif;
			endforeach;
			
			if($arItem["PROPERTIES"]["vote_count"]["VALUE"]):
				?><td><?=GetMessage("T_IBLOCK_VOTE_RESULTS", array("#VOTES#"=>$arItem["PROPERTIES"]["vote_count"]["VALUE"] , "#RATING#"=>$arItem["PROPERTIES"]["rating"]["VALUE"]))?></td><?
			else:
				?><td><?=GetMessage("T_IBLOCK_VOTE_NO_RESULTS")?></td><?
			endif;
		?></tr></table></div><?
		endif;
		
		if ($arParams["SHOW_SHOWS"] == "Y"):
		?><div class="photo-shows"><?=GetMessage("P_SHOWS")?>: <?=intVal($arItem["SHOW_COUNTER"])?></div><?
		endif;
		
		if ($arParams["SHOW_COMMENTS"] == "Y"):
		?><div class="photo-shows"><?=GetMessage("P_COMMENTS")?>: <?=intVal($arParams["COMMENTS_TYPE"] == "FORUM" ? $arItem["PROPERTIES"]["FORUM_MESSAGE_CNT"]["VALUE"] : $arItem["PROPERTIES"]["BLOG_COMMENTS_CNT"]["VALUE"])?></div><?
		endif;
		
				?><div class="photo-description"><?=$arItem["PREVIEW_TEXT"]?></div><?
				
			?></div><?
		?></div><?
		endif;
		?><div class="photo-title" style="width:<?=intVal($arParams["THUMBS_SIZE"] + 14)?>px;"><?=$arItem["NAME"]?></div><?
		
		?></td></tr><?
	?></table><?
endforeach;

?><div class="empty-clear"></div><?

	if ($arParams["SHOW_CONTROLS"] == "Y" && $arParams["PERMISSION"] >= "W"):
		if ($arParams["DetailListViewMode"] == "edit"):
			?></form><?
		endif;
	?><script type="text/javascript">
	function Delete()
	{
		var form = document.getElementById('photoForm');
		var bNotEmpty = false;
		if (form && form.elements["items[]"])
		{
			if (!form.elements["items[]"].length && form.elements["items[]"].checked)
			{
				bNotEmpty = true;
			}
			else if (form.elements["items[]"].length > 0)
			{
				for (var ii = 0; ii < form.elements["items[]"].length; ii++)
				{
					if (form.elements["items[]"][ii].checked == true)
					{
						bNotEmpty = true;
						break;
					}
				}
			}
			
			if (bNotEmpty)
			{
				if (confirm('<?=GetMessage("P_DELETE_CONFIRM")?>'))
				{
					form.elements['ACTION'].value = 'drop'; 
					form.submit();
				}
			}
		}
	}
	function Move(action)
	{
		action = (action == 'send' ? 'send' : 'show');
		var form = document.getElementById('photoForm');
		var bNotEmpty = false;
		if (!(form && form.elements["items[]"]))
			return false;
		if (!form.elements["items[]"].length && form.elements["items[]"].checked)
		{
			bNotEmpty = true;
		}
		else if (form.elements["items[]"].length > 0)
		{
			for (var ii = 0; ii < form.elements["items[]"].length; ii++)
			{
				if (form.elements["items[]"][ii].checked == true)
				{
					bNotEmpty = true;
					break;
				}
			}
		}
		
		if (!bNotEmpty)
			return false;
	
		if (action == 'show')
		{
			var div_ = document.getElementById('photo_detail_list_edit').cloneNode(true);
			div_.style.display = 'block';
			
			var div = document.createElement("DIV");
			div.id = "photo_section_edit";
			div.style.visible = 'hidden';
			div.className = "photo-popup";
			div.style.position = 'absolute';
			div.appendChild(div_);
			form.appendChild(div);
			PhotoMenu.PopupShow(div);
			return false;
		}
		form.elements['ACTION'].value = 'move'; 
		form.submit();
		return false;
	}
	</script><?
	endif;

	if (($arParams["SHOW_PAGE_NAVIGATION"] == "bottom" || $arParams["SHOW_PAGE_NAVIGATION"] == "both") && !empty($arResult["NAV_STRING"])):
		?><div class="photo-navigation"><?=$arResult["NAV_STRING"]?></div><?
	endif;
endif;

if (!empty($arResult["ERROR_MESSAGE"])):
?><div class="photo-error"><?=ShowError($arResult["ERROR_MESSAGE"])?></div><?
endif;
?>
<script type="text/javascript">
function ChangeText(obj)
{
	if (typeof obj != "object")
		return;
	if (<?=intVal($GLOBALS["USER"]->GetId())?> > 0)
	{
		var TID = CPHttpRequest.InitThread(); 
		CPHttpRequest.SetAction(TID, function(data){window.location.reload(true);})
		CPHttpRequest.Send(TID, '/bitrix/components/bitrix/photogallery.detail.list/user_settings.php', {"picture_sight":obj.value, "sessid":'<?=bitrix_sessid()?>'});
	}
	else
	{
		jsUtils.Redirect([], '<?=CUtil::addslashes($GLOBALS["APPLICATION"]->GetCurPageParam("PICTURES_SIGHT=#pictures_sight#", array("PICTURES_SIGHT", "sessid"), false))?>'.replace('#pictures_sight#', obj.value));		
	}
}
function HideDescription(id)
{
	if (document.getElementById('item_' + id))
		document.getElementById('item_' + id).style.display = 'none';
}
function ShowDescription(id)
{
	if (document.getElementById('item_' + id))
		document.getElementById('item_' + id).style.display = 'block';
}
</script>
<div class="empty-clear"></div><?
if ($arParams["BEHAVIOUR"] == "USER"):
?></div><?
endif;
?>