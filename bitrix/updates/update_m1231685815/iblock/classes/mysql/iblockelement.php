<?
include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/classes/general/iblockelement.php");
//IncludeModuleLangFile(__FILE__);

class CIBlockElement extends CAllIBlockElement
{
	///////////////////////////////////////////////////////////////////
	// Clear history
	///////////////////////////////////////////////////////////////////
	function WF_CleanUpHistory()
	{
		if (CModule::IncludeModule("workflow"))
		{
			$err_mess = "FILE: ".__FILE__."<br>LINE: ";
			global $DB;
			$HISTORY_DAYS = intval(COption::GetOptionString("workflow","HISTORY_DAYS","-1"));
			if ($HISTORY_DAYS>=0)
			{
				$strSql =
					"SELECT ID, WF_PARENT_ELEMENT_ID ".
					"FROM b_iblock_element ".
					"WHERE to_days(now())-to_days(TIMESTAMP_X)>=".$HISTORY_DAYS." ".
					"	AND WF_PARENT_ELEMENT_ID is not null ";
				$z = $DB->Query($strSql, false, $err_mess.__LINE__);
				while ($zr = $z->Fetch())
				{
					$LAST_ID = CIBlockElement::WF_GetLast($zr["WF_PARENT_ELEMENT_ID"]);
					if ($LAST_ID!=$zr["ID"])
					{
						CIBlockElement::Delete($zr["ID"]);
					}
				}
			}
		}
	}

	///////////////////////////////////////////////////////////////////
	// Function returns lock status of element (red, yellow, green)
	///////////////////////////////////////////////////////////////////
	function WF_GetLockStatus($ID, &$locked_by, &$date_lock)
	{
		global $DB, $USER;
		$err_mess = "FILE: ".__FILE__."<br> LINE:";
		$ID = intval($ID);
		$MAX_LOCK = intval(COption::GetOptionString("workflow","MAX_LOCK_TIME","60"));
		$uid = intval($USER->GetID());
		$strSql =
			"SELECT WF_LOCKED_BY, ".
			"	".$DB->DateToCharFunction("WF_DATE_LOCK")."		WF_DATE_LOCK, ".
			"	if (WF_DATE_LOCK is null, 'green',  ".
			"		if(DATE_ADD(WF_DATE_LOCK, interval $MAX_LOCK MINUTE)<now(), 'green', ".
			"			if(WF_LOCKED_BY=$uid, 'yellow', 'red')))						LOCK_STATUS ".
			"FROM b_iblock_element ".
			"WHERE ID = ".$ID." ";
		$z = $DB->Query($strSql, false, $err_mess.__LINE__);
		$zr = $z->Fetch();
		$locked_by = $zr["WF_LOCKED_BY"];
		$date_lock = $zr["WF_DATE_LOCK"];
		return $zr["LOCK_STATUS"];
	}

	///////////////////////////////////////////////////////////////////
	// Locking element
	///////////////////////////////////////////////////////////////////
	function WF_Lock($LAST_ID)
	{
		global $DB, $USER;
		$err_mess = "FILE: ".__FILE__."<br>LINE: ";
		$LAST_ID = intval($LAST_ID);
		$strSql = "SELECT WF_PARENT_ELEMENT_ID FROM b_iblock_element WHERE ID=".$LAST_ID;
		$z = $DB->Query($strSql, false, $err_mess.__LINE__);
		if ($zr = $z->Fetch())
		{
			$PARENT_ID = $zr["WF_PARENT_ELEMENT_ID"];
			$arFields = array(
				"WF_DATE_LOCK"	=> $DB->GetNowFunction(),
				"WF_LOCKED_BY"	=> $USER->GetID()
				);
			$DB->Update("b_iblock_element",$arFields,"WHERE ID in (".$LAST_ID.", ".IntVal($PARENT_ID).")",$err_mess.__LINE__);
		}
	}

	///////////////////////////////////////////////////////////////////
	// Unlock element
	///////////////////////////////////////////////////////////////////
	function WF_UnLock($LAST_ID)
	{
		global $DB, $USER;
		$LAST_ID = intval($LAST_ID);
		$err_mess = "FILE: ".__FILE__."<br>LINE: ";
		$strSql = "SELECT WF_PARENT_ELEMENT_ID, WF_LOCKED_BY FROM b_iblock_element WHERE ID=".$LAST_ID;
		$z = $DB->Query($strSql,false,$err_mess.__LINE__);
		$zr = $z->Fetch();
		if (CWorkflow::IsAdmin() || $zr["WF_LOCKED_BY"]==$USER->GetID())
		{
			$arFields = array(
				"WF_DATE_LOCK"	=> "null",
				"WF_LOCKED_BY"	=> "null"
				);
			$PARENT_ID = $zr["WF_PARENT_ELEMENT_ID"];
			$DB->Update("b_iblock_element",$arFields,"WHERE ID in ('$LAST_ID','$PARENT_ID') or WF_PARENT_ELEMENT_ID = '$PARENT_ID'",$err_mess.__LINE__);
		}
	}


	///////////////////////////////////////////////////////////////////
	// List the history items
	///////////////////////////////////////////////////////////////////
	function WF_GetHistoryList($ELEMENT_ID, &$by, &$order, $arFilter=Array(), &$is_filtered)
	{
		$err_mess = "FILE: ".__FILE__."<br>LINE: ";
		global $DB;
		$ELEMENT_ID = intval($ELEMENT_ID);
		$arSqlSearch = Array();
		$strSqlSearch = "";
		if(is_array($arFilter))
		{
			$filter_keys = array_keys($arFilter);
			for ($i=0; $i<count($filter_keys); $i++)
			{
				$val = $DB->ForSql($arFilter[$filter_keys[$i]]);
				if (strlen($val)<=0 || $val=="NOT_REF") continue;
				switch(strtoupper($filter_keys[$i]))
				{
				case "ID":
					$arr = explode(",",$val); $str = "";
					foreach ($arr as $a) $str .= intval($a).",";
					$arSqlSearch[] = "E.ID in (".$DB->ForSQL($str)."0)";
					break;
				case "TIMESTAMP_FROM":
					$arSqlSearch[] = "E.TIMESTAMP_X>=FROM_UNIXTIME('".MkDateTime(FmtDate($val,"D.M.Y"),"d.m.Y")."')";
					break;
				case "TIMESTAMP_TO":
					$arSqlSearch[] = "E.TIMESTAMP_X<=FROM_UNIXTIME('".MkDateTime(FmtDate($val,"D.M.Y")." 23:59:59","d.m.Y H:i:s")."')";
					break;
				case "MODIFIED_BY":
				case "MODIFIED_USER_ID":
					$arSqlSearch[] = "E.MODIFIED_BY='".intval($val)."'";
					break;
				case "IBLOCK_ID":
					$arSqlSearch[] = "E.IBLOCK_ID='".intval($val)."'";
					break;
				case "NAME":
					if($val!="%%")
						$arSqlSearch[] = "upper(E.NAME) like upper('".$DB->ForSQL($val,255)."')";
					break;
				case "STATUS":
				case "STATUS_ID":
					$arSqlSearch[] = "E.WF_STATUS_ID='".intval($val)."'";
					break;
				}
			}
			for($i=0; $i<count($arSqlSearch); $i++) $strSqlSearch .= " and (".$arSqlSearch[$i].") ";
		}

		if($by == "s_id")		$strSqlOrder = "ORDER BY E.ID";
		elseif($by == "s_timestamp")	$strSqlOrder = "ORDER BY E.TIMESTAMP_X";
		elseif($by == "s_modified_by")	$strSqlOrder = "ORDER BY E.MODIFIED_BY";
		elseif($by == "s_name")		$strSqlOrder = "ORDER BY E.NAME";
		elseif($by == "s_status")	$strSqlOrder = "ORDER BY E.WF_STATUS_ID";
		else
		{
			$by = "s_id";
			$strSqlOrder = "ORDER BY E.ID";
		}
		if($order!="asc")
		{
			$strSqlOrder .= " desc ";
			$order="desc";
		}

		$strSql = "
			SELECT
				E.*,
				".$DB->DateToCharFunction("E.TIMESTAMP_X")." TIMESTAMP_X,
				concat('(', U.LOGIN, ') ', ifnull(U.NAME,''), ' ', ifnull(U.LAST_NAME,'')) USER_NAME,
				S.TITLE STATUS_TITLE
			FROM
				b_iblock_element E
				INNER JOIN b_workflow_status S on S.ID = E.WF_STATUS_ID
				LEFT JOIN b_user U ON U.ID = E.MODIFIED_BY
			WHERE
				E.WF_PARENT_ELEMENT_ID = ".$ELEMENT_ID."
			".$strSqlSearch.$strSqlOrder;
		$res = $DB->Query($strSql, false, $err_mess.__LINE__);
		$is_filtered = (strlen($strSqlSearch)>0);
		return $res;
	}

