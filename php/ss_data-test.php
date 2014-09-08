<?
$_SERVER["DOCUMENT_ROOT"] = "/home/webserver/www";
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/main.class.php");
define("ADMIN_SECTION", false);
//require("/home/webserver/www/bitrix/modules/main/include/prolog_before.php");
//require("/home/webserver/www/classes/main.class.php");

function check( $param ){
	if( !$param )
		$param = "";
	return $param;
}

CModule::IncludeModule("iblock");
//Рецепты
$arRecipes = array();
$rsRecipes = CIBlockElement::GetList(array("name"=>"asc"),array("IBLOCK_ID"=>5,"PROPERTY_lib"=>"y","ACTIVE"=>"Y"));
while($arRecipe = $rsRecipes->GetNext()){
	$arRecipeIds[] = $arRecipe["ID"];
	$arRecipeNames[] = addslashes(htmlspecialchars($arRecipe["NAME"]));
}
$arRecipes[0] = $arRecipeIds;
$arRecipes[1] = $arRecipeNames;

//Кухни
$arKitchens = array();
$rsKitchens = CIBlockElement::GetList(array("name"=>"asc"),array("IBLOCK_ID"=>2,"ACTIVE"=>"Y"));
while($arKitchen = $rsKitchens->GetNext()){
	$arKitchenIds[] = $arKitchen["ID"];
	$arKitchenNames[] = addslashes(htmlspecialchars($arKitchen["NAME"]));
}
$arKitchens[0] = $arKitchenIds;
$arKitchens[1] = $arKitchenNames;

//Типы блюд
$arType = array();
$rsTypes = CIBlockElement::GetList(array("name"=>"asc"),array("IBLOCK_ID"=>1,"ACTIVE"=>"Y"));
while($arType = $rsTypes->GetNext()){
	$arTypeIds[] = $arType["ID"];
	$arTypeNames[] = addslashes(htmlspecialchars($arType["NAME"]));
}

//Основной ингредиент
$arMainIngredients = array();
$rsMainIngredients = CIBlockElement::GetList(array("name"=>"asc"),array("IBLOCK_ID"=>14,"ACTIVE"=>"Y"));
while($arMainIngredient = $rsMainIngredients->GetNext()){
	$arMainIngredientIds[] = $arMainIngredient["ID"];
	$arMainIngredientNames[] = addslashes(htmlspecialchars($arMainIngredient["NAME"]));
}
$arMainIngredients[0] = $arMainIngredientIds;
$arMainIngredients[1] = $arMainIngredientNames;

//Ингредиенты
$arIngredients = array();
$rsIngredients = CIBlockElement::GetList(array("name"=>"asc"),array("IBLOCK_ID"=>3,"ACTIVE"=>"Y"),false,false,array("ID","NAME","PROPERTY_unit","IBLOCK_SECTION_ID"));
while($arIngredient = $rsIngredients->GetNext()){
	if(intval($arIngredient["IBLOCK_SECTION_ID"])){
		$arIngredientIds[ intval($arIngredient["IBLOCK_SECTION_ID"]) ][] = $arIngredient["ID"];
		$arIngredientNames[ intval($arIngredient["IBLOCK_SECTION_ID"]) ][] = addslashes(htmlspecialchars($arIngredient["NAME"]));
		$arIngredientMeasures[ intval($arIngredient["IBLOCK_SECTION_ID"]) ][] = $arIngredient["PROPERTY_UNIT_VALUE"];
	}
}

//echo "arIngredientIds<pre>";print_r($arIngredientIds);echo "</pre>";
//echo "arIngredientNames<pre>";print_r($arIngredientNames);echo "</pre>";

$arKeys = array_keys($arIngredientIds);
if(!empty($arKeys)){
	$rsIngredientSections = CIBlockSection::GetList(array("name"=>"asc"),array("IBLOCK_ID"=>3,"ID"=>$arKeys));
	while($arIngredientSection = $rsIngredientSections->GetNext()){
		$arIngredientSectionIds[ $arIngredientSection["ID"] ] = $arIngredientSection["ID"];
		$arIngredientSectionNames[ $arIngredientSection["ID"] ] = addslashes(htmlspecialchars($arIngredientSection["NAME"]));
	}
}

//echo "arIngredientSectionIds<pre>";print_r($arIngredientSectionIds);echo "</pre>";
//echo "arIngredientSectionNames<pre>";print_r($arIngredientSectionNames);echo "</pre>";
//die;
//ob_start();

/*echo "var recipeArray = [];".PHP_EOL;
echo "recipeArray[0] = {".implode(",", $arRecipeIds)."}".PHP_EOL;
echo "recipeArray[1] = {".implode(",", $arRecipeNames)."}".PHP_EOL;
echo "var dishTypeArray =[];".PHP_EOL;
echo "dishTypeArray[0] = {".implode(",", $arTypeIds)."}".PHP_EOL;
echo "dishTypeArray[1] = {".implode(",", $arTypeNames)."}".PHP_EOL;
echo "var cuisineArray =[];".PHP_EOL;
echo "cuisineArray[0] = {".implode(",", $arKitchenIds)."}".PHP_EOL;
echo "cuisineArray[1] = {".implode(",", $arKitchenNames)."}".PHP_EOL;
echo "var mainIngredientArray =[];".PHP_EOL;
echo "mainIngredientArray[0] = {".implode(",", $arMainIngredientIds)."}".PHP_EOL;
echo "mainIngredientArray[1] = {".implode(",", $arMainIngredientNames)."}".PHP_EOL;
echo "var ingredientArray =[]; ingredientArray[0]=[]; ingredientArray[1]=[]; ingredientArray[2]=[];ingredientArray[2][0]=[];".PHP_EOL;*/

$i = 0;
foreach($arIngredientIds as $key => $arSection){
	//echo $i."@";echo implode(",", array_values($arSection));echo implode(",", array_values($arIngredientNames[$key]));echo "<br>";
	//echo $arIngredientSectionNames[$i];echo "<br>";
	$arIndexID[$i] = $arIngredientSectionIds[ $key ];
	$arIndexName[$i] = $arIngredientSectionNames[ $key ];
	//echo "ingredientArray[2][".$i."] = [];".PHP_EOL;
	//echo "ingredientArray[2][".$i."][0]={".implode(",", array_values($arSection))."}".PHP_EOL;
	//echo "ingredientArray[2][".$i."][1]={".implode(",", array_values($arIngredientNames[$key]))."}".PHP_EOL;
	//echo "ingredientArray[2][".$i."][2]={".implode(",", array_values($arIngredientMeasures[$key]))."}".PHP_EOL;	
	$i++;
}

//echo "ingredientArray[0] = {".implode(",", $arIngredientSectionIds)."}".PHP_EOL;
//echo "ingredientArray[1] = {".implode(",", $arIngredientSectionNames)."}".PHP_EOL;

//$content = ob_get_contents();
//ob_end_clean();
//$fp = fopen($_SERVER["DOCUMENT_ROOT"]."/js/ss_data.js", 'w+');
//fwrite($fp, $content);
//fclose($fp);
die;
?>