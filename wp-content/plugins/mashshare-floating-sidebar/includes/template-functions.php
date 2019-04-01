<?php

/**
 * Template Functions
 *
 * @package     MASHFS
 * @subpackage  Functions/Templates
 * @copyright   2015, Mashshare
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */
// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

/**
 * Filter for loading mashsb.js
 *
 * @return boolean true when MASHFS is enabled
 * since 1.0.1
 */
function mashfsMashsbActive($content) {

    if (mashfs_is_active()) {
        return true;
    } else {
        return $content;
    }
}

add_filter('mashsb_active', 'mashfsMashsbActive', 100);

/*
 * Create HTML output for the sidebar
 */

function mashfs_sidebar() {
    global $mashsb_options, $post;

    // true if it is excluded
    if (mashfsbar_is_excluded()) {
        return false;
    }

    if (!mashfs_is_active()) {
        return false;
    }

    $url = mashfs_get_current_url();
    
    $total_shares = (int) mashfs_get_sharecount($url);

    $mobile_hidden = mashfs_is_disabled() === true ? 'mashfs-hide' : '';
    $twitter_handle = !empty($mashsb_options['mashsharer_hashtag']) ? '&via=' . $mashsb_options['mashsharer_hashtag'] : '';
    $side = !isset($mashsb_options['mashfs_side']) || $mashsb_options['mashfs_side'] == 0 ? 'mashfs-right' : 'mashfs-left';
    $current_url = mashfs_get_current_url();
    $output = '<aside id="mashfs-main" class="' . $mobile_hidden . ' ' . $side . '" ' . mashfsCreateInlineCSS('#mashfs-main') . '>';
    $output .= '<div class="mashfs-buttons">';

    $share_label = !empty($mashsb_options['sharecount_title']) ? $mashsb_options['sharecount_title'] : 'shares';

    if (isset($total_shares) && mashfs_get_hide_sharecount($total_shares) && isset($mashsb_options['mashfs_enable_count'])) {
        $output .= '<div id="mashfs-total-shares" ' . mashfsCreateInlineCSS('#mashfs-total-shares') . '><span id="mashfs-count">' . roundshares($total_shares) . '</span> ' . $share_label . '</div>';
    }
    foreach ($mashsb_options['mashfs_networks'] as $network => $params) {

        if (isset($params['status']) && $params['status'] > 0 && $network === 'mail') {
            $output .= mashfs_get_mail_link($params['url'], $network);
        }

        if (isset($params['status']) && $params['status'] > 0 && $network !== 'twitter' && $network !== 'mail' && $network !== 'pinterest') {
            $url = str_replace('$url', $current_url, $params['url']);
            $url = str_replace('$title', rawurlencode(html_entity_decode(mashfs_get_document_title())), $url);
            $url = str_replace('$image', mashsb_get_image($post->ID), $url);
            $output .= '<a href="' . $url . '" class="mashicon-' . $network . ' mashfs-popup-share" ' . mashfsCreateInlineCSS('#mashfs-main-a') . ' target="_blank" rel="external nofollow"><span class="icon"></span></a>';
        }

        if (isset($params['status']) && $params['status'] > 0 && $network === 'twitter') {
            $url_tw = str_replace('$twittertitle', mashfs_get_twitter_title(), $params['url']);
            $url_tw = str_replace('$via', $twitter_handle, $url_tw);
            $url_tw = str_replace('$urltw', mashfs_get_twitter_url(), $url_tw);
            $output .= '<a href="' . $url_tw . '" class="mashicon-' . $network . ' mashfs-popup-share" ' . mashfsCreateInlineCSS('#mashfs-main-a') . ' target="_blank" rel="external nofollow"><span class="icon"></span></a>';
        }

        if (isset($params['status']) && $params['status'] > 0 && $network === 'pinterest') {
            $url_pi = str_replace('$title', urlencode(mashfs_get_pinterest_desc()), $params['url']);
            $url_pi = str_replace('$image', urlencode(mashfs_get_pinterest_image()), $url_pi);
            $url_pi = str_replace('$url', $current_url, $url_pi);
            $output .= '<a data-mashsb-url="' . $url_pi . '" href="#" class="mashicon-' . $network . ' mashfs-popup-share" ' . mashfsCreateInlineCSS('#mashfs-main-a') . ' target="_blank" rel="external nofollow"><span class="icon"></span></a>';
        }
    }

    if (isset($mashsb_options['mashfs_enable_toggle'])) {
        $output .= '<a href="#" id="mashfs-hidebtn" ' . mashfsCreateInlineCSS('#mashfs-hidebtn') . '></a>';
    }
    $output .= '</div></aside>';

    echo $output;
}

