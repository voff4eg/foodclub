<?
if(!defined("CACHED_b_iblock_type")) define("CACHED_b_iblock_type", 3600);
if(!defined("CACHED_b_iblock")) define("CACHED_b_iblock", 3600);
if(!defined("CACHED_b_iblock_property_enum")) define("CACHED_b_iblock_property_enum", 3600);
if(!defined("CACHED_b_iblock_property_enum_bucket_size")) define("CACHED_b_iblock_property_enum_bucket_size", 100);

global $DBType;

//IncludeModuleLangFile(__FILE__);
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/lang.php");

class CIBlockPropertyResult extends CDBResult
{
	function Fetch()
	{
		$res = parent::Fetch();
		if($res && $res["USER_TYPE"]!="")
		{
			$arUserType = CIBlockProperty::GetUserType($res["USER_TYPE"]);
			if(array_key_exists("ConvertFromDB", $arUserType))
			{
				$value = array("VALUE"=>$res["VALUE"],"DESCRIPTION"=>"");
				$value = call_user_func_array($arUserType["ConvertFromDB"],array($res,$value));
				$res["VALUE"] = $value["VALUE"];
				if(array_key_exists("DEFAULT_VALUE", $res))
				{
					$value = array("VALUE"=>$res["DEFAULT_VALUE"],"DESCRIPTION"=>"");
					$value = call_user_func_array($arUserType["ConvertFromDB"],array($res,$value));
					$res["DEFAULT_VALUE"] = $value["VALUE"];
				}
			}
		}
		return $res;
	}
}

class CIBlockResult extends CDBResult
{
	var $arIBlockMultProps=false;
	var $arIBlockConvProps=false;
	var $arIBlockAllProps =false;
	var $arIBlockNumProps =false;
	var $arIBlockLongProps = false;

	var $nInitialSize;
	var $table_id;

	function Fetch()
	{
		global $DB,$DBType;
		$res = parent::Fetch();
		$arUpdate = array();
		if($res && is_object($this))
		{
			if(is_array($this->arIBlockLongProps))
			{
				foreach($res as $k=>$v)
				{
					if(preg_match("#^ALIAS_(\d+)_(.*)$#", $k, $match))
					{
						$res[$this->arIBlockLongProps[$match[1]].$match[2]] = $v;
						unset($res[$k]);
					}
				}
			}
			if($res["IBLOCK_ID"]!="" && $res["ID"]!="" && is_array($this->arIBlockMultProps) && count($this->arIBlockMultProps)>0)
			{
				foreach($this->arIBlockMultProps[$res["IBLOCK_ID"]] as $prop_id=>$db_prop)
				{
					if(strncmp($prop_id, "*", 1)==0)
					{
						$strProp = "PROPERTY_".substr($prop_id, 1);
						$strDesc = "DESCRIPTION_".substr($prop_id, 1);
					}
					else
					{
						$strProp = "PROPERTY_".strtoupper($prop_id)."_VALUE";
						$strDesc = "PROPERTY_".strtoupper($prop_id)."_DESCRIPTION";
					}
					if(array_key_exists($strProp, $res))
					{
						if(is_object($res[$strProp]))
							$res[$strProp]=$res[$strProp]->load();
						if(strlen($res[$strProp])=="")
						{
							$strSql = "
								SELECT VALUE,DESCRIPTION
								FROM b_iblock_element_prop_m".$res["IBLOCK_ID"]."
								WHERE
									IBLOCK_ELEMENT_ID = ".intval($res["ID"])."
									AND IBLOCK_PROPERTY_ID = ".intval($db_prop["ORIG_ID"])."
								ORDER BY ID
							";
							$rs = $DB->Query($strSql);
							$res[$strProp] = array();
							$res[$strDesc] = array();
							while($ar=$rs->Fetch())
							{
								$res[$strProp][]=$ar["VALUE"];
								$res[$strDesc][]=$ar["DESCRIPTION"];
							}
							$arUpdate["b_iblock_element_prop_s".$res["IBLOCK_ID"]]["PROPERTY_".$db_prop["ORIG_ID"]] = serialize(array("VALUE"=>$res[$strProp],"DESCRIPTION"=>$res[$strDesc]));
						}
						else
						{
							$tmp = unserialize($res[$strProp]);
							$res[$strProp] = $tmp["VALUE"];
							$res[$strDesc] = $tmp["DESCRIPTION"];
						}
						if(is_array($res[$strProp]) && $db_prop["PROPERTY_TYPE"]=="L")
						{
							foreach($res[$strProp] as $key=>$val)
							{
								unset($res[$strProp][$key]);
								$arEnum = CIBlockPropertyEnum::GetByID($val);
								if($arEnum!==false)
									$res[$strProp][$val] = $arEnum["VALUE"];
							}
						}
					}
				}
				foreach($arUpdate as $strTable=>$arFields)
				{
					$strUpdate = $DB->PrepareUpdate($strTable, $arFields);
					if($strUpdate!="")
					{
						$strSql = "UPDATE ".$strTable." SET ".$strUpdate." WHERE IBLOCK_ELEMENT_ID = ".intval($res["ID"]);
						$DB->QueryBind($strSql, $arFields);
					}
				}
			}
			if(is_array($this->arIBlockConvProps))
			{
				foreach($this->arIBlockConvProps as $strFieldName=>$arCallback)
				{
					if(is_array($res[$strFieldName]))
					{

						foreach($res[$strFieldName] as $key=>$value)
						{
							$arValue = call_user_func_array($arCallback["ConvertFromDB"], array($arCallback["PROPERTY"], array("VALUE"=>$value,"DESCRIPTION"=>"")));
							$res[$strFieldName][$key] = $arValue["VALUE"];
						}
					}
					else
					{
						$arValue = call_user_func_array($arCallback["ConvertFromDB"], array($arCallback["PROPERTY"], array("VALUE"=>$res[$strFieldName],"DESCRIPTION"=>"")));
						$res[$strFieldName] = $arValue["VALUE"];
					}
				}
			}
			if(is_array($this->arIBlockNumProps))
			{
				foreach($this->arIBlockNumProps as $prop_id=>$db_prop)
				{
					$strProp = "PROPERTY_".strtoupper($prop_id)."_VALUE";
					if(strlen($res[$strProp])>0)
						$res[$strProp] = doubleval($res[$strProp]);
				}
			}
		}
		return $res;
	}
	function CIBlockResult($res)
	{
		parent::CDBResult($res);
	}

