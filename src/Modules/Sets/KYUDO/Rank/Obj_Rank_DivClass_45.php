<?php
require_once ('Common/Rank/Obj_Rank_DivClass.php');

	class Obj_Rank_DivClass_45 extends Obj_Rank_DivClass {
		public function __construct($opts) {
			parent::__construct($opts);
		}
        public function read() {
            parent::read();
            foreach ($this->data["sections"] as $kData=>$vData) {
                $this->data["sections"][$kData]["meta"]["fields"]["countryName"]="Dojo";
            };
        }

        public function calculate() {
			return true;
		}
	}