$(function() {	

	$(".b-recipe-menu__button__type-print").addClass("print").click(function() {
		openPrintWindow($(this));
		return false;
	});

	if ( window.BX ) {
		BX.addCustomEvent( "onFrameDataReceived", function () {

			if(!$(this).hasClass("print")){
				$(".b-recipe-menu__button__type-print").addClass("print").click(function() {
					openPrintWindow($(this));
					return false;
				});
			}
		});
	}

	//Open window for printing recipe
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
		
		var printWindow = window.open("", "", "width=800, height=800,toolbar=0,scrollbars=yes,status=0,directories=0,location=0,menubar=0,resizable=0");
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

});