/*
													- Fun_AJAX_SetEventRules.js -
	Contiene le funzioni ajax usate da SetEventRules.php
*/

var Cache = new Array();	// cache per l'update

/*
	Adds a Division+Class+Subclass to an Event
*/
function AddEventRule(Event) {
    var OptDiv=$('#New_EcDivision').val();
    var OptCl=$('#New_EcClass').val();
    var OptSubCl=$('#New_EcSubClass').val();

    if (OptDiv.length>0 && OptCl.length>0 && ($('#New_EcSubClass:disabled').length>0 || OptSubCl.length>0)) {
        var QueryString = 'EvCode=' + Event;
        $(OptDiv).each(function() {
            QueryString += '&New_EcDivision[]=' + this;
        });

        $(OptCl).each(function() {
            QueryString += '&New_EcClass[]=' + this;
        });

        if($('#New_EcSubClass:disabled').length>0) {
            QueryString += '&New_EcSubClass[]=';
        } else {
            $(OptSubCl).each(function() {
                QueryString += '&New_EcSubClass[]=' + this;
            });
        }

        $.getJSON("AddEventRule.php?" + QueryString, function(data) {
            if (data.error==0) {
                $(data.rules).each(function() {
                    $('#tbody').prepend('<tr id="Row_' + Event + '_' + this[0] + this[1] + this[2] + '">' +
                        '<td class="Center">'+this[0]+'</td>' +
                        '<td class="Center">'+this[1]+'</td>' +
                        '<td class="Center">'+this[2]+'</td>' +
                        '<td class="Center"><img src="../../Common/Images/drop.png" border="0" alt="Delete" title="Delete" onclick="DeleteEventRule(\'' + Event + '\',\'' + this[0] + '\',\'' + this[1] + '\',\'' + this[2] + '\')"></td>' +
                        '</tr>');
                });

                $('#New_EcDivision').val([]);
                $('#New_EcClass').val([]);
                $('#New_EcSubClass').val([]);
            }
        });
    }
}

/*
	Deletes a Div+Class+Subclass combination from an event
*/
function DeleteEventRule(Event, DelDiv, DelClass, DelSubClass) {
    var QueryString
        = 'EvCode=' + Event + '&'
        + 'DelDiv=' + DelDiv + '&'
        + 'DelCl=' + DelClass + '&'
        + 'DelSubCl=' + DelSubClass;
    $.getJSON("DeleteEventRule.php?" + QueryString, function(data) {

        if (data.error==0) {
            $('#Row_' + Event + '_' + DelDiv + DelClass + DelSubClass).remove();
        } else {
            alert(data.msg);
        }
    });
}

function enableSubclass(obj) {
    document.getElementById('New_EcSubClass').disabled = !obj.checked;
}

function showAdvanced() {
    document.getElementById('Advanced').style.display='table-row-group';
    document.getElementById('AdvancedButton').style.display='none';
}

function UpdateData(obj) {
    $.getJSON('../UpdateRuleParam.php?'+obj.id+'&val='+$(obj).val(), function(data) {
        if (data.error!=0) {
            alert(data.msg);
        }
    });
}
