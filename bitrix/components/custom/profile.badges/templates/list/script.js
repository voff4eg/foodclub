$(function() {
	$(".b-all-badges__list").each(function() {
		new Badges(this);
	});
});

function Badges(elem) {
	var self = this;
	
	init();
	
	function init() {
		initElements();
		handleEvents();
	}
	
	function initElements() {
		self.$elem = $(elem);
		self.url = self.$elem.attr("data-url");
		self.author = $(".b-personal-card__name__first").text();
	}
	
	function handleEvents() {
		self.$elem
			.delegate(".b-all-badges__item__link, .b-balloon", "mouseenter", showHint)
			.delegate(".b-all-badges__item__link, .b-balloon", "mouseleave", hideHint);
		
		function showHint() {
			var $item = $(this).parent();
			
			if($item.find(".b-balloon").is("div")) {
				$item.find(".b-balloon").show();
			} else {
				var json = $.parseJSON($item.attr("data-balloon"));
				json.text = json.text.replace(/\"/g, '');
				console.log(json.text);
				var src = "http://" + window.location.hostname + $item.find("img").attr("src");
				var title = self.author + ' &mdash; ' + json.title + ' на Foodclub.ru';
				var url = self.url// + json.id + "/"
				
				$item.prepend('<div class="i-relative"><div class="b-all-badges__balloon b-balloon"><div class="b-balloon__pointer"></div><div class="b-balloon__heading">' + json.title + '</div><div class="b-balloon__text">' + json.text + '</div><div class="b-balloon__share"><span class="b-balloon__share__heading">Расскажите друзьям:</span> <a title="Через Вконтакте" class="b-balloon__share__item i-vkontakte" target="_blank" onclick="window.open(this.href, \'\', \'width=540,height=450\');return false;" href="http://vkontakte.ru/share.php?url=' + url + '&amp;title=' + title + '&amp;image=' + src + '&amp;description=' + json.text + '&amp;noparse=yes"></a><a title="Через Facebook" class="b-balloon__share__item i-facebook" target="_blank" onclick="window.open(this.href, \'\', \'width=540,height=450\');return false;" href="http://www.facebook.com/sharer.php?s=100&amp;p[title]=' + title + '&amp;p[summary]=' + json.text + '&amp;p[url]=' + url + '&amp;p[images][0]=' + src + '"></a><a title="Через Twitter" class="b-balloon__share__item i-twitter" target="_blank" onclick="window.open(this.href, \'\', \'width=540,height=450\');return false;" href="http://twitter.com/share?text=' + title + '&amp;url=' + url + '"></a><a title="Через Google" class="b-balloon__share__item i-google" target="_blank" onclick="window.open(this.href, \'\', \'width=540,height=450\');return false;" href="https://plus.google.com/share?url=' + url + '"></a></div></div></div>');
			}
		}
		
		function hideHint() {
			$(this).parent().find(".b-balloon").hide();
		}
	}
}