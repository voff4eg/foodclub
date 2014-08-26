<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest' && ($_REQUEST["action"] == "like" || $_REQUEST["action"] == "dislike") && intval($_REQUEST["id"]) > 0 && $USER->IsAuthorized())
{
	CModule::IncludeModule("iblock");

	$idComment  = intval($_REQUEST["id"]);
	$action	= trim($_REQUEST["action"]);

	// user can like foodshot
	$bUserCanLikeFoodshot = false;

	// check if user ever liked this foodshot
	$arLikesFilter = array (
		"IBLOCK_CODE" 	    => "likes",
		"ACTIVE"	    	    => "Y",
		"CREATED_BY"   	    => $USER->GetID(),
		"PROPERTY_ELEMENT" => $idComment,
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
		}elseif(strval($arLikesItem["PROPERTY_LIKE_VALUE"]) === "1" && $action == "dislike"){
			CIBlockElement::SetPropertyValueCode($arLikesItem["ID"], "like", "0");
		}
	}else{
		// add new foodshot like
		$foodshotLike = new CIBlockElement;

		$arFields = array (
			"IBLOCK_ID"   	  => 35,
			"NAME"		  => "#".$idComment." Comment like",
			"CREATED_BY"	  => $USER->GetID(),
			"PROPERTY_VALUES" => array (
				"90" => $idComment,
			),
		);

		if ($action == "like")
			$arFields["PROPERTY_VALUES"]["91"] = "1";
		elseif ($action == "dislike") 
			$arFields["PROPERTY_VALUES"]["91"] = "0";

		$foodshotLike->Add($arFields);
	}

	// get this foodshot likes count
	$arAllLikesFilter = array (
		"IBLOCK_CODE" 	    => "likes",
		"ACTIVE"	    	    => "Y",
		"PROPERTY_ELEMENT" => $idComment,
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
	$CACHE_MANAGER->ClearByTag("iblock_id_".$idComment);

	$rsComment = CIBlockElement::GetList(array(),array("IBLOCK_ID" => 6,"ID" => $idComment),false,false,array("PROPERTY_recipe"));
	if($arComment = $rsComment->Fetch()){
		$CACHE_MANAGER->ClearByTag("recipe_comments#".$arComment["PROPERTY_RECIPE_VALUE"]);
		$CACHE_MANAGER->ClearByTag("recipe_comments#".$arComment["PROPERTY_RECIPE_VALUE"]."_comment#".$idComment);
	}

	echo ($allLikesItemsCount >= 0 ? $allLikesItemsCount : 0);
}else{
	echo 0;
}
?>