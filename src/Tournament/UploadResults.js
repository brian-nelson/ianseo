var toUpload = 0;
var toRefresh = 0;
var toLastUpload = 0;

$(function() {
	toggleOris();
});

function toggleDeleteColor(obj) {
	$('.Deletable').toggleClass('ToDelete', obj.checked)
}

function toggleOris() {
	if($('#oris')[0].checked) {
		$('.OrisShow').show();
		$('.OrisHide').hide();
		$('.OrisHide').find('input:checked').prop('checked', false)
	} else {
		$('.OrisShow').hide();
		$('.OrisShow').find('input:checked').prop('checked', false)
		$('.OrisHide').show();
	}
}

function toggleAccordion(obj) {
	if($(obj).attr('status')=='on') {
		$('#Tbody-'+$(obj).attr('ref')).toggleClass('hidden', true);
		$(obj).attr('status', 'off');
		$(obj).find('i').toggleClass('fa-caret-down', false).toggleClass('fa-caret-right', true);
	} else {
		$('#Tbody-'+$(obj).attr('ref')).toggleClass('hidden', false);
		$(obj).attr('status', 'on');
		$(obj).find('i').toggleClass('fa-caret-down', true).toggleClass('fa-caret-right', false);
	}
	var lnk='?';
	$('[id^="Tbody-"]:visible').each(function() {
		lnk+=this.id.substring(6)+'&';
	});
	history.pushState({}, '', lnk);
}

