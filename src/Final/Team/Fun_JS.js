/* Folder Final/Team*/

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
	---------------------------------- Funzioni associate a InsertPoint_Bra.php ----------------------------------
*/

/*
	- BlockPhase(Phase,EnValue,DisValue)
	Blocca/Sblocca una fase
	Phase è la fase da gestire (0 agisce sia sull'oro che sul bronzo)
	EnValue è il valore Del bottone quando la fase è attiva,DisValue è quello
	di fase bloccata.EnValue si scambia con DisValue e vice-versa.

	NumTiebreak è il numero di frecce di tiebreak

	Phase == 0 allora il matchno va da 0 a 3
	negli altri casi va da 2*Phase a 4*Phase-1
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
