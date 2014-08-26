$(function() {
	
	/*$(".b-kitchen__item__type_empty a").click(function(e) {
		e.preventDefault();
		showHideLayer("top_layer");
		showHideLayer("kitchen-add-form");
		$("#kitchen-add-form").animate({marginTop: "-360px"});
	});*/
	
	new KitchenAddForm();
	
});

function KitchenAddForm() {
	var self = this;
	
	init();
	
	function init() {
		initVarsAndElems();
		handleEvents();
	}
	
	function initVarsAndElems() {
		self.$elem = $(".b-kitchen__add-form");
		self.$elem.data("KitchenAddForm", self);
		self.$elem.find("select").select2();
		self.$slider = self.$elem.find(".b-input-range");
		self.$sliderInput = self.$slider.find(".b-input-range__input");
		initSlider();
		self.$button = self.$elem.find(".b-form-submit .b-button");
		self.$type = self.$elem.find(".b-kitchen__add-form__type");
		self.$trade = self.$elem.find(".b-kitchen__add-form__trade");
		self.$model = self.$elem.find(".b-kitchen__add-form__model");
		self.$form = self.$elem.find("form");
		self.cash = {};
	}
	
	function handleEvents() {
		self.$button.click(clickSubmitButton);
		self.$type.delegate("select", "change", changeTypeSelect);
		self.$trade.delegate("select", "change", changeTradeSelect);
	}
	
	function changeTypeSelect(e) {
		var data = {};
		data.value = self.$type.find("option:selected")[0].getAttribute("value");
		if(!data.value && data.value != 0) {
			makeInvisible(self.$trade);
			makeInvisible(self.$model);
			return;
		}
		
		makeLoading(self.$trade.find("select"));
		makeLoading(self.$model.find("select"));
		setTradesModels(data);
	}
	
	function setTradesModels(type) {
		if(self.cash[type.value]) {
			success(self.cash[type.value]);
		} else {
			$.ajax({
				url: self.$trade.attr("data-ajax-url"),
				type: "GET",
				dataType: "json",
				data: type,
				async: false,
				success: success,
				error: ajaxError
			});
		}
		
		function success(tradesModels) {
			setCash(type.value, tradesModels);
			makeSelect(self.$trade, getTrades(tradesModels));
			makeSelect(self.$model, getModels(tradesModels));
		}
		
		function getTrades(tradesModels) {
			var optionsHtml = "";
			for(var i = 0; i < tradesModels.length; i++) {
				optionsHtml += '<option value="' + tradesModels[i].trade.id + '">' + tradesModels[i].trade.name + '</option>'
			}
			
			return optionsHtml;
		}
	}
		
	function getModels(tradesModels) {
		var value = self.$trade.find("option:selected")[0].getAttribute("value");
		if(!value && value != 0) return;
		
		for(var i = 0; i < tradesModels.length; i++) {
			if(tradesModels[i].trade.id == value) {
				var models = tradesModels[i].models;
			}
		}
		
		var optionsHtml = "";
		for(var i = 0; i < models.length; i++) {
			optionsHtml += '<option value="' + models[i].id + '">' + models[i].name + '</option>'
		}
		
		return optionsHtml;
	}
	
	function makeSelect($elem, optionsHtml) {
		var $select = $('<select name="' + $elem.find("select").attr("name") + '" required>' + optionsHtml + '</select>');
		$elem.find("select").select2("destroy").remove().end().append($select);
		$elem.find("select").select2();
		makeVisible($elem);
	}
	
	function setCash(type, tradesModels) {
		self.cash[type] = tradesModels;
	}
	
	function changeTradeSelect(e) {
		var type = self.$type.find("option:selected")[0].getAttribute("value");
		if(!type && type != 0) return;
		
		makeSelect(self.$model, getModels(self.cash[type]));
	}
	
	function makeVisible($field) {
		$field.removeClass("i-invisible").removeClass("i-loading").addClass("i-visible");
	}
	
	function makeInvisible($field) {
		$field.removeClass("i-visible").removeClass("i-loading").addClass("i-invisible");
	}
	
	function makeLoading($field) {
		$field.removeClass("i-visible").removeClass("i-invisible").addClass("i-loading");
	}
	
	function clickSubmitButton(e) {
		e.preventDefault();
		
		self.$elem
			.find(".b-form-field").removeClass("i-attention").end()
			.find(".i-invisible, .i-loading").addClass("i-attention");
		
		if(self.$elem.find(".i-invisible").size() != 0 || self.$elem.find(".i-loading").size() != 0) {
			if(!self.$type.find("select option:selected")[0].getAttribute("value")) self.$type.addClass("i-attention");
			return;
		}
		
		$.ajax({
			url: self.$form.attr("action"),
			type: self.$form.attr("method"),
			data: self.$form.serialize(),
			beforeSend: beforeSend,
			success: success,
			error: ajaxError
		});
		
		function beforeSend() {
			self.$elem.height(self.$elem.height()).addClass("i-preloader").animate({height: "100px"});
			self.$elem.closest(".b-popup").animate({marginTop: "-130px"});
		}
		
		function success(data) {
			self.$elem.closest(".b-popup").popup({
				close: function() {reset();}
			});
			
			var $equipmentItem = getEquipment(data);
			if($equipmentItem) appendItem($equipmentItem);
			
			function appendItem($equipmentItem) {
				var id = $equipmentItem.attr("data-id");
				var flag = false;
				
				$(".i-tech .b-kitchen__item").each(function() {
					if($(this).attr("data-id") == id) {
						$(this).before($equipmentItem).remove();
						flag = true;
					}
				});
				if(!flag) $(".i-tech .b-kitchen__item__type_empty").before($equipmentItem);
			}
			
			function getEquipment(data) {
				data = $.parseJSON(data);
				if(!data) return false;
				
				var equipmentObject = {
					title: "",
					brand: "",
					text: "",
					rating: "",
					price: "",
					image: {
						"src": "",
						"width": "155",
						"height": "155",
						"alt": '"' + data.title + '"'
					},
					model: ""
				};
				
				$.extend(equipmentObject, data);
				
				var template = document.getElementById('kitchen-equipment-template').innerHTML;
				var compiled = tmpl(template);
				return $(compiled(data));
			}
		}
	}
	
	function initSlider() {
		self.$slider.slider("option", {
			value: 3,
			step: 1,
			range: "min",
			change: change,
			create: create
		})
		
		function change(e, ui) {
			self.$sliderInput.val(ui.value);
		}
		
		function create() {
			self.$sliderInput.val(3);
		}
	}
	
	function reset() {
		self.$elem.removeClass("i-preloader");
		self.$elem.css({height: "auto"}).closest(".b-popup").attr({style: ""});
		self.$type.find("option:eq(0)").attr("selected", "selected");
		self.$type.find("select").change();
		self.$elem.find("textarea").val("").end().find(".b-input-range").slider("value", 3);
		self.$elem.find(".b-form-hidden").remove();
		self.$elem.find(".i-attention").removeClass("i-attention");
	}
	
	function fillForm(data) {
		setType();
		setTrade();
		setModel();
		setRating();
		setComment();
		setHidden();
		
		function setType() {
			if(!data.title || !data.title.id) return;
			
			if(self.$elem.find(".b-kitchen__add-form__type option[value=" + data.title.id + "]").size() == 0) {
				self.$elem.find(".b-kitchen__add-form__type select").append('<option value="' + data.title.id + '">' + data.title.name + '</option>');
			}
			self.$elem.find(".b-kitchen__add-form__type select").val(data.title.id).trigger("change");
		}
		
		function setTrade() {
			if(!data.brand || !data.brand.id) return;
			
			if(self.$elem.find(".b-kitchen__add-form__trade option[value=" + data.brand.id + "]").size() == 0) {
				self.$elem.find(".b-kitchen__add-form__trade select").append('<option value="' + data.brand.id + '">' + data.brand.name + '</option>');
			}
			self.$elem.find(".b-kitchen__add-form__trade select").val(data.brand.id).trigger("change");
		}
		
		function setModel() {
			if(!data.model || !data.model.id) return;
			
			if(self.$elem.find(".b-kitchen__add-form__model option[value=" + data.model.id + "]").size() == 0) {
				self.$elem.find(".b-kitchen__add-form__model select").append('<option value="' + data.model.id + '">' + data.model.name + '</option>');
			}
			self.$elem.find(".b-kitchen__add-form__model select").val(data.model.id).trigger("change");
		}
		
		function setRating() {
			if(!data.rating) return;
			
			self.$elem.find(".b-input-range").slider("value", data.rating);
		}
		
		function setComment() {
			if(!data.text) return;
			
			self.$elem.find("textarea").val(data.text);
		}
		
		function setHidden() {
			if(!data.hidden || typeof data.hidden != "object") return;
			
			if(self.$elem.find(".b-form-hidden").size() == 0) self.$elem.find(".b-form-submit").before('<div class="b-form-hidden"></div>');
			
			var $hidden = self.$elem.find(".b-form-hidden").empty();
			
			$hidden.append('<input type="hidden" name="id" value="' + data.id + '">');
			for(var key in data.hidden) {
				$hidden.append('<input type="hidden" name="' + key + '" value="' + data.hidden[key] + '">');
			}
		}
	}
	
	function changeTitle(title, button) {
		button = button || title;
		if(!title) return;
		self.$button.text(button);
		self.$elem.closest(".b-popup").find(".b-popup__heading").text(title);
	}
	
	/*---public methods---*/
	
	this.fillForm = function(data) {
		fillForm(data);
		return self;
	};
	
	this.reset = function() {
		reset();
		return self;
	};
	
	this.changeTitle = function(title, button) {
		changeTitle(title, button);
		return self;
	};
}