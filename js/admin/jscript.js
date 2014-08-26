$(document).ready(function() {
	$("div.dish_parents").find("span.search_list_icon").click(function() {
		showStageIngredientsLayer(10);
		$(this).siblings("div.search_list").children("ul").css({display:"none"}).empty();
	}).end().find("input.smartsearch").keypress(function(event) {
		smartsearchKeyPress(this, event);
	}).end().find("input.smartsearch").keyup(function(event) {
		smartsearchKeyUp(this, event);
	});
	$("#dish_stages").find("img.delete").click(function() {
		$(this).parentNode().remove();
	});
	//события в этапах
	$("#dish_stages").delegate("div.ingredient a.delete_icon", "click", function() {
		$(this).addClass("confirm");
		if(confirm("Удалить ингредиент?")) {
			deleteIngredient(this);
		}
		else {
			$(this).removeClass("confirm").removeClass("attention");
			return false;
		}
		return false;
	}).delegate("span.search_list_icon", "click", function() {
		stage_number = $("#dish_stages div.stage").index($(this).closest("div.stage"));
		showStageIngredientsLayer(0);
		$(this).siblings("div.search_list").children("ul").css({display:"none"}).empty();
	}).delegate("span.scales img", "mouseenter", function() {
		$(this).next("span").css({visibility:"visible"});
	}).delegate("span.scales img", "mouseleave", function() {
		$(this).next("span").css({visibility:"hidden"});
	}).delegate("span.scales img", "click", function() {
		window.open('/table/','scalesWin','width=800, height=800,toolbar=0,scrollbars=yes,status=0,directories=0,location=0,menubar=0,resizable=0');
	}).delegate("input.smartsearch", "keypress", function(event) {
		smartsearchKeyPress(this, event);
	}).delegate("input.unit", "keypress", function(event) {
		if (window.event) event = window.event;
		switch (event.keyCode ? event.keyCode : event.which ? event.which : null) {
			case 13:
				//appendNewIngredientField(this);
				if($(this).attr("value")!="") {
					if($(this).closest("div.item").next("div.item").size()!=0) {
						$(this).closest("div.item").next("div.item").find("input.smartsearch").focus();
					}
					else {
						$(this).closest("div.ingredient").find("div.add_ingredient").children("a").click();
					}
				}
				break;
		}
	}).delegate("input.unit", "keyup", function(event) {
		checkNumberField(this);
	}).delegate("input.smartsearch", "keyup", function(event) {
		smartsearchKeyUp(this, event);
	}).delegate("div.add_ingredient a", "click", function() {
		appendNewIngredientField(this);
		return false;
	}).delegate("div.delete_icon a", "click", function() {
		stage_number = $("#dish_stages div.stage").index($(this).closest("div.stage"));
		deleteStage();
		return false;
	}).delegate("div.file_name a.delete_icon", "click", function() {
		deleteStageImage(this, this.getAttribute("id"));
		return false;
	}).delegate("div.input_file input.text", "change", function() {
		HandleChanges(this);
	});
	$("#stage_button").click(function() {
		addStageIngredients();
	});
	$("div.file_name a.delete_icon").click(function() {
		deleteStageImage(this, this.getAttribute("id"));
		return false;
	});
	$("input[name='kkal'], input[name='yield']").keyup(function() {
		checkNumberField(this)
	});
	/*$("select[name='cooking']").change(function() {
		chooseDishType(this);
	});
	if($("select[name='cooking']").size() != 0) {
		chooseDishType(typeId);
	}*/
});

function appendNewIngredientField(obj) {
	//doesn't delete if there is an empty field
	//var emptyInputs = $(obj).closest("div.ingredient").find("input.smartsearch[value='']");
	//if ($(emptyInputs).size() == 0) {
		var stageIndex = $("#dish_stages div.stage").index($(obj).closest("div.stage"));
 		var newIngredient = stageIngredient(stageIndex);
		$(obj).closest("div.ingredient").find("div.stage_ing_list").append(stageIngredient(stageIndex)).find("input.smartsearch:last").focus();
	//}
	//else {
	//	$(emptyInputs)[0].focus();
	//}
}

