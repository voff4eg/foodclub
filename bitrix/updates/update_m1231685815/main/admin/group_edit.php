<?
##############################################
# Bitrix Site Manager                        #
# Copyright (c) 2002-2007 Bitrix             #
# http://www.bitrixsoft.com                  #
# mailto:admin@bitrixsoft.com                #
##############################################

require_once(dirname(__FILE__)."/../include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/prolog_user.php");
define("HELP_FILE", "users/group_edit.php");

if (!$USER->CanDoOperation('view_groups'))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

IncludeModuleLangFile(__FILE__);
$modules = COperation::GetAllowedModules();
for($i = 0, $l=count($modules);$i < $l;$i++)
	IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/".$modules[$i]."/admin/task_description.php");
/***************************************************************************
			   ќбработка GET | POST
****************************************************************************/
$strError="";
$ID=intval($ID);
$COPY_ID=intval($COPY_ID);
if($COPY_ID>0)
	$ID = $COPY_ID;
$modules = CModule::GetList();
$arModules = array();
while ($mr = $modules->Fetch()) $arModules[] = $mr["ID"];

$USER_COUNT = CUser::GetCount();
$USER_COUNT_MAX = 25;


$arBXGroupPolicyLow = Array(
		"SESSION_TIMEOUT" => 30, //minutes
		"SESSION_IP_MASK" => "0.0.0.0",
		"MAX_STORE_NUM" => 20,
		"STORE_IP_MASK" => "255.0.0.0",
		"STORE_TIMEOUT" => 60*24*93, //minutes
		"CHECKWORD_TIMEOUT" => 60*24*185,  //minutes
		"PASSWORD_LENGTH" => 6,
		"PASSWORD_UPPERCASE" => "N",
		"PASSWORD_LOWERCASE" => "N",
		"PASSWORD_DIGITS" => "N",
		"PASSWORD_PUNCTUATION" => "N",
	);
$arBXGroupPolicyMiddle = Array(
		"SESSION_TIMEOUT" => 20, //minutes
		"SESSION_IP_MASK" => "255.255.0.0",
		"MAX_STORE_NUM" => 10,
		"STORE_IP_MASK" => "255.255.0.0",
		"STORE_TIMEOUT" => 60*24*30, //minutes
		"CHECKWORD_TIMEOUT" => 60*24*1,  //minutes
		"PASSWORD_LENGTH" => 8,
		"PASSWORD_UPPERCASE" => "Y",
		"PASSWORD_LOWERCASE" => "Y",
		"PASSWORD_DIGITS" => "Y",
		"PASSWORD_PUNCTUATION" => "N",
	);
$arBXGroupPolicyHigh = Array(
		"SESSION_TIMEOUT" => 15, //minutes
		"SESSION_IP_MASK" => "255.255.255.255",
		"MAX_STORE_NUM" => 1,
		"STORE_IP_MASK" => "255.255.255.255",
		"STORE_TIMEOUT" => 60*24*3, //minutes
		"CHECKWORD_TIMEOUT" => 60,  //minutes
		"PASSWORD_LENGTH" => 10,
		"PASSWORD_UPPERCASE" => "Y",
		"PASSWORD_LOWERCASE" => "Y",
		"PASSWORD_DIGITS" => "Y",
		"PASSWORD_PUNCTUATION" => "Y",
	);
$BX_GROUP_POLICY_CONTROLS = Array(
	"SESSION_TIMEOUT"	=>	array("text", 5),
	"SESSION_IP_MASK"	=>	array("text", 20),
	"MAX_STORE_NUM"		=>	array("text", 5),
	"STORE_IP_MASK"		=>	array("text", 20),
	"STORE_TIMEOUT"		=>	array("text", 5),
	"CHECKWORD_TIMEOUT"	=>	array("text", 5),
	"PASSWORD_LENGTH"	=>	array("text", 5),
	"PASSWORD_UPPERCASE"	=>	array("checkbox", "Y"),
	"PASSWORD_LOWERCASE"	=>	array("checkbox", "Y"),
	"PASSWORD_DIGITS"	=>	array("checkbox", "Y"),
	"PASSWORD_PUNCTUATION"	=>	array("checkbox", "Y"),
);

