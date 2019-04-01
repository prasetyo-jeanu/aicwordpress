<?php
/**
 * Plugin Name: Mashshare - Floating Sidebar Add-On
 * Plugin URI: https://www.mashshare.net
 * Description: Shows a sticky sharing sidebar on left or right edge of your screen
 * Author: René Hermenau, Steffen Arnold
 * Author URI: https://www.mashshare.net
 * Version: 1.3.8
 * Text Domain: mashfs
 * Domain Path: languages
 * 
 *
 * @package MASHFS
 * @category Add-On
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'MashshareFloatingSidebar' ) ) :

// Plugin version
if (!defined('MASHFS_VERSION')) {
    define('MASHFS_VERSION', '1.3.8');
}

/**
 * Main mashfs Class
 *
 * @since 1.0.0
 */
class MashshareFloatingSidebar {
	/** Singleton *************************************************************/

	/**
	 * @var MashshareFloatingSidebar $instance The one and only MashshareFloatingSidebar
	 * @since 1.0.0
	 */
	private static $instance;

	
	/**
	 * Main Instance
	 *
	 * Insures that only one instance of this Add-On exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @since 1.0.0
	 * @static
	 * @staticvar array $instance
	 * @return object self::$instance The one true MASHSB_Plugin_Name
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof MashshareFloatingSidebar ) ) {
			self::$instance = new MashshareFloatingSidebar();
			self::$instance->setup_constants();
			self::$instance->includes();
			self::$instance->load_textdomain();
                        self::$instance->hooks();
		}
		return self::$instance;
        }

	/**
	 * Throw error on object clone
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @return void
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'plugin-name' ), '1.0.0' );
	}

	/**
	 * Disable unserializing of the class
	 *
	 * @since 1.0.0
	 * @access protected
	 * @return void
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'plugin-name' ), '1.0.0' );
	}

	/**
	 * Setup plugin constants
	 *
	 * @access private
	 * @since 1.0.0
	 * @return void
	 */
	private function setup_constants() {
		global $wpdb, $mashsb_options;

		// Plugin Folder Path
		if ( ! defined( 'MASHFS_PLUGIN_DIR' ) ) {
			define( 'MASHFS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}

		// Plugin Folder URL
		if ( ! defined( 'MASHFS_PLUGIN_URL' ) ) {
			define( 'MASHFS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}

		// Plugin Root File
		if ( ! defined( 'MASHFS_PLUGIN_FILE' ) ) {
			define( 'MASHFS_PLUGIN_FILE', __FILE__ );
		}
                
	}

	/**
	 * Include required files
	 *
	 * @access private
	 * @since 1.0.0
	 * @return void
	 */
	private function includes() {
            require_once MASHFS_PLUGIN_DIR . 'includes/scripts.php';
            require_once MASHFS_PLUGIN_DIR . 'includes/template-functions.php';

            // Required files only available in backend
            if (is_admin() || ( defined('WP_CLI') && WP_CLI )) {
                require_once MASHFS_PLUGIN_DIR . 'includes/admin/settings.php';
                require_once MASHFS_PLUGIN_DIR . 'includes/admin/plugins.php';
                require_once MASHFS_PLUGIN_DIR . 'includes/admin/welcome.php';
            }
        }

        /**
         * Run action and filter hooks
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         *
         */
        private function hooks() {

             /* Instantiate class MASHSB_licence 
             * Create 
             * @since 1.0.0
             * @return apply_filter mashsb_settings_licenses and create licence key input field in Mashshare core plugin
             * 
             */
            if (class_exists('MASHSB_License')) {
                $mashsb_sl_license = new MASHSB_License(__FILE__, 'Floating Sidebar', MASHFS_VERSION, 'Rene Hermenau', 'edd_sl_license_key');
            }
        }

	/**
	 * Loads the plugin language files
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'mashfs', false, dirname( plugin_basename( MASHFS_PLUGIN_FILE ) ) . '/languages/' );
	}
       
       /* Activation function fires when the plugin is activated.  
         * Checks first if multisite is enabled
         * @since 1.3.1
         * 
         */

        public static function activation($networkwide) {
            global $wpdb;

            if (function_exists('is_multisite') && is_multisite()) {
                // check if it is a network activation - if so, run the activation function for each blog id
                if ($networkwide) {
                    $old_blog = $wpdb->blogid;
                    // Get all blog ids
                    $blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
                    foreach ($blogids as $blog_id) {
                        switch_to_blog($blog_id);
                        MashshareFloatingSidebar::mashfs_network_activation();
                    }
                    switch_to_blog($old_blog);
                    return;
                }
            }
            MashshareFloatingSidebar::mashfs_network_activation();
        }
        
        /**
	 * Activation function fires when the plugin is activated.
	 * This function is fired when the activation hook is called by WordPress.
	 *
	 * @since 1.0
	 * @access public
	 *
	 * @return void
	 */
	public static function mashfs_network_activation() {
                // Get mashsb settings. 
                // Do not use global $mashsb_options! 
                // its not sure if MASHSB() is running so this global var would be empty
		$mashsb_options = get_option('mashsb_settings'); 
		
                $previous_version = get_option('mashfs_version');
		
                if ($previous_version){
			update_option('mashfs_version_upgraded_from', $previous_version);
                }
                
                // Add the current version
		update_option('mashfs_version', MASHFS_VERSION);
                
		// Bail if activating from network, or bulk
//		if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
//			return;
//		}
                              
                // First time installation -> Store all social networks
                if (!isset($mashsb_options['mashfs_networks']) || count($mashsb_options['mashfs_networks']) < 10 ){
		$mashsb_options['mashfs_networks'] = array(
                        'facebook' => array(
                                'url' => 'http://www.facebook.com/sharer.php?u=$url',
                                'status' => 0,
			),
			'twitter' => array(
				'url' => 'https://twitter.com/intent/tweet?text=$twittertitle$via&amp;url=$urltw',
				'status' => 0,
			),
			'google' => array(
				'url' => 'https://plus.google.com/share?text=$title&amp;url=$url',
				'status' => 0,
			),
			'whatsapp' => array(
				'url' => 'whatsapp://send?text=$title%20$url',
				'status' => 0,
			),
			'pinterest' => array(
				'url' => 'https://pinterest.com/pin/create/button/?url=$url&amp;media=$image&amp;description=$title',
				'status' => 0,
			),
			'digg' => array(
				'url' => 'http://digg.com/submit?phase=2%20&amp;url=$url&amp;title=$title',
				'status' => 0,
			),
			'linkedin' => array(
				'url' => 'https://www.linkedin.com/shareArticle?trk=$title&amp;url=$url',
				'status' => 0,
			),
			'reddit' => array(
				'url' => 'http://www.reddit.com/submit?url=$url&amp;title=$title',
				'status' => 0,
			),
			'stumbleupon' => array(
				'url' => 'http://www.stumbleupon.com/submit?url=$url',
				'status' => 0,
			),
			'vk' => array(
				'url' => 'http://vkontakte.ru/share.php?url=$url&amp;item=$title',
				'status' => 0,
			),
			'print' => array(
				'url' => 'http://www.printfriendly.com/print/?url=$url&amp;item=$title',
				'status' => 0,
			),
			'delicious' => array(
				'url' => 'https://delicious.com/save?v=5&amp;noui&amp;jump=close&amp;url=$url&amp;title=$title',
				'status' => 0,
			),
			'buffer' => array(
				'url' => 'https://bufferapp.com/add?url=$url&amp;text=$title',
				'status' => 0,
			),
			'weibo' => array(
				'url' => 'http://service.weibo.com/share/share.php?url=$url&amp;title=$title',
				'status' => 0,
			),
			'pocket' => array(
				'url' => 'https://getpocket.com/save?title=$title&amp;url=$url',
				'status' => 0,
			),
			'xing' => array(
				'url' => 'https://www.xing.com/social_plugins/share?h=1;url=$url&amp;title=$title',
				'status' => 0,
			),
			'tumblr' => array(
				'url' => 'https://www.tumblr.com/share?v=3&amp;u=$url&amp;t=$title',
				'status' => 0,
			),
			'meneame' => array(
				'url' => 'http://www.meneame.net/submit.php?url=$url&amp;title=$title',
				'status' => 0,
			),
			'odnoklassniki' => array(
				'url' => 'http://www.odnoklassniki.ru/dk?st.cmd=addShare&amp;st.s=1&amp;st._surl=$url&amp;title=$title',
				'status' => 0,
			),
			'managewp' => array(
				'url' => 'http://managewp.org/share/form?url=$url&amp;title=$title',
				'status' => 0,
			),
                     'mailru' => array(
				'url' => 'http://connect.mail.ru/share?share_url=$url',
				'status' => 0,
			),
			'line' => array(
				'url' => 'http://line.me/R/msg/text/?$title%20$url',
				'status' => 0,
			),
		);
                    update_option('mashsb_settings', $mashsb_options);
                }
                
            // Remove broken mail icon added with version 1.2.0
            if( ( isset( $mashsb_options['mashfs_networks']['mail'] ) ||
                    array_key_exists( 'mail', $mashsb_options['mashfs_networks'] ) ) &&
                    version_compare( $previous_version, '1.2.0', '<=' ) ) {

                unset( $mashsb_options['mashfs_networks']['mail'] );
            }

            // Check if mail is missing than add it to the existing array 
            // (Needed for updating the plugin from a older version than 1.2.0)
            if( ( !isset($mashsb_options['mashfs_networks']['mail']) || !array_key_exists( 'mail', $mashsb_options['mashfs_networks'] ) ) 
                    && isset($mashsb_options['mashfs_networks']) ) {

                $mail_network = array(
                    'mail' => array(
                        'url' => 'mailto:?subject=$subject%20$title&amp;body=$body%20$url',
                        'status' => 0
                    )
                );
                $mashsb_options['mashfs_networks'] = array_merge( $mashsb_options['mashfs_networks'], $mail_network );
                update_option('mashsb_settings', $mashsb_options);
            }

		// Not a first time update, 1.2.2 update
		// Change Pinterest URL
		if (isset($mashsb_options['mashfs_networks']) &&
			version_compare($previous_version, '1.2.2', '<=') &&
			array_key_exists('pinterest', $mashsb_options['mashfs_networks'])
		) {
			$mashsb_options['mashfs_networks']['pinterest']['url'] = 'https://pinterest.com/pin/create/button/?url=$url&amp;media=$image&amp;description=$title';
			update_option('mashsb_settings', $mashsb_options);
		}
              // check if skype button is missing and add it
            // (Needed for updating the plugin from a older version than 1.3.3)
            if( ( !isset($mashsb_options['mashfs_networks']['skype']) || !array_key_exists( 'skype', $mashsb_options['mashfs_networks'] ) ) && isset($mashsb_options['mashfs_networks']) ) {
               
                $networks = array(
                    'frype' => array(
                        'url' => 'http://www.draugiem.lv/say/ext/add.php?title=$title&amp;link=$url',
                        'status' => 0
                    ),
                    'skype' => array(
                        'url' => 'https://web.skype.com/share?url=$url&lang=en-en',
                        'status' => 0
                    ),
                    'telegram' => array(
                        'url' => 'https://telegram.me/share/url?url=$url&text=$title',
                        'status' => 0
                    ),
                    'yummly' => array(
                        'url' => 'http://www.yummly.com/urb/verify?url=&amp;title=$title',
                        'status' => 0
                    ),
                    'flipboard' => array(
                        'url' => 'https://share.flipboard.com/bookmarklet/popout?v=2&title=$title&url=$url',
                        'status' => 0
                    ),
                    'hackernews' => array(
                        'url' => 'http://news.ycombinator.com/submitlink?u=$url&t=$title',
                        'status' => 0
                    ),
                );
                $mashsb_options['mashfs_networks'] = array_merge( $mashsb_options['mashfs_networks'], $networks );
                update_option('mashsb_settings', $mashsb_options);
            }  
            
//                    'yummly' => 'http://www.yummly.com/urb/verify?url=' . $url . '&amp;title=' . $title,
//        'frype' => 'http://www.draugiem.lv/say/ext/add.php?title='. $title .'&amp;link='.$url,
//        'skype' => 'https://web.skype.com/share?url='.$url.'&lang=en-en',
//        'telegram' => 'https://telegram.me/share/url?url='.$url.'&text=' . $title,
//        'flipboard' => 'https://share.flipboard.com/bookmarklet/popout?v=2&title=' . urlencode($title) . '&url=' . $url,
//        'hackernews' => 'http://news.ycombinator.com/submitlink?u='.$url.'&t='.urlencode($title),
           

            // Add the transient to redirect
            set_transient('_mashfs_activation_redirect', true, 30);
	}
}




/**
 * The main function responsible for returning the one true Add-On
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $MASHFS = MASHFS(); ?>
 *
 * @since 1.0.0
 * @return object The one true MashshareFloatingSidebar Instance
 *
 * @todo        Inclusion of the activation code below isn't mandatory, but
 *              can prevent any number of errors, including fatal errors, in
 *              situations where this extension is activated but MASHSB is not
 *              present.
 */

function MASHFS() {
    if( ! class_exists( 'Mashshare' ) ) {
        if( ! class_exists( 'MASHSB_Extension_Activation' ) ) {
            require_once 'includes/class.extension-activation.php';
        }
        $activation = new MASHSB_Extension_Activation( plugin_dir_path( __FILE__ ), basename( __FILE__ ) );
        $activation = $activation->run();
    } else {
        return MashshareFloatingSidebar::instance();
    }
}

/** 
 * The activation hook is called outside of the singleton because WordPress doesn't
 * register the call from within the class hence, needs to be called outside and the
 * function also needs to be static.
 */

register_activation_hook( __FILE__, array( 'MashshareFloatingSidebar', 'activation' ) );



// Get MashshareFloatingSidebar running after other plugins loaded
add_action( 'plugins_loaded', 'MASHFS' );

endif; // End if class_exists check