add_action('wp_footer', 'mashfs_sidebar');

/**
 * Get Sharecount
 * 
 * @global array $post
 * @param string url
 * 
 * @return int
 */
function mashfs_get_sharecount($url) {

    $shares = mashfs_check_version_3() ? mashfsGetNonPostShares_new($url) : mashfsGetNonPostShares_old($url);
    return apply_filters('mashfs_sharecount', $shares);
}

/**
 * Check if MashShare Version is later than 3.0.0
 * @return boolean
 */
function mashfs_check_version_3() {
    if (version_compare(MASHSB_VERSION, '3.0.0', '>=')) {
        return true;
    }
    return false;
}

/*
 * Check if sidebar is enabled on mobile
 *
 * @return bool True when it is allowed to be loaded on mobile devices
 */

function mashfs_is_disabled() {
    global $mashsb_options;
    $is_disabled = isset($mashsb_options['mashfs_disable_mobile']) ? true : false;

    return $is_disabled;
}

/*
 * Create array of custom inline styles for elements
 *
 * @return array => key is the class name or id of the element
 */

function mashfsCreateInlineCSS($class) {
    global $mashsb_options;
    $bgcolor = !empty($mashsb_options['mashfs_backgroundcolor']) ? '#' . $mashsb_options['mashfs_backgroundcolor'] : 'transparent';
    $sharecolor = !empty($mashsb_options['mashfs_sharecountcolor']) ? $mashsb_options['mashfs_sharecountcolor'] : '5a5a5f';
    $margin = !empty($mashsb_options['mashfs_margin_edge']) ? $mashsb_options['mashfs_margin_edge'] : '0';
    $marginbottom = !empty($mashsb_options['mashfs_margin_buttons']) ? $mashsb_options['mashfs_margin_buttons'] : '0';

    $styles = array(
        '#mashfs-main' => 'background-color: ' . $bgcolor . '; margin-left:' . $margin . 'px; margin-right:' . $margin . 'px;',
        '#mashfs-hidebtn' => 'background-color: ' . $bgcolor . ';color: #' . $sharecolor . ';',
        '#mashfs-total-shares' => 'color: #' . $sharecolor . ';',
        '#mashfs-main-a' => 'margin-bottom: ' . $marginbottom . 'px;',
    );

    if (!empty($styles[$class]))
        return 'style="' . $styles[$class] . '"';
}

/**
 * Return true if sidebar is enabled
 * 
 * @global array $mashsb_options
 * @return boolean true if sidebar is enabled
 */
function mashfs_is_active() {
    global $mashsb_options;

    if (is_404()) {
        return false;
    }

    // true if it is excluded
    if (mashfsbar_is_excluded()) {
        return false;
    }

    if (isset($mashsb_options['mashfs_pages'])) {
        $is_home = isset($mashsb_options['mashfs_pages']['home']) ? true : null;
        $is_single = isset($mashsb_options['mashfs_pages']['single']) ? true : null;
        $is_page = isset($mashsb_options['mashfs_pages']['page']) ? true : null;
        $is_category = isset($mashsb_options['mashfs_pages']['category']) ? true : null;

        // frontpage check
        if (!$is_home && is_front_page()) {
            return false;
        }

        if (
                $is_home && is_front_page() ||
                $is_category && !is_singular() ||
                $is_page && is_page() ||
                $is_single && is_single() && !is_front_page()
        ) {
            return true;
        }
    }
}

/**
 * Check if floating sidebar is excluded from specific page
 *
 * @return true if is excluded
 */
function mashfsbar_is_excluded() {
    global $post, $mashsb_options;
    // important 
    wp_reset_query();

    $excluded = isset($mashsb_options['mashfs_excluded']) ? $mashsb_options['mashfs_excluded'] : false;
    // Load scripts when page is not excluded
    if (strpos($excluded, ',') !== false) {
        $excluded = explode(',', $excluded);
        if (in_array($post->ID, $excluded)) {
            return true;
        }
    } else if ($post->ID == $excluded) {
        return true;
    }
}

/**
 * Get number of shares until share count is invisible
 *
 * @global array $mashsb_options
 * @return int int number of hide sharecount.
 */
function mashfs_get_hide_sharecount($shares) {
    global $mashsb_options;

    if (empty($mashsb_options['hide_sharecount'])) {
        return true;
    }

    if ($shares > $mashsb_options['hide_sharecount']) {
        return true;
    }

    return false;
}

/**
 * Create the mail link
 * 
 * @global array $mashsb_options
 * @param string $url url of the network
 * @param string $network network name
 *
 * @return string HTML
 */
