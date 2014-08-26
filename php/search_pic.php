<?
$_SERVER["DOCUMENT_ROOT"] = "/home/webserver/www";
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
function ReturnSizes($max,$arParams){
	$arSizes = array();
	$max_c = 99999999;
	if($arParams["WIDTH"] > $max && $arParams["HEIGHT"] > $max){		
		if($arParams["WIDTH"] > $arParams["HEIGHT"]){
			$arSizes = array("width"=>$max_c,"height"=>$max);
		}elseif($arParams["HEIGHT"] > $arParams["WIDTH"]){
			$arSizes = array("width"=>$max,"height"=>$max_c);
		}else{
			$arSizes = array("width"=>$max,"height"=>$max);
		}
	}elseif($arParams["WIDTH"] > $max){
		$arSizes = array("width"=>$max_c,"height"=>$max);
	}elseif($arParams["HEIGHT"] > $max){
		$arSizes = array("width"=>$max,"height"=>$max_c);
	}
	return $arSizes;
}

if(CModule::IncludeModule("iblock")){
	$rsRecipes = CIBlockElement::GetList(array(),array("IBLOCK_ID"=>5,"PROPERTY_lib"=>"y","ID"=>57910),false,false,array("ID","PREVIEW_PICTURE","PROPERTY_search_pic"));
	while($arRecipe = $rsRecipes->GetNext()){
		if(intval($arRecipe["PROPERTY_SEARCH_PIC_VALUE"]) > 0){
			$strSearchPicPath = CFile::GetPath($arRecipe["PROPERTY_SEARCH_PIC_VALUE"]);
			//if(!file_exists($_SERVER["DOCUMENT_ROOT"].$strSearchPicPath)){
				if(intval($arRecipe["PREVIEW_PICTURE"])){
					$arrNewImages[$arRecipe["ID"]] = CFile::CopyFile($arRecipe["PREVIEW_PICTURE"]);					
				}
			//}
		}else{
			if(intval($arRecipe["PREVIEW_PICTURE"])){
				$arrNewImages[$arRecipe["ID"]] = CFile::CopyFile($arRecipe["PREVIEW_PICTURE"]);					
			}
		}
	}
	if(!empty($arrNewImages)){
		echo "<pre>";print_r($arrNewImages);echo "</pre>";die;
		foreach($arrNewImages as $key => $image){
			//echo "@".CFile::GetPath($image)."@";
			$arFile = CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"].CFile::GetPath($image));
			$arImgParamsM = CFile::_GetImgParams($image, $iSizeWHTTP, $iSizeHHTTP);
			//echo "<pre>";print_R($arImgParamsM);echo "</pre>";
			$arSizes = ReturnSizes(50,$arImgParamsM);
			//echo "arSizes<pre>";print_R($arSizes);echo "</pre>";die;
			if(!empty($arSizes)){
				CAllFile::ResizeImage(
		          &$arFile, // путь к изображению, сюда же будет записан уменьшенный файл
		          $arSizes,
		          BX_RESIZE_IMAGE_PROPORTIONAL // метод масштабирования. обрезать прямоугольник без учета пропорций
		        );
			}

	        CIBlockElement::SetPropertyValuesEx($key, 5, array(
				"search_pic" => array(
					"VALUE" => $arFile,
				)
			));
		}
		echo "@";
	}
}
?>