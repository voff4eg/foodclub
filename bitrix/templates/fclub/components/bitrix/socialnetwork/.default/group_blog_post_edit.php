<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div id="content">
	<div id="text_space">
<?
if (CModule::IncludeModule("advertising")){ $strBanner = CAdvBanner::Show("right_banner"); }

$APPLICATION->IncludeComponent(
	"bitrix:socialnetwork.group", 
	"short", 
	Array(
		"PATH_TO_USER" => $arParams["PATH_TO_USER"],
		"PATH_TO_GROUP" => $arResult["PATH_TO_GROUP"],
		"PATH_TO_GROUP_EDIT" => $arResult["PATH_TO_GROUP_EDIT"],
		"PATH_TO_GROUP_CREATE" => $arResult["PATH_TO_GROUP_CREATE"],
		"PATH_TO_GROUP_REQUEST_SEARCH" => $arResult["PATH_TO_GROUP_REQUEST_SEARCH"],
		"PATH_TO_USER_REQUEST_GROUP" => $arResult["PATH_TO_USER_REQUEST_GROUP"],
		"PATH_TO_GROUP_REQUESTS" => $arResult["PATH_TO_GROUP_REQUESTS"],
		"PATH_TO_GROUP_MODS" => $arResult["PATH_TO_GROUP_MODS"],
		"PATH_TO_SEARCH" => $arResult["PATH_TO_SEARCH"],
		"PATH_TO_GROUP_USERS" => $arResult["PATH_TO_GROUP_USERS"],
		"PATH_TO_USER_LEAVE_GROUP" => $arResult["PATH_TO_USER_LEAVE_GROUP"],
		"PATH_TO_GROUP_DELETE" => $arResult["PATH_TO_GROUP_DELETE"],
		"PATH_TO_GROUP_FEATURES" => $arResult["PATH_TO_GROUP_FEATURES"],
		"PATH_TO_GROUP_BAN" => $arResult["PATH_TO_GROUP_BAN"],
		"PATH_TO_MESSAGE_TO_GROUP" => $arResult["PATH_TO_MESSAGE_TO_GROUP"], 
		"PAGE_VAR" => $arResult["ALIASES"]["page"],
		"USER_VAR" => $arResult["ALIASES"]["user_id"],
		"GROUP_VAR" => $arResult["ALIASES"]["group_id"],
		"SET_TITLE" => "N", 
		"SHORT_FORM" => "Y",
		"USER_ID" => $arResult["VARIABLES"]["user_id"],
		"GROUP_ID" => $arResult["VARIABLES"]["group_id"],
		"ITEMS_COUNT" => $arParams["ITEM_MAIN_COUNT"],
	),
	$component 
);
?>
<?
$APPLICATION->IncludeComponent(
	"custom:blog.post.edit",
	"",
	Array(
		"ID"					=> $arResult["VARIABLES"]["post_id"],
		"PATH_TO_BLOG" => $arResult["PATH_TO_USER_BLOG"], 
		"PATH_TO_GROUP_BLOG" => $arResult["PATH_TO_GROUP_BLOG"], 
		"PATH_TO_POST"		=> $arResult["PATH_TO_GROUP_BLOG_POST"],
		"PATH_TO_POST_EDIT"		=> $arResult["PATH_TO_GROUP_BLOG_POST_EDIT"],
		"PATH_TO_USER" => $arParams["PATH_TO_USER"],
		"PATH_TO_DRAFT"			=> $arResult["PATH_TO_GROUP_BLOG_DRAFT"], 
		"PATH_TO_SMILE" => $arResult["PATH_TO_SMILE"], 
		"SET_TITLE"				=> $arResult["SET_TITLE"],
		"SET_NAV_CHAIN" => "N",
		"POST_PROPERTY" => $arParams["POST_PROPERTY"],
		"DATE_TIME_FORMAT" => $arResult["DATE_TIME_FORMAT"],
		"USER_ID"					=> $arResult["VARIABLES"]["user_id"],
		"USER_VAR" => $arResult["ALIASES"]["user_id"],
		"PAGE_VAR" => $arResult["ALIASES"]["blog_page"],
		"POST_VAR" => $arResult["ALIASES"]["post_id"],
		"SOCNET_GROUP_ID" => $arResult["VARIABLES"]["group_id"],
		"GROUP_ID" => $arParams["BLOG_GROUP_ID"],
	),
	$component
);
?>
	</div>
	<div id="banner_space">
		<?if(strlen($strBanner) > 0){?><div class="banner"><?=$strBanner?></div><?}?>
	</div>
	<div class="clear"></div>

</div>