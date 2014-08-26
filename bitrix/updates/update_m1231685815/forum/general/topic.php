<?
IncludeModuleLangFile(__FILE__); 
/**********************************************************************/
/************** FORUM TOPIC *******************************************/
/**********************************************************************/
class CAllForumTopic
{
	function CanUserViewTopic($TID, $arUserGroups)
	{
		$TID = intVal($TID);
		$arTopic = CForumTopic::GetByID($TID);
		if ($arTopic)
		{
			$FID = intVal($arTopic["FORUM_ID"]);
			$strPerms = CForumNew::GetUserPermission($FID, $arUserGroups);
			if ($strPerms>="Y") 
				return True;
			if ($strPerms<"E") 
				return False;
			if ($strPerms<"Q" && $arTopic["APPROVED"]!="Y") 
				return False;

			$arForum = CForumNew::GetByID($FID);
			if ($arForum)
			{
				if ($arForum["ACTIVE"]!="Y") 
					return False;
			}
			else
			{
				return False;
			}
		}
		else
		{
			return False;
		}
		return True;
	}

	function CanUserAddTopic($FID, $arUserGroups, $iUserID = 0, $arForum = false)
	{
		if (!$arForum || (!is_array($arForum)) || (intVal($arForum["ID"]) != intVal($FID)))
		{
			$arForum = CForumNew::GetByID($FID);
		}
		if (is_array($arForum) && ($arForum["ID"] = $FID))
		{
			$strPerms = CForumNew::GetUserPermission($FID, $arUserGroups);
			if ($strPerms>="Y") 
				return True;
			if ($arForum["ACTIVE"]!="Y") 
				return False;
			if ($strPerms<"M") 
				return False;
			if (CForumUser::IsLocked($iUserID)) 
				return false;
		}
		else 
		{
			return False;
		}
		return True;
	}

	function CanUserUpdateTopic($TID, $arUserGroups, $iUserID = 0)
	{
		$TID = intVal($TID);
		$arTopic = CForumTopic::GetByID($TID);
		
		if ($arTopic)
		{
			// If current user is MODERATOR or more, that user can update topic
			$FID = intVal($arTopic["FORUM_ID"]);
			$strPerms = CForumNew::GetUserPermission($FID, $arUserGroups);
			if ($strPerms>="Y") 
				return True;
			if (CForumUser::IsLocked($iUserID)) 
				return False;
			if ($strPerms<"M") 
				return False;
			if ($strPerms<"Q" && $arTopic["STATE"]!="Y") 
				return False;
			$arForum = CForumNew::GetByID($FID);
			if ($arForum)
			{
				if ($arForum["ACTIVE"]!="Y") 
					return False;
				if ($strPerms>="U") 
					return True;

				$db_res = CForumMessage::GetList(array("ID"=>"ASC"), array("TOPIC_ID"=>$TID, "FORUM_ID"=>$FID), False, 2);
				$iCnt = 0;
				$iOwner = 0;
				while ($ar_res = $db_res->Fetch())
				{
					$iOwner = intVal($ar_res["AUTHOR_ID"]);
					$iCnt++;
					if ($iCnt>1) break;
				}
				if ($iCnt>1) return False;
				$iUserID = intVal($iUserID);
				if ($iOwner<=0 || $iUserID<=0 || $iOwner!=$iUserID) return False;
			}
			else
			{
				return False;
			}
		}
		else
		{
			return False;
		}
		return True;
	}

	function CanUserDeleteTopic($TID, $arUserGroups, $iUserID = 0)
	{
		$TID = intVal($TID);
		$arTopic = CForumTopic::GetByID($TID);
		
		if ($arTopic)
		{
			$FID = intVal($arTopic["FORUM_ID"]);
			$strPerms = CForumNew::GetUserPermission($FID, $arUserGroups);
			if ($strPerms>="Y") 
				return True;
			if (CForumUser::IsLocked($iUserID)) 
				return False;

			$arForum = CForumNew::GetByID($FID);
			if ($arForum)
			{
				if ($arForum["ACTIVE"]!="Y") return False;
				if ($strPerms>="U") return True;
			}
		}
		return False;
	}

	function CanUserDeleteTopicMessage($TID, $arUserGroups, $iUserID = 0)
	{
		$TID = intVal($TID);
		$arTopic = CForumTopic::GetByID($TID);
		if ($arTopic)
		{
			$FID = intVal($arTopic["FORUM_ID"]);
			$strPerms = CForumNew::GetUserPermission($FID, $arUserGroups);
			if ($strPerms>="Y") 
				return True;
			if (CForumUser::IsLocked($iUserID)) 
				return False;

			$arForum = CForumNew::GetByID($FID);
			if ($arForum)
			{
				if ($arForum["ACTIVE"]!="Y") return False;
				if ($strPerms>="U") return True;
			}
		}
		return False;
	}

