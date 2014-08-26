<?
class CFactory {
	function humanDate($strDate = ""){
		$arMonth = Array(
			"01"=>"января",
			"02"=>"февраля",
			"03"=>"марта",
			"04"=>"апреля",
			"05"=>"мая",
			"06"=>"июня",
			"07"=>"июля",
			"08"=>"августа",
			"09"=>"сентября",
			"10"=>"октября",
			"11"=>"ноября",
			"12"=>"декабря",
		);
		$arDate = explode(".",$strDate);
		$strReturn = date("j ".$arMonth[$arDate[1]]." Y", mktime(0, 0, 0, $arDate[1], $arDate[0], $arDate[2]));
		return $strReturn;
	}
	
	function plural_form($n, $forms) {
		return $n%10==1&&$n%100!=11?$forms[0]:($n%10>=2&&$n%10<=4&&($n%100<10||$n%100>=20)?$forms[1]:$forms[2]);
	}
	
	function optimalSize($width, $height, $max_width = 60, $max_height = 60){
		if( $width > $max_width || $height > $max_height ){
			if($width > $height){
				$new_height = ceil($height/100)*ceil($max_width/($width/100));
				return array($max_width, $new_height);
			} elseif($width < $height) {
				$new_width = ceil($width/100)*ceil($max_height/($height/100));
				return array($new_width, $max_height);
			}else{
				return array($max_width, $max_height);
			}
		} else {
			return array($width, $heigh);
		}
	}
}
?>