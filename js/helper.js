function showList(listArray, chosenArray, header) {//list of ingredients
	var itemsNumbering = Math.ceil(listArray.length/3);
	var ul_1 = $("<ul>").appendTo($("#search_list_layer div.relative div.padding div.column:eq(0)"));
	for (var i = 0; i < itemsNumbering; i++ ) {
		if (listArray[i]) {
			var li = $('<li rel="' + i + '">').appendTo($(ul_1));
			var anc = $('<a href="#">' + listArray[i] + '</a>').appendTo($(li));
			
			for (var j = 0; j < chosenArray.length; j++) {
				if (chosenArray[j] == i) {
					$(li).addClass("selected");
				}
			}
			$(anc).click(function() {
				$(this).parent().toggleClass("selected");
				return false;
			});
		}
	}
	
	var ul_2 = $("<ul>").appendTo($("#search_list_layer div.relative div.padding div.column:eq(1)"));
	for (var i = itemsNumbering; i < itemsNumbering*2; i++ ) {
		if (listArray[i]) {
			var li = $('<li rel="' + i + '">').appendTo($(ul_2));
			var anc = $('<a href="#">' + listArray[i] + '</a>').appendTo($(li));
			
			for (var j = 0; j < chosenArray.length; j++) {
				if (chosenArray[j] == i) {
					$(li).addClass("selected");
				}
			}
			$(anc).click(function() {
				$(this).parent().toggleClass("selected");
				return false;
			});
		}
	}
	
	var ul_3 = $("<ul>").appendTo($("#search_list_layer div.relative div.padding div.column:eq(2)"));
	for (var i = itemsNumbering*2; i < listArray.length; i++ ) {
		if (listArray[i]) {
			var li = $('<li rel="' + i + '">').appendTo($(ul_3));
			var anc = $('<a href="#">' + listArray[i] + '</a>').appendTo($(li));
			
			for (var j = 0; j < chosenArray.length; j++) {
				if (chosenArray[j] == i) {
					$(li).addClass("selected");
				}
			}
			$(anc).click(function() {
				$(this).parent().toggleClass("selected");
				return false;
			});
		}
	}
	$("#search_list_layer div.relative div.padding h2").text(header);
}

//открытие формы выбора ингредиентов - формирование списка групп
function createGroupList(topIcon) {
	var groupUl = $("#i_have_ingredients_group ul").empty();
	//ul
	var liArray = new Array();//массив со всеми li групп ингредиентов
	for (var i = 0; i < ingredientArray[1].length; i++) {
		var groupLi = $('<li><a href="#" onClick="showIngredients(' + i + ', ' + topIcon + '); return false;">' + ingredientArray[1][i] + '</a><span>' + ingredientArray[1][i] + '</span></li>').appendTo($(groupUl));
		liArray.push(groupLi);
	}
	$("#i_have_ingredients_group ul li:eq(0)").addClass("act");
	showIngredients(0, topIcon);
	//если вызываем окно с ингредиентами повторно для данного этапа
	if (chosenSearchIngredient[0].length != 0) {
		for (var i = 0; i < chosenSearchIngredient[0].length; i++) {
			showSubList(liArray[chosenSearchIngredient[0][i]], true, chosenSearchIngredient[0][i], chosenSearchIngredient[1][i]);
		}
	}
}

