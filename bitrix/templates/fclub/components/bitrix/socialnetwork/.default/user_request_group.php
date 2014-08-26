<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div id="content">
	<div class="system_message">
<?
$APPLICATION->IncludeComponent(
	"bitrix:socialnetwork.user_request_group", 
	"", 
	Array(
		"PATH_TO_USER" => $arResult["PATH_TO_USER"],
		"PATH_TO_GROUP" => $arResult["PATH_TO_GROUP"],
		"PAGE_VAR" => $arResult["ALIASES"]["page"],
		"USER_VAR" => $arResult["ALIASES"]["user_id"],
		"GROUP_VAR" => $arResult["ALIASES"]["group_id"],
		"SET_TITLE" => "Y", 
		"PATH_TO_SMILE" => $arResult["PATH_TO_SMILE"],
		"GROUP_ID" => $arResult["VARIABLES"]["group_id"],
	),
	$component 
);
?>
	</div>
</div>
