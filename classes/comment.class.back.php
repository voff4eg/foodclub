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
		if(isset($intReply))$arProp['root'] = $intRoot;
		
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
		);
		
		CEvent::Send("NEW_COMMENT", array("s1"), $arFields, "N");
		return $intID;
	}
	
	/*
	 * Добавления комментария
	 */
	static public function update($intComment, $strComment, $intRecipe){
		global $USER;
		
		$rsC = CIBlockElement::GetByID($intComment);
		$arC = $rsC->Fetch();
		
		if($USER->IsAdmin() || $arC['CREATED_BY'] == $USER->GetID()){
			$arProp = Array("recipe"=>$intRecipe);
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
		
		$rsComment = CIBlockElement::GetList(Array("ID"=>"ASC"), 
											 Array("IBLOCK_ID"=>6, "ACTIVE"=>"Y", "PROPERTY_recipe"=>$mixID), 
											 False, 
											 False,
											 Array("ID","CREATED_BY", "PREVIEW_TEXT", "CREATED_USER_NAME", "DATE_CREATE", "PROPERTY_recipe", "PROPERTY_root", "PROPERTY_reply"));
											 
		while($arComment = $rsComment->GetNext()){
			$strUserName = substr($arComment['CREATED_USER_NAME'], 1, strpos($arComment['CREATED_USER_NAME'], ")")-1);
			if(strpos($strUserName, "http") !== false){
				$strUserType = "OPENID";
				if(strpos($strUserName, "livejournal") !== false){
					$strAuthType = "lj";
					$strUserLink = $strUserName;
					$strUserName = substr($strUserName, 7, (strpos($strUserName, ".livejournal")-7));
				}
			} else {
				$strUserType = "INSIDE";
				$strAuthType = "fc";
				$strUserLink = "mailto:".$arComment["EMAIL"];
			}
			$arComment['DATE_CREATE'] = substr(str_replace(" ", " в ", $arComment['DATE_CREATE']), 0, -3);
			$arComment['USER'] = Array("NAME"=>$strUserName, "TYPE"=>$strUserType, "AUTH"=>$strAuthType, "LINK"=>$strUserLink);
			
			$arResult[ $arComment['ID'] ] = $arComment;
		}
		/*									 
		while($arComment = $rsComment->GetNext()){
			
			if(is_null($arComment['PROPERTY_ROOT_VALUE'])) $arResult['root'][] = $arComment;
			else $arResult['child'][ $arComment['PROPERTY_ROOT_VALUE'] ][ $arComment['PROPERTY_REPLY_VALUE'] ][ $arComment['ID'] ] = $arComment['ID'];
			
			$arResult['all'][ $arComment['ID'] ] = $arComment;
		}//while
					
		$arDump = Array();
		foreach($arResult['root'] as $arRoot){
			
			while(count($arResult['child'][ $arRoot['ID'] ]) > 1){
				foreach($arResult['child'][ $arRoot['ID'] ][ $arRoot['ID'] ] as $arItem){
					$arDump[] = $arItem;
					if( isset($arResult['child'][ $arRoot['ID'] ][$arItem]) ){
						$arDump = array_merge_recursive($arDump, array_values($arResult['child'][ $arRoot['ID'] ][$arItem]));
						unset($arResult['child'][ $arRoot['ID'] ][$arItem]);
					}
				}
				$arResult['child'][ $arRoot['ID'] ][$arRoot['ID']] = $arDump;
				unset($arDump);
			}
			//unset($arResult['child'][ $arRoot['ID'] ][ $arRoot['ID'] ]);
			
		}
		*/
		return $arResult;
	}
	
	static public function getInstance(){
		return (self::$_instance !== null) ? self::$_instance : (self::$_instance = new CFClubComment());  
	}
}
?>