	function GetNext($bTextHtmlAuto=true, $use_tilda=true)
	{
		$res = parent::GetNext($bTextHtmlAuto, $use_tilda);
		if($res)
		{
			if(strlen($res["IBLOCK_ID"])>0)
			{
				$res["LIST_PAGE_URL"] =
					str_replace("//", "/",
						str_replace("#LANG#", $res["LANG_DIR"],
							str_replace("#SITE_DIR#", SITE_DIR,
								str_replace("#SERVER_NAME#", SITE_SERVER_NAME,
									str_replace("#IBLOCK_ID#", $res["IBLOCK_ID"], $res["LIST_PAGE_URL"])
								)
							)
						)
					);

				if(array_key_exists("DETAIL_PAGE_URL", $res))
					$res["DETAIL_PAGE_URL"] = CIBlock::ReplaceDetailUrl($res["DETAIL_PAGE_URL"], $res, true);
				if(array_key_exists("SECTION_PAGE_URL", $res))
					$res["SECTION_PAGE_URL"] = CIBlock::ReplaceDetailUrl($res["SECTION_PAGE_URL"], $res, true);
			}
			else
				$res["LIST_PAGE_URL"] =
					str_replace("//", "/",
						str_replace("#LANG#", $res["LANG_DIR"],
							str_replace("#SITE_DIR#", SITE_DIR,
								str_replace("#SERVER_NAME#", SITE_SERVER_NAME,
									str_replace("#IBLOCK_ID#", $res["ID"], $res["LIST_PAGE_URL"])
								)
							)
						)
					);
		}
		return $res;
	}

	function GetNextElement()
	{
		if(!($r = $this->GetNext()))
			return $r;

		$res = new _CIBElement;
		$res->fields = $r;
		if(count($this->arIBlockAllProps)>0)
			$res->props  = $this->arIBlockAllProps;
		return $res;
	}

	function SetTableID($table_id)
	{
		$this->table_id = $table_id;
	}

	function NavStart($nPageSize=20, $bShowAll=true, $iNumPage=false)
	{
		if($this->table_id)
		{
			if ($_REQUEST["mode"] == "excel")
				return;

			$nSize = CAdminResult::GetNavSize($this->table_id, $nPageSize);
			if(is_array($nPageSize))
			{
				$this->nInitialSize = $nPageSize["nPageSize"];
				$nPageSize["nPageSize"] = $nSize;
			}
			else
			{
				$this->nInitialSize = $nPageSize;
				$nPageSize = $nSize;
			}
		}
		parent::NavStart($nPageSize, $bShowAll, $iNumPage);
	}

	function GetNavPrint($title, $show_allways=true, $StyleText="", $template_path=false)
	{
		if($this->table_id && ($template_path === false))
			$template_path = $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/interface/navigation.php";
		return parent::GetNavPrint($title, $show_allways, $StyleText, $template_path);
	}
}

class _CIBElement
{
	var $fields;
	var $props=false;

	function GetFields()
	{
		return $this->fields;
	}