//заполнение поля со списком ингредиентов (верхний слой)
function showIngredients(group_number, topIcon) {
	var groupNumber = group_number;
	$("#i_have_ingredients_list div.column ul").empty();
	
	$("#i_have_ingredients_group ul li").removeClass("act");
	$("#i_have_ingredients_group ul li:eq(" + groupNumber + ")").addClass("act");
		
	//h2
	$("#i_have_ingredients_list h2").text(ingredientArray[1][group_number]);
	
	//ul
	var iHaveArray = new Array();
	$("#i_have_list").find("tr").each(function() {
		iHaveArray.push($(this).attr("class"));
	});
	if (((ingredientArray[2][groupNumber][0].length*18) + 50) > $("#i_have_ingredients_group").height()) {
		if (((ingredientArray[2][groupNumber][0].length/2*18) + 50) >= $("#i_have_ingredients_group").height()) {
			var columnUl = $("#i_have_ingredients_list div.column ul:eq(0)");
			for (var i = 0; i < ingredientArray[2][groupNumber][0].length/2; i++) {
				var classId = ingredientArray[2][groupNumber][0][i];
				for (var j = 0; j < iHaveArray.length; j++) {
					if (iHaveArray[j] == classId) {
						classId += " selected";
					}
				}
				$(columnUl).append('<li class="' + classId + '"><a href="#" onClick="selectIngredient(this, ' +  groupNumber + ', ' + i + ', ' + topIcon + '); return false;">' + ingredientArray[2][groupNumber][1][i] + '</a></li>');
			}
			columnUl = $("#i_have_ingredients_list div.column ul:eq(1)");
			for (var i = Math.ceil(ingredientArray[2][groupNumber][0].length/2); i < ingredientArray[2][groupNumber][0].length; i++) {
				var classId = ingredientArray[2][groupNumber][0][i];
				for (var j = 0; j < iHaveArray.length; j++) {
					if (iHaveArray[j] == classId) {
						classId += " selected";
					}
				}
				$(columnUl).append('<li class="' + classId + '"><a href="#" onClick="selectIngredient(this, ' +  groupNumber + ', ' + i + ', ' + topIcon + '); return false;">' + ingredientArray[2][groupNumber][1][i] + '</a></li>');
			}
		}
		else {
			var columnUl = $("#i_have_ingredients_list div.column ul:eq(0)");
			for (var i = 0; i < ingredientArray[2][groupNumber][0].length; i++) {
				if ($("#i_have_ingredients_group").height() > $("#i_have_ingredients_list").height()) {
					var classId = ingredientArray[2][groupNumber][0][i];
					for (var j = 0; j < iHaveArray.length; j++) {
						if (iHaveArray[j] == classId) {
							classId += " selected";
						}
					}
					$(columnUl).append('<li class="' + classId + '"><a href="#" onClick="selectIngredient(this, ' +  groupNumber + ', ' + i + ', ' + topIcon + '); return false;">' + ingredientArray[2][groupNumber][1][i] + '</a></li>');
				}
				else {
					columnUl = $("#i_have_ingredients_list div.column ul:eq(1)");
					var classId = ingredientArray[2][groupNumber][0][i];
					for (var j = 0; j < iHaveArray.length; j++) {
						if (iHaveArray[j] == classId) {
							classId += " selected";
						}
					}
					$(columnUl).append('<li class="' + classId + '"><a href="#" onClick="selectIngredient(this, ' +  groupNumber + ', ' + i + ', ' + topIcon + '); return false;">' + ingredientArray[2][groupNumber][1][i] + '</a></li>');
				}
			}
		}
	}
	else {
		var columnUl = $("#i_have_ingredients_list div.column ul:eq(0)");
		for (var i = 0; i < ingredientArray[2][groupNumber][0].length; i++) {
			var classId = ingredientArray[2][groupNumber][0][i];
			for (var j = 0; j < iHaveArray.length; j++) {
				if (iHaveArray[j] == classId) {
					classId += " selected";
				}
			}
			$(columnUl).append('<li class="' + classId + '"><a href="#" onClick="selectIngredient(this, ' +  groupNumber + ', ' + i + ', ' + topIcon + '); return false;">' + ingredientArray[2][groupNumber][1][i] + '</a></li>');
		}
	}
	
	increaseTopLayer();
}

