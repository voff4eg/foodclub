<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->RestartBuffer();

CModule::IncludeModule("iblock");
$CFClub = CFClub::getInstance();

$Content="var fc_data = {";
//recipes
if(!isset($arRecipes)){
	$arRecipes = $CFClub->getList(10000);
}
foreach($arRecipes['ITEMS'] as $Recipe)
{
		$arJSRecipe[] = "{id:'".$Recipe['ID']."',name:'".$Recipe['NAME']."'}";
}
$Content .= "recipes:[".join(",", $arJSRecipe)."],\n";

//cuisines
if(!isset($arCuisines)){
	$arCuisines = $CFClub->getKitchens();
}
foreach($arCuisines as $Cuisine)
{
	$arJSCuisines[] = "{id:'".$Cuisine['ID']."',name:'".$Cuisine['NAME']."'}";
}
$Content .= "cuisines:[".join(",", $arJSCuisines)."],\n";

//dishes
if(!isset($arDishes)){
	$arDishes = $CFClub->getDishType();
}
foreach($arDishes as $Dish)
{
	$arJSDishes[] = "{id:'".$Dish['ID']."',name:'".$Dish['NAME']."'}";
}
$Content .= "dishes:[".join(',', $arJSDishes)."],\n";

//ingredients
if(!isset($arIngGroups)){
	$arIngGroups = $CFClub->getUnitList();
}
foreach ($arIngGroups as $IngGroup)
{
	$arIngredient = array();
	foreach($IngGroup['UNITS'] as $intKey => $arItem)
	{
		$arIngredient[]="{id:'".$arItem['ID']."',name:'".$arItem['NAME']."',unit:'".$arItem['UNIT']."'}";
	}
	$jsIngredient=join(',', $arIngredient);
	$arIngr[]="{id:'".$IngGroup['ID']."',name:'".$IngGroup['NAME']."',items:[".$jsIngredient."]}";
}
$Content .= 'ingredients:['.join(',', $arIngr)."]";
$Content .= "}";

$fp = fopen($_SERVER['DOCUMENT_ROOT']."/js/ss_data2.js", 'w+');
fwrite($fp, $Content);
fclose($fp);
?>