	function GetProperties($arOrder = false, $arFilter=Array())
	{
		if($arOrder===false)
			$arOrder = Array("sort"=>"asc","id"=>"asc","enum_sort"=>"asc","value_id"=>"asc");
		if(count($arFilter)==0 && is_array($this->props))
		{
			$arAllProps = Array();
			foreach($this->props as $arProp)
			{
				if(strlen(trim($arProp["CODE"]))>0)
					$PIND = $arProp["CODE"];
				else
					$PIND = $arProp["ID"];

				$arProp["VALUE"] = $this->fields["PROPERTY_".$arProp["ID"]];
				$arProp["DESCRIPTION"] = $this->fields["DESCRIPTION_".$arProp["ID"]];
				if($arProp["MULTIPLE"]=="N")
				{
					if($arProp["PROPERTY_TYPE"]=="L")
					{
						$arProp["VALUE_ENUM_ID"] = $val = $arProp["VALUE"];
						$arEnum = CIBlockPropertyEnum::GetByID($val);
						if($arEnum!==false)
						{
							$arProp["~VALUE"] = $arEnum["VALUE"];
							if(is_array($arProp["VALUE"]) || preg_match("/[;&<>\"]/", $arProp["VALUE"]))
								$arProp["VALUE"]  = htmlspecialcharsex($arEnum["VALUE"]);
							else
								$arProp["VALUE"]  = $arEnum["VALUE"];
						}
						else
						{
							$arProp["~VALUE"] = "";
							$arProp["VALUE"]  = "";
						}
					}
					elseif(strlen($arProp["VALUE"])>0)
					{
						if($arProp["PROPERTY_TYPE"]=="N")
							$arProp["VALUE"] = doubleval($arProp["VALUE"]);
						$arProp["~VALUE"] = $this->fields["~PROPERTY_".$arProp["ID"]];
						$arProp["~DESCRIPTION"] = $this->fields["~DESCRIPTION_".$arProp["ID"]];
					}
					else
					{
						$arProp["VALUE"] = $arProp["~VALUE"] = "";
						$arProp["DESCRIPTION"] = $arProp["~DESCRIPTION"] = "";
					}
				}
				else
				{
					$arList = $arProp["VALUE"];
					$arListTilda = $this->fields["~PROPERTY_".$arProp["ID"]];
					if($arProp["PROPERTY_TYPE"]=="L")
					{
						$arProp["~VALUE"] = $arProp["VALUE"] = $arProp["VALUE_ENUM_ID"] = false;
						foreach($arList as $key=>$val)
						{
							if(strlen($val)>0)
							{
								if(is_array($arProp["VALUE"]))
								{
									$arProp["VALUE_ENUM_ID"][] = $key;
									$arProp["~VALUE"][] = $val;
									if(is_array($val) || preg_match("/[;&<>\"]/", $val))
										$arProp["VALUE"][] = htmlspecialcharsex($val);
									else
										$arProp["VALUE"][] = $val;
								}
								else
								{
									$arProp["VALUE_ENUM_ID"] = array($key);
									$arProp["~VALUE"] = array($val);
									if(is_array($val) || preg_match("/[;&<>\"]/", $val))
										$arProp["VALUE"] = array(htmlspecialcharsex($val));
									else
										$arProp["VALUE"] = array($val);
								}
							}
						}
					}
					else
					{
						$arDesc = $arProp["DESCRIPTION"];
						$arDescTilda = $this->fields["~DESCRIPTION_".$arProp["ID"]];

						$arProp["~VALUE"] = $arProp["VALUE"] = false;
						$arProp["~DESCRIPTION"] = $arProp["DESCRIPTION"] = false;
						foreach($arList as $key=>$val)
						{
							if(strlen($val)>0)
							{
								if(is_array($arProp["VALUE"]))
								{
									$arProp["~VALUE"][] = $arListTilda[$key];
									if($arProp["PROPERTY_TYPE"]=="N")
										$val = doubleval($val);
									$arProp["VALUE"][] = $val;
									$arProp["~DESCRIPTION"][] = $arDescTilda[$key];
									$arProp["DESCRIPTION"][] = $arDesc[$key];
								}
								else
								{
									$arProp["~VALUE"] = array($arListTilda[$key]);
									if($arProp["PROPERTY_TYPE"]=="N")
										$val = doubleval($val);
									$arProp["VALUE"] = array($val);
									$arProp["~DESCRIPTION"] = array($arDescTilda[$key]);
									$arProp["DESCRIPTION"] = array($arDesc[$key]);
								}
							}
						}
					}
				}
				$arAllProps[$PIND]=$arProp;
			}
			return $arAllProps;
		}

		if(array_key_exists("ID", $arFilter) && !is_numeric(substr($arFilter["ID"], 0, 1)))
		{
			$arFilter["CODE"] = $arFilter["ID"];
			unset($arFilter["ID"]);
		}

		if(!array_key_exists("ACTIVE", $arFilter))
			$arFilter["ACTIVE"]="Y";

		$props = CIBlockElement::GetProperty($this->fields["IBLOCK_ID"], $this->fields["ID"], $arOrder, $arFilter);

		$arAllProps = Array();
		while($arProp = $props->Fetch())
		{
			if(strlen(trim($arProp["CODE"]))>0)
				$PIND = $arProp["CODE"];
			else
				$PIND = $arProp["ID"];

			if($arProp["PROPERTY_TYPE"]=="L")
			{
				$arProp["VALUE_ENUM_ID"] = $arProp["VALUE"];
				$arProp["VALUE"] = $arProp["VALUE_ENUM"];
			}

			if(is_array($arProp["VALUE"]) || (strlen($arProp["VALUE"]) > 0))
			{
				$arProp["~VALUE"] = $arProp["VALUE"];
				if(is_array($arProp["VALUE"]) || preg_match("/[;&<>\"]/", $arProp["VALUE"]))
					$arProp["VALUE"] = htmlspecialcharsex($arProp["VALUE"]);
				$arProp["~DESCRIPTION"] = $arProp["DESCRIPTION"];
				if(preg_match("/[;&<>\"]/", $arProp["DESCRIPTION"]))
					$arProp["DESCRIPTION"] = htmlspecialcharsex($arProp["DESCRIPTION"]);
			}
			else
			{
				$arProp["VALUE"] = $arProp["~VALUE"] = "";
				$arProp["DESCRIPTION"] = $arProp["~DESCRIPTION"] = "";
			}

			if($arProp["MULTIPLE"]=="Y")
			{
				if(array_key_exists($PIND, $arAllProps))
				{
					$arTemp = &$arAllProps[$PIND];
					if($arProp["VALUE"]!=="")
					{
						if(is_array($arTemp["VALUE"]))
						{
							$arTemp["VALUE"][] = $arProp["VALUE"];
							$arTemp["~VALUE"][] = $arProp["~VALUE"];
							$arTemp["DESCRIPTION"][] = $arProp["DESCRIPTION"];
							$arTemp["~DESCRIPTION"][] = $arProp["~DESCRIPTION"];
							$arTemp["PROPERTY_VALUE_ID"][] = $arProp["PROPERTY_VALUE_ID"];
							if($arProp["PROPERTY_TYPE"]=="L")
								$arTemp["VALUE_ENUM_ID"][] = $arProp["VALUE_ENUM_ID"];
						}
						else
						{
							$arTemp["VALUE"] = array($arProp["VALUE"]);
							$arTemp["~VALUE"] = array($arProp["~VALUE"]);
							$arTemp["DESCRIPTION"] = array($arProp["DESCRIPTION"]);
							$arTemp["~DESCRIPTION"] = array($arProp["~DESCRIPTION"]);
							$arTemp["PROPERTY_VALUE_ID"] = array($arProp["PROPERTY_VALUE_ID"]);
							if($arProp["PROPERTY_TYPE"]=="L")
								$arTemp["VALUE_ENUM_ID"] = array($arProp["VALUE_ENUM_ID"]);
						}
					}
				}
				else
				{
					$arProp["~NAME"] = $arProp["NAME"];
					if(preg_match("/[;&<>\"]/", $arProp["NAME"]))
						$arProp["NAME"] = htmlspecialcharsex($arProp["NAME"]);
					$arProp["~DEFAULT_VALUE"] = $arProp["DEFAULT_VALUE"];
					if(is_array($arProp["DEFAULT_VALUE"]) || preg_match("/[;&<>\"]/", $arProp["DEFAULT_VALUE"]))
						$arProp["DEFAULT_VALUE"] = htmlspecialcharsex($arProp["DEFAULT_VALUE"]);
					if($arProp["VALUE"]!=="")
					{
						$arProp["VALUE"] = array($arProp["VALUE"]);
						$arProp["~VALUE"] = array($arProp["~VALUE"]);
						$arProp["DESCRIPTION"] = array($arProp["DESCRIPTION"]);
						$arProp["~DESCRIPTION"] = array($arProp["~DESCRIPTION"]);
						$arProp["PROPERTY_VALUE_ID"] = array($arProp["PROPERTY_VALUE_ID"]);
						if($arProp["PROPERTY_TYPE"]=="L")
							$arProp["VALUE_ENUM_ID"] = array($arProp["VALUE_ENUM_ID"]);
					}
					else
					{
						$arProp["VALUE"] = false;
						$arProp["~VALUE"] = false;
						$arProp["DESCRIPTION"] = false;
						$arProp["~DESCRIPTION"] = false;
						$arProp["PROPERTY_VALUE_ID"] = false;
						if($arProp["PROPERTY_TYPE"]=="L")
							$arProp["VALUE_ENUM_ID"] = false;
					}
					$arAllProps[$PIND] = $arProp;
				}
			}
			else
			{
				$arProp["~NAME"] = $arProp["NAME"];
				if(preg_match("/[;&<>\"]/", $arProp["NAME"]))
					$arProp["NAME"] = htmlspecialcharsex($arProp["NAME"]);
				$arProp["~DEFAULT_VALUE"] = $arProp["DEFAULT_VALUE"];
				if(is_array($arProp["DEFAULT_VALUE"]) || preg_match("/[;&<>\"]/", $arProp["DEFAULT_VALUE"]))
					$arProp["DEFAULT_VALUE"] = htmlspecialcharsex($arProp["DEFAULT_VALUE"]);
				$arAllProps[$PIND] = $arProp;
			}
		}

		return $arAllProps;
	}

