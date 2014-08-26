<?
$_SERVER["DOCUMENT_ROOT"] = "/home/webserver/www";
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/main.class.php");

CModule::IncludeModule("iblock");

$CFClub = CFClub::getInstance();

$arRecipe = array();		$arRecipesJSON = array();		$arRecipeProps = array();
$arCuisine = array();		$arCuisineJSON = array();		$arRecipes = array();
$arDish = array();			$arDishesJSON = array();
$arIngredient = array();	$arIngredientsJSON = array();
$arIngGroup = array();		$arIngGroupsJSON = array();

function check( $param ){
	if( !$param )
		$param = "";
	return $param;
}

$arStagesID = array();$arCcalSum = array();
//Рецепты
if(empty($arRecipes)){

	$rsRecipes = CIBlockElement::GetList(array(),array("IBLOCK_ID"=>5, "ACTIVE"=>"Y", "PROPERTY_lib"=>"Y"),false,false);
	while($obRecipe = $rsRecipes->GetNextElement()){
		
		$arRecipe = $obRecipe->GetFields();
		$arRecipe["PROPERTIES"] = $obRecipe->GetProperties();

		if(!empty($arRecipe["PROPERTIES"]["recipt_steps"]["VALUE"])){
			$arStagesID = array_merge($arStagesID,$arRecipe["PROPERTIES"]["recipt_steps"]["VALUE"]);
			$arStagesID = array_unique($arStagesID);
		}
		$arRecipes[] = $arRecipe;

		$arCcalSum[ $arRecipe["ID"] ] = 0;
	}
}
//Этапы
if(!empty($arStagesID)){

	function IntNotNull($var){
	    // returns whether the input integer is odd
	    return(intval($var) > 0);
	}
	$arIng = array();	
	$arStagesID = array_filter($arStagesID, "IntNotNull");
	$rsStages = CIBlockElement::GetList(array(),array("IBLOCK_ID"=>4,"ID"=>$arStagesID),false,false);
	while($obStage = $rsStages->GetNextElement()){
		$arStage = $obStage->GetFields();
		$arStage["PROPERTIES"] = $obStage->GetProperties();
		if(intval($arStage["PROPERTIES"]["parent"]["VALUE"]) > 0){
		//echo "<pre>";print_r($arStage["PROPERTIES"]);echo "</pre>";
			if(!empty($arStage["PROPERTIES"]["ingredient"]["VALUE"])){
				$arIngrCcal = array_combine($arStage["PROPERTIES"]["ingredient"]["VALUE"], $arStage["PROPERTIES"]["numer"]["VALUE"]);				
				$rsIng = CIBlockElement::GetList(array(), array("ID"=>$arStage["PROPERTIES"]["ingredient"]["VALUE"],"IBLOCK_ID"=>3), false, false, array("ID","NAME","PROPERTY_unit","PROPERTY_kkal"));
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
					
					$arCcalSum[ $arStage["PROPERTIES"]["parent"]["VALUE"] ] += $mass*$kkal;					
				}
			}	
		}
	}
}

//echo "<pre>";print_r($arCcalSum);echo "</pre>";die;

foreach($arRecipes as $recipe){
	$arJSONRecipe["id"] = $recipe['ID'];
	//$arJSONRecipe["title"] = htmlspecialchars($recipe['NAME']);
	$arJSONRecipe["title"] = htmlspecialchars($recipe['NAME']);
	//if(intval($recipe["PREVIEW_PICTURE"])){
	if(intval($recipe["PROPERTIES"]["search_pic"]["VALUE"])){
		//$arJSONRecipe["image"] = CFile::GetPath($recipe['PREVIEW_PICTURE']);
		$arJSONRecipe["image"] = CFile::GetPath( $recipe["PROPERTIES"]["search_pic"]["VALUE"] );
	}else{
		$arJSONRecipe["image"] = "";
	}
	$arJSONRecipe["time"] = check( $recipe["PROPERTIES"]["cooking_time"]["VALUE"] );	
	if(intval($recipe["PROPERTIES"]["portion"]["VALUE"]) > 0){
		$arJSONRecipe["nutrition"] = intval($arCcalSum[ $recipe["ID"] ]/intval($recipe["PROPERTIES"]["portion"]["VALUE"]))." кКал";
	}else{
		$arJSONRecipe["nutrition"] = intval($arCcalSum[ $recipe["ID"] ])." кКал";
	}
	//$arJSONRecipe["nutrition"] = check( $recipe["PROPERTIES"]["kkal"]["VALUE"] );
	$arJSONRecipe["yield"] = check( $recipe["PROPERTIES"]["portion"]["VALUE"] );
	
	$arRecipesJSON[] = $arJSONRecipe;
}

