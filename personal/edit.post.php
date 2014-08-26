<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

if($USER->IsAuthorized()){
	
	if( strlen($_FILES["PERSONAL_PHOTO"]['name']) > 0 ){
		$image = $_FILES["PERSONAL_PHOTO"]['tmp_name'];
		list($width, $height, $type, $attr) = getimagesize($image);
		$ext_name = time();
		if($width > 500 || $height > 500)
		{
			$Koef = $width/$height;
			if($width >= $height)
			{
				$newWidth = 500;
				$newHeight = $newWidth/$Koef;
			} 
			elseif($width < $height)
			{
				$newHeight = 500;
				$newWidth = $newHeight/$Koef;
			}
			
			$tmp_name = time();
            $dest = $_SERVER['DOCUMENT_ROOT'].'/upload/tmp/'.$ext_name.$_FILES["PERSONAL_PHOTO"]['name'];
			if(!CAllFile::ResizeImageFile($_FILES["PERSONAL_PHOTO"]['tmp_name'], $dest, Array("width"=>$newWidth, "height"=>$newHeight) ) ){
			    echo "Internal error!";
			};

		} else {
		    $dest = $_SERVER['DOCUMENT_ROOT'].'/upload/tmp/'.$ext_name.$_FILES["PERSONAL_PHOTO"]['name'];
		    move_uploaded_file($image, $dest);
		}
				
		function setHeaderContent(){
			ob_start();
			?>
			<script src="/js/jquery.Jcrop.pack.js"></script>
			<link rel="stylesheet" href="/css/jquery.Jcrop.css" type="text/css" />
			<script language="Javascript">

			jQuery(function(){
				jQuery('#cropbox').Jcrop({
					aspectRatio: 1,
					setSelect: [ 50, 50, 100, 100 ],
					onSelect: updateCoords,
				});
			});
			
			function updateCoords(c){
				jQuery('#x').val(c.x);
				jQuery('#y').val(c.y);
				jQuery('#w').val(c.w);
				jQuery('#h').val(c.h);
			};
						
			function checkCoords(){
				if (parseInt(jQuery('#w').val())>0) return true;
				alert('Пожалуйста, выберете область и нажмите Отправить.');
				return false;
			};
			
			</script>
			<?
			$strHtml = ob_get_contents();
			ob_end_clean();
			return $strHtml;
		}
		?>
		<div id="content">
			<div id="text_space">
				<h1>Редактирование аватары</h1>
				<form action="" method="POST" onsubmit="return checkCoords();">
				<?foreach($_POST as $Key=>$Value){?>
					<input type="hidden" name="<?=$Key?>" value="<?=$Value?>">
				<?}?>
					<div><img id="cropbox" src="/upload/tmp/<?=$ext_name.$_FILES["PERSONAL_PHOTO"]['name']?>" alt=""></div>
					<p>Выберите необходимую область.</p>
					<input type="hidden" id="x" name="x" value="" />
					<input type="hidden" id="y" name="y" value="" />
					<input type="hidden" id="w" name="w" value="" />
					<input type="hidden" id="h" name="h" value="" />
					<input type="hidden" name="file" value="/upload/tmp/<?=$ext_name.$_FILES["PERSONAL_PHOTO"]['name']?>">
					<input type="submit" style="margin-top:10px; " value="Cохранить">
				</form>
			</div>
			<div class="clear"></div>
		</div>
		<?
		require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
		
	}else{

		if(isset($_POST['file'])){
			
			$targ_w = $targ_h = 100;
			$jpeg_quality = 90;
			
			$src = $_SERVER['DOCUMENT_ROOT'].$_POST['file'];
			$img_r = imagecreatefromjpeg($src);
			
			$dst_r = ImageCreateTrueColor( $targ_w, $targ_h );
			
			imagecopyresampled($dst_r,$img_r,0,0,$_POST['x'],$_POST['y'],
			    $targ_w,$targ_h,$_POST['w'],$_POST['h']);
			    
			$output = $_SERVER['DOCUMENT_ROOT']."/upload/tmp/avatar.jpg";
			imagejpeg($dst_r, $output, $jpeg_quality);
			
			$arAvatar = CFile::MakeFileArray($_SERVER['DOCUMENT_ROOT']."/upload/tmp/avatar.jpg");
						
		}
		
		
		$bOk = false;
		$obUser = new CUser;
		$arResult["ID"]=intval($USER->GetID());
	
		$arPERSONAL_PHOTO = $arAvatar;
		$arPERSONAL_PHOTO["old_file"] = IntVal($_REQUEST["PERSONAL_PHOTO_ID"]);
		$arPERSONAL_PHOTO["del"] = $_REQUEST["PERSONAL_PHOTO_del"];
	
	
		$arFields = Array(
			"NAME"					=> $_REQUEST["NAME"],
			"LAST_NAME"				=> $_REQUEST["LAST_NAME"],
			//"SECOND_NAME"			=> $_REQUEST["SECOND_NAME"],
			"EMAIL"					=> $_REQUEST["EMAIL"],
			"LOGIN"					=> $_REQUEST["LOGIN"],
			"PERSONAL_GENDER"		=> $_REQUEST["PERSONAL_GENDER"],
			"WORK_WWW"				=> $_REQUEST["WORK_WWW"],
			"PERSONAL_BIRTHDAY"		=> $_REQUEST["birthday"].".".$_REQUEST["birthmonth"].".".$_REQUEST["birthyear"],
			"UF_INTEREST"			=> $_REQUEST["UF_INTEREST"],
			"UF_ABOUT_SELF"			=> $_REQUEST["UF_ABOUT_SELF"],
			"PERSONAL_PHOTO"		=> $arPERSONAL_PHOTO,
			);
	
		$rsUser = CUser::GetByID($arResult["ID"]);
		if ($arUser = $rsUser->Fetch())
		{
			if(strlen($arUser['EXTERNAL_AUTH_ID']) > 0)
			{
				$arFields['EXTERNAL_AUTH_ID'] = $arUser['EXTERNAL_AUTH_ID'];
			}
		}
			
		if(strlen($_REQUEST["NEW_PASSWORD"])>0)
		{
			$arFields["PASSWORD"]=$_REQUEST["NEW_PASSWORD"];
			$arFields["CONFIRM_PASSWORD"]=$_REQUEST["NEW_PASSWORD_CONFIRM"];
		}
		/*if(!isset($_REQUEST["UF_PROFILE_FULL"]) || strlen($_REQUEST["UF_PROFILE_FULL"]) <= 0){
			$arFields["UF_PROFILE_FULL"] = serialize(array("full"=>"true"));
			//echo "<pre>"; print_r($arFields); echo "</pre>";die;
		}else{
			$UF_PROFILE_FULL = unserialize($_REQUEST["UF_PROFILE_FULL"]);
			if($UF_PROFILE_FULL["full"] != "true"){
				$UF_PROFILE_FULL["full"] = "true";
				$arFields["UF_PROFILE_FULL"] = serialize($UF_PROFILE_FULL);
			}
		}*/
		//$UF_PROFILE = $arFields["UF_PROFILE_FULL"];
		$GLOBALS["USER_FIELD_MANAGER"]->EditFormAddFields("USER", $arFields);
		//$arFields["UF_PROFILE_FULL"] = serialize($UF_PROFILE);
		$rate = 1;
		//unset($arFields["UF_PROFILE_FULL"]);
		foreach($arFields as $field){
			if(is_array($field)){
				if(!empty($field)){}else{$rate = 0;}
			}else{
				if(strlen(trim($field)) > 0){
					//TODO:nothing
				}else{
					if(key($field) != "PASSWORD" && key($field) != "CONFIRM_PASSWORD"){
						$rate = 0;
					}
				}
			}
		}
		if($rate > 0 && strlen($_REQUEST["UF_PROFILE_FULL"]) <= 0){
			$SQL = "SELECT UF_PROFILE_FULL FROM b_uts_user WHERE VALUE_ID = ".$USER->GetID();
			global $DB;
			$rowFields = $DB->Query($SQL, false);
			if ( $Row = $rowFields->Fetch())
			{
				if(strlen($Row["UF_PROFILE_FULL"]) <= 0){
					require_once($_SERVER['DOCUMENT_ROOT']."/classes/mark.class.php");
					$CMark = new CMark;
					$CMark->initIblock("",false,true);
					$CMark->updateUserRait($USER->GetID(),$way = "up","update_profile");
					// Записываем
					$arFields["UF_PROFILE_FULL"] = serialize(array("full"=>"true"));
					if($arResult["ID"] > 0) $res = $obUser->Update($arResult["ID"], $arFields, true);
				}else{
					$arFields["UF_PROFILE_FULL"] = $Row["UF_PROFILE_FULL"];
					if($arResult["ID"] > 0) $res = $obUser->Update($arResult["ID"], $arFields, true);
				}
			}
		}elseif($rate == 0){
			$SQL = "SELECT UF_PROFILE_FULL FROM b_uts_user WHERE VALUE_ID = ".$USER->GetID();
			global $DB;
			$rowFields = $DB->Query($SQL, false);
			if ( $Row = $rowFields->Fetch())
			{				
				if(strlen($Row["UF_PROFILE_FULL"]) > 0){
					$profile_full = unserialize($Row["UF_PROFILE_FULL"]);
					if($profile_full["full"] == "true"){
						unset($profile_full["full"]);
						if(!empty($profile_full)){
							$arFields["UF_PROFILE_FULL"] = serialize($profile_full);
							require_once($_SERVER['DOCUMENT_ROOT']."/classes/mark.class.php");
							$CMark = new CMark;
							$CMark->initIblock("",false,true);
							$CMark->updateUserRait($USER->GetID(),$way = "low","update_profile");	
						}else{
							$arFields["UF_PROFILE_FULL"] = "";
							require_once($_SERVER['DOCUMENT_ROOT']."/classes/mark.class.php");
							$CMark = new CMark;
							$CMark->initIblock("",false,true);
							$CMark->updateUserRait($USER->GetID(),$way = "low","update_profile");	

						}
					}else{
						$arFields["UF_PROFILE_FULL"] = "";
					}
				}
			}
			if($arResult["ID"] > 0) $res = $obUser->Update($arResult["ID"], $arFields, true);
		}
		
		$_SESSION['ERROR'] = $obUser->LAST_ERROR;
		LocalRedirect("/profile/");
	}
	
} else {
	LocalRedirect("/auth/?backurl=/profile/edit/");
}
