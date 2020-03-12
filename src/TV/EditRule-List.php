<?php
if (!defined('IN_PHP')) CD_redirect();

/********************************
 *
 *  if not editing a segment, shows the already defined and preset segments of the rule
 *
 ***********************************/

// Show the already made Chain
$RuleChain='<table class="Tabella">';
$q=safe_r_sql("SELECT TVSequence.*,TVSOrder=(SELECT COUNT(*) FROM TVSequence WHERE TVSRule=$RuleId AND TVSTournament=$TourId) as last FROM TVSequence WHERE TVSRule=$RuleId AND TVSTournament=$TourId ORDER BY TVSOrder");
if(safe_num_rows($q)) {
	$RuleChain.='<tr>';
	$RuleChain.='<th class="TitleCenter">'.get_text('Order','Tournament').'</th>';
	$RuleChain.='<th class="TitleCenter">'.get_text('Action','Tournament').'</th>';
	$RuleChain.='<th class="TitleCenter">'.get_text('Type','Tournament').'</th>';
	$RuleChain.='<th class="TitleCenter">'.get_text('Name','Tournament').'</th>';
	$RuleChain.='<th class="TitleCenter">'.get_text('TVFilter-Display','Tournament').'</th>';
	$RuleChain.='<th class="TitleCenter">'.get_text('TVDefault-Scroll','Tournament').'</th>';
	$RuleChain.='</tr>';
	while($r=safe_fetch($q)) $RuleChain.=decode_chain($r);
} else {
	$RuleChain.='<tr><td>'.get_text('TVOutNoRules','Tournament').'</td></tr>';
}
$RuleChain.='</table>';

?>
<tr>
<th class="TitleLeft" width="15%"><?php print get_text('TVRenameRule','Tournament');?></th>
<td><input type="text" name="d_TVRuleName" id="d_TVRuleName" size="20" maxlength="64">&nbsp;(<?php print get_text('TVNoFill2NotChange','Tournament');?>)</td>
</tr>

<tr>
<th class="TitleLeft" width="15%"><?php print get_text('TVDefinedChain','Tournament');?></th>
<td><?php echo $RuleChain; ?></td>
</tr>

<tr>
<th class="TitleLeft" width="15%"><?php print get_text('TVPresetChains','Tournament');?></th>
<td><?php echo preset_chains(); ?></td>
</tr>

<tr><td colspan="2"></td></tr>

<?php

echo '<tr>
		<td class="Center" colspan="2"><a href="'.go_get(array('NewDb'=>1, 'ChainType' => 'DB')).'">'.get_text('NewTourContent', 'Tournament').'</a>
 		<a href="'.go_get(array('NewMm'=>1, 'ChainType' => 'MM')).'">'.get_text('NewMediaContent', 'Tournament').'</a></td></tr>';

?>