//echo "<pre>";
/*foreach($arRecipes['ITEMS'] as $Recipe)
{
	$arRecipe["id"] = $Recipe['ID'];
	$arRecipe["title"] = htmlspecialchars($Recipe['NAME']);
	$arRecipe["image"] = $Recipe['PREVIEW_PICTURE']["SRC"];

	$res = CIBlockElement::GetList( array(), array( "IBLOCK_CODE" => "recipe", "ID" => $Recipe['ID'] ), false, false, array( "PROPERTY_cooking_time", "PROPERTY_kcals", "PROPERTY_portion" ) );
	if( $arRecipeProps = $res->GetNext() ){
		$arRecipe["time"] = check( $arRecipeProps["PROPERTY_COOKING_TIME_VALUE"] );
		$arRecipe["nutrition"] = check( $arRecipeProps["PROPERTY_KCALS_VALUE"] );
		$arRecipe["yield"] = check( $arRecipeProps["PROPERTY_PORTION_VALUE"] );
	}

	$arRecipesJSON[] = $arRecipe;
}*/
//echo "</pre>";

//Кухни
if(!isset($arCuisines)){
	$arCuisines = $CFClub->getKitchens();
}
foreach($arCuisines as $Cuisine)
{
	$arCuisine["id"] = $Cuisine['ID'];
	$arCuisine["title"] = $Cuisine['NAME'];
	$arCuisineJSON[] = $arCuisine;
}


//Блюда
if(!isset($arDishes)){
	$arDishes = $CFClub->getDishType();
}
foreach($arDishes as $Dish)
{
	$arDish["id"] = $Dish['ID'];
	$arDish["title"] = $Dish['NAME'];
	$arDishesJSON[] = $arDish;
}


//Ингредиенты
if(!isset($arIngGroups)){
	$arIngGroups = $CFClub->getUnitList();
}

//echo "<pre>";
foreach ($arIngGroups as $IngGroup)
{
	if($IngGroup['ID'] > 0){
		$arIngGroup["id"] = check( $IngGroup['ID'] );
		$arIngGroup["title"] = check( $IngGroup['NAME'] );
						//print_r($IngGroup);

			//print_r($IngGroup);
		foreach($IngGroup['UNITS'] as $intKey => $arItem)
		{
			//echo "<pre>";print_r($arItem["PREVIEW_PICTURE"]);echo "</pre>";
			$arIngredient["id"] = $arItem['ID'];
			$arIngredient["title"] = $arItem['NAME'];
			$arIngredient["image"] = check( $arItem["PREVIEW_PICTURE"]["SRC"] );
			$arIngredient["unit"] = check( $arItem['UNIT'] );
			$arIngredient["group"] = check( $IngGroup['ID'] );
			$arIngredientsJSON[] = $arIngredient;
						//echo $intKey." => ";
						//print_r($arItem);
			$arIngGroup["items"][] = $arItem['ID'];
		}

		$arIngGroupsJSON[] = $arIngGroup;
	}	
}
//echo "</pre>";
$arRecipesHeads = array("title" => "Рецепты", "url" => "/detail/$&/", "items" => $arRecipesJSON);
$arIngredientsHeads = array("title" => "Ингредиенты", "url" => "/search_service/?id=$&", "items" => $arIngredientsJSON);
$arIngGroupsHeads = array("title" => "Группы ингредиентов", "url" => "", "items" => $arIngGroupsJSON);
$arCuisinesHeads = array("title" => "Кухни", "url" => "/all/?k=$&", "items" => $arCuisineJSON);
$arDishesHeads = array("title" => "Типы блюд", "url" => "/all/?d=$&", "items" => $arDishesJSON);

$arJSON = array(
	"recipes" => $arRecipesHeads,
	"ingredients" => $arIngredientsHeads,
	"ingredientsGroup" => $arIngGroupsHeads,
	"cuisines" => $arCuisinesHeads,
	"dishes" => $arDishesHeads
);
$strJSON = json_encode($arJSON);

ob_start();
echo $strJSON;
$content = ob_get_contents();
ob_end_clean();
//echo $strJSON;
$fp = fopen($_SERVER['DOCUMENT_ROOT']."/php/foodclubJSON.php", 'w+');
fwrite($fp, $content);
fclose($fp);
die;
?>