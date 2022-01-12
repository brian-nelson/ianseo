$(function() {
	$.getJSON('./ManStaffField_Edit.php', function(data) {
		if(data.error==0) {
			resetFieldStaff();
			$('#FieldStaff').html(data.table);
		} else {
			alert(data.msg);
		}
	});
});

function addFieldStaff() {
	if($('#new_Matr').val()==''
			|| $('#new_FamilyName').val()==''
			|| $('#new_GivenName').val()==''
			|| $('#new_Gender').val()==''
			|| $('#new_CountryCode').val()==''
			|| $('#new_CountryName').val()==''
			|| $('#new_Type').val()==''
			) {
		alert(NoEmptyField);
		return;
	}
	let form={
		Code:$('#new_Matr').val(),
		FamilyName:$('#new_FamilyName').val(),
		GivenName:$('#new_GivenName').val(),
		Gender:$('#new_Gender').val(),
		CountryCode:$('#new_CountryCode').val(),
		CountryName:$('#new_CountryName').val(),
		Type:$('#new_Type').val(),
		ID:'new',
		act:'new',
	};
	$.getJSON('./ManStaffField_Edit.php', form, function(data) {
		if(data.error==0) {
			resetFieldStaff();
			$('#FieldStaff').html(data.table);
		} else {
			alert(data.msg);
		}
	});
}

function editFieldStaff(obj) {
	let row=$(obj).closest('tr');
	if(row.find('[name="Code"]').val()==''
			|| row.find('[name="FamilyName"]').val()==''
			|| row.find('[name="GivenName"]').val()==''
			|| row.find('[name="Gender"]').val()==''
			|| row.find('[name="CountryCode"]').val()==''
			|| row.find('[name="Type"]').val()==''
			) {
		alert(NoEmptyField);
		return;
	}
	let form={
		Code:row.find('[name="Code"]').val(),
		FamilyName:row.find('[name="FamilyName"]').val(),
		GivenName:row.find('[name="GivenName"]').val(),
		Gender:row.find('[name="Gender"]').val(),
		CountryCode:row.find('[name="CountryCode"]').val(),
		CountryName:row.find('[name="CountryName"]').val(),
		Type:row.find('[name="Type"]').val(),
		ID:row.attr('ref'),
		act:'edit',
	};

	$.getJSON('./ManStaffField_Edit.php', form, function(data) {
		if(data.error==0) {
			resetFieldStaff();
			$('#FieldStaff').html(data.table);
		} else {
			alert(data.msg);
		}
	});
}

function deleteFieldStaff(obj) {
	let row=$(obj).closest('tr');
	if(confirm(AreYouSure)) {
		let form={
			ID:row.attr('ref'),
			act:'delete',
		};
		$.getJSON('./ManStaffField_Edit.php', form, function(data) {
			if(data.error==0) {
				resetFieldStaff();
				$('#FieldStaff').html(data.table);
			} else {
				alert(data.msg);
			}
		});
	}
}

function resetFieldStaff() {
	$('#new_Matr').val('');
	$('#new_FamilyName').val('');
	$('#new_GivenName').val('');
	$('#new_Gender').val('');
	$('#new_CountryCode').val('');
	$('#new_CountryName').val('');
	$('#new_Type').val('');
}

function FindFieldStaff(obj) {
	$.getJSON('./ManStaffField_Find.php?act=find&Code='+obj.value, function(data) {
		if(data.rows.length==1) {
			// we have a single person, so use it
			let fields=data.rows[0];
			$('#new_FamilyName').val(fields.FamName);
			$('#new_GivenName').val(fields.GivName);
			$('#new_Gender').val(fields.Gender);
			$('#new_CountryCode').val(fields.CoCode);
			$('#new_CountryName').val(fields.CoName);
		} else {
			searchFieldStaff(obj.value);
		}
	});
}

