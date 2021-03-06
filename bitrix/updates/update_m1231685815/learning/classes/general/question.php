<?
class CAllLQuestion
{
	function CheckFields(&$arFields, $ID = false)
	{
		global $DB;
		$arMsg = Array();

		if ( (is_set($arFields, "NAME") || $ID === false) && strlen($arFields["NAME"]) <= 0)
			$arMsg[] = array("id"=>"NAME", "text"=> GetMessage("LEARNING_BAD_NAME"));


		if (is_set($arFields, "FILE_ID"))
		{
			$error = CFile::CheckImageFile($arFields["FILE_ID"]);
			if (strlen($error)>0)
				$arMsg[] = array("id"=>"FILE_ID", "text"=> $error);
		}

		if(strlen($this->LAST_ERROR)<=0)
		{
			if (
				($ID === false && !is_set($arFields, "LESSON_ID"))
				|| 
				(is_set($arFields, "LESSON_ID") && intval($arFields["LESSON_ID"]) < 1)
				)
			{
				$arMsg[] = array("id"=>"LESSON_ID", "text"=> GetMessage("LEARNING_BAD_LESSON_ID"));
			}
			elseif (is_set($arFields, "LESSON_ID"))
			{
				$res = CLesson::GetByID($arFields["LESSON_ID"]);
				if($arRes = $res->Fetch())
				{
					if (CCourse::GetPermission($arRes["COURSE_ID"])<"W")
						$arMsg[] = array("id"=>"LESSON_ID", "text"=> GetMessage("LEARNING_BAD_LESSON_ID_EX"));
				}
				else
				{
					$arMsg[] = array("id"=>"LESSON_ID", "text"=> GetMessage("LEARNING_BAD_LESSON_ID_EX"));
				}
			}
		}

		if(!empty($arMsg))
		{
			$e = new CAdminException($arMsg);
			$GLOBALS["APPLICATION"]->ThrowException($e);
			return false;
		}


		if (is_set($arFields, "QUESTION_TYPE") && !in_array($arFields["QUESTION_TYPE"], Array("S", "M")))
			$arFields["QUESTION_TYPE"] = "S";

		if (is_set($arFields, "DESCRIPTION_TYPE") && $arFields["DESCRIPTION_TYPE"] != "html")
			$arFields["DESCRIPTION_TYPE"] = "text";

		if (is_set($arFields, "DIRECTION") && $arFields["DIRECTION"] != "H")
			$arFields["DIRECTION"] = "V";

		if (is_set($arFields, "SELF") && $arFields["SELF"] != "Y")
			$arFields["SELF"] = "N";

		if (is_set($arFields, "ACTIVE") && $arFields["ACTIVE"] != "Y")
			$arFields["ACTIVE"] = "N";

		return true;

	}


	function Add($arFields)
	{
		global $DB;

		if($this->CheckFields($arFields))
		{
			unset($arFields["ID"]);
			CFile::SaveForDB($arFields, "FILE_ID", "learning");
			$ID = $DB->Add("b_learn_question", $arFields, Array("DESCRIPTION"));
			return $ID;
		}
		return false;
	}


	function Update($ID, $arFields)
	{
		global $DB;

		$ID = intval($ID);
		if ($ID < 1) return false;

		if (is_set($arFields, "FILE_ID"))
		{
			if(strlen($arFields["FILE_ID"]["name"])<=0 && strlen($arFields["FILE_ID"]["del"])<=0)
				unset($arFields["FILE_ID"]);
			else
			{
				$pic_res = $DB->Query("SELECT FILE_ID FROM b_learn_question WHERE ID=".$ID);
				if($pic_res = $pic_res->Fetch())
					$arFields["FILE_ID"]["old_file"]=$pic_res["FILE_ID"];
			}
		}

		if ($this->CheckFields($arFields, $ID))
		{
			unset($arFields["ID"]);

			$arBinds=Array(
				"DESCRIPTION"=>$arFields["DESCRIPTION"]
			);

			CFile::SaveForDB($arFields, "FILE_ID", "learning");

			$strUpdate = $DB->PrepareUpdate("b_learn_question", $arFields);
			$strSql = "UPDATE b_learn_question SET ".$strUpdate." WHERE ID=".$ID;
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

		$strSql = "SELECT FILE_ID FROM b_learn_question WHERE ID = ".$ID;
		$r = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		if (!$arQuestion = $r->Fetch())
			return false;

		$answers = CLAnswer::GetList(Array(), Array("QUESTION_ID" => $ID));
		while($arAnswer = $answers->Fetch())
		{
			if(!CLAnswer::Delete($arAnswer["ID"]))
				return false;
		}

		$arAttempts = Array();
		$strSql = "SELECT ATTEMPT_ID FROM b_learn_test_result WHERE QUESTION_ID = ".$ID;
		$res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		while($ar = $res->Fetch())
			$arAttempts[] = $ar["ATTEMPT_ID"]; //Attempts to recount

		//Results
		$strSql = "DELETE FROM b_learn_test_result WHERE QUESTION_ID = ".$ID;
		if (!$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__))
			return false;

		foreach($arAttempts as $ATTEMPT_ID)
		{
			CTestAttempt::RecountQuestions($ATTEMPT_ID);
			CTestAttempt::OnAttemptChange($ATTEMPT_ID);
		}

		$strSql = "DELETE FROM b_learn_question WHERE ID = ".$ID;

		if (!$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__))
			return false;

