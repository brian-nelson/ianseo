<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Fun_Sessions.inc.php');

	CheckTourSession(true);

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

	$ComboSessions= '<select name="Session">' . "\n";
		$ComboSessions.='<option value="All">' . get_text('AllSessions','Tournament') . '</option>' . "\n";
		foreach ($sessions AS $s)
		{
			$ComboSessions.='<option value="' . $s->SesOrder. '">' . $s->Descr . '</option>' . "\n";
		}
	$ComboSessions.='</select>' . "\n";

	$PAGE_TITLE=get_text('PrintList','Tournament');

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
				<br/><a href="PrnSession.php?Session=<?php print $s->SesOrder;?>&tf=1" class="Link" target="PrintOut">+ <?php print get_text('TargetType');?></a>
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
			<br/><a href="PrnCategory.php?tf=1" class="Link" target="PrintOut">+ <?php print get_text('TargetType');?></a>
		</td>
		<?php foreach ($sessions as $s) { ?>
			<td class="Center" width="<?php print $SmallCellW;?>%">
				<a href="PrnCategory.php?Session=<?php print $s->SesOrder;?>" class="Link" target="PrintOut">
					<img src="../Common/Images/pdf_small.gif" alt="<?php print get_text('StartlistCategory','Tournament');?>" border="0"><br>
					<?php print $s->Descr;?>
				</a>
				<br/><a href="PrnCategory.php?Session=<?php print $s->SesOrder;?>&tf=1" class="Link" target="PrintOut">+ <?php print get_text('TargetType');?></a>
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
			<a href="OrisCountry.php" class="Link" target="ORISPrintOut">
				<img src="../Common/Images/pdfOris.gif" alt="<?php print get_text('StartlistCountry','Tournament');?>" border="0"><br>
				<?php print get_text('StartlistCountry','Tournament');?>
			</a>
			<br/><a href="OrisCountry.php?Athletes=1" class="Link" target="ORISPrintOut">
				<?php print get_text('StartlistCountryOnlyAthletes','Tournament');?>
			</a>
		</td>
		<td class="Center" width="20%">
			<a href="OrisListCountry.php" class="Link" target="ORISPrintOut">
				<img src="../Common/Images/pdfOris.gif" alt="<?php print get_text('ListCountries','Tournament');?>" border="0"><br>
				<?php print get_text('ListCountries','Tournament');?>
			</a>
		</td>
		<td class="Center" width="20%">
			<a href="OrisTeamList.php" class="Link" target="ORISPrintOut">
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
	<tr>
	<?php // stampa piazzole ?>
		<td width="20%" class="Center">
			<form action="PrnSession.php" method="get" target="PrintOut">
				&nbsp;<br />
				<?php print get_text('Session');?>&nbsp;&nbsp;&nbsp;
				<?php print $ComboSessions;?>
				<br />&nbsp;<br />
				<input type="submit" name="Submit" value="<?php print get_text('CmdOk');?>">
			</form>
		</td>
	<?php // Elenco per società ?>
		<td width="20%" class="Center">
			<form action="PrnCountry.php" method="get" target="PrintOut">
				&nbsp;<br />
				<?php print get_text('Session');?>&nbsp;&nbsp;&nbsp;
				<?php print $ComboSessions;?>
				<br />&nbsp;<br />
				<?php print get_text('Country');?>&nbsp;&nbsp;&nbsp;<input name="CountryName" type="text" size="20" maxlength="30">
				<br />&nbsp;<br />
				<?php print get_text('SortBy');?>&nbsp;&nbsp;&nbsp;
				<select name="MainOrder">
					<option value="0"><?php print get_text('CountryCode') ?></option>
					<option value="1"><?php print get_text('Nation') ?></option>
				</select>
				<br />&nbsp;<br />
				<?php echo get_text('NoPhoto'); ?>&nbsp;&nbsp;&nbsp;<input name="NoPhoto" type="checkbox" size="20" maxlength="30">
				<br />&nbsp;<br />
				<input type="submit" name="Submit" value="<?php print get_text('CmdOk');?>">
			</form>
		</td>
	<?php // Elenco Alfabetico ?>
		<td width="20%" class="Center">
			<form action="PrnAlphabetical.php" method="get" target="PrintOut">
				&nbsp;<br />
				<?php print get_text('Session');?>&nbsp;&nbsp;&nbsp;
				<?php print $ComboSessions;?>
				<br />&nbsp;<br />
				<?php print get_text('Archers');?>&nbsp;&nbsp;&nbsp;<input name="ArcherName" type="text" size="20" maxlength="30">
				<br />&nbsp;<br />
				<?php print get_text('DivisionClass');?>&nbsp;&nbsp;&nbsp;<input name="Divisions" type="text" size="20" maxlength="30">
				<br />&nbsp;<br />
				<input type="submit" name="Submit" value="<?php print get_text('CmdOk');?>">
			</form>
		</td>
	<!--  Elenco per Categoria -->
		<td width="20%" class="Center">
			<form action="PrnCategory.php" method="get" target="PrintOut">
				&nbsp;<br />
				<?php print get_text('Session');?>&nbsp;&nbsp;&nbsp;
				<?php print $ComboSessions;?>
				<br />&nbsp;<br />
				<?php print get_text('SortBy');?>&nbsp;&nbsp;&nbsp;
				<select name="MainOrder">
					<option value="0"><?php print get_text('Athlete') ?></option>
					<option value="1"><?php print get_text('CountryCode') ?></option>
					<option value="2"><?php print get_text('Nation') ?></option>
				</select>
				<br />&nbsp;<br />
				<?php print get_text('DivisionClass');?>&nbsp;&nbsp;&nbsp;<input name="ArcherCategories" type="text" size="20" maxlength="30">
				<br />&nbsp;<br />
				<input type="submit" name="Submit" value="<?php print get_text('CmdOk');?>">
			</form>
		</td>
	<?php // Elenco Anomalia ?>
		<td width="20%" class="Center">
			<form action="PrnErrors.php" method="get" target="PrintOut">
				&nbsp;<br />
				<?php print get_text('Session');?>&nbsp;&nbsp;&nbsp;
				<?php print $ComboSessions;?><br />&nbsp;<br />
				<input type="submit" name="Submit" value="<?php print get_text('CmdOk');?>">
			</form>
		</td>
	</tr>
</table>
<?php include('Common/Templates/tail.php');?>
