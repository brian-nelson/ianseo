<?php
function emptyIdCard($sets='') {
	$ret=new StdClass();
	$ret->Settings=array();
	$ret->Background = '';
	$ret->ImgSize = 0;
	if($sets) {
		$ret->Settings = unserialize($sets->IcSettings);
		$ret->Background = $sets->IcBackground;
		if(!empty($sets->ImgSize)) $ret->ImgSize = $sets->ImgSize;
	}

	if(!isset($ret->Settings["Height"])) $ret->Settings["Height"] = $_SESSION['ToPaper'] ? 139 : 148;
	if(!isset($ret->Settings["OffsetY"])) $ret->Settings["OffsetY"] = '0;'.$ret->Settings["Height"];
	if(!isset($ret->Settings["PaperHeight"])) $ret->Settings["PaperHeight"] = $_SESSION['ToPaper'] ? 278 : 297;
	if(!isset($ret->Settings["Width"])) $ret->Settings["Width"] = $_SESSION['ToPaper'] ? 108 : 105;
	if(!isset($ret->Settings["OffsetX"])) $ret->Settings["OffsetX"] = '0;'.$ret->Settings["Width"];
	if(!isset($ret->Settings["PaperWidth"])) $ret->Settings["PaperWidth"] = $_SESSION['ToPaper'] ? 216 : 210;


	if(!isset($ret->Settings["IdBgX"])) $ret->Settings["IdBgX"] = 0;
	if(!isset($ret->Settings["IdBgY"])) $ret->Settings["IdBgY"] = 0;
	if(!isset($ret->Settings["IdBgH"])) $ret->Settings["IdBgH"] = 0;
	if(!isset($ret->Settings["IdBgW"])) $ret->Settings["IdBgW"] = 0;

	return $ret;
}

