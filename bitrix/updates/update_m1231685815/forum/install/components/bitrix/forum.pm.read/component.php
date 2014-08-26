<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if (!CModule::IncludeModule("forum")):
	ShowError(GetMessage("F_NO_MODULE"));
	return 0;
elseif (!$USER->IsAuthorized()):
	$APPLICATION->AuthForm(GetMessage("PM_AUTH"));
	return 0;
endif;
// *****************************************************************************************
if(!function_exists("GetUserName"))
{
	function GetUserName($USER_ID)
	{
		$ar_res = false;
		if (IntVal($USER_ID)>0)
		{
			$db_res = CUser::GetByID(IntVal($USER_ID));
			$ar_res = $db_res->Fetch();
		}

		if (!$ar_res)
		{
			$db_res = CUser::GetByLogin($USER_ID);
			$ar_res = $db_res->Fetch();
		}

		$USER_ID = IntVal($ar_res["ID"]);
		$f_LOGIN = htmlspecialcharsex($ar_res["LOGIN"]);

		$forum_user = CForumUser::GetByUSER_ID($USER_ID);
		if (($forum_user["SHOW_NAME"]=="Y") && (strlen(trim($ar_res["NAME"]))>0 || strlen(trim($ar_res["LAST_NAME"]))>0))
		{
			return trim(htmlspecialcharsex($ar_res["NAME"])." ". htmlspecialcharsex($ar_res["LAST_NAME"]));
		}
		else
			return $f_LOGIN;
	}
}

// ************************* Input params***************************************************************
// ************************* BASE **********************************************************************
	$arParams["version"] = intVal(COption::GetOptionString("forum", "UsePMVersion", "2"));
	$arParams["FID"] = intVal(intVal($arParams["FID"]) > 0 ? $arParams["FID"] : $_REQUEST["FID"]);
	if ($arParams["version"] == 2 && $arParams["FID"] == 2)
		$arParams["FID"] = 3;
	$arParams["MID"] = intVal(intVal($arParams["MID"]) > 0 ? $arParams["MID"] : $_REQUEST["MID"]);
	$arParams["UID"] = intVal($USER->GetID());
// ************************* URL ***********************************************************************
	$URL_NAME_DEFAULT = array(
		"pm_list" => "PAGE_NAME=pm_list&FID=#FID#",
		"pm_read" => "PAGE_NAME=pm_read&FID=#FID#&MID=#MID#",
		"pm_edit" => "PAGE_NAME=pm_edit&FID=#FID#&MID=#MID#&mode=#mode#",
		"pm_folder" => "PAGE_NAME=pm_folder",
		"profile_view" => "PAGE_NAME=profile_view&UID=#UID#");

	InitSorting();
	global $by, $order;

	foreach ($URL_NAME_DEFAULT as $URL => $URL_VALUE)
	{
		if (strLen(trim($arParams["URL_TEMPLATES_".strToUpper($URL)])) <= 0)
			$arParams["URL_TEMPLATES_".strToUpper($URL)] = $APPLICATION->GetCurPageParam($URL_VALUE, array("PAGE_NAME", "FID", "TID", "UID", "MID", "action", "mode", "sessid", BX_AJAX_PARAM_ID));
		$arParams["~URL_TEMPLATES_".strToUpper($URL)] = $arParams["URL_TEMPLATES_".strToUpper($URL)];
		if (!empty($by))
		{
			$arParams["~URL_TEMPLATES_".strToUpper($URL)] = ForumAddPageParams($arParams["URL_TEMPLATES_".strToUpper($URL)], 
				array("by" => $by, "order" => $order), false, false);
		}
		
		$arParams["URL_TEMPLATES_".strToUpper($URL)] = htmlspecialchars($arParams["~URL_TEMPLATES_".strToUpper($URL)]);
	}
// ************************* ADDITIONAL ****************************************************************
	$arParams["DATE_TIME_FORMAT"] = trim(empty($arParams["DATE_TIME_FORMAT"]) ? $DB->DateFormatToPHP(CSite::GetDateFormat("FULL")) : $arParams["DATE_TIME_FORMAT"]);
	$arParams["PATH_TO_SMILE"] = trim($arParams["PATH_TO_SMILE"]);
	$arParams["SET_NAVIGATION"] = ($arParams["SET_NAVIGATION"] == "N" ? "N" : "Y");
// ************************* CACHE & TITLE *************************************************************
	$arParams["SET_TITLE"] = ($arParams["SET_TITLE"] == "N" ? "N" : "Y");
