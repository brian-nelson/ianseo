
function WriteSchedule(Field) {
    $.get('../Final/Individual/WriteDateTime.php?'+Field.id + "=" +encodeURIComponent(Field.value), function(data) {
        if($(data).find('error').text()=='0') {
            $('#d_FSScheduledDate_'+$(data).find('e').text()+'_'+$(data).find('m').text()).val($(data).find('date').text());
            $('#d_FSScheduledTime_'+$(data).find('e').text()+'_'+$(data).find('m').text()).val($(data).find('time').text());
            $('#d_FSScheduledLen_'+$(data).find('e').text()+'_'+$(data).find('m').text()).val($(data).find('len').text());
        }
    }, 'XML');
}

function CloneSchedule(obj) {
    $.getJSON('SetTarget-Schedule.php?dest='+$(obj).attr('event')+'&org='+$(obj).closest('div').find('select').val(), function(data) {
        if(data.error==0) {
            var first=true;
            $(data.items).each(function(item) {
                if(first) {
                    $('[id^="d_FSScheduledDate_'+this.FSEvent+'"]').val('');
                    $('[id^="d_FSScheduledTime_'+this.FSEvent+'"]').val('');
                    $('[id^="d_FSScheduledLen_'+this.FSEvent+'"]').val('');
                    first=false;
                }
                $('#d_FSScheduledDate_'+this.FSEvent+'_'+this.FSMatchNo).val(this.FSScheduledDate);
                $('#d_FSScheduledTime_'+this.FSEvent+'_'+this.FSMatchNo).val(this.FSScheduledTime);
                $('#d_FSScheduledLen_'+this.FSEvent+'_'+this.FSMatchNo).val(this.FSScheduledLen);
            });
        }
    });
}

function UpdateTargetNo(Field) {
	$(Field).toggleClass('error', false);
	$.getJSON('UpdateTargetNo.php?'+Field.id+'='+encodeURIComponent(Field.value), function(data) {
		if (data.error==1) {
			$(Field).toggleClass('error', true);
		} else {
			$(data.rows).each(function() {
				$('#d_q_ElTargetNo_'+this.key).val(this.value);
			});

			FindRedTargetElim();
		}
	});
}

function UpdateSession(Field) {
	$.getJSON('UpdateSession.php?'+Field.id+'='+encodeURIComponent(Field.value), function(data) {
		$(Field).toggleClass('error', data.error==1);

		if (data.error==0) {
			$(Field).val(data.val);
		}

		FindRedTargetElim();
	});
}

function FindRedTargetElim() {
	$.getJSON('FindRedTarget.php', function(data) {
		if (data.error==0) {
			$(data.rows).each(function() {
				$('#d_q_ElTargetNo_'+this.id).toggleClass('red', this.good==0);
			});
		}
	});
}
