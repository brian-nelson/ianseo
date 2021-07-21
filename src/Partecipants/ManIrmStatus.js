$(function() {
	selectEvent();
});

function selectEvent(obj) {
	if(obj) {
		ReqTeam=obj.value;
		ReqEvent='';
		ReqPhase='';
	}
	$('#TeamSelector').val(ReqTeam);
	if (history.pushState) {
		var newurl = window.location.origin + window.location.pathname + '?team='+ReqTeam;
		window.history.pushState({path:newurl},'',newurl);
	}
	$.getJSON('ManIrmStatus-getElements.php?type=team&value='+$('#TeamSelector').val(), function(data) {
		$('#EventsSelector').empty();
		$('#SearchBox').hide();
		$.each(data.elements, function() {
			$('#EventsSelector').append('<option value="'+this.key+'" class="'+this.class+'"'+(this.class=='disabled' ? ' disabled="disabled"' : '')+'>'+this.value+'</option>')
		});
		if(ReqEvent!='') {
			$('#EventsSelector').val(ReqEvent);
		}
		if (history.pushState) {
			var newurl = window.location.origin + window.location.pathname + '?team='+ReqTeam+'&event='+ReqEvent;
			window.history.pushState({path:newurl},'',newurl);
		}
		SelectPhase();
	});
}

function SelectPhase(obj) {
	if(obj) {
		ReqEvent=obj.value;
		ReqPhase='';
	}
	if (history.pushState) {
		var newurl = window.location.origin + window.location.pathname + '?team='+ReqTeam+'&event='+ReqEvent+'&phase='+ReqPhase;
		window.history.pushState({path:newurl},'',newurl);
	}
	$.getJSON('ManIrmStatus-getElements.php?type=event&value='+$('#EventsSelector').val()+'&team='+$('#TeamSelector').val(), function(data) {
		$('#PhaseSelector').empty();
		$('#SearchBox').hide();
		$.each(data.elements, function() {
			$('#PhaseSelector').append('<option value="'+this.key+'" class="'+this.class+'"'+(this.class=='disabled' ? ' disabled="disabled"' : '')+'>'+this.value+'</option>')
		});
		if(ReqPhase!='') {
			$('#PhaseSelector').val(ReqPhase);
		}
		if (history.pushState) {
			var newurl = window.location.origin + window.location.pathname + '?team='+ReqTeam+'&event='+ReqEvent+'&phase='+ReqPhase;
			window.history.pushState({path:newurl},'',newurl);
		}
		ShowItems();
	});
}

function ShowItems(obj) {
	var order;
	if(obj) {
		ReqPhase=obj.value;
	}
	if (history.pushState) {
		var newurl = window.location.origin + window.location.pathname + '?team='+ReqTeam+'&event='+ReqEvent+'&phase='+ReqPhase;
		window.history.pushState({path:newurl},'',newurl);
	}
	$.getJSON('ManIrmStatus-getElements.php?type=phase&value='+$('#PhaseSelector').val()
			+'&team='+$('#TeamSelector').val()
			+'&event='+$('#EventsSelector').val()
			+'&order='+$('[active="1"]').attr('ord')
			+'&ordertype='+$('[active="1"]').attr('type')
			+'&search='+$('#SearchText').val(), function(data) {
		$('#ResultsTable').empty();
		$('#SearchBox').show();
		$.each(data.elements, function() {
			var row = $('<tr id="'+this.IrmKey+'" class="rowHover Bye-'+this.Bye+'">' +
				'<td>'+this.Bib+'</td>' +
				'<td>'+this.Athlete+'</td>' +
				'<td>'+this.Noc+'</td>' +
				'<td>'+this.Country+'</td>' +
				'<td>'+this.Score+'</td>' +
				'<td><input type="text" value="'+this.Note+'" onchange="updateNote(this)"></td>' +
				'<td org="'+this.IrmType+'" ref="IrmSelector">'+IrmSelector+'</td>' +
				'<td org="'+this.Bye+'" ref="Bye">'+ByeSelector+'</td>' +
				'<td org="'+this.QualRank+'" ref="QualRank">'+this.QualRank+'</td>' +
				'<td org="'+this.SubClassRank+'" ref="SubClassRank" SubClass="'+this.SubClass+'">'+(this.SubClass ? this.SubClass+': ' : '')+'<span>'+this.SubClassRank+'</span></td>' +
				'<td org="'+this.FinRank+'" ref="FinRank">'+this.FinRank+'</td>' +
				'<td ref="confirmButton">'+EditRankIcon+'</td>' +
				'</tr>')
			row.find('[ref="IrmSelector"] select').val(this.IrmType);
			row.find('[ref="Bye"] select').val(this.Bye);
			if(this.SecondMatch) {
				if(this.SecondMatch=='1') {
					row.toggleClass('BorderBottom', true);
				} else {
					row.toggleClass('BorderTop', true);
				}
			}
			$('#ResultsTable').append(row);
		});
		$('[ref="FinRank"]').toggleClass('hide', data.hideFinals);
		$('[ref="SubClassRank"]').toggleClass('hide', data.hideSubClass);
		$('[ord="SubClassRank"]').html(data.ScTitle);
		$('[ref="Bye"]').toggleClass('hide', !data.HasBye);
	});

}

