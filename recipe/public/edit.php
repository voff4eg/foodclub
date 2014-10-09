<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
require($_SERVER["DOCUMENT_ROOT"]."/classes/factory.class.php");
\Bitrix\Main\Data\StaticHtmlCache::getInstance()->markNonCacheable();
$APPLICATION->AddHeadScript("/js/ss_data.js");

CModule::IncludeModule("iblock");

if( $USER->IsAdmin() || $USER->IsAuthorized() ){
	
function numberingStage($stageNumber, $bOneCount = false) {
	if($bOneCount === true)
	{
		$numberingArray1 = Array("Первый", "Второй", "Третий", "Четвёртый", "Пятый", "Шестой", "Седьмой", "Восьмой", "Девятый");
		$numberingArray2 = Array("Одиннадцатый", "Двенадцатый", "Тринадцатый", "Четырнадцатый", "Пятнадцатый", "Шеснадцатый", "Семнадцатый", "Восемнадцатый", "Девятьнадцатый");
		$numberingArray3 = Array("Десятый", "Двадцатый", "Тридцатый", "Сороковой", "Пятидесятый", "Шестидесятый", "Семидесятый", "Восьмидесятый", "Девяностый");
		$numberingArray4 = Array("десять", "двадцать", "тридцать", "сорок", "пятьдесят", "шестьдесят", "семьдесят", "восемьдесят", "девяносто");	
	} 
	else
	{
		$numberingArray1 = Array("первого", "второго", "третьего", "четвёртого", "пятого", "шестого", "седьмого", "восьмого", "девятого");
		$numberingArray2 = Array("одиннадцатого", "двенадцатого", "тринадцатого", "четырнадцатого", "пятнадцатого", "шестнадцатого", "семнадцатого", "восемнадцатого", "девятнадцатого");
		$numberingArray3 = Array("десятого", "двадцатого", "тридцатого", "сорокового", "пятидесятого", "шестидесятого", "семидесятого", "восьмидесятого", "девяностого");
		$numberingArray4 = Array("десять", "двадцать", "тридцать", "сорок", "пятьдесят", "шестьдесят", "семьдесят", "восемьдесят", "девяносто");	
	}
	
	if (IntVal($stageNumber)+1 < 10) {
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

$APPLICATION->SetPageProperty("title", "Foodclub.ru — Редактирование рецепта.");
$APPLICATION->SetTitle("Foodclub");

$CFClub = CFClub::getInstance();

/*
 * Данные по рецепту
 */
$intRecipeId = IntVal($_REQUEST['r']);
$rsRecipe = CIBlockElement::GetById($intRecipeId);
if(SITE_ID == "s1"){
    $mainIngrFilter = array("IBLOCK_ID" => "14");
}elseif(SITE_ID == "fr"){
    $mainIngrFilter = array("IBLOCK_ID" => "22");
}
$rsIngredients = CIBlockElement::GetList(array("NAME"=>"ASC"),$mainIngrFilter,false,false,array("ID","NAME"));
while($arIngredients = $rsIngredients -> GetNext()){
    $Ingredients[$arIngredients["ID"]] = $arIngredients["NAME"];
}
if($obRecipe = $rsRecipe->GetNextElement()){
	$arProp = $obRecipe->GetProperties();
	$arFields = $obRecipe->GetFields();
	
	if($arFields['CREATED_BY'] == $USER->GetID() || $USER->IsAdmin()){
	
		//if(!($USER->IsAdmin()) && (MakeTimeStamp($arFields["DATE_CREATE"]) <= (time() - 3600*24*3))){
		if(!($USER->IsAdmin()) && (MakeTimeStamp($arProp["edit_deadline"]["VALUE"]) < time())){
			//echo ConvertTimeStamp(MakeTimeStamp($arFields["DATE_CREATE"]))." @@ ".ConvertTimeStamp(time() - 3600*24*3);die;
			/*echo "<div class='b-error-message'>
				<div class='b-error-message__pointer'>
					<div class='b-error-message__pointer__div'></div>
				</div>
				Вам нельзя редактировать этот рецепт, т.к. он был добавлен более, чем 3 дня назад.
			</div>
			<div class='i-clearfix'></div>";
			require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
			die;*/
			LocalRedirect("/detail/".$arFields["ID"]."/?cant_edit");
		}
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
			"PROPERTY_VALUES" => $P,
			"NAME"            => $arFields['NAME'],
			"ACTIVE"          => "Y",
			"PREVIEW_TEXT"    => $arFields['PREVIEW_TEXT'],
			"PREVIEW_PICTURE" => $arPreIMAGE,
		);
		
		if(SITE_ID == "s1"){
		    $arLoadProductArray["IBLOCK_ID"] = 5;
		}elseif(SITE_ID == "fr"){
		    $arLoadProductArray["IBLOCK_ID"] = 24;
		}
		
		$elStep   = new CIBlockElement;
		$elStep->Update($intRecipeId, $arLoadProductArray);
		
		unset($arFields['PREVIEW_PICTURE']);
	}
	if(SITE_ID == "s1"){
	    $arKitchens = $CFClub->getKitchens();
	}elseif(SITE_ID == "fr"){
	    $arKitchens = $CFClub->getKitchens(true,array(),"fr");
	}
	
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
			if(intval($arDash["ID"]) > 0){
                            if (strlen($strDumpId) == 0) {$strDumpId = '"'.$arDash['ID'].'"';} else {$strDumpId .= ', "'.$arDash['ID'].'"';}
                            if (strlen($strDumpName) == 0) {$strDumpName = '"'.$arDash['NAME'].'"';} else {$strDumpName .= ', "'.$arDash['NAME'].'"';}
                        }
		}
		$strJavaTwo .= "cookingArray[1][".$intNum."][0] = new Array($strDumpId);";
		$strJavaTwo .= "cookingArray[1][".$intNum."][1] = new Array($strDumpName);";
		$intNum++;
	}
	if(SITE_ID == "s1"){
	    $arUnits = $CFClub->getUnitList();
	}elseif(SITE_ID == "fr"){
	    $arUnits = $CFClub->getUnitList("fr");
	}
	$rsAllDishTypes = CIBlockElement::GetList(array("name"=>"ASC"),array("IBLOCK_ID"=>1,"ACTIVE"=>"Y"),false,false,array("ID","NAME"));
	while($arAllDishTypes = $rsAllDishTypes -> GetNext()){
		$ALlDishTypes[ $arAllDishTypes["ID"] ] = $arAllDishTypes;
	}
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
	/*if(intval($arProp["main_ingredient"]["VALUE"]) > 0){
		$arProp["main_ingredient"] = CIBlockElement::GetByID($arProp["main_ingredient"]["VALUE"])->Fetch();
	}*/
	?>
	<div id="content">
	<form action="<?=SITE_DIR?>recipe/edit/post/" method="post" enctype="multipart/form-data" id="add_recipe_form">
	<input type="hidden" name="active_from" value="<?=$arFields['ACTIVE_FROM']?>">
	<input type="hidden" name='recipe_id' value="<?=$intRecipeId?>">
	<input type="hidden" name='comment_count' value="<?=IntVal($arProp['comment_count']['VALUE'])?>">
			<div class="body">
				<div id="dish_description">
					<h2>Общее описание</h2>
					<div class="dish_properties">
						<div class="form_field">
							<h5>Название блюда*<span class="no_text">?</span></h5>
							<input type="text" name="name" value="<?=$arFields['NAME']?>" class="text">
						</div>
						<div class="form_field">
							<h5>Описание блюда*<span class="no_text">?</span></h5>
							<textarea name="dish_description" cols="" rows=""><?=$arFields['~PREVIEW_TEXT']?></textarea>
						</div>
						<div class="form_field">
							<h5>Главное фото (600х400 px)*<span class="no_text">?</span></h5>
							<div class="input_file"><input type="file" name="general_photo" class="text"><input type="hidden" name="main_photo_id" value="<?=$arMainFile['ID']?>"></div>
							<?if(IntVal($arFields['PREVIEW_PICTURE']) > 0):?>
								<div class="file_name"><div class="img_icon"></div><a href="/upload/<?=$arMainFile['SUBDIR']?>/<?=$arMainFile['FILE_NAME']?>" target="_blank"><?=$arMainFile['FILE_NAME']?></a><span class="file_size">(<?=number_format(($arMainFile['FILE_SIZE']/1024), "","",2)?> кб)</span><a href="#" title="Удалить изображение" class="delete_icon" id="<?=$arMainFile['ID']?>"></a></div>
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
									var typeId=<?=$Selected["ID"]?>;
									<?=$strJavaTwo?>
								//-->
							</script>
                                                    <?//echo"<br/>";print_r($Selected["ID"]);echo"<br/>";?>
							<h5>Кухня*</h5>
							<select name="cooking"><!--onChange="chooseDishType(this);"-->
								<?=$strHtml?>
							</select>
						</div>
						<div class="form_field">
							<h5>Тип блюда*</h5>
							<select name="dish_type">
								<?foreach($ALlDishTypes as $dish):?>
									<?if($dish["ID"] == $arProp["dish_type"]["VALUE"]):?>
										<option value="<?=$dish["ID"]?>" selected="selected"><?=$dish["NAME"]?></option>
									<?else:?>
										<option value="<?=$dish["ID"]?>"><?=$dish["NAME"]?></option>
									<?endif;?>
								<?endforeach;?>
							</select>
							<!--<script language="javascript">-->
								<!--
									chooseDishType();
								//-->
							<!--</script>-->
						</div>
						<div class="form_field">
							<h5>Основной ингредиент*<span class="no_text">?</span></h5>
                                                        <select name="main_ingredient_id">
                                                            <?foreach($Ingredients as $key=>$value):?>
                                                                <?if($key == $arProp["main_ingredient"]["VALUE"]):?>
                                                                    <option value="<?=$key?>" selected="selected"><?=$value?></option>
                                                                <?else:?>
                                                                    <option value="<?=$key?>"><?=$value?></option>
                                                                <?endif;?>
                                                            <?endforeach;?>
							</select>
						</div>
						<!--<div class="form_field">
							<h5>Количество килокалорий на порцию</h5>
							<input type="text" name="kkal" value="<?=$arProp["kcals"]["VALUE"]?>" class="text">
						</div>-->
						<div class="form_field yield">
							<h5>Количество порций</h5>
							<input type="text" name="yield" value="<?=$arProp["portion"]["VALUE"]?>" class="text">
						</div>
						<!--cooking_time-->
						<?$cooking_time_hours = $arProp["cooking_time"]["VALUE"]/60;
						$cooking_time_minutes = $arProp["cooking_time"]["VALUE"]%60;?>
						<div class="form_field dish_time">
							<h5>Время приготовления</h5>
							<select name="hours"><?$Factory = new CFactory;?>
								<?for($i=0;$i<=24;$i++):?>
									<?if($i == intval($cooking_time_hours)){
										$selected = "selected=&quot;selected&quot;";
									}?>
									<?if($i < 24):?>
										<option <?=(strlen($selected) > 0 ? $selected." " : "")?>value="<?=$i?>"><?=$i?> <?=$Factory->plural_form($i,array("час","часа","часов"));?></option>
									<?else:?>
										<option <?=(strlen($selected) > 0 ? $selected." " : "")?>value="<?=$i?>"><?=$i?> <?=$Factory->plural_form($i,array("час","часа","часов"));?> и более</option>
									<?endif;?>
									<?unset($selected);?>
								<?endfor;?>
							</select>
							<select name="minutes">
								<?$j = 0;?>
								<?while($j <= 50):?>
									<?if($j == intval($cooking_time_minutes)){
										$selected = "selected=&quot;selected&quot;";
									}?>
									<option <?=(strlen($selected) > 0 ? $selected." " : "")?>value="<?=$j?>"><?=$j?> <?=$Factory->plural_form($j,array("минута","минуты","минут"))?></option>
									<?if($j < 30):?>
										<?$j+=5;?>
									<?else:?>
										<?$j+=10;?>
									<?endif;?>
									<?unset($selected);?>
								<?endwhile;?>
							</select>
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
						
						$rsIng = CIBlockElement::GetList(Array(), Array("ID"=>$arStageProp['ingredient']['VALUE']), false, false, Array("ID","NAME","PROPERTY_unit","PROPERTY_fr_unit","PROPERTY_fr_name"));
						while($arIngSrc = $rsIng->GetNext()){
							$arIng[ $arIngSrc['ID'] ] = $arIngSrc; 
						}
						
						if(SITE_ID == "s1"){
							foreach($arStageProp['ingredient']['VALUE'] as $strKey => $strItem){
							    $arResult[] = Array(
								"ID"    => $strItem,
							        "NAME"  => $arIng[ $strItem ]['NAME'],
								"UNIT"  => $arIng[ $strItem ]['PROPERTY_UNIT_VALUE'],
							        "VALUE" => $arStageProp['numer']['VALUE'][ $strKey ],
							    );
							}
						}elseif(SITE_ID == "fr"){
							foreach($arStageProp['ingredient']['VALUE'] as $strKey => $strItem){
							    $arResult[] = Array(
								"ID"    => $strItem,
								"NAME"  => (strlen($arIng[ $strItem ]['PROPERTY_FR_NAME_VALUE']) > 0 ? $arIng[ $strItem ]['PROPERTY_FR_NAME_VALUE'] : $arIng[ $strItem ]['NAME']),
								"UNIT"  => (strlen($arIng[ $strItem ]['PROPERTY_FR_UNIT_VALUE']) > 0 ? $arIng[ $strItem ]['PROPERTY_FR_UNIT_VALUE'] : $arIng[ $strItem ]['PROPERTY_UNIT_VALUE']),
							        "VALUE" => $arStageProp['numer']['VALUE'][ $strKey ],
								);
							}
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
									"PROPERTY_VALUES" => $Prop,
									"NAME"            => $arStageFields['NAME'],
									"ACTIVE"          => "Y",
									"PREVIEW_TEXT"    => $arStageFields['PREVIEW_TEXT'],
									"PREVIEW_PICTURE" => $arPreIMAGE,
								);
								
								if(SITE_ID == "s1"){
								    $arLoadProductArray["IBLOCK_ID"] = 4;
								}elseif(SITE_ID == "fr"){
								    $arLoadProductArray["IBLOCK_ID"] = 23;
								}
								
								$elStep   = new CIBlockElement;
								$elStep->Update($arStageFields['ID'], $arLoadProductArray);
								
								unset($arStageFields['PREVIEW_PICTURE']);
							}
						}
					?>
						<div class="stage">
							<input type="hidden" name="stage_id[<?=$strMKey?>]" value="<?=$arStageFields['ID']?>">
							<!--<div class="delete_icon"><div title="Удалить этап" onClick="stage_number = <?=$strMKey?>; deleteStage();"></div></div>-->
                                                        <div class="delete_icon"><a href="#" title="Удалить этап"></a></div>
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
									<div class="file_name"><div class="img_icon"></div><a href="/upload/<?=$arFile['SUBDIR']?>/<?=$arFile['FILE_NAME']?>" target="_blank"><?=$arFile['FILE_NAME']?></a><span class="file_size">(<?=number_format(($arFile['FILE_SIZE']/1024), "","",2)?> кб)</span><a href="#" title="Удалить изображение" class="delete_icon" id="<?=$arFile['ID']?>"></a></div>
								<?endif;?>
								</div>
							</div>
							<div class="ingredient">
								<h5>Ингредиенты <?=numberingStage($strMKey);?> этапа<span class="scales"><img src="/images/icons/scales.gif" width="12" height="12" alt="Таблица мер"><span class="hint"><span>Таблица мер</span></span></span></h5>
								<div class="stage_ing_list">
								<?
								$strGroupArr = "";
								$strIngArr = "";
								foreach($arResult as $strKey => $arItem):?>
								<div class="item">
                                                                    <div class="search_list">
                                                                        <ul class="search_list">
                                                                        </ul>
                                                                    </div>
                                                                    <input type="text" value="<?=$arItem['NAME']?>" class="text smartsearch"><span class="search_list_icon"><span title="Весь список ингредиентов"></span></span><input type="hidden" value="<?=$arItem['ID']?>" class="click_field"><input type="hidden" name="ingredients_<?=$strMKey?>_id[]" value="<?=$arItem['ID']?>"><input type="text" name="ingredients_<?=$strMKey?>_number[]" value="<?=$arItem['VALUE']?>" style="display: inline;" class="text unit"><span class="unit" style="display: inline;"><?=$arItem['UNIT']?></span><span class="no_text">?</span><a href="#" class="delete_icon" title="Удалить ингредиент"></a></div>
									<?
									$strGroupArr .= '"'.$arSortArray[$arItem['ID']][0].'", ';
									$strIngArr   .= '"'.$arSortArray[$arItem['ID']][1].'", ';
									?>
								<?endforeach;
								$strGroupArr = substr($strGroupArr, 0, -2);
								$strIngArr = substr($strIngArr, 0, -2);
								?>
								<script language="javascript" type="text/javascript">
								<!--
									stagesIngredientsArray[<?=$strMKey?>]    = new Array();
									stagesIngredientsArray[<?=$strMKey?>][0] = new Array(<?=$strGroupArr?>);//номера групп
									stagesIngredientsArray[<?=$strMKey?>][1] = new Array(<?=$strIngArr?>);//номера ингредиентов в группе
								//-->
								</script>
								<!--<div class="choose"><a href="#" onClick="stage_number = <?=$strMKey?>; showStageIngredientsLayer(); return false;">Выбрать ингредиенты</a></div>-->
								<?if(count($arResult) < 3):?><?for($i = 1; $i <= (3 - count($arResult)); $i++):?>
									<div class="item">
										<div class="search_list">
											<ul class="search_list">
											</ul>
										</div>
										<input type="text" value="" class="text smartsearch"><span class="search_list_icon"><span title="Весь список ингредиентов"></span></span><input type="hidden" value="" class="click_field"><input type="hidden" name="ingredients_<?=$strMKey?>_id[]" value=""><input type="text" name="ingredients_<?=$strMKey?>_number[]" value="" class="text unit" style=""><span style="" class="unit"></span><span class="no_text">?</span><a href="#" class="delete_icon" title="Удалить ингредиент"></a>
                                                                        </div>
                                                                <?endfor;?><?elseif(count($arResult) == 0):?>
                                                                        <div class="item">
										<div class="search_list">
											<ul class="search_list">
											</ul>
										</div>
										<input type="text" value="" class="text smartsearch"><span class="search_list_icon"><span title="Весь список ингредиентов"></span></span><input type="hidden" value="" class="click_field"><input type="hidden" name="ingredients_<?=$strMKey?>_id[]" value=""><input type="text" name="ingredients_<?=$strMKey?>_number[]" value="" class="text unit" style=""><span style="" class="unit"></span><span class="no_text">?</span><a href="#" class="delete_icon" title="Удалить ингредиент"></a>
									</div>
									<div class="item">
										<div class="search_list">
											<ul class="search_list">
											</ul>
										</div>
										<input type="text" value="" class="text smartsearch"><span class="search_list_icon"><span title="Весь список ингредиентов"></span></span><input type="hidden" value="" class="click_field"><input type="hidden" name="ingredients_<?=$strMKey?>_id[]" value=""><input type="text" name="ingredients_<?=$strMKey?>_number[]" value="" class="text unit" style=""><span style="" class="unit"></span><span class="no_text">?</span><a href="#" class="delete_icon" title="Удалить ингредиент"></a>
									</div>
                                                                        <div class="item">
										<div class="search_list">
											<ul class="search_list">
											</ul>
										</div>
										<input type="text" value="" class="text smartsearch"><span class="search_list_icon"><span title="Весь список ингредиентов"></span></span><input type="hidden" value="" class="click_field"><input type="hidden" name="ingredients_<?=$strMKey?>_id[]" value=""><input type="text" name="ingredients_<?=$strMKey?>_number[]" value="" class="text unit" style="display: inline;"><span class="unit" style="display: inline;"></span><span class="no_text">?</span><a href="#" class="delete_icon" title="Удалить ингредиент"></a>
									</div>
                                                                <?endif;?>
                                                                <?unset($arResult);?></div>
								<div class="add_ingredient"><span class="icon"></span><a href="#">Добавить ингредиент</a></div>
							</div>
							<div class="clear"></div>
						</div>
					<?endforeach;?>
					<div class="button"><input type="button" value="Добавить этап" onClick="addStage();"></div>
				</div>
			</div>
			<div class="save_recipe"><input type="submit" value="Сохранить рецепт" id="save_recipe"></div>
			<!--<div class="button"><input type="submit" value="Сохранить рецепт" onClick="checkStageForm('dish'); return false;"></div>-->
		</form>
		</div>
	<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
<?} else {
	LocalRedirect(SITE_DIR."all/");
	}?>
	<?} else { LocalRedirect("/profile/recipes/"); }?>
<?} else {
	LocalRedirect("/auth/?backurl=/admin/edit/".$_REQUEST['r']."/");
}
?>
