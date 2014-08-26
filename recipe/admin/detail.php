<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
LocalRedirect("/404.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/classes/favorite.class.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/classes/factory.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/mark.class.php");

if (CModule::IncludeModule("advertising")){
    $strBanner = CAdvBanner::Show("right_banner");
    $strBookBanner = CAdvBanner::Show("book_banner");
	$strSecond_banner = CAdvBanner::Show("second_right_banner");
}
$cache_time = "9999999999";
$mixCount = 0;
$arResult = array();
$cache_dir = "/recipes_cache/id".IntVal($_REQUEST['r']);
$cache_id = "id".IntVal($_REQUEST['r']);
$obCache = new CPHPCache;
if($obCache->InitCache($cache_time, $cache_id, $cache_dir)){
	$vars = $obCache->GetVars();	
	$arResult = $vars["recipe"];
	$arAuthor = $vars["owner"];
	$arAllUnits = $vars["units"];
	$strStages = $vars["stages"];
	$strBlockLike = $vars["like"];
	$mixCount = $vars["ccount"];
	
	$Favorite = new CFavorite;
	$Factory = new CFactory;
	$CMark = new CMark;
	$CFClub = CFClub::getInstance();
	global $USER;	
	global $arKitchens;
	global $arDishType;
	
	if(strlen($arResult['PROPERTY_TITLE_VALUE']) > 0){
		$APPLICATION->SetPageProperty("title", $arResult['PROPERTY_TITLE_VALUE']);
	} else {
		$APPLICATION->SetPageProperty("title", $arResult['NAME']." &mdash; рецепт с пошаговыми фото. Foodclub.ru");
	}

	$APPLICATION->SetPageProperty("keywords", (strlen($arResult['PROPERTY_KEYWORDS_VALUE'])>0?$arResult['PROPERTY_KEYWORDS_VALUE']:"фото рецепты, рецепты с фотографиями, фото блюд"));

	$APPLICATION->AddHeadString('<meta property="og:title" content="'.$arResult["NAME"].'"/>',true);
	$APPLICATION->AddHeadString('<meta property="og:type" content="food"/>',true);
	if(IntVal($arResult['DETAIL_PICTURE']) > 0){
		$APPLICATION->AddHeadString('<meta property="og:image" content="http://www.'.$_SERVER["SERVER_NAME"].CFile::GetPath($arResult['DETAIL_PICTURE']).'" />',true);
	}
	$APPLICATION->AddHeadString('<meta property="og:url" content="http://www.'.$_SERVER["SERVER_NAME"].$APPLICATION->GetCurDir().'" />',true);
	$APPLICATION->AddHeadString('<meta property="og:site_name" content="Кулинарные рецепты с пошаговыми фотографиями"/>',true);
	$APPLICATION->AddHeadString('<meta property="og:description" content="'.strip_tags($arResult["PREVIEW_TEXT"]).'"/>',true);
	
	if(strlen($arResult["PREVIEW_TEXT"]) > 0){
		$APPLICATION->SetPageProperty("description", strip_tags($arResult["PREVIEW_TEXT"]));
	}else{
		$APPLICATION->SetPageProperty("description", "Рецепты блюд с фотографиями и пошаговыми инструкциями.");
	}
	$isOwner = false;
	if($USER->IsAuthorized()){
		if($arResult['CREATED_BY'] == $USER->GetID()){
			$isOwner = true;
		}
	}
}elseif(CModule::IncludeModule("iblock") && $obCache->StartDataCache()){	
	//Получение данных рецепта из БД
	$rsResult = CIBlockElement::GetList(Array(), Array("IBLOCK_CODE"=>"recipe","ID"=>IntVal($_REQUEST['r'])), false, false, Array("ID", "NAME", "DATE_ACTIVE_FROM", "CREATED_BY", "TAGS", "PREVIEW_PICTURE", "DETAIL_PICTURE", "PREVIEW_TEXT", "PROPERTY_kitchen", "PROPERTY_dish_type", "PROPERTY_recipt_steps", "PROPERTY_comment_count", "PROPERTY_title", "PROPERTY_keywords", "PROPERTY_description", "PROPERTY_block_photo", "PROPERTY_block_search", "PROPERTY_block_like",
	"PROPERTY_main_ingredient","PROPERTY_kcals","PROPERTY_portion","PROPERTY_cooking_time","PROPERTY_vegetarian","PROPERTY_technics"));
	if($arRecipe = $rsResult->GetNext()){

		$arResult = $arRecipe;
		global $CACHE_MANAGER;
		$CACHE_MANAGER->StartTagCache($cache_dir);
		
		$Favorite = new CFavorite;
		$Factory = new CFactory;
		$CMark = new CMark;
		$CFClub = CFClub::getInstance();
		global $USER;
		global $arKitchens;
		global $arDishType;
		
		if(strlen($arResult['PROPERTY_TITLE_VALUE']) > 0){
			$APPLICATION->SetPageProperty("title", $arResult['PROPERTY_TITLE_VALUE']);
		} else {

			$APPLICATION->SetPageProperty("title", $arResult['NAME']." &mdash; рецепт с пошаговыми фото. Foodclub.ru");
		}

		$APPLICATION->SetPageProperty("keywords", (strlen($arResult['PROPERTY_KEYWORDS_VALUE'])>0?$arResult['PROPERTY_KEYWORDS_VALUE']:"фото рецепты, рецепты с фотографиями, фото блюд"));

		$APPLICATION->AddHeadString('<meta property="og:title" content="'.$arResult["NAME"].'"/>',true);
		$APPLICATION->AddHeadString('<meta property="og:type" content="food"/>',true);
		if(IntVal($arResult['DETAIL_PICTURE']) > 0){
			$APPLICATION->AddHeadString('<meta property="og:image" content="http://www.'.$_SERVER["SERVER_NAME"].CFile::GetPath($arResult['DETAIL_PICTURE']).'" />',true);
		}
		$APPLICATION->AddHeadString('<meta property="og:url" content="http://www.'.$_SERVER["SERVER_NAME"].$APPLICATION->GetCurDir().'" />',true);
		$APPLICATION->AddHeadString('<meta property="og:site_name" content="Кулинарные рецепты с пошаговыми фотографиями"/>',true);
		$APPLICATION->AddHeadString('<meta property="og:description" content="'.strip_tags($arResult["PREVIEW_TEXT"]).'"/>',true);
		
		if(strlen($arResult["PREVIEW_TEXT"]) > 0){
			$APPLICATION->SetPageProperty("description", strip_tags($arResult["PREVIEW_TEXT"]));
		}else{
			$APPLICATION->SetPageProperty("description", "Рецепты блюд с фотографиями и пошаговыми инструкциями.");
		}
		
		if(intval($arResult["PROPERTY_MAIN_INGREDIENT_VALUE"]) > 0){
			$arResult["MAIN_INGREDIENT"] = CIBlockElement::GetByID($arResult["PROPERTY_MAIN_INGREDIENT_VALUE"])->Fetch();
		}
		
		$isOwner = false;
		if($USER->IsAuthorized()){
			if($arResult['CREATED_BY'] == $USER->GetID()){
				$isOwner = true;
			}
		}
		
		$arSteps[] = $arResult['PROPERTY_RECIPT_STEPS_VALUE'];
		$TechnicsID = array();
		if(intval($arResult['PROPERTY_TECHNICS_VALUE']) > 0){
			$TechnicsID[] = $arResult['PROPERTY_TECHNICS_VALUE'];
		}
		
		if(!is_null($arResult['PROPERTY_BLOCK_LIKE_VALUE'])) $arLike[] = $arResult['PROPERTY_BLOCK_LIKE_VALUE'];
		while($arItem = $rsResult->GetNext()){
			$arSteps[ $arItem['PROPERTY_RECIPT_STEPS_VALUE_ID'] ] = $arItem['PROPERTY_RECIPT_STEPS_VALUE'];
			if(!is_null($arItem['PROPERTY_BLOCK_LIKE_VALUE'])) $arLike[ $arItem['PROPERTY_BLOCK_LIKE_VALUE_ID'] ] = $arItem['PROPERTY_BLOCK_LIKE_VALUE'];
			if(!in_array($arItem['PROPERTY_TECHNICS_VALUE'],$TechnicsID) && intval($arItem['PROPERTY_TECHNICS_VALUE']) > 0){
				$TechnicsID[] = $arItem['PROPERTY_TECHNICS_VALUE'];
			}
		}
		if(!empty($TechnicsID)){
			$rsTechnics = CIBlockElement::GetList(array("name"=>"asc"),array("IBLOCK_CODE"=>"technics","ID"=>$TechnicsID), false, false, array("ID","NAME","PREVIEW_PICTURE","PROPERTY_link"));
			while($arTechnic = $rsTechnics->GetNext()){
				if(intval($arTechnic["PREVIEW_PICTURE"]) > 0){
					$arFile = CFile::GetFileArray($arTechnic["PREVIEW_PICTURE"]);
					if($arFile)
						$arTechnic["PICTURE"] = $arFile["SRC"];
				}
				$Technics[] = $arTechnic;
			}
		}
		$arResult['PROPERTY_TECHNICS_VALUE'] = $Technics;
		$arResult['PROPERTY_RECIPT_STEPS_VALUE'] = $arSteps;
		$arResult['PROPERTY_BLOCK_LIKE_VALUE'] = $arLike;
		
		$strHtml = '';
		$strJavaOne = '';
		$strJavaTwo = '';
		$intNum = 0;

		foreach ($arKitchens as $arItem)
		{
			$strHtml .= '<option value="'.$arItem['ID'].'" '.($arProp['kitchen']['VALUE'] == $arItem['ID'] ? "selected='selected'" : "").'>'.$arItem['NAME'].'</option>';
			if (strlen($strJavaOne) == 0) {$strJavaOne = '"'.$arItem['ID'].'"';} else {$strJavaOne .= ', "'.$arItem['ID'].'"';}

			$strJavaTwo .= 'cookingArray[1]['.$intNum.'] = new Array();';
			$strDumpId = ''; $strDumpName = '';

			$arItem['DISH'][0] = $arItem['DISH'][ $arProp['dish_type']['VALUE'] ];
			unset($arItem['DISH'][ $arProp['dish_type']['VALUE'] ]);

			foreach ($arItem['DISH'] as $arDash)
			{
				if (strlen($strDumpId) == 0) {$strDumpId = '"'.$arDash['ID'].'"';} else {$strDumpId .= ', "'.$arDash['ID'].'"';}
				if (strlen($strDumpName) == 0) {$strDumpName = '"'.$arDash['NAME'].'"';} else {$strDumpName .= ', "'.$arDash['NAME'].'"';}
			}
			$strJavaTwo .= "cookingArray[1][".$intNum."][0] = new Array($strDumpId);";
			$strJavaTwo .= "cookingArray[1][".$intNum."][1] = new Array($strDumpName);";
			$intNum++;
		}

		$arUnits = $CFClub->getUnitList();

		$strUnitHtml = '';
		$strUnitsHtml = '';
		$intNum = 0;
		$strTemp = '';
		foreach ($arUnits as $arUnit)
		{
			if (!isset($strId)) {$strId = '"'.$arUnit['ID'].'"';} else {$strId .= ', "'.$arUnit['ID'].'"';}
			if (!isset($strName)) {$strName = '"'.$arUnit['NAME'].'"';} else {$strName .= ', "'.$arUnit['NAME'].'"';}
			$strDumpId = ''; $strDumpName = ''; $strDumpUnit = '';
			$strTemp .= "ingredientsArray[2][$intNum] = new Array();";
			foreach($arUnit['UNITS'] as $intKey => $arItem)
			{
				if (strlen($strDumpId) == 0) {$strDumpId = '"'.$arItem['ID'].'"';} else {$strDumpId .= ', "'.$arItem['ID'].'"';}
				if (strlen($strDumpName) == 0) {$strDumpName = '"'.$arItem['NAME'].'"';} else {$strDumpName .= ', "'.$arItem['NAME'].'"';}
				if (strlen($strDumpUnit) == 0) {$strDumpUnit = '"'.$arItem['UNIT'].'"';} else {$strDumpUnit .= ', "'.$arItem['UNIT'].'"';}
				$arSortArray[ $arItem['ID'] ][0] = $intNum;
				$arSortArray[ $arItem['ID'] ][1] = $intKey;
			}
			$strTemp .= "ingredientsArray[2][".$intNum."][0] = new Array($strDumpId);";
			$strTemp .= "ingredientsArray[2][".$intNum."][1] = new Array($strDumpName);";
			$strTemp .= "ingredientsArray[2][".$intNum."][2] = new Array($strDumpUnit);";

			$intNum++;
		}
		$strUnitHtml .= "ingredientsArray[0] = new Array($strId);	ingredientsArray[1] = new Array($strName); ingredientsArray[2] = new Array();".$strTemp;

		$rsMainFile = CFile::GetByID($arResult['DETAIL_PICTURE']);
		$arMainFile = $rsMainFile->Fetch();
		$arResult["MainFile"] = $arMainFile;

		$intCount = count($arResult['PROPERTY_RECIPT_STEPS_VALUE']);

		$rsStages = CIBlockElement::GetList(Array(), Array("ID"=>$arResult['PROPERTY_RECIPT_STEPS_VALUE']), false, false, Array("ID", "NAME", "PREVIEW_PICTURE", "PREVIEW_TEXT", "PROPERTY_ingredient", "PROPERTY_numer", "PROPERTY_parent"));
		while($arStage = $rsStages->GetNext()){

			$arStages[ $arStage['ID'] ]['ID'] = $arStage['ID'];
			$arStages[ $arStage['ID'] ]['NAME'] = $arStage['NAME'];
			$arStages[ $arStage['ID'] ]['PREVIEW_PICTURE'] = $arStage['PREVIEW_PICTURE'];
			$arStages[ $arStage['ID'] ]['~PREVIEW_TEXT'] = $arStage['~PREVIEW_TEXT'];
			$arStages[ $arStage['ID'] ]['PREVIEW_TEXT'] = $arStage['PREVIEW_TEXT'];
			$arStages[ $arStage['ID'] ]['PROPERTY_INGREDIENT_VALUE'][ $arStage['PROPERTY_INGREDIENT_VALUE_ID'] ] = $arStage['PROPERTY_INGREDIENT_VALUE'];
			$arStages[ $arStage['ID'] ]['PROPERTY_NUMER_VALUE'][ $arStage['PROPERTY_NUMER_VALUE_ID'] ] = $arStage['PROPERTY_NUMER_VALUE'];
			$arStages[ $arStage['ID'] ]['PROPERTY_PARENT_VALUE'] = $arStage['PROPERTY_PARENT_VALUE'];

		}
			
		global $DB;
		foreach($arStages as $Item)
		{
			$ElementId = $Item['ID'];

			$sqlIngridient = "SELECT * FROM `b_iblock_element_property` WHERE `IBLOCK_ELEMENT_ID` = ".$ElementId." AND `IBLOCK_PROPERTY_ID` = 3 ORDER BY `ID` ASC";
			$sqlNumer = "SELECT * FROM `b_iblock_element_property` WHERE `IBLOCK_ELEMENT_ID` = ".$ElementId." AND `IBLOCK_PROPERTY_ID` = 4 ORDER BY `ID` ASC";

			$rowFields = $DB->Query($sqlIngridient, false);
			while($Field = $rowFields->Fetch()){
				$arStages[ $Item['ID'] ]['PROPERTY_INGREDIENT_VALUE'][ $Field['ID'] ] = $Field['VALUE'];
			}
			ksort($arStages[ $Item['ID'] ]['PROPERTY_INGREDIENT_VALUE']);
			reset($arStages[ $Item['ID'] ]['PROPERTY_INGREDIENT_VALUE']);

			$rowFields = $DB->Query($sqlNumer, false);
			while($Field = $rowFields->Fetch()){
				$arStages[ $Item['ID'] ]['PROPERTY_NUMER_VALUE'][ $Field['ID'] ] = $Field['VALUE'];
			}
			ksort($arStages[ $Item['ID'] ]['PROPERTY_NUMER_VALUE']);
			reset($arStages[ $Item['ID'] ]['PROPERTY_NUMER_VALUE']);
		}

		$strMKey = 0;
		ob_start();
		$bFirst = true;?>
		<div class="instructions">
				<div class="description">
						<?=$arResult['~PREVIEW_TEXT']?>
				</div>
				<div class="border"></div>
		<?
		$kkals = 0;
		foreach($arStages as $strStep){

			$arStageFields = Array(
								"ID" => $strStep['ID'],
								"NAME" => $strStep['NAME'],
								"PREVIEW_PICTURE" => $strStep['PREVIEW_PICTURE'],
								"PREVIEW_TEXT" => $strStep['PREVIEW_TEXT'],
								"~PREVIEW_TEXT" => $strStep['~PREVIEW_TEXT']
								);
			$arStageProp = Array(
								"ingredient" => $strStep['PROPERTY_INGREDIENT_VALUE'],
								"numer" => $strStep['PROPERTY_NUMER_VALUE'],
								"parent" => $strStep['PROPERTY_PARENT_VALUE'],
								);
			$arKey = array_keys($arStageProp['ingredient']);
			$arStageProp['numer'] = array_combine($arKey, $arStageProp['numer']);

			$rsIng = CIBlockElement::GetList(Array(), Array("ID"=>$arStageProp['ingredient'],"IBLOCK_ID"=>3), false, false, Array("ID","NAME","PROPERTY_unit","PROPERTY_kkal"));
			while($arIngSrc = $rsIng->GetNext()){
				$arIng[ $arIngSrc['ID'] ] = $arIngSrc;				
			}
			foreach($arStageProp['ingredient'] as $strKey => $strItem){
				if(!is_null($strItem)){
					$arUnitsVal[] = Array(
						"ID"    => $strItem,
						"NAME"  => $arIng[ $strItem ]['NAME'],
						"UNIT"  => $arIng[ $strItem ]['PROPERTY_UNIT_VALUE'],
						"VALUE" => $arStageProp['numer'][ $strKey ],
					);

					$arAllUnits[$strItem][] = Array(
						"ID"    => $strItem,
						"NAME"  => $arIng[ $strItem ]['NAME'],
						"UNIT"  => $arIng[ $strItem ]['PROPERTY_UNIT_VALUE'],
						"VALUE" => $arStageProp['numer'][ $strKey ],
						"KKAL"  => $arIng[ $strItem ]['PROPERTY_KKAL_VALUE'],
					);
					if(floatval(str_replace(",",".",$arIng[ $strItem ]['PROPERTY_KKAL_VALUE'])) <= 0){
						$kkal = 0;
					}else{
						$kkal = floatval(str_replace(",",".",$arIng[ $strItem ]['PROPERTY_KKAL_VALUE']));
					}
					if(floatval(str_replace(",",".",$arStageProp['numer'][ $strKey ])) <= 0){
						$mass = 0;
					}else{
						$mass = floatval(str_replace(",",".",$arStageProp['numer'][ $strKey ]));
					}
					//echo $mass." * ".$kkal." = ".$mass*$kkal;
					$kkals += $mass * $kkal;
				}
			}
			$arResult["kkals"] = $kkals;
			$rsFile = CFile::GetByID($arStageFields['PREVIEW_PICTURE']);
			$arFile = $rsFile->Fetch();?>
			<?if(!$bFirst){?><div class="border"></div><?} else {$bFirst = false;}?>
				<div class="stage">
					<div class="body">
					<?if($arResult["PROPERTY_VEGETARIAN_VALUE"] == "Y"):?><input type="hidden" name="vegetarian" value="y" /><?endif;?>
					<?if(count($arUnitsVal) > 0){?>
					<div class="needed">
						<h2>Ингредиенты <?=$strMKey+1;?>-го этапа:</h2>
						<table>
							<?$flag = false;
							foreach($arUnitsVal as $strKey => $arItem):
								if($flag == false){ echo "<tr>";}?>
								<td class="ing_name<?=($flag == true ? " border" : "")?>"><?=$arItem['NAME']?></td>
								<td class="ing_amount"><?=str_replace(Array("1/2", "1/4", "3/4"), Array("&frac12;","&frac14;","&frac34;"), $arItem['VALUE'])?> <?=$arItem['UNIT']?></td><?
								if($flag == true){ echo "</tr>";}
								if($flag == false){$flag = true;}else{$flag = false;}
							endforeach; 
							if(count($arUnitsVal) % 2 != 0){
								echo "<td class='ing_name border'></td><td class='ing_amount'></td></tr>";
							}
							unset($arUnitsVal);?>
						</table>
					</div>
					<?} //if?>
					<?if(IntVal($arStageFields['PREVIEW_PICTURE']) > 0):?>
						<div class="image"><div class="screen"><div style="width: <?=$arFile['WIDTH']?>px; height: <?=$arFile['HEIGHT']?>px;"></div></div><img src="/upload/<?=$arFile['SUBDIR']?>/<?=$arFile['FILE_NAME']?>" alt="<?=(strlen($arFile['DESCRIPTION']) > 0 ? $arFile['DESCRIPTION'] : $arFields['NAME'])?>" title="<?=(strlen($arFile['DESCRIPTION']) > 0 ? $arFile['DESCRIPTION'] : $arFields['NAME'])?>" width="<?=$arFile['WIDTH']?>" height="<?=$arFile['HEIGHT']?>" class="photo"></div>
					<?endif;?>
					<?if(strlen($arStageFields['~PREVIEW_TEXT']) > 0){?>
						<div class="instruction">
							<?=$arStageFields['~PREVIEW_TEXT']?>
						</div>
					<?}?>
					</div>

					<div class="clear"></div>
				</div>

			<?
			$strMKey++;
		}?>
		</div>
		<?
		$strStages = ob_get_contents();
		ob_end_clean();
		
		$rsAuthor = CUser::GetByID($arResult['CREATED_BY']);
		if($arAuthor = $rsAuthor->Fetch()){
			if(strpos($arAuthor['EXTERNAL_AUTH_ID'], "OPENID") !== false){

				if(strpos($arAuthor['LOGIN'], "livejournal") !== false){					
					$arAuthor['LOGIN_TYPE'] = "lj";					
				}

			} else {
				$arAuthor['LOGIN_TYPE'] = "fc";
			}
			$arAuthor['URL'] = "/profile/".$arAuthor['ID']."/";

			if(strlen($arAuthor["NAME"]) > 0 && strlen($arAuthor["LAST_NAME"]) > 0){
		     	$arAuthor["FULLNAME"] = $arAuthor["NAME"]." ".$arAuthor["LAST_NAME"];
		 	}else{
		 		$arAuthor["FULLNAME"] = $arAuthor["LOGIN"];
		 	}			

			if(intval($arAuthor['PERSONAL_PHOTO']) > 0){
				$rsAvatar = CFile::GetByID($arAuthor['PERSONAL_PHOTO']);
				if($Avatar = $rsAvatar->Fetch()){
					$arAuthor["avatar"] = "/upload/".$Avatar['SUBDIR']."/".$Avatar['FILE_NAME'];					
				}
			} else {
				$arAuthor["avatar"] = "/images/avatar/avatar.jpg";
			}

			$rsCount = CIBlockElement::GetProperty(5, $arResult["ID"], "sort", "asc", Array("CODE"=>"comment_count"));
			if($arCount = $rsCount->Fetch()){
				$mixCount = IntVal($arCount["VALUE"]);
				$CACHE_MANAGER->RegisterTag("recipe#".IntVal($_REQUEST['r'])."#ccount");
			}
		}
		
		if(!is_null($arResult['PROPERTY_BLOCK_LIKE_VALUE'])){
			if(!is_null($arResult['PROPERTY_BLOCK_PHOTO_VALUE'])){
				$rsSearchFile = CFile::GetByID($arResult['PROPERTY_BLOCK_PHOTO_VALUE']);
				$arSearchFile = $rsSearchFile->Fetch();
			}
			
			if(!is_null($arResult['PROPERTY_BLOCK_SEARCH_VALUE'])){
				$strSearch = $arResult['PROPERTY_BLOCK_SEARCH_VALUE'];
			}
			
			$rsBlockRecipe = CIBlockElement::GetList(Array(), Array("ID"=>$arResult['PROPERTY_BLOCK_LIKE_VALUE']), false, false, Array("ID", "NAME", "CREATED_BY", "PROPERTY_comment_count","PREVIEW_PICTURE"));			
			while($arBlockRecipe = $rsBlockRecipe->GetNext()){

				$rsUser = CUser::GetByID($arBlockRecipe['CREATED_BY']);
				$arUser = $rsUser->Fetch();
				if(strlen($arUser["NAME"]) > 0 && strlen($arUser["LAST_NAME"]) > 0){
			     	$arUser["FULLNAME"] = $arUser["NAME"]." ".$arUser["LAST_NAME"];
			 	}else{
			 		$arUser["FULLNAME"] = $arUser["LOGIN"];
			 	}
				$arBlockRecipe["USER"] = $arUser;
				
				$BlockRecipes[] = $arBlockRecipe;
				if(!is_null($arBlockRecipe['PREVIEW_PICTURE'])){
					$rsBlockFile = CFile::GetByID($arBlockRecipe['PREVIEW_PICTURE']);
					$arBlockFile[$arBlockRecipe["ID"]] = $rsBlockFile->Fetch();
				}
			}
			if(!empty($BlockRecipes)){	
				ob_start();?>
				<div id="other_recipes">
					<h3>Похожие рецепты</h3>
					<div class="items_block">
						<?foreach($BlockRecipes as $key=>$recipe):?>
							<div class="item recipe_list_item">
								<?if(isset($arBlockFile[$recipe["ID"]])){?>
									<div class="photo">
										<a href="/detail/<?=$recipe["ID"]?>/"><img width="150" alt="<?=$recipe["NAME"]?>" src="/upload/<?=$arBlockFile[$recipe["ID"]]['SUBDIR']?>/<?=$arBlockFile[$recipe["ID"]]['FILE_NAME']?>"></a>										
									</div>
								<?}?>
								<h5><a href="/detail/<?=$recipe["ID"]?>/"><?=$recipe["NAME"]?></a></h5>
								<p class="author">От: <?=$recipe["USER"]['FULLNAME']?></p>
							</div>
						<?endforeach;?>
					</div>
				</div>
				<?$strBlockLike = ob_get_contents();
				ob_end_clean();
			}else{
				$strBlockLike = '';
			}
		}
			
		$CACHE_MANAGER->RegisterTag("recipe#".IntVal($_REQUEST['r']));
		$CACHE_MANAGER->EndTagCache();
		
		
	}else{
		$obCache->AbortDataCache();
		LocalRedirect("/404.php");
	}
	$obCache->EndDataCache(
		array(
			"recipe" => $arResult,
			"owner" => $arAuthor,
			"units" => $arAllUnits,
			"stages" => $strStages,
			"like" => $strBlockLike,
			"ccount" => $mixCount
		)
	);
}else{
    LocalRedirect("/404.php");
}


