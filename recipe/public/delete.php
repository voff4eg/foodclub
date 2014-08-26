<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("iblock");
if( $USER->IsAdmin() || $USER->IsAuthorized() ){
	if(IntVal($_REQUEST['r']) > 0){
		$rsElement = CIBlockElement::GetList(array(), array("ID" => $_REQUEST['r']), false, false);
		$obElement = $rsElement->GetNextElement();
		$Fields = $obElement->GetFields();
		$Prop   = $obElement->GetProperties();
		
		if($USER->GetID() == $Fields['CREATED_BY'] || $USER->IsAdmin()){

			if(!($USER->IsAdmin()) && (MakeTimeStamp($Fields["DATE_CREATE"]) <= (time() - 3600*24*3))){
				LocalRedirect("/detail/".$Fields["ID"]."/?cant_delete");
			}
			
			foreach($Prop['recipt_steps']['VALUE'] as $Item){
				CIBlockElement::Delete($Item);
			}
			//---Рейтинг
			require_once($_SERVER['DOCUMENT_ROOT']."/classes/mark.class.php");
			$CMark = new CMark;
			$CMark->initIblock($Fields['ID']);
			global $DB;
			$rsFav = $DB->Query("SELECT * FROM `b_recipe_favorite` WHERE `recipe` = ".$Fields['ID'], false);
			while($arFav = $rsFav->GetNext()){
				if($arFav['user'] != $Fields['CREATED_BY']){
					$CMark->updateUserRait($Fields['CREATED_BY'],$way = "low","r_favorite");
				}
			}
			$CMark->updateUserRait($Fields['CREATED_BY'],$way = "low","r_create");
			//----
			$rsComments = CIBlockElement::GetList(array(),array("IBLOCK_ID"=>6,"PROPERTY_recipe"=>$_REQUEST['r']),false,false,array("ID","CREATED_BY"));
			while($arComments = $rsComments->GetNext()){
				if($Fields['CREATED_BY'] != $arComments["CREATED_BY"]){
					$CMark->updateUserRait($Fields['CREATED_BY'],$way = "low","r_comment_recipe");
					$CMark->updateUserRait($arComments["CREATED_BY"],$way = "low","r_comment_recipe");
				}else{
					$CMark->updateUserRait($arComments["CREATED_BY"],$way = "low","r_comment_recipe");
				}
			}
			CIBlockElement::Delete($Fields['ID']);
			LocalRedirect("/profile/recipes/");
		} else {
			LocalRedirect("/all/");
		}
	}
	
} else {
	LocalRedirect("/auth/?backurl=/recipe/add/");
}
?>