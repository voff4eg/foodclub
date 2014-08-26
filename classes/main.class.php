<?
class CFClub
{
	static private $_instance = null;
	const REC_IBLOCK = 5;
	const TOPCOOKRATEDINDEX = "users_index";
	const TOPCOOKRATEDNOTINDEX = "users_not_index";
		
	public function getKitchens($bFull = false, $Chose = Array(), $site_id = "s1")
	{
		if(count($Chose) >  0){
			$Prop = Array('ACTIVE'=>'Y', 'ID' => $Chose, 'SITE_ID' => $site_id);
		} else {
			$Prop = Array('ACTIVE'=>'Y', 'SITE_ID' => $site_id);
		}
		if($site_id == "s1"){
		    $Prop['IBLOCK_CODE'] = 'kitchens';
		}elseif($site_id == "fr"){
		    $Prop['IBLOCK_CODE'] = 'kitchens_fr';
		}
		
		$rsKitchens = CIBlockElement::GetList(Array('NAME'=>'ASC'), $Prop);
		
		if(!$bFull){
			while ($arKitchen = $rsKitchens->GetNext())
			{
				$arResult[ $arKitchen['ID'] ] = Array("ID" => $arKitchen['ID'], "NAME" => $arKitchen['NAME']);
			}
		} else {
			while ($obKitchen = $rsKitchens->GetNextElement())
			{
				$arFields = $obKitchen->GetFields();
				$arProp   = $obKitchen->GetProperties();
				$arFields['dish'] = $arProp['dish_type']['VALUE'];
				$arDishType = $this->getDishType(true, $arFields['dish']);
				$arResult[ $arFields['ID'] ] = Array("ID" => $arFields['ID'], "NAME" => $arFields['NAME'], "DISH" => $arDishType);
			}
		}
		return $arResult;
	}
	
	static public function getDishList($site_id = "s1"){
	    if($site_id == "s1"){
		$rsDish = CIBlockElement::GetList(Array('NAME'=>'ASC'), Array('IBLOCK_CODE'=>'dish_type', 'ACTIVE'=>'Y'));
	    }elseif($site_id == "fr"){
		$rsDish = CIBlockElement::GetList(Array('NAME'=>'ASC'), Array('IBLOCK_CODE'=>'fr_dish_type', 'ACTIVE'=>'Y'));
	    }
		$arDishs = Array();
		while($arDish = $rsDish->GetNext()){
			$arDishs[ $arDish['ID'] ] = $arDish;
		}
		return $arDishs;
	}

	static public function getTags(){
		if(CModule::IncludeModule('search'))
		{
			$rsTags = CSearchTags::GetList(
				array(),
				array(
					"MODULE_ID" => "iblock",
				),
				array(
					"CNT" => "DESC",
				),
				$SqlReqestCount
			);
			while($arTag = $rsTags->Fetch()){
				$allTags[]=$arTag['NAME'];				
			}
			
			return $allTags;
		}
	}
	
	static public function getDishType($bShowEmpty = true, $arDish = Array())
	{
		$arResult = Array();
		if(count($arDish) > 0){
			if($bShowEmpty == true){
				foreach ($arDish as $strItem)
				{
					$rsDishType = CIBlockElement::GetById($strItem);
					$arDishType = $rsDishType->GetNext();
					$arResult[ $arDishType['ID'] ] = Array("ID"=>$arDishType['ID'], "NAME"=>$arDishType['NAME'], "IMAGE"=>$arDishType['PREVIEW_PICTURE']['SRC']);
				}
			} else {
				foreach ($arDish as $strItem)
				{
					$rsDishType = CIBlockElement::GetById($strItem);
					$arDishType = $rsDishType->GetNext();
					$rsRecipe = CIBlockElement::GetList(Array(), Array("IBLOCK_ID"=>self::REC_IBLOCK, "ACTIVE"=>"Y", "PROPERTY_dish_type"=>$arDishType['ID']), false, false);
					$intCount = $rsRecipe->SelectedRowsCount();
					if(intval($intCount) > 0){$arResult[ $arDishType['ID'] ] = Array("ID"=>$arDishType['ID'], "NAME"=>$arDishType['NAME'], "IMAGE"=>$arDishType['PREVIEW_PICTURE']['SRC']);}
				}
			}
			
		} else {
			$rsSections = CIBlockElement::GetList(Array('NAME'=>'ASC'), Array('IBLOCK_CODE'=>'dish_type', 'ACTIVE'=>'Y'));
			
			if($bShowEmpty == true){
				while($arSection = $rsSections->GetNext()){
					$arResult[ $arSection['ID'] ] = $arSection;
				}
			}
			
		}
		return $arResult;
	}
	
