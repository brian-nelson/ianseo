$(function() {
	$.getJSON('ManTargets-Action.php?act=list', function(data) {
		if(data.error==0) {
			$('#tbody').html(data.table);
			$('#categories').html(data.categories);
		}
	})
});


function resetTarget() {
	$('#edit').toggleClass('warning', false);
	$('#TdRegExp').val('');
	$('#TdName').val('');
	$('#TdClasses').val('');
	$('#TdDefault').prop('checked', false);
	$('[id^=TdFace]').val('');
	$('[id^=TdDiam]').val('');
}

function saveTarget() {
	let form={
		act:'new',
		RegExp:$('#TdRegExp').val(),
		TfName:$('#TdName').val(),
		cl:$('#TdClasses').val(),
		isDefault:$('#TdDefault').is(':checked') ? 1 : 0,
		tdface:{},
		tddiam:{},
	}

	for (var i=1;i<=numDist;++i) {
		form.tdface[i]=$('#TdFace'+i).val();
		form.tddiam[i]=$('#TdDiam'+i).val();
	}

	$('#edit').toggleClass('warning', false);
	$.getJSON('ManTargets-Action.php', form, function(data) {
		if(data.error==0) {
			$('#tbody').html(data.table);
			$('#categories').html(data.categories);
			resetTarget();
		} else {
			$('#edit').toggleClass('warning', true);
		}
	});
}

function updateTarget(obj) {
	let form={
		act:'update',
		row:$(obj).closest('tr').attr('ref'),
		dist:$(obj).closest('td').attr('ref'),
		target:$(obj).closest('tr').find('[name="target"]').val(),
		diameter:$(obj).closest('tr').find('[name="diameter"]').val(),
	}

	$(obj).closest('tr').toggleClass('warning', false);
	$.getJSON('ManTargets-Action.php', form, function(data) {
		if(data.error==0) {
			$('#tbody').html(data.table);
			$('#categories').html(data.categories);
			resetTarget();
		} else {
			$(obj).closest('tr').toggleClass('warning', true);
		}
	});
}

function deleteTarget(obj) {
	if (confirm(StrConfirm)) {
		$.getJSON('ManTargets-Action.php?act=delete&row='+$(obj).closest('tr').attr('ref'), function(data) {
			if(data.error==0) {
				$('#tbody').html(data.table);
				$('#categories').html(data.categories);
			}
		});
	}
}
