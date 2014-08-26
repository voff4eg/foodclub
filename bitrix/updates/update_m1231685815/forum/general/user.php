<?
IncludeModuleLangFile(__FILE__); 
/**********************************************************************/
/************** FORUM USER ********************************************/
/**********************************************************************/
class CAllForumUser
{
	//---------------> User insert, update, delete
	function IsLocked($USER_ID)
	{
		$USER_ID = IntVal($USER_ID);
		if ($USER_ID>0)
		{
			$ar_user = CForumUser::GetByUSER_ID($USER_ID);
			if ($ar_user)
			{
				if ($ar_user["ALLOW_POST"]!="Y") 
				{
					return True;
				}
			}
		}
		return False;
	}

	function CanUserAddUser($arUserGroups)
	{
		return True;
	}

	function CanUserUpdateUser($ID, $arUserGroups, $CurrentUserID = 0)
	{
		$ID = IntVal($ID);
		$CurrentUserID = IntVal($CurrentUserID);
		if (in_array(1, $arUserGroups)) return True;
		$arUser = CForumUser::GetByID($ID);
		if ($arUser && IntVal($arUser["USER_ID"]) == $CurrentUserID) return True;
		return False;
	}

	function CanUserDeleteUser($ID, $arUserGroups)
	{
		$ID = IntVal($ID);
		if (in_array(1, $arUserGroups)) return True;
		return False;
	}

	function CheckFields($ACTION, &$arFields, $ID=false)
	{
		// Checking user for updating or adding	
		// USER_ID as value
		if ((is_set($arFields, "USER_ID") || $ACTION=="ADD") && IntVal($arFields["USER_ID"])<=0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("F_GL_ERR_EMPTY_USER_ID"), "EMPTY_USER_ID");
			return false;
		}
		// Check for exist user
		if (is_set($arFields, "USER_ID") && (intVal($arFields["USER_ID"]) > 0))
		{
			$db_res = CUser::GetByID($arFields["USER_ID"]);
			if (!$db_res->Fetch())
			{
				$GLOBALS["APPLICATION"]->ThrowException(
					str_replace(
							"#UID#", 
							htmlspecialchars($arFields["USER_ID"]),
							GetMessage("F_GL_ERR_USER_NOT_EXIST")), 
					"USER_IS_NOT_EXIST");
				return false;
			}
			
			$res = CForumUser::GetByUSER_ID(intVal($arFields["USER_ID"]));
			
			if ($ACTION=="ADD")
			{
				if (intVal($res["ID"]) > 0)
				{
					$GLOBALS["APPLICATION"]->ThrowException(
						str_replace(
							"#UID#", 
							htmlspecialchars($arFields["USER_ID"]),
							GetMessage("F_GL_ERR_USER_IS_EXIST")), 
						"USER_IS_EXIST");
					return false;
				}
			}
			elseif ($ACTION=="UPDATE")
			{
				unset($arFields["USER_ID"]);
			}
		}
		// last visit
		if (is_set($arFields, "LAST_VISIT") && (strLen(trim($arFields["LAST_VISIT"])) > 0))
		{
			if (($arFields["LAST_VISIT"] != $GLOBALS["DB"]->GetNowFunction()) && (!$GLOBALS["DB"]->IsDate($arFields["LAST_VISIT"], false, LANG, "FULL")))
			{
				$GLOBALS["APPLICATION"]->ThrowException(GetMessage("F_GL_ERR_LAST_VISIT"), "LAST_VISIT");
				return false;
			}
		}
		elseif (is_set($arFields, "LAST_VISIT") && (strLen(trim($arFields["LAST_VISIT"])) <= 0))
		{
			unset($arFields["LAST_VISIT"]);
		}
		
		// date registration
		if (is_set($arFields, "DATE_REG") && (strLen(trim($arFields["DATE_REG"])) > 0))
		{
			if (($arFields["DATE_REG"] != $GLOBALS["DB"]->GetNowFunction()) && (!$GLOBALS["DB"]->IsDate($arFields["DATE_REG"], false, LANG, "SHORT")))
			{
				$GLOBALS["APPLICATION"]->ThrowException(GetMessage("F_GL_ERR_DATE_REG"), "DATE_REG");
				return false;
			}
		}
		elseif (is_set($arFields, "DATE_REG") && (strLen(trim($arFields["DATE_REG"])) <= 0))
		{
			unset($arFields["DATE_REG"]);
		}
		

		if (is_set($arFields, "AVATAR") && strLen($arFields["AVATAR"]["name"])<=0 && strLen($arFields["AVATAR"]["del"])<=0)
		{
			unset($arFields["AVATAR"]);
		}

		if (is_set($arFields, "AVATAR"))
		{
			$max_size = COption::GetOptionInt("forum", "avatar_max_size", 10000);
			$max_width = COption::GetOptionInt("forum", "avatar_max_width", 90);
			$max_height = COption::GetOptionInt("forum", "avatar_max_height", 90);
			$res = CFile::CheckImageFile($arFields["AVATAR"], $max_size, $max_width, $max_height);
			if (strLen($res) > 0)
			{
				$GLOBALS["APPLICATION"]->ThrowException($res, "AVATAR");
				return false;
			}
		}

		if ((is_set($arFields, "ALLOW_POST") || $ACTION=="ADD") && ($arFields["ALLOW_POST"]!="Y" && $arFields["ALLOW_POST"]!="N")) 
			$arFields["ALLOW_POST"] = "Y";

