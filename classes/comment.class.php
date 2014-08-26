<?
class CFClubComment
{
	static private $_instance = null;
	
	/*
	 * Добавления комментария
	 */
	static public function add($intRecipe, $strComment, $intReply, $intRoot){
		$intID = false;
		global $USER;
		
		
		$arProp = Array("recipe"=>$intRecipe);
		if(isset($intReply))$arProp['reply'] = $intReply;
		if(isset($intRoot))$arProp['root'] = $intRoot;
		
		$arLoadProductArray = Array(
			"IBLOCK_SECTION"  => false,
			"IBLOCK_ID"       => 6,
			"PROPERTY_VALUES" => $arProp,
			"NAME"            => $intRecipe.".комментарий",
			"ACTIVE"          => "Y",
			"PREVIEW_TEXT"    => $strComment,
	    );
	    
	    $elComment   = new CIBlockElement;
		$intID = $elComment->Add($arLoadProductArray);
		
		$rsCount = CIBlockElement::GetProperty(5, $intRecipe, "sort", "asc", Array("CODE"=>"comment_count"));
		$arCount = $rsCount->Fetch(); 
		$mixCount = IntVal($arCount["VALUE"]);
		CIBlockElement::SetPropertyValues($intRecipe, 5, IntVal($mixCount)+1, "comment_count");
		$events = GetModuleEvents("iblock", "OnAfterIBlockElementUpdate");
		while($arEvent = $events->Fetch())
		{			
			if(ExecuteModuleEvent($arEvent, array("update_element_id"=>$intRecipe)) === false)
			{
				if($err = $APPLICATION->GetException())
					$arResult['ERRORS'][] = $err->GetString();
				
				break;
			}
		}
		$obCache = new CPageCache;
		$obCache->Clean("main", "home");
		
		$rsRecipe = CIBlockElement::GetById($intRecipe);
		$arRecipe = $rsRecipe->Fetch();
		$rsUser = CUser::GetByID($arRecipe['CREATED_BY']);
		$arUser = $rsUser->Fetch();
		
		$arFields = array(
		    "MESSAGE"	=> $strComment,
		    "ID"		=> $intRecipe,
			"CID"		=> $intID,
			"EMAIL"		=> $arUser['EMAIL'],
			"RECIPE_LINK" => "detail/".($arRecipe["CODE"] ? $arRecipe["CODE"] : $arRecipe["ID"])."/",
			"RECIPE_NAME" => $arRecipe["NAME"],
			"AUTHOR_NAME" => "",
			"AUTHOR_LINK" => ""
		);
		if(intval($USER->GetID())){
			$rsCommentAuthor = CUser::GetByID($USER->GetID());
			if($arCommentAuthor = $rsCommentAuthor->Fetch()){
				if($arCommentAuthor["NAME"] && $arCommentAuthor["LAST_NAME"])
					$arFields["AUTHOR_NAME"] = $arCommentAuthor["NAME"]." ".$arCommentAuthor["LAST_NAME"];
				else
					$arFields["AUTHOR_NAME"] = $arCommentAuthor["LOGIN"];

				$arFields["AUTHOR_LINK"] = "profile/".$arCommentAuthor["ID"]."/";
			}
		}
		
		CEvent::Send("NEW_COMMENT", array("s1"), $arFields, "N", 10);

		if(intval($intRoot)){
			$rsRecipe = CIBlockElement::GetList(array(),array("ID"=>$intRoot,"IBLOCK_ID"=>6),false,false,array("CREATED_BY"));
			if($arRecipe = $rsRecipe->Fetch()){
				$rsUser = CUser::GetByID($arRecipe['CREATED_BY']);
				$arUser = $rsUser->Fetch();
				$arFields["EMAIL"] = $arUser["EMAIL"];
				CEvent::Send("NEW_COMMENT", array("s1"), $arFields, "N", 57);
			}
		}

		return $intID;
	}
	
