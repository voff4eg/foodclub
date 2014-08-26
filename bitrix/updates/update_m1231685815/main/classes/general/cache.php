<?
/*********************************************************************
						Caching
*********************************************************************/
class CPHPCache
{
	var $filename;
	var $folder;
	var $content;
	var $vars;
	var $TTL;
	var $uniq_str;
	var $initdir;
	var $bStarted = false;
	var $bInit = "NO";

	function GetPath($uniq_str)
	{
		$un = md5($uniq_str);
		return substr($un, 0, 2)."/".$un.".php";
	}

	function Clean($uniq_str, $initdir = false, $basedir = "cache")
	{
		$cache_file = $_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/".$basedir."/".$initdir."/".CPHPCache::GetPath($uniq_str);
		$res = false;
		if (file_exists($cache_file))
		{
			@chmod($cache_file, BX_FILE_PERMISSIONS);
			if(unlink($cache_file))
				$res = true;
		}
		return $res;
	}

	function InitCache($TTL, $uniq_str, $initdir=false, $basedir = "cache")
	{
		global $APPLICATION, $USER;
		if($initdir === false)
			$initdir = $APPLICATION->GetCurDir();

		$this->TTL = $TTL;
		$this->folder = $_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/".$basedir."/".$initdir."/";
		$this->filename = $this->folder.CPHPCache::GetPath($uniq_str);
		$this->tmp_filename = $this->folder.md5(mt_rand()).".tmp";
		$this->uniq_str = $uniq_str;
		$this->initdir = $initdir;
		$this->vars = false;

		if($TTL<=0)
			return false;

		if(is_object($USER))
		{
			if(strtoupper($_GET["clear_cache"])=="Y" && $USER->CanDoOperation('cache_control'))
				return false;

			if(strtoupper($_GET["clear_cache_session"])=="Y" && $USER->CanDoOperation('cache_control'))
				$_SESSION["SESS_CLEAR_CACHE"] = "Y";
			elseif(strlen($_GET["clear_cache_session"])>0 && $USER->CanDoOperation('cache_control'))
				unset($_SESSION["SESS_CLEAR_CACHE"]);
		}

		if($_SESSION["SESS_CLEAR_CACHE"] == "Y")
			return false;

		if(!file_exists($this->filename))
			return false;

		$INCLUDE_FROM_CACHE='Y';
		if(file_exists($this->filename) && !include($this->filename))
			return false;

		if(IntVal($datecreate)<mktime()-$TTL)
			return false;

		$arAllVars = unserialize($ser_content);
		$this->content = $arAllVars["CONTENT"];
		$this->vars = $arAllVars["VARS"];
		return true;
	}

	function Output()
	{
		echo $this->content;
	}

	function GetVars()
	{
		return $this->vars;
	}

	function StartDataCache($TTL=false, $uniq_str=false, $initdir=false, $vars=Array(), $basedir = "cache")
	{
		$narg = func_num_args();
		if($narg<=0)
			$TTL = $this->TTL;
		if($narg<=1)
			$uniq_str = $this->uniq_str;
		if($narg<=2)
			$initdir = $this->initdir;
		if($narg<=3)
			$vars = $this->vars;

		if($this->InitCache($TTL, $uniq_str, $initdir, $basedir))
		{
			$this->Output();
			return false;
		}

		if($TTL<=0)
			return true;

		ob_start();
		$this->vars = $vars;
		$this->bStarted = true;
		return true;
	}

	function AbortDataCache()
	{
		if(!$this->bStarted) return;
		$this->bStarted = false;
		ob_end_flush();
	}

