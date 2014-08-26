<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(intval($_REQUEST["u"]) > 0){
	$UserId = IntVal($_REQUEST['u']);
}else{
	if(CUSer::IsAuthorized()){
		$UserId = IntVal($USER->GetId());
	}else{
		LocalRedirect("/auth/?backurl=".$APPLICATION->GetCurPage());
	}	
}

$APPLICATION->AddHeadScript("/js/file-upload/js/vendor/jquery.ui.widget.js");
$APPLICATION->AddHeadScript("/js/file-upload/js/jquery.iframe-transport.js");
$APPLICATION->AddHeadScript("/js/file-upload/js/jquery.fileupload.js");
$APPLICATION->AddHeadScript("/js/file-upload/js/jquery.fileupload-fp.js");
$APPLICATION->AddHeadScript("/js/file-upload/js/jquery.fileupload-ui.js");
//$APPLICATION->AddHeadScript("/components/personal.ill/script.js");
$APPLICATION->AddHeadString('<!-- The XDomainRequest Transport is included for cross-domain file deletion for IE8+ -->
<!--[if gte IE 8]><script src="js/cors/jquery.xdr-transport.js"></script><![endif]-->');

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
	$CACHE_MANAGER->StartTagCache("/profile");
	$rsUser = $USER->GetByID($UserId);
	if($arUser = $rsUser->Fetch()){
		
		if(strlen($arUser["NAME"]) > 0 && strlen($arUser["LAST_NAME"]) > 0){
	     	$arUser['FULLNAME'] = $arUser["NAME"]." ".$arUser["LAST_NAME"];
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

		$rsStatus = CUserFieldEnum::GetList(array(), array(
			"ID" => $arUser["UF_STATUS"],
		));
		if($arStatus = $rsStatus->GetNext()){
			$arUser["STATUS"] = $arStatus["VALUE"];
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
	$CACHE_MANAGER->RegisterTag("profile#".$UserId);
	$CACHE_MANAGER->EndTagCache();
	$obCache->EndDataCache($arUser);
}else{
	$arUser = array();
}
return $arUser;
?>