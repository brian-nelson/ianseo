<?php
require_once('Common/Fun_Sessions.inc.php');
require_once('Common/Fun_FormatText.inc.php');

$Select
	= "SELECT EnCode as Bib, EnName AS Name, SesName, "
	. " PhPhoto, EnId, "
	. " upper(EnFirstName) AS FirstName, SUBSTRING(AtTargetNo,1,1) AS Session,"
	. " SUBSTRING(AtTargetNo,2) AS TargetNo,"
	. " CoCode AS NationCode, CoName AS Nation, EnClass AS ClassCode,"
	. " EnDivision AS DivCode, EnAgeClass as AgeClass,"
	. " EnSubClass as SubClass, EnStatus as Status "
	. "FROM AvailableTarget at "
	. "LEFT JOIN (SELECT EnTournament, QuTargetNo, EnId, EnCode, EnName, EnFirstName, CoCode, CoName, "
		. "EnClass, EnDivision, EnAgeClass, EnSubClass, EnStatus, EnIndClEvent, EnTeamClEvent, EnIndFEvent, EnTeamFEvent "
		. "FROM Qualifications AS q  "
		. "INNER JOIN Entries AS e ON q.QuId=e.EnId AND e.EnTournament= " . StrSafe_DB($TourId)  . " AND EnAthlete=1 "
		. "INNER JOIN Countries AS c ON e.EnCountry=c.CoId AND e.EnTournament=c.CoTournament"
		. ") as Sq ON at.AtTargetNo=Sq.QuTargetNo "
	. "LEFT JOIN Session on SUBSTRING(AtTargetNo,1,1) = SesOrder and EnTournament=SesTournament and SesType='Q' "
	. "LEFT JOIN Photos on EnId=PhEnId "
	. "WHERE"
		. " AtTournament = " . StrSafe_DB($TourId)
		. " AND EnCode IS NOT NULL "
		. ($TVsettings->TVPSession ? " AND SUBSTRING(AtTargetNo,1,1)= " . StrSafe_DB($TVsettings->TVPSession) . " " : "")
	. "ORDER BY AtTargetNo, CoCode, Name, CoName, FirstName ";

$Rs=safe_r_sql($Select);
//print $Select;exit;
$oldTarget='x';
$tab = array();
$oldSession=0;

$Ath4Target=0;

//$fotow=min(200,intval($_SESSION['WINHEIGHT']/6)*4/3); // redefined later
include_once('Common/CheckPictures.php');
CheckPictures($TourCode);

