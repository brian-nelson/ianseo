var dwData;
var keyPressedActive=false;
var KeyListener=null;
var Preparation=true;

$(document).ready(function() {
    getMatchesData();
});

function getMatchesData() {
    $.getJSON(WebDir+'Final/Spotting-getEvents.php', function (data) {
        if (data.error == 0) {
            dwData = data.data;
            updateComboEvents();
        }
    });
}

function updateComboEvents() {
    $('#Spotting').hide();
    var spType = $('#spotType').val();
    $('#spotCode').empty();
    $('#spotCode').append('<option value="">---</option>');
    $('#spotPhase').empty();
    $('#spotPhase').append('<option value="">---</option>');
    $('#spotMatch').empty();
    $('#spotMatch').append('<option value="">---</option>');
    $.each(dwData[spType], function (i, item) {
        $('#spotCode').append('<option value="'+i+'"'+(PreEvent==i ? ' selected="selected"' : '')+'>'+i+' - '+item.name+'</option>');
    });
    if(PreEvent!='') {
        updateComboPhases();
    }

	if (history.pushState) {
		var newurl = window.location.origin + window.location.pathname + '?Team='+(spType=='Team' ? 1 : 0);
		window.history.pushState({path:newurl},'',newurl);
	}
}

function updateComboPhases() {
    $('#Spotting').hide();
    var spType = $('#spotType').val();
    var spEvent = $('#spotCode').val();
    $('#spotPhase').empty();
    $('#spotPhase').append('<option value="">---</option>');
    $('#spotMatch').empty();
    $('#spotMatch').append('<option value="">---</option>');
    if(spEvent!='') {
        $.each(dwData[spType][spEvent]['phases'], function (i, item) {
            $('#spotPhase').append('<option value="'+item.id+'"'+(PrePhase==item.id ? ' selected="selected"' : '')+'>'+item.name+'</option>');
        });
        if(PrePhase>=0) {
            updateComboMatches();
        }
    }
}

function updateComboMatches(selectedMatch) {
    $('#Spotting').hide();
    var spType = ($('#spotType').val()=='Team' ? '1' : '0');
    var spEvent = $('#spotCode').val();
    var spPhase = $('#spotPhase').val();
    $('#spotMatch').empty();
    $.getJSON(WebDir+'Final/Spotting-MatchesList.php?CompCode='+CompCode+'&Type='+spType+'&Event='+spEvent+'&Phase='+spPhase, function (data) {
        if(data.data.length!=1) {
            $('#spotMatch').append('<option value="">---</option>');
        }
        $.each(data.data, function (i, item) {
            if(item.LeftOpponent.TeamCode != null && item.RightOpponent.TeamCode != null) {
                var text='';
                if(item.Prefix != '') {
                    text = item.Prefix + ' - ';
                } else {
                    text = item.LeftOpponent.Target + (item.LeftOpponent.Target == item.RightOpponent.Target ? '' : '-' +  item.RightOpponent.Target) + '&nbsp;&nbsp;|&nbsp;&nbsp;';
                }
                if(item.Type=='1') {
                    text+=item.LeftOpponent.TeamName + ' - ' + item.RightOpponent.TeamName;
                } else {
                    text+=item.LeftOpponent.FamilyName + ' ' + item.LeftOpponent.GivenName + ' (' + item.LeftOpponent.TeamCode + ') - ' +
                        item.RightOpponent.FamilyName + ' ' + item.RightOpponent.GivenName + ' (' + item.RightOpponent.TeamCode + ')';
                }
                $('#spotMatch').append('<option value="' + item.MatchId + '"'+((PreMatchno==item.MatchId || selectedMatch==item.MatchId) ? ' selected="selected"' : '')+'>' + text + '</option>');
            }
        });
        if(PreMatchno>=0 || selectedMatch>0) {
            buildScorecard();
        }
    });
}

function toggleTarget() {
    $('#Target').toggleClass('Hidden', $('#spotTarget:checked').length==0);
    buildScorecard();
}

function toggleAlternate() {
    if($('.Alternate:hidden').length>4) {
        $('.Alternate').show();
        var Ends=$('table.Scorecard').attr('ends');
        var Arrows=$('table.Scorecard').attr('arrows');
        var SO=$('table.Scorecard').attr('so');
        var tabindex=1;
        $('.ShootsFirst:checked').each(function() {
            // gets the rows set as shooting first and set them as alternates
            // r1=$()
        });
    } else {
        $('.Alternate').hide();
        $('[tabindexorg]').each(function() {
            this.prop('tabindex', this.attr('tabindexorg'));
        });
    }
}

