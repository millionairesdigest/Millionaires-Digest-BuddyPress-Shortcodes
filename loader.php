<?php
/*
Plugin Name: Millionaire's Digest BuddyPress Shortcodes
Description: Add the ability to use shortcodes to display and show BuddyPress profile information.
Version: 1.0.0
Author: K&L (Founder of the Millionaire's Digest)
Author URI: https://millionairedigest.com/
*/

if ( !defined( 'ABSPATH' ) ) exit;

function bppsc_plugin_init() {
    require( dirname( __FILE__ ) . '/inc/bppsc-shortcodes-extra.php');
    $obj = new bppsc_shortcodes();
    require( dirname( __FILE__ ) . '/inc/bppsc-shortcode-button.php');
}
add_action( 'bp_include', 'bppsc_plugin_init' );

define( 'BPPSC_PLUGIN_DIR', dirname( __FILE__ ). '/' );
define	( 'BPPSC_PLUGIN_URL', plugins_url( '', __FILE__ ) );

// Fix for wp quotes texturize
function bppsc_remove_smart_quotes($content) {

	if(strpos($content,"[bpps_test") !== FALSE ) {
		$content= str_replace(
		array("\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\x93", "\xe2\x80\x94", "\xe2\x80\xa6"),
		array("'", "'", '"', '"', '-', '--', '...'), $content);

		$content= str_replace(
		array(chr(145), chr(146), chr(147), chr(148), chr(150), chr(151), chr(133)),
		array("'", "'", '"', '"', '-', '--', '...'), $content);
	}
	
return $content;



}

add_filter( 'the_content', 'bppsc_remove_smart_quotes');

// Localization
function bppse_localization() {

load_plugin_textdomain('bp-Profile-Shortcodes-Extra', false, dirname(plugin_basename( __FILE__ ) ).'/langs/' );
}
 
add_action('init', 'bppse_localization');

function bppsc_check_buddypress(){
    if(!class_exists('BuddyPress')):
        add_action( 'admin_notices', 'bppsc_no_bp_admin_notice' );
    endif;
}
add_action( 'plugins_loaded', 'bppsc_check_buddypress' );

function bppsc_no_bp_admin_notice() {
    ?>
    <div class="error fade notice-error6 is-dismissible">
        <p><?php esc_textarea(_e( 'BuddyPress needs to be installed for BP Profile Shortcodes Extra to work.', 'bp-Profile-Shortcodes-Extra' ) ); ?></p>
    </div>
<?php
}
require_once __DIR__ . '/lib/class-wp-rest-shortcodes-controller-bpps.php';


add_action( 'rest_api_init', 'bpps_register_rest_routes' );

function bpps_register_rest_routes() {
	$controller = new WP_REST_Shortcodes_Controller_bpps();
	$controller->register_routes();
}


add_action( 'enqueue_block_editor_assets', 'bpps_sc_enqueue_block_editor_assets' );

function bpps_sc_enqueue_block_editor_assets() {
	wp_enqueue_script(
		'bpps-gutenberg-shortcode-block',
		plugins_url( '/block.build.js', __FILE__ ),
		array( 'wp-blocks', 'wp-i18n', 'wp-element' ),
		filemtime( plugin_dir_path( __FILE__ ) . 'block.build.js' )
	);

	wp_enqueue_style(
		'bpps-gutenberg-shortcode-block-css-editor',
		plugins_url( '/blocks/editor.css', __FILE__ ),
		array( 'wp-edit-blocks' ),
		filemtime( plugin_dir_path( __FILE__ ) . 'blocks/editor.css' )
	);
}
