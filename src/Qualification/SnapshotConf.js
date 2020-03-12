function toggleSnapshot(obj) {
    $.getJSON('SnapshotConf-Toggle.php', function (data) {
        if (data.error==0) {
            obj.checked=(data.status==1);

            if(data.status==1) {
                $('#Conf').show();
            } else {
                $('#Conf').hide();
            }
        }
    });
}

function rebuildSnapshot(ses, dist, from, to) {
    $.getJSON('MakeSnapshot.php?json=&numArrows=0&Session=' + ses + '&Distance=' + dist + '&fromTarget=' + from + '&toTarget=' + to, function (data) {
        if (data.Error==0) {
            alert(data.msg);
        }
    });
}