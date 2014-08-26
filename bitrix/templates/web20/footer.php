<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeTemplateLangFile(__FILE__);
?> 
</div>

<div id="bottom">
		<div id="blogs"><div class="lj"><a href="http://community.livejournal.com/foodclub_ru/" target="_blank"><img src="/images/livejournal.gif" width="40" height="40" alt="" title="Мы в LiveJournal"></a></div><div class="mail"><a href="http://blogs.mail.ru/community/foodclub/" target="_blank"><img src="/images/mail_ru.gif" width="74" height="21" alt="" title="Мы на mail.ru"></a></div><div class="clear"></div></div>
		<div id="letter"><a href="">Связаться с нами</a><div class="letter_icon"></div></div>

		<div id="made">Сайт создан <a href="http://twinpx.ru" target="_blank">Twin px</a> 2008 год</div>
		<div class="clear"></div>
		<div id="tags">Сайт посвящен рецептам с пошаговыми фотографиями. На сайте уже можно найти <a href="/all_recipes/">рецепты с курицей</a>, <a href="/all_recipes/">рецепты из тыквы</a>, <a href="/all_recipes/">супы</a>, <a href="/all_recipes/">индейка на гриле</a>, <a href="/all_recipes/">авторские рецепты</a></div>

		<div id="copyright"><div class="copyright">&copy;</div> Все фотографии, опубликованные на сайте, могут быть использованы только с письменного разрешения авторов рецептов.<br>При использовании материалов из этого проекта ссылка на данный ресурс обязательна.</div>
	</div>
</div></div>


<div id="top_layer" style="display:none;"><iframe src="/iframe.html" width="10" height="10" frameborder="0"></iframe></div>
<div id="ingredients_list_layer" style="display:none;">
	<div class="relative">
		<div class="padding">
			<div id="ingredients_group"></div>
			<div id="ingredients_list"></div>
			<div class="clear"></div>
			<div class="button"><input type="button" value="Добавить ингредиенты" onClick="addIngredients();"></div>

		</div>
	  <div class="close_icon" onClick="hideIngredientsLayer();"></div>
	</div>
</div>

</body>
</html>