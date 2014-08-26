<?
##############################################
# Bitrix Site Manager                        #
# Copyright (c) 2002-2007 Bitrix             #
# http://www.bitrixsoft.com                  #
# mailto:admin@bitrixsoft.com                #
##############################################

require_once(dirname(__FILE__)."/../include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/prolog_user.php");
define("HELP_FILE", "users/user_edit.php");
$strRedirect_admin = BX_ROOT."/admin/user_admin.php?lang=".LANG;
$strRedirect = BX_ROOT."/admin/user_edit.php?lang=".LANG;

if (!($USER->CanDoOperation('view_own_profile') || $USER->CanDoOperation('edit_own_profile') || $USER->CanDoOperation('view_subordinate_users') || $USER->CanDoOperation('view_all_users')))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$PROPERTY_ID = "USER";

if($USER->CanDoOperation('edit_own_profile') && !($USER->CanDoOperation('view_subordinate_users') || $USER->CanDoOperation('view_all_users') || $USER->CanDoOperation('edit_all_users') || $USER->CanDoOperation('edit_subordinate_users')))
{
	$ID = $USER->GetID();
	if (intval($ID)<=0) $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
	$COPY_ID = 0;
}
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/admin/user_edit.php");

/***************************************************************************
					��������� GET | POST
****************************************************************************/
$ID=IntVal($ID);
$COPY_ID=intval($COPY_ID);

$uid = $USER->GetID();
$arUserGroups = CUser::GetUserGroup($uid);

if($COPY_ID<=0)
{
	$arUserGroups = CUser::GetUserGroup($ID);
}
else
{
	$arUserGroups = Array();
	$ID = $COPY_ID;
}

$selfEdit = ($USER->CanDoOperation('edit_own_profile') && $ID == $uid);

if($USER->CanDoOperation('edit_subordinate_users') && !$USER->CanDoOperation('edit_all_users'))
{
	$arUserSubordinateGroups = Array();
	$arUserGroups_u = CUser::GetUserGroup($uid);
	for ($j = 0,$len = count($arUserGroups_u); $j < $len; $j++)
	{
		$arSubordinateGroups = CGroup::GetSubordinateGroups($arUserGroups_u[$j]);
		$arUserSubordinateGroups = array_merge ($arUserSubordinateGroups, $arSubordinateGroups);
	}
	$arUserSubordinateGroups = array_unique($arUserSubordinateGroups);

	if (count(array_diff($arUserGroups, $arUserSubordinateGroups)) > 0 && !$selfEdit)
		LocalRedirect(BX_ROOT."/admin/user_admin.php?lang=".LANG);
}

$editable = ($USER->IsAdmin() ||
	$selfEdit ||
	($USER->CanDoOperation('edit_subordinate_users') && !in_array(1, $arUserGroups)) ||
	($USER->CanDoOperation('edit_all_users') && !in_array(1, $arUserGroups))
);


$canSelfEdit = true;
if($ID==$uid && !($USER->CanDoOperation('edit_php') || ($USER->CanDoOperation('edit_all_users') && $USER->CanDoOperation('edit_groups'))))
	$canSelfEdit = false;

$showGroupTabs = (($USER->CanDoOperation('view_subordinate_users') || $USER->CanDoOperation('view_all_users')) && $canSelfEdit);

$strError="";
$message=null;

