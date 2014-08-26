<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if ($arResult["NEED_AUTH"] == "Y")
{
	$APPLICATION->AuthForm("");
}
elseif (strlen($arResult["FatalError"]) > 0)
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

	if ($arResult["ShowForm"] == "Input")
	{
		?>
	<div class="club_body">
	<?if(isset($arResult['POST']['NAME'])){?><h1>Редактирование</h1><?}else{?><h1>Новый клуб</h1><?}?>
		<form method="post" name="form1" id="edit_form" action="<?=POST_FORM_ACTION_URI?>" enctype="multipart/form-data">
			<div class="form_field">
				<h5><?= GetMessage("SONET_C8_NAME") ?></h5>
				<input type="text" name="GROUP_NAME" class="text" value="<?= $arResult["POST"]["NAME"]; ?>">
			</div>
			<div class="form_field">
				<h5><?= GetMessage("SONET_C8_DESCR") ?></h5>
				<textarea name="GROUP_DESCRIPTION" rows="10" cols="10"><?= $arResult["POST"]["DESCRIPTION"]; ?></textarea>
			</div>
			<div class="form_field">
				<div class="input_file"><input type="file" class="text" name="GROUP_IMAGE_ID"></div>
			</div>
		<?if ($arResult["POST"]["IMAGE_ID_FILE"]){?>
			<div class="form_field">
				<table>
					<tr>
						<td>
						<?if (strlen($arResult["POST"]["IMAGE_ID_IMG"]) > 0):
								echo $arResult["POST"]["IMAGE_ID_IMG"];
							endif;?>
						</td>
						<td><div class="form_checkbox"><div class="checkbox"><table><tr><td><input type="checkbox" name="GROUP_IMAGE_ID_DEL" value="Y"<?= ($arResult["POST"]["IMAGE_ID_DEL"] == "Y") ? " checked" : ""?> id="delete_image_id"></td></tr></table></div><label for="delete_image_id">Удалить</label></div></td>
					</tr>
				</table>
			</div>
		<?}?>
			<div class="form_field">
				<h5><?=GetMessage("SONET_C8_SUBJECT") ?></h5>
				<select name="GROUP_SUBJECT_ID">
					<option value=""><?= GetMessage("SONET_C8_TO_SELECT") ?></option>
					<?foreach ($arResult["Subjects"] as $key => $value):?>
						<option value="<?= $key ?>"<?= ($key == $arResult["POST"]["SUBJECT_ID"]) ? " selected" : "" ?>><?= $value ?></option>
					<?endforeach;?>
				</select>
			</div>
			<div class="form_checkbox">
				<div class="checkbox">
					<table><tr><td>
						<input type="checkbox" value="Y" name="GROUP_VISIBLE"<?= ($arResult["POST"]["VISIBLE"] == "Y") ? " checked" : ""?> id="GROUP_VISIBLE">
					</td></tr></table>
				</div>
				<label for="GROUP_VISIBLE"><?= GetMessage("SONET_C8_PARAMS_VIS") ?></label>
			</div>
			<div class="form_checkbox">
				<div class="checkbox">
					<table><tr><td>
						<input type="checkbox" value="Y" name="GROUP_OPENED"<?= ($arResult["POST"]["OPENED"] == "Y") ? " checked" : ""?> id="GROUP_OPENED">
					</td></tr></table>
				</div>
				<label for="GROUP_OPENED"><?= GetMessage("SONET_C8_PARAMS_OPEN") ?></label>
			</div>
			<div class="clear"></div>
			<div class="form_field">
				<h5><?=GetMessage("SONET_C8_KEYWORDS") ?></h5>
				<?if (IsModuleInstalled("search")):?>
					<?
					$APPLICATION->IncludeComponent("bitrix:search.tags.input", "template", Array(
						"NAME" => "GROUP_KEYWORDS",	// Имя поля ввода
						"VALUE" => $arResult["POST"]["KEYWORDS"],	// Содержимое поля ввода
						"arrFILTER" => "socialnetwork",	// Ограничение области поиска
						"PAGE_ELEMENTS" => "10",	// Количество записей в выпадающем списке
						"SORT_BY_CNT" => "Y",	// Сортировать по популярности
						)
					);
					?>
				<?else:?>
					<input type="text" name="GROUP_KEYWORDS" class="text" value="<?= $arResult["POST"]["KEYWORDS"]; ?>">
				<?endif;?>
			</div>
			<div class="form_field">
				<h5><?=GetMessage("SONET_C8_INVITE") ?></h5>
				<select name="GROUP_INITIATE_PERMS">
					<option value=""><?= GetMessage("SONET_C8_TO_SELECT") ?>-</option>
					<?foreach ($arResult["InitiatePerms"] as $key => $value):?>
						<option value="<?= $key ?>"<?= ($key == $arResult["POST"]["INITIATE_PERMS"]) ? " selected" : "" ?>><?= $value ?></option>
					<?endforeach;?>
				</select>
			</div>
			<div class="form_field">
				<h5><?= GetMessage("SONET_C8_SPAM_PERMS") ?></h5>
				<select name="GROUP_SPAM_PERMS">
					<option value=""><?= GetMessage("SONET_C8_TO_SELECT") ?>-</option>
					<?foreach ($arResult["SpamPerms"] as $key => $value):?>
						<option value="<?= $key ?>"<?= ($key == $arResult["POST"]["SPAM_PERMS"]) ? " selected" : "" ?>><?= $value ?></option>
					<?endforeach;?>
				</select>
			</div>
			<input type="hidden" name="SONET_USER_ID" value="<?= $GLOBALS["USER"]->GetID() ?>">
			<input type="hidden" name="SONET_GROUP_ID" value="<?= $arParams["GROUP_ID"] ?>">
			<?=bitrix_sessid_post()?>
			<input type="hidden" name="save" value="<?= ($arParams["GROUP_ID"] > 0) ? GetMessage("SONET_C8_DO_EDIT") : GetMessage("SONET_C8_DO_CREATE") ?>">

			<div class="button">Сохранить</div>
			
		</form>
		<?
	}
	else
	{?>
	<div class="system_message">
		<div class="pointer"></div>
		<div class="padding"><h2>
		<?if ($arParams["GROUP_ID"] > 0):?>
			<?= GetMessage("SONET_C8_SUCCESS_EDIT") ?>
		<?else:?>
			<?= GetMessage("SONET_C8_SUCCESS_CREATE") ?>
		<?endif;?></h2>
		<p><a href="<?= $arResult["Urls"]["NewGroup"] ?>">Спасибо, понятно</a></p>
		</div>
		</div>
	<?}
}
?>
</div>