function mashfs_get_mail_link($url, $network) {
    global $mashsb_options;

    
    $subject = mashfs_get_mail_subject();
    $body = mashfs_get_mail_body();

    //$current_url = get_home_url() . $_SERVER['REQUEST_URI'];
    $current_url = mashfs_get_current_url();

    $url = str_replace('$url', $current_url, $url);
    $url = str_replace('$title', $subject, $url);
    $url = str_replace('$body', $body, $url);
    $url = str_replace('+', '%20', $url);

    return '<a href="' . $url . '" class="mashicon-' . $network . ' mashfs-popup-share" ' . mashfsCreateInlineCSS('#mashfs-main-a') . '><span class="icon"></span></a>';
}

/**
 * Get mail body
 * @global array $mashsb_options
 * @global array $post
 * @return string
 */
function mashfs_get_mail_body() {
    global $mashsb_options, $post;

    $body = empty ($mashsb_options['mashfs_mail_body']) ? '' : $mashsb_options['mashfs_mail_body'];

 
    
    if (isset($body) && !empty($body)) {
        $body = trim($body);



        if (false !== strpos($body, '[excerpt]')) {
            $body = str_replace('[excerpt]', $post->post_excerpt, $body);
        }

        if (false !== strpos($body, '[mash_body]')) {
            $mash_og_description = get_post_meta($post->ID, 'mashsb_og_description', true);

            $body = str_replace('[mash_body]', $mash_og_description, $body);
        }

        $body = trim($body);
    }

    if (empty($body) && isset($mashsb_options['mashnet_bodytext']) && !empty($mashsb_options['mashnet_bodytext'])) {
        $body = trim($mashsb_options['mashnet_bodytext']);
    }

    if (empty($body)) {
        $body = 'Highly recommended article:';
    }

    return $body;
}

/**
 * Get Mail Subject
 * @global array $mashsb_options
 * @global array $post
 * @return string
 */
function mashfs_get_mail_subject() {
    global $mashsb_options, $post;

    $subject = empty($mashsb_options['mashfs_mail_subject']) ? '' : $mashsb_options['mashfs_mail_subject'];

    if (!isset($subject) || empty($subject)) {
        return $subject;
    }

    $title = trim(mashsb_get_title());

    if (empty($title)) {
        $title = $post->post_title;
    }

    $subject = str_replace('[title]', $mashsb_options['mashfs_mail_subject'], $title);

    return $subject;
}

/*
 * Get URL to share
 * 
 * @return url  $string
 * @scince 2.2.8
 */

function mashfs_get_current_url() {
    global $post;
    
        //$url = mashfs_get_main_url();

    if (is_singular()) {
        // The permalink for singular pages
        $url = mashfs_sanitize_url(get_permalink($post->ID));
    } else {
        // The main URL
        $url = mashfs_get_main_url();
    }
    return apply_filters('mashfs_get_url', $url);
}

/**
 * Return the current main url
 * 
 * @return mixed string|bool current url or false
 */
function mashfs_get_main_url() {
    global $wp;

    $url = home_url(add_query_arg(array(), $wp->request));
    if (!empty($url)) {
        return mashfs_sanitize_url($url);
    }
}

/**
 * Sanitize url and remove mashshare specific url parameters
 * 
 * @param string $url
 * @return string $url
 */
function mashfs_sanitize_url($url) {
    if (empty($url)) {
        return "";
    }

    $url1 = str_replace('?mashsb-refresh', '', $url);
    $url2 = str_replace('&mashsb-refresh', '', $url1);
    $url3 = str_replace('%26mashsb-refresh', '', $url2);

    return $url3;
}

/**
 * Get the twitter url
 * 
 * @return string twitter url
 */
function mashfs_get_twitter_url() {
    if (function_exists('mashsb_get_shorturl_singular')) {
        $url = mashsb_get_shorturl_singular(mashfs_get_current_url());
    } else if (function_exists('mashsuGetShortURL')) { // compatibility mode for MashShare earlier than 3.0
        $get_url = mashfs_get_current_url();
        $url = mashsuGetShortURL($get_url);
    } else {
        $url = mashfs_get_current_url();
    }
    return apply_filters('mashfs_get_twitter_url', $url);
}

/**
 * Return twitter custom title
 * 
 * @global object $mashsb_meta_tags
 * @changed 3.0.0
 * 
 * @return string the custom twitter title
 */
