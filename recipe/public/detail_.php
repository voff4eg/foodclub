<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
require($_SERVER["DOCUMENT_ROOT"]."/classes/favorite.class.php");
require($_SERVER["DOCUMENT_ROOT"]."/classes/factory.class.php");


if (CModule::IncludeModule("advertising")){
    $strBanner = CAdvBanner::Show("right_banner");
    $strBookBanner = CAdvBanner::Show("book_banner");
}

$obCache = new CPHPCache;
$intRecipeId = IntVal($_REQUEST['r']);

if( $intRecipeId > 0 )
{
    $arR = $DB->Query("SELECT `IBLOCK_ID` FROM  `b_iblock_element` WHERE  `ID` =".$intRecipeId." AND  `ACTIVE` =  'Y'")->GetNext();
    if( $arR == false )
    {
		//echo "<pre>"; print_r($_REQUEST); echo "</pre>";die;
        /*@define("ERROR_404", "Y");
        CHTTP::SetStatus("404 Not Found");
        include $_SERVER["DOCUMENT_ROOT"]."/404.php";die;*/
		LocalRedirect("/404.php");
    }
    else
    {
if(intval($arR['IBLOCK_ID']) == 5){


$Favorite = new CFavorite;

if(isset($_REQUEST['f']))
{
	if($USER->IsAuthorized())
	{
		if($_REQUEST['f'] == "y")
		{
			$Favorite->add($intRecipeId);
			$arUser = $USER->GetByID($USER->GetID())->Fetch();
			$UF_PROFILE_FULL = unserialize($arUser["UF_PROFILE_FULL"]);
			if(intval($_REQUEST['r']) > 0){
				if(strlen($UF_PROFILE_FULL[intval($_REQUEST['r'])]) <= 0){
					$UF_PROFILE_FULL[intval($_REQUEST['r'])] = "y";
					$profile_full = serialize($UF_PROFILE_FULL);
					$USER->Update( $USER->GetID(), array("UF_PROFILE_FULL"=>$profile_full));
					$Recipe = CIBlockElement::GetByID($intRecipeId)->Fetch();
					if($Recipe["CREATED_BY"] != $USER->GetID()){
						require_once($_SERVER['DOCUMENT_ROOT']."/classes/mark.class.php");
						$CMark = new CMark;
						$CMark->initIblock($Recipe["CREATED_BY"],false,true);
						$CMark->updateUserRait($Recipe["CREATED_BY"],$way = "up","r_favorite");
					}
					unset($Recipe);
				}
			}
		}
		elseif($_REQUEST['f'] == "n")
		{
			$Favorite->delete($intRecipeId);
			$arUser = $USER->GetByID($USER->GetID())->Fetch();
			$UF_PROFILE_FULL = unserialize($arUser["UF_PROFILE_FULL"]);
			if(intval($_REQUEST['r']) > 0){
				if(strlen($UF_PROFILE_FULL[intval($_REQUEST['r'])]) > 0){
					unset($UF_PROFILE_FULL[intval($_REQUEST['r'])]);
					$profile_full = serialize($UF_PROFILE_FULL);
					$USER->Update( $USER->GetID(), array("UF_PROFILE_FULL"=>$profile_full));
					$Recipe = CIBlockElement::GetByID($intRecipeId)->Fetch();
					if($Recipe["CREATED_BY"] != $USER->GetID()){
						require_once($_SERVER['DOCUMENT_ROOT']."/classes/mark.class.php");
						$CMark = new CMark;
						$CMark->initIblock($Recipe["CREATED_BY"],false,true);
						$CMark->updateUserRait($Recipe["CREATED_BY"],$way = "low","r_favorite");
					}
					unset($Recipe);
				}
			}
		}
	}
	else
	{
		LocalRedirect('/auth/?backurl=/detail/'.$intRecipeId.'/?f=y');
	}
}
CModule::IncludeModule("iblock");

if($obCache->InitCache((3*60*60), "recipe".$intRecipeId, "detail") && !$USER->IsAdmin()){
	$vars = $obCache->GetVars();
	$arRecipe = $vars["recipes"];

} else {

	$CFClub = CFClub::getInstance();
	$rsRecipe = CIBlockElement::GetList(Array(), Array("ID"=>$intRecipeId), false, false, Array("ID", "NAME", "DATE_ACTIVE_FROM", "CREATED_BY", "PREVIEW_PICTURE", "DETAIL_PICTURE", "PREVIEW_TEXT", "PROPERTY_kitchen", "PROPERTY_dish_type", "PROPERTY_recipt_steps", "PROPERTY_comment_count", "PROPERTY_title", "PROPERTY_keywords", "PROPERTY_description", "PROPERTY_block_photo", "PROPERTY_block_search", "PROPERTY_block_like",
	"PROPERTY_main_ingredient","PROPERTY_kcals","PROPERTY_portion","PROPERTY_cooking_time","PROPERTY_vegetarian"));
	$arRecipe = $rsRecipe->GetNext();
	$arSteps[] = $arRecipe['PROPERTY_RECIPT_STEPS_VALUE'];

	if(!is_null($arRecipe['PROPERTY_BLOCK_LIKE_VALUE'])) $arLike[] = $arRecipe['PROPERTY_BLOCK_LIKE_VALUE'];
	while($arItem = $rsRecipe->GetNext()){
		$arSteps[ $arItem['PROPERTY_RECIPT_STEPS_VALUE_ID'] ] = $arItem['PROPERTY_RECIPT_STEPS_VALUE'];
		if(!is_null($arItem['PROPERTY_BLOCK_LIKE_VALUE'])) $arLike[ $arItem['PROPERTY_BLOCK_LIKE_VALUE_ID'] ] = $arItem['PROPERTY_BLOCK_LIKE_VALUE'];
	}

	$arRecipe['PROPERTY_RECIPT_STEPS_VALUE'] = $arSteps;
	$arRecipe['PROPERTY_BLOCK_LIKE_VALUE'] = $arLike;
}

$isOwner = false;
if($USER->IsAuthorized()){
	if($arRecipe['CREATED_BY'] == $USER->GetID()){
		$isOwner = true;
	}
}

if(strlen($arRecipe['PROPERTY_TITLE_VALUE']) > 0){
	$APPLICATION->SetPageProperty("title", $arRecipe['PROPERTY_TITLE_VALUE']);
} else {
	$APPLICATION->SetPageProperty("title", $arRecipe['NAME']." &mdash; рецепт с пошаговыми фото. Foodclub.ru");
}

$APPLICATION->SetPageProperty("description", (strlen($arRecipe['PROPERTY_DESCRIPTION_VALUE'])>0?$arRecipe['PROPERTY_DESCRIPTION_VALUE']:"Рецепты блюд с фотографиями и пошаговыми инструкциями."));
$APPLICATION->SetPageProperty("keywords", (strlen($arRecipe['PROPERTY_KEYWORDS_VALUE'])>0?$arRecipe['PROPERTY_KEYWORDS_VALUE']:"фото рецепты, рецепты с фотографиями, фото блюд"));

//echo "<pre>"; print_r($arRecipe); echo "</pre>";
?>
<div id="content">
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
		<div id="text_space">

			<ul class="recipe_menu">
			<li class="ya">
				<script type="text/javascript" src="//yandex.st/share/share.js" charset="utf-8"></script>
				<div class="yashare-auto-init" data-yashareL10n="ru"data-yashareType="none" data-yashareQuickServices="yaru,vkontakte,facebook,twitter,odnoklassniki,moimir"></div>
			</li>
			<? if($USER->IsAuthorized()){
				if($Favorite->status($intRecipeId))
				{
				?>
				<li class="remove_favorite">
					<a title="Удалить из избранного" href="?f=n">Удалить из избранного</a>
				</li><?
				}
				else
				{
				?>
				<li class="add_favorite">
					<a title="Добавить в избранное" href="?f=y">Добавить в избранное</a>
				</li><?
				}
			}
			else
			{
				?>
				<li class="add_favorite">
					<a title="Добавить в избранное" href="?f=y">Добавить в избранное</a>
				</li><?
			}?>
				<!--<li class="i_like"><a href="">Мне нравится</a> <span>и еще 15 кулинарам</span></li>-->
			</ul>

<?
if($isOwner || $USER->IsAdmin() || $obCache->StartDataCache()):
	$intRecipeId = IntVal($_REQUEST['r']);


	function numberingStage($stageNumber) {
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

	$CFClub = CFClub::getInstance();
	$arKitchens = $CFClub->getKitchens();
	$arDishType = $CFClub->getDishType();

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

	$rsMainFile = CFile::GetByID($arRecipe['DETAIL_PICTURE']);
	$arMainFile = $rsMainFile->Fetch();

	$intCount = count($arRecipe['PROPERTY_RECIPT_STEPS_VALUE']);

	$rsStages = CIBlockElement::GetList(Array(), Array("ID"=>$arRecipe['PROPERTY_RECIPT_STEPS_VALUE']), false, false, Array("ID", "NAME", "PREVIEW_PICTURE", "PREVIEW_TEXT", "PROPERTY_ingredient", "PROPERTY_numer", "PROPERTY_parent"));
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
                    <?=$arRecipe['~PREVIEW_TEXT']?>
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

		$rsIng = CIBlockElement::GetList(Array(), Array("ID"=>$arStageProp['ingredient']/*,"!PROPERTY_kkal"=>false*/), false, false, Array("ID","NAME","PROPERTY_unit","PROPERTY_kkal"));
		while($arIngSrc = $rsIng->GetNext()){
			$arIng[ $arIngSrc['ID'] ] = $arIngSrc;
			//echo "<pre>"; print_r($arIng); echo "</pre>";die;
		}
		foreach($arStageProp['ingredient'] as $strKey => $strItem){
			if(!is_null($strItem)){
				$arResult[] = Array(
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
		$rsFile = CFile::GetByID($arStageFields['PREVIEW_PICTURE']);
		        $arFile = $rsFile->Fetch();

	?>
		<?if(!$bFirst){?><div class="border"></div><?} else {$bFirst = false;}?>
			<div class="stage">
				<div class="body">
				<?if($arRecipe["PROPERTY_VEGETARIAN_VALUE"] == "Y"):?><input type="hidden" name="vegetarian" value="y" /><?endif;?>
				<?if(count($arResult) > 0){?>
				<div class="needed">
					<h2>Ингредиенты <?=$strMKey+1;?>-го этапа:</h2>
					<table>
						<?foreach($arResult as $strKey => $arItem):?>
						<tr>
							<td class="ing_name"><?=$arItem['NAME']?></td>
							<td class="ing_amount"><?=str_replace(Array("1/2", "1/4", "3/4"), Array("&frac12;","&frac14;","&frac34;"), $arItem['VALUE'])?> <?=$arItem['UNIT']?></td>
						</tr>
						<?endforeach; unset($arResult);?>
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
	}
	?>
		</div>
	<?
		$strStages = ob_get_contents();
		ob_end_clean();

	?>

			<div class="recipe hrecipe" id="<?=$intRecipeId?>">
				<div class="title">
					<div class="body">
					<?$Usr = CUser::GetByID($arRecipe['CREATED_BY'])->Fetch();
					if(strlen($Usr["PERSONAL_PHOTO"]) > 0){
						$arFile = CFile::GetFileArray($Usr["PERSONAL_PHOTO"]);
					}else{
						$arFile = array("SRC" => "/images/avatar/avatar.jpg");
					}
					if(intval($arRecipe["PROPERTY_MAIN_INGREDIENT_VALUE"]) > 0){
						$Main_igredient = CIBlockElement::GetByID($arRecipe["PROPERTY_MAIN_INGREDIENT_VALUE"])->Fetch();
					}?>
					<div class="chain_path">
						<div class="author"><div class="author_photo"><div class="big_photo" style="display: none;"><div><img width="100" height="100" alt="<?=$Usr["LOGIN"]?>" src="<?=$arFile["SRC"]?>"></div></div><img width="30" height="30" alt="<?=$Usr["LOGIN"]?>" src="<?=$arFile["SRC"]?>"></div><a class="nickname" href="/profile/<?=$Usr["ID"]?>/"><?=$Usr["LOGIN"]?></a></div>
						предлагает приготовить:	<span class="tags"><a class="sub-category" href="/search/<?=$arKitchens[ $arRecipe['PROPERTY_KITCHEN_VALUE'] ]['NAME']?>/" rel="tag"><?=$arKitchens[ $arRecipe['PROPERTY_KITCHEN_VALUE'] ]['NAME']?></a>/<a href="/search/<?=$arDishType[ $arRecipe['PROPERTY_DISH_TYPE_VALUE'] ]['NAME']?>/" class="category" rel="tag"><?=$arDishType[ $arRecipe['PROPERTY_DISH_TYPE_VALUE'] ]['NAME']?></a><?if(intval($arRecipe["PROPERTY_MAIN_INGREDIENT_VALUE"]) > 0):?>/<a href="/search/<?=$Main_igredient["NAME"]?>/" class="category" rel="tag"><?=$Main_igredient["NAME"]?></a><?endif;?></span>
					</div>
					<h1 class="fn"><?=$arRecipe['NAME']?><?if($USER->isAdmin()):?><a id="html_code" href="#">HTML-код</a><?endif;?>

						<?if($USER->isAdmin() || $USER->GetID() == $arRecipe['CREATED_BY']):?><a class="edit" href="/recipe/edit/<?=$intRecipeId?>/"><img style="width: 7px; height: 12px;" src="/images/icons/edit.gif" alt="" title="Редактировать рецепт" height="12" width="7"></a>
						<a class="delete" href="javascript:if(confirm('Вы уверены, что хотите удалить рецепт?')) window.location='/recipe/delete/<?=$intRecipeId?>/'"><img width="9" height="9" title="Удалить рецепт" alt="" src="/images/icons/delete.gif"/></a><?endif;?>
					</h1>
					<?if(intval($kkals) > 0 || intval($arRecipe["PROPERTY_PORTION_VALUE"]) > 0 || intval($arRecipe["PROPERTY_COOKING_TIME_VALUE"]) > 0):?>
					<?$cooking_time_hours = $arRecipe["PROPERTY_COOKING_TIME_VALUE"]/60;
					$cooking_time_minutes = $arRecipe["PROPERTY_COOKING_TIME_VALUE"]%60;
					$Factory = new CFactory;?>
					<div class="recipe_info">
						<table>
							<tbody><tr>
								<td class="time"><?if(intval($arRecipe["PROPERTY_COOKING_TIME_VALUE"]) > 0):?><span>Время приготовления:</span> <?=(intval($cooking_time_hours) > 0 ? intval($cooking_time_hours)." ".$Factory->plural_form(intval($cooking_time_hours),array("час","часа","часов"))." " : "")?><?=(intval($cooking_time_minutes) > 0 ? intval($cooking_time_minutes)." мин" : "")?><?endif;?></td>
								<td class="yield"><?if(intval($arRecipe["PROPERTY_PORTION_VALUE"]) > 0):?><span>Порций:</span> <?=$arRecipe["PROPERTY_PORTION_VALUE"]?><?endif;?></td>
								<td class="nutrition"><?if(intval($kkals) > 0):?><span>Калорийность:</span> <?if(intval($arRecipe["PROPERTY_PORTION_VALUE"]) > 0){echo intval($kkals/intval($arRecipe["PROPERTY_PORTION_VALUE"]));}else{echo intval($kkals);}?> кКал на порцию<?endif;?></td>
							</tr>
						</tbody></table>
					</div>
					<?endif;?>
					<?if(count($arAllUnits) > 0){?>
					<div class="needed">
						<h2>Вам понадобится:</h2>
						<div class="scales">
						    Таблица мер
						  <img width="12" height="12" alt="" src="/images/icons/scales.gif"/>
						</div>
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
					<?if(IntVal($arRecipe['PREVIEW_PICTURE']) > 0):?>
						<div class="image"><div class="screen"><div style="width: <?=$arMainFile['WIDTH']?>px; height: <?=$arMainFile['HEIGHT']?>px;"></div></div><img class="final-photo" src="/upload/<?=$arMainFile['SUBDIR']?>/<?=$arMainFile['FILE_NAME']?>" alt="<?=(strlen($arMainFile['DESCRIPTION']) > 0 ? $arMainFile['DESCRIPTION'] : $arRecipe['NAME'])?>" title="<?=(strlen($arMainFile['DESCRIPTION']) > 0 ? $arMainFile['DESCRIPTION'] : $arRecipe['NAME'])?>" width="<?=$arMainFile['WIDTH']?>" height="<?=$arMainFile['HEIGHT']?>"></div>
					<?endif;?>
					</div>

					<div class="clear"></div>
				</div>

				<?=$strStages?>
<?
	$obCache->EndDataCache(Array(
								"recipes"  => $arRecipe,
							));
endif;?>
				<?



		        $rsUser = CUser::GetByID($arRecipe['CREATED_BY']);
                $arUser = $rsUser->Fetch();


        if(strpos($arUser['EXTERNAL_AUTH_ID'], "OPENID") !== false){

	        if(strpos($arUser['LOGIN'], "livejournal") !== false){
		        //$arUser['FULL_LOGIN'] = $arUser['LOGIN'];
		        //$arUser['LOGIN'] = substr($arUser['LOGIN'], 7, (strpos($arUser['LOGIN'], ".livejournal")-7));
		        $arUser['LOGIN_TYPE'] = "lj";
		        //$arUser['URL'] = $arUser['FULL_LOGIN'];
	        }

        } else {
	        $arUser['LOGIN_TYPE'] = "fc";
        }
        $arUser['URL'] = "/profile/".$arUser['ID']."/";

        if(intval($arUser['PERSONAL_PHOTO']) > 0){
            $rsAvatar = CFile::GetByID($arUser['PERSONAL_PHOTO']);
            $Avatar = $rsAvatar->Fetch();
            $Avatar['SRC'] = "/upload/".$Avatar['SUBDIR']."/".$Avatar['FILE_NAME'];
        } else {
            $Avatar['SRC'] = "/images/avatar/avatar.jpg";
        }

        $rsCount = CIBlockElement::GetProperty(5, $intRecipeId, "sort", "asc", Array("CODE"=>"comment_count"));
        $arCount = $rsCount->Fetch();
        $mixCount = IntVal($arCount["VALUE"]);
				?>
				<div class="date"><span class="published"><?=substr($arRecipe['DATE_ACTIVE_FROM'], 0, strlen($arRecipe['DATE_ACTIVE_FROM'])-9);?></span><span class="time"><?=substr($arRecipe['DATE_ACTIVE_FROM'], 11, 9);?></span></div>
				<div class="bar">
				    <div class="padding">
				        <div class="author">
					        <div class="author_photo">
                                <div class="big_photo">
                                    <div><img src="<?=$Avatar['SRC']?>" width="100" height="100" alt="<?=$arUser['LOGIN']?>"></div>
                                </div>
                                <img src="<?=$Avatar['SRC']?>" width="30" height="30" alt="<?=$arUser['LOGIN']?>">
                            </div>
                            <a class="nickname" href="/profile/<?=$arUser['ID']?>/"><?=$arUser['LOGIN']?></a>
                        </div>
					    <?if($USER->IsAuthorized()){?>
						<div class="comments"><a href="#add_opinion">Комментировать</a><span class="number">(<a href="#add_opinion"><?=IntVal($mixCount)?></a>)</span></div>
						<?}?>
					</div>
				</div>
			</div>
			<ul class="recipe_menu">
			<li class="ya">
				<script type="text/javascript" src="//yandex.st/share/share.js" charset="utf-8"></script>
				<div class="yashare-auto-init" data-yashareL10n="ru" data-yashareType="none" data-yashareQuickServices="yaru,vkontakte,facebook,twitter,odnoklassniki,moimir"></div>
			</li>
			<? if($USER->IsAuthorized()){
				if($Favorite->status($intRecipeId))
				{
				?>
				<li class="remove_favorite">
					<a title="Удалить из избранного" href="?f=n">Удалить из избранного</a>
				</li><?
				}
				else
				{
				?>
				<li class="add_favorite">
					<a title="Добавить в избранное" href="?f=y">Добавить в избранное</a>
				</li><?
				}
			}
			else
			{
				?>
				<li class="add_favorite">
					<a title="Добавить в избранное" href="?f=y">Добавить в избранное</a>
				</li><?
			}?>
				<!--<li class="i_like"><a href="">Мне нравится</a> <span>и еще 15 кулинарам</span></li>-->
			</ul>
			<a name="comments"></a>
			<div id="opinion_block">

			<?
			//$obCache = new CPageCache;
			//$obCache->Clean("cRecipe".$arFields['ID'], "detail");

			//if($obCache->StartDataCache(30*60*60, "cRecipe".$arFields['ID'], "detail") && !$USER->IsAdmin()){
				require($_SERVER["DOCUMENT_ROOT"].'/classes/comment.class.php');
				CModule::IncludeModule("iblock");
				$obComment = CFClubComment::getInstance();
				if($arComments = $obComment->getList($intRecipeId)){

					echo "<h2 class='h1'>Отзывы пользователей</h2>";
					foreach($arComments as $arComment){

					    if(intval($arComment['USER']['PERSONAL_PHOTO']) > 0){
						    $rsAvatar = CFile::GetByID($arComment['USER']['PERSONAL_PHOTO']);
						    $arAvatar = $rsAvatar->Fetch();
						    $arAvatar['SRC'] = "/upload/".$arAvatar['SUBDIR']."/".$arAvatar['FILE_NAME'];
					    } else {
						    $arAvatar['SRC'] = "/images/avatar/avatar.jpg";
					    }
					?>
						<a name="<?=$arComment['ID']?>"></a>
						<div class="opinion <?if($arComment['CREATED_BY'] == $USER->GetId())echo "mine";?>" id="<?=$arComment['ID']?>">
    						<div class="icons">
							    <div class="close_icon" title="Закрыть"></div>
							    <div class="pointer"></div>
                                <div class="photo">
                                    <div class="big_photo">
                                        <div><img src="<?=$arAvatar['SRC']?>" width="100" height="100" alt="<?=$arComments['USER']['LOGIN']?>"></div>
                                    </div>
                                    <img src="<?=$arAvatar['SRC']?>" width="30" height="30" alt="<?=$arComments['USER']['LOGIN']?>">
                                </div>
							    <div class="right">
								    <?if($USER->IsAdmin() || $USER->GetID() == $arComment['CREATED_BY']){?><div class="delete" title="Удалить"></div><?};?>
								    <?if($USER->IsAdmin() || $USER->GetID() == $arComment['CREATED_BY']){?><div class="edit" title="Редактировать"></div><?};?>
							    </div>
							</div>

							<div class="padding">
							    <div class="opinion_author"><a href="/profile/<?=$arComment["USER"]["ID"]?>/"><?=$arComment["USER"]["LOGIN"]?></a></div>
								<div class="text">
									<?=$arComment['PREVIEW_TEXT']?>
								</div>
								<form action="/comment.php" name="edit_comment" method="post">
									<input type="hidden" name="cId" value="<?=$arComment['ID']?>">
									<input type="hidden" name="rId" value="<?=$intRecipeId?>">
									<input type="hidden" name="a" value="e">
									<div class="textarea"><textarea name="opinion" cols="10" rows="5"><?=$arComment['~PREVIEW_TEXT']?></textarea></div>
									<div class="button">Сохранить</div>
								</form>
								<div class="properties">
								    <div class="date"><?=$arComment['DATE_CREATE']?></div>
								</div>
							</div>
						</div><?
					}//foreach
				}//if

			//}
			?>

			<?if($USER->IsAuthorized()){?>
				<a name="add_opinion"></a>
				<div id="opinion_form" class="form_field">
					<form action="/comment.php" method="post" name="comment">
						<div class="form_field">
							<h4>Ваш отзыв <span>?</span></h4>
							<div class="textarea"><textarea name="text" cols="10" rows="10"></textarea></div>
							<input type="hidden" name="a" value="new">
							<input type="hidden" name="recipe" value="<?=$intRecipeId?>">
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
							Для добавления отзыва Вам необходимо <a href="/auth/?backurl=<?=$APPLICATION->GetCurPage()?>">авторизоваться</a> или <a href="/registration/?backurl=<?=$APPLICATION->GetCurPage()?>">зарегистрироваться</a>. У пользователей ЖЖ, есть возможность авторизоваться на сайте используя ЖЖ-аккаунт.
						</div>
					</div>
				</div>
			<?}//if?>

			</div>


		</div>
<?
$obPostCache = new CPHPCache;
if($obPostCache->InitCache((3*60*60), "recipe_last_posts", "detail") && !$USER->IsAdmin())
{
	$p = $obPostCache->GetVars();

    $SocNetGroup = $p["SocNetGroup"];
    $SocNetGroupName = $p["SocNetGroupName"];
    $arBlogUser = $p["arBlogUser"];
    $arPosts = $p["arPosts"];

}
else
{
    CModule::IncludeModule("blog");
	CModule::IncludeModule("socialnetwork");
    $rsPosts = CBlogPost::GetList(
        Array("DATE_PUBLISH" => "DESC", "NAME" => "ASC"),
        Array("PUBLISH_STATUS" => BLOG_PUBLISH_STATUS_PUBLISH),
        false,
        Array("nTopCount"=>10)
    );
    while ($arPost = $rsPosts->GetNext()) {
        $arPosts[ $arPost['ID'] ] = $arPost;
        $arUsers[] = $arPost['AUTHOR_ID'];
        $arBlogId[] = $arPost['BLOG_ID'];
    }

    //Получение списка пользователей
    $Users = array_unique($arUsers);
    foreach($Users as $Item)
    {
        if(!$strUsers)
        {
            $strUsers = $Item;
        }
        else
        {
            $strUsers .= " | ".$Item;
        }
    }
    $rsUsers = CUser::GetList(($by="id"), ($order="desc"), array("ID"=>$strUsers));
    while( $User = $rsUsers->GetNext() )
    {
        $arBlogUser[ $User['ID'] ] = $User;
    }

    $Blogs = array_unique($arBlogId);
    sort($Blogs);
    $rsBlogs = CBlog::GetList(
	    Array(),
	    Array(
            "ID"=>$Blogs
	    ),
	    false,
	    array("nTopCount"=>100)
    );

    while($arBlog = $rsBlogs->Fetch())
    {
        $SocNetGroup[ $arBlog['ID'] ] = $arBlog['SOCNET_GROUP_ID'];
        $SNGroup[] = $arBlog['SOCNET_GROUP_ID'];
    }
    $SNGroup = array_unique($SNGroup);

    $rsGroup = CSocNetGroup::GetList(Array(), array("ID"=>$SNGroup), false, false);
    while ($arGroup = $rsGroup->GetNext()) {
        $SocNetGroupName[ $arGroup['ID'] ] = $arGroup['NAME'];
    }
}


?>
		<div id="banner_space">
			<?if(strlen($strBanner) > 0){?><div class="banner"><?=$strBanner?></div><?}?>
			<?if(strlen($strBookBanner) > 0){?><div class="auxiliary_block book"><?=$strBookBanner?><div class="clear"></div></div><?}?>

		    <?if($USER->IsAdmin() || $obPostCache->StartDataCache()):?>
			<div class="auxiliary_block last_topics">
			    <h3>Последние записи в клубах</h3>
            <?foreach ($arPosts as $arItem) {
                $BlogID = $arItem['BLOG_ID'];
                $SocNetGroupID = $SocNetGroup[ $BlogID ];
                $SocNetName = $SocNetGroupName[ $SocNetGroupID ];
                $UserName = $arBlogUser[ $arItem['AUTHOR_ID'] ]['LOGIN'];
            ?>
                <div class="item">
                    <div class="heading"><a href="/blogs/group/<?=$SocNetGroupID?>/blog/<?=$arItem['ID']?>/"><?=$arItem['TITLE']?></a></div>
                    <div class="info"><span class="author"><?=$UserName?></span><span class="date"><?=$arItem['DATE_CREATE']?></span></div>
                    <div class="club">В клубе <a href="/blogs/group/<?=$SocNetGroupID?>/blog/"><?=$SocNetName?></a></div>
                </div>
            <?}?>
			</div>
	        <?
        	$obPostCache->EndDataCache(Array(
							"SocNetGroup"  => $SocNetGroup,
							"SocNetGroupName"  => $SocNetGroupName,
							"arBlogUser"  => $arBlogUser,
							"arPosts"  => $arPosts,
						));
	        endif;?>
			<?require($_SERVER["DOCUMENT_ROOT"]."/facebook_box.html");?>
		</div>
		<div class="clear"></div>


<?
if(!is_null($arRecipe['PROPERTY_BLOCK_LIKE_VALUE'])){
	
	if(!is_null($arRecipe['PROPERTY_BLOCK_PHOTO_VALUE'])){
		$rsSearchFile = CFile::GetByID($arRecipe['PROPERTY_BLOCK_PHOTO_VALUE']);
		$arSearchFile = $rsSearchFile->Fetch();
	}
	
	if(!is_null($arRecipe['PROPERTY_BLOCK_SEARCH_VALUE'])){
		$strSearch = $arRecipe['PROPERTY_BLOCK_SEARCH_VALUE'];
	}

	//$intCell = 0;
	$rsBlockRecipe = CIBlockElement::GetList(Array(), Array("ID"=>$arRecipe['PROPERTY_BLOCK_LIKE_VALUE']), false, false, Array("ID", "NAME", "CREATED_BY", "PROPERTY_comment_count","PREVIEW_PICTURE"));
	//$strBlockHtml = "";
	while($arBlockRecipe = $rsBlockRecipe->GetNext()){
		//if($intCell === 0) $strBlockHTML .= "<tr>";
		//$intCell++;

		$rsUser = CUser::GetByID($arBlockRecipe['CREATED_BY']);
		$arUser = $rsUser->Fetch();
		$arBlockRecipe["USER"] = $arUser;
		
		$BlockRecipes[] = $arBlockRecipe;
		//$strBlockHTML .= '<td><a href="/detail/'.$arBlockRecipe['ID'].'/">'.$arBlockRecipe['NAME'].'</a>&nbsp;<span class="comments">(<a href="/detail/'.$arBlockRecipe['ID'].'/#add_opinion">'.intval($arBlockRecipe['PROPERTY_COMMENT_COUNT_VALUE']).'</a>)</span><span class="author">От: '.$arUser['LOGIN'].'</span></td>';

		//if($intCell === 4){$strBlockHTML .= "</tr>"; $intCell = 0;}
		
		if(!is_null($arBlockRecipe['PREVIEW_PICTURE'])){
			$rsBlockFile = CFile::GetByID($arBlockRecipe['PREVIEW_PICTURE']);
			$arBlockFile[$arBlockRecipe["ID"]] = $rsBlockFile->Fetch();
		}
	}


	/*if($intCell != 0){
		for($j = 4 - $intCell; $j < 4; $j++){
			$strBlockHTML .= '<td></td>';
		}
		$strBlockHTML .= '</tr>';
	}*/

?>
		<div class="other_recipes pages_recipes">
			<table>
			<?foreach($BlockRecipes as $key=>$recipe):?>
			<?if($key%4 == 0):?><tr><?endif;?>
					<td>
					<div class="item">
					<?if(isset($arBlockFile[$recipe["ID"]])){?>
					<div class="photo">
					<div class="big_photo" style="display: none;"><div><table class="frame"><tbody><tr><td class="tl"><img width="11" height="11" alt="" src="/images/spacer.gif"></td><td class="top"><img width="1" height="11" alt="" src="/images/spacer.gif"></td><td class="tr"><img width="14" height="11" alt="" src="/images/spacer.gif"></td></tr><tr><td class="left"><img width="11" height="1" alt="" src="/images/spacer.gif"></td>
					<td class="middle"><a href="/detail/<?=$recipe["ID"]?>/"><img width="<?=$arBlockFile[$recipe["ID"]]['WIDTH']?>" height="<?=$arBlockFile[$recipe["ID"]]['HEIGHT']?>" alt="" src="/upload/<?=$arBlockFile[$recipe["ID"]]['SUBDIR']?>/<?=$arBlockFile[$recipe["ID"]]['FILE_NAME']?>"></a></td>
					<td class="right"><img width="14" height="1" alt="" src="/images/spacer.gif"></td></tr><tr><td class="bl"><img width="11" height="14" alt="" src="/images/spacer.gif"></td><td class="bottom"><img width="1" height="14" alt="" src="/images/spacer.gif"></td><td class="br"><img width="14" height="14" alt="" src="/images/spacer.gif"></td></tr></tbody></table></div></div>
					<img src="/upload/<?=$arBlockFile[$recipe["ID"]]['SUBDIR']?>/<?=$arBlockFile[$recipe["ID"]]['FILE_NAME']?>" alt="<?=(strlen($arBlockFile[$recipe["ID"]]['DESCRIPTION']) > 0 ? $arBlockFile[$recipe["ID"]]['DESCRIPTION'] : $arBlockFile[$recipe["ID"]]['NAME'])?>" title="<?=(strlen($arBlockFile[$recipe["ID"]]['DESCRIPTION']) > 0 ? $arBlockFile[$recipe["ID"]]['DESCRIPTION'] : $arBlockFile[$recipe["ID"]]['NAME'])?>" width="50" height="30"><?}?>
					</div>
					<div class="link"><a href="/detail/<?=$recipe["ID"]?>/"><?=$recipe["NAME"]?></a>
					<span class="comments">(<a href="/detail/<?=$recipe["ID"]?>/#add_opinion"><?=intval($recipe["PROPERTY_COMMENT_COUNT_VALUE"])?></a>)</span>
					<span class="author">От: <?=$recipe["USER"]['LOGIN']?></span>
					</div>
					<div class="clear"></div>
					</div>
					</td>
			<?if(($key+1)%4 == 0):?></tr><?endif;?>	
			<?endforeach;?>
			</table>
		</div>
<?}?>
		</div>
<?
    }
	}
}
else
{
	//echo "<pre>"; print_r($_REQUEST); echo "</pre>";die;
    @define("ERROR_404", "Y");
    CHTTP::SetStatus("404 Not Found");
    die;
}
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>

