<?php

class pvita_whatsapp_redirect_plugin{
	public function __construct(){
		add_filter('query_vars', array($this, 'add_query_vars'), 0);
		add_action('parse_request', array($this, 'sniff_requests'), 0);
		add_action('init', array($this, 'add_endpoint'), 0);

	}
	public function add_query_vars($vars){
		$vars[] = 'pvita_redirect';
		$vars[] = 'message';
		$vars[] = 'post_id';
		$vars[] = 'ph_nums';

		return $vars;

	}

	public function add_endpoint(){
		if(get_option('pvita_wa_rotator_status') === 'valid'){
			add_rewrite_rule('^pvita_whatsapp_redirect_plugin/?([^&]+)?&?([0-9]+)?','index.php?message=$matches[1]&post_id=$matches[2]&ph_nums=$matches[3]','top');
		}
	}
	public function sniff_requests(){
		global $wp;
		//print_r($wp->request == 'pvita_whatsapp_redirect_plugin');
		if($wp->request == 'pvita_whatsapp_redirect_plugin'){
			//echo 'hahahah';
			$this->handle_request();
		}
	}
	protected function handle_request(){
		global $wp;

		$ph_nums = $wp->query_vars['ph_nums'];
		$message = $wp->query_vars['message'];
		$post_id = $wp->query_vars['post_id'];
		$meta_key = 'pvita_plugin_wa_redirect';
		
		$pesan = $message;

		$cs = preg_split('/\r\n|[\r\n]/', $ph_nums);
		$cs_count = count($cs);
		$old_option = get_post_meta($post_id,$meta_key);
		
		if($old_option){
			$cs_id = $old_option[0];
			if($cs_count > 1){
				if($cs_id != 0 && $cs_id >= $cs_count -1){
					update_post_meta($post_id, $meta_key, 0);
				}else{
					update_post_meta($post_id, $meta_key, $cs_id+1);
				}
			}else if($cs_id != 0){
				update_post_meta($post_id, $meta_key, 0);
			}
		}else{
			add_post_meta($post_id, $meta_key, 0);
			$cs_id = get_post_meta($post_id,$meta_key)[0];
		}
		
		$phone=$cs[$cs_id];

		
		$url = 'https://api.whatsapp.com/send?phone=62'.$phone.'&text='.rawurlencode($pesan);

		$this->pvita_rotator_plugin_wp_redirect($url);
		exit;
	}
	protected function pvita_rotator_plugin_wp_redirect($location, $status = 302) {
	    global $is_IIS;
	 
	    /**
	     * Filters the redirect location.
	     *
	     * @since 2.1.0
	     *
	     * @param string $location The path to redirect to.
	     * @param int    $status   Status code to use.
	     */
	    $location = apply_filters( 'wp_redirect', $location, $status );
	 
	    /**
	     * Filters the redirect status code.
	     *
	     * @since 2.3.0
	     *
	     * @param int    $status   Status code to use.
	     * @param string $location The path to redirect to.
	     */
	    $status = apply_filters( 'wp_redirect_status', $status, $location );
	 
	    if ( ! $location )
	        return false;
	 
	    //$location = wp_sanitize_redirect($location);
	 
	    if ( !$is_IIS && PHP_SAPI != 'cgi-fcgi' )
	        status_header($status); // This causes problems on IIS and some FastCGI setups
	 
	    header("Location: $location", true, $status);
	 
	    return true;
	}
}
new pvita_whatsapp_redirect_plugin();
