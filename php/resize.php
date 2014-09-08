<?
$_SERVER["DOCUMENT_ROOT"] = "/home/webserver/www";
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
if(CModule::IncludeModule("iblock")){
	//check files
	/*$rsStages = CIBlockElement::GetList(
		array("timestamp_x" => "asc")
		, array(
			"IBLOCK_ID" => 4
			, "!PREVIEW_PICTURE" => false
			, "DETAIL_PICTURE" => false
		)
		, false
		, array("nTopCount" => 2000)
		, array("ID" ,"PREVIEW_PICTURE", "DETAIL_PICTURE")
	);
	while($arStage = $rsStages->GetNext()){
		echo "<pre>";print_r($arStage);echo "</pre>";
	}*/
	//copy preview to detail
	$rsStages = CIBlockElement::GetList(
		array()
		, array(
			"IBLOCK_ID" => 4
			//, "PROPERTY_parent" => 64119
			, "!PREVIEW_PICTURE" => false
			, "DETAIL_PICTURE" => false
		)
		, false
		, array("nTopCount" => 2000)
		//, false
		//array("ID","PREVIEW_PICTURE","DETAIL_PICTURE")
	);
	while($obStage = $rsStages->GetNextElement()){
		$PROPS = array();
		$arStage = $obStage->GetFields();
		$arStage["PROPERTIES"] = $obStage->GetProperties();
		foreach($arStage["PROPERTIES"] as $key => $property){
			$PROPS[ $key ] = $property["VALUE"];
		}
		$NewFile = CFile::CopyFile($arStage["PREVIEW_PICTURE"]);
		$arFile = CFile::MakeFileArray($NewFile);
		if(!empty($arFile)){
			$el = new CIBlockElement;
			$res = $el->Update($arStage["ID"], array("DETAIL_PICTURE" => $arFile, "PROPERTY_VALUES" => $PROPS),false,false,true);
		}
	}

	//copy make small preview from big detail
	/*$rsStages = CIBlockElement::GetList(
		array(),
		array(
			"IBLOCK_ID" => 4
			//, "PROPERTY_parent" => 64119
			, "!PREVIEW_PICTURE" => false
			, "!DETAIL_PICTURE" => false
		), 
		false,
		array("nPageSize" => 2000,"iNumPage" => 8)
		//false
		//,array("ID","PREVIEW_PICTURE","DETAIL_PICTURE")
	);
	while($obStage = $rsStages->GetNextElement()){
		$PROPS = array();
		$arStage = $obStage->GetFields();
		$arStage["PROPERTIES"] = $obStage->GetProperties();
		foreach($arStage["PROPERTIES"] as $key => $property){
			$PROPS[ $key ] = $property["VALUE"];
		}
		$arFile = CFile::MakeFileArray($arStage["PREVIEW_PICTURE"]);
		echo "<pre>";print_r($arFile);echo "</pre>";
		$el = new CIBlockElement;
		$res = $el->Update($arStage["ID"], array("PREVIEW_PICTURE" => $arFile, "PROPERTY_VALUES" => $PROPS),false,false,true);
	}*/
}
?>