<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$c=count($arResult['ITEMS'])-1; $i=0;
?>

<?//print_r($arResult['ITEMS']);?>
<?/*
[
<? foreach($arResult['ITEMS']['trade'] as $it):?>
	{"trade":{"id":"<?=$it['ID'];?>","name":"<?=$it['NAME1'];?>"},
	"models":[
	<? foreach($arResult['ITEMS']['models'][$it['ID']] as $it1):?>{"id": "<?=$it1['ID'];?>", "name": "<?=$it1['NAME1'];?>"},<?endforeach;?>
	{}]},
<?endforeach;?>

]
	*/?>
<? foreach($arResult['ITEMS']['trade'] as $it):?>
<? $it['models']=$arResult['ITEMS']['models'][$it['id']];?>
<?
	$it['trade']['id']=$it['id'];
	$it['trade']['name']=$it['name'];
	unset($it['id']);
	unset($it['name']);
?>
<?$r[]=$it?>
<?endforeach;?>

<?
if($r)
	echo  json_encode($r);
//else echo '{}';
?>