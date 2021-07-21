function cancelShootOff() {
    document.location.href = ROOT_DIR + 'Final/Individual/AbsIndividual.php';
}

function goToAdvancedMode() {
    var isAdvanced = $('#Advanced').val();
    var evCode = $('#EventCode').val();
    var elimPhase = $('#Elim').val();
    if(parseInt(isAdvanced) == 1) {
        document.location.href = ROOT_DIR + 'Elimination/AbsIndividual.php?Advanced=0&EventCode='+ evCode+'&Elim='+elimPhase;
    } else {
        $.confirm({
            content: MsgForExpert,
            boxWidth: '50%',
            useBootstrap: false,
            title: Advanced,
            buttons: {
                cancel: {
                    text: CmdCancel,
                    btnClass: 'btn-blue' // class for the button
                },
                unset: {
                    text: CmdConfirm,
                    btnClass: 'btn-red', // class for the button
                    action: function () {
                        document.location.href = ROOT_DIR + 'Elimination/AbsIndividual.php?Advanced=1&EventCode='+ evCode+'&Elim='+elimPhase;
                    }
                }
            },
            escapeKey: true,
            backgroundDismiss: true
        });
    }
}

function ResetDataToQR() {
    var evCode = $('#EventCode').val();
    var elimPhase = $('#Elim').val();
    $.confirm({
        content: MsgForExpert,
        boxWidth: '50%',
        useBootstrap: false,
        title: Advanced,
        buttons: {
            cancel: {
                text: CmdCancel,
                btnClass: 'btn-blue' // class for the button
            },
            unset: {
                text: CmdConfirm,
                btnClass: 'btn-red', // class for the button
                action: function () {
                    document.location.href = ROOT_DIR + 'Elimination/AbsIndividual.php?RESET=' + ((parseInt(elimPhase)+1)*42) + '&EventCode='+ evCode+'&Elim='+elimPhase;
                }
            }
        },
        escapeKey: true,
        backgroundDismiss: true
    });

}