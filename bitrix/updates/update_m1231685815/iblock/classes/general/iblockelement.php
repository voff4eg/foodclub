<?
global $IBLOCK_ACTIVE_DATE_FORMAT;
$IBLOCK_ACTIVE_DATE_FORMAT = Array();
global $BX_IBLOCK_PROP_CACHE;
$BX_IBLOCK_PROP_CACHE = Array();
global $ar_IBLOCK_SITE_FILTER_CACHE;
$ar_IBLOCK_SITE_FILTER_CACHE = Array();

//IncludeModuleLangFile(__FILE__);
class CAllIBlockElement
{
	///////////////////////////////////////////////////////////////////
	// Send changing status message
	///////////////////////////////////////////////////////////////////
	function WF_SetMove($NEW_ID, $OLD_ID = 0)
	{
		if(CModule::IncludeModule("workflow"))
		{
			$err_mess = "FILE: ".__FILE__."<br>LINE: ";
			global $DB, $USER;
			$NEW = "Y";
			$OLD_ID = intval($OLD_ID);
			$NEW_ID = intval($NEW_ID);
			if($OLD_ID>0)
			{
				$old = $DB->Query("SELECT WF_STATUS_ID FROM b_iblock_element WHERE ID = ".$OLD_ID, false, $err_mess.__LINE__);
				if($old_r=$old->Fetch())
					$NEW = "N";
			}
			$new = CIBlockElement::GetByID($NEW_ID);
			if($new_r=$new->Fetch())
			{
				$NEW_STATUS_ID = $new_r["WF_STATUS_ID"];
				$OLD_STATUS_ID = $old_r["WF_STATUS_ID"];
				$PARENT_ID = $new_r["WF_PARENT_ELEMENT_ID"];
				$parent = CIBlockElement::GetByID($PARENT_ID);
				if($parent_r = $parent->Fetch())
				{
					$arFields = array(
						"TIMESTAMP_X"		=> $DB->GetNowFunction(),
						"IBLOCK_ELEMENT_ID"	=> "'".intval($PARENT_ID)."'",
						"OLD_STATUS_ID"		=> "'".intval($OLD_STATUS_ID)."'",
						"STATUS_ID"		=> "'".intval($NEW_STATUS_ID)."'",
						"USER_ID"		=> "'".intval($USER->GetID())."'"
						);
					$DB->Insert("b_workflow_move", $arFields, $err_mess.__LINE__);
					if($NEW_STATUS_ID != $OLD_STATUS_ID)
					{
						// Get creator Email
						$strSql = "SELECT EMAIL FROM b_user WHERE ID = ".intval($parent_r["CREATED_BY"]);
						$rs = $DB->Query($strSql, false, $err_mess.__LINE__);
						if($ar = $rs->Fetch())
							$parent_r["CREATED_BY_EMAIL"] = $ar["EMAIL"];
						else
							$parent_r["CREATED_BY_EMAIL"] = "";

						// gather email of the workflow admins
						$WORKFLOW_ADMIN_GROUP_ID = intval(COption::GetOptionString("workflow", "WORKFLOW_ADMIN_GROUP_ID"));
						$strSql = "
							SELECT U.ID, U.EMAIL
							FROM b_user U, b_user_group UG
							WHERE
								UG.GROUP_ID=".$WORKFLOW_ADMIN_GROUP_ID."
								AND U.ID = UG.USER_ID
								AND U.ACTIVE='Y'
						";
						$rs = $DB->Query($strSql, false, $err_mess.__LINE__);
						$arAdmin = Array();
						while($ar = $rs->Fetch())
						{
							$arAdmin[$ar["ID"]] = $ar["EMAIL"];
						}

						// gather email for BCC
						$arBCC = array();

						// gather all who changed doc in its current status
						$strSql = "
							SELECT U.EMAIL
							FROM
								b_workflow_move WM
								INNER JOIN b_user U on U.ID = WM.USER_ID
							WHERE
								IBLOCK_ELEMENT_ID = ".$PARENT_ID."
								AND OLD_STATUS_ID = ".$NEW_STATUS_ID."
						";
						$rs = $DB->Query($strSql, false, $err_mess.__LINE__);
						while($ar = $rs->Fetch())
						{
							$arBCC[$ar["EMAIL"]] = $ar["EMAIL"];
						}

						// gather all editors
						// in case status have notifier flag

						//First those who have write permissions on iblock
						$strSql = "
							SELECT U.EMAIL
							FROM
								b_workflow_status S
								INNER JOIN b_workflow_status2group SG on SG.STATUS_ID = S.ID
								INNER JOIN b_iblock_group IG on IG.GROUP_ID = SG.GROUP_ID
								INNER JOIN b_user_group UG on UG.GROUP_ID = IG.GROUP_ID
								INNER JOIN b_user U on U.ID = UG.USER_ID
							WHERE
								S.ID = ".$NEW_STATUS_ID."
								AND S.NOTIFY = 'Y'
								AND IG.IBLOCK_ID = ".intval($new_r["IBLOCK_ID"])."
								AND IG.PERMISSION >= 'U'
								AND SG.PERMISSION_TYPE = '2'
								AND U.ACTIVE = 'Y'
						";
						$rs = $DB->Query($strSql, false, $err_mess.__LINE__);
						while($ar = $rs->Fetch())
						{
							$arBCC[$ar["EMAIL"]] = $ar["EMAIL"];
						}

						//Second admins if they in PERMISSION_TYPE = 2 list
						//because they have all the rights
						$strSql = "
							SELECT U.EMAIL
							FROM
								b_workflow_status S
								INNER JOIN b_workflow_status2group SG on SG.STATUS_ID = S.ID
								INNER JOIN b_user_group UG on UG.GROUP_ID = SG.GROUP_ID
								INNER JOIN b_user U on U.ID = UG.USER_ID
							WHERE
								S.ID = ".$NEW_STATUS_ID."
								AND S.NOTIFY = 'Y'
								AND SG.GROUP_ID = 1
								AND SG.PERMISSION_TYPE = '2'
								AND U.ACTIVE = 'Y'
						";
						$rs = $DB->Query($strSql, false, $err_mess.__LINE__);
						while($ar = $rs->Fetch())
						{
							$arBCC[$ar["EMAIL"]] = $ar["EMAIL"];
						}

						$iblock_r = CIBlock::GetArrayByID($new_r["IBLOCK_ID"]);

						if(array_key_exists($new_r["MODIFIED_BY"], $arAdmin))
							$new_r["USER_NAME"] .= " (Admin)";
						// it is not new doc
						if($NEW!="Y")
						{
							if(array_key_exists($parent_r["CREATED_BY"], $arAdmin))
								$parent_r["CREATED_USER_NAME"] .= " (Admin)";

							// send change notification
							$arEventFields = array(
								"ID"			=> $PARENT_ID,
								"IBLOCK_ID"		=> $new_r["IBLOCK_ID"],
								"IBLOCK_TYPE"		=> $iblock_r["IBLOCK_TYPE_ID"],
								"ADMIN_EMAIL"		=> implode(",", $arAdmin),
								"BCC"			=> implode(",", $arBCC),
								"PREV_STATUS_ID"	=> $OLD_STATUS_ID,
								"PREV_STATUS_TITLE"	=> CIblockElement::WF_GetStatusTitle($OLD_STATUS_ID),
								"STATUS_ID"		=> $NEW_STATUS_ID,
								"STATUS_TITLE"		=> CIblockElement::WF_GetStatusTitle($NEW_STATUS_ID),
								"DATE_CREATE"		=> $parent_r["DATE_CREATE"],
								"CREATED_BY_ID"		=> $parent_r["CREATED_BY"],
								"CREATED_BY_NAME"	=> $parent_r["CREATED_USER_NAME"],
								"CREATED_BY_EMAIL"	=> $parent_r["CREATED_BY_EMAIL"],
								"DATE_MODIFY"		=> $new_r["TIMESTAMP_X"],
								"MODIFIED_BY_ID"	=> $new_r["MODIFIED_BY"],
								"MODIFIED_BY_NAME"	=> $new_r["USER_NAME"],
								"NAME"			=> $new_r["NAME"],
								"SECTION_ID"		=> $new_r["IBLOCK_SECTION_ID"],
								"PREVIEW_HTML"		=> ($new_r["PREVIEW_TEXT_TYPE"]=="html" ?$new_r["PREVIEW_TEXT"]:TxtToHtml($new_r["PREVIEW_TEXT"])),
								"PREVIEW_TEXT"		=> ($new_r["PREVIEW_TEXT_TYPE"]=="text"? $new_r["PREVIEW_TEXT"]:HtmlToTxt($new_r["PREVIEW_TEXT"])),
								"PREVIEW"		=> $new_r["PREVIEW_TEXT"],
								"PREVIEW_TYPE"		=> $new_r["PREVIEW_TEXT_TYPE"],
								"DETAIL_HTML"		=> ($new_r["DETAIL_TEXT_TYPE"]=="html" ?$new_r["DETAIL_TEXT"]:TxtToHtml($new_r["DETAIL_TEXT"])),
								"DETAIL_TEXT"		=> ($new_r["DETAIL_TEXT_TYPE"]=="text"? $new_r["DETAIL_TEXT"]:HtmlToTxt($new_r["DETAIL_TEXT"])),
								"DETAIL"		=> $new_r["DETAIL_TEXT"],
								"DETAIL_TYPE"		=> $new_r["DETAIL_TEXT_TYPE"],
								"COMMENTS"		=> $new_r["WF_COMMENTS"]
							);
							CEvent::Send("WF_IBLOCK_STATUS_CHANGE", $iblock_r["LID"], $arEventFields);
						}
						else // otherwise
						{
							// it was new one

							$arEventFields = array(
								"ID"			=> $PARENT_ID,
								"IBLOCK_ID"		=> $new_r["IBLOCK_ID"],
								"IBLOCK_TYPE"		=> $iblock_r["IBLOCK_TYPE_ID"],
								"ADMIN_EMAIL"		=> implode(",", $arAdmin),
								"BCC"			=> implode(",", $arBCC),
								"STATUS_ID"		=> $NEW_STATUS_ID,
								"STATUS_TITLE"		=> CIblockElement::WF_GetStatusTitle($NEW_STATUS_ID),
								"DATE_CREATE"		=> $parent_r["DATE_CREATE"],
								"CREATED_BY_ID"		=> $parent_r["CREATED_BY"],
								"CREATED_BY_NAME"	=> $parent_r["CREATED_USER_NAME"],
								"CREATED_BY_EMAIL"	=> $parent_r["CREATED_BY_EMAIL"],
								"NAME"			=> $new_r["NAME"],
								"PREVIEW_HTML"		=> ($new_r["PREVIEW_TEXT_TYPE"]=="html" ?$new_r["PREVIEW_TEXT"]:TxtToHtml($new_r["PREVIEW_TEXT"])),
								"PREVIEW_TEXT"		=> ($new_r["PREVIEW_TEXT_TYPE"]=="text"? $new_r["PREVIEW_TEXT"]:HtmlToTxt($new_r["PREVIEW_TEXT"])),
								"PREVIEW"		=> $new_r["PREVIEW_TEXT"],
								"PREVIEW_TYPE"		=> $new_r["PREVIEW_TEXT_TYPE"],
								"SECTION_ID"		=> $new_r["IBLOCK_SECTION_ID"],
								"DETAIL_HTML"		=> ($new_r["DETAIL_TEXT_TYPE"]=="html" ?$new_r["DETAIL_TEXT"]:TxtToHtml($new_r["DETAIL_TEXT"])),
								"DETAIL_TEXT"		=> ($new_r["DETAIL_TEXT_TYPE"]=="text"? $new_r["DETAIL_TEXT"]:HtmlToTxt($new_r["DETAIL_TEXT"])),
								"DETAIL"		=> $new_r["DETAIL_TEXT"],
								"DETAIL_TYPE"		=> $new_r["DETAIL_TEXT_TYPE"],
								"COMMENTS"		=> $new_r["WF_COMMENTS"]
							);
							CEvent::Send("WF_NEW_IBLOCK_ELEMENT",$iblock_r["LID"], $arEventFields);
						}
					}
				}
			}
		}
	}

	///////////////////////////////////////////////////////////////////
	// Clears the last or old records in history using parameters from workflow module
	///////////////////////////////////////////////////////////////////
	function WF_CleanUpHistoryCopies($ELEMENT_ID=false, $HISTORY_COPIES=false)
	{
		if(CModule::IncludeModule("workflow"))
		{
			$err_mess = "FILE: ".__FILE__."<br>LINE: ";
			global $DB;
			if($HISTORY_COPIES===false)
				$HISTORY_COPIES = intval(COption::GetOptionString("workflow","HISTORY_COPIES","10"));

			$ELEMENT_ID = intval($ELEMENT_ID);
			if($ELEMENT_ID>0)
				$strSqlSearch = " AND ID = $ELEMENT_ID ";
			$strSql = "SELECT ID FROM b_iblock_element ".
					"WHERE (ID=WF_PARENT_ELEMENT_ID or (WF_PARENT_ELEMENT_ID IS NULL AND WF_STATUS_ID=1)) ".
					$strSqlSearch;
			$z = $DB->Query($strSql, false, $err_mess.__LINE__);
			while ($zr=$z->Fetch())
			{
				$DID = $zr["ID"];
				$strSql =
					"SELECT ID, WF_NEW, WF_PARENT_ELEMENT_ID ".
					"FROM b_iblock_element ".
					"WHERE WF_PARENT_ELEMENT_ID = ".$DID." ".
					"	AND WF_PARENT_ELEMENT_ID<>ID ".
					"	AND (WF_NEW<>'Y' or WF_NEW is null) ".
					"ORDER BY ID desc";
				$t = $DB->Query($strSql, false, $err_mess.__LINE__);
				while ($tr = $t->Fetch())
				{
					$i++;
					if($i>$HISTORY_COPIES)
					{
						$LAST_ID = CIBlockElement::WF_GetLast($DID);
						if($LAST_ID!=$tr["ID"])
						{
							CIBlockElement::Delete($tr["ID"]);
						}
					}
				}
			}
		}
	}

	function WF_GetSqlLimit($PS="BE.", $SHOW_NEW="N")
	{
		if(CModule::IncludeModule("workflow"))
		{
			$limit = " and ((".$PS."WF_STATUS_ID=1 and ".$PS."WF_PARENT_ELEMENT_ID is null)";
			if($SHOW_NEW=="Y") $limit .= " or ".$PS."WF_NEW='Y' ";
			$limit .= " ) ";
		}
		else
		{
			$limit = " AND ".$PS."WF_STATUS_ID=1 and ".$PS."WF_PARENT_ELEMENT_ID is null ";
		}
		return $limit;
	}

	///////////////////////////////////////////////////////////////////
	// Returns last ID of element in the history
	///////////////////////////////////////////////////////////////////
	function WF_GetLast($ID)
	{
		global $DB;
		$ID = intval($ID);
		$strSql = "SELECT ID, WF_PARENT_ELEMENT_ID, WF_NEW FROM b_iblock_element WHERE ID='$ID'";
		$z = $DB->Query($strSql);
		$zr = $z->Fetch();
		$WF_PARENT_ELEMENT_ID = intval($zr["WF_PARENT_ELEMENT_ID"]);
		if($WF_PARENT_ELEMENT_ID>0)
		{
			$strSql = "SELECT ID FROM b_iblock_element WHERE WF_PARENT_ELEMENT_ID='".$WF_PARENT_ELEMENT_ID."' ORDER BY ID desc";
			$s = $DB->Query($strSql);
			$sr = $s->Fetch();
			if($sr["ID"]>0) $ID = $sr["ID"];
		}
		else
		{
			$strSql = "SELECT ID, WF_STATUS_ID FROM b_iblock_element WHERE WF_PARENT_ELEMENT_ID='$ID' ORDER BY ID desc";
			$s = $DB->Query($strSql);
			$sr = $s->Fetch();
			if($sr['WF_STATUS_ID']>1 && $sr["ID"]>0) $ID = $sr["ID"];
		}
		return $ID;
	}

