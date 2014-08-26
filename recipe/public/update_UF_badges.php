<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("iblock");


//echo "<pre>";
	/*$arUserBadges = array();
	//Получаем ID Бейджа "Первый рецепт"
	$rsFirstRecipeBadge = CIBlockElement::GetList(array(),array("IBLOCK_CODE"=>"badges","CODE"=>"first_recipe"),false,false,array("ID"));
	if($arFirstRecipeBadge = $rsFirstRecipeBadge->Fetch()){
		$i = 0; $j = 0; $count = 0; $k = 0;
		$rsUsers = CUser::GetList(($by="ID"), ($order="asc"), array(), array("SELECT"=>array("ID", "UF_BADGES"))); 
		while( $arUser = $rsUsers->Fetch() ){	// Перебираем всех юзеров
			//$allUsersList[ = $arUser["ID"];
			$arUserBadges = $arUser["UF_BADGES"];

			//Выявляем юзеров с хотя бы одним рецептом
			$arUserRecipeExists = false;
			$rsUserRecipes = CIBlockElement::GetList(array(),array("IBLOCK_CODE"=>"recipe", "CREATED_BY" => $arUser["ID"]),false,false,array("ID"));
			if( $rsUserRecipes->Fetch() )
				$arUserRecipeExists = true;
				//$arUserRecipeExists = $arUserRecipes["ID"];
				//echo "ID: ".$arUser["ID"]." Number of recipes: ".count($arUserAllRecipes)."Badges ID: ".$arUser["UF_BADGES"][0];
			
			//Устанавливаем/снимаем у юзеров Бейдж "Первый рецепт"
			if( ($arUserRecipeExists && in_array($arFirstRecipeBadge["ID"],$arUserBadges)) ||
				(!$arUserRecipeExists && !in_array($arFirstRecipeBadge["ID"],$arUserBadges)) ){
					//echo $arUser["ID"]; var_dump($arUserRecipeExists); die;
					//Все Ок - Ничего не делаем с юзерами
			}elseif( $arUserRecipeExists && !in_array($arFirstRecipeBadge["ID"],$arUserBadges) ){	
				$arUserBadges[] = $arFirstRecipeBadge["ID"];	//Добавляем бейдж юзеру
				$user = new CUser;
				$user->Update($arUser["ID"], array("UF_BADGES"=>array($arUserBadges)));
				$i++;
				//print_r($arUserBadges);
				//echo "Юзеру ".$arUser["ID"]." установлен бейдж Первый рецепт"."<br>";
				//die;
			}elseif( !$arUserRecipeExists && in_array($arFirstRecipeBadge["ID"],$arUserBadges) ){
				$index = array_search( $arFirstRecipeBadge["ID"], $arUserBadges );
				if( $index !== false ){
					unset( $arUserBadges[$index] );	//Снимаем незаслуженный бейдж
				}
				$user = new CUser;
				$user->Update($arUser["ID"], array("UF_BADGES"=>array($arUserBadges)));
				$j++; 
				//print_r($arUserBadges);
				die;
			}else{
				$k++;
			}
			$count++;
		}
		echo "Всего ".$count." юзеров"."<br>"; 
		echo "Бейдж Первый рецепт добавлен у ".$i." юзеров"."<br>"; 
		echo "Бейдж Первый рецепт снят у ".$j." юзеров"."<br>"; 
		echo "Ошибок ".$k." штук"; 
	}*/

