<?
//echo "<pre>"; print_r($_REQUEST); echo "</pre>"; die;
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
CModule::IncludeModule("iblock");
global $USER;
if(SITE_ID == "s1"){
    $rsMain = CIBlockElement::GetList(array("sort"=>"asc"), array("IBLOCK_ID"=>5, "ID" => intval($_REQUEST['recipe_id'])), false, false);
}else{
    $rsMain = CIBlockElement::GetList(array("sort"=>"asc"), array("IBLOCK_ID"=>24, "ID" => intval($_REQUEST['recipe_id'])), false, false);
}
if($obMain = $rsMain->GetNextElement()){
	$MainField = $obMain->GetFields();
	$MainProperties = $obMain->GetProperties();
	
	//if(!($USER->IsAdmin()) && (MakeTimeStamp($MainField["DATE_CREATE"]) <= (time() - 3600*24*3))){
	if(!($USER->IsAdmin()) && (MakeTimeStamp($MainProperties["edit_deadline"]["VALUE"]) < time())){
		/*LocalRedirect("/recipe/edit/".$MainField["ID"]."/");*/
		LocalRedirect("/detail/".$MainField["ID"]."/?cant_edit");
	}

	foreach($MainProperties as $Key => $Properti){
		$arProperty[$Key] = $Properti['VALUE'];
	}
} else {
	echo "error";
	die();
}
$is_lib = $arProperty["lib"];
$arLikeRecipies = $arProperty["block_like"];
$add_mobile = $arProperty["add_mobile"];
/*
 * TODO необходимо проверять права на запись каждого этапа и рецепта в целом
 */
$StageCount = 1;

if(SITE_ID == "s1"){
    $arIBlock = CIBlock::GetArrayByID(5);
}elseif(SITE_ID == "fr"){
    $arIBlock = CIBlock::GetArrayByID(24);
}

