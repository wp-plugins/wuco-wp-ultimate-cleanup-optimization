<div class="wrap wuco-wrap wuco-dashboard">

    <h2><?php _e('WUCO - WP Ultimate Cleanup & Optimization', 'wuco'); ?></h2>

    <?php wuco_message(); ?>

    <div class="wuco-row">
        <div class="wuco-main">
            <?php wuco_widget_database_cleanup(); ?>
        </div>

        <div class="wuco-sidebar">
            <?php wuco_widget_plugin_status(); ?>
        </div>
    </div>

</div>