	function EndDataCache($vars=false)
	{
		if(!$this->bStarted) return;
		$this->bStarted = false;
		CheckDirPath($this->filename);

		if($handle = fopen($this->tmp_filename, "wb+"))
		{
			$text_contents = ob_get_contents();
			if($vars!==false)
				$this->vars = $vars;
			$arAllVars = Array("CONTENT"=>$text_contents, "VARS"=>$this->vars);
			$contents = "<?";
			$contents .= "\nif(\$INCLUDE_FROM_CACHE!='Y')return false;";
			$contents .= "\n\$datecreate = '".str_pad(mktime(), 12, "0", STR_PAD_LEFT)."';";
			$contents .= "\n\$dateexpire = '".str_pad(mktime() + IntVal($this->TTL), 12, "0", STR_PAD_LEFT)."';";
			$contents .= "\n\$ser_content = '".str_replace("'", "\'", str_replace("\\", "\\\\", serialize($arAllVars)))."';";
			$contents .= "\nreturn true;";
			$contents .= "\n?>";
			$written = fwrite($handle, $contents);
			$len = function_exists('mb_strlen')? mb_strlen($contents, 'latin1'): strlen($contents);
			if($written === $len)
			{
				fclose($handle);
				if(file_exists($this->filename))
					unlink($this->filename);
				rename($this->tmp_filename, $this->filename);
				if(file_exists($this->tmp_filename))
					unlink($this->tmp_filename);
			}
			else
			{
				fclose($handle);
				if(file_exists($this->filename))
					unlink($this->filename);
				if(file_exists($this->tmp_filename))
					unlink($this->tmp_filename);
			}
		}
		//This workaround is used to allow
		//using of caching before localredirect
		//avoids Headers already sent Warning
 		if(strlen(ob_get_contents())>0)
			ob_end_flush();
		else
			ob_end_clean();
	}

	function IsCacheExpired($path)
	{
		if(!file_exists($path))
			return true;

		$dateexpire = 0;

		$INCLUDE_FROM_CACHE='Y';

		$dfile = fopen($path, "rb");
		$str_tmp = fread($dfile, 150);
		fclose($dfile);

		preg_match("/dateexpire\s*=\s*'([\d]+)'/im", $str_tmp, $arTmp);
		if (strlen($arTmp[1])<=0 || DoubleVal($arTmp[1])<mktime())
			return true;

		return false;
	}
}

class CPageCache
{
	var $filename;
	var $folder;
	var $content;
	var $TTL;
	var $bStarted = false;
	var $uniq_str = false;
	var $init_dir = false;

	function GetPath($uniq_str)
	{
		$un = md5($uniq_str);
		return substr($un, 0, 2)."/".$un.".html";
	}

	function Clean($uniq_str, $initdir = false, $basedir = "cache")
	{
		$cache_file = $_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/".$basedir."/".$initdir."/".CPageCache::GetPath($uniq_str);
		$res = false;
		if (file_exists($cache_file))
		{
			@chmod($cache_file, BX_FILE_PERMISSIONS);
			if(unlink($cache_file))
				$res = true;
		}
		return $res;
	}

	function InitCache($TTL, $uniq_str, $initdir = false, $basedir = "cache")
	{
		global $APPLICATION, $USER;
		if($initdir === false)
			$initdir = $APPLICATION->GetCurDir();

		$this->TTL = $TTL;
		$this->folder = $_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/".$basedir."/".$initdir."/";
		$this->filename = $this->folder.CPageCache::GetPath($uniq_str);
		$this->tmp_filename = $this->folder.md5(mt_rand()).".tmp";
		$this->init_dir = $initdir;

		if($TTL<=0)
			return false;

		if(is_object($USER))
		{
			if(strtoupper($_GET["clear_cache"])=="Y" && $USER->CanDoOperation('cache_control'))
				return false;
			if(strtoupper($_GET["clear_cache_session"])=="Y" && $USER->CanDoOperation('cache_control'))
				$_SESSION["SESS_CLEAR_CACHE"] = "Y";
			elseif(strlen($_GET["clear_cache_session"])>0 && $USER->CanDoOperation('cache_control'))
				unset($_SESSION["SESS_CLEAR_CACHE"]);
		}

		if($_SESSION["SESS_CLEAR_CACHE"] == "Y")
			return false;

		if(!file_exists($this->filename))
			return false;

		if(!($handle = fopen($this->filename, "rb")))
			return false;

		$fdatacreate = fread($handle, 2);
		if($fdatacreate=="BX")
		{
			$fdatacreate = fread($handle, 12);
			$fdateexpire = fread($handle, 12);
		}
		else
			$fdatacreate .= fread($handle, 10);

		if(IntVal($fdatacreate)<mktime()-$TTL)
		{
			fclose($handle);
			return false;
		}

		$this->content = fread($handle, filesize($this->filename)-10);
		fclose ($handle);
		return true;
	}

	function Output()
	{
		echo $this->content;
	}

