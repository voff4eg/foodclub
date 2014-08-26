<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if (!CModule::IncludeModule("forum")):
	ShowError(GetMessage("F_NO_MODULE"));
	return 0;
endif;
// ************************* Input params***************************************************************
// ************************* BASE **********************************************************************
	$arParams["FID"] = intVal((intVal($arParams["FID"]) <= 0 ? $_REQUEST["FID"] : $arParams["FID"]));
	$GLOBALS["FID"] = $arParams["FID"]; // for top panel
	$arParams["TID"] = intVal((intVal($arParams["TID"]) <= 0 ? $_REQUEST["TID"] : $arParams["TID"]));
	$arParams["MID_UNREAD"] = (strLen(trim($arParams["MID"])) <= 0 ? $_REQUEST["MID"] : $arParams["MID"]);
	$arParams["MID"] = (is_array($arParams["MID"]) ? 0 : intVal($arParams["MID"]));
	if (strToLower($arParams["MID_UNREAD"]) == "unread_mid")
		$arParams["MID"] = intVal(ForumGetFirstUnreadMessage($arParams["FID"], $arParams["TID"]));
	$arParams["MESSAGES_PER_PAGE"] = intVal(empty($arParams["MESSAGES_PER_PAGE"]) ? 
		COption::GetOptionString("forum", "MESSAGES_PER_PAGE", "10") : $arParams["MESSAGES_PER_PAGE"]);
// ************************* URL ***********************************************************************
	$URL_NAME_DEFAULT = array(
			"index" => "",
			"list" => "PAGE_NAME=list&FID=#FID#",
			"read" => "PAGE_NAME=read&FID=#FID#&TID=#TID#",
			"message" => "PAGE_NAME=message&FID=#FID#&TID=#TID#&MID=#MID#",
			"profile_view" => "PAGE_NAME=profile_view&UID=#UID#",
			"subscr_list" => "PAGE_NAME=subscr_list",
			"pm_edit" => "PAGE_NAME=pm_edit&FID=#FID#&MID=#MID#&UID=#UID#&mode=#mode#",
			"message_send" => "PAGE_NAME=message_send&UID=#UID#&TYPE=#TYPE#",
			"message_move" => "PAGE_NAME=message_move&FID=#FID#&TID=#TID#&MID=#MID#",
			"topic_new" => "PAGE_NAME=topic_new&FID=#FID#",
			"topic_move" => "PAGE_NAME=topic_move&FID=#FID#&TID=#TID#");
		
	if (empty($arParams["URL_TEMPLATES_MESSAGE"]) && !empty($arParams["URL_TEMPLATES_READ"]))
	{
		$arParams["URL_TEMPLATES_MESSAGE"] = $arParams["URL_TEMPLATES_READ"];
	}
	foreach ($URL_NAME_DEFAULT as $URL => $URL_VALUE)
	{
		if (strLen(trim($arParams["URL_TEMPLATES_".strToUpper($URL)])) <= 0)
			$arParams["URL_TEMPLATES_".strToUpper($URL)] = $APPLICATION->GetCurPageParam($URL_VALUE, 
				array("PAGE_NAME", "FID", "TID", "UID", "MID", "ACTION", "sessid", "SEF_APPLICATION_CUR_PAGE_URL", 
					"AJAX_TYPE", "AJAX_CALL", BX_AJAX_PARAM_ID, "result", "order"));
		$arParams["~URL_TEMPLATES_".strToUpper($URL)] = $arParams["URL_TEMPLATES_".strToUpper($URL)];
		$arParams["URL_TEMPLATES_".strToUpper($URL)] = htmlspecialchars($arParams["~URL_TEMPLATES_".strToUpper($URL)]);
	}
// ************************* ADDITIONAL ****************************************************************
	$arParams["PAGEN"] = (intVal($arParams["PAGEN"]) <= 0 ? 1 : intVal($arParams["PAGEN"]));

	$arParams["PATH_TO_SMILE"] = trim($arParams["PATH_TO_SMILE"]);
	$arParams["PATH_TO_ICON"] = trim($arParams["PATH_TO_ICON"]);
	
	$arParams["WORD_LENGTH"] = intVal($arParams["WORD_LENGTH"]);
	$arParams["IMAGE_SIZE"] = (intVal($arParams["IMAGE_SIZE"]) > 0 ? $arParams["IMAGE_SIZE"] : 300);

	// Data and data-time format
	$arParams["DATE_FORMAT"] = trim(empty($arParams["DATE_FORMAT"]) ? $DB->DateFormatToPHP(CSite::GetDateFormat("SHORT")) : $arParams["DATE_FORMAT"]);
	$arParams["DATE_TIME_FORMAT"] = trim(empty($arParams["DATE_TIME_FORMAT"]) ? $DB->DateFormatToPHP(CSite::GetDateFormat("FULL")) : $arParams["DATE_TIME_FORMAT"]);
	// AJAX
	if ($arParams["AJAX_TYPE"] == "Y" || ($arParams["AJAX_TYPE"] == "A" && COption::GetOptionString("main", "component_ajax_on", "Y") == "Y"))
		$arParams["AJAX_TYPE"] = "Y";
	else
		$arParams["AJAX_TYPE"] = "N";
	$arParams["AJAX_CALL"] = (($arParams["AJAX_TYPE"] == "Y" && $_REQUEST["AJAX_CALL"] == "Y") ? "Y" : "N");
	$arParams["SET_NAVIGATION"] = ($arParams["SET_NAVIGATION"] == "N" ? "N" : "Y");
	$arParams["ADD_INDEX_NAV"] = ($arParams["ADD_INDEX_NAV"] == "Y" ? "Y" : "N");
	$arParams["DISPLAY_PANEL"] = ($arParams["DISPLAY_PANEL"] == "Y" ? "Y" : "N");
