<?if(isset($_REQUEST["_escaped_fragment_"])){
	if(intval($_REQUEST["id"])){
		include($_SERVER["DOCUMENT_ROOT"]."/foodshot/html/".intval($_REQUEST["id"])."/index.html");
	}else{
		include($_SERVER["DOCUMENT_ROOT"]."/foodshot/index.html");
	}	
}else{
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("keywords", "Foodstyling, Food design, фуд фотография, фуд дизайн, фуд фото");
$APPLICATION->SetPageProperty("description", "Быстрый и простой способ добавить рецепт.");
$APPLICATION->SetTitle("Фудшот — красивые фотографии еды и простые рецепты");?>
<script type="text/javascript" src="/js/form.js"></script>
<script type="text/javascript" src="/js/history.js"></script>
<script type="text/javascript" src="/foodshot/foodshot.js?<?=filectime($_SERVER["DOCUMENT_ROOT"]."/foodshot/foodshot.js")?>"></script>
<script type="text/javascript" src="/foodshot/script.js"></script>
<script type="text/javascript" src="/foodshot/jquery.fileupload-ui.js"></script>
<script type="text/javascript">
(function(d){
  var f = d.getElementsByTagName('SCRIPT')[0], p = d.createElement('SCRIPT');
  p.type = 'text/javascript';
  p.async = true;
  p.src = '//assets.pinterest.com/js/pinit.js';
  f.parentNode.insertBefore(p, f);
}(document));
</script>
<?
$APPLICATION->AddHeadScript('/js/elem.js');
$APPLICATION->SetAdditionalCSS("/css/form.css",true);
$APPLICATION->SetAdditionalCSS("/foodshot/foodshot.css",true);
?>
<h1><span class="b-h1-heading">Фудшот</span> <span class="b-h1-choice">
<?
if(intval($_REQUEST["id"]) > 0){
	require_once($_SERVER['DOCUMENT_ROOT']."/classes/foodshot.class.php");
	$CFooshot = CFoodshot::getInstance();
	if($arFoodshot = $CFooshot->getByID(intval($_REQUEST["id"]))){		
		$APPLICATION->AddHeadString('<meta property="og:title" content="'.$arFoodshot["name"].'">');
		$APPLICATION->AddHeadString('<meta property="og:type" content="food">');
		if(strlen($arFoodshot["image"]["src"]) > 0){
			$APPLICATION->AddHeadString('<meta property="og:image" content="http://'.$_SERVER["SERVER_NAME"].$arFoodshot["image"]["src"].'">');
		}
		$APPLICATION->AddHeadString('<meta property="og:url" content="http://'.$_SERVER["SERVER_NAME"].'/foodshot/'.intval($_REQUEST["id"]).'/">');
		$APPLICATION->AddHeadString('<meta property="og:site_name" content="'.$APPLICATION->GetTitle().'">');
		$APPLICATION->AddHeadString('<meta property="og:description" content="'.addslashes(strip_tags($arFoodshot["description"]["text"])).'">');

	}
}
//echo "<!--@";print_r($_SERVER);echo "@-->";
?>
<?if(CUser::IsAUthorized()):?><a href="/foodshot/add/" class="b-h1-choice__item b-h1-choice__add-foodshot" id="add-foodshot-button">Добавить фудшот</a><?endif;?></span><div class="i-clearfix"></div></h1>
<script type="text/html" id="foodshot-detail-template">
<div id="foodshotDetail" class="b-foodshot-detail">
	<div class="b-foodshot-detail__close"><a href="#" class="b-close-icon" title="Закрыть"></a></div>

	<% var imgWidth = image.width;
		if(imgWidth > 600) {
			imgWidth = 600;
		}
	%>
	<% var imgHeight = Math.floor((image.height * imgWidth) / image.width); %>
	<div class="b-foodshot-detail__image">
		<img src="<%=image.src%>" width="<%=imgWidth%>" height="<%=imgHeight%>" alt="<%=name%>">
	</div>
	<% if(deleteIcon) { %>
	<div class="b-foodshot-detail__admin-buttons b-admin-buttons">
		<div class="b-admin-buttons__block">
			<div class="b-delete-icon" title="Удалить фудшот"></div>
			<div class="b-edit-icon" title="Редактировать"></div>
		</div>
	</div>
	<% } %>
	<div class="b-foodshot-detail__like">
		<span class="b-like">
			<a href="#" class="b-like-icon b-like-icon__type-button<% if(user_liked) { %> b-like-icon__type-active<% } %>" title="Мне нравится"></a>
			<span class="b-like-num"><%=likeNum%></span>
		</span>
		<span class="b-foodshot-detail__like__item i-vkontakte"></span>
		<span class="b-foodshot-detail__like__item i-facebook"></span>
		<span class="b-foodshot-detail__like__item i-twitter"></span>
		<span class="b-foodshot-detail__like__item i-pinterest"></span>
	</div>
	<div class="b-foodshot-detail__description b-comment b-comment__type-big-userpic">
		<div class="b-comment__userpic">
			<a href="<%=description.author.href%>" class="b-userpic">
				<span class="b-userpic__layer"></span>
				<img src="<%=description.author.src%>" width="100" height="100" alt="<%=description.author.name%>" class="b-userpic__image">
			</a>			
		</div>
		<div class="b-comment__content">
			<div class="b-comment__author">
				<a href="<%=description.author.href%>"><%=description.author.name%></a>
			</div>
			<div class="b-comment__text"><%=description.text%></div>
			
			<% if(description.source != "") { %>
			
			<div class="b-source b-foodshot-detail__description__source">
				Источник: <a href="<%=description.source%>" target="_blank"><%=description.source%></a>
			</div>
			
			<% } %>
			
		</div>
		<div class="i-clearfix"></div>
	</div>
	
	<% if(comments && comments.length > 0) { %>
				
	<div class="b-foodshot-detail__comments b-comment-block-list">
	
		<% for(var i = 0; i < comments.length; i++) { %>
		<div class="b-comment-block<% if(window.userObject && userObject.id == comments[i].author.id) { %> i-mine<% } %> b-comment__user" data-id="<%=comments[i].id%>">
			<div class="i-relative">
				<div class="b-comment-block__pointer"></div>
				<a href="<%=comments[i].author.href%>" class="b-userpic b-comment-block__userpic">
					<img src="<%=comments[i].author.src%>" width="30" height="30" alt="<%=comments[i].author.name%>" class="b-userpic__image">
				</a>
				<% if(window.userObject && (userObject.isAdmin || userObject.id == comments[i].author.id)) { %>
				<div class="b-comment-block__admin-panel b-admin-panel">
					<a href="" class="b-admin-panel__delete" title="Удалить"></a>
				</div>
				<% } %>
			</div>
			
			<div class="b-comment-block__content">
				<div class="b-comment-block__author">
					<a href="<%=comments[i].author.href%>"><%=comments[i].author.name%></a>
				</div>
				<div class="b-comment-block__text">
					<%=comments[i].text%>
				</div>
				<div class="b-comment-block__date"><%=comments[i].date%></div>
			</div>
			<div class="i-clearfix"></div>
		</div>
		<% } %>
		
	</div>
	<% } %>
	
	<% if(window.userObject) { %>
	<div class="b-form-comments">
		<form action="" method="get">
			<div class="b-form-field b-form-field__type-comment b-comment">
				<a href="<%=userObject.href%>" class="b-comment__userpic b-userpic">
					<span class="b-userpic__layer"></span>
					<img src="<%=userObject.src%>" width="30" height="30" alt="<%=userObject.name%>" class="b-userpic__image">
				</a>
				<div class="b-comment__content">
					<textarea cols="30" rows="3" name="comment" class="b-form-field__textarea b-foodshot-detail__comments__textarea" required></textarea>
				</div>
				<div class="i-clearfix"></div>
			</div>
			<div class="b-button b-form-field__type-comment__button">Комментировать</div>
			<div class="i-clearfix"></div>
		</form>
	</div>
	<% } %>
</div>
</script>

<?
if ($USER->IsAUthorized())
{
	$curUser = CUser::GetByID($USER->GetID())->Fetch();
	if (intval($curUser["PERSONAL_PHOTO"]) > 0)
		$curUserPhoto = CFile::ResizeImageGet($curUser["PERSONAL_PHOTO"], array('width'  => "30", 'height' => "30"), BX_RESIZE_IMAGE_EXACT, true, false);
	else
		$curUserPhoto["src"] = "/images/avatar/avatar_small.jpg";
}
?>

<div id="foodshotBoard" class="b-foodshot-board">
	<?if ($USER->IsAUthorized()){?>
	<script type="text/javascript">
		var userObject = {
			"id": "<?=$USER->GetID()?>",
			"href": "http://www.foodclub.ru/profile/<?=$USER->GetID()?>/",
			"src": "http://<?=$_SERVER["HTTP_HOST"]?><?=$curUserPhoto["src"]?>",
			"name": "<?=$USER->GetFullName()?>",			
		};
		<?if($USER->IsAdmin()):?>
		userObject.isAdmin = "yes";
		<?endif;?>
	</script>
	<?}?>
	<script type="text/html" id="foodshot-details-template">
	</script>
	<?if(isset($_GET['_escaped_fragment_'])){
		echo file_get_contents("foodshot-list.html");
	}else{?>
	<script type="text/html" id="foodshot-template">
	<div class="b-foodshot-board__item" data-id="<%=id%>">
		<div class="b-foodshot-board__item-content">
			<% var imgWidth = 170; %>
			<% var imgHeight = Math.floor((image.height * imgWidth) / image.width); %>
			<a href="<%=href%>" class="b-foodshot-board__item-content-image" style="height: <%=imgHeight%>px;"><img src="<%=image.src%>" width="<%=imgWidth%>" height="<%=imgHeight%>" alt="<%=name%>"></a>
			<div class="b-foodshot-board__item-content-text"><%=text%></div>
			<div class="b-recipe-author b-foodshot-board__item-content-author">от <%=author.name%></div>
		</div>
		
		<% if(comments.num > 0) { %>
		<div class="b-foodshot-board__item-comments">
		
			<% if(comments.num > comments.visible.length) { %>
			<% var counter = comments.num - comments.visible.length; %>
			<div class="b-foodshot-board__item-comments-hidden">
				<a href="#" class="b-foodshot-board__item-comments-hidden__button">
					<span class="b-comment-icon b-foodshot-board__item-comments-hidden__icon"><%=counter%></span>
				</a>
			</div>
			<% } %>
			
			<div class="b-comment-list">
				<% for(var i = 0; i < comments.visible.length; i++) { %>
					<div class="b-comment b-comment__type-short b-foodshot-board__item-comment" data-id="<%=comments.visible[i].id%>">
						<a href="<%=comments.visible[i].author.href%>" class="b-comment__userpic b-userpic">
							<span class="b-userpic__layer"></span>
							<img src="<%=comments.visible[i].author.src%>" width="30" height="30" alt="<%=comments.visible[i].author.name%>" class="b-userpic__image">
						</a>
						<div class="b-comment__content">
							<div class="b-comment__author">
								<a href="<%=comments.visible[i].author.href%>"><%=comments.visible[i].author.name%></a>
							</div>
							<div class="b-comment__text"><%=comments.visible[i].text%></div>
						</div>
						<div class="i-clearfix"></div>
					</div>
				<% } %>
			</div>
			
		</div>
		<% } %>
		
		
		<div class="b-foodshot-board__item-action">
			<% if(window.userObject) { %>
			<div class="b-foodshot-board__item-action_comment_hidden b-form-comments">
				<form action="" method="get">
					<div class="b-form-field b-comment b-comment__type-short b-form-field__type-comment">
						<a href="<%=userObject.href%>" class="b-comment__userpic b-userpic">
							<span class="b-userpic__layer"></span>
							<img src="<%=userObject.src%>" width="30" height="30" alt="<%=userObject.name%>" class="b-userpic__image">
						</a>
						<div class="b-comment__content">
							<textarea cols="30" rows="3" name="comment" required></textarea>
						</div>
						<div class="i-clearfix"></div>
					</div>
					<a href="#" class="b-form-field__type-comment__button i-frame-bg">
						<span class="i-frame-bg_left">
							<span class="i-frame-bg_right">
								<span class="i-frame-bg_bg">
									<span class="i-frame-bg_content">Комментировать</span>
								</span>
							</span>
						</span>
					</a>
					<div class="i-clearfix"></div>
				</form>
			</div>
			<% } %>
			<div class="b-foodshot-board__item-action_like">
				<span class="b-like">
					<a href="#" class="b-like-icon b-like-icon__type-button<% if(user_liked) { %> b-like-icon__type-active<% } %>" title="Мне нравится"></a>
					<span class="b-like-num"><%=likeNum%></span>
				</span>
			</div>
			<% if(window.userObject) { %>
			<div class="b-foodshot-board__item-action_comment_visible">
				<a href="#" class="b-comment-icon b-comment-icon__type-button" title="Комментировать"></a>
			</div>
			<% } %>
			<div class="i-clearfix"></div>
		</div>
		
	</div>
	</script>

	<?}?>
	
	<script type="text/html" id="foodshot-comment-template">
		<div class="b-comment b-comment__type-short b-foodshot-board__item-comment" data-id="<%=id%>">
			<a href="<%=author.href%>" class="b-comment__userpic b-userpic">
				<span class="b-userpic__layer"></span>
				<img src="<%=author.src%>" width="30" height="30" alt="<%=author.name%>" class="b-userpic__image">
			</a>
			<div class="b-comment__content">
				<div class="b-comment__author">
					<a href="<%=author.href%>"><%=author.name%></a>
				</div>
				<div class="b-comment__text"><%=text%></div>
			</div>
			<div class="i-clearfix"></div>
		</div>
	</script>

	<script type="text/html" id="foodshot-detail-comment-template">
		<div class="b-comment-block i-mine b-comment__user" data-id="<%=id%>">
			<div class="i-relative">
				<div class="b-comment-block__pointer"></div>
				<a class="b-userpic b-comment-block__userpic" href="<%=author.href%>">
					<img height="30" width="30" class="b-userpic__image" alt="<%=author.name%>" src="<%=author.src%>">
				</a>
				
				<div class="b-comment-block__admin-panel b-admin-panel">
					<a title="Удалить" class="b-admin-panel__delete" href=""></a>
				</div>
				
			</div>
			
			<div class="b-comment-block__content">
				<div class="b-comment-block__author">
					<a href="<%=author.href%>"><%=author.name%></a>
				</div>
				<div class="b-comment-block__text">
					<%=text%>
				</div>
				<div class="b-comment-block__date"><%=date%></div>
			</div>
			
			<div class="i-clearfix"></div>
		</div>
		
	</script>
</div>

<?$APPLICATION->IncludeFile("/foodshot/add_foodshot.php", Array());?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
<?}?>