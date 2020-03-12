/**
 * Created by deligant on 03/07/17.
 */

function updateLanguage(obj) {
    $('#date-'+obj.id.substr(5)).toggleClass('updated', false);
    $.getJSON('UpdateLanguage.php?'+obj.id, function(data) {
        $('#date-'+data.lang).html(data.date)
            .toggleClass('updated', true);
    });
}

function updateAllLanguages() {
    $('[id^="lang="]').each(function() {
        updateLanguage(this);
    });
}