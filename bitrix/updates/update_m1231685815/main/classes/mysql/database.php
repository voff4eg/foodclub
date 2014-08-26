<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/database.php");

/********************************************************************
*	����� ��� ������ � MySQL
********************************************************************/
class CDatabase extends CAllDatabase
{
	var $DBName;
	var $version;
	var $DBHost;
	var $DBLogin;
	var $DBPassword;
	var $bConnected;
	var $cntQuery;
	var $timeQuery;
	var $arQueryDebug;

	function GetVersion()
	{
		if($this->version)
			return $this->version;

		$rs = $this->Query("SELECT VERSION() as R", false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
		if($ar = $rs->Fetch())
		{
			$version = trim($ar["R"]);
			preg_match("#[0-9]+\.[0-9]+\.[0-9]+#", $version, $arr);
			$version = $arr[0];
			$this->version = $version;
			return $version;
		}
		return false;
	}

	function StartTransaction()
	{
		$this->Query("START TRANSACTION");
	}

	function Commit()
	{
		$this->Query("COMMIT", true);
	}

	function Rollback()
	{
		$this->Query("ROLLBACK", true);
	}

	//����������� � ����� ������
	function Connect($DBHost, $DBName, $DBLogin, $DBPassword)
	{
		$this->type="MYSQL";
		$this->DBHost = $DBHost;
		$this->DBName = $DBName;
		$this->DBLogin = $DBLogin;
		$this->DBPassword = $DBPassword;
		$this->bConnected = false;
		if(defined("DELAY_DB_CONNECT") && DELAY_DB_CONNECT===true)
			return true;
		else
			return $this->DoConnect();
	}

	function DoConnect()
	{
		if($this->bConnected)
			return;
		$this->bConnected = true;

		if (!defined("DBPersistent")) define("DBPersistent",true);
		if (DBPersistent)
		{
			$this->db_Conn = @mysql_pconnect($this->DBHost, $this->DBLogin, $this->DBPassword);
		}
		else
		{
			$this->db_Conn = @mysql_connect($this->DBHost, $this->DBLogin, $this->DBPassword);
		}
		if(!($this->db_Conn))
		{
			if (DBPersistent) $s = "mysql_pconnect"; else $s = "mysql_connect";
			if($this->debug || (@session_start() && $_SESSION["SESS_AUTH"]["ADMIN"]))
			{
				echo "<br><font color=#ff0000>Error! ".$s."('-', '-', '-')</font><br>".mysql_error()."<br>";
			}
			else
				SendError("Error! ".$s."('-', '-', '-')\n".mysql_error()."\n");

			return false;
		}


		if(!mysql_select_db($this->DBName, $this->db_Conn))
		{
			if($this->debug || (@session_start() && $_SESSION["SESS_AUTH"]["ADMIN"]))
				echo "<br><font color=#ff0000>Error! mysql_select_db(".$this->DBName.")</font><br>".mysql_error($this->db_Conn)."<br>";
			else
				SendError("Error! mysql_select_db(".$this->DBName.")\n".mysql_error($this->db_Conn)."\n");

			return false;
		}

		$this->cntQuery = 0;
		$this->timeQuery = 0;
		$this->arQueryDebug = array();

		global $DB, $USER, $APPLICATION;
		if(file_exists($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/php_interface/after_connect.php"))
			include($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/php_interface/after_connect.php");

		return true;
	}

	//������ ������ � ���� ������
	function Query($strSql, $bIgnoreErrors=false, $error_position="")
	{
		$this->DoConnect();
		$this->db_Error="";

		if($this->DebugToFile || $this->ShowSqlStat)
		{
			list($usec, $sec) = explode(" ", microtime());
			$start_time = ((float)$usec + (float)$sec);
		}

		$result = @mysql_query($strSql, $this->db_Conn);

		if($this->DebugToFile || $this->ShowSqlStat)
		{
			list($usec, $sec) = explode(" ",microtime());
			$end_time = ((float)$usec + (float)$sec);
			$exec_time = round($end_time-$start_time, 10);

			if($this->ShowSqlStat)
			{
				$this->cntQuery++;
				$this->timeQuery+=$exec_time;
				$this->arQueryDebug[] = array(
					"QUERY"	=>$strSql,
					"TIME"	=>$exec_time,
					"TRACE"	=>(function_exists("debug_backtrace")? debug_backtrace():false),
				);
			}

			if($this->DebugToFile)
			{
				$fp = fopen($_SERVER["DOCUMENT_ROOT"]."/mysql_debug.sql","ab+");
				$str = "TIME: ".$exec_time." SESSION: ".session_id()." \n";
				$str .= $strSql."\n\n";
				$str .= "----------------------------------------------------\n\n";
				fputs($fp, $str);
				@fclose($fp);
			}
		}

		if(!$result)
		{
			$this->db_Error = mysql_error();
			if(!$bIgnoreErrors)
			{
				AddMessage2Log($error_position." MySql Query Error: ".$strSql." [".$this->db_Error."]", "main");
				if ($this->DebugToFile)
				{
					$fp = fopen($_SERVER["DOCUMENT_ROOT"]."/mysql_debug.sql","ab+");
					fputs($fp,"SESSION: ".session_id()." ERROR: ".$this->db_Error."\n\n----------------------------------------------------\n\n");
					@fclose($fp);
				}

				if($this->debug || (@session_start() && $_SESSION["SESS_AUTH"]["ADMIN"]))
				{
					echo $error_position."<br><font color=#ff0000>MySQL Query Error: ".htmlspecialchars($strSql)."</font>[".htmlspecialchars($this->db_Error)."]<br>";
				}
				else
				{
					$error_position = eregi_replace("<br>","\n",$error_position);
					SendError($error_position."\nMySQL Query Error:\n".$strSql." \n [".$this->db_Error."]\n---------------\n\n");
				}

				if(file_exists($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/php_interface/dbquery_error.php"))
				{
					include($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/php_interface/dbquery_error.php");
				}
				elseif(file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/dbquery_error.php"))
				{
					include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/dbquery_error.php");
				}
				else
				{
					die("MySQL Query Error!");
				}
				die();

			}
			return false;
		}
		return new CDBResult($result);
	}

	//������ ������ � ���� ������. ��� MySql ������ ������ ;-)
	function QueryLong($strSql, $bIgnoreErrors = false)
	{
		return $this->Query($strSql, $bIgnoreErrors);
	}

	function CurrentTimeFunction()
	{
		return "now()";
	}

	function CurrentDateFunction()
	{
		return "CURRENT_DATE";
	}

	function DateFormatToDB($format, $field = false)
	{
		static $search  = array("YYYY", "MM", "DD", "HH", "MI", "SS");
		static $replace = array("%Y", "%m", "%d", "%H", "%i", "%s");
		$format = str_replace($search, $replace, $format);
		if($field === false)
		{
			return $format;
		}
		else
		{
			return "DATE_FORMAT(".$field.", '".$format."')";
		}
	}

	function DateToCharFunction($strFieldName, $strType="FULL", $lang=false, $bSearchInSitesOnly=false)
	{
		static $CACHE=array();
		$id = $strType.",".$lang.",".$bSearchInSitesOnly;
		if(!array_key_exists($id,$CACHE))
			$CACHE[$id] = $this->DateFormatToDB(CLang::GetDateFormat($strType, $lang, $bSearchInSitesOnly));
		return "DATE_FORMAT(".$strFieldName.", '".$CACHE[$id]."')";
	}

	function CharToDateFunction($strValue, $strType="FULL", $lang=false)
	{
		return "'".CDatabase::FormatDate($strValue, CLang::GetDateFormat($strType, $lang), "Y-M-D H:I:S")."'";
	}

	//	1		���� date1 > date2
	//	0		���� date1 = date2
	//	-1		���� date1 < date2
	function CompareDates($date1, $date2)
	{
		$s_date1 = $this->CharToDateFunction($date1);
		$s_date2 = $this->CharToDateFunction($date2);
		$strSql = "
			SELECT
				if($s_date1 > $s_date2, 1,
					if ($s_date1 < $s_date2, -1,
						if ($s_date1 = $s_date2, 0, 'x')
				)) as RES
			";
		$z = $this->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
		$zr = $z->Fetch();
		return $zr["RES"];
	}

	function LastID()
	{
		$this->DoConnect();
		return mysql_insert_id($this->db_Conn);
	}

	//������������� �� ��
	function Disconnect()
	{
		if(!DBPersistent && $this->bConnected)
		{
			$this->bConnected = false;
			mysql_close($this->db_Conn);
		}
	}

	function PrepareFields($strTableName, $strPrefix = "str_", $strSuffix = "")
	{
		$this->DoConnect();
		//$db_result = $this->Query("SHOW COLUMNS FROM ".$strTableName);

		$db_result = mysql_list_fields($this->DBName, $strTableName, $this->db_Conn);
		if ($db_result > 0)
		{
			$intNumFields = mysql_num_fields($db_result);
			while (--$intNumFields >= 0)
			{
				$strFieldName = mysql_field_name($db_result, $intNumFields);
				global $$strFieldName;
				$strVarName = $strPrefix.$strFieldName.$strSuffix;
				global $$strVarName;
				switch (mysql_field_type($db_result, $intNumFields))
				{
					case "int":
						$$strVarName = IntVal($$strFieldName);
						break;
					case "real":
						$$strVarName = DoubleVal($$strFieldName);
						break;
					default:
						$$strVarName = $this->ForSql($$strFieldName);
				}
			}
		}
	}


	function PrepareInsert($strTableName, $arFields, $strFileDir="", $lang=false)
	{
		$this->DoConnect();
		$strInsert1 = "";
		$strInsert2 = "";

		$db_result = mysql_list_fields($this->DBName, $strTableName, $this->db_Conn);
		if($db_result > 0)
		{
			$intNumFields = mysql_num_fields($db_result);
			while(--$intNumFields >= 0)
			{
				$strColumnName = mysql_field_name($db_result, $intNumFields);
				$value = $arFields[$strColumnName];
				if(isset($value))
				{
/*
					//������ ����� ���� ��� ���������� ������, ����� �� ���������� - ����� ������� �������������� ���������
					if(is_array($value))
					{
						if(strlen($value["name"])>0 || strlen($value["del"])>0 || strlen($value["description"])>0)
						{
							$res = CFile::SaveFile($value, $strFileDir);
							if($res!==false && strlen($strFileDir)>0)
							{
								$strInsert1 .= ", ".$strColumnName;
								$strInsert2 .= ",  ".$res;
							}
						}
					}

					else
*/
					if($value === false)
					{
						$strInsert1 .= ", ".$strColumnName;
						$strInsert2 .= ",  "."NULL ";
					}
					else
					{
						$strInsert1 .= ", ".$strColumnName;
						if(mysql_field_type($db_result, $intNumFields) == "datetime")
						{
							if(strlen($value)<=0)
								$strInsert2 .= ", NULL ";
							else
								$strInsert2 .= ", '".$this->FormatDate($value, CLang::GetDateFormat("FULL", $lang), "Y-M-D H:I:S")."'";
						}
						elseif(mysql_field_type($db_result, $intNumFields) == "date")
						{
							if(strlen($value)<=0)
								$strInsert2 .= ", NULL ";
							else
								$strInsert2 .= ", '".$this->FormatDate($value, CLang::GetDateFormat("SHORT", $lang), "Y-M-D")."'";
						}
						else
						{
							switch (mysql_field_type($db_result, $intNumFields))
							{
								case "int":
									$value = IntVal($value);
									break;
								case "real":
									$value = DoubleVal($value);
									break;
								default:
									$value = $this->ForSql($value);
							}
							$strInsert2 .= ", '".$value."'";
						}
					}
				}
				elseif(is_set($arFields, "~".$strColumnName))
				{
					$strInsert1 .= ", ".$strColumnName;
					$strInsert2 .= ", ".$arFields["~".$strColumnName];
				}
			}
		}

		if($strInsert1!="")
		{
			$strInsert1 = substr($strInsert1, 2);
			$strInsert2 = substr($strInsert2, 2);
		}
		return array($strInsert1, $strInsert2);
	}

	function PrepareUpdate($strTableName, $arFields, $strFileDir="", $lang = false)
	{
		return $this->PrepareUpdateBind($strTableName, $arFields, $strFileDir, $lang, $arBinds);
	}

	function PrepareUpdateBind($strTableName, $arFields, $strFileDir, $lang, &$arBinds)
	{
		$this->DoConnect();
		$arBinds = array();
		$strUpdate = "";

		$db_result = mysql_list_fields($this->DBName, $strTableName, $this->db_Conn);
		if($db_result > 0)
		{
			$intNumFields = mysql_num_fields($db_result);
			while(--$intNumFields >= 0)
			{
				$strColumnName = mysql_field_name($db_result, $intNumFields);
				$value = $arFields[$strColumnName];
				if(isset($value))
				{
/*
					//������ ����� ���� ��� ���������� ������, ����� �� ���������� - ����� ������� �������������� ���������
					if(is_array($value))
					{
						if(strlen($value["name"])>0 || strlen($value["del"])>0 || is_set($value, "description"))
						{
							$res = CFile::SaveFile($value, $strFileDir);
							if($res!==false && strlen($strFileDir)>0)
								$strUpdate .= ", ".$strColumnName." = ".$res;
						}
					}
					else
*/					
					if($value === false)
					{
						$strUpdate .= ", ".$strColumnName." = NULL";
					}
					else
					{
						switch (mysql_field_type($db_result, $intNumFields))
						{
							case "int":
								$value = IntVal($value);
								break;
							case "real":
								$value = DoubleVal($value);
								break;
							case "datetime":
								if(strlen($value)<=0)
									$value = "NULL";
								else
									$value = "'".$this->FormatDate($value, CLang::GetDateFormat("FULL", $lang), "Y-M-D H:I:S")."'";
								break;
							case "date":
								if(strlen($value)<=0)
									$value = "NULL";
								else
									$value = "'".$this->FormatDate($value, CLang::GetDateFormat("SHORT", $lang), "Y-M-D")."'";
								break;
							default:
								$value = "'".$this->ForSql($value)."'";
						}
						$strUpdate .= ", ".$strColumnName." = ".$value;
					}
				}
				elseif(is_set($arFields, "~".$strColumnName))
				{
					$strUpdate .= ", ".$strColumnName." = ".$arFields["~".$strColumnName];
				}
			}

			if($strUpdate!="")
				$strUpdate = substr($strUpdate, 2);
		}

		return $strUpdate;
	}

	function Insert($table, $arFields, $error_position="", $DEBUG=false, $EXIST_ID="", $ignore_errors=false)
	{
		if(is_array($arFields))
		{
			$str1 = "";
			$str2 = "";
			foreach($arFields as $field => $value)
			{
				$str1 .= $field.", ";
				if(strlen($value) <= 0)
					$str2 .= "'".$value."', ";
				else
					$str2 .= $value.", ";
			}
			$str1 = TrimEx($str1,",");
			$str2 = TrimEx($str2,",");
			if (strlen($EXIST_ID)>0)
			{
				$strSql = "INSERT INTO ".$table."(ID,".$str1.") VALUES ('".$this->ForSql($EXIST_ID)."',".$str2.")";
			}
			else
			{
				$strSql = "INSERT INTO ".$table."(".$str1.") VALUES (".$str2.")";
			}
			if ($DEBUG) echo "<br>".$strSql."<br>";
			$this->Query($strSql, $ignore_errors, $error_position);
			if (strlen($EXIST_ID)>0)
			{
				$ID = $EXIST_ID;
			}
			else
			{
				$ID = $this->LastID();
			}
			return $ID;
		}
		else return false;
	}

	function Update($table, $arFields, $WHERE="", $error_position="", $DEBUG=false, $ignore_errors=false, $additional_check=true)
	{
		$rows = 0;
		if(is_array($arFields))
		{
			$str = "";
			foreach($arFields as $field => $value)
			{
				if (strlen($value)<=0)
					$str .= $field." = '', ";
				else
					$str .= $field." = ".$value.", ";
			}
			$str = TrimEx($str,",");
			$strSql = "UPDATE ".$table." SET ".$str." ".$WHERE;
			if ($DEBUG) echo "<br>".$strSql."<br>";
			$w = $this->Query($strSql, $ignore_errors, $error_position);
			$rows = $w->AffectedRowsCount();
			if ($DEBUG) echo "affected_rows = ".$rows."<br>";
			if (intval($rows)<=0 && $additional_check)
			{
				$w = $this->Query("SELECT 'x' FROM ".$table." ".$WHERE, $ignore_errors, $error_position);
				if ($w->Fetch()) $rows = $w->SelectedRowsCount();
				if ($DEBUG) echo "num_rows = ".$rows."<br>";
			}
		}
		return $rows;
	}

	function Add($tablename, $arFields, $arCLOBFields = Array(), $strFileDir="")
	{
		global $DB;
		$arInsert = $DB->PrepareInsert($tablename, $arFields, $strFileDir);
		$strSql =
			"INSERT INTO ".$tablename."(".$arInsert[0].") ".
			"VALUES(".$arInsert[1].")";
		//echo $strSql."<hr>";
		//die();
		$DB->Query($strSql);
		return $DB->LastID();
	}

	function ForSql($strValue, $iMaxLength=0)
	{
		if(!defined("BX_USE_ESCAPE_FUNC"))
		{
			if(function_exists("mysql_real_escape_string"))
				define("BX_USE_ESCAPE_FUNC", 1);
			else
				define("BX_USE_ESCAPE_FUNC", 2);
		}

		if($iMaxLength>0)
			$strValue = substr($strValue, 0, $iMaxLength);

		if(BX_USE_ESCAPE_FUNC==1)
		{
			if(!is_object($this) || !$this->db_Conn)
			{
				global $DB;
				$DB->DoConnect();
				return mysql_real_escape_string($strValue, $DB->db_Conn);
			}
			else
			{
				$this->DoConnect();
				return mysql_real_escape_string($strValue, $this->db_Conn);
			}
		}
		elseif(BX_USE_ESCAPE_FUNC==2)
			return mysql_escape_string($strValue);

		/////
		return str_replace("'", "\'", str_replace("\\", "\\\\", $strValue));
	}

	function ForSqlLike($strValue, $iMaxLength=0)
	{
		if(!defined("BX_USE_ESCAPE_FUNC"))
		{
			if(function_exists("mysql_real_escape_string"))
				define("BX_USE_ESCAPE_FUNC", 1);
		elseif(BX_USE_ESCAPE_FUNC==2)
				define("BX_USE_ESCAPE_FUNC", 2);
		}

		if($iMaxLength>0)
			$strValue = substr($strValue, 0, $iMaxLength);

		if(BX_USE_ESCAPE_FUNC==1)
		{
			if(!is_object($this) || !$this->db_Conn)
			{
				global $DB;
				$DB->DoConnect();
				return mysql_real_escape_string($strValue, $DB->db_Conn);
			}
			else
			{
				$this->DoConnect();
				return mysql_real_escape_string($strValue, $this->db_Conn);
			}
		}
		else
			return mysql_escape_string($strValue);

		///
		return str_replace("'", "\'", str_replace("\\", "\\\\\\\\", $strValue));
	}

	function InitTableVarsForEdit($tablename, $strIdentFrom="str_", $strIdentTo="str_", $strSuffixFrom="", $bAlways=false)
	{
		$this->DoConnect();
		$db_result = mysql_list_fields($this->DBName, $tablename, $this->db_Conn);
		if($db_result > 0)
		{
			$intNumFields = mysql_num_fields($db_result);
			while(--$intNumFields >= 0)
			{
				$strColumnName = mysql_field_name($db_result, $intNumFields);

				$varnameFrom=$strIdentFrom.$strColumnName.$strSuffixFrom;
				$varnameTo=$strIdentTo.$strColumnName;
				global $$varnameFrom, $$varnameTo;
				if((isset($$varnameFrom) || $bAlways))
				{
					if(is_array($$varnameFrom))
					{
						foreach($$varnameFrom as $k=>$v)
							$$varnameFrom[$k] = htmlspecialchars($v);
					}
					else
						$$varnameTo = htmlspecialchars($$varnameFrom);
				}
			}
		}
	}

	function &GetTableFieldsList($tablename)
	{
		$this->DoConnect();
		$arRes = array();

		$db_result = mysql_list_fields($this->DBName, $tablename, $this->db_Conn);
		if ($db_result > 0)
		{
			$intNumFields = mysql_num_fields($db_result);
			while (--$intNumFields >= 0)
			{
				$arRes[] = mysql_field_name($db_result, $intNumFields);
			}
		}

		return $arRes;
	}

	function LockTables($str)
	{
		register_shutdown_function(array(&$this, "UnLockTables"));
		$this->Query("LOCK TABLE ".$str);
	}

	function UnLockTables()
	{
		$this->Query("UNLOCK TABLES", true);
	}

	function Concat()
	{
		$str = "";
		$ar = func_get_args();
		if (is_array($ar)) $str .= implode(" , ", $ar);
		if (strlen($str)>0) $str = "concat(".$str.")";
		return $str;
	}

	function IsNull($expression, $result)
	{
		return "ifnull(".$expression.", ".$result.")";
	}

	function Length($field)
	{
		return "length($field)";
	}

	function TableExists($tableName)
	{
		$tableName = preg_replace("/[^A-Za-z0-9%_]+/i", "", $tableName);
		$tableName = Trim($tableName);

		if (strlen($tableName) <= 0)
			return False;

		$dbResult = $this->Query("SHOW TABLES LIKE '".$this->ForSql($tableName)."'");
		if ($arResult = $dbResult->Fetch())
			return True;
		else
			return False;
	}

	function IndexExists($tableName, $arColumns)
	{
		if(!is_array($arColumns) || count($arColumns) <= 0)
			return false;

		$rs = $this->Query("SHOW INDEX FROM `".$this->ForSql($tableName)."`", true);
		if(!$rs)
			return false;

		$arIndexes = array();
		while($ar = $rs->Fetch())
			$arIndexes[$ar["Key_name"]][$ar["Seq_in_index"]-1] = $ar["Column_name"];

		$strColumns = implode(",", $arColumns);
		foreach($arIndexes as $Key_name => $arKeyColumns)
		{
			ksort($arKeyColumns);
			$strKeyColumns = implode(",", $arKeyColumns);
			if(substr($strKeyColumns, 0, strlen($strColumns)) === $strColumns)
				return true;
		}

		return false;
		//echo "<pre>",htmlspecialchars(print_r($arIndexes, true)),"</pre><hR>";
	}
}

class CDBResult extends CAllDBResult
{
	//����� ������� ������ ������� �������� ����� � ������
	function Fetch()
	{
		if($this->bNavStart || $this->bFromArray)
		{
			if(!is_array($this->arResult))
				return false;
			if($tmp=current($this->arResult))
				next($this->arResult);
			return $tmp;
		}
		elseif(!$this->arUserMultyFields)
		{
			return mysql_fetch_array($this->result, MYSQL_ASSOC);
		}
		else
		{
			$res = mysql_fetch_array($this->result, MYSQL_ASSOC);
			if($res)
				foreach($this->arUserMultyFields as $FIELD_NAME=>$flag)
					if($res[$FIELD_NAME])
						$res[$FIELD_NAME] = unserialize($res[$FIELD_NAME]);
			return $res;
		}
	}

	function SelectedRowsCount()
	{
		if($this->nSelectedCount !== false)
			return $this->nSelectedCount;

		return mysql_num_rows($this->result);
	}

	function AffectedRowsCount()
	{
		global $DB;
		$DB->DoConnect();
		return mysql_affected_rows($DB->db_Conn);

	}

	function AffectedRowsCountEx()
	{
		if (intval(@mysql_num_rows($this->result))>0) return 0; else return mysql_affected_rows();
	}

	function FieldsCount()
	{
		return mysql_num_fields($this->result);
	}

	function FieldName($iCol)
	{
		return mysql_field_name($this->result, $iCol);
	}

	function DBNavStart()
	{
		//����� ���������� �������
		$this->NavRecordCount = mysql_num_rows($this->result);
		if($this->NavRecordCount < 1)
			return;

		if($this->NavShowAll)
			$this->NavPageSize = $this->NavRecordCount;

		//���������� ����� ������� ��� ��������� ������� �������. ���� ������� � 1
		$this->NavPageCount = floor($this->NavRecordCount/$this->NavPageSize);
		if($this->NavRecordCount % $this->NavPageSize > 0)
			$this->NavPageCount++;

		//����� �������� ��� �����������. ������ ���������� � 1
		$this->NavPageNomer = ($this->PAGEN < 1 || $this->PAGEN > $this->NavPageCount? ($_SESSION[$this->SESS_PAGEN] < 1 || $_SESSION[$this->SESS_PAGEN] > $this->NavPageCount? 1:$_SESSION[$this->SESS_PAGEN]):$this->PAGEN);

		//�������� �� ������ RecordSet
		$NavFirstRecordShow = $this->NavPageSize * ($this->NavPageNomer-1);
		$NavLastRecordShow = $this->NavPageSize * $this->NavPageNomer;

		mysql_data_seek($this->result, $NavFirstRecordShow);
		$temp_arrray = array();
		for($i=$NavFirstRecordShow; $i<$NavLastRecordShow; $i++)
		{
			if(($res = mysql_fetch_array($this->result, MYSQL_ASSOC)))
			{
				if($this->arUserMultyFields)
					foreach($this->arUserMultyFields as $FIELD_NAME=>$flag)
						if($res[$FIELD_NAME])
							$res[$FIELD_NAME] = unserialize($res[$FIELD_NAME]);
				$temp_arrray[] = $res;
			}
			else
			{
				break;
			}
		}

		$this->arResult=$temp_arrray;
//		print_r($temp_arrray);
	}

	function NavQuery($strSql, $cnt, $arNavStartParams)
	{
		if(is_set($arNavStartParams, "SubstitutionFunction"))
		{
			$arNavStartParams["SubstitutionFunction"]($this, $strSql, $cnt, $arNavStartParams);
			return;
		}
		if(is_set($arNavStartParams, "bShowAll"))
			$bShowAll = $arNavStartParams["bShowAll"];
		else
			$bShowAll = true;

		if(is_set($arNavStartParams, "iNumPage"))
			$iNumPage = $arNavStartParams["iNumPage"];
		else
			$iNumPage = false;

		if(is_set($arNavStartParams, "bDescPageNumbering"))
			$bDescPageNumbering = $arNavStartParams["bDescPageNumbering"];
		else
			$bDescPageNumbering = false;

		$this->InitNavStartVars($arNavStartParams);
		$this->NavRecordCount = $cnt;

		if($this->NavShowAll)
			$this->NavPageSize = $this->NavRecordCount;

		//���������� ����� ������� ��� ��������� ������� �������. ���� ������� � 1
		$this->NavPageCount = ($this->NavPageSize>0 ? floor($this->NavRecordCount/$this->NavPageSize) : 0);
		if($bDescPageNumbering)
		{
			$makeweight = ($this->NavRecordCount % $this->NavPageSize);
			if($this->NavPageCount == 0 && $makeweight > 0)
				$this->NavPageCount = 1;

			//����� �������� ��� �����������.
			//if($iNumPage===false)
			//	$this->PAGEN = $this->NavPageCount;
			$this->NavPageNomer =
				(
					$this->PAGEN < 1 || $this->PAGEN > $this->NavPageCount
					?
						($_SESSION[$this->SESS_PAGEN] < 1 || $_SESSION[$this->SESS_PAGEN] > $this->NavPageCount
						?
							$this->NavPageCount
						:
							$_SESSION[$this->SESS_PAGEN]
						)
					:
						$this->PAGEN
				);

			//�������� �� ������ RecordSet
			$NavFirstRecordShow = 0;
			if($this->NavPageNomer != $this->NavPageCount)
				$NavFirstRecordShow += $makeweight;

			$NavFirstRecordShow += ($this->NavPageCount - $this->NavPageNomer) * $this->NavPageSize;
			$NavLastRecordShow = $makeweight + ($this->NavPageCount - $this->NavPageNomer + 1) * $this->NavPageSize;
		}
		else
		{
			if($this->NavRecordCount % $this->NavPageSize > 0)
				$this->NavPageCount++;

			//����� �������� ��� �����������. ������ ���������� � 1
			$this->NavPageNomer = ($this->PAGEN < 1 || $this->PAGEN > $this->NavPageCount? ($_SESSION[$this->SESS_PAGEN] < 1 || $_SESSION[$this->SESS_PAGEN] > $this->NavPageCount? 1:$_SESSION[$this->SESS_PAGEN]):$this->PAGEN);

			//�������� �� ������ RecordSet
			$NavFirstRecordShow = $this->NavPageSize*($this->NavPageNomer-1);
			$NavLastRecordShow = $this->NavPageSize*$this->NavPageNomer;
		}

		if(!$this->NavShowAll)
			$strSql .= " LIMIT ".$NavFirstRecordShow.", ".($NavLastRecordShow - $NavFirstRecordShow);

		global $DB;
		$res_tmp = $DB->Query($strSql);
		/*
		for($i=$NavFirstRecordShow; $i<$NavLastRecordShow; $i++)
			$temp_arrray[] = mysql_fetch_array($res_tmp->result, MYSQL_ASSOC);
		*/

		while($ar = mysql_fetch_array($res_tmp->result, MYSQL_ASSOC))
		{
			if($this->arUserMultyFields)
				foreach($this->arUserMultyFields as $FIELD_NAME=>$flag)
					if($ar[$FIELD_NAME])
						$ar[$FIELD_NAME] = unserialize($ar[$FIELD_NAME]);
			$temp_arrray[] = $ar;
		}

		$this->result = $res_tmp->result; // added for FieldsCount and other compartibility
		$this->arResult = $temp_arrray;
		$this->nSelectedCount = $cnt;
		$this->bDescPageNumbering = $bDescPageNumbering;
		$this->bFromLimited=true;
	}
}
?>
