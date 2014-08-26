<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

CModule::IncludeModule("iblock");

if( $USER->IsAdmin() || $USER->IsAuthorized() ){
	
function numberingStage($stageNumber, $bOneCount = false) {
	if($bOneCount === true)
	{
		$numberingArray1 = Array("Первый", "Второй", "Третий", "Четвертый", "Пятый", "Шестой", "Седьмой", "Восьмой", "Девятый");
		$numberingArray2 = Array("Одиннадцатый", "Двенадцатый", "Тринадцатый", "Четырнадцатый", "Пятнадцатый", "Шеснадцатый", "Семнадцатый", "Восемнадцатый", "Девятьнадцатый");
		$numberingArray3 = Array("Десятый", "Двадцатый", "Тридцатый", "Сороковой", "Пятидесятый", "Шестидесятый", "Семидесятый", "Восьмидесятый", "Девяностый");
		$numberingArray4 = Array("", "двадцать", "тридцать", "сорок", "пятьдесят", "шестьдесят", "семьдесят", "восемьдесят", "девяносто");	
	} 
	else
	{
		$numberingArray1 = Array("первого", "второго", "третьего", "четвёртого", "пятого", "шестого", "седьмого", "восьмого", "девятого");
		$numberingArray2 = Array("одиннадцатого", "двенадцатого", "тринадцатого", "четырнадцатого", "пятнадцатого", "шестнадцатого", "семнадцатого", "восемнадцатого", "девятнадцатого");
		$numberingArray3 = Array("десятого", "двадцатого", "тридцатого", "сорокового", "пятидесятого", "шестидесятого", "семидесятого", "восьмидесятого", "девяностого");
		$numberingArray4 = Array("", "двадцать", "тридцать", "сорок", "пятьдесят", "шестьдесят", "семьдесят", "восемьдесят", "девяносто");	
	}
	
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

$APPLICATION->SetPageProperty("title", "Foodclub");
$APPLICATION->SetTitle("Foodclub");

$CFClub = CFClub::getInstance();

/*
 * Данные по рецепту
 */
$intRecipeId = IntVal($_REQUEST['r']);
$rsRecipe = CIBlockElement::GetById($intRecipeId);
if($obRecipe = $rsRecipe->GetNextElement()){
	$arProp = $obRecipe->GetProperties();
	$arFields = $obRecipe->GetFields();
	
	
	if($arFields['CREATED_BY'] == $USER->GetID() || $USER->IsAdmin()){
	
	/*
	 * Удаление фотографии, если она относится к рецепту
	 */
	if(isset($_REQUEST['id']) && IntVal($_REQUEST['id']) == IntVal($arFields['PREVIEW_PICTURE'])){
		foreach($arProp as $Key=>$Value){
			$P[ $Key ] = $Value['VALUE'];
		}
		
		$arPreIMAGE = array(
			"name" => false,
			"type" => false,
			"tmp_name" => false,
			"error" => 4,
			"size" => 0,
		);
		
		$arPreIMAGE["del"] = "Y";
		$arPreIMAGE["old_file"] 	= CFile::MakeFileArray(CFile::GetPath(IntVal($_REQUEST['id'])));
		$arPreIMAGE["MODULE_ID"] 	= "iblock";
		
		$arLoadProductArray = Array(
			"MODIFIED_BY"     => $USER->GetID(),
			"IBLOCK_SECTION"  => false,
			"IBLOCK_ID"       => 5,
			"PROPERTY_VALUES" => $P,
			"NAME"            => $arFields['NAME'],
			"ACTIVE"          => "Y",
			"PREVIEW_TEXT"    => $arFields['PREVIEW_TEXT'],
			"PREVIEW_PICTURE" => $arPreIMAGE,
		);
		
		$elStep   = new CIBlockElement;
		$elStep->Update($intRecipeId, $arLoadProductArray);
		
		unset($arFields['PREVIEW_PICTURE']);
	}
	
	$arKitchens = $CFClub->getKitchens(true);
	
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
		
		if($arProp['kitchen']['VALUE'] == $arItem['ID']){
			//$arItem['DISH'][0] = $arItem['DISH'][ $arProp['dish_type']['VALUE'] ];
			$Selected = $arItem['DISH'][ $arProp['dish_type']['VALUE'] ];
			
			unset($arItem['DISH'][ $arProp['dish_type']['VALUE'] ]);
			//ksort($arItem['DISH']); reset($arItem['DISH']);
		}

		/*
		 * Сортировка типов блюд
		 */
		$arDumpDish = Array(); 
		foreach ($arItem['DISH'] as $arItem){
			$arDumpDish[ $arItem['NAME'] ] = $arItem;
		}
		ksort($arDumpDish);
		reset($arDumpDish);
		
		$arItem['DISH'][0] = $Selected;
		foreach($arDumpDish as $Item)
		{
			$arItem['DISH'][ $Item['ID'] ] = $Item;
		}
		
		
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
		//echo "<pre>"; print_r($arUnit); echo "</pre>";
		
		if (!isset($strId)) {$strId = '"'.$arUnit['ID'].'"';} else {$strId .= ', "'.$arUnit['ID'].'"';}
		if (!isset($strName)) {$strName = '"'.$arUnit['NAME'].'"';} else {$strName .= ', "'.$arUnit['NAME'].'"';}
		$strDumpId = ''; $strDumpName = ''; $strDumpUnit = '';
		$strTemp .= "ingredientArray[2][$intNum] = new Array();";
		foreach($arUnit['UNITS'] as $intKey => $arItem)
		{
			if (strlen($strDumpId) == 0) {$strDumpId = '"'.$arItem['ID'].'"';} else {$strDumpId .= ', "'.$arItem['ID'].'"';}
			if (strlen($strDumpName) == 0) {$strDumpName = '"'.$arItem['NAME'].'"';} else {$strDumpName .= ', "'.$arItem['NAME'].'"';}
			if (strlen($strDumpUnit) == 0) {$strDumpUnit = '"'.$arItem['UNIT'].'"';} else {$strDumpUnit .= ', "'.$arItem['UNIT'].'"';}
			$arSortArray[ $arItem['ID'] ][0] = $intNum;
			$arSortArray[ $arItem['ID'] ][1] = $intKey;
		}
		$strTemp .= "ingredientArray[2][".$intNum."][0] = new Array($strDumpId);";
		$strTemp .= "ingredientArray[2][".$intNum."][1] = new Array($strDumpName);";
		$strTemp .= "ingredientArray[2][".$intNum."][2] = new Array($strDumpUnit);";
		
		$intNum++;
	}
	$strUnitHtml .= "ingredientArray[0] = new Array($strId);	ingredientArray[1] = new Array($strName); ingredientArray[2] = new Array();".$strTemp;
	
	if(IntVal($arFields['PREVIEW_PICTURE']) > 0){
		$rsMainFile = CFile::GetByID($arFields['PREVIEW_PICTURE']);
		$arMainFile = $rsMainFile->Fetch();
	}
	
	?>
	<div id="content">
	<form name="dish" action="/recipe/edit/post/" method="post" enctype="multipart/form-data">
	<input type="hidden" name="active_from" value="<?=$arFields['ACTIVE_FROM']?>">
	<input type="hidden" name='recipe_id' value="<?=$intRecipeId?>">
	<input type="hidden" name='comment_count' value="<?=IntVal($arProp['comment_count']['VALUE'])?>">
			<div class="body">
				<div id="dish_description">
					<h2>Общее описание</h2>
					<div class="dish_properties">
						<div class="form_field">
							<h5>Название блюда<span class="no_text">?</span></h5>
							<input type="text" name="name" value="<?=$arFields['NAME']?>" class="text">
						</div>
						<div class="form_field">
							<h5>Описание блюда<span class="no_text">?</span></h5>
							<textarea name="dish_description" cols="" rows=""><?=$arFields['PREVIEW_TEXT']?></textarea>
						</div>
						<div class="form_field">
							<h5>Главное фото (600х400 px)<span class="no_text">?</span></h5>
							<div class="input_file"><input type="file" name="general_photo" class="text"><input type="hidden" name="main_photo_id" value="<?=$arMainFile['ID']?>"></div>
							<?if(IntVal($arFields['PREVIEW_PICTURE']) > 0):?>
								<div class="file_name"><div class="img_icon"></div><a href="/upload/<?=$arMainFile['SUBDIR']?>/<?=$arMainFile['FILE_NAME']?>" target="_blank"><?=$arMainFile['FILE_NAME']?></a><span class="file_size">(<?=number_format(($arMainFile['FILE_SIZE']/1024), "","",2)?> кб)</span><img src="/images/spacer.gif" width="7" height="7" alt="Удалить изображение" class="delete_icon fir" onClick="deleteStageImage(this, '<?=$arMainFile['ID']?>');"></div>
							<?endif;?>
						</div>
					</div>
					<div class="dish_parents">
						<div class="form_field">
							<script language="javascript">
								<!--
									var cookingArray = new Array();
									cookingArray[0] = new Array(<?=$strJavaOne?>);
									cookingArray[1] = new Array();
									
									<?=$strJavaTwo?>
								//-->
							</script>
							<h5>Кухня</h5>
							<select name="cooking" onChange="chooseDishType(this);">
								<?=$strHtml?>
							</select>
						</div>
						<div class="form_field">
							<h5>Тип блюда</h5>
							<select name="dish_type">
							</select>
							<script language="javascript">
								<!--
									chooseDishType();
								//-->
							</script>
						</div>
					</div>
					<div class="clear"></div>
				</div>
			</div>
			
			<div id="dish_stages">
				<div class="body">
					<script language="javascript">
						<!--
							var stage_number = 0;
							var ingredientArray = new Array();
							<?=$strUnitHtml?>
							var stagesIngredientsArray = new Array();
						//-->
					</script>
					<?
					
					foreach($arProp['recipt_steps']['VALUE'] as $strMKey=>$strStep):
					
	
						$rsStage = CIBlockElement::GetById($strStep);
						$obStage = $rsStage->GetNextElement();
						
						$arStageProp = $obStage->GetProperties(); $arStageFields = $obStage->GetFields();
						
						$rsIng = CIBlockElement::GetList(Array(), Array("ID"=>$arStageProp['ingredient']['VALUE']), false, false, Array("ID","NAME","PROPERTY_unit"));
						while($arIngSrc = $rsIng->GetNext()){
							$arIng[ $arIngSrc['ID'] ] = $arIngSrc; 
						}
						
						foreach($arStageProp['ingredient']['VALUE'] as $strKey => $strItem){
							$arResult[] = Array(
								"ID"    => $strItem,
								"NAME"  => $arIng[ $strItem ]['NAME'],
								"UNIT"  => $arIng[ $strItem ]['PROPERTY_UNIT_VALUE'],
								"VALUE" => $arStageProp['numer']['VALUE'][ $strKey ],
							);
						}
						
						$rsFile = CFile::GetByID($arStageFields['PREVIEW_PICTURE']);
						$arFile = $rsFile->Fetch();
						
						if(isset($_REQUEST['id'])){
							
							if(IntVal($_REQUEST['id']) > 0 && IntVal($arStageFields['PREVIEW_PICTURE']) == IntVal($_REQUEST['id'])){
								
								foreach($arStageProp as $Key=>$Value){
									$Prop[ $Key ] = $Value['VALUE'];
								}
								
								$arPreIMAGE = array(
									"name" => false,
									"type" => false,
									"tmp_name" => false,
									"error" => 4,
									"size" => 0,
								);
								
								$arPreIMAGE["del"] = "Y";
								$arPreIMAGE["old_file"] 	= CFile::MakeFileArray(CFile::GetPath(IntVal($_REQUEST['id'])));
								$arPreIMAGE["MODULE_ID"] 	= "iblock";
								
								$arLoadProductArray = Array(
									"MODIFIED_BY"     => $USER->GetID(),
									"IBLOCK_SECTION"  => false,
									"IBLOCK_ID"       => 4,
									"PROPERTY_VALUES" => $Prop,
									"NAME"            => $arStageFields['NAME'],
									"ACTIVE"          => "Y",
									"PREVIEW_TEXT"    => $arStageFields['PREVIEW_TEXT'],
									"PREVIEW_PICTURE" => $arPreIMAGE,
								);
								
								$elStep   = new CIBlockElement;
								$elStep->Update($arStageFields['ID'], $arLoadProductArray);
								
								unset($arStageFields['PREVIEW_PICTURE']);
							}
						}
					?>
						<div class="stage">
							<input type="hidden" name="stage_id[<?=$strMKey?>]" value="<?=$arStageFields['ID']?>">
							<div class="delete_icon"><div title="Удалить этап" onClick="stage_number = <?=$strMKey?>; deleteStage();"></div></div>
							<h2><?=numberingStage($strMKey, true)?> этап</h2>
							<div class="description">
								<div class="form_field">
									<h5>Описание <?=numberingStage($strMKey);?> этапа<span class="no_text">?</span></h5>
									<textarea name="stage_description[st_<?=$arStageFields['ID']?>]" cols="" rows=""><?=$arStageFields['~PREVIEW_TEXT']?></textarea>
								</div>
								<div class="form_field">
									<h5>Фото этапа (600х400 px)</h5>
									<div class="input_file">
										<div class="blocker"></div>
										<input type="file" name="photo[st_<?=$arStageFields['ID']?>]" value="Обзор" class="text" id="inputFile">
										<div class="browse_button" title="Выбрать файл"><input type="button" value="Обзор"></div>
										<input type="hidden" name="stage_photo[st_<?=$arStageFields['ID']?>]" value="<?=$arFile['ID']?>">
										<div class="new_file_name"></div>
									</div>
								<?if(IntVal($arStageFields['PREVIEW_PICTURE']) > 0):?>
									<div class="file_name"><div class="img_icon"></div><a href="/upload/<?=$arFile['SUBDIR']?>/<?=$arFile['FILE_NAME']?>" target="_blank"><?=$arFile['FILE_NAME']?></a><span class="file_size">(<?=number_format(($arFile['FILE_SIZE']/1024), "","",2)?> кб)</span><img src="/images/spacer.gif" width="7" height="7" alt="Удалить изображение" class="delete_icon fir" onClick="deleteStageImage(this, '<?=$arFile['ID']?>');"></div>
								<?endif;?>
								</div>
							</div>
							<div class="ingredient">
								<h5>Ингредиенты <?=numberingStage($strMKey);?> этапа</h5>
								<ul>
								<?
								$strGroupArr = "";
								$strIngArr = "";
								foreach($arResult as $strKey => $arItem):?>
									<li><span class="name"><?=$arItem['NAME']?></span><span class="input_block"><input class="text" name="ingredients_<?=$strMKey?>_number[]" value="<?=$arItem['VALUE']?>" type="text"><input name="ingredients_<?=$strMKey?>_id[]" value="<?=$arItem['ID']?>" type="hidden"><span class="unit"><?=$arItem['UNIT']?></span><span class="no_text">?</span><img class="delete" width="9" height="9" src="/images/spacer.gif" alt=""/></span></li>
									<?
									$strGroupArr .= '"'.$arSortArray[$arItem['ID']][0].'", ';
									$strIngArr   .= '"'.$arSortArray[$arItem['ID']][1].'", ';
									?>
								<?endforeach; unset($arResult);
								$strGroupArr = substr($strGroupArr, 0, -2);
								$strIngArr = substr($strIngArr, 0, -2);
								?>
								</ul>
								<script language="javascript" type="text/javascript">
								<!--
									stagesIngredientsArray[<?=$strMKey?>]    = new Array();
									stagesIngredientsArray[<?=$strMKey?>][0] = new Array(<?=$strGroupArr?>);//номера групп
									stagesIngredientsArray[<?=$strMKey?>][1] = new Array(<?=$strIngArr?>);//номера ингредиентов в группе
								//-->
								</script>
								<div class="choose"><a href="#" onClick="stage_number = <?=$strMKey?>; showStageIngredientsLayer(); return false;">Выбрать ингредиенты</a></div>
								<div class="scales">
								    Таблица мер 
								  <img width="12" height="12" alt="" src="/images/icons/scales.gif"/>
								</div>
							</div>
							<div class="clear"></div>
						</div>
					<?endforeach;?>
					<div class="button"><input type="button" value="Добавить этап" onClick="addStage();"></div>
				</div>
			</div>
			<div id="save_recipe" class="button" onclick="checkStageForm('dish');">Сохранить рецепт</div>
			<!--<div class="button"><input type="submit" value="Сохранить рецепт" onClick="checkStageForm('dish'); return false;"></div>-->
		</form>
		</div>
	<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
<?} else {
	LocalRedirect("/all/");
	}?>
	<?} else { LocalRedirect("/profile/recipes/"); }?>
<?} else {
	LocalRedirect("/auth/?backurl=/admin/edit/".$_REQUEST['r']."/");
}
?>
