<?if(intval($_REQUEST["itemId"]) > 0){
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
	global $USER,$DB;
	if ($USER->IsAuthorized()) {		
		$APPLICATION->RestartBuffer();
		CModule::IncludeModule("iblock");
		$element_id = intval($_REQUEST["itemId"]);
		$rsFoodShot = CIBlockElement::GetList(array(),array("IBLOCK_CODE"=>"foodshot_elements","ID"=>$element_id,"ACTIVE" => "Y"),false,false,array("ID","NAME","CREATED_BY","DETAIL_PICTURE","DETAIL_TEXT","PROPERTY_comments_count","PROPERTY_likes_count","PROPERTY_www"));
		if($arFoodShot = $rsFoodShot->GetNext()){
			if($USER->IsAdmin() || $USER->GetID() == $arFoodShot["CREATED_BY"]){
				//echo "<pre>";print_r($arComment);echo "</pre>";die;
				$DB->StartTransaction();
				
				if(!CIBlockElement::Delete($arFoodShot["ID"])){
					$strWarning = 'Error!';
					$DB->Rollback();
				}else{
					$DB->Commit();
					echo "SUCCESS!";
				}
			}
		}
	}
}
?>