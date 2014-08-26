<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

function formatRecipeText($text){
	/* &lt;/br&gt; &lt;br /&gt; &lt;br&gt; &#40;  &#41; */
	switch($text) {
		case (strpos($text,"J'aime") !== false):
			$returnStr = '"'.str_replace("&#41;",")",str_replace("&#40;","(",str_replace("&lt;/br&gt;","",str_replace("&lt;br /&gt;","",str_replace("&lt;br&gt;","",$text))))).'"';		    
			break;
		case (strpos($text,"Shepherd's") !== false):			
			$returnStr = '"'.str_replace("&#41;",")",str_replace("&#40;","(",str_replace("&lt;/br&gt;","",str_replace("&lt;br /&gt;","",str_replace("&lt;br&gt;","",$text))))).'"';		    
			break;
		case (strpos($text,"ch'arki") !== false):
			$returnStr = '"'.str_replace("&#41;",")",str_replace("&#40;","(",str_replace("&lt;/br&gt;","",str_replace("&lt;br /&gt;","",str_replace("&lt;br&gt;","",$text))))).'"';		    
			break;
		default:
			$returnStr = "'".str_replace("&#41;",")",str_replace("&#40;","(",str_replace("&lt;/br&gt;","",str_replace("&lt;br /&gt;","",str_replace("&lt;br&gt;","",$text)))))."'";			
	}
	
	return $returnStr;
}

function formatStageText($text){
	/* &lt;/br&gt; &lt;br /&gt; &lt;br&gt; &#40;  &#41; */		
	return str_replace("&#41;",")",str_replace("&#40;","(",str_replace("&lt;/br&gt;","",str_replace("&lt;br /&gt;","",str_replace("&lt;br&gt;","",$text)))));
}

