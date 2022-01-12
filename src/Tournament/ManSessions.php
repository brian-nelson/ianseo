<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Lib/Fun_DateTime.inc.php');
	require_once('Common/Fun_Sessions.inc.php');
	require_once('Common/Fun_Various.inc.php');
	require_once('Tournament/Fun_Tournament.local.inc.php');
	require_once('Tournament/Fun_ManSessions.inc.php');
    checkACL(AclCompetition, AclReadWrite);

	if (defined('hideSchedulerAndAdvancedSession')) {
		header('location: ManSessions_kiss.php');
		exit;
	}

	if (!CheckTourSession())
	{
		print get_text('CrackError');
		exit;
	}

	$msg="";

	$tourId_safe= StrSafe_DB($_SESSION['TourId']);
	$command=isset($_REQUEST['Command']) ? $_REQUEST['Command'] : null;

// tipi sessione
	$sessionsTypes=GetSessionsTypes();
	if (!is_null($command) && !IsBlocked(BIT_BLOCK_TOURDATA))
	{
		if ($command=='SAVE')
		{
			$myRequest=array
			(
				'oldKey',
				'd_SesOrder',
				'd_SesType',
				'd_SesName',
				'd_SesDtStart',
				'd_SesDtEnd',
				'd_SesTar4Session',
				'd_SesAth4Target',
				'd_SesFirstTarget',
				'd_SesFollow',
				'd_SesOdfCode',
				'd_SesOdfPeriod',
				'd_SesOdfVenue',
				'd_SesOdfLocation',
			);

			$toSave=array();

			foreach ($myRequest as $v)
			{
				if (isset($_REQUEST[$v]))
				{
					$toSave[$v]=$_REQUEST[$v];
				}
				else
				{
					$toSave[$v]='';
				}
			}

			/*print '<pre>';
			print_r($toSave);
			print '</pre>';exit;*/

		// le var ci devono esser tutte

			if (!in_array(null,$toSave,true))
			{
			// il tipo deve essere valido
				if (in_array($toSave['d_SesType'],array_keys($sessionsTypes)))
				{
					$toSave_safe=array();
					foreach($toSave as $k=>$v)
						$toSave_safe[$k]=StrSafe_DB($v);

				// qui mi calcolo il nuovo order del tipo passato e poi magari lo uso
					$newOrder=calcNewOrderForType($_SESSION['TourId'],$toSave['d_SesType']);

				/*
				 * Se d_SesOrder=0 & oldKey='' vuol dire che è una insert pulita
				 * Se d_SesOrder!=0 & oldKey!='' & substr(oldKey,-1)==SesType vuol dire che è un'update pulita (no cambio del tipo)
				 * Se d_SesOrder!=0 & oldKey!='' & substr(oldKey,-1)!=SesType è un'update con cambio di tipo quindi si gestirà la delete del vecchio tipo
				 * Gli altri casi non hanno senso e sono errori
				 */
					if ($toSave['d_SesOrder']==0)
					{
						if ($toSave['oldKey']=='')		// insert pulita
						{
							$x=insertSession(
								$_SESSION['TourId'],
								$newOrder,
								$toSave['d_SesType'],
								$toSave['d_SesName'],
								$toSave['d_SesTar4Session'],
								$toSave['d_SesAth4Target'],
								$toSave['d_SesFirstTarget'],
								$toSave['d_SesFollow'],
								$toSave['d_SesDtStart'],
								$toSave['d_SesDtEnd'],
								$toSave['d_SesOdfCode'],
								$toSave['d_SesOdfPeriod'],
								$toSave['d_SesOdfVenue'],
								$toSave['d_SesOdfLocation']
							);

							if ($x!==true)
								$msg=$x;
						}
						else
						{
							$msg='_error_';
						}
					}
					else
					{
						if ($toSave['oldKey']!='')
						{
							$oldKeyType=substr($toSave['oldKey'],-1);

							if ($oldKeyType==$toSave['d_SesType'])	// update pulita
							{
								$x=updateSession(
									$_SESSION['TourId'],
									$toSave['d_SesOrder'],
									$toSave['d_SesType'],
									$toSave['d_SesName'],
									$toSave['d_SesTar4Session'],
									$toSave['d_SesAth4Target'],
									$toSave['d_SesFirstTarget'],
									$toSave['d_SesFollow'],
									$toSave['d_SesDtStart'],
									$toSave['d_SesDtEnd'],
									$toSave['d_SesOdfCode'],
									$toSave['d_SesOdfPeriod'],
									$toSave['d_SesOdfVenue'],
									$toSave['d_SesOdfLocation'],
									$toSave['d_SesType']=='Q' ? true : false
								);

							}
							else	// update con cambio di tipo
							{
							// prima aggiungo con la insert
								$x=insertSession(
									$_SESSION['TourId'],
									$newOrder,
									$toSave['d_SesType'],
									$toSave['d_SesName'],
									$toSave['d_SesTar4Session'],
									$toSave['d_SesAth4Target'],
									$toSave['d_SesFirstTarget'],
									$toSave['d_SesFollow'],
									$toSave['d_SesDtStart'],
									$toSave['d_SesDtEnd'],
									$toSave['d_SesOdfCode'],
									$toSave['d_SesOdfPeriod'],
									$toSave['d_SesOdfVenue'],
									$toSave['d_SesOdfLocation']
								);

								if ($x!==true)
								{
									$msg=$x;
								}
								else
								{
							// poi con la oldKey ricavo la riga da rancare
									list($oldOrder,$oldType)=explode('_',$toSave['oldKey']);

									$x=deleteSession($_SESSION['TourId'],$oldOrder,$oldType);

									if ($x!==true)
										$msg=$x;
								}
							}
						}
						else
						{
							$msg='_error_';
						}
					}
				}
				else
				{
					$msg='_error_';
				}
			}
			else
			{
				$msg='_error_';
			}
		}
		elseif ($command=='DEL')
		{
			$id=isset($_REQUEST['id']) ? $_REQUEST['id'] : null;
			$idParts=explode('_',$id);

			$x=deleteSession($_SESSION['TourId'],$idParts[0],$idParts[1]);

			if ($x!==true)
				$msg=$x;
		}
		//exit;
	// per resettare la post
		if ($msg=='')
		{
			header('Location: ./ManSessions.php');
			exit;
		}
	}

