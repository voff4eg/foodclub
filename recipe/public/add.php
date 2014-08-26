<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
require($_SERVER["DOCUMENT_ROOT"]."/classes/factory.class.php");

if( $USER->IsAdmin() || $USER->IsAuthorized() ){
	
$APPLICATION->SetPageProperty("title", "Foodclub.ru — Добавление рецепта.");
$APPLICATION->SetTitle("Foodclub.ru — Добавление рецепта.");
$APPLICATION->AddHeadScript("/js/ss_data.js");

CModule::IncludeModule("iblock");
$CFClub = CFClub::getInstance();
	
$arKitchens = $CFClub->getKitchens(true);
$rsIngredients = CIBlockElement::GetList(array("NAME"=>"ASC"),array("IBLOCK_ID" => "14"),false,false,array("ID","NAME"));
while($arIngredients = $rsIngredients -> GetNext()){
    $Ingredients[$arIngredients["ID"]] = $arIngredients["NAME"];
}

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
$rsAllDishTypes = CIBlockElement::GetList(array("name"=>"ASC"),array("IBLOCK_ID"=>1,"ACTIVE"=>"Y"),false,false,array("ID","NAME"));
while($arAllDishTypes = $rsAllDishTypes -> GetNext()){
	$ALlDishTypes[ $arAllDishTypes["ID"] ] = $arAllDishTypes;
}
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
    <div class="hint">
		<div class="pointer"><div></div></div>
		<div class="hint_body">

			<p>Вы хотите опубликовать рецепт, но у Вас нет пошаговых фотографий или Вам лень заносить ингредиенты?</p>
			<p>Выберите наиболее подходящий для Вашего рецепта <a href="http://www.foodclub.ru/blogs/">клуб</a> и опубликуйте рецепт там, его увидят не только наши посетители, но и читатели <a href="http://syndicated.livejournal.com/foodclub_topics/profile" target="_blank">трансляций</a> в ЖЖ.</p>
		</div>
	</div>
	<h1>Добавление рецепта</h1>
    <div class="clear"></div>
<form action="/recipe/add/post/" method="post" enctype="multipart/form-data" id="add_recipe_form">
		<div class="body">
			<div id="dish_description">
				<h2>Общее описание</h2>
				<div class="dish_properties">
					<div class="form_field">
						<h5>Название блюда*<span class="no_text">?</span></h5>
						<input type="text" name="name" value="" class="text">
					</div>
					<div class="form_field">
						<h5>Описание блюда*<span class="no_text">?</span></h5>
						<textarea name="dish_description" cols="10" rows="10"></textarea>
					</div>
					<div class="form_field">
						<h5>Главное фото (600х400 px)*<span class="no_text">?</span></h5>
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
								var typeId;
								<?=$strJavaTwo?>
							//-->
						</script>
						<h5>Кухня*</h5>
						<select name="cooking" <!--onChange="chooseDishType(this);"-->>
							<?=$strHtml?>
						</select>
					</div>
					<div class="form_field">
						<h5>Тип блюда*</h5>
						<select name="dish_type">
							<?foreach($ALlDishTypes as $dish):?>
								<option value="<?=$dish["ID"]?>"><?=$dish["NAME"]?></option>
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
						<input type="text" name="kkal" value="" class="text">
					</div>-->
					<div class="form_field yield">
						<h5>Количество порций</h5>
						<input type="text" name="yield" value="" class="text">
					</div>
					<div class="form_field dish_time">
						<h5>Время приготовления</h5><?$Factory = new CFactory;?>
						<select name="hours">
						<?for($i=0;$i<=24;$i++):?>
							<?if($i < 24):?>
								<option value="<?=$i?>"><?=$i?> <?=$Factory->plural_form($i,array("час","часа","часов"));?></option>
							<?else:?>
								<option value="<?=$i?>"><?=$i?> <?=$Factory->plural_form($i,array("час","часа","часов"));?> и более</option>
							<?endif;?>
						<?endfor;?>
						</select>
						<select name="minutes">
							<?$j = 0;?>
							<?while($j <= 50):?>
								<option value="<?=$j?>"><?=$j?> <?=$Factory->plural_form($j,array("минута","минуты","минут"))?></option>
								<?if($j < 30):?>
									<?$j+=5;?>
								<?else:?>
									<?$j+=10;?>
								<?endif;?>
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
				<div class="stage">
                                    <div class="delete_icon"><a href="#" title="Удалить этап"></a></div>
					<!--<div class="delete_icon"><div title="Удалить этап" onClick="stage_number = 0; deleteStage();"></div></div>-->
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
								<input type="file" name="photo[]" value="Обзор" class="text customFile">
								<div class="browse_button" title="Выбрать файл"><input type="button" value="Обзор"></div>
								<div class="new_file_name"></div>
							</div>
                                                        <div class="file_name"></div>
						</div>
					</div>
					<div class="ingredient">
						<h5>Ингредиенты первого этапа<span class="scales"><img src="/images/icons/scales.gif" width="12" height="12" alt="Таблица мер"><span class="hint"><span>Таблица мер</span></span></span></h5>
						<!--<div class="choose"><a href="#" onClick="stage_number = 0; showStageIngredientsLayer(); return false;">Выбрать ингредиенты</a></div>-->
					<div class="stage_ing_list">
						<div class="item">
							<div class="search_list">
								<ul class="search_list">
								</ul>
							</div>
							<!--В одну строку-->
							<input type="text" value="" class="text smartsearch"><span class="search_list_icon"><span title="Весь список ингредиентов"></span></span><input type="hidden" value="" class="click_field"><input type="hidden" name="ingredients_0_id[]" value=""><input type="text" name="ingredients_0_number[]" value="" class="text unit"><span class="unit"></span><span class="no_text">?</span><a href="#" class="delete_icon" title="Удалить ингредиент"></a>
						</div>
						<div class="item">
							<div class="search_list">
								<ul class="search_list">
								</ul>
							</div>
							<!--В одну строку-->
							<input type="text" value="" class="text smartsearch"><span class="search_list_icon"><span title="Весь список ингредиентов"></span></span><input type="hidden" value="" class="click_field"><input type="hidden" name="ingredients_0_id[]" value=""><input type="text" name="ingredients_0_number[]" value="" class="text unit"><span class="unit"></span><span class="no_text">?</span><a href="#" class="delete_icon" title="Удалить ингредиент"></a>
						</div>
						<div class="item">
							<div class="search_list">
								<ul class="search_list">
								</ul>
							</div>
							<!--В одну строку-->
							<input type="text" value="" class="text smartsearch"><span class="search_list_icon"><span title="Весь список ингредиентов"></span></span><input type="hidden" value="" class="click_field"><input type="hidden" name="ingredients_0_id[]" value=""><input type="text" name="ingredients_0_number[]" value="" class="text unit"><span class="unit"></span><span class="no_text">?</span><a href="#" class="delete_icon" title="Удалить ингредиент"></a>
						</div>
					</div>
					<div class="add_ingredient"><span class="icon"></span><a href="#">Добавить ингредиент</a></div>
					</div>
					<div class="clear"></div>
				</div>
				<div class="button"><input type="button" value="Добавить этап" onClick="addStage();"></div>
				<div class="conditions"><label class="checkbox"><input type="checkbox" name="add_mobile" /> <span>&mdash; Я хочу, чтобы этот рецепт был добавлен в программу <a href="/iphone/">Foodclub HD для iPhone</a> и других мобильных устройств.</span></label></div>
			</div>
		</div>
		<!--onclick="checkStageForm('dish');"  class="button"-->
		<div class="save_recipe"><input type="submit" value="Добавить рецепт" id="save_recipe"></div>
		<!--   <div class="button"><input type="submit" value="Сохранить рецепт" onClick="checkStageForm('dish'); return false;"></div>-->
	</form>
	</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
<?} else {
	LocalRedirect("/auth/?backurl=/recipe/add/");
}
?>
