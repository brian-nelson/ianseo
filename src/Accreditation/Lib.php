<?php

function SetAccreditation($Id, $SetRap=0, $return='RicaricaOpener', $TourId=0, $AccOp=0) {
	$RicaricaOpener=false;
	if(!$TourId)
		$TourId=$_SESSION['TourId'];
	if(!$AccOp)
		$AccOp=$_SESSION['AccOp'];
	/*
	 * Devo prevenire l'insert se l'id è in stato 7.
	* Per farlo cerco lo stato del tizio.
	* Se è 7 vuol dire che uno ha cliccato sul bottone dopo aver aperto il popup e io non scrivo in db
	*/
	$Select = "SELECT EnId FROM Entries
		WHERE EnId="  . StrSafe_DB($Id) . " AND EnTournament=$TourId AND EnStatus='7' ";
	$Rs=safe_r_sql($Select);
	//TODO Patchare la query per supportare bene IpV6
	if (safe_num_rows($Rs)==0) {
		$Insert = "INSERT INTO AccEntries
			(AEId,AEOperation,AETournament,AEWhen,AEFromIp,AERapp)
			VALUES(
				$Id,"
				. StrSafe_DB($AccOp) . ","
				. StrSafe_DB($TourId) . ","
				. StrSafe_DB(date('Y-m-d H:i')) . ","
				. "INET_ATON('" . ($_SERVER['REMOTE_ADDR']!='::1' ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1') . "'), "
				. StrSafe_DB($SetRap)
			. ") ON DUPLICATE KEY UPDATE "
				. "AEWhen=" . StrSafe_DB(date('Y-m-d H:i')) . ","
				. "AEFromIp=INET_ATON('" . ($_SERVER['REMOTE_ADDR']!='::1' ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1') . "') ";
		$RsIns=safe_w_sql($Insert);
		$RicaricaOpener=($return=='RicaricaOpener' ? true : (safe_w_affected_rows() ? 'AccreditationOK' : 'AccreditationTwice'));
	}
	return $RicaricaOpener;
}

function getAccrQuery($Id=0) {
	$Where=array();
	if($_SESSION['chk_Turni']) {
		$Where[]="QuSession IN (".implode(',', StrSafe_DB($_SESSION['chk_Turni'])).")";
	}
	if($Id) {
		$Where[]="EnId=$Id";
	} else {
		if(!empty($_REQUEST['txt_Cognome'])) {
			$Where[]="concat(EnFirstName,' ',EnName, ' ', EnCode) LIKE '%" . $_REQUEST['txt_Cognome'] . "%'";
		}
		if(!empty($_REQUEST['txt_Societa'])) {
			$Where[]="(CoCode LIKE '%" . $_REQUEST['txt_Societa'] . "%' OR CoName LIKE '%" . $_REQUEST['txt_Societa'] . "%')";
		}
		if(!empty($_REQUEST['RemoveAcc'])) {
			if($_SESSION['AccOp'] == -1) {
				$Where[]="PhEnId IS NULL ";
			} else {
				$Where[]="m.AEOperation IS NULL ";
			}
		}
	}
	return "Select EnId,EnTournament,EnDivision,EnClass,EnCountry,CoCode,CoName,EnCode,EnName,EnFirstName,EnStatus,
			EnIndClEvent,EnTeamClEvent,EnIndFEvent,EnTeamFEvent,EnTeamMixEvent,EnPays,QuSession,SUBSTRING(QuTargetNo,2) As TargetNo,
			m.AEOperation, PhEnId
			, ".($_SESSION['chk_Photo'] ? 'PhEnId is not null and PhPhoto!=""' : '1')." as HasPhoto
			, ".($_SESSION['chk_Paid']==1 ? 'p.AEId is not null' : '1')." as HasPaid
			, ".($_SESSION['chk_Accredited']==1 ? 'a.AEId is not null' : '1')." as IsAccredited
		FROM Entries
		LEFT JOIN Countries ON EnCountry=CoId
		INNER JOIN Qualifications ON EnId=QuId AND EnTournament=" . StrSafe_DB($_SESSION['TourId']) . "
		LEFT JOIN AccEntries m ON EnId=m.AEId AND EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND m.AEOperation=" . StrSafe_DB($_SESSION['AccOp']) . "
		LEFT JOIN AccEntries p ON EnId=p.AEId AND EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND p.AEOperation=3
		LEFT JOIN AccEntries a ON EnId=a.AEId AND EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND a.AEOperation=1
		LEFT JOIN AccOperationType ON m.AEOperation=AOTId
		LEFT JOIN Photos ON EnId=PhEnId
		WHERE ".implode(' AND ', $Where). "
		ORDER BY HasPhoto desc, HasPaid desc, IsAccredited desc, QuSession ASC, TargetNo ASC, EnFirstName ASC , EnName ASC , CoCode ASC, EnCode ";
}

function CheckAccreditationCode($EnCode, $Options=array(), $OnlyTour=false) {
	// if EnCode starts with '$' then it is an EnId
	if (substr($EnCode,0,1)=='$') {
		return intval(substr($EnCode,1));
	}

	if(empty($Options)) {
		$Options=array($_SESSION['TourId'] => array());
	}


	$WAbib=preg_split("/['-]/", $EnCode);

	$tmp=stripslashes($EnCode);

	if (is_numeric($tmp)) {
		$bib=ltrim($tmp,'0');
	} else {
		$bib = $tmp;
	}

	$ret=array();
	foreach($Options as $TourId => $Sessions) {
		if($Sessions and !$OnlyTour) {
			$Where="EnTournament=$TourId and QuSession in (" . implode(',', $Sessions) . ")";
		} else {
			$Where="EnTournament=$TourId";
			if($Sessions) $GLOBALS['SESSION_TRICK']=$Sessions;
		}

		if($ID=CheckBibIsOk($bib, $Where, $WAbib)) {
			return $ID;
		}

		// Normal check failed, check against all the accreditation QRcodes for this competition...
		$q=safe_r_sql("select * from IdCardElements where IceType IN ('AthQrCode','AthBarCode') and IceTournament=$TourId");
		while($r=safe_fetch($q)) {
			$CardsMatched=getModuleParameter('Accreditation', 'Matches-'.$r->IceCardType.'-'.$r->IceCardNumber, '', $TourId, true);

            $RegExp = preg_quote('{ENCODE}-{DIVISION}-{CLASS}', '/');
            if ($r->IceContent != '') {
                $RegExp = preg_quote($r->IceContent, '/');
            }

			$iceContent=getIceRegExpMatches($r->IceContent);

			$RegExp=str_replace(array('\\{ENCODE\\}', '\\{COUNTRY\\}','\\{DIVISION\\}','\\{CLASS\\}', '\\{TOURNAMENT\\}'), '(.+?)', $RegExp);
			unset($Matches);

			preg_match('/^'.$RegExp.'$/', $bib, $Matches);
			$BibCode=($iceContent['encode']!=-1 && isset($Matches[$iceContent['encode']]) ? $Matches[$iceContent['encode']] : 0);
			$Division='';
			$Class='';
			$Country='';
			$ToCode='';

			if($BibCode) {
				$Continue=true;
				if($iceContent['country']!=-1) {
					if(isset($Matches[$iceContent['country']])) {
						$Country=$Matches[$iceContent['country']];
					} else {
						$Continue=false;
					}
				}
				if($iceContent['division']!=-1) {
					if(isset($Matches[$iceContent['division']])) {
						$Division=$Matches[$iceContent['division']];
					} else {
						$Continue=false;
					}
				}
				if($iceContent['class']!=-1) {
					if(isset($Matches[$iceContent['class']])) {
						$Class=$Matches[$iceContent['class']];
					} else {
						$Continue=false;
					}
				}
				if($iceContent['tocode']!=-1) {
					if(isset($Matches[$iceContent['tocode']])) {
						$ToCode=$Matches[$iceContent['tocode']];
					} else {
						$Continue=false;
					}
				}
				$CheckCode=str_replace(array('{ENCODE}', '{COUNTRY}','{DIVISION}','{CLASS}','{TOURNAMENT}'), array($BibCode, $Country, $Division, $Class, $ToCode), $r->IceContent);

					// check the cardtype matches the requested bib
				if($Continue) {
					$sql="select * from Entries where EnCode='$BibCode' and EnTournament=$TourId ".($CardsMatched ? "and concat(EnDivision,EnClass) in ('".str_replace(",", "','", $CardsMatched)."')" : "");
					$d=safe_r_SQL($sql);

					if(safe_num_rows($d) and $CheckCode==$EnCode and $ID=CheckBibIsOk($BibCode, $Where, $WAbib, $Country, $Division)) {
						if(!in_array($ID, $ret)) $ret[array_search($TourId, array_keys($Options))]= $ID;
					}
				}
			}
		}

	}

	if($ret) {
		foreach($ret as $id) {
			return $id;
		}
	}

	// fallback!
	return 0;
}

function getIceRegExpMatches($IceContent) {
	unset($Elements);
	preg_match_all('/(\\{[A-Z]+\\})/sim', $IceContent, $Elements);

	$ret=array(
		'encode' => -1,
		'country' => -1,
		'division' => -1,
		'class' => -1,
		'tocode' => -1,
	);
	foreach($Elements[0] as $k => $v) {
		switch($v) {
			case '{ENCODE}':
				$ret['encode']=$k+1;
				break;
			case '{COUNTRY}':
				$ret['country']=$k+1;
				break;
			case '{DIVISION}':
				$ret['division']=$k+1;
				break;
			case '{CLASS}':
				$ret['class']=$k+1;
				break;
			case '{TOURNAMENT}':
				$ret['tocode']=$k+1;
				break;
		}
	}
	return $ret;
}

function CheckBibIsOk($Bib, $Where, $WAbib = null, $Country=null, $Division=null, $Class=null) {
	$Select = "SELECT EnId FROM Entries INNER JOIN Qualifications ON EnId=QuId inner join Countries on CoId=EnCountry WHERE EnCode=" . StrSafe_DB($Bib) . " AND $Where";
	if($Country) {
		$Select.=' and CoCode='. StrSafe_DB($Country);
	}
	if($Division) {
		$Select.=' and EnDivision='. StrSafe_DB($Division);
	}
	if($Class) {
		$Select.=' and EnClass='. StrSafe_DB($Class);
	}
	if(!empty($GLOBALS['SESSION_TRICK'])) {
		$tmp=array();
		foreach($GLOBALS['SESSION_TRICK'] as $s) {
			if($s[0]=='Q') {
				$tmp[]=substr($s,-1);
			}
		}
		if($tmp) {
			$Select.=' order by QuSession in ('.implode(',', $tmp).') desc';
		}

	}


	$RsSel =safe_r_sql($Select);

	if (safe_num_rows($RsSel)==1 or (safe_num_rows($RsSel) and !empty($GLOBALS['SESSION_TRICK']))) {
		// ok
		$row=safe_fetch($RsSel);
		 return $row->EnId;
	} elseif (safe_num_rows($RsSel)>1) {
		// needs to select which one...
		header('Location: SelArcher.php?bib=' . $Bib);
		exit;
	} elseif(!empty($WAbib) and count($WAbib)==3) {
		// coming from  WA bib?
		$Select = "SELECT EnId FROM Entries INNER JOIN Qualifications ON EnId=QuId 
			WHERE EnCode=" . StrSafe_DB($WAbib[0]) . " 
				AND $Where
                AND EnDivision=".StrSafe_DB($WAbib[1])." 
                AND EnClass=".StrSafe_DB($WAbib[2]);
		//print $Select;exit;
		$RsSel =safe_r_sql($Select);
		if (safe_num_rows($RsSel)==1) {
			// ok
			$row=safe_fetch($RsSel);
			return $row->EnId;
		}
	}
	return false;
}

function GateLog($EnId, $Status, $TourId, $Direction=1) {
	safe_w_SQL("insert into GateLog set 
		GLEntry=$EnId,
		GLDateTime=now(),
		GLIP=".StrSafe_DB($_SERVER['REMOTE_ADDR']).",
		GLDirection='$Direction',
		GLTournament=$TourId,
		GLStatus=$Status");
}

function GetGateAccess($EnId) {
	$q=safe_r_sql("select sum(GLDirection) as HowMany from GateLog where GLEntry=$EnId");
	if($r=safe_fetch($q)) {
		return $r->HowMany;
	}

	return 0;
}