function smartsearchKeyUp(object, evt) {
	if (window.evt) evt = window.evt;
	switch (evt.keyCode ? evt.keyCode : evt.which ? evt.which : null) {
		case 38:
			smartsearchNavUp(object);
			break;
		case 40:
			smartsearchNavDown(object);
			break;
		default:
			smartsearchFunction(object);
			showUnitField(object);
	}
}
function smartsearchKeyPress(object, evt) {
	if (window.evt) evt = window.evt;
	switch (evt.keyCode ? evt.keyCode : evt.which ? evt.which : null) {
		case 13:
			$(this).siblings("div.search_list").children("ul").css({display:"none"}).empty();
			//checkUniqueness(this);
			showUnitField(this);
			return false;
			break;
		case 9:
			$(object).siblings("div.search_list").children("ul").css({display:"none"}).empty();
			break;
	}
}

function checkUniqueness(inputObject) {
	var id = $(this).parent().find("input[name*=id]").attr("value");
	//alert($(this).parents("div.stage_ing_list").find("input[value*=" + id + "]").size());
}

//show unit field
function showUnitField(inputObject) {
	if ($(inputObject).prev("div.search_list").children("ul.search_list").children("li").size() == 0) {
		var flag = 0;
		for (var i = 0; i < smartsearchArray.length; i++) {
			if (smartsearchArray[i].toLowerCase() == $(inputObject).attr("value").toLowerCase()) {
				$(inputObject).siblings("input[type='hidden']").attr({value:smartsearchIdArray[i]}).end().attr({value:smartsearchArray[i]});
				$(inputObject).siblings("input.unit").css({display:"inline"}).focus();
				$(inputObject).siblings("span.unit").text(smartsearchUnitArray[i]).css({display:"inline"});
				flag = 1;
			}
		}
		if (flag == 0) {
			$(inputObject).siblings("input.unit").attr({value:""}).end().siblings("input.click_field").attr({value:""}).end().siblings("input[name*='id']").attr({value:""});
			$(inputObject).siblings("span.unit").css({display:"none"});
		}
	}
}

var deleteConfirm = 0;
//выбор кухни
function chooseDishType(typeId) {
	if(!typeId){var typeId = "";
	}
	var selectElement = $("select[name='cooking']");
	var cookingId = selectElement.find(":selected").attr("value");
	for (var i = 0; i < cookingArray[0].length; i++) {
		if (cookingArray[0][i] == cookingId) {
			var cookingNumber = i;
		}
	}
	fillDishTypeSelect(cookingNumber, typeId);
}

function fillDishTypeSelect(cookingNumber, typeId) {
	var selectElement = $("select[name='dish_type']");
	selectElement.empty();
	if (!cookingNumber) {var cookingNumber = 0;
	}
	for (var i = 0; i < cookingArray[1][cookingNumber][0].length; i++) {
		if (typeId != "" && cookingArray[1][cookingNumber][0][i] == typeId) {
			selectElement.append('<option value="' + cookingArray[1][cookingNumber][0][i] + '" selected="selected">' + cookingArray[1][cookingNumber][1][i] + '</option>');
		}
		else {selectElement.append('<option value="' + cookingArray[1][cookingNumber][0][i] + '">' + cookingArray[1][cookingNumber][1][i] + '</option>');
		}
	}
}

function clearElement(element) {
	var length = element.childNodes.length
	for (var i = 0; i < length; i++) {
		element.removeChild(element.childNodes[0]);
	}
}

//нумерация этапов
var numberingArray1 = new Array("первого", "второго", "третьего", "четвёртого", "пятого", "шестого", "седьмого", "восьмого", "девятого");
var numberingArray2 = new Array("одиннадцатого", "двенадцатого", "тринадцатого", "четырнадцатого", "пятнадцатого", "шестнадцатого", "семнадцатого", "восемнадцатого", "девятнадцатого");
var numberingArray3 = new Array("десятого", "двадцатого", "тридцатого", "сорокового", "пятидесятого", "шестидесятого", "семидесятого", "восьмидесятого", "девяностого");
var numberingArray4 = new Array("", "двадцать", "тридцать", "сорок", "пятьдесят", "шестьдесят", "семьдесят", "восемьдесят", "девяносто");

