<?php
// =============================================================================
// FUNCTIONS.PHP
// -----------------------------------------------------------------------------
// Theme functions.
// =============================================================================
define("YELLOWBUSSUN_URL", get_template_directory_uri());
define("YELLOWBUSSUN_PATH", get_template_directory());
require_once(YELLOWBUSSUN_PATH . "/inc/class-tgm-plugin-activation.php");
add_theme_support('post-thumbnails');
function yellowbusiness_register_required_plugins() {
	$plugins = array(
        //array(
        //    'name'               => esc_html__('Slider Revolution',"yellowbusiness"),
        //    'slug'               => 'revslider',
        //    'source'             => YELLOWBUSSUN_PATH . '/inc/revslider.zip',
        //    'required'           => true,
        //    'version'            => '',
        //    'force_activation'   => false,
        //    'force_deactivation' => false,
        //    'external_url'       => '',
        //    'is_callable'        => '',
        //),
        array(
			'name'               => esc_html__('Hybrid Composer',"yellowbusiness"),
			'slug'               => 'hybrid-composer',
			'source'             => YELLOWBUSSUN_PATH . '/inc/hybrid-composer.zip',
			'required'           => true,
			'version'            => '',
			'force_activation'   => false,
			'force_deactivation' => false,
			'external_url'       => '',
			'is_callable'        => '',
		)
	);
	$config = array(
		'id'           => 'theme-tgmpa',
		'default_path' => '',
		'menu'         => 'tgmpa-install-plugins',
		'has_notices'  => true,
		'dismissable'  => true,
		'dismiss_msg'  => '',
		'is_automatic' => false,
		'message'      => ''
	);
	tgmpa( $plugins, $config );
}
add_action( 'tgmpa_register', 'yellowbusiness_register_required_plugins' );
if (!isset( $content_width )) $content_width = 1200;
add_theme_support( 'automatic-feed-links' );
add_theme_support('title-tag');
function yellowbusiness_enqueue_front_end_script() {
    if (!defined("HC_PLUGIN_PATH")) {
        wp_enqueue_style("bootstrap", YELLOWBUSSUN_URL . "/css/bootstrap/css/bootstrap.css", array(), "1.0", "all");
        wp_enqueue_script("bootstrap",  YELLOWBUSSUN_URL . '/css/bootstrap/js/bootstrap.min.js', array("jquery"), "1.0", true);
        wp_enqueue_script("hc_script",  YELLOWBUSSUN_URL . '/inc/default.js', array(), "1.0",true);
        wp_enqueue_style("hc_style", YELLOWBUSSUN_URL . "/style.css", array(), "1.0", "all");
        wp_enqueue_style("hc_icons", YELLOWBUSSUN_URL . "/inc/icons/icons.css", array(), "1.0", "all");
        wp_enqueue_style("hc_css_skin", YELLOWBUSSUN_URL . "/skin.css", array(), "1.0", "all");
    }
    if (is_singular() && comments_open() && (get_option('thread_comments') == 1)) wp_enqueue_script('comment-reply');
}
add_action('wp_enqueue_scripts', 'yellowbusiness_enqueue_front_end_script');
add_action('save_post', function () {
    if (isset($_POST['sidebars-menu'])) {
        $sidebar = $_POST['sidebars-menu'];
        if (update_post_meta($_POST['post_ID'], 'wptf-sidebar', $sidebar) != true) {
            add_post_meta($_POST['post_ID'], 'wptf-sidebar', $sidebar);
        }
    }
}, 10, 2);
function yellowbusiness_sidebar() {
    add_meta_box('wptf_sidebar', 'Sidebars', function () {
        $sidebar = get_post_meta(get_the_ID(), 'wptf-sidebar');
        if (count($sidebar) > 0) $sidebar = $sidebar[0];
        else $sidebar = "";
?>
<select data-hc-setting="sidebars" id="sidebars-menu" name="sidebars-menu">
    <option value="" <?php if ($sidebar == "") echo "selected" ?>><?php esc_attr_e("None","yellowbusiness") ?></option>
    <option value="right" <?php if ($sidebar == "right") echo "selected" ?>><?php esc_attr_e("Right","yellowbusiness") ?></option>
    <option value="left" <?php if ($sidebar == "left") echo "selected" ?>><?php esc_attr_e("Left","yellowbusiness") ?></option>
    <option value="right-left" <?php if ($sidebar == "right-left") echo "selected" ?>><?php esc_attr_e("Right and left","yellowbusiness") ?></option>
</select>
<?php
    }, array(array('Posts','post'), array('Pages','page'),array('Post Types','y-post-types'),array('Post Types Archivies','y-post-types-arc')), 'side', 'low' );
}
add_action('add_meta_boxes', 'yellowbusiness_sidebar');