	function GetRealElement($ID)
	{
		global $DB;
		$ID = intval($ID);

		$strSql = "SELECT WF_PARENT_ELEMENT_ID FROM b_iblock_element WHERE ID='$ID'";
		$z = $DB->Query($strSql);
		$zr = $z->Fetch();
		$PARENT_ID = intval($zr["WF_PARENT_ELEMENT_ID"]);

		return ($PARENT_ID>0)?$PARENT_ID:$ID;
	}

	function WF_GetStatusTitle($STATUS_ID)
	{
		global $DB;
		if(CModule::IncludeModule("workflow"))
		{
			$STATUS_ID = intval($STATUS_ID);
			if($STATUS_ID>0)
			{
				$strSql = "SELECT * FROM b_workflow_status WHERE ID='$STATUS_ID'";
				$z = $DB->Query($strSql);
				$zr = $z->Fetch();
			}
		}
		return $zr["TITLE"];
	}


	function WF_GetCurrentStatus($ELEMENT_ID, &$STATUS_TITLE)
	{
		global $DB;
		if(CModule::IncludeModule("workflow"))
		{
			$ELEMENT_ID = intval($ELEMENT_ID);
			$WF_ID = intval(CIBlockElement::WF_GetLast($ELEMENT_ID));
			if($WF_ID<=0) $WF_ID = $ELEMENT_ID;
			if($WF_ID>0)
			{
				$strSql =
					"SELECT E.WF_STATUS_ID, S.TITLE ".
					"FROM b_iblock_element E, b_workflow_status S ".
					"WHERE E.ID = ".$WF_ID." ".
					"	AND	S.ID = E.WF_STATUS_ID";
				$z = $DB->Query($strSql);
				$zr = $z->Fetch();
				$STATUS_ID = $zr["WF_STATUS_ID"];
				$STATUS_TITLE = $zr["TITLE"];
			}
		}
		return intval($STATUS_ID);
	}

	///////////////////////////////////////////////////////////////////
	// Returns permission status
	///////////////////////////////////////////////////////////////////
	function WF_GetStatusPermission($STATUS_ID, $ID = false)
	{
		global $DB, $USER;
		$result = false;
		if(CModule::IncludeModule("workflow"))
		{
			if(CWorkflow::IsAdmin())
				return 2;
			else
			{
				$ID = intval($ID);
				if($ID)
				{
					$arStatus = array();
					$arSql = Array("ID='".$ID."'", "WF_PARENT_ELEMENT_ID='".$ID."'");
					foreach($arSql as $where)
					{
						$strSql = "SELECT ID, WF_STATUS_ID FROM b_iblock_element WHERE ".$where;
						$rs = $DB->Query($strSql);
						while($ar = $rs->Fetch())
							$arStatus[$ar["WF_STATUS_ID"]] = $ar["WF_STATUS_ID"];
					}
				}
				else
				{
					$arStatus = array(intval($STATUS_ID)=>intval($STATUS_ID));
				}
				$arGroups = $USER->GetUserGroupArray();
				if(!is_array($arGroups)) $arGroups[] = 2;
				$groups = implode(",",$arGroups);
				foreach($arStatus as $STATUS_ID)
				{
					$strSql =
							"SELECT max(G.PERMISSION_TYPE) as MAX_PERMISSION ".
							"FROM b_workflow_status2group G ".
							"WHERE G.STATUS_ID = ".$STATUS_ID." ".
							"	AND G.GROUP_ID in (".$groups.") ";
					$rs = $DB->Query($strSql);
					$ar = $rs->Fetch();
					$ar["MAX_PERMISSION"] = intval($ar["MAX_PERMISSION"]);
					if($result===false || ($result > $ar["MAX_PERMISSION"]))
						$result = $ar["MAX_PERMISSION"];
				}
			}
		}
		return $result;
	}

	function WF_IsLocked($ID, &$locked_by, &$date_lock)
	{
		$err_mess = "FILE: ".__FILE__."<br> LINE:";
		global $DB, $USER;
		$ID = intval($ID);
		$LOCK_STATUS = CIblockElement::WF_GetLockStatus($ID, $locked_by, $date_lock);
		if($LOCK_STATUS=="red") return true;
		return false;
	}

	function MkFilter($arFilter, &$arJoinProps, &$arFullJoins, &$arIBlockFilter, &$bJoinFlatProp)
	{
		global $DB, $USER;

		$iPropCnt = count($arJoinProps);
		$uid = intval($USER->GetID());
		$arSqlSearch = Array();
		$arIBlockFilter = Array();
		$strSqlSearch = "";

		foreach($arFilter as $key=>$val)
		{
			$key = strtoupper($key);
			$p = strpos($key, "PROPERTY_");
			if($p!==false && ($p<3))
			{
				$arFilter[substr($key, 0, $p)."PROPERTY"][substr($key, $p+9)] = $val;
				unset($arFilter[$key]);
			}
		}

		foreach($arFilter as $key=>$val)
		{
			$res = CIBlock::MkOperationFilter($key);
			$key = $res["FIELD"];
			$cOperationType = $res["OPERATION"];

			//it was done before $key = strtoupper($key);
			switch($key)
			{
			case "ACTIVE":
			case "DETAIL_TEXT_TYPE":
			case "PREVIEW_TEXT_TYPE":
				$arSqlSearch[] = CIBlock::FilterCreateEx("BE.".$key, $val, "string_equal", $bFullJoinTmp, $cOperationType);
				break;
			case "NAME":
			case "XML_ID":
			case "TMP_ID":
			case "DETAIL_TEXT":
			case "SEARCHABLE_CONTENT":
			case "PREVIEW_TEXT":
			case "CODE":
			case "TAGS":
			case "WF_COMMENTS":
				$arSqlSearch[] = CIBlock::FilterCreateEx("BE.".$key, $val, "string", $bFullJoinTmp, $cOperationType);
				break;
			case "ID":
			case "SHOW_COUNTER":
			case "WF_PARENT_ELEMENT_ID":
			case "WF_STATUS_ID":
			case "SORT":
			case "CREATED_BY":
			case "PREVIEW_PICTURE":
			case "DETAIL_PICTURE":
					$arSqlSearch[] = CIBlock::FilterCreateEx("BE.".$key, $val, "number", $bFullJoinTmp, $cOperationType);
				break;
			case "TIMESTAMP_X":
			case "DATE_CREATE":
			case "SHOW_COUNTER_START":
				$arSqlSearch[] = CIBlock::FilterCreateEx("BE.".$key, $val, "date", $bFullJoinTmp, $cOperationType);
				break;
			case "IBLOCK_ID":
				$arSqlSearch[] = CIBlock::FilterCreateEx("BE.".$key, $val, "number", $bFullJoinTmp, $cOperationType);
				$arIBlockFilter[] = CIBlock::FilterCreateEx("B.ID", $val, "number", $bFullJoinTmp, $cOperationType);
				break;
			case "EXTERNAL_ID":
				$arSqlSearch[] = CIBlock::FilterCreateEx("BE.XML_ID", $val, "string", $bFullJoinTmp, $cOperationType);
				break;
			case "IBLOCK_TYPE":
				$arIBlockFilter[] = CIBlock::FilterCreateEx("B.IBLOCK_TYPE_ID", $val, "string", $bFullJoinTmp, $cOperationType);
				break;
			case "LID":
			case "SITE_ID":
			case "IBLOCK_LID":
			case "IBLOCK_SITE_ID":
				$arIBlockFilter[] = CIBlock::FilterCreateEx("BS.SITE_ID", $val, "string_equal", $bFullJoinTmp, $cOperationType);
				break;
			case "DATE_ACTIVE_FROM":
				$arSqlSearch[] = CIBlock::FilterCreateEx("BE.ACTIVE_FROM", $val, "date", $bFullJoinTmp, $cOperationType);
				break;
			case "DATE_ACTIVE_TO":
				$arSqlSearch[] = CIBlock::FilterCreateEx("BE.ACTIVE_TO", $val, "date", $bFullJoinTmp, $cOperationType);
				break;
			case "IBLOCK_ACTIVE":
				$arIBlockFilter[] = CIBlock::FilterCreateEx("B.ACTIVE", $val, "string_equal", $bFullJoinTmp, $cOperationType);
				break;
			case "IBLOCK_CODE":
				$arIBlockFilter[] = CIBlock::FilterCreateEx("B.CODE", $val, "string", $bFullJoinTmp, $cOperationType);
				break;
			case "ID_ABOVE":
				$arSqlSearch[] = CIBlock::FilterCreateEx("BE.ID", $val, "number_above", $bFullJoinTmp, $cOperationType);
				break;
			case "ID_LESS":
				$arSqlSearch[] = CIBlock::FilterCreateEx("BE.ID", $val, "number_less", $bFullJoinTmp, $cOperationType);
				break;
			case "ACTIVE_FROM":
				if(strlen($val)>0)
					$arSqlSearch[] = "(BE.ACTIVE_FROM ".($cOperationType=="N"?"<":">=").$DB->CharToDateFunction($DB->ForSql($val), "FULL").($cOperationType=="N"?"":" OR BE.ACTIVE_FROM IS NULL").")";
				break;
			case "ACTIVE_TO":
				if(strlen($val)>0)
					$arSqlSearch[] = "(BE.ACTIVE_TO ".($cOperationType=="N"?">":"<=").$DB->CharToDateFunction($DB->ForSql($val), "FULL").($cOperationType=="N"?"":" OR BE.ACTIVE_TO IS NULL").")";
				break;
			case "ACTIVE_DATE":
				if(strlen($val)>0)
					$arSqlSearch[] = ($cOperationType=="N"?" NOT":"")."((BE.ACTIVE_TO >= ".$DB->GetNowFunction()." OR BE.ACTIVE_TO IS NULL) AND (BE.ACTIVE_FROM <= ".$DB->GetNowFunction()." OR BE.ACTIVE_FROM IS NULL))";
				break;

			case "DATE_MODIFY_FROM":
				if(strlen($val)>0)
					$arSqlSearch[] = "(BE.TIMESTAMP_X ".
						( $cOperationType=="N" ? "<" : ">=" ).$DB->CharToDateFunction($DB->ForSql($val), "FULL").
						( $cOperationType=="N" ? ""  : " OR BE.TIMESTAMP_X IS NULL").")";
				break;
			case "DATE_MODIFY_TO":
				if(strlen($val)>0)
					$arSqlSearch[] = "(BE.TIMESTAMP_X ".
						( $cOperationType=="N" ? ">" : "<=" ).$DB->CharToDateFunction($DB->ForSql($val), "FULL").
						( $cOperationType=="N" ? ""  : " OR BE.TIMESTAMP_X IS NULL").")";
				break;
			case "WF_NEW":
				if($val=="Y" || $val=="N")
					$arSqlSearch[] = CIBlock::FilterCreateEx("BE.WF_NEW", "Y", "string_equal", $bFullJoinTmp, ($val=="Y"?false:true), false);
				break;
			case "MODIFIED_USER_ID": case "MODIFIED_BY":
				$arSqlSearch[] = CIBlock::FilterCreateEx("BE.MODIFIED_BY", $val, "number", $bFullJoinTmp, $cOperationType);
				break;
			case "CREATED_USER_ID": case "CREATED_BY":
				$arSqlSearch[] = CIBlock::FilterCreateEx("BE.CREATED_BY", $val, "number", $bFullJoinTmp, $cOperationType);
				break;
			case "WF_STATUS":
				$arSqlSearch[] = CIBlock::FilterCreateEx("BE.WF_STATUS_ID", $val, "number", $bFullJoinTmp, $cOperationType);
				break;
			case "WF_LOCK_STATUS":
				if(strlen($val)>0)
					$arSqlSearch[] = " if(BE.WF_DATE_LOCK is null, 'green', if(DATE_ADD(BE.WF_DATE_LOCK, interval ".COption::GetOptionInt("workflow", "MAX_LOCK_TIME", 60)." MINUTE)<now(), 'green', if(BE.WF_LOCKED_BY=".intval($USER->GetID()).", 'yellow', 'red'))) = '".$DB->ForSql($val)."'";
				break;
			case "SUBSECTION":
				if(!is_array($val)) $val=Array($val);
				//Find out margins of sections
				$arUnknownMargins = array();
				foreach($val as $i=>$section)
				{
					if(!is_array($section))
						$arUnknownMargins[intval($section)] = intval($section);
				}
				if(count($arUnknownMargins) > 0)
				{
					$rs = $DB->Query("SELECT ID, LEFT_MARGIN, RIGHT_MARGIN FROM b_iblock_section WHERE ID in (".implode(", ", $arUnknownMargins).")");
					while($ar = $rs->Fetch())
					{
						$arUnknownMargins[intval($ar["ID"])] = array(
							intval($ar["LEFT_MARGIN"]),
							intval($ar["RIGHT_MARGIN"]),
						);
					}
					foreach($val as $i=>$section)
					{
						if(!is_array($section))
							$val[$i] = $arUnknownMargins[intval($section)];
					}
				}
				//Now sort them out
				$arMargins = array();
				foreach($val as $i=>$section)
				{
					if(is_array($section) && (count($section) == 2))
					{
						$left = intval($section[0]);
						$right = intval($section[1]);
						if($left > 0 && $right > 0)
							$arMargins[$left] = $right;
					}
				}
				ksort($arMargins);
				//Remove subsubsections of the sections
				$prev_right = 0;
				foreach($arMargins as $left => $right)
				{
					if($right <= $prev_right)
						unset($arMargins[$left]);
					else
						$prev_right = $right;
				}
				$res = "";
				foreach($arMargins as $left => $right)
				{
					if($res!="")
						$res .= ($cOperationType=="N"?" AND ":" OR ");
					$res .= ($cOperationType == "N"? " NOT ": " ")."(BS.LEFT_MARGIN >= ".$left." AND BS.RIGHT_MARGIN <= ".$right.")\n";;
				}
				if($res!="")
				{
					if(!is_array($arSqlSearch["SECTION"]))
						$arSqlSearch["SECTION"] = Array();
					$arSqlSearch["SECTION"][] = "(".$res.")";
				}
				break;
			case "SECTION_ID":
				if(!is_array($val)) $val=Array($val);
				if(count($val)==1)
				{
					if(IntVal($val[0])<=0)
						$arSqlSearch[] = "BE.IN_SECTIONS ".($cOperationType=="N"?"<>":"=")."'N' ";
					else
					{
						if(!is_array($arSqlSearch["SECTION"]))
							$arSqlSearch["SECTION"] = Array();

						$arSqlSearch["SECTION"][] = "(BS.ID ".($cOperationType=="N"?"<>":"=")." ".IntVal($val[0]).($cOperationType=="N"?" OR BE.IN_SECTIONS='N'":"").")";
						if($cOperationType != "N")
							$arSqlSearch["NO_SECTION_DISTINCT"] = true;
					}
				}
				else
				{
					$res="";
					$bNull = false;
					$bInSect = false;
					for($j=0; $j<count($val); $j++)
					{
						if($res!="") $res .= ($cOperationType=="N"?" AND ":" OR ");
						if(IntVal($val[$j])<=0)
						{
							$bNull = true;
							$res .= "BE.IN_SECTIONS ".($cOperationType=="N"?"<>":"=")."'N' ";
						}
						else
						{
							$bInSect = true;
							$res .= "BS.ID ".($cOperationType=="N"?"<>":"=")." ".IntVal($val[$j]);
						}
					}

					if($res!="")
					{
						if($bInSect)
						{
							if(!is_array($arSqlSearch["SECTION"]))
								$arSqlSearch["SECTION"] = Array();
							$arSqlSearch["SECTION"][] = "(".$res." ".(($cOperationType=="N") && !$bNull?" OR BE.IN_SECTIONS='N'":"").")";
						}
						else
							$arSqlSearch[] = "(".$res." ".(($cOperationType=="N") && !$bNull?" OR BE.IN_SECTIONS='N'":"").")";
					}
				}
				break;
			case "SECTION_CODE":
				if(!is_array($val)) $val=Array($val);
				if(count($val)==1)
				{
					if(strlen($val[0])<=0)
						$arSqlSearch[] = "BE.IN_SECTIONS ".($cOperationType=="N"?"<>":"=")."'N' ";
					else
					{
						if(!is_array($arSqlSearch["SECTION"]))
							$arSqlSearch["SECTION"] = Array();

						$arSqlSearch["SECTION"][] = "(BS.CODE ".($cOperationType=="N"?"<>":"=")." '".$DB->ForSql($val[0])."'".($cOperationType=="N"?" OR BE.IN_SECTIONS='N'":"").")";
					}
				}
				else
				{
					$res="";
					$bNull = false;
					$bInSect = false;
					for($j=0; $j<count($val); $j++)
					{
						if($res!="") $res .= ($cOperationType=="N"?" AND ":" OR ");
						if(strlen($val[$j])<=0)
						{
							$bNull = true;
							$res .= "BE.IN_SECTIONS ".($cOperationType=="N"?"<>":"=")."'N' ";
						}
						else
						{
							$bInSect = true;
							$res .= "BS.CODE ".($cOperationType=="N"?"<>":"=")." '".$DB->ForSql($val[$j])."'";
						}
					}

					if($res!="")
					{
						if($bInSect)
						{
							if(!is_array($arSqlSearch["SECTION"]))
								$arSqlSearch["SECTION"] = Array();
							$arSqlSearch["SECTION"][] = "(".$res." ".(($cOperationType=="N") && !$bNull?" OR BE.IN_SECTIONS='N'":"").")";
						}
						else
							$arSqlSearch[] = "(".$res." ".(($cOperationType=="N") && !$bNull?" OR BE.IN_SECTIONS='N'":"").")";
					}
				}
				break;
			case "PROPERTY":
				$cPropertyOperationType = $cOperationType;
				foreach($val as $propID=>$propVAL)
				{
					$res = CIBlock::MkOperationFilter($propID);
					$propID = $res["FIELD"];
					if(substr(strtoupper($propID), -6) == '_VALUE')
						$bValueEnum = true;
					else
						$bValueEnum = false;

					if($res["OPERATION"]!="E")
						$cOperationType = $res["OPERATION"];
					else
						$cOperationType = $cPropertyOperationType;

					if($db_prop = CIBlockProperty::GetPropertyArray($propID, CIBlock::_MergeIBArrays($arFilter["IBLOCK_ID"], $arFilter["IBLOCK_CODE"])))
					{
/*						if(
							($db_prop["VERSION"]!=2 && !$db_prop["IS_CODE_UNIQUE"] && $db_prop["IS_VERSION_MIXED"])
							||($db_prop["VERSION"]==2 && !$db_prop["IS_CODE_UNIQUE"])
						)
							return false;*/
						$bSave = false;
						if($arJoinProps[$db_prop["ID"]]>0)
							$iPropCnt = $arJoinProps[$db_prop["ID"]];
						elseif($db_prop["VERSION"]!=2 || $db_prop["MULTIPLE"]=="Y" || $db_prop["PROPERTY_TYPE"]=="L")
						{
							$bSave = true;
							$iPropCnt++;
						}

						if(!is_array($propVAL))
							$propVAL = Array($propVAL);

						if($db_prop["PROPERTY_TYPE"]=="L")
						{
							if($bValueEnum)
								$r = CIBlock::FilterCreateEx("FPEN".$iPropCnt.".VALUE", $propVAL, "string", $bFullJoin, $cOperationType);
							else
								$r = CIBlock::FilterCreateEx("FPEN".$iPropCnt.".ID", $propVAL, "number", $bFullJoin, $cOperationType);
						}
						elseif($db_prop["PROPERTY_TYPE"]=="N" || $db_prop["PROPERTY_TYPE"]=="G" || $db_prop["PROPERTY_TYPE"]=="E")
						{
							if($db_prop["VERSION"]==2 && $db_prop["MULTIPLE"]=="N")
							{
								$r = CIBlock::FilterCreateEx("FPS.PROPERTY_".$db_prop["ORIG_ID"], $propVAL, "number", $bFullJoin, $cOperationType);
								$bJoinFlatProp = $db_prop["IBLOCK_ID"];
							}
							else
								$r = CIBlock::FilterCreateEx("FPV".$iPropCnt.".VALUE_NUM", $propVAL, "number", $bFullJoin, $cOperationType);
						}
						else
						{
							if($db_prop["VERSION"]==2 && $db_prop["MULTIPLE"]=="N")
							{
								$r = CIBlock::FilterCreateEx("FPS.PROPERTY_".$db_prop["ORIG_ID"], $propVAL, "string", $bFullJoin, $cOperationType);
								$bJoinFlatProp = $db_prop["IBLOCK_ID"];
							}
							else
								$r = CIBlock::FilterCreateEx("FPV".$iPropCnt.".VALUE", $propVAL, "string", $bFullJoin, $cOperationType);
						}

						if(strlen($r)>0)
						{
							if($bSave)
								$arJoinProps[$db_prop["ID"]] = $iPropCnt;
							if($bFullJoin)
								$arFullJoins[] = $db_prop["ID"];
							$arSqlSearch[] = $r;
						}
						else
						{
							if($bSave)
								$iPropCnt--;
						}
						$iPropCnt =  count($arJoinProps);
					}
				}
				break;
			}
		}
		return $arSqlSearch;
	}