// *************************/Input params***************************************************************

// ************************* Default params*************************************************************
	$arError = array();
	$arOK = array();
	$APPLICATION->ResetException();
	$arNotification = array();
	$message = array($arParams["MID"]);
	$action = strToLower($_REQUEST["action"]);
	$result = strToLower($_REQUEST["result"]);
	
	$arResult["ERROR_MESSAGE"] = "";
	$arResult["OK_MESSAGE"] = "";
	$arResult["CURRENT_PAGE"] = CComponentEngine::MakePathFromTemplate($arParams["URL_TEMPLATES_PM_READ"], 
		array("FID" => $arParams["FID"], "MID" => $arParams["MID"]));
	$arResult["MESSAGE"] = array();
	$arResult["MESSAGE_PREV"] = array();
	$arResult["MESSAGE_NEXT"] = array();
	if (empty($by))
	{
		$by = "post_date";
		$order = "desc";
	}
	$parser = new textParser(LANGUAGE_ID, $arParams["PATH_TO_SMILE"]);
// *************************/Default params*************************************************************

	if ($arParams["MID"] <= 0)
	{
		LocalRedirect(ForumAddPageParams(
			CComponentEngine::MakePathFromTemplate($arParams["URL_TEMPLATES_PM_LIST"], array("FID" => $arParams["FID"])),
				array("result" => "no_mid")));
	}
	$db_res = CForumPrivateMessage::GetListEx(array(), array("ID" => $arParams["MID"]));
	if(!($db_res && ($res = $db_res->GetNext())))
	{
		LocalRedirect(ForumAddPageParams(
			CComponentEngine::MakePathFromTemplate($arParams["URL_TEMPLATES_PM_LIST"], array("FID" => $arParams["FID"])),
			array("result" => "no_mid")));
	}
	elseif (!CForumPrivateMessage::CheckPermissions($arParams["MID"])) 
	{
		LocalRedirect(ForumAddPageParams(
			CComponentEngine::MakePathFromTemplate($arParams["URL_TEMPLATES_PM_LIST"], array("FID" => $arParams["FID"])),
			array("result" => "no_perm")));
		die();
	}
	if ($arParams["FID"] != 2)
		$arParams["FID"] = intVal($res["FOLDER_ID"]);
	$arResult["MESSAGE"] = $res;
	ForumSetLastVisit();
