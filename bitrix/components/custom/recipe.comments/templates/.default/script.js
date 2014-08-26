$(function() {
	$(".comments_block").each(function() {
		new RecipeComments(this);
	});
});

function RecipeComments(elem) {
	var self = this;
	
	init();
	
	function init() {
		initElements();
		handleEvents();
	}
	
	function initElements() {
		self.$elem = $(elem);
		self.$getMoreButton = self.$elem.find(".b-more-button a");
		self.commentsPerPage = 5;
		self.page = 1;
	}
	
	function handleEvents() {
		self.$getMoreButton.click(clickMoreRecipes);
	}
	
	function clickMoreRecipes() {
		
		$.ajax({
			url: "/php/get_more_comments.php",
			dataType: "json",
			data: {
				page: ++self.page
			},
			beforeSend: function() {
				self.$getMoreButton.parent().addClass("i-preload");
			},
			success: function(data){
				setTimeout(function() {
					self.$getMoreButton.parent().removeClass("i-preload");
					var div = $('<div style="display:none;"></div>');
					
					var html = '';
					for(var i=0; i < data.comments.length; i++) {
						html += compileComment(data.comments[i]);
					}
					div.html(html);
					self.$getMoreButton.parent().before(div);
					div.css({opacity:0}).show().animate({opacity:1}, 500);
					
					$.scrollTo(div, 1000);
					
					showHideGetMoreButton();
					window.upButton.styleElements();
				}, 500);
			},
			error: ajaxError
		});
		
		return false;
		
	}
	function showHideGetMoreButton() {
		var pages = parseInt(self.$getMoreButton.attr("data-pages"), 10);
		pages--;

		self.$getMoreButton.attr({"data-pages": pages});
		
		if(pages == 1) {
			self.$getMoreButton.hide();
		}
	}

	function compileComment(commentObj) {
		if(!commentObj.reply) {
			commentObj.reply = undefined;
		}
		var template = document.getElementById('comment-template').innerHTML;
		var compiled = tmpl(template);

		return compiled(commentObj);
	}
}