	function PrepareGetList(
		&$arIblockElementFields,
		&$arJoinProps,
		&$arFullJoins,
		&$bOnlyCount,
		&$bDistinct,

		&$arSelectFields,
		&$sSelect,
		&$arAddSelectFields,

		&$arFilter,
		&$sWhere,
		&$sSectionWhere,
		&$arAddWhereFields,

		&$arGroupBy,
		&$sGroupBy,

		&$arOrder,
		&$arSqlOrder,
		&$arAddOrderByFields,

		&$arIBlockFilter,
		&$arIBlockMultProps,
		&$bJoinFlatProp,
		&$arIBlockConvProps,
		&$arIBlockAllProps,
		&$arIBlockNumProps,
		&$arIBlockLongProps
		)
	{
		if(is_array($arSelectFields) && in_array("DETAIL_PAGE_URL", $arSelectFields) && !in_array("LANG_DIR", $arSelectFields))
			$arSelectFields[] = "LANG_DIR";

		global $DB, $DBType;

		if($DBType == "oracle" || $DBType == "mssql")
			$max_alias_len = 30;
		else
			$max_alias_len = false;

		if((!is_array($arSelectFields) && $arSelectFields=="") || count($arSelectFields)<=0 || $arSelectFields===false)
			$arSelectFields = Array("*");

		if(is_bool($arGroupBy) && $arGroupBy!==false)
			$arGroupBy = Array();

		if(is_array($arGroupBy) && count($arGroupBy)==0)
			$bOnlyCount = true;

		$iPropCnt = 0;
		$arJoinProps = Array();
		$arFullJoins = Array();
		$arIBlockMultProps = Array();
		$arIBlockAllProps = Array();
		$arIBlockNumProps = Array();
		$bJoinFlatProp = false;
		$bWasGroup = false;

		//*************************GROUP BY PART****************************
		$sGroupBy = "";
		if(is_array($arGroupBy) && count($arGroupBy)>0)
		{
			$arSelectFields = $arGroupBy;
			$bWasGroup = true;
			foreach($arSelectFields as $key=>$val)
			{
				$val = strtoupper($val);
				if(array_key_exists($val, $arIblockElementFields))
				{
					$sGroupBy.=",".preg_replace("/(\s+AS\s+[A-Z_]+)/i", "", $arIblockElementFields[$val]);
				}
				elseif(substr($val, 0, 9) == "PROPERTY_")
				{
					$PR_ID = substr($val, 9);
					if($db_prop = CIBlockProperty::GetPropertyArray($PR_ID, CIBlock::_MergeIBArrays($arFilter["IBLOCK_ID"], $arFilter["IBLOCK_CODE"])))
					{
						$PR_Prefix = "PROPERTY_".$PR_ID."_";
						if($arJoinProps[$db_prop["ID"]]>0)
							$iPropCnt = $arJoinProps[$db_prop["ID"]];
						elseif($db_prop["VERSION"]!=2 || $db_prop["MULTIPLE"]=="Y" || $db_prop["PROPERTY_TYPE"]=="L")
						{
							$iPropCnt++;
							$arJoinProps[$db_prop["ID"]] = $iPropCnt;
						}

						if($db_prop["PROPERTY_TYPE"]=="L")
							$sGroupBy .= ", FPEN".$iPropCnt.".VALUE, FPEN".$iPropCnt.".ID";
						elseif($db_prop["PROPERTY_TYPE"]=="N")
						{
							if($db_prop["VERSION"]==2 && $db_prop["MULTIPLE"]=="N")
							{
								$sGroupBy .= ", FPS.PROPERTY_".$db_prop["ORIG_ID"];
								$bJoinFlatProp = $db_prop["IBLOCK_ID"];
							}
							else
								$sGroupBy .= ", FPV".$iPropCnt.".VALUE, FPV".$iPropCnt.".VALUE_NUM";
						}
						else
						{
							if($db_prop["VERSION"]==2 && $db_prop["MULTIPLE"]=="N")
							{
								$sGroupBy .= ", FPS.PROPERTY_".$db_prop["ORIG_ID"];
								$bJoinFlatProp = $db_prop["IBLOCK_ID"];
							}
							else
							$sGroupBy .= ", FPV".$iPropCnt.".VALUE";
						}
					}
				}
			}
			if($sGroupBy!="")
				$sGroupBy = " GROUP BY ".substr($sGroupBy, 1)." ";
		}

		//*************************SELECT PART****************************
		$arAddSelectFields = Array();
		if($bOnlyCount)
		{
			$sSelect = "COUNT(%%_DISTINCT_%% BE.ID) as CNT ";
		}
		else
		{
			//Add order by fields to the select list
			//in order to avoid sql errors
			if(is_array($arOrder))
			{
				foreach($arOrder as $by=>$order)
				{
					$by = strtolower($by);
					if($by == "status") $arSelectFields[] = "WF_STATUS_ID";
					elseif($by == "created")  $arSelectFields[] = "DATE_CREATE";
					else $arSelectFields[] = $by;
				}
			}

			$sSelect = "";
			$arDisplayedColumns = Array();
			$bStar = false;
			foreach($arSelectFields as $key=>$val)
			{
				$val = strtoupper($val);
				if(array_key_exists($val, $arIblockElementFields))
				{
					if(array_key_exists($val, $arDisplayedColumns))
						continue;
					$arDisplayedColumns[$val] = true;
					$arSelectFields[$key] = strtoupper($arSelectFields[$key]);
					$sSelect.=",".$arIblockElementFields[$val]." as ".$val;
				}
				elseif($val == "PROPERTY_*" && !$bWasGroup)
				{
					//We have to analyze arFilter IBLOCK_ID and IBLOCK_CODE
					//in a way to be shure we will get properties of the ONE IBLOCK ONLY!
					$arPropertyFilter = array(
						"ACTIVE"=>"Y",
						"VERSION"=>2,
					);
					if(array_key_exists("IBLOCK_ID", $arFilter))
					{
						if(is_array($arFilter["IBLOCK_ID"]) && count($arFilter["IBLOCK_ID"])==1)
							$arPropertyFilter["IBLOCK_ID"] = $arFilter["IBLOCK_ID"][0];
						elseif(!is_array($arFilter["IBLOCK_ID"]) && intval($arFilter["IBLOCK_ID"])>0)
							$arPropertyFilter["IBLOCK_ID"] = $arFilter["IBLOCK_ID"];
					}
					if(!array_key_exists("IBLOCK_ID", $arPropertyFilter))
					{
						if(array_key_exists("IBLOCK_CODE", $arFilter))
						{
							if(is_array($arFilter["IBLOCK_CODE"]) && count($arFilter["IBLOCK_CODE"])==1)
								$arPropertyFilter["IBLOCK_CODE"] = $arFilter["IBLOCK_CODE"][0];
							elseif(!is_array($arFilter["IBLOCK_CODE"]) && strlen($arFilter["IBLOCK_CODE"])>0)
								$arPropertyFilter["IBLOCK_CODE"] = $arFilter["IBLOCK_CODE"];
							else
								continue;
						}
						else
							continue;
					}

					$rs_prop = CIBlockProperty::GetList(array("sort"=>"asc"), $arPropertyFilter);
					while($db_prop = $rs_prop->Fetch())
						$arIBlockAllProps[]=$db_prop;
					$iblock_id = false;
					foreach($arIBlockAllProps as $db_prop)
					{
						if($db_prop["USER_TYPE"]!="")
						{
							$arUserType = CIBlockProperty::GetUserType($db_prop["USER_TYPE"]);
							if(array_key_exists("ConvertFromDB", $arUserType))
								$arIBlockConvProps["PROPERTY_".$db_prop["ID"]] = array(
									"ConvertFromDB"=>$arUserType["ConvertFromDB"],
									"PROPERTY"=>$db_prop,
								);
						}
						$db_prop["ORIG_ID"] = $db_prop["ID"];
						if($db_prop["MULTIPLE"]=="Y")
							$arIBlockMultProps[$db_prop["IBLOCK_ID"]]["*".$db_prop["ID"]] = $db_prop;
						$iblock_id = $db_prop["IBLOCK_ID"];
					}
					if($iblock_id!==false)
					{
						$sSelect .= ", FPS.*";
						$bJoinFlatProp = $iblock_id;
					}
				}
				elseif(substr($val, 0, 9) == "PROPERTY_")
				{
					$PR_ID = strtoupper(substr($val, 9));
					if($db_prop = CIBlockProperty::GetPropertyArray($PR_ID, CIBlock::_MergeIBArrays($arFilter["IBLOCK_ID"], $arFilter["IBLOCK_CODE"])))
					{
/*						if(
							($db_prop["VERSION"]!=2 && !$db_prop["IS_CODE_UNIQUE"] && $db_prop["IS_VERSION_MIXED"])
							||($db_prop["VERSION"]==2 && !$db_prop["IS_CODE_UNIQUE"])
						)
						{
							if(is_object($this))
								$this->LAST_ERROR = "Incompatible properties listed in the select list";
							return false;
						}*/
						$PR_Prefix = "PROPERTY_".$PR_ID."_";

						if($max_alias_len && strlen($PR_Prefix."DESCRIPTION") > $max_alias_len)
						{
							$alias_index = count($arIBlockLongProps);
							$arIBlockLongProps[$alias_index] = $PR_Prefix;
							$PR_Prefix = "ALIAS_".$alias_index."_";
						}

						if($db_prop["USER_TYPE"]!="")
						{
							$arUserType = CIBlockProperty::GetUserType($db_prop["USER_TYPE"]);
							if(array_key_exists("ConvertFromDB", $arUserType))
								$arIBlockConvProps[$PR_Prefix."VALUE"] = array(
									"ConvertFromDB"=>$arUserType["ConvertFromDB"],
									"PROPERTY"=>$db_prop,
								);
						}

						if($arJoinProps[$db_prop["ID"]]>0)
							$iPropCnt = $arJoinProps[$db_prop["ID"]];
						elseif($db_prop["VERSION"]!=2 || ($db_prop["MULTIPLE"]=="N" && $db_prop["PROPERTY_TYPE"]=="L")|| ($db_prop["MULTIPLE"]=="Y" && $bWasGroup))
						{
							$iPropCnt++;
							$arJoinProps[$db_prop["ID"]] = $iPropCnt;
						}
						if($db_prop["VERSION"]==2)
						{
							if($db_prop["MULTIPLE"]=="Y" && !$bWasGroup)
								$arIBlockMultProps[$db_prop["IBLOCK_ID"]][$db_prop["ID"]] = $db_prop;
							if($db_prop["MULTIPLE"]=="N" && $db_prop["PROPERTY_TYPE"]=="N")
								$arIBlockNumProps[$db_prop["ID"]] = $db_prop;
						}
						if($db_prop["PROPERTY_TYPE"]=="L")
						{
							if($db_prop["VERSION"]==2 && $db_prop["MULTIPLE"]=="Y" && !$bWasGroup)
							{
								$sSelect .= ", FPS.PROPERTY_".$db_prop["ORIG_ID"]." as ".$PR_Prefix."VALUE";
								$bJoinFlatProp = $db_prop["IBLOCK_ID"];
							}
							else
							{
								$sSelect .= ", FPEN".$iPropCnt.".VALUE as ".$PR_Prefix."VALUE, FPEN".$iPropCnt.".ID as ".$PR_Prefix."ENUM_ID";
							}
						}
						else
						{
							if($db_prop["VERSION"]==2 && ($db_prop["MULTIPLE"]=="N" || !$bWasGroup))
							{
								$sSelect .= ", FPS.PROPERTY_".$db_prop["ORIG_ID"]." as ".$PR_Prefix."VALUE";
								if($sGroupBy=="")
									$sSelect .= ", FPS.DESCRIPTION_".$db_prop["ORIG_ID"]." as ".$PR_Prefix."DESCRIPTION";
								$bJoinFlatProp = $db_prop["IBLOCK_ID"];
							}
							else
								$sSelect .= ", FPV".$iPropCnt.".VALUE as ".$PR_Prefix."VALUE";
						}

						if($sGroupBy=="")
						{
							if($db_prop["VERSION"]==2)
							{
								if($DB->type=="MSSQL")
									$sSelect .= ", ".$DB->Concat("CAST(BE.ID AS VARCHAR)","':'","'".$db_prop["ORIG_ID"]."'")." as ".$PR_Prefix."VALUE_ID";
								else
									$sSelect .= ", ".$DB->Concat("BE.ID","':'",$db_prop["ORIG_ID"])." as ".$PR_Prefix."VALUE_ID";
							}
							else
								$sSelect .= ", FPV".$iPropCnt.".ID as ".$PR_Prefix."VALUE_ID";
						}
					}
				}
				elseif($val == "*")
				{
					$bStar = true;
				}
				elseif(substr($val, 0, 14) == "CATALOG_GROUP_")
				{
					$arAddSelectFields[] = $val;
				}
				elseif(substr($val, 0, 16) == "CATALOG_QUANTITY")
				{
					$arAddSelectFields[] = $val;
				}
			}
			if($bStar)
			{
				foreach($arIblockElementFields as $key=>$val)
				{
					if(array_key_exists($key, $arDisplayedColumns))
						continue;
					$arSelectFields[]=$key;
					$sSelect.=",".$val." as ".$key;
				}
			}
			elseif($sGroupBy=="") //Try to add missing fields for correct URL translation (only then no grouping)
			{
				if(array_key_exists("DETAIL_PAGE_URL", $arDisplayedColumns) || array_key_exists("SECTION_PAGE_URL", $arDisplayedColumns))
				{
					$arUrlFileds = array("LANG_DIR", "ID", "CODE", "EXTERNAL_ID", "IBLOCK_SECTION_ID", "IBLOCK_TYPE_ID", "IBLOCK_ID", "IBLOCK_CODE", "IBLOCK_EXTERNAL_ID");
				}
				elseif(array_key_exists("LIST_PAGE_URL", $arDisplayedColumns))
				{
					$arUrlFileds = array("LANG_DIR");
				}
				else
				{
					$arUrlFileds = array();
				}
				foreach($arUrlFileds as $key)
				{
					if(array_key_exists($key, $arDisplayedColumns))
						continue;
					$arSelectFields[]=$key;
					$sSelect.=",".$arIblockElementFields[$key]." as ".$key;
				}
			}
			if($sGroupBy!="")
				$sSelect = substr($sSelect, 1).", COUNT(%%_DISTINCT_%% BE.ID) as CNT ";
			else
				$sSelect = "%%_DISTINCT_%% ".substr($sSelect, 1)." ";
		}

		//********************************ORDER BY PART***********************************************
		$arSqlOrder = Array();
		$arAddOrderByFields = Array();
		$iOrdNum = -1;
		if(!is_array($arOrder))
			$arOrder = Array();
		foreach($arOrder as $by=>$order)
		{
			$by_orig = $by;
			$by = strtolower($by);

			$iOrdNum++;
			if(substr($by, 0, 8) == "catalog_")
				$arAddOrderByFields[$iOrdNum] = Array($by=>$order);
			else
			{
				$order = strtolower($order);
				if($order!="asc")
					$order = "desc".($DB->type=="ORACLE"?" NULLS LAST":"");
				else
					$order = "asc".($DB->type=="ORACLE"?" NULLS FIRST":"");

				if($by == "id") $arSqlOrder[$iOrdNum] = " BE.ID ".$order." ";
				elseif($by == "name") $arSqlOrder[$iOrdNum] = " BE.NAME ".$order." ";
				elseif($by == "status") $arSqlOrder[$iOrdNum] = " BE.WF_STATUS_ID ".$order." ";
				elseif($by == "xml_id") $arSqlOrder[$iOrdNum] = " BE.XML_ID ".$order." ";
				elseif($by == "external_id") $arSqlOrder[$iOrdNum] = " BE.XML_ID ".$order." ";
				elseif($by == "code") $arSqlOrder[$iOrdNum] = " BE.CODE ".$order." ";
				elseif($by == "tags") $arSqlOrder[$iOrdNum] = " BE.TAGS ".$order." ";
				elseif($by == "timestamp_x") $arSqlOrder[$iOrdNum] = " BE.TIMESTAMP_X ".$order." ";
				elseif($by == "created") $arSqlOrder[$iOrdNum] = " BE.DATE_CREATE ".$order." ";
				elseif($by == "created_date") $arSqlOrder[$iOrdNum] = " ".$DB->DateFormatToDB("YYYY.MM.DD", "BE.DATE_CREATE")." ".$order." ";
				elseif($by == "iblock_id") $arSqlOrder[$iOrdNum] = " BE.IBLOCK_ID ".$order." ";
				elseif($by == "modified_by") $arSqlOrder[$iOrdNum] = " BE.MODIFIED_BY ".$order." ";
				elseif($by == "active") $arSqlOrder[$iOrdNum] = " BE.ACTIVE ".$order." ";
				elseif($by == "active_from") $arSqlOrder[$iOrdNum] = " BE.ACTIVE_FROM ".$order." ";
				elseif($by == "date_active_from") $arSqlOrder[$iOrdNum] = " BE.ACTIVE_FROM ".$order." ";
				elseif($by == "date_active_to") $arSqlOrder[$iOrdNum] = " BE.ACTIVE_TO ".$order." ";
				elseif($by == "active_to") $arSqlOrder[$iOrdNum] = " BE.ACTIVE_TO ".$order." ";
				elseif($by == "sort") $arSqlOrder[$iOrdNum] = " BE.SORT ".$order." ";
				elseif($by == "show_counter") $arSqlOrder[$iOrdNum] = " BE.SHOW_COUNTER ".$order." ";
				elseif($by == "show_counter_start") $arSqlOrder[$iOrdNum] = " BE.SHOW_COUNTER_START ".$order." ";
				elseif($by == "rand") $arSqlOrder[$iOrdNum] = CIBlockElement::GetRandFunction();
				elseif($by == "shows") $arSqlOrder[$iOrdNum] = CIBlockElement::GetShowedFunction().$order." ";
				elseif($by == "cnt")
				{
					if(strlen($sGroupBy) > 0)
						$arSqlOrder[$iOrdNum] = " CNT ".$order." ";
				}
				elseif(substr($by, 0, 9) == "property_")
				{
					$propID=substr($by_orig, 9);
					if($db_prop = CIBlockProperty::GetPropertyArray($propID, CIBlock::_MergeIBArrays($arFilter["IBLOCK_ID"], $arFilter["IBLOCK_CODE"])))
					{
/*						if(
							($db_prop["VERSION"]!=2 && !$db_prop["IS_CODE_UNIQUE"] && $db_prop["IS_VERSION_MIXED"])
							||($db_prop["VERSION"]==2 && !$db_prop["IS_CODE_UNIQUE"])
						)
						{
							if(is_object($this))
								$this->LAST_ERROR = "Incompatible properties listed in the order by list";
							return false;
						}*/
						if($arJoinProps[$db_prop["ID"]]>0)
							$iPropCnt = $arJoinProps[$db_prop["ID"]];
						elseif($db_prop["VERSION"]!=2 || $db_prop["MULTIPLE"]=="Y" || $db_prop["PROPERTY_TYPE"]=="L")
						{
							$iPropCnt=count($arJoinProps)+1;
							$arJoinProps[$db_prop["ID"]] = $iPropCnt;
						}

						if($db_prop["PROPERTY_TYPE"]=="L")
							$arSqlOrder[$iOrdNum] = " FPEN".$iPropCnt.".VALUE ".$order." ";
						elseif($db_prop["PROPERTY_TYPE"]=="N")
						{
							if($db_prop["VERSION"]==2 && $db_prop["MULTIPLE"]=="N")
							{
								$arSqlOrder[$iOrdNum] = " FPS.PROPERTY_".$db_prop["ORIG_ID"]." ".$order." ";
								$bJoinFlatProp = $db_prop["IBLOCK_ID"];
							}
							else
								$arSqlOrder[$iOrdNum] = " FPV".$iPropCnt.".VALUE_NUM ".$order." ";
						}
						else
						{
							if($db_prop["VERSION"]==2 && $db_prop["MULTIPLE"]=="N")
							{
								$arSqlOrder[$iOrdNum] = " FPS.PROPERTY_".$db_prop["ORIG_ID"]." ".$order." ";
								$bJoinFlatProp = $db_prop["IBLOCK_ID"];
							}
							else
								$arSqlOrder[$iOrdNum] = " FPV".$iPropCnt.".VALUE ".$order." ";
						}
					}
				}
				else
				{
					$arSqlOrder[$iOrdNum] = " BE.ID ".$order." ";
					$by = "id";
				}
			}
		}


		//*********************WHERE PART*********************
		$arAddWhereFields = Array();
		if(is_set($arFilter, "CATALOG"))
		{
			$arAddWhereFields = $arFilter["CATALOG"];
			unset($arFilter["CATALOG"]);
		}

		foreach ($arFilter as $filter_key=>$filter_val)
		{
			$filter_key = strtolower($filter_key);
			if(substr(Trim($filter_key, "=<>!"), 0, 8) == "catalog_")
				$arAddWhereFields[$filter_key] = $filter_val;
		}

		$arSqlSearch = CIBlockElement::MkFilter($arFilter, $arJoinProps, $arFullJoins, $arIBlockFilter, $bJoinFlatProp);
		$bDistinct = false;
		$sSectionWhere = "";
		if(is_array($arSqlSearch["SECTION"]))
		{
			if((count($arSqlSearch["SECTION"]) > 1) || ($arSqlSearch["NO_SECTION_DISTINCT"] !== true))
				$bDistinct = true;
			$arSectWhere = $arSqlSearch["SECTION"];
			for($i=0; $i<count($arSectWhere); $i++)
				if(strlen($arSectWhere[$i])>0)
					$sSectionWhere .= " AND (".$arSectWhere[$i].") ";
			unset($arSqlSearch["SECTION"]);
		}

		$sWhere = "";
		for($i=0; $i<count($arSqlSearch); $i++)
			if(strlen($arSqlSearch[$i])>0)
				$sWhere .= " AND (".$arSqlSearch[$i].") ";
	}