$aTabs = Array();
$aTabs[] = array("DIV" => "edit1", "TAB" => GetMessage("MAIN_USER_TAB1"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("MAIN_USER_TAB1_TITLE"));

if($showGroupTabs)
	$aTabs[] = array("DIV" => "edit2", "TAB" => GetMessage("GROUPS"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("MAIN_USER_TAB2_TITLE"));
$aTabs[] = array("DIV" => "edit3", "TAB" => GetMessage("USER_PERSONAL_INFO"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("USER_PERSONAL_INFO"));
$aTabs[] = array("DIV" => "edit4", "TAB" => GetMessage("MAIN_USER_TAB4"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("USER_WORK_INFO"));

$i = 1;
$db_opt_res = CModule::GetList();
while ($opt_res = $db_opt_res->Fetch())
{
	$mdir = $opt_res["ID"];
	if (file_exists($DOCUMENT_ROOT.BX_ROOT."/modules/".$mdir) && is_dir($DOCUMENT_ROOT.BX_ROOT."/modules/".$mdir))
	{
		$ofile = $DOCUMENT_ROOT.BX_ROOT."/modules/".$mdir."/options_user_settings.php";
		if (file_exists($ofile))
		{
			//$MODULE_RIGHT = $APPLICATION->GetGroupRight($mdir);
			//if ($MODULE_RIGHT>="R")
			//{
				include(GetLangFileName($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$mdir."/lang/", "/options_user_settings.php"));
				$aTabs[] = array("DIV" => "edit".($i+4), "TAB" => GetMessage($mdir."_TAB"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage($mdir."_TAB_TITLE"));
				$i++;
			//}
		}
	}
}

if(($editable && $ID!=$USER->GetID()) || $USER->IsAdmin())
	$aTabs[] = array("DIV" => "edit".($i+4), "TAB" => GetMessage("MAIN_USER_TAB5"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("USER_ADMIN_NOTES"));

//Add user fields tab only when there is fields defined or user has rights for adding new field
if(
	(count($USER_FIELD_MANAGER->GetUserFields($PROPERTY_ID)) > 0) ||
	($USER_FIELD_MANAGER->GetRights($PROPERTY_ID) >= "W")
)
{
	$aTabs[] = $USER_FIELD_MANAGER->EditFormTab($PROPERTY_ID);
}

$tabControl = new CAdminTabControl("tabControl", $aTabs);

$strError="";
if($REQUEST_METHOD=="POST" && (strlen($save)>0 || strlen($apply)>0 || $Update=="Y") && $editable && check_bitrix_sessid())
{
	$user = new CUser;

	if ($ID=="1" && $COPY_ID<=0)
		$ACTIVE = "Y";

	$arPERSONAL_PHOTO = $HTTP_POST_FILES["PERSONAL_PHOTO"];
	$arWORK_LOGO = $HTTP_POST_FILES["WORK_LOGO"];

	$arUser = false;
	if($ID>0)
	{
		$dbUser = CUser::GetById($ID);
		$arUser = $dbUser->Fetch();
	}

	if($arUser)
	{
		$arPERSONAL_PHOTO["old_file"] = $arUser["PERSONAL_PHOTO"];
		$arPERSONAL_PHOTO["del"] = $PERSONAL_PHOTO_del;

		$arWORK_LOGO["old_file"] = $arUser["WORK_LOGO"];
		$arWORK_LOGO["del"] = $WORK_LOGO_del;
	}

	$arFields = Array(
		"NAME"					=> $NAME,
		"LAST_NAME"				=> $LAST_NAME,
		"SECOND_NAME"			=> $SECOND_NAME,
		"EMAIL"					=> $EMAIL,
		"LOGIN"					=> $LOGIN,
		"PERSONAL_PROFESSION"	=> $PERSONAL_PROFESSION,
		"PERSONAL_WWW"			=> $PERSONAL_WWW,
		"PERSONAL_ICQ"			=> $PERSONAL_ICQ,
		"PERSONAL_GENDER"		=> $PERSONAL_GENDER,
		"PERSONAL_BIRTHDAY"		=> $PERSONAL_BIRTHDAY,
		"PERSONAL_PHOTO"		=> $arPERSONAL_PHOTO,
		"PERSONAL_PHONE"		=> $PERSONAL_PHONE,
		"PERSONAL_FAX"			=> $PERSONAL_FAX,
		"PERSONAL_MOBILE"		=> $PERSONAL_MOBILE,
		"PERSONAL_PAGER"		=> $PERSONAL_PAGER,
		"PERSONAL_STREET"		=> $PERSONAL_STREET,
		"PERSONAL_MAILBOX"		=> $PERSONAL_MAILBOX,
		"PERSONAL_CITY"			=> $PERSONAL_CITY,
		"PERSONAL_STATE"		=> $PERSONAL_STATE,
		"PERSONAL_ZIP"			=> $PERSONAL_ZIP,
		"PERSONAL_COUNTRY"		=> $PERSONAL_COUNTRY,
		"PERSONAL_NOTES"		=> $PERSONAL_NOTES,
		"WORK_COMPANY"			=> $WORK_COMPANY,
		"WORK_DEPARTMENT"		=> $WORK_DEPARTMENT,
		"WORK_POSITION"			=> $WORK_POSITION,
		"WORK_WWW"				=> $WORK_WWW,
		"WORK_PHONE"			=> $WORK_PHONE,
		"WORK_FAX"				=> $WORK_FAX,
		"WORK_PAGER"			=> $WORK_PAGER,
		"WORK_STREET"			=> $WORK_STREET,
		"WORK_MAILBOX"			=> $WORK_MAILBOX,
		"WORK_CITY"				=> $WORK_CITY,
		"WORK_STATE"			=> $WORK_STATE,
		"WORK_ZIP"				=> $WORK_ZIP,
		"WORK_COUNTRY"			=> $WORK_COUNTRY,
		"WORK_PROFILE"			=> $WORK_PROFILE,
		"WORK_LOGO"				=> $arWORK_LOGO,
		"WORK_NOTES"			=> $WORK_NOTES
		);

	if($USER->CanDoOperation('edit_all_users') || $USER->CanDoOperation('edit_subordinate_users'))
	{
		if(strlen($LID)>0)
			$arFields["LID"] = $LID;

		if(is_set($_REQUEST, 'EXTERNAL_AUTH_ID'))
			$arFields['EXTERNAL_AUTH_ID'] = $EXTERNAL_AUTH_ID;

		$arFields["ACTIVE"]=$ACTIVE;

		if($showGroupTabs)
		{
			$GROUP_ID_NUMBER = IntVal($GROUP_ID_NUMBER);
			$GROUP_ID = array();
			$ind = -1;
			for ($i = 0; $i <= $GROUP_ID_NUMBER; $i++)
			{
				if (${"GROUP_ID_ACT_".$i} == "Y")
				{
					$gr_id = IntVal(${"GROUP_ID_".$i});

					if($gr_id == 1 && !$USER->IsAdmin())
						continue;

					if ($USER->CanDoOperation('edit_subordinate_users') && !$USER->CanDoOperation('edit_all_users') && !in_array($gr_id, $arUserSubordinateGroups))
						continue;

					$ind++;
					$GROUP_ID[$ind]["GROUP_ID"] = $gr_id;
					$GROUP_ID[$ind]["DATE_ACTIVE_FROM"] = ${"GROUP_ID_FROM_".$i};
					$GROUP_ID[$ind]["DATE_ACTIVE_TO"] = ${"GROUP_ID_TO_".$i};
				}
			}

			if ($ID == "1" && $COPY_ID<=0)
			{
				$ind++;
				$GROUP_ID[$ind]["GROUP_ID"] = 1;
				$GROUP_ID[$ind]["DATE_ACTIVE_FROM"] = false;
				$GROUP_ID[$ind]["DATE_ACTIVE_TO"] = false;
			}

			$arFields["GROUP_ID"]=$GROUP_ID;
		}

		if (($editable && $ID!=$USER->GetID()) || $USER->IsAdmin())
			$arFields["ADMIN_NOTES"]=$ADMIN_NOTES;
	}

	if(strlen($NEW_PASSWORD)>0)
	{
		$arFields["PASSWORD"]=$NEW_PASSWORD;
		$arFields["CONFIRM_PASSWORD"]=$NEW_PASSWORD_CONFIRM;
	}


	$USER_FIELD_MANAGER->EditFormAddFields($PROPERTY_ID, $arFields);
	if($ID>0 && $COPY_ID<=0)
	{
		$res = $user->Update($ID, $arFields, true);
	}
	elseif($USER->CanDoOperation('edit_all_users') || $USER->CanDoOperation('edit_subordinate_users'))
	{
		$ID = $user->Add($arFields);
		$res = ($ID>0);
		$new="Y";
	}

	$strError .= $user->LAST_ERROR;
	if ($GLOBALS['APPLICATION']->GetException())
	{
		$err = $GLOBALS['APPLICATION']->GetException();
		$strError .= $err->GetString();
		$GLOBALS['APPLICATION']->ResetException();
	}

	if(strlen($strError)<=0 && $ID>0)
	{
		if (is_array($profile_module_id) && count($profile_module_id)>0)
		{
			$db_opt_res = CModule::GetList();
			while ($opt_res = $db_opt_res->Fetch())
			{
				if (in_array($opt_res["ID"],$profile_module_id))
				{
					$mdir = $opt_res["ID"];
					if (file_exists($DOCUMENT_ROOT.BX_ROOT."/modules/".$mdir) && is_dir($DOCUMENT_ROOT.BX_ROOT."/modules/".$mdir))
					{
						$ofile = $DOCUMENT_ROOT.BX_ROOT."/modules/".$mdir."/options_user_settings_set.php";
						if (file_exists($ofile))
						{
							$MODULE_RIGHT = $APPLICATION->GetGroupRight($mdir);
							if ($MODULE_RIGHT>="R")
							{
								include($ofile);
								$res = $res && ${$mdir."_res"};
								if (!${$mdir."_res"}) $strError .= ${$mdir."WarningTmp"};
							}
						}
					}
				}
			}
		}


		if (strlen($strError)<=0)
		{
			if($user_info_event=="Y")
			{
	        		if(!defined("ADMIN_SECTION") || ADMIN_SECTION !== true || strlen($user_info_event_lang)<=0)
	        			$user_info_event_lang = LANG;

				if($new=="Y")
					$user->SendUserInfo($ID, $LID, GetMessage("ACCOUNT_INSERT"));
				else
					$user->SendUserInfo($ID, $LID, GetMessage("ACCOUNT_UPDATE"));
			}

			if($USER->CanDoOperation('edit_all_users') || $USER->CanDoOperation('edit_subordinate_users') || ($USER->CanDoOperation('edit_own_profile') && $ID==$uid))
			{
				if(strlen($save)>0)
					LocalRedirect($strRedirect_admin);
				elseif(strlen($apply)>0)
					LocalRedirect($strRedirect."&ID=".$ID."&".$tabControl->ActiveTabParam());
			}
			elseif($new=="Y")
				LocalRedirect($strRedirect."&ID=".$ID."&".$tabControl->ActiveTabParam());
		}
	}
}

$str_GROUP_ID = array();

$user = CUser::GetByID($ID);
if(!$user->ExtractFields("str_"))
{
	$ID=0;
	$str_ACTIVE="Y";
}
else
{
	$dbUserGroup = CUser::GetUserGroupList($ID);
	while ($arUserGroup = $dbUserGroup->Fetch())
	{
		$str_GROUP_ID[IntVal($arUserGroup["GROUP_ID"])]["DATE_ACTIVE_FROM"] = $arUserGroup["DATE_ACTIVE_FROM"];
		$str_GROUP_ID[IntVal($arUserGroup["GROUP_ID"])]["DATE_ACTIVE_TO"] = $arUserGroup["DATE_ACTIVE_TO"];
	}
}

if(strlen($strError)>0)
{
	$save_PERSONAL_PHOTO = $str_PERSONAL_PHOTO;
	$save_WORK_LOGO = $str_WORK_LOGO;

	$DB->InitTableVarsForEdit("b_user", "", "str_");

	$str_PERSONAL_PHOTO = $save_PERSONAL_PHOTO;
	$str_WORK_LOGO = $save_WORK_LOGO;

	$GROUP_ID_NUMBER = IntVal($GROUP_ID_NUMBER);
	$str_GROUP_ID = array();
	for ($i = 0; $i <= $GROUP_ID_NUMBER; $i++)
	{
		if (${"GROUP_ID_ACT_".$i} == "Y")
		{
			$str_GROUP_ID[IntVal(${"GROUP_ID_".$i})]["DATE_ACTIVE_FROM"] = ${"GROUP_ID_FROM_".$i};
			$str_GROUP_ID[IntVal(${"GROUP_ID_".$i})]["DATE_ACTIVE_TO"] = ${"GROUP_ID_TO_".$i};
		}
	}
}


if($ID>0 && $COPY_ID<=0)
	$APPLICATION->SetTitle(GetMessage("EDIT_USER_TITLE", array("#ID#"=>$ID)));
else
	$APPLICATION->SetTitle(GetMessage("NEW_USER_TITLE"));

require_once ($DOCUMENT_ROOT.BX_ROOT."/modules/main/include/prolog_admin_after.php");

$aMenu = array();
if($USER->CanDoOperation('view_all_users'))
{
	$aMenu[] = array(
		"TEXT"	=> GetMessage("RECORD_LIST"),
		"LINK"	=> "/bitrix/admin/user_admin.php?lang=".LANGUAGE_ID."&set_default=Y",
		"ICON"	=> "btn_list",
		"TITLE"	=> GetMessage("RECORD_LIST_TITLE"),
	);
}

if($USER->CanDoOperation('edit_all_users') || $USER->CanDoOperation('edit_subordinate_users'))
{
	if ($ID>0 && $COPY_ID<=0)
	{
		$aMenu[] = array("SEPARATOR"=>"Y");
		$aMenu[] = array(
			"TEXT"	=> GetMessage("MAIN_NEW_RECORD"),
			"LINK"	=> "/bitrix/admin/user_edit.php?lang=".LANGUAGE_ID,
			"ICON"	=> "btn_new",
			"TITLE"	=> GetMessage("MAIN_NEW_RECORD_TITLE"),
		);
		$aMenu[] = array(
			"TEXT"	=> GetMessage("MAIN_COPY_RECORD"),
			"LINK"	=> "/bitrix/admin/user_edit.php?lang=".LANGUAGE_ID.htmlspecialchars("&COPY_ID=").$ID,
			"ICON"	=> "btn_copy",
			"TITLE"	=> GetMessage("MAIN_COPY_RECORD_TITLE"),
		);

		if ($ID!=1)
		{
			$aMenu[] = array(
				"TEXT"	=> GetMessage("MAIN_DELETE_RECORD"),
				"LINK"	=> "javascript:if(confirm('".GetMessage("MAIN_DELETE_RECORD_CONF")."')) window.location='/bitrix/admin/user_admin.php?action=delete&ID=".$ID."&lang=".LANGUAGE_ID."&".bitrix_sessid_get()."';",
				"ICON"	=> "btn_delete",
				"TITLE"	=> GetMessage("MAIN_DELETE_RECORD_TITLE"),
			);
		}
	}
}

if(count($aMenu) > 0)
{
	$context = new CAdminContextMenu($aMenu);
	$context->Show();
}

if ($e = $APPLICATION->GetException())
	$message = new CAdminMessage(GetMessage("MAIN_ERROR_SAVING"), $e);
if($message)
	echo $message->Show();
if(strlen($strError)>0)
	echo CAdminMessage::ShowMessage(Array("MESSAGE"=>$strError, "HTML"=>true, "TYPE"=>"ERROR"));
?>
<form method="POST" name="form1" action="<?echo $APPLICATION->GetCurPage()?>?ID=<?=IntVal($ID)?>&amp;lang=<?=LANG?>" enctype="multipart/form-data">
<?=bitrix_sessid_post()?>
<input type="hidden" name="Update" value="Y">
<input type="hidden" name="COPY_ID" value=<?echo $COPY_ID?>>
<?
$tabControl->Begin();
$tabControl->BeginNextTab();
?>
	<?if($ID>0 && $COPY_ID<=0):?>
	<tr valign="top">
		<td><?echo GetMessage('LAST_UPDATE')?></td>
		<td><?echo $str_TIMESTAMP_X?></td>
	</tr>
	<tr valign="top">
		<td><?echo GetMessage('LAST_LOGIN')?></td>
		<td><?echo $str_LAST_LOGIN?></td>
	</tr>
	<?endif;?>
	<?if(($ID!='1' || $COPY_ID>0) && ($USER->CanDoOperation('view_all_users') || $USER->CanDoOperation('view_own_profile'))):?>
	<tr valign="top">
		<td><?echo GetMessage('ACTIVE')?></td>
		<td>
		<?if($canSelfEdit):?>
			<input type="checkbox" name="ACTIVE" value="Y"<?if($str_ACTIVE=="Y") echo " checked"?>>
		<?else:?>
			<input type="checkbox" <?if($str_ACTIVE=="Y") echo " checked"?> disabled>
			<input type="hidden" name="ACTIVE" value="<?=$str_ACTIVE;?>">
		<?endif;?>
	</tr>
	<?endif;?>
	<tr valign="top">
		<td width="40%"><?echo GetMessage('NAME')?></td>
		<td width="60%"><input type="text" name="NAME" size="30" maxlength="50" value="<? echo $str_NAME?>"></td>
	</tr>
	<tr valign="top">
		<td><?echo GetMessage('LAST_NAME')?></td>
		<td><input type="text" name="LAST_NAME" size="30" maxlength="50" value="<? echo $str_LAST_NAME?>"></td>
	</tr>
	<tr valign="top">
		<td><?echo GetMessage('SECOND_NAME')?></td>
		<td><input type="text" name="SECOND_NAME" size="30" maxlength="50" value="<? echo $str_SECOND_NAME?>"></td>
	</tr>
	<tr valign="top">
		<td><span class="required">*</span><? echo GetMessage('EMAIL')?></td>
		<td><input type="text" name="EMAIL" size="30" maxlength="50" value="<? echo $str_EMAIL?>"></td>
	</tr>
	<tr valign="top">
		<td><span class="required">*</span><?echo GetMessage('LOGIN')?></td>
		<td><input type="text" name="LOGIN" size="30" maxlength="50" value="<? echo $str_LOGIN?>"></td>
	</tr>
	<tr valign="top">
		<td><?if($ID<=0 || $COPY_ID>0):?><span class="required">*</span><?endif?><?echo GetMessage('NEW_PASSWORD_REQ')?><sup><span class="required">1</span></sup>:</td>
		<td><input type="password" name="NEW_PASSWORD" size="30" maxlength="50" value="<? echo htmlspecialchars($NEW_PASSWORD) ?>" autocomplete="off"></td>
	</tr>
	<tr valign="top">
		<td><?if($ID<=0 || $COPY_ID>0):?><span class="required">*</span><?endif?><?echo GetMessage('NEW_PASSWORD_CONFIRM')?></td>
		<td><input type="password" name="NEW_PASSWORD_CONFIRM" size="30" maxlength="50" value="<? echo htmlspecialchars($NEW_PASSWORD_CONFIRM) ?>" autocomplete="off"></td>
	</tr>
	<?if($USER->CanDoOperation('view_all_users')):?>
		<?
		$rExtAuth = CUser::GetExternalAuthList();
		if($arExtAuth = $rExtAuth->GetNext()):
			$bNewAuthID = (strlen($str_EXTERNAL_AUTH_ID)>0);
		?>
		<tr valign="top">
		<td><?echo GetMessage("MAIN_USERED_AUTH_TYPE")?></td>
		<td>
			<select name="EXTERNAL_AUTH_ID"<?if(!$canSelfEdit) echo " disabled"?>>
				<option value=""><?echo GetMessage("MAIN_USERED_AUTH_INT")?></option>
				<?do{?>
				<option value="<?=$arExtAuth['ID']?>"<?
				if($str_EXTERNAL_AUTH_ID==$arExtAuth['ID'])
				{
					echo ' selected';
					$bNewAuthID = false;
				}
				?>><?=$arExtAuth['NAME']?></option>
				<?}while($arExtAuth = $rExtAuth->GetNext());?>
				<?if($bNewAuthID):?>
					<option value="<?=$str_EXTERNAL_AUTH_ID?>"><?=$str_EXTERNAL_AUTH_ID?></option>
				<?endif;?>
			</select>
		</td>
		</tr>
		<?endif?>
		<?if(defined("ADMIN_SECTION") && ADMIN_SECTION===true):?>
			<tr valign="top">
				<td><?echo GetMessage("MAIN_DEFAULT_SITE")?></td>
				<?if(!$canSelfEdit) $dis = " disabled"?>
				<td><?=CSite::SelectBox("LID", $str_LID, "", "",$dis);?></td>
			</tr>
		<?endif?>
	<tr valign="top">
		<td><? echo GetMessage('INFO_FOR_USER')?></td>
		<td><input type="checkbox" name="user_info_event" value="Y"<?if($user_info_event=="Y")echo " checked"?><?if(!$canSelfEdit) echo " disabled"?>>
		</td>
	</tr>
	<? endif; ?>

<?if($showGroupTabs):?>
<?$tabControl->BeginNextTab();?>
	<tr valign="top">
		<td colspan="2" align="center"><table border="0" cellpadding="0" cellspacing="0" align="center">
			<tr>
				<td width="0%" nowrap colspan="2" align="center">&nbsp;</td>
				<td width="0%" nowrap colspan="2" style="padding-left:10px"><?=GetMessage('TBL_GROUP_DATE')?> (<?=FORMAT_DATETIME?>)</td>
				<td nowrap align="center">&nbsp;</td>
				<td nowrap align="center">&nbsp;</td>
			</tr>
			<script language="JavaScript" type="text/javascript">
			function CatGroupsActivate(obj, id)
			{
				var ed = eval("document.form1.GROUP_ID_FROM_" + id);
				var ed1 = eval("document.form1.GROUP_ID_TO_" + id);
				ed.disabled = !obj.checked;
				ed1.disabled = !obj.checked;
			}
			</script>
			<?
			$ind = -1;
			$dbGroups = CGroup::GetList(($b = "c_sort"), ($o = "asc"), Array("ANONYMOUS" => "N"));
			while ($arGroups = $dbGroups->Fetch())
			{
				if (!$USER->CanDoOperation('edit_all_users') && $USER->CanDoOperation('edit_subordinate_users') && !in_array($arGroups["ID"], $arUserSubordinateGroups) || $arGroups["ID"] == 2)
					continue;
				if($arGroups["ID"]==1 && !$USER->IsAdmin())
					continue;
				$ind++;
				?>
				<tr>
					<td width="0%" nowrap><input type="hidden" name="GROUP_ID_<?=$ind?>" value="<?=$arGroups["ID"]?>"><input type="checkbox" name="GROUP_ID_ACT_<?=$ind?>" id="GROUP_ID_ACT_ID_<?=$ind?>" value="Y"<?
						if (array_key_exists($arGroups["ID"], $str_GROUP_ID))
							echo " checked";
						?> OnChange="CatGroupsActivate(this, <?=$ind?>)"></td>
					<td width="0%" nowrap><label for="GROUP_ID_ACT_ID_<?= $ind ?>"><?=$arGroups["NAME"]?> [<a href="/bitrix/admin/group_edit.php?ID=<?=$arGroups["ID"]?>&lang=<?=LANGUAGE_ID?>" title="<?=GetMessage("MAIN_VIEW_GROUP")?>"><?echo intval($arGroups["ID"])?></a>]</label></td>
					<td width="0%" nowrap align="center" style="padding-right:10px; padding-left:10px">
						<?= GetMessage('USER_GROUP_DATE_FROM')?>
						<?= CalendarDate("GROUP_ID_FROM_".$ind, (array_key_exists($arGroups["ID"], $str_GROUP_ID) ? htmlspecialchars($str_GROUP_ID[$arGroups["ID"]]["DATE_ACTIVE_FROM"]) : ""), "form1", "10", ((array_key_exists($arGroups["ID"], $str_GROUP_ID)) ? " " : " disabled"))?>
					</td>
					<td width="0%" nowrap align="center">
						<?= GetMessage('USER_GROUP_DATE_TO')?>
						<?= CalendarDate("GROUP_ID_TO_".$ind, (array_key_exists($arGroups["ID"], $str_GROUP_ID) ? htmlspecialchars($str_GROUP_ID[$arGroups["ID"]]["DATE_ACTIVE_TO"]) : ""), "form1", "10", ((array_key_exists($arGroups["ID"], $str_GROUP_ID)) ? " " : " disabled"))?>
					</td>
				</tr>
				<?
			}
			?>
		</table><input type="hidden" name="GROUP_ID_NUMBER" value="<?= $ind ?>"></td>
	</tr>
<?endif;?>
<?$tabControl->BeginNextTab();?>
	<tr valign="top">
		<td width="40%"><?=GetMessage('USER_PROFESSION')?></td>
		<td width="60%"><input type="text" name="PERSONAL_PROFESSION" size="30" maxlength="255" value="<?=$str_PERSONAL_PROFESSION?>"></td>
	</tr>
	<tr valign="top">
		<td><?=GetMessage('USER_WWW')?></td>
		<td><input type="text" name="PERSONAL_WWW" size="30" maxlength="255" value="<?=$str_PERSONAL_WWW?>"></td>
	</tr>
	<tr valign="top">
		<td><?=GetMessage('USER_ICQ')?></td>
		<td><input type="text" name="PERSONAL_ICQ" size="30" maxlength="255" value="<?=$str_PERSONAL_ICQ?>"></td>
	</tr>
	<tr valign="top">
		<td><?=GetMessage('USER_GENDER')?></td>
		<td><?
			$arr = array(
				"reference"=>array(GetMessage("USER_MALE"),GetMessage("USER_FEMALE")), "reference_id"=>array("M","F"));
			echo SelectBoxFromArray("PERSONAL_GENDER", $arr, $str_PERSONAL_GENDER, GetMessage("USER_DONT_KNOW"));
			?></td>
	</tr>
	<tr valign="top">
		<td><?echo GetMessage("USER_BIRTHDAY_DT")." (".CLang::GetDateFormat("SHORT")."):"?></td>
		<td><?echo CalendarDate("PERSONAL_BIRTHDAY", $str_PERSONAL_BIRTHDAY, "form1")?></td>
	</tr>
	<tr valign="top">
		<td><?=GetMessage("USER_PHOTO")?></td>
		<td><?
		echo CFile::InputFile("PERSONAL_PHOTO", 20, $str_PERSONAL_PHOTO);
		if (strlen($str_PERSONAL_PHOTO)>0):
			?><br><?
			echo CFile::ShowImage($str_PERSONAL_PHOTO, 150, 150, "border=0", "", true);
		endif;
		?></td>
	<tr valign="top" class="heading">
		<td colspan="2" align="center"><?=GetMessage("USER_PHONES")?></td>
	</tr>
	<tr valign="top">
		<td><?=GetMessage('USER_PHONE')?></td>
		<td><input type="text" name="PERSONAL_PHONE" size="30" maxlength="255" value="<?=$str_PERSONAL_PHONE?>"></td>
	</tr>
	<tr valign="top">
		<td><?=GetMessage('USER_FAX')?></td>
		<td><input type="text" name="PERSONAL_FAX" size="30" maxlength="255" value="<?=$str_PERSONAL_FAX?>"></td>
	</tr>
	<tr valign="top">
		<td><?=GetMessage('USER_MOBILE')?></td>
		<td><input type="text" name="PERSONAL_MOBILE" size="30" maxlength="255" value="<?=$str_PERSONAL_MOBILE?>"></td>
	</tr>
	<tr valign="top">
		<td><?=GetMessage('USER_PAGER')?></td>
		<td><input type="text" name="PERSONAL_PAGER" size="30" maxlength="255" value="<?=$str_PERSONAL_PAGER?>"></td>
	</tr>
	<tr valign="top" class="heading">
		<td colspan="2" align="center"><?=GetMessage("USER_POST_ADDRESS")?></td>
	</tr>
	<tr valign="top">
		<td><?=GetMessage('USER_COUNTRY')?></td>
		<td><?echo SelectBoxFromArray("PERSONAL_COUNTRY", GetCountryArray(), $str_PERSONAL_COUNTRY, GetMessage("USER_DONT_KNOW"));?></td>
	</tr>
	<tr valign="top">
		<td><?=GetMessage('USER_STATE')?></td>
		<td><input type="text" name="PERSONAL_STATE" size="30" maxlength="255" value="<?=$str_PERSONAL_STATE?>"></td>
	</tr>
	<tr valign="top">
		<td><?=GetMessage('USER_CITY')?></td>
		<td><input type="text" name="PERSONAL_CITY" size="30" maxlength="255" value="<?=$str_PERSONAL_CITY?>"></td>
	</tr>
	<tr valign="top">
		<td><?=GetMessage('USER_ZIP')?></td>
		<td><input type="text" name="PERSONAL_ZIP" size="30" maxlength="255" value="<?=$str_PERSONAL_ZIP?>"></td>
	</tr>
	<tr valign="top">
		<td><?=GetMessage("USER_STREET")?></td>
		<td><textarea name="PERSONAL_STREET" cols="40" rows="3"><?echo $str_PERSONAL_STREET?></textarea></td>
	</tr>
	<tr valign="top">
		<td><?=GetMessage('USER_MAILBOX')?></td>
		<td><input type="text" name="PERSONAL_MAILBOX" size="30" maxlength="255" value="<?=$str_PERSONAL_MAILBOX?>"></td>
	</tr>
	<tr valign="top">
		<td><?=GetMessage("USER_NOTES")?></td>
		<td><textarea name="PERSONAL_NOTES" cols="40" rows="5"><?echo $str_PERSONAL_NOTES?></textarea></td>
	</tr>

<?$tabControl->BeginNextTab();?>
	<tr valign="top">
		<td width="40%"><?=GetMessage('USER_COMPANY')?></td>
		<td width="60%"><input type="text" name="WORK_COMPANY" size="30" maxlength="255" value="<?=$str_WORK_COMPANY?>"></td>
	</tr>
	<tr valign="top">
		<td><?=GetMessage('USER_WWW')?></td>
		<td><input type="text" name="WORK_WWW" size="30" maxlength="255" value="<?=$str_WORK_WWW?>"></td>
	</tr>
	<tr valign="top">
		<td><?=GetMessage('USER_DEPARTMENT')?></td>
		<td><input type="text" name="WORK_DEPARTMENT" size="30" maxlength="255" value="<?=$str_WORK_DEPARTMENT?>"></td>
	</tr>
	<tr valign="top">
		<td><?=GetMessage('USER_POSITION')?></td>
		<td><input type="text" name="WORK_POSITION" size="30" maxlength="255" value="<?=$str_WORK_POSITION?>"></td>
	</tr>
	<tr valign="top">
		<td><?=GetMessage("USER_WORK_PROFILE")?></td>
		<td><textarea name="WORK_PROFILE" cols="40" rows="5"><?echo $str_WORK_PROFILE?></textarea></td>
	</tr>
	<tr valign="top">
		<td><?=GetMessage("USER_LOGO")?></td>
		<td><?
			echo CFile::InputFile("WORK_LOGO", 20, $str_WORK_LOGO);
			if (strlen($str_WORK_LOGO)>0):
				?><br><?
				echo CFile::ShowImage($str_WORK_LOGO, 150, 150, "border=0", "", true);
			endif;
			?></td>
	</tr>
	<tr valign="top" class="heading">
		<td colspan="2" align="center"><?=GetMessage("USER_PHONES")?></td>
	</tr>
	<tr valign="top">
		<td><?=GetMessage('USER_PHONE')?></td>
		<td><input type="text" name="WORK_PHONE" size="30" maxlength="255" value="<?=$str_WORK_PHONE?>"></td>
	</tr>
	<tr valign="top">
		<td><?=GetMessage('USER_FAX')?></td>
		<td><input type="text" name="WORK_FAX" size="30" maxlength="255" value="<?=$str_WORK_FAX?>"></td>
	</tr>
	<tr valign="top">
		<td><?=GetMessage('USER_PAGER')?></td>
		<td><input type="text" name="WORK_PAGER" size="30" maxlength="255" value="<?=$str_WORK_PAGER?>"></td>
	</tr>
	<tr valign="top" class="heading">
		<td colspan="2" align="center"><?=GetMessage("USER_POST_ADDRESS")?></td>
	</tr>
	<tr valign="top">
		<td><?=GetMessage('USER_COUNTRY')?></td>
		<td><?echo SelectBoxFromArray("WORK_COUNTRY", GetCountryArray(), $str_WORK_COUNTRY, GetMessage("USER_DONT_KNOW"));?></td>
	</tr>
	<tr valign="top">
		<td><?=GetMessage('USER_STATE')?></td>
		<td><input type="text" name="WORK_STATE" size="30" maxlength="255" value="<?=$str_WORK_STATE?>"></td>
	</tr>
	<tr valign="top">
		<td><?=GetMessage('USER_CITY')?></td>
		<td><input type="text" name="WORK_CITY" size="30" maxlength="255" value="<?=$str_WORK_CITY?>"></td>
	</tr>
	<tr valign="top">
		<td><?=GetMessage('USER_ZIP')?></td>
		<td><input type="text" name="WORK_ZIP" size="30" maxlength="255" value="<?=$str_WORK_ZIP?>"></td>
	</tr>
	<tr valign="top">
		<td><?=GetMessage("USER_STREET")?></td>
		<td><textarea name="WORK_STREET" cols="40" rows="3"><?echo $str_WORK_STREET?></textarea></td>
	</tr>
	<tr valign="top">
		<td><?=GetMessage('USER_MAILBOX')?></td>
		<td><input type="text" name="WORK_MAILBOX" size="30" maxlength="255" value="<?=$str_WORK_MAILBOX?>"></td>
	</tr>
	<tr valign="top">
		<td><?=GetMessage("USER_NOTES")?></td>
		<td><textarea name="WORK_NOTES" cols="40" rows="5"><?echo $str_WORK_NOTES?></textarea></td>
	</tr>

	<?
	$db_opt_res = CModule::GetList();
	while ($opt_res = $db_opt_res->Fetch())
	{
		$mdir = $opt_res["ID"];
		if (file_exists($DOCUMENT_ROOT.BX_ROOT."/modules/".$mdir) && is_dir($DOCUMENT_ROOT.BX_ROOT."/modules/".$mdir))
		{
			$ofile = $DOCUMENT_ROOT.BX_ROOT."/modules/".$mdir."/options_user_settings.php";
			if (file_exists($ofile))
			{
//				$MODULE_RIGHT = $APPLICATION->GetGroupRight($mdir);
//				if ($MODULE_RIGHT>="R")
//				{
					$tabControl->BeginNextTab();
					include($ofile);
//				}
			}
		}
	}
	?>

<?if (($editable && $ID!=$USER->GetID()) || $USER->IsAdmin()):?>
<?$tabControl->BeginNextTab();?>
	<tr valign="top">
		<td align="center" colspan="2"><textarea name="ADMIN_NOTES" cols="50" rows="10" style="width:100%;"><?echo $str_ADMIN_NOTES?></textarea></td>
	</tr>
<?endif;?>
<?
//Add user fields tab only when there is fields defined or user has rights for adding new field
if(
	(count($USER_FIELD_MANAGER->GetUserFields($PROPERTY_ID)) > 0) ||
	($USER_FIELD_MANAGER->GetRights($PROPERTY_ID) >= "W")
)
{
	$tabControl->BeginNextTab();
	$USER_FIELD_MANAGER->EditFormShowTab($PROPERTY_ID, ((strLen($strError) > 0) ? true : false), $ID);
}
?>
<?$tabControl->Buttons(array("disabled" => (!$editable), "back_url"=>"user_admin.php?lang=".LANGUAGE_ID));
$tabControl->End();
$tabControl->ShowWarnings("form1", $message);
?>
</form>

<?echo BeginNote();?>
<span class="required">1</span> <?$GROUP_POLICY = CUser::GetGroupPolicy($ID);echo $GROUP_POLICY["PASSWORD_REQUIREMENTS"];?><br>
<span class="required">*</span> <?echo GetMessage("REQUIRED_FIELDS")?>
<?echo EndNote();?>
<?
require_once ($DOCUMENT_ROOT.BX_ROOT."/modules/main/include/epilog_admin.php");
?>