		return True;
	}

	function Add($arFields, $strUploadDir = false)
	{
		global $DB;
		$arBinds = Array();

		if ($strUploadDir===false) 
			$strUploadDir = "avatar";

		if (!CForumUser::CheckFields("ADD", $arFields))
			return false;
			
		if (!is_set($arFields, "LAST_VISIT"))
			$arFields["~LAST_VISIT"] = $DB->GetNowFunction();
			
		if (!is_set($arFields, "DATE_REG"))
			$arFields["~DATE_REG"] = $DB->GetNowFunction();
		if (is_set($arFields, "INTERESTS"))
			$arBinds["INTERESTS"] = $arFields["INTERESTS"];

		CFile::SaveForDB($arFields, "AVATAR", $strUploadDir);

		return $DB->Add("b_forum_user", $arFields, $arBinds);
	}

	function Delete($ID)
	{
		global $DB;
		$ID = IntVal($ID);

		$strSql = 
			"SELECT F.ID ".
			"FROM b_forum_user FU, b_file F ".
			"WHERE FU.ID = ".$ID." ".
			"	AND FU.AVATAR = F.ID ";
		$z = $DB->Query($strSql, false, "FILE: ".__FILE__." LINE:".__LINE__);
		while ($zr = $z->Fetch())
			CFile::Delete($zr["ID"]);

		$arForumUser = CForumUser::GetByID($ID);
		unset($GLOBALS["FORUM_CACHE"]["USER"][$ID]);
		unset($GLOBALS["FORUM_CACHE"]["USER_ID"][$arForumUser["USER_ID"]]);

		return $DB->Query("DELETE FROM b_forum_user WHERE ID = ".$ID, True);
	}

	function CountUsers($bActive = False)
	{
		global $DB;

		$strSql = "SELECT COUNT(*) AS CNT FROM b_forum_user";
		if ($bActive)
			$strSql .= " WHERE NUM_POSTS > 0";

		$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		if ($ar_res = $db_res->Fetch())
			return $ar_res["CNT"];

		return 0;
	}

	function GetByID($ID)
	{
		global $DB;

		$ID = IntVal($ID);
		if (isset($GLOBALS["FORUM_CACHE"]["USER"][$ID]) && is_array($GLOBALS["FORUM_CACHE"]["USER"][$ID]) && is_set($GLOBALS["FORUM_CACHE"]["USER"][$ID], "ID"))
		{
			return $GLOBALS["FORUM_CACHE"]["USER"][$ID];
		}
		else
		{
			$strSql = 
				"SELECT FU.ID, FU.USER_ID, FU.SHOW_NAME, FU.DESCRIPTION, FU.IP_ADDRESS, 
					FU.REAL_IP_ADDRESS, FU.AVATAR, FU.NUM_POSTS, FU.INTERESTS, FU.HIDE_FROM_ONLINE, FU.SUBSC_GROUP_MESSAGE, FU.SUBSC_GET_MY_MESSAGE, 
					FU.LAST_POST, FU.ALLOW_POST, FU.SIGNATURE, FU.RANK_ID, FU.POINTS, 
					".$DB->DateToCharFunction("FU.DATE_REG", "SHORT")." as DATE_REG, 
					".$DB->DateToCharFunction("FU.LAST_VISIT", "FULL")." as LAST_VISIT 
				FROM b_forum_user FU 
				WHERE FU.ID = ".$ID;
			$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			if ($res = $db_res->Fetch())
			{
				$GLOBALS["FORUM_CACHE"]["USER"][$ID] = $res;
				return $res;
			}
		}
		return False;
	}

	function GetByLogin($Name)
	{
		global $DB;
		$Name = $DB->ForSql(trim($Name));
		if (
			isset($GLOBALS["FORUM_CACHE"]["USER_NAME"]) && 
			is_set($GLOBALS["FORUM_CACHE"]["USER_NAME"], $Name) && 
			is_array($GLOBALS["FORUM_CACHE"]["USER_NAME"][$Name]) && 
			is_set($GLOBALS["FORUM_CACHE"]["USER_NAME"][$Name], "ID"))
		{
			return $GLOBALS["FORUM_CACHE"]["USER_NAME"][$Name];
		}
		else
		{
			$strSql = 
				"SELECT ID AS USER_ID 
				FROM b_user
				WHERE LOGIN='".$Name."'";
			$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			$res = $db_res->Fetch();
			if (!empty($res["USER_ID"]))
			{
				$strSql = 
					"SELECT FU.ID, FU.USER_ID, FU.SHOW_NAME, FU.DESCRIPTION, FU.IP_ADDRESS, 
						FU.REAL_IP_ADDRESS, FU.AVATAR, FU.NUM_POSTS, FU.INTERESTS, FU.HIDE_FROM_ONLINE, FU.SUBSC_GROUP_MESSAGE, FU.SUBSC_GET_MY_MESSAGE, 
						FU.LAST_POST, FU.ALLOW_POST, FU.SIGNATURE, FU.RANK_ID, FU.POINTS, 
						".$DB->DateToCharFunction("FU.DATE_REG", "SHORT")." as DATE_REG, 
						".$DB->DateToCharFunction("FU.LAST_VISIT", "FULL")." as LAST_VISIT 
					FROM b_forum_user FU 
					WHERE FU.USER_ID = ".$res["USER_ID"];
				$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
				if ($res = $db_res->Fetch())
				{
					$GLOBALS["FORUM_CACHE"]["USER"][$ID] = $res;
					$GLOBALS["FORUM_CACHE"]["USER_NAME"][$Name] = $res;
					return $res;
				}
			}
		}
		
		return False;
	}

	function GetByIDEx($ID)
	{
		global $DB;

		$ID = IntVal($ID);
		$strSql = 
			"SELECT FU.ID, FU.USER_ID, FU.SHOW_NAME, FU.DESCRIPTION, FU.IP_ADDRESS, ".
			"	FU.REAL_IP_ADDRESS, FU.AVATAR, FU.NUM_POSTS, FU.INTERESTS, ".
			"	FU.LAST_POST, FU.ALLOW_POST, FU.SIGNATURE, FU.RANK_ID, ".
			"	U.EMAIL, U.NAME, U.LAST_NAME, U.LOGIN, U.PERSONAL_BIRTHDATE, ".
			"	".$DB->DateToCharFunction("FU.DATE_REG", "SHORT")." as DATE_REG, ".
			"	".$DB->DateToCharFunction("FU.LAST_VISIT", "FULL")." as LAST_VISIT, ".
			"	U.PERSONAL_ICQ, U.PERSONAL_WWW, U.PERSONAL_PROFESSION, ".
			"	U.PERSONAL_CITY, U.PERSONAL_COUNTRY, U.PERSONAL_PHOTO, ".
			"	U.PERSONAL_GENDER, FU.POINTS, FU.HIDE_FROM_ONLINE, FU.SUBSC_GROUP_MESSAGE, FU.SUBSC_GET_MY_MESSAGE, ".
			"	".$DB->DateToCharFunction("U.PERSONAL_BIRTHDAY", "SHORT")." as PERSONAL_BIRTHDAY ".
			"FROM b_user U, b_forum_user FU ".
			"WHERE FU.USER_ID = U.ID ".
			"	AND FU.ID = ".$ID." ";
		$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		if ($res = $db_res->Fetch())
		{
			return $res;
		}
		return False;
	}

	function GetByUSER_ID($USER_ID)
	{
		global $DB;

		$USER_ID = IntVal($USER_ID);
		if (isset($GLOBALS["FORUM_CACHE"]["USER_ID"][$USER_ID]) && is_array($GLOBALS["FORUM_CACHE"]["USER_ID"][$USER_ID]) && is_set($GLOBALS["FORUM_CACHE"]["USER_ID"][$USER_ID], "ID"))
		{
			return $GLOBALS["FORUM_CACHE"]["USER_ID"][$USER_ID];
		}
		else
		{
			$strSql = 
				"SELECT FU.ID, FU.USER_ID, FU.SHOW_NAME, FU.DESCRIPTION, FU.IP_ADDRESS, 
					FU.REAL_IP_ADDRESS, FU.AVATAR, FU.NUM_POSTS, FU.INTERESTS, FU.HIDE_FROM_ONLINE, FU.SUBSC_GROUP_MESSAGE, FU.SUBSC_GET_MY_MESSAGE, 
					FU.LAST_POST, FU.ALLOW_POST, FU.SIGNATURE, FU.RANK_ID, FU.POINTS, 
					".$DB->DateToCharFunction("FU.DATE_REG", "SHORT")." as DATE_REG, 
					".$DB->DateToCharFunction("FU.LAST_VISIT", "FULL")." as LAST_VISIT 
				FROM b_forum_user FU 
				WHERE FU.USER_ID = ".$USER_ID;
			$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

			if ($db_res && $res = $db_res->Fetch())
			{
				$GLOBALS["FORUM_CACHE"]["USER_ID"][$USER_ID] = $res;
				return $res;
			}
		}
		return False;
	}

	function GetUserRank($USER_ID, $strLang = false)
	{
		$USER_ID = IntVal($USER_ID);
		if ($USER_ID<=0) return false;

		if ($strLang===false)
		{
			$arUser = CForumUser::GetByUSER_ID($USER_ID);
			if ($arUser)
			{
				$db_res = CForumPoints::GetList(array("MIN_POINTS"=>"DESC"), array("<=MIN_POINTS"=>$arUser["POINTS"]));
				if ($ar_res = $db_res->Fetch())
					return $ar_res;
			}
		}
		else
		{
			if (strlen($strLang)!=2) 
				return false;

			$arUser = CForumUser::GetByUSER_ID($USER_ID);
			if ($arUser)
			{
				$db_res = CForumPoints::GetListEx(array("MIN_POINTS"=>"DESC"), array("<=MIN_POINTS"=>$arUser["POINTS"], "LID" => $strLang));
				if ($ar_res = $db_res->Fetch())
				{
					return $ar_res;
				}
			}
		}

		return false;
	}
	
	//---------------> User visited
	function SetUserForumLastVisit($USER_ID, $FORUM_ID=0, $LAST_VISIT=false)
	{
		global $DB;
		$USER_ID = intVal($USER_ID);
		$FORUM_ID = intVal($FORUM_ID);
		if (!$LAST_VISIT || strLen(trim($LAST_VISIT)) <= 0)
		{
			$Fields = array("LAST_VISIT" => $DB->GetNowFunction());
		}
		else 
		{
			if (intVal($LAST_VISIT) > 0)
			{
				$LAST_VISIT = Date(
								CDatabase::DateFormatToPHP(
									CLang::GetDateFormat("FULL", LANG)
								)
							, $LAST_VISIT);
			}
			
			$Fields = array("LAST_VISIT" => $DB->CharToDateFunction($DB->ForSql($LAST_VISIT, "FULL")));
		}
		
		$rows = $DB->Update("b_forum_user_forum", $Fields, "WHERE (FORUM_ID=".$FORUM_ID." AND USER_ID=".$USER_ID.")", $err_mess.__LINE__);		
		if (intVal($rows)<=0)
		{
			$Fields["FORUM_ID"] = $FORUM_ID;
			$Fields["USER_ID"] = $USER_ID;
			$Fields["MAIN_LAST_VISIT"] = $Fields["LAST_VISIT"];
			$DB->Insert("b_forum_user_forum", $Fields, $err_mess.__LINE__);
		}
		if (intVal($FORUM_ID) <= 0)
		{
			$rows = $DB->Update(
				"b_forum_user_forum", 
				array("MAIN_LAST_VISIT" => $Fields["LAST_VISIT"]), 
				"WHERE (USER_ID=".$USER_ID.")", 
				$err_mess.__LINE__);
			$DB->Query("DELETE FROM b_forum_user_forum WHERE (USER_ID=".$USER_ID.") AND (LAST_VISIT < MAIN_LAST_VISIT)", True);
		}
		return true;
	}
	
	function GetListUserForumLastVisit($arOrder = Array("LAST_VISIT"=>"DESC"), $arFilter = Array())
	{
		global $DB;
		$arSqlSearch = Array();
		$arSqlOrder = Array();
		$strSqlSearch = "";
		$strSqlOrder = "";

		if (!is_array($arFilter))
			$filter_keys = Array();
		else
			$filter_keys = array_keys($arFilter);

		for ($i = 0; $i < count($filter_keys); $i++)
		{
			$val = $arFilter[$filter_keys[$i]];

			$key = $filter_keys[$i];
			$key_res = CForumNew::GetFilterOperation($key);
			$key = strToUpper($key_res["FIELD"]);
			$strNegative = $key_res["NEGATIVE"];
			$strOperation = $key_res["OPERATION"];

			switch ($key)
			{
				case "ID":
				case "USER_ID":
				case "FORUM_ID":
					if (IntVal($val)<=0)
						$arSqlSearch[] = ($strNegative=="Y"?"NOT":"")."(FUF.".$key." IS NULL OR FUF.".$key."<=0)";
					else
						$arSqlSearch[] = ($strNegative=="Y"?" FUF.".$key." IS NULL OR NOT ":"")."(FUF.".$key." ".$strOperation." ".IntVal($val)." )";
					break;
			}
		}
		for ($i=0; $i<count($arSqlSearch); $i++)
			$strSqlSearch .= " AND (".$arSqlSearch[$i].") ";
		foreach ($arOrder as $by=>$order)
		{
			$by = strtoupper($by); $order = strtoupper($order);
			if ($order!="ASC") $order = "DESC";

			if ($by == "USER_ID") $arSqlOrder[] = " FUF.USER_ID ".$order." ";
			elseif ($by == "FORUM_ID") $arSqlOrder[] = " FUF.FORUM_ID ".$order." ";
			elseif ($by == "LAST_VISIT") $arSqlOrder[] = " FUF.LAST_VISIT ".$order." ";
			else
			{
				$arSqlOrder[] = " FU.ID ".$order." ";
				$by = "ID";
			}
		}
		DelDuplicateSort($arSqlOrder); 
		if (count($arSqlOrder) > 0)
			$strSqlOrder = " ORDER BY ".implode(", ", $arSqlOrder);
			
		$strSql = "
			SELECT FUF.ID, FUF.FORUM_ID,  FUF.USER_ID, 
				".$DB->DateToCharFunction("FUF.LAST_VISIT", "FULL")." as LAST_VISIT, 
				".$DB->DateToCharFunction("FUF.MAIN_LAST_VISIT", "FULL")." as MAIN_LAST_VISIT 
			FROM b_forum_user_forum FUF
				INNER JOIN b_user U ON (U.ID = FUF.USER_ID)
			WHERE 1=1 ".$strSqlSearch."
			".$strSqlOrder;
		$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		return $db_res;
	}
	//---------------> User visited
	
	//---------------> User utils
	function CountUserPoints($USER_ID = 0, $iCnt = false)
	{
		$USER_ID = IntVal($USER_ID);
		if ($USER_ID<=0) return 0;

		$iNumUserPoints = 0;

		if ($iCnt === false)
			$iCnt = CForumMessage::GetList(array(), array("AUTHOR_ID"=>$USER_ID, "APPROVED"=>"Y"), true);
		
		$iNumUserPosts = IntVal($iCnt);

		$fPointsPerPost = 0.0;
		$db_res = CForumPoints2Post::GetList(array("MIN_NUM_POSTS"=>"DESC"), array("<=MIN_NUM_POSTS"=>$iNumUserPosts));
		if ($ar_res = $db_res->Fetch())
			$fPointsPerPost = DoubleVal($ar_res["POINTS_PER_POST"]);

		$iNumUserPoints += floor($fPointsPerPost*$iNumUserPosts);

		$iCnt = CForumUserPoints::CountSumPoints($USER_ID);
		$iNumUserPoints += $iCnt;

		return $iNumUserPoints;
	}

	function SetStat($USER_ID = 0)
	{
		$USER_ID = IntVal($USER_ID);
		if ($USER_ID<=0) return 0;

		$arUserFields = Array(
			"LAST_POST" => false,
			"LAST_POST_DATE" => false);
		
		$res = CForumUser::GetByUSER_IDEx($USER_ID);
		if ($res)
		{
			$arMessage = CForumMessage::GetByID($res["LAST_MESSAGE_ID"]);
			if ($arMessage)
			{
				$arUserFields["IP_ADDRESS"] = $arMessage["AUTHOR_IP"];
				$arUserFields["REAL_IP_ADDRESS"] = $arMessage["AUTHOR_REAL_IP"];
				$arUserFields["LAST_POST"] = IntVal($arMessage["ID"]);
				$arUserFields["LAST_POST_DATE"] = $arMessage["POST_DATE"];
			}
			$arUserFields["NUM_POSTS"] = IntVal($res["CNT"]);
			
			$arUserFields["POINTS"] = CForumUser::CountUserPoints($USER_ID, $arUserFields["NUM_POSTS"]);
			$ID = IntVal($res["ID"]);
			CForumUser::Update($ID, $arUserFields);
		}
		else 
		{
			$db_res = CForumMessage::GetList(array("ID"=>"DESC"), array("AUTHOR_ID"=>$USER_ID, "APPROVED"=>"Y"), false, 1);
			if ($res = $db_res->Fetch())
			{
				$arUserFields["IP_ADDRESS"] = $res["AUTHOR_IP"];
				$arUserFields["REAL_IP_ADDRESS"] = $res["AUTHOR_REAL_IP"];
				$arUserFields["LAST_POST"] = IntVal($res["ID"]);
				$arUserFields["LAST_POST_DATE"] = $res["POST_DATE"];
			}
			$iCnt = CForumMessage::GetList(array(), array("AUTHOR_ID"=>$USER_ID, "APPROVED"=>"Y"), true);
			$arUserFields["NUM_POSTS"] = IntVal($iCnt);
			$arUserFields["POINTS"] = CForumUser::CountUserPoints($USER_ID, $iCnt);
			$arUserFields["USER_ID"] = $USER_ID;
			$ID = CForumUser::Add($arUserFields);
		}
		return $ID;
	}
	//---------------> User actions
	function OnUserDelete($user_id)
	{
		global $DB;
		$user_id = IntVal($user_id);
		if ($user_id>0)
		{
			$DB->Query("UPDATE b_forum SET LAST_POSTER_ID = NULL WHERE LAST_POSTER_ID = ".$user_id."");
			$DB->Query("UPDATE b_forum_topic SET LAST_POSTER_ID = NULL WHERE LAST_POSTER_ID = ".$user_id."");
			$DB->Query("UPDATE b_forum_topic SET USER_START_ID = NULL WHERE USER_START_ID = ".$user_id."");
			$DB->Query("UPDATE b_forum_message SET AUTHOR_ID = NULL WHERE AUTHOR_ID = ".$user_id."");
			$DB->Query("DELETE FROM b_forum_subscribe WHERE USER_ID = ".$user_id."");

			$strSql = "
				SELECT 
					F.ID
				FROM 
					b_forum_user FU, 
					b_file F
				WHERE 
					FU.USER_ID = $user_id
				and FU.AVATAR = F.ID 
				";
			$z = $DB->Query($strSql, false, "FILE: ".__FILE__." LINE:".__LINE__);
			while ($zr = $z->Fetch()) CFile::Delete($zr["ID"]);

			$DB->Query("DELETE FROM b_forum_user WHERE USER_ID = ".$user_id."");
		}
		return true;
	}
	// >-- Using for private message
	function SearchUser($template)
	{
		global $DB;
		$template = $DB->ForSql(str_replace("*", "%", $template));
		
		$strSql = 
			"SELECT U.ID, U.NAME, U.LAST_NAME, U.LOGIN, F.SHOW_NAME ".
			"FROM b_forum_user F LEFT JOIN b_user U ON(F.USER_ID = U.ID)".
			"WHERE ((F.SHOW_NAME='Y')AND(U.NAME LIKE '".$template."' OR U.LAST_NAME LIKE '".$template."')) OR(( U.LOGIN LIKE '".$template."')AND(F.SHOW_NAME='N'))";		
		$err_mess = "FILE: ".__FILE__."<br>LINE: ";
		$dbRes = $DB->Query($strSql, false, $err_mess.__LINE__);
		return $dbRes;
	}
	// <-- Using for private message
}


