<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
$arResult["images"] = array();
if(strlen($_REQUEST["url"])){	
	if(strpos($_REQUEST["url"],"http://") === false){
		if(strpos($_REQUEST["url"],"www") === false){
			$_REQUEST["url"] = "http://www.".$_REQUEST["url"];
		}else{
			$_REQUEST["url"] = "http://".$_REQUEST["url"];
		}		
	}elseif(strpos($_REQUEST["url"],"www") === false){
		$_REQUEST["url"] = "http://www.".str_replace("http://","",$_REQUEST["url"]);		
	}
	//echo "@".$_REQUEST["url"]."@";
	//echo "<pre>";print_r();echo "</pre>";die;
	/*$headers = get_headers($_REQUEST["url"], 1);
	if(isset($headers["Location"]) && $headers["Location"] != $_REQUEST["url"]){
		$opts = array(
		  'http'=>array(
		    'method'=>"GET",
		    'header'=>"Accept-language: en\r\n" .
		              "Cookie: foo=bar\r\n"
		  )
		);
		$context = stream_context_create($opts);
		$file = file_get_contents($_REQUEST["url"], false, $context);		
	}else{*/
		$ch = curl_init();
		$header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
		$header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
		$header[] = "Cache-Control: max-age=0";
		$header[] = "Connection: keep-alive";
		$header[] = "Keep-Alive: 300";
		$header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
		$header[] = "Accept-Language: en-us,en;q=0.5";
		$header[] = "Pragma: "; // browsers keep this blank. 
		curl_setopt ($ch, CURLOPT_URL, $_REQUEST["url"] );
		curl_setopt($curl, CURLOPT_USERAGENT, 'Googlebot/2.1 (+http://www.google.com/bot.html)');
	    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
	    curl_setopt($curl, CURLOPT_REFERER, 'http://www.google.com'); 
	    //curl_setopt($ch, CURLOPT_VERBOSE, 1);
	    //curl_setopt($ch, CURLOPT_HEADER, 1);
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1); // follow redirects recursively
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	    $file = curl_exec ( $ch );
	    curl_close($ch);
	/*}*/

	//$file = file_get_contents($url, false, $context);
	//echo "<pre>";print_r($file);echo "</pre>";die;
	//echo $file;die;
	
	$arParsedUrl = parse_url($_REQUEST["url"]);
	if(strlen($arParsedUrl["scheme"]) > 0 && strlen($arParsedUrl["host"]) > 0){
		$url = $arParsedUrl["scheme"]."://".$arParsedUrl["host"];
	}

	//echo "<pre>";print_r($arParsedUrl);echo "</pre>";die;

	//echo $url;die;
	//echo "<pre>";print_r(parse_url($_REQUEST["url"]));echo "</pre>";die;

	/*$opts = array(
	  'http'=>array(
	    'method'=>"GET",
	    'header'=>"Accept-language: en\r\n" .
	              "Cookie: foo=bar\r\n"
	  )
	);*/

	//$context = stream_context_create($opts);

	// Open the file using the HTTP headers set above


	$arIgnoredUrl = array(
		"mc.yandex.ru",
		"//mc.yandex.ru/",
		"top.list.ru",
		"top.mail.ru",
		"top-fwz1.mail.ru",
		"yandex.ru",
		"counter.yandex.ru",
		"mobtop.ru",
		"ad.adriver.ru",
		"img-fotki.yandex.ru",
		"top100-images.rambler.ru"
	);

	

	//$file = file_get_contents($url, false, $context);

	preg_match_all('/<img[^>]+>/i',$file, $result);

	if(!empty($result)){

		$arImages = array();
		$arResult["images"] = array();

		foreach($result[0] as $key => $img_tag){

			preg_match_all('/(src)=("[^"]*")/i',$img_tag, $arImages[$img_tag]);

		}

		//echo "<pre>";print_r($arImages);echo "</pre>";die;

		if(!empty($arImages)){

			foreach($arImages as $img){
				$strValidUrl = str_replace("\"","",$img[2][0]);
				//$strValidUrl = str_replace("\"","",$strValidUrl);
				$arParsedUrl = parse_url($strValidUrl);
				//echo "<pre>";print_r($arParsedUrl);echo "</pre>";

				if(strlen($arParsedUrl["host"]) > 0){
					if(strpos(implode(",",$arIgnoredUrl),$arParsedUrl["host"]) === false){
						if(strpos($strValidUrl,$url) !== false || strpos($strValidUrl,"http://") !== false){
							$arResult["images"][] = $strValidUrl;
						}else{
							$arResult["images"][] = $url.$strValidUrl;
						}
					}
				}elseif(strlen($arParsedUrl["path"])){
					if(strpos($arParsedUrl["path"],"http://") !== false){
						if(strpos($strValidUrl,$url) !== false || strpos($strValidUrl,"http://") !== false){
							$arResult["images"][] = $strValidUrl;
						}else{
							$arResult["images"][] = $url.$strValidUrl;
						}
					}elseif(strpos($arParsedUrl["path"],"//") === false){
						if(strpos($strValidUrl,$url) !== false || strpos($strValidUrl,"http://") !== false){
							$arResult["images"][] = $strValidUrl;
						}else{
							$arResult["images"][] = $url.$strValidUrl;
						}
					}
				}				
			}

		}

	}

}
echo json_encode($arResult);
?>