function numberingStage(stageNumber) {
	if (String(stageNumber + 1).length == 1) {
		var numbering = numberingArray1[stageNumber];
	}
	else {
		var lastLetter = (stageNumber + 1)%10;
		if (lastLetter == 0) {
			var numbering = numberingArray3[Math.floor((stageNumber + 1)/10) - 1];
		}
		else {
			if (Math.floor((stageNumber + 1)/10) == 1) {
				var numbering = numberingArray2[stageNumber%10];
			}
			else {
				var numbering = numberingArray4[Math.floor((stageNumber)/10) - 1] + " " + numberingArray1[stageNumber%10];
			}
		}
	}
	return numbering;
}
//добавление этапа
var numberingHeaderArray1 = new Array("Первый", "Второй", "Третий", "Четвёртый", "Пятый", "Шестой", "Седьмой", "Восьмой", "Девятый");
var numberingHeaderArray2 = new Array("Одиннадцатый", "Двенадцатый", "Тринадцатый", "Четырнадцатый", "Пятнадцатый", "Шестнадцатый", "Семнадцатый", "Восемнадцатый", "Девятнадцатый");
var numberingHeaderArray3 = new Array("Десятый", "Двадцатый", "Тридцатый", "Сороковой", "Пятидесятый", "Шестидесятый", "Семидесятый", "Восьмидесятый", "Девяностый");
var numberingHeaderArray4 = new Array("", "Двадцать", "Тридцать", "Сорок", "Пятьдесят", "Шестьдесят", "Семьдесят", "Восемьдесят", "Девяносто");
var numberingHeaderArray5 = new Array("первый", "второй", "третий", "четвёртый", "пятый", "шестой", "седьмой", "восьмой", "девятый");
function numberingStageHeader(stageNumber) {
	if (String(stageNumber + 1).length == 1) {
		var numbering = numberingHeaderArray1[stageNumber];
	}
	else {
		var lastLetter = (stageNumber + 1)%10;
		if (lastLetter == 0) {
			var numbering = numberingHeaderArray3[Math.floor((stageNumber + 1)/10) - 1];
		}
		else {
			if (Math.floor((stageNumber + 1)/10) == 1) {
				var numbering = numberingHeaderArray2[stageNumber%10];
			}
			else {
				var numbering = numberingHeaderArray4[Math.floor((stageNumber)/10) - 1] + " " + numberingHeaderArray5[stageNumber%10];
			}
		}
	}
	return numbering;
}
function addStage() {
	var newStageNumber = $("#dish_stages div.stage").size();
	var newStage = $('<div class="stage"><div class="delete_icon"><a href="#" title="Удалить этап"></a></div><h2>' + numberingStageHeader(newStageNumber) + ' этап</h2><div class="description"><div class="form_field"><h5>Описание ' + numberingStage(newStageNumber) + ' этапа<span class="no_text">?</span></h5><textarea name="stage_description[]" cols="10" rows="10"></textarea></div><div class="form_field"><h5>Фото этапа (600х400 px)</h5><div class="input_file"><div class="blocker"></div><input type="file" name="photo[]" class="text customFile"><div class="browse_button" title="Выбрать файл"><input type="button" value="Обзор"></div><div class="new_file_name"></div></div><div class="file_name"></div></div></div><div class="ingredient"><h5>Ингредиенты ' + numberingStage(newStageNumber) + ' этапа<span class="scales"><img src="/images/icons/scales.gif" width="12" height="12" alt="Таблица мер"><span class="hint"><span>Таблица мер</span></span></span></h5><div class="stage_ing_list"></div><div class="add_ingredient"><span class="icon"></span><a href="#">Добавить ингредиент</a></div></div><div class="clear"></div></div>');
	$(newStage).find("div.stage_ing_list").append(stageIngredient(newStageNumber)).append(stageIngredient(newStageNumber)).append(stageIngredient(newStageNumber));
	$("#dish_stages > div.body > div.button").before(newStage);
	newStage.find(".input_file").each(function() {
		new InputFile($(this));
	});
}

//удаление этапа
function deleteStage() {
	$("#dish_stages div.stage:eq(" + stage_number + ") div.delete_icon").addClass("confirm");
	if (confirm("Удалить этап?")) {
		$("#dish_stages div.stage:eq(" + stage_number + ")").remove();
		stagesIngredientsArray.splice(stage_number, 1);
		if($("#dish_stages div.stage").size() == 0) {
			stage_number = 0;
			addStage();
		}
		else {
			for (var i = stage_number; i < $("#dish_stages div.stage").size(); i++) {
				var stage = $("#dish_stages div.stage:eq(" + i + ")");
				stage.find("h2").text(numberingStageHeader(i) + " этап").end().find("div.description").find("h5:eq(0)").html('Описание ' + numberingStage(i) + ' этапа<span class="no_text">?</span>').end().end().find("div.ingredient").find("h5").html('Ингредиенты ' + numberingStage(i) + ' этапа<span class="scales"><img src="/images/icons/scales.gif" width="12" height="12" alt="Таблица мер"><span class="hint"><span>Таблица мер</span></span></span>').closest("div.ingredient").find("input[name*='id']").attr({name:"ingredients_" + i + "_id[]"}).siblings("input[name*='number']").attr({name:"ingredients_" + i + "_number[]"});
			}
		}
	}
	else {
		$("#dish_stages div.stage:eq(" + stage_number + ") div.delete_icon").removeClass("confirm");
	}
}

