<?php

function stray_new() {	

	global $wpdb,$current_user;
	
	//load options
	$quotesoptions = array();
	$quotesoptions = get_option('stray_quotes_options');
	
	//security check
	if( $quotesoptions['stray_multiuser'] == false && !current_user_can('manage_options') )
		die('Acceso denegado');

	//decode and intercept
	foreach($_POST as $key => $val) {
		$_POST[$key] = stripslashes($val);
	}	

	// control the requests
	$action = !empty($_REQUEST['action']) ? $_REQUEST['action'] : '';
	$quoteID = !empty($_REQUEST['quoteID']) ? $_REQUEST['quoteID'] : '';
	
	//this is for the bookmarklet
	if ( $action == 'bookmarklet' ) {
		
		$quotesoptions = array();
		$quotesoptions = get_option('stray_quotes_options');
		$quote = !empty($_REQUEST['quote_quote']) ? stripslashes(trim($_REQUEST['quote_quote'])) : '';
		if ($quotesoptions['bookmarlet_source'] == 'Y' )$source = !empty($_REQUEST['quote_source']) ? stripslashes(trim($_REQUEST['quote_source'])) : '';
		if ($quotesoptions['bookmarklet_cat']) $category = $quotesoptions['bookmarklet_cat'];
	}
	
	//after adding a new quote
	if ( $action == 'add' ) {
	
		//assign variables and trim them
		$quote = !empty($_REQUEST['quote_quote']) ? trim($_REQUEST['quote_quote']) : '';
		$author = !empty($_REQUEST['quote_author']) ? trim($_REQUEST['quote_author']) : '';
		$source = !empty($_REQUEST['quote_source']) ? trim($_REQUEST['quote_source']) : '';
		$visible = !empty($_REQUEST['quote_visible']) ? trim($_REQUEST['quote_visible']) : '';
		if ( $_REQUEST['quote_category'] )$category = trim($_REQUEST['quote_category']);
		else $category = $_REQUEST['categories'];
		
		//remove spaces from categories
		if (preg_match('/\s+/',$category)>0){
			$category=preg_replace('/\s+/','-',$category);
			$plusmessage = "<br/>Nota: <strong>El nombre de la categoría contiene espacios</strong>, que no están permitidos. <strong>Los hemos sustituido por guiones</strong>.";
		} 
		
		if ($category == false || $category == '') $category = 'default';
		
		//take care of stupid magic quotes
		if ( ini_get('magic_quotes_gpc') )	{
		
			$quote = stripslashes($quote);
			$author = stripslashes($author);
			$source = stripslashes($source);
			$category = stripslashes($category);
			$visible = stripslashes($visible);	
		}	
		
		//insert the quote into the database!!
		$sql = "insert into " . WP_STRAY_QUOTES_TABLE
		. " set `quote`='" . mysql_real_escape_string($quote)
		. "', `author`='" . mysql_real_escape_string($author)
		. "', `source`='" . mysql_real_escape_string($source)
		. "', `category`='" . mysql_real_escape_string($category)
		. "', `visible`='" . mysql_real_escape_string($visible) 
		. "', `user`='" . mysql_real_escape_string($current_user->user_nicename)
		. "'";	     
		$wpdb->get_results($sql);
		
		//check: go and get the quote just inserted
		$sql2 = "select `quoteID` from " . WP_STRAY_QUOTES_TABLE
		. " where `quote`='" . mysql_real_escape_string($quote) 
		. "' and `author`='" . mysql_real_escape_string($author) 
		. "' and `source`='" . mysql_real_escape_string($source) 
		. "' and `category`='" . mysql_real_escape_string($category) 
		. "' and `visible`='" . mysql_real_escape_string($visible) 
		. "' and `user`='" . mysql_real_escape_string($current_user->user_nicename)
		. "' limit 1";
		$result = $wpdb->get_results($sql2);
		
		//failure message
		if ( empty($result) || empty($result[0]->quoteID) )	{
			?><div class="error fade"><p><?php _e('<strong>Fallo:</strong> Algo fue mal intentando añadir la publicidad. Inténtelo de nuevo.',
			'stray-quotes'); ?></p></div><?php				
		}
			
		//success message
		else {
			?><div class="updated fade"><p><?php 
			
			$search = array("%s1", "%s2");
			$replace = array($result[0]->quoteID, get_option("siteurl").'/wp-admin/admin.php?page=stray_manage');
			echo str_replace($search,$replace,__(
			'La publicidad nº <strong>%s1</strong> se añadió a la base de datos. Para añadirlo en un artículo utilice: <code>[stray-id id=%s1]</code>. Para revisar la publicidad hágalo en la <a href="%s2">página de gestión</a>.'.$plusmessage,'stray-quotes')); ?></p></div><?php			
		}
	
	}
	
	//making the "add new quote" page
	?><div class="wrap"><h2><?php _e('Añadir nueva publicidad','stray-quotes') ?></h2><?php
	
		//housecleaning 
		$quoteID=false;
		$data = false;
		
		//get the last inserted quote 
		if ( $quoteID !== false ) {
	
			if ( intval($quoteID) != $quoteID ) {		
				?><div class="error fade"><p><?php _e('El ID no es correcto.','stray-quotes') ?></p></div><?php
				return;
			}
			else {
				$data = $wpdb->get_results("select * from " . WP_STRAY_QUOTES_TABLE . " where quoteID='" . mysql_real_escape_string($quoteID) . "' limit 1");
				if ( empty($data) ) {
					?><div class="error fade"><p><?php _e('Algún dato es erróneo.','stray-quotes') ?></p></div><?php
					return;
				}
				$data = $data[0];
			}	
		}

		//optionally assign the just inserted quote to vaiables
		if ($quotesoptions['stray_clear_form']!=='Y') {
			if ( !empty($data) ) { 
				$quote = $data->quote; 		
				$author = $data->author;
				$source = $data->source;
				$category = $data->category;
			}
		} else if($action != 'bookmarklet')$quote = $author = $source = $category = false;
		
		//visibility
		$defaultVisible = $quotesoptions['stray_quotes_default_visible'];
		if ( empty($data)){				
			if  ($defaultVisible == 'Y') {			
				$visible_yes = "checked";
				$visible_no = "";
			}
			else {
				$visible_yes = "";
				$visible_no = "checked";				
			}				
		}
		else {			
			if ( $data->visible=='yes' ) {
				$visible_yes = "checked";
				$visible_no = "";
			}
			else {
				$visible_yes = "";
				$visible_no = "checked";				
			}		
		}
		
		//default category
		$defaultcategory = $quotesoptions['stray_default_category'];
		
		//make the "add new quote" form
		$styleborder = 'style="border:1px solid #ccc"';
		$styletextarea = 'style="border:1px solid #ccc; font-family: Times New Roman, Times, serif; font-size: 1.4em;"'; ?>
		
		<div style="width:42em">
		<script src="<?php echo WP_STRAY_QUOTES_PATH ?>inc/stray_quicktags.js" type="text/javascript"></script>
		<form name="quoteform" id="quoteform" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
			<input type="hidden" name="action" value="add">
			<input type="hidden" name="quoteID" value="<?php echo $quoteID; ?>">
		
			<p><label><?php _e('Nombre: (para identificarla en la lista)','stray-quotes') ?></label>
			<div style="float:left; display:none;"><script type="text/javascript">edToolbar();</script></div>
            <input type="text" id="qeditor" name="quote_quote" size=58 value="<?php echo $quote ?>" <?php echo $styletextarea ?> />
			<script type="text/javascript">var edCanvas = document.getElementById('qeditor');</script>
			<p class="setting-description"><small><?php _e('Si quieres añadir un adsense, escribe "adsense" (sin comillas) en el espacio del enlace.<br />Para flash (sólo Banner Superior izquierdo 728x90 y Banner lateral derecho 245x303) escribe "flash" (sin comillas)<br />Para facebook (sólo espacios de Facebook) escribe "facebook" (sin comillas)','stray-quotes'); ?></small></p></p>
			
			<p><label><?php _e('Enlace:','stray-quotes') ?></label>
			<input type="text" id="aeditor" name="quote_author" size=58 value="<?php echo htmlspecialchars($author); ?>" <?php echo $styleborder ?> />
			<div style="float:left; display:none;"><script type="text/javascript">edToolbar1();</script>
			<script type="text/javascript">var edCanvas1 = document.getElementById('aeditor');</script><br /></div>
		
			<label><?php _e('Imagen:','stray-quotes') ?></label>
			<textarea id="seditor" name="quote_source" cols=68 rows=4 <?php echo $styleborder ?> /><?php echo htmlspecialchars($source); ?></textarea>
			<div style="float:left; display:none;"><script type="text/javascript">edToolbar2();</script>
			<script type="text/javascript">var edCanvas2 = document.getElementById('seditor');</script></div>
			<div style="float:left; display:none;"><p class="setting-description"><small><?php _e('* Añadiendo un enlace en el autor o en la zona de enlaces los enlaces por defectos son ignorados. Asegúrate de cerrar los enlaces con la etiqueta <code>&lt;/a&gt;</code>.','stray-quotes'); ?></small></p></p></div>
			
			<p style="display:none;"><label><?php _e('Espacio al que corresponde:&nbsp;','stray-quotes') ?></label>				
			<select name="categories" style="vertical-align:middle; width:14em;" > 
			<?php $categorylist = make_categories($current_user->user_nicename); 
			foreach($categorylist as $categoryo){ ?>
			<option value="<?php echo $categoryo; ?>" style=" padding-right:5px" 
			<?php if ($categoryo == $category || $categoryo == $defaultcategory) echo ' selected'; ?> >
			<?php echo $categoryo;?></option>
			<?php } ?>   
			</select>
			<br />  
			<!--<label><?php _e('&nbsp;nueva categoría:&nbsp;','stray-quotes') ?></label>
			<input type="text" name="quote_category" size=24 value="" <?php echo $styleborder ?> /></p>
			-->
			<p><label><?php _e('Visible:','stray-quotes') ?></label>
				<input type="radio" name="quote_visible" class="input" value="yes"<?php echo $visible_yes ?> /> <?php _e('Sí','stray-quotes') ?>					
				<input type="radio" name="quote_visible" class="input" value="no"<?php echo $visible_no ?> /> <?php _e('No','stray-quotes') ?>
			</p><p>&nbsp;</p>
		
			<p><input type="submit" name="save"  class="button-primary" value="<?php _e('Añadir publicidad','stray-quotes') ?> &raquo;" /></p>
		</form></div>
        
	</div><?php	
}
?>