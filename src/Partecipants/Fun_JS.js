/* Folder Partecipants */ 

/*
	---------------------------------- Funzioni associate a index.php ----------------------------------
*/

/*
	- SetCompleteFlag()
	Decide se attivare o disattivare l'autocompletamento della riga durante la digitazione
	della matricola
*/
function SetCompleteFlag(Id)
{
	if (document.getElementById('d_e_EnFirstName_' + Id).value.length==0 &&
		document.getElementById('d_e_EnName_' + Id).value.length==0 &&
		document.getElementById('d_e_EnCtrlCode_' + Id).value.length==0 &&
		document.getElementById('d_c_CoCode_' + Id).value.length==0 &&
		document.getElementById('d_c_CoName_' + Id).value.length==0)
	{
		document.getElementById('CanComplete_' + Id).value=1;
	}
	else
	{
		document.getElementById('CanComplete_' + Id).value=0;
	}
	
}

/*
	---------------------------------- Funzioni associate a Partecipants.php ----------------------------------
*/
function SetOnTextBox()
{
	document.getElementById('EditRow').ondblclick=DblClickOnTextBox;
}