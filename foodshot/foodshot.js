$(document).ready(function() {
	window.foodshotPathname = "/foodshot/";
	window.authUrl = "http://www.foodclub.ru/auth/";

	$(".b-input-file-block").each(function() {
		new InputFile($(this));
	});
	
	window.addFoodshotForm = new AddFoodshotForm();
	window.foodshotBoard = new FoodshotBoard("foodshotBoard", [], {});
	window.foodshotsJSON = {elems:[]};

	checkAddress(foodshotBoard);
	setHistoryAdapter();
});

function setHistoryAdapter() {
	var History = window.History;
	if (!History.enabled) return;

	History.pageTitle = $("title").text();
	History.formPathname = "Добавить foodshot на foodclub.ru";
	History.detailPathname = "foodshot на foodclub.ru";
	
	History.Adapter.bind(window, 'statechange', function() {
		if(!History.pushFlag) {//for- and back- browser navigation
			var State = History.getState();
			if(State.data.id) {
				var id = State.data.id;
				var itemObject = foodshotBoard.getItemObject(id);
				History.adaptFlag = true;
				foodshotBoard.showDetail(itemObject);

			} else if(State.data["add"]) {
				$("#add-foodshot-button").click();
			} else {
				$("#opaco").click();
			}
		}
		History.pushFlag = false;
		//History.log(History.getState().data, "Adapter");
	});
}

function pushHistory(id, name, flag) {
	var History = window.History;
	if (!History.enabled) return;

	if(History.adaptFlag) {
		History.adaptFlag = false;
		return;
	}

	History.pushFlag = true;
	if(id && id == "add") {
		History.pushState({id: undefined, "add": true}, History.formPathname, window.foodshotPathname + "add/");
	} else if(id) {
		var title = History.detailPathname;
		if(name) {
			title = name + " — " + History.detailPathname;
		}
		$("title").text(title);
		var hash = "";
		if(flag && window.location.hash.search('#!') != -1) hash = window.location.hash;
		History.pushState({id: id, "add": false}, title, window.foodshotPathname + id + "/" + hash);
	} else {
		History.pushState({id: undefined, "add": false}, History.pageTitle, window.foodshotPathname);
	}
	
	//History.log(History.getState().data.id, History.getState().data["add"], "pushHistory");
}

