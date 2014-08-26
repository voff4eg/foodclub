<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$GLOBALS['APPLICATION']->SetAdditionalCSS('/bitrix/components/bitrix/blog/templates/.default/style.css');
$GLOBALS['APPLICATION']->SetAdditionalCSS('/bitrix/components/bitrix/blog/templates/.default/themes/blue/style.css');
?>

<div id="blog-posts-content-micro">
<?
if(!empty($arResult["OK_MESSAGE"]))
{
	?>
	<div class="blog-notes blog-note-box">
		<div class="blog-note-text">
			<ul>
				<?
				foreach($arResult["OK_MESSAGE"] as $v)
				{
					?>
					<li><?=$v?></li>
					<?
				}
				?>
			</ul>
		</div>
	</div>
	<?
}
if(!empty($arResult["MESSAGE"]))
{
	?>
	<div class="blog-textinfo blog-note-box">
		<div class="blog-textinfo-text">
			<ul>
				<?
				foreach($arResult["MESSAGE"] as $v)
				{
					?>
					<li><?=$v?></li>
					<?
				}
				?>
			</ul>
		</div>
	</div>
	<?
}
if(!empty($arResult["ERROR_MESSAGE"]))
{
	?>
	<div class="blog-errors blog-note-box blog-note-error">
		<div class="blog-error-text">
			<ul>
				<?
				foreach($arResult["ERROR_MESSAGE"] as $v)
				{
					?>
					<li><?=$v?></li>
					<?
				}
				?>
			</ul>
		</div>
	</div>
	<?
}

