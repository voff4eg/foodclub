<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

CModule::IncludeModule("iblock");

if($_SERVER["REQUEST_METHOD"] == "post" || $_SERVER["REQUEST_METHOD"] == "GET"){
	
	global $USER;
	if ($USER->IsAuthorized()) {

		$photo = $_REQUEST["photo"];
		$title = $_REQUEST["title"];
		$descr = $_REQUEST["description"];
		$url = $_REQUEST["url"];

		if(strlen($_REQUEST["url"]) > 0){
			if(strpos($_REQUEST["url"],"http://") === false){
				if(strpos($_REQUEST["url"],"www") === false){
					$url = "http://www.".$_REQUEST["url"];
				}else{
					$url = "http://".$_REQUEST["url"];
				}		
			}elseif(strpos($_REQUEST["url"],"www") === false){
				$url = "http://www.".str_replace("http://","",$_REQUEST["url"]);		
			}
		}	

	    $path = '';

	    if(intval($_REQUEST["id"]) > 0){
			$id = intval($_REQUEST["id"]);

			$rsFoodShot = CIBlockElement::GetList(array(),array("IBLOCK_CODE"=>"foodshot_elements","ID"=>$id,"ACTIVE" => "Y"),false,false,array("ID","NAME","CREATED_BY","DETAIL_PICTURE","DETAIL_TEXT","PROPERTY_comments_count","PROPERTY_likes_count","PROPERTY_www"));
			if($arFoodShot = $rsFoodShot->GetNext()){

				if($USER->IsAdmin() || $USER->GetID() == $arFoodShot["CREATED_BY"]){

					$el = new CIBlockElement;

					$PROP = array();
					
					$arLoadProductArray = Array(
						"MODIFIED_BY"    => $USER->GetID(),
						"NAME"           => $title,
						"PREVIEW_TEXT"   => $descr,
						"DETAIL_TEXT"    => $descr,
					);
					
					$res = $el->Update($id, $arLoadProductArray);

					$rsFoodshot = CIBlockElement::GetByID($id);
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
								echo json_encode($arResult);
							}
						}
					}
				}
			}

		}else{
	 
		    $ch = curl_init($photo);
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		 
		    $data = curl_exec($ch);
		 
		    curl_close($ch);

		    if(strlen($data) > 0){

	    	
	    		$el = new CIBlockElement;

				$PROP = array();
				$filename = time()."_".$title.".jpg";
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

				$arLoadProductArray = Array(
					"MODIFIED_BY"    => $USER->GetID(),
					"IBLOCK_SECTION_ID" => false,
					"IBLOCK_ID"      => "25",
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
								echo json_encode($arResult);
							}
						}			
					}

				}else
					echo "Error: ".$el->LAST_ERROR;
	    	}
	    }

		//echo "<pre>";print_r($_REQUEST);echo "</pre>";die;
	}	
}

$this->IncludeComponentTemplate();
?>