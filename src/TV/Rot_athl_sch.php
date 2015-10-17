<?php
require_once('Common/Fun_FormatText.inc.php');

$TVsettings->EventFilter=MakeEventFilter($TVsettings->TVPEventInd);
// get the array of our guys
$Select = "select EnId from Entries "
		. "WHERE EnAthlete=1 AND EnIndClEvent=1 AND EnStatus <= 1 AND EnTournament = " . StrSafe_DB($TourId) . " "
		. ($TVsettings->EventFilter ? " AND CONCAT(EnDivision,EnClass) " . $TVsettings->EventFilter : "") . " "
		. "order by rand() "
		. "limit " . ($TVsettings->TVPNumRows ? $TVsettings->TVPNumRows : "1")
		;
$GUYS=array();

$q=safe_r_sql($Select);
while($r=safe_fetch($q)) $GUYS[]=$r->EnId;

// Check-Create the pictures of the Entries
// $fotow=min(200,intval($_SESSION['WINHEIGHT']/6)*4/3); // resized later :)
include_once('Common/CheckPictures.php');
CheckPictures($TourCode);

$ArrowNo=0;
$SnapDistance=0;
if(isset($_REQUEST["ArrowNo"]) && is_numeric($_REQUEST["ArrowNo"]))
	$ArrowNo = $_REQUEST["ArrowNo"];
else
{
	$MyQuery = "SELECT MAX(EqArrowNo) as ArrowNo "
		. "FROM Entries "
		. "INNER JOIN Qualifications ON EnId=QuId "
		. "INNER JOIN ElabQualifications ON EnId=EqId "
		. "WHERE EnAthlete=1 AND EnIndClEvent=1 AND EnStatus <= 1 AND EnTournament = " . StrSafe_DB($TourId) . " "
		. ($TVsettings->EventFilter ? " AND CONCAT(EnDivision,EnClass) " . $TVsettings->EventFilter : "") . " "
		. "GROUP BY QuSession "
		. "ORDER BY ArrowNo ASC";
	$Rs=safe_r_sql($MyQuery);
	if($Rs)
		$ArrowNo = safe_fetch($Rs)->ArrowNo;
}


if($ArrowNo != 0)
{
	$MyQuery = "SELECT MIN(EqDistance) as Distance "
		. "FROM Entries "
		. "INNER JOIN Qualifications ON EnId=QuId "
		. "INNER JOIN ElabQualifications ON EnId=EqId "
		. "WHERE EnAthlete=1 AND EnTournament=" . StrSafe_DB($TourId) . " AND EqArrowNo=" . StrSafe_DB($ArrowNo) . " "
		. ($TVsettings->EventFilter ? " AND CONCAT(EnDivision,EnClass) " . $TVsettings->EventFilter : "");
	$Rs=safe_r_sql($MyQuery);
	if($Rs)
		$SnapDistance=safe_fetch($Rs)->Distance;
}

$Select
	= "SELECT EnId, EnCode as Bib, EnName AS Name, upper(EnFirstName) AS FirstName, SUBSTRING(QuTargetNo,2) AS TargetNo, CoCode AS NationCode, CoName AS Nation, EnClass AS ClassCode, EnDivision AS DivCode,EnAgeClass as AgeClass,  EnSubClass as SubClass, ClDescription, DivDescription, "
	. "CONCAT(EnDivision,EnClass) AS MyEvent,ToNumDist as NumDist,Td1, Td2, Td3, Td4, Td5, Td6, Td7, Td8, "
	. "QuD1Score, QuD1Rank, QuD2Score, QuD2Rank, QuD3Score, QuD3Rank, QuD4Score, QuD4Rank, "
	. "QuD5Score, QuD5Rank, QuD6Score, QuD6Rank, QuD7Score, QuD7Rank, QuD8Score, QuD8Rank, "
	. "QuScore, QuGold, QuXnine, ToGolds AS TtGolds, ToXNine AS TtXNine, ";

