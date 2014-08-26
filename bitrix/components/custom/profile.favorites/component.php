<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$UserId = $arParams["USER"]["ID"];

require_once($_SERVER["DOCUMENT_ROOT"]."/classes/favorite.class.php");

if(isset($_REQUEST['f']))
{
	if( intval($_REQUEST['r']) >0 )
	{
		CFavorite::delete($_REQUEST['r']);
		global $CACHE_MANAGER;
		$CACHE_MANAGER->ClearByTag("profile_".$UserId."_favorites");
		LocalRedirect($APPLICATION->GetCurPageParam("", array("r", "f","place")));
	}
}

$obCache = new CPHPCache;
if($obCache->InitCache(3600, "profile_".SITE_ID."_".$UserId."_favorites", "/profile")){
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
	$CACHE_MANAGER->StartTagCache("/profile");

	if(CModule::IncludeModule("iblock")){
		include_once $_SERVER["DOCUMENT_ROOT"].'/classes/main.class.php';

		$CFavorite = new CFavorite();
		$Recipes = $CFavorite->get_list($UserId);

		if(!empty($Recipes)){
			$CFClub = new CFClub();
			$arRecipe = $CFClub->getList(10000, Array("ID"=>$Recipes), "blogs", "N");
			$arKitchensId = array_keys($arRecipe['Kitchen']);
			$arKitchens = $CFClub->getKitchens(false, $arKitchensId);
		}
	}
	
	$arResult = array(
		"Recipes" => $Recipes,
		"arRecipe" => $arRecipe,
		"arKitchensId" => $arKitchensId,
		"arKitchens" => $arKitchens
	);
	$CACHE_MANAGER->RegisterTag("profile_".$UserId."_favorites");	
	$CACHE_MANAGER->EndTagCache();
	$obCache->EndDataCache(
		$arResult	
	);
}else{
	$arResult = array();	
}

$this->IncludeComponentTemplate();
?>