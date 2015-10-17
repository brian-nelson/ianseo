<?php
// this code-bit goes into the Tournament/index.php

if(empty($MyRow->ToOptions['ISK-Lite-ServerUrl'])) $MyRow->ToOptions['ISK-Lite-ServerUrl']='';
if(empty($MyRow->ToOptions['ISK-Lite-Mode'])) $MyRow->ToOptions['ISK-Lite-Mode']='';
// 	debug_svela($MyRow->ToOptions, true);
echo '<tr>
		<th class="TitleLeft" width="15%">'.get_text('ISK-Lite-ServerUrl','Api').'</th>
		<td>
		<input type="text" name="Options[ISK-Lite-ServerUrl]" value="'.$MyRow->ToOptions['ISK-Lite-ServerUrl'].'">
		</td>
		</tr>';

echo '<tr>
		<th class="TitleLeft" width="15%">'.get_text('ISK-Lite-EnableScore','Api').'</th>
		<td>
		<select name="Options[ISK-Lite-Mode]">
			<option value=""'.(empty($MyRow->ToOptions['ISK-Lite-Mode']) ? ' selected="selected"' : '').'>'.get_text('No').'</option>
			<option value="insecure"'.($MyRow->ToOptions['ISK-Lite-Mode']=='insecure' ? ' selected="selected"' : '').'>'.get_text('ISK-Lite-Insecure', 'Api').'</option>';
// echo '<option value="secure"'.($MyRow->ToOptions['ISK-Lite-Mode']=='secure' ? ' selected="selected"' : '').'>'.get_text('ISK-Lite-Secure', 'Api').'</option>';
echo '</select>
		</td>
		</tr>';