	static public function getUnitList($site_id="s1", $format = "")	
	{
		$rsSections = CIBlockSection::GetList(Array('NAME'=>'ASC'), Array('IBLOCK_CODE'=>'ingredients', 'ACTIVE'=>'Y'));
		while ($arSection = $rsSections->GetNext())
		{
			$arResult[ $arSection['ID'] ] = Array("ID" => $arSection['ID'], "NAME" => $arSection['NAME']);
		}

		$rsUnits = CIBlockElement::GetList(Array('NAME'=>'ASC'), Array('IBLOCK_CODE'=>'ingredients', 'ACTIVE'=>'Y'), false, false, Array("ID", "PREVIEW_PICTURE", "IBLOCK_SECTION_ID", "NAME", "CODE", "DETAIL_TEXT", "PROPERTY_unit", "PROPERTY_fr_name", "PROPERTY_fr_unit"));

		switch ($format) { // /search_service/?id=$& /ingredient/$&/
			case 'js':				
				while ($arUnits = $rsUnits->GetNext())
				{
					if(IntVal($arUnits['PREVIEW_PICTURE']) > 0){
						$rsFile = CFile::GetByID(IntVal($arUnits['PREVIEW_PICTURE']));
						$arFile = $rsFile->Fetch();
						$arFile['SRC'] = "/upload/".$arFile['SUBDIR']."/".$arFile['FILE_NAME'];
						$arUnits["PREVIEW_PICTURE"] = $arFile;
					}
				    if($site_id == "s1"){
				    	if(strlen($arUnits["DETAIL_TEXT"])){
				    		if(strlen($arUnits["CODE"])){
				    			$arResult[ $arUnits['IBLOCK_SECTION_ID'] ]['UNITS'][] = Array("ID" => "ingredient/".$arUnits['CODE']."/", "NAME" => $arUnits['NAME'], "UNIT" => $arUnits['PROPERTY_UNIT_VALUE'], "PREVIEW_PICTURE" => $arUnits["PREVIEW_PICTURE"]);
				    		}else{
				    			$arResult[ $arUnits['IBLOCK_SECTION_ID'] ]['UNITS'][] = Array("ID" => "ingredient/".$arUnits['ID']."/", "NAME" => $arUnits['NAME'], "UNIT" => $arUnits['PROPERTY_UNIT_VALUE'], "PREVIEW_PICTURE" => $arUnits["PREVIEW_PICTURE"]);
				    		}
				    	}else{
				    		$arResult[ $arUnits['IBLOCK_SECTION_ID'] ]['UNITS'][] = Array("ID" => "search_service/?id=".$arUnits['ID'], "NAME" => $arUnits['NAME'], "UNIT" => $arUnits['PROPERTY_UNIT_VALUE'], "PREVIEW_PICTURE" => $arUnits["PREVIEW_PICTURE"]);
				    	}						
				    }elseif($site_id == "fr"){
						$arResult[ $arUnits['IBLOCK_SECTION_ID'] ]['UNITS'][] = Array("ID" => $arUnits['ID'], "NAME" => (strlen($arUnits["PROPERTY_FR_NAME_VALUE"]) > 0 ? $arUnits["PROPERTY_FR_NAME_VALUE"] : $arUnits['NAME']), "UNIT" => (strlen($arUnits["PROPERTY_FR_UNIT_VALUE"]) > 0 ? $arUnits["PROPERTY_FR_UNIT_VALUE"] : $arUnits['PROPERTY_UNIT_VALUE']), "PREVIEW_PICTURE" => $arUnits["PREVIEW_PICTURE"]);
				    }
				}
				break;
			
			default:
				while ($arUnits = $rsUnits->GetNext())
				{
					if(IntVal($arUnits['PREVIEW_PICTURE']) > 0){
						$rsFile = CFile::GetByID(IntVal($arUnits['PREVIEW_PICTURE']));
						$arFile = $rsFile->Fetch();
						$arFile['SRC'] = "/upload/".$arFile['SUBDIR']."/".$arFile['FILE_NAME'];
						$arUnits["PREVIEW_PICTURE"] = $arFile;
					}
				    if($site_id == "s1"){
						$arResult[ $arUnits['IBLOCK_SECTION_ID'] ]['UNITS'][] = Array("ID" => $arUnits['ID'], "NAME" => $arUnits['NAME'], "UNIT" => $arUnits['PROPERTY_UNIT_VALUE'], "PREVIEW_PICTURE" => $arUnits["PREVIEW_PICTURE"]);
				    }elseif($site_id == "fr"){
						$arResult[ $arUnits['IBLOCK_SECTION_ID'] ]['UNITS'][] = Array("ID" => $arUnits['ID'], "NAME" => (strlen($arUnits["PROPERTY_FR_NAME_VALUE"]) > 0 ? $arUnits["PROPERTY_FR_NAME_VALUE"] : $arUnits['NAME']), "UNIT" => (strlen($arUnits["PROPERTY_FR_UNIT_VALUE"]) > 0 ? $arUnits["PROPERTY_FR_UNIT_VALUE"] : $arUnits['PROPERTY_UNIT_VALUE']), "PREVIEW_PICTURE" => $arUnits["PREVIEW_PICTURE"]);
				    }
				}
				break;
		}		
		
		
		return $arResult;
	}
	
