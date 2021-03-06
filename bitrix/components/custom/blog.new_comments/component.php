<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!CModule::IncludeModule("blog"))
{
	ShowError(GetMessage("BLOG_MODULE_NOT_INSTALL"));
	return;
}

$arParams["COMMENT_COUNT"] = IntVal($arParams["COMMENT_COUNT"])>0 ? IntVal($arParams["COMMENT_COUNT"]): 6;
$arParams["SORT_BY1"] = (strlen($arParams["SORT_BY1"])>0 ? $arParams["SORT_BY1"] : "DATE_CREATE");
$arParams["SORT_ORDER1"] = (strlen($arParams["SORT_ORDER1"])>0 ? $arParams["SORT_ORDER1"] : "DESC");
$arParams["SORT_BY2"] = (strlen($arParams["SORT_BY2"])>0 ? $arParams["SORT_BY2"] : "ID");
$arParams["SORT_ORDER2"] = (strlen($arParams["SORT_ORDER2"])>0 ? $arParams["SORT_ORDER2"] : "DESC");
$arParams["MESSAGE_LENGTH"] = (IntVal($arParams["MESSAGE_LENGTH"])>0)?$arParams["MESSAGE_LENGTH"]:100;
$arParams["BLOG_URL"] = preg_replace("/[^a-zA-Z0-9_-]/is", "", Trim($arParams["BLOG_URL"]));
$arParams["USE_SOCNET"] = ($arParams["USE_SOCNET"] == "Y") ? "Y" : "N";
if(!is_array($arParams["GROUP_ID"]))
	$arParams["GROUP_ID"] = array($arParams["GROUP_ID"]);
foreach($arParams["GROUP_ID"] as $k=>$v)
	if(IntVal($v) <= 0)
		unset($arParams["GROUP_ID"][$k]);

if ($arParams["CACHE_TYPE"] == "Y" || ($arParams["CACHE_TYPE"] == "A" && COption::GetOptionString("main", "component_cache_on", "Y") == "Y"))
	$arParams["CACHE_TIME"] = intval($arParams["CACHE_TIME"]);
else
	$arParams["CACHE_TIME"] = 0;	

if(strLen($arParams["BLOG_VAR"])<=0)
	$arParams["BLOG_VAR"] = "blog";
if(strLen($arParams["PAGE_VAR"])<=0)
	$arParams["PAGE_VAR"] = "page";
if(strLen($arParams["USER_VAR"])<=0)
	$arParams["USER_VAR"] = "id";
if(strLen($arParams["POST_VAR"])<=0)
	$arParams["POST_VAR"] = "id";
if(strLen($arParams["COMMENT_ID_VAR"])<=0)
	$arParams["COMMENT_ID_VAR"] = "commentId";
	
$arParams["PATH_TO_BLOG"] = trim($arParams["PATH_TO_BLOG"]);
if(strlen($arParams["PATH_TO_BLOG"])<=0)
	$arParams["PATH_TO_BLOG"] = htmlspecialchars($APPLICATION->GetCurPage()."?".$arParams["PAGE_VAR"]."=blog&".$arParams["BLOG_VAR"]."=#blog#");

$arParams["PATH_TO_SMILE"] = strlen(trim($arParams["PATH_TO_SMILE"]))<=0 ? false : trim($arParams["PATH_TO_SMILE"]);
	
$arParams["PATH_TO_POST"] = trim($arParams["PATH_TO_POST"]);
if(strlen($arParams["PATH_TO_POST"])<=0)
	$arParams["PATH_TO_POST"] = htmlspecialchars($APPLICATION->GetCurPage()."?".$arParams["PAGE_VAR"]."=post&".$arParams["BLOG_VAR"]."=#blog#&".$arParams["POST_VAR"]."=#post_id#");

$arParams["PATH_TO_USER"] = trim($arParams["PATH_TO_USER"]);
if(strlen($arParams["PATH_TO_USER"])<=0)
	$arParams["PATH_TO_USER"] = htmlspecialchars($APPLICATION->GetCurPage()."?".$arParams["PAGE_VAR"]."=user&".$arParams["USER_VAR"]."=#user_id#");
$arParams["DATE_TIME_FORMAT"] = trim(empty($arParams["DATE_TIME_FORMAT"]) ? $DB->DateFormatToPHP(CSite::GetDateFormat("FULL")) : $arParams["DATE_TIME_FORMAT"]);