function AddFoodshotForm() {
	var self = this;
	
	init();
	
	function init() {
		initAddButton();
		initChoice();
		initOptions();
		
		self.$elem = $("#add-foodshot-layer");
		self.$elem.data("AddFoodshotForm", self);
		self.addFoodshotFile = new AddFoodshotFile();
		self.addFoodshotUrl = new AddFoodshotUrl();

		initBackLink();
	}
	
	function initAddButton() {
		$("#add-foodshot-button").click(function() {
			pushHistory("add");

			$("#add-foodshot-layer").popup({
				closeElem: "a.b-close-icon",
				after: function(thisElem){
					if (self.$elem) return false;
				},
				onClose: function($elem) {
					$elem.hide();
					resetForm();
					$("#opaco").remove();
				}
			});
			return false;
		});
	}

	function initOptions() {
		self.options = {};
		self.options.imageMinWidth = 400;
	}
	
	function resetForm() {
		self.addFoodshotFile._reset();
		self.addFoodshotUrl._reset();

		self.addFoodshotUrl.$elem;
		self.$elem
			.removeClass("i-edit")
			.removeClass("i-url")
			.removeClass("i-file")
			.removeClass("i-preloader")
			.css({height: "auto"});
	}

	function hideForm () {
		self.$elem.find(".b-close-icon").click();
	}

	function prependFoodshot(data) {
		window.foodshotBoard._prependItem(data);
	}

	function initBackLink() {
		$(".b-af__form__heading__back").click(function() {
			resetForm();
			return false;
		});
	}
	
	function initChoice() {
		$(".b-af__choice__url").click(function() {
			self.$elem.removeClass("i-file").addClass("i-url");
			self.addFoodshotUrl.$urlInput.focus();
			return false;
		});
		
		$(".b-af__choice__file").click(function() {
			self.$elem.removeClass("i-url").addClass("i-file");
			return false;
		});
	}
	
	function clickSubmitButton() {
		var $submit = $(this);
		if(!window.foodshotBoard.isValidForm($submit)) return false;

		$.ajax({
			url: window.foodshotPathname + "add-foodshot.php",
			dataType: "json",
			data: $submit.closest("form").serialize(),
			beforeSend: beforeSend,
			success: successSubmit,
			error: ajaxError
		});

		return false;

		function beforeSend() {
			var newTop = parseInt(self.$elem.css("top")) + 170;
			self.$elem
				.addClass("i-preloader")
				.removeClass("i-file")
				.removeClass("i-url")
				.animate({height: 210, top: newTop});
		}

		function successSubmit(data) {
			setTimeout(function() {
				hideForm();
				prependFoodshot(data);
			}, 1000)
		}
	}
	
	function AddFoodshotFile() {
		var self = this;
		
		init();
		
		function init() {
			setVariables();
			handleEvents();
		}
		
		function setVariables() {
			self.$elem = $("#add-foodshot-layer__file");
			self.$elem.data("AddFoodshotFile", self);
			self.$preview = self.$elem.find(".b-preview");
			self.$screen = self.$elem.find(".b-preview__screen");

			self.$photoInput = self.$elem.find("input[name=photo]");
			self.$titleInput = self.$elem.find("input[name=title]");
			self.$descriptionTextarea = self.$elem.find("textarea[name=description]");
			self.$descriptionCounter = self.$descriptionTextarea.closest(".b-form-field");
			self.$id = self.$elem.find(".b-id-input");
			self.$submitButton = self.$elem.find(".b-af__form__submit .b-button");
		}

		function handleEvents() {
			self.$submitButton.click(clickSubmitButton);
		}

		function reset() {
			self.$titleInput.val("");
			self.$descriptionTextarea.val("");
			self.$id.val("");
			self.$descriptionCounter.data("FieldCounter")._reset();
		
			self.$screen.html('<img src="/images/spacer.gif" alt="" height="177" width="260"><input name="photo" value="" type="hidden">');
			self.$elem.find(".b-file-upload__name").text("");
			self.$elem.find(".b-form-field").removeClass("i-attention");
			self.$elem.find(":submit").attr({value: "Добавить шот"});
			
			hideErrorMessage(self.$elem);
		}

		window.addFoodshotFileImageOnload = function(img) {
			var $img = $(img);

			var marginTop = 0;
			var imageParameters = {};
			var image = new Image();
			image.src = img.getAttribute("src");

			imageParameters.width = image.width;
			imageParameters.height = image.height;

			imageParameters.previewWidth = imageParameters.width > 260 ? 260 : imageParameters.width;
			imageParameters.previewHeight = Math.floor(imageParameters.height * imageParameters.previewWidth / imageParameters.width);

			imageParameters.previewHeight = imageParameters.previewHeight > 177 ? 177 : imageParameters.previewHeight;
			imageParameters.previewWidth = Math.floor(imageParameters.width * imageParameters.previewHeight / imageParameters.height);

			if(imageParameters.width < 400 || imageParameters.previewWidth < 50 || imageParameters.previewHeight < 50) {
				//showError();
			} else {
				marginTop = Math.floor((177 - imageParameters.previewHeight) / 2);
			}

			$img.css({
					marginTop: marginTop + "px",
					width: imageParameters.previewWidth + "px",
					height: imageParameters.previewHeight
				})
				.show();
		};
		
		function fillForm(dataObj) {
			self.$id.val(dataObj.id);
			self.$titleInput.val(dataObj.name);
			self.$descriptionTextarea.val(dataObj.text).keyup();
			
			var template = document.getElementById('template-download').innerHTML;
			var compiled = tmpl(template);
			
			self.$screen.html($(compiled({files: [{url: dataObj.src}]})));
		}
		
		/*--- public methods ---*/

		this._reset = function() {
			reset();
		};
		
		this.fillForm = function(dataObj) {
			fillForm(dataObj);
		}
	}
	
	function AddFoodshotUrl() {
		var self = this;
		
		init();
		
		function init() {
			setVariables();
			handleEvents();
		}
		
		function setVariables() {
			self.$elem = $("#add-foodshot-layer__url");
			self.$elem.data("AddFoodshotUrl", self);
			self.$formUrl = self.$elem.find(".b-af__form__url");
			self.$urlInput = self.$formUrl.find(".b-input-text");
			self.preview = new Preview(self.$elem.find(".b-preview"));
			self.$hiddenFields = self.$elem.find(".b-af__form__hidden-fields");

			self.$photoInput = self.$elem.find("input[name=photo]");
			self.$titleInput = self.$elem.find("input[name=title]");
			self.$descriptionTextarea = self.$elem.find("[name=description]");
			self.$descriptionCounter = self.$descriptionTextarea.closest(".b-form-field");
			self.$id = self.$elem.find(".b-id-input");
			self.$submitButton = self.$elem.find(".b-af__form__submit .b-button");

			self.errorMessage = 'не удалось найти изображение по указанному адресу.';
		}
		
		function handleEvents() {
			self.$formUrl.find(".b-button").click(clickFormUrlButton);
			self.$submitButton.click(clickSubmitButton);
			self.$urlInput.focus(focusUrlInput);
		}
		
		function focusUrlInput(e) {
			if($("#add-foodshot-layer").hasClass("i-edit")) {
				e.preventDefault();
				$(this).blur();
			};
		}

		function clickFormUrlButton(e) {
			hideErrorMessage(self.$elem);

			var value = $.trim(self.$urlInput.val());
			if(!checkUrlValue(value)) return false;

			var imagesArray = sendUrl(value).images;

			if(imagesArray.length <= 0) {
				showErrorMessage(self.errorMessage, self.$elem);
				self.$urlInput.val("").focus();

				if(self.$hiddenFields.is(":visible")) {
					self.$hiddenFields.slideUp(500);
				}

				return false;
			}

			self.$hiddenFields.slideDown(500);

			makePreviewCarousel(imagesArray);
			return false;
		}

		function checkUrlValue(value) {
			var sourceRegex = /^([a-z]+:\/\/)?([-a-zа-я0-9]+\.)+[a-zа-я]{2,5}(\/[-a-zа-я0-9]+)*(\.[a-z]{3,4})?(\/)?(\?([-_a-zа-я0-9]+=[-_a-zа-я0-9]+&?)*)?$/ig;
			if(value == "" || !sourceRegex.test(value)) return false;
			return true;
		}

		function sendUrl(value) {
			var result;
				
			$.ajax({
				url: window.foodshotPathname + "parse-url.php",
				data: {
					url: value
				},
				dataType: "json",
				async: false,
				beforeSend: beforeSend,
				success: function(data) {
					result = data;
					self.$elem.removeClass("i-preloader");
				},
				error: ajaxError
			});

			return result;

			function beforeSend() {
				self.$elem.addClass("i-preloader");
			}
		}

		function makePreviewCarousel(imagesArray) {
			self.preview._appendImages(imagesArray);
		}

		function reset() {
			self.$urlInput.val("");
			self.$titleInput.val("");
			self.$descriptionTextarea.val("");
			self.$id.val("");
			self.$descriptionCounter.data("FieldCounter")._reset();
			self.preview._clear();
			self.$hiddenFields.hide();
			hideErrorMessage(self.$elem);
			self.$elem.find(":submit").attr({value: "Добавить шот"});
		}
		
		function fillForm(dataObj) {
			self.$id.val(dataObj.id);
			self.$titleInput.val(dataObj.name);
			self.$descriptionTextarea.val(dataObj.text).keyup();
			self.$urlInput.val(dataObj.source);
			
			self.$hiddenFields.slideDown(500);
			makePreviewCarousel([dataObj.src]);
		}
		
		/*--- public methods ---*/

		this._reset = function() {
			reset();
		};
		
		this.fillForm = function(dataObj) {
			fillForm(dataObj);
		}
	}

	function showErrorMessage(message, $elem) {
		hideErrorMessage($elem);
		$elem.append('<div class="b-error-message"><div class="b-error-message__pointer"><div class="b-error-message__pointer__div"></div></div>' + message + '</div>');
	}

	function hideErrorMessage($elem) {
		$elem.find(".b-error-message").remove();
	}
		
	function Preview($elem) {
		var self = this;
		
		init();
		
		function init() {
			initElems();
			initOptions();
			handleEvents();
		}

		function initElems() {
			self.$elem = $elem;
			self.$screen = self.$elem.find(".b-preview__screen");
			self.$belt = self.$elem.find(".b-preview__belt");
			self.$input = self.$screen.find("input:hidden");
			self.$navLeft = self.$elem.find(".b-preview__nav__left");
			self.$navRight = self.$elem.find(".b-preview__nav__right");
		}

		function initOptions() {
			self.options = {};
			self.options.itemWidth = 259;
			self.options.itemHeight = 177;
			self.activeIndex = 0;
			self.maxIndex = 0;
			self.imagesArray = [];
		}

		function handleEvents() {
			self.$navLeft.click(clickNavLeft)
			self.$navRight.click(clickNavRight);
			self.$belt.click(clickNavRight);
		}

		function clickNavLeft() {
			self.activeIndex--;
			scrollTo();
			return false;
		}

		function clickNavRight() {
			self.activeIndex++;
			scrollTo();
			return false;
		}

		function scrollTo() {
			if(self.activeIndex < 0) {
				self.activeIndex = 0;
			} else if(self.activeIndex > self.maxIndex) {
				self.activeIndex = self.maxIndex;
			}

			var left = -1 * self.activeIndex * self.options.itemWidth;
			self.$belt.animate({marginLeft: left}, 500);
			self.$input.val(self.imagesArray[self.activeIndex]);
		}

		function appendImages(imagesArray) {
			makeBelt();
			resetValues();
			setInputValue();

			function makeBelt() {
				makeHtml();
				makeCss();

				function makeHtml() {
					var html = "";
					for(var i = 0; i < imagesArray.length; i++) {
						html += '<span class="b-preview__item"><img src="' + imagesArray[i] + '" alt="" class="b-preview__item__image"></span>';
					}
					self.$belt.html(html);
					self.$belt.find("img").load(loadImage);

					function loadImage() {
						var $this = $(this);
						var imageParameters = getImageParameters($this.attr("src"));
						if(imageParameters.width < window.addFoodshotForm.options.imageMinWidth || imageParameters.previewWidth < 50 || imageParameters.previewHeight < 50) {
							$this.parent().remove();
							splitImagesArray($this.attr("src"));
						} else {
							$this.attr({width: imageParameters.previewWidth, height: imageParameters.previewHeight});
						}
						valignImage($this);
						resetBeltCss();
						resetValues();
						setInputValue();
						showHideNav();
					}

					function getImageParameters(imageSrc) {
						var result = {};
						var image = new Image();
						image.src = imageSrc;

						result.width = image.width;
						result.height = image.height;

						result.previewWidth = result.width > self.options.itemWidth ? self.options.itemWidth : result.width;
						result.previewHeight = Math.ceil(result.height * result.previewWidth / result.width);

						result.previewHeight = result.previewHeight > self.options.itemHeight ? self.options.itemHeight : result.previewHeight;
						result.previewWidth = Math.ceil(result.width * result.previewHeight / result.height);

						return result;
					}

					function valignImage($img) {
						var marginTop = Math.floor((self.options.itemHeight - $img.attr("height")) / 2);
						$img.css({marginTop: marginTop + "px"});
					}

					function resetBeltCss() {
						self.$belt.width(self.options.itemWidth * self.$belt.find(".b-preview__item").size());
					}

					function showHideNav() {
						if(imagesArray.length > 1) {
							self.$navLeft.show();
							self.$navRight.show();
							return;
						}
						self.$navLeft.hide();
						self.$navRight.hide();
					}
				}
				
				function makeCss() {
					self.$belt
						.width(self.options.itemWidth * imagesArray.length)
						.css({marginLeft: 0});
				}
			}

			function splitImagesArray(src) {
				for(var i = 0; i < imagesArray.length; i++) {
					if(imagesArray[i] == src) {
						imagesArray.splice(i, 1);
					}
				}
			}

			function resetValues() {
				self.imagesArray = imagesArray
				self.activeIndex = 0;
				self.maxIndex = imagesArray.length - 1;
			}

			function setInputValue() {
				self.$input.val(imagesArray[self.activeIndex]);
			}
		}

		function clearForm() {
			self.$belt.html('<img src="/images/spacer.gif" width="260" height="177" alt="" />').css({marginLeft: 0, width: "auto"});
			self.$elem.find("[name=photo]").val("");
			self.activeIndex = 0;
			self.maxIndex = 0;
			self.imagesArray = [];
		}

		/*--- public methods ---*/

		this._appendImages = function(imagesArray) {
			appendImages(imagesArray);
		};

		this._clear = function() {
			clearForm();
		};
	}
	
	function fillForm(dataObj) {
		if(!dataObj || !dataObj.src) return;
		$("#foodshotDetail").find(".b-close-icon").click();
		$("#add-foodshot-button").click();
		
		if(dataObj.source) {
			$(".b-af__choice__url").click();
			$("#add-foodshot-layer__url").data("AddFoodshotUrl").fillForm(dataObj);
		} else {
			$(".b-af__choice__file").click();
			$("#add-foodshot-layer__file").data("AddFoodshotFile").fillForm(dataObj);
		} 
	}
	
	function makeEdit() {
		self.$elem.addClass("i-edit");
		self.$elem.find(":submit").attr({value: "Редактировать шот"});
	}
	
	/*---public methods---*/
	this.fillForm = function(dataObj) {
		fillForm(dataObj);
		return self;
	}
	
	this.makeEdit = function() {
		makeEdit();
		return self;
	}
}