	static public function getRecipesTree()
	{
		$rsRecipes = CIBlockElement::GetList(Array("SORT"=>"ASC"), Array("IBLOCK_CODE"=>"recipe", "ACTIVE"=>"Y"), false, false, Array("ID", "NAME", "CODE", "PROPERTY_dish_type", "PROPERTY_kitchen"));
		while($arRecipe = $rsRecipes->GetNext()){
			$arResult[ $arRecipe['PROPERTY_KITCHEN_VALUE'] ][ $arRecipe['PROPERTY_DISH_TYPE_VALUE'] ][] = $arRecipe;
		}
		return $arResult;
	}
	
	static public function getRecipesCount()
	{
		$rsRecipes = CIBlockElement::GetList(Array("SORT"=>"ASC"), Array("IBLOCK_CODE"=>"recipe","SITE_ID"=>SITE_ID), array("IBLOCK_ID"), false, Array("ID", "NAME", "PROPERTY_dish_type", "PROPERTY_kitchen"));
		if($arRecipe = $rsRecipes->GetNext()){
			$count = $arRecipe["CNT"];
		}
		return $count;
	}
	//Рецепты пользователя
	static public function getUserRecipesCount($iUserID)
	{
		if(intval($iUserID)){
			CModule::IncludeModule("iblock");
			$rsRecipes = CIBlockElement::GetList(Array("SORT"=>"ASC"), Array("IBLOCK_CODE"=>"recipe","SITE_ID"=>SITE_ID,"CREATED_BY"=>$iUserID), array("IBLOCK_ID"), false, Array("ID", "NAME", "PROPERTY_dish_type", "PROPERTY_kitchen"));
			if($arRecipe = $rsRecipes->GetNext()){
				$count = $arRecipe["CNT"];
			}
			return $count;
		}else{
			return 0;
		}		
	}
	//Отзывы пользователя
	static public function getUserCommentsCount($iUserID)
	{
		if(intval($iUserID)){
			/*$rsRecipes = CIBlockElement::GetList(Array("SORT"=>"ASC"), Array("IBLOCK_CODE"=>"recipe","SITE_ID"=>SITE_ID,"CREATED_BY"=>$iUserID), array("IBLOCK_ID"), false, Array("ID", "NAME", "PROPERTY_dish_type", "PROPERTY_kitchen"));
			if($arRecipe = $rsRecipes->GetNext()){
				$count = $arRecipe["CNT"];
			}*/
			$count = array();
			$arSelectedFields = Array("ID", "BLOG_ID", "POST_ID", "PARENT_ID", "AUTHOR_ID", "AUTHOR_NAME", "AUTHOR_EMAIL", "AUTHOR_IP", "AUTHOR_IP1", "TITLE", "POST_TEXT", "DATE_CREATE");
			$rsReplies = CBlogComment::GetList(array(), array("AUTHOR_ID"=>$iUserID), array("COUNT"=>"AUTHOR_ID"),false,$arSelectedFields);
			if($arReply = $rsReplies->GetNext()){
				$count = $arReply["AUTHOR_ID"];
			}
			return $count;
		}else{
			return 0;
		}		
	}
	//Комментарии пользователя
	static public function getUserRepliesCount($iUserID)
	{
		if(intval($iUserID)){
			$rsComments = CIBlockElement::GetList(Array("SORT"=>"ASC"), Array("IBLOCK_CODE"=>"comments","SITE_ID"=>SITE_ID,"CREATED_BY"=>$iUserID), array("CREATED_BY"), false);
			if($arComment = $rsComments->GetNext()){
				$count = $arComment["CNT"];
			}
			return $count;
		}else{
			return 0;
		}		
	}
	//Записи пользователя
	static public function getUserPostCount($iUserID)
	{
		if(intval($iUserID)){
			$dbPost = CBlogPost::GetList(
				Array("SORT"=>"ASC"),
				array("AUTHOR_ID"=>$iUserID),
				array("COUNT"=>"AUTHOR_ID"),
				false,
				array("ID", "TITLE", "BLOG_ID", "AUTHOR_ID", "DETAIL_TEXT", "DETAIL_TEXT_TYPE", "DATE_CREATE", "DATE_PUBLISH", "KEYWORDS", "PUBLISH_STATUS", "ATRIBUTE", "ATTACH_IMG", "ENABLE_TRACKBACK", "ENABLE_COMMENTS", "VIEWS", "NUM_COMMENTS", "NUM_TRACKBACKS", "CATEGORY_ID", "CODE")
			);
			if($arPost = $dbPost->Fetch()){
				$count = $arPost["AUTHOR_ID"];	
			}

			return $count;
		}else{
			return 0;
		}		
	}

