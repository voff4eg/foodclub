<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$UserId = $arParams["USER"]["ID"];

CModule::IncludeModule("blog");
CModule::IncludeModule("socialnetwork");
include_once($_SERVER['DOCUMENT_ROOT'].'/classes/factory.class.php');

$cacheid = "profile_".SITE_ID."_".$UserId."_comments";
if(isset($_REQUEST["PAGEN_1"]) && intval($_REQUEST["PAGEN_1"])){
	$cacheid .= "_".intval($_REQUEST["PAGEN_1"]);
}else{
	$cacheid .= "_1";
}

$obCache = new CPHPCache;
if($obCache->InitCache(3600, $cacheid, "/profile")){
	$arResult = $obCache->GetVars();
	/*$arRecipe = $vars["arRecipe"];
	$arKitchensId = $vars["arKitchensId"];
	$arKitchens = $vars["arKitchens"];*/	
	/*require_once($_SERVER["DOCUMENT_ROOT"]."/classes/main.class.php");
	$CFClub = new CFClub();
	if(CModule::IncludeModule("iblock")){
		$arRecipe = $CFClub->getList(30, Array("CREATED_BY"=>$UserId), "blogs", "N");
		$arKitchensId = array_keys($arRecipe['Kitchen']);
		$arKitchens = $CFClub->getKitchens(false, $arKitchensId);
	}*/
}elseif($obCache->StartDataCache()){
    global $CACHE_MANAGER;
	$CACHE_MANAGER->StartTagCache("/profile_".$UserId);

	if(CModule::IncludeModule("iblock")){
		$rsComments = CBlogComment::GetList(Array("DATE_CREATE"=>"DESC"), Array("AUTHOR_ID"=>$UserId), false, Array("nPageSize"=>20), Array("ID", "POST_TEXT", "DATE_CREATE", "AUTHOR_ID", "POST_ID", "BLOG_ID"));
		if($rsComments->isNavPrint()){
			$NavString = $rsComments->GetPageNavStringEx($navComponentObject, "Рецепты", "blogs", "N");
		}
		$BlogsId = array();
		$PostsId = array();
		while ($Item = $rsComments->GetNext()) {
			$Comments[ $Item['ID'] ] = $Item;
			$BlogsId[] = $Item['BLOG_ID'];
			$PostsId[] = $Item['POST_ID'];
		}
		$BlogsId = array_unique($BlogsId);
		$BlogsId = array_values($BlogsId);

		$PostsId = array_unique($PostsId);
		$PostsId = array_values($PostsId);

		/*
		 * Получение id групп в социальных сетях
		 * $SocNetBlogs - массив, в котором - ключ:id блога, значение:id группы
		 */
		if(!empty($BlogsId)){
			$rsBlogs = CBlog::GetList(Array(), Array("ID"=>$BlogsId), false, false);
			while ($Blog = $rsBlogs->GetNext()) {
				$SocNetBlogs[ $Blog['ID'] ] = $Blog['SOCNET_GROUP_ID'];
			}
		}else{
			$SocNetBlogs = array();
		}

		/*
		 * Получение названий групп социальных сетей
		 */
		if(is_array($SocNetBlogs) && !empty($SocNetBlogs)){
			$rsSocNet = CSocNetGroup::GetList(Array(), Array('ID'=>array_values($SocNetBlogs)), false, false, Array('ID', 'NAME'));
			while($SocNet = $rsSocNet->GetNext()){
				$SocNetName[ $SocNet['ID'] ] = $SocNet;
			}
		}else{
			$SocNetName = array();
		}

		/*
		 * Получение названий топиков
		 */
		if(!empty($PostsId)){
			$rsTopics = CBlogPost::GetList(Array("DATE_CREATE"=>"DESC"), Array("ID"=>$PostsId, "PUBLISH_STATUS" => BLOG_PUBLISH_STATUS_PUBLISH,), false, false);
			while ($Item = $rsTopics->GetNext()) {
				$TopicsName[ $Item['ID'] ] = $Item;
			}
		}else{
			$TopicsName = array();
		}
	}
	
	$arResult = array(
		"comments" => $Comments,
		"TopicsName" => $TopicsName,
		"SocNetName" => $SocNetName,
		"SocNetBlogs" => $SocNetBlogs,
		"NavString" => $NavString
	);

	$CACHE_MANAGER->RegisterTag("profile_".$UserId."_comments");	
	$CACHE_MANAGER->EndTagCache();
	$obCache->EndDataCache(
		$arResult	
	);
}else{
	$arResult = array();	
}

$this->IncludeComponentTemplate();
?>