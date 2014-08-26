<link rel="stylesheet" type="text/css" href="/foodshot/foodshot-add.css">

<div id="add-foodshot-layer" style="display: none;">
	<a href="#" class="b-close-icon"></a>

	<div id="add-foodshot-layer__choice" class="b-af__choice">
		<a href="#" class="b-af__choice__link b-af__choice__url">
			<span class="b-af__choice__ill"></span>
			<span class="b-af__choice__heading">Ссылка</span>
			<span class="b-af__choice__note">Фото размещено<br>на другом сайте.</span>
		</a>
		<a href="#" class="b-af__choice__link b-af__choice__file">
			<span class="b-af__choice__ill"></span>
			<span class="b-af__choice__heading">Фото</span>
			<span class="b-af__choice__note">Файл на вашем<br>компьютере.</span>
		</a>
		<div class="i-clearfix"></div>
	</div>

	<div id="add-foodshot-layer__preloader">
		<h2 class="b-af__form__heading">Загружаем фудшот</h2>
		<img src="/images/preloader-grey.gif" width="281" height="52" alt="" class="b-af__form__preloader" />
	</div>
	
	<div id="add-foodshot-layer__file" class="b-af__form">
		<h2 class="b-af__form__heading">
			<span class="b-af__form__heading__text"><span class="i-add">Добавить</span><span class="i-edit">Редактировать</span> фудшот</span>
			<a href="#" class="b-af__form__heading__back" title="Вернуться назад"></a>
		</h2>
		<form action="" method="get">
			<input type="hidden" name="id" value="" class="b-id-input">
			<fieldset class="b-af__form__photo">
				
				<div class="b-af__form__source">

					<div class="b-file-upload b-file-upload__type_preview">
						<div class="i-relative"><div class="b-shutter"></div></div>
						<div class="b-form-field">
							<label class="b-form-label">Файл <span class="b-form-label__error"></span></label>
							
							<span class="b-file-upload__button">
								<input type="file" class="b-file-upload__file" id="fileupload">
							</span>
							<span class="b-file-upload__name"></span>
						</div>
						
						<div class="b-form-field">
							<div class="b-af__form__preview b-preview">
								<label class="b-form-label">Превью</label>
								<div class="b-preview__screen b-file-upload__files-container">
									<img src="/images/spacer.gif" width="260" height="177" alt="" />
									<input type="hidden" name="photo" value="" required>
								</div>
							</div>
						</div>
						
						<div class="i-clearfix"></div>
					</div>
					
				</div>

				<script id="template-upload" type="text/x-tmpl">
				<% for (var i=0; i < files.length; i++) { %>
				    <div></div>
				<% } %>
				</script>
				<!-- The template to display files available for download -->
				<script id="template-download" type="text/x-tmpl">
				<% for (var i=0; i < files.length; i++) { %>
					<img src="<%=files[i].url%>" alt="" width="260" onload="addFoodshotFileImageOnload(this);" />
					<input type="hidden" name="photo" value="<%=files[i].url%>">
				<% } %>
				</script>



				<script src="/js/file-upload/js/vendor/jquery.ui.widget.js"></script>
				<script src="/js/file-upload/js/jquery.iframe-transport.js"></script>
				<script src="/js/file-upload/js/jquery.fileupload.js"></script>
				<script src="/js/file-upload/js/jquery.fileupload-fp.js"></script>
				<script src="/foodshot/jquery.fileupload-ui.js"></script>
				<script src="/foodshot/script.js"></script>
				<!-- The XDomainRequest Transport is included for cross-domain file deletion for IE8+ -->
				<!--[if gte IE 8]><script src="js/cors/jquery.xdr-transport.js"></script><![endif]-->

			</fieldset>
			
			<fieldset class="b-af__form__details">
				<div class="b-form-field b-af__form__title">
					<label class="b-form-label">Название</label>
					<input type="text" name="title" value="" class="b-input-text" required>
				</div>
				<div class="b-form-field b-af__form__description" data-counter="200">
					<div class="b-counter"></div>
					<label class="b-form-label">Описание</label>
					<textarea name="description" cols="10" rows="10" class="b-textarea" required></textarea>
				</div>
			</fieldset>
			
			<div class="i-clearfix"></div>
			
			<div class="b-af__form__submit"><input type="submit" value="Добавить шот" class="b-button" /></div>
		</form>
		
	</div>
	
	<div id="add-foodshot-layer__url" class="b-af__form">
		<h2 class="b-af__form__heading">
			<span class="b-af__form__heading__text"><span class="i-add">Добавить</span><span class="i-edit">Редактировать</span> фудшот</span>
			<a href="#" class="b-af__form__heading__back" title="Вернуться назад"></a>
		</h2>
		<form action="" method="get">
			<input type="hidden" name="id" value="" class="b-id-input">
			<fieldset class="b-af__form__url">
            	<div class="i-relative"><div class="b-shutter"></div></div>			
				<div class="b-form-field">
					<label class="b-form-label">Ссылка</label>
					<input type="text" name="url" value="" class="b-input-text" required>
					<a href="#" class="b-button">Найти картинку</a>
				</div>
			</fieldset>
			
			<div class="b-af__form__hidden-fields">

				<div class="b-af__hr"></div>
				
				<fieldset class="b-af__form__preview">
					<div class="i-relative"><div class="b-shutter"></div></div>
					<div class="b-preview">
						<label class="b-form-label">Превью</label>
						<div class="b-preview__screen">
							<div class="b-preview__belt">
								<img src="/images/spacer.gif" width="260" height="177" alt="" />
							</div>
							<input type="hidden" name="photo" value="" required>
						</div>
						<div class="b-preview__nav">
							<a href="#" class="b-preview__nav__left" title="Назад"></a><a href="#" class="b-preview__nav__right" title="Вперёд"></a>
						</div>
					</div>
				</fieldset>
				
				<fieldset class="b-af__form__details">
					<div class="b-form-field b-af__form__title">
						<label class="b-form-label">Название</label>
						<input type="text" name="title" value="" class="b-input-text" required>
					</div>
					<div class="b-form-field b-af__form__description" data-counter="200">
						<div class="b-counter"></div>
						<label class="b-form-label">Описание</label>
						<textarea name="description" cols="10" rows="10" class="b-textarea" required></textarea>
					</div>
				</fieldset>
				
				<div class="i-clearfix"></div>
				
				<div class="b-af__form__submit"><input type="submit" value="Добавить шот" class="b-button" /></div>
			</div>
		</form>
		
	</div>
	
</div>