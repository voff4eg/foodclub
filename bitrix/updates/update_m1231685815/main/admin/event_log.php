<?php
##############################################
# Bitrix Site Manager                        #
# Copyright (c) 2002-2008 Bitrix             #
# http://www.bitrixsoft.com                  #
# mailto:admin@bitrixsoft.com                #
##############################################

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/prolog.php");

if(!$USER->CanDoOperation('view_event_log'))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

IncludeModuleLangFile(__FILE__);

$bStatistic = CModule::IncludeModule('statistic');

$arAuditTypes = array(
	"USER_AUTHORIZE" => "[USER_AUTHORIZE] ".GetMessage("MAIN_EVENTLOG_USER_AUTHORIZE"),
	"USER_DELETE" => "[USER_DELETE] ".GetMessage("MAIN_EVENTLOG_USER_DELETE"),
	"USER_INFO" => "[USER_INFO] ".GetMessage("MAIN_EVENTLOG_USER_INFO"),
	"USER_LOGIN" => "[USER_LOGIN] ".GetMessage("MAIN_EVENTLOG_USER_LOGIN"),
	"USER_LOGINBYHASH" => "[USER_LOGINBYHASH] ".GetMessage("MAIN_EVENTLOG_USER_LOGINBYHASH"),
	"USER_LOGOUT" => "[USER_LOGOUT] ".GetMessage("MAIN_EVENTLOG_USER_LOGOUT"),
	"USER_PASSWORD_CHANGED" => "[USER_PASSWORD_CHANGED] ".GetMessage("MAIN_EVENTLOG_USER_PASSWORD_CHANGED"),
	"USER_REGISTER" => "[USER_REGISTER] ".GetMessage("MAIN_EVENTLOG_USER_REGISTER"),
	"USER_REGISTER_FAIL" => "[USER_REGISTER_FAIL] ".GetMessage("MAIN_EVENTLOG_USER_REGISTER_FAIL"),
);

$sTableID = "tbl_event_log";
$oSort = new CAdminSorting($sTableID, "ID", "DESC");
$lAdmin = new CAdminList($sTableID, $oSort);

$arFilterFields = Array(
	"find",
	"find_type",
	"find_id",
	"find_timestamp_x_1",
	"find_timestamp_x_2",
	"find_severity",
	"find_audit_type_id",
	"find_module_id",
	"find_item_id",
	"find_site_id",
	"find_user_id",
	"find_guest_id",
	"find_remote_addr",
	"find_user_agent",
	"find_request_uri",
);
function CheckFilter()
{
	$str = "";
	if(strlen($_REQUEST["find_timestamp_x_1"])>0)
	{
		if(!CheckDateTime($_REQUEST["find_timestamp_x_1"], CSite::GetDateFormat("FULL")))
			$str.= GetMessage("MAIN_EVENTLOG_WRONG_TIMESTAMP_X_FROM")."<br>";
	}
	if(strlen($_REQUEST["find_timestamp_x_2"])>0)
	{
		if(!CheckDateTime($_REQUEST["find_timestamp_x_2"], CSite::GetDateFormat("FULL")))
			$str.= GetMessage("MAIN_EVENTLOG_WRONG_TIMESTAMP_X_TO")."<br>";
	}

	if(strlen($str) > 0)
	{
		global $lAdmin;
		$lAdmin->AddFilterError($str);
		return false;
	}

	return true;
}


$arFilter = Array();
$lAdmin->InitFilter($arFilterFields);
InitSorting();

if(CheckFilter())
{
	$audit_type_id_filter = ($find != '' && $find_type == "audit_type_id" ? $find : $find_audit_type_id);
	if(strlen($find_audit_type) > 0)
	{
		if(strlen($audit_type_id_filter) > 0)
			$audit_type_id_filter = "(".$audit_type_id_filter.")|".$find_audit_type;
		else
			$audit_type_id_filter = $find_audit_type;
	}
	$arFilter = Array(
		"TIMESTAMP_X_1" => $find_timestamp_x_1,
		"TIMESTAMP_X_2" => $find_timestamp_x_2,
		"SEVERITY" => is_array($find_severity) && count($find_severity) > 0? implode("|", $find_severity): "",
		"AUDIT_TYPE_ID" => $audit_type_id_filter,
		"MODULE_ID" => $find_module_id,
		"ITEM_ID" => $find_item_id,
		"SITE_ID" => $find_site_id,
		"USER_ID" => ($find != '' && $find_type == "user_id" ? $find : $find_user_id),
		"GUEST_ID" => $find_guest_id,
		"REMOTE_ADDR" => ($find != '' && $find_type == "remote_addr" ? $find : $find_remote_addr),
		"REQUEST_URI" => $find_request_uri,
		"USER_AGENT" => ($find != '' && $find_type == "user_agent" ? $find : $find_user_agent),
	);
}

