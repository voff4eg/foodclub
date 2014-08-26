<?
IncludeModuleLangFile(__FILE__);

class CUserTypeEnum
{
	function GetUserTypeDescription()
	{
		return array(
			"USER_TYPE_ID" => "enumeration",
			"CLASS_NAME" => "CUserTypeEnum",
			"DESCRIPTION" => GetMessage("USER_TYPE_ENUM_DESCRIPTION"),
			"BASE_TYPE" => "enum",
		);
	}

	function GetDBColumnType($arUserField)
	{
		global $DB;
		switch(strtolower($DB->type))
		{
			case "mysql":
				return "int(18)";
			case "oracle":
				return "number(18)";
			case "mssql":
				return "int";
		}
	}

	function PrepareSettings($arUserField)
	{
		$height = intval($arUserField["SETTINGS"]["LIST_HEIGHT"]);
		$disp = $arUserField["SETTINGS"]["DISPLAY"];
		if($disp!="CHECKBOX" && $disp!="LIST")
			$disp = "LIST";
		return array(
			"DISPLAY" => $disp,
			"LIST_HEIGHT" => ($height < 2? 5: $height),
		);
	}

	function GetSettingsHTML($arUserField = false, $arHtmlControl, $bVarsFromForm)
	{
		$result = '';
		if($bVarsFromForm)
			$value = $GLOBALS[$arHtmlControl["NAME"]]["DISPLAY"];
		elseif(is_array($arUserField))
			$value = $arUserField["SETTINGS"]["DISPLAY"];
		else
			$value = "LIST";
		$result .= '
		<tr valign="top">
			<td>'.GetMessage("USER_TYPE_ENUM_DISPLAY").':</td>
			<td>
				<label><input type="radio" name="'.$arHtmlControl["NAME"].'[DISPLAY]" value="LIST" '.("LIST"==$value? 'checked="checked"': '').'>'.GetMessage("USER_TYPE_ENUM_LIST").'</label><br>
				<label><input type="radio" name="'.$arHtmlControl["NAME"].'[DISPLAY]" value="CHECKBOX" '.("CHECKBOX"==$value? 'checked="checked"': '').'>'.GetMessage("USER_TYPE_ENUM_CHECKBOX").'</label><br>
			</td>
		</tr>
		';
		if($bVarsFromForm)
			$value = intval($GLOBALS[$arHtmlControl["NAME"]]["LIST_HEIGHT"]);
		elseif(is_array($arUserField))
			$value = intval($arUserField["SETTINGS"]["LIST_HEIGHT"]);
		else
			$value = 5;
		$result .= '
		<tr valign="top">
			<td>'.GetMessage("USER_TYPE_ENUM_LIST_HEIGHT").':</td>
			<td>
				<input type="text" name="'.$arHtmlControl["NAME"].'[LIST_HEIGHT]" size="10" value="'.$value.'">
			</td>
		</tr>
		';
		return $result;
	}

