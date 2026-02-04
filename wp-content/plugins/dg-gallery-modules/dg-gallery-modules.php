<?php
/*
Plugin Name: DiviGear Gallery
Plugin URI:  https://www.divigear.com/product/divi-gallery-modules/
Description: Divi Gallery for creating attractive Gallery design with most advanced features.
Version:     2.0.1
Author:      DiviGear
Author URI:  https://www.divigear.com/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: dggm-gallery-modules
Domain Path: /languages
Tested up to: 6.3
Requires at least: 5.6
Requires PHP: 7.1
Update URI: https://api.divigear.com/
*/

/**
 * define version and dir paths
 * 
 */
if(!defined('DGGM_VERSION')) {
	define('DGGM_VERSION','2.0.1');
}
if(!defined('DGGM_MAIN_DIR')) {
	define('DGGM_MAIN_DIR', __DIR__);
}
if(!defined('DGGM_MAIN_FILE_PATH')) {
	define('DGGM_MAIN_FILE_PATH', __FILE__);
}
if(!defined('DGGM_CORE_DIR_PATH')) {
	define('DGGM_CORE_DIR_PATH', plugin_dir_path( __FILE__ ) . 'core/');
}
if(!defined('DGGM_PUBLIC_DIR')) {
	define('DGGM_PUBLIC_DIR', trailingslashit(plugin_dir_url(__FILE__)) );
}
if(!defined('DGGM_ASSETS_DIR')) {
	define('DGGM_ASSETS_DIR', trailingslashit(plugin_dir_url(__FILE__)) . 'assets/');
}
if(!defined('DGGM_ASSETS_DIR_PATH')) {
	define('DGGM_ASSETS_DIR_PATH', plugin_dir_path( __FILE__ ) . 'assets/');
}

/**
 * license dashboard
 */
if(file_exists(DGGM_CORE_DIR_PATH . 'init.php')) {
	require_once (__DIR__ . '/core/init.php');
}


/**
 * Register front-end scripts and styles
 *
 * @since 1.0.0
 */
add_action('wp_enqueue_scripts', 'dggm_enqueue_scripts');
function dggm_enqueue_scripts() {
	// lib scripts

	wp_register_script( 'dggm-imagesloaded-lib', DGGM_ASSETS_DIR . 'js/lib/imagesloaded.pkgd.min.js', array(), DGGM_VERSION , true );
	wp_register_script( 'dggm-gallery-lib', DGGM_ASSETS_DIR . 'js/lib/gallerylib.js', array('jquery'), DGGM_VERSION , true );
	wp_register_script( 'dggm-lightgallery-lib', DGGM_ASSETS_DIR . 'js/lib/lightgallery.js', array('jquery'), DGGM_VERSION , true );
	wp_register_script( 'dggm-justified-gallery-lib', DGGM_ASSETS_DIR . 'js/lib/jquery.justifiedGallery.js', array('jquery'), DGGM_VERSION , true );
	wp_register_script( 'dggm-packery-gallery-lib', DGGM_ASSETS_DIR . 'js/lib/packery.pkgd.js', array('jquery'), DGGM_VERSION , true );
	wp_register_script( 'dggm-fitvids-lib', DGGM_ASSETS_DIR . 'js/lib/fitvids.js', array('jquery'), DGGM_VERSION , true );
  
	// custom scripts
	wp_register_script( 'dggm-packery-gallery', DGGM_ASSETS_DIR . 'js/dg-packery.js', array('jquery','dggm-lightgallery-lib' ), DGGM_VERSION , true );
	wp_register_script( 'dggm-image-gallery', DGGM_ASSETS_DIR . 'js/imageGallery.js', array('dggm-lightgallery-lib'), DGGM_VERSION , true );
	wp_register_script( 'dggm-justify-gallery', DGGM_ASSETS_DIR . 'js/justifyGallery.js', array('jquery', 'dggm-lightgallery-lib'), DGGM_VERSION , true );

	wp_register_style( 'dggm-builder-styles', DGGM_ASSETS_DIR . 'css/dg-builder.css', array(), DGGM_VERSION );
	wp_enqueue_style('dggm-builder-styles');

	wp_register_style( 'dggm-lib-styles', DGGM_ASSETS_DIR . 'css/lib/dg_library_style.css', array(), DGGM_VERSION );
	wp_enqueue_style('dggm-lib-styles');
}


