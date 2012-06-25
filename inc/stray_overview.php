<?php

//intro page
function stray_intro() {

	global $wpdb,$current_user;
	
	//load options
	$quotesoptions = array();
	$quotesoptions = get_option('stray_quotes_options');
	
	//security check
	if( $quotesoptions['stray_multiuser'] == false && !current_user_can('manage_options') )
		die('Acceso denegado');
	
	$widgetpage = get_option('siteurl')."/wp-admin/widgets.php";
	$management = get_option('siteurl')."/wp-admin/admin.php?page=stray_manage";
	$options =  get_option('siteurl')."/wp-admin/admin.php?page=stray_quotes_options";
	$new = get_option('siteurl')."/wp-admin/admin.php?page=stray_new";
	$help =  get_option('siteurl')."/wp-admin/admin.php?page=stray_help";
	$toolspage = get_option('siteurl')."/wp-admin/admin.php?page=stray_tools";
	$straymessage = $quotesoptions['stray_quotes_first_time'];
	
	//get total quotes
	$totalsql = "SELECT COUNT(`quoteID`) AS `Rows` FROM `" . WP_STRAY_QUOTES_TABLE . "` WHERE `user`='".$current_user->user_nicename."'";
	$totalquotes = $wpdb->get_var($totalsql);

	//feedback following activation (see main file)
	if ($straymessage !="") {
		
		?><div id="message" class="updated fade"><ul><?php echo $straymessage; ?></ul></div><?php
		
		//empty message after feedback
		$quotesoptions['stray_quotes_first_time'] = "";
		update_option('stray_quotes_options', $quotesoptions);
	}	
	
	?><div class="wrap"><h2>Publicidad aleatoria: <?php _e('Información','stray-quotes'); ?></h2><?php
	
	
	
	
    if ($totalquotes > 0) { 
	
		//quotes and categories
		$howmanycategories = count(make_categories($current_user->user_nicename));
		if ($howmanycategories == 1)$howmanycategories = __('una categoría','stray-quotes');
		else { 
			if ($howmanycategories)
				$howmanycategories = $howmanycategories . ' ' . __('categorías','stray-quotes');
				$categorymost = mostused("category");	
		}		
		$sql = "SELECT COUNT( `category` ) AS `Rows` , `category` FROM `" . WP_STRAY_QUOTES_TABLE . "` WHERE `user`='".$current_user->user_nicename."' GROUP BY `category` ORDER BY `Rows` DESC";
		$howmany = $wpdb->get_results($sql);
		if ( count($howmany) > 1) $as = __(', distribuidos como sigue:','stray-quotes');
		else $as = '.';
        $search = array('%s1','%s2', '%s3');
        $replace = array($totalquotes, $howmanycategories, $as);
        echo str_replace ($search,$replace, __('<p>Ahora mismo tiene <strong>%s1 publicidades</strong> en <strong>%s2</strong>%s3</p>','stray-quotes'));
		if ($howmany && count($howmany) > 1) { ?>
		
			<table class="widefat" style="width:200px"><?php
				
			$i = 0;
			
			foreach ( $howmany as $many ) {
			
				$alt = ($i % 2 == 0) ? ' class="alternate"' : '';
				
				?><tr <?php echo($alt); ?>>
				<th scope="row"><?php echo $many->Rows; ?></th>
				<td><?php echo $many->category; ?></td>
				</tr><?php 
			} ?>
			</table><?php	
		}		
		
		//visible quotes
		$visiblequotes = $wpdb->get_var("SELECT COUNT(`quoteID`) as rows FROM " . WP_STRAY_QUOTES_TABLE . " WHERE visible='yes' AND `user`='".$current_user->user_nicename."'"); 
		if($visiblequotes == $totalquotes)$visiblequotes = __('Toda su publicidad ','stray-quotes');
		echo str_replace ('%s3',$visiblequotes, __('<p><strong>%s3</strong> está visible.</p>','stray-quotes'));
		
		//author
		$authormost = mostused("author");
		if ($authormost) echo str_replace ('%s5',$authormost, __('<p>Su enlace con más publicidades es <strong>%s5</strong>.</p>','stray-quotes'));
		
		//source
		$sourcemost = mostused("source");
		if ($sourcemost) str_replace ('%s5',$sourcemost, __('<p>Su imagen con más publicidades es <strong>%s5</strong>.</p>','stray-quotes'));
		
    } else _e('No hay ningún problema en el sistema.','stray-quotes');
    ?><p><?php
	
	$urls = $wpdb->get_results("SELECT DISTINCT link_url FROM wp_linkclicks");

	echo '<strong>Resultados para Diciembre 2010</strong><br />'; 	
	echo '<table><tr><td width="60"><strong>Clicks</strong></td><td width="800"><strong>URL</strong></td></tr>';
	foreach ($urls as $url){
		$contador = $wpdb->get_var("SELECT COUNT(*) FROM wp_linkclicks WHERE link_url = '$url->link_url'");
		echo '<tr><td width="60">'.$contador . '</td>';
		echo '<td width="800">'.$url->link_url . '</td></tr>';
	}
	echo '</table>';
	
	
	//link pages
    $search = array ("%s1", "%s2");
	$replace = array($new,$management);	
	//echo str_replace($search,$replace,__('Para empezar puede <a href="%s1"><strong>añadir publicidad nueva</strong></a>;<br />use el <a href="%s2"><strong>gestor</strong></a> para editar o borrar las publicidades;','stray-quotes')); 
    
    //if(current_user_can('manage_options')) echo str_replace("%s3",$options,__('<br />cambie las <a href="%s3"><strong>opciones</strong></a> para controlar cómo se muestran las publicidades;','stray-quotes'));
	
	$search2 = array ("%s4","%s5");
    $replace2 = array($help,$toolspage);	
	//echo str_replace($search2,$replace2,__('<br/>la <a href="%s5"><strong>página de herramientas</strong></a> puede ayudarle;<br/>Si es nuevo, visite la <a href="%s4"><strong>Página de ayuda</strong></a>.','stray-quotes')); ?>
    
	</p>
    
    <p><?php _e('Listado de totales. Los individuales se guardan con su fecha y hora de inclusión, por lo que se puede pedir un listado completo en cualquier momento. Ampliaremos las pantallas para visión y estudio de los datos en próximas fechas','stray-quotes'); ?>.<br/><?php _e('Feliz trabajo.','stray-quotes'); ?></p><br/>
    
    <?php //donate ?>
</div><?php
	
}
?>