// ************************* CACHE & TITLE *************************************************************
	if ($arParams["CACHE_TYPE"] == "Y" || ($arParams["CACHE_TYPE"] == "A" && COption::GetOptionString("main", "component_cache_on", "Y") == "Y"))
		$arParams["CACHE_TIME"] = intval($arParams["CACHE_TIME"]);
	else
		$arParams["CACHE_TIME"] = 0;

	$arParams["SET_TITLE"] = ($arParams["SET_TITLE"] == "N" ? "N" : "Y");
// *************************/Input params***************************************************************

// *************************Default params**************************************************************
	$arMessage = array(); 
	$arResult["TOPIC"] = array();
	$arResult["FORUM"] = array();
	$UserInfo = array();
	
	$db_res = false;
	$res = false;
	$arFilter = array();
	
	$arOk = array();
	$action = false;
	if (!empty($_REQUEST["ACTION"]))
		$action = $_REQUEST["ACTION"];
	elseif ($_POST["MESSAGE_TYPE"]=="REPLY")
		$action = "REPLY";
	elseif (($_REQUEST["TOPIC_SUBSCRIBE"] == "Y") || ($_REQUEST["FORUM_SUBSCRIBE"] == "Y"))
		$action = "SUBSCRIBE";
	$number = 1;
	$strErrorMessage = "";
	$strOKMessage = "";
	$View = false;
	$bVarsFromForm = false;
	$arError = array();
	$arNote = array();
	
	switch (strToLower($_REQUEST["result"]))
	{
		case "message_add":
		case "mid_add":
		case "reply":
				$strOKMessage = GetMessage("F_MESS_SUCCESS_ADD");
			break;
			
		case "show": $strOKMessage = GetMessage("F_MESS_SUCCESS_SHOW"); break;
		case "hide": $strOKMessage = GetMessage("F_MESS_SUCCESS_HIDE"); break;
		case "del":	$strOKMessage = GetMessage("F_MESS_SUCCESS_DEL"); break;
		
		case "top": 	$strOKMessage = GetMessage("F_TOPIC_SUCCESS_TOP"); break;
		case "ordinary": 	$strOKMessage = GetMessage("F_TOPIC_SUCCESS_ORD"); break;
		case "open": 	$strOKMessage = GetMessage("F_TOPIC_SUCCESS_OPEN"); break;
		case "close": 	$strOKMessage = GetMessage("F_TOPIC_SUCCESS_CLOSE"); break;
		
		case "VOTE4USER":
			$arFields = array(
				"UID" => $_GET["UID"],
				"VOTES" => $_GET["VOTES"],
				"VOTE" => (($_GET["VOTES_TYPE"]=="U") ? True : False));
			$url = CComponentEngine::MakePathFromTemplate(
				$arParams["URL_TEMPLATES_MESSAGE"], 
				array("FID" => $arParams["FID"], 
					"TID" => $arParams["FID"], 
					"MID" => (intVal($_REQUEST["MID"]) > 0 ? $_REQUEST["MID"] : "s")
				));
			break;
		case "FORUM_SUBSCRIBE":
		case "TOPIC_SUBSCRIBE":
		case "FORUM_SUBSCRIBE_TOPICS":
			$arFields = array(
				"FID" => $arParams["FID"],
				"TID" => (($action=="FORUM_SUBSCRIBE")?0:$arParams["TID"]),
				"NEW_TOPIC_ONLY" => (($action=="FORUM_SUBSCRIBE_TOPICS")?"Y":"N"));
			$url = ForumAddPageParams(
					CComponentEngine::MakePathFromTemplate(
						$arParams["~URL_TEMPLATES_SUBSCR_LIST"], 
						array()
					), 
					array("FID" => $arParams["FID"], "TID" => $arParams["TID"]));
			break;
		case "mid_for_move_is_empty":
			$strErrorMessage = "mid_for_move_is_empty"; 
			break;
	}
	unset($_GET["result"]);
	DeleteParam(array("result"));
	
	$parser = new textParser(LANGUAGE_ID, $arParams["PATH_TO_SMILE"], $arParams["CACHE_TIME"]);
	$parser->MaxStringLen = $arParams["WORD_LENGTH"];
	$parser->image_params["width"] = $arParams["IMAGE_SIZE"];
	$parser->image_params["height"] = $arParams["IMAGE_SIZE"];
	
	$cache = new CPHPCache;
// *************************/Default params*************************************************************


