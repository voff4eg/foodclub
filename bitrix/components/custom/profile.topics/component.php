<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$UserId = $arParams["USER"]["ID"];
$obCache = new CPHPCache;

$cacheid = "profile_".SITE_ID."_".$UserId."_topics";
if(isset($_REQUEST["PAGEN_1"]) && intval($_REQUEST["PAGEN_1"])){
	$cacheid .= "_".intval($_REQUEST["PAGEN_1"]);
}else{
	$cacheid .= "_1";
}

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

	CModule::IncludeModule("blog");
	CModule::IncludeModule("socialnetwork");
	require_once($_SERVER['DOCUMENT_ROOT'].'/classes/factory.class.php');

	$Sort = Array("DATE_PUBLISH" => "DESC", "NAME" => "ASC");

	$arFilter = Array(
		"PUBLISH_STATUS" => BLOG_PUBLISH_STATUS_PUBLISH,
		"AUTHOR_ID" => $UserId,
	);

	$parser = new blogTextParser;

	$Posts = array();

	$dbPosts = CBlogPost::GetList($Sort, $arFilter, false, Array("nPageSize"=>5));

	if($dbPosts->IsNavPrint()){
		$NavString = $dbPosts->GetPageNavStringEx($navComponentObject, "Рецепты", "blogs", "N");
	}

	while ($arPost = $dbPosts->Fetch()){	    

	    $res = CBlogImage::GetList(array("ID"=>"ASC"), array("POST_ID"=>$arPost['ID'], "BLOG_ID"=>$arPost['BLOG_ID']));
	
		while ($arImage = $res->Fetch())
		    $arImages[$arImage['ID']] = $arImage['FILE_ID'];
		
		$text = $parser->convert($arPost['DETAIL_TEXT'], true, $arImages);
		$arPost["DETAIL_TEXT"] = $text;
		
		$arDate = explode(" ", $arPost['DATE_PUBLISH']);
		$arPost["arDate"] = $arDate;
/*
		$rsUser = CUser::GetByID($arPost['AUTHOR_ID']);
		$arUser = $rsUser->Fetch();
		$arPost["arUser"] = $arUser;
		
		if(intval($arUser['PERSONAL_PHOTO']) > 0){
			$rsAvatar = CFile::GetByID($arUser['PERSONAL_PHOTO']);
			$arAvatar = $rsAvatar->Fetch();			
			$arPost["arUser"]["avatar"] = "/upload/".$arAvatar['SUBDIR']."/".$arAvatar['FILE_NAME'];
		} else {
			$arPost["arUser"]["avatar"] = "/images/avatar/avatar_small.jpg";
		}*/

		$Posts[ $arPost['ID'] ] = $arPost;

	    $BlogsId[] = $arPost['BLOG_ID'];
	}

	$BlogsId = array_unique($BlogsId);

	/*
	 * Получение id групп в социальных сетях
	 * $SocNetBlogs - массив, в котором - ключ:id блога, значение:id группы
	 */
	$rsBlogs = CBlog::GetList(Array(), Array("ID"=>$BlogsId), false, false);
	while ($Blog = $rsBlogs->GetNext()) {
		$SocNetBlogs[ $Blog['ID'] ] = $Blog['SOCNET_GROUP_ID'];
	}

	/*
	 * Получение названий групп социальных сетей
	 */
	$rsSocNet = CSocNetGroup::GetList(Array(), Array('ID'=>array_values($SocNetBlogs)), false, false, Array('ID', 'NAME'));
	while($SocNet = $rsSocNet->GetNext()){
		$SocNetName[ $SocNet['ID'] ] = $SocNet;
	}

	$arResult = array(
		"Posts" => $Posts,
		"SocNetBlogs" => $SocNetBlogs,
		"SocNetName" => $SocNetName,
		"NavString" => $NavString,
		"arUser" => $arParams["USER"]
	);

	$CACHE_MANAGER->RegisterTag("profile_".$UserId."_topics");	
	$CACHE_MANAGER->EndTagCache();
	$obCache->EndDataCache(
		$arResult
	);	
}else{
	$arResult = array();	
}

$this->IncludeComponentTemplate();
?>