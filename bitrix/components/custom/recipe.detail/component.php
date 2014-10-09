<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @global CDatabase $DB */
/** @global CUser $USER */
/** @global CMain $APPLICATION */

CPageOption::SetOptionString("main", "nav_page_in_session", "N");

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;

$arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"]);
if(strlen($arParams["IBLOCK_TYPE"])<=0)
	$arParams["IBLOCK_TYPE"] = "news";

$arParams["ELEMENT_ID"] = intval($arParams["~ELEMENT_ID"]);
if($arParams["ELEMENT_ID"] > 0 && $arParams["ELEMENT_ID"]."" != $arParams["~ELEMENT_ID"])
{
	ShowError(GetMessage("T_NEWS_DETAIL_NF"));
	@define("ERROR_404", "Y");
	if($arParams["SET_STATUS_404"]==="Y")
		CHTTP::SetStatus("404 Not Found");
	return;
}

$arParams["CHECK_DATES"] = $arParams["CHECK_DATES"]!="N";
if(!is_array($arParams["FIELD_CODE"]))
	$arParams["FIELD_CODE"] = array();
foreach($arParams["FIELD_CODE"] as $key=>$val)
	if(!$val)
		unset($arParams["FIELD_CODE"][$key]);
if(!is_array($arParams["PROPERTY_CODE"]))
	$arParams["PROPERTY_CODE"] = array();
foreach($arParams["PROPERTY_CODE"] as $k=>$v)
	if($v==="")
		unset($arParams["PROPERTY_CODE"][$k]);

$arParams["IBLOCK_URL"]=trim($arParams["IBLOCK_URL"]);

$arParams["META_KEYWORDS"]=trim($arParams["META_KEYWORDS"]);
if(strlen($arParams["META_KEYWORDS"])<=0)
	$arParams["META_KEYWORDS"] = "-";
$arParams["META_DESCRIPTION"]=trim($arParams["META_DESCRIPTION"]);
if(strlen($arParams["META_DESCRIPTION"])<=0)
	$arParams["META_DESCRIPTION"] = "-";
$arParams["BROWSER_TITLE"]=trim($arParams["BROWSER_TITLE"]);
if(strlen($arParams["BROWSER_TITLE"])<=0)
	$arParams["BROWSER_TITLE"] = "-";

$arParams["INCLUDE_IBLOCK_INTO_CHAIN"] = $arParams["INCLUDE_IBLOCK_INTO_CHAIN"]!="N";
$arParams["ADD_SECTIONS_CHAIN"] = $arParams["ADD_SECTIONS_CHAIN"]!="N"; //Turn on by default
$arParams["ADD_ELEMENT_CHAIN"] = (isset($arParams["ADD_ELEMENT_CHAIN"]) && $arParams["ADD_ELEMENT_CHAIN"] == "Y");
$arParams["SET_TITLE"]=$arParams["SET_TITLE"]!="N";
$arParams["SET_BROWSER_TITLE"] = (isset($arParams["SET_BROWSER_TITLE"]) && $arParams["SET_BROWSER_TITLE"] === 'N' ? 'N' : 'Y');
$arParams["SET_META_KEYWORDS"] = (isset($arParams["SET_META_KEYWORDS"]) && $arParams["SET_META_KEYWORDS"] === 'N' ? 'N' : 'Y');
$arParams["SET_META_DESCRIPTION"] = (isset($arParams["SET_META_DESCRIPTION"]) && $arParams["SET_META_DESCRIPTION"] === 'N' ? 'N' : 'Y');
$arParams["ACTIVE_DATE_FORMAT"] = trim($arParams["ACTIVE_DATE_FORMAT"]);
if(strlen($arParams["ACTIVE_DATE_FORMAT"])<=0)
	$arParams["ACTIVE_DATE_FORMAT"] = $DB->DateFormatToPHP(CSite::GetDateFormat("SHORT"));

$arParams["DISPLAY_TOP_PAGER"] = $arParams["DISPLAY_TOP_PAGER"]=="Y";
$arParams["DISPLAY_BOTTOM_PAGER"] = $arParams["DISPLAY_BOTTOM_PAGER"]!="N";
$arParams["PAGER_TITLE"] = trim($arParams["PAGER_TITLE"]);
$arParams["PAGER_SHOW_ALWAYS"] = $arParams["PAGER_SHOW_ALWAYS"]!="N";
$arParams["PAGER_TEMPLATE"] = trim($arParams["PAGER_TEMPLATE"]);
$arParams["PAGER_SHOW_ALL"] = $arParams["PAGER_SHOW_ALL"]!=="N";

if($arParams["DISPLAY_TOP_PAGER"] || $arParams["DISPLAY_BOTTOM_PAGER"])
{
	$arNavParams = array(
		"nPageSize" => 1,
		"bShowAll" => $arParams["PAGER_SHOW_ALL"],
	);
	$arNavigation = CDBResult::GetNavParams($arNavParams);
}
else
{
	$arNavigation = false;
}

$arParams["SHOW_WORKFLOW"] = $_REQUEST["show_workflow"]=="Y";

$arParams["USE_PERMISSIONS"] = $arParams["USE_PERMISSIONS"]=="Y";
if(!is_array($arParams["GROUP_PERMISSIONS"]))
	$arParams["GROUP_PERMISSIONS"] = array(1);

$bUSER_HAVE_ACCESS = !$arParams["USE_PERMISSIONS"];
if($arParams["USE_PERMISSIONS"] && isset($USER) && is_object($USER))
{
	$arUserGroupArray = $USER->GetUserGroupArray();
	foreach($arParams["GROUP_PERMISSIONS"] as $PERM)
	{
		if(in_array($PERM, $arUserGroupArray))
		{
			$bUSER_HAVE_ACCESS = true;
			break;
		}
	}
}

if(!$bUSER_HAVE_ACCESS)
{
	ShowError(GetMessage("T_NEWS_DETAIL_PERM_DEN"));
	return 0;
}