	///////////////////////////////////////////////////////////////////
	// Removes element
	///////////////////////////////////////////////////////////////////
	function Delete($ID)
	{
		global $DB, $APPLICATION;
		$ID = IntVal($ID);

		$APPLICATION->ResetException();
		$db_events = GetModuleEvents("iblock", "OnBeforeIBlockElementDelete");
		while($arEvent = $db_events->Fetch())
			if(ExecuteModuleEvent($arEvent, $ID)===false)
			{
				$err = GetMessage("MAIN_BEFORE_DEL_ERR").' '.$arEvent['TO_NAME'];
				if($ex = $APPLICATION->GetException())
					$err .= ': '.$ex->GetString();
				$APPLICATION->throwException($err);
				return false;
			}

		$arSql = Array("ID='".$ID."'", "WF_PARENT_ELEMENT_ID='".$ID."'");
		for($i=0; $i<count($arSql); $i++)
		{
			$strSql =
				"SELECT ID, IBLOCK_ID, WF_PARENT_ELEMENT_ID, WF_STATUS_ID, PREVIEW_PICTURE, DETAIL_PICTURE, XML_ID as EXTERNAL_ID ".
				"FROM b_iblock_element ".
				"WHERE ".$arSql[$i]." ".
				"ORDER BY ID DESC";

			$z = $DB->Query($strSql);
			while ($zr = $z->Fetch())
			{
				$VERSION = CIBlockElement::GetIBVersion($zr["IBLOCK_ID"]);
				$db_res = CIBlockElement::GetProperty($zr["IBLOCK_ID"], $zr["ID"], "sort", "asc", array("PROPERTY_TYPE"=>"F"));

				while($res = $db_res->Fetch())
					CFile::Delete($res["VALUE"]);

				if($VERSION==2)
				{
					if(!$DB->Query("DELETE FROM b_iblock_element_prop_m".$zr["IBLOCK_ID"]." WHERE IBLOCK_ELEMENT_ID = ".IntVal($zr["ID"]), false, $err_mess.__LINE__))
					return false;
					if(!$DB->Query("DELETE FROM b_iblock_element_prop_s".$zr["IBLOCK_ID"]." WHERE IBLOCK_ELEMENT_ID = ".IntVal($zr["ID"]), false, $err_mess.__LINE__))
					return false;
				}
				elseif(!$DB->Query("DELETE FROM b_iblock_element_property WHERE IBLOCK_ELEMENT_ID = ".IntVal($zr["ID"]), false, $err_mess.__LINE__))
					return false;

				static $arDelCache;
				if(!is_array($arDelCache))
					$arDelCache = Array();
				if(!is_set($arDelCache, $zr["IBLOCK_ID"]))
				{
					$arDelCache[$zr["IBLOCK_ID"]] = false;
					$db_ps = $DB->Query("SELECT ID,IBLOCK_ID,VERSION,MULTIPLE FROM b_iblock_property WHERE PROPERTY_TYPE='E' AND (LINK_IBLOCK_ID=".$zr["IBLOCK_ID"]." OR LINK_IBLOCK_ID=0 OR LINK_IBLOCK_ID IS NULL)", false, $err_mess.__LINE__);
					while($ar_ps = $db_ps->Fetch())
					{
						if($ar_ps["VERSION"]==2)
						{
							if($ar_ps["MULTIPLE"]=="Y")
								$strTable = "b_iblock_element_prop_m".$ar_ps["IBLOCK_ID"];
							else
								$strTable = "b_iblock_element_prop_s".$ar_ps["IBLOCK_ID"];
						}
						else
						{
							$strTable = "b_iblock_element_property";
						}
						$arDelCache[$zr["IBLOCK_ID"]][$strTable][] = $ar_ps["ID"];
					}
				}

				if($arDelCache[$zr["IBLOCK_ID"]])
				{
					foreach($arDelCache[$zr["IBLOCK_ID"]] as $strTable=>$arProps)
					{
						if(strncmp("b_iblock_element_prop_s", $strTable, 23)==0)
						{
							foreach($arProps as $prop_id)
							{
								$strSql = "UPDATE ".$strTable." SET PROPERTY_".$prop_id."=null,DESCRIPTION_".$prop_id."=null WHERE PROPERTY_".$prop_id."=".$zr["ID"];
								if(!$DB->Query($strSql, false, $err_mess.__LINE__))
									return false;
							}
						}
						elseif(strncmp("b_iblock_element_prop_m", $strTable, 23)==0)
						{
							$strSql = "SELECT IBLOCK_PROPERTY_ID, IBLOCK_ELEMENT_ID FROM ".$strTable." WHERE IBLOCK_PROPERTY_ID IN (".implode(", ", $arProps).") AND VALUE_NUM=".$zr["ID"];
							$rs = $DB->Query($strSql, false, $err_mess.__LINE__);
							while($ar = $rs->Fetch())
							{
								$strSql = "
									UPDATE ".str_replace("prop_m", "prop_s", $strTable)."
									SET	PROPERTY_".$ar["IBLOCK_PROPERTY_ID"]."=null,
										DESCRIPTION_".$ar["IBLOCK_PROPERTY_ID"]."=null
									WHERE IBLOCK_ELEMENT_ID = ".$ar["IBLOCK_ELEMENT_ID"]."
								";
								if(!$DB->Query($strSql, false, $err_mess.__LINE__))
									return false;
							}
							$strSql = "DELETE FROM ".$strTable." WHERE IBLOCK_PROPERTY_ID IN (".implode(", ", $arProps).") AND VALUE_NUM=".$zr["ID"];
							if(!$DB->Query($strSql, false, $err_mess.__LINE__))
								return false;
						}
						else
						{
							$strSql = "DELETE FROM ".$strTable." WHERE IBLOCK_PROPERTY_ID IN (".implode(", ", $arProps).") AND VALUE_NUM=".$zr["ID"];
							if(!$DB->Query($strSql, false, $err_mess.__LINE__))
								return false;
						}
					}
				}

				if(!$DB->Query("DELETE FROM b_iblock_section_element WHERE IBLOCK_ELEMENT_ID = ".IntVal($zr["ID"]), false, $err_mess.__LINE__))
					return false;

				$events = GetModuleEvents("iblock", "OnIBlockElementDelete");
				while($arEvent = $events->Fetch())
					ExecuteModuleEvent($arEvent, IntVal($zr["ID"]));

				if(IntVal($zr["WF_PARENT_ELEMENT_ID"])<=0 && $zr["WF_STATUS_ID"]==1 && CModule::IncludeModule("search"))
					CSearch::DeleteIndex("iblock", IntVal($zr["ID"]));

				CFile::Delete($zr["PREVIEW_PICTURE"]);
				CFile::Delete($zr["DETAIL_PICTURE"]);

				if(CModule::IncludeModule("workflow"))
					$DB->Query("DELETE FROM b_workflow_move WHERE IBLOCK_ELEMENT_ID=".IntVal($zr["ID"]), false, $err_mess.__LINE__);

				if(!$DB->Query("DELETE FROM b_iblock_element WHERE ID=".IntVal($zr["ID"]), false, $err_mess.__LINE__))
					return false;

				$db_events = GetModuleEvents("iblock", "OnAfterIBlockElementDelete");
				while($arEvent = $db_events->Fetch())
					ExecuteModuleEvent($arEvent, $zr);
			}
		}
		/************* QUOTA *************/
		$_SESSION["SESS_RECOUNT_DB"] = "Y";
		/************* QUOTA *************/
		return true;
	}