/**********************************************************************/
/************** SUBSCRIBE *********************************************/
/**********************************************************************/
class CAllForumSubscribe
{
	//---------------> User insert, update, delete
	function CanUserAddSubscribe($FID, $arUserGroups)
	{
		if (CForumNew::GetUserPermission($FID, $arUserGroups)>="E") return True;
		return False;
	}

	function CanUserUpdateSubscribe($ID, $arUserGroups, $CurrentUserID = 0)
	{
		$ID = IntVal($ID);
		$CurrentUserID = IntVal($CurrentUserID);
		if (in_array(1, $arUserGroups)) return True;

		$arSubscr = CForumSubscribe::GetByID($ID);
		if ($arSubscr && IntVal($arSubscr["USER_ID"]) == $CurrentUserID) return True;
		return False;
	}

	function CanUserDeleteSubscribe($ID, $arUserGroups, $CurrentUserID = 0)
	{
		$ID = IntVal($ID);
		$CurrentUserID = IntVal($CurrentUserID);
		if (in_array(1, $arUserGroups)) return True;

		$arSubscr = CForumSubscribe::GetByID($ID);
		if ($arSubscr && IntVal($arSubscr["USER_ID"]) == $CurrentUserID) return True;
		return False;
	}

	function CheckFields($ACTION, &$arFields)
	{
		if ((is_set($arFields, "USER_ID") || $ACTION=="ADD") && IntVal($arFields["USER_ID"])<=0) return false;
		if ((is_set($arFields, "FORUM_ID") || $ACTION=="ADD") && IntVal($arFields["FORUM_ID"])<=0) return false;
		if ((is_set($arFields, "SITE_ID") || $ACTION=="ADD") && strlen($arFields["SITE_ID"])<=0) return false;

		if ((is_set($arFields, "TOPIC_ID") || $ACTION=="ADD") && IntVal($arFields["TOPIC_ID"])<=0) $arFields["TOPIC_ID"] = false;
		if ((is_set($arFields, "NEW_TOPIC_ONLY") || $ACTION=="ADD") && ($arFields["NEW_TOPIC_ONLY"]!="Y")) $arFields["NEW_TOPIC_ONLY"] = "N";

		if ($arFields["TOPIC_ID"]!==false) $arFields["NEW_TOPIC_ONLY"] = "N";

		if ($ACTION=="ADD")
		{
			$db_res = CForumSubscribe::GetList(array(), array("USER_ID"=>IntVal($arFields["USER_ID"]), "FORUM_ID"=>IntVal($arFields["FORUM_ID"]), "TOPIC_ID"=>IntVal($arFields["TOPIC_ID"])));
			if ($res = $db_res->Fetch())
			{
				return false;
			}
		}

		return True;
	}
	