$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("MAIN_TAB"), "ICON" => "group_edit", "TITLE" => GetMessage("MAIN_TAB_TITLE")),
	array("DIV" => "edit2", "TAB" => GetMessage("TAB_2"), "ICON" => "group_edit", "TITLE" => GetMessage('MUG_POLICY_TITLE')),
);
if($ID!=1 || $COPY_ID>0 || (COption::GetOptionString("main", "controller_member", "N") == "Y" && COption::GetOptionString("main", "~controller_limited_admin", "N") == "Y"))
{
	$aTabs[] = array("DIV" => "edit3", "TAB" => GetMessage("TAB_3"), "ICON" => "group_edit", "TITLE" => GetMessage("MODULE_RIGHTS"));
}
$tabControl = new CAdminTabControl("tabControl", $aTabs);

if($REQUEST_METHOD=="POST" && (strlen($save)>0 || strlen($apply)>0) && $USER->CanDoOperation('edit_groups') && check_bitrix_sessid())
{
	if ($ID <= 2 && $COPY_ID<=0)
		$ACTIVE = "Y";

	$group = new CGroup;

	$arGroupPolicy = array();
	foreach ($BX_GROUP_POLICY as $key => $value)
	{
		$curVal = ${"gp_".$key};
		$curValParent = ${"gp_".$key."_parent"};

		if ($curValParent != "Y")
			$arGroupPolicy[$key] = $curVal;
	}

	$arFields = array(
			"ACTIVE" => $ACTIVE,
			"C_SORT" => $C_SORT,
			"NAME" => $NAME,
			"DESCRIPTION" => $DESCRIPTION,
			"STRING_ID" => $STRING_ID,
			"SECURITY_POLICY" => serialize($arGroupPolicy)
		);

	if ($USER_COUNT <= $USER_COUNT_MAX)
	{
		$USER_ID_NUMBER = IntVal($USER_ID_NUMBER);
		$USER_ID = array();
		$ind = -1;
		for ($i = 0; $i <= $USER_ID_NUMBER; $i++)
		{
			if (${"USER_ID_ACT_".$i} == "Y")
			{
				$ind++;
				$USER_ID[$ind]["USER_ID"] = IntVal(${"USER_ID_".$i});
				$USER_ID[$ind]["DATE_ACTIVE_FROM"] = ${"USER_ID_FROM_".$i};
				$USER_ID[$ind]["DATE_ACTIVE_TO"] = ${"USER_ID_TO_".$i};
			}
		}

		if ($ID == 1 && $COPY_ID<=0)
		{
			$ind++;
			$USER_ID[$ind]["USER_ID"] = 1;
			$USER_ID[$ind]["DATE_ACTIVE_FROM"] = false;
			$USER_ID[$ind]["DATE_ACTIVE_TO"] = false;
		}

		$arFields["USER_ID"] = $USER_ID;
	}

	if($ID>0 && $COPY_ID<=0)
		$res = $group->Update($ID, $arFields);
	else
	{
		$ID = $group->Add($arFields);
		$res = ($ID>0);
		$new="Y";
	}

	$strError .= $group->LAST_ERROR;
	if (strlen($strError)<=0)
	{
		if (intval($ID) != 1 || (COption::GetOptionString("main", "controller_member", "N") == "Y" && COption::GetOptionString("main", "~controller_limited_admin", "N") == "Y"))
		{
			// устанавливаем права по модул€м
			reset($arModules);
			$arTasks = Array();
			foreach ($arModules as $MID)
			{
				if(isset(${"TASKS_".$MID}))
				{
					$arTasks[$MID] = ${"TASKS_".$MID};
					$rt = CTask::GetLetter($arTasks[$MID]);
				}
				else
				{
					$rt = ${"RIGHTS_".$MID};
				}

				if (strlen($rt) > 0 && $rt != "NOT_REF")
					$APPLICATION->SetGroupRight($MID, $ID, $rt);
				else
					$APPLICATION->DelGroupRight($MID, array($ID));
			}

			$arTasksModules = CTask::GetTasksInModules(false, false, 'module');
			$nID = COperation::GetIDByName('edit_subordinate_users');
			$nID2 = COperation::GetIDByName('view_subordinate_users');
			$arTaskIds = $arTasksModules['main'];
			$handle_subord = false;
			$l = count($arTaskIds);
			for ($i=0;$i<$l;$i++)
			{
				if ($arTaskIds[$i]['ID'] == $arTasks['main'])
				{
					$arOpInTask = CTask::GetOperations($arTaskIds[$i]['ID']);
					if (in_array($nID,$arOpInTask) || in_array($nID2,$arOpInTask))
						$handle_subord = true;
					break;
				}
			}
			if ($handle_subord)
			{
				$arSubordinateGroups = (isset($_POST['subordinate_groups'])) ? $_POST['subordinate_groups'] : Array();
				CGroup::SetSubordinateGroups($ID, $arSubordinateGroups);
			}
			else
				CGroup::SetSubordinateGroups($ID);

			$old_arTasks = CGroup::GetTasks($ID,true);
			if (count(array_diff($old_arTasks, $arTasks)) > 0 || count(array_diff($arTasks,$old_arTasks)) > 0)
				CGroup::SetTasks($ID, $arTasks, true);
		}

		if($USER->CanDoOperation('edit_groups') && strlen($save)>0)
			LocalRedirect("group_admin.php?lang=".LANGUAGE_ID);
		elseif($USER->CanDoOperation('edit_groups') && strlen($apply)>0)
			LocalRedirect($APPLICATION->GetCurPage()."?lang=".LANGUAGE_ID."&ID=".$ID."&".$tabControl->ActiveTabParam());
		elseif($new=="Y")
			LocalRedirect($APPLICATION->GetCurPage()."?lang=".LANGUAGE_ID."&ID=".$ID."&".$tabControl->ActiveTabParam());
	}
}