function editIRM(obj) {
	var row = $(obj).closest('tr');
	var QuRankValue=row.find('[ref="QualRank"]').attr('org');
	var QuSCRankValue=row.find('[ref="SubClassRank"]').attr('org');
	var QuFinRankValue=row.find('[ref="FinRank"]').attr('org');
/*	if(obj.value==20) {
		QuRankValue=QuDeranking;
		QuSCRankValue=QuDeranking;
		QuFinRankValue=QuDeranking;
	} else if($('#PhaseSelector').val()=='Q') {
		if(obj.value==15) {
			QuRankValue=QuDisqualify;
			QuSCRankValue=QuDisqualify;
			QuFinRankValue=QuDisqualify;
		} else {
			QuRankValue=QuDidnotstart;
			QuSCRankValue=QuDidnotstart;
			QuFinRankValue=QuDidnotstart;
		}
	}
*/
	row.find('[ref="QualRank"]').html('<input type="number" ref="newQualRank" value="' + QuRankValue + '">');
	if($('#PhaseSelector').val()=='Q' || $('#PhaseSelector').val()=='E0' || $('#PhaseSelector').val()=='E1') {
		row.find('[ref="SubClassRank"] span').html('<input type="number" ref="newSubClassRank" value="' + QuSCRankValue + '">');
	}
	row.find('[ref="FinRank"]').html('<input type="number" ref="newFinRank" value="' + QuFinRankValue + '">');
	row.find('[ref="confirmButton"]').html('<input type="button" value="'+strCancel+'" onclick="abortIRM(this)"><input type="button" value="'+strOk+'" onclick="confirmIRM(this)">');
}

function showHelp(id) {
	$.dialog({
		content:'<div style="line-height:1.3em;min-height:1.5em">'+$('#'+id).html()+'</div>',
		title:'',
		useBootstrap:false,
		backgroundDismiss:true,
	});
}

function setIRM(obj) {
	var row = $(obj).closest('tr');
	var QuRankValue=row.find('[ref="QualRank"]').attr('org');
	var QuSCRankValue=row.find('[ref="SubClassRank"]').attr('org');
	var QuFinRankValue=row.find('[ref="FinRank"]').attr('org');
	if(obj.value==20) {
		QuRankValue=QuDeranking;
		QuSCRankValue=QuDeranking;
		QuFinRankValue=QuDeranking;
	} else if($('#PhaseSelector').val()=='Q') {
		if(obj.value==15 && $('#PhaseSelector').val()=='Q') {
			QuRankValue=QuDisqualify;
			QuSCRankValue=QuDisqualify;
			QuFinRankValue=QuDisqualify;
		} else {
			QuRankValue=QuDidnotstart;
			QuSCRankValue=QuDidnotstart;
			QuFinRankValue=QuDidnotstart;
		}
	}

	row.find('[ref="QualRank"]').html('<input type="number" ref="newQualRank" value="' + QuRankValue + '">');
	if($('#PhaseSelector').val()=='Q') {
		row.find('[ref="SubClassRank"] span').html('<input type="number" ref="newSubClassRank" value="' + QuSCRankValue + '">');
	}
	row.find('[ref="FinRank"]').html('<input type="number" ref="newFinRank" value="' + QuFinRankValue + '">');
	row.find('[ref="confirmButton"]').html('<input type="button" value="'+strCancel+'" onclick="abortIRM(this)"><input type="button" value="'+strOk+'" onclick="confirmIRM(this)">');
	$('[ref="IrmSelector"] select').prop('disabled', true);
}