	function GetEditFormHTML($arUserField, $arHtmlControl)
	{
		//if($arUserField["ENTITY_VALUE_ID"]<1) && strlen($arUserField["SETTINGS"]["DEFAULT_VALUE"])>0)
		//	$arHtmlControl["VALUE"] = htmlspecialchars($arUserField["SETTINGS"]["DEFAULT_VALUE"]);
		$result = '';
		$rsEnum = call_user_func_array(
			array($arUserField["USER_TYPE"]["CLASS_NAME"], "getlist"),
			array(
				$arUserField,
			)
		);
		if($arUserField["SETTINGS"]["DISPLAY"]=="CHECKBOX")
		{
			$bWasSelect = false;
			$result2 = '';
			while($arEnum = $rsEnum->GetNext())
			{
				$bSelected = (
					($arHtmlControl["VALUE"]==$arEnum["ID"]) ||
					($arUserField["ENTITY_VALUE_ID"]<=0 && $arEnum["DEF"]=="Y")
				);
				$bWasSelect = $bWasSelect || $bSelected;
				$result2 .= '<label><input type="radio" value="'.$arEnum["ID"].'" name="'.$arHtmlControl["NAME"].'"'.($bSelected? ' checked': '').'>'.$arEnum["VALUE"].'</label><br>';
			}
			if($arUserField["MANDATORY"]!="Y")
				$result .= '<label><input type="radio" value="" name="'.$arHtmlControl["NAME"].'"'.(!$bWasSelect? ' checked': '').'>'.GetMessage("MAIN_NO").'</label><br>';
			$result .= $result2;
		}
		else
		{
			$bWasSelect = false;
			$result2 = '';
			while($arEnum = $rsEnum->GetNext())
			{
				$bSelected = (
					($arHtmlControl["VALUE"]==$arEnum["ID"]) ||
					($arUserField["ENTITY_VALUE_ID"]<=0 && $arEnum["DEF"]=="Y")
				);
				$bWasSelect = $bWasSelect || $bSelected;
				$result2 .= '<option value="'.$arEnum["ID"].'"'.($bSelected? ' selected': '').'>'.$arEnum["VALUE"].'</option>';
			}
			$result = '<select name="'.$arHtmlControl["NAME"].'" size="'.$arUserField["SETTINGS"]["LIST_HEIGHT"].'">';
			if($arUserField["MANDATORY"]!="Y")
				$result .= '<option value=""'.(!$bWasSelect? ' selected': '').'>'.GetMessage("MAIN_NO").'</option>';
			$result .= $result2;
			$result .= '</select>';
		}
		return $result;
	}

	function GetEditFormHTMLMulty($arUserField, $arHtmlControl)
	{
		if(!is_array($arHtmlControl["VALUE"]))
			$arHtmlControl["VALUE"] = array();
		$result = '';
		$rsEnum = call_user_func_array(
			array($arUserField["USER_TYPE"]["CLASS_NAME"], "getlist"),
			array(
				$arUserField,
			)
		);
		if($arUserField["SETTINGS"]["DISPLAY"]=="CHECKBOX")
		{
			while($arEnum = $rsEnum->GetNext())
			{
				$bSelected = (
					(in_array($arEnum["ID"], $arHtmlControl["VALUE"])) ||
					($arUserField["ENTITY_VALUE_ID"]<=0 && $arEnum["DEF"]=="Y")
				);
				$bWasSelect = $bWasSelect || $bSelected;
				$result .= '<label><input type="checkbox" value="'.$arEnum["ID"].'" name="'.$arHtmlControl["NAME"].'"'.($bSelected? ' checked': '').'>'.$arEnum["VALUE"].'</label><br>';
			}
		}
		else
		{
			$result = '<select multiple name="'.$arHtmlControl["NAME"].'" size="'.$arUserField["SETTINGS"]["LIST_HEIGHT"].'" >';
			if($arUserField["MANDATORY"]!="Y")
				$result .= '<option value=""'.(!$arHtmlControl["VALUE"]? ' selected': '').'>'.GetMessage("MAIN_NO").'</option>';
			while($arEnum = $rsEnum->GetNext())
			{
				$bSelected = (
					(in_array($arEnum["ID"], $arHtmlControl["VALUE"])) ||
					($arUserField["ENTITY_VALUE_ID"]<=0 && $arEnum["DEF"]=="Y")
				);
				$result .= '<option value="'.$arEnum["ID"].'"'.($bSelected? ' selected': '').'>'.$arEnum["VALUE"].'</option>';
			}
			$result .= '</select>';
		}
		return $result;
	}

