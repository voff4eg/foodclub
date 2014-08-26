<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

if( $USER->IsAdmin() || $USER->IsAuthorized() ){
	
$APPLICATION->SetPageProperty("title", "Добавить рецепт на сайт кулинарных рецептов. Foodclub");
$APPLICATION->SetPageProperty("description", "Поделитесь своим фирменным рецептом с помощью формы добавления рецепта на кулинарном сайте.");
$APPLICATION->SetPageProperty("keywords", "добавить рецепт, добавление рецепта, добавление кулинарного рецепта, форма добавления рецепта");
$APPLICATION->SetTitle("Добавление рецепта");

CModule::IncludeModule("iblock");
$CFClub = CFClub::getInstance();
	
$arKitchens = $CFClub->getKitchens(true);

$strHtml = '';
$strJavaOne = '';
$strJavaTwo = '';
$intNum = 0;

foreach ($arKitchens as $arItem)
{
	$strHtml .= '<option value="'.$arItem['ID'].'" '.(strlen($strHtml) == 0 ? "selected='selected'" : "").'>'.$arItem['NAME'].'</option>';
	if (strlen($strJavaOne) == 0) {$strJavaOne = '"'.$arItem['ID'].'"';} else {$strJavaOne .= ', "'.$arItem['ID'].'"';}
	
	$strJavaTwo .= 'cookingArray[1]['.$intNum.'] = new Array();';
	$strDumpId = ''; $strDumpName = '';
	
	/*
	 * Сортировка типов блюд
	 */
	$arDumpDish = Array(); 
	foreach ($arItem['DISH'] as $arItem){
		$arDumpDish[ $arItem['NAME'] ] = $arItem;
	}
	ksort($arDumpDish);
	reset($arDumpDish);
	
	
	//echo "<pre>"; print_r($arItem['DISH']); echo "</pre>";
	
	
	foreach ($arDumpDish as $arDash)
	{
		
		if (strlen($strDumpId) == 0) {$strDumpId = '"'.$arDash['ID'].'"';} else {$strDumpId .= ', "'.$arDash['ID'].'"';}
		if (strlen($strDumpName) == 0) {$strDumpName = '"'.$arDash['NAME'].'"';} else {$strDumpName .= ', "'.$arDash['NAME'].'"';}
	}
	$strJavaTwo .= "cookingArray[1][".$intNum."][0] = new Array($strDumpId);";
	$strJavaTwo .= "cookingArray[1][".$intNum."][1] = new Array($strDumpName);";
	$intNum++;
}

$arUnits = $CFClub->getUnitList();
//echo "<pre>"; print_r($arUnits); echo "</pre>";
$strUnitHtml = '';
$strUnitsHtml = '';
$intNum = 0;
$strTemp = '';
foreach ($arUnits as $arUnit)
{
	if (!isset($strId)) {$strId = '"'.$arUnit['ID'].'"';} else {$strId .= ', "'.$arUnit['ID'].'"';}
	if (!isset($strName)) {$strName = '"'.$arUnit['NAME'].'"';} else {$strName .= ', "'.$arUnit['NAME'].'"';}
	$strDumpId = ''; $strDumpName = ''; $strDumpUnit = '';
	$strTemp .= "ingredientArray[2][$intNum] = new Array();";
	foreach($arUnit['UNITS'] as $arItem)
	{
		if (strlen($strDumpId) == 0) {$strDumpId = '"'.$arItem['ID'].'"';} else {$strDumpId .= ', "'.$arItem['ID'].'"';}
		if (strlen($strDumpName) == 0) {$strDumpName = '"'.$arItem['NAME'].'"';} else {$strDumpName .= ', "'.$arItem['NAME'].'"';}
		if (strlen($strDumpUnit) == 0) {$strDumpUnit = '"'.$arItem['UNIT'].'"';} else {$strDumpUnit .= ', "'.$arItem['UNIT'].'"';}
	}
	$strTemp .= "ingredientArray[2][".$intNum."][0] = new Array($strDumpId);";
	$strTemp .= "ingredientArray[2][".$intNum."][1] = new Array($strDumpName);";
	$strTemp .= "ingredientArray[2][".$intNum."][2] = new Array($strDumpUnit);";
	
	$intNum++;
}
$strUnitHtml .= "ingredientArray[0] = new Array($strId);	ingredientArray[1] = new Array($strName); ingredientArray[2] = new Array();".$strTemp;
?>
<div id="content">
	<h1>Добавление рецепта</h1>
<form name="dish" action="/recipe/add/post/" method="post" enctype="multipart/form-data">
		<div class="body">
			<div id="dish_description">
				<h2>Общее описание</h2>
				<div class="dish_properties">
					<div class="form_field">
						<h5>Название блюда<span class="no_text">?</span></h5>
						<input type="text" name="name" value="" class="text">
					</div>
					<div class="form_field">
						<h5>Описание блюда<span class="no_text">?</span></h5>
						<textarea name="dish_description" cols="10" rows="10"></textarea>
					</div>
					<div class="form_field">
						<h5>Главное фото (600х400 px)<span class="no_text">?</span></h5>
						<div class="input_file"><input type="file" class="text" name="general_photo"></div>
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
				<div class="stage">
					<div class="delete_icon"><div title="Удалить этап" onClick="stage_number = 0; deleteStage();"></div></div>
					<h2>Первый этап</h2>
					<div class="description">
						<div class="form_field">
							<h5>Описание первого этапа<span class="no_text">?</span></h5>
							<textarea name="stage_description[]" cols="10" rows="10"></textarea>
						</div>
						<div class="form_field">
							<h5>Фото этапа (600х400 px)</h5>
							<div class="input_file">
								<div class="blocker"></div>
								<input type="file" name="photo[]" value="Обзор" class="text" id="inputFile">
								<div class="browse_button" title="Выбрать файл"><input type="button" value="Обзор"></div>
								<div class="new_file_name"></div>
							</div>
						</div>
					</div>
					<div class="ingredient">
						<div class="choose"><a href="#" onClick="stage_number = 0; showStageIngredientsLayer(); return false;">Выбрать ингредиенты</a></div>
					</div>
					<div class="clear"></div>
				</div>
				<div class="button"><input type="button" value="Добавить этап" onClick="addStage();"></div>
			</div>
		</div>
		<div id="save_recipe" class="button" onclick="checkStageForm('dish');">Сохранить рецепт</div>
		<!--   <div class="button"><input type="submit" value="Сохранить рецепт" onClick="checkStageForm('dish'); return false;"></div>-->
	</form>
	</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
<?} else {
	LocalRedirect("/auth/?backurl=/recipe/add/");
}
?>