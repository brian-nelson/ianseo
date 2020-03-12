// var Channel and var Page are defined in the php page (/tv.php)

$(document).ready(function() {
	checkChannel();
});

function checkChannel() {
	$.getJSON("TV/ChannelCheck.php?id="+Channel,
		function(data) {
			if(data.error==0) {
				if(data.page != Page || data.reload) {
					// page content changed...
					// fade out
					$('#channel').fadeOut(function() {
						Page=data.page;
						$('#channel')[0].src=data.page;
						$('#channel').fadeIn();
					});
				}
			}

			setTimeout(checkChannel, 3000);
		});
}

