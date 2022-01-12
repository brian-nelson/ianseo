<?php
class FileList
{
	private $Files=array();
	private $BasePath;
	private $Exclusion;
	private $Filter;
	private $boolShowSize;
	private $boolShowMD5;
	private $includeFolder;
	private $Updatable=true;
	private $Prefix='';


	function __construct($dir2scroll='') {
		$this->BasePath = $dir2scroll;
		$this->Prefix=strlen($this->BasePath);
		$this->Exclusion = NULL;
		$this->Filter = NULL;
		$this->boolShowSize = false;
		$this->boolShowMD5 = false;
		$this->includeFolder = false;
	}

	function Load()
	{
		if(!is_writable($this->BasePath)) return false;
		$this->FilesInFolder($this->BasePath);
		ksort($this->Files);
		return $this->Updatable;
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
    			$DistFolder=substr($Folder, $this->Prefix);
	    		while (false !== ($file = readdir($handle)))
	    		{
	    			$DistFile=substr($DistFolder . "/" . $file,1);
	        		if ($file != "."
	        			&& $file != ".."
	        			&& (is_null($this->Exclusion) ||  preg_match('/'. ($this->Exclusion).'/i', $file) == 0)
	        			&& $DistFolder!="/Images" // Pictures are "personal" so don't touch!
	        			&& !($DistFolder=="/Common" && $file=="config.inc.php") // config.inc is "personal" so don't touch!
	        			&& !($DistFolder=="/Common" && $file=="DebugOverrides.php") // overrides are "personal" so don't touch!
// 	        			&& !($DistFolder=="/Common" && $file=="Languages") // Langs have their own update!
	        			&& !($DistFolder=="/TV" && $file=="Photos") // Pictures are "personal" so don't touch!
	        			&& !($DistFolder=="/Install" && $file=="dbdumps") // Dumps of the DB... better leave them safe!
	        			&& !($DistFolder=="/Modules" && $file=="Custom") // Custom directory in Modules is "private" too
                        && !($DistFolder=="/Common/Languages" && is_dir($DistFile)) // Languages will get recreated by the update process
	        			) {

	        			if(!is_link($Folder) and !is_link($Folder . "/" . $file) and !is_writable($Folder . "/" . $file)) {
                            $this->Updatable=false;
                        }

	        			$tmp=array(
							's' => filesize($Folder . "/" . $file),
							'm' => @md5_file($Folder . "/" . $file)
							);

	        			if(is_dir($Folder . "/" . $file))
	        			{
	        				$this->FilesInFolder($Folder . "/" . $file);
	        				if($this->includeFolder) {
	        					$this->Files[$DistFile] = $tmp;
	        				}
	        			}
	        			elseif(is_file($Folder . "/" . $file) && (is_null($this->Filter) || preg_match('/'.$this->Filter.'/i', $file) == 0))
	        			{
	            			$this->Files[$DistFile] = $tmp;
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

	function Serialize() {
        $ret = new stdClass();

		$ret->ProgVersion = ProgramVersion;
		$ret->ProgRelease = ProgramRelease;
		$ret->ProgBuild = ProgramBuild;
		$ret->UUID = GetParameter('UUID2');
		$ret->DbVersion = GetParameter('DBUpdate');
		$ret->AcceptGPL = GetParameter('AcceptGPL');
		$ret->Files = $this->Files;
		return serialize($ret);
	}
}

