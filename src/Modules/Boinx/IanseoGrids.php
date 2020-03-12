<?php
require_once('./config.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Lib/ArrTargets.inc.php');
require_once('Common/Fun_Phases.inc.php');
//require_once('Common/Fun_Phases.inc.php');

// checks and creates the necessary flags/pictures
include('Common/CheckPictures.php');
CheckPictures($TourCode);

$Ind=array();
$Team=array();

// finds out what is to be served!
$MyQuery="select * from BoinxSchedule where (BsType like 'Grd\\_%') and BsTournament=$TourId "
	. "ORDER BY"
	. " BsType";

$Rs=safe_r_sql($MyQuery);
while($MyRow=safe_fetch($Rs) ) {
	$tmp= explode('_', $MyRow->BsType);
	if($tmp[1]=='Ind') {
		$Ind[]=$tmp[2];
	} elseif($tmp[1]=='Team') {
		$Team[]=$tmp[2];
	}
}

if(!$Ind and !$Team) {
	die("Nothing selected!");
}

//Genero la query che mi ritorna tutti i podi individuali
$MyQuery = "(SELECT"
	. " EvEventName"
	. " , f1.FinAthlete as AthId"
	. " , CONCAT_WS(' ', EnFirstName, EnName) as Atleta"
	. " , f1.FinMatchNo pos"
	. " , CoCode"
	. " , EnIocCode"
	. " , EvTeamEvent"
	. " , EvProgr"
	. " , EvCode"
	. " , f1.FinMatchNo"
	. " , IF(EvMatchMode=0,f1.FinScore,f1.FinSetScore) AS Score "
	. " , f1.FinTie "
	. " , IF(EvMatchMode=0,f1.FinScore>f2.FinScore,f1.FinSetScore>f2.FinSetScore) or f1.FinTie>0 AS Winner "
	. "FROM"
	. " Finals f1"
	. " INNER JOIN Events on f1.FinEvent=EvCode and EvTeamEvent=0 and f1.FinTournament=EvTournament "
	. " LEFT JOIN Finals f2 on f1.FinEvent=f2.FinEvent and f1.FinTournament=f2.FinTournament and f2.FinMatchNo=if(f1.FinMatchNo%2, f1.FinMatchNo-1, f1.FinMatchNo+1) "
	. " LEFT JOIN Entries on f1.FinAthlete=EnId "
	. " LEFT JOIN Countries on EnCountry=CoId "
	. "WHERE"
	. " f1.FinTournament = $TourId"
	. " AND f1.FinMatchNo < 16 "
	. " AND EvCode in ('".implode("','", $Ind)."') "
	. "ORDER BY"
	. " EvProgr ASC, EvCode, f1.FinMatchNo) "
	. "UNION "
	. "(SELECT"
	. " EvEventName"
	. " , CoId as AthId "
	. " , CoName as Atleta "
	. " , f1.TfMatchNo "
	. " , CoCode "
	. " , CoIocCode "
	. " , EvTeamEvent "
	. " , EvProgr "
	. " , EvCode "
	. " , f1.TfMatchNo "
	. " , IF(EvMatchMode=0,f1.TfScore,f1.TfSetScore) AS Score "
	. " , f1.TfTie "
	. " , IF(EvMatchMode=0,f1.TfScore>f2.TfScore,f1.TfSetScore>f2.TfSetScore) or f1.TfTie>0 AS Winner "
	. "FROM "
	. " TeamFinals f1 "
	. " INNER JOIN Events on f1.TfEvent=EvCode and EvTeamEvent=1 and f1.TfTournament=EvTournament "
	. " INNER JOIN TeamFinals f2 on f1.TfEvent=f2.TfEvent and f1.TfTournament=f2.TfTournament and f2.TfMatchNo=if(f1.TfMatchNo%2, f1.TfMatchNo-1, f1.TfMatchNo+1) "
	. " INNER JOIN Countries on f1.TfTeam=CoId "
	. "WHERE"
	. " f1.TfTournament = $TourId"
	. " AND f1.TfMatchNo < 16 "
	. " AND EvCode in ('".implode("','", $Team)."') "
	. "ORDER BY"
	. " EvProgr ASC, EvCode, f1.TfMatchNo) "
	. "ORDER BY"
	. " EvTeamEvent, EvProgr, EvCode, FinMatchNo";
	$Rs=safe_r_sql($MyQuery);