$str_USER_ID = array();

$z = CGroup::GetByID($ID, "N");
if(!$z->ExtractFields("str_"))
{
	$ID=0;
	$str_ACTIVE="Y";
	$str_C_SORT = 100;
}
else
{
	$dbUserGroup = CGroup::GetGroupUserEx($ID);
	while ($arUserGroup = $dbUserGroup->Fetch())
	{
		$str_USER_ID[IntVal($arUserGroup["USER_ID"])]["DATE_ACTIVE_FROM"] = $arUserGroup["DATE_ACTIVE_FROM"];
		$str_USER_ID[IntVal($arUserGroup["USER_ID"])]["DATE_ACTIVE_TO"] = $arUserGroup["DATE_ACTIVE_TO"];
	}
}

if (strlen($strError)>0)
{
	$DB->InitTableVarsForEdit("b_group", "", "str_");

	$USER_ID_NUMBER = IntVal($USER_ID_NUMBER);
	$str_USER_ID = array();
	for ($i = 0; $i <= $USER_ID_NUMBER; $i++)
	{
		if (${"USER_ID_ACT_".$i} == "Y")
		{
			$str_USER_ID[IntVal(${"USER_ID_".$i})]["DATE_ACTIVE_FROM"] = ${"USER_ID_FROM_".$i};
			$str_USER_ID[IntVal(${"USER_ID_".$i})]["DATE_ACTIVE_TO"] = ${"USER_ID_TO_".$i};
		}
	}
}

$ed_title = ($USER->CanDoOperation('edit_groups')) ? GetMessage("EDIT_GROUP_TITLE") : GetMessage("EDIT_GROUP_TITLE_VIEW");
$sDocTitle = ($ID > 0 && $COPY_ID <= 0) ? eregi_replace("#ID#","$ID", $ed_title) : GetMessage("NEW_GROUP_TITLE");
$APPLICATION->SetTitle($sDocTitle);

