<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');

	CheckTourSession(true);
    checkACL(AclAccreditation, AclReadWrite);

	$dir=$CFG->DOCUMENT_PATH . 'Accreditation/IdCard/Photo/';

	$skip=isset($_REQUEST['skip']) && $_REQUEST['skip']==1 ? 1 : 0;

	$bibs=array();
	$photonames=array();
	$files=array();

	$query = "SELECT EnId,EnCode, EdExtra
	    FROM Entries 
	    LEFT JOIN Photos ON EnId=PhEnId
        left join ExtraData on EdId=EnId and EdType='X'
	    WHERE EnTournament=" . StrSafe_DB($_SESSION['TourId']) . "
	    " . ($skip==1 ? " AND (PhPhoto IS NULL OR PhPhoto='') " : "") . " 
        ORDER BY EnCode ASC ";
		//	print $query;
	$rs=safe_r_sql($query);
	while ($myRow=safe_fetch($rs)) {
	    $bibs[$myRow->EnId]=$myRow->EnCode;
	    if($myRow->EdExtra) {
	        $photonames[$myRow->EnId]=$myRow->EdExtra;
	    }
	}

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

	//debug_svela($photonames);

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
		require_once('Common/CheckPictures.php');
	}
	foreach ($files as $f) {
		// se l'id è nella lista di quelli da tirar dentro
		$parts = pathinfo($dir . $f);
		$kk=array_search($parts['filename'], $bibs);
		if ($kk===false) {
            $kk=array_search($parts['basename'], $photonames);
		}
		if ($kk!==false) {
			$errMsg='';
			print $dir . $f . ' --> ';
			if($image=photoresize($dir . $f, true)) {
				$Booth='';
				if($_SESSION['AccBooth']) {
					// pictures will be recorded in a Database!
					$q=safe_r_sql("select EnCode, EnIocCode, ToCode from Entries inner join Tournament on EnTournament=ToId where EnId={$kk}");
					$Booth=safe_fetch($q);
				}

				if(InsertPhoto($kk, $image, $Booth)) {
					print 'ok';
					++$ok;
				} else {
					print 'error';
					++$error;
				}
			} else {
				print $errMsg;
			}
			print '<br/>';
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