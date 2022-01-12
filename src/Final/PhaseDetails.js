$(function() {
	getDetails();
});

function changeType(obj) {
	TeamType=obj.value;
	if (history.pushState) {
		var newurl = window.location.origin + window.location.pathname + '?act=get&team='+TeamType+'&option='+OptionType;
		window.history.pushState({path:newurl},'',newurl);
	}
	getDetails();
}

function changeOption(obj) {
	OptionType=obj.value;
	if (history.pushState) {
		var newurl = window.location.origin + window.location.pathname + '?act=get&team='+TeamType+'&option='+OptionType;
		window.history.pushState({path:newurl},'',newurl);
	}
	getDetails();
}

function getDetails() {
	$.getJSON('PhaseDetails-actions.php?act=get&team='+TeamType+'&option='+OptionType, function(data) {
		if(data.error==0) {
			$('#TableBody').empty();
			switch(OptionType) {
				case 'AthButt':
					$('.phhide').hide();
					$('.varCol').prop('colspan', 9);
					$('.PhaseLegend').html('<div class="Left"><div class="opp-badge1 active">1</div><div class="opp-badge2 active">2</div>'+data.legend1+'</div><div><div class="opp-multimatch"></div><div class="opp-multimatch active"></div>'+data.legend2+'</div>');
					$.each(data.events, function() {
						var row='<tr ref="'+this.code+'">' +
							'<th class="Left">'+this.event+'</th>';
						$.each(this.phases, function() {
							var select='';
							if(this.badge!='') {
								select='' +
									'<div onclick="changePhaseOption(this, '+this.ph+')" class="opp-badge1'+(this.badge==1 ? ' active' : '')+'">1</div>' +
									'<div onclick="changePhaseOption(this, '+this.ph+')" class="opp-badge2'+(this.badge==2 ? ' active' : '')+'">2</div>' +
									'<div onclick="changeMatchOption(this, '+this.ph+')" class="opp-multimatch'+(this.double==1 ? ' active' : '')+'"></div>';
							}
							row+='<td class="Center ph'+this.ph+'">'+select+'</td>';
						})
						row+='</tr>';
						$('#TableBody').append(row);
					});
					$.each(data.show, function(a,b) {
						if(b) {
							$('.'+a).show();
						} else {
							$('.'+a).hide();
						}
					});
					break;
				case 'ArrowPhase':
					$('.phhide').show();
					$('.varCol').prop('colspan', 13);
					$('.PhaseLegend').html('<div class="topRow"><i class="fa fa-circle fa-lg active mr-2"></i>'+data.legend1+'</div><div><i class="fa fa-circle fa-lg active mr-2"></i>'+data.legend2+'</div>');
					$.each(data.events, function() {
						var row='<tr ref="'+this.code+'" class="topRow">' +
							'<th rowspan="2"  class="Left">'+this.event+'</th>' +
							'<th>'+this.eText+'</th>' +
							'<td class="Center"><input type="number" onchange="changeValue(this)" name="eEnds" value="'+this.eEnds+'"></td>' +
							'<td class="Center"><input type="number" onchange="changeValue(this)" name="eArrows" value="'+this.eArrows+'"></td>' +
							'<td class="Center"><input type="number" onchange="changeValue(this)" name="eSO" value="'+this.eSO+'"></td>';
						$.each(this.phases, function() {
							var select='';
							if(this.val!='') {
								select='<i onclick="changePhaseArrows(this, '+this.ph+')" class="fa fa-circle fa-2x '+(this.val==1 ? ' active' : ' inactive')+'" ref="1"></i>' ;
							}
							row+='<td class="Center ph'+this.ph+'">'+select+'</td>';
						})
						row+='</tr>';
						row+='<tr ref="'+this.code+'">' +
							'<th>'+this.fText+'</th>' +
							'<td class="Center"><input type="number" onchange="changeValue(this)" name="fEnds" value="'+this.fEnds+'"></td>' +
							'<td class="Center"><input type="number" onchange="changeValue(this)" name="fArrows" value="'+this.fArrows+'"></td>' +
							'<td class="Center"><input type="number" onchange="changeValue(this)" name="fSO" value="'+this.fSO+'"></td>';
						$.each(this.phases, function() {
							var select='';
							if(this.val!='') {
								select='<i onclick="changePhaseArrows(this, '+this.ph+')" class="fa fa-circle fa-2x '+(this.val==2 ? ' active' : ' inactive')+'" ref="2"></i>' ;
							}
							row+='<td class="Center ph'+this.ph+'">'+select+'</td>';
						})
						row+='</tr>';
						$('#TableBody').append(row);
					});
					$.each(data.show, function(a,b) {
						if(b) {
							$('.'+a).show();
						} else {
							$('.'+a).hide();
						}
					});
					break;
			}
		}
	});
}

function changePhaseOption(obj, phase) {
	$.getJSON('PhaseDetails-actions.php?act=set&team='+TeamType+'&option='+OptionType+'&phase='+phase+'&event='+$(obj).closest('tr').attr('ref')+'&value='+$(obj).html(), function(data) {
		if(data.error==0) {
			$(obj).closest('td').find('.opp-badge1.active').toggleClass('active', false);
			$(obj).closest('td').find('.opp-badge2.active').toggleClass('active', false);
			$(obj).toggleClass('active', true);
		}
	});
}

function changeMatchOption(obj, phase) {
	$.getJSON('PhaseDetails-actions.php?act=set&team='+TeamType+'&option=DoubleMatch&phase='+phase+'&event='+$(obj).closest('tr').attr('ref')+'&value='+($(obj).hasClass('active')?0:1), function(data) {
		if(data.error==0) {
			$(obj).closest('td').find('.opp-multimatch.active').toggleClass('active', false);
			if(data.double=='1') {
				$(obj).toggleClass('active', true);
			}
		}
	});
}

function changePhaseArrows(obj, phase) {
	$.getJSON('PhaseDetails-actions.php?act=set&team='+TeamType+'&option='+OptionType+'&phase='+phase+'&event='+$(obj).closest('tr').attr('ref')+'&value='+$(obj).attr('ref'), function(data) {
		if(data.error==0) {
			var ref = $(obj).closest('tr').attr('ref');
			$('[ref="'+ref+'"]').find('.ph'+phase+' i').toggleClass('active', false).toggleClass('inactive', true);
			$(obj).toggleClass('active', true).toggleClass('inactive', false);
		}
	});
}

function changeValue(obj) {
	$(obj).toggleClass('updated', false);
	$.getJSON('PhaseDetails-actions.php?act=set&team='+TeamType+'&option='+OptionType+'&field='+obj.name+'&event='+$(obj).closest('tr').attr('ref')+'&value='+obj.value, function(data) {
		if(data.error==0) {
			obj.defaultvalue=obj.value;
			$(obj).toggleClass('updated', true);
			$(obj).closest('td').toggleClass('updated', true);
			setTimeout(function() {
				$(obj).closest('td').toggleClass('updated', false);
				$(obj).toggleClass('updated', false);
				}, 1000);
		} else {
			obj.value=obj.defaultValue;
		}
	});
}
