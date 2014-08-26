$(function() {
	window.foodshotPathname = "/foodshot/";
	$(".b-last-foodshot").each(function() {
		new LastFoodshot($(this));
	});
});

function LastFoodshot(elem) {
	var self = this;
	
	init();
	
	function init() {
		initVarsAndElems();
		handleEvents();
	}
	
	function initVarsAndElems() {
		self.$elem = $(elem);
		self.$elem.data("LastFoodshot", self);
		self.options = {};
		self.options.commentsNum = 3;
		self.options.textarea = {
				minHeight: 52,
				maxHeight: 300
			};
	}
	
	function handleEvents() {
		self.$elem
		.delegate(".b-comment-icon__type-button", "click", function() {
			showCommentsForm({$el: $(this)});
			return false;
		})
		.delegate(".b-like-icon", "click", function() {
			likeAction($(this));
			return false;
		})
		.delegate(".b-form-field__type-comment__button", "click", function() {
			if(!isValidForm($(this))) return false;
			
			addComment({$el: $(this)});
			return false;
		})
		.delegate("textarea", "keyup",  function() {
			resizeTextarea({$el: $(this)});
		});
	}
	
	function resizeTextarea(args) {
		var $textarea = args.$el;
		$textarea.height(self.options.textarea.minHeight);
		
		var height = $textarea.outerHeight(),
			scrollHeight = $textarea[0].scrollHeight + 2;/*2 - borders*/
		
		if(!self.options.textarea.diff) {
			self.options.textarea.diff = height - scrollHeight;
		}
		scrollHeight = scrollHeight + self.options.textarea.diff;
		
		if (height != scrollHeight) {
			if (scrollHeight > self.options.textarea.maxHeight) {
				$textarea.height(self.options.textarea.maxHeight).addClass("i-over-height");
			}
			else if(scrollHeight < self.options.textarea.minHeight) {
				$textarea.height(self.options.textarea.minHeight).removeClass("i-over-height");
			}
			else {
				$textarea.height(scrollHeight).removeClass("i-over-height");
			}
		}
	}
	
	function addComment(args) {
		var $button = args.$el;
		
		var $itemElem = $button.closest(".b-foodshot-board__item");
		
		var $form = $button.closest("form"),
			$textarea = $form.find("textarea"),
			commentText = args.commentText || $textarea.val();
		
		hideForm();
		
		var id = $button.closest(".b-foodshot-board__item").attr("data-id");
		var commentObj = sendComment(commentText, id);
		appendComment({commentObj: commentObj, $itemElem: $itemElem});

		function hideForm() {
			$textarea.val("").css({height: ""});
			$itemElem.find("div.b-foodshot-board__item-action").removeClass("i-foodshot-board__item-action-invert");
		}
	};
	
	function sendComment(commentText, id) {
		var result;
		
		$.ajax({
			url: window.foodshotPathname + "add_comment.php",
			dataType: "json",
			data: {
				"text": commentText,
				"id": id
			},
			async: false,
			success: successSendComment,
			error: ajaxError
		});
		
		return result;
		
		function successSendComment(data) {
			result = data;
		}
	};

	function appendComment(args) {
		var commentObj = args.commentObj,
			$itemElem = args.$itemElem;

		var id = commentObj.id;
		var commentTextEdited = commentObj.text;
		
		var $comment = compileComment(id, commentTextEdited);
		
		var $commentList = getCommentList();
		$commentList.append($comment);
		
		hideComment();
		
		function hideComment() {
			if($commentList.find("div.b-comment").size() <= self.options.commentsNum) return;
			
			$commentList.find("div.b-comment:eq(0)").remove();
			
			$commentsHidden = $itemElem.find("div.b-foodshot-board__item-comments-hidden");
			var commentsCounter = parseInt($commentsHidden.find("span.b-comment-icon").text()) + 1;
			
			if(!$commentsHidden.is("div")) {
				$itemElem
					.find("div.b-foodshot-board__item-comments")
					.prepend('<div class="b-foodshot-board__item-comments-hidden"><a class="b-foodshot-board__item-comments-hidden__button" href="' + window.foodshotPathname + $itemElem.attr("data-id") + '/"><span class="b-comment-icon b-foodshot-board__item-comments-hidden__icon">1</span></a></div>');
				
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
		
		function compileComment(id, text) {
			var commentObj = {
				"id": id,
				"author": {
					"href": userObject.href,
					"src": userObject.src,
					"name": userObject.name
				},
				"text": text
			};
			var template = document.getElementById('foodshot-comment-template').innerHTML;
			var compiled = tmpl(template);
			
			return $(compiled(commentObj));
		}
	};
	
	function isValidForm($elem) {
		var $form = $elem.closest("form"),
			flag = true;

		$form.find("[required]").each(function() {
			if($.trim($(this).val()) == "") {
				flag = false;
				$(this).closest(".b-form-field").addClass("i-attention");
			} else {
				$(this).closest(".b-form-field").removeClass("i-attention");
			}
		});

		return flag;
	};
	
	function likeAction($button) {

		var $itemElem = $button.closest(".b-foodshot-board__item");
		var id = $itemElem.attr("data-id");

		if(!window.userObject) {
			var backurl = window.location.pathname;
			window.location.href = window.authUrl + "?backurl=" + backurl + "#like" + id;
			return;
		}

		if($button.hasClass("b-like-icon__type-disabled")) return;
		
		var action = "like";
		if($button.hasClass("b-like-icon__type-active")) action = "unlike";
		
		var likeObject = sendLike(id, action);
		
		markLike($button, likeObject);
	}
	
	function sendLike(id, action) {
		var result;

		$.ajax({
			type: "POST",
			url: window.foodshotPathname + "like.php",
			async: false,
			data: "id=" + id + "&action=" + action,
			success: function(data) {
				result = data;
			}
		});
		
		return result;
	}

	function markLike($button, likeObject) {
		$button.siblings(".b-like-num").text(likeObject);
		$button.toggleClass("b-like-icon__type-active");
	}
	
	function showCommentsForm(args) {
		var $button = args.$el;
		$button.closest(".b-foodshot-board__item-action")
			.addClass("i-foodshot-board__item-action-invert")
			.find("textarea").focus();
	}
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