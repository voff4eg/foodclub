<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if (!$this->__component->__parent || empty($this->__component->__parent->__name) || $this->__component->__parent->__name != "bitrix:blog"):
	$GLOBALS['APPLICATION']->SetAdditionalCSS('/bitrix/components/bitrix/blog/templates/.default/style.css');
	$GLOBALS['APPLICATION']->SetAdditionalCSS('/bitrix/components/bitrix/blog/templates/.default/themes/blue/style.css');
endif;
?>
<div class="blog-post-current-micro">
<?
if(strlen($arResult["MESSAGE"])>0)
{
	?>
	<div class="blog-textinfo blog-note-box">
		<div class="blog-textinfo-text">
			<?=$arResult["MESSAGE"]?>
		</div>
	</div>
	<?
}
if(strlen($arResult["ERROR_MESSAGE"])>0)
{
	?>
	<div class="blog-errors blog-note-box blog-note-error">
		<div class="blog-error-text">
			<?=$arResult["ERROR_MESSAGE"]?>
		</div>
	</div>
	<?
}
if(strlen($arResult["FATAL_MESSAGE"])>0)
{
	?>
	<div class="blog-errors blog-note-box blog-note-error">
		<div class="blog-error-text">
			<?=$arResult["FATAL_MESSAGE"]?>
		</div>
	</div>
	<?
}
elseif(strlen($arResult["NOTE_MESSAGE"])>0)
{
	?>
	<div class="blog-textinfo blog-note-box">
		<div class="blog-textinfo-text">
			<?=$arResult["NOTE_MESSAGE"]?>
		</div>
	</div>
	<?
}
else
{
	if(!empty($arResult["Post"])>0)
	{
		$className = "blog-post microblog-post";
		$className .= " blog-post-first";
		$className .= " blog-post-alt";
		$className .= " blog-post-year-".$arResult["Post"]["DATE_PUBLISH_Y"];
		$className .= " blog-post-month-".IntVal($arResult["Post"]["DATE_PUBLISH_M"]);
		$className .= " blog-post-day-".IntVal($arResult["Post"]["DATE_PUBLISH_D"]);
		?>
		<div class="<?=$className?>">
		<div class="blog-post-info">
			<div class="blog-post-avatar"><?
				if(strlen($arResult["BlogUser"]["AVATAR_img"]) > 0)
					echo $arResult["BlogUser"]["AVATAR_img"];
				else
					echo '<img src="/bitrix/components/bitrix/blog/templates/.default/images/noavatar.gif" border="0">';
			?></div>
			<?if ($arParams["SHOW_RATING"] == "Y"):?>
			<div class="blog-post-rating rating_vote_standart">
			<?
			$APPLICATION->IncludeComponent(
				"bitrix:rating.vote", $arParams["RATING_TYPE"],
				Array(
					"ENTITY_TYPE_ID" => "BLOG_POST",
					"ENTITY_ID" => $arResult["Post"]["ID"],
					"OWNER_ID" => $arResult["Post"]["AUTHOR_ID"],
					"USER_VOTE" => $arResult["RATING"]["USER_VOTE"],
					"USER_HAS_VOTED" => $arResult["RATING"]["USER_HAS_VOTED"],
					"TOTAL_VOTES" => $arResult["RATING"]["TOTAL_VOTES"],
					"TOTAL_POSITIVE_VOTES" => $arResult["RATING"]["TOTAL_POSITIVE_VOTES"],
					"TOTAL_NEGATIVE_VOTES" => $arResult["RATING"]["TOTAL_NEGATIVE_VOTES"],
					"TOTAL_VALUE" => $arResult["RATING"]["TOTAL_VALUE"],
					"PATH_TO_USER_PROFILE" => $arParams["~PATH_TO_USER"],
				),
				$component,
				array("HIDE_ICONS" => "Y")
			);?>
			</div>
			<?endif;?>
			<div class="blog-author">
			<?
			if (COption::GetOptionString("blog", "allow_alias", "Y") == "Y" && array_key_exists("ALIAS", $arResult["BlogUser"]) && strlen($arResult["BlogUser"]["ALIAS"]) > 0)
				$arTmpUser = array(
					"NAME" => "",
					"LAST_NAME" => "",
					"SECOND_NAME" => "",
					"LOGIN" => "",
					"NAME_LIST_FORMATTED" => $arResult["BlogUser"]["~ALIAS"],
				);
			elseif (strlen($arResult["urlToBlog"]) > 0 || strlen($arResult["urlToAuthor"]) > 0)
					$arTmpUser = array(
						"NAME" => $arResult["arUser"]["~NAME"],
						"LAST_NAME" => $arResult["arUser"]["~LAST_NAME"],
						"SECOND_NAME" => $arResult["arUser"]["~SECOND_NAME"],
						"LOGIN" => $arResult["arUser"]["~LOGIN"],
						"NAME_LIST_FORMATTED" => "",
					);
			?>
			<?if($arParams["SEO_USER"] == "Y"):?>
				<noindex>
			<?endif;?>
			<?		
			$APPLICATION->IncludeComponent("bitrix:main.user.link",
				'',
				array(
					"ID" => $arResult["arUser"]["ID"],
					"HTML_ID" => "blog_post_".$arResult["arUser"]["ID"],
					"NAME" => $arTmpUser["NAME"],
					"LAST_NAME" => $arTmpUser["LAST_NAME"],
					"SECOND_NAME" => $arTmpUser["SECOND_NAME"],
					"LOGIN" => $arTmpUser["LOGIN"],
					"NAME_LIST_FORMATTED" => $arTmpUser["NAME_LIST_FORMATTED"],
					"USE_THUMBNAIL_LIST" => "N",
					"PROFILE_URL" => $arResult["urlToAuthor"],
					"PROFILE_URL_LIST" => $arResult["urlToBlog"],
					"PATH_TO_SONET_MESSAGES_CHAT" => $arParams["~PATH_TO_MESSAGES_CHAT"],
					"PATH_TO_VIDEO_CALL" => $arParams["~PATH_TO_VIDEO_CALL"],
					"DATE_TIME_FORMAT" => $arParams["DATE_TIME_FORMAT"],
					"SHOW_YEAR" => $arParams["SHOW_YEAR"],
					"CACHE_TYPE" => $arParams["CACHE_TYPE"],
					"CACHE_TIME" => $arParams["CACHE_TIME"],
					"NAME_TEMPLATE" => $arParams["NAME_TEMPLATE"],
					"SHOW_LOGIN" => $arParams["SHOW_LOGIN"],
					"PATH_TO_CONPANY_DEPARTMENT" => $arParams["~PATH_TO_CONPANY_DEPARTMENT"],
					"PATH_TO_SONET_USER_PROFILE" => ($arParams["USE_SOCNET"] == "Y" ? $arParams["~PATH_TO_USER"] : $arParams["~PATH_TO_SONET_USER_PROFILE"]),
					"INLINE" => "Y",
					"SEO_USER" => $arParams["SEO_USER"],
				),
				false,
				array("HIDE_ICONS" => "Y")
			);
			?>
			<?if($arParams["SEO_USER"] == "Y"):?>
				</noindex>
			<?endif;?>
			</div>
			<div class="blog-post-date"><span class="blog-post-day"><?=$arResult["Post"]["DATE_PUBLISH_DATE"]?></span><span class="blog-post-time"><?=$arResult["Post"]["DATE_PUBLISH_TIME"]?></span><span class="blog-post-date-formated"><?=$arResult["Post"]["DATE_PUBLISH_FORMATED"]?></span></div>
		</div>
		<div class="blog-clear-float"></div>
		<div class="blog-post-content">
			<?=$arResult["Post"]["textFormated"]?>
		</div>
			<div class="blog-post-meta">
				<?
				if(array_key_exists("USE_SHARE", $arParams) && $arParams["USE_SHARE"] == "Y")
				{
					?><div class="blog-post-share">
						<noindex><?
						$APPLICATION->IncludeComponent("bitrix:main.share", "", array(
								"HANDLERS" => $arParams["SHARE_HANDLERS"],
								"PAGE_URL" => htmlspecialcharsback($arResult["urlToPost"]),
								"PAGE_TITLE" => $arResult["Post"]["~TITLE"],
								"SHORTEN_URL_LOGIN" => $arParams["SHARE_SHORTEN_URL_LOGIN"],
								"SHORTEN_URL_KEY" => $arParams["SHARE_SHORTEN_URL_KEY"],
								"ALIGN" => "right",
								"HIDE" => $arParams["SHARE_HIDE"],
							),
							$component,
							array("HIDE_ICONS" => "Y")
						);
						?></noindex>
					</div>
					<?
				}?>
				<div class="blog-post-meta-util">
					<?if($arResult["Post"]["ENABLE_COMMENTS"] == "Y"):?>
						<span class="microblog-post-comments-link"><a href=""><span class="microblog-post-link-caption"><?=GetMessage("BLOG_BLOG_BLOG_COMMENTS")?></span><span class="blog-post-link-counter"><?=IntVal($arResult["Post"]["NUM_COMMENTS"])?></span></a></span>
					<?endif;?>
					<?if(strLen($arResult["urlToEdit"])>0):?>
						<span class="blog-vert-separator"></span>
						<span class="microblog-post-edit-link"><a href="<?=$arResult["urlToEdit"]?>"><span class="blog-post-link-caption"><?=GetMessage("BLOG_BLOG_BLOG_EDIT")?></span></a></span>
					<?endif;?>
					<?if(strLen($arResult["urlToDelete"])>0):?>
						<span class="blog-vert-separator"></span>
						<span class="microblog-post-delete-link"><a href="javascript:if(confirm('<?=GetMessage("BLOG_MES_DELETE_POST_CONFIRM")?>')) window.location='<?=$arResult["urlToDelete"]."&".bitrix_sessid_get()?>'"><span class="blog-post-link-caption"><?=GetMessage("BLOG_BLOG_BLOG_DELETE")?></span></a></span>
					<?endif;?>
					<?if ($arParams["SHOW_RATING"] == "Y"):?>
					<span class="rating_vote_like">
					<span class="blog-vert-separator"></span>
					<?
					$APPLICATION->IncludeComponent(
						"bitrix:rating.vote", $arParams["RATING_TYPE"],
						Array(
							"ENTITY_TYPE_ID" => "BLOG_POST",
							"ENTITY_ID" => $arResult["Post"]["ID"],
							"OWNER_ID" => $arResult["Post"]["AUTHOR_ID"],
							"USER_VOTE" => $arResult["RATING"]["USER_VOTE"],
							"USER_HAS_VOTED" => $arResult["RATING"]["USER_HAS_VOTED"],
							"TOTAL_VOTES" => $arResult["RATING"]["TOTAL_VOTES"],
							"TOTAL_POSITIVE_VOTES" => $arResult["RATING"]["TOTAL_POSITIVE_VOTES"],
							"TOTAL_NEGATIVE_VOTES" => $arResult["RATING"]["TOTAL_NEGATIVE_VOTES"],
							"TOTAL_VALUE" => $arResult["RATING"]["TOTAL_VALUE"],
							"PATH_TO_USER_PROFILE" => $arParams["~PATH_TO_USER"],
						),
						$component,
						array("HIDE_ICONS" => "Y")
					);?>
					</span>
					<?endif;?>
				</div>
			</div>
		</div>
		<?
	}
	else
		echo GetMessage("BLOG_BLOG_BLOG_NO_AVAIBLE_MES");
}
?>
</div>