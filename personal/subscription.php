<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Подписка");?>

<?$APPLICATION->SetAdditionalCSS("/css/profile.css");
$APPLICATION->SetAdditionalCSS("/css/elem.css");?>

<?
if (CModule::IncludeModule("advertising")){
    $strBanner = CAdvBanner::Show("right_banner");
}

CModule::IncludeModule("subscribe");
include($_SERVER['DOCUMENT_ROOT'].'/classes/factory.class.php');

if($USER->IsAuthorized())
{
    $UserId = IntVal($USER->GetId());
    CSubscription::Authorize($UserId);
    $Email = $_SESSION['SESS_AUTH']['EMAIL'];
}
else
{
    LocalRedirect("/auth/?backurl=/profile/subscribe/");
}

$aSb = CSubscription::GetUserSubscription();
$aSRub = CSubscription::GetRubricArray($aSb['ID']);

/*
$arFields = Array(
    "USER_ID" => $UserId,
    "FORMAT" => "html",
    "EMAIL" => $Email,
    "ACTIVE" => "Y",
    "RUB_ID" => array("1"),
    "SEND_CONFIRM" => "N",
);

$rsSub = new CSubscription;
$ID = $rsSub->Add($arFields);
if( intval($ID) > 0 )
{
    $elSub = $rsSub->GetByID($ID);
    $arSub = $elSub->ExtractFields();
    $rsSub->Update($ID, array("CONFIRM_CODE"=>$arSub['CONFIRM_CODE']));
}
*/


$arUser = $APPLICATION->IncludeComponent("custom:profile", "", Array());
$APPLICATION->SetPageProperty("title", $arUser['FULLNAME']." &mdash; рецепты пользователя на Foodclub");

?><div id="content">
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
			<h3 class="b-hr-bg b-personal-page__heading">
				<span class="b-hr-bg__content">Подписка</span>
			</h3>
	        <form action="/personal/sub.post.php" method="post" class="subscription_list">
		        <input  type="hidden"
		                name="user_rub"
		                value="<?=( intval($aSb['ID']) > 0 ? $aSb['ID'] : 'is_new')?>">
			    
			    <?$rsRub = CRubric::GetList(array("SORT"=>"ASC", "NAME"=>"ASC"), array("ACTIVE"=>"Y", "LID"=>LANG));
			    while($arRub = $rsRub->GetNext()):
			        $curStatus = ( in_array($arRub['ID'], $aSRub) ? 1 : 0);?>
			        <div class="item">
						<div class="checkbox">
						    <input  type="checkbox"
						            name="rs_SUB[]"
						            value="<?=$arRub['ID']?>"
						            <?=($curStatus ? "checked" : "")?> >
						    <input type="hidden" name="cur_sub_val[<?=$arRub['ID']?>]" value="<?=$curStatus?>">
						 </div>
						<div class="text">
							<h2><?=$arRub['NAME']?></h2>
							<p><?=$arRub['DESCRIPTION']?></p>
						</div>
						<div class="image"><img src="/images/infoblock/subscriptions/1.jpg" width="106" height="118" alt=""></div>
						<div class="clear"></div>
					</div>
		    	<?endwhile;?>

	            <!--input type="submit" value="" class="submit button"-->
				<button type="submit" class="b-button b-button__width_100">Сохранить</button>
			</form>
	    </div>
		<div id="banner_space">
			<?
			/*$rsRandomDoUKnow = CIBlockElement::GetList(array("rand"=>"asc"),array("IBLOCK_CODE"=>"do_u_know"),false,false,array("ID","PREVIEW_TEXT"));
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
			<?}*/?>
			<?if(strlen($strBanner) > 0){?><div class="banner"><?=$strBanner?></div><?}?>
		</div>
		<div class="clear"></div>
	</div>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>

