<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
CModule::IncludeModule("iblock");

//echo "<pre>";print_r($_REQUEST);echo "</pre>";die;

$APPLICATION->RestartBuffer();
include($_SERVER['DOCUMENT_ROOT']."/bitrix/templates/fclub/service.header.php");

if($USER->IsAuthorized()){

	//echo "<pre>";print_r($_REQUEST);echo "</pre>";
	
	require($_SERVER["DOCUMENT_ROOT"].'/classes/comment.class.php');
	$obComment = CFClubComment::getInstance();

	if($_REQUEST['a'] == "new"){
		if(isset($_REQUEST['recipe']) && intval($_REQUEST['recipe']) > 0)$intRecipe = IntVal($_REQUEST['recipe']);
			else $bError = true;
			
		if(isset($_REQUEST['text']) && strlen($_REQUEST['text']) > 0)$strComment = StrVal($_REQUEST['text']);
			else $bError = true;
			
		if(isset($_REQUEST['reply']))$intReply = StrVal($_REQUEST['reply']);
			else $intReply = NULL;		
			
		if(isset($_REQUEST['root']))$intRoot = StrVal($_REQUEST['root']);
			else $intRoot = NULL;

		preg_match("/^root([0-9]+)_id/", $_REQUEST['parentId'], $matches, PREG_OFFSET_CAPTURE);

		if(intval($matches[1][0])){
			$intRoot = intval($matches[1][0]);			
		}
		
		if(!$bError){
			if($intID = $obComment->add($intRecipe, $strComment, $intReply, $intRoot)){
				if(intval($intID) > 0){
					require_once($_SERVER['DOCUMENT_ROOT']."/classes/mark.class.php");
					$CMark = new CMark;
					$CMark->initIblock($intID);
					$rec = CIBlockElement::GetById($_REQUEST['recipe'])->Fetch();
					if($rec["CREATED_BY"] != $USER->GetID()){
						$CMark->updateUserRait($USER->GetID(),$way = "up","r_comment_recipe");
						$CMark->updateUserRait($rec["CREATED_BY"],$way = "up","r_comment_recipe");
					}else{
						$CMark->updateUserRait($USER->GetID(),$way = "up","r_comment_recipe");
					}
					if($rec["CODE"]){
						LocalRedirect("/detail/".$rec["CODE"]."/#".$intID);
					}else{
						LocalRedirect("/detail/".$intRecipe."/#".$intID);
					}
				}				
			} else {
				echo '<div class="error_message">&mdash; При добавлении комментария произошла ошибка.</div>';
			}//if
		} else {
			echo '<div class="error_message">&mdash; Ошибка переданных данных.</div>';
		}
		
	}
	
	if($_REQUEST['a'] == "e"){

		if(isset($_REQUEST['cId']) && intval($_REQUEST['cId']) > 0)$intComment = IntVal($_REQUEST['cId']);
			else $bError = true;
		
		if(isset($_REQUEST['comment']) && strlen($_REQUEST['comment']) > 0)$strComment = StrVal($_REQUEST['comment']);
			else $bError = true;
			
		if(isset($_REQUEST['rId']) && intval($_REQUEST['rId']) > 0)$intRecipe = IntVal($_REQUEST['rId']);
			else $bError = true;

		if(isset($_REQUEST['root']))$intRoot = StrVal($_REQUEST['root']);
			else $intRoot = NULL;

		preg_match("/^root([0-9]+)_id/", $_REQUEST['parentId'], $matches, PREG_OFFSET_CAPTURE);

		if(intval($matches[1][0])){
			$intRoot = intval($matches[1][0]);			
		}
		
		if(!$bError){
			if($obComment->update($intComment, $strComment, $intRecipe, $intRoot)){
				$rec = CIBlockElement::GetById($intRecipe)->Fetch();
				if($rec["CODE"]){
					LocalRedirect("/detail/".$rec["CODE"]."/#".$intComment);
				}else{
					LocalRedirect("/detail/".$intRecipe."/#".$intComment);
				}				
			} else {
				echo '<div class="error_message">&mdash; При редактировании комментария произошла ошибка.</div>';
			}//if
		} else {
			echo '<div class="error_message">&mdash; Ошибка переданных данных.</div>';
		}
		
	}
	
	if($_REQUEST['a'] == "d"){
		if(isset($_REQUEST['cId']) && intval($_REQUEST['cId']) > 0)$intComment = IntVal($_REQUEST['cId']);
			else $bError = true;
			
		preg_match("/^root([0-9]+)_id/", $_REQUEST['delete_comment_id'], $matches, PREG_OFFSET_CAPTURE);

		if(intval($matches[1][0])){
			$intComment = intval($matches[1][0]);
			$bError = false;
		}

		if(isset($_REQUEST['rId']) && intval($_REQUEST['rId']) > 0)$intRecipe = IntVal($_REQUEST['rId']);
			else $bError = true;
		

		if(!$bError){
			$com = CIBlockElement::GetList(array(),array("ID"=>$intComment),false,false,array("CREATED_BY"))->Fetch();
			require_once($_SERVER['DOCUMENT_ROOT']."/classes/mark.class.php");
			$CMark = new CMark;
			$CMark->initIblock($_REQUEST['rId']);
			$rec = CIBlockElement::GetById($_REQUEST['rId'])->Fetch();
			if($rec["CREATED_BY"] != $com["CREATED_BY"]){
				$CMark->updateUserRait($com["CREATED_BY"],$way = "low","r_comment_recipe");
				$CMark->updateUserRait($rec["CREATED_BY"],$way = "low","r_comment_recipe");
			}else{
				$CMark->updateUserRait($com["CREATED_BY"],$way = "low","r_comment_recipe");
			}
			if($obComment->delete($intComment)){
				if($rec["CODE"]){
					LocalRedirect("/detail/".$rec["CODE"]."/#add_opinion");
				}else{
					LocalRedirect("/detail/".$intRecipe."/#add_opinion");
				}				
			} else {
				echo '<div class="error_message">&mdash; При удалении комментария произошла ошибка.</div>';
			}//if
		} else {
			echo '<div class="error_message">&mdash; Ошибка переданных данных.</div>';
		}
	}//if
	
} else {
	LocalRedirect("/auth/");
} //if

include($_SERVER['DOCUMENT_ROOT']."/bitrix/templates/fclub/service.footer.php"); die;?>