// *****************************************************************************************************
	if ($arParams["MID"]>0)
	{
		$res = CForumMessage::GetByIDEx($arParams["MID"], array("GET_TOPIC_INFO" => "Y", "GET_FORUM_INFO" => "Y"));
		if (is_array($res))
		{
			$arParams["TID"] = intVal($res["TOPIC_ID"]);
			$arParams["FID"] = intVal($res["FORUM_ID"]);
			$arResult["TOPIC"] = $res["TOPIC_INFO"];
			$arResult["FORUM"] = $res["FORUM_INFO"];
		}
	}
	else
	{
		$res = CForumTopic::GetByIDEx($arParams["TID"], array("GET_FORUM_INFO" => "Y")); 
		if (is_array($res))
		{
			$arParams["FID"] = intVal($res["FORUM_ID"]);
			$arResult["TOPIC"] = $res;
			$arResult["FORUM"] = $res["FORUM_INFO"];
		}
	}
	if (empty($arResult["TOPIC"]))
	{
		$arError = array(
			"code" => "tid_is_lost",
			"title" => GetMessage("F_ERROR_TID_IS_LOST"),
			"link" => CComponentEngine::MakePathFromTemplate($arParams["~URL_TEMPLATES_LIST"], array("FID" => $arParams["FID"])));
	}
	elseif (($arResult["TOPIC"]["STATE"] == "L") && (intVal($arResult["TOPIC"]["TOPIC_ID"]) > 0))
	{
		$res_temp = CForumTopic::GetByID($arResult["TOPIC"]["TOPIC_ID"]); 
		if ($res_temp)
		{
			$arNote = array(
				"code" => "tid_moved",
				"title" => GetMessage("F_ERROR_TID_MOVED"),
				"link" => CComponentEngine::MakePathFromTemplate($arParams["~URL_TEMPLATES_READ"], 
					array("FID" => $arResult["TOPIC"]["FORUM_ID"], "TID" => $arResult["TOPIC"]["TOPIC_ID"], "MID" => "s")));
		}
		else 
		{
			$arError = array(
				"code" => "tid_is_lost",
				"title" => GetMessage("F_ERROR_TID_IS_LOST"),
				"link" => CComponentEngine::MakePathFromTemplate($arParams["~URL_TEMPLATES_LIST"], 
					array("FID" => $arResult["TOPIC"]["FORUM_ID"])));
		}
	}
	elseif (ForumCurrUserPermissions($arParams["FID"]) < "E")
	{
		$APPLICATION->AuthForm(GetMessage("F_FPERMS"));
	}
	elseif (!CForumTopic::CanUserViewTopic($arParams["TID"], $USER->GetUserGroupArray()))
	{
	// Topic is approve? For moderation forum.
		$arError = array(
			"code" => "tid_not_approved",
			"title" => GetMessage("F_ERROR_TID_NOT_APPROVED"),
			"link" => CComponentEngine::MakePathFromTemplate($arParams["~URL_TEMPLATES_LIST"], 
				array("FID" => $arParams["FID"])));
	}
	else
	{
		$arResult["UserPermission"] = ForumCurrUserPermissions($arParams["FID"]);
		foreach ($arResult["TOPIC"] as $key => $val)
		{
			$arResult["TOPIC"]["~".$key] = $val;
			$arResult["TOPIC"][$key] = $parser->wrap_long_words(htmlspecialcharsEx($val));
		}
		foreach ($arResult["FORUM"] as $key => $val)
		{
			$arResult["FORUM"]["~".$key] = $val;
			$arResult["FORUM"][$key] = htmlspecialcharsEx($val);
		}
		
		$cache_id = "forum_forum_".$arParams["FID"];
		$cache_path = "/".SITE_ID."/forum/forum/".$arParams["FID"]."/";
		
		if ($arParams["CACHE_TIME"] > 0)
		{
			$cache->StartDataCache($arParams["CACHE_TIME"], $cache_id, $cache_path);
			$cache->EndDataCache(array("arForum"=>$arResult["FORUM"]));
		}
	}
// **************************** Redirect if error ******************************************
	if (!empty($arNote["link"]) || !empty($arError))
	{
		if ($arParams["AJAX_CALL"] == "N" && !empty($arError))
		{
			ShowError($arError["title"]);
			return false;
			//LocalRedirect(ForumAddPageParams($arError["link"], array("error" => $arError["code"])));
		}
		elseif ($arParams["AJAX_CALL"] == "N" && !empty($arNote["link"]))
		{
			LocalRedirect(ForumAddPageParams($arNote["link"], array("result" => $arNote["action"])));
		}
		elseif ($arParams["AJAX_CALL"] == "Y")
		{
			$APPLICATION->RestartBuffer();
			?><?=CUtil::PhpToJSObject(
				array(
					"error" => $arError,
					"note" => $arNote
					))?><?
			die();
		}
	}
// **************************** Redirect if error ******************************************
	ForumSetLastVisit($arParams["FID"], $arParams["TID"]);
// **************************** ACTION *****************************************************
	include("action.php");
// **************************** /ACTION *****************************************************
	ForumSetReadTopic($arParams["FID"], $arParams["TID"]);
	
	if ($arParams["AJAX_CALL"] == "Y")
	{
		$APPLICATION->RestartBuffer();
		?><?=CUtil::PhpToJSObject(
			array(
			"error" => array(
				"code" => $action,
				"title" => $strErrorMessage),
			"note" => $arNote));
		die();
	}
	elseif (!empty($arNote["link"]))
	{
		LocalRedirect(ForumAddPageParams($arNote["link"], array("result" => $arNote["code"]), true, false).(!empty($arParams["MID"]) ? "#message".$arParams["MID"] : ""));
	}
	include_once("functions.php"); // For customs templates only
	// ************************* Message List **************************************************
	$arResult["CURRENT_PAGE"] = CComponentEngine::MakePathFromTemplate($arParams["URL_TEMPLATES_READ"], 
		array("FID" => $arParams["FID"], "TID" => $arParams["TID"], "MID" => "s"));

	if ((intVal($_REQUEST["PAGEN_".$arParams["PAGEN"]]) > 1) && (intVal($arParams["MID"]) <= 0))
		$arResult["CURRENT_PAGE"] = ForumAddPageParams($arResult["CURRENT_PAGE"], 
			array("PAGEN_".$arParams["PAGEN"] => intVal($_REQUEST["PAGEN_".$arParams["PAGEN"]])));
// *****************************************************************************************
// *****************************************************************************************
	$arResult["ERROR_MESSAGE"] = $strErrorMessage;
	$arResult["OK_MESSAGE"] = $strOKMessage;
