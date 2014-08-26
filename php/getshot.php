<?if(strlen($_REQUEST["image"]) > 0){
	$img = imagecreatefromstring(base64_decode($_REQUEST["image"]));
	if($img != false)
	{
		imagejpeg($img, 'image_from_android_'.date("d.m.Y H:i:s",time()).'.jpg');
	}
}
?>