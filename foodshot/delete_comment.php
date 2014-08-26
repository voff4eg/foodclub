<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?
global $USER,$DB;
if ($USER->IsAuthorized()) {	
	if(intval($_REQUEST["itemId"]) > 0 && intval($_REQUEST["id"]) > 0){	
		if(CModule::IncludeModule("iblock")){
			$rsComment = CIBlockElement::GetList(array(),array("IBLOCK_CODE"=>"foodshot_comments","ID"=>intval($_REQUEST["id"])),false,false);			
			if($arComment = $rsComment->Fetch()){
				//$arComment = $obComment->GetFields();
				
				if($USER->IsAdmin() || ($USER->GetID() == $arComment["CREATED_BY"])){
					//echo "<pre>";print_r($arComment);echo "</pre>";die;
					$DB->StartTransaction();
					
					if(!CIBlockElement::Delete($arComment["ID"])){
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
}
?>