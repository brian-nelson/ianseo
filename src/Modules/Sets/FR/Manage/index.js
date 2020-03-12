
function updateMatch(obj, auto) {
	var phase=$(obj).closest('tr').attr('match').substr(3);
	var matchno=$(obj).attr('match').substr(3);
	var item=$(obj).attr('match').substr(0,2);
	var tbody=$(obj).closest('tbody');
	var game=tbody.attr('game');
	var team=$(obj).closest('tbody').find('[match="te-'+matchno+'"]');
	$(team).closest('td').css('backgroundColor','');
	if(item=='te') {
		// disengage all "old" teams from disabled status
		$('tbody[game="'+game+'"]').find('option[value="'+$(obj).attr('oldvalue')+'"]').each(function() { this.disabled=false; });
		$(obj).attr('oldvalue', $(obj).val());
		$('tbody[game="'+game+'"]').find('option[value="'+$(obj).val()+'"]').each(function() {this.disabled=true;});
		obj.disabled=false;
	}
	if(team[0].value==0) {
		$(team).closest('td').css('backgroundColor','red');
		tbody.find().find('input').val('');
	} else {
		$.getJSON('index-update.php?day='+$('#MatchDays').val()+'&event='+$('#Category').val()+'&phase='+phase+'&match='+matchno+'&item='+item+'&team='+team[0].value+'&val='+obj.value+(auto ? '&auto=1' : ''), function(data) {
			if(data.error==0) {
				$(data.matches).each(function() {
					$('[match="ma-'+this.ph+'"]').find('[match="'+this.id+'"]').val(this.val);
				});
			}
		});
	}
}

function assignPeople(Event) {
	$.getJSON('./index-assign.php?event='+Event, function(data) {
		alert(data.msg);
	});
}

function setTeams(Event, obj) {
	$.getJSON('./index-teams.php?event='+Event+'&day='+obj.value, function(data) {
		if(data.error==0) {
			for(var team in data.teams) {
				$(data.teams[team]).each(function(idx) {
					$('[match="te-'+this+'"]').val(team);
				});
			}
		}
		if(data.games==4) {
			$('tbody[game="5"]').hide();
			$('#GameTitle5').hide();
		} else {
			$('tbody[game="5"]').show();
			$('#GameTitle5').show();
		}
		alert(data.msg);
	});
}