	function GetFilterHTML($arUserField, $arHtmlControl)
	{
		if(!is_array($arHtmlControl["VALUE"]))
			$arHtmlControl["VALUE"] = array();
		$result = '';
		$rsEnum = call_user_func_array(
			array($arUserField["USER_TYPE"]["CLASS_NAME"], "getlist"),
			array(
				$arUserField,
			)
		);
		$result = '<select multiple name="'.$arHtmlControl["NAME"].'[]" size="'.$arUserField["SETTINGS"]["LIST_HEIGHT"].'" >';
		$result .= '<option value=""'.(!$arHtmlControl["VALUE"]? ' selected': '').'>'.GetMessage("MAIN_NO").'</option>';
		while($arEnum = $rsEnum->GetNext())
		{
			$result .= '<option value="'.$arEnum["ID"].'"'.(in_array($arEnum["ID"], $arHtmlControl["VALUE"])? ' selected': '').'>'.$arEnum["VALUE"].'</option>';
		}
		$result .= '</select>';
		return $result;
	}

	function GetAdminListViewHTML($arUserField, $arHtmlControl)
	{
		static $cache = array();
		if(!array_key_exists($arHtmlControl["VALUE"], $cache))
		{
			$rsEnum = call_user_func_array(
				array($arUserField["USER_TYPE"]["CLASS_NAME"], "getlist"),
				array(
					$arUserField,
				)
			);
			while($arEnum = $rsEnum->GetNext())
				$cache[$arEnum["ID"]] = $arEnum["VALUE"];
		}
		if(!array_key_exists($arHtmlControl["VALUE"], $cache))
			$cache[$arHtmlControl["VALUE"]] = "&nbsp;";
		return $cache[$arHtmlControl["VALUE"]];
	}

	function GetAdminListEditHTML($arUserField, $arHtmlControl)
	{
		$result = '';
		$rsEnum = call_user_func_array(
			array($arUserField["USER_TYPE"]["CLASS_NAME"], "getlist"),
			array(
				$arUserField,
			)
		);
		$result = '<select name="'.$arHtmlControl["NAME"].'" size="'.$arUserField["SETTINGS"]["LIST_HEIGHT"].'">';
		if($arUserField["MANDATORY"]!="Y")
			$result .= '<option value=""'.(!$arHtmlControl["VALUE"]? ' selected': '').'>'.GetMessage("MAIN_NO").'</option>';
		while($arEnum = $rsEnum->GetNext())
		{
			$result .= '<option value="'.$arEnum["ID"].'"'.($arHtmlControl["VALUE"]==$arEnum["ID"]? ' selected': '').'>'.$arEnum["VALUE"].'</option>';
		}
		$result .= '</select>';
		return $result;
	}

	function GetAdminListEditHTMLMulty($arUserField, $arHtmlControl)
	{
		if(!is_array($arHtmlControl["VALUE"]))
			$arHtmlControl["VALUE"] = array();
		$result = '';
		$rsEnum = call_user_func_array(
			array($arUserField["USER_TYPE"]["CLASS_NAME"], "getlist"),
			array(
				$arUserField,
			)
		);
		$result = '<select multiple name="'.$arHtmlControl["NAME"].'[]" size="'.$arUserField["SETTINGS"]["LIST_HEIGHT"].'">';
		if($arUserField["MANDATORY"]!="Y")
			$result .= '<option value=""'.(!$arHtmlControl["VALUE"]? ' selected': '').'>'.GetMessage("MAIN_NO").'</option>';
		while($arEnum = $rsEnum->GetNext())
		{
			$result .= '<option value="'.$arEnum["ID"].'"'.(in_array($arEnum["ID"], $arHtmlControl["VALUE"])? ' selected': '').'>'.$arEnum["VALUE"].'</option>';
		}
		$result .= '</select>';
		return $result;
	}

	function CheckFields($arUserField, $value)
	{
		$aMsg = array();
		return $aMsg;
	}

	function GetList($arUserField)
	{
		$obEnum = new CUserFieldEnum;
		$rsEnum = $obEnum->GetList(array(), array("USER_FIELD_ID"=>$arUserField["ID"]));
		return $rsEnum;
	}
}
AddEventHandler("main", "OnUserTypeBuildList", array("CUserTypeEnum", "GetUserTypeDescription"));
?>