// some global variables needed to spot
var SvgCursor;

function buildScorecard() {
    $('#Spotting').hide();
    $('.ActiveArrow').toggleClass('ActiveArrow', false);
    $('#Target').toggleClass('TargetL', false).toggleClass('TargetR', false);

	if($('#ActivateKeys').is(':checked')==false && $('#spotTarget').is(':checked')) {
		$('#ActivateKeys').prop('checked', 'checked').click();
	}

    var spTeam = ($('#spotType').val()=='Team' ? '1' : '0');
    var spEvent = $('#spotCode').val();
    var spMatch = $('#spotMatch').val();
    var spTarget = $('#spotTarget:checked').length>0;
	Preparation=true;
    $.getJSON(WebDir+'Final/Spotting-getScorecards.php?Team='+spTeam+'&Event='+spEvent+'&MatchId='+spMatch+(spTarget ? '&ArrowPosition=1' : ''), function (data) {
        if(data.error!=0) {
            return;
        }

        $('#OpponentNameL').html(data.nameL);
        $('#OpponentNameR').html(data.nameR);
        $('#ScorecardL').html(data.scoreL);
        $('#ScorecardR').html(data.scoreR);
        $('#IrmSelectL').val(data.irmL);
        $('#IrmSelectL').attr('initial', data.irmL);
        $('#IrmSelectL').attr('ref', data.matchnoL);
        $('#IrmSelectR').val(data.irmR);
        $('#IrmSelectR').attr('initial', data.irmR);
        $('#IrmSelectR').attr('ref', data.matchnoR);

        $('#buttonMove2Next').html(data.move2next);

        $('#MatchAlternate').prop('checked', data.isAlternate);
        if(data.isAlternate) {
            $('.Alternate').show();
        } else {
            $('.Alternate').hide();
        }

        if(data.isLive) {
            $('#liveButton').val(TurnLiveOff).toggleClass('Live', true);
        } else {
            $('#liveButton').val(TurnLiveOn).toggleClass('Live', false);
        }

        $('#OpponentNameL').toggleClass('Winner', data.winner=='L');
        $('#OpponentNameR').toggleClass('Winner', data.winner=='R');
        $('#ScorecardL').toggleClass('Winner', data.winner=='L');
        $('#ScorecardR').toggleClass('Winner', data.winner=='R');
        if(data.confirmed) {
            $('#OpponentNameL').toggleClass('Confirmed', data.winner=='L');
            $('#OpponentNameR').toggleClass('Confirmed', data.winner=='R');
            $('#ScorecardL').toggleClass('Confirmed', data.winner=='L');
            $('#ScorecardR').toggleClass('Confirmed', data.winner=='R');
            $('#confirmMatch').attr('disabled', true);
        }

        if(spTarget) {
            var TgtOrgSize=data.targetSize;
            var TgtSize=Math.min($('#Content').width()/3, $('#Content').height() - $('#MatchSelector').outerHeight() - 75);
            var zoom=data.targetZoom;
            $('#Target').html(data.target).width(TgtSize).height(TgtSize);
            SvgCursor=$('#Target #SvgCursor circle');
            $('.SVGTarget')
                .width(TgtSize)
                .height(TgtSize)
                .attr('OrgSize', TgtOrgSize)
                .mousemove(function(e) {
                    var activeArrow=$('.ActiveArrow input');
                    if(activeArrow.length==1) {
                        var ratio = TgtOrgSize/ TgtSize;
                        var w = parseInt(TgtOrgSize / zoom);
                        var x = parseInt(e.offsetX * ratio);
                        var y = parseInt(e.offsetY * ratio);
                        $(this).attr('viewBox', (x - x / zoom) + ' ' + (y - y / zoom) + ' ' + (w) + ' ' + (w));
                        SvgCursor.attr('cx', x).attr('cy', y).show();
                    }
                })
                .click(function(e) {
                    var activeArrow=$('.ActiveArrow input');
                    if(activeArrow.length==1) {
                        var realsize = parseInt($(this).attr('realsize'));
                        var TgtOrgSize=$(this).attr('OrgSize');
                        var ratio = TgtOrgSize / TgtSize;
                        var convert=realsize/(TgtOrgSize-80);
                        var w = parseInt(TgtOrgSize / zoom);
                        var x = (parseInt(e.offsetX) * ratio - TgtOrgSize/2)*convert;
                        var y = (parseInt(e.offsetY) * ratio - TgtOrgSize/2)*convert;
                        var position={'x':x, 'y':y };
                        if(e.which==3) {
                            position.noValue=1;
                        }
                        updateArrow(activeArrow[0], position);
                        SvgCursor.hide();
                    }
                })
                .contextmenu(function(e) {
                    var activeArrow=$('.ActiveArrow input');
                    if(activeArrow.length==1) {
                        var realsize = parseInt($(this).attr('realsize'));
                        var TgtOrgSize=$(this).attr('OrgSize');
                        var ratio = TgtOrgSize / TgtSize;
                        var convert=realsize/(TgtOrgSize-80);
                        var w = parseInt(TgtOrgSize / zoom);
                        var x = (parseInt(e.offsetX) * ratio - TgtOrgSize/2)*convert;
                        var y = (parseInt(e.offsetY) * ratio - TgtOrgSize/2)*convert;
                        var position={'x':x, 'y':y, 'noValue':1};
                        updateArrow(activeArrow[0], position);
                        SvgCursor.hide();
                        return false;
                    }
                })
                .mouseleave(function(e) {
                    $(this).attr('viewBox', '0 0 '+(TgtOrgSize)+' '+(TgtOrgSize));
                    SvgCursor.hide();
                });
        }

        $('input[id^="Arrow["]').keydown(function(e) {
            // if the key is a star on its own
            if(e.key=='*' && !e.shiftKey && !e.metaKey && !e.ctrlKey && !e.altKey) {
                // check if an active arrow cell has focus
                var val = this.value;
                if (val.substr(-1) == '*') {
                    this.value = val.substr(0, val.length - 1);
                } else {
                    this.value = val + '*';
                }
                this.select();
                e.preventDefault();
            }
        });

        $('#Spotting').show();
        if(spTarget) {
            var TgtSize=Math.min($('#Content').width() - $('#ScorecardL').outerWidth() - $('#ScorecardR').outerWidth(), $('#Content').height() - $('#MatchSelector').outerHeight() - 75);
            $('#Target').width(TgtSize).height(TgtSize);
            $('.SVGTarget')
                .width(TgtSize)
                .height(TgtSize);
        }
        minTabEmpty=999;
        $('[id^="Arrow"]').each(function() {
            if(this.value=='' && $(this).prop('tabIndex') < minTabEmpty) {
                minTabEmpty = $(this).prop('tabIndex');
                $('[id="'+this.id+'"]').focus();
                selectArrow($('[id="'+this.id+'"]')[0]);
            }
        });

        if(keyPressedActive) {
        	// adapt the sscorecard
	        // makes all inputs inactive
	        $('.arrowcell').on('click', function() {
		        selectArrow($(this).find('input')[0]);
	        });
	        $('#Spotting input[type="text"]').prop('disabled', true);
        }

        // simulate call on the first arrow of each match to check the stars if any on loading the scorecard
	    updateArrow($('[id^="Arrow["]')[0], null);
	    Preparation=false;
    });
}

