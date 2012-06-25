<?php
/*
Copyright: © 2009 WebSharks, Inc. ( coded in the USA )
<mailto:support@websharks-inc.com> <http://www.websharks-inc.com/>

Released under the terms of the GNU General Public License.
You should have received a copy of the GNU General Public License,
along with this software. In the main directory, see: /licensing/
If not, see: <http://www.gnu.org/licenses/>.
*/
/*
Version: 110709
Stable tag: 110709
Framework: WS-W-110523

SSL Compatible: yes
WordPress Compatible: yes
WP Multisite Compatible: yes
Multisite Blog Farm Compatible: yes

Tested up to: 3.2
Requires at least: 3.1
Requires: WordPress® 3.1+, PHP 5.2.3+

Copyright: © 2009 WebSharks, Inc.
License: GNU General Public License
Contributors: WebSharks, PriMoThemes
Author URI: http://www.primothemes.com/
Author: PriMoThemes.com / WebSharks, Inc.
Donate link: http://www.primothemes.com/donate/

Plugin Name: Publicidad Noletia
Forum URI: http://www.primothemes.com/forums/viewforum.php?f=8
Privacy URI: http://www.primothemes.com/about/privacy-policy/
Plugin URI: http://www.primothemes.com/post/product/ad-codes-widget/
Description: The Ad Codes Widget allows you to place ANY size banner ( ad, ads, advertisements ) into a widget-ready bar for WordPress®. It supports AdSense®, Javascript, XHTML and more.
Tags: widget, widgets, ad codes, ads, adsense, google, sponsors, advertise, advertisements, banners, ad networks, banner rotation, options panel included, websharks framework, w3c validated code, multi widget support, includes extensive documentation, highly extensible
*/
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
	exit ("Do not access this file directly.");
/*
Define versions.
*/
@define ("WS_WIDGET__AD_CODES_VERSION", "110709");
@define ("WS_WIDGET__AD_CODES_MIN_PHP_VERSION", "5.2.3");
@define ("WS_WIDGET__AD_CODES_MIN_WP_VERSION", "3.1");
@define ("WS_WIDGET__AD_CODES_MIN_PRO_VERSION", "110709");
/*
Compatibility checks.
*/
if (version_compare (PHP_VERSION, WS_WIDGET__AD_CODES_MIN_PHP_VERSION, ">=") && version_compare (get_bloginfo ("version"), WS_WIDGET__AD_CODES_MIN_WP_VERSION, ">=") && !isset ($GLOBALS["WS_WIDGET__"]["ad_codes"]))
	{
		$GLOBALS["WS_WIDGET__"]["ad_codes"]["l"] = __FILE__;
		/*
		Hook before loaded.
		*/
		do_action ("ws_widget__ad_codes_before_loaded");
		/*
		System configuraton.
		*/
		include_once dirname (__FILE__) . "/includes/syscon.inc.php";
		/*
		Hooks and filters.
		*/
		include_once dirname (__FILE__) . "/includes/hooks.inc.php";
		/*
		Hook after system config & hooks are loaded.
		*/
		do_action ("ws_widget__ad_codes_config_hooks_loaded");
		/*
		Load a possible Pro module, if/when available.
		*/
		if (apply_filters ("ws_widget__ad_codes_load_pro", true) && file_exists (dirname (__FILE__) . "-pro/pro-module.php"))
			include_once dirname (__FILE__) . "-pro/pro-module.php";
		/*
		Configure options and their defaults now.
		*/
		ws_widget__ad_codes_configure_options_and_their_defaults ();
		/*
		Function includes.
		*/
		include_once dirname (__FILE__) . "/includes/funcs.inc.php";
		/*
		Hook after loaded.
		*/
		do_action ("ws_widget__ad_codes_after_loaded");
	}
else if (is_admin ()) /* Admin compatibility errors. */
	{
		if (!version_compare (PHP_VERSION, WS_WIDGET__AD_CODES_MIN_PHP_VERSION, ">="))
			{
				add_action ("all_admin_notices", create_function ('', 'echo \'<div class="error fade"><p>You need PHP v\' . WS_WIDGET__AD_CODES_MIN_PHP_VERSION . \'+ to use the Ad Codes widget.</p></div>\';'));
			}
		else if (!version_compare (get_bloginfo ("version"), WS_WIDGET__AD_CODES_MIN_WP_VERSION, ">="))
			{
				add_action ("all_admin_notices", create_function ('', 'echo \'<div class="error fade"><p>You need WordPress® v\' . WS_WIDGET__AD_CODES_MIN_WP_VERSION . \'+ to use the Ad Codes widget.</p></div>\';'));
			}
	}

/*************************************************************************************************************************************************
// Página de opciones de publi
*************************************************************************************************************************************************/
function set_publicidad_gestor_publi_cookie() {
	
	$location = 'http://'.$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];	
	if(isset($_POST['publicidad_ver_publi'])){
	
		if(isset($_POST['publicidad_publis_prov']) || isset($_POST['publicidad_publis_tipo'])){
			
			setcookie('publicidad_gest_port_tipo_prov', $_POST['publicidad_publis_tipo'].'---'.$_POST['publicidad_publis_prov'], time()+1080, "/", COOKIE_DOMAIN, false);
			wp_redirect( $location);
			exit;	
		}
	} 
}
add_action( 'admin_init', 'set_publicidad_gestor_publi_cookie');


function publicidad_admin_head() { ?>

<?php }

// VARIABLES

$themename = "Publicidad";
$shortname = "publicidad";
$manualurl = get_bloginfo('home');
$optionspubli = array();

add_option("publicidad_settings",$optionspubli);

$template_path = get_bloginfo('template_directory');

$layout_path = TEMPLATEPATH . '/layouts/'; 
$layouts = array();

$alt_stylesheet_path = TEMPLATEPATH . '/styles/';
$alt_stylesheets = array();

$functions_path = TEMPLATEPATH . '/functions/';


//Los offsets permitidos para la selección de posts automática
$publicidad_slide = array('Sin publicidad');
$publicidad_slide_id = array(0);

//El tipo de publi y la provincia de la publi que se está visualizando
$tipo_prov = $_COOKIE['publicidad_gest_port_tipo_prov'];
$tipo_prov = explode('---',$tipo_prov);
$tipo_publi = $tipo_prov[0];
$provincia_publi = $tipo_prov[1];

global $wpdb;
$publicidades = $wpdb->get_results( "SELECT * FROM `wp_stray_quotes` WHERE visible = 'yes'" );

//print_r( $publicidades );

$cont = 1;
foreach ( $publicidades as $publicidad ){
	$publicidad_slide_id[$cont] = $publicidad->quoteID;
	$publicidad_slide[$cont] = $publicidad->quote;
	$publicidad_slide_url[$cont] = $publicidad->author;
	$publicidad_slide_image[$cont] = $publicidad->source;
	
	$cont++;
}

// THESE ARE THE DIFFERENT FIELDS

