<?php

//require_once(dirname(dirname(__FILE__)) . '/config.php');
//require_once('Common/Obj_Scheduler.php');
//$opts=array(
//	'final'=>false,
//	'finalOrphans'=>false,
//	'training'=>false,
//	'trainingOrphans'=>false,
//);
//$s=new Obj_Scheduler($opts);
//print'<pre>';
//print_r($s->getData());
//print'</pre>';

//require_once(dirname(dirname(__FILE__)) . '/config.php');
//require_once('Common/Rank/Obj_Rank.php');
//require_once('Modules/Sets/F2F/Rank/Obj_Rank_Abs_21.php');
//
//$r=new Obj_Rank_Abs_21(array('events'=>array('CM'),'session'=>0, 'dist'=>0));
//$r->read();

//require_once(dirname(dirname(__FILE__)) . '/config.php');
//require_once('Modules/F2F/Fun_F2F.local.inc.php');
//
//getRankGroup(array('CM'),array(1));



require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Lib/Obj_RankFactory.php');
//print '<pre>';
//print_r($_SESSION);
//print '</pre>';
$opts=array(
	//'final'=>array('t'=>false),
	'divcl'=>array('i'=>true,'t'=>true),
	//'noMatch'=>array('i'=>true)
);
$r=Obj_RankFactory::create('MedalList',$opts);
$r->read();

print '<pre>';
print_r($r->getData());
print '</pre>';