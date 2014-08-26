<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

/***************************************
*									   *
*	Reindex Foodclub Recipes manually  *
*	for search folder				   *
*	(c)	Twinpx by Vladimir Egorov 	   *
*									   *
***************************************/

CModule::IncludeModule("iblock");
CModule::IncludeModule("search");

//Getting recipes iblock

/*$rsRecipeIblock = CIBlock::GetByID(5);
if($arRecipeIblock = $rsRecipeIblock->GetNext()){

	//Getting encyclopedia recipes
	$rsRecipes = CIBlockElement::GetList(array(),array("IBLOCK_ID"=>$arRecipeIblock["ID"],"ACTIVE"=>"Y","PROPERTY_lib"=>"Y"),false,false,array("ID","NAME","TIMESTAMP_X","DETAIL_TEXT","TAGS"));
	while($arRecipe = $rsRecipes->GetNext()){
		$arr = Array(
			"DATE_CHANGE"=>$arRecipe["TIMESTAMP_X"],
			"TITLE"=>$arRecipe["NAME"],
			"SITE_ID"=>SITE_ID,
			"PARAM1"=>$arRecipeIblock["IBLOCK_TYPE_ID"],
			"PARAM2"=>$arRecipeIblock["ID"],
			"PERMISSIONS"=>array(1,2,3),
			"URL"=>"/detail/".$arRecipe["ID"]."/",
			"BODY"=>$arRecipe["DETAIL_TEXT"],
			"TAGS"=>$arRecipe["TAGS"]
		);
		echo "<pre>";print_r($arr);echo "</pre>";	
		CSearch::Index(
			"iblock",
			$arRecipe["ID"],
			$arr,
			$bOverWrite
		);		
	}
}*/


//Getting kitchens iblock
/*$rsKitchenIblock = CIBlock::GetByID(2);
if($arKitchenIblock = $rsKitchenIblock->GetNext()){

	//Getting kitchens
	$rsKitchens = CIBlockElement::GetList(array(),array("IBLOCK_ID"=>$arKitchenIblock["ID"],"ACTIVE"=>"Y"),false,false,array("ID","NAME","TIMESTAMP_X","DETAIL_TEXT","TAGS"));
	while($arKitchen = $rsKitchens->GetNext()){			
		$arr = Array(
			"DATE_CHANGE"=>$arKitchen["TIMESTAMP_X"],
			"TITLE"=>$arKitchen["NAME"],
			"SITE_ID"=>SITE_ID,
			"PARAM1"=>$arKitchenIblock["IBLOCK_TYPE_ID"],
			"PARAM2"=>$arKitchenIblock["ID"],
			"PERMISSIONS"=>array(1,2,3),
			"URL"=>"",
			"BODY"=>$arKitchen["DETAIL_TEXT"],
			"TAGS"=>$arKitchen["TAGS"]
		);
		echo "<pre>";print_r($arr);echo "</pre>";
		CSearch::Index(
			"iblock",
			$arKitchen["ID"],
			$arr,
			$bOverWrite
		);		
	}
}*/

//Getting dish_type iblock
/*$rsDishIblock = CIBlock::GetByID(1);
if($arDishIblock = $rsDishIblock->GetNext()){

	//Getting dish_type
	$rsDishes = CIBlockElement::GetList(array(),array("IBLOCK_ID"=>$arDishIblock["ID"],"ACTIVE"=>"Y"),false,false,array("ID","NAME","TIMESTAMP_X","DETAIL_TEXT","TAGS"));
	while($arDish = $rsDishes->GetNext()){			
		$arr = Array(
			"DATE_CHANGE"=>$arDish["TIMESTAMP_X"],
			"TITLE"=>$arDish["NAME"],
			"SITE_ID"=>SITE_ID,
			"PARAM1"=>$arDishIblock["IBLOCK_TYPE_ID"],
			"PARAM2"=>$arDishIblock["ID"],
			"PERMISSIONS"=>array(1,2,3),
			"URL"=>"",
			"BODY"=>$arDish["DETAIL_TEXT"],
			"TAGS"=>$arDish["TAGS"]
		);		
		echo "<pre>";print_r($arr);echo "</pre>";
		CSearch::Index(
			"iblock",
			$arDish["ID"],
			$arr,
			$bOverWrite
		);		
	}
}*/

//Getting ingredients iblock
/*$rsIngredientsIblock = CIBlock::GetByID(3);
if($arIngredientIblock = $rsIngredientsIblock->GetNext()){

	//Getting ingredients
	$rsIngredients = CIBlockElement::GetList(array(),array("IBLOCK_ID"=>$arIngredientIblock["ID"],"ACTIVE"=>"Y"),false,false,array("ID","NAME","TIMESTAMP_X","DETAIL_TEXT","TAGS"));
	while($arIngredient = $rsIngredients->GetNext()){			
		$arr = Array(
			"DATE_CHANGE"=>$arIngredient["TIMESTAMP_X"],
			"TITLE"=>$arIngredient["NAME"],
			"SITE_ID"=>SITE_ID,
			"PARAM1"=>$arIngredientIblock["IBLOCK_TYPE_ID"],
			"PARAM2"=>$arIngredientIblock["ID"],
			"PERMISSIONS"=>array(1,2,3),
			"URL"=>"",
			"BODY"=>$arIngredient["DETAIL_TEXT"],
			"TAGS"=>$arIngredient["TAGS"]
		);
		echo "<pre>";print_r($arr);echo "</pre>";
		CSearch::Index(
			"iblock",
			$arIngredient["ID"],
			$arr,
			$bOverWrite
		);		
	}
}*/

//Getting stages iblock
$rsRecipeStagesIblock = CIBlock::GetByID(4);
if($arRecipeStagesIblock = $rsRecipeStagesIblock->GetNext()){

	//Getting stages
	$rsRecipeStages = CIBlockElement::GetList(array(),array("IBLOCK_ID"=>$arRecipeStagesIblock["ID"],"ACTIVE"=>"Y"),false,false,array("ID","NAME","TIMESTAMP_X","DETAIL_TEXT","TAGS"));
	while($arRecipeStage = $rsRecipeStages->GetNext()){			
		$arr = Array(
			"DATE_CHANGE"=>$arRecipeStage["TIMESTAMP_X"],
			"TITLE"=>$arRecipeStage["NAME"],
			"SITE_ID"=>SITE_ID,
			"PARAM1"=>$arRecipeStagesIblock["IBLOCK_TYPE_ID"],
			"PARAM2"=>$arRecipeStagesIblock["ID"],
			"PERMISSIONS"=>array(1,2,3),
			"URL"=>"",
			"BODY"=>$arRecipeStage["DETAIL_TEXT"],
			"TAGS"=>$arRecipeStage["TAGS"]
		);
		echo "<pre>";print_r($arr);echo "</pre>";		
		CSearch::Index(
			"iblock",
			$arRecipeStage["ID"],
			$arr,
			$bOverWrite
		);		
	}
}

?>