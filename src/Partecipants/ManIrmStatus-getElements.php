<?php
/*
	Viene incluso il motore ajax di index per sfruttare UpdateField
*/
define('debug',false);	// settare a true per l'output di debug

require_once(dirname(dirname(__FILE__)) . '/config.php');
CheckTourSession(true);
checkACL(AclParticipants, AclReadWrite);

$JSON=array('error' => 0, 'elements' => array(), 'ScTitle' => get_text('SubClassRank', 'Tournament'));

if(empty($_REQUEST['type']) or !isset($_REQUEST['value'])) {
    JsonOut($JSON);
}

switch($_REQUEST['type']) {
    case 'team':
        $SQL=array();
        $Team=intval($_REQUEST['value']);
        $In='';
        $Out='';
        $SQL[] = "select 0 as Ord1, EvProgr as Ord2, 0 as Ord3, concat('M',EvCode) as `key`, concat(EvCode,'-',EvEventName) as `value`, '' as class
            from Events
            where EvTournament={$_SESSION['TourId']} and EvTeamEvent=$Team";
        if(!$Team) {
            // Individuals, add the qualification DivClass elements
            $In='(';
            $Out=')';
            $SQL[] = "select 1 as Ord1, DivViewOrder as Ord2, ClViewOrder as Ord3, concat('Q',DivId,ClId) as `key`, concat(DivId,ClId,'-',DivDescription,' ',ClDescription) as `value`, '' as class
                from Divisions
                inner join Classes on ClTournament=DivTournament and (ClDivisionsAllowed='' or find_in_set(DivId, ClDivisionsAllowed))
                where DivTournament={$_SESSION['TourId']} and DivAthlete=1 and ClAthlete=1 ";
        }


        $q=safe_r_sql($In . implode(') UNION (', $SQL) . $Out . ' order by Ord1, Ord2, Ord3');

        $OldEvent='';
        while($r=safe_fetch($q)) {
            if($r->key[0]!=$OldEvent) {
                $OldEvent=$r->key[0];
                switch($OldEvent) {
	                case 'Q':
	                    $JSON['elements'][]=array('key' => '', 'value'=>get_text('MenuLM_Divisions and Classes'), 'class' => 'disabled');
	                    break;
	                case 'M':
	                    $JSON['elements'][]=array('key' => '', 'value'=>get_text('Events', 'Tournament'), 'class' => 'disabled');
	                    break;
                }
            }
            $JSON['elements'][]=array('key' => $r->key, 'value'=>$r->value, 'class' => '');
        }
        break;
    case 'event':
        $Team=intval($_REQUEST['team']);
        $JSON['elements'][]=array('key' => 'Q', 'value'=>get_text('QualRound'), 'class'=>'');
        if($_REQUEST['value'][0]=='M') {
        	require_once('Common/Lib/CommonLib.php');
        	$SQL="select EvElimType, EvFinalFirstPhase from Events where EvTeamEvent=$Team and EvCode='".substr($_REQUEST['value'],1)."' and EvTournament={$_SESSION['TourId']}";
            $q=safe_r_sql($SQL);
            if($r=safe_fetch($q) and $r->EvFinalFirstPhase) {
                require_once('Common/Fun_Phases.inc.php');
                switch($r->EvElimType) {
	                case 4:
	                	// New Field/3D formula
	                	foreach(getPoolMatchesHeadersWA() as $k => $v) {
	                        $JSON['elements'][]=array('key' => $k, 'value'=>$v, 'class'=>'');
		                }
	                	break;
	                case 3:
	                	// World Games
	                	foreach(getPoolMatchesPhases() as $k => $v) {
	                        $JSON['elements'][]=array('key' => $k, 'value'=>$v, 'class'=>'');
		                }
	                	break;
	                case 2:
                        $JSON['elements'][]=array('key' => 'E0', 'value'=>get_text('Eliminations_1', 'InfoSystem'), 'class'=>'');
                        $JSON['elements'][]=array('key' => 'E1', 'value'=>get_text('Eliminations_2', 'InfoSystem'), 'class'=>'');
	                	break;
	                case 1:
                        $JSON['elements'][]=array('key' => 'E1', 'value'=>get_text('Eliminations_2', 'InfoSystem'), 'class'=>'');
	                	break;
                }

                $GridPhase=valueFirstPhase($r->EvFinalFirstPhase);
                while($GridPhase>=1) {
                    $Phase=namePhase($r->EvFinalFirstPhase, $GridPhase);
                    if($Phase==$GridPhase) {
	                    $JSON['elements'][]=array('key' => $GridPhase, 'value'=>get_text($Phase.'_Phase'), 'class'=>'');
                    } else {
	                    $JSON['elements'][]=array('key' => $GridPhase, 'value'=>get_text($Phase.'_Phase').' + '.get_text($GridPhase.'_Phase'), 'class'=>'');
                    }
	                $GridPhase/=2;
                }
                $JSON['elements'][]=array('key' => 0, 'value'=>get_text('0_Phase'), 'class'=>'');
            }
        }
        break;

	case 'phase':
		$JSON['HasBye']=false;
        $Team=intval($_REQUEST['team']);
        $Phase=$_REQUEST['value'];
        $Event=substr($_REQUEST['event'], 1);
        $EventType=$_REQUEST['event'][0];
        $Search=$_REQUEST['search'];

        $Order='EnFirstName, EnName, CoCode';
        $OrderType='ASC';

        switch($_REQUEST['ordertype']) {
	        case 'ASC': $OrderType='ASC'; break;
	        case 'DESC': $OrderType='DESC'; break;
        }

        switch($_REQUEST['order']) {
	        case 'Code': $Order="CAST(Bib as UNSIGNED) $OrderType, Bib $OrderType"; break;
	        case 'Archer': $Order="Athlete $OrderType"; break;
	        case 'CountryCode': $Order="Noc $OrderType, Athlete"; break;
	        case 'Country': $Order="Country $OrderType, Athlete"; break;
	        case 'IrmStatus': $Order="IrmType $OrderType"; break;
	        case 'Score': $Order="CAST(Score as UNSIGNED) $OrderType "; break;
	        case 'QualRank': $Order="CAST(QualRank as UNSIGNED) $OrderType"; break;
	        case 'FinRank': $Order="CAST(FinRank as UNSIGNED) $OrderType"; break;
	        case 'SubClassRank': $Order="SubClass, CAST(SubClassRank as UNSIGNED) $OrderType"; break;
        }

        $SQL='';

        // Event Type
		switch($Team.$EventType) {
			case '0Q':
				// individual DivClass Qualifications
				$SQL="select concat('0-Q-',EnId) as IrmKey, EnId as Id, EnCode as Bib, concat(ucase(EnFirstName), ' ', EnName) as Athlete, CoCode as Noc, CoName as Country, QuIrmType as IrmType, concat_ws('/',QuScore,QuGold,QuXnine) as Score, QuClRank as QualRank, '' as FinRank, if(EnSubClass=0, '', QuSubClassRank) as SubClassRank, QuNotes as Note, ScDescription as SubClass, 0 as Bye
					from Entries
					inner join Qualifications on QuId=EnId
					inner join Countries on CoId=EnCountry
					left join SubClass on ScTournament=EnTournament and ScId=EnSubClass
					where concat(EnDivision,EnClass)=".StrSafe_DB($Event)." and EnTournament={$_SESSION['TourId']}".($Search ? ' and '.assembleWhereCondition(array('EnName','EnFirstname','CoName'), array($Search)) : '')."
					order by $Order";
				break;
			case '1Q':
				// Team qualification... needs to first change how teams are generated otherwise at each recalculation of the teams the IrmStatus is reset to OK
				// this is never triggered
				break;
			case '0M':
				// Individual Events
				if($Phase=='Q') {
                    // Qualification
                    $SQL = "select concat('0-M-Q-$Event-',EnId) as IrmKey, EnId as Id, EnCode as Bib, concat(ucase(EnFirstName), ' ', EnName) as Athlete, CoCode as Noc, CoName as Country, IndIrmType as IrmType, concat_ws('/',QuScore,QuGold,QuXnine) as Score, IndRank as QualRank, if(EvFinalFirstPhase>0, IndRankFinal, '') as FinRank, if(EnSubClass=0, '', QuSubClassRank) as SubClassRank, IndNotes as Note, ScDescription as SubClass, 0 as Bye
						from Individuals
					    inner join Events on EvTournament=IndTournament and EvCode=IndEvent and EvTeamEvent=0
						inner join Entries on EnId=IndId and EnTournament=IndTournament
						inner join Qualifications on QuId=EnId
						inner join Countries on CoId=EnCountry
						left join SubClass on ScTournament=EnTournament and ScId=EnSubClass
						where IndEvent=" . StrSafe_DB($Event) . " and IndTournament={$_SESSION['TourId']}" . ($Search ? ' and ' . assembleWhereCondition(array('EnName', 'EnFirstname', 'CoCode', 'CoName'), array($Search)) : '') . "
						order by $Order";
				} elseif($Phase=='E0') {
                    // Qualification
                    $SQL = "select concat('0-M-E0-$Event-',EnId) as IrmKey, EnId as Id, EnCode as Bib, concat(ucase(EnFirstName), ' ', EnName) as Athlete, CoCode as Noc, CoName as Country, ElIrmType as IrmType, ElScore as Score, IndRank as QualRank, if(EvFinalFirstPhase>0, IndRankFinal, '') as FinRank, ElRank as SubClassRank, IndNotes as Note, '' as SubClass, 0 as Bye
						from Individuals
					    inner join Events on EvTournament=IndTournament and EvCode=IndEvent and EvTeamEvent=0
						inner join Entries on EnId=IndId and EnTournament=IndTournament
						inner join Eliminations on ElId=EnId and ElTournament=EnTournament and ElElimPhase=0 and ElEventCode=EvCode
						inner join Countries on CoId=EnCountry
						left join SubClass on ScTournament=EnTournament and ScId=EnSubClass
						where IndEvent=" . StrSafe_DB($Event) . " and IndTournament={$_SESSION['TourId']}" . ($Search ? ' and ' . assembleWhereCondition(array('EnName', 'EnFirstname', 'CoCode', 'CoName'), array($Search)) : '') . "
						order by $Order";
                    $JSON['ScTitle']=get_text('Eliminations_1');
				} elseif($Phase=='E1') {
                    // Qualification
                    $SQL = "select concat('0-M-E1-$Event-',EnId) as IrmKey, EnId as Id, EnCode as Bib, concat(ucase(EnFirstName), ' ', EnName) as Athlete, CoCode as Noc, CoName as Country, ElIrmType as IrmType, ElScore as Score, IndRank as QualRank, if(EvFinalFirstPhase>0, IndRankFinal, '') as FinRank, ElRank as SubClassRank, IndNotes as Note, '' as SubClass, 0 as Bye
						from Individuals
					    inner join Events on EvTournament=IndTournament and EvCode=IndEvent and EvTeamEvent=0
						inner join Entries on EnId=IndId and EnTournament=IndTournament
						inner join Eliminations on ElId=EnId and ElTournament=EnTournament and ElElimPhase=1 and ElEventCode=EvCode
						inner join Countries on CoId=EnCountry
						left join SubClass on ScTournament=EnTournament and ScId=EnSubClass
						where IndEvent=" . StrSafe_DB($Event) . " and IndTournament={$_SESSION['TourId']}" . ($Search ? ' and ' . assembleWhereCondition(array('EnName', 'EnFirstname', 'CoCode', 'CoName'), array($Search)) : '') . "
						order by $Order";
                    $JSON['ScTitle']=get_text('Eliminations_2');
				} elseif(is_numeric($Phase)) {
					$SQL="select concat('0-M-$Phase-$Event-',EnId) as IrmKey, EnId as Id, EnCode as Bib, concat(ucase(EnFirstName), ' ', EnName) as Athlete, CoCode as Noc, CoName as Country, FinIrmType as IrmType, concat('Tgt ', ifnull(FsTarget+0,''), ' - ', if(EvMatchMode=1, FinSetScore, FinScore)) as Score, IndRank as QualRank, IndRankFinal as FinRank, '' as SubClassRank, '' as SubClass, FinNotes as Note, FinTie as Bye, FinMatchNo % 2 as SecondMatch
						from Individuals
						inner join Entries on EnId=IndId and EnTournament=IndTournament
					    inner join Events on EvTournament=IndTournament and EvCode=IndEvent and EvTeamEvent=0
						inner join Countries on CoId=EnCountry
						inner join Finals on FinTournament=IndTournament and FinEvent=IndEvent and FinAthlete=IndId
						inner join Grids on GrPhase=$Phase and GrMatchNo=FinMatchNo 
						LEFT JOIN FinSchedule on FSTournament=IndTournament and FSEvent=IndEvent and FSMatchNo=FinMatchNo and FSTeamEvent=EvTeamEvent
						where IndEvent=".StrSafe_DB($Event)." and IndTournament={$_SESSION['TourId']}".($Search ? ' and '.assembleWhereCondition(array('EnName','EnFirstname', 'CoCode', 'CoName'), array($Search)) : '')."
						order by FsTarget=0, FSScheduledDate, FSScheduledTime, FsTarget, FinMatchno, $Order";
					$JSON['HasBye']=true;
				}

				break;
			case '1M':
				if($Phase=='Q') {
					$SQL="select concat('1-M-Q-$Event-',TeCoId, '-',TeSubteam) as IrmKey, 
       						CoId as Id, if(TeSubTeam>0, TeSubTeam, '') as Bib, group_concat(concat('<div>', ucase(EnFirstName), ' ', EnName,'</div>') separator '') as Athlete, CoCode as Noc, CoName as Country, TeIrmType as IrmType, TeScore as Score, TeRank as QualRank, TeRankFinal as FinRank, '' as SubClassRank, TeNotes as Note, '' as SubClass, 0 as Bye
						from Teams
						inner join TeamComponent on TcTournament=TeTournament and TcCoId=TeCoId and TcSubTeam=TeSubTeam and TcEvent=TeEvent and TcFinEvent=TeFinEvent
						inner join Countries on CoId=TeCoId and CoTournament=TeTournament
						inner join Entries on EnId=TcId
						where TeFinEvent=1 and TeEvent=".StrSafe_DB($Event)." and TeTournament={$_SESSION['TourId']}".($Search ? ' and '.assembleWhereCondition(array('EnName','EnFirstname', 'CoCode', 'CoName'), array($Search)) : '')." and TeFinEvent=1
						group by TeCoId, TeSubTeam, TeTournament, TeEvent
						order by $Order";
				} elseif(is_numeric($Phase)) {
					$SQL="select concat('1-M-$Phase-$Event-',TeCoId, '-',TeSubteam) as IrmKey, 
       						CoId as Id, if(TeSubTeam>0, TeSubTeam, '') as Bib, group_concat(concat('<div>', ucase(EnFirstName), ' ', EnName,'</div>') separator '') as Athlete, CoCode as Noc, CoName as Country, TfIrmType as IrmType, concat('Tgt ', ifnull(FsTarget+0,''), ' - ', if(EvMatchMode=1, TfSetScore, TfScore)) as Score, TeRank as QualRank, TeRankFinal as FinRank, '' as SubClassRank, '' as SubClass, TfNotes as Note, TfTie as Bye, TfMatchNo % 2 as SecondMatch
						from Teams
					    inner join Events on EvTournament=TeTournament and EvCode=TeEvent and EvTeamEvent=1
						inner join TeamFinComponent on TfcTournament=TeTournament and TfcCoId=TeCoId and TfcSubTeam=TeSubTeam and TfcEvent=TeEvent
						inner join Countries on CoId=TeCoId and CoTournament=TeTournament
						inner join Entries on EnId=TfcId
						inner join TeamFinals on TfTournament=TeTournament and TfEvent=TeEvent and TfTeam=TeCoId and TfSubTeam=TeSubTeam
						inner join Grids on GrPhase=$Phase and GrMatchNo=TfMatchNo 
						LEFT JOIN FinSchedule on FSTournament=TeTournament and FSEvent=TeEvent and FSMatchNo=TfMatchNo and FSTeamEvent=EvTeamEvent
						where TeFinEvent=1 and TeEvent=".StrSafe_DB($Event)." and TeTournament={$_SESSION['TourId']}".($Search ? ' and '.assembleWhereCondition(array('EnName','EnFirstname', 'CoCode', 'CoName'), array($Search)) : '')." and TeFinEvent=1
						group by TeCoId, TeSubTeam, TeTournament, TeEvent
						order by FSScheduledDate, FSScheduledTime, FsTarget, $Order";
					$JSON['HasBye']=true;
				} else {
					//debug_svela($_REQUEST);
				}
				break;
		}

		if($SQL) {
			$q=safe_r_sql($SQL);
			while($r=safe_fetch($q)) {
				$JSON['hideFinals']=($r->FinRank==='');
				$JSON['hideSubClass']=($r->SubClassRank==='');
				$JSON['elements'][]=$r;
			}
		}

		break;
}

JsonOut($JSON);
