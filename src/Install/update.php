<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	@include('Modules/IanseoTeam/IanseoFeatures/isIanseoTeam.php');
	include('FileList.php');

	$JS_SCRIPT=array(
		'<script type="text/javascript" src="../Common/js/Fun_JS.inc.js"></script>',
		);

	include('Common/Templates/head.php');

	//Gestisci Elenco Remoto
	if(!isset($_REQUEST["Address"]))
	{
		echo '<form name="FrmParam" method="POST" action="">';
		echo '<table class="Tabella" width="50%">';
		echo '<tr><th>' . get_text('Host','HTT') . '</th></tr>';
		echo '<tr><td class="Center">';
		echo '<input type="text" name="Address" id="Address" size="50">';
		echo '</td></tr>';
		echo '<tr><td class="Center"><input type="submit" value="' . get_text('CmdOk') . '"></td></tr>';
		echo '</table>';
		echo '</form>';
	}
	else
	{
		set_time_limit(240);
		$remoteList = "";
		$xmlDoc;

		$rFileList;
		$lFileList;


		$remoteList = @file_get_contents("http://" . $_REQUEST["Address"] . "/Install/UpdateList.php");
		if(strlen($remoteList)>0)
		{
			$xmlDoc=new DOMDocument();
			$x=$xmlDoc->loadXML($remoteList);

			$root=$xmlDoc->documentElement;

			if ($root->nodeName!='FileList')	//Il oot node si chiama FileList
				exit();
			if (!$root->hasChildNodes())		//Ha figli?
				exit();

			// collezione di Files
			$Files = $xmlDoc->getElementsByTagName('File');
			for ($i=0; $i<$Files->length; $i++)
			{
				$f=$Files->item($i);
				$name = $f->getElementsByTagName('Name')->item(0)->textContent;
				$size = $f->getElementsByTagName('Size')->item(0)->textContent;
				$md5  = $f->getElementsByTagName('MD5')->item(0)->textContent;
//				echo $name . " (" . $size . ")<br>";

				$rFileList[] = new File($name, $size, $md5);
			}
		}

		if(count($rFileList)>0)
		{


			//Gestisci Elenco Locale
			$tmp = new FileList("/");
			$tmp->EscludeFiles('^[.]');
			$tmp->Load();
			$lFileList = $tmp->toArray();

			//Calcola Le differenze DA SCARICARE
			$NeedFile = array_udiff($rFileList, $lFileList, array("File","compare"));

			echo "<br />&nbsp;<br />Aggiornamento Files \n";
			echo "<pre>";


			foreach($NeedFile as $value)
			{
				$tmp = file_get_contents("http://" . $_REQUEST["Address"] . "/Install/Download.php?FileName=" . urlencode($value->Name) . "&FileSize=" . urlencode($value->Size));
				if($tmp !== false)
				{
					$tmp = gzuncompress($tmp);
					if($value->MD5 == md5($tmp))
					{
						if(!is_dir(dirname($CFG->INCLUDE_PATH . $value->Name)))
							mkdir(dirname($CFG->INCLUDE_PATH . $value->Name),0775, true);
						file_put_contents ($CFG->INCLUDE_PATH . $value->Name , $tmp);
						echo $value->Name . " (" . $value->Size . " bytes): " . $value->MD5 . "\n";
					}
					else
					{
						echo "ERRORE di DOWNLOAD: " .$value->Name . " (" . $value->Size . " bytes): " . $value->MD5 . "\n";
					}
				}
				else
				{
					echo "ERRORE di DOWNLOAD: " . $value->Name . "\n";
				}
			}
			echo "</pre>";

			//Calcola Le differenze DA CANCELLARE
			//Gestisci Elenco Locale
			$tmp = new FileList("/");
			$tmp->IncludeFolders(true);
			$tmp->Load();
			$lFileList = $tmp->toArray();

			$NeedFile = array_udiff($lFileList, $rFileList, array("File","compare"));

			usort($NeedFile, array("File","compare"));
			echo "Cancellazione Files non utilizzati\n";
			echo "<pre>";

			foreach($NeedFile as $value)
			{
				if(is_dir($CFG->INCLUDE_PATH . $value->Name))
				{
					if(count(scandir($CFG->INCLUDE_PATH . $value->Name)) == 2)
					{
						rmdir($CFG->INCLUDE_PATH . $value->Name);
						echo "CARTELLA: " . $value->Name . "\n";
					}
				}
				else
				{
					if($value->Name != "/Common/config.inc.php")
					{
						unlink($CFG->INCLUDE_PATH . $value->Name);
						echo $value->Name . " (" . $value->Size . " bytes): " . $value->MD5 . "\n";
					}
				}
			}
			echo "</pre>";
		}
		else
		{
			echo "Impossibile verificare lo stato di aggiornamento sul server";
		}
	}

	include('Common/Templates/tail.php');
?>