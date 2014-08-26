<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?//echo "<pre>";print_R($arResult);echo "</pre>";?>
<?if(!empty($arResult["Posts"])):
$posts_count = count($arResult["Posts"]);
$i = 0;?>
<div id="text_space">
	<h3 class="b-hr-bg b-personal-page__heading">
		<span class="b-hr-bg__content"><?=(CUser::GetID() == $arParams["USER"]["ID"] ? "Моя лента" : "Лента")?></span>
	</h3>
	<?foreach ($arResult["Posts"] as $arItem):
	//echo "<pre>";print_r($arItem);echo "</pre>";
	$i++;?>
	<div class="topic_item">
		<div class="chain_path"><a href="/blogs/group/<?=$arResult["arBlogs"][ $arItem['BLOG_ID'] ]['ID']?>/blog/"><?=$arResult["arBlogs"][ $arItem['BLOG_ID'] ]['NAME']?></a></div>
		<h2><a href="/blogs/group/<?=$arResult["arBlogs"][ $arItem['BLOG_ID'] ]['ID']."/blog/".$arItem['ID']?>/"><?=$arItem["TITLE"]?></a>
		<?if(strlen($arItem["urlToEdit"])):?><a href="/blogs/group/<?=$arResult["arBlogs"][ $arItem['BLOG_ID'] ]['ID']."/blog/edit/".$arItem['ID']?>/" class="edit" title="Редактировать запись">Редактировать запись<img src="/images/icons/edit.gif" width="7" height="12" alt="" title="Редактировать запись"></a><?endif;?></h2>
 		<div class="text"><?=$arItem["DETAIL_TEXT"]?></div>
		<div class="bar">
			<div class="padding">
				<div class="author"><div class="photo"><div class="big_photo"><div><img src="<?=$arItem["USER"]["AVATAR"]['SRC']?>" width="100" height="100" alt="<?=$arItem["USER"]['FULLNAME']?>"></div></div><img src="<?=$arItem["USER"]["AVATAR"]['SRC']?>" width="30" height="30" alt="<?=$arItem["USER"]['FULLNAME']?>"></div><a href="/profile/"><?=$arItem["USER"]['FULLNAME']?></a></div>
				<div class="comments"><div class="icon"><a href="#add_opinion"><img src="/images/icons/comment.gif" width="15" height="15" alt=""></a></div><a href="/blogs/group/<?=$arResult["SocNetBlogs"][ $arItem['BLOG_ID'] ]."/blog/".$arItem['ID']?>/#add_comment">Добавить отзыв</a><span class="number">(<a href="/blogs/group/<?=$arResult["SocNetBlogs"][ $arItem['BLOG_ID'] ]."/blog/".$arItem['ID']?>/#00"><?=$arItem['NUM_COMMENTS']?></a>)</span></div>
				<?if(!empty($arItem["DATE"])):?><div class="date"><?=CFactory::humanDate($arItem["DATE"][0])?><span class="time"><?=$arItem["DATE"][1]?></span></div><?endif;?>
			</div>
		</div>
	</div>
	<?if($i != $posts_count):?><div class="border"></div><?endif;?>
	<?endforeach;?>
	<?if(isset($arResult["NavString"])){echo $arResult["NavString"];}?>
<?endif;?>	
</div>