<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
if(CModule::IncludeModule("iblock")){
	@set_time_limit(0);
	global $USER;
	include($_SERVER["DOCUMENT_ROOT"].'/bitrix/classes/SimpleImage.php');

	chdir("../upload/android/");
	if ($dir = @opendir(getcwd()))
	{
		while(($file = readdir($dir)) !== false){
			if(is_dir($file)&&$file!=="."&&$file!==".."){
				$array["dir"][] =$file;
			}elseif(is_file($file)){
				$array[] = $file;
			}
		}
		closedir($dir);
	}
	//echo "<pre>";print_r($array);echo "</pre>";die();	
	foreach($array as $key => $file){
		echo $file."<br>";
		$filepath = $_SERVER["DOCUMENT_ROOT"]."/upload/drawable-mdpi/".$file;
		$filearray = CFile::MakeFileArray($filepath);
		$arPREVIEW_PICTURE = CIBlock::ResizePicture($filearray, array(
			"WIDTH" => "480",
			"HEIGHT" => "999999999",
			"METHOD" => "resample",
		));
		//$PREVIEW_PICTURE = CFile::SaveFile($arPREVIEW_PICTURE, "iblock");		
	}
	echo "<pre>"; print_r($Uploaded); echo "</pre>";	
}?>