	function StartDataCache($TTL, $uniq_str=false, $initdir=false, $basedir = "cache")
	{
		if($this->InitCache($TTL, $uniq_str, $initdir, $basedir))
		{
			$this->Output();
			return false;
		}

		if($TTL<=0)
			return true;

		ob_start();
		$this->bStarted = true;
		return true;
	}

	function EndDataCache()
	{
		if(!$this->bStarted) return;
		$this->bStarted = false;
		CheckDirPath($this->filename);
		if($handle = fopen($this->tmp_filename, "wb+"))
		{
			$contents = ob_get_contents();
			fwrite($handle, "BX".str_pad(mktime(), 12, "0", STR_PAD_LEFT).str_pad(mktime() + IntVal($this->TTL), 12, "0", STR_PAD_LEFT));
			fwrite($handle, $contents);
			fclose($handle);
			if(file_exists($this->filename))
				unlink($this->filename);
			rename($this->tmp_filename, $this->filename);
		}
		ob_end_flush();
	}

	function IsCacheExpired($path)
	{
		if(!file_exists($path))
			return true;

		if(!($handle = fopen($path, "rb")))
			return false;

		$fdatacreate = fread($handle, 2);
		if($fdatacreate=="BX")
		{
			$fdatacreate = fread($handle, 12);
			$fdateexpire = fread($handle, 12);
		}
		else
			$fdataexpire = 0;

		fclose($handle);

		if(IntVal($fdateexpire)<mktime())
			return true;

		return false;
	}
}

function BXClearCache($full=false, $initdir="")
{
	if($full !== true && $full !== false && $initdir === "" && is_string($full))
	{
		$initdir = $full;
		$full = true;
	}
	$res = true;
	$path = $_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/cache".$initdir;
	if(is_dir($path) && ($handle = opendir($path)))
	{
		while(($file = readdir($handle)) !== false)
		{
			if($file == "." || $file == "..") continue;

			if(is_dir($path."/".$file))
			{
				if(!BXClearCache($full, $initdir."/".$file))
					$res = false;
				else
				{
					@chmod($path."/".$file, BX_DIR_PERMISSIONS);
					if(!rmdir($path."/".$file))
						$res = false;
				}
			}
			else
			{
				$expired = $full;
				if(!$expired)
				{
					if(substr($file, -5)==".html")
						$expired = CPageCache::IsCacheExpired($path."/".$file);
					elseif(substr($file, -4)==".php")
						$expired = CPHPCache::IsCacheExpired($path."/".$file);
					else
						$res = false;
				}

				if($expired)
				{
					@chmod($path."/".$file, BX_FILE_PERMISSIONS);
					if(!unlink($path."/".$file))
						$res = false;
				}
			}
		}
		closedir($handle);
	}

	return $res;
}
// The main purpose of the class is:
// one read - many uses - optional one write
// of the set of variables
class CCacheManager
{
	var $CACHE= array();
	var $CACHE_PATH = array();
	var $VARS = array();
	var $TTL = array();
	// Tries to read cached variable value from the file
	// Returns true on success
	// overwise returns false
	function Read($ttl, $uniqid, $table_id=false)
	{
		global $DB;
		if(array_key_exists($uniqid, $this->CACHE))
			return true;
		else
		{
			$this->CACHE[$uniqid] = new CPHPCache;
			$this->CACHE_PATH[$uniqid] = $DB->type.($table_id===false?"":"/".$table_id);
			$this->TTL[$uniqid] = $ttl;
			return $this->CACHE[$uniqid]->InitCache($ttl, $uniqid, $this->CACHE_PATH[$uniqid], "managed_cache");
		}
	}
	// This method is used to read the variable value
	// from the cache after successfull Read
	function Get($uniqid)
	{
		if(array_key_exists($uniqid, $this->VARS))
			return $this->VARS[$uniqid];
		elseif(array_key_exists($uniqid, $this->CACHE))
			return $this->CACHE[$uniqid]->GetVars();
		else
			return false;
	}
	// Sets new value to the variable
	function Set($uniqid, $val)
	{
		if(array_key_exists($uniqid, $this->CACHE))
			$this->VARS[$uniqid]=$val;
	}
	// Marks cache entry as invalid
	function Clean($uniqid, $table_id=false)
	{
		global $DB;
		$obCache = new CPHPCache;
		$obCache->Clean($uniqid, $DB->type."/".($table_id!==false?$table_id."/":""), "managed_cache");
		if(array_key_exists($uniqid, $this->CACHE))
		{
			unset($this->CACHE[$uniqid]);
			unset($this->CACHE_PATH[$uniqid]);
			unset($this->VARS[$uniqid]);
		}
	}
	// Marks cache entries associated with the table as invalid
	function CleanDir($table_id)
	{
		global $DB;
		$strPath = $DB->type."/".$table_id;
		foreach($this->CACHE_PATH as $uniqid=>$Path)
		{
			if($Path==$strPath)
			{
				unset($this->CACHE[$uniqid]);
				unset($this->CACHE_PATH[$uniqid]);
				unset($this->VARS[$uniqid]);
			}
		}
		DeleteDirFilesEx(BX_PERSONAL_ROOT."/managed_cache/".$strPath);
	}
	// Clears all managed_cache
	function CleanAll()
	{
		global $DB;
		$this->CACHE= array();
		$this->CACHE_PATH = array();
		$this->VARS = array();
		$this->TTL = array();
		DeleteDirFilesEx(BX_PERSONAL_ROOT."/managed_cache/".$DB->type);
	}
	// Use it to flush cache to the files.
	// Causion: only at the end of all operations!
	function _Finalize()
	{
		global $DB, $CACHE_MANAGER;
		$obCache = new CPHPCache;
		foreach($CACHE_MANAGER->CACHE as $uniqid=>$val)
		{
			if(array_key_exists($uniqid, $CACHE_MANAGER->VARS))
			{
				$obCache->StartDataCache($CACHE_MANAGER->TTL[$uniqid], $uniqid, $CACHE_MANAGER->CACHE_PATH[$uniqid],  $CACHE_MANAGER->VARS[$uniqid], "managed_cache");
				$obCache->EndDataCache();
			}
		}
	}
}

