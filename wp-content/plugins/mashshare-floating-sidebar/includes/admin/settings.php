<?php

/**
 * Registers the options in mashsb Extensions tab
 * *
 * @access      private
 * @since       1.0
 * @param 	$settings array the existing plugin settings
 * @return      array
 * 
 */
function mashfs_extension_settings($settings) {

    $ext_settings = array(
        array(
            'id' => 'mashfs_header',
            'name' => '<strong>' . __('Floating Sidebar', 'mashfs') . '</strong>',
            'desc' => '',
            'type' => 'header',
            'size' => 'regular'
        ),
        array(
            'id' => 'mashfs_side',
            'name' => __('Side', 'mashfs'),
            'desc' => __('', 'mashfs'),
            'type' => 'select',
            'options' => array(
                '0' => __('Right', 'mashfs'),
                '1' => __('Left', 'mashfs'),
            ),
        ),
        array(
            'id' => 'mashfs_pages',
            'name' => __('Pages', 'mashfs'),
            'desc' => __('Select pages on which floating sidebar will be displayed', 'mashfs'),
            'type' => 'mashfspages',
        ),
        array(
            'id' => 'mashfs_networks',
            'name' => __('Social Networks', 'mashfs'),
            'desc' => __('Select social networks which should be displayed in sidebar. <br><strong>You can drag and drop to sort them!</strong>', 'mashfs'),
            'type' => 'mashfsnetworks',
        ),
        array(
            'id' => 'mashfs_mail_subject',
            'name' => __('E-Mail Subject', 'mashfs'),
            'desc' => __('E-Mail Subject; use [title] for post title', 'mashfs'),
            'type' => 'text',
            'size' => 'medium',
        ),
        array(
            'id' => 'mashfs_mail_body',
            'name' => __('E-Mail Body', 'mashfs'),
            'desc' => __(
                    'E-Mail Message Body, you can add [excerpt] for post excerpt / short description or ' .
                    '[mash_body] URL of your content will be added at the end of the e-mail body', 'mashfs'
            ),
            'type' => 'text',
            'size' => 'medium',
        ),
        array(
            'id' => 'mashfs_excluded',
            'name' => __('Exclude from', 'mashfs'),
            'desc' => __('Exclude floating sidebar from a list of specific posts. Put in the post id separated by a comma, e.g. 23, 63, 114', 'mashfs'),
            'type' => 'text',
            'size' => 'medium',
        ),
        array(
            'id' => 'mashfs_sharecountcolor',
            'name' => __('Share count', 'mashfbar'),
            'desc' => __('Color of the total share count, e.g. ffffff', 'mashfs'),
            'type' => 'color_select',
            'std' => '5a5a5f'
        ),
        array(
            'id' => 'mashfs_backgroundcolor',
            'name' => __('Background', 'mashfbar'),
            'desc' => __('Background color of the sidebar, e.g. 00adef. Leave empty for transparent', 'mashfs'),
            'type' => 'color_select',
            'std' => ''
        ),
        array(
            'id' => 'mashfs_enable_count',
            'name' => __('Show total shares', 'mashfs'),
            'desc' => __(''),
            'type' => 'checkbox'
        ),
        array(
            'id' => 'mashfs_enable_toggle',
            'name' => __('Enable hide button', 'mashfs'),
            'desc' => __('This shows a button on bottom of the sidebar which allows to hide the sidebar'),
            'type' => 'checkbox'
        ),
        array(
            'id' => 'mashfs_disable_mobile',
            'name' => __('Disable on mobile devices', 'mashfs'),
            'desc' => __(''),
            'type' => 'select',
            'options' => array(
                'show_it' => 'Show on mobile devices',
                '460' => 'Hide on screens smaller 460px',
                '568' => 'Hide on screens smaller 568px',
            )
        ),
        array(
            'id' => 'mashfs_margin_edge',
            'name' => __('Margin', 'mashfbar'),
            'desc' => __('Margin to the left or right edge of the screen in pixel. <strong>Default:</strong> 0', 'mashfs'),
            'type' => 'number',
            'size' => 'small',
            'std' => '0'
        ),
        array(
            'id' => 'mashfs_margin_buttons',
            'name' => __('Margin Buttons', 'mashfbar'),
            'desc' => __('Space between each button in pixel. <strong>Default:</strong> 0', 'mashfs'),
            'type' => 'number',
            'size' => 'small',
            'std' => '0'
        ),
    );

    return array_merge($settings, $ext_settings);
}

add_filter('mashsb_settings_extension', 'mashfs_extension_settings');

function mashsb_mashfspages_callback($args) {
    global $mashsb_options;
    $pages = array(
        'home' => 'Home',
        'single' => 'Posts',
        'page' => 'Pages',
        'category' => 'Categories',
    );
    $output = '';
    foreach ($pages as $type => $label) {
        $checked = isset($mashsb_options[$args['id']][$type]);
        $output .= '<input type="checkbox" name="mashsb_settings[' . $args['id'] . '][' . $type . ']"
			id="mashsb_settings[' . $args['id'] . '][' . $type . ']" value="' . $type . '"' . checked($checked, true, false) . '>';
        $output .= '<label for="mashsb_settings[' . $args['id'] . '][' . $type . ']">' . $label . '</label><br>';
    }

    echo $output;
}

function mashsb_mashfsnetworks_callback($args) {
    global $mashsb_options;

    if (!isset($mashsb_options[$args['id']])) {
        echo '<span style="color:red;">' . __('Error: No networks available. Deactivate and activate again the floating Sidebar Add-On!', 'mashfs') . '</span>';
        return false;
    }

    // Add e-mail
    $mashsb_options[$args['id']]['mail']['url'] = str_replace(
            array(
        '$subject',
        '$body'
            ), array(
        '',
        ''
            ), $mashsb_options[$args['id']]['mail']['url']
    );

    $output = '<div id="mashfs-sortable-wrapper">';
    foreach ($mashsb_options[$args['id']] as $network => $params) {
        $checked = isset($params['status']) ? $params['status'] : null;
        $output .= '<div class="mashfs-network" style="cursor:move;"><input type="checkbox" name="mashsb_settings[' . $args['id'] . '][' . $network . '][status]"
			id="mashsb_settings[' . $args['id'] . '][' . $network . ']" value="1"' . checked($checked, 1, false) . '>';
        $output .= '<input type="hidden" name="mashsb_settings[' . $args['id'] . '][' . $network . '][url]"
			id="mashsb_settings[' . $args['id'] . '][' . $network . '][status]" value="' . $params['url'] . '">';
        $output .= '<label for="mashsb_settings[' . $args['id'] . '][' . $network . ']">' . $network . '</label></div>';
    }
    $output .= '</div>';

    echo $output;
}