function updateArrow(obj, position) {
	console.log('funzione updateArrow ...');
    var spType = ($('#spotType').val()=='Team' ? '1' : '0');
    var spEvent = $('#spotCode').val();
    var spMatch = $('#spotMatch').val();
    var spTarget = $('#spotTarget:checked').length>0;
    var valChanged = (position || (obj && obj.value!=obj.defaultValue));

    var GetDataJSON={};
	GetDataJSON.Changed=(valChanged ? 1 : 0);
	GetDataJSON.Team=spType;
	GetDataJSON.Event=spEvent;
	GetDataJSON.MatchId=spMatch;
	GetDataJSON[obj.id]=obj.value;
	$('input[type="checkbox"].Closest:checked').each(function() {
		GetDataJSON.Closest=this.value;
	})
	if(spTarget) {
		GetDataJSON.ArrowPosition=1;
	}
	if(Preparation) {
		GetDataJSON.noUpdate=1;
	}
	if(position) {
		$.each(position, function(idx) {
			GetDataJSON[idx]=this;
		});
	}

    $.getJSON(WebDir+'Final/Spotting-setArrow.php', GetDataJSON, function (data) {
        if(data.error!=0) {
            return;
        }
	    obj.defaultValue=obj.value;
        $('#OpponentNameL').toggleClass('Winner', data.winner=='L');
        $('#OpponentNameR').toggleClass('Winner', data.winner=='R');
        $('#ScorecardL').toggleClass('Winner', data.winner=='L');
        $('#ScorecardR').toggleClass('Winner', data.winner=='R');
        $('.Confirmed').toggleClass('Confirmed', false);

        $('[id="'+data.arrowID+'"]').val(data.arrowValue);
        $.each(data.t, function() {
            $('[id="'+this.id+'"]').html(this.val);
        });

        var expand=$('.SVGTarget').attr('convert');
        var TgtCenter=$('.SVGTarget').attr('OrgSize')/2;
        if(typeof data.p.id != 'undefined') {
        	$('.SVGTarget [id="'+data.p.id.replace(/\[/g,'\\[').replace(/\]/g,'\\]')+'"]').attr('cx', data.p.data.X*expand + TgtCenter).attr('cy', data.p.data.Y*expand + TgtCenter);
        }

        if(position) {
        	let nextIndex=parseInt($(obj).attr('tabIndex'));
        	if($('#MoveNext').is(':checked')) {
		        nextIndex++;
	        }
            var NextTabIndex=$('[tabindex="'+nextIndex+'"]');
            if(NextTabIndex.length>0) {
                selectArrow(NextTabIndex[0], true);
            }
        }

        if(valChanged || data.changed==1) {
            $('[id="'+data.confirm+'"]').attr('disabled', false);
        }

        if(data.newSOPossible) {
            $('.newSoNeeded').show();
        } else {
            $('.newSoNeeded').hide();
        }

        $.each(data.stars, function() {
	        if(this.isStar) {
	            $('#'+this.id).attr('ref', this.ref).attr('next', this.nextValue).show();
	        } else {
	            $('#'+this.id).attr('ref','').attr('next', '').hide();
	        }
        });

        $('#ClosestL').prop('checked', data.ClosestL==1);
        $('#ClosestR').prop('checked', data.ClosestR==1);

        if(data.ShootOff) {
            $('.StarRaiserSO').show();
            $('.StarRaiserArrows').hide();
        } else {
            $('.StarRaiserSO').hide();
            $('.StarRaiserArrows').show();
        }
        if(data.starsL) {
	        $('[ref="ScorecardL"]').show();
	        $('[ref="ConfirmL"]').prop('disabled', true);
        } else {
	        $('[ref="ScorecardL"]').hide();
	        $('[ref="ConfirmL"]').prop('disabled', false);
        }
        if(data.starsR) {
	        $('[ref="ScorecardR"]').show();
	        $('[ref="ConfirmR"]').prop('disabled', true);
        } else {
	        $('[ref="ScorecardR"]').hide();
	        $('[ref="ConfirmR"]').prop('disabled', false);
        }

        if(data.showClosest) {
	        $('.ClosestSpan').show();
        } else {
	        $('.ClosestSpan').hide();
        }

        if(data.DontMove) {
        	obj.focus();
        }
    });
}

