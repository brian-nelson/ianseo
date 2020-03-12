function toggleDiv(div) {
    var toggle;
    $('.DivSelect'+div).each(function(idx) {
        if(idx==0) {
            toggle=!this.checked;
        }
        this.checked=toggle;
    });
    toggleCategory(this);
}

function toggleClass(cl) {
    var toggle;
    $('.ClSelect'+cl).each(function(idx) {
        if(idx==0) {
            toggle=!this.checked;
        }
        this.checked=toggle;
    });
    toggleCategory(this);
}

function toggleCategory() {
    var queryString='CardType='+CardType+'&CardNumber='+CardNumber;
    $('.CategorySelects:checked').each(function() {
        queryString+='&match[]='+encodeURIComponent(this.value);
    });

    $.getJSON('IdCardEdit-toggleCat.php?'+queryString, function (data) {
        if(data.error!=0) {
            // reset all selectors
            $('.CategorySelects').each(function() {
                this.checked=($(this).attr('checked') ? true : false);
            });
        }
    });
}

function UpdateCardSettings() {
    var queryString='CardType='+CardType+'&CardNumber='+CardNumber;
    $('[id^="IdCardsSettings"]').each(function() {
        queryString+='&'+this.id+'='+encodeURIComponent(this.value);
    });

    $.getJSON('IdCardEdit-update.php?'+queryString, function (data) {
        if(data.error==0) {
            // update picture
            $('#IdCardImage').attr('src', 'ImgIdCard.php?CardType='+CardType+'&CardNumber='+CardNumber+'&date='+ new Date().getTime());
        }
    });
}

function UpdateRowContent(obj) {
    var queryString='CardType='+CardType+'&CardNumber='+CardNumber;
    var iceType=$(obj).closest('tr').attr('icetype');
    var iceOrder=$(obj).closest('tr').attr('iceorder');

    $('[id^="Content\['+iceOrder+'\]"]').each(function() {
        var val='';
        switch(this.type) {
            case 'checkbox':
                val=(this.checked ? '1' : '0');
                break;
            default:
                val=this.value;
        }
        queryString+='&'+this.id+'='+encodeURIComponent(val);
    });

    $.getJSON('IdCardEdit-update.php?'+queryString, function (data) {
        if(data.reload) {
            document.location.reload();
            return;
        }
        if(data.error==0) {
            // update picture
            $('#IdCardImage').attr('src', 'ImgIdCard.php?CardType='+CardType+'&CardNumber='+CardNumber+'&date='+ new Date().getTime());
        }
    });
}

function pickerPopup302Deferred(field, div) {
    return new Promise(pickerPopup302(field, div));
}

function PickColorPalette(obj) {
    $(pickerPopup302( $(obj).attr('field'), $(obj).attr('div') )).promise().done( UpdateRowContent(obj) );
}