?>
<div id="content">
<?if(!empty($arResult)){
	
	//Добавление в избранное
	if(isset($_REQUEST['f'])){
		if($USER->IsAuthorized())
		{
			if($_REQUEST['f'] == "y")
			{				
				$Favorite->add($arResult["ID"]);
				if(!$isOwner){					
					$CMark->updateUserRait($arResult["CREATED_BY"],$way = "up","r_favorite");
				}
				global $CACHE_MANAGER;
				$CACHE_MANAGER->ClearByTag("profile_".$USER->GetId()."_favorites");
				LocalRedirect($APPLICATION->GetCurPageParam("", array("r", "f","place")));
			}
			elseif($_REQUEST['f'] == "n")
			{
				$Favorite->delete($arResult["ID"]);				
				if(!$isOwner){					
					$CMark->updateUserRait($arResult["CREATED_BY"],$way = "low","r_favorite");
				}
				global $CACHE_MANAGER;
				$CACHE_MANAGER->ClearByTag("profile_".$USER->GetId()."_favorites");
				LocalRedirect($APPLICATION->GetCurPageParam("", array("r", "f","place")));
			}
		}
		else
		{
			LocalRedirect('/auth/?backurl=/detail/'.$arResult["ID"].'/?f='.$_REQUEST['f']);
		}
	}

	$bAllowEdit = ((!($USER->IsAdmin()) && (MakeTimeStamp($arResult["DATE_CREATE"]) <= (time() - 3600*24*3))) || $USER->IsAdmin());?>
	
	
	<style>
	@media print {
	body, #logo strong, div.bar div.date, div.bar div.date span {color:#000000;}
	#top_panel, #top_banner, #recipe_search, #iphone_link, #topbar, #text_space ul.recipe_menu, #text_space div.scales, #opinion_block, div.bar div.comments, div.bar div.favourite, div.bar div.share, div.other_recipes, #bottom, #bottom_nav, #banner_space, div.recipe div.image div.screen {display:none;}
	a {
	text-decoration:none;
	color:#000000;}
	div.bar {padding-bottom:70px;}
	#body, #content {width:700px;}
	#body div.padding {padding:0;}
	}
	</style>
	<?if(isset($_REQUEST["cant_delete"])):
		if(!$bAllowEdit){
			echo "<div class='b-error-message'>
				<div class='b-error-message__pointer'>
					<div class='b-error-message__pointer__div'></div>
				</div>
				Вам нельзя удалять этот рецепт, т.к. он был добавлен более, чем 3 дня назад.
			</div>
			<div class='i-clearfix'></div>";
		}
	endif;
	if(isset($_REQUEST["cant_edit"])):
		if(!$bAllowEdit){
			echo "<div class='b-error-message'>
				<div class='b-error-message__pointer'>
					<div class='b-error-message__pointer__div'></div>
				</div>
				Вам нельзя редактировать этот рецепт, т.к. он был добавлен более, чем 3 дня назад.
			</div>
			<div class='i-clearfix'></div>";
		}
	endif;?>
	
	<div id="text_space">
	
		<div class="b-recipe-menu">
			<? if($USER->IsAuthorized()){
				if($Favorite->status($arResult["ID"]))
				{
				?>
				<a href="<?=SITE_DIR?>detail/<?=$arResult["ID"]?>/?f=n" class="b-recipe-menu__item b-recipe-menu__button b-recipe-menu__button__type-favorite" title="Убрать из избранного">
					<span class="b-favorite-button i-remove-favorite"></span>
				</a>
				<?
				}
				else
				{
				?>
				<a href="<?=SITE_DIR?>detail/<?=$arResult["ID"]?>/?f=y" class="b-recipe-menu__item b-recipe-menu__button b-recipe-menu__button__type-favorite" title="Добавить в избранное">
					<span class="b-favorite-button i-add-favorite"></span>
				</a>
				<?
				}
			}
			else
			{?>
				<a href="<?=SITE_DIR?>detail/<?=$arResult["ID"]?>/?f=y" class="b-recipe-menu__item b-recipe-menu__button b-recipe-menu__button__type-favorite" title="Добавить в избранное">
					<span class="b-favorite-button i-add-favorite"></span>
				</a>
			<?}?>
			<a href="#" class="b-recipe-menu__item b-recipe-menu__button b-recipe-menu__button__type-print">
				<span class="b-print-button" title="Распечатать рецепт"></span>
			</a>
			
			<div class="b-recipe-menu__item b-recipe-menu__like b-fb-button"><fb:like send="false" layout="button_count" width="50" show_faces="false"></fb:like></div>
			<div class="b-recipe-menu__item b-recipe-menu__like b-vk-button">
				<div id="vk_like"></div>
				<script type="text/javascript">
				VK.Widgets.Like("vk_like", {type: "mini", height: 20});
				</script>
			</div>
		<div class="clear"></div>
	</div>
	
	<div class="recipe hrecipe" id="<?=$arResult["ID"]?>">
		<div class="title">
			<div class="body">			
			<div class="chain_path">
				<div class="author"><div class="author_photo"><div class="big_photo" style="display: none;"><div><img width="100" height="100" alt="<?=$arAuthor["FULLNAME"]?>" src="<?=$arAuthor["avatar"]?>"></div></div><img width="30" height="30" alt="<?=$arAuthor["FULLNAME"]?>" src="<?=$arAuthor["avatar"]?>"></div><a class="nickname" href="/profile/<?=$arAuthor["ID"]?>/" title="<?=$arAuthor["FULLNAME"]?>"><?if(strlen($arAuthor["FULLNAME"]) > 10):?><?=substr($arAuthor["FULLNAME"],0,10)?>...<?else:?><?=$arAuthor["FULLNAME"]?><?endif;?></a></div>
				предлагает приготовить:	<span class="tags"><a class="sub-category" href="/search/<?=$arKitchens[ $arResult['PROPERTY_KITCHEN_VALUE'] ]['NAME']?>/" rel="tag"><?=$arKitchens[ $arResult['PROPERTY_KITCHEN_VALUE'] ]['NAME']?></a>/<a href="/search/<?=$arDishType[ $arResult['PROPERTY_DISH_TYPE_VALUE'] ]['NAME']?>/" class="category" rel="tag"><?=$arDishType[ $arResult['PROPERTY_DISH_TYPE_VALUE'] ]['NAME']?></a><?if(intval($arResult["PROPERTY_MAIN_INGREDIENT_VALUE"]) > 0):?>/<a href="/search/<?=$arResult["MAIN_INGREDIENT"]["NAME"]?>/" class="category" rel="tag"><?=$arResult["MAIN_INGREDIENT"]["NAME"]?></a><?endif;?></span>
			</div>
			<?if($USER->isAdmin() || $USER->GetID() == $arResult['CREATED_BY']):?>
				<div class="admin_panel">
				<noindex>
				<a id="html_code" href="#">HTML-код</a>
				<?if($bAllowEdit):?><a title="Редактировать запись" href="<?=SITE_DIR?>recipe/edit/<?=$arResult["ID"]?>/" class="edit">Редактировать запись</a>
				<a title="Удалить запись" class="delete" href="<?=SITE_DIR?>recipe/delete/<?=$arResult["ID"]?>/">Удалить запись</a><?endif;?>
				</noindex>
				</div>
			<?endif;?>
			<h1 class="fn"><?=$arResult['NAME']?></h1>
			<?if(strlen($arResult["TAGS"]) > 0){echo "<!-- TAGS:".$arResult['TAGS']." -->";}?>
			<?if(intval($arResult["kkals"]) > 0 || intval($arResult["PROPERTY_PORTION_VALUE"]) > 0 || intval($arResult["PROPERTY_COOKING_TIME_VALUE"]) > 0):?>
			<?$cooking_time_hours = $arResult["PROPERTY_COOKING_TIME_VALUE"]/60;
			$cooking_time_minutes = $arResult["PROPERTY_COOKING_TIME_VALUE"]%60;?>
			<div class="recipe_info">
				<table>
					<tbody><tr>
						<td class="time"><?if(intval($arResult["PROPERTY_COOKING_TIME_VALUE"]) > 0):?><span>Время приготовления:</span> <?=(intval($cooking_time_hours) > 0 ? intval($cooking_time_hours)." ".$Factory->plural_form(intval($cooking_time_hours),array("час","часа","часов"))." " : "")?><?=(intval($cooking_time_minutes) > 0 ? intval($cooking_time_minutes)." мин" : "")?><?endif;?></td>
						<td class="yield"><?if(intval($arResult["PROPERTY_PORTION_VALUE"]) > 0):?><span>Порций:</span> <?=$arResult["PROPERTY_PORTION_VALUE"]?><?endif;?></td>
						<td class="nutrition"><?if(intval($arResult["kkals"]) > 0):?><span>Калорийность:</span> <?if(intval($arResult["PROPERTY_PORTION_VALUE"]) > 0){echo intval($arResult["kkals"]/intval($arResult["PROPERTY_PORTION_VALUE"]));}else{echo intval($arResult["kkals"]);}?> кКал на порцию<?endif;?></td>
					</tr>
				</tbody></table>
			</div>
			<?endif;?>
			<?if(IntVal($arResult['PREVIEW_PICTURE']) > 0):?>
				<div class="image"><div class="screen"><div style="width: <?=$arResult["MainFile"]['WIDTH']?>px; height: <?=$arResult["MainFile"]['HEIGHT']?>px;"></div></div><img class="final-photo" src="/upload/<?=$arResult["MainFile"]['SUBDIR']?>/<?=$arResult["MainFile"]['FILE_NAME']?>" alt="<?=(strlen($arResult["MainFile"]['DESCRIPTION']) > 0 ? $arResult["MainFile"]['DESCRIPTION'] :$arResult['NAME'])?>" title="<?=(strlen($arResult["MainFile"]['DESCRIPTION']) > 0 ? $arResult["MainFile"]['DESCRIPTION'] : $arResult['NAME'])?>" width="<?=$arResult["MainFile"]['WIDTH']?><?//=$arMainFile['WIDTH']?>" height="<?=$arResult["MainFile"]['HEIGHT']?><?//=$arMainFile['HEIGHT']?>"></div>
			<?endif;?>
			<?if(count($arAllUnits) > 0 || count($arResult['PROPERTY_TECHNICS_VALUE']) > 0){?>
			<div class="needed">
				<h2>Вам понадобится:</h2>
				<?if(!empty($arResult['PROPERTY_TECHNICS_VALUE'])):?>
					<div class="tools">
						<?foreach($arResult['PROPERTY_TECHNICS_VALUE'] as $technic):?>
							<span class="item">
								<?if(strlen($technic["PICTURE"]) > 0):?>
									<img width="100" height="100" alt="<?=$technic["NAME"]?>" src="<?=$technic["PICTURE"]?>">
								<?endif;?>
								<?if(strlen($technic["PROPERTY_LINK_VALUE"]) > 0):?>
									<span class="p"><a target="_blank" href="<?=$technic["PROPERTY_LINK_VALUE"]?>"><?=$technic["NAME"]?></a></span>
								<?else:?>
									<span class="p"><?=$technic["NAME"]?></span>
								<?endif;?>
							</span>
						<?endforeach;?>
					</div>
				<?endif;?>
				<div class="scales"><a title="Таблица мер" href="#"></a></div>
				<table>
					<?
					$bF = true;
					foreach($arAllUnits as $arItem){
						$intUnitCount = 0;
						foreach($arItem as $arUnit){
							ob_start(); eval("echo ".$arUnit['VALUE'].";"); $i = ob_get_contents(); ob_end_clean();
							$intUnitCount += FloatVal($i);
						}
						if($bF == true){ echo "<tr>";};
					?>
						<td class="ing_name <?if($bF == false){echo "border";}?>"><span class="ingredient"><?=$arItem[0]['NAME']?></span></td>
						<td class="ing_amount"><?=str_replace(Array("0.5", "0.25", "0.75"), Array("&frac12;","&frac14;","&frac34;"), $intUnitCount)?> <?=$arItem[0]['UNIT']?></td>
					<?
						if($bF == false){ echo "</tr>"; $bF = true;} else { $bF = false; };
					}
					if($bF == false){
						echo '<td class="ing_name border"></td><td class="ing_amount"></td></tr>';
					}
					?>
				</table>
			</div>
			<?} //if?>
			</div>

			<div class="clear"></div>
		</div>

		<?=$strStages?>
				
		<div class="date"><span class="published"><?=substr($arResult['DATE_ACTIVE_FROM'], 0, strlen($arResult['DATE_ACTIVE_FROM'])-9);?></span><span class="time"><?=substr($arResult['DATE_ACTIVE_FROM'], 11, 9);?></span></div>
			<div class="bar">
				<div class="padding">
					<div class="author">
						<div class="author_photo">
							<div class="big_photo">
								<div><img src="<?=$arAuthor["avatar"]?>" width="100" height="100" alt="<?=$arAuthor['FULLNAME']?>"></div>
							</div>
							<img src="<?=$arAuthor["avatar"]?>" width="30" height="30" alt="<?=$arAuthor['FULLNAME']?>">
						</div>
						<a class="nickname" href="/profile/<?=$arAuthor['ID']?>/" title="<?=$arAuthor['FULLNAME']?>"><?if(strlen($arAuthor['FULLNAME']) > 10):?><?=substr($arAuthor['FULLNAME'],0,10)?>...<?else:?><?=$arAuthor['FULLNAME']?><?endif;?></a>
					</div>
					<?if($USER->IsAuthorized()){?>
					<div class="comments"><a href="#add_opinion">Комментировать</a><span class="number">(<a href="#add_opinion"><?=IntVal($mixCount)?></a>)</span></div>
					<?}?>
				</div>
			</div>
		</div>
		<div class="b-recipe-menu b-recipe-menu__type-block-under">
			<? if($USER->IsAuthorized()){
			if($Favorite->status($arResult["ID"]))
			{
			?>
			<a href="" class="b-recipe-menu__item b-recipe-menu__button b-recipe-menu__button__type-favorite" title="Убрать из избранного">
				<span class="b-favorite-button i-remove-favorite"></span>
			</a>
			<?
			}
			else
			{
			?>
			<a href="" class="b-recipe-menu__item b-recipe-menu__button b-recipe-menu__button__type-favorite" title="Добавить в избранное">
				<span class="b-favorite-button i-add-favorite"></span>
			</a>
			<?
			}
		}
		else
		{
			?>
			<a href="<?=SITE_DIR?>detail/<?=$arResult["ID"]?>/?f=y" class="b-recipe-menu__item b-recipe-menu__button b-recipe-menu__button__type-favorite" title="Добавить в избранное">
				<span class="b-favorite-button i-add-favorite"></span>
			</a>
		<?
		}?>
			<a href="#" class="b-recipe-menu__item b-recipe-menu__button b-recipe-menu__button__type-print">
				<span class="b-print-button" title="Распечатать рецепт"></span>
			</a>
			<div class="i-clearfix"></div>
		</div>
		<div class="b-social-buttons">
			<div class="b-social-buttons__item b-vk-like">
				<div id="vk_like1"></div>
				<script type="text/javascript">
					VK.Widgets.Like("vk_like1", {type: "mini", height: 20});
				</script>
			</div>
			<div class="b-social-buttons__item b-twitter-like">
				<a href="https://twitter.com/share" class="twitter-share-button">Tweet</a>
				<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>				
			</div>
			<div class="b-social-buttons__item b-fb-like"><fb:like send="false" layout="button_count" width="50" show_faces="false"></fb:like></div>
			<div class="b-social-buttons__item b-surf-like">
				<a target="_blank" class="surfinbird__like_button" data-surf-config="{'layout': 'common', 'width': '100', 'height': '20'}" href="http://surfingbird.ru/share">Серф</a>
				<script type="text/javascript" charset="UTF-8" src="http://surfingbird.ru/share/share.min.js"></script>
			</div>
			<div class="b-social-buttons__item b-pin-like">
				<a target="_blank"  href="http://pinterest.com/pin/create/button/?url=foodclub.ru<?=$APPLICATION->GetCurPage()?>&media=http://foodclub.ru/upload/<?=$arResult["MainFile"]["SUBDIR"]?>/<?=$arResult["MainFile"]["FILE_NAME"]?>&description=<?=urlencode($arResult["~PREVIEW_TEXT"])?>" class="pin-it-button" count-layout="horizontal"><img border="0" src="//assets.pinterest.com/images/PinExt.png" title="Pin It" /></a>
			</div>
			<div class="b-social-buttons__item b-ya-share">
				<script type="text/javascript" src="//yandex.st/share/share.js" charset="utf-8"></script>
				<div class="yashare-auto-init" data-yashareL10n="ru" data-yashareType="icon" data-yashareQuickServices="yaru,vkontakte,facebook,twitter,odnoklassniki"></div> 
			</div>
			<div class="i-clearfix"></div>
		</div>
<?}?>
	
	<a name="comments"></a>
	<div id="opinion_block">
	<?
	$comment_cache_id = "comments_id".IntVal($_REQUEST['r']);
	$comment_cache_dir = "/recipes_comments_cache/id".IntVal($_REQUEST['r']);	
	if($obCache->InitCache($cache_time, $comment_cache_id, $comment_cache_dir)){
		$arComments = $obCache->GetVars();
	}elseif(CModule::IncludeModule("iblock") && $obCache->StartDataCache()){
		global $CACHE_MANAGER;
		$CACHE_MANAGER->StartTagCache($comment_cache_dir);
		require_once($_SERVER["DOCUMENT_ROOT"].'/classes/comment.class.php');
		CModule::IncludeModule("iblock");
		$obComment = CFClubComment::getInstance();
		$arComments = $obComment->getList($arResult["ID"]);
		if($arComments !== false){
			foreach($arComments as $arComment){
				$CACHE_MANAGER->RegisterTag("recipe_comments#".IntVal($_REQUEST['r'])."_comment#".$arComment["ID"]);
			}				
		}else{
			$arComments = array();
		}
		$CACHE_MANAGER->RegisterTag("recipe_comments#".IntVal($_REQUEST['r']));
		$CACHE_MANAGER->EndTagCache();
		$obCache->EndDataCache($arComments);
	}else{
		$arComments = array();
	}
	echo "<h2 class='h1'>Отзывы пользователей</h2>";
	foreach($arComments as $arComment){?>
		<a name="<?=$arComment['ID']?>"></a>
		<div class="opinion <?if($arComment['CREATED_BY'] == $USER->GetId())echo "mine";?>" id="<?=$arComment['ID']?>">
			<div class="icons">
				<div class="close_icon" title="Закрыть"></div>
				<div class="pointer"></div>
				<div class="photo">
					<div class="big_photo">
						<div><img src="<?=$arComment['USER']['SRC']?>" width="100" height="100" alt="<?=$arComment['USER']['FULLNAME']?>"></div>
					</div>
					<img src="<?=$arComment['USER']['SRC']?>" width="30" height="30" alt="<?=$arComment['USER']['FULLNAME']?>">
				</div>
				<div class="right">
					<?if($USER->IsAdmin() || $USER->GetID() == $arComment['CREATED_BY']){?><div class="delete" title="Удалить"></div>
					<div class="edit" title="Редактировать"></div><?};?>
				</div>
			</div>

			<div class="padding">
				<div class="opinion_author"><a href="/profile/<?=$arComment["USER"]["ID"]?>/"><?=$arComment["USER"]["FULLNAME"]?></a></div>
				<div class="text">
					<?=$arComment['PREVIEW_TEXT']?>
				</div>
				<form action="/comment.php" name="edit_comment" method="post">
					<input type="hidden" name="cId" value="<?=$arComment['ID']?>">
					<input type="hidden" name="rId" value="<?=$arResult["ID"]?>">
					<input type="hidden" name="a" value="e">
					<div class="textarea"><textarea name="opinion" cols="10" rows="5"><?=$arComment['~PREVIEW_TEXT']?></textarea></div>
					<div class="button">Сохранить</div>
				</form>
				<div class="properties">
					<div class="date"><?=$arComment['DATE_CREATE']?></div>
				</div>
			</div>
		</div>
	<?}
	if($USER->IsAuthorized()){?>
		<a name="add_opinion"></a>
		<div id="opinion_form" class="form_field">
			<form action="/comment.php" method="post" name="comment">
				<div class="form_field">
					<h4>Ваш отзыв <span>?</span></h4>
					<textarea name="text" cols="10" rows="10"></textarea>
					<input type="hidden" name="a" value="new">
					<input type="hidden" name="recipe" value="<?=$arResult["ID"]?>">
					<div class="button">Оставить отзыв</div>
				</div>
			</form>
		</div>
	<?} else {?>
		<div class="opinion foodclub">
			<div class="icons">
				<div class="pointer"></div>
			</div>
			<div class="padding">
				<div class="text">
					Для добавления отзыва Вам необходимо <a href="/auth/?backurl=<?=$APPLICATION->GetCurPage()?>#add_opinion">авторизоваться</a> или <a href="/registration/?backurl=<?=$APPLICATION->GetCurPage()?>#add_opinion">зарегистрироваться</a>. У пользователей ЖЖ, есть возможность авторизоваться на сайте используя ЖЖ-аккаунт.
				</div>
			</div>
		</div>
	<?}//if?>
	</div>
	
</div>
										
	<div id="banner_space">
		<?
		$rsRandomDoUKnow = CIBlockElement::GetList(array("rand"=>"asc"),array("IBLOCK_CODE"=>"do_u_know"),false,false,array("ID","PREVIEW_TEXT"));
		if($arRandomDoUKnow = $rsRandomDoUKnow->Fetch()){?>
		<div id="do-you-know-that" class="b-facts">
			<div class="b-facts__heading">Знаете ли вы что:</div>
			<div class="b-facts__content">
				<div class="b-facts__item" data-id="<?=$arRandomDoUKnow["ID"]?>">
					<?=$arRandomDoUKnow["PREVIEW_TEXT"]?>
				</div>
			</div>
			<div class="b-facts__more">
				<a href="#" class="b-facts__more__link">Еще</a>
			</div>
		</div>
		<?}?>
		<?if(strlen($strBanner) > 0){?>
			<div class="banner">
				<h5>Реклама</h5>
				<?=$strBanner?>
			</div>
		<?}?>
		<?if(strlen($strBookBanner) > 0){?>
			<div class="auxiliary_block book">
				<?=$strBookBanner?>
				<div class="clear"></div>
			</div>
		<?}?>
		<?=$strBlockLike?>
		<?if(strlen($strSecond_banner) > 0){?>
			<div class="banner">
				<h5>Реклама</h5>
				<?=$strSecond_banner?>
			</div>
		<?}?>
		<?require($_SERVER["DOCUMENT_ROOT"]."/facebook_box.html");?>
	</div>
	<div class="clear"></div>
</div>
<?//include(__DIR__."/recipe_print.html");
//$file = file_get_contents('/recipe_print.html', true);?>
<script type="text/html" id="print-recipe">
<!DOCTYPE HTML>
<html>
<head>
<title><%=title%></title>
<style>
@media print {
	.b-print-button, .screen {
		display: none;
	}
}

html {overflow-y: scroll;}
* {
	padding: 0;
	margin: 0;}
body {
	font-size: 10pt;
	width: 100%;
	height: 100%;
	background: #ffffff;
	color: #333333;
	font-family: Tahoma, Arial, Helvetica, sans-serif;}
img {
	border: 0;
	vertical-align: bottom;
}
table, table td {border-collapse: collapse;}
table td {vertical-align: top;}
p {margin-top: 10px;}
h1, h2, h3, h4, h5, h6 {
	font-weight: normal;
	font-family: Georgia, Times, serif;
}
h1 {
	font-size: 20pt;
	margin: 0 0 20px 0;}
h2 {
	font-size: 14pt;
	color: #999999;
	margin-bottom: 10px;
}
h3 {
	font-size: 14pt;
	font-family: Georgia, Times, serif;}
h4 {}
h5 {
	color: #999999;
	font-size: 10pt;
	font-family: Georgia, Times, serif;
	font-style: italic;}
h6 {}

.i-clearfix {
	clear: both;
	height: 0;
	overflow: hidden;
	width: 1px;}


#body {
	width: 602px;
	padding: 50px 0;
	margin: 0 auto;
}
#recipe {
	border-left: 1px solid #cccccc;
	border-right: 1px solid #cccccc;
	padding: 30px 50px;
}
#top_decor, #bottom_decor {
	height:4px;
	font-size: 0;
}