foreach($_REQUEST['stage_description'] as $Key=>$Description){
	if( strpos($Key, "st_") !== false ){
		$StageId = intval( str_replace("st_","",$Key) );
		$intNumer = array_search( strval($StageId), $_REQUEST['stage_id'] );

		if( $intNumer !== false ){
			/*
			 * Выборка ингридиентов из данных запроса
			 */
			//echo"<br/>";print_r($_REQUEST);echo"<br/>";die;
			$arProp = Array(); $arId = Array(); $arNumber = Array();
			//if(isset($_REQUEST['ingredients_' . $intNumer . '_id'])){
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
			//}
		    $arProp = Array("ingredient" => $arId, "numer" => $arNumber, "parent" => $_REQUEST['recipe_id']);
		    
		    /*
		     * Базовые настройки
		     */
		    $arLoadProductArray = Array(
		    	"MODIFIED_BY"     => $USER->GetID(),
				"IBLOCK_SECTION"  => false,
				"PROPERTY_VALUES" => $arProp,
				"NAME"            => ($_REQUEST['name']." (этап ".($StageCount++).")"),
				"ACTIVE"          => "Y",
				"PREVIEW_TEXT"    => $_REQUEST['stage_description'][$Key],
		    );
		    if(SITE_ID == "s1"){
		        $arLoadProductArray["IBLOCK_ID"] = 4;
		    }elseif(SITE_ID == "fr"){
			$arLoadProductArray["IBLOCK_ID"] = 23;
		    }
		    
		    /*
		     * Фотография этапа
		     */
		    if($_FILES["photo"]['error'][ $Key ] == 0){
				
		    	$arPhoto = Array(
			        "name"     => $_FILES["photo"]['name'][$Key],
			        "type"     => $_FILES["photo"]['type'][$Key],
			        "tmp_name" => $_FILES["photo"]['tmp_name'][$Key],
			        "error"    => $_FILES["photo"]['error'][$Key],
			        "size"     => $_FILES["photo"]['size'][$Key]
			    );
				
				$arPhoto = CIBlock::ResizePicture($arPhoto, $arIBlock["FIELDS"]["DETAIL_PICTURE"]["DEFAULT_VALUE"]);
			    
				$arPreIMAGE 				= $arPhoto;
				$arPreIMAGE["old_file"] 	= "";
				$arPreIMAGE["del"] 			= "N";
				$arPreIMAGE["MODULE_ID"] 	= "iblock";
				
			    if (strlen($arPreIMAGE["name"]) > 0){
					$intPreIMAGE = CFile::SaveFile($arPreIMAGE, "iblock");
					$arLoadProductArray["PREVIEW_PICTURE"] = CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"].CFile::GetPath($intPreIMAGE));
				}
		    } elseif(intval($_REQUEST['stage_photo'][$Key]) > 0) {
		    	$arLoadProductArray["PREVIEW_PICTURE"] = CFile::MakeFileArray(CFile::GetPath($_REQUEST['stage_photo'][$Key]));
		    }
		    
		    /*
		     * Обновление этапа
		     */
		    
		    $elStep   = new CIBlockElement;
			$elStep->Update($StageId, $arLoadProductArray);
			
			$arDishStepsId[] = $StageId;
			
		}
	} else {
		// этап необходимо создавать
		$intNumer = count($_REQUEST['stage_id'])+intval($Key);
		
		$arProp = Array(); $arId = Array(); $arNumber = Array();
		if(isset($_REQUEST['ingredients_' . $intNumer . '_id'])){
		    foreach(range(0, count($_REQUEST['ingredients_' . $intNumer . '_id'])-1, 1) as $intX)
		    {
		        if( intval($_REQUEST['ingredients_' . $intNumer . '_id'][$intX]) > 0 &&
		            intval($_REQUEST['ingredients_' . $intNumer . '_number'][$intX]) > 0 )
		        {
		            $arId[]     = $_REQUEST['ingredients_' . $intNumer . '_id'][$intX];
		            $arNumber[] = $_REQUEST['ingredients_' . $intNumer . '_number'][$intX];
		        }
		    }
		}
	    $arProp = Array("ingredient" => $arId, "numer" => $arNumber, "parent" => $_REQUEST['recipe_id']);
	    
		/*
	     * Базовые настройки
	     */
	    $arLoadProductArray = Array(
			"IBLOCK_SECTION"  => false,
			"PROPERTY_VALUES" => $arProp,
			"NAME"            => ($_REQUEST['name']." (этап ".($StageCount++).")"),
			"ACTIVE"          => "Y",
			"PREVIEW_TEXT"    => $_REQUEST['stage_description'][$Key],
	    );
	    
	    if(SITE_ID == "s1"){
	        $arLoadProductArray["IBLOCK_ID"] = 4;
	    }elseif(SITE_ID == "fr"){
		$arLoadProductArray["IBLOCK_ID"] = 23;
	    }
	    
	    /*
	     * Фотография этапа
	     */
	    if($_FILES["photo"]['error'][ $Key ] == 0){
			
	    	$arPhoto = Array(
		        "name"     => $_FILES["photo"]['name'][$Key],
		        "type"     => $_FILES["photo"]['type'][$Key],
		        "tmp_name" => $_FILES["photo"]['tmp_name'][$Key],
		        "error"    => $_FILES["photo"]['error'][$Key],
		        "size"     => $_FILES["photo"]['size'][$Key]
		    );
		    
			$arPhoto = CIBlock::ResizePicture($arPhoto, $arIBlock["FIELDS"]["DETAIL_PICTURE"]["DEFAULT_VALUE"]);
			
			$arPreIMAGE 				= $arPhoto;
			$arPreIMAGE["old_file"] 	= "";
			$arPreIMAGE["del"] 			= "N";
			$arPreIMAGE["MODULE_ID"] 	= "iblock";
			
		    if (strlen($arPreIMAGE["name"]) > 0){
				$intPreIMAGE = CFile::SaveFile($arPreIMAGE, "iblock");
				$arLoadProductArray["PREVIEW_PICTURE"] = CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"].CFile::GetPath($intPreIMAGE));
			}
	    }
	    
		/*
	     * Обновление этапа
	     */
		$elStep   = new CIBlockElement;
		$strIntId = $elStep->Add($arLoadProductArray);
		$arDishStepsId[] = $strIntId;
	}
}

