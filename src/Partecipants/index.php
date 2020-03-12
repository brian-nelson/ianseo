<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Fun_Various.inc.php');
	CheckTourSession(true);
    checkACL(AclParticipants, AclReadWrite);
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Fun_Partecipants.local.inc.php');
	require_once('Common/Fun_Sessions.inc.php');

	// Check the coherence of Qualifications and Entries tables,
	// adding all the qualification that are stated as "athlete" in Entries

	safe_w_sql("insert ignore into Qualifications (QuId) select EnId from Entries left join Qualifications on EnId=QuId where QuId is null and EnTournament={$_SESSION['TourId']}");

	$PAGE_TITLE=get_text('TourPartecipants','Tournament');

	$OrderCrit='ordTar';
	$GroupType=GROUP_TYPE_TARGET;

	$OrderBy="`Session` ASC, `TargetNo` ASC ";

	$AllTargets=(isset($_REQUEST['AllTargets']) ? $_REQUEST['AllTargets'] : 0);

	$OrderCrit=isset($_REQUEST['ord']) ? $_REQUEST['ord'] : 'ordTar';

	$OrderDir=isset($_REQUEST['dir']) ? $_REQUEST['dir'] : 'ASC';

	$OrderCol=array(
		'ordStatus'	=> 'ASC',
		'ordTar'	=> 'ASC',
		'ordCode'	=> 'ASC',
		'ordLocCode'	=> 'ASC',
		'ordName'	=> 'ASC',
		'ordEmail'	=> 'ASC',
		'ordCaption'	=> 'ASC',
		'ordCtrl'	=> 'ASC',
		'ordSex'	=> 'ASC',
		'ordCountryCode'	=> 'ASC',
		'ordCountry'	=> 'ASC',
		'ordWc'	=> 'ASC',
		'ordDiv'	=> 'ASC',
		'ordAgeCl'	=> 'ASC',
		'ordCl'	=> 'ASC',
		'ordSubCl'	=> 'ASC',
	);

	$OrderCol[$OrderCrit]=($OrderDir=='ASC'?'DESC':'ASC');

	switch ($OrderCrit)
	{
		case 'ordStatus':
			$OrderBy = "EnStatus {$OrderDir} ";
			$GroupType=GROUP_TYPE_NOGROUP;
			break;
		case 'ordTar':
			$OrderBy = "Session {$OrderDir}, TargetNo {$OrderDir}, EnDivision, EnClass, EnFirstName, EnName ";
			$GroupType=GROUP_TYPE_TARGET;
			break;
		case 'ordCode':
			$OrderBy = "EnCode {$OrderDir} ";
			$GroupType=GROUP_TYPE_NOGROUP;
			break;
		case 'ordLocCode':
			$OrderBy = "locBib {$OrderDir} ";
			$GroupType=GROUP_TYPE_NOGROUP;
			break;
		case 'ordName':
			$OrderBy = "EnFirstName collate {$_SESSION['COLLATION']} {$OrderDir},EnName collate {$_SESSION['COLLATION']} {$OrderDir} ";
			$GroupType=GROUP_TYPE_LETTER;
			break;
		case 'ordEmail':
			$OrderBy = "EdEmail {$OrderDir} ";
			$GroupType=GROUP_TYPE_NOGROUP;
			break;
		case 'ordCaption':
			$OrderBy = "EnCaption {$OrderDir} ";
			$GroupType=GROUP_TYPE_NOGROUP;
			break;
		case 'ordCtrl':
			$OrderBy = "EnDob {$OrderDir}, EnFirstName collate {$_SESSION['COLLATION']} ASC,EnName collate {$_SESSION['COLLATION']} ASC ";
			$GroupType=GROUP_TYPE_NOGROUP;
			break;
		case 'ordSex':
			$OrderBy = "EnSex {$OrderDir}, EnFirstName collate {$_SESSION['COLLATION']} ASC,EnName collate {$_SESSION['COLLATION']} ASC ";
			$GroupType=GROUP_TYPE_NOGROUP;
			break;
		case 'ordCountryCode':
			$OrderBy = "CoCode {$OrderDir}, EnFirstName collate {$_SESSION['COLLATION']} ASC,EnName collate {$_SESSION['COLLATION']} ASC ";
			$GroupType=GROUP_TYPE_COUNTRY;
			break;
		case 'ordCountry':
			$OrderBy = "CoName {$OrderDir}, EnFirstName collate {$_SESSION['COLLATION']} ASC,EnName collate {$_SESSION['COLLATION']} ASC ";
			$GroupType=GROUP_TYPE_COUNTRY;
			break;
		case 'ordDiv':
			$OrderBy = "EnDivision {$OrderDir}, EnClass ASC, EnFirstName collate {$_SESSION['COLLATION']} ASC,EnName collate {$_SESSION['COLLATION']} ASC ";
			$GroupType=GROUP_TYPE_CATEGORY;
			break;
		case 'ordWc':
			$OrderBy = "EnWChair {$OrderDir}, EnDivision, EnClass ASC, EnFirstName collate {$_SESSION['COLLATION']} ASC,EnName collate {$_SESSION['COLLATION']} ASC ";
			$GroupType=GROUP_TYPE_CATEGORY;
			break;
		case 'ordAgeCl':
			$OrderBy = "EnAgeClass {$OrderDir}, EnDivision ASC, EnFirstName collate {$_SESSION['COLLATION']} ASC,EnName collate {$_SESSION['COLLATION']} ASC ";
			$GroupType=GROUP_TYPE_CATEGORY;
			break;
		case 'ordCl':
			$OrderBy = "EnClass {$OrderDir}, EnDivision ASC, EnFirstName collate {$_SESSION['COLLATION']} ASC,EnName collate {$_SESSION['COLLATION']} ASC ";
			$GroupType=GROUP_TYPE_CATEGORY;
			break;
		case 'ordSubCl':
			$OrderBy = "EnSubClass {$OrderDir}, EnDivision ASC, EnClass ASC, EnFirstName collate {$_SESSION['COLLATION']} ASC,EnName collate {$_SESSION['COLLATION']} ASC ";
			$GroupType=GROUP_TYPE_NOGROUP;
			break;
	}

	if ($OrderDir=='ASC')
		$NewDir='DESC';
	else
		$NewDir='ASC';

