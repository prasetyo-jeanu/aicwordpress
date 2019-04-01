<?php
/**
 * Massive Addons Tweaks
 *
 * @package Total WordPress Theme
 * @subpackage 3rd Party
 * @version 4.5.4
 */

if ( ! class_exists( 'WPEX_MASSIVE_VC_ADDONS_CONFIG' ) ) {

	class WPEX_MASSIVE_VC_ADDONS_CONFIG {

		/**
		 * Main constructor
		 *
		 * @version 4.5.4
		 */
		public function __construct() {

			// Disable advanced parallax
			add_filter( 'vcex_supports_advanced_parallax', '__return_false' );

		}

	}
	
}
new WPEX_MASSIVE_VC_ADDONS_CONFIG();