<?php
/**
 * Plugin Name: Subdomain Pro
 * Plugin URI: http://adroittechnosoft.com/subdomain-pro
 * Description: This plugin can converts post and pages in sub-domains.
 * Version: 2.0.0
 * Author: Adroit Technosoft
 * Author URI: http://adroittechnosoft.com
 * License: GPL
 */
 // tested on 4.2.7
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('subdomain_pro') ) :

class subdomain_pro {
	
	// vars
	var $slug_name,$old_rules;
		
	
	/*
	*  __construct
	*
	*  A dummy constructor to ensure subdomain_pro is only initialized once
	*
	*/
	function __construct() {
		
		/* Do nothing here */
		
	}
	
	
	/*
	*  initialize
	*
	*  The real constructor to initialize subdomain_pro
	*
	*/
	function initialize() {		
			
		// actions/filters
		add_action( 'init',					array($this, 'wp_init'), 5);
		add_filter( 'page_rewrite_rules',	array($this,'pps_rewrite_rules') ); // for pages only
        add_filter( 'post_rewrite_rules',	array($this,'pps_rewrite_rules') ); // for posts/custom posts
        add_filter( 'option_rewrite_rules',	array($this,'pps_option_rewrite_rules') ); // for taxonomies
		add_filter( 'page_link', 			array($this,'pps_page_link'), 100, 2 ); // for pages only
		add_filter( 'post_link', 			array($this,'pps_page_link'), 100, 2 ); // for posts
		add_filter( 'post_type_link', 		array($this,'pps_page_link'), 100, 2 ); // custom posts
		add_filter( 'term_link', 			array($this,'pps_term_link'), 100, 3 ); // for taxonomies
		add_action( 'add_meta_boxes',		array($this, 'wp_add_meta_boxes'), 10, 2 );
		add_action( 'save_post',    		array($this, 'wp_save_post'), 5);
		//add_filter( 'allowed_redirect_hosts',array($this,'pps_allowed_redirect_hosts'), 100, 2 );
	}
	
	/*
	*  wp_init
	*
	*  This function will run on the WP init action and setup many things
	*
	*/
	function wp_init() {	
	
		if (!is_admin()) {

			$this->old_rules = get_option('rewrite_rules');
		
			// Stuff changed in WP 2.8
			if (function_exists('set_transient')) {
				set_transient('rewrite_rules', "");
				update_option('rewrite_rules', "");
			} else {
				update_option('rewrite_rules', "");
			}

		}
		
	}
	
	/*
	*  pps_option_rewrite_rules
	*
	*  This function will return new rewrite rules while accessing the taxonomy/category
	*
	*/
	function pps_option_rewrite_rules($rules){ 
	$tax_args = $this->pps_get_taxonomy_arg(); 
	//print_r($tax_args);
	if(is_array($tax_args) && count($tax_args) > 1){
	$this->slug_name = $tax_args[0];
	$taxonomy_name = $tax_args[1];
	if(!taxonomy_exists($taxonomy_name))
		return $rules;
	if($taxonomy_name == 'category')
	$taxonomy_name = 'category_name';
	//$taxonomy_details = get_taxonomy( $this->slug_name );
	//print_r($taxonomy_details);
	//$category = get_term_by('slug', $this->slug_name, $taxonomy_name);
	//print_r($category);
	$rules["feed/(feed|rdf|rss|rss2|atom)/?$"]	= "index.php?$taxonomy_name=" . $this->slug_name . "&feed=\$matches[1]";
	$rules["(feed|rdf|rss|rss2|atom)/?$"]		= "index.php?$taxonomy_name=" . $this->slug_name . "&feed=\$matches[1]";
	
	$rules2 = array();
	$rules2["$"]							= "index.php?$taxonomy_name=" . $this->slug_name;
	$rules2["page/?([0-9]{1,})/?$"]			= "index.php?$taxonomy_name=" . $this->slug_name. "&paged=\$matches[1]";
	return  $rules2;
	}
	return  $rules;
	}
	
	
	/*
	*  pps_rewrite_rules
	*
	*  This function will return new rewrite rules while accessing the page/post
	*
	*/
	function pps_rewrite_rules($rules){ 
		global $wp_version;
		//return $rules;
		
		$this->slug_name = $this->pps_get_formate_url();
		$posttypes = $this->get_enabled_post_types();
	   
		if(empty($this->slug_name))
			return $rules;
		if((int)$wp_version <= 3) {// old version code 3.x.x
			foreach($posttypes as $post_type) { 
				$post = get_page_by_path($this->slug_name, OBJECT, $post_type);
				if(is_object($post)) break;
			}
		}else{
			$post = get_page_by_path($this->slug_name, OBJECT, $posttypes);
		}
		
		if(!is_object($post))
			return $rules;
		
		$post_id = $post->ID;
		$isnotsubdomain = get_post_meta( $post_id, '_subdomain_pro', true ); //die();
		$frontpage_id = get_option('page_on_front');
		
		$newrules = array();

		foreach((array)$this->old_rules as $key=>$rule){
			$newrules[$key] = $rule;
		}
		
		if($frontpage_id == $post_id || $isnotsubdomain){ 			// redirect to 404 page if not found
			$newrules["$"] = "index.php?pagename=fourzeofour";
			$rules = $newrules + $rules;
			return $rules;
		}
		
		$newrules["$"] = '';
		
		if(get_post_type($post_id) == 'post'){ 						// if post found
			$newrules["$"] = "index.php?p=".$post_id;
		}elseif(get_post_type($post_id) == 'page'){ 				// if page found
			$newrules["$"] = "index.php?pagename=".$this->slug_name;
		}else{														// if custom posttype found
			foreach($posttypes as $type) 
			$newrules["$"] = "index.php?$type=".$this->slug_name;
		}
		
		//echo $newrules["$"]; die();
		//$newrules["$"] = "index.php?p=".$post_id;
		$rules = $newrules + $rules;
		return $newrules;
	}

