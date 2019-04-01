<?php
// =============================================================================
// TEMPLATE NAME: Base
// -----------------------------------------------------------------------------
// Base template.
// =============================================================================


get_header();
if (is_home()) { ?>
<div class="header-base header-default-blog">
    <div class="container">
        <div class="title-base text-center">
            <h1>
                <?php esc_attr_e("Blog","yellowbusiness") ?>
            </h1>
        </div>
    </div>
</div>
<div class="container content">
    <?php yellowbusiness_default_blog() ?>
</div>
<?php
} else {
   yellowbusiness_the_content();
}
get_footer();
?>
