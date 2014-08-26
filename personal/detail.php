<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
?>
<?$APPLICATION->SetAdditionalCSS("/css/profile.css");
$APPLICATION->SetAdditionalCSS("/css/elem.css");?>
<?
if (CModule::IncludeModule("advertising")){ $strBanner = CAdvBanner::Show("right_banner"); }
?>
<?
$arUser = $APPLICATION->IncludeComponent("custom:profile", "", array()
);
?>
<?if(!empty($arUser)):?>
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
			<?if(trim(strlen($arUser['UF_ABOUT_SELF']))):?>
			<div class="b-personal-page__about">
				<h3 class="b-hr-bg b-personal-page__heading">
					<span class="b-hr-bg__content">О себе</span>
				</h3>
				<div class="b-personal-page__about__text">
					<?=$arUser['UF_ABOUT_SELF']?>
				</div>
			</div>
			<?endif;?>
			<?if(strlen($arUser["UF_FACEBOOK"]) > 0 || strlen($arUser["UF_VKONTAKTE"]) > 0 || strlen($arUser["UF_TWITTER"]) > 0 || strlen($arUser["UF_YANDEX"]) > 0 
			|| strlen($arUser["UF_LJ"]) > 0 || strlen($arUser["UF_ODNOKLASSNIKI"]) > 0 || strlen($arUser["UF_YOUTUBE"]) > 0 || strlen($arUser["UF_VIMEO"])):?>
			<div class="b-personal-page__web">
				<h3 class="b-hr-bg b-personal-page__heading">
					<span class="b-hr-bg__content">Сайт и аккаунты в соцсетях</span>
				</h3>
				<?if(strlen($arUser["WORK_WWW"]) > 0 && $arUser["WORK_WWW"] != "http://"):?><div class="b-personal-page__web__site"><?if(strpos($arUser["WORK_WWW"],"http://") !== false):?><a rel="nofollow" href="<?=$arUser["WORK_WWW"]?>" target="_blank"><?else:?><a href="http://<?=$arUser["WORK_WWW"]?>" target="_blank"><?endif;?><?=$arUser["WORK_WWW"]?></a></div><?endif;?>
				<div class="b-personal-page__web__networks b-networks-detailed">
					<?if(strlen($arUser["UF_FACEBOOK"]) > 0):?><?if(strpos($arUser["UF_FACEBOOK"],"facebook") !== false):?><?if(strpos($arUser["UF_FACEBOOK"],"https://") !== false):?><a rel="nofollow" href="<?=$arUser["UF_FACEBOOK"]?>" target="_blank" class="b-networks-detailed__item i-facebook"><?else:?><a rel="nofollow" href="https://<?=$arUser["UF_FACEBOOK"]?>" target="_blank" class="b-networks-detailed__item i-facebook"><?endif;?><?else:?><a href="https://facebook.com/<?=$arUser["UF_FACEBOOK"]?>" target="_blank" class="b-networks-detailed__item i-facebook"><?endif;?>Facebook</a><?endif;?>
					<?if(strlen($arUser["UF_VKONTAKTE"]) > 0):?><?if(strpos($arUser["UF_VKONTAKTE"],"vkontakte") !== false || strpos($arUser["UF_VKONTAKTE"],"vk.com") !== false):?><?if(strpos($arUser["UF_VKONTAKTE"],"http://") !== false):?><a rel="nofollow" href="<?=$arUser["UF_VKONTAKTE"]?>" target="_blank" class="b-networks-detailed__item i-vkontakte"><?else:?><a rel="nofollow" href="http://<?=$arUser["UF_VKONTAKTE"]?>" target="_blank" class="b-networks-detailed__item i-vkontakte"><?endif;?><?else:?><a href="http://vk.com/<?=$arUser["UF_VKONTAKTE"]?>" target="_blank" class="b-networks-detailed__item i-vkontakte"><?endif;?>вКонтакте</a><?endif;?>
					<?if(strlen($arUser["UF_TWITTER"]) > 0):?><?if(strpos($arUser["UF_TWITTER"],"twitter") !== false):?><?if(strpos($arUser["UF_TWITTER"],"http://") !== false || strpos($arUser["UF_TWITTER"],"https://") !== false):?><a rel="nofollow" href="<?=$arUser["UF_TWITTER"]?>" target="_blank" class="b-networks-detailed__item i-twitter"><?else:?><a rel="nofollow" href="http://<?=$arUser["UF_TWITTER"]?>" target="_blank" class="b-networks-detailed__item i-twitter"><?endif;?><?else:?><a href="http://twitter.com/<?=$arUser["UF_TWITTER"]?>" target="_blank" class="b-networks-detailed__item i-twitter"><?endif;?>Twitter</a><?endif;?>
					<?if(strlen($arUser["UF_YANDEX"]) > 0):?><?if(strpos($arUser["UF_YANDEX"],"ya.ru") !== false):?><?if(strpos($arUser["UF_YANDEX"],"http://") !== false):?><a rel="nofollow" href="<?=$arUser["UF_YANDEX"]?>" target="_blank" class="b-networks-detailed__item i-yandex"><?else:?><a rel="nofollow" href="http://<?=$arUser["UF_YANDEX"]?>" target="_blank" class="b-networks-detailed__item i-yandex"><?endif;?><?else:?><a href="http://<?=$arUser["UF_YANDEX"]?>.ya.ru" target="_blank" class="b-networks-detailed__item i-yandex"><?endif;?>Яндекс</a><?endif;?>
					<?if(strlen($arUser["UF_LJ"]) > 0):?><?if(strpos($arUser["UF_LJ"],"livejournal") !== false):?><?if(strpos($arUser["UF_LJ"],"http://") !== false):?><a rel="nofollow" href="<?=$arUser["UF_LJ"]?>" target="_blank" class="b-networks-detailed__item i-livejournal"><?else:?><a rel="nofollow" href="http://<?=$arUser["UF_LJ"]?>" target="_blank" class="b-networks-detailed__item i-livejournal"><?endif;?><?else:?><a href="http://<?=$arUser["UF_LJ"]?>.livejournal.com" target="_blank" class="b-networks-detailed__item i-livejournal"><?endif;?>Livejournal</a><?endif;?>
					<?if(strlen($arUser["UF_ODNOKLASSNIKI"]) > 0):?><?if(strpos($arUser["UF_ODNOKLASSNIKI"],"odnoklassniki") !== false):?><?if(strpos($arUser["UF_ODNOKLASSNIKI"],"http://") !== false):?><a rel="nofollow" href="<?=$arUser["UF_ODNOKLASSNIKI"]?>" target="_blank" class="b-networks-detailed__item i-classmates"><?else:?><a rel="nofollow" href="http://<?=$arUser["UF_ODNOKLASSNIKI"]?>" target="_blank" class="b-networks-detailed__item i-classmates"><?endif;?><?else:?><?if(strpos($arUser["UF_ODNOKLASSNIKI"],"profile") !== false):?><a href="http://odnoklassniki.ru/<?=$arUser["UF_ODNOKLASSNIKI"]?>" target="_blank" class="b-networks-detailed__item i-classmates"><?else:?><a href="http://odnoklassniki.ru/profile/<?=$arUser["UF_ODNOKLASSNIKI"]?>" target="_blank" class="b-networks-detailed__item i-classmates"><?endif;?><?endif;?>Одноклассники</a><?endif;?>
					<?if(strlen($arUser["UF_YOUTUBE"]) > 0):?><?if(strpos($arUser["UF_YOUTUBE"],"youtube") !== false):?><?if(strpos($arUser["UF_YOUTUBE"],"http://") !== false):?><a rel="nofollow" href="<?=$arUser["UF_YOUTUBE"]?>" target="_blank" class="b-networks-detailed__item i-youtube"><?else:?><a rel="nofollow" href="http://<?=$arUser["UF_YOUTUBE"]?>" target="_blank" class="b-networks-detailed__item i-youtube"><?endif;?><?else:?><?if(strpos($arUser["UF_YOUTUBE"],"user/") !== false):?><a href="http://youtube.com/<?=$arUser["UF_YOUTUBE"]?>" target="_blank" class="b-networks-detailed__item i-youtube"><?else:?><a href="http://youtube.com/user/<?=$arUser["UF_YOUTUBE"]?>" target="_blank" class="b-networks-detailed__item i-youtube"><?endif;?><?endif;?>YouTube</a><?endif;?>
					<?if(strlen($arUser["UF_VIMEO"]) > 0):?><?if(strpos($arUser["UF_VIMEO"],"vimeo") !== false):?><?if(strpos($arUser["UF_VIMEO"],"http://") !== false):?><a rel="nofollow" href="<?=$arUser["UF_VIMEO"]?>" target="_blank" class="b-networks-detailed__item i-vimeo"><?else:?><a rel="nofollow" href="http://<?=$arUser["UF_VIMEO"]?>" target="_blank" class="b-networks-detailed__item i-vimeo"><?endif;?><?else:?><a href="https://vimeo.com/<?=$arUser["UF_VIMEO"]?>" target="_blank" class="b-networks-detailed__item i-vimeo"><?endif;?>Vimeo</a><?endif;?>
				</div>
			</div>
			<?endif;?>
			<?if(is_array($arUser["LAST_RECIPES"])):?>
			<?if(!empty($arUser["LAST_RECIPES"])):?>
			<div class="b-personal-page__recipes">
				<h3 class="b-hr-bg b-personal-page__heading">
					<span class="b-hr-bg__content">Последние рецепты</span>
				</h3>
				<div class="b-recipes-list">
					<?foreach($arUser["LAST_RECIPES"] as $recipe):?>
					<div class="b-recipes-list__item b-recipe-preview">
						<?if(!empty($recipe["PREVIEW_PICTURE"]) > 0):?>
						<div class="b-recipe-preview__photo"><a href="/detail/<?=($recipe["CODE"] ? $recipe["CODE"] : $recipe["ID"])?>/" title="<?=$recipe["NAME"]?>" class="b-recipe-preview__photo__link"><img src="<?=$recipe["PREVIEW_PICTURE"]["SRC"]?>" width="170" alt="<?=$recipe["NAME"]?>" class="b-recipe-preview__photo__image" /></a></div>
						<?endif;?>
						<div class="b-recipe-preview__heading b-h5"><a href="/detail/<?=($recipe["CODE"] ? $recipe["CODE"] : $recipe["ID"])?>/" class="b-recipe-preview__heading__link"><?=$recipe["NAME"]?></a></div>
						<div class="b-recipe-preview__info"><a href="/detail/<?=($recipe["CODE"] ? $recipe["CODE"] : $recipe["ID"])?>/#comments" class="b-recipe-preview__comments b-comments-preview" title="Оставить отзыв"><span class="b-comments-preview__icon"></span><span class="b-comments-preview__num"><?=intval($recipe["PROPERTY_COMMENT_COUNT_VALUE"])?></span></a></div>
					</div>
					<?endforeach;?>						
					<div class="i-clearfix"></div>
				</div>
			</div>
			<?endif;?>
			<?endif;?>
			<?if(!empty($arUser["LAST_POSTS"]["Posts"])):?>
			<div class="b-personal-page__entries">
				<h3 class="b-hr-bg b-personal-page__heading">
					<span class="b-hr-bg__content">Последние записи</span>
				</h3>
				<div class="b-entries-list">
					<?foreach($arUser["LAST_POSTS"]["Posts"] as $post):?>
					<div class="b-entries-list__item">
						<div class="b-entries-list__item__marker"></div>
						<div class="b-entries-list__item__blog"><?=$arUser["LAST_POSTS"]["SocNetName"][ $arUser["LAST_POSTS"]["SocNetBlogs"][ $post['BLOG_ID'] ] ]['NAME']?></div>
						<div class="b-entries-list__item__title"><a href="/blogs/group/<?=$arUser["LAST_POSTS"]["SocNetBlogs"][ $post['BLOG_ID'] ]."/blog/".$post['ID']?>/"><?=$post["TITLE"]?></a></div>
					</div>
					<?endforeach;?>				
				</div>
			</div>
			<?endif;?>			
		</div>
		<div id="banner_space">
			<?$APPLICATION->IncludeComponent("custom:profile.badges", "", array("USER" => $arUser));?>
			<?
			$rsRandomDoUKnow = CIBlockElement::GetList(array("rand"=>"asc"),array("IBLOCK_CODE"=>"do_u_know"),false,false,array("ID","PREVIEW_TEXT"));
			if($arRandomDoUKnow = $rsRandomDoUKnow->Fetch()){?>
			<div id="do-you-know-that" class="b-facts">
				<div class="b-facts__heading">Знаете ли вы что:</div>
				<div class="b-facts__content">
					<div class="b-facts__item" data-id="<?=$arRandomDoUKnow["ID"]?>">
						<?=$arRandomDoUKnow["PREVIEW_TEXT"]?>
					</div>
				</div>
				<div class="b-facts__more">
					<a href="#" class="b-facts__more__link">Еще</a>
				</div>
			</div>
			<?}?>
			<?if(strlen($strBanner) > 0){?><div class="banner"><?=$strBanner?></div><?}?>
		</div>
		<div class="clear"></div>
	</div>
</div>
<?else:
	LocalRedirect("/404.php");
endif;?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
