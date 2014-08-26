<?
/*
##############################################
# Bitrix Site Manager                        #
# Copyright (c) 2002-2007 Bitrix             #
# http://www.bitrixsoft.com                  #
# mailto:admin@bitrixsoft.com                #
##############################################
*/

global $BX_DOC_ROOT;
$BX_DOC_ROOT = rtrim(preg_replace("'[\\\\/]+'", "/", $_SERVER["DOCUMENT_ROOT"]), "/ ");

/*********************************************************************
							HTML элементы
*********************************************************************/

/**************************************************************
	¬озвращает HTML код элемента "input"
***************************************************************/

function InputType($strType,$strName,$strValue,$strCmp,$strPrintValue = false,$strPrint = "", $field1 = "")
{
	if ($strPrintValue) $strPrintValue = $strValue; else $strPrintValue = $strPrint;
	$bCheck = false;
	if (strlen($strCmp) > 0 and strlen($strValue) > 0)
	{
		$arr = (is_array($strCmp)) ? $strCmp : explode(",",$strCmp);
		if (in_array($strValue,$arr)) $bCheck = true;
	}
	$strReturn = "<input type=\"$strType\" $field1 name=\"$strName\" id=\"$strName\" value=\"$strValue\" ";
	if ($bCheck) $strReturn = $strReturn." checked ";
	$strReturn = $strReturn.">".$strPrintValue;
	return $strReturn;
}

/**************************************************************
	¬озвращает HTML код элемента "select" из выборки
***************************************************************/

function SelectBox(
	$strBoxName,					// им€ элемента
	$a,								// выборка с пол€ми REFERENCE, REFERENCE_ID
	$strDetText = "",				// пустой элемент списка с value = NOT_REF
	$strSelectedVal = "",			// выбранный элемент
	$field1="class=\"typeselect\""	// дополнительное поле
	)
{
	if(!isset($strSelectedVal)) $strSelectedVal="";
	$strReturnBox = "<select $field1 name=\"$strBoxName\" id=\"$strBoxName\" size=\"1\">";
	$bSelected = false;
	if (strlen($strDetText) > 0)
		$strReturnBox = $strReturnBox."<option value=\"NOT_REF\">$strDetText</option>";
	while ($ar = $a->Fetch())
	{
		$reference_id = $ar["REFERENCE_ID"];
		$reference = $ar["REFERENCE"];
		if (strlen($reference_id)<=0) $reference_id = $ar["reference_id"];
		if (strlen($reference)<=0) $reference = $ar["reference"];

		$strReturnBox = $strReturnBox."<option ";
		if (strcasecmp($reference_id,$strSelectedVal)== 0)
		{
			$strReturnBox = $strReturnBox." selected ";
			$bSelected = True;
		}
		$strReturnBox = $strReturnBox."value=\"".htmlspecialchars($reference_id). "\">". htmlspecialchars($reference)."</option>";
	}
	return $strReturnBox."</select>";
}

/**************************************************************
	¬озвращает HTML код элемента "select multiple" из выборки
***************************************************************/

function SelectBoxM(
	$strBoxName,					// им€ элемента
	$a,								// выборка дл€ отображени€ с пол€ми REFERENCE, REFERENCE_ID
	$arr,							// массив значений которые необходимо выбрать
	$strDetText = "",				// пустой элемент списка с value = NOT_REF
	$strDetText_selected = false,	// выбрать ли пустой элемент
	$size = 5,						// поле size элемента
	$field1="class=\"typeselect\""	// стиль элемента
	)
{
	$strReturnBox = "<select $field1 multiple name=\"$strBoxName\" id=\"$strBoxName\" size=\"$size\">";
	if (strlen($strDetText)>0)
	{
		$strReturnBox = $strReturnBox."<option ";
		if ($strDetText_selected) $strReturnBox = $strReturnBox." selected ";
		$strReturnBox = $strReturnBox." value='NOT_REF'>".$strDetText."</option>";
	}
	while ($ar=$a->Fetch())
	{
		$reference_id = $ar["REFERENCE_ID"];
		$reference = $ar["REFERENCE"];
		if (strlen($reference_id)<=0) $reference_id = $ar["reference_id"];
		if (strlen($reference)<=0) $reference = $ar["reference"];

		$sel = (is_array($arr) && in_array($reference_id, $arr))? "selected": "";
		$strReturnBox = $strReturnBox."<option ".$sel;
		$strReturnBox = $strReturnBox." value=\"".htmlspecialchars($reference_id)."\">". htmlspecialchars($reference)."</option>";
	}
	return $strReturnBox."</select>";
}

/**************************************************************
	¬озвращает HTML код элемента "select multiple" из массива
***************************************************************/

function SelectBoxMFromArray(
	$strBoxName,			// им€ элемента
	$a,				// ассоциированный массив с пол€ми REFERENCE, REFERENCE_ID
	$arr,				// массив значений которые необходимо выбрать
	$strDetText = "",		// пустой элемент списка с value = NOT_REF
	$strDetText_selected = false,	// выбрать ли пустой элемент
	$size = 5,			// поле "size" элемента
	$field1="class='typeselect'"	// стиль элемента
	)
{
	$strReturnBox = "<select $field1 multiple name=\"$strBoxName\" id=\"$strBoxName\" size=\"$size\">";

	if(array_key_exists("REFERENCE_ID", $a))
		$reference_id = $a["REFERENCE_ID"];
	elseif(array_key_exists("reference_id", $a))
		$reference_id = $a["reference_id"];
	else
		$reference_id = array();

	if(array_key_exists("REFERENCE", $a))
		$reference = $a["REFERENCE"];
	elseif(array_key_exists("reference", $a))
		$reference = $a["reference"];
	else
		$reference = array();

	if(strlen($strDetText) > 0)
	{
		$strReturnBox .= "<option ";
		if($strDetText_selected)
			$strReturnBox .= " selected ";
		$strReturnBox .= " value='NOT_REF'>".$strDetText."</option>";
	}

	foreach($reference_id as $key => $value)
	{
		$sel = (is_array($arr) && in_array($value, $arr)) ? "selected" : "";
		$strReturnBox .= "<option value=\"".htmlspecialchars($value)."\" ".$sel.">". htmlspecialchars($reference[$key])."</option>";
 	}

	$strReturnBox .= "</select>";
	return $strReturnBox;
}

/***********************************************************
	¬озвращает HTML код элемента "select" из массива
************************************************************/

function SelectBoxFromArray(
	$strBoxName,
	$db_array,
	$strSelectedVal = "",
	$strDetText = "",
	$field1="class='typeselect'",
	$go=false, // перейти сразу после выбора
	$form="form1"
	)
{
	if ($go)
	{
		$strReturnBox = "<script type=\"text/javascript\">\n".
			"function ".$strBoxName."LinkUp()\n".
			"{var number = document.".$form.".".$strBoxName.".selectedIndex;\n".
			"if(document.".$form.".".$strBoxName.".options[number].value!=\"0\"){ \n".
			"document.".$form.".".$strBoxName."_SELECTED.value=\"yes\";\n".
			"document.".$form.".submit();\n".
			"}}\n".
			"</script>\n";
		$strReturnBox .= "<input type=\"hidden\" name=\"".$strBoxName."_SELECTED\" id=\"".$strBoxName."_SELECTED\" value=\"\">";
		$strReturnBox .= "<select $field1 name='$strBoxName' id='$strBoxName' size='1' OnChange='".$strBoxName."LinkUp()' class='typeselect'>";
	}
	else
	{
		$strReturnBox = "<select $field1 name='$strBoxName' id='$strBoxName' size='1'>";
	}

	$ref=$db_array["reference"];
	$ref_id=$db_array["reference_id"];
	if (!is_array($ref)) $ref=$db_array["REFERENCE"];
	if (!is_array($ref_id)) $ref_id=$db_array["REFERENCE_ID"];

	If (strlen($strDetText) > 0)
		$strReturnBox = $strReturnBox."<option value=\"\">$strDetText</option>";

	for ($i=0;$i<count($ref);$i++)
	{
		$strReturnBox = $strReturnBox."<option ";
		if (strcasecmp($ref_id[$i], $strSelectedVal) == 0 )
			$strReturnBox = $strReturnBox." selected ";
		$strReturnBox = $strReturnBox."value=\"".$ref_id[$i]. "\">".htmlspecialchars($ref[$i])."</option>";
	}
	return $strReturnBox = $strReturnBox. "</select>";
}

/*********************************************************************
	ƒаты
*********************************************************************/

function Calendar($sFieldName, $sFormName="skform", $sFromName="", $sToName="")
{
	if(class_exists("CAdminCalendar"))
		return CAdminCalendar::Calendar($sFieldName, $sFromName, $sToName);

	global $bCalendarCode;
	$func = "";
	if($bCalendarCode <> "Y")
	{
		$bCalendarCode = "Y";
		$func =
			"<script type=\"text/javascript\">\n".
			"<!--\n".
			"window.Calendar = function(params, dateVal)\n".
			"{\n".
			"	var left, top;\n".
			"	var width = 180, height = 160;\n".
			"	if('['+typeof(window.event)+']' == '[object]')\n".
			"	{\n".
			"		top = (window.event.screenY+20+height>screen.height-40? window.event.screenY-45-height:window.event.screenY+20);\n".
			"		left = (window.event.screenX-width/2);\n".
			"	}\n".
			"	else\n".
			"	{\n".
			"		top = Math.floor((screen.height - height)/2-14);\n".
			"		left = Math.floor((screen.width - width)/2-5);\n".
			"	}\n".
			"	window.open('/bitrix/tools/calendar.php?lang=".LANGUAGE_ID.(defined("ADMIN_SECTION") && ADMIN_SECTION===true?"&admin_section=Y":"&admin_section=N")."&'+params+'&date='+escape(dateVal)+'&initdate='+escape(dateVal),'','scrollbars=no,resizable=yes,width='+width+',height='+height+',left='+left+',top='+top);\n".
			"}\n".
			"//-->\n".
			"</script>\n";
	}
	return $func."<a href=\"javascript:void(0);\" onclick=\"window.Calendar('name=".urlencode($sFieldName)."&amp;from=".urlencode($sFromName)."&amp;to=".urlencode($sToName)."&amp;form=".urlencode($sFormName)."', document['".$sFormName."']['".$sFieldName."'].value);\" title=\"".GetMessage("TOOLS_CALENDAR")."\"><img src=\"".BX_ROOT."/images/icons/calendar.gif\" alt=\"".GetMessage("TOOLS_CALENDAR")."\" width=\"15\" height=\"15\" border=\"0\" /></a>";
}

function CalendarDate($sFromName, $sFromVal, $sFormName="skform", $size="10", $param="class=\"typeinput\"")
{
	if(class_exists("CAdminCalendar"))
		return CAdminCalendar::CalendarDate($sFromName, $sFromVal, $size, ($size > 10));

	return '<input type="text" name="'.$sFromName.'" id="'.$sFromName.'" size="'.$size.'" value="'.htmlspecialchars($sFromVal).'" '.$param.' /> '."\n".Calendar($sFromName, $sFormName)."\n";
}

function CalendarPeriod($sFromName, $sFromVal, $sToName, $sToVal, $sFormName="skform", $show_select="N", $field_select="class=\"typeselect\"", $field_input="class=\"typeinput\"", $size="10")
{
	if(class_exists("CAdminCalendar"))
		return CAdminCalendar::CalendarPeriod($sFromName, $sToName, $sFromVal, $sToVal, ($show_select=="Y"), $size, ($size > 10));

	$arr = array();
	$str = "";
	if ($show_select=="Y")
	{
		$sname = $sFromName."_DAYS_TO_BACK";
		$str = "
<script type=\"text/javascript\">
function ".$sFromName."_SetDate()
{
	var number = document.".$sFormName.".".$sname.".selectedIndex-1;
	document.".$sFormName.".".$sFromName.".disabled = false;
	if (number>=0)
	{
		document.".$sFormName.".".$sFromName.".value = dates[number];
		document.".$sFormName.".".$sFromName.".disabled = true;
	}
}
</script>
";
		global $$sname;
		$value = $$sname;
		if (strlen($value)>0 && $value!="NOT_REF") $ds="disabled";
		?><script type="text/javascript">
			var dates = new Array();
		<?
		for ($i=0; $i<=90; $i++)
		{
			$prev_date = GetTime(time()-86400*$i);
			?>dates[<?=$i?>]="<?=$prev_date?>";<?
			if (!is_array($arr["reference"])) $arr["reference"] = array();
			if (!is_array($arr["reference_id"])) $arr["reference_id"] = array();
			$arr["reference"][] = $i." ".GetMessage("TOOLS_DN");
			$arr["reference_id"][] = $i;
		}
		?></script><?
		$str .= SelectBoxFromArray($sname, $arr, $value , "&nbsp;", "onchange=\"".$sFromName."_SetDate()\" ".$field_select);
		$str .= "&nbsp;";
	}
	$str .=
		'<input '.$ds.' '.$field_input.' type="text" name="'.$sFromName.'" id="'.$sFromName.'" size="'.$size.'" value="'.htmlspecialchars($sFromVal).'" /> '."\n".
		Calendar($sFromName, $sFormName, $sFromName, $sToName).' ... '."\n".
		'<input '.$field_input.' type="text" name="'.$sToName.'" id="'.$sToName.'" size="'.$size.'" value="'.htmlspecialchars($sToVal).'" /> '."\n".
		Calendar($sToName, $sFormName, $sFromName, $sToName)."\n";

	return '<span style="white-space: nowrap;">'.$str.'</span>';
}

