<?php

// these go here as it is a "global" definition, used or not
$tourCollation = '';
$tourDetIocCode = 'IS';
if(empty($SubRule)) $SubRule='1';

function CreateStandardDivisions($TourId, $Type=1, $SubRule=0) { //Bogaflokkar/Undankeppni. Þessum parti er lokið COMPLETE
    // Ignoring sub-rules for now. Þetta function section bætir við Bogaflokkum í "Division and classes" partinn af Ianseo
    $i=1;
	CreateDivision($TourId, $i++, 'R', 'Recurve/ Sveigbogi');
	CreateDivision($TourId, $i++, 'C', 'Compound/ Trissubogi');
    CreateDivision($TourId, $i++, 'B', 'Barebow/ Berbogi');
	if ($SubRule==6) { // "Set Kids classes" BARA UNGMENNA FLOKKAR SEMSAGT NUM COMPLETE
	CreateDivision($TourId, $i++, 'L', 'Longbow/ Langbogi');
	CreateDivision($TourId, $i++, 'I', 'Instinctive bow');
	}
}

function CreateStandardClasses($TourId, $SubRule, $Type) { //Aldursflokkar og kyn/Undankeppni. COMPLETE
    //Aldursflokkar. Þetta function section bætir við aldursflokkum og kynjum í "Division and classes" partinn af Ianseo
    $i=1;

	if ($SubRule==1) { // "Championship" Allir Aldursflokkar COMPLETE
		CreateClass($TourId, $i++, 1, 99, 0, 'M', 'M', 'Men/ Karla', 1, '');
		CreateClass($TourId, $i++, 1, 99, 1, 'W', 'W', 'Women/ Kvenna', 1, '');
        CreateClass($TourId, $i++, 50, 99, 0, 'MM', 'MM,M', 'Master Men/ E50 Karla', 1, '');
        CreateClass($TourId, $i++, 50, 99, 1, 'MW', 'MW,W', 'Master Women/ E50 Kvenna', 1, '');
        CreateClass($TourId, $i++, 18, 20, 0, 'JM', 'JM,M', 'Junior Men/ U21 Karla', 1, '');
        CreateClass($TourId, $i++, 18, 20, 1, 'JW', 'JW,W', 'Junior Women/ U21 Kvenna', 1, '');
        CreateClass($TourId, $i++, 16, 17, 0, 'CM', 'CM,JM,M', 'Cadet Men/ U18 Karla', 1, '');
        CreateClass($TourId, $i++, 16, 17, 1, 'CW', 'CW,JW,W', 'Cadet Women/ U18 Kvenna', 1, '');
        CreateClass($TourId, $i++, 1, 15, 0, 'NM', 'NM,CM,JM,M', 'Nordic/ Men U16 Karla', 1, '');
        CreateClass($TourId, $i++, 1, 15, 1, 'NW', 'NW,CW,JW,W', 'Nordic/ Women U16 Kvenna', 1, '');
		}

	if ($SubRule==2) { // "Only Adult Classes" Bara opinn flokkur COMPLETE
		CreateClass($TourId, $i++, 1, 99, 0, 'M', 'M', 'Men/ Karla', 1, '');
		CreateClass($TourId, $i++, 1, 99, 1, 'W', 'W', 'Women/ Kvenna', 1, '');
		}

    if ($SubRule==3) { // "All-in-one class" Bara opinn flokkur UNISEX COMPLETE
        CreateClass($TourId, $i++, 1, 99, -1, 'U', 'U', 'Universal/ Bæði Kyn');
		}
	
	if ($SubRule==4) { // "EVERY CLASSES" Allir aldurflokkar UNISEX - YOUTH SERIES
		CreateClass($TourId, $i++, 18, 20, 0, 'JM', 'JM', 'U21 Karla', 1, '');
        CreateClass($TourId, $i++, 18, 20, 1, 'JW', 'JW', 'U21 Kvenna', 1, '');
		CreateClass($TourId, $i++, 16, 17, 0, 'CM', 'CM,JM', 'U18 Karla', 1, '');
        CreateClass($TourId, $i++, 16, 17, 1, 'CW', 'CW,JW', 'U18 Kvenna', 1, '');
        CreateClass($TourId, $i++, 14, 15, 0, 'NM', 'NM,CM,JM', 'U16 Karla', 1, '');
        CreateClass($TourId, $i++, 14, 15, 1, 'NW', 'NW,CW,JW', 'U16 Kvenna', 1, '');
		CreateClass($TourId, $i++, 1, 13, 0, 'KM', 'KM,NM,CM,JM', 'U14 Karla', 1, '');
        CreateClass($TourId, $i++, 1, 13, 1, 'KW', 'KW,NW,CW,JW', 'U14 Kvenna', 1, '');
		}
		
	if ($SubRule==5) { // "Set Kids classes" ÓKLÁRAÐ EN VERÐUR MASTERS ÞEGAR ÞAÐ ER BÚIÐ
        CreateClass($TourId, $i++, 30, 39, 0, '3M', '3M', '30+ Men / 30+ Karla', 1, '');
        CreateClass($TourId, $i++, 30, 39, 1, '3W', '3W', '30+ Women/ 30+ Kvenna', 1, '');
        CreateClass($TourId, $i++, 40, 49, 0, '4M', '4M', '40+ Men/ 40+ Karla', 1, '');
        CreateClass($TourId, $i++, 40, 49, 1, '4W', '4W', '40+ Women/ 40+ Kvenna', 1, '');
        CreateClass($TourId, $i++, 50, 59, 0, '5M', '5M', '50+ Men Masters / 50+ Karla', 1, '');
        CreateClass($TourId, $i++, 50, 59, 1, '5W', '5W', '50+ Women Masters / 50+ Kvenna', 1, '');
		CreateClass($TourId, $i++, 60, 69, 0, '6M', '6M', '60+ Men Masters / 60+ Karla', 1, '');
        CreateClass($TourId, $i++, 60, 69, 1, '6W', '6W', '60+ Women Masters / 60+ Kvenna', 1, '');
		CreateClass($TourId, $i++, 70, 99, 0, '7M', '7M', '70+ Men Masters / 70+ Karla', 1, '');
        CreateClass($TourId, $i++, 70, 99, 1, '7W', '7W', '70+ Women Masters / 70+ Kvenna', 1, '');
		}
}

function CreateStandardSubClasses($TourId) { //Undirflokkar. Þessi partur er ekki notaður COMPLETE
	// Hérna seturðu inn subclasses/undirflokka
}

