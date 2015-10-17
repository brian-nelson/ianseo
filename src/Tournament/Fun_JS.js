/* Folder Tournament */ 

/*	
	---------------------------------- Funzioni associate a ManSessions.php ----------------------------------
*/

/*
	- ChangeNumSession()
	Attiva o disattiva le caselle di testo dei numeri di paglione e dei numeri di arcieri 
	per le sessioni non utilizzate
*/
function ChangeNumSession()
{
	for (var i=1;i<=9;++i)
	{
	// abilito le caselle con indice <= al numero di sessioni
		if (i<=document.Frm.elements['d_ToNumSession'].value)
		{	
			document.Frm.elements['d_ToTar_' + i].className='number';
			document.Frm.elements['d_ToTar_' + i].readOnly=false;
			
			document.Frm.elements['d_ToAth_' + i].className='number';
			document.Frm.elements['d_ToAth_' + i].readOnly=false;
		}
		else	// disabilito le altre
		{
			document.Frm.elements['d_ToTar_' + i].value=0;
			document.Frm.elements['d_ToTar_' + i].className='number disabled';
			document.Frm.elements['d_ToTar_' + i].readOnly=true;
			
			document.Frm.elements['d_ToAth_' + i].value=0;
			document.Frm.elements['d_ToAth_' + i].className='number disabled';
			document.Frm.elements['d_ToAth_' + i].readOnly=true;
		}
	}
}

/*
	- FormCancel()
	Annulla il form
*/
function FormCancel()
{
	document.Frm.reset();
	ChangeNumSession();
}

/*	
	---------------------------------- Funzioni associate a ManStaffField.php ----------------------------------
*/

/*
	- DeleteId(Id,Message,Command)
	Cancella una persona da TournamentInvolved.
	Riceve l'id della persona, il messaggio di conferma e la stringa del comando
*/
function DeleteId(Id,Message)
{
	if (confirm(Message))
	{
		window.location.href='ManStaffField.php?Command=DELETE&IdDel=' + Id;
	}
}



/*
	- setAll(name,check)
	Seleziona o deseleziona tutte le checkbox con nome name.
	rifCheck � la check che richiama la funzione e che setta il checked uguale al suo 
*/
function setAllCheck(name,rifCheck)
{
	var rif=document.getElementById(rifCheck);
	var chks=document.getElementsByName(name);
	
	for (var i=0;i<chks.length;++i)
		if (chks[i].type=='checkbox')
			chks[i].checked=rif.checked;
}

function SelectBook(obj) {
//	document.getElementById('ENS').checked=obj.checked;
	document.getElementById('ENC').checked=obj.checked;
//	document.getElementById('ENA').checked=obj.checked;
	document.getElementById('STC').checked=obj.checked;
	document.getElementById('STE').checked=obj.checked;
	document.getElementById('MEDSTD').checked=obj.checked;
	document.getElementById('MEDLST').checked=obj.checked;
	
	var tmp;
	// Qualification Individual
	if(tmp=document.getElementById('allResultIndAbs')) {
		tmp.checked=obj.checked;
		setAllCheck('QualificationInd[]', 'allResultIndAbs');
	}
	// Qualification Teams
	if(tmp=document.getElementById('allResultTeamAbs')) {
		tmp.checked=obj.checked;
		setAllCheck('QualificationTeam[]', 'allResultTeamAbs');
	}
	// Eliminations
	if(tmp=document.getElementById('allResultElim')) {
		tmp.checked=obj.checked;
		setAllCheck('EliminationInd[]', 'allResultElim');
	}
	// Brackets IND
	if(tmp=document.getElementById('allIndBra')) {
		tmp.checked=obj.checked;
		setAllCheck('BracketsInd[]', 'allIndBra');
	}
	// Brackets TEAM
	if(tmp=document.getElementById('allTeamBra')) {
		tmp.checked=obj.checked;
		setAllCheck('BracketsTeam[]', 'allTeamBra');
	}
	// Rank Ind
	if(tmp=document.getElementById('allIndFin')) {
		tmp.checked=obj.checked;
		setAllCheck('FinalInd[]', 'allIndFin');
	}
	// Rank Team
	if(tmp=document.getElementById('allTeamFin')) {
		tmp.checked=obj.checked;
		setAllCheck('FinalTeam[]', 'allTeamFin');
	}
}

