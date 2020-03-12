function CheckIfOris(chkValue,FormName,Individual) {
if(Individual) {
        if(document.getElementById(chkValue).checked) {
            document.getElementById(FormName).action = 'Individual/OrisIndividual.php';
        } else {
            document.getElementById(FormName).action = 'Individual/PrnIndividual.php';
        }
    } else {
        if(document.getElementById(chkValue).checked) {
            document.getElementById(FormName).action = 'Team/OrisTeam.php';
        } else {
            document.getElementById(FormName).action = 'Team/PrnTeam.php';
        }
    }
}

function CheckIfLabel(chkValue,FormName,Individual) {
    if(Individual) {
        if(document.getElementById(chkValue).checked) {
            document.getElementById(FormName).action = 'Individual/PrnLabels.php';
        } else {
            document.getElementById(FormName).action = 'Individual/PrnIndividual.php';
        }
    } else {
        if(document.getElementById(chkValue).checked) {
            document.getElementById(FormName).action = 'Team/PrnLabels.php';
        } else {
            document.getElementById(FormName).action = 'Team/PrnTeam.php';
        }
    }
}

function updateEvents(obj, TeamEvent) {
    $.getJSON('PrintOut-getEvents.php?showChildren='+(obj.checked ? 1 : 0)+'&team='+TeamEvent, function(data) {
        if(data.error==0) {
            var options='';
            $(data.options).each(function() {
                options+='<option value="'+this.v+'">'+this.t+'</option>';
            });

            $(TeamEvent==0 ? '#IndividualEvents' : '#TeamEvents').html(options);
        }
    });
}