//открытие формы выбора ингредиентов - формирование списка групп
/*function createStageGroupList() {
	var ingredientsGroupDiv = document.getElementById("stage_ingredients_group")
	clearElement(ingredientsGroupDiv);
	var numbering = numberingStage(stage_number);
	
	//text
	var groupText = document.createTextNode("Выберите ингредиенты для " + numbering + " этапа готовки");
	ingredientsGroupDiv.appendChild(groupText);
	
	//ul
	var groupUl = document.createElement("UL");
	ingredientsGroupDiv.appendChild(groupUl);
	var liArray = new Array();//массив со всеми li групп ингредиентов
	for (var i = 0; i < ingredientArray[1].length; i++) {
		//li
		var groupLi = document.createElement("LI");
		liArray.push(groupLi);
		groupUl.appendChild(groupLi);
		//a
		var groupLiAnchor = document.createElement("A");
		groupLi.appendChild(groupLiAnchor);
		groupLiAnchor.setAttribute("href", "#");
		var groupLiAnchorOnClickString = "showStageIngredients(" + i + "); return false;";
		groupLiAnchor.onclick=new Function(groupLiAnchorOnClickString);
		groupLiAnchor.appendChild(document.createTextNode(ingredientArray[1][i]));
		//span
		var groupLiSpan = document.createElement("SPAN");
		groupLiSpan.className = "name"
		groupLi.appendChild(groupLiSpan);
		groupLiSpan.appendChild(document.createTextNode(ingredientArray[1][i]));
	}
	
	//если вызываем окно с ингредиентами повторно для данного этапа
	if (stagesIngredientsArray[stage_number] && stagesIngredientsArray[stage_number].length != 0) {
		for (var i = 0; i < stagesIngredientsArray[stage_number][0].length; i++) {
			showStageSubList(liArray[stagesIngredientsArray[stage_number][0][i]], true, stagesIngredientsArray[stage_number][0][i], stagesIngredientsArray[stage_number][1][i]);
		}
	}
	//устанавливаем высоту плавающего фрейма
	$("#top_layer iframe").attr({height:"" + ingredientsGroupDiv.offsetHeight + 40});
}

//заполнение поля со списком ингредиентов (верхний слой)
function showStageIngredients(group_number) {
	var groupNumber = group_number;
	var ingredientsGroupDiv = document.getElementById("stage_ingredients_group");
	var ingredientsListDiv = document.getElementById("stage_ingredients_list");
	clearElement(ingredientsListDiv);
	ingredientsGroupLi = findStageGroupLi(groupNumber);
	
	//Отмечаем группу ингредиентов как выбранную (убираем ссылку)
	var ingredientsGroupUl = ingredientsGroupLi.parentNode;
	for (var i = 0; i < ingredientsGroupUl.childNodes.length; i++) {
		if (ingredientsGroupUl.childNodes[i].className) {
			ingredientsGroupUl.childNodes[i].className = "";
		}
	}
	ingredientsGroupLi.className = "act";
	
	//определяем какие ингредиенты уже выбраны
	for (var i = 0; i < ingredientsGroupLi.childNodes.length; i++) {
		if (ingredientsGroupLi.childNodes[i].className == "items") {
			var itemsSpan = ingredientsGroupLi.childNodes[i];
		}
	}
	var ingredientsNimbers = new Array();
	if (itemsSpan) {
		for (var j = 0; j < itemsSpan.childNodes.length; j++) {
			if (itemsSpan.childNodes[j].tagName == "UL") {
				var itemsUl = itemsSpan.childNodes[j];
				for (var i = 0; i < itemsUl.childNodes.length; i++) {
					if (itemsUl.childNodes[i].tagName == "LI" && itemsUl.childNodes[i].className != "separator") {
						ingredientsNimbers.push(itemsUl.childNodes[i].className);
					}
				}
			}
		}
	}
	
	//h2
	var h2 = document.createElement("H2");
	h2.appendChild(document.createTextNode(ingredientArray[1][group_number]));
	ingredientsListDiv.appendChild(h2);
	
	//column
	var columnDiv = document.createElement("DIV");
	columnDiv.className = "column";
	ingredientsListDiv.appendChild(columnDiv);
	
	//ul
	var columnUl = document.createElement("UL");
	columnDiv.appendChild(columnUl);
	for (var i = 0; i < ingredientArray[2][groupNumber][0].length; i++) {
		var li = document.createElement("LI");
		for (var j = 0; j < ingredientsNimbers.length; j++) {
			if (ingredientsNimbers[j] == i) {li.className = "selected";
			}
		}
		var liAnchor = document.createElement("A");
		liAnchor.setAttribute("href", "#");
		var liAnchorOnClickString = "selectStageIngredient(this, " +  groupNumber + ", " + i + "); return false;"
		liAnchor.onclick=new Function(liAnchorOnClickString);
		liAnchor.appendChild(document.createTextNode(ingredientArray[2][groupNumber][1][i]));
		li.appendChild(liAnchor);
		columnUl.appendChild(li);
		if (columnDiv.offsetHeight > (ingredientsGroupDiv.offsetHeight - 37)) {
			columnUl.removeChild(li);
			//column
			var columnDiv = document.createElement("DIV");
			columnDiv.className = "column";
			ingredientsListDiv.appendChild(columnDiv);
			//ul
			var columnUl = document.createElement("UL");
			columnDiv.appendChild(columnUl);
			columnUl.appendChild(li);
		}
	}
}*/

