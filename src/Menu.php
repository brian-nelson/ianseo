<?php
require_once('Common/Lib/Fun_Modules.php');
	define ('ClubTeam',false);
	define ('CasTeam',false);
	define('MENU_DIVIDER', '---');

	$Path_DIR = $CFG->DOCUMENT_PATH . 'Common/phplayersmenu/';
	$Path_WWW = $CFG->ROOT_DIR . 'Common/phplayersmenu/';

// definisce globali per JS la variabile di configurazione WebDir per reindirizzare correttamente i files...
// la variabile ha la forma /path/to/ianseo/root/dir/

echo "<script>\nvar WebDir = '$CFG->ROOT_DIR';\n</script>\n";

// <link rel="stylesheet" href="<?php //print $Path_WWW; ? >layersmenu-gtk2.css" type="text/css">

//<link rel="stylesheet" href="<?php print $Path_WWW; ? >layersmenu-keramik.css" type="text/css">
?>
<link rel="icon" href="<?php print $CFG->ROOT_DIR; ?>favicon.ico" sizes="16x16 32x32 48x48 64x64" type="image/vnd.microsoft.icon"/>

<?php // javascript per ingrandire logo on over ?>
<script type="text/javascript" src="<?php echo $CFG->ROOT_DIR ?>Common/js/Fun_ResizeImg.inc.js"></script>

<?php // javascript per popup eccetera ?>
<script type="text/javascript" src="<?php echo $CFG->ROOT_DIR ?>Common/js/Fun_JS.inc.js"></script>

<script language="JavaScript" type="text/javascript">
// <!--
<?php // require_once $Path_DIR . 'libjs/layersmenu-browser_detection.js'; ?>
// -->
</script>
<?php
// <script type="text/javascript" src="<?php print $Path_WWW;? >libjs/layersmenu-library.js"></script>
// <script type="text/javascript" src="<?php print $Path_WWW;? >libjs/layersmenu.js"></script>
	require_once $Path_DIR . 'lib/PHPLIB.php';
//	require_once $Path_DIR . 'lib/layersmenu-common.inc.php';
//	require_once $Path_DIR . 'lib/layersmenu.inc.php';

// 	$mid = new LayersMenu(6, 7, 2, 1);	// Gtk2-like

// 	$mid = new LayersMenu(3, 8, 1, 1);

// 	$mid->setDirroot($Path_DIR);
// 	$mid->setLibjsdir($Path_DIR . 'libjs/');
// 	$mid->setImgdir($Path_DIR . 'menuimages/');
// 	$mid->setImgwww($Path_WWW . 'menuimages/');
// 	$mid->setIcondir($Path_DIR . 'menuicons/');
// 	$mid->setIconwww($Path_WWW . 'menuicons/');
// 	$mid->setTpldir($Path_DIR . 'templates/');
// 	//$mid->setHorizontalMenuTpl('layersmenu-horizontal_menu-full.ihtml');
// 	//$mid->setSubMenuTpl('layersmenu-sub_menu.ihtml');

// 	$mid->setHorizontalMenuTpl('layersmenu-horizontal_menu-keramik-full.ihtml');
// 	$mid->setSubMenuTpl('layersmenu-sub_menu-keramik.ihtml');

// 	$WhichMenu = build_menu(get_which_menu(CheckTourSession()));

// 	$mid->setMenuStructureString($WhichMenu);
// 	$mid->setIconsize(16, 16);
// 	$mid->parseStructureForMenu('hormenu1');
// 	$mid->newHorizontalMenu('hormenu1');

// 	$mid->printHeader();

//if(!$_SESSION['WINHEIGHT']) {
	echo '<script>';
	echo 'var video="648";';
	echo 'var videowidth="1000";';
	echo 'video=document.documentElement.clientHeight;';
	echo 'videowidth=document.documentElement.clientWidth;';
	echo 'document.write(\'<LINK href="http://'.$_SERVER['SERVER_NAME'].$CFG->ROOT_DIR.'Common/Styles/Load_css.php?video=\'+video+\'&videowidth=\'+videowidth+\'" rel="stylesheet" type="text/css">\');';
	//if($ERROR_REPORT) echo 'alert(video);';
	echo '</script>';
//}

	if(!empty($_SESSION['WINHEIGHT'])) echo "<style>#Content {height:".max(180,$_SESSION['WINHEIGHT']-120)."px}</style>";


