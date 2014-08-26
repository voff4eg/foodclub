$(document).ready(function(){
	$(document).pngFix();
	WindowOnLoad();
	if(document.getElementById("example_link")) {
		var exampleLinkNumber = Math.floor((Math.random()*(exampleLinkArray.length-0.01)));
		$("#example_link").text(exampleLinkArray[exampleLinkNumber])
		$("#example_link").click(function() {
			var exampleLinkString = new String($(this).text());
			var firstLetter = exampleLinkString.substr(0, 1).toUpperCase();
			exampleLinkString = firstLetter + exampleLinkString.substr(1, exampleLinkString.length);
			document.getElementById("recipe_search_field").value = exampleLinkString;
			$("#recipe_search_field").focus();
			return false;
		});
	}
	if (String(window.location).search("#add_opinion") != -1) {
		$("#opinion_form").find("textarea").focus();
	}
	$("a[href='#add_opinion']").click(function() {
		$("#opinion_form").find("textarea").focus();
		return false;
	});
	$("#choose_kitchen a").click(function() {
		showList(kitchenArray[1], chosenSearchKitchen, "Кухни");
		showHideLayer("top_layer");
		showHideLayer("search_list_layer");
		return false;
	});
	$("#choose_dish a").click(function() {
		showList(dishArray[1], chosenSearchDish, "Тип блюда");
		showHideLayer("top_layer");
		showHideLayer("search_list_layer");
		return false;
	});
	$("#choose_ingredient a").click(function() {
		showIngredientsLayer();
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
	$("div.photo").hover(function() {
		$(this).children("div.big_photo").animate({opacity:"show"}, "middle");
	}, function() {
		$(this).children("div.big_photo").animate({opacity:"hide"}, "fast");
	});
	$("#recipe_line_block").find("div.photo").children("img").mouseover(function() {
		var topValue = "-121px";
		var leftValue = pageX(this) - pageX(document.getElementById("recipe_line_block")) - 12;
		$("#" + $(this).parent().attr("id") + "_big").children("div").css({top:topValue, left:leftValue});
		$("#" + $(this).parent().attr("id") + "_big").animate({opacity:"show"}, "middle");
		
	});
	$("#recipe_line_block").find("div.big_photo").hover(function() {
	}, function() {
		$(this).animate({opacity:"hide"}, "fast");
	});
	$("#recipe_line_block").find("div.pointer").hover(function() {
		$(this).addClass("hover");
	}, function() {
		$(this).removeClass("hover");
	})
	$("#recipe_line_block div.relative div.backward").click(function() {
		stepcarousel.stepBy('recipe_line', -1);
	});
	$("#recipe_line_block div.relative div.forward div").click(function() {
		stepcarousel.stepBy('recipe_line', 1);
	});
	$("div.search_field form input.button").click(function() {
		searchForm(this);
		return false;
	});
	$("div.search_field div.search_delete").click(function() {
		$(this).siblings("form").children("input.text").attr({value:""});
		clearArray(chosenSearchKitchen);
		clearArray(chosenSearchDish);
		clearArray(chosenSearchIngredient[0]);
		clearArray(chosenSearchIngredient[1]);
		$("#recipe_search_field").focus();
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
	$("#topbar").find("a.enter").click(function() {
		showHideLayer('top_layer');
		showHideLayer ('authorization_layer');
		$("#lj_id_field").children("div.fields").find("input").focus();
		return false;
	});
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
			if ($(this).attr("id") == "recipe_search_field") {
				searchForm(this);
				return false;
			}
			if (!$(this).attr("name") || $(this).parents("form").attr("name") == "authorization" || $(this).parents("form").attr("name") == "open_id") {
				checkALayer(this);
			}
			else {
				checkForm(this);
			}
		}
	});
//	$("#authorization_field").find("input.text").unbind("keypress").bind("keypress", (function(e){
//		alert("");
//	}));
	$("#opinion_block").find("div.edit").click(function() {
		$("#opinion_block").find("div.opinion").removeClass("edit_form");
		$(this).parents("div.opinion").addClass("edit_form");
	});
	$("#opinion_block").find("div.button").click(function() {
		checkForm(this);
	});
	$("#opinion_block").find("div.delete").click(function() {
		if (!confirm("Удалить отзыв?")) {
			return false;
		}
		else {
			var opinionId = $(this).parents("div.opinion").attr("id");
			var recipeId = $("#content").children("div.recipe").attr("id");
			var newHref = String(window.location).split(document.domain)[0] + document.domain + "/comment.php?cId=" + opinionId + "&rId=" + recipeId + "&a=d";
			window.location.href = newHref;
		}
	});
	$("#opinion_block div.close_icon").click(function() {
		$(this).parents("div.opinion").removeClass("edit_form");
	});
	$("div.comments_block").find("div.reply_string").children("a").click(function() {
		$("div.comments_block").find("div.comment").removeClass("edit_form");
		$("div.comments_block").find("div.reply_string").css({visibility:"visible"});
		$("#reply_form").css({display:"none"}).remove().insertAfter($(this).parent().parent().parent().parent()).slideDown("middle");
		$("#reply_form div.close_icon div").click(function() {
			$("#reply_form").prev().find("div.reply_string").css({visibility:"visible"});
			$("#reply_form").slideUp("middle");
		});
		$("#reply_form form div.button").click(function() {
			$(this).parents("form").submit();
		});
		$(this).parent().css({visibility:"hidden"});
		var parentId = $(this).parents("div.comment").attr("id");
		$("#reply_form").find("input[name='parentId']").attr({value:parentId});
		return false;
	});
	$("div.comments_block").find("div.edit").click(function() {
		$("div.comments_block").find("div.comment").removeClass("edit_form");
		$(this).parents("div.comment").addClass("edit_form");
		$("div.comments_block").find("div.reply_string").css({visibility:"visible"});
		$("#reply_form").slideUp("middle");
	});
	$("div.comments_block div.close_icon").click(function() {
		$(this).parents("div.comment").removeClass("edit_form");
	});
	$("div.comments_block").find("div.button").click(function() {
		checkForm(this);
	});
	$("#comment_form").find("div.button").click(function() {
		checkForm(this);
	});
	$("div.comments_block").find("div.delete").click(function() {
		if (!confirm("Удалить комментарий?")) {
			return false;
		}
		else {
			var commentId = $(this).parents("div.comment").attr("id");
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
	$("div.file_name img.delete_icon").hover(function() {
		$(this).css({backgroundPosition:"left -7px"});
	}, function() {
		if (deleteConfirm == 0) {
			$(this).css({backgroundPosition:"left top"});
		}
	});
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
		if (this.value == "Название клуба") {
			this.value = "";
		}
	});
	$("#club_search_field").blur(function() {
		if (this.value == "") {
			this.value = "Название клуба";
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
		checkStageForm('dish');
	});
	$("div.file_name img.delete_icon").hover(function() {
		$(this).css({backgroundPosition:"left -7px"});
	}, function() {
		if (deleteConfirm == 0) {
			$(this).css({backgroundPosition:"left top"});
		}
	});
	$("div.stage div.delete_icon div").hover(function() {
		$(this).parent().addClass("attention");
	}, function() {
		if (this.parentNode.className.search("confirm") == -1) {
			$(this).parent().removeClass("attention");
		}
	});
	$("div.stage").find("img.delete").hover(function() {
		$(this).addClass("attention");
	}, function() {
		$(this).removeClass("attention");
	});
	$("div.stage").find("img.delete").click(function() {
		$(this).addClass("confirm");
		if(confirm("Удалить ингредиент?")) {
			deleteIngredient(this);
		}
		else {
			$(this).removeClass("confirm").removeClass("attention");
			return false;
		}
	});
	$("div.scales img").click(function() {
		window.open('/table/','scalesWin','width=800, height=800,toolbar=0,scrollbars=yes,status=0,directories=0,location=0,menubar=0,resizable=0');
	});
	$("#search_helper div.body div.menu div.item a").click(function() {
		$("#search_helper div.body div.search_blocks").css({display:"none"});
		$("#" + $(this).attr("rel")).css({display:"block"});
		$(this).parent().parent().children("div.item").removeClass("act");
		$(this).parent().addClass("act");
	});
	$("#search_helper div.body div.slide_up_button").click(function() {
		$("#search_helper").slideUp("middle", function() {
			showHideLayer("top_layer");
		});
		$("#search_helper div.body div.menu div.item").removeClass("act");
		$("#search_helper div.body div.menu div.item:eq(0)").addClass("act");
		$("#search_helper div.body div.search_blocks").css({display:"none"});
		$("#h_helper").css({display:"block"});
	});
	$("#search_helper_link").click(function() {
		showHideLayer("top_layer");
		$("#search_helper").slideDown("middle");
	});
	$("#i_have_list").find("a.delete").hover(function() {
		$(this).addClass("hover");
	}, function() {
		$(this).removeClass("hover");
	});
});

//удаление фотографии
function deleteStageImage(img_object, img_id) {
	deleteConfirm = 1;
	if (confirm("Удалить изображение?")) {
		window.location.href = window.location + "?id=" + img_id;
	}
	else {
		$(img_object).css({backgroundPosition:"left top"});
		deleteConfirm = 0;
	}
}

function WindowOnLoad() {
	var fileName = $("<div class='new_file_name'>").css({display:"none"});	
	var bb = $("<div class='browse_button'>").append($("<input type='button' value='Обзор'>"));
	var bl = $("<div class='blocker'>");
	var bl2 = $("<div class='blocker2'>");
	$("div.input_file").children("input.text").attr({value:""}).addClass("customFile");
	$("div.input_file").children("input.text").change(function() {
		HandleChanges(this);
	});
	$("div.input_file").append($(bb)).append($(bl)).append($(bl2)).prepend($(fileName));
}
function HandleChanges(input_object) {
	var fileName = $(input_object).parent().children("div.new_file_name");
	file = $(input_object).attr("value");
	reWin = /.*\\(.*)/;
	var fileTitle = file.replace(reWin, "$1"); //выдираем название файла
	reUnix = /.*\/(.*)/;
	fileTitle = fileTitle.replace(reUnix, "$1"); //выдираем название файла
	if (fileTitle.length > 18) {
		fileTitle = "..." + fileTitle.substr(fileTitle.length - 16, 16);
	}
	$(fileName).text(fileTitle);
	
	var RegExExt =/.*\.(.*)/;
	var ext = fileTitle.replace(RegExExt, "$1");//и его расширение
	
	var pos;
	if (ext){
/*		switch (ext.toLowerCase()) {
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
		}*/
		$(fileName).css({display:"block"});
		$(input_object).parent().children("div.file_name").css({display:"none"});
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
			if ($(formElement).attr("class") == "button") {
				var searchString = new String($(formElement).siblings("input.text").attr("value"));
			}
			if ($(formElement).attr("class") == "text") {
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
	if ((form_element.className == "text" && form_element.getAttribute("name")) || (form_element.className == "button")) {
		var form = $(form_element).parents("form");
		if ($(form).attr("name") == "forget") {
			checkForgetForm();
		}
		else {
			var flag = 0;
			$(form).find("input[type!='hidden']").each(function() {
				if ($(this).attr("value") == "" && $(this).parent().children("h5").children("span").text() != "") {
					if (this.getAttribute("name") && this.getAttribute("name").search("PASSWORD_CONFIRM") != -1) {
						var passConfField = this;
						var passwordField;
						$(form).find("input[name*='PASSWORD']").each(function() {
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
							$(form).find("input[name*='PASSWORD']").each(function() {
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
			$(form).find("textarea").each(function() {
				if ($(this).text() == "" && $(this).attr("value") == "" && $(this).parent().children("h4").children("span").text() != "") {
					flag = 1;
					$(this).parent().addClass("attention");
				}
			});
			if (flag == 0) {
				$(form).submit();
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

function showList(listArray, chosenArray, header) {
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

function writeChosenItems(button) {
	switch($(button).parent().children("h2").text()) {
		case "Кухни": var curArray = chosenSearchKitchen; break;
		case "Тип блюда": var curArray = chosenSearchDish; break;
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
	
	fillSearchField();
}

//открытие формы выбора ингредиентов - формирование списка групп
function createGroupList() {
	var ingredientsGroupDiv = $("#ingredients_group").empty();
	
	//ul
	var groupUl = $("<ul>").appendTo($(ingredientsGroupDiv));
	var liArray = new Array();//массив со всеми li групп ингредиентов
	for (var i = 0; i < ingredientArray[1].length; i++) {
		//li
		var groupLi = document.createElement("LI");
		$(groupLi).appendTo($(groupUl));
		liArray.push(groupLi);
		//a
		var groupLiAnchor = $('<a href="#" onClick="showIngredients(' + i + '); return false;">' + ingredientArray[1][i] + '</a>').appendTo($(groupLi));
		//span
		var groupLiSpan = $('<span class="name">' + ingredientArray[1][i] + '</span>').appendTo($(groupLi));
	}
	
	//если вызываем окно с ингредиентами повторно для данного этапа
	if (chosenSearchIngredient[0].length != 0) {
		for (var i = 0; i < chosenSearchIngredient[0].length; i++) {
			showSubList(liArray[chosenSearchIngredient[0][i]], true, chosenSearchIngredient[0][i], chosenSearchIngredient[1][i]);
		}
	}
}

//заполнение поля со списком ингредиентов (верхний слой)
function showIngredients(group_number) {
	var groupNumber = group_number;
	var ingredientsGroupDiv = document.getElementById("ingredients_group");
	var ingredientsListDiv = $("#ingredients_list").empty();
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
	var h2 = $("<h2>" + ingredientArray[1][group_number] + "</h2>").appendTo($(ingredientsListDiv));
	
	//column
	var columnDiv = $('<div class="column">').appendTo($(ingredientsListDiv));
	
	//ul
	var columnUl = $('<ul>').appendTo($(columnDiv));
	for (var i = 0; i < ingredientArray[2][groupNumber][0].length; i++) {
		var li = document.createElement("LI");
		for (var j = 0; j < ingredientsNimbers.length; j++) {
			if (ingredientsNimbers[j] == i) {li.className = "selected";
			}
		}
		var liAnchor = document.createElement("A");
		liAnchor.setAttribute("href", "#");
		var liAnchorOnClickString = "selectIngredient(this, " +  groupNumber + ", " + i + "); return false;"
		liAnchor.onclick=new Function(liAnchorOnClickString);
		liAnchor.appendChild(document.createTextNode(ingredientArray[2][groupNumber][1][i]));
		li.appendChild(liAnchor);
		$(columnUl).append($(li));
	}
	increaseTopLayer();
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

function showIngredientsLayer() {
	showHideLayer('top_layer');
	showHideLayer ('ingredients_list_layer');
	createGroupList();
}
function hideIngredientsLayer() {
	showHideLayer('top_layer');
	showHideLayer ('ingredients_list_layer');
	$("#ingredients_group").empty();
	$("#ingredients_list").empty();
	document.getElementById("recipe_search_field").focus();
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
// Определение координаты элемента по вертикали
function pageY(elem) {
	return elem.offsetParent ?
	elem.offsetTop + pageY( elem.offsetParent ) :
	elem.offsetTop;
}
//навигация с помощью стрелок
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