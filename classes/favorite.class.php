<?
class CFavorite {
	/**
	 * Проверяет, занесен ли рецепт в избранное
	 * @param Integer ID рецепта
	 * @return Bool
	 */
	public function status($Id)
	{
		global $DB, $USER;
		if( intval($Id) > 0 )
		{
			$rowFields = $DB->Query("SELECT * FROM `b_recipe_favorite` WHERE `recipe`=".intval($Id).' AND `user` = '.$USER->GetId(), false);
			if( $Field = $rowFields->Fetch() ){
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
	
	/**
	 * Метод добавляет рецепт в избранное
	 * @param Integer ID рецепта
	 * @return Bool
	 */
	public static function add($Id)
	{
		global $DB, $USER;
		if( intval($Id) > 0 && CFavorite::status( intval($Id) ) == false)
		{
			$arFields = Array(
				"user" 	 => $USER->GetID(),
				"recipe" => intval($Id),
			);
			
			$DB->StartTransaction();
			
			$intID = $DB->Insert("b_recipe_favorite", $arFields, $err_mess.__LINE__);
			$intID = IntVal($intID);
			
			if (strlen($strError)<=0){
				 $DB->Commit();
				 return true;
			} else {
				$DB->Rollback();
				$this->errors = "Ошибка занесения данных в базу";
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Метод удаляет рецепт в избранное
	 * @param Integer ID рецепта
	 * @return Bool
	 */
	public static function delete($Id)
	{
		global $DB, $USER;
		if( intval($Id) > 0 )
		{
			$Sql = "DELETE FROM `b_recipe_favorite` WHERE `recipe` = ".intval($Id).' AND `user`='.intval($USER->GetId());
			$DB->Query($Sql, false);
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Метод возвращает рецепты из избранного
	 * @param Integer ID рецепта
	 * @return Array
	 */
	public static function get_list($User)
	{
		global $DB, $USER;

		$rowFields = $DB->Query("SELECT * FROM `b_recipe_favorite` WHERE `user` = ".$User, false);
		while( $Field = $rowFields->Fetch() ){
			$arReturn[ $Field['id'] ] = $Field['recipe'];
		}
		
		if(count($arReturn) > 0)
		{
			return $arReturn;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Метод количество избранных рецептов
	 * @param Integer ID рецепта
	 * @return Array
	 */
	public static function getCount($User)
	{
		global $DB, $USER;
		$strReturn = "";

		$rowFields = $DB->Query("SELECT count(id) FROM `b_recipe_favorite` WHERE `user` = ".$User, false);
		while( $Field = $rowFields->Fetch() ){
			$strReturn = $Field["count(id)"];
		}
		
		if(strlen($strReturn))
		{
			return $strReturn;
		}
		else
		{
			return false;
		}
	}
}
?>