	//Последние рецепты пользователя
	static public function getUserLastRecipes($iUserID)
	{
		if(intval($iUserID)){
			$arRecipes = array();
			$arProp = array("IBLOCK_ID"=>self::REC_IBLOCK, "ACTIVE"=>"Y", /*"PROPERTY_lib"=>"Y",*/"CREATED_BY"=>$iUserID);
			$rsRecipes = CIBlockElement::GetList(Array("ACTIVE_FROM"=>"DESC","DATE_CREATE"=>"DESC","SITE_ID"=>SITE_ID), $arProp, false, array("nTopCount"=>3), Array("ID", "NAME", "CODE", "PREVIEW_TEXT", "PREVIEW_PICTURE", "CREATED_BY", "PROPERTY_dish_type", "PROPERTY_kitchen", "PROPERTY_comment_count", "DATE_ACTIVE_FROM", "DATE_ACTIVE_TO"));			
			while($arRecipe = $rsRecipes->GetNext()){

				if(IntVal($arRecipe['PREVIEW_PICTURE']) > 0){
					$rsFile = CFile::GetByID(IntVal($arRecipe['PREVIEW_PICTURE']));
					$arFile = $rsFile->Fetch();
					$arFile['SRC'] = "/upload/".$arFile['SUBDIR']."/".$arFile['FILE_NAME'];
					$arRecipe["PREVIEW_PICTURE"] = $arFile;
				}				

				$arRecipes[] = $arRecipe;
			}
			return $arRecipes;
		}else{
			return 0;
		}
	}

