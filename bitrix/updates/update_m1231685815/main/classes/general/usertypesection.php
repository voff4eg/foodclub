<?
IncludeModuleLangFile(__FILE__);

class CUserTypeIBlockSection extends CUserTypeEnum
{
	function GetUserTypeDescription()
	{
		return array(
			"USER_TYPE_ID" => "iblock_section",
			"CLASS_NAME" => "CUserTypeIBlockSection",
			"DESCRIPTION" => GetMessage("USER_TYPE_IBSEC_DESCRIPTION"),
			"BASE_TYPE" => "int",
		);
	}

	function PrepareSettings($arUserField)
	{
		$height = intval($arUserField["SETTINGS"]["LIST_HEIGHT"]);
		$disp = $arUserField["SETTINGS"]["DISPLAY"];
		if($disp!="CHECKBOX" && $disp!="LIST")
			$disp = "LIST";
		$iblock_id = intval($arUserField["SETTINGS"]["IBLOCK_ID"]);
		if($iblock_id <= 0)
			$iblock_id = "";
		return array(
			"DISPLAY" => $disp,
			"LIST_HEIGHT" => ($height < 2? 5: $height),
			"IBLOCK_ID" => $iblock_id,
		);
	}

	function GetSettingsHTML($arUserField = false, $arHtmlControl, $bVarsFromForm)
	{
		$result = '';
		if($bVarsFromForm)
			$value = $GLOBALS[$arHtmlControl["NAME"]]["IBLOCK_ID"];
		elseif(is_array($arUserField))
			$value = $arUserField["SETTINGS"]["IBLOCK_ID"];
		else
			$value = "";
		$result .= '
		<tr valign="top">
			<td>'.GetMessage("USER_TYPE_IBSEC_DISPLAY").':</td>
			<td>
				'.GetIBlockDropDownList($value, $arHtmlControl["NAME"].'[IBLOCK_TYPE_ID]', $arHtmlControl["NAME"].'[IBLOCK_ID]').'
			</td>
		</tr>
		';
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
				<label><input type="radio" name="'.$arHtmlControl["NAME"].'[DISPLAY]" value="LIST" '.("LIST"==$value? 'checked="checked"': '').'>'.GetMessage("USER_TYPE_IBSEC_LIST").'</label><br>
				<label><input type="radio" name="'.$arHtmlControl["NAME"].'[DISPLAY]" value="CHECKBOX" '.("CHECKBOX"==$value? 'checked="checked"': '').'>'.GetMessage("USER_TYPE_IBSEC_CHECKBOX").'</label><br>
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
			<td>'.GetMessage("USER_TYPE_IBSEC_LIST_HEIGHT").':</td>
			<td>
				<input type="text" name="'.$arHtmlControl["NAME"].'[LIST_HEIGHT]" size="10" value="'.$value.'">
			</td>
		</tr>
		';
		return $result;
	}

	function CheckFields($arUserField, $value)
	{
		$aMsg = array();
		return $aMsg;
	}

	function GetList($arUserField)
	{
		$obSection = new CIBlockSectionEnum;
		$rsSection = $obSection->GetTreeList($arUserField["SETTINGS"]["IBLOCK_ID"]);
		return $rsSection;
	}
}
if(CModule::IncludeModule('iblock'))
{
	class CIBlockSectionEnum extends CDBResult
	{
		function GetTreeList($IBLOCK_ID)
		{
			$rs = CIBlockSection::GetTreeList(Array("IBLOCK_ID"=>$IBLOCK_ID));
			if($rs)
			{
				$rs = new CIBlockSectionEnum($rs);
			}
			return $rs;
		}

		function GetNext()
		{
			$r = parent::GetNext();
			if($r)
				$r["VALUE"] = str_repeat(" . ", $r["DEPTH_LEVEL"]).$r["NAME"];
			return $r;
		}
	}
	AddEventHandler("main", "OnUserTypeBuildList", array("CUserTypeIBlockSection", "GetUserTypeDescription"));
}
?>