<div id="content"> <link rel="stylesheet" type="text/css" href="/css/recipe-om.css?2"></link> 
<script src="/js/basic.js" type="text/javascript"></script>
 
<script>
	$(document).ready(function() {
		
		(function() {
			var $currentMonthRecipe = $(".b-recipe-om__current");
			$(".b-recipe-om__month").each(function() {
				
				if($(this).offset().top >= $currentMonthRecipe.offset().top + $currentMonthRecipe.outerHeight()) {
					$(this).addClass("b-recipe-om__month__type-wide");
				}
				
			});
			
			$(".b-recipe-of-month img").load(function() {
				var $currentMonthRecipe = $(".b-recipe-om__current");
				$(".b-recipe-om__month").each(function() {
					$(this).removeClass("b-recipe-om__month__type-wide");
					
					if($(this).offset().top >= $currentMonthRecipe.offset().top + $currentMonthRecipe.outerHeight()) {
						$(this).addClass("b-recipe-om__month__type-wide");
					}
					
				});
			});
			
		})();

		(function() {
			var $elem = $(".b-recipe-om__current-month");
			if($elem.is("div")) {
				var width = $elem.width();
				var $name = $elem.find(".b-recipe-om__current-month__name");
				var classPrefix = "i-recipe-om__current-month__name__";
				
				changeSize(1);
			}
			
			function changeSize(i) {
				$name.removeClass(classPrefix + (i-1));
				$name.addClass(classPrefix + i);
				
				
				if($name.width() >= (width-2)) {
					changeSize(++i);
				}
			}
		})();
		
		(function() {
			if(!window.ga) return;
			
			$(".b-recipe-om__next__button .b-add-button").click(function() {
				ga('send', 'event', 'Рецепт месяца', 'Добавить рецепт');
			});
			
			$(".b-recipe-om__current__image__link").click(function() {
				var href = $(this).closest(".b-recipe-om__current").find(".b-recipe-om__current__heading a").attr("href");
				var id = /([0-9]{3,})/.exec(href)[0];
				var title = $(this).closest(".b-recipe-om__current").find(".b-recipe-om__current__heading").text();
				ga('send', 'event', 'Рецепт месяца', 'Последний рецепт &ndash; Нажатие на картинку', id + ' – ' + title);
			});
			
			$(".b-recipe-om__current__heading a").click(function() {
				var href = $(this).closest(".b-recipe-om__current").find(".b-recipe-om__current__heading a").attr("href");
				var id = /([0-9]{3,})/.exec(href)[0];
				var title = $(this).closest(".b-recipe-om__current").find(".b-recipe-om__current__heading").text();
				ga('send', 'event', 'Рецепт месяца', 'Последний рецепт – Нажатие на название', id + ' – ' + title);
			});
			
			$(".b-recipe-om__month__image a").click(function() {
				var href = $(this).closest(".b-recipe-om__month").find(".b-recipe-om__month__text__heading a").attr("href");
				var id = /([0-9]{3,})/.exec(href)[0];
				var title = $(this).closest(".b-recipe-om__month").find(".b-recipe-om__month__text__heading").text();
				ga('send', 'event', 'Рецепт месяца', 'Ранние рецепты – Нажатие на картинку', id + ' – ' + title);
			});
			
			$(".b-recipe-om__month__text__heading a").click(function() {
				var href = $(this).closest(".b-recipe-om__month").find(".b-recipe-om__month__text__heading a").attr("href");
				var id = /([0-9]{3,})/.exec(href)[0];
				var title = $(this).closest(".b-recipe-om__month").find(".b-recipe-om__month__text__heading").text();
				ga('send', 'event', 'Рецепт месяца', 'Ранние рецепты – Нажатие на название', id + ' – ' + title);
			});
			
			$(".b-recipe-om__month a.b-userpic").click(function() {
				var name = $(this).closest(".b-recipe-om__month__comment__author").find(".b-recipe-om__month__comment__author__name").text();
				ga('send', 'event', 'Рецепт месяца', 'Ранние рецепты – Нажатие на фото эксперта', name);
			});
			
			$(".b-comment__userpic a.b-userpic").click(function() {
				var name = $(this).closest(".b-comment__userpic").find(".b-comment__author").text();
				ga('send', 'event', 'Рецепт месяца', 'Последний рецепт – Нажатие на фото эксперта', name);
			});
			
		})();



	});
</script>
 
  <h1>Рецепт месяца</h1>
 
  <div class="b-sponsor-banner"> 
    <div class="i-relative b-sponsor-banner__ill"> <img src="/images/recipe-of-month/steamer.jpg" width="169" height="136"  /> </div>
   <img src="/images/recipe-of-month/philips-banner.gif" width="960" height="100" alt="Специальный приз от PHILIPS"  /> </div>
 
  <div class="b-recipe-of-month"> 
    <div class="b-recipe-om__current"> 
		<div class="b-recipe-om__current__image"> <a href="http://www.foodclub.ru/detail/60105/" class="b-recipe-om__current__image__link" target="_blank" > <span class="b-recipe-om__current__image__plate"></span> <span class="b-recipe-om__current__image__wrapper" style="display: block; overflow: hidden; height: 406px; width: 270px; margin: -30px 0 0 62px;"><img src="http://www.foodclub.ru/upload/iblock/0a9/we4.jpg" width="270" alt="Пирог из полу-песочного теста с творогом и абрикосами" class="b-recipe-om__current__image__pic" style="margin-top: 0;"  /></span> </a> 
        <div class="b-recipe-om__current-month"> <span class="b-recipe-om__current-month__name">Май</span> </div>
       </div>
     
      <h2 class="b-recipe-om__current__heading"><a href="http://www.foodclub.ru/detail/60105/" target="_blank" >Пирог из полу-песочного теста с творогом и абрикосами</a></h2>
     
      <div class="b-recipe-author b-recipe-om__current__author b-recipe-author__size-M">От: Дарья Мальцева</div>
     
      <div class="b-recipe-om__current__like-buttons b-social-buttons"> 
        <div class="b-social-buttons__item b-vk-like"> 
          <div id="vk_like1"></div>
         
