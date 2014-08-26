<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if(!empty($arResult["comments"])):?>
<div id="text_space">
	<h3 class="b-hr-bg b-personal-page__heading">
		<span class="b-hr-bg__content"><?=(CUser::GetID() == $arParams["USER"]["ID"] ? "Мои комментарии" : "Комментарии")?></span>
	</h3>
	<ul class="comments_list">
		<?foreach($arResult["comments"] as $comment):?>
		<li><p><?=$comment["POST_TEXT"]?></p>
		<p class="sign">
			<?$arDate = explode(" ", $comment['DATE_CREATE']);?>
			<?=CFactory::humanDate($arDate[0])?> к топику 
			<a href="/blogs/group/<?=$arResult["SocNetBlogs"][ $comment['BLOG_ID'] ]."/blog/".$comment['POST_ID']?>/">
				<?=$arResult["TopicsName"][ $comment['POST_ID'] ]['TITLE']?></a> в клубе 
			<a href="/blogs/group/<?=$arResult["SocNetBlogs"][ $comment['BLOG_ID'] ]?>/blog/">
				<?=$arResult["SocNetName"][ $arResult["SocNetBlogs"][ $comment['BLOG_ID'] ] ]['NAME']?></a></p></li>		
		<?endforeach;?>
	</ul>	
	<?if(isset($NavString)){echo $NavString;}?>
</div>
<?else:?>
<h2>У меня пока нет комментариев.</h2>
<?endif;?>