	function GetByID($ID)
	{
		return CIBlockElement::GetList(Array(), $arFilter=Array("ID"=>IntVal($ID), "SHOW_HISTORY"=>"Y"));
	}


	///////////////////////////////////////////////////////////////////
	// Checks fields before update or insert
	///////////////////////////////////////////////////////////////////
	function CheckFields(&$arFields, $ID=false)
	{
		global $DB, $APPLICATION, $USER;
		$this->LAST_ERROR = "";

		if(($ID===false || is_set($arFields, "NAME")) && strlen($arFields["NAME"])<=0)
			$this->LAST_ERROR .= GetMessage("IBLOCK_BAD_ELEMENT_NAME")."<br>";

		if(strlen($arFields["ACTIVE_FROM"])>0 && (!$DB->IsDate($arFields["ACTIVE_FROM"], false, LANG, "FULL")))
			$this->LAST_ERROR .= GetMessage("IBLOCK_BAD_ACTIVE_FROM")."<br>";

		if(strlen($arFields["ACTIVE_TO"])>0 && (!$DB->IsDate($arFields["ACTIVE_TO"], false, LANG, "FULL")))
			$this->LAST_ERROR .= GetMessage("IBLOCK_BAD_ACTIVE_TO")."<br>";

		if(is_set($arFields, "PREVIEW_PICTURE"))
		{
			$error = CFile::CheckImageFile($arFields["PREVIEW_PICTURE"]);
			if (strlen($error)>0) $this->LAST_ERROR .= $error."<br>";
		}

		if(is_set($arFields, "DETAIL_PICTURE"))
		{
			$error = CFile::CheckImageFile($arFields["DETAIL_PICTURE"]);
			if (strlen($error)>0) $this->LAST_ERROR .= $error."<br>";
		}

		if($ID===false && !is_set($arFields, "IBLOCK_ID"))
			$this->LAST_ERROR .= GetMessage("IBLOCK_BAD_BLOCK_ID")."<br>";

		if($ID!==false && is_set($arFields, "XML_ID") && strlen($arFields["XML_ID"])<=0)
			$this->LAST_ERROR .= GetMessage("IBLOCK_BAD_EXTERNAL_CODE")."<br>";

		$IBLOCK_ID = 0;
		static $IBLOCK_CACHE = array();
		if(is_set($arFields, "IBLOCK_ID"))
		{
			if(!array_key_exists($arFields["IBLOCK_ID"], $IBLOCK_CACHE))
			{
				$IBLOCK_CACHE[$arFields["IBLOCK_ID"]] = CIBlock::GetArrayByID($arFields["IBLOCK_ID"]);
			}
			if($IBLOCK_CACHE[$arFields["IBLOCK_ID"]])
				$IBLOCK_ID = $arFields["IBLOCK_ID"];
			else
				$this->LAST_ERROR .= GetMessage("IBLOCK_BAD_BLOCK_ID")."<br>";
		}

		if($IBLOCK_ID <= 0)
		{
			$res = $DB->Query("SELECT IBLOCK_ID FROM b_iblock_element WHERE ID=".IntVal($ID));
			if($ar = $res->Fetch())
				$IBLOCK_ID = $ar["IBLOCK_ID"];
		}

		if($IBLOCK_ID > 0 && !array_key_exists($IBLOCK_ID, $IBLOCK_CACHE))
		{
			$IBLOCK_CACHE[$IBLOCK_ID] = CIBlock::GetArrayByID($IBLOCK_ID);
		}

		if($IBLOCK_CACHE[$IBLOCK_ID])
		{
			$ar = $IBLOCK_CACHE[$IBLOCK_ID]["FIELDS"];
			if(is_array($ar))
			{
				$arOldElement = false;
				foreach($ar as $FIELD_ID => $field)
				{
					if($field["IS_REQUIRED"] === "Y")
					{
						switch($FIELD_ID)
						{
						case "NAME":
						case "ACTIVE":
						case "PREVIEW_TEXT_TYPE":
						case "DETAIL_TEXT_TYPE":
						case "SORT":
							//We should never check for this fields
							break;
						case "IBLOCK_SECTION":
							if($ID===false || array_key_exists($FIELD_ID, $arFields))
							{
								$sum = 0;
								if(is_array($arFields[$FIELD_ID]))
								{
									foreach($arFields[$FIELD_ID] as $k => $v)
										if(intval($v) > 0)
											$sum += intval($v);
								}
								else
								{
									$sum = intval($arFields[$FIELD_ID]);
								}
								if($sum <= 0)
									$this->LAST_ERROR .= GetMessage("IBLOCK_BAD_FIELD", array("#FIELD_NAME#" => $field["NAME"]))."<br>";
							}
							break;
						case "PREVIEW_PICTURE":
						case "DETAIL_PICTURE":
							if($ID !== false && !$arOldElement)
							{
								$rs = $DB->Query("SELECT PREVIEW_PICTURE, DETAIL_PICTURE from b_iblock_element WHERE ID = ".intval($ID));
								$arOldElement = $rs->Fetch();
							}
							if($arOldElement && $arOldElement[$FIELD_ID] > 0)
							{//There was an picture so just check that it is not deleted
								if(
									array_key_exists($FIELD_ID, $arFields)
									&& $arFields[$FIELD_ID]["del"] === "Y"
									&& !(
										array_key_exists("error", $arFields[$FIELD_ID])
										&& $arFields[$FIELD_ID]["error"] === 0
										&& $arFields[$FIELD_ID]["size"] > 0
									)
								)
									$this->LAST_ERROR .= GetMessage("IBLOCK_BAD_FIELD", array("#FIELD_NAME#" => $field["NAME"]))."<br>";
							}
							else
							{//There was NO picture so it MUST be present
								if(
									!array_key_exists($FIELD_ID, $arFields)
									|| !is_array($arFields[$FIELD_ID])
									|| $arFields[$FIELD_ID]["del"] === "Y"
									|| (array_key_exists("error", $arFields[$FIELD_ID]) && $arFields[$FIELD_ID]["error"] !== 0)
									|| $arFields[$FIELD_ID]["size"] <= 0
								)
									$this->LAST_ERROR .= GetMessage("IBLOCK_BAD_FIELD", array("#FIELD_NAME#" => $field["NAME"]))."<br>";
							}
							break;
						default:
							if($ID===false || array_key_exists($FIELD_ID, $arFields))
							{
								if(is_array($arFields[$FIELD_ID]))
									$val = implode("", $arFields[$FIELD_ID]);
								else
									$val = $arFields[$FIELD_ID];
								if(strlen($val) <= 0)
									$this->LAST_ERROR .= GetMessage("IBLOCK_BAD_FIELD", array("#FIELD_NAME#" => $field["NAME"]))."<br>";
							}
							break;
						}
					}
				}
			}
		}

		if(is_set($arFields, "PROPERTY_VALUES") && is_array($arFields["PROPERTY_VALUES"]))
		{
			$arProperties = array();
			foreach($arFields["PROPERTY_VALUES"] as $key=>$value)
			{
				$arProperty = CIBlockProperty::GetPropertyArray($key, $IBLOCK_ID);
				if($arProperty["USER_TYPE"]!="")
				{
					$arUserType = CIBlockProperty::GetUserType($arProperty["USER_TYPE"]);
					if(array_key_exists("CheckFields", $arUserType))
					{
						foreach($value as $key2=>$value2)
						{
							if(!is_array($value2))
								$value2=array("VALUE"=>$value2);
							if(is_array($arError = call_user_func_array($arUserType["CheckFields"],array($arProperty,$value2))))
								foreach($arError as $err_mess)
									$this->LAST_ERROR .= $err_mess;
						}
					}
				}
				if($arProperty["IS_REQUIRED"]==="Y")
				{
					$propertyValue = $value;
					//Files check
					if ($arProperty['PROPERTY_TYPE'] == 'F')
					{
						//New element
						if($ID===false)
						{
							$bError = true;
							if(is_array($propertyValue))
							{
								if(array_key_exists("tmp_name", $propertyValue) && array_key_exists("size", $propertyValue))
								{
									if($propertyValue['size'] > 0)
									{
										$bError = false;
									}
								}
								else
								{
									foreach ($propertyValue as $arFile)
									{
										if ($arFile['size'] > 0)
										{
											$bError = false;
											break;
										}
									}
								}
							}
						}
						else
						{
							$dbProperty = CIBlockElement::GetProperty(
								$arProperty["IBLOCK_ID"],
								$ID,
								"sort", "asc",
								array("ID" => $arProperty["ID"])
							);

							$bCount = 0;
							while ($dbProperty->Fetch())
								$bCount++;

							foreach ($propertyValue as $arFile)
							{
								if ($arFile['size'] > 0)
								{
									$bCount++;
									break;
								}
								elseif ($arFile['del'] == 'Y')
								{
									$bCount--;
								}
							}

							$bError = $bCount <= 0;
						}
					}
					else
					{
						if(!is_array($propertyValue))
						{
							$bError = strlen($propertyValue) <= 0;
						}
						elseif(array_key_exists("VALUE", $propertyValue))
						{
							$bError = strlen($propertyValue["VALUE"]) <= 0;
						}
						else
						{
							$len = 0;
							foreach($propertyValue as $propVal)
							{
								if(!is_array($propVal))
								{
									$len += strlen($propVal);
								}
								elseif(array_key_exists("VALUE", $propVal))
								{
									if($arProperty["USER_TYPE"] != "")
									{
										$arUserType = CIBlockProperty::GetUserType($arProperty["USER_TYPE"]);
										if(array_key_exists("GetLength", $arUserType))
										{
											$len += call_user_func_array($arUserType["GetLength"], array($arProperty, $propVal));
										}
									}
									else
									{
										$len += strlen($propVal["VALUE"]);
									}
								}
								if($len > 0)
									break;
							}
							$bError = $len <= 0;
						}
					}

					if ($bError)
					{
						$this->LAST_ERROR .= GetMessage("IBLOCK_BAD_PROPERTY", array("#PROPERTY#" => $arProperty["NAME"]))."<br>";
					}
				}
			}
		}

		if(strlen($this->LAST_ERROR)<=0)
		{
			// check file properties for correctness
			if(is_set($arFields, "PROPERTY_VALUES"))
			{
				$db_prop = CIBlock::GetProperties($IBLOCK_ID, Array(), Array("PROPERTY_TYPE"=>"F"));
				while($props = $db_prop->Fetch())
				{
					$bImageOnly = False;
					$arImageExtentions = explode(",", strtoupper(CFile::GetImageExtensions()));
					if (strlen($props["FILE_TYPE"]) > 0)
					{
						$bImageOnly = True;
						$arAvailTypes = explode(",", strtoupper($props["FILE_TYPE"]));
						for ($i1 = 0; $i1 < count($arAvailTypes); $i1++)
						{
							if (!in_array(trim($arAvailTypes[$i1]), $arImageExtentions))
							{
								$bImageOnly = False;
								break;
							}
						}
					}

					$values = $arFields["PROPERTY_VALUES"][$props["ID"]];
					if(!is_array($values) || (is_array($values) && is_set($values, "tmp_name")))
						$values = Array($values);
					foreach($values as $key=>$value)
					{
						if($bImageOnly)
							$error = CFile::CheckImageFile($value);
						else
							$error = CFile::CheckFile($value, 0, false, $props["FILE_TYPE"]);

						//For user without edit php permissions
						//we allow only pictures upload
						if(!is_object($USER) || !$USER->IsAdmin())
						{
							if(HasScriptExtension($value["name"]))
							{
								$error = GetMessage("FILE_BAD_TYPE")." (".$value["name"].").";
							}
						}

						if (strlen($error) > 0)
							$this->LAST_ERROR .= $error."<br>";
					}

					if(strlen($props["CODE"])>0)
					{
						$values = $arFields["PROPERTY_VALUES"][$props["CODE"]];
						if(!is_array($values) || (is_array($values) && is_set($values, "tmp_name")))
							$values = Array($values);
						if(is_array($values))
						{
							foreach($values as $key=>$value)
							{
								if ($bImageOnly)
									$error = CFile::CheckImageFile($value);
								else
									$error = CFile::CheckFile($value, 0, false, $props["FILE_TYPE"]);

								//For user without edit php permissions
								//we allow only pictures upload
								if(!is_object($USER) || !$USER->IsAdmin())
								{
									if(HasScriptExtension($value["name"]))
									{
										$error = GetMessage("FILE_BAD_TYPE")." (".$value["name"].").";
									}
								}

								if (strlen($error) > 0)
									$this->LAST_ERROR .= $error."<br>";
							}
						}
					}
				}
			}
		}

		$APPLICATION->ResetException();
		if($ID===false)
			$db_events = GetModuleEvents("iblock", "OnBeforeIBlockElementAdd");
		else
		{
			$arFields["ID"] = $ID;
			$db_events = GetModuleEvents("iblock", "OnBeforeIBlockElementUpdate");
		}

		while($arEvent = $db_events->Fetch())
		{
			$bEventRes = ExecuteModuleEvent($arEvent, &$arFields);
			if($bEventRes===false)
			{
				if($err = $APPLICATION->GetException())
					$this->LAST_ERROR .= $err->GetString()."<br>";
				else
				{
					$APPLICATION->ThrowException("Unknown error");
					$this->LAST_ERROR .= "Unknown error.<br>";
				}
				break;
			}
		}

		/****************************** QUOTA ******************************/
		if(empty($this->LAST_ERROR) && (COption::GetOptionInt("main", "disk_space") > 0))
		{
			$quota = new CDiskQuota();
			if(!$quota->checkDiskQuota($arFields))
				$this->LAST_ERROR = $quota->LAST_ERROR;
		}
		/****************************** QUOTA ******************************/

		if(strlen($this->LAST_ERROR)>0)
			return false;

		return true;
	}


