<?php

/**
 * Scripts
 *
 * @package     MASHFS
 * @subpackage  Functions
 * @copyright   @todo
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) )
    exit;

/**
 * Load Scripts
 *
 * Enqueues the required scripts on your websites frontend.
 *
 * @since 1.0.0
 * @global $mashfs_options
 * @global $post
 * @return void
 */
function mashfs_load_scripts( $hook ) {
    if( !apply_filters( 'mashfs_load_scripts', mashfs_is_active(), $hook ) ) {
        return;
    }


    global $mashsb_options, $post;

    $js_dir = MASHFS_PLUGIN_URL . 'assets/js/';
    $js_title = 'mashfs';
    
    $mobile_width = isset($mashsb_options['mashfs_disable_mobile']) ? $mashsb_options['mashfs_disable_mobile'] : '';

    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
    // Check if js should be loaded in footer
    isset( $mashfs_options['load_scripts_footer'] ) ? $in_footer = true : $in_footer = false;
    wp_enqueue_script( 'mashfs', $js_dir . $js_title . $suffix . '.js', array('jquery'), MASHFS_VERSION, $in_footer );
    wp_localize_script( 'mashfs', 'mashfs', array(
        'mobile_width' => $mobile_width
    ) );
}

add_action( 'wp_enqueue_scripts', 'mashfs_load_scripts' );

/**
 * Register Styles
 *
 * Checks the styles option and hooks the required filter.
 *
 * @since 1.0.0
 * @global $mashfs_options
 * @return void
 */
function mashfs_register_styles( $hook ) {
    if( !apply_filters( 'mashfs_load_scripts', mashfs_is_active(), $hook ) ) {
        return;
    }
    global $mashsb_options;

    $css_dir = MASHFS_PLUGIN_URL . 'assets/css/';
    $css_title = 'mashfs';

    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

    wp_enqueue_style( 'mashfs', $css_dir . $css_title . $suffix . '.css', array(), MASHFS_VERSION );
}

add_action( 'wp_enqueue_scripts', 'mashfs_register_styles' );

/**
 * Load Admin Scripts
 *
 * Enqueues the required admin scripts.
 *
 * @since 1.0.0
 * @global $post
 * @param string $hook Page hook
 * @return void
 */
function mashfs_load_admin_scripts( $hook ) {
    if( function_exists( 'mashsb_is_admin_page' ) ) {
        if( !apply_filters( 'mashfs_load_admin_scripts', mashsb_is_admin_page(), $hook ) ) {
            return;
        }
    }

    $js_dir = MASHFS_PLUGIN_URL . 'assets/js/';
    $css_dir = MASHFS_PLUGIN_URL . 'assets/css/';

    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
    // Check if js should be loaded in footer
    isset( $mashfs_options['load_scripts_footer'] ) ? $in_footer = true : $in_footer = false;
    wp_enqueue_script( 'jquery-ui-core', array('jquery') );
    wp_enqueue_script( 'jquery-ui-sortable', array('jquery', 'jquery-ui-core') );
    wp_enqueue_script( 'mashfs-admin', $js_dir . 'mashfs-admin' . $suffix . '.js', array('jquery'), MASHFS_VERSION, $in_footer );
}

add_action( 'admin_enqueue_scripts', 'mashfs_load_admin_scripts', 100 );