$rsData = CEventLog::GetList(array($by => $order), $arFilter);
$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart(20);
$lAdmin->NavText($rsData->GetNavPrint(GetMessage("MAIN_EVENTLOG_LIST_PAGE")));

$arHeaders = array(
	array(
		"id" => "ID",
		"content" => GetMessage("MAIN_EVENTLOG_ID"),
		"sort" => "ID",
		"default" => true,
		"align" => "right",
	),
	array(
		"id" => "TIMESTAMP_X",
		"content" => GetMessage("MAIN_EVENTLOG_TIMESTAMP_X"),
		"sort" => "TIMESTAMP_X",
		"default" => true,
		"align" => "right",
	),
	array(
		"id" => "SEVERITY",
		"content" => GetMessage("MAIN_EVENTLOG_SEVERITY"),
	),
	array(
		"id" => "AUDIT_TYPE_ID",
		"content" => GetMessage("MAIN_EVENTLOG_AUDIT_TYPE_ID"),
		"default" => true,
	),
	array(
		"id" => "MODULE_ID",
		"content" => GetMessage("MAIN_EVENTLOG_MODULE_ID"),
	),
	array(
		"id" => "ITEM_ID",
		"content" => GetMessage("MAIN_EVENTLOG_ITEM_ID"),
		"default" => true,
	),
	array(
		"id" => "REMOTE_ADDR",
		"content" => GetMessage("MAIN_EVENTLOG_REMOTE_ADDR"),
		"default" => true,
	),
	array(
		"id" => "USER_AGENT",
		"content" => GetMessage("MAIN_EVENTLOG_USER_AGENT"),
	),
	array(
		"id" => "REQUEST_URI",
		"content" => GetMessage("MAIN_EVENTLOG_REQUEST_URI"),
		"default" => true,
	),
	array(
		"id" => "SITE_ID",
		"content" => GetMessage("MAIN_EVENTLOG_SITE_ID"),
	),
	array(
		"id" => "USER_ID",
		"content" => GetMessage("MAIN_EVENTLOG_USER_ID"),
		"default" => true,
	),
	array(
		"id" => "DESCRIPTION",
		"content" => GetMessage("MAIN_EVENTLOG_DESCRIPTION"),
		"default" => true,
	),
);
if($bStatistic)
	$arHeaders[] = array(
		"id" => "GUEST_ID",
		"content" => GetMessage("MAIN_EVENTLOG_GUEST_ID"),
	);

$lAdmin->AddHeaders($arHeaders);

$arUsersCache = array();

while($db_res = $rsData->NavNext(true, "a_"))
{
	$row =& $lAdmin->AddRow($a_ID, $db_res);
	$row->AddViewField("AUDIT_TYPE_ID", array_key_exists($a_AUDIT_TYPE_ID, $arAuditTypes)? preg_replace("/^\\[.*?\\]\\s+/", "", $arAuditTypes[$a_AUDIT_TYPE_ID]): $a_AUDIT_TYPE_ID);
	if($bStatistic && strlen($a_GUEST_ID))
	{
		$row->AddViewField("GUEST_ID", '<a href="/bitrix/admin/hit_list.php?lang='.LANGUAGE_ID.'&amp;set_filter=Y&amp;find_guest_id='.$a_GUEST_ID.'&amp;find_guest_id_exact_match=Y">'.$a_GUEST_ID.'</a>');
	}
	if($a_USER_ID)
	{
		if(!array_key_exists($a_USER_ID, $arUsersCache))
		{
			$rsUser = CUser::GetByID($a_USER_ID);
			if($arUser = $rsUser->GetNext())
			{
				$arUser["FULL_NAME"] = $arUser["NAME"].(strlen($arUser["NAME"])<=0 || strlen($arUser["LAST_NAME"])<=0?"":" ").$arUser["LAST_NAME"];
			}
			$arUsersCache[$a_USER_ID] = $arUser;
		}
		if($arUsersCache[$a_USER_ID])
			$row->AddViewField("USER_ID", '[<a href="user_edit.php?lang='.LANG.'&ID='.$a_USER_ID.'">'.$a_USER_ID.'</a>] '.$arUsersCache[$a_USER_ID]["FULL_NAME"]);
	}
	if($a_ITEM_ID)
	{
		switch($a_AUDIT_TYPE_ID)
		{
		case "USER_AUTHORIZE":
		case "USER_LOGOUT":
		case "USER_REGISTER":
		case "USER_INFO":
		case "USER_PASSWORD_CHANGED":
		case "USER_DELETE":
			if(!array_key_exists($a_ITEM_ID, $arUsersCache))
			{
				$rsUser = CUser::GetByID($a_ITEM_ID);
				if($arUser = $rsUser->GetNext())
				{
					$arUser["FULL_NAME"] = $arUser["NAME"].(strlen($arUser["NAME"])<=0 || strlen($arUser["LAST_NAME"])<=0?"":" ").$arUser["LAST_NAME"];
				}
				$arUsersCache[$a_ITEM_ID] = $arUser;
			}
			if($arUsersCache[$a_ITEM_ID])
				$row->AddViewField("ITEM_ID", '[<a href="user_edit.php?lang='.LANG.'&ID='.$a_ITEM_ID.'">'.$a_ITEM_ID.'</a>] '.$arUsersCache[$a_ITEM_ID]["FULL_NAME"]);
		break;
		}
	}
	if(strlen($a_REQUEST_URI))
	{
		$row->AddViewField("REQUEST_URI", htmlspecialchars($a_REQUEST_URI));
	}
}

