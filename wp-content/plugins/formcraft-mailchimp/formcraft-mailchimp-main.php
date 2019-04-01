<?php

	/*
	Plugin Name: FormCraft MailChimp Add-On
	Plugin URI: http://formcraft-wp.com/addons/mailchimp/
	Description: MailChimp Add-On for FormCraft
	Author: nCrafts
	Author URI: http://formcraft-wp.com/
	Version: 1.11
	Text Domain: formcraft-mailchimp
	*/

	use \DrewM\MailChimp\MailChimp;
	global $fc_meta, $fc_forms_table, $fc_submissions_table, $fc_views_table, $fc_files_table, $wpdb;

	add_action('formcraft_after_save', 'formcraft_mailchimp_trigger', 10, 4);
	function formcraft_mailchimp_trigger($content, $meta, $raw_content, $integrations)
	{
		global $fc_final_response;
		if ( in_array('MailChimp', $integrations['not_triggered']) ) {
			return false;
		}
		$mailchimp_data = formcraft_get_addon_data('MailChimp', $content['Form ID']);
		$double = isset($mailchimp_data['double_opt_in']) && $mailchimp_data['double_opt_in']==true ? 'pending' : 'subscribed';

		if (!$mailchimp_data) {
			return false;
		}
		if (!isset($mailchimp_data['validKey']) || empty($mailchimp_data['validKey']) ) {
			return false;
		}
		if (!isset($mailchimp_data['Map'])) {
			return false;
		}

		$submit_data = array();
		foreach ($mailchimp_data['Map'] as $key => $line) {
			$submit_data[$line['listID']]['status'] = $double;
			if ($line['columnID']=='EMAIL') {
				$email = fc_template($content, $line['formField']);
				if ( !filter_var($email,FILTER_VALIDATE_EMAIL) ) {
					continue;
				}
				$submit_data[$line['listID']]['email_address'] = $email;
			}
			else
			{
				$submit_data[$line['listID']]['merge_fields'][$line['columnID']] = fc_template($content, $line['formField']);
				$submit_data[$line['listID']]['merge_fields'][$line['columnID']] = trim(preg_replace('/\s*\[[^)]*\]/', '', $submit_data[$line['listID']]['merge_fields'][$line['columnID']]));
			}
		}

		require_once('MailChimpV3.php');
		foreach ($submit_data as $list_id => $list_submit) {
						
			if (!isset($list_submit['email_address'])) {
				$fc_final_response['debug']['failed'][] = __('MailChimp: No Email Specified','formcraft-mailchimp');
				continue;
			}
			$mailchimp = new MailChimp($mailchimp_data['validKey']);
			$mailchimp->verify_ssl = false;

			$result = $mailchimp->post("lists/$list_id/members", $list_submit);
			if (!$mailchimp->success()) {
				$fc_final_response['debug']['failed'][] = $mailchimp->getLastError();
			} else {
				$fc_final_response['debug']['success'][] = 'MailChimp Added: '.$list_submit['email_address'];
			}
		}
	}

	add_action('formcraft_addon_init', 'formcraft_mailchimp_addon');
	add_action('formcraft_addon_scripts', 'formcraft_mailchimp_scripts');

	function formcraft_mailchimp_addon()
	{
		register_formcraft_addon('MC_printContent',142,'MailChimp','MailChimpController',plugins_url('assets/logo.png', __FILE__ ),plugin_dir_path( __FILE__ ).'templates/',1);
	}
	function formcraft_mailchimp_scripts()
	{
		wp_enqueue_script('fcm-main-js', plugins_url( 'assets/builder.js', __FILE__ ));
		wp_enqueue_style('fcm-main-css', plugins_url( 'assets/builder.css', __FILE__ ));
	}

	add_action( 'wp_ajax_formcraft_mailchimp_test_api', 'formcraft_mailchimp_test_api' );
	function formcraft_mailchimp_test_api()
	{
		$key = $_GET['key'];
		require_once('MailChimpV3.php');
		$mailchimp = new MailChimp($key);
		$mailchimp->verify_ssl = false;
		$lists = $mailchimp->get('lists');
		if ($lists['lists']!=NULL) {
			echo json_encode(array('success'=>'true'));
			die();
		} else {
			echo json_encode(array('failed'=>'true'));
			die();
		}
	}
	add_action( 'wp_ajax_formcraft_mailchimp_get_lists', 'formcraft_mailchimp_get_lists' );
	function formcraft_mailchimp_get_lists()
	{
		$key = $_GET['key'];
		require_once('MailChimpV3.php');
		$mailchimp = new MailChimp($key);
		$mailchimp->verify_ssl = false;
		$lists = $mailchimp->get('lists');
		$lists = $lists['lists'];
		$listsRefined = array();
		foreach ($lists as $key => $value) {
			$listsRefined[$key]['id'] = $value['id'];
			$listsRefined[$key]['name'] = $value['name'];
		}
		if ($listsRefined) {
			echo json_encode(array('success'=>'true','lists'=>$listsRefined));
			die();
		} else {
			echo json_encode(array('failed'=>'true'));
			die();
		}
	}
	add_action( 'wp_ajax_formcraft_mailchimp_get_columns', 'formcraft_mailchimp_get_columns' );
	function formcraft_mailchimp_get_columns()
	{
		$key = $_GET['key'];
		$id = $_GET['id'];
		require_once('MailChimpV3.php');
		$mailchimp = new MailChimp($key);
		$mailchimp->verify_ssl = false;
		$columns = $mailchimp->get("lists/$id/merge-fields");
		$columns = $columns['merge_fields'];
		$columnsRefined = array();
		$columnsRefined[] = array('tag'=>'EMAIL', 'name'=>'Email Address');		
		foreach ($columns as $key => $value) {
			$columnsRefined[] = array('tag'=>$value['tag'], 'name'=>$value['name']);
		}
		if ($columnsRefined) {
			echo json_encode(array('success'=>'true','columns'=>$columnsRefined));
			die();
		} else {
			echo json_encode(array('failed'=>'true'));
			die();
		}
	}

	function MC_printContent()
	{

		?>
		<div id='mc-cover' id='mc-valid-{{Addons.MailChimp.showOptions}}'>
			<div class='loader'>
				<div class="fc-spinner small">
					<div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div>
				</div>
			</div>
			<div class='help-link'>
				<a class='trigger-help' data-post-id='19'><?php _e('how does this work?','formcraft-mailchimp'); ?></a>
			</div>
			<div class='api-key hide-{{Addons.MailChimp.showOptions}}'>	
				<input placeholder='<?php _e('Enter API Key','formcraft-mailchimp') ?>' style='width: 77%; margin-right: 3%; margin-left:0' type='text' ng-model='Addons.MailChimp.api_key'><button ng-click='testKey()' style='width: 20%' class='button blue'><?php _e('Check','formcraft-mailchimp') ?></button>
			</div>
			<div ng-show='Addons.MailChimp.showOptions'>
				<div id='mapped-mc' class='nos-{{Addons.MailChimp.Map.length}}'>
					<div>
						<?php _e('Nothing Here','formcraft-mailchimp') ?>
					</div>
					<table cellpadding='0' cellspacing='0'>
						<tbody>
							<tr ng-repeat='instance in Addons.MailChimp.Map'>
								<td style='width: 30%'>
									<span>{{instance.listName}}</span>
								</td>
								<td style='width: 30%'>
									<span>{{instance.columnName}}</span>
								</td>
								<td style='width: 30%'>
									<span><input type='text' ng-model='instance.formField'/></span>
								</td>
								<td style='width: 10%; text-align: center'>
									<i ng-click='removeMap($index)' class='icon-cancel-circled'></i>
								</td>								
							</tr>
						</tbody>
					</table>
				</div>
				<div id='mc-map'>
					<select class='select-list' ng-model='SelectedList'><option value='' selected="selected"><?php _e('List','formcraft-mailchimp') ?></option><option ng-repeat='list in MCLists' value='{{list.id}}'>{{list.name}}</option></select>

					<select class='select-column' ng-model='SelectedColumn'><option value='' selected="selected"><?php _e('Column','formcraft-mailchimp') ?></option><option ng-repeat='col in MCColumns' value='{{col.tag}}'>{{col.name}}</option></select>

					<input class='select-field' type='text' ng-model='FieldName' placeholder='<?php _e('Form Field','formcraft-mailchimp') ?>'>
					<button class='button' ng-click='addMap()'><i class='icon-plus'></i></button>
				</div>
				<div class='more-options'>
					<label><input type='checkbox' ng-model='Addons.MailChimp.double_opt_in'><?php _e('Double Opt-In','formcraft-mailchimp'); ?></label>
				</div>
			</div>
		</div>
		<?php
	}


	?>