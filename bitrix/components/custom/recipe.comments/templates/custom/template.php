<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(false);?>
<script src="/js/file-upload/js/vendor/jquery.ui.widget.js"></script>
<script src="/js/file-upload/js/jquery.iframe-transport.js"></script>
<script src="/js/file-upload/js/jquery.fileupload.js"></script>
<script type="text/template" id="comment-template">
	<div class="b-rc__block">
		<div class="b-rc__item<%if(mine) {%> i-mine<%}%>" data-id="<%=id%>">
			<div class="i-relative">
				<a href="#" class="b-rc__item__close b-close-icon b-close-icon__color_3" title="Закрыть"></a>
				<div class="b-rc__item__pointer"></div>
				<div class="b-rc__item__author b-author-avatar">
					<a href="<%=author.href%>" class="b-author-avatar__link">
						<img src="<%=author.src%>" width="100%" height="100%" alt="<%=author.name%>" class="b-author-avatar__img">
					</a>
				</div>
				<div class="b-rc__admin-buttons b-admin-buttons">
					<div class="b-admin-buttons__block">
						<div class="b-delete-icon" title="Удалить отзыв"></div>
						<div class="b-edit-icon" title="Редактировать"></div>
					</div>
				</div>
			</div>
			<div class="b-rc__item__content">
				<div class="b-rc__item__content__author"><a href="<%=author.href%>"><%=author.name%></a></div>
				<% if(image.src && image.id) { %>
				<div class="b-rc__item__content__text">
					<div class="b-rc__item__content__text__img">
						<img src="<%=image.src%>" height="65" alt="" class="i-align-img" data-id="<%=image.id%>" />
					</div>
					<div class="b-rc__item__content__text__content"><%=text.html%></div>
					<div class="i-clearfix"></div>
				</div>
				<% } else { %>
				<div class="b-rc__item__content__text">
					<%=text.html%>
				</div>
				<% } %>
				<div class="b-rc-edit-form">
					<form action="" method="post">
						<div class="b-form-field b-form__photo-block">
							<% if(image.src && image.id) { %>
							<div class="b-admin-buttons">
								<div class="b-admin-buttons__block">
									<div class="b-delete-icon" title="Удалить фото"></div>
									<div class="b-image-icon" title="Загрузить новое фото">
										<div class="b-image-icon__input-file input_file"><input type="file" class="text" id="fileupload<%=id%>" data-ajax-url="/js/file-upload/server/php/comment.php"></div>
										<input type="hidden" name="comment-photo" value="" class="b-image-icon__photo-url" />
									</div>
								</div>
							</div>
							<div class="b-form__photo-block__img">
								<img src="<%=image.src%>" height="65" alt="" class="i-align-img" data-id="<%=image.id%>" />
							</div>
							<% } else { %>
							<div class="input_file"><input type="file" class="text" id="fileupload<%=id%>" data-ajax-url="/js/file-upload/server/php/comment.php"></div>
							<div class="b-form__photo-block__img"></div>
							<input type="hidden" name="comment-photo" value="" class="b-form__photo-block__url" />
							<% } %>
						</div>
						
						<div class="b-form-field b-form__text-block">
							<input type="hidden" value="<%=id%>" name="cId">
							<input type="hidden" value="<%=rId%>" name="rId">
							<input type="hidden" value="e" name="a">
							<input id="sessid" type="hidden" value="<%=sessid%>" name="sessid">
							<label class="b-form-label">Редактирование отзыва <span class="b-form-attention">?</span></label>
							<div class="b-textarea-wrapper"><textarea name="comment" cols="10" rows="5" class="b-textarea" required><%=text.text%></textarea></div>
							<button class="b-button b-button__width_100" type="submit">Написать отзыв</button>
						</div>
						<div class="i-clearfix"></div>
					</form>
				</div>
				<div class="b-rc__props">
					<div class="b-rc__props__reply">
						<a class="b-button__size_S" href="#">Ответить</a>
					</div>
					<div class="b-rc__props__like">
						<span class="b-like">
							<a title="Мне нравится" class="b-like-icon b-like-icon__type-button" href="#" data-ajax-url="/bitrix/components/custom/recipe.comments/like.php"></a>
							<span class="b-like-num"><%=likeNum%></span>
						</span>
					</div>
					<div class="b-rc__props__date"><%=date%></div>
					<div class="i-clearfix"></div>
				</div>
			</div>
		</div>
	</div>
