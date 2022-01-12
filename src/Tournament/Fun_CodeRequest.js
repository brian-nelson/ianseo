function RequestCode() {
	var Email=$('#Email').val();
	var Password=$('#Password').val();
	let Nation=$('#ToNation').val();

	if(Email=='' || Password=='' || Nation=='') {
		return;
	}

    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    if(!re.test(Email)) {
        alert('Email Not Valid!');
        return;
    }

    var form={
    	Code: $('#ToCode').html(),
    	Name: $('#ToName').html(),
	    ComCode: $('#ToCommitee').html(),
	    ComName: $('#ToComDescr').html(),
	    Where: $('#ToWhere').html(),
	    From: $('#ToWhenFrom').html(),
	    To: $('#ToWhenTo').html(),
	    Nation: Nation,
	    Password: Password,
	    Email: Email,
	    Google: $('#GoogleMap').val(),
	    JSON: true,
    }

    $.getJSON(IanseoRequestCodeURI, form, function(data) {
	    var Message=window[data.result];
	    if(data.result=='ErrYellowCard') Message=Message + "\n\n" + window['ErrNoError'];
	    alert(Message);
    });
}

function UpdateNation(obj) {

}
