<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
if(CModule::IncludeModule("iblock")){
	$arResult["facts"] = array();
	$rsRandomDoUKnow = CIBlockElement::GetList(array("rand"=>"asc"),array("IBLOCK_CODE"=>"do_u_know"),false,false,array("ID","PREVIEW_TEXT"));
	while($arRandomDoUKnow = $rsRandomDoUKnow->GetNext()){
		$arResult["facts"][] = array("id"=>$arRandomDoUKnow["ID"],"text"=>$arRandomDoUKnow["PREVIEW_TEXT"]);
	}
	echo json_encode($arResult);
}?>