function setBye(obj) {
	if(!confirm(strConfirmBye)) {
		return;
	}

	var row = $(obj).closest('tr');
	var bye = row.find('[ref="Bye"]');

	$.getJSON('ManIrmStatus-setIrm.php?item=' + row.attr('id')
		+ '&bye='+bye.find('select').val(),
		function(data) {
			if(data.error==0) {
				bye.attr('org', bye.find('select').val());
				if(window.opener) {
					window.opener.location.reload()
				}
			} else {
				bye.find('select').val(bye.attr('org'));
			}
			if(data.msg!='') {
				alert(data.msg);
			}
		});
}

function abortIRM(obj) {
	var row = $(obj).closest('tr');

	row.find('[ref="QualRank"]').html( row.find('[ref="QualRank"]').attr('org') );
	row.find('[ref="FinRank"]').html( row.find('[ref="FinRank"]').attr('org'));
	row.find('[ref="SubClassRank"] span').html( row.find('[ref="SubClassRank"]').attr('org'));
	row.find('[ref="IrmSelector"] select').val( row.find('[ref="IrmSelector"]').attr('org'));
	row.find('[ref="confirmButton"]').html(EditRankIcon);
	$('[ref="IrmSelector"] select').prop('disabled', false);
}

function confirmIRM(obj) {
	if(!confirm(strConfirm)) {
		abortIRM(obj);
		return;
	}

	var row = $(obj).closest('tr');
	var irm = row.find('[ref="IrmSelector"]');
	var irmVal = row.find('[ref="IrmSelector"] select');

	$.getJSON('ManIrmStatus-setIrm.php?item=' + row.attr('id')
		+ '&irm='+irmVal.val()
		+ '&qual='+row.find('[ref="newQualRank"]').val()
		+ '&sub='+row.find('[ref="newSubClassRank"]').val()
		+ '&fin='+row.find('[ref="newFinRank"]').val(),
		function(data) {
		if(data.error==0) {
			irm.attr('org', irmVal.val());
			row.find('[ref="QualRank"]').attr('org', data.qual );
			row.find('[ref="SubClassRank"]').attr('org', data.sub );
			row.find('[ref="FinRank"]').attr('org', data.fin );

			if(data.OpStatusChange) {
				$('#'+data.OpStatusChange).find('[ref="Bye"]').val(2);
				$('#'+data.OpStatusChange).find('[ref="FinRank"]').attr('org', 0).html(0);
				row.find('[ref="FinRank"]').html(data.FinRank).attr('org',data.FinRank);
				row.find('[ref="Bye"]').val(0);
			}

			if(window.opener) {
				window.opener.location.reload()
			}
		}
		abortIRM(obj);
		if(data.msg!='') {
			alert(data.msg);
		}
	});
}

function updateNote(obj) {
	var row = $(obj).closest('tr');
	var note = obj.value;

	$.getJSON('ManIrmStatus-setIrm.php?item=' + row.attr('id') + '&note='+note,
		function(data) {
		if(data.error==0) {
		}
	});
}

function setOrder(obj) {
	var orderType= ($(obj).attr('type')=='ASC' && $(obj).attr('active')==1 ? 'DESC' : 'ASC');
	$('[active="1"]').each(function() {
		$(this).attr('active','');
	})
	$(obj).attr('type', orderType);
	$(obj).attr('active', 1);
	ShowItems();
}