function setShootingFirst(obj, tabindex) {
    $.getJSON(WebDir+"Final/Spotting-setShootingFirst.php?" + obj.id + "="  +  (obj.checked ? 'y' : 'n'), function(data) {
        if (data.error==0) {
            // sets the tabindex values of the next end!
            var i='';
            $(data.t).each(function() {
                if(i=='' || this.val==tabindex) {
                    i=this.id;
                }
                $('[id="'+this.id+'"]').prop('tabIndex', this.val);
            });
            if(i!='') {
            	var newArrow=$('[id="'+i+'"]');
                newArrow.focus();
                selectArrow(newArrow[0]);
            }
        }
    });
}

function selectArrow(obj, noselect) {
    $('.ActiveArrow').toggleClass('ActiveArrow', false);
    $(obj).parent().toggleClass('ActiveArrow', true);
    $('#Target').toggleClass('TargetL', false).toggleClass('TargetR', false);
    $('[id^="SvgEndL_"]').hide();
    $('[id^="SvgEndR_"]').hide();
    $('[id^="SvgEndL_SO_"]').hide();
    $('[id^="SvgEndR_SO_"]').hide();
    var so=$(obj).closest('tr').attr('so')=='1';
    var end=$(obj).closest('tr').attr('end');
    if($(obj).closest('td.Opponents').attr('id')=='ScorecardL') {
        $('#Target').toggleClass('TargetL', true);
        if(so) {
            $('#SvgEndL_SO_'+end).show();
        } else {
            $('#SvgEndL_'+end).show();
        }
    } else {
        $('#Target').toggleClass('TargetR', true);
        if(so) {
            $('#SvgEndR_SO_'+end).show();
        } else {
            $('#SvgEndR_'+end).show();
        }
    }
    if(obj.value!='' && document.getElementById('svgLastArrow')) {
    	let sighter=document.getElementById('svgLastArrow').getBBox();
    	let arrow=document.getElementById('Svg'+obj.id).getBBox();

    	$('#svgLastArrow').attr('opacity',1);
    	$('#svgLastArrow').attr('transform','translate('+(arrow.x-sighter.x-((sighter.width-arrow.width)/2))+', '+(arrow.y-sighter.y-((sighter.height-arrow.height)/2))+')');
    } else {
    	$('#svgLastArrow').attr('opacity',0);
    }
    if(noselect==true) {
        return;
    }
    obj.select();
}

