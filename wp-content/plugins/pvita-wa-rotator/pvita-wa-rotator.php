<?php
/*

Plugin Name: Pvita WA Rotator
Plugin URI: http://babaturan.net
Description: plugin rotator untuk non pvita
Author: http://babaturan.net
Version: 1.2
Author URI: http://babaturan.net

*/

if(get_option('pvita_wa_rotator_status') === 'valid'){
	include( plugin_dir_path( __FILE__ ) . 'wa-redirect-widget.php');
	include( plugin_dir_path( __FILE__ ) . 'wa-redirect-page.php');
}

function babaturan_pixel_button_style(){
	wp_enqueue_style( 'pvita_wa_rotator_lugin', plugin_dir_url( __FILE__ ).'wa-redirect-style.css');
}

add_action( 'wp_enqueue_scripts', 'babaturan_pixel_button_style' );

$edd_vars = array(
    // The plugin file, if this array is defined in the plugin
    'plugin_file' => __FILE__,

    // The current version of the plugin.
    // Also need to change in readme.txt and plugin header.
    'version' => '1.2',

    // The main URL of your store for license verification
    'store_url' => 'http://babaturan.net/members',

    // Your name
    'author' => 'Babaturan Team',

    // The URL to renew or purchase a license
    'purchase_url' => 'http://babaturan.net/members/checkout?edd_action=add_to_cart&download_id=12928',

    // The URL of your contact page
    'contact_url' => 'http://yoursite.com/contact',

    // This should match the download name exactly
    'item_name' => 'Wa Rotator',

    // The option names to store the license key and activation status
    'license_key' => 'pvita_wa_rotator_key',
    'license_status' => 'pvita_wa_rotator_status',

    // Option group param for the settings api
    'option_group' => 'pvita_wa_rotator',

	// The plugin settings admin page slug
    'admin_page_slug' => 'pvita-wa-rotator',

    // If using add_menu_page, this is the parent slug to add a submenu item underneath.
    // Otherwise we'll add our own parent menu item.
    'parent_menu_slug' => '',

    // The translatable title of the plugin
    'plugin_title' => __( 'WA Rotator', 'edd-sample' ),

    // Title of the settings page with activation key
    'settings_page_title' => __( 'Settings', 'edd-sample' ),

    // If this plugin depends on another plugin to be installed,
    // we can either check that a class exists or plugin is active.
    // Only one is needed.
    'dependent_class_to_check' => '\\Elementor\Plugin', // name of class to verify...
    'dependent_plugin' => '', // ...or plugin name for is_plugin_active() call
    'dependent_plugin_title' => __( 'Elementors', 'edd-sample' ),
);

if ( !class_exists( 'wa_rotator_SL_Plugin_Updater' ) ) {
    // load our custom updater
    include( dirname( __FILE__ ) . '/edd/wa_rotator_SL_Plugin_Updater.php' );
}

// You should rename the class in edd/edd.php and modify this to avoid any conflicts
if ( !class_exists( 'wa_rotator_plugin_updater' ) )
	require_once( dirname( __FILE__ ) . '/edd/wa_rotator_plugin_updater.php' );

// Kick off our EDD class
new wa_rotator_plugin_updater( $edd_vars );

