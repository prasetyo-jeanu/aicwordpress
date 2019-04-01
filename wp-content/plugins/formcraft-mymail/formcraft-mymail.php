<?php

	/*
	Plugin Name: FormCraft Mailster Add-On
	Plugin URI: http://formcraft-wp.com/addons/mymail/
	Description: Mailster Add-On for FormCraft
	Author: nCrafts
	Author URI: http://formcraft-wp.com/
	Version: 1.2
	Text Domain: formcraft-mailster
	*/

	global $fc_meta, $fc_forms_table, $fc_submissions_table, $fc_views_table, $fc_files_table, $wpdb;

	add_action('formcraft_after_save', 'formcraft_mailster_trigger', 10, 4);
	function formcraft_mailster_trigger($content, $meta, $raw_content, $integrations)
	{
		global $fc_final_response;
		if(!function_exists('mailster')){ return false; }
		if ( in_array('Mailster', $integrations['not_triggered']) ){ return false; }

		$mailster_data = formcraft_get_addon_data('Mailster', $content['Form ID']);
		if (!$mailster_data){return false;}
		if (!isset($mailster_data['Map'])){return false;}

		$double = isset($mailster_data['Double']) && $mailster_data['Double'] === true ? true : false;

		$submit_data = array();
		foreach ($mailster_data['Map'] as $key => $line) {
			if ($line['columnID']=='email')
			{
				$email = fc_template($content, $line['formField']);
				if ( !filter_var($email,FILTER_VALIDATE_EMAIL) ) { continue; }
				$submit_data[$line['listID']][$line['columnID']] = $email;
			}
			else
			{
				$name = fc_template($content, $line['formField']);
				$name = trim(preg_replace('/\s*\[[^)]*\]/', '', $name));
				$submit_data[$line['listID']][$line['columnID']] = $name;
			}
		}

		foreach ($submit_data as $key => $list_submit) {
			if ( empty($list_submit['email']) )
			{
				$fc_final_response['debug']['failed'][] = "Mailster Error: No email to add.";
				continue;
			}

			$list_submit['status'] = $double === true ? 0 : 1;
			$subscriber_id = mailster('subscribers')->add($list_submit, 1 );

			if ( is_wp_error($subscriber_id) )
			{
				$error_string = $subscriber_id->get_error_message();
				$fc_final_response['debug']['failed'][] = "Mailster Error: (".$list_submit['email'].") ".$error_string;
			}
			else if ( $subscriber_id > 0 )
			{
				$success = mailster('subscribers')->assign_lists($subscriber_id, $key, $remove_old = false);
				$fc_final_response['debug']['success'][] = 'Mailster Added: '.$list_submit['email'].' to list '.$key;
			}
		}
	}

	add_action('formcraft_addon_init', 'formcraft_mailster_addon');
	add_action('formcraft_addon_scripts', 'formcraft_mailster_scripts');

	function formcraft_mailster_addon()
	{
		register_formcraft_addon('Mailster_PrintContent',518,'Mailster','MailsterController',plugins_url('assets/logo.png', __FILE__ ), plugin_dir_path( __FILE__ ).'templates/',1);
	}
	function formcraft_mailster_scripts()
	{
		wp_enqueue_script('formcraft-mailster-main-js', plugins_url( 'assets/builder.js', __FILE__ ));
		wp_enqueue_style('formcraft-mailster-main-css', plugins_url( 'assets/builder.css', __FILE__ ));
	}

	function Mailster_PrintContent()
	{
		if (!function_exists('mailster')) {
			?>
			<div style='text-align: center; padding: 20px'>You don't seem to have Mailster installed.<br>The add-on isn't of much use.</div>
			<?php
		}
		else
		{
			$mailster_lists = mailster('lists')->get();

			?>
			<div id='mailster-cover'>
				<div style='padding-left: 1.2em; padding-top: 1.2em; font-size: .9em'>
					<div id='mapped-mailster' class='nos-{{Addons.Mailster.Map.length}}'>
						<div class='nothing-here'>
							<?php _e('Add a Field Mapping Below','formcraft-mailster') ?>
						</div>
						<div class='something-here'>
							<div ng-repeat='instance in Addons.Mailster.Map'>
								<div class='w-25'>
									<span class='is-text'>{{instance.listName}}</span>
								</div>
								<div class='w-25'>
									<span class='is-text'>{{instance.columnID}}</span>
								</div>
								<div class='w-25'>
									<span><input type='text' ng-model='instance.formField'/></span>
								</div>
								<div class='w-25'>
									<button ng-click='removeMap($index)' class='formcraft-button red'>Delete</button>
								</div>
							</div>
						</div>
					</div>
					<div id='mailster-map'>
						<div class='w-25'>
							<select class='select-list' ng-model='SelectedList'>
							<option value='' selected="selected">(<?php _e('List','formcraft-mailster') ?>)</option>
							<?php
							foreach($mailster_lists as $list){
								echo "<option value='".$list->ID."'>".$list->name."</option>";
							}
							?>
						</select>
						</div>
						<div class='w-25'>
							<?php $custom_fields = mailster()->get_custom_fields(); ?>
							<select class='select-column' ng-model='SelectedColumn'>
							<option value='' selected="selected">(<?php _e('Column','formcraft-mailster') ?>)</option>
							<option value='email'>E-mail</option>
							<option value='firstname'>Firstname</option>
							<option value='lastname'>Lastname</option>
							<?php
							foreach($custom_fields as $custom_field_id => $custom_field){
								echo "<option value='".esc_attr($custom_field_id)."'>".$custom_field['name']."</option>";
							}
							?>
						</select>
						</div>
						<div class='w-25'>
							<input class='select-field' type='text' ng-model='FieldName' placeholder='<?php _e('Form Field','formcraft-mailster') ?>'>
							<i class='formcraft-icon tooltip-icon' data-toggle='tooltip' title='Enter the form label, enclosed in square bracket. If the form label is Email, type in <strong>[Email]</strong> here'>info_outline</i>
						</div>
						<div class='w-25'>
							<button class='formcraft-button' ng-click='addMap()'>Add</button>
						</div>
					</div>
				</div>
				<label class='single-option has-checkbox' style='border-top-width: 1px; border-bottom-width: 0; margin-top: .75em'>
					<input type='checkbox' ng-model='Addons.Mailster.Double'/> Double Opt-In
				</label>
			</div>
			<?php
		}
	}


	?>