$optionspubli = array (

				array(	"name" => "Cabecera Izquierda – 728×90",
						"type" => "heading"),
	
				array(	"name" => "Primera",
						"id" => $shortname."_".$tipo_publi."_".$provincia_publi."_supizq1",
						"std" => "Última",
						"type" => "select",
						"options" => $publicidad_slide,
						"ids" => $publicidad_slide_id),
				
				array(	"name" => "Segunda",
						"id" => $shortname."_".$tipo_publi."_".$provincia_publi."_supizq2",
						"std" => "Penúltima",
						"type" => "select",
						"options" => $publicidad_slide,
						"ids" => $publicidad_slide_id),
				
				array(	"name" => "Tercera",
						"id" => $shortname."_".$tipo_publi."_".$provincia_publi."_supizq3",
						"std" => "Antepenúltima",
						"type" => "select",
						"options" => $publicidad_slide,
						"ids" => $publicidad_slide_id),
				
				array(	"name" => "Cuarta",
						"id" => $shortname."_".$tipo_publi."_".$provincia_publi."_supizq4",
						"std" => "Anterior a la antepenúltima",
						"type" => "select",
						"options" => $publicidad_slide,
						"ids" => $publicidad_slide_id),
						
				array(	"name" => "Cabecera Derecha – 234×60",
						"type" => "heading"),
	
				array(	"name" => "Primera",
						"id" => $shortname."_".$tipo_publi."_".$provincia_publi."_supder1",
						"std" => "Última",
						"type" => "select",
						"options" => $publicidad_slide,
						"ids" => $publicidad_slide_id),
				
				array(	"name" => "Segunda",
						"id" => $shortname."_".$tipo_publi."_".$provincia_publi."_supder2",
						"std" => "Penúltima",
						"type" => "select",
						"options" => $publicidad_slide,
						"ids" => $publicidad_slide_id),
				
				array(	"name" => "Tercera",
						"id" => $shortname."_".$tipo_publi."_".$provincia_publi."_supder3",
						"std" => "Antepenúltima",
						"type" => "select",
						"options" => $publicidad_slide,
						"ids" => $publicidad_slide_id),
				
				array(	"name" => "Cuarta",
						"id" => $shortname."_".$tipo_publi."_".$provincia_publi."_supder4",
						"std" => "Anterior a la antepenúltima",
						"type" => "select",
						"options" => $publicidad_slide,
						"ids" => $publicidad_slide_id),
						
				array(	"name" => "Banner Lateral Principal – 245×303",
						"type" => "heading"),
	
				array(	"name" => "Primera",
						"id" => $shortname."_".$tipo_publi."_".$provincia_publi."_latderbig1",
						"std" => "Última",
						"type" => "select",
						"options" => $publicidad_slide,
						"ids" => $publicidad_slide_id),
				
				array(	"name" => "Segunda",
						"id" => $shortname."_".$tipo_publi."_".$provincia_publi."_latderbig2",
						"std" => "Penúltima",
						"type" => "select",
						"options" => $publicidad_slide,
						"ids" => $publicidad_slide_id),
				
				array(	"name" => "Tercera",
						"id" => $shortname."_".$tipo_publi."_".$provincia_publi."_latderbig3",
						"std" => "Antepenúltima",
						"type" => "select",
						"options" => $publicidad_slide,
						"ids" => $publicidad_slide_id),
				
				array(	"name" => "Cuarta",
						"id" => $shortname."_".$tipo_publi."_".$provincia_publi."_latderbig4",
						"std" => "Anterior a la antepenúltima",
						"type" => "select",
						"options" => $publicidad_slide,
						"ids" => $publicidad_slide_id),


				array(	"name" => "Banner Lateral Secundario 1 – 245×85",
						"type" => "heading"),
	
				array(	"name" => "Primera",
						"id" => $shortname."_".$tipo_publi."_".$provincia_publi."_latdersmall11",
						"std" => "Última",
						"type" => "select",
						"options" => $publicidad_slide,
						"ids" => $publicidad_slide_id),
				
				array(	"name" => "Segunda",
						"id" => $shortname."_".$tipo_publi."_".$provincia_publi."_latdersmall12",
						"std" => "Penúltima",
						"type" => "select",
						"options" => $publicidad_slide,
						"ids" => $publicidad_slide_id),
				
				array(	"name" => "Tercera",
						"id" => $shortname."_".$tipo_publi."_".$provincia_publi."_latdersmall13",
						"std" => "Antepenúltima",
						"type" => "select",
						"options" => $publicidad_slide,
						"ids" => $publicidad_slide_id),
				
				array(	"name" => "Cuarta",
						"id" => $shortname."_".$tipo_publi."_".$provincia_publi."_latdersmall14",
						"std" => "Anterior a la antepenúltima",
						"type" => "select",
						"options" => $publicidad_slide,
						"ids" => $publicidad_slide_id),
		
				array(	"name" => "Banner Lateral Secundario 2 – 245×85",
						"type" => "heading"),
	
				array(	"name" => "Primera",
						"id" => $shortname."_".$tipo_publi."_".$provincia_publi."_latdersmall21",
						"std" => "Última",
						"type" => "select",
						"options" => $publicidad_slide,
						"ids" => $publicidad_slide_id),
				
				array(	"name" => "Segunda",
						"id" => $shortname."_".$tipo_publi."_".$provincia_publi."_latdersmall22",
						"std" => "Penúltima",
						"type" => "select",
						"options" => $publicidad_slide,
						"ids" => $publicidad_slide_id),
				
				array(	"name" => "Tercera",
						"id" => $shortname."_".$tipo_publi."_".$provincia_publi."_latdersmall23",
						"std" => "Antepenúltima",
						"type" => "select",
						"options" => $publicidad_slide,
						"ids" => $publicidad_slide_id),
				
				array(	"name" => "Cuarta",
						"id" => $shortname."_".$tipo_publi."_".$provincia_publi."_latdersmall24",
						"std" => "Anterior a la antepenúltima",
						"type" => "select",
						"options" => $publicidad_slide,
						"ids" => $publicidad_slide_id),
		
				array(	"name" => "Banner Lateral Secundario 3 – 245×85",
						"type" => "heading"),
	
				array(	"name" => "Primera",
						"id" => $shortname."_".$tipo_publi."_".$provincia_publi."_latdersmall31",
						"std" => "Última",
						"type" => "select",
						"options" => $publicidad_slide,
						"ids" => $publicidad_slide_id),
				
				array(	"name" => "Segunda",
						"id" => $shortname."_".$tipo_publi."_".$provincia_publi."_latdersmall32",
						"std" => "Penúltima",
						"type" => "select",
						"options" => $publicidad_slide,
						"ids" => $publicidad_slide_id),
				
				array(	"name" => "Tercera",
						"id" => $shortname."_".$tipo_publi."_".$provincia_publi."_latdersmall33",
						"std" => "Antepenúltima",
						"type" => "select",
						"options" => $publicidad_slide,
						"ids" => $publicidad_slide_id),
				
				array(	"name" => "Cuarta",
						"id" => $shortname."_".$tipo_publi."_".$provincia_publi."_latdersmall34",
						"std" => "Anterior a la antepenúltima",
						"type" => "select",
						"options" => $publicidad_slide,
						"ids" => $publicidad_slide_id),

				array(	"name" => "Facebook",
						"type" => "heading"),
	
				array(	"name" => "Primera",
						"id" => $shortname."_".$tipo_publi."_".$provincia_publi."_facebook1",
						"std" => "Última",
						"type" => "select",
						"options" => $publicidad_slide,
						"ids" => $publicidad_slide_id),
				
				array(	"name" => "Segunda",
						"id" => $shortname."_".$tipo_publi."_".$provincia_publi."_facebook2",
						"std" => "Penúltima",
						"type" => "select",
						"options" => $publicidad_slide,
						"ids" => $publicidad_slide_id),
				
				array(	"name" => "Tercera",
						"id" => $shortname."_".$tipo_publi."_".$provincia_publi."_facebook3",
						"std" => "Antepenúltima",
						"type" => "select",
						"options" => $publicidad_slide,
						"ids" => $publicidad_slide_id),
				
				array(	"name" => "Cuarta",
						"id" => $shortname."_".$tipo_publi."_".$provincia_publi."_facebook4",
						"std" => "Anterior a la antepenúltima",
						"type" => "select",
						"options" => $publicidad_slide,
						"ids" => $publicidad_slide_id),

				
				array(	"name" => "Banner Horizontal Intermedio – 695×90",
						"type" => "heading"),
	
				array(	"name" => "Primera",
						"id" => $shortname."_".$tipo_publi."_".$provincia_publi."_horizport1",
						"std" => "Última",
						"type" => "select",
						"options" => $publicidad_slide,
						"ids" => $publicidad_slide_id),
				
				array(	"name" => "Segunda",
						"id" => $shortname."_".$tipo_publi."_".$provincia_publi."_horizport2",
						"std" => "Penúltima",
						"type" => "select",
						"options" => $publicidad_slide,
						"ids" => $publicidad_slide_id),
				
				array(	"name" => "Tercera",
						"id" => $shortname."_".$tipo_publi."_".$provincia_publi."_horizport3",
						"std" => "Antepenúltima",
						"type" => "select",
						"options" => $publicidad_slide,
						"ids" => $publicidad_slide_id),
				
				array(	"name" => "Cuarta",
						"id" => $shortname."_".$tipo_publi."_".$provincia_publi."_horizport4",
						"std" => "Anterior a la antepenúltima",
						"type" => "select",
						"options" => $publicidad_slide,
						"ids" => $publicidad_slide_id),


				array(	"name" => "Faldón (banner pie de página) – 968×130",
						"type" => "heading"),
	
				array(	"name" => "Primera",
						"id" => $shortname."_".$tipo_publi."_".$provincia_publi."_pie1",
						"std" => "Última",
						"type" => "select",
						"options" => $publicidad_slide,
						"ids" => $publicidad_slide_id),
				
				array(	"name" => "Segunda",
						"id" => $shortname."_".$tipo_publi."_".$provincia_publi."_pie2",
						"std" => "Penúltima",
						"type" => "select",
						"options" => $publicidad_slide,
						"ids" => $publicidad_slide_id),
				
				array(	"name" => "Tercera",
						"id" => $shortname."_".$tipo_publi."_".$provincia_publi."_pie3",
						"std" => "Antepenúltima",
						"type" => "select",
						"options" => $publicidad_slide,
						"ids" => $publicidad_slide_id),
				
				array(	"name" => "Cuarta",
						"id" => $shortname."_".$tipo_publi."_".$provincia_publi."_pie4",
						"std" => "Anterior a la antepenúltima",
						"type" => "select",
						"options" => $publicidad_slide,
						"ids" => $publicidad_slide_id),
					
			);
				
				

// ADMIN PANEL

function publicidad_add_admin() {

	 global $themename, $optionspubli;
	
	if ( $_GET['page'] == basename(__FILE__) ) {
        if ( 'save' == $_REQUEST['action'] ) {
                foreach ($optionspubli as $value) {
					if($value['type'] != 'multicheck'){
                    	update_option( $value['id'], $_REQUEST[ $value['id'] ] ); 
					}else{
						foreach($value['options'] as $mc_key => $mc_value){
							$up_opt = $value['id'].'_'.$mc_key;
							update_option($up_opt, $_REQUEST[$up_opt] );
						}
					}
				}

                foreach ($optionspubli as $value) {
					if($value['type'] != 'multicheck'){
                    	if( isset( $_REQUEST[ $value['id'] ] ) ) { update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); } else { /*delete_option( $value['id'] );*/ } 
					}else{
						foreach($value['options'] as $mc_key => $mc_value){
							$up_opt = $value['id'].'_'.$mc_key;						
							if( isset( $_REQUEST[ $up_opt ] ) ) { update_option( $up_opt, $_REQUEST[ $up_opt ]  ); } else { /*delete_option( $up_opt );*/ } 
						}
					}
				}
						
				header("Location: admin.php?page=publicidad.php&saved=true");								
			
			die;

		} else if ( 'reset' == $_REQUEST['action'] ) {
			delete_option('sandbox_logo');
			
			header("Location: admin.php?page=publicidad.php&reset=true");
			die;
		}
	}

add_menu_page("Gestión Publi", "Gestión Publi", 'moderate_comments', basename(__FILE__), 'publicidad_page');
}


function publicidad_page (){

	global $optionspubli, $themename, $manualurl;
		

	//Mostramos en el primer select todos los TIPOS de publis		
	$publicidad_tipos_ids[0] = 'musica';
	$publicidad_tipo_nombre[0] = 'Música';
	$publicidad_tipos_ids[1] = 'artes-escenicas';
	$publicidad_tipo_nombre[1] = 'Artes escénicas';
	$publicidad_tipos_ids[2] = 'arte';
	$publicidad_tipo_nombre[2] = 'Arte';
	$publicidad_tipos_ids[3] = 'literatura';
	$publicidad_tipo_nombre[3] = 'Literatura';
	$publicidad_tipos_ids[4] = 'audiovisual';
	$publicidad_tipo_nombre[4] = 'Audiovisual';
	$publicidad_tipos_ids[5] = 'formacion';
	$publicidad_tipo_nombre[5] = 'Formación';    
  	$publicidad_tipos_ids[6] = 'descuentos';
	$publicidad_tipo_nombre[6] = 'Descuentos';
  	$publicidad_tipos_ids[7] = 'concursos';
	$publicidad_tipo_nombre[7] = 'Concursos';
  	$publicidad_tipos_ids[8] = 'agenda';
	$publicidad_tipo_nombre[8] = 'Agenda';

	//Mostramos en el segundo select todas las PROVINCIAS
/*	$cont_prov = 0;
	$provincias = get_categories('child_of=14&hide_empty=0'); 
	foreach ($provincias as $provincia) {
  	
  		$publicidad_prov_id[$cont_prov] = $provincia->slug;
		$publicidad_prov_name[$cont_prov] = $provincia->cat_name;
  	
		$cont_prov++;
	}*/

	$publicidad_prov_id[0] = 'a-coruna';
	$publicidad_prov_name[0] = 'A Coruña';
	$publicidad_prov_id[1] = 'almeria';
	$publicidad_prov_name[1] = 'Almería';
	$publicidad_prov_id[2] = 'cadiz';
	$publicidad_prov_name[2] = 'Cádiz';
	$publicidad_prov_id[3] = 'ciudad-real';
	$publicidad_prov_name[3] = 'Ciudad Real';
	$publicidad_prov_id[4] = 'cordoba';
	$publicidad_prov_name[4] = 'Córdoba';
	$publicidad_prov_id[5] = 'granada';
	$publicidad_prov_name[5] = 'Granada';
	$publicidad_prov_id[6] = 'huelva';
	$publicidad_prov_name[6] = 'Huelva';
	$publicidad_prov_id[7] = 'madrid';
	$publicidad_prov_name[7] = 'Madrid';
	$publicidad_prov_id[8] = 'malaga';
	$publicidad_prov_name[8] = 'Málaga';
	$publicidad_prov_id[9] = 'ourense';
	$publicidad_prov_name[9] = 'Ourense';
	$publicidad_prov_id[10] = 'pontevedra';
	$publicidad_prov_name[10] = 'Pontevedra';
	$publicidad_prov_id[11] = 'toledo';
	$publicidad_prov_name[11] = 'Toledo';
	$publicidad_prov_id[12] = 'sevilla';
	$publicidad_prov_name[12] = 'Sevilla';

		
		//$tipo_publi = $_POST['publicidad_publis_tipo'];
		//$provincia_publi = $_POST['publicidad_publis_prov'];
		
		$tipo_prov = $_COOKIE['publicidad_gest_port_tipo_prov'];
		$tipo_prov = explode('---',$tipo_prov);
		$cookie_tipo = $tipo_prov[0];
		$cookie_prov = $tipo_prov[1];
		?>
		<div class="wrap">
    		
    		<div class="seleccion-tipo-publi">
    			<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" name="form-seleccion-tipo-publi">
				<h2>Publicidad</h2>
				<p style="clear:both;width:90%;border:1px solid #E6DB55;padding:10px;background-color:#FFFFE0;">Si seleccionas "sin publicidad" no tendrá en cuenta los campos para los porcentajes.</p>
						
					<div style="clear:both;height:20px;"></div>  			
					<!--START: GENERAL SETTINGS-->
     						
     					<table class="maintable">
     						<tr class="mainrow">
     							<td class="titledesc" style="margin: -5px 0 0 0;vertical-align:text-top;">Selecciona la portada que deseas personalizar:</td>
							</tr>	
							<tr>
								<td class="forminp">
									<select name="publicidad_publis_tipo" id="publicidad_publis_tipo" style="width: 300px;margin-right:80px;">
	                					<option <?php if($cookie_tipo == 'global'){ echo ' selected="selected"'; } ?> value="global">Global</option>
	                					<?php for($i=0;$i<=8;$i++){ ?>
	                						<option <?php if($cookie_tipo == $publicidad_tipos_ids[$i]){echo ' selected="selected"';}?> value="<?php echo $publicidad_tipos_ids[$i]; ?>">
	                							<?php echo $publicidad_tipo_nombre[$i]; ?>
	                						</option>
	                					<?php } ?>
	            					</select>
	            					<br/><br />
									<span>Selecciona la opción 'Global' si deseas ver la portada principal.<br /><br /></span>
								</td>
								<td class="forminp">
									<select name="publicidad_publis_prov" id="publicidad_publis_prov" style="width: 300px;">
	                					<option <?php if($cookie_prov == 'todas') { echo ' selected="selected"'; }?> value="todas">Todas</option>
	                					<?php for($j=0;$j<=12;$j++){ ?>
	                						<option<?php if($cookie_prov == $publicidad_prov_id[$j]){echo ' selected="selected"';}?> value="<?php echo $publicidad_prov_id[$j]; ?>">
	                							<?php echo $publicidad_prov_name[$j]; ?>
	                						</option>
	                					<?php } ?>
	            					</select>
	            					<br/><br />
									<span>Selecciona la opción 'Todas' si deseas ver la portada sin ningún filtro por provincias.<br /><br /></span>
								</td>	
							</tr>		
						</table>
			

							<p class="submit">
								<input class="button-primary" name="save" type="submit" value="Ver Portada" />    
								<input type="hidden" name="publicidad_ver_publi" value="true" />
							</p>							
							
							<div style="clear:both;"></div>		
						
						<!--END: GENERAL SETTINGS-->						  
            	</form>
    		</div><!--  // End if div.seleccion-tipo-publi -->
    
    
 			<?php if(isset($_COOKIE['publicidad_gest_port_tipo_prov'])){ ?>
 			
 				<div class="secciones-de-publi">
 				<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" name="form-secciones-de-publi">

					<?php if ( $_REQUEST['saved'] ) { ?><div style="clear:both;height:20px;width:90%;border:1px solid #E6DB55;padding:20px;background-color:#FFFFE0;">La publicidad se ha actualizado</div><?php } ?>
											
					<div style="clear:both;height:20px;"></div>  			
					

					<!--START: GENERAL SETTINGS-->
     						
     					<table class="maintable">
     				
     				
     				
     				
     				
     							
							<?php foreach ($optionspubli as $value) { ?>
	
									<?php if ( $value['type'] <> "heading" ) { ?>
	
										<tr class="mainrow">
										<td class="titledesc" style="margin: -5px 0 0 0;vertical-align:text-top;"><?php echo $value['name']; ?></td>
										<td class="forminp">
		
									<?php } ?>		 
	
									<?php
										
										switch ( $value['type'] ) {
										
										case 'select':?>
										
											<select name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" style="width: 300px">
	                						<?php $i=0; ?>
	                						<?php foreach ($value['options'] as $option) { ?>
	                							<?php $ids = $value['ids']; ?>
	                							<option<?php if ( get_settings( $value['id'] ) == $ids[$i]) { echo ' selected="selected"'; } elseif ($option == $value['std']) { echo ' selected="selected"'; } ?> value="<?php echo $ids[$i]; ?>"><?php echo $option; ?></option>
	                							<?php $i++; ?>
	                						<?php } ?>
	            							</select><?php
		
										break;
										
										case "heading":
									?>
											</table> 	
		    								<h3 class="title"><?php echo $value['name']; ?></h3>
											<table class="maintable">
									<?php
										
										break;
										default:
										break;
									
									} ?>
	
									<?php if ( $value['type'] <> "heading" ) { ?>
	
										<?php if ( $value['type'] <> "checkbox" ) { ?><br/><br /><?php } ?><span><?php echo $value['desc']; ?></span>
										</td></tr>
	
									<?php } ?>		
									
							<?php } ?>	
							
							</table>	


							<p class="submit">
								<input class="button-primary" name="save" type="submit" value="Guardar cambios" />    
								<input type="hidden" name="action" value="save" />
							</p>							
							
							<div style="clear:both;"></div>		
						
						<!--END: GENERAL SETTINGS-->						  
            	</form>
 				</div>
 			
 			<?php } // End if div.secciones-de-publi ?>
 			
			
            
</div><!--wrap-->

<div style="clear:both;height:20px;"></div>
 
 <?php

};

