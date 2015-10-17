<!DOCTYPE HTML>
<html>
<head>
<style>
.sScrollTable td,
.sScrollTable th {
	transition: all 1s ease-out;
	-webkit-transition: all 1s ease-out; /* Safari */

}

.dkillRow td,
.dkillRow th {
	overflow:hidden;
	height:0px;
}

.ScrollTable { position: relative; }
.ScrollTable tr {
    position: absolute; top: 0; left: 0;
    transition: all 0.2s ease-out;
}

.ScrollTable tr:nth-child(0)  { transform: translate3d(0, 0%, 0); }
.ScrollTable tr:nth-child(1)  { transform: translate3d(0, 100%, 0); }
.ScrollTable tr:nth-child(2)  { transform: translate3d(0, 200%, 0); }
.ScrollTable tr:nth-child(3)  { transform: translate3d(0, 300%, 0); }

</style>
<script type="text/javascript">
function killRow(obj) {
// 	obj.classList.add('killRow');
	obj.parentElement.deleteRow(obj.rowIndex);
}
</script>
</head>
<body>

<table class="ScrollTable">
<tr class="killRow" onclick="killRow(this)">
	<th>heading</th>
	<td>col1</td>
	<td>col2</td>
</tr>
<tr class="killRow" onclick="killRow(this)">
	<th>heading</th>
	<td>col1</td>
	<td>col2</td>
</tr>
<tr class="killRow" onclick="killRow(this)">
	<th>heading</th>
	<td>col1</td>
	<td>col2</td>
</tr>
<tr class="killRow" onclick="killRow(this)">
	<th>heading</th>
	<td>col1</td>
	<td>col2</td>
</tr>
</table>
</body>
</html>