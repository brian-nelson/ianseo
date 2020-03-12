<?php
	function purgeSetPoints($p1,$p2)
	{
		$p1=explode('|',$p1);
		$p2=explode('|',$p2);

		if (count($p1)==count($p2))
		{
			for ($i=count($p1)-1;$i>=0;--$i)
			{
				if ($p1[$i]!=0 || $p2[$i]!=0)
					break;
					
				if ($p1[$i]==0 && $p2[$i]==0)
				{
					unset($p1[$i]);
					unset($p2[$i]);
				}
				
				--$i;
			}
		}
		return array(implode(' ',$p1),implode(' ',$p2));
	}
	
	function isFinished($row,$points4win,$max)
	{
		$ret=0;
		$winner=0;
		
		if ($row->setScore1>$row->setScore2)
			$winner=1;
		elseif ($row->setScore2>$row->setScore1)
			$winner=2;
		
		if ($winner==0) {
			if($row->setScore1==$row->setScore2 && $row->setScore1==($points4win[$row->event]-1))
				$ret=3;
			else
				$ret=0;
		} else {
		// qui il match non è finito
			if ($row->{'setScore'.$winner}<$points4win[$row->event]) {
				$ret=0;
		// qui il match è finito ma devo capire se adesso o prima
			} else {
				if (($row->setScore1+$row->setScore2)==$max) // finito ora
					$ret=2;				
				else	// finito prima
					$ret=1;
			}
		}
		
		return $ret;
	}