	//Последние записи в клубах пользователя
	static public function getUserLastPosts($iUserID)
	{
		if(intval($iUserID)){
			
			$arPosts = array();
			$BlogsId = array();
			$SocNetBlogs = array();
			$SocNetName = array();

			$dbPost = CBlogPost::GetList(
				Array("DATE_CREATE"=>"DESC"),
				array("AUTHOR_ID"=>$iUserID,"!BLOG_ID"=>false,"PUBLISH_STATUS" => BLOG_PUBLISH_STATUS_PUBLISH,),
				false,
				array("nTopCount"=>3),
				array("ID", "TITLE", "BLOG_ID", "AUTHOR_ID", "DETAIL_TEXT", "DETAIL_TEXT_TYPE", "DATE_CREATE", "DATE_PUBLISH", "KEYWORDS", "PUBLISH_STATUS", "ATRIBUTE", "ATTACH_IMG", "ENABLE_TRACKBACK", "ENABLE_COMMENTS", "VIEWS", "NUM_COMMENTS", "NUM_TRACKBACKS", "CATEGORY_ID", "CODE")
			);
			while($arPost = $dbPost->Fetch()){				
				if(intval($arPost["BLOG_ID"]) > 0){					
					/*$dbCategory = CBlogPostCategory::GetList(Array("NAME" => "ASC"), Array("BLOG_ID" => $arPost["BLOG_ID"], "POST_ID" => $arPost["ID"]));
					if($arCategory = $dbCategory->Fetch()){						
						
						$arCatTmp = $arCategory;
						$arCatTmp["urlToCategory"] = "/blogs/group/".$arBlogs[ $arPost['BLOG_ID'] ]['SOCNET_GROUP_ID']."/blog/?category=".$arCatTmp['CATEGORY_ID'];
						$arPost["CATEGORY"] = $arCatTmp["NAME"];						
					}*/					
					$BlogsId[] = $arPost['BLOG_ID'];
					$arPosts[] = $arPost;
				}
			}

			$BlogsId = array_unique($BlogsId);

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

			/*if($arBlog = CBlog::GetByID($arPost["BLOG_ID"])){						
				$arPost["HREF"] = "/blogs/group/".$arBlog['SOCNET_GROUP_ID']."/blog/".$arPost['ID']."/";
			}*/

			return array(
				"Posts" => $arPosts,
				"SocNetName" => $SocNetName,
				"SocNetBlogs" => $SocNetBlogs
			);
		}else{
			return 0;
		}
	}
	
	static public function getOnlineCount()
	{
		$rsData = CUserOnline::GetList($guest_count, $session_count, Array($by=>$order), array());
		while($arRes = $rsData->GetNext())
		{
			$arUsersOnline[] = $arRes["LAST_USER_ID"];
		}
		return count($arUsersOnline);
	}
	
	static public function getList($strPageSize = 5, $arFilter = Array(), $Template = "fclub", $Lib = "Y"){
		
		if(IntVal($strPageSize) > 0){
			$arNavStartParams = Array("nPageSize" => $strPageSize);
		} else {
			$arNavStartParams = false;
		}
		
		$arProp = Array("IBLOCK_ID"=>self::REC_IBLOCK, "ACTIVE"=>"Y");
		if(count($arFilter) > 0){
			foreach($arFilter as $strKey => $strItem){
				if($strKey == "k"){
					$arProp["PROPERTY_kitchen"] = IntVal($strItem);
				} elseif($strKey == "d"){
					$arProp["PROPERTY_dish_type"] = IntVal($strItem);
				} elseif($strKey == 'CREATED_BY'){
					$arProp["CREATED_BY"] = IntVal($strItem);
				} elseif($strKey == 'ID'){
					$arProp["ID"] = $strItem;
				}elseif($strKey == 'dates'){
					$arProp["ACTIVE_DATE"] = "Y";
					$arProp[">=DATE_ACTIVE_FROM"] = $strItem['from'];
				}elseif($strKey == 'site_id'){
				    if($strItem == "fr"){
					$arProp["IBLOCK_ID"] = "24";
				    }
				}
			}
		}				
		
		//echo "<pre>"; print_r($arProp); echo "</pre>";
		
		
		if($Lib == "Y"){
			$arProp['PROPERTY_lib'] = "Y";
		}
		
		$rsRecipes = CIBlockElement::GetList(Array("ACTIVE_FROM"=>"DESC","DATE_CREATE"=>"DESC"), $arProp, false, $arNavStartParams, Array("ID", "NAME", "CODE", "PREVIEW_TEXT", "PREVIEW_PICTURE", "CREATED_BY", "PROPERTY_dish_type", "PROPERTY_kitchen", "PROPERTY_comment_count", "DATE_ACTIVE_FROM", "DATE_ACTIVE_TO"));
		while($arRecipe = $rsRecipes->GetNext()){
		
			$rsUser = CUser::GetByID($arRecipe['CREATED_BY']);
			$arUser = $rsUser->Fetch();
			
			/*
			if(strpos($arUser['EXTERNAL_AUTH_ID'], "OPENID") !== false){
				if(strpos($arUser['LOGIN'], "livejournal") !== false){
					$arUser['FULL_LOGIN'] = $arUser['LOGIN'];
					$arUser['LOGIN'] = substr($arUser['LOGIN'], 7, (strpos($arUser['LOGIN'], ".livejournal")-7));
					$arUser['LOGIN_TYPE'] = "lj";
				}
			}
			*/
			if(strlen($arUser["NAME"]) > 0 && strlen($arUser["LAST_NAME"]) > 0){
		     	$arUser["FULLNAME"] = $arUser["NAME"]." ".$arUser["LAST_NAME"];
		 	}else{
		 		$arUser["FULLNAME"] = $arUser["LOGIN"];
		 	}
			$arRecipe['USER'] = $arUser;
			
			if(IntVal($arRecipe['PREVIEW_PICTURE']) > 0){
				$rsFile = CFile::GetByID(IntVal($arRecipe['PREVIEW_PICTURE']));
				$arFile = $rsFile->Fetch();
				$arFile['SRC'] = "/upload/".$arFile['SUBDIR']."/".$arFile['FILE_NAME'];
				$arRecipe["PREVIEW_PICTURE"] = $arFile;
			}
			$arResult['ITEMS'][ $arRecipe['ID'] ] = $arRecipe;
			$arResult['Kitchen'][ $arRecipe['PROPERTY_KITCHEN_VALUE'] ][] = $arRecipe['ID'];
		}
		
		if($rsRecipes->IsNavPrint()){
			$arResult["NAV_STRING"] = $rsRecipes->GetPageNavStringEx($navComponentObject, "Рецепты", $Template, "N");
		}		
		return $arResult;
	}
	
