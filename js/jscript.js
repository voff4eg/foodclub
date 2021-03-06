var smartsearchIdArray = new Array();
var smartsearchUnitArray = new Array();
var smartsearchArray = new Array();
var ingredientSearchArray = new Array();
var allRecipesResult = [];
$(document).ready(function() {

	favoriteButtons();
	
	window.upButton = new UpButton();
	
	DoYouKnowThat("do-you-know-that");
	
	$(".recipe, .topic_item").addtocopy({htmlcopytxt: '<br>Подробнее: <a href="' + window.location.href + '">' + window.location.href + '</a>', minlen: 50, addcopyfirst: false});
	
	$.cookie("gogogo", null);
	
	$(".input_file").each(function() {
		new InputFile($(this));
	});
	
	$(".b-recipe-menu__button__type-print").click(function() {
		openPrintWindow($(this));
		return false;
	});
	
	$(document).bind("click", function() {
		$("#top_search_list").children("ul").css({display:"none"});
		$("#top_panel span.kitchen > span.submenu").slideUp("middle").siblings("a").children("span").addClass("up").removeClass("down");
		$("#top_panel div.add span.body").slideUp("middle");
		$("#filter_list:visible").slideUp("fast", function() {$("#filter_recipes .open").removeClass("open");});		
	});
	
	buttonActions();
	
	$("#comment_form textarea").resizeTextarea();
	
	new FilterRecipes();
	
	$(".b-filter").each(function() {
		new Filter($(this));
	});
	
	new FilterList();
	
	new SearchRecipeFeed();//combine with FilterList
	
	valignRecipePreviewPhoto();
	
	$(".recipe #html_code").click(function() {
		var HTMLcode=window.open('','HTMLcode','width=800, height=800,toolbar=0,scrollbars=yes,status=0,directories=0,location=0,menubar=0,resizable=0');
		var ingredientsText="", stagesText="", ingSpan = $("div.title div.needed span.ingredient"), amountTd = $("div.title div.needed td.ing_amount"), stagesDiv = $("div.stage");
		ingSpan.each(function() {
			ingredientsText += this.innerHTML +"&amp;nbsp;&amp;nbsp;&amp;nbsp;"+ amountTd[ingSpan.index(this)].innerHTML + "&lt;br /&gt;";
		});
		stagesDiv.each(function() {
			var img="", ings = $(this).find("td.ing_name"), amounts = $(this).find("td.ing_amount"), ingText = "";
			ings.each(function() {
				ingText += this.innerHTML +"&amp;nbsp;&amp;nbsp;&amp;nbsp;"+ amounts[ings.index(this)].innerHTML + "&lt;br /&gt;";
			});
			if($(this).find("img.photo").is("img")) {img='&lt;br /&gt;&lt;br /&gt;&lt;img src="http://www.foodclub.ru' + $(this).find("img.photo").attr("src") + '" /&gt';}
			stagesText += Number(stagesDiv.index(this)+1)+". "+ ingText + $(this).find("div.instruction").text() +img+ '&lt;br /&gt;&lt;br /&gt;&lt;br /&gt;'
		});
		var text = '&lt;img src="http://www.foodclub.ru'+$("img.final-photo").attr("src")+'" /&gt;&lt;br /&gt;&lt;br /&gt;'+$("div.description").text()+'&lt;br /&gt;&lt;br /&gt;&lt;a href="http://www.foodclub.ru/detail/'+$("div.hrecipe").attr("id")+'/" target="_blank"&gt;Постоянный адрес рецепта&lt;/a&gt; на foodclub.ru&lt;br /&gt;&lt;br /&gt;&lt;lj-cut text="Читать подробности"&gt;&lt;br /&gt;&lt;br /&gt;Состав&lt;br /&gt;&lt;br /&gt;'+ingredientsText+'&lt;br /&gt;	&lt;br /&gt;&lt;br /&gt;'+stagesText+'&lt;/lj-cut&gt;';
		HTMLcode.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=windows-1251" /><title>HTML-код</title></head><body><textarea cols="80" rows="40">'+text+'</textarea></body></html>');
		HTMLcode.document.getElementByTag("TEXTAREA").select();
		return false;
	});
	$(".topic_item #html_code").click(function() {
		var $topic = $(".topic_item"),
			HTMLcode = window.open('', 'HTMLcode', 'width=800, height=800, toolbar=0, scrollbars=yes, status=0, directories=0, location=0, menubar=0, resizable=0'),
			heading = $topic.find("h1").text(),
			$text = $topic.find(".text"),
			$div = $('<div></div>');
		
		$div.html($text.html());
		$div = makeFullSrcHrefAttributes($div);
		var text = makeTextLjCutTag($div);
		
		HTMLcode.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=windows-1251" /><title>HTML-код</title></head><body><textarea cols="80" rows="40">' + text + '</textarea></body></html>');
		
		//HTMLcode.document.getElementByTag("TEXTAREA").select();
		return false;
		
		function makeFullSrcHrefAttributes($div) {
			$div
				.find("[src]").each(function() {
					var src = $(this).attr("src");
					if(/^http/i.test(src)) return;
					
					$(this).attr({src: window.location.protocol + "//" + window.location.hostname + src});
				})
				.find("[href]").each(function() {
					var href = $(this).attr("href");
					if(/^http/i.test(href)) return;
					
					$(this).attr({href: window.location.protocol + "//" + window.location.hostname + href});
				});
			
			return $div;
		}
		
		function makeTextLjCutTag($div) {
			var $cut = $div.find('[name="cut"]');
		
			if($cut.size() == 0) return $div.html();
			
			$cut.before('<lj-cut></lj-cut>').remove();
			
			var text = $div.html();
			
			text += "";
			text = text.split('</lj-cut>');
			
			text = text[0] + text[1] + '</lj-cut>';
			
			return text;
		}
	});
	
	$("div.needed div.scales").click(function() {
		window.open('/table/','scalesWin','width=800, height=800,toolbar=0,scrollbars=yes,status=0,directories=0,location=0,menubar=0,resizable=0');
		return false;
	});
	$("#top_panel span.kitchen > a").click(function(e) {
		$(this).siblings("span.submenu").slideToggle("middle").end().children("span").toggleClass("up").toggleClass("down");
		stopEvent(this, e);
		return false;
	});
	$("#top_panel div.add > a.button").click(function(e) {
		$(this).siblings("span.submenu").children("span.body").slideToggle("middle");
		stopEvent(this, e);
		return false;
	});
	$("div.share > a").click(function() {
		$(this).closest("div.bar").next("div.share_bar").slideToggle("middle");
		$(this).parent().toggleClass("open");
		return false;
	});
	$("input.button_obj").hover(function() {$(this).addClass("button_obj_hover");
	}, function() {$(this).removeClass("button_obj_hover");
	});
	$("input.button_obj").mousedown(function() {$(this).addClass("button_obj_press");
	});
	if (String(window.location).search("#add_opinion") != -1) {
		$("#opinion_form").find("textarea").focus();
	}
	$("a[href='#add_opinion']").click(function() {
		$("#opinion_form").find("textarea").focus();
		return false;
	});
	$("#search_list_layer div.relative div.close_icon").click(function() {
		showHideLayer("top_layer");
		showHideLayer("search_list_layer");
		$("#search_list_layer div.relative div.padding div.column").empty();
	});
	$("#search_list_layer div.relative div.padding div.button").click(function() {
		writeChosenItems(this);
		showHideLayer("top_layer");
		showHideLayer("search_list_layer");
		$("#search_list_layer div.relative div.padding div.column").empty();
		document.getElementById("recipe_search_field").focus();
	});
	$("#recipe_line_block").find("div.big_photo").each(function() {
		$(this).appendTo($("#big_recipe_photos"));
	});
	$(document).delegate("div.photo, div.author_photo", "mouseenter", function() {
		$(this).children("div.big_photo").css({display:"block"});
	})
	.delegate("div.photo, div.author_photo", "mouseleave", function() {
		$(this).children("div.big_photo").css({display:"none"});//.animate({opacity:"hide"}, "fast");
	});
	$("#recipe_line_block").find("div.photo").children("img").mouseover(function() {
		var topValue = "-121px";
		var leftValue = pageX(this) - pageX(document.getElementById("recipe_line_block")) - 12;
		$("#" + $(this).parent().attr("id") + "_big").children("div").css({top:topValue, left:leftValue});
		$("#" + $(this).parent().attr("id") + "_big").css({display:"block"});
		
	});
	$("#recipe_line_block").find("div.big_photo").hover(function() {
	}, function() {
		$(this).animate({opacity:"hide"}, "fast");
	});
	$("div.search_field form input.button").click(function() {
		searchForm(this);
		return false;
	});
	$("div.search_field div.search_delete").click(function() {
		$(this).siblings("form").children("input.text").attr({value:""});
		$("#recipe_search_field").focus();
		$("#top_search_list").children("ul").css({display:"none"});
	});
	$("#recipe_search_field").focus(function() {
		if (this.value == "Я ищу") {
			this.value = "";
		}
	});
	$("#recipe_search_field").blur(function() {
		if (this.value == "") {
			this.value = "Я ищу";
		}
	});
	/*$("#top_panel a.sign_in, #contest_sauce div.recipe_carousel a.sign_in").click(function() {
		showHideLayer('top_layer');
		showHideLayer ('authorization_layer');
		$("#lj_id_field").children("div.fields").find("input").focus();
		return false;
	});*/
	$("#authorization_layer div.relative div.padding div.button").click(function() {
		checkALayer(this);
	});
	$("#authorization_layer div.relative div.padding div.bar div a").click(function() {
		var preAct = $(this).parent().parent().parent().children("div.body").children("div.act");
		$(preAct).removeClass("act");
		$(preAct).slideUp("middle");
		$(preAct).find("div.form_field").removeClass("attention").children("input").attr({value:""});
		$(preAct).find("div.form_field").children("input[name='open_id']").attr({value:"http://"});
		$(this).parent().parent().children("div.act").removeClass("act");
		$(this).parent().addClass("act");
		$("#" + $(this).attr("rel")).addClass("act")
		$("#" + $(this).attr("rel")).slideDown("middle");
//		setTimeout(function() {$("#" + $(this).attr("href")).find("div.fields").find("input:eq(0)").focus()}, 3000);
		$("#" + $(this).attr("rel")).find("div.fields").find("input:eq(0)").focus();
		return false;
	});
	$("#authorization_layer div.relative div.close_icon").click(function() {
		showHideLayer('top_layer');
		showHideLayer ('authorization_layer');
	});
	$("#form div.button_authorization").click(function() {
		checkAForm(this);
	});
	$("#form div.authorization").find("input.text").keypress(function(e){
		if(e.which == 13){
			checkAForm(this);
		}
	});
	$("#form div.lj_id").find("input.text").keypress(function(e){
		if(e.which == 13){
			checkAForm(this);
		}
	});
	$("#form div.open_id").find("input.text").keypress(function(e){
		if(e.which == 13){
			checkAForm(this);
		}
	});
	$("#form div.authorization form div div.remember a").click(function() {
		if (this.parentNode.className.search("chosen") != -1) {
			$(this).parent().removeClass("chosen");
			$(this).siblings("input").attr({value:"N"});
		}
		else {
			$(this).parent().addClass("chosen");
			$(this).siblings("input").attr({value:"Y"})
		}
		return false;
	});
	$("#form div h2 a").click(function() {
		var preAct = $(this).parent().parent().parent().children("div.act").removeClass("act");
		$(preAct).find("div.fields").slideUp("middle");
		$(preAct).find("div.form_field").removeClass("attention").children("input").attr({value:""});
		$(preAct).find("div.form_field").removeClass("attention").children("input[name='open_id']").attr({value:"http://"});
		$(this).parent().parent().addClass("act");
		$(this).parent().parent().find("div.fields").slideDown("middle");
		return false;
	});
	$("#form").find("div.button").click(function() {
		checkForm(this);
	});
	$("input.text").keypress(function(e){
		if(e.which == 13){
			if ($(this).attr("id") == "helper_smartsearch") {return false;
			}
			else if ($(this).attr("id") == "recipe_search_field") {
				return false;
			}
			else if ($(this).parents("#dish_stages").size() != 0) {
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
			else if ($(this).hasClass("smartsearch")) {
				return false;
			}
			else if (!$(this).attr("name") || $(this).parents("form").attr("name") == "authorization" || $(this).parents("form").attr("name") == "open_id") {
				checkALayer(this);
			}
			else {
				checkForm(this);
				return false;
			}
		}
	});
	$("#opinion_block").find("div.edit").click(function() {
		$("#opinion_block").find("div.opinion").removeClass("edit_form");
		$(this).parents("div.opinion").addClass("edit_form");
	});
	$("#opinion_block, #comment_form").find("div.button").click(function() {
		checkForm(this);
	});
	$("#opinion_form").find("textarea").keyup(function(e) {
		var parentDiv = this.parentNode;
		for (var i = 0; i < parentDiv.childNodes.length; i++) {
			if (parentDiv.childNodes[i].className.search("button") != -1) {
				var but = parentDiv.childNodes[i];
			}
		}
		if (e.ctrlKey && e.keyCode == 13) {
				checkForm(but);
		}
	}).keydown(function(e) {
		var parentDiv = this.parentNode;
		for (var i = 0; i < parentDiv.childNodes.length; i++) {
			if (parentDiv.childNodes[i].className.search("button") != -1) {
				var but = parentDiv.childNodes[i];
			}
		}
		if (e.ctrlKey && e.keyCode == 13) {
				checkForm(but);
		}
	});
	$("#opinion_block").find("div.delete").click(function() {
		if (!confirm("Удалить отзыв?")) {
			return false;
		}
		else {
			var opinionId = $(this).parents("div.opinion").attr("id");
			var recipeId = $("#text_space").children("div.recipe").attr("id");
			var newHref = String(window.location).split(document.domain)[0] + document.domain + "/comment.php?cId=" + opinionId + "&rId=" + recipeId + "&a=d";
			window.location.href = newHref;
		}
	});
	$("#opinion_block div.close_icon").click(function() {
		$(this).parents("div.opinion").removeClass("edit_form");
	});
	$("div.comments_block")
	.delegate("div.reply_string a", "click", function() {
		if($(this).hasClass("i-open")) {
			$("#reply_form div.close_icon div").click();
			return false;
		}
		$("div.comments_block div.comment").removeClass("edit_form");
		//$("div.comments_block div.reply_string").css({visibility:"visible"});
		$("div.comments_block div.reply_string a").text("Ответить").removeClass("i-open");
		$("#reply_form").hide().remove().insertAfter($(this).parent().parent().parent().parent()).slideDown("middle");
		$("#reply_form div.close_icon div").unbind("click").click(function() {
			$("#reply_form").prev().find("div.reply_string a").text("Ответить").removeClass("i-open");
			$("#reply_form").slideUp("middle").find("h4").removeClass("attention");
		});
		$("#reply_form .button")
			.click(function() {
				if($(this).hasClass("i-sent")) return false;
				$(this).addClass("i-sent");
				
				if($("#reply_form textarea").val() != "") {
					$("#reply_form form").submit();
				}
				else {
					$("#reply_form h4").addClass("attention");
					return false;
				}
			})
			.hover(
				function() {
					$(this).addClass("button_hover");
				},
				function() {
					$(this).removeClass("button_hover");
				}
			)
			.mousedown(function() {$(this).addClass("button_active");})
			.mouseup(function() {$(this).removeClass("button_active");});
		$("#reply_form textarea").val("").keyup(function(e) {
			if (e.ctrlKey && e.keyCode == 13) {
				if($(this).attr("value") != "" || $(this).text() != "") {
					$("#reply_form").children("form").submit();
				}
				else {return false;
				}
			}
		}).keydown(function(e) {
			if (e.ctrlKey && e.keyCode == 13) {
				if($(this).attr("value") != "" || $(this).text() != "") {
					$("#reply_form").children("form").submit();
				}
				else {return false;
				}
			}
		}).resizeTextarea();
		$(this).text("Скрыть").addClass("i-open");
		var parentId = $(this).parents("div.comment").attr("id");
		$("#reply_form").find("input[name='parentId']").attr({value:parentId});
		return false;
	})
	.delegate("div.edit", "click", function() {
		$("div.comments_block").find("div.edit_form").removeClass("edit_form");
		$(this).parents("div.comment").addClass("edit_form").find("textarea").resizeTextarea();
		$("div.comments_block").find("div.reply_string").css({visibility:"visible"});
		$("#reply_form").slideUp("middle");
	})
	.delegate("div.close_icon", "click", function() {
		$(this).parents("div.comment").removeClass("edit_form");
	})
	.delegate("div.button", "click", function() {
		checkForm(this);
	})
	.delegate("div.button", "click", function() {
		checkForm(this);
	})
	.delegate("textarea", "keyup", function(e) {
		var parentDiv = this.parentNode;
		for (var i = 0; i < parentDiv.childNodes.length; i++) {
			if (parentDiv.childNodes[i].className.search("button") != -1) {
				var but = parentDiv.childNodes[i];
			}
		}
		if (e.ctrlKey && e.keyCode == 13) {
				checkForm(but);
		}
	})
	.delegate("textarea", "keydown", function(e) {
		var parentDiv = this.parentNode;
		for (var i = 0; i < parentDiv.childNodes.length; i++) {
			if (parentDiv.childNodes[i].className.search("button") != -1) {
				var but = parentDiv.childNodes[i];
			}
		}
		if (e.ctrlKey && e.keyCode == 13) {
				checkForm(but);
		}
	})
	.delegate("div.delete", "click", function() {
		if (!confirm("Удалить комментарий?")) {
			return false;
		} else {
			/*var $form = $(this).closest(".comment").find("form");
			$form.append('<input type="hidden" name="a" value="d">');
			$form.submit();*/
			var commentId = $(this).closest(".comment").attr("id");
			var newHref = String(window.location).split("?")[0] + "?delete_comment_id=" + commentId + "&sessid=" + $("#sessId").text() + "";
			window.location.href = newHref;
		}
	});
	$("#personal_bar div.item a").click(function() {
		$("#personal_bar").children("div.act").removeClass("act");
		$(this).parent().addClass("act");
		return false;
	});
	var deleteConfirm = 0;
	$("#left_menu").find("div.switch").find("a").click(function() {
		$("#left_menu").find("ul.list").removeClass("act");
		$("#" + $(this).attr("rel")).addClass("act");
		$(this).parent().parent().children("div").removeClass("act");
		$(this).parent().addClass("act");
		return false;
	});
	$("div.pager").children("div.pointer").hover(function() {
		$(this).addClass("hover");
	}, function() {
		$(this).removeClass("hover");
	});
	$("#club_search_field").focus(function() {
		if (this.value == "Найти клуб") {
			this.value = "";
		}
	});
	$("#club_search_field").blur(function() {
		if (this.value == "") {
			this.value = "Найти клуб";
		}
	});
	$("#add_post_form").find("div.button").click(function() {
		document.getElementById("add_post_form").submit();
	});
	$("#user_list").find("div.button").click(function() {
		document.getElementById("user_list").submit();
	});
	$("#edit_form").find("div.button").click(function() {
		checkForm(this);
	});
	$("#setup_form").find("div.button").click(function() {
		checkForm(this);
	});
	$("div.form_checkbox_pic img").click(function() {
		activateCheckbox(this);
	});
	$("div.form_checkbox_pic span").click(function() {
		activateCheckbox(this);
	});
	$("#edit_avatar").click(function() {
		$(this).parent("form").submit();
	});
	$("#save_recipe").click(function() {
		checkStageForm();
		return false;
	});
	/*Helper*/
	if($.placeholder) $("#helper_smartsearch").placeholder();
	//slide down, show helper
	$("#search_helper_link").click(function() {
		showHideLayer("top_layer");
		$("#search_helper div.body div.menu div.item").removeClass("act");
		$("#search_helper div.body div.menu div.item:eq(0)").addClass("act");
		$("#search_helper div.body div.search_blocks").css({display:"none"});
		$("#h_helper").css({display:"block"});
		$("#i_have_list div.bg table").empty();
		$("#i_have_dash").css({display:"block"});
		$("#search_helper").css({top:"0"}).removeClass("stage_helper").slideDown("middle");
		return false;
	});
	//slide up helper
	$("#search_helper div.body div.slide_up_button").click(function() {
		$("#search_helper").slideUp("middle", function() {
			showHideLayer("top_layer");
		});
	});
	//topmenu switch
	$("#search_helper div.body div.menu div.item a").click(function() {
		$("#search_helper div.body div.search_blocks").css({display:"none"});
		$("#" + $(this).attr("rel")).css({display:"block"});
		$(this).parent().parent().children("div.item").removeClass("act");
		$(this).parent().addClass("act");
		if ($(this).attr("rel") == "h_ingredients") {
			$("#i_have_ingredients_list div.column ul").empty();
			$("#i_have_ingredients_list h2").empty();
			createGroupList();
		}
		return false;
	});
	$("#i_have_button").click(function() {
		var location = "/search/";
		$(this).parent().find("tr").each(function() {
			location += $(this).find("span").text().toLowerCase() + "/";
		});
		window.location.href = location;
	});
	
	$("#helper_smartsearch").focus(function() {
		if (this.value == "") {
			this.value = "";
		}
	});
	$("#helper_smartsearch").blur(function() {
		if (this.value == "") {
			this.value = "";
		}
	});
	var keyPress = 0;//ie&chrome 
	$("#helper_smartsearch").keyup(function(event) {
		if (window.event) event = window.event;
		switch (event.keyCode ? event.keyCode : event.which ? event.which : null) {
			case 38:
				if(keyPress == 0) {smartsearchNavUp(this);
				}
				break;
			case 40:
				if(keyPress == 0) {smartsearchNavDown(this);
				}
				break;
			default:smartsearchFunction(this);
		}
	});
	$("#helper_smartsearch").keypress(function(event) {
		if (window.event) event = window.event;
		switch (event.keyCode ? event.keyCode : event.which ? event.which : null) {
			case 38:
				keyPress = 1;
				smartsearchNavUp(this);
			break;
			case 40:
				keyPress = 1;
				smartsearchNavDown(this);
			break;
			case 13:
				keyPress = 0;
				pressEnter();
			break;
		}
	});
	/*_Helper_*/
	/*Stages*/
	
	/*_Stages_*/
});

function UpButton() {//consider the case when the top banner is shown
	var self = this;
	
	init();
	
	function init() {
		if(!document.getElementById("top_panel") || !document.getElementById("body")) return;
		appendElements();
		initVariables();
		styleElements();
		handleEvents();
		
		$(window).scroll().resize();
		$(document).mousemove();
	}
	
	function appendElements() {
		$("body").prepend('<div id="upButton"><span class="b-up-button__up">Наверх</span><span class="b-up-button__down">Обратно</span></div>');
		$("body").prepend('<div id="upButtonClickable"></div>');
	}
	
	function initVariables() {
		self.scrolled = getScrolled();
		self.memory = 0;
		self.$elem = $("#upButton");
		self.$elem.data("object", self);
		self.$clickable = $("#upButtonClickable");
	}
	
	function styleElements() {
		styleButton();
		styleClickable();
		
		function styleButton() {			
			var top = $("#top_panel").offset().top + $("#top_panel").height() + 50 - self.scrolled;
			var left = $("#body").offset().left - 100;
			
			self.$elem.css({
				top: top,
				left: left
			});
		}
		
		function styleClickable() {
			var top = $("#top_panel").offset().top + $("#top_panel").height() - self.scrolled;
			var left = 0;
			var width = $("#body").offset().left - 25;
			self.$clickable.css({height: $("#body").height()});
			var height = $(document).height() - top;
			
			if(width < 0) {
				width = 0;
			}
			
			self.$clickable.css({
				top: top,
				left: left,
				width: width,
				height: height
			});
		}
	}
	
	function getScrolled() {
		return window.pageYOffset || document.documentElement.scrollTop;
	}
	
	function handleEvents() {
		$(window)
			.scroll(scrollWindow)
			.resize(resizeWindow);
		
		self.$clickable
			.click(clickClickable);
		
		$(document).mousemove(showHideClickable);
		
		function showHideClickable(e) {
			if(self.scrolled < 200) {
				hideClickable();
			}
			
			if(e.pageX) {
				self.pageX = e.pageX;
			}
			
			if(!self.pageX) return;
			
			if((self.pageX < self.$clickable.width() && self.scrolled > 200) || (self.pageX < self.$clickable.width() && self.scrolled == 0 && self.memory != 0)) {
				showClickable();
			} else {
				hideClickable();
			}
			
		}
		
		function showClickable() {
			if(self.memory != self.scrolled) {
				self.$clickable.addClass("i-visible");
			}
		}
		
		function hideClickable() {
			self.$clickable.removeClass("i-visible");
		}
		
		function clickClickable() {
			jumpUpDown();
			
			function jumpUpDown() {
				var memory = self.memory;
				self.memory = self.scrolled;
				$.scrollTo(memory);//calling scroll event scrollWindow()
			}
		}
		
		function scrollWindow(e) {
			self.scrolled = getScrolled();
			showHideButton();
			showHideClickable(e);
			if(!self.$elem.hasClass("i-reverse")) {
				self.memory = 0;
			}
		}
		
		function resizeWindow(e) {
			styleElements();
			showHideClickable(e);
		}
	}
	
	function showHideButton() {
		if(self.scrolled > 200) {
			self.$elem.addClass("i-visible");
			self.$elem.removeClass("i-reverse");
		} else if(self.memory != 0) {
			self.$elem.addClass("i-reverse");
		} else {
			self.$elem.removeClass("i-visible");
			self.$elem.removeClass("i-reverse");
		}
	}

	/*--- public methods ---*/

	this.styleElements = function() {
		styleElements();
	};
}

function SearchRecipeFeed() {
	var self = this;
	
	init();
	
	function init() {
		setElements();
		handleEvents();
	}
	
	function setElements() {
		self.$statistics = $("#fc_statistics");
		self.$getMoreButton = $("#get_more_recipes");
		self.recipesPerPage = 27;
	}
	
	function handleEvents() {
		self.$getMoreButton.click(clickMoreRecipes);
	}
	
	function clickMoreRecipes() {
		var idPortion = [];
		
		for(var i = 0; i < self.recipesPerPage; i++) {
			if(allRecipesResult[0]) {
				idPortion[i] = allRecipesResult.shift();
			}
		}
		
		$.ajax({
			url: "/php/get_more_recipes.php",
			dataType: "json",
			data: "id=" + idPortion,
			beforeSend: function() {
				$("#get_more_recipes").addClass("preload");
			},
			success: function(data){
				$("#get_more_recipes").removeClass("preload");
				var div = $('<div class="block" style="display:none;"></div>');
				
				var html = '';
				for(var i=0; i < data.recipes.length; i++) {
					html += '<div class="item recipe_list_item"><div class="photo"><a href="'+data.recipes[i].href+'" title="'+data.recipes[i].name+'"><img src="'+data.recipes[i].src+'" width="170" alt="'+data.recipes[i].name+'" /></a></div><h5><a href="'+data.recipes[i].href+'">'+data.recipes[i].name+'</a></h5><p class="author">От: '+data.recipes[i].author+'</p><p class="info"><span class="comments_icon" title="Оставить отзыв"><noindex><a href="'+data.recipes[i].href+'#comments">'+data.recipes[i].comments+'</a></noindex></span></p></div>';
				}
				div.html(html);
				$("#recipe_feed_block").find("div.clear").before(div).end().find("div.block:last div.recipe_list_item div.photo a").each(function() {
					var $this = $(this),
						$img = $this.children("img"),
						img = new Image();
						
					img.src = $img.attr("src");
					var hei = Math.floor(img.height*$img.attr("width")/img.width);
					if(hei > 0) {
						$img.css({marginTop:(parseInt($this.css("height"))/2-hei/2)+"px"});
					}
					else {
						$img.load(function() {
							var hei = Math.floor(img.height*$img.attr("width")/img.width);
							if(hei>0) {$img.css({marginTop:(parseInt($this.css("height"))/2-hei/2)+"px"});}
						});
					}
				}).end()
				.find("div.block:last").css({opacity:0}).show().animate({opacity:1}, 500);
				
				$.scrollTo($("#recipe_feed_block div.block:last div.item:first"), 1000);
				
				showHideGetMoreButton();
				
				window.upButton.styleElements();
				
				function showHideGetMoreButton() {
					var difference = allRecipesResult.length;
					
					if(difference == 0) {
						$("#get_more_recipes").hide();
					} else {
						$("#get_more_recipes").show();
					}
				}

			}
		});
		
		return false;
		
	}
	
	function showRecipesNum(recipesNum) {
		self.$statistics.find("span.num").text(recipesNum).end().find("span.word").text(recipeWord(recipesNum));
			
		function recipeWord(num) {
			if (/(10|11|12|13|14|15|16|17|18|19)$/.test(num)) {
				return 'рецептов';
			} else if (/.*1$/.test(num)) {
				return 'рецепт';
			} else if (/[2-4]$/.test(num)) {
				return 'рецепта';
			} else {
				return 'рецептов';
			}
		}
	}
}

function buttonActions() {
	$(".button")
		.mousedown(function() {
			$(this).addClass("button_active");
		})
		.mouseup(function() {
			$(this).removeClass("button_active");
		})
		.hover(function() {
			$(this).addClass("button_hover");
		},
		function() {
			$(this).removeClass("button_hover");
		});
}

function deleteStageImage(img_object, img_id) {
	deleteConfirm = 1;
	if (confirm("Удалить?")) {
		window.location.href = window.location + "?id=" + img_id;
	}
	else {
		$(img_object).css({backgroundPosition:"left top"});
		deleteConfirm = 0;
	}
}

function InputFile($elem, params) {
	var self = this;
	self.$elem = $elem;
	self.$input = self.$elem.find(":file");
	
	var options = {}, params = params || {};
	options.extentions = params.extentions || ["jpg", "jpeg"];
	options.messages = params.maessages ||
		{
			wrongExtention : "Загружайте изображения в jpeg формате"
		};
	init();
	
	function init() {
		
		createHTML();
		
		self.$name = self.$elem.find("div.new_file_name");
		
		self.$input.change(function() {
			handleChanges();
		});
		
	}
	
	function clearValue() {
		self.$elem.find(":file").remove();
		self.$elem.find(".browse_button").after(self.$input);
	}
	
	function createHTML() {
		self.$elem.html('<div class="browse_button" title="Выбрать файл"></div><div class="blocker"></div><div class="new_file_name"></div>');
		self.$elem.find(".browse_button").after(self.$input);
	}
	
	function handleChanges() {
		
		var fileTitle = getFileTitle();
		
		var fileExt = getFileExt(fileTitle);
		
		if(isValidFileExt(fileExt)) {
			self.$name.text(fileTitle);
			self.$name.removeClass("i-attention");
		}
		else {
			self.$name.text(options.messages.wrongExtention);
			self.$name.addClass("i-attention");
			//clearValue();
		}
		
		self.$name.css({display:"block"});
	}
	
	function filesize (url) {
		// Get file size  
		// 
		// version: 1109.2015
		// discuss at: http://phpjs.org/functions/filesize
		// +   original by: Enrique Gonzalez
		// +      input by: Jani Hartikainen
		// +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
		// +   improved by: T. Wild
		// %        note 1: This function uses XmlHttpRequest and cannot retrieve resource from different domain.
		// %        note 1: Synchronous so may lock up browser, mainly here for study purposes. 
		// *     example 1: filesize('http://kevin.vanzonneveld.net/pj_test_supportfile_1.htm');
		// *     returns 1: '3'
		var req = this.window.ActiveXObject ? new ActiveXObject("Microsoft.XMLHTTP") : new XMLHttpRequest();
		if (!req) {
			throw new Error('XMLHttpRequest not supported');
		}
	 
		req.open('HEAD', url, false);
		req.send(null);
	 
		if (!req.getResponseHeader) {
			try {
				throw new Error('No getResponseHeader!');
			} catch (e) {
				return false;
			}
		} else if (!req.getResponseHeader('Content-Length')) {
			try {
				throw new Error('No Content-Length!');
			} catch (e2) {
				return false;
			}
		} else {
			return req.getResponseHeader('Content-Length');
		}
	}
	
	function isValidFileExt(fileExt) {
		
		var flag = false;
		
		for(var i = 0; i < options.extentions.length; i++) {
			if(fileExt.toLowerCase() == options.extentions[i]) flag = true;
		}
		
		return flag;
	}
	
	function getFileExt(fileTitle) {
		var RegExExt =/.*\.(.*)/;
		var fileExt = fileTitle.replace(RegExExt, "$1");
		
		return fileExt;
	}
	
	function getFileTitle() {
		var value = self.$input.val();
		
		reWin = /.*\\(.*)/;
		var fileTitle = value.replace(reWin, "$1");
		
		reUnix = /.*\/(.*)/;
		fileTitle = fileTitle.replace(reUnix, "$1");
		
		if (fileTitle.length > 18) {
			fileTitle = "..." + fileTitle.substr(fileTitle.length - 16, 16);
		}
		
		return fileTitle;
	}
}

function checkAForm(input) {
	var flag = 0;
	var actDiv = $("#form div.act");
	$(actDiv).find("input[type!='hidden']").each(function() {
		if ($(this).attr("value") == "") {
			flag = 1;
			$(this).parent().addClass("attention");
		}
		else {$(this).parent().removeClass("attention");
		}
	});
	if (flag == 0) {
		var form = $(actDiv).children("form");
		if ($(form).attr("name") == "lj_id") {
			var userName = $(actDiv).children("div.fields").find("input").attr("value");
			$(form).find("input").attr({value:"http://" + userName + ".livejournal.com"});
		}
		if ($(form).attr("name") == "open_id" && $(form).find("input").attr("value") == "http://") {
			return false;
		}
		form.submit();
	}
}

function checkALayer(input) {
	var flag = 0;
	var actDiv = $("#authorization_layer div.relative div.padding div.act");
	$(actDiv).find("input[type!='hidden']").each(function() {
		if ($(this).attr("value") == "") {
			flag = 1;
			$(this).parent().addClass("attention");
		}
		else {$(this).parent().removeClass("attention");
		}
	});
	if (flag == 0) {
		var form = $(actDiv).children("form");
		if ($(form).attr("name") == "lj_id") {
			var userName = $(actDiv).children("div.fields").find("input").attr("value");
			$(form).find("input").attr({value:"http://" + userName + ".livejournal.com"});
		}
		if ($(form).attr("name") == "open_id" && $(form).find("input").attr("value") == "http://") {
			return false;
		}
		form.submit();
	}
}

function searchForm(formElement) {
	if (document.getElementById("recipe_search_field").value != "" ) {
		if (document.getElementById("recipe_search_field").value != "Я ищу" ) {
			if ($(formElement).hasClass("button")) {
				var searchString = new String($(formElement).siblings("input.text").attr("value"));
			}
			if ($(formElement).hasClass("text")) {
				var searchString = new String($(formElement).attr("value"));
			}
			var searchItems = searchString.split(", ");
			var locationString = searchItems.join("/");
			var newHref = String(window.location).split(document.domain)[0] + document.domain + "/search/" + locationString + "/";
			window.location.href = newHref;
		}
	}
}

function checkForgetForm() {
	var form = $("#form").find("form");
	var loginField = $("form").find("input[name='login']");
	var mailField = $("form").find("input[name='e-mail']");
	if ($(loginField).attr("value") != "") {
		form.submit();
	}
	else {
		if ($(mailField).attr("value") != "") {
			var mail = $(mailField).attr("value");
			var mailRegex = /^([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,})$/i;
			if(!mail.match(mailRegex)){
				$(mailField).parent().addClass("attention");
			}
			 else {
				 form.submit();
			};
		}
	}
}

function checkForm(form_element) {
	if($(form_element).hasClass("i-sent")) return false;
	$(form_element).addClass("i-sent");
	
	if($(form_element).hasClass("i-processing")) return false;
	if (form_element.className == "text" && !form_element.getAttribute("name")) {
		if ($(form_element).attr("value") == "" && $(form_element).parent().children("h5").children("span").text() != "") {
			$(form_element).parent().addClass("attention");
		}
		else {
			var form = $(form_element).parents("div.open_id").children("form");
			var userName = $(form_element).attr("value");
			$(form).find("input").attr({value:"http://" + userName + ".livejournal.com"});
			form.submit();
		}
	}
	if ((form_element.className == "text" && form_element.getAttribute("name")) || (form_element.className.search("button") != -1)) {
		var $form = $(form_element).closest("form");
		if ($form.attr("name") == "forget") {
			checkForgetForm();
		}
		else {
			var flag = 0;
			$form.find("input[type!='hidden']").each(function() {
				if ($(this).attr("value") == "" && $(this).parent().children("h5").children("span").text() != "") {
					if (this.getAttribute("name") && this.getAttribute("name").search("PASSWORD_CONFIRM") != -1) {
						var passConfField = this;
						var passwordField;
						$form.find("input[name*='PASSWORD']").each(function() {
							if (this != passConfField) {
								passwordField = this;
							}
						});
						if($(passwordField).attr("value") != ""){
							$(this).parent().addClass("attention");
							flag = 1;
						}
						else {$(this).parent().removeClass("attention");
						}
					}
					else {
						flag = 1;
						$(this).parent().addClass("attention");
					}
				}
				else {
					if ($(this).attr("name").search("mail") != -1 || $(this).attr("name").search("MAIL") != -1) {
						var mail = $(this).attr("value");
						var mailRegex = /^([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,})$/i;
						if(!mail.match(mailRegex)){
							$(this).parent().addClass("attention");
							flag = 1;
						}
						else {$(this).parent().removeClass("attention");
						}
					}
					else {
						if (this.getAttribute("name") && this.getAttribute("name").search("PASSWORD_CONFIRM") != -1) {
							var passConfField = this;
							var passwordField;
							$form.find("input[name*='PASSWORD']").each(function() {
								if (this != passConfField) {
									passwordField = this;
								}
							});
							if($(this).attr("value") != $(passwordField).attr("value")){
								$(this).parent().addClass("attention");
								flag = 1;
							}
							else {$(this).parent().removeClass("attention");
							}
						}
						else {
							$(this).parent().removeClass("attention");
						}
					}
				}
			});
			$form.find("textarea").each(function() {
				if ($(this).val() == "" && $(this).closest(".form_field").find("h4 span").text() != "") {
					flag = 1;
					$(this).closest(".form_field").addClass("attention");
				}
			});
			if (flag == 0) {
				if($(form_element).hasClass("button") || $(form_element).hasClass("b-button")) $(form_element).addClass("i-processing");
				$form.submit();
			}
		}
	}
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
			var layerHeight = $(layer).height();
			if ($(layer).find("div.padding").height() != "") {
				layerHeight = $(layer).find("div.padding").height();
			}
			if (document.documentElement.clientHeight > layerHeight) {
				var lTop = (document.documentElement.clientHeight)/2 + $(window).scrollTop() - layerHeight/2 - 20 + "px";
			}
			else {
				var lTop = $(window).scrollTop() + 20 + "px";
			}
			if (layer_id == "ingredients_list_layer" || layer_id == "stage_ingredients_list_layer") {
				var lTop = $(window).scrollTop() + 20 + "px";
			}
			$("#" + layer_id).animate({top:lTop}, 500, function() {
				$("#top_layer").height($(document).height() + 30);
				$("#top_layer iframe").height($(document).height() + 30);
			});
		}
	}
	else {layer.style.display = "none";
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

function writeChosenItems(button) {
/*	switch($(button).parent().children("h2").text()) {
		case "Р С™РЎС“РЎвЂ¦Р Р…Р С??&#65533;": var curArray = chosenSearchKitchen; break;
		case "Р СћР С??&#65533;Р С— Р В±Р В»РЎР‹Р Т??&#65533;Р В°": var curArray = chosenSearchDish; break;
	}
	var parent = button.parentNode;
	var length = curArray.length;
	for (var i = 0; i < length; i++) {
		curArray.pop();
	}
	for (var i = 0; i < parent.childNodes.length; i++) {
		if (parent.childNodes[i].className == "column") {
			var column = parent.childNodes[i];
			for (var j = 0; j < column.childNodes.length; j++) {
				if (column.childNodes[j].tagName == "UL") {
					var ul = column.childNodes[j];
					for (var p = 0; p < ul.childNodes.length; p++) {
						if (ul.childNodes[p].className == "selected") {
							curArray.push(ul.childNodes[p].getAttribute("rel"));
						}
					}
				}
			}
		}
	}
	
	fillSearchField();*/
}

function increaseTopLayer() {
	var ingredientsListLayerString = new String($("#ingredients_list_layer").css("top"));
	var ingredientsListLayerTop = parseInt(ingredientsListLayerString.substring(0, ingredientsListLayerString.length - 2));
	var topLayerHeight = $("#ingredients_list").height() + 140 + ingredientsListLayerTop;
	if ($("#top_layer").height() < topLayerHeight) {
		$("#top_layer").height(topLayerHeight);
		$("#top_layer iframe").height(topLayerHeight);
	}
	else {
		$("#top_layer").height(getyScroll());
		$("#top_layer iframe").height(getyScroll());
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
	//РЎС“Р Т??&#65533;Р В°Р В»РЎРЏР ВµР С?&#65533; Р Т??&#65533;Р Р†Р С•Р в„–Р Р…РЎвЂ№Р Вµ Р С—РЎР‚Р С•Р В±Р ВµР В»РЎвЂ№
	objectClass = objectClass.split("  ").join(" ");
	//РЎС“Р Т??&#65533;Р В°Р В»РЎРЏР ВµР С?&#65533; Р С—РЎР‚Р С•Р В±Р ВµР В»РЎвЂ№ Р Р† Р С”Р С•Р Р…Р Вµ Р С??&#65533; Р Р…Р В°РЎвЂЎР В°Р В»Р Вµ РЎРѓРЎвЂљРЎР‚Р С•Р С”Р С??&#65533;
	if (objectClass.slice(0,1) == " ") {
		objectClass = objectClass.slice(1, objectClass.length);
	}
	if (objectClass.slice(objectClass.length-1) == " ") {
		objectClass = objectClass.slice(0, objectClass.length-1);
	}
	object.className = objectClass;
}

//Р С—РЎР‚Р С•Р Р†Р ВµРЎР‚Р С”Р В° РЎвЂ Р С??&#65533;РЎвЂћРЎР‚Р С•Р Р†РЎвЂ№РЎвЂ¦ Р С—Р С•Р В»Р ВµР в„–
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

function clearArray(array) {
	var length = array.length;
	for(var i = 0; i < length; i++) {
		array.pop();
	}
}
function activateCheckbox(element) {
	var img = $(element).parent().children("img");
	var attr2 = new String($(img).attr("src"));
	var hiddenInput = $(element).parent().children("input");
	if (attr2.search('_act') != -1) {
		$(img).attr({src:"/images/checkbox.gif"});
		$(hiddenInput).attr({value:"N"});
	}
	else {
		$(img).attr({src:"/images/checkbox_act.gif"});
		$(hiddenInput).attr({value:"Y"});
	}
}
function pageX(elem) {
	return elem.offsetParent ?
	elem.offsetLeft + pageX( elem.offsetParent ) :
	elem.offsetLeft;
}
// Р С›Р С—РЎР‚Р ВµР Т??&#65533;Р ВµР В»Р ВµР Р…Р С??&#65533;Р Вµ Р С”Р С•Р С•РЎР‚Р Т??&#65533;Р С??&#65533;Р Р…Р В°РЎвЂљРЎвЂ№ РЎРЊР В»Р ВµР С?&#65533;Р ВµР Р…РЎвЂљР В° Р С—Р С• Р Р†Р ВµРЎР‚РЎвЂљР С??&#65533;Р С”Р В°Р В»Р С??&#65533;
function pageY(elem) {//alert(elem.id);
	return elem.offsetParent ?
	elem.offsetTop + pageY( elem.offsetParent ) :
	elem.offsetTop;
}
//Р Р…Р В°Р Р†Р С??&#65533;Р С–Р В°РЎвЂ Р С??&#65533;РЎРЏ РЎРѓ Р С—Р С•Р С?&#65533;Р С•РЎвЂ°РЎРЉРЎР‹ РЎРѓРЎвЂљРЎР‚Р ВµР В»Р С•Р С”
function NavigateThrough(event) {
	if (!document.getElementById) return;
	if (window.event) event = window.event;
	if (event.ctrlKey) {
		var link = null;
		var href = null;
		switch (event.keyCode ? event.keyCode : event.which ? event.which : null) {
			case 0x25: link = document.getElementById ('PrevLink'); break;
			case 0x27: link = document.getElementById ('NextLink'); break;
			case 0x24: href = '/'; break;
		}
		if (link && link.href) document.location = link.href;
		if (href) document.location = href;
	}			

}
function stopEvent(object, e) {
if(!e) e = window.event;
if(e.stopPropagation) e.stopPropagation();
else e.cancelBubble = true;
}

/**
 * Copyright (c) 2007-2012 Ariel Flesler - aflesler(at)gmail(dot)com | http://flesler.blogspot.com
 * Dual licensed under MIT and GPL.
 * @author Ariel Flesler
 * @version 1.4.3.1
 */
;(function($){var h=$.scrollTo=function(a,b,c){$(window).scrollTo(a,b,c)};h.defaults={axis:'xy',duration:parseFloat($.fn.jquery)>=1.3?0:1,limit:true};h.window=function(a){return $(window)._scrollable()};$.fn._scrollable=function(){return this.map(function(){var a=this,isWin=!a.nodeName||$.inArray(a.nodeName.toLowerCase(),['iframe','#document','html','body'])!=-1;if(!isWin)return a;var b=(a.contentWindow||a).document||a.ownerDocument||a;return/webkit/i.test(navigator.userAgent)||b.compatMode=='BackCompat'?b.body:b.documentElement})};$.fn.scrollTo=function(e,f,g){if(typeof f=='object'){g=f;f=0}if(typeof g=='function')g={onAfter:g};if(e=='max')e=9e9;g=$.extend({},h.defaults,g);f=f||g.duration;g.queue=g.queue&&g.axis.length>1;if(g.queue)f/=2;g.offset=both(g.offset);g.over=both(g.over);return this._scrollable().each(function(){if(e==null)return;var d=this,$elem=$(d),targ=e,toff,attr={},win=$elem.is('html,body');switch(typeof targ){case'number':case'string':if(/^([+-]=)?\d+(\.\d+)?(px|%)?$/.test(targ)){targ=both(targ);break}targ=$(targ,this);if(!targ.length)return;case'object':if(targ.is||targ.style)toff=(targ=$(targ)).offset()}$.each(g.axis.split(''),function(i,a){var b=a=='x'?'Left':'Top',pos=b.toLowerCase(),key='scroll'+b,old=d[key],max=h.max(d,a);if(toff){attr[key]=toff[pos]+(win?0:old-$elem.offset()[pos]);if(g.margin){attr[key]-=parseInt(targ.css('margin'+b))||0;attr[key]-=parseInt(targ.css('border'+b+'Width'))||0}attr[key]+=g.offset[pos]||0;if(g.over[pos])attr[key]+=targ[a=='x'?'width':'height']()*g.over[pos]}else{var c=targ[pos];attr[key]=c.slice&&c.slice(-1)=='%'?parseFloat(c)/100*max:c}if(g.limit&&/^\d+$/.test(attr[key]))attr[key]=attr[key]<=0?0:Math.min(attr[key],max);if(!i&&g.queue){if(old!=attr[key])animate(g.onAfterFirst);delete attr[key]}});animate(g.onAfter);function animate(a){$elem.animate(attr,f,g.easing,a&&function(){a.call(this,e,g)})}}).end()};h.max=function(a,b){var c=b=='x'?'Width':'Height',scroll='scroll'+c;if(!$(a).is('html,body'))return a[scroll]-$(a)[c.toLowerCase()]();var d='client'+c,html=a.ownerDocument.documentElement,body=a.ownerDocument.body;return Math.max(html[scroll],body[scroll])-Math.min(html[d],body[d])};function both(a){return typeof a=='object'?a:{top:a,left:a}}})(jQuery);

/**
 * Cookie plugin
 *
 * Copyright (c) 2006 Klaus Hartl (stilbuero.de)
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */

/**
 * Create a cookie with the given name and value and other optional parameters.
 *
 * @example $.cookie('the_cookie', 'the_value');
 * @desc Set the value of a cookie.
 * @example $.cookie('the_cookie', 'the_value', { expires: 7, path: '/', domain: 'jquery.com', secure: true });
 * @desc Create a cookie with all available options.
 * @example $.cookie('the_cookie', 'the_value');
 * @desc Create a session cookie.
 * @example $.cookie('the_cookie', null);
 * @desc Delete a cookie by passing null as value. Keep in mind that you have to use the same path and domain
 *       used when the cookie was set.
 *
 * @param String name The name of the cookie.
 * @param String value The value of the cookie.
 * @param Object options An object literal containing key/value pairs to provide optional cookie attributes.
 * @option Number|Date expires Either an integer specifying the expiration date from now on in days or a Date object.
 *                             If a negative value is specified (e.g. a date in the past), the cookie will be deleted.
 *                             If set to null or omitted, the cookie will be a session cookie and will not be retained
 *                             when the the browser exits.
 * @option String path The value of the path atribute of the cookie (default: path of page that created the cookie).
 * @option String domain The value of the domain attribute of the cookie (default: domain of page that created the cookie).
 * @option Boolean secure If true, the secure attribute of the cookie will be set and the cookie transmission will
 *                        require a secure protocol (like HTTPS).
 * @type undefined
 *
 * @name $.cookie
 * @cat Plugins/Cookie
 * @author Klaus Hartl/klaus.hartl@stilbuero.de
 */

/**
 * Get the value of a cookie with the given name.
 *
 * @example $.cookie('the_cookie');
 * @desc Get the value of a cookie.
 *
 * @param String name The name of the cookie.
 * @return The value of the cookie.
 * @type String
 *
 * @name $.cookie
 * @cat Plugins/Cookie
 * @author Klaus Hartl/klaus.hartl@stilbuero.de
 */
jQuery.cookie = function(name, value, options) {
    if (typeof value != 'undefined') { // name and value given, set cookie
        options = options || {};
        if (value === null) {
            value = '';
            options.expires = -1;
        }
        var expires = '';
        if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
            var date;
            if (typeof options.expires == 'number') {
                date = new Date();
                date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
            } else {
                date = options.expires;
            }
            expires = '; expires=' + date.toUTCString(); // use expires attribute, max-age is not supported by IE
        }
        // CAUTION: Needed to parenthesize options.path and options.domain
        // in the following expressions, otherwise they evaluate to undefined
        // in the packed version for some reason...
        var path = options.path ? '; path=' + (options.path) : '';
        var domain = options.domain ? '; domain=' + (options.domain) : '';
        var secure = options.secure ? '; secure' : '';
        document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
    } else { // only name given, get cookie
        var cookieValue = null;
        if (document.cookie && document.cookie != '') {
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = jQuery.trim(cookies[i]);
                // Does this cookie string begin with the name we want?
                if (cookie.substring(0, name.length + 1) == (name + '=')) {
                    cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                    break;
                }
            }
        }
        return cookieValue;
    }
};

