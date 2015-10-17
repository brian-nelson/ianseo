<?php

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');

	define("debug",false);

	$CFG->TRACE_QUERRIES=false;

	// Controlla se è arrivato un file di gara
	if($_FILES and !empty($_FILES['Gara']['tmp_name'])){

		// piccolo hack per aumentare la RAM a disposizione se il file è pesante
		// si parte da un minimo di 48M e si sale in base alle dimensioni del file
		// per un file da 10 MB (vedi CI di padova 2010, sono necessari circa 128 MB
		// moltiplicando il logaritmo neperiano delle dimensioni del file per 8 si ha
		// la ragionevole speranza che la memoria venga aumentata a sufficienza
		// (129 MB per un file da 10 MB, 134 per uno da 20, 142 per uno da 50)
		$filesize=filesize($_FILES['Gara']['tmp_name']);
// 		ini_set('memory_limit',sprintf('%sM',max(128,intval(log($filesize)*18))));

		include('Common/Fun_TourDelete.php');

		$TourId = tour_import($_FILES['Gara']['tmp_name']);

		// if an ID is returned then everything is fine!
		if($TourId) {
			header('Location: '.$CFG->ROOT_DIR.'Common/TourOn.php?ToId=' . $TourId . '&BackTo='.$CFG->ROOT_DIR.'Main.php');
			exit;
		}

		die(get_text('IncompatibleVersions', 'Tournament', '<a href="http://www.ianseo.net/">Ianseo.net</a>'));
	}


	$JS_SCRIPT = array(
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/Fun_JS.inc.js"></script>'
		);
	$PAGE_TITLE=get_text('TournamentImport','Tournament');

	include('Common/Templates/head.php');

	$onclick='';
	if(GetParameter('TourBusy')) $onclick=' onclick="return(confirm(\''.str_replace("\n",'\n',addslashes(get_text('TourBusy','Tournament'))).'\'))"';
	//$onclick=' onclick="return(confirm(\'Ciao\'))"';

?>
<div align="center">
<div class="medium">
<form method="POST" enctype="multipart/form-data">
<table class="Tabella">
<tr><th class="Title" colspan="2"><?php print get_text('TournamentImport','Tournament'); ?></th></tr>
<tr class="Spacer"><td colspan="2"></td></tr>
<tr><th class="SubTitle"><?php echo get_text('SelFile2Imp','HTT') ?></th>
	<td><input type="file" name="Gara">
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" value="<?php echo get_text('CmdImport','HTT') ?>"<?php echo $onclick; ?>></td></tr>
</table>
</form>
</div>
</div>
<?php

	include('Common/Templates/tail.php');

?>