	/*
	*  pps_get_formate_url
	*
	*  This function will return and set slug name for linking page/post
	*
	*/
	function pps_get_taxonomy_arg(){
		//global $sitePress; // may be used for wpml plugin
		
		$url = $_SERVER['HTTP_HOST'];
		$arg = array();
		$domain = explode( ".", $url );
		if(isset($domain[0]))
		$arg[] = $domain[0];
		if(isset($domain[1]))
		$arg[] = $domain[1];
		return $arg;
	}
	/*
	*  pps_get_formate_url
	*
	*  This function will return and set slug name for linking page/post
	*
	*/
	function pps_get_formate_url(){
		//global $sitePress; // may be used for wpml plugin
		
		$url = $_SERVER['HTTP_HOST'];

		$domain = explode( ".", $url );
		if(isset($domain[0]))
		$this->slug_name = $domain[0];
	
		/*if(function_exists('idn_to_utf8')) // may be used for wpml plugin
		$this->slug_name = idn_to_utf8($domain[0]);
		else
		$this->slug_name = IDN::decodeIDN($domain[0]);*/
	
		return $this->slug_name;
    }
	
	/*
	*  get_enabled_post_types
	*
	*  This function will return array of post types for which plugin should make sub-domain
	*
	*/
	function get_enabled_post_types(){
		$post_types = array();
		$disabled = (array)get_option('subdomain_pro_posttypes');
		if(get_option('s_p_disable_post') && get_option('s_p_disable_page')){
			return $post_types;
		}
		if(get_option('s_p_disable_page')){
			$disabled[] = 'page';
		}
		if(get_option('s_p_disable_post')){
			$disabled = get_post_types(array('public'=>true));
		}
		
		foreach(get_post_types(array('public'=>true)) as $post_type) { 
			if($post_type == 'attachment') continue;
			if(!in_array($post_type, $disabled))
			$post_types[] = $post_type;
		}
		//print_r($disabled);
		//print_r($post_types);
		return $post_types;
	}
	
	
	/*
	*  get_enabled_taxonomies
	*
	*  This function will return array of taxonomies for which plugin should make sub-domain
	*
	*/
	function get_enabled_taxonomies(){
		$en_taxonomies = array();
		if(get_option('s_p_disable_taxonomies')){
			return $en_taxonomies;
		}
		$disabled = (array)get_option('subdomain_pro_taxonomies');
		// checking default category
		$slug = 'category';
		if(!in_array($slug, $disabled))
			$en_taxonomies[] = $slug;
		$args = array(
			'public'   => true,
			'_builtin' => false
		); 
		
		// checking custom taxonomies
		foreach(get_taxonomies($args,'objects')  as $slug=>$taxonomy) {
			if(!in_array($slug, $disabled))
			$en_taxonomies[] = $slug;
		}
		
		return $en_taxonomies;
	}
	
