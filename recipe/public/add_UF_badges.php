<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("iblock");


	$newBadgeID = "46343";

	$arFilter = array(
		"IBLOCK_CODE" => "recipe",
		"ACTIVE" => "Y",

	);

	$arrFilter = array(
		">=PROPERTY_cooking_time" => 5,
		"<=PROPERTY_cooking_time" => 30,
	);

	$arUserIDs = array();

	$dbList = CIBlockElement::GetList(array(), array_merge($arFilter, $arrFilter), false, false, array("ID","CREATED_BY"));
	while($arRecipe = $dbList->GetNext()){ 
		$arRecipeIDs[] = $arRecipe["ID"];
		if(!in_array($arRecipe["CREATED_BY"],$arUserIDs))
			$arUserIDs[] = $arRecipe["CREATED_BY"];
	}

	$arFilter["ID"] = $arRecipeIDs;

	if(!empty($arUserIDs)){
		//echo "@".count($arUserIDs)."@";
		$arUserBadges = array();
		$rsUsers = CUser::GetList($a,$b,array("ID" => implode(" | ",$arUserIDs), "!=UF_BADGES" => $newBadgeID),array("SELECT"=>array("UF_BADGES")));
		echo $rsUsers->SelectedRowsCount();
		while($arUser = $rsUsers->Fetch()){
			$arUserBadges[ $arUser["ID"] ] = $arUser["UF_BADGES"];	//Получаем имеющиеся у юзера бейджики
			$arUserEmail[] = $arUser["EMAIL"];
			

		}			

	}
	//echo "<pre>";print_r(array_keys($arUserBadges));echo "</pre>";die;
	//echo count(array_keys($arUserBadges));
	if($arUserIDs){
		foreach($arUserIDs as $userID){			

			if(!empty($arUserBadges[ $userID ])){
				if(!in_array($newBadgeID, $arUserBadges[ $userID ]))
					$arUserBadges[ $userID ][] = $newBadgeID;
			}else{
				$arUserBadges[ $userID ] = array($newBadgeID);
			}
			
			/*$user = new CUser;
			$user->Update($userID, array("UF_BADGES"=>$arUserBadges[ $userID ]));*/
			
		}

	}

	$arUserEmail[] = "dp@twinpx.ru";

	//CEvent::Send("USER_FAST_FOOD", SITE_ID, array("EMAIL_TO" => implode(",", $arUserEmail)), "N", "");



echo "<pre>";print_r($arUserEmail);echo "</pre>";die;

	

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog.php");
?>