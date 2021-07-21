/* Folder Final/Individual*/

/*
	---------------------------------- Funzioni associate a InsertPoint2.php ----------------------------------
*/

/*
	- ChangePhase(NewPhase)
	Cambia la fase con NewPhase
	Se Sch!=0 allora sto passando uno schedule
*/
function ChangePhase(NewPhase,Sch)
{

	if (Sch==0)
	{
		document.getElementById('d_Phase').value=NewPhase;
	}
	else
	{
		document.getElementById('x_Session').value=NewPhase;
	}
	document.Frm.submit();
}

/*
	---------------------------------- Funzioni associate a ListEvents.php ----------------------------------
*/

/*
	Mi serve per riportare al valore vecchio, la fase nel caso uno decida di annullare l'operazione
*/
var OldPhase = -1;

/*
	- SetOldPhase(Value)
	Setta OldPhase a Value
*/
function SetOldPhase(Value)
{
	OldPhase=Value;
}



/*
	- ManageElim(EvCode)
	Imposta i blocchi sulle caselle di testo delle eliminatorie in base al valore
	della tendina rispetto a EvCode
*/
function ManageElim(EvCode)
{
	if(document.getElementById('d_EvElim_'+EvCode).value == 0)
	{
		document.getElementById('d_EvElim1_'+EvCode).value = 0;
		document.getElementById('d_EvElim1_'+EvCode).readOnly = true;
		document.getElementById('d_EvElim2_'+EvCode).value = 0;
		document.getElementById('d_EvElim2_'+EvCode).readOnly = true;
		UpdateField('d_EvElim2_'+EvCode);
		UpdateField('d_EvElim1_'+EvCode);
	}
	else if(document.getElementById('d_EvElim_'+EvCode).value == 1)
	{
		document.getElementById('d_EvElim1_'+EvCode).value = 0;
		document.getElementById('d_EvElim1_'+EvCode).readOnly = true;
		document.getElementById('d_EvElim2_'+EvCode).readOnly = false;
		UpdateField('d_EvElim1_'+EvCode);
		UpdateField('d_EvElim2_'+EvCode);
	}
	else if(document.getElementById('d_EvElim_'+EvCode).value == 2)
	{
		document.getElementById('d_EvElim1_'+EvCode).readOnly = false;
		document.getElementById('d_EvElim2_'+EvCode).readOnly = false;
	}

	if (document.getElementById('old_EvElim_'+EvCode).value!='d_EvElim_'+EvCode)
		ResetElim(EvCode);
}

/*
	- ChangeNew_EvElim()
	In base al valore della tendina New_EvElim blocca/sblocca le textbox a lei associate
*/
function ChangeNew_EvElim()
{
	if(document.getElementById('New_EvElim') != null)
	{
		var x=document.getElementById('New_EvElim').value;
		switch (x)
		{
			case '0':
				document.getElementById('New_EvElim1').disabled=true;
				document.getElementById('New_EvElim2').disabled=true;
				break;
			case '1':
				document.getElementById('New_EvElim1').disabled=true;
				document.getElementById('New_EvElim2').disabled=false;
				break;
			case '2':
				document.getElementById('New_EvElim1').disabled=false;
				document.getElementById('New_EvElim2').disabled=false;
				break;
		}
	}
}

/*
	---------------------------------- Funzioni associate a InsertPoint_Bra.php ----------------------------------
*/

/*
	- BlockPhase(Phase)
	Lock/Unlock a phase (0 means both gold and bronze medal matches)
*/
function BlockPhase(Phase) {
	var Button = $('#CmdBlockPhase_' + Phase);

	var Disable=(Button.val()==CmdDisable);
	Button.val(Disable ? CmdEnable : CmdDisable);

	$('.ph-'+Phase).find('select,input').each(function() {
		this.disabled=Disable;
		$(this).toggleClass('disabled', Disable);
	});
}

/*
	---------------------------------- Funzioni associate a SpotMatch.php ----------------------------------
*/
function EseguiSubmit()
{
	document.getElementById('Command').value='OK';
	document.FrmVolee.submit();
	document.getElementById('Command').value='';
}

// Mette o toglie il dubbio nella textbox Txt
function GestisciDubbio(Txt)
{
	var Tmp=document.getElementById(Txt).value;

	// Tolgo il dubbio
	if (Tmp.indexOf("*")==Tmp.length-1)
	{
		Tmp=Tmp.substring(0,Tmp.length-1);
	}
	else	// Metto il dubbio
	{
		Tmp=Tmp+"*";
	}

	document.getElementById(Txt).value=Tmp;

	// Eseguo il submit
	EseguiSubmit();
}