add_action('admin_menu', 'publicidad_add_admin');
add_action('admin_head', 'publicidad_admin_head');



/***********************************************************************************************************/
//****  FUNCIÓN PARA MOSTRAR LA PUBLICIDAD  ******//
/***********************************************************************************************************/

//Función para mostrar las publicidades

function mostrar_sup_izq(){
	
	global $wpdb;
	
	$prov_portada = ( isset( $_COOKIE['noletia_prov'] ) ) ? $_COOKIE['noletia_prov'] : 'todas';
	if ( $prov_portada == 'A Coruña') $prov_portada = 'a-coruna';
	if ( $prov_portada == 'Santiago de Compostela') $prov_portada = 'santiago-de-compostela';
	if ( $prov_portada == 'Almería') $prov_portada = 'almeria';
	if ( $prov_portada == 'Cádiz') $prov_portada = 'cadiz';
	if ( $prov_portada == 'Chiclana de la Frontera') $prov_portada = 'chiclana-de-la-frontera';
	if ( $prov_portada == 'El Puerto de Santa María') $prov_portada = 'el-puerto-de-santa-maria';
	if ( $prov_portada == 'Jerez de la Frontera') $prov_portada = 'jerez-de-la-frontera';
	if ( $prov_portada == 'Puerto Real') $prov_portada = 'puerto-real';
	if ( $prov_portada == 'San Fernando') $prov_portada = 'san-fernando';
	if ( $prov_portada == 'Villaluenga del Rosario') $prov_portada = 'villaluenga-del-rosario';
	if ( $prov_portada == 'Ciudad Real') $prov_portada = 'ciudad-real';
	if ( $prov_portada == 'Córdoba') $prov_portada = 'cordoba';
	if ( $prov_portada == 'Granada') $prov_portada = 'granada';
	if ( $prov_portada == 'Huelva') $prov_portada = 'huelva';
	if ( $prov_portada == 'Jaén') $prov_portada = 'jaen';
	if ( $prov_portada == 'Madrid') $prov_portada = 'madrid';
	if ( $prov_portada == 'Málaga') $prov_portada = 'malaga';
	if ( $prov_portada == 'Nacional') $prov_portada = 'nacional';
	if ( $prov_portada == 'Ourense') $prov_portada = 'ourense';
	if ( $prov_portada == 'Pontevedra') $prov_portada = 'pontevedra';
	if ( $prov_portada == 'Vigo') $prov_portada = 'vigo-pontevedra';
	$tipo_portada = nombre_portada_de_seccion();
	$tipo_portada = ( ( $tipo_portada == '' ) ) ? 'global' : $tipo_portada; 
	if(is_single()){
		if(in_category('musica')){
		    $tipo_portada = 'musica';
		}elseif(in_category('artes-escenicas')){
		    $tipo_portada = 'artes-escenicas';
		}elseif(in_category('arte')){
		    $tipo_portada = 'arte';
		}elseif(in_category('literatura')){
		    $tipo_portada = 'literatura';
		}elseif(in_category('audiovisual')){
		    $tipo_portada = 'audiovisual';
		}elseif(in_category('formacion')){
		    $tipo_portada = 'formacion';
		}elseif(in_category('descuentos')){
		    $tipo_portada = 'descuentos';
		}elseif(in_category('concursos')){
		    $tipo_portada = 'concursos';
		}else{
		    
		    if(is_object_in_term( $post->ID, 'event-categories', 'musica')){
		  	  $tipo_portada = 'musica';
		    }elseif(is_object_in_term($post->ID, 'event-categories', 'artes-escenicas')){
		   	 $tipo_portada = 'artes-escenicas';
		    }elseif(is_object_in_term($post->ID, 'event-categories', 'arte')){
		    $tipo_portada = 'arte';
		    }elseif(is_object_in_term($post->ID, 'event-categories', 'literatura')){
		    $tipo_portada = 'literatura';
		    }elseif(is_object_in_term($post->ID, 'event-categories', 'audiovisual')){
		    $tipo_portada = 'audiovisual';
		    }elseif(is_object_in_term($post->ID, 'event-categories', 'formacion')){
		    $tipo_portada = 'formacion';
		    }elseif(is_object_in_term($post->ID, 'event-categories', 'descuentos')){
		    $tipo_portada = 'descuentos';
		    }elseif(is_object_in_term($post->ID, 'event-categories', 'concursos')){
		    $tipo_portada = 'concursos';
		    }else{
		    $tipo_portada = 'agenda';
		    }
		    
		}
					
	}

	$tipo_portada_publi = $tipo_portada;
	$prov_portada_publi = $prov_portada;

	$publi_sup_izq_1 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_supizq1' );
	$publi_sup_izq_2 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_supizq2' );
	$publi_sup_izq_3 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_supizq3' );
	$publi_sup_izq_4 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_supizq4' );
	
	$publi_sup = '';
	if ( $publi_sup_izq_1 != '0' ) $publi_sup[] = $publi_sup_izq_1;
	if ( $publi_sup_izq_2 != '0' ) $publi_sup[] = $publi_sup_izq_2;
	if ( $publi_sup_izq_3 != '0' ) $publi_sup[] = $publi_sup_izq_3;
	if ( $publi_sup_izq_4 != '0' ) $publi_sup[] = $publi_sup_izq_4;
	
	if ( $publi_sup != '' ) {	
		shuffle( $publi_sup );
		$id = $publi_sup[0];
		$mostrar = $wpdb->get_row( "SELECT * FROM `wp_stray_quotes` WHERE quoteID = '$id'" );
		if ( $mostrar->author == 'adsense' ) {
			echo '<div class="pub_sup_left" id="pub_sup_left">';
			echo $mostrar->source;
			echo '</div>';
		} else {
			echo '<div class="pub_sup_left">';
			$add_publi = "add_publi('$id','$tipo_portada_publi','$prov_portada_publi','$mostrar->quote')";
			echo '<a rel="external" href="'.$mostrar->author.'" target="_blank" onclick="'.$add_publi.'"><img src="'.$mostrar->source.'" alt ="'.$mostrar->quote.'" /></a>';
			echo '</div>';
		} // adsense
	} else {
		$prov_portada = 'todas';
		$tipo_portada = 'global';
	
		$publi_sup_izq_1 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_supizq1' );
		$publi_sup_izq_2 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_supizq2' );
		$publi_sup_izq_3 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_supizq3' );
		$publi_sup_izq_4 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_supizq4' );

		$publi_sup = '';
		if ( $publi_sup_izq_1 != '0' ) $publi_sup[] = $publi_sup_izq_1;
		if ( $publi_sup_izq_2 != '0' ) $publi_sup[] = $publi_sup_izq_2;
		if ( $publi_sup_izq_3 != '0' ) $publi_sup[] = $publi_sup_izq_3;
		if ( $publi_sup_izq_4 != '0' ) $publi_sup[] = $publi_sup_izq_4;

		if ( $publi_sup != '' ) {	
			shuffle( $publi_sup );
			$id = $publi_sup[0];
			$mostrar = $wpdb->get_row( "SELECT * FROM `wp_stray_quotes` WHERE quoteID = '$id'" );
			if ( $mostrar->author == 'adsense' ) {
				echo '<div class="pub_sup_left">';
				echo $mostrar->source;
				echo '</div>';
			} else {
				echo '<div class="pub_sup_left">';
				$add_publi = "add_publi('$id','$tipo_portada_publi','$prov_portada_publi','$mostrar->quote')";
				echo '<a rel="external" href="'.$mostrar->author.'" target="_blank" onclick="'.$add_publi.'"><img src="'.$mostrar->source.'" alt ="'.$mostrar->quote.'" /></a>';
				echo '</div>';
			} // adsense
		} // publicidad para el global
	
	}
	
}


