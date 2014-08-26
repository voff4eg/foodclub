<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
define("NO_KEEP_STATISTIC", true);
if(!empty($_REQUEST)){
    $file = fopen('postdata.txt', 'a');
        fwrite($file,serialize($_REQUEST)."\n");
            fclose($file);
        	$register_correct = false;
        	    if (
        		isset( $_REQUEST['PASSWORD'] ) && strlen( $_REQUEST['PASSWORD'] ) > 0
        		    &&
        			isset( $_REQUEST['LOGIN'] ) && strlen( $_REQUEST['LOGIN'] ) > 0
        			    &&
        				isset( $_REQUEST['EMAIL'] ) && strlen( $_REQUEST['EMAIL'] ) > 0
        				    )
        					{
        						if(check_email($_REQUEST['EMAIL']) && COption::GetOptionString("main", "new_user_email_uniq_check", "N") !== "Y"){
        							    $rsUser = CUser::GetByLogin( $_REQUEST['LOGIN'] );
        									if ($arUser = $rsUser->Fetch())
        										    {
        												    
        														}else{
        																$user = new CUser();
        																		$ID = $user->Add(array(
        																				    "EMAIL" => $_REQUEST['EMAIL'],
        																							"LOGIN" => $_REQUEST['LOGIN'],
        																									    "PASSWORD" => $_REQUEST['PASSWORD'],
        																												"ACTIVE" => "Y",
        																														));
        																																if (intval($ID) > 0)
        																																		{
        																																				    $register_correct = true;
        																																						    }
        																																								}
        																																									}		
        																																									    }		
        																																										//
        																																										    if($register_correct){
        																																											    //Пользователь авторизован, все ОК	
        																																												    echo "true";
        																																													}else{
        																																														//Пароль и логин не подходят
        																																															echo "false";
        																																															    }
        																																															    }					
        																																															    ?>