function setLive() {
    var spType = ($('#spotType').val()=='Team' ? '1' : '0');
    var spEvent = $('#spotCode').val();
    var spMatch = $('#spotMatch').val();


    $.getJSON(WebDir+"Final/Spotting-UpdateLive.php?d_Event=" + spEvent + "&d_Match="  +  spMatch + "&d_Team=" + spType, function(data) {
        if(data.error==0) {
            if(data.isLive) {
                $('#liveButton').val(TurnLiveOff).toggleClass('Live', true);
            } else {
                $('#liveButton').val(TurnLiveOn).toggleClass('Live', false);
            }
        } else {
            alert(data.msg);
        }
    });
}

function ConfirmEnd(obj) {
    var spType = ($('#spotType').val()=='Team' ? '1' : '0');
    var spEvent = $('#spotCode').val();
    var spMatch = $('#spotMatch').val();

    $.getJSON(WebDir+'Final/Spotting-SetConfirmation.php?team=' + spType + '&event=' + spEvent + '&' + obj.id + "=y", function(data) {
        if(data.error==0) {
            if(data.starter!='') {
                // sets the shooting first selector
                var shootingFirst=$('[id="'+data.starter+'"]');
                if(shootingFirst.length>0) {
                    shootingFirst.attr('checked', true);
                    setShootingFirst(shootingFirst[0], data.tabindex);
                }
            }

            // sets the the confirmation!
            obj.disabled=true;

            $('#OpponentNameL').toggleClass('Winner', data.winner=='L');
            $('#OpponentNameR').toggleClass('Winner', data.winner=='R');
            $('#ScorecardL').toggleClass('Winner', data.winner=='L');
            $('#ScorecardR').toggleClass('Winner', data.winner=='R');
            $('.Confirmed').toggleClass('Confirmed', false);


            $('#confirmMatch').attr('disabled', true);
            if(data.winner!='') {
                // match is over, asks confirmation
                $('#confirmMatch').attr('disabled', false);
            }
        }
    });
}

function confirmMatch(obj) {
    var spType = ($('#spotType').val()=='Team' ? '1' : '0');
    var spEvent = $('#spotCode').val();
    var spMatch = $('#spotMatch').val();

    $.getJSON(WebDir+'Final/Spotting-setConfirmationMatch.php?match='+ spMatch + '&team=' + spType + '&event=' + spEvent, function(data) {
        if (data.error==0) {
            // sets the winner
            $('#OpponentNameL').toggleClass('Confirmed', data.winner=='L');
            $('#OpponentNameR').toggleClass('Confirmed', data.winner=='R');
            $('#ScorecardL').toggleClass('Confirmed', data.winner=='L');
            $('#ScorecardR').toggleClass('Confirmed', data.winner=='R');
            if(data.winner!='') {
                $('.ActiveArrow').toggleClass('ActiveArrow', false);
                $('#Target').toggleClass('TargetL', false).toggleClass('TargetR', false);
                obj.disabled=true;
            }
        }
    });
}

function addStar (id) {
    tmp = $('[id="'+id+'"]').val();
    if(tmp != '') {
        if(tmp.indexOf('*')==-1) {
            tmp += '*';
        } else {
            tmp = tmp.replace('*','');
        }
        $('[id="'+id+'"]').val(tmp);
        updateArrow($('[id="'+id+'"]').get(0));
    }
}

