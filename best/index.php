<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "Самые интересные подборки рецептов и статей объединенных одной тематикой собранны на страницах раздела «Самое интересное»");
$APPLICATION->SetTitle("Самое интересное на Foodclub.ru");
$APPLICATION->SetAdditionalCSS("/best/best.css");
?> 
<div id="content"> 	 
  <h1>Самое интересное</h1>
 
  <p>На свете существует множество блюд и рецептов, часть из них воплощена на нашем сайте в виде фоторецептов. Но и здесь их уже стало так много, что мы решили выделить самые популярные, и разместить их на этой страничке по категориям.
    <br />
   
    <br />
   </p>
 	 
  <div class="b-best-block"> 		 
    <h2>Выпуск 1</h2>
   		 
    <div class="b-grid"> 			 
      <div class="b-gird__column"> 				<?$APPLICATION->IncludeFile(SITE_TEMPLATE_PATH."/include_areas/1.php", Array(), Array(
				    "MODE"      => "html",                                           // будет редактировать в веб-редакторе
				    "NAME"      => "Редактирование включаемой области",      // текст всплывающей подсказки на иконке
				    "TEMPLATE"  => ""                    // имя шаблона для нового файла
				    )
				);?>				 			</div>
     			 			 
      <div class="b-gird__column"> 				<?$APPLICATION->IncludeFile(SITE_TEMPLATE_PATH."/include_areas/2.php", Array(), Array(
				    "MODE"      => "html",                                           // будет редактировать в веб-редакторе
				    "NAME"      => "Редактирование включаемой области",      // текст всплывающей подсказки на иконке
				    "TEMPLATE"  => ""                    // имя шаблона для нового файла
				    )
				);?>				 			</div>
     			 			 
      <div class="b-gird__column"> 				<?$APPLICATION->IncludeFile(SITE_TEMPLATE_PATH."/include_areas/3.php", Array(), Array(
				    "MODE"      => "html",                                           // будет редактировать в веб-редакторе
				    "NAME"      => "Редактирование включаемой области",      // текст всплывающей подсказки на иконке
				    "TEMPLATE"  => ""                    // имя шаблона для нового файла
				    )
				);?>				 			</div>
     		</div>
   	</div>
 </div>
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>