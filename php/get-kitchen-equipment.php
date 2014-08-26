<?require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

/*
    [type] => 12859
    [trade] => 12861
    [model] => 12862
    [mark] => 4
    [comment] =>
*/
if(intval($_REQUEST['oldId']) && intval($_REQUEST['model']))
{
	$_REQUEST['id']=intval($_REQUEST['oldId']);
	include('delete-kitchen-equipment.php');
}
if(CModule::IncludeModule("iblock"))
{
	//фильтр для получения элемента
	$arFilter['ID']=intval($_REQUEST['model']);
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
	//добавление пользователя в список влыдельцев
	if(!in_array($USER->GetID(), $arUsersElem) )//такого пользователя еще нет
	{
		$arUsersElem[]=$USER->GetID();
		//update
		$pv=array( 'users'=>$arUsersElem, "brand"=>$arFields["PROPERTY_BRAND_VALUE"], "cost"=>$arFields["PROPERTY_COST_VALUE"], "tech_type"=>$arFields["PROPERTY_TECH_TYPE_VALUE"]);
		//	echo "222222<pre>";
		//	print_r($pv);
		//	echo "</pre>";
		CIBlockElement::SetPropertyValues($arElem['ID'], 33, $pv);
		
		//Учет рейтинга
		$arFilterR['PROPERTY_model']=intval($_REQUEST['model']);
		$arFilterR['IBLOCK_ID']=34;
		
		$flagRExist=true;
		$arSelectR = Array("ID", "PROPERTY_summ", "PROPERTY_count_people", "PROPERTY_model", "PROPERTY_rating_string");
		//получение элемента - модель
		$resR = CIBlockElement::GetList(Array(), $arFilterR, false, Array("nPageSize"=>50), $arSelectR);
		while($obR = $resR->GetNextElement())
		{

			$flagRExist=false;
			$arFieldsR = $obR->GetFields();
			
			$el = new CIBlockElement;

			$PROP = array();
			$PROP['summ'] = $arFieldsR["PROPERTY_SUMM_VALUE"] + intval($_REQUEST['mark']);  
			$PROP['count_people'] = $arFieldsR["PROPERTY_COUNT_PEOPLE_VALUE"]+1;  
			$PROP['model'] = $arFilter['ID'];  
			$PROP['rating_string'] = $arFieldsR['PROPERTY_RATING_STRING_VALUE'].$USER->GetID().":".intval($_REQUEST['mark']).";";  

			$arLoadProductArray = Array(
			  "MODIFIED_BY"    => $USER->GetID(), // элемент изменен текущим пользователем
			  //"IBLOCK_SECTION_ID" => false,          // элемент лежит в корне раздела
			  "IBLOCK_ID"      => 34,
			  "PROPERTY_VALUES"=> $PROP,
			  "NAME"           => $arFilter['ID'],
			  "ACTIVE"         => "Y",            // активен
			  //"DETAIL_TEXT"    => htmlspecialchars($_REQUEST['comment']),
			  );
		//	  echo "RAITING";
		//	  print_r($arLoadProductArray);
			CIBlockElement::SetPropertyValues($arFieldsR['ID'], 34,$PROP);
		}
		$echoR=$PROP['summ']/$PROP['count_people'];
		if($flagRExist)
		{
			$el = new CIBlockElement;

			$PROP = array();
			$PROP['summ'] = intval($_REQUEST['mark']);  
			$PROP['count_people'] = 1;  
			$PROP['model'] = $arFilter['ID'];  
			$PROP['rating_string'] = $USER->GetID().":".intval($_REQUEST['mark']).";";  
			$echoR=$PROP['summ']/$PROP['count_people'];
			$arLoadProductArray = Array(
			  "MODIFIED_BY"    => $USER->GetID(), // элемент изменен текущим пользователем
			  //"IBLOCK_SECTION_ID" => false,          // элемент лежит в корне раздела
			  "IBLOCK_ID"      => 34,
			  "PROPERTY_VALUES"=> $PROP,
			  "NAME"           => $arFilter['ID'],
			  "ACTIVE"         => "Y",            // активен
			  //"DETAIL_TEXT"    => htmlspecialchars($_REQUEST['comment']),
			  );
			$RAITING_ID = $el->Add($arLoadProductArray);
		}
		
		
		
		
			//добавление комментария пользователя
			if($_REQUEST['comment'])
			{
				$el = new CIBlockElement;

				$PROP = array();
				$PROP['user'] = $USER->GetID();  
				$PROP['model'] = $arFilter['ID'];  

				$arLoadProductArray = Array(
				  "MODIFIED_BY"    => $USER->GetID(), // элемент изменен текущим пользователем
				  //"IBLOCK_SECTION_ID" => false,          // элемент лежит в корне раздела
				  "IBLOCK_ID"      => 30,
				  "PROPERTY_VALUES"=> $PROP,
				  "NAME"           => $USER->GetID()." ".$arFilter['ID'],
				  "ACTIVE"         => "Y",            // активен
				  "DETAIL_TEXT"    => htmlspecialchars($_REQUEST['comment']),
				  );
				$COMMENT_ID = $el->Add($arLoadProductArray);
				//if($PRODUCT_ID = $el->Add($arLoadProductArray)){}
			
			}
		
		
	}
	
	




}

//print_r($_REQUEST);
$res = CIBlockElement::GetByID($arFields["PROPERTY_BRAND_VALUE"]);
if($ar_res = $res->GetNext())
{
  $BRAND= $ar_res['NAME'];
  $BRANDID= $ar_res['ID'];
}
  
 $res = CIBlockElement::GetByID($arFields["PROPERTY_TECH_TYPE_VALUE"]);
if($ar_res = $res->GetNext())
{
  $TECH= $ar_res['NAME'];
  $TECHID= $ar_res['ID'];
}
  if(!$_REQUEST['id'])$_REQUEST['id']=$_REQUEST['model'];

$returnText='{
	"id": "'.$_REQUEST['id'].'",
	"title": {
		"name": "'.$TECH.'",
		"id": "'.$TECHID.'"
	},
	"brand": {
		"name": "'.$BRAND.'",
		"id": "'.$BRANDID.'"
	},
	"text": "'.htmlspecialchars($_REQUEST['comment']).'",
	"rating": "'.round($echoR).'",
	"price": "'.$arFields["PROPERTY_COST_VALUE"].'",
	"image": {
		"src": "'.$arFields['imageSRC'].'",
		"width": "155",
		"height": "155",
		"alt": "'.$TECH.' '.$BRAND.'"
	},
	"model": 
	{
		"name": "'.$arFields["NAME"].'",
		"id": "'.$_REQUEST['id'].'"
	}
}';
$returnText=str_replace("\r\n", " ", $returnText);
$returnText=str_replace("\n", " ", $returnText);
if($returnText)
echo $returnText;
else
{
echo "{}";
}
?>