foreach($MainProperties['recipt_steps']['VALUE'] as $RowStep){
	if(!in_array($RowStep, $arDishStepsId)){
		CIBlockElement::Delete($RowStep);
		var_dump($RowStep);
	}
}

/*
 * Свойства рецепта
 */
$cookingtime = $_REQUEST['hours']*60+$_REQUEST['minutes'];
$arProperty["main_ingredient"] = (intval($_REQUEST['main_ingredient_id']) > 0 ? intval($_REQUEST['main_ingredient_id']) : "");
//$arProperty["kcals"] = (strlen($_REQUEST['kkal']) > 0 ? $_REQUEST['kkal'] : 0);
$arProperty["portion"] = (strlen($_REQUEST['yield']) > 0 ? $_REQUEST['yield'] : 0);
$arProperty["cooking_time"] = (intval($cookingtime) > 0 ? $cookingtime : 0);

$arProperty["kitchen"] = $_REQUEST['cooking'];
$arProperty["dish_type"] = $_REQUEST['dish_type']; 
$arProperty["recipt_steps"] = $arDishStepsId;
$arProperty["lib"] = $is_lib;
$arProperty["block_like"] = $arLikeRecipies;
if(strlen($add_mobile) > 0)
	$arProperty["add_mobile"] = "3";


$arLoadProductArray = Array(
	"MODIFIED_BY"     => $USER->GetID(),
	"IBLOCK_SECTION"  => false,
	"ACTIVE_FROM"     => $_REQUEST['active_from'],
	"PROPERTY_VALUES" => $arProperty,
	"NAME"            => $_REQUEST['name'],
	"ACTIVE"          => "Y",
	"PREVIEW_TEXT"    => $_REQUEST['dish_description'],
);

if(SITE_ID == "s1"){
    $arLoadProductArray["IBLOCK_ID"] = 5;
}elseif(SITE_ID == "fr"){
    $arLoadProductArray["IBLOCK_ID"] = 24;
}

/*
 * Фотография рецепта
 */
if($_FILES["general_photo"]['error'] == 0){
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

	/*$arNewFile = CIBlock::ResizePicture($arPREVIEW_PICTURE, array(
		"WIDTH" => 50,
		"HEIGHT" => 50,
		"METHOD" => "resample",
	));*/
	//if(is_array($arNewFile) && !empty($arNewFile)){
		//$arLoadProductArray["PROPERTY_VALUES"]["search_pic"] = $arNewFile;
		
	//}
	
	//$arLoadProductArray["PREVIEW_PICTURE"] = CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"].CFile::GetPath($intPreIMAGE));
	
} else {
	
	$arLoadProductArray["PREVIEW_PICTURE"] = CFile::MakeFileArray(CFile::GetPath($MainField['PREVIEW_PICTURE']));

	$db_props = CIBlockElement::GetProperty(5, $arFields["ID"], array("sort" => "asc"), Array("CODE"=>"search_pic"));
	if($ar_props = $db_props->Fetch()){
		$arLoadProductArray["PROPERTY_VALUES"]["search_pic"] = $ar_props["VALUE"];
	}
	
}