// ************************* Action ********************************************************************
	if(($res["IS_READ"] != "Y") && ($arParams["FID"] != 2))
	{
		CForumPrivateMessage::MakeRead($arParams["MID"]);
		BXClearCache(true, "/bitrix/forum/user/".$USER->GetId()."/");
	}
	
	if (!empty($action))
	{
		$arFilter = array(
			"USER_ID"=>$arParams["UID"], 
			"FOLDER_ID"=>$arParams["FID"]);
		if ($arParams["FID"] == 2) //If this is outbox folder
			$arFilter = array("OWNER_ID" => $arParams["UID"]);
		$db_res = CForumPrivateMessage::GetListEx(array($by=>$order), $arFilter);
		$next = array();
		if($db_res && ($res = $db_res->Fetch()))
		{
			$bFound = false;
			do 
			{
				if ($bFound)
				{
					$next = $res;
					break;
				}
				if ($res["ID"] == $arParams["MID"])
					$bFound = true;
				
			}while ($res = $db_res->Fetch());
		}
		if (!check_bitrix_sessid())
		{
			$arError[] = array(
				"code" => "bad_sessid",
				"title" => GetMessage("F_ERROR_BAD_SESSID"));
		}
		elseif (!(is_array($message) && !empty($message)))
		{
			$arError[] = array(
				"code" => "bad_data",
				"title" => GetMessage("PM_ERR_NO_DATA"));
		}
		else
		{
			switch($action)
			{
				case "delete":
					foreach ($message as $MID) 
					{
						if (!CForumPrivateMessage::CheckPermissions($MID))
						{
							$arError[] = array(
								"code" => "bad_permission_".$MID,
								"title" => str_replace("#MID#", $MID, GetMessage("PM_ERR_DELETE_NO_PERM")));
						}
						elseif(!CForumPrivateMessage::Delete($MID, array("FOLDER_ID"=>4,)))
						{
							$arError[] = array(
								"code" => "not_delete_".$MID,
								"title" => str_replace("#MID#", $MID, GetMessage("PM_ERR_DELETE")));
						}
						else 
						{
							$arOk[] = array(
								"code" => "delete_".$MID,
								"title" => str_replace("#MID#", $MID, GetMessage("PM_OK_DELETE")));
						}
					}
					break;
				case "copy":
				case "move":
					$folder_id = intVal($_REQUEST["folder_id"]);
					$arrVars = array(
						"FOLDER_ID" => intVal($folder_id),
						"USER_ID" => $USER->GetId(),
						"IS_READ" => "Y");
					if ($folder_id <= 0)
					{
						$arError[] = array(
							"code" => "empty_folder_id_",
							"title" => GetMessage("PM_ERR_MOVE_NO_FOLDER"));
					}
					else
					{
						foreach ($message as $MID) 
						{
							if (!CForumPrivateMessage::CheckPermissions($MID))
							{
								$arError[] = array(
									"code" => "bad_permission_".$MID,
									"title" => str_replace("#MID#", intVal($MID), GetMessage("PM_ERR_MOVE_NO_PERM")));
							}
							elseif (($action == "move" && !CForumPrivateMessage::Update($MID, $arrVars)) ||
								($action == "copy" && !CForumPrivateMessage::Copy($MID, $arrVars)))
							{
								if ($APPLICATION->GetException())
								{
									$err = $APPLICATION->GetException();
									if ($err)
									{
										$arError[] = array(
											"code" => "bad_".$action."_".$MID,
											"title" => $err->GetString());
									}
								}
							}
							else 
							{
								$arOk[] = array(
									"code" => $action."_".$MID,
									"title" => str_replace("#MID#", $MID, GetMessage("PM_OK_MOVE")));
							}
						}
					}
					break;
				case "send_notification": 
					if ($arParams["version"] == 2 && $arResult["MESSAGE"]["REQUEST_IS_READ"] == "Y")
					{
						$arNotification["POST_SUBJ"] = GetMessage("SYSTEM_POST_SUBJ");
						$arNotification["POST_MESSAGE"] = GetMessage("SYSTEM_POST_MESSAGE");
						$arNotification["FIELDS"] = 
							array(
								"USER_NAME" => $arResult["MESSAGE"]["RECIPIENT_NAME"], 
								"USER_ID" => $arResult["MESSAGE"]["RECIPIENT_ID"], 
								"USER_LINK" => CComponentEngine::MakePathFromTemplate($arParams["~URL_TEMPLATES_PROFILE_VIEW"], array("UID" => $arResult["MESSAGE"]["RECIPIENT_ID"])),
								"SUBJECT" => $arResult["MESSAGE"]["~POST_SUBJ"], 
								"MESSAGE" => $arResult["MESSAGE"]["~POST_MESSAGE"], 
								"MESSAGE_DATE" => $arResult["MESSAGE"]["POST_DATE"], 
								"MESSAGE_LINK" => CComponentEngine::MakePathFromTemplate($arParams["~URL_TEMPLATES_PM_READ"], array("FID" => "1", "MID" => $arResult["MESSAGE"]["ID"])), 
								"SERVER_NAME" => $_SERVER["SERVER_NAME"],
							);
						foreach ($arNotification["FIELDS"] as $key => $val)
							$arNotification["POST_MESSAGE"] = str_replace("#".$key."#", $val, $arNotification["POST_MESSAGE"]);
							
						$arFields = array(
							"AUTHOR_ID" => $USER->GetID(),
							"USER_ID" => $arResult["MESSAGE"]["AUTHOR_ID"],
							"POST_SUBJ" => $arNotification["POST_SUBJ"],
							"POST_MESSAGE" => $arNotification["POST_MESSAGE"],
							"USE_SMILES" => "Y",
						);
						if($newMID = CForumPrivateMessage::Send($arFields))
						{
							BXClearCache(true, "/bitrix/forum/user/".$arResult["MESSAGE"]["AUTHOR_ID"]."/");
							if (!empty($arResult["MESSAGE"]["AUTHOR_EMAIL"]))
							{
								$event = new CEvent;
								$arSiteInfo = $event->GetSiteFieldsArray(SITE_ID);
								$arFields = Array(
									"FROM_NAME" => $arResult["MESSAGE"]["RECIPIENT_NAME"],
									"FROM_USER_ID" => $USER->GetId(),
									"FROM_EMAIL" => $arSiteInfo["DEFAULT_EMAIL_FROM"],
									"TO_NAME" => $arResult["MESSAGE"]["AUTHOR_NAME"],
									"TO_USER_ID" => $arResult["MESSAGE"]["AUTHOR_ID"],
									"TO_EMAIL" => $arResult["MESSAGE"]["AUTHOR_EMAIL"],
									"SUBJECT" => $arNotification["POST_SUBJ"],
									"MESSAGE" => $parser->convert4mail($arNotification["POST_MESSAGE"]),
									"MESSAGE_DATE" => date("d.m.Y H:i:s"),
									"MESSAGE_LINK" => "http://#SERVER_NAME#".CComponentEngine::MakePathFromTemplate($arParams["~URL_TEMPLATES_PM_READ"], array("FID" => "1", "MID" => $newMID)),
								);
								if ($event->Send("NEW_FORUM_PRIVATE_MESSAGE", SITE_ID, $arFields))
								{
									$arOK[] = array(
										"code" => "send",
										"title" => GetMessage("PM_NOTIFICATION_SEND"));
								}
							}
						}
					}
					break;
			}
		}
		
		if (empty($arError))
		{
			if (!empty($next))
			{
				LocalRedirect(CComponentEngine::MakePathFromTemplate($arParams["~URL_TEMPLATES_PM_READ"], 
					array("FID" => $arParams["FID"], "MID" => $next["ID"])));
			}
			else 
			{
				LocalRedirect(CComponentEngine::MakePathFromTemplate($arParams["URL_TEMPLATES_PM_LIST"], 
					array("FID" => $arParams["FID"])));
			}
		}
		
		if (!empty($arError))
		{
			$arRes = array();
			foreach ($arError as $res)
				$arRes[] = (empty($res["title"]) ? $res["code"] : $res["title"]);
			$arResult["ERROR_MESSAGE"] = implode(".<br />", $arRes).".";
		}
		if (!empty($arOk))
		{
			$arRes = array();
			foreach ($arOk as $res)
				$arRes[] = (empty($res["title"]) ? $res["code"] : $res["title"]);
			$arResult["OK_MESSAGE"] = implode(".<br />", $arRes).".";
		}
	}
