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
?>

<?$APPLICATION->IncludeComponent("bitrix:blog.rss.link", ".default", array(
	"RSS1" => "N",
	"RSS2" => "Y",
	"ATOM" => "N",
	"MODE" => "B",
	"BLOG_URL" => "",
	"POST_ID" => $post_id,
	"GROUP_ID" => "",
	"PATH_TO_RSS" => $arResult["PATH_TO_GROUP_BLOG_RSS"],
	"PATH_TO_RSS_ALL" => "",
	"BLOG_VAR" => "",
	"PAGE_VAR" => $arResult["ALIASES"]["blog_page"],
	"GROUP_VAR" => ""
	),
	false
);?>

<?
$APPLICATION->IncludeComponent(
	"custom:socialnetwork.blog.blog",
	"",
	Array(
		"MESSAGE_COUNT" => "5", 
		"DATE_TIME_FORMAT" => $arParams["DATE_TIME_FORMAT"], 
		"PATH_TO_BLOG" => $arResult["PATH_TO_USER_BLOG"], 
		"PATH_TO_GROUP_BLOG" => $arResult["PATH_TO_GROUP_BLOG"], 
		"PATH_TO_BLOG_CATEGORY" => $APPLICATION->GetCurPageParam("category=#category_id#", Array("category")), 
		"PATH_TO_POST" => $arResult["PATH_TO_GROUP_BLOG_POST"], 
		"PATH_TO_POST_EDIT" => $arResult["PATH_TO_GROUP_BLOG_POST_EDIT"], 
		"PATH_TO_USER" => $arParams["PATH_TO_USER"],
		"PATH_TO_SMILE" => $arResult["PATH_TO_SMILE"], 
		"USER_ID" => $arResult["VARIABLES"]["user_id"], 
		"YEAR" => $year, 
		"MONTH" => $month, 
		"DAY" => $day, 
		"CATEGORY_ID" => $_REQUEST["category"], 
		"CACHE_TYPE" => $arResult["CACHE_TYPE"], 
		"CACHE_TIME" => $arResult["CACHE_TIME"], 
		"CACHE_TIME_LONG" => "604800", 
		"SET_NAV_CHAIN" => "N", 
		"SET_TITLE" => $arResult["SET_TITLE"], 
		"FILTER_NAME" => "", 
		"NAV_TEMPLATE" => "blogs", 
		"POST_PROPERTY_LIST" => array(),
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
		<?
		$rsRandomDoUKnow = CIBlockElement::GetList(array("rand"=>"asc"),array("IBLOCK_CODE"=>"do_u_know"),false,false,array("ID","PREVIEW_TEXT"));
		if($arRandomDoUKnow = $rsRandomDoUKnow->Fetch()){?>
		<div id="do-you-know-that" class="b-facts">
			<div class="b-facts__heading">Знаете ли вы что:</div>
			<div class="b-facts__content">
				<div class="b-facts__item" data-id="<?=$arRandomDoUKnow["ID"]?>">
					<?=$arRandomDoUKnow["PREVIEW_TEXT"]?>
				</div>
			</div>
			<div class="b-facts__more">
				<a href="#" class="b-facts__more__link">Еще</a>
			</div>
		</div>
		<?}?>
		<?if(strlen($strBanner) > 0){?><div class="banner"><?=$strBanner?></div><?}?>
                <?require($_SERVER["DOCUMENT_ROOT"]."/facebook_box.html");?>
	</div>
	<div class="clear"></div>
</div>