$elStep   = new CIBlockElement;
$elStep->Update( IntVal($_REQUEST['recipe_id']) , $arLoadProductArray);
//echo "Error: ".$elStep->LAST_ERROR;
LocalRedirect(SITE_DIR."detail/".$_REQUEST['recipe_id']."/");
/*
if( $USER->IsAdmin() || in_array(5, $USER->GetParam("GROUPS")) ){
foreach(range(0, count($_REQUEST['stage_description'])-1, 1) as $intKey=>$intNumer)
{
    $arProp = Array(); $arId = Array(); $arNumber = Array();
    foreach(range(0, count($_REQUEST['ingredients_' . $intNumer . '_id'])-1, 1) as $intX)
    {
        $arId[]     = $_REQUEST['ingredients_' . $intNumer . '_id'][$intX];
        $arNumber[] = $_REQUEST['ingredients_' . $intNumer . '_number'][$intX];
    }
    
    $arProp = Array("ingredient" => $arId, "numer" => $arNumber);
    
    $arLoadProductArray = Array(
		"IBLOCK_SECTION"  => false,
		"IBLOCK_ID"       => 4,
		"PROPERTY_VALUES" => $arProp,
		"NAME"            => ($_REQUEST['name']." (этап ".($intKey+1).")"),
		"ACTIVE"          => "Y",
		"PREVIEW_TEXT"    => $_REQUEST['stage_description'][$intNumer],
    );
    
    if($_FILES["photo"]['error'][$intNumer] == 0){
	    $arPhoto = Array(
	        "name"     => $_FILES["photo"]['name'][$intNumer],
	        "type"     => $_FILES["photo"]['type'][$intNumer],
	        "tmp_name" => $_FILES["photo"]['tmp_name'][$intNumer],
	        "error"    => $_FILES["photo"]['error'][$intNumer],
	        "size"     => $_FILES["photo"]['size'][$intNumer]
	    );
	    
		$arPreIMAGE 				= $arPhoto;
		$arPreIMAGE["old_file"] 	= "";
		$arPreIMAGE["del"] 			= "N";
		$arPreIMAGE["MODULE_ID"] 	= "iblock";
		
	    if (strlen($arPreIMAGE["name"]) > 0){
			$intPreIMAGE = CFile::SaveFile($arPreIMAGE, "iblock");
			$arLoadProductArray["PREVIEW_PICTURE"] = CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"].CFile::GetPath($intPreIMAGE));
		}
    } elseif(intval($_REQUEST['stage_photo'][$intNumer]) > 0) {
    	$arLoadProductArray["PREVIEW_PICTURE"] = CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"].CFile::GetPath(intval($_REQUEST['stage_photo'][$intNumer])));
    }
    
    $elStep   = new CIBlockElement;
	$strIntId = $elStep->Add($arLoadProductArray);
	$arDishStepsId[] = $strIntId;
}

$rsGladiators = CIBlockElement::GetList(Array(), Array("IBLOCK_ID"=>4, "ACTIVE"=>"Y", "PROPERTY_PARENT"=>$_REQUEST['recipe_id']), false, false, Array("ID"));

while($arItem = $rsGladiators->GetNext()){
	if(!CIBlockElement::Delete($arItem['ID']))
	{
		$strWarning .= 'Error!';
	}
}

$arPreIMAGE 				= $_FILES['general_photo'];
$arPreIMAGE["old_file"] 	= "";
$arPreIMAGE["del"] 			= "N";
$arPreIMAGE["MODULE_ID"] 	= "iblock";

$arProp = Array("kitchen" => $_REQUEST['cooking'], "dish_type" => $_REQUEST['dish_type'], "recipt_steps" => $arDishStepsId, "comment_count"=>IntVal($_REQUEST['comment_count']));

$arLoadProductArray = Array(
	"IBLOCK_SECTION"  => false,
	"IBLOCK_ID"       => 5,
	"ACTIVE_FROM"     => $_REQUEST['active_from'],
	"PROPERTY_VALUES" => $arProp,
	"NAME"            => $_REQUEST['name'],
	"ACTIVE"          => "Y",
	"PREVIEW_TEXT"    => $_REQUEST['dish_description'],
);
if (strlen($arPreIMAGE["name"]) > 0){
	$intPreIMAGE = CFile::SaveFile($arPreIMAGE, "iblock");
	$arLoadProductArray["PREVIEW_PICTURE"] = CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"].CFile::GetPath($intPreIMAGE));
}
	
$elStep   = new CIBlockElement;
$strIntId = $elStep->Update($_REQUEST['recipe_id'], $arLoadProductArray);

foreach($arDishStepsId as $intStepId){
	CIBlockElement::SetPropertyValueCode($intStepId, "parent", $_REQUEST['recipe_id']);
}
LocalRedirect("/detail/".$_REQUEST['recipe_id']."/");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
<?} else {
	LocalRedirect("/auth/?backurl=/admin/edit/".$_REQUEST['r']."/");
}
*/
?>
