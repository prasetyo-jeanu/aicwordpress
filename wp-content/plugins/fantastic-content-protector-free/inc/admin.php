<?php

function fantasticcontent_admin_pages() {
    ?>
    <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0" />
    <link href ="<?php echo WP_CONTENT_URL; ?>/plugins/fantastic-content-protector-free/css/adminstyle.css" type="text/css" rel="stylesheet"/>
    <style type="text/css">
        #gif
        {
            width:100%;
            height:100%;
            display:none;
            margin-left: 393px;
        }
        #result
        {
            display:none;
            border-color:#e8426d;
            background-color:#FFFFF;
            color:#e8426d;
            border: solid;
            width:160px;
            text-align: center;
            margin-left: 389px;
        }


    </style>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            jQuery('#submit_general').click(function($)
            {
                jQuery('#gif').css("display", "block");


            });
        });
        function submitgeneral() {
            jQuery.ajax({type: 'POST', url: 'options.php', data: jQuery('#form_general').serialize(), success: function(response) {


                    jQuery('#gif').css("display", "none");
                    jQuery('#result').css("display", "block");
                    jQuery('#result').html("Settings Saved");
                    jQuery('#result').fadeOut(2500, "linear");
                }});

            return false;
        }

    </script>
    <div class="wrap">
        <?php
        $bpageheader = true;
        if ($bpageheader == true) {
            ?>
            <div class="ic"></div>
            <a href="http://fantasticplugins.com" target="_blank"><h2 style="text-align: left;"><img style="position:relative;top:66px;" src="<?php echo WP_CONTENT_URL; ?>/plugins/fantastic-content-protector-free/assets/favicon.png"/></h2></a><br/><br/>
            <h2 style="text-align: right;margin-top:-160px;"><label><strong>Check Out Our Pro Version</strong></label></h2>
            <a href="http://codecanyon.net/item/smart-content-protector-pro-wp-copy-protection/5400835?ref=FantasticPlugins" target="_blank"><h2 style="text-align: right;"><img style="height: 200px;
                                                                                                                                                                                 width: 315px;" src="<?php echo WP_CONTENT_URL; ?>/plugins/fantastic-content-protector-free/assets/smartcontentprotector.jpg"/></h2></a>
                                                                                                                                                                             <?php } ?>
        <div class="left">
            <div class="metabox-holder4">
                <div class="postbox4">
                    <h3>General Settings</h3>
                    <div class="inside4">
                        <p><label>Fantastic Content Protector Free is to Automatically Protect the Website Content under Theft<br/><br/>
                                Basically this Plugin Disable the Control Keys like <br/><br/></label>
                            <label style="position: relative; left:393px;">
                                CTRL+A <br/>
                                CTRL+C<br/>
                                CTRL+V<br/>
                                CTRL+X<br/>
                                CTRL+S<br/>
                                CTRL+U<br/>
                                MOUSE RIGHT CLICK
                                <br/>
                            </label>
                        </p>

                        <form id="form_general" onsubmit="return submitgeneral();">
                            <?php
                            $options = get_option('content_protector_disable');
                            $credit_links = get_option('credit_link_content_protect');
                            ?>
                            <?php settings_fields('general_credit_links'); ?>
                            <ul>
                                <li>
                                    <label>Disable this Plugin</label>
                                    <input type="checkbox" class="checkbox_general" name="content_protector_disable" style="margin-left:257px;" value="1"<?php checked('1', $options); ?>/>
                                </li>
                                <li>
                                    <label>Credit Link </label>
                                    <input type="radio" class="radiobox_general" name="credit_link_content_protect" style="margin-left:309px;" value="1"<?php checked('1', $credit_links); ?>/><label>&nbsp;ON</label><br/>
                                    <input type="radio" class="radiobox_general" name="credit_link_content_protect" style="margin-left:393px;" value="2"<?php checked('2', $credit_links); ?>/><label>&nbsp;OFF</label><br/><br/>
                                </li>
                            </ul>
                            <div id="gif"><img src="<?php echo WP_PLUGIN_URL; ?>/fantastic-content-protector-free/assets/bar.gif"/></div>
                            <div id="result"></div>
                            <p class="submit" style="margin-left:393px;">
                                <input class="button-primary" id="submit_general" type="submit" value="<?php _e('Save') ?>" />
                            </p>
                        </form>
                        <form method="post" class="form-1" style="margin-top:-64px; margin-left:308px;">
                            <p class="submit">
                                <input class="button-secondary"  name="Reset" type="submit" value="<?php _e('Reset'); ?>" />
                                <input name="action" type="hidden" value="reset" />
                        </form>


                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    $bpageside1 = false;
    if ($bpageside1 == true) {
        ?>
        <div class="metabox-holder_lp">
            <div class="postbox_lp"  >
                <h3>You Like This Plugin?</h3>
                <div class="inside_lp">
                    <p><strong>We are not taking donations. Try becoming a <a href="http://fantasticplugins.com/" target="_blank">Fantastic Plugins Member</a>. Afterall each Fantastic WordPress Plugin will cost less than a $1 for Members.</strong></p>
                </div>
            </div>
        </div>
    <?php } ?>

    <?php
    $bpageside2 = false;
    if ($bpageside2 == true) {
        ?>
        <div class="metabox-holder_latest">
            <div class="postbox_latest"  >
                <h3>Latest News</h3>
                <div class="inside_latest">
                    <?php
                    $new = file_get_contents("http://fantasticplugins.com/blog/feed");
                    $x = new SimpleXmlElement($new);
                    echo "<ul>";

                    foreach ($x->channel->item as $entry) {
                        echo "<li><a href='$entry->link' title='$entry->title'>" . $entry->title . "</a></li>";
                    }
                    echo "</ul>";
                    ?>
                </div>

            </div>
        </div>
        <?php
    }
}
?>