function addPoint (id) {
    tmp = $('[id="'+id+'"]').val();
    if(tmp != '') {
        var spType = ($('#spotType').val()=='Team' ? '1' : '0');
        var spEvent = $('#spotCode').val();

        $.getJSON(WebDir+'Final/Spotting-getNextValidValue.php?Team='+spType+'&Event='+spEvent+'&CurValue='+tmp, function (data) {
            if (data.error != 0) {
                return;
            } else {
                $('[id="' + id + '"]').val(data.nextValue);
                updateArrow($('[id="'+id+'"]').get(0));
            }
        });
    }
}

function moveToNextPhase(obj) {
    var spType = ($('#spotType').val()=='Team' ? '1' : '0');
    var spEvent = $('#spotCode').val();
    var spMatch = $('#spotMatch').val();

    $.getJSON(WebDir + 'Final/Spotting-nextPhase.php?event='+spEvent+'&team='+spType+'&matchno='+spMatch+(obj ? '&pool='+obj : ''), function(data) {
        if(data.error==0) {
            updateComboMatches(spMatch);
            alert(data.msg);
        }
    });
}

function toggleKeypressNew() {
	keyPressedActive=!keyPressedActive;
	$('#ActivateKeys')[0].checked=keyPressedActive;

	if(keyPressedActive) {
		// makes all inputs inactive
		$('.arrowcell').on('click', function() {
			selectArrow($(this).find('input')[0]);
		});
		$('#Spotting input[type="text"]').prop('disabled', true);

		// creates the definitions

		$(document).on('keydown', function(e) {
			switch(e.key) {
				case '0':
				case 'm':
				case 'M':
					setValue('M');
					break;
				case '1':
				case '2':
				case '3':
				case '4':
				case '5':
				case '6':
				case '7':
				case '8':
				case '9':
					setValue(e.key);
					break;
				case 'Tab':
					if(e.shiftKey) {
						gotoPrevious();
					} else {
						gotoNext();
					}
					break;
			}
			console.log(e);
		});

	} else {
		// makes all inputs acive
		$('#Spotting input[type="text"]').prop('disabled', false);
		$('.arrowcell').off('click');

		$(document).off('keydown');
	}

}
// http://dmauro.github.io/Keypress/

