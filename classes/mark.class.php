<?
class CFUser {

	/*
	 * Функция возвращает список пользователей. На вход подается массив ID пользователей.
	 */
	public function getList($id, $top = true)
	{
		if( is_array($id) )
		{
			if( count($id) > 1 )
			{
				$Params = array(
					"ID" => join(" | ", $id) );

				$result = $this->getRequest($Params);

				if($result !== false) return $result;
				return false;
			}
			elseif( count($id) === 1 )
			{
				$dump = $this->getById( current($id) );
				$result[ $dump['ID'] ] = $dump;
				return $result;
			}
			else { return false; }

		}
		else
		{
			return false;
		}
	}

	public function getById($id)
	{
		if( strlen($id) > 0 )
		{
			$Params = array("ID"=>$id);
			$result = $this->getRequest($Params);

			if($result !== false) return $result;
			return false;
		}
		else
		{
			return false;
		}
	}

	/*
	 *
	 */
	private function getRequest( $Params )
	{
		global $USER;

		$rsUser = CUser::GetList(($by="ID"), ($order="desc"), $Params ,array("SELECT"=>array("UF_*")));
		while($arUser = $rsUser->Fetch())
		{
			$arStatus = array(
				"1" => "Новичок",
				"2" => "Опытный",
				"3" => "Продвинутый",
				"4" => "Профессионал",
				"5" => "Эксперт",
				//"6" => "Campbell's",
			);
			$arUser['status'] = $arUser['UF_STATUS'];
			$arUser['status_name'] = $arStatus[ $arUser['UF_STATUS'] ];

			if(IntVal($arUser['PERSONAL_PHOTO']) > 0){
				$rsFile = CFile::GetByID(IntVal($arUser['PERSONAL_PHOTO']));
				$arFile = $rsFile->Fetch();
				$arFile['SRC'] = "/upload/".$arFile['SUBDIR']."/".$arFile['FILE_NAME'];
				$arUser["photo"] = $arFile;
			}
			else
			{
				$arUser["photo"]['SRC'] = "/images/default_avatar.jpg";
			};

			if( intval($arUser['UF_LOGIN']) <= 0 ){
			    $arUser['FULL_NAME'] = trim($arUser['NAME']." ".$arUser['LAST_NAME']);
            } else {
                $arUser['FULL_NAME'] = $arUser['LOGIN'];
            }

			$arUser['profile_url'] = "/profile/".$arUser['ID']."/";
			$arResult[ $arUser['ID'] ] = $arUser;
		}
		$count = count($arResult);

		if( $count == 1 )
		{
			return current($arResult);
		}
		elseif( $count > 1 )
		{
			return $arResult;
		}
		else
		{
			return false;
		}

		return $arUser;

	}
}

class CMark {
	private static $_instance = null;
	private static $like_rait = 1;
	private static $recipe_rait = 10;
	private static $comment_rait = 1;
	private static $r_create_rait = 30; // Рейтинг при добавлении рецепта
	private static $b_create_rait = 10; // Рейтинг при добавлении записи
	private static $b_comment_rait = 1; // Рейтинг при комментировании записи
	private static $r_comment_rait = 1; // Рейтинг при комментировании своего рецепта
	private static $update_profile_rait = 15; // Рейтинг при заполнении всех полей учетной записи
	private static $r_another_comment_rait = 2; // Рейтинг при комментировании не своего рецепта
	private static $r_favorite_rait = 3; // Рейтинг при добавлении рецепта в избранное
	
