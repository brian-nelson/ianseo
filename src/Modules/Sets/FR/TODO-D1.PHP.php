<?php

/*
- 3 giornate
- ogni giornata ha 5 match (fasi)
- ogni match (fase) consta di 8 incontri
- ogni incontro è fatto da 5 scontri veri e propri
- Scontro 1: tra i 2 primi
- Scontro 2: tra i 2 secondi
- scontro 3: tra le 2 squadre
- scontro 4: tra i 2 terzi
- scontro 5: tra i 2 quarti

PASSO 1:
- done: creare i 16 eventi dove poi andranno infialti gli atleti
- TODO: ogni evento consta di 40 scontri (8*5), quindi servono 80 matchno (16 per fase).
- TODO: fase 1: matchno da 128 a 143
- TODO: fase 2: matchno da 144 a 159
- TODO: fase 3: matchno da 160 a 175
- TODO: fase 4: matchno da 176 a 191
- TODO: fase 5: matchno da 192 a 207

*** ATTENZIONE AL CONTROLLO SCORE CHE NON FACCIA PASSARE GENTE NEL MATCHNO SUCCESSIVO



