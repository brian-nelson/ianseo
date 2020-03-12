/**
 * Created by deligant on 10/05/17.
 */

function getSituation(ToId) {
    $.getJSON('GateSituation-getSituation.php?toid='+ToId, function(data) {
        if(data.error==0) {
            $('#Sit-'+ToId).html(data.html);
        }
    });

}