function selectIngredient(anchor_element, group_number, ingredient_number, topIcon) {
	if(topIcon == 10) {
		var anchorElement = anchor_element;
		$("#dish_description input.smartsearch").attr({value:$(anchorElement).text()}).closest("div.form_field").find(":hidden").attr({value:$(anchorElement).parent().attr("class")});
		hideStageIngredientsLayer();
	}
	else {
		var anchorElement = anchor_element;
		$(anchor_element).parent().toggleClass("selected");
		if (anchorElement.parentNode.className.search("selected") != -1) {
			var addInr = 1;
		}
		else {var addInr = 0;
		}
		fillIHaveTable(addInr, group_number, ingredient_number);
	}
}

//fill i-have table
function fillIHaveTable(addInr, group_number, ingredient_number) {
	if (addInr == 1) {
		var trObject = $('<tr class="' + ingredientArray[2][group_number][0][ingredient_number] + '"><td><span>' + ingredientArray[2][group_number][1][ingredient_number] + '</span></td><td class="icon"><a href="#" class="delete" title="Удалить ингредиент"></a></td></tr>');
		$(trObject).find("a.delete").hover(function() {
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
		$("#i_have_list div.bg table").append(trObject);
		$("#i_have_dash").css({display:"none"});
	}
	else {
		$("#i_have_list div.bg table").find("tr." + ingredientArray[2][group_number][0][ingredient_number]).remove();
		if($("#i_have_list div.bg table tr").length == 0) {
			$("#i_have_dash").css({display:"block"});
		}
	}
}

//формирование массива ингредиентов этапа для вывода в форме со списком ингредиентов
function addIngredients() {	
	//формирование массива ингредиентов, поступивших из формы
	var length1 = chosenSearchIngredient[0].length;
	for (var i = 0; i < length1; i++) {
		chosenSearchIngredient[0].pop();
	}
	var length2 = chosenSearchIngredient[1].length;
	for (var i = 0; i < length2; i++) {
		chosenSearchIngredient[1].pop();
	}
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
								chosenSearchIngredient[0].push(i);
								chosenSearchIngredient[1].push(itemsSpan.childNodes[k].childNodes[n].className);
							}
						}
					}
				}
			}
		}
	}
	fillSearchField();
	hideIngredientsLayer();
}

function fillSearchField() {
	//записываем выбранные ингредиенты в поле
	var inputString = new String();
	var kitchenInput = new Array();
	var dishInput = new Array();
	var ingredientInput = new Array();
	for (var i = 0; i < chosenSearchKitchen.length; i++) {
		if (inputString == "") {
			inputString = kitchenArray[1][chosenSearchKitchen[i]];
		}
		else {
			inputString += ", ";
			inputString += kitchenArray[1][chosenSearchKitchen[i]].toLowerCase();
		}
	}
	for (var i = 0; i < chosenSearchDish.length; i++) {
		if (inputString == "") {
			inputString = dishArray[1][chosenSearchDish[i]];
		}
		else {
			inputString += ", ";
			inputString += dishArray[1][chosenSearchDish[i]].toLowerCase();
		}
	}
	for (var i = 0; i < chosenSearchIngredient[0].length; i++) {
		if (inputString == "") {
			inputString = ingredientArray[2][chosenSearchIngredient[0][i]][1][chosenSearchIngredient[1][i]];
		}
		else {
			inputString += ", ";
			inputString += ingredientArray[2][chosenSearchIngredient[0][i]][1][chosenSearchIngredient[1][i]].toLowerCase();
		}
//		ingredientInput.push($('<input type="hidden" name="ingredient_id[]" value="' + ingredientArray[2][chosenSearchIngredient[0][i]][0][chosenSearchIngredient[1][i]] + '">'));
	}
	$("#recipe_search_field").attr({value:""}).attr({value:inputString});
/*	$("#recipe_search div.search_field form input:hidden").remove();
	for (var i = 0; i < ingredientInput.length; i++) {
		$("#recipe_search div.search_field form").prepend(ingredientInput[i]);
	}
	for (var i = 0; i < dishInput.length; i++) {
		$("#recipe_search div.search_field form").prepend(dishInput[i]);
	}
	for (var i = 0; i < kitchenInput.length; i++) {
		$("#recipe_search div.search_field form").prepend(kitchenInput[i]);
	}*/
}

