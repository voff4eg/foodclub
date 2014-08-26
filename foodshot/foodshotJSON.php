<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$page = intval($_REQUEST["page"]);
$profileOwnerId = intval($_REQUEST["profileOwnerId"]);
$page_size = 50;
if(CUser::IsAuthorized()){
	$uid = CUser::GetID();
}else{
	$uid = 0;
}
$cache_id = "foodshot_pid".$page."_ps".$page_size."_uid".$uid."_pid".$profileOwnerId;
$cache_dir = "/foodshot";

$obCache = new CPHPCache;
if($obCache->InitCache(36000, $cache_id, $cache_dir)){
	$resultArray = $obCache->GetVars();
}elseif(CModule::IncludeModule("iblock") && $obCache->StartDataCache()){

	global $CACHE_MANAGER;
	$CACHE_MANAGER->StartTagCache($cache_dir);

	$resultArray = array();
	$arFilter = array(
		"IBLOCK_CODE"=>"foodshot_elements",
		"ACTIVE" => "Y",
	);
	if($profileOwnerId > 0) $arFilter["CREATED_BY"] = $profileOwnerId;
	$rsFoodshots = CIBlockElement::GetList(array("DATE_CREATE"=>"DESC"),$arFilter,false,array("nPageSize"=>$page_size,"iNumPage"=>$page),array("ID","NAME","CREATED_BY","PREVIEW_PICTURE","PREVIEW_TEXT","PROPERTY_comments_count","PROPERTY_likes_count","PROPERTY_www"));
	$intFoodshotCount = $rsFoodshots->SelectedRowsCount();
	$resultArray["pages"] = intval($intFoodshotCount/$page_size);
	if(($intFoodshotCount % $page_size) > 0){
		$resultArray["pages"]++;
	}	
	while($arFoodshot = $rsFoodshots->GetNext()){
		$CACHE_MANAGER->RegisterTag("iblock_id_".$arFoodshot["ID"]);

		$Foodshot = array();
		$Foodshot["id"] = $arFoodshot["ID"];
		$Foodshot["href"] = "/foodshot/".$arFoodshot["ID"]."/#!foodshot";
		if(intval($arFoodshot["PREVIEW_PICTURE"]) > 0){
			$photo = CFile::GetByID($arFoodshot["PREVIEW_PICTURE"])->Fetch();
			if(!empty($photo)){
				$Foodshot["image"] = array(
					"src" => CFile::GetPath($photo["ID"]),
					"width" => $photo["WIDTH"],
					"height" => $photo["HEIGHT"],
				);
			}
			unset($photo);
		}
		$Foodshot["name"] = $arFoodshot["NAME"];
		$Foodshot["text"] = $arFoodshot["PREVIEW_TEXT"];
		if(intval($arFoodshot["CREATED_BY"]) > 0){
			$author = CUser::GetByID($arFoodshot["CREATED_BY"])->Fetch();
			//echo "author<pre>";print_r($author);echo "</pre>";die;
			if(!empty($author)){
				$Foodshot["author"] = array(
					"id" => $author["ID"],
					"href" => "/profile/".$author["ID"]."/",
					"name" => (strlen($author["NAME"]) > 0 && strlen($author["LAST_NAME"]) > 0 ? $author["NAME"]." ".$author["LAST_NAME"]:$author["LOGIN"]),
				);
				if(intval($author["PERSONAL_PHOTO"]) > 0){
					$author_photo = CFile::GetByID($author["PERSONAL_PHOTO"])->Fetch();
					if(!empty($author_photo)){
						$Foodshot["author"]["src"] = CFile::GetPath($author_photo["ID"]);
					}
				}else{
					$Foodshot["author"]["src"] = "/images/avatar/avatar.jpg";
				}
			}
		}
		$Foodshot["comments"] = array();
		if(intval($arFoodshot["PROPERTY_COMMENTS_COUNT_VALUE"]) > 0){
			$Foodshot["comments"]["num"] = $arFoodshot["PROPERTY_COMMENTS_COUNT_VALUE"];
		}else{
			$Foodshot["comments"]["num"] = "";
		}
		$Foodshot["comments"]["visible"] = array();
		$rsAllComments = CIBlockElement::GetList(array("DATE_CREATE" => "ASC"),array("IBLOCK_CODE"=>"foodshot_comments","PROPERTY_element"=>$arFoodshot["ID"]),false,false,array("ID"));
		$commentsCount = intval($rsAllComments->SelectedRowsCount());
		$Foodshot["comments"]["num"] = $commentsCount;

		$rsComments = CIBlockElement::GetList(array("DATE_CREATE" => "DESC"),array("IBLOCK_CODE"=>"foodshot_comments","PROPERTY_element"=>$arFoodshot["ID"]),false,array("nTopCount"=>3),array("ID","CREATED_BY","NAME","PREVIEW_TEXT"));
		while($arComment = $rsComments->GetNext()){
			if(intval($arComment["CREATED_BY"]) > 0){
				if(!in_array($arComment["CREATED_BY"],$arRequestedUsers)){
					$comment_author = CUser::GetByID($arComment["CREATED_BY"])->Fetch();
					if(!empty($comment_author)){
						$arRequestedUsers[ $comment_author["ID"] ] = $comment_author;
						if(intval($comment_author["PERSONAL_PHOTO"]) > 0){
							$comment_author_photo = CFile::GetByID($comment_author["PERSONAL_PHOTO"])->Fetch();
							if(!empty($comment_author_photo)){
								$arRequestedUsers[ $comment_author["ID"] ]["PERSONAL_PHOTO_ARRAY"] = $comment_author_photo;
								$arRequestedUsers[ $comment_author["ID"] ]["PERSONAL_PHOTO_ARRAY"]["SRC"] = CFile::GetPath($arRequestedUsers[ $arComment["CREATED_BY"] ]["PERSONAL_PHOTO_ARRAY"]["ID"]);
							}
						}else{
							$arRequestedUsers[ $comment_author["ID"] ]["PERSONAL_PHOTO_ARRAY"]["SRC"] = "/images/avatar/avatar.jpg";
						}
					}
				}
				$Foodshot["comments"]["visible"][] = array(
					"id" => $arComment["ID"],
					"author" => array(
						"id" => $arRequestedUsers[ $arComment["CREATED_BY"] ]["ID"],
						"href" => "/profile/".$arRequestedUsers[ $arComment["CREATED_BY"] ]["ID"]."/",
						"src" => $arRequestedUsers[ $arComment["CREATED_BY"] ]["PERSONAL_PHOTO_ARRAY"]["SRC"],
						"name" => (strlen($arRequestedUsers[ $arComment["CREATED_BY"] ]["NAME"]) > 0 && strlen($arRequestedUsers[ $arComment["CREATED_BY"] ]["LAST_NAME"]) > 0 ? $arRequestedUsers[ $arComment["CREATED_BY"] ]["NAME"]." ".$arRequestedUsers[ $arComment["CREATED_BY"] ]["LAST_NAME"]:$arRequestedUsers[ $arComment["CREATED_BY"] ]["LOGIN"]),
					),
					"text" => $arComment["PREVIEW_TEXT"]
				);
			}
		}
		$Foodshot["comments"]["visible"] = array_reverse($Foodshot["comments"]["visible"]);

		// likes count
		$arAllLikesFilter = array (
			"IBLOCK_CODE" 	    => "foodshot_likes",
			"ACTIVE" => "Y",
			"PROPERTY_ELEMENT" => $arFoodshot["ID"],
			"PROPERTY_LIKE"	    => "1",
		);

		$arAllLikesSelect = array (
			"ID",
			"CREATED_BY",
		);

		$rsAllLikesItems = CIBlockElement::GetList(array(), $arAllLikesFilter, false, false, $arAllLikesSelect);
		$Foodshot["likeNum"] = intval($rsAllLikesItems->SelectedRowsCount());

		while($arAllLikesItems = $rsAllLikesItems->Fetch()){
			if ($USER->IsAuthorized() && intval($USER->GetID()) === intval($arAllLikesItems["CREATED_BY"]))
				$Foodshot["user_liked"] = "yes";			
		}	
		
		if(trim($Foodshot["name"])!="" && trim($Foodshot["text"])!="" && trim($Foodshot["author"]["name"])!="" && trim($Foodshot["href"])!="" && trim($Foodshot["image"]["src"]) ){
			$resultArray["elems"][] = $Foodshot;
		}
	}

	$CACHE_MANAGER->RegisterTag("iblock_id_new");
	$CACHE_MANAGER->EndTagCache();

	$obCache->EndDataCache($resultArray);
}else{
	$resultArray = array();
}
//$APPLICATION->RestartBuffer();
//echo "<pre>";print_r($resultArray);echo "</pre>";die;
echo json_encode($resultArray);
?>