<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
global $USER;
?>



<link rel="stylesheet" type="text/css" href="/css/form-kich-style.css">
<link rel="stylesheet" type="text/css" href="/css/form.css">
<script src="/js/form.js"></script>
<script src="/js/form-kich-script.js"></script>


<div class="b-popup b-popup__style_1" id="kitchen-equipment-add-form" style="display: none;">
	<a href="#" class="b-popup__close"></a>
	<div class="b-popup__heading">Добавить технику</div>
	<div class="b-popup__content b-kitchen__add-form">
		<form action="/php/get-kitchen-equipment.php" method="post">
			<div class="b-form-field b-kitchen__add-form__type">
				<label class="b-form-label">Тип техники (Мультиварка)</label>
<?$APPLICATION->IncludeComponent("custom:tech.type.list","select",Array(
		"DISPLAY_DATE" => "Y",
		"DISPLAY_NAME" => "Y",
		"DISPLAY_PICTURE" => "Y",
		"DISPLAY_PREVIEW_TEXT" => "Y",
		"AJAX_MODE" => "Y",
		"IBLOCK_TYPE" => "news",
		"IBLOCK_ID" => "31",
		"NEWS_COUNT" => "9999",
		"SORT_BY1" => "NAME",
		"SORT_ORDER1" => "ASC",
		"SORT_BY2" => "SORT",
		"SORT_ORDER2" => "ASC",
		"FILTER_NAME" => "",
		"FIELD_CODE" => Array("ID"),
		"PROPERTY_CODE" => Array("DESCRIPTION"),
		"CHECK_DATES" => "Y",
		"DETAIL_URL" => "",
		"PREVIEW_TRUNCATE_LEN" => "",
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"SET_TITLE" => "N",
		"SET_STATUS_404" => "N",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "Y",
		"ADD_SECTIONS_CHAIN" => "Y",
		"HIDE_LINK_WHEN_NO_DETAIL" => "Y",
		"PARENT_SECTION" => "",
		"PARENT_SECTION_CODE" => "",
		"INCLUDE_SUBSECTIONS" => "Y",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600",
		"CACHE_FILTER" => "Y",
		"CACHE_GROUPS" => "Y",
		"DISPLAY_TOP_PAGER" => "Y",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"PAGER_TITLE" => "Новости",
		"PAGER_SHOW_ALWAYS" => "Y",
		"PAGER_TEMPLATE" => "",
		"PAGER_DESC_NUMBERING" => "Y",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "Y",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_ADDITIONAL" => ""
	)
);?>

			</div>
			
			<div class="b-form-field b-kitchen__add-form__trade i-invisible" data-ajax-url="/php/get-kitchen-trade-model.php">
				<label class="b-form-label">Торговая марка</label>
				<div class="i-relative"><div class="b-form-field__shutter"></div></div>
				<select name="trade" required>
					<option value="1">Philips</option>
					<option value="2">Braun</option>
					<option value="3">Moulinex</option>
					<option value="4">Vitek</option>
					<option value="5">Bosh</option>
					<option value="6">Bork</option>
					<option value="7">Samsung</option>
				</select>
			</div>
			
			<div class="b-form-field b-kitchen__add-form__model i-invisible" >
				<label class="b-form-label">Модель</label>
				<div class="i-relative"><div class="b-form-field__shutter"></div></div>
				<select name="model" required>
					<option value="1">HT1029209</option>
					<option value="2">HT2029209</option>
					<option value="3">HT39209</option>
					<option value="4">HT10409</option>
					<option value="5">HT159209</option>
					<option value="6">H69209</option>
					<option value="7">HT1059</option>
				</select>
			</div>
			
			<div class="b-form-field">
				<label class="b-form-label">Ваша оценка</label>
				<div class="b-input-range" data-min="0" data-max="5" data-scale="true">
					<input type="hidden" name="mark" value="3" class="b-input-range__input">
				</div>
			</div>
			
			<div class="b-form-field">
				<label class="b-form-label">Комментарий</label>
				<textarea name="comment" rows="10" cols="20" class="b-textarea"></textarea>
			</div>
			
			<div class="b-form-submit">
				<button type="submit" class="b-button">Добавить технику</button>
			</div>
		</form>
	</div>
</div>



