// Sends the selected checkboxes
function doUpload() {
	clearTimeout(toUpload);
	$('#msg').html('');
	// var sendData='';

	// aborts if a file ise being upload without a name
	if($('#FIL').val()!='' && $('#FILname').val()=='') {
		alert('Missing name');
		return;
	}

	// aborts if a file ise being upload without a name
	if($('#URL').val()!='' && $('#URLname').val()=='') {
		alert('Missing description');
		return;
	}

	$('body').append('<div class="backDrop" id="backDrop"><img src="../Common/Images/ianseo.svg" class="rotate"></div>')

	var fdata=new FormData($('#uploads')[0]);

	$.ajax({
		url: 'UploadResults-upload.php',
		type: 'POST',
		data: fdata, // The form with the file    inputs.
		processData: false,                          // Using FormData, no need to process data.
		contentType:false,
		dataType:'json'
	}).done(function(data){
		if(data.error==0) {
			$('#msg').html(data.msg);
			if($('#btnDelOnline').prop('checked')==true) {
				$('.Deletable [type="checkbox"]').prop('checked', false);
				$('.Deletable').toggleClass('ToDelete', false)
			} else {
				$('.removeAfterUpload').prop('checked', false);
			}
			$('[type="text"].removeAfterUpload').val('');
			$('[type="number"].removeAfterUpload').val('');
			$('[type="file"].removeAfterUpload').val('');

			// deal with the online files
			$('[id^="Files-"]').remove();
			$.each(data.files, function() {
				$('#FIL').closest('.flexLines').before('<div class="flexLines" id="Files-'+this.IFName+'">' +
					'<div>'+this.IFName+'</div>' +
					'<div>'+StrOrder+': <input type="number" name="FilesOrder['+this.IFName+']" size="3" value="'+this.IFOrder+'"></div>' +
					'<div>'+StrDescription+': <input type="text" name="FilesDescr['+this.IFName+']" value="'+this.IFDescr+'" /></div>' +
					'<div><input type="checkbox" value="'+this.IFName+'" name="FilesRemove[]" class="removeAfterUpload"/>'+StrDelete+'</div>' +
					'</div>');
			});

			// deal with the online URLs
			$('[id^="Urls-"]').remove();
			$.each(data.urls, function() {
				$('#URL').closest('.flexLines').before('<div class="flexLines" id="Urls-'+this.ILId+'">' +
					'<div>'+StrUrl+': <input type="text" name="UrlsUrl['+this.ILId+']" value="'+this.ILUrl+'"/></div>' +
					'<div>'+StrOrder+': <input type="number" name="UrlsOrder['+this.ILId+']" size="3" value="'+this.ILOrder+'"></div>' +
					'<div>'+StrDescription+': <input type="text" name="UrlsDescr['+this.ILId+']" value="'+this.ILDescr+'"/></div>' +
					'<div><input type="checkbox" value="'+this.ILId+'" name="UrlsRemove[]" class="removeAfterUpload"/>'+StrDelete+'</div>' +
					'</div>');
			});
		} else {
			$.alert(data.msg)
		}

		$('#backDrop').remove();
	}).fail(function(){
		console.log("An error occurred, the files couldn't be sent!");
	}).always(function () {
		$.getJSON('UploadResults-status.php', function (data) {

			$('#sel_IndAbs').html(data.IndAbs);
			if(data.IndAbs !== '') {
				$('#tit_IndAbs').show();
			} else {
				$('#tit_IndAbs').hide();
			}
			$('#sel_TeamAbs').html(data.TeamAbs);
			if(data.TeamAbs !== '') {
				$('#tit_TeamAbs').show();
			} else {
				$('#tit_TeamAbs').hide();
			}
			if(data.IndAbs !== '' || data.TeamAbs !== '') {
				$('.tit_Abs').show();
			} else {
				$('.tit_Abs').hide();
			}

			$('#sel_IndBra').html(data.IndBra);
			if(data.IndBra !== '') {
				$('#tit_IndBra').show();
			} else {
				$('#tit_IndBra').hide();
			}
			$('#sel_TeamBra').html(data.TeamBra);
			if(data.TeamBra !== '') {
				$('#tit_TeamBra').show();
			} else {
				$('#tit_TeamBra').hide();
			}
			if(data.IndBra !== '' || data.TeamBra !== '') {
				$('.tit_Bra').show();
			} else {
				$('.tit_Bra').hide();
			}
			$('#sel_IndFin').html(data.IndFin);
			if(data.IndFin !== '') {
				$('#tit_IndFin').show();
			} else {
				$('#tit_IndFin').hide();
			}
			$('#sel_TeamFin').html(data.TeamFin);
			if(data.TeamFin !== '') {
				$('#tit_TeamFin').show();
			} else {
				$('#tit_TeamFin').hide();
			}
			if(data.IndFin !== '' || data.TeamFin !== '') {
				$('.tit_Fin').show();
			} else {
				$('.tit_Fin').hide();
			}

			if(data.Medals || data.FinalBook) {
				$('.tit_MedBook').show();
			} else {
				$('.tit_MedBook').hide();
			}
			if(data.Medals) {
				$('.tit_Med').show();
			} else {
				$('.tit_Med').hide();
			}
			if(data.FinalBook) {
				$('.tit_Book').show();
			} else {
				$('.tit_Book').hide();
			}

			['QualificationInd[]','EliminationInd[]','FinalInd[]','BracketsInd[]','QualificationTeam[]','FinalTeam[]','BracketsTeam[]'].forEach(function (ph) {
				fdata.getAll(ph).forEach(function (item) {
					$("input[name='"+ph+"'][value='"+item+"']").prop('checked', true);
				});
			});
			AutoUpload();
		})
	});
}

function setAllCheck(name,rifCheck) {
	var rif=document.getElementById(rifCheck);
	var chks=document.getElementsByName(name);

	for (var i=0;i<chks.length;++i)
		if (chks[i].type=='checkbox')
			chks[i].checked=rif.checked;
}

function SelectBook(obj) {
	if(obj.checked) {
		// expands all sections only if we ask for the complete book...
		$('.AccordionToggle[status="off"]').each(function() {
			if($(this).attr('ref')=='PDFS') {
				return;
			}
			toggleAccordion(this);
		});
	}

	// NEW ORIS 2020
	$('.InBook [type="checkbox"]').prop('checked', obj.checked);
}

function AutoUpload() {
	clearTimeout(toUpload);
	clearInterval(toRefresh);
	$('#toCountDown').html('');
	if($('#AutoUploadToggle').is(':checked') && parseInt($('#AutoUploadTimer').val())!=0) {
		toLastUpload = new Date();
		toUpload = setTimeout(doUpload, parseInt($('#AutoUploadTimer').val())*60000);
		toRefresh = setInterval(refreshCountDown, 1000);
	}
}

function refreshCountDown() {
	endTime = new Date();
	$('#toCountDown').html((parseInt($('#AutoUploadTimer').val())*60 - ((endTime - toLastUpload) / 1000)).toFixed(0));
}