<?php

/**

* STEP 2: check della configurazione mysql
  - se esiste già una connessione lo script fa un dump di
    tutte le gare + un mysqldump --opt del DB di scrittura
  - visualizzare i campi per la connession ai 2 DB (lettura e scrittura),
    e relative password di root qualora dovessero essere creati gli utenti


*/


?>
<Tr>
	<Th class="Title"><?php echo get_text('Parameter','Install') ?></Th>
	<Th class="Title"><?php echo get_text('Write server','Install') ?></Th>
	<Th class="Title"><?php echo get_text('Read server','Install') ?></Th>
</tr>
<tr>
	<Th><?php echo get_text('Host','Install') ?></Th>
	<td><input type="text" name="W_HOST" value="<?php echo $_SESSION['INSTALL']['CFG']['W_HOST'] ?>"></td>
	<td><input type="text" name="R_HOST" value="<?php echo $_SESSION['INSTALL']['CFG']['R_HOST'] ?>"></td>
</tr>
<tr>
	<Th><?php echo get_text('User','Install') ?></Th>
	<td><input type="text" name="W_USER" value="<?php echo $_SESSION['INSTALL']['CFG']['W_USER'] ?>"></td>
	<td><input type="text" name="R_USER" value="<?php echo $_SESSION['INSTALL']['CFG']['R_USER'] ?>"></td>
</tr>
<tr>
	<Th><?php echo get_text('Password','Install') ?></Th>
	<td><input type="text" name="W_PASS" value="<?php echo $_SESSION['INSTALL']['CFG']['W_PASS'] ?>"></td>
	<td><input type="text" name="R_PASS" value="<?php echo $_SESSION['INSTALL']['CFG']['R_PASS'] ?>"></td>
</tr>
<tr>
	<Th><?php echo get_text('Database name','Install') ?></Th>
	<td><input type="text" name="DB_NAME" value="<?php echo $_SESSION['INSTALL']['CFG']['DB_NAME'] ?>"></td>
	<td>&nbsp;</td>
</tr>

<tr><Td colspan="3"><?php echo get_text('Mysql-description','Install') ?></Td></tr>
<tr class="Divider"><td colspan="3"></td></tr>

<tr><Td colspan="3"><?php echo get_text('Mysql-root-description','Install') ?></Td></tr>
<tr>
	<Th><?php echo get_text('Root password','Install') ?></Th>
	<td><input type="text" name="W_ROOT" value=""></td>
	<td><input type="text" name="R_ROOT" value=""></td>
</tr>
<tr class="Divider"><td colspan="3"></td></tr>

<tr><th colspan="3"><input type="submit" value="<?php echo get_text('Create','Install') ?>"></th></tr>