	/*
	 * Добавления комментария
	 */
	static public function update($intComment, $strComment, $intRecipe, $intRoot){
		global $USER;
		
		$rsC = CIBlockElement::GetByID($intComment);
		$arC = $rsC->Fetch();
		
		if($USER->IsAdmin() || $arC['CREATED_BY'] == $USER->GetID()){
			$arProp = Array("recipe"=>$intRecipe);
			if(isset($intRoot))$arProp['root'] = $intRoot;
			$arLoadProductArray = Array(
				"MODIFIED_BY"    => $USER->GetID(),
				"IBLOCK_SECTION"  => false,
				"IBLOCK_ID"       => 6,
				"PROPERTY_VALUES" => $arProp,
				"NAME"            => $intRecipe.".комментарий",
				"ACTIVE"          => "Y",
				"PREVIEW_TEXT"    => $strComment,
		    );
		    
		    $elComment   = new CIBlockElement;
			if($elComment->Update($intComment, $arLoadProductArray)){
				return true;	
			} else {
				return false;
			}//if
		} else {
			return false;
		}//if
	}
	
	static public function delete($intComment){
		$bError = true;
		
		$rsC = CIBlockElement::GetByID($intComment);
		$arC = $rsC->Fetch();
		
		global $DB, $USER;
		
		$rsRecipe = CIBlockElement::GetProperty(6, $intComment, "sort", "asc", Array("CODE"=>"recipe"));
		$arRecipe = $rsRecipe->Fetch(); 
		$mixRecipe = IntVal($arRecipe["VALUE"]);
				
		if($USER->IsAdmin() || $arC['CREATED_BY'] == $USER->GetID()){
			$DB->StartTransaction();
			if(!CIBlockElement::Delete($intComment)){
				$bError = false;
				$DB->Rollback();
			}
			else{
				$DB->Commit();
				
				$rsCount = CIBlockElement::GetProperty(5, $mixRecipe, "sort", "asc", Array("CODE"=>"comment_count"));
				$arCount = $rsCount->Fetch(); 
				$mixCount = IntVal($arCount["VALUE"]);
				CIBlockElement::SetPropertyValues($mixRecipe, 5, IntVal($mixCount)-1, "comment_count");
				
				$obCache = new CPageCache;
				$obCache->Clean("main", "home");
			}
				
		} else $bError = false;
		
		return $bError;
	}
	