// провер€ет корректность ввода даты по заданному формату
function CheckDateTime($datetime, $format=false)
{
	if ($format===false && defined("FORMAT_DATETIME")) $format = FORMAT_DATETIME;

	$ar = ParseDateTime($datetime, $format);
	//echo "<pre>"; print_r($ar); echo "</pre>";
	$day   = intval($ar["DD"]);
	$month = intval($ar["MM"]);
	$year  = intval($ar["YYYY"]);
	$hour  = intval($ar["HH"]);
	$min   = intval($ar["MI"]);
	$sec   = intval($ar["SS"]);

	if (!checkdate($month, $day, $year))
		return false;

	if ($hour>24 || $hour<0 || $min<0 || $min>59 || $sec<0 || $sec>59)
		return false;

	$arSep_1 = $arSep_2 = array();
	$ar = split("[[:digit:]]", $datetime);
	TrimArr($ar); if (is_array($ar)) foreach($ar as $s) $arSep_1[] = $s;

	$ar = split("[[:alpha:]]", $format);
	TrimArr($ar); if (is_array($ar)) foreach($ar as $s) $arSep_2[] = $s;

	if (count($arSep_1)<=count($arSep_2))
	{
		for($i=0; $i<=count($arSep_1)-1; $i++)
			if ($arSep_1[$i]!=$arSep_2[$i])
				return false;
	}
	else
	{
		for($i=0; $i<=count($arSep_2)-1; $i++)
			if ($arSep_1[$i]!=$arSep_2[$i])
				return false;
	}

	return true;
}

// возвращает Unix-timestamp из строки даты
function MakeTimeStamp($datetime, $format=false)
{
	if ($format===false && defined("FORMAT_DATETIME")) $format = FORMAT_DATETIME;
	$ar = ParseDateTime($datetime, $format);
	$day   = intval($ar["DD"]);
	$month = intval($ar["MM"]);
	$year  = intval($ar["YYYY"]);
	$hour  = intval($ar["HH"]);
	$min   = intval($ar["MI"]);
	$sec   = intval($ar["SS"]);

	if (!checkdate($month,$day,$year))
		return false;

	if ($hour>24 || $hour<0 || $min<0 || $min>59 || $sec<0 || $sec>59)
		return false;

	$ts = mktime($hour,$min,$sec,$month,$day,$year);
	if($ts <= 0)
		return false;

	return $ts;
}

// разбирает врем€ в массив
function ParseDateTime($datetime, $format=false)
{
	if ($format===false && defined("FORMAT_DATETIME")) $format = FORMAT_DATETIME;
	$fm = split("[^[:alpha:]]", $format);
	if (is_array($fm))
	{
		$dt = split("[^[:digit:]]", $datetime);
		if (is_array($dt))
		{
			$dt_args = array();
			foreach($dt as $v)
				if(strlen(trim($v)) > 0)
					$dt_args[] = $v;

			$fm_args = array();
			foreach($fm as $v)
				if(strlen(trim($v)) > 0)
					$fm_args[] = $v;

			if(count($fm_args) > 0 && count($dt_args) > 0)
			{
				foreach($fm_args as $i => $v)
				{
					$arrResult[$v] = sprintf("%0".strlen($v)."d", intval($dt_args[$i]));
				}
				return $arrResult;
			}
		}
	}
	return false;
}

// прибавл€ет к Unix-timestamp заданный период времени
function AddToTimeStamp($arrAdd, $stmp=false)
{
	if ($stmp===false) $stmp = time();
	if (is_array($arrAdd) && count($arrAdd)>0)
	{
		while(list($key, $value) = each($arrAdd))
		{
			$value = intval($value);
			if (is_int($value))
			{
				switch ($key)
				{
					case "DD":
						$stmp = AddTime($stmp, $value, "D");
						break;
					case "MM":
						$stmp = AddTime($stmp, $value, "MN");
						break;
					case "YYYY":
						$stmp = AddTime($stmp, $value, "Y");
						break;
					case "HH":
						$stmp = AddTime($stmp, $value, "H");
						break;
					case "MI":
						$stmp = AddTime($stmp, $value, "M");
						break;
					case "SS":
						$stmp = AddTime($stmp, $value, "S");
						break;
				}
			}
		}
	}
	return $stmp;
}

function ConvertDateTime($datetime, $to_format=false, $from_site=false, $bSearchInSitesOnly = false)
{
	if ($to_format===false && defined("FORMAT_DATETIME")) $to_format = FORMAT_DATETIME;
	return FmtDate($datetime, $to_format, $from_site, false, $bSearchInSitesOnly);
}

function ConvertTimeStamp($timestamp=false, $type="SHORT", $site=false, $bSearchInSitesOnly = false)
{
	if ($timestamp===false) $timestamp = time();
	return GetTime($timestamp, $type, $site, $bSearchInSitesOnly);
}

// конвертирует дату из формата одного из сайтов в заданный формат
function FmtDate($str_date, $format=false, $site=false, $bSearchInSitesOnly = false)
{
	global $DB;
	if ($site===false && defined("SITE_ID")) $site = SITE_ID;
	if ($format===false && defined("FORMAT_DATETIME")) $format = FORMAT_DATETIME;
	return $DB->FormatDate($str_date, CSite::GetDateFormat("FULL", $site, $bSearchInSitesOnly), $format);
}

// возвращает врем€ в формате текущего €зыка по заданному Unix Timestamp
function GetTime($timestamp, $type="SHORT", $site=false, $bSearchInSitesOnly = false)
{
	global $DB;
	if ($site===false && defined("SITE_ID")) $site = SITE_ID;
	return date($DB->DateFormatToPHP(CSite::GetDateFormat($type, $site, $bSearchInSitesOnly)), $timestamp);
}

// устаревша€ функци€
function AddTime($stmp, $add, $type="D")
{
	switch ($type)
	{
		case "H":
			$ret = mktime(
				date("H",$stmp)+$add,date("i",$stmp),date("s",$stmp),
				date("m",$stmp),date("d",$stmp),date("Y",$stmp));
			break;
		case "M":
			$ret = mktime(
				date("H",$stmp),date("i",$stmp)+$add,date("s",$stmp),
				date("m",$stmp),date("d",$stmp),date("Y",$stmp));
			break;
		case "S":
			$ret = mktime(
				date("H",$stmp),date("i",$stmp),date("s",$stmp)+$add,
				date("m",$stmp),date("d",$stmp),date("Y",$stmp));
			break;
		case "D":
			$ret = mktime(
				date("H",$stmp),date("i",$stmp),date("s",$stmp),
				date("m",$stmp),date("d",$stmp)+$add,date("Y",$stmp));
			break;
		case "MN":
			$ret = mktime(
				date("H",$stmp),date("i",$stmp),date("s",$stmp),
				date("m",$stmp)+$add,date("d",$stmp),date("Y",$stmp));
			break;
		case "Y":
			$ret = mktime(
				date("H",$stmp),date("i",$stmp),date("s",$stmp),
				date("m",$stmp),date("d",$stmp),date("Y",$stmp)+$add);
			break;
	}
	return $ret;
}

// устаревша€ функци€
function ParseDate($strDate, $format="dmy")
{
	$day = $month = $year = 0;
	$args = split( '[/.-]', $strDate);
	$bound = min(strlen($format), count($args));
	for($i=0; $i<$bound; $i++)
	{
		if($format[$i] == 'm') $month = intval($args[$i]);
		elseif($format[$i] == 'd') $day = intval($args[$i]);
		elseif($format[$i] == 'y') $year = intval($args[$i]);
	}
	return (checkdate($month, $day, $year) ? array($day, $month, $year) : 0);
}

// устаревша€ функци€
function MkDateTime($strDT, $format="d.m.Y H:i:s")
{
	$arr = array("d.m.Y","d.m.Y H:i","d.m.Y H:i:s");
	if (!(in_array($format,$arr)))
		return false;

	$strDT = ereg_replace("([[:blank:]])+","\\1",$strDT);
	list($date,$time) = explode(" ",$strDT);
	$date  = trim($date);
	$time  = trim($time);
	list($day,$month,$year) = explode(".",$date);
	list($hour,$min,$sec)   = explode(":",$time);
	$day   = intval($day);
	$month = intval($month);
	$year  = intval($year);
	$hour  = intval($hour);
	$min   = intval($min);
	$sec   = intval($sec);
	if (!checkdate($month,$day,$year))
		return false;
	if ($hour>24 || $hour<0 || $min<0 || $min>59 || $sec<0 || $sec>59)
		return false;

	$ts = mktime($hour,$min,$sec,$month,$day,$year);
	if($ts <= 0)
		return false;

	return $ts;
}

// устаревша€ функци€
function PHPFormatDateTime($strDateTime, $format="d.m.Y H:i:s")
{
	return date($format, MkDateTime(FmtDate($strDateTime,"D.M.Y H:I:S"), "d.m.Y H:i:s"));
}

/*********************************************************************
							ћассивы
*********************************************************************/

/*
удал€ет дубли в массиве сортировки
массив
Array
(
    [0] => T.NAME DESC
    [1] => T.NAME ASC
    [2] => T.ID ASC
    [3] => T.ID DESC
    [4] => T.DESC
)
преобразует в
Array
(
    [0] => T.NAME DESC
    [1] => T.ID ASC
    [2] => T.DESC ASC
)
*/
function DelDuplicateSort(&$arSort)
{
	if (is_array($arSort) && count($arSort)>0)
	{
		$arSort2 = array();
		foreach($arSort as $val)
		{
			$arSort1 = explode(" ", trim($val));
			$order = array_pop($arSort1);
			$order_ = strtoupper(trim($order));
			if (!($order_=="DESC" || $order_=="ASC"))
			{
				$arSort1[] = $order;
				$order_ = "";
			}
			$by = implode(" ", $arSort1);
			if(strlen($by)>0 && !array_key_exists($by, $arSort2))
				$arSort2[$by] = $order_;
		}
		$arSort = array();
		foreach($arSort2 as $by=>$order)
			$arSort[] = $by." ".$order;
	}
}

function array2param($cur,$ar)
{
	$str = "";
	$keys = array_keys($ar);
	for ($i=0;$i<count($keys);$i++)
	{
		if (is_array( $ar[$keys[$i]] ))
		{
			$str.= (empty($str) ? "" : "&").
				array2param(
							htmlspecialchars(sprintf("%s[%s]", $cur, $keys[$i])), $ar[$keys[$i]]
							);
		}
		else
		{
			$str.= (empty($str) ? "" : "&").htmlspecialchars(sprintf("%s[%s]=%s",$cur,$keys[$i],UrlEncode($ar[$keys[$i]])));
		}
	}
	return $str;
}

function array_convert_name_2_value($arr)
{
	$arr_res = array();
	if (is_array($arr) && count($arr)>0)
	{
		while (list($key, $value)=each($arr))
		{
			global $$value;
			$arr_res[$key] = $$value;
		}
	}
	return $arr_res;
}

function InitBVarFromArr($arr)
{
	if (is_array($arr) && count($arr)>0)
	{
		foreach($arr as $value)
		{
			global $$value;
			$$value = ($$value=="Y") ? "Y" : "N";
		}
	}
}

function TrimArr(&$arr, $trim_value=false)
{
	if(!is_array($arr))
		return false;

	$found = false;
	while (list($key,$value)=each($arr))
	{
		if ($trim_value)
		{
			$arr[$key] = trim($value);
		}
		if (strlen(trim($value))<=0)
		{
			unset($arr[$key]);
			$found = true;
		}
	}
	reset($arr);
	return ($found) ? true : false;
}

function is_set(&$a, $k=false)
{
	if ($k===false)
		return isset($a);

	if(is_array($a))
		return array_key_exists($k, $a);

	return false;
}

/*********************************************************************
							—троки
*********************************************************************/

mt_srand ((double) microtime() * 1000000);
function randString($pass_len=10, $pass_chars=false)
{
	static $allchars = "abcdefghijklnmopqrstuvwxyzABCDEFGHIJKLNMOPQRSTUVWXYZ0123456789";
	$string = "";
	if(is_array($pass_chars))
	{
		while(strlen($string) < $pass_len)
		{
			if(function_exists('shuffle'))
				shuffle($pass_chars);
			foreach($pass_chars as $chars)
			{
				$n = strlen($chars) - 1;
				$string .= $chars[mt_rand(0, $n)];
			}
		}
		if(strlen($string) > count($pass_chars))
			$string = substr($string, 0, $pass_len);
	}
	else
	{
		if($pass_chars !== false)
		{
			$chars = $pass_chars;
			$n = strlen($pass_chars) - 1;
		}
		else
		{
			$chars = $allchars;
			$n = 61; //strlen($allchars)-1;
		}
		for ($i = 0; $i < $pass_len; $i++)
			$string .= $chars[mt_rand(0, $n)];
	}
	return $string;
}
//alias for randString()
function GetRandomCode($len=8)
{
	return randString($len);
}

function TruncateText($strText, $intLen )
{
	If(strlen($strText) >= $intLen )
		return substr($strText, 0, $intLen)." ...";
	else
		return $strText;
}

function InsertSpaces($sText, $iMaxChar=80, $symbol=" ")
{
	$NewString=$sText;
	if ($iMaxChar>0 && strlen($sText)>$iMaxChar)
	{
		$NewString = ereg_replace("([^] \[\(\)\n\r\t\-\%\!\?\{\}]{".$iMaxChar."})","\\1".$symbol, $NewString);
	}
	return $NewString;
}

function TrimExAll($str,$symbol)
{
	while (substr($str,0,1)==$symbol or substr($str,strlen($str)-1,1)==$symbol)
	{
		$str = TrimEx($str,$symbol);
	}
	return $str;
}

function TrimEx($str,$symbol,$side="both")
{
	$str = trim($str);
	if ($side=="both")
	{
		if (substr($str,0,1) == $symbol) $str = substr($str,1,strlen($str));
		if (substr($str,strlen($str)-1,1) == $symbol) $str = substr($str,0,strlen($str)-1);
	}
	elseif ($side=="left")
	{
		if (substr($str,0,1) == $symbol) $str = substr($str,1,strlen($str));
	}
	elseif ($side=="right")
	{
		if (substr($str,strlen($str)-1,1) == $symbol) $str = substr($str,0,strlen($str)-1);
	}
	return $str;
}

function utf8win1251($s)
{
	$out="";$c1="";$byte2=false;
	for ($c=0;$c<strlen($s);$c++)
	{
		$i=ord($s[$c]);
		if ($i<=127) $out.=$s[$c];
		if ($byte2)
		{
			$new_c2=($c1&3)*64+($i&63);
			$new_c1=($c1>>2)&5;
			$new_i=$new_c1*256+$new_c2;
			if ($new_i==1025) $out_i=168; else
			if ($new_i==1105) $out_i=184; else $out_i=$new_i-848;
			$out.=chr($out_i);
			$byte2=false;
		}
		if (($i>>5)==6)
		{
			$c1=$i;
			$byte2=true;
		}
	}
	return $out;
}

