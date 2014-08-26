<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule("iblock");
$arIBlock = CIBlock::GetArrayByID(5);

$Elements = CIBlockElement::GetList(Array("SORT"=>"ASC"), 
									Array("IBLOCK_ID"=>"5"), false, false
							);
while($obElement = $Elements->GetNextElement())
{
	$ElementFields = $obElement->GetFields();
	$ElementProps = $obElement->GetProperties();
	
	foreach($ElementProps as $Key => $Properti){
		$arProperty[$Key] = $Properti['VALUE'];
	}
	
	$_FILES["general_photo"] = CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"].CFile::GetPath($ElementFields['PREVIEW_PICTURE']));
	
	$arDETAIL_PICTURE = CIBlock::ResizePicture($_FILES["general_photo"], $arIBlock["FIELDS"]["DETAIL_PICTURE"]["DEFAULT_VALUE"]);
	$DETAIL_PICTURE = CFile::SaveFile($arDETAIL_PICTURE, "iblock");
	
	if(copy($_FILES["general_photo"]["tmp_name"], $_FILES["general_photo"]["tmp_name"]."~"))
	{
		$_FILES["PREVIEW_PICTURE"] = $_FILES["general_photo"];
		$_FILES["PREVIEW_PICTURE"]["tmp_name"] .= "~";
		
		$arPREVIEW_PICTURE = CIBlock::ResizePicture($_FILES["PREVIEW_PICTURE"], $arIBlock["FIELDS"]["PREVIEW_PICTURE"]["DEFAULT_VALUE"]);
		if(!is_array($arPREVIEW_PICTURE))
		{
			if($arIBlock["FIELDS"]["PREVIEW_PICTURE"]["DEFAULT_VALUE"]["IGNORE_ERRORS"] === "Y")
				$arPREVIEW_PICTURE = $_FILES["PREVIEW_PICTURE"];
			else
			{
				$arPREVIEW_PICTURE = array(
					"name" => false,
					"type" => false,
					"tmp_name" => false,
					"error" => 4,
					"size" => 0,
				);
			}
		}
	}
	
	
	$arLoadProductArray = Array(
		"MODIFIED_BY"     => $USER->GetID(),
		"IBLOCK_SECTION"  => false,
		"IBLOCK_ID"       => 5,
		"PROPERTY_VALUES" => $arProperty,
		"NAME"            => $ElementFields['NAME'],
		"ACTIVE"          => "Y",
		"PREVIEW_TEXT"    => $ElementFields['PREVIEW_TEXT'],
		"PREVIEW_PICTURE" => $arPREVIEW_PICTURE,
		"DETAIL_PICTURE"  => CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"].CFile::GetPath($DETAIL_PICTURE)),
	);
	
	$elStep   = new CIBlockElement;
	$elStep->Update($ElementFields['ID'], $arLoadProductArray);
}
?>