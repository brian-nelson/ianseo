

function toggleLock(obj) {
	$.getJSON('Sessions-Toggle.php?key='+$(obj).attr('ref'), function(data) {
		if(data.error==0) {
			$.each(data.status, function(idx) {
				$('[ref="'+idx+'"]')
					.toggleClass('locked fa-times-circle', this==1)
					.toggleClass('unlocked fa-check-circle', this==0);
			});
		} else {
			alert(data.msg);
		}
	});
}
