var smartsearchIdArray = new Array();
var smartsearchUnitArray = new Array();
var smartsearchArray = new Array();

$(function() {
	
	if (window.ingredientArray) {
		for (var i = 0; i < ingredientArray[0].length; i++) {
			smartsearchIdArray = smartsearchIdArray.concat(ingredientArray[2][i][0]);
			smartsearchUnitArray = smartsearchUnitArray.concat(ingredientArray[2][i][2]);
			smartsearchArray = smartsearchArray.concat(ingredientArray[2][i][1]);
		}
		ingredientSearchArray = new Array(smartsearchIdArray, smartsearchArray);
	}
});

var inputValue;
var itemNum = 5;//number of items in each list
function topSsNavUp(inputObject) {
	var $input = $(inputObject);
	var $prevLi = $("#top_search_list li.hover");

	if ($prevLi.text() == "") {
		inputValue = $input.val();
		var $currentLi = $("#top_search_list li:last");
		if (isHeading($currentLi)) {
			skipHeading();
			return false;
		}
		hightlightLi($currentLi);
		if(isSearchAll($currentLi)) {
			$input.val(inputValue);
		}
		else {
			$input.val($currentLi.text());
		}
	}
	else {
		$prevLi.removeClass("hover");
		var $currentLi = $prevLi.prev("li");
		if ($currentLi.hasClass("heading")) {
			$currentLi = $currentLi.prev("li");
		}
		if ($currentLi.text() != "") {
			hightlightLi($currentLi);
			
			if(isSearchAll($currentLi)) {
				$input.val(inputValue);
			}
			else {
				$input.val($currentLi.text());
			}
		}
		else {
			$input.val(inputValue);
		}
	}
	
	function isHeading($elem) {
		if($elem.hasClass("heading")) return true;
		return false;
	}
	
	function isSearchAll($elem) {
		if($elem.hasClass("search_all")) return true;
		return false;
	}
	
	function skipHeading() {
		topSsNavUp(inputObject);
	}
	
	function hightlightLi($elem) {
		$elem.addClass("hover");
	}
}


function topSsNavDown(inputObject) {
	var preLi = $("#top_search_list").children("ul").children("li.hover");
	if ($(preLi).text() == "") {
		inputValue = $(inputObject).attr("value");
		var nowLi = $("#top_search_list").children("ul").children("li:first");
		if ($(nowLi).attr("class") && $(nowLi).attr("class").search("heading") != -1) {
			nowLi = $(nowLi).next("li");
		}
		$(nowLi).addClass("hover");
		if($(nowLi).attr("class").search("search_all") != -1) {
			$(inputObject).attr({value:inputValue});
		}
		else {$(inputObject).attr({value:$(nowLi).text()});
		}
	}
	else {
		$(preLi).removeClass("hover");
		var nowLi = $(preLi).next("li");
		if ($(nowLi).attr("class") && $(nowLi).attr("class").search("heading") != -1) {
			nowLi = $(nowLi).next("li");
		}
		if ($(nowLi).text() != "") {
			$(nowLi).addClass("hover");
			if($(nowLi).attr("class").search("search_all") != -1) {
				$(inputObject).attr({value:inputValue});
			}
			else {$(inputObject).attr({value:$(nowLi).text()});
			}
		}
		else {
			$(inputObject).attr({value:inputValue});
		}
	}
}

function topSsFunction(inputObject) {
	cyrillic($(inputObject));

	function cyrillic($input) {
		if(/^[\u0400-\u04ff\s]*$/.test($input.val())) return;
		
		$input.val($input.val().substring(0, $input.val().length-1));
		cyrillic($input);
	}
	
	$("#top_search_list").children("ul").empty();
	if($(inputObject).attr("value") != "") {
		var searchString = new String($(inputObject).attr("value")).toLowerCase().replace("ё", "е").replace("й", "и").split(" ");
		var ingredientSortedArray = buildSsList(inputObject, searchString, ingredientSearchArray);
		var recipeSortedArray = buildSsList(inputObject, searchString, recipeArray);
		var cuisineSortedArray = buildSsList(inputObject, searchString, cuisineArray);
		var dishTypeSortedArray = buildSsList(inputObject, searchString, dishTypeArray);//с ё и й
		
		var searchFlag = 0;
		
		//ingredients
		if (ingredientSortedArray.length != 0) {
			searchFlag = addSsListItem(ingredientSortedArray, searchString, searchFlag, "Ингредиенты");
		}
		
		//recipes
		if (recipeSortedArray.length != 0) {
			searchFlag = addSsListItem(recipeSortedArray, searchString, searchFlag, "Рецепты");
		}
		
		//cuisines
		if (cuisineSortedArray.length != 0) {
			searchFlag = addSsListItem(cuisineSortedArray, searchString, searchFlag, "Кухни");
		}
		
		//dish types
		if (dishTypeSortedArray.length != 0) {
			searchFlag = addSsListItem(dishTypeSortedArray, searchString, searchFlag, "Типы блюд");
		}
		
		$("#top_search_list").children("ul").append('<li class="search_all"><a href="/search/' + $(inputObject).attr("value") + '/">Все рецепты с «<span>' + $(inputObject).attr("value") + '</span>»</a></li>');
		$("#top_search_list").children("ul").append('<li class="search_all"><a href="/posts_search/?q=' + $(inputObject).attr("value") + '">Все записи с «<span>' + $(inputObject).attr("value") + '</span>»</a></li>');
		
		$("#top_search_list").children("ul").children("li").click(function() {
			$(inputObject).attr({value:""}).focus();
			$(this).parent().css({display:"none"}).empty();
			
			var liId;
			for (var r = 0; r < searchArray[1].length; r++) {
				if ($(this).text() == searchArray[1][r]) {
					liId = searchArray[0][r];
				}
			}
		})/*.hover(function() {
			$(this).addClass("hover");
		}, function() {
			$(this).removeClass("hover");
		});*/
		$("#top_search_list").children("ul").css({display:"block"});
		/*if (searchFlag == 1) {
			$("#top_search_list").children("ul").css({display:"block"});
		}
		else {
			$("#top_search_list").children("ul").css({display:"none"});
		}*/
	}
	else {
		$("#top_search_list").children("ul").css({display:"none"});
	}
}

