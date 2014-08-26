<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div id="content">
	<div id="text_space">
<?
if (CModule::IncludeModule("advertising")){ $strBanner = CAdvBanner::Show("right_banner"); }

$arGroup = $APPLICATION->IncludeComponent(
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
		"PATH_TO_SEARCH" => $arResult["PATH_TO_SEARCH"],
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
if (CModule::IncludeModule("blog")){
	//$arBlog = CBlog::GetList($arGroup["ID"]);SOCNET_GROUP_ID
	//echo "<pre>";print_R($arBlog);echo "</pre>";
	$SORT = Array("DATE_CREATE" => "DESC", "NAME" => "ASC");
	$arFilter = Array(
	        "ACTIVE" => "Y",
	        "GROUP_SITE_ID" => SITE_ID,
	        "SOCNET_GROUP_ID" => $arGroup["ID"]
	    );	
	$arSelectedFields = array("ID", "NAME", "DESCRIPTION", "URL", "OWNER_ID", "DATE_CREATE");

	$dbBlogs = CBlog::GetList(
	        $SORT,
	        $arFilter,
	        false,
	        false,
	        $arSelectedFields
	    );

	if($arBlog = $dbBlogs->Fetch())
	{
	    echo "<!--<pre>";print_R($arBlog);echo "</pre>-->";
	}
}
?>
<?
$APPLICATION->IncludeComponent(
	"bitrix:blog.blog.draft",
	"",
	Array(
		"MESSAGE_COUNT" => "25", 
		"DATE_TIME_FORMAT" => $arParams["DATE_TIME_FORMAT"], 
		"PATH_TO_BLOG" => $arResult["PATH_TO_USER_BLOG"], 
		"PATH_TO_GROUP_BLOG" => $arResult["PATH_TO_GROUP_BLOG"], 
		"PATH_TO_BLOG_CATEGORY" => $arResult["PATH_TO_BLOG_CATEGORY"],
		"PATH_TO_POST" => $arResult["PATH_TO_GROUP_BLOG_POST"], 
		"PATH_TO_POST_EDIT" => $arResult["PATH_TO_GROUP_BLOG_POST_EDIT"], 
		"PATH_TO_USER" => $arParams["PATH_TO_USER"],
		"PATH_TO_SMILE" => $arResult["PATH_TO_SMILE"], 
		"USER_ID" => $arResult["VARIABLES"]["user_id"], 
		"CACHE_TYPE" => $arResult["CACHE_TYPE"], 
		"CACHE_TIME" => $arResult["CACHE_TIME"], 
		"CACHE_TIME_LONG" => "604800", 
		"SET_NAV_CHAIN" => "N", 
		"SET_TITLE" => $arResult["SET_TITLE"], 
		"NAV_TEMPLATE" => "", 
		"POST_PROPERTY_LIST" => array(),
		"USER_VAR" => $arResult["ALIASES"]["user_id"],
		"PAGE_VAR" => $arResult["ALIASES"]["blog_page"],
		"POST_VAR" => $arResult["ALIASES"]["post_id"],
		"SOCNET_GROUP_ID" => $arResult["VARIABLES"]["group_id"],
		"GROUP_ID" => $arParams["BLOG_GROUP_ID"],
		"BLOG_URL" => $arBlog["URL"]
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