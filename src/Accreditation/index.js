
function ChangeAccOp(obj) {
	$.getJSON('index-AccOp.php?type='+obj.value, function(data) {
		$('#ExtraContent').html(data.value);
	});
}