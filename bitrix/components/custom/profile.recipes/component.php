<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

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
	/*$arRecipe = $vars["arRecipe"];
	$arKitchensId = $vars["arKitchensId"];
	$arKitchens = $vars["arKitchens"];*/	
	/*require_once($_SERVER["DOCUMENT_ROOT"]."/classes/main.class.php");
	$CFClub = new CFClub();
	if(CModule::IncludeModule("iblock")){
		$arRecipe = $CFClub->getList(30, Array("CREATED_BY"=>$UserId), "blogs", "N");
		$arKitchensId = array_keys($arRecipe['Kitchen']);
		$arKitchens = $CFClub->getKitchens(false, $arKitchensId);
	}*/
}elseif($obCache->StartDataCache()){
    global $CACHE_MANAGER;
	$CACHE_MANAGER->StartTagCache("/profile_".$UserId);
	require_once($_SERVER["DOCUMENT_ROOT"]."/classes/main.class.php");
	$CFClub = new CFClub();
	if(CModule::IncludeModule("iblock")){
		$arRecipe = array();
		$arRecipe = $CFClub->getList(12, Array("CREATED_BY"=>$UserId), "blogs", "N");
		$arKitchensId = array();
		if(is_array($arRecipe['Kitchen'])){
			$arKitchensId = array_keys($arRecipe['Kitchen']);
			if(!empty($arKitchensId)){
				$arKitchens = $CFClub->getKitchens(false, $arKitchensId);
			}else{
				$arKitchens = array();	
			}
		}else{
			$arKitchens = array();
		}
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
$this->IncludeComponentTemplate();
?>