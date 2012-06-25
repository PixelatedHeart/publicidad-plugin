<?php

function stray_remove() {

	global $wpdb;
	
	// Check Whether User Can Manage Options
	if(!current_user_can('manage_options'))die('Acceso denegado');	
	$mode = trim($_GET['mode']);
	
	//decode and intercept
	foreach($_POST as $key => $val) {
		$_POST[$key] = stripslashes($val);
	}
		
	//handle the post event
	if(!empty($_POST['do'])) {

		//update options
		$removeoptions =  $_POST['remove'];
		$removetable = $_POST['removequotes'];
		$quotesoptions = get_option('stray_quotes_options');
		if ($removeoptions == 1 && $removetable == 1)$quotesoptions['stray_quotes_uninstall'] = 'both';
		else if ($removeoptions == 1)$quotesoptions['stray_quotes_uninstall'] = 'options';
		else if ($removetable == 1)$quotesoptions['stray_quotes_uninstall'] = 'table';
		/*else $quoteoptions['stray_quotes_uninstall'] = 'none';*/
		update_option('stray_quotes_options', $quotesoptions);
	
		$deactivate_url = get_option("siteurl"). '/wp-admin/plugins.php?action=deactivate&amp;plugin='.STRAY_DIR.'/stray_quotes.php';
		if(function_exists('wp_nonce_url'))	$deactivate_url = urldecode(wp_nonce_url($deactivate_url, 'deactivate-plugin_'.STRAY_DIR.'/stray_quotes.php'));	       

		//execute and feedback the removal
		$quotesoptions = get_option('stray_quotes_options');
		?><div class="wrap"><h2><?php _e('Remove and deactivate', 'stray-quotes') ?></h2>
		<p><strong><a href="<?php echo $deactivate_url ?>" >
		<?php _e('Pinche aquí</a> para desactivar la publicidad.', 'stray-quotes'); ?>
		</a></strong></p><p style="#990000"><?php			
		if( $quotesoptions['stray_quotes_uninstall'] ==  'both' ) _e('La <strong>publicidad</strong> Y las <strong>opciones</strong> se pueden borrar.', 'stray-quotes').'<br />';
		else if( $quotesoptions['stray_quotes_uninstall'] ==  'options' ) _e('Se borrarán las <strong>opciones</strong>.', 'stray-quotes').'<br />';
		else if( $quotesoptions['stray_quotes_uninstall'] ==  'table' ) _e('Se borrará la <strong>publicidad</strong>.', 'stray-quotes').'<br />';
		?></p></div><?php 
		
	} else {
	
		// the deactivation form ?>
		<form method="post" action="<?php $_SERVER['REQUEST_URI'] ?>">
		<div class="wrap">
		<h2><?php _e('borrar y desactivar','stray-quotes') ?></h2>     
		<span class="setting-description"><?php _e('"Patch grief with proverbs." ~ William Shakespeare','stray-quotes') ?></span>     
		<br/><br/>
		<table class="form-table">
		<tr valign="top"><th scope="row"><?php _e('When deactivating Stray Random Quotes', 'stray-quotes'); ?></th>
		<td>
		<input type="checkbox" name="remove" value="1" />
		<?php _e('Borra las opciones de la base de datos.','stray-quotes') ?><br />
		<input type="checkbox" name="removequotes" value="1" />
		<?php _e('Borra toda la publicidad de la base de datos.','stray-quotes') ?><br />
		</tr>
		</table></div>
		<br/>
		<div class="submit">
		<input type="hidden" name="do" value="Deactivate" />
		<input type="submit" value="<?php _e('Desactivar el plugin &raquo;','stray-quotes') ?>"  style="color:#990000"/>
		</div>
		<p>&nbsp;</p>
		</form><?php 
	
	}
}

?>