/**
 * Created by deligant on 10/05/17.
 */

function loadCombo(obj) {
    $.getJSON('GateControl-getCombo.php?toid='+obj.value+'&today='+(obj.checked ? '1' : '0'), function(data) {
        if(data.error==0) {
            $('#Combo-'+obj.value).html(data.html);
        }
    });
}

function setSession(obj) {
    $('[name="'+obj.name+'"]').each(function() {
        if(this!=obj) {
            if(this.checked) {
                $.getJSON('GateControl-toggleSession.php?toid='+$(this).attr('tour')+'&session='+this.value, function(data) {
                    if(data.error!=0) {
                        this.checked=!this.checked;
                    }
                });
            }
            this.checked=false;
        }
    });
    $.getJSON('GateControl-toggleSession.php?toid='+$(obj).attr('tour')+'&session='+obj.value, function(data) {
        if(data.error!=0) {
            objs.checked=!obj.checked;
        }
    });

}

function clearField(ToId) {
    $.getJSON('GateControl-clearField.php?toid='+ToId, function(data) {
        if(data.error==0) {
            alert(data.msg);
        }
    });

}