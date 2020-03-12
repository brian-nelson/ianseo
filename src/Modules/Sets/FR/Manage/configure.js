function confUpdate(obj) {
	$(obj).closest('td').css('backgroundColor','');
	$.getJSON('./configure-updateWinners.php?item='+$(obj).attr('item')+'&cat='+$(obj).attr('cat')+'&pos='+$(obj).attr('pos')+'&club='+obj.value, function(data) {
		if(data.reload==1) {
			location.reload();
			return;
		}
		$(obj).closest('td').css('backgroundColor', data.error==0 ? 'green' : 'red');
	});
}