(function($) {
	var defaults = {
		minHeight:50,
		maxHeight:300
	};
	$.fn.resizeTextarea = function(params) {
		var options = $.extend({}, params, defaults);
		$(this).each(function() {
			var $this = $(this),
				methods = {
					setHeight:function() {
						$this.height(options.minHeight);
						if ($this.height() != getScrollHeight($this)) {
							if (getScrollHeight($this) > options.maxHeight) {$this.height(options.maxHeight);}
							else {$this.height(getScrollHeight($this) || options.minHeight);}
						}
					}
				};
			methods.setHeight();
			$this.attr({rows:options.minHeight});
			
			$this.keyup(function(){methods.setHeight();});
		});
		return $(this);
	};
})(jQuery);

function getScrollHeight($elem) {
	$elem.scrollTop($elem.get(0).scrollHeight);
	var result = $elem.scrollTop() + $elem.height();
	$elem.scrollTop(0);
	
	return result;
}

function openPrintWindow($a) {
	var $recipe = $(".recipe");
	var $title = $recipe.find(".title");
	var $instructions = $recipe.find(".instructions");
	var $stages = [];
	
	var heading = [
		["Первый", "Второй", "Третий", "Четвёртый", "Пятый", "Шестой", "Седьмой", "Восьмой", "Девятый"],
		["Одиннадцатый", "Двенадцатый", "Тринадцатый", "Четырнадцатый", "Пятнадцатый", "Шестнадцатый", "Семнадцатый", "Восемнадцатый", "Девятнадцатый"],
		["Десятый", "Двадцатый", "Тридцатый", "Сороковой", "Пятидесятый", "Шестидесятый", "Семидесятый", "Восьмидесятый", "Девяностый"],
		["", "Двадцать", "Тридцать", "Сорок", "Пятьдесят", "Шестьдесят", "Семьдесят", "Восемьдесят", "Девяносто"],
		["первый", "второй", "третий", "четвёртый", "пятый", "шестой", "седьмой", "восьмой", "девятый"]
	];
	
	$instructions.find(".stage").each(function() {
		var $div = $('<div></div>').html($(this).html());
		$div.find(".body").append('<div class="i-clearfix"></div>');
		var $image = $div.find(".image");
		$image.html(resizeImage($image.html()));
		
		$stages.push($div.html());
	});
	
	var printWindow = window.open("", "printWindow", "width=800, height=800,toolbar=0,scrollbars=yes,status=0,directories=0,location=0,menubar=0,resizable=0");
	printWindow.document.write(compileBody());
	
	function resizeImage(html) {
		var $block = $('<div></div>').html(html);
		var $image = $block.find("img");
		var $screen = $block.find(".screen");
		var $div = $screen.children("div");
		
		var width = parseInt($div.css("width"));
		var height = parseInt($div.css("height"));
		
		var ratio = 200/width;
		
		var newWidth = width * ratio;
		var newHeight = height * ratio;
		
		$div.css({width: width * ratio + "px", height: height * ratio + "px"});
		$image.attr({width: width * ratio, height: height * ratio});
		
		return $block.html();
	}
	
	function compileBody() {
		
		var bodyObj = {
			browser: $.browser,
			title: $("title").text(),
			h1: $("h1").text(),
			recipeInfo: $recipe.find(".recipe_info").html(),
			needed: $title.find(".needed").find("table").html(),
			titleImage: resizeImage($title.find(".image").html()),
			description: $recipe.find(".description").html(),
			stages: $stages			
		};
		
		var template = document.getElementById('print-recipe').innerHTML;
		var compiled = tmpl(template);
		return compiled(bodyObj);
	}
}