var smartsearchInputValue;
function smartsearchNavUp(inputObject) {
	var preLi = $(inputObject).siblings("div.search_list").children("ul").children("li.hover");
	if ($(preLi).text() == "") {
		smartsearchInputValue = $(inputObject).attr("value");
		var nowLi = $(inputObject).siblings("div.search_list").children("ul").children("li:last");
		$(nowLi).addClass("hover");
		$(inputObject).attr({value:$(nowLi).text()});
	}
	else {
		$(preLi).removeClass("hover");
		var nowLi = $(preLi).prev("li");
		if ($(nowLi).text() != "") {
			$(nowLi).addClass("hover");
			$(inputObject).attr({value:$(nowLi).text()});
		}
		else {
			$(inputObject).attr({value:smartsearchInputValue});
		}
	}
}

function smartsearchNavDown(inputObject) {
	var preLi = $(inputObject).siblings("div.search_list").children("ul").children("li.hover");
	if ($(preLi).text() == "") {
		smartsearchInputValue = $(inputObject).attr("value");
		var nowLi = $(inputObject).siblings("div.search_list").children("ul").children("li:first");
		$(nowLi).addClass("hover");
		$(inputObject).attr({value:$(nowLi).text()});
	}
	else {
		$(preLi).removeClass("hover");
		var nowLi = $(preLi).next("li");
		if ($(nowLi).text() != "") {
			$(nowLi).addClass("hover");
			$(inputObject).attr({value:$(nowLi).text()});
		}
		else {
			$(inputObject).attr({value:smartsearchInputValue});
		}
	}
}

