<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');

	CheckTourSession(true);

	$dir=$CFG->DOCUMENT_PATH . 'Accreditation/IdCard/Photo/';

	$skip=isset($_REQUEST['skip']) && $_REQUEST['skip']==1 ? 1 : 0;

	$bibs=array();
	$files=array();

	$query
		= "SELECT "
			. "EnId,EnCode "
		. "FROM "
			. "Entries "
			. "LEFT JOIN "
				. "Photos "
			. "ON EnId=PhEnId "
		. "WHERE "
			. "EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
			. ($skip==1 ? " AND (PhPhoto IS NULL OR PhPhoto='') " : "") . " "
		. "ORDER BY "
			. "EnCode ASC ";
		//	print $query;
	$rs=safe_r_sql($query);
	while ($myRow=safe_fetch($rs)) $bibs[$myRow->EnId]=$myRow->EnCode;

	/*print'<pre>';
	print_r($bibs);
	print '</pre>';exit;*/

	if ($handle = opendir($dir))
	{
	    while ($file = readdir($handle))
	    {
	        if ($file != "." && $file != "..")
	        {
	            $files[]=$file;
	        }
	    }
	    closedir($handle);
	}

	/*print'<pre>';
	print_r($files);
	print '</pre>';exit;*/

	include('Common/Templates/head.php');
	include('Common/PhotoResize.php');
?>
<b>Start</b><br/>
<?php
// per ogni files
	$ok=0;
	$error=0;
	if(IsBlocked(BIT_BLOCK_ACCREDITATION)) {
		print 'Accreditation is blocked';
	} else {
		foreach ($files as $f)
		{
		// se l'id è nella lista di quelli da tirar dentro
			$parts = pathinfo($dir . $f);
			$kk=array_search($parts['filename'],$bibs);
			if ($kk!==false)
			{
				$errMsg='';
				print $dir . $f . ' --> ';
				if($image=photoresize($dir . $f, true)) {
					$query
						= "INSERT INTO Photos (PhEnId,PhPhoto,PhPhotoEntered) "
						. "VALUES("
							. Strsafe_DB($kk) . ","
							. "'" . $image ."', "
							. "NOW()"
						. ") "
						. "ON DUPLICATE KEY UPDATE "
							. "PhPhoto='" . $image . "', PhPhotoEntered=NOW()";
						//print $query . '<br>';
					$rs=safe_w_sql($query);
					if ($rs)
					{
						print 'ok';
						++$ok;
					}
					else
					{
						print 'error';
						++$error;
					}
				} else {
					print $errMsg;
				}
				print '<br/>';
			}
		}
	}
?>
<b>End</b><br/>
<b>Total bibs: <?php print count ($bibs); ?></b><br/>
<b>Success: <?php print $ok; ?></b><br/>
<b>Error: <?php print $error; ?></b><br/>
<?php
	include('Common/Templates/tail.php');
?>