	function Add($arFields)
	{
		global $DB;

		if (!CForumSubscribe::CheckFields("ADD", $arFields))
			return false;
			
		$Fields = array(
			"USER_ID" => intVal($arFields["USER_ID"]),
			"FORUM_ID" => intVal($arFields["FORUM_ID"]),
			"START_DATE" => $DB->GetNowFunction(),
			"NEW_TOPIC_ONLY" => "'".$DB->ForSQL($arFields["NEW_TOPIC_ONLY"], 1)."'",
			"SITE_ID" => "'".$DB->ForSQL($arFields["SITE_ID"], 2)."'"
			);
		if (intVal($arFields["TOPIC_ID"]) > 0)
			$Fields["TOPIC_ID"] = intVal($arFields["TOPIC_ID"]);
			
		return $DB->Insert("b_forum_subscribe", $Fields, "File: ".__FILE__."<br>Line: ".__LINE__);
	}
	
	function Update($ID, $arFields)
	{
		global $DB;
		$ID = IntVal($ID);

		if (!CForumSubscribe::CheckFields("UPDATE", $arFields))
			return false;

		$strUpdate = $DB->PrepareUpdate("b_forum_subscribe", $arFields);
		$strSql = "UPDATE b_forum_subscribe SET ".$strUpdate." WHERE ID = ".$ID;
		$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		return $ID;
	}

	function Delete($ID)
	{
		global $DB;
		$ID = IntVal($ID);
		return $DB->Query("DELETE FROM b_forum_subscribe WHERE ID = ".$ID, True);
	}
	
	function DeleteUSERSubscribe($USER_ID)
	{
		global $DB;
		$USER_ID = IntVal($USER_ID);
		return $DB->Query("DELETE FROM b_forum_subscribe WHERE USER_ID = ".$USER_ID, True);
	}

	function UpdateLastSend($MID, $sIDs)
	{
		global $DB;
		$MID = IntVal($MID);
		if (strlen($sIDs)<2) return False;

		$DB->Query(
			"UPDATE b_forum_subscribe SET ".
			"	LAST_SEND = ".$MID." ".
			"WHERE ID IN (".$sIDs.")"
		);
	}

	function GetList($arOrder = array("ID"=>"ASC"), $arFilter = array())
	{
		global $DB;
		$arSqlSearch = Array();

		if (!is_array($arFilter)) 
			$filter_keys = Array();
		else
			$filter_keys = array_keys($arFilter);

		for ($i = 0; $i < count($filter_keys); $i++)
		{
			$val = $arFilter[$filter_keys[$i]];

			$key = $filter_keys[$i];
			$key_res = CForumNew::GetFilterOperation($key);
			$key = strtoupper($key_res["FIELD"]);
			$strNegative = $key_res["NEGATIVE"];
			$strOperation = $key_res["OPERATION"];

			switch ($key)
			{
				case "ID":
				case "USER_ID":
				case "FORUM_ID":
				case "TOPIC_ID":
				case "LAST_SEND":
					if (IntVal($val)<=0)
						$arSqlSearch[] = ($strNegative=="Y"?"NOT":"")."(FP.".$key." IS NULL OR FP.".$key."<=0)";
					else
						$arSqlSearch[] = ($strNegative=="Y"?" FP.".$key." IS NULL OR NOT ":"")."(FP.".$key." ".$strOperation." ".IntVal($val)." )";
					break;
				case "TOPIC_ID_OR_NULL":
					$arSqlSearch[] = "(FP.TOPIC_ID = ".IntVal($val)." OR FP.TOPIC_ID = 0 OR FP.TOPIC_ID IS NULL)";
					break;
				case "NEW_TOPIC_ONLY":
					if (strlen($val)<=0)
						$arSqlSearch[] = ($strNegative=="Y"?"NOT":"")."(FP.NEW_TOPIC_ONLY IS NULL)";
					else
						$arSqlSearch[] = ($strNegative=="Y"?" FP.NEW_TOPIC_ONLY IS NULL OR NOT ":"")."(FP.NEW_TOPIC_ONLY ".$strOperation." '".$DB->ForSql($val)."' )";
					break;
				case "LAST_SEND_OR_NULL":
					$arSqlSearch[] = "(FP.LAST_SEND IS NULL OR FP.LAST_SEND = 0 OR FP.LAST_SEND < ".IntVal($val).")";
					break;
			}
		}

		$strSqlSearch = "";
		for ($i=0; $i<count($arSqlSearch); $i++)
		{
			$strSqlSearch .= " AND (".$arSqlSearch[$i].") ";
		}

		$strSql = 
			"SELECT FP.ID, FP.USER_ID, FP.FORUM_ID, FP.TOPIC_ID, FP.LAST_SEND, FP.NEW_TOPIC_ONLY, FP.SITE_ID, ".
			"	".$DB->DateToCharFunction("FP.START_DATE", "FULL")." as START_DATE ".
			"FROM b_forum_subscribe FP ".
			"WHERE 1 = 1 ".
			"	".$strSqlSearch." ";

		$arSqlOrder = Array();
		foreach ($arOrder as $by=>$order)
		{
			$by = strtoupper($by);
			$order = strtoupper($order);
			if ($order!="ASC") $order = "DESC";

			if ($by == "FORUM_ID") $arSqlOrder[] = " FP.FORUM_ID ".$order." ";
			elseif ($by == "USER_ID") $arSqlOrder[] = " FP.USER_ID ".$order." ";
			elseif ($by == "TOPIC_ID") $arSqlOrder[] = " FP.TOPIC_ID ".$order." ";
			elseif ($by == "NEW_TOPIC_ONLY") $arSqlOrder[] = " FP.NEW_TOPIC_ONLY ".$order." ";
			elseif ($by == "START_DATE") $arSqlOrder[] = " FP.START_DATE ".$order." ";
			else
			{
				$arSqlOrder[] = " FP.ID ".$order." ";
				$by = "ID";
			}
		}

		$strSqlOrder = "";
		DelDuplicateSort($arSqlOrder); for ($i=0; $i<count($arSqlOrder); $i++)
		{
			if ($i==0)
				$strSqlOrder = " ORDER BY ";
			else
				$strSqlOrder .= ", ";

			$strSqlOrder .= $arSqlOrder[$i];
		}

		$strSql .= $strSqlOrder;
		$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		return $db_res;
	}
	