.b-button {
	display: inline-block;
	height: 43px;
	//height: 31px;
	background: url(/images/buttons.png) repeat-x 0 -1841px;
	color: #ffffff;
	font-size: 11pt;
	font-family: Georgia, "Times New Roman", Times, serif;
	color: #7d7c7c;
	text-shadow: 0 1px 0 #ffffff;
	cursor: pointer;
	text-align: center;
	padding: 12px 18px 0 18px;
	-moz-border-radius: 5px; /* Firefox */
	-webkit-border-radius: 5px; /* Safari, Chrome */
	-khtml-border-radius: 5px; /* KHTML */
	border-radius: 5px; /* CSS3 */
	box-sizing: border-box;
	-moz-box-sizing: border-box;
	-webkit-box-sizing: border-box;
	-khtml-box-sizing: border-box;
	border: 0;
	text-decoration: none;}
.b-button:hover {background-position:0 -1884px;}
.b-button:active {background-position:0 -1927px;}

.b-print-button {text-align:center;}

.b-print-head {
	margin: 30px 0 35px 0;
}
.b-print-head__logo {
	float: left;
	margin: 0 27px 0 0;
}
.b-logo {
	width: 89px;
	height: 63px;
}
.b-print-head__slogan {
	float: left;
	margin: 12px 0 0 0;
}
.b-slogan {
	font-family: Georgia, "Times New Roman", Times, serif;
	font-size: 9pt;
	font-style: italic;
	color: #666666;
}
.b-print-head__web {
	float: right;
	margin: 18px 0 0 0;
}
.b-web {
	font-family: Georgia, "Times New Roman", Times, serif;
	font-size: 18pt;
	font-style: italic;
}

