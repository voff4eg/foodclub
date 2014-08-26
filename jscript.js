$(document).ready(function(){
	WindowOnLoad();
	$("#cooking_list ul li a").click(function(){
		var href = $(this).attr("href");
		href = href.substr(1);
		document.getElementById(href).style.display = "block";
		var preItem = $(this).parents("ul").children(".act");
		var preHref = $(preItem).children("a").attr("href");
		if (preHref) {
			preHref = preHref.substr(1);
			document.getElementById(preHref).style.display = "none";
			$(preItem).removeClass("act");
		}
		$(this).parent().addClass("act");
		return false;
	});
});

        
function WindowOnLoad() {
	var fileName = $("<div class='new_file_name'>").css({display:"none", backgroud:"url(/images/icons.gif)"});	
	var bb = $("<div class='browse_button'>").append($("<input type='button' value='Обзор'>"));
	var bl = $("<div class='blocker'>");
	$("div.input_file").children("input.text").attr({value:""}).addClass("customFile");
	$("div.input_file").children("input.text").change(function() {
		HandleChanges(this);
	});
	$("div.input_file").append($(bb)).append($(bl)).append($(fileName));
}
function HandleChanges(input_object) {
	var fileName = $(input_object).parent().children("div.new_file_name");
	file = $(input_object).attr("value");
	reWin = /.*\\(.*)/;
	var fileTitle = file.replace(reWin, "$1"); //выдираем название файла
	reUnix = /.*\/(.*)/;
	fileTitle = fileTitle.replace(reUnix, "$1"); //выдираем название файла
	$(fileName).text(fileTitle);
	
	var RegExExt =/.*\.(.*)/;
	var ext = fileTitle.replace(RegExExt, "$1");//и его расширение
	
	var pos;
	if (ext){
		switch (ext.toLowerCase()) {
			case 'doc': pos = '0'; break;
			case 'bmp': pos = '16'; break;                       
			case 'jpg': pos = '32'; break;
			case 'jpeg': pos = '32'; break;
			case 'png': pos = '48'; break;
			case 'gif': pos = '64'; break;
			case 'psd': pos = '80'; break;
			case 'mp3': pos = '96'; break;
			case 'wav': pos = '96'; break;
			case 'ogg': pos = '96'; break;
			case 'avi': pos = '112'; break;
			case 'wmv': pos = '112'; break;
			case 'flv': pos = '112'; break;
			case 'pdf': pos = '128'; break;
			case 'exe': pos = '144'; break;
			case 'txt': pos = '160'; break;
			default: pos = '176'; break;
		}
		$(fileName).css({display:"block", background:"url(/images/icons.png) no-repeat 0 -"+pos+"px"});
	}
}

function getyScroll() {
	var yScroll = 0;
	
	if (window.innerHeight && window.scrollMaxY) {
		yScroll = window.innerHeight + window.scrollMaxY;
		
		var deff = document.documentElement;
		var hff = (deff&&deff.clientHeight) || document.body.clientHeight || window.innerHeight || self.innerHeight;
	
		yScroll -= (window.innerHeight - hff);
	} 
	else if (document.body.scrollHeight > document.body.offsetHeight) { // all but Explorer Mac
		yScroll = document.body.scrollHeight;
	} 
	else { // Explorer Mac...would also work in Explorer 6 Strict, Mozilla and Safari
		yScroll = document.body.offsetHeight;
	}
	return yScroll;
}

function showHideLayer(layer_id) {
	var layer = document.getElementById(layer_id);
	if (layer.style.display == "none") {
		layer.style.display = "block";
		if (layer_id == "top_layer") {
			layer.style.height = getyScroll() + "px";
			for (var i = 0; i < layer.childNodes.length; i++) {
				if (layer.childNodes[i].tagName == "IFRAME") {
					layer.childNodes[i].style.height = getyScroll() + "px";
					layer.childNodes[i].style.width = "100%";
				}
			}
		}
		else {
			var layerHeight = layer.offsetHeight;
			if (document.documentElement.clientHeight > layer.offsetHeight) {
				layer.style.top = parseInt(document.documentElement.clientHeight)/2 + parseInt(document.documentElement.scrollTop) + "px";
			}
			else {
				layer.style.top = parseInt(layer.offsetHeight)/2 + parseInt(document.documentElement.scrollTop) + 20 + "px";
			}			
		}
	}
	else {layer.style.display = "none";
	}
}

