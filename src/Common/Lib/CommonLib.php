<?php

function Get_Tournament_Option($key, $Ret='', $TourId=0) {
	if(!$TourId and !empty($_SESSION['TourId'])) $TourId=$_SESSION['TourId'];
	if(empty($TourId)) return array();


	$q=safe_r_sql("select ToOptions from Tournament where ToId={$TourId}");
	$r=safe_fetch($q);
	if($r->ToOptions) $ToOptions=unserialize($r->ToOptions);
	if(!empty($ToOptions[$key])) {
		if(is_array($ToOptions[$key])) {
			if(empty($Ret)) $Ret=array();
			foreach($ToOptions[$key] as $k => $v) {
				$Ret[$k] = $v;
			}
		} elseif(is_object($ToOptions[$key])) {
			if(empty($Ret)) $Ret=new StdClass();
			foreach($ToOptions[$key] as $k => $v) {
				$Ret->$k = $v;
			}
		} else {
			$Ret=$ToOptions[$key];
		}
	}
	return $Ret;
}

function Get_Image($IocCode=null, $Section=null, $Reference=null, $Type=null, $Tourid=0) {
	if(empty($Tourid)) {
		$Tourid=$_SESSION['TourId'];
	}
	$SQL="select * from Images where ImTournament=$Tourid";
	if(!isnull($IocCode)) $SQL.=" and ImIocCode='$IocCode'";
	if(!isnull($Section)) $SQL.=" and ImSection='$Section'";
	if(!isnull($Reference)) $SQL.=" and ImReference='$Reference'";
	if(!isnull($Type)) $SQL.=" and ImType='$Type'";

	$q=safe_r_sql($SQL);
}

/**
 * Serve a collegare le var stringa definite in php a javascript.
 * Per ogni stringa in $vars viene generata una var javascript con lo stesso nome e lo stesso valore.
 * Se la var in php è un vettore (1-dimensionale) anche quello verrà convertito in js.
 *
 * @param string[] $vars: nomi delle var da generare
 *
 * @return string: script javascript che inizializza le variabili localizzate
 */
function phpVars2js($vars)
{
	$out='';

	$out.='<script type="text/javascript">' . "\n";

	foreach ($vars as $k => $v)
	{

		if (is_array($v))		// array
		{

			$out.='var ' . $k . '=new Array();' . "\n";
			foreach ($v as $index => $value)
			{
				if (!is_numeric($index))
				{
					$index="'" . $index . "'";
				}
				$out.=$k . '[' . $index . ']="' . addslashes($value) . '";' . "\n";
			}
		}
		else		// var scalare
		{
			$out.='var ' . $k . '="' . addslashes($v) . '";' . "\n";
		}

	}

	$out.='</script>' . "\n";

	return $out;
}