	/*
	*  pps_page_link
	*
	*  This hook function will change the permalink everywhere in the site
	*
	*/
	function pps_term_link( $termlink, $term, $taxonomy ){ 
		$force_ebale = get_option('subdomain_pro_force_cate');
		$force_ebale_arr = (array)explode(',',$force_ebale);
		$this_force_enable = false;
		if(in_array($term->term_id,$force_ebale_arr)){
			$this_force_enable = true;
		}
		$terms = $this->get_enabled_taxonomies();
		if(!in_array($taxonomy, $terms) && $this_force_enable == false){
			return $termlink;
		}
		$original_link = $termlink; 
		$parts = parse_url($original_link);
		$req_link = '';
		if(isset($parts['query']))
		$req_link = $parts['query']; 
		$slug_name = $term->slug;
		//create url
		$site_url = get_site_url(); 
		$new_link = str_replace('http://', '', $site_url);
		$new_link = str_replace('https://', '', $new_link);
		$new_link = str_replace('www.', '', $new_link);
		$new_link = str_replace($slug_name, '', $new_link);
		$new_link = str_replace($taxonomy, '', $new_link);
		$protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
		if($req_link)
			$new_link = $protocol.$slug_name.'.'.$taxonomy.'.'.$new_link.'?'.$req_link;
		else
			$new_link = $protocol.$slug_name.'.'.$taxonomy.'.'.$new_link;
	//	return "$slug_name.$taxonomy".home_url();
		return $new_link;
	}
	
	
	function pps_page_link( $link, $id ){
		
		$original_link = $link; 
		$parts = parse_url($original_link);
		$req_link = '';
		if(isset($parts['query']))
		$req_link = $parts['query']; 
		
		$site_url = get_site_url(); 

		$link = str_replace($site_url, '', $link);
		
		$link = str_replace('www.','',$link);
		
		if($req_link){
		$link = str_replace($req_link,'',$link);
		$link = str_replace('?','',$link);
		}
		
		$posttypes = $this->get_enabled_post_types();
		
		if(is_object($id)){  
			$expo = explode('/', $link);
			$expo = array_filter( $expo );
			$reversed = array_reverse($expo);
			$new_slug = $this->slug_name = implode('.',$reversed);
			$post = $id; 
		}elseif($id){
			$expo = explode('/', $link);
			$expo = array_filter( $expo );
			$reversed = array_reverse($expo);
			if(isset($reversed[0]))
			$new_slug = $reversed[0];
			$new_slug = $this->slug_name = implode('.',$reversed);
			$post = get_post( $id ); 
		}elseif(isset($_GET['post'])){
			$expo = explode('/', $link);
			$expo = array_filter( $expo );
			$reversed = array_reverse($expo);
			if(isset($reversed[0]))
			$new_slug = $reversed[0];
			$this->slug_name = implode('.',$reversed);
			$post = get_post( $_GET['post'] ); 
		}else{
			$post = get_page_by_path($this->slug_name, OBJECT, $posttypes);
		}
		
		$force_ebale = get_option('subdomain_pro_force_post');
		$force_ebale_arr = (array)explode(',',$force_ebale);
		$this_force_enable = false;
		if(in_array($post->ID,$force_ebale_arr)){
			$this_force_enable = true;
		}
		$suffix = ($post->post_type == 'page')?'_page':'';
		if($date = get_option('subdomain_pro_date'.$suffix) && $this_force_enable == false){
			$pfx_date = get_the_date( 'Y-m-d', $post->ID );
			//echo $date .'>= '.$pfx_date;
			if($date >= $pfx_date){
				return $original_link;
			}
		}
		
		//create url
		$new_link = str_replace('http://', '', $site_url);
		$new_link = str_replace('https://', '', $new_link);
		$new_link = str_replace('www.', '', $new_link);
		$new_link = str_replace($this->slug_name, '', $new_link);
		$protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
		if($req_link)
			$new_link = $protocol.$new_slug.'.'.$new_link.'?'.$req_link;
		else
			$new_link = $protocol.$new_slug.'.'.$new_link;
		
		$isnotsubdomain = get_post_meta( $post->ID, '_subdomain_pro', true );

		$selected = in_array($post->post_type,(array)$posttypes)?true:false;
		if(($isnotsubdomain == 1 || !$selected) && $this_force_enable == false)
			return $original_link;
		
		return $new_link;
	}
	
	
	/*
	*  pps_page_link
	*
	*  This function will add a meta box for custom setting with each posts and pages.
	*
	*/
	function wp_add_meta_boxes() {
		//$screens = array( 'post', 'page', 'products');
		$screens = $this->get_enabled_post_types();
		foreach ( $screens as $screen ) {
			
			add_meta_box('pps_pagemeta', __( 'Post-Page Subdomain Pro Settings', 'subdomain_pro' ),array($this, 'wp_post_page_meta_box_callback'),$screen, "side", "high", null);
			
		}
	}
	
