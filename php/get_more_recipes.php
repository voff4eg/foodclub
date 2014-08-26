<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
if(strlen($_REQUEST["id"])){
	CModule::IncludeModule("iblock");
	$Ids = explode(",",$_REQUEST["id"]);
	if(!empty($Ids)){
		//$str = '\'{"recipes":[';
		$str["recipes"] = array();
		$rsRecipes = CIBlockElement::GetList(array("ACTIVE_FROM"=>"DESC","DATE_CREATE"=>"DESC"),array("ID"=>$Ids,"ACTIVE"=>"Y","PROPERTY_lib"=>"Y"),false,false,array("NAME","ID", "CODE","PREVIEW_PICTURE","DETAIL_PICTURE","CREATED_BY","PROPERTY_comment_count"));
		while($arRecipe = $rsRecipes->GetNext()){
			if(intval($arRecipe["PREVIEW_PICTURE"])){
				$arFile = CFile::GetFileArray($arRecipe["PREVIEW_PICTURE"]);
				$src = "http://www.foodclub.ru".$arFile["SRC"];
			}else{
				$src = "";
			}
			$rsUser = CUser::GetByID($arRecipe['CREATED_BY']);
			$arUser = $rsUser->Fetch();
			if(strlen($arUser["NAME"]) > 0 && strlen($arUser["LAST_NAME"]) > 0){
		     	$name = $arUser["NAME"]." ".$arUser["LAST_NAME"];
		 	}else{
		 		$name = $arUser["LOGIN"];
		 	}
			$str["recipes"][] = array("name"=>$arRecipe["NAME"],"href"=>"/detail/".($arRecipe["CODE"] ? $arRecipe["CODE"] : $arRecipe["ID"])."/","src"=>"http://www.foodclub.ru".$arFile["SRC"], "author"=>$name, "comments"=>intval($arRecipe["PROPERTY_COMMENT_COUNT_VALUE"]));
		}
		//$str = substr($str, 0, -1);
		//$str .= ']}\'';
	}
}
echo json_encode($str);?>