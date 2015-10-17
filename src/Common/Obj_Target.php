<?php
/********************************************
 *
 * Object Target
 *
 *
 ********************************************/
class Obj_Target
{
	protected $Sizes=array();
	protected $Colors=array();
	protected $BorderColors=array();
	protected $ArrowPos=array();

	public function __construct($arrArrows)
	{
		foreach($arrArrows as $k=>$v)
		{
			if($v[0]!=0)
			{
				$this->Sizes[$k] = $v[0];
				$this->Colors[$k]=array(intval("0x".substr($v[1],0,2),16),intval("0x".substr($v[1],2,2),16),intval("0x".substr($v[1],4,2),16));
				$this->BorderColors[$k]=array(intval("0x".substr($v[2],0,2),16),intval("0x".substr($v[2],2,2),16),intval("0x".substr($v[2],4,2),16));
			}
		}
		arsort($this->Sizes);
	}

	public function setArrowPos($Arrows=array())
	{
		$this->ArrowPos=$Arrows;
	}

	public function getHitValue($dim, $x, $y)
	{
		$returnValue = array("V"=>'A', "X"=>0, "Y"=>0);
		$dimension = $this->reDim($dim);
		$Center = $dimension/2;
		$ScaleFactor = $dimension/max($this->Sizes);
		$dist = sqrt(pow(($Center-$y),2) + pow(($Center-$x),2))/$ScaleFactor;
		foreach ($this->Sizes as $k=>$v)
		{
			if($v >= $dist*2)
				$returnValue["V"]= $k;
		}
		$returnValue["X"] = (int) ((($x-$Center)*1000/$Center));
		$returnValue["Y"] = (int) ((($y-$Center)*1000/$Center));
		return $returnValue;
	}

	protected function reDim($dimension)
	{
		if($dimension % 2 == 0)
			$dimension += 1;
		return $dimension;
	}

	public function draw($dimension = 100)
	{
		$dimension = $this->reDim($dimension);
		$Center = $dimension/2;
		$ScaleFactor = $dimension/max($this->Sizes);
		$image = imagecreatetruecolor($dimension, $dimension);
		imagefill($image,0,0,imagecolorallocate($image,255, 255, 244));

		for($i=0; $i<count($this->Sizes); $i++)

		$ArrowDiameter=ceil($dimension/20);

		foreach($this->Sizes as $key=>$value)
		{
			$diameter=($value*$ScaleFactor);
			//$ArrowDiameter=min($diameter,$ArrowDiameter);
			$color = imagecolorallocate($image, $this->Colors[$key][0], $this->Colors[$key][1], $this->Colors[$key][2]);
			$bordercolor = imagecolorallocate($image, $this->BorderColors[$key][0], $this->BorderColors[$key][1], $this->BorderColors[$key][2]);
			imagefilledellipse($image, $Center, $Center, $diameter, $diameter, $color);
			imageellipse($image, $Center, $Center, $diameter, $diameter, $bordercolor);
		}

		//$ArrowDiameter=max($dimension/25, $ArrowDiameter/2);
		$color = imagecolorallocate($image, 64, 255, 64);
		foreach($this->ArrowPos as $pos) {
			if(preg_match("/^[\-0-9]*,[\-0-9]*$/",$pos)) {
				list($x,$y)=explode(',', $pos);
				$x = $dimension/2 + ($x*$dimension/2000);
				$y = $dimension/2 + ($y*$dimension/2000);
	
				imagefilledellipse($image, $x, $y, $ArrowDiameter, $ArrowDiameter, $color);
				imageellipse($image, $x, $y, $ArrowDiameter, $ArrowDiameter, $bordercolor);
			}
		}

		header("Content-type: image/png");
		imagepng($image);
		imagedestroy($image);
	}
}
?>