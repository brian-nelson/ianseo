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
        CreateClass($TourId, $i++, 15, 17, 0, 'CM', 'CM,JM,M', 'Cadet Men/ U18 Karla', 1, '');
        CreateClass($TourId, $i++, 15, 17, 1, 'CW', 'CW,JW,W', 'Cadet Women/ U18 Kvenna', 1, '');
        CreateClass($TourId, $i++, 1, 14, 0, 'NM', 'NM,CM,JM,M', 'Nordic/ Men U15 Karla', 1, '');
        CreateClass($TourId, $i++, 1, 14, 1, 'NW', 'NW,CW,JW,W', 'Nordic/ Women U15 Kvenna', 1, '');
        CreateClass($TourId, $i++, 1, 99, 0, 'BM', 'BM', 'Beginner Men/ Byrjendur Karla', 1, '');
        CreateClass($TourId, $i++, 1, 99, 1, 'BW', 'BW', 'Beginner Women/ Byrjendur Kvenna', 1, '');
		}

	if ($SubRule==2) { // "Only Adult Classes" Bara opinn flokkur COMPLETE
		CreateClass($TourId, $i++, 1, 99, 0, 'M', 'M', 'Men/ Karla', 1, '');
		CreateClass($TourId, $i++, 1, 99, 1, 'W', 'W', 'Women/ Kvenna', 1, '');
		}

    if ($SubRule==3) { // "All-in-one class" Bara opinn flokkur UNISEX COMPLETE
        CreateClass($TourId, $i++, 1, 99, -1, 'U', 'U', 'Universal/ Bæði Kyn');
        return;
		}
	
	if ($SubRule==4) { // "EVERY CLASSES" Allir aldurflokkar UNISEX
        CreateClass($TourId, $i++, 1, 99, -1, 'U', 'U', 'Universal/ Bæði Kyn');
		CreateClass($TourId, $i++, 1, 99, -1, 'MU', 'MU,U', 'Universal Master/ Bæði Kyn E50');
		CreateClass($TourId, $i++, 1, 99, -1, 'JU', 'JU,U', 'Universal Junior/ Bæði Kyn U21');
		CreateClass($TourId, $i++, 1, 99, -1, 'CU', 'CU,JU,U', 'Universal Cadet/ Bæði Kyn U18');
		CreateClass($TourId, $i++, 1, 99, -1, 'NU', 'NU,CU,JU,U', 'Universal Nordic/ Bæði Kyn U15');
        return;
		}
	
}

function CreateStandardSubClasses($TourId) { //Undirflokkar. Þessi partur er ekki notaður COMPLETE
	// Hérna seturðu inn subclasses/undirflokka
}

