<?php
require_once('./config.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Lib/ArrTargets.inc.php');
require_once('Common/Fun_Phases.inc.php');
//require_once('Common/Fun_Phases.inc.php');

// checks and creates the necessary flags/pictures
require_once('Common/CheckPictures.php');
CheckPictures($TourCodeSafe);


require_once('Common/Lib/Obj_RankFactory.php');
require_once('Common/Lib/CommonLib.php');

$Ind=array();
$Team=array();
$AbsInd=array();
$AbsTeam=array();

$Awards=array();
$RaiseFlag=0;

// finds out what is to be served!
$MyQuery="select * from BoinxSchedule where (BsType like 'Awa\\_%') and BsTournament=$TourId "
	. "ORDER BY"
	. " BsType";

$Rs=safe_r_sql($MyQuery);
while($MyRow=safe_fetch($Rs) ) {
	$tmp= explode('_', $MyRow->BsType);
	if($tmp[1]=='Ind') {
		$Ind[]=$tmp[2].$tmp[3];
	} elseif($tmp[1]=='Team') {
		$Team[]=$tmp[2].$tmp[3];
	} elseif($tmp[1]=='Abs') {
		$AbsInd[]=$tmp[2];
	} elseif($tmp[1]=='AbsTeam') {
		$AbsTeam[]=$tmp[2];
	}
	if($MyRow->BsExtra==2) {
		$RaiseFlag=1;
	}
}

if(!$Ind and !$Team and !$AbsInd and !$AbsTeam) {
	die("Nothing selected!");
}

$BackColor=Get_Tournament_Option('AwardBackColor','#d0d0d0', $TourId);
$BackColor='none';
$BackPhoto='http://' . $_SERVER['HTTP_HOST'] . $CFG->ROOT_DIR . 'TV/Photos/' . $TourCodeSafe . '--Award--.jpg';
if(!file_exists($CFG->DOCUMENT_PATH . 'TV/Photos/' . $TourCodeSafe . '--Award--.jpg')) {
	$BackPhoto='';
} else {
	$BackPhoto.='?t='.filemtime($CFG->DOCUMENT_PATH . 'TV/Photos/' . $TourCodeSafe . '--Award--.jpg');
}

$fotodir='http://' . $_SERVER['HTTP_HOST'] . $CFG->ROOT_DIR . 'TV/Photos/' . $TourCodeSafe . '-%s-%s.jpg';
$fotodirSVG='http://' . $_SERVER['HTTP_HOST'] . $CFG->ROOT_DIR . 'TV/Photos/' . $TourCodeSafe . '-%sSvg-%s.svg';
$ranks=array('','g','s','b','w');