/***************************************************************************
			   HTML форма
****************************************************************************/

require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");

$aMenu = array(
	array(
		"TEXT"	=> GetMessage("RECORD_LIST"),
		"TITLE"	=> GetMessage("RECORD_LIST_TITLE"),
		"LINK"	=> "/bitrix/admin/group_admin.php?lang=".LANGUAGE_ID."&set_default=Y",
		"ICON"	=> "btn_list"

	)
);

if($USER->CanDoOperation('edit_groups'))
{
	if(intval($ID)>0 && $COPY_ID<=0)
	{
		$aMenu[] = array("SEPARATOR"=>"Y");

		$aMenu[] = array(
			"TEXT"	=> GetMessage("MAIN_NEW_RECORD"),
			"TITLE"	=> GetMessage("MAIN_NEW_RECORD_TITLE"),
			"LINK"	=> "/bitrix/admin/group_edit.php?lang=".LANGUAGE_ID,
			"ICON"	=> "btn_new"
		);
		if($ID>1)
		{
			$aMenu[] = array(
				"TEXT"	=> GetMessage("MAIN_COPY_RECORD"),
				"TITLE"	=> GetMessage("MAIN_COPY_RECORD_TITLE"),
				"LINK"	=> "/bitrix/admin/group_edit.php?lang=".LANGUAGE_ID."&amp;COPY_ID=".$ID,
				"ICON"	=> "btn_copy"
			);
		}

		if($ID>2)
		{
			$aMenu[] = array(
				"TEXT"	=> GetMessage("MAIN_DELETE_RECORD"),
				"TITLE"	=> GetMessage("MAIN_DELETE_RECORD_TITLE"),
				"LINK"	=> "javascript:if(confirm('".CUtil::JSEscape(GetMessage("MAIN_DELETE_RECORD_CONF"))."')) window.location='/bitrix/admin/group_admin.php?ID=".$ID."&action=delete&lang=".LANGUAGE_ID."&".bitrix_sessid_get()."';",
				"ICON"	=> "btn_delete"
			);
		}
	}
}

$context = new CAdminContextMenu($aMenu);
$context->Show();
?>
<?=CAdminMessage::ShowMessage($strError);?>
<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?" name="form1">
<?=bitrix_sessid_post()?>
<input type="hidden" name="lang" value="<?echo LANG?>">
<input type="hidden" name="ID" value="<?echo $ID?>">
<?if(strlen($COPY_ID)>0):?><input type="hidden" name="COPY_ID" value="<?echo htmlspecialchars($COPY_ID)?>"><?endif?>
<?
$tabControl->Begin();