function mashfs_get_twitter_title() {

    global $mashsb_meta_tags, $post;
    // $mashsb_meta_tags is only available on singular pages
    if (isset($mashsb_meta_tags) && is_singular()) {
        $title = $mashsb_meta_tags->get_twitter_title();
        $title = html_entity_decode($title, ENT_QUOTES, 'UTF-8');
        $title = urlencode($title);
        $title = str_replace('#', '%23', $title);
        $title = esc_html($title);
    } else {
        $title = mashfs_get_document_title();
        $title = html_entity_decode($title, ENT_QUOTES, 'UTF-8');
        $title = urlencode($title);
        $title = str_replace('#', '%23', $title);
        $title = esc_html($title);
    }
    return apply_filters('mashfs_twitter_title', $title);
}

/**
 * Get share count for all pages where $post is empty. E.g. category or blog list pages
 * Uses transients 
 * 
 * @param string $url
 * @param int $cacheexpire
 *  
 * @returns integer $shares
 * 
 * @deprecated since version 1.1.7
 */
function mashfsGetNonPostShares_old($url) {
    global $post;

    /*
     * Try first to get the share count from $post->ID on singular pages
     */
    if (is_singular()) {
        //$shares = get_post_meta($post->ID, 'mashsb_shares', true);
        //return $shares + getFakecount();        
        return function_exists('getSharedcount') ? getSharedcount($url) : get_post_meta($post->ID, 'mashsb_shares', true) + getFakecount();
   }

    /*
     * Current page is not singular so
     * get and set shares via transient API because we
     * have no way to store the shares in get_post_meta on non singular pages
     */
    if (false === get_transient('mashcount_' . md5($url))) {
        // It wasn't there, so regenerate the data and save the transient
        // Get the share Object
        $mashsbSharesObj = mashsbGetShareObj($url);
        // Get the share counts object
        $mashsbShareCounts = mashsbGetShareMethod($mashsbSharesObj);
        $transient_name = md5($url);
        // Set the transient
        set_transient('mashcount_' . md5($url), $mashsbShareCounts->total, mashfs_get_cache_expiration());
        return $mashsbShareCounts->total + getFakecount();
    } else {
        $shares = get_transient('mashcount_' . md5($url));
        if (isset($shares) && is_numeric($shares)) {
            mashdebug()->info('Share count where $post is_null(): ' . $shares);
            return $shares + getFakecount();
        } else {
            return 0 + getFakecount(); // we need a result
        }
    }
}

/**
 * Get mashsb cache expiration time
 * 
 * @return int
 */
function mashfs_get_cache_expiration() {
    isset( $mashsb_options['mashsharer_cache'] ) ? $cacheexpire = $mashsb_options['mashsharer_cache'] : $cacheexpire = 300;
    /* make sure 300sec is default value */
    $cacheexpire < 300 ? $cacheexpire = 300 : $cacheexpire;

    if( isset( $mashsb_options['disable_cache'] ) ) {
        $cacheexpire = 2;
    }

    return $cacheexpire;
}

function mashfsGetNonPostShares_new($url) {
    global $mashsb_options, $post;

    // Get the result early from post if singular() to prevent duplicate execution
    if (is_singular()) {
        //return get_post_meta($post->ID, 'mashsb_shares', true) + getFakecount();
        return function_exists('getSharedcount') ? getSharedcount($url) : get_post_meta($post->ID, 'mashsb_shares', true) + getFakecount();
    }

    // Expiration
    $expiration = mashsb_get_expiration();

    // Remove variables, parameters and trailingslash
    $url_clean = mashsb_sanitize_url($url);

    // Get any existing copy of our transient data and fill the cache
    if (mashsb_force_cache_refresh()) {

        // Regenerate the data and save the transient
        // Get the share Object
        $mashsbSharesObj = mashsbGetShareObj($url_clean);
        // Get the share counts object
        $mashsbShareCounts = mashsbGetShareMethod($mashsbSharesObj);

        // Set the transient and return shares
        set_transient('mashcount_' . md5($url_clean), $mashsbShareCounts->total, $expiration);
        MASHSB()->logger->info('mashfsGetNonPostShares set_transient - shares:' . $mashsbShareCounts->total . ' url: ' . $url_clean);
        return $mashsbShareCounts->total + getFakecount();
    } else {
        // Get shares from transient cache

        $shares = get_transient('mashcount_' . md5($url_clean));

        if (isset($shares) && is_numeric($shares)) {
            MASHSB()->logger->info('mashfsGetNonPostShares() get shares from get_transient. URL: ' . $url_clean . ' SHARES: ' . $shares);
            return $shares + getFakecount();
        } else {
            return 0 + getFakecount(); // we need a result
        }
    }
}

/**
 * Returns document title for the current page.
 *
 * @since 3.0
 *
 * @global int $page  Page number of a single post.
 * @global int $paged Page number of a list of posts.
 *
 * @return string Tag with the document title.
 */
