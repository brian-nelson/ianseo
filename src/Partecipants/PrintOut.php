<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Fun_Sessions.inc.php');

$IncludeJquery=true;

	CheckTourSession(true);
    checkACL(AclParticipants, AclReadOnly);

	$SesNo=0;
	$SmallCellW=0;

	$sessions=GetSessions('Q');

	$SesNo=count($sessions);

	switch ($SesNo)
	{
		case 1:
		case 2:
		case 3:
			$SmallCellW = 20;
			break;
		case 4:
		case 5:
			$SmallCellW = 15;
			break;
		case 6:
		case 7:
			$SmallCellW = 10;
			break;
		case 8:
		case 9:
			$SmallCellW = 7;
			break;
		default:
			$SmallCellW = 4;
	}

	$ComboSessions= '<select name="Session">';
		$ComboSessions.='<option value="All">' . get_text('AllSessions','Tournament') . '</option>';
		foreach ($sessions AS $s)
		{
			$ComboSessions.='<option value="' . $s->SesOrder. '">' . $s->Descr . '</option>';
		}
	$ComboSessions.='</select>';

	$PAGE_TITLE=get_text('PrintList','Tournament');

	$JS_SCRIPT[]='<script src="PrintOut.js"></script>';
	$JS_SCRIPT[]='<style>
			form div {margin-top:0.5em}
			.smallForm select {max-width:15em;overflow:hidden;}
			</style>';

	include('Common/Templates/head.php');
?>
<table class="Tabella">
	<tr><th class="Title" colspan="<?php print ($SesNo+1);?>"><?php print get_text('PrintList','Tournament');?></th></tr>

<?php // -------------------- lista piazzole ?>
	<tr><th class="SubTitle" colspan="<?php print ($SesNo+1);?>"><?php print get_text('StartlistSession','Tournament');?></th></tr>
	<tr>
		<td class="Center">
			<?php if ($SesNo) { ?>
				<a href="PrnSession.php" class="Link" target="PrintOut">
				<img src="../Common/Images/pdf.gif" alt="<?php print get_text('StartlistSession','Tournament');?>" border="0"><br><?php print get_text('StartlistSession','Tournament');?></a>
				<br/><a href="PrnSession.php?Filled=1" class="Link" target="PrintOut"><?php print get_text('StartlistSessionNoEmpty','Tournament');?></a>
				<br/><a href="PrnSession.php?Empty=1" class="Link" target="PrintOut"><?php print get_text('StartlistSessionEmptyPlaces','Tournament');?></a>
				<br/><a href="PrnSession.php?tf=1" class="Link" target="PrintOut">+ <?php print get_text('TargetType');?></a>
			<?php } else { ?>
				<img src="../Common/Images/pdf_small.gif" alt="<?php print get_text('StartlistSession','Tournament');?>" border="0"><br>
				<?php echo get_text('StartlistSession','Tournament');?>
			<?php } ?>
		</td>
		<?php foreach ($sessions as $s) { ?>
			<td class="Center" width="<?php print $SmallCellW;?>%">
				<a href="PrnSession.php?Session=<?php print $s->SesOrder;?>" class="Link" target="PrintOut">
					<img src="../Common/Images/pdf_small.gif" alt="<?php print get_text('StartlistSession','Tournament');?>" border="0"><br>
					<?php print $s->Descr;?>
				</a>
				<br/><a href="PrnSession.php?Session=<?php print $s->SesOrder;?>&Filled=1" class="Link" target="PrintOut"><?php print get_text('StartlistSessionNoEmpty','Tournament');?></a>
				<br/><a href="PrnSession.php?Session=<?php print $s->SesOrder;?>&Empty=1" class="Link" target="PrintOut"><?php print get_text('StartlistSessionEmptyPlaces','Tournament');?></a>
				<br/><a href="PrnSession.php?Session=<?php print $s->SesOrder;?>&tf=1" class="Link" target="PrintOut">+ <?php print get_text('TargetType');?></a>
                <br/><a href="OrisStartList.php?Session=<?php print $s->SesOrder;?>" class="Link" target="ORISPrintOut"><?php print get_text('StdORIS','Tournament');?></a>
			</td>
		<?php }?>
	</tr>
	<tr class="Divider"><td  colspan="<?php print ($SesNo+1);?>">&nbsp;</td></tr>

