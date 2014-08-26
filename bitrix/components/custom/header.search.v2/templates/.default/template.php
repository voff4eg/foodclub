<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div id="recipeSearch" class="b-recipe-search">
	<form action="/search/" method="post">
		<div class="b-form-field b-recipe-search__form-field">
			<a href="#" class="b-recipe-search__delete" title="Очистить поле"></a>
			<input type="text" class="b-input-text b-recipe-search__input" value="" autocomplete="off" data-placeholder="Я ищу">
		</div>
		<button class="b-recipe-search__button"></button>
		<div class="i-clearfix"></div>
	</form>
	<p class="b-recipe-search__helper"><noindex><a href="#" id="search_helper_link" class="b-recipe-search__helper__link">Помощник</a></noindex></p>
</div>

<script type="text/html" id="recipe_search_items">
	<li class="b-rs__item">
		<a href="<%=url%>" class="b-rs__item__link">
			<% var imageRegExp = new RegExp('\.(gif)|(jpeg)|(jpg)|(png)$', 'gi'); %>
			<% if(imageRegExp.test(image)) { %>
			<span class="b-rs__item__image-wrapper" data-image="<%=image%>"></span>
			<% } else { %>
			<span class="b-rs__item__image-wrapper b-rs__item__image-wrapper__type_empty"></span>
			<% } %>
			<span class="b-rs__item__text b-rs-str"><%=title%></span>
			<span class="b-rs__item__info">
				<span class="b-rs__item__time"><% if(time.hours && time.hours > 0) {%>
				<% function recipeWord(num) {
					if (/(10|11|12|13|14|15|16|17|18|19)$/.test(num)) {return 'часов';}
					else if (/.*1$/.test(num)) {return 'час';}
					else if (/[2-4]$/.test(num)) {return 'часа';}
					else {return 'часов';}
				} %>
				<%=time.hours%> <%=recipeWord(time.hours)%><% } %><% if(time.minutes && time.minutes > 0) {%> <%=time.minutes%> мин<% } %></span>
				<span class="b-rs__item__nutrition"><%=nutrition%></span>
			</span>
			<span class="clear"></span>
		</a>
		<span class="b-rs__item__hr"></span>
	</li>
</script>