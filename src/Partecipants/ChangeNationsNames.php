<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	CheckTourSession(true);
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Fun_Various.inc.php');
    checkACL(AclParticipants, AclReadWrite);

	if(!empty($_REQUEST['delete']) and $CoId=intval($_REQUEST['delete'])) {
		$q=safe_r_sql("select EnId from Entries where EnTournament={$_SESSION['TourId']} and (EnCountry=$CoId or EnCountry2=$CoId or EnCountry3=$CoId) limit 1");
		if(!safe_num_rows($q)) {
			safe_w_sql("delete from Flags where FlTournament={$_SESSION['TourId']} and FlCode=(select CoCode from Countries where CoId=$CoId)");
			safe_w_sql("delete from Countries where CoId=$CoId");
		}
	}

	$PAGE_TITLE=get_text('ChangeNationsNames','Tournament');

	$tour=StrSafe_DB($_SESSION['TourId']);

	$MyHeader
		= '<tr>'
			. '<td class="Title" width="5%"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?ordCountry=' . (isset($_REQUEST['ordCountry']) && $_REQUEST['ordCountry']=='ASC' ? 'DESC' : 'ASC') . '">' . get_text('Country') . '</a></td>'
			. '<td class="Title" width="25%"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?ordNation=' . (isset($_REQUEST['ordNation']) && $_REQUEST['ordNation']=='ASC' ? 'DESC' : 'ASC') . '">' . get_text('Nation') . '</a></td>'
			. '<td class="Title" width="30%"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?ordNationComplete=' . (isset($_REQUEST['ordNationComplete']) && $_REQUEST['ordNationComplete']=='ASC' ? 'DESC' : 'ASC') . '">' . get_text('CompleteCountryName') . '</a></td>'
			. '<td class="Title" width="20%"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?ordParent1=' . (isset($_REQUEST['ordParent1']) && $_REQUEST['ordParent1']=='ASC' ? 'DESC' : 'ASC') . '">' . get_text('CountryNameParent1') . '</a></td>'
			. '<td class="Title" width="20%"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?ordParent2=' . (isset($_REQUEST['ordParent2']) && $_REQUEST['ordParent2']=='ASC' ? 'DESC' : 'ASC') . '">' . get_text('CountryNameParent2') . '</a></td>'
	. '</tr>';

	$OrderBy='c.CoCode ASC ';

	if (isset($_REQUEST['ordCountry']) && ($_REQUEST['ordCountry']=='ASC' || $_REQUEST['ordCountry']=='DESC'))
		$OrderBy = "c.CoCode " . $_REQUEST['ordCountry'] . " ";

	if (isset($_REQUEST['ordNation']) && ($_REQUEST['ordNation']=='ASC' || $_REQUEST['ordNation']=='DESC'))
		$OrderBy = "c.CoName " . $_REQUEST['ordNation'] . " ";

	if (isset($_REQUEST['ordNationComplete']) && ($_REQUEST['ordNationComplete']=='ASC' || $_REQUEST['ordNationComplete']=='DESC'))
		$OrderBy = "c.CoNameComplete " . $_REQUEST['ordNationComplete'] . " ";

	if (isset($_REQUEST['ordParent1']) && ($_REQUEST['ordParent1']=='ASC' || $_REQUEST['ordParent1']=='DESC'))
	$OrderBy = "p1Code " . $_REQUEST['ordParent1'] . " ";

	if (isset($_REQUEST['ordParent2']) && ($_REQUEST['ordParent2']=='ASC' || $_REQUEST['ordParent2']=='DESC'))
	$OrderBy = "p2Code " . $_REQUEST['ordParent2'] . " ";


	$filter="c.CoTournament={$tour} ";

	if (isset($_REQUEST['SetFilter']))
	{
		if (strlen($_REQUEST['fCoCode'])>0)
		{
			$filter.="AND c.CoCode=" . StrSafe_DB($_REQUEST['fCoCode']) . " ";
		}

		if (strlen($_REQUEST['fCoName'])>0)
		{
			$filter.="AND c.CoName LIKE " . StrSafe_DB("%" . $_REQUEST['fCoName'] . "%") . " ";
		}

		if (strlen($_REQUEST['fCoNameComplete'])>0)
		{
			$filter.="AND c.CoNameComplete LIKE " . StrSafe_DB("%" . $_REQUEST['fCoNameComplete'] . "%") . " ";
		}
	}

	$q="
		SELECT distinct
			c.CoId, c.CoTournament, c.CoCode, c.CoName, c.CoNameComplete,
			IFNULL(p1.CoCode,'') as p1Code, IFNULL(p1.CoName,'') as p1Name,
			IFNULL(p2.CoCode,'') as p2Code, IFNULL(p2.CoName,'') as p2Name,
			EnId is null as CanDelete
		FROM Countries as c
		LEFT JOIN Countries AS p1 ON c.CoParent1=p1.CoId AND c.CoTournament=p1.CoTournament
		LEFT JOIN Countries AS p2 ON c.CoParent2=p2.CoId AND c.CoTournament=p2.CoTournament
		left join Entries on EnCountry=c.CoId and EnTournament={$_SESSION['TourId']}
		WHERE
			{$filter}
		ORDER BY
			{$OrderBy}

	";
	$rs=safe_r_sql($q);

	$JS_SCRIPT=array(
		'<script type="text/javascript" src="../Common/ajax/ObjXMLHttpRequest.js"></script>',
		'<script type="text/javascript" src="Fun_AJAX_index.js"></script>',
		'<script type="text/javascript" src="Fun_JS.js"></script>',
		'<script type="text/javascript" src="../Common/js/Fun_JS.inc.js"></script>'
	);

	include('Common/Templates/head.php');