function mostrar_sup_der(){
	
	global $wpdb;
	
	$prov_portada = ( isset( $_COOKIE['noletia_prov'] ) ) ? $_COOKIE['noletia_prov'] : 'todas';
	if ( $prov_portada == 'A Coruña') $prov_portada = 'a-coruna';
	if ( $prov_portada == 'Santiago de Compostela') $prov_portada = 'santiago-de-compostela';
	if ( $prov_portada == 'Almería') $prov_portada = 'almeria';
	if ( $prov_portada == 'Cádiz') $prov_portada = 'cadiz';
	if ( $prov_portada == 'Chiclana de la Frontera') $prov_portada = 'chiclana-de-la-frontera';
	if ( $prov_portada == 'El Puerto de Santa María') $prov_portada = 'el-puerto-de-santa-maria';
	if ( $prov_portada == 'Jerez de la Frontera') $prov_portada = 'jerez-de-la-frontera';
	if ( $prov_portada == 'Puerto Real') $prov_portada = 'puerto-real';
	if ( $prov_portada == 'San Fernando') $prov_portada = 'san-fernando';
	if ( $prov_portada == 'Villaluenga del Rosario') $prov_portada = 'villaluenga-del-rosario';
	if ( $prov_portada == 'Ciudad Real') $prov_portada = 'ciudad-real';
	if ( $prov_portada == 'Córdoba') $prov_portada = 'cordoba';
	if ( $prov_portada == 'Granada') $prov_portada = 'granada';
	if ( $prov_portada == 'Huelva') $prov_portada = 'huelva';
	if ( $prov_portada == 'Jaén') $prov_portada = 'jaen';
	if ( $prov_portada == 'Madrid') $prov_portada = 'madrid';
	if ( $prov_portada == 'Málaga') $prov_portada = 'malaga';
	if ( $prov_portada == 'Nacional') $prov_portada = 'nacional';
	if ( $prov_portada == 'Ourense') $prov_portada = 'ourense';
	if ( $prov_portada == 'Pontevedra') $prov_portada = 'pontevedra';
	if ( $prov_portada == 'Vigo') $prov_portada = 'vigo-pontevedra';
	$tipo_portada = nombre_portada_de_seccion();
	$tipo_portada = ( ( $tipo_portada == '' ) ) ? 'global' : $tipo_portada; 
	if(is_single()){
		if(in_category('musica')){
		    $tipo_portada = 'musica';
		}elseif(in_category('artes-escenicas')){
		    $tipo_portada = 'artes-escenicas';
		}elseif(in_category('arte')){
		    $tipo_portada = 'arte';
		}elseif(in_category('literatura')){
		    $tipo_portada = 'literatura';
		}elseif(in_category('audiovisual')){
		    $tipo_portada = 'audiovisual';
		}elseif(in_category('formacion')){
		    $tipo_portada = 'formacion';
		}elseif(in_category('descuentos')){
		    $tipo_portada = 'descuentos';
		}elseif(in_category('concursos')){
		    $tipo_portada = 'concursos';
		}else{
		    
		    if(is_object_in_term( $post->ID, 'event-categories', 'musica')){
		  	  $tipo_portada = 'musica';
		    }elseif(is_object_in_term($post->ID, 'event-categories', 'artes-escenicas')){
		   	 $tipo_portada = 'artes-escenicas';
		    }elseif(is_object_in_term($post->ID, 'event-categories', 'arte')){
		    $tipo_portada = 'arte';
		    }elseif(is_object_in_term($post->ID, 'event-categories', 'literatura')){
		    $tipo_portada = 'literatura';
		    }elseif(is_object_in_term($post->ID, 'event-categories', 'audiovisual')){
		    $tipo_portada = 'audiovisual';
		    }elseif(is_object_in_term($post->ID, 'event-categories', 'formacion')){
		    $tipo_portada = 'formacion';
		    }elseif(is_object_in_term($post->ID, 'event-categories', 'descuentos')){
		    $tipo_portada = 'descuentos';
		    }elseif(is_object_in_term($post->ID, 'event-categories', 'concursos')){
		    $tipo_portada = 'concursos';
		    }else{
		    $tipo_portada = 'agenda';
		    }
		    
		}
					
	}


	$tipo_portada_publi = $tipo_portada;
	$prov_portada_publi = $prov_portada;

	$publi_sup_der_1 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_supder1' );
	$publi_sup_der_2 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_supder2' );
	$publi_sup_der_3 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_supder3' );
	$publi_sup_der_4 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_supder4' );
	
	$publi_sup = '';
	if ( $publi_sup_der_1 != '0' ) $publi_sup[] = $publi_sup_der_1;
	if ( $publi_sup_der_2 != '0' ) $publi_sup[] = $publi_sup_der_2;
	if ( $publi_sup_der_3 != '0' ) $publi_sup[] = $publi_sup_der_3;
	if ( $publi_sup_der_4 != '0' ) $publi_sup[] = $publi_sup_der_4;
	
	if ( $publi_sup != '' ) {	
		shuffle( $publi_sup );
		$id = $publi_sup[0];
		$mostrar = $wpdb->get_row( "SELECT * FROM `wp_stray_quotes` WHERE quoteID = '$id'" );
		if ( $mostrar->author == 'adsense' ) {
			echo '<div class="pub_sup_right"><p>Publicidad</p>';
			echo $mostrar->source;
			echo '</div>';
		} else {
			echo '<div class="pub_sup_right"><p>Publicidad</p>';
						$add_publi = "add_publi('$id','$tipo_portada_publi','$prov_portada_publi','$mostrar->quote')";
			echo '<a rel="external" href="'.$mostrar->author.'" target="_blank" onclick="'.$add_publi.'"><img src="'.$mostrar->source.'" alt ="'.$mostrar->quote.'" /></a>';

			echo '</div>';
		} // adsense
	} else {
		$prov_portada = 'todas';
		$tipo_portada = 'global';
	
		$publi_sup_der_1 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_supder1' );
		$publi_sup_der_2 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_supder2' );
		$publi_sup_der_3 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_supder3' );
		$publi_sup_der_4 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_supder4' );

		$publi_sup = '';
		if ( $publi_sup_der_1 != '0' ) $publi_sup[] = $publi_sup_der_1;
		if ( $publi_sup_der_2 != '0' ) $publi_sup[] = $publi_sup_der_2;
		if ( $publi_sup_der_3 != '0' ) $publi_sup[] = $publi_sup_der_3;
		if ( $publi_sup_der_4 != '0' ) $publi_sup[] = $publi_sup_der_4;

		if ( $publi_sup != '' ) {	
			shuffle( $publi_sup );
			$id = $publi_sup[0];
			$mostrar = $wpdb->get_row( "SELECT * FROM `wp_stray_quotes` WHERE quoteID = '$id'" );
			if ( $mostrar->author == 'adsense' ) {
				echo '<div class="pub_sup_right"><p>Publicidad</p>';
				echo $mostrar->source;
				echo '</div>';
			} else {
				echo '<div class="pub_sup_right"><p>Publicidad</p>';
			$add_publi = "add_publi('$id','$tipo_portada_publi','$prov_portada_publi','$mostrar->quote')";
			echo '<a rel="external" href="'.$mostrar->author.'" target="_blank" onclick="'.$add_publi.'"><img src="'.$mostrar->source.'" alt ="'.$mostrar->quote.'" /></a>';
				echo '</div>';
			} // adsense
		} // publicidad para el global
	
	}
	
}

function mostrar_lat_der_big(){
	
	global $wpdb;
	
	$prov_portada = ( isset( $_COOKIE['noletia_prov'] ) ) ? $_COOKIE['noletia_prov'] : 'todas';
	if ( $prov_portada == 'A Coruña') $prov_portada = 'a-coruna';
	if ( $prov_portada == 'Santiago de Compostela') $prov_portada = 'santiago-de-compostela';
	if ( $prov_portada == 'Almería') $prov_portada = 'almeria';
	if ( $prov_portada == 'Cádiz') $prov_portada = 'cadiz';
	if ( $prov_portada == 'Chiclana de la Frontera') $prov_portada = 'chiclana-de-la-frontera';
	if ( $prov_portada == 'El Puerto de Santa María') $prov_portada = 'el-puerto-de-santa-maria';
	if ( $prov_portada == 'Jerez de la Frontera') $prov_portada = 'jerez-de-la-frontera';
	if ( $prov_portada == 'Puerto Real') $prov_portada = 'puerto-real';
	if ( $prov_portada == 'San Fernando') $prov_portada = 'san-fernando';
	if ( $prov_portada == 'Villaluenga del Rosario') $prov_portada = 'villaluenga-del-rosario';
	if ( $prov_portada == 'Ciudad Real') $prov_portada = 'ciudad-real';
	if ( $prov_portada == 'Córdoba') $prov_portada = 'cordoba';
	if ( $prov_portada == 'Granada') $prov_portada = 'granada';
	if ( $prov_portada == 'Huelva') $prov_portada = 'huelva';
	if ( $prov_portada == 'Jaén') $prov_portada = 'jaen';
	if ( $prov_portada == 'Madrid') $prov_portada = 'madrid';
	if ( $prov_portada == 'Málaga') $prov_portada = 'malaga';
	if ( $prov_portada == 'Nacional') $prov_portada = 'nacional';
	if ( $prov_portada == 'Ourense') $prov_portada = 'ourense';
	if ( $prov_portada == 'Pontevedra') $prov_portada = 'pontevedra';
	if ( $prov_portada == 'Vigo') $prov_portada = 'vigo-pontevedra';
	$tipo_portada = nombre_portada_de_seccion();
	$tipo_portada = ( ( $tipo_portada == '' ) ) ? 'global' : $tipo_portada; 
	if(is_single()){
		if(in_category('musica')){
		    $tipo_portada = 'musica';
		}elseif(in_category('artes-escenicas')){
		    $tipo_portada = 'artes-escenicas';
		}elseif(in_category('arte')){
		    $tipo_portada = 'arte';
		}elseif(in_category('literatura')){
		    $tipo_portada = 'literatura';
		}elseif(in_category('audiovisual')){
		    $tipo_portada = 'audiovisual';
		}elseif(in_category('formacion')){
		    $tipo_portada = 'formacion';
		}elseif(in_category('descuentos')){
		    $tipo_portada = 'descuentos';
		}elseif(in_category('concursos')){
		    $tipo_portada = 'concursos';
		}else{
		    
		    if(is_object_in_term( $post->ID, 'event-categories', 'musica')){
		  	  $tipo_portada = 'musica';
		    }elseif(is_object_in_term($post->ID, 'event-categories', 'artes-escenicas')){
		   	 $tipo_portada = 'artes-escenicas';
		    }elseif(is_object_in_term($post->ID, 'event-categories', 'arte')){
		    $tipo_portada = 'arte';
		    }elseif(is_object_in_term($post->ID, 'event-categories', 'literatura')){
		    $tipo_portada = 'literatura';
		    }elseif(is_object_in_term($post->ID, 'event-categories', 'audiovisual')){
		    $tipo_portada = 'audiovisual';
		    }elseif(is_object_in_term($post->ID, 'event-categories', 'formacion')){
		    $tipo_portada = 'formacion';
		    }elseif(is_object_in_term($post->ID, 'event-categories', 'descuentos')){
		    $tipo_portada = 'descuentos';
		    }elseif(is_object_in_term($post->ID, 'event-categories', 'concursos')){
		    $tipo_portada = 'concursos';
		    }else{
		    $tipo_portada = 'agenda';
		    }
		    
		}
					
	}

	$tipo_portada_publi = $tipo_portada;
	$prov_portada_publi = $prov_portada;

	$publi_lat_der_big_1 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_latderbig1' );
	$publi_lat_der_big_2 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_latderbig2' );
	$publi_lat_der_big_3 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_latderbig3' );
	$publi_lat_der_big_4 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_latderbig4' );
	
	$publi_lat = '';
	if ( $publi_lat_der_big_1 != '0' ) $publi_lat[] = $publi_lat_der_big_1;
	if ( $publi_lat_der_big_2 != '0' ) $publi_lat[] = $publi_lat_der_big_2;
	if ( $publi_lat_der_big_3 != '0' ) $publi_lat[] = $publi_lat_der_big_3;
	if ( $publi_lat_der_big_4 != '0' ) $publi_lat[] = $publi_lat_der_big_4;
	
	if ( $publi_lat != '' ) {	
		shuffle( $publi_lat );
		$id = $publi_lat[0];
		$mostrar = $wpdb->get_row( "SELECT * FROM `wp_stray_quotes` WHERE quoteID = '$id'" );
		if ( $mostrar->author == 'adsense' ) {
			echo '<div id="ads-sidebar-top">
		<p>Publicidad</p>';
			echo $mostrar->source;
			echo '<p>Publicidad</p>
</div>';
		} elseif ( $mostrar->author == 'flash' ) {
			echo '<a href="http://www.teatrocentral.es/" target="_blank"><div id="ads-sidebar-top"><p>Publicidad</p><object codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" width="245">

                                <param name="movie" value="';
			echo $mostrar->source;
			echo '">

                                <param name="quality" value="high">

                                <embed src="';
			echo $mostrar->source;                            
			echo '" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="245"> 

                              </object></div></a>';
		} else {
			echo '<div id="ads-sidebar-top">
		<p>Publicidad</p>';
						$add_publi = "add_publi('$id','$tipo_portada_publi','$prov_portada_publi','$mostrar->quote')";
			echo '<a rel="external" href="'.$mostrar->author.'" target="_blank" onclick="'.$add_publi.'"><img src="'.$mostrar->source.'" alt ="'.$mostrar->quote.'" /></a>';

			echo '<p>Publicidad</p>
</div>';
		} // adsense
	} else {
		$prov_portada = 'todas';
		$tipo_portada = 'global';
	
		$publi_lat_der_big_1 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_latderbig1' );
		$publi_lat_der_big_2 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_latderbig2' );
		$publi_lat_der_big_3 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_latderbig3' );
		$publi_lat_der_big_4 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_latderbig4' );

		$publi_lat = '';
		if ( $publi_lat_der_big_1 != '0' ) $publi_lat[] = $publi_lat_der_big_1;
		if ( $publi_lat_der_big_2 != '0' ) $publi_lat[] = $publi_lat_der_big_2;
		if ( $publi_lat_der_big_3 != '0' ) $publi_lat[] = $publi_lat_der_big_3;
		if ( $publi_lat_der_big_4 != '0' ) $publi_lat[] = $publi_lat_der_big_4;

		if ( $publi_lat != '' ) {	
			shuffle( $publi_lat );
			$id = $publi_lat[0];
			$mostrar = $wpdb->get_row( "SELECT * FROM `wp_stray_quotes` WHERE quoteID = '$id'" );
			if ( $mostrar->author == 'adsense' ) {
				echo '<div id="ads-sidebar-top">
			<p>Publicidad</p>';
				echo $mostrar->source;
				echo '<p>Publicidad</p>
</div>';
			} elseif ( $mostrar->author == 'flash' ) {
				echo '<a href="http://www.teatrocentral.es/" target="_blank"><div id="ads-sidebar-top"><p>Publicidad</p><object codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" width="245">
		
    	                            <param name="movie" value="';
				echo $mostrar->source;
				echo '">
		
    	                            <param name="quality" value="high">
		
    	                            <embed src="';
				echo $mostrar->source;                            
				echo '" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="245"> 
		
    	                          </object></div></a>';
			} else {
				echo '<div id="ads-sidebar-top">
			<p>Publicidad</p>';
							$add_publi = "add_publi('$id','$tipo_portada_publi','$prov_portada_publi','$mostrar->quote')";
			echo '<a rel="external" href="'.$mostrar->author.'" target="_blank" onclick="'.$add_publi.'"><img src="'.$mostrar->source.'" alt ="'.$mostrar->quote.'" /></a>';

				echo '<p>Publicidad</p>
</div>';
			} // adsense
		} // publicidad para el global
	
	}
	
}

