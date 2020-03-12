<?php
class FileList
{
	private $Files;
	private $BasePath;
	private $Exclusion;
	private $Filter;
	private $boolShowSize;
	private $boolShowMD5;
	private $includeFolder;


	function __construct($dir2scroll)
	{
		$this->BasePath = $dir2scroll;
		$this->Exclusion = NULL;
		$this->Filter = NULL;
		$this->boolShowSize = false;
		$this->boolShowMD5 = false;
		$this->includeFolder = false;
	}

	function Load()
	{
		global $CFG;
		$this->FilesInFolder($CFG->INCLUDE_PATH . $this->BasePath);

		natcasesort($this->Files);
	}

	function IncludeFolders($Include = false)
	{
		$this->includeFolder = $Include;
	}

	function ShowSize($Show = false)
	{
		$this->boolShowSize = $Show;
	}

	function ShowMD5($Show = false)
	{
		$this->boolShowMD5 = $Show;
	}

	function ApplyFilter($NewFilter = NULL)
	{
		$this->Filter = $NewFilter;
	}

	function EscludeFiles($EscludeFilter = NULL)
	{
		$this->Exclusion = $EscludeFilter;
	}

	private function FilesInFolder($Folder)
	{
		if(is_dir($Folder))
		{
			if ($handle = opendir($Folder))
			{
	    		while (false !== ($file = readdir($handle)))
	    		{
	        		if ($file != "." && $file != ".." && (is_null($this->Exclusion) ||  preg_match('/'. ($this->Exclusion).'/i', $file) == 0) && !(strstr($Folder,"//Common")!==false && $file=="config.inc.php"))
	        		{
	        			if(is_dir($Folder . "/" . $file))
	        			{
	        				$this->FilesInFolder($Folder . "/" . $file);
	        				if($this->includeFolder)
	        					$this->Files[] = $Folder . "/" . $file;
	        			}
	        			elseif(is_file($Folder . "/" . $file) && (is_null($this->Filter) || preg_match('/'.$this->Filter.'/i', $file) == 0))
	        			{
	            			$this->Files[] = $Folder . "/" . $file;
	        			}
	        		}
	    		}
	    		closedir($handle);
			}
		}
	}

	function count()
	{
		return count($this->Files);
	}

	public function toArray()
	{
		global $CFG;
		$arrFiles = array();
		foreach($this->Files as $detail)
		{
			$arrFiles[] = new File(str_replace('//','/',str_replace($CFG->DOCUMENT_PATH, "", $detail)), filesize($detail), @md5_file($detail));
		}
		return $arrFiles;
	}

	function XML()
	{
		global $CFG;
		$XmlDoc = new DOMDocument('1.0', 'UTF-8');
		$XmlRoot = $XmlDoc->createElement('FileList');
		$XmlRoot->setAttribute('Count', $this->count());
		$XmlDoc->appendChild($XmlRoot);

		foreach($this->Files as $detail)
		{
			$FileNode = $XmlDoc->createElement('File');
			$XmlRoot->appendChild($FileNode);
			$tmpNode = $XmlDoc->createElement('Name', str_replace("//","/",str_replace($CFG->DOCUMENT_PATH, "", $detail)));
			$FileNode->appendChild($tmpNode);
			if($this->boolShowSize)
			{
				$tmpNode = $XmlDoc->createElement('Size',filesize($detail));
				$FileNode->appendChild($tmpNode);
			}
			if($this->boolShowMD5)
			{
				$tmpNode = $XmlDoc->createElement('MD5', md5_file($detail));
				$FileNode->appendChild($tmpNode);
			}

		}
		return $XmlDoc->SaveXML();
	}
}

class File
{
	var $Name;
	var $Size;
	var $MD5;

	function __construct($myName, $mySize, $myMD5)
	{
		$this->Name = $myName;
		$this->Size = $mySize;
		$this->MD5 = $myMD5;
	}

    function compare($a, $b)
    {
        if ($a->Name == $b->Name && $a->Size == $b->Size && $a->MD5 == $b->MD5)
        	return 0;
        else
        {
			if($a->Name > $b->Name)
				return -1;
			else if($a->Name < $b->Name)
				return 1;
			else
			{
				if($a->Size > $b->Size)
					return -1;
				else if($a->Size < $b->Size)
					return 1;
				else
				{
					if($a->MD5 > $b->MD5)
						return -1;
					else
						return 1;
				}
			}
        }
    }
}