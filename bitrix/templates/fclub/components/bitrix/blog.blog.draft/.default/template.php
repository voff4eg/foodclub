
<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if(!empty($arResult["OK_MESSAGE"])){?>
	<div class="system_message">
	<div class="pointer"></div>
	<div class="padding">
	<?foreach($arResult["OK_MESSAGE"] as $v){?><h2><?=$v?></h2><?}
	?></div>
	</div><?
}
if(!empty($arResult["MESSAGE"])){?>
	<div class="system_message">
	<div class="pointer"></div>
	<div class="padding">
	<?foreach($arResult["MESSAGE"] as $v){?><h2><?=$v?></h2><?}
	?></div>
	</div><?
}
if(!empty($arResult["ERROR_MESSAGE"])){?>
	<div class="system_message">
	<div class="pointer"></div>
	<div class="padding">
	<?foreach($arResult["ERROR_MESSAGE"] as $v){?><h2><?=$v?></h2><?}
	?></div>
	</div><?}

// Factory class
include($_SERVER['DOCUMENT_ROOT'].'/classes/factory.class.php');

if(count($arResult["POST"])>0){
	$rsUser = CUser::GetByID($USER->GetID());
	$arUser = $rsUser->Fetch();
	
	if(intval($arUser['PERSONAL_PHOTO']) > 0){
		$rsAvatar = CFile::GetByID($arUser['PERSONAL_PHOTO']);
		$arAvatar = $rsAvatar->Fetch();
		$arAvatar['SRC'] = "/upload/".$arAvatar['SUBDIR']."/".$arAvatar['FILE_NAME'];
	} else {
		$arAvatar['SRC'] = "/images/avatar/avatar_small.jpg";
	}
	echo "<!--<pre>";print_r($arParams);echo "</pre>-->";
	$bFirst = true;
	foreach($arResult["POST"] as $CurPost){
									
		$arDate = explode(" ", $CurPost['DATE_PUBLISH']);
		if(!$bFirst){
			echo '<div class="border"></div>';
		} else {
			$bFirst = false;
		}
	?>
		<div class="topic_item">
			<h2>
				<?=$CurPost['TITLE']?>
				<?if(strLen($CurPost["urlToEdit"])>0):?>
				<a href="<?=str_replace("#group_id#", $arParams["SOCNET_GROUP_ID"], $CurPost["urlToEdit"])?>" class="edit">
					<img src="/images/icons/edit.gif" width="7" height="12" alt="" title="Редактировать рецепт">
				</a>
				<?endif;?>
				<?if(strlen($CurPost['urlToDelete'])>0){?>
				<a class="delete" href="javascript:if(confirm('Вы действительно хотите удалить этот пост?')) window.location='<?=$CurPost["urlToDelete"]?>'">
					<img width="9" height="9" title="Удалить" alt="" src="/images/icons/delete.gif"/>
				</a>
				<?}?>
			</h2>
			<div class="text">
				<?=$CurPost['TEXT_FORMATED']?>
			</div>
			<div class="bar">
				<div class="padding">
					<div class="date"><?=CFactory::humanDate($arDate[0])?><span class="time"><?=$arDate[1]?></span></div>
				</div>
			</div>
		</div>
		<?
	}
} else {
	?><div class="system_message">
	<div class="pointer"></div>
	<div class="padding"><h2><?
	echo GetMessage("B_B_DRAFT_NO_MES");
	?></h2></div>
	</div><?
}
?>	