<?php
require_once('Common/Fun_FormatText.inc.php');

define("HideCols", GetParameter("IntEvent"));

$Session='';
if(!empty($_REQUEST['Session'])) $Session=" and Left(EnFirstName, 1)='{$_REQUEST['Session']}'";

$Select
	= "SELECT LEFT(UPPER(EnFirstName), 1) as Initial, EnCode as Bib, EnName AS Name, SesName, DivDescription, ClDescription, upper(EnFirstName) AS FirstName, QuSession AS Session, SUBSTRING(QuTargetNo,2) AS TargetNo, CoCode AS NationCode, CoName AS Nation, EnClass AS ClassCode, EnDivision AS DivCode, EnAgeClass as AgeClass, EnSubClass as SubClass, EnStatus as Status "
	. "FROM Entries  "
	. "INNER JOIN Countries ON EnCountry=CoId AND EnTournament=CoTournament "
	. "INNER JOIN Divisions ON EnDivision=DivId AND EnTournament=DivTournament and DivAthlete=1 "
	. "INNER JOIN Classes ON EnClass=ClId AND EnTournament=ClTournament and ClAthlete=1  "
	. "INNER JOIN Qualifications ON EnId=QuId "
	. "LEFT JOIN Session ON QuSession=SesOrder AND SesType='Q' AND EnTournament=SesTournament "
	. "WHERE EnTournament = " . StrSafe_DB($TourId) . " AND EnCode IS NOT NULL and QuSession>0 "
	. $Session
	. ($TVsettings->TVPSession ? " AND QuSession='$TVsettings->TVPSession' " : '')
	. " ORDER BY FirstName, Name, CoCode, CoName ";
$Rs=safe_r_sql($Select);
//print $Select;exit;
$RowCounter = 0;
$oldTarget='x';
$Class='';
while($MyRow=safe_fetch($Rs))
{
	if(!isset($ret[$MyRow->Initial])) {

		// crea l'header della gara
		$tmp = '';

		$NumCol = 6;

		$tmp.= '<tr><th class="Title" colspan="' . ($NumCol) . '">';
		$tmp.= $Arr_Pages[$TVsettings->TVPPage];
		$tmp.= '</th></tr>' . "\n";

		// Titolo della tabella
		$tmp.= '<tr><th class="Title" colspan="' . ($NumCol) . '">' . $MyRow->Initial . '</th></tr>';

// 		$tmp.=  ;
// // 		$tmp.=  ($MyRow->SesName ? $MyRow->SesName : $MyRow->Initial);
// 		$tmp.=  . "\n";

	// Header vero e proprio, incluse le larghezze delle colonne;
		$col=array();
		$tmp.= '<tr>';
		$tmp.= '<th>' . get_text('Target') . '</th>';
		$col[]=9;
		$tmp.= '<th>' . get_text('Athlete') . '</th>';
		$col[]=25;
		$tmp.= '<th>' . get_text('Country') . '</th>';
		$col[]=($TVsettings->TVPViewNationName?30:8);
// 		$tmp.= '<th>' . get_text('Division') . '</th>';
// 		$col[]=6;
// 		$tmp.= '<th>' . get_text('Class') . '</th>';
// 		$col[]=6;
		$tmp.= '<th>' . get_text('Session') . '</th>';
		$col[]=25;
		$tmp.= '</tr>' . "\n";
		$ret[$MyRow->Initial]['head']=$tmp;

		$SumCol=array_sum($col);
		$cols='';
		foreach($col as $w) $cols.='<col width="'.round(100*$w/$SumCol, 0).'%"></col>';

		$ret[$MyRow->Initial]['cols']=$cols;
		$ret[$MyRow->Initial]['fissi']='';
		$ret[$MyRow->Initial]['basso']='';
		$ret[$MyRow->Initial]['type']='DB';
		$ret[$MyRow->Initial]['style']=$ST;
		$ret[$MyRow->Initial]['js']=$JS;
		$ret[$MyRow->Initial]['js'] .= 'FreshDBContent[%1$s]=\'GetNewContent.php?Quadro=%1$s&Rule='.$RULE->TVRId.'&Tour='.$RULE->TVRTournament.'&Segment='.$TVsettings->TVPId.'&Session='.$MyRow->Initial."';\n";
	}
	$RowCounter++;
	$tmp = '';
	//Dati
	if($oldTarget!=intval($MyRow->TargetNo)) {
		if($oldTarget!='x') $tmp.= '<tr style="height:2px;"><th colspan="' . ($NumCol) . '"></th></tr>';
		$oldTarget=intval($MyRow->TargetNo);
		$Class=($Class ? '' : ' class="Next"');
	}

	$tmp.= '<tr' . $Class . '>'
		. '<td class="NumberAlign">' . ltrim($MyRow->TargetNo, '0') . '&nbsp;</td>'
		. '<td>' . $MyRow->FirstName . ' ' . ($TVsettings->TVPNameComplete==0 ? FirstLetters($MyRow->Name) : $MyRow->Name) . '</td>'
		. '<td>' . $MyRow->NationCode . ' ' . ($TVsettings->TVPViewNationName==1 ? $MyRow->Nation : '') . '</td>'
// 		. '<td class="Center">' . (HideCols && $MyRow->DivDescription ? $MyRow->DivDescription : $MyRow->DivCode) . '</td>'
// 		. '<td class="Center">' . (HideCols && $MyRow->ClDescription ? $MyRow->ClDescription : $MyRow->ClassCode) . '</td>'
		. '<td class="Center">' . ($MyRow->SesName ? $MyRow->SesName : get_text('Session') . ' ' . $MyRow->Session) . '</td>'
		. '</tr>' . "\n";

	$ret[$MyRow->Initial]['basso'].=$tmp;
}


?>