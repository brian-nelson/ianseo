<?php
	$id=isset($_REQUEST['id']) ? $_REQUEST['id'] : null;
	
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	if (!CheckTourSession() || is_null($id)) PrintCheckError();
    checkACL(AclParticipants, AclReadWrite, false);
	
	require_once('Common/Fun_Various.inc.php');
	
	$query
		= "SELECT "
			. "EnIndClEvent,EnTeamClEvent,EnIndFEvent,EnTeamFEvent,EnTeamMixEvent "
		. "FROM "
			. "Entries "
		. "WHERE "
			. "EnId=" . StrSafe_DB($id) . " ";
			
	$rs=safe_r_sql($query);
	$myRow=null;
	
	if (safe_num_rows($rs)!=1)
	{
		$errMsg=get_text('Error');
	}
	else
	{
		$myRow=safe_fetch($rs);
	}
	
	$JS_SCRIPT=array(
		'<link href="'.$CFG->ROOT_DIR.'Partecipants-exp/css/partecipants.css" media="screen" rel="stylesheet" type="text/css">',
		phpVars2js(array(
			'StrPhoto' => get_text('Photo', 'Tournament'),
			'StrClose' => get_text('Close'),
			'StrEvents' => get_text('Events', 'Tournament'),
			'StrEventAccess' => get_text('EventAccess', 'Tournament'),
			'StrError' => get_text('Error'),
			'StrOk' => get_text('CmdOk'),
			'WebDir' => $CFG->ROOT_DIR,
			)),
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ext-2.2/adapter/ext/ext-base.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ext-2.2/ext-all-debug.js"></script>',
		'<script type="text/javascript">',
		'	Ext.onReady',
		'	(',
		'		function ()',
		'		{',
		'			Ext.get(\'error-msg\').update(\'\');',
		'			',
		'			Ext.select(\'.ev\')',
		'				.on(\'change\',',
		'					function()',
		'					{',
		'						var o=',
		'						{',
		'							url: \''.$CFG->ROOT_DIR.'Partecipants-exp/actions/xmlUpdateEventAccess.php\',',
		'							method: \'POST\',',
		'							params:',
		'							{',
		'								p: this.id,',
		'								v: this.value',
		'							},',
		'							success: function(response)',
		'							{',
		'								var xml=response.responseXML;',
		'								',
		'								var dq=Ext.DomQuery;',
		'								',
		'								var error=dq.selectNode(\'error\',xml).firstChild.data;',
		'								',
		'								if (error==1)',
		'								{',
		'									Ext.get(\'error-msg\').update(StrError);',
		'								}',
		'								else',
		'								{',
		'									Ext.get(\'error-msg\').update(StrOk);',
		'								}',
		'							}',
		'						}',
		'						Ext.Ajax.request(o);',
		'					}',
		'				);',
		'		},',
		'		window',
		'	);',
		'</script>',
	);
	include('Common/Templates/head-min.php');
?>
<div id="events-access">
	<form name="frm" id="frm" method="post" action="<?php print $_SERVER['PHP_SELF']; ?>"  enctype="multipart/form-data">
		<input type="hidden" name="id" value="<?php print $id; ?>" />

		<table class="Tabella">
			<tr>
				<th class="TitleLeft"><?php print get_text('IndClEvent', 'Tournament'); ?></th>
				<td class="Center">
					<select class="ev" name="EnIndClEvent_<?php print $id; ?>" id="EnIndClEvent_<?php print $id; ?>">
						<option value="1"<?php print ($myRow->EnIndClEvent==1 ? ' selected' : ''); ?>><?php print get_text('Yes'); ?></option>
						<option value="0"<?php print ($myRow->EnIndClEvent==0 ? ' selected' : ''); ?>><?php print get_text('No'); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th class="TitleLeft"><?php print get_text('IndFinEvent', 'Tournament');; ?></th>
				<td class="Center">
					<select class="ev" name="EnIndFEvent_<?php print $id; ?>" id="EnIndFEvent_<?php print $id; ?>">
						<option value="1"<?php print ($myRow->EnIndFEvent==1 ? ' selected' : ''); ?>><?php print get_text('Yes'); ?></option>
						<option value="0"<?php print ($myRow->EnIndFEvent==0 ? ' selected' : ''); ?>><?php print get_text('No'); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th class="TitleLeft"><?php print get_text('TeamClEvent', 'Tournament'); ?></th>
				<td class="Center">
					<select class="ev" name="EnTeamClEvent_<?php print $id; ?>" id="EnTeamClEvent_<?php print $id; ?>">
						<option value="1"<?php print ($myRow->EnTeamClEvent==1 ? ' selected' : ''); ?>><?php print get_text('Yes'); ?></option>
						<option value="0"<?php print ($myRow->EnTeamClEvent==0 ? ' selected' : ''); ?>><?php print get_text('No'); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th class="TitleLeft"><?php print get_text('TeamFinEvent', 'Tournament'); ?></th>
				<td class="Center">
					<select class="ev" name="EnTeamFEvent_<?php print $id; ?>" id="EnTeamFEvent_<?php print $id; ?>">
						<option value="1"<?php print ($myRow->EnTeamFEvent==1 ? ' selected' : ''); ?>><?php print get_text('Yes'); ?></option>
						<option value="0"<?php print ($myRow->EnTeamFEvent==0 ? ' selected' : ''); ?>><?php print get_text('No'); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th class="TitleLeft"><?php print get_text('MixedTeamFinEvent', 'Tournament'); ?></th>
				<td class="Center">
					<select class="ev" name="EnTeamFEvent_<?php print $id; ?>" id="EnTeamMixEvent_<?php print $id; ?>">
						<option value="1"<?php print ($myRow->EnTeamMixEvent==1 ? ' selected' : ''); ?>><?php print get_text('Yes'); ?></option>
						<option value="0"<?php print ($myRow->EnTeamMixEvent==0 ? ' selected' : ''); ?>><?php print get_text('No'); ?></option>
					</select>
				</td>
			</tr>
		</table>

	</form>

	<div id="error-msg" style="color:#ff0000; font-weight:bold;"></div>
</div>
<?php
	include('Common/Templates/tail-min.php');
?>