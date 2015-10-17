<?php
/*

ods-php a library to read and write ods files from php.

This library has been forked from eyeOS project and licended under the LGPL3
terms available at: http://www.gnu.org/licenses/lgpl-3.0.txt (relicenced
with permission of the copyright holders)

Copyright: Juan Lao Tebar (juanlao@eyeos.org) and Jose Carlos Norte (jose@eyeos.org) - 2008

https://sourceforge.net/projects/ods-php/

*/


include("ods.php"); //include the class and wrappers

//	$obj = new ods();

$object = new Ods(); //create a new ods file
$object->addCell('RM I',0,0,1,'float'); //add a cell to sheet 0, row 0, cell 0, with value 1 and type float
$object->addCell('RW I',0,1,2,'float'); //add a cell to sheet 0, row 0, cell 1, with value 1 and type float
$object->addCell('CM T',1,0,1,'float'); //add a cell to sheet 0, row 1, cell 0, with value 1 and type float
$object->addCell('CW T',1,1,2,'float'); //add a cell to sheet 0, row 1, cell 1, with value 1 and type float
saveOds($object,'/tmp/new.ods'); //save the object to a ods file

header('Content-type: application/vnd.oasis.opendocument.spreadsheet');
readfile('/tmp/new.ods');

?>