	function GetProperty($ID)
	{
		$res = $this->GetProperties(Array(), Array("ID"=>$ID));
		list(, $res) = each($res);
		return $res;
	}

	function GetGroups()
	{
		$res = CIBlockElement::GetElementGroups($this->fields["ID"]);
		return $res;
	}
}


/********************************************************************
*  Information blocks classes
********************************************************************/
include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/classes/".$DBType."/iblocktype.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/classes/".$DBType."/iblock.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/classes/".$DBType."/iblocksection.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/classes/".$DBType."/iblockproperty.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/classes/".$DBType."/iblockelement.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/classes/".$DBType."/iblockrss.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/classes/general/prop_datetime.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/classes/general/prop_xmlid.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/classes/general/prop_fileman.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/classes/general/prop_html.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/classes/general/prop_anchor.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/classes/general/prop_element_list.php");


/*********************************************
Public helper functions
*********************************************/
function GetIBlockListWithCnt($type, $arTypesInc = Array(), $arTypesExc = Array(), $arOrder=Array("SORT"=>"ASC"), $cnt=0)
{
	if(!is_array($arTypesInc))
		$arTypesInc = Array($arTypesInc);

	$arIDsInc = Array();
	$arCODEsInc = Array();
	for($i=0; $i<count($arTypesInc); $i++)
		if(IntVal($arTypesInc[$i])>0)
			$arIDsInc[] = $arTypesInc[$i];
		else
			$arCODEsInc[] = $arTypesInc[$i];

	if(!is_array($arTypesExc))
		$arTypesExc = Array($arTypesExc);

	$arIDsExc = Array();
	$arCODEsExc = Array();
	for($i=0; $i<count($arTypesExc); $i++)
		if(IntVal($arTypesExc[$i])>0)
			$arIDsExc[] = $arTypesExc[$i];
		else
			$arCODEsExc[] = $arTypesExc[$i];

	$res = CIBlock::GetList($arOrder, Array("type"=>$type, "LID"=>LANG, "ACTIVE"=>"Y", "ID"=>$arIDsInc, "CNT_ACTIVE"=>"Y", "CODE"=>$arCODEsInc, "!ID"=>$arIDsExc, "!CODE"=>$arCODEsExc), true);
	$dbr = new  CIBlockResult($res);
	if($cnt>0)
		$dbr->NavStart($cnt);
	return $dbr;
}

