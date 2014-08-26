<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
define("NO_KEEP_STATISTIC", true);
require_once($_SERVER["DOCUMENT_ROOT"]."/classes/factory.class.php");
$Factory = new CFactory;
$arUser = $arParams["USER"];
//$APPLICATION->AddHeadString('<link href="/css/styles-with-smartsearch.css" type="text/css" rel="stylesheet" />',true);
$link = "";
if(intval($_REQUEST["u"]) <= 0){
	if($APPLICATION->GetCurDir() != "/profile/"){
		$link = "/profile/";
	}
}elseif($APPLICATION->GetCurDir() != "/profile/".$arUser["ID"]."/"){
	$link = "/profile/".$arUser["ID"]."/";
}
?>
<div class="b-personal-page__intro">

	<?$APPLICATION->IncludeComponent("custom:profile_avatar",
	"",
	Array("USER_ID"=>$arUser["ID"]),
	false
	);?>	
	<div class="b-personal-page__intro__card b-personal-card">
		<div class="b-personal-card__pic">
		<?if(strlen($link)):?>
		<a href="<?=$link?>" class="b-personal-card__pic__link"><img src="<?=$arUser["AVATAR"]['SRC']?>" width="100" height="100" alt="<?=((strlen($arUser["NAME"]) > 0 && strlen($arUser["LAST_NAME"]) > 0) ? $arUser["LAST_NAME"]." ".$arUser["NAME"] : $arUser["LOGIN"])?>" class="b-personal-card__pic__image"></a>
		<?else:?>
		<span class="b-personal-card__pic__link"><img src="<?=$arUser["AVATAR"]['SRC']?>" width="100" height="100" alt="<?=((strlen($arUser["NAME"]) > 0 && strlen($arUser["LAST_NAME"]) > 0) ? $arUser["LAST_NAME"]." ".$arUser["NAME"] : $arUser["LOGIN"])?>" class="b-personal-card__pic__image"></span>
		<?endif;?>
		</div>
		<div class="b-personal-card__info">
			<h1 class="b-personal-card__name">
				<?if(strlen($arUser["NAME"]) > 0 && strlen($arUser["LAST_NAME"]) > 0):?>
				<div class="b-personal-card__name__first"><?=$arUser["NAME"]?> <?=$arUser["LAST_NAME"]?><?if(CUser::GetID() == $arUser["ID"]):?> <a href="/profile/edit/" class="b-edit-button" title="Редактировать анкету"></a><?endif;?></div>
				<?if(strlen($arUser["SECOND_NAME"])):?>
				<div class="b-personal-card__name__patronymic"><?=$arUser["SECOND_NAME"]?></div>
				<?endif;?>
				<?else:?>
				<div class="b-personal-card__name__first"><?=$arUser["LOGIN"]?><?if(CUser::GetID() == $arUser["ID"]):?> <a href="/profile/edit/" class="b-edit-button" title="Редактировать анкету"></a><?endif;?></div>
				<?endif;?>
			</h1>
			<div class="b-personal-card__position"><?=$arUser["UF_INFO_STATUS"]?></div>
			<div class="b-personal-card__town"><?=$arUser["PERSONAL_CITY"]?></div>
			<div class="b-personal-card__date">С нами с <?=$Factory->humanDate($arUser["DATE_REGISTER"])?></div>						
			<?if(!$arUser["UF_NO_BIRTHDAY"] && strlen($arUser["PERSONAL_BIRTHDAY"]) > 0):?><div class="b-personal-card__age"><?=FormatDate("Ydiff",strtotime($arUser["PERSONAL_BIRTHDAY"]))?></div><?endif;?>
		</div>
	</div>
	
	<div class="b-personal-page__intro__achievements b-achievements">
		<div class="b-achievements__items">
			<span class="b-achievements__item">
				<span class="b-achievements__item__num"><?=intval($arUser["RECIPES_COUNT"])?></span>
				<span class="b-achievements__item__br"></span>
				<span class="b-achievements__item__title"><?=$Factory->plural_form($arUser["RECIPES_COUNT"],array("Рецепт","Рецепта","Рецептов"))?></span>
			</span>
			
			<span class="b-achievements__item">
				<span class="b-achievements__item__num"><?=intval($arUser["POSTS_COUNT"])?></span>
				<span class="b-achievements__item__br"></span>
				<span class="b-achievements__item__title"><?=$Factory->plural_form($arUser["POSTS_COUNT"],array("Запись","Записи","Записей"))?></span>
			</span>
			
			<span class="b-achievements__item">
				<span class="b-achievements__item__num"><?=intval($arUser["REPLIES_COUNT"])?></span>
				<span class="b-achievements__item__br"></span>
				<span class="b-achievements__item__title"><?=$Factory->plural_form($arUser["REPLIES_COUNT"],array("Отзыв","Отзыва","Отзывов"))?></span>
			</span>

			<span class="b-achievements__item">
				<span class="b-achievements__item__num"><?=intval($arUser["COMMENTS_COUNT"])?></span>
				<span class="b-achievements__item__br"></span>
				<span class="b-achievements__item__title"><?=$Factory->plural_form($arUser["COMMENTS_COUNT"],array("Комментарий","Комментария","Комментариев"))?></span>
			</span>
			
			<!-- <span class="b-achievements__item">
				<span class="b-achievements__item__num">34</span>
				<span class="b-achievements__item__br"></span>
				<span class="b-achievements__item__title">Рецепта<br>готовила</span>
			</span> -->
		</div>
		
		<div class="b-achievements__summ">
			<span class="b-achievements__item">
				<span class="b-achievements__item__num"><?=intval($arUser["UF_RAITING"])?></span>
				<span class="b-achievements__item__br"></span>
				<span class="b-achievements__item__title"><?=$Factory->plural_form($arUser["UF_RAITING"],array("Балл","Балла","Баллов"))?></span>
			</span>
		</div>
	</div>
	
	<div class="i-clearfix"></div>
</div>