$XmlDoc = new DOMDocument('1.0', 'UTF-8');
$XmlRoot = $XmlDoc->createElement('Ianseogrid');
$XmlDoc->appendChild($XmlRoot);


$StartEvent='a';
$OldEvent='';
$HighLighList=array();
$fotodir='http://' . $_SERVER['HTTP_HOST'] . $CFG->ROOT_DIR . 'TV/Photos/' . $TourCodeSafe . '-%s-%s.jpg';

while ($MyRow=safe_fetch($Rs)) {
	if($OldEvent!=$MyRow->EvEventName) {
		$Item = $XmlDoc->createElement($StartEvent);
		$XmlRoot->appendChild($Item);

		$Item->appendChild($XmlDoc->createElement('header', get_text($MyRow->EvEventName, null, null, true)));
		$OldEvent=$MyRow->EvEventName;
		$HighLighList=array();
		$StartEvent++;
	}

	if(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-Fl-'.$MyRow->CoCode.'.jpg')) {
		$Flag=sprintf($fotodir, 'Fl', $MyRow->CoCode);
	} elseif(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-Fl-'.$MyRow->EnIocCode.'.jpg')) {
		$Flag=sprintf($fotodir, 'Fl', $MyRow->EnIocCode);
	} else {
		$Flag='';
	}
	if($MyRow->pos<2)
		$HighLighList[]=$MyRow->AthId;

	$Item->appendChild($XmlDoc->createElement('id_'.$MyRow->pos, $MyRow->Atleta));
	$Item->appendChild($XmlDoc->createElement('m'.$MyRow->pos, $MyRow->AthId!=0 && $MyRow->Winner ? 2 : 1));
	$Item->appendChild($XmlDoc->createElement('s'.$MyRow->pos, $MyRow->FinTie==2 ? get_text('Bye') : ($MyRow->AthId!=0 ? $MyRow->Score.($MyRow->FinTie?'*':'') : '')));
	$Item->appendChild($XmlDoc->createElement('flag_'.$MyRow->pos, $Flag));
	$Item->appendChild($XmlDoc->createElement('c'.$MyRow->pos, $MyRow->CoCode));
}

header('Cache-Control: no-store, no-cache, must-revalidate');
header('Content-type: text/xml; charset=' . PageEncode);
echo $XmlDoc->SaveXML();

