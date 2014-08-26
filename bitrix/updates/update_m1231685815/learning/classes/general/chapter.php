<?

class CAllChapter
{

	function CheckFields($arFields, $ID = false)
	{
		global $DB;
		$arMsg = Array();

		if ( (is_set($arFields, "NAME") || $ID === false) && strlen($arFields["NAME"]) <= 0)
			$arMsg[] = array("id"=>"NAME", "text"=> GetMessage("LEARNING_BAD_NAME"));

		if ($ID===false && !is_set($arFields, "COURSE_ID"))
			$arMsg[] = array("id"=>"COURSE_ID", "text"=> GetMessage("LEARNING_BAD_COURSE_ID"));

		if (is_set($arFields, "COURSE_ID"))
		{
			$r = CCourse::GetByID($arFields["COURSE_ID"]);
			if(!$r->Fetch())
				$arMsg[] = array("id"=>"COURSE_ID", "text"=> GetMessage("LEARNING_BAD_COURSE_ID_EX"));
		}

		if (is_set($arFields, "PREVIEW_PICTURE"))
		{
			$error = CFile::CheckImageFile($arFields["PREVIEW_PICTURE"]);
			if (strlen($error)>0) 
				$arMsg[] = array("id"=>"PREVIEW_PICTURE", "text"=> $error);
		}

		if (is_set($arFields, "DETAIL_PICTURE"))
		{
			$error = CFile::CheckImageFile($arFields["DETAIL_PICTURE"]);
			if (strlen($error)>0)
				$arMsg[] = array("id"=>"DETAIL_PICTURE", "text"=> $error);
		}

		if (empty($arMsg))
		{
			if (intval($arFields["CHAPTER_ID"])>0)
			{
				$r = CChapter::GetByID($arFields["CHAPTER_ID"]);
				if($ar = $r->Fetch())
				{
					//for Update
					if ($ID)
					{
						$rthis = CChapter::GetByID($ID);
						if($arthis = $rthis->Fetch())
						{
							if ($ar["COURSE_ID"] != $arthis["COURSE_ID"])
								$arMsg[] = array("id"=>"CHAPTER_ID", "text"=> GetMessage("LEARNING_BAD_BLOCK_SECTION_ID_PARENT"));
							elseif ($ar["CHAPTER_ID"] == $arthis["CHAPTER_ID"])
								$arMsg[] = array("id"=>"CHAPTER_ID", "text"=> GetMessage("LEARNING_BAD_BLOCK_SECTION_PARENT"));
						}
						else
							$arMsg[] = array("id"=>"CHAPTER_ID", "text"=> GetMessage("LEARNING_BAD_BLOCK_SECTION_ID_PARENT"));
					}
				}
				else
					$arMsg[] = array("id"=>"CHAPTER_ID", "text"=> GetMessage("LEARNING_BAD_BLOCK_SECTION_PARENT"));
			}
		}

		if(!empty($arMsg))
		{
			$e = new CAdminException($arMsg);
			$GLOBALS["APPLICATION"]->ThrowException($e);
			return false;
		}

		return true;
	}


	function Add($arFields)
	{
		global $DB;

		if (is_set($arFields, "ACTIVE") && $arFields["ACTIVE"] != "Y")
			$arFields["ACTIVE"]="N";

		if (is_set($arFields, "DESCRIPTION_TEXT_TYPE") && $arFields["DESCRIPTION_TEXT_TYPE"] != "html")
			$arFields["DESCRIPTION_TEXT_TYPE"]="text";

		if (is_set($arFields, "PREVIEW_TEXT_TYPE") && $arFields["PREVIEW_TEXT_TYPE"] != "html")
			$arFields["PREVIEW_TEXT_TYPE"]="text";

		if (is_set($arFields, "PREVIEW_PICTURE") && strlen($arFields["PREVIEW_PICTURE"]["name"])<=0 && strlen($arFields["PREVIEW_PICTURE"]["del"])<=0)
			unset($arFields["PREVIEW_PICTURE"]);

		if (is_set($arFields, "DETAIL_PICTURE") && strlen($arFields["DETAIL_PICTURE"]["name"])<=0 && strlen($arFields["DETAIL_PICTURE"]["del"])<=0)
			unset($arFields["DETAIL_PICTURE"]);

		if (intval($arFields["CHAPTER_ID"]) < 1)
			$arFields["CHAPTER_ID"] = false;

		if ($this->CheckFields($arFields))
		{
			unset($arFields["ID"]);

			CFile::SaveForDB($arFields, "PREVIEW_PICTURE", "learning");
			CFile::SaveForDB($arFields, "DETAIL_PICTURE", "learning");

			$ID = $DB->Add("b_learn_chapter", $arFields, Array("PREVIEW_TEXT","DETAIL_TEXT"));

			return $ID;
		}

		return false;
	}