function toggleKeypress() {
	keyPressedActive=!keyPressedActive;
	$('#ActivateKeys')[0].checked=keyPressedActive;
    if(keyPressedActive)
        $('#keypadLegenda').show();
    else {
        $('#keypadLegenda').hide();
    }

	if(keyPressedActive) {
		if(KeyListener) {
			KeyListener.reset();
		}
		KeyListener = new window.keypress.Listener();

		// makes all inputs inactive
		$('.arrowcell').on('click', function() {
				selectArrow($(this).find('input')[0]);
			});
		$('#Spotting input[type="text"]').prop('disabled', true);

		// creates the definitions

		KeyListener.simple_combo("right", function() {
			gotoNext();
		});

		KeyListener.simple_combo("backspace", function() {
			gotoNext();
		});

		KeyListener.simple_combo("num_divide", function() {
			gotoNext();
		});

		KeyListener.simple_combo("tab", function() {
			gotoNext();
		});

		KeyListener.simple_combo("shift tab", function() {
			gotoPrevious();
		});

		KeyListener.simple_combo("left", function() {
			gotoPrevious();
		});

		KeyListener.simple_combo(".", function() {
			setValue('');
		});

		KeyListener.simple_combo("delete", function() {
			setValue('');
		});

		KeyListener.simple_combo("esc", function() {
			setValue('');
		});

		KeyListener.simple_combo("num_decimal", function() {
			setValue('');
		});

		KeyListener.simple_combo("num_multiply", function() {
			toggleStar();
		});

		KeyListener.simple_combo("*", function(e) {
			toggleStar();
		});

		KeyListener.simple_combo("shift d", function(e) {
			toggleStar();
		});

        KeyListener.simple_combo("d", function(e) {
            toggleStar();
        });

		KeyListener.simple_combo("num_0", function() {
			setValue('M');
		});

		KeyListener.simple_combo("m", function() {
			setValue('M');
		});

		KeyListener.simple_combo("shift m", function() {
			setValue('M');
		});

		KeyListener.simple_combo("num_1", function() {
			setValue('1');
		});

		KeyListener.simple_combo("1", function() {
			setValue('1');
		});

		KeyListener.simple_combo("num_2", function() {
			setValue('2');
		});

		KeyListener.simple_combo("2", function() {
			setValue('2');
		});

		KeyListener.simple_combo("num_3", function() {
			setValue('3');
		});

		KeyListener.simple_combo("3", function() {
			setValue('3');
		});

		KeyListener.simple_combo("num_4", function() {
			setValue('4');
		});

		KeyListener.simple_combo("4", function() {
			setValue('4');
		});

		KeyListener.simple_combo("num_5", function() {
			setValue('5');
		});

		KeyListener.simple_combo("5", function() {
			setValue('5');
		});

		KeyListener.simple_combo("num_6", function() {
			setValue('6');
		});

		KeyListener.simple_combo("6", function() {
			setValue('6');
		});

		KeyListener.simple_combo("num_7", function() {
			setValue('7');
		});

		KeyListener.simple_combo("7", function() {
			setValue('7');
		});

		KeyListener.simple_combo("num_8", function() {
			setValue('8');
		});

		KeyListener.simple_combo("8", function() {
			setValue('8');
		});

		KeyListener.simple_combo("num_9", function() {
			setValue('9');
		});

		KeyListener.simple_combo("9", function() {
			setValue('9');
		});

		KeyListener.simple_combo("num_subtract", function() {
			setValue('10');
		});

		KeyListener.simple_combo("shift t", function() {
			setValue('10');
		});

        KeyListener.simple_combo("t", function() {
            setValue('10');
        });

		KeyListener.simple_combo("shift e", function() {
			setValue('11');
		});

        KeyListener.simple_combo("e", function() {
            setValue('11');
        });

		KeyListener.simple_combo("num_add", function() {
			setValue('X');
		});

        KeyListener.simple_combo("x", function() {
            setValue('X');
        });

		KeyListener.simple_combo("shift x", function() {
			setValue('X');
		});

		KeyListener.simple_combo("shift q", function() {
			// confirm left end
			var obj=$('[ref="ConfirmL"]');
			if(obj.length && !obj.prop('disabled')) {
				ConfirmEnd(obj[0]);
			}
		});

		KeyListener.simple_combo("shift e", function() {
			// confirm left end
			var obj=$('[ref="ConfirmR"]');
			if(obj.length && !obj.prop('disabled')) {
				ConfirmEnd(obj[0]);
			}
		});

		KeyListener.simple_combo("shift w", function() {
			// confirm left end
			var obj=$('#confirmMatch');
			if(obj.length && !obj.prop('disabled')) {
				confirmMatch(obj[0]);
			}
		});

	} else {
		KeyListener.reset();

		// KeyListener = new window.keypress.Listener();

		// makes all inputs acive
		$('#Spotting input[type="text"]').prop('disabled', false);
		$('.arrowcell').off('click');

		// focus on the active cell
		activeCell=$('.ActiveArrow input').focus();

		// KeyListener.simple_combo("tab", function() {
		// 	gotoNext();
		// });
		//
		// KeyListener.simple_combo("shift tab", function() {
		// 	gotoPrevious();
		// });

	}
}

function setValue(num) {
	var activeCell=$('.ActiveArrow input');
	activeCell[0].value=num;
	updateArrow(activeCell[0]);
}

function gotoNext() {
	var tabindex=parseInt($('.ActiveArrow input').attr('tabIndex'));
	// moves forward
	tabindex++;
	var newCell=$('[tabindex="'+tabindex+'"]');
	newCell.focus();
	if(newCell.length==1) {
		selectArrow(newCell[0], true);
	}
}

function gotoPrevious() {
	var tabindex=parseInt($('.ActiveArrow input').attr('tabIndex'));
	// moves backwards
	tabindex--;
	if(tabindex<101) {
		tabindex=101;
	}
	var newCell=$('[tabindex="'+tabindex+'"]');
	newCell.focus();
	if(newCell.length==1) {
		selectArrow(newCell[0], true);
	}
}

function toggleStar() {
	var activeCell=$('.ActiveArrow input');
	var newCell=$('.ActiveArrow input');
	var tabindex=parseInt($('.ActiveArrow input').attr('tabIndex'));

	if(newCell[0].value=='') {
		// toggle the star to the previous arrow!
		tabindex--;
		if(tabindex>100) {
			newCell=$('[tabindex="'+tabindex+'"]');
		}
		activeCell[0].vallue=='';
	}

	var newCellVal=newCell[0].value;
	if(newCellVal=='') {
		// the cell is empty, so nothing to do!
		return;
	}

	if(newCellVal.substr(-1)=='*') {
		// removes the star
		newCell[0].value=newCellVal.substr(0, newCellVal.length-1);
	} else {
		newCell[0].value=newCellVal + '*';
	}
	updateArrow(newCell[0]);
}