function CreateStandardEvents($TourId, $TourType, $SubRule, $Outdoor=true) { //Útsláttarkeppni. COMPLETE
    //StandardEvents = Eliminations/Matches Útsláttarkeppni uppsetning, útskýring er fyrir ofan hvern hluta um hvað sá hluti gerir

{	// Hér fyrir neðan er skilgreining á því hvaða skífustærðir og fjarlægðir eru notaðar í ÚTSLÁTTARKEPPNI fyrir mismunandi flokka.
	// Senior - Opinn flokkur Útsláttarkeppni Skífustærðir og Fjarlægðir
    $TargetR=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetC=($Outdoor ? TGT_OUT_5_big10 : TGT_IND_1_small10);
    $TargetB=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
	$TargetL=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
	$TargetI=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetSizeR=($Outdoor ? 122 : 40);
    $TargetSizeC=($Outdoor ? 80 : 40);
    $TargetSizeB=($Outdoor ? 122 : 40);
	$TargetSizeL=($Outdoor ? 122 : 40);
	$TargetSizeI=($Outdoor ? 122 : 40);
    $DistanceR=($Outdoor ? 70 : 18);
    $DistanceC=($Outdoor ? 50 : 18);
    $DistanceB=($Outdoor ? 50 : 18);
	$DistanceL=($Outdoor ? 30 : 18);
	$DistanceI=($Outdoor ? 30 : 18);

    // Master - E50 Útsláttarkeppni Skífustærðir og Fjarlægðir
    $TargetRM=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetCM=($Outdoor ? TGT_OUT_5_big10 : TGT_IND_1_small10);
	$TargetBM=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
	$TargetLM=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
	$TargetIM=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetSizeRM=($Outdoor ? 122 : 40);
    $TargetSizeCM=($Outdoor ? 80 : 40);
	$TargetSizeBM=($Outdoor ? 122 : 40);
	$TargetSizeLM=($Outdoor ? 122 : 40);
	$TargetSizeIM=($Outdoor ? 122 : 40);
    $DistanceRM=($Outdoor ? 60 : 18);
    $DistanceCM=($Outdoor ? 50 : 18);
	$DistanceBM=($Outdoor ? 50 : 18);
	$DistanceLM=($Outdoor ? 30 : 18);
	$DistanceIM=($Outdoor ? 30 : 18);

    // Junior - U21 Útsláttarkeppni Skífustærðir og Fjarlægðir
    $TargetRJ=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetCJ=($Outdoor ? TGT_OUT_5_big10 : TGT_IND_1_small10);
	$TargetBJ=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
	$TargetLJ=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
	$TargetIJ=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetSizeRJ=($Outdoor ? 122 : 40);
    $TargetSizeCJ=($Outdoor ? 80 : 40);
	$TargetSizeBJ=($Outdoor ? 122 : 40);
	$TargetSizeLJ=($Outdoor ? 122 : 40);
	$TargetSizeIJ=($Outdoor ? 122 : 40);
    $DistanceRJ=($Outdoor ? 70 : 18);
    $DistanceCJ=($Outdoor ? 50 : 18);
	$DistanceBJ=($Outdoor ? 50 : 18);
	$DistanceLJ=($Outdoor ? 30 : 18);
	$DistanceIJ=($Outdoor ? 30 : 18);

    // Cadet - U18 Útsláttarkeppni Skífustærðir og Fjarlægðir
    $TargetRC=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetCC=($Outdoor ? TGT_OUT_5_big10 : TGT_IND_1_small10);
	$TargetBC=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
	$TargetLC=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
	$TargetIC=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetSizeRC=($Outdoor ? 122 : 60);
    $TargetSizeCC=($Outdoor ? 80 : 60);
	$TargetSizeBC=($Outdoor ? 122 : 60);
	$TargetSizeLC=($Outdoor ? 122 : 60);
	$TargetSizeIC=($Outdoor ? 122 : 60);
    $DistanceRC=($Outdoor ? 60 : 18);
    $DistanceCC=($Outdoor ? 50 : 18);
	$DistanceBC=($Outdoor ? 40 : 18);
	$DistanceLC=($Outdoor ? 30 : 18);
	$DistanceIC=($Outdoor ? 30 : 18);

    // Nordic - U16 Útsláttarkeppni Skífustærðir og Fjarlægðir
    $TargetRN=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetCN=($Outdoor ? TGT_OUT_5_big10 : TGT_IND_1_small10);
	$TargetBN=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
	$TargetLN=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
	$TargetIN=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetSizeRN=($Outdoor ? 122 : 60);
    $TargetSizeCN=($Outdoor ? 80 : 60);
	$TargetSizeBN=($Outdoor ? 122 : 60);
	$TargetSizeLN=($Outdoor ? 122 : 60);
	$TargetSizeIN=($Outdoor ? 122 : 60);
    $DistanceRN=($Outdoor ? 40 : 12);
    $DistanceCN=($Outdoor ? 30 : 12);
	$DistanceBN=($Outdoor ? 30 : 12);
	$DistanceLN=($Outdoor ? 30 : 12);
	$DistanceIN=($Outdoor ? 30 : 12);
	
	// U14 Útsláttarkeppni Skífustærðir og Fjarlægðir
    $TargetRK=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetCK=($Outdoor ? TGT_OUT_5_big10 : TGT_IND_1_small10);
	$TargetBK=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
	$TargetLK=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
	$TargetIK=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetSizeRK=($Outdoor ? 122 : 60);
    $TargetSizeCK=($Outdoor ? 80 : 60);
	$TargetSizeBK=($Outdoor ? 122 : 60);
	$TargetSizeLK=($Outdoor ? 122 : 60);
	$TargetSizeIK=($Outdoor ? 122 : 60);
    $DistanceRK=($Outdoor ? 20 : 6);
    $DistanceCK=($Outdoor ? 20 : 6);
	$DistanceBK=($Outdoor ? 20 : 6);
	$DistanceLK=($Outdoor ? 20 : 6);
	$DistanceIK=($Outdoor ? 20 : 6);
	
	// Master 30+ - 30+ Útsláttarkeppni Skífustærðir og Fjarlægðir
    $TargetR3=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetC3=($Outdoor ? TGT_OUT_5_big10 : TGT_IND_1_small10);
    $TargetB3=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
	$TargetL3=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
	$TargetI3=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetSizeR3=($Outdoor ? 122 : 40);
    $TargetSizeC3=($Outdoor ? 80 : 40);
    $TargetSizeB3=($Outdoor ? 122 : 40);
	$TargetSizeL3=($Outdoor ? 122 : 40);
	$TargetSizeI3=($Outdoor ? 122 : 40);
    $DistanceR3=($Outdoor ? 70 : 18);
    $DistanceC3=($Outdoor ? 50 : 18);
    $DistanceB3=($Outdoor ? 50 : 18);
	$DistanceL3=($Outdoor ? 30 : 18);
	$DistanceI3=($Outdoor ? 30 : 18);
	
	// Master 40+ - 40+ Útsláttarkeppni Skífustærðir og Fjarlægðir
    $TargetR4=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetC4=($Outdoor ? TGT_OUT_5_big10 : TGT_IND_1_small10);
    $TargetB4=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
	$TargetL4=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
	$TargetI4=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetSizeR4=($Outdoor ? 122 : 40);
    $TargetSizeC4=($Outdoor ? 80 : 40);
    $TargetSizeB4=($Outdoor ? 122 : 40);
	$TargetSizeL4=($Outdoor ? 122 : 40);
	$TargetSizeI4=($Outdoor ? 122 : 40);
    $DistanceR4=($Outdoor ? 70 : 18);
    $DistanceC4=($Outdoor ? 50 : 18);
    $DistanceB4=($Outdoor ? 50 : 18);
	$DistanceL4=($Outdoor ? 30 : 18);
	$DistanceI4=($Outdoor ? 30 : 18);
	
	// Master 50+ - 50+ Útsláttarkeppni Skífustærðir og Fjarlægðir
    $TargetR5=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetC5=($Outdoor ? TGT_OUT_5_big10 : TGT_IND_1_small10);
    $TargetB5=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
	$TargetL5=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
	$TargetI5=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetSizeR5=($Outdoor ? 122 : 40);
    $TargetSizeC5=($Outdoor ? 80 : 40);
    $TargetSizeB5=($Outdoor ? 122 : 40);
	$TargetSizeL5=($Outdoor ? 122 : 40);
	$TargetSizeI5=($Outdoor ? 122 : 40);
    $DistanceR5=($Outdoor ? 60 : 18);
    $DistanceC5=($Outdoor ? 50 : 18);
    $DistanceB5=($Outdoor ? 50 : 18);
	$DistanceL5=($Outdoor ? 30 : 18);
	$DistanceI5=($Outdoor ? 30 : 18);

	// Master 60+ - 60+ Útsláttarkeppni Skífustærðir og Fjarlægðir
    $TargetR6=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetC6=($Outdoor ? TGT_OUT_5_big10 : TGT_IND_1_small10);
    $TargetB6=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
	$TargetL6=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
	$TargetI6=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetSizeR6=($Outdoor ? 122 : 40);
    $TargetSizeC6=($Outdoor ? 80 : 40);
    $TargetSizeB6=($Outdoor ? 122 : 40);
	$TargetSizeL6=($Outdoor ? 122 : 40);
	$TargetSizeI6=($Outdoor ? 122 : 40);
    $DistanceR6=($Outdoor ? 60 : 18);
    $DistanceC6=($Outdoor ? 50 : 18);
    $DistanceB6=($Outdoor ? 50 : 18);
	$DistanceL6=($Outdoor ? 30 : 18);
	$DistanceI6=($Outdoor ? 30 : 18);
	
	// Master 70+ - 70+ Útsláttarkeppni Skífustærðir og Fjarlægðir
    $TargetR7=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetC7=($Outdoor ? TGT_OUT_5_big10 : TGT_IND_1_small10);
    $TargetB7=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
	$TargetL7=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
	$TargetI7=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetSizeR7=($Outdoor ? 122 : 40);
    $TargetSizeC7=($Outdoor ? 80 : 40);
    $TargetSizeB7=($Outdoor ? 122 : 40);
	$TargetSizeL7=($Outdoor ? 122 : 40);
	$TargetSizeI7=($Outdoor ? 122 : 40);
    $DistanceR7=($Outdoor ? 60 : 18);
    $DistanceC7=($Outdoor ? 50 : 18);
    $DistanceB7=($Outdoor ? 50 : 18);
	$DistanceL7=($Outdoor ? 30 : 18);
	$DistanceI7=($Outdoor ? 30 : 18);
}
	
	// $Phase stillir globally í hvaða útslætti útsláttarkeppni byrjar 0=engin útsláttur "---" 1=semi finals, 2=quarter finals og svo framvegis.
	// Ef þú vilt stilla suma útslætti til að byrja á ákveðnum stað þarftu að finna þann útslátt og bæta við t.d =0 fyrir aftan $Phase í útsláttarlínuni fyrir þann flokk
    $Phase=0; 
    $i=0;

	// Einstaklinga útslættir
	if ($SubRule==1) { // "Championship" Allir aldursflokkar útsláttarkeppni EINSTAKLINGA COMPLETE
		// Sveigbogi
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'RM',  'Recurve Men/ Sveigbogi Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'RW',  'Recurve Women/ Sveigbogi Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRM, 5, 3, 1, 5, 3, 1, 'RMM',  'Recurve Master Men/ Sveigbogi E50 Karla)', 1, 240, 255, 0, 0, '', '', $TargetSizeRM, $DistanceRM);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRM, 5, 3, 1, 5, 3, 1, 'RMW',  'Recurve Master Women/ Sveigbogi E50 Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeRM, $DistanceRM);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRJ, 5, 3, 1, 5, 3, 1, 'RJM',  'Recurve Junior Men/ Sveigbogi U21 Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeRJ, $DistanceRJ);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRJ, 5, 3, 1, 5, 3, 1, 'RJW',  'Recurve Junior Women/ Sveigbogi U21 Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeRJ, $DistanceRJ);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRC, 5, 3, 1, 5, 3, 1, 'RCM',  'Recurve Cadet Men/ Sveigbogi U18 Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeRC, $DistanceRC);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRC, 5, 3, 1, 5, 3, 1, 'RCW',  'Recurve Cadet Women/ Sveigbogi U18 Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeRC, $DistanceRC);
	    CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRN, 5, 3, 1, 5, 3, 1, 'RNM',  'Recurve Nordic Men/ Sveigbogi U16 Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeRN, $DistanceRN);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRN, 5, 3, 1, 5, 3, 1, 'RNW',  'Recurve Nordic Women/ Sveigbogi U16 Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeRN, $DistanceRN);
		// Trissubogi
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC, 5, 3, 1, 5, 3, 1, 'CM',  'Compound Men/ Trissubogi Karla', 0, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceC);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC, 5, 3, 1, 5, 3, 1, 'CW',  'Compound Women/ Trissubogi Kvenna', 0, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceC);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCM, 5, 3, 1, 5, 3, 1, 'CMM', 'Compound Master Men/ Trissubogi E50 Karla', 0, 240, 255, 0, 0, '', '', $TargetSizeCM, $DistanceCM);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCM, 5, 3, 1, 5, 3, 1, 'CMW', 'Compound Master Women/ Trissubogi E50 Kvenna', 0, 240, 255, 0, 0, '', '', $TargetSizeCM, $DistanceCM);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCJ, 5, 3, 1, 5, 3, 1, 'CJM', 'Compound Junior Men/ Trissubogi U21 Karla', 0, 240, 255, 0, 0, '', '', $TargetSizeCJ, $DistanceCJ);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCJ, 5, 3, 1, 5, 3, 1, 'CJW', 'Compound Junior Women/ Trissubogi U21 Kvenna', 0, 240, 255, 0, 0, '', '', $TargetSizeCJ, $DistanceCJ);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCC, 5, 3, 1, 5, 3, 1, 'CCM', 'Compound Cadet Men/ Trissubogi U18 Karla', 0, 240, 255, 0, 0, '', '', $TargetSizeCC, $DistanceCC);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCC, 5, 3, 1, 5, 3, 1, 'CCW', 'Compound Cadet Women/ Trissubogi U18 Kvenna', 0, 240, 255, 0, 0, '', '', $TargetSizeCC, $DistanceCC);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCN, 5, 3, 1, 5, 3, 1, 'CNM', 'Compound Nordic Men/ Trissubogi U16 Karla', 0, 240, 255, 0, 0, '', '', $TargetSizeCN, $DistanceCN);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCN, 5, 3, 1, 5, 3, 1, 'CNW', 'Compound Nordic Women/ Trissubogi U16 Kvenna', 0, 240, 255, 0, 0, '', '', $TargetSizeCN, $DistanceCN);
		// Berbogi
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB, 5, 3, 1, 5, 3, 1, 'BM',  'Barebow Men/ Berbogi Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceB);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB, 5, 3, 1, 5, 3, 1, 'BW',  'Barebow Women/ Berbogi Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceB);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBM, 5, 3, 1, 5, 3, 1, 'BMM', 'Barebow Master Men/ Berbogi E50 Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeBM, $DistanceBM);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBM, 5, 3, 1, 5, 3, 1, 'BMW', 'Barebow Master Women/ Berbogi E50 Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeBM, $DistanceBM);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBJ, 5, 3, 1, 5, 3, 1, 'BJM', 'Barebow Junior Men/ Berbogi U21 Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeBJ, $DistanceBJ);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBJ, 5, 3, 1, 5, 3, 1, 'BJW', 'Barebow Junior Women/ Berbogi U21 Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeBJ, $DistanceBJ);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBC, 5, 3, 1, 5, 3, 1, 'BCM', 'Barebow Cadet Men/ Berbogi U18 Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeBC, $DistanceBC);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBC, 5, 3, 1, 5, 3, 1, 'BCW', 'Barebow Cadet Women/ Berbogi U18 Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeBC, $DistanceBC);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBN, 5, 3, 1, 5, 3, 1, 'BNM', 'Barebow Nordic Men/ Berbogi U16 Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeBN, $DistanceBN);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBN, 5, 3, 1, 5, 3, 1, 'BNW', 'Barebow Nordic Women/ Berbogi U16 Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeBN, $DistanceBN);		
		}

	if ($SubRule==2) { //"Only Adult Classes" Bara opinn flokkur útsláttarkeppni EINSTAKLINGA COMPLETE
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'RM',  'Recurve Men/ Sveigbogi Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'RW',  'Recurve Women/ Sveigbogi Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC, 5, 3, 1, 5, 3, 1, 'CM',  'Compound Men/ Trissubogi Karla', 0, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceC);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC, 5, 3, 1, 5, 3, 1, 'CW',  'Compound Women/ Trissubogi Kvenna', 0, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceC);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB, 5, 3, 1, 5, 3, 1, 'BM',  'Barebow Men/ Berbogi Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceB);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB, 5, 3, 1, 5, 3, 1, 'BW',  'Barebow Women/ Berbogi Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceB);
		}

    if ($SubRule==3) { // "All-in-one class" Bara opinn flokkur UNISEX ÚTSLÁTTARKEPPNI EINSTAKLINGA COMPLETE
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'R',  'Recurve/ Sveigbogi', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);    
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC, 5, 3, 1, 5, 3, 1, 'C',  'Compound/ Trissubogi', 0, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceC);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB, 5, 3, 1, 5, 3, 1, 'B',  'Barebow/ Berbogi', 0, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceB);
		}
		
	if ($SubRule==4) { // "Every Classes" Allir aldurflokkar UNISEX ÚTSLÁTTARKEPPNI EINSTAKLINGA YOUTH SERIES COMPLETE
		// Sveigbogi
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRJ, 5, 3, 1, 5, 3, 1, 'RJM',  'Recurve Junior Men/ Sveigbogi U21 Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeRJ, $DistanceRJ);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRJ, 5, 3, 1, 5, 3, 1, 'RJW',  'Recurve Junior Women/ Sveigbogi U21 Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeRJ, $DistanceRJ);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRC, 5, 3, 1, 5, 3, 1, 'RCM',  'Recurve Cadet Men/ Sveigbogi U18 Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeRC, $DistanceRC);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRC, 5, 3, 1, 5, 3, 1, 'RCW',  'Recurve Cadet Women/ Sveigbogi U18 Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeRC, $DistanceRC);
	    CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRN, 5, 3, 1, 5, 3, 1, 'RNM',  'Recurve Nordic Men/ Sveigbogi U16 Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeRN, $DistanceRN);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRN, 5, 3, 1, 5, 3, 1, 'RNW',  'Recurve Nordic Women/ Sveigbogi U16 Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeRN, $DistanceRN);
	    CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRK, 5, 3, 1, 5, 3, 1, 'RKM',  'Sveigbogi U14 Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeRK, $DistanceRK);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRK, 5, 3, 1, 5, 3, 1, 'RKW',  'Sveigbogi U14 Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeRK, $DistanceRK);	
		// Trissubogi
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCJ, 5, 3, 1, 5, 3, 1, 'CJM', 'Compound Junior Men/ Trissubogi U21 Karla', 0, 240, 255, 0, 0, '', '', $TargetSizeCJ, $DistanceCJ);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCJ, 5, 3, 1, 5, 3, 1, 'CJW', 'Compound Junior Women/ Trissubogi U21 Kvenna', 0, 240, 255, 0, 0, '', '', $TargetSizeCJ, $DistanceCJ);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCC, 5, 3, 1, 5, 3, 1, 'CCM', 'Compound Cadet Men/ Trissubogi U18 Karla', 0, 240, 255, 0, 0, '', '', $TargetSizeCC, $DistanceCC);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCC, 5, 3, 1, 5, 3, 1, 'CCW', 'Compound Cadet Women/ Trissubogi U18 Kvenna', 0, 240, 255, 0, 0, '', '', $TargetSizeCC, $DistanceCC);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCN, 5, 3, 1, 5, 3, 1, 'CNM', 'Compound Nordic Men/ Trissubogi U16 Karla', 0, 240, 255, 0, 0, '', '', $TargetSizeCN, $DistanceCN);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCN, 5, 3, 1, 5, 3, 1, 'CNW', 'Compound Nordic Women/ Trissubogi U16 Kvenna', 0, 240, 255, 0, 0, '', '', $TargetSizeCN, $DistanceCN);
	    CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCK, 5, 3, 1, 5, 3, 1, 'CKM',  'Trissubogi U14 Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeCK, $DistanceCK);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCK, 5, 3, 1, 5, 3, 1, 'CKW',  'Trissubogi U14 Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeCK, $DistanceCK);
		// Berbogi
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBJ, 5, 3, 1, 5, 3, 1, 'BJM', 'Barebow Junior Men/ Berbogi U21 Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeBJ, $DistanceBJ);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBJ, 5, 3, 1, 5, 3, 1, 'BJW', 'Barebow Junior Women/ Berbogi U21 Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeBJ, $DistanceBJ);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBC, 5, 3, 1, 5, 3, 1, 'BCM', 'Barebow Cadet Men/ Berbogi U18 Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeBC, $DistanceBC);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBC, 5, 3, 1, 5, 3, 1, 'BCW', 'Barebow Cadet Women/ Berbogi U18 Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeBC, $DistanceBC);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBN, 5, 3, 1, 5, 3, 1, 'BNM', 'Barebow Nordic Men/ Berbogi U16 Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeBN, $DistanceBN);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBN, 5, 3, 1, 5, 3, 1, 'BNW', 'Barebow Nordic Women/ Berbogi U16 Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeBN, $DistanceBN);
	    CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBK, 5, 3, 1, 5, 3, 1, 'BKM',  'Berbogi U14 Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeBK, $DistanceBK);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBK, 5, 3, 1, 5, 3, 1, 'BKW',  'Berbogi U14 Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeBK, $DistanceBK);
		}

	if ($SubRule==5) { // "Set Kids classes" ÓKLÁRAÐ EN VERÐUR MASTERS ÞEGAR ÞAÐ ER BÚIÐ
		// Sveigbogi
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR3, 5, 3, 1, 5, 3, 1, 'R3M', 'Recurve 30+ Men', 1, 240, 255, 0, 0, '', '', $TargetSizeR3, $DistanceR3);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR3, 5, 3, 1, 5, 3, 1, 'R3W', 'Recurve 30+ Women', 1, 240, 255, 0, 0, '', '', $TargetSizeR3, $DistanceR3);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR4, 5, 3, 1, 5, 3, 1, 'R4M', 'Recurve 40+ Men', 1, 240, 255, 0, 0, '', '', $TargetSizeR4, $DistanceR4);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR4, 5, 3, 1, 5, 3, 1, 'R4W', 'Recurve 40+ Women', 1, 240, 255, 0, 0, '', '', $TargetSizeR4, $DistanceR4);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR5, 5, 3, 1, 5, 3, 1, 'R5M', 'Recurve 50+ Men)', 1, 240, 255, 0, 0, '', '', $TargetSizeR5, $DistanceR5);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR5, 5, 3, 1, 5, 3, 1, 'R5W', 'Recurve 50+ Women', 1, 240, 255, 0, 0, '', '', $TargetSizeR5, $DistanceR5);
	    CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR6, 5, 3, 1, 5, 3, 1, 'R6M', 'Recurve 60+ Men', 1, 240, 255, 0, 0, '', '', $TargetSizeR6, $DistanceR6);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR6, 5, 3, 1, 5, 3, 1, 'R6W', 'Recurve 60+ Women', 1, 240, 255, 0, 0, '', '', $TargetSizeR6, $DistanceR6);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR7, 5, 3, 1, 5, 3, 1, 'R7M', 'Recurve 70+ Men', 1, 240, 255, 0, 0, '', '', $TargetSizeR7, $DistanceR7);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR7, 5, 3, 1, 5, 3, 1, 'R7W', 'Recurve 70+ Women', 1, 240, 255, 0, 0, '', '', $TargetSizeR7, $DistanceR7);
 		// Trissubogi
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC3, 5, 3, 1, 5, 3, 1, 'C3M', 'Compound 30+ Men', 0, 240, 255, 0, 0, '', '', $TargetSizeC3, $DistanceC3);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC3, 5, 3, 1, 5, 3, 1, 'C3W', 'Compound 30+ Women', 0, 240, 255, 0, 0, '', '', $TargetSizeC3, $DistanceC3);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC4, 5, 3, 1, 5, 3, 1, 'C4M', 'Compound 40+ Men', 0, 240, 255, 0, 0, '', '', $TargetSizeC4, $DistanceC4);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC4, 5, 3, 1, 5, 3, 1, 'C4W', 'Compound 40+ Women', 0, 240, 255, 0, 0, '', '', $TargetSizeC4, $DistanceC4);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC5, 5, 3, 1, 5, 3, 1, 'C5M', 'Compound 50+ Men', 0, 240, 255, 0, 0, '', '', $TargetSizeC5, $DistanceC5);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC5, 5, 3, 1, 5, 3, 1, 'C5W', 'Compound 50+ Women', 0, 240, 255, 0, 0, '', '', $TargetSizeC5, $DistanceC5);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC6, 5, 3, 1, 5, 3, 1, 'C6M', 'Compound 60+ Men', 0, 240, 255, 0, 0, '', '', $TargetSizeC6, $DistanceC6);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC6, 5, 3, 1, 5, 3, 1, 'C6W', 'Compound 60+ Women', 0, 240, 255, 0, 0, '', '', $TargetSizeC6, $DistanceC6);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC7, 5, 3, 1, 5, 3, 1, 'C7M', 'Compound 70+ Men', 0, 240, 255, 0, 0, '', '', $TargetSizeC7, $DistanceC7);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC7, 5, 3, 1, 5, 3, 1, 'C7W', 'Compound 70+ Women', 0, 240, 255, 0, 0, '', '', $TargetSizeC7, $DistanceC7);
		// Berbogi
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB3, 5, 3, 1, 5, 3, 1, 'B3M', 'Barebow 30+ Men', 1, 240, 255, 0, 0, '', '', $TargetSizeB3, $DistanceB3);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB3, 5, 3, 1, 5, 3, 1, 'B3W', 'Barebow 30+ Women', 1, 240, 255, 0, 0, '', '', $TargetSizeB3, $DistanceB3);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB4, 5, 3, 1, 5, 3, 1, 'B4M', 'Barebow 40+ Men', 1, 240, 255, 0, 0, '', '', $TargetSizeB4, $DistanceB4);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB4, 5, 3, 1, 5, 3, 1, 'B4W', 'Barebow 40+ Women', 1, 240, 255, 0, 0, '', '', $TargetSizeB4, $DistanceB4);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB5, 5, 3, 1, 5, 3, 1, 'B5M', 'Barebow 50+ Men', 1, 240, 255, 0, 0, '', '', $TargetSizeB5, $DistanceB5);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB5, 5, 3, 1, 5, 3, 1, 'B5W', 'Barebow 50+ Women', 1, 240, 255, 0, 0, '', '', $TargetSizeB5, $DistanceB5);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB6, 5, 3, 1, 5, 3, 1, 'B6M', 'Barebow 60+ Men', 1, 240, 255, 0, 0, '', '', $TargetSizeB6, $DistanceB6);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB6, 5, 3, 1, 5, 3, 1, 'B6W', 'Barebow 60+ Women', 1, 240, 255, 0, 0, '', '', $TargetSizeB6, $DistanceB6);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB7, 5, 3, 1, 5, 3, 1, 'B7M', 'Barebow 70+ Men', 1, 240, 255, 0, 0, '', '', $TargetSizeB7, $DistanceB7);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB7, 5, 3, 1, 5, 3, 1, 'B7W', 'Barebow 70+ Women', 1, 240, 255, 0, 0, '', '', $TargetSizeB7, $DistanceB7);
		}

	// LIÐA útslættir
	if ($SubRule==1) { // "Championship" Allir aldursflokkar útsláttarkeppni LIÐA COMPLETE
		// Til upplýsinga tölurnar á eftir $Targetx standa fyrir "fjöldi umferða","fjöldi örva","fjöldi örva bráðabani" og svo sömu tölur enduteknar.
		// Sveigbogi
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetR, 4, 6, 3, 4, 6, 3, 'RM',  'Recurve Men/ Sveigbogi Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetR, 4, 6, 3, 4, 6, 3, 'RW',  'Recurve Women/ Sveigbogi Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRM, 4, 4, 2, 4, 4, 2, 'RMM',  'Recurve Master Men/ Sveigbogi E50 Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeRM, $DistanceRM);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRM, 4, 4, 2, 4, 4, 2, 'RMW',  'Recurve Master Women/ Sveigbogi E50 Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeRM, $DistanceRM);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRJ, 4, 4, 2, 4, 4, 2, 'RJM',  'Recurve Junior Men/ Sveigbogi U21 Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeRJ, $DistanceRJ);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRJ, 4, 4, 2, 4, 4, 2, 'RJW',  'Recurve Junior Women/ Sveigbogi U21 Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeRJ, $DistanceRJ);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRC, 4, 4, 2, 4, 4, 2, 'RCM',  'Recurve Cadet Men/ Sveigbogi U18 Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeRC, $DistanceRC);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRC, 4, 4, 2, 4, 4, 2, 'RCW',  'Recurve Cadet Women/ Sveigbogi U18 Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeRC, $DistanceRC);
	    CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRN, 4, 4, 2, 4, 4, 2, 'RNM',  'Recurve Nordic Men/ Sveigbogi U16 Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeRN, $DistanceRN);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRN, 4, 4, 2, 4, 4, 2, 'RNW',  'Recurve Nordic Women/ Sveigbogi U16 Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeRN, $DistanceRN);
		// Trissubogi
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetC, 4, 6, 3, 4, 6, 3, 'CM',  'Compound Men/ Trissubogi Karla', 0, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceC);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetC, 4, 6, 3, 4, 6, 3, 'CW',  'Compound Women/ Trissubogi Kvenna', 0, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceC);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCM, 4, 4, 2, 4, 4, 2, 'CMM', 'Compound Master Men/ Trissubogi E50 Karla', 0, 240, 255, 0, 0, '', '', $TargetSizeCM, $DistanceCM);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCM, 4, 4, 2, 4, 4, 2, 'CMW', 'Compound Master Women/ Trissubogi E50 Kvenna', 0, 240, 255, 0, 0, '', '', $TargetSizeCM, $DistanceCM);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCJ, 4, 4, 2, 4, 4, 2, 'CJM', 'Compound Junior Men/ Trissubogi U21 Karla', 0, 240, 255, 0, 0, '', '', $TargetSizeCJ, $DistanceCJ);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCJ, 4, 4, 2, 4, 4, 2, 'CJW', 'Compound Junior Women/ Trissubogi U21 Kvenna', 0, 240, 255, 0, 0, '', '', $TargetSizeCJ, $DistanceCJ);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCC, 4, 4, 2, 4, 4, 2, 'CCM', 'Compound Cadet Men/ Trissubogi U18 Karla', 0, 240, 255, 0, 0, '', '', $TargetSizeCC, $DistanceCC);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCC, 4, 4, 2, 4, 4, 2, 'CCW', 'Compound Cadet Women/ Trissubogi U18 Kvenna', 0, 240, 255, 0, 0, '', '', $TargetSizeCC, $DistanceCC);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCN, 4, 4, 2, 4, 4, 2, 'CNM', 'Compound Nordic Men/ Trissubogi U16 Karla', 0, 240, 255, 0, 0, '', '', $TargetSizeCN, $DistanceCN);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCN, 4, 4, 2, 4, 4, 2, 'CNW', 'Compound Nordic Women/ Trissubogi U16 Kvenna', 0, 240, 255, 0, 0, '', '', $TargetSizeCN, $DistanceCN);
		// Berbogi
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetB, 4, 6, 3, 4, 6, 3, 'BM',  'Barebow Men/ Berbogi Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceB);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetB, 4, 6, 3, 4, 6, 3, 'BW',  'Barebow Women/ Berbogi Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceB);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBM, 4, 4, 2, 4, 4, 2, 'BMM', 'Barebow Master Men/ Berbogi E50 Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeBM, $DistanceBM);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBM, 4, 4, 2, 4, 4, 2, 'BMW', 'Barebow Master Women/ Berbogi E50 Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeBM, $DistanceBM);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBJ, 4, 4, 2, 4, 4, 2, 'BJM', 'Barebow Junior Men/ Berbogi U21 Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeBJ, $DistanceBJ);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBJ, 4, 4, 2, 4, 4, 2, 'BJW', 'Barebow Junior Women/ Berbogi U21 Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeBJ, $DistanceBJ);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBC, 4, 4, 2, 4, 4, 2, 'BCM', 'Barebow Cadet Men/ Berbogi U18 Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeBC, $DistanceBC);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBC, 4, 4, 2, 4, 4, 2, 'BCW', 'Barebow Cadet Women/ Berbogi U18 Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeBC, $DistanceBC);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBN, 4, 4, 2, 4, 4, 2, 'BNM', 'Barebow Nordic Men/ Berbogi U16 Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeBN, $DistanceBN);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBN, 4, 4, 2, 4, 4, 2, 'BNW', 'Barebow Nordic Women/ Berbogi U16 Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeBN, $DistanceBN);
		}
		
	if ($SubRule==2) { //"Only Adult Classes" Bara opinn flokkur útsláttarkeppni LIÐA COMPLETE
		// Sveigbogi
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetR, 4, 6, 3, 4, 6, 3, 'RM', 'Recurve Team Men/ Sveigbogi Liðakeppni Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetR, 4, 6, 3, 4, 6, 3, 'RW', 'Recurve Team Women/ Sveigbogi Liðakeppni Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);
		// Trissubogi
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetC, 4, 6, 3, 4, 6, 3, 'CM', 'Compound Team Men/ Trissubogi Liðakeppni Karla', 0, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceC);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetC, 4, 6, 3, 4, 6, 3, 'CW', 'Compound Team Women/ Trissubogi Liðakeppni Kvenna', 0, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceC);
		// Berbogi
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetB, 4, 6, 3, 4, 6, 3, 'BM', 'Barebow Team Men/ Berbogi Liðakeppni Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceB);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetB, 4, 6, 3, 4, 6, 3, 'BW', 'Barebow Team Women/ Berbogi Liðakeppni Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceB);
		}

	if ($SubRule==4) { // "EVERY CLASSES" YOUTH SERIES útsláttarkeppni LIÐA COMPLETE
		// Til upplýsinga tölurnar á eftir $Targetx standa fyrir "fjöldi umferða","fjöldi örva","fjöldi örva bráðabani" og svo sömu tölur enduteknar.
		// Sveigbogi
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRJ, 4, 4, 2, 4, 4, 2, 'RJM',  'Recurve Junior Men/ Sveigbogi U21 Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeRJ, $DistanceRJ);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRJ, 4, 4, 2, 4, 4, 2, 'RJW',  'Recurve Junior Women/ Sveigbogi U21 Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeRJ, $DistanceRJ);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRC, 4, 4, 2, 4, 4, 2, 'RCM',  'Recurve Cadet Men/ Sveigbogi U18 Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeRC, $DistanceRC);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRC, 4, 4, 2, 4, 4, 2, 'RCW',  'Recurve Cadet Women/ Sveigbogi U18 Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeRC, $DistanceRC);
	    CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRN, 4, 4, 2, 4, 4, 2, 'RNM',  'Recurve Nordic Men/ Sveigbogi U16 Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeRN, $DistanceRN);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRN, 4, 4, 2, 4, 4, 2, 'RNW',  'Recurve Nordic Women/ Sveigbogi U16 Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeRN, $DistanceRN);
	    CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRK, 4, 4, 2, 4, 4, 2, 'RKM',  'Sveigbogi U14 Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeRK, $DistanceRK);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRK, 4, 4, 2, 4, 4, 2, 'RKW',  'Sveigbogi U14 Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeRK, $DistanceRK);
		// Trissubogi
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCJ, 4, 4, 2, 4, 4, 2, 'CJM', 'Compound Junior Men/ Trissubogi U21 Karla', 0, 240, 255, 0, 0, '', '', $TargetSizeCJ, $DistanceCJ);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCJ, 4, 4, 2, 4, 4, 2, 'CJW', 'Compound Junior Women/ Trissubogi U21 Kvenna', 0, 240, 255, 0, 0, '', '', $TargetSizeCJ, $DistanceCJ);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCC, 4, 4, 2, 4, 4, 2, 'CCM', 'Compound Cadet Men/ Trissubogi U18 Karla', 0, 240, 255, 0, 0, '', '', $TargetSizeCC, $DistanceCC);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCC, 4, 4, 2, 4, 4, 2, 'CCW', 'Compound Cadet Women/ Trissubogi U18 Kvenna', 0, 240, 255, 0, 0, '', '', $TargetSizeCC, $DistanceCC);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCN, 4, 4, 2, 4, 4, 2, 'CNM', 'Compound Nordic Men/ Trissubogi U16 Karla', 0, 240, 255, 0, 0, '', '', $TargetSizeCN, $DistanceCN);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCN, 4, 4, 2, 4, 4, 2, 'CNW', 'Compound Nordic Women/ Trissubogi U16 Kvenna', 0, 240, 255, 0, 0, '', '', $TargetSizeCN, $DistanceCN);
	    CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCK, 4, 4, 2, 4, 4, 2, 'CKM',  'Trissubogi U14 Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeCK, $DistanceCK);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCK, 4, 4, 2, 4, 4, 2, 'CKW',  'Trissubogi U14 Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeCK, $DistanceCK);
		// Berbogi
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBJ, 4, 4, 2, 4, 4, 2, 'BJM', 'Barebow Junior Men/ Berbogi U21 Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeBJ, $DistanceBJ);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBJ, 4, 4, 2, 4, 4, 2, 'BJW', 'Barebow Junior Women/ Berbogi U21 Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeBJ, $DistanceBJ);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBC, 4, 4, 2, 4, 4, 2, 'BCM', 'Barebow Cadet Men/ Berbogi U18 Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeBC, $DistanceBC);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBC, 4, 4, 2, 4, 4, 2, 'BCW', 'Barebow Cadet Women/ Berbogi U18 Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeBC, $DistanceBC);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBN, 4, 4, 2, 4, 4, 2, 'BNM', 'Barebow Nordic Men/ Berbogi U16 Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeBN, $DistanceBN);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBN, 4, 4, 2, 4, 4, 2, 'BNW', 'Barebow Nordic Women/ Berbogi U16 Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeBN, $DistanceBN);
	    CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBK, 4, 4, 2, 4, 4, 2, 'BKM',  'Berbogi U14 Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeBK, $DistanceBK);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBK, 4, 4, 2, 4, 4, 2, 'BKW',  'Berbogi U14 Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeBK, $DistanceBK);
		}
		
		
	//if($Outdoor) { // MIXED TEAM Útsláttarkeppni bætist við þegar mót er utandyra
	if ($SubRule==1) { // "Championship" Allir aldursflokkar útsláttarkeppni MIXED TEAM COMPLETE
		// Recurve Mixed Team - Sveigboga Paraliðakeppni útsláttur
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetR, 4, 4, 2, 4, 4, 2, 'RX', 'Recurve Mixed Team/ Sveigbogi Parakeppni', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRJ, 4, 4, 2, 4, 4, 2, 'RJX', 'Recurve Junior Mixed Team/ Sveigbogi Parakeppni U21', 1, 240, 255, 0, 0, '', '', $TargetSizeRJ, $DistanceRJ);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRC, 4, 4, 2, 4, 4, 2, 'RCX', 'Recurve Cadet Mixed Team/ Sveigbogi Parakeppni U18', 1, 240, 255, 0, 0, '', '', $TargetSizeRC, $DistanceRC);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRM, 4, 4, 2, 4, 4, 2, 'RMX', 'Recurve Master Mixed Team/ Sveigbogi Parakeppni E50', 1, 240, 255, 0, 0, '', '', $TargetSizeRM, $DistanceRM);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRN, 4, 4, 2, 4, 4, 2, 'RNX', 'Recurve Nordic Mixed Team/ Sveigbogi Parakeppni U16', 1, 240, 255, 0, 0, '', '', $TargetSizeRN, $DistanceRN);
		// Compound MIXED TEAM Trissuboga Paraliðakeppni útsláttur
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetC, 4, 4, 2, 4, 4, 2, 'CX', 'Compound Mixed Team/ Trissubogi Parakeppni', 0, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceC);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCJ, 4, 4, 2, 4, 4, 2, 'CJX', 'Compound Junior Mixed Team/ Trissubogi Parakeppni U21', 0, 240, 255, 0, 0, '', '', $TargetSizeCJ, $DistanceCJ);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCC, 4, 4, 2, 4, 4, 2, 'CCX', 'Compound Cadet Mixed Team/ Trissubogi Parakeppni U18', 0, 240, 255, 0, 0, '', '', $TargetSizeCC, $DistanceCC);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCM, 4, 4, 2, 4, 4, 2, 'CMX', 'Compound Master Mixed Team/ Trissubogi Parakeppni E50', 0, 240, 255, 0, 0, '', '', $TargetSizeCM, $DistanceCM);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCN, 4, 4, 2, 4, 4, 2, 'CNX', 'Compound Nordic Mixed Team/ Trissubogi Parakeppni U16', 0, 240, 255, 0, 0, '', '', $TargetSizeCN, $DistanceCN);
		// BAREBOW MIXED TEAM Berboga Paraliðakeppni útsláttur
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetB, 4, 4, 2, 4, 4, 2, 'BX', 'Barebow Mixed Team/ Berbogi Parakeppni', 1, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceB);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBJ, 4, 4, 2, 4, 4, 2, 'BJX', 'Barebow Junior Mixed Team/ Berbogi Parakeppni U21', 1, 240, 255, 0, 0, '', '', $TargetSizeBJ, $DistanceBJ);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBC, 4, 4, 2, 4, 4, 2, 'BCX', 'Barebow Cadet Mixed Team/ Berbogi Parakeppni U18', 1, 240, 255, 0, 0, '', '', $TargetSizeBC, $DistanceBC);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBM, 4, 4, 2, 4, 4, 2, 'BMX', 'Barebow Master Mixed Team/ Berbogi Parakeppni E50', 1, 240, 255, 0, 0, '', '', $TargetSizeBM, $DistanceBM);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBN, 4, 4, 2, 4, 4, 2, 'BNX', 'Barebow Nordic Mixed Team/ Berbogi Parakeppni U16', 1, 240, 255, 0, 0, '', '', $TargetSizeBN, $DistanceBN);
		}
		
	if ($SubRule==2) { //"Only Adult Classes" Bara opinn flokkur útsláttarkeppni MIXED TEAM COMPLETE
		// Recurve MIXED TEAM
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetR, 4, 4, 2, 4, 4, 2, 'RX', 'Recurve Mixed Team/ Sveigbogi Parakeppni', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);
		// Compound MIXED TEAM
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetC, 4, 4, 2, 4, 4, 2, 'CX', 'Compound Mixed Team/ Trissubogi Parakeppni', 0, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceC);
		// BAREBOW MIXED TEAM 
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetB, 4, 4, 2, 4, 4, 2, 'BX', 'Barebow Mixed Team/ Berbogi Parakeppni', 1, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceB);
		}
		//}
}