function FoodshotBoard(id, elems, params) {
	var self = this;
	
	self.items = [];
	self.grid = {};
	self.cols = [];
	var options = params || {};
	
	setDefaults();
	
	self.$elem = $("#" + id);
	self.pagesNum = 1;
	self.page = 1;
	
	init();
	
	function init() {
		makeGrid();
		makeCoord();
		loadPage();

		boardEvents();
		documentEvents();
	}

	function documentEvents() {
		$(document)
			.bind("scroll", scrollDocument)
			.bind("keyup", keyupDocument);
		$(window).bind("resize", resizeWindow).resize();
		
		function keyupDocument(e) {
			if(e.which == 27) {
				if(document.getElementById("foodshotDetail")) {
					$("#foodshotDetail .b-foodshot-detail__close .b-close-icon").click();
				}
				
				if($("#add-foodshot-layer").is(":visible")) {
					$("#add-foodshot-layer .b-close-icon").click();
				}
				return;
			}
			
			if(!document.getElementById("foodshotDetail")) return;
			
			if(e.which == 37) {
				$("#foodshotDetail .b-foodshot-detail__nav__prev").click();
			} else if(e.which == 39) {
				$("#foodshotDetail .b-foodshot-detail__nav__next").click();
			}
		}
	}

	function scrollDocument() {
		increaseOpaco();
		if(self.scrollFlag || !checkHeight()) return;
		self.scrollFlag = true;
		loadPage();
	}

	function resizeWindow() {
		self.windowHeight = $(window).height();
		if(self.windowHeight < options.minWindowHeight) {
			self.windowHeight = 600;
		}
	}

	function increaseOpaco() {
		var $opaco = $("#opaco");
		if(!$opaco.is("div")) return false;

		$opaco.height($(document).height());
	}

	function loadPage() {
		if(self.page > self.pagesNum) {
			self.$elem.removeClass("i-preloader");
			return;
		}
		
		var profileOwnerId = "";
		if(window.profileOwnerId) {
			profileOwnerId = "&profileOwnerId=" + window.profileOwnerId;
		}

		self.$elem.addClass("i-preloader");

		$.ajax({
			type: "POST",
			dataType: "json",
			url: window.foodshotPathname + "foodshotJSON.php?page=" + self.page + profileOwnerId,
			success: function(data) {
				self.pagesNum = data.pages;

				var foodshotsJSONElemsLength = foodshotsJSON.elems.length;
				window.foodshotsJSON.elems = window.foodshotsJSON.elems.concat(data.elems);
				elems = window.foodshotsJSON.elems;

				getItems(foodshotsJSONElemsLength);
				setItemsData(foodshotsJSONElemsLength);				
				positionItems(foodshotsJSONElemsLength);
				adaptBoardHeight();

				window.upButton.styleElements();
				self.scrollFlag = false;
				
				//scroll to id
				if(window.scrollDocumentToIdFlag) {
					for(var i = 0; i < window.foodshotsJSON.elems.length; i++) {
						if(window.foodshotsJSON.elems[i].id == window.scrollDocumentToIdFlag) {
							var scrollToElemObject = window.foodshotsJSON.elems[i];
							window.scrollDocumentToIdFlag = undefined;
						}
					}
				}

				if(checkHeight() || window.scrollDocumentToIdFlag) {
					loadPage();
				}
				
				if(scrollToElemObject) {
					scrollDocumentToElem(scrollToElemObject);
					
				}
			},
			error: ajaxError
			
		});

		self.page++;
	}
	
	function scrollDocumentToElem(elemObject) {
		var $elem = $("[data-id=" + elemObject.id + "]");
		var top = parseInt($elem.offset().top) - 100;
		$.scrollTo(top);
		
		switch(window.onLoadAction) {
			case "like":
				likeAction();
				break;
		}
		
		function likeAction() {
			$elem.find(".b-like").click();
			window.onLoadAction = undefined;
		}
	}

	function checkHeight() {
		var height = 3 * self.windowHeight + $(window).scrollTop();
		if(self.$elem.height() < height) {
			return true;
		}

		return false;
	}
	
	this.getItemObject = function(id) {
		for(var i = 0; i < self.items.length; i++) {
			if(self.items[i].id == id) {
				return self.items[i];
			}
		}
		return {id: id};
	};
	
	function setItemsData(index) {
		for(var i = index; i < self.items.length; i++) {
			self.items[i].id = self.items[i].$elem.attr("data-id");
		}
	}
	
	function moveLowItems(lowItems, diff) {
		
		for(var i = 0; i < lowItems.length; i++) {
			var $elem = self.items[lowItems[i]].$elem;
			var top = $elem.css("top");
			$elem.css({top: parseInt(top) + diff*1 + "px"});
		}
		
	}
	
	function getLowItems($itemElem) {
		var index = $itemElem.attr("data-index"),
			colNum = self.items[index].column,
			array = [];

		for(var i = 0; i < self.cols[colNum].length; i++) {
			array[i] = self.cols[colNum][i];
		}
		
		for(var i = 0; i < array.length; i++) {
			if(array[i] == index) {
				var result = array.slice(i+1);
				break;
			}
		}
		
		return result;
	}
	
	function adaptBoardHeight() {
		self.$elem.height(self.coord[self.coord.length - 1].y + 40);
	}
	
	function boardEvents() {
		self.$elem
		.delegate(".b-comment-icon__type-button", "click", function() {
			self.changeItemContent($(this), showCommentsForm, {$el: $(this)});
			return false;
		})
		.delegate(".b-like-icon", "click", function() {
			self.likeAction($(this));
			return false;
		})
		.delegate(".b-form-field__type-comment__button", "click", function() {
			if(!self.isValidForm($(this))) return false;
			
			self.changeItemContent($(this), self.addComment, {$el: $(this)});
			return false;
		})
		.delegate("textarea", "keyup",  function() {
			self.changeItemContent($(this), resizeTextarea, {$el: $(this)});
		})
		.delegate(".b-foodshot-board__item-content-image, .b-foodshot-board__item-comments-hidden__button", "click",  function() {
			var itemObject = getItemObject({$el: $(this)});
			self.showDetail(itemObject);
			return false;
		});
		
	};

	function deleteFoodshot(itemObject) {
		itemObject.$elem.remove();
		var deletedItemPosition = getDeletedItemPosition();

		if(deletedItemPosition === false) return;

		removeFoodshotsAfter(deletedItemPosition);
		window.foodshotBoard.items.splice(deletedItemPosition, window.foodshotBoard.items.length - deletedItemPosition);
		window.foodshotsJSON.elems.splice(deletedItemPosition, 1);

		self.cols = [];
		makeCoord();
		getItems(deletedItemPosition);
		setItemsData(0);
		positionItems(0);
		adaptBoardHeight();

		window.upButton.styleElements();

		function removeFoodshotsAfter(deletedItemPosition) {
			for(var i = deletedItemPosition; i < window.foodshotBoard.items.length; i++) {
				window.foodshotBoard.items[i].$elem.remove();
			}
		}

		function getDeletedItemPosition() {
			for(var i = 0; i < foodshotBoard.items.length; i++) {
				if(foodshotBoard.items[i].id == itemObject.id) return i;
			}
			return false;
		}
	}
	
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
	
	this.showDetail = function(itemObject, which) {
		var data = {
			id: itemObject.id
		};
		if(which && which == 37) data.prev = "Y";
		if(which && which == 39) data.next = "Y";
		
		$.ajax({
			type: "POST",
			dataType: "json",
			url: window.foodshotPathname + "detailJSON.php",
			data: data,
			success: function(detailData) {
				if(document.getElementById("foodshotDetail")) $("#foodshotDetail .b-foodshot-detail__close .b-close-icon").click();
				if(!detailData.id )detailData.id = itemObject.id;
				if(data.prev || data.next) itemObject = getSiblingItemObject(detailData.id, data);
				
				itemObject.detail = new FoodshotDetail(detailData, itemObject);
				popup(itemObject);
				addContent(itemObject.detail.$elem);
				pushHistory(itemObject.id, detailData.name, true);
			},
			error: ajaxError
		});
		
		function getSiblingItemObject(id, data) {
			var result = foodshotBoard.getItemObject(id);//{id: id}
			
			return result;
		}
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
	
	function resizeTextarea(args) {
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
	
	this.isValidForm = function($elem) {
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
	
	function addContent($content) {}
	
	this.addComment = function(args) {
		var $button = args.$el;
		
		var $itemElem = $button.closest(".b-foodshot-board__item");
		var itemObject = args.itemObject || self.items[$itemElem.attr("data-index")];
		
		var $form = $button.closest("form"),
			$textarea = $form.find("textarea"),
			commentText = args.commentText || $textarea.val();
		
		hideForm();
		
		var id = $button.closest(".b-foodshot-board__item").attr("data-id");
		var commentObj = self.sendComment(commentText, id);
		self.appendComment({commentObj: commentObj, $itemElem: $itemElem});

		function hideForm() {
			$textarea.val("").css({height: ""});
			$itemElem.find("div.b-foodshot-board__item-action").removeClass("i-foodshot-board__item-action-invert");
		}
	};

	this.appendComment = function(args) {
		var commentObj = args.commentObj,
			$itemElem = args.$itemElem;

		var id = commentObj.id;
		var commentTextEdited = commentObj.text;
		
		var $comment = compileComment(id, commentTextEdited);
		
		var $commentList = getCommentList();
		$commentList.append($comment);
		
		addContent($comment);
		
		hideComment();
		
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
	
	this.deleteComment = function(args) {
		var $commentItem = args.itemObject.$elem.find(".b-comment[data-id=" + args.id + "]");
		if(!$commentItem.is("div")) return false;
		$commentItem.remove();
		
		$.ajax({
			url: window.foodshotPathname + "delete_comment.php",
			type: "POST",
			data: "itemId=" + args.itemObject.id + "&id=" + args.id,
			error: ajaxError
		});
	}
	
	this.sendComment = function(commentText, id) {
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
	
	this.changeItemContent = function($el, fnc, args) {
		var $itemElem = $el.closest(".b-foodshot-board__item");
		var itemObject = lowestItemInColumn = self.items[$itemElem.attr("data-index")];
		
		var bottom1 = itemObject.getBorders().bottom;
		
		fnc(args);
		
		var bottom2 = itemObject.getBorders().bottom;
		var diff = bottom2 - bottom1;
		
		var lowItems = getLowItems($itemElem);
		
		if(lowItems.length > 0) {
			lowestItemInColumn = self.items[lowItems[lowItems.length - 1]];
		}
		
		lowestBottom = lowestItemInColumn.getBorders().bottom;
		
		moveLowItems(lowItems, diff);
		
		var index = getCoordY(lowestItemInColumn);

		changeGridY(lowestItemInColumn, index);
		adaptBoardHeight();
	
		function getCoordY(itemObject) {
			for(var i = 0; i < self.coord.length; i++) {
				if(self.coord[i].x == itemObject.getBorders().left) {
					return i;
					break;
				}
			}

			return false;
		}
		
	};
	
	this.likeAction = function($button) {

		var $itemElem = $button.closest(".b-foodshot-board__item");
		var itemObject = self.items[$itemElem.attr("data-index")];

		if(!window.userObject) {
			var backurl = window.location.pathname;
			window.location.href = window.authUrl + "?backurl=" + backurl + "#like" + itemObject.id;
			return;
		}

		if($button.hasClass("b-like-icon__type-disabled")) return;
		
		var action = "like";
		if($button.hasClass("b-like-icon__type-active")) action = "unlike";
		
		var likeObject = self.sendLike(itemObject, action);
		
		self.markLike($button, likeObject);
	}

	this.sendLike = function(itemObject, action) {
		var result;

		$.ajax({
			type: "POST",
			url: window.foodshotPathname + "like.php",
			async: false,
			data: "id=" + itemObject.id + "&action=" + action,
			success: function(data) {
				result = data;
			}
		});
		
		return result;
	}

	this.markLike = function($button, likeObject) {
		$button.siblings(".b-like-num").text(likeObject);
		$button.toggleClass("b-like-icon__type-active");
	}
	
	function showCommentsForm(args) {
		var $button = args.$el;
		$button.closest(".b-foodshot-board__item-action")
			.addClass("i-foodshot-board__item-action-invert")
			.find("textarea").focus();
	}
	
	function getItems(index) {
		for(var i = index; i < elems.length; i++) {
			var item = new Foodshot(elems[i], i);
			if(item.$elem) self.items.push(item);
		}
		
		for(var i = index; i < self.items.length; i++) {
			self.$elem.append(self.items[i].$elem);
		}
	}
	
	function positionItems(index) {
		for(var i = index; i < self.items.length; i++) {
			var startIndex = 0;
			positionItemsY(self.items[i], startIndex);
			
			positionItemsX(self.items[i], startIndex);
			
			setColumn(self.items[i], startIndex, i);
			
			changeGridY(self.items[i], startIndex);
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
		self.grid.width = options.elemWidth;//self.items[0].$elem.outerWidth();
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
		options.minWindowHeight = 600;
		options.elemWidth = 188;
	}

	function clearBoard() {
		for(var i = 0; i < self.items.length; i++) {
			self.items[i].$elem.remove();//not .empty() to save script-templates
		}
		self.items = [];
		self.grid = {};
		self.cols = [];

		self.$elem
			.height(20)
			.undelegate();
		$(document).unbind("scroll", scrollDocument);
		$(window).unbind("resize", resizeWindow);
		window.upButton.$clickable.height(0);
	}

	function prependItem(data) {
		clearBoard();
		window.foodshotBoard = new FoodshotBoard("foodshotBoard", [], {});
		window.foodshotsJSON = {elems:[]};
	}

	/*--- public methods ---*/

	this._clearBoard = function() {
		clearBoard();
	};

	this._prependItem = function(data) {
		prependItem(data);
	};

	this.deleteFoodshot = function(itemObject) {
		deleteFoodshot(itemObject);
	};
}


function Foodshot(obj, index) {
	var self = this;
	
	init();
	
	function init() {
		var template = document.getElementById('foodshot-template').innerHTML;
		var compiled = tmpl(template);
		
		if(!obj.user_liked) {
			obj.user_liked = false;
		}
		if(!obj.id || !obj.href || !obj.name || !obj.text || !obj.author || (!obj.likeNum && obj.likeNum != "") || !obj.image) return;
		
		var result = compiled(obj);

		self.$elem = $(result);
		
		self.$elem.attr({"data-index": index});
	}
	
	this.getBorders = function() {
		var $img = self.$elem.find(".b-foodshot-board__item-content-image img");
		var elemHeight = self.$elem.outerHeight();
		
		/*if($img.height() == 0) {
			var image = new Image();
			image.src = $img.attr("src");
			elemHeight += $img.attr("height");
		}*/
		
		var borderBottom = parseInt(self.$elem.css("top")) + elemHeight;
		var borderLeft = parseInt(self.$elem.css("left"));
		var borderRight = borderLeft + self.$elem.outerWidth();
		
		return {bottom: borderBottom, right: borderRight, left: borderLeft};
	};
}

function FoodshotDetail(data, itemObject) {
	var self = this;
	
	init();
	
	function init() {
		compileFoodshotDetail();
		
		setTimeout(function() {
			renderLikeButtons();
		}, 0);
		
		handleEvents();
	}
	
	function compileFoodshotDetail() {
		var template = document.getElementById('foodshot-detail-template').innerHTML;
		var compiled = tmpl(template);
		if(!data.user_liked) {
			data.user_liked = false;
		}
		if(!data.deleteIcon) {
			data.deleteIcon = false;
		}
		var result = compiled(data);
		
		self.$elem = $(result);
		valignImage();
	}

	function valignImage() {
		var $img = self.$elem.find(".b-foodshot-detail__image img");
		var height = parseInt($img.attr("height"));
		if(height <= 400) {
			$img.css({marginTop: (400 - height) / 2})
		}
	}

	function renderLikeButtons() {
		self.$likePannel = self.$elem.find(".b-foodshot-detail__like");
		var url = window.location.href;
		var text = data.description.formatted;
		var media = "http://www.foodclub.ru" + data.image.src;
		if(itemObject.id == 43909) {
			var media = "http:\/\/www.foodclub.ru\/upload\/iblock\/42f\/1369829654_Посуда\.jpg";
		}

		self.$likePannel.find(".b-foodshot-detail__like__item").each(function() {
			var $this = $(this);
			if($this.hasClass("i-facebook")) {
				var fbId = "foodshotDetailFbId";
				$this.attr({id: fbId}).append('<fb:like href="' + url + '" send="false" layout="button_count" width="100" show_faces="false" font="arial"></fb:like>');
				try{
					FB.XFBML.parse(document.getElementById(fbId));
				}
				catch(ex){}

			} else if($this.hasClass("i-vkontakte")) {
				var vkId = "foodshotDetailVkId";

				//var apiIdForLikeButtons = 2404991;
				//var apiIdForAuthorization = 2672095;
				
				$this.append('<div id="' + vkId + '"></div>');
				
				//VK.init({apiId: apiIdForLikeButtons, onlyWidgets: true});
				VK.Widgets.Like(vkId, {type: "mini", height: 20, pageTitle: text, pageUrl: url/*, pageImage: media*/});
				//VK.init({apiId: apiIdForAuthorization});

			} else if($this.hasClass("i-twitter")) {

				$this.append('<iframe scrolling="no" frameborder="0" allowtransparency="true" src="http://platform.twitter.com/widgets/tweet_button.html?count=horizontal&amp;lang=en&amp;size=m&amp;text=' + text + '&amp;url=' + url + '" class="twitter-share-button twitter-count-horizontal" style="width: 109px; height: 20px;"></iframe>');
			} else if($this.hasClass("i-pinterest"))  {
				$this.append("<a href='//pinterest.com/pin/create/button/?url=" + url + "&media=" + media + "&description=" + text + "' data-pin-do='buttonPin' data-pin-config='above' target='_blank'><img src='//assets.pinterest.com/images/pidgets/pin_it_button.png' /></a>");
			}
		});
	}
	
	function handleEvents() {
		self.$elem
		.delegate(".b-like-icon", "click", clickLike)
		.delegate(".b-form-field__type-comment__button", "click", clickCommentButton)
		.delegate(".b-comment-block-list .b-admin-panel__delete", "click", clickDeleteComment)
		.delegate(".b-foodshot-detail__admin-buttons .b-delete-icon", "click", clickDeleteFoodshot)
		.delegate(".b-foodshot-detail__admin-buttons .b-edit-icon", "click", clickEditFoodshot)
		.delegate(".b-foodshot-detail__nav__prev", "click", clickNavPrev)
		.delegate(".b-foodshot-detail__nav__next", "click", clickNavNext);
		
		function clickLike(e) {
			self.likeAction($(this));
			e.preventDefault();
		}
		
		function clickCommentButton(e) {
			if(!foodshotBoard.isValidForm($(this))) return false;

			var $button = $(this),
				$form = $button.closest("form"),
				$textarea = $form.find("textarea"),
				commentText = $textarea.val();

			var commentObj = foodshotBoard.sendComment(commentText, itemObject.id);
			
			if(itemObject.$elem) {
				var $boardButton = itemObject.$elem.find(".b-comment-icon__type-button");
				foodshotBoard.changeItemContent($boardButton, foodshotBoard.appendComment, {commentObj: commentObj, $itemElem: itemObject.$elem});
			}
			self.addComment(commentObj, $button);
			e.preventDefault();
		}
		
		function clickDeleteComment(e) {
			if(confirm("Удалить комментарий?")) {
				deleteComment($(this));
			}
			e.preventDefault();
		}
		
		function clickDeleteFoodshot(e) {
			if(confirm($(this).attr("title") + "?")) {
				deleteFoodshot($(this));
			}
			e.stopPropagation();
			e.preventDefault();
		}
		
		function clickEditFoodshot(e) {
			var dataObj = getDataObj();
			$("#add-foodshot-layer").data("AddFoodshotForm").makeEdit().fillForm(dataObj);
			e.preventDefault();
			
			function getDataObj() {
				var result = {};
				result.id = itemObject.id;
				
				if(self.$elem.find(".b-foodshot-detail__description__source a").size() != 0) {
					result.source = self.$elem.find(".b-foodshot-detail__description__source a").attr("href");
				}
				
				result.src = self.$elem.find(".b-foodshot-detail__image img").attr("src");
				result.name = self.$elem.find(".b-foodshot-detail__image img").attr("alt");
				result.text = self.$elem.find(".b-comment__text").text();
				
				return result;
			}
		}
		
		function clickNavPrev(e) {
			foodshotBoard.showDetail({id: data.id}, 37);
			e.preventDefault();
		}
		
		function clickNavNext(e) {
			foodshotBoard.showDetail({id: data.id}, 39);
			e.preventDefault();
		}
	}
	
	function deleteFoodshot($deleteButton) {
		var id = itemObject.id;
		self.$elem.find(".b-foodshot-detail__close .b-close-icon").click();
		
		if(itemObject.$elem) {
			foodshotBoard.deleteFoodshot(itemObject);
		}
		
		$.ajax({
			url: window.foodshotPathname + "delete_foodshot.php",
			type: "POST",
			data: "itemId=" + itemObject.id,
			error: ajaxError
		});
	}
	
	function deleteComment($deleteButton) {
		var id = $deleteButton.closest(".b-comment-block").attr("data-id");
		$deleteButton.closest(".b-comment-block").remove();
		
		if(itemObject.$elem && itemObject.$elem.find(".b-comment[data-id=" + id + "]").is("div")) {
			foodshotBoard.changeItemContent(itemObject.$elem.find(".b-foodshot-board__item-content"), foodshotBoard.deleteComment, {itemObject: itemObject, id: id});
			return;
		}
		
		$.ajax({
			url: window.foodshotPathname + "delete_comment.php",
			type: "POST",
			data: "itemId=" + itemObject.id + "&id=" + id,
			error: ajaxError
		});
	}
	
	this.addComment = function(commentObj, $button) {
		var $form = $button.closest("form"),
			$textarea = $form.find("textarea");
		
		self.appendComment(commentObj);
		
		$textarea.val("");
	}

	this.appendComment = function(commentObj) {
		var $comment = compileComment(commentObj.id, commentObj.text, commentObj.date);
		var $commentList = getCommentList();
		$commentList.append($comment);
		
		function getCommentList() {
			var $commentList = self.$elem.find(".b-comment-block-list");
			if(!$commentList.is("div")) {
				self.$elem
					.find(".b-foodshot-detail__description")
					.after('<div class="b-foodshot-detail__comments"><div class="b-comment-block-list"></div></div>');
				
				$commentList = self.$elem.find(".b-comment-block-list");
			}
			
			return $commentList;
		}
		
		function compileComment(id, text, date) {
			var commentObj = {
				"id": id,
				"author": {
					"href": userObject.href,
					"src": userObject.src,
					"name": userObject.name
				},
				"text": text,
				"date": date
			};
			var template = document.getElementById('foodshot-detail-comment-template').innerHTML;
			var compiled = tmpl(template);
			
			return $(compiled(commentObj));
		}
	};
	
	this.likeAction = function($button) {

		if(!window.userObject) {
			var backurl = window.location.pathname;
			window.location.href = window.authUrl + "?backurl=" + backurl + "#like" + itemObject.id;
			return;
		}

		var action = "like";
		if($button.hasClass("b-like-icon__type-active")) action = "unlike";

		var likeObject = foodshotBoard.sendLike(itemObject, action);
		
		self.markLike($button, likeObject);

		if(itemObject.$elem) {
			var $button = itemObject.$elem.find(".b-like-icon__type-button");
			foodshotBoard.markLike($button, likeObject);
		}
	};
	
	this.markLike = function($button, likeObject) {
		$button.siblings(".b-like-num").text(likeObject);
		$button.toggleClass("b-like-icon__type-active");
	};
	
}

//popup
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

				if(document.getElementById("bx-panel")) {
					topPx += $("#bx-panel").height();
				}
				
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

				$self
					.show()
					.css({
						marginLeft: marginLeft,
						left: leftPx
					})
					.css({
						top: topPx + "px"
					});
				
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
					closeElem
						.unbind("click")
						.bind("click", function(e) {
							closeFunc();
							e.preventDefault();
							pushHistory();
						});
				}
				
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

function checkAddress(foodshotBoard) {
	if(window.location.pathname.search("foodshot") == -1) return;

	checkDetail();
	checkLikeAction();

	function checkLikeAction() {
		var id = /^.*like(\d+)*.*$/.exec(window.location.hash);
		if(!id) return;
		id = id[1];
		
		window.scrollDocumentToIdFlag = id;
		window.onLoadAction = "like";
	}

	function checkDetail() {
		var id = /^\S*\/foodshot\/(\d+)\/?\S*$/.exec(window.location);
		
		if(id && id.length && id[1]) {
			id = id[1];
		}
		if(id) {
			var timeCounter = 0;
			trackFoodshotBoardItems(function() {
				var itemObject = foodshotBoard.getItemObject(id);
				foodshotBoard.showDetail(itemObject);
			}, timeCounter);
		} else if(String(window.location).search("foodshot/add") != -1) {
			$("#add-foodshot-button").click();
		}
	}

	function trackFoodshotBoardItems(fn, timeCounter) {
		var interval = setInterval(function() {intervalFunction(fn)}, 500);

		function intervalFunction(fn) {
			timeCounter += 500;

			if(timeCounter >= 10000) {
				clearInterval(interval);
			} else if(foodshotBoard.items && foodshotBoard.items.length > 0) {
				fn();
				clearInterval(interval);
			}
		}
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

function InputFile($elem, params) {
	var self = this;
	self.$elem = $elem;
	self.$input = self.$elem.find(":file");
	
	init();
	
	function init() {
		
		setOptions();
		
		createHTML();
		
		self.$name = self.$elem.find(".b-input-file-block__file-name");
		//self.$newName = self.$elem.find(".b-input-file-block__new-file-name");
		
		self.$button.click(function() {
			self.$input.click();
			return false;
		});
				
		fileHandler();
		
	}
	
	function setOptions() {
		self.options = {}, params = params || {};
		self.options.extentions = params.extentions || undefined;
		self.options.messages = params.maessages ||
			{
				wrongExtention : "Неверное расширение файла"
			};
		self.options.browseButtonText = params.browseButtonText || self.$input.attr("data-text") || '';
		self.options.newNameInputName = params.newNameInputName || self.$input.attr("data-name") || self.$input.attr("name") + '-new-name';
		self.options.newNameTitle = params.newNameTitle || self.$input.attr("data-new-name-title") || '';
		//self.options.newName = params.newName || self.$input.attr("data-new-name") || false;
	}
	
	function clearValue() {
		self.$elem.find(":file").remove();
		self.$elem.find(".b-input-file-block__browse-button").after(self.$input);
	}
	
	function createHTML() {
		self.$elem.html('<a href="#" class="b-button b-input-file-block__browse-button">' + self.options.browseButtonText + '</a><div class="b-input-file-block__file-name"></div>');
		
		self.$button = self.$elem.find(".b-input-file-block__browse-button");
		
		self.$button.after(self.$input);
	}
	
	this._handleChanges = function() {
		
		var fileTitle = getFileTitle();
		
		var fileExt = getFileExt(fileTitle);
		
		if(isValidFileExt(fileExt)) {
			self.$name.text(fileTitle);
			self.$name.removeClass("i-attention");
			//self.$newName.show();
		} else {
			self.$name.text(self.options.messages.wrongExtention);
			self.$name.addClass("i-attention");
			clearValue();
		}
		
		self.$name.css({display:"block"});
		
		//getExtIconPos(fileExt);
	}
	
	function filesize (url) {
		// Get file size  
		// 
		// version: 1109.2015
		// discuss at: http://phpjs.org/functions/filesize
		// +   original by: Enrique Gonzalez
		// +      input by: Jani Hartikainen
		// +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
		// +   improved by: T. Wild
		// %        note 1: This function uses XmlHttpRequest and cannot retrieve resource from different domain.
		// %        note 1: Synchronous so may lock up browser, mainly here for study purposes.
		// *     example 1: filesize('http://kevin.vanzonneveld.net/pj_test_supportfile_1.htm');
		// *     returns 1: '3'
		var req = this.window.ActiveXObject ? new ActiveXObject("Microsoft.XMLHTTP") : new XMLHttpRequest();
		if (!req) {
			throw new Error('XMLHttpRequest not supported');
		}
	 
		req.open('HEAD', url, false);
		req.send(null);
	 
		if (!req.getResponseHeader) {
			try {
				throw new Error('No getResponseHeader!');
			} catch (e) {
				return false;
			}
		} else if (!req.getResponseHeader('Content-Length')) {
			try {
				throw new Error('No Content-Length!');
			} catch (e2) {
				return false;
			}
		} else {
			return req.getResponseHeader('Content-Length');
		}
	}
	
	function isValidFileExt(fileExt) {
		if(!self.options.extentions) return true;
		
		var flag = false;
		
		for(var i = 0; i < self.options.extentions.length; i++) {
			if(fileExt.toLowerCase() == self.options.extentions[i]) flag = true;
		}
		
		return flag;
	}
	
	function getFileExt(fileTitle) {
		var RegExExt =/.*\.(.*)/;
		var fileExt = fileTitle.replace(RegExExt, "$1");
		
		return fileExt;
	}
	
	function getFileTitle() {
		var value = self.$input.val();
		
		reWin = /.*\\(.*)/;
		var fileTitle = value.replace(reWin, "$1");
		
		reUnix = /.*\/(.*)/;
		fileTitle = fileTitle.replace(reUnix, "$1");
		
		/*if (fileTitle.length > 18) {
			fileTitle = "..." + fileTitle.substr(fileTitle.length - 16, 16);
		}*/
		
		return fileTitle;
	}
	
	function getExtIconPos(ext) {
		var pos;
		if (ext){
			switch (ext.toLowerCase()) {
				case 'doc': pos = '0'; break;
				case 'bmp': pos = '16'; break;                       
				case 'jpg': pos = '32'; break;
				case 'jpeg': pos = '32'; break;
				case 'png': pos = '48'; break;
				case 'gif': pos = '64'; break;
				case 'psd': pos = '80'; break;
				case 'mp3': pos = '96'; break;
				case 'wav': pos = '96'; break;
				case 'ogg': pos = '96'; break;
				case 'avi': pos = '112'; break;
				case 'wmv': pos = '112'; break;
				case 'flv': pos = '112'; break;
				case 'pdf': pos = '128'; break;
				case 'exe': pos = '144'; break;
				case 'txt': pos = '160'; break;
				default: pos = '176'; break;
			}
		}
	}
	
	function fileHandler() {
		self.$elem.find(":file").change(function() {
			self._handleChanges();
		});
	}
	
	/*---public methods---*/
	
	this._fileHandler = function() {
		fileHandler();
	}
}