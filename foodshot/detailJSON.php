<?if(intval($_REQUEST["id"]) > 0){
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
	$APPLICATION->RestartBuffer();
	$ID = intval($_REQUEST["id"]);
	require_once($_SERVER['DOCUMENT_ROOT']."/classes/foodshot.class.php");
	$CFooshot = CFoodshot::getInstance();
	$resultArray = array();
	if($arFoodshot = $CFooshot->getByID($ID)){
		$resultArray = $arFoodshot;
	}
	echo json_encode($resultArray);
}
?>