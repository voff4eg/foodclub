<?

if($updater->TableExists("b_user") || $updater->TableExists("B_USER"))
{
	$arEventTypes = array(
		"ru" => array(
			"LID" => "ru",
			"EVENT_NAME" => "NEW_USER_CONFIRM",
			"NAME" => "Подтверждение регистрации нового пользователя",
			"DESCRIPTION" => "
#USER_ID# - ID пользователя
#LOGIN# - Логин
#EMAIL# - EMail
#NAME# - Имя
#LAST_NAME# - Фамилия
#USER_IP# - IP пользователя
#USER_HOST# - Хост пользователя
#CONFIRM_CODE# - Код подтверждения
",
			"SORT" => 3,
		),
	);

	$arMessages = array(
		"ru" => array(
			"EVENT_NAME" => "NEW_USER_CONFIRM",
			"LID" => "",
			"EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#",
			"EMAIL_TO" => "#EMAIL#",
			"SUBJECT" => "#SITE_NAME#: Подтверждение регистрации нового пользователя",
			"MESSAGE" => "Информационное сообщение сайта #SITE_NAME#
------------------------------------------

Здравствуйте,

Вы получили это сообщение, так как ваш адрес был использован при регистрации нового пользователя на сервере #SERVER_NAME#.

Ваш код для подтверждения регистрации: #CONFIRM_CODE#

Для подтверждения регистрации перейдите по следующей ссылке:
http://#SERVER_NAME#/auth/index.php?confirm_registration=yes&confirm_user_id=#USER_ID#&confirm_code=#CONFIRM_CODE#

Вы также можете ввести код для подтверждения регистрации на странице:
http://#SERVER_NAME#/auth/index.php?confirm_registration=yes&confirm_user_id=#USER_ID#

Внимание! Ваш бюджет не будет активным, пока вы не подтвердите свою регистрацию.

---------------------------------------------------------------------

Сообщение сгенерировано автоматически.",
		),
	);
	$rsLang = CLanguage::GetList(($b=""), ($o=""));
	while($arLang = $rsLang->Fetch())
	{
		if(array_key_exists($arLang["LID"], $arEventTypes))
		{
			$rs = $DB->Query("SELECT * from b_event_type WHERE LID = '".$DB->ForSQL($arLang["LID"])."' AND EVENT_NAME = 'NEW_USER_CONFIRM'");
			if(!$rs->Fetch())
			{
				$TID = $DB->Add("b_event_type", $arEventTypes[$arLang["LID"]], Array("DESCRIPTION"));
				$MID = $DB->Add("b_event_message", $arMessages[$arLang["LID"]], Array("MESSAGE"));

				$rsSites = CLang::GetList($by, $order, Array("LANGUAGE_ID"=>$arLang["LID"]));
				while($arSite = $rsSites->Fetch())
				{
					$DB->Query("INSERT INTO b_event_message_site(EVENT_MESSAGE_ID, SITE_ID) VALUES (".intval($MID).", '".$DB->ForSQL($arSite["LID"])."')");
				}
			}
		}
	}
}
?>