	function GetListEx($arOrder = array("ID"=>"ASC"), $arFilter = array())
	{
		global $DB;
		$arSqlSearch = array();
		$arSqlFrom = array();
		$arSqlGroup = array();
		$arSqlSelect = array();
		$arSqlOrder = array();
		$strSqlSearch = "";
		$strSqlFrom = "";
		$strSqlGroup = "";
		$strSqlOrder = "";
		$arSqlSelectConst = array(
			"FS.ID" =>"FS.ID", 
			"FS.USER_ID" => "FS.USER_ID",
			"FS.FORUM_ID" => "FS.FORUM_ID",
			"FS.TOPIC_ID" => "FS.TOPIC_ID",
			"FS.LAST_SEND" => "FS.LAST_SEND",
			"FS.NEW_TOPIC_ONLY" => "FS.NEW_TOPIC_ONLY",
			"FS.SITE_ID" => "FS.SITE_ID",
			"START_DATE" => $DB->DateToCharFunction("FS.START_DATE", "FULL"), 
			"U.EMAIL" => "U.EMAIL",
			"U.LOGIN" => "U.LOGIN",
			"U.NAME" => "U.NAME",
			"U.LAST_NAME" =>"U.LAST_NAME",
			"FT.TITLE" => "FT.TITLE",
			"FORUM_NAME" => "F.NAME"
		);

		if (!is_array($arFilter))
			$filter_keys = Array();
		else
			$filter_keys = array_keys($arFilter);

		for ($i = 0; $i < count($filter_keys); $i++)
		{
			$val = $arFilter[$filter_keys[$i]];

			$key = $filter_keys[$i];
			$key_res = CForumNew::GetFilterOperation($key);
			$key = strtoupper($key_res["FIELD"]);
			$strNegative = $key_res["NEGATIVE"];
			$strOperation = $key_res["OPERATION"];

			switch ($key)
			{
				case "ID":
				case "USER_ID":
				case "FORUM_ID":
				case "TOPIC_ID":
				case "LAST_SEND":
					if (IntVal($val)<=0)
						$arSqlSearch[] = ($strNegative=="Y"?"NOT":"")."(FS.".$key." IS NULL OR FS.".$key."<=0)";
					else
						$arSqlSearch[] = ($strNegative=="Y"?" FS.".$key." IS NULL OR NOT ":"")."(FS.".$key." ".$strOperation." ".IntVal($val)." )";
					break;
				case "TOPIC_ID_OR_NULL":
					$arSqlSearch[] = "(FS.TOPIC_ID = ".IntVal($val)." OR FS.TOPIC_ID = 0 OR FS.TOPIC_ID IS NULL)";
					break;
				case "NEW_TOPIC_ONLY":
					if (strlen($val)<=0)
						$arSqlSearch[] = ($strNegative=="Y"?"NOT":"")."(FS.".$key." IS NULL)";
					else
						$arSqlSearch[] = ($strNegative=="Y"?" FS.".$key." IS NULL OR NOT ":"")."(FS.".$key." ".$strOperation." '".$DB->ForSql($val)."' )";
					break;
				case "START_DATE":
					if(strlen($val)<=0)
						$arSqlSearch[] = ($strNegative=="Y"?"NOT":"")."(FS.".$key." IS NULL)";
					else
						$arSqlSearch[] = ($strNegative=="Y"?" FS.".$key." IS NULL OR NOT ":"")."(FS.".$key." ".$strOperation." ".$DB->CharToDateFunction($DB->ForSql($val), "SHORT").")";
					break;
				case "LAST_SEND_OR_NULL":
					$arSqlSearch[] = "(FS.LAST_SEND IS NULL OR FS.LAST_SEND = 0 OR FS.LAST_SEND < ".IntVal($val).")";
					break;
				case "ACTIVE":
					if (strlen($val)<=0)
						$arSqlSearch[] = ($strNegative=="Y"?"NOT":"")."(U.".$key." IS NULL)";
					else
						$arSqlSearch[] = ($strNegative=="Y"?" U.".$key." IS NULL OR NOT ":"")."(U.".$key." ".$strOperation." '".$DB->ForSql($val)."' )";
					break;
				case "FORUM":
				case "TOPIC":
					$key = $key == "FORUM"	? "F.NAME" : "FT.TITLE";
					$arSqlSearch[] = GetFilterQuery($key, $val);
					break;
				case "PERMISSION":
					if (strlen($val)>0)
					{
						$arSqlSearch[] = "(
							(FP.PERMISSION >= '".$DB->ForSql($val)."') OR
							(FP1.PERMISSION >= '".$DB->ForSql($val)."') OR 
							((FP.ID IS NULL) AND (UG.GROUP_ID = 1)))";
						$arSqlSelect[] = "FU.SUBSC_GROUP_MESSAGE, FU.SUBSC_GET_MY_MESSAGE";
						$arSqlFrom[] = " 
							LEFT JOIN b_forum_user FU ON (U.ID = FU.USER_ID) 
							LEFT JOIN b_user_group UG ON (U.ID = UG.USER_ID) 
							LEFT JOIN b_forum_perms FP ON (FP.FORUM_ID = FS.FORUM_ID AND FP.GROUP_ID=UG.GROUP_ID)
							LEFT JOIN b_forum_perms FP1 ON (FP1.FORUM_ID = FS.FORUM_ID AND FP1.GROUP_ID=2)";
						$arSqlGroup = array_values($arSqlSelectConst);
						$arSqlGroup[] = "FU.SUBSC_GROUP_MESSAGE, FU.SUBSC_GET_MY_MESSAGE";
					}
					break;
			}
		}

		if (count($arSqlSelect) > 0)
			$strSqlSelect .= ", ".implode(", ", $arSqlSelect);
			
