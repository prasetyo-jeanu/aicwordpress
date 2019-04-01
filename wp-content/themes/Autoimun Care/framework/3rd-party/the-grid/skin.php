<?php
/**
 * The Grid Total Skin
 *
 * @package Total WordPress Theme
 * @subpackage 3rd Party
 * @version 4.5.4
 */

if ( is_admin() ) {
   return '<div class="tg-item-content"></div>';
}

global $tg_skins_preview; // global var to check if the skin is used for the preview mode

$output = '';

$tg_el = The_Grid_Elements();

$grid_item = tg_get_grid_item();

global $post;
$post = get_post( $grid_item['ID'], OBJECT );

setup_postdata( $post );

$output .= '<div class="tg-item-content">';

    ob_start();
            
    wpex_get_template_part( 'blog_entry' );

    $output .= ob_get_clean();

$output .= '</div>';

wp_reset_postdata();

return $output;