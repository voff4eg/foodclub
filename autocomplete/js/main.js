$(function() {
	$("[data-placeholder]").placeholder();
	
	var recipeSearch = new RecipeSearch("recipe-search", {array: recipes});
});

(function($) {
	var defaults = {
		text: "",
		color: "#aaaaaa",
		clsName: "placeholder"
	};
	$.fn.placeholder = function(params) {
	   
		var options = $.extend({}, defaults, params);
		
		$(this).each(function() {
							
			 var $self = $(this)
				 text = options.text || $self.attr("data-placeholder");
			
			init();
			
			function init() {
				$self.focus(focusField);
				$self.blur(blurField);
				
				$self.blur();
			}
			
			function focusField() {
				if($self.val() == text) {
					$self.val("");
					$self.removeClass(options.clsName);
				}
			}
			
			function blurField() {
				if($self.val() == "") {
					$self.val(text);
					$self.addClass(options.clsName);
				}
			}
			
		});
		
		return this;
	};
	
})(jQuery);