$tabControl->BeginNextTab();
?>
	<?if(strlen($str_TIMESTAMP_X)>0):?>
	<tr valign="top">
		<td width="40%"><?echo GetMessage('LAST_UPDATE')?></td>
		<td width="60%"><?echo $str_TIMESTAMP_X?></td>
	</tr>
	<? endif; ?>
	<?
	if ($ID > 0 && $ID != 2 && $COPY_ID<=0)
	{
		$dbGroupTmp = CGroup::GetByID($ID, "Y");
		if ($arGroupTmp = $dbGroupTmp->Fetch())
		{
			?>
			<tr valign="top">
				<td width="40%"><?echo GetMessage('MAIN_TOTAL_USERS')?></td>
				<td width="60%"><a href="user_admin.php?lang=<?=LANG?>&find_group_id[]=<?=$ID?>&set_filter=Y" title="<?=GetMessage("MAIN_VIEW_USER_GROUPS")?>"><?= IntVal($arGroupTmp["USERS"]) ?></a></td>
			</tr>
			<?
		}
	}
	?>
	<?if($ID>2 && $COPY_ID<=0):?>
	<tr valign="top">
		<td width="40%"><?echo GetMessage('ACTIVE')?></td>
		<td width="60%"><input type="checkbox" name="ACTIVE" value="Y"<?if($str_ACTIVE=="Y")echo " checked"?>></td>
	</tr>
	<?endif;?>
	<tr valign="top">
		<td width="40%"><?=GetMessage("MAIN_C_SORT")?></td>
		<td width="60%"><input type="text" name="C_SORT" size="5" maxlength="18" value="<?echo $str_C_SORT?>"></td>
	</tr>
	<tr valign="top">
		<td><span class="required">*</span><?echo GetMessage('NAME')?></td>
		<td><input type="text" name="NAME" size="40" maxlength="50" value="<?=$str_NAME?>"></td>
	</tr>
	<tr valign="top">
		<td><?echo GetMessage('STRING_ID')?></td>
		<td><input type="text" name="STRING_ID" size="40" maxlength="50" value="<?=$str_STRING_ID?>"></td>
	</tr>
	<tr valign="top">
		<td><?echo GetMessage('DESCRIPTION')?></td>
		<td><textarea name="DESCRIPTION" cols="30" rows="5"><?echo $str_DESCRIPTION?></textarea>
		</td>
	</tr>
	<?if($USER_COUNT<=$USER_COUNT_MAX && $ID!=2):?>
	<tr class="heading">
		<td colspan="2"><?echo GetMessage('USERS');?></td>
	<tr>
		<td colspan="2" align="center">
		<table border="0" cellpadding="0" cellspacing="0" class="internal">
			<tr class="heading">
				<td>&nbsp;</td>
				<td><?echo GetMessage("USER_LIST")?></td>
				<td><?=GetMessage('TBL_GROUP_DATE')?> (<?=FORMAT_DATETIME?>)</td>
			</tr>
			<script>
			function CatGroupsActivate(obj, id)
			{
				var ed = eval("document.form1.USER_ID_FROM_" + id);
				var ed1 = eval("document.form1.USER_ID_TO_" + id);
				ed.disabled = !obj.checked;
				ed1.disabled = !obj.checked;
			}
			</script>
			<?
			$ind = -1;
			$dbUsers = CUser::GetList(($b="id"), ($o="asc"), array("ACTIVE" => "Y"));
			while ($arUsers = $dbUsers->Fetch())
			{
				$ind++;
				?>
				<tr>
					<td>
						<input type="hidden" name="USER_ID_<?=$ind?>" value="<?=$arUsers["ID"] ?>">
						<input type="checkbox" name="USER_ID_ACT_<?=$ind?>" id="USER_ID_ACT_ID_<?=$ind?>" value="Y" <?
							if (array_key_exists($arUsers["ID"], $str_USER_ID))
								echo " checked";
							?> OnChange="CatGroupsActivate(this, <?=$ind?>)"></td>
					<td><label for="USER_ID_ACT_ID_<?=$ind?>">[<a href="/bitrix/admin/user_edit.php?ID=<?=$arUsers["ID"]?>&lang=<?=LANGUAGE_ID?>" title="<?=GetMessage("MAIN_VIEW_USER")?>"><?=$arUsers["ID"]?></a>] (<?=htmlspecialchars($arUsers["LOGIN"])?>) <?=htmlspecialchars($arUsers["NAME"])?> <?=htmlspecialchars($arUsers["LAST_NAME"])?></label></td>
					<td nowrap>

						<?=GetMessage('USER_GROUP_DATE_FROM')?>
						<?=CalendarDate("USER_ID_FROM_".$ind, (array_key_exists($arUsers["ID"], $str_USER_ID) ? htmlspecialchars($str_USER_ID[$arUsers["ID"]]["DATE_ACTIVE_FROM"]) : ""), "form1", "10", (array_key_exists($arUsers["ID"], $str_USER_ID) ? " " : " disabled"))?>
						<?=GetMessage('USER_GROUP_DATE_TO')?>
						<?=CalendarDate("USER_ID_TO_".$ind, (array_key_exists($arUsers["ID"], $str_USER_ID) ? htmlspecialchars($str_USER_ID[$arUsers["ID"]]["DATE_ACTIVE_TO"]) : ""), "form1", "10", (array_key_exists($arUsers["ID"], $str_USER_ID) ? " " : " disabled"))?>

					</td>
				</tr>
				<?
			}
			?>
		</table><input type="hidden" name="USER_ID_NUMBER" value="<?= $ind ?>"><?
		//echo SelectBoxM("USER_ID[]", CUser::GetDropDownList(), $str_USER_ID, "", false, 20);
		?></td>
	</tr>
	<?endif?>