$lAdmin->AddFooter(array(
	array(
		"title" => GetMessage("MAIN_ADMIN_LIST_SELECTED"),
		"value" => $rsData->SelectedRowsCount()
	),
));

$aContext = array();
$lAdmin->AddAdminContextMenu($aContext);

$APPLICATION->SetTitle(GetMessage("MAIN_EVENTLOG_PAGE_TITLE"));
$lAdmin->CheckListMode();

require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");
?>
<form name="find_form" method="GET" action="<?echo $APPLICATION->GetCurPage()?>?">
<input type="hidden" name="lang" value="<?echo LANG?>">
<?
$arFilterNames = array(
	"find_id" => GetMessage("MAIN_EVENTLOG_ID"),
	"find_timestamp_x" => GetMessage("MAIN_EVENTLOG_TIMESTAMP_X"),
	"find_severity" => GetMessage("MAIN_EVENTLOG_SEVERITY"),
	"find_audit_type_id" => GetMessage("MAIN_EVENTLOG_AUDIT_TYPE_ID"),
	"find_module_id" => GetMessage("MAIN_EVENTLOG_MODULE_ID"),
	"find_item_id" => GetMessage("MAIN_EVENTLOG_ITEM_ID"),
	"find_site_id" => GetMessage("MAIN_EVENTLOG_SITE_ID"),
	"find_user_id" => GetMessage("MAIN_EVENTLOG_USER_ID"),
	"find_guest_id" => GetMessage("MAIN_EVENTLOG_GUEST_ID"),
	"find_remote_addr" => GetMessage("MAIN_EVENTLOG_REMOTE_ADDR"),
	"find_user_agent" => GetMessage("MAIN_EVENTLOG_USER_AGENT"),
	"find_request_uri" => GetMessage("MAIN_EVENTLOG_REQUEST_URI"),
);
if(!$bStatistic)
	unset($arFilterNames["find_guest_id"]);

$oFilter = new CAdminFilter($sTableID."_filter", $arFilterNames);
$oFilter->Begin();
?>
<tr>
	<td><b><?echo GetMessage("MAIN_EVENTLOG_SEARCH")?>:</b></td>
	<td nowrap>
		<input type="text" size="25" name="find" value="<?echo htmlspecialchars($find)?>">
		<select name="find_type">
			<option value="audit_type_id"<?if($find_type=="audit_type_id") echo " selected"?>><?echo GetMessage("MAIN_EVENTLOG_AUDIT_TYPE_ID")?></option>
			<option value="user_id"<?if($find_type=="user_id") echo " selected"?>><?echo GetMessage("MAIN_EVENTLOG_USER_ID")?></option>
			<option value="remote_addr"<?if($find_type=="remote_addr") echo " selected"?>><?echo GetMessage("MAIN_EVENTLOG_REMOTE_ADDR")?></option>
			<option value="user_agent"<?if($find_type=="user_agent") echo " selected"?>><?echo GetMessage("MAIN_EVENTLOG_USER_AGENT")?></option>
		</select>
	</td>
</tr>
<tr>
	<td><?echo GetMessage("MAIN_EVENTLOG_ID")?>:</td>
	<td><input type="text" name="find_id" size="47" value="<?echo htmlspecialchars($find_id)?>"></td>
