<?
/************************************************************************/
/********************* XML Classes **************************************/
/************************************************************************/

/**********************************************************************/
/*********   CDataXMLNode   ****************************************/
/**********************************************************************/
class CDataXMLNode
{
	var $name;				// Name of the node
	var $content;			// Content of the node
	var $children;			// Subnodes
	var $attributes;		// Attributes

	function CDataXMLNode()
	{
	}

	function name() { return $this->name; }
	function children() { return $this->children; }
	function textContent() { return $this->content; }

	function getAttribute($attribute)
	{
		foreach ($this->attributes as $anode)
			if ($anode->name == $attribute)
				return $anode->content;
		return "";
	}

	function getAttributes()
	{
		return $this->attributes;
	}

	function namespaceURI()
	{
		return $this->getAttribute("xmlns");
	}

	function elementsByName($tagname)
	{
		$result = array();

		if ($this->name == $tagname)
			array_push($result, $this);

		if (count($this->children))
		foreach ($this->children as $node)
		{
			$more = $node->elementsByName($tagname);
			if (is_array($more) and count($more))
				foreach($more as $mnode)
					array_push($result, $mnode);
		}
		return $result;
	}

	function _SaveDataType_OnDecode(&$result, $name, $value)
	{
		if (isset($result[$name]))
		{
			$i = 1;
			while (isset($result[$i.":".$name])) $i++;
			$result[$i.":".$name] = $value;
			return "indexed";
		}
		else
		{
			$result[$name] = $value;
			return "common";
		}
	}

	function decodeDataTypes($attrAsNodeDecode = false)
	{
		$result = array();

		if (!$this->children)
			$this->_SaveDataType_OnDecode($result, $this->name(), $this->textContent());
		else
			foreach ($this->children() as $child)
			{
				$cheese = $child->children();
				if (!$cheese or !count($cheese))
				{
					$this->_SaveDataType_OnDecode($result, $child->name(), $child->textContent());
				}
				else
				{
					$cheresult = $child->decodeDataTypes();
					if (is_array($cheresult))
						$this->_SaveDataType_OnDecode($result, $child->name(), $cheresult);
				}
			}

		if ($attrAsNodeDecode)
			foreach ($this->getAttributes() as $child)
			{
				$this->_SaveDataType_OnDecode($result, $child->name(), $child->textContent());
			}

		return $result;
	}

	function &__toString()
	{
		$ret = "";

		switch ($this->name)
		{
			case "cdata-section":
				$ret = "<![CDATA[";
				$ret .= $this->content;
				$ret .= "]]>";
				break;

			default:
				$isOneLiner = false;

				if (count($this->children)==0 && (strlen($this->content)<=0))
					$isOneLiner = true;

				$attrStr = "";

				if (count($this->attributes) > 0)
					foreach ($this->attributes as $attr)
					{
						$attrStr .= " ".$attr->name."=\"".$attr->content."\" ";
					}

				if ($isOneLiner)
					$oneLinerEnd = " /";
				else
					$oneLinerEnd = "";

				$ret = "<".$this->name.$attrStr.$oneLinerEnd.">";

				if (count($this->children)>0)
					foreach ($this->children as $child)
					{
						$ret .= $child->__toString();
					}

				if (!$isOneLiner)
				{
					if (strlen($this->content)>0)
						$ret .= $this->content;

					$ret .= "</".$this->name.">";
				}

				break;
		}

		return $ret;
	}

	function &__toArray()
	{
		$arInd = array();
		$retHash = array();

		$retHash["@"] = array();
		if (count($this->attributes) > 0)
			foreach ($this->attributes as $attr)
			{
				$retHash["@"][$attr->name] = $attr->content;
				$numAdded++;
			}

		$retHash["#"] = "";
		if (strlen($this->content)>0)
		{
			$retHash["#"] = $this->content;
		}
		else
		{
			if (count($this->children)>0)
			{
				$ar = array();
				foreach ($this->children as $child)
				{
					if (array_key_exists($child->name, $arInd))
						$arInd[$child->name] = $arInd[$child->name] + 1;
					else
						$arInd[$child->name] = 0;

					$ar[$child->name][$arInd[$child->name]] = $child->__toArray();
				}
				$retHash["#"] = $ar;
			}
		}

		return $retHash;
	}
}



/**********************************************************************/
/*********   CDataXMLDocument   ******************************************/
/**********************************************************************/
class CDataXMLDocument
{
	var $version;				// XML version
	var $encoding;				// XML encoding

