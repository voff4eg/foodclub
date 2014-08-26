<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
?>
<?$APPLICATION->SetAdditionalCSS("/css/profile.css");
$APPLICATION->SetAdditionalCSS("/css/elem.css");?>
<?

//print_r($_REQUEST);

//echo $USER->GetID();die;

if (CModule::IncludeModule("advertising")){ $strBanner = CAdvBanner::Show("right_banner"); }

/*if(strpos($_REQUEST['place'], "pr_recipe") !== false){
    if($USER->IsAuthorized())
    {
        $UserId = IntVal($USER->GetId());
    }
    else 
    {
	    LocalRedirect("/auth/?backurl=/profile/recipes/");
    }
} else {
	$UserId = IntVal($_REQUEST['u']);
}*/

/*if(intval($_REQUEST["u"]) > 0){
	$UserId = IntVal($_REQUEST['u']);
}else{
	if(CUSer::IsAuthorized()){
		$UserId = IntVal($USER->GetId());
	}else{
		LocalRedirect("/auth/?backurl=".$APPLICATION->GetCurPage());
	}	
}*/

$arUser = $APPLICATION->IncludeComponent("custom:profile", "", Array());

$APPLICATION->SetPageProperty("title", $arUser['FULLNAME']." &mdash; рецепты пользователя на Foodclub");
?>
<div id="content">
	<div class="b-personal-page">
	<?$APPLICATION->IncludeFile("/personal/.profile_header.php", Array(
		"USER" => $arUser)
	);?>
	
	<?$APPLICATION->IncludeComponent(
		"custom:profile_menu",
		"",
		Array(
			"ROOT_MENU_TYPE" => "profile",
			"MAX_LEVEL" => "1",
			"CHILD_MENU_TYPE" => "profile",
			"USE_EXT" => "N",
			"DELAY" => "N",
			"ALLOW_MULTI_SELECT" => "N",
			"MENU_CACHE_TYPE" => "N",
			"MENU_CACHE_TIME" => "3600",
			"MENU_CACHE_USE_GROUPS" => "Y",
			"MENU_CACHE_GET_VARS" => ""
		),
	false
	);?>

	<div id="text_space">
		<?if($USER->IsAuthorized()) $curUser = $USER->GetID();?>
		<h3 class="b-hr-bg b-personal-page__heading">
			<span class="b-hr-bg__content"><?=( (!$_REQUEST["u"] || ($_REQUEST["u"] && $curUser && $curUser == $_REQUEST["u"])) ? "Мои фудшоты" : "Фудшоты");?></span>
		</h3>
		
		<script>
			var profileOwnerId = "<?echo ($_REQUEST["u"] ? $_REQUEST["u"] : $curUser);?>";
		</script>

		<script type="text/javascript" src="/js/form.js"></script>
		<script type="text/javascript" src="/js/history.js"></script>
		<script type="text/javascript" src="/foodshot/foodshot.js"></script>
		<script type="text/javascript" src="/foodshot/script.js"></script>
		<script type="text/javascript" src="/foodshot/jquery.fileupload-ui.js"></script>
		<link rel="stylesheet" type="text/css" href="/css/form.css">
		<link rel="stylesheet" type="text/css" href="/foodshot/foodshot.css">

		<script type="text/html" id="foodshot-detail-template">
		<div id="foodshotDetail" class="b-foodshot-detail">
			<div class="b-foodshot-detail__close"><a href="#" class="b-close-icon" title="Закрыть"></a></div>
			<% var imgWidth = image.width;
				if(imgWidth > 600) {
					imgWidth = 600;
				}
			%>
			<% var imgHeight = Math.floor((image.height * imgWidth) / image.width); %>
			<div class="b-foodshot-detail__image" style="height: <%=imgHeight%>px;">
				<img src="<%=image.src%>" width="<%=imgWidth%>" height="<%=imgHeight%>" alt="<%=name%>">
			</div>
			<div class="b-foodshot-detail__like">
				<div class="b-foodshot-detail__like__panel">
					<div class="b-foodshot-detail__like__item"></div>
				</div>
			</div>
			<div class="b-foodshot-detail__description b-comment b-comment__type-big-userpic">
				<div class="b-comment__userpic">
					<a href="<%=description.author.href%>" class="b-userpic">
						<span class="b-userpic__layer"></span>
						<img src="<%=description.author.src%>" width="100" height="100" alt="<%=description.author.name%>" class="b-userpic__image">
					</a>
					<div class="b-like">
						<a href="#" class="b-like-icon b-like-icon__type-button<% if(user_liked) { %> b-like-icon__type-active<% } %>" title="Мне нравится"></a>
						<span class="b-like-num"><%=likeNum%></span>
					</div>
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
						
			<div class="b-foodshot-detail__comments b-comment-list">
			
				<% for(var i = 0; i < comments.length; i++) { %>
				<div class="b-comment b-comment__user" data-id="<%=comments[i].id%>">
					<a href="<%=comments[i].author.href%>" class="b-userpic b-comment__userpic">
						<span class="b-userpic__layer"></span>
						<img src="<%=comments[i].author.src%>" width="30" height="30" alt="<%=comments[i].author.name%>" class="b-userpic__image">
					</a>
					
					<% if(window.userObject && (userObject.isAdmin || userObject.id) == comments[i].author.id) { %>
					<div class="b-comment__admin-panel b-admin-panel">
						<a href="" class="b-admin-panel__delete" title="Удалить"></a>
					</div>
					<% } %>
					
					<div class="b-comment__content">
						<div class="b-comment__author">
							<a href="<%=comments[i].author.href%>"><%=comments[i].author.name%></a>
						</div>
						<div class="b-comment__text">
							<%=comments[i].text%>
						</div>
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
					"href": "http://site.foodclub.2px/profile/<?=$USER->GetID()?>/",
					"src": "http://<?=$_SERVER["HTTP_HOST"]?><?=$curUserPhoto["src"]?>",
					"name": "<?=$USER->GetFullName()?>"
				};
			</script>
			<?}?>
			<script type="text/html" id="foodshot-details-template">
			</script>
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
					<div class="b-foodshot-board__item-comments-hidden">
						<a href="#" class="b-foodshot-board__item-comments-hidden__button">
							<span class="b-comment-icon b-foodshot-board__item-comments-hidden__icon"><%=comments.num%></span>
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
							<% if(!window.userObject) { %>
							<span class="b-like-icon b-like-icon__type-button b-like-icon__type-disabled" title="Понравилось"></span>
							<% } else { %>
							<a href="#" class="b-like-icon b-like-icon__type-button<% if(user_liked) { %> b-like-icon__type-active<% } %>" title="Мне нравится"></a>
							<% } %>
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
				<div class="b-comment b-comment__user" data-id="<%=id%>">
					<a class="b-userpic b-comment__userpic" href="<%=author.href%>"><span class="b-userpic__layer b-userpic__layer__30"></span>
						<span class="b-userpic__layer"></span>
						<img height="30" width="30" class="b-userpic__image" alt="<%=author.name%>" src="<%=author.src%>">
					</a>
					
					
					<div class="b-comment__admin-panel b-admin-panel">
						<a title="Удалить" class="b-admin-panel__delete" href="#"></a>
					</div>
					
					
					<div class="b-comment__content">
						<div class="b-comment__author">
							<a href="<%=author.href%>"><%=author.name%></a>
						</div>
						<div class="b-comment__text">
							<%=text%>
						</div>
					</div>
					<div class="i-clearfix"></div>
				</div>
				
			</script>
		</div>

		<?$APPLICATION->IncludeFile("/foodshot/add_foodshot.php", Array());?>

	</div>

	<div id="banner_space">
		<?if(strlen($strBanner) > 0){?><div class="banner"><?=$strBanner?></div><?}?>
	</div>
	<div class="clear"></div>
	</div>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
