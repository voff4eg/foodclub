<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>

<?if(!empty($arResult["ITEMS"])){

	CModule::IncludeModule("iblock");
	
	global $USER;
	if($USER->IsAuthorized()){
		$rsUser = $USER->GetByID($USER->GetID());
		$arUser = $rsUser->Fetch();
		$arResult["user"]["ID"] = $arUser["ID"];
		$arResult["user"]["NAME"] = $arUser["NAME"];
		$arResult["user"]["LAST_NAME"] = $arUser["LAST_NAME"];
		if(intval($arUser["PERSONAL_PHOTO"]) > 0){
			//$is_image = CFile::IsImage($arUser["PERSONAL_PHOTO"]);
			//if($is_image === true){
				$arUser["PERSONAL_PHOTO"] = CFile::GetFileArray($arUser["PERSONAL_PHOTO"]);
				$arResult["user"]["PERSONAL_PHOTO"] = CFile::ResizeImageGet($arUser["PERSONAL_PHOTO"], array('width'=>"30",'height'=>30), BX_RESIZE_IMAGE_PROPORTIONAL, true);
			//}
		}else{
			$arResult["user"]["PERSONAL_PHOTO"]["src"] = "/images/avatar/avatar_small.jpg";
		}
	}
	
	$arResult["users"] = array();

	foreach($arResult["ITEMS"] as $index => $arItem){
		if(intval($arItem["FIELDS"]["CREATED_BY"]) > 0){
			$rsUser = $USER->GetByID($arItem["FIELDS"]["CREATED_BY"]);
			$arUser = $rsUser->Fetch();
			if(strlen($arUser["NAME"]) > 0 && strlen($arUser["LAST_NAME"]) > 0){
				$arResult["ITEMS"][$index]["user_name"] = $arUser["NAME"]." ".$arUser["LAST_NAME"];
			}else{
				$arResult["ITEMS"][$index]["user_name"] = $arUser["LOGIN"];
			}
		}

		$arFilter = array(
			"IBLOCK_CODE" => "foodshot_comments",
			"ACTIVE" => "Y",
			"PROPERTY_element" => $arItem["ID"],
		);
		$dbList = CIBlockElement::GetList(array("created"=>"desc"), $arFilter, false, false, array("ID", "CREATED_BY", "PREVIEW_TEXT", "PROPERTY_element"));
		while($arComment = $dbList->GetNext()){
			if(intval($arComment["CREATED_BY"] > 0)){
				if(array_key_exists($arComment["CREATED_BY"], $arResult["users"]) === false){

					$rsUser = $USER->GetByID($arComment["CREATED_BY"]);
					$arUser = $rsUser->Fetch();
					$arResult["users"][$arComment["CREATED_BY"]]["name"] = $arUser["NAME"]." ".$arUser["LAST_NAME"];

					if(intval($arUser["PERSONAL_PHOTO"]) >0){
						//$is_image = CFile::IsImage($arUser["PERSONAL_PHOTO"]);
						//if($is_image === true){
							$arUser["PERSONAL_PHOTO"] = CFile::GetFileArray($arUser["PERSONAL_PHOTO"]);
							$arResult["users"][$arComment["CREATED_BY"]]["PERSONAL_PHOTO"] = CFile::ResizeImageGet($arUser["PERSONAL_PHOTO"], array('width'=>"30",'height'=>30), BX_RESIZE_IMAGE_PROPORTIONAL, true);
						//}
					}else{
						$arResult["users"][$arComment["CREATED_BY"]]["PERSONAL_PHOTO"]["src"] = "/images/avatar/avatar_small.jpg";
					}
				}
			}
			$arResult["ITEMS"][$index]["user_login"] = $arUser["LOGIN"];
			$arResult["ITEMS"][$index]["comments"][] = $arComment;
		}

		$arResult["ITEMS"][$index]["comments"] = array_reverse($arResult["ITEMS"][$index]["comments"]);

		$arFilter["IBLOCK_CODE"] = "foodshot_likes";
		$arFilter["PROPERTY_like"] = 1;
		$dbList = CIBlockElement::GetList(array(), $arFilter, false, false, array("CREATED_BY"));
		while($arLike = $dbList->GetNext()){
			$arResult["ITEMS"][$index]["likes"][] = $arLike["CREATED_BY"];
		}
	}
}
?>