// $(document).keyup(function(e) {
// 	// check if an active arrow cell has focus
// 	if($('.ActiveArrow input:focus').length>0) {
// 		return;
// 	}
//
// 	// if the key is a star on its own
// 	if(e.key=='*' && !e.shiftKey && !e.metaKey && !e.ctrlKey && !e.altKey) {
// 		// Select the activearrow tabindex and star the previous one
// 		var tabindex=parseInt($('.ActiveArrow input').attr('tabIndex'));
// 		if(tabindex>1) {
// 			var previous=$('[tabindex="'+(tabindex-1)+'"]');
// 			if(previous.length==1) {
// 				var val= previous.val();
// 				if(val.substr(-1)=='*') {
// 					previous.val(val.substr(0,val.length-1));
// 				} else {
// 					previous.val(val+'*');
// 				}
// 				updateArrow(previous[0]);
// 			}
// 		}
// 	}
//
// 	// it is a tab (ASC=9)
// 	if(e.which==9 && !e.metaKey && !e.ctrlKey && !e.altKey) {
// 		var tabindex=parseInt($('.ActiveArrow input').attr('tabIndex'));
// 		// moves forward or backwards depending on the shift key
// 		if(e.shiftKey) {
// 			if(tabindex>1) {
// 				tabindex--;
// 			}
// 		} else {
// 			tabindex++;
// 		}
// 		var newCell=$('[tabindex="'+tabindex+'"]');
// 		if(newCell.length==1) {
// 			selectArrow(newCell[0], true);
// 		}
// 	}
// });

function updateIrm(obj) {
	if(!confirm(ConfirmIrmMsg)) {
		$(obj).val($(obj).attr('initial'));
		return;
	}
	var spType = ($('#spotType').val()=='Team' ? '1' : '0');
	var spEvent = $('#spotCode').val();

	$.getJSON(WebDir + 'Final/Spotting-updateIRM.php?event='+spEvent+'&team='+spType+'&matchno='+$('#'+obj.id).attr('ref')+'&value='+obj.value, function(data) {
		if(data.error==0) {
			$(obj).attr('initial', obj.value);
			alert(data.msg);
		}
	});
}

function raiseStar(obj) {
	$('[id="'+$(obj).attr('ref')+'"]').val($(obj).attr('next')).trigger('blur');
	$(obj).hide();
}

function removeStars(obj) {
	$(obj).closest('#'+$(obj).attr('ref')).find('[id^="Star-"]:visible').each(function() {
		var arrow=$('[id="'+$(this).attr('ref')+'"]');
		arrow.val(arrow.val().replace('*',''));
		arrow[0].onblur();
	})
	$(obj).closest('#'+$(obj).attr('ref')).find('[id^="StarSO-"]:visible').each(function() {
		var arrow=$('[id="'+$(this).attr('ref')+'"]');
		arrow.val(arrow.val().replace('*',''));
		arrow[0].onblur();
	})
}

function toggleClosest(obj) {
	var spType = ($('#spotType').val()=='Team' ? '1' : '0');
	var spEvent = $('#spotCode').val();
	var spMatch = $('#spotMatch').val();

	// execute the toggle
	$.getJSON(WebDir+'Final/Spotting-SetClosest.php?team=' + spType + '&event=' + spEvent + '&match=' + spMatch + '&closest='+(obj.checked ? obj.value : ''), function(data) {
		if(data.error==0) {
			$('#OpponentNameL').toggleClass('Winner', data.winner=='L');
			$('#OpponentNameR').toggleClass('Winner', data.winner=='R');
			$('#ScorecardL').toggleClass('Winner', data.winner=='L');
			$('#ScorecardR').toggleClass('Winner', data.winner=='R');
			$('.Confirmed').toggleClass('Confirmed', false);

			$(data.t).each(function() {
				$('[id="'+this.id+'"]').html(this.val);
			});

			if(data.newSOPossible) {
				$('.newSoNeeded').show();
			} else {
				$('.newSoNeeded').hide();
			}

			$('#ClosestL').prop('checked', data.ClosestL==1);
			$('#ClosestR').prop('checked', data.ClosestR==1);

			if(data.showClosest) {
				$('.ClosestSpan').show();
			} else {
				$('.ClosestSpan').hide();
			}
		}
	});
}
