<?php

if(!empty($on) AND $_SESSION["TourLocRule"]=='FR' AND $acl[AclCompetition] >= AclReadOnly) {
    $ret['COMP']['EXPT'][] = MENU_DIVIDER;
    $ret['COMP']['EXPT'][] = get_text('MenuLM_Export-FR-Results') . '|' . $CFG->ROOT_DIR . 'Modules/Sets/FR/exports/';

	if($_SESSION['TourLocSubRule']=='SetFRChampsD1DNAP') {
		$SubMenu=get_text('SetFRChampsD1DNAP', 'Install');
		$tmp= array(
            $SubMenu,
            get_text('Setup', 'ISK'). '|' . $CFG->ROOT_DIR . 'Modules/Sets/FR/Manage/configure.php',
            get_text('MenuLM_Target Assignment') . '|' . $CFG->ROOT_DIR . 'Modules/Sets/FR/Manage/',
            get_text('ScorecardsInd', 'Tournament') . '|' . $CFG->ROOT_DIR . 'Final/Individual/PrintScore.php',
            get_text('ScorecardsTeams', 'Tournament') . '|' . $CFG->ROOT_DIR . 'Final/Team/PrintScore.php',
			get_text('StartListbyTarget', 'Tournament') . '|' . $CFG->ROOT_DIR . 'Modules/Sets/FR/Manage/StartList.php|||PrintOut',
			get_text('TempRank') . '|' . $CFG->ROOT_DIR . 'Modules/Sets/FR/Manage/CompetitionRanking.php|||PrintOut',
			get_text('MenuLM_Printout') . '|' . $CFG->ROOT_DIR . 'Final/PrintOut.php',
		);

		if(isset($ret['SetFRChampsD1DNAP'])) {
			$ret['SetFRChampsD1DNAP']=array_merge($tmp , $ret['SetFRChampsD1DNAP']);
		} else {
			$ret['SetFRChampsD1DNAP']=$tmp;
		}
	}
}
