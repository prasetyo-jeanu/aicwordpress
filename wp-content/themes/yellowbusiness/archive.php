<?php
// =============================================================================
// TEMPLATE NAME: Archive
// -----------------------------------------------------------------------------
// Archive template for all custom Post Types - Lists and for blog posts. This template is hidden.
// This template not show any direct content, but fetch the content of the archive page built by the user for the specific Post Type.
// =============================================================================
if (defined("HC_PLUGIN_PATH")) {
    hc_archive_engine();
} else {
    get_header();
    $filter = "";
    $date = "";
    $title = "";

    if (is_author()) {
        $title = esc_attr__("Author's posts of ","yellowbusiness") . get_the_author() ;
    }
    if (is_day()) $date = get_the_date();
    if (is_month()) $date = get_the_date('F Y');
    if (is_year()) $date = get_the_date('Y');

    if (count($wp_query->tax_query->queries) > 0) {
        $args['category_name'] = $wp_query->tax_query->queries[0]['terms'][0];
        $title = esc_attr__("Archives with tag ","yellowbusiness") . $wp_query->queried_object->name;
    }
    if ($date != "") {
        $title = esc_attr__("Archives with date ","yellowbusiness") . $date;
    }

    $content = "";
    $paged = get_query_var( 'paged' ) ? absint(get_query_var('paged')) : 1;

    if ($date != "") {
        $args['monthnum'] = date("m",strtotime($date));
        $args['year'] = date("Y",strtotime($date));
    }
?>
<div class="header-base">
    <div class="container">
        <div class="row">
            <div class="col-md-9">
                <div class="title-base text-left">
                    <h1>
                        <?php echo esc_attr($title) ?>
                    </h1>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container content">
    <?php yellowbusiness_default_blog() ?>
</div>
<?php
}
get_footer();
?>


