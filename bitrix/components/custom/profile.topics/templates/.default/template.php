<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?global $USER;?>
<?if(!empty($arResult["Posts"])):
$posts_count = count($arResult["Posts"]);
$i = 0;?>
<div id="text_space">
	<h3 class="b-hr-bg b-personal-page__heading">
		<span class="b-hr-bg__content"><?=(CUser::GetID() == $arParams["USER"]["ID"] ? "Мои записи" : "Записи")?></span>
	</h3>
	<?foreach ($arResult["Posts"] as $arItem):
	$i++;?>
	<div class="topic_item">
		<div class="chain_path"><a href="/blogs/group/<?=$arResult["SocNetBlogs"][ $arItem['BLOG_ID'] ]?>/blog/"><?=$arResult["SocNetName"][ $arResult["SocNetBlogs"][ $arItem['BLOG_ID'] ] ]['NAME']?></a></div>
		<h2><a href="/blogs/group/<?=$arResult["SocNetBlogs"][ $arItem['BLOG_ID'] ]."/blog/".$arItem['ID']?>/"><?=$arItem["TITLE"]?></a>
			<?if($USER->IsAdmin() || $USER->GetID() == $arItem['AUTHOR_ID']){?><a href="/blogs/group/<?=$arResult["SocNetBlogs"][ $arItem['BLOG_ID'] ]."/blog/edit/".$arItem['ID']?>/" class="edit" title="Редактировать запись">Редактировать запись<img src="/images/icons/edit.gif" width="7" height="12" alt="" title="Редактировать запись"></a><?}?>
		</h2>
<!--<div class="image"><a href="/recipe/"><img src="/images/infoblock/last_recipe.jpg" width="600" height="400" alt=""></a></div>-->
 		<div class="text"><?=$arItem["DETAIL_TEXT"]?></div>
		<div class="bar">
			<div class="padding">
				<div class="author"><div class="photo"><div class="big_photo"><div><img src="<?=$arResult["arUser"]["AVATAR"]['SRC']?>" width="100" height="100" alt="<?=$arResult["arUser"]['FULLNAME']?>"></div></div><img src="<?=$arResult["arUser"]["AVATAR"]['SRC']?>" width="30" height="30" alt="<?=$arResult["arUser"]['FULLNAME']?>"></div><a href="/profile/"><?=$arResult["arUser"]['FULLNAME']?></a></div>
				<div class="comments"><div class="icon"><a href="#add_opinion"><img src="/images/icons/comment.gif" width="15" height="15" alt=""></a></div><a href="/blogs/group/<?=$arResult["SocNetBlogs"][ $arItem['BLOG_ID'] ]."/blog/".$arItem['ID']?>/#add_comment">Добавить отзыв</a><span class="number">(<a href="/blogs/group/<?=$arResult["SocNetBlogs"][ $arItem['BLOG_ID'] ]."/blog/".$arItem['ID']?>/#00"><?=$arItem['NUM_COMMENTS']?></a>)</span></div>
				<?if(!empty($arItem["arDate"])):?><div class="date"><?=CFactory::humanDate($arItem["arDate"][0])?><span class="time"><?=$arItem["arDate"][1]?></span></div><?endif;?>
			</div>
		</div>
	</div>
	<?if($i != $posts_count):?><div class="border"></div><?endif;?>
	<?endforeach;?>	
	<?if(isset($arResult["NavString"])){echo $arResult["NavString"];}?>	
<?endif;?>	
</div>