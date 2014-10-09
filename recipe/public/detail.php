<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
//define("MINIMIZE", true);
$APPLICATION->SetAdditionalCSS("/css/recipe.css");
$APPLICATION->AddHeadScript("/js/recipe.js");
?>
<div id="content">
<?$APPLICATION->IncludeComponent(
	"custom:recipe.menu",
	"",
	Array(
		"DISPLAY_DATE" => "Y",
		"DISPLAY_NAME" => "Y",
		"DISPLAY_PICTURE" => "Y",
		"DISPLAY_PREVIEW_TEXT" => "Y",
		"USE_SHARE" => "N",
		"AJAX_MODE" => "N",
		"IBLOCK_TYPE" => "recipes",
		"IBLOCK_ID" => "5",
		"ELEMENT_ID" => intval($_REQUEST["r"]),
		"ELEMENT_CODE" => $_REQUEST['c'],
		"CHECK_DATES" => "Y",
		"FIELD_CODE" => array("ID", "CODE", "XML_ID", "NAME", "TAGS", "SORT", "PREVIEW_TEXT", "PREVIEW_PICTURE", "DETAIL_TEXT", "DETAIL_PICTURE", "DATE_ACTIVE_FROM", "ACTIVE_FROM", "DATE_ACTIVE_TO", "ACTIVE_TO", "SHOW_COUNTER", "SHOW_COUNTER_START", "IBLOCK_TYPE_ID", "IBLOCK_ID", "IBLOCK_CODE", "IBLOCK_NAME", "IBLOCK_EXTERNAL_ID", "DATE_CREATE", "CREATED_BY", "CREATED_USER_NAME", "TIMESTAMP_X", "MODIFIED_BY", "USER_NAME"),
		"PROPERTY_CODE" => array("keywords", "comment_count", "cooking_time", "kcals", "portion", "block_search", "title", "description", "lib", "raiting", "edit_deadline"),
		"IBLOCK_URL" => "",
		"SET_TITLE" => "Y",
		"SET_BROWSER_TITLE" => "Y",
		"BROWSER_TITLE" => "-",
		"SET_META_KEYWORDS" => "Y",
		"META_KEYWORDS" => "-",
		"SET_META_DESCRIPTION" => "Y",
		"META_DESCRIPTION" => "-",
		"SET_STATUS_404" => "N",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "Y",
		"ADD_SECTIONS_CHAIN" => "Y",
		"ADD_ELEMENT_CHAIN" => "N",
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"USE_PERMISSIONS" => "N",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"CACHE_GROUPS" => "Y",
		"PAGER_TEMPLATE" => ".default",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"PAGER_TITLE" => "Страница",
		"PAGER_SHOW_ALL" => "Y",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N"
	)
);?>
<?$arReturn = $APPLICATION->IncludeComponent(
	"custom:recipe.detail",
	"",
	Array(
		"DISPLAY_DATE" => "Y",
		"DISPLAY_NAME" => "Y",
		"DISPLAY_PICTURE" => "Y",
		"DISPLAY_PREVIEW_TEXT" => "Y",
		"USE_SHARE" => "N",
		"AJAX_MODE" => "N",
		"IBLOCK_TYPE" => "recipes",
		"IBLOCK_ID" => "5",
		"ELEMENT_ID" => intval($_REQUEST["r"]),
		"ELEMENT_CODE" => $_REQUEST['c'],
		"CHECK_DATES" => "Y",
		"FIELD_CODE" => array("ID", "CODE", "XML_ID", "NAME", "TAGS", "SORT", "PREVIEW_TEXT", "PREVIEW_PICTURE", "DETAIL_TEXT", "DETAIL_PICTURE", "DATE_ACTIVE_FROM", "ACTIVE_FROM", "DATE_ACTIVE_TO", "ACTIVE_TO", "SHOW_COUNTER", "SHOW_COUNTER_START", "IBLOCK_TYPE_ID", "IBLOCK_ID", "IBLOCK_CODE", "IBLOCK_NAME", "IBLOCK_EXTERNAL_ID", "DATE_CREATE", "CREATED_BY", "CREATED_USER_NAME", "TIMESTAMP_X", "MODIFIED_BY", "USER_NAME"),
		"PROPERTY_CODE" => array("keywords", "comment_count", "cooking_time", "kcals", "portion", "block_search", "title", "description", "lib", "raiting", "edit_deadline"),
		"IBLOCK_URL" => "",
		"SET_TITLE" => "Y",
		"SET_BROWSER_TITLE" => "Y",
		"BROWSER_TITLE" => "-",
		"SET_META_KEYWORDS" => "Y",
		"META_KEYWORDS" => "-",
		"SET_META_DESCRIPTION" => "Y",
		"META_DESCRIPTION" => "-",
		"SET_STATUS_404" => "N",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "Y",
		"ADD_SECTIONS_CHAIN" => "Y",
		"ADD_ELEMENT_CHAIN" => "N",
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"USE_PERMISSIONS" => "N",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"CACHE_GROUPS" => "Y",
		"PAGER_TEMPLATE" => ".default",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"PAGER_TITLE" => "Страница",
		"PAGER_SHOW_ALL" => "Y",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N"
	)
);?>
<?if (CModule::IncludeModule("advertising")){
    $strrBanner = CAdvBanner::Show("right_banner");
    $strBookBanner = CAdvBanner::Show("book_banner");
	$strSecond_banner = CAdvBanner::Show("second_right_banner");
}?>
<?$APPLICATION->IncludeComponent("custom:recipe.comments", "custom", Array(
	"DISPLAY_DATE" => "Y",	// Выводить дату элемента
	"DISPLAY_NAME" => "Y",	// Выводить название элемента
	"DISPLAY_PICTURE" => "Y",	// Выводить изображение для анонса
	"DISPLAY_PREVIEW_TEXT" => "Y",	// Выводить текст анонса
	"AJAX_MODE" => "N",	// Включить режим AJAX
	"IBLOCK_TYPE" => "news",	// Тип информационного блока (используется только для проверки)
	"IBLOCK_ID" => "",	// Код информационного блока
	"RECIPE_ID" => $arReturn["ID"],	// Код Рецепта
	"CREATED_BY" => "",
	"NEWS_COUNT" => "20",	// Количество новостей на странице
	"SORT_BY1" => "ACTIVE_FROM",	// Поле для первой сортировки новостей
	"SORT_ORDER1" => "DESC",	// Направление для первой сортировки новостей
	"SORT_BY2" => "SORT",	// Поле для второй сортировки новостей
	"SORT_ORDER2" => "ASC",	// Направление для второй сортировки новостей
	"FILTER_NAME" => "",	// Фильтр
	"FIELD_CODE" => "",	// Поля
	"PROPERTY_CODE" => "",	// Свойства
	"CHECK_DATES" => "Y",	// Показывать только активные на данный момент элементы
	"DETAIL_URL" => "",	// URL страницы детального просмотра (по умолчанию - из настроек инфоблока)
	"PREVIEW_TRUNCATE_LEN" => "",	// Максимальная длина анонса для вывода (только для типа текст)
	"ACTIVE_DATE_FORMAT" => "d.m.Y",	// Формат показа даты
	"SET_TITLE" => "Y",	// Устанавливать заголовок страницы
	"SET_STATUS_404" => "N",	// Устанавливать статус 404, если не найдены элемент или раздел
	"INCLUDE_IBLOCK_INTO_CHAIN" => "Y",	// Включать инфоблок в цепочку навигации
	"ADD_SECTIONS_CHAIN" => "Y",	// Включать раздел в цепочку навигации
	"HIDE_LINK_WHEN_NO_DETAIL" => "N",	// Скрывать ссылку, если нет детального описания
	"PARENT_SECTION" => "",	// ID раздела
	"PARENT_SECTION_CODE" => "",	// Код раздела
	"CACHE_TYPE" => "N",	// Тип кеширования
	"CACHE_TIME" => "36000000",	// Время кеширования (сек.)
	"CACHE_NOTES" => "",
	"CACHE_FILTER" => "N",	// Кэшировать при установленном фильтре
	"CACHE_GROUPS" => "Y",	// Учитывать права доступа
	"DISPLAY_TOP_PAGER" => "N",	// Выводить над списком
	"DISPLAY_BOTTOM_PAGER" => "Y",	// Выводить под списком
	"PAGER_TITLE" => "Новости",	// Название категорий
	"PAGER_SHOW_ALWAYS" => "Y",	// Выводить всегда
	"PAGER_TEMPLATE" => "",	// Название шаблона
	"PAGER_DESC_NUMBERING" => "N",	// Использовать обратную навигацию
	"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",	// Время кеширования страниц для обратной навигации
	"PAGER_SHOW_ALL" => "Y",	// Показывать ссылку "Все"
	"AJAX_OPTION_SHADOW" => "Y",	// Включить затенение
	"AJAX_OPTION_JUMP" => "N",	// Включить прокрутку к началу компонента
	"AJAX_OPTION_STYLE" => "Y",	// Включить подгрузку стилей
	"AJAX_OPTION_HISTORY" => "N",	// Включить эмуляцию навигации браузера
	"AJAX_OPTION_ADDITIONAL" => "",	// Дополнительный идентификатор
	),
	false
);?>
</div>
	<div id="banner_space">
		<?
		$rsRandomDoUKnow = CIBlockElement::GetList(array("rand"=>"asc"),array("IBLOCK_CODE"=>"do_u_know"),false,false,array("ID","PREVIEW_TEXT"));
		if($arRandomDoUKnow = $rsRandomDoUKnow->Fetch()){?>
		<div id="do-you-know-that" class="b-facts">
			<div class="b-facts__heading">Знаете ли вы что:</div>
			<div class="b-facts__content">
				<div class="b-facts__item" data-id="<?=$arRandomDoUKnow["ID"]?>">
					<?=$arRandomDoUKnow["PREVIEW_TEXT"]?>
				</div>
			</div>
			<div class="b-facts__more">
				<a href="#" class="b-facts__more__link">Еще</a>
			</div>
		</div>
		<?}?>
		<?if(strlen($strrBanner) > 0){?>
			<div class="banner">
				<h5>Реклама</h5>
				<?=$strrBanner?>
			</div>
		<?}?>
		<?$APPLICATION->IncludeComponent("custom:store.banner.vertical", "", Array(),false);?>
		<?if(strlen($strBookBanner) > 0){?>
			<div class="auxiliary_block book">
				<?=$strBookBanner?>
				<div class="clear"></div>
			</div>
		<?}?>
		<?=$arReturn["like"]?>
		<?if(strlen($strSecond_banner) > 0){?>
			<div class="banner">
				<h5>Реклама</h5>
				<?=$strSecond_banner?>
			</div>
		<?}?>
		<?global $arrFilter;
		if(strlen($arReturn["TAGS"])){
			$arTags = explode(",", $arReturn["TAGS"]);
			foreach ($arTags as $key => $tag) {
				$arTagFilter[] = array("TAGS" => trim($tag));
			}
			if(!empty($arTags) && count($arTags) > 1){
				$arrFilter = array(
					"?TAGS" => "(".implode(" ||", $arTags).")"

				);
			}else{
				$arrFilter = array(
					"TAGS" => $arReturn["TAGS"]	
				);
			}
		}
		?>