//выбор кухни
function chooseDishType() {
	var selectElement = document.forms["dish"].elements["cooking"];
	var cookingId = selectElement.value;
	for (var i = 0; i < cookingArray[0].length; i++) {
		if (cookingArray[0][i] == cookingId) {
			var cookingNumber = i;
		}
	}
	fillDishTypeSelect(cookingNumber);
}

function fillDishTypeSelect(cookingNumber) {
	var selectElement = document.forms["dish"].elements["dish_type"];
	clearElement(selectElement);
	if (!cookingNumber) {var cookingNumber = 0;
	}
	for (var i = 0; i < cookingArray[1][cookingNumber][0].length; i++) {
		var optionElement = document.createElement("OPTION");
		optionElement.setAttribute("value", cookingArray[1][cookingNumber][0][i]);
		optionElement.appendChild(document.createTextNode(cookingArray[1][cookingNumber][1][i]));
		selectElement.appendChild(optionElement);
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
function addStage() {
	//вычисляем порядковый номер добавляемого этапа
	var dishStagesDiv = document.getElementById("dish_stages");
	for (var i = 0; i < dishStagesDiv.childNodes.length; i++) {
		if (dishStagesDiv.childNodes[i].className == "body") {
			var bodyDiv = dishStagesDiv.childNodes[i];
			var stageDivCounter = 0;
			for (var j = 0; j < bodyDiv.childNodes.length; j++) {
				if (bodyDiv.childNodes[j].className == "stage") {stageDivCounter++;
				}
				if (bodyDiv.childNodes[j].className == "button") {var addStageButtonDiv = bodyDiv.childNodes[j];
				}
			}
		}
	}
	var newStageNumber = stageDivCounter;
	
	//создаём этап
	//stage
	var stageDiv = document.createElement("DIV");
	stageDiv.className = "stage";
	bodyDiv.removeChild(addStageButtonDiv);
	bodyDiv.appendChild(stageDiv);
	bodyDiv.appendChild(addStageButtonDiv);
	//delete_icon
	var deleteIconDiv = document.createElement("DIV");
	deleteIconDiv.className = "delete_icon";
	stageDiv.appendChild(deleteIconDiv);
	var deleteDiv = document.createElement("DIV");
	deleteIconDiv.appendChild(deleteDiv);
	deleteDiv.setAttribute("title", "Удалить этап");
	var deleteDivOnClickString = "stage_number = " + newStageNumber + "; deleteStage();"
	deleteDiv.onclick=new Function(deleteDivOnClickString);
	//description
	var descriptionDiv = document.createElement("DIV");
	descriptionDiv.className = "description";
	stageDiv.appendChild(descriptionDiv);
	//form_field
	var formFieldDiv = document.createElement("DIV");
	formFieldDiv.className = "form_field";
	descriptionDiv.appendChild(formFieldDiv);
	//h5
	var fieldHeader = document.createElement("H5");
	var fieldHeaderText = document.createTextNode("Описание " + numberingStage(newStageNumber) + " этапа");
	var noTextSpan = document.createElement("SPAN");
	noTextSpan.className = "no_text";
	var noText = document.createTextNode("?");
	noTextSpan.appendChild(noText);
	fieldHeader.appendChild(fieldHeaderText);
	fieldHeader.appendChild(noTextSpan);
	formFieldDiv.appendChild(fieldHeader);
	//textarea
	var textareaElemen = document.createElement("TEXTAREA");
	formFieldDiv.appendChild(textareaElemen);
	textareaElemen.setAttribute("name", "stage_description[]");
	textareaElemen.setAttribute("cols", "");
	textareaElemen.setAttribute("rows", "");
	//form_field
	var formFieldDiv = document.createElement("DIV");
	formFieldDiv.className = "form_field";
	descriptionDiv.appendChild(formFieldDiv);
	//h5
	var fieldHeader = document.createElement("H5");
	var fieldHeaderText = document.createTextNode("Фото этапа (600х400 px)");
	fieldHeader.appendChild(fieldHeaderText);
	formFieldDiv.appendChild(fieldHeader);
	
	//input
	var inputFile = $("<div class='input_file'>");
	if (navigator.appName == "Netscape" || navigator.appName == "Opera") {
		inputElement = document.createElement("INPUT");
		inputElement.setAttribute("type", "file");
		inputElement.setAttribute("name", "photo[]");
		inputElement.className = "text";
	}
	else {var inputElement = document.createElement('<input type="file" name="photo[]" class="text">');
	}
	$(inputFile).append(inputElement);
	var fileName = $("<div class='new_file_name'>").css({display:"none", backgroud:"url(/images/icons.gif)"});
	var bb = $("<div class='browse_button'>").append($("<input type='button' value='Обзор'>"));
	var bl = $("<div class='blocker'>");
	$(inputFile).children("input.text").attr({value:""}).addClass("customFile");
	$(inputFile).children("input.text").change(function() {
		HandleChanges(this);
	});
	$(inputFile).append($(bb)).append($(bl)).append($(fileName));
	
	$(formFieldDiv).append($(inputFile));

	//ingredient
	var ingredientDiv = document.createElement("DIV");
	ingredientDiv.className = "ingredient";
	stageDiv.appendChild(ingredientDiv);
	//choose
	var chooseDiv = document.createElement("DIV");
	chooseDiv.className = "choose";
	ingredientDiv.appendChild(chooseDiv);
	var chooseAnchor = document.createElement("A");
	chooseDiv.appendChild(chooseAnchor);
	chooseAnchor.setAttribute("href", "#");
	$(chooseAnchor).click(function() {
		stage_number = newStageNumber;
		showIngredientsLayer();
		return false;
	});
	chooseAnchorText = document.createTextNode("Выбрать ингредиенты");
	chooseAnchor.appendChild(chooseAnchorText);
	//clear
	var clearDiv = document.createElement("DIV");
	clearDiv.className = "clear";
	stageDiv.appendChild(clearDiv);
}

//удаление этапа
function deleteStage() {
	var dishStagesDiv = document.getElementById("dish_stages");
	for (var i = 0; i < dishStagesDiv.childNodes.length; i++) {
		if (dishStagesDiv.childNodes[i].className == "body") {
			var bodyDiv = dishStagesDiv.childNodes[i];
			var stageDivArray = new Array();
			for (var j = 0; j < bodyDiv.childNodes.length; j++) {
				if (bodyDiv.childNodes[j].className == "stage") {
					stageDivArray.push(bodyDiv.childNodes[j]);
				}
			}
		}
	}
	for (var i = 0; i < stageDivArray.length; i++) {
		if (i == stage_number) {
			for (var j = 0; j < stageDivArray[i].childNodes.length; j++) {
				if (stageDivArray[i].childNodes[j].className == "delete_icon") {
					var stageDeleteIcon = stageDivArray[i].childNodes[j];
					addRemoveClass(stageDeleteIcon, "attention");
				}
			}
		}
	}
	if (confirm("Удалить этап?")) {
		for (var i = 0; i < stageDivArray.length; i++) {
			if (i == stage_number) {
				bodyDiv.removeChild(stageDivArray[i]);
				stageDivArray.splice(i, 1);
				stagesIngredientsArray.splice(i, 1);
			}
		}
		for (var i = stage_number; i < stageDivArray.length; i++) {
			for (var j = 0; j < stageDivArray[i].childNodes.length; j++) {
				if (stageDivArray[i].childNodes[j].className == "delete_icon") {
					var deleteIconDiv = stageDivArray[i].childNodes[j];
					clearElement(deleteIconDiv);
					//delete
					var deleteDiv = document.createElement("DIV");
					deleteIconDiv.appendChild(deleteDiv);
					deleteDiv.setAttribute("title", "Удалить этап");
					var deleteDivOnClickString = "stage_number = " + i + "; deleteStage();"
					deleteDiv.onclick=new Function(deleteDivOnClickString);
				}
				if (stageDivArray[i].childNodes[j].className == "description") {
					var descriptionDiv = stageDivArray[i].childNodes[j];
					var formFieldArray = new Array();
					for (var a = 0; a < descriptionDiv.childNodes.length; a++) {
						if (descriptionDiv.childNodes[a].className == "form_field") {formFieldArray.push(descriptionDiv.childNodes[a]);
						}
					}
					//description h5
					for (var a = 0; a < formFieldArray[0].childNodes.length; a++) {
						if (formFieldArray[0].childNodes[a].tagName == "H5") {
							var descriptionHeader = formFieldArray[0].childNodes[a];
							for (var b = 0; b < descriptionHeader.childNodes.length; b++) {
								if (descriptionHeader.childNodes[b].tagName == "SPAN") {
										noTextSpan = descriptionHeader.childNodes[b];
										clearElement(descriptionHeader);
										var headerText = document.createTextNode("Описание " + numberingStage(i) + " этапа");
										descriptionHeader.appendChild(headerText);
										descriptionHeader.appendChild(noTextSpan);
								}
							}
						}
					}
				}
				if (stageDivArray[i].childNodes[j].className == "ingredient") {
					var ingredientsDiv = stageDivArray[i].childNodes[j];
					for (var a = 0; a < ingredientsDiv.childNodes.length; a++) {
						//h5
						if (ingredientsDiv.childNodes[a].tagName == "H5") {
							var ingredientsHeader = ingredientsDiv.childNodes[a];
							clearElement(ingredientsHeader);
							var headerText = document.createTextNode("Ингредиенты " + numberingStage(i) + " этапа");
							ingredientsHeader.appendChild(headerText);
						}
						//ul
						if (ingredientsDiv.childNodes[a].tagName == "UL") {
							var ingredientsList = ingredientsDiv.childNodes[a];
							for (var b = 0; b < ingredientsList.childNodes.length; b++) {
								if (ingredientsList.childNodes[b].tagName == "LI") {
									var ingredientLI = ingredientsList.childNodes[b];
									for (var c = 0; c < ingredientLI.childNodes.length; c++) {
										if (ingredientLI.childNodes[c].tagName == "INPUT" && ingredientLI.childNodes[c].type == "text") {
											//number
											ingredientLI.childNodes[c].removeAttribute("name");
											ingredientLI.childNodes[c].setAttribute("name", "ingredients_" + i + "_number[]");
										}
										if (ingredientLI.childNodes[c].tagName == "INPUT" && ingredientLI.childNodes[c].type == "hidden") {
											//id
											ingredientLI.childNodes[c].removeAttribute("name");
											ingredientLI.childNodes[c].setAttribute("name", "ingredients_" + i + "_id[]");
										}
									}
								}
							}
						}
						//choose
						if (ingredientsDiv.childNodes[a].className == "choose") {
							var chooseDiv = ingredientsDiv.childNodes[a];
							clearElement(chooseDiv);
							var chooseAnchor = document.createElement("A");
							chooseDiv.appendChild(chooseAnchor);
							chooseAnchor.setAttribute("href", "#");
							var chooseAnchorOnClickString = "stage_number = " + i + "; showIngredientsLayer(); return false;"
							chooseAnchor.onclick=new Function(chooseAnchorOnClickString);
							chooseAnchor.appendChild(document.createTextNode("Выберите ингредиенты"));
						}
					}
				}
			}
		}
	}
	else {addRemoveClass(stageDeleteIcon, "attention");
	}
}

//открытие формы выбора ингредиентов - формирование списка групп
function createGroupList() {
	var ingredientsGroupDiv = document.getElementById("ingredients_group")
	clearElement(ingredientsGroupDiv);
	var numbering = numberingStage(stage_number);
	
	//text
	var groupText = document.createTextNode("Выберите ингредиенты для " + numbering + " этапа готовки");
	ingredientsGroupDiv.appendChild(groupText);
	
	//ul
	var groupUl = document.createElement("UL");
	ingredientsGroupDiv.appendChild(groupUl);
	var liArray = new Array();//массив со всеми li групп ингредиентов
	for (var i = 0; i < ingredientsArray[1].length; i++) {
		//li
		var groupLi = document.createElement("LI");
		liArray.push(groupLi);
		groupUl.appendChild(groupLi);
		//a
		var groupLiAnchor = document.createElement("A");
		groupLi.appendChild(groupLiAnchor);
		groupLiAnchor.setAttribute("href", "#");
		var groupLiAnchorOnClickString = "showIngredients(" + i + "); return false;";
		groupLiAnchor.onclick=new Function(groupLiAnchorOnClickString);
		groupLiAnchor.appendChild(document.createTextNode(ingredientsArray[1][i]));
		//span
		var groupLiSpan = document.createElement("SPAN");
		groupLiSpan.className = "name"
		groupLi.appendChild(groupLiSpan);
		groupLiSpan.appendChild(document.createTextNode(ingredientsArray[1][i]));
	}
	
	//если вызываем окно с ингредиентами повторно для данного этапа
	if (stagesIngredientsArray[stage_number] && stagesIngredientsArray[stage_number].length != 0) {
		for (var i = 0; i < stagesIngredientsArray[stage_number][0].length; i++) {
			showSubList(liArray[stagesIngredientsArray[stage_number][0][i]], true, stagesIngredientsArray[stage_number][0][i], stagesIngredientsArray[stage_number][1][i]);
		}
	}
	//устанавливаем высоту плавающего фрейма
	$("#top_layer iframe").attr({height:"" + ingredientsGroupDiv.offsetHeight + 40});
}

//заполнение поля со списком ингредиентов (верхний слой)
function showIngredients(group_number) {
	var groupNumber = group_number;
	var ingredientsGroupDiv = document.getElementById("ingredients_group");
	var ingredientsListDiv = document.getElementById("ingredients_list");
	clearElement(ingredientsListDiv);
	ingredientsGroupLi = findGroupLi(groupNumber);
	
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
	h2.appendChild(document.createTextNode(ingredientsArray[1][group_number]));
	ingredientsListDiv.appendChild(h2);
	
	//column
	var columnDiv = document.createElement("DIV");
	columnDiv.className = "column";
	ingredientsListDiv.appendChild(columnDiv);
	
	//ul
	var columnUl = document.createElement("UL");
	columnDiv.appendChild(columnUl);
	for (var i = 0; i < ingredientsArray[2][groupNumber][0].length; i++) {
		var li = document.createElement("LI");
		for (var j = 0; j < ingredientsNimbers.length; j++) {
			if (ingredientsNimbers[j] == i) {li.className = "selected";
			}
		}
		var liAnchor = document.createElement("A");
		liAnchor.setAttribute("href", "#");
		var liAnchorOnClickString = "selectIngredient(this, " +  groupNumber + ", " + i + "); return false;"
		liAnchor.onclick=new Function(liAnchorOnClickString);
		liAnchor.appendChild(document.createTextNode(ingredientsArray[2][groupNumber][1][i]));
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
}

function addRemoveClass(object, new_class) {
	var newClass = new_class;
	var objectClass = new String(object.className);
	if (objectClass.search(newClass) != -1) {
		objectClass = objectClass.split(newClass).join("");
	}
	else {
		objectClass = objectClass + " " + newClass;
	}
	//удаляем двойные пробелы
	objectClass = objectClass.split("  ").join(" ");
	//удаляем пробелы в коне и начале строки
	if (objectClass.slice(0,1) == " ") {
		objectClass = objectClass.slice(1, objectClass.length);
	}
	if (objectClass.slice(objectClass.length-1) == " ") {
		objectClass = objectClass.slice(0, objectClass.length-1);
	}
	object.className = objectClass;
}

//находим родительский пункт в списке групп ингредиентов
function findGroupLi(groupNumber) {
	var ingredientsGroupDiv = document.getElementById("ingredients_group");
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

function selectIngredient(anchor_element, group_number, ingredient_number) {
	var anchorElement = anchor_element;
	var groupNumber = group_number;
	var ingredientNumber = ingredient_number;
	addRemoveClass(anchorElement.parentNode, "selected");
	
	ingredientsGroupLi = findGroupLi(groupNumber);
	
	if (anchorElement.parentNode.className.search("selected") != -1) {var addInr = true;
	}
	else {var addInr = false;
	}
	
	showSubList(ingredientsGroupLi, addInr, groupNumber, ingredientNumber);
}

//формирование серого списка в скобках
function showSubList(ingredientsGroupLi, addInr, groupNumber, ingredientNumber) {
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
		var liText = document.createTextNode(ingredientsArray[2][groupNumber][1][ingredientNumber]);
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
}

//формирование массива ингредиентов этапа для вывода в форме со списком ингредиентов
var stagesIngredientsArray = new Array();//массив для хранения списков ингредиентов для всех этапов
function addIngredients() {
	var stageNumber = stage_number;//порядковый номер этапа, начиная с 0
	stagesIngredientsArray[stage_number] = new Array();
	stagesIngredientsArray[stage_number][0] = new Array();//номера группы
	stagesIngredientsArray[stage_number][1] = new Array();//номера ингредиентов
	
	//формирование массива ингредиентов, поступивших из формы
	var ingredientsGroupDiv = document.getElementById("ingredients_group");
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
	for (var i = 0; i < groupLiArray.length; i++) {
		for (var j = 0; j < groupLiArray[i].childNodes.length; j++) {
			if (groupLiArray[i].childNodes[j].className == "items") {
				var itemsSpan = groupLiArray[i].childNodes[j];
				for (var k = 0; k < itemsSpan.childNodes.length; k++) {
					if (itemsSpan.childNodes[k].tagName == "UL") {
						for (var n = 0; n < itemsSpan.childNodes[k].childNodes.length; n++) {
							if (itemsSpan.childNodes[k].childNodes[n].tagName == "LI" && itemsSpan.childNodes[k].childNodes[n].className != "separator") {
								stagesIngredientsArray[stage_number][0].push(i);
								stagesIngredientsArray[stage_number][1].push(itemsSpan.childNodes[k].childNodes[n].className);
							}
						}
					}
				}
			}
		}
	}
	
	//добавление новых полей/удаление ненужных полей
	var dishStagesDiv = document.getElementById("dish_stages");
	var stageDivArray = new Array();//дивы-этапы
	for (var i = 0; i < dishStagesDiv.childNodes.length; i++) {
		if (dishStagesDiv.childNodes[i].className == "body") {
			for (var j = 0; j < dishStagesDiv.childNodes[i].childNodes.length; j++) {
				if (dishStagesDiv.childNodes[i].childNodes[j].className == "stage") {
					stageDivArray.push(dishStagesDiv.childNodes[i].childNodes[j]);
				}
			}
		}
	}
	for (var i = 0; i < stageDivArray[stageNumber].childNodes.length; i++) {
		if (stageDivArray[stageNumber].childNodes[i].className == "ingredient") {
			var ingredientDiv = stageDivArray[stageNumber].childNodes[i];
		}
	}
	var ingredientHeader = 0;
	var ingredientList = 0;
	var length = ingredientDiv.childNodes.length;
	for (var i = 0; i < length; i++) {
		var flag = 0;
		if (ingredientDiv.childNodes[0].tagName == "H5") {
			ingredientHeader = ingredientDiv.childNodes[0];
			flag = 1;
		}
		if (ingredientDiv.childNodes[0].tagName == "UL") {
			ingredientList = ingredientDiv.childNodes[0];
			flag = 1;
		}
		if (ingredientDiv.childNodes[0].className == "choose") {
			var chooseDiv = ingredientDiv.childNodes[0];
			flag = 1;
		}
		ingredientDiv.removeChild(ingredientDiv.childNodes[0]);
	}
	if (ingredientHeader == 0) {
		var h5Text = document.createTextNode("Ингредиенты " + numberingStage(stage_number) + " этапа");
		ingredientHeader = document.createElement("H5");
		ingredientHeader.appendChild(h5Text);
	}
	var ingredientListId = new Array();
	var ingredientListLi = new Array();
	if (ingredientList != 0) {
		for (var i = 0; i < ingredientList.childNodes.length; i++) {
			if (ingredientList.childNodes[i].tagName == "LI") {
				var li = ingredientList.childNodes[i];
				ingredientListLi.push(li);
				for (var j = 0; j < li.childNodes.length; j++) {
					if (li.childNodes[j].tagName == "INPUT" && li.childNodes[j].type == "hidden") {
						ingredientListId.push(li.childNodes[j].value);
					}
				}
			}
		}
	}
	else {ingredientList = document.createElement("UL");
	}
	
	//удаление ненужных полей
	var inputsArrayi = new Array();
	var inputsArrayj = new Array();
	for (var i = 0; i < ingredientListId.length; i++) {
		for (var j = 0; j < stagesIngredientsArray[stage_number][0].length; j++) {
			if (ingredientListId[i] == ingredientsArray[2][stagesIngredientsArray[stage_number][0][j]][0][stagesIngredientsArray[stage_number][1][j]]) {
				inputsArrayi[i] = "1";
				inputsArrayj[j] = "1";
			}
		}
	}
	//формирование полей ингредиентов
	if (ingredientList.childNodes.length != 0) {
		var ingredientListLi = new Array();
		for (var i = 0; i < ingredientList.childNodes.length; i++) {
			if (ingredientList.childNodes[i].tagName == "LI") {
				ingredientListLi.push(ingredientList.childNodes[i]);
			}
		}
		for (var i = 0; i < ingredientListLi.length; i++) {
			if (inputsArrayi[i] != "1") {
				ingredientList.removeChild(ingredientListLi[i]);
			}
		}
	}
	for (var j = 0; j < stagesIngredientsArray[stage_number][0].length; j++) {
		if (inputsArrayj[j] != "1") {
			var liElement = document.createElement("LI");
			ingredientList.appendChild(liElement);
			//name
			var nameSpan = document.createElement("SPAN");
			nameSpan.className = "name";
			var nameSpanText = document.createTextNode(ingredientsArray[2][stagesIngredientsArray[stage_number][0][j]][1][stagesIngredientsArray[stage_number][1][j]]);
			nameSpan.appendChild(nameSpanText);
			liElement.appendChild(nameSpan);
			//input
			var inputElement = document.createElement("INPUT");
			inputElement.setAttribute("type", "text");
			inputElement.setAttribute("value", "");
			inputElement.setAttribute("name", "ingredients_" + stage_number + "_number[]");
			inputElement.className = "text";
			liElement.appendChild(inputElement);
			inputElementOnKeyUp = new String("checkNumberField(this);");
			inputElement.onkeyup=new Function(inputElementOnKeyUp);
			//hidden
			var inputElement = document.createElement("INPUT");
			inputElement.setAttribute("type", "hidden");
			inputElement.setAttribute("value", ingredientsArray[2][stagesIngredientsArray[stage_number][0][j]][0][stagesIngredientsArray[stage_number][1][j]]);
			inputElement.setAttribute("name", "ingredients_" + stage_number + "_id[]");
			liElement.appendChild(inputElement);
			//unit
			var unitSpan = document.createElement("SPAN");
			unitSpan.className = "unit";
			var unitSpanText = document.createTextNode(ingredientsArray[2][stagesIngredientsArray[stage_number][0][j]][2][stagesIngredientsArray[stage_number][1][j]]);
			unitSpan.appendChild(unitSpanText);
			liElement.appendChild(unitSpan);
			//no_text
			var noTextSpan = document.createElement("SPAN");
			noTextSpan.className = "no_text";
			var noText = document.createTextNode("?");
			noTextSpan.appendChild(noText);
			liElement.appendChild(noTextSpan);
		}
	}
	if (stagesIngredientsArray[stage_number][0].length != 0) {
		ingredientDiv.appendChild(ingredientHeader);
		ingredientDiv.appendChild(ingredientList);
	}
	ingredientDiv.appendChild(chooseDiv);
	
	hideIngredientsLayer();
}

function showIngredientsLayer() {
	showHideLayer('top_layer');
	showHideLayer ('ingredients_list_layer');
	createGroupList();
}
function hideIngredientsLayer() {
	showHideLayer('top_layer');
	showHideLayer ('ingredients_list_layer');
	clearElement(document.getElementById("ingredients_group"));
	clearElement(document.getElementById("ingredients_list"));
}

var sendFormFlag = 0;//флаг для отслеживания можно ли отправлять форму
function checkForm(form_name) {//функция получает имя формы, в которой надо проверить поля на заполненность
	var form = document.forms[form_name];
	sendFormFlag = 0;
	for (var i = 0; i < form.elements.length; i++) {
		if (form.elements[i].type != "hidden") {//проверяем только нескрытые поля
			if (form.elements[i].value == "") {//находим все пустые поля
				if (form.elements[i].disabled) {//для полей, в которые нельзя вводить текст картинки-предупреждения остаются скрытыми
					imgDisplay("hidden",form.elements[i]);
				}
				else {imgDisplay("visible",form.elements[i],1);
				}
			}
			else {
				if (form.elements[i].name.search('EMAIL') != -1) {//если поле заполнено, но должно содержать e-mail, проверяем содержимое на соответствие правилам написания адреса e-mail
					var mail = form.elements[i].value;
					var mailRegex = /^([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,})$/i;
					if(!mail.match(mailRegex)){
						errorFlag = 1;
						imgDisplay("visible",form.elements[i],1);
					}
					 else {
						imgDisplay("hidden",form.elements[i]);
					};
				
				}
				else {imgDisplay("hidden",form.elements[i]);
				}
			}
		}
	}
	if (sendFormFlag == 0) {//если флаг остался равным нулю (все поля заполнены и e-mail соответствует правилам) отправляем форму
		form.submit();
	}
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
							sendFormFlag = flag_value;
						}
					}
				}
			}
		}
	}
	if (formElement.parentNode.tagName == "LI") {
		var liElement = formElement.parentNode;
		for (var j = 0; j < liElement.childNodes.length; j++) {
			if (liElement.childNodes[j].className == "no_text") {
				if (visibilityValue == "visible" && liElement.className.search(attentionClass) == -1) {
					liElement.className = attentionClass;
				}
				if (visibilityValue == "hidden" && liElement.className.search(attentionClass) != -1) {
					liElement.className = "";
				}
				if(flag_value){//присваиваем флагу необходимое значение
					sendFormFlag = flag_value;
				}
			}
		}
	}
}

//проверка цифровых полей
function checkNumberField (input) {
	var inputValue = input.value;
	if (inputValue != "") {
		var numberRegex = /([\d.,/])$/i;
		if(!inputValue.match(numberRegex)){
			input.value = inputValue.slice(0,inputValue.length-1);
			checkNumberField(input);
		}
	}
}

//удаление фотографии
function deleteStageImage(img_object, img_id) {
	addRemoveClass(img_object, "attention");
	if (confirm("Удалить изображение?")) {
		window.location.href = window.location + "?id=" + img_id;
	}
	else {addRemoveClass(img_object, "attention");
	}
}