// *************************/Action ********************************************************************

// ************************* Page **********************************************************************
	$arResult["MESSAGE"]["POST_MESSAGE"] = $parser->convert(
		$arResult["MESSAGE"]["~POST_MESSAGE"], 
		array(
			"HTML" => "N",
			"ANCHOR" => "Y",
			"BIU" => "Y",
			"IMG" => "Y",
			"LIST" => "Y",
			"QUOTE" => "Y",
			"CODE" => "Y",
			"FONT" => "Y",
			"SMILES" => $arResult["MESSAGE"]["USE_SMILES"],
			"UPLOAD" => "N",
			"NL2BR" => "N"));
	$arResult["MESSAGE"]["RECIPIENT_LINK"] = CComponentEngine::MakePathFromTemplate($arParams["URL_TEMPLATES_PROFILE_VIEW"], 
		array("UID" => $arResult["MESSAGE"]["RECIPIENT_ID"]));
	$arResult["MESSAGE"]["AUTHOR_LINK"] = CComponentEngine::MakePathFromTemplate($arParams["URL_TEMPLATES_PROFILE_VIEW"], 
		array("UID" => $arResult["MESSAGE"]["AUTHOR_ID"]));
	$arResult["MESSAGE"]["POST_DATE"] = CForumFormat::DateFormat($arParams["DATE_TIME_FORMAT"], 
		MakeTimeStamp($arResult["MESSAGE"]["POST_DATE"], CSite::GetDateFormat()));
