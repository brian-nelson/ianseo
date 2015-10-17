<?php

/*

You can customize your menus adding lines in this file. The structure is as follows:
  · $ret['XXXX'][] = 'Description of the item' . '|' . $CFG->ROOT_DIR . 'Modules/Custom/' . 'Link/To/Your/page.php';
  OR
  · $ret['XXXX'][] = MENU_DIVIDER;

* Global variables:
- $on: if true, a tournament is selected and open
- $ret['XXXX']: the first level menu items.
  You can insert whatever you want as the key to create a new main menu
  or add your items in the predefined menus.
  A list of first level predefined keys (XXXX) follow:
  · COMP: Competition
  · PART: Participants
  · QUAL: Qualification
  · ELIM: Eliminations
  · FINI: Individual Finals
  · FINT: Team Finals
  · CLUB: Club Team Finals
  · CAST: Cas-Cag Finals
  · PRNT: Printouts
  · HHT:  HHT features
  · MEDI: Media outputs
- MENU_DIVIDER: a constant that writes a separation line in the menu
- $CFG->ROOT_DIR: the prefix for EVERY link you want to insert in your pages.

* Sub menu:
Creating a submenu is straight forward: just add a nested array, giving it a (string)key, as in:
	$ret['XXXX']['YYYY']

* Link to resource
Each menu item is a string, that joins the text description of the item and the link itself with a pipe (|) symbol:
	$ret['XXXX'][] = 'Description of the item' . '|' . $CFG->ROOT_DIR . 'Modules/Custom/' . 'Link/To/Your/page.php';

*/

?>