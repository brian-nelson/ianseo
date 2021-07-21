<?php
	define('debug',false);	// settare a true per l'output di debug

	//error_reporting(E_ALL);

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Fun_Various.inc.php');
	require_once('Common/Fun_Sessions.inc.php');
	require_once('Qualification/Fun_Qualification.local.inc.php');
	require_once('Fun_Partecipants.local.inc.php');
	require_once('Common/Fun_FormatText.inc.php');

	if (!CheckTourSession()) printCrackerror('popup');
    checkACL(AclParticipants, AclReadWrite);

	$id=isset($_REQUEST['id']) ? $_REQUEST['id'] : null;

	if ($id===null)
		exit;


	$ses=null;
	$tar=null;

	if (isset($_REQUEST['ses']) && isset($_REQUEST['tar']))
	{
		$ses=$_REQUEST['ses'];
		$tar=$_REQUEST['tar'];
	}

	$reload=0;	// 0 non succede nulla all'opener;1 ricarica;2 ricarica e il popup si chiude

/* salvo */
	if (isset($_REQUEST['Command']))
	{
		if (!IsBlocked(BIT_BLOCK_PARTICIPANT) && ($_REQUEST['Command']=='SAVE' || $_REQUEST['Command']=='SAVE_CONTINUE'))
		{

			$EnCountries=array(''=>0,'2'=>0,'3'=>0);
			$EnCoCodes=array(''=>'','2'=>'','3'=>'');
			$EnDob='0000-00-00';

		// country
			foreach (array('','2','3') as $v)
			{
				if (trim($_REQUEST['d_c_CoCode' . $v.'_'])) {

					// Code is ALL CAPS
					// Name is Title!
					$CoCode=mb_convert_case(trim($_REQUEST['d_c_CoCode' . $v.'_']), MB_CASE_UPPER, "UTF-8");
					$EnCoCodes[$v]=$CoCode;
					$CoName=AdjustCaseTitle($_REQUEST['d_c_CoName' . $v.'_']);

					// if code already exists, updates the name
					$Select
						= "SELECT CoId,CoName "
						. "FROM Countries "
						. "WHERE CoCode='$CoCode' AND CoTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
					$Rs=safe_r_sql($Select);

					if ($myrow=safe_fetch($Rs)) {
						$CoId=$myrow->CoId;
					} else {
						// Inserts the new nation/club
						$Insert = "INSERT INTO Countries (CoTournament,CoCode) "
							. "VALUES("
							. StrSafe_DB($_SESSION['TourId']) . ","
							. StrSafe_DB($CoCode) . " "
							. ")";
						$RsIns=safe_w_sql($Insert);

						// estraggo l'ultimo id
						$CoId=safe_w_last_id();

						// we need to add this country...
						LogAccBoothQuerry("insert ignore into Countries set CoCode=".StrSafe_DB($CoCode).", CoTournament=§TOCODETOID§", $_SESSION['TourCode']);
					}

					$EnCountries[$v]=$CoId;

					// Updates the name anyway
					$Update = "UPDATE Countries SET "
						. "CoName=" . StrSafe_DB($CoName) . " "
						. "WHERE CoId=" . StrSafe_DB($CoId) . " AND CoTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
					$Rs=safe_w_sql($Update);

					// we need to update this country...
					LogAccBoothQuerry("update Countries set CoName=" . StrSafe_DB($CoName) . " where CoCode=".StrSafe_DB($CoCode)." and CoTournament=§TOCODETOID§", $_SESSION['TourCode']);
				}
			}

		// sesso e dob
			$EnDob='0000-00-00';
			$ctrlCode=ConvertDateLoc($_REQUEST['d_e_EnCtrlCode_']);
			if ($ctrlCode!==false)
			{
				$EnDob=$ctrlCode;
			}

		/*
		 * In base al valore dell'id decido se aggiungere o aggiornare
		 *
		 * 1) Scrivo in Entries
		 * 2) Se aggiungo, creo la riga anche in Qualifications
		 * 3) Scrivo la sessione in Qualifications
		 */

			// recupero dell'indicazione se atleta in div e clas con la div e clas di gara
			$t=safe_r_sql("SELECT"
				." DivAthlete and ClAthlete as Athlete  "
				."FROM "
				." Divisions "
				." INNER JOIN Classes on DivTournament=ClTournament "
				."WHERE "
				." DivTournament={$_SESSION['TourId']} "
				." AND DivId=". StrSafe_DB(trim($_REQUEST['d_e_EnDivision_']))
				." AND ClID=" . StrSafe_DB(trim($_REQUEST['d_e_EnClass_'])));

			$EnAthlete = ($u=safe_fetch($t) and $u->Athlete);

			$Op=($id!=0 ? 'Up' : 'Ins');

			$recalc=false;
			$indFEventOld=$teamFEventOld=$countryOld=$divOld=$clOld=$subClOld=$zeroOld=null;
			$indFEvent=$teamFEvent=$country=$div=$cl=$subCl=$zero=null;

			if ($id!=0)
			{
			// se la vecchia classe o divisione è diversa ricalcolo spareggi e squadre per la vecchia e la nuova
				$query= "SELECT EnClass FROM Entries WHERE EnId=" . StrSafe_DB($id) . " AND (EnClass<>" . StrSafe_DB($_REQUEST['d_e_EnClass_']) . " OR EnDivision<>" . StrSafe_DB($_REQUEST['d_e_EnDivision_']) . " OR EnStatus<>" . StrSafe_DB($_REQUEST['d_e_EnStatus_']) . ") ";
				//print $query;exit;
				$rs=safe_r_sql($query);
				if ($rs && safe_num_rows($rs)==1)
				{
					$recalc=true;
				// prendo le vecchie impostazioni
					$x=Params4Recalc($id);
					if ($x!==false)
					{
						list($indFEventOld,$teamFEventOld,$countryOld,$divOld,$clOld,$subClOld,$zeroOld)=$x;
					}
				}
				// se la vecchia matricola è diversa Azzero la foto
				$query= "SELECT EnCode FROM Entries WHERE EnId=" . StrSafe_DB($id) . " AND EnCode<>" . StrSafe_DB($_REQUEST['d_e_EnCode_']);
				//print $query;exit;
				$rs=safe_r_sql($query);
				if ($rs && safe_num_rows($rs)==1)
				{
					$query
						= "DELETE FROM "
							. "Photos "
						. "WHERE "
							. "PhEnId=" . StrSafe_DB($id) . " ";
					$rs=safe_w_sql($query);
					// IMPORTANT! NO NEED to delete the icture associated with the old EnCode anyway!...
				}

			}
			else
			{
				$recalc=true;
			}

			$EnName=AdjustCaseTitle($_REQUEST['d_e_EnName_']);
			$EnFirstName=AdjustCaseTitle($_REQUEST['d_e_EnFirstName_']);

			$EnCode=trim($_REQUEST['d_e_EnCode_']);
			$EnIocCode=$_REQUEST['LupSelect'];
			$EnDivision=trim($_REQUEST['d_e_EnDivision_']);
			$EnWChair=trim($_REQUEST['d_e_EnWChair_']);
			$EnDouble=trim($_REQUEST['d_e_EnDoubleSpace_']);

			$Sql="EnTournament=§TOCODETOID§,
				EnDivision=" . StrSafe_DB($EnDivision) . ",
				EnClass=" . StrSafe_DB(trim($_REQUEST['d_e_EnClass_'])) . ",
				EnAthlete=" . StrSafe_DB($EnAthlete) . ",
				EnSubClass=" . StrSafe_DB(trim($_REQUEST['d_e_EnSubClass_'])) . ",
				EnAgeClass=" . StrSafe_DB(trim($_REQUEST['d_e_EnAgeClass_'])) . ",
				EnSubTeam=" . StrSafe_DB($_REQUEST['d_e_EnSubTeam_']) . ",
				EnCountry=-Country-,
				EnCountry2=-Country2-,
				EnCountry3=-Country3-,
				EnDob=" . StrSafe_DB(trim($EnDob)) . ",
				EnCode=" . StrSafe_DB($EnCode) . ",
				EnName=" . StrSafe_DB($EnName) . ",
				EnFirstName=" . StrSafe_DB($EnFirstName) . ",
				EnSex=" . StrSafe_DB($_REQUEST['d_e_EnSex_']) . ",
				EnTargetFace=" . StrSafe_DB($_REQUEST['d_e_EnTargetFace_']) . ",
				EnStatus=" . StrSafe_DB($_REQUEST['d_e_EnStatus_']) . ",
				EnIndClEvent=" . StrSafe_DB(trim($_REQUEST['d_e_EnIndClEvent_'])) . ",
				EnTeamClEvent=" . StrSafe_DB(trim($_REQUEST['d_e_EnTeamClEvent_'])) . ",
				EnIndFEvent=" . StrSafe_DB(trim($_REQUEST['d_e_EnIndFEvent_'])) . ",
				EnTeamFEvent=" . StrSafe_DB(trim($_REQUEST['d_e_EnTeamFEvent_'])) . ",
				EnTeamMixEvent=" . StrSafe_DB(trim($_REQUEST['d_e_EnTeamMixEvent_']));

			$SelectEnId="select EnId from Entries where EnTournament=§TOCODETOID§ and EnCode='{$EnCode}' and EnIocCode='{$EnIocCode}' and EnDivision='{$EnDivision}' limit 1";

			$Insert = "INSERT INTO Entries set
					". ($id ? " EnId='{$id}', " : "") ."
					$Sql
				ON DUPLICATE KEY UPDATE
					$Sql"
			;
			$Rs=safe_w_sql(str_replace(array('§TOCODETOID§', '-Country-','-Country2-','-Country3-'), array(StrSafe_DB($_SESSION['TourId']), StrSafe_DB($EnCountries['']), StrSafe_DB($EnCountries['2']), StrSafe_DB($EnCountries['3'])), $Insert));

			$NewAthlete=false;
			if ($id==0) {
				$id=safe_w_last_id();
				$NewAthlete=true;
			}

			if($NewAthlete) {
				LogAccBoothQuerry(str_replace(array('-Country-','-Country2-','-Country3-'), array(
						$EnCoCodes[''] ? "(select CoId from Countries where CoCode='{$EnCoCodes['']}' and CoTournament=§TOCODETOID§)" : 0,
						$EnCoCodes['2'] ? "(select CoId from Countries where CoCode='{$EnCoCodes['2']}' and CoTournament=§TOCODETOID§)" : 0,
						$EnCoCodes['3'] ? "(select CoId from Countries where CoCode='{$EnCoCodes['3']}' and CoTournament=§TOCODETOID§)" : 0,
						), "INSERT INTO Entries set EnIocCode='{$EnIocCode}', EnWChair='$EnWChair', EnDoubleSpace='$EnDouble', $Sql"), $_SESSION['TourCode']);
				LogAccBoothQuerry("insert into Qualifications set QuSession='0', QuId=($SelectEnId)", $_SESSION['TourCode']);
			} else {
				if(safe_w_affected_rows()) {
					safe_w_sql("update Entries set EnBadgePrinted=0 where EnId=$id");
					LogAccBoothQuerry(str_replace(array('-Country-','-Country2-','-Country3-'), array(
							$EnCoCodes[''] ? "(select CoId from Countries where CoCode='{$EnCoCodes['']}' and CoTournament=§TOCODETOID§)" : 0,
							$EnCoCodes['2'] ? "(select CoId from Countries where CoCode='{$EnCoCodes['2']}' and CoTournament=§TOCODETOID§)" : 0,
							$EnCoCodes['3'] ? "(select CoId from Countries where CoCode='{$EnCoCodes['3']}' and CoTournament=§TOCODETOID§)" : 0,
							), "update Entries set EnTimestamp=EnTimestamp, EnBadgePrinted=0, EnWChair='$EnWChair', EnDoubleSpace='$EnDouble', $Sql
								where EnTournament=§TOCODETOID§ and EnCode='{$EnCode}' and EnIocCode='{$EnIocCode}' and EnDivision='{$EnDivision}'"), $_SESSION['TourCode']);
				}
			}

			safe_w_sql("update Entries set
				EnTimestamp=EnTimestamp,
				EnIocCode=". StrSafe_DB($EnIocCode) . ",
				EnWChair=" . StrSafe_DB($EnWChair) . ",
				EnDoubleSpace=" . StrSafe_DB($EnDouble) . "
				where EnId=$id");

			// deletes the email
			safe_w_SQL("update ExtraData set EdEmail='' where EdId=$id and EdType='E'");
			LogAccBoothQuerry("update ExtraData set EdEmail='' where EdId=($SelectEnId) and EdType='E'");
			safe_w_sql("delete from ExtraData where EdEmail='' and EdId=$id and EdType='E' and EdExtra=''");
			LogAccBoothQuerry("delete from ExtraData where EdEmail='' and EdId=($SelectEnId) and EdType='E' and EdExtra=''");

			// updates the flights for Vegas... if any
			safe_w_sql("update Vegas set VeSubClass=".StrSafe_DB(trim($_REQUEST['d_e_EnSubClass_']))." where VeId=$id");
			LogAccBoothQuerry("update Vegas set VeSubClass=".StrSafe_DB(trim($_REQUEST['d_e_EnSubClass_']))." where VeId=($SelectEnId)");

			// if it is an email...
			if(preg_match('/^[a-z0-9._#-]+@[a-z0-9._-]+$/sim', $_REQUEST['d_ed_EdEmail_'])) {
				safe_w_sql("insert into ExtraData set EdId=$id, EdType='E', EdEmail=".StrSafe_DB($_REQUEST['d_ed_EdEmail_'])." on duplicate key update EdEmail=".StrSafe_DB($_REQUEST['d_ed_EdEmail_']));
				$up=safe_w_affected_rows();
				LogAccBoothQuerry("insert into ExtraData set EdId=($SelectEnId), EdType='E', EdEmail=".StrSafe_DB($_REQUEST['d_ed_EdEmail_'])." on duplicate key update EdEmail=".StrSafe_DB($_REQUEST['d_ed_EdEmail_']));
				if($up) {
				    // updates the entry timestamp as well
                    safe_w_SQL("update Entries set EnTimestamp='".date('Y-m-d H:i:s')."' where EnId={$id}");
                    LogAccBoothQuerry("update Entries set EnTimestamp='".date('Y-m-d H:i:s')."' where EnId=($SelectEnId)");
                }
			}

			if ($Op=='Ins') {
			// aggiungo la riga in Qualifications
				$Insert
					= "INSERT INTO Qualifications (QuId,QuSession) "
					. "VALUES("
					. StrSafe_DB($id) . ","
					. "0"
					. ") ";
				$Rs=safe_w_sql($Insert);
				//print $Insert.'<br><br>';
			}


			$Update
				= "UPDATE Qualifications SET "
				. "QuSession=" . StrSafe_DB((!empty($_REQUEST['d_q_QuSession_']) ? $_REQUEST['d_q_QuSession_']:0)) . ", "
				. "QuTargetNo=" . StrSafe_DB((!empty($_REQUEST['d_q_QuSession_']) ? $_REQUEST['d_q_QuSession_'] :'' ) . $_REQUEST['d_q_QuTargetNo_']) . ", "
				. "QuTarget=" . intval($_REQUEST['d_q_QuTargetNo_']) . ", "
				. "QuLetter='" . substr($_REQUEST['d_q_QuTargetNo_'], -1) . "',
				QuTimestamp=QuTimestamp "
				. "WHERE  ";
			$Rs=safe_w_sql($Update."QuId=" . StrSafe_DB($id));
			if(safe_w_affected_rows()) {
				LogAccBoothQuerry($Update." QuId=($SelectEnId)");
				safe_w_sql("Update Entries set EnTimestamp='".date('Y-m-d H:i:s')."' where EnId={$id}");
				LogAccBoothQuerry("Update Entries set EnTimestamp='".date('Y-m-d H:i:s')."' where EnId=($SelectEnId)");
				safe_w_sql("update Qualifications SET QuBacknoPrinted=0, QuTimestamp=QuTimestamp where QuId={$id}");
				LogAccBoothQuerry("update Qualifications SET QuBacknoPrinted=0, QuTimestamp=QuTimestamp where QuId=($SelectEnId)");
			}

			if ($recalc) {
				$x=Params4Recalc($id);
				if ($x!==false)
				{
					list($indFEvent,$teamFEvent,$country,$div,$cl,$subCl,$zero)=$x;
				}

			// ricalcolo il vecchio e il nuovo
				if (!is_null($indFEvent)) {
					RecalculateShootoffAndTeams($indFEventOld,$teamFEventOld,$countryOld,$divOld,$clOld,$subClOld,$zeroOld);
				}
				RecalculateShootoffAndTeams($indFEvent,$teamFEvent,$country,$div,$cl,$subCl,$zero);

			// rank di classe x tutte le distanze
				$q="SELECT ToNumDist FROM Tournament WHERE ToId={$_SESSION['TourId']}";
				$r=safe_r_sql($q);
				$tmpRow=safe_fetch($r);
				for ($i=0; $i<$tmpRow->ToNumDist;++$i)
				{
					if (!is_null($indFEvent))
						CalcQualRank($i,$divOld.$clOld);
					CalcQualRank($i,$div.$cl);
				}

				MakeIndAbs();

			}
			checkAgainstLUE($id);
		}
		//exit;
		if ($_REQUEST['Command']=='SAVE')
		{
			$reload=2;
		}
		elseif ($_REQUEST['Command']=='SAVE_CONTINUE')
		{
			$reload=1;

			$id=0;

		// se $ses e $tar non sono nulli cerco il primo paglione libero dopo $ses.$tar
			if ($ses!==null && $tar!==null)
			{
				$t=$ses.$tar;
				$q="
					SELECT
						AtTargetNo,EnId
					FROM
						AvailableTarget
						LEFT JOIN (
							SELECT
								EnTournament,EnId,QuTargetNo
							FROM
								Entries INNER JOIN Qualifications ON EnId=QuId AND EnTournament={$_SESSION['TourId']}
						) AS sq ON AtTournament=EnTournament AND AtTargetNo=QuTargetNo
					WHERE
						AtTournament={$_SESSION['TourId']} AND AtTargetNo>'{$t}' AND EnId IS NULL
					LIMIT 0,1
				";
				//print $q;exit;
				$r=safe_r_sql($q);

				if (safe_num_rows($r)==1)
				{
					$row=safe_fetch($r);
					$ses=substr($row->AtTargetNo,0,1);
					$tar=substr($row->AtTargetNo,1);
				}
				else
				{
					$ses=null;
					$tar=null;
				}
			}
		}
	}
/* fine salvo */

	$record=null;

	if ($id!=0)
	{
		$record=GetRows($id);
		$record=$record[0];
		$record['dob']=dateRenderer($record['dob'],get_text('DateFmt'));
	}
	else
	{
		$record=array(
			'id' => 0,
			'ioccode' => '',
			'code' => '',
			'status' => 0,
			'session' => $ses!==null ? $ses : '',
			'targetno' => $tar!==null ? $tar : '',
			'firstname' => '',
			'name' => '',
			'email' => '',
			'sex_id' => 0,
			'sex' => get_text('ShortMale','Tournament'),
			'ctrl_code' => '',
			'dob' => '',
			'country_id' => 0,
			'country_code' => '',
			'country_name' => '',
			'sub_team' => 0,
			'country_id2' =>0,
			'country_code2' =>'',
			'country_name2' => '',
			'country_id3' =>0,
			'country_code3' =>'',
			'country_name3' => '',
			'division' =>'--',
			'class' => '--',
			'ageclass' => '--',
			'subclass' => '--',
			'targetface' => 0,
			'targetface_name' => '',
			'indcl'=>0,
			'teamcl'=>0,
			'indfin'=>0,
			'teamfin'=>0,
			'mixteamfin'=>0,
			'wc'=>0,
			'double'=>0,
		);
	}

	$arrStatus=array();

	foreach (array(1,5,7,8,9) as $s)
	{
		$arrStatus[]=array('id'=>$s,'descr'=>get_text('Status_'.$s));
	}

	$comboStatus=ComboFromRs(
		$arrStatus,
		'id',
		'descr',
		1,
		null,
		array('0',''),
		'd_e_EnStatus_',
		'd_e_EnStatus_'
	);

	$tmp=GetSessions('Q');
	$sessions=array();
	foreach ($tmp as $s)
	{
		$sessions[]=array('id'=>$s->SesOrder,'descr'=>$s->Descr);
	}

	$comboSes=ComboFromRs(
		$sessions,
		'id',
		'descr',
		1,
		null,
		array('0','--'),
		'd_q_QuSession_',
		'd_q_QuSession_',
		array(
			'onblur'=>'SelectSession();'
		)
	);


	$comboSex=ComboFromRs(
		array(
			array('id'=>0,'descr'=>get_text('ShortMale','Tournament')),
			array('id'=>1,'descr'=>get_text('ShortFemale','Tournament'))
		),
		'id',
		'descr',
		1,
		0,
		null,
		'd_e_EnSex_',
		'd_e_EnSex_',
		array(
			'onchange'=>'CheckCtrlCode(this);'
		)
	);

	// Division Selects
	$rsDiv=safe_r_sql("SELECT DivId FROM Divisions WHERE DivTournament=" . StrSafe_DB($_SESSION['TourId']) . " ORDER BY DivViewOrder ASC");
	$comboDiv=ComboFromRs(
		$rsDiv,
		'DivId',
		'DivId',
		0,
		null,
		array('','--'),
		'd_e_EnDivision_',
		'd_e_EnDivision_',
		array(
			'onchange'=>'CheckCtrlCode(this);'
		)
	);

	safe_data_seek($rsDiv,0);

	$comboFindDiv=ComboFromRs(
		$rsDiv,
		'DivId',
		'DivId',
		0,
		(!empty($_REQUEST['findDiv']) ? $_REQUEST['findDiv'] : ''),
		array('','--'),
		'findDiv',
		'findDiv'
	);

	// Class Selection
	$rsCl=safe_r_sql("SELECT ClId FROM Classes WHERE ClTournament=" . StrSafe_DB($_SESSION['TourId']) . " ORDER BY ClViewOrder ASC");

	$comboCl=ComboFromRs(
		$rsCl,
		'ClId',
		'ClId',
		0,
		null,
		array('','--'),
		'd_e_EnClass_',
		'd_e_EnClass_',
		array(
			'onchange'=>'CheckCtrlCode(this);'
		)
	);

	safe_data_seek($rsCl,0);
	$comboAgeCl=ComboFromRs(
		$rsCl,
		'ClId',
		'ClId',
		0,
		null,
		array('','--'),
		'd_e_EnAgeClass_',
		'd_e_EnAgeClass_',
		array(
			'onchange'=>'CheckCtrlCode(this);',
//			'onblur'=>'SelectAgeClass();',
//			'onfocus'=>'GetClassesByGender();'
		)
	);

	safe_data_seek($rsCl,0);
	$comboFindCl=ComboFromRs(
		$rsCl,
		'ClId',
		'ClId',
		0,
		(!empty($_REQUEST['findCl']) ? $_REQUEST['findCl'] : ''),
		array('','--'),
		'findCl',
		'findCl'
	);


	$comboSubCl=ComboFromRs(
		safe_r_sql("
			SELECT
				ScId
			FROM
				SubClass
			WHERE
				ScTournament=" . StrSafe_DB($_SESSION['TourId']) . "
			ORDER BY
				ScViewOrder ASC
		"),
		'ScId',
		'ScId',
		0,
		null,
		array('','--'),
		'd_e_EnSubClass_',
		'd_e_EnSubClass_'
	);

	$comboFindSubCl=ComboFromRs(
		safe_r_sql("
			SELECT
				ScId
			FROM
				SubClass
			WHERE
				ScTournament=" . StrSafe_DB($_SESSION['TourId']) . "
			ORDER BY
				ScViewOrder ASC
		"),
		'ScId',
		'ScId',
		0,
		(!empty($_REQUEST['findSubCl']) ? $_REQUEST['findSubCl'] : ''),
		array('','--'),
		'findSubCl',
		'findSubCl'
	);

	$comboTf=ComboFromRs(
		array(
			array('key'=>0,'descr'=>'--')
		),
		'key',
		'descr',
		1,
		null,
		null,
		'd_e_EnTargetFace_',
		'd_e_EnTargetFace_'
	);

	$combos=array();

	$combos['indcl']=array(
		'descr'=>get_text('IndClEvent', 'Tournament'),
		'combo'=> ComboFromRs(
			array(
				array('key'=>'0','descr'=>get_text('No')),
				array('key'=>'1','descr'=>get_text('Yes')),
			),
			'key',
			'descr',
			1,
			null,
			null,
			'd_e_EnIndClEvent_',
			'd_e_EnIndClEvent_'
		)
	);

	$combos['teamcl']=array(
		'descr'=>get_text('TeamClEvent', 'Tournament'),
		'combo'=>ComboFromRs(
			array(
				array('key'=>'0','descr'=>get_text('No')),
				array('key'=>'1','descr'=>get_text('Yes')),
			),
			'key',
			'descr',
			1,
			null,
			null,
			'd_e_EnTeamClEvent_',
			'd_e_EnTeamClEvent_'
		)
	);

	$combos['indfin']=array(
		'descr'=>get_text('IndFinEvent', 'Tournament'),
		'combo'=>ComboFromRs(
			array(
				array('key'=>'0','descr'=>get_text('No')),
				array('key'=>'1','descr'=>get_text('Yes')),
			),
			'key',
			'descr',
			1,
			null,
			null,
			'd_e_EnIndFEvent_',
			'd_e_EnIndFEvent_'
		)
	);

	$combos['teamfin']=array(
		'descr'=>get_text('TeamFinEvent', 'Tournament'),
		'combo'=>ComboFromRs(
			array(
				array('key'=>'0','descr'=>get_text('No')),
				array('key'=>'1','descr'=>get_text('Yes')),
			),
			'key',
			'descr',
			1,
			null,
			null,
			'd_e_EnTeamFEvent_',
			'd_e_EnTeamFEvent_'
		)
	);

	$combos['mixteamfin']=array(
		'descr'=>get_text('MixedTeamFinEvent', 'Tournament'),
		'combo'=>ComboFromRs(
			array(
				array('key'=>'0','descr'=>get_text('No')),
				array('key'=>'1','descr'=>get_text('Yes')),
			),
			'key',
			'descr',
			1,
			null,
			null,
			'd_e_EnTeamMixEvent_',
			'd_e_EnTeamMixEvent_'
		)
	);

	$combos['wc']=array(
		'descr'=>get_text('WheelChair', 'Tournament'),
		'combo'=>ComboFromRs(
			array(
				array('key'=>'0','descr'=>get_text('No')),
				array('key'=>'1','descr'=>get_text('Yes')),
			),
			'key',
			'descr',
			1,
			null,
			null,
			'd_e_EnWChair_',
			'd_e_EnWChair_'
		)
	);

	$combos['double']=array(
		'descr'=>get_text('DoubleSpace', 'Tournament'),
		'combo'=>ComboFromRs(
			array(
				array('key'=>'0','descr'=>get_text('No')),
				array('key'=>'1','descr'=>get_text('Yes')),
			),
			'key',
			'descr',
			1,
			null,
			null,
			'd_e_EnDoubleSpace_',
			'd_e_EnDoubleSpace_'
		)
	);

// per i reset js delle combo
	list($allDivs,$allAgeCls,$allCls)=getAllDivCl();

//	print'<pre>';
//	print_r($allDivs);
//	print_r($allAgeCls);
//	print_r($allCls);
//	print'</pre>';
//	exit;

	$JS_SCRIPT=array(
		getTargetsScript(),
		($record!==null ? phpVars2js(array('record'=>$record)) : ''),
		phpVars2js(array(
			'allDivs'=>$allDivs,
			'allCls'=>$allCls,
			'allAgeCls'=>$allAgeCls
		)),
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ajax/ObjXMLHttpRequest.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/Fun_JS.inc.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/jquery-3.2.1.min.js"></script>',
		'<script type="text/javascript" src="Fun_AJAX_PopEdit.js"></script>',
		'<script type="text/javascript">
			function save(c)
			{
				if (document.getElementById(\'d_e_EnCode_\').value.length>0 && !CtrlCode_Error && !TargetNo_Error && !SubTeam_Error)
				{
					document.getElementById(\'d_e_EnAgeClass_\').disabled=false;
					document.getElementById(\'Command\').value=c;
					document.Frm.submit();
				}
			}

			function hideFinder()
			{
				var finder=document.getElementById(\'finder\');
				if (finder.style.display==\'none\')
				{
					finder.style.display=\'block\';
				}
				else
				{
					finder.style.display=\'none\';
				}
			}
		</script>'
	);

	if ($reload==1)
	{
		$ONLOAD=' onLoad="javascript:opener.window.location=opener.window.location;';
		if(!empty($_REQUEST['findDiv'])
			or !empty($_REQUEST['findCl'])
			or !empty($_REQUEST['findSubCl'])
			or !empty($_REQUEST['findCode'])
			or !empty($_REQUEST['findAth'])
			or !empty($_REQUEST['findCountry'])) {
				$ONLOAD.= 'setTimeout(\'FindArchers()\', 50);';
			}
			$ONLOAD.= '"';
	}
	elseif ($reload==2)
	{
		$ONLOAD=' onLoad="javascript:opener.window.location=opener.window.location; window.close();"';
	}

	$comboLup='<select name="LupSelect" id="LupSelect"><option></option>';
	$t=safe_r_sql("select LueIocCode, ToId is not null as LueDefault from (select distinct LueIocCode from LookUpEntries) lue left join Tournament on LueIocCode=ToIocCode and ToId={$_SESSION['TourId']} order by LueIocCode");
	while($u=safe_fetch($t)) {
		$selected='';
		if($id) {
			if($record['ioccode']==$u->LueIocCode) {
				$selected=' selected="selected"';
			}
		} else {
			if($u->LueDefault) {
				$selected=' selected="selected"';
			}
		}
		$comboLup.= '<option value="' . $u->LueIocCode . '"'.$selected.'>'.($u->LueIocCode ? get_text('LUE-'.$u->LueIocCode, 'Tournament') : '').'</option>';
	}
	$comboLup.='</select>';

	include('Common/Templates/head-popup.php');
?>

<form name="Frm" method=post action="<?php print $_SERVER['PHP_SELF'];?>">
	<input type="hidden" name="id" id="id" value="<?php print $id;?>">
	<input type="hidden" name="d_e_EnId_" id="d_e_EnId_" value="0">
	<input type="hidden" name="CanComplete_" id="CanComplete_" value="0">
	<input type="hidden" name="d_e_EnCountry_" id="d_e_EnCountry_" value="0">
	<input type="hidden" name="d_e_EnCountry2_" id="d_e_EnCountry2_" value="0">
	<input type="hidden" name="d_e_EnCountry3_" id="d_e_EnCountry3_" value="0">
	<input type="hidden" name="Command" id="Command" value=""/>

	<?php if ($ses!==null && $tar!==null) {?>
		<input type="hidden" name="ses" id="ses" value="<?php print $ses;?>">
		<input type="hidden" name="tar" id="tar" value="<?php print $tar;?>">
	<?php }?>


	<table class="Tabella" id="idAthList">
		<tr>
			<th class="TitleLeft"><?php print get_text('Status','Tournament');?></th>
			<td><?php print $comboStatus;?></td>
			<th class="TitleLeft"><?php print get_text('Div');?></th>
			<td><?php  print $comboDiv;?></td>
		</tr>
		<tr>
			<th class="TitleLeft"><?php print get_text('Session');?></th>
			<td><?php print $comboSes;?></td>
			<th class="TitleLeft"><?php print get_text('AgeCl');?></th>
			<td><?php  print $comboAgeCl;?></td>
		</tr>
		<tr>
			<th class="TitleLeft"><?php print get_text('Target');?></th>
			<td><input type="text" maxlength="<?php print (TargetNoPadding+1); ?>" name="d_q_QuTargetNo_" id="d_q_QuTargetNo_" value="" onblur="CheckTargetNo();"/></td>
			<th class="TitleLeft"><?php print get_text('Cl');?></th>
			<td><?php  print $comboCl;?></td>
		</tr>
		<tr>
			<th class="TitleLeft"><?php print get_text('LookupTable','Tournament');?></th>
			<td><?php print $comboLup;?></td>
			<th class="TitleLeft"><?php print get_text('SubCl','Tournament');?></th>
			<td><?php  print $comboSubCl;?></td>
		</tr>
		<tr>
			<th class="TitleLeft"><?php print get_text('Code','Tournament');?></th>
			<td>
				<input type="text" maxlength="25" name="d_e_EnCode_" id="d_e_EnCode_" value="" onblur="SetCompleteFlag();" onfocus="SetCompleteFlag();" onkeyup="CercaMatr(true);"/>
				<?php /* ?>
				&nbsp;
				<a class="Link" href="javascript:hideFinder();"><?php print get_text('Search','Tournament');?></a>
				<?php */?>
			</td>
			<th class="TitleLeft"><?php print get_text('TargetType');?></th>
			<td><?php  print $comboTf;?></td>
		</tr>
		<tr>
			<th class="TitleLeft"><?php print get_text('FamilyName','Tournament');?></th>
			<td colspan="3"><input type="text" maxlength="20" size="20" name="d_e_EnFirstName_" id="d_e_EnFirstName_" value=""/></td>
		</tr>
		<tr>
			<th class="TitleLeft"><?php print get_text('Name','Tournament');?></th>
			<td><input type="text" maxlength="20" size="20" name="d_e_EnName_" id="d_e_EnName_" value=""/></td>
			<th class="TitleLeft"><?php echo $combos['indcl']['descr']; ?></th>
			<td><?php echo $combos['indcl']['combo']; ?></td>
		</tr>
		<tr>
			<th class="TitleLeft"><?php print get_text('DOB','Tournament');?></th>
			<td><input type="text" maxlength="16" name="d_e_EnCtrlCode_" id="d_e_EnCtrlCode_" value="" onblur="CheckCtrlCode(this);"/></td>
			<th class="TitleLeft"><?php echo $combos['teamcl']['descr']; ?></th>
			<td><?php echo $combos['teamcl']['combo']; ?></td>
		</tr>
		<tr>
			<th class="TitleLeft"><?php print get_text('Sex','Tournament');?></th>
			<td><?php print $comboSex;?></td>
			<th class="TitleLeft"><?php echo $combos['indfin']['descr']; ?></th>
			<td><?php echo $combos['indfin']['combo']; ?></td>
		</tr>
		<tr>
			<th class="TitleLeft"><?php print get_text('Country');?></th>
			<td><input type="text" maxlength="10" name="d_c_CoCode_" id="d_c_CoCode_" value="" onkeyup="SelectCountryCode('');"/></td>
			<th class="TitleLeft"><?php echo $combos['teamfin']['descr']; ?></th>
			<td><?php echo $combos['teamfin']['combo']; ?></td>
		</tr>
		<tr>
			<th class="TitleLeft"><?php print get_text('NationShort','Tournament');?></th>
			<td><input type="text" maxlength="30" size="50" name="d_c_CoName_" id="d_c_CoName_" value=""/></td>
			<th class="TitleLeft"><?php echo $combos['mixteamfin']['descr']; ?></th>
			<td><?php echo $combos['mixteamfin']['combo']; ?></td>
		</tr>

		<tr>
			<th class="TitleLeft"><?php print get_text('PartialTeam');?></th>
			<td colspan="3"><input type="text" maxlength="3" name="d_e_EnSubTeam_" id="d_e_EnSubTeam_" value="" onblur="CheckSubTeam();" /></td>
		</tr>

		<tr>
			<th class="TitleLeft"><?php print get_text('Country') . ' (2)';?></th>
			<td><input type="text" maxlength="10" name="d_c_CoCode2_" id="d_c_CoCode2_" value=""  onkeyup="SelectCountryCode('2');"/></td>
			<th class="TitleLeft"><?php echo $combos['wc']['descr']; ?></th>
			<td><?php echo $combos['wc']['combo']; ?></td>
		</tr>
		<tr>
			<th class="TitleLeft"><?php print get_text('NationShort','Tournament') . ' (2)';?></th>
			<td><input type="text" maxlength="30" size="50" name="d_c_CoName2_" id="d_c_CoName2_" value=""/></td>
			<th class="TitleLeft"><?php echo $combos['double']['descr']; ?></th>
			<td><?php echo $combos['double']['combo']; ?></td>
		</tr>

		<tr>
			<th class="TitleLeft"><?php print get_text('Country') . ' (3)';?></th>
			<td colspan="3"><input type="text" maxlength="10" name="d_c_CoCode3_" id="d_c_CoCode3_" value=""  onkeyup="SelectCountryCode('3');"/></td>
		</tr>
		<tr>
			<th class="TitleLeft"><?php print get_text('NationShort','Tournament') . ' (3)';?></th>
			<td><input type="text" maxlength="30" size="50" name="d_c_CoName3_" id="d_c_CoName3_" value=""/></td>
			<th class="TitleLeft"><?php echo get_text('Email','Tournament'); ?></th>
			<td><input type="email" name="d_ed_EdEmail_" id="d_ed_EdEmail_" value=""/></td>
		</tr>

		<tr><td class="Center" colspan="4">
			<input type="button" value="<?php print get_text('CmdSave');?>" onclick="save('SAVE');"/>
			&nbsp;&nbsp;
			<input type="button" value="<?php print get_text('CmdSaveContinue');?>" onclick="save('SAVE_CONTINUE');"/>
			&nbsp;&nbsp;
			<input type="button" value="<?php print get_text('Close');?>" onclick="window.close();"/>
		</td></tr>
	</table>

<div id="finder"> <!-- style="display:none;"-->
		<table class="Tabella">
			<tr>
				<th><?php print get_text('Code','Tournament');?></th>
				<th><?php print get_text('Athlete');?></th>
				<th><?php print get_text('Country');?></th>
				<th><?php print get_text('Div');?></th>
				<th><?php print get_text('Cl');?></th>
				<th><?php print get_text('SubCl','Tournament');?></th>
				<th>&nbsp;</th>
			</tr>
			<tr>
				<td class="Center" style="width:10%;"><input type="text" size="6" id="findCode" name="findCode" value="<?php echo !empty($_REQUEST['findCode']) ? $_REQUEST['findCode'] : ''; ?>" /></td>
				<td class="Center" style="width:25%;"><input type="text" size="25" id="findAth" name="findAth" value="<?php echo !empty($_REQUEST['findAth']) ? $_REQUEST['findAth'] : ''; ?>" /></td>
				<td class="Center" style="width:25%;"><input type="text" size="25" id="findCountry" name="findCountry" value="<?php echo !empty($_REQUEST['findCountry']) ? $_REQUEST['findCountry'] : ''; ?>" /></td>
				<td class="Center" style="width:10%;"><?php print $comboFindDiv;?></td>
				<td class="Center" style="width:10%;"><?php print $comboFindCl;?></td>
				<td class="Center" style="width:10%;"><?php print $comboFindSubCl;?></td>
				<td class="Center" style="width:10%;"><input type="button" id="btnSearch" value="<?php print get_text('Search','Tournament');?>" onclick="FindArchers();" /></td>
			</tr>
		</table>
	<div id="results">
	</div>
</div>
</form>

<script type="text/javascript">loadRecord(record);</script>

<?php

include('Common/Templates/tail-popup.php');

?>