	/**
	 * Prints the box content.
	 * 
	 * @param WP_Post $post The object for the current post/page.
	 */
	function wp_post_page_meta_box_callback( $post ) {

		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'wp_subdomain_pro', 'subdomain_pro' );

		$value = get_post_meta( $post->ID, '_subdomain_pro', true );

		echo '<label for="_subdomain_pro">';
		_e( 'Not a subdomain this page/post', 'subdomain_pro' );
		echo '</label> ';
		echo '<input type="checkbox" id="_subdomain_pro" name="_subdomain_pro" value="1" '.( $value == 1 ? 'checked' : '').' />';
	}

	/**
	 * When the post is saved, saves our custom data.
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	function wp_save_post( $post_id ) {
		 
		// Default Value
		add_post_meta($post_id, '_subdomain_pro', 0, true);
		
		// Check if our nonce is set.
		if ( ! isset( $_POST['_subdomain_pro'] ) ) {
			return;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST['_subdomain_pro'], 'wp_subdomain_pro' ) ) {
			return;
		}

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check the user's permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		
		// OK, it's safe for us to save the data now.
		
		// Sanitize user input.
		$my_data = sanitize_text_field( $_POST['_subdomain_pro'] );

		// Update the meta field in the database.
		update_post_meta( $post_id, '_subdomain_pro', $my_data );
		
	}

	/*function pps_allowed_redirect_hosts($allowed_hosts, $hosts) {
		$haystack = $hosts;
		foreach($allowed_hosts as $inhosts){
			$needle = $inhosts;
			$check = $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
			$allowed_hosts[] = $hosts;
		}
			return $allowed_hosts;
	}*/
}


/*
*  subdomain_pro
*
*  The main function responsible for returning the one true subdomain_pro Instance to functions everywhere.
*  Use this function like you would a global variable, except without needing to declare the global.
*
*  Example: <?php $subdomain_pro = subdomain_pro(); ?>
*
*  @type	function
*/

function subdomain_pro() {

	global $subdomain_pro;
	
	if( !isset($subdomain_pro) ) {
	
		$subdomain_pro = new subdomain_pro();
		
		$subdomain_pro->initialize();
		
	}
	
	return $subdomain_pro;
}


// initialize
subdomain_pro();


endif; // class_exists check


/*
Lets Use the Login Cookie
*/

# OK, so we rock up and setup a constant....
define('ROOT_COOKIE', '/' );

# Then we paste the WP functions from /wp-includes/pluggable.php
# ...
# and to finish we replace COOKIEPATH, PLUGINS_COOKIE_PATH  and ADMIN_COOKIE_PATH with ROOT_COOKIE, job done!

if ( !function_exists('wp_set_auth_cookie') ) :
/**
 * Sets the authentication cookies based User ID.
 *
 * The $remember parameter increases the time that the cookie will be kept. The
 * default the cookie is kept without remembering is two days. When $remember is
 * set, the cookies will be kept for 14 days or two weeks.
 *
 * @since 2.5
 *
 * @param int $user_id User ID
 * @param bool $remember Whether to remember the user
 */
	function wp_set_auth_cookie($user_id, $remember = false, $secure = '') {
		if ( $remember ) {
			$expiration = $expire = time() + apply_filters('auth_cookie_expiration', 1209600, $user_id, $remember);
		} else {
			$expiration = time() + apply_filters('auth_cookie_expiration', 172800, $user_id, $remember);
			$expire = 0;
		}
		
		if ( '' === $secure )
			$secure = is_ssl();		

	if ( $secure ) {
		$auth_cookie_name = SECURE_AUTH_COOKIE;
		$scheme = 'secure_auth';
	} else {
		$auth_cookie_name = AUTH_COOKIE;
		$scheme = 'auth';
	}

	$auth_cookie = wp_generate_auth_cookie($user_id, $expiration, $scheme);
	$logged_in_cookie = wp_generate_auth_cookie($user_id, $expiration, 'logged_in');

	do_action('set_auth_cookie', $auth_cookie, $expire, $expiration, $user_id, $scheme);
	do_action('set_logged_in_cookie', $logged_in_cookie, $expire, $expiration, $user_id, 'logged_in');
	
	$subdomain = get_option('subdomain_pro_subdomain');

	if($subdomain==1)
		{
			# Use Scotts implementation
			$info = get_bloginfo('url');
			$info = parse_url($info);
			$info = $info['host'];
			$exp = explode('.',$info);
			if(count($exp)==3){$domain = '.'.$exp[1].'.'.$exp[2];}
			elseif(count($exp)==2){$domain = '.'.$info;}
			elseif(count($exp) > 3){$exp = array_reverse($exp); $domain = '.'.$exp[1].'.'.$exp[0];}
			else{$domain = COOKIE_DOMAIN;}
		}
	else
		{
			# Default
			$domain = COOKIE_DOMAIN;
	}
	$secure_logged_in_cookie = '';
	setcookie($auth_cookie_name, $auth_cookie, $expire, ROOT_COOKIE, $domain, $secure, true);
	/** Duplicate of above - Created by Find & Replace
	setcookie($auth_cookie_name, $auth_cookie, $expire, ROOT_COOKIE, $domain, $secure, true);
	 **/
	setcookie(LOGGED_IN_COOKIE, $logged_in_cookie, $expire, ROOT_COOKIE, $domain, $secure_logged_in_cookie, true);
	if ( COOKIEPATH != SITECOOKIEPATH )
		setcookie(LOGGED_IN_COOKIE, $logged_in_cookie, $expire, SITECOOKIEPATH, COOKIE_DOMAIN, $secure_logged_in_cookie, true);
}