	/*
	 * Получения списка комментариев
	 */
	static public function getList($mixID){
		$arResult = false;
		$arResult = array();$arResult["IDS"] = array();

		$rsComment = CIBlockElement::GetList(Array("DATE_CREATE"=>"DESC"), 
											 Array("IBLOCK_ID"=>6, "ACTIVE"=>"Y", "PROPERTY_recipe"=>$mixID), 
											 False, 
											 False,
											 Array("ID","CREATED_BY", "PREVIEW_TEXT", "CREATED_USER_NAME", "DATE_CREATE", "TIMESTAMP_X", "PROPERTY_recipe", "PROPERTY_root", "PROPERTY_reply", "PROPERTY_foodshot"));
											 
		while($arComment = $rsComment->GetNext()){
			
			$rsUser = CUser::GetById( $arComment['CREATED_BY'] );
			$site_format = CSite::GetDateFormat();

    		// преобразуем дату в Unix формат
    		if(strlen($arComment["TIMESTAMP_X"]) && strlen($arComment["DATE_CREATE"])){
    			$stmpUpdate = MakeTimeStamp($arComment["TIMESTAMP_X"], $site_format);
    			$stmpCreate = MakeTimeStamp($arComment["DATE_CREATE"], $site_format);
				$arComment["stmpUpdate"] = $stmpUpdate;
				$arComment["stmpCreate"] = $stmpCreate;
				if($stmpUpdate > $stmpCreate){
					$arComment['DATE_CREATE'] = "Отредактировано ".substr(str_replace(" ", " в ", $arComment['TIMESTAMP_X']), 0, -3);
				}else{
					$arComment['DATE_CREATE'] = substr(str_replace(" ", " в ", $arComment['DATE_CREATE']), 0, -3);
				}
    		}elseif(strlen($arComment["DATE_CREATE"])){
				$arComment['DATE_CREATE'] = substr(str_replace(" ", " в ", $arComment['DATE_CREATE']), 0, -3);
    		}else{
    			$arComment['DATE_CREATE'] = "";
    		}
						
			$arComment['USER'] = $rsUser->Fetch();
			if(strlen($arComment['USER']['NAME']) > 0 && strlen($arComment['USER']["LAST_NAME"]) > 0){		     	
		     	$arComment['USER']['FULLNAME'] = $arComment['USER']['NAME']." ".$arComment['USER']["LAST_NAME"];
		 	}else{
		 		$arComment['USER']["FULLNAME"] = $arComment['USER']["LOGIN"];
		 	}
			
			if(intval($arComment['USER']['PERSONAL_PHOTO']) > 0){
				$rsAvatar = CFile::GetByID($arComment['USER']['PERSONAL_PHOTO']);
				$arAvatar = $rsAvatar->Fetch();
				$arComment['USER']['SRC'] = "/upload/".$arAvatar['SUBDIR']."/".$arAvatar['FILE_NAME'];
			} else {
				$arComment['USER']['SRC'] = "/images/avatar/avatar.jpg";
			}

			if(intval($arComment["PROPERTY_FOODSHOT_VALUE"])){
				require_once($_SERVER['DOCUMENT_ROOT']."/classes/foodshot.class.php");
				$CFoodshot = CFoodshot::getInstance();
				$arFoodshot = $CFoodshot->GetByID($arComment["PROPERTY_FOODSHOT_VALUE"]);
				$arComment["FOODSHOT"] = $arFoodshot;
			}

			if(intval($arComment["PROPERTY_ROOT_VALUE"])){				
				$arResult["IDS"][ $arComment["PROPERTY_ROOT_VALUE"] ][] = $arComment["ID"];
			}else{
				$arResult["IDS"][] = $arComment["ID"];
			}
			
			$arResult["COMMENT_ID"][] = $arComment["ID"];

			$arResult["COMMENTS"][ $arComment['ID'] ] = $arComment;
		}
		
		return $arResult;
	}

	/*
	 * Получения лайков
	 */
	static public function getLikes($idList){
		$arResult = false;

		// likes count
		$arAllLikesFilter = array (
			"IBLOCK_CODE" 	    => "likes",
			"ACTIVE" => "Y",
			"PROPERTY_ELEMENT" => $idList,
			"PROPERTY_LIKE"	    => "1",
		);

		$arAllLikesSelect = array (
			"ID",
			"CREATED_BY",
		);

		$rsAllLikesItems = CIBlockElement::GetList(array(), $arAllLikesFilter, array("PROPERTY_element"), false, $arAllLikesSelect);		

		while($arAllLikesItems = $rsAllLikesItems->Fetch()){
			$arResult[ $arAllLikesItems["PROPERTY_ELEMENT_VALUE"] ] = $arAllLikesItems["CNT"];
			/*if ($USER->IsAuthorized() && intval($USER->GetID()) === intval($arAllLikesItems["CREATED_BY"]))
				$Foodshot["user_liked"] = "yes";*/
		}		
		
		return $arResult;
	}

	/*
	 * Получения лайков пользователя
	 */
	static public function getUserLikes($idUser,$idList){
		$arResult = false;

		// likes count
		$arAllLikesFilter = array (
			"IBLOCK_CODE" 	    => "likes",
			"ACTIVE" => "Y",
			"PROPERTY_ELEMENT" => $idList,
			"PROPERTY_LIKE"	    => "1",
			"CREATED_BY" => $idUser
		);

		$arAllLikesSelect = array (
			"ID",
			"PROPERTY_element"
		);

		$rsAllLikesItems = CIBlockElement::GetList(array(), $arAllLikesFilter, false, false, $arAllLikesSelect);		

		while($arAllLikesItems = $rsAllLikesItems->Fetch()){
			$arResult[ $arAllLikesItems["PROPERTY_ELEMENT_VALUE"] ] = true;
		}		
		
		return $arResult;
	}
	
