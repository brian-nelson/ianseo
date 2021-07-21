<?php
if (!defined('IN_PHP')) CD_redirect();

// Show the already made Chain
$RuleChain='<table class="Tabella">';
$q=safe_r_sql("SELECT TVSequence.*,TVSOrder=(SELECT COUNT(*) FROM TVSequence WHERE TVSRule=$RuleId AND TVSTournament=$TourId) as last FROM TVSequence WHERE TVSRule=$RuleId AND TVSTournament=$TourId ORDER BY TVSOrder");
if(safe_num_rows($q)) {
	$RuleChain.='<tr>';
	$RuleChain.='<th class="TitleCenter">'.get_text('Order','Tournament').'</th>';
	$RuleChain.='<th class="TitleCenter">'.get_text('Type','Tournament').'</th>';
	$RuleChain.='<th class="TitleCenter">'.get_text('Name','Tournament').'</th>';
	$RuleChain.='<th class="TitleCenter">'.get_text('TVFilter-Display','Tournament').'</th>';
	$RuleChain.='<th class="TitleCenter">'.get_text('TVDefault-Scroll','Tournament').'</th>';
	$RuleChain.='</tr>';
	while($r=safe_fetch($q)) $RuleChain.=decode_chain($r, false);
} else {
	$RuleChain.='<tr><td>'.get_text('TVOutNoRules','Tournament').'</td></tr>';
}
$RuleChain.='</table>';

?>

<tr>
<th class="TitleLeft" width="15%"><?php print get_text('TVDefinedChain','Tournament');?></th>
<td><?php echo $RuleChain; ?></td>
</tr>
</table>
<?php

/***************************************
 *
 * if not editing a Tournament content, edits a multimedia
 *
 ****************************************/

// the default object...
$MyRow=new StdClass();
$MyRow->TVPContentName=''; // new content name
$MyRow->TVPContentText=''; // new content name
$MyRow->TVPMultimediaTime='5'; // new content name
$MyRow->TVPMultimediaScroll='50'; // new content name
$MyRow->TVPMultimediaFullScreen=''; // new content name

if($CHAIN) {
	$MyRow->TVPMultimediaTime=$CHAIN->TVSTime; // new content name
	$MyRow->TVPMultimediaScroll=$CHAIN->TVSScroll; // new content name
	$MyRow->TVPMultimediaFullScreen=$CHAIN->TVSFullScreen; // new content name

	$t=safe_r_sql("select * from TVContents where TVCId=$MMId and TVCTournament=" . ($CHAIN->TVSCntSameTour?$TourId:'-1'));
	if($u=safe_fetch($t) and strstr($u->TVCMimeType, 'text/')==$u->TVCMimeType) $MyRow->TVPContentText=$u->TVCContent; // new content name
}
?>
<br/>

<table class="Tabella">
<tr><th class="Title" colspan="3"><?php echo get_text('TVMultimediaContents','Tournament'); ?></th></tr>
<?php

$q=safe_r_sql("select * from TVContents where TVCTournament in (-1,$TourId)");
if(safe_num_rows($q)) {
?>
<tr>
<th class="TitleLeft" width="15%"><?php print get_text('TVMultimediaSelect','Tournament');?></th>
<td>
<table>
<?php
$ActId=$MMId;
if(isset($CHAIN->TVSCntSameTour)) $ActId .= '|' . $CHAIN->TVSCntSameTour;

while($r=safe_fetch($q)) {
	$sel = $r->TVCId . '|' . ($r->TVCTournament!=-1 ? '1':'0');

	echo '<tr><td><input type="radio" name="d_TVMultimedia" value="'.$sel.'"'.($sel==$ActId ?' checked="checked"':'').'></td>';
	echo '<td>'.$r->TVCName.'</td>';
	echo '<td>';
	if($r->TVCTournament!=-1) echo '<a href="'.go_get('contdel', $r->TVCId).'"><img src="../Common/Images/drop.png" border="0"></a>';
	else echo '&nbsp;';
	echo '</td></tr>';
}
?>
</table>
</td>
<td><?php print get_text('TVSelectMultimediaDescr','Tournament');?></td>
</tr>

<tr>
<th class="TitleLeft" width="15%"><?php print get_text('TVMultimediaTime','Tournament');?></th>
<td><input type="text" name="d_TVMultimediaTime" id="d_TVMultimediaTime" size="2" maxlength="2" value="<?php print $MyRow->TVPMultimediaTime;?>"></td>
<td><?php print get_text('TVMultimediaTimeDescr','Tournament');?></td>
</tr>

<tr>
<th class="TitleLeft" width="15%"><?php print get_text('TVMultimediaScroll','Tournament');?></th>
<td><input type="text" name="d_TVMultimediaScroll" id="d_TVMultimediaScroll" size="2" maxlength="2" value="<?php print $MyRow->TVPMultimediaScroll;?>"></td>
<td><?php print get_text('TVMultimediaScrollDescr','Tournament');?></td>
</tr>

<tr>
<th class="TitleLeft" width="15%"><?php print get_text('TVMultimediaFullScreen','Tournament');?></th>
<td><input type="checkbox" name="d_TVMultimediaFullScreen" id="d_TVMultimediaFullScreen"<?php print ($MyRow->TVPMultimediaFullScreen?' checked="checked"':'');?>"></td>
<td><?php print get_text('TVMultimediaFullDescr','Tournament');?></td>
</tr>
<?php

}

?>

<tr><th class="TitleCenter" colspan="3"><?php echo get_text('TVMultimediaNewContent','Tournament'); ?></th></tr>

<tr>
<th class="TitleLeft" width="15%"><?php print get_text('TVContentName','Tournament');?></th>
<td><input type="text" name="d_TVContentName" id="d_TVContentName" value="<?php echo $MyRow->TVPContentName ?>"></td>
<td><?php print get_text('TVContentNameDescr','Tournament');?></td>
</tr>

<tr>
<th class="TitleLeft" width="15%"><?php print get_text('TVContentText','Tournament');?></th>
<td><textarea name="d_TVContentText" id="d_TVContentText" style="width:100%;height:20vh;box-sizing: border-box;"><?php echo $MyRow->TVPContentText ?></textarea></td>
<td><?php print get_text('TVContentTextDescr','Tournament');?></td>
</tr>

<tr>
<th class="TitleLeft" width="15%"><?php print get_text('TVContentUpload','Tournament');?></th>
<td><input type="file" name="d_TVContentUpload" id="d_TVContentUpload"></td>
<td><?php print get_text('TVContentUploadDescr','Tournament');?></td>
</tr>

<tr>
<th class="TitleLeft" width="15%"><?php print get_text('TVDefaultTime','Tournament');?></th>
<td><input type="text" name="d_TVDefaultTime" id="d_TVDefaultTime" value="5"></td>
<td><?php print get_text('TVDefaultTimeDescr','Tournament');?></td>
</tr>

<tr>
<th class="TitleLeft" width="15%"><?php print get_text('TVDefaultScroll','Tournament');?></th>
<td><input type="text" name="d_TVDefaultScroll" id="d_TVDefaultScroll" value="50"></td>
<td><?php print get_text('TVDefaultScrollDescr','Tournament');?></td>
</tr>
</table>