</script>

<script type="text/template" id="reply-template">
	<div class="b-rc__item" data-id="<%=id%>">
		
		<div class="i-relative">
			<a href="#" class="b-rc__item__close b-close-icon b-close-icon__color_3" title="Закрыть"></a>
			<div class="b-rc__item__pointer"></div>
			<div class="b-rc__item__author b-author-avatar">
				<a href="<%=author.href%>" class="b-author-avatar__link">
					<img src="<%=author.src%>" width="100%" height="100%" alt="<%=author.name%>" class="b-author-avatar__img">
				</a>
			</div>
			<div class="b-rc__admin-buttons b-admin-buttons">
				<div class="b-admin-buttons__block">
					<div class="b-delete-icon" title="Удалить отзыв"></div>
					<div class="b-edit-icon" title="Редактировать"></div>
				</div>
			</div>
		</div>
		
		<div class="b-rc__item__content">
			<div class="b-rc__item__content__author"><a href="<%=author.href%>"><%=author.name%></a></div>
			
			<% if(image.src && image.id) { %>
			<div class="b-rc__item__content__text">
				<div class="b-rc__item__content__text__img">
					<img src="<%=image.src%>" height="65" alt="" class="i-align-img" data-id="<%=image.id%>" />
				</div>
				<div class="b-rc__item__content__text__content"><%=text.html%></div>
				<div class="i-clearfix"></div>
			</div>
			<% } else { %>
			<div class="b-rc__item__content__text">
				<%=text.html%>
			</div>
			<% } %>
			
			<div class="b-rc-edit-form">
				<form action="" method="post">
					<div class="b-form-field b-form__photo-block">
					
						<% if(image.src && image.id) { %>
						<div class="b-admin-buttons">
							<div class="b-admin-buttons__block">
								<div class="b-delete-icon" title="Удалить фото"></div>
								<div class="b-image-icon" title="Загрузить новое фото">
									<div class="b-image-icon__input-file input_file"><input type="file" class="text" id="fileupload<%=id%>" data-ajax-url="/js/file-upload/server/php/comment.php"></div>
									<input type="hidden" name="comment-photo" value="" class="b-image-icon__photo-url" />
								</div>
							</div>
						</div>
						<div class="b-form__photo-block__img">
							<img src="<%=image.src%>" height="65" alt="" class="i-align-img" data-id="<%=image.id%>" />
						</div>
						<% } else { %>
						<div class="input_file"><input type="file" class="text" id="fileupload<%=id%>" data-ajax-url="/js/file-upload/server/php/comment.php"></div>
						<div class="b-form__photo-block__img"></div>
						<input type="hidden" name="comment-photo" value="" class="b-form__photo-block__url" />
						<% } %>
					</div>
					
					<div class="b-form-field b-form__text-block">
						<input type="hidden" value="<%=cId%>" name="cId">
						<input type="hidden" value="<%=rId%>" name="rId">
						<input type="hidden" value="e" name="a">						
						<input id="sessid" type="hidden" value="<%=sessid%>" name="sessid">
						<input type="hidden" value="<%=root%>" name="root">
						<label class="b-form-label">Редактирование отзыва <span class="b-form-attention">?</span></label>
						<div class="b-textarea-wrapper"><textarea name="comment" cols="10" rows="5" class="b-textarea" required><%=text.text%></textarea></div>
						<button class="b-button b-button__width_100" type="submit">Написать отзыв</button>
					</div>
					<div class="i-clearfix"></div>
				</form>
			</div>
			<div class="b-rc__props">
				<div class="b-rc__props__like">
					<span class="b-like">
						<a title="Мне нравится" class="b-like-icon b-like-icon__type-button" href="#" data-ajax-url="/bitrix/components/custom/recipe.comments/like.php"></a>
						<span class="b-like-num"><%=likeNum%></span>
					</span>
				</div>
				<div class="b-rc__props__date"><%=date%></div>
				<div class="i-clearfix"></div>
			</div>
		</div>
	</div>
</script>

