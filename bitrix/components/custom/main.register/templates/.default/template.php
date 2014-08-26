<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$APPLICATION->RestartBuffer();
include($_SERVER['DOCUMENT_ROOT']."/bitrix/templates/fclub/service.header.php");

if(!$USER->IsAuthorized()){
?>
	<div id="form">
		<form method="post" action="<?=POST_FORM_ACTION_URI?>" name="regform" enctype="multipart/form-data">
		<?if(strlen($arResult["BACKURL"]) > 0){?><input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" /><?}?>
			<h1>Регистрация</h1>
			<div class="form_field">

				<h5>Логин <span>?</span></h5>
				<input type="text" class="text" name="REGISTER[LOGIN]" value="<?=$arResult["VALUES"]["LOGIN"]?>">
			</div>
			<div class="two">
				<div class="form_field">
					<h5>Пароль <span>?</span></h5>
					<input type="password" class="text" name="REGISTER[PASSWORD]" value="<?=$arResult["VALUES"]["PASSWORD"]?>">

				</div>
				<div class="form_field">
					<h5>Подтверждение пароля <span>?</span></h5>
					<input type="password" class="text" name="REGISTER[CONFIRM_PASSWORD]" value="<?=$arResult["VALUES"]["CONFIRM_PASSWORD"]?>">
				</div>
				<div class="clear"></div>
			</div>
			<div class="form_field">

				<h5>E-mail <span>?</span></h5>
				<input type="text" class="text" name="REGISTER[EMAIL]" value="<?=$arResult["VALUES"]["EMAIL"]?>">
			</div>
			<div class="form_field">
				<h5>Адрес домашней страницы</h5>
				<input type="text" class="text" name="REGISTER[PERSONAL_WWW]" value="<?=$arResult["VALUES"]["PERSONAL_WWW"]?>">
			</div>
			<div class="capture">
				<div class="form_field">
					<h5>Введите код картинки <span>?</span></h5>
					<input type="text" class="text" name="captcha_word" value="">
				</div>
				<div class="code"><img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt=""><input type="hidden" name="captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>" /></div>
				<div class="clear"></div>
			</div>
			<?
			if (count($arResult["ERRORS"]) > 0){
				foreach ($arResult["ERRORS"] as $key => $error)
				{
					if (intval($key) <= 0) echo '<div class="error_message">&mdash; '.$arResult["ERRORS"][$key] = str_replace("#FIELD_NAME#", GetMessage("REGISTER_FIELD_".$key), $error)."</div>";
				}
			}
			?>
			<input type="hidden" name="register_submit_button" value="<?=GetMessage("AUTH_REGISTER")?>" />
			<div class="button">Отправить</div>
		</form>
	</div>
<?} else {
	LocalRedirect("/profile/".$USER->GetID()."/");
}
include($_SERVER['DOCUMENT_ROOT']."/bitrix/templates/fclub/service.footer.php"); die;?>