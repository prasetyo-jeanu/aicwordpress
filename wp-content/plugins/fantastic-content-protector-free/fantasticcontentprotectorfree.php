<?php
/* /
  Plugin Name: Fantastic Content Protector Free
  Plugin URI: http://fantasticplugins.com/fantastic-content-protector-free
  Description: Automatically protects your content by disabling copy actions such as Ctrl+a, Ctrl+c, Ctrl+p, Ctrl+v, Ctrl+x, Image Dragging etc.Checkout Pro Version <a href="http://fantasticplugins.com/shop/ultimate-content-protector/">Ultimate Content Protector</a>.
  Version: 2.6
  Author: Fantastic Plugins
  Author URI: http://fantasticplugins.com
  License: GPLv2
  / */

class FantasticContentProtectorFree {

    public function __construct() {

        require_once('inc/admin.php');
        if (isset($_POST["Reset"])) {
            add_action('admin_init', array($this, 'update_setting_fantastic_content_protector'));
        }
    }

    public static function fantastic_content_admin_free_menu() {
        add_submenu_page('options-general.php', 'Fantastic Content Protector Free', 'Fantastic Content Protector Free', 'manage_options', 'content_protector_free', 'fantasticcontent_admin_pages');
    }

    public static function register_setting_fantastic_content_protector() {
        register_setting('general_credit_links', 'content_protector_disable');
        register_setting('general_credit_links', 'credit_link_content_protect');
        register_setting('general_credit_links', 'credit_link_display');
    }

    public static function update_setting_fantastic_content_protector() {
        delete_option('content_protector_disable');
        delete_option('credit_link_content_protect');
        add_option('credit_link_content_protect', '2');
        $input_anchor_text = array("Fantastic Plugins", "FantasticPlugins", "FantasticPlugins.com");
        $rand_anchor_text = rand(0, 2);
        $input_url = array("http://fantasticplugins.com");

        $rand_url = rand(0, 0);
        $input_text = array("Content Protector Developer", "Plugin Developer", "Plugin Supporter", "Plugin Engineered By", "Content Protection Supported By", "Content Protector Engineered By", "Supporter of Content Protector", "Plugin Support By", "Plugin Developer", "Plugin Developed By");
        $random_text = rand(0, 9);
        if ((get_option('credits_defaults_new') == 'http://www.velhightech.com') || (get_option('credits_defaults_new') == 'http://www.youtube.com/watch?v=N2Qxi7r_CaY') || (get_option('credits_defaults_new') == 'http://www.youtube.com/watch?v=nf1o13L2G-c') || (get_option('credits_defaults_new') == 'http://www.youtube.com/watch?v=gEvr3cuJApc') || (get_option('credits_defaults_new') == 'http://www.youtube.com/watch?v=3Ea6o5CaKtg') || (get_option('credits_defaults_new') == 'http://www.youtube.com/watch?v=wwLJCuPCNyQ')) {
            delete_option('credits_defaults_new');
            delete_option('credits_name_new');
        } else {

            add_option('credits_name_new', $input_anchor_text[$rand_anchor_text]);
            add_option('credits_defaults_new', $input_url[$rand_url]);
        }
        $input_nofollow = array("nofollow", "dofollow");
        $random = rand(0, 100);
        $nofollow_key = 1;
        if ($random <= 90) {
            $nofollow_key = 1;
        } else {
            $nofollow_key = 0;
        }

        add_option('credits_nofollow_new', $input_nofollow[$nofollow_key]);
        add_option('credit_text_new', $input_text[$random_text]);
    }

    public static function fantasticcontentprotector() {
        if (get_option('content_protector_disable') != '1') {
            ?>
            <style>
                body {
                    -webkit-touch-callout: none;
                    -webkit-user-select: none;
                    -khtml-user-select: none;
                    -moz-user-select: none;
                    -ms-user-select: none;
                    user-select: none;
                }
            </style>


            <script type="text/javascript">
                //<![CDATA[
                document.onkeypress = function(event) {
                    event = (event || window.event);
                    if (event.keyCode === 123) {
                        //alert('No F-12');
                        return false;
                    }
                };
                document.onmousedown = function(event) {
                    event = (event || window.event);
                    if (event.keyCode === 123) {
                        //alert('No F-keys');
                        return false;
                    }
                };
                document.onkeydown = function(event) {
                    event = (event || window.event);
                    if (event.keyCode === 123) {
                        //alert('No F-keys');
                        return false;
                    }
                };

                function contentprotector() {
                    return false;
                }
                function mousehandler(e) {
                    var myevent = (isNS) ? e : event;
                    var eventbutton = (isNS) ? myevent.which : myevent.button;
                    if ((eventbutton === 2) || (eventbutton === 3))
                        return false;
                }
                document.oncontextmenu = contentprotector;
                document.onmouseup = contentprotector;
                var isCtrl = false;
                window.onkeyup = function(e)
                {
                    if (e.which === 17)
                        isCtrl = false;
                }

                window.onkeydown = function(e)
                {
                    if (e.which === 17)
                        isCtrl = true;
                    if (((e.which === 85) || (e.which === 65) || (e.which === 80) || (e.which === 88) || (e.which === 67) || (e.which === 86) || (e.which === 83)) && isCtrl === true)
                    {
                        return false;
                    }
                }
                isCtrl = false;
                document.ondragstart = contentprotector;
                //]]>
            </script>
            <?php
        }
    }

}

function add_credit_link() {
    if (get_option('content_protector_disable') != '1') {
        if (get_option('credit_link_content_protect') != 2) {
            ?>
            <center><small> <small align="center"> <?php echo get_option('credit_text_new'); ?> <a href="<?php echo get_option('credits_defaults_new'); ?>" rel="<?php echo get_option('credits_nofollow_new'); ?>" > <?php echo get_option('credits_name_new'); ?></a> </small></small></center>
            <?php
        }
    }
}

function plugin_settings_links($links) {
    $settings_link = '<a href="options-general.php?page=content_protector_free">Settings</a>';
    array_unshift($links, $settings_link);
    return $links;
}

$new = new FantasticContentProtectorFree();
$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'plugin_settings_links');
add_action('wp_footer', 'add_credit_link');
add_action('admin_menu', array('FantasticContentProtectorFree', 'fantastic_content_admin_free_menu'));
add_action('wp_head', array('FantasticContentProtectorFree', 'fantasticcontentprotector'));
add_action('admin_init', array('FantasticContentProtectorFree', 'register_setting_fantastic_content_protector'));
register_activation_hook(__FILE__, array('FantasticContentProtectorFree', 'update_setting_fantastic_content_protector'));
?>