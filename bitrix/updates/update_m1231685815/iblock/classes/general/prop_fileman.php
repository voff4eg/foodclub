<?
IncludeModuleLangFile(__FILE__);

class CIBlockPropertyFileMan
{
	function GetUserTypeDescription()
	{
		return array(
			"PROPERTY_TYPE"		=>"S",
			"USER_TYPE"		=>"FileMan",
			"DESCRIPTION"		=>GetMessage("IBLOCK_PROP_FILEMAN_DESC"),
			"GetPropertyFieldHtml"	=>array("CIBlockPropertyFileMan","GetPropertyFieldHtml"),
			"ConvertToDB"		=>array("CIBlockPropertyFileMan","ConvertToDB"),
			"ConvertFromDB"		=>array("CIBlockPropertyFileMan","ConvertFromDB"),
		);
	}

	function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
	{
		global $APPLICATION;
/*		?><pre><b>$arProperty</b>:<br><?print_r($arProperty)?></pre><?
		?><pre><b>$value</b>:<br><?print_r($value)?></pre><?
		?><pre><b>$strHTMLControlName</b>:<br><?print_r($strHTMLControlName)?></pre><?
*/		if (strLen(trim($strHTMLControlName["FORM_NAME"])) <= 0)
			$strHTMLControlName["FORM_NAME"] = "form_element";
		ob_start();
		$name = preg_replace("/[^a-zA-Z0-9_]/i", "", htmlspecialchars($strHTMLControlName["VALUE"]));

		if(is_array($value["VALUE"]))
		{
			$value["VALUE"] = $value["VALUE"]["VALUE"];
			$value["DESCRIPTION"] = $value["DESCRIPTION"]["VALUE"];
		}
		?><input type="text" name="<?=htmlspecialchars($strHTMLControlName["VALUE"])?>" id="<?=preg_replace("/[^a-zA-Z0-9_]/i", "", htmlspecialchars($strHTMLControlName["VALUE"]))?>" size="<?=$arProperty["COL_COUNT"]?>" value="<?=htmlspecialcharsEx($value["VALUE"])?>">
			<input type="button" value="<?=GetMessage("IBLOCK_PROP_FILEMAN_VIEW")?>" OnClick="BtnClick<?=$name?>();">
			<?$APPLICATION->ShowFileSelectDialog(
			    "BtnClick".$name,
			    array("ELEMENT_ID" => $name));

		if($arProperty["WITH_DESCRIPTION"]=="Y")
			echo ' <span title="'.GetMessage("IBLOCK_ELEMENT_EDIT_PROP_DESC").'">'.GetMessage("IBLOCK_ELEMENT_EDIT_PROP_DESC_1").'<input name="DESCRIPTION_'.htmlspecialcharsEx($strHTMLControlName["VALUE"]).'" value="'.htmlspecialcharsEx($value["DESCRIPTION"]).'" size="18" type="text"></span>';
			echo "<br>";
		$return = ob_get_contents();
		ob_end_clean();
		return  $return;
	}

	function ConvertToDB($arProperty, $value)
	{
		$result = array();
		$return = array();
		if(is_array($value["VALUE"]))
		{
			$result["VALUE"] = $value["VALUE"]["VALUE"];
			$result["DESCRIPTION"] = $value["DESCRIPTION"]["VALUE"];
		}
		else
		{
			$result["VALUE"] = $value["VALUE"];
			$result["DESCRIPTION"] = $value["DESCRIPTION"];
		}
		$return["VALUE"] = trim($result["VALUE"]);
		$return["DESCRIPTION"] = trim($result["DESCRIPTION"]);
		return $return;
	}

	function ConvertFromDB($arProperty, $value)
	{
		$return = array();
		if (strLen(trim($value["VALUE"])) > 0)
			$return["VALUE"] = $value["VALUE"];
		if (strLen(trim($value["DESCRIPTION"])) > 0)
			$return["DESCRIPTION"] = $value["DESCRIPTION"];
		return $return;
	}
}

AddEventHandler("iblock", "OnIBlockPropertyBuildList", array("CIBlockPropertyFileMan", "GetUserTypeDescription"));
?>
