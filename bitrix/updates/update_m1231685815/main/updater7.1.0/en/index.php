<?
if($updater->TableExists("b_user") || $updater->TableExists("B_USER"))
{
	$arEventTypes = array(
		"en" => array(
			"LID" => "en",
			"EVENT_NAME" => "NEW_USER_CONFIRM",
			"NAME" => "New user registration confirmation",
			"DESCRIPTION" => "
#USER_ID# - User ID
#LOGIN# - Login
#EMAIL# - E-mail
#NAME# - First name
#LAST_NAME# - Last name
#USER_IP# - User IP
#USER_HOST# - User host
#CONFIRM_CODE# - Confirmation code
",
			"SORT" => 3,
		),
	);

	$arMessages = array(
		"en" => array(
			"EVENT_NAME" => "NEW_USER_CONFIRM",
			"LID" => "",
			"EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#",
			"EMAIL_TO" => "#EMAIL#",
			"SUBJECT" => "#SITE_NAME#: New user registration confirmation",
			"MESSAGE" => "Greetings from #SITE_NAME#!
------------------------------------------

Hello,

you have received this message because you (or someone else) used your e-mail to register at #SERVER_NAME#.

Your registration confirmation code: #CONFIRM_CODE#

Please use the link below to verify and activate your registration:
http://#SERVER_NAME#/auth/index.php?confirm_registration=yes&confirm_user_id=#USER_ID#&confirm_code=#CONFIRM_CODE#

Alternatively, open this link in your browser and enter the code manually:
http://#SERVER_NAME#/auth/index.php?confirm_registration=yes&confirm_user_id=#USER_ID#

Attention! Your account will not be activated until you confirm registration.

---------------------------------------------------------------------

This is an automated message.",
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