<?
class CFoodshot
{
	static private $_instance = null;
	
	/*
	 * Добавления фудшота
	 */
	static public function add($title, $descr, $photo, $url){
		//TODO
		if($url){
			if(strpos($url,"http://") === false){
				if(strpos($url,"www") === false){
					$url = "http://www.".$url;
				}else{
					$url = "http://".$url;
				}		
			}elseif(strpos($url,"www") === false){
				$url = "http://www.".str_replace("http://","",$url);		
			}			
		}

		$ch = curl_init($photo);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	 
	    $data = curl_exec($ch);
	 
	    curl_close($ch);

	    if(strlen($data) > 0){

    	
    		$el = new CIBlockElement;

			$PROP = array();
			$filename = time().".jpg";
			file_put_contents($_SERVER["DOCUMENT_ROOT"]."/upload/tmp/".$filename, $data);
			$arPic = CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"]."/upload/tmp/".$filename);

			//echo "<pre>";print_R($arPic);echo "</pre>";die;
			$arParsedUrl = parse_url($url);
			if(strlen($url) > 0){
				$arProp = array("www"=>$url);
			}

			if(strlen($descr) > 0){
				$safe_descr = $descr;
				preg_match_all('/(a|href)=("|\')[^"\'>]+/i',$safe_descr, $matches);
				if(!empty($matches[0])){
					$matches[0] = array_unique($matches[0]);
					foreach($matches[0] as $match){					
						$safe_descr = str_replace($match, 'rel="nofollow" '.$match, $safe_descr);
					}
				}
				$arProp["secure_descr"] = Array("VALUE" => Array ("TEXT" => $safe_descr, "TYPE" => "html"));
			}

			global $USER;

			$arLoadProductArray = Array(
				"MODIFIED_BY"    => $USER->GetID(),
				"IBLOCK_SECTION_ID" => false,
				"IBLOCK_ID"      => "25",//18
				"NAME"           => $title,
				"ACTIVE"         => "Y",
				"PREVIEW_TEXT"   => $descr,
				"DETAIL_TEXT"    => $descr,			
				"DETAIL_PICTURE" => $arPic,
				"PROPERTY_VALUES"=> $arProp,
			);

			$rsAuthor = CUser::GetByID(CUser::GetID());
			if($arAuthor = $rsAuthor->Fetch()){
				if(intval($arAuthor["PERSONAL_PICTURE"]))
					$arAuthor["AVATAR"] = CFile::GetPath($arAuthor["PERSONAL_PICTURE"]);
				else
					$arAuthor["AVATAR"] = "http://".$_SERVER["SERVER_NAME"]."/images/avatar/avatar.jpg";

				if(strlen($arAuthor["NAME"]) > 0 && strlen($arAuthor["LAST_NAME"]) > 0){
					$arAuthor["FULL_NAME"] = $arAuthor["NAME"]." ".$arAuthor["LAST_NAME"];
				}else{
					$arAuthor["FULL_NAME"] = $arAuthor["LOGIN"];
				}
			}

			if($PRODUCT_ID = $el->Add($arLoadProductArray,false,true,true)){

				$rsFoodshot = CIBlockElement::GetByID($PRODUCT_ID);
				if($arFoodshot = $rsFoodshot->Fetch()){

					/*FOODSHOT_NAME# - Название фудшота
					FOODSHOT_LINK# - Ссылка на фудшот*/

					//Отправка почтового сообщения админу
					$arFields = array(
					    "FOODSHOT_NAME"	=> $arFoodshot["NAME"],
					    "FOODSHOT_LINK"	=> "http://".$_SERVER["SERVER_NAME"]."/foodshot/".$arFoodshot["ID"]."/"
					);
					
					CEvent::Send("FOODSHOT_ADDED", array("s1"), $arFields, "N");

					if(intval($arFoodshot["PREVIEW_PICTURE"]) > 0){

						$rsFile = CFile::GetByID($arFoodshot["PREVIEW_PICTURE"]);
						if($arFile = $rsFile->Fetch()){
							
							$arResult = array(
								"id" => $PRODUCT_ID,
								"href" => "http://".$_SERVER["SERVER_NAME"],
								"image" => array(
										"src" => CFile::GetPath($arFile["ID"]),
										"width" => $arFile["WIDTH"],
										"height" => $arFile["HEIGHT"]
									),
								"name" => $title,
								"text" => $descr,
								"author" => array(
										"href" => "http://".$_SERVER["SERVER_NAME"]."/profile/".$arAuthor["ID"]."/",
										"src" => $arAuthor["AVATAR"],
										"name" => $arAuthor["FULL_NAME"]
									),
								"comments" => array(
										"num" => "",
										"visible" => ""
									),
								"likeNum" => ""
							);
							return $arResult;
						}
					}
				}

			}else
				return false;
    	}
	}
	