endif;

if ( !function_exists('wp_clear_auth_cookie') ) :
/**
 * Removes all of the cookies associated with authentication.
 *
 * @since 2.5
 */
function wp_clear_auth_cookie() {
	do_action('clear_auth_cookie');
	
	$subdomain = get_option('subdomain_pro_subdomain');

	# As ABOVE!
	if($subdomain==1)
	{
		$info = get_bloginfo('url');
		$info = parse_url($info);
		$info = $info['host'];
		$exp = explode('.',$info);
		if(count($exp)==3){$domain = '.'.$exp[1].'.'.$exp[2];}
		elseif(count($exp)==2){$domain = '.'.$info;}
		elseif(count($exp) > 3){$exp = array_reverse($exp); $domain = '.'.$exp[1].'.'.$exp[0];}
		else{$domain = COOKIE_DOMAIN;}
	}
	else
	{
		$domain = COOKIE_DOMAIN;
	}

	/** Clear All possible cookies **/

	setcookie(AUTH_COOKIE, ' ', time() - 31536000, ADMIN_COOKIE_PATH, COOKIE_DOMAIN);
	setcookie(AUTH_COOKIE, ' ', time() - 31536000, ROOT_COOKIE, COOKIE_DOMAIN);
	setcookie(AUTH_COOKIE, ' ', time() - 31536000, ROOT_COOKIE, $domain);
	setcookie(AUTH_COOKIE, ' ', time() - 31536000, ADMIN_COOKIE_PATH, $domain);
	
	setcookie(SECURE_AUTH_COOKIE, ' ', time() - 31536000, ADMIN_COOKIE_PATH, COOKIE_DOMAIN);
	setcookie(SECURE_AUTH_COOKIE, ' ', time() - 31536000, ROOT_COOKIE, COOKIE_DOMAIN);
	setcookie(SECURE_AUTH_COOKIE, ' ', time() - 31536000, ROOT_COOKIE, $domain);
	setcookie(SECURE_AUTH_COOKIE, ' ', time() - 31536000, ADMIN_COOKIE_PATH, $domain);

	setcookie(AUTH_COOKIE, ' ', time() - 31536000, PLUGINS_COOKIE_PATH, COOKIE_DOMAIN);
	setcookie(AUTH_COOKIE, ' ', time() - 31536000, PLUGINS_COOKIE_PATH, $domain);
	
	setcookie(SECURE_AUTH_COOKIE, ' ', time() - 31536000, PLUGINS_COOKIE_PATH, COOKIE_DOMAIN);
	setcookie(SECURE_AUTH_COOKIE, ' ', time() - 31536000, PLUGINS_COOKIE_PATH, $domain);
	
	setcookie(LOGGED_IN_COOKIE, ' ', time() - 31536000, COOKIEPATH, COOKIE_DOMAIN);
	setcookie(LOGGED_IN_COOKIE, ' ', time() - 31536000, ROOT_COOKIE, COOKIE_DOMAIN);
	setcookie(LOGGED_IN_COOKIE, ' ', time() - 31536000, ROOT_COOKIE, $domain);
	setcookie(LOGGED_IN_COOKIE, ' ', time() - 31536000, COOKIEPATH, $domain);
	
	setcookie(LOGGED_IN_COOKIE, ' ', time() - 31536000, SITECOOKIEPATH, COOKIE_DOMAIN);
	setcookie(LOGGED_IN_COOKIE, ' ', time() - 31536000, SITECOOKIEPATH, $domain);
	
	// Old cookies
	setcookie(AUTH_COOKIE, ' ', time() - 31536000, COOKIEPATH, COOKIE_DOMAIN);
	setcookie(AUTH_COOKIE, ' ', time() - 31536000, ROOT_COOKIE, COOKIE_DOMAIN);
	setcookie(AUTH_COOKIE, ' ', time() - 31536000, ROOT_COOKIE, $domain);
	setcookie(AUTH_COOKIE, ' ', time() - 31536000, COOKIEPATH, $domain);
	
	setcookie(AUTH_COOKIE, ' ', time() - 31536000, SITECOOKIEPATH, COOKIE_DOMAIN);
	setcookie(AUTH_COOKIE, ' ', time() - 31536000, SITECOOKIEPATH, $domain);

	setcookie(SECURE_AUTH_COOKIE, ' ', time() - 31536000, COOKIEPATH, COOKIE_DOMAIN);
	setcookie(SECURE_AUTH_COOKIE, ' ', time() - 31536000, ROOT_COOKIE, COOKIE_DOMAIN);
	setcookie(SECURE_AUTH_COOKIE, ' ', time() - 31536000, ROOT_COOKIE, $domain);
	setcookie(SECURE_AUTH_COOKIE, ' ', time() - 31536000, COOKIEPATH, $domain);
	
	setcookie(SECURE_AUTH_COOKIE, ' ', time() - 31536000, SITECOOKIEPATH, COOKIE_DOMAIN);
	setcookie(SECURE_AUTH_COOKIE, ' ', time() - 31536000, SITECOOKIEPATH, $domain);
	
	// Even older cookies
	setcookie(USER_COOKIE, ' ', time() - 31536000, COOKIEPATH, COOKIE_DOMAIN);
	setcookie(USER_COOKIE, ' ', time() - 31536000, ROOT_COOKIE, COOKIE_DOMAIN);
	setcookie(USER_COOKIE, ' ', time() - 31536000, ROOT_COOKIE, $domain);
	setcookie(USER_COOKIE, ' ', time() - 31536000, COOKIEPATH, $domain);
	
	setcookie(PASS_COOKIE, ' ', time() - 31536000, COOKIEPATH, COOKIE_DOMAIN);
	setcookie(PASS_COOKIE, ' ', time() - 31536000, ROOT_COOKIE, COOKIE_DOMAIN);
	setcookie(PASS_COOKIE, ' ', time() - 31536000, ROOT_COOKIE, $domain);
	setcookie(PASS_COOKIE, ' ', time() - 31536000, COOKIEPATH, $domain);
	
	setcookie(USER_COOKIE, ' ', time() - 31536000, SITECOOKIEPATH, COOKIE_DOMAIN);
	setcookie(USER_COOKIE, ' ', time() - 31536000, SITECOOKIEPATH, $domain);

	setcookie(PASS_COOKIE, ' ', time() - 31536000, SITECOOKIEPATH, COOKIE_DOMAIN);
	setcookie(PASS_COOKIE, ' ', time() - 31536000, SITECOOKIEPATH, $domain);

}
endif;
register_activation_hook( __FILE__,'subdomain_pro_activate');
function subdomain_pro_activate ()
	{
		$opt_val = get_option('subdomain_pro_subdomain');
		if($opt_val!=1)
			{
				delete_option('subdomain_pro_subdomain');
				add_option('subdomain_pro_subdomain',0);
			}
	}
