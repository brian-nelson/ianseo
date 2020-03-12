function UpdateRows(obj) {
	var Rows=document.getElementsByClassName(obj.value);

	for(var i=0; i<Rows.length; i++) {
		Rows[i].style.display=(obj.checked ? 'table-row' : 'none');
	}
}