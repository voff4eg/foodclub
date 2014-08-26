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
		$update_time = MakeTimeStamp(ConvertDateTime($arFoodshot["date_update"], "DD.MM.YYYY HH:MI:SS"), "DD.MM.YYYY HH:MI:SS");
		//1409051419 26-08-14 15-10
		if($update_time > 1409051419){
			$time = $update_time;
		}else{
			$time = 1409051419;
		}
		$strFoodshotXML .= "
	<url>
		<loc>http://www.foodclub.ru/foodshot/".$arFoodshot["id"]."/#!foodshot</loc>
		<lastmod>".TimeEncode($time)."</lastmod>
	</url>";
	}
}

//TimeEncode(MakeTimeStamp(ConvertDateTime($ar["FULL_DATE_CHANGE"], "DD.MM.YYYY HH:MI:SS"), "DD.MM.YYYY HH:MI:SS"))
function TimeEncode($iTime)
{
	$iTZ = date("Z", $iTime);
	$iTZHour = intval(abs($iTZ)/3600);
	$iTZMinutes = intval((abs($iTZ)-$iTZHour*3600)/60);
	$strTZ = ($iTZ<0? "-": "+").sprintf("%02d:%02d", $iTZHour, $iTZMinutes);
	return date("Y-m-d",$iTime)."T".date("H:i:s",$iTime).$strTZ;
}

$str = '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
$str .= $strFoodshotXML;
$str .= '
</urlset>';

chdir ('/srv/www/foodclub/');
$file = fopen('yandex_foodshot.xml', 'w');
fwrite($file,$str);
fclose($file);
?>