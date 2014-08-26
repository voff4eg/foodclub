$(function() {
	ny2014topMenu();
});

function ny2014topMenu() {
	$(".b-ny__top-menu").delegate("a", "click", clickLink);
	$(document).delegate("body", "keyup", keyupBody);
	$(".b-ny").delegate(".recipe_list_item", "click", clickRecipe);
	
	$(".b-ny__top-menu a:eq(1)").click();
	
	function clickRecipe() {
		if(!window.ga) return;
		ga('send', 'event', 'NY2014', 'Click recipe', $(this).find("h5").text() + ", url:" + $(this).find("a").attr("href"));
	}
	
	function clickLink(e) {
		e.preventDefault();
		var $link = $(this);
		$(".b-ny__top-menu li").removeClass("b-nav-left").removeClass("b-nav-right").removeClass("i-active");
		
		$link.parent().addClass("i-active");
		$link.parent().prev("li").addClass("b-nav-left");
		$link.parent().next("li").addClass("b-nav-right");
		
		$(".b-ny__menu").hide();
		$("#" + $link.attr("data-id")).show();
		
		if(!window.ga) return;
		ga('send', 'event', 'NY2014', 'Click menu', $link.attr("data-id"));
	}
	
	function keyupBody(e) {
		if(e.which == 39) {
			$(".b-ny__top-menu .b-nav-right a").click();
			if(!window.ga) return;
			ga('send', 'event', 'NY2014', 'Keyboard navigation');
		} else if(e.which == 37) {
			$(".b-ny__top-menu .b-nav-left a").click();
			if(!window.ga) return;
			ga('send', 'event', 'NY2014', 'Keyboard navigation');
		}
	}
}
