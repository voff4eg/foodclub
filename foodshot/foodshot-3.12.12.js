$(document).ready(function() {

(function() {
	$.ajax({
		type: "POST",
		dataType: "json",
		url: "foodshotJSON.php",
		success: function(data) {
			var foodshotsJSON = data;
			var foodshotBoard = new FoodshotBoard("foodshotBoard", foodshotsJSON.elems);
			
			checkAddress(foodshotBoard);
			onDocumentReady();
		},
		error: function(a, b, c) {
			if(!window.console) return;
			window.console.log(a + b + c);
		}
		
	});
})();

});


function FoodshotBoard(id, elems, params) {
	var self = this;
	
	this.items = [];
	this.grid = {};
	this.cols = [];
	var options = params || {};
	
	setDefaults();
	
	this.$elem = $("#" + id);
	
	init();
	
	function init() {
		getItems();
		setItemsData();
		makeGrid();
		makeCoord();
		
		positionItems();
		
		adaptBoardHeight();
		
		boardEvents();
		
	}
	
	this.getItemObject = function(id) {
		for(var i = 0; i < self.items.length; i++) {
			if(self.items[i].id == id) {
				return self.items[i];
			}
		}
		return false;
	};
	
	function setItemsData() {
		for(var i = 0; i < self.items.length; i++) {
			self.items[i].id = self.items[i].$elem.attr("data-id");
		}
	}
	
	function moveLowItems(lowItems, diff) {
		
		for(var i = 1; i < lowItems.length; i++) {
			var $elem = self.items[lowItems[i]].$elem;
			var top = $elem.css("top");
			$elem.css({top: parseInt(top) + diff*1 + "px"});
		}
		
	}
	
	function getLowItems($itemElem) {
		var index = $itemElem.attr("data-index"),
			colNum = self.items[index].column,
			array = self.cols[colNum];
		
		for(var i = 0; i < array.length; i++) {
			if(array[i] == index) {
				var result = array.slice(i);
				break;
			}
		}
		
		return result;
	}
	
	function adaptBoardHeight() {
		self.$elem.height(self.coord[self.coord.length - 1].y);
	}
	
	function boardEvents() {
		self.$elem
		.delegate(".b-foodshot-board__item-comments-hidden__button", "click", function() {
			return false;
		})
		.delegate(".b-comment-icon__type-button", "click", function() {
			changeItemContent($(this), showCommentsFrom, {$el: $(this)});
			return false;
		})
		.delegate(".b-like-icon", "click", function() {
			
			likeAction($(this));
			$(this).toggleClass("b-like-icon__type-active");
			
			return false;
		})
		.delegate(".b-form-field__type-comment__button", "click", function() {
			if(!isValidForm($(this))) return false;
			
			changeItemContent($(this), addComment, {$el: $(this)});
			return false;
		})
		.delegate("textarea", "keyup",  function() {
			changeItemContent($(this), resizeTexarea, {$el: $(this)});
		})
		.delegate(".b-foodshot-board__item-content-image, .b-foodshot-board__item-comments-hidden__button", "click",  function() {
			var $itemElem = getItemElem({$el: $(this)});
			var itemObject = getItemObject({$itemElem: $itemElem});
			self.showDetail(itemObject);
			return false;
		});
		
	};
	
	function getItemElem(args) {
		if(args.$el) {
			return args.$el.closest(".b-foodshot-board__item");
		}
	}
	
	function getItemObject(args) {
		if(args.$itemElem) {
			return self.items[args.$itemElem.attr("data-index")];
		}
		if(args.$el) {
			var $itemElem = getItemElem(args);
			return self.items[$itemElem.attr("data-index")];
		}
	}
	
	this.showDetail = function(itemObject) {
		$.ajax({
			type: "POST",
			dataType: "json",
			url: "detailJSON.php",
			data: "id=" + itemObject.id,
			success: function(data) {
				itemObject.detail = new FoodshotDetail(data);
				popup(itemObject);
				addContent(itemObject.detail.$elem);
			},
			error: function(a, b, c) {
				if(!window.console) return;
				window.console.log(a + b + c);
			}
		});
	};
	
	function popup(itemObject) {
		$("body").append(itemObject.detail.$elem);
		itemObject.detail.$elem.popup({
			closeElem: "a.b-close-icon",
			onClose: function($elem) {
				$elem.remove();
				$("#opaco").remove();
				itemObject.detail = null;
			}
		});
	}
	
	function resizeTexarea(args) {
		var $textarea = args.$el;
		$textarea.height(options.textarea.minHeight);
		
		var height = $textarea.outerHeight(),
			scrollHeight = $textarea[0].scrollHeight + 2;/*2 - borders*/
		
		if(!options.textarea.diff) {
			options.textarea.diff = height - scrollHeight;
		}
		scrollHeight = scrollHeight + options.textarea.diff;
		
		if (height != scrollHeight) {
			if (scrollHeight > options.textarea.maxHeight) {
				$textarea.height(options.textarea.maxHeight).addClass("i-over-height");
			}
			else if(scrollHeight < options.textarea.minHeight) {
				$textarea.height(options.textarea.minHeight).removeClass("i-over-height");
			}
			else {
				$textarea.height(scrollHeight).removeClass("i-over-height");
			}
		}
	}
	
	function isValidForm() {
		return true;
	}
	
	function addContent($content) {
		userpicLayer($content);
	}
	
	function addComment(args) {
		var $button = args.$el;
		
		var $itemElem = $button.closest(".b-foodshot-board__item");
		var itemObject = self.items[$itemElem.attr("data-index")];
		
		var $form = $button.closest("form"),
			$textarea = $form.find("textarea"),
			commentText = $textarea.val();
		
		var $comment = compileComment();
		
		var $commentList = getCommentList();
		$commentList.append($comment);
		
		addContent($comment);
		
		hideComment();
		
		hideForm();
		
		sendComment();
		
		function sendComment() {
			$.ajax({
				type: "POST",
				dataType: "json",
				url: "add_comment.php",
				data: "id=" + itemObject.id + "&text=" + commentText,
				success:function(data) {
				}
			});
		}
		
		function hideForm() {
			$textarea.val("").css({height: ""});
			$itemElem.find("div.b-foodshot-board__item-action").removeClass("i-foodshot-board__item-action-invert");
		}
		
		function hideComment() {
			if($commentList.find("div.b-comment").size() <= options.commentsNum) return;
			
			$commentList.find("div.b-comment:eq(0)").remove();
			
			$commentsHidden = $itemElem.find("div.b-foodshot-board__item-comments-hidden");
			var commentsCounter = parseInt($commentsHidden.find("span.b-comment-icon").text()) + 1;
			
			if(!$commentsHidden.is("div")) {
				$itemElem
					.find("div.b-foodshot-board__item-comments")
					.prepend('<div class="b-foodshot-board__item-comments-hidden"><a class="b-foodshot-board__item-comments-hidden__button" href="#"><span class="b-comment-icon b-foodshot-board__item-comments-hidden__icon">1</span></a></div>');
				
				$commentsHidden = $itemElem.find("div.b-foodshot-board__item-comments-hidden");
				commentsCounter = 1;
			}
			
			$commentsHidden.find("span.b-comment-icon").text(commentsCounter);
		}
		
		function getCommentList() {
			var $commentList = $itemElem.find(".b-comment-list");
			if(!$commentList.is("div")) {
				$itemElem
					.find(".b-foodshot-board__item-content")
					.after('<div class="b-foodshot-board__item-comments"><div class="b-comment-list"></div></div>');
				
				$commentList = $itemElem.find(".b-comment-list");
			}
			
			return $commentList;
		}
		
		function compileComment() {
			var commentObj = {
				"author": {
					"href": userObject.href,
					"src": userObject.src,
					"name": userObject.name
				},
				"text": commentText
			};
			var template = document.getElementById('foodshot-comment-template').innerHTML;
			var compiled = tmpl(template);
			
			return $(compiled(commentObj));
		}
	}
	
	function changeItemContent($el, fnc, args) {
		var $itemElem = $el.closest(".b-foodshot-board__item");
		var itemObject = self.items[$itemElem.attr("data-index")];
		
		var bottom1 = lowestBottom = itemObject.getBorders().bottom;
		
		fnc(args);
		
		var bottom2 = itemObject.getBorders().bottom;
		var diff = bottom2 - bottom1;
		
		var lowItems = getLowItems($itemElem);
		
		
		if(lowItems.length > 1) {
			lowestBottom = self.items[lowItems[lowItems.length - 1]].getBorders().bottom;
		}
		
		moveLowItems(lowItems, diff);
		
		var index = getCoordY(lowestBottom, diff);
		
		changeGridY(self.items[lowItems[lowItems.length - 1]], index);
		
		adaptBoardHeight();
		
	}
	
	function likeAction($button) {
		var $itemElem = $button.closest(".b-foodshot-board__item");
		var itemObject = self.items[$itemElem.attr("data-index")];
		
		var action = "like";
		if($button.hasClass("b-like-icon__type-active")) action = "unlike";
		
		$.ajax({
			type: "POST",
			url: "like.php",
			data: "id=" + itemObject.id + "&action=" + action,
			success: function(data) {
				$button.siblings(".b-like-num").text(data);
			}
		});
	}
	
	function getCoordY(oldY, diff) {
		for(var i = 0; i < self.coord.length; i++) {
			if(self.coord[i].y == oldY) {
				var index = i;
				break;
			}
		}
		return index;
	}
	
	function showCommentsFrom(args) {
		var $button = args.$el;
		$button.closest(".b-foodshot-board__item-action")
			.addClass("i-foodshot-board__item-action-invert")
			.find("textarea").focus();
	}
	
	function getItems() {
		for(var i = 0; i < elems.length; i++) {
			self.items.push(new Foodshot(elems[i], i));
		}
		
		for(var i = 0; i < self.items.length; i++) {
			self.$elem.append(self.items[i].$elem);
		}
	}
	
	function positionItems() {
		for(var i = 0; i < self.items.length; i++) {
			var index = 0;
			positionItemsY(self.items[i], index);
			
			positionItemsX(self.items[i], index);
			
			setColumn(self.items[i], index, i);
			
			changeGridY(self.items[i], index);
		}
	}
	
	function setColumn(itemObject, index, num) {
		var colNum = Math.floor(self.coord[index].x / self.grid.num);
		if(!self.cols[colNum]) self.cols[colNum] = [];
		
		self.cols[colNum].push(num);
		
		itemObject.column = colNum;
	}
	
	function positionItemsX(itemObject, index) {
		itemObject.$elem.css({left: self.coord[index].x + "px"});
	}
	
	function positionItemsY(itemObject, index) {
		itemObject.$elem.css({top: self.coord[index].y*1 + options.marginY*1 + "px"});
	}
	
	function changeGridY(itemObject, index) {
		
		self.coord[index].y = itemObject.getBorders().bottom;
		
		self.coord.sort(sortGridY);
		
		function sortGridY(a, b){
			if(a.y < b.y)
				return -1;
			if(a.y > b.y)
				return 1;
			return 0;
		}
	}
	
	function makeGrid() {
		var boardWidth = self.$elem.width();
		
		self.grid = {};
		self.grid.width = self.items[0].$elem.outerWidth();
		self.grid.num = Math.floor(boardWidth / self.grid.width);
		self.grid.margin = getMarginX(self.grid.num);
		
		if(self.grid.margin < options.minMarginX) {
			self.grid.margin = getMarginX(--self.grid.num);
		}
		
		self.grid.margin = Math.floor(self.grid.margin);
		
		function getMarginX(n) {
			return (boardWidth - self.grid.width * self.grid.num) / (self.grid.num - 1);
		}
	}
	
	function makeCoord() {
		
		self.coord = [];
		
		for(var i = 0; i < self.grid.num; i++) {
			self.coord[i] = {};
			
			self.coord[i].x = i * self.grid.width + i * self.grid.margin;
			self.coord[i].y = 0;
		}
		
	}
	
	function setDefaults() {
		options.minMarginX = options.minMarginX || "5";
		options.marginY = options.marginY || "6";
		options.commentsNum = options.commentsNum || 3;
		options.textarea = options.textarea ||
			{
				minHeight: 52,
				maxHeight: 300
			};
	}
}