	var $children;
	var $root;

	function CDataXMLDocument()
	{
	}

	function elementsByName($tagname)
	{
		$result = array();
		if (count($this->children))
		foreach ($this->children as $node)
		{
			$more = $node->elementsByName($tagname);
			if (is_array($more) and count($more))
				foreach($more as $mnode)
					array_push($result, $mnode);
		}
		return $result;
	}

	function encodeDataTypes( $name, $value)
	{
		$Xsd = array(
			"string"=>"string", "bool"=>"boolean", "boolean"=>"boolean",
			"int"=>"integer", "integer"=>"integer", "double"=>"double", "float"=>"float", "number"=>"float",
			"array"=>"anyType", "resource"=>"anyType",
			"mixed"=>"anyType", "unknown_type"=>"anyType", "anyType"=>"anyType"
		);

		$node = new CDataXMLNode();
		$node->name = $name;

		$nameSplited = split(":", $name);
		if ($nameSplited)
			$name = $nameSplited[count($nameSplited) - 1];

		if (is_object($value))
		{
			$ovars = get_object_vars($value);
			foreach ($ovars as $pn => $pv)
			{
				$decode = CDataXMLDocument::encodeDataTypes( $pn, $pv);
				if ($decode) array_push($node->children, $decode);
			}
		}
		else if (is_array($value))
		{
			foreach ($value as $pn => $pv)
			{
				$decode = CDataXMLDocument::encodeDataTypes( $pn, $pv);
				if ($decode) array_push($node->children, $decode);
			}
		}
		else
		{
			if (isset($Xsd[gettype($value)]))
			{
				$node->content = $value;
			}
		}
		return $node;
	}

	/* Returns a XML string of the DOM document */
	function &__toString()
	{
		$ret = "<"."?xml";
		if (strlen($this->version)>0)
			$ret .= " version=\"".$this->version."\"";
		if (strlen($this->encoding)>0)
			$ret .= " encoding=\"".$this->encoding."\"";
		$ret .= "?".">";

		if (count($this->children) > 0)
			foreach ($this->children as $child)
			{
				$ret .= $child->__toString();
			}

		return $ret;
	}

	/* Returns an array of the DOM document */
	function &__toArray()
	{
		$arRetArray = array();

		if (count($this->children)>0)
			foreach ($this->children as $child)
			{
				$arRetArray[$child->name] = $child->__toArray();
			}

		return $arRetArray;
	}
}



/**********************************************************************/
/*********   CDataXML   **************************************************/
/**********************************************************************/
class CDataXML
{
	var $tree;
	var $TrimWhiteSpace;
	
	var $delete_ns = true;

	function CDataXML($TrimWhiteSpace = True)
	{
		$this->TrimWhiteSpace = ($TrimWhiteSpace ? True : False);
		$this->tree = False;
	}

	function Load($file)
	{
		global $APPLICATION;

		unset($this->tree);
		$this->tree = False;

		if (file_exists($file))
		{
			$content = file_get_contents($file);
			$charset = "windows-1251";
			if (preg_match("/<"."\?XML[^>]{1,}encoding=[\"']([^>\"']{1,})[\"'][^>]{0,}\?".">/i", $content, $matches))
			{
				$charset = Trim($matches[1]);
			}
			$content = $APPLICATION->ConvertCharset($content, $charset, SITE_CHARSET);
			$this->tree = &$this->__parse($content);
			return $this->tree !== false;
		}

		return false;
	}

	function LoadString($text)
	{
		unset($this->tree);
		$this->tree = False;

		if (strlen($text)>0)
		{
			$this->tree = &$this->__parse($text);
			return $this->tree !== false;
		}

		return false;
	}

	function &GetTree()
	{
		return $this->tree;
	}

	function &GetArray()
	{
		return $this->tree->__toArray();
	}

	function &GetString()
	{
		return $this->tree->__toString();
	}

	function &SelectNodes($strNode)
	{
		if (!is_object($this->tree))
			return false;

		$result = &$this->tree;

		$tmp = explode("/", $strNode);
		for ($i = 1; $i < count($tmp); $i++)
		{
			if ($tmp[$i] != "")
			{
				if (!is_array($result->children))
					return false;

				$bFound = False;
				for ($j = 0; $j < count($result->children); $j++)
				{
					if ($result->children[$j]->name==$tmp[$i])
					{
						$result = &$result->children[$j];
						$bFound = True;
						break;
					}
				}

				if (!$bFound)
					return False;
			}
		}

		return $result;
	}