while($MyRow=safe_fetch($Rs))
{
	if($tab and ($oldTarget!=intval($MyRow->TargetNo) or !isset($ret[$MyRow->Session]))) {
		// cambio di paglione o di turno, scarico le righe
		$tmpRow = '';
		foreach($tab as $row=>$cols) {
			$tmpRow.= '<tr valign="'.($row==0?'middle':'top').'"'.($row<2?' align="center"' :'').'>';
			foreach($cols as $col) {
				if($col) {
					$tmpRow.= '<td align="center">'.$col.'</td>';
				} else {
					$tmpRow.= '<td>&nbsp;</td>';
				}
			}
			$tmpRow.= '</tr>';

		}
		$tmpRow.= '<tr style="height:2px;"><th colspan="' . ($NumCol) . '"></th></tr>';
		$ret[$oldSession]['basso'].=$tmpRow;

		// resetta per il prossimo paglione
		$ses=GetSessions('Q',true,$MyRow->Session.'_Q', $TourId);
		$Ath4Target=$ses[0]->SesAth4Target;
		$tab = array();
		for($n=0; $n<$Ath4Target; $n++) {
			$tab[0][sprintf('%c', $n+65)]=''; // foto
			$tab[1][sprintf('%c', $n+65)]=''; // bib num, divisione e classe
			$tab[2][sprintf('%c', $n+65)]=''; // Nome
			$tab[3][sprintf('%c', $n+65)]=''; // società
		}
	}

	if(!isset($ret[$MyRow->Session])) {
		// recupera il numero di atleti per paglione per quel turno

		$ses=GetSessions('Q',true,$MyRow->Session.'_Q', $TourId);
		$Ath4Target=$ses[0]->SesAth4Target;

		$fotow=min(200,intval($_SESSION['WINWIDTH']/$Ath4Target));

		// crea l'header della gara
		$tmp = '';

		$NumCol = $Ath4Target;

		$tmp.= '<tr><th class="Title" colspan="' . ($NumCol) . '">';
		$tmp.= $Arr_Pages[$TVsettings->TVPPage];
		$tmp.= '</th></tr>' . "\n";

		// Titolo della tabella
		$tmp.= '<tr><th class="Title" colspan="' . ($NumCol) . '">';
		$tmp.=  ($ses[0]->SesName ? $ses[0]->SesName : get_text('Session') . ' ' . $MyRow->Session);
		$tmp.= '</th></tr>' . "\n";

	// Header vero e proprio, incluse le larghezze delle colonne;
		$tmp.= '<tr>';
		$cols='';
		$def=100/$Ath4Target;
		for($n=0; $n < $Ath4Target; $n++) {
			$tmp.= '<th>' . sprintf('%c',$n+65) . '</th>';
			$cols.='<col width="'.($def).'%"></col>';
		}
		$tmp.= '</tr>' . "\n";
		$ret[$MyRow->Session]['head']=$tmp;
		$ret[$MyRow->Session]['cols']=$cols;
		$ret[$MyRow->Session]['fissi']='';
		$ret[$MyRow->Session]['basso']='';
		$ret[$MyRow->Session]['type']='DB';
		$ret[$MyRow->Session]['style']=$ST;
		$ret[$MyRow->Session]['js']=$JS;
		$ret[$MyRow->Session]['js'] .= 'FreshDBContent[%1$s]=\'GetNewContent.php?Quadro=%1$s&Rule='.$RULE->TVRId.'&Tour='.$RULE->TVRTournament.'&Segment='.$TVsettings->TVPId.'&Session='.$MyRow->Session."';\n";

		// resetta per il prossimo paglione
		$tab = array();
		for($n=0; $n<$Ath4Target; $n++) {
			$tab[0][sprintf('%c', $n+65)]=''; // foto
			$tab[1][sprintf('%c', $n+65)]=''; // bib num, divisione e classe
			$tab[2][sprintf('%c', $n+65)]=''; // Nome
			$tab[3][sprintf('%c', $n+65)]=''; // società
		}
	}

	// recupera se si tratta di A, B, eccetera
	$Num=substr($MyRow->TargetNo,-1);
	$tab[0][$Num] = '<img class="athletephoto" src="Photos/'.$TourCode.'-En-'.$MyRow->EnId.'.jpg" width="'.$fotow.'" alternate=""/>';
	$tab[1][$Num] = $MyRow->TargetNo . '&nbsp;&nbsp;' . $MyRow->DivCode . '&nbsp;&nbsp;' . $MyRow->ClassCode;
	$tab[2][$Num] = $MyRow->FirstName . ' ' . ($TVsettings->TVPNameComplete==0 ? FirstLetters($MyRow->Name) : $MyRow->Name);
	$tab[3][$Num] = $MyRow->NationCode . ' ' . ($TVsettings->TVPViewNationName==1 ? ($MyRow->Nation) : '');

	$oldTarget=intval($MyRow->TargetNo);
	$oldSession=$MyRow->Session;
}

if($tab) {
	// cambio di paglione, scarico le righe
	$tmpRow = '';
	foreach($tab as $row=>$cols) {
		$tmpRow.= '<tr valign="top">';
		foreach($cols as $col) {
			if($col) {
				$tmpRow.= '<td align="center">'.$col.'</td>';
			} else {
				$tmpRow.= '<td>&nbsp;</td>';
			}
		}
		$tmpRow.= '</tr>';

	}
	$tmpRow.= '<tr style="height:2px;"><th colspan="' . ($NumCol) . '"></th></tr>';
	$ret[$oldSession]['basso'].=$tmpRow;
}


?>