//*************************!Making page*************************************************
	unset($_GET["MID"]);
	unset($GLOBALS["HTTP_GET_VARS"]["MID"]);
	unset($_GET["ACTION"]);
	unset($GLOBALS["HTTP_GET_VARS"]["ACTION"]);
	// For correct page navigation. But may be it`s the future bug. 
	$_SERVER["REQUEST_URI"] = $arResult["CURRENT_PAGE"];
	$arAllow = array(
		"HTML" => $arResult["FORUM"]["ALLOW_HTML"],
		"ANCHOR" => $arResult["FORUM"]["ALLOW_ANCHOR"],
		"BIU" => $arResult["FORUM"]["ALLOW_BIU"],
		"IMG" => $arResult["FORUM"]["ALLOW_IMG"],
		"LIST" => $arResult["FORUM"]["ALLOW_LIST"],
		"QUOTE" => $arResult["FORUM"]["ALLOW_QUOTE"],
		"CODE" => $arResult["FORUM"]["ALLOW_CODE"],
		"FONT" => $arResult["FORUM"]["ALLOW_FONT"],
		"SMILES" => $arResult["FORUM"]["ALLOW_SMILES"],
		"UPLOAD" => $arResult["FORUM"]["ALLOW_UPLOAD"],
		"NL2BR" => $arResult["FORUM"]["ALLOW_NL2BR"]
	);
	// LAST MESSAGE
	$db_res = CForumMessage::GetList(array("ID"=>"DESC"), array("TOPIC_ID"=>$arParams["TID"]), false, 1);
	$arResult["TOPIC"]["iLAST_TOPIC_MESSAGE"] = "";
	if (($db_res) && ($res = $db_res->Fetch()))
		$arResult["TOPIC"]["iLAST_TOPIC_MESSAGE"] = intVal($res["ID"]);
	// NUMBER CURRENT PAGE
	$iNumPage = 0;
	if ($arParams["MID"] > 0)
		$iNumPage = CForumMessage::GetMessagePage($arParams["MID"], $arParams["MESSAGES_PER_PAGE"], $USER->GetUserGroupArray());
	// Create filter and additional fields for message select
	$arFilter = array("TOPIC_ID" => $arParams["TID"]);
	if ($arResult["UserPermission"] < "Q") {
		$arFilter["APPROVED"] = "Y";}
	if ($USER->IsAuthorized()) {$arFilter["POINTS_TO_AUTHOR_ID"] = $USER->GetID();}
	$arFields = array("bDescPageNumbering"=>false, "nPageSize"=>$arParams["MESSAGES_PER_PAGE"], "bShowAll" => false);
	if ($iNumPage > 0) {$arFields["iNumPage"] = $iNumPage;}
	
	//********************* Pagen ******************************************************
	CPageOption::SetOptionString("main", "nav_page_in_session", "N");
	$db_res = CForumMessage::GetListEx(
		array("ID"=>"ASC"), $arFilter, false, false, 
		$arFields);
	if (intVal($iNumPage) > 0)
		$db_res->NavStart($arParams["MESSAGES_PER_PAGE"], false, $iNumPage);
	else 
		$db_res->NavStart($arParams["MESSAGES_PER_PAGE"], false);
	//**********************************************************************************
	$arResult["NAV_RESULT"] = $db_res;
	$arResult["NAV_STRING"] = $db_res->GetPageNavStringEx($navComponentObject, GetMessage("F_TITLE_NAV"), $arParams["PAGE_NAVIGATION_TEMPLATE"]);
	$number = intVal($db_res->NavPageNomer-1)*$arParams["MESSAGES_PER_PAGE"] + 1;
	//********************* Check rights current user **********************************
	$arResult["Rank"] = CForumUser::GetUserRank(intVal($USER->GetParam("USER_ID")));
	$arResult["bCanUserDeleteMessages"] = CForumTopic::CanUserDeleteTopicMessage($arParams["TID"], $USER->GetUserGroupArray(), $USER->GetID());
	$arResult["CanUserAddTopic"] = CForumTopic::CanUserAddTopic($arParams["FID"], $USER->GetUserGroupArray(), $USER->GetID(), $arResult["FORUM"]);
	//*********************/Check rights current user **********************************
	// New topic. Check rights & paths.
	$arResult["topic_new"] = CComponentEngine::MakePathFromTemplate($arParams["URL_TEMPLATES_TOPIC_NEW"], array("FID" => $arParams["FID"]));
	$arResult["list"] = CComponentEngine::MakePathFromTemplate($arParams["URL_TEMPLATES_LIST"], array("FID" => $arResult["FORUM"]["ID"]));
	$arResult["MESSAGE_LIST"] = array();
	//**********************************************************************************
	while ($res = $db_res->GetNext())
	{
		$arUser = array();
		if (($res["AUTHOR_ID"]>0) && (!isset($UserInfo[$res["AUTHOR_ID"]])))
		{
			$arUser["Groups"] = CUser::GetUserGroup($res["AUTHOR_ID"]);
			if (!in_array(2, $arUser["Groups"]))
				$arUser["Groups"][] = 2;
			$arUser["Perms"] = CForumNew::GetUserPermission($res["FORUM_ID"], $arUser["Groups"]);
			if (($arUser["Perms"]<="Q") && (COption::GetOptionString("forum", "SHOW_VOTES", "Y")=="Y"))
				$arUser["Rank"] = CForumUser::GetUserRank($res["AUTHOR_ID"], LANGUAGE_ID);
				
			if (intVal($res["POINTS"]) > 0)
				$arUser["Points"] = array("POINTS" => $res["POINTS"], "DATE_UPDATE" => $res["DATE_UPDATE"]);
			else
				$arUser["Points"] = false;
			$UserInfo[$res["AUTHOR_ID"]] = $arUser;
		}
		elseif(($res["AUTHOR_ID"]>0) && (isset($UserInfo[$res["AUTHOR_ID"]])))
		{
			$arUser = $UserInfo[$res["AUTHOR_ID"]];
		}
		
		$res["AUTHOR_ID"] = intVal($res["AUTHOR_ID"]);
	
		$res["AUTHOR_STATUS"] = "";
		if ($res["AUTHOR_ID"]>0)
		{
			if ($arUser["Perms"]=="Q") 
				$res["AUTHOR_STATUS"] = GetMessage("F_MODERATOR");
			elseif ($arUser["Perms"]=="U") 
				$res["AUTHOR_STATUS"] = GetMessage("F_EDITOR");
			elseif ($arUser["Perms"]=="Y") 
				$res["AUTHOR_STATUS"] = GetMessage("F_ADMIN");
			elseif (COption::GetOptionString("forum", "SHOW_VOTES", "Y")=="Y")
				$res["AUTHOR_STATUS"] = $arUser["Rank"]["NAME"];
			elseif ($arParams["SHOW_DEFAULT_RANK"] == "Y") 
				$res["AUTHOR_STATUS"] = GetMessage("F_USER");
		}
		else 
		{
			$res["AUTHOR_STATUS"] = GetMessage("F_GUEST");
		}
		
		$res["profile_view"] = CComponentEngine::MakePathFromTemplate($arParams["URL_TEMPLATES_PROFILE_VIEW"], array("UID" => $res["AUTHOR_ID"]));
		$res["message_link"] = CComponentEngine::MakePathFromTemplate($arParams["URL_TEMPLATES_MESSAGE"], 
			array(
				"FID" => $arParams["FID"], 
				"TID" => $arParams["TID"],
				"MID" => $res["ID"]));
		if (strLen($res["AVATAR"])>0)
		{
			// ******************************************************************************************
			$cache_id = "forum_avatar_".$res["AVATAR"];
			$cache_path = "/".SITE_ID."/forum/avatar/".$res["AVATAR"]."/";
			if ($arParams["CACHE_TIME"] > 0 && $cache->InitCache($arParams["CACHE_TIME"], $cache_id, $cache_path))
			{
				$cache_result = $cache->GetVars();
				if (is_array($cache_result["AVATAR"]) && (count($cache_result["AVATAR"]) > 0) && ($cache_result["AVATAR"]["ID"] == $res["AVATAR"]))
					$res["AVATAR"] = $cache_result["AVATAR"];
			}
			else
			{
				if ($arParams["CACHE_TIME"] > 0)
					$cache->StartDataCache($arParams["CACHE_TIME"], $cache_id, $cache_path);
				$res["AVATAR"] = array("ID" => $res["AVATAR"]);
				$res["AVATAR"]["FILE"] = CFile::GetFileArray($res["AVATAR"]["ID"]);
				$res["AVATAR"]["HTML"] = CFile::ShowImage($res["AVATAR"]["FILE"]["SRC"], COption::GetOptionString("forum", "avatar_max_width", 90), COption::GetOptionString("forum", "avatar_max_height", 90), "border=\"0\" vspace=\"5\" hspace=\"5\"", "", true);
				
				if ($arParams["CACHE_TIME"] > 0)
					$cache->EndDataCache(array("AVATAR" => $res["AVATAR"]));
			}
			// *****************************************************************************************
		}
		$res["AUTHOR_NAME"] = $parser->wrap_long_words($res["AUTHOR_NAME"]);
		$res["DESCRIPTION"] = $parser->wrap_long_words($res["DESCRIPTION"]);
		
		//************************message number********************************************
		$res["NUMBER"] = $number++;
		//************************message text**********************************************
		$arAllow["SMILES"] = $arResult["FORUM"]["ALLOW_SMILES"];
		if ($res["USE_SMILES"]!="Y") 
			$arAllow["SMILES"] = "N";
			
		$res["~POST_MESSAGE_TEXT"] = (COption::GetOptionString("forum", "FILTER", "Y")=="Y" ? $res["~POST_MESSAGE_FILTER"] : $res["~POST_MESSAGE"]);
		$res["POST_MESSAGE_TEXT"] = $parser->convert($res["~POST_MESSAGE_TEXT"], $arAllow);
		//************************message attach img****************************************
		$res["ATTACH_IMG"] = "";
		$res["~ATTACH_FILE"] = array();
		$res["ATTACH_FILE"] = array();
		if (intVal($res["~ATTACH_IMG"])>0 && ($arResult["FORUM"]["ALLOW_UPLOAD"]=="Y" || 
			$arResult["FORUM"]["ALLOW_UPLOAD"]=="F" || $arResult["FORUM"]["ALLOW_UPLOAD"]=="A"))
		{
			$res["~ATTACH_FILE"] = CFile::GetFileArray($res["~ATTACH_IMG"]);
			$res["ATTACH_IMG"] = CFile::ShowFile($res["~ATTACH_IMG"], 0, 
				$arParams["IMAGE_SIZE"], $arParams["IMAGE_SIZE"], true, "border=0", false);
			$res["ATTACH_FILE"] = $res["ATTACH_IMG"];
		}
		
		//************************user signature********************************************
		if (strLen($res["SIGNATURE"])>0)
		{
			$arAllow["SMILES"] = "N";
			$res["SIGNATURE"] = $parser->convert($res["~SIGNATURE"], $arAllow);
		}
		
		//************************user info*************************************************
		if (($res["AUTHOR_ID"] > 0))
		{
//			$res["message_send"] = CComponentEngine::MakePathFromTemplate($arParams["URL_TEMPLATES_MESSAGE_SEND"], array("UID" => $res["AUTHOR_ID"]));
			$res["email"] = CComponentEngine::MakePathFromTemplate($arParams["URL_TEMPLATES_MESSAGE_SEND"], array("UID" => $res["AUTHOR_ID"], "TYPE" => "email"));
			$res["icq"] = CComponentEngine::MakePathFromTemplate($arParams["URL_TEMPLATES_MESSAGE_SEND"], array("UID" => $res["AUTHOR_ID"], "TYPE" => "icq"));
			$res["pm_edit"] = CComponentEngine::MakePathFromTemplate($arParams["URL_TEMPLATES_PM_EDIT"], array("FID" => 0, "MID" => 0, "UID" => $res["AUTHOR_ID"], "mode" => "new"));
			//********************Voting****************************************************
			$res["VOTES"] = array("ACTION" => "N");
			if (COption::GetOptionString("forum", "SHOW_VOTES", "Y")=="Y" && $USER->IsAuthorized() && 
				($USER->IsAdmin() || (intVal($USER->GetID())!=$res["AUTHOR_ID"])))
			{
				$strNotesText = "";
				$bVote = "N";
				$bUnVote = "N";
				if ($arUser["Points"])
				{
					$bUnVote = "Y";
					$strNotesText = str_replace("#POINTS#", $arUser["Points"]["POINTS"], 
						str_replace("#END#", ForumNumberEnding($arUser["Points"]["POINTS"]), GetMessage("F_YOU_ALREADY_VOTE1"))).". ";

					if (intVal($arUser["Points"]["POINTS"]) < intVal($arResult["Rank"]["VOTES"]))
					{
						$bVote = "Y";
						$strNotesText .= str_replace("#POINTS#", (intVal($arUser["Points"]["VOTES"])-intVal($arUser["Points"]["POINTS"])), 
							str_replace("#END#", ForumNumberEnding((intVal($arResult["Rank"]["VOTES"])-intVal($arUser["Points"]["POINTS"]))), GetMessage("F_YOU_ALREADY_VOTE3")));
					}
					if ($USER->IsAdmin())
						$strNotesText .= GetMessage("F_VOTE_ADMIN");
				}
				else
				{
					if (intVal($arResult["Rank"]["VOTES"])>0 || $USER->IsAdmin())
					{
						$bVote = "Y";
						$strNotesText = GetMessage("F_NO_VOTE").
							str_replace("#POINTS#", (intVal($arResult["Rank"]["VOTES"])-intVal($arUser["Points"]["POINTS"])), 
							str_replace("#END#", ForumNumberEnding((intVal($arResult["Rank"]["VOTES"])-intVal($arUser["Points"]["POINTS"]))), 
								GetMessage("F_NO_VOTE1"))).". ";
						if ($USER->IsAdmin())
							$strNotesText .= GetMessage("F_VOTE_ADMIN");
					}
				}
				if (($bVote == "Y") || ($bUnVote == "Y"))
				{
					$res["VOTES"]["ACTION"] = ($bVote == "Y" ? "VOTE" : "UNVOTE");
					
					$res["VOTES"]["link"] = ForumAddPageParams(
											$res["message_link"],
											array(
												"UID" => $res["AUTHOR_ID"],
												"MID" => $res["ID"],
												"VOTES" => intVal($arResult["Rank"]["VOTES"]),
												"VOTES_TYPE" => (($bVote == "Y") ? "V" : "U"),
												"ACTION" => "VOTE4USER"											
												))."&amp;".bitrix_sessid_get();
				}
			}
		}
		//********************Panel Edit (SHOW_PANEL)**********************************
		$res["SHOW_PANEL"] = "N";
		$res["SHOW_HIDE"] = array("ACTION" => "N");
		$res["MESSAGE_DELETE"] = array("ACTION" => "N");
		$res["MESSAGE_SUPPORT"] = array("ACTION" => "N");
		$res["MESSAGE_EDIT"] = array("ACTION" => "N");
		
		// SHOW_HIDE
		if ($arResult["UserPermission"] >= "Q")
		{
			$res["SHOW_PANEL"] = "Y";
			if ($res["APPROVED"]=="Y")
			{
				$res["SHOW_HIDE"]["ACTION"] = "HIDE";
				$res["SHOW_HIDE"]["link"] = ForumAddPageParams($res["message_link"], 
					array("MID" => $res["ID"], "ACTION" => "HIDE"))."&amp;".bitrix_sessid_get();
			}
			else
			{
				$res["SHOW_HIDE"]["ACTION"] = "SHOW";
				$res["SHOW_HIDE"]["link"] = ForumAddPageParams($res["message_link"], 
					array("MID" => $res["ID"], "ACTION" => "SHOW"))."&amp;".bitrix_sessid_get();
			}
		}
		
		// MESSAGE_DELETE
		if ($arResult["bCanUserDeleteMessages"])
		{
			$res["SHOW_PANEL"] = "Y";
			if ($arResult["bCanUserDeleteMessages"])
			{
				$res["MESSAGE_DELETE"]["ACTION"] = "DELETE";
				$res["MESSAGE_DELETE"]["link"] = ForumAddPageParams($res["message_link"], 
					array("MID" => $res["ID"], "ACTION" => "DEL"))."&amp;".bitrix_sessid_get();
				if($res["AUTHOR_ID"]>0 && CModule::IncludeModule("support"))
				{
					$res["MESSAGE_SUPPORT"]["ACTION"] = "SUPPORT";
					$res["MESSAGE_SUPPORT"]["link"] = ForumAddPageParams($res["message_link"], 
						array("MID" => $res["ID"], "ACTION" => "FORUM_MESSAGE2SUPPORT"))."&amp;".bitrix_sessid_get();
				}
			}
		}
		// MESSAGE_EDIT
		if (($USER->IsAuthorized() &&
			(((COption::GetOptionString("forum", "USER_EDIT_OWN_POST", "N") == "Y") && 
				($res["AUTHOR_ID"] == $USER->GetId())) || 
			(COption::GetOptionString("forum", "USER_EDIT_OWN_POST", "N") != "Y" && 
				($res["AUTHOR_ID"] == intVal($USER->GetParam("USER_ID"))) &&
				($arResult["TOPIC"]["iLAST_TOPIC_MESSAGE"] == intVal($res["ID"]))))) ||
			($arResult["UserPermission"] > "Q"))
		{
			$res["SHOW_PANEL"] = "Y";
			$res["MESSAGE_EDIT"]["ACTION"] = "EDIT";
			$res["MESSAGE_EDIT"]["link"] = 
				ForumAddPageParams(
					CComponentEngine::MakePathFromTemplate(
						$arParams["URL_TEMPLATES_TOPIC_NEW"], 
						array("FID" => $arParams["FID"])
					), 
					array("TID" => $arParams["TID"], "MID" => $res["ID"], "MESSAGE_TYPE" => "EDIT")
				)."&amp;".bitrix_sessid_get();
		}
		// IP
		if ($arResult["UserPermission"]>="Q")
		{
			$bIP = false;
			if (ereg("^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$", $res["~AUTHOR_IP"]))
				$bIP = True;
				
			if ($bIP)
				$res["AUTHOR_IP"] = GetWhoisLink($res["~AUTHOR_IP"], "");
			else
				$res["AUTHOR_IP"] = $res["AUTHOR_IP"];

			$bIP = false;
			if (ereg("^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$", $res["~AUTHOR_REAL_IP"]))
				$bIP = True;
				
			if ($bIP)
				$res["AUTHOR_REAL_IP"] =  GetWhoisLink($res["~AUTHOR_REAL_IP"], "");
			else
				$res["AUTHOR_REAL_IP"] = $res["AUTHOR_REAL_IP"];
				
			$res["IP_IS_DIFFER"] = "N";
			if($res["AUTHOR_IP"] <> $res["AUTHOR_REAL_IP"]):
				$res["IP_IS_DIFFER"] = "Y";
			endif;
		}
			
		// CModule::IncludeModule("statistic")
		$res["SHOW_STATISTIC"] = "N";
		if (CModule::IncludeModule("statistic") && intVal($res["GUEST_ID"])>0 && $APPLICATION->GetGroupRight("statistic")!="D")
		{
			$res["SHOW_STATISTIC"] = "Y";
		}
		$res["SHOW_AUTHOR_ID"] = "N";
		if (intVal($res["AUTHOR_ID"])> 0 && $APPLICATION->GetGroupRight("main")>="R")
		{
			$res["SHOW_AUTHOR_ID"] = "Y";
		}
		$res["FOR_JS"]["AUTHOR_NAME"] = Cutil::JSEscape(htmlspecialchars($res["~AUTHOR_NAME"]));
		
		if (COption::GetOptionString("forum", "FILTER", "Y")=="Y")
			$res["FOR_JS"]["POST_MESSAGE"] = $res["~POST_MESSAGE_FILTER"];
		else 
			$res["FOR_JS"]["POST_MESSAGE"] = $res["~POST_MESSAGE"];
		$res["FOR_JS"]["POST_MESSAGE"] = Cutil::JSEscape(htmlspecialchars($res["FOR_JS"]["POST_MESSAGE"]));
		
// *****************************************************************************************
		if (strLen(trim($res["DATE_REG"])) > 0)
		{
			$res["DATE_REG"] = CForumFormat::DateFormat($arParams["DATE_FORMAT"], MakeTimeStamp($res["DATE_REG"], CSite::GetDateFormat()));
		}
		if (strLen(trim($res["POST_DATE"])) > 0)
		{
			$res["POST_DATE"] = CForumFormat::DateFormat($arParams["DATE_TIME_FORMAT"], MakeTimeStamp($res["POST_DATE"], CSite::GetDateFormat()));
		}
		$res["MESSAGE_ANCHOR"] = CComponentEngine::MakePathFromTemplate($arParams["URL_TEMPLATES_MESSAGE"], array("FID" => $arParams["FID"], "TID" => $arParams["TID"], "MID" => $res["ID"]));
// *****************************************************************************************
		if (!empty($res["EDITOR_ID"]))
		{
			$res["EDITOR_LINK"] = CComponentEngine::MakePathFromTemplate($arParams["URL_TEMPLATES_PROFILE_VIEW"], array("UID" => $res["EDITOR_ID"]));
		}
		
		if (strLen(trim($res["EDIT_DATE"])) > 0)
		{
			$res["EDIT_DATE"] = CForumFormat::DateFormat($arParams["DATE_TIME_FORMAT"], MakeTimeStamp($res["EDIT_DATE"], CSite::GetDateFormat()));
		}

// *****************************************************************************************
		$arResult["MESSAGE_LIST"][] = $res;
	}
	
	
