<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?
if (/*isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest' && */($_REQUEST["action"] == "like" || $_REQUEST["action"] == "unlike") && intval($_REQUEST["id"]) > 0 && $USER->IsAuthorized())
{
	CModule::IncludeModule("iblock");

	$idFoodshot  = intval($_REQUEST["id"]);
	$action	= trim($_REQUEST["action"]);

	// user can like foodshot
	$bUserCanLikeFoodshot = false;

	// check if user ever liked this foodshot
	$arLikesFilter = array (
		"IBLOCK_CODE" 	    => "foodshot_likes",
		"ACTIVE"	    	    => "Y",
		"CREATED_BY"   	    => $USER->GetID(),
		"PROPERTY_ELEMENT" => $idFoodshot,
	);

	$arLikesSelect = array (
		"ID",
		"PROPERTY_LIKE",
	);

	$rsLikesItems = CIBlockElement::GetList(array(), $arLikesFilter, false, false, $arLikesSelect);
	
	// if 'like' exists
	if ($arLikesItem = $rsLikesItems->Fetch()){
		// update PROPERTY_LIKE
		if (strval($arLikesItem["PROPERTY_LIKE_VALUE"]) === "0" && $action == "like"){
			CIBlockElement::SetPropertyValueCode($arLikesItem["ID"], "like", "1");
		}elseif(strval($arLikesItem["PROPERTY_LIKE_VALUE"]) === "1" && $action == "unlike"){
			CIBlockElement::SetPropertyValueCode($arLikesItem["ID"], "like", "0");
		}
	}else{
		// add new foodshot like
		$foodshotLike = new CIBlockElement;

		$arFields = array (
			"IBLOCK_ID"   	  => 29,
			"NAME"		  => "#".$idFoodshot." Foodshot like",
			"CREATED_BY"	  => $USER->GetID(),
			"PROPERTY_VALUES" => array (
				"69" => $idFoodshot,
			),
		);

		if ($action == "like")
			$arFields["PROPERTY_VALUES"]["70"] = "1";
		elseif ($action == "unlike") 
			$arFields["PROPERTY_VALUES"]["70"] = "0";


		//echo "@<pre>";print_r($arFields);echo "</pre>";

		$foodshotLike->Add($arFields);
	}

	// get this foodshot likes count
	$arAllLikesFilter = array (
		"IBLOCK_CODE" 	    => "foodshot_likes",
		"ACTIVE"	    	    => "Y",
		"PROPERTY_ELEMENT" => $idFoodshot,
		"PROPERTY_LIKE"	    => "1",
	);

	$arAllLikesSelect = array (
		"ID",
		"CREATED_BY",
	);

	$rsAllLikesItems = CIBlockElement::GetList(array(), $arAllLikesFilter, false, false, $arAllLikesSelect);
	$allLikesItemsCount = intval($rsAllLikesItems->SelectedRowsCount());

	// cleaning tag cache
	global $CACHE_MANAGER;
	$CACHE_MANAGER->ClearByTag("iblock_id_".$idFoodshot);

	echo $allLikesItemsCount;
}

?>