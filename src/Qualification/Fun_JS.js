/* Folder Qualifications */ 

/*
	---------------------------------- Funzioni associate a index.php e index_all.php----------------------------------
*/

/*
	- ChangeGoldXNine(Value)
	Compila il form per settare/disattivare l'edit dei gold e delle X.
	Value contiene il valore da assegnare a xxx che verrà rinominato in command
*/
function ChangeGoldXNine(Value)
{
	document.getElementById('x_Gold').checked=!(document.getElementById('x_Gold').checked);
	document.getElementById('Command').name='Command';
	document.getElementById('Command').value=Value;
	document.FrmParam.submit();
}

/*
- ChangeArrows(Value)
Compila il form per settare/disattivare l'edit del numero di frecce.
Value contiene il valore da assegnare a xxx che verrà rinominato in command
*/
function ChangeArrows(Value)
{
	combo=document.getElementById('x_Arrows');
	combo.value=(combo.value==0 ? 1 : 0);

	document.getElementById('Command').name='Command';
	document.getElementById('Command').value=Value;
	document.FrmParam.submit();
}

/*
 * - CreateArrowsText()
 * Crea la textbox per impostare il numero di hit della distanza 
 * a tutti gli atleti sui paglioni selezionati
 */
function CreateArrowsText()
{
	if (document.getElementById('chk_BlockAutoSave').checked)
		return ;
	combo=document.getElementById('x_Arrows');
	span=document.getElementById('ArrowsToAllText');
	
	if (combo.value==2)
	{
		
		span.innerHTML='<input type="text" size="3" maxlength="4" name="x_AllArrows" id="x_AllArrows" value="0" />';
	}
	else
	{
		span.innerHTML='';
	}
}

/*
	- ChangeDist(NewDist,Value)
	Compila il form per cambiare la distanza attiva se è diversa da quella attuale.
	NewDist è la nuova distanza, Value è il valore da passare al Command
*/
function ChangeDist(NewDist,Value)
{
	if (NewDist!=document.getElementById('x_Dist').value)
	{
		document.getElementById('Command').name='Command';
		document.getElementById('Command').value=Value;
		document.getElementById('x_Dist').value=NewDist;
		document.FrmParam.submit();
	}
}

/*
	---------------------------------- Funzioni associate a CheckTargetUpdate.php----------------------------------
*/

/*
	- WriteHour(Hour)
	Scrive l'orario Hour nella textbox dell'ora
*/
function WriteHour(Hour)
{
	document.getElementById('x_Hour').value=Hour;
}

function submitTicket()
{
	document.getElementById('xx_Session').value=document.getElementById('x_Session').value;
	document.getElementById('xx_From').value=document.getElementById('x_From').value;
	document.getElementById('xx_To').value=document.getElementById('x_To').value;
	document.getElementById('xx_noEmpty').value=document.getElementById('x_noEmpty').value;
	document.frmTick.submit();
}