// VIEW
	$arResult["VIEW"] = "N";
	if ($View)
	{
		$arResult["VIEW"] = "Y";
		
		$arAllow["SMILES"] = $arResult["FORUM"]["ALLOW_SMILES"];
		if ($_POST["USE_SMILES"]!="Y") 
			$arAllow["SMILES"] = "N";
		$arResult["POST_MESSAGE_VIEW"] = $parser->convert($_POST["POST_MESSAGE"], $arAllow);
	}
	
// *****************************************************************************************
	$res = ShowActiveUser(array("PERIOD" => 600, "TITLE" => "", "FORUM_ID" => $arParams["FID"], "TOPIC_ID" => $arParams["TID"]));
	$res["SHOW_USER"] = "N";
	if ($res["NONE"] != "Y")
	{
		$arUser = array();
		if (is_array($res["USER"]) && (count($res["USER"]) > 0))
		{
			foreach ($res["USER"] as $r)
			{
				$r["SHOW_NAME"] = $parser->wrap_long_words($r["SHOW_NAME"]);
				$r["profile_view"] = CComponentEngine::MakePathFromTemplate($arParams["URL_TEMPLATES_PROFILE_VIEW"], array("UID" => $r["UID"]));
				$arUser[] = $r;
			}
			if (count($arUser) > 0)
			{
				$res["SHOW_USER"] = "Y";
			}	
			$res["USER"] = $arUser;
		}
	}
	$arResult["UserOnLine"] = $res;
	$arResult["bVarsFromForm"] = $bVarsFromForm;
	$arResult["SHOW_PANEL_GUEST"] = ($USER->IsAuthorized() ? "N" : "Y");
	$arResult["SHOW_PANEL_ATTACH_IMG"] = "N";
	$arResult["SHOW_SUBSCRIBE"] = "N";
	$arResult["CAPTCHA_CODE"] = "";
	$arResult["FORUM_SUBSCRIBE"] = "N";
	$arResult["TOPIC_SUBSCRIBE"] = "N";
	
	/* Attach Panel */
	if ($arResult["FORUM"]["ALLOW_UPLOAD"]=="Y" || $arResult["FORUM"]["ALLOW_UPLOAD"]=="F" || $arResult["FORUM"]["ALLOW_UPLOAD"]=="A")
	{
		$arPanel["SHOW_PANEL_ATTACH_IMG"] = "Y";
		if (strlen($arPost["ATTACH_IMG"])>0)
		{
			$arPost["~ATTACH_FILE"] = CFile::GetFileArray($arPost["ATTACH_IMG"]);
			if ($arPost["~ATTACH_FILE"] !== false)
				$arPost["ATTACH_FILE"] = CFile::ShowImage($arPost["~ATTACH_FILE"]["SRC"], 200, 200, "border=0");
		}
	}
	
	/* Subscribe Panel */
	if ($USER->IsAuthorized() && (ForumCurrUserPermissions($FID) > "E"))
	{
		$arResult["SHOW_SUBSCRIBE"] = "Y";
		$arFields = array(
			"USER_ID" => $USER->GetID(),
			"FORUM_ID" => $FID,
			"SITE_ID" => LANG
			);
		$db_res = CForumSubscribe::GetList(array(), $arFields);
		if ($db_res)
		{
			while ($res = $db_res->Fetch())
			{
				if (intVal($res["TOPIC_ID"]) <= 0)
				{
					$arResult["FORUM_SUBSCRIBE"] = "Y";
				}
				elseif($res["TOPIC_ID"] == $TID) 
				{
					$arResult["TOPIC_SUBSCRIBE"] = "Y";
				}
			}
		}
	}

	/* Captcha Panel */
	if (!$USER->IsAuthorized() && $arResult["FORUM"]["USE_CAPTCHA"]=="Y")
	{
		include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/captcha.php");
		$cpt = new CCaptcha();
		$captchaPass = COption::GetOptionString("main", "captcha_password", "");
		if (strlen($captchaPass) <= 0)
		{
			$captchaPass = randString(10);
			COption::SetOptionString("main", "captcha_password", $captchaPass);
		}
		$cpt->SetCodeCrypt($captchaPass);
		$arResult["CAPTCHA_CODE"] = htmlspecialchars($cpt->GetCodeCrypt());
	}
	
	/*  */
	// *****************************************************************************************
	// *****************************************************************************************
	$arResult["index"] = CComponentEngine::MakePathFromTemplate($arParams["URL_TEMPLATES_INDEX"], array());
	if ($arParams["SET_NAVIGATION"] != "N")
	{
		if ($arParams["ADD_INDEX_NAV"] == "Y")
			$APPLICATION->AddChainItem(GetMessage("F_INDEX"), CComponentEngine::MakePathFromTemplate($arParams["~URL_TEMPLATES_INDEX"], array()));
		$APPLICATION->AddChainItem(htmlspecialchars($arResult["FORUM"]["~NAME"]), CComponentEngine::MakePathFromTemplate($arParams["~URL_TEMPLATES_LIST"], array("FID" => $arParams["FID"])));
		$APPLICATION->AddChainItem(htmlspecialchars($arResult["TOPIC"]["~TITLE"]));
	}
	//******************************************************************************************
	if ($arParams["SET_TITLE"] != "N")
		$APPLICATION->SetTitle(htmlspecialcharsEx($arResult["TOPIC"]["~TITLE"]));
	if($arParams["DISPLAY_PANEL"] == "Y" && $USER->IsAuthorized())
		CForumNew::ShowPanel($arParams["FID"], $arParams["TID"], false);
	// ****************** Important for castom components***************************************
	$arResult["arFormParams"] = array();
	if ($arResult["TOPIC"]["STATE"]=="Y")
	{
		$arFormParams = array(
			"MESSAGE_TYPE" => "REPLY",
			"FID" => $arParams["FID"],
			"TID" => $arParams["TID"],
			"arForum" => $arResult["FORUM"],
			"bVarsFromForm" => $bVarsFromForm,
			"strErrorMessage" => $strErrorMessage,
			"strOKMessage" => $strOKMessage,
			"VIEW" => $arResult["VIEW"],
			"PAGE_NAME" => "read");
		if ($bVarsFromForm)
		{
			$arFormParams["AUTHOR_NAME"] = $_POST["AUTHOR_NAME"];
			$arFormParams["AUTHOR_EMAIL"] = $_POST["AUTHOR_EMAIL"];
			$arFormParams["POST_MESSAGE"] = $_POST["POST_MESSAGE"];
			$arFormParams["USE_SMILES"] = $_POST["USE_SMILES"];
		}
		$arResult["arFormParams"] = $arFormParams;
		$arResult["arFormParams"]["PATH_TO_SMILE"] = $arParams["PATH_TO_SMILE"];
		$arResult["arFormParams"]["CACHE_TIME"] = $arParams["CACHE_TIME"];
	}
	$arResult["PARSER"] = $parser;
	$this->IncludeComponentTemplate();
	
	
	return array("FORUM" => $arResult["FORUM"], "bVarsFromForm" => ($bVarsFromForm ? "Y" : "N"));
?>