function ToUpper($str)
{
	if(!defined("BX_CUSTOM_TO_UPPER_FUNC"))
	{
		if(defined("BX_UTF"))
		{
			return strtoupper($str);
		}
		else
		{
			return strtoupper(strtr($str, "йцукенгшщзхъэждлорпавыф€чсмитьбюЄ", "…÷” ≈Ќ√Ўў«’ЏЁ∆ƒЋќ–ѕј¬џ‘я„—ћ»“№Ѕё®"));
		}
	}
	else
	{
		$func = BX_CUSTOM_TO_UPPER_FUNC;
		return $func($str);
	}
}

function ToLower($str)
{
	if(!defined("BX_CUSTOM_TO_LOWER_FUNC"))
	{
		if(defined("BX_UTF"))
		{
			return strtolower($str);
		}
		else
		{
			return strtolower(strtr($str, "…÷” ≈Ќ√Ўў«’ЏЁ∆ƒЋќ–ѕј¬џ‘я„—ћ»“№Ѕё®", "йцукенгшщзхъэждлорпавыф€чсмитьбюЄ"));
		}
	}
	else
	{
		$func = BX_CUSTOM_TO_LOWER_FUNC;
		return $func($str);
	}
}

/**********************************
	 онвертаци€ текста дл€ EMail
**********************************/
function convert_code_tag_for_email($text="", $arMsg=array())
{
	if (strlen($text)<=0) return;

	$text = stripslashes($text);
	$text = preg_replace("#<#", "&lt;", $text);
	$text = preg_replace("#>#", "&gt;", $text);
	$text = preg_replace("#^(.*?)$#", "   \\1", $text);

	$s1 = "--------------- ".$arMsg["MAIN_CODE_S"]." -------------------";
	$s2 = str_repeat("-", strlen($s1));
	$text = "\n\n>".$s1."\n".$text."\n>".$s2."\n\n";
	return $text;
}

function PrepareTxtForEmail($text, $lang=false, $convert_url_tag=true, $convert_image_tag=true)
{
	$text = Trim($text);
	if(strlen($text)<=0)
		return "";

	if($lang===false)
		$lang = LANGUAGE_ID;

	$arMsg = IncludeModuleLangFile(__FILE__, $lang, true);

	$text = preg_replace("#<code(\s+[^>]*>|>)(.+?)</code(\s+[^>]*>|>)#is", "[code]\\2[/code]", $text);
	$text = preg_replace("#\[code(\s+[^\]]*\]|\])(.+?)\[/code(\s+[^\]]*\]|\])#ies", "convert_code_tag_for_email('\\2', \$arMsg)", $text);

	$text = preg_replace("/^(\r|\n)+?(.*)$/", "\\2", $text);
	$text = preg_replace("#<b>(.+?)</b>#is", "\\1", $text);
	$text = preg_replace("#<i>(.+?)</i>#is", "\\1", $text);
	$text = preg_replace("#<u>(.+?)</u>#is", "_\\1_", $text);
	$text = preg_replace("#\[b\](.+?)\[/b\]#is", "\\1", $text);
	$text = preg_replace("#\[i\](.+?)\[/i\]#is", "\\1", $text);
	$text = preg_replace("#\[u\](.+?)\[/u\]#is", "_\\1_", $text);

	$text = preg_replace("#<(/?)quote(.*?)>#is", "[\\1quote]", $text);

	$s = "-------------- ".$arMsg["MAIN_QUOTE_S"]." -----------------";
	$text = preg_replace("#\[quote(.*?)\]#is", "\n>".$s."\n", $text);
	$text = preg_replace("#\[/quote(.*?)\]#is", "\n>".str_repeat("-", strlen($s))."\n", $text);

	if($convert_url_tag)
	{
		$text = preg_replace("#<a[^>]*href=[\"']?([^>\"' ]+)[\"']?[^>]*>(.+?)</a>#is", "\\2 (URL: \\1)", $text);
		$text = preg_replace("#\[url\](\S+?)\[/url\]#is", "(URL: \\1)", $text);
		$text = preg_replace("#\[url\s*=\s*(\S+?)\s*\](.*?)\[\/url\]#is", "\\2 (URL: \\1)", $text);
	}

	if($convert_image_tag)
	{
		$text = preg_replace("#<img[^>]*src=[\"']?([^>\"' ]+)[\"']?[^>]*>#is", " (IMAGE: \\1) ", $text);
		$text = preg_replace("#\[img\](.+?)\[/img\]#is", " (IMAGE: \\1) ", $text);
	}

	$text = preg_replace("#<ul(\s+[^>]*>|>)#is", "\n", $text);
	$text = preg_replace("#<ol(\s+[^>]*>|>)#is", "\n", $text);
	$text = preg_replace("#<li(\s+[^>]*>|>)#is", " [*] ", $text);
	$text = preg_replace("#</li>#is", "", $text);
	$text = preg_replace("#</ul>#is", "\n\n", $text);
	$text = preg_replace("#</ol>#is", "\n\n", $text);

	$text = preg_replace("#\[list\]#is", "\n", $text);
	$text = preg_replace("#\[/list\]#is", "\n", $text);

	$text = preg_replace("#<br>#is", "\n", $text);
	$text = preg_replace("#<wbr>#is", "", $text);

	//$text = preg_replace("#<.+?".">#", "", $text);

	$text = str_replace("&quot;", "\"", $text);
	$text = str_replace("&#092;", "\\", $text);
	$text = str_replace("&#036;", "\$", $text);
	$text = str_replace("&#33;", "!", $text);
	$text = str_replace("&#39;", "'", $text);
	$text = str_replace("&lt;", "<", $text);
	$text = str_replace("&gt;", ">", $text);
	$text = str_replace("&nbsp;", " ", $text);
	$text = str_replace("&#124;", '|', $text);
	$text = str_replace("&amp;", "&", $text);

	return $text;
}

/**********************************
	 онвертаци€ текста в HTML
**********************************/

// используетс€ как вспомогательна€ функци€ дл€ TxtToHTML
function delete_special_symbols($text, $replace="")
{
	static $arr = array(
		"\x1",		// спецсимвол дл€ преобразовани€ URL'ов протокола http, https, ftp
		"\x2",		// спецсимвол дл€ пробела ($iMaxStringLen)
		"\x3",		// спецсимвол дл€ преобразовани€ URL'ов протокола mailto
		"\x4",		// спецсимвол замен€ющий \n (используетс€ дл€ преобразовани€ <code>)
		"\x5",		// спецсимвол замен€ющий \r (используетс€ дл€ преобразовани€ <code>)
		"\x6",		// спецсимвол замен€ющий пробел (используетс€ дл€ преобразовани€ <code>)
		"\x7",		// спецсимвол замен€ющий табул€цию (используетс€ дл€ преобразовани€ <code>)
		"\x8",		// спецсимвол замен€ющий слэш "\"
		);
	return str_replace($arr, $replace, $text);
}

// используетс€ как вспомогательна€ функци€ дл€ TxtToHTML
function convert_code_tag_for_html_before($text = "")
{
	if (strlen($text)<=0) return;
	$text = stripslashes($text);
	$text = str_replace(chr(2), "", $text);
	$text = str_replace("\n", chr(4), $text);
	$text = str_replace("\r", chr(5), $text);
	$text = str_replace(" ", chr(6), $text);
	$text = str_replace("\t", chr(7), $text);
	$text = str_replace("http", "!http!", $text);
	$text = str_replace("https", "!https!", $text);
	$text = str_replace("ftp", "!ftp!", $text);
	$text = str_replace("@", "!@!", $text);

	$text = str_replace(Array("[","]"), Array(chr(16), chr(17)), $text);

	$return = "[code]".$text."[/code]";

	return $return;
}

// используетс€ как вспомогательна€ функци€ дл€ TxtToHTML
function convert_code_tag_for_html_after($text = "", $code_table_class, $code_head_class, $code_body_class, $code_textarea_class)
{
	if (strlen($text)<=0) return;
	$text = stripslashes($text);
	$code_mess = GetMessage("MAIN_CODE");
	$text = str_replace("!http!", "http", $text);
	$text = str_replace("!https!", "https", $text);
	$text = str_replace("!ftp!", "ftp", $text);
	$text = str_replace("!@!", "@", $text);

	//$text = str_replace(Array(chr(9), chr(10)), Array("[","]")  , $text);

	$return = "<table class='$code_table_class'><tr><td class='$code_head_class'>$code_mess</td></tr><tr><td class='$code_body_class'><textarea class='$code_textarea_class' contentEditable=false cols=60 rows=15 wrap=virtual>$text</textarea></td></tr></table>";

	return $return;
}

// используетс€ как вспомогательна€ функци€ дл€ TxtToHTML
function convert_open_quote_tag($quote_table_class, $quote_head_class, $quote_body_class)
{
	global $QUOTE_ERROR, $QUOTE_OPENED, $QUOTE_CLOSED, $MESS;
	$QUOTE_OPENED++;
	return "<table class='$quote_table_class' width='95%' border='0' cellpadding='3' cellspacing='1'><tr><td class='".$quote_head_class."'>".GetMessage("MAIN_QUOTE")."</td></tr><tr><td class='".$quote_body_class."'>";
}

// используетс€ как вспомогательна€ функци€ дл€ TxtToHTML
function convert_close_quote_tag()
{
	global $QUOTE_ERROR, $QUOTE_OPENED, $QUOTE_CLOSED;
	if ($QUOTE_OPENED == 0)
	{
		$QUOTE_ERROR++;
		return;
	}
	$QUOTE_CLOSED++;
	return "</td></tr></table>";
}

// используетс€ как вспомогательна€ функци€ дл€ TxtToHTML
function convert_quote_tag($text="", $quote_table_class, $quote_head_class, $quote_body_class)
{
	global $QUOTE_ERROR, $QUOTE_OPENED, $QUOTE_CLOSED;
	if (strlen($text)<=0) return;
	$text = stripslashes($text);
	$txt = $text;
	$txt = preg_replace("#\[quote\]#ie", "convert_open_quote_tag('$quote_table_class', '$quote_head_class', '$quote_body_class')", $txt);
	$txt = preg_replace("#\[/quote\]#ie", "convert_close_quote_tag()", $txt);
	if (($QUOTE_OPENED==$QUOTE_CLOSED) && ($QUOTE_ERROR==0))
	{
		return $txt;
	}
	else
	{
		return $text;
	}
}

// используетс€ как вспомогательна€ функци€ дл€ TxtToHTML
function extract_url($s)
{
	$x = 0;
	while(strpos(",}])>.", substr($s, -1, 1))!==false)
	{
		$s2 = substr($s, -1, 1);
		$s = substr($s, 0, strlen($s)-1);
	}
	$res = chr(1).$s."/".chr(1).$s2;
	return $res;
}

// используетс€ как вспомогательна€ функци€ дл€ TxtToHTML
function convert_to_href($url, $link_class="", $event1="", $event2="", $event3="", $script="")
{
	$url = stripslashes($url);
	$goto = $url;
	if (strlen($event1)>0 || strlen($event2)>0)
	{
		$script = strlen($script)>0 ? $script : "/bitrix/redirect.php";
		$goto = $script.
			"?event1=".urlencode($event1).
			"&event2=".urlencode($event2).
			"&event3=".urlencode($event3).
			"&goto=".urlencode($goto);
	}
	$s = "<a class=\"".$link_class."\" href=\"".delete_special_symbols($goto)."\">".$url."</a>";
	return $s;
}

// используетс€ как вспомогательна€ функци€ дл€ TxtToHTML
function convert_to_mailto($s, $link_class="")
{
	$s = stripslashes($s);
	$s = "<a class=\"".$link_class."\" href=\"mailto:".delete_special_symbols($s)."\" title=\"".GetMessage("MAIN_MAILTO")."\">".$s."</a>";
	return $s;
}