/**
 * Register admin scripts and styles
 *
 * @since 1.0.0
 */
add_action('admin_enqueue_scripts', 'dggm_admin_enqueue_scripts');
function dggm_admin_enqueue_scripts() {

	$screen = get_current_screen();

	// custom styles
	wp_register_style( 'dggm-builder-styles', DGGM_ASSETS_DIR . 'css/dg-builder.css', array(), DGGM_VERSION );
	wp_enqueue_style('dggm-builder-styles');
}

if ( ! function_exists( '_initialize_extension_dg_gallery' ) ):
	/**
	 * Creates the extension's main class instance.
	 *
	 * @since 1.0.0
	 */
	function _initialize_extension_dg_gallery() {
		//require_once plugin_dir_path( __FILE__ ) . 'includes/AdvancedTabModule.php';
		require_once ( plugin_dir_path( __FILE__ ) . '/includes/functions.php' );
		require_once plugin_dir_path( __FILE__ ) . 'includes/DiviGalleryModules.php';
	}
	add_action( 'divi_extensions_init', '_initialize_extension_dg_gallery' );
endif;


// Create New Admin Column
//add_filter( 'manage_et_pb_layout_posts_columns', 'dgat_shortcode_create_shortcode_column', 5 );
function dggm_shortcode_create_shortcode_column( $columns ) {
    $columns['dgat_shortcode_id'] = __( 'Shortcode', 'dg_avbanced_tab');
    return $columns;
}
// Display Shortcode
//add_action( 'manage_et_pb_layout_posts_custom_column', 'dggm_shortcode_content', 5, 2 );
function dggm_shortcode_content ( $column, $id ) {
    if( 'dgat_shortcode_id' == $column ) {
        $value = sprintf('[dgat_layout_shortcode id="%1$s"]', esc_attr( $id ));
        ?>
        <div class="dgat-shortcode-wrapper">
            <p class="dgat-shortcode-copy"><?php echo esc_html($value); ?></p>
            <p class="dgat-cpy-tooltip">Click to copy</p>
        </div>
        <?php
    }
}
// Function to show the module
function dggm_module_shortcode_callback($atts) {
	$atts = shortcode_atts(array('id' => ''), $atts);
	return do_shortcode('[et_pb_section global_module="'.  esc_attr($atts['id']).'"][/et_pb_section]');	
}
//add_shortcode('dgat_layout_shortcode', 'dggm_module_shortcode_callback');


/**
 * Filter through the selector and remove the wrapper if any
 * 
 * @param String $selector the css selector
 * @param String $function_name
 */
add_filter('et_pb_set_style_selector', 'dggm_remove_css_selector_wrapper', 99999, 2);
function dggm_remove_css_selector_wrapper($selector, $function_name) {
	//when Builder plugin not active
	if ( ! dggm_is_divi_builder_plugin_active() && strpos($selector, 'dggm_') ) {
        $selector = str_replace( '.et-db ', "", $selector );
        $selector = str_replace( '#et-boc ', "", $selector );
        $selector = str_replace( '.et-l ', "", $selector );
	}
	
    return $selector;
}

if ( ! function_exists( 'dggm_is_divi_builder_plugin_active' ) ):
	/**
	 * Returns boolean if the Divi Builder Plugin active in the current WordPress installation
	 *
	 * @return boolean
	 * @since 1.0.0
	 */
	function dggm_is_divi_builder_plugin_active() {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		return is_plugin_active( 'divi-builder/divi-builder.php' );
	}
endif;