	/*
	 * Обновление фудшота
	 */
	static public function update($id, $title, $descr, $photo, $url){
		//TODO		
		$rsFoodShot = CIBlockElement::GetList(array(),array("IBLOCK_CODE"=>"foodshot_elements","ID"=>$id,"ACTIVE" => "Y"),false,false,array("ID","NAME","CREATED_BY","DETAIL_PICTURE","DETAIL_TEXT","PROPERTY_comments_count","PROPERTY_likes_count","PROPERTY_www"));
		if($arFoodShot = $rsFoodShot->GetNext()){
			global $USER;
			if($USER->IsAdmin() || $USER->GetID() == $arFoodShot["CREATED_BY"]){

				$el = new CIBlockElement;

				$PROP = array();

				$ch = curl_init($photo);
			    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			 
			    $data = curl_exec($ch);
			 
			    curl_close($ch);

			    if(strlen($data) > 0){

		    	
		    		$el = new CIBlockElement;

					$PROP = array();
					$filename = time().".jpg";
					file_put_contents($_SERVER["DOCUMENT_ROOT"]."/upload/tmp/".$filename, $data);
					$arPic = CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"]."/upload/tmp/".$filename);
				}
				
				$arLoadProductArray = Array(
					"MODIFIED_BY"    => $USER->GetID(),
					"NAME"           => $title,
					"PREVIEW_TEXT"   => $descr,
					"DETAIL_TEXT"    => $descr,
				);

				if(isset($arPic)){
					//$arLoadProductArray["PREVIEW_PICTURE"] = $arPic;
					$arLoadProductArray["DETAIL_PICTURE"] = $arPic;
				}

				//echo "<pre>";print_R($arLoadProductArray);echo "</pre>";die;
				
				$res = $el->Update($id, $arLoadProductArray,false,false,true);				

				//echo $id;die;

				$rsFoodshot = CIBlockElement::GetByID($id);
				if($arFoodshot = $rsFoodshot->Fetch()){

					//echo "<pre>";print_R($arFoodshot);echo "</pre>";die;				

					/*FOODSHOT_NAME# - Название фудшота
					FOODSHOT_LINK# - Ссылка на фудшот*/

					//Отправка почтового сообщения админу
					$arFields = array(
					    "FOODSHOT_NAME"	=> $arFoodshot["NAME"],
					    "FOODSHOT_LINK"	=> "http://".$_SERVER["SERVER_NAME"]."/foodshot/".$arFoodshot["ID"]."/"
					);
					
					CEvent::Send("FOODSHOT_ADDED", array("s1"), $arFields, "N");

					if(intval($arFoodshot["DETAIL_PICTURE"]) > 0){

						$rsFile = CFile::GetByID($arFoodshot["DETAIL_PICTURE"]);
						if($arFile = $rsFile->Fetch()){
							
							$arResult = array(
								"id" => $id,
								"href" => "http://".$_SERVER["SERVER_NAME"],
								"image" => array(
										"src" => CFile::GetPath($arFile["ID"]),
										"width" => $arFile["WIDTH"],
										"height" => $arFile["HEIGHT"]
									),
								"name" => $title,
								"text" => $descr,
								"author" => array(
										"href" => "http://".$_SERVER["SERVER_NAME"]."/profile/".$arAuthor["ID"]."/",
										"src" => $arAuthor["AVATAR"],
										"name" => $arAuthor["FULL_NAME"]
									),
								"comments" => array(
										"num" => "",
										"visible" => ""
									),
								"likeNum" => ""
							);
							return $arResult;
						}
					}
				}
			}
		}
		return false;
	}

	/*
	 * Удаление фудшота
	 */	
	static public function delete($intComment){
		//TODO
	}

