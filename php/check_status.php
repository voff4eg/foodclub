<?php
/*$dom = new domDocument("1.0", "utf-8"); // Создаём XML-документ версии 1.0 с кодировкой utf-8
$dom->load("../sitemap_000.xml"); // Загружаем XML-документ из файла в объект DOM
$root = $dom->documentElement; // Получаем корневой элемент
$childs = $root->childNodes; // Получаем дочерние элементы у корневого элемента
for ($i = 0; $i < $childs->length; $i++) {
	$user = $childs->item($i); // Получаем следующий элемент из NodeList
	$lp = $user->childNodes;
	$login = $lp->nodeValue;
	echo $login;
}
echo "@";*/

function get_http_response_code($theURL) {
    $headers = get_headers($theURL);
    return substr($headers[0], 9, 3);
}

$ar200StatusLinks = array();
$arOtherStatusLinks = array();

$locs = simplexml_load_file('../sitemap_000.xml');
foreach ($locs as $key => $url) {
	$loc = $url->loc;
	if(get_http_response_code($loc) == "404"){
		$arOtherStatusLinks[] = $loc;
	}else{
		$ar200StatusLinks[] = $loc;
	}
}
//echo "<pre>";print_r($ar200StatusLinks);echo "</pre>";
echo "<pre>";print_r($arOtherStatusLinks);echo "</pre>";
?>