function TxtToHTML(
	$str,										// текст дл€ преобразовани€
	$bMakeUrls				= true,				// true - преобразовавыть URL в <a href="URL">URL</a>
	$iMaxStringLen			= 0,				// максимальна€ длина фразы без пробелов или символов перевода каретки
	$QUOTE_ENABLED			= "N",				// Y - преобразовать <QUOTE>...</QUOTE> в рамку цитаты
	$NOT_CONVERT_AMPERSAND	= "Y",				// Y - не преобразовывать символ "&" в "&amp;"
	$CODE_ENABLED			= "N",				// Y - преобразовать <CODE>...</CODE> в readonly textarea
	$BIU_ENABLED			= "N",				// Y - преобразовать <B>...</B> и т.д. в соответствующие HTML тэги
	$quote_table_class		= "quotetable",		// css класс на таблицу цитаты
	$quote_head_class		= "tdquotehead",	// css класс на первую TD таблицы цитаты
	$quote_body_class		= "tdquote",		// css класс на вторую TD таблицы цитаты
	$code_table_class		= "codetable",		// css класс на таблицу кода
	$code_head_class		= "tdcodehead",		// css класс на первую TD таблицы кода
	$code_body_class		= "tdcodebody",		// css класс на вторую TD таблицы кода
	$code_textarea_class	= "codetextarea",	// css класс на textarea в таблице кода
	$link_class				= "txttohtmllink",	// css класс на ссылках
	$arUrlEvent				= array()			// массив в нем если заданы ключи EVENT1, EVENT2, EVENT3 то ссылки будут через
												// $arUrlEvent["SCRIPT"] (по умолчанию равен "/bitrix/redirect.php")
	)
{

	global $QUOTE_ERROR, $QUOTE_OPENED, $QUOTE_CLOSED;
	$QUOTE_ERROR = $QUOTE_OPENED = $QUOTE_CLOSED = 0;

	$str = delete_special_symbols($str);

	//echo "\n<br>=====================\n<br><pre>".htmlspecialchars($str)."</pre>\n<br>=======================\n<br>";

	// вставим спецсимвол chr(2) там где в дальнейшем необходимо вставить пробел
	if($iMaxStringLen>0)
	{
		$str = InsertSpaces($str, $iMaxStringLen, chr(2));
	}

	// \ => chr(8)
	$str = str_replace("\\", chr(8), $str); // спецсимвол замен€ющий слэш "\"


	// <quote>...</quote> => [quote]...[/quote]
	if ($QUOTE_ENABLED=="Y")
	{
		$str = preg_replace("#(?:<|\[)(/?)quote(.*?)(?:>|\])#is", " [\\1quote]", $str);
	}

	// <code>...</code> => [code]...[/code]
	// \n => chr(4)
	// \r => chr(5)
	if ($CODE_ENABLED=="Y")
	{
		$str = preg_replace("#<code(\s+[^>]*>|>)(.+?)</code(\s+[^>]*>|>)#is", "[code]\\2[/code]", $str);
		$str = preg_replace("#\[code(\s+[^\]]*\]|\])(.+?)\[/code(\s+[^\]]*\]|\])#ies", "convert_code_tag_for_html_before('\\2')", $str);
	}

	// <b>...</b> => [b]...[/b]
	// <i>...</i> => [i]...[/i]
	// <u>...</u> => [u]...[/u]
	if ($BIU_ENABLED=="Y")
	{
		$str = preg_replace("#<b(\s+[^>]*>|>)(.+?)</b(\s+[^>]*>|>)#is", "[b]\\2 [/b]", $str);
		$str = preg_replace("#<i(\s+[^>]*>|>)(.+?)</i(\s+[^>]*>|>)#is", "[i]\\2 [/i]", $str);
		$str = preg_replace("#<u(\s+[^>]*>|>)(.+?)</u(\s+[^>]*>|>)#is", "[u]\\2 [/u]", $str);
	}

	// URL => chr(1).URL."/".chr(1)
	// EMail => chr(3).E-Mail.chr(3)
	if($bMakeUrls)
	{
		$str = preg_replace("#((http|https|ftp):\/\/[a-z:,./\#\%=~\\&?*+\[\]_0-9\x01-\x08-]+)#ies", "extract_url('\\1')", $str);
		$str = preg_replace("#([=_\.0-9a-z+~\x01-\x08-]+@([\._0-9a-z\x01-\x08-]+)+\.[a-z]{2,4})#is", chr(3)."\\1".chr(3), $str);
	}



	// конвертаци€ критичных символов
	if ($NOT_CONVERT_AMPERSAND!="Y") $str = str_replace("&", "&amp;", $str);
	static $search=array("<",">","\"","'","%",")","(","+");
	static $replace=array("&lt;","&gt;","&quot;","&#39;","&#37;","&#41;","&#40;","&#43;");
	$str = str_replace($search, $replace, $str);

	// chr(1).URL."/".chr(1) => <a href="URL">URL</a>
	// chr(3).E-Mail.chr(3) => <a href="mailto:E-Mail">E-Mail</a>
	if($bMakeUrls)
	{
		$event1 = $arUrlEvent["EVENT1"];
		$event2 = $arUrlEvent["EVENT2"];
		$event3 = $arUrlEvent["EVENT3"];
		$script = $arUrlEvent["SCRIPT"];
		$str = preg_replace("#\x01([^\n\x01]+?)/\x01#ies", "convert_to_href('\\1', '$link_class', '$event1', '$event2', '$event3', '$script')", $str);
		$str = preg_replace("#\x03([^\n\x03]+?)\x03#ies", "convert_to_mailto('\\1', '$link_class')", $str);
	}

	$str = str_replace("\r\n", "\n", $str);
	$str = str_replace("\n", "<br>", $str);
	$str = preg_replace("# {2}#", "&nbsp;&nbsp;", $str);
	$str = preg_replace("#\t#", "&nbsp;&nbsp;&nbsp;&nbsp;", $str);

	// chr(2) => " "
	if($iMaxStringLen>0)
	{
		$str = str_replace(chr(2), "<wbr>", $str);
	}

	// [quote]...[/quote] => <table>...</table>
	if ($QUOTE_ENABLED=="Y")
	{
		$str = preg_replace("#(\[quote(.*?)\](.*)\[/quote(.*?)\])#ies", "convert_quote_tag('\\1', '$quote_table_class', '$quote_head_class', '$quote_body_class')", $str);
	}

	// [code]...[/code] => <textarea>...</textarea>
	// chr(4) => \n
	// chr(5) => \r
	if ($CODE_ENABLED=="Y")
	{
		$str = preg_replace("#\[code\](.*?)\[/code\]#ies", "convert_code_tag_for_html_after('\\1', '$code_table_class', '$code_head_class', '$code_body_class', '$code_textarea_class')", $str);
		$str = str_replace(chr(4), "\n", $str);
		$str = str_replace(chr(5), "\r", $str);
		$str = str_replace(chr(6), " ", $str);
		$str = str_replace(chr(7), "\t", $str);
		$str = str_replace(chr(16), "[", $str);
		$str = str_replace(chr(17), "]", $str);
	}

	// [b]...[/b] => <b>...</b>
	// [i]...[/i] => <i>...</i>
	// [u]...[/u] => <u>...</u>
	if ($BIU_ENABLED=="Y")
	{
		$str = preg_replace("#\[b\](.*?)\[/b\]#is", "<b>\\1</b>", $str);
		$str = preg_replace("#\[i\](.*?)\[/i\]#is", "<i>\\1</i>", $str);
		$str = preg_replace("#\[u\](.*?)\[/u\]#is", "<u>\\1</u>", $str);
	}

	// chr(8) => \
	$str = str_replace(chr(8), "\\", $str);

	$str = delete_special_symbols($str);

	return $str;
}

/*********************************
	 онвертаци€ HTML в текст
*********************************/

function HTMLToTxt($str, $strSiteUrl="", $aDelete=array(), $maxlen=70)
{
	//get rid of whitespace
	$str = preg_replace("/[\\t\\n\\r]/", " ", $str);

	//replace tags with placeholders
	static $search = array (
				"'<script[^>]*?>.*?</script>'si",
				"'<style[^>]*?>.*?</style>'si",
				"'<select[^>]*?>.*?</select>'si",
				"'&(quot|#34);'i",
//				"'&(amp|#38);'i",
//				"'&(lt|#60);'i",
//				"'&(gt|#62);'i",
//				"'&(nbsp|#160);'i",
				"'&(iexcl|#161);'i",
				"'&(cent|#162);'i",
				"'&(pound|#163);'i",
				"'&(copy|#169);'i",
				"'&#(\d+);'e", // evaluate as php
				);

	static $replace = array (
				"",
				"",
				"",
				"\"",
//				"&",
//				"<",
//				">",
//				" ",
				"\xa1",
				"\xa2",
				"\xa3",
				"\xa9",
				"(intval('\\1')>=848 ? chr(intval('\\1')-848) : chr(intval('\\1')))",
				);

	$str = preg_replace($search, $replace, $str);

	$str = eregi_replace("<[/]{0,1}(b>|i>|u>|em>|small>|strong>)", "", $str);
	$str = eregi_replace("<[/]{0,1}(font|div|span)[^>]*>", "", $str);

	//ищем списки
	$str = eregi_replace("<ul[^>]*>", "\r\n", $str);
	$str = eregi_replace("<li[^>]*>", "\r\n  - ", $str);

	//удалим то что заданно
	for($i = 0; $i<count($aDelete); $i++)
		$str = eregi_replace($aDelete[$i], "", $str);

	//ищем картинки
	$str = eregi_replace('<img[ ]+src[ ]*=[ ]*[\"\'](/[^\"\'>]+)[\"\'][^>]*>', "[".chr(1).$strSiteUrl."\\1".chr(1)."] ", $str);
	$str = eregi_replace('<img[ ]+src[ ]*=[ ]*[\"\']([^\"\'>]+)[\"\'][^>]*>', "[".chr(1)."\\1".chr(1)."] ", $str);

	//ищем ссылки
	$str = eregi_replace('<a[ ]+href[ ]*=[ ]*[\"\'](/[^\"\'>]+)[\"\'][^>]*>([^>]+)</a>', "\\2 [".chr(1).$strSiteUrl."\\1".chr(1)."]", $str);
	$str = eregi_replace('<a[ ]+href[ ]*=[ ]*[\"\']([^\"\'>]+)[\"\'][^>]*>([^>]+)</a>', "\\2 [".chr(1)."\\1".chr(1)."]", $str);

	//ищем <br>
	$str = eregi_replace("<br[^>]*>", "\r\n", $str);

	//ищем <p>
	$str = eregi_replace("<p[^>]*>", "\r\n\r\n", $str);

	//ищем <hr>
	$str = str_replace("<hr>", "\r\n----------------------\r\n", $str);

	//ищем таблицы
	$str = eregi_replace("</{0,1}(thead|tbody)[^>]*>", "", $str);
	$str = eregi_replace("<(/{0,1})th[^>]*>", "<\\1td>", $str);

	$str = eregi_replace("</td>", "\t", $str);
	$str = eregi_replace("</tr>", "\r\n", $str);
	$str = eregi_replace("<table[^>]*>", "\r\n", $str);

	$str = eregi_replace("\r\n[ ]+", "\r\n", $str);

	//мочим вообще все оставшиес€ тэги
	$str = eregi_replace("</{0,1}[^>]+>", "", $str);

	$str = ereg_replace("[ ]+ ", " ", $str);
	$str = str_replace("\t", "    ", $str);

	//переносим длинные строки
	if($maxlen > 0)
		$str = ereg_replace("([^\n\r]{".$maxlen."}[^ \r\n]*[] ])([^\r])","\\1\r\n\\2",$str);

	$str = str_replace(chr(1), " ",$str);
	return trim($str);
}

function FormatText($strText, $strTextType="text")
{
	if(strtolower($strTextType)=="html")
		return $strText;

	return TxtToHtml($strText);
}

function htmlspecialcharsEx($str)
{
	static $search = array("&amp;","&lt;","&gt;","&quot;","<",">","\"");
	static $replace = array("&amp;amp;","&amp;lt;","&amp;gt;","&amp;quot;","&lt;","&gt;","&quot;");
	return str_replace($search, $replace, $str);
}

function htmlspecialcharsback($str)
{
	if(strlen($str)>0)
	{
		$str = str_replace("&lt;", "<", $str);
		$str = str_replace("&gt;", ">", $str);
		$str = str_replace("&quot;", "\"", $str);
		$str = str_replace("&amp;", "&", $str);
	}
	return $str;
}

/*********************************************************************
						‘айлы и каталоги
*********************************************************************/

function CheckDirPath($path, $bPermission=true)
{
	$badDirs=Array();
	$path = str_replace(array("\\", "//"), "/", $path);

	if($path[strlen($path)-1]!="/") //отрежем им€ файла
	{
		$p=bxstrrpos($path, "/");
		$path = substr($path, 0, $p);
	}

	while(strlen($path)>1 && $path[strlen($path)-1]=="/") //отрежем / в конце, если есть
		$path=substr($path, 0, strlen($path)-1);

	$p=bxstrrpos($path, "/");
	while($p>0)
	{
		if(file_exists($path) && is_dir($path))
		{
			if($bPermission)
			{
				if(!is_writable($path))
					@chmod($path, BX_DIR_PERMISSIONS);
			}
			break;
		}
		$badDirs[]=substr($path, $p+1);
		$path = substr($path, 0, $p);
		$p=bxstrrpos($path, "/");
	}

	for($i=count($badDirs)-1; $i>=0; $i--)
	{
		$path = $path."/".$badDirs[$i];
		mkdir($path, BX_DIR_PERMISSIONS);
	}
}

function CopyDirFiles($path_from, $path_to, $ReWrite = True, $Recursive = False, $bDeleteAfterCopy = False, $strExclude = "")
{
	if (strpos($path_to."/", $path_from."/")===0)
		return False;

	if (is_dir($path_from))
	{
		CheckDirPath($path_to."/");
	}
	elseif(is_file($path_from))
	{
		$p = bxstrrpos($path_to, "/");
		$path_to_dir = substr($path_to, 0, $p);
		CheckDirPath($path_to_dir."/");

		if (file_exists($path_to) && !$ReWrite)
			return False;

		@copy($path_from, $path_to);
		if(is_file($path_to))
			@chmod($path_to, BX_FILE_PERMISSIONS);

		if ($bDeleteAfterCopy)
			@unlink($path_from);

		return True;
	}
	else
	{
		return True;
	}

	if ($handle = @opendir($path_from))
	{
		while (($file = readdir($handle)) !== false)
		{
			if ($file == "." || $file == "..") continue;

			if (strlen($strExclude)>0 && substr($file, 0, strlen($strExclude))==$strExclude) continue;

			if (is_dir($path_from."/".$file) && $Recursive)
			{
				CopyDirFiles($path_from."/".$file, $path_to."/".$file, $ReWrite, $Recursive, $bDeleteAfterCopy, $strExclude);
				if ($bDeleteAfterCopy)
					@rmdir($path_from."/".$file);
			}
			elseif (is_file($path_from."/".$file))
			{
				if (file_exists($path_to."/".$file) && !$ReWrite)
					continue;

				@copy($path_from."/".$file, $path_to."/".$file);
				@chmod($path_to."/".$file, BX_FILE_PERMISSIONS);

				if($bDeleteAfterCopy)
					@unlink($path_from."/".$file);
			}
		}
		@closedir($handle);

		if ($bDeleteAfterCopy)
			@rmdir($path_from);
	}
}

function DeleteDirFilesEx($path)
{
	$f = true;
	if(is_file($_SERVER["DOCUMENT_ROOT"].$path))
	{
		if(@unlink($_SERVER["DOCUMENT_ROOT"].$path))
			return True;
		return false;
	}

	if($handle = @opendir($_SERVER["DOCUMENT_ROOT"].$path))
	{
		while(($file = readdir($handle)) !== false)
		{
			if($file == "." || $file == "..") continue;

			if(is_dir($_SERVER["DOCUMENT_ROOT"].$path."/".$file))
			{
				if(!DeleteDirFilesEx($path."/".$file))
					$f = false;
			}
			else
			{
				if(!@unlink($_SERVER["DOCUMENT_ROOT"].$path."/".$file))
					$f = false;
			}
		}
	}

	@closedir($handle);
	if(!@rmdir($_SERVER["DOCUMENT_ROOT"].$path))
		return false;
	else
		return $f;
}

