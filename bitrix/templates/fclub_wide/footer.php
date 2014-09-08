<?
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/layout.css",true);
/*$obCache = new CPageCache;
if($USER->IsAdmin() || $obCache->StartDataCache((3*60*60), "search_form", "/")):
	CModule::IncludeModule("iblock");
	$CFClub = CFClub::getInstance();*/
	global $arKitchens;
	global $arDishType;
	
	$strKitchensID = ''; $strKitchensName = '';
	foreach($arKitchens as $arItem){
		$strKitchensID .= '"'.$arItem['ID'].'", ';
		$strKitchensName .= '"'.$arItem['NAME'].'", ';
	}
	
	$strDishID = ''; $strDishName = '';
	foreach($arDishType as $arItem){
		$strDishID .= '"'.$arItem['ID'].'", ';
		$strDishName .= '"'.$arItem['NAME'].'", ';
	}?>
	<div id="bottom">
		<?if($APPLICATION->GetCurDir() != "/recipes/" && $APPLICATION->GetCurDir() != "/"  && $APPLICATION->GetCurDir() != "/lavka/"){$APPLICATION->IncludeComponent("custom:store.banner.horizontal", "", Array(),false);}?>
		<?$APPLICATION->IncludeFile(BX_ROOT."/templates/fclub/include.footer.php", Array(), Array("MODE"=>"html"))?>
		<div id="liveinternet" style="margin-top: 20px;">
<!--LiveInternet counter-->
<script><!--
document.write("<a href='http://www.liveinternet.ru/click' "+
"target=_blank><img src='//counter.yadro.ru/hit?t44.1;r"+
escape(document.referrer)+((typeof(screen)=="undefined")?"":
";s"+screen.width+"*"+screen.height+"*"+(screen.colorDepth?
screen.colorDepth:screen.pixelDepth))+";u"+escape(document.URL)+
";h"+escape(document.title.substring(0,80))+";"+Math.random()+
"' alt='' title='LiveInternet' "+
"border='0' width='31' height='31'><\/a>")
//--></script>
<!--/LiveInternet-->
</div>
	</div>
	</div></div>
	<div id="top_layer" style="display:none;"><iframe src="/iframe.html" width="10" height="10" frameborder="0"></iframe></div>
	<?if(strpos($APPLICATION->GetCurDir(), "recipe/") !== false){?>
	<div id="stage_ingredients_list_layer" style="display:none;">
		<div class="relative">
			<div class="padding">
				<div id="stage_ingredients_group"></div>
	
				<div id="stage_ingredients_list"></div>
				<div class="clear"></div>
				<div class="button" onClick="addStageIngredients();">Выбрать</div>
			</div>
		  <div class="close_icon" onClick="hideStageIngredientsLayer();"></div>
		</div>
	</div>
		
	<div id="ingredients_list_layer" style="display:none;">
		<div class="relative">
			<div class="padding">
				<div id="ingredients_group"></div>
				<div id="ingredients_list"></div>
				<div class="clear"></div>
				<div class="button" onClick="addIngredients();">Выбрать</div>
	
			</div>
		  <div class="close_icon" onClick="hideIngredientsLayer();"></div>
		</div>
	</div>
	<?}?>