//находим родительский пункт в списке групп ингредиентов
function findStageGroupLi(groupNumber) {
	var ingredientsGroupDiv = document.getElementById("stage_ingredients_group");
	for (var i = 0; i < ingredientsGroupDiv.childNodes.length; i++) {
		if (ingredientsGroupDiv.childNodes[i].tagName == "UL") {
			var groupUl = ingredientsGroupDiv.childNodes[i];
			var groupLiArray = new Array();
			for (var j = 0; j < groupUl.childNodes.length; j++) {
				if (groupUl.childNodes[j].tagName == "LI") {
					groupLiArray.push(groupUl.childNodes[j]);
				}
			}
		}
	}
	var ingredientsGroupLi = groupLiArray[groupNumber];
	return ingredientsGroupLi;
}

function selectStageIngredient(anchor_element, group_number, ingredient_number) {
	var anchorElement = anchor_element;
	var groupNumber = group_number;
	var ingredientNumber = ingredient_number;
	addRemoveClass(anchorElement.parentNode, "selected");
	
	ingredientsGroupLi = findStageGroupLi(groupNumber);
	
	if (anchorElement.parentNode.className.search("selected") != -1) {var addInr = true;
	}
	else {var addInr = false;
	}
	
	showStageSubList(ingredientsGroupLi, addInr, groupNumber, ingredientNumber);
}

/*//формирование серого списка в скобках
function showStageSubList(ingredientsGroupLi, addInr, groupNumber, ingredientNumber) {
	//проверяем поставлена галочка или снята
	for (var i = 0; i < ingredientsGroupLi.childNodes.length; i++) {
		if (ingredientsGroupLi.childNodes[i].className == "items") {
			var itemsSpan = ingredientsGroupLi.childNodes[i];
		}
	}
	if (addInr == true) {
		//есть ли items
		if (itemsSpan) {
			for (var j = 0; j < itemsSpan.childNodes.length; j++) {
				if (itemsSpan.childNodes[j].tagName == "UL") {
					var itemsUl = itemsSpan.childNodes[j];
					var itemsLi = document.createElement("LI");
					itemsLi.className = "separator";
					var liText = document.createTextNode(", ");
					itemsLi.appendChild(liText);
					itemsUl.appendChild(itemsLi);
				}
			}
		}
		else {
			var itemsSpan = document.createElement("SPAN");
			itemsSpan.className = "items";
			ingredientsGroupLi.appendChild(itemsSpan);
			var leftBracket = document.createTextNode("(");
			itemsSpan.appendChild(leftBracket);
			var itemsUl = document.createElement("UL");
			itemsSpan.appendChild(itemsUl);
			var rightBracket = document.createTextNode(")");
			itemsSpan.appendChild(rightBracket);
		}
		var itemsLi = document.createElement("LI");
		itemsLi.className = ingredientNumber;
		var liText = document.createTextNode(ingredientArray[2][groupNumber][1][ingredientNumber]);
		itemsLi.appendChild(liText);
		itemsUl.appendChild(itemsLi);
	}
	else {
		for (var i = 0; i < itemsSpan.childNodes.length; i++) {
			if (itemsSpan.childNodes[i].tagName == "UL") {
				var itemsUl = itemsSpan.childNodes[i];
				var ingredientsLiArray = new Array();
				for (var j = 0; j < itemsUl.childNodes.length; j++) {
					if (itemsUl.childNodes[j].tagName == "LI") {
						ingredientsLiArray.push(itemsUl.childNodes[j]);
					}
				}
			}
		}
		if (ingredientsLiArray.length == 1) {ingredientsGroupLi.removeChild(itemsSpan);
		}
		else {
			for (var i = 0; i < ingredientsLiArray.length; i++) {
				if (ingredientsLiArray[i].className == ingredientNumber) {
					itemsUl.removeChild(ingredientsLiArray[i]);
					if (i != 0) {itemsUl.removeChild(ingredientsLiArray[i-1]);
					}
					else {itemsUl.removeChild(ingredientsLiArray[i+1]);
					}
				}
			}
		}
	}
}*/