	/*
	 * Получение по ID фудшота
	 */	
	static public function getByID($intFoodshot){
		//TODO	
		global $USER;
		if($USER->IsAuthorized()){
			$auth = 11;
		}else{
			$auth = 0;
		}	
		$cache_id = "foodshot_pid_".$intFoodshot."_".$auth;
		$cache_dir = "/foodshot";
		$resultArray = array();

		$obCache = new CPHPCache;
		if($obCache->InitCache(36000, $cache_id, $cache_dir)){
			$resultArray = $obCache->GetVars();
		}elseif(CModule::IncludeModule("iblock") && $obCache->StartDataCache()){

			global $CACHE_MANAGER;			
			$CACHE_MANAGER->StartTagCache($cache_dir);
			//лучше по IBLOCK_ID		
			$rsFoodShot = CIBlockElement::GetList(array(),array("IBLOCK_CODE"=>"foodshot_elements","ID"=>$intFoodshot,"ACTIVE" => "Y"),false,false,array("ID","NAME","CREATED_BY","DETAIL_PICTURE","DETAIL_TEXT","PROPERTY_comments_count","PROPERTY_likes_count","PROPERTY_www"));
			if($arFoodShot = $rsFoodShot->GetNext()){
			// echo "<pre>";print_r($arFoodShot);echo "</pre>";			
				$arRequestedUsers = array();
				if(intval($arFoodShot["DETAIL_PICTURE"]) > 0){
					$asPhoto = CFile::GetByID($arFoodShot["DETAIL_PICTURE"])->Fetch();
					$resultArray["image"]["id"] = $asPhoto["ID"];
					$resultArray["image"]["src"] = CFile::GetPath($asPhoto["ID"]);
					$resultArray["image"]["width"] = $asPhoto["WIDTH"];
					$resultArray["image"]["height"] = $asPhoto["HEIGHT"];			
				} else {
					$resultArray["image"]["id"] = "";
					$resultArray["image"]["src"] = "";
					$resultArray["image"]["width"] = "";
					$resultArray["image"]["height"] = "";
				}
				
				$resultArray["name"] = $arFoodShot["NAME"];
				
				if(strlen($arFoodShot["DETAIL_TEXT"]) > 0){
					$resultArray["description"]["text"] = $arFoodShot["DETAIL_TEXT"];
					$resultArray["description"]["formatted"] = addslashes($arFoodShot["DETAIL_TEXT"]);
				} else {
					$resultArray["description"]["text"] = "";
					$resultArray["description"]["formatted"] = "";
				}
				
				if(strlen($arFoodShot["PROPERTY_WWW_VALUE"]) > 0){
					$resultArray["description"]["source"] = $arFoodShot["PROPERTY_WWW_VALUE"];			
				} else {
					$resultArray["description"]["source"] = "";
				}		
				
				if(strlen($arFoodShot["CREATED_BY"]) > 0){
					$author = CUser::GetByID($arFoodShot["CREATED_BY"])->Fetch();
					if(!empty($author)){			
						if($USER->IsAdmin() || ($USER->GetID() == $author["ID"])){					
							$resultArray["deleteIcon"] = "yes";
						}
						$arRequestedUsers[ $author["ID"] ] = $author;
						$resultArray["description"]["author"]["id"] = $author["ID"];
						$resultArray["description"]["author"]["name"] = (strlen($author["NAME"]) > 0 && strlen($author["LAST_NAME"]) > 0 ? $author["NAME"]." ".$author["LAST_NAME"]:$author["LOGIN"]);
						if(intval($author["PERSONAL_PHOTO"]) > 0){
							$personal_photo = CFile::GetByID($author["PERSONAL_PHOTO"])->Fetch();
							if(!empty($personal_photo)){
								$arRequestedUsers[ $author["ID"] ]["PERSONAL_PHOTO_ARRAY"] = $personal_photo;
								$arRequestedUsers[ $author["ID"] ]["PERSONAL_PHOTO_ARRAY"]["SRC"] = CFile::GetPath($arRequestedUsers[ $author["ID"] ]["PERSONAL_PHOTO_ARRAY"]["ID"]);
								$resultArray["description"]["author"]["href"] = "/profile/".$author["ID"]."/";
								$resultArray["description"]["author"]["src"] = CFile::GetPath($personal_photo["ID"]);
							}else{
								$arRequestedUsers[ $author["ID"] ]["PERSONAL_PHOTO_ARRAY"]["SRC"] = "/images/avatar/avatar.jpg";
								$resultArray["description"]["author"]["href"] = "/profile/".$author["ID"]."/";
								$resultArray["description"]["author"]["src"] = "/images/avatar/avatar.jpg";
							}
						} else {
							$resultArray["description"]["author"]["href"] = "/profile/".$author["ID"]."/";
							$resultArray["description"]["author"]["src"] = "/images/avatar/avatar.jpg";
						}
					}
				} 
				$rsComments = CIBlockElement::GetList(array("DATE_CREATE" => "ASC"),array("IBLOCK_CODE"=>"foodshot_comments","PROPERTY_element"=>$intFoodshot),false,false,array("ID","CREATED_BY","PREVIEW_TEXT","DATE_CREATE"));
				if ($rsComments->SelectedRowsCount() > 0) {
					while($arComment = $rsComments->GetNext()){
						if(intval($arComment["CREATED_BY"]) > 0){
							if(!in_array($arComment["CREATED_BY"],$arRequestedUsers)){
								$comment_author = CUser::GetByID($arComment["CREATED_BY"])->Fetch();
								if(!empty($comment_author)){
									$arRequestedUsers[ $comment_author["ID"] ] = $comment_author;
									if(intval($comment_author["PERSONAL_PHOTO"]) > 0){
										$comment_author_photo = CFile::GetByID($comment_author["PERSONAL_PHOTO"])->Fetch();
										if(!empty($comment_author_photo)){
											$arRequestedUsers[ $comment_author["ID"] ]["PERSONAL_PHOTO_ARRAY"] = $comment_author_photo;
											$arRequestedUsers[ $comment_author["ID"] ]["PERSONAL_PHOTO_ARRAY"]["SRC"] = CFile::GetPath($arRequestedUsers[ $comment_author["ID"] ]["PERSONAL_PHOTO_ARRAY"]["ID"]);
										}else{
											$arRequestedUsers[ $comment_author["ID"] ]["PERSONAL_PHOTO_ARRAY"]["SRC"] = "/images/avatar/avatar.jpg";
										}
									}else{
										$arRequestedUsers[ $comment_author["ID"] ]["PERSONAL_PHOTO_ARRAY"]["SRC"] = "/images/avatar/avatar.jpg";
									}
								}
							}
							$resultArray["comments"][] = array(
								"id" => $arComment["ID"],
								"author" => array(
									"id" => $arRequestedUsers[ $arComment["CREATED_BY"] ]["ID"],
									"href" => "/profile/".$arRequestedUsers[ $arComment["CREATED_BY"] ]["ID"]."/",
									"src" => $arRequestedUsers[ $arComment["CREATED_BY"] ]["PERSONAL_PHOTO_ARRAY"]["SRC"],
									"name" => (strlen($arRequestedUsers[ $arComment["CREATED_BY"] ]["NAME"]) > 0 && strlen($arRequestedUsers[ $arComment["CREATED_BY"] ]["LAST_NAME"]) > 0 ? $arRequestedUsers[ $arComment["CREATED_BY"] ]["NAME"]." ".$arRequestedUsers[ $arComment["CREATED_BY"] ]["LAST_NAME"]:$arRequestedUsers[ $arComment["CREATED_BY"] ]["LOGIN"]),
								),
								"text" => $arComment["PREVIEW_TEXT"],
								"date" => strtolower(ConvertDateTime($arComment["DATE_CREATE"],"DD-MM-YYYY HH:MI", "ru"))
								//"date" => strtolower($arComment["DATE_CREATE"])
							);
						}
					}
				} else {
					$resultArray["comments"] = array();
				}
				
				$arAllLikesFilter = array (
					"IBLOCK_CODE" 	    => "foodshot_likes",
					"ACTIVE"	    	    => "Y",
					"PROPERTY_ELEMENT" => $arFoodShot["ID"],
					"PROPERTY_LIKE"	    => "1",
				);

				$arAllLikesSelect = array (
					"ID",
					"CREATED_BY",
				);

				$rsAllLikesItems = CIBlockElement::GetList(array(), $arAllLikesFilter, false, false, $arAllLikesSelect);
				$resultArray["likeNum"] = intval($rsAllLikesItems->SelectedRowsCount());

				while($arAllLikesItems = $rsAllLikesItems->Fetch())
				{
					if ($USER->IsAuthorized() && intval($USER->GetID()) === intval($arAllLikesItems["CREATED_BY"]))
						$resultArray["user_liked"] = "yes";			
				}

				/*if(intval($arFoodShot["PROPERTY_LIKES_COUNT_VALUE"]) > 0){
					$resultArray["likeNum"] = $arFoodShot["PROPERTY_LIKES_COUNT_VALUE"];
				} else {
					$resultArray["likeNum"] = "";
				}*/			
			}
			$CACHE_MANAGER->RegisterTag("foodshot_detail_".$intFoodshot);
			$CACHE_MANAGER->EndTagCache();

			$obCache->EndDataCache($resultArray);
		}

		return $resultArray;
	}
	
