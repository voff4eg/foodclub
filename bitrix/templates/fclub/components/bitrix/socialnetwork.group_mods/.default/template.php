<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="club_body">
	<h2>Выбор модератора</h2>
</div>

<?
if (strlen($arResult["FatalError"]) > 0)
{
	?>
	<div class="system_message">
	<div class="pointer"></div>
	<div class="padding"><h2><?
	echo $arResult["FatalError"];
	?></h2></div>
	</div>
	<?
}
else
{
	if(strlen($arResult["ErrorMessage"])>0)
	{
		?>
		<div class="system_message">
		<div class="pointer"></div>
		<div class="padding"><h2><?
		echo $arResult["ErrorMessage"];
		?></h2></div>
		</div>
		<?
	}
	?>	
	<?if ($arResult["CurrentUserPerms"]["UserCanModifyGroup"]):?>
		<form method="post" name="form1" id="user_list" action="<?=POST_FORM_ACTION_URI?>" enctype="multipart/form-data">
	<?endif;?>
	<div class="user_list">
		<h5>Участники</h5>
		<?
		if ($arResult["Moderators"] && $arResult["Moderators"]["List"]){
			$ind = 1;
			foreach ($arResult["Moderators"]["List"] as $friend){

				?>
				<div class="user">
				<?if ($arResult["CurrentUserPerms"]["UserCanModifyGroup"] || $arResult["CurrentUserPerms"]["UserCanModerateGroup"]){?>
					<?if ($friend["USER_ID"] != $arResult["Group"]["OWNER_ID"]){?>
						<div class="checkbox">
							<table><tr><td><input type="checkbox" name="checked_<?=$ind?>" value="Y"></td></tr></table>
						</div>
					<?}
					echo "<input type=\"hidden\" name=\"id_".$ind."\" value=\"".$friend["ID"]."\">";	
				}?>
					<div class="photo">
						
					<?if(is_array($friend['USER_PERSONAL_PHOTO_FILE'])){
						?>
						<div class="big_photo">
							<div>
								<img src="<?=$friend['USER_PERSONAL_PHOTO_FILE']['SRC']?>" width="100" height="100" alt="<?=$friend['USER_LOGIN']?>">
							</div>
						</div>
						<img  src="<?=$friend['USER_PERSONAL_PHOTO_FILE']['SRC']?>" 
								width="30" height="30" alt="<?=$friend['USER_LOGIN']?>"><?
					} else {
						?>
						<div class="big_photo">
							<div>
								<img src="/images/avatar/avatar.jpg" width="100" height="100" alt="<?=$friend['USER_LOGIN']?>">
							</div>
						</div>
						<img src="/images/avatar/avatar_small.jpg" width="30" height="30" alt="<?=$friend['USER_LOGIN']?>"><?
					}?>
						
					</div>
					<a href="/profile/<?=$friend['USER_ID']?>/"><?=str_replace("http://", "", $friend['USER_LOGIN'])?></a>
				</div>
				<?
				$ind++;
			}
		}
		else
		{
			echo GetMessage("SONET_C25_T_EMPTY");
		}
		?>
	<div class="clear"></div>
	</div>

	<?if ($arResult["CurrentUserPerms"]["UserCanModifyGroup"]):?>

		<input type="hidden" name="max_count" value="<?=$ind?>">
		<?=bitrix_sessid_post()?>
		<input type="hidden" name="save" value="Y">
		<div class="button">Уволить</div>
		</form>
	<?endif;?>
	<?
}
?>
	