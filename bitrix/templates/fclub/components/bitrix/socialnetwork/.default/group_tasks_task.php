<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$APPLICATION->IncludeComponent(
	"bitrix:socialnetwork.group_menu",
	"",
	Array(
		"GROUP_VAR" => $arResult["ALIASES"]["group_id"],
		"PAGE_VAR" => $arResult["ALIASES"]["page"],
		"PATH_TO_GROUP" => $arResult["PATH_TO_GROUP"],
		"PATH_TO_GROUP_MODS" => $arResult["PATH_TO_GROUP_MODS"],
		"PATH_TO_GROUP_USERS" => $arResult["PATH_TO_GROUP_USERS"],
		"PATH_TO_GROUP_EDIT" => $arResult["PATH_TO_GROUP_EDIT"],
		"PATH_TO_GROUP_REQUEST_SEARCH" => $arResult["PATH_TO_GROUP_REQUEST_SEARCH"],
		"PATH_TO_GROUP_REQUESTS" => $arResult["PATH_TO_GROUP_REQUESTS"],
		"PATH_TO_GROUP_BAN" => $arResult["PATH_TO_GROUP_BAN"],
		"PATH_TO_GROUP_BLOG" => $arResult["PATH_TO_GROUP_BLOG"],
		"PATH_TO_GROUP_PHOTO" => $arResult["PATH_TO_GROUP_PHOTO"],
		"PATH_TO_GROUP_FORUM" => $arResult["PATH_TO_GROUP_FORUM"],
		"PATH_TO_GROUP_CALENDAR" => $arResult["PATH_TO_GROUP_CALENDAR"],
		"PATH_TO_GROUP_FILES" => $arResult["PATH_TO_GROUP_FILES"],
		"PATH_TO_GROUP_TASKS" => $arResult["PATH_TO_GROUP_TASKS"],
		"GROUP_ID" => $arResult["VARIABLES"]["group_id"],
		"PAGE_ID" => "group_tasks",
	),
	$component
);
?>
<br />
<?
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
if (CSocNetFeatures::IsActiveFeature(SONET_ENTITY_GROUP, $arResult["VARIABLES"]["group_id"], "tasks"))
{
	?>
	<br class="sn-br" />
	<?
	$APPLICATION->IncludeComponent("bitrix:intranet.tasks.menu", ".default", Array(
			"IBLOCK_ID" => $arParams["TASK_IBLOCK_ID"],
			"OWNER_ID" => $arResult["VARIABLES"]["group_id"],
			"TASK_TYPE" => 'group',
			"PAGE_VAR" => $arResult["ALIASES"]["page"],
			"GROUP_VAR" => $arResult["ALIASES"]["group_id"],
			"VIEW_VAR" => $arResult["ALIASES"]["view_id"],
			"TASK_VAR" => $arResult["ALIASES"]["task_id"],
			"ACTION_VAR" => $arResult["ALIASES"]["action"],
			"ACTION" => $arResult["VARIABLES"]["action"],
			"TASK_ID" => $arResult["VARIABLES"]["task_id"],
			"PATH_TO_GROUP_TASKS" => $arResult["PATH_TO_GROUP_TASKS"],
			"PATH_TO_GROUP_TASKS_TASK" => $arResult["PATH_TO_GROUP_TASKS_TASK"],
			"PATH_TO_GROUP_TASKS_VIEW" => $arResult["PATH_TO_GROUP_TASKS_VIEW"],
			"PAGE_ID" => "group_tasks_task",
		),
		$component,
		array("HIDE_ICONS" => "Y")
	);
	?>
	<br class="sn-br" />
	<?
	$zzz = $APPLICATION->IncludeComponent(
		"bitrix:intranet.tasks.create",
		".default",
		Array(
			"IBLOCK_ID" => $arParams["TASK_IBLOCK_ID"],
			"OWNER_ID" => $arResult["VARIABLES"]["group_id"],
			"TASK_TYPE" => 'group',
			"PAGE_VAR" => $arResult["ALIASES"]["page"],
			"GROUP_VAR" => $arResult["ALIASES"]["group_id"],
			"VIEW_VAR" => $arResult["ALIASES"]["view_id"],
			"TASK_VAR" => $arResult["ALIASES"]["task_id"],
			"ACTION_VAR" => $arResult["ALIASES"]["action"],
			"ACTION" => $arResult["VARIABLES"]["action"],
			"TASK_ID" => $arResult["VARIABLES"]["task_id"],
			"PATH_TO_GROUP_TASKS" => $arResult["PATH_TO_GROUP_TASKS"],
			"PATH_TO_GROUP_TASKS_TASK" => $arResult["PATH_TO_GROUP_TASKS_TASK"],
			"PATH_TO_GROUP_TASKS_VIEW" => $arResult["PATH_TO_GROUP_TASKS_VIEW"],
			"TASKS_FIELDS_SHOW" => $arParams["TASKS_FIELDS_SHOW"],
		),
		$component,
		array("HIDE_ICONS" => "Y")
	);

	if ($zzz && IntVal($arResult["VARIABLES"]["task_id"]) > 0 && $arResult["VARIABLES"]["action"] == "view")
	{
		?>
		<br /><br />
		<?
		$APPLICATION->IncludeComponent(
			"bitrix:forum.topic.reviews",
			"",
			Array(
				"CACHE_TYPE" => $arParams["CACHE_TYPE"],
				"CACHE_TIME" => $arParams["CACHE_TIME"],
				"MESSAGES_PER_PAGE" => $arParams["ITEM_DETAIL_COUNT"],
				"USE_CAPTCHA" => "N",
				"PREORDER" => "Y",
				"PATH_TO_SMILE" => $arParams["PATH_TO_FORUM_SMILE"],
				"FORUM_ID" => $arParams["TASK_FORUM_ID"],
				"URL_TEMPLATES_READ" => $arParams["URL_TEMPLATES_READ"],
				"SHOW_LINK_TO_FORUM" => "N",
				"ELEMENT_ID" => $arResult["VARIABLES"]["task_id"],
			),
			$component,
			array("HIDE_ICONS" => "Y")
		);
	}
}
?>