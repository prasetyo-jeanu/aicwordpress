<div class="wrap iffe-wrap">
    <div class="options_wrap">

        <h2><?php echo esc_html__( 'Instagram Feed For Elementor', 'add-instagram-feed-for-elementor' ); ?></h2>


    <form method="post" action="options.php">

        <?php 

        settings_fields( 'iffe_option_group' ); 

        $options = get_option( 'iffe_options' ); 

        ?>
        <div class="options_wrap_full">
            <h3 class="sub-title"><?php echo esc_html__( 'General Settings', 'add-instagram-feed-for-elementor' ); ?></h3>

            <div class="options_input options_text">      
                <span class="labels">
                    <label for="username">Instagram Username</label>
                </span>
                <input name="iffe_options[username]" id="username" type="text" value="<?php if ( isset( $options['username']) ){ esc_attr_e($options['username']);  } ?>">
            </div>

            <div class="options_input options_text">      
                <span class="labels">
                    <label for="access_token"><?php echo esc_html__( 'Access Token', 'add-instagram-feed-for-elementor' ); ?></label>
                </span>

                <?php
                    if(isset($_GET['access_token'])){
                        $access_token = $_GET['access_token'];
                    }elseif(isset($options['access_token']) && $options['access_token'] !=''){
                        $access_token = $options['access_token'];
                    }else{
                        $access_token = '';
                    }
                ?>

                <input name="iffe_options[access_token]" id="access_token" type="text" value="<?php echo esc_attr($access_token); ?>">

                <?php $return_url = urlencode(admin_url('admin.php?page=add-instagram-feed-for-elementor')) . '&response_type=token'; ?>

                <small>
                    <?php echo esc_html__( 'Please enter the Instagram Access Token. Click the link below to get your Instagram access token.', 'add-instagram-feed-for-elementor' ); ?>
                    <a href="https://api.instagram.com/oauth/authorize/?client_id=54da896cf80343ecb0e356ac5479d9ec&scope=basic+public_content&redirect_uri=http://api.web-dorado.com/instagram/?return_url=<?php echo $return_url;?>"><?php echo esc_html__( 'Get Access Token', 'add-instagram-feed-for-elementor' ); ?></a>
                </small>
            </div>
        </div>

        <div class="clearfix"></div>
        <span class="submit">
            <input class="button button-primary" type="submit" name="save" value="<?php _e('Save All Changes', 'add-instagram-feed-for-elementor') ?>" />
        </span>
    </form>
    </div>

    <div class="sidebox first-sidebox"> 

        <h3><?php echo esc_html__( 'Instruction to use Plugin', 'add-instagram-feed-for-elementor' ); ?></h3>

        <hr />

        <p><?php echo esc_html__( 'Go to Elementor page customization panel', 'add-instagram-feed-for-elementor' ); ?></p>

        <p><?php echo esc_html__( 'Scroll down and find WPCaps Elements', 'add-instagram-feed-for-elementor' ); ?></p>

        <p><?php echo esc_html__( 'You will find Instagram Feed added under WPCaps Elements', 'add-instagram-feed-for-elementor' ); ?></p>

        <p><?php echo esc_html__( 'Drag and drop Instagram Feed on the section you want to dispaly feed', 'add-instagram-feed-for-elementor' ); ?></p>

        <hr />

        <h3><?php echo esc_html__( 'Do you find this Plugin useful?', 'add-instagram-feed-for-elementor' ); ?></h3>

        <p>If yes, give 5 star rating to respect our work. Click <a href="http://wordpress.org/support/view/plugin-reviews/add-instagram-feed-for-elementor" target="_blank">here</a> to submit your ratings.</p>

    </div>

</div>