// esc("<script>") = &lt;script%gt;
function esc(string) {
  return (''+string).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#x27;').replace(/\//g,'&#x2F;');
}

// JavaScript micro-templating, from underscore.js
function tmpl(str) {
  var tmpl = 'var __p=[],print=function(){__p.push.apply(__p,arguments);};' +
    'with(obj||{}){__p.push(\'' +
    str.replace(/\\/g, '\\\\')
       .replace(/'/g, "\\'")
       .replace(/<%-([\s\S]+?)%>/g, function(match, code) {
         return "',esc(" + code.replace(/\\'/g, "'") + "),'";
       })
       .replace(/<%=([\s\S]+?)%>/g, function(match, code) {
         return "'," + code.replace(/\\'/g, "'") + ",'";
       })
       .replace(/<%([\s\S]+?)%>/g, function(match, code) {
         return "');" + code.replace(/\\'/g, "'")
                            .replace(/[\r\n\t]/g, ' ') + ";__p.push('";
       })
       .replace(/\r/g, '\\r')
       .replace(/\n/g, '\\n')
       .replace(/\t/g, '\\t')
       + "');}return __p.join('');";
  var func = new Function('obj', tmpl);
  return function(data) {
    return func.call(this, data);
  };
};

jQuery.fn.addtocopy = function (usercopytxt) {
    var options = { htmlcopytxt: '<br>More: <a href="' + window.location.href + '">' + window.location.href + '</a><br>', minlen: 25, addcopyfirst: false }
    $.extend(options, usercopytxt);
    var copy_sp = document.createElement('span');
    copy_sp.id = 'ctrlcopy';
    copy_sp.innerHTML = options.htmlcopytxt;
    return this.each(function () {
        $(this).mousedown(function () { $('#ctrlcopy').remove(); });
        $(this).mouseup(function () {
            if (window.getSelection) {	//good times 
                var slcted = window.getSelection();
                var seltxt = slcted.toString();
                if (!seltxt || seltxt.length < options.minlen) return;
                var nslct = slcted.getRangeAt(0);
                seltxt = nslct.cloneRange();
                seltxt.collapse(options.addcopyfirst);
                seltxt.insertNode(copy_sp);
                if (!options.addcopyfirst) nslct.setEndAfter(copy_sp);
                slcted.removeAllRanges();
                slcted.addRange(nslct);
            } else if (document.selection) {	//bad times
                var slcted = document.selection;
                var nslct = slcted.createRange();
                var seltxt = nslct.text;
                if (!seltxt || seltxt.length < options.minlen) return;
                seltxt = nslct.duplicate();
                seltxt.collapse(options.addcopyfirst);
                seltxt.pasteHTML(copy_sp.outerHTML);
                if (!options.addcopyfirst) { nslct.setEndPoint("EndToEnd", seltxt); nslct.select(); }
            }
        });
    });
}

function DoYouKnowThat(id) {
	var self = this;
	
	init();
	
	function init() {
		if(!document.getElementById(id)) return false;
		
		self.$elem = $("#" + id);
		handleEvents();
	}
	
	function handleEvents() {
		initMoreButton();
		
		function initMoreButton() {
			self.$elem.find(".b-facts__more__link").click(clickMoreLink);
			
			function clickMoreLink() {
				if(!self.factsArray) {
					getFactsArray();
					return false;
				}
					
				showNextFact();
				trackEvent();
				
				function getFactsArray() {
					$.getJSON(
						"/php/get_more_facts.php",
						success
					)
					
					function success(data, textStatus, jqXHR) {
						self.factsArray = data.facts;
						showNextFact();
					}
				}
				
				function trackEvent() {
					if(!window.ga) return;
					ga('send', 'event', 'Знаете ли вы что', 'Нажали кнопку Еще', self.$elem.find(".b-facts__item").text());
				}
				
				return false;
			}
		}
	}
	
	function showNextFact() {
		if(!self.factsArray) {
			self.$elem.find(".b-facts__more__link").click();
			return false;
		}
		
		var factObject = getNextFactObject();
		replaceFactWithNew();
		
		function replaceFactWithNew() {
			self.$elem.find(".b-facts__item").fadeOut(500, function() {
				self.$elem.find(".b-facts__item")
					.attr({"data-id": factObject.id})
					.html(factObject.text)
					.fadeIn(500);
			});
		}
		
		function getNextFactObject() {
			var currentFactObject = {
					id: self.$elem.find(".b-facts__item").attr("data-id"),
					text: self.$elem.find(".b-facts__item").text()
				}
			
			while(self.factsArray[0].id == currentFactObject.id) {
				moveFactObjectToTheEnd();
			}
			
			var resultFactObject = self.factsArray[0];
			moveFactObjectToTheEnd();
			
			function moveFactObjectToTheEnd() {
				self.factsArray.push(self.factsArray.shift());
			}
			
			return resultFactObject;
		}
		
	}
}

function FilterRecipes() {
	var self = this;
	
	init();
	
	function init() {
		initFilter();
	}
	
	function initFilter() {
		$("#filter_recipes").delegate(".item a", "click", clickItem);
		
		function clickItem() {
			var $this = $(this),
				col = 4,
				type = getType(),
				array = getArray(type);
			
			makeHtml();
			
			function getType() {
				var result = $this.parent()[0].className.split(/\s*item\s*/);
				for(var i = 0; i < result.length; i++) {
					if(result[i] != "") {
						result = result[i];
						break;
					}
				}
				
				return result;
			}
			
			function getArray(type) {
				switch(type) {
					case "cuisine":
						return cuisineArray;
					
					case "dish":
						return dishTypeArray;
					
					case "ingredient":
						return mainIngredientArray;
					
					case "tag":
						return tagArray;
				}
				
				return cuisineArray;
			}
			
			function makeHtml() {
				
				if($this.hasClass("open")) {
					$("#filter_list").slideUp("middle").empty();
					$this.removeClass("open");
					document.getElementById("filter_list").className = "";
					return;
				}
				
				var html = "",
					num = Math.ceil(array[1].length/col);
				
				html += '<div class="pad"><table>';
								
				for(var i = 0; i < num; i++) {
					html += '<tr>';
									
					for(var j = 0; j < col; j++) {
						x = num * j + i;
						
						if(array[0][x]) {
							html += '<td><a href="#" rel="' + array[0][x] + '">' + array[1][x] + '</a></td>';
						}
						else {
							html += '<td><span></span></td>';
						}
					}
					
					html += '</tr>';
				}
				
				html += '</table></div>';
				$("#filter_list").html(html).slideDown("middle");
				$("#filter_recipes .item a").removeClass("open");
				$this.addClass("open");
				document.getElementById("filter_list").className = type;
			}
			return false;
		}
	}
}

function Filter($elem) {
	var self = this;
	
	init();
	
	function init() {
		self.$elem = $elem;
		self.$lists = self.$elem.find(".b-filter__lists");
		self.$list = self.$elem.find(".b-filter__list");
		initFilter();
	}
	
	function initFilter() {
		$elem.delegate(".b-filter__item__button", "click", clickItem);
		
		function clickItem() {
			var $button = $(this),
				col = parseInt(self.$elem.attr("data-col"), 10),
				array = window[$button.closest(".b-filter__item").attr("data-array")];
			
			if($button.hasClass("open")) {
				self.$list.slideUp("middle").empty();
				$button.removeClass("open");
			} else {
				makeHtml();
			}
			
			function makeHtml() {
				
				var html = "",
					num = Math.ceil(array.length/col);
				
				html += '<div class="pad"><table>';
								
				for(var i = 0; i < num; i++) {
					html += '<tr>';
									
					for(var j = 0; j < col; j++) {
						x = num * j + i;
						
						if(array[x]) {
							html += '<td><a href="#" rel="' + array[x].id + '">' + array[x].name + '</a></td>';
						}
						else {
							html += '<td><span></span></td>';
						}
					}
					
					html += '</tr>';
				}
				
				html += '</table></div>';
				self.$list.html(html).slideDown("middle");
				self.$elem.find(".b-filter__item__button").removeClass("open");
				$button.addClass("open");
			}
			return false;
		}
	}
}

function FilterList() {
	var self = this;
	self.recipesPerPage = 27;
	self.makeRequest = true;
	
	init();
	
	function init() {
		if(!document.getElementById("filter_list")) return false;
		
		pageLoad();
		initList();
	}
	
	function pageLoad() {
		var urlParams = analyzePageUrl();
		pushHistory(urlParams);
		makeRequest(urlParams);
		
		function analyzePageUrl() {
			var object = getDataFromUrl();
			var result = {};
			
			for(var key in object) {
				if(key == "cuisine" || key == "dish" || key == "ingredient" || key == "tag") {
					result.type = key;
					result.data = object[key];
				}
			}
			
			result.num = object.num;
			
			return result;
			
			function getDataFromUrl() {
				var search = window.location.search;
				var regExp = /([a-z0-9]+)=([a-z0-9]+)/gi;
				var array;
				var object = {};
				while((array = regExp.exec(search)) != null) {
					object[array[1]] = array[2];
				}
				
				return object;
			}
		}
		
		function pushHistory(urlParams) {
			var params = {};
			
			for(var key in urlParams) {
				params[key] = urlParams[key];
			}
			
			if(!params.num) {//loaded page with no params at all
				params.num = $("#recipe_feed_block .recipe_list_item").size();
			}
			
			params.allRecipesNum = parseInt($("#fc_statistics span.num").text(), 10);
			params.recipes = getRecipesObjectFromHtml();
			
			var History = window.History;
			if (!History.enabled) return;
		
			History.pushState(params);
			//History.log(History.getState().data, "pushHistory");
		}
		
		function makeRequest(params) {
			if(params.type && params.data) {//show chosen group of recipes
				firstRecipesRequest(params.type, params.data, params.num, true);
			} else if(params.num) {//show last recipes
				lastRecipesRequest(params.num, true)
			}
		}
	}
		
	function initList() {
		$("#filter_list")
			.click(function(e) {
				e.stopPropagation();
			})
			.delegate("a", "click", clickListLink);
		
		setHistoryAdapter();
		$("#get_more_recipes a").click(clickMoreRecipes);
	}
		
	function clickListLink() {
		var type = document.getElementById("filter_list").className,
			typeId = this.getAttribute('rel');
		
		firstRecipesRequest(type, typeId);
		$("#filter_list").hide().empty();
		
		return false;
	}
	
	function firstRecipesRequest(type, typeId, num, replaceFlag) {
		var requestRecipesNum = self.recipesPerPage;
		if(num) requestRecipesNum = num;
		
		$.ajax({
			url: "/php/all_recipes_result.php",
			dataType: "json",
			data: "num=" + requestRecipesNum + "&type=" + type + "&data=" + typeId,
			beforeSend: beforeAjax,
			success: successAjax
		});
		
		function beforeAjax() {
			showPreloader();
		}
		
		function successAjax(data) {
			allRecipesResult = data.id;
			var recipesNum = parseInt(allRecipesResult.length, 10) + parseInt(data.recipes.length, 10);
			
			self.makeRequest = false;
			
			setWindowHistory({
				type: type,
				data: typeId,
				num: data.recipes.length,
				recipes: data.recipes,
				allRecipesNum: recipesNum
			}, replaceFlag);
			
			setTimeout(function() {showRecipesFromState(window.History.getState())}, 0);//setTimeout for IE
		}
	}
	
	function lastRecipesRequest(num, replaceFlag) {
		var requestRecipesNum = self.recipesPerPage;
		if(num) requestRecipesNum = num;
		
		$.ajax({
			url: "/php/last_recipes_result.php",
			dataType: "json",
			data: "num=" + requestRecipesNum,
			beforeSend: beforeAjax,
			success: successAjax
		});
		
		function beforeAjax() {
			showPreloader();
		}
		
		function successAjax(data) {
			var recipesNum = parseInt(data.id.length, 10) + parseInt(data.recipes.length, 10);
			
			self.makeRequest = false;
			
			setWindowHistory({
				num: data.recipes.length,
				recipes: data.recipes,
				allRecipesNum: recipesNum
			}, replaceFlag);
			
			setTimeout(function() {showRecipesFromState(window.History.getState())}, 0);//setTimeout for IE
		}
	}
	
	function showPreloader() {
		$("#recipe_feed_block").empty().html('<div class="preloader"><img src="/images/preloader.gif" width="281" height="52" alt="" /></div>');
	}
	
	function clickMoreRecipes() {
		var idPortion = [];
		
		for(var i = 0; i < self.recipesPerPage; i++) {
			if(allRecipesResult[0]) {
				idPortion[i] = allRecipesResult.shift();
			}
		}
		
		$.ajax({
			url: "/php/get_more_recipes.php",
			dataType: "json",
			data: "id=" + idPortion,
			beforeSend: function() {
				$("#get_more_recipes").addClass("preload");
			},
			success: function(data){
				$("#get_more_recipes").removeClass("preload");
				var div = $('<div class="block" style="display:none;"></div>');
				
				var html = '';
				for(var i=0; i < data.recipes.length; i++) {
					html += '<div class="item recipe_list_item"><div class="photo"><a href="'+data.recipes[i].href+'" title="'+data.recipes[i].name+'"><img src="'+data.recipes[i].src+'" width="170" alt="'+data.recipes[i].name+'" /></a></div><h5><a href="'+data.recipes[i].href+'">'+data.recipes[i].name+'</a></h5><p class="author">От: '+data.recipes[i].author+'</p><p class="info"><span class="comments_icon" title="Оставить отзыв"><noindex><a href="'+data.recipes[i].href+'#comments">'+data.recipes[i].comments+'</a></noindex></span></p></div>';
				}
				div.html(html);
				$("#recipe_feed_block").find("div.clear").before(div).end().find("div.block:last div.recipe_list_item div.photo a").each(function() {
					var $this = $(this),
						$img = $this.children("img"),
						img = new Image();
						
					img.src = $img.attr("src");
					var hei = Math.floor(img.height*$img.attr("width")/img.width);
					if(hei > 0) {
						$img.css({marginTop:(parseInt($this.css("height"))/2-hei/2)+"px"});
					}
					else {
						$img.load(function() {
							var hei = Math.floor(img.height*$img.attr("width")/img.width);
							if(hei>0) {$img.css({marginTop:(parseInt($this.css("height"))/2-hei/2)+"px"});}
						});
					}
				}).end()
				.find("div.block:last").css({opacity:0}).show().animate({opacity:1}, 500);
				
				$.scrollTo($("#recipe_feed_block div.block:last div.item:first"), 1000);
				
				updateHistory(data);
				
				showHideGetMoreButton();
				
				window.upButton.styleElements();
			}
		});
		
		return false;
		
		function updateHistory(moreData) {
			var History = window.History;
			if (!History.enabled) return;
			
			self.makeRequest = false;
			var State = History.getState();
			State.data.num = $("#recipe_feed_block .recipe_list_item").size();
			
			if(State.data.recipes && moreData.recipes) {
				State.data.recipes = State.data.recipes.concat(moreData.recipes);
			}
			
			setWindowHistory(State.data, false);
		}
	}
	
	function setWindowHistory(stateObj, replaceFlag) {
		var History = window.History; // Note: We are using a capital H instead of a lower h
		if (!History.enabled) return;
		
		var stateUrl = formStateUrl();
		
		if(replaceFlag) {
			History.replaceState(stateObj, "Рецепты", stateUrl);
			//History.log(History.getState().data, "setWindowHistory replace");
			return;
		}
		
		History.pushState(stateObj, "Рецепты", stateUrl);
		//History.log(History.getState().data, "setWindowHistory");
		
		function formStateUrl() {
			var url = [];
			if(stateObj.type && stateObj.data) {
				url.push(stateObj.type + "=" + stateObj.data);
			}
			url.push("num=" + stateObj.num);
			
			url = url.join("&");
			if(url != "") {
				url = "?" + url;
			}
			
			return url;
		}
	}
		
	function setHistoryAdapter() {
		var History = window.History;
		if (!History.enabled) return;
		
		History.Adapter.bind(window, 'statechange', function() {
			if(self.makeRequest) {//for- and back- browser navigation
				var State = History.getState();
				
				if(State.data.recipes) {
					showRecipesFromState(State);
				} else {
					firstRecipesRequest(null, null);
				}
				//History.log(History.getState().data);
			}
			
			self.makeRequest = true;
		});
	}
	
	function showRecipesFromState(State) {//request for recipes of new type (dish, ingredient, etc)
		if(State.data.type && State.data.data) {
			var array = getTypeArray(State.data.type);
			var text = getTextFromData(array, State.data.data);
			highlightButton($("." + State.data.type + " a"), text);
		} else {
			highlightButton($(), "");
		}
		
		renderRecipes(State.data.recipes);
		showRecipesNum(State.data.allRecipesNum);
		highlightTopbarButton();
		showHideGetMoreButton();
	}
	
	function showHideGetMoreButton() {
		var History = window.History;
		if (History.enabled) {
			var State = History.getState();
			var difference = State.data.allRecipesNum - State.data.recipes.length;
		} else {
			var difference = allRecipesResult.length;
		}
		if(difference == 0) {
			$("#get_more_recipes").hide();
		} else {
			$("#get_more_recipes").show();
		}
	}
	
	function highlightTopbarButton() {
		$("#topbar div.item").each(function() {
			if($(this).text() == "Рецепты") {
				if(window.search && window.search != "") {
					$(this).html('<a href="' + window.pathname + '"><span>Рецепты</span></a>');
				} else {
					$(this).html('<span><span>Рецепты</span></span>');
				}
			}
		});
	}
	
	function getTypeArray(type) {
		switch(type) {
			case "cuisine":
				return cuisineArray;
			
			case "dish":
				return dishTypeArray;
			
			case "ingredient":
				return mainIngredientArray;
			
			case "tag":
				return tagArray;
		}
	}
	
	function getTextFromData(array, data) {
		for(var i = 0; i < array[0].length; i++) {
			if(array[0][i] == data) {
				return array[1][i];
			}
		}
	}
	
	function renderRecipes(recipesArray) {
		var html = "";
		
		for(var i = 0; i < recipesArray.length; i++) {
			html += '<div class="item recipe_list_item"><div class="photo"><a href="'+recipesArray[i].href+'" title="'+recipesArray[i].name+'"><img src="'+recipesArray[i].src+'" width="170" alt="'+recipesArray[i].name+'" /></a></div><h5><a href="'+recipesArray[i].href+'">'+recipesArray[i].name+'</a></h5><p class="author">От: '+recipesArray[i].author+'</p><p class="info"><span class="comments_icon" title="Оставить отзыв"><noindex><a href="'+recipesArray[i].href+'#comments">'+recipesArray[i].comments+'</a></noindex></span></p></div>';
		}
		
		html += '<div class="clear"></div>';
		
		$("#recipe_feed_block")
			.html(html)
			.find("div.recipe_list_item div.photo a").each(function() {
				var $this = $(this),
					$img = $this.children("img"),
					img = new Image();
					
				img.src = $img.attr("src");
				var hei = Math.floor(img.height * $img.attr("width") / img.width);
				if(hei > 0) {
					$img.css({
						marginTop:(parseInt($this.css("height"))/2 - hei/2) + "px"
					});
				} else {
					$img.load(function() {
						var hei = Math.floor(img.height * $img.attr("width") / img.width);
						if(hei > 0) {
							$img.css({
								marginTop:(parseInt($this.css("height"))/2 - hei/2) + "px"
							});
						}
					});
				}
			});
	}
	
	function highlightButton($link, text) {
		$("#filter_recipes a.active").removeClass("active").find("span.bg span").text("—");
		$link.removeClass("open").addClass("active").find("span.bg span").text(text);
	}
	
	function getRecipesObjectFromHtml() {
		var result = [];
		
		$("#recipe_feed_block div.item").each(function() {
			var $item = $(this);
			
			result.push({
				name: $item.find("h5 a").text(),
				href: $item.find("h5 a").attr("href"),
				src: $item.find("img").attr("src"),
				author: $item.find(".author").text().substring(4),
				comments: $item.find(".comments_icon a").text()
			});
		});
		
		return result;
	}
	
	function showRecipesNum(recipesNum) {
		$("#fc_statistics").find("span.num").text(recipesNum).end().find("span.word").text(recipeWord(recipesNum));
			
		function recipeWord(num) {
			if (/(10|11|12|13|14|15|16|17|18|19)$/.test(num)) {
				return 'рецептов';
			} else if (/.*1$/.test(num)) {
				return 'рецепт';
			} else if (/[2-4]$/.test(num)) {
				return 'рецепта';
			} else {
				return 'рецептов';
			}
		}
	}
}

function valignRecipePreviewPhoto() {
	
	$("div.recipe_list_item div.photo a, .b-recipe-preview__photo__link").each(function() {
		var $this = $(this),
			$img = $this.children("img"),
			img = new Image();
			
		img.src = $img.attr("src");
		var hei = Math.floor(img.height*$img.attr("width")/img.width);
		if(hei>0) {$img.css({marginTop:(parseInt($this.css("height"))/2-hei/2)+"px"});}
		else {
			$img.load(function() {
				var hei = Math.floor(img.height*$img.attr("width")/img.width);
				if(hei>0) {$img.css({marginTop:(parseInt($this.css("height"))/2-hei/2)+"px"});}
			});
		}
	});
	
}

function favoriteButtons() {
	$(".b-favorite-button").hover(
		function() {
			$(this).addClass("i-hover").stop().animate({width: "79px"}, function() {
				$(this).find(".b-favorite-button__text").css({display: "inline-block"});
			});
		},
		function() {
			$(this).find(".b-favorite-button__text").hide();
			$(this).stop().animate({width: "35px"}, function() {
				$(this).removeClass("i-hover")
			});
		}
	).click(clickFavorite);

	function clickFavorite() {
		if(!window.ga) return;
		var $button = $(this);

		if($button.hasClass("i-remove-favorite")) {
			_gaq.push(['_trackEvent', 'Избранное', 'Удалили из избранного']);
			ga('send', 'event', 'Избранное', 'Удалили из избранного');
		} else {
			_gaq.push(['_trackEvent', 'Избранное', 'Добавили в избранное']);
			ga('send', 'event', 'Избранное', 'Добавили в избранное');
		}
	}
}

function ajaxError(a, b, c) {
	if(window.console) {
		console.log(a);
		console.log(b);
		console.log(c);
	}
}