	static public function getLastFiveLibRecipes(){
		$arResult = array();
		

	    $obCache = new CPHPCache;
	    if($obCache->InitCache(86400, "LastFiveLibRecipes", "/LastFiveLibRecipes")){
			$arResult = $obCache->GetVars();
		}elseif($obCache->StartDataCache()){
			global $CACHE_MANAGER;
			$CACHE_MANAGER->StartTagCache("/LastFiveLibRecipes");
			$arProp = array("IBLOCK_ID"=>self::REC_IBLOCK, "ACTIVE"=>"Y", "PROPERTY_lib"=>"Y","SITE_ID"=>SITE_ID);
			//echo "<pre>";print_R($arProp);echo "</pre>";
			$rsRecipes = CIBlockElement::GetList(Array("ACTIVE_FROM"=>"DESC","DATE_CREATE"=>"DESC"), $arProp, false, array("nTopCount"=>5), Array("ID", "NAME", "CODE", "PREVIEW_TEXT", "PREVIEW_PICTURE", "CREATED_BY", "PROPERTY_dish_type", "PROPERTY_kitchen", "PROPERTY_comment_count", "DATE_ACTIVE_FROM", "DATE_ACTIVE_TO"));
			while($arRecipe = $rsRecipes->GetNext()){

				$rsUser = CUser::GetByID($arRecipe['CREATED_BY']);
				$arUser = $rsUser->Fetch();
				if(strlen($arUser["NAME"]) > 0 && strlen($arUser["LAST_NAME"]) > 0){
			     	$arUser["FULLNAME"] = $arUser["NAME"]." ".$arUser["LAST_NAME"];
			 	}else{
			 		$arUser["FULLNAME"] = $arUser["LOGIN"];
			 	}
				$arRecipe['USER'] = $arUser;
				
				if(IntVal($arRecipe['PREVIEW_PICTURE']) > 0){
					$rsFile = CFile::GetByID(IntVal($arRecipe['PREVIEW_PICTURE']));
					$arFile = $rsFile->Fetch();
					$arFile['SRC'] = "/upload/".$arFile['SUBDIR']."/".$arFile['FILE_NAME'];
					$arRecipe["PREVIEW_PICTURE"] = $arFile;
				}

				$arResult['ITEMS'][ $arRecipe['ID'] ] = $arRecipe;
				$arResult['AUTHOR_ID'][ $arRecipe['CREATED_BY'] ][] = $arRecipe['ID'];
				$arResult['Kitchen'][ $arRecipe['PROPERTY_KITCHEN_VALUE'] ][] = $arRecipe['ID'];
				$CACHE_MANAGER->RegisterTag("LastFiveLibRecipesTag_".$recipe["ID"]);
			}
			//echo "<pre>";print_R($arResult);echo "</pre>";die;
			$CACHE_MANAGER->EndTagCache();
			$obCache->EndDataCache($arResult);
		}else{
			$arResult = array();
		}

		return $arResult;	
	}