<?php // -------------------- lista x società ?>
	<tr><th class="SubTitle" colspan="<?php print ($SesNo+1);?>"><?php print get_text('StartlistCountry','Tournament');?></th></tr>
	<tr>
		<td class="Center">
			<a href="PrnCountry.php" class="Link" target="PrintOut">
			<img src="../Common/Images/pdf.gif" alt="<?php print get_text('StartlistCountry','Tournament');?>" border="0">
			<br/><?php print get_text('StartlistCountry','Tournament');?></a>
			<br/><a href="PrnCountry.php?SinglePage=1" class="Link" target="PrintOut"><?php print get_text('StartlistEachCountry','Tournament');?></a>
			<br/><a href="PrnCountry.php?tf=1" class="Link" target="PrintOut">+ <?php print get_text('TargetType');?></a>
		</td>
		<?php foreach ($sessions as $s) { ?>
			<td class="Center" width="<?php print $SmallCellW;?>%">
				<a href="PrnCountry.php?Session=<?php print $s->SesOrder;?>" class="Link" target="PrintOut">
					<img src="../Common/Images/pdf_small.gif" alt="<?php print get_text('StartlistCountry','Tournament');?>" border="0"><br>
					<?php print $s->Descr;?>
				</a>
				<br/><a href="PrnCountry.php?Session=<?php print $s->SesOrder;?>&tf=1" class="Link" target="PrintOut">+ <?php print get_text('TargetType');?></a>
                <br/><a href="OrisCountry.php?Session=<?php print $s->SesOrder;?>" class="Link" target="ORISPrintOut"><?php print get_text('StdORIS','Tournament');?></a>
			</td>
		<?php }?>
	</tr>
	<tr class="Divider"><td  colspan="<?php print ($SesNo+1);?>">&nbsp;</td></tr>

<?php // -------------------- lista x ordine alfabetico ?>
	<tr><th class="SubTitle" colspan="<?php print ($SesNo+1);?>"><?php print get_text('StartlistAlpha','Tournament');?></th></tr>
	<tr>
		<td class="Center">
			<a href="PrnAlphabetical.php" class="Link" target="PrintOut">
			<img src="../Common/Images/pdf.gif" alt="<?php print get_text('StartlistAlpha','Tournament');?>" border="0"><br><?php print get_text('StartlistAlpha','Tournament');?></a>
			<br/><a href="PrnAlphabetical.php?tf=1" class="Link" target="PrintOut">+ <?php print get_text('TargetType');?></a>
		</td>
		<?php foreach ($sessions as $s) { ?>
			<td class="Center" width="<?php print $SmallCellW;?>%">
				<a href="PrnAlphabetical.php?Session=<?php print $s->SesOrder;?>" class="Link" target="PrintOut">
					<img src="../Common/Images/pdf_small.gif" alt="<?php print get_text('StartlistAlpha','Tournament');?>" border="0"><br>
					<?php print $s->Descr;?>
				</a>
				<br/><a href="PrnAlphabetical.php?Session=<?php print $s->SesOrder;?>&tf=1" class="Link" target="PrintOut">+ <?php print get_text('TargetType');?></a>
                <br/><a href="OrisAlphabetical.php?Session=<?php print $s->SesOrder;?>" class="Link" target="ORISPrintOut"><?php print get_text('StdORIS','Tournament');?></a>
			</td>
		<?php }?>
	</tr>
	<tr class="Divider"><td  colspan="<?php print ($SesNo+1);?>">&nbsp;</td></tr>

<!-- -------------------- lista x classe/Divisione -->
	<tr><th class="SubTitle" colspan="<?php print ($SesNo+1);?>"><?php print get_text('StartlistCategory','Tournament');?></th></tr>
	<tr>
		<td class="Center">
			<a href="PrnCategory.php" class="Link" target="PrintOut">
			<img src="../Common/Images/pdf.gif" alt="<?php print get_text('StartlistCategory','Tournament');?>" border="0"><br><?php print get_text('StartlistCategory','Tournament');?></a>
			<br/><a href="PrnCategory.php?SinglePage=1" class="Link" target="PrintOut"><?php print get_text('StartlistEachCategory','Tournament');?></a>
			<br/><a href="PrnCategory.php?tf=1" class="Link" target="PrintOut">+ <?php print get_text('TargetType');?></a>
		</td>
		<?php foreach ($sessions as $s) { ?>
			<td class="Center" width="<?php print $SmallCellW;?>%">
				<a href="PrnCategory.php?Session=<?php print $s->SesOrder;?>" class="Link" target="PrintOut">
					<img src="../Common/Images/pdf_small.gif" alt="<?php print get_text('StartlistCategory','Tournament');?>" border="0"><br>
					<?php print $s->Descr;?>
				</a>
				<br/><a href="PrnCategory.php?Session=<?php print $s->SesOrder;?>&tf=1" class="Link" target="PrintOut">+ <?php print get_text('TargetType');?></a>
                <br/><a href="OrisTeamList.php?Session=<?php print $s->SesOrder;?>" class="Link" target="ORISPrintOut"><?php print get_text('StdORIS','Tournament');?></a>
			</td>
		<?php }?>
	</tr>
	<tr class="Divider"><td  colspan="<?php print ($SesNo+1);?>">&nbsp;</td></tr>

