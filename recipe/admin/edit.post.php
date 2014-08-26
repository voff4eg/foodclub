<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
CModule::IncludeModule("iblock");

$rsMain = CIBlockElement::GetList(Array(), Array("ID" => IntVal($_REQUEST['recipe_id'])), false, false);
if($obMain = $rsMain->GetNextElement()){
	$MainField = $obMain->GetFields();
	$MainProperties = $obMain->GetProperties();
	
	foreach($MainProperties as $Key => $Properti){
		$arProperty[$Key] = $Properti['VALUE'];
	}
} else {
	echo "error";
	die();
}
/*
 * TODO необходимо проверять права на запись каждого этапа и рецепта в целом
 */
$StageCount = 1;
foreach($_REQUEST['stage_description'] as $Key=>$Description){
	if( strpos($Key, "st_") !== false ){
		$StageId = intval( str_replace("st_","",$Key) );
		$intNumer = array_search( strval($StageId), $_REQUEST['stage_id'] );

		if( $intNumer !== false ){
			/*
			 * Выборка ингридиентов из данных запроса
			 */
			
			$arProp = Array(); $arId = Array(); $arNumber = Array();
			//if(isset($_REQUEST['ingredients_' . $intNumer . '_id'])){
			    foreach(range(0, count($_REQUEST['ingredients_' . $intNumer . '_id'])-1, 1) as $intX)
			    {
			        $arId[]     = $_REQUEST['ingredients_' . $intNumer . '_id'][$intX];
			        $arNumber[] = $_REQUEST['ingredients_' . $intNumer . '_number'][$intX];
			    }
			//}
		    $arProp = Array("ingredient" => $arId, "numer" => $arNumber);
		    
		    /*
		     * Базовые настройки
		     */
		    $arLoadProductArray = Array(
		    	"MODIFIED_BY"     => $USER->GetID(),
				"IBLOCK_SECTION"  => false,
				"IBLOCK_ID"       => 4,
				"PROPERTY_VALUES" => $arProp,
				"NAME"            => ($_REQUEST['name']." (этап ".($StageCount++).")"),
				"ACTIVE"          => "Y",
				"PREVIEW_TEXT"    => $_REQUEST['stage_description'][$Key],
		    );
		    
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
		    //echo "<pre>"; print_r($arLoadProductArray); echo "</pre>";
		    
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
		        $arId[]     = $_REQUEST['ingredients_' . $intNumer . '_id'][$intX];
		        $arNumber[] = $_REQUEST['ingredients_' . $intNumer . '_number'][$intX];
		    }
		}
	    $arProp = Array("ingredient" => $arId, "numer" => $arNumber);
	    
		/*
	     * Базовые настройки
	     */
	    $arLoadProductArray = Array(
			"IBLOCK_SECTION"  => false,
			"IBLOCK_ID"       => 4,
			"PROPERTY_VALUES" => $arProp,
			"NAME"            => ($_REQUEST['name']." (этап ".($StageCount++).")"),
			"ACTIVE"          => "Y",
			"PREVIEW_TEXT"    => $_REQUEST['stage_description'][$Key],
	    );
	    
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
		
	    //echo "<pre>"; print_r($arLoadProductArray); echo "</pre>";
	    
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
$arProperty["kitchen"] = $_REQUEST['cooking'];
$arProperty["dish_type"] = $_REQUEST['dish_type']; 
$arProperty["recipt_steps"] = $arDishStepsId;

$arLoadProductArray = Array(
	"MODIFIED_BY"     => $USER->GetID(),
	"IBLOCK_SECTION"  => false,
	"IBLOCK_ID"       => 5,
	"ACTIVE_FROM"     => $_REQUEST['active_from'],
	"PROPERTY_VALUES" => $arProperty,
	"NAME"            => $_REQUEST['name'],
	"ACTIVE"          => "Y",
	"PREVIEW_TEXT"    => $_REQUEST['dish_description'],
);

/*
 * Фотография рецепта
 */
if($_FILES["general_photo"]['error'] == 0){
	$arPreIMAGE 				= $_FILES['general_photo'];
	$arPreIMAGE["old_file"] 	= "";
	$arPreIMAGE["del"] 			= "N";
	$arPreIMAGE["MODULE_ID"] 	= "iblock";
	$intPreIMAGE = CFile::SaveFile($arPreIMAGE, "iblock");
	$arLoadProductArray["PREVIEW_PICTURE"] = CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"].CFile::GetPath($intPreIMAGE));
} else {
	$arLoadProductArray["PREVIEW_PICTURE"] = CFile::MakeFileArray(CFile::GetPath($MainField['PREVIEW_PICTURE']));
}

//echo "<pre>"; print_r($arLoadProductArray); echo "</pre>";
//die;

$elStep   = new CIBlockElement;
$elStep->Update( IntVal($_REQUEST['recipe_id']) , $arLoadProductArray);
//echo "Error: ".$elStep->LAST_ERROR;

LocalRedirect("/detail/".$_REQUEST['recipe_id']."/");
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