		if (count($arSqlSearch) > 0)
			$strSqlSearch .= " AND (".implode(") 
			AND 
			(", $arSqlSearch).") ";

		if (count($arSqlFrom)>0)
			$strSqlFrom .= " ".implode(" ", $arSqlFrom)." ";

		if (count($arSqlGroup)>0)
			$strSqlGroup .= " GROUP BY ".implode(", ", $arSqlGroup)." ";
			
		foreach ($arOrder as $by=>$order)
		{
			$by = strtoupper($by);
			$order = strtoupper($order);
			if ($order!="ASC") $order = "DESC";

			if ($by == "FORUM_ID") $arSqlOrder[] = " FS.FORUM_ID ".$order." ";
			elseif ($by == "USER_ID") $arSqlOrder[] = " FS.USER_ID ".$order." ";
			elseif ($by == "FORUM_NAME") $arSqlOrder[] = " F.NAME ".$order." ";
			elseif ($by == "TOPIC_ID") $arSqlOrder[] = " FS.TOPIC_ID ".$order." ";
			elseif ($by == "TITLE") $arSqlOrder[] = " FT.TITLE ".$order." ";
			elseif ($by == "START_DATE") $arSqlOrder[] = " FS.START_DATE ".$order." ";
			elseif ($by == "NEW_TOPIC_ONLY") $arSqlOrder[] = " FS.NEW_TOPIC_ONLY ".$order." ";
			elseif ($by == "LAST_SEND") $arSqlOrder[] = " FS.LAST_SEND ".$order." ";
			else
			{
				$arSqlOrder[] = " FS.ID ".$order." ";
				$by = "ID";
			}
		}
		DelDuplicateSort($arSqlOrder); 
		if (count($arSqlOrder)>0)
			$strSqlOrder = " ORDER BY ".implode(", ", $arSqlOrder);
			
		$strSql = "
			SELECT FS.ID, FS.USER_ID, FS.FORUM_ID, FS.TOPIC_ID, FS.LAST_SEND, FS.NEW_TOPIC_ONLY, FS.SITE_ID, 
				".$DB->DateToCharFunction("FS.START_DATE", "FULL")." as START_DATE, 
				U.EMAIL, U.LOGIN, U.NAME, U.LAST_NAME, FT.TITLE, F.NAME AS FORUM_NAME".$strSqlSelect."
			 FROM b_forum_subscribe FS 
				INNER JOIN b_user U ON (FS.USER_ID = U.ID) 
				LEFT JOIN b_forum_topic FT ON (FS.TOPIC_ID = FT.ID) 
				LEFT JOIN b_forum F ON (FS.FORUM_ID = F.ID) 
				".$strSqlFrom." 
			WHERE 1 = 1 
				".$strSqlSearch." 
			".$strSqlGroup."
			".$strSqlOrder;
		$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		return $db_res;
	}

	function GetByID($ID)
	{
		global $DB;
		$ID = IntVal($ID);

		$strSql = 
			"SELECT FP.ID, FP.USER_ID, FP.FORUM_ID, FP.TOPIC_ID, FP.LAST_SEND, FP.NEW_TOPIC_ONLY, FP.SITE_ID, ".
			"	".$DB->DateToCharFunction("FP.START_DATE", "FULL")." as START_DATE ".
			"FROM b_forum_subscribe FP ".
			"WHERE FP.ID = ".$ID."";
		$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		if ($res = $db_res->Fetch())
		{
			return $res;
		}
		return False;
	}
}

/**********************************************************************/
/************** RANK **************************************************/
/**********************************************************************/
class CAllForumRank
{
	//---------------> User insert, update, delete
	function CanUserAddRank($arUserGroups)
	{
		if (in_array(1, $arUserGroups)) return True;
		return False;
	}

	function CanUserUpdateRank($ID, $arUserGroups)
	{
		if (in_array(1, $arUserGroups)) return True;
		return False;
	}

	function CanUserDeleteRank($ID, $arUserGroups)
	{
		if (in_array(1, $arUserGroups)) return True;
		return False;
	}

	function CheckFields($ACTION, &$arFields)
	{
		if (is_set($arFields, "LANG") || $ACTION=="ADD")
		{
			for ($i = 0; $i<count($arFields["LANG"]); $i++)
			{
				if (!is_set($arFields["LANG"][$i], "LID") || strlen($arFields["LANG"][$i]["LID"])<=0) return false;
				if (!is_set($arFields["LANG"][$i], "NAME") || strlen($arFields["LANG"][$i]["NAME"])<=0) return false;
			}

			$db_lang = CLang::GetList(($b="sort"), ($o="asc"));
			while ($arLang = $db_lang->Fetch())
			{
				$bFound = False;
				for ($i = 0; $i<count($arFields["LANG"]); $i++)
				{
					if ($arFields["LANG"][$i]["LID"]==$arLang["LID"])
						$bFound = True;
				}
				if (!$bFound) return false;
			}
		}

		return True;
	}

	function Delete($ID)
	{
		global $DB;
		$ID = IntVal($ID);

		$arUsers = array();
		$db_res = CForumUser::GetList(array(), array("RANK_ID"=>$ID));
		while ($ar_res = $db_res->Fetch())
		{
			$arUsers[] = $ar_res["USER_ID"];
		}

		$DB->Query("DELETE FROM b_forum_rank_lang WHERE RANK_ID = ".$ID, True);
		$DB->Query("DELETE FROM b_forum_rank WHERE ID = ".$ID, True);

		for ($i = 0; $i < count($arUsers); $i++)
		{
			CForumUser::SetStat(IntVal($arUsers[$i]));
		}

		return true;
	}

	function GetList($arOrder = array("MIN_NUM_POSTS"=>"ASC"), $arFilter = array())
	{
		global $DB;
		$arSqlSearch = array();
		$arSqlOrder = array();
		$strSqlSearch = "";
		$strSqlOrder = "";

		if (!is_array($arFilter)) 
			$filter_keys = Array();
		else
			$filter_keys = array_keys($arFilter);

		for ($i = 0; $i < count($filter_keys); $i++)
		{
			$val = $arFilter[$filter_keys[$i]];

			$key = $filter_keys[$i];
			$key_res = CForumNew::GetFilterOperation($key);
			$key = strtoupper($key_res["FIELD"]);
			$strNegative = $key_res["NEGATIVE"];
			$strOperation = $key_res["OPERATION"];

			switch ($key)
			{
				case "ID":
				case "MIN_NUM_POSTS":
					if (IntVal($val)<=0)
						$arSqlSearch[] = ($strNegative=="Y"?"NOT":"")."(FR.".$key." IS NULL OR FR.".$key."<=0)";
					else
						$arSqlSearch[] = ($strNegative=="Y"?" FR.".$key." IS NULL OR NOT ":"")."(FR.".$key." ".$strOperation." ".IntVal($val)." )";
					break;
			}
		}

		if (count($arSqlSearch) > 0)
			$strSqlSearch = " AND (".implode(") AND (", $arSqlSearch).") ";

		foreach ($arOrder as $by=>$order)
		{
			$by = strtoupper($by); $order = strtoupper($order);
			if ($order!="ASC") $order = "DESC";

			if ($by == "ID") $arSqlOrder[] = " FR.ID ".$order." ";
			else
			{
				$arSqlOrder[] = " FR.MIN_NUM_POSTS ".$order." ";
				$by = "MIN_NUM_POSTS";
			}
		}
		DelDuplicateSort($arSqlOrder); 
		if (count($arSqlOrder) > 0)
			$strSqlOrder = " ORDER BY ".implode(", ", $arSqlOrder);
			
		$strSql = 
			"SELECT FR.ID, FR.MIN_NUM_POSTS 
			FROM b_forum_rank FR 
			WHERE 1 = 1 
			".$strSqlSearch." 
			".$strSqlOrder;

		$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		return $db_res;
	}