function get_which_menu($on=false) {
	global $CFG;
	$ret=array();

	// subsites have very specific menu!
	if(!empty($_SESSION['ShortMenu'])) {
		return $_SESSION['ShortMenu'];
	}

	// Start with minimal structure


	if($on) {
		/** COMPETITION MENU **/
		$ret['COMP'][] = get_text('MenuLM_Competition') .'';
		$ret['COMP'][] = get_text('MenuLM_Open') .'|'.$CFG->ROOT_DIR.'index.php';
		$ret['COMP'][] = get_text('MenuLM_Close') .'|'.$CFG->ROOT_DIR.'Common/TourOff.php';
		$ret['COMP'][] = MENU_DIVIDER;
		$ret['COMP'][] = get_text('MenuLM_Delete') .'|'.$CFG->ROOT_DIR.'Tournament/TourDelete.php';
		$ret['COMP'][] = MENU_DIVIDER;
		$ret['COMP'][] = get_text('MenuLM_View Competition Info') .'|'.$CFG->ROOT_DIR.'Main.php';
		$ret['COMP']['INFO'][] = get_text('MenuLM_Edit Competition Info') ;
		$ret['COMP']['INFO'][] = get_text('MenuLM_Competition Info') .'|'.$CFG->ROOT_DIR.'Tournament/index.php';
		$ret['COMP']['INFO'][] = get_text('MenuLM_Images') .'|'.$CFG->ROOT_DIR.'Tournament/ManLogo.php';
		$ret['COMP']['INFO'][] = get_text('MenuLM_Session') .'|'.$CFG->ROOT_DIR.'Tournament/ManSessions_kiss.php';
		$ret['COMP']['INFO'][] = get_text('MenuLM_Field Crew') .'|'.$CFG->ROOT_DIR.'Tournament/ManStaffField.php';
		$ret['COMP']['INFO'][] = get_text('MenuLM_Divisions and Classes') .'|'.$CFG->ROOT_DIR.'Tournament/ManDivClass.php';
		$ret['COMP']['INFO'][] = get_text('MenuLM_SubClasses') .'|'.$CFG->ROOT_DIR.'Tournament/ManSubClass.php';
		$ret['COMP']['INFO'][] = get_text('MenuLM_Distances') .'|'.$CFG->ROOT_DIR.'Tournament/ManDistances.php';
		$ret['COMP']['INFO'][] = get_text('MenuLM_Targets') .'|'.$CFG->ROOT_DIR.'Tournament/ManTargets.php';
		if (!defined('hideSchedulerAndAdvancedSession')) {
			$ret['COMP'][] = MENU_DIVIDER;
			$ret['COMP'][] = get_text('MenuLM_Scheduling') .'|'.$CFG->ROOT_DIR.'Scheduler/index.php';
			$ret['COMP'][] = get_text('MenuLM_Training') .'|'.$CFG->ROOT_DIR.'Final/ManTraining.php';
		}
		$ret['COMP'][] = MENU_DIVIDER;
		$ret['COMP']['FINI'][] = get_text('MenuLM_Individual Final Setup') .'';
		$ret['COMP']['FINI'][] = get_text('MenuLM_Manage Events') .'|'.$CFG->ROOT_DIR.'Final/Individual/ListEvents.php';
		$ret['COMP']['FINI'][] = get_text('MenuLM_Archers on Targets') .'|'.$CFG->ROOT_DIR.'Final/Individual/SetAthForTarget.php';
		$ret['COMP']['FINI'][] = get_text('MenuLM_Arr4Set') .'|'.$CFG->ROOT_DIR.'Final/SetArrForMatch.php';
		$ret['COMP']['FINI'][] = get_text("MenuLM_Target's Number") .'|'.$CFG->ROOT_DIR.'Final/Individual/ManTarget.php';
		$ret['COMP']['FINI'][] = get_text('MenuLM_Scheduling') .'|'.$CFG->ROOT_DIR.'Final/Individual/ManSchedule.php';
		$ret['COMP']['FINI'][] = get_text('MenuLM_Training') .'|'.$CFG->ROOT_DIR.'Final/ManTraining.php';
		if (!defined('hideRunning'))
			$ret['COMP']['FINI'][] = get_text('MenuLM_RunningEvents') .'|'.$CFG->ROOT_DIR.'Final/RunningEvent.php';
		$ret['COMP']['FINT'][] = get_text('MenuLM_Team Final Setup') .'';
		$ret['COMP']['FINT'][] = get_text('MenuLM_Manage Events') .'|'.$CFG->ROOT_DIR.'Final/Team/ListEvents.php';
		$ret['COMP']['FINT'][] = get_text('MenuLM_Arr4Set') .'|'.$CFG->ROOT_DIR.'Final/SetArrForMatch.php?Teams=1';
		$ret['COMP']['FINT'][] = get_text("MenuLM_Target's Number") .'|'.$CFG->ROOT_DIR.'Final/Team/ManTarget.php';
		$ret['COMP']['FINT'][] = get_text('MenuLM_Scheduling') .'|'.$CFG->ROOT_DIR.'Final/Team/ManSchedule.php';
		$ret['COMP']['FINT'][] = get_text('MenuLM_Training') .'|'.$CFG->ROOT_DIR.'Final/ManTraining.php';
		if (!defined('hideRunning'))
			$ret['COMP']['FINT'][] = get_text('MenuLM_RunningEvents') .'|'.$CFG->ROOT_DIR.'Final/RunningEvent.php';
		$ret['COMP'][] = get_text('MenuLM_Final Field of Play Layout') .'|'.$CFG->ROOT_DIR.'Final/FopSetup.php|||PrintOut';
		$ret['COMP'][] = MENU_DIVIDER;
		$ret['COMP']['BLOCK'][] = get_text('MenuLM_Lock setup') .'|'.$CFG->ROOT_DIR.'Tournament/BlockTour.php';
		$ret['COMP']['REPT'][] = get_text('MenuLM_Final report') .'';
		$ret['COMP']['REPT'][] = get_text('MenuLM_EditFinalReport') .'|'.$CFG->ROOT_DIR.'Tournament/FinalReport/index.php';
		$ret['COMP']['REPT'][] = get_text('MenuLM_PrintFinalReport') .'|'.$CFG->ROOT_DIR.'Tournament/FinalReport/PDFReport.php|||PrintOut';
		$ret['COMP']['REPT'][] = get_text('MenuLM_CheckList') .'|'.$CFG->ROOT_DIR.'Tournament/FinalReport/PDFCheckList.php|||PrintOut';
		$ret['COMP'][] = MENU_DIVIDER;

		$ret['COMP']['EXPT'][] = get_text('MenuLM_Export') .'|'.$CFG->ROOT_DIR.'Tournament/TournamentExport.php?Complete=1';
		$ret['COMP']['EXPT'][] = get_text('MenuLM_Export Results') .'|'.$CFG->ROOT_DIR.'Tournament/Export2Fitarco.php';
		$ret['COMP']['EXPT'][] = get_text('MenuLM_Export Tournament') .'|'.$CFG->ROOT_DIR.'Tournament/TournamentExport.php';
		$ret['COMP']['EXPT'][] = get_text('MenuLM_Export Tournament Photo') .'|'.$CFG->ROOT_DIR.'Tournament/TournamentExport.php?Complete=1';
		if(ProgramRelease=='HEAD') $ret['COMP']['EXPT'][] = get_text('MenuLM_Export Entries') .'|'.$CFG->ROOT_DIR.'Partecipants/EntriesExchange.php';
		$ret['COMP']['EXPT'][] = get_text('MenuLM_Export BackNumbers') .'|'.$CFG->ROOT_DIR.'Tournament/BackNumbersExport.php';
		$ret['COMP'][] = MENU_DIVIDER;
		$ret['COMP']['SEND'][] = get_text('MenuLM_Send to ianseo.net') .'|'.$CFG->ROOT_DIR.'Tournament/UploadResults.php';
		$ret['COMP']['SEND'][] = get_text('MenuLM_Set on-line codes') .'|'.$CFG->ROOT_DIR.'Tournament/SetCredentials.php';
		// aggiungere un controllo se c'è il codice di accesso?
		$ret['COMP']['SEND'][] = get_text('MenuLM_Send') .'|'.$CFG->ROOT_DIR.'Tournament/UploadResults.php';

		$ret['COMP'][] = MENU_DIVIDER;
		$ret['COMP'][] = get_text('MenuLM_Select Language') .'|'.$CFG->ROOT_DIR.'Language/index.php';
		if(substr(SelectLanguage(),0,2)!='en') $ret['COMP'][] = 'Select Language|'.$CFG->ROOT_DIR.'Language/index.php';


		/** PARTICIPANTS MENU **/
		$ret['PART'][] = get_text('MenuLM_Participants');
		$ret['PART'][] = get_text('MenuLM_List (Simple)') .'|'.$CFG->ROOT_DIR.'Partecipants/index.php';
		$ret['PART'][] = get_text('MenuLM_Partecipant List (Advanced)') .'|'.$CFG->ROOT_DIR.'Partecipants-exp/index.php';
		$ret['PART'][] = get_text('MenuLM_Athlete Status Management') .'|'.$CFG->ROOT_DIR.'Partecipants/ManStatus.php';
		$ret['PART'][] = get_text('MenuLM_Athletes Participation to Ind/Team Event') .'|'.$CFG->ROOT_DIR.'Partecipants/ManEventAccess.php';
		$ret['PART']['TARG'][] = get_text('MenuLM_Target') .'';
		$ret['PART']['TARG'][] = get_text('MenuLM_Manual Assignment') .'|'.$CFG->ROOT_DIR.'Partecipants/SetTarget_default.php?Ses=*';
		$ret['PART']['TARG'][] = get_text('MenuLM_Draw') .'|'.$CFG->ROOT_DIR.'Partecipants/SetTarget_auto.php';
		$ret['PART']['TARG'][] = MENU_DIVIDER;
		$ret['PART']['TARG'][] = get_text('MenuLM_MoveSession') . '|' . $CFG->ROOT_DIR.'Partecipants/MoveSession.php';
		$ret['PART']['TARG'][] = get_text('MenuLM_MoveTarget') . '|' . $CFG->ROOT_DIR.'Partecipants/MoveTarget.php';
		$ret['PART']['TARG'][] = get_text('MenuLM_TargetFromRank') . '|' . $CFG->ROOT_DIR.'Partecipants/TargetFromRank.php';
		$ret['PART']['TARG'][] = get_text('MenuLM_DeleteTarget') . '|' . $CFG->ROOT_DIR.'Partecipants/DeleteTarget.php';
		$ret['PART'][] = MENU_DIVIDER;
		$ret['PART']['ACCR'][] = get_text('MenuLM_Accreditation') .'';
		$ret['PART']['ACCR'][] = get_text('MenuLM_Accreditation') .'|'.$CFG->ROOT_DIR.'Accreditation/index.php';
		$ret['PART']['ACCR'][] = get_text('TakePicture', 'Tournament') .'|'.$CFG->ROOT_DIR.'Accreditation/AccreditationPicture.php';
		$ret['PART']['ACCR'][] = MENU_DIVIDER;
		$ret['PART']['ACCR'][] = get_text('MenuLM_Fees setup') .'|'.$CFG->ROOT_DIR.'Accreditation/ManagePrices.php';
		$ret['PART']['ACCR'][] = get_text('MenuLM_Athlets fees status') .'|'.$CFG->ROOT_DIR.'Accreditation/ManagePays.php';
		$ret['PART']['ACCR'][] = get_text('MenuLM_Accreditation colors management') .'|'.$CFG->ROOT_DIR.'Accreditation/Colors.php';
		$ret['PART']['ACCR'][] = MENU_DIVIDER;
// 		$ret['PART']['ACCR'][] = get_text('MenuLM_PrintBadges') .'|'.$CFG->ROOT_DIR.'Accreditation/IdCards.php';
		$ret['PART']['ACCR'][] = get_text('MenuLM_PrintBadges') .'|'.$CFG->ROOT_DIR.'Accreditation/IdCards-new.php';
		$ret['PART']['ACCR'][] = get_text('MenuLM_Printout') .'|'.$CFG->ROOT_DIR.'Accreditation/PrintOut.php';
		$ret['PART'][] = MENU_DIVIDER;
		$ret['PART']['SYNC'][] = get_text('MenuLM_Athletes Sync.') .'|'.$CFG->ROOT_DIR.'Partecipants/LookupTableLoad.php';
		$ret['PART']['SYNC'][] = get_text('MenuLM_Athletes Sync.') .'|'.$CFG->ROOT_DIR.'Partecipants/LookupTableLoad.php';
		$ret['PART']['SYNC'][] = get_text('MenuLM_ListLoad') .'|'.$CFG->ROOT_DIR.'Partecipants/ListLoad.php';
		$ret['PART']['SYNC'][] = get_text('MenuLM_AthletesDiscrepancies') .'|'.$CFG->ROOT_DIR.'Partecipants/Discrepancies.php';
		$ret['PART'][] = MENU_DIVIDER;
		$ret['PART'][] = get_text('MenuLM_Statistics') .'|'.$CFG->ROOT_DIR.'Partecipants/PrintOutStat.php';
		$ret['PART'][] = MENU_DIVIDER;
		$ret['PART'][] = get_text('MenuLM_Printout') .'|'.$CFG->ROOT_DIR.'Partecipants/PrintOut.php';

		/** QUALIFICATIONS MENU **/
		$ret['QUAL'][] = get_text('MenuLM_Qualification') .'';
		$ret['QUAL']['SCOR'][] = get_text('MenuLM_Input Score') ;
		$ret['QUAL']['SCOR'][] = get_text('MenuLM_Standard Table') .'|'.$CFG->ROOT_DIR.'Qualification/index.php';
		$ret['QUAL']['SCOR'][] = get_text('MenuLM_Extended Table') .'|'.$CFG->ROOT_DIR.'Qualification/index_all.php';
		$ret['QUAL']['SCOR'][] = get_text('MenuLM_Arrow by Arrow (Advanced user)') .'|'.$CFG->ROOT_DIR.'Qualification/WriteArrows.php';
		$ret['QUAL']['SCOR'][] = get_text('MenuLM_Arrow by Arrow (Scorecards)') .'|'.$CFG->ROOT_DIR.'Qualification/WriteScoreCard.php';
		$ret['QUAL']['SCOR'][] = MENU_DIVIDER;
		$ret['QUAL']['SCOR'][] = get_text('MenuLM_Check Data Update') .'|'.$CFG->ROOT_DIR.'Qualification/CheckTargetUpdate.php';
		$ret['QUAL'][] = MENU_DIVIDER;
		$ret['QUAL'][] = get_text('MenuLM_Export Text File') .'|'.$CFG->ROOT_DIR.'Qualification/ExportTSV.php';
		if(!empty($_SESSION['MenuElimDo'])) {
			$ret['QUAL'][] = get_text('MenuLM_ExportFieldStatistics') .'|'.$CFG->ROOT_DIR.'Qualification/ExportCSV.php';
		}
		$ret['QUAL'][] = MENU_DIVIDER;
		$ret['QUAL'][] = get_text('MenuLM_Scorecard Printout') .'|'.$CFG->ROOT_DIR.'Qualification/PrintScore.php';
		$ret['QUAL'][] = get_text('MenuLM_NewBacknumbers') .'|'.$CFG->ROOT_DIR.'Accreditation/IdCards-new.php?CardType=Q';
// 		$ret['QUAL'][] = get_text('MenuLM_Back Number Printout') .'|'.$CFG->ROOT_DIR.'Qualification/PrintBackNo.php';
		$ret['QUAL'][] = get_text('MenuLM_Qualification Field of Play Layout') .'|'.$CFG->ROOT_DIR.'Qualification/FopSetup.php|||PrintOut';
		$ret['QUAL'][] = MENU_DIVIDER;
		$ret['QUAL'][] = get_text('MenuLM_Personal Rank') .'|'.$CFG->ROOT_DIR.'Qualification/RankPersonal1.php';
		$ret['QUAL'][] = MENU_DIVIDER;
		$ret['QUAL'][] = get_text('MenuLM_Category Result List') .'|'.$CFG->ROOT_DIR.'Qualification/PrintOut.php';
		$ret['QUAL'][] = get_text('MenuLM_Qualification Round') .'|'.$CFG->ROOT_DIR.'Qualification/PrintOutAbs.php';


		/** ELIMINATION MENU **/
		if($_SESSION['MenuElimDo']) {
			$ret['ELIM'][] = get_text('MenuLM_Eliminations') .'';
			$tmp = get_text('MenuLM_Check shoot-off before eliminations') .'';
			if($_SESSION['MenuElim1']) {
				$tmp .= '<br/><b style="color:red">(Round 1: '.implode('-', $_SESSION['MenuElim1']).')</b>';
			}
			if($_SESSION['MenuElim2']) {
				$tmp .= '<br/><b style="color:red">(Round 2: '.implode('-', $_SESSION['MenuElim2']).')</b>';
			}
			$tmp.='|'.$CFG->ROOT_DIR.'Elimination/AbsIndividual1.php';
			$ret['ELIM'][]=$tmp;

			$ret['ELIM'][] = MENU_DIVIDER;
			$ret['ELIM'][] = get_text('MenuLM_Scorecard Printout') .'|'.$CFG->ROOT_DIR.'Elimination/PrintScore.php';
// 			$ret['ELIM'][] = get_text('MenuLM_Back Number Printout') .'|'.$CFG->ROOT_DIR.'Elimination/PrintBackNo.php';
			$ret['ELIM'][] = get_text('MenuLM_NewBacknumbers') .'|'.$CFG->ROOT_DIR.'Accreditation/IdCards-new.php?CardType=E';
			$ret['ELIM'][] = MENU_DIVIDER;
			$ret['ELIM'][] = get_text('MenuLM_Target Assignment') .'|'.$CFG->ROOT_DIR.'Elimination/SetTarget.php';
			if($_SESSION['MenuElimOn']) {

				$ret['ELIM']['SCOR'][] = get_text('MenuLM_Input Score') ;
				$ret['ELIM']['SCOR'][] = get_text('MenuLM_Standard Table') .'|'.$CFG->ROOT_DIR.'Elimination/index.php';
				$ret['ELIM']['SCOR'][] = get_text('MenuLM_Arrow by Arrow (Advanced user)') .'|'.$CFG->ROOT_DIR.'Elimination/WriteArrows.php';
				$ret['ELIM']['SCOR'][] = get_text('MenuLM_Arrow by Arrow (Scorecards)') .'|'.$CFG->ROOT_DIR.'Elimination/WriteScoreCard.php';
				$ret['ELIM'][] = MENU_DIVIDER;
				$ret['ELIM'][] = get_text('MenuLM_Printout') .'|'.$CFG->ROOT_DIR.'Elimination/PrintOut.php';
			}
		}


		/** INDIVIDUAL FINAL MENU **/
		if($_SESSION['MenuFinIDo']) {
			$ret['FINI'][] = get_text('MenuLM_Individual Finals') .'';
			$tmp = get_text('MenuLM_Check shoot-off before final phases') .'';
			if($_SESSION['MenuFinI']) {
				$tmp .= ' <b style="color:red">('.implode('-', $_SESSION['MenuFinI']).')</b>';
			}
			$tmp .= '|'.$CFG->ROOT_DIR.'Final/Individual/AbsIndividual1.php';
			$ret['FINI'][] = $tmp;
			$ret['FINI'][] = get_text('MenuLM_PrnShootOff') .'|'.$CFG->ROOT_DIR.'Qualification/PrnShootoff.php|||PrintOut';
			$ret['FINI'][] = MENU_DIVIDER;
			$ret['FINI'][] = get_text('MenuLM_Scorecard Printout') .'|'.$CFG->ROOT_DIR.'Final/Individual/PrintScore.php';
// 			$ret['FINI'][] = get_text('MenuLM_Back Number Printout') .'|'.$CFG->ROOT_DIR.'Final/Individual/PrintBackNo.php';
			$ret['FINI'][] = get_text('MenuLM_NewBacknumbers') .'|'.$CFG->ROOT_DIR.'Accreditation/IdCards-new.php?CardType=I';
			if($_SESSION['MenuFinIOn']) {
				$ret['FINI'][] = MENU_DIVIDER;
				$ret['FINI'][] = get_text('MenuLM_Data insert (Bracket view)') .'|'.$CFG->ROOT_DIR.'Final/Individual/InsertPoint_Bra.php';
				$ret['FINI'][] = get_text('MenuLM_Data insert (Table view)') .'|'.$CFG->ROOT_DIR.'Final/Individual/InsertPoint1.php';
				$ret['FINI'][] = get_text('MenuLM_Arrow by Arrow (Advanced user)') .'|'.$CFG->ROOT_DIR.'Final/WriteArrows.php';
				$ret['FINI'][] = get_text('MenuLM_Spotting') . '|' . $CFG->ROOT_DIR.'Final/WriteScoreCard.php?Team=0';
				$ret['FINI'][] = MENU_DIVIDER;
				$ret['FINI'][] = get_text('MenuLM_Printout') .'|'.$CFG->ROOT_DIR.'Final/PrintOut.php';
			}
		}

		/** TEAM FINAL MENU **/
		if($_SESSION['MenuFinTDo']) {
			$ret['FINT'][] = get_text('MenuLM_Team Finals') .'';
			$tmp = get_text('MenuLM_Check shoot-off before final phases') .'';
			if($_SESSION['MenuFinT']) {
				$tmp.=' <b style="color:red">('.implode('-', $_SESSION['MenuFinT']).')</b>';
			}
			$tmp.='|'.$CFG->ROOT_DIR.'Final/Team/AbsTeam1.php';
			$ret['FINT'][] = $tmp;
			$ret['FINT'][] = get_text('MenuLM_PrnShootOff') .'|'.$CFG->ROOT_DIR.'Qualification/PrnShootoff.php|||PrintOut';
			$ret['FINT'][] = MENU_DIVIDER;
			$ret['FINT'][] = get_text('MenuLM_Scorecard Printout') .'|'.$CFG->ROOT_DIR.'Final/Team/PrintScore.php';
// 			$ret['FINT'][] = get_text('MenuLM_Back Number Printout') .'|'.$CFG->ROOT_DIR.'Final/Team/PrintBackNo.php';
			$ret['FINT'][] = get_text('MenuLM_NewBacknumbers') .'|'.$CFG->ROOT_DIR.'Accreditation/IdCards-new.php?CardType=T';
			if($_SESSION['MenuFinTOn']) {
				$ret['FINT'][] = MENU_DIVIDER;
				$ret['FINT'][] = get_text('MenuLM_Change Components') .'|'.$CFG->ROOT_DIR.'Final/Team/ChangeComponents1.php';
				$ret['FINT'][] = MENU_DIVIDER;
				$ret['FINT'][] = get_text('MenuLM_Data insert (Bracket view)') .'|'.$CFG->ROOT_DIR.'Final/Team/InsertPoint_Bra.php';
				$ret['FINT'][] = get_text('MenuLM_Data insert (Table view)') .'|'.$CFG->ROOT_DIR.'Final/Team/InsertPoint1.php';
				$ret['FINT'][] = get_text('MenuLM_Arrow by Arrow (Advanced user)') .'|'.$CFG->ROOT_DIR.'Final/WriteArrows.php';
				$ret['FINT'][] = get_text('MenuLM_Spotting') . '|' . $CFG->ROOT_DIR.'Final/WriteScoreCard.php?Team=1';
				$ret['FINT'][] = MENU_DIVIDER;
				$ret['FINT'][] = get_text('MenuLM_Printout') .'|'.$CFG->ROOT_DIR.'Final/PrintOut.php';
			}
		}

		/** PRINTOUT MENU **/
		$ret['PRNT'][] = get_text('MenuLM_Printout') .'';
		$ret['PRNT'][] = get_text('MenuLM_Participant Lists') .'|'.$CFG->ROOT_DIR.'Partecipants/PrintOut.php';
		$ret['PRNT'][] = get_text('MenuLM_Statistics') .'|'.$CFG->ROOT_DIR.'Partecipants/PrintOutStat.php';
		$ret['PRNT'][] = MENU_DIVIDER;
		$ret['PRNT'][] = get_text('MenuLM_Div/Class Result List') .'|'.$CFG->ROOT_DIR.'Qualification/PrintOut.php';
		$ret['PRNT'][] = get_text('MenuLM_Qualification Round') .'|'.$CFG->ROOT_DIR.'Qualification/PrintOutAbs.php';
		$ret['PRNT'][] = get_text('MenuLM_Final Rounds') .'|'.$CFG->ROOT_DIR.'Final/PrintOut.php';
		$ret['PRNT'][] = MENU_DIVIDER;
		$ret['PRNT'][] = get_text('MenuLM_Header for Result Printouts') .'|'.$CFG->ROOT_DIR.'Tournament/PrintoutComments.php';
		$ret['PRNT'][] = MENU_DIVIDER;
		$ret['PRNT'][] = get_text('MenuLM_ManAwards') .'|'.$CFG->ROOT_DIR.'Tournament/ManAwards.php';
		$ret['PRNT'][] = get_text('MenuLM_CheckAwards') .'|'.$CFG->ROOT_DIR.'Tournament/PDFAward-check.php|||PrintOut';
		$ret['PRNT'][] = get_text('MenuLM_PrintAwards') .'|'.$CFG->ROOT_DIR.'Tournament/PDFAward.php|||PrintOut';
		$ret['PRNT'][] = MENU_DIVIDER;
		$ret['PRNT'][] = get_text('MenuLM_Print Sign') .'|'.$CFG->ROOT_DIR.'Common/Sign';

		/** HHT MENU **/
		if($_SESSION['MenuHHT']) {
			$ret['HHT'][] = get_text('MenuLM_HTT') .'';
			$ret['HHT'][] = get_text('MenuLM_HTT Communication Setup') .'|'.$CFG->ROOT_DIR.'HHT/Configuration.php';
			$ret['HHT'][] = MENU_DIVIDER;
			$ret['HHT'][] = get_text('MenuLM_HTT Setup') .'|'.$CFG->ROOT_DIR.'HHT/InitHTT.php';
			$ret['HHT'][] = get_text('MenuLM_Athletes Setup') .'|'.$CFG->ROOT_DIR.'HHT/InitAthletes.php';
			$ret['HHT'][] = get_text('MenuLM_Scores Setup') .'|'.$CFG->ROOT_DIR.'HHT/InitScores.php';
			$ret['HHT'][] = get_text('MenuLM_Setup HTT Sequence') .'|'.$CFG->ROOT_DIR.'HHT/Sequence.php';
			$ret['HHT'][] = get_text('MenuLM_Collect Data') .'|'.$CFG->ROOT_DIR.'HHT/Collect.php';
			$ret['HHT'][] = get_text('MenuLM_Get Info') .'|'.$CFG->ROOT_DIR.'HHT/GetInfo.php';
		}

		if(!empty($_SESSION['UseApi'])) {
			$ret['API'][] = get_text('ISKMenuHeader') .'';

			switch($_SESSION['UseApi']) {
				case 1:
// 					$ret['API'][] = get_text('ISK-Configuration') .'|'.$CFG->ROOT_DIR.'Api/ISK/';
// 					$ret['API'][] = get_text('MenuLM_QrCodes') .'|'.$CFG->ROOT_DIR.'Api/ISK/QRcodes.php';
					break;
				case 2:
					$ret['API'][] = get_text('ISK-Configuration') .'|'.$CFG->ROOT_DIR.'Api/ISK/';
					$ret['API'][] = get_text('MenuLM_QrCodes') .'|'.$CFG->ROOT_DIR.'Api/ISK/QRcodes.php';
					$ret['API'][] = get_text('ISK-Results') .'|'.$CFG->ROOT_DIR.'Api/ISK/Results.php';
					$ret['API'][] = get_text('ISK-Anomalies', 'Api') .'|'.$CFG->ROOT_DIR.'Api/ISK/Anomalies.php';
					$ret['API'][] = get_text('ISK-GetQRData') .'|'.$CFG->ROOT_DIR.'Api/ISK/GetQRData.php';
					$ret['API'][] = MENU_DIVIDER;
					break;
				case 3:
					$ret['API'][] = get_text('ISK-Configuration') .'|'.$CFG->ROOT_DIR.'Api/ISK-Live/';
					$ret['API'][] = get_text('ISK-Results') .'|'.$CFG->ROOT_DIR.'Api/ISK-Live/Results.php';
					$ret['API'][] = get_text('MenuLM_Get Info') .'|'.$CFG->ROOT_DIR.'Api/ISK-Live/GetInfo.php';
	// 				$ret['API'][] = get_text('ISK-Anomalies', 'Api') .'|'.$CFG->ROOT_DIR.'Api/ISK-Live/Anomalies.php';
					$ret['API'][] = MENU_DIVIDER;
					$ret['API'][] = get_text('MenuLM_QrCodes') .'|'.$CFG->ROOT_DIR.'Api/ISK-Live/QRcodes.php';
					$ret['API'][] = get_text('ISK-GetQRData') .'|'.$CFG->ROOT_DIR.'Api/ISK-Live/GetQRData.php';
					$ret['API'][] = MENU_DIVIDER;
					break;
			}
			$ret['API'][] = get_text('API-TargetGrouping', 'Api') .'|'.$CFG->ROOT_DIR.'Api/ApiGrouping.php';
		}

		/** OUTPUT MENU **/
		$ret['MEDI'][] = get_text('MenuLM_Output') .'';
		$ret['MEDI'][] = get_text('MenuLM_TV Output') .'|'.$CFG->ROOT_DIR.'TV/';
		if(ProgramRelease=='HEAD') $ret['MEDI'][] = get_text('MenuLM_TV Channels') .'|'.$CFG->ROOT_DIR.'TV/ChannelSetup.php';
		$ret['MEDI'][] = MENU_DIVIDER;
		$ret['MEDI'][] = get_text('MenuLM_Spotting') .'|'.$CFG->ROOT_DIR.'Final/Spot/|||_blank';
	} else {
		// MENU OFF!!!

		$ret['COMP'][] = get_text('MenuLM_Competition');
		$ret['COMP'][] = get_text('MenuLM_New') .'|'.$CFG->ROOT_DIR.'Tournament/index.php?New=';
		$ret['COMP'][] = get_text('MenuLM_Open') .'|'.$CFG->ROOT_DIR.'index.php';
		$ret['COMP'][] = get_text('MenuLM_Import Tournament') .'|'.$CFG->ROOT_DIR.'Tournament/TournamentImport.php';
		$ret['COMP'][] = MENU_DIVIDER;
		$ret['COMP'][] = get_text('MenuLM_Select Language') .'|'.$CFG->ROOT_DIR.'Language/index.php';
		if(substr(SelectLanguage(),0,2)!='en') $ret['COMP'][] = 'Select Language|'.$CFG->ROOT_DIR.'Language/index.php';

		if(ProgramRelease=='HEAD') {
			$ret['MEDI'][] = get_text('MenuLM_Output') .'';
			$ret['MEDI'][] = get_text('MenuLM_TV Channels') .'|'.$CFG->ROOT_DIR.'tv.php';
		}

	}

	$ret['MODS'][] = get_text('MenuLM_Modules');
	if(ProgramRelease!='HEAD') $ret['MODS'][] = ''.get_text('MenuLM_Update') .'|'.$CFG->ROOT_DIR.'Update/';
	//$ret['MODS'][] = get_text('MenuLM_SearchModules') .'|'.$CFG->ROOT_DIR.'Modules/';

	/** Additional Modules Menu **/
	$Modules = glob($CFG->DOCUMENT_PATH . 'Modules/*/menu.php');
	foreach($Modules as $Module) {
		include($Module);
	}

	$Modules = glob($CFG->DOCUMENT_PATH . 'Modules/Sets/*/menu.php');
	foreach($Modules as $Module) {
		include($Module);
	}

	$Modules = glob($CFG->DOCUMENT_PATH . 'Modules/Custom/*/menu.php');
	foreach($Modules as $Module) {
		include($Module);
	}

	if(!empty($ret['IANSEO'])) {
		array_unshift($ret['IANSEO'], get_text('MenuLM_Ianseo'));
	}

	return $ret;
}