function subdomain_pro_menu ()
	{
		global $subdomain_pro_admin_hook;
		
		$subdomain_pro_admin_hook = add_options_page('SubDomain settings', 'Subdomain settings', 'manage_options' , 'subdomain-cookie', 'subdomain_pro_options');
	}

function subdomain_pro_options ()
	{
	
	// Read in existing option value from database
	if(isset($_POST['Submit']))
	{
		if(isset($_POST['subdomain_pro_subdomain']))
		update_option('subdomain_pro_subdomain', $_POST['subdomain_pro_subdomain'] );
		if(isset($_POST['subdomain_pro_posttypes']))
		update_option('subdomain_pro_posttypes', $_POST['subdomain_pro_posttypes'] );
		if(isset($_POST['subdomain_pro_taxonomies']))
		update_option('subdomain_pro_taxonomies', $_POST['subdomain_pro_taxonomies'] );
	
		if(isset($_POST['s_p_disable_post']))
		update_option('s_p_disable_post', $_POST['s_p_disable_post'] );
		if(isset($_POST['s_p_disable_page']))
		update_option('s_p_disable_page', $_POST['s_p_disable_page'] );
		if(isset($_POST['s_p_disable_taxonomies']))
		update_option('s_p_disable_taxonomies', $_POST['s_p_disable_taxonomies'] );
		if(isset($_POST['subdomain_pro_date']))
		update_option('subdomain_pro_date', $_POST['subdomain_pro_date'] );
		if(isset($_POST['subdomain_pro_date_page']))
		update_option('subdomain_pro_date_page', $_POST['subdomain_pro_date_page'] );
		if(isset($_POST['subdomain_pro_force_post']))
		update_option('subdomain_pro_force_post', $_POST['subdomain_pro_force_post'] );
		if(isset($_POST['subdomain_pro_force_cate']))
		update_option('subdomain_pro_force_cate', $_POST['subdomain_pro_force_cate'] );
		echo '<div class="updated"><p><strong>'._('Options saved.').'</strong></p></div>';
	}

				
				

?>
<div class="wrap">
<?php echo "<h2>" . __( 'Subdomain Options', 'subdomain_pro' ) . "</h2>"; ?>
<form name="plugin_options" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
<input type="hidden" name="subdomain_pro_submit_hidden" value="Y" />

<p>

<table class="form-table">
<?php
 $subdomain_pro_posttypes = get_option('subdomain_pro_posttypes'); 
 $subdomain_pro_subdomain = get_option('subdomain_pro_subdomain'); 
 $subdomain_pro_taxonomies = get_option('subdomain_pro_taxonomies'); 
 $s_p_disable_post = get_option('s_p_disable_post'); 
 $s_p_disable_page = get_option('s_p_disable_page'); 
 $s_p_disable_taxonomies  = get_option('s_p_disable_taxonomies'); 
 $subdomain_pro_date = get_option('subdomain_pro_date'); 
 $subdomain_pro_force_post = get_option('subdomain_pro_force_post'); 
 $subdomain_pro_force_cate = get_option('subdomain_pro_force_cate'); 
 $subdomain_pro_date_page = get_option('subdomain_pro_date_page'); 
?>
<tr valign="top">
	<th scope="row"><?php _e("Allow Cookies to go across All Subdomains:", 'subdomain_pro' ); ?> </th>
	<td>
	<input type="hidden" name="subdomain_pro_subdomain" value="0"/>
	<input type="checkbox" name="subdomain_pro_subdomain" value="1" <?php if($subdomain_pro_subdomain){echo " CHECKED";} ?> /><span class="description"> <?php _e("Tick this box and we'll try to <b>guess</b> your domain and enable the cookie.", 'subdomain_pro' );?></span></td>
</tr>
<tr valign="top"><td><hr></td><td><hr></td></tr>
<tr valign="top">
	<th scope="row"><?php _e("Disable for all posts/custom posts:", 'subdomain_pro' ); ?> </th>
	<td>
	<input type="hidden" name="s_p_disable_post" value="0"/>
	<input type="checkbox" id="s_p_disable_post" name="s_p_disable_post" value="1" <?php if($s_p_disable_post){echo " CHECKED";} ?> /><span class="description"> <?php _e("Tick this box to disable to convert subdomain for all posts and custom posts", 'subdomain_pro' );?></span></td>
</tr>

<script>
document.getElementById('s_p_disable_post').addEventListener('change', function() {
    if (!this.checked) {
        document.getElementById('subdomain_pro_posttypes').disabled = false;
    } else {
        document.getElementById('subdomain_pro_posttypes').disabled = true;
    }
});
</script>
<tr valign="top">
	<th scope="row"><?php _e("Disable post-types", 'subdomain_pro' ); ?> </th>
	<?php $disabled = ($s_p_disable_post)?'disabled="disabled"':''; ?>
	<td><select style="width: 150px;" id="subdomain_pro_posttypes" name="subdomain_pro_posttypes[]" <?php echo $disabled;?>multiple>
	<option value=""><?php _e("None", 'subdomain_pro' );?></option>
	<?php 
	//print_r(get_post_types(array('public'=>true))); 
	foreach(get_post_types(array('public'=>true)) as $post_type) { 
	if($post_type == 'attachment' || $post_type == 'page') continue;
	 $selected = in_array($post_type,(array)$subdomain_pro_posttypes)?'selected':'';
	?>
	<option value="<?php echo $post_type;?>" <?php echo $selected;?>><?php echo $post_type;?></option>
	<?php } ?>
	</select>
	</td>
</tr>
<tr valign="top">
	<th scope="row"><?php _e("Disable posts(custom posts) before date", 'subdomain_pro' ); ?> </th>
	<td><input type="date" value="<?php echo $subdomain_pro_date;?>" name="subdomain_pro_date"><span class="description"> <?php _e("Left blank if allow for all posts(custom posts)", 'subdomain_pro' );?></span></td>
</tr>
<tr valign="top">
	<th scope="row"><?php _e("Force enable pages/posts/custom posts IDs", 'subdomain_pro' ); ?> </th>
	<td><input type="text" value="<?php echo $subdomain_pro_force_post;?>" name="subdomain_pro_force_post"><span class="description"> <?php _e("User comma seperated id here like eg. 95,78,99,...", 'subdomain_pro' );?></span></td>
</tr>
<tr valign="top"><td><hr></td><td><hr></td></tr>
<tr valign="top">
	<th scope="row"><?php _e("Disable for all pages:", 'subdomain_pro' ); ?> </th>
	<td>
	<input type="hidden" name="s_p_disable_page" value="0"/>
	<input type="checkbox" id="s_p_disable_page" name="s_p_disable_page" value="1" <?php if($s_p_disable_page){echo " CHECKED";} ?> /><span class="description"> <?php _e("Tick this box to disable to convert subdomain for all pages", 'subdomain_pro' );?></span></td>
</tr>
<tr valign="top">
	<th scope="row"><?php _e("Disable pages before date", 'subdomain_pro' ); ?> </th>
	<td><input type="date" value="<?php echo $subdomain_pro_date_page;?>" name="subdomain_pro_date_page"><span class="description"> <?php _e("Left blank if allow for all pages", 'subdomain_pro' );?></span></td>
</tr>

<tr valign="top"><td><hr></td><td><hr></td></tr>
<tr valign="top">
	<th scope="row"><?php _e("Disable for all categories/taxonomies:", 'subdomain_pro' ); ?> </th>
	<td>
	<input type="hidden" name="s_p_disable_taxonomies" value="0"/>
	<input type="checkbox" id="s_p_disable_taxonomies" name="s_p_disable_taxonomies" value="1" <?php if($s_p_disable_taxonomies){echo " CHECKED";} ?> /><span class="description"> <?php _e("Tick this box to disable to convert subdomain for all categories and taxonomies", 'subdomain_pro' );?></span></td>
</tr>
<script>
document.getElementById('s_p_disable_taxonomies').addEventListener('change', function() {
    if (!this.checked) {
        document.getElementById('subdomain_pro_taxonomies').disabled = false;
    } else {
        document.getElementById('subdomain_pro_taxonomies').disabled = true;
    }
});
</script>
<tr valign="top">
	<th scope="row"><?php _e("Disable Taxonomies", 'subdomain_pro' ); ?> </th>
	<?php $disabled = ($s_p_disable_taxonomies)?'disabled="disabled"':''; ?>
	<td><select style="width: 150px;" id="subdomain_pro_taxonomies" name="subdomain_pro_taxonomies[]" <?php echo $disabled;?>multiple>
	<option value=""><?php _e("None", 'subdomain_pro' );?></option>
	 <?php $selected = in_array('category',(array)$subdomain_pro_taxonomies)?'selected':''; ?>
	<option value="category" <?php echo $selected;?>><?php _e("Category", 'subdomain_pro' );?></option>
	<?php 
	//print_r(get_taxonomies(array(),'objects'));  die();
	$args = array(
		'public'   => true,
		'_builtin' => false
	); 
	foreach(get_taxonomies($args,'objects') as $slug=>$taxonomy) { 
	 $selected = in_array($slug,(array)$subdomain_pro_taxonomies)?'selected':'';
	?>
	<option value="<?php echo $slug;?>" <?php echo $selected;?>><?php echo $taxonomy->name;?></option>
	<?php } ?>
	</select>
	</td>
</tr>
<tr valign="top">
	<th scope="row"><?php _e("Force enable cat/term IDs", 'subdomain_pro' ); ?> </th>
	<td><input type="text" value="<?php echo $subdomain_pro_force_cate;?>" name="subdomain_pro_force_cate"><span class="description"> <?php _e("User comma seperated id here like eg. 95,78,99,...", 'subdomain_pro' );?></span></td>
</tr>
</table>

</p><hr />
<p class="submit">
<input type="submit" name="Submit" value="<?php _e('Update Options', 'subdomain_pro' ) ?>" />
</p>
</form>
</div>
<?php
}

add_action( 'init', 'subdomain_pro_init', 5);
// Run all actions and hooks at the end to keep it tidy
function subdomain_pro_init(){
	if (is_admin()) { // only run admin stuff if we're an admin.
		add_action('admin_menu', 'subdomain_pro_menu');
	}
}	