if($SnapDistance==0)
{
	$Select .= " QuScore as OrderScore, QuGold as OrderGold, QuXnine as OrderXNine, ";
	$Select .= "'0' as EqDistance, '0' as EqScore, ";
}
else
{
	for($i=1; $i<$SnapDistance; $i++)
		$Select .= "QuD" . $i . "Score+";
	$Select .="IFNULL(EqScore,0) AS OrderScore, ";
	for($i=1; $i<$SnapDistance; $i++)
		$Select .= "QuD" . $i . "Gold+";
	$Select .="IFNULL(EqGold,0) AS OrderGold, ";
	for($i=1; $i<$SnapDistance; $i++)
		$Select .= "QuD" . $i . "XNine+";
	$Select .="IFNULL(EqXNine,0) AS OrderXNine, ";
	$Select .= "EqDistance, IFNULL(EqScore,0) as EqScore, ";
}


$Select .= "ToType, QuD1Xnine, QuD2Xnine, QuD3Xnine, QuD4Xnine, QuD5Xnine, QuD6Xnine, QuD7Xnine, QuD8Xnine "
	. "FROM Tournament AS t "
	. "INNER JOIN Entries AS e ON t.ToId=e.EnTournament "
	. "INNER JOIN Countries AS c ON e.EnCountry=c.CoId AND e.EnTournament=c.CoTournament "
	. "INNER JOIN Qualifications AS q ON e.EnId=q.QuId "
	. "INNER JOIN Classes AS cl ON e.EnClass=cl.ClId AND ClTournament=" . StrSafe_DB($TourId) . " "
	. "INNER JOIN Divisions AS d ON e.EnDivision=d.DivId AND DivTournament=" . StrSafe_DB($TourId) . " ";
if($SnapDistance!=0)
	$Select .=  "LEFT JOIN ElabQualifications AS eq ON e.EnId=eq.EqId AND eq.EqArrowNo=" . StrSafe_DB($ArrowNo) . " ";

$Select .= "LEFT JOIN TournamentDistances AS td ON t.ToType=td.TdType and TdTournament=ToId AND CONCAT(TRIM(e.EnDivision),TRIM(e.EnClass)) LIKE TdClasses "
	. "WHERE EnAthlete=1 AND EnIndClEvent=1 AND EnStatus <= 1 AND ToId = " . StrSafe_DB($TourId) . " "
	. ($TVsettings->EventFilter ? " AND CONCAT(e.EnDivision,e.EnClass) " . $TVsettings->EventFilter : "") . " "
	. ($TVsettings->TVPSession ? " AND QuSession = " . StrSafe_DB($TVsettings->TVPSession) : "") . " ";

if(getTournamentType() != 14) //Tutto tranne Las Vegas
	$Select.= "ORDER BY DivViewOrder, EnDivision, ClViewOrder, EnClass, OrderScore DESC, OrderGold DESC, OrderXNine DESC, FirstName, Name ";
else
	$Select.= "ORDER BY DivViewOrder, EnDivision, ClViewOrder, EnClass, (QuD1Score+QuD2Score+QuD3Score) DESC, QuD4Score DESC, QuXnine DESC, FirstName, Name ";

$Rs=safe_r_sql($Select);

//print $Select;exit;

//print $Select;exit;
$MyStrData="";

$MyRank = 1;
$MyPos = 0;
$MyScoreOld = 0;
$MyGoldOld = 0;
$MyXNineOld = 0;
$fotow = intval($_SESSION['WINWIDTH']/3);

$count=0;

if (safe_num_rows($Rs)==0) return '';

// Variabili che contengono i punti del precedente atleta per la gestione del rank
$MyRank = 1;
$MyPos = 0;
$MyScoreOld = 0;
$MyGoldOld = 0;
$MyXNineOld = 0;
$oldEventCode = '';