		CFile::Delete($arQuestion["FILE_ID"]);

		return true;
	}

	function GetByID($ID)
	{
		return CLQuestion::GetList($arOrder=Array(), $arFilter=Array("ID" => $ID));
	}


	function GetFilter($arFilter)
	{

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
				case "ID":
				case "SORT":
				case "LESSON_ID":
				case "POINT":
					$arSqlSearch[] = CCourse::FilterCreate("CQ.".$key, $val, "number", $bFullJoin, $cOperationType);
					break;

				case "COURSE_ID":
					$arSqlSearch[] = CCourse::FilterCreate("CL.".$key, $val, "number", $bFullJoin, $cOperationType);
					break;

				case "NAME":
					$arSqlSearch[] = CCourse::FilterCreate("CQ.".$key, $val, "string", $bFullJoin, $cOperationType);
					break;

				case "QUESTION_TYPE":
				case "ACTIVE":
				case "SELF":
					$arSqlSearch[] = CCourse::FilterCreate("CQ.".$key, $val, "string_equal", $bFullJoin, $cOperationType);
					break;
			}

		}

		return $arSqlSearch;

	}

	function GetList($arOrder=Array(), $arFilter=Array())
	{
		global $DB, $USER;

		$arSqlSearch = CLQuestion::GetFilter($arFilter);

		$strSqlSearch = "";
		for($i=0; $i<count($arSqlSearch); $i++)
			if(strlen($arSqlSearch[$i])>0)
				$strSqlSearch .= " AND ".$arSqlSearch[$i]." ";

		$strSql =
		"SELECT CQ.*, ".
		$DB->DateToCharFunction("CQ.TIMESTAMP_X")." as TIMESTAMP_X ".
		"FROM b_learn_question CQ ".
		"INNER JOIN b_learn_lesson CL ON CQ.LESSON_ID = CL.ID ".
		"INNER JOIN b_learn_course C ON CL.COURSE_ID = C.ID ".
		//"FROM b_learn_question CQ, b_learn_lesson CL, b_learn_course C ".
		//"WHERE CQ.LESSON_ID = CL.ID AND CL.COURSE_ID = C.ID ".
		"WHERE 1=1 ".
		$strSqlSearch;

		if (!is_array($arOrder))
			$arOrder = Array();

		foreach($arOrder as $by=>$order)
		{
			$by = strtolower($by);
			$order = strtolower($order);
			if ($order!="asc")
				$order = "desc";

			if ($by == "id")						$arSqlOrder[] = " CQ.ID ".$order." ";
			elseif ($by == "name")			$arSqlOrder[] = " CQ.NAME ".$order." ";
			elseif ($by == "sort")				$arSqlOrder[] = " CQ.SORT ".$order." ";
			elseif ($by == "point")			$arSqlOrder[] = " CQ.POINT ".$order." ";
			elseif ($by == "type")			$arSqlOrder[] = " CQ.QUESTION_TYPE ".$order." ";
			elseif ($by == "self")				$arSqlOrder[] = " CQ.SELF ".$order." ";
			elseif ($by == "active")			$arSqlOrder[] = " CQ.ACTIVE ".$order." ";
			else
			{
				$arSqlOrder[] = " CQ.TIMESTAMP_X ".$order." ";
				$by = "timestamp_x";
			}
		}

		$strSqlOrder = "";
		DelDuplicateSort($arSqlOrder);
		for ($i=0; $i<count($arSqlOrder); $i++)
		{
			if($i==0)
				$strSqlOrder = " ORDER BY ";
			else
				$strSqlOrder .= ",";

			$strSqlOrder .= $arSqlOrder[$i];
		}

		$strSql .= $strSqlOrder;

		//echo $strSql;

		return $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

	}


	function GetCount($arFilter=Array())
	{
		global $DB;

		$arSqlSearch = CLQuestion::GetFilter($arFilter);

		$strSqlSearch = "";
		for($i=0; $i<count($arSqlSearch); $i++)
			if(strlen($arSqlSearch[$i])>0)
				$strSqlSearch .= " AND ".$arSqlSearch[$i]." ";

		$strSql =
		"SELECT COUNT(DISTINCT CQ.ID) as C ".
		"FROM b_learn_question CQ ".
		"INNER JOIN b_learn_lesson CL ON CQ.LESSON_ID = CL.ID ".
		"INNER JOIN b_learn_course C ON CL.COURSE_ID = C.ID ".
		"WHERE 1=1 ".
		$strSqlSearch;


		$res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		$res_cnt = $res->Fetch();

		return intval($res_cnt["C"]);

	}
}

?>