<script type="text/template" id="photo-template">
	<div class="b-admin-buttons">
		<div class="b-admin-buttons__block">
			<div class="b-delete-icon" title="Удалить фото"></div>
			<div class="b-image-icon" title="Загрузить новое фото">
				<%
					id = id || "";
					if(type == "edit") id = "fileupload" + id;
					if(type == "reply") id = "fileuploadReply";
					if(type == "comment") id = "fileuploadComment";
				%>
				<div class="b-image-icon__input-file b-rc-<%=type%>-form__input-file input_file"><input type="file" class="text" id="<%=id%>" data-ajax-url="/js/file-upload/server/php/comment.php"></div>
				<input type="hidden" name="comment-photo" value="<%=url%>" class="b-image-icon__photo-url" />
			</div>
		</div>
	</div>
	<div class="b-form__photo-block__img">
		<img src="<%=url%>" height="65" alt="" class="i-align-img" data-id="<%=id%>" />
	</div>
</script>

<script type="text/template" id="file-input-template">
	<%
		id = id || "";
		if(type == "edit") id = "fileupload" + id;
		if(type == "reply") id = "fileuploadReply";
		if(type == "comment") id = "fileuploadComment";
	%>
	<div class="input_file"><input type="file" class="text" id="<%=id%>" data-ajax-url="/js/file-upload/server/php/comment.php"></div>
	<div class="b-form__photo-block__img"></div>
	<input type="hidden" name="comment-photo" value="" class="b-form__photo-block__url" />
</script>