//формирование массива ингредиентов этапа для вывода в форме со списком ингредиентов
function addStageIngredients() {
	var stageNumber = stage_number;//порядковый номер этапа, начиная с 0
	var curStageIng = new Array();
	curStageIng[0] = new Array();//groups
	curStageIng[1] = new Array();//items
	var trIng = new Array();
	$("#i_have_list").find("tr").each(function() {
		trIng.push($(this).attr("class"));
	});
	for (var t = 0; t < trIng.length; t++) {
		for (var i = 0; i < ingredientArray[0].length; i++) {
			for (var j = 0; j < ingredientArray[2][i][0].length; j++) {
				if (trIng[t] == ingredientArray[2][i][0][j]) {
					curStageIng[0].push(i);
					curStageIng[1].push(j);
				}
			}
		}
	}
	
	var ingredientDiv = $("#dish_stages div.stage:eq(" + stage_number + ") div.ingredient");
	var ingredientList = $(ingredientDiv).children("div.stage_ing_list");
	var chooseDiv = $(ingredientDiv).children("div.add_ingredient");
	var scalesDiv = $(ingredientDiv).find("span.scales");
	
	var ingredientListItems = $(ingredientList).find("div.item");
	
	$(ingredientList).empty();
	
	if (trIng.length != 0) {
		var flag;
		for (var i = 0; i < trIng.length; i++) {
			flag = 0;
			for (var j = 0; j < ingredientListItems.length; j++) {
				if (trIng[i] == $(ingredientListItems[j]).find("input[name*='id']").attr("value")) {
					$(ingredientList).append(ingredientListItems[j]);
					flag = 1;
				}
			}
			if (flag == 0) {
				$(ingredientList).append(stageIngredient(stageNumber, ingredientArray[2][curStageIng[0][i]][1][curStageIng[1][i]], trIng[i], ingredientArray[2][curStageIng[0][i]][2][curStageIng[1][i]]));
				
			}
		}
		if (trIng.length == 1) {$(ingredientList).append(stageIngredient(stageNumber)).append(stageIngredient(stageNumber));
		}
		else if (trIng.length == 2) {$(ingredientList).append(stageIngredient(stageNumber));
		}
		
	}
	else {
		$(ingredientList).append(stageIngredient(stageNumber)).append(stageIngredient(stageNumber)).append(stageIngredient(stageNumber));
		
	}
	
	hideStageIngredientsLayer();
}

