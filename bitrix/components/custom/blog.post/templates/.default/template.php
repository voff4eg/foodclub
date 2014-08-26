<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$APPLICATION->SetTitle($arResult['Post']['TITLE']." &mdash; клуб «".str_replace("Блог группы ","", $arResult['Blog']['NAME'])."»");

if(strlen($arResult["MESSAGE"]) > 0){
	?><div class="system_message">
	<div class="pointer"></div>
	<div class="padding"><h2><?
	echo $arResult["MESSAGE"];
	?></h2></div>
	</div><?
}
if(strlen($arResult["ERROR_MESSAGE"]) > 0){
	?><div class="system_message">
	<div class="pointer"></div>
	<div class="padding"><h2><?
	echo $arResult["ERROR_MESSAGE"];
	?></h2></div>
	</div><?
}
if(strlen($arResult["FATAL_MESSAGE"]) > 0){
	?><div class="system_message">
	<div class="pointer"></div>
	<div class="padding"><h2><?
	echo $arResult["FATAL_MESSAGE"];
	?></h2></div>
	</div><?
}
if(strlen($arResult["NOTE_MESSAGE"]) > 0){
	?><div class="system_message">
	<div class="pointer"></div>
	<div class="padding"><h2><?
	echo $arResult["NOTE_MESSAGE"];
	?></h2></div>
	</div><?
}

$arDate = explode(" ", $arResult["Post"]["DATE_PUBLISH_FORMATED"]);

$arAvatar = Array();
		