function mostrar_lat_der_small1(){
	
	global $wpdb;
	
	$prov_portada = ( isset( $_COOKIE['noletia_prov'] ) ) ? $_COOKIE['noletia_prov'] : 'todas';
	if ( $prov_portada == 'A Coruña') $prov_portada = 'a-coruna';
	if ( $prov_portada == 'Santiago de Compostela') $prov_portada = 'santiago-de-compostela';
	if ( $prov_portada == 'Almería') $prov_portada = 'almeria';
	if ( $prov_portada == 'Cádiz') $prov_portada = 'cadiz';
	if ( $prov_portada == 'Chiclana de la Frontera') $prov_portada = 'chiclana-de-la-frontera';
	if ( $prov_portada == 'El Puerto de Santa María') $prov_portada = 'el-puerto-de-santa-maria';
	if ( $prov_portada == 'Jerez de la Frontera') $prov_portada = 'jerez-de-la-frontera';
	if ( $prov_portada == 'Puerto Real') $prov_portada = 'puerto-real';
	if ( $prov_portada == 'San Fernando') $prov_portada = 'san-fernando';
	if ( $prov_portada == 'Villaluenga del Rosario') $prov_portada = 'villaluenga-del-rosario';
	if ( $prov_portada == 'Ciudad Real') $prov_portada = 'ciudad-real';
	if ( $prov_portada == 'Córdoba') $prov_portada = 'cordoba';
	if ( $prov_portada == 'Granada') $prov_portada = 'granada';
	if ( $prov_portada == 'Huelva') $prov_portada = 'huelva';
	if ( $prov_portada == 'Jaén') $prov_portada = 'jaen';
	if ( $prov_portada == 'Madrid') $prov_portada = 'madrid';
	if ( $prov_portada == 'Málaga') $prov_portada = 'malaga';
	if ( $prov_portada == 'Nacional') $prov_portada = 'nacional';
	if ( $prov_portada == 'Ourense') $prov_portada = 'ourense';
	if ( $prov_portada == 'Pontevedra') $prov_portada = 'pontevedra';
	if ( $prov_portada == 'Vigo') $prov_portada = 'vigo-pontevedra';
	$tipo_portada = nombre_portada_de_seccion();
	$tipo_portada = ( ( $tipo_portada == '' ) ) ? 'global' : $tipo_portada; 
	if(is_single()){
		if(in_category('musica')){
		    $tipo_portada = 'musica';
		}elseif(in_category('artes-escenicas')){
		    $tipo_portada = 'artes-escenicas';
		}elseif(in_category('arte')){
		    $tipo_portada = 'arte';
		}elseif(in_category('literatura')){
		    $tipo_portada = 'literatura';
		}elseif(in_category('audiovisual')){
		    $tipo_portada = 'audiovisual';
		}elseif(in_category('formacion')){
		    $tipo_portada = 'formacion';
		}elseif(in_category('descuentos')){
		    $tipo_portada = 'descuentos';
		}elseif(in_category('concursos')){
		    $tipo_portada = 'concursos';
		}else{
		    
		    if(is_object_in_term( $post->ID, 'event-categories', 'musica')){
		  	  $tipo_portada = 'musica';
		    }elseif(is_object_in_term($post->ID, 'event-categories', 'artes-escenicas')){
		   	 $tipo_portada = 'artes-escenicas';
		    }elseif(is_object_in_term($post->ID, 'event-categories', 'arte')){
		    $tipo_portada = 'arte';
		    }elseif(is_object_in_term($post->ID, 'event-categories', 'literatura')){
		    $tipo_portada = 'literatura';
		    }elseif(is_object_in_term($post->ID, 'event-categories', 'audiovisual')){
		    $tipo_portada = 'audiovisual';
		    }elseif(is_object_in_term($post->ID, 'event-categories', 'formacion')){
		    $tipo_portada = 'formacion';
		    }elseif(is_object_in_term($post->ID, 'event-categories', 'descuentos')){
		    $tipo_portada = 'descuentos';
		    }elseif(is_object_in_term($post->ID, 'event-categories', 'concursos')){
		    $tipo_portada = 'concursos';
		    }else{
		    $tipo_portada = 'agenda';
		    }
		    
		}
					
	}

	$tipo_portada_publi = $tipo_portada;
	$prov_portada_publi = $prov_portada;

	$publi_lat_der_small1_1 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_latdersmall11' );
	$publi_lat_der_small1_2 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_latdersmall12' );
	$publi_lat_der_small1_3 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_latdersmall13' );
	$publi_lat_der_small1_4 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_latdersmall14' );
	
	$publi_lat = '';
	if ( $publi_lat_der_small1_1 != '0' ) $publi_lat[] = $publi_lat_der_small1_1;
	if ( $publi_lat_der_small1_2 != '0' ) $publi_lat[] = $publi_lat_der_small1_2;
	if ( $publi_lat_der_small1_3 != '0' ) $publi_lat[] = $publi_lat_der_small1_3;
	if ( $publi_lat_der_small1_4 != '0' ) $publi_lat[] = $publi_lat_der_small1_4;
	
	if ( $publi_lat != '' ) {	
		shuffle( $publi_lat );
		$id = $publi_lat[0];
		$mostrar = $wpdb->get_row( "SELECT * FROM `wp_stray_quotes` WHERE quoteID = '$id'" );
		if ( $mostrar->author == 'adsense' ) {
			echo '<div id="ads-sidebar-top">
		<p>Publicidad</p>';
			echo $mostrar->source;
				echo '</div>';
		} else {
			echo '<div id="ads-sidebar-top">
		<p>Publicidad</p>';
						$add_publi = "add_publi('$id','$tipo_portada_publi','$prov_portada_publi','$mostrar->quote')";
			echo '<a rel="external" href="'.$mostrar->author.'" target="_blank" onclick="'.$add_publi.'"><img src="'.$mostrar->source.'" alt ="'.$mostrar->quote.'" /></a>';

				echo '</div>';
		} // adsense
	} else {
		$prov_portada = 'todas';
		$tipo_portada = 'global';
	
		$publi_lat_der_small1_1 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_latdersmall11' );
		$publi_lat_der_small1_2 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_latdersmall12' );
		$publi_lat_der_small1_3 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_latdersmall13' );
		$publi_lat_der_small1_4 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_latdersmall14' );

		$publi_lat = '';
		if ( $publi_lat_der_small1_1 != '0' ) $publi_lat[] = $publi_lat_der_small1_1;
		if ( $publi_lat_der_small1_2 != '0' ) $publi_lat[] = $publi_lat_der_small1_2;
		if ( $publi_lat_der_small1_3 != '0' ) $publi_lat[] = $publi_lat_der_small1_3;
		if ( $publi_lat_der_small1_4 != '0' ) $publi_lat[] = $publi_lat_der_small1_4;

		if ( $publi_lat != '' ) {	
			shuffle( $publi_lat );
			$id = $publi_lat[0];
			$mostrar = $wpdb->get_row( "SELECT * FROM `wp_stray_quotes` WHERE quoteID = '$id'" );
			if ( $mostrar->author == 'adsense' ) {
				echo '<div id="ads-sidebar-top">
		<p>Publicidad</p>';
				echo $mostrar->source;
				echo '</div>';
			} else {
				echo '<div id="ads-sidebar-top">
		<p>Publicidad</p>';
							$add_publi = "add_publi('$id','$tipo_portada_publi','$prov_portada_publi','$mostrar->quote')";
			echo '<a rel="external" href="'.$mostrar->author.'" target="_blank" onclick="'.$add_publi.'"><img src="'.$mostrar->source.'" alt ="'.$mostrar->quote.'" /></a>';

				echo '</div>';
			} // adsense
		} // publicidad para el global
	
	}
	
}

