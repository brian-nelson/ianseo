function UpdateArrow(obj, score) {
	$.getJSON(RootDir+'UpdateArrow.php?arr='+obj.id+'&value='+obj.value+'&type='+score, function(data) {
		var FieldId=$(obj).closest('tr').attr('id').substring(4);
		if(data.error==0) {
			obj.className='';
			obj.value=data.value;
		} else {
			obj.value='';
			if(data.msg) {
				alert(data.msg);
				return;
			} else {
				obj.className='error';
			}
		}
		$('#Score_'+FieldId).html(data.score);
		$('#Gold_'+FieldId).html(data.gold);
		$('#XNine_'+FieldId).html(data.xnine);

		if(score=='score') {
			var tmp=FieldId.split('_');
			tmp.pop();
			FieldId=tmp.join('_');
			for(var i in data.details) {
				$('#End_'+FieldId+'_'+i).html(data.details[i].end);
				$('#EndRun_'+FieldId+'_'+i).html(data.details[i].endrun);
				$('#Score_'+FieldId+'_'+i).html(data.details[i].score);
			}

			$('#Score').html(data.score);
			$('#TotScore').html(data.score);
			$('#Gold').html(data.gold);
			$('#XNine').html(data.xnine);
			$('#Hits').html(data.hits);
		}
	});
}

function SelectSession(obj) {
	var Events=obj.value.split(',');
	$('#EventSelector [type="checkbox"]').prop('checked', false);
	for(var i=0; i<Events.length; i++) {
		$('[value="'+Events[i]+'"]').prop('checked', true);
	}
}

function UpdateElim(obj) {
	$.getJSON(RootDir+'UpdateElim.php?'+obj.id+'='+obj.value, function(data) {
		var FieldId=$(obj).closest('tr').attr('id').substring(4);

		if(data.error==0) {
			obj.className='';
			obj.value=data.value;
		} else {
			obj.value='';
			if(data.msg) {
				alert(data.msg);
				return;
			} else {
				obj.className='error';
			}
		}
		$('#d_ElScore_'+FieldId).html(data.total);
		$('#d_ElGold_'+FieldId).html(data.golds);
		$('#d_ElXnine_'+FieldId).html(data.xnine);
		$('#d_ElHits_'+FieldId).html(data.hits);
	});
}
