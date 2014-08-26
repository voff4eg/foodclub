<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$UserId = $arParams["USER"]["ID"];
$obCache = new CPHPCache;

CModule::IncludeModule("blog");
CModule::IncludeModule("socialnetwork");
CModule::IncludeModule("iblock");

$cacheid = "profile_".SITE_ID."_".$UserId."_lenta";
if(isset($_REQUEST["PAGEN_1"]) && intval($_REQUEST["PAGEN_1"])){
	$cacheid .= "_".intval($_REQUEST["PAGEN_1"]);
}else{
	$cacheid .= "_1";
}

if($obCache->InitCache(3600, $cacheid, "/profile")){
	$arResult = $obCache->GetVars();
}elseif($obCache->StartDataCache()){

    global $CACHE_MANAGER;
	$CACHE_MANAGER->StartTagCache("/profile_".$UserId);

	$rsRel = CSocNetUserToGroup::GetList(Array(), Array("USER_ID"=>$UserId), false, false);
	while($rs = $rsRel->GetNext()){
	    $arGroupProp['ID'][] = $rs['GROUP_ID'];
	}

	$arBlogs = array();

	if(!is_null($arGroupProp)){
		$rsGroup = CSocNetGroup::GetList(Array(), $arGroupProp, false, false);

		/**
		 * TODO Необходимо собрать уникальные ID блогов и запрашивать информацию только по ним вне цикла.
		 */
		while ($arGroup = $rsGroup->GetNext()) {
			$arBlog = CBlog::GetBySocNetGroupID($arGroup['ID']);
			$arBlogs[ $arBlog['ID'] ] = $arGroup;
		}
		$arFilter["<DATE_PUBLISH"] = ConvertTimeStamp($to, "FULL");
		$dbPosts = CBlogPost::GetList(  Array("DATE_PUBLISH" => "DESC", "NAME" => "ASC"), 
										Array("PUBLISH_STATUS" => BLOG_PUBLISH_STATUS_PUBLISH, "BLOG_ID" => array_keys($arBlogs),
										"<=DATE_PUBLISH"=>ConvertTimeStamp(date(), "FULL"),
										),
										false,
										Array('nPageSize'=>5)
									 );
		$Nav_string = $dbPosts->GetPageNavString("", "blogs");
		
		//Parser class
		$parser = new blogTextParser;
	}

	$Posts = array();
	if(!is_null($arGroupProp)){
		
		while ($arPost = $dbPosts->Fetch()){
			
			$dbCategory = CBlogPostCategory::GetList(Array("NAME" => "ASC"), Array("BLOG_ID" => $arPost["BLOG_ID"], "POST_ID" => $arPost["ID"]));
			while($arCategory = $dbCategory->GetNext()){
				$arCatTmp = $arCategory;
				$arCatTmp["urlToCategory"] = "/blogs/group/".$arBlogs[ $arPost['BLOG_ID'] ]['ID']."/blog/?category=".$arCatTmp['CATEGORY_ID'];
				$arPost["CATEGORY"][] = $arCatTmp;
			}
										
			$arPost['urlToDelete'] = "/blogs/group/".$arBlogs[ $arPost['BLOG_ID'] ]['ID']."/blog/?del_id=".$arPost['ID'];
			$res = CBlogImage::GetList(array("ID"=>"ASC"), array("POST_ID"=>$arPost['ID'], "BLOG_ID"=>$arPost['BLOG_ID']));
			
			while ($arImage = $res->Fetch())
			    $arImages[$arImage['ID']] = $arImage['FILE_ID'];
			
			$text = $parser->convert($arPost['DETAIL_TEXT'], true, $arImages);
			$arPost["DETAIL_TEXT"] = $text;

			if (preg_match("/(\[CUT\])/i",$arPost['DETAIL_TEXT']) || preg_match("/(<CUT>)/i",$arPost['DETAIL_TEXT']))
				$arPost["CUT"] = "Y";
		    
			$rsUser = CUser::GetByID($arPost['AUTHOR_ID']);
			$arUser = $rsUser->Fetch();

			if(strlen($arUser["NAME"]) > 0 && strlen($arUser["LAST_NAME"]) > 0){
		     	$arUser['FULLNAME'] = $arUser["NAME"]." ".$arUser["LAST_NAME"];
		 	}else{
		 		$arUser['FULLNAME'] = $arUser['LOGIN'];	 	
		 	}		 	
			
			if(intval($arUser['PERSONAL_PHOTO']) > 0){
				$rsAvatar = CFile::GetByID($arUser['PERSONAL_PHOTO']);
				$arAvatar = $rsAvatar->Fetch();
				$arAvatar['SRC'] = "/upload/".$arAvatar['SUBDIR']."/".$arAvatar['FILE_NAME'];
			} else {
				$arAvatar['SRC'] = "/images/avatar/avatar_small.jpg";
			}
			$arUser["AVATAR"] = $arAvatar;

			$arPost["USER"] = $arUser;
			
			$arDate = explode(" ", $arPost['DATE_PUBLISH']);

			$arPost["DATE"] = $arDate;

			$Posts[] = $arPost;
		}
	}
	
	$arResult = array(
		"Posts" => $Posts,
		"arBlogs" => $arBlogs,
		"Nav_string" => $Nav_string
	);
	$CACHE_MANAGER->RegisterTag("profile_".$UserId."_lenta");	
	$CACHE_MANAGER->EndTagCache();
	$obCache->EndDataCache(
		$arResult
	);
}else{
	$arResult = array();	
}

$this->IncludeComponentTemplate();
?>