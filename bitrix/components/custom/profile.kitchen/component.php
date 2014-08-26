<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
//echo "2222222222222222222222222222222".$_REQUEST['u'];
$UserId = $arParams["USER"]["ID"];
$UserKitchen = $arParams["USER"]["UF_KITCHEN"];
$obCache = new CPHPCache;
/*
$cacheid = "profile_".SITE_ID."_".$UserId."_recipes";
if(isset($_REQUEST["PAGEN_1"]) && intval($_REQUEST["PAGEN_1"])){
	$cacheid .= "_".intval($_REQUEST["PAGEN_1"]);
}else{
	$cacheid .= "_1";
}

if($obCache->InitCache(3, $cacheid, "/profile")){
	$arResult = $obCache->GetVars();

}elseif($obCache->StartDataCache()){
    global $CACHE_MANAGER;
	$CACHE_MANAGER->StartTagCache("/profile_".$UserId);
	require_once($_SERVER["DOCUMENT_ROOT"]."/classes/main.class.php");
	$CFClub = new CFClub();
	}*/
	if(CModule::IncludeModule("iblock")){
		//$arRecipe = $CFClub->getList(12, Array("CREATED_BY"=>$UserId), "blogs", "N");		
		//$arKitchensId = array_keys($arRecipe['Kitchen']);
		//$arKitchens = $CFClub->getKitchens(false, $arKitchensId);
		$U=$_REQUEST['u'];
		$pos1=strpos($U, '?');
		if($pos1){$U=substr($U, 0, $pos1);}
		
		$bOwnerPage=false;
		if(intval($_REQUEST["u"]) > 0)
		{
			$U = IntVal($_REQUEST['u']);
			if(CUSer::IsAuthorized() && $USER->GetId()==$U)
			{ 
				$bOwnerPage=true;
			}
		}
		else
		{
			if(CUSer::IsAuthorized()){
				$U = IntVal($USER->GetId());
				$bOwnerPage=true;
			}else{
				LocalRedirect("/auth/?backurl=".$APPLICATION->GetCurPage());
			}	
		}
		
		$arResult['ITEMS']=array();
		$arSelect = Array("ID", "NAME", "PREVIEW_PICTURE", "PROPERTY_users", "PROPERTY_brand", "PROPERTY_cost", "PROPERTY_tech_type");
		$arFilter = Array("IBLOCK_ID"=>33, "PROPERTY_users"=>$U);
					//print_r($arFilter);
			//echo "=========================";
		$res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>50), $arSelect);
		while($ob = $res->GetNextElement())
		{
			$arFields = $ob->GetFields();
			//echo "<h1>-----------------1111111111111111111---------------------</h1>";
			//print_r($arFields);
			//echo "<h1>-----------------2222222222222222222---------------------</h1>";
			$arSelectCom = Array("NAME", "DETAIL_TEXT");
			$arFilterCom = Array("IBLOCK_ID"=>30, "PROPERTY_user"=>$U,"PROPERTY_model"=>$arFields["ID"]);
			$resCom = CIBlockElement::GetList(Array(), $arFilterCom, false, Array("nPageSize"=>50), $arSelectCom);
			while($obCom = $resCom->GetNextElement())
			{
				$arFieldsCom[$arFields['ID']] = $obCom->GetFields();
			}
			

			$arSelectBrand = Array("NAME", "PROPERTY_tech_type", "ID");
			$arFilterBrand = Array("IBLOCK_ID"=>32, "ID"=>$arFields["PROPERTY_BRAND_VALUE"]);
			$resBrand = CIBlockElement::GetList(Array(), $arFilterBrand, false, Array("nPageSize"=>50), $arSelectBrand);
			while($obBrand = $resBrand->GetNextElement())
			{
				$arFieldsBrand[$arFields['ID']] = $obBrand->GetFields();
			}
			

			$arSelectRating = Array("NAME", "PROPERTY_summ", "PROPERTY_count_people", "ID");
			$arFilterRating = Array("IBLOCK_ID"=>34, "PROPERTY_model"=>$arFields["ID"]);
			$resRating = CIBlockElement::GetList(Array(), $arFilterRating, false, Array("nPageSize"=>500), $arSelectRating);
			while($obRating = $resRating->GetNextElement())
			{
				$arFieldsRating[$arFields['ID']] = $obRating->GetFields();
			}
			

			$arSelectTech = Array("NAME", "PROPERTY_tech_type", "ID");
			$arFilterTech = Array("IBLOCK_ID"=>31, "ID"=>$arFields["PROPERTY_TECH_TYPE_VALUE"]);
			//echo "Tech filter: ";print_r($arFilterTech);
			$resTech = CIBlockElement::GetList(Array(), $arFilterTech, false, false, $arSelectTech);
			while($obTech = $resTech->GetNextElement())
			{
				$arFieldsTech[$arFields['ID']] = $obTech->GetFields();
				//echo "<h1>+++++++++++++++++++++++++++++++++++++++</h1>";
				//echo $arFieldsTech[$arFields['ID']]["NAME"];
			}
			
			
			
			
			$arResult['ITEMS'][$arFields["ID"]]["ID"]=$arFields["ID"];
			$arResult['ITEMS'][$arFields["ID"]]["MODEL"]=$arFields["NAME"];
			$arResult['ITEMS'][$arFields["ID"]]["MODEL_ID"]=$arFields["ID"];
			$arResult['ITEMS'][$arFields["ID"]]["PREVIEW_PICTURE"]['SRC']=CFile::GetPath($arFields["PREVIEW_PICTURE"]);
			$arResult['ITEMS'][$arFields["ID"]]["COMMENTS"]=$arFieldsCom[$arFields['ID']]["DETAIL_TEXT"];
			//$arResult['ITEMS'][$arFields["ID"]]["DETAIL_TEXT"]=$arFields["DETAIL_TEXT"];
			$arResult['ITEMS'][$arFields["ID"]]["BRAND"]=$arFieldsBrand[$arFields['ID']]["NAME"];
			$arResult['ITEMS'][$arFields["ID"]]["BRAND_ID"]=$arFieldsBrand[$arFields['ID']]["ID"];
			$arResult['ITEMS'][$arFields["ID"]]["TECH"]=$arFieldsTech[$arFields['ID']]["NAME"];//$arFields['ID']["PROPERTY_tech_type_VALUE"];
			$arResult['ITEMS'][$arFields["ID"]]["TECH_ID"]=$arFieldsTech[$arFields['ID']]["ID"];//$arFields['ID']["PROPERTY_tech_type_VALUE"];
			$arResult['ITEMS'][$arFields["ID"]]["COST"]=$arFields["PROPERTY_COST_VALUE"];
			$arResult['ITEMS'][$arFields["ID"]]["RATING_VAL"]=$arFieldsRating[$arFields['ID']]["PROPERTY_SUMM_VALUE"];
			$arResult['ITEMS'][$arFields["ID"]]["RATING_COL"]=$arFieldsRating[$arFields['ID']]["PROPERTY_COUNT_PEOPLE_VALUE"];
			$arResult['ITEMS'][$arFields["ID"]]["RATING_RESULT"]=round(($arFieldsRating[$arFields['ID']]["PROPERTY_SUMM_VALUE"])/($arFieldsRating[$arFields['ID']]["PROPERTY_COUNT_PEOPLE_VALUE"]));
			echo "<!-----333333333333333333333333333333--------------- ".$arResult['ITEMS'][$arFields["ID"]]["RATING_RESULT"]." ------------------>";
			if($arResult['ITEMS'][$arFields["ID"]]["RATING_RESULT"]>5)$arResult['ITEMS'][$arFields["ID"]]["RATING_RESULT"]=5;
			elseif($arResult['ITEMS'][$arFields["ID"]]["RATING_RESULT"]<0)$arResult['ITEMS'][$arFields["ID"]]["RATING_RESULT"]=0;
			
			
		}
		

		/*
		echo "<BR>22222222222222222222222222";
		echo "<BR>";
		print_r($arResult);
		echo "<BR>22222222222222222222222222";
		echo "<BR>";
		*/
		}
		
		
		
		
		
		
		
		
	//}	
	/*
		echo "<BR>22222222222222222222222222";
		echo "<BR>";
		print_r($arResult);
		echo "<BR>22222222222222222222222222";
		echo "<BR>";*/
/*	$CACHE_MANAGER->RegisterTag("profile_".$UserId."_recipes");	
	$CACHE_MANAGER->EndTagCache();
	$obCache->EndDataCache(
		$arResult	
	);
}else{
	$arResult = array();	
}*/
$arResult['UserKitchen']=$UserKitchen;
$arResult['bOwnerPage']=$bOwnerPage;
$this->IncludeComponentTemplate();
?>