function mostrar_lat_der_small2(){
	
	global $wpdb;
	
	$prov_portada = ( isset( $_COOKIE['noletia_prov'] ) ) ? $_COOKIE['noletia_prov'] : 'todas';
	if ( $prov_portada == 'A Coruña') $prov_portada = 'a-coruna';
	if ( $prov_portada == 'Santiago de Compostela') $prov_portada = 'santiago-de-compostela';
	if ( $prov_portada == 'Almería') $prov_portada = 'almeria';
	if ( $prov_portada == 'Cádiz') $prov_portada = 'cadiz';
	if ( $prov_portada == 'Chiclana de la Frontera') $prov_portada = 'chiclana-de-la-frontera';
	if ( $prov_portada == 'El Puerto de Santa María') $prov_portada = 'el-puerto-de-santa-maria';
	if ( $prov_portada == 'Jerez de la Frontera') $prov_portada = 'jerez-de-la-frontera';
	if ( $prov_portada == 'Puerto Real') $prov_portada = 'puerto-real';
	if ( $prov_portada == 'San Fernando') $prov_portada = 'san-fernando';
	if ( $prov_portada == 'Villaluenga del Rosario') $prov_portada = 'villaluenga-del-rosario';
	if ( $prov_portada == 'Ciudad Real') $prov_portada = 'ciudad-real';
	if ( $prov_portada == 'Córdoba') $prov_portada = 'cordoba';
	if ( $prov_portada == 'Granada') $prov_portada = 'granada';
	if ( $prov_portada == 'Huelva') $prov_portada = 'huelva';
	if ( $prov_portada == 'Jaén') $prov_portada = 'jaen';
	if ( $prov_portada == 'Madrid') $prov_portada = 'madrid';
	if ( $prov_portada == 'Málaga') $prov_portada = 'malaga';
	if ( $prov_portada == 'Nacional') $prov_portada = 'nacional';
	if ( $prov_portada == 'Ourense') $prov_portada = 'ourense';
	if ( $prov_portada == 'Pontevedra') $prov_portada = 'pontevedra';
	if ( $prov_portada == 'Vigo') $prov_portada = 'vigo-pontevedra';
	$tipo_portada = nombre_portada_de_seccion();
	$tipo_portada = ( ( $tipo_portada == '' ) ) ? 'global' : $tipo_portada; 
	if(is_single()){
		if(in_category('musica')){
		    $tipo_portada = 'musica';
		}elseif(in_category('artes-escenicas')){
		    $tipo_portada = 'artes-escenicas';
		}elseif(in_category('arte')){
		    $tipo_portada = 'arte';
		}elseif(in_category('literatura')){
		    $tipo_portada = 'literatura';
		}elseif(in_category('audiovisual')){
		    $tipo_portada = 'audiovisual';
		}elseif(in_category('formacion')){
		    $tipo_portada = 'formacion';
		}elseif(in_category('descuentos')){
		    $tipo_portada = 'descuentos';
		}elseif(in_category('concursos')){
		    $tipo_portada = 'concursos';
		}else{
		    
		    if(is_object_in_term( $post->ID, 'event-categories', 'musica')){
		  	  $tipo_portada = 'musica';
		    }elseif(is_object_in_term($post->ID, 'event-categories', 'artes-escenicas')){
		   	 $tipo_portada = 'artes-escenicas';
		    }elseif(is_object_in_term($post->ID, 'event-categories', 'arte')){
		    $tipo_portada = 'arte';
		    }elseif(is_object_in_term($post->ID, 'event-categories', 'literatura')){
		    $tipo_portada = 'literatura';
		    }elseif(is_object_in_term($post->ID, 'event-categories', 'audiovisual')){
		    $tipo_portada = 'audiovisual';
		    }elseif(is_object_in_term($post->ID, 'event-categories', 'formacion')){
		    $tipo_portada = 'formacion';
		    }elseif(is_object_in_term($post->ID, 'event-categories', 'descuentos')){
		    $tipo_portada = 'descuentos';
		    }elseif(is_object_in_term($post->ID, 'event-categories', 'concursos')){
		    $tipo_portada = 'concursos';
		    }else{
		    $tipo_portada = 'agenda';
		    }
		    
		}
					
	}

	$tipo_portada_publi = $tipo_portada;
	$prov_portada_publi = $prov_portada;

	$publi_lat_der_small2_1 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_latdersmall21' );
	$publi_lat_der_small2_2 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_latdersmall22' );
	$publi_lat_der_small2_3 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_latdersmall23' );
	$publi_lat_der_small2_4 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_latdersmall24' );
	
	$publi_lat = '';
	if ( $publi_lat_der_small2_1 != '0' ) $publi_lat[] = $publi_lat_der_small2_1;
	if ( $publi_lat_der_small2_2 != '0' ) $publi_lat[] = $publi_lat_der_small2_2;
	if ( $publi_lat_der_small2_3 != '0' ) $publi_lat[] = $publi_lat_der_small2_3;
	if ( $publi_lat_der_small2_4 != '0' ) $publi_lat[] = $publi_lat_der_small2_4;
	
	if ( $publi_lat != '' ) {	
		shuffle( $publi_lat );
		$id = $publi_lat[0];
		$mostrar = $wpdb->get_row( "SELECT * FROM `wp_stray_quotes` WHERE quoteID = '$id'" );
		if ( $mostrar->author == 'adsense' ) {
			echo '<div id="ads-sidebar-top">
		<p>Publicidad</p>';
			echo $mostrar->source;
				echo '</div>';
		} else {
			echo '<div id="ads-sidebar-top">
		<p>Publicidad</p>';
						$add_publi = "add_publi('$id','$tipo_portada_publi','$prov_portada_publi','$mostrar->quote')";
			echo '<a rel="external" href="'.$mostrar->author.'" target="_blank" onclick="'.$add_publi.'"><img src="'.$mostrar->source.'" alt ="'.$mostrar->quote.'" /></a>';

				echo '</div>';
		} // adsense
	} else {
		$prov_portada = 'todas';
		$tipo_portada = 'global';
	
		$publi_lat_der_small2_1 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_latdersmall21' );
		$publi_lat_der_small2_2 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_latdersmall22' );
		$publi_lat_der_small2_3 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_latdersmall23' );
		$publi_lat_der_small2_4 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_latdersmall24' );

		$publi_lat = '';
		if ( $publi_lat_der_small2_1 != '0' ) $publi_lat[] = $publi_lat_der_small2_1;
		if ( $publi_lat_der_small2_2 != '0' ) $publi_lat[] = $publi_lat_der_small2_2;
		if ( $publi_lat_der_small2_3 != '0' ) $publi_lat[] = $publi_lat_der_small2_3;
		if ( $publi_lat_der_small2_4 != '0' ) $publi_lat[] = $publi_lat_der_small2_4;

		if ( $publi_lat != '' ) {	
			shuffle( $publi_lat );
			$id = $publi_lat[0];
			$mostrar = $wpdb->get_row( "SELECT * FROM `wp_stray_quotes` WHERE quoteID = '$id'" );
			if ( $mostrar->author == 'adsense' ) {
				echo '<div id="ads-sidebar-top">
		<p>Publicidad</p>';
				echo $mostrar->source;
				echo '</div>';
			} else {
				echo '<div id="ads-sidebar-top">
		<p>Publicidad</p>';
							$add_publi = "add_publi('$id','$tipo_portada_publi','$prov_portada_publi','$mostrar->quote')";
			echo '<a rel="external" href="'.$mostrar->author.'" target="_blank" onclick="'.$add_publi.'"><img src="'.$mostrar->source.'" alt ="'.$mostrar->quote.'" /></a>';

				echo '</div>';
			} // adsense
		} // publicidad para el global
	
	}
	
}

function mostrar_lat_der_small3(){
	
	global $wpdb;
	
	$prov_portada = ( isset( $_COOKIE['noletia_prov'] ) ) ? $_COOKIE['noletia_prov'] : 'todas';
	if ( $prov_portada == 'A Coruña') $prov_portada = 'a-coruna';
	if ( $prov_portada == 'Santiago de Compostela') $prov_portada = 'santiago-de-compostela';
	if ( $prov_portada == 'Almería') $prov_portada = 'almeria';
	if ( $prov_portada == 'Cádiz') $prov_portada = 'cadiz';
	if ( $prov_portada == 'Chiclana de la Frontera') $prov_portada = 'chiclana-de-la-frontera';
	if ( $prov_portada == 'El Puerto de Santa María') $prov_portada = 'el-puerto-de-santa-maria';
	if ( $prov_portada == 'Jerez de la Frontera') $prov_portada = 'jerez-de-la-frontera';
	if ( $prov_portada == 'Puerto Real') $prov_portada = 'puerto-real';
	if ( $prov_portada == 'San Fernando') $prov_portada = 'san-fernando';
	if ( $prov_portada == 'Villaluenga del Rosario') $prov_portada = 'villaluenga-del-rosario';
	if ( $prov_portada == 'Ciudad Real') $prov_portada = 'ciudad-real';
	if ( $prov_portada == 'Córdoba') $prov_portada = 'cordoba';
	if ( $prov_portada == 'Granada') $prov_portada = 'granada';
	if ( $prov_portada == 'Huelva') $prov_portada = 'huelva';
	if ( $prov_portada == 'Jaén') $prov_portada = 'jaen';
	if ( $prov_portada == 'Madrid') $prov_portada = 'madrid';
	if ( $prov_portada == 'Málaga') $prov_portada = 'malaga';
	if ( $prov_portada == 'Nacional') $prov_portada = 'nacional';
	if ( $prov_portada == 'Ourense') $prov_portada = 'ourense';
	if ( $prov_portada == 'Pontevedra') $prov_portada = 'pontevedra';
	if ( $prov_portada == 'Vigo') $prov_portada = 'vigo-pontevedra';
	$tipo_portada = nombre_portada_de_seccion();
	$tipo_portada = ( ( $tipo_portada == '' ) ) ? 'global' : $tipo_portada; 
	if(is_single()){
		if(in_category('musica')){
		    $tipo_portada = 'musica';
		}elseif(in_category('artes-escenicas')){
		    $tipo_portada = 'artes-escenicas';
		}elseif(in_category('arte')){
		    $tipo_portada = 'arte';
		}elseif(in_category('literatura')){
		    $tipo_portada = 'literatura';
		}elseif(in_category('audiovisual')){
		    $tipo_portada = 'audiovisual';
		}elseif(in_category('formacion')){
		    $tipo_portada = 'formacion';
		}elseif(in_category('descuentos')){
		    $tipo_portada = 'descuentos';
		}elseif(in_category('concursos')){
		    $tipo_portada = 'concursos';
		}else{
		    
		    if(is_object_in_term( $post->ID, 'event-categories', 'musica')){
		  	  $tipo_portada = 'musica';
		    }elseif(is_object_in_term($post->ID, 'event-categories', 'artes-escenicas')){
		   	 $tipo_portada = 'artes-escenicas';
		    }elseif(is_object_in_term($post->ID, 'event-categories', 'arte')){
		    $tipo_portada = 'arte';
		    }elseif(is_object_in_term($post->ID, 'event-categories', 'literatura')){
		    $tipo_portada = 'literatura';
		    }elseif(is_object_in_term($post->ID, 'event-categories', 'audiovisual')){
		    $tipo_portada = 'audiovisual';
		    }elseif(is_object_in_term($post->ID, 'event-categories', 'formacion')){
		    $tipo_portada = 'formacion';
		    }elseif(is_object_in_term($post->ID, 'event-categories', 'descuentos')){
		    $tipo_portada = 'descuentos';
		    }elseif(is_object_in_term($post->ID, 'event-categories', 'concursos')){
		    $tipo_portada = 'concursos';
		    }else{
		    $tipo_portada = 'agenda';
		    }
		    
		}
					
	}

	$tipo_portada_publi = $tipo_portada;
	$prov_portada_publi = $prov_portada;

	$publi_lat_der_small3_1 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_latdersmall31' );
	$publi_lat_der_small3_2 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_latdersmall32' );
	$publi_lat_der_small3_3 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_latdersmall33' );
	$publi_lat_der_small3_4 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_latdersmall34' );
	
	$publi_lat = '';
	if ( $publi_lat_der_small3_1 != '0' ) $publi_lat[] = $publi_lat_der_small3_1;
	if ( $publi_lat_der_small3_2 != '0' ) $publi_lat[] = $publi_lat_der_small3_2;
	if ( $publi_lat_der_small3_3 != '0' ) $publi_lat[] = $publi_lat_der_small3_3;
	if ( $publi_lat_der_small3_4 != '0' ) $publi_lat[] = $publi_lat_der_small3_4;
	
	if ( $publi_lat != '' ) {	
		shuffle( $publi_lat );
		$id = $publi_lat[0];
		$mostrar = $wpdb->get_row( "SELECT * FROM `wp_stray_quotes` WHERE quoteID = '$id'" );
		if ( $mostrar->author == 'adsense' ) {
			echo '<div id="ads-sidebar-top">
		<p>Publicidad</p>';
			echo $mostrar->source;
				echo '</div>';
		} else {
			echo '<div id="ads-sidebar-top">
		<p>Publicidad</p>';
						$add_publi = "add_publi('$id','$tipo_portada_publi','$prov_portada_publi','$mostrar->quote')";
			echo '<a rel="external" href="'.$mostrar->author.'" target="_blank" onclick="'.$add_publi.'"><img src="'.$mostrar->source.'" alt ="'.$mostrar->quote.'" /></a>';

				echo '</div>';
		} // adsense
	} else {
		$prov_portada = 'todas';
		$tipo_portada = 'global';
	
		$publi_lat_der_small3_1 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_latdersmall31' );
		$publi_lat_der_small3_2 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_latdersmall32' );
		$publi_lat_der_small3_3 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_latdersmall33' );
		$publi_lat_der_small3_4 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_latdersmall34' );

		$publi_lat = '';
		if ( $publi_lat_der_small3_1 != '0' ) $publi_lat[] = $publi_lat_der_small3_1;
		if ( $publi_lat_der_small3_2 != '0' ) $publi_lat[] = $publi_lat_der_small3_2;
		if ( $publi_lat_der_small3_3 != '0' ) $publi_lat[] = $publi_lat_der_small3_3;
		if ( $publi_lat_der_small3_4 != '0' ) $publi_lat[] = $publi_lat_der_small3_4;

		if ( $publi_lat != '' ) {	
			shuffle( $publi_lat );
			$id = $publi_lat[0];
			$mostrar = $wpdb->get_row( "SELECT * FROM `wp_stray_quotes` WHERE quoteID = '$id'" );
			if ( $mostrar->author == 'adsense' ) {
				echo '<div id="ads-sidebar-top">
		<p>Publicidad</p>';
				echo $mostrar->source;
				echo '</div>';
			} else {
				echo '<div id="ads-sidebar-top">
		<p>Publicidad</p>';
							$add_publi = "add_publi('$id','$tipo_portada_publi','$prov_portada_publi','$mostrar->quote')";
			echo '<a rel="external" href="'.$mostrar->author.'" target="_blank" onclick="'.$add_publi.'"><img src="'.$mostrar->source.'" alt ="'.$mostrar->quote.'" /></a>';

				echo '</div>';
			} // adsense
		} // publicidad para el global
	
	}
	
}

