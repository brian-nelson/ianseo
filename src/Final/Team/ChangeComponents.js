var componentList = {};
var tEvCode='';
var tTeamId=0;
var tTeamSubId=0;

$(document).ready(function() {
    populateData();
});

function clearCombo(cmbId) {
    $('#'+cmbId).val(0);
    populateData();
}

function printTeamComponentForm() {
    var cmbEvent = $('#cmbEvent').val();
    var cmbTeam = $('#cmbTeam').val();
    window.open('PDFTeamDeclarationForm.php?EvCode='+cmbEvent+'&CoId='+cmbTeam, 'prnTeamDeclaration');
}

function editComponents(EvCode, TeamId, TeamSubId) {
    if(EvCode === tEvCode && TeamId === tTeamId && TeamSubId === tTeamSubId) {
        $('tbody tr.TeamLine').show().removeClass('selectedTeam');
        $('tbody tr.divider').show();
        $('.srcControls input').removeAttr('disabled');
        $('.srcControls select').removeAttr('disabled');
        $('#cmdEdit_' + EvCode + '_' + TeamId + '_' + TeamSubId).attr('value',cmdEdit);
        $('.editTeam').remove();
        $('tfoot').hide();
        tEvCode='';
        tTeamId=0;
        tTeamSubId=0;
    } else {
        tEvCode = EvCode;
        tTeamId = TeamId;
        tTeamSubId = TeamSubId;
        firstRow=0;
        $('tbody tr.TeamLine[grTeam!="' + EvCode + '_' + TeamId + '_' + TeamSubId + '"]').hide();
        $('tbody tr.TeamLine[grTeam="' + EvCode + '_' + TeamId + '_' + TeamSubId + '"]').addClass('selectedTeam');
        $('.srcControls input').attr('disabled','disabled');
        $('.srcControls select').attr('disabled','disabled');
        $('tbody tr.divider').hide();
        $('#cmdEdit_'+EvCode + '_' + TeamId + '_' + TeamSubId).attr('value',cmdBack);
        $.getJSON('ChangeComponents-data.php?EvCode='+EvCode+'&TeamId='+TeamId+'&TeamSubId='+TeamSubId, function(data) {
            if(data.error===0) {
                componentList = data.data;
                $.each(data.data, function (igroup, group) {
                    firstRow += (group.Athletes.length+1);
                });
                $.each(componentList, function (igroup, group) {
                    var Html = '<tr class="editTeam"><th colspan="6">&nbsp;</th>';
                    if(firstRow!=0) {
                        Html += '<td class="Center" colspan="2" rowspan="'+firstRow+'"><input type="button" id="cmdSave" value="'+cmdSave+'" onclick="saveComponents()"></td>';
                        firstRow=0;
                    }
                    Html += '</tr>';
                    $.each(group.Athletes, function (index, item) {
                        Html += '<tr class="editTeam"><td colspan="4"></td>'+
                            '<td id="td_'+item.Id+'"><input type="checkbox" qty="'+group.Qty+'" grp="'+group.Group+'" isF="'+item.isF+'" value="' + item.Id + '"' + (item.isF ? 'checked':'') + ' onclick="VerifyCheckbox()">'+item.Athlete+'</td>'+
                            '<td class="divClassContainer">'+(item.isQ ? '&#9654':'&#9655')+'&nbsp;'+item.Div+' - '+item.Cl+'</td>'+
                            '</tr>';
                    })
                    $('#lstBody').append(Html);

                });
                $('tfoot').show();
                VerifyCheckbox();
            }
        });
    }
}

function saveComponents() {
    var payLoad = [];
    $('input:checkbox').each(function () {
        if($(this).is(":checked")) {
            payLoad.push({Id: $(this).attr('value'), Grp: $(this).attr('grp')});
        }
    });
    console.log(payLoad);
    $.post( 'ChangeComponents-data.php', {EvCode: tEvCode, TeamId: tTeamId, TeamSubId: tTeamSubId, data: payLoad }, function(data) {
        editComponents(tEvCode, tTeamId, tTeamSubId);
        populateData();
    }, "json");

}

