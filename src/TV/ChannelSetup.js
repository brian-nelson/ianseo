function saveChannel() {
	var name = $('#Name\\[0\\]').val();
	var message = $('#Message\\[0\\]').val();
	var url = $('#Url\\[0\\]').val();
	var tour = $('#Tournament\\[0\\]').val();
	var rule = $('#Rule\\[0\\]').val();
	var status = $('#Status\\[0\\]').val();

	$.getJSON("ChannelsUpdate.php?act=save&name="+encodeURIComponent(name)+"&msg="+encodeURIComponent(message)+"&url="+encodeURIComponent(url)+"&tour="+tour+"&rule="+rule+"&status="+status,
		function(data) {
			if(data.error==0) {
				var rowClone=$('#newRow').clone();
				rowClone[0].id='';
				// changes the id in inputs textareas and selects, and attaches the update event
				rowClone.find('input').each(function(i, item) {
					item.onclick=function() {update(this);};
					item.id=item.id.replace('[0]', '['+data.newID+']');
				});
				rowClone.find('textarea').each(function(i, item) {
					item.onclick=function() {update(this);};
					item.id=item.id.replace('[0]', '['+data.newID+']');
				});
				rowClone.find('select').each(function(i, item) {
					if(item.id!='Tournament[0]') item.onclick=function() {update(this);};
					item.id=item.id.replace('[0]', '['+data.newID+']');
				});

				$('#newRow').before(rowClone);

			}
		});
}

function update(obj) {
	$.getJSON("ChannelsUpdate.php?act=update&"+obj.id+"="+encodeURIComponent(obj.value),
		function(data) {
			if(data.TVRules!=undefined) {
				// we have TV rules to update!
				var Select=$(obj).parent().next().children(':first-child');
				Select[0].options.length=0;
				$.each(data.TVRules, function (i, item) {
					Select.append($('<option>', {
				        value: i,
				        text : item
				    }));
				});
			}
		});
}
