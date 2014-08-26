<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/templates/fclub/sauce_header.php");
$APPLICATION->SetPageProperty("keywords", "кулинарный конкурс, лучший рецепт соуса, рецепт соусов, фото рецепт соусов, конкурс");
$APPLICATION->SetPageProperty("description", "Кулинарный конкурс на лучший фото-рецепт соуса для стейка от foodclub.ru и meatandwine.ru");
$APPLICATION->SetTitle("Кулинарный конкурс соусов для стейка «Соус Прайм». Foodclub.ru");
?>
<div id="content">
	<div>
		<div class="relative">
			<h1>Соус Прайм</h1>
			<a href="" class="partner"></a>
			<div class="description">Совместно с интернет-магазином премиального мяса и вина <strong>Meat & Wine</strong>  мы проводим конкурс на лучший пошаговый рецепт соуса для стейков.</div>
			<div class="prise1"><div>Приглашение<br>на двоих<br>в ресторан<br>«Торро-гриль»</div></div>
			<div class="prise1_desc"><p>Первым победителем, получающим титул «Прайм-соусье» станет автор рецепта, признанного экспертной комиссией самым лучшим.</p><p>Экспертная комиссия: известный кулинарный блоггер Влад Пискунов и Кирилл Мартыненко — управляющий партнер сети стейкхаусов Торро-гриль.</p></div>
			<div class="prise2_desc"><p>Вторым победителем, получающим титул «Чойс-соусье», будет автор рецепта набравшего в сумме больше всех голосов посетителей сайта и пользователей соцсетей Facebook, Odnoklassniki и Vkontakte.</p></div>
			<div class="prise2"><div>Набор<br>премиального мяса<br>и вина<br>от Meat & Wine</div></div>
			<div class="sause_boat"><a href="#vote" class="add_recipe"><span>Голосуйте</span></a></div>
			<a href="http://meatandwine.ru/" target="_blank" class="meat_wine_logo" title=""><span>Призы от</span>Meat&Wine</a>
		</div>
<a name="vote"></a>
		<script type="text/javascript" src="//yandex.st/share/share.js" charset="utf-8"></script>
<div class="yashare-auto-init" data-yashareL10n="ru" data-yashareType="link" data-yashareQuickServices="yaru,vkontakte,facebook,twitter,odnoklassniki,moimir,lj"></div>
		<?$APPLICATION->IncludeComponent("custom:contest_members.list", "souce", array(
	"IBLOCK_TYPE" => "-",
	"CONTEST" => "14715",
	"IBLOCK_ID" => "5",
	"NEWS_COUNT" => "20",
	"SORT_BY1" => "ACTIVE_FROM",
	"SORT_ORDER1" => "DESC",
	"SORT_BY2" => "SORT",
	"SORT_ORDER2" => "ASC",
	"FILTER_NAME" => "",
	"FIELD_CODE" => array(
		0 => "",
		1 => "",
	),
	"PROPERTY_CODE" => array(
		0 => "",
		1 => "",
	),
	"CHECK_DATES" => "Y",
	"DETAIL_URL" => "",
	"AJAX_MODE" => "N",
	"AJAX_OPTION_SHADOW" => "Y",
	"AJAX_OPTION_JUMP" => "N",
	"AJAX_OPTION_STYLE" => "Y",
	"AJAX_OPTION_HISTORY" => "N",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "36000000",
	"CACHE_FILTER" => "N",
	"CACHE_GROUPS" => "Y",
	"PREVIEW_TRUNCATE_LEN" => "",
	"ACTIVE_DATE_FORMAT" => "d.m.Y",
	"SET_TITLE" => "N",
	"SET_STATUS_404" => "N",
	"INCLUDE_IBLOCK_INTO_CHAIN" => "Y",
	"ADD_SECTIONS_CHAIN" => "Y",
	"HIDE_LINK_WHEN_NO_DETAIL" => "N",
	"PARENT_SECTION" => "",
	"PARENT_SECTION_CODE" => "",
	"DISPLAY_TOP_PAGER" => "N",
	"DISPLAY_BOTTOM_PAGER" => "Y",
	"PAGER_TITLE" => "Новости",
	"PAGER_SHOW_ALWAYS" => "Y",
	"PAGER_TEMPLATE" => "",
	"PAGER_DESC_NUMBERING" => "N",
	"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
	"PAGER_SHOW_ALL" => "Y",
	"DISPLAY_DATE" => "Y",
	"DISPLAY_NAME" => "Y",
	"DISPLAY_PICTURE" => "Y",
	"DISPLAY_PREVIEW_TEXT" => "Y",
	"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
);?>
		<div class="partners">
			<h2>Спасибо за поддержку:</h2>
			<a href="http://www.torrogrill.ru/" target="_blank" class="torro_grill" title="Torro Grill&amp;Wine bar">Torro Grill&amp;Wine bar</a>
			<a href="http://meatandwine.ru/" target="_blank" class="meat_wine" title="Meat&amp;Wine">Meat&amp;Wine</a>
			<a href="http://clubs.ya.ru/gurman/" target="_blank" class="ya_gurman" title="Я.Гурман">Я.Гурман</a>
			<a href="http://zhelezoiogon.ru/" target="_blank" class="ferr_fire" title="Железо и Огонь">Железо и Огонь</a>
		</div>
		<div class="conditions">
			<h3>Условия конкурса</h3>
			<h4>Сроки</h4>
			<ol>
				<li>Общий срок конкурса — с 28 июня по 5 августа 2011 года</li>
				<li>Срок для добавления работ — с 28 июня по 31 июля 2011 года</li>
				<li>Срок для голосования — с 1 по 5 августа 2011 года</li>
			</ol>
			
			<h4>Требования к работам</h4>
			<ol>
				<li>К участию в конкурсе принимаются рецепты соусов с пошаговыми фотографиями, опубликованные на сайте foodclub.ru в течение срока для добавления работ</li>
				<li>Фотографии к рецепту должны быть хорошего качества. </li>
				<li>На финальной фотографии готового блюда должен присутствовать соус и мясо, с которым его предполагается употреблять</li>
			</ol>
			
			<h4>Оценка результатов и победители</h4>
			<ol>
				<li>Победителем конкурса с присвоением титула «Прайм-соусье» становится человек, чей рецепт будет выбран экспертной комиссией.</li>
				<li>Победителем конкурса с присвоением титула «Чойс-соусье» становится человек, за рецепт которого проголосует наибольшее количество посетителей сайта и соцсетей.</li>
				<li>Привлечение друзей к голосованию приветствуется, но искусственная накрутка голосов не допускается и будет пресекаться.</li>
				<li>Нарушение правил конкурса может повлечь снятие работы с конкурса.</li>
			</ol>
			
			<h4>Призы</h4>
			<ol>
				<li>Прайм-соусье получит приглашение на двоих в ресторан «Торро-гриль» и сможет не только поужинать там на 5000 рублей, но и заглянуть на кухню, чтобы узнать все тайны приготовления самых вкусных стейков.</li>
				<li>Чойс-соусье получит набор премиального мяса и вина от Интернет-магазина Meat & Wine на сумму 5000 рублей.</li>
				<li>В исключительных случаях, когда отправка приза получателю невозможна, приз может быть заменен сертификатом на покупку кулинарных книг в магазине ozon.ru на сумму, эквивалентную стоимости приза.</li>
			</ol>
		</div>
	</div>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>