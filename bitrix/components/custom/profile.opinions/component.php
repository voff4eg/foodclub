<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$UserId = $arParams["USER"]["ID"];

CModule::IncludeModule("blog");
CModule::IncludeModule("socialnetwork");
include_once($_SERVER['DOCUMENT_ROOT'].'/classes/factory.class.php');

$cacheid = "profile_".SITE_ID."_".$UserId."_opinions";
if(isset($_REQUEST["PAGEN_1"]) && intval($_REQUEST["PAGEN_1"])){
	$cacheid .= "_".intval($_REQUEST["PAGEN_1"]);
}else{
	$cacheid .= "_1";
}

$obCache = new CPHPCache;
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

	$Filter = Array("IBLOCK_ID"=>6, "ACTIVE"=>"Y", "CREATED_BY"=>$UserId);
	$Select = Array("ID","PREVIEW_TEXT", "DATE_CREATE", "PROPERTY_recipe");

	$rowOpinions = CIblockElement::GetList( Array("DATE_CREATE"=>"DESC"), $Filter, false, Array("nPageSize"=>25), $Select );
	if($rowOpinions->isNavPrint()){
		$NavString = $rowOpinions->GetPageNavStringEx($navComponentObject, "Рецепты", "blogs", "N");
	}

	while($Opinion = $rowOpinions->GetNext())
	{
		$Opinions[ $Opinion['ID'] ] = $Opinion;
		$Iblocks[] = $Opinion['PROPERTY_RECIPE_VALUE'];
	}

	if( count($Iblocks) > 0 )
	{
		$rowBlocks = CIBlockElement::GetList(
						Array("NAME"=>"ASC"), 
						Array("ID"=>$Iblocks), 
						false, false,
						Array("ID", "NAME", "CODE")
					);
					
		while ($Recipe = $rowBlocks->GetNext()) {
			$Recipes[ $Recipe['ID'] ] = $Recipe;
		}
	}
	
	$arResult = array(
		"Opinions" => $Opinions,
		"Recipes" => $Recipes,
		"Iblocks" => $Iblocks,
		"SocNetBlogs" => $SocNetBlogs,
		"NavString" => $NavString
	);
	$CACHE_MANAGER->RegisterTag("profile_".$UserId."_opinions");	
	$CACHE_MANAGER->EndTagCache();
	$obCache->EndDataCache(
		$arResult	
	);
}else{
	$arResult = array();	
}

$this->IncludeComponentTemplate();
?>