$UserGroupID = Array(1);
if($USER->IsAuthorized())
	$UserGroupID[] = 2;

$cache = new CPHPCache;
$cache_id = "blog_last_comments_".serialize($arParams)."_".serialize($UserGroupID)."_".$USER->IsAdmin();
$cache_path = "/".SITE_ID."/blog/last_comments/";

if ($arParams["CACHE_TIME"] > 0 && $cache->InitCache($arParams["CACHE_TIME"], $cache_id, $cache_path))
{
	$Vars = $cache->GetVars();
	foreach($Vars["arResult"] as $k=>$v)
		$arResult[$k] = $v;
	CBitrixComponentTemplate::ApplyCachedData($Vars["templateCachedData"]);	
	$cache->Output();
}
else
{
	if ($arParams["CACHE_TIME"] > 0)
		$cache->StartDataCache($arParams["CACHE_TIME"], $cache_id, $cache_path);

	$arFilter = Array(
			"BLOG_ACTIVE" => "Y",
			"BLOG_GROUP_SITE_ID" => SITE_ID,
			">PERMS" => BLOG_PERMS_DENY,
			"BLOG_POST_PUBLISH_STATUS" => BLOG_PUBLISH_STATUS_PUBLISH,
			"PUBLISH_STATUS" => BLOG_PUBLISH_STATUS_PUBLISH,
		);	
	if(strlen($arParams["BLOG_URL"]) > 0)
		$arFilter["BLOG_URL"] = $arParams["BLOG_URL"];
	if(!empty($arParams["GROUP_ID"]))
		$arFilter["BLOG_GROUP_ID"] = $arParams["GROUP_ID"];
	if($USER->IsAdmin())
		unset($arFilter[">PERMS"]);

	$arSelectedFields = array("ID", "BLOG_ID", "POST_ID", "PARENT_ID", "AUTHOR_ID", "AUTHOR_NAME", "AUTHOR_EMAIL", "AUTHOR_IP", "AUTHOR_IP1", "TITLE", "POST_TEXT", "BLOG_URL", "DATE_CREATE", "BLOG_ACTIVE", "BLOG_GROUP_ID", "BLOG_GROUP_SITE_ID", "BLOG_OWNER_ID", "BLOG_SOCNET_GROUP_ID");

	if(CModule::IncludeModule("socialnetwork") && IntVal($arParams["SOCNET_GROUP_ID"]) <= 0 && IntVal($arParams["USER_ID"]) <= 0 && $arParams["USE_SOCNET"] == "Y")
	{
		unset($arFilter[">PERMS"]);
		$arSelectedFields[] = "SOCNET_BLOG_READ";
		$arFilter["BLOG_USE_SOCNET"] = "Y";
	}
	elseif((IntVal($arParams["SOCNET_GROUP_ID"]) > 0 || IntVal($arParams["USER_ID"]) > 0) && $arParams["USE_SOCNET"] == "Y")
	{
		$user_id = $USER->GetID();
		$arFilterTmp = Array("ACTIVE" => "Y", "GROUP_SITE_ID" => SITE_ID, "USE_SOCNET" => "Y");

		if(IntVal($arParams["SOCNET_GROUP_ID"]) > 0)
			$arFilterTmp["SOCNET_GROUP_ID"] = $arParams["SOCNET_GROUP_ID"];
		if(IntVal($arParams["USER_ID"]) > 0)
			$arFilterTmp["OWNER_ID"] = $arParams["USER_ID"];
		if(!empty($arParams["GROUP_ID"]))
			$arFilterTmp["GROUP_ID"] = $arParams["GROUP_ID"];

		$perms = BLOG_PERMS_DENY;
		$dbBlog = CBlog::GetList(Array(), $arFilterTmp, false, Array("nTopCount" => 1), Array("ID"));
		if($arBlog = $dbBlog->Fetch())
		{
			if(IntVal($arParams["SOCNET_GROUP_ID"]) > 0)
			{
				$perms = BLOG_PERMS_DENY;
				if (CSocNetFeaturesPerms::CanPerformOperation($user_id, SONET_ENTITY_GROUP, $arParams["SOCNET_GROUP_ID"], "blog", "full_post", CSocNetUser::IsCurrentUserModuleAdmin()) || $APPLICATION->GetGroupRight("blog") >= "W")
					$perms = BLOG_PERMS_FULL;
				elseif (CSocNetFeaturesPerms::CanPerformOperation($user_id, SONET_ENTITY_GROUP, $arParams["SOCNET_GROUP_ID"], "blog", "write_post"))
					$perms = BLOG_PERMS_WRITE;
				elseif (CSocNetFeaturesPerms::CanPerformOperation($user_id, SONET_ENTITY_GROUP, $arParams["SOCNET_GROUP_ID"], "blog", "view_post"))
					$perms = BLOG_PERMS_READ;
			}
			else
			{
				$perms = BLOG_PERMS_DENY;
				if (CSocNetFeaturesPerms::CanPerformOperation($user_id, SONET_ENTITY_USER, $arParams["USER_ID"], "blog", "full_post", CSocNetUser::IsCurrentUserModuleAdmin()) || $APPLICATION->GetGroupRight("blog") >= "W" || $arParams["USER_ID"] == $user_id)
					$perms = BLOG_PERMS_FULL;
				elseif (CSocNetFeaturesPerms::CanPerformOperation($user_id, SONET_ENTITY_USER, $arParams["USER_ID"], "blog", "write_post"))
					$perms = BLOG_PERMS_WRITE;
				elseif (CSocNetFeaturesPerms::CanPerformOperation($user_id, SONET_ENTITY_USER, $arParams["USER_ID"], "blog", "view_post"))
					$perms = BLOG_PERMS_READ;
			}
			$arFilter["BLOG_ID"] = $arBlog["ID"];
			unset($arFilter[">PERMS"]);
		}
	}

	if(strlen($perms) <= 0 || (!empty($arFilter["BLOG_ID"]) && $perms >= BLOG_PERMS_READ))
	{
		$SORT = Array($arParams["SORT_BY1"]=>$arParams["SORT_ORDER1"], $arParams["SORT_BY2"]=>$arParams["SORT_ORDER2"]);
		
		if($arParams["COMMENT_COUNT"]>0)
			$COUNT = Array("nTopCount" => $arParams["COMMENT_COUNT"]);
		else
			$COUNT = false;

		$arResult = Array();
		$dbComment = CBlogComment::GetList(
			$SORT,
			$arFilter,
			false,
			$COUNT,
			$arSelectedFields
		);
		global $CACHE_MANAGER;
		$CACHE_MANAGER->StartTagCache($cache_path);
		$p = new blogTextParser(false, $arParams["PATH_TO_SMILE"]);
		$itemCnt = 0;
		while ($arComment = $dbComment->GetNext())
		{
		
			$text = preg_replace("#\[img\](.+?)\[/img\]#ie", "", $arComment["~POST_TEXT"]);
			$text = preg_replace("#\[url(.+?)\](.*?)\[/url\]#is", "\\2", $text);
			$text = preg_replace("#(\[|<)(/?)(b|u|i|list|code|quote|url|img|color|font|/*)(.*?)(\]|>)#is", "", $text);
			$text = TruncateText($text, $arParams["MESSAGE_LENGTH"]);
			$text = $p->convert($text, false, false, array("HTML" => "N", "ANCHOR" => "N", "BIU" => "N", "IMG" => "N", "QUOTE" => "N", "CODE" => "N", "FONT" => "N", "LIST" => "N", "SMILES" => "Y", "NL2BR" => "N", "VIDEO" => "N"));
			$arComment["TEXT_FORMATED"] = $text;
			
			if(IntVal($arComment["AUTHOR_ID"])>0)
			{
				$arComment["urlToAuthor"] = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_USER"], array("user_id" => $arComment["AUTHOR_ID"]));
				$arComment["BlogUser"] = CBlogUser::GetByID($arComment["AUTHOR_ID"], BLOG_BY_USER_ID); 
				$arComment["BlogUser"] = CBlogTools::htmlspecialcharsExArray($arComment["BlogUser"]);
				$dbUser = CUser::GetByID($arComment["AUTHOR_ID"]);
				$arComment["arUser"] = $dbUser->GetNext();
				$arComment["AuthorName"] = CBlogUser::GetUserName($arComment["BlogUser"]["ALIAS"], $arComment["arUser"]["NAME"], $arComment["arUser"]["LAST_NAME"], $arComment["arUser"]["LOGIN"]);
				$arComment["Blog"] = CBlog::GetByOwnerID(IntVal($arComment["AUTHOR_ID"]), $arParams["GROUP_ID"]);
				if(!empty($arComment["Blog"]))
					$arComment["urlToBlog"] = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_BLOG"], array("blog" => $arComment["Blog"]["URL"], "user_id" => $arComment["AUTHOR_ID"]));
				else
					$arComment["urlToBlog"] = $arComment["urlToAuthor"];
			}
			else
			{
				$arComment["AuthorName"]  = $arComment["AUTHOR_NAME"];
				$arComment["AuthorEmail"]  = $arComment["AUTHOR_EMAIL"];
			}
			
			if(IntVal($arComment["BLOG_SOCNET_GROUP_ID"]) > 0)
				$arComment["urlToComment"] = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_GROUP_BLOG_POST"], array("blog" => $arComment["BLOG_URL"], "post_id"=>$arComment["POST_ID"], "group_id" => $arComment["BLOG_SOCNET_GROUP_ID"]));
			else
				$arComment["urlToComment"] = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_POST"], array("blog" => $arComment["BLOG_URL"], "post_id"=>$arComment["POST_ID"], "user_id" => $arComment["BLOG_OWNER_ID"]));

			if(strpos($arComment["urlToComment"], "?") !== false)
				$arComment["urlToComment"] .= "&";
			else
				$arComment["urlToComment"] .= "?";
			$arComment["urlToComment"] .= $arParams["COMMENT_ID_VAR"]."=".$arComment["ID"]."#".$arComment["ID"];
			
			$arComment["AVATAR_file"] = CFile::GetFileArray($arComment["BlogUser"]["AVATAR"]);
			if ($arComment["AVATAR_file"] !== false)
				$arComment["AVATAR_img"] = CFile::ShowImage($arComment["AVATAR_file"]["SRC"], 100, 100, "border=0 align='right'");

			if(strlen($arComment["TITLE"])>0)
				$arComment["TitleFormated"] = $p->convert($arComment["~TITLE"], false);
			$arComment["DATE_CREATE_FORMATED"] = date($arParams["DATE_TIME_FORMAT"], MakeTimeStamp($arComment["DATE_CREATE"], CSite::GetDateFormat("FULL")));
			if($itemCnt==0)
				$arComment["FIRST"] = "Y";
				
			$itemCnt++;
			
			//$arResult[ $arComment["ID"] ] = $arComment;
			$arReplies[ $arComment["ID"] ] = $arComment;
			$arDates[ $arComment["ID"] ] = strtotime($arComment["DATE_CREATE"]);
		}
		$arResult = array();
		$arResult["ITEMS"] = array();
		$arResult["RECIPES"] = array();
		require_once($_SERVER["DOCUMENT_ROOT"].'/classes/comment.class.php');
		CModule::IncludeModule("iblock");
		$obComment = CFClubComment::getInstance();
		if($arrComments = $obComment->getLastRepliesNew("10")){			
			foreach($arrComments["ITEMS"] as $arComment){
				$arComments[ $arComment["ID"] ] = $arComment;
				$arDates[ $arComment["ID"] ] = strtotime($arComment["DATE_CREATE"]);
			}
			if(!empty($arrComments["RECIPES"])){				
				$arRecipes = array_unique($arrComments["RECIPES"]);				
				$rsRecipes = CIBlockElement::GetList(array(),array("IBLOCK_ID" => 5, "ID" => $arRecipes),false,false,array("ID","CODE"));
				while($arRecipe = $rsRecipes->GetNext()){
					$arResult["RECIPES"][ $arRecipe["ID"] ] = $arRecipe["CODE"];
				}
			}
		}
		asort($arDates);
		$i = 0;
		foreach($arDates as $key => $date){
			//$i++;
			//if($i <= 6){
				if(!empty($arReplies[$key]) > 0){
					$arResult["ITEMS"][] = $arReplies[$key];
				}elseif(!empty($arComments[$key])){
					$arResult["ITEMS"][] = $arComments[$key];
				}
			//}
		}
		$array = array();
		for($i = count($arResult["ITEMS"]);$i >= (count($arResult["ITEMS"]) - 10); $i--){			
			if(!empty($arResult["ITEMS"][ $i ])){
				$array[] = $arResult["ITEMS"][ $i ];
			}			
		}
		$arResult["ITEMS"] = $array;
		//$arResult = $array;
		$CACHE_MANAGER->RegisterTag("CommentsIndexTag");
		$CACHE_MANAGER->EndTagCache();		
	}

	if ($arParams["CACHE_TIME"] > 0)
		$cache->EndDataCache(array("templateCachedData" => $this->GetTemplateCachedData(), "arResult" => $arResult));
}
$this->IncludeComponentTemplate();
?>