<?php // -------------------- lista delle anomalie ?>
	<tr><th class="SubTitle" colspan="<?php print ($SesNo+1);?>"><?php print get_text('PartecipantListError','Tournament');?></th></tr>
	<tr>
		<td class="Center">
			<a href="PrnErrors.php" class="Link" target="PrintOut">
			<img src="../Common/Images/pdf.gif" alt="<?php print get_text('PartecipantListError','Tournament');?>" border="0"><br><?php print get_text('PartecipantListError','Tournament');?></a>
		</td>
		<?php foreach ($sessions as $s) { ?>
			<td class="Center" width="<?php print $SmallCellW;?>%">
				<a href="PrnErrors.php?Session=<?php print $s->SesOrder;?>" class="Link" target="PrintOut">
					<img src="../Common/Images/pdf_small.gif" alt="<?php print get_text('PartecipantListError','Tournament');?>" border="0"><br>
					<?php print $s->Descr;?>
				</a>
			</td>
		<?php }?>
	</tr>
</table>

<?php // -------------------- oris ?>
<br/>
<table class="Tabella">
	<tr><th class="Title" colspan="5"><?php print get_text('StdORIS','Tournament');?></th></tr>
	<tr>
		<td class="Center" width="20%">
			<?php if($SesNo) {?>
				<a href="OrisStartList.php" class="Link" target="ORISPrintOut">
					<img src="../Common/Images/pdfOris.gif" alt="<?php print get_text('StartlistSession','Tournament');?>" border="0"><br>
					<?php print get_text('StartlistSession','Tournament');?>
				</a>
			<?php } else { ?>
				<img src="../Common/Images/pdfOris_small.gif" alt="<?php print get_text('StartlistSession','Tournament');?>" border="0"><br>
				<?php print get_text('StartlistSession','Tournament');?>
			<?php }?>
		</td>
		<td class="Center" width="20%">
            <div>
                <a href="OrisCountry.php" class="Link" target="ORISPrintOut">
                    <img src="../Common/Images/pdfOris.gif" alt="<?php print get_text('StartlistCountry','Tournament');?>" border="0"><br>
                    <?php print get_text('StartlistCountry','Tournament');?>
                </a>
            </div>
            <div>
                <a href="OrisCountry.php?Athletes=1" class="Link" target="ORISPrintOut">
                    <?php print get_text('StartlistCountryOnlyAthletes','Tournament');?>
                </a>
            </div>

            <div style="margin-top:0.5em;"><b><?php print get_text('StartlistEachCountry','Tournament');?></b></div>
			<div style="display: flex;align-items: flex-end">
                <div style="margin:0 0.5em"><?= get_text('DOB', 'Tournament') ?><br><input type="checkbox" id="CoDoB" checked="checked"></div>
                <div style="margin:0 0.5em"><?= get_text('Contacts', 'Tournament') ?><br><input type="checkbox" id="CoContacts" checked="checked"></div>
                <div style="margin:0 0.5em"><?= get_text('MissingPhoto', 'Tournament') ?><br><input type="checkbox" id="CoMissing" checked="checked"></div>
                <div style="margin:0 0.5em"><?= get_text('PhotoRetake', 'Tournament') ?><br><input type="checkbox" id="CoPictures" checked="checked"></div>
                <div style="margin:0 0.5em"><input type="button" onclick="printCountries()" value="<?= get_text('Print', 'Tournament') ?>"></div>
            </div>

		</td>
		<td class="Center" width="20%">
			<a href="OrisListCountry.php" class="Link" target="ORISPrintOut">
				<img src="../Common/Images/pdfOris.gif" alt="<?php print get_text('ListCountries','Tournament');?>" border="0"><br>
				<?php print get_text('ListCountries','Tournament');?>
			</a>
		</td>
		<td class="Center" width="20%">
			<div style="float:right"><select id="TeamEvents[]" multiple="multiple" size="8"><?php
			$q=safe_r_sql("select * from Events where EvTeamEvent=0 and EvTournament={$_SESSION['TourId']} order by EvProgr");
			while($r=safe_fetch($q)) {
				echo '<option value="'.$r->EvCode.'">'.$r->EvEventName.'</option>';
			}
			?></select></div>
			<a href="OrisTeamList.php" class="Link" target="ORISPrintOut" onclick="CheckOrisTeam(this)">
				<img src="../Common/Images/pdfOris.gif" alt="<?php print get_text('OrisTeamList','Tournament');?>" border="0"><br>
				<?php print get_text('StartlistTeam','Tournament');?>
			</a>

		</td>
		<td class="Center" width="20%">
			<a href="OrisAlphabetical.php" class="Link" target="ORISPrintOut">
				<img src="../Common/Images/pdfOris.gif" alt="<?php print get_text('StartlistAlpha','Tournament');?>" border="0"><br>
				<?php print get_text('StartlistAlpha','Tournament');?>
			</a>
		</td>
	</tr>