<div id="search_helper" style="display:none;">
	<div class="body">
		<div class="menu">
			<div class="item act"><a href="#" rel="h_helper">Помощник поиска</a><span>Помощник поиска</span></div>                        
			<div class="item right"><a href="#" rel="h_cuisines">Национальные кухни</a><span>Национальные кухни</span></div>
			<div class="clear"></div>
		</div>
		<div id="h_helper" class="search_blocks">
			<div class="column">
				<div class="item">

					<img src="/images/search/diet.jpg" width="200" height="150" alt="">
					<ul>
						<li><a href="/search/Низкокалорийные/">Низкокалорийные</a></li>
						<li><a href="/search/Диетические блюда/">Диетические</a></li>
						<li><a href="/search/Постные блюда/">Постные</a></li>
					</ul>
				</div>

			</div>
			<div class="column">
				<div class="item">
					<img src="/images/search/dinner.jpg" width="200" height="150" alt="">
					<ul>
						<li><a href="/search/Позавтракать/">Позавтракать</a></li>
						<li><a href="/search/Пообедать/">Пообедать</a></li>
						<li><a href="/search/Поужинать/">Поужинать</a></li>
						<li><a href="/search/Поздний ужин/">Поздний ужин</a></li>
					</ul>
				</div>
			</div>
			<div class="column">
				<div class="item">
					<img src="/images/search/quick.jpg" width="200" height="150" alt="">
					<ul>
						<li><a href="/search/Холостяку/">Холостяку</a></li>
						<li><a href="/search/Быстренько/">Быстренько</a></li>
						<li><a href="/search/Хочется экзотики/">Хочется экзотики</a></li>
					</ul>
				</div>
			</div>
			<div class="column right">

				<div class="item">
					<img src="/images/search/guests.jpg" width="200" height="150" alt="">
					<ul>
						<li><a href="/search/День рождения/">День рождения</a></li>
						<li><a href="/search/Удивить гостей/">Удивить гостей</a></li>
						<li><a href="/search/Романтический ужин/">Романтический ужин</a></li>
						<li><a href="/search/Вечеринка/">Вечеринка с друзьями</a></li>
					</ul>
				</div>
			</div>
			<div class="clear"></div>			
		</div>
		<div id="h_ingredients" class="search_blocks">
			<div id="i_have_list">

				<div class="bg">
					<h2>У меня есть:</h2>
					<div id="i_have_dash">—</div>
					<table>
					</table>
				</div>
				<div id="i_have_button">Найти</div>
				<div id="stage_button">Добавить</div>

			</div>
			<div id="i_have_ingredients">
				<div class="search_field">
					<div class="search_input"><input type="text" class="text" value="Введите название" id="helper_smartsearch"><div class="search_list"><ul class="search_list"></ul></div></div>
				</div>
				<div id="i_have_ingredients_group">
					<ul>
					</ul>
				</div>

				<div id="i_have_ingredients_list">
					<h2></h2>
					<div class="column">
						<ul>
						</ul>
					</div>
					<div class="column">
						<ul>
						</ul>

					</div>
					<div class="clear"></div>
				</div>
				<div class="clear"></div>
			</div>
			<div class="clear"></div>
		</div>
		<div id="h_cuisines" class="search_blocks">
			<div class="column">

				<div class="item">
					<img src="/images/search/europe.jpg" width="200" height="150" alt="">
					<h3>Европа</h3>
					<ul>
						<li><a href="/search/Австрийская кухня/">Австрийская кухня</a></li>
						<li><a href="/search/Английская кухня/">Английская кухня</a></li>
						<li><a href="/search/Болгарская кухня/">Болгарская кухня</a></li>

						<li><a href="/search/Венгерская кухня/">Венгерская кухня</a></li>
						<li><a href="/search/Немецкая кухня/">Немецкая кухня</a></li>
						<li><a href="/search/Польская кухня/">Польская кухня</a></li>
						<li><a href="/search/Португальская кухня/">Португальская кухня</a></li>
						<li><a href="/search/Чешская кухня/">Чешская кухня</a></li>
						<li><a href="/search/Шотландская кухня/">Шотландская кухня</a></li>

					</ul>
				</div>
			</div>
			<div class="column">
				<div class="item">
					<img src="/images/search/mediterranean.jpg" width="200" height="150" alt="">
					<h3>Средиземноморье</h3>
					<ul>
						<li><a href="/search/Греческая кухня/">Греческая кухня</a></li>
						<li><a href="/search/Испанская кухня/">Испанская кухня</a></li>
						<li><a href="/search/Итальянская кухня/">Итальянская кухня</a></li>
						<li><a href="/search/Французская кухня/">Французская кухня</a></li>
					</ul>
				</div>
				<div class="item">

					<img src="/images/search/asia.jpg" width="200" height="150" alt="">
					<h3>Азия</h3>
					<ul>
						<li><a href="/search/Индонезийская кухня/">Индонезийская кухня</a></li>
						<li><a href="/search/Китайская кухня/">Китайская кухня</a></li>
						<li><a href="/search/Тайская кухня/">Тайская кухня</a></li>
						<li><a href="/search/Японская кухня/">Японская кухня</a></li>
					</ul>
				</div>
			</div>
			<div class="column">
				<div class="item">
					<img src="/images/search/ussr.jpg" width="200" height="150" alt="">
					<h3>Бывший СССР</h3>
					<ul>
						<li><a href="/search/Армянская кухня/">Армянская кухня</a></li>
						<li><a href="/search/Белорусская кухня/">Белорусская кухня</a></li>
						<li><a href="/search/Грузинская кухня/">Грузинская кухня</a></li>
						<li><a href="/search/Русская кухня/">Русская кухня</a></li>
						<li><a href="/search/Узбекская кухня/">Узбекская кухня</a></li>
						<li><a href="/search/Украинская кухня/">Украинская кухня</a></li>
					</ul>
				</div>
			</div>
			<div class="column right">
				<div class="item">
					<img src="/images/search/america.jpg" width="200" height="150" alt="">
					<h3>Америка</h3>
					<ul>

						<li><a href="/search/Американская кухня/">Американская кухня</a></li>
						<li><a href="/search/Бразильская кухня/">Бразильская кухня</a></li>
						<li><a href="/search/Кубинская кухня/">Кубинская кухня</a></li>
						<li><a href="/search/Мексиканская кухня/">Мексиканская кухня</a></li>
						<li><a href="/search/Ямайская кухня/">Ямайская кухня</a></li>
					</ul>

				</div>
			</div>
			<div class="clear"></div>
		</div>
		<div class="slide_up_button"><div></div></div>
	</div>