	function GetListEx($arOrder = array("MIN_NUM_POSTS"=>"ASC"), $arFilter = array())
	{
		global $DB;
		$arSqlSearch = array();
		$arSqlOrder = array();
		$strSqlSearch = "";
		$strSqlOrder = "";

		if (!is_array($arFilter)) 
			$filter_keys = Array();
		else
			$filter_keys = array_keys($arFilter);

		for ($i = 0; $i < count($filter_keys); $i++)
		{
			$val = $arFilter[$filter_keys[$i]];

			$key = $filter_keys[$i];
			$key_res = CForumNew::GetFilterOperation($key);
			$key = strtoupper($key_res["FIELD"]);
			$strNegative = $key_res["NEGATIVE"];
			$strOperation = $key_res["OPERATION"];

			switch ($key)
			{
				case "ID":
				case "MIN_NUM_POSTS":
					if (IntVal($val)<=0)
						$arSqlSearch[] = ($strNegative=="Y"?"NOT":"")."(FR.".$key." IS NULL OR FR.".$key."<=0)";
					else
						$arSqlSearch[] = ($strNegative=="Y"?" FR.".$key." IS NULL OR NOT ":"")."(FR.".$key." ".$strOperation." ".IntVal($val)." )";
					break;
				case "LID":
					if (strlen($val)<=0)
						$arSqlSearch[] = ($strNegative=="Y"?"NOT":"")."(FRL.LID IS NULL OR LENGTH(FRL.LID)<=0)";
					else
						$arSqlSearch[] = ($strNegative=="Y"?" FRL.LID IS NULL OR NOT ":"")."(FRL.LID ".$strOperation." '".$DB->ForSql($val)."' )";
					break;
			}
		}
		if (count($arSqlSearch) > 0)
			$strSqlSearch = " AND (".imlode(" ) AND (", $arSqlSearch).") ";

		foreach ($arOrder as $by=>$order)
		{
			$by = strtoupper($by);	$order = strtoupper($order);
			if ($order!="ASC") $order = "DESC";

			if ($by == "ID") $arSqlOrder[] = " FR.ID ".$order." ";
			elseif ($by == "LID") $arSqlOrder[] = " FRL.LID ".$order." ";
			elseif ($by == "NAME") $arSqlOrder[] = " FRL.NAME ".$order." ";
			else
			{
				$arSqlOrder[] = " FR.MIN_NUM_POSTS ".$order." ";
				$by = "MIN_NUM_POSTS";
			}
		}
		DelDuplicateSort($arSqlOrder); 
		if (count($arSqlOrder) > 0)
			$strSqlOrder = " ORDER BY ".implode(", ", $arSqlOrder);

		$strSql = "
			SELECT FR.ID, FR.MIN_NUM_POSTS, FRL.LID, FRL.NAME 
			FROM b_forum_rank FR 
				LEFT JOIN b_forum_rank_lang FRL ON FR.ID = FRL.RANK_ID 
			WHERE 1 = 1 
			".$strSqlSearch."
			".$strSqlOrder;

		$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		return $db_res;
	}

	function GetByID($ID)
	{
		global $DB;

		$ID = IntVal($ID);
		$strSql = 
			"SELECT FR.ID, FR.MIN_NUM_POSTS ".
			"FROM b_forum_rank FR ".
			"WHERE FR.ID = ".$ID."";
		$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		if ($res = $db_res->Fetch())
		{
			return $res;
		}
		return False;
	}

	function GetByIDEx($ID, $strLang)
	{
		global $DB;

		$ID = IntVal($ID);
		$strSql = 
			"SELECT FR.ID, FRL.LID, FRL.NAME, FR.MIN_NUM_POSTS ".
			"FROM b_forum_rank FR ".
			"	LEFT JOIN b_forum_rank_lang FRL ON (FR.ID = FRL.RANK_ID AND FRL.LID = '".$DB->ForSql($strLang)."') ".
			"WHERE FR.ID = ".$ID."";
		$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		if ($res = $db_res->Fetch())
		{
			return $res;
		}
		return False;
	}

	function GetLangByID($RANK_ID, $strLang)
	{
		global $DB;

		$RANK_ID = IntVal($RANK_ID);
		$strSql = 
			"SELECT FRL.ID, FRL.RANK_ID, FRL.LID, FRL.NAME ".
			"FROM b_forum_rank_lang FRL ".
			"WHERE FRL.RANK_ID = ".$RANK_ID." ".
			"	AND FRL.LID = '".$DB->ForSql($strLang)."' ";
		$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		if ($res = $db_res->Fetch())
		{
			return $res;
		}
		return False;
	}
	
	
}

class CALLForumStat
{
	function RegisterUSER_OLD($arFields = array())
	{
		global $DB, $USER;
		$tmp = "";
		if ($_SESSION["FORUM"]["SHOW_NAME"] == "Y" && strLen(trim($_SESSION["SESS_AUTH"]["NAME"])) > 0)
			$tmp = $_SESSION["SESS_AUTH"]["NAME"];
		else 
			$tmp = $_SESSION["SESS_AUTH"]["LOGIN"];
			
			
		$session_id = "'".$DB->ForSQL(session_id(), 255)."'";
		$Fields = array(
			"FS.USER_ID" => intVal($USER->GetID()), 
			"FS.IP_ADDRESS" => "'".$DB->ForSql($_SERVER["REMOTE_ADDR"],15)."'",
			"FS.SHOW_NAME" => "'".$DB->ForSQL($tmp, 255)."'",
			"FS.LAST_VISIT" => $DB->GetNowFunction(),
			"FS.FORUM_ID" => intVal($arFields["FORUM_ID"]),
			"FS.TOPIC_ID" => intVal($arFields["TOPIC_ID"])
			);
		$FieldsForInsert = array(
			"USER_ID" => $Fields["FS.USER_ID"], 
			"IP_ADDRESS" => $Fields["FS.IP_ADDRESS"],
			"SHOW_NAME" => $Fields["FS.SHOW_NAME"],
			"LAST_VISIT" => $Fields["FS.LAST_VISIT"],
			"FORUM_ID" => $Fields["FS.FORUM_ID"],
			"TOPIC_ID" => $Fields["FS.TOPIC_ID"],
			"PHPSESSID" => $session_id
			);
			
			
		if (intVal($USER->GetID()) > 0)
		{
			$FieldsForUpdate = $Fields;
			$FieldsForUpdate["FU.LAST_VISIT"] = $DB->GetNowFunction();
			$rows = $DB->Update(
				"b_forum_user FU, b_forum_stat FS", 
				$FieldsForUpdate, 
				"WHERE (FU.USER_ID=".$Fields["FS.USER_ID"].") AND (FS.PHPSESSID=".$session_id.")", 
				$err_mess.__LINE__,
				false);
				
			if (intVal($rows) < 2)
			{
				if (intVal($rows)<=0)
				{
					$rows = $DB->Update(
						"b_forum_user", 
						array("USER_ID" => $Fields["FS.USER_ID"]), 
						"WHERE (USER_ID=".$Fields["FS.USER_ID"].")", 
						$err_mess.__LINE__,
						false);
					if (intVal($rows) <= 0)
					{
						$ID = CForumUser::Add(array("USER_ID" => $Fields["FS.USER_ID"]));
					}
					
					$rows = $DB->Update(
						"b_forum_stat", 
						array(
							"USER_ID" => $Fields["FS.USER_ID"], 
							"IP_ADDRESS" => $Fields["FS.IP_ADDRESS"],
							"SHOW_NAME" => $Fields["FS.SHOW_NAME"],
							"LAST_VISIT" => $Fields["FS.LAST_VISIT"],
							"FORUM_ID" => $Fields["FS.FORUM_ID"],
							"TOPIC_ID" => $Fields["FS.TOPIC_ID"],
							), 
						"WHERE (PHPSESSID=".$session_id.")", 
						$err_mess.__LINE__,
						false);
					if (intVal($rows) <= 0)
					{
						$DB->Insert("b_forum_stat", $FieldsForInsert, $err_mess.__LINE__);
					}
				}
			}
		}
		else 
		{
			$rows = $DB->Update(
				"b_forum_stat", 
				array(
					"USER_ID" => $Fields["FS.USER_ID"], 
					"IP_ADDRESS" => $Fields["FS.IP_ADDRESS"],
					"SHOW_NAME" => $Fields["FS.SHOW_NAME"],
					"LAST_VISIT" => $Fields["FS.LAST_VISIT"],
					"FORUM_ID" => $Fields["FS.FORUM_ID"],
					"TOPIC_ID" => $Fields["FS.TOPIC_ID"],
					), 
				"WHERE (PHPSESSID=".$session_id.")", $err_mess.__LINE__);		
				
			if (intVal($rows)<=0)
			{
				$DB->Insert("b_forum_stat", $FieldsForInsert, $err_mess.__LINE__);
			}	
		}
		return true;
	}
	
