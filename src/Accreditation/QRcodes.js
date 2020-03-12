function showWifiPart(obj) {
	var showIt = $('#wifiControl').is(":checked");
	$('.hideWifi').each(function() {
		if(showIt) {
			$(this).show();
		} else {
			$(this).hide();
		}

	});
}

function addWifi() {
	var id = $('[name^="items[WifiSSID]"]').length;
	var html = '<tr class="hideWifi" id="wifi0_'+id+'">'+
		'<th class="Title" rowspan="3" id="th_'+id+'">'+(id+1)+'<br><a style="text-decoration: none; color:#FFFFFF" href="javascript:delWifi('+id+');">[-]</a></th>'+
		'<th>'+WifiSSID+'</th>'+
		'<td><input type="text" name="items[WifiSSID]['+id+']" value=""></td>'+
		'</tr>'+
		'<tr class="hideWifi" id="wifi1_'+id+'">'+
		'<th>'+WifiPWD+'</th>'+
		'<td><input type="text" name="items[WifiPWD]['+id+']" value=""></td>'+
		'<tr class="hideWifi" id="wifi2_'+id+'">'+
		'<th>'+WifiUse+'</th>'+
		'<td><input type="checkbox" name="items[WifiUseWAGate]['+id+']" value="1">WAGate'+
		'&nbsp;&nbsp;&nbsp;<input type="checkbox" name="items[WifiUseOVA]['+id+']" value="1">OVA</td>'+
		'</tr>'+
		'<tr class="divider" id="wifi3_'+id+'"><td colspan="3"></td></tr>';
	$('#wifi3_'+(id-1)).after(html);
}

function delWifi(idDel) {
	var id = ($('[name="items[WifiSSID][]"]').length-1);

	$('#wifi0_'+idDel).remove();
	$('#wifi1_'+idDel).remove();
	if(idDel!=id) {
		for(var i=idDel+1; i<=id; i++) {
			$('#wifi0_'+i).id='wifi0_'+(i-1);
			$('#wifi1_'+i).id='wifi1_'+(i-1);
			$('#th_'+i).html(i+'<br><a style="text-decoration: none; color:#FFFFFF" href="javascript:delWifi('+(i-1)+');">[-]</a>');
			$('#th_'+i).id='th_'+(i-1);
		}
	}
}
