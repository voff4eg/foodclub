<?php
$_SERVER["DOCUMENT_ROOT"] = "/home/webserver/www";

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule("iblock");
$strFoodshotXML = "";
require_once($_SERVER['DOCUMENT_ROOT']."/classes/foodshot.class.php");
$CFooshot = CFoodshot::getInstance();
$arFoodshotList = $CFooshot->getList(array(),0);
if(!empty($arFoodshotList["elems"])){
	foreach($arFoodshotList["elems"] as $i => $arFoodshot){
		$strFoodshotXML .= "
<url>
	<loc>http://www.foodclub.ru/foodshot/".$arFoodshot["id"]."/#!foodshot</loc>
	<lastmod>".TimeEncode(MakeTimeStamp(ConvertDateTime($arFoodshot["date_update"], "DD.MM.YYYY HH:MI:SS"), "DD.MM.YYYY HH:MI:SS"))."</lastmod>
</url>";
	}
}

echo $strFoodshotXML;

//TimeEncode(MakeTimeStamp(ConvertDateTime($ar["FULL_DATE_CHANGE"], "DD.MM.YYYY HH:MI:SS"), "DD.MM.YYYY HH:MI:SS"))
function TimeEncode($iTime)
{
	$iTZ = date("Z", $iTime);
	$iTZHour = intval(abs($iTZ)/3600);
	$iTZMinutes = intval((abs($iTZ)-$iTZHour*3600)/60);
	$strTZ = ($iTZ<0? "-": "+").sprintf("%02d:%02d", $iTZHour, $iTZMinutes);
	return date("Y-m-d",$iTime)."T".date("H:i:s",$iTime).$strTZ;
}
?>