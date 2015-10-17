<?php

if(empty($CFG)) require_once(dirname(dirname(__FILE__)) . '/config.php');

require_once('Common/Lib/CommonLib.php');

/**
 * Ritorna l'output dell'url wrappato
 *
 * @param String $url: indirizzo da wrappare escluso l'host name
 *
 * @return: false in caso di errore; la pagina in caso di successo
 */

	function URLWrapper($url)
	{
		$out='';
		$fp=false;

		$fp = fsockopen($_SERVER['HTTP_HOST'], $_SERVER['SERVER_PORT'], $errno, $errstr, 30);
	//	print $url . '<br>';
		//print $errno . ' ' . $errstr;exit;

		if (!$fp)
			return false;

	// eseguo la get http alla pagina
		$out = "GET /" . $url ." HTTP/1.1\r\n";
	    $out .= "Host: " . $_SERVER['HTTP_HOST'] . "\r\n";
	    $out .= "Connection: Close\r\n\r\n";
		fwrite($fp, $out);
		/*print'<pre>';
		print_r(explode(chr(0x0d).chr(0x0a).chr(0x0d).chr(0x0a),stream_get_contents($fp)));exit;
		print'</pre>';exit;*/
	    /*while (!feof($fp))
	    {
	        $out.=fgets($fp, 4096);
	    }*/

	/*
	 * Esplodo lo stream sul doppio a capo.
	 * Avrò almeno due elementi, l'header e il content.
	 * Il content però avrà davanti la lunghezza del pacchetto + chr(0x0d).chr(0x0a).
	 * $data potrebbe avere degli elementi successivi a [1] che dovranno essere accodati
	 * a lui così come sono preceduti dal doppio a capo (che è stato fatto saltare dall'explode)
	 */
		//$ret='';
		$data=explode(chr(0x0d).chr(0x0a).chr(0x0d).chr(0x0a),stream_get_contents($fp));

		//print_r($data);exit;
	// Tiro fuori la stringa dopo chr(0x0d).chr(0x0a) dentro a [1]
		$pos=strpos($data[1],chr(0x0d).chr(0x0a));
		if ($pos===false)
			return '';

		//print $pos;exit;
		$out=substr($data[1],$pos+2);	// devo partire da due char dopo!

	/*
	 *  se ho elementi dopo [1] li accodo così come li trovo preceduti dal doppio a capo
	 *  Se count($data)==2 allora il for non inizia neppure
	 */
		for ($i=2;$i<count($data);++$i)
			$out.=chr(0x0d).chr(0x0a).chr(0x0d).chr(0x0a).$data[$i];

		fclose($fp);

		return $out;
	}

	/*function URLWrapper($url)
	{
		return file_get_contents($url);
	}*/

// Versione vecchia
	/*function URLWrapper($url)
	{
		$out='';
		$fp=false;

		if (($fp=fopen($url,'r'))===false)
			return false;

		while (!feof($fp))
			$out.=fgets($fp);

		fclose($fp);

		return $out;
	}*/

	function comboFromRs($rs,$valueField,$displayField,$rsType=0,$selected=null,$noSelEl=null,$id=null,$name=null,$opts=array())
	{
		$out='<select';
			if (!is_null($name))
				$out.=' name="' . $name . '"';
			if (!is_null($id))
				$out.=' id="' . $id . '"';

			if (count($opts)>0)
			{
				foreach ($opts as $k=>$v)
				{
					$out.=' ' . $k . '="' . $v . '"';
				}
			}
		$out.='>';

			if (!is_null($noSelEl))
				$out.='<option value="' . $noSelEl[0] . '">' . $noSelEl[1] . '</option>' . "\n";

			while ($myRow=($rsType==0 ? safe_fetch_assoc($rs) : current($rs)))
			{
				$v=$myRow[$valueField];
				$d=$myRow[$displayField];

				$sel=(!is_null($selected) && $selected==$v ? ' selected' : '');

				$out.='<option value="' . $v . '"' . $sel . '>' . $d . '</option>' . "\n";

				if ($rsType==1)
					next($rs);
			}

		$out.='</select>' . "\n";

		return $out;
	}