require_once($_SERVER["DOCUMENT_ROOT"]."/classes/favorite.class.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/classes/factory.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/mark.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/main.class.php");

if($arParams["SHOW_WORKFLOW"] || $this->StartResultCache(false, array(($arParams["CACHE_GROUPS"]==="N"? false: $USER->GetGroups()),$bUSER_HAVE_ACCESS, $arNavigation)))
{

	if(!CModule::IncludeModule("iblock"))
	{
		$this->AbortResultCache();
		ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
		return;
	}

	$arFilter = array(
		"IBLOCK_LID" => SITE_ID,
		"IBLOCK_ACTIVE" => "Y",
		"ACTIVE" => "Y",
		"CHECK_PERMISSIONS" => "Y",
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"SHOW_HISTORY" => $arParams["SHOW_WORKFLOW"]? "Y": "N",
	);
	if($arParams["CHECK_DATES"])
		$arFilter["ACTIVE_DATE"] = "Y";
	if(intval($arParams["IBLOCK_ID"]) > 0)
		$arFilter["IBLOCK_ID"] = $arParams["IBLOCK_ID"];

	//Handle case when ELEMENT_CODE used
	if($arParams["ELEMENT_ID"] <= 0)
		$arParams["ELEMENT_ID"] = CIBlockFindTools::GetElementID(
			$arParams["ELEMENT_ID"],
			$arParams["ELEMENT_CODE"],
			false,
			false,
			$arFilter
		);

	$WF_SHOW_HISTORY = "N";
	if ($arParams["SHOW_WORKFLOW"] && CModule::IncludeModule("workflow"))
	{
		$WF_ELEMENT_ID = CIBlockElement::WF_GetLast($arParams["ELEMENT_ID"]);

		$WF_STATUS_ID = CIBlockElement::WF_GetCurrentStatus($WF_ELEMENT_ID, $WF_STATUS_TITLE);
		$WF_STATUS_PERMISSION = CIBlockElement::WF_GetStatusPermission($WF_STATUS_ID);

		if ($WF_STATUS_ID == 1 || $WF_STATUS_PERMISSION < 1)
			$WF_ELEMENT_ID = $arParams["ELEMENT_ID"];
		else
			$WF_SHOW_HISTORY = "Y";

		$arParams["ELEMENT_ID"] = $WF_ELEMENT_ID;
	}

	$CFClub = CFClub::getInstance();
	global $arKitchens;
	global $arDishType;

	$arSelect = array_merge($arParams["FIELD_CODE"], array(
		"ID",
		"NAME",
		"IBLOCK_ID",
		"IBLOCK_SECTION_ID",
		"DETAIL_TEXT",
		"DETAIL_TEXT_TYPE",
		"PREVIEW_TEXT",
		"PREVIEW_TEXT_TYPE",
		"DETAIL_PICTURE",
		"ACTIVE_FROM",
		"LIST_PAGE_URL",
		"DETAIL_PAGE_URL",
	));
	$bGetProperty = count($arParams["PROPERTY_CODE"]) > 0
			|| $arParams["BROWSER_TITLE"] != "-"
			|| $arParams["META_KEYWORDS"] != "-"
			|| $arParams["META_DESCRIPTION"] != "-";
	if($bGetProperty)
		$arSelect[]="PROPERTY_*";

	$arFilter["ID"] = $arParams["ELEMENT_ID"];
	$arFilter["SHOW_HISTORY"] = $WF_SHOW_HISTORY;

	//echo "<pre>";print_r($arFilter);echo "</pre>";die;

	$rsElement = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
	$rsElement->SetUrlTemplates($arParams["DETAIL_URL"], "", $arParams["IBLOCK_URL"]);
	if($obElement = $rsElement->GetNextElement())
	{
		$arResult = $obElement->GetFields();

		$arResult["NAV_RESULT"] = new CDBResult;
		if(($arResult["DETAIL_TEXT_TYPE"]=="html") && (strstr($arResult["DETAIL_TEXT"], "<BREAK />")!==false))
			$arPages=explode("<BREAK />", $arResult["DETAIL_TEXT"]);
		elseif(($arResult["DETAIL_TEXT_TYPE"]!="html") && (strstr($arResult["DETAIL_TEXT"], "&lt;BREAK /&gt;")!==false))
			$arPages=explode("&lt;BREAK /&gt;", $arResult["DETAIL_TEXT"]);
		else
			$arPages=array();
		$arResult["NAV_RESULT"]->InitFromArray($arPages);
		$arResult["NAV_RESULT"]->NavStart($arNavParams);
		if(count($arPages)==0)
		{
			$arResult["NAV_RESULT"] = false;
		}
		else
		{
			$arResult["NAV_STRING"] = $arResult["NAV_RESULT"]->GetPageNavStringEx($navComponentObject, $arParams["PAGER_TITLE"], $arParams["PAGER_TEMPLATE"], $arParams["PAGER_SHOW_ALWAYS"]);
			$arResult["NAV_CACHED_DATA"] = $navComponentObject->GetTemplateCachedData();

			$arResult["NAV_TEXT"] = "";
			while($ar = $arResult["NAV_RESULT"]->Fetch())
				$arResult["NAV_TEXT"].=$ar;
		}

		if(strlen($arResult["ACTIVE_FROM"])>0)
			$arResult["DISPLAY_ACTIVE_FROM"] = CIBlockFormatProperties::DateFormat($arParams["ACTIVE_DATE_FORMAT"], MakeTimeStamp($arResult["ACTIVE_FROM"], CSite::GetDateFormat()));
		else
			$arResult["DISPLAY_ACTIVE_FROM"] = "";

		$ipropValues = new \Bitrix\Iblock\InheritedProperty\ElementValues($arResult["IBLOCK_ID"], $arResult["ID"]);
		$arResult["IPROPERTY_VALUES"] = $ipropValues->getValues();

		if(isset($arResult["PREVIEW_PICTURE"]))
		{
			$arResult["PREVIEW_PICTURE"] = (0 < $arResult["PREVIEW_PICTURE"] ? CFile::GetFileArray($arResult["PREVIEW_PICTURE"]) : false);
			if ($arResult["PREVIEW_PICTURE"])
			{
				$arResult["PREVIEW_PICTURE"]["ALT"] = $arResult["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"];
				if ($arResult["PREVIEW_PICTURE"]["ALT"] == "")
					$arResult["PREVIEW_PICTURE"]["ALT"] = $arResult["NAME"];
				$arResult["PREVIEW_PICTURE"]["TITLE"] = $arResult["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"];
				if ($arResult["PREVIEW_PICTURE"]["TITLE"] == "")
					$arResult["PREVIEW_PICTURE"]["TITLE"] = $arResult["NAME"];
			}
		}
		if(isset($arResult["DETAIL_PICTURE"]))
		{
			$arResult["DETAIL_PICTURE"] = (0 < $arResult["DETAIL_PICTURE"] ? CFile::GetFileArray($arResult["DETAIL_PICTURE"]) : false);
			if ($arResult["DETAIL_PICTURE"])
			{
				$arResult["DETAIL_PICTURE"]["ALT"] = $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"];
				if ($arResult["DETAIL_PICTURE"]["ALT"] == "")
					$arResult["DETAIL_PICTURE"]["ALT"] = $arResult["NAME"];
				$arResult["DETAIL_PICTURE"]["TITLE"] = $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"];
				if ($arResult["DETAIL_PICTURE"]["TITLE"] == "")
					$arResult["DETAIL_PICTURE"]["TITLE"] = $arResult["NAME"];
			}
		}

		$arResult["FIELDS"] = array();
		foreach($arParams["FIELD_CODE"] as $code)
			if(array_key_exists($code, $arResult))
				$arResult["FIELDS"][$code] = $arResult[$code];

		if($bGetProperty)
			$arResult["PROPERTIES"] = $obElement->GetProperties();
		$arResult["DISPLAY_PROPERTIES"]=array();
		foreach($arParams["PROPERTY_CODE"] as $pid)
		{
			$prop = &$arResult["PROPERTIES"][$pid];
			if(
				(is_array($prop["VALUE"]) && count($prop["VALUE"])>0)
				|| (!is_array($prop["VALUE"]) && strlen($prop["VALUE"])>0)
			)
			{
				$arResult["DISPLAY_PROPERTIES"][$pid] = CIBlockFormatProperties::GetDisplayValue($arResult, $prop, "news_out");
			}
		}

		$arResult["IBLOCK"] = GetIBlock($arResult["IBLOCK_ID"], $arResult["IBLOCK_TYPE"]);

		$arResult["SECTION"] = array("PATH" => array());
		$arResult["SECTION_URL"] = "";
		if($arParams["ADD_SECTIONS_CHAIN"] && $arResult["IBLOCK_SECTION_ID"] > 0)
		{
			$rsPath = CIBlockSection::GetNavChain($arResult["IBLOCK_ID"], $arResult["IBLOCK_SECTION_ID"]);
			$rsPath->SetUrlTemplates("", $arParams["SECTION_URL"]);
			while($arPath = $rsPath->GetNext())
			{
				$ipropValues = new \Bitrix\Iblock\InheritedProperty\SectionValues($arParams["IBLOCK_ID"], $arPath["ID"]);
				$arPath["IPROPERTY_VALUES"] = $ipropValues->getValues();
				$arResult["SECTION"]["PATH"][] = $arPath;
				$arResult["SECTION_URL"] = $arPath["~SECTION_PAGE_URL"];
			}
		}

		//Customization
		if(intval($arResult["PROPERTIES"]["main_ingredient"]["VALUE"]) > 0){
			$arResult["MAIN_INGREDIENT"] = CIBlockElement::GetByID($arResult["PROPERTIES"]["main_ingredient"]["VALUE"])->Fetch();
		}

		$arResult["OWNER"] = false;
		if($USER->IsAuthorized()){
			if($arResult['CREATED_BY'] == $USER->GetID()){
				$arResult["OWNER"] = true;
			}
		}

		if(!empty($arResult["PROPERTIES"]["technics"]["VALUE"])){
			$rsTechnics = CIBlockElement::GetList(array("name"=>"asc"),array("IBLOCK_CODE"=>"technics","ID"=>$arResult["PROPERTIES"]["technics"]["VALUE"]), false, false, array("ID","NAME","PREVIEW_PICTURE","PROPERTY_link"));
			while($arTechnic = $rsTechnics->GetNext()){
				if(intval($arTechnic["PREVIEW_PICTURE"]) > 0){
					$arFile = CFile::GetFileArray($arTechnic["PREVIEW_PICTURE"]);
					if($arFile)
						$arTechnic["PICTURE"] = $arFile["SRC"];
				}
				$arResult["TECHNICS"][] = $arTechnic;
			}
		}

		$strHtml = '';
		$strJavaOne = '';
		$strJavaTwo = '';
		$intNum = 0;

		foreach ($arKitchens as $arItem)
		{
			$strHtml .= '<option value="'.$arItem['ID'].'" '.($arProp['kitchen']['VALUE'] == $arItem['ID'] ? "selected='selected'" : "").'>'.$arItem['NAME'].'</option>';
			if (strlen($strJavaOne) == 0) {$strJavaOne = '"'.$arItem['ID'].'"';} else {$strJavaOne .= ', "'.$arItem['ID'].'"';}

			$strJavaTwo .= 'cookingArray[1]['.$intNum.'] = new Array();';
			$strDumpId = ''; $strDumpName = '';

			$arItem['DISH'][0] = $arItem['DISH'][ $arProp['dish_type']['VALUE'] ];
			unset($arItem['DISH'][ $arProp['dish_type']['VALUE'] ]);

			foreach ($arItem['DISH'] as $arDash)
			{
				if (strlen($strDumpId) == 0) {$strDumpId = '"'.$arDash['ID'].'"';} else {$strDumpId .= ', "'.$arDash['ID'].'"';}
				if (strlen($strDumpName) == 0) {$strDumpName = '"'.$arDash['NAME'].'"';} else {$strDumpName .= ', "'.$arDash['NAME'].'"';}
			}
			$strJavaTwo .= "cookingArray[1][".$intNum."][0] = new Array($strDumpId);";
			$strJavaTwo .= "cookingArray[1][".$intNum."][1] = new Array($strDumpName);";
			$intNum++;
		}

		$arUnits = $CFClub->getUnitList();

		$strUnitHtml = '';
		$strUnitsHtml = '';
		$intNum = 0;
		$strTemp = '';
		foreach ($arUnits as $arUnit)
		{
			if (!isset($strId)) {$strId = '"'.$arUnit['ID'].'"';} else {$strId .= ', "'.$arUnit['ID'].'"';}
			if (!isset($strName)) {$strName = '"'.$arUnit['NAME'].'"';} else {$strName .= ', "'.$arUnit['NAME'].'"';}
			$strDumpId = ''; $strDumpName = ''; $strDumpUnit = '';
			$strTemp .= "ingredientsArray[2][$intNum] = new Array();";
			foreach($arUnit['UNITS'] as $intKey => $arItem)
			{
				if (strlen($strDumpId) == 0) {$strDumpId = '"'.$arItem['ID'].'"';} else {$strDumpId .= ', "'.$arItem['ID'].'"';}
				if (strlen($strDumpName) == 0) {$strDumpName = '"'.$arItem['NAME'].'"';} else {$strDumpName .= ', "'.$arItem['NAME'].'"';}
				if (strlen($strDumpUnit) == 0) {$strDumpUnit = '"'.$arItem['UNIT'].'"';} else {$strDumpUnit .= ', "'.$arItem['UNIT'].'"';}
				$arSortArray[ $arItem['ID'] ][0] = $intNum;
				$arSortArray[ $arItem['ID'] ][1] = $intKey;
			}
			$strTemp .= "ingredientsArray[2][".$intNum."][0] = new Array($strDumpId);";
			$strTemp .= "ingredientsArray[2][".$intNum."][1] = new Array($strDumpName);";
			$strTemp .= "ingredientsArray[2][".$intNum."][2] = new Array($strDumpUnit);";

			$intNum++;
		}
		$strUnitHtml .= "ingredientsArray[0] = new Array($strId);	ingredientsArray[1] = new Array($strName); ingredientsArray[2] = new Array();".$strTemp;

		if(!empty($arResult["PROPERTIES"]["recipt_steps"]["VALUE"])){
			$rsStages = CIBlockElement::GetList(Array(), Array("ID"=>$arResult["PROPERTIES"]["recipt_steps"]["VALUE"]), false, false, Array("ID", "NAME", "PREVIEW_PICTURE", "DETAIL_PICTURE", "PREVIEW_TEXT", "PROPERTY_ingredient", "PROPERTY_numer", "PROPERTY_parent"));
			while($arStage = $rsStages->GetNext()){

				$arStages[ $arStage['ID'] ]['ID'] = $arStage['ID'];
				$arStages[ $arStage['ID'] ]['NAME'] = $arStage['NAME'];
				$arStages[ $arStage['ID'] ]['PREVIEW_PICTURE'] = $arStage['PREVIEW_PICTURE'];
				$arStages[ $arStage['ID'] ]['DETAIL_PICTURE'] = $arStage['DETAIL_PICTURE'];
				$arStages[ $arStage['ID'] ]['~PREVIEW_TEXT'] = $arStage['~PREVIEW_TEXT'];
				$arStages[ $arStage['ID'] ]['PREVIEW_TEXT'] = $arStage['PREVIEW_TEXT'];
				$arStages[ $arStage['ID'] ]['PROPERTY_INGREDIENT_VALUE'][ $arStage['PROPERTY_INGREDIENT_VALUE_ID'] ] = $arStage['PROPERTY_INGREDIENT_VALUE'];
				$arStages[ $arStage['ID'] ]['PROPERTY_NUMER_VALUE'][ $arStage['PROPERTY_NUMER_VALUE_ID'] ] = $arStage['PROPERTY_NUMER_VALUE'];
				$arStages[ $arStage['ID'] ]['PROPERTY_PARENT_VALUE'] = $arStage['PROPERTY_PARENT_VALUE'];

			}
		}		
			
		global $DB;
		foreach($arStages as $Item)
		{
			$ElementId = $Item['ID'];

			$sqlIngridient = "SELECT * FROM `b_iblock_element_property` WHERE `IBLOCK_ELEMENT_ID` = ".$ElementId." AND `IBLOCK_PROPERTY_ID` = 3 ORDER BY `ID` ASC";
			$sqlNumer = "SELECT * FROM `b_iblock_element_property` WHERE `IBLOCK_ELEMENT_ID` = ".$ElementId." AND `IBLOCK_PROPERTY_ID` = 4 ORDER BY `ID` ASC";

			$rowFields = $DB->Query($sqlIngridient, false);
			while($Field = $rowFields->Fetch()){
				$arStages[ $Item['ID'] ]['PROPERTY_INGREDIENT_VALUE'][ $Field['ID'] ] = $Field['VALUE'];
			}
			ksort($arStages[ $Item['ID'] ]['PROPERTY_INGREDIENT_VALUE']);
			reset($arStages[ $Item['ID'] ]['PROPERTY_INGREDIENT_VALUE']);

			$rowFields = $DB->Query($sqlNumer, false);
			while($Field = $rowFields->Fetch()){
				$arStages[ $Item['ID'] ]['PROPERTY_NUMER_VALUE'][ $Field['ID'] ] = $Field['VALUE'];
			}
			ksort($arStages[ $Item['ID'] ]['PROPERTY_NUMER_VALUE']);
			reset($arStages[ $Item['ID'] ]['PROPERTY_NUMER_VALUE']);
		}

		$strMKey = 0;
		ob_start();
		$bFirst = true;?>
		<div class="instructions">
				<div class="description">
						<?=$arResult['~PREVIEW_TEXT']?>
				</div>
				<div class="border"></div>
		<?
		$kkals = 0;
		foreach($arStages as $strStep){

			$arStageFields = Array(
								"ID" => $strStep['ID'],
								"NAME" => $strStep['NAME'],
								"PREVIEW_PICTURE" => $strStep['PREVIEW_PICTURE'],
								"DETAIL_PICTURE" => $strStep['DETAIL_PICTURE'],
								"PREVIEW_TEXT" => $strStep['PREVIEW_TEXT'],
								"~PREVIEW_TEXT" => $strStep['~PREVIEW_TEXT']
								);
			$arStageProp = Array(
								"ingredient" => $strStep['PROPERTY_INGREDIENT_VALUE'],
								"numer" => $strStep['PROPERTY_NUMER_VALUE'],
								"parent" => $strStep['PROPERTY_PARENT_VALUE'],
								);
			$arKey = array_keys($arStageProp['ingredient']);
			$arStageProp['numer'] = array_combine($arKey, $arStageProp['numer']);

			$rsIng = CIBlockElement::GetList(Array(), Array("ID"=>$arStageProp['ingredient'],"IBLOCK_ID"=>3), false, false, Array("ID","NAME","CODE","PROPERTY_unit","PROPERTY_kkal","DETAIL_TEXT"));
			while($arIngSrc = $rsIng->GetNext()){
				$arIng[ $arIngSrc['ID'] ] = $arIngSrc;				
			}
			foreach($arStageProp['ingredient'] as $strKey => $strItem){
				if(!is_null($strItem)){
					$arUnitsVal[] = Array(
						"ID"    => $strItem,
						"NAME"  => $arIng[ $strItem ]['NAME'],
						"UNIT"  => $arIng[ $strItem ]['PROPERTY_UNIT_VALUE'],
						"VALUE" => $arStageProp['numer'][ $strKey ],
					);

					$arAllUnits[$strItem][] = Array(
						"ID"    => $strItem,
						"NAME"  => $arIng[ $strItem ]['NAME'],
						"CODE"  => $arIng[ $strItem ]['CODE'],
						"UNIT"  => $arIng[ $strItem ]['PROPERTY_UNIT_VALUE'],
						"VALUE" => $arStageProp['numer'][ $strKey ],
						"KKAL"  => $arIng[ $strItem ]['PROPERTY_KKAL_VALUE'],
						"LINK"  => ($arIng[ $strItem ]['DETAIL_TEXT'] ? true : false)
					);
					if(floatval(str_replace(",",".",$arIng[ $strItem ]['PROPERTY_KKAL_VALUE'])) <= 0){
						$kkal = 0;
					}else{
						$kkal = floatval(str_replace(",",".",$arIng[ $strItem ]['PROPERTY_KKAL_VALUE']));
					}
					if(floatval(str_replace(",",".",$arStageProp['numer'][ $strKey ])) <= 0){
						$mass = 0;
					}else{
						$mass = floatval(str_replace(",",".",$arStageProp['numer'][ $strKey ]));
					}
					//echo $mass." * ".$kkal." = ".$mass*$kkal;
					$kkals += $mass * $kkal;
				}
			}
			$arResult["units"] = $arAllUnits;
			$arResult["kkals"] = $kkals;
			$rsFile = CFile::GetByID($arStageFields['PREVIEW_PICTURE']);
			$arFile = $rsFile->Fetch();
			$rsDetailFile = CFile::GetByID($arStageFields['DETAIL_PICTURE']);
			$arDetailFile = $rsDetailFile->Fetch();
			?>
			<?if(!$bFirst){?><div class="border"></div><?} else {$bFirst = false;}?>
				<div class="stage">
					<div class="body">
					<?if($arResult["PROPERTIES"]["vegetarian"]["VALUE"] == "Y"):?><input type="hidden" name="vegetarian" value="y" /><?endif;?>
					<?if(count($arUnitsVal) > 0){?>
					<div class="needed">
						<h2>Ингредиенты <?=$strMKey+1;?>-го этапа:</h2>
						<table>
							<?$flag = false;
							foreach($arUnitsVal as $strKey => $arItem):
								if($flag == false){ echo "<tr>";}?>
								<td class="ing_name<?=($flag == true ? " border" : "")?>"><?=$arItem['NAME']?></td>
								<td class="ing_amount"><?=str_replace(Array("1/2", "1/4", "3/4"), Array("&frac12;","&frac14;","&frac34;"), $arItem['VALUE'])?> <?=$arItem['UNIT']?></td><?
								if($flag == true){ echo "</tr>";}
								if($flag == false){$flag = true;}else{$flag = false;}
							endforeach; 
							if(count($arUnitsVal) % 2 != 0){
								echo "<td class='ing_name border'></td><td class='ing_amount'></td></tr>";
							}
							unset($arUnitsVal);?>
						</table>
					</div>
					<?} //if?>
					<?if(intval($arStageFields["DETAIL_PICTURE"])):?>
						<div class="image"><div class="screen"><div style="width: <?=$arDetailFile['WIDTH']?>px; height: <?=$arDetailFile['HEIGHT']?>px;"></div></div><img src="/upload/<?=$arFile['SUBDIR']?>/<?=$arFile['FILE_NAME']?>" data-src="/upload/<?=$arDetailFile['SUBDIR']?>/<?=$arDetailFile['FILE_NAME']?>" alt="<?=(strlen($arFile['DESCRIPTION']) > 0 ? $arFile['DESCRIPTION'] : $arRecipe['NAME']." ".$strMKey." этап")?>" title="<?=(strlen($arFile['DESCRIPTION']) > 0 ? $arFile['DESCRIPTION'] : $arRecipe['NAME']." ".$strMKey." этап")?>" width="<?=$arDetailFile['WIDTH']?>" height="<?=$arDetailFile['HEIGHT']?>" class="photo"></div>
					<?elseif(IntVal($arStageFields['PREVIEW_PICTURE']) > 0):?>
						<div class="image"><div class="screen"><div style="width: <?=$arFile['WIDTH']?>px; height: <?=$arFile['HEIGHT']?>px;"></div></div><img src="/upload/<?=$arFile['SUBDIR']?>/<?=$arFile['FILE_NAME']?>" alt="<?=(strlen($arFile['DESCRIPTION']) > 0 ? $arFile['DESCRIPTION'] : $arRecipe['NAME']." ".$strMKey." этап")?>" title="<?=(strlen($arFile['DESCRIPTION']) > 0 ? $arFile['DESCRIPTION'] : $arRecipe['NAME']." ".$strMKey." этап")?>" width="<?=$arFile['WIDTH']?>" height="<?=$arFile['HEIGHT']?>" class="photo"></div>
					<?endif;?>
					<?if(strlen($arStageFields['~PREVIEW_TEXT']) > 0){?>
						<div class="instruction">
							<?=$arStageFields['~PREVIEW_TEXT']?>
						</div>
					<?}?>
					</div>

					<div class="clear"></div>
				</div>

			<?
			$strMKey++;
		}?>
		</div>
		<?
		$strStages = ob_get_contents();
		ob_end_clean();
		
		$rsAuthor = CUser::GetByID($arResult['CREATED_BY']);
		if($arAuthor = $rsAuthor->Fetch()){
			if(strpos($arAuthor['EXTERNAL_AUTH_ID'], "OPENID") !== false){

				if(strpos($arAuthor['LOGIN'], "livejournal") !== false){					
					$arAuthor['LOGIN_TYPE'] = "lj";					
				}

			} else {
				$arAuthor['LOGIN_TYPE'] = "fc";
			}
			$arAuthor['URL'] = "/profile/".$arAuthor['ID']."/";

			if(strlen($arAuthor["NAME"]) > 0 && strlen($arAuthor["LAST_NAME"]) > 0){
		     	$arAuthor["FULLNAME"] = $arAuthor["NAME"]." ".$arAuthor["LAST_NAME"];
		 	}else{
		 		$arAuthor["FULLNAME"] = $arAuthor["LOGIN"];
		 	}			

			if(intval($arAuthor['PERSONAL_PHOTO']) > 0){
				$rsAvatar = CFile::GetByID($arAuthor['PERSONAL_PHOTO']);
				if($Avatar = $rsAvatar->Fetch()){
					$arAuthor["avatar"] = "/upload/".$Avatar['SUBDIR']."/".$Avatar['FILE_NAME'];					
				}
				$file = CFile::ResizeImageGet($arAuthor['PERSONAL_PHOTO'], array('width'=>30, 'height'=>30), BX_RESIZE_IMAGE_PROPORTIONAL, true);
				$arAuthor["small_avatar"] = $file["src"];
			} else {
				$arAuthor["avatar"] = "/images/avatar/avatar.jpg";
				$arAuthor["small_avatar"] = "/images/avatar/avatar_small.jpg";
			}

			$arResult["AUTHOR"] = $arAuthor;

			$rsCount = CIBlockElement::GetProperty(5, $arResult["ID"], "sort", "asc", Array("CODE"=>"comment_count"));
			if($arCount = $rsCount->Fetch()){
				$mixCount = IntVal($arCount["VALUE"]);
				//$CACHE_MANAGER->RegisterTag("recipe#".IntVal($_REQUEST['r'])."#ccount");
			}
		}
		
		if(!is_null($arResult["PROPERTIES"]["block_like"]["VALUE"])){
			if(!is_null($arResult["PROPERTIES"]["block_photo"]["VALUE"])){
				$rsSearchFile = CFile::GetByID($arResult["PROPERTIES"]["block_photo"]["VALUE"]);
				$arSearchFile = $rsSearchFile->Fetch();
			}
			
			if(!is_null($arResult["PROPERTIES"]["block_search"]["VALUE"])){
				$strSearch = $arResult["PROPERTIES"]["block_search"]["VALUE"];
			}
			
			$rsBlockRecipe = CIBlockElement::GetList(Array(), Array("ID"=>$arResult["PROPERTIES"]["block_like"]["VALUE"]), false, false, Array("ID", "NAME", "CODE", "CREATED_BY", "PROPERTY_comment_count","PREVIEW_PICTURE", "DETAIL_PICTURE"));			
			while($arBlockRecipe = $rsBlockRecipe->GetNext()){

				$rsUser = CUser::GetByID($arBlockRecipe['CREATED_BY']);
				$arUser = $rsUser->Fetch();
				if(strlen($arUser["NAME"]) > 0 && strlen($arUser["LAST_NAME"]) > 0){
			     	$arUser["FULLNAME"] = $arUser["NAME"]." ".$arUser["LAST_NAME"];
			 	}else{
			 		$arUser["FULLNAME"] = $arUser["LOGIN"];
			 	}
				$arBlockRecipe["USER"] = $arUser;
				
				$BlockRecipes[] = $arBlockRecipe;
				if(!is_null($arBlockRecipe['PREVIEW_PICTURE'])){
					$rsBlockFile = CFile::GetByID($arBlockRecipe['PREVIEW_PICTURE']);
					$arBlockFile[$arBlockRecipe["ID"]] = $rsBlockFile->Fetch();
					$arBlockFileEx[$arBlockRecipe["ID"]] = $arBlockRecipe['PREVIEW_PICTURE'];
				}
				if(!is_null($arBlockRecipe['DETAIL_PICTURE'])){
					$rsBlockFile = CFile::GetByID($arBlockRecipe['DETAIL_PICTURE']);
					$arBlockFile[$arBlockRecipe["ID"]] = $rsBlockFile->Fetch();
					$arBlockFileEx[$arBlockRecipe["ID"]] = $arBlockRecipe['DETAIL_PICTURE'];
				}
			}
			if(!empty($BlockRecipes)){	
			$resDt = CIBlockElement::GetByID($arResult["PROPERTIES"]["dish_type"]["VALUE"]);
			if($ar_res = $resDt->GetNext()){$DtName=$ar_res['NAME'];}
				ob_start();?>
				
				
			<div class="b-other-recipes">
				<div class="b-recipes-icon"></div>
				<h5><a href="/search/<?=$arResult["MAIN_INGREDIENT"]['NAME']?>/">Похожие рецепты</a></h5>
				<div class="b-or__block">
				
					<?foreach($BlockRecipes as $key=>$recipe):?>
					<div class="b-or__item recipe_list_item">
						<div class="b-or__item__photo"><a href="/detail/<?=($recipe['CODE'] ? $recipe['CODE'] : $recipe['ID'])?>/" 
						title="<?=$recipe["NAME"]?>">
						<?
						if($arBlockFileEx[$recipe["ID"]])
						{
							$renderImage = CFile::ResizeImageGet($arBlockFileEx[$recipe["ID"]], Array("width" => 246, "height" => 164), BX_RESIZE_IMAGE_EXACT, false); 
						}
						?>
						<img src="<?=$renderImage["src"]?>" alt="<?=$recipe["NAME"]?>" /></a></div>						
						<h5><a href="/detail/<?=($recipe['CODE'] ? $recipe['CODE'] : $recipe['ID'])?>/"><?=$recipe["NAME"]?></a></h5>
						<div class="i-clearfix"></div>
						<p class="author">От: <?=$recipe["USER"]['FULLNAME']?></p>
					</div>
					<?endforeach;?>
				</div>
				
				<a href="/search/<?=$DtName?>/" class="b-or__all">
					<span class="b-or__all__text">Другие популярные <?=$DtName?></span>
					<span class="b-or__all__photo">
						<span class="b-or__all__wrapper"><img src="/images/infoblock/new/1.jpg" width="45" alt="" /></span>
					</span>
				</a>
			</div>
			
			
				<?$strBlockLike = ob_get_contents();
				ob_end_clean();
			}else{
				$strBlockLike = '';
			}
		}
		if(isset($arKitchens) && $arResult["PROPERTIES"]["kitchen"]["VALUE"]){
			$arResult["KITCHEN"] = $arKitchens[ $arResult["PROPERTIES"]["kitchen"]["VALUE"] ];
		}
		if(isset($arDishType) && $arResult["PROPERTIES"]["dish_type"]["VALUE"]){
			$arResult["DISH_TYPE"] = $arDishType[ $arResult["PROPERTIES"]["dish_type"]["VALUE"] ];
		}
		$arResult["stages"] = $strStages;
		$arResult["like"] = $strBlockLike;
		$arResult["ccount"] = $mixCount;

		if($arResult["PROPERTIES"]["edit_deadline"]["VALUE"]){
			$bAllowEdit = ((!($USER->IsAdmin()) && (MakeTimeStamp($arResult["PROPERTIES"]["edit_deadline"]["VALUE"]) >= time())) || $USER->IsAdmin());
		}else{
			$bAllowEdit = ($USER->IsAdmin());
		}
		$arResult["bAllowEdit"] = $bAllowEdit;

		$this->SetResultCacheKeys(array(
			"ID",
			"CODE",
			"TAGS",
			"IBLOCK_ID",
			"NAV_CACHED_DATA",
			"NAME",
			"IBLOCK_SECTION_ID",
			"IBLOCK",
			"LIST_PAGE_URL", "~LIST_PAGE_URL",
			"SECTION_URL",
			"SECTION",
			"PREVIEW_PICTURE",
			"DETAIL_PICTURE",
			"PREVIEW_TEXT",
			"PROPERTIES",
			"IPROPERTY_VALUES",
			"TECHNICS",
			"AUTHOR",
			"MAIN_INGREDIENT",
			"OWNER",
			"KITCHEN",
			"DISH_TYPE",
			"stages",
			"like",
			"ccount",
			"kkals",
			//"MainFile",
			"units",
			"bAllowEdit"
		));

		$this->IncludeComponentTemplate();
	}
	else
	{
		$this->AbortResultCache();
		LocalRedirect("/404.php");
		/*ShowError(GetMessage("T_NEWS_DETAIL_NF"));
		@define("ERROR_404", "Y");
		if($arParams["SET_STATUS_404"]==="Y")
			CHTTP::SetStatus("404 Not Found");*/
	}
}