function yellowbusiness_theme_add_editor_styles() {
    add_editor_style(YELLOWBUSSUN_URL . "/css/tiny-mce.css");
}
add_action('admin_init', 'yellowbusiness_theme_add_editor_styles');

//MENU
function yellowbusiness_init_menus() {
    register_nav_menus(
          array(
            'header-menu' => esc_html__('Header Menu',"yellowbusiness"),
            'footer-menu' => esc_html__('Footer Menu',"yellowbusiness"),
            'extra-menu' => esc_html__('Top mini Menu',"yellowbusiness")
          )
    );
}
add_action('after_setup_theme', 'yellowbusiness_init_menus');

//WIDGETS
function yellowbusiness_widgets_init() {
    register_sidebar( array(
        'name'          => esc_html__('Right Sidebar',"yellowbusiness"),
        'id'            => 'right_side_bar',
        'description'   => esc_html__('Global sidebar for pages, enable it on single page.',"yellowbusiness"),
        'before_widget' => '<div class="list-group list-blog">',
        'after_widget'  => '</div>',
        'before_title'  => '<p class="list-group-item active">',
        'after_title'   => '</p>',
    ) );
    register_sidebar(array(
        'name'          => esc_html__('Left Sidebar',"yellowbusiness"),
        'id'            => 'left_side_bar',
        'description'   => esc_html__('Global sidebar for pages, enable it on single page.',"yellowbusiness"),
        'before_widget' => '<div class="list-group list-blog">',
        'after_widget'  => '</div>',
        'before_title'  => '<p class="list-group-item active">',
        'after_title'   => '</p>',
    ));
    if (class_exists('WooCommerce')) {
        register_sidebar(array(
           'name'          => esc_html__('Woocommerce Shop Sidebar Left',"yellowbusiness"),
           'id'            => 'woocommerce_shop_left_side_bar',
           'description'   => esc_html__('Shop sidebar, enable it on Theme options > List Post Types',"yellowbusiness"),
           'before_widget' => '<div class="list-group list-blog">',
           'after_widget'  => '</div>',
           'before_title'  => '<p class="list-group-item active">',
           'after_title'   => '</p>',
       ));
        register_sidebar(array(
          'name'          => esc_html__('Woocommerce Shop Sidebar Right',"yellowbusiness"),
          'id'            => 'woocommerce_shop_right_side_bar',
          'description'   => esc_html__('Shop sidebar, enable it on Theme options > List Post Types',"yellowbusiness"),
          'before_widget' => '<div class="list-group list-blog">',
          'after_widget'  => '</div>',
          'before_title'  => '<p class="list-group-item active">',
          'after_title'   => '</p>',
      ));
        register_sidebar(array(
          'name'          => esc_html__('Woocommerce Item Sidebar Left',"yellowbusiness"),
          'id'            => 'woocommerce_single_left_side_bar',
          'description'   => esc_html__('Single product sidebar, enable it on Theme options > List Post Types',"yellowbusiness"),
          'before_widget' => '<div class="list-group list-blog">',
          'after_widget'  => '</div>',
          'before_title'  => '<p class="list-group-item active">',
          'after_title'   => '</p>',
      ));
        register_sidebar(array(
           'name'          => esc_html__('Woocommerce Item Sidebar Right',"yellowbusiness"),
           'id'            => 'woocommerce_single_right_side_bar',
           'description'   => esc_html__('Single product sidebar, enable it on Theme options > List Post Types',"yellowbusiness"),
           'before_widget' => '<div class="list-group list-blog">',
           'after_widget'  => '</div>',
           'before_title'  => '<p class="list-group-item active">',
           'after_title'   => '</p>',
       ));
    }
}
add_action( 'widgets_init', 'yellowbusiness_widgets_init' );

