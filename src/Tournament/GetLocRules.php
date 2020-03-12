<?php
// if no type asked, exits!

if (empty($_REQUEST['type'])) exit;

define('debug',false);	// settare a true per l'output di debug

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Fun_FormatText.inc.php');

$type=intval($_REQUEST['type']);

$locrules=array();
$tmpLang=SelectLanguage();
$tmpMainLang=explode('-', $tmpLang);
$tmpMainLang=$tmpMainLang[0];
$lang='default';

if ($type>0) {
	// Search for the local setup rules
	$glob=glob($CFG->DOCUMENT_PATH . 'Modules/Sets/*');
	foreach($glob as $val) {
		if(is_dir($val) and file_exists($val . '/Setup_' . $type . '_' . basename($val) . '.set')) {
			$locrules[] = array(
				'code' => basename($val),
				'descr' => get_text('Setup-'.basename($val),'Install')
				);
		}
	}

	// adds the default FITA rule for that type
	if(is_file($CFG->DOCUMENT_PATH . 'Modules/Sets/FITA/Setup_' . $type . '.set')) {
		$locrules[] = array(
			'code' => 'default',
			'descr' => get_text('Setup-Default','Install')
			);
	}

	// if there is more than 1 choice adds the "select Local rule"...
	if(count($locrules)>1) array_unshift(
		$locrules,
		array(
			'code' => '',
			'descr' => get_text('Setup-Select','Install')
			));
}
/*print '<pre>';
print_r($rules);
print '</pre>';exit;*/

$xmlDoc=new DOMDocument('1.0','UTF-8');
	$xmlRoot=$xmlDoc->createElement('response');
	$xmlDoc->appendChild($xmlRoot);

		$node=$xmlDoc->createElement('lang','none');
		$xmlRoot->appendChild($node);

		if (count($locrules)>0)
		{
			foreach ($locrules as $rule)
			{
				$xmlRule=$xmlDoc->createElement('rule');
				$xmlRoot->appendChild($xmlRule);
				foreach ($rule as $k=>$v)
				{
					$node=$xmlDoc->createElement($k);
					$xmlRule->appendChild($node);

					$cdata=$xmlDoc->createCDATASection(!$v ? intval($v) : $v);
					$node->appendChild($cdata);
				}
			}

		}

header('Cache-Control: no-store, no-cache, must-revalidate');
header('Content-type: text/xml; charset=' . PageEncode);

print $xmlDoc->saveXML();
?>