function InsertStandardEvents($TourId, $TourType, $SubRule, $Outdoor=true) { //Tenging undankeppni við útsláttarkeppni. COMPLETE
	//Þetta function tengir Útsláttarkeppni í ákveðnum flokki við Undankeppnina. Takkinn sem maður ýtir á vinstra megin við útsláttarkeppnina í "Individual final setup/manage events" í Ianseo.
	
	//TENGINGAR Í EINSTAKLINGA ÚTSlÆTTI
	if ($SubRule==1) { // "Championship" Allir aldursflokkar útsláttarkeppni EINSTAKLINGA tenging COMPLETE
	// Til upplýsinga fyrsta talan er fjöldi lína í tenginguni sem á að búa til 0=disabled/einstaklingskeppni og önnur talan er fjöldi keppenda. 
	// Þess vegna stendur 1,1 og 2,1 í mixed team "lína 1 = 1 RM, lína 2 = 1 RW" og 1,3 í liða "lína 1 = 3 RM"
		// Recurve Einstaklinga tenging, þeas hver má keppa í hvaða útslætti
		InsertClassEvent($TourId, 0, 1, 'RM',  'R', 'M');
		InsertClassEvent($TourId, 0, 1, 'RW',  'R', 'W');
		InsertClassEvent($TourId, 0, 1, 'RMM',  'R', 'MM');
		InsertClassEvent($TourId, 0, 1, 'RMW',  'R', 'MW');
		InsertClassEvent($TourId, 0, 1, 'RJM',  'R', 'JM');
		InsertClassEvent($TourId, 0, 1, 'RJW',  'R', 'JW');
		InsertClassEvent($TourId, 0, 1, 'RCM',  'R', 'CM');
		InsertClassEvent($TourId, 0, 1, 'RCW',  'R', 'CW');
		InsertClassEvent($TourId, 0, 1, 'RNM',  'R', 'NM');
		InsertClassEvent($TourId, 0, 1, 'RNW',  'R', 'NW');
		InsertClassEvent($TourId, 0, 1, 'RBM',  'R', 'BM');
		InsertClassEvent($TourId, 0, 1, 'RBW',  'R', 'BW');
		// Compound Einstaklinga tenging, þeas hver má keppa í hvaða útslætti
		InsertClassEvent($TourId, 0, 1, 'CM',  'C', 'M');
		InsertClassEvent($TourId, 0, 1, 'CW',  'C', 'W');
		InsertClassEvent($TourId, 0, 1, 'CMM',  'C', 'MM');
		InsertClassEvent($TourId, 0, 1, 'CMW',  'C', 'MW');
		InsertClassEvent($TourId, 0, 1, 'CJM',  'C', 'JM');
		InsertClassEvent($TourId, 0, 1, 'CJW',  'C', 'JW');
		InsertClassEvent($TourId, 0, 1, 'CCM',  'C', 'CM');
		InsertClassEvent($TourId, 0, 1, 'CCW',  'C', 'CW');
		InsertClassEvent($TourId, 0, 1, 'CNM',  'C', 'NM');
		InsertClassEvent($TourId, 0, 1, 'CNW',  'C', 'NW');
		InsertClassEvent($TourId, 0, 1, 'CBM',  'C', 'BM');
		InsertClassEvent($TourId, 0, 1, 'CBW',  'C', 'BW');
		// Barebow Einstaklinga tenging, þeas hver má keppa í hvaða útslætti
		InsertClassEvent($TourId, 0, 1, 'BM',  'B', 'M');
		InsertClassEvent($TourId, 0, 1, 'BW',  'B', 'W');
		InsertClassEvent($TourId, 0, 1, 'BMM',  'B', 'MM');
		InsertClassEvent($TourId, 0, 1, 'BMW',  'B', 'MW');
		InsertClassEvent($TourId, 0, 1, 'BJM',  'B', 'JM');
		InsertClassEvent($TourId, 0, 1, 'BJW',  'B', 'JW');
		InsertClassEvent($TourId, 0, 1, 'BCM',  'B', 'CM');
		InsertClassEvent($TourId, 0, 1, 'BCW',  'B', 'CW');
		InsertClassEvent($TourId, 0, 1, 'BNM',  'B', 'NM');
		InsertClassEvent($TourId, 0, 1, 'BNW',  'B', 'NW');
		InsertClassEvent($TourId, 0, 1, 'BBM',  'B', 'BM');
		InsertClassEvent($TourId, 0, 1, 'BBW',  'B', 'BW');
		}

	if ($SubRule==2) { // "Only Adult Classes" Bara opinn flokkur útsláttarkeppni EINSTAKLINGA tenging COMPLETE
		// Recurve Einstaklinga tenging, þeas hver má keppa í hvaða útslætti
		InsertClassEvent($TourId, 0, 1, 'RM',  'R', 'M');
		InsertClassEvent($TourId, 0, 1, 'RW',  'R', 'W');
		// Compound Einstaklinga tenging, þeas hver má keppa í hvaða útslætti
		InsertClassEvent($TourId, 0, 1, 'CM',  'C', 'M');
		InsertClassEvent($TourId, 0, 1, 'CW',  'C', 'W');
		// Barebow Einstaklinga tenging, þeas hver má keppa í hvaða útslætti
		InsertClassEvent($TourId, 0, 1, 'BM',  'B', 'M');
		InsertClassEvent($TourId, 0, 1, 'BW',  'B', 'W');
		}
	
    if ($SubRule==3) {	// "All-in-one class" Bara opinn flokkur UNISEX ÚTSLÁTTARKEPPNI EINSTAKLINGA TENGING COMPLETE
        InsertClassEvent($TourId, 0, 1, 'R',  'R', 'U');
        InsertClassEvent($TourId, 0, 1, 'C',  'C', 'U');
		InsertClassEvent($TourId, 0, 1, 'B',  'B', 'U');
		}
		
	if ($SubRule==4) {	// "EVERY CLASSES" Ungmenna ÚTSLÁTTARKEPPNI EINSTAKLINGA TENGING COMPLETE
		// Recurve Einstaklinga tenging, þeas hver má keppa í hvaða útslætti
		InsertClassEvent($TourId, 0, 1, 'RJM',  'R', 'JM');
		InsertClassEvent($TourId, 0, 1, 'RJW',  'R', 'JW');
		InsertClassEvent($TourId, 0, 1, 'RCM',  'R', 'CM');
		InsertClassEvent($TourId, 0, 1, 'RCW',  'R', 'CW');
		InsertClassEvent($TourId, 0, 1, 'RNM',  'R', 'NM');
		InsertClassEvent($TourId, 0, 1, 'RNW',  'R', 'NW');
		InsertClassEvent($TourId, 0, 1, 'RKM',  'R', 'KM');
		InsertClassEvent($TourId, 0, 1, 'RKW',  'R', 'KW');
		// Compound Einstaklinga tenging, þeas hver má keppa í hvaða útslætti
		InsertClassEvent($TourId, 0, 1, 'CJM',  'C', 'JM');
		InsertClassEvent($TourId, 0, 1, 'CJW',  'C', 'JW');
		InsertClassEvent($TourId, 0, 1, 'CCM',  'C', 'CM');
		InsertClassEvent($TourId, 0, 1, 'CCW',  'C', 'CW');
		InsertClassEvent($TourId, 0, 1, 'CNM',  'C', 'NM');
		InsertClassEvent($TourId, 0, 1, 'CNW',  'C', 'NW');
		InsertClassEvent($TourId, 0, 1, 'CKM',  'C', 'KM');
		InsertClassEvent($TourId, 0, 1, 'CKW',  'C', 'KW');
		// Barebow Einstaklinga tenging, þeas hver má keppa í hvaða útslætti
		InsertClassEvent($TourId, 0, 1, 'BJM',  'B', 'JM');
		InsertClassEvent($TourId, 0, 1, 'BJW',  'B', 'JW');
		InsertClassEvent($TourId, 0, 1, 'BCM',  'B', 'CM');
		InsertClassEvent($TourId, 0, 1, 'BCW',  'B', 'CW');
		InsertClassEvent($TourId, 0, 1, 'BNM',  'B', 'NM');
		InsertClassEvent($TourId, 0, 1, 'BNW',  'B', 'NW');
		InsertClassEvent($TourId, 0, 1, 'BKM',  'B', 'KM');
		InsertClassEvent($TourId, 0, 1, 'BKW',  'B', 'KW');	
		}
		
	if ($SubRule==5) { // "SET KIDS CLASSES" ÖLDUNGAMÓT ALDURFLOKKAR 30+,40+,50+,ETC EINSTAKLINGA tenging COMPLETE
	// Til upplýsinga fyrsta talan er fjöldi lína í tenginguni sem á að búa til 0=disabled/einstaklingskeppni og önnur talan er fjöldi keppenda. 
	// Þess vegna stendur 1,1 og 2,1 í mixed team "lína 1 = 1 RM, lína 2 = 1 RW" og 1,3 í liða "lína 1 = 3 RM"
		// Recurve Einstaklinga tenging, þeas hver má keppa í hvaða útslætti
		InsertClassEvent($TourId, 0, 1, 'R3M',  'R', '3M');
		InsertClassEvent($TourId, 0, 1, 'R3W',  'R', '3W');
		InsertClassEvent($TourId, 0, 1, 'R4M',  'R', '4M');
		InsertClassEvent($TourId, 0, 1, 'R4W',  'R', '4W');
		InsertClassEvent($TourId, 0, 1, 'R5M',  'R', '5M');
		InsertClassEvent($TourId, 0, 1, 'R5W',  'R', '5W');
		InsertClassEvent($TourId, 0, 1, 'R6M',  'R', '6M');
		InsertClassEvent($TourId, 0, 1, 'R6W',  'R', '6W');
		InsertClassEvent($TourId, 0, 1, 'R7M',  'R', '7M');
		InsertClassEvent($TourId, 0, 1, 'R7W',  'R', '7W');
		// Compound Einstaklinga tenging, þeas hver má keppa í hvaða útslætti
		InsertClassEvent($TourId, 0, 1, 'C3M',  'C', '3M');
		InsertClassEvent($TourId, 0, 1, 'C3W',  'C', '3W');
		InsertClassEvent($TourId, 0, 1, 'C4M',  'C', '4M');
		InsertClassEvent($TourId, 0, 1, 'C4W',  'C', '4W');
		InsertClassEvent($TourId, 0, 1, 'C5M',  'C', '5M');
		InsertClassEvent($TourId, 0, 1, 'C5W',  'C', '5W');
		InsertClassEvent($TourId, 0, 1, 'C6M',  'C', '6M');
		InsertClassEvent($TourId, 0, 1, 'C6W',  'C', '6W');
		InsertClassEvent($TourId, 0, 1, 'C7M',  'C', '7M');
		InsertClassEvent($TourId, 0, 1, 'C7W',  'C', '7W');
		// Barebow Einstaklinga tenging, þeas hver má keppa í hvaða útslætti
		InsertClassEvent($TourId, 0, 1, 'B3M',  'B', '3M');
		InsertClassEvent($TourId, 0, 1, 'B3W',  'B', '3W');
		InsertClassEvent($TourId, 0, 1, 'B4M',  'B', '4M');
		InsertClassEvent($TourId, 0, 1, 'B4W',  'B', '4W');
		InsertClassEvent($TourId, 0, 1, 'B5M',  'B', '5M');
		InsertClassEvent($TourId, 0, 1, 'B5W',  'B', '5W');
		InsertClassEvent($TourId, 0, 1, 'B6M',  'B', '6M');
		InsertClassEvent($TourId, 0, 1, 'B6W',  'B', '6W');
		InsertClassEvent($TourId, 0, 1, 'B7M',  'B', '7M');
		InsertClassEvent($TourId, 0, 1, 'B7W',  'B', '7W');
		}
  
	//TENGINGAR Í LIÐAÚTSLÆTTI
	if ($SubRule==1) { // "Championship" Allir aldursflokkar útsláttarkeppni LIÐA tenging COMPLETE
		//Recurve Team Liðatenging 3 manna standard worldarchery í opnum flokki, 2 manna liðakeppni annarsstaðar
		InsertClassEvent($TourId, 1, 3, 'RM', 'R', 'M');
		InsertClassEvent($TourId, 1, 3, 'RW', 'R', 'W');
		InsertClassEvent($TourId, 1, 2, 'RMM', 'R', 'MM');
		InsertClassEvent($TourId, 1, 2, 'RMW', 'R', 'MW');
		InsertClassEvent($TourId, 1, 2, 'RJM', 'R', 'JM');
		InsertClassEvent($TourId, 1, 2, 'RJW', 'R', 'JW');
		InsertClassEvent($TourId, 1, 2, 'RCM', 'R', 'CM');
		InsertClassEvent($TourId, 1, 2, 'RCW', 'R', 'CW');
		InsertClassEvent($TourId, 1, 2, 'RNM', 'R', 'NM');
		InsertClassEvent($TourId, 1, 2, 'RNW', 'R', 'NW');

		//Compound Team Liðatenging 3 manna standard worldarchery
		InsertClassEvent($TourId, 1, 3, 'CM', 'C', 'M');
		InsertClassEvent($TourId, 1, 3, 'CW', 'C', 'W');
		InsertClassEvent($TourId, 1, 2, 'CMM', 'C', 'MM');
		InsertClassEvent($TourId, 1, 2, 'CMW', 'C', 'MW');
		InsertClassEvent($TourId, 1, 2, 'CJM', 'C', 'JM');
		InsertClassEvent($TourId, 1, 2, 'CJW', 'C', 'JW');
		InsertClassEvent($TourId, 1, 2, 'CCM', 'C', 'CM');
		InsertClassEvent($TourId, 1, 2, 'CCW', 'C', 'CW');
		InsertClassEvent($TourId, 1, 2, 'CNM', 'C', 'NM');
		InsertClassEvent($TourId, 1, 2, 'CNW', 'C', 'NW');
	
		//Barebow Team Liðatenging 3 manna standard worldarchery
		InsertClassEvent($TourId, 1, 3, 'BM', 'B', 'M');
		InsertClassEvent($TourId, 1, 3, 'BW', 'B', 'W');
		InsertClassEvent($TourId, 1, 2, 'BMM', 'B', 'MM');
		InsertClassEvent($TourId, 1, 2, 'BMW', 'B', 'MW');
		InsertClassEvent($TourId, 1, 2, 'BJM', 'B', 'JM');
		InsertClassEvent($TourId, 1, 2, 'BJW', 'B', 'JW');
		InsertClassEvent($TourId, 1, 2, 'BCM', 'B', 'CM');
		InsertClassEvent($TourId, 1, 2, 'BCW', 'B', 'CW');
		InsertClassEvent($TourId, 1, 2, 'BNM', 'B', 'NM');
		InsertClassEvent($TourId, 1, 2, 'BNW', 'B', 'NW');
		}
	
	if ($SubRule==2) { // "Only Adult Classes" Bara opinn flokkur útsláttarkeppni LIÐA tenging COMPLETE
		//Recurve Team Liðatenging 3 manna standard worldarchery
		InsertClassEvent($TourId, 1, 3, 'RM', 'R', 'M');
		InsertClassEvent($TourId, 1, 3, 'RW', 'R', 'W');
		//Compound Team Liðatenging 3 manna standard worldarchery
		InsertClassEvent($TourId, 1, 3, 'CM', 'C', 'M');
		InsertClassEvent($TourId, 1, 3, 'CW', 'C', 'W');
		//Barebow Team Liðatenging 3 manna standard worldarchery
		InsertClassEvent($TourId, 1, 3, 'BM', 'B', 'M');
		InsertClassEvent($TourId, 1, 3, 'BW', 'B', 'W');
		}
				
	if ($SubRule==4) { // "EVERY CLASSES" Ungmenna LIÐA tenging VIRKAR EKKI SKIL EKKI AFHVERJU, ÆTTI AÐ VIRKA
		//Recurve Team Liðatenging 3 manna standard worldarchery
		InsertClassEvent($TourId, 1, 2, 'RJM',  'R', 'JM');
		InsertClassEvent($TourId, 1, 2, 'RJW',  'R', 'JW');
		InsertClassEvent($TourId, 1, 2, 'RCM',  'R', 'CM');
		InsertClassEvent($TourId, 1, 2, 'RCW',  'R', 'CW');
		InsertClassEvent($TourId, 1, 2, 'RNM',  'R', 'NM');
		InsertClassEvent($TourId, 1, 2, 'RNW',  'R', 'NW');
		InsertClassEvent($TourId, 1, 2, 'RKM',  'R', 'KM');
		InsertClassEvent($TourId, 1, 2, 'RKW',  'R', 'KW');
		//Compound Team Liðatenging 3 manna standard worldarchery
		InsertClassEvent($TourId, 1, 2, 'CJM',  'C', 'JM');
		InsertClassEvent($TourId, 1, 2, 'CJW',  'C', 'JW');
		InsertClassEvent($TourId, 1, 2, 'CCM',  'C', 'CM');
		InsertClassEvent($TourId, 1, 2, 'CCW',  'C', 'CW');
		InsertClassEvent($TourId, 1, 2, 'CNM',  'C', 'NM');
		InsertClassEvent($TourId, 1, 2, 'CNW',  'C', 'NW');
		InsertClassEvent($TourId, 1, 2, 'CKM',  'C', 'KM');
		InsertClassEvent($TourId, 1, 2, 'CKW',  'C', 'KW');
		//Barebow Team Liðatenging 3 manna standard worldarchery
		InsertClassEvent($TourId, 1, 2, 'BJM',  'B', 'JM');
		InsertClassEvent($TourId, 1, 2, 'BJW',  'B', 'JW');
		InsertClassEvent($TourId, 1, 2, 'BCM',  'B', 'CM');
		InsertClassEvent($TourId, 1, 2, 'BCW',  'B', 'CW');
		InsertClassEvent($TourId, 1, 2, 'BNM',  'B', 'NM');
		InsertClassEvent($TourId, 1, 2, 'BNW',  'B', 'NW');
		InsertClassEvent($TourId, 1, 2, 'BKM',  'B', 'KM');
		InsertClassEvent($TourId, 1, 2, 'BKW',  'B', 'KW');
		}
		
	
	//if($Outdoor) { // TENGINGAR Í MIXED TEAM ÚTSLÆTTI COMPLETE
	if ($SubRule==1) { // "Championship" Allir aldursflokkar útsláttarkeppni MIXED TEAM tenging COMPLETE
		// Recurve Mixed Team
		InsertClassEvent($TourId, 1, 1, 'RX',  'R', 'M');
		InsertClassEvent($TourId, 2, 1, 'RX',  'R', 'W');
		InsertClassEvent($TourId, 1, 1, 'RMX',  'R', 'MM');
		InsertClassEvent($TourId, 2, 1, 'RMX',  'R', 'MW');
		InsertClassEvent($TourId, 1, 1, 'RJX',  'R', 'JM');
		InsertClassEvent($TourId, 2, 1, 'RJX',  'R', 'JW');
		InsertClassEvent($TourId, 1, 1, 'RCX',  'R', 'CM');
		InsertClassEvent($TourId, 2, 1, 'RCX',  'R', 'CW');
		InsertClassEvent($TourId, 1, 1, 'RNX',  'R', 'NM');
		InsertClassEvent($TourId, 2, 1, 'RNX',  'R', 'NW');
		// Compound Mixed Team
		InsertClassEvent($TourId, 1, 1, 'CX',  'C', 'M');
		InsertClassEvent($TourId, 2, 1, 'CX',  'C', 'W');
		InsertClassEvent($TourId, 1, 1, 'CMX',  'C', 'MM');
		InsertClassEvent($TourId, 2, 1, 'CMX',  'C', 'MW');
		InsertClassEvent($TourId, 1, 1, 'CJX',  'C', 'JM');
		InsertClassEvent($TourId, 2, 1, 'CJX',  'C', 'JW');
		InsertClassEvent($TourId, 1, 1, 'CCX',  'C', 'CM');
		InsertClassEvent($TourId, 2, 1, 'CCX',  'C', 'CW');
		InsertClassEvent($TourId, 1, 1, 'CNX',  'C', 'NM');
		InsertClassEvent($TourId, 2, 1, 'CNX',  'C', 'NW');
		// Barebow Mixed Team
		InsertClassEvent($TourId, 1, 1, 'BX',  'B', 'M');
		InsertClassEvent($TourId, 2, 1, 'BX',  'B', 'W');
		InsertClassEvent($TourId, 1, 1, 'BMX',  'B', 'MM');
		InsertClassEvent($TourId, 2, 1, 'BMX',  'B', 'MW');
		InsertClassEvent($TourId, 1, 1, 'BJX',  'B', 'JM');
		InsertClassEvent($TourId, 2, 1, 'BJX',  'B', 'JW');
		InsertClassEvent($TourId, 1, 1, 'BCX',  'B', 'CM');
		InsertClassEvent($TourId, 2, 1, 'BCX',  'B', 'CW');
		InsertClassEvent($TourId, 1, 1, 'BNX',  'B', 'NM');
		InsertClassEvent($TourId, 2, 1, 'BNX',  'B', 'NW');
		}
		
	if ($SubRule==2) { // "Only Adult Classes" Bara opinn flokkur útsláttarkeppni MIXED TEAM tenging COMPLETE
		// Recurve Mixed Team
		InsertClassEvent($TourId, 1, 1, 'RX',  'R', 'M');
		InsertClassEvent($TourId, 2, 1, 'RX',  'R', 'W');
		// Compound Mixed Team
		InsertClassEvent($TourId, 1, 1, 'CX',  'C', 'M');
		InsertClassEvent($TourId, 2, 1, 'CX',  'C', 'W');
		// Barebow Mixed Team
		InsertClassEvent($TourId, 1, 1, 'BX',  'B', 'M');
		InsertClassEvent($TourId, 2, 1, 'BX',  'B', 'W');
		}
//}
		
		// Engir MIXED TEAM útslættir fyrir Unisex móta uppsetningar
	
}

