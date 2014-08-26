<?require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');


if(CModule::IncludeModule("iblock"))
{
	//фильтр для получения элемента
	$arFilter['ID']=intval($_REQUEST['id']);
	$arFilter['IBLOCK_ID']=33;
	
	$arSelect = Array("ID", "NAME", "PREVIEW_PICTURE", "PROPERTY_users", "PROPERTY_brand", "PROPERTY_cost", "PROPERTY_tech_type");
	//получение элемента - модель
	$resModel = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>50), $arSelect);
	while($ob = $resModel->GetNextElement())
	{
		$arFields = $ob->GetFields();//На каждого пользователя возвращается по экземпляру
		//print_r($ob);
		if(trim($arFields['PREVIEW_PICTURE'])) $arFields['imageSRC'] = CFile::GetPath($arFields['PREVIEW_PICTURE']);
		else $arFields['imageSRC'] = "/images/icons/kitchen.png";
		$arElem=$arFields;
		$arUsersElem[]=$arFields['PROPERTY_USERS_VALUE'];//массив всех пользователей
	}	
	//добавление пользователя в список влaдельцев
	if(in_array($USER->GetID(), $arUsersElem) )//такого пользователя уже есть
	{
		//Удаление пользователя из массива
		if(($key = array_search($USER->GetID(),$arUsersElem)) !== false)
		{
			unset($arUsersElem[$key]);
		}

		//update
		$pv=array( 'users'=>$arUsersElem, "brand"=>$arFields["PROPERTY_BRAND_VALUE"], "cost"=>$arFields["PROPERTY_COST_VALUE"], "tech_type"=>$arFields["PROPERTY_TECH_TYPE_VALUE"]);
		CIBlockElement::SetPropertyValues($arElem['ID'], 33, $pv);
		
		
		//удаление комментария
		$arFilter="";
		$arSelect = Array("ID", "NAME", "DETAIL_TEXT");
		$arFilter['PROPERTY_model']=intval($_REQUEST['id']);
		$arFilter['IBLOCK_ID']=30;
		$arFilter['PROPERTY_user'] = $USER->GetID();
		$resCom = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>50), $arSelect);
		
		while($ob = $resCom->GetNextElement())
		{
			$arFieldsCom = $ob->GetFields();
			//print_R( $arFieldsCom);
			CIBlockElement::Delete($arFieldsCom['ID']);
		}
		
	}
	
	
	
	
//print_r($_REQUEST['id']);

}




?>