function GetIBlockList($type, $arTypesInc = Array(), $arTypesExc = Array(), $arOrder=Array("SORT"=>"ASC"), $cnt=0)
{
	return GetIBlockListLang(LANG, $type, $arTypesInc, $arTypesExc, $arOrder, $cnt);
}

function GetIBlockListLang($lang, $type, $arTypesInc = Array(), $arTypesExc = Array(), $arOrder=Array("SORT"=>"ASC"), $cnt=0)
{
	if(!is_array($arTypesInc))
		$arTypesInc = Array($arTypesInc);

	$arIDsInc = Array();
	$arCODEsInc = Array();
	for($i=0; $i<count($arTypesInc); $i++)
		if(IntVal($arTypesInc[$i])>0)
			$arIDsInc[] = $arTypesInc[$i];
		else
			$arCODEsInc[] = $arTypesInc[$i];

	if(!is_array($arTypesExc))
		$arTypesExc = Array($arTypesExc);

	$arIDsExc = Array();
	$arCODEsExc = Array();
	for($i=0; $i<count($arTypesExc); $i++)
		if(IntVal($arTypesExc[$i])>0)
			$arIDsExc[] = $arTypesExc[$i];
		else
			$arCODEsExc[] = $arTypesExc[$i];

	$res = CIBlock::GetList($arOrder, Array("type"=>$type, "LID"=>$lang, "ACTIVE"=>"Y", "ID"=>$arIDsInc, "CODE"=>$arCODEsInc, "!ID"=>$arIDsExc, "!CODE"=>$arCODEsExc));
	$dbr = new  CIBlockResult($res);
	if($cnt>0)
		$dbr->NavStart($cnt);
	return $dbr;
}

