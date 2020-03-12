var videoElement;
var videoSelect;
var canvas;
var ctx;

function snapshot() {
	if (window.stream) {
	    canvas.crossOrigin='anonymous';
	    videoElement.crossOrigin='anonymous';

    	canvas.height=videoElement.videoHeight;
    	canvas.width=videoElement.videoWidth;
    	ctx.drawImage(videoElement, 0, 0, videoElement.videoWidth, videoElement.videoHeight);
    	resizeVideo();

//    	if(document.getElementById("athPic").src=='data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==' || confirm(AreYouSure)) {
    	sendPicture(canvas.toDataURL('image/png'));
//    	}
    }
}

function changeZoom() {

}

function stopVideo() {
	window.stream.stop();
	document.getElementById("start-button").style.display='';
	document.getElementById("stop-button").style.display='none';
	document.getElementById("face").style.display='none';
}

function startVideo() {
	if (!!window.stream) {
		videoElement.src = null;
		window.stream.stop();
	}
	var videoSource = videoSelect.value;
	var constraints = {
		video: {
			optional: [{sourceId: videoSource}],
    	    mandatory: {
    	    	minWidth: minW,
		    	minHeight: minH,
		    }
		}
	};
	navigator.getUserMedia(constraints, successCallback, errorCallback);
}

function successCallback(stream) {
	window.stream = stream; // make stream available to console
	videoElement.src = window.URL.createObjectURL(stream);
	videoElement.play();

	document.getElementById("start-button").style.display='none';
	document.getElementById("stop-button").style.display='';
	setTimeout(function(){resizeVideo()}, 1000);
}

function errorCallback(error){
	console.log("navigator.getUserMedia error: ", error);
}

function resizeVideo() {
	var videoElement=document.getElementById('CamVideo');
	var width = videoElement.clientWidth;
	var height = videoElement.clientHeight;
	var ratio= Math.min(width/300, height/400);
	var new_width = 400*ratio;
	var new_height = 400*ratio;

	document.getElementById("face").style.top = ((height-new_height)/2)+'px';
	document.getElementById("face").style.left = ((width-new_width)/2)+'px';
	document.getElementById("face").style.width = new_width+'px';
	document.getElementById("face").style.height = new_height+'px';
	if(window.stream)
		document.getElementById("face").style.display='';
}

function gotSources(sourceInfos) {
	for (var i = 0; i != sourceInfos.length; ++i) {
		var sourceInfo = sourceInfos[i];
		var option = document.createElement("option");
		option.value = sourceInfo.id;
	    if (sourceInfo.kind === 'video') {
	    	option.text = sourceInfo.label || '--- ' + (videoSelect.length + 1) + ' ---';
	    	videoSelect.appendChild(option);
	    }
	}
}

function setupVideo() {
	navigator.getUserMedia = (navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia);
	videoElement = document.querySelector("video");
	videoSelect = document.querySelector("select#videoSource");
	canvas = document.querySelector('canvas');
	ctx = canvas.getContext('2d');

	if (typeof MediaStreamTrack === 'undefined'){
		alert('This browser does not support MediaStreamTrack.\n\nTry Chrome Canary.');
	} else {
		try {
			MediaStreamTrack.getSources(gotSources);
		} catch(e) {
			// alert('This browser does not support camera selection.\n\nTry Chrome Canary.');
		}
	}
	window.onresize = function(){resizeVideo()};
}