function build_menu($menu, $dots='.|') {
	$ret='';
	foreach($menu as $val) {
		if(is_array($val)) {
			$tmp=array_shift($val);
			if($tmp)
				$ret .= $dots . $tmp . "\n";
			$ret .= build_menu($val, '.' . $dots);
		} else {
			$ret .= $dots . $val;
		}
		$ret.="\n";
	}
	return trim($ret);
}

function DoSubMenu($List, $lvl=0) {
	$ret='';
	if(is_array($List)) {
		// first item is the title!
		$tit=array_shift($List);
		$li=getSubMenuItem($tit, $lvl);
// 		@list($tit, $lnk, $dum, $dum, $tgt)=explode('|', $tit);
// 		$link ='<a href="'.($lnk ? $lnk : '#url').'"';
// 		if($tgt) $link.=' target="'.$tgt.'"';
// 		$link.=($lvl ? ' class="submenu"' : '').'>'.$tit.'</a>';
// 		$ret.= '<li'.($lvl ? '' : ' class="MenuTitle"').'>'.$link."\n<ul>";
		$ret.=$li;
		$first=true;
		foreach($List as $t => $sl) {
			$ret.=DoSubMenu($sl, $lvl+1);
			$first=false;
		}
		$ret.= "</ul></li>\n";
// 		$ret.='<li class="close"><a href="#url">Close X</a></li>';
	} elseif($List==MENU_DIVIDER  ) {
		$ret.= "<hr>\n";
	} else {
		$li=getSubMenuItem($List);
		$ret.=$li;
// 		@list($t,$lnk, $dum, $dum, $tgt)=explode('|', $List);
// 		$ret.= '<li><a href="'.$lnk.'">'.$t.'</a></li>'."\n";
	}
	return $ret;
}

function getSubMenuItem($tit, $lvl=-1) {
		@list($tit, $lnk, $dum, $dum, $tgt)=explode('|', $tit);
		$link ='<a href="'.($lnk ? $lnk : '#url').'"';
		if($tgt) $link.=' target="'.$tgt.'"';
		$link.=($lvl>0 ? ' class="submenu"' : '').'>'.$tit.'</a>';
		return '<li'.($lvl!=0 ? '' : ' class="MenuTitle"').'>'.$link."\n".($lvl==-1 ? '</li>' : '<ul>');
}

function PrintMenu() {
	$Menu=get_which_menu(CheckTourSession());
	echo '<ul>';
	foreach($Menu as $Top => $List) {
		echo DoSubMenu($List);
	}
	echo '</ul>';
}