function stageIngredient(stageNumber, siName, siID, siUnit) {
	if(siName && siID && siUnit) {
		var lItem =  $('<div class="item"><div class="search_list"><ul class="search_list"></ul></div><input type="text" value="' + siName + '" class="text smartsearch"><span class="search_list_icon"><span title="Весь список ингредиентов"></span></span><input type="hidden" value="" class="click_field"><input type="hidden" name="ingredients_' + stageNumber + '_id[]" value="' + siID + '"><input type="text" name="ingredients_' + stageNumber + '_number[]" value="" class="text unit" style="display:inline;"><span class="unit" style="display:inline;">' + siUnit + '</span><span class="no_text">?</span><a href="#" class="delete_icon" title="Удалить ингредиент"></a>');
	}
	else {
		var lItem =  $('<div class="item"><div class="search_list"><ul class="search_list"></ul></div><input type="text" value="" class="text smartsearch"><span class="search_list_icon"><span title="Весь список ингредиентов"></span></span><input type="hidden" value="" class="click_field"><input type="hidden" name="ingredients_' + stageNumber + '_id[]" value=""><input type="text" name="ingredients_' + stageNumber + '_number[]" value="" class="text unit"><span class="unit"></span><span class="no_text">?</span><a href="#" class="delete_icon" title="Удалить ингредиент"></a>');
	}
	$(lItem).find("input.text").keypress(function(e){
		if(e.which == 13){
			if($(this).hasClass("unit")) {
				if($(this).attr("value")!="") {
					if($(this).closest("div.item").next("div.item").size()!=0) {
						$(this).closest("div.item").next("div.item").find("input.smartsearch").focus();
					}
					else {
						$(this).closest("div.ingredient").find("div.add_ingredient").children("a").click();
					}
				}
			}
			else {
				$(this).siblings("div.search_list").children("ul").css({display:"none"}).empty();
				//checkUniqueness(this);
				showUnitField(this);
			}
			return false;
		}
	});
	return lItem;
}

function deleteIngredient(deleteImgObject) {
	/*while($(deleteImgObject).closest("div.stage_ing_list").children("div.item").size() < 4) {
		$(deleteImgObject).closest("div.stage_ing_list").append(stageIngredient($("#dish_stages div.stage").index($(deleteImgObject).closest("div.stage"))));
	}*/
	$(deleteImgObject).parent().remove();
}

function showStageIngredientsLayer(topIcon) {
	showHideLayer('top_layer');
	$("#search_helper").addClass("stage_helper").css({top:$(window).scrollTop()}).slideDown("middle");
	$("#search_helper div.body div.search_blocks").css({display:"none"});
	$("#h_ingredients").css({display:"block"});
	$("#i_have_ingredients_list div.column ul").empty();
	$("#i_have_ingredients_list h2").empty();
	//table
	var table = $("#i_have_list div.bg table");
	$(table).empty();
	if(topIcon != 10) {
		$("#dish_stages div.stage:eq(" + stage_number + ") div.ingredient div.item").each(function() {
			if($(this).find("input[name*='id']").attr("value") != "") {
				$(table).append('<tr class="' + $(this).find("input[name*='id']").attr("value") + '"><td><span>' + $(this).find("input.text").attr("value") + '</span></td><td class="icon"><a href="#" class="delete" title="Удалить ингредиент"></td></tr>');
			}
		});
		$(table).find("a.delete").hover(function() {
			$(this).addClass("hover");
		}, function() {
			$(this).removeClass("hover");
		}).click(function() {
			var id = this.parentNode.parentNode.className;
			$("#i_have_ingredients_list").find("li." + id).removeClass("selected");
			$(this).parent().parent().remove();
			if($("#i_have_list div.bg table tr").length == 0) {
				$("#i_have_dash").css({display:"block"});
			}
			return false;
		});
	}
	if ($(table).find("tr").length == 0) {$("#i_have_dash").css({display:"block"});
	}
	else {$("#i_have_dash").css({display:"none"});
	}
	createGroupList(topIcon);
	//createStageGroupList();
}
function hideStageIngredientsLayer() {
	showHideLayer('top_layer');
	$("#search_helper").slideUp("middle");
}