function mostrar_facebook(){
	
	global $wpdb;
	
	$prov_portada = ( isset( $_COOKIE['noletia_prov'] ) ) ? $_COOKIE['noletia_prov'] : 'todas';
	if ( $prov_portada == 'A Coruña') $prov_portada = 'a-coruna';
	if ( $prov_portada == 'Santiago de Compostela') $prov_portada = 'santiago-de-compostela';
	if ( $prov_portada == 'Almería') $prov_portada = 'almeria';
	if ( $prov_portada == 'Cádiz') $prov_portada = 'cadiz';
	if ( $prov_portada == 'Chiclana de la Frontera') $prov_portada = 'chiclana-de-la-frontera';
	if ( $prov_portada == 'El Puerto de Santa María') $prov_portada = 'el-puerto-de-santa-maria';
	if ( $prov_portada == 'Jerez de la Frontera') $prov_portada = 'jerez-de-la-frontera';
	if ( $prov_portada == 'Puerto Real') $prov_portada = 'puerto-real';
	if ( $prov_portada == 'San Fernando') $prov_portada = 'san-fernando';
	if ( $prov_portada == 'Villaluenga del Rosario') $prov_portada = 'villaluenga-del-rosario';
	if ( $prov_portada == 'Ciudad Real') $prov_portada = 'ciudad-real';
	if ( $prov_portada == 'Córdoba') $prov_portada = 'cordoba';
	if ( $prov_portada == 'Granada') $prov_portada = 'granada';
	if ( $prov_portada == 'Huelva') $prov_portada = 'huelva';
	if ( $prov_portada == 'Jaén') $prov_portada = 'jaen';
	if ( $prov_portada == 'Madrid') $prov_portada = 'madrid';
	if ( $prov_portada == 'Málaga') $prov_portada = 'malaga';
	if ( $prov_portada == 'Nacional') $prov_portada = 'nacional';
	if ( $prov_portada == 'Ourense') $prov_portada = 'ourense';
	if ( $prov_portada == 'Pontevedra') $prov_portada = 'pontevedra';
	if ( $prov_portada == 'Vigo') $prov_portada = 'vigo-pontevedra';
	$tipo_portada = nombre_portada_de_seccion();
	$tipo_portada = ( ( $tipo_portada == '' ) ) ? 'global' : $tipo_portada; 
	if(is_single()){
		if(in_category('musica')){
		    $tipo_portada = 'musica';
		}elseif(in_category('artes-escenicas')){
		    $tipo_portada = 'artes-escenicas';
		}elseif(in_category('arte')){
		    $tipo_portada = 'arte';
		}elseif(in_category('literatura')){
		    $tipo_portada = 'literatura';
		}elseif(in_category('audiovisual')){
		    $tipo_portada = 'audiovisual';
		}elseif(in_category('formacion')){
		    $tipo_portada = 'formacion';
		}elseif(in_category('descuentos')){
		    $tipo_portada = 'descuentos';
		}elseif(in_category('concursos')){
		    $tipo_portada = 'concursos';
		}else{
		    
		    if(is_object_in_term( $post->ID, 'event-categories', 'musica')){
		  	  $tipo_portada = 'musica';
		    }elseif(is_object_in_term($post->ID, 'event-categories', 'artes-escenicas')){
		   	 $tipo_portada = 'artes-escenicas';
		    }elseif(is_object_in_term($post->ID, 'event-categories', 'arte')){
		    $tipo_portada = 'arte';
		    }elseif(is_object_in_term($post->ID, 'event-categories', 'literatura')){
		    $tipo_portada = 'literatura';
		    }elseif(is_object_in_term($post->ID, 'event-categories', 'audiovisual')){
		    $tipo_portada = 'audiovisual';
		    }elseif(is_object_in_term($post->ID, 'event-categories', 'formacion')){
		    $tipo_portada = 'formacion';
		    }elseif(is_object_in_term($post->ID, 'event-categories', 'descuentos')){
		    $tipo_portada = 'descuentos';
		    }elseif(is_object_in_term($post->ID, 'event-categories', 'concursos')){
		    $tipo_portada = 'concursos';
		    }else{
		    $tipo_portada = 'agenda';
		    }
		    
		}
					
	}

	$tipo_portada_publi = $tipo_portada;
	$prov_portada_publi = $prov_portada;
	
	$publi_facebook_1 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_facebook1' );
	$publi_facebook_2 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_facebook2' );
	$publi_facebook_3 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_facebook3' );
	$publi_facebook_4 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_facebook4' );
	
	$publi_lat = '';
	if ( $publi_facebook_1 != '0' ) $publi_lat[] = $publi_facebook_1;
	if ( $publi_facebook_2 != '0' ) $publi_lat[] = $publi_facebook_2;
	if ( $publi_facebook_3 != '0' ) $publi_lat[] = $publi_facebook_3;
	if ( $publi_facebook_4 != '0' ) $publi_lat[] = $publi_facebook_4;
	
	if ( $publi_lat != '' ) {	
		shuffle( $publi_lat );
		$id = $publi_lat[0];
		$mostrar = $wpdb->get_row( "SELECT * FROM `wp_stray_quotes` WHERE quoteID = '$id'" );
		if ( $mostrar->author == 'facebook' ) {
				echo '<div style="background-color:#fff;">';
		echo '<iframe src="//www.facebook.com/plugins/likebox.php?href=';
		echo $mostrar->source;
		echo '&amp;width=256&amp;height=590&amp;colorscheme=light&amp;show_faces=true&amp;border_color&amp;stream=true&amp;header=true&amp;appId=290994574288616" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:256px; height:590px;" allowTransparency="true"></iframe>';
		echo '</div>';
		} else {
			echo '<div id="ads-sidebar-top">
		<p>Publicidad</p>';
						$add_publi = "add_publi('$id','$tipo_portada_publi','$prov_portada_publi','$mostrar->quote')";
			echo '<a rel="external" href="'.$mostrar->author.'" target="_blank" onclick="'.$add_publi.'"><img src="'.$mostrar->source.'" alt ="'.$mostrar->quote.'" /></a>';

				echo '</div>';
		} // facebook
	} else {
		$prov_portada = 'todas';
		$tipo_portada = 'global';
	
		$publi_facebook_1 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_facebook1' );
		$publi_facebook_2 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_facebook2' );
		$publi_facebook_3 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_facebook3' );
		$publi_facebook_4 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_facebook4' );

		$publi_lat = '';
		if ( $publi_facebook_1 != '0' ) $publi_lat[] = $publi_facebook_1;
		if ( $publi_facebook_2 != '0' ) $publi_lat[] = $publi_facebook_2;
		if ( $publi_facebook_3 != '0' ) $publi_lat[] = $publi_facebook_3;
		if ( $publi_facebook_4 != '0' ) $publi_lat[] = $publi_facebook_4;

		if ( $publi_lat != '' ) {	
			shuffle( $publi_lat );
			$id = $publi_lat[0];
			$mostrar = $wpdb->get_row( "SELECT * FROM `wp_stray_quotes` WHERE quoteID = '$id'" );
			if ( $mostrar->author == 'facebook' ) {
				echo '<div style="background-color:#fff;">';
			echo '<iframe src="//www.facebook.com/plugins/likebox.php?href=';
			echo $mostrar->source;
			echo '&amp;width=256&amp;height=590&amp;colorscheme=light&amp;show_faces=true&amp;border_color&amp;stream=true&amp;header=true&amp;appId=290994574288616" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:256px; height:590px;" allowTransparency="true"></iframe>';
			echo '</div>';
			} else {
				echo '<div id="ads-sidebar-top">
			<p>Publicidad</p>';
							$add_publi = "add_publi('$id','$tipo_portada_publi','$prov_portada_publi','$mostrar->quote')";
			echo '<a rel="external" href="'.$mostrar->author.'" target="_blank" onclick="'.$add_publi.'"><img src="'.$mostrar->source.'" alt ="'.$mostrar->quote.'" /></a>';

					echo '</div>';
			} // facebook
		} // publicidad para el global
	
	}
	
}

