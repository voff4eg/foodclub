<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
// *****************************************************************************************
if (!empty($arResult["ERROR_MESSAGE"]) || !empty($arResult["OK_MESSAGE"])):
?><?=ShowError($arResult["ERROR_MESSAGE"])?><?
?><?=ShowNote($arResult["OK_MESSAGE"])?><?
?><br/><?
endif;
$arResult["FOLDERS"] = array();
for ($ii = 1; $ii <= $arResult["SystemFolder"]; $ii++)
{
	if ($arResult["version"] == 2 && $ii==2)
		continue;
	if ($ii == $arParams["FID"])
		continue;
	$arResult["FOLDERS"][] = array("ID" => $ii, "TITLE" => GetMessage("PM_FOLDER_ID_".$ii));
}
if (($arResult["UserFolder"] != "N") && is_array($arResult["UserFolder"]) && (!empty($arResult["UserFolder"])))
{
	foreach ($arResult["UserFolder"] as $res)
	{
		if ($res["ID"] = $arParams["FID"])
			continue;
		$arResult["FOLDERS"][] = array("ID" => $res["ID"], "TITLE" => $res["TITLE"]);
	}
}

?><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td align="right">
		<div class="out"><div class="in" style="width:<?=$arResult["count"]?>%">&nbsp;</div></div>
		<div class="out1"><div class="in1"><?=GetMessage("PM_POST_FULLY")." ".$arResult["count"]?>%</div></div>
	</td></tr></table><?

	?><div class="forum-br"></div><?
	?><div class="forum-title"><b><a href="<?=$arResult["pm_list"]?>"><?=$arResult["FolderName"]?></a></b></div><?
	?><div class="forum-br"></div><?
?><table class="forum-main forum-pm">
	<thead>
		<tr>
			<th class="left" width="1%"><?=GetMessage("PM_FROM")?>:</th>
			<td width="99%"><a href="<?=$arResult["MESSAGE"]["AUTHOR_LINK"]?>"><?=$arResult["MESSAGE"]["AUTHOR_NAME"]?></a></td>
		</tr>
		<tr>
			<th class="left"><?=GetMessage("PM_TO")?>:</th>
			<td><a href="<?=$arResult["MESSAGE"]["RECIPIENT_LINK"]?>"><?=$arResult["MESSAGE"]["RECIPIENT_NAME"]?></a></td>
		</tr>
		<tr>
			<th class="left"><?=GetMessage("PM_DATA")?>:</th>
			<td><?=$arResult["MESSAGE"]["POST_DATE"]?></td>
		</tr>
		<tr>
			<th class="left"><?=GetMessage("PM_HEAD_SUBJ")?></th>
			<td><?=$arResult["MESSAGE"]["POST_SUBJ"];?></td>
		</tr>
	</thead>
	<tbody>
	<tr>
		<td colspan="2"><?=$arResult["MESSAGE"]["POST_MESSAGE"]?>
		<?if (($arResult["MESSAGE"]["REQUEST_IS_READ"] == "Y") && ($arParams["version"]==2)):?>
		<div class="forum-pm-notification">
			<?=GetMessage("PM_REQUEST_NOTIF")?>
			<form action="<?=$APPLICATION->GetCurPageParam()?>" method="get" name="PMESSAGE" class="forum-form" >
				<input type="hidden" name="FID" value="<?=$arResult["FID"]?>" />
				<input type="hidden" name="MID" value="<?=$arResult["MID"]?>" />
				<input type="hidden" name="PAGE_NAME" value="pm_read" />
				<input type="hidden" name="action" value="send_notification" />
				<?=$arResult["sessid"]?>
				<input type="submit" class="forum-mess-button" value="<?=GetMessage("PM_SEND_NOTIF")?>" />
			</form>
		</div>
		<?endif;?>
		</td>
	</tr>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="2">
<table class="clear" cellpadding="0" cellspacing="0" border="0" width="100%"><tr valign="bottom">
	<td>
		<div class="forum-control actions">
			<a href="<?=ForumAddPageParams($arResult["pm_reply"])?>" class="forum-action pm-reply"><?=GetMessage("PM_ACT_REPLY")?></a>
			<a href="<?=ForumAddPageParams($arResult["pm_edit"])?>" class="forum-action pm-edit"><?=GetMessage("PM_ACT_EDIT")?></a>
			<a href="<?=$APPLICATION->GetCurPageParam(bitrix_sessid_get()."&action=delete", array("sessid", "action"))?>" <?
				?>class="forum-action pm-delete"><?=GetMessage("PM_ACT_DELETE")?></a>
		</div>
		<div class="forum-control actions"  style="float:left;">
		<form action="<?=$APPLICATION->GetCurPageParam()?>" method="get" class="forum-form" >
			<input type="hidden" name="FID" value="<?=$arResult["FID"]?>" />
			<input type="hidden" name="MID" value="<?=$arResult["MID"]?>" />
			<input type="hidden" name="PAGE_NAME" value="pm_read" />
			<input type="hidden" name="action" value="none" />
			<?=$arResult["sessid"]?>
			
			<div class="forum-action pm-copy">
				<?=GetMessage("PM_ACT_COPY")?> <?=GetMessage("PM_IN")?>:<br />
				<select name="folder_id">
				<?
				foreach ($arResult["FOLDERS"] as $res)
				{
					?><option value="<?=$res["ID"]?>" <?=(($_REQUEST["action"] == "copy" && $res["ID"] == $_REQUEST["folder_id"]) 
						? " selected='selected'" : "")?>><?=$res["TITLE"]?></option><?
				}
				?></select><?
				?><input type="submit" value="<?=GetMessage("PM_OK")?>" onclick="this.form.action.value='copy';" />
			</div>
			
			<div class="forum-action move">
				<?=GetMessage("PM_ACT_MOVE")?> <?=GetMessage("PM_IN")?>:<br />
				<select name="folder_id">
				<?
				foreach ($arResult["FOLDERS"] as $res)
				{
					?><option value="<?=$res["ID"]?>" <?=(($_REQUEST["action"] == "move" && $res["ID"] == $_REQUEST["folder_id"]) 
						? " selected='selected'" : "")?>><?=$res["TITLE"]?></option><?
				}
				?></select><?
				?><input type="submit" value="<?=GetMessage("PM_OK")?>" onclick="this.form.action.value='move';" />
			</div>
		</form>
		</div>
	</td>
	<td align="right" class="navigation">
		<div class="navigation" style="width:100%; text-align:right;clear:both;">
			<span class="prev"><?
if (!empty($arResult["MESSAGE_PREV"])):
		?><a href="<?=$arResult["MESSAGE_PREV"]["MESSAGE_LINK"]?>"><?=GetMessage("P_PREV")?></a><?
else :
		?><?=GetMessage("P_PREV")?><?
endif;
			?></span>
			<span class="curr"></span>
			<span class="next"><?
if (!empty($arResult["MESSAGE_NEXT"])):
		?><a href="<?=$arResult["MESSAGE_NEXT"]["MESSAGE_LINK"]?>"><?=GetMessage("P_NEXT")?></a><?
else :
		?><?=GetMessage("P_NEXT")?><?
endif;
			?></span>
		</div>
	</td>
</tr></table>

			</td>
		</tr>
	</tfoot>
</table>