<script type="text/javascript">
			VK.Widgets.Like("vk_like1", {
				type: "mini",
				height: 20,
				pageTitle: "Пирог из полу-песочного теста с творогом и абрикосами",
				pageUrl: "http://www.foodclub.ru/detail/60105/",
				pageImage: "http://www.foodclub.ru/upload/iblock/0a9/we4.jpg"
			});
		  </script>
 </div>
       
        <div class="b-social-buttons__item b-fb-like"> <fb:like font="arial" show_faces="false" width="50" layout="button_count" send="false" href="http://www.foodclub.ru/detail/60105/"></fb:like> </div>
       
        <div class="i-clearfix"></div>
       </div>
     
      <div class="b-comment b-comment__type-big-userpic b-recipe-om__current__comment"> 
        <div class="b-comment__userpic"> <noindex><a class="b-userpic" href="http://www.vkusitsvet.ru/" target="_blank" rel="nofollow" > <img width="100" height="100" class="b-userpic__image" alt="Ольга Сюткина" src="http://www.foodclub.ru/images/userpic/sutkina.jpg"  /> </a></noindex> 
          <div class="b-comment__author">Ольга Сюткина
            <br />
           <i>Эксперт</i></div>
         </div>
       
        <div class="b-comment__content"> 
          <div class="b-comment__text"> 
            <p style="text-indent: -9px;">&laquo;Если оценивать летние пироги, то уж конечно это пироги с сезонными ягодами и фруктами. Абрикосы отлично ведут себя в любой выпечке. Сохраняют структуру, после тепловой обработки становятся слаще, ароматнее и не теряют важного своего свойства - яркости. Сочетание белоснежной сливочной начинки и солнечных половинок абрикосов идеально подходит для пирога, поданного на стол в разгаре лета.&raquo;</p>
           </div>
         </div>
       
        <div class="i-clearfix"></div>
       </div>
     </div>
   
    <div class="b-recipe-om__right-column"> 
      <div class="b-recipe-om__description"> 
        <h2>Как мы выбираем рецепт месяца</h2>
       
        <p>Каждый месяц приглашенный эксперт будет выбирать лучший рецепт, из опубликованных на нашем сайте и в сообществе <a href="http://foodclub-ru.livejournal.com" target="_blank" >http://foodclub-ru.livejournal.com</a> в прошедшем месяце, а мы будем вручать небольшой, но приятный приз автору выбранного рецепта.</p>
       
        <p>Для участия в конкурсе надо опубликовать свой пошаговый рецепт на сайте, а там уж эксперты выберут достойнейший и мы вручим приз победителю.</p>
       </div>
     
      <div class="b-recipe-om__next"> 
        <div class="b-recipe-om__next__month"> 
          <div class="b-recipe-om__next__month__pic"></div>
         
          <div class="b-recipe-om__next__month__name">Июнь</div>
         </div>
       
        <div class="b-recipe-om__next__button"> <a class="i-frame-bg b-add-button" href="http://www.foodclub.ru/recipe/add/" > <span class="i-frame-bg_left"> <span class="i-frame-bg_right"> <span class="i-frame-bg_bg"><span class="i-frame-bg_content">Добавить рецепт</span></span> </span> </span> </a> </div>
       
        <div class="i-clearfix"></div>
       </div>
     </div>
   
    <div class="b-recipe-om__feed">
	
	  <div class="b-recipe-om__month">
        <div class="b-recipe-om__month__heading"><span class="b-recipe-om__month__heading__name">Рецепт апреля</span></div>
       
        <div class="b-recipe-om__month__image"><a href="http://www.foodclub.ru/detail/59059/" target="_blank" ><img src="http://www.foodclub.ru/upload/iblock/813/%D0%B3%D0%BB%D0%B2%D0%BD%D0%BE%D0%B5%201.jpg" width="200" alt="Шоколадные пряники с трещинками" /></a></div>
       
        <div class="b-recipe-om__month__text"> 
          <div class="b-recipe-om__month__recipe"> 
            <h3 class="b-recipe-om__month__text__heading"><a href="http://www.foodclub.ru/detail/59059/" target="_blank" >Шоколадные пряники с трещинками</a></h3>
           
            <div class="b-recipe-author b-recipe-author__size-M">От: Анна Рябова</div>
           </div>
         
          <div class="b-recipe-om__month__comment"> 
            <div class="b-recipe-om__month__comment__author"> <noindex><a class="b-userpic b-recipe-om__month__comment__author__userpic" href="http://www.selectcake.com/" target="_blank" rel="nofollow" ><img width="50" height="50" class="b-userpic__image" alt="Влад Пискунов" src="http://www.foodclub.ru/images/userpic/agronik.jpg"  /></a></noindex> <span class="b-recipe-om__month__comment__author__name">Катерина Агроник</span> </div>
           
            <div class="b-recipe-om__month__comment__text">В рецепте Анна Рябова использует много качественного горького шоколада, а также подробно и доступно объясняет каждую ступень приготовления, уделяя внимание некоторым кулинарным хитростям.</div>
           </div>
         </div>
       
        <div class="i-clearfix"></div>
       </div>
	
	  <div class="b-recipe-om__month"> 
        <div class="b-recipe-om__month__heading"><span class="b-recipe-om__month__heading__name">Рецепт марта</span></div>
       
        <div class="b-recipe-om__month__image"><a href="http://www.foodclub.ru/detail/56778/" target="_blank" ><img src="/upload/iblock/186/IMG_6635.jpg" width="200" alt="Сельдь домашнего, пряного посола со смородиновым соусом"  /></a></div>
       
        <div class="b-recipe-om__month__text"> 
          <div class="b-recipe-om__month__recipe"> 
            <h3 class="b-recipe-om__month__text__heading"><a href="http://www.foodclub.ru/detail/56778/" target="_blank" >Сельдь домашнего, пряного посола со смородиновым соусом</a></h3>
           
            <div class="b-recipe-author b-recipe-author__size-M">От: Анна Басто</div>
           </div>
         
          <div class="b-recipe-om__month__comment"> 
            <div class="b-recipe-om__month__comment__author"> <noindex><a class="b-userpic b-recipe-om__month__comment__author__userpic" href="http://www.foodclub.ru/profile/70/" target="_blank" rel="nofollow" ><img width="50" height="50" class="b-userpic__image" alt="Влад Пискунов" src="http://www.foodclub.ru/upload/main/517/avatar.jpg"  /></a></noindex> <span class="b-recipe-om__month__comment__author__name">Влад Пискунов</span> </div>
           
            <div class="b-recipe-om__month__comment__text">Очень необычное и смелое сочетание, но уверен - это вкусно. Как только у меня в саду созреет чёрная смородина, я обязательно попробую повторить это блюдо.</div>
           </div>
         </div>
       
        <div class="i-clearfix"></div>
       </div>
		
	  <div class="b-recipe-om__month"> 
        <div class="b-recipe-om__month__heading"><span class="b-recipe-om__month__heading__name">Рецепт февраля</span></div>
       
        <div class="b-recipe-om__month__image"><a href="http://www.foodclub.ru/detail/55931/" target="_blank" ><img src="/upload/iblock/7b6/IMG_6388.jpg" width="200" alt="Запечённый утиный паштет"  /></a></div>
       
        <div class="b-recipe-om__month__text"> 
          <div class="b-recipe-om__month__recipe"> 
            <h3 class="b-recipe-om__month__text__heading"><a href="http://www.foodclub.ru/detail/55931/" target="_blank" >Запечённый утиный паштет</a></h3>
           
            <div class="b-recipe-author b-recipe-author__size-M">От: Анна Басто</div>
           </div>
         
          <div class="b-recipe-om__month__comment"> 
            <div class="b-recipe-om__month__comment__author"> <noindex><a class="b-userpic b-recipe-om__month__comment__author__userpic" href="https://www.facebook.com/konstantin.ivlev.56" target="_blank" rel="nofollow" ><img width="50" height="50" class="b-userpic__image" alt="Константин Ивлев" src="http://www.foodclub.ru/images/userpic/ivlev.jpg"  /></a></noindex> <span class="b-recipe-om__month__comment__author__name">Константин Ивлев</span> </div>
           
            <div class="b-recipe-om__month__comment__text">Я выбираю утиный паштет, потому что, во-первых, вижу по текстуре, что блюдо правильно приготовлено и правильно подано, а во-вторых, оно подходит по сезонности.</div>
           </div>
         </div>
       
        <div class="i-clearfix"></div>
       </div>
	
		 	 
      <div class="b-recipe-om__month"> 
        <div class="b-recipe-om__month__heading"><span class="b-recipe-om__month__heading__name">Рецепт января</span></div>
       
        <div class="b-recipe-om__month__image"><a href="http://www.foodclub.ru/detail/54444/" target="_blank" ><img src="/upload/iblock/1da/fc1.jpg" width="200" alt="Аранчини по-сицилийски"  /></a></div>
       
        <div class="b-recipe-om__month__text"> 
          <div class="b-recipe-om__month__recipe"> 
            <h3 class="b-recipe-om__month__text__heading"><a href="http://www.foodclub.ru/detail/54444/" target="_blank" >Аранчини по-сицилийски</a></h3>
           
            <div class="b-recipe-author b-recipe-author__size-M">От: caterina_sicilia</div>
           </div>
         
          <div class="b-recipe-om__month__comment"> 
            <div class="b-recipe-om__month__comment__author"> <noindex><a class="b-userpic b-recipe-om__month__comment__author__userpic" href="https://www.facebook.com/profile.php?id=100001491332754" target="_blank" rel="nofollow" ><img width="50" height="50" class="b-userpic__image" alt="Мария Савельева" src="http://www.foodclub.ru/upload/main/fb3/avatar.jpg"  /></a></noindex> <span class="b-recipe-om__month__comment__author__name">Мария Савельева</span> </div>
           
            <div class="b-recipe-om__month__comment__text">Это блюдо очень популярно в Италии, и, хотя оно непростое, с таким подробным рецептом его вполне возможно приготовить дома, пригласить побольше гостей и устроить итальянскую вечеринку.</div>
           </div>
         </div>
       
        <div class="i-clearfix"></div>
       </div>
	
		 	 
      <div class="b-recipe-om__month"> 
        <div class="b-recipe-om__month__heading"><span class="b-recipe-om__month__heading__name">Рецепт декабря</span></div>
       
        <div class="b-recipe-om__month__image"><a href="http://www.foodclub.ru/detail/53585/" target="_blank" ><img src="/upload/iblock/9b5/DSC_0471_.jpg" width="200" alt="Куриный мусс с соусом с добавлением Мадеры"  /></a></div>
       
        <div class="b-recipe-om__month__text"> 
          <div class="b-recipe-om__month__recipe"> 
            <h3 class="b-recipe-om__month__text__heading"><a href="http://www.foodclub.ru/detail/53585/" target="_blank" >Куриный мусс с соусом с добавлением Мадеры</a></h3>
           
            <div class="b-recipe-author b-recipe-author__size-M">От: Кати Левченко</div>
           </div>
         
          <div class="b-recipe-om__month__comment"> 
            <div class="b-recipe-om__month__comment__author"> <noindex><a class="b-userpic b-recipe-om__month__comment__author__userpic" href="https://www.facebook.com/imkushnir" target="_blank" rel="nofollow" ><img width="50" height="50" class="b-userpic__image" alt="Ирина Кушнир" src="http://www.foodclub.ru/images/userpic/kushnir.jpg"  /></a></noindex> <span class="b-recipe-om__month__comment__author__name">Ирина Кушнир</span> </div>
           
            <div class="b-recipe-om__month__comment__text">Технологичен, выдержан в духе &quot;диетического&quot; времени, универсален с точки зрения подачи в горячем и холодном виде, хорошо и внятно оформлен. Однако, соус я предложила бы взбить блендером, чтобы придать ему более однородную структуру.</div>
           </div>
         </div>
       
        <div class="i-clearfix"></div>
       </div>
     	 	 
      <div class="b-recipe-om__month"> 
        <div class="b-recipe-om__month__heading"><span class="b-recipe-om__month__heading__name">Рецепт ноября</span></div>
       
        <div class="b-recipe-om__month__image"><a href="http://www.foodclub.ru/detail/52136/" target="_blank" ><img src="/upload/iblock/62d/IMG_5890-2 Edit.jpg" width="200" alt="Лепешки с брынзой"  /></a></div>
       
        <div class="b-recipe-om__month__text"> 
          <div class="b-recipe-om__month__recipe"> 
            <h3 class="b-recipe-om__month__text__heading"><a href="http://www.foodclub.ru/detail/52136/" target="_blank" >Лепешки с брынзой</a></h3>
           
            <div class="b-recipe-author b-recipe-author__size-M">От: Юлии Ахановой</div>
           </div>
         
          <div class="b-recipe-om__month__comment"> 
            <div class="b-recipe-om__month__comment__author"> <noindex><a class="b-userpic b-recipe-om__month__comment__author__userpic" href="https://www.facebook.com/roman.lerner" target="_blank" rel="nofollow" ><img width="50" height="50" class="b-userpic__image" alt="Роман Лернер" src="http://www.foodclub.ru/images/userpic/lerner.jpg"  /></a></noindex> <span class="b-recipe-om__month__comment__author__name">Роман Лернер</span> </div>
           
            <div class="b-recipe-om__month__comment__text">Такое блюдо есть в арсенале многих хозяек. Например, моя мама печет точно такие. Сыр обжигающе горячий, хрусткая корочка. В пару к сладкому чаю с лимоном - шедевр.</div>
           </div>
         </div>
       
        <div class="i-clearfix"></div>
       </div>
     		 	 
      <div class="b-recipe-om__month"> 
        <div class="b-recipe-om__month__heading"><span class="b-recipe-om__month__heading__name">Рецепт октября</span></div>
       
        <div class="b-recipe-om__month__image"><a href="http://www.foodclub.ru/detail/51043/" target="_blank" ><img src="/upload/iblock/19e/DSC_0854.JPG" width="200" alt="Рыбный пирог"  /></a></div>
       
        <div class="b-recipe-om__month__text"> 
          <div class="b-recipe-om__month__recipe"> 
            <h3 class="b-recipe-om__month__text__heading"><a href="http://www.foodclub.ru/detail/51043/" target="_blank" >Рыбный пирог</a></h3>
           
            <div class="b-recipe-author b-recipe-author__size-M">От: Кати Левченко</div>
           </div>
         
          <div class="b-recipe-om__month__comment"> 
            <div class="b-recipe-om__month__comment__author"> <noindex><a class="b-userpic b-recipe-om__month__comment__author__userpic" href="http://www.syrnikov.ru/" target="_blank" rel="nofollow" ><img width="50" height="50" class="b-userpic__image" alt="Максим Сырников" src="http://www.foodclub.ru/images/userpic/syrnikov.jpg"  /></a></noindex> <span class="b-recipe-om__month__comment__author__name">Максим Сырников</span> </div>
           
            <div class="b-recipe-om__month__comment__text">Из всех претендентов больше всего понравился рыбный пирог.</div>
           </div>
         </div>
       
        <div class="i-clearfix"></div>
       </div>
     		 	 
      <div class="b-recipe-om__month"> 
        <div class="b-recipe-om__month__heading"><span class="b-recipe-om__month__heading__name">Рецепт сентября</span></div>
       
        <div class="b-recipe-om__month__image"><a href="http://www.foodclub.ru/detail/49648/" target="_blank" ><img src="http://www.foodclub.ru/upload/iblock/4cf/DSC_1833-Edit.JPG" width="200" alt="Запеченный лук"  /></a></div>
       
        <div class="b-recipe-om__month__text"> 
          <div class="b-recipe-om__month__recipe"> 
            <h3 class="b-recipe-om__month__text__heading"><a href="http://www.foodclub.ru/detail/49648/" target="_blank" >Запеченный лук</a></h3>
           
            <div class="b-recipe-author b-recipe-author__size-M">От: Виталия Байко</div>
           </div>
         
          <div class="b-recipe-om__month__comment"> 
            <div class="b-recipe-om__month__comment__author"> <noindex><a class="b-userpic b-recipe-om__month__comment__author__userpic" href="http://merienn.livejournal.com/" target="_blank" rel="nofollow" ><img width="50" height="50" class="b-userpic__image" alt="Тинатин Мжаванадзе" src="http://www.foodclub.ru/images/userpic/tinatin.jpg"  /></a></noindex> <span class="b-recipe-om__month__comment__author__name">Тинатин Мжаванадзе</span> </div>
           
            <div class="b-recipe-om__month__comment__text">Хороша сама идея, можно варьировать начинки для лука. Это аппетитное, здоровое и свежее блюдо, которое может быть как гарниром, так и самостоятельной горячей закуской..</div>
           </div>
         </div>
       
        <div class="i-clearfix"></div>
       </div>
     	 	 
      <div class="b-recipe-om__month"> 
        <div class="b-recipe-om__month__heading"><span class="b-recipe-om__month__heading__name">Рецепт августа</span></div>
       
        <div class="b-recipe-om__month__image"><a href="http://www.foodclub.ru/detail/47492/" target="_blank" ><img src="http://www.foodclub.ru/upload/iblock/259/37a995cd-e019-42a5-9f66-36670a960249_w600_h0_p.jpg" width="200" alt="Кефаль маринованная"  /></a></div>
       
        <div class="b-recipe-om__month__text"> 
          <div class="b-recipe-om__month__recipe"> 
            <h3 class="b-recipe-om__month__text__heading"><a href="http://www.foodclub.ru/detail/47492/" target="_blank" >Кефаль маринованная</a></h3>
           
            <div class="b-recipe-author b-recipe-author__size-M">От: annu_an</div>
           </div>
         
          <div class="b-recipe-om__month__comment"> 
            <div class="b-recipe-om__month__comment__author"> <noindex><a class="b-userpic b-recipe-om__month__comment__author__userpic" href="http://zveruska.livejournal.com/" target="_blank" rel="nofollow" ><img width="50" height="50" class="b-userpic__image" alt="Аня и Ася Борисовы" src="http://www.foodclub.ru/images/userpic/borisovy.jpg"  /></a></noindex> <span class="b-recipe-om__month__comment__author__name">Аня и Ася Борисовы</span> </div>
           
            <div class="b-recipe-om__month__comment__text">Этот рецепт универсален для любой жирной рыбы – и результат неизменно хорош. Подойдет и скумбрия, и сельдь, и сардины.</div>
           </div>
         </div>
       
        <div class="i-clearfix"></div>
       </div>
     	 
      <div class="b-recipe-om__month"> 
        <div class="b-recipe-om__month__heading"><span class="b-recipe-om__month__heading__name">Рецепт июля</span></div>
       
        <div class="b-recipe-om__month__image"><a href="http://www.foodclub.ru/detail/45805/" target="_blank" ><img src="http://www.foodclub.ru/upload/iblock/f5b/_7.jpg" width="200" alt="Филе-миньон с чесночным соусом"  /></a></div>
       
        <div class="b-recipe-om__month__text"> 
          <div class="b-recipe-om__month__recipe"> 
            <h3 class="b-recipe-om__month__text__heading"><a href="http://www.foodclub.ru/detail/45805/" target="_blank" >Филе-миньон с чесночным соусом</a></h3>
           
            <div class="b-recipe-author b-recipe-author__size-M">От: Пачкуале Пестрини</div>
           </div>
         
          <div class="b-recipe-om__month__comment"> 
            <div class="b-recipe-om__month__comment__author"> <noindex><a class="b-userpic b-recipe-om__month__comment__author__userpic" href="http://tasty-mama.livejournal.com/" target="_blank" rel="nofollow" ><img width="50" height="50" class="b-userpic__image" alt="Ольга Шенкерман" src="http://www.foodclub.ru/images/userpic/olga.jpg"  /></a></noindex> <span class="b-recipe-om__month__comment__author__name">Ольга Шенкерман</span> </div>
           
            <div class="b-recipe-om__month__comment__text">Мало кто у нас в стране умеет обращаться с мясом. У автора рецепта очень недурно получается филе-миньон средней прожарки, и я просто не могла пройти мимо и не поощрить автора!</div>
           </div>
         </div>
       
        <div class="i-clearfix"></div>
       </div>
     
      <div class="b-recipe-om__month"> 
        <div class="b-recipe-om__month__heading"><span class="b-recipe-om__month__heading__name">Рецепт июня</span></div>
       
        <div class="b-recipe-om__month__image"><a href="http://www.foodclub.ru/detail/45091/" target="_blank" ><img src="http://www.foodclub.ru/upload/iblock/dd9/Xc0zJzojAFU.jpg" width="200" alt="Эстонская выпечка с корицей"  /></a></div>
       
        <div class="b-recipe-om__month__text"> 
          <div class="b-recipe-om__month__recipe"> 
            <h3 class="b-recipe-om__month__text__heading"><a href="http://www.foodclub.ru/detail/45091/" target="_blank" >Эстонская выпечка с корицей</a></h3>
           
            <div class="b-recipe-author b-recipe-author__size-M">От: Елизавета Безгина</div>
           </div>
         
          <div class="b-recipe-om__month__comment"> 
            <div class="b-recipe-om__month__comment__author"> <noindex><a class="b-userpic b-recipe-om__month__comment__author__userpic" href="https://www.facebook.com/Ivan.Evlentyev" target="_blank" rel="nofollow" ><img width="50" height="50" class="b-userpic__image" alt="Иван Евлентьев" src="http://www.foodclub.ru/upload/main/fff/babay.jpg"  /></a></noindex> <span class="b-recipe-om__month__comment__author__name">Иван Евлентьев</span> </div>
           
            <div class="b-recipe-om__month__comment__text">Я свой выбор сделал: Эстонская выпечка с корицей от Елизаветы Безгиной. Запах горячей выпечки смешанный с ароматом корицы способен создать атмосферу дома, даже если находишься за сотни километров от него. Несложно в приготовлении и вполне доступно по затратам. В моем понимании, это и есть самая настоящая домашняя еда.</div>
           </div>
         </div>
       
        <div class="i-clearfix"></div>
       </div>
     
      <div class="b-recipe-om__month"> 
        <div class="b-recipe-om__month__heading"><span class="b-recipe-om__month__heading__name">Рецепт мая</span></div>
       
        <div class="b-recipe-om__month__image"><a href="http://www.foodclub.ru/detail/43786/" target="_blank" ><img src="http://www.foodclub.ru/upload/iblock/f90/DSC027221.jpg" width="200" alt="Гречка с финиками и апельсинами"  /></a></div>
       
        <div class="b-recipe-om__month__text"> 
          <div class="b-recipe-om__month__recipe"> 
            <h3 class="b-recipe-om__month__text__heading"><a href="http://www.foodclub.ru/detail/43786/" target="_blank" >Гречка с финиками и апельсинами</a></h3>
           
            <div class="b-recipe-author b-recipe-author__size-M">От: lavender ribbon</div>
           </div>
         
          <div class="b-recipe-om__month__comment"> 
            <div class="b-recipe-om__month__comment__author"> <noindex><a class="b-userpic b-recipe-om__month__comment__author__userpic" href="https://www.facebook.com/marianna.orlinkova" target="_blank" rel="nofollow" > <img width="50" height="50" class="b-userpic__image" alt="Мария Савельева" src="http://www.foodclub.ru/upload/main/fff/Orlinkova.jpg"  /> </a></noindex> <span class="b-recipe-om__month__comment__author__name">Марианна Орлинкова</span> </div>
           
            <div class="b-comment__userpic"> 
              <div class="b-recipe-om__month__comment__text">Мой выбор &mdash; гречка с апельсинами и финиками. Полезная и вкусная гречка, увы, очень быстро надоедает в том виде, в котором мы привыкли ее есть (либо просто так, либо с сахаром и молоком, либо с грибами или бефстрогановом). Гречка по этому рецепту — отличный и не приевшийся способ накормить ею кого угодно, даже капризного ребенка.</div>
             </div>
           </div>
         </div>
       
        <div class="i-clearfix"></div>
       </div>
     </div>
   
    <div class="b-recipe-om__month"> 
      <div class="b-recipe-om__month__heading"><span class="b-recipe-om__month__heading__name">Рецепт апреля</span></div>
     
      <div class="b-recipe-om__month__image"><a href="http://www.foodclub.ru/detail/43268/" target="_blank" ><img src="http://foodclub.ru/upload/iblock/fcc/shep1.jpg" width="200" alt="Пастуший пирог"  /></a></div>
     
      <div class="b-recipe-om__month__text"> 
        <div class="b-recipe-om__month__recipe"> 
          <h3 class="b-recipe-om__month__text__heading"><a href="http://www.foodclub.ru/detail/43268/" target="_blank" >Пастуший пирог</a></h3>
         
          <div class="b-recipe-author b-recipe-author__size-M">От: kate_grigoryeva</div>
         </div>
       
        <div class="b-recipe-om__month__comment"> 
          <div class="b-recipe-om__month__comment__author"> <noindex><a class="b-userpic b-recipe-om__month__comment__author__userpic" href="http://www.foodclub.ru/profile/2/" target="_blank" rel="nofollow" > <img width="50" height="50" class="b-userpic__image" alt="Мария Савельева" src="http://www.foodclub.ru/upload/main/fb3/avatar.jpg"  /> </a></noindex> <span class="b-recipe-om__month__comment__author__name">Мария Савельева</span> </div>
         
          <div class="b-comment__userpic"> 
            <div class="b-recipe-om__month__comment__text">В рецепте месяца все должно быть прекрасно: и содержание, и описание, и фотографии. Поэтому титул «Рецепт месяца» получил традиционный пастуший пирог — лаконичный и красивый, аккуратно исполненный, хорошо описанный и сфотографированный.</div>
           </div>
         </div>
       </div>
     
      <div class="i-clearfix"></div>
     </div>
   </div>
 
  <div class="b-recipe-om__month"> 
    <div class="b-recipe-om__month__heading"><span class="b-recipe-om__month__heading__name">Рецепт марта</span></div>
   
    <div class="b-recipe-om__month__image"><a href="http://www.foodclub.ru/detail/41932/" target="_blank" ><img src="http://foodclub.ru/images/userpic/potica.jpg" width="200" alt="Povitica. Potica. (Повитица, потица)"  /></a></div>
   
    <div class="b-recipe-om__month__text"> 
      <div class="b-recipe-om__month__recipe"> 
        <h3 class="b-recipe-om__month__text__heading"><a href="http://www.foodclub.ru/detail/41932/" target="_blank" >Povitica. Potica. (Повитица, потица)</a></h3>
       
        <div class="b-recipe-author b-recipe-author__size-M">От: shuntik</div>
       </div>
     
      <div class="b-recipe-om__month__comment"> 
        <div class="b-recipe-om__month__comment__author"> <noindex><a class="b-userpic b-recipe-om__month__comment__author__userpic" href="http://laperla-foto.livejournal.com/" target="_blank" rel="nofollow" ><img width="50" height="50" class="b-userpic__image" alt="Юлия Атаева" src="http://www.foodclub.ru/images/userpic/ataeva.jpg"  /></a></noindex> <span class="b-recipe-om__month__comment__author__name">Юлия Атаева</span> </div>
       
        <div class="b-recipe-om__month__comment__text">Простая домашняя выпечка, хорошо описана технология приготовления, вопросов у меня не возникло, а испечь захотелось сразу. Разрез очень понравился, ну а маковая начинка вряд ли кого-то оставит равнодушным.</div>
       </div>
     </div>
   
    <div class="i-clearfix"></div>
   </div>
 
  <div class="b-recipe-om__month"> 
    <div class="b-recipe-om__month__heading"><span class="b-recipe-om__month__heading__name">Рецепт февраля</span></div>
   
    <div class="b-recipe-om__month__image"><a href="http://www.foodclub.ru/detail/39966/" target="_blank" ><img src="http://foodclub.ru/upload/iblock/7c0/IMG_9090.jpg" width="200" alt="Картофель по-герцогски"  /></a></div>
   
    <div class="b-recipe-om__month__text"> 
      <div class="b-recipe-om__month__recipe"> 
        <h3 class="b-recipe-om__month__text__heading"><a href="http://www.foodclub.ru/detail/39966/" target="_blank" >Картофель по-герцогски</a></h3>
       
        <div class="b-recipe-author b-recipe-author__size-M">От: Карташёвой Татьяны</div>
       </div>
     
      <div class="b-recipe-om__month__comment"> 
        <div class="b-recipe-om__month__comment__author"> <noindex><a class="b-userpic b-recipe-om__month__comment__author__userpic" href="http://www.facebook.com/katyapal" target="_blank" rel="nofollow" ><img width="50" height="50" class="b-userpic__image" alt="Катя Пал" src="http://www.foodclub.ru/images/userpic/katyapal.jpg"  /></a></noindex> <span class="b-recipe-om__month__comment__author__name">Катя Пал</span> </div>
       
        <div class="b-recipe-om__month__comment__text">Немного выдумки – и на вашей тарелке уже не традиционное «картофельное пюре» а симпатичные, с золотистой корочкой, картофельные сердечки (а что за февраль без сердечек?)</div>
       </div>
     </div>
   
    <div class="i-clearfix"></div>
   </div>
 
  <div class="b-recipe-om__month"> 
    <div class="b-recipe-om__month__heading"><span class="b-recipe-om__month__heading__name">Рецепт января</span></div>
   
    <div class="b-recipe-om__month__image"><a href="http://www.foodclub.ru/detail/38886/" target="_blank" ><img src="http://foodclub.ru/upload/iblock/0f1/IMG_8610.jpg" width="200" alt="Румынские шоколадные конфеты"  /></a></div>
   
    <div class="b-recipe-om__month__text"> 
      <div class="b-recipe-om__month__recipe"> 
        <h3 class="b-recipe-om__month__text__heading"><a href="http://www.foodclub.ru/detail/38886/" target="_blank" >Румынские шоколадные конфеты</a></h3>
       
        <div class="b-recipe-author b-recipe-author__size-M">От: Карташёвой Татьяны</div>
       </div>
     
      <div class="b-recipe-om__month__comment"> 
        <div class="b-recipe-om__month__comment__author"> <noindex><a class="b-userpic b-recipe-om__month__comment__author__userpic" href="http://elladkin.livejournal.com/" target="_blank" rel="nofollow" ><img width="50" height="50" class="b-userpic__image" alt="Элла Мартино" src="http://www.foodclub.ru/images/userpic/martino.jpg"  /></a></noindex> <span class="b-recipe-om__month__comment__author__name">Элла Мартино</span> </div>
       
        <div class="b-recipe-om__month__comment__text">Хочу отметить и поблагодарить всех участвующих за серьезный подход. Я же остановила свой выбор на рецепте шоколадных конфет. Этот рецепт мне показался самым оригинальным.</div>
       </div>
     </div>
   
    <div class="i-clearfix"></div>
   </div>
 
  <div class="b-recipe-om__month"> 
    <div class="b-recipe-om__month__heading"><span class="b-recipe-om__month__heading__name">Рецепт декабря</span></div>
   
    <div class="b-recipe-om__month__image"><a href="http://www.foodclub.ru/detail/38576/" target="_blank" ><img src="http://foodclub.ru/upload/iblock/619/1.jpg" width="200" alt="Сыр из йогурта"  /></a></div>
   
    <div class="b-recipe-om__month__text"> 
      <div class="b-recipe-om__month__recipe"> 
        <h3 class="b-recipe-om__month__text__heading"><a href="http://www.foodclub.ru/detail/38576/" target="_blank" >Сыр из йогурта</a></h3>
       
        <div class="b-recipe-author b-recipe-author__size-M">От: Spurga</div>
       </div>
     
      <div class="b-recipe-om__month__comment"> 
        <div class="b-recipe-om__month__comment__author"> <noindex><a class="b-userpic b-recipe-om__month__comment__author__userpic" href="http://chadeyka.livejournal.com/" target="_blank" rel="nofollow" ><img width="50" height="50" class="b-userpic__image" alt="Ирина Чадеева" src="http://www.foodclub.ru/images/userpic/chadeyka.jpg"  /></a></noindex> <span class="b-recipe-om__month__comment__author__name">Ирина Чадеева</span> </div>
       
        <div class="b-recipe-om__month__comment__text">Сыр из йогурта - прекрасная идея! Полезно, вкусно, а ингредиенты найдутся на любой кухне. Мне также очень нравится, что этот рецепт открывает огромное поле для экспериментов и сулит множество кулинарных открытий - ведь домашние &quot;фокусы&quot; с молочными продуктами очень интересная и занимательная штука. </div>
       </div>
     </div>
   
    <div class="i-clearfix"></div>
   </div>
 
  <div class="b-recipe-om__month"> 
    <div class="b-recipe-om__month__heading"><span class="b-recipe-om__month__heading__name">Рецепт ноября</span></div>
   
    <div class="b-recipe-om__month__image"><a href="http://www.foodclub.ru/detail/37352/" target="_blank" ><img src="http://foodclub.ru/upload/iblock/984/img_0047_600.jpg" width="200" alt="Круассаны"  /></a></div>
   
    <div class="b-recipe-om__month__text"> 
      <div class="b-recipe-om__month__recipe"> 
        <h3 class="b-recipe-om__month__text__heading"><a href="http://www.foodclub.ru/detail/37352/" target="_blank" >Круассаны</a></h3>
       
        <div class="b-recipe-author b-recipe-author__size-M">От: its_al_dente</div>
       </div>
     
      <div class="b-recipe-om__month__comment"> 
        <div class="b-recipe-om__month__comment__author"> <noindex><a class="b-userpic b-recipe-om__month__comment__author__userpic" href="http://abugaisky.livejournal.com/" target="_blank" rel="nofollow" ><img width="50" height="50" class="b-userpic__image" alt="Андрей Бугайский" src="http://www.foodclub.ru/images/userpic/bug.jpg"  /></a></noindex> <span class="b-recipe-om__month__comment__author__name">Андрей Бугайский</span> </div>
       
        <div class="b-recipe-om__month__comment__text">Хорошо бы этот рецепт внимательно прочитал кто-нибудь из практических пекарей и запустил в производство — у нас до обидного мало небольших пекарен со свежей вкусной выпечкой, а как бы украсили жизнь традиционные Boulangerie. 
          <p></p>
         </div>
       </div>
     </div>
   
    <div class="i-clearfix"></div>
   </div>
 
  <div class="b-recipe-om__month"> 
    <div class="b-recipe-om__month__heading"><span class="b-recipe-om__month__heading__name">Рецепт октября</span></div>
   
    <div class="b-recipe-om__month__image"><a href="http://www.foodclub.ru/detail/36916/" target="_blank" ><img src="http://foodclub.ru/upload/iblock/e6c/0_a8007_27e70e69_L.jpg" width="200" alt="Шавля"  /></a></div>
   
    <div class="b-recipe-om__month__text"> 
      <div class="b-recipe-om__month__recipe"> 
        <h3 class="b-recipe-om__month__text__heading"><a href="http://www.foodclub.ru/detail/36916/" target="_blank" >Шавля</a></h3>
       
        <div class="b-recipe-author b-recipe-author__size-M">От: anastasiasomo</div>
       </div>
     
      <div class="b-recipe-om__month__comment"> 
        <div class="b-recipe-om__month__comment__author"> <noindex><a class="b-userpic b-recipe-om__month__comment__author__userpic" href="http://aspiri.livejournal.com/" target="_blank" rel="nofollow" ><img width="50" height="50" class="b-userpic__image" alt="Алёна Спирина" src="http://www.foodclub.ru/images/userpic/spirina.jpg"  /></a></noindex> <span class="b-recipe-om__month__comment__author__name">Алёна Спирина</span> </div>
       
        <div class="b-recipe-om__month__comment__text"> Хорошее домашнее блюдо на каждый день, особенно холодный и ненастный. Такую еду я обычно называю «уютной». 
          <p>К шавле хорошо подать тонко нарезанную зелёную редьку, посыпанную солью, или салат из свежих помидоров. Маринованные кольца сладкого репчатого лука тоже весьма украсят блюдо.</p>
         </div>
       </div>
     </div>
   
    <div class="i-clearfix"></div>
   </div>
 
  <div class="b-recipe-om__month"> 
    <div class="b-recipe-om__month__heading"><span class="b-recipe-om__month__heading__name">Рецепт сентября</span></div>
   
    <div class="b-recipe-om__month__image"><a href="http://www.foodclub.ru/detail/36039/" target="_blank" ><img src="http://www.foodclub.ru/upload/iblock/579/8005605297_7c54729920_o.jpg" width="200" alt="Деревенская печеная тыква с грибами и корнем петрушки"  /></a></div>
   
    <div class="b-recipe-om__month__text"> 
      <div class="b-recipe-om__month__recipe"> 
        <h3 class="b-recipe-om__month__text__heading"><a href="http://www.foodclub.ru/detail/36039/" target="_blank" >Деревенская печеная тыква с грибами и корнем петрушки</a></h3>
       
        <div class="b-recipe-author b-recipe-author__size-M">От: furmanfood</div>
       </div>
     
      <div class="b-recipe-om__month__comment"> 
        <div class="b-recipe-om__month__comment__author"> <noindex><a class="b-userpic b-recipe-om__month__comment__author__userpic" href="http://www.vkusitsvet.ru/" target="_blank" rel="nofollow" ><img width="50" height="50" class="b-userpic__image" alt="Ольга Сюткина" src="http://www.foodclub.ru/images/userpic/sutkina.jpg"  /></a></noindex> <span class="b-recipe-om__month__comment__author__name">Ольга Сюткина</span> </div>
       
        <div class="b-recipe-om__month__comment__text"> Неожиданное сочетание ингредиентов, простота приготовления, сезонность — это, пожалуй, самое главное. Когда как не по осени наслаждаться ее же дарами, а уж тыквой в первую очередь, она поистине королева! </div>
       </div>
     </div>
   
    <div class="i-clearfix"></div>
   </div>
 
  <div class="b-recipe-om__month"> 
    <div class="b-recipe-om__month__heading"><span class="b-recipe-om__month__heading__name">Рецепт августа</span></div>
   
    <div class="b-recipe-om__month__image"><a href="http://www.foodclub.ru/detail/35660/" target="_blank" ><img src="http://www.foodclub.ru/upload/iblock/6b1/P1120627.JPG" width="200" alt="Крем-суп из сельдерея, базилика, молодого горошка с креветками"  /></a></div>
   
    <div class="b-recipe-om__month__text"> 
      <div class="b-recipe-om__month__recipe"> 
        <h3 class="b-recipe-om__month__text__heading"><a href="http://www.foodclub.ru/detail/35660/" target="_blank" >Крем-суп из сельдерея, базилика, молодого горошка с креветками</a></h3>
       
        <div class="b-recipe-author b-recipe-author__size-M">От: rinav71</div>
       </div>
     
      <div class="b-recipe-om__month__comment"> 
        <div class="b-recipe-om__month__comment__author"> <noindex><a class="b-userpic b-recipe-om__month__comment__author__userpic" href="http://www.facebook.com/people/%D0%91%D0%BE%D1%80%D0%B8%D1%81-%D0%91%D1%83%D1%80%D0%B4%D0%B0/100001693129698" target="_blank" rel="nofollow" ><img width="50" height="50" class="b-userpic__image" alt="Борис Бурда" src="http://www.foodclub.ru/images/userpic/burda.jpg"  /></a></noindex> <span class="b-recipe-om__month__comment__author__name">Борис Бурда</span> </div>
       
        <div class="b-recipe-om__month__comment__text"> Пока зеленушки на базаре полно и она дешевая, почаще надо есть этот суп. А что же будем делать зимой? Да платить подороже и тоже есть — больно уж все гармонично&hellip;. </div>
       </div>
     </div>
   
    <div class="i-clearfix"></div>
   </div>
 
  <div class="b-recipe-om__month"> 
    <div class="b-recipe-om__month__heading"><span class="b-recipe-om__month__heading__name">Рецепт июля</span></div>
   
    <div class="b-recipe-om__month__image"><a href="http://www.foodclub.ru/detail/34132/" target="_blank" ><img src="http://www.foodclub.ru/upload/iblock/2f2/20120708-DSC_0132.jpg" width="200" alt="Запеканка из цветной капусты"  /></a></div>
   
    <div class="b-recipe-om__month__text"> 
      <div class="b-recipe-om__month__recipe"> 
        <h3 class="b-recipe-om__month__text__heading"><a href="http://www.foodclub.ru/detail/34132/" target="_blank" >Запеканка из цветной капусты</a></h3>
       
        <div class="b-recipe-author b-recipe-author__size-M">От: irina_ctc</div>
       </div>
     
      <div class="b-recipe-om__month__comment"> 
        <div class="b-recipe-om__month__comment__author"> <noindex><a class="b-userpic b-recipe-om__month__comment__author__userpic" href="http://www.foodclub.ru/profile/5676/" target="_blank" rel="nofollow" ><img width="50" height="50" class="b-userpic__image" alt="Елена Айзикович" src="http://www.foodclub.ru/upload/main/a5c/avatar.jpg"  /></a></noindex> <span class="b-recipe-om__month__comment__author__name">Дмитрий Журавлев</span> </div>
       
        <div class="b-recipe-om__month__comment__text"> Интересное блюдо, в котором представлен один продукт, но приготовленный разными способами. Цветная капуста отлично для этого подходит — в зависимости от способа приготовления, у неё достаточно сильно изменяется не только текстура, но и вкус. И это очень заманчиво — получить такое разнообразие в одном блюде. </div>
       </div>
     </div>
   
    <div class="i-clearfix"></div>
   </div>
 
  <div class="b-recipe-om__month"> 
    <div class="b-recipe-om__month__heading"><span class="b-recipe-om__month__heading__name">Рецепт июня</span></div>
   
    <div class="b-recipe-om__month__image"><a href="http://www.foodclub.ru/detail/33757/" target="_blank" ><img src="http://www.foodclub.ru/upload/iblock/7e7/0_5bd80_1f3e7fa8_L.jpg" width="200" alt="Куриные шашлычки"  /></a></div>
   
    <div class="b-recipe-om__month__text"> 
      <div class="b-recipe-om__month__recipe"> 
        <h3 class="b-recipe-om__month__text__heading"><a href="http://www.foodclub.ru/detail/33757/" target="_blank" >Куриные шашлычки</a></h3>
       
        <div class="b-recipe-author b-recipe-author__size-M">От: Mariha-kitchen</div>
       </div>
     
      <div class="b-recipe-om__month__comment"> 
        <div class="b-recipe-om__month__comment__author"> <noindex><a class="b-userpic b-recipe-om__month__comment__author__userpic" href="http://elaizik.livejournal.com/" target="_blank" rel="nofollow" ><img width="50" height="50" class="b-userpic__image" alt="Елена Айзикович" src="http://www.foodclub.ru/images/userpic/8951089.jpg"  /></a></noindex> <span class="b-recipe-om__month__comment__author__name">Елена Айзикович 
            <br />
           (elaizik)</span> </div>
       
        <div class="b-recipe-om__month__comment__text"> «Доступные продукты, несложно и быстро, гарантированно вкусно, если не передержать шашлычки в духовке. 
          <br />
         
          <br />
         Единственное, что я позволю себе заметить: соус велюте, рецепт которого приводится, будет вкуснее, если готовить его по классической технологии – обжарить муку с маслом, а потом добавить вино и бульон». </div>
       </div>
     </div>
   
    <div class="i-clearfix"></div>
   </div>
 
  <div class="b-recipe-om__month"> 
    <div class="b-recipe-om__month__heading"><span class="b-recipe-om__month__heading__name">Рецепт мая</span></div>
   
    <div class="b-recipe-om__month__image"><a href="http://www.foodclub.ru/detail/32545/" target="_blank" ><img src="http://www.foodclub.ru/upload/iblock/a42/288-9.jpg" width="200" alt="Овсяные палочки с сыром"  /></a></div>
   
    <div class="b-recipe-om__month__text"> 
      <div class="b-recipe-om__month__recipe"> 
        <h3 class="b-recipe-om__month__text__heading"><a href="http://www.foodclub.ru/detail/32545/" target="_blank" >Овсяные палочки с сыром</a></h3>
       
        <div class="b-recipe-author b-recipe-author__size-M">От: Marisha_solo</div>
       </div>
     
      <div class="b-recipe-om__month__comment"> 
        <div class="b-recipe-om__month__comment__author"> <a class="b-userpic b-recipe-om__month__comment__author__userpic" href="http://www.foodclub.ru/profile/70/" target="_blank" ><img width="50" height="50" class="b-userpic__image" alt="Влад Пискунов" src="http://www.foodclub.ru/upload/main/517/avatar.jpg"  /></a> <span class="b-recipe-om__month__comment__author__name">Влад Пискунов</span> </div>
       
        <div class="b-recipe-om__month__comment__text"> «Несложное, но вполне достойное блюдо. По соотношению «удовольствие / затраченное время» рецепт, пожалуй, лучший из всех представленных. Блюдо очень вариативно, можно экспериментировать как с сыром, так и с разными посыпками — дает поле для творчества». </div>
       </div>
     </div>
   
    <div class="i-clearfix"></div>
   </div>
 </div>
 
<div class="i-clearfix"></div>
