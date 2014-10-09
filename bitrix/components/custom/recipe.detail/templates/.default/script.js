$(function() {	
	$(".recipe #html_code").click(function() {
		returnHTML();
		return false;
	});

	$("div.needed div.scales").click(function() {
		window.open('/table/','','width=800, height=800,toolbar=0,scrollbars=yes,status=0,directories=0,location=0,menubar=0,resizable=0');
		return false;
	});

	if ( window.BX ) {
		BX.addCustomEvent( "onFrameDataReceived", function () {
			if(!$(this).hasClass("html")){
				$(".recipe #html_code").addClass("html").click(function() {
					returnHTML();
					return false;
				});
			}

			if(!$(this).hasClass("html")){
				$("div.needed div.scales").addClass("table").click(function() {
					window.open('/table/','','width=800, height=800,toolbar=0,scrollbars=yes,status=0,directories=0,location=0,menubar=0,resizable=0');
					return false;
				});
			}
		});
	}

	//Open window for printing recipe
	function returnHTML() {
		var HTMLcode = window.open('','','width=800, height=800,toolbar=0,scrollbars=yes,status=0,directories=0,location=0,menubar=0,resizable=0');
		var ingredientsText = "",
			stagesText="",
			ingSpan = $("div.title div.needed span.ingredient"),
			amountTd = $("div.title div.needed td.ing_amount"),
			stagesDiv = $("div.stage");
			
		ingSpan.each( function() {
			ingredientsText += this.innerHTML +
			"&amp;nbsp;&amp;nbsp;&amp;nbsp;" +
			amountTd[ingSpan.index(this)].innerHTML +
			"&lt;br /&gt;";
		});
		
		stagesDiv.each( function() {
			var img= "",
				ings = $(this).find("td.ing_name"),
				amounts = $(this).find("td.ing_amount"),
				ingText = "";
			
			ings.each(function() {
				ingText += this.innerHTML +
				"&amp;nbsp;&amp;nbsp;&amp;nbsp;" +
				amounts[ings.index(this)].innerHTML +
				"&lt;br /&gt;";
			});
			if( $(this).find("img.photo").is("img") ) {
				img = '&lt;br /&gt;&lt;br /&gt;&lt;img\
				src="' + $(this).find("img.photo").attr("src") +
				'" /&gt';
			}
			stagesText += Number(stagesDiv.index(this)+1) +
			". " + ingText + $(this).find("div.instruction").text() +
			img + '&lt;br /&gt;&lt;br /&gt;&lt;br /&gt;';
		});
		
		var text = '&lt;img src="' + $("img.result-photo").attr("src") +
			'" /&gt;&lt;br /&gt;&lt;br /&gt;' +
			$("div.description").text() +
			'&lt;br /&gt;&lt;br /&gt;&lt;a href="http://www.foodclub.ru/detail/' +
			$("div.hrecipe").attr("id") +
			'/" target="_blank"&gt;Постоянный адрес рецепта&lt;/a&gt; \
			на foodclub.ru&lt;br /&gt;&lt;br /&gt;&lt;lj-cut \
			text="Читать подробности"&gt;&lt;br /&gt;&lt;br /&gt;\
			Состав&lt;br /&gt;&lt;br /&gt;' +
			ingredientsText + '&lt;br /&gt;	&lt;br /&gt;&lt;br \
			/&gt;' + stagesText + '&lt;/lj-cut&gt;';
		
		HTMLcode.document.write('<!DOCTYPE html><html><head>\
			<meta charset="windows-1251" /><title>HTML-код</title></head>\
			<body><textarea cols="80" rows="40">' + text + '</textarea></body>\
			</html>');
		textarea = HTMLcode.document.getElementsByTagName("TEXTAREA");
		textarea[0].focus();
	}
});