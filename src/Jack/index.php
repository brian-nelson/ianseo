<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('Common/Fun_Modules.php');


if (!CheckTourSession()) {
	print get_text('CrackError');
	exit;
}

if(isset($_REQUEST['Event']) &&  isset($_REQUEST['Module']) &&  preg_match("/^[a-z0-9_.-]+$/i", $_REQUEST['Event']) &&  preg_match("/^[a-z0-9_.-]+$/i", $_REQUEST['Module'])) {
	removeJack($_REQUEST['Event'], $_REQUEST['Module']);
	CD_redirect($_SERVER["PHP_SELF"]);	
}

$JS_SCRIPT=array('<script type="text/javascript">
	function removeJack(Event,Module) {
		location.href = \'' . $_SERVER["PHP_SELF"] . '?Event=\'+Event+\'&Module=\'+Module;
	}
	</script>');
$PAGE_TITLE=get_text('MenuLM_JackSettings', 'Common');

include('Common/Templates/head.php');

echo '<table class="Tabella">';
echo '<tr><th class="Title" colspan="6">'.get_text('MenuLM_JackSettings','Common').'</th></tr>';

$Modules=getModule('Jack', '%', $_SESSION["TourId"]);
//debug_svela($Modules);
echo '<tr>
	<th>'.get_text('JackEvent','Common').'</th>
	<th colspan="2">'.get_text('JackModule','Common').'</th>
	<th>'.get_text('JackInclude','Common').'</th>
	<th>'.get_text('JackCallback','Common').'</th>
	<th>'.get_text('JackExtra','Common').'</th>
	</tr>';
foreach($Modules as $Event => $values) {
	echo '<tr>
		<td rowspan="'.count($values).'">'.$Event.'</td>';
		$firstRow = true;
		foreach($values as $k=>$v) {
			if(!$firstRow) {
				echo '</tr><tr>';
			}
			$extraParams = "";
			if(isset($v['extraparams']) && is_array($v['extraparams'])) {
				foreach($v['extraparams'] as $kX=>$vX) {
					if(is_array($vX)) {
						$extraParams .= "$kX => (";
						foreach($vX as $kX2=>$vX2) {
							$extraParams .= "$kX2: $vX2, ";
						}
						$extraParams = substr($extraParams,0,-2) . ")<br>";
					} else {
						$extraParams .= "$kX => $vX<br>";
					}
				}
			}
			echo '
				<td><img src="'.$CFG->ROOT_DIR.'Common/Images/drop.png" onClick="removeJack(\''.$Event.'\',\''.$k.'\')"></td>
				<td>'.$k.'</td>
				<td>'.$v['include'].'</td>
				<td>'.$v['callback'].'</td>
				<td>'.$extraParams.'</td>';
			$firstRow = false;
		}
	echo '</tr>';
}

echo '</table>';



include('Common/Templates/tail.php');



