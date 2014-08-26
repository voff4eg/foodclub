<?
define("NO_KEEP_STATISTIC", true);
define('NO_AGENT_CHECK', true);
define("NO_AGENT_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
define('DisableEventsCheck', true);

include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

global $USER;
$_GET["sessid"] = $_POST["sessid"];

if(check_bitrix_sessid() && $USER->IsAuthorized())
{	
	function AddNewComment(){
		if(isset($_POST['recipe']) && intval($_POST['recipe']) > 0)$intRecipe = IntVal($_POST['recipe']);
			else $bError = true;
			
		if(isset($_POST['text']) && strlen($_POST['text']) > 0)$strComment = StrVal($_POST['text']);
			else $bError = true;
			
		if(isset($_POST['reply']))$intReply = StrVal($_POST['reply']);
			else $intReply = NULL;		
			
		if(isset($_POST['rId']))$intRoot = StrVal($_POST['rId']);
			else $intRoot = NULL;

		if(isset($_POST["comment-photo"]))$strPhoto = StrVal($_POST["comment-photo"]);
			else $strPhoto = NULL;

		preg_match("/^root([0-9]+)_id/", $_POST['parentId'], $matches, PREG_OFFSET_CAPTURE);

		if(intval($matches[1][0])){
			$intRoot = intval($matches[1][0]);			
		}
		
		if(!$bError){
			require_once($_SERVER["DOCUMENT_ROOT"].'/classes/comment.class.php');
			$obComment = CFClubCommentJSON::getInstance();
			if(CModule::IncludeModule("iblock")){
				global $USER;
				$arReturn = $obComment->add($intRecipe, $strComment, $intReply, $intRoot, $strPhoto);
				//echo "<pre>";print_r($arReturn);echo "</pre>";
				if(intval($arReturn["id"]) > 0){
					require_once($_SERVER['DOCUMENT_ROOT']."/classes/mark.class.php");
					$CMark = new CMark;
					$CMark->initIblock($arReturn["id"]);
					$rec = CIBlockElement::GetById($_POST['recipe'])->Fetch();
					if($rec["CREATED_BY"] != $USER->GetID()){
						$CMark->updateUserRait($USER->GetID(),$way = "up","r_comment_recipe");
						$CMark->updateUserRait($rec["CREATED_BY"],$way = "up","r_comment_recipe");
					}else{
						$CMark->updateUserRait($USER->GetID(),$way = "up","r_comment_recipe");
					}					
					BXClearCache(true, "/recipes_comments_cache/id".$intRecipe."/");
					global $CACHE_MANAGER;
					$CACHE_MANAGER->ClearByTag("iblock_id_".$arReturn["id"]);
					$CACHE_MANAGER->ClearByTag("recipe_comments#".$intRecipe);
					$CACHE_MANAGER->ClearByTag("recipe_comments#".$intRecipe."_comment#".$arReturn["id"]);
					/*global $CACHE_MANAGER;					
					$CACHE_MANAGER->ClearByTag("recipe_comments#".$intRecipe."_comment#".$arReturn["id"]);
					$CACHE_MANAGER->ClearByTag("recipe_comments#".$intRecipe);*/
					echo json_encode($arReturn);
				} else {
					echo '<div class="error_message">&mdash; При добавлении комментария произошла ошибка.</div>';
				}//if
			}
		} else {
			echo '<div class="error_message">&mdash; Ошибка переданных данных.</div>';
		}
	}

	function DeleteComment(){
		if(isset($_POST['id']) && intval($_POST['id']) > 0)$intComment = IntVal($_POST['id']);
			else $bError = true;
			
		if(isset($_REQUEST['rId']) && intval($_REQUEST['rId']) > 0)$intRecipe = IntVal($_REQUEST['rId']);
			else $bError = true;		
		
		if(!$bError){			
			require_once($_SERVER["DOCUMENT_ROOT"].'/classes/comment.class.php');
			$obComment = CFClubCommentJSON::getInstance();
			if(CModule::IncludeModule("iblock")){
				global $USER;
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
				$arReturn = $obComment->delete($intComment);
				BXClearCache(true, "/recipes_comments_cache/id".$intRecipe."/");
				global $CACHE_MANAGER;
				$CACHE_MANAGER->ClearByTag("iblock_id_".$arReturn["id"]);
				$CACHE_MANAGER->ClearByTag("recipe_comments#".$intRecipe);
				$CACHE_MANAGER->ClearByTag("recipe_comments#".$intRecipe."_comment#".$arReturn["id"]);
				echo json_encode($arReturn);				
			}			
		} else {
			echo '<div class="error_message">&mdash; Ошибка переданных данных.</div>';
		}
	}

	function EditComment(){
		if(isset($_POST['cId']) && intval($_POST['cId']) > 0)$intComment = IntVal($_POST['cId']);
			else $bError = true;
		
		if(isset($_POST['comment']) && strlen($_POST['comment']) > 0)$strComment = StrVal($_POST['comment']);
			else $bError = true;
			
		if(isset($_POST['rId']) && intval($_POST['rId']) > 0)$intRecipe = IntVal($_POST['rId']);
			else $bError = true;

		if(isset($_POST['root']))$intRoot = StrVal($_POST['root']);
			else $intRoot = NULL;

		if(isset($_POST["comment-photo"]))$strPhoto = StrVal($_POST["comment-photo"]);
			else $strPhoto = NULL;

		preg_match("/^root([0-9]+)_id/", $_POST['parentId'], $matches, PREG_OFFSET_CAPTURE);

		if(intval($matches[1][0])){
			$intRoot = intval($matches[1][0]);			
		}		

		if(!$bError){
			require_once($_SERVER["DOCUMENT_ROOT"].'/classes/comment.class.php');
			$obComment = CFClubCommentJSON::getInstance();
			if(CModule::IncludeModule("iblock")){
				global $USER;
				$arReturn = $obComment->update($intComment, $strComment, $intRecipe, $intRoot, $strPhoto);
				BXClearCache(true, "/recipes_comments_cache/id".$intRecipe."/");
				global $CACHE_MANAGER;
				$CACHE_MANAGER->ClearByTag("iblock_id_".$arReturn["id"]);
				$CACHE_MANAGER->ClearByTag("recipe_comments#".$intRecipe);
				$CACHE_MANAGER->ClearByTag("recipe_comments#".$intRecipe."_comment#".$arReturn["id"]);
				//BXClearCache(true, "/recipes_comments_cache/id".$intRecipe."/");
				//BXClearCache(true, "/recipes_cache/id".$intRecipe."/");
				echo json_encode($arReturn);
			}
		} else {
			echo '<div class="error_message">&mdash; Ошибка переданных данных.</div>';
		}
	}

	if(
		isset($_REQUEST["a"])		
	)
	{
		switch ($_REQUEST["a"]) {
			case 'new':
				AddNewComment();
				break;

			case 'e':
				EditComment();
				break;

			case 'delete':
				DeleteComment();
				break;
			
			default:
				# code...
				break;
		}
	}
}

die();?>