	///////////////////////////////////////////////////////////////////
	// List of elements
	///////////////////////////////////////////////////////////////////
	function GetList($arOrder=Array("SORT"=>"ASC"), $arFilter=Array(), $arGroupBy=false, $arNavStartParams=false, $arSelectFields=Array())
	{
		/*
		Filter combinations:
		CHECK_PERMISSIONS="N" - check permissions of the current user to the infoblock
			MIN_PERMISSION="R" - when permissions check, then minimal access level
		SHOW_HISTORY="N" - add history items to list
			SHOW_NEW="N" - if not add history items, then add new, but not published elements
		*/
		global $DB, $USER, $APPLICATION;
		$MAX_LOCK = intval(COption::GetOptionString("workflow","MAX_LOCK_TIME","60"));
		if(is_object($USER))
			$uid = intval($USER->GetID());
		else
			$uid = 0;

		$arIblockElementFields = Array(
				"ID"=>"BE.ID",
				"TIMESTAMP_X"=>$DB->DateToCharFunction("BE.TIMESTAMP_X"),
				"MODIFIED_BY"=>"BE.MODIFIED_BY",
				"DATE_CREATE"=>$DB->DateToCharFunction("BE.DATE_CREATE"),
				"CREATED_BY"=>"BE.CREATED_BY",
				"IBLOCK_ID"=>"BE.IBLOCK_ID",
				"IBLOCK_SECTION_ID"=>"BE.IBLOCK_SECTION_ID",
				"ACTIVE"=>"BE.ACTIVE",
				/*
				"ACTIVE_FROM"=>$DB->DateToCharFunction("BE.ACTIVE_FROM", CPageOption::GetOptionString("iblock", "FORMAT_ACTIVE_DATES", "SHORT")),
				"ACTIVE_TO"=>$DB->DateToCharFunction("BE.ACTIVE_TO", CPageOption::GetOptionString("iblock", "FORMAT_ACTIVE_DATES", "SHORT")),
				"DATE_ACTIVE_FROM"=>$DB->DateToCharFunction("BE.ACTIVE_FROM", CPageOption::GetOptionString("iblock", "FORMAT_ACTIVE_DATES", "SHORT")),
				"DATE_ACTIVE_TO"=>$DB->DateToCharFunction("BE.ACTIVE_TO", CPageOption::GetOptionString("iblock", "FORMAT_ACTIVE_DATES", "SHORT")),
				*/
				"ACTIVE_FROM"=>(
						CPageOption::GetOptionString("iblock", "FORMAT_ACTIVE_DATES", "-")!="-"
						?
							$DB->DateToCharFunction("BE.ACTIVE_FROM", CPageOption::GetOptionString("iblock", "FORMAT_ACTIVE_DATES", "SHORT"))
						:
							"IF(EXTRACT(HOUR_SECOND FROM BE.ACTIVE_FROM)>0, ".$DB->DateToCharFunction("BE.ACTIVE_FROM", "FULL").", ".$DB->DateToCharFunction("BE.ACTIVE_FROM", "SHORT").")"
						),
				"ACTIVE_TO"=>(
						CPageOption::GetOptionString("iblock", "FORMAT_ACTIVE_DATES", "-")!="-"
						?
							$DB->DateToCharFunction("BE.ACTIVE_TO", CPageOption::GetOptionString("iblock", "FORMAT_ACTIVE_DATES", "SHORT"))
						:
							"IF(EXTRACT(HOUR_SECOND FROM BE.ACTIVE_TO)>0, ".$DB->DateToCharFunction("BE.ACTIVE_TO", "FULL").", ".$DB->DateToCharFunction("BE.ACTIVE_TO", "SHORT").")"
						),
				"DATE_ACTIVE_FROM"=>(
						CPageOption::GetOptionString("iblock", "FORMAT_ACTIVE_DATES", "-")!="-"
						?
							$DB->DateToCharFunction("BE.ACTIVE_FROM", CPageOption::GetOptionString("iblock", "FORMAT_ACTIVE_DATES", "SHORT"))
						:
							"IF(EXTRACT(HOUR_SECOND FROM BE.ACTIVE_FROM)>0, ".$DB->DateToCharFunction("BE.ACTIVE_FROM", "FULL").", ".$DB->DateToCharFunction("BE.ACTIVE_FROM", "SHORT").")"
						),
				"DATE_ACTIVE_TO"=>(
						CPageOption::GetOptionString("iblock", "FORMAT_ACTIVE_DATES", "-")!="-"
						?
							$DB->DateToCharFunction("BE.ACTIVE_TO", CPageOption::GetOptionString("iblock", "FORMAT_ACTIVE_DATES", "SHORT"))
						:
							"IF(EXTRACT(HOUR_SECOND FROM BE.ACTIVE_TO)>0, ".$DB->DateToCharFunction("BE.ACTIVE_TO", "FULL").", ".$DB->DateToCharFunction("BE.ACTIVE_TO", "SHORT").")"
						),
				"SORT"=>"BE.SORT",
				"NAME"=>"BE.NAME",
				"PREVIEW_PICTURE"=>"BE.PREVIEW_PICTURE",
				"PREVIEW_TEXT"=>"BE.PREVIEW_TEXT",
				"PREVIEW_TEXT_TYPE"=>"BE.PREVIEW_TEXT_TYPE",
				"DETAIL_PICTURE"=>"BE.DETAIL_PICTURE",
				"DETAIL_TEXT"=>"BE.DETAIL_TEXT",
				"DETAIL_TEXT_TYPE"=>"BE.DETAIL_TEXT_TYPE",
				"SEARCHABLE_CONTENT"=>"BE.SEARCHABLE_CONTENT",
				"WF_STATUS_ID"=>"BE.WF_STATUS_ID",
				"WF_PARENT_ELEMENT_ID"=>"BE.WF_PARENT_ELEMENT_ID",
				"WF_NEW"=>"BE.WF_NEW",
//				"WF_LOCK"=>"BE.WF_LOCK",
				"LOCK_STATUS"=>"if (BE.WF_DATE_LOCK is null, 'green', if(DATE_ADD(BE.WF_DATE_LOCK, interval ".$MAX_LOCK." MINUTE)<now(), 'green', if(BE.WF_LOCKED_BY=".$uid.", 'yellow', 'red')))",
				"WF_LOCKED_BY"=>"BE.WF_LOCKED_BY",
				"WF_DATE_LOCK"=>$DB->DateToCharFunction("BE.WF_DATE_LOCK"),
				"WF_COMMENTS"=>"BE.WF_COMMENTS",
				"IN_SECTIONS"=>"BE.IN_SECTIONS",
				"SHOW_COUNTER"=>"BE.SHOW_COUNTER",
				"SHOW_COUNTER_START"=>"BE.SHOW_COUNTER_START",
				"CODE"=>"BE.CODE",
				"TAGS"=>"BE.TAGS",
				"XML_ID"=>"BE.XML_ID",
				"EXTERNAL_ID"=>"BE.XML_ID",
				"TMP_ID"=>"BE.TMP_ID",
//				"WF_LAST_HISTORY_ID"=>"BE.WF_LAST_HISTORY_ID",
				"USER_NAME"=>"concat('(',U.LOGIN,') ',ifnull(U.NAME,''),' ',ifnull(U.LAST_NAME,''))",
				"LOCKED_USER_NAME"=>"concat('(',UL.LOGIN,') ',ifnull(UL.NAME,''),' ',ifnull(UL.LAST_NAME,''))",
				"CREATED_USER_NAME"=>"concat('(',UC.LOGIN,') ',ifnull(UC.NAME,''),' ',ifnull(UC.LAST_NAME,''))",
				"LANG_DIR"=>"L.DIR",
				"LID"=>"B.LID",
				"IBLOCK_TYPE_ID"=>"B.IBLOCK_TYPE_ID",
				"IBLOCK_CODE"=>"B.CODE",
				"IBLOCK_NAME"=>"B.NAME",
				"IBLOCK_EXTERNAL_ID"=>"B.XML_ID",
				"DETAIL_PAGE_URL"=>"B.DETAIL_PAGE_URL",
				"LIST_PAGE_URL"=>"B.LIST_PAGE_URL"
			);

		$bDistinct = false;

		CIBlockElement::PrepareGetList(
				$arIblockElementFields,
				$arJoinProps,
				$arFullJoins,
				$bOnlyCount,
				$bDistinct,

				$arSelectFields,
				$sSelect,
				$arAddSelectFields,

				$arFilter,
				$sWhere,
				$sSectionWhere,
				$arAddWhereFields,

				$arGroupBy,
				$sGroupBy,

				$arOrder,
				$arSqlOrder,
				$arAddOrderByFields,

				$arIBlockFilter,
				$arIBlockMultProps,
				$bJoinFlatProp,
				$arIBlockConvProps,
				$arIBlockAllProps,
				$arIBlockNumProps,
				$arIBlockLongProps
			);

		$strIBLOCKFilter = "";

		if((is_array($arIBlockFilter) && count($arIBlockFilter)>0) || ($arFilter["CHECK_PERMISSIONS"] == "Y" && !$USER->IsAdmin()))
		{
			$BlockMinPerm = false;
			if($arFilter["CHECK_PERMISSIONS"] == "Y" && !$USER->IsAdmin())
				$BlockMinPerm = (strlen($arFilter["MIN_PERMISSION"])==1 ? $arFilter["MIN_PERMISSION"] : "R");

			global $ar_IBLOCK_SITE_FILTER_CACHE;
			$sSiteFilterID = md5(serialize($arIBlockFilter)."|Perm=".$BlockMinPerm);
			if(is_set($ar_IBLOCK_SITE_FILTER_CACHE, $sSiteFilterID))
				$strIBLOCKFilter = $ar_IBLOCK_SITE_FILTER_CACHE[$sSiteFilterID];
			else
			{
				$sIBlockFilter = "";
				foreach($arIBlockFilter as $val)
					if(strlen($val)>0)
						$sIBlockFilter .= "  AND ".$val;

				$strIBLOCKFilter = "0";
				$strSql =
					"SELECT DISTINCT B.ID ".
					"FROM b_iblock B, b_iblock_site BS ".($BlockMinPerm?", b_iblock_group IBG ":"").
					"WHERE B.ID = BS.IBLOCK_ID ".
						($BlockMinPerm?
							"	AND B.ID = IBG.IBLOCK_ID ".
							"	AND IBG.GROUP_ID IN (".$USER->GetGroups().") ".
							"	AND IBG.PERMISSION>='".$BlockMinPerm."' ".
							"	AND (IBG.PERMISSION='X' OR B.ACTIVE='Y') "
						:"").
						$sIBlockFilter;

				$dbRes = $DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
				while($arRes = $dbRes->Fetch())
					$strIBLOCKFilter .= ",".$arRes["ID"];

				$ar_IBLOCK_SITE_FILTER_CACHE[$sSiteFilterID] = $strIBLOCKFilter;
			}

			if($strIBLOCKFilter!="")
				$strIBLOCKFilter = " AND B.ID IN (".$strIBLOCKFilter.") ";
		}

		//******************FROM PART********************************************
		$sFrom = "";
		foreach($arJoinProps as $propID=>$i)
		{
			if(strlen($propID)>0 && $i>0 && ($db_prop = CIBlockProperty::GetPropertyArray($propID, CIBlock::_MergeIBArrays($arFilter["IBLOCK_ID"], $arFilter["IBLOCK_CODE"]))))
			{
				if($db_prop["MULTIPLE"]=="Y")
					$bDistinct = true;
				if($db_prop["VERSION"]==2)
					$strTable = "b_iblock_element_prop_m".$db_prop["IBLOCK_ID"];
				else
					$strTable = "b_iblock_element_property";
				if($db_prop["PROPERTY_TYPE"]=="L" && $db_prop["VERSION"]==2 && $db_prop["MULTIPLE"]=="N")
					$bJoinFlatProp = $db_prop["IBLOCK_ID"];
				if(in_array($propID, $arFullJoins))
					$sFrom .= " INNER JOIN b_iblock_property FP".$i." ON FP".$i.".IBLOCK_ID=B.ID AND ".
								(IntVal($propID)>0?" FP".$i.".ID=".IntVal($propID)." ":" FP".$i.".CODE='".$DB->ForSQL($propID, 200)."' ").
								(
								$db_prop["VERSION"]==2 && $db_prop["MULTIPLE"]=="N" && $db_prop["PROPERTY_TYPE"]=="L"?
								""
								:" INNER JOIN ".$strTable." FPV".$i." ON FP".$i.".ID=FPV".$i.".IBLOCK_PROPERTY_ID ".
								"	AND FPV".$i.".IBLOCK_ELEMENT_ID=BE.ID "
								).
								($db_prop["PROPERTY_TYPE"]=="L"?
									"	INNER JOIN b_iblock_property_enum FPEN".$i." ON FP".$i.".ID = FPEN".$i.".PROPERTY_ID ".
									(
									$db_prop["VERSION"]==2 && $db_prop["MULTIPLE"]=="N"?
									"AND FPS.PROPERTY_".$db_prop["ORIG_ID"]."=FPEN".$i.".ID "
									:"AND FPV".$i.".VALUE_ENUM=FPEN".$i.".ID "
									)
								:"");
				else
					$sFrom .= " LEFT JOIN b_iblock_property FP".$i." ON FP".$i.".IBLOCK_ID=B.ID AND ".
								(IntVal($propID)>0?" FP".$i.".ID=".IntVal($propID)." ":" FP".$i.".CODE='".$DB->ForSQL($propID, 200)."' ").
								(
								$db_prop["VERSION"]==2 && $db_prop["MULTIPLE"]=="N" && $db_prop["PROPERTY_TYPE"]=="L"?
								""
								:" LEFT JOIN ".$strTable." FPV".$i." ON FP".$i.".ID=FPV".$i.".IBLOCK_PROPERTY_ID ".
								"	AND FPV".$i.".IBLOCK_ELEMENT_ID=BE.ID "
								).
								($db_prop["PROPERTY_TYPE"]=="L"?
									"	LEFT JOIN b_iblock_property_enum FPEN".$i." ON FP".$i.".ID = FPEN".$i.".PROPERTY_ID ".
									(
									$db_prop["VERSION"]==2 && $db_prop["MULTIPLE"]=="N"?
									"AND FPS.PROPERTY_".$db_prop["ORIG_ID"]."=FPEN".$i.".ID "
									:"AND FPV".$i.".VALUE_ENUM=FPEN".$i.".ID "
									)
								:"");
			}
		}
		//************************************************************************

		if(CModule::IncludeModule("catalog"))
		{
			if(count($arAddSelectFields)>0 || count($arAddWhereFields)>0 || count($arAddOrderByFields)>0)
			{
				$res_catalog = CCatalogProduct::GetQueryBuildArrays($arAddOrderByFields, $arAddWhereFields, $arAddSelectFields);
				if($sGroupBy=="")
					$sSelect .= $res_catalog["SELECT"]." ";
				$sFrom .= $res_catalog["FROM"]." ";
				$sWhere .= $res_catalog["WHERE"]." ";
				if(is_array($res_catalog["ORDER"]))
				{
					foreach($res_catalog["ORDER"] as $i=>$val)
						$arSqlOrder[$i] = $val;
				}
			}
		}

		$sOrderBy = "";
		ksort($arSqlOrder);
		reset($arSqlOrder);
		foreach($arSqlOrder as $i=>$val)
		{
			if(strlen($val)>0)
			{
				if($sOrderBy=="")
					$sOrderBy = " ORDER BY ";
				else
					$sOrderBy .= ",";

				$sOrderBy .= $val;
			}
		}

		if(strlen(trim($sSelect))<=0)
			$sSelect = "0 as NOP ";

		$bDistinct = $bDistinct || ($arFilter["INCLUDE_SUBSECTIONS"]=="Y");

		if($bDistinct)
			$sSelect = str_replace("%%_DISTINCT_%%", "DISTINCT", $sSelect);
		else
			$sSelect = str_replace("%%_DISTINCT_%%", "", $sSelect);

		$sWhere .= $sSectionWhere;
		$strSql =
			"FROM b_iblock B ".
			"	INNER JOIN b_lang L ON B.LID=L.LID ".
			"	INNER JOIN b_iblock_element BE ON BE.IBLOCK_ID = B.ID ".
			($bJoinFlatProp?
				"	INNER JOIN b_iblock_element_prop_s".$bJoinFlatProp." FPS ON FPS.IBLOCK_ELEMENT_ID = BE.ID "
				:""
			).
			($sSectionWhere!=""?
				"INNER JOIN b_iblock_section_element BSE ON BSE.IBLOCK_ELEMENT_ID = BE.ID ".
				($arFilter["INCLUDE_SUBSECTIONS"]=="Y"?
					"INNER JOIN b_iblock_section BSubS ON BSE.IBLOCK_SECTION_ID = BSubS.ID ".
					"INNER JOIN b_iblock_section BS ON (BSubS.IBLOCK_ID=BS.IBLOCK_ID ".
					"	AND BSubS.LEFT_MARGIN>=BS.LEFT_MARGIN ".
					"	AND BSubS.RIGHT_MARGIN<=BS.RIGHT_MARGIN) "
				:
					" INNER JOIN b_iblock_section BS ON BSE.IBLOCK_SECTION_ID = BS.ID "
				)
				:""
			).
				$sFrom.
			(in_array("USER_NAME", $arSelectFields)?"	LEFT JOIN b_user U ON U.ID=BE.MODIFIED_BY ":"").
			(in_array("LOCKED_USER_NAME", $arSelectFields)?"	LEFT JOIN b_user UL ON UL.ID=BE.WF_LOCKED_BY ":"").
			(in_array("CREATED_USER_NAME", $arSelectFields)?"	LEFT JOIN b_user UC ON UC.ID=BE.CREATED_BY ":"").
			"WHERE 1=1 ".
			$strIBLOCKFilter.
			($arFilter["SHOW_HISTORY"]!="Y"?
				"	AND ".
				"	( ".
				"		(BE.WF_STATUS_ID=1 AND BE.WF_PARENT_ELEMENT_ID IS NULL) ".
						($arFilter["SHOW_NEW"]=="Y"?" OR BE.WF_NEW='Y' ":"").
				"	)"
			:"").
			$sWhere.
			$sGroupBy;

		if($bOnlyCount)
		{
			$res = $DB->Query("SELECT ".$sSelect.$strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
			$res = $res->Fetch();
			return $res["CNT"];
		}

		if(is_array($arNavStartParams) && IntVal($arNavStartParams["nTopCount"])<=0)
		{
			if($sGroupBy=="")
			{
				$res_cnt = $DB->Query("SELECT COUNT(".($bDistinct? "DISTINCT BE.ID": "'x'").") as C ".$strSql);
				$res_cnt = $res_cnt->Fetch();
				$cnt = $res_cnt["C"];
			}
			else
			{
				$res_cnt = $DB->Query("SELECT 'x' ".$strSql);
				$cnt = $res_cnt->SelectedRowsCount();
			}

			$strSql = "SELECT ".$sSelect.$strSql.$sOrderBy;
			$res = new CDBResult();

			$res->NavQuery($strSql, $cnt, $arNavStartParams);
			$res = new CIBlockResult($res);
			$res->arIBlockMultProps = $arIBlockMultProps;
			$res->arIBlockConvProps = $arIBlockConvProps;
			$res->arIBlockAllProps  = $arIBlockAllProps;
			$res->arIBlockNumProps = $arIBlockNumProps;
			$res->arIBlockLongProps = $arIBlockLongProps;
			//echo $strSql;
			return $res;
		}
		else
		{
			$strSql = "SELECT ".$sSelect.$strSql.$sOrderBy;
			if(is_array($arNavStartParams) && IntVal($arNavStartParams["nTopCount"])>0)
				$strSql = $strSql." LIMIT ".$arNavStartParams["nTopCount"];
			//echo $strSql."<br><br>";
			$res = $DB->Query($strSql);
			$res = new CIBlockResult($res);
			$res->arIBlockMultProps = $arIBlockMultProps;
			$res->arIBlockConvProps = $arIBlockConvProps;
			$res->arIBlockAllProps  = $arIBlockAllProps;
			$res->arIBlockNumProps = $arIBlockNumProps;
			$res->arIBlockLongProps = $arIBlockLongProps;
		}
		return $res;
	}

	///////////////////////////////////////////////////////////////////
	// Add function
	///////////////////////////////////////////////////////////////////
	function Add($arFields, $bWorkFlow=false, $bUpdateSearch=true)
	{
		global $DB, $USER;

		$bWorkFlow = $bWorkFlow && (CIBlock::GetArrayByID($arFields["IBLOCK_ID"], "WORKFLOW") != "N");

		if(is_set($arFields, "IBLOCK_SECTION_ID") && intval($arFields["IBLOCK_SECTION_ID"])<=0)
			unset($arFields["IBLOCK_SECTION_ID"]);

		if(is_set($arFields, "IBLOCK_SECTION_ID") && !is_set($arFields, "IBLOCK_SECTION"))
			$arFields["IBLOCK_SECTION"] = Array($arFields["IBLOCK_SECTION_ID"]);

		if(is_set($arFields, "PREVIEW_PICTURE"))
		{
			if(strlen($arFields["PREVIEW_PICTURE"]["name"])<=0 && strlen($arFields["PREVIEW_PICTURE"]["del"])<=0)
				unset($arFields["PREVIEW_PICTURE"]);
			else
				$arFields["PREVIEW_PICTURE"]["MODULE_ID"] = "iblock";
		}

		if(is_set($arFields, "DETAIL_PICTURE"))
		{
			if(strlen($arFields["DETAIL_PICTURE"]["name"])<=0 && strlen($arFields["DETAIL_PICTURE"]["del"])<=0)
				unset($arFields["DETAIL_PICTURE"]);
			else
				$arFields["DETAIL_PICTURE"]["MODULE_ID"] = "iblock";
		}

		if(is_set($arFields, "ACTIVE") && $arFields["ACTIVE"]!="Y")
			$arFields["ACTIVE"]="N";

		if(is_set($arFields, "PREVIEW_TEXT_TYPE") && $arFields["PREVIEW_TEXT_TYPE"]!="html")
			$arFields["PREVIEW_TEXT_TYPE"]="text";

		if(is_set($arFields, "DETAIL_TEXT_TYPE") && $arFields["DETAIL_TEXT_TYPE"]!="html")
			$arFields["DETAIL_TEXT_TYPE"]="text";

		if(is_set($arFields, "DATE_ACTIVE_FROM"))
			$arFields["ACTIVE_FROM"] = $arFields["DATE_ACTIVE_FROM"];
		if(is_set($arFields, "DATE_ACTIVE_TO"))
			$arFields["ACTIVE_TO"] = $arFields["DATE_ACTIVE_TO"];
		if(is_set($arFields, "EXTERNAL_ID"))
			$arFields["XML_ID"] = $arFields["EXTERNAL_ID"];

		if($bWorkFlow)
		{
			$arFields["WF"] = "Y";
			if($arFields["WF_STATUS_ID"]!=1)
				$arFields["WF_NEW"] = "Y";
			else
				$arFields["WF_NEW"] = "";
		}

		$arFields["SEARCHABLE_CONTENT"] =
			ToUpper(
				$arFields["NAME"]."\r\n".
				($arFields["PREVIEW_TEXT_TYPE"]=="html" ?
					HTMLToTxt($arFields["PREVIEW_TEXT"]) :
					$arFields["PREVIEW_TEXT"]
				)."\r\n".
				($arFields["DETAIL_TEXT_TYPE"]=="html" ?
					HTMLToTxt($arFields["DETAIL_TEXT"]) :
					$arFields["DETAIL_TEXT"]
				)
			);

		if(!$this->CheckFields(&$arFields))
		{
			$Result = false;
			$arFields["RESULT_MESSAGE"] = &$this->LAST_ERROR;
		}
		else
		{
			$SAVED_PREVIEW_PICTURE = $arFields["PREVIEW_PICTURE"];
			CFile::SaveForDB($arFields, "PREVIEW_PICTURE", "iblock");
			$SAVED_DETAIL_PICTURE = $arFields["DETAIL_PICTURE"];
			CFile::SaveForDB($arFields, "DETAIL_PICTURE", "iblock");

			unset($arFields["ID"]);
			$arInsert = $DB->PrepareInsert("b_iblock_element", $arFields, "iblock");

			if(array_key_exists("PREVIEW_PICTURE", $arFields))
				$arFields["PREVIEW_PICTURE"] = $SAVED_PREVIEW_PICTURE;
			if(array_key_exists("DETAIL_PICTURE", $arFields))
				$arFields["DETAIL_PICTURE"] = $SAVED_DETAIL_PICTURE;

			$user_id = (is_object($USER)?IntVal($USER->GetID()):0);
			$strSql =
				"INSERT INTO b_iblock_element(".
					(!is_set($arFields, "TIMESTAMP_X")?"TIMESTAMP_X, ":"").
					(!is_set($arFields, "DATE_CREATE")?"DATE_CREATE, ":"").
					($user_id>0 && !is_set($arFields, "CREATED_BY")?"CREATED_BY, ":"").
					$arInsert[0].") ".
				"VALUES(".
					(!is_set($arFields, "TIMESTAMP_X")?"now(), ":"").
					(!is_set($arFields, "DATE_CREATE")?"now(), ":"").
					($user_id>0 && !is_set($arFields, "CREATED_BY")?$user_id.", ":"").
					$arInsert[1].")";

			$DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
			$ID = $DB->LastID();

			if(CIBlockElement::GetIBVersion($arFields["IBLOCK_ID"])==2)
				$DB->Query("INSERT INTO b_iblock_element_prop_s".$arFields["IBLOCK_ID"]."(IBLOCK_ELEMENT_ID)VALUES(".$ID.")");

			if(strlen($arFields["XML_ID"])<=0)
			{
				$arFields["XML_ID"] = $ID;
				$DB->Query("UPDATE b_iblock_element SET XML_ID = ".$ID." WHERE ID = ".$ID);
			}

			if(is_set($arFields, "PROPERTY_VALUES"))
				CIBlockElement::SetPropertyValues($ID, $arFields["IBLOCK_ID"], $arFields["PROPERTY_VALUES"]);

			if(is_set($arFields, "IBLOCK_SECTION"))
			{
				CIBlockElement::SetElementSection($ID, $arFields["IBLOCK_SECTION"], true);
			}

			if($bUpdateSearch)
				CIBlockElement::UpdateSearch($ID);

			if($bWorkFlow && intval($arFields["WF_PARENT_ELEMENT_ID"])<=0)
			{
				// It is completly new element - so make it copy
				unset($arFields["WF_NEW"]);
				$arFields["WF_PARENT_ELEMENT_ID"] = $ID;
				$WF_ID = $this->Add($arFields);
				CIBlockElement::WF_SetMove($WF_ID);
			}

			$Result = $ID;
			$arFields["ID"] = &$ID;

			/************* QUOTA *************/
			$_SESSION["SESS_RECOUNT_DB"] = "Y";
			/************* QUOTA *************/
		}

		$arFields["RESULT"] = &$Result;

		$events = GetModuleEvents("iblock", "OnAfterIBlockElementAdd");
		while ($arEvent = $events->Fetch())
			ExecuteModuleEvent($arEvent, &$arFields);

		return $Result;
	}

	///////////////////////////////////////////////////////////////////
	// Update lement function
	///////////////////////////////////////////////////////////////////
	function Update($ID, $arFields, $bWorkFlow=false, $bUpdateSearch=true)
	{
		global $DB;

		$ID = intval($ID);

		$db_element = CIBlockElement::GetList(array(), array("ID"=>$ID, "SHOW_HISTORY"=>"Y"), false, false,
			array(
				"ID",
				"TIMESTAMP_X",
				"MODIFIED_BY",
				"DATE_CREATE",
				"CREATED_BY",
				"IBLOCK_ID",
				"ACTIVE",
				"ACTIVE_FROM",
				"ACTIVE_TO",
				"SORT",
				"NAME",
				"PREVIEW_PICTURE",
				"PREVIEW_TEXT",
				"PREVIEW_TEXT_TYPE",
				"DETAIL_PICTURE",
				"DETAIL_TEXT",
				"DETAIL_TEXT_TYPE",
				"WF_STATUS_ID",
				"WF_PARENT_ELEMENT_ID",
				"WF_NEW",
				"WF_COMMENTS",
				"IN_SECTIONS",
				"CODE",
				"TAGS",
				"XML_ID",
				"TMP_ID",
			)
		);
		if(!($ar_element = $db_element->Fetch()))
			return false;

		$bWorkFlow = $bWorkFlow && (CIBlock::GetArrayByID($ar_element["IBLOCK_ID"], "WORKFLOW") != "N") && CModule::IncludeModule("workflow");

		//$arCalcValues = Array("WF_LOCKED_BY", "SHOW_COUNTER", "DATE_ACTIVE_FROM", "DATE_ACTIVE_TO", "SHOW_COUNTER_START", "EXTERNAL_ID", "WF_DATE_LOCK", "IBLOCK_SECTION_ID");
		//for($i=0; $i<count($arCalcValues );$i++)
		//	unset($ar_element[$arCalcValues[$i]]);

		$ar_wf_element = $ar_element;

		$LAST_ID = 0;
		if($bWorkFlow)
		{
			$LAST_ID = CIBlockElement::WF_GetLast($ID);
			if($LAST_ID!=$ID)
			{
				$db_element = CIBlockElement::GetByID($LAST_ID);
				if(!($ar_wf_element = $db_element->Fetch()))
					return false;
			}

			CIBlockElement::__InitFile($ar_wf_element["PREVIEW_PICTURE"], $arFields, 'PREVIEW_PICTURE');
			CIBlockElement::__InitFile($ar_wf_element["DETAIL_PICTURE"], $arFields, 'DETAIL_PICTURE');

			$bFieldProps = array();
			if(array_key_exists("PROPERTY_VALUES", $arFields) && is_array($arFields["PROPERTY_VALUES"]))
				foreach($arFields["PROPERTY_VALUES"] as $k=>$v)
					$bFieldProps[$k]=true;

			$arFieldProps = &$arFields['PROPERTY_VALUES'];
			$props = CIBlockElement::GetProperty($ar_element["IBLOCK_ID"], $ar_wf_element["ID"]);
			while($arProp = $props->Fetch())
			{
				if($arProp['PROPERTY_TYPE']=='F' && intval($arProp['PROPERTY_VALUE_ID'])>0)
				{
					$pr_id = $arProp['ID'];
					if(strlen($arProp["CODE"])>0 && is_set($arFieldProps, $arProp["CODE"]))
						$pr_id = $arProp["CODE"];
					if(is_set($arFieldProps[$pr_id], $arProp['PROPERTY_VALUE_ID']))
					{
						if(strlen($arFieldProps[$pr_id][$arProp['PROPERTY_VALUE_ID']]['name'])<=0
							&& $arFieldProps[$pr_id][$arProp['PROPERTY_VALUE_ID']]['del']!="Y"
							&& strlen($arFieldProps[$pr_id][$arProp['PROPERTY_VALUE_ID']]['VALUE']['name'])<=0
							&& $arFieldProps[$pr_id][$arProp['PROPERTY_VALUE_ID']]['VALUE']['del']!="Y"
							)
						{
							$p = Array("VALUE"=>CFile::MakeFileArray($arProp['VALUE']));
							if($p["VALUE"])
								$p["VALUE"]["OLD_VALUE"] = true;
							$p['DESCRIPTION'] = $arProp['DESCRIPTION'];
							if(is_set($arFieldProps[$pr_id][$arProp['PROPERTY_VALUE_ID']], 'DESCRIPTION'))
								$p['DESCRIPTION'] = $arFieldProps[$pr_id][$arProp['PROPERTY_VALUE_ID']]['DESCRIPTION'];
							$arFieldProps[$pr_id][$arProp['PROPERTY_VALUE_ID']] = $p;
						}
					}
					else
						$arFieldProps[$pr_id][$arProp['PROPERTY_VALUE_ID']] = CFile::MakeFileArray($arProp['VALUE']);

					continue;
				}

				if (
					intval($arProp['PROPERTY_VALUE_ID'])<=0
					|| array_key_exists($arProp["ID"], $bFieldProps)
					|| (
						strlen($arProp["CODE"])>0
						&& array_key_exists($arProp["CODE"], $bFieldProps)
					)
				)
					continue;

				$arFieldProps[$arProp["ID"]][$arProp['PROPERTY_VALUE_ID']] = array("VALUE"=>$arProp['VALUE'],"DESCRIPTION"=>$arProp["DESCRIPTION"]);
			}

			unset($ar_wf_element["DATE_ACTIVE_FROM"]);
			unset($ar_wf_element["DATE_ACTIVE_TO"]);
			unset($ar_wf_element["EXTERNAL_ID"]);
			unset($ar_wf_element["TIMESTAMP_X"]);
			unset($ar_wf_element["ID"]);

			$arFields = $arFields + $ar_wf_element;
		}

		$arFields["WF"] = ($bWorkFlow?"Y":"N");

		if(is_set($arFields, "ACTIVE") && $arFields["ACTIVE"]!="Y")
			$arFields["ACTIVE"]="N";

		if(is_set($arFields, "PREVIEW_TEXT_TYPE") && $arFields["PREVIEW_TEXT_TYPE"]!="html")
			$arFields["PREVIEW_TEXT_TYPE"]="text";

		if(is_set($arFields, "DETAIL_TEXT_TYPE") && $arFields["DETAIL_TEXT_TYPE"]!="html")
			$arFields["DETAIL_TEXT_TYPE"]="text";

		if(is_set($arFields, "PREVIEW_PICTURE") && strlen($arFields["PREVIEW_PICTURE"]["name"])<=0 && strlen($arFields["PREVIEW_PICTURE"]["del"])<=0 && !is_set($arFields["PREVIEW_PICTURE"], "description"))
			unset($arFields["PREVIEW_PICTURE"]);

		if(is_set($arFields, "DETAIL_PICTURE") && strlen($arFields["DETAIL_PICTURE"]["name"])<=0 && strlen($arFields["DETAIL_PICTURE"]["del"])<=0 && !is_set($arFields["DETAIL_PICTURE"], "description"))
			unset($arFields["DETAIL_PICTURE"]);

		if(is_set($arFields, "DATE_ACTIVE_FROM"))
			$arFields["ACTIVE_FROM"] = $arFields["DATE_ACTIVE_FROM"];
		if(is_set($arFields, "DATE_ACTIVE_TO"))
			$arFields["ACTIVE_TO"] = $arFields["DATE_ACTIVE_TO"];
		if(is_set($arFields, "EXTERNAL_ID"))
			$arFields["XML_ID"] = $arFields["EXTERNAL_ID"];

		if(is_set($arFields, "PREVIEW_PICTURE"))
			$arFields["PREVIEW_PICTURE"]["MODULE_ID"] = "iblock";

		if(is_set($arFields, "DETAIL_PICTURE"))
			$arFields["DETAIL_PICTURE"]["MODULE_ID"] = "iblock";
		if(!$bWorkFlow && is_set($arFields, "PREVIEW_PICTURE"))
			$arFields["PREVIEW_PICTURE"]["old_file"] = $ar_wf_element["PREVIEW_PICTURE"];

		if(!$bWorkFlow && is_set($arFields, "DETAIL_PICTURE"))
			$arFields["DETAIL_PICTURE"]["old_file"] = $ar_wf_element["DETAIL_PICTURE"];

		$arFields["WF_NEW"] = false;

		$arFields["SEARCHABLE_CONTENT"] =
			ToUpper(
				(is_set($arFields, "NAME")?$arFields["NAME"]:$ar_wf_element["NAME"])."\r\n".
				((is_set($arFields, "PREVIEW_TEXT_TYPE")?$arFields["PREVIEW_TEXT_TYPE"]:$ar_wf_element["PREVIEW_TEXT_TYPE"])=="html" ?
					HTMLToTxt((is_set($arFields, "PREVIEW_TEXT")?$arFields["PREVIEW_TEXT"]:$ar_wf_element["PREVIEW_TEXT"])) :
					(is_set($arFields, "PREVIEW_TEXT")?$arFields["PREVIEW_TEXT"]:$ar_wf_element["PREVIEW_TEXT"])
				)."\r\n".
				((is_set($arFields, "DETAIL_TEXT_TYPE")?$arFields["DETAIL_TEXT_TYPE"]:$ar_wf_element["DETAIL_TEXT_TYPE"])=="html" ?
					HTMLToTxt((is_set($arFields, "DETAIL_TEXT")?$arFields["DETAIL_TEXT"]:$ar_wf_element["DETAIL_TEXT"])) :
					(is_set($arFields, "DETAIL_TEXT")?$arFields["DETAIL_TEXT"]:$ar_wf_element["DETAIL_TEXT"])
				)
			);

		if(is_set($arFields["IBLOCK_SECTION_ID"]) && !is_set($arFields, "IBLOCK_SECTION"))
			$arFields["IBLOCK_SECTION"] = Array($arFields["IBLOCK_SECTION_ID"]);

		$arFields["IBLOCK_ID"] = $ar_element["IBLOCK_ID"];

		if(!$this->CheckFields(&$arFields, $ID))
		{
			$Result = false;
			$arFields["RESULT_MESSAGE"] = &$this->LAST_ERROR;
		}
		else
		{
			unset($arFields["ID"]);

			// edit was done in workflow mode
			if($bWorkFlow)
			{
				$arFields["WF_PARENT_ELEMENT_ID"] = $ID;
				$NID = $this->Add($arFields);

				if($NID>0)
				{
					if($arFields["WF_STATUS_ID"]==1)
					{
						$DB->Query("UPDATE b_iblock_element SET TIMESTAMP_X=TIMESTAMP_X, WF_NEW=null WHERE ID=".$ID);
						$DB->Query("UPDATE b_iblock_element SET TIMESTAMP_X=TIMESTAMP_X, WF_NEW=null WHERE WF_PARENT_ELEMENT_ID=".$ID);
						$ar_wf_element["WF_NEW"] = false;
					}

					CIBlockElement::WF_SetMove($NID, $LAST_ID);

					if($ar_element["WF_STATUS_ID"] != 1
						&& $ar_wf_element["WF_STATUS_ID"] != $arFields["WF_STATUS_ID"]
						&& $arFields["WF_STATUS_ID"] != 1
						)
					{
						$DB->Query("UPDATE b_iblock_element SET TIMESTAMP_X=TIMESTAMP_X, WF_STATUS_ID=".$arFields["WF_STATUS_ID"]." WHERE ID=".$ID);
					}
				}
				CIBlockElement::WF_CleanUpHistoryCopies($ID);

				//element was not published, so keep original
				if((is_set($arFields, "WF_STATUS_ID") && $arFields["WF_STATUS_ID"]!=1) || (!is_set($arFields, "WF_STATUS_ID") && $ar_wf_element["WF_STATUS_ID"]!=1))
					return true;

				$arFields['WF_PARENT_ELEMENT_ID'] = false;
				if(intval($ar_wf_element['PREVIEW_PICTURE'])<=0
					&& intval($ar_element['PREVIEW_PICTURE'])>0
					&& strlen($arFields['PREVIEW_PICTURE']['name'])<=0
				)
				{
					$arFields["PREVIEW_PICTURE"]["old_file"] = $ar_element["PREVIEW_PICTURE"];
					$arFields["PREVIEW_PICTURE"]["MODULE_ID"] = "iblock";
					$arFields['PREVIEW_PICTURE']['del'] = 'Y';
				}
				$arFields["PREVIEW_PICTURE"]["old_file"] = $ar_element["PREVIEW_PICTURE"];

				if(intval($ar_wf_element['DETAIL_PICTURE'])<=0
					&& intval($ar_element['DETAIL_PICTURE'])>0
					&& strlen($arFields['DETAIL_PICTURE']['name'])<=0
				)
				{
					$arFields["DETAIL_PICTURE"]["old_file"] = $ar_element["DETAIL_PICTURE"];
					$arFields["DETAIL_PICTURE"]["MODULE_ID"] = "iblock";
					$arFields['DETAIL_PICTURE']['del'] = 'Y';
				}
				$arFields["DETAIL_PICTURE"]["old_file"] = $ar_element["DETAIL_PICTURE"];

				//if($ar_wf_element["WF_STATUS_ID"] != 1 && $arFields["WF_STATUS_ID"] == 1)
				{
					$db_img = CIBlockElement::GetProperty($ar_element["IBLOCK_ID"], $ID,"sort","asc",array("PROPERTY_TYPE"=>"F"));
					while($ar_img = $db_img->Fetch())
					{
						$prop_img_id = $ar_img["ID"];
						if(is_set($arFieldProps, $ar_img["CODE"]))
							$prop_img_id = $ar_img["CODE"];
						if(!is_set($arFieldProps[$prop_img_id], $ar_img["PROPERTY_VALUE_ID"]))
							$arFieldProps[$prop_img_id][$ar_img["PROPERTY_VALUE_ID"]] = Array("del"=>"Y", "name"=>"", "tmp_name"=>"");
					}
				}
			}

			UnSet($arFields["IBLOCK_ID"]);
			UnSet($arFields["WF_NEW"]);
			UnSet($arFields["IBLOCK_SECTION_ID"]);

			$bTimeStampNA = false;
			if(is_set($arFields, "TIMESTAMP_X") && ($arFields["TIMESTAMP_X"] === NULL || $arFields["TIMESTAMP_X"]===false))
			{
				$bTimeStampNA = true;
				UnSet($arFields["TIMESTAMP_X"]);
			}

			$arForDelete = Array();
			if($bWorkFlow && $arFields["PREVIEW_PICTURE"]["OLD_VALUE"])
			{
				$arForDelete[] = $ar_wf_element["PREVIEW_PICTURE"];
				$arFields["PREVIEW_PICTURE"]["old_file"] = "";
			}

			if($bWorkFlow && $arFields["DETAIL_PICTURE"]["OLD_VALUE"])
			{
				$arForDelete[] = $ar_wf_element["DETAIL_PICTURE"];
				$arFields["DETAIL_PICTURE"]["old_file"] = "";
			}

			$SAVED_PREVIEW_PICTURE = $arFields["PREVIEW_PICTURE"];
			CFile::SaveForDB($arFields, "PREVIEW_PICTURE", "iblock");
			$SAVED_DETAIL_PICTURE = $arFields["DETAIL_PICTURE"];
			CFile::SaveForDB($arFields, "DETAIL_PICTURE", "iblock");

			$strUpdate = $DB->PrepareUpdate("b_iblock_element", $arFields, "iblock");

			if(array_key_exists("PREVIEW_PICTURE", $arFields))
				$arFields["PREVIEW_PICTURE"] = $SAVED_PREVIEW_PICTURE;
			if(array_key_exists("DETAIL_PICTURE", $arFields))
				$arFields["DETAIL_PICTURE"] = $SAVED_DETAIL_PICTURE;

			if(strlen($strUpdate)>0)
				$strUpdate .= ", ";

			for($i=0; $i<count($arForDelete); $i++)
				CFile::Delete($arForDelete[$i]);

			$strSql = "UPDATE b_iblock_element SET ".$strUpdate.($bTimeStampNA?"TIMESTAMP_X=TIMESTAMP_X":"TIMESTAMP_X=now()")." WHERE ID=".$ID;
			$DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);

			if(is_set($arFields, "PROPERTY_VALUES"))
				CIBlockElement::SetPropertyValues($ID, $ar_element["IBLOCK_ID"], $arFields["PROPERTY_VALUES"]);

			if(is_set($arFields, "IBLOCK_SECTION"))
				CIBlockElement::SetElementSection($ID, $arFields["IBLOCK_SECTION"]);

			if($bUpdateSearch)
				CIBlockElement::UpdateSearch($ID, true);

			$Result = true;

			/************* QUOTA *************/
			$_SESSION["SESS_RECOUNT_DB"] = "Y";
			/************* QUOTA *************/
		}

		$arFields["ID"] = $ID;
		$arFields["IBLOCK_ID"] = $ar_element["IBLOCK_ID"];
		$arFields["RESULT"] = &$Result;


		$events = GetModuleEvents("iblock", "OnAfterIBlockElementUpdate");
		while ($arEvent = $events->Fetch())
			ExecuteModuleEvent($arEvent, &$arFields);

		return $Result;
	}

