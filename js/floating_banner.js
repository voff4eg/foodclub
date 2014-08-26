$(function() {
	$("[data-floating=true]").each(function() {
		new FloatingElem(this);
	});
});

function FloatingElem(elem) {
	var self = this;
	
	setTimeout(init, 1000);
	
	function init() {
		initVarsAndElems();
		handleEvents();
	}
	
	function initVarsAndElems() {
		self.$elem = $(elem);
		self.topBorder = getTopBorder() + 10 || undefined;
		self.bottomBorder = getBottomBorder() - 10 || undefined;
		self.topElemBorder = self.$elem.offset().top;
		self.leftElemBorder = self.$elem.offset().left;
		self.elemHeight = self.$elem.height();
		self.$spacer = $('<div></div>');
		self.$spacer.width(self.$elem.outerWidth()).height(self.$elem.outerHeight());
	}

	function handleEvents() {
		$(window)
			.bind("scroll", scrollWindow)
			.bind("resize", resizeWindow)
			.scroll();
	}

	function getTopBorder() {
		return $("#top_panel").height();
	}

	function getBottomBorder() {
		var footerTopBorders = [];

		$(".b-store-block:not(.b-store-block__type_aside), #bottom, .collection_block, #ng-app").each(function() {
			footerTopBorders.push({
				"elem": this,
				"topBorder": $(this).offset().top
			});
		});

		footerTopBorders.sort(sortTopBorders);

		return footerTopBorders[0].topBorder;

		function sortTopBorders(a, b) {
			if(a.topBorder < b.topBorder) return -1;
			if(a.topBorder > b.topBorder) return 1;
			return 0;
		}
	}

	function scrollWindow() {
		self.scroll = getScroll();
		
		if(!self.$elem.hasClass("i-top-fixed") && (self.scroll >= self.topElemBorder - self.topBorder && self.scroll < self.bottomBorder - self.$elem.outerHeight() - self.topBorder)) {
			doTopFixed();
		} else if(!self.$elem.hasClass("i-bottom-fixed") && (self.scroll >= self.bottomBorder - self.$elem.outerHeight() - self.topBorder)) {
			doBottomFixed();
		} else if((self.$elem.hasClass("i-top-fixed") || self.$elem.hasClass("i-bottom-fixed")) && self.scroll < self.topElemBorder - self.topBorder) {
			undoTopFixed();
			undoBottomFixed();
		}

		/*if(!self.$elem.hasClass("i-top-fixed") && (self.scroll >= self.topElemBorder - self.topBorder && self.scroll < self.bottomBorder - self.$elem.outerHeight() - self.topBorder)) {
			doTopFixed();
		} else if(self.topFixedFlag && (self.scroll < self.topElemBorder - self.topBorder)) {
			undoTopFixed();
		} else if(self.topFixedFlag && self.scroll >= self.bottomBorder - self.$elem.outerHeight() - self.topBorder) {
			doBottomFixed();
		}*/
	}
	
	function resizeWindow() {
		self.leftElemBorder = self.$elem.parent().offset().left;
		self.$elem.css({left: self.leftElemBorder + "px"});
	}

	function getScroll() {
		return $(window).scrollTop();
	}
	
	function doBottomFixed() {
		self.$elem
			.removeClass("i-top-fixed")
			.addClass("i-bottom-fixed")
			.css({top: self.bottomBorder - self.$elem.outerHeight() + "px", left: self.leftElemBorder + "px"});
	}
	
	function undoBottomFixed() {
		self.$elem.removeClass("i-bottom-fixed");
	}
	
	function doTopFixed() {
		self.$elem
			.removeClass("i-bottom-fixed")
			.addClass("i-top-fixed")
			.css({top: self.topBorder + "px", left: self.leftElemBorder + "px"})
			.after(self.$spacer);
	}
	
	function undoTopFixed() {
		self.$elem.removeClass("i-top-fixed");
		self.$spacer.remove();
	}
	
}