	function xmlspecialchars($str)
	{
		static $search = array("&","<",">","\"","'");
		static $replace = array("&amp;","&lt;","&gt;","&quot;","&apos;");
		return str_replace($search, $replace, $str);
	}

	function xmlspecialcharsback($str)
	{
		static $search = array("&lt;","&gt;","&quot;","&apos;","&amp;");
		static $replace = array("<",">","\"","'","&");
		return str_replace($search, $replace, $str);
	}

	/* Will return an DOM object tree from the well formed XML. */
	function &__parse(&$strXMLText)
	{
		global $APPLICATION;

		$TagStack = array();

		$oXMLDocument = new CDataXMLDocument();

		// stip the !doctype
		$strXMLText = &preg_replace("%<\!DOCTYPE.*?\]>%is", "", $strXMLText);
		$strXMLText = &preg_replace("%<\!DOCTYPE.*?>%is", "", $strXMLText);

		// get document version and encoding from header
		preg_match_all("#<\?(.*?)\?>#i", $strXMLText, $arXMLHeader_tmp);
		foreach ($arXMLHeader_tmp[0] as $strXMLHeader_tmp)
		{
			preg_match_all("/([a-zA-Z:]+=\".*?\")/i", $strXMLHeader_tmp, $arXMLParam_tmp);
			foreach ($arXMLParam_tmp[0] as $strXMLParam_tmp)
			{
				if (strlen($strXMLParam_tmp)>0)
				{
					$arXMLAttribute_tmp = explode("=\"", $strXMLParam_tmp);
					if ($arXMLAttribute_tmp[0]=="version")
						$oXMLDocument->version = substr($arXMLAttribute_tmp[1], 0, strlen($arXMLAttribute_tmp[1]) - 1);
					elseif ($arXMLAttribute_tmp[0]=="encoding")
						$oXMLDocument->encoding = substr($arXMLAttribute_tmp[1], 0, strlen($arXMLAttribute_tmp[1]) - 1);
				}
			}
		}

		// strip header
		$strXMLText = &preg_replace("#<\?.*?\?>#", "", $strXMLText);

		// strip comments
		$strXMLText = &CDataXML::__stripComments($strXMLText);

		$oXMLDocument->root = &$oXMLDocument->children;
		$currentNode = &$oXMLDocument;

		$pos = 0;
		$endTagPos = 0;
		while ($pos < strlen($strXMLText))
		{
			$char = substr($strXMLText, $pos, 1);
			if ($char == "<")
			{
				// find tag name
				$endTagPos = strpos($strXMLText, ">", $pos);

				// tag name with attributes
				$tagName = substr($strXMLText, $pos + 1, $endTagPos - ($pos + 1));

				// check if it's an endtag </tagname>
				if (substr($tagName, 0, 1) == "/")
				{
					$lastNodeArray = array_pop($TagStack);
					$lastTag = $lastNodeArray["TagName"];

					$lastNode = &$lastNodeArray["ParentNodeObject"];

					unset($currentNode);
					$currentNode = &$lastNode;

					$tagName = substr($tagName, 1, strlen($tagName));

					// strip out namespace; nameSpace:Name
					if ($this->delete_ns)
					{
						$colonPos = strpos($tagName, ":");
	
						if ($colonPos > 0)
							$tagName = substr($tagName, $colonPos + 1, strlen($tagName));
					}
					if ($lastTag != $tagName)
					{
						print("Error parsing XML, unmatched tags $tagName");
						return false;
					}
				}
				else
				{
					$firstSpaceEnd = strpos($tagName, " ");
					$firstNewlineEnd = strpos($tagName, "\n");

					if ($firstNewlineEnd != false)
					{
						if ($firstSpaceEnd != false)
						{
							$tagNameEnd = min($firstSpaceEnd, $firstNewlineEnd);
						}
						else
						{
							$tagNameEnd = $firstNewlineEnd;
						}
					}
					else
					{
						if ($firstSpaceEnd != false)
						{
							$tagNameEnd = $firstSpaceEnd;
						}
						else
						{
							$tagNameEnd = 0;
						}
					}

					if ($tagNameEnd > 0)
					{
						$justName = substr($tagName, 0, $tagNameEnd);
					}
					else
						$justName = $tagName;


					// strip out namespace; nameSpace:Name
					if ($this->delete_ns)
					{
						$colonPos = strpos($justName, ":");
	
						if ($colonPos > 0)
							$justName = substr($justName, $colonPos + 1, strlen($justName));
					}
					// remove trailing / from the name if exists
					if (substr($justName, -1) == "/")
					{
						$justName = substr($justName, 0, strlen($justName ) - 1);
					}


					// check for CDATA
					$cdataSection = "";
					$isCDATASection = false;
					$cdataPos = strpos($strXMLText, "<![CDATA[", $pos);
					if ($cdataPos == $pos && $pos > 0)
					{
						$isCDATASection = true;
						$endTagPos = strpos($strXMLText, "]]>", $cdataPos);
						$cdataSection = &substr($strXMLText, $cdataPos + 9, $endTagPos - ( $cdataPos + 9));

						// new CDATA node
						unset($subNode);
						$subNode = new CDataXMLNode();
						$subNode->name = "cdata-section";
						$subNode->content = $cdataSection;

						$currentNode->children[] = &$subNode;

						$pos = $endTagPos;
						$endTagPos += 2;
					}
					else
					{
						// normal start tag
						unset($subNode);
						$subNode = new CDataXMLNode();
						$subNode->name = $justName;

						$currentNode->children[] = &$subNode;
					}

					// find attributes
					if ($tagNameEnd > 0)
					{
						$attributePart = &substr($tagName, $tagNameEnd, strlen($tagName));

						// attributes
						unset($attr);
						$attr = &CDataXML::__parseAttributes($attributePart);

						if ($attr != false)
							$subNode->attributes = &$attr;
					}

					// check it it's a oneliner: <tagname /> or a cdata section
					if ($isCDATASection == false)
						if (substr($tagName, -1) != "/")
						{
							array_push($TagStack,
								array("TagName" => $justName, "ParentNodeObject" => &$currentNode));

							unset($currentNode);
							$currentNode = &$subNode;
						}
				}
			}

			$pos = strpos($strXMLText, "<", $pos + 1);

			if ($pos == false)
			{
				// end of document
				$pos = strlen($strXMLText);
			}
			else
			{
				// content tag
				$tagContent = substr($strXMLText, $endTagPos + 1, $pos - ($endTagPos + 1));

				if (($this->TrimWhiteSpace && (trim($tagContent)!="")) || !$this->TrimWhiteSpace)
				{
					unset($subNode);
/*
					if ($oXMLDocument->encoding)
					{
						$tagContent = $APPLICATION->ConvertCharset($tagContent,
							$oXMLDocument->encoding, SITE_CHARSET);
					}
*/
					// convert special chars
					$tagContent = &str_replace("&amp;", "&", $tagContent);
					$tagContent = &str_replace("&gt;", ">", $tagContent);
					$tagContent = &str_replace("&lt;", "<", $tagContent);
					$tagContent = &str_replace("&apos;", "'", $tagContent);
					$tagContent = &str_replace("&quot;", '"', $tagContent);

					$currentNode->content = $tagContent;

				}
			}
		}

		return $oXMLDocument;
	}

	function __stripComments(&$str)
	{
		$str = &preg_replace("#<\!--.*?-->#s", "", $str);
		return $str;
	}

	/* Parses the attributes. Returns false if no attributes in the supplied string is found */
	function &__parseAttributes($attributeString)
	{
		$ret = false;

		preg_match_all("/(\\S+)\\s*=\\s*([\"'])(.*?)\\2/s".BX_UTF_PCRE_MODIFIER, $attributeString, $attributeArray);

		foreach ($attributeArray[0] as $i => $attributePart)
		{
			if (trim($attributePart) != "" && trim($attributePart) != "/")
			{
				$attributeName = $attributeArray[1][$i];

				// strip out namespace; nameSpace:Name
				if ($this->delete_ns)
				{
					$colonPos = strpos($attributeName, ":");
	
					if ($colonPos > 0)
						$attributeName = substr($attributeName, $colonPos + 1, strlen($attributeName));
				}
				$attributeValue = $attributeArray[3][$i];

				unset($attrNode);
				$attrNode = new CDataXMLNode();
				$attrNode->name = $attributeName;
				$attrNode->content = $attributeValue;

				$ret[] = &$attrNode;
			}
		}
		return $ret;
	}
}
?>