	/*
		Получение фиксированного числа последних отзывов
	*/
	static public function getLastReplies($count){
		$arResult = false;
		$arFilter = Array("ACTIVE"=>"Y", "IBLOCK_ID"=>6);
		$rsComment = CIBlockElement::GetList(Array("created" => "DESC"),
											 $arFilter,
											 false,
											 Array ("nTopCount" => $count),
											 Array("ID","CREATED_BY", "PREVIEW_TEXT", "CREATED_USER_NAME", "DATE_CREATE", "PROPERTY_recipe", "PROPERTY_root", "PROPERTY_reply", "DETAIL_PAGE_URL")
											);
		while($arComment = $rsComment->GetNext()){
			$rsUser = CUser::GetById( $arComment['CREATED_BY'] );
			$arComment['DATE_CREATE'] = substr(str_replace(" ", " в ", $arComment['DATE_CREATE']), 0, -3);
			$arComment['USER'] = $rsUser->Fetch();
			$arResult[ $arComment['ID'] ] = $arComment;
		}
		return $arResult;
	}
	
	static public function getLastRepliesNew($count){
		$arResult = false;
		$arFilter = Array("ACTIVE"=>"Y", "IBLOCK_ID"=>6);
		$rsComment = CIBlockElement::GetList(Array("created" => "DESC"),
											 $arFilter,
											 false,
											 Array ("nTopCount" => $count),
											 Array("ID","CREATED_BY", "PREVIEW_TEXT", "CREATED_USER_NAME", "DATE_CREATE", "PROPERTY_recipe", "PROPERTY_root", "PROPERTY_reply", "DETAIL_PAGE_URL")
											);
		while($arComment = $rsComment->GetNext()){
			$rsUser = CUser::GetById( $arComment['CREATED_BY'] );
			$arComment['DATE_CREATE'] = $arComment['DATE_CREATE'];
			$arComment['USER'] = $rsUser->Fetch();
			$arResult["RECIPES"][] = $arComment["PROPERTY_RECIPE_VALUE"];
			$arResult["ITEMS"][$arComment["ID"]] = $arComment;
		}
		return $arResult;
	}
	
	
	static public function getInstance(){
		return (self::$_instance !== null) ? self::$_instance : (self::$_instance = new CFClubComment());  
	}
}

