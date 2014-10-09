<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$APPLICATION->AddHeadScript($this->GetFolder()."/script.js");?>
<?if($USER->IsAuthorized()):
	$strUserObj = '<script type="text/javascript">
	var userObject = {
		"id": "'.$arResult["user"]["ID"].'",
		"href": "http://www.foodclub.ru/profile/'.$arResult["user"]["ID"].'/",
		"src": "'.$arResult["user"]["PERSONAL_PHOTO"]["src"].'",
		"name": "'.$arResult["user"]["NAME"]." ".$arResult["user"]["LAST_NAME"].'"
	};';
	if($USER->isAdmin()){
		$strUserObj .= 'userObject.isAdmin = "yes";';
	}
	$strUserObj .= '</script>';
	$APPLICATION->AddHeadString($strUserObj);?>	
<?endif;?>
<?$this->createFrame()->begin('<img src="/images/preloader.gif" width="100%" alt="">');?>
<link rel="stylesheet" type="text/css" href="/foodshot/foodshot.css?135037931126807">
<?$curDir = $APPLICATION->GetCurDir();?>
<div class="b-last-foodshot <?if($curDir!="/"):?>b-collection-block b-collection-block__type_top-border<?endif;?>">
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
	
	<?if($curDir == "/"):?>
		<h2><a href="/foodshot/#!foodshot">Свежий фудшот</a></h2>
	<?else:?>
		<div class="b-collection-block__heading">
			<a href="/foodshot/#!foodshot" class="b-collection-block__heading__content">Свежие фудшоты</a>
		</div>
	<?endif;?>

	<?foreach($arResult["ITEMS"] as $arItem):?>
		<?
		$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
		$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
		?>

		<div data-id="<?=$arItem["ID"]?>" class="b-foodshot-board__item b-foodshot-board__item__type_static" id="<?=$this->GetEditAreaId($arItem['ID']);?>">

		  <div class="b-foodshot-board__item-content">
			<?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arItem["PREVIEW_PICTURE"])):?>
				<?if(!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])):?>
					<a class="b-foodshot-board__item-content-image" href="<?=$arItem["DETAIL_PAGE_URL"]?>">
						<img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" width="<?=$arItem["PREVIEW_PICTURE"]["WIDTH"]?>" height="<?=$arItem["PREVIEW_PICTURE"]["HEIGHT"]?>" alt="<?=$arItem["NAME"]?>" title="<?=$arItem["NAME"]?>" />
					</a>
				<?else:?>
					<img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" width="<?=$arItem["PREVIEW_PICTURE"]["WIDTH"]?>" height="<?=$arItem["PREVIEW_PICTURE"]["HEIGHT"]?>" alt="<?=$arItem["NAME"]?>" title="<?=$arItem["NAME"]?>" />
				<?endif;?>
			<?endif?>	
			<?if($arParams["DISPLAY_PREVIEW_TEXT"]!="N" && $arItem["PREVIEW_TEXT"]):?>
				<div class="b-foodshot-board__item-content-text">
					<?echo $arItem["PREVIEW_TEXT"];?>
				</div>
			<?endif;?>
			<?if($arItem["FIELDS"]["CREATED_BY"]):?>
				<div class="b-recipe-author b-foodshot-board__item-content-author">от <?=$arItem["user_name"]?></div>
			<?endif;?>
		  </div>


		    <?if($arItem["comments"]):?>		  	
		  	<div class="b-foodshot-board__item-comments">
				<?$commentCNT = count($arItem["comments"])?>
				<?define("COMMENTS", 3);?>
				<?if($commentCNT > COMMENTS):?>
					<div class="b-foodshot-board__item-comments-hidden">
						<a class="b-foodshot-board__item-comments-hidden__button" href="<?=$arItem["DETAIL_PAGE_URL"]?>">
							<span class="b-comment-icon b-foodshot-board__item-comments-hidden__icon"><?echo $commentCNT-COMMENTS;?></span>
						</a>
					</div>
				<?endif;?>
				<div class="b-comment-list">
				  	<?foreach($arItem["comments"] as $index => $comment):?>
						  <div data-id="<?=$index?>" class="b-comment b-comment__type-short b-foodshot-board__item-comment">
						  	<a class="b-comment__userpic b-userpic" href="http://www.foodclub.ru/profile/<?=$comment["CREATED_BY"]?>/">
						  		<span class="b-userpic__layer"></span>
						  		<img width="30" height="30" class="b-userpic__image" alt="<?=$arResult["users"][$comment["CREATED_BY"]]["name"]?>" src="<?=$arResult["users"][$comment["CREATED_BY"]]["PERSONAL_PHOTO"]["src"]?>">
						  	</a>
							<div class="b-comment__content">
							  <div class="b-comment__author">
							  	<a href="/profile/<?=$comment["CREATED_BY"]?>/"><?=$arResult["users"][$comment["CREATED_BY"]]["name"]?></a>
							  </div>
							  <div class="b-comment__text"><?=$comment["PREVIEW_TEXT"]?></div>
							</div>
							<div class="i-clearfix"></div>
						  </div>
						<?if( $index >= COMMENTS-1 ) break;?>
				  	<?endforeach;?>
		  		</div>
		  	</div>
		    <?endif;?>
		  <div class="b-foodshot-board__item-action">
		  	<?if($USER->IsAuthorized()):?>
			  	<div class="b-foodshot-board__item-action_comment_hidden b-form-comments">
					<form method="get" action="">
						<div class="b-form-field b-comment b-comment__type-short b-form-field__type-comment">
							<a class="b-comment__userpic b-userpic" href="http://www.foodclub.ru/profile/<?=$arResult["user"]["ID"]?>/">
								<span class="b-userpic__layer"></span>
								<img width="30" height="30" class="b-userpic__image" alt="<?=$arResult["user"]["NAME"]." ".$arResult["user"]["LAST_NAME"]?>" src="<?=$arResult["user"]["PERSONAL_PHOTO"]["src"]?>">
							</a>
							<div class="b-comment__content">
								<textarea required="" name="comment" rows="3" cols="30"></textarea>
							</div>
							<div class="i-clearfix"></div>
						</div>
						<a class="b-form-field__type-comment__button i-frame-bg" href="#">
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
			<?endif;?>
			<div class="b-foodshot-board__item-action_like">
				<span class="b-like">
			  		<a title="Мне нравится" class="b-like-icon b-like-icon__type-button<?=($USER->IsAuthorized() && in_array($USER->GetID(),$arItem["likes"]) ? ' b-like-icon__type-active' : '')?>" href="#"></a>
			  		<span class="b-like-num">
			  			<?//echo ($arItem["DISPLAY_PROPERTIES"]["likes_count"]["VALUE"] ? $arItem["DISPLAY_PROPERTIES"]["likes_count"]["VALUE"] : "0") ;?>
			  			<?=count($arItem["likes"]);?>
			  		</span>
			  	</span>
			</div>
		  	<?if($USER->IsAuthorized()):?>
			  <div class="b-foodshot-board__item-action_comment_visible">
				<a href="#" class="b-comment-icon b-comment-icon__type-button" title="Комментировать"></a>
			  </div>
			<?endif;?>
			<div class="i-clearfix"></div>
		  </div>
		</div>
	<?endforeach;?>
	<div class="i-clearfix"></div>
</div>