if(!empty($_REQUEST['Which'])) {
	if($_REQUEST['Which']=='WaDown') {
		foreach(array('USA', 'WA') as $i => $Nation) {
			$FlagSVG='';
			$Flag='';
			updateFlag($Nation, 'All', safe_r_sql("select FlCode, FlJPG, FlSVG, '{$_SESSION['TourCodeSafe']}' as ToCode, '{$_SESSION['TourId']}' as ToId from Flags
				where FlCode='$Nation' and FlIocCode='FITA' limit 1"));
			if(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-Fl-'.$Nation.'.jpg')) {
				$Flag=sprintf($fotodir, 'Fl', $Nation);
				if(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-FlSvg-'.$Nation.'.svg')) {
					$FlagSVG=sprintf($fotodirSVG, 'Fl', $Nation);
				}
			}
			$Awards['WaDown']['header'] = 'WaDown';
			$Awards['WaDown']['items'][$ranks[$i+1]]=array(
				'flag'		=>$Flag,
				'name'	=> $Nation,
				'country' => $Nation,
				'svg' => $FlagSVG,
				);
		}
	} elseif($_REQUEST['Which']=='Next') {
		foreach(array('ARG') as $i => $Nation) {
			$FlagSVG='';
			$Flag='';
			updateFlag($Nation, 'All', safe_r_sql("select FlCode, FlJPG, FlSVG, '{$_SESSION['TourCodeSafe']}' as ToCode, '{$_SESSION['TourId']}' as ToId from Flags
				where FlCode='$Nation' and FlIocCode='FITA' limit 1"));
			if(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-Fl-'.$Nation.'.jpg')) {
				$Flag=sprintf($fotodir, 'Fl', $Nation);
				if(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-FlSvg-'.$Nation.'.svg')) {
					$FlagSVG=sprintf($fotodirSVG, 'Fl', $Nation);
				}
			}
			$Awards['WaDown']['header'] = 'WaDown';
			$Awards['WaDown']['items'][$ranks[$i+1]]=array(
				'flag'		=>$Flag,
				'name'	=> $Nation,
				'country' => $Nation,
				'svg' => $FlagSVG,
				);
		}
	}

} else {
	//Genero la query che mi ritorna i podi individuali di qualifica
	if($Ind) {
		//Genero la query che mi ritorna tutti al max 10 righe dei podi qualificati
		$options=array('dist'=>0);
		$options['cutRank'] = 4;
		$family = 'DivClass';
		$options['arrNo'] = 0;
		$options['tournament']=$TourId;
		$options['events']=$Ind;

		$rank=Obj_RankFactory::create($family,$options);
		$rank->read();
		$rankData=$rank->getData();

		$Title=$rankData['meta']['title'];
		foreach($rankData['sections'] as $Event => $data) {
			$Awards['I-'.$Event]['header'] = $data['meta']['descr'];
			$txt=array();
			foreach($data['items'] as $item) {
				$FlagSVG='';
				if(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-Fl-'.$item['countryCode'].'.jpg')) {
					$Flag=sprintf($fotodir, 'Fl', $item['countryCode']);
					if(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-FlSvg-'.$item['countryCode'].'.svg')) {
						$FlagSVG=sprintf($fotodirSVG, 'Fl', $item['countryCode']);
					}
				} elseif(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-Fl-'.$item['countryIocCode'].'.jpg')) {
					$Flag=sprintf($fotodir, 'Fl', $item['countryIocCode']);
					if(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-FlSvg-'.$item['countryIocCode'].'.svg')) {
						$FlagSVG=sprintf($fotodirSVG, 'Fl', $item['countryIocCode']);
					}
				} else {
					$Flag='';
				}
				$Awards['I-'.$Event]['items'][$ranks[$item['rank']]]=array(
					'flag'		=>$Flag,
					'name'	=>$item['athlete'],
					'country' => $item['countryCode'],
					'svg' => $FlagSVG,
					);
			}
		}
	}

	if($Team) {
		//Genero la query che mi ritorna tutti al max 10 righe dei podi qualificati
		$options=array('dist'=>0);
		$options['cutRank'] = 4;
		$family = 'DivClassTeam';
		$options['arrNo'] = 0;
		$options['tournament']=$TourId;
		$options['events']=$Team;

		$rank=Obj_RankFactory::create($family,$options);
		$rank->read();
		$rankData=$rank->getData();

		$Title=$rankData['meta']['title'];
		foreach($rankData['sections'] as $Event => $data) {
			$Awards['T-'.$Event]['header'] = $data['meta']['descr'];
			$txt=array();
			foreach($data['items'] as $item) {
				$FlagSVG='';
				if(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-Fl-'.$item['countryCode'].'.jpg')) {
					$Flag=sprintf($fotodir, 'Fl', $item['countryCode']);
					if(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-FlSvg-'.$item['countryCode'].'.svg')) {
						$FlagSVG=sprintf($fotodirSVG, 'Fl', $item['countryCode']);
					}
				} elseif(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-Fl-'.$item['countryIocCode'].'.jpg')) {
					$Flag=sprintf($fotodir, 'Fl', $item['countryIocCode']);
					if(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-FlSvg-'.$item['countryIocCode'].'.svg')) {
						$FlagSVG=sprintf($fotodirSVG, 'Fl', $item['countryIocCode']);
					}
				} else {
					$Flag='';
				}
				$Awards['T-'.$Event]['items'][$ranks[$item['rank']]]=array(
					'flag'		=>$Flag,
					'name'	=>$item['countryName'],
					'country' => $item['countryCode'],
					'svg' => $FlagSVG,
					);
			}
		}
	}

	if($AbsInd) {
		require_once('Common/Lib/Obj_RankFactory.php');

		//Genero la query che mi ritorna tutti al max 10 righe dei podi qualificati
		$options=array('dist'=>0);
		$family = 'FinalInd';
		$options['tournament']=$TourId;
		$options['eventsR']=$AbsInd;

		$rank=Obj_RankFactory::create($family,$options);
		$rank->read();
		$rankData=$rank->getData();

		$Title=$rankData['meta']['title'];
		foreach($rankData['sections'] as $Event => $data) {
			$Awards['AI-'.$Event]['header'] = $data['meta']['descr'];
			$Awards['AI-'.$Event]['items'][$ranks[1]]=array('flag' => '', 'name' =>'');
			$Awards['AI-'.$Event]['items'][$ranks[2]]=array('flag' => '', 'name' =>'');
			$Awards['AI-'.$Event]['items'][$ranks[3]]=array('flag' => '', 'name' =>'');
			$Awards['AI-'.$Event]['items'][$ranks[4]]=array('flag' => '', 'name' =>'');
			$txt=array();
			foreach($data['items'] as $item) {
				if($item['rank'] > 4) break;
				$FlagSVG='';
				if(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-Fl-'.$item['countryCode'].'.jpg')) {
					$Flag=sprintf($fotodir, 'Fl', $item['countryCode']);
					if(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-FlSvg-'.$item['countryCode'].'.svg')) {
						$FlagSVG=sprintf($fotodirSVG, 'Fl', $item['countryCode']);
					}
				} elseif(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-Fl-'.$item['countryIocCode'].'.jpg')) {
					$Flag=sprintf($fotodir, 'Fl', $item['countryIocCode']);
					if(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-FlSvg-'.$item['countryIocCode'].'.svg')) {
						$FlagSVG=sprintf($fotodirSVG, 'Fl', $item['countryIocCode']);
					}
				} else {
					$Flag='';
				}
				$Awards['AI-'.$Event]['items'][$ranks[$item['rank']]]=array(
					'flag'		=>$Flag,
					'name'	=>$item['athlete'],
					'country' => $item['countryCode'],
					'svg' => $FlagSVG,
					);
			}
		}
	}

	if($AbsTeam) {
		$family = 'FinalTeam';
		$options['tournament']=$TourId;
		$options['eventsR']=$AbsTeam;

		$rank=Obj_RankFactory::create($family,$options);
		$rank->read();
		$rankData=$rank->getData();


		$Title=$rankData['meta']['title'];
		foreach($rankData['sections'] as $Event => $data) {
			$Awards['AT-'.$Event]['header'] = $data['meta']['descr'];
			$txt=array();
			foreach($data['items'] as $item) {
				if($item['rank']>4) break;
				if($item['rank']==0) continue;
				$FlagSVG='';
				if(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-Fl-'.$item['countryCode'].'.jpg')) {
					$Flag=sprintf($fotodir, 'Fl', $item['countryCode']);
					if(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-FlSvg-'.$item['countryCode'].'.svg')) {
						$FlagSVG=sprintf($fotodirSVG, 'Fl', $item['countryCode']);
					}
	//			} elseif(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-Fl-'.$item['countryIocCode'].'.jpg')) {
	//				$Flag=sprintf($fotodir, 'Fl', $item['countryIocCode']);
				} else {
					$Flag='';
				}
				$Awards['AT-'.$Event]['items'][$ranks[$item['rank']]]=array(
					'flag'		=>$Flag,
					'name'	=>$item['countryName'],
					'country' => $item['countryCode'],
					'svg' => $FlagSVG,
					);
			}
		}
	}

}


$XmlDoc = new DOMDocument('1.0', 'UTF-8');
$XmlRoot = $XmlDoc->createElement('Ianseoaward');
$XmlDoc->appendChild($XmlRoot);

$StartEvent='a';
$OldEvent='';

foreach($Awards as $Event => $Data) {
	$XmlRoot->appendChild($Item = $XmlDoc->createElement($StartEvent));
	$Item->appendChild($XmlDoc->createElement('header', $Data['header']));

	// Raise Flags
	$element = $XmlDoc->createElement('raiseflags');
	$element->appendChild($XmlDoc->createCDATASection($RaiseFlag));
	$Item->appendChild($element);

	// Background Color
	$element = $XmlDoc->createElement('backcolor');
	$element->appendChild($XmlDoc->createCDATASection($BackColor));
	$Item->appendChild($element);

	// Background Photo
	$element = $XmlDoc->createElement('backphoto');
	$element->appendChild($XmlDoc->createCDATASection($BackPhoto));
	$Item->appendChild($element);
	foreach($Data['items'] as $k => $v) {

		$element = $XmlDoc->createElement($k.'name');
		$element->appendChild($XmlDoc->createCDATASection(!empty($v['name']) ? $v['name']:''));
		$Item->appendChild($element);

		$element = $XmlDoc->createElement($k.'flag');
		$element->appendChild($XmlDoc->createCDATASection(!empty($v['flag']) ? $v['flag'] : ''));
		$Item->appendChild($element);

		$element = $XmlDoc->createElement($k.'country');
		$element->appendChild($XmlDoc->createCDATASection(!empty($v['country']) ? $v['country'] : ''));
		$Item->appendChild($element);

		$element = $XmlDoc->createElement($k.'svg');
		$element->appendChild($XmlDoc->createCDATASection(!empty($v['svg']) ? $v['svg'] : ''));
		$Item->appendChild($element);
	}
	$StartEvent++;
}

header('Cache-Control: no-store, no-cache, must-revalidate');
header('Content-type: text/xml; charset=' . PageEncode);
echo $XmlDoc->SaveXML();

/*

<?xml version="1.0"?>
 <Ianseoaward>

            <a> evento (progressivo a,b,c,d,e,f,ecc)
                <header>Recurve Men</header> Titolo evento
                <gname>Scarzella</gname> nome gold
                <sname>Pisani</sname> nome silver
                <bname>Deligant</bname> nome bronze
                <gflag>http://localhost:8888/ianseo/Boinx/flag1.jpg</gflag> bandiera gold
                <sflag>http://localhost:8888/ianseo/Boinx/flag3.gif</sflag> bandiera silver
                <bflag>http://localhost:8888/ianseo/Boinx/flag2.gif</bflag> bandiera bronze
            </a>
            <b>
                <header>Recurve Women</header>
                 <gname>Scarzella</gname>
                <sname>Pisani</sname>
                <bname>Deligant</bname>
                <gflag>http://localhost:8888/ianseo/Boinx/flag1.jpg</gflag>
                <sflag>http://localhost:8888/ianseo/Boinx/flag3.gif</sflag>
                <bflag>http://localhost:8888/ianseo/Boinx/flag2.gif</bflag>
            </b>
            <c>
                <header>Compound Men</header>
                  <gname>Scarzella</gname>
                <sname>Pisani</sname>
                <bname>Deligant</bname>
                <gflag>http://localhost:8888/ianseo/Boinx/flag1.jpg</gflag>
                <sflag>http://localhost:8888/ianseo/Boinx/flag3.gif</sflag>
                <bflag>http://localhost:8888/ianseo/Boinx/flag2.gif</bflag>
            </c>
            <d>
                <header>Compound Women</header>
                  <gname>Scarzella</gname>
                <sname>Pisani</sname>
                <bname>Deligant</bname>
                <gflag>http://localhost:8888/ianseo/Boinx/flag1.jpg</gflag>
                <sflag>http://localhost:8888/ianseo/Boinx/flag3.gif</sflag>
                <bflag>http://localhost:8888/ianseo/Boinx/flag2.gif</bflag>
            </d>



  </Ianseoaward>

*/
?>