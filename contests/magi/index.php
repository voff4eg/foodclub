<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("keywords", "кулинарный конкурс, лучший рецепт суши, рецепт суши, фото рецепт суши, конкурс");
$APPLICATION->SetPageProperty("description", "Кулинарный конкурс на лучший фото-рецепт суши и роллов от foodclub.ru и sushimag.ru");
$APPLICATION->SetTitle("Кулинарный конкурс фото-рецептов суши «Суши маги». Foodclub.ru");
?>
	<script type="text/javascript">
		$(document).ready(function() {
			var magiStart = new Date("May 20, 2011 15:00:00"), magiPeriod = new Date(), days, hours, minutes, daysText, hoursText, minutesText;
			magiPeriod=magiStart.getTime()-magiPeriod.getTime();
			
			days=Math.floor(magiPeriod/(24*60*60*1000));
			magiPeriod=magiPeriod-days*24*60*60*1000;
			if (/(10|11|12|13|14|15|16|17|18|19)$/.test(days)) {daysText = ' дней, ';}
			else if (/.*1$/.test(days)) {daysText = ' день, ';}
			else if (/[2-4]$/.test(days)) {daysText = ' дня, ';}
			else {daysText = ' дней, ';}
			
			hours=Math.floor(magiPeriod/(60*60*1000));
			magiPeriod=magiPeriod-hours*60*60*1000;
			if (/(10|11|12|13|14|15|16|17|18|19)$/.test(hours)) {hoursText = ' часов, ';}
			else if (/.*1$/.test(hours)) {hoursText = ' час, ';}
			else if (/[2-4]$/.test(hours)) {hoursText = ' часа, ';}
			else {hoursText = ' часов, ';}
			
			minutes=Math.floor(magiPeriod/(60*1000));
			if (/(10|11|12|13|14|15|16|17|18|19)$/.test(minutes)) {minutesText = ' минут';}
			else if (/.*1$/.test(minutes)) {minutesText = ' минута';}
			else if (/[2-4]$/.test(minutes)) {minutesText = ' минуты';}
			else {minutesText = ' минут';}
			
			$("#contest_magi em.period").text(days+daysText+hours+hoursText+minutes+minutesText);
		});
	</script>

<div id="content">
		<div id="contest_magi">
			<h1>Суши маги</h1>
			<div class="border1">
				<div class="border2">
					<div class="content">
						<p class="intro">Совместно с интернет-магазином <a href="http://sushimag.ru" target="_blank">sushimag.ru</a> мы проводим конкурс на лучший пошаговый рецепт суши.</p>
						<p>Чтобы Ваш рецепт мог принять участие в конкурсе, он должен</p>
						<ul>
							<li>соответствовать тематике конкурса;</li>
							<li>иметь авторские пошаговые фотографии хорошего качества размером 600х400 px.</li>
						</ul>
						<p style="margin:0 0 21px 0;">Рецепты на конкурс принимаются с 15 апреля по 16 мая 2011 года.</p>

						<h2>Сроки</h2>
						<p>Конкурс продлится с 15 апреля по 20 мая 2011 года. После 15:00 в пятницу будут объявлены победители.</p>
						<!--<p>До окончания голосования осталось: <em class="period"></em>.</p>-->
						<div class="bookmarks"></div>
						<?$APPLICATION->IncludeComponent("custom:contest_members.list", ".default", array(
	"IBLOCK_TYPE" => "-",
	"IBLOCK_ID" => "5",
	"CONTEST" => "12960",
	"NEWS_COUNT" => "20",
	"SORT_BY1" => "ACTIVE_FROM",
	"SORT_ORDER1" => "ASC",
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
	"CACHE_TYPE" => "N",
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
						<div class="rules">
							<h2>Правила</h2>
							<div class="stages">
								<div class="stage stage1">
									<div class="num"><div>1</div></div>
									<div class="image"></div>
									<p><a href="/recipe/add/">Добавьте</a> рецепт суши или роллов с пошаговыми фотографиями на сайт.</p>
								</div>
								<!--<div class="stage stage2">
									<div class="num"><div>2</div></div>
									<div class="image"></div>
									<p>Нажать кнопку «Участвовать в конкурсе».</p>
								</div>-->
								<div class="stage stage3">
									<div class="num"><div>2</div></div>
									<div class="image"></div>
									<p>За 7 дней до конца конкурса, начинается неделя голосования.<br>Голосуйте и следите за голосованием.</p>
								</div>
							</div>
						</div>
					</div>
					<div class="banner">
						<h2>Приз</h2>
						<div class="image"></div>
						<div class="prises">
							<div class="item">
								<div class="num"><div>1</div></div>
								Продукты для суши<br>на сумму <strong>5 000 рублей</strong>
							</div>
							<div class="item">
								<div class="num"><div>2</div></div>
								Продукты для суши<br>на сумму <strong>3 500 рублей</strong>
							</div>
							<div class="item last">
								<div class="num"><div>3</div></div>
								Продукты для суши<br>на сумму <strong>1 500 рублей</strong>
							</div>
						</div>
						<div class="sponsor1">
							<a href="http://sushimag.ru/" target="_blank" class="logo" title="SushiMag — для тех, кто сам готовит суши"></a>
							<div class="slogan">Для тех, кто сам готовит суши</div>
						</div>
						<div class="sponsor2">
							<a href="http://www.ozon.ru/context/detail/id/5544220/?partner=foodclub" target="_blank" class="logo" title="Книга на Ozon.ru"></a>
							<div class="slogan">3 победителям<br>книга <a href="http://vlad-piskunov.livejournal.com/" target="_blank" title="Блог Влада Пискунова на livejournal.com">Влада Пискунова</a><br>«ВСЕ о том, КАК вкусно ЕСТЬ»</div>
						</div>
					</div>
					<div class="clear"></div>
				</div>
			</div>
			<a href="/recipe/add/" class="add_recipe" title="Добавить рецепт">Добавить рецепт</a>
			<script type="text/javascript" src="//yandex.st/share/share.js" charset="utf-8"></script><div class="yashare-auto-init" data-yashareL10n="ru" data-yashareType="link" data-yashareQuickServices="yaru,vkontakte,facebook,twitter,odnoklassniki,moimir"></div>
			<div class="clear"></div>
			<div class="partners">
				<h2>Спасибо за поддержку:</h2>
				<a href="http://sushimag.ru/" target="_blank" class="sushimag" title="SushiMag — для тех, кто сам готовит суши"></a>
				<a href="http://community.livejournal.com/sushi_ru/" target="_blank" class="sushi_ru" title="Суши и Сашими"></a>
				<a href="http://clubs.ya.ru/gurman/" target="_blank" class="ya_gurman" title="Я.Гурман"></a>
			</div>
		</div>
	</div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>