function DeleteDirFiles($frDir, $toDir, $arExept = array())
{
	if(is_dir($frDir))
	{
		$d = dir($frDir);
		while ($entry = $d->read())
		{
			if ($entry=="." || $entry=="..")
				continue;
			if (in_array($entry, $arExept))
				continue;
			@unlink($toDir."/".$entry);
		}
		$d->close();
	}
}

function RewriteFile($abs_path, $strContent)
{
	CheckDirPath($abs_path);
	if(file_exists($abs_path) && !is_writable($abs_path))
		@chmod($abs_path, BX_FILE_PERMISSIONS);
	$fd = fopen($abs_path, "wb");
	if(!fwrite($fd, $strContent)) return false;
	@chmod($abs_path, BX_FILE_PERMISSIONS);
	fclose($fd);
	return true;
}

function GetScriptFileExt()
{
	static $FILEMAN_SCRIPT_EXT = false;
	if($FILEMAN_SCRIPT_EXT !== false)
		return $FILEMAN_SCRIPT_EXT;

	$script_files = COption::GetOptionString("fileman", "~script_files", "php,php3,php4,php5,php6,phtml,pl,asp,aspx,cgi,exe,ico");
	$arScriptFiles = array();
	foreach(explode(",", $script_files) as $ext)
		if(($e = trim($ext)) != "")
			$arScriptFiles[] = $e;

	$FILEMAN_SCRIPT_EXT = $arScriptFiles;
	return $arScriptFiles;
}

function RemoveScriptExtension($name)
{
	$check_name = strtolower($name);
	foreach(GetScriptFileExt() as $ext)
	{
		$ext = strtolower($ext);
		while(($p = strpos($check_name, ".".$ext.".")) !== false)
			$check_name = substr($check_name, 0, $p).substr($check_name, $p+strlen($ext)+1);
		if(($p = strrpos($check_name, '.')) !== false && substr($check_name, $p+1) == $ext)
			$check_name = substr($check_name, 0, $p);
	}
	return $check_name;
}

function HasScriptExtension($name)
{
	$name = strtolower($name);
	foreach(GetScriptFileExt() as $ext)
	{
		$ext = ".".strtolower($ext);
		while(strpos($name, $ext.".") !== false)
			return true;
		if(substr($name, -strlen($ext)) == $ext)
			return true;
	}
	return false;
}

function GetFileExtension($path)
{
	$path = rtrim($path, "\0.\\/+ ");
	$pos = strrpos($path, ".");
	$extension = substr($path, $pos+1);
	return $extension;
}

function GetFileType($path)
{
	$extension = GetFileExtension(strtolower($path));
	switch ($extension)
	{
		case "jpg": case "gif":	case "bmp": case "png":
			$type = "IMAGE";
			break;
		case "swf":
			$type = "FLASH";
			break;
		case "html": case "htm": case "asp": case "php": case "php3": case "php4":
		case "shtml": case "sql": case "txt": case "inc": case "js": case "vbs":
		case "tpl": case "css": case "shtm":
			$type = "SOURCE";
			break;
		default:
			$type = "UNKNOWN";
	}
	return $type;
}

function GetDirectoryIndex($path, $strDirIndex=false)
{ return GetDirIndex($path, $strDirIndex); }

function GetDirIndex($path, $strDirIndex=false)
{
	global $DOCUMENT_ROOT;
	if (strlen($_SERVER["DOCUMENT_ROOT"])<=0) $doc_root = $GLOBALS["DOCUMENT_ROOT"];
	else $doc_root = $_SERVER["DOCUMENT_ROOT"];
	$dir = GetDirPath($path);
	$arrDirIndex = GetDirIndexArray($strDirIndex);
	if (is_array($arrDirIndex) && count($arrDirIndex)>0)
	{
		foreach($arrDirIndex as $page_index)
		{
			if (file_exists($doc_root.$dir.$page_index)) return $page_index;
		}
	}
	return "index.php";
}

function GetDirIndexArray($strDirIndex=false)
{
	$default = "index.php index.html index.htm index.phtml default.html index.php3";
	if($strDirIndex === false && defined("DIRECTORY_INDEX"))
		$strDirIndex = DIRECTORY_INDEX;
	if(trim($strDirIndex) == '')
		$strDirIndex = $default;
	$arrRes = array();
	$arr = explode(" ", $strDirIndex);
	foreach($arr as $page_index)
	{
		$page_index = trim($page_index);
		if($page_index <> '')
			$arrRes[] = $page_index;
	}
	return $arrRes;
}

function GetPagePath($page=false, $get_index_page=true)
{
	if($page===false && $_SERVER["REQUEST_URI"]<>"")
		$page = $_SERVER["REQUEST_URI"];
	if($page===false)
		$page = $_SERVER["SCRIPT_NAME"];

	$found = strpos($page, "?");
	$sPath = ($found? substr($page, 0, $found) : $page);

	//fix for %20 security ussue
	$sPath = urldecode($sPath);

	if(substr($sPath, -1, 1) == "/" && $get_index_page)
		$sPath .= GetDirectoryIndex($sPath);

	$sPath = str_replace(array("<",">","\""),array("&lt;","&gt;","&quot;"), $sPath);

	return Rel2Abs("/", $sPath);
}

function GetDirPath($sPath)
{
	$p = strrpos($sPath, "/");
	if($p===false)
		return '/';

	return (substr($sPath, 0, $p+1));
}

function bxstrrpos($haystack, $needle)
{
	$index = strpos(strrev($haystack), strrev($needle));
	if($index === false)
		return false;
	$index = strlen($haystack) - strlen($needle) - $index;
	return $index;
}

function Rel2Abs($curdir, $relpath)
{
	if(strlen($relpath)<=0)
		return false;

	/*
	if(strpos($relpath, "://")>0)
		return $relpath;
	*/

	$relpath = preg_replace("'[\\\/]+'", "/", $relpath);

	if($relpath[0]=="/" || preg_match("#^[a-z]:/#i", $relpath))
		$res = $relpath;
	else
	{
		$curdir = preg_replace("'[\\\/]+'", "/", $curdir);
		if($curdir[0]!="/" && !preg_match("#^[a-z]:/#i", $curdir))
			$curdir="/".$curdir;
		if($curdir[strlen($curdir)-1]!="/")
			$curdir.="/";
		$res = $curdir.$relpath;
	}

	if(($p = strpos($res, "\0"))!==false)
		$res = substr($res, 0, $p);

	while(strpos($res, "/./")!==false)
		$res = str_replace("/./", "/", $res);

	//$res = preg_replace("'\\.\\.+'", "..", $res); // .......

	//while(($pos=strpos($res, "/.."))!==false) // .......
	while(($pos=strpos($res, "../"))!==false)
	{
		$lp = substr($res, 0, $pos);
		$posl = bxstrrpos($lp, "/");
		if($posl===false)
			return;
		$lp = substr($lp, 0, $posl+1);
		$rp = substr($res, $pos+3);
		//$rp = substr($res, $pos+4); // .......
		$res = $lp.$rp;
	}

	$res = preg_replace("'[\\\/]+'", "/", $res);

	$res = rtrim($res, "\0");

	return $res;
}

/*********************************************************************
						языковые файлы
*********************************************************************/

function GetMessage($name, $aReplace=false)
{
	global $MESS;
	$s = $MESS[$name];
	if($aReplace!==false && is_array($aReplace))
		foreach($aReplace as $search=>$replace)
			$s = str_replace($search, $replace, $s);
	return $s;
}

global $ALL_LANG_FILES;
$ALL_LANG_FILES = Array();
function GetLangFileName($before, $after, $lang=false)
{
	if ($lang===false)
		$lang = LANGUAGE_ID;

	global $ALL_LANG_FILES;
	$ALL_LANG_FILES[] = $before.$lang.$after;
	if(file_exists($before.$lang.$after))
		return $before.$lang.$after;
	if(file_exists($before."en".$after))
		return $before."en".$after;

	if(strpos($before, "/bitrix/modules/")===false)
		return $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/lang/en/tools.php";

	$old_path = Rtrim($before, "/");
	$old_path = substr($old_path, strlen($_SERVER["DOCUMENT_ROOT"]));
	$path = substr($old_path, 16);
	$module = substr($path, 0, strpos($path, "/"));
	$path = substr($path, strpos($path, "/"));
	if(substr($path, -5)=="/lang")
		$path = substr($path, 0, -5);
	IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$module.$path.$after, $lang);
	return $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$module."/lang/".$lang.$path.$after;
}

function __IncludeLang($path, $bReturnArray=false)
{
	global $ALL_LANG_FILES;
	$ALL_LANG_FILES[] = $path;

	if($bReturnArray)
		$MESS = array();
	else
		global $MESS;

	include($path);

	if($bReturnArray)
		return $MESS;
	else
		return true;
}

function IncludeTemplateLangFile($filepath, $lang=false)
{
	global $BX_DOC_ROOT;
	$filepath = rtrim(preg_replace("'[\\\\/]+'", "/", $filepath), "/ ");
	$module_path = "/bitrix/modules/";
	$templ_path = BX_PERSONAL_ROOT."/templates/";
	$module_name = "";
	if(strpos($filepath, $templ_path)!==false)
	{
		$templ_pos = strlen($filepath) - strpos(strrev($filepath), strrev($templ_path));
		$rel_path = substr($filepath, $templ_pos);
		$p = strpos($rel_path, "/");
		if(!$p)
			return;
		$template_name = substr($rel_path, 0, $p);
		$file_name = substr($rel_path, $p+1);
		$p = strpos($file_name, "/");
		if($p>0)
			$module_name = substr($file_name, 0, $p);
	}
	elseif(strpos($filepath, $module_path) !== false)
	{
		$templ_pos = strlen($filepath) - strpos(strrev($filepath), strrev($module_path));
		$rel_path = substr($filepath, $templ_pos);
		$p = strpos($rel_path, "/");
		if(!$p)
			return;
		$module_name = substr($rel_path, 0, $p);
		if(defined("SITE_TEMPLATE_ID"))
			$template_name = SITE_TEMPLATE_ID;
		else
			$template_name = ".default";
		$file_name = substr($rel_path, $p + strlen("/install/templates/"));
	}
	else
		return false;

	$templ_path = $BX_DOC_ROOT.$templ_path;
	$module_path = $BX_DOC_ROOT.$module_path;
	if((substr($file_name, -16) == ".description.php") && $module_name!="")
	{
		if((($lang!==false && $lang!="en" && $lang!="ru") || ($lang===false && LANGUAGE_ID!="en" && LANGUAGE_ID!="ru") && file_exists($module_path.$module_name."/install/templates/lang/en/".$file_name)))
			__IncludeLang($module_path.$module_name."/install/templates/lang/en/".$file_name);

		if(file_exists($module_path.$module_name."/install/templates/lang/".($lang===false?LANGUAGE_ID:$lang)."/".$file_name))
			__IncludeLang($module_path.$module_name."/install/templates/lang/".($lang===false?LANGUAGE_ID:$lang)."/".$file_name);
	}

	if(file_exists($templ_path.$template_name."/lang/".($lang===false?LANGUAGE_ID:$lang)."/".$file_name))
	{
		if(($lang!==false && $lang!="en" && $lang!="ru") || ($lang===false && LANGUAGE_ID!="en" && LANGUAGE_ID!="ru"))
			__IncludeLang($templ_path.$template_name."/lang/en/".$file_name);
		__IncludeLang($templ_path.$template_name."/lang/".($lang===false?LANGUAGE_ID:$lang)."/".$file_name);
		if(substr($file_name, -16) != ".description.php") return ;
	}
	elseif($template_name!=".default" && file_exists($templ_path.".default/lang/".($lang===false?LANGUAGE_ID:$lang)."/".$file_name))
	{
		if(($lang!==false && $lang!="en" && $lang!="ru") || ($lang===false && LANGUAGE_ID!="en" && LANGUAGE_ID!="ru"))
			__IncludeLang($templ_path.".default/lang/en/".$file_name);
		__IncludeLang($templ_path.".default/lang/".($lang===false?LANGUAGE_ID:$lang)."/".$file_name);
	}
	elseif($module_name!="" && file_exists($module_path.$module_name."/install/templates/lang/".($lang===false?LANGUAGE_ID:$lang)."/".$file_name))
	{
		if(($lang!==false && $lang!="en" && $lang!="ru") || ($lang===false && LANGUAGE_ID!="en" && LANGUAGE_ID!="ru"))
			__IncludeLang($module_path.$module_name."/install/templates/lang/en/".$file_name);
		__IncludeLang($module_path.$module_name."/install/templates/lang/".($lang===false?LANGUAGE_ID:$lang)."/".$file_name);
	}
}

function IncludeModuleLangFile($filepath, $lang=false, $bReturnArray=false)
{
	global $BX_DOC_ROOT;
	$filepath = rtrim(preg_replace("'[\\\\/]+'", "/", $filepath), "/ ");
	$module_path = "/modules/";
	if(strpos($filepath, $module_path) !== false)
	{
		$pos = strlen($filepath) - strpos(strrev($filepath), strrev($module_path));
		$rel_path = substr($filepath, $pos);
		$p = strpos($rel_path, "/");
		if(!$p)
			return false;

		$module_name = substr($rel_path, 0, $p);
		$rel_path = substr($rel_path, $p+1);
		$module_path = $BX_DOC_ROOT.BX_ROOT.$module_path.$module_name;
	}
	elseif(strpos($filepath, "/.last_version/") !== false)
	{
		$pos = strlen($filepath) - strpos(strrev($filepath), strrev("/.last_version/"));
		$rel_path = substr($filepath, $pos);
		$module_path = substr($filepath, 0, $pos-1);
	}
	else
	{
		return false;
	}

	if($lang === false)
		$lang = LANGUAGE_ID;

	$arMess = array();
	if(file_exists($module_path."/lang/".$lang."/".$rel_path))
	{
		if($lang <> "en" && $lang <> "ru")
		{
			$arMess = __IncludeLang($module_path."/lang/en/".$rel_path, $bReturnArray);
		}
		$msg = __IncludeLang($module_path."/lang/".$lang."/".$rel_path, $bReturnArray);
		if(is_array($msg))
			$arMess = array_merge($arMess, $msg);
	}
	elseif(file_exists($module_path."/lang/en/".$rel_path))
	{
		$arMess = __IncludeLang($module_path."/lang/en/".$rel_path, $bReturnArray);
	}
	if($bReturnArray)
		return $arMess;
	return true;
}