// combo sessioni
	$comboSessionsTypes= '<select id="d_SesType" name="d_SesType">' . "\n";
		foreach ($sessionsTypes as $k => $v)
		{
			$toAdd=true;
			if($k=='E' && !$_SESSION['MenuElimDo'])
				$toAdd=false;
			if($k=='F' && !$_SESSION['MenuFinTDo'] && !$_SESSION['MenuFinIDo'])
				$toAdd=false;
			if($toAdd)
				$comboSessionsTypes.='<option value="' . $k . '">' . $v. '</option>' . "\n";
		}
	$comboSessionsTypes.='</select>' . "\n";

// combo si / no
	$comboFollow= '<select id="d_SesFollow" name="d_SesFollow">' . "\n";
		$comboFollow.='<option value="0">' . get_text('No'). '</option>' . "\n";
		$comboFollow.='<option value="1">' . get_text('Yes'). '</option>' . "\n";
	$comboFollow.='</select>' . "\n";


// elenco delle sessioni
	$sessions=array();

	foreach ($sessionsTypes as $k=> $v)
	{
		$sessions[$v]=array();

		$q="
			SELECT *
			FROM
				Session
			WHERE
				SesTournament={$tourId_safe} AND SesType='{$k}'
			ORDER BY
				SesOrder ASC
		";
		$r=safe_r_sql($q);

		if ($r && safe_num_rows($r)>0)
		{
			while ($myRow=safe_fetch($r))
			{
				$sessions[$v][]=$myRow;

				$schedules=array();

				//print_r($schedules);
				$sessions[$v][count($sessions[$v])-1]->schedules=$schedules;
			}
		}
	}

	/*print '<pre>';
	print_r($sessions);
	print '</pre>';
	exit;*/
	$PAGE_TITLE=get_text('ManSession', 'Tournament');

	$JS_SCRIPT=array(
		phpVars2js(array('StrMsgAreYouSure'=>get_text('MsgAreYouSure'),'isODF'=>(empty($CFG->ODF) ? '0':'1'))),
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ext-2.2/adapter/ext/ext-base.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ext-2.2/ext-all-debug.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Tournament/Fun_ManSessions.js"></script>'
	);

	include('Common/Templates/head.php');