class CFClubCommentJSON extends CFClubComment
{
	static private $_instance = null;
	/*
	 * Добавления комментария
	 */
	static public function add($intRecipe, $strComment, $intReply, $intRoot, $strPhoto){
		$intID = false;
		global $USER;
		
		
		$arProp = Array("recipe"=>$intRecipe);
		if(isset($intReply))$arProp['reply'] = $intReply;
		if(isset($intRoot))$arProp['root'] = $intRoot;

		$rsRecipe = CIBlockElement::GetById($intRecipe);
		$arRecipe = $rsRecipe->Fetch();

		if(isset($strPhoto)){
			require_once($_SERVER['DOCUMENT_ROOT']."/classes/foodshot.class.php");
			$CFoodshot = CFoodshot::getInstance();
			if($intFoodshotID = $CFoodshot->add("Фудшот с рецепта ".$arRecipe["NAME"], $strComment, $strPhoto, "http://".$_SERVER["SERVER_NAME"]."/detail/".($arRecipe["CODE"] ? $arRecipe["CODE"] : $arRecipe["ID"])."/")){
				$arProp['foodshot'] = $intFoodshotID['id'];
			}
		}

		global $DB;
		$date =  date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL")), time());
		
		$arLoadProductArray = Array(
			"IBLOCK_SECTION"  => false,
			"IBLOCK_ID"       => 6,
			"PROPERTY_VALUES" => $arProp,
			"NAME"            => $intRecipe.".комментарий",
			"ACTIVE"          => "Y",
			"DATE_CREATE" => $date,
			"PREVIEW_TEXT"    => $strComment,			
	    );
	    
	    $elComment   = new CIBlockElement;
		$intID = $elComment->Add($arLoadProductArray);
		
		$rsCount = CIBlockElement::GetProperty(5, $intRecipe, "sort", "asc", Array("CODE"=>"comment_count"));
		$arCount = $rsCount->Fetch(); 
		$mixCount = IntVal($arCount["VALUE"]);
		CIBlockElement::SetPropertyValues($intRecipe, 5, IntVal($mixCount)+1, "comment_count");
		$events = GetModuleEvents("iblock", "OnAfterIBlockElementUpdate");
		while($arEvent = $events->Fetch())
		{			
			if(ExecuteModuleEvent($arEvent, array("update_element_id"=>$intRecipe)) === false)
			{
				if($err = $APPLICATION->GetException())
					$arResult['ERRORS'][] = $err->GetString();
				
				break;
			}
		}
		$obCache = new CPageCache;
		$obCache->Clean("main", "home");
				
		$rsUser = CUser::GetByID($arRecipe['CREATED_BY']);
		$arUser = $rsUser->Fetch();
		
		$arFields = array(
		    "MESSAGE"	=> $strComment,
		    "ID"		=> $intRecipe,
			"CID"		=> $intID,
			"EMAIL"		=> $arUser['EMAIL'],
			"RECIPE_LINK" => "detail/".$intRecipe."/",
			"RECIPE_NAME" => $arRecipe["NAME"],
			"AUTHOR_NAME" => "",
			"AUTHOR_LINK" => ""
		);
		if(intval($USER->GetID())){
			$rsCommentAuthor = CUser::GetByID($USER->GetID());
			if($arCommentAuthor = $rsCommentAuthor->Fetch()){
				if(intval($arCommentAuthor["PERSONAL_PHOTO"])){					
					$arFields["AUTHOR_AVATAR"] = CFile::GetPath($arCommentAuthor["PERSONAL_PHOTO"]);
				}else{
					$arFields["AUTHOR_AVATAR"] = "/images/avatar/avatar.jpg";
				}
				if($arCommentAuthor["NAME"] && $arCommentAuthor["LAST_NAME"])
					$arFields["AUTHOR_NAME"] = $arCommentAuthor["NAME"]." ".$arCommentAuthor["LAST_NAME"];
				else
					$arFields["AUTHOR_NAME"] = $arCommentAuthor["LOGIN"];

				$arFields["AUTHOR_LINK"] = "profile/".$arCommentAuthor["ID"]."/";
			}
		}
		
		CEvent::Send("NEW_COMMENT", array("s1"), $arFields, "N", 10);

		if(intval($intRoot)){
			$rsRecipe = CIBlockElement::GetList(array(),array("ID"=>$intRoot,"IBLOCK_ID"=>6),false,false,array("CREATED_BY"));
			if($arRecipe = $rsRecipe->Fetch()){
				$rsUser = CUser::GetByID($arRecipe['CREATED_BY']);
				$arUser = $rsUser->Fetch();
				$arFields["EMAIL"] = $arUser["EMAIL"];
				CEvent::Send("NEW_COMMENT", array("s1"), $arFields, "N", 52);
			}
		}

		$arReturn = array(
			"id" => $intID,
			"rId" => $intRecipe,
			"date" => $date,
			"image" => array(
				"id" => "",
				"src" => "",
				"width" => "",
				"height" => ""
			),
			"text" => array(
				"text" => $strComment,
				"html" => $strComment
			),
			"author" => array(
				"href" => "/".$arFields["AUTHOR_LINK"],
				"src" => $arFields["AUTHOR_AVATAR"],
				"name" => $arFields["AUTHOR_NAME"]
			),
			"likeNum" => ""
		);

		if($arProp['foodshot']){
			$arFoodshot = $CFoodshot->GetByID($arProp['foodshot']);
			$arReturn["image"] = $arFoodshot["image"];
		}

		return $arReturn;
	}
	
