<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
?>
<?$APPLICATION->SetAdditionalCSS("/css/profile.css");
$APPLICATION->SetAdditionalCSS("/css/elem.css");?>
<?

if (CModule::IncludeModule("advertising")){ $strBanner = CAdvBanner::Show("right_banner"); }

/*if(strpos($_REQUEST['place'], "pr_comment") !== false){
    if($USER->IsAuthorized())
    {
        $UserId = IntVal($USER->GetId());
    }
    else 
    {
	    LocalRedirect("/auth/?backurl=/profile/comments/");
    }
} else {
	$UserId = IntVal($_REQUEST['u']);
}*/

/*if(intval($_REQUEST["u"]) > 0){
	$UserId = IntVal($_REQUEST['u']);
}else{
	if(CUSer::IsAuthorized()){
		$UserId = IntVal($USER->GetId());
	}else{
		LocalRedirect("/auth/?backurl=".$APPLICATION->GetCurPage());
	}	
}

$obCache = new CPHPCache;
if($obCache->InitCache(3600, "profile_".SITE_ID."_".$UserId, "/profile")){
	require_once($_SERVER["DOCUMENT_ROOT"]."/classes/factory.class.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/classes/main.class.php");
	$Factory = new CFactory;
	$CFClub = new CFClub;
	$arUser = $obCache->GetVars();
	$APPLICATION->SetPageProperty("title", $arUser['FULLNAME']." &mdash; страница пользователя на Foodclub");	
}elseif($obCache->StartDataCache()){
    global $CACHE_MANAGER;
	$CACHE_MANAGER->StartTagCache("/profile_".$UserId);
	$rsUser = $USER->GetByID($UserId);
	if($arUser = $rsUser->Fetch()){
		
		if(strlen($arUser["NAME"]) > 0 && strlen($arUser["LAST_NAME"]) > 0){
	     	$arUser['FULLNAME'] = $arUser["LAST_NAME"]." ".$arUser["NAME"];
	 	}else{
	 		$arUser['FULLNAME'] = $arUser['LOGIN'];	 	
	 	}

		CModule::IncludeModule("socialnetwork");
		$rsRel = CSocNetUserToGroup::GetList(Array(), Array("USER_ID"=>$UserId), false, false);
		while($rs = $rsRel->GetNext()){
			$Groups[] = CSocNetGroup::GetByID($rs['GROUP_ID']);
		}
		
		$APPLICATION->SetPageProperty("title", $arUser['FULLNAME']." &mdash; страница пользователя на Foodclub");
		
		$arMonth = Array("01"=>"января", "02"=>"февраля", "03"=>"марта", "04"=>"апреля",	"05"=>"мая", "06"=>"июня", "07"=>"июля", 
						 "08"=>"августа", "09"=>"сентября", "10"=>"октября", "11"=>"ноября", "12"=>"декабря");
		
		if(intval($arUser['PERSONAL_PHOTO']) > 0){
			$rsAvatar = CFile::GetByID($arUser['PERSONAL_PHOTO']);
			$arAvatar = $rsAvatar->Fetch();
			$arAvatar['SRC'] = "/upload/".$arAvatar['SUBDIR']."/".$arAvatar['FILE_NAME'];
			$arUser["AVATAR"] = $arAvatar;
		} else {
			$arAvatar['SRC'] = "/images/avatar/avatar.jpg";
			$arUser["AVATAR"] = $arAvatar;
		}

		CModule::IncludeModule("blog");

		require_once($_SERVER["DOCUMENT_ROOT"]."/classes/factory.class.php");
		require_once($_SERVER["DOCUMENT_ROOT"]."/classes/main.class.php");
		$Factory = new CFactory;
		$CFClub = new CFClub;

		$arUser["RECIPES_COUNT"] = $CFClub->getUserRecipesCount($arUser["ID"]);
		$arUser["COMMENTS_COUNT"] = $CFClub->getUserCommentsCount($arUser["ID"]);
		$arUser["REPLIES_COUNT"] = $CFClub->getUserRepliesCount($arUser["ID"]);
		$arUser["POSTS_COUNT"] = $CFClub->getUserPostCount($arUser["ID"]);
		$arUser["LAST_RECIPES"] = $CFClub->getUserLastRecipes($arUser["ID"]);
		$arUser["LAST_POSTS"] = $CFClub->getUserLastPosts($arUser["ID"]);
		
	}else{
		$arUser = array();
	}
	$CACHE_MANAGER->RegisterTag("profile_".$UserId);	
	$CACHE_MANAGER->EndTagCache();
	$obCache->EndDataCache($arUser);
}else{
	$arUser = array();
}*/

$arUser = $APPLICATION->IncludeComponent("custom:profile", "", array());

$APPLICATION->SetPageProperty("title", $arUser['FULLNAME']." &mdash; комментарии пользователя на Foodclub");
?>
<div id="content">
	<div class="b-personal-page">
	<?$APPLICATION->IncludeFile("/personal/.profile_header.php", Array(
		"USER" => $arUser)
	);?>
	
	 <?$APPLICATION->IncludeComponent(
		"custom:profile_menu",
		"",
		Array(
			"ROOT_MENU_TYPE" => "profile",
			"MAX_LEVEL" => "1",
			"CHILD_MENU_TYPE" => "profile",
			"USE_EXT" => "N",
			"DELAY" => "N",
			"ALLOW_MULTI_SELECT" => "N",
			"MENU_CACHE_TYPE" => "N",
			"MENU_CACHE_TIME" => "3600",
			"MENU_CACHE_USE_GROUPS" => "Y",
			"MENU_CACHE_GET_VARS" => ""
		),
	false
	);?>
	<?
	$APPLICATION->IncludeComponent("custom:profile.comments", "", array("USER"=>$arUser));
	?>
	</div>
	<div id="banner_space">
		<?$APPLICATION->IncludeComponent("custom:profile.badges", "", array("USER" => $arUser));?>
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
	</div>
	<div class="clear"></div>
</div>	
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