function Foodshot(obj, index) {
	var self = this;
	
	init();
	
	function init() {
		var template = document.getElementById('foodshot-template').innerHTML;
		var compiled = tmpl(template);
		var result = compiled(obj);

		self.$elem = $(result);
		
		self.$elem.attr({"data-index": index});
	}
	
	this.getBorders = function() {
		var $img = self.$elem.find(".b-foodshot-board__item-content-image img");
		var elemHeight = self.$elem.outerHeight();
		
		if($img.height() == 0) {
			var image = new Image();
			image.src = $img.attr("src");
			elemHeight += $img.attr("height");
		}
		
		var borderBottom = parseInt(self.$elem.css("top")) + elemHeight;
		var borderRight = parseInt(self.$elem.css("left")) + self.$elem.outerWidth();
		
		return {bottom: borderBottom, right: borderRight};
	};
}

function FoodshotDetail(obj) {
	var self = this;
	
	init();
	
	function init() {
		var template = document.getElementById('foodshot-detail-template').innerHTML;
		var compiled = tmpl(template);
		var result = compiled(obj);

		self.$elem = $(result);
	}
	
}

(function($) {
	var defaults = {
		opaco: true,
		valign: "center",
		align: "center",
		after: function(thisElem){}
		//closeElem:"a.close"
		//onClose:function(thisElem){} overwrites default function
	};
	
	$.fn.popup = function(params) {
		var options = $.extend({}, defaults, params);
		$(this).each(function() {
			
			var $self = $(this);
			
			if(!$self.is(":visible")) {
				var topPx = 20,
					leftPx = "50%",
					marginLeft = 0,
					outerHeight = $self.outerHeight() + 24,
					winHeight = $(window).height();
				
				switch(options.valign) {
					case "top":
						topPx = $(window).scrollTop() + topPx*1;
						break;
					case "center":
						if (winHeight > outerHeight) {
							topPx = winHeight/2 + $(window).scrollTop() - outerHeight/2;
						}
						else {
							topPx = $(window).scrollTop() + topPx*1;
						}
						break;
					case "bottom":
						topPx = $(window).scrollTop() + winHeight - outerHeight - topPx;
						break;
				}
				
				switch(options.align) {
					case "left":
						leftPx = "0px";
						break;
					case "center":
						leftPx = "50%";
						marginLeft = -$self.outerWidth()/2 + "px";
						break;
					case "right":
						leftPx = $(document).width() - $self.outerWidth() + "px";
						break;
				}
				
				if(options.opaco) {
					$self.before('<div id="opaco"></div>');
					$("#opaco")
						.css({
							width: "100%",
							height: $(document).height()+"px"})
						.show();
					
					var closeElem = $("#opaco");
					if(options.closeElem){
						closeElem = $("#opaco, " + options.closeElem + "");
					}
					
					var closeFunc = function() {$self.popup(options);};
					if(options.onClose){
						closeFunc = function(){
							options.onClose($self);
						};
					}
					closeElem.click(function(e) {
						closeFunc();
						e.preventDefault();
					});
				}
				$self
					.show()
					.css({
						marginLeft: marginLeft,
						left: leftPx
					})
					.css({
						top: topPx + "px"
					});
			}
			else {
				$self.hide();
				$("#opaco").remove();
				if(options.closeElem){
					$("" + options.closeElem + "").unbind("click");
				}
			}
			options.after($self);
		});
		return this;
	};
})(jQuery);

function userpicLayer($elem) {
	if(!$elem) {
		var $elem = $("body");
	}
	
	$elem.find(".b-userpic").each(function() {
		var $this = $(this);
		
		var $layer = $('<span class="b-userpic__layer"></span>');
		$this.prepend($layer);
		
		var cls = "b-userpic__layer__" + $this.find("img").attr("width");
		$layer.addClass(cls);
	});
}

function onDocumentReady() {
	userpicLayer();
}

function checkAddress(foodshotBoard) {
	if(window.location.pathname.search("foodshot") != -1) {
		var id = /^\S*id=(\d+)&?\S*$/.exec(window.location.search);
		if(id.length && id[1]) {
			id = id[1];
		}
		
		var itemObject = foodshotBoard.getItemObject(id);
		if(!itemObject) {
			itemObject = {
				id: id
			}
		}
		
		foodshotBoard.showDetail(itemObject);
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