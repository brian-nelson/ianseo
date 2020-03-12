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
	protected $TargetSize=0;
	protected $TourId=0;
	protected $TargetData='';
	protected $SVGHeader='';
	protected $SVGTarget='';
	protected $SVGArrows='';
	protected $Ratio=1;
	protected $Expand=1;
	protected $CenterX=0;
	protected $CenterY=0;
	protected $ArrowPos=array();

	public $Diameter=0;
	public $TargetRadius=0;

	public function __construct($arrArrows = array(), $tgtSize=6100) {
		$this->TargetSize = $tgtSize;
		foreach($arrArrows as $k=>$v) {
			if($v['size']!=0) {
				$this->Sizes[$k] = $v['size'];
				$this->Colors[$k]=array(intval("0x".substr($v['fillColor'],0,2),16),intval("0x".substr($v['fillColor'],2,2),16),intval("0x".substr($v['fillColor'],4,2),16));
				$this->BorderColors[$k]=array(intval("0x".substr($v['lineColor'],0,2),16),intval("0x".substr($v['lineColor'],2,2),16),intval("0x".substr($v['lineColor'],4,2),16));
			}
		}
		arsort($this->Sizes);
	}

	public function setArrowPos($Arrows=array()) {
		$this->ArrowPos=$Arrows;
	}

	public function getHitValue($dim, $x, $y) {
		$returnValue = array("V"=>'A', "X"=>0, "Y"=>0);
		$dimension = $this->reDim($dim);
		$Center = $dimension/2;
		$ScaleFactor = $dimension/max($this->Sizes);
		$dist = sqrt(pow(($Center-$y),2) + pow(($Center-$x),2))/$ScaleFactor;
		foreach ($this->Sizes as $k=>$v) {
			if($v >= $dist*2)
				$returnValue["V"]= $k;
		}
		$returnValue["X"] = round(((($x-$Center)/$Center)*$this->TargetSize),0,PHP_ROUND_HALF_DOWN);
		$returnValue["Y"] = round(((($Center-$y)/$Center)*$this->TargetSize),0,PHP_ROUND_HALF_DOWN);
		return $returnValue;
	}

	protected function reDim($dimension) {
		if($dimension % 2 == 0)
			$dimension += 1;
		return $dimension;
	}

	public function draw($dimension = 100) {
		$dimension = $this->reDim($dimension);
		$Center = $dimension/2;
		$ScaleFactor = $dimension/max($this->Sizes);
		$image = imagecreatetruecolor($dimension, $dimension);
		imagefill($image,0,0,imagecolorallocate($image,255, 255, 244));

		$ArrowDiameter=min(ceil($dimension/20),20);

		foreach($this->Sizes as $key=>$value) {
			$diameter=($value*$ScaleFactor);
			$color = imagecolorallocate($image, $this->Colors[$key][0], $this->Colors[$key][1], $this->Colors[$key][2]);
			$bordercolor = imagecolorallocate($image, $this->BorderColors[$key][0], $this->BorderColors[$key][1], $this->BorderColors[$key][2]);
			imagefilledellipse($image, $Center, $Center, $diameter, $diameter, $color);
			imageellipse($image, $Center, $Center, $diameter, $diameter, $bordercolor);
		}

		$color = imagecolorallocate($image, 64, 255, 64);
		foreach($this->ArrowPos as $pos) {
			$retValue=array();
			if(preg_match("/^([\-0-9]+)[,]([\-0-9]+)$/", $pos, $retValue)) {
				$x = $dimension/2 + ($retValue[1]*$dimension/(2*$this->TargetSize));
				$y = $dimension/2 + (-1*$retValue[2]*$dimension/(2*$this->TargetSize));
				imagefilledellipse($image, $x, $y, $ArrowDiameter, $ArrowDiameter, $color);
				imageellipse($image, $x, $y, $ArrowDiameter, $ArrowDiameter, $bordercolor);
			}
		}
		header("Content-type: image/png");
		imagepng($image);
		imagedestroy($image);
		die();
	}

	public function initSVG($TourId=0, $Event='', $Match=999, $Team=0) {
		// get Event and target
		$this->TourId=$TourId ? $TourId : $_SESSION['TourId'];
		$this->TargetData=GetMaxScores($Event, $Match, $Team, $this->TourId);

		// calculate the ratio between the face global size and the actual face size
		$this->Expand=$this->TargetData['FullSize']/$this->TargetData['MaxSize'];

		// calculate the ratio between the viewport and the size of the target
		$this->Ratio=$this->TargetData['FullSize']/$this->TargetData['TargetSize'];

		$this->CenterX=$this->TargetData['FullSize']*5 + 40;
		$this->CenterY=$this->TargetData['FullSize']*5 + 40;
	}

	public function setSVGHeader($id='', $event='') {
		$this->Diameter=$this->TargetData['FullSize']*10 + 80;
		$this->TargetRadius=$this->TargetData['TargetRadius'];
		$this->SVGHeader='<?xml version="1.0" standalone="yes"?><svg class="SVGTarget" '
			.($id ? ' id="'.$id.'" ' : '')
			.($event ? ' '.$event.' ' : '')
			.' convert="'.($this->Ratio*$this->Expand).'" realsize="'.($this->TargetData['TargetSize']*10/$this->Expand).'" viewbox="0 0 '.($this->Diameter).' '.($this->Diameter).'" height="'.$this->Diameter.'" width="'.$this->Diameter.'" xmlns="http://www.w3.org/2000/svg">';
		$this->SVGHeader.='<defs>
    		<radialGradient id="gradArrow" cx="50%" cy="50%" r="50%" fx="50%" fy="50%">
      			<stop offset="0%" style="stop-color:rgb(0,0,0);stop-opacity:0.7;" />
      			<stop offset="70%" style="stop-color:rgb(0,255,0);stop-opacity:0.8;" />
      			<stop offset="100%" style="stop-color:rgb(0,128,0);stop-opacity:1;" />
    		</radialGradient>
    		<radialGradient id="gradArrowReverse" cx="50%" cy="50%" r="50%" fx="50%" fy="50%">
      			<stop offset="0%" style="stop-color:rgb(255,0,255);stop-opacity:0.7;" />
      			<stop offset="50%" style="stop-color:rgb(64,0,64);stop-opacity:0.8;" />
      			<stop offset="100%" style="stop-color:rgb(255,255,0);stop-opacity:1;" />
    		</radialGradient>
    		<radialGradient id="gradLeft" cx="50%" cy="50%" r="50%" fx="50%" fy="50%">
      			<stop offset="0%" style="stop-color:rgb(0,0,0);stop-opacity:0.7;" />
      			<stop offset="70%" style="stop-color:rgb(0,255,255);stop-opacity:0.8;" />
      			<stop offset="100%" style="stop-color:rgb(0,128,128);stop-opacity:1;" />
    		</radialGradient>
    		<radialGradient id="gradRight" cx="50%" cy="50%" r="50%" fx="50%" fy="50%">
      			<stop offset="0%" style="stop-color:rgb(0,0,0);stop-opacity:0.7;" />
      			<stop offset="70%" style="stop-color:rgb(255,255,0);stop-opacity:0.8;" />
      			<stop offset="100%" style="stop-color:rgb(128,128,0);stop-opacity:1;" />
    		</radialGradient>
  			</defs>';
	}

	public function setTarget() {
		$Tgt=array();
		foreach($this->TargetData['Arrows'] as $Circle) {
			if($Circle['size']>0) {
				$Tgt[$Circle['size']]='<circle cx="'.$this->CenterX.'" cy="'.$this->CenterY.'" r="'.round($this->Expand*$Circle['size']*5, 2).'" stroke="#'.$Circle['lineColor'].'" stroke-width="1" fill="#'.$Circle['fillColor'].'" />';
			}
		}
		krsort($Tgt);
		$this->SVGTarget=implode('', $Tgt);

		// add middle spot
		$this->SVGTarget.='<line x1="'.($this->CenterX-3).'" y1="'.($this->CenterY).'" x2="'.($this->CenterX+3).'" y2="'.($this->CenterY).'" stroke-width="0.5" stroke="#000000"/>';
		$this->SVGTarget.='<line x1="'.($this->CenterX).'" y1="'.($this->CenterY-3).'" x2="'.($this->CenterX).'" y2="'.($this->CenterY+3).'" stroke-width="0.5" stroke="#000000"/>';
	}

	public function drawSVG($Event='', $Team=0, $Match=999, $End=0, $Both=0, $TourId=0) {
		if(!$this->TargetData) {
			$this->initSVG($TourId, $Event, $Match, $Team);
		}

		if(!$this->SVGHeader) {
			$this->setSVGHeader();
		}

		if(!$this->SVGTarget) {
			$this->setTarget();
		}

		if(!$this->SVGArrows) {
			// get the arrow positions
			if($Team) {
				$SQL="select ".($End<=-1 ? 'TfTiePosition' : 'TfArrowPosition')." as ArrowPositions from TeamFinals where TfEvent='$Event' and TfMatchNo=$Match and TfTournament={$this->TourId}";
			} else {
				$SQL="select ".($End<=-1 ? 'FinTiePosition' : 'FinArrowPosition')." as ArrowPositions from Finals where FinEvent='$Event' and FinMatchNo=$Match and FinTournament={$this->TourId}";
			}

			$q=safe_r_sql($SQL);
			if($r=safe_fetch($q) and $pos=@json_decode($r->ArrowPositions)) {
				// get the index and the number of arrows to fetch
				if($End==0) {
					$Index=0;
					$Num=count($pos);
				} else {
					$Num=$End<0 ? $this->TargetData['SO'] : $this->TargetData['ArrowsPerEnd'];
					$Index=(abs($End)-1)*$Num;
				}

				$this->drawSVGArrows(array_slice($pos, $Index, $Num));
			}
		}

		return $this->OutputStringSVG();
	}

	public function drawSVGArrows($Arrows=array(), $JudgesView=false, $DrawSighter=false) {
		$this->SVGArrows='';
		if($Arrows) {
			// draws the arrows
			foreach($Arrows as $ar) {
				if($JudgesView) {
					// for the judges view (Final/Viewer/) the arrow radius is parametric and assigned as 1/80 of the visualized target radius
					$newArRadius = $this->TargetData['TargetRadius']/65;
					// distance is from center of target to edge of arrow, so X and Y must be recalculate
					$R1=$ar['D']+$ar['R'];
					$R2=$ar['D']+$newArRadius;
					$ar['X']=$ar['X']*$R2/$R1;
					$ar['Y']=$ar['Y']*$R2/$R1;
					$ar['R']=$newArRadius;
				}
				$this->SVGArrows.='<circle class="svgArrow" cx="'.round(($this->Ratio*$this->Expand*$ar['X'])+$this->CenterX, 2).'" cy="'.round($this->CenterY+($this->Ratio*$this->Expand*$ar['Y']), 2).'" r="'.($this->Ratio*$this->Expand*$ar['R']).'" fill="url(#gradArrow)" />';
			}

			if($JudgesView and !empty($ar) and $DrawSighter) {
				$StartX=round(($this->Ratio*$this->Expand*$ar['X'])+$this->CenterX, 2);
				$StartY=round($this->CenterY+($this->Ratio*$this->Expand*$ar['Y']), 2);
				$R=round($this->Ratio*$this->Expand*$ar['R'], 2);
				$this->SVGArrows.='<path class="svgLastArrow" d="M '.$StartX.', '.($StartY-2*$R).' l -'.$R.',-' . (2*$R) . ' l '.(2*$R).',0 l -'.($R).','.(2*$R).' z M '.$StartX.', '.($StartY+2*$R).' l -'.$R.',' . (2*$R) . ' l '.(2*$R).',0 l -'.($R).',-'.(2*$R).' z M '.($StartX-2*$R).', '.($StartY).' l -'.(2*$R).',-' . ($R) . ' l 0,'.(2*$R).' l '.(2*$R).',-'.($R).' z M '.($StartX+2*$R).', '.($StartY).' l '.(2*$R).',-' . ($R) . ' l 0,'.(2*$R).' l -'.(2*$R).',-'.($R).' z" fill="#00ff00" stroke="#000000" opacity="0" stroke-width="2" />';
			}
		}
	}

	public function drawSVGArrowsGroups($GroupId='', $Arrows=array()) {
		$tmp='';
		if($Arrows) {
			// draws the arrows
			foreach($Arrows as $ArId => $ar) {
				$tmp.='<circle class="svgArrow" id="'.$ArId.'" cx="'.round(($this->Ratio*$this->Expand*$ar['X'])+$this->CenterX, 2).'" cy="'.round($this->CenterY+($this->Ratio*$this->Expand*$ar['Y']), 2).'" r="'.($this->Ratio*$this->Expand*($ar['R'])).'" fill="url(#gradArrow)" />';
			}
		}
		if($GroupId) {
			$tmp='<g id="'.$GroupId.'">'.$tmp.'</g>';
		}
		$this->SVGArrows.=$tmp;
	}

	public function OutputStringSVG() {
		return $this->SVGHeader.$this->SVGTarget.$this->SVGArrows.'<g id="SvgCursor"><circle style="display:none" cx="0" cy="0" r="'.($this->Ratio*$this->Expand*3).'" fill="#000000" fill-opacity="0.7" /><circle style="display:none" cx="0" cy="0" r="'.($this->Ratio*$this->Expand*2).'" fill="#00a000" fill-opacity="1" /></g></svg>';
	}

	public function OutputSVG($Event='', $Team=0, $Match=999, $End=0, $Both=0, $TourId=0) {
		header('Content-type: image/svg+xml');
		echo($this->drawSVG($Event, $Team, $Match, $End, $Both, $TourId));
		die();
	}

	public function drawSvgError() {
		global $CFG;
		header('Content-type: image/svg+xml');
		readfile($CFG->DOCUMENT_PATH.'/Common/Images/isk-close.svg');
		die();
	}
}
?>