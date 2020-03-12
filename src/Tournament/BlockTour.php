<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(__FILE__)) . '/config.php');
    checkACL(AclRoot, AclReadWrite);
	CheckTourSession(true);
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Tournament/Fun_Tournament.local.inc.php');

	$ToSet = getBlocksToSet();
	$ToUnset = getBlocksToUnset();

	$QueryBlock
		= "SELECT ToBlock "
		. "FROM Tournament "
		. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";

	$Command=(isset($_REQUEST['Command']) ? $_REQUEST['Command'] : null);
	if (!is_null($Command)) {
	// Devo tirar fuori il vecchio valore nel db
		$OldBlock=0;

		$RsBlock=safe_r_sql($QueryBlock);

		if ($MyRow=safe_fetch($RsBlock)) {
			$OldBlock=$MyRow->ToBlock;
		}

		$Bit = $_REQUEST['Bit'];

		$NewBlock=0;

		if ($Command=='Set') {
			$NewBlock = $OldBlock | $ToSet["$Bit"];
		} elseif ($Command=='Unset') {
			$NewBlock = $OldBlock & $ToUnset["$Bit"];
		}

		//print str_pad(decbin($NewBlock),6,'0',STR_PAD_LEFT);exit;

		$Query
			= "UPDATE Tournament SET ToBlock=" . StrSafe_DB($NewBlock) . " "
			. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']). " ";
		$Rs=safe_w_sql($Query);
	}

	$labels = array();
	$cmds = array();


	$RsBlock=safe_r_sql($QueryBlock);

	if ($MyRow=safe_fetch($RsBlock)) {
		foreach($ToSet as $i => $dummy) {
			$Val=pow(2,$i);
			$labels[$i]=get_text('PhaseBlock_' . $i,'Tournament');

			$v=$MyRow->ToBlock & $Val;

			$queryString=($v!=$Val ? '?Command=set' : '?Command=unset');

			$Command='Command=' . ($v!=$Val ? 'Set' : 'Unset');
			$Value='Bit=' . $i;

			$queryString='?' . $Command . '&amp;' . $Value;

			$link=($v!=$Val ? get_text('BlockSet','Tournament') : get_text('BlockUnset','Tournament'));

			$cmds[$i]='<a href="BlockTour.php' . $queryString . '">' . $link . '</a>';
		}
	}
	else
		exit;


	$PAGE_TITLE=get_text('BlockSetup', 'Tournament');

	include('Common/Templates/head.php');

?>
<div align="center">
	<div class=half>
		<form name="Frm" method="post" action="<?php print $_SERVER['PHP_SELF']; ?>">
			<table class="Tabella">
				<tr><th class="Title" colspan="2"><?php print get_text('BlockSetup','Tournament'); ?></th></tr>
				<?php foreach($labels as $i => $val) { ?>
					<tr>
						<td style="width:40%;"><?php print $labels[$i]; ?></td>
						<td><?php print $cmds[$i]; ?></td>
					</tr>
				<?php } ?>
			</table>
		</form>
	</div>
</div>
<div id="idOutput"></div>
<?php
	include('Common/Templates/tail.php');
?>