if(count($arResult["POST"])>0)
{
	foreach($arResult["POST"] as $ind => $CurPost)
	{
		$className = "blog-post microblog-post";
		if($ind == 0)
			$className .= " blog-post-first";
		elseif(($ind+1) == count($arResult["POST"]))
			$className .= " blog-post-last";
		if($ind%2 == 0)
			$className .= " blog-post-alt";
		$className .= " blog-post-year-".$CurPost["DATE_PUBLISH_Y"];
		$className .= " blog-post-month-".IntVal($CurPost["DATE_PUBLISH_M"]);
		$className .= " blog-post-day-".IntVal($CurPost["DATE_PUBLISH_D"]);
		?>
			<div class="<?=$className?>">
				<div class="blog-post-info">
					<div class="blog-post-avatar"><?
						if(strlen($CurPost["BlogUser"]["AVATAR_img"]) > 0)
							echo $CurPost["BlogUser"]["AVATAR_img"];
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
							"ENTITY_ID" => $CurPost["ID"],
							"OWNER_ID" => $CurPost["arUser"]["ID"],
							"USER_VOTE" => $arResult["RATING"][$CurPost["ID"]]["USER_VOTE"],
							"USER_HAS_VOTED" => $arResult["RATING"][$CurPost["ID"]]["USER_HAS_VOTED"],
							"TOTAL_VOTES" => $arResult["RATING"][$CurPost["ID"]]["TOTAL_VOTES"],
							"TOTAL_POSITIVE_VOTES" => $arResult["RATING"][$CurPost["ID"]]["TOTAL_POSITIVE_VOTES"],
							"TOTAL_NEGATIVE_VOTES" => $arResult["RATING"][$CurPost["ID"]]["TOTAL_NEGATIVE_VOTES"],
							"TOTAL_VALUE" => $arResult["RATING"][$CurPost["ID"]]["TOTAL_VALUE"],
							"PATH_TO_USER_PROFILE" => $arParams["~PATH_TO_USER"]
						),
						$component,
						array("HIDE_ICONS" => "Y")
					);?>
					</div>
					<?endif;?>
					<div class="blog-author">
					<?
					if (COption::GetOptionString("blog", "allow_alias", "Y") == "Y" && array_key_exists("ALIAS", $CurPost["BlogUser"]) && strlen($CurPost["BlogUser"]["ALIAS"]) > 0)
						$arTmpUser = array(
							"NAME" => "",
							"LAST_NAME" => "",
							"SECOND_NAME" => "",
							"LOGIN" => "",
							"NAME_LIST_FORMATTED" => $CurPost["BlogUser"]["~ALIAS"],
						);
					elseif (strlen($CurPost["urlToBlog"]) > 0 || strlen($CurPost["urlToAuthor"]) > 0)
						$arTmpUser = array(
							"NAME" => $CurPost["arUser"]["~NAME"],
							"LAST_NAME" => $CurPost["arUser"]["~LAST_NAME"],
							"SECOND_NAME" => $CurPost["arUser"]["~SECOND_NAME"],
							"LOGIN" => $CurPost["arUser"]["~LOGIN"],
							"NAME_LIST_FORMATTED" => "",
						);
					?>					
					<?if($arParams["SEO_USER"] == "Y"):?>
						<noindex>
					<?endif;?>
					<?
					$GLOBALS["APPLICATION"]->IncludeComponent("bitrix:main.user.link",
						'',
						array(
							"ID" => $CurPost["arUser"]["ID"],
							"HTML_ID" => "blog_blog_".$CurPost["arUser"]["ID"],
							"NAME" => $arTmpUser["NAME"],
							"LAST_NAME" => $arTmpUser["LAST_NAME"],
							"SECOND_NAME" => $arTmpUser["SECOND_NAME"],
							"LOGIN" => $arTmpUser["LOGIN"],
							"NAME_LIST_FORMATTED" => $arTmpUser["NAME_LIST_FORMATTED"],
							"USE_THUMBNAIL_LIST" => "N",
							"PROFILE_URL" => $CurPost["urlToAuthor"],
							"PROFILE_URL_LIST" => $CurPost["urlToBlog"],
							"PATH_TO_SONET_MESSAGES_CHAT" => $arParams["~PATH_TO_MESSAGES_CHAT"],
							"PATH_TO_VIDEO_CALL" => $arParams["~PATH_TO_VIDEO_CALL"],
							"DATE_TIME_FORMAT" => $arParams["DATE_TIME_FORMAT"],
							"SHOW_YEAR" => $arParams["SHOW_YEAR"],
							"CACHE_TYPE" => $arParams["CACHE_TYPE"],
							"CACHE_TIME" => $arParams["CACHE_TIME"],
							"NAME_TEMPLATE" => $arParams["NAME_TEMPLATE"],
							"SHOW_LOGIN" => $arParams["SHOW_LOGIN"],
							"PATH_TO_CONPANY_DEPARTMENT" => $arParams["~PATH_TO_CONPANY_DEPARTMENT"],
							"PATH_TO_SONET_USER_PROFILE" => $arParams["~PATH_TO_SONET_USER_PROFILE"],
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
					<div class="blog-post-date"><a href="<?=$CurPost["urlToPost"]?>"><span class="blog-post-day"><?=$CurPost["DATE_PUBLISH_DATE"]?></span><span class="blog-post-time"><?=$CurPost["DATE_PUBLISH_TIME"]?></span></a><span class="blog-post-date-formated"><a href="<?=$CurPost["urlToPost"]?>"><?=$CurPost["DATE_PUBLISH_FORMATED"]?></a></span></div>
				</div>
				<div class="blog-clear-float"></div>
				<div class="blog-post-content">
					<a href="<?=$CurPost["urlToPost"]?>"><?=$CurPost["TEXT_FORMATED"]?></a>
				</div>
				<div class="blog-post-meta">
					<?
					if(array_key_exists("USE_SHARE", $arParams) && $arParams["USE_SHARE"] == "Y")
					{
						?>
						<div class="blog-post-share" style="float: right; display:inline-block;">
							<noindex>
							<?
							$APPLICATION->IncludeComponent("bitrix:main.share", "", array(
									"HANDLERS" => $arParams["SHARE_HANDLERS"],
									"PAGE_URL" => htmlspecialcharsback($CurPost["urlToPost"]),
									"PAGE_TITLE" => $CurPost["TEXT_FORMATED"],
									"SHORTEN_URL_LOGIN" => $arParams["SHARE_SHORTEN_URL_LOGIN"],
									"SHORTEN_URL_KEY" => $arParams["SHARE_SHORTEN_URL_KEY"],
									"ALIGN" => "right",
									"HIDE" => $arParams["SHARE_HIDE"],
								),
								$component,
								array("HIDE_ICONS" => "Y")
							);
							?>
							</noindex>
						</div>
						<?
					}
					?>	
					<div class="blog-post-meta-util">
						<?if($CurPost["ENABLE_COMMENTS"] == "Y"):?>
							<span class="microblog-post-comments-link"><a href="<?=$CurPost["urlToPost"]?>#comments"><span class="blog-post-link-caption"><?=GetMessage("BLOG_BLOG_BLOG_COMMENTS")?></span><span class="microblog-post-link-counter"><?=IntVal($CurPost["NUM_COMMENTS"]);?></span></a></span>
						<?endif;?>
						<?if(strLen($CurPost["urlToEdit"])>0):?>
							<span class="blog-vert-separator"></span>
							<span class="microblog-post-edit-link"><a href="<?=$CurPost["urlToEdit"]?>"><?=GetMessage("BLOG_MES_EDIT")?></a></span>
						<?endif;?>
						<?if(strLen($CurPost["urlToDelete"])>0):?>
							<span class="blog-vert-separator"></span>
							<span class="microblog-post-delete-link"><a href="javascript:if(confirm('<?=GetMessage("BLOG_MES_DELETE_POST_CONFIRM")?>')) window.location='<?=$CurPost["urlToDelete"]."&".bitrix_sessid_get()?>'"><?=GetMessage("BLOG_MES_DELETE")?></a></span>
						<?endif;?>
						<?if ($arParams["SHOW_RATING"] == "Y"):?>
							<span class="blog-post-rating rating_vote_like">
							<span class="blog-vert-separator"></span>
							<?
							$APPLICATION->IncludeComponent(
								"bitrix:rating.vote", $arParams["RATING_TYPE"],
								Array(
									"ENTITY_TYPE_ID" => "BLOG_POST",
									"ENTITY_ID" => $CurPost["ID"],
									"OWNER_ID" => $CurPost["arUser"]["ID"],
									"USER_VOTE" => $arResult["RATING"][$CurPost["ID"]]["USER_VOTE"],
									"USER_HAS_VOTED" => $arResult["RATING"][$CurPost["ID"]]["USER_HAS_VOTED"],
									"TOTAL_VOTES" => $arResult["RATING"][$CurPost["ID"]]["TOTAL_VOTES"],
									"TOTAL_POSITIVE_VOTES" => $arResult["RATING"][$CurPost["ID"]]["TOTAL_POSITIVE_VOTES"],
									"TOTAL_NEGATIVE_VOTES" => $arResult["RATING"][$CurPost["ID"]]["TOTAL_NEGATIVE_VOTES"],
									"TOTAL_VALUE" => $arResult["RATING"][$CurPost["ID"]]["TOTAL_VALUE"],
									"PATH_TO_USER_PROFILE" => $arParams["~PATH_TO_USER"]
								),
								$component,
								array("HIDE_ICONS" => "Y")
							);?>
							</span>
						<?endif;?>
					</div>
					<div class="clear-float"></div>
				</div>
			</div>
		<?
	}
	if(strlen($arResult["NAV_STRING"])>0)
		echo $arResult["NAV_STRING"];
}
?>	
</div>