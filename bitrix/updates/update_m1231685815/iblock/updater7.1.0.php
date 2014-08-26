<?
//Install components
$updater->CopyFiles("install/components", "components");

if($updater->TableExists("b_iblock_section") || $updater->TableExists("B_IBLOCK_SECTION"))
{
	if(!$DB->Query("SELECT SOCNET_GROUP_ID from b_iblock_section WHERE 1=0", true))
	{
		$updater->Query(array(
			"MySql"  => "alter table b_iblock_section add SOCNET_GROUP_ID int(18)",
			"MsSql"  => "alter table b_iblock_section add SOCNET_GROUP_ID int",
			"Oracle" => "alter table b_iblock_section add SOCNET_GROUP_ID number(18)",
		));
	}
}
?>