var SearchDialog;
function searchFieldStaff(code) {
	SearchDialog=$.dialog({
		title:'',
		type:'red',
		content:'<table class="Tabella">' +
			'<tr>' +
			'<th colspan="2" style="width: 10%">'+TitCode+'</th>' +
			'<th style="width: 25%">'+TitFName+'</th>' +
			'<th style="width: 25%">'+TitGName+'</th>' +
			'<th style="width: 5%">'+TitGender+'</th>' +
			'<th style="width: 5%">'+TitCoCode+'</th>' +
			'<th style="width: 20%">'+TitCountry+'</th>' +
			'<th style="width: 10%">'+TitDob+'</th>' +
			'</tr>' +
			'<tr>' +
			'<td colspan="2" class="Center"><input type="text" style="width: 95%" id="searchCode"'+(code ? ' value="'+code+'"' : '')+'></td>' +
			'<td class="Center"><input type="text" style="width: 97%" id="searchFName"></td>' +
			'<td class="Center"><input type="text" style="width: 97%" id="searchGName"></td>' +
			'<td class="Center"><select type="text" style="width: 95%" id="searchGender">' +
			'<option value="">--</option>' +
			'<option value="0">M</option>' +
			'<option value="1">W</option>' +
			'</select></td>' +
			'<td class="Center"><input type="text" style="width: 95%" id="searchCoCode"></td>' +
			'<td class="Center"><input type="text" style="width: 95%" id="searchCoName"></td>' +
			'<td></td>' +
			'</tr>' +
			'<tbody id="SearchTable"></tbody>' +
			'<tr><th colspan="8">' +
			'<div class="Button" onclick="doSearch()">Search</div>' +
			'<div class="Button" onclick="doClose(this)">Close</div>' +
			'</th></tr>' +
			'</table>',
		boxWidth: '75%',
		useBootstrap: false,
		closeIcon:false,
		backgroundDismiss:true,
		onContentReady:function() {
			if(code) {
				doSearch();
			}
		},
		onOpen: function () {
			$(document).keydown(function (event) {
				if (event.keyCode == 13) {
					doSearch()
				}
			});
		},
		onClose: function () {
			$(document).off('keydown');
		},

	});
}

function doSearch() {
	let form={
		Code:$('#searchCode').val(),
		FamilyName:$('#searchFName').val(),
		GivenName:$('#searchGName').val(),
		Gender:$('#searchGender').val(),
		CountryCode:$('#searchCoCode').val(),
		CountryName:$('#searchCoName').val(),
		act:'search',
	}
	$.getJSON('./ManStaffField_Find.php', form, function(data) {
		if(data.error==0) {
			let table='';
			$.each(data.rows, function() {
				table+='<tr onclick="doInsert(this)" style="height: 1.5rem;">' +
					'<td style="width: 3%; text-align: center"><i class="fa fa-plus mr-2"></i></td>' +
					'<td ref="Code" val="'+this.Code+'">'+this.Code+'</td>' +
					'<td ref="FamName" val="'+this.FamName+'">'+this.FamName+'</td>' +
					'<td ref="GivName" val="'+this.GivName+'">'+this.GivName+'</td>' +
					'<td ref="Gender" val="'+this.Gender+'">'+window['Gender'+this.Gender]+'</td>' +
					'<td ref="CoCode" val="'+this.CoCode+'">'+this.CoCode+'</td>' +
					'<td ref="CoName" val="'+this.CoName+'">'+this.CoName+'</td>' +
					'<td>'+this.DOB+'</td>' +
					'</tr>';
			});
			$('#SearchTable').html(table);
		} else {
			alert(data.msg);
		}
	});
}

function doClose() {
	SearchDialog.close();
}

function doInsert(obj) {
	$('#new_Matr').val($(obj).find('[ref="Code"]').attr('val'));
	$('#new_FamilyName').val($(obj).find('[ref="FamName"]').attr('val'));
	$('#new_GivenName').val($(obj).find('[ref="GivName"]').attr('val'));
	$('#new_Gender').val($(obj).find('[ref="Gender"]').attr('val'));
	$('#new_CountryCode').val($(obj).find('[ref="CoCode"]').attr('val'));
	$('#new_CountryName').val($(obj).find('[ref="CoName"]').attr('val'));
	doClose();
}