	/*
	 * Функция отмечает рецепт как "Понравилось" для выбранного пользователя.
	 */
	public function like($user, $recipe)
	{
		if( intval($user) > 0 && intval($recipe) > 0 )
		{
			global $DB;
			$sqlSelect = 'SELECT * FROM `c_like` WHERE `user_id`='.intval($user).' AND `recipe_id`='.intval($recipe).' LIMIT 1';
			$rowFields = $DB->Query($sqlSelect, false);
			if ( $rowFields->Fetch() === false)
			{
				$arFields  =array(
					"user_id" => intval($user),
					"recipe_id" => intval($recipe),
				);

				$DB->StartTransaction();
				$intID = $DB->Insert("c_like", $arFields, $err_mess.__LINE__);
				if (strlen($strError)<=0){
					$DB->Commit();
				} else {
					$DB->Rollback();
					return false;
				}

				// Обновление рейтинга в связи с событием "Понравился"
				$this->addLikeRait($recipe);

				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}

	/*
	 * Инициализация инфоблока со статистикой пользователя.
	 */
	public function initIblock( $recipe, $userId = false,$blog = false )
	{	
		if($blog == false){
			if( intval($recipe) > 0 )
			{
				CModule::IncludeModule("iblock");

				$rsRecipe = CIBlockElement::getById( $recipe );
				$arRecipe = $rsRecipe->Fetch();

				if($userId == false){
					$userId = $arRecipe['CREATED_BY'];
				}

				$rsElement = CIBlockElement::GetList(
					array(),
					array(
						"IBLOCK_ID" => 13,
						"ACTIVE" => "Y",
						"PROPERTY_user" => $userId,
					),
					false,
					false,
					array(
						"PROPERTY_like", "PROPERTY_posts", "PROPERTY_comments", "ID",
					)
				);

				// Если инфоблока нет, он создается.
				$Element = $rsElement->GetNext();
				if( $Element === false )
				{
					$arProp = Array(
						"dish_up" => 0,
						"like" => 0,
						"posts" => 0,
						"comments" => 0,
						"user" => intval($userId),
					);
					$arLoadProductArray = Array(
						"IBLOCK_SECTION"  => false,
						"IBLOCK_ID"       => 13,
						"PROPERTY_VALUES" => $arProp,
						"NAME"            => $userId."й пользователь",
						"ACTIVE"          => "Y",
					);
					$elInfo   = new CIBlockElement;
					$intInfoID = $elInfo->Add($arLoadProductArray);

					if( intval($intInfoID) > 0 ){
						$arProp['info_id'] = $intInfoID;
						return $arProp;
					} else {
						return false;
					}
				}
				else // Если есть, возвращает статистику пользователя
				{
					$arProp = Array(
						"info_id" => $Element['ID'],
						"dish_up" => $Element['PROPERTY_DISH_UP_VALUE'],
						"like" => $Element['PROPERTY_LIKE_VALUE'],
						"posts" => $Element['PROPERTY_POSTS_VALUE'],
						"comments" => $Element['PROPERTY_COMMENTS_VALUE'],
						"user" => intval($userId),
					);
					return $arProp;
				}
			}
			else
			{
				return false;
			}
		}else{
			CModule::IncludeModule("iblock");
		}
	}

	
	public function addRecipeRaite($recipe, $user)
	{
	    if( intval($recipe) > 0 )
		{
            $arInfo = $this->initIblock($recipe, $user);
            $arInfoPosts = intval($arInfo['posts']) + 1;
            // Обновление рейтинга пользователя
			$this->updateUserRait($user, self::$recipe_rait);
            CIBlockElement::SetPropertyValues($arInfo['info_id'], 13, $arInfoPosts, "posts");
            $this->calcStatus($arInfo['user']);
            return true;
		}
		else
		{
			return false;
		}
	}

	public function addCommentRaite($recipe, $user)
	{
	    if( intval($recipe) > 0 )
		{
            $arInfo = $this->initIblock($recipe, $user);
            $arInfoCommetns = intval($arInfo['comments']) + 1;
            // Обновление рейтинга пользователя
			$this->updateUserRait($user, self::$comment_rait);
            CIBlockElement::SetPropertyValues($user, 13, $arInfoCommetns, "comments");

            $this->calcStatus($user);

            return true;
		}
		else
		{
			return false;
		}
	}
	
	public function lowCommentRaite($recipe, $user)
	{
	    if( intval($recipe) > 0 )
		{
            $arInfo = $this->initIblock($recipe, $user);
            $arInfoCommetns = intval($arInfo['comments']) - 1;
            // Обновление рейтинга пользователя
			
			$this->updateUserRait($user, self::$comment_rait, "low");
			
            CIBlockElement::SetPropertyValues($user, 13, $arInfoCommetns, "comments");
            $this->calcStatus($user);
            return true;
		}
		else
		{
			return false;
		}
	}

	/*
	 * Обновление всех свойств связанных с событием "Понравилось" для рецепта
	 */
	public function addLikeRait($recipe)
	{
		if( intval($recipe) > 0 )
		{
			CModule::IncludeModule("iblock");
			global $USER;

			// Количество "Понравилось"
			$rsLike = CIBlockElement::GetProperty(5, $recipe, array(), array("CODE"=>"like"));
			$arLike = $rsLike->Fetch();
			$Like = intval($arLike['VALUE']);

			// Увеличение количество "Понравилось" у рецепта
			$Like++;

			$arInfo = $this->initIblock($recipe);
			// Общее количество "Понравилось" у пользователя
			$arInfoDishup = intval($arInfo['like']) + 1;

			/*
			 * Блок записи всех свойст
			 */
			CIBlockElement::SetPropertyValues($recipe, 5, $Like, "like");
			CIBlockElement::SetPropertyValues($arInfo['info_id'], 13, $arInfoDishup, "like");
			// Обновление рейтинга пользователя
			$this->updateUserRait($arInfo['user'], self::$like_rait);
			// Обновление рейтинга рецепта
			$this->updateRecipeRait($recipe, self::$like_rait);
			$this->calcStatus($arInfo['user']);
			return true;
		}
		else
		{
			return false;
		}
	}

	/*
	 * Обновление рейтинга пользователя на заданную величину
	 */
	public function updateUserRait($user, $way = "up", $type = false)
	{
		if($type == "r_create"){
			$rait = self::$r_create_rait;
		}elseif($type == "b_create"){
			$rait = self::$b_create_rait;
		}elseif($type == "b_comment_create"){
			$rait = self::$b_comment_rait;
		}elseif($type == "r_comment_create"){
			$rait = self::$r_comment_rait;
		}elseif($type == "update_profile"){
			$rait = self::$update_profile_rait;
		}elseif($type == "r_comment_recipe"){
			$rait = self::$r_another_comment_rait;
		}elseif($type == "r_favorite"){
			$rait = self::$r_favorite_rait;
		}
		if( intval($user) > 0 && intval($rait) > 0)
		{
			global $USER;
			//require($_SERVER["DOCUMENT_ROOT"].'/classes/user.class.php');
			$CFUser = new CFUser;
			$arUser = $CFUser->getById( $user );
			$UserRait = intval( $arUser['UF_RAITING'] );
			//Подсчет нового рейтинга пользователя
			if ($way == "up")
    			{$UserRait = $UserRait + $rait;}
            elseif ($way == "low")
                {$UserRait = $UserRait - $rait;}
			
			if ( $USER->Update( $user, array("UF_RAITING"=>$UserRait) ) )
				return true;

			return false;
		}
	}

	/*
	 * Обновление рейтинга рецепта на заданную величину
	 */
	public function updateRecipeRait($recipe, $rait)
	{
		if( intval($recipe) > 0 && intval($rait) > 0)
		{
			// Общий рейтинг рецепта
			$rsRaiting = CIBlockElement::GetProperty(5, $recipe, array(), array("CODE"=>"raiting"));
			$arRaiting = $rsRaiting->Fetch();
			$Raiting = intval($arRaiting['VALUE']);

			// Подсчет нового рейтинга рецепта
			$Raiting = $Raiting + $rait;
			CIBlockElement::SetPropertyValues($recipe, 5, $Raiting, "raiting");
            
			return false;
		}
	}

	public function isLiked($user, $recipe)
	{
		if( intval($user) > 0 && intval($recipe) > 0 )
		{
			global $DB;
			$sqlSelect = 'SELECT * FROM `c_like` WHERE `user_id`='.intval($user).' AND `recipe_id`='.intval($recipe).' LIMIT 1';
			$rowFields = $DB->Query($sqlSelect, false);
			if ( $rowFields->Fetch() === false)
			{
				return false;
			}
			else
			{
				return true;
			}
		}
		else
		{
			return false;
		}
	}

	/*
	 * Обсчет данных пользователя и выставление рейтинга
	 */
    public function calcStatus($userId)
    {
        global $USER;

        CModule::IncludeModule("iblock");

        $rsElement = CIBlockElement::GetList(
			array(),
			array(
				"IBLOCK_ID" => 13,
				"ACTIVE" => "Y",
				"PROPERTY_user" => $userId,
			),
			false,
			false,
			array(
				"PROPERTY_dish_up", "PROPERTY_like", "PROPERTY_posts", "PROPERTY_comments", "ID",
			)
		);

		$arElement = $rsElement->GetNext();

		$Like = intval($arElement['PROPERTY_LIKE_VALUE']);
        $Dishup = intval($arElement['PROPERTY_DISH_UP_VALUE']);
		$Posts = intval($arElement['PROPERTY_POSTS_VALUE']);
        $Comments = intval($arElement['PROPERTY_COMMENTS_VALUE']);

		$rsUser = CUser::GetList(($by="ID"), ($order="desc"), array("ID"=>$userId) ,array("SELECT"=>array("UF_*")));
		$arUser = $rsUser->GetNext();

		$rowStatus = $arUser['UF_STATUS'];
		$rowRaiting = $arUser['UF_RAITING'];

		if( $Posts >= 8 && $rowRaiting >= 120){
			$Status = 5;
		}
		elseif( $Posts >= 4 && $rowRaiting >= 80 ){
			$Status = 4;
		}
		elseif( $Posts >= 2 && $rowRaiting >= 40 ){
			$Status = 2;
		}
		else{
			$Status = 1;
		}

		if( in_array(8, $USER->GetUserGroup($userId)) == false )
		{
			$USER->Update( $userId, array("UF_STATUS"=>$Status) );
			$arFields = array(
			    "EMAIL"			=> $arUser['EMAIL'],
			    "USER"          => $arUser['LOGIN'],
		    );

			if($Status > $rowStatus)
			{
			    //CEvent::Send("STATUS_UPDATE_".$Status, array("s1"), $arFields, "N");
				$USER->Update( $userId, array("UF_RAITING"=>$rowRaiting+10) );
			}
			elseif($Status < $rowStatus)
			{
			    //CEvent::Send("STATUS_UPDATE_".$Status, array("s1"), $arFields, "N");
				$USER->Update( $userId, array("UF_RAITING"=>$rowRaiting-10) );
			}
		}
    }
}


?>

