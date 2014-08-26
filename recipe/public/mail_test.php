<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("iblock");

	$arTest = array("dp@twinpx.ru", "skiripich@gmail.com");


	CEvent::Send("USER_FAST_FOOD", SITE_ID, array("EMAIL_TO" => implode(",", $arTest)), "N", "");

	echo "223";

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog.php");
?>