</div>

	<script language="javascript" type="text/javascript">
	<!--
		var kitchenArray = new Array();
		kitchenArray[0] = new Array(<?=substr($strKitchensID, 0, -2)?>);
		kitchenArray[1] = new Array(<?=substr($strKitchensName, 0, -2)?>);
		
		var dishArray = new Array();
		dishArray[0] = new Array(<?=substr($strDishID, 0, -2)?>);
		dishArray[1] = new Array(<?=substr($strDishName, 0, -2)?>);
<!--		<?if(strpos($APPLICATION->GetCurDir(), "recipe/") === false){?>var ingredientArray = new Array();<?=$strUnitHtml?><?}?> -->
		var chosenSearchKitchen = new Array();
		var chosenSearchDish = new Array();
		var chosenSearchIngredient = new Array();
		chosenSearchIngredient[0] = new Array();
		chosenSearchIngredient[1] = new Array();
	-->
	</script>
<?/*endif;*/?>
<?/*?>
	<div id="authorization_layer" style="display:none;">
		<div class="relative">
			<div class="padding">
				<div class="bar">
					<div class="act"><span>Авторизация</span><a href="#" rel="authorization_field" class="no_link">Авторизация</a></div>
					<div><span>LiveJournal id</span><a href="#" rel="lj_id_field" class="no_link">LiveJournal id</a></div>
					<div><span>Open id</span><a href="#" rel="open_id_field" class="no_link">Open id</a></div>
					<div class="clear"></div>
				</div>
			<div class="body">
				<div id="authorization_field" class="act">
					<form action="/auth/index.php" method="post" name="authorization">
						<input type="hidden" name="AUTH_FORM" value="Y" />
						<input type="hidden" name="TYPE" value="AUTH" />
						<input type='hidden' name='backurl' value='<?=$APPLICATION->GetCurPage()?>' />
						<div class="fields">
	
							<div class="form_field">
								<h5>Логин <span>?</span></h5>
								<input type="text" class="text" name="USER_LOGIN">
							</div>
							<div class="form_field">
								<h5>Пароль <span>?</span></h5>
								<input type="password" class="text" name="USER_PASSWORD">
	
							</div>
							<div class="clear"></div>
							<div class="remember form_checkbox_pic"><img src="/images/checkbox.gif" width="12" height="12" alt=""><span>Запомнить меня</span><input type="hidden" value="N" name="USER_REMEMBER"></div>
							
							
							<div class="forget"><div><a href="/registration/?backurl=<?=$APPLICATION->GetCurPage()?>">Зарегистрироваться!</a></div><a href="/auth/?forgot_password=yes&backurl=%2Findex.php">Забыли?</a></div>
							<div class="clear"></div>
						</div>
					</form>
				</div>
				<div id="lj_id_field" >
					<div class="fields">
						<div class="form_field">
							<h5>Имя пользователя <span>?</span></h5>
							<input type="text" class="text" value="">
						</div>
					</div>
					<form action="/auth/index.php" method="post" name="lj_id">
					<input type='hidden' name='backurl' value='<?=$APPLICATION->GetCurPage()?>' />
					<input type="hidden" name="OPENID_IDENTITY" value="">
					</form>
				</div>
				
				<div id="open_id_field">
					<form action="/auth/index.php" method="post" name="open_id">
					<input type='hidden' name='backurl' value='<?=$APPLICATION->GetCurPage()?>' />
						<div class="fields">
							<div class="form_field">
								<h5>Имя пользователя <span>?</span></h5>
								<input type="text" class="text" name="OPENID_IDENTITY" value="http://">
							</div>
						</div>
					</form>
				</div>
			</div>
			<div class="button">Войти</div>
			</div>
			<div class="close_icon"></div>
		</div>
	</div>
<?*/?>
<!-- Yandex.Metrika counter --><div style="display:none;"><script type="text/javascript">(function(w, c) { (w[c] = w[c] || []).push(function() { try { w.yaCounter7715218 = new Ya.Metrika({id:7715218, clickmap:true, accurateTrackBounce:true}); } catch(e) { } }); })(window, 'yandex_metrika_callbacks');</script></div><script src="//mc.yandex.ru/metrika/watch.js" type="text/javascript" defer="defer"></script><noscript><div><img src="//mc.yandex.ru/watch/7715218" style="position:absolute; left:-9999px;" alt="" /></div>
</noscript><!-- /Yandex.Metrika counter -->

</body>
</html>
