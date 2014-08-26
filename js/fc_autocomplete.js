/*
	$("#search").fc_autocomplete({
		arrays:[
			{
				array:"fc_data.recipes[].name",
				href:"http://www.foodclub.ru/detail/\%id\%/",
				target:"_blank",
				id:"fc_data.recipes[].id"
			}
		],
		extra_links:[
			{
				string:"Все рецепты с «\%substring\%»",
				href:"http://www.foodclub.ru/search/\%substring\%/",
				target:"_blank"
			}
		],
		num:2,
		total:""
	});
*/

(function($){
	var defaults = {
		arrays:[],
		num:5,
		onSelect:function($this, resultUi, li, arrayObject) {//method to execute when item is selected
			resultUi.hide().empty();
		}
	};
	
	if(!Array.prototype.indexOf) {
		Array.prototype.indexOf = function(string) {
			for(var i=0; i<this.length; i++) {
				if(this[i] == string) {
					var result = i;
					break;
				}
			}
			return result;
		}
	}
	
	$.fn.fc_autocomplete = function(params) {
		var options = $.extend({}, defaults, params);
		
		if(!document.getElementById("fc_autocomplete_result")) {
			$("body").append('<div id="fc_autocomplete_result"><ul></ul></div>');
		}
		var resultBlock=$("#fc_autocomplete_result"), resultUi=$("#fc_autocomplete_result ul");
		
		$(this).each(function() {
			//html structure
			var $this = $(this).bind("blur", function() {
				setTimeout(function() {resultUi.hide().empty()}, 150);
			});
			$this.curValue = "";
			
			$this.unbind("keydown").unbind("keyup").unbind("keypress").bind("keyup", function(e) {
				methods.keyup(e);
			});
			var methods = {
				keyup:function(e) {
					switch (e.which) {
						case 38:methods.navUp(); break;
						case 40:methods.navDown(); break;
						case 13:methods.pressEnter(); break;
						default:
							$this.curValue = $this.attr("value");
							methods.autocomplete();
					}
				},
				navUp:function() {//make one function
					var preLi = resultUi.children("li.over"), curLi;
					if(preLi.is("li")) {
						preLi.removeClass("over");
						curLi = $(preLi).prev("li");
						if (curLi.hasClass("heading")) {
							curLi = curLi.prev("li");
						}
						if (curLi.is("li")) {
							curLi.addClass("over");
							if(curLi.hasClass("extra_link")) {
								$this.val($this.curValue);
							}
							else {curLi.children("a").is("a") ? $this.val(curLi.children("a").text()) : $this.val(curLi.text());
							}
						}
						else {
							$this.val($this.curValue);
						}
					}
					else {
						//$this.curValue = $this.attr("value");
						curLi = resultUi.children("li:last");
						if (curLi.hasClass("heading")) {
							curLi=curLi.prev("li");
						}
						curLi.addClass("over");
						if(curLi.hasClass("extra_link")) {
							$this.val($this.curValue);
						}
						else {curLi.children("a").is("a") ? $this.val(curLi.children("a").text()) : $this.val(curLi.text());
						}
					}
				},
				navDown:function() {
					var preLi = resultUi.children("li.over"), curLi;
					if(preLi.is("li")) {
						preLi.removeClass("over");
						curLi = $(preLi).next("li");
						if (curLi.hasClass("heading")) {
							curLi = curLi.next("li");
						}
						if (curLi.is("li")) {
							curLi.addClass("over");
							if(curLi.hasClass("extra_link")) {
								$this.val($this.curValue);
							}
							else {curLi.children("a").is("a") ? $this.val(curLi.children("a").text()) : $this.val(curLi.text());
							}
						}
						else {
							$this.val($this.curValue);
						}
					}
					else {
						//$this.curValue = $this.attr("value");
						curLi = resultUi.children("li:first");
						if (curLi.hasClass("heading")) {
							curLi=curLi.next("li");
						}
						curLi.addClass("over");
						if(curLi.hasClass("extra_link")) {
							$this.val($this.curValue);
						}
						else {curLi.children("a").is("a") ? $this.val(curLi.children("a").text()) : $this.val(curLi.text());
						}
					}
				},
				pressEnter:function() {
					var li = "undefined";
					if(resultUi.find("li.over").is("li")) {
						li=resultUi.find("li.over");
						if(li.children("a").is("a")) {
							$("recipe_photo").text(li.children("a").attr("target"));
							window.open(li.children("a").attr("href"), li.children("a").attr("target"), "")
							//window.location = li.children("a").attr("href");
							return;
						}
					};
					options.onSelect($this, resultUi, li, options);
				},
				autocomplete:function() {
					resultUi.hide().empty();
					if($.trim($this.val()) != "") {
						var sortedArray, total, target;

						for(var i=0; i<options.arrays.length; i++) {
							sortedArray = methods.resultArray(options.arrays[i]);
							if((options.arrays[i].heading || ((options.total || options.total=="") && sortedArray.length>options.num)) && sortedArray.length != 0) {
								total="";
								if((options.total || options.total=="") && sortedArray.length>options.num) {
									total = '<div class="total">' + options.total + " " + options.num + ' из ' + sortedArray.length + '</div>';
								}
								if(options.arrays[i].heading) {
									total += options.arrays[i].heading;
								}
								resultUi.append('<li class="heading">' + total + '</li>');
							}
							for (var j = 0; j < Math.min(sortedArray.length, options.num); j++) {
								if(options.arrays[i].href) {
									target=' target="_self"';
									if(options.arrays[i].target) {target=' target="'+options.arrays[i].target+'"';}
									sortedArray[j] = '<a href="' + options.arrays[i].href[options.arrays[i].array.indexOf(sortedArray[j])].replace(/\%substring\%/g, $this.curValue) + '"'+target+'>' + sortedArray[j] + '</a>';}
									resultUi.append('<li>' + sortedArray[j] + '</li>');
							}
						}
						if(options.extra_links) {
							for(var i=0; i<options.extra_links.length; i++) {
								target=' target="_self"';
								if(options.extra_links[i].target) {target=' target="'+options.arrays[i].target+'"';}
								resultUi.append('<li class="extra_link"><a href="' + options.extra_links[i].href.replace(/\%substring\%/g, $this.curValue) + '"'+target+'>' + options.extra_links[i].string.replace(/\%substring\%/g, $this.curValue) + '</a></li>');
							}
						}
						if (resultUi.children("li").size() != 0) {
							resultBlock.css({top:$this.offset().top+$this.outerHeight()+"px", left:$this.offset().left+"px"});
							
							resultUi.width($this.outerWidth()).show().children("li").hover(function() {
								$(this).addClass("hover");
							}, function() {
								$(this).removeClass("hover");
							});
							resultUi.children("li").each(function() {
								if(!$(this).children("a").is("a")) {
									$(this).click(function() {
										options.onSelect($this, resultUi, $(this), options);
									});
								}
							});
						}
						else {
							resultUi.hide();
						}
					}
					else {
						resultUi.hide();
					}
				},
				resultArray:function(array) {
					var searchString = $.trim($this.attr("value")).toLowerCase().split(" "), indexOfArray = [], index, sortedArray = [], string, word;
					var dots = function(w) {
						var str = "";
						for(var a=0; a<w.length; a++) {
							str += ".";
						}
						return str;
					};
					for (var i = 0; i < array.toLower.length; i++) {
						index = 1000;
						string = array.toLower[i];
						for(var j=0; j<searchString.length; j++) {
							index = Math.min(string.indexOf(searchString[j]), index);
							if(index != -1) {
								word = new RegExp("[a-zа-я0-9%]*"+ searchString[j] + "[a-zа-я0-9%]*").exec(string)[0];
								string = string.replace(word, dots(word));
							}
							else {
								break;
							}
						}
						if (index != -1) {
							if(!indexOfArray[index]) {
								indexOfArray[index] = [];
							}
							indexOfArray[index].push(array.array[i]);
						}
					}
					for(var i=0; i<indexOfArray.length; i++) {
						if(indexOfArray[i]) {
							sortedArray = sortedArray.concat(indexOfArray[i].sort());
						}
					}
					return sortedArray;
				}
			};
		});
		
		var parseFunc = {
			arrayToLower:function(arrays) {
				for (var i = 0; i < arrays.length; i++) {
					arrays[i].toLower = [];
					for (var j = 0; j < arrays[i].array.length; j++) {
						arrays[i].toLower.push(arrays[i].array[j].toLowerCase());
					}
				}
			},
			parseArrays:function(string) {
				var regExp = /\[\]/g;
				var counter = -1;
				function replacer() {
					counter++;
					return "[var" + counter + "]";
				};
				return {string:string.replace(regExp,replacer), num:counter};
			},
			createArray:function(num, string) {
				var subs, statement="array.push("+string+")";
				for(var i=num; i>=0; i--) {
					subs = string.substring(0,string.indexOf("[var"+i+"]"));
					statement = "for(var var"+i+"=0; var"+i+"<"+subs+".length; var"+i+"++) {"+statement+"}";
				}
				statement += "return array;"
				return new Function("var array=[];" + statement);
			},
			parseString:function(obj, key) {
				var str, strFunc;
				str = obj[key];
				obj[key]=[];
				for(var i=0; i<obj.array.length; i++) {
					strFunc = function() {
						if(arguments[1] == "substring") {return "\%substring\%";
						}
						else {return obj[arguments[1]][i];}
					};
					obj[key][i]=str.replace(/\%(\w+)\%/g,strFunc);
				}
			},
			parseData:function() {
				for(var i=0; i<options.arrays.length; i++) {
					var type = typeof(options.arrays[i]);
					if(type == "string" || type == "number" || type == "boolean") {
						options.arrays = [{array:options.arrays}];
						break;
					}
					else if(type == "object") {
						for(var key in options.arrays[i]) {
							if(typeof options.arrays[i][key] == "string" && /[\w\.]+\[\]/i.test(options.arrays[i][key])) {//check reg exp
								options.arrays[i][key] = parseFunc.parseArrays(options.arrays[i][key]);
								options.arrays[i][key] = parseFunc.createArray(options.arrays[i][key].num, options.arrays[i][key].string)();
							}
						}
						for(var key in options.arrays[i]) {
							if(typeof options.arrays[i][key] == "string" && /\%\w+\%/i.test(options.arrays[i][key])) {//check reg exp
								parseFunc.parseString(options.arrays[i],key);
							}
						}
					}
				}
			}
		};
		
		//create data
		parseFunc.parseData();
		
		//create lower case versions for each array
		parseFunc.arrayToLower(options.arrays);
		
		return this;
	};
})(jQuery)

//console.profile();
//console.profileEnd();