$(function(){window.foodshotPathname="/foodshot/";window.authUrl="http://www.foodclub.ru/auth/";$(".b-last-foodshot").each(function(){new LastFoodshot($(this))});if(window.BX){BX.addCustomEvent("onFrameDataReceived",function(){$(".b-last-foodshot").each(function(){if(!$(this).data("LastFoodshot")){new LastFoodshot($(this))}})})}});function LastFoodshot(t){var e=this;o();function o(){i();n()}function i(){e.$elem=$(t);e.$elem.data("LastFoodshot",e);e.options={};e.options.commentsNum=3;e.options.textarea={minHeight:52,maxHeight:300}}function n(){e.$elem.delegate(".b-comment-icon__type-button","click",function(){h({$el:$(this)});return false}).delegate(".b-like-icon","click",function(){m($(this));return false}).delegate(".b-form-field__type-comment__button","click",function(){if(!c($(this)))return false;s({$el:$(this)});return false}).delegate("textarea","keyup",function(){a({$el:$(this)})})}function a(t){var o=t.$el;o.height(e.options.textarea.minHeight);var i=o.outerHeight(),n=o[0].scrollHeight+2;if(!e.options.textarea.diff){e.options.textarea.diff=i-n}n=n+e.options.textarea.diff;if(i!=n){if(n>e.options.textarea.maxHeight){o.height(e.options.textarea.maxHeight).addClass("i-over-height")}else if(n<e.options.textarea.minHeight){o.height(e.options.textarea.minHeight).removeClass("i-over-height")}else{o.height(n).removeClass("i-over-height")}}}function s(t){var e=t.$el;var o=e.closest(".b-foodshot-board__item");var i=e.closest("form"),n=i.find("textarea"),a=t.commentText||n.val();m();var s=e.closest(".b-foodshot-board__item").attr("data-id");var c=r(a,s);d({commentObj:c,$itemElem:o});function m(){n.val("").css({height:""});o.find("div.b-foodshot-board__item-action").removeClass("i-foodshot-board__item-action-invert")}}function r(t,e){var o;$.ajax({url:window.foodshotPathname+"add_comment.php",dataType:"json",data:{text:t,id:e},async:false,success:i,error:ajaxError});return o;function i(t){o=t}}function d(t){var o=t.commentObj,i=t.$itemElem;var n=o.id;var a=o.text;var s=m(n,a);var r=c();r.append(s);d();function d(){if(r.find("div.b-comment").size()<=e.options.commentsNum)return;r.find("div.b-comment:eq(0)").remove();$commentsHidden=i.find("div.b-foodshot-board__item-comments-hidden");var t=parseInt($commentsHidden.find("span.b-comment-icon").text())+1;if(!$commentsHidden.is("div")){i.find("div.b-foodshot-board__item-comments").prepend('<div class="b-foodshot-board__item-comments-hidden"><a class="b-foodshot-board__item-comments-hidden__button" href="'+window.foodshotPathname+i.attr("data-id")+'/"><span class="b-comment-icon b-foodshot-board__item-comments-hidden__icon">1</span></a></div>');$commentsHidden=i.find("div.b-foodshot-board__item-comments-hidden");t=1}$commentsHidden.find("span.b-comment-icon").text(t)}function c(){var t=i.find(".b-comment-list");if(!t.is("div")){i.find(".b-foodshot-board__item-content").after('<div class="b-foodshot-board__item-comments"><div class="b-comment-list"></div></div>');t=i.find(".b-comment-list")}return t}function m(t,e){var o={id:t,author:{href:userObject.href,src:userObject.src,name:userObject.name},text:e};var i=document.getElementById("foodshot-comment-template").innerHTML;var n=tmpl(i);return $(n(o))}}function c(t){var e=t.closest("form"),o=true;e.find("[required]").each(function(){if($.trim($(this).val())==""){o=false;$(this).closest(".b-form-field").addClass("i-attention")}else{$(this).closest(".b-form-field").removeClass("i-attention")}});return o}function m(t){var e=t.closest(".b-foodshot-board__item");var o=e.attr("data-id");if(!window.userObject){var i=window.location.pathname;var n=window.authUrl+"?backurl="+i+"#like"+o;window.location.href=n;return}if(t.hasClass("b-like-icon__type-disabled"))return;var a="like";if(t.hasClass("b-like-icon__type-active"))a="unlike";var s=f(o,a);l(t,s)}function f(t,e){var o;$.ajax({type:"POST",url:window.foodshotPathname+"like.php",async:false,data:"id="+t+"&action="+e,success:function(t){o=t}});return o}function l(t,e){t.siblings(".b-like-num").text(e);t.toggleClass("b-like-icon__type-active")}function h(t){var e=t.$el;e.closest(".b-foodshot-board__item-action").addClass("i-foodshot-board__item-action-invert").find("textarea").focus()}}function tmpl(t){var e="var __p=[],print=function(){__p.push.apply(__p,arguments);};"+"with(obj||{}){__p.push('"+t.replace(/\\/g,"\\\\").replace(/'/g,"\\'").replace(/<%-([\s\S]+?)%>/g,function(t,e){return"',esc("+e.replace(/\\'/g,"'")+"),'"}).replace(/<%=([\s\S]+?)%>/g,function(t,e){return"',"+e.replace(/\\'/g,"'")+",'"}).replace(/<%([\s\S]+?)%>/g,function(t,e){return"');"+e.replace(/\\'/g,"'").replace(/[\r\n\t]/g," ")+";__p.push('"}).replace(/\r/g,"\\r").replace(/\n/g,"\\n").replace(/\t/g,"\\t")+"');}return __p.join('');";var o=new Function("obj",e);return function(t){return o.call(this,t)}}