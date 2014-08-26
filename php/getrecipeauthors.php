<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if(CModule::IncludeModule("iblock")){
	//Рецепты
	$rsRecipes = CIBlockElement::GetList(array(),array("IBLOCK_ID"=>5,"PROPERTY_lib"=>"Y",array(
        "LOGIC" => "OR",
        array("PROPERTY_add_mobile"=>3),
        array("CREATED_BY"=>array(2,7,384,27,56,392,2766,4272,5,26,62,70,340,451,5680)),
    )),false,false);
	$arAuthors = array();
	while($obRecipe = $rsRecipes->GetNextElement()){
		$arRecipe = $obRecipe->GetFields();		
		$arRecipe["PROPERTIES"] = $obRecipe->GetProperties();
		if(!in_array($arRecipe["CREATED_BY"],$arAuthors)){
			$arAuthors[] = $arRecipe["CREATED_BY"];
		}
	}

	if(!empty($arAuthors)){
		$rsAuthors = CUser::GetList(($by="personal_country"), ($order="desc"), array("ID"=>implode(" | ",$arAuthors)));
		while($arAuthor = $rsAuthors->Fetch()){
			if(strlen($arAuthor["LAST_NAME"]) > 0 && strlen($arAuthor["NAME"]) > 0){
				$Authors[] = $arAuthor["LAST_NAME"]." ".$arAuthor["NAME"];
				//$Authors[] = $arAuthor["ID"]." ".$arAuthor["LAST_NAME"]." ".$arAuthor["NAME"];
			}else{
				$Authors[] = $arAuthor["LOGIN"];
				//$Authors[] = $arAuthor["ID"]." ".$arAuthor["LOGIN"];
			}
		}
		echo implode(", ", $Authors);
	}	
}