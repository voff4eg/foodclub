<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?
global $USER;
if (/*isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest' && */$USER->IsAuthorized()) {

	CModule::IncludeModule("iblock");

	//echo "@";die;

	if (intval($_REQUEST["id"]) > 0 && strlen($_REQUEST["text"]) > 0){
		$idFoodshot  = intval($_REQUEST["id"]);
		$strComment = stripslashes(htmlspecialchars(trim($_REQUEST["text"])));
		// add new comment
		$foodshotComment = new CIBlockElement;
		// получим формат сайта
		$site_format = CSite::GetDateFormat("FULL");

		// переведем формат сайта в формат PHP
		$php_format = $DB->DateFormatToPHP($site_format);

		// выведем вчерашнюю дату в формате текущего сайта
		$date = date($php_format, time());
		//$date = strtolower(ConvertDateTime(ConvertTimeStamp(false, "FULL"),"DD-MM-YYYY в HH:MI", "ru"));
		//echo "<pre>";print_R($date);echo "</pre>";
		//echo $date;die;

		$arFields = array (
			"IBLOCK_ID" => 26,
			"NAME"		  => "#".$idFoodshot." Foodshot comment",
			"PREVIEW_TEXT"	  => $strComment,
			"CREATED_BY"	  => $USER->GetID(),
			"PROPERTY_VALUES" => array (
				"72" => $idFoodshot	
			),	
			"DATE_CREATE" => $date,
		);
		if ($fId = $foodshotComment->Add($arFields)){

			/*EMAIL_TO# - Кому отсылать письмо
			FOODSHOT_NAME# - Название фудшота
			FOODSHOT_LINK# - Ссылка на фудшот
			COMMENT_TEXT# - Текст комментария
			COMMENT_AUTHOR# - Автор комментария
			COMMENT_AUTHOR_LINK# - Ссылка на профиль пользователя*/

			//echo $USER->GetEmail();die;

			//Отправка почтового сообщения автору фудшота

			$rsFoodshot = CIBlockElement::GetByID($idFoodshot);
			if($arFoodshot = $rsFoodshot->Fetch()){					
				if(intval($arFoodshot["CREATED_BY"])){
					$rsFoodshotAuthor = CUser::GetByID(intval($arFoodshot["CREATED_BY"]));
					if($arFoodshotAuthor = $rsFoodshotAuthor->Fetch()){							
						if(strlen($USER->GetFirstName()) > 0 && strlen($USER->GetLastName()) > 0){
			             	$name = $USER->GetFirstName()." ".$USER->GetLastName();
		             	}else{
		             		$name = $USER->GetLogin();
		             	}

		             	$arFields = array(
							"EMAIL_TO" => $arFoodshotAuthor["EMAIL"],
						    "FOODSHOT_NAME"	=> $arFoodshot["NAME"],
						    "FOODSHOT_LINK"	=> "http://".$_SERVER["SERVER_NAME"]."/foodshot/".$arFoodshot["ID"]."/",
						    "COMMENT_TEXT" => $strComment,
						    "COMMENT_AUTHOR" => $name,
						    "COMMENT_AUTHOR_LINK" => "http://".$_SERVER["SERVER_NAME"]."/profile/".$arFoodshotAuthor["ID"]."/"
						);

						//echo "<pre>";print_r($arFields);echo "</pre>";die;
						
						CEvent::Send("FOODSHOT_COMMENTED", array("s1"), $arFields, "N");
					}
				}					
			}			
			
			// cleaning tag cache
			global $CACHE_MANAGER;
			// $CACHE_MANAGER->ClearByTag("iblock_id_".$idFoodshot);
			
			//echo json_encode(array("id"=>$fId, "text"=>iconv("windows-1251", "utf-8",$strComment)));
			echo json_encode(array("id"=>$fId, "text"=>$strComment, "date"=> $date));
		}else{
			echo "Error: ".$foodshotComment->LAST_ERROR;
		}
	}
}	
?>