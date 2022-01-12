// var Channel and var Page are defined in the php page (/tv.php)

$(document).ready(function() {
	checkChannel();
});

function checkChannel() {
	$.getJSON("TV/ChannelCheck.php?id="+Channel,
		function(data) {
			if(data.error==0) {
				if(data.reload) {
					$('.TvoChannel').fadeOut(function() {
						location.reload();
					});

				}
				$.each(data.pages, function(i,item) {
					if(item != Pages[i]) {
						// page content changed...
						// fade out
						$('#channel-'+i).fadeOut(function() {
							Pages[i]=item;
							$('#channel-'+i).prop('src', item);
							$('#channel-'+i).fadeIn();
						});
					}
				})
			}

			setTimeout(checkChannel, 3000);
		});
}