	//////////////////////////////////////////////////////////////////////////
	//
	//////////////////////////////////////////////////////////////////////////
	function SetPropertyValueCode($ELEMENT_ID, $PROPERTY_CODE, $PROPERTY_VALUE)
	{
		global $DB;

		$strSql =
			"SELECT BE.IBLOCK_ID ".
			"FROM b_iblock_element BE ".
			"WHERE BE.ID = ".IntVal($ELEMENT_ID);

		$dbr = $DB->Query($strSql);
		if($dbr_arr = $dbr->Fetch())
		{
			$IBLOCK_ID = $dbr_arr["IBLOCK_ID"];
			CIBlockElement::SetPropertyValues($ELEMENT_ID, $IBLOCK_ID, $PROPERTY_VALUE, $PROPERTY_CODE);

			return true;
		}
		return false;
	}


	//////////////////////////////////////////////////////////////////////////
	//
	//////////////////////////////////////////////////////////////////////////
	function GetElementGroups($ID, $bElementOnly = false)
	{
		global $DB;
		$dbr = $DB->Query(
			"SELECT S.* ".
			"FROM b_iblock_section_element SE, b_iblock_section S ".
			"WHERE SE.IBLOCK_SECTION_ID=S.ID ".
			"	AND SE.IBLOCK_ELEMENT_ID=".IntVal($ID)." ".
			($bElementOnly?"	AND SE.ADDITIONAL_PROPERTY_ID IS NULL ":"")
			);
		return $dbr;
	}

	//////////////////////////////////////////////////////////////////////////
	//
	//////////////////////////////////////////////////////////////////////////
	function RecalcSections($ID)
	{
		global $DB;
		$ID = IntVal($ID);
		$res = $DB->Query(
			"SELECT COUNT('x') as C, MIN(SE.IBLOCK_SECTION_ID) as IBLOCK_SECTION_ID_NEW, E.IBLOCK_SECTION_ID, E.IN_SECTIONS ".
			"FROM b_iblock_section_element SE, b_iblock_element E ".
			"WHERE SE.IBLOCK_ELEMENT_ID = ".$ID." ".
			"	AND E.ID=".$ID." ".
			"	AND ADDITIONAL_PROPERTY_ID IS NULL ".
			"GROUP BY E.IN_SECTIONS, E.IBLOCK_SECTION_ID"
			);
		$res = $res->Fetch();
		$cnt = (IntVal($res["C"])>0?"Y":"N");
		if($cnt!=$res["IN_SECTIONS"] || $res["IBLOCK_SECTION_ID_NEW"]!=$res["IBLOCK_SECTION_ID"])
		{
			$DB->Query(
				"UPDATE b_iblock_element SET ".
				"	IN_SECTIONS='".$cnt."', ".
				"	IBLOCK_SECTION_ID=".(IntVal($res["IBLOCK_SECTION_ID_NEW"])>0?IntVal($res["IBLOCK_SECTION_ID_NEW"]):"NULL")." ".
				"WHERE ID=".$ID
				);
		}
	}

	//////////////////////////////////////////////////////////////////////////
	//
	//////////////////////////////////////////////////////////////////////////
	function SetElementSection($ID, $arSections, $bNew = false)
	{
		global $DB;
		$ID = IntVal($ID);

		if(!$bNew)
			$DB->Query("DELETE FROM b_iblock_section_element WHERE IBLOCK_ELEMENT_ID=".$ID." AND ADDITIONAL_PROPERTY_ID IS NULL");

		if(!is_array($arSections))
		{
			if(IntVal($arSections)<=0)
				$arSections = Array();
			else
				$arSections = Array($arSections);
		}
		$ids="0";
		foreach($arSections as $key=>$val)
			if(IntVal($val)>0)
				$ids .= ",".IntVal($val);

		if($ids=="0")
		{
			$DB->Query(
				"UPDATE b_iblock_element SET ".
				"	IN_SECTIONS='N', ".
				"	IBLOCK_SECTION_ID=NULL ".
				"WHERE ID=".$ID
				);
			return;
		}

		$DB->Query(
			"INSERT INTO b_iblock_section_element(IBLOCK_SECTION_ID, IBLOCK_ELEMENT_ID) ".
			"SELECT S.ID, E.ID ".
			"FROM b_iblock_section S, b_iblock_element E ".
			"WHERE S.IBLOCK_ID=E.IBLOCK_ID ".
			"	AND S.ID IN (".$ids.") ".
			"	AND E.ID = ".$ID
			);

		CIBlockElement::RecalcSections($ID);
	}

	function __InitFile($old_id, &$arFields, $fname)
	{
		if($old_id>0
			&&
			(
				!is_set($arFields, $fname)
				||
				(
					strlen($arFields[$fname]['name'])<=0
					&&
					$arFields[$fname]['del']!="Y"
				)
			)
			&&
			($p = CFile::MakeFileArray($old_id))
		)
		{
			if(is_set($arFields[$fname], 'description'))
				$p['description'] = $arFields[$fname]['description'];
			$p["OLD_VALUE"] = true;
			$arFields[$fname] = $p;
		}
	}


