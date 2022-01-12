<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Partecipants/Fun_Targets.php');

$JSON=array('error'=>1);

if(!hasACL(AclCompetition, AclReadWrite) or !CheckTourSession() or empty($_REQUEST['act'])) {
	JsonOut($JSON);
}

$Advanced = (ProgramRelease!='FITARCO' AND ProgramRelease!='STABLE');

$AllowedNullTargets=array(
	6,//TrgField
	8,//Trg3DComplete
	11,//TrgHunterNor
	12,//TrgForestSwe
	18,//TrgNfaa3D
	22,//TrgNfaaAnimal
	);

switch($_REQUEST['act']) {
	case 'list':
		// do nothing, just fills in the data
		break;
	case 'new':
		if(IsBlocked(BIT_BLOCK_TOURDATA) or empty($_REQUEST['TfName']) or (empty($_REQUEST['cl']) and empty($_REQUEST['RegExp'])) or (!$Advanced and !empty($_REQUEST['RegExp']))) {
			JsonOut($JSON);
		}

		$RegExp=(empty($_REQUEST['RegExp']) ? '' : $_REQUEST['RegExp']);
		$cl=((empty($_REQUEST['cl']) or $RegExp) ? '' : $_REQUEST['cl']);
		$TfName=$_REQUEST['TfName'];

		// check if we already have the same name or selector
        // the selector uniqueness applies only if this is a default target!
        $filter='';
        if(!empty($_REQUEST['isDefault'])) {
            if($cl) {
                $filter="or TfClasses=".StrSafe_DB($cl);
            } else {
                $filter="or TfRegExp=".StrSafe_DB($RegExp);
            }
        }
		$q=safe_r_sql("select TfId from TargetFaces where TfTournament={$_SESSION['TourId']} and (TfName=".StrSafe_DB($TfName)." {$filter})");
		if(safe_num_rows($q)) {
			JsonOut($JSON);
		}

		$targets=array();
		foreach($_REQUEST['tdface'] as $dist=>$face) {
			if(!$face or (empty($_REQUEST['tddiam'][$dist]) and !in_array($face, $AllowedNullTargets))) {
				JsonOut($JSON);
			}
			$targets[$face]='';
		}

		// check if the rule hits one or more div/cl
		$select = "SELECT CONCAT(trim(DivId),trim(ClId)) as Ev
			FROM Divisions 
		    INNER JOIN Classes ON DivTournament=ClTournament and (ClDivisionsAllowed='' or find_in_set(DivId, ClDivisionsAllowed))
		    WHERE
		        CONCAT(trim(DivId),trim(ClId)) " . ($RegExp ? "RLIKE " . StrSafe_DB($RegExp) : "LIKE " . StrSafe_DB($cl)) . " 
		        AND DivTournament=" . StrSafe_DB($_SESSION['TourId']) .  " ";
		$rs=safe_r_sql($select);

		if(!safe_num_rows($rs)) {
			JsonOut($JSON);
		}

		$TfId=1;
		$q=safe_r_sql("select max(TfId) MaxId from TargetFaces where TfTournament={$_SESSION['TourId']}");
		if($r=safe_fetch($q)) {
			$TfId = $r->MaxId + 1;
		}

		// get the name of the targets involved
		ksort($targets);
		$q=safe_r_sql("select TarId, TarDescr from Targets where TarId in (".implode(',', array_keys($targets)).")");
		while($r=safe_fetch($q)) {
			$targets[$r->TarId] = get_text($r->TarDescr);
		}


		$insert = "Insert ignore INTO TargetFaces set "
			. "TfTournament={$_SESSION['TourId']}"
			. ", TfId=$TfId"
			. ", TfDefault=" . ($_REQUEST['isDefault']?'1':'0')
			. ", TfClasses=" . StrSafe_DB($RegExp ? '' : $cl)
			. ", TfRegExp=" . StrSafe_DB($RegExp)
			. ", TfName=" . StrSafe_DB($_REQUEST['TfName']);
		foreach($_REQUEST['tdface'] as $dist => $face) {
			$insert.= ", TfT$dist = " . intval($face);
			$insert.= ", TfW$dist = " . intval($_REQUEST['tddiam'][$dist] );
		}

		$rs=safe_w_sql($insert);
		break;
	case 'update':
		if(IsBlocked(BIT_BLOCK_TOURDATA) or empty($_REQUEST['row']) or empty($_REQUEST['dist']) or empty($_REQUEST['target']) or (empty($_REQUEST['diameter']) and !in_array($_REQUEST['target'], $AllowedNullTargets))) {
			JsonOut($JSON);
		}

		$Row=intval($_REQUEST['row']);
		$Dist=intval($_REQUEST['dist']);
		$Tgt=intval($_REQUEST['target']);
		$Diam=intval($_REQUEST['diameter']);
		if($Dist<1 or $Dist>8) {
			JsonOut($JSON);
		}
		// bcheck the target is valid
		$q=safe_r_sql("select TarId from Targets where TarId=$Tgt");
		if(!safe_num_rows($q)) {
			JsonOut($JSON);
		}

		$SQL = "update TargetFaces set 
			TfT{$Dist}=$Tgt, TfW{$Dist}=$Diam 
			where TfTournament={$_SESSION['TourId']} AND TfId=$Row";
		$rs=safe_w_sql($SQL);
		break;
	case 'delete':
		if(empty($_REQUEST['row'])) {
			JsonOut($JSON);
		}

		safe_w_sql("delete from TargetFaces where TfTournament={$_SESSION['TourId']} and TfId=".intval($_REQUEST['row']));
		break;
	default:
		$JSON['error']=1;
}