/*********************************************************************
							ќтладка
*********************************************************************/

function mydump($thing, $maxdepth=-1, $depth=0)
{
	$res="";
	$fmt = sprintf ("%%%ds", 4*$depth);
	$pfx = sprintf ($fmt, "");
	$type = gettype($thing);
	if($type == 'array')
	{
		$n = sizeof($thing);
		$res.="$pfx array($n) => \n";
		foreach(array_keys($thing) as $key)
		{
			$res.=" $pfx"."[".$key."] =>\n";
			$res.=mydump($thing[$key], $maxdepth, $depth+1);
		}
	}
	elseif($type == 'string')
	{
		$n = strlen($thing);
		$res.="$pfx string($n) =>\n";
		$res.="$pfx\"".$thing."\"\n";
	}
	elseif($type == 'object')
	{
		$name = get_class($thing);
		$res.="$pfx object($name) =>\n";
		$methodArray = get_class_methods($name);
		foreach (array_keys($methodArray) as $m)
			$res.=" $pfx method($m) => $methodArray"."[".$m."]\n";
		$classVars = get_class_vars($name);
		foreach(array_keys($classVars) as $v)
		{
			$res.=" $pfx default => $v =>\n";
			$res.=mydump($classVars[$v], $maxdepth, $depth+2);
		}
		$objectVars = get_object_vars($thing);
		foreach (array_keys($objectVars) as $v)
		{
			$res.=" $pfx $v =>\n";
			$res.=mydump($objectVars[$v], $maxdepth, $depth+2);
		}
	}
	elseif ($type == 'boolean')
	{
		if($thing)
			$res.="$pfx boolean(true)\n";
		else
			$res.="$pfx boolean(false)\n";
	}
	else
		$res.="$pfx $type(".$thing.")\n";

	return $res;
}

function SendError($error)
{
	global $HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_COOKIE_VARS, $HTTP_SERVER_VARS, $HTTP_HOST;

	if(defined("ERROR_EMAIL") && strlen(ERROR_EMAIL)>0)
	{
		@mail(ERROR_EMAIL, $HTTP_HOST.": Error!",
			$error.
			"HTTP_GET_VARS:\n".mydump($HTTP_GET_VARS)."\n\n".
			"HTTP_POST_VARS:\n".mydump($HTTP_POST_VARS)."\n\n".
			"HTTP_COOKIE_VARS:\n".mydump($HTTP_COOKIE_VARS)."\n\n".
			"HTTP_SERVER_VARS:\n".mydump($HTTP_SERVER_VARS)."\n\n",
			"From: error@bitrix.ru\r\n".
		    "Reply-To: admin@bitrix.ru\r\n".
    		"X-Mailer: PHP/" . phpversion()
		);
	}
}

function AddMessage2Log($sText, $sModule = "")
{
	if (defined("LOG_FILENAME") && strlen(LOG_FILENAME)>0)
	{
		if (strlen($sText)>0)
		{
			ignore_user_abort(true);
			if ($fp = @fopen(LOG_FILENAME, "ab+"))
			{
				if (flock($fp, LOCK_EX))
				{
					@fwrite($fp, date("Y-m-d H:i:s")." - ".$sModule." - ".$sText."\n");
					if (function_exists("debug_backtrace"))
					{
						$arBacktrace = debug_backtrace();
						$strFunctionStack = "";
						$iterationsCount = min(count($arBacktrace), 4);
						for ($i = 1; $i < $iterationsCount; $i++)
						{
							if (strlen($strFunctionStack)>0)
							{
								$strFunctionStack .= " < ";
							}
							if (strlen($arBacktrace[$i]["class"])>0)
							{
								$strFunctionStack .= $arBacktrace[$i]["class"]."::";
							}
							$strFunctionStack .= $arBacktrace[$i]["function"];
						}
						if (strlen($strFunctionStack)>0)
						{
							@fwrite($fp, "    ".$strFunctionStack."\n");
						}
					}
					@fwrite($fp, "----------\n");
					@fflush($fp);
					@flock($fp, LOCK_UN);
					@fclose($fp);
				}
			}
			ignore_user_abort(false);
		}
	}
}

/*********************************************************************
						 вотирование
*********************************************************************/

function UnQuote($str, $type)
{
	$str = str_replace("\0", "", $str);

	if($type == "syb")
		$str = str_replace("''", "'", $str);
	elseif($type == "gpc")
	{
		$str = str_replace("\\'","'", $str);
		$str = str_replace('\\"','"', $str);
		$str = str_replace("\\\\","\\", $str);
	}

	return $str;
}


function __unquoteitem(&$item, $key, $param = Array())
{
	$first_use = $param["first_use"];
	$type = $param["type"];

	$register_globals = $first_use && ((bool)ini_get("register_globals"));
	if(is_array($item))
	{
		//array_walk($item, '__unquoteitem', Array("type"=>$type, "first_use"=>false));
		foreach($item as $k=>$v)
			__unquoteitem($item[$k], $k, Array("type"=>$type, "first_use"=>false));
		if($register_globals)
		{
			global $$key;
			if(isset($$key) && is_array($$key)/* && count($$key)==count($item)*/)
				//array_walk(&$$key, '__unquoteitem', Array("type"=>$type, "first_use"=>false));
				foreach($$key as $k=>$v)
					__unquoteitem($GLOBALS[$key][$k], $k, Array("type"=>$type, "first_use"=>false));
		}
	}
	else
	{
		if($register_globals)
		{
			global $$key;
			if(isset($$key) && $$key==$item)
				$$key = UnQuote($$key, $type);
		}
		$item = UnQuote($item, $type);
	}
}

function UnQuoteArr(&$arr, $syb = false)
{
	if (is_array($arr))
	{
		if(ini_get("magic_quotes_sybase")==1)
			array_walk($arr, '__unquoteitem', Array("type"=>"syb", "first_use"=>true));
		elseif(ini_get("magic_quotes_gpc")==1)
			array_walk($arr, '__unquoteitem', Array("type"=>"gpc", "first_use"=>true));
		else
			array_walk($arr, '__unquoteitem', Array("type"=>"nulls", "first_use"=>true));
	}
}


function ___UnQuoteArrOld(&$arr, $syb=false)
{
	if (is_array($arr))
	{
		reset($arr);
		while(list($varname, $rptempvar) = each ($arr))
		{
			global $$varname;
			$var=$$varname;
			if(is_array($rptempvar))
			{
				foreach($rptempvar as $key => $val)
				{
					if(is_array($var) && count($var)==count($rptempvar) && $rptempvar[$key]==$var[$key])
						${$varname}[$key]=UnQuote($rptempvar[$key], $syb);
					$arr[$varname][$key]=UnQuote($rptempvar[$key], $syb);
				}
			}
			else
			{
				if($$varname==$arr[$varname])
					$$varname=UnQuote($rptempvar, $syb);
				$arr[$varname]=UnQuote($rptempvar, $syb);
			}
		}
		reset($arr);
	}
}

function UnQuoteAll()
{
	global $HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_COOKIE_VARS;
	UnQuoteArr($_GET);
	UnQuoteArr($_POST);
	UnQuoteArr($_REQUEST);
	UnQuoteArr($_COOKIE);
	UnQuoteArr($HTTP_GET_VARS);
	UnQuoteArr($HTTP_POST_VARS);
	UnQuoteArr($HTTP_COOKIE_VARS);

	if(ini_get("magic_quotes_runtime")==1)
		set_magic_quotes_runtime(0);
}

/*********************************************************************
						ѕрочие функции
*********************************************************************/
function LocalRedirect($url)
{
	global $HTTP_HOST, $APPLICATION, $SERVER_PORT;
	global $oldSiteExpireDate, $SiteExpireDate;

	if(defined("DEMO") && DEMO=="Y" && ($oldSiteExpireDate!=$SiteExpireDate || SITEEXPIREDATE!=OLDSITEEXPIREDATE || strlen($SiteExpireDate)<=0))
		die(GetMessage("TOOLS_TRIAL_EXP"));

	$url = str_replace("&amp;","&",$url);

	$sessid = Get_PHPSESSID();
	$sessname = Get_PHPSESSID_NAME();

	if (strlen($sessid)>0 && false)
	{
		if(strpos($url,$sessname."=")===false)
		{
			$arr_url = explode("#",$url);
			$url_1 = $arr_url[0];
			$url_2 = $arr_url[1];
			if(!(strpos($url, "?") === false))
				$url_1 .= "&".$sessname."=".$sessid;
			else
				$url_1 .= "?".$sessname."=".$sessid;
			$url = $url_1.((strlen($url_2)>0) ? "#".$url_2 : "");
		}
	}
	$arr = explode("?",$url);
	if (strpos($arr[0],"/")===false) $url = $APPLICATION->GetCurDir().$url;

	if(function_exists("getmoduleevents"))
	{
		$db_events = GetModuleEvents("main", "OnBeforeLocalRedirect");
		while($arEvent = $db_events->Fetch())
			ExecuteModuleEvent($arEvent, &$url);
	}

	// http response splitting defence
	$url = str_replace ("\r", "", $url);
	$url = str_replace ("\n", "", $url);

	CHTTP::SetStatus("302 Found");

	if(
		strtolower(substr($url,0,7))=="http://" ||
		strtolower(substr($url,0,8))=="https://" ||
		strtolower(substr($url,0,6))=="ftp://")
	{
		header("Request-URI: $url");
		header("Content-Location: $url");
		header("Location: $url");
	}
	else
	{
		if ($SERVER_PORT!="80" && $SERVER_PORT != 443 && $SERVER_PORT>0 && strpos($HTTP_HOST,":".$SERVER_PORT)<=0)
			$HTTP_HOST .= ":".$SERVER_PORT;

		$protocol = (CMain::IsHTTPS() ? "https" : "http");

		header("Request-URI: $protocol://$HTTP_HOST$url");
		header("Content-Location: $protocol://$HTTP_HOST$url");
		header("Location: $protocol://$HTTP_HOST$url");
	}

	if(function_exists("getmoduleevents"))
	{
		$db_events = GetModuleEvents("main", "OnLocalRedirect");
		while($arEvent = $db_events->Fetch())
			ExecuteModuleEvent($arEvent);
	}
	exit;
}


