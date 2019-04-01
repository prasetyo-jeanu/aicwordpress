<?php
/**
 * Weclome Page Class
 *
 * @package     MASHFS
 * @subpackage  Admin/Welcome
 * @copyright   @todo
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * MASHFS_Welcome Class
 *
 * A general class for About and Credits page.
 *
 * @since 1.0
 */
class MASHFS_Welcome {

	/**
	 * @var string The capability users should have to view the page
	 */
	public $minimum_capability = 'manage_options';

	/**
	 * Get things started
	 *
	 * @since 1.0.1
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'welcome'    ) );
	}

	/**
	 * Sends user to the Settings page on first activation of MASHFS as well as each
	 * time MASHFS is upgraded to a new version
	 *
	 * @access public
	 * @since 1.0.0
	 * @global $mashfs_options Array of all the MASHFS Options
	 * @return void
	 */
	public function welcome() {
		global $mashfs_options;

		// Bail if no activation redirect
		if ( ! get_transient( '_mashfs_activation_redirect' ) )
			return;

		// Delete the redirect transient
		delete_transient( '_mashfs_activation_redirect' );

		// Bail if activating from network, or bulk
		if ( is_network_admin() || isset( $_GET['activate-multi'] ) )
			return;

		$upgrade = get_option( 'mashfs_version_upgraded_from' );

                /*if mashsb core exists (or is activated) redirect otherwise exit or we get a permission error */
		if (class_exists( 'Mashshare' )) { // First time install
			wp_safe_redirect( admin_url( 'admin.php?page=mashsb-settings&tab=extensions#mashsb_settingsmashfs_header' ) ); exit;
		} else { // Update
			/* nothing here*/
		}
	}
}
new MASHFS_Welcome();
