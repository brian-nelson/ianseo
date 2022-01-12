
function doUpdate(force) {
    let form={
        act:'getFile',
        force:force,
    };

    // create the status panel
    if($('#UpdateBox').length==0) {
        $('body').append('<div id="UpdateBox""><div id="DialogUpdate"><div id="DialogMessage"></div><div id="endDialog"></div></div></div>');
    }

    let a = $.getJSON('index-action.php', form, function(data) {
        if(data.error!=0) {
            $('#DialogMessage').html(data.msg)
            $('#endDialog').html('<div>' +
                '<div class="Button" onclick="doUpdate(1)">'+cmdForceUpdate+'</div>' +
                '<div class="Button" onclick="closeDialog()">Close</div>' +
                '</div>')
            return;
        }

        let form={
            act:'doUpdate',
            user:$('#Email').val(),
            pwd:$('#Password').val(),
        };

        let b = $.getJSON('index-action.php', form, function(data) {
            $('#endDialog').html(data.msg)
            $('#endDialog').append('<div>' +
                '<div class="Button" onclick="closeDialog()">'+cmdClose+'</div>' +
                '</div>')
        });

        showProcess();
    });
}

function showProcess() {
    let form={
        act:'getInfo',
    };
    let c= $.getJSON('index-action.php', form, function(data) {
        console.log(data);
        if(data.error==0) {
            $('#DialogMessage').html(data.status);
            $('#endDialog')[0].scrollIntoView();

            if(data.finished==0) {
                setTimeout(function() {
                    showProcess()
                }, 500);
                return;
            }
        } else {
            $('#UpdateBox').remove();
            $.alert({
                content:data.msg,
                useBootstrap: false,
                boxWidth:'40vw',
            });
        }
    });
}

function closeDialog() {
    $('#UpdateBox').remove();
    location.href='../'
}
