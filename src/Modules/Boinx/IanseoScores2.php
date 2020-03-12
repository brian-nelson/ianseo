<?php

require_once('./config.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Lib/ArrTargets.inc.php');
require_once('Common/Fun_Phases.inc.php');

// check where is the live flag
$SQL= "(Select '0' Team from Finals "
	. "where "
	. " FinLive='1' "
	. " and FinTournament=$TourId)"
	. " UNION "
	. "(Select '1' team from TeamFinals "
	. "where "
	. " TfLive='1' "
	. " and TfTournament=$TourId)";
$q=safe_r_sql($SQL);

include('Common/CheckPictures.php');
CheckPictures($TourCode);

if($r=safe_fetch($q)) {
	include("IanseoScores2-$r->Team.php");
} else {
	exit('No Live flag selected. Go to Ianseo Final/Team Spotting page and activate a Live Event');
}

exit();


?>
<archeryscores>
    <header>1/4' finale</header>
    <games>
        <game>
            <opponent1>
                <targetno>1</targetno>
                <name>Topolinia</name>
                <shortname>TOP</shortname>
                <component1>Pippo</component1>
                <component2>Pluto</component2>
                <component3>Paperino</component3>
                <set1>24</set1>
                <set2>26</set2>
                <set3>25</set3>
                <set4>31</set4>
                <set5>32</set5>
                <tie>10+</tie>
                <setpoints>9</setpoints>
                <arrow1>9</arrow1>
                <arrow2>9</arrow2>
                <arrow3>10</arrow3>
                <arrow4>9</arrow4>
                <arrow5>9</arrow5>
                <arrow6>9</arrow6>
                <total>153</total>
                <setscore>5</setscore>
                <flag>file:///Users/markcar/desktop/disney.jpg</flag>
                <photo1>file:///Users/markcar/desktop/photo1-1.jpg</photo1>
                <photo2>file:///Users/markcar/desktop/photo1-2.jpg</photo2>
                <photo3>file:///Users/markcar/desktop/photo1-3.jpg</photo3>
            </opponent1>
            <opponent2>
                <targetno>2</targetno>
                <name>Paperopoli</name>
                <shortname>PAP</shortname>
                <component1>Minni</component1>
                <component2>Clarabella</component2>
                <component3>Orazio</component3>
                <set1>27</set1>
                <set2>27</set2>
                <set3>25</set3>
                <set4>24</set4>
                <set5>25</set5>
                <tie>9-</tie>
                <setpoints>9</setpoints>
                <arrow1>27</arrow1>
                <arrow2>27</arrow2>
                <arrow3>25</arrow3>
                <arrow4>24</arrow4>
                <arrow5>25</arrow5>
                <arrow6>9</arrow6>
                <total>133</total>
                <setscore>3</setscore>
                <flag>file:///Users/markcar/desktop/mat.jpg</flag>
                <photo1>file:///Users/markcar/desktop/photo2-1.jpg</photo1>
                <photo2>file:///Users/markcar/desktop/photo2-2.jpg</photo2>
                <photo3>file:///Users/markcar/desktop/photo2-3.jpg</photo3>
            </opponent2>
        </game>
	</games>
</archeryscores>
<?php

/*

tie: è la freccia di spareggio. il + e - erano solo per verificare gli spazi per la freccia più vicina al centro a parità di punteggio
arrows1 - 6: valore delle freccie del set in corso (non ancora utilizzate)
total: totale dei punti freccia dello scontro dello scontro sia per l'individuale che per le squadre
name: nome dell'arciere o della nazione
shortname: nome abbreviato per visualizzazioni in poco spazio. Per le nazioni è il codice cio (ITA,FRA) per l'individuale è più un casino...
potremmo adottare il sistema fita dei 12 caratteri per il segnapunti in un campo separato del database oppure il codice cio. il semplice troncamento del nome a n caratteri è già previsto per quasi tutti i layers, manca ancora nel top score.

BASED ON VERSION 1.2 OF IANSEOSCORES

Il Layer 3 set Match utilizza:
	flag
	targetno
	name
	setscore
	set1
	set2
	set3
	tie

Il Layer 5 set Match utilizza:
	flag
	targetno
	name
	setscore
	set1
	set2
	set3
	set4
	set5
	tie

Il Layer Team Match utilizza:
	targetno
	name
	Total
	set1
	set2
	set3
	set4
	tie

Il Layer Archery Top Scoring utilizza:
	flag
	name (o shortname in vista top)
	setscore (se abilitato)
	total o setpoints a scelta (setpoints è il punteggio del set/volee corrente)

Il Layer Archery Heading utilizza:
	header



*/



?>