//MAIN CONTENT
function yellowbusiness_get_post_info() {
    $txt = "";
    $txt .= '<div class="info-box">';
    $txt .= '</span><span class="info-date"><i class="icon-calendar"></i> <a href="#">' . get_the_date() . '</a></span>';
    $txt .= '<span class="info-cat"><i class="icon-android-folder"></i> ';
    $categories = get_the_category();
    $separator = ', ';
    $output = '';
    if ( ! empty( $categories ) ) {
        foreach( $categories as $category ) {
            $output .= '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '" alt="' . esc_attr( sprintf( __( 'View all posts in %s', 'yellowbusiness' ), $category->name ) ) . '">' . esc_html( $category->name ) . '</a>' . $separator;
        }
        $txt .= trim( $output, $separator );
    }
    $txt .= '</span><span class="info-author"><i class="icon-android-contact"></i> <a href="' . get_author_posts_url(get_the_author_meta('ID')) . '">' . get_the_author() . '</a></span>';
    $output = '';
    $posttags = get_the_tags();
    if ($posttags) {
        foreach($posttags as $tag) {
            $output .= '<a href="' . get_tag_link($tag->term_id) . '">' . $tag->name . '</a>' . $separator; 
        }
        $txt .= '<i class="icon-bookmark"></i> ' . trim( $output, $separator );
    }
    return $txt . '</div>';
}

