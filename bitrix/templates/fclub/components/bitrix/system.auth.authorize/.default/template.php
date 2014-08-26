<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$APPLICATION->RestartBuffer();
include($_SERVER['DOCUMENT_ROOT']."/bitrix/templates/fclub/new.register.header.php");
if(!empty($arResult["ERRORS"])){
	$errors = implode(",", $arResult["ERRORS"]);
}

if(!$USER->IsAuthorized()){
?>
<div class="columns">
<h2>Авторизоваться</h2>
	<div class="left_column">
		<form name="authorization" method="post" action="<?=$arResult["AUTH_URL"]?>">
			<input type="hidden" value="Y" name="AUTH_FORM">
			<input type="hidden" value="AUTH" name="TYPE">
		<?if(strlen($arResult["BACKURL"]) > 0){?><input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" /><?}?>
			<div class="form_field<?=(strpos($errors,"Пользователь с логином") ? " attention" : "" )?>">
				<label><?=(strpos($errors,"Пользователь с логином") ? "Пользователь с логином ".$arResult["VALUES"]["LOGIN"]." уже существует" : "Логин" )?> <span class="no_text">?</span></label>
				<input type="text" class="text" name="USER_LOGIN" value="<?=$arResult["VALUES"]["LOGIN"]?>">
			</div>
			<div class="form_field">
				<label>Пароль <span class="no_text">?</span></label>
				<input type="password" class="text" name="USER_PASSWORD" value="<?=$arResult["VALUES"]["PASSWORD"]?>">
			</div>
			<?if(isset($arResult["CAPTCHA_CODE"]) && strlen($arResult["CAPTCHA_CODE"]) > 0):?>
			<div class="form_field captcha">
				<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="" /><input type="hidden" name="captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>" />
				<label>Код на картинке<span class="no_text">?</span></label>
				<input type="text" class="text" name="captcha_word" value="" />
			</div>
			<?endif;?>
			<div class="form_field">
				<label class="checkbox"><input type="checkbox" name="REMEMBER_ME" /> <span>Запомнить меня</span></label>
			</div>
			<?
			if (count($arResult["ERRORS"]) > 0){
				foreach ($arResult["ERRORS"] as $key => $error)
				{
					if (intval($key) <= 0) echo '<div class="error_message">&mdash; '.$arResult["ERRORS"][$key] = str_replace("#FIELD_NAME#", GetMessage("REGISTER_FIELD_".$key), $error)."</div>";
				}
			}
			?>
			<div class="submit">
				<button class="button" type="submit">Авторизоваться</button>
				<div class="b-form-forgotten"><a href="/auth/?forgot_password=yes">Забыли пароль?</a></div>
			</div>
		</form>
	</div>
	<div class="right_column">
		<div class="networks">
			<?$APPLICATION->IncludeComponent("custom:socserv.auth.form", "", 
				array(
					"AUTH_SERVICES"=>$arResult["AUTH_SERVICES"],
					"CURRENT_SERVICE"=>$arResult["CURRENT_SERVICE"],
					"AUTH_URL"=>$arResult["AUTH_URL"],
					"POST"=>$arResult["POST"],
				), 
				$component, 
				array("HIDE_ICONS"=>"Y")
			);
			?>
		</div>
	</div>
</div>
<div class="clear"></div>
<?} else {
	if (isset($_REQUEST["backurl"]) && strlen($_REQUEST["backurl"])>0){
		LocalRedirect($_REQUEST["backurl"]);
	}else{
		LocalRedirect("/profile/".$USER->GetID()."/");
	}
}
include($_SERVER['DOCUMENT_ROOT']."/bitrix/templates/fclub/new.register.footer.php");die;?>