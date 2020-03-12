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
	- BlockPhase(Phase,EnValue,DisValue)
	Blocca/Sblocca una fase
	Phase è la fase da gestire (0 agisce sia sull'oro che sul bronzo)
	EnValue è il valore Del bottone quando la fase è attiva,DisValue è quello
	di fase bloccata.EnValue si scambia con DisValue e vice-versa.

	NumTiebreak è il numero di frecce di tiebreak

	Phase == 0 allora il matchno va da 0 a 3
	negli altri casi va da 2*Phase a 4*Phase-1
*/
function BlockPhase(Phase,EnValue,DisValue,NumTiebreak)
{
	var Bottone = document.getElementById('CmdBlockPhase_' + Phase);

	if (Bottone)
	{
		var Disabilita;
		if (Bottone.value==EnValue) {
			Disabilita = false;
			Bottone.value=DisValue;
		} else if (Bottone.value==DisValue) {
			Disabilita = true;
			Bottone.value=EnValue;
		}

		var ee = document.getElementById('d_Event').value;	// evento

		if(Phase==24) Phase=32;
		if(Phase==48) Phase=64;

		var mm1 = (Phase==0 ? 0 : 2*Phase);		// matchno di partenza
		var mm2 = (Phase==0 ? 3 : 4*Phase-1);	// matchno di arrivo


		for (i=mm1;i<=mm2;++i)
		{
			var Score = document.getElementById('d_S_' + ee + '_' + i);

			if (Score) {
				Score.disabled=Disabilita;

				if (Disabilita)
					SetStyle(Score.id,'disabled');
				else
					SetStyle(Score.id,'');
			}

			var Tie = document.getElementById('d_T_' + ee + '_' + i);

			if (Tie) {
				Tie.disabled=Disabilita;

				if (Disabilita) {
                    SetStyle(Tie.id, 'disabled');
                } else {
                    SetStyle(Tie.id, '');
                }
			}

			for (j=0;j<(NumTiebreak*3);++j) {
				Tiebreak = document.getElementById('d_t_' + ee + '_' + i + '_' + j);

				if (Tiebreak) {
					Tiebreak.disabled=Disabilita;

					if (Disabilita) {
                        SetStyle(Tiebreak.id, 'disabled');
                    } else {
                        SetStyle(Tiebreak.id, '');
                    }
				}
			}
		}


	}
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


/*
	---------------------------------- Funzioni associate a AbsIndividual1.php ----------------------------------
*/

/*
	- SelectAction()
	Determina su che pagina ciclare la form in base al valore selezionato
*/
function SelectAction() {
	var x=document.getElementById('EventCode').value;
	if (x.match(/^.+#{1}2{1}$/))	// eliminatorie
		document.Frm.action='../../Elimination/AbsIndividual2.php';
	else	// finali normali
		document.Frm.action='AbsIndividual2.php';

	document.Frm.submit();
}