function yellowbusiness_the_content() {
    function show_the_content() {
        global $HC_CLASSIC_CONTENT;
        while (have_posts()) {
            the_post();
            if (defined("HC_PLUGIN_PATH"))  {
                if (hc_get_setting("featured-image"))  {
                    the_post_thumbnail("large");
                } 
            } else { ?>
<div class="featured-image">
    <?php the_post_thumbnail("large"); ?>
</div>
             <?php }
            the_content();
            if ($HC_CLASSIC_CONTENT || !isset($HC_CLASSIC_CONTENT)) {
                if (get_post_type() == "post") {
                    echo yellowbusiness_get_post_info();
                }   
            }  
            wp_link_pages(array('before' => '<div class="pagination post-pagination">','after' => '</div>','link_before' => '<span>','link_after' => '</span>','pagelink' => '%'));
             ?>

<?php if (comments_open()) { ?>
<div class="comments-cnt <?php if (!have_comments()) echo "no-comments" ?>">
    <div class="container">
        <?php comments_template() ?>
    </div>
</div>
<?php }
        }
    }
    $default_content = false;
    if (!defined("HC_PLUGIN_PATH")) {
        $default_content = true;
    } else {
        global $HC_CLASSIC_CONTENT;
        if ($HC_CLASSIC_CONTENT == true) $default_content = true;
    }
    if ($default_content) {
?>
<div class="header-base">
    <div class="container">
        <div class="row">
            <div class="col-md-9">
                <div class="title-base text-left">
                    <h1>
                        <?php the_title(); ?>
                    </h1>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
    } else {
        hc_get_title();
    }
    $post_type_id = 0;
    $post_type = get_post_type();
    if ($post_type != "post" && $post_type != "page") {
        $current_post_type = get_post_type_object(get_post_type());
        $lists_ids = array();
        $args = array( 'post_type' => 'y-post-types', 'posts_per_page' => 999 );
        $the_query = new WP_Query($args);
        if ($the_query->have_posts()) {
            while ($the_query->have_posts()) {
                $the_query->the_post();
                if (strcasecmp($current_post_type->label,$the_query->post->post_name) == 0) {
                    $post_type_id = $the_query->post->ID;
                }
            }
        }
        wp_reset_query();
    } else {
        $post_type_id = get_the_ID();
    }
    $sidebar = get_post_meta($post_type_id, 'wptf-sidebar');
    $sw = array("left"=>"col-md-3","right"=>"col-md-3","content"=>"col-md-9");
    
    if (count($sidebar) > 0) {
        $sidebar = $sidebar[0];
        $woocommerce_prefix = "";
        if (defined("HC_PLUGIN_PATH") && hc_get_setting("shop-page") == $post_type_id) $woocommerce_prefix = "woocommerce_shop_";
        if (defined("HC_PLUGIN_PATH")) $sw = hc_get_sidebars_width($sidebar);
    }
    else $sidebar = "";
    if ($default_content || $sidebar != "") {
        if ($sidebar != "") echo "<div class='sidebar-cnt'>"; ?>
<div class="container content <?php if ($sidebar != "") echo "sidebar-content"; ?>">
    <?php }
    if ($sidebar == "left") {
    ?>
    <div class="row">
        <div class="<?php echo esc_attr($sw["left"]) ?> widget">
            <?php if (is_active_sidebar("left_side_bar")) dynamic_sidebar($woocommerce_prefix . "left_side_bar"); ?>
        </div>
        <div class="<?php echo esc_attr($sw["content"]) ?>">
            <?php show_the_content() ?>
        </div>
    </div>
    <?php
    }
    if ($sidebar == "right") {
    ?>
    <div class="row">
        <div class="<?php echo esc_attr($sw["content"]) ?>">
            <?php show_the_content() ?>
        </div>
        <div class="<?php echo esc_attr($sw["right"]) ?> widget">
            <?php if (is_active_sidebar("right_side_bar")) dynamic_sidebar($woocommerce_prefix . "right_side_bar"); ?>
        </div>
    </div>
    <?php
    }
    if ($sidebar == "right-left") {
    ?>
    <div class="row">
        <div class="<?php echo esc_attr($sw["left"]) ?> widget">
            <?php if (is_active_sidebar("left_side_bar")) dynamic_sidebar($woocommerce_prefix . "left_side_bar"); ?>
        </div>
        <div class="<?php echo esc_attr($sw["content"]) ?>">
            <?php show_the_content() ?>
        </div>
        <div class="<?php echo esc_attr($sw["right"]) ?> widget">
            <?php if (is_active_sidebar("right_side_bar")) dynamic_sidebar($woocommerce_prefix . "right_side_bar"); ?>
        </div>
    </div>
    <?php
    }
    if ($sidebar == "") {
        show_the_content();
    }
    if ($default_content || $sidebar != "") echo "</div></div>";
}
function yellowbusiness_search() {
    if (defined("HC_PLUGIN_PATH")) { hc_default_title(); }
    else {
    ?>
    <div class="header-base search-results-header">
        <div class="container">
            <div class="row">
                <div class="col-md-9">
                    <div class="title-base text-left">
                        <h1><?php echo esc_html__("Search results for ","yellowbusiness") . get_search_query() ?></h1>
                        <p><?php esc_attr_e("Search results for ","yellowbusiness"); echo '<b><i>' . get_search_query() . '</i></b>'; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
    <div class="content-parallax">
        <div class="section-item section-empty">
            <div class="content container">
                <div class="search-page-box">
                    <form role="search" method="get" id="searchform" class="navbar-form" action="<?php echo esc_url(home_url()) ?>">
                        <div class="input-group">
                            <input name="s" id="s" type="text" class="form-control" placeholder="<?php esc_attr_e( 'Search for ...','yellowbusiness'); ?>" />
                            <span class="input-group-btn">
                                <input class="btn btn-default" type="submit" id="searchsubmit" value="<?php esc_attr_e( 'GO','yellowbusiness'); ?>" />
                            </span>
                        </div>
                    </form>
                </div>
                <?php
    global $query_string;
    global $wp_query;
    $query_args = explode("&", $query_string);
    $search_query = array();
    if( strlen($query_string) > 0 ) {
        foreach($query_args as $key => $string) {
            $query_split = explode("=", $string);
            $search_query[$query_split[0]] = urldecode($query_split[1]);
        }
    }
    if ($wp_query->found_posts > 0) {
        while ($wp_query->have_posts()) {
            $wp_query->the_post();
            $link = get_the_permalink();
            $img = get_the_post_thumbnail_url($wp_query->post->ID, 'large');
            $css = "advs-box-side-icon";
            if ($img != "" && $img != false)  $css = "advs-box-side";
                ?>
                <div class="advs-box <?php echo esc_attr($css) ?> default-box">
                    <div class="row">
<?php
            if ($img != "" && $img != false) {
                echo '<div class="col-md-4"><a  href="' . esc_url($link) . '" class="img-box"><img src="' . esc_url($img) . '" alt="" /></a></div><div class="col-md-8">';
            } else {
                echo '<div class="col-md-12">';
            }
?>                         <h3>
                                <a href="<?php echo esc_url($link) ?>">
                                    <?php echo esc_attr(get_the_title()) ?>
                                </a>
                            </h3>
                            <hr class="anima" />
                            <p>
                                <?php
            if (defined("HC_PLUGIN_PATH")) {
                echo hc_get_the_excerpt(get_the_excerpt());
            } else echo get_the_excerpt();
                                ?>
                            </p>
                            <hr class="space s" />
                            <a class="circle-button btn btn-sm" href="<?php echo esc_url($link) ?>">
                                <?php esc_attr_e("Read more","yellowbusiness") ?>
                            </a>
                        </div>
                    </div>
                </div>
                <hr class="space" />
                <?php
        }
    } else echo "<h2 class='no-search-results'>" . esc_html__("No results found ...","yellowbusiness") . "</h2><hr class='space row-500' />";
                ?>
            </div>
        </div>
    </div>
    <?php
}
function yellowbusiness_set_default_menu() {
    if (($locations = get_nav_menu_locations()) && isset($locations["header-menu"])) {
        $menu = wp_get_nav_menu_object($locations["header-menu"]);
        if (isset($menu->term_id)) {
            $menu_items = wp_get_nav_menu_items($menu->term_id);
            $menu_count = count($menu_items);
            for ($i = 0; $i < $menu_count; $i++) {
                $menu_item = $menu_items[$i];
                if ($menu_item->ID != "-1") {
                    if ($i < $menu_count - 1 && $menu_items[$i + 1]->menu_item_parent == $menu_item->ID) {
    ?>
    <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"><?php echo esc_attr($menu_item->title) ?> <span class="caret"></span></a>
        <ul class="dropdown-menu">
            <?php
                        for ($j = $i; $j < $menu_count; $j++) {
                            $menu_sub_item_a = $menu_items[$j];
                            if ($menu_items[$j]->menu_item_parent == $menu_item->ID) {
                                if ($j < $menu_count - 1 && $menu_items[$j + 1]->menu_item_parent == $menu_sub_item_a->ID) {
            ?>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"><?php echo esc_attr($menu_sub_item_a->title) ?> <span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <?php
                                    for ($y = $j; $y < $menu_count; $y++) {
                                        $menu_sub_item_b = $menu_items[$y];
                                        if ($menu_items[$y]->menu_item_parent == $menu_sub_item_a->ID) {
                                            $menu_items[$y]->ID = "-1";
                    ?>
                    <li><a href="<?php echo esc_url($menu_sub_item_b->url) ?>"><?php echo esc_attr($menu_sub_item_b->title) ?></a></li>
                    <?php
                                        }
                                    }
                    ?>
                </ul>
            </li>
            <?php
                                } else {
            ?>
            <li><a href="<?php echo esc_url($menu_sub_item_a->url) ?>"><?php echo esc_attr($menu_sub_item_a->title) ?></a></li>
            <?php
                                }
                                $menu_items[$j]->ID = "-1";
                            }
                        }
            ?>
        </ul>
    </li>
    <?php
                    } else {
    ?>
    <li><a href="<?php echo esc_url($menu_item->url) ?>"><?php echo esc_attr($menu_item->title) ?></a></li>
    <?php
                    }
                }
            }
        }
    }
}
function yellowbusiness_default_blog() {
    global $wp_query;
    if (have_posts()) {
        while (have_posts()) {
            the_post(); 
            $date = get_the_date("U");
    ?>
   <div class="advs-box advs-box-side default-box <?php if (is_sticky()) echo "sticky-post"; ?>" data-anima="fade-left" data-trigger="hover">
    <div class="row">
        <div class="col-md-12">
            <?php if (!defined("HC_PLUGIN_PATH")) { ?>
            <div class="block-infos">
                <div class="block-data">
                    <p class="bd-day">
                        <?php echo date('d', $date) ?>
                    </p>
                    <p class="bd-month">
                        <?php echo date_i18n('M', $date) . ' ' . date('Y', $date) ?>
                    </p>
                </div>
            </div>
            <?php } ?>
            <h3>
                <a href="<?php echo esc_url(get_the_permalink()) ?>">
                    <?php echo esc_attr(get_the_title()) ?>
                </a>
            </h3>
            <?php echo yellowbusiness_get_post_info(); ?>
            <?php if (!defined("HC_PLUGIN_PATH")) the_post_thumbnail() ?>
            <hr class="anima" />
            <p>
                <?php echo get_the_excerpt(); ?>
            </p>
            <hr class="space s" />
            <a class="circle-button btn btn-sm" href="<?php echo esc_url(get_the_permalink()) ?>">
                <?php esc_attr_e("Read more","yellowbusiness") ?>
            </a>
        </div>
    </div>
</div>
    <hr class="space" />
    <?php }
        wp_reset_postdata();
    }
    if ($wp_query->max_num_pages > 1) {  ?>
    <ul class="pagination-sm pagination-grid pagination default-pagination">
        <li class="prev">
            <?php echo get_previous_posts_link(__("< Previous","yellowbusiness")); ?>
        </li>
        <li class="next">
            <?php echo get_next_posts_link(__("Next >","yellowbusiness"), $wp_query->max_num_pages ); ?>
        </li>
    </ul>
    <?php }
}
    ?>