	/*
	 * Добавления комментария
	 */
	static public function update($intComment, $strComment, $intRecipe, $intRoot, $strPhoto = ""){
		global $USER;

		$rsRecipe = CIBlockElement::GetById($intRecipe);
		$arRecipe = $rsRecipe->Fetch();
				
		$rsC = CIBlockElement::GetList(array(), array("IBLOCK_CODE" => "comments","ID" => $intComment), false, false);
		if($obC = $rsC->GetNextElement()){
			$arC = $obC->GetFields();
			$arC["PROPERTIES"] = $obC->GetProperties();
		}else{
			return array();
		}

		$rsLike = CIBlockElement::GetList(array(), array("IBLOCK_CODE" => "likes","PROPERTY_element" => $intComment), false, false);
		if($obL = $rsLike -> GetNextElement()){
			$arL = $obL->GetFields();
			$arL["PROPERTIES"] = $obL->GetProperties();
		}
		
		if($USER->IsAdmin() || $arC['CREATED_BY'] == $USER->GetID()){
			$arProp = Array("recipe"=>$intRecipe);
			if(isset($intRoot))$arProp['root'] = $intRoot;			

		    if($arC["PROPERTIES"]["foodshot"]["VALUE"]){
		    	if(strlen($strPhoto)){
		    		$arProp['foodshot'] = $arC["PROPERTIES"]["foodshot"]["VALUE"];
		    	}else{
		    		$arProp['foodshot'] = "";		    		
		    	}
		    }else{
		    	if(isset($strPhoto)){
					require_once($_SERVER['DOCUMENT_ROOT']."/classes/foodshot.class.php");
					$CFoodshot = CFoodshot::getInstance();
					if($intFoodshotID = $CFoodshot->add("Фудшот с рецепта ".$arRecipe["NAME"], $strComment, $strPhoto, "http://".$_SERVER["SERVER_NAME"]."/detail/".$arRecipe["ID"]."/")){
						$arProp['foodshot'] = $intFoodshotID['id'];
					}
				}
		    }

		    $arLoadProductArray = Array(
				"MODIFIED_BY"    => $USER->GetID(),
				"IBLOCK_SECTION"  => false,
				"IBLOCK_ID"       => 6,
				"PROPERTY_VALUES" => $arProp,
				"NAME"            => $intRecipe.".комментарий",
				"ACTIVE"          => "Y",
				"PREVIEW_TEXT"    => $strComment,
		    );
		    
		    $elComment   = new CIBlockElement;
			if($elComment->Update($intComment, $arLoadProductArray)){

				if(intval($arC['CREATED_BY'])){
					$arAuthor = array();
					$rsCommentAuthor = CUser::GetByID($arC['CREATED_BY']);
					if($arCommentAuthor = $rsCommentAuthor->Fetch()){
						if(intval($arCommentAuthor["PERSONAL_PHOTO"])){					
							$arAuthor["AUTHOR_AVATAR"] = CFile::GetPath($arCommentAuthor["PERSONAL_PHOTO"]);
						}else{
							$arAuthor["AUTHOR_AVATAR"] = "/images/avatar/avatar.jpg";
						}
						if($arCommentAuthor["NAME"] && $arCommentAuthor["LAST_NAME"])
							$arAuthor["AUTHOR_NAME"] = $arCommentAuthor["NAME"]." ".$arCommentAuthor["LAST_NAME"];
						else
							$arAuthor["AUTHOR_NAME"] = $arCommentAuthor["LOGIN"];

						$arAuthor["AUTHOR_LINK"] = "profile/".$arCommentAuthor["ID"]."/";
					}
				}

				$arReturn = array(
					"id" => $intComment,
					"rId" => $intRecipe,
					"date" => $arC["DATE_CREATE"],					
					"text" => array(
						"text" => $strComment,
						"html" => $strComment
					),
					"author" => array(
						"href" => "/".$arAuthor["AUTHOR_LINK"],
						"src" => $arAuthor["AUTHOR_AVATAR"],
						"name" => $arAuthor["AUTHOR_NAME"]
					),
					"likeNum" => (isset($arL["PROPERTIES"]["like"]["VALUE"]) ? intval($arL["PROPERTIES"]["like"]["VALUE"]) : 0)
				);

				$rsUC = CIBlockElement::GetById($intComment);
				if($arUC = $rsUC->Fetch()){

					//echo "<pre>";print_r($arUC);echo "</pre>";die;

		    		// преобразуем дату в Unix формат
		    		if(strlen($arUC["TIMESTAMP_X_UNIX"]) && strlen($arUC["DATE_CREATE_UNIX"])){
		    			$stmpUpdate = $arUC["TIMESTAMP_X_UNIX"];
		    			$stmpCreate = $arUC["DATE_CREATE_UNIX"];
						if($stmpUpdate > $stmpCreate){
							$arReturn["date"] = "Отредактировано ".substr(str_replace(" ", " в ", $arUC['TIMESTAMP_X']), 0, -3);
						}else{
							$arReturn["date"] = substr(str_replace(" ", " в ", $arUC['DATE_CREATE']), 0, -3);
						}
		    		}elseif(strlen($arUC["DATE_CREATE"])){
						$arReturn["date"] = substr(str_replace(" ", " в ", $arUC['DATE_CREATE']), 0, -3);
		    		}else{
		    			$arReturn["date"] = "";
		    		}
				}				

				if($arC["PROPERTIES"]["foodshot"]["VALUE"]){
					require_once($_SERVER['DOCUMENT_ROOT']."/classes/foodshot.class.php");
					$CFoodshot = CFoodshot::getInstance();
					$arFoodshot = $CFoodshot->GetByID($arC["PROPERTIES"]["foodshot"]["VALUE"]);
					//check if need to update foodshot										
					if(isset($strPhoto) && $strPhoto != $arFoodshot["image"]["src"]){
						//update foodshot
						if(strlen($strPhoto) <= 0){
							$arReturn["image"] = "";
						}else{							
							if($arFoodshot = $CFoodshot->update($arC["PROPERTIES"]["foodshot"]["VALUE"] ,"Фудшот с рецепта ".$arRecipe["NAME"], $strComment, $strPhoto, "http://".$_SERVER["SERVER_NAME"]."/detail/".$arRecipe["ID"]."/")){
								$arReturn["image"] = $arFoodshot["image"];							
							}
						}
					}else{
						//do nothing
						$arReturn["image"] = $arFoodshot["image"];
					}
				}else{
					if($arProp['foodshot']){
						$arFoodshot = $CFoodshot->GetByID($arProp['foodshot']);
						$arReturn["image"] = $arFoodshot["image"];
					}
				}

				return $arReturn;
			} else {
				return array();
			}//if
		} else {
			return array();
		}//if
	}

