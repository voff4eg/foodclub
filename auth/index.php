<?
define("NEED_AUTH", true);
//require($_SERVER["DOCUMENT_ROOT"]."/bitrix/h0eader.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if (isset($_REQUEST["backurl"]) && strlen($_REQUEST["backurl"])>0) 
	LocalRedirect($_REQUEST["backurl"]);

LocalRedirect("/");
?>