?>
<div align="center">
<div class="medium">
<table class="Tabella">
<tbody>
<tr><th class="Title"><?php echo get_text('ChangeNationsNames','Tournament') ?></th></tr>
<tr class="Divider"><td></td></tr>
<tr>
<td class="Bold">
	<input type="checkbox" name="chk_BlockAutoSave" id="chk_BlockAutoSave" value="1"><?php echo get_text('CmdBlocAutoSave') ?>
</td>
</tr>
</tbody>
</table>

<form method="post" action="<?php print $_SERVER['PHP_SELF'];?>">
	<table class="Tabella">

		<?php print $MyHeader;?>

		<tr>
			<td class="Right"><input type="text" name="fCoCode" size="10" value="<?php print isset($_REQUEST['fCoCode']) ? $_REQUEST['fCoCode'] : '';?>" /></td>
			<td><input type="text" name="fCoName" size="30" value="<?php print isset($_REQUEST['fCoName']) ? $_REQUEST['fCoName'] : '';?>"/></td>
			<td><input type="text" name="fCoNameComplete" size="50" value="<?php print isset($_REQUEST['fCoNameComplete']) ? $_REQUEST['fCoNameComplete'] : '';?>"/></td>
			<td><input type="text" name="CoParent1" size="5" value="<?php print isset($_REQUEST['CoParent1']) ? $_REQUEST['CoParent1'] : '';?>" /></td>
			<td>
				<input type="text" name="fCoCode" size="5" value="<?php print isset($_REQUEST['CoParent2']) ? $_REQUEST['CoParent2'] : '';?>" />
				<input type="submit" name="SetFilter" value="<?php print get_text('Search','Tournament');?>"/>
			</td>
		</tr>

		<?php while ($row=safe_fetch($rs)) { ?>
			<tr>
				<td class="Right Bold"><input type="text" id="d_c_CoCode_<?php print $row->CoId;?>" size="10" maxlength="10" value="<?php print $row->CoCode;?>" onblur="UpdateField('d_c_CoCode_<?php print $row->CoId;?>');" /></td>
				<td><input type="text" id="d_c_CoName_<?php print $row->CoId;?>" size="30" maxlength="30" value="<?php print $row->CoName;?>" onblur="UpdateField('d_c_CoName_<?php print $row->CoId;?>');" /></td>
				<td><input type="text" id="d_c_CoNameComplete_<?php print $row->CoId;?>" size="50" maxlength="80" value="<?php print $row->CoNameComplete;?>"  onblur="UpdateField('d_c_CoNameComplete_<?php print $row->CoId;?>');" /></td>
				<td><input type="text" id="d_c_CoParent1_<?php print $row->CoId;?>" size="5" maxlength="5" value="<?php print $row->p1Code;?>"  onblur="UpdateField('d_c_CoParent1_<?php print $row->CoId;?>');" />&nbsp;<?php print $row->p1Name;?></td>
				<td><input type="text" id="d_c_CoParent2_<?php print $row->CoId;?>" size="5" maxlength="5" value="<?php print $row->p2Code;?>"  onblur="UpdateField('d_c_CoParent2_<?php print $row->CoId;?>');" />&nbsp;<?php print $row->p2Name;?></td>
				<td><img src="<?php echo $CFG->ROOT_DIR."Common/Images/status-".($row->CanDelete ? 'noshoot' : 'ok').".gif" ;?>" <?php if($row->CanDelete) {echo 'onclick="location.href=\''.go_get(array('delete'=>$row->CoId)).'\'"';} ?>></td>
				</tr>
		<?php }?>
	</table>
</form>
</div>
</div>
<?php
	include('Common/Templates/tail.php');
?>