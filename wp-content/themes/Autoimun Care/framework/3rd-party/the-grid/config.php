<?php
/**
 * The Grid Plugin Tweaks
 *
 * @package Total WordPress Theme
 * @subpackage 3rd Party
 * @version 4.5.4
 */

if ( ! class_exists( 'WPEX_The_Grid_Plugin_Config' ) ) {

	class WPEX_The_Grid_Plugin_Config {

		/**
		 * Main constructor
		 *
		 * @version 4.5.4
		 */
		public function __construct() {

			define( 'WPEX_THE_GRID_DIR', WPEX_FRAMEWORK_DIR . '3rd-party/the-grid/' );
			define( 'WPEX_THE_GRID_DIR_URI', WPEX_FRAMEWORK_DIR_URI . '3rd-party/the-grid/' );

			add_filter( 'tg_add_item_skin', array( $this, 'skins' ) );

		}

		/**
		 * Add custom skins
		 *
		 * @version 4.5.4
		 */
		public function skins( $skins ) {
			$skins['total'] = array(
				'type'   => 'grid',
				'filter' => 'Total',
				'slug'   => 'total',
				'name'   => 'Total',
				'php'    => WPEX_THE_GRID_DIR . 'skin.php',
				'css'    => false,
			);
			$skins['total_masonry'] = array(
				'type'   => 'masonry',
				'filter' => 'Total',
				'slug'   => 'total_masonry',
				'name'   => 'Total',
				'php'    => WPEX_THE_GRID_DIR . 'skin.php',
				'css'    => false,
			);
			return $skins;
		}

	}
	
}
new WPEX_The_Grid_Plugin_Config();