<div class="wrap wuco-wrap wuco-dashboard">

    <h2><?php _e('WUCO - WordPress Ultimate Cleanup & Optimization', 'wuco'); ?></h2>

    <?php wuco_message_updated(); ?>

    <div class="wuco-main">
        <?php wuco_widget_database_cleanup(); ?>
    </div>

</div>