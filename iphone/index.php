<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("keywords", "кулинарная программа, Foodclub для iPhone, Foodclub для iPod, Foodclub для iPad");
$APPLICATION->SetPageProperty("description", "Уникальная кулинарная программа Foodclub HD для iPhone, iPod, iPad");
$APPLICATION->SetTitle("Кулинарная программа Foodclub HD для iPhone, iPod, iPad");
?> 
<div id="content"> 		 
<style>
			body {background:url(/images/iphone/bg.gif) repeat 0 -15px;}
			#bg1 {}
			#body div.padding {}
			#top_panel {background:url(/images/iphone/pic.gif) repeat-x 0 -124px;}
			#top_banner {}
			#top_search_list {border-color:#1c1c1c;}
			#head {margin-top:0; padding-top:30px;}
			#logo strong {color:#ffffff;}
			#logo img {display:none;}
			#logo a {
				display:inline;
				float:left;
				margin-right:22px;
				width:143px;
				height:102px;
				background:url(/images/iphone/pic.gif) no-repeat 0 0;}
			#head a, #head a:hover, #bottom a, #bottom a:hover {color:#ffffff;}
			#content {
				background:#dedede;
				padding-bottom:50px;
				-moz-border-radius:7px; /* Firefox */
				-webkit-border-radius:7px; /* Safari, Chrome */
				-khtml-border-radius:7px; /* KHTML */
				border-radius:7px; /* CSS3 */
				padding:20px;}
			#bottom {}
			#bottom_nav {}
			#blogs {display:none;}
			#retina {
				font-size:18pt;
				font-family:"Myriad Pro", Arial, Helvetica, sans-serif;
				color:#cc0000;
				float:right;
				margin:18px 0 0 0;
				text-shadow:0 1px 0 #ffffff;}
			h1 {
				font-family:Tahoma, Arial, Helvetica, sans-serif;
				font-size:35pt;
				color:#333333;
				text-shadow:0 1px 0 #ffffff;
				vertical-align:middle;
				margin-bottom:10px;}
			h1 span {
				font-size:13.5pt;
				display:inline-block;
				margin:0 -23px 0 75px;
				vertical-align:middle;}
			#iphone_main {
				background:#ffffff;
				-moz-border-radius:7px; /* Firefox */
				-webkit-border-radius:7px; /* Safari, Chrome */
				-khtml-border-radius:7px; /* KHTML */
				border-radius:7px; /* CSS3 */
				padding:25px 20px 0 20px;
				text-align:center;
				box-shadow:0 1px 1px #c8c8c8;
				-webkit-box-shadow:0 1px 1px #c8c8c8; /* Safari, Chrome */
				-moz-box-shadow:0 1px 1px #c8c8c8; /* Firefox */
				margin:0 0 50px 0;}
			#iphone_main .relative {position:relative;}
			#iphone_main .price {
				color:#666666;
				font-size:44pt;
				position:absolute;
				top:133px;
				left:675px;
				text-align:left;}
			#iphone_main .rub {
				font-size:18pt;
				display:block;}
			#iphone_main .price p {
				font-size:8pt;
				margin:5px 0 0 0;}
			#iphone_main .app_button {
				display:block;
				width:169px;
				height:57px;
				background:url(/images/iphone/pic.gif) no-repeat -145px 0;
				color:#b9bbbc;
				font-size:14pt;
				text-indent:-350px;
				overflow:hidden;
				position:absolute;
				top:292px;
				left:673px;
				text-align:left;}
			#iphone_main .internet {
				font-size:8pt;
				position:absolute;
				top:500px;
				left:686px;
				text-align:left;
				width:200px;}
			#iphone_main .internet .icon {
				position:absolute;
				width:19px;
				height:13px;
				background:url(/images/iphone/pic.gif) no-repeat -318px 0;
				top:-20px;
				left:-12px;}
			#iphone_link {visibility:hidden;}
			#iphone_link div.pic {
				width:93px;
				height:113px;
				background-position:0 0;}
			
			#iphone_presentations {
				width:580px;
				margin:0 auto 65px auto;}
			#iphone_presentations .item {
				float:left;
				display:inline;
				vertical-align:top;
				margin:0 50px;
				width:190px;
				height:180px;}
			#iphone_presentations .thumb {
				display:inline-block;
				text-align:center;
				text-decoration:none;
				position:relative;
				padding:2px;
				background:#ffffff;
				box-shadow:0 0 4px #cccccc;
				-webkit-box-shadow:0 0 4px #cccccc; /* Safari, Chrome */
				-moz-box-shadow:0 0 4px #cccccc; /* Firefox */
				border:1px solid #e7e7e7;
				-moz-border-radius:3px; /* Firefox */
				-webkit-border-radius:3px; /* Safari, Chrome */
				-khtml-border-radius:3px; /* KHTML */
				border-radius:3px; /* CSS3 */
				margin-bottom:12px;}
			#iphone_presentations .thumb img {
				vertical-align:bottom;
				padding:4px 16px;
				background:#333333;}
			#iphone_presentations .thumb .time {
				display:inline-block;
				height:17px;
				background:#cccccc;
				color:#ffffff;
				padding:2px 10px;
				position:absolute;
				bottom:0;
				right:0;}
			#iphone_presentations .thumb .layer {
				display:inline-block;
				width:152px;
				height:90px;
				background:url(/images/tv_layer.png) no-repeat center -236px;
				position:absolute;
				top:0;
				left:2px;
				//display:none;}
			#iphone_presentations .thumb:hover .layer {background-position:center 6px;}
			#iphone_presentations h2 {
				margin:0 0 8px 0;
				font-size:13.5pt;
				font-family:Tahoma, Arial, Helvetica, sans-serif;}
			#iphone_presentations h2 a {
				font-size:13.5pt;
				font-family:Tahoma, Arial, Helvetica, sans-serif;}
			#iphone_presentations .description {
				margin-right:20px;
				font-size:8pt;
				word-wrap:break-word;}
			
			#iphone_new {
				color:#605d5d;
				text-shadow:0 1px 0 #ffffff;
				font-size:10.5pt;
				line-height:16pt;}
			#iphone_new h2 {
				font-family:Tahoma, Arial, Helvetica, sans-serif;
				color:#605d5d;
				font-size:26pt;
				text-align:center;
				line-height:normal;}
			#iphone_new .stage1 {margin-left:27px;}
			#iphone_new .stage1 img {
				float:left;
				margin-right:36px;}
			#iphone_new .stage1 .text {
				float:left;
				width:340px;
				margin-top:2px;}
			#iphone_new .stage2 {margin:-81px 26px 0 0;}
			#iphone_new .stage2 img {
				float:right;
				margin-left:8px;}
			#iphone_new .stage2 .text {
				float:right;
				width:390px;
				margin-top:24px;}
			#iphone_new .stage3 {margin:-28px 0 0 0;}
			#iphone_new .stage3 img {
				float:left;
				margin-right:30px;}
			#iphone_new .stage3 .text {
				float:left;
				width:415px;
				margin-top:50px;}
			#iphone_new .stage4 {margin:30px 80px 0 0;}
			#iphone_new .stage4 img {
				float:right;
				margin-left:35px;}
			#iphone_new .stage4 .text {
				float:right;
				width:305px;
				margin-top:-12px;}
			#iphone_new .stage5 {margin:22px 0 0 0;}
			#iphone_new .stage5 img {
				float:left;
				margin-right:35px;}
			#iphone_new .stage5 .text {
				float:left;
				width:300px;
				margin-top:10px;}
			#iphone_new .stage6 {margin:15px 320px 0 0;}
			#iphone_new .stage6 img {
				float:right;
				margin-left:25px;}
			#iphone_new .stage6 .text {
				float:right;
				width:270px;
				margin-top:55px;}
			#iphone_new .app_button {
				display:block;
				width:171px;
				height:59px;
				background:url(/images/iphone/pic.gif) no-repeat -145px -58px;
				color:#b9bbbc;
				font-size:14pt;
				text-indent:-350px;
				overflow:hidden;
				text-align:left;
				margin:70px auto 0 auto;}
			#iphone_new .yashare-auto-init {position:relative;}
			#iphone_new .yashare-auto-init .b-share {
				position:absolute;
				top:-35px;
				left:0;}
			#iphone_new .email {
				margin-bottom:30px;
				position:relative;}
			#iphone_new .email a {
				position:absolute;
				top:-35px;
				right:10px;}
		</style>
 
