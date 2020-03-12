function ActivateACL() {
    $.getJSON('UpdateFeature.php?AclOnOff='+$('#AclEnable').val()+"&AclRecord="+$('#AclRecord').val(), function (data) {
        $('#AclEnable').val(data.AclEnable);
        $('#AclRecord').val(data.AclRecord);
    });
}

function createList(JsonData) {
    $("#ipList").empty();
    $.each( JsonData, function( i, item ) {
        trHTML = '<tr id="row_'+i+'" ip="'+item.Ip+'" class="rowHover">' +
            '<td class="Center">' +
                '<input type="button" onclick="deleteIp(\''+item.Ip+'\');" value="'+CmdDelete+'"><br>' +
                '<img src="'+RootDir+'Common/Images/ACL0.png" style="height: 12px; margin: 7px 2px 5px ;" onclick="changeAll('+i+',\'0\')">'+
                '<img src="'+RootDir+'Common/Images/ACL1.png" style="height: 12px; margin: 7px 2px 5px ;" onclick="changeAll('+i+',\'1\')">'+
                '<img src="'+RootDir+'Common/Images/ACL2.png" style="height: 12px; margin: 7px 2px 5px ;" onclick="changeAll('+i+',\'2\')">'+
            '</td>' +
            '<td class="aclIP" onclick="copyDetails(\''+item.Ip+'\',\''+item.Name+'\')">'+item.Ip+'</td>' +
            '<td onclick="copyDetails(\''+item.Ip+'\',\''+item.Name+'\')">'+item.Name+'</td>';
        for(var j=0; j<optNo; j++) {
            if(item.Opt[j]===undefined) {
                item.Opt[j] = 0
            }
            trHTML += '<td class="Center"><img class="ClickableDiv" style="margin: 5px;" id="opt_'+i+'_'+j+'" src="'+RootDir+'Common/Images/ACL'+item.Opt[j]+'.png" onclick="changeFeature('+i+','+j+')"></td>';
        }
        trHTML += '</tr>';
        $('#ipList').append(trHTML);
    });
}

function copyDetails(ip,nick) {
    $('#newIP').val(ip);
    $('#newNick').val(nick);
}

function updateList() {
    $.getJSON('UpdateFeature.php', function(data) {
        createList(data);
    });
}

function deleteIp(Ip) {
    if(confirm(AreYouSure)) {
        $.getJSON('UpdateFeature.php?deleteIP=' +Ip, function (data) {
            createList(data);
        });
    }
}

function saveIp() {
    $.getJSON('UpdateFeature.php?IP='+$('#newIP').val()+"&Name="+$('#newNick').val(), function(data) {
        copyDetails('','');
        createList(data);
    });
}

function changeFeature(id, feature) {
    var ChangeIp = $('#row_'+id).attr('ip');
    $.getJSON('UpdateFeature.php?featureIP='+ChangeIp+"&featureID="+feature, function(data) {
        if(data[id].Ip==ChangeIp) {
            $('#opt_'+id+'_'+feature).attr('src',RootDir+'Common/Images/ACL'+(data[id].Opt[feature]===undefined ? '0' : data[id].Opt[feature])+'.png');
        } else {
            createList(data);
        }
    });
}

function changeAll(id, level) {
    if(confirm(AreYouSure)) {
        var ChangeIp = $('#row_' + id).attr('ip');
        $.getJSON('UpdateFeature.php?featureIP=' + ChangeIp + "&levelID=" + level, function (data) {
            createList(data);
        });
    }
}