<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arParams["ELEMENT_ID"] = intVal($arParams["ELEMENT_ID"]); // active element
$arParams["SLIDER_COUNT_CELL"] = (intVal($arParams["SLIDER_COUNT_CELL"]) <= 0 ? 4 : $arParams["SLIDER_COUNT_CELL"]);

$temp = array("STRING" => preg_replace("/[^0-9]/is", "/", $arParams["THUMBS_SIZE"]));
list($temp["WIDTH"], $temp["HEIGHT"]) = explode("/", $temp["STRING"]);
$arParams["THUMBS_SIZE"] = (intVal($temp["WIDTH"]) > 0 ? intVal($temp["WIDTH"]) : 120);
if ($arParams["PICTURES_SIGHT"] != "standart" && $arParams["PICTURES"][$arParams["PICTURES_SIGHT"]]["size"] > 0)
	$arParams["THUMBS_SIZE"] = $arParams["PICTURES"][$arParams["PICTURES_SIGHT"]]["size"];

$arResult["ELEMENTS"] = array();
$arResult["ELEMENTS_PREV"] = array();
$arResult["ELEMENTS_CURR"] = array();
$arResult["ELEMENTS_NEXT"] = array();
$arResult["MAX_VAL"] = array("WIDTH" => 0, "HEIGHT" => 0);

$bActiveIsFined = false;

foreach ($arResult["ELEMENTS_LIST"]	as $key => $arElement):
	$coeff = 1;
	if ($arElement["PICTURE"]["WIDTH"] > $arParams["THUMBS_SIZE"] || $arElement["PICTURE"]["HEIGHT"] > $arParams["THUMBS_SIZE"])
	{
		$coeff = max($arElement["PICTURE"]["WIDTH"], $arElement["PICTURE"]["HEIGHT"]);
		$coeff = $coeff / $arParams["THUMBS_SIZE"];
	}	
	$res = array(
		"id" => $arElement["ID"],
		"url" => $arElement["~URL"],
		"src" => $arElement["PICTURE"]["SRC"],
		"width" => intval(roundEx($arElement["PICTURE"]["WIDTH"]/$coeff)),
		"height" => intval(roundEx($arElement["PICTURE"]["HEIGHT"]/$coeff)),
		"alt" => $arElement["CODE"],
		"title" => $arElement["NAME"]);
		
	$arResult["MAX_VAL"]["WIDTH"] = max($arResult["MAX_VAL"]["WIDTH"], $res["width"]);
	$arResult["MAX_VAL"]["HEIGHT"] = max($arResult["MAX_VAL"]["HEIGHT"], $res["height"]);

	if ($arElement["ID"] == $arParams["ELEMENT_ID"])
	{
		$bActiveIsFined = true;
		$res["active"] = "Y";
	}
	
	if ($bActiveIsFined)
		array_push($arResult["ELEMENTS_NEXT"], $res);
	else 
		array_push($arResult["ELEMENTS_PREV"], $res);
	$arResult["ELEMENTS"][] = $res;
endforeach;

for ($ii = 0; $ii < round($arParams["SLIDER_COUNT_CELL"]/2); $ii++):
	$res = array_pop($arResult["ELEMENTS_PREV"]);
	if (is_array($res) && !empty($res))
		array_unshift($arResult["ELEMENTS_CURR"], $res);
	else
		break;
endfor;

while (count($arResult["ELEMENTS_CURR"]) < $arParams["SLIDER_COUNT_CELL"]):
	$res = array_shift($arResult["ELEMENTS_NEXT"]);
	if (is_array($res) && !empty($res))
		array_push($arResult["ELEMENTS_CURR"], $res);
	else
		break;
endwhile;
		
while (count($arResult["ELEMENTS_CURR"]) < $arParams["SLIDER_COUNT_CELL"]):
	$res = array_pop($arResult["ELEMENTS_PREV"]);
	if (is_array($res) && !empty($res))
		array_unshift($arResult["ELEMENTS_CURR"], $res);
	else
		break;
endwhile;

$arParams["B_ACTIVE_IS_FINED"] = (($bActiveIsFined) ? "Y" : "N");
?>