<?$APPLICATION->IncludeComponent(
	"custom:foodshot.list", 
	"one_foodshot.recipe", 
	array(
		"IBLOCK_TYPE" => "foodshot",
		"IBLOCK_ID" => "25",
		"NEWS_COUNT" => "1",
		"SORT_BY1" => "rand",
		"SORT_ORDER1" => "DESC",
		"SORT_BY2" => "",
		"SORT_ORDER2" => "",
		"FILTER_NAME" => "arrFilter",
		"FIELD_CODE" => array(
			0 => "TAGS",
			1 => "CREATED_BY",
			2 => "",
		),
		"PROPERTY_CODE" => array(
			0 => "comments_count",
			1 => "likes_count",
			2 => "",
		),
		"CHECK_DATES" => "Y",
		"DETAIL_URL" => "",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"CACHE_TYPE" => "N",
		"CACHE_TIME" => "360000",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "Y",
		"PREVIEW_TRUNCATE_LEN" => "",
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"SET_TITLE" => "N",
		"SET_STATUS_404" => "N",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
		"ADD_SECTIONS_CHAIN" => "N",
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"PARENT_SECTION" => "",
		"PARENT_SECTION_CODE" => "",
		"INCLUDE_SUBSECTIONS" => "Y",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "N",
		"PAGER_TITLE" => "Новости",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => "",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"DISPLAY_DATE" => "Y",
		"DISPLAY_NAME" => "Y",
		"DISPLAY_PICTURE" => "Y",
		"DISPLAY_PREVIEW_TEXT" => "Y",
		"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
);?>
	</div>
	<div class="clear"></div>
</div>
<script type="text/html" id="print-recipe">
<!DOCTYPE HTML>
<html>
<head>
<title><%=title%></title>
<link href="/css/print.css?140912446479610" type="text/css"  rel="stylesheet" />
</head>

<body>
	<div id="body">
		<div id="top_decor"><img src="/images/print/top_decor.gif" width="602" height="4" alt=""></div>
			<div id="recipe">
				<% if(!(browser.opera || browser.msie)) { %>
				<div class="b-print-button"><a href="#" class="b-button i-print-button" onclick="window.print(); return false;"><span>Распечатать</span></a></div>
				<% } %>
				<div class="b-print-head">
					<div class="b-print-head__logo b-logo"><img src="/images/print/foodclub_logo.gif" width="89" height="63" alt="Foodclub.ru"></div>
					<div class="b-print-head__slogan b-slogan">Рецепты<br>с пошаговыми<br>фотографиями</div>
					<div class="b-print-head__web b-web">www.foodclub.ru</div>
					<div class="i-clearfix"></div>
				</div>
				<div class="title">
					<h1><%=h1%></h1>
					<div class="recipe_info"><%=recipeInfo%></div>
					<div class="needed">
						<h2>Для приготовления блюда вам понадобится:</h2>
						<table><%=needed%></table>
					</div>
					<div class="image"><%=titleImage%></div>
					<div class="description"><%=description%></div>
					<div class="i-clearfix"></div>
				</div>
				<div class="instructions">
					<% for(var i = 0; i < stages.length; i++) { %>
					<div class="stage"><%=stages[i]%></div>
					<% } %>
				</div>
				<% if(!(browser.opera || browser.msie)) { %>
				<div class="b-print-button"><a href="#" class="b-button i-print-button" onclick="window.print(); return false;"><span>Распечатать</span></a></div>
				<% } %>
			</div>
			<div id="bottom_decor"><img src="/images/print/bottom_decor.gif" width="602" height="4" alt=""></div>
		</div>
	</div>
</body>
</html>
</script>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>