if(CModule::IncludeModule("iblock")){

	$arRecipesSQL = array();
	$arCcalSum = array();
	//
	//2,7,384,27,56,392,2766,4272,5,26,62,70,340,451,5680,9585
	//J'aime Shepherd's ch'arki
	//Создание необходимых таблиц
	$content = "BEGIN TRANSACTION;".PHP_EOL;
	$content .= 'CREATE TABLE "android_metadata" ("locale" TEXT DEFAULT \'en_US\');'.PHP_EOL;
	$content .= 'CREATE TABLE buys (value NUMERIC, measure TEXT, name TEXT, _id NUMERIC);'.PHP_EOL;	
	$content .= 'CREATE TABLE favorite (rid NUMERIC, _id INTEGER PRIMARY KEY);'.PHP_EOL;
	$content .= 'CREATE TABLE help (_id INTEGER PRIMARY KEY, name TEXT);'.PHP_EOL;
	$content .= 'CREATE TABLE history (rid NUMERIC, _id INTEGER PRIMARY KEY);'.PHP_EOL;
	$content .= 'CREATE TABLE stage_ingredients (_id INTEGER PRIMARY KEY, sid NUMERIC, iid NUMERIC, value NUMERIC, rid NUMERIC);'.PHP_EOL;
	$content .= 'CREATE TABLE tobuy (sort NUMERIC, value NUMERIC, measure TEXT, _id INTEGER PRIMARY KEY, name TEXT);'.PHP_EOL;
	$content .= 'CREATE TABLE my_recipes (description TEXT, _id INTEGER PRIMARY KEY, name TEXT, picture TEXT);'.PHP_EOL;
	$content .= 'CREATE TABLE recipes (time TEXT, ccal TEXT,mid NUMERIC, did NUMERIC, kid NUMERIC, portions NUMERIC, image TEXT, text TEXT, name TEXT, s_id NUMERIC, _id INTEGER PRIMARY KEY);'.PHP_EOL;
	//Рецепты
	$arFreeRecipesId = array(
		52136, 52023, 51994, 51977, 51791, 51662, 51644, 53585, 53427, 53362, 53079, 53060, 53045, 52761, 52536, 52374, 51150, 51043, 50946, 50878, 50453, 50320, 50152, 50128, 49979, 49918, 49727, 49648, 49240, 49038, 49021, 48805, 48751, 48600, 48488, 48464, 47863, 47749, 47492,
		47502, 47595, 47427, 47397, 47278, 47122, 46984, 46776, 45485, 45174, 45160, 45091, 44849, 44829, 44702, 44213, 44079, 44023, 43786, 43657, 43356, 43268, 42969, 42779, 42525, 42230, 42034, 41992, 42034, 41964, 41859, 40774, 41131, 39301, 38576, 38474, 37917, 37352, 37291,
		36972, 35724, 35743, 35660, 35590, 34132, 33629, 33507, 33472, 33272, 33074, 32822, 32545, 32465, 31514, 21537, 21492, 21341, 21321, 21239, 21047, 21032, 21009, 20753, 20741, 20573, 20526, 20444, 20351, 18988, 18500, 18438, 18170, 18141, 17916, 17893, 17575, 17254, 17047,
		16828, 16763, 16706, 20794, 34198, 44461, 19952, 36039, 15943, 7963, 44309, 32074, 42936, 16142, 43775, 16149, 17485, 44539, 6632, 38630, 5850, 18552, 17518, 36790, 38836, 8440, 18035, 7943, 46912, 6014, 49229, 16989, 16187, 1340, 16201, 32848,
		31197, 45192, 49353, 14714, 47946, 6240
	);
	
	$rsRecipes = CIBlockElement::GetList(array(),array("IBLOCK_ID"=>5,"PROPERTY_lib"=>"Y",array(
        "LOGIC" => "OR",
        array("PROPERTY_add_mobile"=>3),
        array("CREATED_BY"=>array(2,7,384,27,56,392,2766,4272,5,26,62,70,340,451,5680,9585)),
    )),false,false);
	echo $rsRecipes->SelectedRowsCount();
	while($obRecipe = $rsRecipes->GetNextElement()){
		$arRecipe = $obRecipe->GetFields();
		$arRecipe["PROPERTIES"] = $obRecipe->GetProperties();			

		$arRecipes[ $arRecipe["ID"] ] = $arRecipe;		
		$arRecipeSteps[ $arRecipe["ID"] ] = $arRecipe["PROPERTIES"]["recipt_steps"]["VALUE"];
		foreach($arRecipe["PROPERTIES"]["recipt_steps"]["VALUE"] as $stage){
			$arStageRecipe[ $stage ] = $arRecipe["ID"];
		}
		if(intval($arRecipe["DETAIL_PICTURE"]) > 0){
			$strFileSrc = CFile::GetPath($arRecipe["DETAIL_PICTURE"]);			
			//$arFile = CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"].$strFileSrc);
			//echo "<pre>";print_r($arFile);echo "</pre>";die;
			$path_attr = pathinfo($_SERVER["DOCUMENT_ROOT"].$strFileSrc);
			if(strlen($path_attr["extension"]) > 0){
				$file = $_SERVER["DOCUMENT_ROOT"].$strFileSrc;
				$des = $_SERVER["DOCUMENT_ROOT"]."/upload/android/p".$arRecipe["ID"].".".$path_attr["extension"];
				if(file_exists($file)){
					if (!copy($file, $des)) {
					    echo "не удалось скопировать $file в $des..\n";
					}else{
						$strFileSrc = "p".$arRecipe["ID"].".".$path_attr["extension"];
					}
				}				
			}
		}else{
			$strFileSrc = "";
		}
		$arCcalSum[ $arRecipe["ID"] ] = 0;
		$arRecipesSQL[ $arRecipe["ID"] ] = array(
			"main_ingredient" => $arRecipe["PROPERTIES"]["main_ingredient"]["VALUE"],
			"dish_type" => $arRecipe["PROPERTIES"]["dish_type"]["VALUE"],
			"kitchen" => $arRecipe["PROPERTIES"]["kitchen"]["VALUE"],
			"portion" => (intval($arRecipe["PROPERTIES"]["portion"]["VALUE"]) > 0 ? intval($arRecipe["PROPERTIES"]["portion"]["VALUE"]) : 1),
			"time" => $arRecipe["PROPERTIES"]["cooking_time"]["VALUE"],
			"strFileSrc" => $strFileSrc,
			"PREVIEW_TEXT" => strip_tags($arRecipe["~PREVIEW_TEXT"]),
			"NAME" => $arRecipe["NAME"],
			"ID" => $arRecipe["ID"]
		);

		if(in_array($arRecipe["ID"], $arFreeRecipesId)){
			$arRecipesSQL[ $arRecipe["ID"] ]["PREVIEW_TEXT"] = strip_tags($arRecipe["~PREVIEW_TEXT"]);		
		}else{
			$arRecipesSQL[ $arRecipe["ID"] ]["PREVIEW_TEXT"] = "";
		}
		//$content .= "INSERT INTO recipes VALUES('".$arRecipe["PROPERTIES"]["main_ingredient"]["VALUE"]."','".$arRecipe["PROPERTIES"]["dish_type"]["VALUE"]."','".$arRecipe["PROPERTIES"]["kitchen"]["VALUE"]."','".(intval($arRecipe["PROPERTIES"]["portion"]["VALUE"]) > 0 ? intval($arRecipe["PROPERTIES"]["portion"]["VALUE"]) : 1)."','".$strFileSrc."','".strip_tags($arRecipe["~PREVIEW_TEXT"])."','".$arRecipe["NAME"]."','".$arRecipe["ID"]."','".$arRecipe["ID"]."');".PHP_EOL;
	}

	$content .= 'CREATE TABLE main_ingredient (icon TEXT, _id INTEGER PRIMARY KEY, name TEXT);'.PHP_EOL;	
	//Основные ингредиенты
	$rsMainIngredients = CIBlockElement::GetList(array(),array("IBLOCK_ID"=>14),false,false,array("ID","NAME"));
	while($arMainIngredient = $rsMainIngredients->Fetch()){	
		$content .= "INSERT INTO main_ingredient VALUES('','".$arMainIngredient["ID"]."','".trim(str_replace("&quot;",'"',$arMainIngredient["NAME"]))."');".PHP_EOL;
	}

	$content .= 'CREATE TABLE ingredients (ccal NUMERIC, measure TEXT, name TEXT, _id INTEGER PRIMARY KEY);'.PHP_EOL;
	//Ингредиенты
	$rsIngredients = CIBlockElement::GetList(array(),array("IBLOCK_ID"=>3),false,false,array("ID","NAME","PREVIEW_PICTURE","PROPERTY_unit","PROPERTY_kkal"));
	while($arIngredient = $rsIngredients->Fetch()){
		$content .= "INSERT INTO ingredients VALUES('".$arIngredient["PROPERTY_KKAL_VALUE"]."','".$arIngredient["PROPERTY_UNIT_VALUE"]."','".trim(str_replace("&quot;",'"',$arIngredient["NAME"]))."','".$arIngredient["ID"]."');".PHP_EOL;
	}
	$stage_id = 0;
	$stage_ingr_content = "";
	$content .= 'CREATE TABLE stages (image TEXT, text TEXT, r_id NUMERIC, _id INTEGER PRIMARY KEY);'.PHP_EOL;
	//Этапы рецептов
	$rsRecipeStages = CIBlockElement::GetList(array(),array("IBLOCK_ID"=>4,"ID"=>array_values($arRecipeSteps)),false,false);
	while($obRecipeStage = $rsRecipeStages->GetNextElement()){
		//array("ID","NAME","PREVIEW_TEXT","PREVIEW_PICTURE","PROPERTY_ingredient","PROPERTY_numer","PROPERTY_parent")
		$arRecipeStage = $obRecipeStage->GetFields();
		$arRecipeStage["PROPERTIES"] = $obRecipeStage->GetProperties();
		
		if(intval($arRecipeStage["PREVIEW_PICTURE"]) > 0){
			$strFileSrc = CFile::GetPath($arRecipeStage["PREVIEW_PICTURE"]);
			$path_attr = pathinfo($_SERVER["DOCUMENT_ROOT"].$strFileSrc);
			if(strlen($path_attr["extension"]) > 0){
				$file = $_SERVER["DOCUMENT_ROOT"].$strFileSrc;
				$des = $_SERVER["DOCUMENT_ROOT"]."/upload/android/p".$arRecipeStage["ID"].".".$path_attr["extension"];
				if(file_exists($file)){
					if (!copy($file, $des)) {
					    echo "не удалось скопировать $file в $des..\n";
					}else{
						$strFileSrc = "p".$arRecipeStage["ID"].".".$path_attr["extension"];
					}
				}
			}
		}else{
			$strFileSrc = "";
		}
		if($arStageRecipe[ $arRecipeStage["ID"] ] > 0){
			if(in_array($arStageRecipe[ $arRecipeStage["ID"] ], $arFreeRecipesId)){
				$content .= "INSERT INTO stages VALUES('".$strFileSrc."','".strip_tags(formatStageText($arRecipeStage["PREVIEW_TEXT"]))."','".$arStageRecipe[ $arRecipeStage["ID"] ]."','".$arRecipeStage["ID"]."');".PHP_EOL;
			}else{
				$content .= "INSERT INTO stages VALUES('','','".$arStageRecipe[ $arRecipeStage["ID"] ]."','".$arRecipeStage["ID"]."');".PHP_EOL;
			}			
			foreach($arRecipeStage["PROPERTIES"]["ingredient"]["VALUE"] as $key => $singr){
				$stage_id++;
				//$content .= "INSERT INTO stages VALUES('".$arRecipeStage["ID"]."','".$strFileSrc."','".strip_tags(formatText($arRecipeStage["PREVIEW_TEXT"]))."','".$arStageRecipe[ $arRecipeStage["ID"] ]."','".$arRecipeStage["PROPERTIES"]["numer"]["VALUE"][$key]."','".$singr."','".$stage_id."');".PHP_EOL;
				$stage_ingr_content .= "INSERT INTO stage_ingredients VALUES('".$stage_id."','".$arRecipeStage["ID"]."','".$singr."','".$arRecipeStage["PROPERTIES"]["numer"]["VALUE"][$key]."','".$arStageRecipe[ $arRecipeStage["ID"] ]."');".PHP_EOL;
			}
		}

		if(intval($arRecipeStage["PROPERTIES"]["parent"]["VALUE"]) > 0){
			if(!empty($arRecipeStage["PROPERTIES"]["ingredient"]["VALUE"])){
				$arIngrCcal = array_combine($arRecipeStage["PROPERTIES"]["ingredient"]["VALUE"], $arRecipeStage["PROPERTIES"]["numer"]["VALUE"]);				
				$rsIng = CIBlockElement::GetList(array(), array("ID"=>$arRecipeStage["PROPERTIES"]["ingredient"]["VALUE"],"IBLOCK_ID"=>3), false, false, array("ID","NAME","PROPERTY_unit","PROPERTY_kkal"));
				while($arIngSrc = $rsIng->GetNext()){

					if(floatval(str_replace(",",".",$arIngSrc["PROPERTY_KKAL_VALUE"])) <= 0){
						$kkal = 0;
					}else{
						$kkal = floatval(str_replace(",",".",$arIngSrc["PROPERTY_KKAL_VALUE"]));
					}
					if(floatval(str_replace(",",".",$arIngrCcal[ $arIngSrc["ID"] ])) <= 0){
						$mass = 0;
					}else{
						$mass = floatval(str_replace(",",".",$arIngrCcal[ $arIngSrc["ID"] ]));
					}
					
					$arCcalSum[ $arRecipeStage["PROPERTIES"]["parent"]["VALUE"] ] += $mass*$kkal;					
				}
			}	
		}
	}
	
	$content .= $stage_ingr_content;

	foreach($arRecipesSQL as $id => $recipe){
		$content .= "INSERT INTO recipes VALUES('".$recipe["time"]."','".$arCcalSum[ $id ]."','".$recipe["main_ingredient"]."','".$recipe["dish_type"]."','".$recipe["kitchen"]."','".(intval($recipe["portion"]) > 0 ? intval($recipe["portion"]) : 1)."','".$recipe["strFileSrc"]."',".strip_tags(formatRecipeText($recipe["PREVIEW_TEXT"])).",'".trim(str_replace("&quot;",'"',$recipe["NAME"]))."','".$recipe["ID"]."','".$recipe["ID"]."');".PHP_EOL;
	}
	
	$kid = 0;
	$content .= 'CREATE TABLE kitchen (sid NUMERIC, icon TEXT, d_id NUMERIC, name TEXT, _id INTEGER PRIMARY KEY);'.PHP_EOL;
	//Кухни
	$rsKitchens = CIBlockElement::GetList(array(),array("IBLOCK_ID"=>2),false,false);
	while($obKitchen = $rsKitchens->GetNextElement()){
		$arKitchen = $obKitchen->GetFields();
		$arKitchen["PROPERTIES"] = $obKitchen->GetProperties();
		
		if(intval($arKitchen["PREVIEW_PICTURE"]) > 0){
			$strFileSrc = CFile::GetPath($arKitchen["PREVIEW_PICTURE"]);
			$path_attr = pathinfo($_SERVER["DOCUMENT_ROOT"].$strFileSrc);
			if(strlen($path_attr["extension"]) > 0){
				$file = $_SERVER["DOCUMENT_ROOT"].$strFileSrc;
				$des = $_SERVER["DOCUMENT_ROOT"]."/upload/android/p".$arKitchen["ID"].".".$path_attr["extension"];
				if(file_exists($file)){
					if (!copy($file, $des)) {
					    echo "не удалось скопировать $file в $des..\n";
					}else{
						$strFileSrc = "p".$arKitchen["ID"].".".$path_attr["extension"];
					}
				}
			}
		}else{
			$strFileSrc = "";
		}
		foreach($arKitchen["PROPERTIES"]["dish_type"]["VALUE"] as $dish_type){
			$kid++;
			$content .= "INSERT INTO kitchen VALUES('".$arKitchen["ID"]."','".$strFileSrc."','".$dish_type."','".trim(str_replace("&quot;",'"',$arKitchen["NAME"]))."','".$kid."');".PHP_EOL;
		}
	}
	
	$content .= 'CREATE TABLE dish (icon TEXT, name TEXT, _id INTEGER PRIMARY KEY);'.PHP_EOL;
	//Типы блюд
	$rsDish = CIBlockElement::GetList(array(),array("IBLOCK_ID"=>1),false,false);
	while($obDish = $rsDish->GetNextElement()){
		$arDish = $obDish->GetFields();		
		
		if(intval($arDish["PREVIEW_PICTURE"]) > 0){
			$strFileSrc = CFile::GetPath($arDish["PREVIEW_PICTURE"]);
			$path_attr = pathinfo($_SERVER["DOCUMENT_ROOT"].$strFileSrc);
			if(strlen($path_attr["extension"]) > 0){
				$file = $_SERVER["DOCUMENT_ROOT"].$strFileSrc;
				$des = $_SERVER["DOCUMENT_ROOT"]."/upload/android/p".$arDish["ID"].".".$path_attr["extension"];
				if(file_exists($file)){
					if (!copy($file, $des)) {
					    echo "не удалось скопировать $file в $des..\n";
					}else{
						$strFileSrc = "p".$arDish["ID"].".".$path_attr["extension"];
					}
				}
			}
		}else{
			$strFileSrc = "";
		}
		$content .= "INSERT INTO dish VALUES('".$strFileSrc."','".trim(str_replace("&quot;",'"',$arDish["NAME"]))."','".$arDish["ID"]."');".PHP_EOL;
	}
	
	$content .= 'COMMIT;'.PHP_EOL;

	$fp = fopen($_SERVER['DOCUMENT_ROOT']."/upload/sql4freeandroidapp.txt", 'w+');
	fwrite($fp, $content);
	fclose($fp);
}