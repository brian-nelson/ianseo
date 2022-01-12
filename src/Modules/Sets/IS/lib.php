<?php

// these go here as it is a "global" definition, used or not
$tourCollation = '';
$tourDetIocCode = 'IS';
if(empty($SubRule)) $SubRule='1';

function CreateStandardDivisions($TourId, $Type=1, $SubRule=0) { //Bogaflokkar/Undankeppni. Þessum parti er lokið COMPLETE
    // Ignoring sub-rules for now. Þetta function section bætir við Bogaflokkum í "Division and classes" partinn af Ianseo
    $i=1;
	CreateDivision($TourId, $i++, 'R', 'Sveigbogi/ Recurve');
	CreateDivision($TourId, $i++, 'C', 'Trissubogi/ Compound');
    CreateDivision($TourId, $i++, 'B', 'Berbogi/ Barebow');
	if ($SubRule==7) { // "Set Kids classes" BARA UNGMENNA FLOKKAR SEMSAGT NUM COMPLETE
	CreateDivision($TourId, $i++, 'L', 'Langbogi/ Longbow');
	CreateDivision($TourId, $i++, 'I', 'Instinctive bow');
	}
}

function CreateStandardClasses($TourId, $SubRule, $Type) { //Aldursflokkar og kyn/Undankeppni. COMPLETE
    //Aldursflokkar. Þetta function section bætir við aldursflokkum og kynjum í "Division and classes" partinn af Ianseo
    $i=1;

	if ($SubRule==1) { // "Championship" Allir Aldursflokkar COMPLETE
		CreateClass($TourId, $i++, 21, 29, 0, 'M', 'M,AM', 'Karla/ Men', 1, '');
		CreateClass($TourId, $i++, 21, 29, 1, 'W', 'W,AW', 'Kvenna/ Women', 1, '');
        CreateClass($TourId, $i++, 18, 20, 0, 'JM', 'JM,M,AM', 'U21 Karla/ Junior Men', 1, '');
        CreateClass($TourId, $i++, 18, 20, 1, 'JW', 'JW,W,AW', 'U21 Kvenna/ Junior Women', 1, '');
        CreateClass($TourId, $i++, 16, 17, 0, 'CM', 'CM,JM,M,AM', 'U18 Karla/ Cadet Men', 1, '');
        CreateClass($TourId, $i++, 16, 17, 1, 'CW', 'CW,JW,W,AW', 'U18 Kvenna/ Cadet Women', 1, '');
        CreateClass($TourId, $i++, 14, 15, 0, 'NM', 'NM,CM,JM,M,AM', 'U16 Karla/ Nordic Men', 1, '');
        CreateClass($TourId, $i++, 14, 15, 1, 'NW', 'NW,CW,JW,W,AW', 'U16 Kvenna/ Nordic Women', 1, '');
		CreateClass($TourId, $i++, 1, 13, 0, 'KM', 'KM,NM,CM,JM,M,AM', 'U14 Karla', 1, '');
        CreateClass($TourId, $i++, 1, 13, 1, 'KW', 'KW,NW,CW,JW,W,AW', 'U14 Kvenna', 1, '');
		CreateClass($TourId, $i++, 30, 39, 0, '3M', '3M,M,AM', '30+ Karla/ 30+ Men', 1, '');
        CreateClass($TourId, $i++, 30, 39, 1, '3W', '3W,W,AW', '30+ Kvenna/ 30+ Women', 1, '');
        CreateClass($TourId, $i++, 40, 49, 0, '4M', '3M,4M,M,AM', '40+ Karla/ 40+ Men', 1, '');
        CreateClass($TourId, $i++, 40, 49, 1, '4W', '3W,4W,W,AW', '40+ Kvenna/ 40+ Women', 1, '');
        CreateClass($TourId, $i++, 50, 59, 0, '5M', '3M,4M,5M,M,AM', '50+ Karla/ 50+ Men-Masters', 1, '');
        CreateClass($TourId, $i++, 50, 59, 1, '5W', '3W,4W,5W,W,AW', '50+ Kvenna/ 50+ Women-Masters', 1, '');
		CreateClass($TourId, $i++, 60, 69, 0, '6M', '3M,4M,5M,6M,M,AM', '60+ Karla/ 60+ Men-Veteran', 1, '');
        CreateClass($TourId, $i++, 60, 69, 1, '6W', '3W,4W,5W,6W,W,AW', '60+ Kvenna/ 60+ Women-Veteran', 1, '');
		CreateClass($TourId, $i++, 70, 99, 0, '7M', '3M,4W,5M,6M,7M,M,AM', '70+ Karla/ 70+ Men', 1, '');
        CreateClass($TourId, $i++, 70, 99, 1, '7W', '3W,4W,5W,6W,7W,W,AW', '70+ Kvenna/ 70+ Women', 1, '');
		CreateClass($TourId, $i++, 99, 100, 0, 'AM', 'AM', 'Áhugamannaflokkur Karla', 1, '');
        CreateClass($TourId, $i++, 99, 100, 1, 'AW', 'AW', 'Áhugamannaflokkur Kvenna', 1, '');
		}

	if ($SubRule==2) { // "Only Adult Classes" Bara opinn flokkur en skilgreindir aldursflokkar fyrir úrlistakerfi, COMPLETE
		CreateClass($TourId, $i++, 21, 29, 0, 'M', 'M,AM', 'Karla/ Men', 1, '');
		CreateClass($TourId, $i++, 21, 29, 1, 'W', 'W,AW', 'Kvenna/ Women', 1, '');
        CreateClass($TourId, $i++, 18, 20, 0, 'JM', 'M,AM', 'U21 Karla/ Junior Men', 1, '');
        CreateClass($TourId, $i++, 18, 20, 1, 'JW', 'W,AW', 'U21 Kvenna/ Junior Women', 1, '');
        CreateClass($TourId, $i++, 16, 17, 0, 'CM', 'M,AM', 'U18 Karla/ Cadet Men', 1, '');
        CreateClass($TourId, $i++, 16, 17, 1, 'CW', 'W,AW', 'U18 Kvenna/ Cadet Women', 1, '');
        CreateClass($TourId, $i++, 14, 15, 0, 'NM', 'M,AM', 'U16 Karla/ Nordic Men', 1, '');
        CreateClass($TourId, $i++, 14, 15, 1, 'NW', 'W,AW', 'U16 Kvenna/ Nordic Women', 1, '');
		CreateClass($TourId, $i++, 1, 13, 0, 'KM', 'M,AM', 'U14 Karla', 1, '');
        CreateClass($TourId, $i++, 1, 13, 1, 'KW', 'W,AW', 'U14 Kvenna', 1, '');
		CreateClass($TourId, $i++, 30, 39, 0, '3M', 'M,AM', '30+ Karla/ 30+ Men', 1, '');
        CreateClass($TourId, $i++, 30, 39, 1, '3W', 'W,AW', '30+ Kvenna/ 30+ Women', 1, '');
        CreateClass($TourId, $i++, 40, 49, 0, '4M', 'M,AM', '40+ Karla/ 40+ Men', 1, '');
        CreateClass($TourId, $i++, 40, 49, 1, '4W', 'W,AW', '40+ Kvenna/ 40+ Women', 1, '');
        CreateClass($TourId, $i++, 50, 59, 0, '5M', 'M,AM', '50+ Karla/ 50+ Men-Masters', 1, '');
        CreateClass($TourId, $i++, 50, 59, 1, '5W', 'W,AW', '50+ Kvenna/ 50+ Women-Masters', 1, '');
		CreateClass($TourId, $i++, 60, 69, 0, '6M', 'M,AM', '60+ Karla/ 60+ Men-Veteran', 1, '');
        CreateClass($TourId, $i++, 60, 69, 1, '6W', 'W,AW', '60+ Kvenna/ 60+ Women-Veteran', 1, '');
		CreateClass($TourId, $i++, 70, 99, 0, '7M', 'M,AM', '70+ Karla/ 70+ Men', 1, '');
        CreateClass($TourId, $i++, 70, 99, 1, '7W', 'W,AW', '70+ Kvenna/ 70+ Women', 1, '');
		CreateClass($TourId, $i++, 99, 100, 0, 'AM', 'AM', 'Áhugamannaflokkur Karla', 1, '');
        CreateClass($TourId, $i++, 99, 100, 1, 'AW', 'AW', 'Áhugamannaflokkur Kvenna', 1, '');
		}

    if ($SubRule==3) { // "All-in-one class" UNISEX/IceCup bara opinn flokkur en skilgreindir aldursflokkar fyrir úrslitakerfi, COMPLETE
        CreateClass($TourId, $i++, 70, 99, -1, '7U', 'U,AU', '70+ Unisex');
		CreateClass($TourId, $i++, 60, 69, -1, '6U', 'U,AU', '60+ Unisex');
		CreateClass($TourId, $i++, 50, 59, -1, '5U', 'U,AU', '50+ Unisex');
		CreateClass($TourId, $i++, 40, 49, -1, '4U', 'U,AU', '40+ Unisex');
		CreateClass($TourId, $i++, 30, 39, -1, '3U', 'U,AU', '30+ Unisex');
        CreateClass($TourId, $i++, 21, 29, -1, 'U', 'U,AU', 'Unisex');
		CreateClass($TourId, $i++, 18, 20, -1, 'JU', 'U,AU', 'U21 Unisex');
		CreateClass($TourId, $i++, 16, 17, -1, 'CU', 'U,AU', 'U18 Unisex');
		CreateClass($TourId, $i++, 14, 15, -1, 'NU', 'U,AU', 'U16 Unisex');
		CreateClass($TourId, $i++, 1, 13, -1, 'KU', 'U,AU', 'U14 Unisex');
		CreateClass($TourId, $i++, 99, 100, -1, 'AU', 'AU', 'Áhugamannaflokkur Unisex');
		}
	
	if ($SubRule==4) { // "EVERY CLASSES" Allir aldurflokkar ungmenna UNGMENNADEILDIN COMPLETE
		CreateClass($TourId, $i++, 18, 20, 0, 'JM', 'JM', 'U21 Karla', 1, '');
        CreateClass($TourId, $i++, 18, 20, 1, 'JW', 'JW', 'U21 Kvenna', 1, '');
		CreateClass($TourId, $i++, 16, 17, 0, 'CM', 'CM,JM', 'U18 Karla', 1, '');
        CreateClass($TourId, $i++, 16, 17, 1, 'CW', 'CW,JW', 'U18 Kvenna', 1, '');
        CreateClass($TourId, $i++, 14, 15, 0, 'NM', 'NM,CM,JM', 'U16 Karla', 1, '');
        CreateClass($TourId, $i++, 14, 15, 1, 'NW', 'NW,CW,JW', 'U16 Kvenna', 1, '');
		CreateClass($TourId, $i++, 1, 13, 0, 'KM', 'KM,NM,CM,JM', 'U14 Karla', 1, '');
        CreateClass($TourId, $i++, 1, 13, 1, 'KW', 'KW,NW,CW,JW', 'U14 Kvenna', 1, '');
		}
		
	if ($SubRule==5) { // "Set Kids classes" Allir aldursflokkar öldunga ÍSLANDSMÓT ÖLDUNGA COMPLETE
		CreateClass($TourId, $i++, 30, 39, 0, '3M', '3M', '30+ Karla/ 30+ Men', 1, '');
        CreateClass($TourId, $i++, 30, 39, 1, '3W', '3W', '30+ Kvenna/ 30+ Women', 1, '');
        CreateClass($TourId, $i++, 40, 49, 0, '4M', '3M,4M', '40+ Karla/ 40+ Men', 1, '');
        CreateClass($TourId, $i++, 40, 49, 1, '4W', '3W,4W', '40+ Kvenna/ 40+ Women', 1, '');
        CreateClass($TourId, $i++, 50, 59, 0, '5M', '3M,4M,5M', '50+ Karla/ 50+ Men-Masters', 1, '');
        CreateClass($TourId, $i++, 50, 59, 1, '5W', '3W,4W,5W', '50+ Kvenna/ 50+ Women-Masters', 1, '');
		CreateClass($TourId, $i++, 60, 69, 0, '6M', '3M,4M,5M,6M', '60+ Karla/ 60+ Men-Veteran', 1, '');
        CreateClass($TourId, $i++, 60, 69, 1, '6W', '3W,4W,5W,6W', '60+ Kvenna/ 60+ Women-Veteran', 1, '');
		CreateClass($TourId, $i++, 70, 99, 0, '7M', '3M,4M,5M,6M,7M', '70+ Karla/ 70+ Men', 1, '');
        CreateClass($TourId, $i++, 70, 99, 1, '7W', '3W,4W,5W,6W,7W', '70+ Kvenna/ 70+ Women', 1, '');
		}

    if ($SubRule==6) { // "All Classes WA 4 Pools" UNISEX allir aldursflokkar og skilgreindir aldursflokkar fyrir úrslitakerfi, COMPLETE
        CreateClass($TourId, $i++, 70, 99, -1, '7U', '3U,4U,5U,6U,7U,U,AU', '70+ Unisex');
		CreateClass($TourId, $i++, 60, 69, -1, '6U', '3U,4U,5U,6U,U,AU', '60+ Unisex');
		CreateClass($TourId, $i++, 50, 59, -1, '5U', '3U,4U,5U,U,AU', '50+ Unisex');
		CreateClass($TourId, $i++, 40, 49, -1, '4U', '3U,4U,U,AU', '40+ Unisex');
		CreateClass($TourId, $i++, 30, 39, -1, '3U', '3U,U,AU', '30+ Unisex');
        CreateClass($TourId, $i++, 21, 29, -1, 'U', 'U,AU', 'Unisex');
		CreateClass($TourId, $i++, 18, 20, -1, 'JU', 'JU,U,AU', 'U21 Unisex');
		CreateClass($TourId, $i++, 16, 17, -1, 'CU', 'CU,JU,U,AU', 'U18 Unisex');
		CreateClass($TourId, $i++, 14, 15, -1, 'NU', 'NU,CU,JU,U,AU', 'U16 Unisex');
		CreateClass($TourId, $i++, 1, 13, -1, 'KU', 'KU,NU,CU,JU,U,AU', 'U14 Unisex');
		CreateClass($TourId, $i++, 99, 100, -1, 'AU', 'AU', 'Áhugamannaflokkur Unisex');
		}		
		
}