	function Update($ID, $arFields)
	{
		global $DB;

		$ID = intval($ID);
		if ($ID < 1) return false;

		$db_record = CChapter::GetByID($ID);
		if(!($db_record = $db_record->Fetch()))
			return false;


		if (is_set($arFields, "ACTIVE") && $arFields["ACTIVE"] != "Y")
			$arFields["ACTIVE"]="N";

		if (is_set($arFields, "DESCRIPTION_TEXT_TYPE") && $arFields["DESCRIPTION_TEXT_TYPE"] != "html")
			$arFields["DESCRIPTION_TEXT_TYPE"]="text";

		if (is_set($arFields, "PREVIEW_TEXT_TYPE") && $arFields["PREVIEW_TEXT_TYPE"] != "html")
			$arFields["PREVIEW_TEXT_TYPE"]="text";

		if (is_set($arFields, "CHAPTER_ID") && intval($arFields["CHAPTER_ID"]) == "0")
			$arFields["CHAPTER_ID"] = false;

		if (is_set($arFields, "PREVIEW_PICTURE"))
		{
			if(strlen($arFields["PREVIEW_PICTURE"]["name"])<=0 && strlen($arFields["PREVIEW_PICTURE"]["del"])<=0)
				unset($arFields["PREVIEW_PICTURE"]);
			else
			{
				$pic_res = $DB->Query("SELECT PREVIEW_PICTURE FROM b_learn_chapter WHERE ID=".$ID);
				if($pic_res = $pic_res->Fetch())
					$arFields["PREVIEW_PICTURE"]["old_file"]=$pic_res["PREVIEW_PICTURE"];
			}
		}

		if (is_set($arFields, "DETAIL_PICTURE"))
		{
			if(strlen($arFields["DETAIL_PICTURE"]["name"])<=0 && strlen($arFields["DETAIL_PICTURE"]["del"])<=0)
				unset($arFields["DETAIL_PICTURE"]);
			else
			{
				$pic_res = $DB->Query("SELECT DETAIL_PICTURE FROM b_learn_chapter WHERE ID=".$ID);
				if($pic_res = $pic_res->Fetch())
					$arFields["DETAIL_PICTURE"]["old_file"]=$pic_res["DETAIL_PICTURE"];
			}
		}

		if ($this->CheckFields($arFields, $ID))
		{
			unset($arFields["ID"]);
			unset($arFields["COURSE_ID"]);

			$arBinds=Array(
				"PREVIEW_TEXT"=>$arFields["PREVIEW_TEXT"],
				"DETAIL_TEXT"=>$arFields["DETAIL_TEXT"]
			);

			CFile::SaveForDB($arFields, "PREVIEW_PICTURE", "learning");
			CFile::SaveForDB($arFields, "DETAIL_PICTURE", "learning");

			$strUpdate = $DB->PrepareUpdate("b_learn_chapter", $arFields);
			$strSql = "UPDATE b_learn_chapter SET ".$strUpdate." WHERE ID=".$ID;
			$DB->QueryBind($strSql, $arBinds, false, "File: ".__FILE__."<br>Line: ".__LINE__);

			return true;
		}
		return false;
	}

	function Delete($ID)
	{
		global $DB;

		$ID = intval($ID);
		if ($ID < 1) return false;

		$chapters = CChapter::GetList(Array(), Array("CHAPTER_ID"=>$ID));
		while ($chapter = $chapters->Fetch())
		{
			if(!CChapter::Delete($chapter["ID"]))
				return false;
		}

		$s = CChapter::GetByID($ID);
		if ($arS = $s->Fetch())
		{
			CFile::Delete($arS["PREVIEW_PICTURE"]);
			CFile::Delete($arS["DETAIL_PICTURE"]);

			//$strSql = "UPDATE b_learn_lesson SET CHAPTER_ID = NULL WHERE CHAPTER_ID=".$ID;
			//$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

			$les = CLesson::GetList(Array(), Array("CHAPTER_ID" => $ID));
			while ($arLesson = $les->Fetch())
			{
				if (!CLesson::Delete($arLesson["ID"]))
					return false;
			}

			$strSql = "DELETE FROM b_learn_chapter WHERE ID = ".$ID;
			return $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		}
		return true;
	}



	function GetByID($ID)
	{
		return CChapter::GetList(Array(),Array("ID" => $ID));
	}


