
$(document).ready(function() {
    getList();
});

function getList() {
    $.getJSON('PrintoutComments.php?action=list', renderList);
}

function selectSession() {
    $.getJSON('PrintoutComments.php?action=session&key='+$('#cmbSessions').val(), function(data){
        $('[id^="chkEv_"]').each(function() {
            $('#' + this.id).prop('checked', false);
        });
        if(data.error==0) {
            $(data.data).each(function() {
                $('#'+this).prop('checked', true);
            });

        }
    });
}

function chkBulkSelection() {
    var isChecked = $('#chkBulk').is(':checked');
    $('[id^="chkEv_"]').each(function() {
        if(isChecked) {
            $('#' + this.id).prop('checked', true);
        } else {
            $('#' + this.id).prop('checked', false);
        }
    });
}

function bulkSave(what) {
    var selectedChk = [];
    $('[id^="chkEv_"]').each(function() {
        if($('#'+this.id).is(':checked')) {
            selectedChk.push(this.id);
        }
    });
    if(selectedChk.length!=0) {
        $.getJSON('PrintoutComments.php?action=bulk&what='+what+'&key='+encodeURIComponent(selectedChk.join('|'))+'&value='+encodeURIComponent($('#txt'+what).val()), renderList);
    }
}

function updateField(obj) {
    if($(obj).val() !== $(obj).attr('oldValue')) {
        $(obj).addClass('red');
        $.getJSON('PrintoutComments.php?action=set&key='+$(obj).attr('id')+'&value='+encodeURIComponent($(obj).val()), renderList);
    } else {
        $(obj).removeClass('red');
    }
}

function renderList(data) {
    if(data.error==0) {
        $(data.data).each(function() {
            $('#txtQ_'+this.id).val(this.Q).attr('oldValue',this.Q).removeClass('red');
            $('#txtF_'+this.id).val(this.F).attr('oldValue',this.F).removeClass('red');
        });
    }
}
