<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div id="content">
	<div id="text_space">
<?
if (CModule::IncludeModule("advertising")){ $strBanner = CAdvBanner::Show("right_banner"); }

$APPLICATION->IncludeComponent(
	"bitrix:socialnetwork.group", 
	"", 
	Array(
		"PATH_TO_USER" => $arResult["PATH_TO_USER"],
		"PATH_TO_GROUP" => $arResult["PATH_TO_GROUP"],
		"PATH_TO_GROUP_EDIT" => $arResult["PATH_TO_GROUP_EDIT"],
		"PATH_TO_GROUP_CREATE" => $arResult["PATH_TO_GROUP_CREATE"],
		"PATH_TO_GROUP_REQUEST_SEARCH" => $arResult["PATH_TO_GROUP_REQUEST_SEARCH"],
		"PATH_TO_USER_REQUEST_GROUP" => $arResult["PATH_TO_USER_REQUEST_GROUP"],
		"PATH_TO_GROUP_REQUESTS" => $arResult["PATH_TO_GROUP_REQUESTS"],
		"PATH_TO_GROUP_MODS" => $arResult["PATH_TO_GROUP_MODS"],
		"PATH_TO_GROUP_USERS" => $arResult["PATH_TO_GROUP_USERS"],
		"PATH_TO_USER_LEAVE_GROUP" => $arResult["PATH_TO_USER_LEAVE_GROUP"],
		"PATH_TO_GROUP_DELETE" => $arResult["PATH_TO_GROUP_DELETE"],
		"PATH_TO_GROUP_FEATURES" => $arResult["PATH_TO_GROUP_FEATURES"],
		"PATH_TO_GROUP_BAN" => $arResult["PATH_TO_GROUP_BAN"],
		"PATH_TO_SEARCH" => $arResult["PATH_TO_SEARCH"],
		"PAGE_VAR" => $arResult["ALIASES"]["page"],
		"USER_VAR" => $arResult["ALIASES"]["user_id"],
		"GROUP_VAR" => $arResult["ALIASES"]["group_id"],
		"SET_TITLE" => "Y", 
		"USER_ID" => $arResult["VARIABLES"]["user_id"],
		"GROUP_ID" => $arResult["VARIABLES"]["group_id"],
		"ITEMS_COUNT" => $arParams["ITEM_MAIN_COUNT"],
		"PATH_TO_GROUP_BLOG_POST" => $arResult["PATH_TO_GROUP_BLOG_POST"], 
		"PATH_TO_GROUP_BLOG" => $arResult["PATH_TO_GROUP_BLOG"], 
		"PATH_TO_BLOG" => $arResult["PATH_TO_USER_BLOG"], 
		"PATH_TO_POST" => $arResult["PATH_TO_USER_BLOG_POST"], 
		"PATH_TO_GROUP_FORUM" => $arResult["PATH_TO_GROUP_FORUM"], 
		"PATH_TO_GROUP_FORUM_TOPIC" => $arResult["~PATH_TO_GROUP_FORUM_TOPIC"], 
		"PATH_TO_GROUP_FORUM_MESSAGE" => $arResult["~PATH_TO_GROUP_FORUM_MESSAGE"], 
		"FORUM_ID" => $arParams["FORUM_ID"],
		"PATH_TO_GROUP_SUBSCRIBE" => $arResult["PATH_TO_GROUP_SUBSCRIBE"], 
		"PATH_TO_MESSAGE_TO_GROUP" => $arResult["PATH_TO_MESSAGE_TO_GROUP"], 

		"TASK_IBLOCK_ID" => $arParams["TASK_IBLOCK_ID"],
		"TASK_VAR" => $arResult["ALIASES"]["task_id"],
		"TASK_ACTION_VAR" => $arResult["ALIASES"]["action"],
		"PATH_TO_GROUP_TASKS" => $arResult["PATH_TO_GROUP_TASKS"],
		"PATH_TO_GROUP_TASKS_TASK" => $arResult["PATH_TO_GROUP_TASKS_TASK"],
		"PATH_TO_GROUP_TASKS_VIEW" => $arResult["PATH_TO_GROUP_TASKS_VIEW"],
		"TASKS_FIELDS_SHOW" => $arParams["TASKS_FIELDS_SHOW"],
	),
	$component 
);
?>
	</div>
	<div id="banner_space">
		<?if(strlen($strBanner) > 0){?><div class="banner"><?=$strBanner?></div><?}?>
                <?require($_SERVER["DOCUMENT_ROOT"]."/facebook_box.html");?>
	</div>
	<div class="clear"></div>
</div>