/*
 * Occhio qui!
 * $eval4ref contiene del codice php da eseguire con eval().
 * Raprresenta il test da eseguire per decidere il colore di ogni riga della griglia.
 *  A secondo del tipo di ordinamento abbiamo una colorazione diversa delle righe.
 *  Mettere nel ciclo di stampa della griglia tutte le possibili condizioni è scomodo;
 *  questa soluzione è più facile da mantenere e gestire perchè toccando qui si va a modificare
 *  il test nel ciclo senza andare a cacciare questo switch là dentro.
 */
	$eval4ref='';

	switch($GroupType)
	{
		case GROUP_TYPE_TARGET:
			$eval4ref='
				if ($ref!=substr($r[\'session\'].$r[\'targetno\'],0,-1))
				{
					$ref=substr($r[\'session\'].$r[\'targetno\'],0,-1);
					{{:style}}
				}
			';
			break;
		case GROUP_TYPE_LETTER:
			$eval4ref='
				if ($ref!=substr($r[\'firstname\'],0,1))
				{
					$ref=substr($r[\'firstname\'],0,1);
					{{:style}}
				}
			';
			break;
		case GROUP_TYPE_COUNTRY:
			$eval4ref='
				if ($ref!=$r[\'country_code\'])
				{
					$ref=$r[\'country_code\'];
					{{:style}}
				}
			';
			break;
		case GROUP_TYPE_CATEGORY:
			$eval4ref='
				if ($ref!=$r[\'division\'].'.($OrderCrit=='ordAgeCl'?'$r[\'ageclass\']':'$r[\'class\']').')
				{
					$ref=$r[\'division\'].'.($OrderCrit=='ordAgeCl'?'$r[\'ageclass\']':'$r[\'class\']').';
					{{:style}}
				}
			';
			break;
		default:
		/*
		 * Barbatrucco per fare le righe alternate
		 */
			$eval4ref='
				if ($ref!=$style)
				{
					$ref=$style;
					{{:style}}
				}
			';
			break;
	}

	//print $eval4ref;exit;

	$eval4ref=str_replace('{{:style}}','$style=($style==\'warning\' ? \'\' : \'warning\');',$eval4ref);

	$MyHeader
		= '<tr>'
			. '<td class="Title"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?AllTargets=' . $AllTargets. '&ord=ordStatus&dir=' .  $OrderCol['ordStatus'] . '">' . get_text('Status','Tournament') . '</a></td>'
			. '<td class="Title"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?AllTargets=' . $AllTargets. '&ord=ordTar&dir=' .  $OrderCol['ordTar'] . '">' . get_text('Session') . '</a></td>'
			. '<td class="Title"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?AllTargets=' . $AllTargets. '&ord=ordTar&dir=' .  $OrderCol['ordTar'] . '">' . get_text('Target') . '</a></td>'
			. '<td class="Title"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?AllTargets=' . $AllTargets. '&ord=ordCode&dir=' .  $OrderCol['ordCode'] . '">' . get_text('Code','Tournament') . '</a></td>'
			. '<td class="Title"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?AllTargets=' . $AllTargets. '&ord=ordLocCode&dir=' .  $OrderCol['ordLocCode'] . '">' . get_text('LocalCode','Tournament') . '</a></td>'
			. '<td class="Title"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?AllTargets=' . $AllTargets. '&ord=ordName&dir=' .  $OrderCol['ordName'] . '">' . get_text('FamilyName','Tournament') . '</a></td>'
			. '<td class="Title"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?AllTargets=' . $AllTargets. '&ord=ordName&dir=' .  $OrderCol['ordName'] . '">' . get_text('Name','Tournament') . '</a></td>'
			. '<td class="Title"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?AllTargets=' . $AllTargets. '&ord=ordName&dir=' .  $OrderCol['ordName'] . '">' . get_text('TVNameShort','Tournament') . '</a></td>'
			. '<td class="Title"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?AllTargets=' . $AllTargets. '&ord=ordEmail&dir=' .  $OrderCol['ordEmail'] . '">' . get_text('Email','Tournament') . '</a></td>'
			. '<td class="Title"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?AllTargets=' . $AllTargets. '&ord=ordCaption&dir=' .  $OrderCol['ordCaption'] . '">' . get_text('AccrCaption','Tournament') . '</a></td>'
			. '<td class="Title"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?AllTargets=' . $AllTargets. '&ord=ordCtrl&dir=' .  $OrderCol['ordCtrl'] . '">' .get_text('DOB','Tournament') . '</a></td>'
			. '<td class="Title"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?AllTargets=' . $AllTargets. '&ord=ordSex&dir=' .  $OrderCol['ordSex'] . '">' . get_text('Sex','Tournament') . '</a></td>'
			. '<td class="Title"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?AllTargets=' . $AllTargets. '&ord=ordCountryCode&dir=' .  $OrderCol['ordCountryCode'] . '">' . get_text('Country') . '</a></td>'
			. '<td class="Title"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?AllTargets=' . $AllTargets. '&ord=ordCountry&dir=' .  $OrderCol['ordCountry'] . '">' . get_text('NationShort','Tournament') . '</a></td>'
			. '<td class="Title"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?AllTargets=' . $AllTargets. '&ord=ordWc&dir=' .  $OrderCol['ordWc'] . '">' . get_text('WheelChair', 'Tournament') . '</a></td>'
			. '<td class="Title"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?AllTargets=' . $AllTargets. '&ord=ordDiv&dir=' .  $OrderCol['ordDiv'] . '">' . get_text('Div') . '</a></td>'
			. '<td class="Title"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?AllTargets=' . $AllTargets. '&ord=ordAgeCl&dir=' .  $OrderCol['ordAgeCl'] . '">' . get_text('AgeCl') . '</a></td>'
			. '<td class="Title"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?AllTargets=' . $AllTargets. '&ord=ordCl&dir=' .  $OrderCol['ordCl'] . '">' . get_text('Cl') . '</a></td>'
			. '<td class="Title"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?AllTargets=' . $AllTargets. '&ord=ordSubCl&dir=' .  $OrderCol['ordSubCl'] . '">' . get_text('SubCl','Tournament') . '</a></td>'
			. '<td class="Title">' . get_text('TargetType') . '</td>'
			. '<td class="Title">&nbsp;</td>'
		. '</tr>';

	$Rows=GetRows(null,$OrderBy,$AllTargets);

	$JS_SCRIPT=array(
		phpVars2js(array(
			'StrAreYouSure'=>get_text('MsgAreYouSure')
		)),
		//'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ajax/ObjXMLHttpRequest.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/jQuery/jquery-3.2.1.min.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/Fun_JS.inc.js"></script>',
		'<script type="text/javascript" src="Fun_index_edit.js"></script>',
		'<script type="text/javascript">
			function PopEdit(id,opts)
			{
				var other=(opts!==null ? \'&\'+opts : \'\');
				OpenPopup(\'PopEdit.php?id=\'+id+other, \'PopEdit\', 910,700);
			}

			function chkAllTargets(ord,dir)
			{
				var url="index.php?ord="+ord+"&dir="+dir;

				var c=document.getElementById("AllTargets");

				if (c.checked)
				{
					url+="&AllTargets=1";
				}
				else
				{
					url+="&AllTargets=0";
				}

				window.location=url;
			}

			function deleteRow(id,ord,dir)
			{
				if (confirm(StrAreYouSure))
				{
					document.frm.action=\'DeleteRow.php?id=\'+id+\'&ord=\'+ord+\'&dir=\'+dir+\'&AllTargets=\'+(document.getElementById("AllTargets").checked ? 1 : 0);
					document.frm.submit();
				}
			}

			function add()
			{
				var w=window.open(\'PopEdit.php?id=0\', \'PopEdit\', \'resizeable,scrollbars\');
				w.resizeTo(900,600);
			}
		</script>'
	);

	include('Common/Templates/head.php');
?>

<form name="frm" method="post" action="">
	<table class="Tabella" id="idAthList">
		<tbody>
			<tr><th class="Title" colspan="20"><?php echo get_text('TourPartecipants','Tournament') ?></th></tr>
			<tr class="Divider"><td colspan="20"></td></tr>
			<tr><td colspan="20"><input onclick="chkAllTargets('<?php print $OrderCrit;?>','<?php print $OrderDir;?>');" type="checkbox" id="AllTargets" value="1" <?php print ($AllTargets==1 ? ' checked="checked"' : '');?>/><?php print get_text('AllTargets','Tournament');?></td></tr>
			<tr class="Divider"><td colspan="20"></td></tr>
			<tr>
				<td colspan="20"><a class="Link" href="#" onclick="add(); return false;">:<?php print get_text('CmdAdd','Tournament');?>:</a></td>
			</tr>
			<tr class="Divider"><td colspan="20"></td></tr>
<?php
print $MyHeader;

if (count($Rows)>0) {
	$ref='***';
	$style='';
	foreach ($Rows as $IDrow => $r) {
		if(isset($_REQUEST['diffs']) and $r['code']!=$r['locCode']) continue;
		eval($eval4ref); // <-- Vedi commento "Occhio qui!"

		echo '<tr id="row_'.$IDrow.'_'.$r['id'].'" ondblclick="PopEdit('.($r['id']!==null ? $r['id'] : 0).',\''.($AllTargets==1 ? 'ses=' . $r['session'] . '&tar='.$r['targetno']:'').'\');" class="'.$style.' rowHover">';
		echo '<td class="Center">';
		$img='';
		switch ($r['status'])
		{
			case '0':
				$img='status-ok.gif';
				$title=get_text('CmdOk');
				break;
			case '1':
				$img='status-canshoot.gif';
				$title=get_text('Status_1');
				break;
			case '5':
				$img='status-unknown.gif';
				$title=get_text('Status_5');
				break;
			case '6':
				$img='status-gohome.gif';
				$title=get_text('Status_6');
				break;
			case '7':
				$img='status-notaccredited.gif';
				$title=get_text('Status_7');
				break;
			case '8':
				$img='status-couldshoot.gif';
				$title=get_text('Status_8');
				break;
			case '9':
				$img='status-noshoot.gif';
				$title=get_text('Status_9');
				break;
		}
		if ($r['status']!==null) {
			echo '<img src="'.$CFG->ROOT_DIR.'Common/Images/'.$img.'"  title="'.$title.'"/>';
		}
		echo '</td>';
		echo '<td>'.$r['session'].'</td>';
		echo '<td>'.$r['targetno'].'</td>';
		echo '<td>'.$r['code'].'</td>';
		echo '<td onclick="insertInput(this,\'localCode\')">'.$r['locCode'].'</td>';
		echo '<td onclick="insertInput(this,\'firstname\')">'.$r['firstname'].'</td>';
		echo '<td onclick="insertInput(this,\'name\')">'.$r['name'].'</td>';
		echo '<td onclick="insertInput(this,\'tvname\')">'.$r['tvname'].'</td>';
		echo '<td onclick="insertInput(this,\'email\')">'.$r['email'].'</td>';
		echo '<td onclick="insertInput(this,\'caption\')">'.$r['caption'].'</td>';
		echo '<td>'.dateRenderer($r['dob'],get_text('DateFmt')).'</td>';
		echo '<td>'.$r['sex'].'</td>';
		echo '<td>';
		echo $r['country_code'].($r['country_id2']!=0?'&nbsp;<img src="'.$CFG->ROOT_DIR.'Common/Images/info.gif" style="width:12px;height:12px;" title="'.get_text('Country') . ' (2): ' . $r['country_code2'] .' - ' . $r['country_name2'].'" />':'');
		echo ($r['country_id3']!=0?'&nbsp;<img src="'.$CFG->ROOT_DIR.'Common/Images/info.gif" style="width:12px;height:12px;" title="'.get_text('Country') . ' (3): ' . $r['country_code3'] .' - ' . $r['country_name3'].'" />':'');
		echo '</td>';
		echo '<td>'.$r['country_name'].'</td>';
		echo '<td class="Center">'.($r['wc'] ? 'x' : '').'</td>';
		echo '<td onclick="insertInput(this,\'division\')">'.$r['division'].'</td>';
		echo '<td onclick="insertInput(this,\'ageclass\')">'.$r['ageclass'].'</td>';
		echo '<td onclick="insertInput(this,\'class\')">'.$r['class'].'</td>';
		echo '<td onclick="insertInput(this,\'subclass\')">'.$r['subclass'].'</td>';
		echo '<td>'.get_text($r['targetface_name'], 'Tournament', '', true).'</td>';
		echo '<td class="Center">'.($r['id']!==null?'<img src="'.$CFG->ROOT_DIR.'Common/Images/drop.png" onclick="deleteRow('.$r['id'] . ',\''.$OrderCrit.'\',\''.$OrderDir.'\');"/>':'').'</td>';
		echo '</tr>';
	}
}

?>
		</tbody>
	</table>
</form>

<div id="idOutput">	</div>
<?php
	include('Common/Templates/tail.php');
?>