/**
 * Funzioni x gestire il  resize dell'immagine del logo
*/

/**
The JavaScript functions below will gradually enlarge or shrink an image
on the current page. I use this for mouseover effects, but there might be
some other interesting applications of it as well.

You can use these scripts in any way you'd like, just don't pretend like
you wrote them yourself.

version 1.0
March 17, 2005
Julian Robichaux, http://www.nsftools.com
*/

/**** adjust these two parameters to control how fast or slow
 **** the images grow/shrink ****/
// how many milliseconds we should wait between resizing events
var resizeDelay = 10;
// how many pixels we should grow or shrink by each time we resize
var resizeIncrement = 5;

// this will hold information about the images we're dealing with
var imgCache = new Object();


/**
The getCacheTag function just creates a (hopefully) unique identifier for
each <img> that we resize.
*/
function getCacheTag (imgElement) {
	return imgElement.src + "~" + imgElement.offsetLeft + "~" + imgElement.offsetTop;
}


/**
We're using this as a class to hold information about the <img> elements
that we're manipulating.
*/
function cachedImg (imgElement, increment) {
	this.img = imgElement;
	this.cacheTag = getCacheTag(imgElement);
	this.originalSrc = imgElement.src;
	
	var h = imgElement.height;
	var w = imgElement.width;
	this.originalHeight = h;
	this.originalWidth = w;
	
	increment = (!increment) ? resizeIncrement : increment;
	this.heightIncrement = Math.ceil(Math.min(1, (h / w)) * increment);
	this.widthIncrement = Math.ceil(Math.min(1, (w / h)) * increment);
}


/**
This is the function that should be called in the onMouseOver and onMouseOut
events of an <img> element. For example:

<img src='onesmaller.gif' onMouseOver='resizeImg(this, 150, "onebigger.gif")' onMouseOut='resizeImg(this)'>

The only required parameter is the first one (imgElement), which is a
reference to the <img> element itself. If you're calling from onMousexxx, 
you can just use "this" as the value.

The second parameter specifies how much larger or smaller we should resize
the image to, as a percentage of the original size. In the example above,
we want to resize it to be 150% larger. If this parameter is omitted, we'll
assume you want to resize the image to its original size (100%).

The third parameter can specify another image that should be used as the
image is being resized (it's common for "rollover images" to be similar but
slightly different or more colorful than the base images). If this parameter
is omitted, we'll just resize the existing image.
*/
function resizeImg (imgElement, percentChange, newImageURL) {
	// convert the percentage (like 150) to an percentage value we can use
	// for calculations (like 1.5)
	var pct = (percentChange) ? percentChange / 100 : 1;
	
	// if we've already resized this image, it will have a "cacheTag" attribute
	// that should uniquely identify it. If the attribute is missing, create a
	// cacheTag and add the attribute
	var cacheTag = imgElement.getAttribute("cacheTag");
	if (!cacheTag) {
		cacheTag = getCacheTag(imgElement);
		imgElement.setAttribute("cacheTag", cacheTag);
	}
	
	// look for this image in our image cache. If it's not there, create it.
	// If it is there, update the percentage value.
	var cacheVal = imgCache[cacheTag];
	if (!cacheVal) {
		imgCache[cacheTag] = new Array(new cachedImg(imgElement), pct);
	} else {
		cacheVal[1] = pct;
	}
	
	// if we're supposed to be using a rollover image, use it
	if (newImageURL)
		imgElement.src = newImageURL;
	
	// start the resizing loop. It will continue to call itself over and over
	// until the image has been resized to the proper value.
	resizeImgLoop(cacheTag);
	return true;
}


/**
This is the function that actually does all the resizing. It calls itself
repeatedly with setTimeout until the image is the right size.
*/
function resizeImgLoop (cacheTag) {
	// get information about the image element from the image cache
	var cacheVal = imgCache[cacheTag];
	if (!cacheVal)
		return false;
	
	var cachedImageObj = cacheVal[0];
	var imgElement = cachedImageObj.img;
	var pct = cacheVal[1];
	var plusMinus = (pct > 1) ? 1 : -1;
	var hinc = plusMinus * cachedImageObj.heightIncrement;
	var vinc = plusMinus * cachedImageObj.widthIncrement;
	var startHeight = cachedImageObj.originalHeight;
	var startWidth = cachedImageObj.originalWidth;
	
	var currentHeight = imgElement.height;
	var currentWidth = imgElement.width;
	var endHeight = Math.round(startHeight * pct);
	var endWidth = Math.round(startWidth * pct);
	
	// if the image is already the right size, we can exit
	if ( (currentHeight == endHeight) || (currentWidth == endWidth) )
		return true;
	
	// increase or decrease the height and width, making sure we don't get
	// larger or smaller than the final size we're supposed to be
	var newHeight = currentHeight + hinc;
	var newWidth = currentWidth + vinc;
	if (pct > 1) {
		if ((newHeight >= endHeight) || (newWidth >= endWidth)) {
			newHeight = endHeight;
			newWidth = endWidth;
		}
	} else {
		if ((newHeight <= endHeight) || (newWidth <= endWidth)) {
			newHeight = endHeight;
			newWidth = endWidth;
		}
	}
	
	// set the image element to the new height and width
	imgElement.height = newHeight;
	imgElement.width = newWidth;
	
	// if we've returned to the original image size, we can restore the
	// original image as well (because we may have been using a rollover
	// image in the original call to resizeImg)
	if ((newHeight == cachedImageObj.originalHeight) || (newWidth == cachedImageObj.originalwidth)) {
		imgElement.src = cachedImageObj.originalSrc;
	}
	
	// shrink or grow again in a few milliseconds
	setTimeout("resizeImgLoop('" + cacheTag + "')", resizeDelay);
}

