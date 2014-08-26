<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("iblock");
if( $USER->IsAdmin() || in_array(5, $USER->GetParam("GROUPS")) ){
foreach(range(0, count($_REQUEST['stage_description'])-1, 1) as $intKey=>$intNumer)
{
    $arProp = Array(); $arId = Array(); $arNumber = Array();
    foreach(range(0, count($_REQUEST['ingredients_' . $intNumer . '_id'])-1, 1) as $intX)
    {
        $arId[]     = $_REQUEST['ingredients_' . $intNumer . '_id'][$intX];
        $arNumber[] = $_REQUEST['ingredients_' . $intNumer . '_number'][$intX];
    }
    
    $arProp = Array("ingredient" => $arId, "numer" => $arNumber);
    
    $arPhoto = Array(
        "name"     => $_FILES["photo"]['name'][$intNumer],
        "type"     => $_FILES["photo"]['type'][$intNumer],
        "tmp_name" => $_FILES["photo"]['tmp_name'][$intNumer],
        "error"    => $_FILES["photo"]['error'][$intNumer],
        "size"     => $_FILES["photo"]['size'][$intNumer]
    );
    
    $arPreIMAGE 				= $arPhoto;
    $arPreIMAGE["old_file"] 	= "";
    $arPreIMAGE["del"] 			= "N";
    $arPreIMAGE["MODULE_ID"] 	= "iblock";
    
    $arLoadProductArray = Array(
		"IBLOCK_SECTION"  => false,
		"IBLOCK_ID"       => 4,
		"PROPERTY_VALUES" => $arProp,
		"NAME"            => ($_REQUEST['name']." (этап ".($intKey+1).")"),
		"ACTIVE"          => "Y",
		"PREVIEW_TEXT"    => $_REQUEST['stage_description'][$intNumer],
    );
    
    if (strlen($arPreIMAGE["name"]) > 0){
		$intPreIMAGE = CFile::SaveFile($arPreIMAGE, "iblock");
		$arLoadProductArray["PREVIEW_PICTURE"] = CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"].CFile::GetPath($intPreIMAGE));
	}

	$elStep   = new CIBlockElement;
	$strIntId = $elStep->Add($arLoadProductArray);
	$arDishStepsId[] = $strIntId;
}


$arPreIMAGE 				= $_FILES['general_photo'];
$arPreIMAGE["old_file"] 	= "";
$arPreIMAGE["del"] 			= "N";
$arPreIMAGE["MODULE_ID"] 	= "iblock";

$arProp = Array("kitchen" => $_REQUEST['cooking'], "dish_type" => $_REQUEST['dish_type'], "recipt_steps" => $arDishStepsId);

$arLoadProductArray = Array(
	"IBLOCK_SECTION"  => false,
	"IBLOCK_ID"       => 5,
	"ACTIVE_FROM"     => date("d.m.Y h:m:s"),
	"PROPERTY_VALUES" => $arProp,
	"NAME"            => $_REQUEST['name'],
	"ACTIVE"          => "Y",
	"PREVIEW_TEXT"    => $_REQUEST['dish_description'],
);
if (strlen($arPreIMAGE["name"]) > 0){
	$intPreIMAGE = CFile::SaveFile($arPreIMAGE, "iblock");
	$arLoadProductArray["PREVIEW_PICTURE"] = CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"].CFile::GetPath($intPreIMAGE));
}
	
$elStep   = new CIBlockElement;
$strIntId = $elStep->Add($arLoadProductArray);

foreach($arDishStepsId as $intStepId){
	CIBlockElement::SetPropertyValueCode($intStepId, "parent", $strIntId);
}
LocalRedirect("/admin/recipe/");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog.php");
?>
<?} else {
	LocalRedirect("/auth/?backurl=/admin/recipe/add/");
}
?>