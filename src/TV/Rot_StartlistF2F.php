<?php
// parametri
$Arr_Ev = array();
$Arr_Ph = array();
if($TVsettings->TVPEventInd) $Arr_Ev = explode('|', $TVsettings->TVPEventInd);
if(strlen($TVsettings->TVPPhasesInd)) $Arr_Ph = explode('|', $TVsettings->TVPPhasesInd);

$f=array();

if($Arr_Ev and count($Arr_Ph)) {
	foreach($Arr_Ph as $p) {
		foreach($Arr_Ev as $e) $f[] = '(F2FEntries.F2FEventCode=' . StrSafe_DB($e) . ' AND F2FEntries.F2FPhase=' . $p . ')';
	}
} elseif(count($Arr_Ph)) {
	foreach($Arr_Ph as $p) $f[] = '(F2FEntries.F2FPhase=' . $p . ')';
} elseif($Arr_Ev) {
	$f[] = '(F2FEntries.F2FEventCode=' . StrSafe_DB($e) . ')';
}

$where=($f ? "WHERE " . '(' . implode(' OR ', $f) . ')' : '');

$query="
	SELECT
		e.*,
		f.F2FTarget AS TargetNo,
		f.F2FGroup AS Session,
		2 AS SesAth4Target,
		CONCAT('Poule ', f.F2FGroup) AS SesName,
		EvCode,
		EvProgr,
		DivDescription,
		ClDescription,
		IFNULL(EvCode,CONCAT(TRIM(EnDivision),TRIM(EnClass))) as EventCode,
		concat(upper(EnFirstName), ' ', EnName) Athlete,
		EnCode as Bib,

		upper(c.CoCode) AS NationCode,
		upper(c.CoName) AS Nation,
		upper(c2.CoCode) NationCode2,
		upper(c2.CoName) Nation2,
		upper(c3.CoCode) NationCode3,
		upper(c3.CoName) Nation3,
		IFNULL(EvEventName,CONCAT('|',DivDescription, '| |', ClDescription)) as EventName,
		EnSubTeam,
		EnClass AS ClassCode,
		EnDivision AS DivCode,
		EnAgeClass as AgeClass,
		EnSubClass as SubClass,
		EnStatus as Status,
		EnIndClEvent AS `IC`,
		EnTeamClEvent AS `TC`,
		EnIndFEvent AS `IF`,
		EnTeamFEvent as `TF`,
		EnTeamMixEvent as `TM`,
		DATE_FORMAT(EnDob,'%d %b %Y') as DOB,
		TfName,
		F2FEntries.F2FPhase,
		F2FEntries.F2FMatchNo,
		F2FEntries.F2FGroup,
		F2FEntries.F2FEventCode
	FROM
		F2FEntries
		INNER JOIN
			F2FFinal AS f
		ON F2FEntries.F2FTournament=f.F2FTournament AND F2FEntries.F2FPhase=f.F2FPhase AND F2FEntries.F2FGroup=f.F2FGroup AND F2FEntries.F2FMatchNo=f.F2FMatchNo AND F2FEntries.F2FEnId=f.F2FEnId AND F2FEntries.F2FEventCode=f.F2FEvent AND f.F2FRound=1
		INNER JOIN Entries AS e ON F2FEntries.F2FEnId=EnId AND e.EnTournament= " . StrSafe_DB($RULE->TVRTournament) . " AND EnAthlete=1
		INNER JOIN Countries AS c ON e.EnCountry=c.CoId AND e.EnTournament=c.CoTournament
		left join Individuals on IndId=EnId and IndTournament=EnTournament
		left join Events on EvCode=IndEvent and EvTournament=IndTournament and EvTeamEvent=0
		LEFT JOIN Countries AS c2 ON e.EnCountry2=c2.CoId AND e.EnTournament=c2.CoTournament
		LEFT JOIN Countries AS c3 ON e.EnCountry3=c3.CoId AND e.EnTournament=c3.CoTournament
		LEFT JOIN Divisions ON TRIM(EnDivision)=TRIM(DivId) AND EnTournament=DivTournament
		LEFT JOIN Classes ON TRIM(EnClass)=TRIM(ClId) AND EnTournament=ClTournament
		LEFT JOIN TargetFaces ON EnTournament=TfTournament AND EnTargetFace=TfId
	{$where}
	order by F2FEventCode, F2FPhase, F2FGroup, TargetNo
";

$rs=safe_r_sql($query);

$html='';
$curGroup='';
$CurPage='';

while ($row=safe_fetch($rs)) {
	if($CurPage != "$row->F2FEventCode - $row->F2FPhase") {
		$CurPage = "$row->F2FEventCode - $row->F2FPhase";

		// title
		$col=array();
		$tmp = '<tr><th class="Title" colspan="5">' . $row->EventName .' - ' . get_text('Phase'). ' ' . $row->F2FPhase.'</th></tr>';
		$tmp.= '<tr>';
		$tmp.= '<th>'.get_text('Target').'</th>';
		$col[]=2;
		$tmp.= '<th>'.get_text('Athlete').'</th>';
		$col[]=9;
		$tmp.= '<th>'.get_text('Country').'</th>';
		$col[]=9;
		$tmp.= '<th>'.get_text('Division').'</th>';
		$col[]=3;
		$tmp.= '<th>'.get_text('Class').'</th>';
		$col[]=3;
		$tmp.= '</tr>';

		$SumCol=array_sum($col);
		$cols='';
		foreach($col as $w) $cols.='<col width="'.round(100*$w/$SumCol, 0).'%"></col>';

		$ret[$CurPage]['head']=$tmp;
		$ret[$CurPage]['cols']=$cols;
		$ret[$CurPage]['fissi']='';
		$ret[$CurPage]['basso']='';
		$ret[$CurPage]['type']='DB';
		$ret[$CurPage]['style']=$ST;
		$ret[$CurPage]['js']=$JS;
		$ret[$CurPage]['js'] .= 'FreshDBContent[%1$s]=\'GetNewContent.php?Quadro=%1$s&Rule='.$RULE->TVRId.'&Tour='.$RULE->TVRTournament.'&Segment='.$TVsettings->TVPId.'&Event='.$row->F2FEventCode.'&Phase='.$row->F2FPhase."';\n";
	}

	$tmp='';
	if ($curGroup!=$row->Session) {
//		if ($curGroup) $tmp .= '<tr style="height:2px;"><th colspan="7"></th></tr>';
		$tmp.='<tr><th colspan="5">Group ' . $row->Session.'</th></tr>';
		$curGroup=$row->Session;
	}

	$tmp.='
				<tr>
					<td>'.$row->TargetNo.'</td>
					<td>'.$row->Athlete.'</td>
					<td>'.$row->NationCode.' '.$row->Nation.'</td>
					<td>'.$row->DivDescription.'</td>
					<td>'.$row->ClDescription.'</td>
				</tr>
			';
	$ret[$CurPage]['basso'].=$tmp;

}