if(isset($arResult["ID"]))
{
	$arTitleOptions = null;
	if(CModule::IncludeModule("iblock"))
	{
		CIBlockElement::CounterInc($arResult["ID"]);

		if($USER->IsAuthorized())
		{
			if(
				$APPLICATION->GetShowIncludeAreas()
				|| $arParams["SET_TITLE"]
				|| isset($arResult[$arParams["BROWSER_TITLE"]])
			)
			{
				$arReturnUrl = array(
					"add_element" => CIBlock::GetArrayByID($arResult["IBLOCK_ID"], "DETAIL_PAGE_URL"),
					"delete_element" => (
						empty($arResult["SECTION_URL"])?
						$arResult["LIST_PAGE_URL"]:
						$arResult["SECTION_URL"]
					),
				);

				$arButtons = CIBlock::GetPanelButtons(
					$arResult["IBLOCK_ID"],
					$arResult["ID"],
					$arResult["IBLOCK_SECTION_ID"],
					Array(
						"RETURN_URL" => $arReturnUrl,
						"SECTION_BUTTONS" => false,
					)
				);

				if($APPLICATION->GetShowIncludeAreas())
					$this->AddIncludeAreaIcons(CIBlock::GetComponentMenu($APPLICATION->GetPublicShowMode(), $arButtons));

				if($arParams["SET_TITLE"] || isset($arResult[$arParams["BROWSER_TITLE"]]))
				{
					$arTitleOptions = array(
						'ADMIN_EDIT_LINK' => $arButtons["submenu"]["edit_element"]["ACTION"],
						'PUBLIC_EDIT_LINK' => $arButtons["edit"]["edit_element"]["ACTION"],
						'COMPONENT_NAME' => $this->GetName(),
					);
				}
			}
		}
	}

	$this->SetTemplateCachedData($arResult["NAV_CACHED_DATA"]);

	if($arParams["SET_TITLE"])
	{
		if ($arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"] != "")
			$APPLICATION->SetTitle($arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"], $arTitleOptions);
		else
			$APPLICATION->SetTitle($arResult["NAME"], $arTitleOptions);
	}

	if ($arParams["SET_BROWSER_TITLE"] === 'Y')
	{
		$browserTitle = \Bitrix\Main\Type\Collection::firstNotEmpty(
			$arResult["PROPERTIES"], array($arParams["BROWSER_TITLE"], "VALUE")
			,$arResult, $arParams["BROWSER_TITLE"]
			,$arResult["IPROPERTY_VALUES"], "ELEMENT_META_TITLE"
		);
		if (is_array($browserTitle))
			$APPLICATION->SetPageProperty("title", implode(" ", $browserTitle), $arTitleOptions);
		elseif ($browserTitle != "")
			$APPLICATION->SetPageProperty("title", $browserTitle, $arTitleOptions);
	}

	if ($arParams["SET_META_KEYWORDS"] === 'Y')
	{
		$metaKeywords = \Bitrix\Main\Type\Collection::firstNotEmpty(
			$arResult["PROPERTIES"], array($arParams["META_KEYWORDS"], "VALUE")
			,$arResult["IPROPERTY_VALUES"], "ELEMENT_META_KEYWORDS"
		);
		if (is_array($metaKeywords))
			$APPLICATION->SetPageProperty("keywords", implode(" ", $metaKeywords), $arTitleOptions);
		elseif ($metaKeywords != "")
			$APPLICATION->SetPageProperty("keywords", $metaKeywords, $arTitleOptions);
	}

	if ($arParams["SET_META_DESCRIPTION"] === 'Y')
	{
		$metaDescription = \Bitrix\Main\Type\Collection::firstNotEmpty(
			$arResult["PROPERTIES"], array($arParams["META_DESCRIPTION"], "VALUE")
			,$arResult["IPROPERTY_VALUES"], "ELEMENT_META_DESCRIPTION"
		);
		if (is_array($metaDescription))
			$APPLICATION->SetPageProperty("description", implode(" ", $metaDescription), $arTitleOptions);
		elseif ($metaDescription != "")
			$APPLICATION->SetPageProperty("description", $metaDescription, $arTitleOptions);
	}

	if($arParams["INCLUDE_IBLOCK_INTO_CHAIN"] && isset($arResult["IBLOCK"]["NAME"]))
	{
		$APPLICATION->AddChainItem($arResult["IBLOCK"]["NAME"], $arResult["~LIST_PAGE_URL"]);
	}

	if($arParams["ADD_SECTIONS_CHAIN"] && is_array($arResult["SECTION"]))
	{
		foreach($arResult["SECTION"]["PATH"] as $arPath)
		{
			if ($arPath["IPROPERTY_VALUES"]["SECTION_PAGE_TITLE"] != "")
				$APPLICATION->AddChainItem($arPath["IPROPERTY_VALUES"]["SECTION_PAGE_TITLE"], $arPath["~SECTION_PAGE_URL"]);
			else
				$APPLICATION->AddChainItem($arPath["NAME"], $arPath["~SECTION_PAGE_URL"]);
		}
	}
	if ($arParams["ADD_ELEMENT_CHAIN"])
	{
		if ($arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"] != "")
			$APPLICATION->AddChainItem($arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"]);
		else
			$APPLICATION->AddChainItem($arResult["NAME"]);
	}

	if(strlen($arResult["CODE"]) > 0 && $APPLICATION->GetCurDir() == "/detail/".$arResult["ID"]."/"){
		$APPLICATION->AddHeadString('<link rel="canonical" href="http://'.$_SERVER["SERVER_NAME"].'/detail/'.$arResult["CODE"].'/"/>');
		$APPLICATION->AddHeadString('<meta name="robots" content="none">');
	}

	//echo "<pre>";print_r($arResult);echo "</pre>";

	$APPLICATION->AddHeadString('<meta property="og:title" content="'.$arResult["NAME"].'"/>',true);
	$APPLICATION->AddHeadString('<meta property="og:type" content="food"/>',true);
	if(!empty($arResult['DETAIL_PICTURE']) > 0){
		$APPLICATION->AddHeadString('<meta property="og:image" content="http://'.$_SERVER["SERVER_NAME"].$arResult['DETAIL_PICTURE']["SRC"].'" />',true);
	}
	$APPLICATION->AddHeadString('<meta property="og:url" content="http://'.$_SERVER["SERVER_NAME"].$APPLICATION->GetCurDir().'" />',true);
	$APPLICATION->AddHeadString('<meta property="og:site_name" content="Кулинарные рецепты с пошаговыми фотографиями"/>',true);
	$APPLICATION->AddHeadString('<meta property="og:description" content="'.strip_tags($arResult["PREVIEW_TEXT"]).'"/>',true);

	return array(
		"ID" => $arResult["ID"],
		"TAGS" => $arResult["TAGS"],
		"like" => $arResult["like"]
	);
}
else
{
	return 0;
}
?>