function CreateStandardEvents($TourId, $TourType, $SubRule, $Outdoor=true) { //Útsláttarkeppni. COMPLETE
    //StandardEvents = Eliminations/Matches Útsláttarkeppni uppsetning, útskýring er fyrir ofan hvern hluta um hvað sá hluti gerir

	// Hér fyrir neðan er skilgreining á því hvaða skífustærðir og fjarlægðir eru notaðar í ÚTSLÁTTARKEPPNI fyrir mismunandi flokka.
	// Senior - Opinn flokkur Útsláttarkeppni Skífustærðir og Fjarlægðir
    $TargetR=($Outdoor ? TGT_OUT_FULL : TGT_IND_6_big10);
    $TargetC=($Outdoor ? TGT_OUT_5_big10 : TGT_IND_6_small10);
    $TargetB=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetSizeR=($Outdoor ? 122 : 40);
    $TargetSizeC=($Outdoor ? 80 : 40);
    $TargetSizeB=($Outdoor ? 80 : 40);
    $DistanceR=($Outdoor ? 70 : 18);
    $DistanceC=($Outdoor ? 50 : 18);
    $DistanceB=($Outdoor ? 30 : 18);

    // Master - E50 Útsláttarkeppni Skífustærðir og Fjarlægðir
    $TargetRM=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetCM=($Outdoor ? TGT_OUT_5_big10 : TGT_IND_6_small10);
	$TargetBM=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetSizeRM=($Outdoor ? 122 : 40);
    $TargetSizeCM=($Outdoor ? 80 : 40);
	$TargetSizeBM=($Outdoor ? 80 : 40);
    $DistanceRM=($Outdoor ? 60 : 18);
    $DistanceCM=($Outdoor ? 50 : 18);
	$DistanceBM=($Outdoor ? 30 : 18);

    // Junior - U21 Útsláttarkeppni Skífustærðir og Fjarlægðir
    $TargetRJ=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetCJ=($Outdoor ? TGT_OUT_5_big10 : TGT_IND_6_small10);
	$TargetBJ=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetSizeRJ=($Outdoor ? 122 : 40);
    $TargetSizeCJ=($Outdoor ? 80 : 40);
	$TargetSizeBJ=($Outdoor ? 80 : 40);
    $DistanceRJ=($Outdoor ? 70 : 18);
    $DistanceCJ=($Outdoor ? 50 : 18);
	$DistanceBJ=($Outdoor ? 30 : 18);

    // Cadet - U18 Útsláttarkeppni Skífustærðir og Fjarlægðir
    $TargetRC=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetCC=($Outdoor ? TGT_OUT_5_big10 : TGT_IND_6_small10);
	$TargetBC=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetSizeRC=($Outdoor ? 122 : 40);
    $TargetSizeCC=($Outdoor ? 80 : 40);
	$TargetSizeBC=($Outdoor ? 80 : 40);
    $DistanceRC=($Outdoor ? 60 : 18);
    $DistanceCC=($Outdoor ? 50 : 18);
	$DistanceBC=($Outdoor ? 30 : 18);

    // Nordic - U15 Útsláttarkeppni Skífustærðir og Fjarlægðir
    $TargetRN=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetCN=($Outdoor ? TGT_OUT_5_big10 : TGT_IND_1_small10);
	$TargetBN=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetSizeRN=($Outdoor ? 122 : 60);
    $TargetSizeCN=($Outdoor ? 80 : 60);
	$TargetSizeBN=($Outdoor ? 80 : 40);
    $DistanceRN=($Outdoor ? 40 : 12);
    $DistanceCN=($Outdoor ? 30 : 12);
	$DistanceBN=($Outdoor ? 30 : 18);

    // Beginner - Byrjendur Útsláttarkeppni Skífustærðir og Fjarlægðir
    $TargetRB=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetCB=($Outdoor ? TGT_OUT_5_big10 : TGT_IND_1_small10);
	$TargetBB=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetSizeRB=($Outdoor ? 122 : 40);
    $TargetSizeCB=($Outdoor ? 80 : 40);
	$TargetSizeBB=($Outdoor ? 80 : 40);
    $DistanceRB=($Outdoor ? 60 : 18);
    $DistanceCB=($Outdoor ? 50 : 18);
	$DistanceBB=($Outdoor ? 30 : 18);

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
	    CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRN, 5, 3, 1, 5, 3, 1, 'RNM',  'Recurve Nordic Men/ Sveigbogi U15 Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeRN, $DistanceRN);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRN, 5, 3, 1, 5, 3, 1, 'RNW',  'Recurve Nordic Women/ Sveigbogi U15 Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeRN, $DistanceRN);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRB, 5, 3, 1, 5, 3, 1, 'RBM',  'Recurve Beginner Men/ Sveigbogi Byrjendur Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeRB, $DistanceRB);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRB, 5, 3, 1, 5, 3, 1, 'RBW',  'Recurve Beginner Women/ Sveigbogi Byrjendur Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeRB, $DistanceRB);
		// Trissubogi
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC, 5, 3, 1, 5, 3, 1, 'CM',  'Compound Men/ Trissubogi Karla', 0, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceC);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC, 5, 3, 1, 5, 3, 1, 'CW',  'Compound Women/ Trissubogi Kvenna', 0, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceC);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCM, 5, 3, 1, 5, 3, 1, 'CMM', 'Compound Master Men/ Trissubogi E50 Karla', 0, 240, 255, 0, 0, '', '', $TargetSizeCM, $DistanceCM);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCM, 5, 3, 1, 5, 3, 1, 'CMW', 'Compound Master Women/ Trissubogi E50 Kvenna', 0, 240, 255, 0, 0, '', '', $TargetSizeCM, $DistanceCM);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCJ, 5, 3, 1, 5, 3, 1, 'CJM', 'Compound Junior Men/ Trissubogi U21 Karla', 0, 240, 255, 0, 0, '', '', $TargetSizeCJ, $DistanceCJ);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCJ, 5, 3, 1, 5, 3, 1, 'CJW', 'Compound Junior Women/ Trissubogi U21 Kvenna', 0, 240, 255, 0, 0, '', '', $TargetSizeCJ, $DistanceCJ);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCC, 5, 3, 1, 5, 3, 1, 'CCM', 'Compound Cadet Men/ Trissubogi U18 Karla', 0, 240, 255, 0, 0, '', '', $TargetSizeCC, $DistanceCC);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCC, 5, 3, 1, 5, 3, 1, 'CCW', 'Compound Cadet Women/ Trissubogi U18 Kvenna', 0, 240, 255, 0, 0, '', '', $TargetSizeCC, $DistanceCC);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCN, 5, 3, 1, 5, 3, 1, 'CNM', 'Compound Nordic Men/ Trissubogi U15 Karla', 0, 240, 255, 0, 0, '', '', $TargetSizeCN, $DistanceCN);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCN, 5, 3, 1, 5, 3, 1, 'CNW', 'Compound Nordic Women/ Trissubogi U15 Kvenna', 0, 240, 255, 0, 0, '', '', $TargetSizeCN, $DistanceCN);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCB, 5, 3, 1, 5, 3, 1, 'CBM', 'Compound Beginner Men/ Trissubogi Byrjendur Karla', 0, 240, 255, 0, 0, '', '', $TargetSizeCB, $DistanceCB);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCB, 5, 3, 1, 5, 3, 1, 'CBW', 'Compound Beginner Women/ Trissubogi Byrjendur Kvenna', 0, 240, 255, 0, 0, '', '', $TargetSizeCB, $DistanceCB);
		// Berbogi
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB, 5, 3, 1, 5, 3, 1, 'BM',  'Barebow Men/ Berbogi Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceB);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB, 5, 3, 1, 5, 3, 1, 'BW',  'Barebow Women/ Berbogi Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceB);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBM, 5, 3, 1, 5, 3, 1, 'BMM', 'Barebow Master Men/ Berbogi E50 Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeBM, $DistanceBM);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBM, 5, 3, 1, 5, 3, 1, 'BMW', 'Barebow Master Women/ Berbogi E50 Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeBM, $DistanceBM);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBJ, 5, 3, 1, 5, 3, 1, 'BJM', 'Barebow Junior Men/ Berbogi U21 Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeBJ, $DistanceBJ);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBJ, 5, 3, 1, 5, 3, 1, 'BJW', 'Barebow Junior Women/ Berbogi U21 Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeBJ, $DistanceBJ);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBC, 5, 3, 1, 5, 3, 1, 'BCM', 'Barebow Cadet Men/ Berbogi U18 Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeBC, $DistanceBC);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBC, 5, 3, 1, 5, 3, 1, 'BCW', 'Barebow Cadet Women/ Berbogi U18 Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeBC, $DistanceBC);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBN, 5, 3, 1, 5, 3, 1, 'BNM', 'Barebow Nordic Men/ Berbogi U15 Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeBN, $DistanceBN);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBN, 5, 3, 1, 5, 3, 1, 'BNW', 'Barebow Nordic Women/ Berbogi U15 Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeBN, $DistanceBN);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBB, 5, 3, 1, 5, 3, 1, 'BBM', 'Barebow Beginner Men/ Berbogi Byrjendur Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeBB, $DistanceBB);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBB, 5, 3, 1, 5, 3, 1, 'BBW', 'Barebow Beginner Women/ Berbogi Byrjendur Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeBB, $DistanceBB);		
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
        return;
		}
		
	if ($SubRule==4) { // "Every Classes" Allir aldurflokkar UNISEX ÚTSLÁTTARKEPPNI EINSTAKLINGA COMPLETE
		// Sveigbogi útsláttarkeppni
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'R',  'Recurve Unisex/ Sveigbogi bæði kyn', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRM, 5, 3, 1, 5, 3, 1, 'RM',  'Recurve Master Unisex/ Sveigbogi E50 bæði kyn', 1, 240, 255, 0, 0, '', '', $TargetSizeRM, $DistanceRM);    
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRJ, 5, 3, 1, 5, 3, 1, 'RJ',  'Recurve Junior Unisex/ Sveigbogi U21 bæði kyn', 1, 240, 255, 0, 0, '', '', $TargetSizeRJ, $DistanceRJ);    
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRC, 5, 3, 1, 5, 3, 1, 'RC',  'Recurve Cadet Unisex/ Sveigbogi U18 bæði kyn', 1, 240, 255, 0, 0, '', '', $TargetSizeRC, $DistanceRC);    
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRN, 5, 3, 1, 5, 3, 1, 'RN',  'Recurve Nordic Unisex/ Sveigbogi U15 bæði kyn', 1, 240, 255, 0, 0, '', '', $TargetSizeRN, $DistanceRN);    
		// Trissubogi útsláttarkeppni
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC, 5, 3, 1, 5, 3, 1, 'C',  'Compound Unisex/ Trissubogi bæði kyn', 0, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCM, 5, 3, 1, 5, 3, 1, 'CM',  'Compound Master Unisex/ Trissubogi E50 bæði kyn', 0, 240, 255, 0, 0, '', '', $TargetSizeCM, $DistanceCM);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCJ, 5, 3, 1, 5, 3, 1, 'CJ',  'Compound Junior Unisex/ Trissubogi U21 bæði kyn', 0, 240, 255, 0, 0, '', '', $TargetSizeCJ, $DistanceCJ);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCC, 5, 3, 1, 5, 3, 1, 'CC',  'Compound Cadet Unisex/ Trissubogi U18 bæði kyn', 0, 240, 255, 0, 0, '', '', $TargetSizeCC, $DistanceCC);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCN, 5, 3, 1, 5, 3, 1, 'CN',  'Compound Nordic Unisex/ Trissubogi U15 bæði kyn', 0, 240, 255, 0, 0, '', '', $TargetSizeCN, $DistanceCN);				
		// Berbogi útsláttarkeppni
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB, 5, 3, 1, 5, 3, 1, 'B',  'Barebow Unisex/ Berbogi bæði kyn', 0, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceB);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBM, 5, 3, 1, 5, 3, 1, 'BM',  'Barebow Master Unisex/ Berbogi E50 bæði kyn', 0, 240, 255, 0, 0, '', '', $TargetSizeBM, $DistanceBM);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBJ, 5, 3, 1, 5, 3, 1, 'BJ',  'Barebow Junior Unisex/ Berbogi U21 bæði kyn', 0, 240, 255, 0, 0, '', '', $TargetSizeBJ, $DistanceBJ);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBC, 5, 3, 1, 5, 3, 1, 'BC',  'Barebow Cadet Unisex/ Berbogi U18 bæði kyn', 0, 240, 255, 0, 0, '', '', $TargetSizeBC, $DistanceBC);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBN, 5, 3, 1, 5, 3, 1, 'BN',  'Barebow Nordic Unisex/ Berbogi U15 bæði kyn', 0, 240, 255, 0, 0, '', '', $TargetSizeBN, $DistanceBN);
        return;
		}

	// LIÐA útslættir
	if ($SubRule==1) { // "Championship" Allir aldursflokkar útsláttarkeppni LIÐA COMPLETE
		// Til upplýsinga tölurnar á eftir $Targetx standa fyrir "fjöldi umferða","fjöldi örva","fjöldi örva bráðabani" og svo sömu tölur enduteknar.
		// Sveigbogi
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetR, 4, 6, 3, 4, 6, 3, 'RM',  'Recurve Men/ Sveigbogi Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetR, 4, 6, 3, 4, 6, 3, 'RW',  'Recurve Women/ Sveigbogi Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRM, 4, 6, 3, 4, 6, 3, 'RMM',  'Recurve Master Men/ Sveigbogi E50 Karla)', 1, 240, 255, 0, 0, '', '', $TargetSizeRM, $DistanceRM);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRM, 4, 6, 3, 4, 6, 3, 'RMW',  'Recurve Master Women/ Sveigbogi E50 Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeRM, $DistanceRM);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRJ, 4, 6, 3, 4, 6, 3, 'RJM',  'Recurve Junior Men/ Sveigbogi U21 Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeRJ, $DistanceRJ);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRJ, 4, 6, 3, 4, 6, 3, 'RJW',  'Recurve Junior Women/ Sveigbogi U21 Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeRJ, $DistanceRJ);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRC, 4, 6, 3, 4, 6, 3, 'RCM',  'Recurve Cadet Men/ Sveigbogi U18 Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeRC, $DistanceRC);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRC, 4, 6, 3, 4, 6, 3, 'RCW',  'Recurve Cadet Women/ Sveigbogi U18 Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeRC, $DistanceRC);
	    CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRN, 4, 6, 3, 4, 6, 3, 'RNM',  'Recurve Nordic Men/ Sveigbogi U15 Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeRN, $DistanceRN);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRN, 4, 6, 3, 4, 6, 3, 'RNW',  'Recurve Nordic Women/ Sveigbogi U15 Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeRN, $DistanceRN);
        //CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRB, 4, 6, 3, 4, 6, 3, 'RBM',  'Recurve Beginner Men/ Sveigbogi Byrjendur Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeRB, $DistanceRB); // Enginn útsláttarkeppni fyrir liða byrjendur
        //CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRB, 4, 6, 3, 4, 6, 3, 'RBW',  'Recurve Beginner Women/ Sveigbogi Byrjendur Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeRB, $DistanceRB); // Enginn útsláttarkeppni fyrir liða byrjendur
		// Trissubogi
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetC, 4, 6, 3, 4, 6, 3, 'CM',  'Compound Men/ Trissubogi Karla', 0, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceC);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetC, 4, 6, 3, 4, 6, 3, 'CW',  'Compound Women/ Trissubogi Kvenna', 0, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceC);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCM, 4, 6, 3, 4, 6, 3, 'CMM', 'Compound Master Men/ Trissubogi E50 Karla', 0, 240, 255, 0, 0, '', '', $TargetSizeCM, $DistanceCM);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCM, 4, 6, 3, 4, 6, 3, 'CMW', 'Compound Master Women/ Trissubogi E50 Kvenna', 0, 240, 255, 0, 0, '', '', $TargetSizeCM, $DistanceCM);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCJ, 4, 6, 3, 4, 6, 3, 'CJM', 'Compound Junior Men/ Trissubogi U21 Karla', 0, 240, 255, 0, 0, '', '', $TargetSizeCJ, $DistanceCJ);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCJ, 4, 6, 3, 4, 6, 3, 'CJW', 'Compound Junior Women/ Trissubogi U21 Kvenna', 0, 240, 255, 0, 0, '', '', $TargetSizeCJ, $DistanceCJ);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCC, 4, 6, 3, 4, 6, 3, 'CCM', 'Compound Cadet Men/ Trissubogi U18 Karla', 0, 240, 255, 0, 0, '', '', $TargetSizeCC, $DistanceCC);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCC, 4, 6, 3, 4, 6, 3, 'CCW', 'Compound Cadet Women/ Trissubogi U18 Kvenna', 0, 240, 255, 0, 0, '', '', $TargetSizeCC, $DistanceCC);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCN, 4, 6, 3, 4, 6, 3, 'CNM', 'Compound Nordic Men/ Trissubogi U15 Karla', 0, 240, 255, 0, 0, '', '', $TargetSizeCN, $DistanceCN);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCN, 4, 6, 3, 4, 6, 3, 'CNW', 'Compound Nordic Women/ Trissubogi U15 Kvenna', 0, 240, 255, 0, 0, '', '', $TargetSizeCN, $DistanceCN);
        //CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCB, 4, 6, 3, 4, 6, 3, 'CBM', 'Compound Beginner Men/ Trissubogi Byrjendur Karla', 0, 240, 255, 0, 0, '', '', $TargetSizeCB, $DistanceCB); // Enginn útsláttarkeppni fyrir liða byrjendur
        //CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCB, 5, 3, 1, 5, 3, 1, 'CBW', 'Compound Beginner Women/ Trissubogi Byrjendur Kvenna', 0, 240, 255, 0, 0, '', '', $TargetSizeCB, $DistanceCB); // Enginn útsláttarkeppni fyrir liða byrjendur
		// Berbogi
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetB, 4, 6, 3, 4, 6, 3, 'BM',  'Barebow Men/ Berbogi Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceB);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetB, 4, 6, 3, 4, 6, 3, 'BW',  'Barebow Women/ Berbogi Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceB);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBM, 4, 6, 3, 4, 6, 3, 'BMM', 'Barebow Master Men/ Berbogi E50 Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeBM, $DistanceBM);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBM, 4, 6, 3, 4, 6, 3, 'BMW', 'Barebow Master Women/ Berbogi E50 Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeBM, $DistanceBM);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBJ, 4, 6, 3, 4, 6, 3, 'BJM', 'Barebow Junior Men/ Berbogi U21 Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeBJ, $DistanceBJ);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBJ, 4, 6, 3, 4, 6, 3, 'BJW', 'Barebow Junior Women/ Berbogi U21 Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeBJ, $DistanceBJ);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBC, 4, 6, 3, 4, 6, 3, 'BCM', 'Barebow Cadet Men/ Berbogi U18 Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeBC, $DistanceBC);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBC, 4, 6, 3, 4, 6, 3, 'BCW', 'Barebow Cadet Women/ Berbogi U18 Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeBC, $DistanceBC);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBN, 4, 6, 3, 4, 6, 3, 'BNM', 'Barebow Nordic Men/ Berbogi U15 Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeBN, $DistanceBN);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBN, 4, 6, 3, 4, 6, 3, 'BNW', 'Barebow Nordic Women/ Berbogi U15 Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeBN, $DistanceBN);
        //CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBB, 4, 6, 3, 4, 6, 3, 'BBM', 'Barebow Beginner Men/ Berbogi Byrjendur Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeBB, $DistanceBB); // Enginn útsláttarkeppni fyrir liða byrjendur
        //CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBB, 4, 6, 3, 4, 6, 3, 'BBW', 'Barebow Beginner Women/ Berbogi Byrjendur Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeBB, $DistanceBB); // Enginn útsláttarkeppni fyrir liða byrjendur		
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
	// Engir liðaútslættir fyrir Unisex móta uppsetningar
	
	if($Outdoor) { // MIXED TEAM Útsláttarkeppni bætist við þegar mót er utandyra
	if ($SubRule==1) { // "Championship" Allir aldursflokkar útsláttarkeppni MIXED TEAM COMPLETE
		// Recurve Mixed Team - Sveigboga Paraliðakeppni útsláttur
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetR, 4, 4, 2, 4, 4, 2, 'RX', 'Recurve Mixed Team/ Sveigbogi Parakeppni', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRJ, 4, 4, 2, 4, 4, 2, 'RJX', 'Recurve Junior Mixed Team/ Sveigbogi Parakeppni U21', 1, 240, 255, 0, 0, '', '', $TargetSizeRJ, $DistanceRJ);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRC, 4, 4, 2, 4, 4, 2, 'RCX', 'Recurve Cadet Mixed Team/ Sveigbogi Parakeppni U18', 1, 240, 255, 0, 0, '', '', $TargetSizeRC, $DistanceRC);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRM, 4, 4, 2, 4, 4, 2, 'RMX', 'Recurve Master Mixed Team/ Sveigbogi Parakeppni E50', 1, 240, 255, 0, 0, '', '', $TargetSizeRM, $DistanceRM);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRN, 4, 4, 2, 4, 4, 2, 'RNX', 'Recurve Nordic Mixed Team/ Sveigbogi Parakeppni U15', 1, 240, 255, 0, 0, '', '', $TargetSizeRN, $DistanceRN);
		// Compound MIXED TEAM Trissuboga Paraliðakeppni útsláttur
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetC, 4, 4, 2, 4, 4, 2, 'CX', 'Compound Mixed Team/ Trissubogi Parakeppni', 0, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceC);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCJ, 4, 4, 2, 4, 4, 2, 'CJX', 'Compound Junior Mixed Team/ Trissubogi Parakeppni U21', 0, 240, 255, 0, 0, '', '', $TargetSizeCJ, $DistanceCJ);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCC, 4, 4, 2, 4, 4, 2, 'CCX', 'Compound Cadet Mixed Team/ Trissubogi Parakeppni U18', 0, 240, 255, 0, 0, '', '', $TargetSizeCC, $DistanceCC);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCM, 4, 4, 2, 4, 4, 2, 'CMX', 'Compound Master Mixed Team/ Trissubogi Parakeppni E50', 0, 240, 255, 0, 0, '', '', $TargetSizeCM, $DistanceCM);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCN, 4, 4, 2, 4, 4, 2, 'CNX', 'Compound Nordic Mixed Team/ Trissubogi Parakeppni U15', 0, 240, 255, 0, 0, '', '', $TargetSizeCN, $DistanceCN);
		// BAREBOW MIXED TEAM Berboga Paraliðakeppni útsláttur
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetB, 4, 4, 2, 4, 4, 2, 'BX', 'Barebow Mixed Team/ Berbogi Parakeppni', 1, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceB);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBJ, 4, 4, 2, 4, 4, 2, 'BJX', 'Barebow Junior Mixed Team/ Berbogi Parakeppni U21', 1, 240, 255, 0, 0, '', '', $TargetSizeBJ, $DistanceBJ);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBC, 4, 4, 2, 4, 4, 2, 'BCX', 'Barebow Cadet Mixed Team/ Berbogi Parakeppni U18', 1, 240, 255, 0, 0, '', '', $TargetSizeBC, $DistanceBC);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBM, 4, 4, 2, 4, 4, 2, 'BMX', 'Barebow Master Mixed Team/ Berbogi Parakeppni E50', 1, 240, 255, 0, 0, '', '', $TargetSizeBM, $DistanceBM);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBN, 4, 4, 2, 4, 4, 2, 'BNX', 'Barebow Nordic Mixed Team/ Berbogi Parakeppni U15', 1, 240, 255, 0, 0, '', '', $TargetSizeBN, $DistanceBN);
		}
		
	if ($SubRule==2) { //"Only Adult Classes" Bara opinn flokkur útsláttarkeppni MIXED TEAM COMPLETE
		// Recurve MIXED TEAM
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetR, 4, 4, 2, 4, 4, 2, 'RX', 'Recurve Mixed Team/ Sveigbogi Parakeppni', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);
		// Compound MIXED TEAM
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetC, 4, 4, 2, 4, 4, 2, 'CX', 'Compound Mixed Team/ Trissubogi Parakeppni', 0, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceC);
		// BAREBOW MIXED TEAM 
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetB, 4, 4, 2, 4, 4, 2, 'BX', 'Barebow Mixed Team/ Berbogi Parakeppni', 1, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceB);
		}
		}
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
        return;
		}
		
	if ($SubRule==4) {	// "VANTAR SUBRULE4 SETTIÐ Í SETS SKJALIÐ" Allir aldurflokkar UNISEX ÚTSLÁTTARKEPPNI EINSTAKLINGA TENGING COMPLETE
        InsertClassEvent($TourId, 0, 1, 'R',  'R', 'U');
		InsertClassEvent($TourId, 0, 1, 'RM',  'R', 'MU');
		InsertClassEvent($TourId, 0, 1, 'RJ',  'R', 'JU');
		InsertClassEvent($TourId, 0, 1, 'RC',  'R', 'CU');
		InsertClassEvent($TourId, 0, 1, 'RN',  'R', 'NU');
		
        InsertClassEvent($TourId, 0, 1, 'C',  'C', 'U');
		InsertClassEvent($TourId, 0, 1, 'CM',  'C', 'MU');
		InsertClassEvent($TourId, 0, 1, 'CJ',  'C', 'JU');
		InsertClassEvent($TourId, 0, 1, 'CC',  'C', 'CU');
		InsertClassEvent($TourId, 0, 1, 'CN',  'C', 'NU');
		
		InsertClassEvent($TourId, 0, 1, 'B',  'B', 'U');
		InsertClassEvent($TourId, 0, 1, 'BM',  'B', 'MU');
		InsertClassEvent($TourId, 0, 1, 'BJ',  'B', 'JU');
		InsertClassEvent($TourId, 0, 1, 'BC',  'B', 'CU');
		InsertClassEvent($TourId, 0, 1, 'BN',  'B', 'NU');
        return;
		}
  
	//TENGINGAR Í LIÐAÚTSLÆTTI
	if ($SubRule==1) { // "Championship" Allir aldursflokkar útsláttarkeppni LIÐA tenging COMPLETE
		//Recurve Team Liðatenging 3 manna standard worldarchery
		InsertClassEvent($TourId, 1, 3, 'RM', 'R', 'M');
		InsertClassEvent($TourId, 1, 3, 'RW', 'R', 'W');
		InsertClassEvent($TourId, 1, 3, 'RMM', 'R', 'MM');
		InsertClassEvent($TourId, 1, 3, 'RMW', 'R', 'MW');
		InsertClassEvent($TourId, 1, 3, 'RJM', 'R', 'JM');
		InsertClassEvent($TourId, 1, 3, 'RJW', 'R', 'JW');
		InsertClassEvent($TourId, 1, 3, 'RCM', 'R', 'CM');
		InsertClassEvent($TourId, 1, 3, 'RCW', 'R', 'CW');
		InsertClassEvent($TourId, 1, 3, 'RNM', 'R', 'NM');
		InsertClassEvent($TourId, 1, 3, 'RNW', 'R', 'NW');
		//InsertClassEvent($TourId, 1, 3, 'RBM', 'R', 'BM'); //Byrjendalið ekki í notkun
		//InsertClassEvent($TourId, 1, 3, 'RBM', 'R', 'BW'); //Byrjendalið ekki í notkun

		//Compound Team Liðatenging 3 manna standard worldarchery
		InsertClassEvent($TourId, 1, 3, 'CM', 'C', 'M');
		InsertClassEvent($TourId, 1, 3, 'CW', 'C', 'W');
		InsertClassEvent($TourId, 1, 3, 'CMM', 'C', 'MM');
		InsertClassEvent($TourId, 1, 3, 'CMW', 'C', 'MW');
		InsertClassEvent($TourId, 1, 3, 'CJM', 'C', 'JM');
		InsertClassEvent($TourId, 1, 3, 'CJW', 'C', 'JW');
		InsertClassEvent($TourId, 1, 3, 'CCM', 'C', 'CM');
		InsertClassEvent($TourId, 1, 3, 'CCW', 'C', 'CW');
		InsertClassEvent($TourId, 1, 3, 'CNM', 'C', 'NM');
		InsertClassEvent($TourId, 1, 3, 'CNW', 'C', 'NW');
		//InsertClassEvent($TourId, 1, 3, 'CBM', 'C', 'BM'); //Byrjendalið ekki í notkun
		//InsertClassEvent($TourId, 1, 3, 'CBM', 'C', 'BW'); //Byrjendalið ekki í notkun
	
		//Barebow Team Liðatenging 3 manna standard worldarchery
		InsertClassEvent($TourId, 1, 3, 'BM', 'B', 'M');
		InsertClassEvent($TourId, 1, 3, 'BW', 'B', 'W');
		InsertClassEvent($TourId, 1, 3, 'BMM', 'B', 'MM');
		InsertClassEvent($TourId, 1, 3, 'BMW', 'B', 'MW');
		InsertClassEvent($TourId, 1, 3, 'BJM', 'B', 'JM');
		InsertClassEvent($TourId, 1, 3, 'BJW', 'B', 'JW');
		InsertClassEvent($TourId, 1, 3, 'BCM', 'B', 'CM');
		InsertClassEvent($TourId, 1, 3, 'BCW', 'B', 'CW');
		InsertClassEvent($TourId, 1, 3, 'BNM', 'B', 'NM');
		InsertClassEvent($TourId, 1, 3, 'BNW', 'B', 'NW');
		//InsertClassEvent($TourId, 1, 3, 'BBM', 'B', 'BM'); //Byrjendalið ekki í notkun
		//InsertClassEvent($TourId, 1, 3, 'BBM', 'B', 'BW'); //Byrjendalið ekki í notkun	
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
		
		// Engir liðaútslættir fyrir Unisex móta uppsetningar
	
	if($Outdoor) { // TENGINGAR Í MIXED TEAM ÚTSLÆTTI COMPLETE
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
		//InsertClassEvent($TourId, 1, 1, 'RBX',  'R', 'BM'); //Byrjendalið ekki í notkun
		//InsertClassEvent($TourId, 2, 1, 'RBX',  'R', 'BW'); //Byrjendalið ekki í notkun
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
		//InsertClassEvent($TourId, 1, 1, 'CBX',  'C', 'BM'); //Byrjendalið ekki í notkun
		//InsertClassEvent($TourId, 2, 1, 'CBX',  'C', 'BW'); //Byrjendalið ekki í notkun
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
		//InsertClassEvent($TourId, 1, 1, 'BBX',  'B', 'BM'); //Byrjendalið ekki í notkun
		//InsertClassEvent($TourId, 2, 1, 'BBX',  'B', 'BW'); //Byrjendalið ekki í notkun
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
		}
		
		// Engir MIXED TEAM útslættir fyrir Unisex móta uppsetningar
	
}

