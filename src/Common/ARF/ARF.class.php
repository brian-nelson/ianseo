<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');

	class ARF
	{
		protected $xmlDoc=null;
		protected $xmlRoot=null;
		protected $xmlHeader=null;

		private $tourId=null;

		private $direction=null;
		private $tourCode=null;
		private $tourName=null;
		private $phase=null;
		private $type=null;

		private $error=0;

		const ARF_OUTPUT_VERSION='20081119.01';
		const ARF_INPUT_VERSION='20081119.01';

		const QUALIFICATION = 'QUALIFICATION';
		const ELIMINATION = 'ELIMINATION';
		const INDIVIDUAL_FINAL = 'INDIVIDUAL_FINAL';
		const TEAM_FINAL = 'TEAM_FINAL';

		public function __construct($tourId,$phase)
		{
			$this->setTourId($tourId);
			$this->setPhase($phase);

		// Estraggo le info del torneo
			/*$query
				= "SELECT "
					. "ToCode,ToName,IF(TtElabTeam=1 || TtElabTeam=2,'1',IF(INSTR(TtName,'Indoor')=0,'0','2' )) AS MyType "
				. "FROM "
					. "Tournament INNER JOIN Tournament*Type ON ToType=TtId "
				. "WHERE "
					. "ToId=" . $this->getTourId() . " ";*/
			$query
				= "SELECT "
					. "ToCode,ToName,IF(ToElabTeam=1 || ToElabTeam=2,'1',IF(INSTR(ToTypeName,'Indoor')=0,'0','2' )) AS MyType "
				. "FROM "
					. "Tournament "
				. "WHERE "
					. "ToId=" . $this->getTourId() . " ";

			$rs=safe_r_sql($query);

			if (safe_num_rows($rs)==1)
			{
				$myRow=safe_fetch($rs);

				$this->setTourCode($myRow->ToCode);
				$this->setTourName($myRow->ToName);
				$this->setType($myRow->MyType);

			}
			else
			{
				$this->setError(1);
			}

		}

		protected function setTourId($v)
		{
			$this->tourId=$v;
		}

		protected function getTourId()
		{
			return StrSafe_DB($this->tourId);
		}

		protected function setTourCode($v)
		{
			$this->tourCode=$v;
		}

		protected function getTourCode()
		{
			return $this->tourCode;
		}

		protected function setTourName($v)
		{
			$this->tourName=$v;
		}

		protected function getTourName()
		{
			return $this->tourName;
		}

		protected function setPhase($v)
		{
			$this->phase=$v;
		}

		protected function getPhase()
		{
			return $this->phase;
		}

		protected function setType($v)
		{
			$this->type=$v;
		}

		protected function getType()
		{
			return $this->type;
		}

		protected function setDirection($v)
		{
			$this->direction=$v;
		}

		protected function getDirection()
		{
			return $this->direction;
		}

		protected function setError($v)
		{
			$this->error=$v;
		}

		public function getError()
		{
			return $this->error;
		}
	}
?>