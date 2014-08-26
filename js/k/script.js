$(function() {
	$(".b-kitchen__list").each(function() {
		new Badges(this);
	});
	
	kitchenAdminButtons();
});

function kitchenAdminButtons() {
	$(".b-personal-page__kitchen")
		.delegate(".b-delete-icon", "click", clickDelete)
		.delegate(".b-edit-icon", "click", clickEdit);
	
	function clickDelete(e) {
		e.stopPropagation();
		$button = $(this);
		
		if(confirm($button.attr("title") + "?")) {
			$.ajax({
				url: $button.closest(".b-kitchen__list").attr("data-delete-ajax-url"),
				type: "POST",
				data: "id=" + $button.closest(".b-kitchen__item").attr("data-id"),
				success: function(data) {
					$button.closest(".b-kitchen__item").fadeOut(500, function() {
						$(this).remove();
					});
				},
				error: ajaxError
			});
			return true;
		}
		return false;
	}
	
	function clickEdit() {
		
		var $item = $(this).closest(".b-kitchen__item");		
		var data = getData();
		openAddForm();
		$(".b-kitchen__add-form").data("KitchenAddForm").fillForm(data).changeTitle("Редактировать технику", "Сохранить");
		
		function getData() {
			var data = $.parseJSON($item.attr("data-balloon"));
			var dataAjax = $.parseJSON($item.attr("data-ajax"));
			data.id = $item.attr("data-id");
			data.model = $item.find(".b-kitchen__item__model").text();
			data.hidden = dataAjax;
			
			data.title = {"id": $item.find(".b-kitchen__item__title").attr("data-id"), "name": data.title};
			data.brand = {"id": $item.find(".b-kitchen__item__brand").attr("data-id"), "name": data.brand};
			data.model = {"id": $item.find(".b-kitchen__item__model").attr("data-id"), "name": data.model};
			
			return data;
		}
		
		function openAddForm() {
			if(!document.getElementById("kitchen-equipment-add-form")) return;
			$("#kitchen-equipment-add-form").popup({
				closeElem: ".b-popup__close",
				close: function() {$(".b-kitchen__add-form").data("KitchenAddForm").reset().changeTitle("Добавить технику");}
			});
		}
	}
}

function Badges(elem) {
	var self = this;
	
	init();
	
	function init() {
		initElements();
		handleEvents();
	}
	
	function initElements() {
		self.$elem = $(elem);
		self.$elem.data("Badges", self);
		self.url = self.$elem.attr("data-url");
		self.author = $(".b-personal-card__name__first").text();
	}
	
	function handleEvents() {
		self.$elem
			.delegate(".b-kitchen__item__link, .b-balloon", "mouseenter", showHint)
			.delegate(".b-kitchen__item__link, .b-balloon", "mouseleave", hideHint);
		
		function showHint() {
			var $item = $(this).parent();
			
			if($item.find(".b-balloon").is("div")) {
				$item.find(".b-balloon").show();
			} else {
				var json = $.parseJSON($item.attr("data-balloon"));
				json.text = json.text.replace(/\"/g, '');
				var src = "http://" + window.location.hostname + $item.find("img").attr("src");
				var title = self.author + ' &mdash; ' + json.title + ' на Foodclub.ru';
				var url = self.url// + json.id + "/"
				
				var price = "";
				if(json.price != "") price = '<div class="b-balloon__column-right b-balloon__price"><div class="b-balloon__small-heading">Ориентировочная цена</div><div class="b-balloon__price__value">'  + json.price + ' руб.</div></div>';
				
				$item.prepend('<div class="i-relative"><div class="b-kitchen__balloon b-balloon"><div class="b-balloon__pointer"></div><div class="b-balloon__heading">' + json.title + ' ' + json.brand + '</div><div class="b-balloon__text">' + json.text + '</div><div class="b-balloon__column-left b-balloon__rating"><div class="b-balloon__small-heading">Рейтинг Foodclub.ru</div><div class="b-balloon__rating__stars i-stars'  + json.rating + '"></div></div>' + price + '<div class="i-clearfix"></div><div class="b-balloon__share"><span class="b-balloon__share__heading">Расскажите друзьям:</span> <a title="Через Вконтакте" class="b-balloon__share__item i-vkontakte" target="_blank" onclick="window.open(this.href, \'\', \'width=540,height=450\');return false;" href="http://vkontakte.ru/share.php?url=' + url + '&amp;title=' + title + '&amp;image=' + src + '&amp;description=' + json.text + '&amp;noparse=yes"></a><a title="Через Facebook" class="b-balloon__share__item i-facebook" target="_blank" onclick="window.open(this.href, \'\', \'width=540,height=450\');return false;" href="http://www.facebook.com/sharer.php?s=100&amp;p[title]=' + title + '&amp;p[summary]=' + json.text + '&amp;p[url]=' + url + '&amp;p[images][0]=' + src + '"></a><a title="Через Twitter" class="b-balloon__share__item i-twitter" target="_blank" onclick="window.open(this.href, \'\', \'width=540,height=450\');return false;" href="http://twitter.com/share?text=' + title + '&amp;url=' + url + '"></a><a title="Через Google" class="b-balloon__share__item i-google" target="_blank" onclick="window.open(this.href, \'\', \'width=540,height=450\');return false;" href="https://plus.google.com/share?url=' + url + '"></a></div></div></div>');
			}
		}
		
		function hideHint() {
			$(this).parent().find(".b-balloon").hide();
		}
	}
}