<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if(strlen($arResult["BACKGROUND"])):?>
<div class="b-personal-page__intro__ill b-personal-page__intro__ill__type_custom">
	<div class="i-relative">
		
		<div class="b-personal-page__intro__ill__background" style="background-image: url('<?=$arResult["BACKGROUND"]?>');"></div>
		<div class="b-personal-page__intro__ill__pic" style="background-image: url('<?=$arResult["BACKGROUND"]?>');"></div>
		<?if(intval(CUser::GetID()) &&  ((CUser::GetID() == $arParams["USER_ID"]) || intval($arParams["USER_ID"]) <= 0)):?>
		<div class="b-personal-page__intro__ill__button" title="Загрузите картинку для обложки 960×135 пикселей">
			<input type="file" class="b-personal-page__ill__file" id="fileupload">
		</div>
		<?endif;?>
		<div class="b-personal-page__intro__ill__error-message"></div>
	</div>
</div>
<?elseif(strlen($arResult["DEFAULT_BACKGROUND"])):?>
<div class="b-personal-page__intro__ill b-personal-page__intro__ill__type_custom">
	<div class="i-relative">
		
		<div class="b-personal-page__intro__ill__background" style="background-image: url('<?=$arResult["DEFAULT_BACKGROUND"]?>');"></div>
		<div class="b-personal-page__intro__ill__pic" style="background-image: url('<?=$arResult["DEFAULT_BACKGROUND"]?>');"></div>
		<?if(intval(CUser::GetID()) &&  ((CUser::GetID() == $arParams["USER_ID"]) || intval($arParams["USER_ID"]) <= 0)):?>
		<div class="b-personal-page__intro__ill__button" title="Загрузите картинку для обложки 960×135 пикселей">
			<input type="file" class="b-personal-page__ill__file" id="fileupload">
		</div>
		<?endif;?>
		<div class="b-personal-page__intro__ill__error-message"></div>
	</div>
</div>
<?else:?>
<div class="b-personal-page__intro__ill">
	<div class="i-relative">
		
		<div class="b-personal-page__intro__ill__background"></div>
		<div class="b-personal-page__intro__ill__pic"></div>
		<?if(intval(CUser::GetID()) &&  ((CUser::GetID() == $arParams["USER_ID"]) || intval($arParams["USER_ID"]) <= 0)):?>
		<div class="b-personal-page__intro__ill__button" title="Загрузите картинку для обложки 960×135 пикселей">
			<input type="file" class="b-personal-page__ill__file" id="fileupload">
		</div>
		<?endif;?>
		<div class="b-personal-page__intro__ill__error-message"></div>
	</div>
</div>
<?endif;?>
<script id="template-upload" type="text/x-tmpl">
<% for (var i=0, file; file=files[i]; i++) { %>
    <tr class="template-upload fade">
    </tr>
<% } %>
</script>
<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
<% for (var i=0, file; file=o.files[i]; i++) { %>
	<div class="b-file-upload__type_avatar__avatar">
		<img src="<%=file.thumbnail_url%>" width="100" height="100" alt="" />
		<input type="hidden" name="avatar" value="<%=file.url%>">
	</div>
<% } %>
</script>