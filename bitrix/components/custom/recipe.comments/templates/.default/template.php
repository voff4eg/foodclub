<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<script type="text/template" id="comment-template">
<div class="block">
	<div class="comment" id="<%=id%>">
		<div class="icons">
			<div class="close_icon" title="Закрыть"></div>
			<div class="pointer"></div>
			<div class="photo"><div class="big_photo"><div><img src="<%=author.pic%>" width="100" height="100" alt="<%=author.name%>"></div></div><img src="<%=author.pic%>" width="30" height="30" alt="<%=author.name%>"></div>
			<div class="right">
				<div class="delete" title="Удалить"></div>
				<div class="edit" title="Редактировать"></div>
			</div>
		</div>
		<div class="padding">
			<div class="comment_author"><a href="<%=author.url%>"><%=author.name%></a></div>
			<div class="text">
				<%=text.html%>
			</div>
			<form action="/comment.php" name="edit_comment" method="post">
				<div class="textarea"><textarea name="comment" cols="10" rows="5">
<%=text.text%>
				</textarea></div>
				<div class="button">Редактировать</div>
			</form>
			<div class="properties">
				<div class="reply_string"><a href="#">Ответить</a></div>
				<div class="date"><%=date%></div>
			</div>
		</div>
	</div>
	<% if(reply) { %>
	<div class="reply_block">
		<div class="comment" id="<%=reply.id%>">
			<div class="icons">
				<div class="close_icon" title="Закрыть"></div>
				<div class="pointer"></div>
				<div class="photo"><div class="big_photo"><div><img src="<%=reply.author.pic%>" width="100" height="100" alt="<%=reply.author.name%>"></div></div><img src="<%=reply.author.pic%>" width="30" height="30" alt="<%=reply.author.name%>"></div>
				<div class="right">
					<div class="delete" title="Удалить"></div>
					<div class="edit" title="Редактировать"></div>
				</div>
			</div>
			<div class="padding">
				<div class="comment_author"><a href="<%=reply.author.url%>"><%=reply.author.name%></a></div>
				<div class="text">
					<%=reply.text.html%>
				</div>
				<form action="/comment.php" name="edit_comment" method="post">
					<div class="textarea"><textarea name="comment" cols="10" rows="5">
<%=reply.text.text%>
					</textarea></div>
					<div class="button">Редактировать</div>
				</form>
				<div class="properties">
					<div class="reply_string"><a href="#">Ответить</a></div>
					<div class="date"><%=reply.date%></div>
				</div>
			</div>
		</div>
	</div>
	<% } %>