	function RegisterUSER($arFields = array())
	{
		global $DB, $USER;
		$tmp = "";
		if ($_SESSION["FORUM"]["SHOW_NAME"] == "Y" && strLen(trim($_SESSION["SESS_AUTH"]["NAME"])) > 0)
			$tmp = $_SESSION["SESS_AUTH"]["NAME"];
		else 
			$tmp = $_SESSION["SESS_AUTH"]["LOGIN"];
		$session_id = "'".$DB->ForSQL(session_id(), 255)."'";
		$Fields = array(
			"USER_ID" => intVal($USER->GetID()), 
			"IP_ADDRESS" => "'".$DB->ForSql($_SERVER["REMOTE_ADDR"],15)."'",
			"SHOW_NAME" => "'".$DB->ForSQL($tmp, 255)."'",
			"LAST_VISIT" => $DB->GetNowFunction(),
			"FORUM_ID" => intVal($arFields["FORUM_ID"]),
			"TOPIC_ID" => intVal($arFields["TOPIC_ID"]));
		
		$rows = $DB->Update("b_forum_stat", $Fields, "WHERE PHPSESSID=".$session_id."", $err_mess.__LINE__);		
		if (intval($rows)<=0)
		{
			$Fields = array(
				"USER_ID" => intVal($USER->GetID()), 
				"IP_ADDRESS" => "'".$DB->ForSql($_SERVER["REMOTE_ADDR"],15)."'",
				"SHOW_NAME" => "'".$DB->ForSQL($tmp, 255)."'",
				"PHPSESSID" => "'".$DB->ForSQL(session_id(), 255)."'", 
				"LAST_VISIT" => $DB->GetNowFunction(),
				"FORUM_ID" => intVal($arFields["FORUM_ID"]),
				"TOPIC_ID" => intVal($arFields["TOPIC_ID"]));
			return $DB->Insert("b_forum_stat", $Fields, $err_mess.__LINE__);
		}
		else
			return true;
	}
	
	function Add($arFields)
	{
		global $DB, $USER;
		$Fields = array(
			"USER_ID" => $USER->GetID(), 
			"IP_ADDRESS" => "'".$DB->ForSql($_SERVER["REMOTE_ADDR"],15)."'",
			"PHPSESSID" => "'".$DB->ForSQL(session_id(), 255)."'", 
			"LAST_VISIT" => "'".$DB->GetNowFunction()."'",
			"FORUM_ID" => intVal($arFields["FORUM_ID"]),
			"TOPIC_ID" => intVal($arFields["TOPIC_ID"]));

		return $DB->Insert("b_forum_stat", $Fields, "File: ".__FILE__."<br>Line: ".__LINE__);
	}
	
	function GetListEx($arOrder = Array("ID"=>"ASC"), $arFilter = Array())
	{
		global $DB;
		$arSqlSearch = array();
		$arSqlSelect = array();
		$arSqlFrom = array();
		$arSqlGroup = array();
		$arSqlOrder = array();
		$arSql = array(); 
		$strSqlSearch = "";
		$strSqlSelect = "";
		$strSqlFrom = "";
		$strSqlGroup = "";
		$strSqlOrder = "";
		$strSql = "";
		
		$arSqlSelectConst = array(
			"FSTAT.USER_ID" => "FSTAT.USER_ID", 
			"FSTAT.IPADDRES" => "FSTAT.IPADDRES", 
			"FSTAT.PHPSESSID" => "FSTAT.PHPSESSID", 
			"LAST_VISIT" => $DB->DateToCharFunction("FSTAT.LAST_VISIT", "FULL"), 
			"FSTAT.FORUM_ID" => "FSTAT.FORUM_ID",
			"FSTAT.TOPIC_ID" => "FSTAT.TOPIC_ID"
		);
		$arSqlSelect = $arSqlSelectConst;

		if (!is_array($arFilter))
			$filter_keys = Array();
		else
			$filter_keys = array_keys($arFilter);

		for ($i = 0; $i < count($filter_keys); $i++)
		{
			$val = $arFilter[$filter_keys[$i]];
			$key = $filter_keys[$i];
			$key_res = CForumNew::GetFilterOperation($key);
			$key = strtoupper($key_res["FIELD"]);
			$strNegative = $key_res["NEGATIVE"];
			$strOperation = $key_res["OPERATION"];

			switch ($key)
			{
				case "TOPIC_ID":
				case "FORUM_ID":
				case "USER_ID":
					if (IntVal($val)<=0)
						$arSqlSearch[] = ($strNegative=="Y"?"NOT":"")."(FSTAT.".$key." IS NULL OR FSTAT.".$key."<=0)";
					else
						$arSqlSearch[] = ($strNegative=="Y"?" FSTAT.".$key." IS NULL OR NOT ":"")."(FSTAT.".$key." ".$strOperation." ".IntVal($val)." )";
					break;
				case "LAST_VISIT":
					if(strlen($val)<=0)
						$arSqlSearch[] = ($strNegative=="Y"?"NOT":"")."(FSTAT.".$key." IS NULL)";
					else
						$arSqlSearch[] = ($strNegative=="Y"?" FSTAT.".$key." IS NULL OR NOT ":"")."(FSTAT.".$key." ".$strOperation." ".$DB->CharToDateFunction($DB->ForSql($val), "FULL").")";
					break;
				case "HIDE_FROM_ONLINE":
					$arSqlFrom["FU"] = "LEFT JOIN b_forum_user FU ON FSTAT.USER_ID=FU.USER_ID";
					if (strlen($val)<=0)
						$arSqlSearch[] = ($strNegative=="Y"?"NOT":"")."(FU.".$key." IS NULL OR LENGTH(FU.".$key.")<=0)";
					else
						$arSqlSearch[] = ($strNegative=="Y"?" FU.".$key." IS NULL OR NOT ":"")."(FU.".$key." ".$strOperation." '".$DB->ForSql($val)."' )";
					break;
				break;
				case "COUNT_GUEST":
					$arSqlSelect = array(
						"FSTAT.USER_ID" => "FSTAT.USER_ID", 
						"FSTAT.SHOW_NAME" => "FSTAT.SHOW_NAME", 
						"COUNT_USER" => "COUNT(FSTAT.PHPSESSID) AS COUNT_USER", 
					);
					$arSqlGroup["FSTAT.USER_ID"] = "FSTAT.USER_ID";
					$arSqlGroup["FSTAT.SHOW_NAME"] = "FSTAT.SHOW_NAME";
					break;
			}
		}
		if (count($arSqlSearch) > 0)
			$strSqlSearch = " AND (".implode(") AND (", $arSqlSearch).") ";
		if (count($arSqlSelect) > 0)
			$strSqlSelect = implode(", ", $arSqlSelect);
		if (count($arSqlFrom) > 0)
			$strSqlFrom = implode("	", $arSqlFrom);
		if (count($arSqlGroup) > 0)
			$strSqlGroup = " GROUP BY ".implode(", ", $arSqlGroup);


		foreach ($arOrder as $by=>$order)
		{
			$by = strtoupper($by); $order = strtoupper($order);
			$order = $order!="ASC" ? $order = "DESC" : "ASC";

			if ($by == "USER_ID") $arSqlOrder[] = " FSTAT.USER_ID ".$order." ";
		}

		DelDuplicateSort($arSqlOrder); 
		if (count($arSqlOrder) > 0)
			$strSqlOrder = " ORDER BY ".implode(", ", $arSqlOrder);

		$strSql = " SELECT ".$strSqlSelect."
			FROM b_forum_stat FSTAT
			".$strSqlFrom."
			WHERE 1=1 
			".$strSqlSearch."
			".$strSqlGroup."
			".$strSqlOrder;
			
		$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		return $db_res;
	}
	
	function CleanUp($period = 48) // time in hours
	{
		global $DB;
		$period = intVal($period)*3600;
		$date = $DB->CharToDateFunction($DB->ForSql(Date(CDatabase::DateFormatToPHP(CLang::GetDateFormat("FULL", LANG)), time()-$period)), "FULL") ;
		$strSQL = "DELETE FROM b_forum_stat 
					WHERE (LAST_VISIT
					< ".$date.")";
		$DB->Query($strSQL, false, $err_mess.__LINE__);
		return "CForumStat::CleanUp();";
	}
}

?>