<?php
/*
 * Qui ci sono le funzioni per inserire le righe nella tabella F2FGrid.
 * Sono qui e non sparse per i vari moduli perchè anche l'updatedb deve poterle usare ed essere sicuro
 * di trovarle dato che dovrà aggiornare il db.
 * Inoltre ora F2FGrid è una tabella legata al torneo e dato che esiste a priori, queste funzioni esistono 
 * a priori.
 */

/*
 * Ritorna la query che inserisce le righe in F2FGrid per un f2f inglese (tipo 21)
 */
function insertIntoGridForF2F_21($tourId)
{
	$q="
		REPLACE INTO `F2FGrid` (`F2FTournament`, `F2FPhase`, `F2FRound`, `F2FMatchNo1`, `F2FMatchNo2`, `F2FGroup`) VALUES
			({$tourId}, 0, 1, 1, 6, 16),({$tourId}, 0, 1, 2, 5, 16),({$tourId}, 0, 1, 3, 4, 16),({$tourId}, 0, 2, 1, 5, 16),({$tourId}, 0, 2, 2, 3, 16),({$tourId}, 0, 2, 4, 6, 16),
			({$tourId}, 0, 3, 1, 4, 16),({$tourId}, 0, 3, 2, 6, 16),({$tourId}, 0, 3, 3, 5, 16),({$tourId}, 0, 4, 1, 3, 16),({$tourId}, 0, 4, 2, 4, 16),({$tourId}, 0, 4, 5, 6, 16),
			({$tourId}, 0, 5, 1, 2, 16),({$tourId}, 0, 5, 3, 6, 16),({$tourId}, 0, 5, 4, 5, 16),({$tourId}, 1, 1, 1, 6, 8),({$tourId}, 1, 1, 2, 5, 8),({$tourId}, 1, 1, 3, 4, 8),
			({$tourId}, 1, 2, 1, 5, 8),({$tourId}, 1, 2, 2, 3, 8),({$tourId}, 1, 2, 4, 6, 8),({$tourId}, 1, 3, 1, 4, 8),({$tourId}, 1, 3, 2, 6, 8),({$tourId}, 1, 3, 3, 5, 8),
			({$tourId}, 1, 4, 1, 3, 8),({$tourId}, 1, 4, 2, 4, 8),({$tourId}, 1, 4, 5, 6, 8),({$tourId}, 1, 5, 1, 2, 8),({$tourId}, 1, 5, 3, 6, 8),({$tourId}, 1, 5, 4, 5, 8),
			({$tourId}, 2, 1, 1, 6, 4),({$tourId}, 2, 1, 2, 5, 4),({$tourId}, 2, 1, 3, 4, 4),({$tourId}, 2, 2, 1, 5, 4),({$tourId}, 2, 2, 2, 3, 4),({$tourId}, 2, 2, 4, 6, 4),
			({$tourId}, 2, 3, 1, 4, 4),({$tourId}, 2, 3, 2, 6, 4),({$tourId}, 2, 3, 3, 5, 4),({$tourId}, 2, 4, 1, 3, 4),({$tourId}, 2, 4, 2, 4, 4),({$tourId}, 2, 4, 5, 6, 4),
			({$tourId}, 2, 5, 1, 2, 4),({$tourId}, 2, 5, 3, 6, 4),({$tourId}, 2, 5, 4, 5, 4),({$tourId}, 3, 1, 1, 6, 2),({$tourId}, 3, 1, 2, 5, 2),({$tourId}, 3, 1, 3, 4, 2),
			({$tourId}, 3, 2, 1, 5, 2),({$tourId}, 3, 2, 2, 3, 2),({$tourId}, 3, 2, 4, 6, 2),({$tourId}, 3, 3, 1, 4, 2),({$tourId}, 3, 3, 2, 6, 2),({$tourId}, 3, 3, 3, 5, 2),
			({$tourId}, 3, 4, 1, 3, 2),({$tourId}, 3, 4, 2, 4, 2),({$tourId}, 3, 4, 5, 6, 2),({$tourId}, 3, 5, 1, 2, 2),({$tourId}, 3, 5, 3, 6, 2),({$tourId}, 3, 5, 4, 5, 2),
			({$tourId}, 0, 1, 1, 6, 1),({$tourId}, 0, 1, 2, 5, 1),({$tourId}, 0, 1, 3, 4, 1),({$tourId}, 0, 2, 1, 5, 1),({$tourId}, 0, 2, 2, 3, 1),({$tourId}, 0, 2, 4, 6, 1),
			({$tourId}, 0, 3, 1, 4, 1),({$tourId}, 0, 3, 2, 6, 1),({$tourId}, 0, 3, 3, 5, 1),({$tourId}, 0, 4, 1, 3, 1),({$tourId}, 0, 4, 2, 4, 1),({$tourId}, 0, 4, 5, 6, 1),
			({$tourId}, 0, 5, 1, 2, 1),({$tourId}, 0, 5, 3, 6, 1),({$tourId}, 0, 5, 4, 5, 1),({$tourId}, 0, 1, 1, 6, 2),({$tourId}, 0, 1, 2, 5, 2),({$tourId}, 0, 1, 3, 4, 2),
			({$tourId}, 0, 2, 1, 5, 2),({$tourId}, 0, 2, 2, 3, 2),({$tourId}, 0, 2, 4, 6, 2),({$tourId}, 0, 3, 1, 4, 2),({$tourId}, 0, 3, 2, 6, 2),({$tourId}, 0, 3, 3, 5, 2),
			({$tourId}, 0, 4, 1, 3, 2),({$tourId}, 0, 4, 2, 4, 2),({$tourId}, 0, 4, 5, 6, 2),({$tourId}, 0, 5, 1, 2, 2),({$tourId}, 0, 5, 3, 6, 2),({$tourId}, 0, 5, 4, 5, 2),
			({$tourId}, 0, 1, 1, 6, 3),({$tourId}, 0, 1, 2, 5, 3),({$tourId}, 0, 1, 3, 4, 3),({$tourId}, 0, 2, 1, 5, 3),({$tourId}, 0, 2, 2, 3, 3),({$tourId}, 0, 2, 4, 6, 3),
			({$tourId}, 0, 3, 1, 4, 3),({$tourId}, 0, 3, 2, 6, 3),({$tourId}, 0, 3, 3, 5, 3),({$tourId}, 0, 4, 1, 3, 3),({$tourId}, 0, 4, 2, 4, 3),({$tourId}, 0, 4, 5, 6, 3),
			({$tourId}, 0, 5, 1, 2, 3),({$tourId}, 0, 5, 3, 6, 3),({$tourId}, 0, 5, 4, 5, 3),({$tourId}, 0, 1, 1, 6, 4),({$tourId}, 0, 1, 2, 5, 4),({$tourId}, 0, 1, 3, 4, 4),
			({$tourId}, 0, 2, 1, 5, 4),({$tourId}, 0, 2, 2, 3, 4),({$tourId}, 0, 2, 4, 6, 4),({$tourId}, 0, 3, 1, 4, 4),({$tourId}, 0, 3, 2, 6, 4),({$tourId}, 0, 3, 3, 5, 4),
			({$tourId}, 0, 4, 1, 3, 4),({$tourId}, 0, 4, 2, 4, 4),({$tourId}, 0, 4, 5, 6, 4),({$tourId}, 0, 5, 1, 2, 4),({$tourId}, 0, 5, 3, 6, 4),({$tourId}, 0, 5, 4, 5, 4),
			({$tourId}, 0, 1, 1, 6, 5),({$tourId}, 0, 1, 2, 5, 5),({$tourId}, 0, 1, 3, 4, 5),({$tourId}, 0, 2, 1, 5, 5),({$tourId}, 0, 2, 2, 3, 5),({$tourId}, 0, 2, 4, 6, 5),
			({$tourId}, 0, 3, 1, 4, 5),({$tourId}, 0, 3, 2, 6, 5),({$tourId}, 0, 3, 3, 5, 5),({$tourId}, 0, 4, 1, 3, 5),({$tourId}, 0, 4, 2, 4, 5),({$tourId}, 0, 4, 5, 6, 5),
			({$tourId}, 0, 5, 1, 2, 5),({$tourId}, 0, 5, 3, 6, 5),({$tourId}, 0, 5, 4, 5, 5),({$tourId}, 0, 1, 1, 6, 6),({$tourId}, 0, 1, 2, 5, 6),({$tourId}, 0, 1, 3, 4, 6),
			({$tourId}, 0, 2, 1, 5, 6),({$tourId}, 0, 2, 2, 3, 6),({$tourId}, 0, 2, 4, 6, 6),({$tourId}, 0, 3, 1, 4, 6),({$tourId}, 0, 3, 2, 6, 6),({$tourId}, 0, 3, 3, 5, 6),
			({$tourId}, 0, 4, 1, 3, 6),({$tourId}, 0, 4, 2, 4, 6),({$tourId}, 0, 4, 5, 6, 6),({$tourId}, 0, 5, 1, 2, 6),({$tourId}, 0, 5, 3, 6, 6),({$tourId}, 0, 5, 4, 5, 6),
			({$tourId}, 0, 1, 1, 6, 7),({$tourId}, 0, 1, 2, 5, 7),({$tourId}, 0, 1, 3, 4, 7),({$tourId}, 0, 2, 1, 5, 7),({$tourId}, 0, 2, 2, 3, 7),({$tourId}, 0, 2, 4, 6, 7),
			({$tourId}, 0, 3, 1, 4, 7),({$tourId}, 0, 3, 2, 6, 7),({$tourId}, 0, 3, 3, 5, 7),({$tourId}, 0, 4, 1, 3, 7),({$tourId}, 0, 4, 2, 4, 7),({$tourId}, 0, 4, 5, 6, 7),
			({$tourId}, 0, 5, 1, 2, 7),({$tourId}, 0, 5, 3, 6, 7),({$tourId}, 0, 5, 4, 5, 7),({$tourId}, 0, 1, 1, 6, 8),({$tourId}, 0, 1, 2, 5, 8),({$tourId}, 0, 1, 3, 4, 8),
			({$tourId}, 0, 2, 1, 5, 8),({$tourId}, 0, 2, 2, 3, 8),({$tourId}, 0, 2, 4, 6, 8),({$tourId}, 0, 3, 1, 4, 8),({$tourId}, 0, 3, 2, 6, 8),({$tourId}, 0, 3, 3, 5, 8),
			({$tourId}, 0, 4, 1, 3, 8),({$tourId}, 0, 4, 2, 4, 8),({$tourId}, 0, 4, 5, 6, 8),({$tourId}, 0, 5, 1, 2, 8),({$tourId}, 0, 5, 3, 6, 8),({$tourId}, 0, 5, 4, 5, 8),
			({$tourId}, 0, 1, 1, 6, 9),({$tourId}, 0, 1, 2, 5, 9),({$tourId}, 0, 1, 3, 4, 9),({$tourId}, 0, 2, 1, 5, 9),({$tourId}, 0, 2, 2, 3, 9),({$tourId}, 0, 2, 4, 6, 9),
			({$tourId}, 0, 3, 1, 4, 9),({$tourId}, 0, 3, 2, 6, 9),({$tourId}, 0, 3, 3, 5, 9),({$tourId}, 0, 4, 1, 3, 9),({$tourId}, 0, 4, 2, 4, 9),({$tourId}, 0, 4, 5, 6, 9),
			({$tourId}, 0, 5, 1, 2, 9),({$tourId}, 0, 5, 3, 6, 9),({$tourId}, 0, 5, 4, 5, 9),({$tourId}, 0, 1, 1, 6, 10),({$tourId}, 0, 1, 2, 5, 10),({$tourId}, 0, 1, 3, 4, 10),
			({$tourId}, 0, 2, 1, 5, 10),({$tourId}, 0, 2, 2, 3, 10),({$tourId}, 0, 2, 4, 6, 10),({$tourId}, 0, 3, 1, 4, 10),({$tourId}, 0, 3, 2, 6, 10),({$tourId}, 0, 3, 3, 5, 10),
			({$tourId}, 0, 4, 1, 3, 10),({$tourId}, 0, 4, 2, 4, 10),({$tourId}, 0, 4, 5, 6, 10),({$tourId}, 0, 5, 1, 2, 10),({$tourId}, 0, 5, 3, 6, 10),({$tourId}, 0, 5, 4, 5, 10),
			({$tourId}, 0, 1, 1, 6, 11),({$tourId}, 0, 1, 2, 5, 11),({$tourId}, 0, 1, 3, 4, 11),({$tourId}, 0, 2, 1, 5, 11),({$tourId}, 0, 2, 2, 3, 11),({$tourId}, 0, 2, 4, 6, 11),
			({$tourId}, 0, 3, 1, 4, 11),({$tourId}, 0, 3, 2, 6, 11),({$tourId}, 0, 3, 3, 5, 11),({$tourId}, 0, 4, 1, 3, 11),({$tourId}, 0, 4, 2, 4, 11),({$tourId}, 0, 4, 5, 6, 11),
			({$tourId}, 0, 5, 1, 2, 11),({$tourId}, 0, 5, 3, 6, 11),({$tourId}, 0, 5, 4, 5, 11),({$tourId}, 0, 1, 1, 6, 12),({$tourId}, 0, 1, 2, 5, 12),({$tourId}, 0, 1, 3, 4, 12),
			({$tourId}, 0, 2, 1, 5, 12),({$tourId}, 0, 2, 2, 3, 12),({$tourId}, 0, 2, 4, 6, 12),({$tourId}, 0, 3, 1, 4, 12),({$tourId}, 0, 3, 2, 6, 12),({$tourId}, 0, 3, 3, 5, 12),
			({$tourId}, 0, 4, 1, 3, 12),({$tourId}, 0, 4, 2, 4, 12),({$tourId}, 0, 4, 5, 6, 12),({$tourId}, 0, 5, 1, 2, 12),({$tourId}, 0, 5, 3, 6, 12),({$tourId}, 0, 5, 4, 5, 12),
			({$tourId}, 0, 1, 1, 6, 13),({$tourId}, 0, 1, 2, 5, 13),({$tourId}, 0, 1, 3, 4, 13),({$tourId}, 0, 2, 1, 5, 13),({$tourId}, 0, 2, 2, 3, 13),({$tourId}, 0, 2, 4, 6, 13),
			({$tourId}, 0, 3, 1, 4, 13),({$tourId}, 0, 3, 2, 6, 13),({$tourId}, 0, 3, 3, 5, 13),({$tourId}, 0, 4, 1, 3, 13),({$tourId}, 0, 4, 2, 4, 13),({$tourId}, 0, 4, 5, 6, 13),
			({$tourId}, 0, 5, 1, 2, 13),({$tourId}, 0, 5, 3, 6, 13),({$tourId}, 0, 5, 4, 5, 13),({$tourId}, 0, 1, 1, 6, 14),({$tourId}, 0, 1, 2, 5, 14),({$tourId}, 0, 1, 3, 4, 14),
			({$tourId}, 0, 2, 1, 5, 14),({$tourId}, 0, 2, 2, 3, 14),({$tourId}, 0, 2, 4, 6, 14),({$tourId}, 0, 3, 1, 4, 14),({$tourId}, 0, 3, 2, 6, 14),({$tourId}, 0, 3, 3, 5, 14),
			({$tourId}, 0, 4, 1, 3, 14),({$tourId}, 0, 4, 2, 4, 14),({$tourId}, 0, 4, 5, 6, 14),({$tourId}, 0, 5, 1, 2, 14),({$tourId}, 0, 5, 3, 6, 14),({$tourId}, 0, 5, 4, 5, 14),
			({$tourId}, 0, 1, 1, 6, 15),({$tourId}, 0, 1, 2, 5, 15),({$tourId}, 0, 1, 3, 4, 15),({$tourId}, 0, 2, 1, 5, 15),({$tourId}, 0, 2, 2, 3, 15),({$tourId}, 0, 2, 4, 6, 15),
			({$tourId}, 0, 3, 1, 4, 15),({$tourId}, 0, 3, 2, 6, 15),({$tourId}, 0, 3, 3, 5, 15),({$tourId}, 0, 4, 1, 3, 15),({$tourId}, 0, 4, 2, 4, 15),({$tourId}, 0, 4, 5, 6, 15),
			({$tourId}, 0, 5, 1, 2, 15),({$tourId}, 0, 5, 3, 6, 15),({$tourId}, 0, 5, 4, 5, 15),({$tourId}, 1, 1, 1, 6, 1),({$tourId}, 1, 1, 2, 5, 1),({$tourId}, 1, 1, 3, 4, 1),
			({$tourId}, 1, 2, 1, 5, 1),({$tourId}, 1, 2, 2, 3, 1),({$tourId}, 1, 2, 4, 6, 1),({$tourId}, 1, 3, 1, 4, 1),({$tourId}, 1, 3, 2, 6, 1),({$tourId}, 1, 3, 3, 5, 1),
			({$tourId}, 1, 4, 1, 3, 1),({$tourId}, 1, 4, 2, 4, 1),({$tourId}, 1, 4, 5, 6, 1),({$tourId}, 1, 5, 1, 2, 1),({$tourId}, 1, 5, 3, 6, 1),({$tourId}, 1, 5, 4, 5, 1),
			({$tourId}, 1, 1, 1, 6, 2),({$tourId}, 1, 1, 2, 5, 2),({$tourId}, 1, 1, 3, 4, 2),({$tourId}, 1, 2, 1, 5, 2),({$tourId}, 1, 2, 2, 3, 2),({$tourId}, 1, 2, 4, 6, 2),
			({$tourId}, 1, 3, 1, 4, 2),({$tourId}, 1, 3, 2, 6, 2),({$tourId}, 1, 3, 3, 5, 2),({$tourId}, 1, 4, 1, 3, 2),({$tourId}, 1, 4, 2, 4, 2),({$tourId}, 1, 4, 5, 6, 2),
			({$tourId}, 1, 5, 1, 2, 2),({$tourId}, 1, 5, 3, 6, 2),({$tourId}, 1, 5, 4, 5, 2),({$tourId}, 1, 1, 1, 6, 3),({$tourId}, 1, 1, 2, 5, 3),({$tourId}, 1, 1, 3, 4, 3),
			({$tourId}, 1, 2, 1, 5, 3),({$tourId}, 1, 2, 2, 3, 3),({$tourId}, 1, 2, 4, 6, 3),({$tourId}, 1, 3, 1, 4, 3),({$tourId}, 1, 3, 2, 6, 3),({$tourId}, 1, 3, 3, 5, 3),
			({$tourId}, 1, 4, 1, 3, 3),({$tourId}, 1, 4, 2, 4, 3),({$tourId}, 1, 4, 5, 6, 3),({$tourId}, 1, 5, 1, 2, 3),({$tourId}, 1, 5, 3, 6, 3),({$tourId}, 1, 5, 4, 5, 3),
			({$tourId}, 1, 1, 1, 6, 4),({$tourId}, 1, 1, 2, 5, 4),({$tourId}, 1, 1, 3, 4, 4),({$tourId}, 1, 2, 1, 5, 4),({$tourId}, 1, 2, 2, 3, 4),({$tourId}, 1, 2, 4, 6, 4),
			({$tourId}, 1, 3, 1, 4, 4),({$tourId}, 1, 3, 2, 6, 4),({$tourId}, 1, 3, 3, 5, 4),({$tourId}, 1, 4, 1, 3, 4),({$tourId}, 1, 4, 2, 4, 4),({$tourId}, 1, 4, 5, 6, 4),
			({$tourId}, 1, 5, 1, 2, 4),({$tourId}, 1, 5, 3, 6, 4),({$tourId}, 1, 5, 4, 5, 4),({$tourId}, 1, 1, 1, 6, 5),({$tourId}, 1, 1, 2, 5, 5),({$tourId}, 1, 1, 3, 4, 5),
			({$tourId}, 1, 2, 1, 5, 5),({$tourId}, 1, 2, 2, 3, 5),({$tourId}, 1, 2, 4, 6, 5),({$tourId}, 1, 3, 1, 4, 5),({$tourId}, 1, 3, 2, 6, 5),({$tourId}, 1, 3, 3, 5, 5),
			({$tourId}, 1, 4, 1, 3, 5),({$tourId}, 1, 4, 2, 4, 5),({$tourId}, 1, 4, 5, 6, 5),({$tourId}, 1, 5, 1, 2, 5),({$tourId}, 1, 5, 3, 6, 5),({$tourId}, 1, 5, 4, 5, 5),
			({$tourId}, 1, 1, 1, 6, 6),({$tourId}, 1, 1, 2, 5, 6),({$tourId}, 1, 1, 3, 4, 6),({$tourId}, 1, 2, 1, 5, 6),({$tourId}, 1, 2, 2, 3, 6),({$tourId}, 1, 2, 4, 6, 6),
			({$tourId}, 1, 3, 1, 4, 6),({$tourId}, 1, 3, 2, 6, 6),({$tourId}, 1, 3, 3, 5, 6),({$tourId}, 1, 4, 1, 3, 6),({$tourId}, 1, 4, 2, 4, 6),({$tourId}, 1, 4, 5, 6, 6),
			({$tourId}, 1, 5, 1, 2, 6),({$tourId}, 1, 5, 3, 6, 6),({$tourId}, 1, 5, 4, 5, 6),({$tourId}, 1, 1, 1, 6, 7),({$tourId}, 1, 1, 2, 5, 7),({$tourId}, 1, 1, 3, 4, 7),
			({$tourId}, 1, 2, 1, 5, 7),({$tourId}, 1, 2, 2, 3, 7),({$tourId}, 1, 2, 4, 6, 7),({$tourId}, 1, 3, 1, 4, 7),({$tourId}, 1, 3, 2, 6, 7),({$tourId}, 1, 3, 3, 5, 7),
			({$tourId}, 1, 4, 1, 3, 7),({$tourId}, 1, 4, 2, 4, 7),({$tourId}, 1, 4, 5, 6, 7),({$tourId}, 1, 5, 1, 2, 7),({$tourId}, 1, 5, 3, 6, 7),({$tourId}, 1, 5, 4, 5, 7),
			({$tourId}, 2, 1, 1, 6, 1),({$tourId}, 2, 1, 2, 5, 1),({$tourId}, 2, 1, 3, 4, 1),({$tourId}, 2, 2, 1, 5, 1),({$tourId}, 2, 2, 2, 3, 1),({$tourId}, 2, 2, 4, 6, 1),
			({$tourId}, 2, 3, 1, 4, 1),({$tourId}, 2, 3, 2, 6, 1),({$tourId}, 2, 3, 3, 5, 1),({$tourId}, 2, 4, 1, 3, 1),({$tourId}, 2, 4, 2, 4, 1),({$tourId}, 2, 4, 5, 6, 1),
			({$tourId}, 2, 5, 1, 2, 1),({$tourId}, 2, 5, 3, 6, 1),({$tourId}, 2, 5, 4, 5, 1),({$tourId}, 2, 1, 1, 6, 2),({$tourId}, 2, 1, 2, 5, 2),({$tourId}, 2, 1, 3, 4, 2),
			({$tourId}, 2, 2, 1, 5, 2),({$tourId}, 2, 2, 2, 3, 2),({$tourId}, 2, 2, 4, 6, 2),({$tourId}, 2, 3, 1, 4, 2),({$tourId}, 2, 3, 2, 6, 2),({$tourId}, 2, 3, 3, 5, 2),
			({$tourId}, 2, 4, 1, 3, 2),({$tourId}, 2, 4, 2, 4, 2),({$tourId}, 2, 4, 5, 6, 2),({$tourId}, 2, 5, 1, 2, 2),({$tourId}, 2, 5, 3, 6, 2),({$tourId}, 2, 5, 4, 5, 2),
			({$tourId}, 2, 1, 1, 6, 3),({$tourId}, 2, 1, 2, 5, 3),({$tourId}, 2, 1, 3, 4, 3),({$tourId}, 2, 2, 1, 5, 3),({$tourId}, 2, 2, 2, 3, 3),({$tourId}, 2, 2, 4, 6, 3),
			({$tourId}, 2, 3, 1, 4, 3),({$tourId}, 2, 3, 2, 6, 3),({$tourId}, 2, 3, 3, 5, 3),({$tourId}, 2, 4, 1, 3, 3),({$tourId}, 2, 4, 2, 4, 3),({$tourId}, 2, 4, 5, 6, 3),
			({$tourId}, 2, 5, 1, 2, 3),({$tourId}, 2, 5, 3, 6, 3),({$tourId}, 2, 5, 4, 5, 3),({$tourId}, 3, 1, 1, 6, 1),({$tourId}, 3, 1, 2, 5, 1),({$tourId}, 3, 1, 3, 4, 1),
			({$tourId}, 3, 2, 1, 5, 1),({$tourId}, 3, 2, 2, 3, 1),({$tourId}, 3, 2, 4, 6, 1),({$tourId}, 3, 3, 1, 4, 1),({$tourId}, 3, 3, 2, 6, 1),({$tourId}, 3, 3, 3, 5, 1),
			({$tourId}, 3, 4, 1, 3, 1),({$tourId}, 3, 4, 2, 4, 1),({$tourId}, 3, 4, 5, 6, 1),({$tourId}, 3, 5, 1, 2, 1),({$tourId}, 3, 5, 3, 6, 1),({$tourId}, 3, 5, 4, 5, 1);
	";
	
	return $q;
}