	function GetFilter($arFilter=Array())
	{
		global $DB;

		if (!is_array($arFilter))
			$arFilter = Array();

		$arSqlSearch = Array();

		foreach ($arFilter as $key => $val)
		{
			$res = CCourse::MkOperationFilter($key);
			$key = $res["FIELD"];
			$cOperationType = $res["OPERATION"];

			$key = strtoupper($key);

			switch ($key)
			{
				case "CHAPTER_ID":
					if (intval($val)<=0)
						$arSqlSearch[] =  CCourse::FilterCreate("CH.".$key, "", "number", $bFullJoin, $cOperationType, false);
					else
						$arSqlSearch[] =  CCourse::FilterCreate("CH.".$key, intval($val), "number", $bFullJoin, $cOperationType);
					break;
				case "ID":
				case "SORT":
				case "COURSE_ID":
					$arSqlSearch[] = CCourse::FilterCreate("CH.".$key, $val, "number", $bFullJoin, $cOperationType);
					break;
				case "ACTIVE":
					$arSqlSearch[] = CCourse::FilterCreate("CH.".$key, $val, "string_equal", $bFullJoin, $cOperationType);
					break;
				case "NAME":
				case "CODE":
				case "DETAIL_TEXT":
				case "PREVIEW_TEXT":
					$arSqlSearch[] = CCourse::FilterCreate("CH.".$key, $val, "string", $bFullJoin, $cOperationType);
					break;
				case "TIMESTAMP_X":
					$arSqlSearch[] = CCourse::FilterCreate("CH.".$key, $val, "date", $bFullJoin, $cOperationType);
					break;
			}
		}

		return $arSqlSearch;
	}

	function GetCount($arFilter=Array())
	{
		global $DB, $USER;

		$arSqlSearch = CChapter::GetFilter($arFilter);

		$strSqlSearch = "";
		for($i=0; $i<count($arSqlSearch); $i++)
			if(strlen($arSqlSearch[$i])>0)
				$strSqlSearch .= " AND ".$arSqlSearch[$i]." ";

		$bCheckPerm = (!$USER->IsAdmin() && $arFilter["CHECK_PERMISSIONS"] != "N");

		$strSql =
		"SELECT COUNT(DISTINCT CH.ID) as C ".
		"FROM b_learn_chapter CH ".
		"INNER JOIN b_learn_course C ON CH.COURSE_ID = C.ID ".
		($bCheckPerm ? "LEFT JOIN b_learn_course_permission CP ON CP.COURSE_ID = C.ID " : "").
		"WHERE 1=1 ".
		($bCheckPerm ?
		"AND CP.USER_GROUP_ID IN (".$USER->GetGroups().") ".
		"AND CP.PERMISSION >= '".(strlen($arFilter["MIN_PERMISSION"])==1 ? $arFilter["MIN_PERMISSION"] : "R")."' ".
		"AND (CP.PERMISSION='X' OR C.ACTIVE='Y')"
		:"").
		$strSqlSearch;

		$res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		$res_cnt = $res->Fetch();

		return intval($res_cnt["C"]);
	}


	function GetTreeList($COURSE_ID)
	{
		global $DB;

		$COURSE_ID = intval($COURSE_ID);

		$strSql = 
		"SELECT C.* ".
		"FROM b_learn_chapter C ".
		"WHERE C.COURSE_ID = ".$COURSE_ID." ".
		"ORDER BY CHAPTER_ID, SORT ";

		$res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		$resElements = Array();
		while ($ar= $res->Fetch())
			$resElements[intval($ar["CHAPTER_ID"])][] = $ar;

		if (!empty($resElements))
			$arReturn = CChapter::_RecursiveGetTree($resElements);

		$r = new CDBResult();
		$r->InitFromArray($arReturn);
		unset($arReturn);
		return $r;
	}

	function _RecursiveGetTree(&$resElements, $CHAPTER_ID = 0, $DEPTH_LEVEL = 1)
	{
		static $arReturn;

		foreach ($resElements[$CHAPTER_ID] as $element)
		{
			$arReturn[$element["ID"]] = $element;
			$arReturn[$element["ID"]]["DEPTH_LEVEL"] = $DEPTH_LEVEL;

			if (isset($resElements[$element["ID"]]))
				CChapter::_RecursiveGetTree(&$resElements, $element["ID"], ($DEPTH_LEVEL+1));
		}

		return $arReturn;
	}

	function GetNavChain($COURSE_ID, $CHAPTER_ID)
	{
		global $DB;
		static $arReturn = Array();

		$strSql = 
		"SELECT C.* ".
		"FROM b_learn_chapter C ".
		"WHERE C.ID = ".intval($CHAPTER_ID)." ".
		"AND C.COURSE_ID = ".intval($COURSE_ID);

		$res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		while ($arRes= $res->Fetch())
		{
			$arReturn[$arRes["ID"]] = $arRes;
			CChapter::GetNavChain($COURSE_ID, $arRes["CHAPTER_ID"]);
		}

		$r = new CDBResult();
		$r->InitFromArray(array_reverse($arReturn));
		return $r;
	}



}

?>