<?php

// Creating the widget 
class pvita_wa_rotator_button_plugin extends WP_Widget {

	/*output the widget in widget admin*/
	function pvita_wa_rotator_button_plugin(){
		parent::__construct(false, '#pvita WA Rotator Plugin');
	}

	/*output the options*/
	function form($instance) {

		/*set the default value of the inputs*/
		$defaults = array(
			'text' => 'Hubungi Kami Via Whatsapp',
			'color' => 'hijau', //or putih
			'size' => 'standard',//large or xlarge
			'position'=>'left',//right,center or block
			'bold' => 'tipis',
			'sticky' => 'sticky',
			'event1' => 'event1',
			'event2' => 'event2',
			'ph_nums' => '8122800200',
			'message' => 'hai kak'
		);

		// This overwrites any default values with saved values
		$instance = wp_parse_args( (array) $instance, $defaults );

		/*the actual input*/
		?>

		<!-- Button text -->
		<p>
			<label for="<?php echo $this->get_field_id('text'); ?>"><strong><?php _e('Text Pada Tombol','pwar'); ?></strong></label>

			<input id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>" value="<?php echo $instance['text']; ?>" type="text" class="widefat" />

		</p>

		<!-- button color -->
		<p>
			<label for="<?php echo $this->get_field_id('color'); ?>"><strong><?php _e('Warna','pwar'); ?></strong></label>

			<select class="pwar-select widefat" id="<?php echo $this->get_field_id('color'); ?>" name="<?php echo $this->get_field_name('color'); ?>">
				<option class="pwar-option" value="hijau" <?php selected( $instance['color'],'hijau' ) ?> >Hijau</option>
				<option class="pwar-option" value="putih" <?php selected( $instance['color'],'putih' ) ?> >Putih</option>
			</select>

		</p>
		<!-- button size -->
		<p>
			<label for="<?php echo $this->get_field_id('size'); ?>"><strong><?php _e('Ukuran','pwar'); ?></strong></label>

			<select class="pwar-select widefat" id="<?php echo $this->get_field_id('size'); ?>" name="<?php echo $this->get_field_name('size'); ?>">
				<option class="pwar-option" value="standard" <?php selected( $instance['size'],'standard') ?> >Normal</option>
				<option class="pwar-option" value="large" <?php selected( $instance['size'],'large') ?> >Gedean Dikit</option>
				<option class="pwar-option" value="xlarge" <?php selected( $instance['size'],'xlarge') ?> >Gede Bingiits</option>
			</select>
		</p>

		<!-- position -->
		<p>
			<label for="<?php echo $this->get_field_id('position'); ?>"><strong><?php _e('Posisi','pwar'); ?></strong></label>

			<select class="pwar-select widefat" id="<?php echo $this->get_field_id('position'); ?>" name="<?php echo $this->get_field_name('position'); ?>">
				<option class="pwar-option" value="left" <?php selected( $instance['position'],'left') ?> >Kiri</option>
				<option class="pwar-option" value="right" <?php selected( $instance['position'],'right') ?> >Kanan</option>
				<option class="pwar-option" value="center" <?php selected( $instance['position'],'center') ?> >Tengah</option>
				<option class="pwar-option" value="block" <?php selected( $instance['position'],'block') ?> >Blok</option>
			</select>
		</p>

		<!-- bold -->
		<p>
			<label for="<?php echo $this->get_field_id('bold'); ?>"><strong><?php _e('Text nya di tebelin?','pwar'); ?></strong></label>
			<select class="pwar-select widefat" id="<?php echo $this->get_field_id('bold'); ?>" name="<?php echo $this->get_field_name('bold'); ?>" >
				<option class="pwar-option" value="bold" <?php selected( $instance['bold'],'bold') ?> >Yoih</option>
				<option class="pwar-option" value="thin" <?php selected( $instance['bold'],'thin') ?> >Tipis aja gpp</option>
			</select>

		</p>

		<!-- sticky -->
		<p>
			<label for="<?php echo $this->get_field_id('sticky'); ?>"><strong><?php _e('Sticky di mobile?','pwar'); ?></strong></label>
			<select class="pwar-select widefat" id="<?php echo $this->get_field_id('sticky'); ?>" name="<?php echo $this->get_field_name('sticky'); ?>" >
				<option class="pwar-option" value="sticky" <?php selected( $instance['sticky'],'sticky') ?> >Iyes</option>
				<option class="pwar-option" value="no-sticky" <?php selected( $instance['sticky'],'no-sticky') ?> >Ngga ah</option>
			</select>

		</p>
		<!-- event 1 -->
		<p>
			<label for="<?php echo $this->get_field_id('event1'); ?>"><strong><?php _e('Event pada tombol','pwar'); ?></strong></label>

			<input id="<?php echo $this->get_field_id('event1'); ?>" name="<?php echo $this->get_field_name('event1'); ?>" value="<?php echo $instance['event1']; ?>" type="text" class="widefat" />

		</p>

		<!-- event 2 -->
		<p>
			<label for="<?php echo $this->get_field_id('event2'); ?>"><strong><?php _e('Event kedua pada tombol','pwar'); ?></strong></label>

			<input id="<?php echo $this->get_field_id('event2'); ?>" name="<?php echo $this->get_field_name('event2'); ?>" value="<?php echo $instance['event2']; ?>" type="text" class="widefat" />

		</p>

		<!-- phone numbers -->
		<p>
			<label for="<?php echo $this->get_field_id('ph_nums'); ?>"><strong><?php _e('Nomer WA','pwar'); ?></strong></label>

			<textarea id="<?php echo $this->get_field_id('ph_nums'); ?>" name="<?php echo $this->get_field_name('ph_nums'); ?>" type="text" class="widefat" rows="4"><?php echo $instance['ph_nums']; ?></textarea>

		</p>

		<!-- phone numbers -->
		<p>
			<label for="<?php echo $this->get_field_id('message'); ?>"><strong><?php _e('Isi Pesan','pwar'); ?></strong></label>

			<textarea id="<?php echo $this->get_field_id('message'); ?>" name="<?php echo $this->get_field_name('message'); ?>" type="text" class="widefat" rows="4"><?php echo $instance['message']; ?></textarea>

		</p>
		<?php		
	}

