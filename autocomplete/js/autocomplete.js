function RecipeSearch(id, options) {
	
	var self = this;
	this._$elem = $("#" + id);
	this._$clearElem = this._$elem.find("div.clear-field a");
	this._$fieldElem = this._$elem.find(":text");
	this._$suggBlock = this._$elem.find(".recipe-autocomplete-suggestions");
	this._suggItems = [];
	
	init();
	
	function init() {
		self._$fieldElem
			.keyup(function(e) {
				makeSuggestions(e);
			})
			.keydown(function(e) {
				keyNavigation(e);
			})
			.focus(function() {
				makeSuggestions();
			})
			.blur(function() {
				removeSuggestions();
			});
		
		self._$clearElem.click(function() {
			clearField();
			return false;
		});	
	}
	
	function clearField() {
		self._$fieldElem.val("").focus();
	}
	
	function keyNavigation(e) {
		switch(e.which) {
			case 38:
				navigateUp();
				break;
			case 40:
				navigateDown();
				break;
			case 13:
				sendData();
				break;
		}
	}
	
	function navigateUp() {}
	
	function navigateDown() {}
	
	function sendData() {}
	
	function makeSuggestions(e) {
		if(e && (e.which == 38 || e.which == 40 || e.which == 13)) return;
		self._suggItems = getItems();
		
		removeSuggestions();
		if(self._suggItems.length == 0) return;
		sortItems();
		showSuggestionList();
	}
	
	function removeSuggestions() {
		self._$suggBlock.find("ul.list").remove();
	}
	
	function getItems() {
		if(self._$fieldElem.val() == "") return [];
		
		var suggestionsArray = [];
		
		for(var i = 0; i < options.array.length; i++) {
			var name = options.array[i].name;
			var value = self._$fieldElem.val().replace("\\", "\\\\");			
			
			var regExp = new RegExp(value, 'i');
			
			if(regExp.test(name)) {
				suggestionsArray.push(options.array[i]);
			}
		}
		
		return suggestionsArray;
	}
	
	function sortItems() {
		self._suggItems.sort(sortFunction);
	}
	
	function sortFunction(a, b) {
		var searchStr = self._$fieldElem.val();
		
		var name1 = a.name.toLowerCase();
		var name2 = b.name.toLowerCase();
		
		//sort by substring position
		if(name1.indexOf(searchStr) < name2.indexOf(searchStr)) {
			return -1;
		}
		if(name1.indexOf(searchStr) > name2.indexOf(searchStr)) {
			return 1;
		}
		
		//sort by alphabet
		if(name1 < name2) {
			return -1;
		}
		if(name1 > name2) {
			return 1;
		}
		
		return 0;
	}
	
	function showSuggestionList() {
		var template = document.getElementById('recipe-list-template').innerHTML;
		var compiled = tmpl(template);
		
		var result = compiled(
			{
				num: 5,
				items: self._suggItems,
				title: "Рецепты"
			}
		);
		
		result = highlightSearchStr(result);
		
		var $result = $(result);
		self._$suggBlock.append($result);
	}
	
	function highlightSearchStr(html) {
		var regExp = new RegExp("(" + self._$fieldElem.val() + ")", 'ig');
		return html.replace(regExp, "<b>$1</b>");
	}
}