</table>

<?php // -------------------- combo ?>
<br/>
<table class="Tabella">
	<tr><th class="Title" colspan="5"><?php print get_text('PrintList','Tournament');?></th></tr>
	<tr>
		<th class="SubTitle" width="20%"><?php print get_text('StartlistSession','Tournament');?></th>
		<th class="SubTitle" width="20%"><?php print get_text('StartlistCountry','Tournament');?></th>
		<th class="SubTitle" width="20%"><?php print get_text('StartlistAlpha','Tournament');?></th>
		<th class="SubTitle" width="20%"><?php print get_text('StartlistCategory','Tournament');?></th>
		<th class="SubTitle" width="20%"><?php print get_text('PartecipantListError','Tournament');?></th>
	</tr>
	<tr valign="top">
	<?php

// stampa piazzole
echo '<td width="20%" class="Center">
		<form action="PrnSession.php" method="get" target="PrintOut" class="smallForm">
			<div>'. get_text('Session') . '&nbsp;&nbsp;&nbsp;'.$ComboSessions.'</div>
			<div><input type="submit" name="Submit" value="'.get_text('CmdOk').'"></div>
		</form>
	</td>';

// Country Order
echo '<td width="20%" class="Center">
		<form action="PrnCountry.php" method="get" target="PrintOut" class="smallForm">
			<div>'.get_text('Session').'&nbsp;&nbsp;&nbsp;'.$ComboSessions.'</div>
			<div>'.get_text('Country').'&nbsp;&nbsp;&nbsp;<input name="CountryName" type="text" size="20" maxlength="30"></div>
			<div>'.get_text('SortBy').'&nbsp;&nbsp;&nbsp;
			<select name="MainOrder">
				<option value="0">'.get_text('CountryCode').'</option>
				<option value="1">'.get_text('Nation').'</option>
			</select></div>
			<div>'.get_text('NoPhoto').'&nbsp;&nbsp;&nbsp;<input name="NoPhoto" type="checkbox"></div>
			<div>'.get_text('Email', 'Tournament').'&nbsp;&nbsp;&nbsp;<input name="Email" type="checkbox"></div>
			<div><input type="submit" name="Submit" value="'.get_text('CmdOk').'"></div>
		</form>
	</td>';


// Alphabetical Order
echo '<td width="20%" class="Center">
		<form action="PrnAlphabetical.php" method="get" target="PrintOut" class="smallForm">
			<div>'.get_text('Session').'&nbsp;&nbsp;&nbsp;'.$ComboSessions.'</div>
			<div>'.get_text('Archers').'&nbsp;&nbsp;&nbsp;<input name="ArcherName" type="text" size="20" maxlength="30"></div>
			<div>'.get_text('DivisionClass').'&nbsp;&nbsp;&nbsp;<input name="Divisions" type="text" size="20" maxlength="30"></div>
			<div><input type="submit" name="Submit" value="'.get_text('CmdOk').'"></div>
		</form>
	</td>';

// SubCategories
echo '<td width="20%" class="Center">
		<form action="PrnCategory.php" method="get" target="PrintOut" class="smallForm">
			<div>'.get_text('Session').'&nbsp;&nbsp;&nbsp;'.$ComboSessions.'</div>
			<div>'.get_text('SortBy').'&nbsp;&nbsp;&nbsp;
			<select name="MainOrder">
				<option value="0">'.get_text('Athlete').'</option>
				<option value="1">'.get_text('CountryCode').'</option>
				<option value="2">'.get_text('Nation').'</option>
			</select></div>
			<div>'.get_text('DivisionClass').'&nbsp;&nbsp;&nbsp;<input name="ArcherCategories" type="text" size="20" maxlength="30"></div>
			<div><input type="submit" name="Submit" value="'.get_text('CmdOk').'"></div>
		</form>
	</td>';

// Anomalies
echo '<td width="20%" class="Center">
		<form action="PrnErrors.php" method="get" target="PrintOut" class="smallForm">
			<div>'.get_text('Session').'&nbsp;&nbsp;&nbsp;'.$ComboSessions.'</div>
			<div><input type="submit" name="Submit" value="'.get_text('CmdOk').'"></div>
		</form>
	</td>';

echo '</tr>
</table>';


include('Common/Templates/tail.php');

