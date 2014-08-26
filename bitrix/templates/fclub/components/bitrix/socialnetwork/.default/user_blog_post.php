<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
$APPLICATION->IncludeComponent(
	"bitrix:socialnetwork.user_menu",
	"",
	Array(
		"USER_VAR" => $arResult["ALIASES"]["user_id"],
		"PAGE_VAR" => $arResult["ALIASES"]["page"],
		"PATH_TO_USER" => $arResult["PATH_TO_USER"],
		"PATH_TO_USER_EDIT" => $arResult["PATH_TO_USER_PROFILE_EDIT"],
		"PATH_TO_USER_FRIENDS" => $arResult["PATH_TO_USER_FRIENDS"],
		"PATH_TO_USER_GROUPS" => $arResult["PATH_TO_USER_GROUPS"],
		"PATH_TO_USER_FRIENDS_ADD" => $arResult["PATH_TO_USER_FRIENDS_ADD"],
		"PATH_TO_USER_FRIENDS_DELETE" => $arResult["PATH_TO_USER_FRIENDS_DELETE"],
		"PATH_TO_MESSAGES_INPUT" => $arResult["PATH_TO_MESSAGES_INPUT"],
		"PATH_TO_MESSAGE_FORM" => $arResult["PATH_TO_MESSAGE_FORM"],
		"PATH_TO_USER_BLOG" => $arResult["PATH_TO_USER_BLOG"],
		"PATH_TO_USER_PHOTO" => $arResult["PATH_TO_USER_PHOTO"],
		"PATH_TO_USER_FORUM" => $arResult["PATH_TO_USER_FORUM"],
		"PATH_TO_USER_CALENDAR" => $arResult["PATH_TO_USER_CALENDAR"],
		"PATH_TO_USER_FILES" => $arResult["PATH_TO_USER_FILES"],
		"PATH_TO_USER_TASKS" => $arResult["PATH_TO_USER_TASKS"],
		"ID" => $arResult["VARIABLES"]["user_id"],
		"PAGE_ID" => "user_blog"
	),
	$component
);
?>

<br />

<?$APPLICATION->IncludeComponent(
	"bitrix:socialnetwork.user_profile", 
	"short", 
	Array(
		"PATH_TO_USER" => $arResult["PATH_TO_USER"],
		"PATH_TO_USER_EDIT" => $arResult["PATH_TO_USER_PROFILE_EDIT"],
		"PATH_TO_USER_FRIENDS" => $arResult["PATH_TO_USER_FRIENDS"],
		"PATH_TO_USER_GROUPS" => $arResult["PATH_TO_USER_GROUPS"],
		"PATH_TO_USER_FRIENDS_ADD" => $arResult["PATH_TO_USER_FRIENDS_ADD"],
		"PATH_TO_USER_FRIENDS_DELETE" => $arResult["PATH_TO_USER_FRIENDS_DELETE"],
		"PATH_TO_MESSAGE_FORM" => $arResult["PATH_TO_MESSAGE_FORM"],
		"PATH_TO_MESSAGES_CHAT" => $arResult["PATH_TO_MESSAGES_CHAT"],
		"PATH_TO_MESSAGES_USERS_MESSAGES" => $arResult["PATH_TO_MESSAGES_USERS_MESSAGES"],
		"PATH_TO_USER_SETTINGS_EDIT" => $arResult["PATH_TO_USER_SETTINGS_EDIT"],
		"PATH_TO_GROUP" => $arParams["PATH_TO_GROUP"],
		"PATH_TO_GROUP_CREATE" => $arResult["PATH_TO_GROUP_CREATE"],
		"PATH_TO_USER_FEATURES" => $arResult["PATH_TO_USER_FEATURES"],
		"PAGE_VAR" => $arResult["ALIASES"]["page"],
		"USER_VAR" => $arResult["ALIASES"]["user_id"],
		"SET_TITLE" => "N", 
		"USER_PROPERTY_MAIN" => $arResult["USER_PROPERTY_MAIN"],
		"USER_PROPERTY_CONTACT" => $arResult["USER_PROPERTY_CONTACT"],
		"USER_PROPERTY_PERSONAL" => $arResult["USER_PROPERTY_PERSONAL"],
		"USER_FIELDS_MAIN" => $arResult["USER_FIELDS_MAIN"],
		"USER_FIELDS_CONTACT" => $arResult["USER_FIELDS_CONTACT"],
		"USER_FIELDS_PERSONAL" => $arResult["USER_FIELDS_PERSONAL"],
		"PATH_TO_USER_FEATURES" => $arResult["PATH_TO_USER_FEATURES"],
		"DATE_TIME_FORMAT" => $arResult["DATE_TIME_FORMAT"],
		"SHORT_FORM" => "Y",
		"ITEMS_COUNT" => $arParams["ITEM_MAIN_COUNT"],
		"ID" => $arResult["VARIABLES"]["user_id"],
		"PATH_TO_GROUP_REQUEST_GROUP_SEARCH" => $arResult["PATH_TO_GROUP_REQUEST_GROUP_SEARCH"], 
		"PATH_TO_CONPANY_DEPARTMENT" => $arParams["PATH_TO_CONPANY_DEPARTMENT"], 
	),
	$component,
	array("HIDE_ICONS" => "Y") 
);
?>
<?
$APPLICATION->IncludeComponent(
	"bitrix:socialnetwork.blog.menu",
	"",
	Array(
		"PATH_TO_USER" => $arResult["PATH_TO_USER"],
		"PATH_TO_POST_EDIT" => $arResult["PATH_TO_USER_BLOG_POST_EDIT"],
		"PATH_TO_DRAFT" => $arResult["PATH_TO_USER_BLOG_DRAFT"],
		"USER_ID" => $arResult["VARIABLES"]["user_id"],
		"USER_VAR" => $arResult["ALIASES"]["user_id"],
		"PAGE_VAR" => $arResult["ALIASES"]["blog_page"],
		"POST_VAR" => $arResult["ALIASES"]["post_id"],
		"PATH_TO_BLOG" => $arResult["PATH_TO_USER_BLOG"],
		"SET_NAV_CHAIN" => $arResult["SET_NAV_CHAIN"],
	),
	$component
);