	static public function delete($intComment){
		$bError = true;
		
		$rsC = CIBlockElement::GetByID($intComment);
		$arC = $rsC->Fetch();
		
		global $DB, $USER;
		
		$rsRecipe = CIBlockElement::GetProperty(6, $intComment, "sort", "asc", Array("CODE"=>"recipe"));
		$arRecipe = $rsRecipe->Fetch();
		$mixRecipe = IntVal($arRecipe["VALUE"]);
				
		if($USER->IsAdmin() || $arC['CREATED_BY'] == $USER->GetID()){
			$DB->StartTransaction();
			if(!CIBlockElement::Delete($intComment)){
				$bError = false;
				$DB->Rollback();
				$arReturn = array(
					"success" => "true",
					"text" => "Сообщение успешно удалено!"
				);
			}
			else{
				$DB->Commit();
				
				$rsCount = CIBlockElement::GetProperty(5, $mixRecipe, "sort", "asc", Array("CODE"=>"comment_count"));
				$arCount = $rsCount->Fetch(); 
				$mixCount = IntVal($arCount["VALUE"]);
				CIBlockElement::SetPropertyValues($mixRecipe, 5, IntVal($mixCount)-1, "comment_count");
				
				$obCache = new CPageCache;
				$obCache->Clean("main", "home");
				$arReturn = array(
					"success" => "false",
					"text" => "Что-то пошло не так! :-("
				);
			}
				
		} else $bError = false;
		
		return $arReturn;
	}

	static public function getInstance(){
		return (self::$_instance !== null) ? self::$_instance : (self::$_instance = new CFClubCommentJSON());  
	}
}
?>