if(intval($arResult['arUser']['PERSONAL_PHOTO']) > 0){
	$rsAvatar = CFile::GetByID($arResult['arUser']['PERSONAL_PHOTO']);
	$arAvatar = $rsAvatar->Fetch();
	$arAvatar['SRC'] = "/upload/".$arAvatar['SUBDIR']."/".$arAvatar['FILE_NAME'];
} else {
	$arAvatar['SRC'] = "/images/avatar/avatar.jpg";
}
if(strlen($arResult['arUser']["NAME"]) > 0 && strlen($arResult['arUser']["LAST_NAME"]) > 0){
	$arResult['arUser']['FULLNAME'] = $arResult['arUser']["NAME"]." ".$arResult['arUser']["LAST_NAME"];
}else{
	$arResult['arUser']["FULLNAME"] = $arResult['arUser']['LOGIN'];	 	
}
?>
<style>
@media print {
body, #logo strong, div.bar div.date, div.bar div.date span {color:#000000;}
#top_panel, #top_banner, #recipe_search, #iphone_link, #topbar, #banner_space, div.club_menu, div.topic_item div.tags, div.bar div.rating, div.bar div.comments, div.comments_block, #comment_form, #bottom, #bottom_nav, h1 a.edit, h1 a.delete {display:none;}
a {
text-decoration:none;
color:#000000;}
div.bar {padding:20px 0 70px 0;}
#body, #content {width:700px;}
#body div.padding {padding:0;}
}
</style>
	<div class="topic_item" id="topic_id">
		<?if(strLen($arResult["urlToEdit"]) > 0 || strLen($arResult["urlToDelete"]) > 0):?>
		    <div class="admin_panel">
		        <noindex>
			<?if(strLen($arResult["urlToEdit"]) > 0):?>
			    <a id="html_code" href="#">HTML-код</a>
			    <a title="Редактировать запись" href="<?=$arResult["urlToEdit"]?>" class="edit">Редактировать запись</a>
			<?endif;?>
			<?if(strLen($arResult["urlToDelete"]) > 0):?>
	    			<a title="Удалить запись" class="delete" href="<?=$arResult["urlToDelete"]?>">Удалить запись</a>
		        <?endif;?>
		    </noindex>
		    </div>
		<?endif;?>
		<h1><?=$arResult['Post']['TITLE']?></h1>
		<div class="text">
			<?=$arResult["Post"]["textFormated"]?>
		</div>
	<?if(count($arResult['Category']) > 0){ $bFirst = true;?>
		<div class="tags">
			<h5>Метки</h5>
		<?foreach($arResult['Category'] as $Item){?><?if(!$bFirst){echo ", ";}else{$bFirst = false;}?><a href="<?=$Item['urlToCategory']?>"><?=$Item['NAME']?></a><?}?>
		</div>
	<?}?>
		<div class="date"><?=$arDate[0]?><span class="time"><?=$arDate[1]?></span></div>
		<div class="bar">
			<div class="padding">
				<div class="author">
					<div class="photo">
						<div class="big_photo">
							<div>
								<img src="<?=$arAvatar['SRC']?>" width="100" height="100" alt="<?=$arResult['arUser']['FULLNAME']?>">
							</div>
						</div>	
						<img src="<?=$arAvatar['SRC']?>" width="30" height="30" alt="<?=$arResult['arUser']['FULLNAME']?>">
					</div>
					<a href="/profile/<?=$arResult['arUser']['ID']?>/"><?=$arResult['arUser']['FULLNAME']?></a>
				</div>
				<div class="comments">
					<a class="add" href="#add_comment">Комментировать</a>
					<span class="number">(<a href="#00"><?=$arResult['Post']['NUM_COMMENTS']?></a>)</span>
				</div>
				<?/*<script charset="utf-8" src="//yandex.st/share/share.js" type="text/javascript"></script>
<div data-yasharequickservices="yaru,vkontakte,facebook,twitter,odnoklassniki,moimir" data-yasharetype="link" class="yashare-auto-init"><span class="b-share"><a data-vdirection="" data-hdirection="" id="ya-share-0.07947865051645653-1300444008268" class="b-share__handle"><span class="b-share__text">Поделиться</span></a><a data-service="yaru" href="http://share.yandex.ru/go.xml?service=yaru&amp;url=http%3A%2F%2Fmarkup.foodclub.2px%2Fclubs%2Fclub%2Ftopic%2F&amp;title=Foodclub%20%E2%80%94%20%D0%A2%D0%BE%D0%BF%D0%B8%D0%BA" class="b-share__handle b-share__link" title="Я.ру" target="_blank" rel="nofollow"><span class="b-share-icon b-share-icon_yaru"></span></a><a data-service="vkontakte" href="http://share.yandex.ru/go.xml?service=vkontakte&amp;url=http%3A%2F%2Fmarkup.foodclub.2px%2Fclubs%2Fclub%2Ftopic%2F&amp;title=Foodclub%20%E2%80%94%20%D0%A2%D0%BE%D0%BF%D0%B8%D0%BA" class="b-share__handle b-share__link" title="Вконтакте" target="_blank" rel="nofollow"><span class="b-share-icon b-share-icon_vkontakte"></span></a><a data-service="facebook" href="http://share.yandex.ru/go.xml?service=facebook&amp;url=http%3A%2F%2Fmarkup.foodclub.2px%2Fclubs%2Fclub%2Ftopic%2F&amp;title=Foodclub%20%E2%80%94%20%D0%A2%D0%BE%D0%BF%D0%B8%D0%BA" class="b-share__handle b-share__link" title="Facebook" target="_blank" rel="nofollow"><span class="b-share-icon b-share-icon_facebook"></span></a><a data-service="twitter" href="http://share.yandex.ru/go.xml?service=twitter&amp;url=http%3A%2F%2Fmarkup.foodclub.2px%2Fclubs%2Fclub%2Ftopic%2F&amp;title=Foodclub%20%E2%80%94%20%D0%A2%D0%BE%D0%BF%D0%B8%D0%BA" class="b-share__handle b-share__link" title="twitter" target="_blank" rel="nofollow"><span class="b-share-icon b-share-icon_twitter"></span></a><a data-service="odnoklassniki" href="http://share.yandex.ru/go.xml?service=odnoklassniki&amp;url=http%3A%2F%2Fmarkup.foodclub.2px%2Fclubs%2Fclub%2Ftopic%2F&amp;title=Foodclub%20%E2%80%94%20%D0%A2%D0%BE%D0%BF%D0%B8%D0%BA" class="b-share__handle b-share__link" title="Одноклассники" target="_blank" rel="nofollow"><span class="b-share-icon b-share-icon_odnoklassniki"></span></a><a data-service="moimir" href="http://share.yandex.ru/go.xml?service=moimir&amp;url=http%3A%2F%2Fmarkup.foodclub.2px%2Fclubs%2Fclub%2Ftopic%2F&amp;title=Foodclub%20%E2%80%94%20%D0%A2%D0%BE%D0%BF%D0%B8%D0%BA" class="b-share__handle b-share__link" title="Мой Мир" target="_blank" rel="nofollow"><span class="b-share-icon b-share-icon_moimir"></span></a></span></div>*/?>
			</div>
		</div>

			<div class="b-social-buttons">
				<div class="b-social-buttons__item b-vk-like">
					<div id="vk_like1"></div>
					<script type="text/javascript">
						VK.Widgets.Like("vk_like1", {type: "mini", height: 20});
					</script>
				</div>
				<div class="b-social-buttons__item b-twitter-like">
					<a href="https://twitter.com/share" class="twitter-share-button">Tweet</a>
					<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>				
				</div>
				<div class="b-social-buttons__item b-fb-like"><fb:like send="false" layout="button_count" width="50" show_faces="true"></fb:like></div>
				<div class="b-social-buttons__item b-surf-like">
					<a target="_blank" class="surfinbird__like_button" data-surf-config="{'layout': 'common', 'width': '100', 'height': '20'}" href="http://surfingbird.ru/share">Серф</a>
					<script type="text/javascript" charset="UTF-8" src="http://surfingbird.ru/share/share.min.js"></script>
				</div>
				<div class="b-social-buttons__item b-pin-like">
					<a target="_blank"  href="http://pinterest.com/pin/create/button/?url=foodclub.ru<?=$APPLICATION->GetCurPage()?>&media=http://<?=$_SERVER["SERVER_NAME"].$arResult["image"]?>&description=<?=urlencode($arResult["Post"]["TITLE"])?>" class="pin-it-button" count-layout="horizontal"><img border="0" src="//assets.pinterest.com/images/PinExt.png" title="Pin It" /></a>
				</div>
				<div class="b-social-buttons__item b-ya-share">
					<script type="text/javascript" src="//yandex.st/share/share.js" charset="utf-8"></script>
					<div class="yashare-auto-init" data-yashareL10n="ru" data-yashareType="icon" data-yashareQuickServices="yaru,vkontakte,facebook,twitter,odnoklassniki"></div> 
				</div>
				<div class="i-clearfix"></div>
			</div>
		
	</div>