$APPLICATION->IncludeComponent(
		"bitrix:blog.post", 
		"", 
		Array(
				"POST_VAR"				=> $arResult["ALIASES"]["post_id"],
				"USER_VAR"				=> $arResult["ALIASES"]["user_id"],
				"PAGE_VAR"				=> $arResult["ALIASES"]["blog_page"],
				"PATH_TO_BLOG"			=> $arResult["PATH_TO_USER_BLOG"],
				"PATH_TO_BLOG_CATEGORY"	=> $arResult["PATH_TO_USER_BLOG_CATEGORY"],
				"PATH_TO_POST_EDIT"		=> $arResult["PATH_TO_USER_BLOG_POST_EDIT"],
				"PATH_TO_USER"			=> $arResult["PATH_TO_USER"],
				"PATH_TO_SMILE"			=> $arResult["PATH_TO_SMILE"],
				"ID"					=> $arResult["VARIABLES"]["post_id"],
				"CACHE_TYPE"			=> $arResult["CACHE_TYPE"],
				"CACHE_TIME"			=> $arResult["CACHE_TIME"],
				"SET_NAV_CHAIN" => "N", 
				"SET_TITLE"				=> $arResult["SET_TITLE"],
				"POST_PROPERTY"	=> $arParams["POST_PROPERTY"],
				"DATE_TIME_FORMAT"	=> $arResult["DATE_TIME_FORMAT"],
				"USER_ID" => $arResult["VARIABLES"]["user_id"],
				"GROUP_ID" => $arParams["BLOG_GROUP_ID"],
			),
		$component 
	);

$APPLICATION->IncludeComponent(
		"bitrix:blog.post.comment", 
		"", 
		Array(
				"BLOG_VAR"		=> $arResult["ALIASES"]["blog"],
				"USER_VAR"		=> $arResult["ALIASES"]["user_id"],
				"PAGE_VAR"		=> $arResult["ALIASES"]["blog_page"],
				"POST_VAR"			=> $arResult["ALIASES"]["post_id"],
				"PATH_TO_BLOG"	=> $arResult["PATH_TO_USER_BLOG"],
				"PATH_TO_POST"	=> $arResult["PATH_TO_USER_BLOG_POST"],
				"PATH_TO_USER"	=> $arResult["PATH_TO_USER"],
				"PATH_TO_SMILE"	=> $arResult["PATH_TO_SMILE"],
				"ID"			=> $arResult["VARIABLES"]["post_id"],
				"CACHE_TYPE"	=> $arResult["CACHE_TYPE"],
				"CACHE_TIME"	=> $arResult["CACHE_TIME"],
				"COMMENTS_COUNT" => $arResult["COMMENTS_COUNT"],
				"DATE_TIME_FORMAT"	=> $arResult["DATE_TIME_FORMAT"],
				"USE_ASC_PAGING"	=> $arParams["USE_ASC_PAGING"],
				"USER_ID" => $arResult["VARIABLES"]["user_id"],
				"GROUP_ID" => $arParams["BLOG_GROUP_ID"],
				"NOT_USE_COMMENT_TITLE" => "Y",
			),
		$component 
	);

?>