<?php
function getSesSQL($SesType='Q', $Session=0, $SesPhase=0) {
	switch($SesType) {
		case 'Q':
			return "select SesOrder, SesName, SesTar4Session, SesFirstTarget
				from Session
				where SesTournament={$_SESSION['TourId']} and SesType='Q'
				".($Session ? " and SesOrder=$Session " : '')."
				order by SesOrder";
			break;
		case 'E':
			return 	"select ifnull(SesOrder, 0) SesOrder,
				ifnull(SesName,'') SesName,
				ElElimPhase+1 Phase,
				group_concat(distinct conv(ElTargetNo, 10, 10) order by ElTargetNo) as SesTar4Session
				from Eliminations
				left join Session on ElTournament=SesTournament and ElSession=SesOrder and SesType='E'
				where ElTournament={$_SESSION['TourId']}
				".($Session ? " and ElSession=$Session " : '')."
				".($SesPhase ? " and ElElimPhase=".($SesPhase-1)." " : '')."
				group by ElElimPhase, ElSession";
			break;
	}
}

function BuildGroups($SesType='Q', $Session=0, $ElPhase=0, $ArrTargets=array(), $GroupName='') {
	global $CFG;
	static $Groups=array();
	if(empty($Groups)) {
		$q=safe_r_sql("select * from TargetGroups
				where TgTournament={$_SESSION['TourId']}
				and TgSession=$Session
				and TgSesType='".$SesType.($ElPhase ? $ElPhase : '')."'
				".($GroupName ? " and TgGroup='$GroupName' " : '')."
				order by TgTargetNo");
		while($r=safe_fetch($q)) $Groups[$r->TgGroup][]=$r->TgTargetNo;
	}

	$ret='';

	foreach($Groups as $Group=>$Targets) {
		$ret.= '<tr>';
		$ret.= '<th><img id="'.$SesType.($ElPhase ? $ElPhase : '').'-'.$Session.'-'.$Group.'" title="'.get_text('CmdDelete', 'Tournament').'" alt="delete" src="'.$CFG->ROOT_DIR.'Common/Images/Enabled0.png" height="20" alt="del" onclick="DeleteGroup(this)"></th>';
		$ret.= '<th>'.$Group.'</th>';
		foreach($ArrTargets as $Target) {
			$tgtno=sprintf($Session.'%03s', $Target);
			$ret.= '<td><input type="radio" onclick="UpdateGroup(this)" name="tgt['.$SesType.']['.$ElPhase.']['.$tgtno.']" value="'.$Group.'" '.(in_array($tgtno, $Targets) ? ' checked="checked"' : '').'></td>';
		}
		$ret.= '</tr>';
		$ret.= '';
		$ret.= '';
	}

	return $ret;
}

function BuildDeviceGroups($ArrTargets=array(), $GroupName='') {
	global $CFG;
	static $Groups=array();
	if(empty($Groups)) {
		$q=safe_r_sql("select * from TargetGroups
				where TgTournament={$_SESSION['TourId']}
				".($GroupName ? " and TgGroup='$GroupName' " : '')."
				order by TgTargetNo+0");
		while($r=safe_fetch($q)) $Groups[$r->TgGroup][]=$r->TgTargetNo;
	}

	$ret='';

	foreach($Groups as $Group=>$Targets) {
		$ret.= '<tr>';
		$ret.= '<th><img id="g'.$Group.'" title="'.get_text('CmdDelete', 'Tournament').'" alt="delete" src="'.$CFG->ROOT_DIR.'Common/Images/Enabled0.png" height="20" alt="del" onclick="DeleteGroup(this)"></th>';
		$ret.= '<th>'.$Group.'</th>';
		foreach($ArrTargets as $Target) {
			$ret.= '<td><input type="radio" onclick="UpdateDeviceGroup(this)" name="tgt['.$Target.']" value="'.$Group.'" '.(in_array($Target, $Targets) ? ' checked="checked"' : '').'></td>';
		}
		$ret.= '</tr>';
	}

	return $ret;
}