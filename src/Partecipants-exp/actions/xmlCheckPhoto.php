<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');

/****** Controller ******/

	$id=isset($_REQUEST['id']) ? $_REQUEST['id'] : null;
	$row=isset($_REQUEST['row']) ? $_REQUEST['row'] : null;

	if (!CheckTourSession() && !is_null($id) && !is_null($row))
	{
		print get_text('CrackError');
		exit;
	}
    checkACL(AclParticipants, AclReadOnly, false);

	$error=0;
	$has_photo = 0;

	$query
		= "SELECT "
			. "PhEnId "
		. "FROM "
			. "Photos "
		. "WHERE "
			. "PhEnId=" . StrSafe_DB($id) . " AND PhPhoto<>'' ";
	$rs=safe_r_sql($query);
	//print $query;exit;
	if ($rs)
	{
		if (safe_num_rows($rs)==1)
			$has_photo=1;
	}
	else
	{
		$error=1;
	}

	$xmlDoc=new DOMDocument('1.0',PageEncode);
		$xmlRoot=$xmlDoc->createElement('response');
		$xmlDoc->appendChild($xmlRoot);

	// Header
		$xmlHeader=$xmlDoc->createElement('header');
		$xmlRoot->appendChild($xmlHeader);

			$node=$xmlDoc->createElement('error',$error);
			$xmlHeader->appendChild($node);

		$node=$xmlDoc->createElement('row',$row);
		$xmlRoot->appendChild($node);

		$node=$xmlDoc->createElement('has_photo',$has_photo);
		$xmlRoot->appendChild($node);

	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Content-type: text/xml; charset=' . PageEncode);

	print $xmlDoc->saveXML();

?>