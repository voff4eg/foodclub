<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?

$APPLICATION->SetTitle("О клубе «".$arResult['Group']['NAME']."»");

$strModerators = "";
foreach($arResult['Moderators']['List'] as $arItem){
	$rsUser = CUser::GetByID($arItem['USER_ID']);
	$arUser = $rsUser->Fetch();
	$strModerators .= '<div class="user">';
	if(IntVal($arItem['USER_PERSONAL_PHOTO_FILE']['ID']) > 0){
		$arPhoto = $arItem['USER_PERSONAL_PHOTO_FILE'];
		$strModerators .= '<div class="photo"><div class="big_photo"><div><img src="'.$arPhoto['SRC'].'" width="100" height="100" alt="'.$arUser['LOGIN'].'"></div></div><img src="'.$arPhoto['SRC'].'" width="30" height="30" alt="'.$arUser['LOGIN'].'"></div>';
	} else {
		$strModerators .= '<div class="photo"><div class="big_photo"><div><img src="/images/avatar/avatar.jpg" width="100" height="100" alt="'.$arUser['LOGIN'].'"></div></div><img src="/images/avatar/avatar_small.jpg" width="30" height="30" alt="'.$arUser['LOGIN'].'"></div>';
		//$strModerators .= '<div class="photo"><a href="/profile/'.$arUser['ID'].'/"><img src="/images/avatar/avatar_small.jpg" width="30" height="30" alt="'.$arUser['LOGIN'].'"></a></div>';
	}
	$strModerators .= '<a href="/profile/'.$arUser['ID'].'/">'.$arUser['LOGIN'].'</a></div>';
}

$strMembersList = "";
foreach($arResult['Members']['List'] as $arItem){
	$rsUser = CUser::GetByID($arItem['USER_ID']);
	$arUser = $rsUser->Fetch();
	$strMembersList .= "<a href='/profile/".$arUser['ID']."'>".$arUser['LOGIN']."</a>, ";
}
?>
<h1><a href="/blogs/group/<?=$arResult['Group']['ID']?>/blog/"><?=$arResult['Group']['NAME']?></a></h1>
<?if( strlen($arResult['Group']['IMAGE_ID_FILE']['ID']) > 0 ){?>
	<div class="theme_pic"><img src="<?=$arResult['Group']['IMAGE_ID_FILE']['SRC']?>" 
								width="<?=$arResult['Group']['IMAGE_ID_FILE']['WIDTH']?>" 
								height="<?=$arResult['Group']['IMAGE_ID_FILE']['HEIGHT']?>" 
								alt="<?=$arResult['Group']['IMAGE_ID_FILE']['DESCRIPTION']?>"></div>
<?}?>
<?if($USER->IsAuthorized()){?>
<div class="club_menu">
	<?
	$arPerms = $arResult['CurrentUserPerms'];
	
	
if($arPerms['UserIsMember']){?>
		<?if(preg_match("#^/blogs/group/([0-9]+)/blog/edit/new/#", $subject) == 0){?>
		<a href="<?=$arResult['Urls']['View']?>blog/edit/new/">Написать</a> <?}else{?>
		<span>Написать</span> <?}?>
		<?if(preg_match("#^/blogs/group/([0-9]+)/blog/draft/#", $subject) == 0){?>
		<a href="<?=$arResult['Urls']['View']?>blog/draft/">Черновики</a> <?}else{?>
		<span>Черновики</span> <?}?>
	 <?}
	 ?><span>О клубе</span><?
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
			case "GroupMods":
				if($arPerms['UserIsOwner'] || $arParms['UserCanModifyGroup']){
					if(preg_match("#^/blogs/group/([0-9]+)/moderators/#", $subject) == 0){
						echo '<a href="'.$strItem.'">Модераторы</a> ';
					}else{
						echo '<span>Модераторы</span> ';
					}
				}
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
			case "UserLeaveGroup":
				if($arPerms['UserIsMember'] == true && $arPerms['UserIsOwner'] != true)
					echo '<a href="'.$strItem.'">Выйти</a> ';
			break;
			case "GroupDelete":
				if($arPerms['UserIsOwner'] || $arParms['UserCanModifyGroup'])
					echo '<a href="'.$strItem.'">Удалить группу</a> ';
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
			case "Edit":
				if($arPerms['UserIsOwner'] || $arParms['UserCanModifyGroup']){
					if(preg_match("#^/blogs/group/([0-9]+)/edit/#", $subject) == 0){
						echo '<a href="'.$strItem.'">Редактировать</a> ';
					}else{
						echo '<span>Редактировать</span> ';
					}
				}
			break;
			
			/*
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
			case "GroupBan":
				if($arPerms['UserIsOwner'] || $arParms['UserCanModifyGroup']){
					if(preg_match("#^/blogs/group/([0-9]+)/ban/#", $subject) == 0){
						echo '<a href="'.$strItem.'">Черный список</a> ';
					}else{
						echo '<span>Черный список</span> ';
					}
				}
			break;
			*/
			default:
				;
			break;
		}
			
	}?> 
	
</div>
<?}?>
<div class="about_club">
	<h2>О клубе</h2>
	<p><?=nl2br($arResult['Group']['~DESCRIPTION'])?></p>
</div>
<div class="moderators_list">
	<h5>Модераторы</h5>
	<?=$strModerators?>
	<div class="clear"></div>
</div>
<div class="members_list">
	<h5>Участники</h5>
	<?=substr($strMembersList, 0, -2)?>
</div>

