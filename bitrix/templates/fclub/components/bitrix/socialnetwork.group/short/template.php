<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
//$arResult["FatalError"]
//$arResult["ErrorMessage"]
$APPLICATION->SetTitle($arResult['Group']['NAME']." &mdash; кулинарный клуб на Foodclub.ru");

$GLOBALS['Group']['NAME'] = $arResult['Group']['NAME'];
$GLOBALS['Group']['ID'] = $arResult['Group']['ID'];

if( strpos($_SERVER['REQUEST_URI'], "?") > 0){
	$subject = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], "?"));
} else {
	$subject = $_SERVER['REQUEST_URI'];
}

$pattern = '/blogs/group/'.$arResult['Group']['ID'].'/blog/'; 

?>	<?if($pattern == $subject){?>
		<h2 class="club"><?=$arResult['Group']['NAME']?><a href="<?=$arResult['Urls']['View']?>blog/rss/rss2/" class="rss"><img src="/images/icons/rss.gif" width="12" height="12" alt="RSS" title="RSS"></a></h2>
	<?} else {?>
		<h2 class="club"><a href='<?=$arResult['Urls']['View']?>blog/'><?=$arResult['Group']['NAME']?></a><a href="<?=$arResult['Urls']['View']?>blog/rss/rss2/" class="rss"><img src="/images/icons/rss.gif" width="12" height="12" alt="RSS" title="RSS"></a></h2>
	<?}?>
	<div class="club_head">
	<?if(IntVal($arResult['Group']['IMAGE_ID']) > 0){?>
		<div class="theme_pic"><img src="<?=$arResult['Group']['IMAGE_ID_FILE']['SRC']?>" 
									width="<?=$arResult['Group']['IMAGE_ID_FILE']['WIDTH']?>" 
									height="<?=$arResult['Group']['IMAGE_ID_FILE']['HEIGHT']?>" 
									alt="<?=$arResult['Group']['IMAGE_ID_FILE']['DESCRIPTION']?>">
		</div>
	<?}?>
	<div class="club_menu">
<?if($USER->IsAuthorized()){?>
	<?
	$arPerms = $arResult['CurrentUserPerms'];
	
	if($arPerms['UserIsMember']){?>
		<?if(preg_match("#^/blogs/group/([0-9]+)/blog/edit/new/#", $subject) == 0){?>
		<a href="<?=$arResult['Urls']['View']?>blog/edit/new/">Написать</a><?}else{?>
		<span>Написать</span><?}?>
		<?if(preg_match("#^/blogs/group/([0-9]+)/blog/draft/#", $subject) == 0){?>
		<a href="<?=$arResult['Urls']['View']?>blog/draft/">Черновики</a><?}else{?>
		<span>Черновики</span><?}?>
	 <?}
}?>
<a href="<?=$arResult['Urls']['View']?>">О клубе</a>
<?if($USER->IsAuthorized()){
	foreach($arResult['Urls'] as $strKey=>$strItem){
		switch ($strKey) {
			case "UserRequestGroup":
				$Temp[0] = Array($strKey=>$strItem);
			break;
			case "UserLeaveGroup":
				$Temp[1] = Array($strKey=>$strItem);
			break;
			case "GroupUsers":
				$Temp[2] = Array($strKey=>$strItem);
			break;
			case "GroupMods":
				$Temp[3] = Array($strKey=>$strItem);
			break;
			case "Edit":
				$Temp[4] = Array($strKey=>$strItem);
			break;
			case "Features":
				$Temp[5] = Array($strKey=>$strItem);
			break;
			case "GroupDelete":
				$Temp[6] = Array($strKey=>$strItem);
			break;
			default:
				;
			break;
		}
	}
	ksort($Temp);
	unset($arResult['Urls']);
	foreach ($Temp as $Key => $Item) {
		$Keys = array_keys($Item);
		$Values = array_values($Item);
		$arResult['Urls'][ $Keys[0] ] = $Values[0];
	}
	
	
	foreach($arResult['Urls'] as $strKey=>$strItem){
		switch ($strKey) {
			case "UserRequestGroup":
				if($arPerms['UserIsMember'] == false && $arPerms['UserCanAutoJoinGroup']){
					if(preg_match("#^/blogs/group/([0-9]+)/blog/edit/new/#", $subject) == 0){
						echo '<a href="'.$strItem.'">Вступить</a> ';
					}else{
						echo '<span>Вступить</span> ';
					}
				}
			break;
			case "UserLeaveGroup":
				if($arPerms['UserIsMember'] == true && $arPerms['UserIsOwner'] != true)
					echo '<a href="'.$strItem.'">Покинуть клуб</a> ';
			break;
			case "GroupUsers":
				if($arPerms['UserIsOwner'] || $arParms['UserCanModifyGroup']){
					if(preg_match("#^/blogs/group/([0-9]+)/users/#", $subject) == 0){
						echo '<a href="'.$strItem.'">Участники</a> ';
					}else{
						echo '<span>Участники</span> ';
					}
				}
			break;
			case "GroupMods":
				if($arPerms['UserIsOwner'] || $arParms['UserCanModifyGroup']){
					if(preg_match("#^/blogs/group/([0-9]+)/moderators/#", $subject) == 0){
						echo '<a href="'.$strItem.'">Модераторы</a> ';
					}else{
						echo '<span>Модераторы</span> ';
					}
				}
			break;
			case "Edit":
				if($arPerms['UserIsOwner'] || $arParms['UserCanModifyGroup']){
					if(preg_match("#^/blogs/group/([0-9]+)/edit/#", $subject) == 0){
						echo '<a href="'.$strItem.'">Редактировать</a> ';
					}else{
						echo '<span>Редактировать</span> ';
					}
				}
			break;
			case "Features":
				if($arPerms['UserIsOwner'] || $arParms['UserCanModifyGroup']){
					if($arPerms['UserIsOwner'] || $arParms['UserCanModifyGroup']){
						if(preg_match("#^/blogs/group/([0-9]+)/features/#", $subject) == 0){
							echo '<a href="'.$strItem.'">Настройки</a> ';
						}else{
							echo '<span>Настройки</span> ';
						}
					}
				}
			break;
			case "GroupDelete":
				if($arPerms['UserIsOwner'] || $arParms['UserCanModifyGroup'])
					echo '<a href="'.$strItem.'">Удалить клуб</a> ';
			break;
			/*
			case "GroupBan":
				if($arPerms['UserIsOwner'] || $arParms['UserCanModifyGroup']){
					if(preg_match("#^/blogs/group/([0-9]+)/ban/#", $subject) == 0){
						echo '<a href="'.$strItem.'">Черный список</a> ';
					}else{
						echo '<span>Черный список</span> ';
					}
				}
			break;
			case "Subscribe":
				if($arPerms['UserIsMember'])
					echo '<div class="item"><a href="'.$strItem.'">Подписка</a></div>';
			break;
			case "GroupRequestSearch":
				if($arPerms['UserCanInitiate'] == true || $arPerms['UserCanModerateGroup'])
					echo '<div class="item"><a href="'.$strItem.'">Пригласить</a></div>';
			break;
			
			case "GroupRequests":
				if($arPerms['UserIsOwner'] || $arPerms['UserCanModerateGroup'])
					echo '<div class="item"><a href="'.$strItem.'">Заявки на вступление</a></div>';
			break;
			*/
			default:
				;
			break;
		}
			
	}?>
<?}?>
		
	</div>
</div>