while ($MyRow=safe_fetch($Rs))
{
	if($oldEventCode!=$MyRow->MyEvent) {
		$MyRank = 1;
		$MyPos = 0;
	}

	// Sicuramente devo incrementare la posizione
	++$MyPos;
	// Se non ho parimerito il ranking è uguale alla posizione
	if(getTournamentType($TourId) == 14) //Tipo Las Vegas
	{
		if (!($MyRow->OrderScore==$MyScoreOld))
			$MyRank = $MyPos;
	}
	else	//STANDARD
	{
		if (!($MyRow->OrderScore==$MyScoreOld &&
			$MyRow->OrderGold==$MyGoldOld &&
			$MyRow->OrderXNine==$MyXNineOld))
		$MyRank = $MyPos;
	}

	if(in_array($MyRow->EnId, $GUYS)) {
		// lo include nelle schede da ritornare
		$ret[$MyRow->EnId]['head']='';
		$ret[$MyRow->EnId]['cols']='';
		$ret[$MyRow->EnId]['fissi']='';
		$ret[$MyRow->EnId]['type']='DB';
		$ret[$MyRow->EnId]['style']=$ST;
		$ret[$MyRow->EnId]['js']=$JS;
		$ret[$MyRow->EnId]['js'] .= 'FreshDBContent[%1$s]=\'\';'."\n";

		$tmp ='<table width="100%" height="'.($_SESSION['WINHEIGHT']-35).'">';
		// prima riga, a destra ci va la foto, a sinistra i dati
		$tmp.='<tr>';
		$tmp.='<td rowspan="5"><img class="athletephoto" src="Photos/'.$TourCode.'-En-'.$MyRow->EnId.'.jpg" width="'.$fotow.'" alternate=""/></td>';
		$tmp.='<td width="100%" align="center">' . $MyRow->FirstName . ' ' . ($TVsettings->TVPNameComplete==0 ? FirstLetters($MyRow->Name) : $MyRow->Name).'</td>';
		$tmp.='</tr>';

		$tmp.='<tr><td align="center" class="piccolo"><span class="piccolo">'.get_text('RankingPosition','Tournament').'</span><br/>'.$MyRank.'<span class="piccolo">';
		for ($i=1;$i<=$MyRow->NumDist;++$i)
		{
			$tmp .= '<br/>' . $MyRow->{'Td' . $i} . ': ' ;
			if($SnapDistance==0)
				$tmp .= str_pad($MyRow->{'QuD' . $i . 'Score'},3," ",STR_PAD_LEFT) . '<span class="piccolo">/' . str_pad(($MyRow->ToType!=14 ? $MyRow->{'QuD' . $i . 'Rank'} : $MyRow->{'QuD' . $i . 'Xnine'}),2," ",STR_PAD_LEFT) . '</span>';
			else if($i < $SnapDistance)
				$tmp .= str_pad($MyRow->{'QuD' . $i . 'Score'},3," ",STR_PAD_LEFT);
			else if($i == $SnapDistance)
				$tmp .= str_pad($MyRow->{'EqScore'},3," ",STR_PAD_LEFT);
			else
				$tmp .= str_pad("0",3," ",STR_PAD_LEFT);
		}
		$tmp.='</span></td></tr>';

		$tmp.='<tr><td align="center" class="piccolo"><span class="piccolo">'.get_text('Division').' - '.get_text('Class').'</span><br/>'.$MyRow->DivCode . '&nbsp;&nbsp;' . $MyRow->ClassCode.'</td></tr>';

		$tmp.='<tr><td align="center" class="piccolo"><span class="piccolo">'.get_text('Target').'</span><br/>'.$MyRow->TargetNo.'</td></tr>';

		$tmp.='<tr>';
		$tmp.='<td align="center" class="piccolo"><span class="piccolo">'.get_text('Score','Tournament').'</span><br/>' . $MyRow->OrderScore;
		$tmp.=($MyRow->OrderScore != $MyRow->QuScore ?  '<br/><span class="piccolo">' . $MyRow->QuScore . '</span>' : "") . '</td>';
		$tmp.='</tr>';
		$tmp.='';
		$tmp.='';
		$tmp.='</table>';

//		if($MyRow->ToType!=14) {
//			$tmp.= '<td>' . ($MyRow->ToType!=14 ? $MyRow->NationCode : substr($MyRow->NationCode,0,3)) . ' ' . ($TVsettings->TVPViewNationName==1 ? ($MyRow->Nation) : '') . '</td>';
//		}
	//NewDistanze


		$ret[$MyRow->EnId]['basso']=$tmp;
	}

	$MyScoreOld = $MyRow->OrderScore;
	if($MyRow->ToType!=14)		//Non Considera ORI se "Las Vegas"
		$MyGoldOld = $MyRow->OrderGold;
	$MyXNineOld = $MyRow->OrderXNine;
	$oldEventCode=$MyRow->MyEvent;
}

?>