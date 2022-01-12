function saveWaDivision(divCode) {
    $.getJSON('ExportIWS.php?updateDiv=' + encodeURIComponent(divCode) + '&Value=' + encodeURIComponent($('#sel_'+divCode).val()), function(data) {
        if(data.error===0) {
            $('#div_'+divCode).removeClass('notValid').addClass('isValid');
        } else {
            $('#div_'+divCode).removeClass('isValid').addClass('notValid');
        }
    });
    console.log(divCode);
}