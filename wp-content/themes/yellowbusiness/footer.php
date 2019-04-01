</div>
<?php
// =============================================================================
// FOOTER.PHP
// -----------------------------------------------------------------------------
// The footer of the theme.
// This file generate the following footer types:

// 01. FOOTER BASE - framework-y.com/components/footer/footer-base.html
// 02. FOOTER PARALLAX - framework-y.com/components/footer/footer-parallax.html
// 03. FOOTER MINIMAL- framework-y.com/components/footer/footer-minimal.html
// =============================================================================

if (defined("HC_PLUGIN_PATH")) {
    hc_get_footer_engine();
} else { ?>
<footer class="default-wp-footer">
    <div class="content">
        <div class="container">
            <div><?php echo esc_html_e("Schiocco | This is an example text | Powered by","yellowbusiness") ?> <a href="<?php echo esc_url("http//wordpress.org") ?>"><?php echo esc_attr("WordPress")?></a></div>
        </div>
    </div>
</footer>    
<?php } 
wp_footer();
?>
</body>
</html>