$JSON['error']=0;
$JSON['categories']='';
$JSON['table']='';


$numDist=0;
$colspan=0;
$rsDist='';


$AvDiv=array();
$q=safe_r_sql("select DivId, ClId from Divisions inner join Classes on ClTournament=DivTournament and ClAthlete=DivAthlete where DivTournament='{$_SESSION['TourId']}' and DivAthlete=1 AND (ClDivisionsAllowed='' or find_in_set(DivId, ClDivisionsAllowed)) order by DivViewOrder, ClViewOrder");
while($r=safe_fetch($q)) {
    $AvDiv[$r->DivId][$r->ClId]='<a name="" onclick="document.getElementById(\'TdClasses\').value=\''.$r->DivId.$r->ClId.'\'">'.$r->DivId.$r->ClId.'</a>';
}

$AvTargets=array();
$SelTargets='<option value="">---</option>';
$q=safe_r_sql("select * from Targets order by TarOrder");
while($r=safe_fetch($q)) {
    $AvTargets[$r->TarId]= get_text($r->TarDescr);
    $SelTargets.='<option value="'.$r->TarId.'">'.get_text($r->TarDescr).'</option>';
}

$select = "SELECT ToType,ToNumDist AS TtNumDist
    FROM Tournament
    WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";
$rs=safe_r_sql($select);

if ($r=safe_fetch($rs) and $r->TtNumDist) {
    $numDist=$r->TtNumDist;
    $colspan=2+$numDist+$Advanced;

    $select = "SELECT DISTINCT *
        FROM TargetFaces
        WHERE TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
    $rsDist=safe_r_sql($select);
}

foreach(($DefinedTargets=getTargets(false)) as $Target=>$divs) {
    foreach($divs as $Div=>$cl) {
        foreach($cl as $Class=>$default) {
            if($default) unset ($AvDiv[$Div][$Class]);
        }
    }
}

foreach($AvDiv as $Div=>$Cl) {
    if($Cl) {
	    $JSON['categories'].='<div>'.implode(', ',$Cl).'</div>';
    }
}

if ($rsDist) {
    $k=0;
    while ($myRow=safe_fetch($rsDist)) {
		$JSON['table'].= '<tr ref="'.$myRow->TfId.'">';
        $JSON['table'].= '<td>'.print_targets($myRow->TfId).'</td>';
        $JSON['table'].= '<td class="Center">'.get_text($myRow->TfName,'Tournament','',true).'</td>';
        $JSON['table'].= '<td class="Center" style="width:20%;">'.$myRow->TfClasses.'</td>';
        $JSON['table'].= '<td class="Center" style="width:20%;">'.$myRow->TfRegExp.'</td>';
        for ($i=1;$i<=$numDist;++$i) {
            $JSON['table'].= '<td class="Center" ref="'.$i.'">
				<select onchange="updateTarget(this)" name="target">'.preg_replace('/(value="'.($myRow->{'TfT' . $i} ? $myRow->{'TfT' . $i} : '').'")/','$1 selected="selected"',$SelTargets).'</select>
				<br/>ø (cm) <input name="diameter" value="'.($myRow->{'TfW' . $i}) . '" onchange="updateTarget(this)" size="3" maxlength="3">';
        }
        $JSON['table'].= '<td class="Center">'.($myRow->TfDefault?get_text('Yes'):'').'</td>';
        $JSON['table'].= '<td class="Center"><i class="fa fa-2x fa-trash-o text-danger" onclick="deleteTarget(this)"></i></td>';
        $JSON['table'].= '</tr>';
        ++$k;
    }
}

JsonOut($JSON);

function print_targets($TfId) {
	global $DefinedTargets;
	$ret='';
	if(empty($DefinedTargets[$TfId])) return '&nbsp;';
	foreach($DefinedTargets[$TfId] as $Div=>$Cl) {
		$ret.= $Div . ':&nbsp;';
		foreach($Cl as $class=>$def) {
			if($def) $class="<b>$class</b>";
			$ret.= $class . '&nbsp;';
		}
		$ret.='<br/>';
	}
	return $ret;
}

