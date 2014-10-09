<?$_SERVER["DOCUMENT_ROOT"] = "/home/webserver/www";
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
	CModule::IncludeModule("iblock");
	$SERVER_NAME = "www.foodclub.ru";
	//chdir ('/srv/www/foodclub/public_html/');
	chdir ('/srv/www/foodclub/');
	//echo getcwd();
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
	global $DB;
	$arProps = array(
		25 => "calories",
		5 => "kitchen",
		6 => "dish_type",
		26 => "portion",
		27 => "cooking_time",
		22 => "lib",
		7 => "recipt_steps"
	);
	$strPictureSRC = array();
	$encyclopedia = array();
	//RECIPES
	$strSelectSQL = "SELECT e.ID,e.NAME,e.CODE,e.CREATED_BY,e.DETAIL_PICTURE,p.IBLOCK_PROPERTY_ID,p.VALUE FROM b_iblock_element AS e LEFT JOIN b_iblock_element_property AS p ON e.ID = p.IBLOCK_ELEMENT_ID WHERE e.IBLOCK_ID = 5 AND e.ACTIVE = 'Y' AND ((p.IBLOCK_PROPERTY_ID = 22 AND p.VALUE = 'y') OR p.IBLOCK_PROPERTY_ID = 25 OR p.IBLOCK_PROPERTY_ID = 5 OR p.IBLOCK_PROPERTY_ID = 6 OR p.IBLOCK_PROPERTY_ID = 26 OR p.IBLOCK_PROPERTY_ID = 27)";
	$res = $DB->Query($strSelectSQL, false, $err_mess.__LINE__);
	while ($row = $res->Fetch()){
		if($row["IBLOCK_PROPERTY_ID"] == 22 && $row["VALUE"] == 'y'){
			$encyclopedia[] = $row["ID"];
		}		
		$arRecipe = array(
			"ID" => $row["ID"],
			"NAME" => $row["NAME"],
			"CODE" => $row["CODE"],
			"CREATED_BY" => $row["CREATED_BY"],
			"DETAIL_PICTURE" => "http://".$SERVER_NAME.CFile::GetPath($row["DETAIL_PICTURE"]),
		);

		$strPictureSRC[ $row["ID"] ] = $arRecipe["DETAIL_PICTURE"];

		if(!isset($arRecipes[ $row["ID"] ])){
			$arRecipes[ $row["ID"] ] = $arRecipe;
		}
		$arRecipes[ $row["ID"] ]["PROPERTIES"][ $arProps[ $row["IBLOCK_PROPERTY_ID"] ] ] = $row["VALUE"];

		if($row["IBLOCK_PROPERTY_ID"] == 7){
			$ReciptSteps[] = $row["VALUE"];
		}

		if($row["IBLOCK_PROPERTY_ID"] == 5 && intval($row["VALUE"]) > 0 && !in_array($row["VALUE"],$Kitchens)){
			$Kitchens[] = $row["VALUE"];
		}
		if($row["IBLOCK_PROPERTY_ID"] == 6 && intval($row["VALUE"]) > 0 && !in_array($row["VALUE"],$DishTypes)){
			$DishTypes[] = $row["VALUE"];
		}
		if(intval($row["CREATED_BY"]) > 0){
			$Authors[] = $row["CREATED_BY"];
		}
	}

	$rsIngrs = CIBlockElement::GetList(array(),array("IBLOCK_ID"=>3,"ID"=>$Ingrs),false,false,array("ID","NAME","PROPERTY_unit"));
	while($arIngr = $rsIngrs->GetNext()){		
		$arIngredients[ $arIngr["ID"] ] = $arIngr;
	}
	
	//STEPS
	$strSelectSQL = "SELECT e.ID,e.PREVIEW_TEXT,e.PREVIEW_PICTURE FROM b_iblock_element AS e LEFT JOIN b_iblock_element_property AS p ON e.ID = p.IBLOCK_ELEMENT_ID WHERE e.IBLOCK_ID = 4 AND e.ACTIVE = 'Y' AND p.IBLOCK_PROPERTY_ID = 8";
	$res = $DB->Query($strSelectSQL, false, $err_mess.__LINE__);
	while ($row = $res->Fetch()){
		$arReciptSteps[ $row["IBLOCK_PROPERTY_ID"] ] = array(
			"PREVIEW_TEXT" => $row["PREVIEW_TEXT"],
			"PREVIEW_PICTURE" => "http://".$SERVER_NAME.CFile::GetPath($row["PREVIEW_PICTURE"])
		);
		$strPictureSRC[ $row["IBLOCK_PROPERTY_ID"] ] .= $arReciptSteps[ $row["IBLOCK_PROPERTY_ID"] ]["PREVIEW_PICTURE"];
		$ElementId = $row["ID"];
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
			$Ingrs[] = $Field['VALUE'];
		}
		$rowFields = $DB->Query($sqlNumer, false);
		while($Field = $rowFields->Fetch()){
			$arIngrAmountByRec[ $RecipeID ][ $Field["IBLOCK_ELEMENT_ID"] ][] = $Field["VALUE"];
			$arNumer[ $ElementId ][] = $Field['VALUE'];
		}
		//Магия с ингридиентами
		foreach($arIngrIDByRec[ $RecipeID ][ $ElementId ] as $key => $val){
			if(isset($ingredients[ $RecipeID ][ $val ])){				
				ob_start(); eval("echo ".$arIngrAmountByRec[ $RecipeID ][ $ElementId ][ $key ].";"); $i = ob_get_contents(); ob_end_clean();
				$ingredients[ $RecipeID ][ $val ] += FloatVal($i);				
			}else{
				$ingredients[ $RecipeID ][ $val ] = 0;				
				ob_start(); eval("echo ".$arIngrAmountByRec[ $RecipeID ][ $ElementId ][ $key ].";"); $i = ob_get_contents(); ob_end_clean();
				$ingredients[ $RecipeID ][ $val ] += FloatVal($i);				
			}			
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
	$arNoIngrRecipes = array();
	foreach($arRecipes as $recipe){
		if(!empty($ingredients[ $recipe["ID"] ]) && in_array($recipe["ID"],$encyclopedia)){
			if(strpos($strPictureSRC[ $recipe["ID"] ], "]") === false || strpos($strPictureSRC[ $recipe["ID"] ], "[") === false){
				$str_xml .= "<recipe>";
				$str_xml .= "<name>".htmlspecialchars(strip_tags($recipe["NAME"]),ENT_QUOTES)."</name>";
				$str_xml .= "<url>http://".$SERVER_NAME."/detail/".($recipe["CODE"] ? $recipe["CODE"] : $recipe["ID"])."/</url>";
				$str_xml .= "<type>".htmlspecialchars(strip_tags($arDishTypes[ $recipe["PROPERTIES"]["dish_type"] ]["NAME"]),ENT_QUOTES)."</type>";
				$str_xml .= "<cuisine-type>".htmlspecialchars(strip_tags($arKitchens[ $recipe["PROPERTIES"]["kitchen"] ]["NAME"]),ENT_QUOTES)."</cuisine-type>";
				$str_xml .= "<author>".htmlspecialchars(strip_tags($arAuthors[ $recipe["CREATED_BY"] ]["LOGIN"]),ENT_QUOTES)."</author>";
				if(intval($recipe["PROPERTIES"]["calories"]) > 0){
					$str_xml .= "<calorie>".$recipe["PROPERTIES"]["calories"]." ккал</calorie>";
				}		
				foreach($ingredients[ $recipe["ID"] ] as $key => $ing){
					$str_xml .= "<ingredient><name>".htmlspecialchars(strip_tags($arIngredients[ $key ]["NAME"]),ENT_QUOTES)."</name><type>".$arIngredients[ $key ]["PROPERTY_UNIT_VALUE"]."</type><value>".$ingredients[ $recipe["ID"] ][ $key ]."</value></ingredient>";
				}
				foreach($arReciptSteps[ $recipe["ID"] ] as $step){
					if(strlen($step["PREVIEW_TEXT"]) > 0){
						$str_xml .= "<instruction>".htmlspecialchars(strip_tags($step["PREVIEW_TEXT"]),ENT_QUOTES)."</instruction>";
					}
				}
				foreach($arReciptSteps[ $recipe["ID"] ] as $step){
					if(intval($step["PREVIEW_PICTURE"]) > 0){
						$str_xml .= "<photo>http://".$SERVER_NAME.CFile::GetPath($step["PREVIEW_PICTURE"])."</photo>";
					}
				}
				$str_xml .= "<final-photo>".$recipe["DETAIL_PICTURE"]."</final-photo>";
				if(strlen($recipe["PROPERTIES"]["portion"]) > 0){
					$str_xml .= "<yield>".$recipe["PROPERTIES"]["portion"]."</yield>";
				}
				if(intval($recipe["PROPERTIES"]["cooking_time"]) > 0){
					$cooking_time_hours = $recipe["PROPERTIES"]["cooking_time"]/60;
					$cooking_time_minutes = $recipe["PROPERTIES"]["cooking_time"]%60;
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
			}else{
				$arNotValidRecipes[] = $recipe["ID"];
			}
		}else{
			$arNoIngrRecipes[] = $recipe["ID"];
		}
	}
	//$str_xml .= '<xi:include href="known-features.xml"/>';
	$str_xml .= '</entities>';
	//echo strlen($str_xml);
	//echo $str_xml;
	fwrite($file,$str_xml);
	if(!empty($arNotValidRecipes)){
		$Name = "Foodclub Yandex Feed script"; //senders name 
		$email = "info@foodclub.ru"; //senders e-mail adress 
		$recipient = "madler@yandex.ru"; //recipient 
		$mail_body = "Названия файлов изображений содержат невалидные символы для Яндекс фида ([,]) для этих рецептов ".implode(",", $arNotValidRecipes); //mail body 
		$subject = "Невалидные рецепты"; //subject
		$header = "From: ". $Name . " <" . $email . ">\r\n"; //optional headerfields 

		mail($recipient, $subject, $mail_body, $header); //mail command :) 
	}
?>