// ************************* Pagen *********************************************************************
	$arFilter = array(
		"USER_ID"=>$arParams["UID"], 
		"FOLDER_ID"=>$arParams["FID"]);
	if ($arParams["FID"] == 2) //If this is outbox folder
		$arFilter = array("OWNER_ID" => $arParams["UID"]);
	$db_res = CForumPrivateMessage::GetListEx(array($by=>$order), $arFilter);
	$prev = array();
	$next = array();
	$bFound = false;
	if($db_res && ($res = $db_res->Fetch()))
	{
		do 
		{
			if ($bFound)
			{
				$next = $res;
				break;
			}
			if ($res["ID"] == $arParams["MID"])
				$bFound = true;
			if (!$bFound)
				$prev = $res;
			
		}while ($res = $db_res->Fetch());
	}
	
	if (!empty($next))
	{
		$arResult["MESSAGE_NEXT"] = $next;
		$arResult["MESSAGE_NEXT"]["MESSAGE_LINK"] = CComponentEngine::MakePathFromTemplate($arParams["URL_TEMPLATES_PM_READ"], 
			array("FID" => $arParams["FID"], "MID" => $next["ID"]));
	}
	if (!empty($prev))
	{
		$arResult["MESSAGE_PREV"] = $prev;
		$arResult["MESSAGE_PREV"]["MESSAGE_LINK"] = CComponentEngine::MakePathFromTemplate($arParams["URL_TEMPLATES_PM_READ"], 
			array("FID" => $arParams["FID"], "MID" => $prev["ID"]));
	}
	
	$arResult["pm_edit"] = CComponentEngine::MakePathFromTemplate($arParams["URL_TEMPLATES_PM_EDIT"], 
		array("FID"=>$arParams["FID"], "mode" => "edit", "MID" => $arParams["MID"], "UID" => $arResult["MESSAGE"]["RECIPIENT_ID"]));
	$arResult["pm_reply"] = CComponentEngine::MakePathFromTemplate($arParams["URL_TEMPLATES_PM_EDIT"], 
		array("FID"=>$arParams["FID"], "mode" => "reply", "MID" => $arParams["MID"], "UID" => $arResult["MESSAGE"]["AUTHOR_ID"]));
	$arResult["pm_list"] = CComponentEngine::MakePathFromTemplate($arParams["URL_TEMPLATES_PM_LIST"], 
		array("FID" => $arParams["FID"]));
	$arResult["SystemFolder"] = FORUM_SystemFolder;
	
	$resFolder = CForumPMFolder::GetList(array(), array("USER_ID" => $USER->GetID()));
	$arResult["UserFolder"] = "N";
	if (($resFolder) && ($resF = $resFolder->GetNext()))
	{
		$arResult["UserFolder"] = array();
		do
		{
			$arResult["UserFolder"][$resF["ID"]] = $resF;
		}
		while ($resF = $resFolder->GetNext());
	}
	$arResult["count"] = CForumPrivateMessage::PMSize($USER->GetID(), COption::GetOptionInt("forum", "MaxPrivateMessages", 100));
	$arResult["count"] = round($arResult["count"]*100);
	
	$arResult["FolderName"] = ($arParams["FID"] <= $arResult["SystemFolder"]) ? GetMessage("PM_FOLDER_ID_".$arParams["FID"]) : 
		$arResult["UserFolder"][$arParams["FID"]]["TITLE"];
// *************************/Page **********************************************************************

// ************************* Only for custom components ************************************************
	$arResult["sessid"] = bitrix_sessid_post();
	$arResult["FID"] = $arParams["FID"];
	$arResult["MID"] = $arParams["MID"];
	if ((intVal($arResult["FID"]) > 1) && (intVal($arResult["FID"]) <=3))
	{
		$arResult["StatusUser"] = "RECIPIENT";
		$arResult["InputOutput"] = "RECIPIENT_ID";
		$arResult["recipient"]["name"] = $arResult["MESSAGE"]["RECIPIENT_NAME"];
		$arResult["recipient"]["profile_view"] = CComponentEngine::MakePathFromTemplate($arParams["URL_TEMPLATES_PROFILE_VIEW"], 
			array("UID" => $arResult["MESSAGE"]["RECIPIENT_ID"]));
	}
	else
	{
		$arResult["StatusUser"] = "SENDER";
		$arResult["InputOutput"] = "AUTHOR_ID";
		$arResult["recipient"]["name"] = $arResult["MESSAGE"]["AUTHOR_NAME"];
		$arResult["recipient"]["profile_view"] = CComponentEngine::MakePathFromTemplate($arParams["URL_TEMPLATES_PROFILE_VIEW"], 
			array("UID" => $arResult["MESSAGE"]["AUTHOR_ID"]));
	}
	$arResult["NameUser"] = $arResult["recipient"]["name"];
// *************************/Only for custom components ************************************************

if ($arParams["SET_NAVIGATION"] != "N")
{
	$APPLICATION->AddChainItem(GetMessage("PM_TITLE_NAV"), CComponentEngine::MakePathFromTemplate($arParams["URL_TEMPLATES_PM_FOLDER"], 
		array()));
	$APPLICATION->AddChainItem($arResult["FolderName"], $arResult["pm_list"]);
	$APPLICATION->AddChainItem($res["POST_SUBJ"]);
}
if ($arParams["SET_TITLE"] != "N")
{
	$APPLICATION->SetTitle(str_replace("#SUBJECT#", $arResult["MESSAGE"]["POST_SUBJ"], GetMessage("PM_TITLE")));
}

$this->IncludeComponentTemplate();

?>