function CreateStandardSubClasses($TourId) { //Undirflokkar. Þessi partur er ekki notaður COMPLETE
	// Hérna seturðu inn subclasses/undirflokka
}

function CreateStandardEvents($TourId, $TourType, $SubRule, $Outdoor=true) { //Útsláttarkeppni. COMPLETE
    //StandardEvents = Eliminations/Matches Útsláttarkeppni uppsetning, útskýring er fyrir ofan hvern hluta um hvað sá hluti gerir

{	// Hér fyrir neðan er skilgreining á því hvaða skífustærðir og fjarlægðir eru notaðar í ÚTSLÁTTARKEPPNI fyrir mismunandi flokka. COMPLETE
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
	
	// Master 50+ Útsláttarkeppni Skífustærðir og Fjarlægðir
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
	
	// Áhugamannaflokkur - Áhugamannaflokkur Útsláttarkeppni Skífustærðir og Fjarlægðir
    $TargetRA=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetCA=($Outdoor ? TGT_OUT_5_big10 : TGT_IND_1_small10);
    $TargetBA=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
	$TargetLA=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
	$TargetIA=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetSizeRA=($Outdoor ? 122 : 40);
    $TargetSizeCA=($Outdoor ? 80 : 40);
    $TargetSizeBA=($Outdoor ? 122 : 40);
	$TargetSizeLA=($Outdoor ? 122 : 40);
	$TargetSizeIA=($Outdoor ? 122 : 40);
    $DistanceRA=($Outdoor ? 40 : 12);
    $DistanceCA=($Outdoor ? 30 : 12);
    $DistanceBA=($Outdoor ? 30 : 12);
	$DistanceLA=($Outdoor ? 30 : 12);
	$DistanceIA=($Outdoor ? 30 : 12);
}
	
	// $Phase stillir globally í hvaða útslætti útsláttarkeppni byrjar 0=engin útsláttur "---" 1=semi finals, 2=quarter finals og svo framvegis.
	// Ef þú vilt stilla suma útslætti til að byrja á ákveðnum stað þarftu að finna þann útslátt og bæta við t.d =0 fyrir aftan $Phase í útsláttarlínuni fyrir þann flokk
    $Phase=0; 
    $i=0;

	// Einstaklinga útslættir
	if ($SubRule==1) { // "Championship" Allir aldursflokkar útsláttarkeppni EINSTAKLINGA COMPLETE
		// Sveigbogi
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR,  5, 3, 1, 5, 3, 1, 'RM',  'Sveigbogi Karla/ Recurve Men', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR,  5, 3, 1, 5, 3, 1, 'RW',  'Sveigbogi Kvenna/ Recurve Women', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRJ, 5, 3, 1, 5, 3, 1, 'RJM', 'Sveigbogi U21 Karla/ Recurve Junior Men', 1, 240, 255, 0, 0, '', '', $TargetSizeRJ, $DistanceRJ);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRJ, 5, 3, 1, 5, 3, 1, 'RJW', 'Sveigbogi U21 Kvenna/ Recurve Junior Women', 1, 240, 255, 0, 0, '', '', $TargetSizeRJ, $DistanceRJ);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRC, 5, 3, 1, 5, 3, 1, 'RCM', 'Sveigbogi U18 Karla/ Recurve Cadet Men', 1, 240, 255, 0, 0, '', '', $TargetSizeRC, $DistanceRC);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRC, 5, 3, 1, 5, 3, 1, 'RCW', 'Sveigbogi U18 Kvenna/ Recurve Cadet Women', 1, 240, 255, 0, 0, '', '', $TargetSizeRC, $DistanceRC);
	    CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRN, 5, 3, 1, 5, 3, 1, 'RNM', 'Sveigbogi U16 Karla/ Recurve Nordic Men', 1, 240, 255, 0, 0, '', '', $TargetSizeRN, $DistanceRN);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRN, 5, 3, 1, 5, 3, 1, 'RNW', 'Sveigbogi U16 Kvenna/ Recurve Nordic Women', 1, 240, 255, 0, 0, '', '', $TargetSizeRN, $DistanceRN);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRK, 5, 3, 1, 5, 3, 1, 'RKM', 'Sveigbogi U14 Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeRK, $DistanceRK);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRK, 5, 3, 1, 5, 3, 1, 'RKW', 'Sveigbogi U14 Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeRK, $DistanceRK);	
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR3, 5, 3, 1, 5, 3, 1, 'R3M', 'Sveigbogi 30+ Karla/ Recurve 30+ Men', 1, 240, 255, 0, 0, '', '', $TargetSizeR3, $DistanceR3);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR3, 5, 3, 1, 5, 3, 1, 'R3W', 'Sveigbogi 30+ Kvenna/ Recurve 30+ Women', 1, 240, 255, 0, 0, '', '', $TargetSizeR3, $DistanceR3);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR4, 5, 3, 1, 5, 3, 1, 'R4M', 'Sveigbogi 40+ Karla/ Recurve 40+ Men', 1, 240, 255, 0, 0, '', '', $TargetSizeR4, $DistanceR4);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR4, 5, 3, 1, 5, 3, 1, 'R4W', 'Sveigbogi 40+ Kvenna/ Recurve 40+ Women', 1, 240, 255, 0, 0, '', '', $TargetSizeR4, $DistanceR4);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR5, 5, 3, 1, 5, 3, 1, 'R5M', 'Sveigbogi 50+ Karla/ Recurve 50+ Men', 1, 240, 255, 0, 0, '', '', $TargetSizeR5, $DistanceR5);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR5, 5, 3, 1, 5, 3, 1, 'R5W', 'Sveigbogi 50+ Kvenna/ Recurve 50+ Women', 1, 240, 255, 0, 0, '', '', $TargetSizeR5, $DistanceR5);
	    CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR6, 5, 3, 1, 5, 3, 1, 'R6M', 'Sveigbogi 60+ Karla/ Recurve 60+ Men', 1, 240, 255, 0, 0, '', '', $TargetSizeR6, $DistanceR6);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR6, 5, 3, 1, 5, 3, 1, 'R6W', 'Sveigbogi 60+ Kvenna/ Recurve 60+ Women', 1, 240, 255, 0, 0, '', '', $TargetSizeR6, $DistanceR6);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR7, 5, 3, 1, 5, 3, 1, 'R7M', 'Sveigbogi 70+ Karla/ Recurve 70+ Men', 1, 240, 255, 0, 0, '', '', $TargetSizeR7, $DistanceR7);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR7, 5, 3, 1, 5, 3, 1, 'R7W', 'Sveigbogi 70+ Kvenna/ Recurve 70+ Women', 1, 240, 255, 0, 0, '', '', $TargetSizeR7, $DistanceR7);		
		// Trissubogi
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC,  5, 3, 1, 5, 3, 1, 'CM',  'Trissubogi Karla/ Compound Men', 0, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceC);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC,  5, 3, 1, 5, 3, 1, 'CW',  'Trissubogi Kvenna/ Compound Women', 0, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCJ, 5, 3, 1, 5, 3, 1, 'CJM', 'Trissubogi U21 Karla/ Compound Junior Men', 0, 240, 255, 0, 0, '', '', $TargetSizeCJ, $DistanceCJ);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCJ, 5, 3, 1, 5, 3, 1, 'CJW', 'Trissubogi U21 Kvenna/ Compound Junior Women', 0, 240, 255, 0, 0, '', '', $TargetSizeCJ, $DistanceCJ);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCC, 5, 3, 1, 5, 3, 1, 'CCM', 'Trissubogi U18 Karla/ Compound Cadet Men', 0, 240, 255, 0, 0, '', '', $TargetSizeCC, $DistanceCC);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCC, 5, 3, 1, 5, 3, 1, 'CCW', 'Trissubogi U18 Kvenna/ Compound Cadet Women', 0, 240, 255, 0, 0, '', '', $TargetSizeCC, $DistanceCC);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCN, 5, 3, 1, 5, 3, 1, 'CNM', 'Trissubogi U16 Karla/ Compound Nordic Men', 0, 240, 255, 0, 0, '', '', $TargetSizeCN, $DistanceCN);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCN, 5, 3, 1, 5, 3, 1, 'CNW', 'Trissubogi U16 Kvenna/ Compound Nordic Women', 0, 240, 255, 0, 0, '', '', $TargetSizeCN, $DistanceCN);
	    CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCK, 5, 3, 1, 5, 3, 1, 'CKM', 'Trissubogi U14 Karla', 0, 240, 255, 0, 0, '', '', $TargetSizeCK, $DistanceCK);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCK, 5, 3, 1, 5, 3, 1, 'CKW', 'Trissubogi U14 Kvenna', 0, 240, 255, 0, 0, '', '', $TargetSizeCK, $DistanceCK);		
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC3, 5, 3, 1, 5, 3, 1, 'C3M', 'Trissubogi 30+ Karla/ Compound Men 30+', 0, 240, 255, 0, 0, '', '', $TargetSizeC3, $DistanceC3);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC3, 5, 3, 1, 5, 3, 1, 'C3W', 'Trissubogi 30+ Kvenna/ Compound Women 30+', 0, 240, 255, 0, 0, '', '', $TargetSizeC3, $DistanceC3);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC4, 5, 3, 1, 5, 3, 1, 'C4M', 'Trissubogi 40+ Karla/ Compound Men 40+', 0, 240, 255, 0, 0, '', '', $TargetSizeC4, $DistanceC4);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC4, 5, 3, 1, 5, 3, 1, 'C4W', 'Trissubogi 40+ Kvenna/ Compound Women 40+', 0, 240, 255, 0, 0, '', '', $TargetSizeC4, $DistanceC4);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC5, 5, 3, 1, 5, 3, 1, 'C5M', 'Trissubogi 50+ Karla/ Compound Men 50+', 0, 240, 255, 0, 0, '', '', $TargetSizeC5, $DistanceC5);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC5, 5, 3, 1, 5, 3, 1, 'C5W', 'Trissubogi 50+ Kvenna/ Compound Women 50+', 0, 240, 255, 0, 0, '', '', $TargetSizeC5, $DistanceC5);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC6, 5, 3, 1, 5, 3, 1, 'C6M', 'Trissubogi 60+ Karla/ Compound Men 60+', 0, 240, 255, 0, 0, '', '', $TargetSizeC6, $DistanceC6);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC6, 5, 3, 1, 5, 3, 1, 'C6W', 'Trissubogi 60+ Kvenna/ Compound Women 60+', 0, 240, 255, 0, 0, '', '', $TargetSizeC6, $DistanceC6);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC7, 5, 3, 1, 5, 3, 1, 'C7M', 'Trissubogi 70+ Karla/ Compound Men 70+', 0, 240, 255, 0, 0, '', '', $TargetSizeC7, $DistanceC7);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC7, 5, 3, 1, 5, 3, 1, 'C7W', 'Trissubogi 70+ Kvenna/ Compound Women 70+', 0, 240, 255, 0, 0, '', '', $TargetSizeC7, $DistanceC7);		
		// Berbogi
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB,  5, 3, 1, 5, 3, 1, 'BM',  'Berbogi Karla/ Barebow Men', 1, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceB);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB,  5, 3, 1, 5, 3, 1, 'BW',  'Berbogi Kvenna/ Barebow Women', 1, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceB);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBJ, 5, 3, 1, 5, 3, 1, 'BJM', 'Berbogi U21 Karla/ Barebow Junior Men', 1, 240, 255, 0, 0, '', '', $TargetSizeBJ, $DistanceBJ);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBJ, 5, 3, 1, 5, 3, 1, 'BJW', 'Berbogi U21 Kvenna/ Barebow Junior Women', 1, 240, 255, 0, 0, '', '', $TargetSizeBJ, $DistanceBJ);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBC, 5, 3, 1, 5, 3, 1, 'BCM', 'Berbogi U18 Karla/ Barebow Cadet Men', 1, 240, 255, 0, 0, '', '', $TargetSizeBC, $DistanceBC);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBC, 5, 3, 1, 5, 3, 1, 'BCW', 'Berbogi U18 Kvenna/ Barebow Cadet Women', 1, 240, 255, 0, 0, '', '', $TargetSizeBC, $DistanceBC);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBN, 5, 3, 1, 5, 3, 1, 'BNM', 'Berbogi U16 Karla/ Barebow Nordic Men', 1, 240, 255, 0, 0, '', '', $TargetSizeBN, $DistanceBN);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBN, 5, 3, 1, 5, 3, 1, 'BNW', 'Berbogi U16 Kvenna/ Barebow Nordic Women', 1, 240, 255, 0, 0, '', '', $TargetSizeBN, $DistanceBN);
	    CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBK, 5, 3, 1, 5, 3, 1, 'BKM', 'Berbogi U14 Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeBK, $DistanceBK);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBK, 5, 3, 1, 5, 3, 1, 'BKW', 'Berbogi U14 Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeBK, $DistanceBK);		
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB3, 5, 3, 1, 5, 3, 1, 'B3M', 'Berbogi 30+ Karla/ Barebow Men 30+', 1, 240, 255, 0, 0, '', '', $TargetSizeB3, $DistanceB3);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB3, 5, 3, 1, 5, 3, 1, 'B3W', 'Berbogi 30+ Kvenna/ Barebow Women 30+', 1, 240, 255, 0, 0, '', '', $TargetSizeB3, $DistanceB3);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB4, 5, 3, 1, 5, 3, 1, 'B4M', 'Berbogi 40+ Karla/ Barebow Men 40+', 1, 240, 255, 0, 0, '', '', $TargetSizeB4, $DistanceB4);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB4, 5, 3, 1, 5, 3, 1, 'B4W', 'Berbogi 40+ Kvenna/ Barebow Women 40+', 1, 240, 255, 0, 0, '', '', $TargetSizeB4, $DistanceB4);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB5, 5, 3, 1, 5, 3, 1, 'B5M', 'Berbogi 50+ Karla/ Barebow Men 50+', 1, 240, 255, 0, 0, '', '', $TargetSizeB5, $DistanceB5);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB5, 5, 3, 1, 5, 3, 1, 'B5W', 'Berbogi 50+ Kvenna/ Barebow Women 50+', 1, 240, 255, 0, 0, '', '', $TargetSizeB5, $DistanceB5);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB6, 5, 3, 1, 5, 3, 1, 'B6M', 'Berbogi 60+ Karla/ Barebow Men 60+', 1, 240, 255, 0, 0, '', '', $TargetSizeB6, $DistanceB6);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB6, 5, 3, 1, 5, 3, 1, 'B6W', 'Berbogi 60+ Kvenna/ Barebow Women 60+', 1, 240, 255, 0, 0, '', '', $TargetSizeB6, $DistanceB6);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB7, 5, 3, 1, 5, 3, 1, 'B7M', 'Berbogi 70+ Karla/ Barebow Men 70+', 1, 240, 255, 0, 0, '', '', $TargetSizeB7, $DistanceB7);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB7, 5, 3, 1, 5, 3, 1, 'B7W', 'Berbogi 70+ Kvenna/ Barebow Women 70+', 1, 240, 255, 0, 0, '', '', $TargetSizeB7, $DistanceB7);		
		}

	if ($SubRule==2) { //"Only Adult Classes" Bara opinn flokkur útsláttarkeppni EINSTAKLINGA COMPLETE
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'RM',  'Sveigbogi Karla/ Recurve Men', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'RW',  'Sveigbogi Kvenna/ Recurve Women', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC, 5, 3, 1, 5, 3, 1, 'CM',  'Trissubogi Karla/ Compound Men', 0, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceC);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC, 5, 3, 1, 5, 3, 1, 'CW',  'Trissubogi Kvenna/ Compound Women', 0, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceC);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB, 5, 3, 1, 5, 3, 1, 'BM',  'Berbogi Karla/ Barebow Men', 1, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceB);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB, 5, 3, 1, 5, 3, 1, 'BW',  'Berbogi Kvenna/ Barebow Women', 1, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceB);
		}

    if ($SubRule==3) { // "All-in-one class" Bara opinn flokkur UNISEX ÚTSLÁTTARKEPPNI EINSTAKLINGA COMPLETE
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'R',  'Sveigbogi/ Recurve', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);    
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC, 5, 3, 1, 5, 3, 1, 'C',  'Trissubogi/ Compound', 0, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceC);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB, 5, 3, 1, 5, 3, 1, 'B',  'Berbogi/ Barebow', 1, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceB);
		}
		
	if ($SubRule==4) { // "Every Classes" Allir aldurflokkar ÚTSLÁTTARKEPPNI UNGMENNAMÓT COMPLETE
		// Sveigbogi
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRJ, 5, 3, 1, 5, 3, 1, 'RJM', 'Sveigbogi U21 Karla/ Recurve Junior Men', 1, 240, 255, 0, 0, '', '', $TargetSizeRJ, $DistanceRJ);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRJ, 5, 3, 1, 5, 3, 1, 'RJW', 'Sveigbogi U21 Kvenna/ Recurve Junior Women', 1, 240, 255, 0, 0, '', '', $TargetSizeRJ, $DistanceRJ);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRC, 5, 3, 1, 5, 3, 1, 'RCM', 'Sveigbogi U18 Karla/ Recurve Cadet Men', 1, 240, 255, 0, 0, '', '', $TargetSizeRC, $DistanceRC);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRC, 5, 3, 1, 5, 3, 1, 'RCW', 'Sveigbogi U18 Kvenna/ Recurve Cadet Women', 1, 240, 255, 0, 0, '', '', $TargetSizeRC, $DistanceRC);
	    CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRN, 5, 3, 1, 5, 3, 1, 'RNM', 'Sveigbogi U16 Karla/ Recurve Nordic Men', 1, 240, 255, 0, 0, '', '', $TargetSizeRN, $DistanceRN);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRN, 5, 3, 1, 5, 3, 1, 'RNW', 'Sveigbogi U16 Kvenna/ Recurve Nordic Women', 1, 240, 255, 0, 0, '', '', $TargetSizeRN, $DistanceRN);
	    CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRK, 5, 3, 1, 5, 3, 1, 'RKM', 'Sveigbogi U14 Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeRK, $DistanceRK);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRK, 5, 3, 1, 5, 3, 1, 'RKW', 'Sveigbogi U14 Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeRK, $DistanceRK);	
		// Trissubogi
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCJ, 5, 3, 1, 5, 3, 1, 'CJM', 'Trissubogi U21 Karla/ Compound Junior Men', 0, 240, 255, 0, 0, '', '', $TargetSizeCJ, $DistanceCJ);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCJ, 5, 3, 1, 5, 3, 1, 'CJW', 'Trissubogi U21 Kvenna/ Compound Junior Women', 0, 240, 255, 0, 0, '', '', $TargetSizeCJ, $DistanceCJ);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCC, 5, 3, 1, 5, 3, 1, 'CCM', 'Trissubogi U18 Karla/ Compound Cadet Men', 0, 240, 255, 0, 0, '', '', $TargetSizeCC, $DistanceCC);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCC, 5, 3, 1, 5, 3, 1, 'CCW', 'Trissubogi U18 Kvenna/ Compound Cadet Women', 0, 240, 255, 0, 0, '', '', $TargetSizeCC, $DistanceCC);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCN, 5, 3, 1, 5, 3, 1, 'CNM', 'Trissubogi U16 Karla/ Compound Nordic Men', 0, 240, 255, 0, 0, '', '', $TargetSizeCN, $DistanceCN);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCN, 5, 3, 1, 5, 3, 1, 'CNW', 'Trissubogi U16 Kvenna/ Compound Nordic Women', 0, 240, 255, 0, 0, '', '', $TargetSizeCN, $DistanceCN);
	    CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCK, 5, 3, 1, 5, 3, 1, 'CKM', 'Trissubogi U14 Karla', 0, 240, 255, 0, 0, '', '', $TargetSizeCK, $DistanceCK);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCK, 5, 3, 1, 5, 3, 1, 'CKW', 'Trissubogi U14 Kvenna', 0, 240, 255, 0, 0, '', '', $TargetSizeCK, $DistanceCK);
		// Berbogi
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBJ, 5, 3, 1, 5, 3, 1, 'BJM', 'Berbogi U21 Karla/ Barebow Junior Men', 1, 240, 255, 0, 0, '', '', $TargetSizeBJ, $DistanceBJ);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBJ, 5, 3, 1, 5, 3, 1, 'BJW', 'Berbogi U21 Kvenna/ Barebow Junior Women', 1, 240, 255, 0, 0, '', '', $TargetSizeBJ, $DistanceBJ);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBC, 5, 3, 1, 5, 3, 1, 'BCM', 'Berbogi U18 Karla/ Barebow Cadet Men', 1, 240, 255, 0, 0, '', '', $TargetSizeBC, $DistanceBC);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBC, 5, 3, 1, 5, 3, 1, 'BCW', 'Berbogi U18 Kvenna/ Barebow Cadet Women', 1, 240, 255, 0, 0, '', '', $TargetSizeBC, $DistanceBC);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBN, 5, 3, 1, 5, 3, 1, 'BNM', 'Berbogi U16 Karla/ Barebow Nordic Men', 1, 240, 255, 0, 0, '', '', $TargetSizeBN, $DistanceBN);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBN, 5, 3, 1, 5, 3, 1, 'BNW', 'Berbogi U16 Kvenna/ Barebow Nordic Women', 1, 240, 255, 0, 0, '', '', $TargetSizeBN, $DistanceBN);
	    CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBK, 5, 3, 1, 5, 3, 1, 'BKM', 'Berbogi U14 Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeBK, $DistanceBK);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBK, 5, 3, 1, 5, 3, 1, 'BKW', 'Berbogi U14 Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeBK, $DistanceBK);
		}

	if ($SubRule==5) { // "Set Kids classes" ÖLDUNGAMÓT ÚTSLÆTTIR COMPLETE
		// Sveigbogi
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR3, 5, 3, 1, 5, 3, 1, 'R3M', 'Sveigbogi 30+ Karla/ Men', 1, 240, 255, 0, 0, '', '', $TargetSizeR3, $DistanceR3);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR3, 5, 3, 1, 5, 3, 1, 'R3W', 'Sveigbogi 30+ Kvenna/ Women', 1, 240, 255, 0, 0, '', '', $TargetSizeR3, $DistanceR3);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR4, 5, 3, 1, 5, 3, 1, 'R4M', 'Sveigbogi 40+ Karla/ Men', 1, 240, 255, 0, 0, '', '', $TargetSizeR4, $DistanceR4);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR4, 5, 3, 1, 5, 3, 1, 'R4W', 'Sveigbogi 40+ Kvenna/ Women', 1, 240, 255, 0, 0, '', '', $TargetSizeR4, $DistanceR4);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR5, 5, 3, 1, 5, 3, 1, 'R5M', 'Sveigbogi 50+ Master Karla/ Men', 1, 240, 255, 0, 0, '', '', $TargetSizeR5, $DistanceR5);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR5, 5, 3, 1, 5, 3, 1, 'R5W', 'Sveigbogi 50+ Master Kvenna/ Women', 1, 240, 255, 0, 0, '', '', $TargetSizeR5, $DistanceR5);
	    CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR6, 5, 3, 1, 5, 3, 1, 'R6M', 'Sveigbogi 60+ Karla/ Men', 1, 240, 255, 0, 0, '', '', $TargetSizeR6, $DistanceR6);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR6, 5, 3, 1, 5, 3, 1, 'R6W', 'Sveigbogi 60+ Kvenna/ Women', 1, 240, 255, 0, 0, '', '', $TargetSizeR6, $DistanceR6);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR7, 5, 3, 1, 5, 3, 1, 'R7M', 'Sveigbogi 70+ Karla/ Men', 1, 240, 255, 0, 0, '', '', $TargetSizeR7, $DistanceR7);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR7, 5, 3, 1, 5, 3, 1, 'R7W', 'Sveigbogi 70+ Kvenna/ Women', 1, 240, 255, 0, 0, '', '', $TargetSizeR7, $DistanceR7);
 		// Trissubogi
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC3, 5, 3, 1, 5, 3, 1, 'C3M', 'Trissubogi 30+ Karla/ Men', 0, 240, 255, 0, 0, '', '', $TargetSizeC3, $DistanceC3);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC3, 5, 3, 1, 5, 3, 1, 'C3W', 'Trissubogi 30+ Kvenna/ Women', 0, 240, 255, 0, 0, '', '', $TargetSizeC3, $DistanceC3);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC4, 5, 3, 1, 5, 3, 1, 'C4M', 'Trissubogi 40+ Karla/ Men', 0, 240, 255, 0, 0, '', '', $TargetSizeC4, $DistanceC4);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC4, 5, 3, 1, 5, 3, 1, 'C4W', 'Trissubogi 40+ Kvenna/ Women', 0, 240, 255, 0, 0, '', '', $TargetSizeC4, $DistanceC4);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC5, 5, 3, 1, 5, 3, 1, 'C5M', 'Trissubogi 50+ Master Karla/ Men', 0, 240, 255, 0, 0, '', '', $TargetSizeC5, $DistanceC5);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC5, 5, 3, 1, 5, 3, 1, 'C5W', 'Trissubogi 50+ Master Kvenna/ Women', 0, 240, 255, 0, 0, '', '', $TargetSizeC5, $DistanceC5);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC6, 5, 3, 1, 5, 3, 1, 'C6M', 'Trissubogi 60+ Karla/ Men', 0, 240, 255, 0, 0, '', '', $TargetSizeC6, $DistanceC6);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC6, 5, 3, 1, 5, 3, 1, 'C6W', 'Trissubogi 60+ Kvenna/ Women', 0, 240, 255, 0, 0, '', '', $TargetSizeC6, $DistanceC6);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC7, 5, 3, 1, 5, 3, 1, 'C7M', 'Trissubogi 70+ Karla/ Men', 0, 240, 255, 0, 0, '', '', $TargetSizeC7, $DistanceC7);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC7, 5, 3, 1, 5, 3, 1, 'C7W', 'Trissubogi 70+ Kvenna/ Women', 0, 240, 255, 0, 0, '', '', $TargetSizeC7, $DistanceC7);
		// Berbogi
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB3, 5, 3, 1, 5, 3, 1, 'B3M', 'Berbogi 30+ Karla/ Men', 1, 240, 255, 0, 0, '', '', $TargetSizeB3, $DistanceB3);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB3, 5, 3, 1, 5, 3, 1, 'B3W', 'Berbogi 30+ Kvenna/ Women', 1, 240, 255, 0, 0, '', '', $TargetSizeB3, $DistanceB3);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB4, 5, 3, 1, 5, 3, 1, 'B4M', 'Berbogi 40+ Karla/ Men', 1, 240, 255, 0, 0, '', '', $TargetSizeB4, $DistanceB4);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB4, 5, 3, 1, 5, 3, 1, 'B4W', 'Berbogi 40+ Kvenna/ Women', 1, 240, 255, 0, 0, '', '', $TargetSizeB4, $DistanceB4);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB5, 5, 3, 1, 5, 3, 1, 'B5M', 'Berbogi 50+ Master Karla/ Men', 1, 240, 255, 0, 0, '', '', $TargetSizeB5, $DistanceB5);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB5, 5, 3, 1, 5, 3, 1, 'B5W', 'Berbogi 50+ Master Kvenna/ Women', 1, 240, 255, 0, 0, '', '', $TargetSizeB5, $DistanceB5);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB6, 5, 3, 1, 5, 3, 1, 'B6M', 'Berbogi 60+ Karla/ Men', 1, 240, 255, 0, 0, '', '', $TargetSizeB6, $DistanceB6);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB6, 5, 3, 1, 5, 3, 1, 'B6W', 'Berbogi 60+ Kvenna/ Women', 1, 240, 255, 0, 0, '', '', $TargetSizeB6, $DistanceB6);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB7, 5, 3, 1, 5, 3, 1, 'B7M', 'Berbogi 70+ Karla/ Men', 1, 240, 255, 0, 0, '', '', $TargetSizeB7, $DistanceB7);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB7, 5, 3, 1, 5, 3, 1, 'B7W', 'Berbogi 70+ Kvenna/ Women', 1, 240, 255, 0, 0, '', '', $TargetSizeB7, $DistanceB7);
		}
		
	if ($SubRule==6) { // "All Classes WA 4 Pools" UNISEX allir aldursflokkar útsláttarkeppni EINSTAKLINGA COMPLETE
		// Sveigbogi
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR,  5, 3, 1, 5, 3, 1, 'RU',  'Sveigbogi Opinn', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRJ, 5, 3, 1, 5, 3, 1, 'RJU', 'Sveigbogi U21', 1, 240, 255, 0, 0, '', '', $TargetSizeRJ, $DistanceRJ);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRC, 5, 3, 1, 5, 3, 1, 'RCU', 'Sveigbogi U18', 1, 240, 255, 0, 0, '', '', $TargetSizeRC, $DistanceRC);
	    CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRN, 5, 3, 1, 5, 3, 1, 'RNU', 'Sveigbogi U16', 1, 240, 255, 0, 0, '', '', $TargetSizeRN, $DistanceRN);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRK, 5, 3, 1, 5, 3, 1, 'RKU', 'Sveigbogi U14', 1, 240, 255, 0, 0, '', '', $TargetSizeRK, $DistanceRK);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR3, 5, 3, 1, 5, 3, 1, 'R3U', 'Sveigbogi 30+', 1, 240, 255, 0, 0, '', '', $TargetSizeR3, $DistanceR3);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR4, 5, 3, 1, 5, 3, 1, 'R4U', 'Sveigbogi 40+', 1, 240, 255, 0, 0, '', '', $TargetSizeR4, $DistanceR4);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR5, 5, 3, 1, 5, 3, 1, 'R5U', 'Sveigbogi 50+', 1, 240, 255, 0, 0, '', '', $TargetSizeR5, $DistanceR5);
	    CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR6, 5, 3, 1, 5, 3, 1, 'R6U', 'Sveigbogi 60+', 1, 240, 255, 0, 0, '', '', $TargetSizeR6, $DistanceR6);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR7, 5, 3, 1, 5, 3, 1, 'R7U', 'Sveigbogi 70+', 1, 240, 255, 0, 0, '', '', $TargetSizeR7, $DistanceR7);
		// Trissubogi
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC,  5, 3, 1, 5, 3, 1, 'CU',  'Trissubogi Opinn', 0, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCJ, 5, 3, 1, 5, 3, 1, 'CJU', 'Trissubogi U21', 0, 240, 255, 0, 0, '', '', $TargetSizeCJ, $DistanceCJ);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCC, 5, 3, 1, 5, 3, 1, 'CCU', 'Trissubogi U18', 0, 240, 255, 0, 0, '', '', $TargetSizeCC, $DistanceCC);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCN, 5, 3, 1, 5, 3, 1, 'CNU', 'Trissubogi U16', 0, 240, 255, 0, 0, '', '', $TargetSizeCN, $DistanceCN);
	    CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCK, 5, 3, 1, 5, 3, 1, 'CKU', 'Trissubogi U14', 0, 240, 255, 0, 0, '', '', $TargetSizeCK, $DistanceCK);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC3, 5, 3, 1, 5, 3, 1, 'C3U', 'Trissubogi 30+', 0, 240, 255, 0, 0, '', '', $TargetSizeC3, $DistanceC3);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC4, 5, 3, 1, 5, 3, 1, 'C4U', 'Trissubogi 40+', 0, 240, 255, 0, 0, '', '', $TargetSizeC4, $DistanceC4);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC5, 5, 3, 1, 5, 3, 1, 'C5U', 'Trissubogi 50+', 0, 240, 255, 0, 0, '', '', $TargetSizeC5, $DistanceC5);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC6, 5, 3, 1, 5, 3, 1, 'C6U', 'Trissubogi 60+', 0, 240, 255, 0, 0, '', '', $TargetSizeC6, $DistanceC6);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC7, 5, 3, 1, 5, 3, 1, 'C7U', 'Trissubogi 70+', 0, 240, 255, 0, 0, '', '', $TargetSizeC7, $DistanceC7);
		// Berbogi
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB,  5, 3, 1, 5, 3, 1, 'BU',  'Berbogi Opinn', 1, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceB);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBJ, 5, 3, 1, 5, 3, 1, 'BJU', 'Berbogi U21', 1, 240, 255, 0, 0, '', '', $TargetSizeBJ, $DistanceBJ);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBC, 5, 3, 1, 5, 3, 1, 'BCU', 'Berbogi U18', 1, 240, 255, 0, 0, '', '', $TargetSizeBC, $DistanceBC);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBN, 5, 3, 1, 5, 3, 1, 'BNU', 'Berbogi U16', 1, 240, 255, 0, 0, '', '', $TargetSizeBN, $DistanceBN);
	    CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBK, 5, 3, 1, 5, 3, 1, 'BKU', 'Berbogi U14', 1, 240, 255, 0, 0, '', '', $TargetSizeBK, $DistanceBK);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB3, 5, 3, 1, 5, 3, 1, 'B3U', 'Berbogi 30+', 1, 240, 255, 0, 0, '', '', $TargetSizeB3, $DistanceB3);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB4, 5, 3, 1, 5, 3, 1, 'B4U', 'Berbogi 40+', 1, 240, 255, 0, 0, '', '', $TargetSizeB4, $DistanceB4);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB5, 5, 3, 1, 5, 3, 1, 'B5U', 'Berbogi 50+', 1, 240, 255, 0, 0, '', '', $TargetSizeB5, $DistanceB5);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB6, 5, 3, 1, 5, 3, 1, 'B6U', 'Berbogi 60+', 1, 240, 255, 0, 0, '', '', $TargetSizeB6, $DistanceB6);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB7, 5, 3, 1, 5, 3, 1, 'B7U', 'Berbogi 70+', 1, 240, 255, 0, 0, '', '', $TargetSizeB7, $DistanceB7);
		}

	// LIÐA útslættir
	if ($SubRule==1) { // "Championship" Allir aldursflokkar útsláttarkeppni LIÐA COMPLETE
		// Til upplýsinga tölurnar á eftir $Targetx standa fyrir "fjöldi umferða","fjöldi örva","fjöldi örva bráðabani" og svo sömu tölur enduteknar. Tölurnar á eftir $i++ eru ? og mixed team 1 er Yes 2 er No
		// Tölurnar á efir distance tengjast því að búa til mörg lið eða 1 lið
		// Sveigbogi
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetR,  4, 6, 3, 4, 6, 3, 'RM',  'Sveigbogi Karla/ Recurve Men', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR, '', 1);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetR,  4, 6, 3, 4, 6, 3, 'RW',  'Sveigbogi Kvenna/ Recurve Women', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRJ, 4, 4, 2, 4, 4, 2, 'RJM', 'Sveigbogi U21 Karla/ Recurve Junior Men', 1, 240, 255, 0, 0, '', '', $TargetSizeRJ, $DistanceRJ, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRJ, 4, 4, 2, 4, 4, 2, 'RJW', 'Sveigbogi U21 Kvenna/ Recurve Junior Women', 1, 240, 255, 0, 0, '', '', $TargetSizeRJ, $DistanceRJ, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRC, 4, 4, 2, 4, 4, 2, 'RCM', 'Sveigbogi U18 Karla/ Recurve Cadet Men', 1, 240, 255, 0, 0, '', '', $TargetSizeRC, $DistanceRC, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRC, 4, 4, 2, 4, 4, 2, 'RCW', 'Sveigbogi U18 Kvenna/ Recurve Cadet Women', 1, 240, 255, 0, 0, '', '', $TargetSizeRC, $DistanceRC, '', 1);
	    CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRN, 4, 4, 2, 4, 4, 2, 'RNM', 'Sveigbogi U16 Karla/ Recurve Nordic Men', 1, 240, 255, 0, 0, '', '', $TargetSizeRN, $DistanceRN, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRN, 4, 4, 2, 4, 4, 2, 'RNW', 'Sveigbogi U16 Kvenna/ Recurve Nordic Women', 1, 240, 255, 0, 0, '', '', $TargetSizeRN, $DistanceRN, '', 1);
	    CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRK, 4, 4, 2, 4, 4, 2, 'RKM', 'Sveigbogi U14 Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeRK, $DistanceRK, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRK, 4, 4, 2, 4, 4, 2, 'RKW', 'Sveigbogi U14 Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeRK, $DistanceRK, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetR5, 4, 4, 2, 4, 4, 2, 'R5M', 'Sveigbogi 50+ Karla/ Recurve Master Men', 1, 240, 255, 0, 0, '', '', $TargetSizeR5, $DistanceR5, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetR5, 4, 4, 2, 4, 4, 2, 'R5W', 'Sveigbogi 50+ Kvenna/ Recurve Master Women', 1, 240, 255, 0, 0, '', '', $TargetSizeR5, $DistanceR5, '', 1);		
		// Trissubogi
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetC,  4, 6, 3, 4, 6, 3, 'CM',  'Trissubogi Karla/ Compound Men', 0, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceC, '', 1);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetC,  4, 6, 3, 4, 6, 3, 'CW',  'Trissubogi Kvenna/ Compound Women', 0, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceC, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCJ, 4, 4, 2, 4, 4, 2, 'CJM', 'Trissubogi U21 Karla/ Compound Junior Men', 0, 240, 255, 0, 0, '', '', $TargetSizeCJ, $DistanceCJ, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCJ, 4, 4, 2, 4, 4, 2, 'CJW', 'Trissubogi U21 Kvenna/ Compound Junior Women', 0, 240, 255, 0, 0, '', '', $TargetSizeCJ, $DistanceCJ, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCC, 4, 4, 2, 4, 4, 2, 'CCM', 'Trissubogi U18 Karla/ Compound Cadet Men', 0, 240, 255, 0, 0, '', '', $TargetSizeCC, $DistanceCC, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCC, 4, 4, 2, 4, 4, 2, 'CCW', 'Trissubogi U18 Kvenna/ Compound Cadet Women', 0, 240, 255, 0, 0, '', '', $TargetSizeCC, $DistanceCC, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCN, 4, 4, 2, 4, 4, 2, 'CNM', 'Trissubogi U16 Karla/ Compound Nordic Men', 0, 240, 255, 0, 0, '', '', $TargetSizeCN, $DistanceCN, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCN, 4, 4, 2, 4, 4, 2, 'CNW', 'Trissubogi U16 Kvenna/ Compound Nordic Women', 0, 240, 255, 0, 0, '', '', $TargetSizeCN, $DistanceCN, '', 1);
	    CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCK, 4, 4, 2, 4, 4, 2, 'CKM', 'Trissubogi U14 Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeCK, $DistanceCK, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCK, 4, 4, 2, 4, 4, 2, 'CKW', 'Trissubogi U14 Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeCK, $DistanceCK, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetC5, 4, 4, 2, 4, 4, 2, 'C5M', 'Trissubogi 50+ Karla/ Compound Master Men', 0, 240, 255, 0, 0, '', '', $TargetSizeC5, $DistanceC5, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetC5, 4, 4, 2, 4, 4, 2, 'C5W', 'Trissubogi 50+ Kvenna/ Compound Master Women', 0, 240, 255, 0, 0, '', '', $TargetSizeC5, $DistanceC5, '', 1);		
		// Berbogi
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetB,  4, 6, 3, 4, 6, 3, 'BM',  'Berbogi Karla/ Barebow Men', 1, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceB, '', 1);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetB,  4, 6, 3, 4, 6, 3, 'BW',  'Berbogi Kvenna/ Barebow Women', 1, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceB, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBJ, 4, 4, 2, 4, 4, 2, 'BJM', 'Berbogi U21 Karla/ Barebow Junior Men', 1, 240, 255, 0, 0, '', '', $TargetSizeBJ, $DistanceBJ, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBJ, 4, 4, 2, 4, 4, 2, 'BJW', 'Berbogi U21 Kvenna/ Barebow Junior Women', 1, 240, 255, 0, 0, '', '', $TargetSizeBJ, $DistanceBJ, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBC, 4, 4, 2, 4, 4, 2, 'BCM', 'Berbogi U18 Karla/ Barebow Cadet Men', 1, 240, 255, 0, 0, '', '', $TargetSizeBC, $DistanceBC, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBC, 4, 4, 2, 4, 4, 2, 'BCW', 'Berbogi U18 Kvenna/ Barebow Cadet Women', 1, 240, 255, 0, 0, '', '', $TargetSizeBC, $DistanceBC, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBN, 4, 4, 2, 4, 4, 2, 'BNM', 'Berbogi U16 Karla/ Barebow Nordic Men', 1, 240, 255, 0, 0, '', '', $TargetSizeBN, $DistanceBN, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBN, 4, 4, 2, 4, 4, 2, 'BNW', 'Berbogi U16 Kvenna/ Barebow Nordic Women', 1, 240, 255, 0, 0, '', '', $TargetSizeBN, $DistanceBN, '', 1);
	    CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBK, 4, 4, 2, 4, 4, 2, 'BKM', 'Berbogi U14 Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeBK, $DistanceBK, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBK, 4, 4, 2, 4, 4, 2, 'BKW', 'Berbogi U14 Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeBK, $DistanceBK, '', 1);	
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetB5, 4, 4, 2, 4, 4, 2, 'B5M', 'Berbogi 50+ Karla/ Barebow Master Men', 1, 240, 255, 0, 0, '', '', $TargetSizeB5, $DistanceB5, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetB5, 4, 4, 2, 4, 4, 2, 'B5W', 'Berbogi 50+ Kvenna/ Barebow Master Women', 1, 240, 255, 0, 0, '', '', $TargetSizeB5, $DistanceB5, '', 1);		
		}
		
	if ($SubRule==2) { //"Only Adult Classes" Bara opinn flokkur útsláttarkeppni LIÐA COMPLETE
		// Sveigbogi
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetR, 4, 6, 3, 4, 6, 3, 'RM', 'Recurve Team Men/ Sveigbogi Liðakeppni Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR, '', 1);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetR, 4, 6, 3, 4, 6, 3, 'RW', 'Recurve Team Women/ Sveigbogi Liðakeppni Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR, '', 1);
		// Trissubogi
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetC, 4, 6, 3, 4, 6, 3, 'CM', 'Compound Team Men/ Trissubogi Liðakeppni Karla', 0, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceC, '', 1);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetC, 4, 6, 3, 4, 6, 3, 'CW', 'Compound Team Women/ Trissubogi Liðakeppni Kvenna', 0, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceC, '', 1);
		// Berbogi
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetB, 4, 6, 3, 4, 6, 3, 'BM', 'Barebow Team Men/ Berbogi Liðakeppni Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceB, '', 1);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetB, 4, 6, 3, 4, 6, 3, 'BW', 'Barebow Team Women/ Berbogi Liðakeppni Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceB, '', 1);
		}

	if ($SubRule==4) { // "EVERY CLASSES" YOUTH SERIES útsláttarkeppni LIÐA COMPLETE
		// Til upplýsinga tölurnar á eftir $Targetx standa fyrir "fjöldi umferða","fjöldi örva","fjöldi örva bráðabani" og svo sömu tölur enduteknar.
		// Sveigbogi
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRJ, 4, 4, 2, 4, 4, 2, 'RJM', 'Sveigbogi U21 Karla/ Recurve Junior Men', 1, 240, 255, 0, 0, '', '', $TargetSizeRJ, $DistanceRJ, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRJ, 4, 4, 2, 4, 4, 2, 'RJW', 'Sveigbogi U21 Kvenna/ Recurve Junior Women', 1, 240, 255, 0, 0, '', '', $TargetSizeRJ, $DistanceRJ, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRC, 4, 4, 2, 4, 4, 2, 'RCM', 'Sveigbogi U18 Karla/ Recurve Cadet Men', 1, 240, 255, 0, 0, '', '', $TargetSizeRC, $DistanceRC, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRC, 4, 4, 2, 4, 4, 2, 'RCW', 'Sveigbogi U18 Kvenna/ Recurve Cadet Women', 1, 240, 255, 0, 0, '', '', $TargetSizeRC, $DistanceRC, '', 1);
	    CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRN, 4, 4, 2, 4, 4, 2, 'RNM', 'Sveigbogi U16 Karla/ Recurve Nordic Men', 1, 240, 255, 0, 0, '', '', $TargetSizeRN, $DistanceRN, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRN, 4, 4, 2, 4, 4, 2, 'RNW', 'Sveigbogi U16 Kvenna/ Recurve Nordic Women', 1, 240, 255, 0, 0, '', '', $TargetSizeRN, $DistanceRN, '', 1);
	    CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRK, 4, 4, 2, 4, 4, 2, 'RKM', 'Sveigbogi U14 Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeRK, $DistanceRK, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRK, 4, 4, 2, 4, 4, 2, 'RKW', 'Sveigbogi U14 Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeRK, $DistanceRK, '', 1);
		// Trissubogi
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCJ, 4, 4, 2, 4, 4, 2, 'CJM', 'Trissubogi U21 Karla/ Compound Junior Men', 0, 240, 255, 0, 0, '', '', $TargetSizeCJ, $DistanceCJ, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCJ, 4, 4, 2, 4, 4, 2, 'CJW', 'Trissubogi U21 Kvenna/ Compound Junior Women', 0, 240, 255, 0, 0, '', '', $TargetSizeCJ, $DistanceCJ, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCC, 4, 4, 2, 4, 4, 2, 'CCM', 'Trissubogi U18 Karla/ Compound Cadet Men', 0, 240, 255, 0, 0, '', '', $TargetSizeCC, $DistanceCC, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCC, 4, 4, 2, 4, 4, 2, 'CCW', 'Trissubogi U18 Kvenna/ Compound Cadet Women', 0, 240, 255, 0, 0, '', '', $TargetSizeCC, $DistanceCC, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCN, 4, 4, 2, 4, 4, 2, 'CNM', 'Trissubogi U16 Karla/ Compound Nordic Men', 0, 240, 255, 0, 0, '', '', $TargetSizeCN, $DistanceCN, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCN, 4, 4, 2, 4, 4, 2, 'CNW', 'Trissubogi U16 Kvenna/ Compound Nordic Women', 0, 240, 255, 0, 0, '', '', $TargetSizeCN, $DistanceCN, '', 1);
	    CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCK, 4, 4, 2, 4, 4, 2, 'CKM', 'Trissubogi U14 Karla', 0, 240, 255, 0, 0, '', '', $TargetSizeCK, $DistanceCK, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCK, 4, 4, 2, 4, 4, 2, 'CKW', 'Trissubogi U14 Kvenna', 0, 240, 255, 0, 0, '', '', $TargetSizeCK, $DistanceCK, '', 1);
		// Berbogi
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBJ, 4, 4, 2, 4, 4, 2, 'BJM', 'Berbogi U21 Karla/ Barebow Junior Men', 1, 240, 255, 0, 0, '', '', $TargetSizeBJ, $DistanceBJ, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBJ, 4, 4, 2, 4, 4, 2, 'BJW', 'Berbogi U21 Kvenna/ Barebow Junior Women', 1, 240, 255, 0, 0, '', '', $TargetSizeBJ, $DistanceBJ, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBC, 4, 4, 2, 4, 4, 2, 'BCM', 'Berbogi U18 Karla/ Barebow Cadet Men', 1, 240, 255, 0, 0, '', '', $TargetSizeBC, $DistanceBC, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBC, 4, 4, 2, 4, 4, 2, 'BCW', 'Berbogi U18 Kvenna/ Barebow Cadet Women', 1, 240, 255, 0, 0, '', '', $TargetSizeBC, $DistanceBC, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBN, 4, 4, 2, 4, 4, 2, 'BNM', 'Berbogi U16 Karla/ Barebow Nordic Men', 1, 240, 255, 0, 0, '', '', $TargetSizeBN, $DistanceBN, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBN, 4, 4, 2, 4, 4, 2, 'BNW', 'Berbogi U16 Kvenna/ Barebow Nordic Women', 1, 240, 255, 0, 0, '', '', $TargetSizeBN, $DistanceBN, '', 1);
	    CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBK, 4, 4, 2, 4, 4, 2, 'BKM', 'Berbogi U14 Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeBK, $DistanceBK, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBK, 4, 4, 2, 4, 4, 2, 'BKW', 'Berbogi U14 Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeBK, $DistanceBK, '', 1);
		}
		
	if ($SubRule==5) { // "ONLY YOUTH CLASSES" ÖLDUNGAFLOKKA útsláttarkeppni LIÐA ÓKLÁRAÐ COMPLETE FOR NOW ÞARF AÐ ÁKVEÐA HVAÐ Á AÐ GERA MEÐ MASTERS LIÐAKEPPNI
		// Til upplýsinga tölurnar á eftir $Targetx standa fyrir "fjöldi umferða","fjöldi örva","fjöldi örva bráðabani" og svo sömu tölur enduteknar.
		// Sveigbogi
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetR5, 4, 4, 2, 4, 4, 2, 'R5M', 'Sveigbogi Karla 50+/ Recurve Men Masters', 		1, 240, 255, 0, 0, '', '', $TargetSizeR5, $DistanceR5, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetR5, 4, 4, 2, 4, 4, 2, 'R5W', 'Sveigbogi Kvenna 50+/ Recurve Women Masters', 	1, 240, 255, 0, 0, '', '', $TargetSizeR5, $DistanceR5, '', 1);		
		// Trissubogi
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetC5, 4, 4, 2, 4, 4, 2, 'C5M', 'Trissubogi Karla 50+/ Compound Men Masters', 		0, 240, 255, 0, 0, '', '', $TargetSizeC5, $DistanceC5, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetC5, 4, 4, 2, 4, 4, 2, 'C5W', 'Trissubogi Kvenna 50+/ Compound Women Masters', 	0, 240, 255, 0, 0, '', '', $TargetSizeC5, $DistanceC5, '', 1);		
		// Berbogi
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetB5, 4, 4, 2, 4, 4, 2, 'B5M', 'Berbogi Karla 50+/ Barebow Men Masters', 			1, 240, 255, 0, 0, '', '', $TargetSizeB5, $DistanceB5, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetB5, 4, 4, 2, 4, 4, 2, 'B5W', 'Berbogi Kvenna 50+/ Barebow Women Masters', 		1, 240, 255, 0, 0, '', '', $TargetSizeB5, $DistanceB5, '', 1);		
		
		/* 
		REGLUSETT HÉR FYRIR NEÐAN EF TIL KEMUR AÐ VIÐ VERÐUM MEÐ 2 MANNA LIÐAKEPPNI Í ÖLLUM ALDURSFLOKKUM Í FRAMTÍÐINNI		
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetR3, 4, 4, 2, 4, 4, 2, 'R3M', 'Sveigbogi Karla 30+/ Recurve Men 30+', 		1, 240, 255, 0, 0, '', '', $TargetSizeR3, $DistanceR3, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetR3, 4, 4, 2, 4, 4, 2, 'R3W', 'Sveigbogi Kvenna 30+/ Recurve Women 30+', 	1, 240, 255, 0, 0, '', '', $TargetSizeR3, $DistanceR3, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetR4, 4, 4, 2, 4, 4, 2, 'R4M', 'Sveigbogi Karla 40+/ Recurve Men 40+', 		1, 240, 255, 0, 0, '', '', $TargetSizeR4, $DistanceR4, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetR4, 4, 4, 2, 4, 4, 2, 'R4W', 'Sveigbogi Kvenna 40+/ Recurve Women 40+', 	1, 240, 255, 0, 0, '', '', $TargetSizeR4, $DistanceR4, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetR5, 4, 4, 2, 4, 4, 2, 'R5M', 'Sveigbogi Karla 50+/ Recurve Men 50+', 		1, 240, 255, 0, 0, '', '', $TargetSizeR5, $DistanceR5, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetR5, 4, 4, 2, 4, 4, 2, 'R5W', 'Sveigbogi Kvenna 50+/ Recurve Women 50+', 	1, 240, 255, 0, 0, '', '', $TargetSizeR5, $DistanceR5, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetR6, 4, 4, 2, 4, 4, 2, 'R6M', 'Sveigbogi Karla 60+/ Recurve Men 60+', 		1, 240, 255, 0, 0, '', '', $TargetSizeR6, $DistanceR6, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetR6, 4, 4, 2, 4, 4, 2, 'R6W', 'Sveigbogi Kvenna 60+/ Recurve Women 60+', 	1, 240, 255, 0, 0, '', '', $TargetSizeR6, $DistanceR6, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetR7, 4, 4, 2, 4, 4, 2, 'R7M', 'Sveigbogi Karla 70+/ Recurve Men 70+', 		1, 240, 255, 0, 0, '', '', $TargetSizeR7, $DistanceR7, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetR7, 4, 4, 2, 4, 4, 2, 'R7W', 'Sveigbogi Kvenna 70+/ Recurve Women 70+', 	1, 240, 255, 0, 0, '', '', $TargetSizeR7, $DistanceR7, '', 1);		
		
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetC3, 4, 4, 2, 4, 4, 2, 'C3M', 'Trissubogi Karla 30+/ Compound Men 30+', 	0, 240, 255, 0, 0, '', '', $TargetSizeC3, $DistanceC3, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetC3, 4, 4, 2, 4, 4, 2, 'C3W', 'Trissubogi Kvenna 30+/ Compound Women 30+', 0, 240, 255, 0, 0, '', '', $TargetSizeC3, $DistanceC3, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetC4, 4, 4, 2, 4, 4, 2, 'C4M', 'Trissubogi Karla 40+/ Compound Men 40+', 	0, 240, 255, 0, 0, '', '', $TargetSizeC4, $DistanceC4, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetC4, 4, 4, 2, 4, 4, 2, 'C4W', 'Trissubogi Kvenna 40+/ Compound Women 40+', 0, 240, 255, 0, 0, '', '', $TargetSizeC4, $DistanceC4, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetC5, 4, 4, 2, 4, 4, 2, 'C5M', 'Trissubogi Karla 50+/ Compound Men 50+', 	0, 240, 255, 0, 0, '', '', $TargetSizeC5, $DistanceC5, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetC5, 4, 4, 2, 4, 4, 2, 'C5W', 'Trissubogi Kvenna 50+/ Compound Women 50+', 0, 240, 255, 0, 0, '', '', $TargetSizeC5, $DistanceC5, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetC6, 4, 4, 2, 4, 4, 2, 'C6M', 'Trissubogi Karla 60+/ Compound Men 60+', 	0, 240, 255, 0, 0, '', '', $TargetSizeC6, $DistanceC6, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetC6, 4, 4, 2, 4, 4, 2, 'C6W', 'Trissubogi Kvenna 60+/ Compound Women 60+', 0, 240, 255, 0, 0, '', '', $TargetSizeC6, $DistanceC6, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetC7, 4, 4, 2, 4, 4, 2, 'C7M', 'Trissubogi Karla 70+/ Compound Men 70+', 	0, 240, 255, 0, 0, '', '', $TargetSizeC7, $DistanceC7, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetC7, 4, 4, 2, 4, 4, 2, 'C7W', 'Trissubogi Kvenna 70+/ Compound Women 70+', 0, 240, 255, 0, 0, '', '', $TargetSizeC7, $DistanceC7, '', 1);		
		
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetB3, 4, 4, 2, 4, 4, 2, 'B3M', 'Berbogi Karla 30+/ Barebow Men 30+', 		1, 240, 255, 0, 0, '', '', $TargetSizeB3, $DistanceB3, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetB3, 4, 4, 2, 4, 4, 2, 'B3W', 'Berbogi Kvenna 30+/ Barebow Women 30+', 	1, 240, 255, 0, 0, '', '', $TargetSizeB3, $DistanceB3, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetB4, 4, 4, 2, 4, 4, 2, 'B4M', 'Berbogi Karla 40+/ Barebow Men 40+', 		1, 240, 255, 0, 0, '', '', $TargetSizeB4, $DistanceB4, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetB4, 4, 4, 2, 4, 4, 2, 'B4W', 'Berbogi Kvenna 40+/ Barebow Women 40+', 	1, 240, 255, 0, 0, '', '', $TargetSizeB4, $DistanceB4, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetB5, 4, 4, 2, 4, 4, 2, 'B5M', 'Berbogi Karla 50+/ Barebow Men 50+', 		1, 240, 255, 0, 0, '', '', $TargetSizeB5, $DistanceB5, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetB5, 4, 4, 2, 4, 4, 2, 'B5W', 'Berbogi Kvenna 50+/ Barebow Women 50+', 	1, 240, 255, 0, 0, '', '', $TargetSizeB5, $DistanceB5, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetB6, 4, 4, 2, 4, 4, 2, 'B6M', 'Berbogi Karla 60+/ Barebow Men 60+', 		1, 240, 255, 0, 0, '', '', $TargetSizeB6, $DistanceB6, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetB6, 4, 4, 2, 4, 4, 2, 'B6W', 'Berbogi Kvenna 60+/ Barebow Women 60+', 	1, 240, 255, 0, 0, '', '', $TargetSizeB6, $DistanceB6, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetB7, 4, 4, 2, 4, 4, 2, 'B7M', 'Berbogi Karla 70+/ Barebow Men 70+', 		1, 240, 255, 0, 0, '', '', $TargetSizeB7, $DistanceB7, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetB7, 4, 4, 2, 4, 4, 2, 'B7W', 'Berbogi Kvenna 70+/ Barebow Women 70+', 	1, 240, 255, 0, 0, '', '', $TargetSizeB7, $DistanceB7, '', 1);

		*/
		}		
		
	//if($Outdoor) { // MIXED TEAM Útsláttarkeppni bætist við þegar mót er utandyra
	if ($SubRule==1) { // "Championship" Allir aldursflokkar útsláttarkeppni MIXED TEAM COMPLETE.
		// Recurve Mixed Team - Sveigboga Paraliðakeppni útsláttur
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetR,  4, 4, 2, 4, 4, 2, 'RX',	 'Sveigbogi Parakeppni/ Recurve Mixed Team', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR, '', 1);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRJ, 4, 4, 2, 4, 4, 2, 'RJX', 'Sveigbogi Parakeppni U21/ Recurve Mixed Team Junior', 1, 240, 255, 0, 0, '', '', $TargetSizeRJ, $DistanceRJ, '', 1);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRC, 4, 4, 2, 4, 4, 2, 'RCX', 'Sveigbogi Parakeppni U18/ Recurve Mixed Team Cadet', 1, 240, 255, 0, 0, '', '', $TargetSizeRC, $DistanceRC, '', 1);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRN, 4, 4, 2, 4, 4, 2, 'RNX', 'Sveigbogi Parakeppni U16/ Recurve Mixed Team Nordic ', 1, 240, 255, 0, 0, '', '', $TargetSizeRN, $DistanceRN, '', 1);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRK, 4, 4, 2, 4, 4, 2, 'RKX', 'Sveigbogi Parakeppni U14', 1, 240, 255, 0, 0, '', '', $TargetSizeRK, $DistanceRK, '', 1);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetR5, 4, 4, 2, 4, 4, 2, 'R5X', 'Sveigbogi Parakeppni 50+/ Recurve Mixed Team Masters ', 1, 240, 255, 0, 0, '', '', $TargetSizeR5, $DistanceR5, '', 1);		
		// Compound MIXED TEAM Trissuboga Paraliðakeppni útsláttur
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetC,  4, 4, 2, 4, 4, 2, 'CX',  'Trissubogi Parakeppni/ Compound Mixed Team', 0, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceC, '', 1);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCJ, 4, 4, 2, 4, 4, 2, 'CJX', 'Trissubogi Parakeppni U21/ Compound Mixed Team Junior', 0, 240, 255, 0, 0, '', '', $TargetSizeCJ, $DistanceCJ, '', 1);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCC, 4, 4, 2, 4, 4, 2, 'CCX', 'Trissubogi Parakeppni U18/ Compound Mixed Team Cadet', 0, 240, 255, 0, 0, '', '', $TargetSizeCC, $DistanceCC, '', 1);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCN, 4, 4, 2, 4, 4, 2, 'CNX', 'Trissubogi Parakeppni U16/ Compound Mixed Team Nordic', 0, 240, 255, 0, 0, '', '', $TargetSizeCN, $DistanceCN, '', 1);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCK, 4, 4, 2, 4, 4, 2, 'CKX', 'Trissubogi Parakeppni U14', 0, 240, 255, 0, 0, '', '', $TargetSizeCK, $DistanceCK, '', 1);	
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetC5, 4, 4, 2, 4, 4, 2, 'C5X', 'Trissubogi Parakeppni 50+/ Compound Mixed Team Masters', 0, 240, 255, 0, 0, '', '', $TargetSizeC5, $DistanceC5, '', 1);		
		// BAREBOW MIXED TEAM Berboga Paraliðakeppni útsláttur
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetB,  4, 4, 2, 4, 4, 2, 'BX',  'Berbogi Parakeppni/ Barebow Mixed Team', 1, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceB, '', 1);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBJ, 4, 4, 2, 4, 4, 2, 'BJX', 'Berbogi Parakeppni U21/ Barebow Mixed Team Junior', 1, 240, 255, 0, 0, '', '', $TargetSizeBJ, $DistanceBJ, '', 1);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBC, 4, 4, 2, 4, 4, 2, 'BCX', 'Berbogi Parakeppni U18/ Barebow Mixed Teamn Cadet', 1, 240, 255, 0, 0, '', '', $TargetSizeBC, $DistanceBC, '', 1);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBN, 4, 4, 2, 4, 4, 2, 'BNX', 'Berbogi Parakeppni U16/ Barebow Mixed Team Nordic', 1, 240, 255, 0, 0, '', '', $TargetSizeBN, $DistanceBN, '', 1);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBK, 4, 4, 2, 4, 4, 2, 'BKX', 'Berbogi Parakeppni U14', 1, 240, 255, 0, 0, '', '', $TargetSizeBK, $DistanceBK, '', 1);	
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetB5, 4, 4, 2, 4, 4, 2, 'B5X', 'Berbogi Parakeppni 50+/ Barebow Mixed Team Masters', 1, 240, 255, 0, 0, '', '', $TargetSizeB5, $DistanceB5, '', 1);		
		}
		
	if ($SubRule==2) { //"Only Adult Classes" Bara opinn flokkur útsláttarkeppni MIXED TEAM COMPLETE
		// Recurve MIXED TEAM
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetR, 4, 4, 2, 4, 4, 2, 'RX', 'Sveigbogi Parakeppni/ Recurve Mixed Team', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR, '', 1);
		// Compound MIXED TEAM
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetC, 4, 4, 2, 4, 4, 2, 'CX', 'Trissubogi Parakeppni/ Compound Mixed Team', 0, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceC, '', 1);
		// BAREBOW MIXED TEAM 
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetB, 4, 4, 2, 4, 4, 2, 'BX', 'Berbogi Parakeppni/ Barebow Mixed Team', 1, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceB, '', 1);
		}
		
	if ($SubRule==4) { // "EVERY CLASSES" Ungmenna útsláttarkeppni MIXED TEAM COMPLETE
		// Recurve Mixed Team - Sveigboga Paraliðakeppni útsláttur
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRJ, 4, 4, 2, 4, 4, 2, 'RJX', 'Sveigbogi Parakeppni U21/ Recurve Mixed Team Junior', 1, 240, 255, 0, 0, '', '', $TargetSizeRJ, $DistanceRJ, '', 1);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRC, 4, 4, 2, 4, 4, 2, 'RCX', 'Sveigbogi Parakeppni U18/ Recurve Mixed Team Cadet', 1, 240, 255, 0, 0, '', '', $TargetSizeRC, $DistanceRC, '', 1);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRN, 4, 4, 2, 4, 4, 2, 'RNX', 'Sveigbogi Parakeppni U16/ Recurve Mixed Team Nordic ', 1, 240, 255, 0, 0, '', '', $TargetSizeRN, $DistanceRN, '', 1);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRK, 4, 4, 2, 4, 4, 2, 'RKX', 'Sveigbogi Parakeppni U14', 1, 240, 255, 0, 0, '', '', $TargetSizeRK, $DistanceRK, '', 1);		
		// Compound MIXED TEAM Trissuboga Paraliðakeppni útsláttur
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCJ, 4, 4, 2, 4, 4, 2, 'CJX', 'Trissubogi Parakeppni U21/ Compound Mixed Team Junior', 0, 240, 255, 0, 0, '', '', $TargetSizeCJ, $DistanceCJ, '', 1);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCC, 4, 4, 2, 4, 4, 2, 'CCX', 'Trissubogi Parakeppni U18/ Compound Mixed Team Cadet', 0, 240, 255, 0, 0, '', '', $TargetSizeCC, $DistanceCC, '', 1);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCN, 4, 4, 2, 4, 4, 2, 'CNX', 'Trissubogi Parakeppni U16/ Compound Mixed Team Nordic', 0, 240, 255, 0, 0, '', '', $TargetSizeCN, $DistanceCN, '', 1);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCK, 4, 4, 2, 4, 4, 2, 'CKX', 'Trissubogi Parakeppni U14', 0, 240, 255, 0, 0, '', '', $TargetSizeCK, $DistanceCK, '', 1);		
		// BAREBOW MIXED TEAM Berboga Paraliðakeppni útsláttur
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBJ, 4, 4, 2, 4, 4, 2, 'BJX', 'Berbogi Parakeppni U21/ Barebow Mixed Team Junior', 1, 240, 255, 0, 0, '', '', $TargetSizeBJ, $DistanceBJ, '', 1);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBC, 4, 4, 2, 4, 4, 2, 'BCX', 'Berbogi Parakeppni U18/ Barebow Mixed Teamn Cadet', 1, 240, 255, 0, 0, '', '', $TargetSizeBC, $DistanceBC, '', 1);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBN, 4, 4, 2, 4, 4, 2, 'BNX', 'Berbogi Parakeppni U16/ Barebow Mixed Team Nordic', 1, 240, 255, 0, 0, '', '', $TargetSizeBN, $DistanceBN, '', 1);
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBK, 4, 4, 2, 4, 4, 2, 'BKX', 'Berbogi Parakeppni U14', 1, 240, 255, 0, 0, '', '', $TargetSizeBK, $DistanceBK, '', 1);		
		}	
		
	if ($SubRule==5) { // "ONLY YOUTH CLASSES" ÖLDUNGAFLOKKA útsláttarkeppni MIXED TEAM COMPLETE
		// Recurve Mixed Team - Sveigboga Paraliðakeppni útsláttur
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetR5, 4, 4, 2, 4, 4, 2, 'R5X', 'Sveigbogi Parakeppni 50+/ Recurve Mixed Team Masters ', 1, 240, 255, 0, 0, '', '', $TargetSizeR5, $DistanceR5, '', 1);	
		// Compound MIXED TEAM Trissuboga Paraliðakeppni útsláttur
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetC5, 4, 4, 2, 4, 4, 2, 'C5X', 'Trissubogi Parakeppni 50+/ Compound Mixed Team Masters', 0, 240, 255, 0, 0, '', '', $TargetSizeC5, $DistanceC5, '', 1);		
		// BAREBOW MIXED TEAM Berboga Paraliðakeppni útsláttur
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetB5, 4, 4, 2, 4, 4, 2, 'B5X', 'Berbogi Parakeppni 50+/ Barebow Mixed Team Masters', 1, 240, 255, 0, 0, '', '', $TargetSizeB5, $DistanceB5, '', 1);		
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
		InsertClassEvent($TourId, 0, 1, 'RJM',  'R', 'JM');
		InsertClassEvent($TourId, 0, 1, 'RJW',  'R', 'JW');
		InsertClassEvent($TourId, 0, 1, 'RCM',  'R', 'CM');
		InsertClassEvent($TourId, 0, 1, 'RCW',  'R', 'CW');
		InsertClassEvent($TourId, 0, 1, 'RNM',  'R', 'NM');
		InsertClassEvent($TourId, 0, 1, 'RNW',  'R', 'NW');
		InsertClassEvent($TourId, 0, 1, 'RKM',  'R', 'KM');
		InsertClassEvent($TourId, 0, 1, 'RKW',  'R', 'KW');
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
		InsertClassEvent($TourId, 0, 1, 'CM',  'C', 'M');
		InsertClassEvent($TourId, 0, 1, 'CW',  'C', 'W');
		InsertClassEvent($TourId, 0, 1, 'CJM',  'C', 'JM');
		InsertClassEvent($TourId, 0, 1, 'CJW',  'C', 'JW');
		InsertClassEvent($TourId, 0, 1, 'CCM',  'C', 'CM');
		InsertClassEvent($TourId, 0, 1, 'CCW',  'C', 'CW');
		InsertClassEvent($TourId, 0, 1, 'CNM',  'C', 'NM');
		InsertClassEvent($TourId, 0, 1, 'CNW',  'C', 'NW');
		InsertClassEvent($TourId, 0, 1, 'CKM',  'C', 'KM');
		InsertClassEvent($TourId, 0, 1, 'CKW',  'C', 'KW');
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
		InsertClassEvent($TourId, 0, 1, 'BM',  'B', 'M');
		InsertClassEvent($TourId, 0, 1, 'BW',  'B', 'W');
		InsertClassEvent($TourId, 0, 1, 'BJM',  'B', 'JM');
		InsertClassEvent($TourId, 0, 1, 'BJW',  'B', 'JW');
		InsertClassEvent($TourId, 0, 1, 'BCM',  'B', 'CM');
		InsertClassEvent($TourId, 0, 1, 'BCW',  'B', 'CW');
		InsertClassEvent($TourId, 0, 1, 'BNM',  'B', 'NM');
		InsertClassEvent($TourId, 0, 1, 'BNW',  'B', 'NW');
		InsertClassEvent($TourId, 0, 1, 'BKM',  'B', 'KM');
		InsertClassEvent($TourId, 0, 1, 'BKW',  'B', 'KW');
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
		
		if ($SubRule==6) { // "All Classes WA 4 Pools" UNISEX allir aldursflokkar, útsláttarkeppni EINSTAKLINGA tenging COMPLETE
		InsertClassEvent($TourId, 0, 1, 'RU',  'R', 'U');
		InsertClassEvent($TourId, 0, 1, 'RJU',  'R', 'JU');
		InsertClassEvent($TourId, 0, 1, 'RCU',  'R', 'CU');
		InsertClassEvent($TourId, 0, 1, 'RNU',  'R', 'NU');
		InsertClassEvent($TourId, 0, 1, 'RKU',  'R', 'KU');
		InsertClassEvent($TourId, 0, 1, 'R3U',  'R', '3U');
		InsertClassEvent($TourId, 0, 1, 'R4U',  'R', '4U');
		InsertClassEvent($TourId, 0, 1, 'R5U',  'R', '5U');
		InsertClassEvent($TourId, 0, 1, 'R6U',  'R', '6U');
		InsertClassEvent($TourId, 0, 1, 'R7U',  'R', '7U');
		// Compound Einstaklinga tenging, þeas hver má keppa í hvaða útslætti
		InsertClassEvent($TourId, 0, 1, 'CU',  'C', 'U');
		InsertClassEvent($TourId, 0, 1, 'CJU',  'C', 'JU');
		InsertClassEvent($TourId, 0, 1, 'CCU',  'C', 'CU');
		InsertClassEvent($TourId, 0, 1, 'CNU',  'C', 'NU');
		InsertClassEvent($TourId, 0, 1, 'CKU',  'C', 'KU');
		InsertClassEvent($TourId, 0, 1, 'C3U',  'C', '3U');
		InsertClassEvent($TourId, 0, 1, 'C4U',  'C', '4U');
		InsertClassEvent($TourId, 0, 1, 'C5U',  'C', '5U');
		InsertClassEvent($TourId, 0, 1, 'C6U',  'C', '6U');
		InsertClassEvent($TourId, 0, 1, 'C7U',  'C', '7U');
		// Barebow Einstaklinga tenging, þeas hver má keppa í hvaða útslætti
		InsertClassEvent($TourId, 0, 1, 'BU',  'B', 'U');
		InsertClassEvent($TourId, 0, 1, 'BJU',  'B', 'JU');
		InsertClassEvent($TourId, 0, 1, 'BCU',  'B', 'CU');
		InsertClassEvent($TourId, 0, 1, 'BNU',  'B', 'NU');
		InsertClassEvent($TourId, 0, 1, 'BKU',  'B', 'KU');
		InsertClassEvent($TourId, 0, 1, 'B3U',  'B', '3U');
		InsertClassEvent($TourId, 0, 1, 'B4U',  'B', '4U');
		InsertClassEvent($TourId, 0, 1, 'B5U',  'B', '5U');
		InsertClassEvent($TourId, 0, 1, 'B6U',  'B', '6U');
		InsertClassEvent($TourId, 0, 1, 'B7U',  'B', '7U');
		}	
  
	//TENGINGAR Í LIÐAÚTSLÆTTI
	if ($SubRule==1) { // "Championship" Allir aldursflokkar útsláttarkeppni LIÐA tenging COMPLETE
		//Recurve Team Liðatenging 3 manna standard worldarchery í opnum flokki, 2 manna liðakeppni annarsstaðar
		InsertClassEvent($TourId, 1, 3, 'RM', 'R', 'M');
		InsertClassEvent($TourId, 1, 3, 'RW', 'R', 'W');
		InsertClassEvent($TourId, 1, 2, 'RJM', 'R', 'JM');
		InsertClassEvent($TourId, 1, 2, 'RJW', 'R', 'JW');
		InsertClassEvent($TourId, 1, 2, 'RCM', 'R', 'CM');
		InsertClassEvent($TourId, 1, 2, 'RCW', 'R', 'CW');
		InsertClassEvent($TourId, 1, 2, 'RNM', 'R', 'NM');
		InsertClassEvent($TourId, 1, 2, 'RNW', 'R', 'NW');
		InsertClassEvent($TourId, 1, 2, 'RKM', 'R', 'KM');
		InsertClassEvent($TourId, 1, 2, 'RKW', 'R', 'KW');
		InsertClassEvent($TourId, 1, 3, 'RM', 'R', '3M');
		InsertClassEvent($TourId, 1, 3, 'RW', 'R', '3W');
		InsertClassEvent($TourId, 1, 3, 'RM', 'R', '4M');
		InsertClassEvent($TourId, 1, 3, 'RW', 'R', '4W');		
		InsertClassEvent($TourId, 1, 2, 'R5M', 'R', '5M');
		InsertClassEvent($TourId, 1, 2, 'R5W', 'R', '5W');
		InsertClassEvent($TourId, 1, 2, 'R5M', 'R', '6M');
		InsertClassEvent($TourId, 1, 2, 'R5W', 'R', '6W');	
		InsertClassEvent($TourId, 1, 2, 'R5M', 'R', '7M');
		InsertClassEvent($TourId, 1, 2, 'R5W', 'R', '7W');			

		//Compound Team Liðatenging 3 manna standard worldarchery í opnum flokki, 2 manna liðakeppni annarsstaðar
		InsertClassEvent($TourId, 1, 3, 'CM', 'C', 'M');
		InsertClassEvent($TourId, 1, 3, 'CW', 'C', 'W');
		InsertClassEvent($TourId, 1, 2, 'CJM', 'C', 'JM');
		InsertClassEvent($TourId, 1, 2, 'CJW', 'C', 'JW');
		InsertClassEvent($TourId, 1, 2, 'CCM', 'C', 'CM');
		InsertClassEvent($TourId, 1, 2, 'CCW', 'C', 'CW');
		InsertClassEvent($TourId, 1, 2, 'CNM', 'C', 'NM');
		InsertClassEvent($TourId, 1, 2, 'CNW', 'C', 'NW');
		InsertClassEvent($TourId, 1, 2, 'CKM', 'C', 'KM');
		InsertClassEvent($TourId, 1, 2, 'CKW', 'C', 'KW');
		InsertClassEvent($TourId, 1, 3, 'CM', 'C', '3M');
		InsertClassEvent($TourId, 1, 3, 'CW', 'C', '3W');
		InsertClassEvent($TourId, 1, 3, 'CM', 'C', '4M');
		InsertClassEvent($TourId, 1, 3, 'CW', 'C', '4W');		
		InsertClassEvent($TourId, 1, 2, 'C5M', 'C', '5M');
		InsertClassEvent($TourId, 1, 2, 'C5W', 'C', '5W');
		InsertClassEvent($TourId, 1, 2, 'C5M', 'C', '6M');
		InsertClassEvent($TourId, 1, 2, 'C5W', 'C', '6W');	
		InsertClassEvent($TourId, 1, 2, 'C5M', 'C', '7M');
		InsertClassEvent($TourId, 1, 2, 'C5W', 'C', '7W');			
	
		//Barebow Team Liðatenging 3 manna standard worldarchery í opnum flokki, 2 manna liðakeppni annarsstaðar
		InsertClassEvent($TourId, 1, 3, 'BM', 'B', 'M');
		InsertClassEvent($TourId, 1, 3, 'BW', 'B', 'W');
		InsertClassEvent($TourId, 1, 2, 'BJM', 'B', 'JM');
		InsertClassEvent($TourId, 1, 2, 'BJW', 'B', 'JW');
		InsertClassEvent($TourId, 1, 2, 'BCM', 'B', 'CM');
		InsertClassEvent($TourId, 1, 2, 'BCW', 'B', 'CW');
		InsertClassEvent($TourId, 1, 2, 'BNM', 'B', 'NM');
		InsertClassEvent($TourId, 1, 2, 'BNW', 'B', 'NW');
		InsertClassEvent($TourId, 1, 2, 'BKM', 'B', 'KM');
		InsertClassEvent($TourId, 1, 2, 'BKW', 'B', 'KW');		
		InsertClassEvent($TourId, 1, 3, 'BM', 'B', '3M');
		InsertClassEvent($TourId, 1, 3, 'BW', 'B', '3W');
		InsertClassEvent($TourId, 1, 3, 'BM', 'B', '4M');
		InsertClassEvent($TourId, 1, 3, 'BW', 'B', '4W');
		InsertClassEvent($TourId, 1, 2, 'B5M', 'B', '5M');
		InsertClassEvent($TourId, 1, 2, 'B5W', 'B', '5W');		
		InsertClassEvent($TourId, 1, 2, 'B5M', 'B', '6M');
		InsertClassEvent($TourId, 1, 2, 'B5W', 'B', '6W');	
		InsertClassEvent($TourId, 1, 2, 'B5M', 'B', '7M');
		InsertClassEvent($TourId, 1, 2, 'B5W', 'B', '7W');		
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
				
	if ($SubRule==4) { // "EVERY CLASSES" Ungmenna LIÐA tenging COMPLETE
		//Recurve Team Liðatenging 2 manna worldarchery af youth Olympics og Universiade
		InsertClassEvent($TourId, 1, 2, 'RJM',  'R', 'JM');
		InsertClassEvent($TourId, 1, 2, 'RJW',  'R', 'JW');
		InsertClassEvent($TourId, 1, 2, 'RCM',  'R', 'CM');
		InsertClassEvent($TourId, 1, 2, 'RCW',  'R', 'CW');
		InsertClassEvent($TourId, 1, 2, 'RNM',  'R', 'NM');
		InsertClassEvent($TourId, 1, 2, 'RNW',  'R', 'NW');
		InsertClassEvent($TourId, 1, 2, 'RKM',  'R', 'KM');
		InsertClassEvent($TourId, 1, 2, 'RKW',  'R', 'KW');
		//Compound Team Liðatenging 2 manna worldarchery af youth Olympics og Universiade
		InsertClassEvent($TourId, 1, 2, 'CJM',  'C', 'JM');
		InsertClassEvent($TourId, 1, 2, 'CJW',  'C', 'JW');
		InsertClassEvent($TourId, 1, 2, 'CCM',  'C', 'CM');
		InsertClassEvent($TourId, 1, 2, 'CCW',  'C', 'CW');
		InsertClassEvent($TourId, 1, 2, 'CNM',  'C', 'NM');
		InsertClassEvent($TourId, 1, 2, 'CNW',  'C', 'NW');
		InsertClassEvent($TourId, 1, 2, 'CKM',  'C', 'KM');
		InsertClassEvent($TourId, 1, 2, 'CKW',  'C', 'KW');
		//Barebow Team Liðatenging 2 manna worldarchery af youth Olympics og Universiade
		InsertClassEvent($TourId, 1, 2, 'BJM',  'B', 'JM');
		InsertClassEvent($TourId, 1, 2, 'BJW',  'B', 'JW');
		InsertClassEvent($TourId, 1, 2, 'BCM',  'B', 'CM');
		InsertClassEvent($TourId, 1, 2, 'BCW',  'B', 'CW');
		InsertClassEvent($TourId, 1, 2, 'BNM',  'B', 'NM');
		InsertClassEvent($TourId, 1, 2, 'BNW',  'B', 'NW');
		InsertClassEvent($TourId, 1, 2, 'BKM',  'B', 'KM');
		InsertClassEvent($TourId, 1, 2, 'BKW',  'B', 'KW');
		}
		
	if ($SubRule==5) { // "Only youth classes" Öldunga LIÐA tenging COMPLETE
		//Recurve Team Liðatenging 2 manna worldarchery af youth Olympics og Universiade
		InsertClassEvent($TourId, 1, 2, 'R5M', 'R', '5M');
		InsertClassEvent($TourId, 1, 2, 'R5W', 'R', '5W');
		InsertClassEvent($TourId, 1, 2, 'R5M', 'R', '6M');
		InsertClassEvent($TourId, 1, 2, 'R5W', 'R', '6W');	
		InsertClassEvent($TourId, 1, 2, 'R5M', 'R', '7M');
		InsertClassEvent($TourId, 1, 2, 'R5W', 'R', '7W');	
		//Compound Team Liðatenging 2 manna worldarchery af youth Olympics og Universiade
		InsertClassEvent($TourId, 1, 2, 'C5M', 'C', '5M');
		InsertClassEvent($TourId, 1, 2, 'C5W', 'C', '5W');
		InsertClassEvent($TourId, 1, 2, 'C5M', 'C', '6M');
		InsertClassEvent($TourId, 1, 2, 'C5W', 'C', '6W');	
		InsertClassEvent($TourId, 1, 2, 'C5M', 'C', '7M');
		InsertClassEvent($TourId, 1, 2, 'C5W', 'C', '7W');	
		//Barebow Team Liðatenging 2 manna worldarchery af youth Olympics og Universiade
		InsertClassEvent($TourId, 1, 2, 'B5M', 'B', '5M');
		InsertClassEvent($TourId, 1, 2, 'B5W', 'B', '5W');
		InsertClassEvent($TourId, 1, 2, 'B5M', 'B', '6M');
		InsertClassEvent($TourId, 1, 2, 'B5W', 'B', '6W');	
		InsertClassEvent($TourId, 1, 2, 'B5M', 'B', '7M');
		InsertClassEvent($TourId, 1, 2, 'B5W', 'B', '7W');	
		}	
	
	
	
	
	//if($Outdoor) { // TENGINGAR Í MIXED TEAM ÚTSLÆTTI COMPLETE
	if ($SubRule==1) { // "Championship" Allir aldursflokkar útsláttarkeppni MIXED TEAM tenging COMPLETE
		// Recurve Mixed Team
		InsertClassEvent($TourId, 1, 1, 'RX',  'R', 'M');
		InsertClassEvent($TourId, 2, 1, 'RX',  'R', 'W');
		InsertClassEvent($TourId, 1, 1, 'RJX',  'R', 'JM');
		InsertClassEvent($TourId, 2, 1, 'RJX',  'R', 'JW');
		InsertClassEvent($TourId, 1, 1, 'RCX',  'R', 'CM');
		InsertClassEvent($TourId, 2, 1, 'RCX',  'R', 'CW');
		InsertClassEvent($TourId, 1, 1, 'RNX',  'R', 'NM');
		InsertClassEvent($TourId, 2, 1, 'RNX',  'R', 'NW');
		InsertClassEvent($TourId, 1, 1, 'RKX',  'R', 'KM');
		InsertClassEvent($TourId, 2, 1, 'RKX',  'R', 'KW');
		InsertClassEvent($TourId, 1, 1, 'RX',  'R', '3M');
		InsertClassEvent($TourId, 2, 1, 'RX',  'R', '3W');	
		InsertClassEvent($TourId, 1, 1, 'RX',  'R', '4M');
		InsertClassEvent($TourId, 2, 1, 'RX',  'R', '4W');			
		InsertClassEvent($TourId, 1, 1, 'R5X',  'R', '5M');
		InsertClassEvent($TourId, 2, 1, 'R5X',  'R', '5W');	
		InsertClassEvent($TourId, 1, 1, 'R5X',  'R', '6M');
		InsertClassEvent($TourId, 2, 1, 'R5X',  'R', '6W');	
		InsertClassEvent($TourId, 1, 1, 'R5X',  'R', '7M');
		InsertClassEvent($TourId, 2, 1, 'R5X',  'R', '7W');			
		// Compound Mixed Team
		InsertClassEvent($TourId, 1, 1, 'CX',  'C', 'M');
		InsertClassEvent($TourId, 2, 1, 'CX',  'C', 'W');
		InsertClassEvent($TourId, 1, 1, 'CJX',  'C', 'JM');
		InsertClassEvent($TourId, 2, 1, 'CJX',  'C', 'JW');
		InsertClassEvent($TourId, 1, 1, 'CCX',  'C', 'CM');
		InsertClassEvent($TourId, 2, 1, 'CCX',  'C', 'CW');
		InsertClassEvent($TourId, 1, 1, 'CNX',  'C', 'NM');
		InsertClassEvent($TourId, 2, 1, 'CNX',  'C', 'NW');
		InsertClassEvent($TourId, 1, 1, 'CKX',  'C', 'KM');
		InsertClassEvent($TourId, 2, 1, 'CKX',  'C', 'KW');
		InsertClassEvent($TourId, 1, 1, 'CX',  'C', '3M');
		InsertClassEvent($TourId, 2, 1, 'CX',  'C', '3W');
		InsertClassEvent($TourId, 1, 1, 'CX',  'C', '4M');
		InsertClassEvent($TourId, 2, 1, 'CX',  'C', '4W');		
		InsertClassEvent($TourId, 1, 1, 'C5X',  'C', '5M');
		InsertClassEvent($TourId, 2, 1, 'C5X',  'C', '5W');	
		InsertClassEvent($TourId, 1, 1, 'C5X',  'C', '6M');
		InsertClassEvent($TourId, 2, 1, 'C5X',  'C', '6W');	
		InsertClassEvent($TourId, 1, 1, 'C5X',  'C', '7M');
		InsertClassEvent($TourId, 2, 1, 'C5X',  'C', '7W');			
		// Barebow Mixed Team
		InsertClassEvent($TourId, 1, 1, 'BX',  'B', 'M');
		InsertClassEvent($TourId, 2, 1, 'BX',  'B', 'W');
		InsertClassEvent($TourId, 1, 1, 'BJX',  'B', 'JM');
		InsertClassEvent($TourId, 2, 1, 'BJX',  'B', 'JW');
		InsertClassEvent($TourId, 1, 1, 'BCX',  'B', 'CM');
		InsertClassEvent($TourId, 2, 1, 'BCX',  'B', 'CW');
		InsertClassEvent($TourId, 1, 1, 'BNX',  'B', 'NM');
		InsertClassEvent($TourId, 2, 1, 'BNX',  'B', 'NW');
		InsertClassEvent($TourId, 1, 1, 'BKX',  'B', 'KM');
		InsertClassEvent($TourId, 2, 1, 'BKX',  'B', 'KW');
		InsertClassEvent($TourId, 1, 1, 'BX',  'B', '3M');
		InsertClassEvent($TourId, 2, 1, 'BX',  'B', '3W');	
		InsertClassEvent($TourId, 1, 1, 'BX',  'B', '4M');
		InsertClassEvent($TourId, 2, 1, 'BX',  'B', '4W');				
		InsertClassEvent($TourId, 1, 1, 'B5X',  'B', '5M');
		InsertClassEvent($TourId, 2, 1, 'B5X',  'B', '5W');	
		InsertClassEvent($TourId, 1, 1, 'B5X',  'B', '6M');
		InsertClassEvent($TourId, 2, 1, 'B5X',  'B', '6W');		
		InsertClassEvent($TourId, 1, 1, 'B5X',  'B', '7M');
		InsertClassEvent($TourId, 2, 1, 'B5X',  'B', '7W');				
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

	if ($SubRule==4) { // "EVERY CLASSES" Ungmenna MIXED TEAM tenging COMPLETE
		// Recurve Mixed Team
		InsertClassEvent($TourId, 1, 1, 'RJX',  'R', 'JM');
		InsertClassEvent($TourId, 2, 1, 'RJX',  'R', 'JW');
		InsertClassEvent($TourId, 1, 1, 'RCX',  'R', 'CM');
		InsertClassEvent($TourId, 2, 1, 'RCX',  'R', 'CW');
		InsertClassEvent($TourId, 1, 1, 'RNX',  'R', 'NM');
		InsertClassEvent($TourId, 2, 1, 'RNX',  'R', 'NW');
		InsertClassEvent($TourId, 1, 1, 'RKX',  'R', 'KM');
		InsertClassEvent($TourId, 2, 1, 'RKX',  'R', 'KW');		
		// Compound Mixed Team
		InsertClassEvent($TourId, 1, 1, 'CJX',  'C', 'JM');
		InsertClassEvent($TourId, 2, 1, 'CJX',  'C', 'JW');
		InsertClassEvent($TourId, 1, 1, 'CCX',  'C', 'CM');
		InsertClassEvent($TourId, 2, 1, 'CCX',  'C', 'CW');
		InsertClassEvent($TourId, 1, 1, 'CNX',  'C', 'NM');
		InsertClassEvent($TourId, 2, 1, 'CNX',  'C', 'NW');
		InsertClassEvent($TourId, 1, 1, 'CKX',  'C', 'KM');
		InsertClassEvent($TourId, 2, 1, 'CKX',  'C', 'KW');		
		// Barebow Mixed Team
		InsertClassEvent($TourId, 1, 1, 'BJX',  'B', 'JM');
		InsertClassEvent($TourId, 2, 1, 'BJX',  'B', 'JW');
		InsertClassEvent($TourId, 1, 1, 'BCX',  'B', 'CM');
		InsertClassEvent($TourId, 2, 1, 'BCX',  'B', 'CW');
		InsertClassEvent($TourId, 1, 1, 'BNX',  'B', 'NM');
		InsertClassEvent($TourId, 2, 1, 'BNX',  'B', 'NW');
		InsertClassEvent($TourId, 1, 1, 'BKX',  'B', 'KM');
		InsertClassEvent($TourId, 2, 1, 'BKX',  'B', 'KW');		
		}		
		
	if ($SubRule==5) { // "Only youth classes" Öldunga MIXED TEAM tenging COMPLETE
		// Recurve Mixed Team
		InsertClassEvent($TourId, 1, 1, 'R5X',  'R', '5M');
		InsertClassEvent($TourId, 2, 1, 'R5X',  'R', '5W');	
		InsertClassEvent($TourId, 1, 1, 'R5X',  'R', '6M');
		InsertClassEvent($TourId, 2, 1, 'R5X',  'R', '6W');	
		InsertClassEvent($TourId, 1, 1, 'R5X',  'R', '7M');
		InsertClassEvent($TourId, 2, 1, 'R5X',  'R', '7W');			
		// Compound Mixed Team
		InsertClassEvent($TourId, 1, 1, 'C5X',  'C', '5M');
		InsertClassEvent($TourId, 2, 1, 'C5X',  'C', '5W');	
		InsertClassEvent($TourId, 1, 1, 'C5X',  'C', '6M');
		InsertClassEvent($TourId, 2, 1, 'C5X',  'C', '6W');	
		InsertClassEvent($TourId, 1, 1, 'C5X',  'C', '7M');
		InsertClassEvent($TourId, 2, 1, 'C5X',  'C', '7W');		
		// Barebow Mixed Team
		InsertClassEvent($TourId, 1, 1, 'B5X',  'B', '5M');
		InsertClassEvent($TourId, 2, 1, 'B5X',  'B', '5W');	
		InsertClassEvent($TourId, 1, 1, 'B5X',  'B', '6M');
		InsertClassEvent($TourId, 2, 1, 'B5X',  'B', '6W');		
		InsertClassEvent($TourId, 1, 1, 'B5X',  'B', '7M');
		InsertClassEvent($TourId, 2, 1, 'B5X',  'B', '7W');			
		}				
		
		
//}
		
		// Engir MIXED TEAM útslættir fyrir Unisex móta uppsetningar
	
}