	function CheckFields($ACTION, &$arFields)
	{
		// Fatal Errors
		if (is_set($arFields, "TITLE") || $ACTION=="ADD")
		{
			$arFields["TITLE"] = trim($arFields["TITLE"]);
			if (strLen($arFields["TITLE"]) <= 0)
				return false;
 		}
 		
		if (is_set($arFields, "USER_START_NAME") || $ACTION=="ADD")
		{
			$arFields["USER_START_NAME"] = trim($arFields["USER_START_NAME"]);
			if (strLen($arFields["USER_START_NAME"]) <= 0)
				return false;
		}
		
		if (is_set($arFields, "FORUM_ID") || $ACTION=="ADD")
		{
			$arFields["FORUM_ID"] = intVal($arFields["FORUM_ID"]);
			if ($arFields["FORUM_ID"] <= 0)
				return false;
		}
		
		if (is_set($arFields, "LAST_POSTER_NAME") || $ACTION=="ADD")
		{
			$arFields["LAST_POSTER_NAME"] = trim($arFields["LAST_POSTER_NAME"]);
			if (strLen($arFields["LAST_POSTER_NAME"]) <= 0)
				return false;
		}
		
		// Check Data
		if (is_set($arFields, "USER_START_ID") || $ACTION=="ADD")
			$arFields["USER_START_ID"] = (intVal($arFields["USER_START_ID"]) > 0 ? intVal($arFields["USER_START_ID"]) : false);
		if (is_set($arFields, "LAST_POSTER_ID") || $ACTION=="ADD")
			$arFields["LAST_POSTER_ID"] = (intVal($arFields["LAST_POSTER_ID"]) > 0 ? intVal($arFields["LAST_POSTER_ID"]) : false);
		if (is_set($arFields, "LAST_MESSAGE_ID") || $ACTION=="ADD")
			 $arFields["LAST_MESSAGE_ID"] = (intVal($arFields["LAST_MESSAGE_ID"]) > 0 ? intVal($arFields["LAST_MESSAGE_ID"]) : false);
		if (is_set($arFields, "ICON_ID") || $ACTION=="ADD") 
			$arFields["ICON_ID"] = (intVal($arFields["ICON_ID"]) > 0 ? intVal($arFields["ICON_ID"]) : false);
		if (is_set($arFields, "STATE") || $ACTION=="ADD") 
			$arFields["STATE"] = (in_array($arFields["STATE"], array("Y", "N", "L")) ?  $arFields["STATE"] : "Y");
		if (is_set($arFields, "APPROVED") || $ACTION=="ADD") 
			$arFields["APPROVED"] = ($arFields["APPROVED"] == "N" ? "N" : "Y");
		if (is_set($arFields, "SORT") || $ACTION=="ADD")
			$arFields["SORT"] = (intVal($arFields["SORT"]) > 0 ? intVal($arFields["SORT"]) : 150);
		if (is_set($arFields, "VIEWS") || $ACTION=="ADD")
			$arFields["VIEWS"] = (intVal($arFields["VIEWS"]) > 0 ? intVal($arFields["VIEWS"]) : 1); 
		if (is_set($arFields, "POSTS") || $ACTION=="ADD")
			$arFields["POSTS"] = (intVal($arFields["POSTS"]) > 0 ? intVal($arFields["POSTS"]) : 0);
		if (is_set($arFields, "TOPIC_ID")) 
			$arFields["TOPIC_ID"]=intVal($arFields["TOPIC_ID"]);
		return True;
	}

	function Add($arFields)
	{
		global $DB;

		$arFields["VIEWS"] = 1;
		$arFields["POSTS"] = 0;
		$arFields["STATE"] = (in_array($arFields["STATE"], array("Y", "N", "L")) ? $arFields["STATE"] : "Y");
		
		if (!CForumTopic::CheckFields("ADD", $arFields))
			return false;
		
		if (COption::GetOptionString("forum", "FILTER", "Y") == "Y")
		{
			$arr = array(
				"TITLE"=>CFilterUnquotableWords::Filter($arFields["TITLE"]), 
				"DESCRIPTION" => CFilterUnquotableWords::Filter($arFields["DESCRIPTION"]),
				"LAST_POSTER_NAME" => CFilterUnquotableWords::Filter($arFields["LAST_POSTER_NAME"]),
				"USER_START_NAME" => CFilterUnquotableWords::Filter($arFields["USER_START_NAME"]),
				"TAGS" => CFilterUnquotableWords::Filter($arFields["TAGS"])
			);
			if (empty($arr["TITLE"]))
				$arr["TITLE"] = "*";
			$arFields["HTML"] = serialize($arr);
		}
		$Fields = array(
			"TITLE" => "'".$DB->ForSQL($arFields["TITLE"], 255)."'",
			"USER_START_NAME" => "'".$DB->ForSQL($arFields["USER_START_NAME"], 255)."'",
			"FORUM_ID" => intVal($arFields["FORUM_ID"]),
			"LAST_POSTER_NAME" => "'".$DB->ForSQL($arFields["LAST_POSTER_NAME"], 255)."'",
			"TAGS" => "'".$DB->ForSQL($arFields["TAGS"], 255)."'",
			"HTML" => "'".$DB->ForSQL($arFields["HTML"])."'",
			
			"STATE" => "'".$arFields["STATE"]."'",
			"APPROVED" => "'".$arFields["APPROVED"]."'",
			
			"START_DATE" => is_set($arFields, "START_DATE") ? "'".$arFields["START_DATE"]."'" : $DB->GetNowFunction(),
			"LAST_POST_DATE" => is_set($arFields, "LAST_POST_DATE") ? "'".$arFields["LAST_POST_DATE"]."'" : $DB->GetNowFunction(),
			
			"SORT" => intVal($arFields["SORT"]),
			"POSTS" => intVal($arFields["POSTS"]),
			"VIEWS" => intVal($arFields["VIEWS"]),
			"TOPIC_ID" => intVal($arFields["TOPIC_ID"]));
		if (strLen(trim($arFields["DESCRIPTION"])) > 0)
			$Fields["DESCRIPTION"] = "'".$DB->ForSQL($arFields["DESCRIPTION"], 255)."'";
		if (strLen(trim($arFields["XML_ID"])) > 0)
			$Fields["XML_ID"] = "'".$DB->ForSQL($arFields["XML_ID"], 255)."'";
		if (intVal($arFields["USER_START_ID"]) > 0)
			$Fields["USER_START_ID"] = intVal($arFields["USER_START_ID"]);
		if (intVal($arFields["ICON_ID"]) > 0)
			$Fields["ICON_ID"] = intVal($arFields["ICON_ID"]);
		if (intVal($arFields["LAST_MESSAGE_ID"]) > 0)
			$Fields["LAST_MESSAGE_ID"] = intVal($arFields["LAST_MESSAGE_ID"]);
		if ($arFields["LAST_POSTER_ID"])
			$Fields["LAST_POSTER_ID"] = intVal($arFields["LAST_POSTER_ID"]);
		return  $DB->Insert("b_forum_topic", $Fields, "File: ".__FILE__."<br>Line: ".__LINE__);
	}
	
