$(function() {
	if(EventCode) {
		$('[name^="Multiple-"][value="AB"]').each(function() {
			// for each multiple phase chek if the data already inserted are matching "AB", "CD" or "AB/CD"
			var selector='    ';
			var phase=this.name.substr(9);
			$('span[phase="'+phase+'"]').each(function() {
				// check each letter to see how many combinations we have
				switch($(this).html()) {
					case 'A': selector='A' + selector.substr(1); break;
					case 'B': selector=selector.substr(0,1) + 'B' + selector.substr(2); break;
					case 'C': selector=selector.substr(0,2) + 'C' + selector.substr(3); break;
					case 'D': selector=selector.substr(0,3) + 'D'; break;
				}
			});
			switch(selector.replace(/ +/,'')) {
				case 'A':
				case 'AB':
					$(this).closest('div').find('[value="AB"]').prop('checked', true);
					break;
				case 'C':
				case 'CD':
					$(this).closest('div').find('[value="CD"]').prop('checked', true);
					break;
				case 'AC':
				case 'ABCD':
					$(this).closest('div').find('[value="ABCD"]').prop('checked', true);
					break;
			}
		});
		FindRedTarget(EventCode);
	}
});

function WriteTarget(obj) {
	$.getJSON('ManTarget-action.php?act=set&fld='+obj.id+'&val='+encodeURIComponent(obj.value)+'&type='+($('[name="Multiple-'+$(obj).attr('phase')+'"]:checked').val() || ''), function(data) {
		$(obj).toggleClass('red', false);
		if(data.error==0) {
			$(data.targets).each(function() {
				$('#'+this.id).val(this.tgt);
				$('#Letter-'+this.matchno).html(this.let);
			});
			FindRedTarget(EventCode);
		} else {
			$(obj).toggleClass('red', true);
		}
	});
}

/*
	Esegue una post a FindRedTarget.php
	Event è l'evento
	Phase è la fase
	Tar!='' implica il filtro nella pagina
*/
function FindRedTarget(Event, Phase, Tar) {
	$.getJSON('ManTarget-action.php?act=get&event='+Event+'&phase='+Phase, function(data) {
		$(data.targets).each(function() {
			$('#'+this.id).toggleClass('red', this.error);
		});
	});
}