function smartsearchFunction(inputObject) {
	$(inputObject).siblings("div.search_list").children("ul").empty();
	if($(inputObject).attr("value") != "") {
		var searchString = new String($(inputObject).attr("value")).toLowerCase().split(" ");
		
		var smartsearchArrayLower = new Array();
		for (var i = 0; i < smartsearchArray.length; i++) {
			smartsearchArrayLower.push(String(smartsearchArray[i]).toLowerCase());
		}
		var indexOfArray = new Array();
		for (var i = 0; i < smartsearchArrayLower.length; i++) {
			//perfect match //if ($(inputObject).attr("value").toLowerCase() != smartsearchArrayLower[i]) {
				var indexOfValue = 100;
				for (var e = 0; e < searchString.length; e++) {
					indexOfValue = Math.min(indexOfValue, smartsearchArrayLower[i].indexOf(searchString[e]));
				}
				if (indexOfValue != -1) {
					if(!indexOfArray[indexOfValue]) {
						indexOfArray[indexOfValue] = new Array();
					}
					indexOfArray[indexOfValue].push(smartsearchArray[i]);
				}
			//}
		}
		var sortedArray = new Array();
		for (var i = 0; i < indexOfArray.length; i++) {
			if (indexOfArray[i]) {
				indexOfArray[i].sort();
				for (var j = 0; j < indexOfArray[i].length; j++) {
					sortedArray.push(indexOfArray[i][j]);
				}
			}
		}
		if (sortedArray.length != 0) {
			$(inputObject).siblings("div.search_list").children("ul").css({display:"block"});
		}
		else {
			$(inputObject).siblings("div.search_list").children("ul").css({display:"none"});
		}
		for (var i = 0; i < sortedArray.length; i++) {
			if (i < 7) {
				$(inputObject).siblings("div.search_list").children("ul").append('<li>' + sortedArray[i] + '</li>');
			}
		}
		$(inputObject).siblings("div.search_list").children("ul").children("li").hover(function() {
			$(this).addClass("hover");
			$(inputObject).siblings("input.click_field").attr({value:$(this).text()});
		}, function() {
			$(this).removeClass("hover");
		}).click(function() {
			if (this.parentNode.parentNode.parentNode.className == "item") {
				$(this).parent().css({display:"none"}).empty();
				$(inputObject).attr({value:$(inputObject).siblings("input.click_field").attr("value")}).focus();
				showUnitField(inputObject);
			}
			else if (this.parentNode.parentNode.parentNode.parentNode.className == "dish_parents") {
				$(this).parent().css({display:"none"}).empty();
				$(inputObject).attr({value:$(this).text()}).focus();
				var liId;
				for (var r = 0; r < smartsearchArray.length; r++) {
					if ($(this).text() == smartsearchArray[r]) {
						liId = smartsearchIdArray[r];
					}
				}
				$(inputObject).siblings("input[name*='id']").attr({value:liId});
			}
			else {
				$(inputObject).attr({value:""}).focus();
				$(this).parent().css({display:"none"}).empty();
				
				var liId;
				for (var r = 0; r < smartsearchArray.length; r++) {
					if ($(this).text() == smartsearchArray[r]) {
						liId = smartsearchIdArray[r];
					}
				}
				
				if (!$("#i_have_list div.bg table").find("tr." + liId).html()) {
					var trObject = $('<tr class="' + liId + '"><td><span>' + $(this).text() + '</span></td><td class="icon"><a href="#" class="delete" title="Удалить ингредиент"></a></td></tr>');
					$(trObject).find("a.delete").hover(function() {
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
					$("#i_have_list div.bg table").append(trObject);
					$("#i_have_ingredients_list").find("li." + liId).addClass("selected");
					$("#i_have_dash").css({display:"none"});
				}
			}
		});
	}
	else {
		$(inputObject).siblings("div.search_list").children("ul").css({display:"none"});
	}
}

function pressEnter() {
	var liHover = $("#helper_smartsearch").parent().find("li.hover");
	
	if ($(liHover).text()) {
		$("#helper_smartsearch").attr({value:""}).focus();
		$(this).parent().css({display:"none"}).empty();
		
		var liId;
		for (var r = 0; r < smartsearchArray.length; r++) {
			if ($(liHover).text() == smartsearchArray[r]) {
				liId = smartsearchIdArray[r];
			}
		}
		
		if (!$("#i_have_list div.bg table").find("tr." + liId).html()) {
			var trObject = $('<tr class="' + liId + '"><td><span>' + $(liHover).text() + '</span></td><td class="icon"><a href="#" class="delete" title="Удалить ингредиент"></a></td></tr>');
			$(trObject).find("a.delete").hover(function() {
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
			$("#i_have_list div.bg table").append(trObject);
			$("#i_have_ingredients_list").find("li." + liId).addClass("selected");
			$("#i_have_dash").css({display:"none"});
		} 
	}
	else {
		for (var t = 0; t < smartsearchArray.length; t++) {
			if ($("#helper_smartsearch").attr("value").toLowerCase() == smartsearchArray[t].toLowerCase()) {
				var ingName = smartsearchArray[t];
				var ingId = smartsearchIdArray[t];
				if (!$("#i_have_list div.bg table").find("tr." + ingId).html()) {
					var trObject = $('<tr class="' + ingId + '"><td><span>' + ingName + '</span></td><td class="icon"><a href="#" class="delete" title="Удалить ингредиент"></a></td></tr>');
					$(trObject).find("a.delete").hover(function() {
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
					});
					$("#i_have_list div.bg table").append(trObject);
					$("#i_have_ingredients_list").find("li." + ingId).addClass("selected");
					$("#i_have_dash").css({display:"none"});
				} 
				$("#helper_smartsearch").attr({value:""}).focus();
				$(this).parent().css({display:"none"}).empty();
			}
		}
	}
}