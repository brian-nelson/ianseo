<?php

$JSON=array('error' => 1, 'reload' => false);

// ACL and other checks are made in the config
require_once('./IdCardEdit-config.php');


if(!empty($_REQUEST['IdCardsSettings'])) {
	$sql="IcTournament={$_SESSION['TourId']}, IcType='$CardType', IcNumber=$CardNumber, IcSettings=".StrSafe_DB(serialize($_REQUEST["IdCardsSettings"]));
	safe_w_sql("INSERT INTO IdCards set $sql on duplicate key update $sql");
	$JSON['error']=0;
	JsonOut($JSON);
}

if(!empty($_REQUEST["Content"])) {
	foreach ($_REQUEST["Content"] as $IdOrder => $Options) {
		$SQL = array();
		if (!empty($Options['File'])) {
			$SQL[] = 'IceMimeType=' . StrSafe_DB($Options['File']);
		}
		if (isset($Options['Text'])) {
			$SQL[] = 'IceContent=' . StrSafe_DB($Options['Text']);
		}
		if (!empty($Options['Event'])) {
			$SQL[] = 'IceContent=' . StrSafe_DB($Options['Event']);
		}
        if (!empty($Options['Ranking'])) {
            $SQL[] = 'IceContent=' . StrSafe_DB($Options['Ranking']);
        }
        if (!empty($Options['FinalRanking'])) {
            $SQL[] = 'IceContent=' . StrSafe_DB($Options['FinalRanking']);
        }
		if (!empty($Options['Category'])) {
			$SQL[] = 'IceContent=' . StrSafe_DB($Options['Category']);
		}
		if (!empty($Options['Athlete'])) {
			$SQL[] = 'IceContent=' . StrSafe_DB($Options['Athlete']);
		}
		if (!empty($Options['Club'])) {
			$SQL[] = 'IceContent=' . StrSafe_DB($Options['Club']);
		}
		if (!empty($Options['AthQrCode'])) {
			$SQL[] = 'IceContent=' . StrSafe_DB($Options['AthQrCode']);
		}
		if (!empty($Options['AthBarCode'])) {
			$SQL[] = 'IceContent=' . StrSafe_DB($Options['AthBarCode']);
		}
		if (!empty($Options['TgtSequence'])) {
			$SQL[] = 'IceContent=' . StrSafe_DB($Options['TgtSequence']);
		}
		if (!empty($Options['Options'])) {
			if ($Options['Type'] == 'Picture') {
				if ($Options['Options']['W'] or $Options['Options']['H']) {
					if (empty($Options['Options']['W'])) {
						$Options['Options']['W'] = intval($Options['Options']['H'] * MAX_WIDTH / MAX_HEIGHT);
					} else {
						$Options['Options']['H'] = intval($Options['Options']['W'] * MAX_HEIGHT / MAX_WIDTH);
					}
				} else {
					$Options['Options']['W'] = 30;
					$Options['Options']['H'] = 40;
				}
			}
			$SQL[] = 'IceOptions=' . StrSafe_DB(serialize($Options['Options']));
		}
		safe_w_sql("update IdCardElements set " . implode(',', $SQL) . " where IceTournament={$_SESSION['TourId']} and IceCardType='$CardType' and IceCardNumber=$CardNumber and IceOrder=$IdOrder");

		$JSON['error']=0;

		if (empty($Options['Order'])) {
			$JSON['reload']=true;
		} else {
			if($IdOrder != intval($Options['Order'])) {
				$JSON['reload']=true;
				switchOrder($IdOrder, intval($Options['Order']), $CardType, $CardNumber);
			}
		}
	}
}

JsonOut($JSON);