/*

<?xml version="1.0"?>
 <Ianseogrid>

            <a> evento (progressivo a,b,c,d,e,f,ecc)
                <header>Recurve Men</header>     Titolo evento
                <id_8>Scarzella</id_8>   nome atleta (0,1 finale  2,3 finalina ecc)
                <id_9>Pisani</id_9>
                <id_10>Deligant</id_10>
                <id_11>Scarzella</id_11>
                <id_12>Pisani</id_12>
                <id_13>Deligant</id_13>
                <id_14>Scarzella</id_14>
                <id_15>Pisani</id_15>
                <id_4>Deligant</id_4>
                <id_5>Scarzella</id_5>
                <id_6>Pisani</id_6>
                <id_7>Deligant</id_7>
                <id_0>Pisani</id_0>
                <id_1>Deligant</id_1>
                <id_2>Deligant</id_2>
                <id_3>Scarzella</id_3>
                <m0>1</m0>   stato atleta (1=scontro perso, 2= scontro vinto)
                <m1>1</m1>
                <m2>2</m2>
                <m3>1</m3>
                <m4>1</m4>
                <m5>2</m5>
                <m6>1</m6>
                <m7>1</m7>
                <m8>1</m8>
                <m9>1</m9>
                <m10>2</m10>
                <m11>1</m11>
                <m12>2</m12>
                <m13>1</m13>
                <m14>2</m14>
                <m15>1</m15>
                <m16>2</m16>
                <flag_0>http://localhost:8888/ianseo/Boinx/flag1.jpg</flag_0> bandiera atleta
                <flag_1>http://localhost:8888/ianseo/Boinx/flag2.gif</flag_1>
                <flag_2>http://localhost:8888/ianseo/Boinx/flag1.jpg</flag_2>
                <flag_3>http://localhost:8888/ianseo/Boinx/flag3.gif</flag_3>
                <flag_4>http://localhost:8888/ianseo/Boinx/flag1.jpg</flag_4>
                <flag_5>http://localhost:8888/ianseo/Boinx/flag3.gif</flag_5>
                <flag_6>http://localhost:8888/ianseo/Boinx/flag2.gif</flag_6>
                <flag_7>http://localhost:8888/ianseo/Boinx/flag1.jpg</flag_7>
                <flag_8>http://localhost:8888/ianseo/Boinx/flag1.jpg</flag_8>
                <flag_9>http://localhost:8888/ianseo/Boinx/flag1.jpg</flag_9>
                <flag_10>http://localhost:8888/ianseo/Boinx/flag1.jpg</flag_10>
                <flag_11>http://localhost:8888/ianseo/Boinx/flag3.gif</flag_11>
                <flag_12>http://localhost:8888/ianseo/Boinx/flag2.gif</flag_12>
                <flag_13>http://localhost:8888/ianseo/Boinx/flag1.jpg</flag_13>
                <flag_14>http://localhost:8888/ianseo/Boinx/flag2.gif</flag_14>
                <flag_15>http://localhost:8888/ianseo/Boinx/flag1.jpg</flag_15>
                <s0>1</s0> Punti atleta (score o set)
                <s1>121</s1>
                <s2>2</s2>
                <s3>133</s3>
                <s4>1</s4>
                <s5>2</s5>
                <s6>100</s6>
                <s7>1</s7>
                <s8>1</s8>
                <s9>1</s9>
                <s10>200</s10>
                <s11>1</s11>
                <s12>102</s12>
                <s13>1</s13>
                <s14>2</s14>
                <s15>1</s15>
                <s16>2</s16>
            </a>
            <b>
                <header>Recurve Women</header>
                <id_8>q1ba</id_8>
                <id_9>q2ba</id_9>
                <id_10>q3ba</id_10>
                <id_11>q4ba</id_11>
                <id_12>q5ba</id_12>
                <id_13>q6ba</id_13>
                <id_14>q7ba</id_14>
                <id_15>q8ba</id_15>
                <id_4>s1ba</id_4>
                <id_5>s2ba</id_5>
                <id_6>s3ba</id_6>
                <id_7>s4ba</id_7>
                <id_0>f1ba</id_0>
                <id_1>f2ba</id_1>
                <id_2>f3ba</id_2>
                <id_3>f4ba</id_3>
                <m0>1</m0>
                <m1>1</m1>
                <m2>2</m2>
                <m3>1</m3>
                <m4>1</m4>
                <m5>2</m5>
                <m6>1</m6>
                <m7>1</m7>
                <m8>1</m8>
                <m9>1</m9>
                <m10>2</m10>
                <m11>1</m11>
                <m12>2</m12>
                <m13>1</m13>
                <m14>2</m14>
                <m15>1</m15>
                <m16>2</m16>
                <flag_0>http://localhost:8888/ianseo/Boinx/flag2.gif</flag_0>
                <flag_1>http://localhost:8888/ianseo/Boinx/flag1.jpg</flag_1>
                <flag_2>http://localhost:8888/ianseo/Boinx/flag1.jpg</flag_2>
                <flag_3>http://localhost:8888/ianseo/Boinx/flag2.gif</flag_3>
                <flag_4>http://localhost:8888/ianseo/Boinx/flag3.gif</flag_4>
                <flag_5>http://localhost:8888/ianseo/Boinx/flag1.jpg</flag_5>
                <flag_6>http://localhost:8888/ianseo/Boinx/flag1.jpg</flag_6>
                <flag_7>http://localhost:8888/ianseo/Boinx/flag3.gif</flag_7>
                <flag_8>http://localhost:8888/ianseo/Boinx/flag2.gif</flag_8>
                <flag_9>http://localhost:8888/ianseo/Boinx/flag1.jpg</flag_9>
                <flag_10>http://localhost:8888/ianseo/Boinx/flag1.jpg</flag_10>
                <flag_11>http://localhost:8888/ianseo/Boinx/flag1.jpg</flag_11>
                <flag_12>http://localhost:8888/ianseo/Boinx/flag3.gif</flag_12>
                <flag_13>http://localhost:8888/ianseo/Boinx/flag1.jpg</flag_13>
                <flag_14>http://localhost:8888/ianseo/Boinx/flag2.gif</flag_14>
                <flag_15>http://localhost:8888/ianseo/Boinx/flag1.jpg</flag_15>
                <s0>1</s0>
                <s1>1</s1>
                <s2>2</s2>
                <s3>1</s3>
                <s4>1</s4>
                <s5>2</s5>
                <s6>1</s6>
                <s7>1</s7>
                <s8>1</s8>
                <s9>1</s9>
                <s10>2</s10>
                <s11>1</s11>
                <s12>2</s12>
                <s13>1</s13>
                <s14>2</s14>
                <s15>1</s15>
                <s16>2</s16>
            </b>
            <c>
                <header>Compound Men</header>
                <id_8>q1ac</id_8>
                <id_9>q2ac</id_9>
                <id_10>q3ac</id_10>
                <id_11>q4ac</id_11>
                <id_12>q5ac</id_12>
                <id_13>q6ac</id_13>
                <id_14>q7ac</id_14>
                <id_15>q8ac</id_15>
                <id_4>s1ac</id_4>
                <id_5>s2ac</id_5>
                <id_6>s3ac</id_6>
                <id_7>s4ac</id_7>
                <id_0>f1ac</id_0>
                <id_1>f2ac</id_1>
                <id_2>f3ac</id_2>
                <id_3>f4ac</id_3>
                <m0>2</m0>
                <m1>1</m1>
                <m2>2</m2>
                <m3>1</m3>
                <m4>1</m4>
                <m5>2</m5>
                <m6>1</m6>
                <m7>1</m7>
                <m8>1</m8>
                <m9>1</m9>
                <m10>2</m10>
                <m11>1</m11>
                <m12>2</m12>
                <m13>1</m13>
                <m14>2</m14>
                <m15>1</m15>
                <m16>2</m16>
                <flag_0>http://localhost:8888/ianseo/Boinx/flag1.jpg</flag_0>
                <flag_1>http://localhost:8888/ianseo/Boinx/flag2.gif</flag_1>
                <flag_2>http://localhost:8888/ianseo/Boinx/flag1.jpg</flag_2>
                <flag_3>http://localhost:8888/ianseo/Boinx/flag3.gif</flag_3>
                <flag_4>http://localhost:8888/ianseo/Boinx/flag1.jpg</flag_4>
                <flag_5>http://localhost:8888/ianseo/Boinx/flag2.gif</flag_5>
                <flag_6>http://localhost:8888/ianseo/Boinx/flag1.jpg</flag_6>
                <flag_7>http://localhost:8888/ianseo/Boinx/flag3.gif</flag_7>
                <flag_8>http://localhost:8888/ianseo/Boinx/flag3.gif</flag_8>
                <flag_9>http://localhost:8888/ianseo/Boinx/flag1.jpg</flag_9>
                <flag_10>http://localhost:8888/ianseo/Boinx/flag3.gif</flag_10>
                <flag_11>http://localhost:8888/ianseo/Boinx/flag1.jpg</flag_11>
                <flag_12>http://localhost:8888/ianseo/Boinx/flag2.gif</flag_12>
                <flag_13>http://localhost:8888/ianseo/Boinx/flag1.jpg</flag_13>
                <flag_14>http://localhost:8888/ianseo/Boinx/flag2.gif</flag_14>
                <flag_15>http://localhost:8888/ianseo/Boinx/flag1.jpg</flag_15>
                <s0>1</s0>
                <s1>1</s1>
                <s2>2</s2>
                <s3>1</s3>
                <s4>1</s4>
                <s5>2</s5>
                <s6>1</s6>
                <s7>1</s7>
                <s8>1</s8>
                <s9>1</s9>
                <s10>2</s10>
                <s11>1</s11>
                <s12>2</s12>
                <s13>1</s13>
                <s14>2</s14>
                <s15>1</s15>
                <s16>2</s16>
            </c>
            <d>
                <header>Compound Women</header>
                <id_8>q1ad</id_8>
                <id_9>q2ad</id_9>
                <id_10>q3ad</id_10>
                <id_11>q4ad</id_11>
                <id_12>q5ad</id_12>
                <id_13>q6ad</id_13>
                <id_14>q7ad</id_14>
                <id_15>q8ad</id_15>
                <id_4>s1ad</id_4>
                <id_5>s2ad</id_5>
                <id_6>s3ad</id_6>
                <id_7>s4ad</id_7>
                <id_0>f1ad</id_0>
                <id_1>f2ad</id_1>
                <id_2>f3ad</id_2>
                <id_3>f4ad</id_3>
                <m0>1</m0>
                <m1>1</m1>
                <m2>2</m2>
                <m3>1</m3>
                <m4>1</m4>
                <m5>2</m5>
                <m6>1</m6>
                <m7>1</m7>
                <m8>1</m8>
                <m9>1</m9>
                <m10>2</m10>
                <m11>1</m11>
                <m12>2</m12>
                <m13>1</m13>
                <m14>2</m14>
                <m15>1</m15>
                <m16>2</m16>
                <flag_0>http://localhost:8888/ianseo/Boinx/flag1.jpg</flag_0>
                <flag_1>http://localhost:8888/ianseo/Boinx/flag1.jpg</flag_1>
                <flag_2>http://localhost:8888/ianseo/Boinx/flag1.jpg</flag_2>
                <flag_3>http://localhost:8888/ianseo/Boinx/flag1.jpg</flag_3>
                <flag_4>http://localhost:8888/ianseo/Boinx/flag1.jpg</flag_4>
                <flag_5>http://localhost:8888/ianseo/Boinx/flag1.jpg</flag_5>
                <flag_6>http://localhost:8888/ianseo/Boinx/flag1.jpg</flag_6>
                <flag_7>http://localhost:8888/ianseo/Boinx/flag1.jpg</flag_7>
                <flag_8>http://localhost:8888/ianseo/Boinx/flag1.jpg</flag_8>
                <flag_9>http://localhost:8888/ianseo/Boinx/flag1.jpg</flag_9>
                <flag_10>http://localhost:8888/ianseo/Boinx/flag1.jpg</flag_10>
                <flag_11>http://localhost:8888/ianseo/Boinx/flag1.jpg</flag_11>
                <flag_12>http://localhost:8888/ianseo/Boinx/flag1.jpg</flag_12>
                <flag_13>http://localhost:8888/ianseo/Boinx/flag1.jpg</flag_13>
                <flag_14>http://localhost:8888/ianseo/Boinx/flag1.jpg</flag_14>
                <flag_15>http://localhost:8888/ianseo/Boinx/flag1.jpg</flag_15>
                <s0>1</s0>
                <s1>1</s1>
                <s2>2</s2>
                <s3>1</s3>
                <s4>1</s4>
                <s5>2</s5>
                <s6>1</s6>
                <s7>1</s7>
                <s8>1</s8>
                <s9>1</s9>
                <s10>2</s10>
                <s11>1</s11>
                <s12>2</s12>
                <s13>1</s13>
                <s14>2</s14>
                <s15>1</s15>
                <s16>2</s16>
        </d>

    </grid>

  </Ianseogrid>
*/
?>