</tr>
<tr>
	<td><?echo GetMessage("MAIN_EVENTLOG_TIMESTAMP_X")?>:</td>
	<td><?echo CAdminCalendar::CalendarPeriod("find_timestamp_x_1", "find_timestamp_x_2", $find_timestamp_x_1, $find_timestamp_x_2, false, 15, true)?></td>
</tr>
<tr>
	<td><?echo GetMessage("MAIN_EVENTLOG_SEVERITY")?>:</td>
	<td><?echo SelectBoxMFromArray("find_severity[]", array(
		"REFERENCE"    => array("SECURITY", "WARNING"),
		"REFERENCE_ID" => array("SECURITY", "WARNING"),
		), $find_severity, GetMessage("MAIN_ALL"))?></td>
</tr>
<tr>
	<td><?echo GetMessage("MAIN_EVENTLOG_AUDIT_TYPE_ID")?>:</td>
	<td>
		<input type="text" name="find_audit_type_id" size="47" value="<?echo htmlspecialchars($find_audit_type_id)?>">&nbsp;<?=ShowFilterLogicHelp()?><br>
		<?echo SelectBoxFromArray("find_audit_type", array("reference"=>array_values($arAuditTypes),"reference_id"=>array_keys($arAuditTypes)), $find_audit_type, GetMessage("MAIN_ALL"), "");?>
	</td>
</tr>
<tr>
	<td><?echo GetMessage("MAIN_EVENTLOG_MODULE_ID")?>:</td>
	<td><input type="text" name="find_module_id" size="47" value="<?echo htmlspecialchars($find_module_id)?>">&nbsp;<?=ShowFilterLogicHelp()?></td>
</tr>
<tr>
	<td><?echo GetMessage("MAIN_EVENTLOG_ITEM_ID")?>:</td>
	<td><input type="text" name="find_item_id" size="47" value="<?echo htmlspecialchars($find_item_id)?>">&nbsp;<?=ShowFilterLogicHelp()?></td>
</tr>
<?
$arSiteDropdown = array("reference" => array(), "reference_id" => array());
$rs = CSite::GetList(($v1="sort"), ($v2="asc"));
while ($ar = $rs->Fetch())
{
	$arSiteDropdown["reference_id"][] = $ar["ID"];
	$arSiteDropdown["reference"][]    = "[".$ar["ID"]."] ".$ar["NAME"];
}
?>
<tr>
	<td><?echo GetMessage("MAIN_EVENTLOG_SITE_ID")?>:</td>
	<td><?echo SelectBoxFromArray("find_site_id", $arSiteDropdown, $find_site_id, GetMessage("MAIN_ALL"), "");?></td>
</tr>
<tr>
	<td><?echo GetMessage("MAIN_EVENTLOG_USER_ID")?>:</td>
	<td><input type="text" name="find_user_id" size="47" value="<?echo htmlspecialchars($find_user_id)?>"></td>
</tr>
<?if($bStatistic):?>
<tr>
	<td><?echo GetMessage("MAIN_EVENTLOG_GUEST_ID")?>:</td>
	<td><input type="text" name="find_guest_id" size="47" value="<?echo htmlspecialchars($find_guest_id)?>"></td>
</tr>
<?endif?>
<tr>
	<td><?echo GetMessage("MAIN_EVENTLOG_REMOTE_ADDR")?>:</td>
	<td><input type="text" name="find_remote_addr" size="47" value="<?echo htmlspecialchars($find_remote_addr)?>">&nbsp;<?=ShowFilterLogicHelp()?></td>
</tr>
<tr>
	<td><?echo GetMessage("MAIN_EVENTLOG_USER_AGENT")?>:</td>
	<td><input type="text" name="find_user_agent" size="47" value="<?echo htmlspecialchars($find_user_agent)?>">&nbsp;<?=ShowFilterLogicHelp()?></td>
</tr>
<tr>
	<td><?echo GetMessage("MAIN_EVENTLOG_REQUEST_URI")?>:</td>
	<td><input type="text" name="find_request_uri" size="47" value="<?echo htmlspecialchars($find_request_uri)?>">&nbsp;<?=ShowFilterLogicHelp()?></td>
</tr>
<?
$oFilter->Buttons(array("table_id"=>$sTableID, "url"=>$APPLICATION->GetCurPage(), "form"=>"find_form"));
$oFilter->End();
?>
</form>
<?

$lAdmin->DisplayList();

require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");
?>