	function UpdateSearch($ID, $bOverWrite=false)
	{
		if(!CModule::IncludeModule("search"))
			return;

		global $DB;
		$ID = Intval($ID);

		static $strElementSql = false;
		if(!$strElementSql)
			$strElementSql = "
				SELECT BE.ID, BE.NAME, BE.XML_ID as EXTERNAL_ID,
					BE.PREVIEW_TEXT_TYPE, BE.PREVIEW_TEXT, BE.CODE,
					BE.TAGS,
					BE.DETAIL_TEXT_TYPE, BE.DETAIL_TEXT, BE.IBLOCK_ID, B.IBLOCK_TYPE_ID,
					".$DB->DateToCharFunction("BE.TIMESTAMP_X")." as LAST_MODIFIED,
					".$DB->DateToCharFunction("BE.ACTIVE_FROM")." as DATE_FROM,
					".$DB->DateToCharFunction("BE.ACTIVE_TO")." as DATE_TO,
					BE.IBLOCK_SECTION_ID,
					B.CODE as IBLOCK_CODE, B.XML_ID as IBLOCK_EXTERNAL_ID, B.DETAIL_PAGE_URL,
					B.VERSION
				FROM b_iblock_element BE, b_iblock B
				WHERE BE.IBLOCK_ID=B.ID
					AND B.ACTIVE='Y'
					AND BE.ACTIVE='Y'
					AND B.INDEX_ELEMENT='Y'
					".CIBlockElement::WF_GetSqlLimit("BE.", "N")."
					AND BE.ID=";

		$dbrIBlockElement = $DB->Query($strElementSql.$ID);

		if($arIBlockElement = $dbrIBlockElement->Fetch())
		{
			$IBLOCK_ID = $arIBlockElement["IBLOCK_ID"];
			$DETAIL_URL =
					"=ID=".$arIBlockElement["ID"].
					"&EXTERNAL_ID=".$arIBlockElement["EXTERNAL_ID"].
					"&IBLOCK_SECTION_ID=".$arIBlockElement["IBLOCK_SECTION_ID"].
					"&IBLOCK_TYPE_ID=".$arIBlockElement["IBLOCK_TYPE_ID"].
					"&IBLOCK_ID=".$arIBlockElement["IBLOCK_ID"].
					"&IBLOCK_CODE=".$arIBlockElement["IBLOCK_CODE"].
					"&IBLOCK_EXTERNAL_ID=".$arIBlockElement["IBLOCK_EXTERNAL_ID"].
					"&CODE=".$arIBlockElement["CODE"];

			static $arGroups = array();
			if(!array_key_exists($IBLOCK_ID, $arGroups))
			{
				$arGroups[$IBLOCK_ID] = array();
				$strSql =
					"SELECT GROUP_ID ".
					"FROM b_iblock_group ".
					"WHERE IBLOCK_ID= ".$IBLOCK_ID." ".
					"	AND PERMISSION>='R' ".
					"ORDER BY GROUP_ID";

				$dbrIBlockGroup = $DB->Query($strSql);
				while($arIBlockGroup = $dbrIBlockGroup->Fetch())
				{
					$arGroups[$IBLOCK_ID][] = $arIBlockGroup["GROUP_ID"];
					if($arIBlockGroup["GROUP_ID"]==2) break;
				}
			}

			static $arSITE = array();
			if(!array_key_exists($IBLOCK_ID, $arSITE))
			{
				$arSITE[$IBLOCK_ID] = array();
				$strSql =
					"SELECT SITE_ID ".
					"FROM b_iblock_site ".
					"WHERE IBLOCK_ID= ".$IBLOCK_ID;

				$dbrIBlockSite = $DB->Query($strSql);
				while($arIBlockSite = $dbrIBlockSite->Fetch())
					$arSITE[$IBLOCK_ID][] = $arIBlockSite["SITE_ID"];
			}

			$BODY =
				($arIBlockElement["PREVIEW_TEXT_TYPE"]=="html" ?
					CSearch::KillTags($arIBlockElement["PREVIEW_TEXT"]) :
					$arIBlockElement["PREVIEW_TEXT"]
				)."\r\n".
				($arIBlockElement["DETAIL_TEXT_TYPE"]=="html" ?
					CSearch::KillTags($arIBlockElement["DETAIL_TEXT"]) :
					$arIBlockElement["DETAIL_TEXT"]
				);

			static $arProperties = array();
			if(!array_key_exists($IBLOCK_ID, $arProperties))
			{
				$arProperties[$IBLOCK_ID] = array();
				$rsProperties = CIBlockProperty::GetList(
					array("sort"=>"asc","id"=>"asc"),
					array("ACTIVE"=>"Y", "SEARCHABLE"=>"Y")
				);
				while($ar = $rsProperties->Fetch())
					$arProperties[$IBLOCK_ID][$ar["ID"]] = $ar;
			}

			//Read current property values from database
			$strProperties = "";
			if(count($arProperties[$IBLOCK_ID])>0)
			{
				if($arIBlockElement["VERSION"]==1)
				{
					$rs = $DB->Query("
						select *
						from b_iblock_element_property
						where IBLOCK_ELEMENT_ID=".$arIBlockElement["ID"]."
						AND IBLOCK_PROPERTY_ID in (".implode(", ", array_keys($arProperties[$IBLOCK_ID])).")
					");
					while($ar=$rs->Fetch())
					{
						$strProperties .= "\r\n";
						$arProperty = $arProperties[$IBLOCK_ID][$ar["IBLOCK_PROPERTY_ID"]];
						if(strlen($arProperty["USER_TYPE"])>0)
						{
							$UserType = CIBlockProperty::GetUserType($arProperty["USER_TYPE"]);
							if(array_key_exists("GetPublicViewHTML", $UserType))
							{
								$strProperties .= CSearch::KillTags(
									call_user_func_array($UserType["GetPublicViewHTML"],
										array(
											$arProperty,
											array("VALUE" => $ar["VALUE"]),
											array(),
										)
									)
								);
							}
						}
						elseif($arProperty["PROPERTY_TYPE"]=='L')
						{
							$arEnum = CIBlockPropertyEnum::GetByID($ar["VALUE"]);
							if($arEnum!==false)
								$strProperties .= $arEnum["VALUE"];
						}
						else
						{
							$strProperties .= $ar["VALUE"];
						}
					}
				}
				else
				{
					$rs = $DB->Query("
						select *
						from b_iblock_element_prop_m".$IBLOCK_ID."
						where IBLOCK_ELEMENT_ID=".$arIBlockElement["ID"]."
						AND IBLOCK_PROPERTY_ID in (".implode(", ", array_keys($arProperties[$IBLOCK_ID])).")
					");
					while($ar=$rs->Fetch())
					{
						$strProperties .= "\r\n";
						if($arProperties[$IBLOCK_ID][$ar["IBLOCK_PROPERTY_ID"]]["PROPERTY_TYPE"]=='L')
						{
							$arEnum = CIBlockPropertyEnum::GetByID($ar["VALUE"]);
							if($arEnum!==false)
								$strProperties .= $arEnum["VALUE"];
						}
						else
						{
							$strProperties .= $ar["VALUE"];
						}
					}
					$rs = $DB->Query("
						select *
						from b_iblock_element_prop_s".$IBLOCK_ID."
						where IBLOCK_ELEMENT_ID=".$arIBlockElement["ID"]."
					");
					if($ar=$rs->Fetch())
					{
						foreach($arProperties[$IBLOCK_ID] as $property_id=>$property)
						{
							if( array_key_exists("PROPERTY_".$property_id, $ar)
								&& $property["MULTIPLE"]=="N"
								&& strlen($ar["PROPERTY_".$property_id])>0)
							{
								$strProperties .= "\r\n";
								if($property["PROPERTY_TYPE"]=='L')
								{
									$arEnum = CIBlockPropertyEnum::GetByID($ar["VALUE"]);
									if($arEnum!==false)
										$strProperties .= $arEnum["VALUE"];
								}
								else
								{
									$strProperties .= $ar["PROPERTY_".$property_id];
								}
							}
						}
					}
				}
			}
			$BODY .= $strProperties;

			CSearch::Index(
					"iblock",
					$ID,
					Array(
						"LAST_MODIFIED"=>(strlen($arIBlockElement["DATE_FROM"])>0?$arIBlockElement["DATE_FROM"]:$arIBlockElement["LAST_MODIFIED"]),
						"DATE_FROM"=>(strlen($arIBlockElement["DATE_FROM"])>0? $arIBlockElement["DATE_FROM"] : false),
						"DATE_TO"=>(strlen($arIBlockElement["DATE_TO"])>0? $arIBlockElement["DATE_TO"] : false),
						"TITLE"=>$arIBlockElement["NAME"],
						"PARAM1"=>$arIBlockElement["IBLOCK_TYPE_ID"],
						"PARAM2"=>$IBLOCK_ID,
						"SITE_ID"=>$arSITE[$IBLOCK_ID],
						"PERMISSIONS"=>$arGroups[$IBLOCK_ID],
						"URL"=>$DETAIL_URL,
						"BODY"=>$BODY,
						"TAGS"=>$arIBlockElement["TAGS"],
					),
					$bOverWrite
				);
		}
		else
		{
			CSearch::DeleteIndex("iblock", $ID);
		}
	}

	function GetProperty($IBLOCK_ID, $ELEMENT_ID, $by="sort", $order="asc", $arFilter = Array())
	{
		global $DB;
		if(is_array($by))
		{
			if($order!="asc")
				$arFilter = $order;
			$arOrder = $by;
		}
		else
			$arOrder = false;

		$IBLOCK_ID = intval($IBLOCK_ID);
		$ELEMENT_ID = intval($ELEMENT_ID);
		$VERSION = CIBlockElement::GetIBVersion($IBLOCK_ID);

			$strSqlSearch = "";
			foreach($arFilter as $key=>$val)
			{
				if(strlen($val)<=0) continue;
				switch(strtoupper($key))
				{
				case "ACTIVE":
					if($val=="Y" || $val=="N")
						$strSqlSearch .= "AND BP.ACTIVE='".$val."'\n";
					break;
				case "SEARCHABLE":
					if($val=="Y" || $val=="N")
						$strSqlSearch .= "AND BP.SEARCHABLE='".$val."'\n";
					break;
				case "NAME":
					$strSqlSearch .= "AND ".CIBLock::_Upper("BP.NAME")." LIKE ".CIBlock::_Upper("'".$DB->ForSql($val)."'")."\n";
					break;
				case "ID":
					$strSqlSearch .= "AND BP.ID=".IntVal($val)."\n";
					break;
				case "PROPERTY_TYPE":
					$strSqlSearch .= "AND BP.PROPERTY_TYPE='".$DB->ForSql($val)."'\n";
					break;
				case "CODE":
					$strSqlSearch .= "AND ".CIBLock::_Upper("BP.CODE")." LIKE ".CIBLock::_Upper("'".$DB->ForSql($val)."'")."\n";
					break;
				case "EMPTY":
					if($val=="Y")
						$strSqlSearch .= "AND BEP.ID IS NULL\n";
					elseif($VERSION!=2)
						$strSqlSearch .= "AND BEP.ID IS NOT NULL\n";
					break;
				}
			}

			$arSqlOrder = array();
			if($arOrder)
			{
				foreach($arOrder as $by=>$order)
				{
					$order = strtolower($order);
					if($order!="desc")
						$order = "asc";

					$by = strtolower($by);
					if($by == "sort")		$arSqlOrder["BP.SORT"]=$order;
					elseif($by == "id")		$arSqlOrder["BP.ID"]=$order;
					elseif($by == "name")		$arSqlOrder["BP.NAME"]=$order;
					elseif($by == "active")		$arSqlOrder["BP.ACTIVE"]=$order;
					elseif($by == "value_sort")	$arSqlOrder["BEP.SORT"]=$order;
					elseif($by == "value_id")	$arSqlOrder["BEP.ID"]=$order;
					elseif($by == "enum_sort")	$arSqlOrder["BEPE.SORT"]=$order;
					else
						$arSqlOrder["BP.SORT"]=$order;
				}
			}
			else
			{
				if($by == "id")		$arSqlOrder["BP.ID"]="asc";
				elseif($by == "name")	$arSqlOrder["BP.NAME"]="asc";
				elseif($by == "active")	$arSqlOrder["BP.ACTIVE"]="asc";
				else
				{
					$arSqlOrder["BP.SORT"]="asc";
					$by = "sort";
				}

				if ($order!="desc")
				{
					$arSqlOrder["BP.SORT"]="asc";
					$arSqlOrder["BP.ID"]="asc";
					$arSqlOrder["BEPE.SORT"]="asc";
					$arSqlOrder["BEP.ID"]="asc";
					$order = "asc";
				}
				else
				{
					$arSqlOrder["BP.SORT"]="desc";
					$arSqlOrder["BP.ID"]="desc";
					$arSqlOrder["BEPE.SORT"]="desc";
					$arSqlOrder["BEP.ID"]="desc";
				}
			}

			$strSqlOrder = "";
			foreach($arSqlOrder as $key=>$val)
				$strSqlOrder.=", ".$key." ".$val;

			if($strSqlOrder!="")
				$strSqlOrder = ' ORDER BY '.substr($strSqlOrder, 1);

		if($VERSION==2)
		{
			$strTable = "b_iblock_element_prop_m".$IBLOCK_ID;
		}
		else
		{
			$strTable = "b_iblock_element_property";
		}

		$strSql = "
			SELECT BP.*, BEP.ID as PROPERTY_VALUE_ID, BEP.VALUE, BEP.DESCRIPTION, BEPE.VALUE VALUE_ENUM, BEPE.XML_ID VALUE_XML_ID
			FROM b_iblock B
				INNER JOIN b_iblock_property BP ON B.ID=BP.IBLOCK_ID
				LEFT JOIN ".$strTable." BEP ON (BP.ID = BEP.IBLOCK_PROPERTY_ID AND BEP.IBLOCK_ELEMENT_ID = ".$ELEMENT_ID.")
				LEFT JOIN b_iblock_property_enum BEPE ON (BP.PROPERTY_TYPE = 'L' AND BEPE.ID=BEP.VALUE_ENUM AND BEPE.PROPERTY_ID=BP.ID)
			WHERE B.ID = ".$IBLOCK_ID."
				".$strSqlSearch."
			".$strSqlOrder;

		if($VERSION==2)
		{
			$result = array();
			$arElements = array();
			$rs = $DB->Query($strSql);
			while($ar = $rs->Fetch())
			{
				if($ar["VERSION"]==2 && $ar["MULTIPLE"]=="N")
				{
					if(!array_key_exists($ELEMENT_ID, $arElements))
					{
						$strSql = "
							SELECT *
							FROM b_iblock_element_prop_s".$ar["IBLOCK_ID"]."
							WHERE IBLOCK_ELEMENT_ID = ".$ELEMENT_ID."
						";
						$rs2 = $DB->Query($strSql);
						$arElements[$ELEMENT_ID] = $rs2->Fetch();
					}
					if($arFilter["EMPTY"]!="Y" || strlen($arElements["PROPERTY_".$ar["ID"]])>0)
					{
						$val = $arElements[$ELEMENT_ID]["PROPERTY_".$ar["ID"]];
						$ar["PROPERTY_VALUE_ID"]=$ELEMENT_ID.":".$ar["ID"];
						if($ar["PROPERTY_TYPE"]=="L" && intval($val)>0)
						{
							$arEnum = CIBlockPropertyEnum::GetByID($val);
							if($arEnum!==false)
							{
								$ar["VALUE_ENUM"] = $arEnum["VALUE"];
								$ar["VALUE_XML_ID"] = $arEnum["XML_ID"];
							}
						}
						else
						{
							$ar["VALUE_ENUM"] = "";
						}
						if($ar["PROPERTY_TYPE"]=="N" && strlen($val)>0)
						{
							$val = doubleval($val);
						}
						$ar["DESCRIPTION"] = $arElements[$ELEMENT_ID]["DESCRIPTION_".$ar["ID"]];
						$ar["VALUE"] = $val;
					}
					else
						continue;
				}
				if($arFilter["EMPTY"]=="N" && $ar["PROPERTY_VALUE_ID"]=="")
					continue;
				$result[]=$ar;
			}
			$rs = new CIBlockPropertyResult;
			$rs->InitFromArray($result);
		}
		else
		{
			$rs = new CIBlockPropertyResult($DB->Query($strSql));
		}
		return $rs;
	}

	function CounterInc($ID)
	{
		global $DB;
		$ID = IntVal($ID);
		if(!is_array($_SESSION["IBLOCK_COUNTER"]))
			$_SESSION["IBLOCK_COUNTER"] = Array();
		if(in_array($ID, $_SESSION["IBLOCK_COUNTER"]))
			return;
		$_SESSION["IBLOCK_COUNTER"][] = $ID;
		$strSql =
			"UPDATE b_iblock_element SET ".
			"	TIMESTAMP_X = ".($DB->type=="ORACLE"?" NULL":"TIMESTAMP_X").", ".
			"	SHOW_COUNTER_START = ".$DB->IsNull("SHOW_COUNTER_START", $DB->CurrentTimeFunction()).", ".
			"	SHOW_COUNTER =  ".$DB->IsNull("SHOW_COUNTER", 0)." + 1 ".
			"WHERE ID=".$ID;
		$DB->Query($strSql);
	}

	function GetIBVersion($iblock_id)
	{
		if(CIBlock::GetArrayByID($iblock_id, "VERSION") == 2)
			return 2;
		else
			return 1;
	}

	function DeletePropertySQL($property, $iblock_element_id)
	{
		if($property["VERSION"]==2)
		{
			if($property["MULTIPLE"]=="Y")
				return "
					DELETE
					FROM	b_iblock_element_prop_m".$property["IBLOCK_ID"]."
					WHERE
						IBLOCK_ELEMENT_ID=".$iblock_element_id."
						AND IBLOCK_PROPERTY_ID=".$property["ID"]."
				";
			else
				return "
					UPDATE
						b_iblock_element_prop_s".$property["IBLOCK_ID"]."
					SET
						PROPERTY_".$property["ID"]."=null
						,DESCRIPTION_".$property["ID"]."=null
					WHERE
						IBLOCK_ELEMENT_ID=".$iblock_element_id."
				";
		}
		else
		{
			return "
				DELETE FROM
					b_iblock_element_property
				WHERE
					IBLOCK_ELEMENT_ID=".$iblock_element_id."
					AND IBLOCK_PROPERTY_ID=".$property["ID"]."
			";
		}
	}

	function SetPropertyValuesEx($ELEMENT_ID, $IBLOCK_ID, $PROPERTY_VALUES, $FLAGS=array())
	{
		//Check input parameters
		if(!is_array($PROPERTY_VALUES))
			return;

		if(!is_array($FLAGS))
			$FLAGS=array();
		//FLAGS - modify function behavior
		//NewElement - if present no db values select will be issued
		//DoNotValidateLists - if present list values do not validates against metadata tables

		global $DB;

		$ELEMENT_ID = intval($ELEMENT_ID);
		if($ELEMENT_ID <= 0)
			return;

		$IBLOCK_ID = intval($IBLOCK_ID);
		if($IBLOCK_ID<=0)
		{
			$rs = $DB->Query("select IBLOCK_ID from b_iblock_element where ID=".$ELEMENT_ID);
			if($ar = $rs->Fetch())
				$IBLOCK_ID = $ar["IBLOCK_ID"];
			else
				return;
		}

		//Get property metadata
		static $PROPS_CACHE = array();
		if(!array_key_exists($IBLOCK_ID, $PROPS_CACHE))
		{
			$PROPS_CACHE[$IBLOCK_ID] = array(0=>array());
			$rs = CIBlock::GetProperties($IBLOCK_ID, array(), array("ACTIVE"=>"Y"));
			while($ar = $rs->Fetch())
			{
				$ar["ConvertToDB"] = false;
				if($ar["USER_TYPE"]!="")
				{
					$arUserType = CIBlockProperty::GetUserType($ar["USER_TYPE"]);
					if(array_key_exists("ConvertToDB", $arUserType))
						$ar["ConvertToDB"] = $arUserType["ConvertToDB"];
				}

				$PROPS_CACHE[$IBLOCK_ID][$ar["ID"]] = $ar;
				//For CODE2ID conversion
				$PROPS_CACHE[$IBLOCK_ID][0][$ar["CODE"]] = $ar["ID"];
				//VERSION
				$PROPS_CACHE[$IBLOCK_ID]["VERSION"] = $ar["VERSION"];
			}
		}
		//echo "PROPS_CACHE=".htmlspecialchars(print_r($PROPS_CACHE[$IBLOCK_ID], true))."\n";

		//Unify properties values arProps[$property_id]=>array($id=>array("VALUE", "DESCRIPTION"),....)
		$arProps = array();
		foreach($PROPERTY_VALUES as $key=>$value)
		{
			//Code2ID
			if(array_key_exists($key, $PROPS_CACHE[$IBLOCK_ID][0]))
				$key = $PROPS_CACHE[$IBLOCK_ID][0][$key];

			if($PROPS_CACHE[$IBLOCK_ID][$key]["PROPERTY_TYPE"]=="F")
			{
				if(is_array($value))
				{
					$ar = array_keys($value);
					if(array_key_exists("tmp_name", $value))
					{
						$uni_value = array(array("ID"=>0,"VALUE"=>$value,"DESCRIPTION"=>""));
					}
					elseif($ar[0]==="VALUE" && $ar[1]==="DESCRIPTION")
					{
						$uni_value = array(array("ID"=>0,"VALUE"=>$value["VALUE"],"DESCRIPTION"=>$value["DESCRIPTION"]));
					}
					elseif(count($ar)===1 && $ar[0]==="VALUE")
					{
						$uni_value = array(array("ID"=>0,"VALUE"=>$value["VALUE"],"DESCRIPTION"=>""));
					}
					else //multiple values
					{
						$uni_value = array();
						foreach($value as $id=>$val)
						{
							if(is_array($val))
							{
								if(array_key_exists("tmp_name", $val))
								{
									$uni_value[] = array("ID"=>$id,"VALUE"=>$val,"DESCRIPTION"=>"");
								}
								else
								{
									$ar = array_keys($val);
									if($ar[0]==="VALUE" && $ar[1]==="DESCRIPTION")
										$uni_value[] = array("ID"=>$id,"VALUE"=>$val["VALUE"],"DESCRIPTION"=>$val["DESCRIPTION"]);
									elseif(count($ar)===1 && $ar[0]==="VALUE")
										$uni_value[] = array("ID"=>$id,"VALUE"=>$value["VALUE"],"DESCRIPTION"=>"");
								}
							}
						}
					}
				}
			}
			elseif(!is_array($value))
			{
				$uni_value = array(array("VALUE"=>$value,"DESCRIPTION"=>""));
			}
			else
			{
				$ar = array_keys($value);
				if($ar[0]==="VALUE" && $ar[1]==="DESCRIPTION")
				{
					$uni_value = array(array("VALUE"=>$value["VALUE"],"DESCRIPTION"=>$value["DESCRIPTION"]));
				}
				elseif(count($ar)===1 && $ar[0]==="VALUE")
				{
					$uni_value = array(array("VALUE"=>$value["VALUE"],"DESCRIPTION"=>""));
				}
				else // multiple values
				{
					$uni_value = array();
					foreach($value as $id=>$val)
					{
						if(!is_array($val))
							$uni_value[] = array("VALUE"=>$val,"DESCRIPTION"=>"");
						else
						{
							$ar = array_keys($val);
							if($ar[0]==="VALUE" && $ar[1]==="DESCRIPTION")
								$uni_value[] = array("VALUE"=>$val["VALUE"],"DESCRIPTION"=>$val["DESCRIPTION"]);
							elseif(count($ar)===1 && $ar[0]==="VALUE")
								$uni_value[] = array("VALUE"=>$val["VALUE"],"DESCRIPTION"=>"");
						}
					}
				}
			}
			foreach($uni_value as $val)
			{
				if(!array_key_exists($key, $arProps))
				{
					$arProps[$key] = array();
				}
				if($PROPS_CACHE[$IBLOCK_ID][$key]["ConvertToDB"]!==false)
				{
					$val = call_user_func_array($PROPS_CACHE[$IBLOCK_ID][$key]["ConvertToDB"], array($PROPS_CACHE[$IBLOCK_ID][$key], $val));
				}
				if(strlen($val["VALUE"])>0)
				{
					if(count($arProps[$key])==0 || $PROPS_CACHE[$IBLOCK_ID][$key]["MULTIPLE"]=="Y")
					{
						$arProps[$key][] = $val;
					}
				}
			}
		}
		if(count($arProps)<=0)
			return;

		//Read current property values from database
		$arDBProps = array();
		if(!array_key_exists("NewElement", $FLAGS))
		{
			if($PROPS_CACHE[$IBLOCK_ID]["VERSION"]==1)
			{
				$rs = $DB->Query("
					select *
					from b_iblock_element_property
					where IBLOCK_ELEMENT_ID=".$ELEMENT_ID."
					AND IBLOCK_PROPERTY_ID in (".implode(", ", array_keys($arProps)).")
				");
				while($ar=$rs->Fetch())
				{
					if(!array_key_exists($ar["IBLOCK_PROPERTY_ID"], $arDBProps))
						$arDBProps[$ar["IBLOCK_PROPERTY_ID"]] = array();
					$arDBProps[$ar["IBLOCK_PROPERTY_ID"]][$ar["ID"]] = $ar;
				}
			}
			else
			{
				$rs = $DB->Query("
					select *
					from b_iblock_element_prop_m".$IBLOCK_ID."
					where IBLOCK_ELEMENT_ID=".$ELEMENT_ID."
					AND IBLOCK_PROPERTY_ID in (".implode(", ", array_keys($arProps)).")
				");
				while($ar=$rs->Fetch())
				{
					if(!array_key_exists($ar["IBLOCK_PROPERTY_ID"], $arDBProps))
						$arDBProps[$ar["IBLOCK_PROPERTY_ID"]] = array();
					$arDBProps[$ar["IBLOCK_PROPERTY_ID"]][$ar["ID"]] = $ar;
				}
				$rs = $DB->Query("
					select *
					from b_iblock_element_prop_s".$IBLOCK_ID."
					where IBLOCK_ELEMENT_ID=".$ELEMENT_ID."
				");
				if($ar=$rs->Fetch())
				{
					foreach($PROPS_CACHE[$IBLOCK_ID] as $property_id=>$property)
					{
						if(	array_key_exists($property_id, $arProps)
							&& array_key_exists("PROPERTY_".$property_id, $ar)
							&& $property["MULTIPLE"]=="N"
							&& strlen($ar["PROPERTY_".$property_id])>0)
						{
							$pr=array(
								"IBLOCK_PROPERTY_ID" => $property_id,
								"VALUE" => $ar["PROPERTY_".$property_id],
								"DESCRIPTION" => $ar["DESCRIPTION_".$property_id],
							);
							if(!array_key_exists($pr["IBLOCK_PROPERTY_ID"], $arDBProps))
								$arDBProps[$pr["IBLOCK_PROPERTY_ID"]] = array();
							$arDBProps[$pr["IBLOCK_PROPERTY_ID"]][$ELEMENT_ID.":".$property_id] = $pr;
						}
					}
				}
			}
		}
		//echo "arDBProps=".htmlspecialchars(print_r($arDBProps, true))."\n";

		//Handle file properties
		foreach($arProps as $property_id=>$values)
		{
			if($PROPS_CACHE[$IBLOCK_ID][$property_id]["PROPERTY_TYPE"]=="F")
			{
				foreach($values as $i=>$value)
				{
					$val = $value["VALUE"];
					$val["MODULE_ID"] = "iblock";
					if(!$val["OLD_VALUE"])
						$val["old_file"] = $arDBProps[$property_id][$value["ID"]]["VALUE"];
					if(strlen($value["DESCRIPTION"])>0)
						$val["description"] = $value["DESCRIPTION"];

					$val = CFile::SaveFile($val, "iblock");

					if($val=="NULL")
					{//Delete it! Actually it will not add an value
						unset($arProps[$property_id][$i]);
					}
					elseif(intval($val)>0)
					{
						$arProps[$property_id][$i]["VALUE"] = intval($val);
						if(strlen($value["DESCRIPTION"])<=0)
							$arProps[$property_id][$i]["DESCRIPTION"]=$arDBProps[$property_id][$value["ID"]]["DESCRIPTION"];
						//CFile::Delete will not called
						unset($arDBProps[$property_id][$value["ID"]]);
					}
					elseif(strlen($value["DESCRIPTION"])>0)
					{
						$arProps[$property_id][$i]["VALUE"] = $arDBProps[$property_id][$value["ID"]]["VALUE"];
						//Only needs to update description so CFile::Delete will not called
						unset($arDBProps[$property_id][$value["ID"]]);
					}
					elseif(array_key_exists("del", $val) || ($val["del"] == "Y"))
					{
						//File will be deleted only when we get command del=Y
						unset($arProps[$property_id][$i]);
					}
					else
					{
						$arProps[$property_id][$i]["VALUE"] = $arDBProps[$property_id][$value["ID"]]["VALUE"];
						//CFile::Delete will not called
						unset($arDBProps[$property_id][$value["ID"]]);
					}
				}
				foreach($arDBProps[$property_id] as $id=>$value)
				{
					//echo "CFile::Delete:".$value["VALUE"]."\n";
					CFile::Delete($value["VALUE"]);
				}
			}
		}

		//Now we'll try to find out properties which do not require any update
		if(!array_key_exists("NewElement", $FLAGS))
		{
			foreach($arProps as $property_id=>$values)
			{
				if($PROPS_CACHE[$IBLOCK_ID][$property_id]["PROPERTY_TYPE"]!="F")
				{
					if(array_key_exists($property_id, $arDBProps))
					{
						$db_values = $arDBProps[$property_id];
						if(count($values) == count($db_values))
						{
							$bEqual = true;
							foreach($values as $id=>$value)
							{
								$bDBFound = false;
								foreach($db_values as $db_id=>$db_row)
								{
									if(strcmp($value["VALUE"],$db_row["VALUE"])==0 && strcmp($value["DESCRIPTION"],$db_row["DESCRIPTION"])==0)
									{
										unset($db_values[$db_id]);
										$bDBFound = true;
										break;
									}
								}
								if(!$bDBFound)
								{
									$bEqual = false;
									break;
								}
							}
							if($bEqual)
							{
								unset($arProps[$property_id]);
								unset($arDBProps[$property_id]);
							}
						}
					}
					elseif(count($values)==0)
					{
						//Values was not found in database neither no values input was given
						unset($arProps[$property_id]);
					}
				}
			}
		}

		//Init "commands" arrays
		$ar2Delete = array(
			"b_iblock_element_property" => array(/*property_id=>true, property_id=>true, ...*/),
			"b_iblock_element_prop_m".$IBLOCK_ID => array(/*property_id=>true, property_id=>true, ...*/),
			"b_iblock_section_element" => array(/*property_id=>true, property_id=>true, ...*/),
		);
		$ar2Insert = array(
			"values" => array(
				"b_iblock_element_property" => array(/*property_id=>value, property_id=>value, ...*/),
				"b_iblock_element_prop_m".$IBLOCK_ID => array(/*property_id=>value, property_id=>value, ...*/),
			),
			"sqls"=>array(
				"b_iblock_element_property" => array(/*property_id=>sql, property_id=>sql, ...*/),
				"b_iblock_element_prop_m".$IBLOCK_ID => array(/*property_id=>sql, property_id=>sql, ...*/),
				"b_iblock_section_element" => array(/*property_id=>sql, property_id=>sql, ...*/),
			),
		);
		$ar2Update = array(
			//"b_iblock_element_property" => array(/*property_id=>value, property_id=>value, ...*/),
			//"b_iblock_element_prop_m".$IBLOCK_ID => array(/*property_id=>value, property_id=>value, ...*/),
			"b_iblock_element_prop_s".$IBLOCK_ID => array(/*property_id=>value, property_id=>value, ...*/),
		);

		foreach($arDBProps as $property_id=>$values)
		{
			if($PROPS_CACHE[$IBLOCK_ID][$property_id]["VERSION"]==1)
			{
				$ar2Delete["b_iblock_element_property"][$property_id]=true;
			}
			elseif($PROPS_CACHE[$IBLOCK_ID][$property_id]["MULTIPLE"]=="Y")
			{
				$ar2Delete["b_iblock_element_prop_m".$IBLOCK_ID][$property_id]=true;
				$ar2Update["b_iblock_element_prop_s".$IBLOCK_ID][$property_id]=false;//null
			}
			else
			{
				$ar2Update["b_iblock_element_prop_s".$IBLOCK_ID][$property_id]=false;//null
			}
			if($PROPS_CACHE[$IBLOCK_ID][$property_id]["PROPERTY_TYPE"]=="G")
				$ar2Delete["b_iblock_section_element"][$property_id]=true;
		}

		foreach($arProps as $property_id=>$values)
		{
			$db_prop = $PROPS_CACHE[$IBLOCK_ID][$property_id];
			if($db_prop["PROPERTY_TYPE"]=="L" && !array_key_exists("DoNotValidateLists",$FLAGS))
			{
				$arID=array();
				foreach($values as $value)
				{
					$value["VALUE"] = intval($value["VALUE"]);
					if($value["VALUE"]>0)
						$arID[]=$value["VALUE"];
				}
				if(count($arID)>0)
				{
					if($db_prop["VERSION"]==1)
					{
						$ar2Insert["sqls"]["b_iblock_element_property"][$property_id] = "
								INSERT INTO b_iblock_element_property
								(IBLOCK_ELEMENT_ID, IBLOCK_PROPERTY_ID, VALUE, VALUE_ENUM)
								SELECT ".$ELEMENT_ID.", P.ID, PEN.ID, PEN.ID
								FROM
									b_iblock_property P
									,b_iblock_property_enum PEN
								WHERE
									P.ID=".$property_id."
									AND P.ID=PEN.PROPERTY_ID
									AND PEN.ID IN (".implode(", ",$arID).")
						";
					}
					elseif($db_prop["MULTIPLE"]=="Y")
					{
						$ar2Insert["sqls"]["b_iblock_element_prop_m".$IBLOCK_ID][$property_id] = "
								INSERT INTO b_iblock_element_prop_m".$IBLOCK_ID."
								(IBLOCK_ELEMENT_ID, IBLOCK_PROPERTY_ID, VALUE, VALUE_ENUM)
								SELECT ".$ELEMENT_ID.", P.ID, PEN.ID, PEN.ID
								FROM
									b_iblock_property P
									,b_iblock_property_enum PEN
								WHERE
									P.ID=".$property_id."
									AND P.ID=PEN.PROPERTY_ID
									AND PEN.ID IN (".implode(", ",$arID).")
						";
					}
					else
					{
						$rs = $DB->Query("
								SELECT PEN.ID
								FROM
									b_iblock_property P
									,b_iblock_property_enum PEN
								WHERE
									P.ID=".$property_id."
									AND P.ID=PEN.PROPERTY_ID
									AND PEN.ID IN (".implode(", ",$arID).")
						");
						if($ar = $rs->Fetch())
							$ar2Update["b_iblock_element_prop_s".$IBLOCK_ID][$property_id]=array("VALUE"=>$ar["ID"],"DESCRIPTION"=>"");
					}
				}
				continue;
			}
			if($db_prop["PROPERTY_TYPE"]=="G")
			{
				$arID=array();
				foreach($values as $value)
				{
					$value["VALUE"] = intval($value["VALUE"]);
					if($value["VALUE"]>0)
						$arID[]=$value["VALUE"];
				}
				if(count($arID)>0)
				{
					if($db_prop["VERSION"]==1)
					{
						$ar2Insert["sqls"]["b_iblock_element_property"][$property_id] = "
								INSERT INTO b_iblock_element_property
								(IBLOCK_ELEMENT_ID, IBLOCK_PROPERTY_ID, VALUE, VALUE_NUM)
								SELECT ".$ELEMENT_ID.", P.ID, S.ID, S.ID
								FROM
									b_iblock_property P
									,b_iblock_section S
								WHERE
									P.ID=".$property_id."
									AND S.IBLOCK_ID = P.LINK_IBLOCK_ID
									AND S.ID IN (".implode(", ",$arID).")
						";
					}
					elseif($db_prop["MULTIPLE"]=="Y")
					{
						$ar2Insert["sqls"]["b_iblock_element_prop_m".$IBLOCK_ID][$property_id] = "
								INSERT INTO b_iblock_element_prop_m".$IBLOCK_ID."
								(IBLOCK_ELEMENT_ID, IBLOCK_PROPERTY_ID, VALUE, VALUE_NUM)
								SELECT ".$ELEMENT_ID.", P.ID, S.ID, S.ID
								FROM
									b_iblock_property P
									,b_iblock_section S
								WHERE
									P.ID=".$property_id."
									AND S.IBLOCK_ID = P.LINK_IBLOCK_ID
									AND S.ID IN (".implode(", ",$arID).")
						";
					}
					else
					{
						$rs = $DB->Query("
								SELECT S.ID
								FROM
									b_iblock_property P
									,b_iblock_section S
								WHERE
									P.ID=".$property_id."
									AND S.IBLOCK_ID = P.LINK_IBLOCK_ID
									AND S.ID IN (".implode(", ",$arID).")
						");
						if($ar = $rs->Fetch())
							$ar2Update["b_iblock_element_prop_s".$IBLOCK_ID][$property_id]=array("VALUE"=>$ar["ID"],"DESCRIPTION"=>"");
					}
					$ar2Insert["sqls"]["b_iblock_section_element"][$property_id] = "
						INSERT INTO b_iblock_section_element
						(IBLOCK_ELEMENT_ID, IBLOCK_SECTION_ID, ADDITIONAL_PROPERTY_ID)
						SELECT ".$ELEMENT_ID.", S.ID, P.ID
						FROM b_iblock_property P, b_iblock_section S
						WHERE P.ID=".$property_id."
							AND S.IBLOCK_ID = P.LINK_IBLOCK_ID
							AND S.ID IN (".implode(", ",$arID).")
					";
				}
				continue;
			}
			foreach($values as $value)
			{
				if($db_prop["VERSION"]==1)
				{
					$ar2Insert["values"]["b_iblock_element_property"][$property_id][]=$value;
				}
				elseif($db_prop["MULTIPLE"]=="Y")
				{
					$ar2Insert["values"]["b_iblock_element_prop_m".$IBLOCK_ID][$property_id][]=$value;
					$ar2Update["b_iblock_element_prop_s".$IBLOCK_ID][$property_id]=false;//null
				}
				else
				{
					$ar2Update["b_iblock_element_prop_s".$IBLOCK_ID][$property_id]=$value;
				}
			}
		}

		foreach($ar2Delete as $table=>$arID)
		{
			if(count($arID)>0)
			{
				if($table=="b_iblock_section_element")
					$DB->Query("
						delete from ".$table."
						where IBLOCK_ELEMENT_ID = ".$ELEMENT_ID."
						and  ADDITIONAL_PROPERTY_ID in (".implode(", ", array_keys($arID)).")
					");
				else
					$DB->Query("
						delete from ".$table."
						where IBLOCK_ELEMENT_ID = ".$ELEMENT_ID."
						and IBLOCK_PROPERTY_ID in (".implode(", ", array_keys($arID)).")
					");
			}
		}

		foreach($ar2Insert["values"] as $table=>$properties)
		{
			$strSqlPrefix = "
					insert into ".$table."
					(IBLOCK_PROPERTY_ID, IBLOCK_ELEMENT_ID, VALUE, VALUE_ENUM, VALUE_NUM, DESCRIPTION)
					values
			";

			$maxValuesLen = $DB->type=="MYSQL"?1024:0;
			$strSqlValues = "";
			foreach($properties as $property_id=>$values)
			{
				foreach($values as $value)
				{
					if(strlen($value["VALUE"])>0)
					{
						$strSqlValues .= ",\n(".
							$property_id.", ".
							$ELEMENT_ID.", ".
							"'".$DB->ForSQL($value["VALUE"])."', ".
							intval($value["VALUE"]).", ".
							roundDB($value["VALUE"]).", ".
							(strlen($value["DESCRIPTION"])? "'".$DB->ForSQL($value["DESCRIPTION"])."'": "null")." ".
						")";
					}
					if(strlen($strSqlValues)>$maxValuesLen)
					{
						$DB->Query($strSqlPrefix.substr($strSqlValues, 2));
						$strSqlValues = "";
					}
				}
			}
			if(strlen($strSqlValues)>0)
			{
				$DB->Query($strSqlPrefix.substr($strSqlValues, 2));
				$strSqlValues = "";
			}
		}

		foreach($ar2Insert["sqls"] as $table=>$properties)
		{
			foreach($properties as $property_id=>$sql)
			{
				$DB->Query($sql);
			}
		}

		foreach($ar2Update as $table=>$properties)
		{
			if(count($properties)>0)
			{
				$arFields = array();
				foreach($properties as $property_id=>$value)
				{
					if($value===false || strlen($value["VALUE"])<=0)
					{
						$arFields[] = "PROPERTY_".$property_id." = null";
						$arFields[] = "DESCRIPTION_".$property_id." = null";
					}
					else
					{
						$arFields[] = "PROPERTY_".$property_id." = '".$DB->ForSQL($value["VALUE"])."'";
						if(strlen($value["DESCRIPTION"]))
							$arFields[] = "DESCRIPTION_".$property_id." = '".$DB->ForSQL($value["DESCRIPTION"])."'";
						else
							$arFields[] = "DESCRIPTION_".$property_id." = null";
					}
				}
				$DB->Query("
					update ".$table."
					set ".implode(",\n", $arFields)."
					where IBLOCK_ELEMENT_ID = ".$ELEMENT_ID."
				");
			}
		}
		/****************************** QUOTA ******************************/
		$_SESSION["SESS_RECOUNT_DB"] = "Y";
		/****************************** QUOTA ******************************/
	}
}
?>