	/*
	 * Получения списка фудшотов
	 */
	static public function getList($arSort = array("DATE_CREATE"=>"DESC"), $mixID = 20){
		//TODO
		global $USER;
		$resultArray = array();	
		$arFilter = array(
			"IBLOCK_CODE"=>"foodshot_elements",
			"ACTIVE" => "Y",
		);
		if($mixID > 0){
			$arNavStartParams = array("nPageSize"=>$mixID,"iNumPage"=>1);
		}else{
			$arNavStartParams = false;
		}
		$rsFoodshots = CIBlockElement::GetList(array("DATE_CREATE"=>"DESC"),$arFilter,false,$arNavStartParams,array("ID","NAME","DATE_CREATE","TIMESTAMP_X","CREATED_BY","PREVIEW_PICTURE","DETAIL_PICTURE","PREVIEW_TEXT","PROPERTY_comments_count","PROPERTY_likes_count","PROPERTY_www"));	
		while($arFoodshot = $rsFoodshots->GetNext()){
			$Foodshot = array();
			$Foodshot["id"] = $arFoodshot["ID"];
			$Foodshot["href"] = "/foodshot/".$arFoodshot["ID"]."/#!foodshot";
			$Foodshot["date_create"] = $arFoodshot["DATE_CREATE"];
			$Foodshot["date_update"] = $arFoodshot["TIMESTAMP_X"];
			if(intval($arFoodshot["PREVIEW_PICTURE"]) > 0){
				$photo = CFile::GetByID($arFoodshot["PREVIEW_PICTURE"])->Fetch();
				if(!empty($photo)){
					$Foodshot["image"] = array(
						"src" => CFile::GetPath($photo["ID"]),
						"width" => $photo["WIDTH"],
						"height" => $photo["HEIGHT"],
					);
				}
				unset($photo);
			}
			if(intval($arFoodshot["DETAIL_PICTURE"]) > 0){
				$photo = CFile::GetByID($arFoodshot["DETAIL_PICTURE"])->Fetch();
				if(!empty($photo)){
					$Foodshot["detail_image"] = array(
						"src" => CFile::GetPath($photo["ID"]),
						"width" => $photo["WIDTH"],
						"height" => $photo["HEIGHT"],
					);
				}
				unset($photo);
			}
			$Foodshot["name"] = $arFoodshot["NAME"];
			$Foodshot["text"] = $arFoodshot["PREVIEW_TEXT"];
			$Foodshot["source"] = $arFoodshot["PROPERTY_WWW_VALUE"];
			if(intval($arFoodshot["CREATED_BY"]) > 0){
				$author = CUser::GetByID($arFoodshot["CREATED_BY"])->Fetch();
				//echo "author<pre>";print_r($author);echo "</pre>";die;
				if(!empty($author)){
					$Foodshot["author"] = array(
						"id" => $author["ID"],
						"href" => "/profile/".$author["ID"]."/",
						"name" => (strlen($author["NAME"]) > 0 && strlen($author["LAST_NAME"]) > 0 ? $author["NAME"]." ".$author["LAST_NAME"]:$author["LOGIN"]),
					);
					if(intval($author["PERSONAL_PHOTO"]) > 0){
						$author_photo = CFile::GetByID($author["PERSONAL_PHOTO"])->Fetch();
						if(!empty($author_photo)){
							$Foodshot["author"]["src"] = CFile::GetPath($author_photo["ID"]);
						}
					}else{
						$Foodshot["author"]["src"] = "/images/avatar/avatar.jpg";
					}
				}
			}
			$Foodshot["comments"] = array();
			if(intval($arFoodshot["PROPERTY_COMMENTS_COUNT_VALUE"]) > 0){
				$Foodshot["comments"]["num"] = $arFoodshot["PROPERTY_COMMENTS_COUNT_VALUE"];
			}else{
				$Foodshot["comments"]["num"] = "";
			}
			$Foodshot["comments"]["visible"] = array();
			$rsAllComments = CIBlockElement::GetList(array("DATE_CREATE" => "ASC"),array("IBLOCK_CODE"=>"foodshot_comments","PROPERTY_element"=>$arFoodshot["ID"]),false,false,array("ID"));
			$commentsCount = intval($rsAllComments->SelectedRowsCount());
			$Foodshot["comments"]["num"] = $commentsCount;

			$rsComments = CIBlockElement::GetList(array("DATE_CREATE" => "DESC"),array("IBLOCK_CODE"=>"foodshot_comments","PROPERTY_element"=>$arFoodshot["ID"]),false,array("nTopCount"=>3),array("ID","CREATED_BY","NAME","PREVIEW_TEXT","DATE_CREATE"));
			while($arComment = $rsComments->GetNext()){
				if(intval($arComment["CREATED_BY"]) > 0){
					if(!in_array($arComment["CREATED_BY"],$arRequestedUsers)){
						$comment_author = CUser::GetByID($arComment["CREATED_BY"])->Fetch();
						if(!empty($comment_author)){
							$arRequestedUsers[ $comment_author["ID"] ] = $comment_author;
							if(intval($comment_author["PERSONAL_PHOTO"]) > 0){
								$comment_author_photo = CFile::GetByID($comment_author["PERSONAL_PHOTO"])->Fetch();
								if(!empty($comment_author_photo)){
									$arRequestedUsers[ $comment_author["ID"] ]["PERSONAL_PHOTO_ARRAY"] = $comment_author_photo;
									$arRequestedUsers[ $comment_author["ID"] ]["PERSONAL_PHOTO_ARRAY"]["SRC"] = CFile::GetPath($arRequestedUsers[ $arComment["CREATED_BY"] ]["PERSONAL_PHOTO_ARRAY"]["ID"]);
								}
							}else{
								$arRequestedUsers[ $comment_author["ID"] ]["PERSONAL_PHOTO_ARRAY"]["SRC"] = "/images/avatar/avatar.jpg";
							}
						}
					}
					$Foodshot["comments"]["visible"][] = array(
						"id" => $arComment["ID"],
						"author" => array(
							"id" => $arRequestedUsers[ $arComment["CREATED_BY"] ]["ID"],
							"href" => "/profile/".$arRequestedUsers[ $arComment["CREATED_BY"] ]["ID"]."/",
							"src" => $arRequestedUsers[ $arComment["CREATED_BY"] ]["PERSONAL_PHOTO_ARRAY"]["SRC"],
							"name" => (strlen($arRequestedUsers[ $arComment["CREATED_BY"] ]["NAME"]) > 0 && strlen($arRequestedUsers[ $arComment["CREATED_BY"] ]["LAST_NAME"]) > 0 ? $arRequestedUsers[ $arComment["CREATED_BY"] ]["NAME"]." ".$arRequestedUsers[ $arComment["CREATED_BY"] ]["LAST_NAME"]:$arRequestedUsers[ $arComment["CREATED_BY"] ]["LOGIN"]),
						),
						"text" => $arComment["PREVIEW_TEXT"],
						"date" => strtolower(ConvertDateTime($arComment["DATE_CREATE"],"DD-MM-YYYY HH:MI", "ru"))
					);
				}
			}
			$Foodshot["comments"]["visible"] = array_reverse($Foodshot["comments"]["visible"]);

			// likes count
			$arAllLikesFilter = array (
				"IBLOCK_CODE" 	    => "foodshot_likes",
				"ACTIVE" => "Y",
				"PROPERTY_ELEMENT" => $arFoodshot["ID"],
				"PROPERTY_LIKE"	    => "1",
			);

			$arAllLikesSelect = array (
				"ID",
				"CREATED_BY",
			);

			$rsAllLikesItems = CIBlockElement::GetList(array(), $arAllLikesFilter, false, false, $arAllLikesSelect);
			$Foodshot["likeNum"] = intval($rsAllLikesItems->SelectedRowsCount());

			while($arAllLikesItems = $rsAllLikesItems->Fetch()){
				if ($USER->IsAuthorized() && intval($USER->GetID()) === intval($arAllLikesItems["CREATED_BY"]))
					$Foodshot["user_liked"] = "yes";			
			}	
			
			if(trim($Foodshot["name"])!="" && trim($Foodshot["text"])!="" && trim($Foodshot["author"]["name"])!="" && trim($Foodshot["href"])!="" && trim($Foodshot["image"]["src"]) ){
				$resultArray["elems"][] = $Foodshot;
			}
		}
		return $resultArray;
	}
		
	static public function getInstance(){
		return (self::$_instance !== null) ? self::$_instance : (self::$_instance = new CFoodshot());  
	}
}
?>
