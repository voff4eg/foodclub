<?php
//echo"BEGINNING_REQUEST<br/>";print_r($_REQUEST);echo"<br/>";
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("iblock");

$arIBlock = CIBlock::GetArrayByID(5);

if( $USER->IsAdmin() || $USER->IsAuthorized() ){
foreach(range(0, count($_REQUEST['stage_description'])-1, 1) as $intKey=>$intNumer)
{
    $arProp = Array(); $arId = Array(); $arNumber = Array();
    foreach(range(0, count($_REQUEST['ingredients_' . $intNumer . '_id'])-1, 1) as $intX)
    {
        if(strpos($_REQUEST['ingredients_' . $intNumer . '_number'][$intX],",") === false){}else{
            $_REQUEST['ingredients_' . $intNumer . '_number'][$intX] = str_replace(",",".",$_REQUEST['ingredients_' . $intNumer . '_number'][$intX]);
        }
        if(strpos($_REQUEST['ingredients_' . $intNumer . '_number'][$intX],"/") === false){$fractional = false;}else{
            if(is_numeric(str_replace("/",".",$_REQUEST['ingredients_' . $intNumer . '_number'][$intX])))
                $fractional = true;
        }
        if( intval($_REQUEST['ingredients_' . $intNumer . '_id'][$intX]) > 0 &&
           ((is_numeric($_REQUEST['ingredients_' . $intNumer . '_number'][$intX]) === true) || ($fractional == true)) )
        {
            $arId[]     = $_REQUEST['ingredients_' . $intNumer . '_id'][$intX];
            $arNumber[] = $_REQUEST['ingredients_' . $intNumer . '_number'][$intX];
        }
    }
    
    $arProp = Array("ingredient" => $arId, "numer" => $arNumber);
    
    $arPhoto = Array(
        "name"     => $_FILES["photo"]['name'][$intNumer],
        "type"     => $_FILES["photo"]['type'][$intNumer],
        "tmp_name" => $_FILES["photo"]['tmp_name'][$intNumer],
        "error"    => $_FILES["photo"]['error'][$intNumer],
        "size"     => $_FILES["photo"]['size'][$intNumer]
    );
    
    $arPhoto = CIBlock::ResizePicture($arPhoto, $arIBlock["FIELDS"]["DETAIL_PICTURE"]["DEFAULT_VALUE"]);
	
    $arPreIMAGE 				= $arPhoto;
    $arPreIMAGE["old_file"] 	= "";
    $arPreIMAGE["del"] 			= "N";
    $arPreIMAGE["MODULE_ID"] 	= "iblock";
    
    $arLoadProductArray = Array(
		"IBLOCK_SECTION"  => false,
		"IBLOCK_ID"       => 4,
		"PROPERTY_VALUES" => $arProp,
		"NAME"            => ($_REQUEST['name']." (этап ".($intKey+1).")"),
		"ACTIVE"          => "Y",
		"PREVIEW_TEXT"    => $_REQUEST['stage_description'][$intNumer],
    );
    
    if (strlen($arPreIMAGE["name"]) > 0){
		/*$intPreIMAGE = CFile::SaveFile($arPreIMAGE, "iblock");
		$arLoadProductArray["PREVIEW_PICTURE"] = CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"].CFile::GetPath($intPreIMAGE));*/
		if(copy($arPhoto["tmp_name"], $arPhoto["tmp_name"]."~"))
		{
			$_FILES["STAGE_PREVIEW_PICTURE"][$intNumer] = $arPhoto;
			$_FILES["STAGE_PREVIEW_PICTURE"][$intNumer]["tmp_name"] .= "~";
			
			$arPREVIEW_PICTURE = CIBlock::ResizePicture($_FILES["STAGE_PREVIEW_PICTURE"][$intNumer], $arStageIBlock["FIELDS"]["PREVIEW_PICTURE"]["DEFAULT_VALUE"]);
			if(!is_array($arPREVIEW_PICTURE))
			{
				if($arStageIBlock["FIELDS"]["PREVIEW_PICTURE"]["DEFAULT_VALUE"]["IGNORE_ERRORS"] === "Y")
					$arPREVIEW_PICTURE = $_FILES["STAGE_PREVIEW_PICTURE"][$intNumer];
				else
				{
					$arPREVIEW_PICTURE = array(
						"name" => false,
						"type" => false,
						"tmp_name" => false,
						"error" => 4,
						"size" => 0,
					);
				}
			}
			$arLoadProductArray["PREVIEW_PICTURE"] = $arPREVIEW_PICTURE;
		}else{
			echo "failed to copy ".$arPhoto['tmp_name']."...\n";
		}
		$intPreIMAGE = CFile::SaveFile($arPreIMAGE, "iblock");
		$arLoadProductArray["DETAIL_PICTURE"] = CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"].CFile::GetPath($intPreIMAGE));
	}

	$elStep   = new CIBlockElement;
	$strIntId = $elStep->Add($arLoadProductArray, false, false, true);
	$arLoadProductArray["IBLOCK_ID"] = 23;
	$strIntId_fr = $elStep->Add($arLoadProductArray);
	$arDishStepsId[] = $strIntId;
	$arDishStepsId_fr[] = $strIntId_fr;
}

$cookingtime = $_REQUEST['hours']*60+$_REQUEST['minutes'];
$arProp = Array("kitchen" => $_REQUEST['cooking'],
				"dish_type" => $_REQUEST['dish_type'],
				"recipt_steps" => $arDishStepsId,
				"main_ingredient" => (intval($_REQUEST['main_ingredient_id']) > 0 ? intval($_REQUEST['main_ingredient_id']) : ""),
				//"kcals" => (strlen($_REQUEST['kkal']) > 0 ? $_REQUEST['kkal'] : 0),
				"portion" => (strlen($_REQUEST['yield']) > 0 ? $_REQUEST['yield'] : 0),
				"cooking_time" => (intval($cookingtime) > 0 ? $cookingtime : 0),
				"add_mobile" => ($_REQUEST["add_mobile"] == "on" ? "3" : ""),
				);

$arLoadProductArray = Array(
	"IBLOCK_SECTION"  => false,
	"IBLOCK_ID"       => 5,
	"ACTIVE_FROM"     => date("d.m.Y h:m:s"),
	"PROPERTY_VALUES" => $arProp,
	"NAME"            => $_REQUEST['name'],
	"ACTIVE"          => "Y",
	"PREVIEW_TEXT"    => $_REQUEST['dish_description'],
);
if (strlen($_FILES["general_photo"]["name"]) > 0){
	
	if(copy($_FILES["general_photo"]["tmp_name"], $_FILES["general_photo"]["tmp_name"]."~"))
	{
		$_FILES["PREVIEW_PICTURE"] = $_FILES["general_photo"];
		$_FILES["PREVIEW_PICTURE"]["tmp_name"] .= "~";
		
		$arPREVIEW_PICTURE = CIBlock::ResizePicture($_FILES["PREVIEW_PICTURE"], $arIBlock["FIELDS"]["PREVIEW_PICTURE"]["DEFAULT_VALUE"]);
		if(!is_array($arPREVIEW_PICTURE))
		{
			if($arIBlock["FIELDS"]["PREVIEW_PICTURE"]["DEFAULT_VALUE"]["IGNORE_ERRORS"] === "Y")
				$arPREVIEW_PICTURE = $_FILES["PREVIEW_PICTURE"];
			else
			{
				$arPREVIEW_PICTURE = array(
					"name" => false,
					"type" => false,
					"tmp_name" => false,
					"error" => 4,
					"size" => 0,
				);
			}
		}
	}
	$arDETAIL_PICTURE = CIBlock::ResizePicture($_FILES["general_photo"], $arIBlock["FIELDS"]["DETAIL_PICTURE"]["DEFAULT_VALUE"]);
	
	$arLoadProductArray["PREVIEW_PICTURE"] = $arPREVIEW_PICTURE;
	$arLoadProductArray["DETAIL_PICTURE"] = $arDETAIL_PICTURE;

	if(copy($_FILES["general_photo"]["tmp_name"], $_FILES["general_photo"]["tmp_name"]."_search"))
	{
		$_FILES["SEARCH_PICTURE"] = $_FILES["general_photo"];
		$_FILES["SEARCH_PICTURE"]["tmp_name"] .= "_search";
		
		//$arPREVIEW_PICTURE = CIBlock::ResizePicture($_FILES["SEARCH_PICTURE"], $arIBlock["FIELDS"]["PREVIEW_PICTURE"]["DEFAULT_VALUE"]);
		$arSEARCH_PICTURE = CIBlock::ResizePicture($_FILES["SEARCH_PICTURE"], array(
			"WIDTH" => 50,
			"HEIGHT" => 50,
			"METHOD" => "resample",
		));
		$arLoadProductArray["PROPERTY_VALUES"]["search_pic"] = $arSEARCH_PICTURE;
	}
}


//Проверка на отсутствие рецептов
$user_id = $USER->GetID();
$firstRecipe = false;
$rsUserRecipes = CIBlockElement::GetList(array(),array("IBLOCK_CODE"=>"recipe", "CREATED_BY" => $user_id),false,false,array("ID"));
//echo intval($rsUserRecipes->SelectedRowsCount()); die;
if( intval($rsUserRecipes->SelectedRowsCount()) == 0 ){
	$firstRecipe = true;
}


$elStep   = new CIBlockElement;

if($strIntId = $elStep->Add($arLoadProductArray)){
	//add edit approval date
	$time_deadline = ConvertTimeStamp(time() + 3600*24*3,"FULL");
	CIBlockElement::SetPropertyValues($strIntId, 5, $time_deadline, "edit_deadline");
}
$arLoadProductArray["IBLOCK_ID"] = 24;
$strIntId_fr = $elStep->Add($arLoadProductArray);

//$user_id = $USER->GetID();
require_once($_SERVER['DOCUMENT_ROOT']."/classes/mark.class.php");
$CMark = new CMark;

$CMark->initIblock($strIntId);
$CMark->updateUserRait($user_id,$way = "up","r_create");
//$CMark->like(2253, 8655);

if(!$strIntId){
    echo "Error: ".$elStep->LAST_ERROR;
    //echo"REQUEST<br/>";print_r($_REQUEST);echo"<br/>";
    //echo"<br/>";print_r($arLoadProductArray);echo"<br/>";
    //die;
}


				
foreach($arDishStepsId as $intStepId){
	CIBlockElement::SetPropertyValueCode($intStepId, "parent", $strIntId);
}
foreach($arDishStepsId_fr as $intStepId){
    CIBlockElement::SetPropertyValueCode($intStepId, "parent", $strIntId_fr);
}


	if($strIntId){
		
		//current user badges
		$arUserBadges = array();
		//get First recipe badge id
		$rsFirstRecipeBadge = CIBlockElement::GetList(array(),array("IBLOCK_CODE"=>"badges","CODE"=>"first_recipe"),false,false,array("ID"));
		if($arFirstRecipeBadge = $rsFirstRecipeBadge->Fetch()){

			//get current user badges
			$rsUser = CUser::GetByID($user_id);
			if($arUser = $rsUser->Fetch()){
				$arUserBadges = $arUser["UF_BADGES"];
				$arUserEmail = $arUser["EMAIL"];
			}

			//check first recipe badge in current user badges
			if(!empty($arUserBadges) && !in_array($arFirstRecipeBadge["ID"],$arUserBadges)){

				$arUserBadges[] = $arFirstRecipeBadge["ID"];
				//need to update current user badges and insert first recipe badge
	            $user = new CUser;
				$user->Update($user_id, array("UF_BADGES"=>$arUserBadges));
			}elseif(empty($arUserBadges)){//if no user badges

				$user = new CUser;
				$user->Update($user_id, array("UF_BADGES"=>array($arFirstRecipeBadge["ID"])));
			}
		}
		//var_dump($arUserEmail);
		//die;
		if( $firstRecipe && $arUserEmail ){
			$arEventFields = array(
				"EMAIL_TO" => $arUserEmail,
				);
			CEvent::Send("USER_FIRST_RECIPE", SITE_ID, $arEventFields, "N", 44);
		}

	}


LocalRedirect("/detail/".$strIntId."/");
//echo "<pre>"; print_r($_REQUEST); echo "</pre>";die;
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog.php");
?>
<?} else {
	LocalRedirect("/auth/?backurl=/recipe/add/");
}
?>