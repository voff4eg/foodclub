<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div id="content">
	<h1>Редактирование анкеты</h1>
	<div class="b-personal-edit-form b-form__type_wide">
		<form name="form1" action="<?=$arResult["FORM_TARGET"]?>" method="post" enctype="multipart/form-data">
			<?if(strlen($arResult["strProfileError"])):?>
			<div class="b-error-message">
				<div class="b-error-message__pointer">
					<div class="b-error-message__pointer__div"></div>
				</div>
				<?=$arResult["strProfileError"]?>
			</div>
			<div class="i-clearfix"></div>
			<?endif;?>
			<?=$arResult["BX_SESSION_CHECK"]?>
			<input type="hidden" name="lang" value="<?=LANG?>" />
			<input type="hidden" name="ID" value=<?=$arResult["ID"]?> />
			<fieldset class="b-fieldset b-personal-edit-form__about">
				<legend>О себе</legend>
				<h3 class="b-hr-bg b-personal-page__heading">
					<span class="b-hr-bg__content">О себе</span>
				</h3>
				
				<div class="b-form-fieldset b-form-fieldset__num_2">
					<div class="b-form-field b-form-field__avatar">
						<label class="b-form-label">Фото <span class="b-form-label__error"></span></label>
						<div class="b-file-upload b-file-upload__type_avatar">
							
							<?if(IntVal($arResult['arUser']['PERSONAL_PHOTO']) > 0){?>
							<div class="b-file-upload__files-container">
								<div class="b-file-upload__type_avatar__avatar">
									<img src="<?=$arResult['arUser']['arFile']['SRC']?>" width="<?=$arResult['arUser']['arFile']['WIDTH']?>" height="<?=$arResult['arUser']['arFile']['HEIGHT']?>" alt="" />									
								</div>
							</div>
							<?}?>
				            <span class="b-file-upload__button">
								<input type="file" class="b-file-upload__file" id="fileupload">
							</span>
							
							<div class="i-clearfix"></div>
							
				        </div>
						
				        <div class="fileupload-loading"></div>
					</div>
					
					<script id="template-upload" type="text/x-tmpl">
					<% for (var i=0, file; file=files[i]; i++) { %>
					    <tr class="template-upload fade">
					    </tr>
					<% } %>
					</script>
					<!-- The template to display files available for download -->
					<script id="template-download" type="text/x-tmpl">
					<% for (var i=0, file; file=files[i]; i++) { %>
						<div class="b-file-upload__type_avatar__avatar">
							<img src="<%=file.thumbnail_url%>" width="100" height="100" alt="" />
							<input type="hidden" name="avatar" value="<%=file.url%>">
						</div>
					<% } %>
					</script>

					<script src="/js/file-upload/js/vendor/jquery.ui.widget.js"></script>					
					<script src="/js/file-upload/js/jquery.iframe-transport.js"></script>
					<script src="/js/file-upload/js/jquery.fileupload.js"></script>
					<script src="/js/file-upload/js/jquery.fileupload-fp.js"></script>
					<script src="/bitrix/components/custom/profile.edit/templates/custom/jquery.fileupload-ui.js"></script>
					<script src="/components/personal.edit/script.js"></script>
					<!-- The XDomainRequest Transport is included for cross-domain file deletion for IE8+ -->
					<!--[if gte IE 8]><script src="js/cors/jquery.xdr-transport.js"></script><![endif]-->

					<div class="b-form-field b-form-field__date">
						<label class="b-form-label">Дата рождения</label>
						<select class="b-select b-select__day" name="birthday">
						<?for($i=1; $i<=31; $i++){?>
						<option value="<?=$i?>" <?if($i == $arResult["DATE"][0]){?>selected="selected"<?}elseif($i == date("j") && !isset($arResult["DATE"][0])){?>selected="selected"<?}?>><?=$i?></option>
						<?}?>
						</select>
						<select class="b-select b-select__month" name="birthmonth">
						<?foreach($arResult["MONTH"] as $strKey => $strItem){?>
						<option value="<?=$strKey?>" <?if($strKey == $arResult["DATE"][1]){?>selected="selected"<?}elseif($strKey == date("m")  && !isset($arResult["DATE"][1])){?>selected="selected"<?}?>><?=$strItem?></option>
						<?}?>
						</select>
						<select class="b-select b-select__year" name="birthyear">
						<?$intYear = date("Y");
						for($i=$intYear-100; $i<=$intYear-6; $i++){?>
						<option value="<?=$i?>" <?if($i == $arResult["DATE"][2]){?>selected="selected"<?}elseif($i == $intYear-30  && !isset($arResult["DATE"][2])){?>selected="selected"<?}?>><?=$i?></option>
						<?}?>
						</select>								
						<div class="i-clearfix"></div>

						<div class="b-form-field">						
							<label class="b-checkbox-block b-checkbox-block__type_inset">
								<span class="b-checkbox-block__input"><input type="checkbox" name="UF_NO_BIRTHDAY" class="b-checkbox"<?=(IntVal($arResult['arUser']['UF_NO_BIRTHDAY']) > 0 ? " checked" : "")?>></span>
								<span class="b-checkbox-block__label">Не показывать на сайте</span>
							</label>
						</div>
				
						<div class="b-form-field">
							<label class="b-form-label">Пол</label>
							<select class="b-select b-select__gender" name="PERSONAL_GENDER">
								<option value="M"<?=($arResult['arUser']["PERSONAL_GENDER"] == "M" ? " selected=selected" : "")?>>Мужской</option>
								<option value="F"<?=($arResult['arUser']["PERSONAL_GENDER"] == "F" ? " selected=selected" : "")?>>Женский</option>
							</select>
						</div>
					</div>
				</div>

				<div class="b-form-fieldset b-form-fieldset__num_2">
					<div class="b-form-note">&mdash; Заполните поля они будут отображаться у каждого рецепта вместо Логина.</div>
					<div class="b-form-field">
						<label class="b-form-label">Имя</label>							
						<input type="text" name="NAME" value="<?=$arResult['arUser']['NAME']?>" class="b-input-text b-personal-edit-form__about__name">
					</div>
					<div class="b-form-field">
						<label class="b-form-label">Фамилия</label>							
						<input type="text" name="LAST_NAME" value="<?=$arResult['arUser']['LAST_NAME']?>" class="b-input-text b-personal-edit-form__about__lastname">
					</div>
				</div>	

				<div class="b-form-fieldset b-form-fieldset__num_2">
					<div class="b-form-field">
						<label class="b-form-label">Email</label>
						<input type="email" value="<?=$arResult['arUser']['EMAIL']?>" name="EMAIL" class="b-input-text b-personal-edit-form__about__email">
					</div>
				</div>				
				
				<div class="b-form-fieldset">
					<div class="b-form-note">&mdash; Напишите о том, кем вы себя ощущаете. Например Король кулинаров!</div>
					<div class="b-form-field">
						<label class="b-form-label">Статус</label>
						<input type="text" value="<?=$arResult['arUser']['UF_INFO_STATUS']?>" name="UF_INFO_STATUS" class="b-input-text b-personal-edit-form__about__status">
					</div>
				</div>

				<div class="b-form-fieldset">
					<div class="b-form-note">&mdash; Посетители вашей страницы смогут больше узнать о вашей работе или хобби.</div>
					<div class="b-form-field">
						<label class="b-form-label">Расскажите о себе подробнее</label>
						<textarea cols="" rows="" name="UF_ABOUT_SELF" class="b-textarea b-personal-edit-form__about__about"><?=$arResult['arUser']['UF_ABOUT_SELF']?></textarea>
					</div>
				</div>					
			</fieldset>

			<fieldset class="b-fieldset b-personal-edit-form__links">
				<legend>Ссылки на ваши страницы</legend>
				<h3 class="b-hr-bg b-personal-page__heading">
					<span class="b-hr-bg__content">Ссылки на ваши страницы</span>
				</h3>
				
				<div class="b-form-fieldset">
					<div class="b-form-note">&mdash; Вставляйте полный адрес ссылки. Например: http://www.facebook.com/foodclubru</div>
					<div class="b-form-field">
						<label class="b-form-label">Ваш сайт</label>
						<input type="text" value="<?=$arResult['arUser']['WORK_WWW']?>" name="WORK_WWW" class="b-input-text b-personal-edit-form__links__site">							
					</div>
				</div>
				
				<div class="b-form-fieldset b-form-fieldset__num_2">
					<div class="b-form-field">
						<label class="b-form-label">Facebook</label>
						<input type="text" value="<?=$arResult['arUser']['UF_FACEBOOK']?>" name="UF_FACEBOOK" class="b-input-text b-form-field__network b-form-field__fb">
					</div>
					<div class="b-form-field">
						<label class="b-form-label">Вконтакте</label>
						<input type="text" value="<?=$arResult['arUser']['UF_VKONTAKTE']?>" name="UF_VKONTAKTE" class="b-input-text b-form-field__network b-form-field__vk">
					</div>
					
					<div class="b-form-field">
						<label class="b-form-label">Twitter</label>
						<input type="text" value="<?=$arResult['arUser']['UF_TWITTER']?>" name="UF_TWITTER" class="b-input-text b-form-field__network b-form-field__twitter">
					</div>
					<div class="b-form-field">
						<label class="b-form-label">Яндекс</label>
						<input type="text" value="<?=$arResult['arUser']['UF_YANDEX']?>" name="UF_YANDEX" class="b-input-text b-form-field__network b-form-field__yandex">
					</div>
					
					<div class="b-form-field">
						<label class="b-form-label">Livejournal</label>
						<input type="text" value="<?=$arResult['arUser']['UF_LJ']?>" name="UF_LJ" class="b-input-text b-form-field__network b-form-field__lj">
					</div>
					<div class="b-form-field">
						<label class="b-form-label">Одноклассники</label>
						<input type="text" value="<?=$arResult['arUser']['UF_ODNOKLASSNIKI']?>" name="UF_ODNOKLASSNIKI" class="b-input-text b-form-field__network b-form-field__classmates">
					</div>
					
					<div class="b-form-field">
						<label class="b-form-label">Youtube</label>
						<input type="text" value="<?=$arResult['arUser']['UF_YOUTUBE']?>" name="UF_YOUTUBE" class="b-input-text b-form-field__network b-form-field__youtube">
					</div>
					<div class="b-form-field">
						<label class="b-form-label">Vimeo</label>
						<input type="text" value="<?=$arResult['arUser']['UF_VIMEO']?>" name="UF_VIMEO" class="b-input-text b-form-field__network b-form-field__vimeo">
					</div>
				</div>
			</fieldset>
			
			<?if(strlen($arUser['EXTERNAL_AUTH_ID']) === 0){?>
			<fieldset class="b-fieldset b-personal-edit-form__change-password">
				<legend>Смена пароля</legend>
				<h3 class="b-hr-bg b-personal-page__heading">
					<span class="b-hr-bg__content">Смена пароля</span>
				</h3>
				
				<div class="b-form-fieldset b-form-fieldset__num_2">
					<div class="b-form-note">&mdash; Вы можете поменять пароль и логин.
Если вы не хотите менять — оставьте поля не заполнеными.</div>
					<div class="b-form-field">
						<label class="b-form-label">Новый пароль</label>
						<input type="password" value="" name="NEW_PASSWORD" class="b-input-text b-personal-edit-form__change-password__password" autocomplete="off" data-equal="password">
					</div>
					<div class="b-form-field">
						<label class="b-form-label">Подтверждение пароля</label>
						<input type="password" value="" name="NEW_PASSWORD_CONFIRM" class="b-input-text b-personal-edit-form__change-password__new-password" autocomplete="off" data-equal="password">
					</div>
				</div>
			</fieldset>				
			<?}?>

			<div class="b-form__submit">
				<input type="submit" name="save" class="b-button b-button__type_submit" value="Сохранить">
			</div>

		</form>
		
		<div class="clear"></div>
	</div>
</div>