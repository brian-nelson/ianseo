
function submitTicket()
{
	document.getElementById('xx_ElimSession').value=document.getElementById('x_ElimSession').value;
	document.getElementById('xx_Session').value=document.getElementById('x_Session').value;
	document.getElementById('xx_From').value=document.getElementById('x_From').value;
	document.getElementById('xx_To').value=document.getElementById('x_To').value;
	document.frmTick.submit();
}