	static public function getTopRatedCookList($count = 10,$index = true){
		$obCache = new CPHPCache;
		$arResult = array();
		if($index){
			if($obCache->InitCache(86400, TOPCOOKRATEDINDEX, "/".TOPCOOKRATEDINDEX)){
				$arResult = $obCache->GetVars();
			}elseif($obCache->StartDataCache()){

				global $CACHE_MANAGER;
				$CACHE_MANAGER->StartTagCache("/".TOPCOOKRATEDINDEX);

				$cUser = new CUser;
				$arResult["USERS"] = array();
				$arFilter = array(
				   "ACTIVE" => 'Y',
				);
				$by_sort = "UF_RAITING";
				$ordr = "desc";				
				$arResult["USER_COUNT"] = $count;
				$dbUsers = $cUser->GetList($by_sort, $ordr, $arFilter,array("SELECT"=>array("UF_*"),"NAV_PARAMS"=>array("nPageSize"=>$count)));
				while ($arUser = $dbUsers->Fetch())
				{
					if(IntVal($arUser['PERSONAL_PHOTO']) > 0){
						$rsFile = CFile::GetByID(IntVal($arUser['PERSONAL_PHOTO']));
						$arFile = $rsFile->Fetch();
						$arFile['SRC'] = "/upload/".$arFile['SUBDIR']."/".$arFile['FILE_NAME'];
						$arUser["photo"] = $arFile;
					}
					$arResult["USERS"][] = $arUser;
					$arResult["USER_ID"][] = $arUser["ID"];
				}

				$CACHE_MANAGER->RegisterTag(TOPCOOKRATEDINDEX);
				$CACHE_MANAGER->EndTagCache();

				$obCache->EndDataCache($arResult);
			}else{
				$arResult = array();
			}
		}else{
			if($obCache->InitCache(86400, TOPCOOKRATEDNOTINDEX, "/".TOPCOOKRATEDNOTINDEX)){
				$arResult = $obCache->GetVars();
			}elseif($obCache->StartDataCache()){

				global $CACHE_MANAGER;
				$CACHE_MANAGER->StartTagCache("/".TOPCOOKRATEDNOTINDEX);

				$cUser = new CUser;
				$arResult["USERS"] = array();
				$arFilter = array(
				   "ACTIVE" => 'Y',
				);
				$by_sort = "UF_RAITING";
				$ordr = "desc";				
				$arResult["USER_COUNT"] = $count;
				$dbUsers = $cUser->GetList($by_sort, $ordr, $arFilter,array("SELECT"=>array("UF_*"),"NAV_PARAMS"=>array("nPageSize"=>$count)));
				while ($arUser = $dbUsers->Fetch())
				{
					if(IntVal($arUser['PERSONAL_PHOTO']) > 0){
						$rsFile = CFile::GetByID(IntVal($arUser['PERSONAL_PHOTO']));
						$arFile = $rsFile->Fetch();
						$arFile['SRC'] = "/upload/".$arFile['SUBDIR']."/".$arFile['FILE_NAME'];
						$arUser["photo"] = $arFile;
					}
					$arResult["USERS"][] = $arUser;
					$arResult["USER_ID"][] = $arUser["ID"];
				}

				$CACHE_MANAGER->RegisterTag(TOPCOOKRATEDNOTINDEX);
				$CACHE_MANAGER->EndTagCache();

				$obCache->EndDataCache($arResult);
			}else{
				$arResult = array();
			}
		}

		return $arResult;
	}
	