	function Update($ID, $arFields, $skip_counts = False)
	{
		global $DB;
		$ID = intVal($ID);
		$arFields1 = array();

		if (!CForumTopic::CheckFields("UPDATE", $arFields))
			return false;
		foreach ($arFields as $key => $value)
		{
			if (substr($key, 0, 1)=="=")
			{
				$arFields1[substr($key, 1)] = $value;
				unset($arFields[$key]);
			}
		}
		if (!$skip_counts || is_set($arFields, "FORUM_ID"))
			$arTopic_prev = CForumTopic::GetByID($ID, array("NoFilter"=>true));

		// Fields "HTML".
		if ((COption::GetOptionString("forum", "FILTER", "Y") == "Y") && 
			(is_set($arFields, "TITLE") || is_set($arFields, "DESCRIPTION") || is_set($arFields, "LAST_POSTER_NAME")))
		{	
			$arr = array();
			// TITLE
			if (is_set($arFields["TITLE"]) && ($arFields["TITLE"] != $arTopic_prev["TITLE"]))
				$arr["TITLE"] = $arFields["TITLE"];
			else 
				$arr["TITLE"] = $arTopic_prev["TITLE"];
			$arr["TITLE"] = CFilterUnquotableWords::Filter($arr["TITLE"]);
			if (empty($arr["TITLE"]))
				$arr["TITLE"] = "*";
			
			// DESCRIPTION
			if (is_set($arFields["DESCRIPTION"]) && ($arFields["DESCRIPTION"] != $arTopic_prev["DESCRIPTION"]))
				$arr["DESCRIPTION"] = $arFields["DESCRIPTION"];
			else 
				$arr["DESCRIPTION"] = $arTopic_prev["DESCRIPTION"];
			$arr["DESCRIPTION"] = CFilterUnquotableWords::Filter($arr["DESCRIPTION"]);
			
			// LAST_POST_NAME
			if (is_set($arFields["LAST_POST_NAME"]) && ($arFields["LAST_POST_NAME"] != $arTopic_prev["LAST_POST_NAME"]))
				$arr["LAST_POST_NAME"] = $arFields["LAST_POST_NAME"];
			else 
				$arr["LAST_POST_NAME"] = $arTopic_prev["LAST_POST_NAME"];
				
			// USER_START_NAME
			if (is_set($arFields["USER_START_NAME"]) && ($arFields["USER_START_NAME"] != $arTopic_prev["USER_START_NAME"]))
				$arr["USER_START_NAME"] = $arFields["USER_START_NAME"];
			else 
				$arr["USER_START_NAME"] = $arTopic_prev["USER_START_NAME"];
				
			// TAGS
			if (is_set($arFields["TAGS"]) && ($arFields["TAGS"] != $arTopic_prev["TAGS"]))
				$arr["TAGS"] = $arFields["TAGS"];
			else 
				$arr["TAGS"] = $arTopic_prev["TAGS"];
			$arr["TAGS"] = CFilterUnquotableWords::Filter($arr["TAGS"]);
			
			$arr["LAST_POST_NAME"] = CFilterUnquotableWords::Filter($arr["LAST_POST_NAME"]);
			
			$arr["USER_START_NAME"] = CFilterUnquotableWords::Filter($arr["USER_START_NAME"]);
			
			$arFields["HTML"] = serialize($arr);
		}
			
		$strUpdate = $DB->PrepareUpdate("b_forum_topic", $arFields);
		
		foreach ($arFields1 as $key => $value)
		{
			if (strLen($strUpdate)>0) $strUpdate .= ", ";
			$strUpdate .= $key."=".$value." ";
		}
		$strSql = "UPDATE b_forum_topic SET ".$strUpdate." WHERE ID = ".$ID;
		$DB->QueryBind($strSql, array("HTML"=>$arFields["HTML"]), false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
		
		$res = array_merge($arFields1, $arFields);
		if ((count($res) == 1) && !empty($res["VIEWS"]))
		{
			if (intVal($res["VIEWS"]) <= 0)
			{
				$GLOBALS["FORUM_CACHE"]["TOPIC"][$ID]["VIEWS"]++;
				$GLOBALS["FORUM_CACHE"]["TOPIC_FILTER"][$ID]["VIEWS"]++;
			}
			else
			{
				$GLOBALS["FORUM_CACHE"]["TOPIC"][$ID]["VIEWS"] = intVal($res["VIEWS"]);
				$GLOBALS["FORUM_CACHE"]["TOPIC_FILTER"][$ID]["VIEWS"] = intVal($res["VIEWS"]);
			}
			return $ID;
		}

		unset($GLOBALS["FORUM_CACHE"]["FORUM"][$arTopic_prev["FORUM_ID"]]);
		unset($GLOBALS["FORUM_CACHE"]["TOPIC"][$ID]);
		unset($GLOBALS["FORUM_CACHE"]["TOPIC_FILTER"][$ID]);
		if (intVal($arFields1["FORUM_ID"]) > 0)
			unset($GLOBALS["FORUM_CACHE"]["FORUM"][intVal($arFields1["FORUM_ID"])]);

		if ((!$skip_counts) || (CModule::IncludeModule("search") && is_set($arFields, "TITLE")))
		{
			$arTopic = CForumTopic::GetByID($ID);
			// recalc statistic if topic removed from another forum
			if ((!$skip_counts) && (intVal($arTopic["FORUM_ID"])!=intVal($arTopic_prev["FORUM_ID"])))
			{
				$DB->StartTransaction();
					$db_res = CForumMessage::GetList(array(), array("TOPIC_ID"=>$ID));
					while ($ar_res = $db_res->Fetch())
					{
						CForumMessage::Update($ar_res["ID"], array("FORUM_ID"=>$arTopic["FORUM_ID"]), true);
					}
					$db_res = CForumSubscribe::GetList(array(), array("TOPIC_ID"=>$ID));
					while ($ar_res = $db_res->Fetch())
					{
						CForumSubscribe::Update($ar_res["ID"], array("FORUM_ID"=>$arTopic["FORUM_ID"]));
					}
					CForumNew::SetStat($arTopic["FORUM_ID"]);
					CForumNew::SetStat($arTopic_prev["FORUM_ID"]);
				$DB->Commit();
			}
			if (CModule::IncludeModule("search") && is_set($arFields, "TITLE"))
			{
				if ($arTopic)
				{
					$arReindex = array();
					if (trim($arTopic_prev["TITLE"])!=trim($arTopic["TITLE"]))
						$arReindex["TITLE"] = $arTopic["TITLE"];
					if (trim($arTopic_prev["TAGS"])!=trim($arTopic["TAGS"]))
						$arReindex["TAGS"] = $arTopic["TAGS"];
					if (!empty($arReindex))
					{
						CSearch::ChangeIndex("forum",$arReindex,
							false, $arTopic["FORUM_ID"], intVal($ID));
					}
				}
			}
		}
		return $ID;
	}

	function MoveTopic2Forum($TID, $FID, $leaveLink="N")
	{
		global $DB;
		$arForum = array();
		$arTopics = array();
		$FID = intVal($FID);
		$leaveLink = (strToUpper($leaveLink) == "Y" ? "Y" : "N");
		$newTID = 0;
		$oldFID = 0;
		// Check forum
		$arForum = CForumNew::GetByID($FID);
		if (empty($arForum) || ($arForum["ID"] != $FID))
		{
			$GLOBALS["APPLICATION"]->ThrowException(str_replace("#FORUM_ID#", $FID, GetMessage("F_ERR_FORUM_NOT_EXIST")), "FORUM_NOT_EXIST");
			return false;
		}
		// Check topic
		if (!is_array($TID))
			$arTopics[intVal($TID)] = array("ID" => intVal($TID));
		else 
		{
			foreach ($TID as $res)
			{
				$arTopics[intVal($res)] = array("ID" => intVal($res));
			}
		}
		if (empty($arTopics))
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("F_ERR_EMPTY_TO_MOVE"), "TOPIC_EMPTY");
			return false;
		}
			