global $CACHE_MANAGER;
$CACHE_MANAGER = new CCacheManager;

/*****************************************************************************************************/
/************************  CStackCacheManager  *******************************************************/
/*****************************************************************************************************/
class CStackCacheManager
{
	var $cache = array();
	var $cacheLength = array();
	var $cacheTTL = array();

	var $defaultLength = 10;
	var $defaultTTL = 3600;

	var $eventHandlerAdded = false;

	function SetLength($entity, $length)
	{
		if (defined("BITRIX_SKIP_STACK_CACHE") && BITRIX_SKIP_STACK_CACHE)
			return;

		$length = IntVal($length);
		if ($length <= 0)
			$length = $this->defaultLength;

		$this->cacheLength[$entity] = IntVal($length);
	}

	function SetTTL($entity, $ttl)
	{
		if (defined("BITRIX_SKIP_STACK_CACHE") && BITRIX_SKIP_STACK_CACHE)
			return;

		$ttl = IntVal($ttl);
		if ($ttl <= 0)
			$ttl = $this->defaultTTL;

		$this->cacheTTL[$entity] = IntVal($ttl);
	}

	function Init($entity, $length, $ttl)
	{
		if (defined("BITRIX_SKIP_STACK_CACHE") && BITRIX_SKIP_STACK_CACHE)
			return;

		if (!$this->eventHandlerAdded)
		{
			AddEventHandler("main", "OnEpilog", array("CStackCacheManager", "SaveAll"));
			$this->eventHandlerAdded = True;
		}

		$length = IntVal($length);
		if ($length <= 0)
			$length = $this->defaultLength;

		$ttl = IntVal($ttl);
		if ($ttl <= 0)
			$ttl = $this->defaultTTL;

		if (!array_key_exists($entity, $this->cache))
			$this->cache[$entity] = array();

		if (!array_key_exists($entity, $this->cacheLength))
			$this->cacheLength[$entity] = IntVal($length);
		if (!array_key_exists($entity, $this->cacheTTL))
			$this->cacheTTL[$entity] = IntVal($ttl);
	}