var sendStageFormFlag = 0;//флаг для отслеживания можно ли отправлять форму
var elementStageNumber = 0;//номер первого элемента, в котором обнаружена ошибка. Для прокрутки страницы к этому элементу
var firstStageElement = "";
function checkStageForm() {
	var form = $("#add_recipe_form");
	sendStageFormFlag = 0;
	elementStageNumber = 0;
	firstStageElement = ""
	$("#add_recipe_form span.no_text").each(function() {
		$(this).closest("div.form_field").find("input.text:visible").each(function() {
			if(this.value == "") {
				if($(this).attr("type") == "file") {
					if($(this).closest("div.form_field").children("div.file_name").size() == 0) {
						sendStageFormFlag = 1;
						$(this).closest("div.form_field").addClass("attention");
						if(firstStageElement == "") {firstStageElement = this;
						}
					}
					else {$(this).closest("div.item").removeClass("attention");
					}
				}
				else {
					sendStageFormFlag = 1;
					$(this).closest("div.form_field").addClass("attention");
					if(firstStageElement == "") {firstStageElement = this;
					}
				}
			}
			else {$(this).closest("div.form_field").removeClass("attention");
			}
		});
		$(this).closest("div.item").find("input.unit:visible").each(function() {
			if(this.value == "") {
				if($(this).siblings("input.smartsearch").attr("value") != "") {
					sendStageFormFlag = 1;
					$(this).closest("div.item").addClass("attention");
					if(firstStageElement == "") {firstStageElement = this;
					}
				}
			}
			else {$(this).closest("div.item").removeClass("attention");
			}
		});
		$(this).closest("div.form_field").find("select").each(function() {
			if($(this).find(":selected").attr("value") == "") {
				sendStageFormFlag = 1;
				$(this).closest("div.form_field").addClass("attention");
				if(firstStageElement == "") {firstStageElement = this;
				}
			}
			else {$(this).closest("div.form_field").removeClass("attention");
			}
		});
		$(this).closest("div.form_field").find("textarea").each(function() {
			if($(this).attr("value") == "" && $(this).text() == "" && $(this).html() == "") {
				sendStageFormFlag = 1;
				$(this).closest("div.form_field").addClass("attention");
				if(firstStageElement == "") {firstStageElement = this;
				}
			}
			else {$(this).closest("div.form_field").removeClass("attention");
			}
		});
	});
	if (sendStageFormFlag == 0) {
		form.submit();
	}
	else {
		//$.scrollTo(firstStageElement, 500);
		$(window).scrollTop($(firstStageElement).offset().top - 100);
	}
	return false;
}
function imgDisplay (visibility_value,form_element,flag_value) {
	var visibilityValue = visibility_value;
	var formElement = form_element;
	var attentionClass = "attention";
	if (formElement.parentNode.tagName == "DIV") {
		var formFieldDiv = formElement.parentNode;
		for (var i = 0; i < formFieldDiv.childNodes.length; i++) {
			if (formFieldDiv.childNodes[i].tagName == "H5") {
				for (var j = 0; j < formFieldDiv.childNodes[i].childNodes.length; j++) {
					if (formFieldDiv.childNodes[i].childNodes[j].className == "no_text") {
						if (visibilityValue == "visible" && formFieldDiv.childNodes[i].className.search(attentionClass) == -1) {
							formFieldDiv.childNodes[i].className = attentionClass;
						}
						if (visibilityValue == "hidden" && formFieldDiv.childNodes[i].className.search(attentionClass) != -1) {
							formFieldDiv.childNodes[i].className = "";
						}
						if(flag_value){//присваиваем флагу необходимое значение
							sendStageFormFlag = flag_value;
						}
					}
				}
			}
		}
	}
	if (formElement.parentNode.parentNode.tagName == "LI") {
		var liElement = formElement.parentNode.parentNode;
		for (var j = 0; j < liElement.childNodes.length; j++) {
			if (liElement.childNodes[j].tagName == "SPAN" && liElement.childNodes[j].className.search("input_block") != -1) {
				var inputBlockDiv = liElement.childNodes[j];
				for (var a = 0; a < inputBlockDiv.childNodes.length; a++) {
					if (inputBlockDiv.childNodes[a].className == "no_text") {
						if (visibilityValue == "visible" && liElement.className.search(attentionClass) == -1) {
							$(liElement).addClass(attentionClass);
						}
						if (visibilityValue == "hidden" && liElement.className.search(attentionClass) != -1) {
							$(liElement).removeClass(attentionClass);
						}
						if(flag_value){//присваиваем флагу необходимое значение
							sendStageFormFlag = flag_value;
						}
					}
				}
			}
		}
	}
}

//удаление фотографии
function deleteStageImage(a_object, img_id) {
	if (a_object.className.search("fir") == -1) {
		$(a_object).addClass("confirm");
		if (confirm("Удалить изображение?")) {
			window.location.href = window.location + "?id=" + img_id;
		}
		else {$(a_object).removeClass("confirm");
		}
	}
	else  {
		deleteConfirm = 1;
		$(a_object).addClass("confirm");
		if (confirm("Удалить изображение?")) {
			window.location.href = window.location + "?id=" + img_id;
		}
		else {
			$(a_object).removeClass("confirm");
			deleteConfirm = 0;
		}
	}
}