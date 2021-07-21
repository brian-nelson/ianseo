$(document).ready(function() {
    $('tbody tr.EventLine').hover(function () {
        $('tr[grEvent="' + $(this).attr('grEvent') + '"]').addClass('hover');
    }, function () {
        $('tr[grEvent="' + $(this).attr('grEvent') + '"]').removeClass('hover');
    })
});

function gotoShootOff(evCode, onlyPending) {
    var toBeSolved = $('#ev_'+evCode).attr('toBeSolved');
    if(!onlyPending || toBeSolved==1) {
        if(toBeSolved!=1) {
            $.confirm({
                content:MsgInitFinalGridsError,
                boxWidth: '50%',
                useBootstrap: false,
                title:evCode,
                buttons: {
                    cancel: {
                        text: CmdCancel,
                        btnClass: 'btn-blue', // class for the button
                    },
                    unset: {
                        text: CmdConfirm,
                        btnClass: 'btn-red', // class for the button
                        action: function () {
                            document.location.href = ROOT_DIR + 'Final/Individual/AbsIndividual.php?EventCodes[]='+ evCode;
                        }
                    }
                },
                escapeKey: true,
                backgroundDismiss: true,
            });
        } else {
            document.location.href = ROOT_DIR + 'Final/Individual/AbsIndividual.php?EventCodes[]='+ evCode;
        }
    }
}

function gotoShootOffElim(evCode, onlyPending, elimPhase) {
    var isSolved = $('#ev_'+evCode).attr('So'+elimPhase);
    if(!onlyPending || isSolved!=1) {
        if(isSolved==1) {
            $.confirm({
                content:MsgInitFinalGridsError,
                boxWidth: '50%',
                useBootstrap: false,
                title:evCode,
                buttons: {
                    cancel: {
                        text: CmdCancel,
                        btnClass: 'btn-blue', // class for the button
                    },
                    unset: {
                        text: CmdConfirm,
                        btnClass: 'btn-red', // class for the button
                        action: function () {
                            document.location.href = ROOT_DIR + 'Elimination/AbsIndividual.php?EventCode='+ evCode+'&Elim='+elimPhase;
                        }
                    }
                },
                escapeKey: true,
                backgroundDismiss: true,
            });
        } else {
            document.location.href = ROOT_DIR + 'Elimination/AbsIndividual.php?EventCode='+ evCode+'&Elim='+elimPhase;
        }
    }
}

function cancelShootOff() {
    document.location.href = ROOT_DIR + 'Final/Individual/AbsIndividual.php';
}

function goToAdvancedMode() {
    var isAdvanced = $('#Advanced').val();
    var events = ''
    $('input[name="EventCodes[]"]').each(function (i,item) {
        events += '&EventCodes[]='+$(item).val();
    });
    if(isAdvanced == "1") {
        document.location.href = ROOT_DIR + 'Final/Individual/AbsIndividual.php?Advanced=0' + events;
    } else {
        $.confirm({
            content: MsgForExpert,
            boxWidth: '50%',
            useBootstrap: false,
            title: Advanced,
            buttons: {
                cancel: {
                    text: CmdCancel,
                    btnClass: 'btn-blue', // class for the button
                },
                unset: {
                    text: CmdConfirm,
                    btnClass: 'btn-red', // class for the button
                    action: function () {
                        document.location.href = ROOT_DIR + 'Final/Individual/AbsIndividual.php?Advanced=1' + events;
                    }
                }
            },
            escapeKey: true,
            backgroundDismiss: true,
        });
    }
}

function ResetDataToQR() {
    var events = '';
    var cntEvents = 0;
    $('input[name="EventCodes[]"]').each(function (i,item) {
        events += '&EventCodes[]='+$(item).val();
        cntEvents++;
    });
    $.confirm({
        content: MsgAttentionFinReset,
        boxWidth: '50%',
        useBootstrap: false,
        title: Advanced,
        buttons: {
            cancel: {
                text: CmdCancel,
                btnClass: 'btn-blue', // class for the button
            },
            unset: {
                text: CmdConfirm,
                btnClass: 'btn-red', // class for the button
                action: function () {
                    document.location.href = ROOT_DIR + 'Final/Individual/AbsIndividual.php?RESET=' + (cntEvents*42) + events;
                }
            }
        },
        escapeKey: true,
        backgroundDismiss: true,
    });

}