function FindUserID($tag_name, $tag_value, $user_name="", $form_name = "form1", $tag_size = "3", $tag_maxlength="", $button_value = "...", $tag_class="typeinput", $button_class="tablebodybutton", $search_page="/bitrix/admin/user_search.php")
{
	global $APPLICATION;
	$tag_name_x = preg_replace("/([^a-z0-9]|\[|\])/is", "x", $tag_name);
	if($APPLICATION->GetGroupRight("main") >= "R")
	{
		$strReturn = "
<input type=\"text\" name=\"".$tag_name."\" id=\"".$tag_name."\" value=\"".$tag_value."\" size=\"".$tag_size."\" maxlength=\"".$tag_maxlength."\" class=\"".$tag_class."\">
<iframe style=\"width:0px; height:0px; border:0px\" src=\"javascript:''\" name=\"hiddenframe".$tag_name."\" id=\"hiddenframe".$tag_name."\"></iframe>
<input class=\"".$button_class."\" type=\"button\" name=\"FindUser\" id=\"FindUser\" OnClick=\"window.open('".$search_page."?lang=".LANGUAGE_ID."&FN=".$form_name."&FC=".$tag_name."', '', 'scrollbars=yes,resizable=yes,width=760,height=500,top='+Math.floor((screen.height - 560)/2-14)+',left='+Math.floor((screen.width - 760)/2-5));\" value=\"".$button_value."\">
<span id=\"div_".$tag_name."\">".$user_name."</span>
<script type=\"text/javascript\">
";
		if($user_name=="")
			$strReturn.= "var tv".$tag_name_x."='';\n";
		else
			$strReturn.= "var tv".$tag_name_x."='".addslashes($tag_value)."';\n";

		$strReturn.= "
function Ch".$tag_name_x."()
{
	var DV_".$tag_name_x.";
	DV_".$tag_name_x." = document.getElementById(\"div_".$tag_name."\");
	if (tv".$tag_name_x."!=document.".$form_name."['".$tag_name."'].value)
	{
		tv".$tag_name_x."=document.".$form_name."['".$tag_name."'].value;
		if (tv".$tag_name_x."!='')
		{
			DV_".$tag_name_x.".innerHTML = '<i>".GetMessage("MAIN_WAIT")."</i>';
			document.getElementById(\"hiddenframe".$tag_name."\").src='/bitrix/admin/get_user.php?ID=' + tv".$tag_name_x."+'&strName=".$tag_name."&lang=".LANG.(defined("ADMIN_SECTION") && ADMIN_SECTION===true?"&admin_section=Y":"")."';
		}
		else
			DV_".$tag_name_x.".innerHTML = '';
	}
	setTimeout(function(){Ch".$tag_name_x."()},1000);
}
Ch".$tag_name_x."();
//-->
</script>
";
	}
	else
	{
		$strReturn = "
			<input type=\"text\" name=\"$tag_name\" id=\"$tag_name\" value=\"$tag_value\" size=\"$tag_size\" maxlength=\"strMaxLenght\">
			<input type=\"button\" name=\"FindUser\" id=\"FindUser\" OnClick=\"window.open('".$search_page."?lang=".LANGUAGE_ID."&FN=$form_name&FC=$tag_name', '', 'scrollbars=yes,resizable=yes,width=760,height=560,top='+Math.floor((screen.height - 560)/2-14)+',left='+Math.floor((screen.width - 760)/2-5));\" value=\"$button_value\">
			$user_name
			";
	}
	return $strReturn;
}

function GetWhoisLink($ip, $class="tablebodylink")
{
	$title = str_replace("#SERVICE#", "Name Intelligence", GetMessage("WHOIS_SERVICE"));
	$URL = "http://www.whois.sc/#IP#";
	$URL = str_replace("#IP#",urlencode($ip),$URL);
	$str = "<a href='$URL' class='".$class."' target='_blank' title='$title'>".htmlspecialchars($ip)."</a>";
	return $str;
}

function IsIE()
{
	global $HTTP_USER_AGENT;
	if (ereg('(MSIE|Internet Explorer) ([0-9]).([0-9])+', $HTTP_USER_AGENT, $version) && strpos($HTTP_USER_AGENT, "Opera")==false)
	{
		if (intval($version[2])>0)
			return DoubleVal($version[2].".".$version[3]);
		return false;
	}
	else
	{
		return false;
	}
}

function GetCountryByID($id, $lang=LANGUAGE_ID)
{
	$msg = IncludeModuleLangFile(__FILE__, $lang, true);
	return $msg["COUNTRY_".$id];
}

function GetCountryArray($lang=LANGUAGE_ID)
{
	$arMsg = IncludeModuleLangFile(__FILE__, $lang, true);
	$arr = array();
	foreach($arMsg as $id=>$country)
		if(strpos($id, "COUNTRY_") === 0)
			$arr[intval(substr($id, 8))] = $country;
	asort($arr);
	$arCountry = array("reference_id"=>array_keys($arr), "reference"=>array_values($arr));
	return $arCountry;
}

function minimumPHPVersion($vercheck)
{
	$minver = explode(".", $vercheck);
	$curver = explode(".", phpversion());
	if ((IntVal($curver[0]) < IntVal($minver[0])) || ((IntVal($curver[0]) == IntVal($minver[0])) && (IntVal($curver[1]) < IntVal($minver[1]))) || ((IntVal($curver[0]) == IntVal($minver[0])) && (IntVal($curver[1]) == IntVal($minver[1])) && (IntVal($curver[2]) < IntVal($minver[2]))))
		return false;
	else
		return true;
}

function FormDecode()
{
	global $HTTP_ENV_VARS, $HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_POST_FILES, $HTTP_COOKIE_VARS, $HTTP_SERVER_VARS;
	$superglobals = Array('_GET', '_SESSION', '_POST', '_COOKIE', '_REQUEST', '_FILES', '_SERVER', 'GLOBALS', '_ENV');

	for($i=0; $i<count($superglobals); $i++)
	{
		unset($_REQUEST[$superglobals[$i]]);
		unset($_GET[$superglobals[$i]]);
		unset($_POST[$superglobals[$i]]);
		unset($_COOKIE[$superglobals[$i]]);
	}

	$register_globals = (bool) ini_get("register_globals");
	if (!$register_globals)
	{
		$HTTP_ENV_VARS = $_ENV;
		while (list($key, $val) = @each($_ENV))
			if(!in_array($key, $superglobals))
				$GLOBALS[$key] = $val;

		$HTTP_GET_VARS = $_GET;
		while (list($key, $val) = @each($_GET))
			if(!in_array($key, $superglobals))
				$GLOBALS[$key] = $val;

		$HTTP_POST_VARS = $_POST;
		while (list($key, $val) = @each($_POST))
			if(!in_array($key, $superglobals))
				$GLOBALS[$key] = $val;

		$HTTP_POST_FILES = $_FILES;
		while (list($key, $val) = @each($_FILES))
			if(!in_array($key, $superglobals))
				$GLOBALS[$key] = $val;

		$HTTP_COOKIE_VARS = $_COOKIE;
		while (list($key, $val) = @each($_COOKIE))
			if(!in_array($key, $superglobals))
				$GLOBALS[$key] = $val;

		$HTTP_SERVER_VARS = $_SERVER;
		while (list($key, $val) = @each($_SERVER))
			if(!in_array($key, $superglobals))
				$GLOBALS[$key] = $val;

		reset($_ENV);
		reset($_GET);
		reset($_POST);
		reset($_FILES);
		reset($_COOKIE);
		reset($_SERVER);
	}
}

function QueryGetData($SITE, $PORT, $PATH, $QUERY_STR, &$errno, &$errstr, $sMethod="GET", $sProto="")
{
	$ob = new CHTTP();
	$ob->Query(
			$sMethod,
			$SITE,
			$PORT,
			$PATH . ($sMethod == 'GET' ? ((strpos($PATH, '?') === false ? '?' : '&') . $QUERY_STR) : ''),
			$sMethod == 'POST' ? $QUERY_STR : false,
			$sProto
		);

	$errno = $ob->errno;
	$errstr = $ob->errstr;

	return $ob->result;
}

function xmlize_xmldata($data)
{
	$data = trim($data);
	$vals = $index = $array = array();
	$parser = xml_parser_create("ISO-8859-1");
	xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
	xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
	xml_parse_into_struct($parser, $data, $vals, $index);
	xml_parser_free($parser);

	$i = 0;

	$tagname = $vals[$i]['tag'];
	if (isset($vals[$i]['attributes']))
	{
		$array[$tagname]['@'] = $vals[$i]['attributes'];
	}
	else
	{
		$array[$tagname]['@'] = array();
	}

	$array[$tagname]["#"] = xml_depth_xmldata($vals, $i);

	return $array;
}

function xml_depth_xmldata($vals, &$i)
{
	$children = array();

	if (isset($vals[$i]['value']))
	{
		array_push($children, $vals[$i]['value']);
	}

	while (++$i < count($vals))
	{
		switch ($vals[$i]['type'])
		{
		   case 'open':
				if (isset($vals[$i]['tag']))
				{
					$tagname = $vals[$i]['tag'];
				}
				else
				{
					$tagname = '';
				}

				if (isset($children[$tagname]))
				{
					$size = sizeof($children[$tagname]);
				}
				else
				{
					$size = 0;
				}

				if (isset($vals[$i]['attributes']))
				{
					$children[$tagname][$size]['@'] = $vals[$i]["attributes"];
				}
				$children[$tagname][$size]['#'] = xml_depth_xmldata($vals, $i);
			break;

			case 'cdata':
				array_push($children, $vals[$i]['value']);
			break;

			case 'complete':
				$tagname = $vals[$i]['tag'];

				if(isset($children[$tagname]))
				{
					$size = sizeof($children[$tagname]);
				}
				else
				{
					$size = 0;
				}

				if(isset($vals[$i]['value']))
				{
					$children[$tagname][$size]["#"] = $vals[$i]['value'];
				}
				else
				{
					$children[$tagname][$size]["#"] = '';
				}

				if (isset($vals[$i]['attributes']))
				{
					$children[$tagname][$size]['@'] = $vals[$i]['attributes'];
				}
			break;

			case 'close':
				return $children;
			break;
		}

	}

	return $children;
}

function Help($module="", $anchor="", $help_file="")
{
	global $APPLICATION, $IS_HELP;
	if (strlen($help_file)<=0) $help_file = basename($APPLICATION->GetCurPage());
	if (strlen($anchor)>0) $anchor = "#".$anchor;

	if($IS_HELP!==true)
	{
		$height = "500";
		//$width = "545";
		$width = "780";
		echo "<script type=\"text/javascript\">
			<!--
			function Help(file, module, anchor)
			{
				window.open('".BX_ROOT."/tools/help_view.php?local=Y&file='+file+'&module='+module+'&lang=".LANGUAGE_ID."'+anchor, '','scrollbars=yes,resizable=yes,width=".$width.",height=".$height.",top='+Math.floor((screen.height - ".$height.")/2-14)+',left='+Math.floor((screen.width - ".$width.")/2-5));
			}
			//-->
			</script>";
		$IS_HELP=true;
	}
	echo "<a href=\"javascript:Help('".urlencode($help_file)."','".$module."','".$anchor."')\" title='".GetMessage("TOOLS_HELP")."'><img src='".BX_ROOT."/images/main/show_help.gif' width='16' height='16' border='0' alt='".GetMessage("TOOLS_HELP")."' align='absbottom' vspace='2' hspace='1'></a>";
}

function InitBVar(&$var)
{
	$var = ($var=="Y") ? "Y" : "N";
}

function init_get_params($url)
{
	return InitURLParam($url);
}

function InitURLParam($url=false)
{
	if ($url===false) $url = $_SERVER["REQUEST_URI"];
	$start = strpos($url, "?");
	if ($start!==false)
	{
		$end = strpos($url, "#");
		$length = ($end>0) ? $end-$start-1 : strlen($url);
		$params = substr($url, $start+1, $length);
		parse_str($params, $_GET);
		parse_str($params, $HTTP_GET_VARS);
		parse_str($params, $arr);
		$_REQUEST += $arr;
		$GLOBALS += $arr;
	}
}

function _ShowHtmlspec($str)
{
	$str = str_replace("<br>", "\n", $str);
	$str = str_replace("<br />", "\n", $str);
	$str = htmlspecialchars($str);
	$str = nl2br($str);
	$str = str_replace("&amp;", "&", $str);
	return $str;
}

function ShowNote($strNote, $cls="notetext")
{
	if($strNote <> "")
	{
		$GLOBALS["APPLICATION"]->IncludeComponent(
			"bitrix:system.show_message",
			".default",
			Array(
				"MESSAGE"=> $strNote,
				"STYLE" => $cls,
			),
			null,
			array(
				"HIDE_ICONS" => "Y"
			)
		);
	}
}

function ShowError($strError, $cls="errortext")
{
	if($strError <> "")
	{
		$GLOBALS["APPLICATION"]->IncludeComponent(
			"bitrix:system.show_message",
			".default",
			Array(
				"MESSAGE"=> $strError,
				"STYLE" => $cls,
			),
			null,
			array(
				"HIDE_ICONS" => "Y"
			)
		);
	}
}

function ShowMessage($arMess)
{
	if(!is_array($arMess))
		$arMess=Array("MESSAGE" => $arMess, "TYPE" => "ERROR");

	if($arMess["MESSAGE"] <> "")
	{
		$GLOBALS["APPLICATION"]->IncludeComponent(
			"bitrix:system.show_message",
			".default",
			Array(
				"MESSAGE"=> $arMess["MESSAGE"],
				"STYLE" => ($arMess["TYPE"]=="OK"?"notetext":"errortext"),
			),
			null,
			array(
				"HIDE_ICONS" => "Y"
			)
		);
	}
}

function DeleteParam($ParamNames)
{
    if(count($_GET) < 1)
        return "";

	$string = "";
	foreach($_GET as $key=>$val)
	{
        $bFound = false;
        foreach($ParamNames as $param)
		{
			if(strcasecmp($param, $key) == 0)
			{
				$bFound = true;
				break;
			}
		}

        if($bFound == false)
        {
			if(!is_array($val))
			{
				if(strlen($string) > 0)
					$string .= '&';
				$string .= urlencode($key).'='.urlencode($val);
			}
			else
			{
				$string.= (empty($string) ? "" : "&").array2param($key, $val);
			}
        }
	}
	return $string;
}

// провер€ет EMail вида "Bitrix <admin@bitrix.ru>" и "admin@bitrix.ru"
function check_email($email)
{
	$email = trim($email);
	global $SERVER_SOFTWARE;
	if(preg_match("#.*?[<\[\(](.*?)[>\]\)].*#i", $email, $arr) && strlen($arr[1])>0) $email = $arr[1];
	if(eregi("^[=_\.0-9a-z+~-]+@(([-0-9a-z_]+\.)+)([a-z]{2,10}$)", $email, $check))
		if(true || strpos($SERVER_SOFTWARE, "(Win32)")>0 || getmxrr($check[1].$check[2], $temp) || gethostbyname($check[1].".".$check[2])!=$check[1].".".$check[2])
			return true;

	return false;
}

function initvar($varname, $value='')
{
	global $$varname;
	if(!isset($$varname))
		$$varname=$value;
}

function Get_PHPSESSID()
{
	$s = ini_get("session.name");
	global $$s;
	return $$s;
}

function Get_PHPSESSID_NAME()
{
	$s = ini_get("session.name");
	return $s;
}

function roundEx($value, $prec=0)
{
	$eps = 1.00/pow(10, $prec+4);
	return round(doubleval($value)+$eps, $prec);
}

function roundDB($value, $len=18, $dec=4)
{
	if($value>=0)
		$value = "0".$value;
	$value = roundEx(DoubleVal($value), $len);
	$value = sprintf("%01.".$dec."f", $value);
	if($len>0 && strlen($value)>$len-$dec)
		$value = trim(substr($value, 0, $len-$dec), ".");
	return $value;
}

function bitrix_sessid()
{
	return md5(session_id());
}

function check_bitrix_sessid($varname='sessid')
{
	global $USER;
	if(defined("BITRIX_STATIC_PAGES") && (!is_object($USER) || !$USER->IsAuthorized()))
		return true;
	else
		return $_REQUEST[$varname] == bitrix_sessid();
}

function bitrix_sessid_get($varname='sessid')
{
	return $varname."=".bitrix_sessid();
}

function bitrix_sessid_post($varname='sessid')
{
	return '<input type="hidden" name="'.$varname.'" id="'.$varname.'" value="'.bitrix_sessid().'" />';
}

function print_url($strUrl, $strText, $sParams="")
{
	return (strlen($strUrl) <= 0? $strText : "<a href=\"".$strUrl."\" ".$sParams.">".$strText."</a>");
}

function IncludeAJAX()
{
	global $APPLICATION;
	$APPLICATION->AddHeadString('<script type="text/javascript">var ajaxMessages = {wait:"'.CUtil::JSEscape(GetMessage('AJAX_WAIT')).'"}</script>', true);
	$APPLICATION->AddHeadString('<script src="/bitrix/js/main/cphttprequest.js"></script>', true);
}

class CUtil
{
	function addslashes($s)
	{
		return str_replace(
			array("\\",   "\"",      "'"),
			array("\\\\", "\\"."\"", "\\'"),
			$s
		);
	}

	function JSEscape($s)
	{
		return str_replace(
			array("\\",   "'",   "\r\n", "\r", "\n",       "</script"),
			array("\\\\", "\\'", "\n",   "\n", "\\n'+\n'", "</s'+'cript"),
			$s
		);
	}

	function PhpToJSObject($arData)
	{
		if(is_array($arData))
		{
			if($arData == array_values($arData))
			{
				foreach($arData as $key => $value)
				{
					$arData[$key] = CUtil::PhpToJSObject($value);
				}
				return "[".implode(",", $arData)."]";
			}

			$res = "\n{";
			$first = true;
			foreach($arData as $key => $value)
			{
				if($first)
					$first = false;
				else
					$res .= ",\n";
				$res .= "'".CUtil::addslashes($key)."':".CUtil::PhpToJSObject($value);
			}
			$res .= "\n}";

			return $res;
		}
		elseif(is_bool($arData))
		{
			if($arData === true)
				return 'true';
			else
				return 'false';
		}
		else
			return "'".CUtil::JSEscape($arData)."'";
	}

	function JSPostUnescape()
	{
		array_walk($_POST, array('CUtil', '__UnEscape'));
		array_walk($_REQUEST, array('CUtil', '__UnEscape'));
	}

	function __UnEscape(&$item, $key)
	{
		if(is_array($item))
			array_walk($item, array('CUtil', '__UnEscape'));
		else
		{
			if(strpos($item, "%u") !== false)
				$item = $GLOBALS["APPLICATION"]->UnJSEscape($item);
		}
	}

	function decodeURIComponent(&$item)
	{
		if(is_array($item))
		{
			array_walk($item, array('CUtil', 'decodeURIComponent'));
		}
		else
		{
			if(LANG_CHARSET != "UTF-8")
				$item = $GLOBALS["APPLICATION"]->ConvertCharset($item, "UTF-8", LANG_CHARSET);
		}
	}
}

class CHTTP
{
	var $url = '';
 	var $status = 0;
	var $result = '';
	var $headers = array();
	var $cookies = array();

	var $http_timeout = 120;

	var $user_agent;

	var $follow_redirect = false;
	var $errno;
	var $errstr;

	function CHTTP()
	{
		$this->user_agent = 'BitrixSM ' . __CLASS__ . ' class';
	}

	function Get($url)
	{
		if ($this->HTTPQuery('GET', $url))
		{
			return $this->result;
		}
		return false;
	}

	function Post($url, $arPostData)
	{
		$postdata = '';
		if (is_array($arPostData))
		{
			foreach ($arPostData as $k => $v)
			{
				if (strlen($postdata) > 0)
				{
					$postdata .= '&';
				}
				$postdata .= urlencode($k) . '=' . urlencode($v);
			}
		}

		if($this->HTTPQuery('POST', $url, $postdata))
		{
			return $this->result;
		}
		return false;
	}

	function HTTPQuery($method, $url, $postdata = '')
	{
		$arUrl = false;
		do {
			$this->url = $url;
			$arUrl = $this->ParseURL($url, $arUrl);
			if (!$this->Query($method, $arUrl['host'], $arUrl['port'], $arUrl['path_query'], $postdata, $arUrl['proto']))
			{
				return false;
			}
		} while ($this->follow_redirect && array_key_exists('Location', $this->headers) && strlen($url = $this->headers['Location']) > 0);

		return true;
	}

	function Query($method, $host, $port, $path, $postdata = false, $proto = '')
	{
		$this->status = 0;
		$this->result = '';
		$this->headers = array();
		$this->cookies = array();

		$fp = fsockopen($proto.$host, $port, $this->errno, $this->errstr, $this->http_timeout);
		if ($fp)
		{
			$strRequest = "$method $path HTTP/1.0\r\n";
			$strRequest .= "User-Agent: {$this->user_agent}\r\n";
			$strRequest .= "Accept: */*\r\n";
			$strRequest .= "Host: $host\r\n";
			$strRequest .= "Accept-Language: en\r\n";
			if ($method == 'POST')
			{
				$strRequest.= "Content-type: application/x-www-form-urlencoded\r\n";
				$strRequest.= "Content-length: " .
					(function_exists('mb_strlen')? mb_strlen($postdata, 'latin1'): strlen($postdata)) . "\r\n";
			}
			$strRequest .= "\r\n";
			if ($method == 'POST')
			{
				$strRequest.= $postdata;
				$strRequest.= "\r\n";
			}

			fputs($fp, $strRequest);

			$result = '';
			while ($line = @fread($fp, 4096))
			{
				$result .= $line;
			}
			list($headers, $body) = explode("\r\n\r\n", $result, 2);
			$this->result = $body;
			$this->ParseHeaders($headers);

			fclose($fp);

			return true;
		}

		$GLOBALS['APPLICATION']->ThrowException(
					GetMessage('HTTP_CLIENT_ERROR_CONNECT',
					array(
						'%ERRSTR%' => $this->errstr,
						'%ERRNO%' => $this->errno,
						'%HOST%' => $host,
						'%PORT%' => $port,
					)
				)
			);
		return false;
	}

	function ParseURL($url, $arUrlOld = false)
	{
		$arUrl = parse_url($url);

		if (is_array($arUrlOld))
		{
			if (!array_key_exists('scheme', $arUrl))
			{
				$arUrl['scheme'] = $arUrlOld['scheme'];
			}

			if (!array_key_exists('host', $arUrl))
			{
				$arUrl['host'] = $arUrlOld['host'];
			}

			if (!array_key_exists('port', $arUrl))
			{
				$arUrl['port'] = $arUrlOld['port'];
			}
		}

		$arUrl['proto'] = '';
		if (array_key_exists('scheme', $arUrl))
		{
			$arUrl['scheme'] = strtolower($arUrl['scheme']);
		}
		else
		{
			$arUrl['scheme'] = 'http';
		}

		if (!array_key_exists('port', $arUrl))
		{
			if ($arUrl['scheme'] == 'https')
			{
				$arUrl['port'] = 443;
			}
			else
			{
				$arUrl['port'] = 80;
			}
		}

		if ($arUrl['scheme'] == 'https')
		{
			$arUrl['proto'] = 'ssl://';
		}

		$arUrl['path_query'] = array_key_exists('path', $arUrl) ? $arUrl['path'] : '/';
		if (array_key_exists('query', $arUrl) && strlen($arUrl['query']) > 0)
		{
			$arUrl['path_query'] .= '?' . $arUrl['query'];
		}

		return $arUrl;
	}

	function ParseHeaders($strHeaders)
	{
		$arHeaders = explode("\n", $strHeaders);
		foreach ($arHeaders as $k => $header)
		{
			if ($k == 0)
			{
				if (preg_match(',HTTP\S+ (\d+),', $header, $arFind))
				{
					$this->status = intval($arFind[1]);
				}
			}
			else
			{
				$arHeader = explode(':', $header, 2);
				if ($arHeader[0] == 'Set-Cookie')
				{
					if (($pos = strpos($arHeader[1], ';')) !== false && $pos > 0)
					{
						$cookie = trim(substr($arHeader[1], 0, $pos));
					}
					else
					{
						$cookie = trim($arHeader[1]);
					}
					$arCookie = explode('=', $cookie, 2);
					$this->cookies[$arCookie[0]] = rawurldecode($arCookie[1]);
				}
				else
				{
					$this->headers[$arHeader[0]] = trim($arHeader[1]);
				}
			}
		}
	}

	function setFollowRedirect($follow)
	{
		$this->follow_redirect = $follow;
	}

	/*public static*/
	function sGet($url, $follow_redirect = false) //static get
	{
		$ob = new CHTTP();
		$ob->setFollowRedirect($follow_redirect);
		return $ob->Get($url);
	}

	/*public static*/
	function sPost($url, $arPostData, $follow_redirect = false) //static post
	{
		$ob = new CHTTP();
		$ob->setFollowRedirect($follow_redirect);
		return $ob->Post($url, $arPostData);
	}

	/*public static*/
	function SetStatus($status)
	{
		$bCgi = (stristr(php_sapi_name(), "cgi") !== false);
		$bFastCgi = ($bCgi && (array_key_exists('FCGI_ROLE', $_SERVER) || array_key_exists('FCGI_ROLE', $_ENV)));
		if($bCgi && !$bFastCgi)
			header("Status: ".$status);
		else
			header($_SERVER["SERVER_PROTOCOL"]." ".$status);
	}
}


function GetMenuTypes($site=false, $default_value=false)
{
	if($default_value === false)
		$default_value = "left=".GetMessage("main_tools_menu_left").",top=".GetMessage("main_tools_menu_top");

	$mt = COption::GetOptionString("fileman", "menutypes", $default_value, $site);
	if (!$mt)
		return Array();

	$armt_ = unserialize(stripslashes($mt));
	$armt = Array();
	if (is_array($armt_))
	{
		foreach($armt_ as $key => $title)
		{
			$key = trim($key);
			if (strlen($key) == 0)
				continue;
			$armt[$key] = trim($title);
		}
		return $armt;
	}

	$armt_ = explode(",", $mt);
	for ($i = 0, $c = count($armt_); $i < $c; $i++)
	{
		$pos = strpos($armt_[$i], '=');
		if ($pos === false)
			continue;
		$key = trim(substr($armt_[$i], 0, $pos));
		if (strlen($key) == 0)
			continue;
		$armt[$key] = trim(substr($armt_[$i], $pos + 1));
	}
	return $armt;
}

function SetMenuTypes($armt, $site = '', $description = false)
{
	COption::SetOptionString('fileman', "menutypes", addslashes(serialize($armt)), $description, $site);
}

function ParseFileContent($filesrc)
{
	/////////////////////////////////////
	// найдем пролог, эпилог, заголовок
	/////////////////////////////////////
	$filesrc = trim($filesrc);

	$php_doubleq = false;
	$php_singleq = false;
	$php_comment = false;
	$php_star_comment = false;
	$php_line_comment = false;

	if(substr($filesrc, 0, 2)=="<?")
	{
		$p = 2;
		while($p < strlen($filesrc))
		{
			if(substr($filesrc, $p, 2)=="?>" && !$php_doubleq && !$php_singleq && !$php_star_comment)
			{
				$p+=2;
				break;
			}
			elseif(!$php_comment && substr($filesrc, $p, 2)=="//" && !$php_doubleq && !$php_singleq)
			{
				$php_comment = $php_line_comment = true;
				$p++;
			}
			elseif($php_line_comment && (substr($filesrc, $p, 1)=="\n" || substr($filesrc, $p, 1)=="\r"))
			{
				$php_comment = $php_line_comment = false;
			}
			elseif(!$php_comment && substr($filesrc, $p, 2)=="/*" && !$php_doubleq && !$php_singleq)
			{
				$php_comment = $php_star_comment = true;
				$p++;
			}
			elseif($php_star_comment && substr($filesrc, $p, 2)=="*/")
			{
				$php_comment = $php_star_comment = false;
				$p++;
			}
			elseif(!$php_comment)
			{
				if(($php_doubleq || $php_singleq) && substr($filesrc, $p, 2)=="\\\\")
				{
					$p++;
				}
				elseif(!$php_doubleq && substr($filesrc, $p, 1)=='"')
				{
					$php_doubleq=true;
				}
				elseif($php_doubleq && substr($filesrc, $p, 1)=='"' && substr($filesrc, $p-1, 1)!='\\')
				{
					$php_doubleq=false;
				}
				elseif(!$php_doubleq)
				{
					if(!$php_singleq && substr($filesrc, $p, 1)=="'")
					{
						$php_singleq=true;
					}
					elseif($php_singleq && substr($filesrc, $p, 1)=="'" && substr($filesrc, $p-1, 1)!='\\')
					{
						$php_singleq=false;
					}
				}
			}

			$p++;
		}

		$prolog = substr($filesrc, 0, $p);
		$filesrc = substr($filesrc, $p);
	}
	elseif(preg_match("'(.*?<title>.*?</title>)(.*)$'is", $filesrc, $reg))
	{
		$prolog = $reg[1];
		$filesrc= $reg[2];
	}

	$title = false;
	if(strlen($prolog)>0 && preg_match("'\\\$APPLICATION->SetTitle\(\"(.*?)(?<!\\\\)\"\);'i", $prolog, $regs))
		$title = UnEscapePHPString($regs[1]);
	elseif(preg_match("'<title[^>]*>([^>]+)</title[^>]*>'i", $prolog, $regs))
		$title = $regs[1];
	elseif(preg_match("'<title[^>]*>([^>]+)</title[^>]*>'i", $filesrc, $regs))
		$title = $regs[1];

	$arPageProps = array();
	if (strlen($prolog)>0)
	{
		preg_match_all("'\\\$APPLICATION->SetPageProperty\(\"(.*?)(?<!\\\\)\" *, *\"(.*?)(?<!\\\\)\"\);'i", $prolog, $out);
		if (count($out[0])>0)
		{
			for ($i1 = 0; $i1 < count($out[0]); $i1++)
			{
				$arPageProps[UnEscapePHPString($out[1][$i1])] = UnEscapePHPString($out[2][$i1]);
			}
		}
	}

	if(substr($filesrc, -2)=="?".">")
	{
		$p = strlen($filesrc) - 2;
		$php_start = "<"."?";
		while(($p > 0) && (substr($filesrc, $p, 2) != $php_start))
			$p--;
		$epilog = substr($filesrc, $p);
		$filesrc = substr($filesrc, 0, $p);
	}

	return Array(
			"PROLOG"=>$prolog,
			"TITLE"=>$title,
			"PROPERTIES"=>$arPageProps,
			"CONTENT"=>$filesrc,
			"EPILOG"=>$epilog
			);
}

function EscapePHPString($str)
{
	$str = str_replace("\\", "\\\\", $str);
	$str = str_replace("\$", "\\\$", $str);
	$str = str_replace("\"", "\\"."\"", $str);
	return $str;
}

function UnEscapePHPString($str)
{
	$str = str_replace("\\\\", "\\", $str);
	$str = str_replace("\\\$", "\$", $str);
	$str = str_replace("\\\"", "\"", $str);
	return $str;
}

?>