/**
 * calcMaxTeamPerson()
 * Calcola ed eventualmente scrive il numero massimo di persone in una squadra
 *
 * @param mixed $e: eventi di cui fare il calcolo. Se stringa verrà considerato l'evento;
 * 					se array verranno considerati gli eventi nel vettore
 * 					se array vuoto verranno considerati tutti gli eventi team del torneo
 * @param bool $write: true per scrivere il valore nella tabella; false altrimenti
 * @param int $t: torneo, se null prende quello aperto
 * @return mixed: false se ci sono stati errori; un array key=>value con key l'evento e value il numero
 */
	function calcMaxTeamPerson($e=array(),$write=true,$t=null)
	{
		$tournament=$t===null ? $_SESSION['TourId'] : $t;
		$events=array();
		if (!is_array($e))
		{
			$events[]=StrSafe_DB($e);
		}
		elseif (count($e)>0)
		{
			foreach ($e as $ee)
			{
				$events[]=StrSafe_DB($ee);
			}
		}


		$eventsFilter="";
		if (count($events)>0)
		{
			$eventsFilter=" AND EcCode IN(" . implode(',',$events).") ";
		}

		$q="
			SELECT
				SUM(sqEcNumber) AS q,sqEcCode
			FROM
				(
					SELECT DISTINCT
						EcCode AS sqEcCode,EcTeamEvent AS sqEcTeamEvent,EcTournament AS sqEcTournament,EcNumber AS sqEcNumber
					FROM
						EventClass
					WHERE
						EcTournament={$tournament} AND EcTeamEvent<>0 {$eventsFilter}
				) AS sq
			GROUP BY sqEcCode
		";
		//print $q.'<br><br>';
		$r=safe_r_sql($q);

		$ret=false;

		if (safe_num_rows($r)>0)
		{
			$ret=array();
			while ($row=safe_fetch($r))
			{
				$ret[]=array('event'=>$row->sqEcCode,'num'=>$row->q);

				if ($write)
				{
					$q="
						UPDATE
							Events
						SET
							EvMaxTeamPerson={$row->q}
						WHERE
							EvTournament={$tournament} AND EvTeamEvent=1 AND EvCode='{$row->sqEcCode}'
					";
					//print $q.'<br><br>';
					$rr=safe_w_sql($q);
				}
			}
		}

		return $ret;
	}

	function getTournamentCategories()
	{
		return array(
			'1'=>array('key'=>'1','descr'=>get_text('OutdoorTourCategory','Tournament')),
			'2'=>array('key'=>'2','descr'=>get_text('IndoorTourCategory','Tournament')),
			'4'=>array('key'=>'4','descr'=>get_text('FieldTourCategory','Tournament')),
			'8'=>array('key'=>'8','descr'=>get_text('3DTourCategory','Tournament')),
			'0'=>array('key'=>'0','descr'=>get_text('OtherTourCategory','Tournament'))
		);
	}

	function getElabTeamMode()
	{
		return array(
			'0'=>array('key'=>'0','descr'=>get_text('StandardElabTeamMode','Tournament')),
			'1'=>array('key'=>'1','descr'=>get_text('FieldElabTeamMode','Tournament')),
			'2'=>array('key'=>'2','descr'=>get_text('3DElabTeamMode','Tournament'))
		);
	}

/**
 * prepareModalMask()
 * Prepare una maschera da usare come finestra modale durante i caricamenti javascript.
 *
 * @param string $id: id del div
 * @param string $content: contenuto del box. Può essere codice html
 * @param string $startVisibility: Deve essere impostato come "hidden" oppure "visible". Di default è "hidden". Server ad impostare lo stato della maschera
 * @param string $wBox: larghezza del box interno della maschera
 * @param string $hBox: altezza del box interno della maschera
 * @param string $topMarginBox: distanza dall'alto del box interno della maschera
 * @param string $bgBox: colore di sfondo del box interno
 * @param string $w: larghezza della maschera
 * @param string $h: altezza della maschera
 * @param string $bg: colore di sfondo della maschera
 *
 * @return string: codice html della maschera da usare nella pagina
 */
	function prepareModalMask($id='',$content='',$startVisibility='hidden',$wBox='50%',$hBox='50%',$topMarginBox='100px',$bgBox='#ffffff',$w='99%',$h='99%',$bg='#cccccc')
	{
		$html="
			<div id=\"{$id}\" style=\"visibility: {$startVisibility}; width:{$w}; height: {$h}; position: absolute; background-color:{$bg};\">
				<div style=\"width: {$wBox}; height: {$hBox}; margin: {$topMarginBox} auto auto auto; background-color:{$bgBox};\">
					{$content}
				</div>
			</div>
		";

		return $html;
	}
?>