?>
<div class="centra">
    <form id="frmSave" name="frmSave" method="post" action="<?php print $_SERVER['PHP_SELF'];?>">
        <input type="hidden" name="Command" value="SAVE"/>
        <input type="hidden" id="d_SesOrder" name="d_SesOrder" value="0"/>
        <input type="hidden" id="oldKey" name="oldKey" value=""/>

        <table class="Tabella freeWidth">
            <tr><th colspan="<?php echo (empty($CFG->ODF) ? '9' : '13')?>" class="Title"><?php print get_text('ManSession','Tournament');?></th></tr>
            <tr>
                <th style="width:15%;"><?php print get_text('Type','Tournament');?></th>
                <th style="width:8%;">#</th>
                <th style="width:57%;"><?php print get_text('Name','Tournament');?></th>
                <th style="width:5%;"><?php print get_text('SessionStart','Tournament');?></th>
                <th style="width:5%;"><?php print get_text('SessionEnd','Tournament');?></th>
                <th style="width:5%;"><?php print get_text('Tar4Session','Tournament');?></th>
                <th style="width:5%;"><?php print get_text('Ath4Target','Tournament');?></th>
                <th style="width:5%;"><?php print get_text('FirstTarget','Tournament');?></th>
                <th style="width:5%;"><?php print get_text('ToFollow','Tournament');?></th>
                <?php
                if(!empty($CFG->ODF)) {
                    echo '<th style="width:5%;">' . get_text('SesOdfCode', 'ODF') . '</th>' .
                        '<th style="width:5%;">' . get_text('SesOdfPeriod', 'ODF') . '</th>' .
                        '<th style="width:5%;">' . get_text('SesOdfVenue', 'ODF') . '</th>' .
                        '<th style="width:5%;">' . get_text('SesOdfLocation', 'ODF') . '</th>';
                }
                ?>
            </tr>
            <tr>
                <td class="Center"><?php print $comboSessionsTypes;?></td>
                <td class="Bold"><div id="orderInEdit"></div></td>
                <td><input type="text" size="60" id="d_SesName" name="d_SesName" value="" /></td>
                <td class="Center"><input type="datetime-local" id="d_SesDtStart" name="d_SesDtStart" min="<?php echo $_SESSION["TourRealWhenFrom"]?>T00:00:00" max="<?php echo $_SESSION["TourRealWhenTo"]?>T23:59:00"/></td>
                <td class="Center"><input type="datetime-local" id="d_SesDtEnd" name="d_SesDtEnd" min="<?php echo $_SESSION["TourRealWhenFrom"]?>T00:00:00" max="<?php echo $_SESSION["TourRealWhenTo"]?>T23:59:00"/></td>

                <td class="Center"><input type="text" size="3" id="d_SesTar4Session" name="d_SesTar4Session" value="0" /></td>
                <td class="Center"><input type="text" size="3" id="d_SesAth4Target" name="d_SesAth4Target" value="0" /></td>
                <td class="Center"><input type="text" size="3" id="d_SesFirstTarget" name="d_SesFirstTarget" value="1" /></td>
                <td class="Center"><?php print $comboFollow;?></td>
                <?php
                if(!empty($CFG->ODF)) {
                    echo '<td class="Center"><input type="text" size="3" id="d_SesOdfCode" name="d_SesOdfCode" value="" /></td>'.
                        '<td class="Center"><input type="text" size="3" id="d_SesOdfPeriod" name="d_SesOdfPeriod" value="" /></td>'.
                        '<td class="Center"><input type="text" size="3" id="d_SesOdfVenue" name="d_SesOdfVenue" value="" /></td>'.
                        '<td class="Center"><input type="text" size="3" id="d_SesOdfLocation" name="d_SesOdfLocation" value="" /></td>';
                }
                ?>
            </tr>
            <tr>
                <td colspan="<?php echo (empty($CFG->ODF) ? '9' : '13')?>" class="Center">
                    <input type="button" id="btnSave" value="<?php print get_text('CmdSave');?>" />
                    <input type="button" id="btnCancel" value="<?php print get_text('CmdCancel');?>" />
                </td>
            </tr>
            <tr>
                <td colspan="<?php echo (empty($CFG->ODF) ? '9' : '13')?>" class="Center">
                    <a class="Link" href="ManSessions_kiss.php">:<?php print get_text('Base');?>:</a>
                </td>
            </tr>
        </table>
    </form>

    <br/><br/>

    <form method="get" action="">
        <?php foreach ($sessions as $k=>$v) { // per ogni tipo di sessione ?>
                <table class="Tabella">
                    <tr><th colspan="<?php echo (empty($CFG->ODF) ? '9' : '13')?>"  class="Title"><?php print $k;?></th></tr>
                    <?php if (count($v)>0) { ?>
                        <tr>
                            <th style="width:15%;" colspan="2">#</th>
                            <th><?php print get_text('Name','Tournament');?></th>
                            <?php
                                echo '<th style="width:5%;">'. get_text('SessionStart','Tournament') . '</th>';
                                echo '<th style="width:5%;">'. get_text('SessionEnd','Tournament') . '</th>';
                            ?>
                            <th style="width:5%;"><?php print get_text('Tar4Session','Tournament');?></th>
                            <th style="width:5%;"><?php print get_text('Ath4Target','Tournament');?></th>
                            <th style="width:5%;"><?php print get_text('FirstTarget','Tournament');?></th>
                            <th style="width:5%;"><?php print get_text('ToFollow','Tournament');?></th>
                            <?php
                            if(!empty($CFG->ODF)) {
                                echo '<th style="width:5%;">'.get_text('SesOdfCode','ODF').'</th>'.
                                    '<th style="width:5%;">'.get_text('SesOdfPeriod','ODF').'</th>'.
                                    '<th style="width:5%;">'.get_text('SesOdfVenue','ODF').'</th>'.
                                    '<th style="width:5%;">'.get_text('SesOdfLocation','ODF').'</th>';
                            }
                            ?>
                        </tr>

                        <?php foreach ($v as $s) { // butto fuori le sessioni di tipo $k?>
                            <?php $id=$s->SesOrder . '_' . $s->SesType; ?>
                            <tr id="row-<?php print $id;?>" style="cursor: pointer;">
                                <td style="width:3%;" class="Center">
                                    <img class="del-<?php print $id;?>" src="../Common/Images/drop.png" style="cursor:pointer; width: 16px;height: 16px;" />
                                </td>
                                <td class="Right">
                                    <input type="hidden" id="order-<?php print $id;?>" value="<?php print $s->SesOrder;?>" />
                                    <a class="Link" id="link-<?php print $id;?>" href="#"><?php print $s->SesOrder;?></a>
                                </td>
                                <td>
                                    <input type="hidden" id="name-<?php print $id;?>" value="<?php print $s->SesName;?>" />
                                    <input type="hidden" id="dtstart-<?php print $id;?>" value="<?php print str_replace(" ","T",$s->SesDtStart);?>" />
                                    <input type="hidden" id="dtend-<?php print $id;?>" value="<?php print str_replace(" ","T",$s->SesDtEnd);?>" />
                                    <?php print $s->SesName; ?>

                                </td>
                                <?php
                                    echo '<td class="Right">';
                                    if(intval($s->SesDtStart)) echo substr($s->SesDtStart,0,-3);
                                    echo '</td>';
                                    echo '<td class="Right">';
                                    if(intval($s->SesDtEnd)) echo substr($s->SesDtEnd,0,-3);
                                    echo '</td>';
                                ?>

                                <td class="Right">
                                    <input type="hidden" id="tar4session-<?php print $id;?>" value="<?php print $s->SesTar4Session;?>" />
                                    <?php print $s->SesTar4Session;?>
                                </td>
                                <td class="Right">
                                    <input type="hidden" id="ath4target-<?php print $id;?>" value="<?php print $s->SesAth4Target;?>" />
                                    <?php print $s->SesAth4Target;?>
                                </td>
                                <td class="Right">
                                    <input type="hidden" id="firstTarget-<?php print $id;?>" value="<?php print $s->SesFirstTarget;?>" />
                                    <?php print $s->SesFirstTarget;?>
                                </td>
                                <td class="Center">
                                    <input type="hidden" id="follow-<?php print $id;?>" value="<?php print $s->SesFollow;?>" />
                                    <?php print ($s->SesFollow==0 ? get_text('No') : get_text('Yes'));?>
                                </td>
                                <?php
                                if(!empty($CFG->ODF)) {
                                    echo '<td class="Center"><input type="hidden" id="odfcode-'.$id.'" value="'.$s->SesOdfCode.'" />'.$s->SesOdfCode.'</td>';
                                    echo '<td class="Center"><input type="hidden" id="odftype-'.$id.'" value="'.$s->SesOdfPeriod.'" />'.$s->SesOdfPeriod.'</td>';
                                    echo '<td class="Center"><input type="hidden" id="odfvenue-'.$id.'" value="'.$s->SesOdfVenue.'" />'.$s->SesOdfVenue.'</td>';
                                    echo '<td class="Center"><input type="hidden" id="odflocation-'.$id.'" value="'.$s->SesOdfLocation.'" />'.$s->SesOdfLocation.'</td>';
                                }
                                ?>
                            </tr>
                            <?php if (count ($s->schedules)>0) { // gli eventi nello scheduler collegati alla sessione ?>
                                <?php foreach ($s->schedules as $schedule) { ?>
                                    <tr>
                                        <td colspan="2">&nbsp;</td>
                                        <td colspan="5">
                                            <?php print $schedule->SchDescr;?>:
                                            <?php print dateRenderer($schedule->SchDateStart); ?> (<?php print $schedule->SchOrder;?>) -&gt;
                                            <?php print ($schedule->SchDateEnd=='0000-00-00 00:00:00' ? '...' : dateRenderer($schedule->SchDateEnd)); ?>
                                        </td>
                                    </tr>
                                    <tr class="divider"><td colspan="7"></td></tr>
                                <?php }?>
                            <?php }?>
                        <?php } // fine butto fuori le sessioni ?>
                    <?php } // fine per ogni sessione?>
                </table>
            <br/>
        <?php }?>
    </form>
</div>
<?php
	include('Common/Templates/tail.php');