<?php
/**
 * Admin Actions
 *
 * @package     MASHFS
 * @subpackage  Admin/Actions
 * @copyright   Copyright (c) 2015, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.1
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


 


/**
 * 
 * @global type $mashsb_options
 * 
 * @deprecated since version 1.0.3
 */
function mashfs_save_order(){
        global $mashsb_options;
        // Get all settings
        
        $current_list = get_option('mashfs_settings');
        $new_order = $_POST['mashfs_list'];
        $new_list =  array();
        
        foreach ($new_order as $n){
            if (isset($current_list[$n])){
                $new_list[$n] = $current_list[$n];  
            }
        }

        //wp_die(print_r($new_list));     
        //wp_die(print_r($_POST));
        /* Update sort order of networks */

        //update_option('mashfs_settings', $new_list['mashfs_networks']);
        die();
}
//add_action ('wp_ajax_mashfs_update_order', 'mashfs_save_order');