	/*update the option upon save*/
	function update($new_instance, $old_instance){

		// Get the old values
		$instance = $old_instance;

		$instance['text'] = strip_tags( $new_instance['text'] );
		$instance['color'] = strip_tags( $new_instance['color'] );
		$instance['size'] = strip_tags( $new_instance['size'] );
		$instance['position'] = strip_tags( $new_instance['position'] );
		$instance['bold'] = strip_tags( $new_instance['bold'] );
		$instance['sticky'] = strip_tags( $new_instance['sticky'] );
		$instance['event1'] = strip_tags( $new_instance['event1'] );
		$instance['event2'] = strip_tags( $new_instance['event2'] );
		$instance['message'] = $new_instance['message'];
		$instance['ph_nums'] = $new_instance['ph_nums'];
		return $instance;
	}
	
	/*output the actual widget to the front end*/
	function widget($args, $instance){

		extract( $args );
		$text = $instance['text'];
		$color = $instance['color'];
		$size = $instance['size'];
		$position = $instance['position'];	
		$bold = $instance['bold'];
		$sticky = $instance['sticky'];	
		$event1 = $instance['event1'];
		$event2 = $instance['event2'];	
		$ph_nums = $instance['ph_nums'];
		$message = $instance['message'];	

		echo $before_widget;

		/*ouput the content of the widget*/
		echo pwar_output_wa_button($text, $color, $size, $position, $bold, $sticky, $event1, $event2,$ph_nums,$message);

		echo $after_widget;
	}

}

/*output the widget*/
function pwar_output_wa_button($text,$color,$size,$position,$bold,$sticky,$event1,$event2,$ph_nums,$message){

	$fbq1 = $event1 ? "fbq('track','$event1');" : "";
	$fbq2 = $event2 ? "fbq('track','$event2');" : ""; 
	$html = '

		<div class="wa pvita-button-container '.$color.' '.$size.' '.$position.' '.$bold.' '.$sticky.'">
			<a href="'.site_url().'/pvita_whatsapp_redirect_plugin/?message='.rawurlencode($message).'&post_id='.get_the_id().'&ph_nums='.rawurlencode($ph_nums).'" class="pvita-button" onClick="'.$fbq1.' '.$fbq2.'">'.$text.'</a>
			<div class="clearfix"></div>
		</div>

	';

	return $html;
}

// Register and load the widget
function pvita_wa_rotator_button_plugin() {
	register_widget( 'pvita_wa_rotator_button_plugin' );
}
add_action( 'widgets_init', 'pvita_wa_rotator_button_plugin' );

?>