<?$tabControl->BeginNextTab();?>
	<script>
	var arGroupPolicyKey = new Array();
	var arGroupPolicyLow = new Array();
	var arGroupPolicyMiddle = new Array();
	var arGroupPolicyHigh = new Array();
	<?
	$i = -1;
	foreach ($BX_GROUP_POLICY as $key => $value)
	{
		$i++;
		?>arGroupPolicyKey[<?= $i ?>] = '<?= $key ?>'; arGroupPolicyLow[<?= $i ?>] = '<?= $arBXGroupPolicyLow[$key] ?>'; arGroupPolicyMiddle[<?= $i ?>] = '<?= $arBXGroupPolicyMiddle[$key] ?>'; arGroupPolicyHigh[<?= $i ?>] = '<?= $arBXGroupPolicyHigh[$key] ?>';<?
	}
	?>

	function gpLevel()
	{
		var i;

		var el = document.form1.gp_level;
		if (el.selectedIndex > 0)
		{
			var sel = el.options[el.selectedIndex].value;

			for (i = 0; i < arGroupPolicyKey.length; i++)
			{
				var el1 = eval("document.form1.gp_" + arGroupPolicyKey[i] + "_parent");
				var el2 = eval("document.form1.gp_" + arGroupPolicyKey[i] + "");

				if (sel == "parent")
					el1.checked = true;
				else
					el1.checked = false;

				gpChangeParent(arGroupPolicyKey[i]);

				if (sel == "low")
				{
					if(el2.type.toLowerCase() == 'checkbox')
						el2.checked = arGroupPolicyLow[i] == "Y";
					else
						el2.value = arGroupPolicyLow[i];
				}
				else if (sel == "middle")
				{
					if(el2.type.toLowerCase() == 'checkbox')
						el2.checked = arGroupPolicyMiddle[i] == "Y";
					else
						el2.value = arGroupPolicyMiddle[i];
				}
				else if (sel == "high")
				{
					if(el2.type.toLowerCase() == 'checkbox')
						el2.checked = arGroupPolicyHigh[i] == "Y";
					else
						el2.value = arGroupPolicyHigh[i];
				}
				else if (sel == "parent")
				{
					if(el2.type.toLowerCase() == 'checkbox')
						el2.checked = false;
					else
						el2.value = "";
				}
			}
		}
	}

	function gpChange(key)
	{
		var el = eval("document.form1.gp_" + key + "_parent");
		if (el.checked)
			el.checked = false;
	}

	function gpChangeParent(key)
	{
		var el1 = eval("document.form1.gp_" + key + "_parent");
		var el2 = eval("document.form1.gp_" + key + "");
		el2.disabled = el1.checked;
	}
	</script>
	<tr valign="top">
		<td width="40%"><?=GetMessage('MUG_PREDEFINED_FIELD')?>:</td>
		<td width="40%">
			<select name="gp_level" OnChange="gpLevel()">
				<option value=""><?=GetMessage('MUG_SELECT_LEVEL1')?></option>
				<option value="parent"><?=GetMessage('MUG_PREDEFINED_PARENT')?></option>
				<option value="low"><?=GetMessage('MUG_PREDEFINED_LOW')?></option>
				<option value="middle"><?=GetMessage('MUG_PREDEFINED_MIDDLE')?></option>
				<option value="high"><?=GetMessage('MUG_PREDEFINED_HIGH')?></option>
			</select>
		</td>
	</tr>
	<?
	$arGroupPolicy = unserialize(htmlspecialcharsback($str_SECURITY_POLICY));
	if (!is_array($arGroupPolicy))
		$arGroupPolicy = array();

	foreach ($BX_GROUP_POLICY as $key => $value)
	{
		$curVal = $arGroupPolicy[$key];
		$curValParent = !array_key_exists($key, $arGroupPolicy);
		if (strlen($strError) > 0)
		{
			$curVal = ${"gp_".$key};
			$curValParent = ((${"gp_".$key."_parent"} == "Y") ? True : False);
		}
		?>
		<tr valign="top">
			<td><label for="gp_<?echo $key?>"><?
			$gpTitle = GetMessage("GP_".$key);
			if (strlen($gpTitle) <= 0)
				$gpTitle = $key;

			echo $gpTitle;
			?></label>:</td>
			<td>

				<input type="checkbox" name="gp_<?= $key ?>_parent" OnClick="gpChangeParent('<?= $key ?>')" id="id_gp_<?= $key ?>_parent" value="Y"<?if ($curValParent) echo "checked";?>><label for="id_gp_<?= $key ?>_parent"><?=GetMessage('MUG_GP_PARENT')?></label><br>
				<?$arControl = $BX_GROUP_POLICY_CONTROLS[$key];
				switch($arControl[0])
				{
				case "checkbox":
					?>
					<input type="checkbox" OnClick="gpChange('<?= $key ?>')" id="gp_<?= $key ?>" name="gp_<?= $key ?>" value="<?= htmlspecialchars($arControl[1]) ?>" <?if($curVal === $arControl[1]) echo "checked"?>>
					<?
					break;
				default:
					?>
					<input type="text" OnChange="gpChange('<?= $key ?>')" name="gp_<?= $key ?>" value="<?= htmlspecialchars($curVal) ?>" size="<?echo ($arControl[1] > 0? $arControl[1]: "30")?>">
					<?
				}
				?>
			</td>
		</tr>
		<script>
		gpChangeParent('<?= $key ?>');
		</script>
		<?
	}
	?>

	<?if (intval($ID)!=1 || $COPY_ID>0 || (COption::GetOptionString("main", "controller_member", "N") == "Y" && COption::GetOptionString("main", "~controller_limited_admin", "N") == "Y")) :?>
	<?$tabControl->BeginNextTab();?>
	<tr valign="top">
		<td width="40%"><?=GetMessage("KERNEL")?></td>
		<td width="60%">
			<script>var arSubordTasks = [];</script>
			<?
			$arTasksModules = CTask::GetTasksInModules(true,false,'module');
			$arTasks = CGroup::GetTasks($ID,true);
			$nID = COperation::GetIDByName('edit_subordinate_users');
			$nID2 = COperation::GetIDByName('view_subordinate_users');
			$v = (isset($arTasks['main'])) ? $arTasks['main'] : false;
			echo SelectBoxFromArray("TASKS_main", $arTasksModules['main'], $v, GetMessage("DEFAULT"));

			$show_subord = false;
			$arTaskIds = $arTasksModules['main']['reference_id'];
			$l = count($arTaskIds);
			for ($i=0;$i<$l;$i++)
			{
				$arOpInTask = CTask::GetOperations($arTaskIds[$i]);
				if (in_array($nID, $arOpInTask) || in_array($nID2, $arOpInTask))
				{
					?><script>
					arSubordTasks.push(<?=$arTaskIds[$i]?>);
					</script><?
					if ($arTaskIds[$i] == $v)
						$show_subord = true;
				}
			}
			//$ar = $APPLICATION->GetMainRightList();
			//$v = $APPLICATION->GetGroupRight("main", array($ID), "N", "N");
			//echo SelectBoxFromArray("RIGHTS_main", $ar, htmlspecialchars($v), GetMessage("DEFAULT"));
			?>
			<script>
			document.getElementById('TASKS_main').onchange = function()
			{
				var show = false;
				for (var s = 0; s < arSubordTasks.length; s++)
				{
					if (arSubordTasks[s].toString() == this.value)
					{
						show = true;
						break;
					}
				}
				var row = document.getElementById('__subordinate_groups_tr');
				if (show)
				{
					try{row.style.display = 'table-row';}
					catch(e){row.style.display = 'block';}
				}
				else
					row.style.display = 'none';
			};
			</script>
		</td>
	</tr>
	<tr valign="top" id="__subordinate_groups_tr" <?echo $show_subord ? '' : 'style="display:none"';?>>
		<td width="40%"><?=GetMessage('SUBORDINATE_GROUPS');?>:</td>
		<td width="60%">
			<select id="subordinate_groups" name="subordinate_groups[]" multiple size="6">
			<?
			$arSubordinateGroups = CGroup::GetSubordinateGroups($ID);
			$rsData = CGroup::GetList($by, $order, $arFilter, "Y");
			while($arRes = $rsData->Fetch())
			{
				if ($arRes['ID'] == 1 || $arRes['ID'] == $ID)
					continue;
				?><option value="<?=$arRes['ID']?>" <?echo (in_array($arRes['ID'],$arSubordinateGroups) || $arRes['ID'] == 2) ? 'selected' : ''?>><? echo '['.$arRes['ID'].'] '.$arRes['NAME']?></option><?
			}
			?>
			</select>
			<script>
			/*document.getElementById('subordinate_groups').onchange = function(e)
			{
				for (var i=0, len = this.options.length; i<len; i++)
				{
					if (this.options[i].value == 2)
					{
						this.options[i].selected = 'selected';
						break;
					}
				}
			}*/
			document.getElementById('subordinate_groups').onblur = function(e)
			{
				for (var i=0, len = this.options.length; i<len; i++)
				{
					if (this.options[i].value == 2)
					{
						this.options[i].selected = 'selected';
						break;
					}
				}
			}
			</script>
		</td>
	</tr>
	<?
	reset($arModules);
	while (list(,$MID) = each($arModules)) :
		if ($MID!="main") :
		if(!file_exists($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/".$MID."/install/index.php"))
			continue;
		include_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/".$MID."/install/index.php");
		if (class_exists($MID)) :
			$module = new $MID;
			if ($module->MODULE_GROUP_RIGHTS=="Y") :
	?>
	<tr valign="top">
		<td><?=$module->MODULE_NAME.":"?></td>
		<td><?
			if (isset($arTasksModules[$MID]))
			{
				$v = (isset($arTasks[$MID])) ? $arTasks[$MID] : false;
				echo SelectBoxFromArray("TASKS_".$MID, $arTasksModules[$MID], $v, GetMessage("DEFAULT"));
			}
			else
			{
				if (method_exists($module, "GetModuleRightList"))
					$ar = call_user_method("GetModuleRightList",$module);
				else
					$ar = $APPLICATION->GetDefaultRightList();
				$v = $APPLICATION->GetGroupRight($MID, array($ID), "N", "N");
				echo SelectBoxFromArray("RIGHTS_".$MID, $ar, htmlspecialchars($v), GetMessage("DEFAULT"));
			}
		?></td>
	</tr>
	<?
			endif;
		endif;
		endif;
	endwhile;
	?>
	<?endif;?>
<?
$tabControl->Buttons(array("disabled" => !$USER->CanDoOperation('edit_groups'), "back_url"=>"group_admin.php?lang=".LANGUAGE_ID));
$tabControl->End();
?>

</form>
<?echo BeginNote();?>
<span class="required">*</span> - <?echo GetMessage("REQUIRED_FIELDS")?>
<?echo EndNote();?>
<?require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");?>
