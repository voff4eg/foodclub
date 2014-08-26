<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$UserId = $arParams["USER"]["ID"];

//echo "<pre>";print_r($arParams["USER"]);echo "</pre>";

require_once($_SERVER["DOCUMENT_ROOT"]."/classes/favorite.class.php");

$obCache = new CPHPCache;
if($obCache->InitCache(3600, "profile_".SITE_ID."_".$UserId."_badges", "/profile")){
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

	if(CModule::IncludeModule("iblock") && CModule::IncludeModule("blog")){
		include_once $_SERVER["DOCUMENT_ROOT"].'/classes/main.class.php';
		$arBadges = array();
		if(!empty($arParams["USER"]["UF_BADGES"])){
			$parser = new blogTextParser;
			$arAllow = array("HTML"=>"Y","NL2BR" => "Y");
			$rsBadges = CIBlockElement::GetList(array("sort"=>"asc"),array("IBLOCK_CODE"=>"badges","ID"=>$arParams["USER"]["UF_BADGES"],"ACTIVE"=>"Y"),false,false,array("ID","NAME","PREVIEW_PICTURE","DETAIL_PICTURE","PREVIEW_TEXT"));
			while($arBadge = $rsBadges->Fetch()){
				$arBadge["PREVIEW_TEXT"] = addslashes($parser->convert($arBadge["PREVIEW_TEXT"] ,false,array(),$arAllow));
				$arBadges[] = $arBadge;
			}
		}		
	}
	
	$arResult = array(
		"badges" => $arBadges,		
	);
	$CACHE_MANAGER->RegisterTag("profile_".$UserId."_badges");	
	$CACHE_MANAGER->EndTagCache();
	$obCache->EndDataCache(
		$arResult	
	);
}else{
	$arResult = array();	
}

$this->IncludeComponentTemplate();
?>