function mostrar_horiz_port(){
	
	global $wpdb;
	
	$prov_portada = ( isset( $_COOKIE['noletia_prov'] ) ) ? $_COOKIE['noletia_prov'] : 'todas';
	if ( $prov_portada == 'A Coruña') $prov_portada = 'a-coruna';
	if ( $prov_portada == 'Santiago de Compostela') $prov_portada = 'santiago-de-compostela';
	if ( $prov_portada == 'Almería') $prov_portada = 'almeria';
	if ( $prov_portada == 'Cádiz') $prov_portada = 'cadiz';
	if ( $prov_portada == 'Chiclana de la Frontera') $prov_portada = 'chiclana-de-la-frontera';
	if ( $prov_portada == 'El Puerto de Santa María') $prov_portada = 'el-puerto-de-santa-maria';
	if ( $prov_portada == 'Jerez de la Frontera') $prov_portada = 'jerez-de-la-frontera';
	if ( $prov_portada == 'Puerto Real') $prov_portada = 'puerto-real';
	if ( $prov_portada == 'San Fernando') $prov_portada = 'san-fernando';
	if ( $prov_portada == 'Villaluenga del Rosario') $prov_portada = 'villaluenga-del-rosario';
	if ( $prov_portada == 'Ciudad Real') $prov_portada = 'ciudad-real';
	if ( $prov_portada == 'Córdoba') $prov_portada = 'cordoba';
	if ( $prov_portada == 'Granada') $prov_portada = 'granada';
	if ( $prov_portada == 'Huelva') $prov_portada = 'huelva';
	if ( $prov_portada == 'Jaén') $prov_portada = 'jaen';
	if ( $prov_portada == 'Madrid') $prov_portada = 'madrid';
	if ( $prov_portada == 'Málaga') $prov_portada = 'malaga';
	if ( $prov_portada == 'Nacional') $prov_portada = 'nacional';
	if ( $prov_portada == 'Ourense') $prov_portada = 'ourense';
	if ( $prov_portada == 'Pontevedra') $prov_portada = 'pontevedra';
	if ( $prov_portada == 'Vigo') $prov_portada = 'vigo-pontevedra';
	$tipo_portada = nombre_portada_de_seccion();
	$tipo_portada = ( ( $tipo_portada == '' ) ) ? 'global' : $tipo_portada; 

	$tipo_portada_publi = $tipo_portada;
	$prov_portada_publi = $prov_portada;

	$publi_horiz_port_1 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_horizport1' );
	$publi_horiz_port_2 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_horizport2' );
	$publi_horiz_port_3 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_horizport3' );
	$publi_horiz_port_4 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_horizport4' );
	
	$publi_lat = '';
	if ( $publi_horiz_port_1 != '0' ) $publi_lat[] = $publi_horiz_port_1;
	if ( $publi_horiz_port_2 != '0' ) $publi_lat[] = $publi_horiz_port_2;
	if ( $publi_horiz_port_3 != '0' ) $publi_lat[] = $publi_horiz_port_3;
	if ( $publi_horiz_port_4 != '0' ) $publi_lat[] = $publi_horiz_port_4;
	
	if ( $publi_lat != '' ) {	
		shuffle( $publi_lat );
		$id = $publi_lat[0];
		$mostrar = $wpdb->get_row( "SELECT * FROM `wp_stray_quotes` WHERE quoteID = '$id'" );
		if ( $mostrar->author == 'adsense' ) {
			echo '<div class="banner-home"><p>Publicidad</p>';
			echo $mostrar->source;
				echo '</div>';
		} else {
			echo '<div class="banner-home"><p>Publicidad</p>';
						$add_publi = "add_publi('$id','$tipo_portada_publi','$prov_portada_publi','$mostrar->quote')";
			echo '<a rel="external" href="'.$mostrar->author.'" target="_blank" onclick="'.$add_publi.'"><img src="'.$mostrar->source.'" alt ="'.$mostrar->quote.'" /></a>';

				echo '</div>';
		} // adsense
	} else {
		$prov_portada = 'todas';
		$tipo_portada = 'global';
	
		$publi_horiz_port_1 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_horizport1' );
		$publi_horiz_port_2 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_horizport2' );
		$publi_horiz_port_3 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_horizport3' );
		$publi_horiz_port_4 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_horizport4' );

		$publi_lat = '';
		if ( $publi_horiz_port_1 != '0' ) $publi_lat[] = $publi_horiz_port_1;
		if ( $publi_horiz_port_2 != '0' ) $publi_lat[] = $publi_horiz_port_2;
		if ( $publi_horiz_port_3 != '0' ) $publi_lat[] = $publi_horiz_port_3;
		if ( $publi_horiz_port_4 != '0' ) $publi_lat[] = $publi_horiz_port_4;

		if ( $publi_lat != '' ) {	
			shuffle( $publi_lat );
			$id = $publi_lat[0];
			$mostrar = $wpdb->get_row( "SELECT * FROM `wp_stray_quotes` WHERE quoteID = '$id'" );
			if ( $mostrar->author == 'adsense' ) {
				echo '<div class="banner-home"><p>Publicidad</p>';
				echo $mostrar->source;
				echo '</div>';
			} else {
				echo '<div class="banner-home"><p>Publicidad</p>';
							$add_publi = "add_publi('$id','$tipo_portada_publi','$prov_portada_publi','$mostrar->quote')";
			echo '<a rel="external" href="'.$mostrar->author.'" target="_blank" onclick="'.$add_publi.'"><img src="'.$mostrar->source.'" alt ="'.$mostrar->quote.'" /></a>';

				echo '</div>';
			} // adsense
		} // publicidad para el global
	
	}
	
}

function mostrar_publi_pie(){
	
	global $wpdb;
	
	$prov_portada = ( isset( $_COOKIE['noletia_prov'] ) ) ? $_COOKIE['noletia_prov'] : 'todas';
	if ( $prov_portada == 'A Coruña') $prov_portada = 'a-coruna';
	if ( $prov_portada == 'Santiago de Compostela') $prov_portada = 'santiago-de-compostela';
	if ( $prov_portada == 'Almería') $prov_portada = 'almeria';
	if ( $prov_portada == 'Cádiz') $prov_portada = 'cadiz';
	if ( $prov_portada == 'Chiclana de la Frontera') $prov_portada = 'chiclana-de-la-frontera';
	if ( $prov_portada == 'El Puerto de Santa María') $prov_portada = 'el-puerto-de-santa-maria';
	if ( $prov_portada == 'Jerez de la Frontera') $prov_portada = 'jerez-de-la-frontera';
	if ( $prov_portada == 'Puerto Real') $prov_portada = 'puerto-real';
	if ( $prov_portada == 'San Fernando') $prov_portada = 'san-fernando';
	if ( $prov_portada == 'Villaluenga del Rosario') $prov_portada = 'villaluenga-del-rosario';
	if ( $prov_portada == 'Ciudad Real') $prov_portada = 'ciudad-real';
	if ( $prov_portada == 'Córdoba') $prov_portada = 'cordoba';
	if ( $prov_portada == 'Granada') $prov_portada = 'granada';
	if ( $prov_portada == 'Huelva') $prov_portada = 'huelva';
	if ( $prov_portada == 'Jaén') $prov_portada = 'jaen';
	if ( $prov_portada == 'Madrid') $prov_portada = 'madrid';
	if ( $prov_portada == 'Málaga') $prov_portada = 'malaga';
	if ( $prov_portada == 'Nacional') $prov_portada = 'nacional';
	if ( $prov_portada == 'Ourense') $prov_portada = 'ourense';
	if ( $prov_portada == 'Pontevedra') $prov_portada = 'pontevedra';
	if ( $prov_portada == 'Vigo') $prov_portada = 'vigo-pontevedra';
	$tipo_portada = nombre_portada_de_seccion();
	$tipo_portada = ( ( $tipo_portada == '' ) ) ? 'global' : $tipo_portada; 
	if(is_single()){
		if(in_category('musica')){
		    $tipo_portada = 'musica';
		}elseif(in_category('artes-escenicas')){
		    $tipo_portada = 'artes-escenicas';
		}elseif(in_category('arte')){
		    $tipo_portada = 'arte';
		}elseif(in_category('literatura')){
		    $tipo_portada = 'literatura';
		}elseif(in_category('audiovisual')){
		    $tipo_portada = 'audiovisual';
		}elseif(in_category('formacion')){
		    $tipo_portada = 'formacion';
		}elseif(in_category('descuentos')){
		    $tipo_portada = 'descuentos';
		}elseif(in_category('concursos')){
		    $tipo_portada = 'concursos';
		}else{
		    
		    if(is_object_in_term( $post->ID, 'event-categories', 'musica')){
		  	  $tipo_portada = 'musica';
		    }elseif(is_object_in_term($post->ID, 'event-categories', 'artes-escenicas')){
		   	 $tipo_portada = 'artes-escenicas';
		    }elseif(is_object_in_term($post->ID, 'event-categories', 'arte')){
		    $tipo_portada = 'arte';
		    }elseif(is_object_in_term($post->ID, 'event-categories', 'literatura')){
		    $tipo_portada = 'literatura';
		    }elseif(is_object_in_term($post->ID, 'event-categories', 'audiovisual')){
		    $tipo_portada = 'audiovisual';
		    }elseif(is_object_in_term($post->ID, 'event-categories', 'formacion')){
		    $tipo_portada = 'formacion';
		    }elseif(is_object_in_term($post->ID, 'event-categories', 'descuentos')){
		    $tipo_portada = 'descuentos';
		    }elseif(is_object_in_term($post->ID, 'event-categories', 'concursos')){
		    $tipo_portada = 'concursos';
		    }else{
		    $tipo_portada = 'agenda';
		    }
		    
		}
					
	}

	$tipo_portada_publi = $tipo_portada;
	$prov_portada_publi = $prov_portada;

	$publi_publi_pie_1 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_pie1' );
	$publi_publi_pie_2 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_pie2' );
	$publi_publi_pie_3 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_pie3' );
	$publi_publi_pie_4 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_pie4' );
	
	$publi_lat = '';
	if ( $publi_publi_pie_1 != '0' ) $publi_lat[] = $publi_publi_pie_1;
	if ( $publi_publi_pie_2 != '0' ) $publi_lat[] = $publi_publi_pie_2;
	if ( $publi_publi_pie_3 != '0' ) $publi_lat[] = $publi_publi_pie_3;
	if ( $publi_publi_pie_4 != '0' ) $publi_lat[] = $publi_publi_pie_4;
	
	if ( $publi_lat != '' ) {	
		shuffle( $publi_lat );
		$id = $publi_lat[0];
		$mostrar = $wpdb->get_row( "SELECT * FROM `wp_stray_quotes` WHERE quoteID = '$id'" );
		if ( $mostrar->author == 'adsense' ) {
			echo '<div class="banner-bottom"><p>Publicidad</p>';
			echo $mostrar->source;
				echo '</div>';
		} elseif ( $mostrar->author == 'flash' ) {
			echo '<div class="banner-bottom"><p>Publicidad</p><object codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" height="130" width="968">

                                <param name="movie" value="';
			echo $mostrar->source;
			echo '">

                                <param name="quality" value="high">

                                <embed src="';
			echo $mostrar->source;                            
			echo '" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="968" height="130"> 

                              </object></div>';
		} else {
			echo '<div class="banner-bottom"><p>Publicidad</p>';
						$add_publi = "add_publi('$id','$tipo_portada_publi','$prov_portada_publi','$mostrar->quote')";
			echo '<a rel="external" href="'.$mostrar->author.'" target="_blank" onclick="'.$add_publi.'"><img src="'.$mostrar->source.'" alt ="'.$mostrar->quote.'" /></a>';

				echo '</div>';
		} // adsense
	} else {
		$prov_portada = 'todas';
		$tipo_portada = 'global';
	
		$publi_publi_pie_1 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_pie1' );
		$publi_publi_pie_2 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_pie2' );
		$publi_publi_pie_3 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_pie3' );
		$publi_publi_pie_4 = get_option( 'publicidad_'.$tipo_portada.'_'.$prov_portada.'_pie4' );

		$publi_lat = '';
		if ( $publi_publi_pie_1 != '0' ) $publi_lat[] = $publi_publi_pie_1;
		if ( $publi_publi_pie_2 != '0' ) $publi_lat[] = $publi_publi_pie_2;
		if ( $publi_publi_pie_3 != '0' ) $publi_lat[] = $publi_publi_pie_3;
		if ( $publi_publi_pie_4 != '0' ) $publi_lat[] = $publi_publi_pie_4;

		if ( $publi_lat != '' ) {	
			shuffle( $publi_lat );
			$id = $publi_lat[0];
			$mostrar = $wpdb->get_row( "SELECT * FROM `wp_stray_quotes` WHERE quoteID = '$id'" );
			if ( $mostrar->author == 'adsense' ) {
				echo '<div class="banner-bottom"><p>Publicidad</p>';
				echo $mostrar->source;
				echo '</div>';
		} elseif ( $mostrar->author == 'flash' ) {
			echo '<div class="banner-bottom"><p>Publicidad</p><object codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" height="130" width="968">

                                <param name="movie" value="';
			echo $mostrar->source;
			echo '">

                                <param name="quality" value="high">

                                <embed src="';
			echo $mostrar->source;                            
			echo '" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="968" height="130"> 

                              </object></div>';
			} else {
				echo '<div class="banner-bottom"><p>Publicidad</p>';
							$add_publi = "add_publi('$id','$tipo_portada_publi','$prov_portada_publi','$mostrar->quote')";
			echo '<a rel="external" href="'.$mostrar->author.'" target="_blank" onclick="'.$add_publi.'"><img src="'.$mostrar->source.'" alt ="'.$mostrar->quote.'" /></a>';

				echo '</div>';
			} // adsense
		} // publicidad para el global
	
	}
	
}


?>