function CreateDefaultA($CardNumber=0, $CardName='') {
	global $CFG;
	$Options=emptyIdCard();
	if(!$CardName) $CardName=get_text('Accreditation', 'Tournament');

	$LogoHeight=ceil($Options->Settings["Height"]/7);
	$HeadHeight=intval($LogoHeight/2);
	$HeadWidth=$Options->Settings["Width"]-10;
	$HeadStart=5;

	$PhotoHeight=intval(min(10, $Options->Settings["Height"]/14))*4;
	$PhotoWidth=$PhotoHeight*3/4;
	$NameWidth=$Options->Settings["Width"]-$PhotoWidth-11;
	$NameHeight=$PhotoHeight/2;

	$FlagWidth=intval($PhotoHeight/3)*3;
	$FlagHeight=2*$FlagWidth/3;
	$ClubWidth=$Options->Settings["Width"]-$FlagWidth-11;
	$ClubHeigth=$FlagHeight/2;

	$Gap=3;

	$TopPhoto=5+$LogoHeight+$Gap;
	$TopFlag=$TopPhoto+$PhotoHeight+$Gap;
	$TopCategory=$TopFlag+$FlagHeight+$Gap;

	$CategoryHeight=$Options->Settings["Height"] - 5 - $TopCategory;

	safe_w_sql("insert ignore into IdCards set
		IcTournament={$_SESSION['TourId']},
		IcType='A',
		IcNumber=$CardNumber,
		IcName=".StrSafe_DB($CardName).",
		IcSettings=".StrSafe_DB(serialize($Options->Settings)));

	$SQL="INSERT INTO IdCardElements set IceTournament={$_SESSION['TourId']}, IceCardType='A', IceCardNumber=$CardNumber, IceOrder=%s, IceType='%s', IceContent=%s, IceOptions=%s";
	$Order=1;

	// logo sx
	if(file_exists($IM=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-ToLeft.jpg')) {
		$Opts = Array ('X' =>  5, 'Y' => 5, 'W' => 0, 'H' => $LogoHeight);
		safe_w_sql(sprintf($SQL, $Order++, 'ToLeft', StrSafe_DB(''), StrSafe_DB(serialize($Opts))));
		$l=getimagesize($IM);
		$HeadWidth-=($LogoHeight*$l[0]/$l[1] + 1);
		$HeadStart+=($LogoHeight*$l[0]/$l[1] + 1);
	}
	// logo dx
	if(file_exists($IM=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-ToRight.jpg')) {
		$l=getimagesize($IM);
		$HeadWidth-=($LogoHeight*$l[0]/$l[1] + 1);
		$Opts = Array ('X' => $Options->Settings["Width"]-5-($LogoHeight*$l[0]/$l[1]), 'Y' => 5, 'W' => 0, 'H' => $LogoHeight);
		safe_w_sql(sprintf($SQL, $Order++, 'ToRight', StrSafe_DB(''), StrSafe_DB(serialize($Opts))));
	}
	// logo bottom
	if(file_exists($IM=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-ToBottom.jpg')) {
		$l=getimagesize($IM);
		$tmp=(10*$l[0]/$l[1]);
		$Opts = Array ('X' => ($Options->Settings["Width"]-$tmp)/2, 'Y' => $Options->Settings["Height"]-15, 'W' => 0, 'H' => 10);
		safe_w_sql(sprintf($SQL, $Order++, 'ToBottom', StrSafe_DB(''), StrSafe_DB(serialize($Opts))));
		$CategoryHeight-=($Gap+10);
	}

	// Competition Name
	$Opts = Array ('X' => $HeadStart, 'Y' => 5, 'W' => $HeadWidth, 'H' => $HeadHeight, 'Col' => '#0000CC', 'BackCol' => '', 'Font' => 'arialbd', 'Size' => 8, 'Just' => 1);
	safe_w_sql(sprintf($SQL, $Order++, 'CompName', StrSafe_DB(''), StrSafe_DB(serialize($Opts))));
	// Competition Details
	$Opts = Array ('X' => $HeadStart, 'Y' => 5+$HeadHeight, 'W' => $HeadWidth, 'H' => $HeadHeight, 'Col' => '#0000CC', 'BackCol' => '', 'Font' => 'arial', 'Size' => 8, 'Just' => 1);
	safe_w_sql(sprintf($SQL, $Order++, 'CompDetails', StrSafe_DB(''), StrSafe_DB(serialize($Opts))));

	// Athlete Family Name
	$Opts = Array ('X' => 5, 'Y' => $TopPhoto, 'W' => $NameWidth, 'H' => $NameHeight, 'Col' => '#000000', 'BackCol' => '', 'Font' => 'arialbd', 'Size' => 30, 'Just' => 0);
	safe_w_sql(sprintf($SQL, $Order++, 'Athlete', StrSafe_DB('FamCaps'), StrSafe_DB(serialize($Opts))));
	// Athlete GivenName
	$Opts = Array ('X' => 5, 'Y' => $TopPhoto+$NameHeight, 'W' => $NameWidth, 'H' => $NameHeight, 'Col' => '#000000', 'BackCol' => '', 'Font' => 'arialbd', 'Size' => 30, 'Just' => 0);
	safe_w_sql(sprintf($SQL, $Order++, 'Athlete', StrSafe_DB('GivCamel'), StrSafe_DB(serialize($Opts))));

	// Picture
	$Opts = Array ('X' => $Options->Settings["Width"] - 5 - $PhotoWidth, 'Y' => $TopPhoto, 'W' => $PhotoWidth, 'H' => $PhotoHeight);
	safe_w_sql(sprintf($SQL, $Order++, 'Picture', StrSafe_DB(''), StrSafe_DB(serialize($Opts))));

	// Flag
	$Opts = Array ('X' => 5, 'Y' => $TopFlag, 'W' => $FlagWidth, 'H' => $FlagHeight);
	safe_w_sql(sprintf($SQL, $Order++, 'Flag', StrSafe_DB(''), StrSafe_DB(serialize($Opts))));
	// Club Code
	$Opts = Array ('X' => 6+$FlagWidth, 'Y' => $TopFlag, 'W' => $ClubWidth, 'H' => $ClubHeigth, 'Col' => '#000000', 'BackCol' => '', 'Font' => 'arialbd', 'Size' => 30, 'Just' => 1);
	safe_w_sql(sprintf($SQL, $Order++, 'Club', StrSafe_DB('NocCaps'), StrSafe_DB(serialize($Opts))));
	// Club Name
	$Opts = Array ('X' => 6+$FlagWidth, 'Y' => $TopFlag+$ClubHeigth, 'W' => $ClubWidth, 'H' => $ClubHeigth, 'Col' => '#000000', 'BackCol' => '', 'Font' => 'arialbd', 'Size' => 20, 'Just' => 1);
	safe_w_sql(sprintf($SQL, $Order++, 'Club', StrSafe_DB('ClubCamel'), StrSafe_DB(serialize($Opts))));

	// Category
	$Opts = Array ('X' => 5, 'Y' => $TopCategory, 'W' => $Options->Settings["Width"] - 10, 'H' => $CategoryHeight, 'Col' => '#000000', 'BackCol' => '', 'BackCat' => 'on', 'Font' => 'arialbd', 'Size' => 25, 'Just' => 1);
	safe_w_sql(sprintf($SQL, $Order++, 'Category', StrSafe_DB(''), StrSafe_DB(serialize($Opts))));
}

function CreateDefaultE($CardNumber=0, $CardName='') {
	CreateDefaultQ($CardNumber, $CardName, 'E');
}

function CreateDefaultZ($CardNumber=0, $CardName='') {
	CreateDefaultY($CardNumber, $CardName, 'Z');
}

function CreateDefaultY($CardNumber=0, $CardName='', $CardType='Y') {
	global $CFG;
	$Options=emptyIdCard();
	$Options->Settings['Width']=$Options->Settings['PaperHeight'];
	$Options->Settings['Height']=$Options->Settings['PaperWidth'];
	$Options->Settings['PaperHeight']=$Options->Settings['Height'];
	$Options->Settings['PaperWidth']=$Options->Settings['Width'];
	$Options->Settings['OffsetX']=0;
	$Options->Settings['OffsetY']=0;
	if(!$CardName) $CardName=get_text($CardType.'-Badge', 'BackNumbers');

	$LogoHeight=50;
	$HeadWidth=$Options->Settings["Width"]-20;
	$HeadStart=10;

	safe_w_sql("insert ignore into IdCards set
			IcTournament={$_SESSION['TourId']},
			IcType='$CardType',
			IcNumber=$CardNumber,
			IcName=".StrSafe_DB($CardName).",
		IcSettings=".StrSafe_DB(serialize($Options->Settings)));

	$SQL="INSERT INTO IdCardElements set IceTournament={$_SESSION['TourId']}, IceCardType='$CardType', IceCardNumber=$CardNumber, IceOrder=%s, IceType='%s', IceContent=%s, IceOptions=%s";
	$Order=1;

	// logo sx
	if(file_exists($IM=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-ToLeft.jpg')) {
		$Opts = Array ('X' =>  10, 'Y' => 10, 'W' => 0, 'H' => $LogoHeight);
		safe_w_sql(sprintf($SQL, $Order++, 'ToLeft', StrSafe_DB(''), StrSafe_DB(serialize($Opts))));
		$l=getimagesize($IM);
		$HeadWidth-=($LogoHeight*$l[0]/$l[1] + 2);
		$HeadStart+=($LogoHeight*$l[0]/$l[1] + 2);
	}
	// logo dx
	if(file_exists($IM=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-ToRight.jpg')) {
		$l=getimagesize($IM);
		$HeadWidth-=($LogoHeight*$l[0]/$l[1] + 2);
		$Opts = Array ('X' => $Options->Settings["Width"]-10-($LogoHeight*$l[0]/$l[1]), 'Y' => 10, 'W' => 0, 'H' => $LogoHeight);
		safe_w_sql(sprintf($SQL, $Order++, 'ToRight', StrSafe_DB(''), StrSafe_DB(serialize($Opts))));
	}
	// logo bottom
	if(file_exists($IM=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-ToBottom.jpg')) {
		$l=getimagesize($IM);
		$tmp=(10*$l[0]/$l[1]);
		$Opts = Array ('X' => ($Options->Settings["Width"]-$tmp)/2, 'Y' => $Options->Settings["Height"]-20, 'W' => 0, 'H' => 10);
		safe_w_sql(sprintf($SQL, $Order++, 'ToBottom', StrSafe_DB(''), StrSafe_DB(serialize($Opts))));
	}

	$SQL="INSERT INTO IdCardElements set IceTournament={$_SESSION['TourId']}, IceCardType='$CardType', IceCardNumber=$CardNumber, IceOrder=%s, IceType='%s', IceContent=%s, IceOptions=%s";

	// Competition Name
	$Opts = Array ('X' => $HeadStart, 'Y' => 10, 'W' => $HeadWidth, 'H' => 20, 'Col' => '#000000', 'BackCol' => '', 'Font' => 'arialbd', 'Size' => 20, 'Just' => 1);
	safe_w_sql(sprintf($SQL, $Order++, 'CompName', StrSafe_DB(''), StrSafe_DB(serialize($Opts))));

	// Competition Detailss
	$Opts = Array ('X' => $HeadStart, 'Y' => 30, 'W' => $HeadWidth, 'H' => 20, 'Col' => '#000000', 'BackCol' => '', 'Font' => 'arialbd', 'Size' => 18, 'Just' => 1);
	safe_w_sql(sprintf($SQL, $Order++, 'CompDetails', StrSafe_DB(''), StrSafe_DB(serialize($Opts))));

	// Athlete
	$Opts = Array ('X' => 10, 'Y' => 60, 'W' => $Options->Settings["Width"] - 20, 'H' => 20, 'Col' => '#000000', 'BackCol' => '', 'Font' => 'timesbd', 'Size' => 30, 'Just' => 1);
	safe_w_sql(sprintf($SQL, $Order++, 'Athlete', StrSafe_DB('FamCaps-GivCamel'), StrSafe_DB(serialize($Opts))));

	// Flag
	$Opts = Array ('X' => 10, 'Y' => 85, 'W' => 30, 'H' => 20);
	safe_w_sql(sprintf($SQL, $Order++, 'Flag', StrSafe_DB(''), StrSafe_DB(serialize($Opts))));

	// ClubName
	$Opts = Array ('X' => 42, 'Y' => 85, 'W' => $Options->Settings["Width"]-52, 'H' => 20, 'Col' => '#000000', 'BackCol' => '', 'Font' => 'arialbd', 'Size' => 35, 'Just' => 0);
	safe_w_sql(sprintf($SQL, $Order++, 'Club', StrSafe_DB('ClubCamel'), StrSafe_DB(serialize($Opts))));

	// Category
	$Opts = Array ('X' => 10, 'Y' => 115, 'W' => $Options->Settings["Width"]-20, 'H' => 30, 'Col' => '#000000', 'BackCol' => '', 'Font' => 'helveticaneueltprob', 'Size' => 25, 'Just' => 1);
	safe_w_sql(sprintf($SQL, $Order++, 'Event', StrSafe_DB('EvDescr'), StrSafe_DB(serialize($Opts))));

	// ColoredArea
	$Opts = Array ('X' => 10, 'Y' => 150, 'W' => 140, 'H' => 15, 'Col' => '#000000', 'BackCol' => '', 'Font' => 'timesi', 'Size' => 30, 'Just' => 2);
	safe_w_sql(sprintf($SQL, $Order++, 'ColoredArea', StrSafe_DB(get_text('QualPosition', 'BackNumbers')), StrSafe_DB(serialize($Opts))));

	// Qual Position
	$Opts = Array ('X' => 155, 'Y' => 150, 'W' => 80, 'H' => 15, 'Col' => '#000000', 'BackCol' => '', 'Font' => 'timesbd', 'Size' => 30, 'Just' => 0);
	safe_w_sql(sprintf($SQL, $Order++, 'Ranking', StrSafe_DB(''), StrSafe_DB(serialize($Opts))));

	// ColoredArea
	$Opts = Array ('X' => 10, 'Y' => 170, 'W' => 140, 'H' => 15, 'Col' => '#000000', 'BackCol' => '', 'Font' => 'timesi', 'Size' => 30, 'Just' => 2);
	safe_w_sql(sprintf($SQL, $Order++, 'ColoredArea', StrSafe_DB(get_text('FinPosition', 'BackNumbers')), StrSafe_DB(serialize($Opts))));

	// Fin Position
	$Opts = Array ('X' => 155, 'Y' => 170, 'W' => 80, 'H' => 15, 'Col' => '#000000', 'BackCol' => '', 'Font' => 'timesbd', 'Size' => 30, 'Just' => 0);
	safe_w_sql(sprintf($SQL, $Order++, 'FinalRanking', StrSafe_DB(''), StrSafe_DB(serialize($Opts))));
}

function CreateDefaultQ($CardNumber=0, $CardName='', $CardType='Q') {
	global $CFG;
	$Options=emptyIdCard();
	$Options->Settings['Width']=$Options->Settings['PaperWidth'];
	$Options->Settings['OffsetX']=0;
	if(!$CardName) $CardName=($CardType=='Q' ? get_text('MenuLM_Qualification') : get_text('MenuLM_Eliminations'));

	$LogoHeight=ceil($Options->Settings["Height"]/3.5);
	$HeadWidth=$Options->Settings["Width"]-10;
	$HeadStart=5;

	$CompName=6;

	$NameHeight=intval($Options->Settings["Height"]/5);
	$FlagHeight=intval($Options->Settings["Height"]/7);
	$FlagWidth=$FlagHeight*3/2;

	$Gap=intval(($Options->Settings['Height']-12-$LogoHeight-$NameHeight-$FlagHeight-2*$CompName)/3);

	safe_w_sql("insert ignore into IdCards set
			IcTournament={$_SESSION['TourId']},
			IcType='$CardType',
			IcNumber=$CardNumber,
			IcName=".StrSafe_DB($CardName).",
		IcSettings=".StrSafe_DB(serialize($Options->Settings)));

	$SQL="INSERT INTO IdCardElements set IceTournament={$_SESSION['TourId']}, IceCardType='$CardType', IceCardNumber=$CardNumber, IceOrder=%s, IceType='%s', IceContent=%s, IceOptions=%s";
	$Order=1;

	// logo sx
	if(file_exists($IM=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-ToLeft.jpg')) {
		$Opts = Array ('X' =>  5, 'Y' => 5, 'W' => 0, 'H' => $LogoHeight);
		safe_w_sql(sprintf($SQL, $Order++, 'ToLeft', StrSafe_DB(''), StrSafe_DB(serialize($Opts))));
		$l=getimagesize($IM);
		$HeadWidth-=($LogoHeight*$l[0]/$l[1] + 1);
		$HeadStart+=($LogoHeight*$l[0]/$l[1] + 1);
	}
	// logo dx
	if(file_exists($IM=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-ToRight.jpg')) {
		$l=getimagesize($IM);
		$HeadWidth-=($LogoHeight*$l[0]/$l[1] + 1);
		$Opts = Array ('X' => $Options->Settings["Width"]-5-($LogoHeight*$l[0]/$l[1]), 'Y' => 5, 'W' => 0, 'H' => $LogoHeight);
		safe_w_sql(sprintf($SQL, $Order++, 'ToRight', StrSafe_DB(''), StrSafe_DB(serialize($Opts))));
	}
	// logo bottom
	if(file_exists($IM=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-ToBottom.jpg')) {
		$l=getimagesize($IM);
		$tmp=(10*$l[0]/$l[1]);
		$Opts = Array ('X' => ($Options->Settings["Width"]-$tmp)/2, 'Y' => $Options->Settings["Height"]-15, 'W' => 0, 'H' => 10);
		safe_w_sql(sprintf($SQL, $Order++, 'ToBottom', StrSafe_DB(''), StrSafe_DB(serialize($Opts))));
		$Gap=intval(($Options->Settings['Height']-23-$LogoHeight-$NameHeight-$FlagHeight-2*$CompName)/3);
	}

	// Target
	$Opts = Array ('X' => $HeadStart, 'Y' => 0, 'W' => $HeadWidth, 'H' => $LogoHeight-10, 'Col' => '#808080', 'BackCol' => '', 'Font' => 'arialbd', 'Size' => $LogoHeight*3, 'Just' => 1);
	safe_w_sql(sprintf($SQL, $Order++, 'Target', StrSafe_DB(''), StrSafe_DB(serialize($Opts))));

	// Line
	$Y=7+$LogoHeight;
	$Opts = Array ('X' => 5, 'Y' => $Y, 'W' => $Options->Settings["Width"] - 10, 'H' => 0.01, 'Col' => '#0000CC', 'BackCol' => '');
	safe_w_sql(sprintf($SQL, $Order++, 'HLine', StrSafe_DB(''), StrSafe_DB(serialize($Opts))));
	// Competition Name
	$Opts = Array ('X' => 5, 'Y' => $Y, 'W' => $Options->Settings["Width"] - 10, 'H' => $CompName, 'Col' => '#0000CC', 'BackCol' => '', 'Font' => 'arialbd', 'Size' => 2*$CompName, 'Just' => 1);
	safe_w_sql(sprintf($SQL, $Order++, 'CompName', StrSafe_DB(''), StrSafe_DB(serialize($Opts))));
	// Line
	$Y+=$CompName;
	$Opts = Array ('X' => 5, 'Y' => $Y, 'W' => $Options->Settings["Width"] - 10, 'H' => 0.01, 'Col' => '#0000CC', 'BackCol' => '');
	safe_w_sql(sprintf($SQL, $Order++, 'HLine', StrSafe_DB(''), StrSafe_DB(serialize($Opts))));

	// Athlete Family Name
	$Y+=$Gap;
	$Opts = Array ('X' => 5, 'Y' => intval($Y-$NameHeight/4), 'W' => $Options->Settings["Width"] - 10, 'H' => $NameHeight, 'Col' => '#000000', 'BackCol' => '', 'Font' => 'arialbd', 'Size' => intval($NameHeight*3.5), 'Just' => 0);
	safe_w_sql(sprintf($SQL, $Order++, 'Athlete', StrSafe_DB('FamCaps-GivCamel'), StrSafe_DB(serialize($Opts))));

	// Flag
	$Y+=$Gap+$NameHeight;
	$Opts = Array ('X' => 5, 'Y' => $Y, 'W' => $FlagWidth, 'H' => $FlagHeight);
	safe_w_sql(sprintf($SQL, $Order++, 'Flag', StrSafe_DB(''), StrSafe_DB(serialize($Opts))));
	// Club Code
	$Opts = Array ('X' => 6+$FlagWidth, 'Y' => intval($Y-$FlagHeight/4), 'W' => $Options->Settings["Width"] - $FlagWidth - 11, 'H' => $FlagHeight, 'Col' => '#008000', 'BackCol' => '', 'Font' => 'arialbd', 'Size' => $FlagHeight*3.7, 'Just' => 0);
	safe_w_sql(sprintf($SQL, $Order++, 'Club', StrSafe_DB('ClubCamel'), StrSafe_DB(serialize($Opts))));

	// Line
	$Y+=$Gap+$FlagHeight;
	$Opts = Array ('X' => 5, 'Y' => $Y, 'W' => $Options->Settings["Width"] - 10, 'H' => 0.01, 'Col' => '#0000CC', 'BackCol' => '');
	safe_w_sql(sprintf($SQL, $Order++, 'HLine', StrSafe_DB(''), StrSafe_DB(serialize($Opts))));
	// Competition Details
	$Opts = Array ('X' => 5, 'Y' => $Y, 'W' => $Options->Settings["Width"] - 10, 'H' => $CompName, 'Col' => '#0000CC', 'BackCol' => '', 'Font' => 'arialbd', 'Size' => 12, 'Just' => 1);
	safe_w_sql(sprintf($SQL, $Order++, 'CompDetails', StrSafe_DB(''), StrSafe_DB(serialize($Opts))));
	// Line
	$Opts = Array ('X' => 5, 'Y' => $Y+$CompName, 'W' => $Options->Settings["Width"] - 10, 'H' => 0.01, 'Col' => '#0000CC', 'BackCol' => '');
	safe_w_sql(sprintf($SQL, $Order++, 'HLine', StrSafe_DB(''), StrSafe_DB(serialize($Opts))));
}

function CreateDefaultI($CardNumber=0, $CardName='') {
	global $CFG;
	$Options=emptyIdCard();
	$Options->Settings['Width']=$Options->Settings['PaperWidth'];
	$Options->Settings['OffsetX']=0;
	if(!$CardName) $CardName=get_text('I-Session', 'Tournament');

	$LogoHeight=ceil($Options->Settings["Height"]/3.5);
	$HeadWidth=$Options->Settings["Width"]-10;
	$HeadStart=5;

	$CompName=6;

	$NameHeight=intval($Options->Settings["Height"]/5.5);


	$FlagHeight=intval($Options->Settings["Height"]/18)*2;
	$FlagWidth=$FlagHeight*3/2;

	$Gap=intval(($Options->Settings['Height']-12-($LogoHeight+$NameHeight+$FlagHeight+2*$CompName))/3);

	safe_w_sql("insert ignore into IdCards set
			IcTournament={$_SESSION['TourId']},
			IcType='I',
			IcNumber=$CardNumber,
			IcName=".StrSafe_DB($CardName).",
		IcSettings=".StrSafe_DB(serialize($Options->Settings)));

	$SQL="INSERT INTO IdCardElements set IceTournament={$_SESSION['TourId']}, IceCardType='I', IceCardNumber=$CardNumber, IceOrder=%s, IceType='%s', IceContent=%s, IceOptions=%s";
	$Order=1;
	// logo sx
	if(file_exists($IM=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-ToLeft.jpg')) {
		$Opts = Array ('X' =>  5, 'Y' => 5, 'W' => 0, 'H' => $LogoHeight);
		safe_w_sql(sprintf($SQL, $Order++, 'ToLeft', StrSafe_DB(''), StrSafe_DB(serialize($Opts))));
		$l=getimagesize($IM);
		$HeadWidth-=($LogoHeight*$l[0]/$l[1] + 1);
		$HeadStart+=($LogoHeight*$l[0]/$l[1] + 1);
	}
	// logo dx
	if(file_exists($IM=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-ToRight.jpg')) {
		$l=getimagesize($IM);
		$HeadWidth-=($LogoHeight*$l[0]/$l[1] + 1);
		$Opts = Array ('X' => $Options->Settings["Width"]-5-($LogoHeight*$l[0]/$l[1]), 'Y' => 5, 'W' => 0, 'H' => $LogoHeight);
		safe_w_sql(sprintf($SQL, $Order++, 'ToRight', StrSafe_DB(''), StrSafe_DB(serialize($Opts))));
	}
	// logo bottom
	if(file_exists($IM=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-ToBottom.jpg')) {
		$l=getimagesize($IM);
		$tmp=(10*$l[0]/$l[1]);
		$Opts = Array ('X' => ($Options->Settings["Width"]-$tmp)/2, 'Y' => $Options->Settings["Height"]-15, 'W' => 0, 'H' => 10);
		safe_w_sql(sprintf($SQL, $Order++, 'ToBottom', StrSafe_DB(''), StrSafe_DB(serialize($Opts))));
		$Gap=intval(($Options->Settings['Height']-23-($LogoHeight+$NameHeight+$FlagHeight+2*$CompName))/3);
	}

	// Event
	$Opts = Array ('X' => $HeadStart, 'Y' => 0, 'W' => intval($HeadWidth/2), 'H' => $LogoHeight-15, 'Col' => '#990000', 'BackCol' => '', 'Font' => 'arialbd', 'Size' => $LogoHeight*3, 'Just' => 1);
	safe_w_sql(sprintf($SQL, $Order++, 'Event', StrSafe_DB('EvCode'), StrSafe_DB(serialize($Opts))));
	// Ranking
	$Opts = Array ('X' => $HeadStart+intval($HeadWidth/2), 'Y' => 0, 'W' => intval($HeadWidth/2), 'H' => $LogoHeight-15, 'Col' => '#666666', 'BackCol' => '', 'Font' => 'arialbd', 'Size' => $LogoHeight*3, 'Just' => 1);
	safe_w_sql(sprintf($SQL, $Order++, 'Ranking', StrSafe_DB(''), StrSafe_DB(serialize($Opts))));

	// Line
	$Y=7+$LogoHeight;
	$Opts = Array ('X' => 5, 'Y' => $Y, 'W' => $Options->Settings["Width"] - 10, 'H' => 0.01, 'Col' => '#0000CC', 'BackCol' => '');
	safe_w_sql(sprintf($SQL, $Order++, 'HLine', StrSafe_DB(''), StrSafe_DB(serialize($Opts))));
	// Competition Name
	$Opts = Array ('X' => 5, 'Y' => $Y, 'W' => $Options->Settings["Width"] - 10, 'H' => $CompName, 'Col' => '#0000CC', 'BackCol' => '', 'Font' => 'arialbd', 'Size' => $CompName*2, 'Just' => 1);
	safe_w_sql(sprintf($SQL, $Order++, 'CompName', StrSafe_DB(''), StrSafe_DB(serialize($Opts))));
	// Line
	$Y+=$CompName;
	$Opts = Array ('X' => 5, 'Y' => $Y, 'W' => $Options->Settings["Width"] - 10, 'H' => 0.01, 'Col' => '#0000CC', 'BackCol' => '');
	safe_w_sql(sprintf($SQL, $Order++, 'HLine', StrSafe_DB(''), StrSafe_DB(serialize($Opts))));

	// Athlete Family Name
	$Y+=$Gap;
	$Opts = Array ('X' => 5, 'Y' => intval($Y-($NameHeight/4)), 'W' => $Options->Settings["Width"] - 10, 'H' => $NameHeight, 'Col' => '#000000', 'BackCol' => '', 'Font' => 'arialbd', 'Size' => $NameHeight*3.5, 'Just' => 0);
	safe_w_sql(sprintf($SQL, $Order++, 'Athlete', StrSafe_DB('FamCaps-GivCamel'), StrSafe_DB(serialize($Opts))));

	// Flag
	$Y+=$Gap+$NameHeight;
	$Opts = Array ('X' => 5, 'Y' => $Y, 'W' => $FlagWidth, 'H' => $FlagHeight);
	safe_w_sql(sprintf($SQL, $Order++, 'Flag', StrSafe_DB(''), StrSafe_DB(serialize($Opts))));
	// Club Code
	$Opts = Array ('X' => 6+$FlagWidth, 'Y' => intval($Y - ($FlagHeight/4)), 'W' => $Options->Settings["Width"] - $FlagWidth -11, 'H' => $FlagHeight, 'Col' => '#008000', 'BackCol' => '', 'Font' => 'arialbd', 'Size' => $FlagHeight*3.7, 'Just' => 0);
	safe_w_sql(sprintf($SQL, $Order++, 'Club', StrSafe_DB('ClubCamel'), StrSafe_DB(serialize($Opts))));

	// Line
	$Y+=$Gap+$FlagHeight;
	$Opts = Array ('X' => 5, 'Y' => $Y, 'W' => $Options->Settings["Width"] - 10, 'H' => 0.01, 'Col' => '#0000CC', 'BackCol' => '');
	safe_w_sql(sprintf($SQL, $Order++, 'HLine', StrSafe_DB(''), StrSafe_DB(serialize($Opts))));
	// Competition Details
	$Opts = Array ('X' => 5, 'Y' => $Y, 'W' => $Options->Settings["Width"] - 10, 'H' => $CompName, 'Col' => '#0000CC', 'BackCol' => '', 'Font' => 'arialbd', 'Size' => $CompName*2, 'Just' => 1);
	safe_w_sql(sprintf($SQL, $Order++, 'CompDetails', StrSafe_DB(''), StrSafe_DB(serialize($Opts))));
	// Line
	$Opts = Array ('X' => 5, 'Y' => $Y+$CompName, 'W' => $Options->Settings["Width"] - 10, 'H' => 0.01, 'Col' => '#0000CC', 'BackCol' => '');
	safe_w_sql(sprintf($SQL, $Order++, 'HLine', StrSafe_DB(''), StrSafe_DB(serialize($Opts))));
}

function CreateDefaultT($CardNumber=0, $CardName='') {
	global $CFG;
	$Options=emptyIdCard();
	$Options->Settings['Width']=$Options->Settings['PaperWidth'];
	$Options->Settings['OffsetX']=0;
	if(!$CardName) $CardName=get_text('T-Session', 'Tournament');

	$LogoHeight=ceil($Options->Settings["Height"]/3.5);
	$HeadWidth=$Options->Settings["Width"]-10;
	$HeadStart=5;

	$CompName=6;

	$NameHeight=intval($Options->Settings["Height"]/9);

	$FlagHeight=intval($Options->Settings["Height"]/11)*2;
	$FlagWidth=$FlagHeight*3/2;

	$CompHeight=intval($Options->Settings["Height"]/12);

	$Gap=intval(($Options->Settings['Height']-12-($LogoHeight+$NameHeight+$FlagHeight+2*$CompName+$CompHeight))/4);

	safe_w_sql("insert ignore into IdCards set
			IcTournament={$_SESSION['TourId']},
			IcType='T',
			IcNumber=$CardNumber,
			IcName=".StrSafe_DB($CardName).",
		IcSettings=".StrSafe_DB(serialize($Options->Settings)));

	$SQL="INSERT INTO IdCardElements set IceTournament={$_SESSION['TourId']}, IceCardType='T', IceCardNumber=$CardNumber, IceOrder=%s, IceType='%s', IceContent=%s, IceOptions=%s";
	$Order=1;
	// logo sx
	if(file_exists($IM=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-ToLeft.jpg')) {
		$Opts = Array ('X' =>  5, 'Y' => 5, 'W' => 0, 'H' => $LogoHeight);
		safe_w_sql(sprintf($SQL, $Order++, 'ToLeft', StrSafe_DB(''), StrSafe_DB(serialize($Opts))));
		$l=getimagesize($IM);
		$HeadWidth-=($LogoHeight*$l[0]/$l[1] + 1);
		$HeadStart+=($LogoHeight*$l[0]/$l[1] + 1);
	}
	// logo dx
	if(file_exists($IM=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-ToRight.jpg')) {
		$l=getimagesize($IM);
		$HeadWidth-=($LogoHeight*$l[0]/$l[1] + 1);
		$Opts = Array ('X' => $Options->Settings["Width"]-5-($LogoHeight*$l[0]/$l[1]), 'Y' => 5, 'W' => 0, 'H' => $LogoHeight);
		safe_w_sql(sprintf($SQL, $Order++, 'ToRight', StrSafe_DB(''), StrSafe_DB(serialize($Opts))));
	}
	// logo bottom
	if(file_exists($IM=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-ToBottom.jpg')) {
		$l=getimagesize($IM);
		$tmp=(10*$l[0]/$l[1]);
		$Opts = Array ('X' => ($Options->Settings["Width"]-$tmp)/2, 'Y' => $Options->Settings["Height"]-15, 'W' => 0, 'H' => 10);
		safe_w_sql(sprintf($SQL, $Order++, 'ToBottom', StrSafe_DB(''), StrSafe_DB(serialize($Opts))));
		$Gap=intval(($Options->Settings['Height']-23-($LogoHeight+$NameHeight+$FlagHeight+2*$CompName+$CompHeight))/4);
	}

	// Event
	$Opts = Array ('X' => $HeadStart, 'Y' => 0, 'W' => intval($HeadWidth/2), 'H' => $LogoHeight-15, 'Col' => '#990000', 'BackCol' => '', 'Font' => 'arialbd', 'Size' => $LogoHeight*3, 'Just' => 1);
	safe_w_sql(sprintf($SQL, $Order++, 'Event', StrSafe_DB('EvCode'), StrSafe_DB(serialize($Opts))));
	// Ranking
	$Opts = Array ('X' => $HeadStart+intval($HeadWidth/2), 'Y' => 0, 'W' => intval($HeadWidth/2), 'H' => $LogoHeight-15, 'Col' => '#666666', 'BackCol' => '', 'Font' => 'arialbd', 'Size' => $LogoHeight*3, 'Just' => 1);
	safe_w_sql(sprintf($SQL, $Order++, 'Ranking', StrSafe_DB(''), StrSafe_DB(serialize($Opts))));

	// Line
	$Y=7+$LogoHeight;
	$Opts = Array ('X' => 5, 'Y' => $Y, 'W' => $Options->Settings["Width"] - 10, 'H' => 0.01, 'Col' => '#0000CC', 'BackCol' => '');
	safe_w_sql(sprintf($SQL, $Order++, 'HLine', StrSafe_DB(''), StrSafe_DB(serialize($Opts))));
	// Competition Name
	$Opts = Array ('X' => 5, 'Y' => $Y, 'W' => $Options->Settings["Width"] - 10, 'H' => $CompName, 'Col' => '#0000CC', 'BackCol' => '', 'Font' => 'arialbd', 'Size' => $CompName*2, 'Just' => 1);
	safe_w_sql(sprintf($SQL, $Order++, 'CompName', StrSafe_DB(''), StrSafe_DB(serialize($Opts))));
	// Line
	$Y+=$CompName;
	$Opts = Array ('X' => 5, 'Y' => $Y, 'W' => $Options->Settings["Width"] - 10, 'H' => 0.01, 'Col' => '#0000CC', 'BackCol' => '');
	safe_w_sql(sprintf($SQL, $Order++, 'HLine', StrSafe_DB(''), StrSafe_DB(serialize($Opts))));


	// Athlete Family Name
	$Y+=$Gap;
	$Opts = Array ('X' => 5, 'Y' => intval($Y-$NameHeight/4), 'W' => $Options->Settings["Width"] - 10, 'H' => $NameHeight, 'Col' => '#000000', 'BackCol' => '', 'Font' => 'arialbd', 'Size' => $NameHeight*2.8, 'Just' => 1);
	safe_w_sql(sprintf($SQL, $Order++, 'Athlete', StrSafe_DB('FamCaps-GivCamel'), StrSafe_DB(serialize($Opts))));

	// Club Code
	$Y+=$Gap+$NameHeight;
	$Opts = Array ('X' => 5, 'Y' => intval($Y-$FlagHeight/4), 'W' => $Options->Settings["Width"] - $FlagWidth - 11, 'H' => $FlagHeight, 'Col' => '#008000', 'BackCol' => '', 'Font' => 'arialbd', 'Size' => $FlagHeight*3.7, 'Just' => 0);
	safe_w_sql(sprintf($SQL, $Order++, 'Club', StrSafe_DB('ClubCamel'), StrSafe_DB(serialize($Opts))));
	// Flag
	$Opts = Array ('X' => $Options->Settings["Width"]-$FlagWidth-5, 'Y' => $Y, 'W' => $FlagWidth, 'H' => $FlagHeight);
	safe_w_sql(sprintf($SQL, $Order++, 'Flag', StrSafe_DB(''), StrSafe_DB(serialize($Opts))));

	// Team Components
	$Y+=$Gap+$FlagHeight;
	$Opts = Array ('X' => 5, 'Y' => $Y, 'W' => $Options->Settings["Width"] - 10, 'H' => $CompHeight, 'Col' => '#000000', 'BackCol' => '', 'Font' => 'arialbd', 'Size' => $CompHeight*2.8, 'Just' => 1);
	safe_w_sql(sprintf($SQL, $Order++, 'TeamComponents', StrSafe_DB('OneLine'), StrSafe_DB(serialize($Opts))));

	// Line
	$Y+=$Gap+$CompHeight;
	$Opts = Array ('X' => 5, 'Y' => $Y, 'W' => $Options->Settings["Width"] - 10, 'H' => 0.01, 'Col' => '#0000CC', 'BackCol' => '');
	safe_w_sql(sprintf($SQL, $Order++, 'HLine', StrSafe_DB(''), StrSafe_DB(serialize($Opts))));
	// Competition Details
	$Opts = Array ('X' => 5, 'Y' => $Y, 'W' => $Options->Settings["Width"] - 10, 'H' => $CompName, 'Col' => '#0000CC', 'BackCol' => '', 'Font' => 'arialbd', 'Size' => $CompName*2, 'Just' => 1);
	safe_w_sql(sprintf($SQL, $Order++, 'CompDetails', StrSafe_DB(''), StrSafe_DB(serialize($Opts))));
	// Line
	$Opts = Array ('X' => 5, 'Y' => $Y+$CompName, 'W' => $Options->Settings["Width"] - 10, 'H' => 0.01, 'Col' => '#0000CC', 'BackCol' => '');
	safe_w_sql(sprintf($SQL, $Order++, 'HLine', StrSafe_DB(''), StrSafe_DB(serialize($Opts))));
}
