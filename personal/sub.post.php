<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");


CModule::IncludeModule("subscribe");
include($_SERVER['DOCUMENT_ROOT'].'/classes/factory.class.php');

if($USER->IsAuthorized())
{
    $UserId = IntVal($USER->GetId());
    $Email = $_SESSION['SESS_AUTH']['EMAIL'];
}
else
{
    LocalRedirect("/auth/?backurl=/profile/subscribe/");
}


if( $_SERVER['REQUEST_METHOD'] == "POST" )
{
    $rsSub = new CSubscription;
    $uID = $_REQUEST['user_rub'];
	
	foreach($_POST['rs_SUB'] as $sub)
	{
		$rubId[] = intval($sub);
	}
		
    if( $uID == "is_new" )
    {
        $arFields = Array(
            "USER_ID" => $UserId,
            "FORMAT" => "html",
            "EMAIL" => $Email,
            "ACTIVE" => "Y",
            "RUB_ID" => array($rubId),
            "SEND_CONFIRM" => "N",
        );

        $uID = $rsSub->Add($arFields);
    }
//    else
//    {

        $elSub = $rsSub->GetByID($uID);
        $arSub = $elSub->ExtractFields();
		

        $arFields = Array(
            "RUB_ID" => $rubId,
            "SEND_CONFIRM" => "N",
            "CONFIRM_CODE" => $arSub['CONFIRM_CODE'],
        );

        $rsSub->Update($uID, $arFields);
//    }
/*
    foreach($_POST['cur_sub_val'] as $ID=>$Item)
    {
        $cur_val = $Item;
        if( $Item == "1" && !in_array($ID, $_POST['rs_SUB']) )
        {
            //Происходит удаление подписки
            CSubscription::Delete($ID);
        }
        elseif( $Item == "0" && in_array($ID, $_POST['rs_SUB']) )
        {
            //Подписываем клиента
            $arFields = Array(
                "USER_ID" => $UserId,
                "FORMAT" => "html",
                "EMAIL" => $Email,
                "ACTIVE" => "Y",
                "RUB_ID" => array($_POST['rs_SUB']),
                "SEND_CONFIRM" => "N",
            );

            $rsSub = new CSubscription;
            $uID = $rsSub->Add($arFields);
            if( intval($uID) > 0 )
            {
                $elSub = $rsSub->GetByID($uID);
                $arSub = $elSub->ExtractFields();
                $rsSub->Update($uID, array("CONFIRM_CODE"=>$arSub['CONFIRM_CODE']));
            }
        }
    }
*/
}

LocalRedirect($_SERVER['HTTP_REFERER']);
?>

