<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
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

?>
<div class="comments_block">
	<div id="sessId"><?=bitrix_sessid()?></div>
	<div id="reply_form">
	<?if( $USER->IsAuthorized() ){?>
		<div class="close_icon"><div></div></div>
		<form method="POST" id="comment" action="<?=POST_FORM_ACTION_URI?>">
			<div class="form_field">
				<h4>Ответить<span>?</span></h4>
				<input type="hidden" name="parentId" value="">
				<?=bitrix_sessid_post()?>
				<div class="textarea"><textarea cols="10" rows="10" name="comment"></textarea></div>
				<input type="hidden" name="post" value="Y">
				<input class="button" type="submit" value="Ответить"/>
			</div>
		</form>
	<?} else {?>

		<div class="comment foodclub">
			<div class="icons">
				<div class="pointer"></div>
			</div>
			<div class="padding">
				<div class="text">
					Если Вы хотите оставить комментарий, Вам необходимо <a href="/auth/?backurl=/blogs/group/<?=$arResult['Blog']['SOCNET_GROUP_ID']?>/blog/<?=$arResult['Post']['ID']?>/">авторизоваться</a> или <a href="/registration/?backurl=/blogs/group/<?=$arResult['Blog']['SOCNET_GROUP_ID']?>/blog/<?=$arResult['Post']['ID']?>/">зарегистрироваться</a> на сайте.
				</div>
			</div>
		</div>

	<?}?>
	</div>

<?
$arnumMonth = Array(".01.", ".02.", ".03.", ".04.", ".05.", ".06.", ".07.", ".08.", ".09.", ".10.", ".11.", ".12.");
$arMonth = Array(" января ", " февраля ", " марта ", " апреля ", " мая ", " июня ", " июля ", " августа ", " сентября ", " октября ", " ноября ", " декабря ", );


function getComment($intID, $arResult){
	echo '<a name="00"></a>';
	foreach($arResult["CommentsResult"][$intID] as $arComment):
		$strHtml .= (is_null($arComment['PARENT_ID']) ? '<div class="block">' : '<div class="reply_block">');
		$strHtml .= getHTML($arComment,&$arResult);
		
		if(isset($arResult["CommentsResult"][$arComment["ID"]])){
			$strHtml .= getComment($arComment["ID"], &$arResult);
		}
		
		$strHtml .= '</div>';
	endforeach;
	return $strHtml;
}

function getHTML($arComment, $arResult){
	global $USER;
	if(intval($arComment['arUser']['PERSONAL_PHOTO']) > 0){
		$rsAvatar = CFile::GetByID($arComment['arUser']['PERSONAL_PHOTO']);
		$arAvatar = $rsAvatar->Fetch();
		$arAvatar['SRC'] = "/upload/".$arAvatar['SUBDIR']."/".$arAvatar['FILE_NAME'];
	} else {
		$arAvatar['SRC'] = "/images/avatar/avatar.jpg";
	}
	ob_start();?>
		<div class="comment<?if($USER->isAuthorized() && $USER->GetId() == $arComment['arUser']['ID'] ){?> mine<?}?>" id="<?=$arComment['ID']?>">
			<div class="icons">
				<div class="close_icon" title="Закрыть"></div>
				<div class="pointer"></div>
				
				
				<div class="photo">
					<div class="big_photo">
						<div>
							<img src="<?=$arAvatar['SRC']?>" width="100" height="100" alt="<?=$arComment['arUser']['LOGIN']?>">
						</div>
					</div>
					<img src="<?=$arAvatar['SRC']?>" width="30" height="30" alt="<?=$arComment['arUser']['LOGIN']?>">
				</div>
				
				<div class="right">

					<?if( strlen($arComment['urlToDelete']) > 0){?>
						<div class="delete" title="Удалить"></div>
					<?}?>
				</div>
			</div>
			<div class="padding">
				<div class="comment_author"><a href="/profile/<?=$arComment['arUser']['ID']?>/">
					<?=$arComment['arUser']['LOGIN']?></a>
				</div>
				<div class="text">
					<?=nl2br($arComment["POST_TEXT"])?>
				</div>
				<div class="properties">
					<div class="reply_string"><div class="icon"></div><a href="#" class="no_link">Ответить</a></div>
					<div class="date"><?=str_replace($arnumMonth, $arMonth, date("d.m.Y H:i",strtotime($arComment["DATE_CREATE"])))?></div>
				</div>
			</div>
		</div>
			
<?$strHTML = ob_get_contents();
ob_end_clean();
return $strHTML;
}
echo getComment(0,&$arResult);
?>	
</div>

<a name="add_comment"></a>
<div id="comment_form">
	<?if($USER->IsAuthorized()){?>
		<form method="POST" id="comment" action="<?=POST_FORM_ACTION_URI?>">
			<div class="form_field">
				<h4>Ваш комментарий <span>?</span></h4>
				<textarea cols="10" rows="10" name="comment"></textarea>
				<input type="hidden" name="parentId" value="">
				<?=bitrix_sessid_post()?>
				<input type="hidden" name="post" value="Y">
				<div class="button">Комментировать</div>
			</div>
		</form>
	<?} else {?>
		<div class="comment foodclub">
			<div class="icons">
				<div class="pointer"></div>
			</div>
			<div class="padding">
				<div class="text">
					Если Вы хотите оставить комментарий, Вам необходимо <a href="/auth/?backurl=/blogs/group/<?=$arResult['Blog']['SOCNET_GROUP_ID']?>/blog/<?=$arResult['Post']['ID']?>/">авторизоваться</a> или <a href="/registration/?backurl=/blogs/group/<?=$arResult['Blog']['SOCNET_GROUP_ID']?>/blog/<?=$arResult['Post']['ID']?>/">зарегистрироваться</a> на сайте.
				</div>
			</div>
		</div>
	<?}?>
</div>


