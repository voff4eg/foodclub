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
		self.$button = self.$elem.find(".b-button");
		self.$type = self.$elem.find(".b-kitchen__add-form__type");
		self.$trade = self.$elem.find(".b-kitchen__add-form__trade");
		self.$model = self.$elem.find(".b-kitchen__add-form__model");
		self.$form = self.$elem.find("form");
	}
	
	function handleEvents() {
		self.$button.click(clickSubmitButton);
		self.$type.delegate("select", "change", changeTypeSelect);
		self.$trade.delegate("select", "change", changeTradeSelect);
	}
	
	function changeTypeSelect(e) {
		var value = self.$type.find("option:selected")[0].getAttribute("value");
		if(!value && value != 0) {
			makeInvisible(self.$trade);
			makeInvisible(self.$model);
			return;
		}
		
		makeLoading(self.$trade.find("select"));
		makeInvisible(self.$model.find("select"));
		getData(self.$trade, value);
	}
	
	function changeTradeSelect(e) {
		var value = e.val;
		if(!value && value != 0) value = self.$trade.find("option:selected")[0].getAttribute("value");
		if(!value && value != 0) return;
		
		makeLoading(self.$model.find("select"));
		getData(self.$model, value);
	}
	
	function getData($field, value) {
		$.ajax({
			url: $field.attr("data-ajax-url"),
			type: "GET",
			dataType: "json",
			data: "value=" + value,
			success: success,
			error: ajaxError
		});
		
		function success(data) {
			var optionsHtml = "";
			for(var key in data) {
				optionsHtml += '<option value="' + key + '">' + data[key] + '</option>'
			}
			var $select = $('<select name="' + $field.find("select").attr("name") + '" required>' + optionsHtml + '</select>');
			$field.find("select").select2("destroy").remove().end().append($select);
			$field.find("select").select2();
			makeVisible($field);
			
			if($field.hasClass("b-kitchen__add-form__trade")) {
				$field.find("select").change();
			}
		}
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
			if($equipmentItem) $(".i-tech .b-kitchen__item__type_empty").before($equipmentItem);
			
			function getEquipment(data) {
				data = $.parseJSON(data);
				if(!data) return false;
				if(!data.title) data.title="";
				if(!data.brand) data.brand="";
				if(!data.text) data.text="";
				if(!data.rating) data.rating="";
				if(!data.price) data.price="";
				if(!data.image) data.image={
					"src": "",
					"width": "155",
					"height": "155",
					"alt": '"' + data.title + '"'
				};
				if(!data.model) data.model="";
				
				var template = document.getElementById('kitchen-equipment-template').innerHTML;
				var compiled = tmpl(template);
				return compiled(data);
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
	}
}