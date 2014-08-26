<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
	CModule::IncludeModule("iblock");
	chdir ('/srv/www/foodclub/public_html/');
	$file = fopen('yandex_recipes.xml', 'w');
	$arRecipes = array();$arRecipeIds = array();
	$ReciptSteps = array();
	$Kitchens = array();$arKitchens = array();
	$DishTypes = array();$arDishTypes = array();
	$Authors = array();$arAuthors = array();
	$Ingredients[] = array();$arIngredients = array();
	$arIngredientValues = array();$arReciptStepIDs = array();$arReciptSteps = array();
	$str_xml = '<?xml version="1.0" encoding="utf-8"?>';
	$str_xml .= '<entities>';
	$rsRecipes = CIBlockElement::GetList(array("NAME"=>"ASC"),array("ACTIVE"=>"Y","IBLOCK_ID"=>5,"PROPERTY_lib"=>"Y","ID"=>array("16666","36695")),
	false,
	false,
	array("ID","NAME","CREATED_BY","DETAIL_PICTURE","PROPERTY_kitchen","PROPERTY_dish_type","PROPERTY_recipt_steps","PROPERTY_kcals","PROPERTY_portion","PROPERTY_cooking_time"));
	while($arRecipe = $rsRecipes->GetNext()){
		$arRecipes[ $arRecipe["ID"] ] = $arRecipe;
		$ReciptSteps[] = $arRecipe["PROPERTY_RECIPT_STEPS_VALUE"];
		if(intval($arRecipe["PROPERTY_KITCHEN_VALUE"]) > 0 && !in_array($arRecipe["PROPERTY_KITCHEN_VALUE"],$Kitchens)){
			$Kitchens[] = $arRecipe["PROPERTY_KITCHEN_VALUE"];
		}
		if(intval($arRecipe["PROPERTY_DISH_TYPE_VALUE"]) > 0 && !in_array($arRecipe["PROPERTY_DISH_TYPE_VALUE"],$DishTypes)){
			$DishTypes[] = $arRecipe["PROPERTY_DISH_TYPE_VALUE"];
		}
		if(intval($arRecipe["CREATED_BY"]) > 0){
			$Authors[] = $arRecipe["CREATED_BY"];
		}
	}
	if(!empty($Kitchens)){
		$Kitchens = array_unique($Kitchens);
		$rsKitchens = CIBlockElement::GetList(array("NAME"=>"ASC"),array("IBLOCK_ID"=>2,"ID"=>$Kitchens),false,false,array("ID","NAME"));
		while($arKitchen = $rsKitchens->GetNext()){
			$arKitchens[ $arKitchen["ID"] ] = $arKitchen;
		}
	}
	if(!empty($DishTypes)){
		$DishTypes = array_unique($DishTypes);
		$rsDishTypes = CIBlockElement::GetList(array("NAME"=>"ASC"),array("IBLOCK_ID"=>1,"ID"=>$DishTypes),false,false,array("ID","NAME"));
		while($arDishType = $rsDishTypes->GetNext()){
			$arDishTypes[ $arDishType["ID"] ] = $arDishType;
		}
	}
	if(!empty($Authors)){
		$rsAuthors = CUser::GetList(($by="personal_country"), ($order="desc"), array("ID"=>$Authors));
		while($arAuthor = $rsAuthors->GetNext()){
			$arAuthors[ $arAuthor["ID"] ] = $arAuthor;
		}
	}
	//echo "ReciptSteps<pre>"; print_r($ReciptSteps); echo "</pre>";
	if(!empty($ReciptSteps)){
		//$ReciptSteps = array_unique($ReciptSteps);
		$rsReciptSteps = CIBlockElement::GetList(array("NAME"=>"ASC"),array("IBLOCK_ID"=>4,"ID"=>$ReciptSteps),false,false,array("ID","NAME","PREVIEW_TEXT","PREVIEW_PICTURE","PROPERTY_ingredient","PROPERTY_numer","PROPERTY_parent"));
		while($arReciptStep = $rsReciptSteps->GetNext()){
			//echo "<pre>"; print_r($arReciptStep); echo "</pre>";
			/*if(intval($arReciptStep["PROPERTY_INGREDIENT_VALUE"]) > 0 && !in_array($arReciptStep["PROPERTY_INGREDIENT_VALUE"],$IngredientIDs)){
				$IngredientIDs[] = $arReciptStep["PROPERTY_INGREDIENT_VALUE"];
				$Ingredients[ $arReciptStep["ID"] ][] = $arReciptStep["PROPERTY_INGREDIENT_VALUE"];
			}
			$arIngredientValues[ $arReciptStep["ID"] ][ $arReciptStep["PROPERTY_INGREDIENT_VALUE_ID"] ][ $arReciptStep["PROPERTY_NUMER_VALUE_ID"] ] = $arReciptStep["PROPERTY_NUMER_VALUE"];*/
			//if(!isset($arIngredientValues[ $arReciptStep["ID"] ][ $arReciptStep["PROPERTY_INGREDIENT_VALUE_ID"] ])){
				//$arIngredientValues[ $arReciptStep["ID"] ][ $arReciptStep["PROPERTY_INGREDIENT_VALUE_ID"] ] = $arReciptStep["PROPERTY_NUMER_VALUE"];
			//}
			if(intval($arReciptStep["PROPERTY_PARENT_VALUE"]) > 0 && !in_array($arReciptStep["ID"],$arReciptStepIDs)){
				$arReciptStepIDs[] = $arReciptStep["ID"];
				$arReciptSteps[ $arReciptStep["PROPERTY_PARENT_VALUE"] ][] = $arReciptStep;
			}
		}
		/*if(!empty($IngredientIDs)){
			//$Ingredients = array_unique($Ingredients);
			$rsIngredients = CIBlockElement::GetList(array("NAME"=>"ASC"),array("IBLOCK_ID"=>3,"ID"=>$IngredientIDs),false,false,array("ID","NAME","PROPERTY_unit"));
			while($arIngredient = $rsIngredients->GetNext()){
				$arIngredients[ $arIngredient["ID"] ] = $arIngredient;
			}
		}*/
	}
	global $DB;
	$arIngrIDByRec = array();
	$arIngrAmountByRec = array();
	$ingredients = array();
	foreach($arReciptStepIDs as $step){
		$ElementId = $step;
		$sqlIngridient = "SELECT * FROM `b_iblock_element_property` WHERE `IBLOCK_ELEMENT_ID` = ".$ElementId." AND `IBLOCK_PROPERTY_ID` = 3 ORDER BY `ID` ASC";
		$sqlNumer = "SELECT * FROM `b_iblock_element_property` WHERE `IBLOCK_ELEMENT_ID` = ".$ElementId." AND `IBLOCK_PROPERTY_ID` = 4 ORDER BY `ID` ASC";
		$sqlRecipeID = "SELECT `VALUE` FROM `b_iblock_element_property` WHERE `IBLOCK_ELEMENT_ID` = ".$ElementId." AND `IBLOCK_PROPERTY_ID` = 8 ORDER BY `ID` ASC";
		$rsRecipeID = $DB->Query($sqlRecipeID, false);
		if($arRec = $rsRecipeID->Fetch()){
			$RecipeID = $arRec["VALUE"];
		}
		$rowFields = $DB->Query($sqlIngridient, false);
		while($Field = $rowFields->Fetch()){			
			$arIngrs[ $ElementId ][] = $Field["VALUE"];
			$arIngrIDByRec[ $RecipeID ][ $Field["IBLOCK_ELEMENT_ID"] ][] = $Field["VALUE"];
			//$strIngrs .= $Field['VALUE'].",";
			$Ingrs[] = $Field['VALUE'];
		}
		/*$strIngrs = substr($strIngrs,0,-1);
		$sqlValue = "SELECT * FROM `b_iblock_element_property` WHERE `IBLOCK_ELEMENT_ID` IN (".$strIngrs.") AND `IBLOCK_PROPERTY_ID` = 2 ORDER BY `ID` ASC";
		$rowFields = $DB->Query($sqlValue, false);
		while($Field = $rowFields->Fetch()){
			$arValues[ $Field['ID'] ] = $Field['VALUE'];
		}*/
		$rowFields = $DB->Query($sqlNumer, false);
		while($Field = $rowFields->Fetch()){
			$arIngrAmountByRec[ $RecipeID ][ $Field["IBLOCK_ELEMENT_ID"] ][] = $Field["VALUE"];
			//$arIngrAmountByRec[ $RecipeID ][ $Field["IBLOCK_ELEMENT_ID"] ][] = $Field["VALUE"];
			//$arStages[ $Item['ID'] ]['PROPERTY_NUMER_VALUE'][ $Field['ID'] ] = $Field['VALUE'];
			$arNumer[ $ElementId ][] = $Field['VALUE'];
		}
		//Магия с ингридиентами
		foreach($arIngrIDByRec[ $RecipeID ][ $ElementId ] as $key => $val){
			if(isset($ingredients[ $val ])){
				//echo "!!".floatval(str_replace(Array("1/2", "1/4", "3/4"), Array("&frac12;","&frac14;","&frac34;"),$arIngrAmountByRec[ $RecipeID ][ $ElementId ][ $key ]))."!!";
				ob_start(); eval("echo ".$arIngrAmountByRec[ $RecipeID ][ $ElementId ][ $key ].";"); $i = ob_get_contents(); ob_end_clean();
				$ingredients[ $val ] += FloatVal($i);
				//$ingredients[ $val ] += floatval($arIngrAmountByRec[ $RecipeID ][ $ElementId ][ $key ]);
			}else{
				$ingredients[ $val ] = 0;
				//echo "!!".floatval(str_replace(Array("1/2", "1/4", "3/4"), Array("&frac12;","&frac14;","&frac34;"),$arIngrAmountByRec[ $RecipeID ][ $ElementId ][ $key ]))."!!";
				ob_start(); eval("echo ".$arIngrAmountByRec[ $RecipeID ][ $ElementId ][ $key ].";"); $i = ob_get_contents(); ob_end_clean();
				$ingredients[ $val ] += FloatVal($i);
				//$ingredients[ $val ] = $arIngrAmountByRec[ $RecipeID ][ $ElementId ][ $key ];
			}			
		}		
	}
	/*foreach($ingredients as $key => $ing){
		echo $key." - ".$ing."<br>";
	}*/
	//echo "<pre>";print_r($arIngrIDByRec);echo "</pre>";die;
	//die;
	$rsIngrs = CIBlockElement::GetList(array(),array("IBLOCK_ID"=>3,"ID"=>$Ingrs),false,false,array("ID","NAME","PROPERTY_unit"));
	while($arIngr = $rsIngrs->GetNext()){		
		$arIngredients[ $arIngr["ID"] ] = $arIngr;
	}
	foreach($arRecipes as $recipe){
		$str_xml .= "<recipe>";
		$str_xml .= "<name>".htmlspecialchars(strip_tags($recipe["NAME"]),ENT_QUOTES)."</name>";
		$str_xml .= "<url>http://www.foodclub.ru/detail/".$recipe["ID"]."/</url>";
		$str_xml .= "<type>".htmlspecialchars(strip_tags($arDishTypes[ $recipe["PROPERTY_DISH_TYPE_VALUE"] ]["NAME"]),ENT_QUOTES)."</type>";
		$str_xml .= "<cuisine-type>".htmlspecialchars(strip_tags($arKitchens[ $recipe["PROPERTY_KITCHEN_VALUE"] ]["NAME"]),ENT_QUOTES)."</cuisine-type>";
		$str_xml .= "<author>".htmlspecialchars(strip_tags($arAuthors[ $recipe["CREATED_BY"] ]["LOGIN"]),ENT_QUOTES)."</author>";
		if(intval($recipe["PROPERTY_KCALS_VALUE"]) > 0){
			$str_xml .= "<calorie>".$recipe["PROPERTY_KCALS_VALUE"]." ккал</calorie>";
		}		
		foreach($ingredients as $key => $ing){	
			$str_xml .= "<ingredient><name>".htmlspecialchars(strip_tags($arIngredients[ $key ]["NAME"]),ENT_QUOTES)."</name><type>".$arIngredients[ $key ]["PROPERTY_UNIT_VALUE"]."</type><value>".$ingredients[ $key ]."</value></ingredient>";
		}
		foreach($arReciptSteps[ $recipe["ID"] ] as $step){
			if(strlen($step["PREVIEW_TEXT"]) > 0){
				$str_xml .= "<instruction>".htmlspecialchars(strip_tags($step["PREVIEW_TEXT"]),ENT_QUOTES)."</instruction>";
			}
		}
		foreach($arReciptSteps[ $recipe["ID"] ] as $step){
			if(intval($step["PREVIEW_PICTURE"]) > 0){
				$str_xml .= "<photo>http://www.foodclub.ru".CFile::GetPath($step["PREVIEW_PICTURE"])."</photo>";
			}
			/*foreach($arIngrs[ $step["ID"] ] as $key => $ingr){
				$str_xml .= "<ingredient><name>".htmlspecialchars(strip_tags($arIngredients[ $ingr ]["NAME"]),ENT_QUOTES)."</name><type>".$arIngredients[ $ingr ]["PROPERTY_UNIT_VALUE"]."</type><value>".$arNumer[ $step["ID"] ][ $key ]."</value></ingredient>";
			}*/
		}
		$str_xml .= "<final-photo>http://www.foodclub.ru".CFile::GetPath($recipe["DETAIL_PICTURE"])."</final-photo>";
		if(strlen($recipe["PROPERTY_PORTION_VALUE"]) > 0){
			$str_xml .= "<yield>".$recipe["PROPERTY_PORTION_VALUE"]."</yield>";
		}
		if(intval($recipe["PROPERTY_COOKING_TIME_VALUE"]) > 0){
			$cooking_time_hours = $recipe["PROPERTY_COOKING_TIME_VALUE"]/60;
			$cooking_time_minutes = $recipe["PROPERTY_COOKING_TIME_VALUE"]%60;
			$str_xml .= "<duration>";
			if(intval($cooking_time_hours) > 0){
				$str_xml .= intval($cooking_time_hours)." ч ";
			}
			if(intval($cooking_time_minutes) > 0){
				$str_xml .= intval($cooking_time_minutes)." мин";
			}
			$str_xml .= "</duration>";
		}		
		$str_xml .= "</recipe>";
	}
	//$str_xml .= '<xi:include href="known-features.xml"/>';
	$str_xml .= '</entities>';
	//fwrite($file,$str_xml);
	$APPLICATION->RestartBuffer();
	header("Content-Type: text/xml; charset=".LANG_CHARSET);
	header("Pragma: no-cache");
	echo $str_xml;
?>
