<?php
/**
 * Obj_Rank_FinalTeam
 *
 * Implementa l'algoritmo di default per il calcolo della rank finale a squadre.
 *
 * La tabella in cui scrive è Teams e popola la RankFinal "a pezzi". Solo alla fine della gara
 * avremo tutta la colonna valorizzata.
 *
 * A seconda della fase che sto trattando avrò porzioni di colonna da gestire differenti e calcoli differenti.
 *
 * Per questa classe $opts ha la seguente forma:
 *
 * array(
 * 		eventsC => array(<ev_1>@<calcPhase_1>,<ev_2>@<calcPhase_2>,...,<ev_n>@<calcPhase_n>)			[calculate,non influisce su read]
 * 		eventsR => array(<ev_1>,...,<ev_n>)																[read,non influisce su calculate]
 * 		tournament => #																					[calculate/read]
 * )
 */
require_once(dirname(__FILE__).'/Obj_Rank_FinalTeam_3_SetFRChampsD1DNAP_calc.php');

class Obj_Rank_FinalInd_3_SetFRChampsD1DNAP_calc extends Obj_Rank_FinalTeam_3_SetFRChampsD1DNAP_calc {
	protected $FromIndividual=true;
}