function addSsListItem(array, searchString, searchFlag, dataType) {
	if (array.length > itemNum) {
		$("#top_search_list").children("ul").append('<li class="heading">' + dataType + ' <div><div>' + itemNum + ' из ' + array.length + '</div></div></li>');
	}
	else {$("#top_search_list").children("ul").append('<li class="heading">' + dataType + '</li>');
	}
	if (array.length != 0) {
		searchFlag = 1;
	}
	switch (dataType) {
		case "Ингредиенты": var curArray = ingredientSearchArray; break;
		case "Рецепты": var curArray = recipeArray; break;
		case "Кухни": var curArray = cuisineArray; break;
		case "Типы блюд": var curArray = dishTypeArray; break;//с ё и й
	}
	
	var strLength;
	var strIndex;
	var itemId = "";
	var arrayI;
	//var arrayIstr;
	for (var i = 0; i < itemNum; i++) {
		for (var t = 0; t < curArray[0].length; t++) {
			if (curArray[1][t].toLowerCase() == array[i].toLowerCase()) {
				itemId = curArray[0][t];
			}
		}
		switch (dataType) {
			case "Ингредиенты": var href = '/search_service/?id=' + itemId; break;
			case "Рецепты": var href = '/detail/' + itemId + '/'; break;
			case "Кухни": var href = '/all/?k=' + itemId; break;
			case "Типы блюд": var href = '/all/?d=' + itemId; break;
		}
		arrayI = array[i].toLowerCase().replace("ё", "е").replace("й", "и");
		for (var e = 0; e < searchString.length; e++) {
			strLength = searchString[e].length;
			strIndex = arrayI.indexOf(searchString[e]);
			arrayIstr = "";
			for (var k = 0; k < searchString[e].length; k++) {
				arrayIstr += " ";
			}
			arrayI = arrayI.split(searchString[e])[0] + "<span>" + arrayIstr + "</span>" + arrayI.split(searchString[e])[1];
			array[i] = array[i].slice(0, strIndex) + "<span>" + array[i].slice(strIndex, strIndex+strLength) + "</span>" + array[i].slice(strIndex+strLength);
		}
		$("#top_search_list").children("ul").append('<li><a href="../recipe-search-new/' + href + '">' + array[i] + '</a></li>');
	}
	return searchFlag;
}

function buildSsList(inputObject, searchString, searchArray) {
	var smartsearchArrayLower = new Array();
	for (var i = 0; i < searchArray[1].length; i++) {
		smartsearchArrayLower.push(String(searchArray[1][i]).toLowerCase().replace("ё", "е").replace("й", "и"));
	}
	var indexOfArray = new Array();
	var indexOfValue;
	for (var i = 0; i < smartsearchArrayLower.length; i++) {
		//if ($(inputObject).attr("value").toLowerCase() != smartsearchArrayLower[i]) {
			indexOfValue = 100;
			for (var e = 0; e < searchString.length; e++) {
				indexOfValue = Math.min(indexOfValue, smartsearchArrayLower[i].indexOf(searchString[e]));
				smartsearchArrayLower[i] = smartsearchArrayLower[i].split(searchString[e])[0] + smartsearchArrayLower[i].split(searchString[e])[1];
			}
			if (indexOfValue != -1) {
				if(!indexOfArray[indexOfValue]) {
					indexOfArray[indexOfValue] = new Array();
				}
				indexOfArray[indexOfValue].push(searchArray[1][i]);//с ё и й
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
	return sortedArray;
}

function topSsPressEnter() {
	var liHover = $("#top_search_list").find("li.hover");
	
	if ($(liHover).text()) {
		window.location = $(liHover).children("a").attr("href");
	}
	else {
		window.location = "/search/" + $("#recipe_search_field").attr("value") + "/";
	}
}