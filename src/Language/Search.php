<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/GlobalsLanguage.inc.php');

$search='';
if(!empty($_GET['search'])) $search=trim(strtolower($_GET['search']));

	include('Common/Templates/head.php');
?>
<div align="center">
<div class="medium">

<?php

if($search) {
	echo '<table class="Tabella">';
	echo '<tr><th class="Title" colspan="5">'.get_text('ModulesFound','Languages',$search).'</th></tr>';
	echo '<tr class="Spacer"><td colspan="5"></td></tr>';
	echo '<tr>';
	echo '<th class="Title">'.get_text('Module','Languages').'</th>';
	echo '<th class="Title">'.get_text('Variable','Languages').'</th>';
	echo '<th class="Title">'.get_text('Text','Languages').'</th>';
	echo '<th class="Title">'.get_text('FunctionS','Languages').'</th>';
	echo '<th class="Title">'.get_text('FunctionL','Languages').'</th>';
	echo '</tr>';
	foreach(check_word($search) as $row) {
		echo '<tr>';
		echo '<td>'.$row[0].'</td>';
		echo '<td>'.$row[1].'</td>';
		echo '<td>'.preg_replace("#($search)#sim", '<b style="color:red">\\1</b>', $row[2]).'</td>';
		echo '<td>get_text(\''.$row[1].'\'' . ($row[0]!='Common' ? ', \''.$row[0].'\'' : '') . ')</td>';
		echo '<td>get_text(\''.$row[1].'\', \''.$row[0].'\', (mixed) $var, [(bool) $translate])</td>';
		echo '</tr>';
	}
	echo '</table>';
}
	echo '<form method="GET" action="">';
	echo '<table class="Tabella">';
	echo '<tr>';
	echo '<td class="Right" nowrap="nowrap">'.get_text('Search','Languages').'</td>';
	echo '<td width="100%">';
	echo '<input type="text" name="search" value="'.ManageHTML($search).'" style="width:90%">';
	echo '</td>';
	echo '</tr>';
	echo '<tr>';
	echo '<td class="Center" colspan="2">'.get_text('CmdSearch','Languages').'</td>';
	echo '</tr>';
	echo '</table>';
	echo '</form>';


?>

</div>
</div>
<?php
	include('Common/Templates/tail.php');
?>