<?php
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.php');
	require_once(dirname(dirname(dirname(__FILE__))) . '/Fun_Final.local.inc.php');
	require_once(dirname(dirname(__FILE__)) . '/functions/var.inc.php');
	require_once('Common/Lib/ArrTargets.inc.php');

	$xml='';
	$error=0;

	function safe(&$item)
	{
		$item=StrSafe_DB($item);
	}


	$schedule=(isset($_REQUEST['schedule']) && preg_match('/^[0-9]{4}\-[0-9]{2}\-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/',$_REQUEST['schedule']) ? $_REQUEST['schedule'] : null);
	$events=(isset($_REQUEST['events']) && is_array($_REQUEST['events'])  ? $_REQUEST['events'] : null);
	$team=(isset($_REQUEST['team']) ? $_REQUEST['team'] : null);
	$serverDate=(isset($_REQUEST['serverDate']) ? $_REQUEST['serverDate'] : null);
	$parameters=isset($_REQUEST['parameters']) ? $_REQUEST['parameters'] : null;

	if (!CheckTourSession() || is_null($schedule) || is_null($events) || is_null($team) || is_null($serverDate))
		exit;

// se ho i parametri li salvo
	if (!is_null($parameters) && strlen($parameters)>0)
		SetParameter('SpkTimer',$parameters);

	$query="SELECT UNIX_TIMESTAMP(NOW()) AS serverDate ";
	$rs=safe_r_sql($query);

	$row=safe_fetch($rs);
	$xml.='<serverDate>' . $row->serverDate . '</serverDate>' . "\n";

	$reset=0;

	if (count($events)>0 && $events[0]!='')
	{
		array_walk($events,'safe');

		$otherWhere= "
			AND fs1.FSTeamEvent=" . StrSafe_DB($team) . " AND (fs1.FSEvent IN(" . implode(',',$events) . ") OR fs2.FSEvent IN(". implode(',',$events) .") )
			AND (CONCAT(fs1.FSScheduledDate,' ',fs1.FSScheduledTime)=" . StrSafe_DB($schedule) . " OR CONCAT(fs2.FSScheduledDate,' ',fs2.FSScheduledTime)=" . StrSafe_DB($schedule) . ")
		";

	/*
	 * cerco se ci sono scontri aggiornati rispetto alla serverDate passata, se sì ritorno tutto altrimenti nulla
	 */
		$otherWhere2='';
		if ($team==0)
		{
			$otherWhere2=$otherWhere. "AND UNIX_TIMESTAMP(IF(f1.FinDateTime>=f2.FinDateTime,f1.FinDateTime,f2.FinDateTime))>" . StrSafe_DB($serverDate) . " ";
		}
		else
		{
			$otherWhere2=$otherWhere. "AND UNIX_TIMESTAMP(IF(tf1.TfDateTime>=tf2.TfDateTime,tf1.TfDateTime,tf2.TfDateTime))>" . StrSafe_DB($serverDate) . " ";
		}

		$orderBy=" fs1.FSTarget = '', fs1.FSTarget ASC,fs2.FSTarget ASC ";
		$rs=GetFinMatches_sql($otherWhere2,$team,$orderBy,false);
		//exit;
		if ($rs)
		{
			if (safe_num_rows($rs)>0)
			{
				$rs=GetFinMatches_sql($otherWhere,$team,$orderBy,false);

				if ($rs && safe_num_rows($rs)>0)
				{
					$points4win=array();
					$max=0;

				// primo giro x inizializzare i vettori accessori
					while ($myRow=safe_fetch($rs))
					{
						if ($myRow->matchMode==1)
						{
							$obj=getEventArrowsParams($myRow->event,$myRow->phase,$team);

							$points4win[$myRow->event]=$obj->winAt;

						// massimo delle somme dei punti win + punti loser
							$sum=$myRow->setScore1+$myRow->setScore2;
							if ($sum>$max)
								$max=$sum;
						}
					}

					/*print '<pre>';
					print_r($points4win);
					print '</pre>';exit;*/

					safe_data_seek($rs,0);	// resetto il puntatore

					$id=0;	// id fittizio
					while ($myRow=safe_fetch($rs))
					{
						$target=$myRow->target1;
						if ($myRow->target2!=$myRow->target1)
							$target.=' - ' . $myRow->target2;

						$score1=$myRow->score1;
						$score2=$myRow->score2;

						if ($myRow->matchMode==1)
						{
							$score1=$myRow->setScore1;
							$score2=$myRow->setScore2;
						}

						$score=$score1 . ' - ' . $score2;

						$setPoints1='';
						$setPoints2='';
						if ($myRow->tie1==2 && $myRow->tie2!=2)	// passa 1
						{
							$setPoints1=get_text('Bye');
						}
						elseif ($myRow->tie1!=2 && $myRow->tie2==2)	// passa 2
						{
							$setPoints2=get_text('Bye');
						}
						else
						{
							list($setPoints1,$setPoints2)=purgeSetPoints($myRow->setPoints1,$myRow->setPoints2);
						}


					// le frecce di tiebreak

						for ($index=1;$index<=2;++$index)
						{
							$arrowstring=$myRow->{'tiebreak'.$index};
							if (trim($arrowstring)!='')
							{
								//print 'pp';
								$tmp=array();
								for ($i=0;$i<strlen($arrowstring);++$i)
								{
									$tmp[]=DecodeFromLetter($arrowstring[$i]);
								}

								${'setPoints'.$index}.=' ' . implode(' ',$tmp);
							}
						}

					/*
					 * 0 => il match no è finito
					 * 1 => il match è finito prima
					 * 2 => il match è finito ora
					 */
						$finished=0;

					/*
					 * <r> stabilisce lo stato di lettura della riga.
					 * Normalmente è zero però il suo valore diventa 1 se:
					 * 1) il match è finito in una volee precedente all'attuale check.
					 * 2) esiste nella request la var corrispondente e vale 1
					 * Questo mi serve per inizializzare la colonna read dello store.
					 *
					 */

						$r=0;

						if ($myRow->matchMode==1)
						{
							$finished=isFinished($myRow,$points4win,$max);
						}

						if ($finished==1)
							$r=1;


					// controllo la request
						if (isset($_REQUEST['r_' . $id]) && preg_match('/^[0-1]{1}$/',$_REQUEST['r_' . $id]) && $myRow->lastUpdate<$serverDate)
							$r=$_REQUEST['r_' . $id];

						$xml.='<m>'
							.'<id>' . $id . '</id>'
							.'<f>' . $finished . '</f>'
							.'<r>' . $r . '</r>'
							.'<ev>' . $myRow->event. '</ev>'
							.'<evn><![CDATA[' . $myRow->eventName. ']]></evn>'
							.'<t>' . $target . '</t>'
							.'<n1><![CDATA[' . $myRow->name1 . ']]></n1>'
							.'<cn1><![CDATA[' . $myRow->countryName1 . ']]></cn1>'
							.'<n2><![CDATA[' . $myRow->name2 . ']]></n2>'
							.'<cn2><![CDATA[' . $myRow->countryName2 . ']]></cn2>'
							.'<sp1><![CDATA[' . $setPoints1 . ']]></sp1>'
							.'<sp2><![CDATA[' . $setPoints2 . ']]></sp2>'
							.'<s>' . $score . '</s>'
							.'<lu>' . $myRow->lastUpdate . '</lu>'
							.'</m>';

						++$id;
					}
				}
			}
		}
		else
		{
			$error=1;
		}
	}
	else
	{
		$reset=1;
	}


	header('Content-Type: text/xml');

	print '<response>' . "\n";

		print '<error>' . $error . '</error>' . "\n";
		print '<reset>' . $reset . '</reset>' . "\n";

		print $xml;

	print '</response>' . "\n";