function mashfs_get_document_title() {
    // wp_get_document_title() exist since WP 4.4
    if (function_exists('wp_get_document_title')) {
        return wp_get_document_title();
    }

    /**
     * Filter the document title before it is generated.
     *
     * Passing a non-empty value will short-circuit wp_get_document_title(),
     * returning that value instead.
     *
     * @param string $title The document title. Default empty string.
     */
    $title = apply_filters('pre_get_document_title', '');
    if (!empty($title)) {
        return $title;
    }

    global $page, $paged;

    $title = array(
        'title' => '',
    );

    // If it's a 404 page, use a "Page not found" title.
    if (is_404()) {
        $title['title'] = __('Page not found');

        // If it's a search, use a dynamic search results title.
    } elseif (is_search()) {
        /* translators: %s: search phrase */
        $title['title'] = sprintf(__('Search Results for &#8220;%s&#8221;'), get_search_query());

        // If on the front page, use the site title.
    } elseif (is_front_page()) {
        $title['title'] = get_bloginfo('name', 'display');

        // If on a post type archive, use the post type archive title.
    } elseif (is_post_type_archive()) {
        $title['title'] = post_type_archive_title('', false);

        // If on a taxonomy archive, use the term title.
    } elseif (is_tax()) {
        $title['title'] = single_term_title('', false);

        /*
         * If we're on the blog page that is not the homepage or
         * a single post of any post type, use the post title.
         */
    } elseif (is_singular()) {
        $title['title'] = single_post_title('', false);

        // If on a category or tag archive, use the term title.
    } elseif (is_category() || is_tag()) {
        $title['title'] = single_term_title('', false);

        // If on an author archive, use the author's display name.
    } elseif (is_author() && $author = get_queried_object()) {
        $title['title'] = $author->display_name;

        // If it's a date archive, use the date as the title.
    } elseif (is_year()) {
        $title['title'] = get_the_date(_x('Y', 'yearly archives date format'));
    } elseif (is_month()) {
        $title['title'] = get_the_date(_x('F Y', 'monthly archives date format'));
    } elseif (is_day()) {
        $title['title'] = get_the_date();
    }

    // Add a page number if necessary.
    if (( $paged >= 2 || $page >= 2 ) && !is_404()) {
        $title['page'] = sprintf(__('Page %s'), max($paged, $page));
    }

    // Append the description or site title to give context.
    if (is_front_page()) {
        $title['tagline'] = get_bloginfo('description', 'display');
    } else {
        $title['site'] = get_bloginfo('name', 'display');
    }

    /**
     * Filter the separator for the document title.
     *
     * @since 4.4.0
     *
     * @param string $sep Document title separator. Default '-'.
     */
    $sep = apply_filters('document_title_separator', '-');

    /**
     * Filter the parts of the document title.
     *
     * @since 4.4.0
     *
     * @param array $title {
     *     The document title parts.
     *
     *     @type string $title   Title of the viewed page.
     *     @type string $page    Optional. Page number if paginated.
     *     @type string $tagline Optional. Site description when on home page.
     *     @type string $site    Optional. Site title when not on home page.
     * }
     */
    $title = apply_filters('document_title_parts', $title);

    $title = implode(" $sep ", array_filter($title));
    $title = wptexturize($title);
    $title = convert_chars($title);
    $title = esc_html($title);
    $title = capital_P_dangit($title);

    return $title;
}

/**
 * Get Pinterest image
 * 
 * @global obj $mashsb_meta_tags
 * @return string
 */
function mashfs_get_pinterest_image() {
    global $post, $mashsb_meta_tags;
    if (is_singular() && class_exists('MASHSB_HEADER_META_TAGS') && method_exists($mashsb_meta_tags, 'get_pinterest_image_url')) {
        $image = $mashsb_meta_tags->get_pinterest_image_url();
    } else {
        $image = function_exists('MASHOG') ? MASHOG()->MASHOG_OG_Output->_add_image() : mashsb_get_image($post->ID);
    }
    return $image;
}

/**
 * Get Pinterest description
 * 
 * @global obj $mashsb_meta_tags
 * @return type
 */
function mashfs_get_pinterest_desc() {
    global $post, $mashsb_meta_tags;
    if (is_singular() && class_exists('MASHSB_HEADER_META_TAGS') && method_exists($mashsb_meta_tags, 'get_pinterest_description')) {
        global $mashsb_meta_tags;
        return $mashsb_meta_tags->get_pinterest_description();
    } else {
        return mashsb_get_excerpt_by_id($post);
    }
}