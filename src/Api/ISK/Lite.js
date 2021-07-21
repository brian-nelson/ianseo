$(function() {
	if(ImportType==0) {
		$('.Cl-ImportType').hide();
	}
	if(CalcDivClI==0) {
		$("#doCalcClDivInd").hide();
	}
	if(CalcDivClT==0) {
		$("#doCalcClDivTeam").hide();
	}
	if(CalcFinI==0) {
		$("#doCalcFinInd").hide();
	}
	if(CalcFinT==0) {
		$("#doCalcFinTeam").hide();
	}

});

function LiteAction(obj) {
	$.getJSON('Lite-Action.php', {'act':obj.name, 'val':obj.value}, function(data) {
		if(data.error==0) {
			if(obj.value==0) {
				if(obj.name.indexOf('Calc')===0) {
					LiteButton($("#do" + obj.name)[0]);
					$("#do" + obj.name).hide();
				} else {
					$(".Cl-" + obj.name).hide();
				}
			} else {
				if(obj.name.indexOf('Calc')===0) {
					$("#do" + obj.name).show();
				} else {
					$(".Cl-" + obj.name).show();
				}
			}
		}
	});
}
function LiteButton(obj) {
	$.getJSON('Lite-Action.php', {'act':obj.id}, function(data) {
		if(data.msg!='') {
			alert(data.msg);
		}
	});
}

function LiteDelete(obj) {
	if(confirm(MsgConfirm)) {
		LiteButton(obj);
	}
}
