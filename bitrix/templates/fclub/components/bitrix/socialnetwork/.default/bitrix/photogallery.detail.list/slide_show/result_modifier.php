<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arParams["DETAIL_URL_FOR_JS"] = CComponentEngine::MakePathFromTemplate($arParams["~DETAIL_URL"], 
				array(
					"USER_ALIAS" => $arParams["USER_ALIAS"], 
					"SECTION_ID" => $arParams["SECTION_ID"], 
					"ELEMENT_ID" =>'#element_id#'));

if (empty($arParams["BACK_URL"]) && !empty($_REQUEST["BACK_URL"]))
	$arParams["BACK_URL"] = $_REQUEST["BACK_URL"];

$arResult["ELEMENT_FOR_JS"] = array();
foreach ($arResult["ELEMENTS_LIST"]	as $key => $arElement):
	$res = array(
		"id" => intVal($arElement["ID"]),
		"url" => $arElement["~URL"],
		"src" => $arElement["PICTURE"]["SRC"],
		"width" => $arElement["PICTURE"]["WIDTH"],
		"height" => $arElement["PICTURE"]["HEIGHT"],
		"alt" => $arElement["CODE"],
		"title" => $arElement["NAME"]);
	$arResult["ELEMENT_FOR_JS"][] = $res;
endforeach;
?>