<div id="b-comments">
<?//$frame = $this->createFrame()->begin();?>
<h2 class="b-rc__heading">Отзывы пользователей</h2>
	<a name="comment"></a>
	<div id="b-comment-form">
		<?if(!$USER->IsAuthorized()){?>
		<div class="b-rc__item i-foodclub">
			<div class="i-relative">
				<div class="b-rc__item__pointer"></div>
			</div>
			<div class="b-rc__item__content">
				<div class="b-rc__item__content__text">Если Вы хотите оставить комментарий, Вам необходимо <noindex><a href="/auth/?backurl=<?=$APPLICATION->GetCurPage()?>#add_opinion">авторизоваться</a></noindex> или <noindex><a href="/registration/?backurl=<?=$APPLICATION->GetCurPage()?>#add_opinion">зарегистрироваться</a></noindex> на сайте.</div>
			</div>
		</div>
		<?}else{?>
		<form action="/bitrix/components/custom/recipe.comments/ajax.php" method="post">
			<div class="b-form-field b-form__photo-block">
				<div class="input_file"><input type="file" class="text" id="fileupload-comment" data-ajax-url="/js/file-upload/server/php/comment.php"></div>
				<div class="b-form__photo-block__img"></div>
				<input type="hidden" name="comment-photo" value="" class="b-comment-form__photo__url" />
			</div>
			
			<div class="b-form-field b-comment-form__text">
				<label class="b-form-label">Ваш отзыв <span class="b-form-attention">?</span></label>
				<div class="b-textarea-wrapper"><textarea name="text" cols="10" rows="10" required class="b-textarea"></textarea></div>
				<?=bitrix_sessid_post()?>
				<input type="hidden" name="parentId" value="">
				<input type="hidden" name="a" value="new">
				<input type="hidden" name="recipe" value="<?=$arResult["ID"]?>">
				<?/*?><div class="error_message">&mdash; универсальное сообщение об ошибке!</div><?*/?>
				<button type="submit" class="b-button b-button__width_100">Написать отзыв</button>
			</div>
			<div class="i-clearfix"></div>
		</form>
		<?}?>
	</div>
	<a name="00"></a>
	<div class="b-recipe-comments" data-sessid="<?=str_replace('sessid=','',bitrix_sessid_get())?>" data-edit-form-action="/bitrix/components/custom/recipe.comments/ajax.php" data-edit-form-method="POST"  data-delete-action="/bitrix/components/custom/recipe.comments/ajax.php?a=delete&rId=<?=$arResult["ID"]?>" data-delete-method="POST" data-delete-photo-action="/bitrix/components/custom/recipe.comments/ajax.php" data-delete-photo-method="POST">
		
		<?foreach($arResult["IDS"] as $ID){
		$arComment = $arResult["COMMENTS"][ $ID ];
		if(!empty($arComment)):?>
		<div class="b-rc__block">
			<div class="b-rc__item" data-id="<?=$arComment['ID']?>" id="<?=$arComment['ID']?>">
				<div class="i-relative">
					<a href="#" class="b-rc__item__close b-close-icon b-close-icon__color_3" title="Закрыть"></a>
					<div class="b-rc__item__pointer"></div>
					<div class="b-rc__item__author b-author-avatar">						
						<a href="/profile/<?=$arComment["USER"]["ID"]?>/" class="b-author-avatar__link">
							<img src="<?=$arComment['USER']['SRC']?>" width="100%" height="100%" alt="<?=$arComment['USER']['FULLNAME']?>" class="b-author-avatar__img">
						</a>
					</div>
					<?if($USER->IsAdmin() || $USER->GetID() == $arComment['CREATED_BY']){?><div class="b-rc__admin-buttons b-admin-buttons">
						<div class="b-admin-buttons__block">
							<div class="b-delete-icon" title="Удалить отзыв"></div>
							<div class="b-edit-icon" title="Редактировать"></div>
						</div>
					</div><?}?>
				</div>
				<div class="b-rc__item__content">
					<div class="b-rc__item__content__author"><a href="/profile/<?=$arComment["USER"]["ID"]?>/"><?=$arComment["USER"]["FULLNAME"]?></a></div>
					<div class="b-rc__item__content__text">
						<?if(!empty($arComment["FOODSHOT"])):?>
						<div class="b-rc__item__content__text__img">
							<img src="<?=$arComment["FOODSHOT"]["image"]["src"]?>" height="65" alt="" class="i-align-img" data-id="<?=$arComment["FOODSHOT"]["image"]["id"]?>" />
						</div>
						<div class="b-rc__item__content__text__content"><?=$arComment['PREVIEW_TEXT']?></div>
						<div class="i-clearfix"></div>
						<?else:?>
						<?=$arComment['PREVIEW_TEXT']?>
						<?endif;?>						
					</div>
					<div class="b-rc-edit-form">
						<form action="" method="post">
						<div class="b-form-field b-form__photo-block">
							<?if($USER->IsAdmin() || $USER->GetID() == $arComment['CREATED_BY']){?>
							<?if(!empty($arComment["FOODSHOT"])):?>
							<div class="b-admin-buttons">
								<div class="b-admin-buttons__block">
									<div class="b-delete-icon" title="Удалить фото"></div>
									<div class="b-image-icon" title="Загрузить новое фото">
										<div class="b-image-icon__input-file input_file"><input type="file" class="text" id="fileupload<?=$ID?>" data-ajax-url="/js/file-upload/server/php/comment.php"></div>
										<?if(!empty($arComment["FOODSHOT"])):?>
										<input type="hidden" name="comment-photo" value="<?=$arComment["FOODSHOT"]["image"]["src"]?>" class="b-image-icon__photo-url" />
										<?else:?>
										<input type="hidden" name="comment-photo" value="" class="b-image-icon__photo-url" />
										<?endif;?>
									</div>
								</div>
							</div>
							<?else:?>									
							<div class="input_file"><input type="file" class="text" id="fileupload<?=$rID?>" data-ajax-url="/js/file-upload/server/php/comment.php"></div>
							<div class="b-form__photo-block__img"></div>
							<input type="hidden" name="comment-photo" value="" class="b-form__photo-block__url" />									
							<?endif;?>
							<?}?>
							<?if(!empty($arComment["FOODSHOT"])):?>
							<div class="b-form__photo-block__img">
								<img src="<?=$arComment["FOODSHOT"]["image"]["src"]?>" height="65" alt="" class="i-align-img" data-id="<?=$arComment["FOODSHOT"]["image"]["id"]?>" />
							</div>
							<?endif;?>
						</div>							
							
							<div class="b-form-field b-form__text-block">
								<?=bitrix_sessid_post()?>
								<input type="hidden" name="cId" value="<?=$arComment['ID']?>">
								<input type="hidden" name="rId" value="<?=$arResult["ID"]?>">
								<input type="hidden" name="a" value="e">
								<label class="b-form-label">Редактирование отзыва <span class="b-form-attention">?</span></label>
								<div class="b-textarea-wrapper"><textarea name="comment" cols="10" rows="5" class="b-textarea" required><?=$arComment['~PREVIEW_TEXT']?></textarea></div>
								<button class="b-button b-button__width_100" type="submit">Написать отзыв</button>
							</div>
							<div class="i-clearfix"></div>
						</form>
					</div>
					<div class="b-rc__props">
						<?if($USER->IsAuthorized()){?>
						<div class="b-rc__props__reply">
							<a class="b-button__size_S" href="#">Ответить</a>
						</div>
						<?}?>
						<div class="b-rc__props__like">
							<span class="b-like">
								<?if(CUser::IsAuthorized()):?>
								<a title="Мне нравится" class="b-like-icon b-like-icon__type-button<?=($arResult["USER_LIKES"][ $arComment["ID"] ] ? " b-like-icon__type-active" : "")?>" href="#" data-ajax-url="/bitrix/components/custom/recipe.comments/like.php"></a>
								<?else:?>
								<a title="Мне нравится" class="b-like-icon b-like-icon__type-button<?=($arResult["USER_LIKES"][ $arComment["ID"] ] ? " b-like-icon__type-active" : "")?>" href="/auth/?backurl=<?=$APPLICATION->GetCurDir()?>"></a>
								<?endif;?>
								<span class="b-like-num"><?=$arResult["LIKES"][ $arComment["ID"] ]?></span>
							</span>
						</div>
						<div class="b-rc__props__date"><?=$arComment['DATE_CREATE']?></div>
						<div class="i-clearfix"></div>
					</div>
				</div>
			</div>
			<?if(!empty($arResult["IDS"][ $ID ])):?>
			<div class="b-rc__reply-block">
				<?foreach($arResult["IDS"][ $ID ] as $rID){
				$arReply = $arResult["COMMENTS"][ $rID ];?>
				<?if(!empty($arReply)):?>			
				<div class="b-rc__item" data-id="<?=$rID?>" id="<?=$rID?>">
					<div class="i-relative">
						<a href="#" class="b-rc__item__close b-close-icon b-close-icon__color_3" title="Закрыть"></a>
						<div class="b-rc__item__pointer"></div>
						<div class="b-rc__item__author b-author-avatar">
							<a href="/profile/<?=$arReply["USER"]["ID"]?>/" class="b-author-avatar__link">
								<img src="<?=$arReply['USER']['SRC']?>" width="100%" height="100%" alt="<?=$arReply['USER']['FULLNAME']?>" class="b-author-avatar__img">
							</a>
						</div>
						<?if($USER->IsAdmin() || $USER->GetID() == $arReply['CREATED_BY']){?><div class="b-rc__admin-buttons b-admin-buttons">
							<div class="b-admin-buttons__block">
								<div class="b-delete-icon" title="Удалить отзыв"></div>
								<div class="b-edit-icon" title="Редактировать"></div>
							</div>
						</div><?}?>
					</div>
					
					<div class="b-rc__item__content">
						<div class="b-rc__item__content__author"><a href="/profile/<?=$arReply["USER"]["ID"]?>/"><?=$arReply['USER']['FULLNAME']?></a></div>
						<div class="b-rc__item__content__text">
							<?if(!empty($arReply["FOODSHOT"])):?>
							<div class="b-rc__item__content__text__img">
								<img src="<?=$arReply["FOODSHOT"]["image"]["src"]?>" height="65" alt="" class="i-align-img" data-id="<?=$arReply["FOODSHOT"]["image"]["id"]?>" />
							</div>
							<div class="b-rc__item__content__text__content"><?=$arReply['PREVIEW_TEXT']?></div>
							<div class="i-clearfix"></div>
							<?else:?>
							<?=$arReply['PREVIEW_TEXT']?>
							<?endif;?>							
						</div>
						<div class="b-rc-edit-form">
							<form action="" method="post">
								<div class="b-form-field b-form__photo-block">
									<?if($USER->IsAdmin() || $USER->GetID() == $arReply['CREATED_BY']){?>
									<?if(!empty($arReply["FOODSHOT"])):?>
									<div class="b-admin-buttons">
										<div class="b-admin-buttons__block">
											<div class="b-delete-icon" title="Удалить фото"></div>
											<div class="b-image-icon" title="Загрузить новое фото">
												<div class="b-image-icon__input-file input_file"><input type="file" class="text" id="fileupload<?=$rID?>" data-ajax-url="/js/file-upload/server/php/comment.php"></div>
												<?if(!empty($arReply["FOODSHOT"])):?>
												<input type="hidden" name="comment-photo" value="<?=$arReply["FOODSHOT"]["image"]["src"]?>" class="b-image-icon__photo-url" />
												<?else:?>
												<input type="hidden" name="comment-photo" value="" class="b-image-icon__photo-url" />
												<?endif;?>
											</div>
										</div>
									</div>
									<?else:?>									
									<div class="input_file"><input type="file" class="text" id="fileupload<?=$rID?>" data-ajax-url="/js/file-upload/server/php/comment.php"></div>
									<div class="b-form__photo-block__img"></div>
									<input type="hidden" name="comment-photo" value="" class="b-form__photo-block__url" />									
									<?endif;?>
									<?}?>
									<?if(!empty($arReply["FOODSHOT"])):?>
									<div class="b-form__photo-block__img">
										<img src="<?=$arReply["FOODSHOT"]["image"]["src"]?>" height="65" alt="" class="i-align-img" data-id="<?=$arReply["FOODSHOT"]["image"]["id"]?>" />
									</div>
									<?endif;?>
								</div>
								
								<div class="b-form-field b-form__text-block">
									<?=bitrix_sessid_post()?>
									<input type="hidden" name="cId" value="<?=$rID?>">
									<input type="hidden" name="rId" value="<?=$arResult["ID"]?>">
									<input type="hidden" name="a" value="e">
									<input type="hidden" name="root" value="<?=$arComment['ID']?>">
									<label class="b-form-label">Редактирование отзыва <span class="b-form-attention">?</span></label>
									<div class="b-textarea-wrapper"><textarea name="comment" cols="10" rows="5" class="b-textarea" required><?=$arReply['~PREVIEW_TEXT']?></textarea></div>
									<button class="b-button b-button__width_100" type="submit">Написать отзыв</button>
								</div>
								<div class="i-clearfix"></div>
							</form>
						</div>
						<div class="b-rc__props">
							<div class="b-rc__props__like">
								<span class="b-like">									
									<?if(CUser::IsAuthorized()):?>
									<a title="Мне нравится" class="b-like-icon b-like-icon__type-button<?=($arResult["USER_LIKES"][ $arReply["ID"] ] ? " b-like-icon__type-active" : "")?>" href="#" data-ajax-url="/bitrix/components/custom/recipe.comments/like.php"></a>
									<?else:?>
									<a title="Мне нравится" class="b-like-icon b-like-icon__type-button<?=($arResult["USER_LIKES"][ $arReply["ID"] ] ? " b-like-icon__type-active" : "")?>" href="/auth/?backurl=<?=$APPLICATION->GetCurDir()?>"></a>
									<?endif;?>
									<span class="b-like-num"><?=$arResult["LIKES"][ $arReply["ID"] ]?></span>
								</span>
							</div>
							<div class="b-rc__props__date"><?=$arReply['DATE_CREATE']?></div>
							<div class="i-clearfix"></div>
						</div>
					</div>
				</div>			
				<?endif;?>
				<?}?>
			</div>
			<?endif;?>
		</div>
		<?endif;?>
		<?}?>
		<?if($USER->IsAuthorized()){?>
		<div id="b-rc__reply-form">
            <div class="i-relative"><a href="#" class="b-close-icon b-rc__reply-form__close b-close-icon__color_3"></a></div>
            <form action="/bitrix/components/custom/recipe.comments/ajax.php" method="post">
                <div class="b-form-field b-form__photo-block">
                    <div class="input_file"><input type="file" class="text" id="fileuploadReply" data-ajax-url="/js/file-upload/server/php/comment.php"></div>
                    <div class="b-form__photo-block__img"></div>
                    <input type="hidden" name="comment-photo" value="" class="b-form__photo-block__url" />
                </div>
                
                <div class="b-form-field b-form__text-block">
                	<?=bitrix_sessid_post()?>
                    <input type="hidden" name="parentId" value="">
					<input type="hidden" name="a" value="new">
					<input type="hidden" name="recipe" value="<?=$arResult["ID"]?>">
					<input type="hidden" name="rId" value="">
                    <label class="b-form-label">Ответ на отзыв <span class="b-form-attention">?</span></label>
                    <div class="b-textarea-wrapper"><textarea name="text" cols="10" rows="10" required class="b-textarea"></textarea></div>
                    <button class="b-button b-button__width_100" type="submit">Ответить</button>
                </div>
                <div class="i-clearfix"></div>
            </form>            
        </div>
        <?}?>
	</div>
</div>
<?//$frame->end();?>