	function Load($entity)
	{
		if (defined("BITRIX_SKIP_STACK_CACHE") && BITRIX_SKIP_STACK_CACHE)
			return;

		if (!array_key_exists($entity, $this->cache))
			$this->Init($entity, $this->defaultLength, $this->defaultTTL);

		$objCache = new CPHPCache;
		if ($objCache->InitCache($this->cacheTTL[$entity], $entity, $GLOBALS["DB"]->type."/".$entity, "stack_cache"))
			$this->cache[$entity] = $objCache->GetVars();
	}
	//NO ONE SHOULD NEVER EVER USE INTEGER $id HERE
	function Clear($entity, $id = False)
	{
		if (defined("BITRIX_SKIP_STACK_CACHE") && BITRIX_SKIP_STACK_CACHE)
			return;

		if (!array_key_exists($entity, $this->cache))
			$this->Load($entity);

		if ($id !== False)
		{
			if (array_key_exists($id, $this->cache[$entity]))
				unset($this->cache[$entity][$id]);
		}
		else
		{
			$this->cache[$entity] = array();

			$objCache = new CPHPCache;
			$objCache->Clean($entity, $GLOBALS["DB"]->type."/".$entity, "stack_cache");
		}
	}

	// Clears all managed_cache
	function CleanAll()
	{
		global $DB;
		$this->cache = array();
		$this->cacheLength = array();
		$this->cacheTTL = array();
		DeleteDirFilesEx(BX_PERSONAL_ROOT."/stack_cache/".$DB->type);
	}
	//NO ONE SHOULD NEVER EVER USE INTEGER $id HERE
	function Exist($entity, $id)
	{
		if (defined("BITRIX_SKIP_STACK_CACHE") && BITRIX_SKIP_STACK_CACHE)
			return False;

		if (!array_key_exists($entity, $this->cache))
			$this->Load($entity);

		if (array_key_exists($id, $this->cache[$entity]))
			return True;
		else
			return False;
	}
	//NO ONE SHOULD NEVER EVER USE INTEGER $id HERE
	function Get($entity, $id)
	{
		if (defined("BITRIX_SKIP_STACK_CACHE") && BITRIX_SKIP_STACK_CACHE)
			return False;

		if (!array_key_exists($entity, $this->cache))
			$this->Load($entity);

		if (array_key_exists($id, $this->cache[$entity]))
		{
			$result = $this->cache[$entity][$id];
			unset($this->cache[$entity][$id]);
			$this->cache[$entity] = $this->cache[$entity] + array($id => $result);

			return $result;
		}
		else
		{
			return False;
		}
	}
	//NO ONE SHOULD NEVER EVER USE INTEGER $id HERE
	function Set($entity, $id, $value)
	{
		if (defined("BITRIX_SKIP_STACK_CACHE") && BITRIX_SKIP_STACK_CACHE)
			return;

		if (!array_key_exists($entity, $this->cache))
			$this->Load($entity);

		if (array_key_exists($id, $this->cache[$entity]))
		{
			unset($this->cache[$entity][$id]);
			$this->cache[$entity] = $this->cache[$entity] + array($id => $value);
		}
		else
		{
			$this->cache[$entity] = $this->cache[$entity] + array($id => $value);
			if (count($this->cache[$entity]) > $this->cacheLength[$entity])
				array_shift($this->cache[$entity]);
		}
	}

	function Save($entity)
	{
		if (defined("BITRIX_SKIP_STACK_CACHE") && BITRIX_SKIP_STACK_CACHE)
			return;

		$objCache = new CPHPCache;
		$objCache->Clean($entity, $GLOBALS["DB"]->type."/".$entity, "stack_cache");

		if (array_key_exists($entity, $this->cache))
		{
			$objCache->StartDataCache($this->cacheTTL[$entity], $entity, $GLOBALS["DB"]->type."/".$entity,  $this->cache[$entity], "stack_cache");
			$objCache->EndDataCache();
		}
	}

	function SaveAll()
	{
		if (defined("BITRIX_SKIP_STACK_CACHE") && BITRIX_SKIP_STACK_CACHE)
			return;

		foreach ($GLOBALS["stackCacheManager"]->cache as $entity => $value)
			$GLOBALS["stackCacheManager"]->Save($entity);
	}

	function MakeIDFromArray($arVals)
	{
		$id = "id";

		sort($arVals);

		for ($i = 0; $i < count($arVals); $i++)
			$id .= "_".$arVals[$i];

		return $id;
	}
}

$GLOBALS["stackCacheManager"] = new CStackCacheManager();
?>