<script type="text/javascript">
$(document).ready(function() {
	$("#iphone_presentations div.item").delegate("a", "click", function() {
		var div = $('<div id="fc_tv_entry"><div class="close"><a id="bxid_681434" href="#" >Закрыть<\/a><iframe title="' + $(this).closest(".item").find("a:eq(1)").text() + '" width="640" height="390" src="' + $(this).attr("href") + '" frameborder="0" allowfullscreen><\/iframe><\/div>');
		$("body").append(div);
		div.togglePopup();
		return false;
	});
	$("body").append('<div id="opaco"><\/div>');
});
//popup
$.fn.alignCenter = function() {
  var marginLeft =  - $(this).width()/2 + 'px';
  var marginTop =  - $(this).height()/2 + 'px';
  return $(this).css({'margin-left':marginLeft, 'margin-top':marginTop});
};

$.fn.togglePopup = function(){
	var $this = $(this);
 if(!$this.is(":visible")) {
   if($.browser.msie) {
	 $('#opaco').height($(document).height()).show()
				.click(function(){$this.togglePopup();});
   }
   else {
	 $('#opaco').height($(document).height()).show().fadeTo('slow', 0.45)
				.click(function(){$this.togglePopup();});
   }

   $this
	 .alignCenter()
	 .show();
	$this.find("div.close").find("a").click(function() {
		$this.togglePopup();
		return false;
	});
 }
 else {
   $('#opaco').hide().removeAttr('style').unbind('click');
   $this.remove();
 }
};
</script>
 		 
  <div id="retina">Retina дисплей</div>
 		 
  <h1>Foodclub HD <span>iPhone</span> <span>iPod</span> <span>iPad</span></h1>
 		 
  <div id="iphone_main"> 			 
    <div class="relative"> 				 
      <div class="price">99 р.</div>
     				<a href="https://itunes.apple.com/ru/app/foodclub-hd/id445878711?mt=8&amp;uo=4&amp;at=1l3vmcn" target="_blank" class="app_button" title="Доступно в App Store">Доступно в App Store</a> 				 
      <div class="internet"> 
        <div class="icon"></div>
       Для программы не требуется наличие подключения к сети Интернет.</div>
     			</div>
   			<img src="/images/iphone/main.jpg" width="460" height="550" alt="Foodclub HD iPhone, iPod, iPad"  /> 		</div>
 		 
  <div id="iphone_presentations"> 			 
    <div class="item"> 				<a class="thumb" target="_blank" href="http://www.youtube.com/v/5M9woh_6kZ8?f=playlists&app=youtube_gdata" > 					<img height="90" width="120" src="http://i.ytimg.com/vi/5M9woh_6kZ8/3.jpg"  /> 					<span class="layer"></span> 				</a> 				 
      <h2><a href="http://www.youtube.com/v/5M9woh_6kZ8?f=playlists&app=youtube_gdata" target="_blank" >Foodclub HD &mdash; iPhone</a></h2>
     				 
      <p class="description">Презентация приложения Foodclub HD для iPhone.</p>
     			</div>
   			 
    <div class="item"> 				<a class="thumb" target="_blank" href="http://www.youtube.com/v/mRnQsG7MOIk?f=playlists&app=youtube_gdata" > 					<img height="90" width="120" src="http://i.ytimg.com/vi/mRnQsG7MOIk/3.jpg"  /> 					<span class="layer"></span> 				</a> 				 
      <h2><a href="http://www.youtube.com/v/mRnQsG7MOIk?f=playlists&app=youtube_gdata" target="_blank" >Foodclub HD — iPad</a></h2>
     				 
      <p class="description">Презентация приложения Foodclub HD для iPad.</p>
     			</div>
   			 
    <div class="clear"></div>
   		</div>
 		 
  <div id="iphone_new"> 			 
    <h2>Что нового?!</h2>
   			 
    <div class="stage1"> 				<img src="/images/iphone/1.jpg" width="302" height="228" alt="Foodclub HD iPhone, iPod, iPad"  /> 				 
      <div class="text"> 					 
        <p>Уникальная кулинарная программа. Более 650 уникальных пошаговых рецептов с качественными пошаговыми фотографиями. Нет ни одной кулинарной книги, где содержалось бы столько рецептов с пошаговыми фотографиями.</p>
       				</div>
     				 
      <div class="clear"></div>
     			</div>
   			 
    <div class="stage2"> 				<img src="/images/iphone/2.jpg" width="315" height="353" alt="Foodclub HD iPhone, iPod, iPad"  /> 				 
      <div class="text"> 					 
        <p>Рецепты разделены по группам и изложены очень простым языком, а блюда по этим рецептам получаются невероятно вкусные. В рецептах используются самые обычные ингредиенты, которые можно купить в обычных магазинах.</p>
       					 
        <p><b>Калорийность и время приготовления</b> 
          <br />
         Для каждого рецепта указано время приготовления, количество персон и калорийность!</p>
       				</div>
     				 
      <div class="clear"></div>
     			</div>
   			 
    <div class="stage3"> 				<img src="/images/iphone/3.jpg" width="406" height="289" alt="Foodclub HD iPhone, iPod, iPad"  /> 				 
      <div class="text"> 					 
        <p><b>Поиск</b> 
          <br />
         Быстрый и удобный поиск, лёгкий доступ к любимым рецептам и истории просмотра рецептов.</p>
       					 
        <p>Умный Помощник, который с лёгкостью подскажет Вам, что можно приготовить из Ваших ингредиентов. Просто укажите Помощнику, что у Вас есть — он сам отберёт подходящие рецепты и сообщит, если чего-то не хватает. А ещё Вы можете отсортировать результаты по ингредиентам.</p>
       				</div>
     				 
      <div class="clear"></div>
     			</div>
   			 
    <div class="stage4"> 				<img src="/images/iphone/4.jpg" width="482" height="268" alt="Foodclub HD iPhone, iPod, iPad"  /> 				 
      <div class="text"> 					 
        <p>У Уникальный ландшафтный режим &laquo;Кулинар&raquo; в iPhone версии. Просто откройте понравившийся рецепт и придайте устройству ландшафтную ориентацию.</p>
       					 
        <p>Все фотографии и шрифт увеличатся для удобства чтения с расстояния, а дисплей не будет гаснуть. Положите устройство на стол и начинайте готовить!</p>
       				</div>
     				 
      <div class="clear"></div>
     			</div>
   			 
    <div class="stage5"> 				<img src="/images/iphone/5.jpg" width="414" height="264" alt="Foodclub HD iPhone, iPod, iPad"  /> 				 
      <div class="text"> 					 
        <p>Новая удобнейшая корзина покупок. Складывайте необходимые ингредиенты в корзину, редактируйте товары в корзине или добавляйте свои собственные. Теперь вы не никогда забудете что-то купить в магазине.</p>
       					 
        <p><b>SMS</b> 
          <br />
         Хотите отправить SMS или электронное письмо с ингредиентами? Без проблем!</p>
       				</div>
     				 
      <div class="clear"></div>
     			</div>
   			 
    <div class="stage6"> 				<img src="/images/iphone/6.jpg" width="160" height="186" alt="Foodclub HD iPhone, iPod, iPad"  /> 				 
      <div class="text"> 					 
        <p>Кулинарный таймер, который работает, даже если программа закрыта.</p>
       				</div>
     				 
      <div class="clear"></div>
     			</div>
   			<a href="http://itunes.apple.com/ru/app/foodclub-hd/id445878711?mt=8" target="_blank" class="app_button" title="Доступно в App Store" >Доступно в App Store</a> 			 
<script type="text/javascript" src="//yandex.st/share/share.js" charset="utf-8"></script>
 			 
    <div class="yashare-auto-init" data-yasharel10n="ru" data-yasharetype="icon" data-yasharequickservices="yaru,vkontakte,facebook,twitter,odnoklassniki,moimir,lj"></div>
   			 
    <div class="email"><a href="mailto:iphone@foodclub.ru" >iphone@foodclub.ru</a></div>
   		</div>
 	</div>
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>