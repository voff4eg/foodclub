<?
IncludeModuleLangFile(__FILE__);

class CIBlockPropertyHTML
{
	function GetUserTypeDescription()
	{
		return array(
			"PROPERTY_TYPE"		=>"S",
			"USER_TYPE"		=>"HTML",
			"DESCRIPTION"		=>GetMessage("IBLOCK_PROP_HTML_DESC"),
			"GetPublicViewHTML"	=>array("CIBlockPropertyHTML","GetPublicViewHTML"),
			"GetAdminListViewHTML"	=>array("CIBlockPropertyHTML","GetAdminListViewHTML"),
			"GetPropertyFieldHtml"	=>array("CIBlockPropertyHTML","GetPropertyFieldHtml"),
			"ConvertToDB"		=>array("CIBlockPropertyHTML","ConvertToDB"),
			"ConvertFromDB"		=>array("CIBlockPropertyHTML","ConvertFromDB"),
			"GetLength"		=>array("CIBlockPropertyHTML","GetLength"),
		);
	}

	function GetPublicViewHTML($arProperty, $value, $strHTMLControlName)
	{
		if(!is_array($value["VALUE"]))
			$value = CIBlockPropertyHTML::ConvertFromDB($arProperty, $value);
		$ar = $value["VALUE"];
		if($ar)
			return FormatText($ar["TEXT"], $ar["TYPE"]);
		else
			return "";
	}

	function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName)
	{
		if(!is_array($value["VALUE"]))
			$value = CIBlockPropertyHTML::ConvertFromDB($arProperty, $value);
		$ar = $value["VALUE"];
		if($ar)
		{
			//if (strToLower($ar["TYPE"]) != "text")
			//	return $ar["TEXT"];
			//else
				return htmlspecialcharsex($ar["TYPE"].":".$ar["TEXT"]);
		}
		else
			return "&nbsp;";
	}

	function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
	{
		global $APPLICATION;

		$strHTMLControlName["VALUE"] = htmlspecialcharsEx($strHTMLControlName["VALUE"]);
		if (!is_array($value["VALUE"]))
			$value = CIBlockPropertyHTML::ConvertFromDB($arProperty, $value);
		$ar = $value["VALUE"];
		if (strToLower($ar["TYPE"]) != "text")
			$ar["TYPE"] = "html";
		else
			$ar["TYPE"] = "text";
		ob_start();
		?><table><?
		if($strHTMLControlName["MODE"]=="FORM_FILL" && COption::GetOptionString("iblock", "use_htmledit", "Y")=="Y" && CModule::IncludeModule("fileman")):
		?><tr>
			<td colspan="2" align="center">
			<input type="hidden" name="<?=$strHTMLControlName["VALUE"]?>" value="">
				<?
				$text_name = preg_replace("/([^a-z0-9])/is", "_", $strHTMLControlName["VALUE"]."[TEXT]");
				$text_type = preg_replace("/([^a-z0-9])/is", "_", $strHTMLControlName["VALUE"]."[TYPE]");
				CFileMan::AddHTMLEditorFrame($text_name, $ar["TEXT"], $text_type, strToLower($ar["TYPE"]), 300, "N", 0, "", "");

				?>
			</td>
		</tr>
		<?else:?>
		<tr>
			<td><?echo GetMessage("IBLOCK_DESC_TYPE")?></td>
			<td>
				<input type="radio" name="<?=$strHTMLControlName["VALUE"]?>[TYPE]" id="<?=$strHTMLControlName["VALUE"]?>[TYPE][TEXT]" value="text" <?if($ar["TYPE"]!="html")echo " checked"?>>
				<label for="<?=$strHTMLControlName["VALUE"]?>[TYPE][TEXT]"><?echo GetMessage("IBLOCK_DESC_TYPE_TEXT")?></label> /
				<input type="radio" name="<?=$strHTMLControlName["VALUE"]?>[TYPE]" id="<?=$strHTMLControlName["VALUE"]?>[TYPE][HTML]" value="html"<?if($ar["TYPE"]=="html")echo " checked"?>>
				<label for="<?=$strHTMLControlName["VALUE"]?>[TYPE][HTML]"><?echo GetMessage("IBLOCK_DESC_TYPE_HTML")?></label></td>
		</tr>
		<tr>
			<td colspan="2" align="center"><textarea cols="60" rows="10" name="<?=$strHTMLControlName["VALUE"]?>[TEXT]" style="width:100%"><?=$ar["TEXT"]?></textarea></td>
		</tr>
		<?endif;
		?></table><?
		$return = ob_get_contents();
		ob_end_clean();
		return  $return;
	}

	function ConvertToDB($arProperty, $value)
	{
		$return = false;
		if (is_array($value) && is_set($value, "VALUE") && (strLen(trim($value["VALUE"]["TEXT"])) > 0))
		{
			$value = CIBlockPropertyHTML::CheckArray($value["VALUE"]);
			$return["VALUE"] = serialize($value);
		}
		return $return;
	}

	function ConvertFromDB($arProperty, $value)
	{
		$return = false;
		if (!is_array($value["VALUE"]))
		{
			$return["VALUE"] = unserialize($value["VALUE"]);
		}
		return $return;
	}

	function CheckArray($arFields = false)
	{
		$return = false;
		if (!is_array($arFields))
		{
			$return = unserialize($arFields);
		}
		else
			$return = $arFields;

		if ($return)
		{
			if (is_set($return, "TEXT") && (strLen(trim($return["TEXT"])) > 0))
			{
				$return["TYPE"] = strToUpper($return["TYPE"]);
				if (($return["TYPE"] != "TEXT") && ($return["TYPE"] != "HTML"))
					$return["TYPE"] = "HTML";
			}
			else
			{
				$return = false;
			}
		}
		return $return;
	}

	function GetLength($arProperty, $value)
	{
		if(is_array($value) && array_key_exists("VALUE", $value))
			return strLen(trim($value["VALUE"]["TEXT"]));
		else
			return 0;
	}
}
AddEventHandler("iblock", "OnIBlockPropertyBuildList", array("CIBlockPropertyHTML", "GetUserTypeDescription"));
?>
