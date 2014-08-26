<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/*
$UserId = $arParams["USER"]["ID"];
$obCache = new CPHPCache;

$cacheid = "profile_".SITE_ID."_".$UserId."_recipes";
if(isset($_REQUEST["PAGEN_1"]) && intval($_REQUEST["PAGEN_1"])){
	$cacheid .= "_".intval($_REQUEST["PAGEN_1"]);
}else{
	$cacheid .= "_1";
}

if($obCache->InitCache(3600, $cacheid, "/profile")){
	$arResult = $obCache->GetVars();

}elseif($obCache->StartDataCache()){
    global $CACHE_MANAGER;
	$CACHE_MANAGER->StartTagCache("/profile_".$UserId);
	require_once($_SERVER["DOCUMENT_ROOT"]."/classes/main.class.php");
	$CFClub = new CFClub();
	if(CModule::IncludeModule("iblock")){
		//$arRecipe = $CFClub->getList(12, Array("CREATED_BY"=>$UserId), "blogs", "N");		
		//$arKitchensId = array_keys($arRecipe['Kitchen']);
		//$arKitchens = $CFClub->getKitchens(false, $arKitchensId);
		
		$arResult['ITEMS']=array();
		$arSelect = Array("ID", "NAME", "PROPERTY_users", "PROPERTY_brand", "PROPERTY_cost");
		$arFilter = Array("IBLOCK_ID"=>25, "PROPERTY_users"=>1);
		$res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>50), $arSelect);
		while($ob = $res->GetNextElement())
		{
			$arFields = $ob->GetFields();
			
			$arSelectCom = Array("NAME", "DETAIL_TEXT");
			$arFilterCom = Array("IBLOCK_ID"=>27, "PROPERTY_users"=>1,"PROPERTY_model"=>$arFields["ID"]);
			$resCom = CIBlockElement::GetList(Array(), $arFilterCom, false, Array("nPageSize"=>50), $arSelectCom);
			while($obCom = $resCom->GetNextElement())
			{
				$arFieldsCom = $obCom->GetFields();
			}
			

			$arSelectBrand = Array("NAME", "PROPERTY_tech_type");
			$arFilterBrand = Array("IBLOCK_ID"=>24, "ID"=>$arFields["PROPERTY_BRAND_VALUE"]);
			$resBrand = CIBlockElement::GetList(Array(), $arFilterBrand, false, Array("nPageSize"=>50), $arSelectBrand);
			while($obBrand = $resBrand->GetNextElement())
			{
				$arFieldsBrand = $obBrand->GetFields();
			}
			

			$arSelectRating = Array("NAME", "PROPERTY_summ", "PROPERTY_count_people");
			$arFilterRating = Array("IBLOCK_ID"=>26, "PROPERTY_model"=>$arFields["ID"]);
			$resRating = CIBlockElement::GetList(Array(), $arFilterRating, false, Array("nPageSize"=>50), $arSelectRating);
			while($obRating = $resRating->GetNextElement())
			{
				$arFieldsRating = $obRating->GetFields();
			}
			

			$arSelectTech = Array("NAME", "PROPERTY_tech_type");
			$arFilterTech = Array("IBLOCK_ID"=>23, "ID"=>$arFieldsBrand["PROPERTY_TECH_TYPE_VALUE"]);
			$resTech = CIBlockElement::GetList(Array(), $arFilterTech, false, Array("nPageSize"=>50), $arSelectTech);
			while($obTech = $resTech->GetNextElement())
			{
				$arFieldsTech = $obTech->GetFields();
			}
		}
		
		$arResult['ITEMS'][$arFields["ID"]]["MODEL"]=$arFields["NAME"];
		$arResult['ITEMS'][$arFields["ID"]]["COMMENTS"]=$arFieldsCom["DETAIL_TEXT"];
		//$arResult['ITEMS'][$arFields["ID"]]["DETAIL_TEXT"]=$arFields["DETAIL_TEXT"];
		$arResult['ITEMS'][$arFields["ID"]]["BRAND"]=$arFieldsBrand["NAME"];
		$arResult['ITEMS'][$arFields["ID"]]["TECH"]=$arFieldsTech["NAME"];
		$arResult['ITEMS'][$arFields["ID"]]["COST"]=$arFields["PROPERTY_COST_VALUE"];
		$arResult['ITEMS'][$arFields["ID"]]["RATING_VAL"]=$arFieldsRating["PROPERTY_SUMM_VALUE"];
		$arResult['ITEMS'][$arFields["ID"]]["RATING_COL"]=$arFieldsRating["PROPERTY_COUNT_PEOPLE_VALUE"];
		$arResult['ITEMS'][$arFields["ID"]]["RATING_RESULT"]=round(($arFieldsRating["PROPERTY_SUMM_VALUE"])/($arFieldsRating["PROPERTY_COUNT_PEOPLE_VALUE"]));
		if($arResult['ITEMS'][$arFields["ID"]]["RATING_RESULT"]>5)$arResult['ITEMS'][$arFields["ID"]]["RATING_RESULT"]=5;
		elseif($arResult['ITEMS'][$arFields["ID"]]["RATING_RESULT"]<0)$arResult['ITEMS'][$arFields["ID"]]["RATING_RESULT"]=0;
		
		echo "<BR>22222222222222222222222222";
		echo "<BR>";
		print_r($arResult);
		echo "<BR>22222222222222222222222222";
		echo "<BR>";
		
		
		
		
		
		
		
		
		
		
	}	
	$arResult = array(
		"arRecipe" => $arRecipe,
		"arKitchensId" => $arKitchensId,
		"arKitchens" => $arKitchens
	);
	$CACHE_MANAGER->RegisterTag("profile_".$UserId."_recipes");	
	$CACHE_MANAGER->EndTagCache();
	$obCache->EndDataCache(
		$arResult	
	);
}else{
	$arResult = array();	
}
*/

$this->IncludeComponentTemplate();
?>