function GetIBlock($ID, $type="")
{
	return GetIBlockLang(LANG, $ID, $type);
}

function GetIBlockLang($lang, $ID, $type="")
{
	$res = CIBlock::GetList(Array("sort"=>"asc"), Array("ID"=>IntVal($ID), "TYPE"=>$type, "LID"=>$lang, "ACTIVE"=>"Y"));
	$res = new CIBlockResult($res);
	return $arRes = $res->GetNext();
}

/**************************
Elements helper functions
**************************/
function GetIBlockElementListEx($type, $arTypesInc=Array(), $arTypesExc=Array(), $arOrder=Array("sort"=>"asc"), $cnt=0, $arFilter = Array(), $arSelect=Array(), $arGroupBy=false)
{
	return GetIBlockElementListExLang(LANG, $type, $arTypesInc, $arTypesExc, $arOrder, $cnt, $arFilter, $arSelect, $arGroupBy);
}

function GetIBlockElementCountEx($type, $arTypesInc=Array(), $arTypesExc=Array(), $arOrder=Array("sort"=>"asc"), $cnt=0, $arFilter = Array())
{
	return GetIBlockElementCountExLang(LANG, $type, $arTypesInc, $arTypesExc, $arOrder, $cnt, $arFilter);
}

function GetIBlockElementListExLang($lang, $type, $arTypesInc=Array(), $arTypesExc=Array(), $arOrder=Array("sort"=>"asc"), $cnt=0, $arFilter = Array(), $arSelect=Array(), $arGroupBy=false)
{
	$filter = _GetIBlockElementListExLang_tmp($lang, $type, $arTypesInc, $arTypesExc, $arOrder, $cnt, $arFilter);
	if(is_array($cnt))
		$arNavParams = $cnt; //Array("nPageSize"=>$cnt, "bShowAll"=>false);
	elseif($cnt>0)
		$arNavParams = Array("nPageSize"=>$cnt);
	else
		$arNavParams = false;

	$dbr = CIBlockElement::GetList($arOrder, $filter, $arGroupBy, $arNavParams, $arSelect);
	if(!is_array($cnt) && $cnt>0)
		$dbr->NavStart($cnt);

	return $dbr;
}

function GetIBlockElementCountExLang($lang, $type, $arTypesInc=Array(), $arTypesExc=Array(), $arOrder=Array("sort"=>"asc"), $cnt=0, $arFilter = Array())
{
	$filter = _GetIBlockElementListExLang_tmp($lang, $type, $arTypesInc, $arTypesExc, $arOrder, $cnt, $arFilter);
	return CIBlockElement::GetList($arOrder, $filter, true);
}


function _GetIBlockElementListExLang_tmp($lang, $type, $arTypesInc=Array(), $arTypesExc=Array(), $arOrder=Array("sort"=>"asc"), $cnt=0, $arFilter = Array(), $arSelect=Array())
{
	global $DB;
	if(!is_array($arTypesInc))
	{
		if($arTypesInc!==false)
			$arTypesInc = Array($arTypesInc);
		else
			$arTypesInc = Array();
	}

	$arIDsInc = Array();
	$arCODEsInc = Array();
	for($i=0; $i<count($arTypesInc); $i++)
		if(IntVal($arTypesInc[$i])>0)
			$arIDsInc[] = $arTypesInc[$i];
		else
			$arCODEsInc[] = $arTypesInc[$i];

	if(!is_array($arTypesExc))
	{
		if($arTypesExc!==false)
			$arTypesExc = Array($arTypesExc);
		else
			$arTypesExc = Array();
	}

	$arIDsExc = Array();
	$arCODEsExc = Array();
	for($i=0; $i<count($arTypesExc); $i++)
		if(IntVal($arTypesExc[$i])>0)
			$arIDsExc[] = $arTypesExc[$i];
		else
			$arCODEsExc[] = $arTypesExc[$i];

	$filter = Array(
			"IBLOCK_ID"=>$arIDsInc, "IBLOCK_LID"=>$lang, "IBLOCK_ACTIVE"=>"Y",
			"IBLOCK_CODE"=>$arCODEsInc, "!IBLOCK_ID"=>$arIDsExc,
			"!IBLOCK_CODE"=>$arCODEsExc, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "CHECK_PERMISSIONS"=>"Y"
			);

	if($type!=false && strlen($type)>0)
		$filter["IBLOCK_TYPE"]=$type;

	if(is_array($arFilter) && count($arFilter)>0)
		$filter = array_merge($filter, $arFilter);

	return $filter;
}