$rsFirstRecipeBadge = CIBlockElement::GetList(array(),array("IBLOCK_CODE"=>"badges","CODE"=>"first_recipe"),false,false,array("ID"));
if($arFirstRecipeBadge = $rsFirstRecipeBadge->Fetch()){ //Получаем ID бейджа Первый рецепт
	$arUserWRecipes = array();
	$rsUserRecipes = CIBlockElement::GetList(array(),array("IBLOCK_CODE"=>"recipe"),array("CREATED_BY"));
	while($arUserRecipe = $rsUserRecipes->GetNext()){ // Группируем рецепты по автору
		$arUserWRecipes[] = $arUserRecipe["CREATED_BY"];
	}

	$rsUserWFRBadge = CUser::GetList(($by="LAST_NAME"), ($order="asc"), array("UF_BADGES"=>$arFirstRecipeBadge["ID"]));
	while($arUserWFRBadge = $rsUserWFRBadge->Fetch()){ //Получаем юзеров с бейджем Первый рецепт

		$arUsersWFRBadge[] = $arUserWFRBadge["ID"];

	}

	$arUsersMerged = array_merge($arUserWRecipes, $arUsersWFRBadge); //Объединяем юзеров-создателей рецептов с юзерами с бейджем Первый рецепт
	$arUserIDs = array_unique($arUsersMerged); //Оставляем уникальных юзеров

	if(!empty($arUserIDs)){

		$rsUsers = CUser::GetList($a,$b,array("ID" => $arUserIDs),array("SELECT"=>array("UF_BADGES")));
		while($arUser = $rsUsers->Fetch()){

			$arUserBadges[ $arUser["ID"] ] = $arUser["UF_BADGES"]; //Получаем имеющиеся у юзера бейджики

		}			

	}

	//echo "<pre>";print_r($arUserBadges);echo "</pre>";die;

	//Массив с пользователями, у которых правильно установлен бейдж Первого рецепта, и нам не нужно ничего менять
	$arUsersWSettedWRBadge = array_intersect($arUserWRecipes, $arUsersWFRBadge);

	//Массив пользователей, которым нужно установить бейдж Первого рецепта
	$arUsersToSetWRBadge = array_diff($arUserWRecipes,$arUsersWFRBadge);

	/*echo "arUserWRecipes<pre>";print_r($arUserWRecipes);echo "</pre>";
	echo "arUsersWFRBadge<pre>";print_r($arUsersWFRBadge);echo "</pre>";
	echo "arUsersWSettedWRBadge<pre>";print_r($arUsersWSettedWRBadge);echo "</pre>";*/
	
	//echo "arUsersToSetWRBadge<pre>";print_r($arUsersToSetWRBadge);echo "</pre>";

	if(is_array($arUsersToSetWRBadge) && !empty($arUsersToSetWRBadge)){

		foreach($arUsersToSetWRBadge as $userID){			

			if(!empty($arUserBadges[ $userID ])){

				$arUserBadges[ $userID ][] = $arFirstRecipeBadge["ID"];	
			}else{

				$arUserBadges[ $userID ] = array($arFirstRecipeBadge["ID"]);

			}
			
			$user = new CUser;
			$user->Update($userID, array("UF_BADGES"=>$arUserBadges[ $userID ]));

		}

	}


	//Массив пользователей, которым нужно убрать бейдж Первого рецепта
	$arUsersToClearWRBadge = array_diff($arUsersWFRBadge,$arUserWRecipes);
	//echo "arUsersToClearWRBadge<pre>";print_r($arUsersToClearWRBadge);echo "</pre>";
	if(is_array($arUsersToClearWRBadge) && !empty($arUsersToClearWRBadge)){

		foreach($arUsersToClearWRBadge as $userID){
			if(!empty($arUserBadges[ $userID ]) && in_array($arFirstRecipeBadge["ID"],$arUserBadges[ $userID ])){

				$key = array_search($arFirstRecipeBadge["ID"],$arUserBadges[ $userID ]);
				if( $key !== false ){
					unset($arUserBadges[ $userID ][ $key ]); //Удаляем бейдж Первый рецепт
					$user = new CUser;
					$user->Update($userID, array("UF_BADGES"=>$arUserBadges[ $userID ]));
				}
			}

		}

	}



	echo "Пользователей с бейджем :".count($arUsersWFRBadge)."<br>";
	echo "Пользователей которым нужно выставить бейдж ".count($arUserWRecipes);
	/*if(!empty($arUserWRecipes)){
		foreach($arUserWRecipes as $userID){



		}
	}*/
}
//echo "</pre>";
	

//require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog.php");
?>