	function SetPropertyValues($ELEMENT_ID, $IBLOCK_ID, $PROPERTY_VALUES, $PROPERTY_CODE = false)
	{
		global $DB;
		$ELEMENT_ID = intVal($ELEMENT_ID);
		if(!is_array($PROPERTY_VALUES))
			$PROPERTY_VALUES = Array($PROPERTY_VALUES);

		$arFilter = Array();
		if($PROPERTY_CODE!==false)
		{
			if(IntVal($PROPERTY_CODE)>0)
				$arFilter["ID"] = IntVal($PROPERTY_CODE);
			else
				$arFilter["CODE"] = $PROPERTY_CODE;
		}
		else
			$arFilter = Array("ACTIVE"=>"Y");

		$uniq_flt = md5(serialize($arFilter));
		global $BX_IBLOCK_PROP_CACHE;
		if(!is_set($BX_IBLOCK_PROP_CACHE, $IBLOCK_ID))
			$BX_IBLOCK_PROP_CACHE[$IBLOCK_ID] = Array();
		if(is_set($BX_IBLOCK_PROP_CACHE[$IBLOCK_ID], $uniq_flt))
			$ar_prop = &$BX_IBLOCK_PROP_CACHE[$IBLOCK_ID][$uniq_flt];
		else
		{
			$db_prop = CIBlock::GetProperties($IBLOCK_ID, Array(), $arFilter);
			$ar_prop = Array();
			while($prop = $db_prop->Fetch())
				$ar_prop[] = $prop;

			$BX_IBLOCK_PROP_CACHE[$IBLOCK_ID][$uniq_flt] = &$ar_prop;
		}
		Reset($ar_prop);

		$bRecalcSections = false;
		$arPROP_ID = array_keys($PROPERTY_VALUES);

		$cacheValues=false;
		if(count($ar_prop)>1)
		{
			$cacheValues = Array();
			$strSql =
				"SELECT ep.ID, ep.VALUE, ep.IBLOCK_PROPERTY_ID ".
				"FROM b_iblock_element_property ep, b_iblock_property p ".
				"WHERE ep.IBLOCK_ELEMENT_ID=".$ELEMENT_ID.
				"	AND ep.IBLOCK_PROPERTY_ID = p.ID ".
				"	AND p.PROPERTY_TYPE <> 'L' ".
				"	AND p.PROPERTY_TYPE <> 'G' ";

			$db_res = $DB->Query($strSql);
			while($res = $db_res->Fetch())
			{
				if(!isset($cacheValues[$res["IBLOCK_PROPERTY_ID"]]))
					$cacheValues[$res["IBLOCK_PROPERTY_ID"]] = Array();
				$cacheValues[$res["IBLOCK_PROPERTY_ID"]][] = $res;
			}
		}

		$ids = "0";

		foreach($ar_prop as $prop)
		{
			if($PROPERTY_CODE)
				$PROP = $PROPERTY_VALUES;
			else
			{
				if(strlen($prop["CODE"])>0 && in_array($prop["CODE"], $arPROP_ID, TRUE))
					$PROP = $PROPERTY_VALUES[$prop["CODE"]];
				else
					$PROP = $PROPERTY_VALUES[$prop["ID"]];
			}
			if($prop["PROPERTY_TYPE"]=="F")
			{
				if(!is_array($PROP) || (is_array($PROP) && is_set($PROP, "tmp_name"))  || (count($PROP)==2 && is_set($PROP, "VALUE") && is_set($PROP, "DESCRIPTION")))
					$PROP = Array($PROP);
			}
			elseif(!is_array($PROP) || (count($PROP)==2 && is_set($PROP, "VALUE") && is_set($PROP, "DESCRIPTION")))
				$PROP = Array($PROP);

			if($prop["USER_TYPE"]!="")
			{
				$arUserType = CIBlockProperty::GetUserType($prop["USER_TYPE"]);
				if(array_key_exists("ConvertToDB", $arUserType))
				{
					foreach($PROP as $key=>$value)
					{
						if(!is_array($value))
							$value=array("VALUE"=>$value);
						elseif(!array_key_exists("VALUE", $value))
							$value=array("VALUE"=>$value);
						$PROP[$key] = call_user_func_array($arUserType["ConvertToDB"], array($prop, $value));
					}
				}
			}

			if($prop["VERSION"]==2)
			{
				if($prop["MULTIPLE"]=="Y")
					$strTable = "b_iblock_element_prop_m".$prop["IBLOCK_ID"];
				else
					$strTable = "b_iblock_element_prop_s".$prop["IBLOCK_ID"];
			}
			else
				$strTable = "b_iblock_element_property";

			if($prop["PROPERTY_TYPE"]=="L")
			{
				$DB->Query(CIBLockElement::DeletePropertySQL($prop, $ELEMENT_ID), false, "File: ".__FILE__."<br>Line: ".__LINE__);
				if($prop["VERSION"]==2 && $prop["MULTIPLE"]=="Y")
				{
					$strSql = "
						UPDATE	b_iblock_element_prop_s".$prop["IBLOCK_ID"]."
						SET	PROPERTY_".$prop["ID"]."=NULL, DESCRIPTION_".$prop["ID"]."=NULL
						WHERE	IBLOCK_ELEMENT_ID=".$ELEMENT_ID."
					";
					$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
				}
				$ids = "0";
				foreach($PROP as $key=>$value)
				{
					if(is_array($value))
						$value = $value["VALUE"];

					if(IntVal($value)<=0) continue;
					$ids .= ",".IntVal($value);
					if($prop["MULTIPLE"]!="Y") break;
				}

				if($prop["VERSION"]==2 && $prop["MULTIPLE"]=="N")
				{
					$strSql = "
						UPDATE
							b_iblock_element_prop_s".$prop["IBLOCK_ID"]." E
							,b_iblock_property P
							,b_iblock_property_enum PEN
						SET
							E.PROPERTY_".$prop["ID"]."=PEN.ID
						WHERE
							E.IBLOCK_ELEMENT_ID=".$ELEMENT_ID."
							AND P.ID=".$prop["ID"]."
							AND P.ID=PEN.PROPERTY_ID
							AND PEN.ID IN (".$ids.")
					";
				}
				else
				{
					$strSql = "
						INSERT INTO ".$strTable."
						(IBLOCK_ELEMENT_ID, IBLOCK_PROPERTY_ID, VALUE, VALUE_ENUM)
						SELECT ".$ELEMENT_ID.", P.ID, PEN.ID, PEN.ID
						FROM
							b_iblock_property P
							,b_iblock_property_enum PEN
						WHERE
							P.ID=".$prop["ID"]."
							AND P.ID=PEN.PROPERTY_ID
							AND PEN.ID IN (".$ids.")
					";
				}
				$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			}
			elseif($prop["PROPERTY_TYPE"]=="G")
			{
				$bRecalcSections = true;
				$DB->Query(CIBLockElement::DeletePropertySQL($prop, $ELEMENT_ID), false, "File: ".__FILE__."<br>Line: ".__LINE__);
				if($prop["VERSION"]==2 && $prop["MULTIPLE"]=="Y")
				{
					$strSql = "
						UPDATE	b_iblock_element_prop_s".$prop["IBLOCK_ID"]."
						SET	PROPERTY_".$prop["ID"]."=NULL, DESCRIPTION_".$prop["ID"]."=NULL
						WHERE	IBLOCK_ELEMENT_ID=".$ELEMENT_ID."
					";
					$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
				}
				$DB->Query("DELETE FROM b_iblock_section_element WHERE ADDITIONAL_PROPERTY_ID=".$prop["ID"]." AND IBLOCK_ELEMENT_ID=".$ELEMENT_ID, false, "File: ".__FILE__."<br>Line: ".__LINE__);
				$ids = "0";
				foreach($PROP as $key=>$value)
				{
					if(is_array($value))
						$value = $value["VALUE"];

					if(IntVal($value)<=0) continue;
					$ids .= ",".IntVal($value);
					if($prop["MULTIPLE"]!="Y") break;
				}

				if($prop["VERSION"]==2 && $prop["MULTIPLE"]=="N")
				{
					$strSql = "
						UPDATE
							b_iblock_element_prop_s".$prop["IBLOCK_ID"]." E
							,b_iblock_property P
							,b_iblock_section S
						SET
							E.PROPERTY_".$prop["ID"]."=S.ID
							,DESCRIPTION_".$prop["ID"]."=null
						WHERE
							E.IBLOCK_ELEMENT_ID=".$ELEMENT_ID."
							AND P.ID=".$prop["ID"]."
							AND S.IBLOCK_ID = P.LINK_IBLOCK_ID
							AND S.ID IN (".$ids.")
					";
				}
				else
				{
					$strSql = "
						INSERT INTO ".$strTable."
						(IBLOCK_ELEMENT_ID, IBLOCK_PROPERTY_ID, VALUE, VALUE_NUM)
						SELECT ".$ELEMENT_ID.", P.ID, S.ID, S.ID
						FROM
							b_iblock_property P
							,b_iblock_section S
						WHERE
							P.ID=".$prop["ID"]."
							AND S.IBLOCK_ID = P.LINK_IBLOCK_ID
							AND S.ID IN (".$ids.")
					";
				}
				$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
				$DB->Query(
					"INSERT INTO b_iblock_section_element(IBLOCK_ELEMENT_ID, IBLOCK_SECTION_ID, ADDITIONAL_PROPERTY_ID) ".
					"SELECT ".$ELEMENT_ID.", S.ID, P.ID ".
					"FROM b_iblock_property P, b_iblock_section S ".
					"WHERE P.ID=".$prop["ID"]." ".
					"	AND S.IBLOCK_ID = P.LINK_IBLOCK_ID ".
					"	AND S.ID IN (".$ids.") "
					, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			}
			else
			{
				$ids = "0";
				$arV = Array();
				if($cacheValues===false || $prop["VERSION"]==2)
				{
					if($prop["VERSION"]==2 && $prop["MULTIPLE"]=="N")
					{
						$strSql = "
							SELECT	concat(IBLOCK_ELEMENT_ID,':','".$prop["ID"]."') ID, PROPERTY_".$prop["ID"]." VALUE
							FROM	b_iblock_element_prop_s".$prop["IBLOCK_ID"]."
							WHERE	IBLOCK_ELEMENT_ID=".$ELEMENT_ID;
					}
					else
					{
						$strSql = "
							SELECT	ID, VALUE
							FROM	".$strTable."
							WHERE	IBLOCK_ELEMENT_ID=".$ELEMENT_ID."
								AND IBLOCK_PROPERTY_ID=".$prop["ID"];
					}
					$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
					while($res = $db_res->Fetch())
						$arV[] = $res;
				}
				elseif(is_set($cacheValues, $prop["ID"]))
					$arV = $cacheValues[$prop["ID"]];

				$arWas = Array();
				for($i=0; $i<count($arV); $i++)
				{
					$res = $arV[$i];
					$val = $PROP[$res["ID"]];
					if(is_array($val) && !is_set($val, "tmp_name"))
					{
						$val_desc = $val["DESCRIPTION"];
						$val = $val["VALUE"];
					}
					else
						$val_desc = false;

					if($prop["PROPERTY_TYPE"]=="E")
					{
						if(in_array($val, $arWas))
							$val = "";
						else
							$arWas[] = $val;
					}

					if($prop["PROPERTY_TYPE"]=="S" || $prop["PROPERTY_TYPE"]=="N" || $prop["PROPERTY_TYPE"]=="E")
					{
						if(strlen($val)<=0)
						{
							if($prop["VERSION"]==2 && $prop["MULTIPLE"]=="N")
							{
								$strSql = "
									UPDATE	b_iblock_element_prop_s".$prop["IBLOCK_ID"]."
									SET
										PROPERTY_".$prop["ID"]."=null
										,DESCRIPTION_".$prop["ID"]."=null
									WHERE	IBLOCK_ELEMENT_ID=".$ELEMENT_ID;
							}
							else
							{
								$strSql = "DELETE FROM ".$strTable." WHERE ID=".$res["ID"];
							}
							$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
							if($prop["VERSION"]==2 && $prop["MULTIPLE"]=="Y")
							{
								$strSql = "
									UPDATE	b_iblock_element_prop_s".$prop["IBLOCK_ID"]."
									SET	PROPERTY_".$prop["ID"]."=NULL, DESCRIPTION_".$prop["ID"]."=NULL
									WHERE	IBLOCK_ELEMENT_ID=".$ELEMENT_ID."
								";
								$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
							}
						}
						else
						{
							if($prop["VERSION"]==2 && $prop["MULTIPLE"]=="N")
							{
								if($prop["PROPERTY_TYPE"]=="N")
									$val = roundDB($val);
								$strSql = "
									UPDATE b_iblock_element_prop_s".$prop["IBLOCK_ID"]."
									SET PROPERTY_".$prop["ID"]."='".$DB->ForSql($val)."'
									,DESCRIPTION_".$prop["ID"]."=".($val_desc!==false?"'".$DB->ForSQL($val_desc, 255)."'":"null")."
									WHERE IBLOCK_ELEMENT_ID=".$ELEMENT_ID;
							}
							else
							{
								$strSql = "
									UPDATE ".$strTable."
									SET 	VALUE='".$DB->ForSql($val)."'
										,VALUE_NUM='".roundDB($val)."'
										".($val_desc!==false ? ",DESCRIPTION='".$DB->ForSql($val_desc, 255)."'" : "")."
									WHERE ID=".$res["ID"];
							}
							$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
							if($prop["VERSION"]==2 && $prop["MULTIPLE"]=="Y")
							{
								$strSql = "
									UPDATE	b_iblock_element_prop_s".$prop["IBLOCK_ID"]."
									SET	PROPERTY_".$prop["ID"]."=NULL, DESCRIPTION_".$prop["ID"]."=NULL
									WHERE	IBLOCK_ELEMENT_ID=".$ELEMENT_ID."
								";
								$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
							}
						}
					}
					elseif($prop["PROPERTY_TYPE"]=="F")
					{
						$val["MODULE_ID"] = "iblock";
						if(!$val["OLD_VALUE"])
							$val["old_file"] = $res["VALUE"];
						if($val_desc!==false)
							$val["DESCRIPTION"] = $val_desc;
						$val = CFile::SaveFile($val, "iblock");
						if($val=="NULL")
						{
							if($prop["VERSION"]==2 && $prop["MULTIPLE"]=="N")
							{
								$strSql = "
									UPDATE b_iblock_element_prop_s".$prop["IBLOCK_ID"]."
									SET PROPERTY_".$prop["ID"]."=null
									,DESCRIPTION_".$prop["ID"]."=null
									WHERE IBLOCK_ELEMENT_ID=".$ELEMENT_ID;
							}
							else
							{
								$strSql = "DELETE FROM ".$strTable." WHERE ID=".$res["ID"];
							}
							$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
							CFile::Delete($res["VALUE"]);
							if($prop["VERSION"]==2 && $prop["MULTIPLE"]=="Y")
							{
								$strSql = "
									UPDATE	b_iblock_element_prop_s".$prop["IBLOCK_ID"]."
									SET	PROPERTY_".$prop["ID"]."=NULL, DESCRIPTION_".$prop["ID"]."=NULL
									WHERE	IBLOCK_ELEMENT_ID=".$ELEMENT_ID."
								";
								$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
							}
						}
						elseif(IntVal($val)>0)
						{
							if($prop["VERSION"]==2 && $prop["MULTIPLE"]=="N")
							{
								$strSql = "
									UPDATE b_iblock_element_prop_s".$prop["IBLOCK_ID"]."
									SET PROPERTY_".$prop["ID"]."='".intval($val)."'
									".($val_desc!==false ? ",DESCRIPTION_".$prop["ID"]."='".$DB->ForSql($val_desc, 255)."'" : "")."
									WHERE IBLOCK_ELEMENT_ID=".$ELEMENT_ID;
							}
							else
							{
								$strSql = "
									UPDATE ".$strTable."
									SET 	VALUE='".intval($val)."'
										,VALUE_NUM='".intval($val)."'
										".($val_desc!==false ? ",DESCRIPTION='".$DB->ForSql($val_desc, 255)."'" : "")."
									WHERE ID=".$res["ID"];
							}
							$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
							if($res["VALUE"]>0)
								CFile::Delete($res["VALUE"]);
							if($prop["VERSION"]==2 && $prop["MULTIPLE"]=="Y")
							{
								$strSql = "
									UPDATE	b_iblock_element_prop_s".$prop["IBLOCK_ID"]."
									SET	PROPERTY_".$prop["ID"]."=NULL, DESCRIPTION_".$prop["ID"]."=NULL
									WHERE	IBLOCK_ELEMENT_ID=".$ELEMENT_ID."
								";
								$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
							}
						}
						elseif($val_desc!==false)
						{
							if($prop["VERSION"]==2 && $prop["MULTIPLE"]=="N")
							{
								$strSql = "
									UPDATE b_iblock_element_prop_s".$prop["IBLOCK_ID"]."
									SET DESCRIPTION_".$prop["ID"]."='".$DB->ForSql($val_desc, 255)."'
									WHERE IBLOCK_ELEMENT_ID=".$ELEMENT_ID."
								";
							}
							else
							{
								$strSql = "
									UPDATE ".$strTable."
									SET DESCRIPTION='".$DB->ForSql($val_desc, 255)."'
									WHERE ID=".$res["ID"]."
								";
							}
							$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
							if($prop["VERSION"]==2 && $prop["MULTIPLE"]=="Y")
							{
								$strSql = "
									UPDATE	b_iblock_element_prop_s".$prop["IBLOCK_ID"]."
									SET	PROPERTY_".$prop["ID"]."=NULL, DESCRIPTION_".$prop["ID"]."=NULL
									WHERE	IBLOCK_ELEMENT_ID=".$ELEMENT_ID."
								";
								$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
							}
						}
					}
					$ids .= ",".$res["ID"];
					unset($PROP[$res["ID"]]);
				} //while($res = $db_res->Fetch())

				foreach($PROP as $key=>$val)
				{
					if(is_array($val) && !is_set($val, "tmp_name"))
					{
						$val_desc = $val["DESCRIPTION"];
						$val = $val["VALUE"];
					}
					else
						$val_desc = false;

					if($prop["PROPERTY_TYPE"]=="F" && is_array($val))
					{
						unset($val["old_file"]);
						$val["MODULE_ID"] = "iblock";
						$val = CFile::SaveFile($val, "iblock");
						if(intval($val)<=0)
							$val = false;
						elseif($prop["MULTIPLE"]!="Y" && strlen($val)>0)
						{
							$strSql = "
								SELECT VALUE
								FROM b_iblock_element_property
								WHERE
									IBLOCK_ELEMENT_ID=".$ELEMENT_ID."
									AND IBLOCK_PROPERTY_ID=".IntVal($prop["ID"])."
							";
							if($prop["VERSION"]==2)
							{
								if($prop["MULTIPLE"]=="Y")
									$strSql = "
										SELECT PROPERTY_".$prop["ID"]." VALUE
										FROM b_iblock_element_prop_m".$prop["IBLOCK_ID"]."
										WHERE
										IBLOCK_ELEMENT_ID=".$ELEMENT_ID."
										AND IBLOCK_PROPERTY_ID=".IntVal($prop["ID"])."
									";
								else
									$strSql = "
										SELECT PROPERTY_".$prop["ID"]." VALUE
										FROM b_iblock_element_prop_s".$prop["IBLOCK_ID"]."
										WHERE IBLOCK_ELEMENT_ID=".$ELEMENT_ID."
									";
							}
							$pfres = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
							while($pfar = $pfres->Fetch())
								CFile::Delete($pfar["VALUE"]);

							$DB->Query(CIBLockElement::DeletePropertySQL($prop, $ELEMENT_ID), false, "File: ".__FILE__."<br>Line: ".__LINE__);
							if($prop["VERSION"]==2 && $prop["MULTIPLE"]=="Y")
							{
								$strSql = "
									UPDATE	b_iblock_element_prop_s".$prop["IBLOCK_ID"]."
									SET	PROPERTY_".$prop["ID"]."=NULL, DESCRIPTION_".$prop["ID"]."=NULL
									WHERE	IBLOCK_ELEMENT_ID=".$ELEMENT_ID."
								";
								$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
							}
						}
					}
					elseif($prop["PROPERTY_TYPE"]=="E")
					{
						if(in_array($val, $arWas))
							$val = "";
						else
							$arWas[] = $val;
					}

					if(strlen($val)>0)
					{
						if($prop["VERSION"]==2 && $prop["MULTIPLE"]=="N")
						{
							$strSql = "
								UPDATE	b_iblock_element_prop_s".$prop["IBLOCK_ID"]."
								SET
									PROPERTY_".$prop["ID"]." = '".$DB->ForSql($val)."'
									,DESCRIPTION_".$prop["ID"]."=".($val_desc!==false?"'".$DB->ForSQL($val_desc, 255)."'":"null")."
								WHERE	IBLOCK_ELEMENT_ID=".$ELEMENT_ID;
						}
						else
						{
							$strSql = "
								INSERT INTO ".$strTable."
								(IBLOCK_ELEMENT_ID, IBLOCK_PROPERTY_ID, VALUE, VALUE_NUM".($val_desc!==false?", DESCRIPTION":"").")
								SELECT
									".$ELEMENT_ID."
									,P.ID
									,'".$DB->ForSql($val)."'
									,'".roundDB($val)."'
									".($val_desc!==false?", '".$DB->ForSQL($val_desc, 255)."'":"")."
								FROM	b_iblock_property P
								WHERE	ID=".IntVal($prop["ID"]);
						}
						$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
						if($prop["VERSION"]==2 && $prop["MULTIPLE"]=="Y")
						{
							$strSql = "
								UPDATE	b_iblock_element_prop_s".$prop["IBLOCK_ID"]."
								SET	PROPERTY_".$prop["ID"]."=NULL, DESCRIPTION_".$prop["ID"]."=NULL
								WHERE	IBLOCK_ELEMENT_ID=".$ELEMENT_ID."
							";
							$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
						}

						if($prop["MULTIPLE"]!="Y") break;
					}
					$ids .= ",".$prop["ID"];
				} //foreach($PROP as $key=>$value)
			} //if($prop["PROPERTY_TYPE"]=="L")
		}

		if($bRecalcSections)
			CIBlockElement::RecalcSections($ELEMENT_ID);

		/****************************** QUOTA ******************************/
			$_SESSION["SESS_RECOUNT_DB"] = "Y";
		/****************************** QUOTA ******************************/
	}

	function GetRandFunction()
	{
		return " RAND(".rand(0, 1000000).") ";
	}

	function GetShowedFunction()
	{
		return " IfNULL(BE.SHOW_COUNTER/((UNIX_TIMESTAMP(now())-UNIX_TIMESTAMP(BE.SHOW_COUNTER_START)+0.1)/60/60),0) ";
	}
}
?>