/*
 * Ritorna la query che inserisce le righe in F2FGrid per un indoor 18m olandese con sottoregola championships
 */
function insertIntoGridForF2F_NL_6_Champs($tourId)
{
	$q="
		REPLACE INTO F2FGrid (F2FTournament,F2FPhase,F2FRound,F2FMatchNo1,F2FMatchNo2,F2FGroup) VALUES	
			({$tourId},0,1,1,2,1),
			({$tourId},0,1,3,4,1),
			({$tourId},0,1,1,2,2),
			({$tourId},0,1,3,4,2),
			({$tourId},0,1,1,2,3),
			({$tourId},0,1,3,4,3),
			({$tourId},0,1,1,2,4),
			({$tourId},0,1,3,4,4),
			({$tourId},0,1,1,2,5),
			({$tourId},0,1,3,4,5),
			({$tourId},0,2,1,3,1),
			({$tourId},0,2,2,4,1),
			({$tourId},0,2,1,3,2),
			({$tourId},0,2,2,4,2),
			({$tourId},0,2,1,3,3),
			({$tourId},0,2,2,4,3),
			({$tourId},0,2,1,3,4),
			({$tourId},0,2,2,4,4),
			({$tourId},0,2,1,3,5),
			({$tourId},0,2,2,4,5),
			({$tourId},0,3,1,4,1),
			({$tourId},0,3,2,3,1),
			({$tourId},0,3,1,4,2),
			({$tourId},0,3,2,3,2),
			({$tourId},0,3,1,4,3),
			({$tourId},0,3,2,3,3),
			({$tourId},0,3,1,4,4),
			({$tourId},0,3,2,3,4),
			({$tourId},0,3,1,4,5),
			({$tourId},0,3,2,3,5),
			({$tourId},1,1,1,2,1),
			({$tourId},1,1,3,4,1),
			({$tourId},1,1,1,2,2),
			({$tourId},1,1,3,4,2),
			({$tourId},1,1,1,2,3),
			({$tourId},1,1,3,4,3),
			({$tourId},1,2,1,3,1),
			({$tourId},1,2,2,4,1),
			({$tourId},1,2,1,3,2),
			({$tourId},1,2,2,4,2),
			({$tourId},1,2,1,3,3),
			({$tourId},1,2,2,4,3),
			({$tourId},1,3,1,4,1),
			({$tourId},1,3,2,3,1),
			({$tourId},1,3,1,4,2),
			({$tourId},1,3,2,3,2),
			({$tourId},1,3,1,4,3),
			({$tourId},1,3,2,3,3)
	";
	return $q;
}