.needed {
	margin: 0 0 30px 0;
	color: #666666;}
.needed table {
	width: 100%;
	border-top: 1px solid #ebebeb;
	margin-top: 8px;}
.needed table td {
	border-bottom: 1px solid #ebebeb;
	font-size: 8pt;
	vertical-align: middle;}
.needed table td.ing_name {
	padding: 5px 13px;
	width: 35%;}
.needed table td.ing_amount {
	padding: 5px 13px 5px 0;
	width: 15%;}
.needed table td.border {border-left:1px solid #ebebeb;}

.recipe_info {
	color: #666666;
	margin: 0 0 28px 0;}
.recipe_info table {
	width:100%;
	border-top:1px solid #ebebeb;}
.recipe_info td {
	border-bottom:1px solid #ebebeb;
	font-size:8pt;
	vertical-align:middle;}
.recipe_info td.time {
	padding:5px 13px;
	width:42%;}
.recipe_info td.yield {
	padding:5px 13px 5px 0;
	width:16%;}
.recipe_info td.nutrition {
	padding:5px 13px 5px 0;
	width:42%;
	text-align:right;}
.recipe_info span {color:#999999;}

.image {
	float: left;
	margin: 0 15px 0 0;
}
.description, .instruction {
	float: left;
	width: 280px;
}

.screen {position:relative;}
.screen div {
	position:absolute;
	top:0;
	left:0;
	background:url(/images/spacer.gif) no-repeat 0 0;}

.stage, .title {
	margin-bottom: 40px;
}
</style>
</head>

<body>
	<div id="body">
		<div id="top_decor"><img src="/images/print/top_decor.gif" width="602" height="4" alt=""></div>
			<div id="recipe">
				<% if(!(browser.opera || browser.msie)) { %>
				<div class="b-print-button"><a href="#" class="b-button i-print-button" onclick="window.print(); return false;"><span>Распечатать</span></a></div>
				<% } %>
				<div class="b-print-head">
					<div class="b-print-head__logo b-logo"><img src="/images/print/foodclub_logo.gif" width="89" height="63" alt="Foodclub.ru"></div>
					<div class="b-print-head__slogan b-slogan">Рецепты<br>с пошаговыми<br>фотографиями</div>
					<div class="b-print-head__web b-web">www.foodclub.ru</div>
					<div class="i-clearfix"></div>
				</div>
				<div class="title">
					<h1><%=h1%></h1>
					<div class="recipe_info"><%=recipeInfo%></div>
					<div class="needed">
						<h2>Для приготовления блюда вам понадобится:</h2>
						<table><%=needed%></table>
					</div>
					<div class="image"><%=titleImage%></div>
					<div class="description"><%=description%></div>
					<div class="i-clearfix"></div>
				</div>
				<div class="instructions">
					<% for(var i = 0; i < stages.length; i++) { %>
					<div class="stage"><%=stages[i]%></div>
					<% } %>
				</div>
				<% if(!(browser.opera || browser.msie)) { %>
				<div class="b-print-button"><a href="#" class="b-button i-print-button" onclick="window.print(); return false;"><span>Распечатать</span></a></div>
				<% } %>
			</div>
			<div id="bottom_decor"><img src="/images/print/bottom_decor.gif" width="602" height="4" alt=""></div>
		</div>
	</div>
</body>
</html>
</script>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