	static public function getMainIgredientList(){
		$rsMainIgredient = CIBlockElement::GetList(Array('NAME'=>'ASC'), Array('IBLOCK_CODE'=>'main_ingredient', 'ACTIVE'=>'Y', 'SITE_ID' => "s1"));
		$arMainIgredient = Array();
		while($arMainIgredient = $rsMainIgredient->GetNext()){
			$arMainIgredients[ $arMainIgredient['ID'] ] = $arMainIgredient;
		}
		return $arMainIgredients;
	}
	
	static public function getIdList($strPageSize = 5, $arFilter = Array(), $Template = "fclub", $Lib = "y"){
		
		if(IntVal($strPageSize) > 0){
			$arNavStartParams = Array("nPageSize" => $strPageSize);
		} else {
			$arNavStartParams = false;
		}
		
		$arProp = Array("IBLOCK_ID"=>self::REC_IBLOCK, "ACTIVE"=>"Y");
		if(count($arFilter) > 0){
			foreach($arFilter as $strKey => $strItem){
				if($strKey == "k"){
					$arProp["PROPERTY_kitchen"] = IntVal($strItem);
				} elseif($strKey == "d"){
					$arProp["PROPERTY_dish_type"] = IntVal($strItem);
				} elseif($strKey == 'CREATED_BY'){
					$arProp["CREATED_BY"] = IntVal($strItem);
				} elseif($strKey == 'ID'){
					$arProp["ID"] = $strItem;
				}elseif($strKey == 'dates'){
					$arProp["ACTIVE_DATE"] = "Y";
					$arProp[">=DATE_ACTIVE_FROM"] = $strItem['from'];
				}elseif($strKey == 'site_id'){
				    if($strItem == "fr"){
					$arProp["IBLOCK_ID"] = "24";
				    }
				}
			}
		}
		//echo "<pre>"; print_r($arProp); echo "</pre>";
		
		
		if($Lib == "y"){
			$arProp['PROPERTY_lib'] = "Y";
		}		

		$rsRecipes = CIBlockElement::GetList(Array("ACTIVE_FROM"=>"DESC","DATE_CREATE"=>"DESC"), $arProp, false, $arNavStartParams, Array("ID"));
		while($arRecipe = $rsRecipes->GetNext()){
			
			if(IntVal($arRecipe['PREVIEW_PICTURE']) > 0){
				$rsFile = CFile::GetByID(IntVal($arRecipe['PREVIEW_PICTURE']));
				$arFile = $rsFile->Fetch();
				$arFile['SRC'] = "/upload/".$arFile['SUBDIR']."/".$arFile['FILE_NAME'];
				$arRecipe["PREVIEW_PICTURE"] = $arFile;
			}
			$arResult[] = $arRecipe;
		}
		
		return $arResult;
	}
	
	static public function getInstance(){
		return (self::$_instance !== null) ? self::$_instance : (self::$_instance = new CFClub());  
	}
	
	static public function numberingStage($stageNumber) {
		$numberingArray1 = Array("первого", "второго", "третьего", "четвёртого", "пятого", "шестого", "седьмого", "восьмого", "девятого");
		$numberingArray2 = Array("одиннадцатого", "двенадцатого", "тринадцатого", "четырнадцатого", "пятнадцатого", "шестнадцатого", "семнадцатого", "восемнадцатого", "девятнадцатого");
		$numberingArray3 = Array("десятого", "двадцатого", "тридцатого", "сорокового", "пятидесятого", "шестидесятого", "семидесятого", "восьмидесятого", "девяностого");
		$numberingArray4 = Array("", "двадцать", "тридцать", "сорок", "пятьдесят", "шестьдесят", "семьдесят", "восемьдесят", "девяносто");

		if (IntVal($stageNumber) < 10) {
			$numbering = $numberingArray1[$stageNumber];
		}
		else {
			$lastLetter = ($stageNumber + 1)%10;
			if ($lastLetter == 0) {
				$numbering = $numberingArray3[floor(($stageNumber + 1)/10) - 1];
			}
			else {
				if (floor(($stageNumber + 1)/10) == 1) {
					$numbering = $numberingArray2[$stageNumber%10];
				}
				else {
					$numbering = $numberingArray4[floor(($stageNumber)/10) - 1] + " " + $numberingArray1[$stageNumber%10];
				}
			}
		}
		return $numbering;
	}
}

?>
