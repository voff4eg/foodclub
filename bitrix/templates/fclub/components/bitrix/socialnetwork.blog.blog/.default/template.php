<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
//$APPLICATION->SetTitle(str_replace("Блог группы ","", $arResult['BLOG']['NAME'])."&mdash; кулинарный клуб на Foodclub");?>
<?
if(!empty($arResult["OK_MESSAGE"])){
	?><div class="system_message">
		<div class="pointer"></div>
		<div class="padding"><?
	foreach($arResult["OK_MESSAGE"] as $v){?>
		<h2><?=$v?></h2>
	<?}
	?></div>
	</div><?
}
if(!empty($arResult["MESSAGE"])){
	?><div class="system_message">
		<div class="pointer"></div>
		<div class="padding"><?
	foreach($arResult["MESSAGE"] as $v){?>
	<h2><?=$v?></h2>
	<?}
	?></div>
	</div><?
}
if(!empty($arResult["ERROR_MESSAGE"])){
	?><div class="system_message">
		<div class="pointer"></div>
		<div class="padding"><?
	foreach($arResult["ERROR_MESSAGE"] as $v){?>
	<h2><?=$v?></h2>
	<?}?></div>
	</div><?
}

// Factory class
include($_SERVER['DOCUMENT_ROOT'].'/classes/factory.class.php');
$bFirst = true;

if(isset($_REQUEST['category'])){
	if(intval($_REQUEST['category']) > 0){
		$Category = CBlogCategory::GetByID( intval($_REQUEST['category']) );
		?>
		<div class="system_message">
			<div class="pointer"></div>
			<div class="padding">
				<h2>Темы, отмеченные как &mdash; <?=$Category['NAME']?>.</h2>
			</div>
		</div>
		
		<?
	}
}

if(count($arResult["POST"])>0){
	foreach($arResult["POST"] as $CurPost){
		
		$arAvatar = Array();
		
		if(intval($CurPost['arUser']['PERSONAL_PHOTO']) > 0){
			$rsAvatar = CFile::GetByID($CurPost['arUser']['PERSONAL_PHOTO']);
			$arAvatar = $rsAvatar->Fetch();
			$arAvatar['SRC'] = "/upload/".$arAvatar['SUBDIR']."/".$arAvatar['FILE_NAME'];
		} else {
			$arAvatar['SRC'] = "/images/avatar/avatar.jpg";
		}
		
		$arDate = explode(" ", $CurPost['DATE_PUBLISH']);
	?>
		<?if($bFirst == false){?><div class="border"></div><?}?>
		<div class="topic_item">
			<h2>
				<a href="<?=$CurPost['urlToPost']?>"><?=$CurPost['TITLE']?></a>
				<?if(strLen($CurPost["urlToEdit"])>0):?>
				<a href="<?=$CurPost["urlToEdit"]?>" class="edit"><img src="/images/icons/edit.gif" width="7" height="12" alt="" title="Редактировать"></a><?endif;?>
				<?if(strLen($CurPost["urlToDelete"])>0){?>
				<a class="delete" href="javascript:if(confirm('<?=GetMessage("BLOG_MES_DELETE_POST_CONFIRM")?>')) window.location='<?=$CurPost["urlToDelete"]."&".bitrix_sessid_get()?>'"><img width="9" height="9" title="Удалить" alt="" src="/images/icons/delete.gif"/></a><?}?>
			</h2>
			<div class="text">
				<?=$CurPost['TEXT_FORMATED']?>
				<?if($CurPost['CUT'] == "Y"){?>
					<div class="fcut"><a href="<?=$CurPost['urlToPost']?>#cut">Подробнее</a></div>
				<?}?>
			</div>
		<?if(isset($CurPost['CATEGORY'])){ $bFirst = true;?>
			<div class="tags">
			<h5>Метки</h5>
			<?foreach($CurPost['CATEGORY'] as $Item){?><?if(!$bFirst){echo ", ";}else{$bFirst = false;}?><a href="<?=$Item['urlToCategory']?>"><?=$Item['NAME']?></a><?}?>
			</div>
		<?}?>
			<div class="bar">
				<div class="padding">
					<div class="author">
						<div class="photo">
							<div class="big_photo">
								<div>
									<img src="<?=$arAvatar['SRC']?>" width="100" height="100" alt="<?=$CurPost['arUser']['LOGIN']?>">
								</div>
							</div>	
							<img src="<?=$arAvatar['SRC']?>" width="30" height="30" alt="<?=$CurPost['arUser']['LOGIN']?>">
						</div>
						<a href="/profile/<?=$CurPost['arUser']['ID']?>/"><?=$CurPost['arUser']['LOGIN']?></a>
					</div>
					<div class="comments">
						<div class="icon">
							<a href="#add_comment">
								<img src="/images/icons/comment.gif" width="15" height="15" alt="">
							</a>
						</div>
						<a href="<?=$CurPost['urlToPost']?>#add_comment">
							Комментировать</a>
						<span class="number">
							(<a href="<?=$CurPost['urlToPost']?>#00"><?=$CurPost['NUM_COMMENTS']?></a>)
						</span>
					</div>
					<div class="date"><?=CFactory::humanDate($arDate[0])?><span class="time"><?=$arDate[1]?></span></div>
				</div>
			</div>
		</div>
		<?
		$bFirst = false;
	}
	if(strlen($arResult["NAV_STRING"])>0)
		echo $arResult["NAV_STRING"];
}
elseif(empty($arResult["MESSAGE"])){
	?><div class="system_message">
		<div class="pointer"></div>
		<div class="padding"><h2><?
	echo GetMessage("BLOG_BLOG_BLOG_NO_AVAIBLE_MES");
	?></h2></div>
	</div><?
}
?>	
