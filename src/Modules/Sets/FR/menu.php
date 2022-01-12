<?php

if(!empty($on) AND $_SESSION["TourLocRule"]=='FR' AND $acl[AclCompetition] >= AclReadOnly) {
    $ret['COMP']['EXPT'][] = MENU_DIVIDER;
    $ret['COMP']['EXPT'][] = get_text('MenuLM_Export-FR-Results') . '|' . $CFG->ROOT_DIR . 'Modules/Sets/FR/exports/';

	if($_SESSION['TourLocSubRule']=='SetFRChampsD1DNAP') {
		$AllInOne=getModuleParameter('FFTA', 'D1AllInOne', 0);

		$SubMenu=get_text('SetFRChampsD1DNAP', 'Install');
		$tmp= array($SubMenu);
        $tmp[]=get_text('Setup', 'ISK'). '|' . $CFG->ROOT_DIR . 'Modules/Sets/FR/Manage/configure.php';
        $tmp[]=get_text('MenuLM_Target Assignment') . '|' . $CFG->ROOT_DIR . 'Modules/Sets/FR/Manage/';
        if($_SESSION['MenuFinIOn']) {
        	$tmp[]=get_text('ScorecardsInd', 'Tournament') . '|' . $CFG->ROOT_DIR . 'Final/Individual/PrintScore.php';
        }
        $tmp[]=get_text('ScorecardsTeams', 'Tournament') . '|' . $CFG->ROOT_DIR . 'Final/Team/PrintScore.php';
		$tmp[]=get_text('StartListbyTarget', 'Tournament') . '|' . $CFG->ROOT_DIR . 'Modules/Sets/FR/Manage/StartList.php|||PrintOut';
		$tmp[]=get_text('TempRank') . '|' . $CFG->ROOT_DIR . 'Modules/Sets/FR/Manage/CompetitionRanking.php|||PrintOut';
		if($AllInOne) {
			$tmp[]=get_text('MenuLM_Check shoot-off before final phases') . '|' . $CFG->ROOT_DIR . 'Modules/Sets/FR/Manage/AbsTeam.php';
		}
		$tmp[]=get_text('MenuLM_Printout') . '|' . $CFG->ROOT_DIR . 'Final/PrintOut.php';

		if(isset($ret['SetFRChampsD1DNAP'])) {
			$ret['SetFRChampsD1DNAP']=array_merge($tmp , $ret['SetFRChampsD1DNAP']);
		} else {
			$ret['SetFRChampsD1DNAP']=$tmp;
		}
	}
}
