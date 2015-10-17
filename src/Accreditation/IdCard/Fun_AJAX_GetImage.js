var TimeCheck = 1500;	// Timeout per il check (in ms)


function reloadPicture()
{
	var x = document.getElementById("valueX").value;
	var y = document.getElementById("valueY").value;
	var w = document.getElementById("valueW").value;
	var athId=document.getElementById("AthId").value;
	var get = document.getElementById("getSnap").value;
	
	if(get!=2) {
		document.getElementById("imgGrabbed").src="grabImage.php?AthId="+athId+"&x="+x+"&y="+y+"&w="+w+"&get="+get+"&ts="+Date();
	}
	if(get==1)
		document.getElementById("getSnap").value=2;
	setTimeout("reloadPicture()",TimeCheck);
}

function moveBox(direction)
{
	var x = document.getElementById("valueX").value * 1;
	var y = document.getElementById("valueY").value * 1;
	var w = document.getElementById("valueW").value * 1;
	if(direction=='N')
		y -= 10;
	if(direction=='S')
		y += 10;

	if(direction=='W')
		x -= 10;
	if(direction=='E')
		x += 10;

	if(direction=='I') {
		w -= 24;
		x += 12;
		y += 16;
	}
	if(direction=='II') {
		w -= 48;
		x += 24;
		y += 32;
	}
	if(direction=='O') {
		w += 24;
		x -= 12;
		y -= 16;
	}
	if(direction=='OO') {
		w += 48;
		x -= 24;
		y -= 32;
	}

	
	if(y<0) y=0;
	if(x<0) x=0;
	if(w<0)	w=300;
	
//	if(x+w > 640)
//		x=640-w-2;
//	if(y+(w*4/3) > 480)
//		y=480-(w*4/3)-2;
	

	document.getElementById("valueX").value = x;
	document.getElementById("valueY").value = y;
	document.getElementById("valueW").value = w;
	if(direction=='GET')
	{
		if(document.getElementById("getSnap").value != 0)
			document.getElementById("getSnap").value = 0;
		else
			document.getElementById("getSnap").value = 1;
	}
}

function centerBox(e) {
	  var w = document.getElementById("valueW").value * 1;
	  var PosX = 0;
	  var PosY = 0;
	  var ImgPos;
	  
	  
	  ImgPos = FindPosition(myImg);
	  if (!e) var e = window.event;
	  if (e.pageX || e.pageY)
	  {
	    PosX = e.pageX;
	    PosY = e.pageY;
	  }
	  else if (e.clientX || e.clientY)
	    {
	      PosX = e.clientX + document.body.scrollLeft
	        + document.documentElement.scrollLeft;
	      PosY = e.clientY + document.body.scrollTop
	        + document.documentElement.scrollTop;
	    }
	  PosX = PosX - ImgPos[0];
	  PosY = PosY - ImgPos[1];
	  
	  PosX -= (w/2);
	  PosY -= (w*2/3);
	  
	  document.getElementById("valueX").value = PosX;
	  document.getElementById("valueY").value = PosY;
   
	  reloadPicture();
}

function FindPosition(oElement)
{
  if(typeof( oElement.offsetParent ) != "undefined")
  {
    for(var posX = 0, posY = 0; oElement; oElement = oElement.offsetParent)
    {
      posX += oElement.offsetLeft;
      posY += oElement.offsetTop;
    }
      return [ posX, posY ];
    }
    else
    {
      return [ oElement.x, oElement.y ];
    }
}