		$DB->StartTransaction();
		$db_res = CForumTopic::GetList(array(), array("@ID" => implode(", ", array_keys($arTopics))));
		if ($db_res && ($res = $db_res->Fetch()))
		{
			do 
			{
				if (intVal($res["FORUM_ID"])==$FID)
				{
					$GLOBALS["APPLICATION"]->ThrowException(str_replace(array("#TITLE#", "#ID#"), array($res["TITLE"], $res["ID"]), GetMessage("F_ERR_THIS_TOPIC_IS_NOT_MOVE")), "FORUM_ID_IDENTICAL");
					continue;
				}
				
				if ($leaveLink != "N")
				{
					CForumTopic::Add(
						array(
							"TITLE" => $res["TITLE"],
							"DESCRIPTION" => $res["DESCRIPTION"],
							"STATE" => "L",
							"USER_START_NAME" => $res["USER_START_NAME"],
							"START_DATE" => $DB->CharToDateFunction($res["START_DATE"]),
							"ICON_ID" => $res["ICON_ID"],
							"POSTS" => "0",
							"VIEWS" => "0",
							"FORUM_ID" => $res["FORUM_ID"],
							"TOPIC_ID" => $res["ID"],
							"APPROVED" => $res["APPROVED"],
							"SORT" => $res["SORT"],
							"LAST_POSTER_NAME" => $res["LAST_POSTER_NAME"],
							"LAST_POST_DATE" => $DB->CharToDateFunction($res["LAST_POST_DATE"]),
							"HTML" => $res["HTML"],
							));
				}
				
				CForumTopic::Update($res["ID"], array("FORUM_ID"=>$FID), true);
				// move message
				$strSql = 
					"UPDATE b_forum_message 
						SET FORUM_ID=".$FID.", POST_MESSAGE_HTML=''  
					WHERE TOPIC_ID=".$res["ID"];
				$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
				// move subscribe
				$strSql = 
					"UPDATE b_forum_subscribe 
						SET FORUM_ID=".intVal($FID)."
					WHERE TOPIC_ID=".$res["ID"];
				$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
				
				$oldFID = $res["FORUM_ID"];
				unset($GLOBALS["FORUM_CACHE"]["TOPIC"][$res["ID"]]);
				unset($GLOBALS["FORUM_CACHE"]["TOPIC_FILTER"][$res["ID"]]);
				$arTopics[intVal($res["ID"])] = $res;
			} while ($res = $db_res->Fetch());
		}
		CForumNew::SetStat($FID);
		CForumNew::SetStat($oldFID);
		$DB->Commit();
		return True;
	}

	function Delete($ID)
	{
		global $DB;
		$ID = intVal($ID);

		$arTopic = CForumTopic::GetByID($ID);
		if ($arTopic)
		{
			$DB->StartTransaction();
			$strMIDList = "0";
			$arAUTHOR_ID = array();
			$db_res = CForumMessage::GetList(array(), array("TOPIC_ID" => $ID));
			while ($res = $db_res->Fetch())
			{
				$strMIDList .= "," . intVal($res["ID"]);
				if (intVal($res["AUTHOR_ID"])>0)
				{
					$arAUTHOR_ID[] = intVal($res["AUTHOR_ID"]);
				}
			}

			$strSql = 
				"SELECT F.ID ".
				"FROM b_forum_message FM, b_file F ".
				"WHERE FM.TOPIC_ID = ".$ID." ".
				"	AND FM.ATTACH_IMG = F.ID ";
			$z = $DB->Query($strSql, false, "FILE: ".__FILE__." LINE:".__LINE__);
			while ($zr = $z->Fetch())
				CFile::Delete($zr["ID"]);

			unset($GLOBALS["FORUM_CACHE"]["TOPIC"][$ID]);
			unset($GLOBALS["FORUM_CACHE"]["TOPIC_FILTER"][$ID]);

			$DB->Query("DELETE FROM b_forum_subscribe WHERE TOPIC_ID = ".$ID."");
			$DB->Query("DELETE FROM b_forum_message WHERE TOPIC_ID = ".$ID."");
			$DB->Query("DELETE FROM b_forum_user_topic WHERE TOPIC_ID = ".$ID."");
			$DB->Query("DELETE FROM b_forum_topic WHERE ID = ".$ID."");
			$DB->Query("DELETE FROM b_forum_topic WHERE TOPIC_ID = ".$ID."");

			for ($i = 0; $i < count($arAUTHOR_ID); $i++)
			{
				CForumUser::SetStat(intVal($arAUTHOR_ID[$i]));
			}
			CForumNew::SetStat($arTopic["FORUM_ID"]);
			$DB->Commit();

			if (CModule::IncludeModule("search"))
			{
				$arMIDs = Split(",", $strMIDList);
				for ($i = 1; $i<count($arMIDs); $i++)
				{
					if (intVal($arMIDs[$i])>0)
					{
						CSearch::Index("forum", intVal($arMIDs[$i]),
							array(
								"TITLE"=>"",
								"BODY"=>""
							)
						);
					}
				}
			}
		}

		return true;
	}

	function GetByID($ID, $arAddParams = array())
	{
		global $DB;

		$ID = intVal($ID);
		
		$NoFilter = ($arAddParams["NoFilter"] == true || (COption::GetOptionString("forum", "FILTER", "Y") != "Y")) ? true : false;
		
		if ($NoFilter && isset($GLOBALS["FORUM_CACHE"]["TOPIC"][$ID]) && is_array($GLOBALS["FORUM_CACHE"]["TOPIC"][$ID]) && is_set($GLOBALS["FORUM_CACHE"]["TOPIC"][$ID], "ID"))
		{
			return $GLOBALS["FORUM_CACHE"]["TOPIC"][$ID];
		}
		elseif (!$NoFilter && isset($GLOBALS["FORUM_CACHE"]["TOPIC_FILTER"][$ID]) && is_array($GLOBALS["FORUM_CACHE"]["TOPIC_FILTER"][$ID]) && is_set($GLOBALS["FORUM_CACHE"]["TOPIC_FILTER"][$ID], "ID"))
		{
			return $GLOBALS["FORUM_CACHE"]["TOPIC_FILTER"][$ID];
		}
		else
		{
			$strSql = 
				"SELECT FT.*, 
					".$DB->DateToCharFunction("FT.START_DATE", "FULL")." as START_DATE, 
					".$DB->DateToCharFunction("FT.LAST_POST_DATE", "FULL")." as LAST_POST_DATE 
				FROM b_forum_topic FT 
				WHERE FT.ID = ".$ID;
				
			$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			if ($db_res)
			{
				if ($res = $db_res->Fetch())
				{
					$GLOBALS["FORUM_CACHE"]["TOPIC"][$ID] = $res;
					if (COption::GetOptionString("forum", "FILTER", "Y") == "Y")
					{
						$db_res_filter = new CDBResult;
						$db_res_filter->InitFromArray(array($res));
						$db_res_filter = new _CTopicDBResult($db_res_filter);
						if ($res_filter = $db_res_filter->Fetch())
							$GLOBALS["FORUM_CACHE"]["TOPIC_FILTER"][$ID] = $res_filter;
					}
					if (!$NoFilter)
						$res = $res_filter;
					return $res;
				}
			}
		}
		return False;
	}

	function GetByIDEx($ID, $arAddParams = array())
	{
		global $DB;

		$ID = intVal($ID);
		if ($ID <= 0)
			return false;
		
		$arAddParams = (is_array($arAddParams) ? $arAddParams : array($arAddParams));
		$arAddParams["GET_FORUM_INFO"] = ($arAddParams["GET_FORUM_INFO"] == "Y" ? "Y" : "N");
		$arSqlSelect = array();
		$arSqlFrom = array();
		if ($arAddParams["GET_FORUM_INFO"] == "Y")
		{
			$arSqlSelect[] = CForumNew::GetSelectFields(array("sPrefix" => "F_", "sReturnResult" => "string"));
			$arSqlFrom[] =  "INNER JOIN b_forum F ON (FT.FORUM_ID = F.ID)";
		}

		$strSql = 
			"SELECT FT.*, 
				".$DB->DateToCharFunction("FT.START_DATE", "FULL")." as START_DATE, 
				".$DB->DateToCharFunction("FT.LAST_POST_DATE", "FULL")." as LAST_POST_DATE, 
				FS.IMAGE, '' as IMAGE_DESCR".
				(!empty($arSqlSelect) ? ", ".implode(", ", $arSqlSelect) : "")."
			FROM b_forum_topic FT 
				LEFT JOIN b_forum_smile FS ON (FT.ICON_ID = FS.ID) 
				".implode(" ", $arSqlFrom)."
			WHERE FT.ID = ".$ID;
		$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		if (COption::GetOptionString("forum", "FILTER", "Y") == "Y")
			$db_res = new _CTopicDBResult($db_res);
		if ($res = $db_res->Fetch())
		{
			if (is_array($res))
			{
				// Cache topic data for hits
				if ($arAddParams["GET_FORUM_INFO"] == "Y")
				{
					$res["TOPIC_INFO"] = array();
					$res["FORUM_INFO"] = array();
					foreach ($res as $key => $val)
					{
						if (substr($key, 0, 2) == "F_")
							$res["FORUM_INFO"][substr($key, 2)] = $val;
						else 
							$res["TOPIC_INFO"][$key] = $val;
					}
					if (!empty($res["TOPIC_INFO"]))
					{
						$GLOBALS["FORUM_CACHE"]["TOPIC"][intVal($res["TOPIC_INFO"]["ID"])] = $res["TOPIC_INFO"];
						if (COption::GetOptionString("forum", "FILTER", "Y") == "Y")
						{
							$db_res_filter = new CDBResult;
							$db_res_filter->InitFromArray(array($res["TOPIC_INFO"]));
							$db_res_filter = new _CTopicDBResult($db_res_filter);
							if ($res_filter = $db_res_filter->Fetch())
								$GLOBALS["FORUM_CACHE"]["TOPIC_FILTER"][intVal($res["TOPIC_INFO"]["ID"])] = $res_filter;
						}
					}
					if (!empty($res["FORUM_INFO"]))
					{
						$GLOBALS["FORUM_CACHE"]["FORUM"][intVal($res["FORUM_INFO"]["ID"])] = $res["FORUM_INFO"];
					}
				}
			}
			return $res;
		}
		return false;
	}

	function GetNeighboringTopics($TID, $arUserGroups)
	{
		$TID = intVal($TID);
		$arTopic = CForumTopic::GetByID($TID);
		if (!$arTopic) return False;

		//-- PREV_TOPIC
		$arFilter = array(
			"FORUM_ID" => $arTopic["FORUM_ID"],
			"<LAST_POST_DATE" => $arTopic["LAST_POST_DATE"]
			);
		if (CForumNew::GetUserPermission($arTopic["FORUM_ID"], $arUserGroups)<"Q")
			$arFilter["APPROVED"] = "Y";

		$db_res = CForumTopic::GetList(array("LAST_POST_DATE"=>"DESC"), $arFilter, false, 1);
		$PREV_TOPIC = 0;
		if ($ar_res = $db_res->Fetch()) $PREV_TOPIC = $ar_res["ID"];

		//-- NEXT_TOPIC
		$arFilter = array(
			"FORUM_ID" => $arTopic["FORUM_ID"],
			">LAST_POST_DATE" => $arTopic["LAST_POST_DATE"]
			);
		if (CForumNew::GetUserPermission($arTopic["FORUM_ID"], $arUserGroups)<"Q")
			$arFilter["APPROVED"] = "Y";

		$db_res = CForumTopic::GetList(array("LAST_POST_DATE"=>"ASC"), $arFilter, false, 1);
		$NEXT_TOPIC = 0;
		if ($ar_res = $db_res->Fetch()) $NEXT_TOPIC = $ar_res["ID"];

		return array($PREV_TOPIC, $NEXT_TOPIC);
	}
	
	function GetSelectFields($arAddParams = array())
	{
		global $DB;
		$arAddParams = (is_array($arAddParams) ? $arAddParams : array());
		$arAddParams["sPrefix"] = $DB->ForSql(empty($arAddParams["sPrefix"]) ? "FT." : $arAddParams["sPrefix"]);
		$arAddParams["sTablePrefix"] = $DB->ForSql(empty($arAddParams["sTablePrefix"]) ? "FT." : $arAddParams["sTablePrefix"]);
		$arAddParams["sReturnResult"] = ($arAddParams["sReturnResult"] == "string" ? "string" : "array");
		
		$res = array(
			$arAddParams["sPrefix"]."ID" => $arAddParams["sTablePrefix"]."ID",
			$arAddParams["sPrefix"]."TITLE" => $arAddParams["sTablePrefix"]."TITLE",
			$arAddParams["sPrefix"]."TAGS" => $arAddParams["sTablePrefix"]."TAGS",
			$arAddParams["sPrefix"]."DESCRIPTION" => $arAddParams["sTablePrefix"]."DESCRIPTION",
			$arAddParams["sPrefix"]."VIEWS" => $arAddParams["sTablePrefix"]."VIEWS",
			$arAddParams["sPrefix"]."LAST_POSTER_ID" => $arAddParams["sTablePrefix"]."LAST_POSTER_ID",
			($arAddParams["sPrefix"] == $arAddParams["sTablePrefix"] ? "" : $arAddParams["sPrefix"]).
				"START_DATE" => $DB->DateToCharFunction($arAddParams["sTablePrefix"]."START_DATE", "FULL"),
			$arAddParams["sPrefix"]."USER_START_NAME" => $arAddParams["sTablePrefix"]."USER_START_NAME",
			$arAddParams["sPrefix"]."USER_START_ID" => $arAddParams["sTablePrefix"]."USER_START_ID",
			$arAddParams["sPrefix"]."POSTS" => $arAddParams["sTablePrefix"]."POSTS",
			$arAddParams["sPrefix"]."LAST_POSTER_NAME" => $arAddParams["sTablePrefix"]."LAST_POSTER_NAME",
			($arAddParams["sPrefix"] == $arAddParams["sTablePrefix"] ? "" : $arAddParams["sPrefix"]).
				"LAST_POST_DATE" => $DB->DateToCharFunction($arAddParams["sTablePrefix"]."LAST_POST_DATE", "FULL"),
			$arAddParams["sPrefix"]."LAST_MESSAGE_ID" => $arAddParams["sTablePrefix"]."LAST_MESSAGE_ID",
			$arAddParams["sPrefix"]."APPROVED" => $arAddParams["sTablePrefix"]."APPROVED",
			$arAddParams["sPrefix"]."STATE" => $arAddParams["sTablePrefix"]."STATE",
			$arAddParams["sPrefix"]."FORUM_ID" => $arAddParams["sTablePrefix"]."FORUM_ID",
			$arAddParams["sPrefix"]."TOPIC_ID" => $arAddParams["sTablePrefix"]."TOPIC_ID",
			$arAddParams["sPrefix"]."ICON_ID" => $arAddParams["sTablePrefix"]."ICON_ID",
			$arAddParams["sPrefix"]."SORT" => $arAddParams["sTablePrefix"]."SORT");

		if ($arAddParams["sReturnResult"] == "string")
		{
			$arRes = array();
			foreach ($res as $key => $val)
			{
				$arRes[] = $val.($key != $val ? " AS ".$key : "");
			}
			$res = implode(", ", $arRes);
		}
		return $res;
	}

	function SetReadLabels($ID, $arUserGroups)
	{
		$ID = intVal($ID);
		$arTopic = CForumTopic::GetByID($ID);
		if ($arTopic)
		{
			$FID = intVal($arTopic["FORUM_ID"]);
			if (is_null($_SESSION["read_forum_".$FID]) || strLen($_SESSION["read_forum_".$FID])<=0)
			{
				$_SESSION["read_forum_".$FID] = "0";
			}
			
			$_SESSION["first_read_forum_".$FID] = intVal($_SESSION["first_read_forum_".$FID]);

			$arFilter = array(
				"FORUM_ID" => $FID,
				"TOPIC_ID" => $ID
				);
			if (intVal($_SESSION["first_read_forum_".$FID])>0)
				$arFilter[">ID"] = intVal($_SESSION["first_read_forum_".$FID]);
			if ($_SESSION["read_forum_".$FID]!="0")
				$arFilter["!@ID"] = $_SESSION["read_forum_".$FID];
			if (CForumNew::GetUserPermission($FID, $arUserGroups)<"Q")
				$arFilter["APPROVED"] = "Y";
			$db_res = CForumMessage::GetList(array(), $arFilter);
			if ($db_res)
			{
				while ($ar_res = $db_res->Fetch())
				{
					$_SESSION["read_forum_".$FID] .= ",".intVal($ar_res["ID"]);
				}
			}
			CForumTopic::Update($ID, array("=VIEWS"=>"VIEWS+1"));
		}
	}

	function SetReadLabelsNew($ID, $update = false, $LastVisit = false, $arAddParams = array())
	{
		global $DB, $USER;
		
		$ID = intVal($ID);
		$result = false;
		$arAddParams = (is_array($arAddParams) ? $arAddParams : array());
		$arAddParams["UPDATE_TOPIC_VIEWS"] = ($arAddParams["UPDATE_TOPIC_VIEWS"] == "N" ? "N" : "Y");
		if (!$update)
		{
			$arTopic = CForumTopic::GetByID($ID);
			if ($arTopic)
			{
				if ($arAddParams["UPDATE_TOPIC_VIEWS"] == "Y")
					CForumTopic::Update($ID, array("=VIEWS"=>"VIEWS+1"));
				
				if (!$USER->IsAuthorized())
					return false;
					
				$USER_ID = intVal($USER->GetID());
				
				$Fields = array(
					"USER_ID" => $USER_ID, 
					"LAST_VISIT" => $DB->GetNowFunction(),
					"FORUM_ID" => $arTopic["FORUM_ID"],
					"TOPIC_ID" => $ID);
					
				if (intVal($LastVisit) > 0)
					$Fields["LAST_VISIT"] = $DB->CharToDateFunction($DB->ForSql(Date(CDatabase::DateFormatToPHP(CLang::GetDateFormat("FULL", LANG)), $LastVisit)), "FULL");
		
				$rows = $DB->Update("b_forum_user_topic", $Fields, "WHERE (TOPIC_ID=".$ID." AND USER_ID=".$USER_ID.")", $err_mess.__LINE__);		
				if (intVal($rows)<=0)
					return $DB->Insert("b_forum_user_topic", $Fields, $err_mess.__LINE__);
				else
					return true;
			}
		}
		else
		{
			if (!$USER->IsAuthorized())
				return false;
			
			$Fields = array(
				"LAST_VISIT" => $DB->GetNowFunction());
	
			return $DB->Update("b_forum_user_topic", $Fields, "WHERE (FORUM_ID=".$ID." AND USER_ID=".intVal($USER->GetID()).")", $err_mess.__LINE__);
		}
		return false;
	}
	
	function CleanUp($period = 168)
	{
		global $DB;
		$period = intVal($period)*3600;
		$date = $DB->CharToDateFunction($DB->ForSql(Date(CDatabase::DateFormatToPHP(CLang::GetDateFormat("FULL", LANG)), time()-$period)), "FULL") ;
		$strSQL = "DELETE FROM b_forum_user_topic 
					WHERE (LAST_VISIT
					< ".$date.")";
		$DB->Query($strSQL, false, $err_mess.__LINE__);
		return "CForumTopic::CleanUp();";
	}
	

	//---------------> Topic utils
	function SetStat($ID = 0)
	{
		$ID = intVal($ID);
		if ($ID<=0) return false;

		$arFields = array(
			"APPROVED" => "N",
			"POSTS" => 0
			);

		$res = CForumMessage::GetListEx(array(), array("TOPIC_ID"=>$ID, "APPROVED"=>"Y"), 4);
		$iCnt = intVal($res["CNT"]);
		if ($iCnt>0)
		{
			$arFields["POSTS"] = $iCnt - 1;
			$arFields["APPROVED"] = "Y";
		}
		else 
		{
			$arFields["APPROVED"] = "N";
			$arFields["POSTS"] = 0;
		}

		$res = CForumMessage::GetByID($res["LAST_MESSAGE_ID"]);
		if ($res)
		{
			$arFields["LAST_POSTER_ID"] = ((intVal($res["AUTHOR_ID"])>0) ? $res["AUTHOR_ID"] : False);
			$arFields["LAST_POSTER_NAME"] = $res["AUTHOR_NAME"];
			$arFields["LAST_POST_DATE"] = $res["POST_DATE"];
			$arFields["LAST_MESSAGE_ID"] = $res["ID"];
		}

		return CForumTopic::Update($ID, $arFields);
	}
}
class _CTopicDBResult extends CDBResult
{
	function _CTopicDBResult($res)
	{
		parent::CDBResult($res);
	}
	function Fetch()
	{
		global $DB;
		if($res = parent::Fetch())
		{
			if (!empty($res["HTML"]) && (COption::GetOptionString("forum", "FILTER", "Y") == "Y")):
				$arr = unserialize($res["HTML"]);
				if (is_array($arr) && is_set($arr, "TITLE"))
				{
					foreach ($arr as $key => $val)
					{
						if (strLen($val)>0)
							$res[$key] = $val;
					}	
					return $res;
				}
			endif;
			
			if (!empty($res["F_HTML"]) && COption::GetOptionString("forum", "FILTER", "Y") == "Y"):
				$arr = unserialize($res["F_HTML"]);
				if (is_array($arr))
				{
					foreach ($arr as $key => $val)
					{
						$res["F_".$key] = $val;
					}
				}
				if (!empty($res["TITLE"]))
					$res["F_TITLE"] = $res["TITLE"];
			endif;
			
		}
		return $res;
	}
}

?>