function GetIBlockElementCount($IBLOCK, $SECT_ID=false, $arOrder=Array("sort"=>"asc"), $cnt=0)
{
	$filter = Array("IBLOCK_ID"=>IntVal($IBLOCK), "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "CHECK_PERMISSIONS"=>"Y");
	if($SECT_ID!==false)
		$filter["SECTION_ID"]=IntVal($SECT_ID);

	return CIBlockElement::GetList($arOrder, $filter, true);
}

function GetIBlockElementList($IBLOCK, $SECT_ID=false, $arOrder=Array("sort"=>"asc"), $cnt=0, $arFilter=array(), $arSelect=array())
{
	$filter = Array("IBLOCK_ID"=>IntVal($IBLOCK), "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "CHECK_PERMISSIONS"=>"Y");
	if($SECT_ID!==false)
		$filter["SECTION_ID"]=IntVal($SECT_ID);

	if (is_array($arFilter) && count($arFilter)>0)
		$filter = array_merge($filter, $arFilter);

	$dbr = CIBlockElement::GetList($arOrder, $filter, false, false, $arSelect);
	if($cnt>0)
		$dbr->NavStart($cnt);

	return $dbr;
}

function GetIBlockElement($ID, $TYPE="")
{
	$filter = Array("ID"=>IntVal($ID), "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "CHECK_PERMISSIONS"=>"Y");
	if($TYPE!="")
		$filter["IBLOCK_TYPE"]=$TYPE;

	$iblockelement = CIBlockElement::GetList(Array(), $filter);
	if($obIBlockElement = $iblockelement->GetNextElement())
	{
		$arIBlockElement = $obIBlockElement->GetFields();
		if($arIBlock = GetIBlock($arIBlockElement["IBLOCK_ID"], $TYPE))
		{
			$arIBlockElement["IBLOCK_ID"] = $arIBlock["ID"];
			$arIBlockElement["IBLOCK_NAME"] = $arIBlock["NAME"];
			$arIBlockElement["~IBLOCK_NAME"] = $arIBlock["~NAME"];
			$arIBlockElement["PROPERTIES"] = $obIBlockElement->GetProperties();
			return $arIBlockElement;
		}
	}

	return false;
}

/******************************
Sections functions
******************************/
function GetIBlockSectionListWithCnt($IBLOCK, $SECT_ID=false, $arOrder = Array("left_margin"=>"asc"), $cnt=0, $arFilter=Array())
{
	$filter = Array("IBLOCK_ID"=>IntVal($IBLOCK), "ACTIVE"=>"Y", "CNT_ACTIVE"=>"Y");
	if($SECT_ID!==false)
		$filter["SECTION_ID"]=IntVal($SECT_ID);

	if(is_array($arFilter) && count($arFilter)>0)
		$filter = array_merge($filter, $arFilter);

	$dbr = CIBlockSection::GetList($arOrder, $filter, true);
	if($cnt>0)
		$dbr->NavStart($cnt);

	return $dbr;
}

function GetIBlockSectionList($IBLOCK, $SECT_ID=false, $arOrder = Array("left_margin"=>"asc"), $cnt=0, $arFilter=Array())
{
	$filter = Array("IBLOCK_ID"=>IntVal($IBLOCK), "ACTIVE"=>"Y", "IBLOCK_ACTIVE"=>"Y");
	if($SECT_ID!==false)
		$filter["SECTION_ID"]=IntVal($SECT_ID);

	if(is_array($arFilter) && count($arFilter)>0)
		$filter = array_merge($filter, $arFilter);

	$dbr = CIBlockSection::GetList($arOrder, $filter);
	if($cnt>0)
		$dbr->NavStart($cnt);

	return $dbr;
}

function GetIBlockSection($ID, $TYPE="")
{
	$ID = intval($ID);
	if($ID>0)
	{
		$iblocksection = CIBlockSection::GetList(Array(), Array("ID"=>$ID, "ACTIVE"=>"Y"));
		if($arIBlockSection = $iblocksection->GetNext())
		{
			if($arIBlock = GetIBlock($arIBlockSection["IBLOCK_ID"], $TYPE))
			{
				$arIBlockSection["IBLOCK_ID"] = $arIBlock["ID"];
				$arIBlockSection["IBLOCK_NAME"] = $arIBlock["NAME"];
				return $arIBlockSection;
			}
		}
	}
	return false;
}

function GetIBlockSectionPath($IBLOCK, $SECT_ID)
{
	return CIBlockSection::GetNavChain(IntVal($IBLOCK), IntVal($SECT_ID));
}

/***************************************************************
RSS
***************************************************************/
function xmlize_rss($data)
{
	$data = trim($data);
	$vals = $index = $array = array();
	$parser = xml_parser_create();
	xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
	xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
	xml_parse_into_struct($parser, $data, $vals, $index);
	xml_parser_free($parser);

	$i = 0;

	$tagname = $vals[$i]['tag'];
	if (isset($vals[$i]['attributes']))
		$array[$tagname]['@'] = $vals[$i]['attributes'];
	else
		$array[$tagname]['@'] = array();

	$array[$tagname]["#"] = xml_depth_rss($vals, $i);

	return $array;
}

function xml_depth_rss($vals, &$i)
{
	$children = array();

	if (isset($vals[$i]['value']))
		array_push($children, $vals[$i]['value']);

	while (++$i < count($vals))
	{
		switch ($vals[$i]['type'])
		{
		   case 'open':
				if (isset($vals[$i]['tag']))
					$tagname = $vals[$i]['tag'];
				else
					$tagname = '';

				if (isset($children[$tagname]))
					$size = sizeof($children[$tagname]);
				else
					$size = 0;

				if (isset($vals[$i]['attributes']))
					$children[$tagname][$size]['@'] = $vals[$i]["attributes"];

				$children[$tagname][$size]['#'] = xml_depth_rss($vals, $i);
			break;

			case 'cdata':
				array_push($children, $vals[$i]['value']);
			break;

			case 'complete':
				$tagname = $vals[$i]['tag'];

				if(isset($children[$tagname]))
					$size = sizeof($children[$tagname]);
				else
					$size = 0;

				if(isset($vals[$i]['value']))
					$children[$tagname][$size]["#"] = $vals[$i]['value'];
				else
					$children[$tagname][$size]["#"] = '';

				if (isset($vals[$i]['attributes']))
					$children[$tagname][$size]['@'] = $vals[$i]['attributes'];
			break;

			case 'close':
				return $children;
			break;
		}

	}

	return $children;
}

function GetIBlockDropDownList($IBLOCK_ID, $strTypeName, $strIBlockName)
{
	$html = '';

	$arTypes = array(''=>GetMessage("IBLOCK_CHOOSE_IBLOCK_TYPE"));
	$arIBlocks = array(''=>array(''=>GetMessage("IBLOCK_CHOOSE_IBLOCK")));
	$IBLOCK_TYPE = false;

	$rsIBlocks = CIBlock::GetList(array("IBLOCK_TYPE" => "ASC", "NAME" => "ASC"), array("MIN_PERMISSION" => "W"));
	while($arIBlock = $rsIBlocks->Fetch())
	{
		if($IBLOCK_ID == $arIBlock["ID"])
			$IBLOCK_TYPE = $arIBlock["IBLOCK_TYPE_ID"];
		if(!array_key_exists($arIBlock["IBLOCK_TYPE_ID"], $arTypes))
		{
			$arType = CIBlockType::GetByIDLang($arIBlock["IBLOCK_TYPE_ID"], LANG);
			$arTypes[$arType["ID"]] = $arType["NAME"]." [".$arType["ID"]."]";
			$arIBlocks[$arType["ID"]] = array(''=>GetMessage("IBLOCK_CHOOSE_IBLOCK"));
		}
		$arIBlocks[$arIBlock["IBLOCK_TYPE_ID"]][$arIBlock["ID"]] = $arIBlock["NAME"]." [".$arIBlock["ID"]."]";
	}

	$html .= '<select name="'.htmlspecialchars($strTypeName).'" id="'.htmlspecialchars($strTypeName).'" OnChange="OnTypeChanged(this)">'."\n";
	foreach($arTypes as $key => $value)
	{
		if($IBLOCK_TYPE === false)
			$IBLOCK_TYPE = $key;
		$html .= '<option value="'.htmlspecialchars($key).'"'.($IBLOCK_TYPE===$key? ' selected': '').'>'.htmlspecialchars($value).'</option>'."\n";
	}
	$html .= "</select>\n";

	$html .= "&nbsp;\n";

	$html .= '<select name="'.htmlspecialchars($strIBlockName).'" id="'.htmlspecialchars($strIBlockName).'">'."\n";
	foreach($arIBlocks[$IBLOCK_TYPE] as $key => $value)
	{
		$html .= '<option value="'.htmlspecialchars($key).'"'.($IBLOCK_ID===$key? ' selected': '').'>'.htmlspecialchars($value).'</option>'."\n";
	}
	$html .= "</select>\n";

	$html .= '
	<script language="JavaScript">
	function OnTypeChanged(typeSelect)
	{
		var arIBlocks = '.CUtil::PhpToJSObject($arIBlocks).';
		var iblockSelect = document.getElementById(\''.CUtil::JSEscape($strIBlockName).'\');
		if(iblockSelect)
		{
			for(var i=iblockSelect.length-1; i >= 0; i--)
				iblockSelect.remove(i);
			var n = 0;
			for(var j in arIBlocks[typeSelect.value])
			{
				var newoption = new Option(arIBlocks[typeSelect.value][j], j, false, false);
				iblockSelect.options[n]=newoption;
				n++;
			}
		}
	}
	</script>
	';

	return $html;
}
?>