</div>
</script>
<?//echo "<pre>";print_r($arResult["IDS"]);echo "</pre>";?>
<?//echo "<pre>";print_r($arResult["COMMENTS"]);echo "</pre>";?>
<a name="00"></a>
<div class="comments_block">
	<h2>Отзывы пользователей</h2>	
	<div id="sessId">5678</div>
	<div id="reply_form">
		<div class="close_icon"><div></div></div>
		<form action="/comment.php" method="post" name="comment">
			<div class="form_field">
				<h4>Ответ на отзыв <span>?</span></h4>
				<div class="textarea"><textarea name="text" cols="10" rows="10"></textarea></div>
				<input type="hidden" name="parentId" value="">
				<input type="hidden" name="a" value="new">
				<input type="hidden" name="recipe" value="<?=$arResult["ID"]?>">
				<input type="submit" class="button" value="Ответить" />
			</div>
		</form>		
	</div>
	<?if(is_array($arResult["IDS"])):?>
	<?foreach($arResult["IDS"] as $ID){
	if(intval($ID) >= 0){
		if(is_array($arResult["COMMENTS"]) && is_array($arResult["COMMENTS"][ (int) $ID ])){
			$arComment = $arResult["COMMENTS"][ $ID ];
		}else{
			$arComment = array();
		}
	}	
	if(!empty($arComment)):?>
	<div class="block">
		<div class="comment" id="root<?=$arComment['ID']?>_id">
			<a name="<?=$ID?>"></a>
			<div class="icons">
				<div class="close_icon" title="Закрыть"></div>
				<div class="pointer"></div>
				<div class="photo"><div class="big_photo"><div><img src="<?=$arComment['USER']['SRC']?>" width="100" height="100" alt="<?=$arComment['USER']['FULLNAME']?>"></div></div><img src="<?=$arComment['USER']['SRC']?>" width="30" height="30" alt="<?=$arComment['USER']['FULLNAME']?>"></div>
				<?if($USER->IsAdmin() || $USER->GetID() == $arComment['CREATED_BY']){?><div class="right">
					<div class="delete" title="Удалить"></div>
					<div class="edit" title="Редактировать"></div>
				</div><?}?>
			</div>
			<div class="padding">
				<div class="comment_author"><a href="/profile/<?=$arComment["USER"]["ID"]?>/"><?=$arComment["USER"]["FULLNAME"]?></a></div>
				<div class="text">
					<?=$arComment['PREVIEW_TEXT']?>
				</div>
				<form action="/comment.php" name="edit_comment" method="post">
					<input type="hidden" name="cId" value="<?=$arComment['ID']?>">
					<input type="hidden" name="rId" value="<?=$arResult["ID"]?>">
					<input type="hidden" name="a" value="e">
					<div class="textarea"><textarea name="comment" cols="10" rows="5"><?=$arComment['~PREVIEW_TEXT']?></textarea></div>
					<div class="button">Редактировать</div>
				</form>
				<div class="properties">
					<?if($arParams["CREATED_BY"] == $USER->GetID()):?><div class="reply_string"><a href="#">Ответить</a></div><?endif;?>
					<div class="date"><?=$arComment['DATE_CREATE']?></div>
				</div>
			</div>
		</div>
		<?if(is_array($arResult["IDS"][ $ID ])):?>
		<?foreach($arResult["IDS"][ $ID ] as $rID){
		$arReply = $arResult["COMMENTS"][ $rID ];?>
		<?if(!empty($arReply)):?>
		<div class="reply_block">
			<div class="comment" id="reply<?=$rID?>_id">
				<a name="<?=$rID?>"></a>
				<div class="icons">
					<div class="close_icon" title="Закрыть"></div>
					<div class="pointer"></div>
					<div class="photo"><div class="big_photo"><div><img src="<?=$arReply['USER']['SRC']?>" width="100" height="100" alt="<?=$arReply['USER']['FULLNAME']?>"></div></div><img src="<?=$arReply['USER']['SRC']?>" width="30" height="30" alt="<?=$arReply['USER']['FULLNAME']?>"></div>
					<div class="right">
						<?if($USER->IsAdmin() || $USER->GetID() == $arReply['CREATED_BY']){?><div class="delete" title="Удалить"></div>
						<div class="edit" title="Редактировать"></div><?}?>
					</div>
				</div>
				<div class="padding">
					<div class="comment_author"><a href="/profile/<?=$arReply["USER"]["ID"]?>/"><?=$arReply['USER']['FULLNAME']?></a></div>
					<div class="text">
						<?=$arReply['PREVIEW_TEXT']?>
					</div>
					<form action="/comment.php" name="edit_comment" method="post">
						<input type="hidden" name="cId" value="<?=$rID?>">
						<input type="hidden" name="rId" value="<?=$arResult["ID"]?>">
						<input type="hidden" name="a" value="e">
						<input type="hidden" name="root" value="<?=$arComment['ID']?>">
						<div class="textarea"><textarea name="comment" cols="10" rows="5"><?=$arReply['~PREVIEW_TEXT']?></textarea></div>
						<div class="button">Редактировать</div>
					</form>
					<div class="properties">
						<!-- <div class="reply_string"><a href="#">Ответить</a></div> -->
						<div class="date"><?=$arReply['DATE_CREATE']?></div>
					</div>
				</div>
			</div>
		</div>
		<?endif;?>
		<?}?>
		<?endif;?>
	</div>
	<?endif;?>
	<?}?>
	<?endif;?>
	<!-- <div class="b-more-button">
		<a class="b-button" href="#" data-pages="5">Ещё</a>
	</div> -->	
</div>
<a name="comment"></a>
<div id="comment_form">
	<?if(!$USER->IsAuthorized()){?>
	<div class="comment foodclub">
		<div class="icons">
			<div class="pointer"></div>
		</div>
		<div class="padding">
			<div class="text">Если Вы хотите оставить комментарий, Вам необходимо <noindex><a href="/auth/?backurl=<?=$APPLICATION->GetCurPage()?>#add_opinion">авторизоваться</a></noindex> или <noindex><a href="/registration/?backurl=<?=$APPLICATION->GetCurPage()?>#add_opinion">зарегистрироваться</a></noindex> на сайте.</div>
		</div>
	</div>
	<?}else{?>
	<form action="/comment.php" method="post">
		<div class="form_field">
			<h4>Ваш отзыв <span>?</span></h4>
			<div class="textarea"><textarea name="text" cols="10" rows="10"></textarea></div>
			<input type="hidden" name="parentId" value="">
			<input type="hidden" name="a" value="new">
			<input type="hidden" name="recipe" value="<?=$arResult["ID"]?>">
			<!-- <div class="error_message">&mdash; универсальное сообщение об ошибке!</div> -->
			<div class="button">Написать отзыв</div>
		</div>
	</form>
	<?}?>
</div>