function VerifyCheckbox() {
    var tmpGroup = {};
    var toSave=false;
    $('input:checkbox').each(function () {
        grp = $(this).attr('grp');
        if(tmpGroup[grp] === undefined) {
            tmpGroup[grp] = 0;
        }
        if($(this).is(":checked")) {
            tmpGroup[grp]++;
            if($(this).attr('isF')==='false') {
                toSave = true;
            }
        }
    });
    $.each(tmpGroup, function (index, item) {
        if(componentList[index].Qty >= componentList[index].Athletes.length) {
            $('input:checkbox[grp="' + index + '"]').attr('disabled', 'disabled');
        } else {
            if (componentList[index].Qty <= item) {
                $('input:checkbox[grp="' + index + '"]').each(function () {
                    if ($(this).is(':not(:checked)')) {
                        $(this).attr('disabled', 'disabled');
                        $('#td_'+$(this).attr('value')).addClass('textDisabled');
                    }
                });
            } else {
                $('input:checkbox[grp="' + index + '"]').removeAttr('disabled');
                $('td').removeClass('textDisabled');
            }
            if (componentList[index].Qty != item) {
                toSave = false;
            }
        }
    });
    if(toSave) {
        $('#cmdSave').show();
        $('#cmdSave').parent().addClass('toSave');
    } else {
        $('#cmdSave').hide();
        $('#cmdSave').parent().removeClass('toSave');
    }
}

function populateData() {
    var cmbEvent = $('#cmbEvent').val();
    var cmbTeam = $('#cmbTeam').val();
    $.getJSON('ChangeComponents-data.php?EvCode='+cmbEvent+'&CoId='+cmbTeam, function(data) {
        if(data.error === 0) {

            $('#cmbEvent').empty();
            $('#cmbEvent').append($('<option>').val(0).text('---').attr('id','cmbEv'));
            $.each(data.eventList, function (index, item) {
                $('#cmbEvent').append($('<option>').val(item.EvCode).text(item.EvCode + ' - ' + item.EvName).attr('id','cmbEv_'+item.EvCode));
            });
            $('#cmbEvent').val(cmbEvent);

            $('#cmbTeam').empty();
            $('#cmbTeam').append($('<option>').val(0).text('---').attr('id','cmbTeam'));
            $.each(data.teamList, function (index, item) {
                $('#cmbTeam').append($('<option>').val(item.Id).text(item.Code + ' - ' + item.Name).attr('id','cmbTeam_'+item.Id));
            });
            $('#cmbTeam').val(cmbTeam);

            $('#lstBody').empty();
            $.each(data.teamComposition, function (index, item) {
                var Html = '<tr class="TeamLine" grTeam="'+item.EvCode+'_'+item.Id+'_'+item.SubId+'">'+
                    '<td rowspan="'+(item.Components.length)+'" class="evCodeContainer">'+item.EvCode+'</td>'+
                    '<td rowspan="'+(item.Components.length)+'" class="nameContainer">'+item.EvName+'</td>'+
                    '<td rowspan="'+(item.Components.length)+'" class="codeContainer">'+item.Code+'</td>'+
                    '<td rowspan="'+(item.Components.length)+'" class="nameContainer">'+item.Name+'</td>'+
                    '<td class="nameContainer">'+item.Components[0].Athlete+'</td>'+
                    '<td class="divClassContainer">'+(item.Components[0].isQ ? '&#9654':'&#9655')+'&nbsp;'+item.Components[0].Div+' - '+item.Components[0].Cl+'</td>'+
                    '<td class="tsContainer">'+item.Components[0].Ts+'</td>'+
                    '<td rowspan="'+(item.Components.length)+'" class="Center"><input id="cmdEdit_'+item.EvCode+'_'+item.Id+'_'+item.SubId+'" type="button" value="'+cmdEdit+'" onclick="editComponents(\''+item.EvCode+'\','+item.Id+','+item.SubId+')"></td>'+
                    '</tr>';
                for(var i=1; i<item.Components.length; i++) {
                    Html += '<tr class="TeamLine" grTeam="'+item.EvCode+'_'+item.Id+'_'+item.SubId+'">'+
                            '<td class="nameContainer">'+item.Components[i].Athlete+'</td>'+
                            '<td class="divClassContainer">'+(item.Components[i].isQ ? '&#9654':'&#9655')+'&nbsp;'+item.Components[i].Div+' - '+item.Components[i].Cl+'</td>'+
                            '<td class="tsContainer">'+item.Components[i].Ts+'</td>'+
                        '</tr>';
                }
                Html += '<tr class="divider"><td colspan="8"></td></tr>';
                $('#lstBody').append(Html);
            });
            $('tbody tr.TeamLine').hover(function () {
                $('[grTeam="'+$(this).attr('grTeam')+'"]').addClass('hover');
            }, function() {
                $('tr[grTeam="'+$(this).attr('grTeam')+'"]').removeClass('hover');
            });
        }
    })
}