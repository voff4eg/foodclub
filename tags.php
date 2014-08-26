<?
//define("STOP_STATISTICS", true);
function rus2translit($string)
{
    $converter = array(
        'а' => 'a',   'б' => 'b',   'в' => 'v',
        'г' => 'g',   'д' => 'd',   'е' => 'e',
        'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
        'и' => 'i',   'й' => 'y',   'к' => 'k',
        'л' => 'l',   'м' => 'm',   'н' => 'n',
        'о' => 'o',   'п' => 'p',   'р' => 'r',
        'с' => 's',   'т' => 't',   'у' => 'u',
        'ф' => 'f',   'х' => 'h',   'ц' => 'c',
        'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
        'ь' => "",  'ы' => 'y',   'ъ' => "",
        'э' => 'e',   'ю' => 'yu',  'я' => 'ya',
 
        'А' => 'A',   'Б' => 'B',   'В' => 'V',
        'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
        'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
        'И' => 'I',   'Й' => 'Y',   'К' => 'K',
        'Л' => 'L',   'М' => 'M',   'Н' => 'N',
        'О' => 'O',   'П' => 'P',   'Р' => 'R',
        'С' => 'S',   'Т' => 'T',   'У' => 'U',
        'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
        'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
        'Ь' => "",  'Ы' => 'Y',   'Ъ' => "",
        'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
    );
    return strtr($string, $converter);
}
//echo rus2translit('Преобразовывает строку в транслит');


if(CModule::IncludeModule("search"))
{
	$allTags=array();
	$adminTags=file($_SERVER["DOCUMENT_ROOT"]."/admintags.txt");

	$SqlReqestCount=48-count($adminTags);

	$allTags=$adminTags;
	
	if(CModule::IncludeModule('search'))
	{
		$rsTags = CSearchTags::GetList(
			array(),
			array(
				"MODULE_ID" => "iblock",
			),
			array(
				"CNT" => "DESC",
			),
			$SqlReqestCount
		);
		while($arTag = $rsTags->Fetch())
			{
				$allTags[]=$arTag['NAME'];
				$i++;
			}
	}
	
}
asort($allTags);
echo "tagArray[0] = [";
foreach($allTags as $tag1)
{
	echo "'".rus2translit(trim($tag1))."'".', ';
}
echo "];";

echo "tagArray[1] = [";
foreach($allTags as $tag1)
{
	$name=trim($tag1);
	$first = mb_substr($name,0,1, 'UTF-8');//первая буква
	$last = mb_substr($name,1);//все кроме первой буквы
	$first = mb_strtoupper($first, 'UTF-8');
	$last = mb_strtolower($last, 'UTF-8');
	$name1 = $first.$last;
	
	echo "'".trim($name1)."'".', ';
}
echo "];";


?>