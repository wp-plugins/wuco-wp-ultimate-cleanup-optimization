<?php


/**
 * Displays a WordPress updated message
 *
 * @since 1.0.0
 */
function wuco_message_updated(){

    if(empty($_REQUEST['action']) || empty($_REQUEST['updated']))
        return;

    $message = apply_filters('wuco_admin_message_updated_' . $_REQUEST['action'], __('The settings have been updated', 'wuco'));

    if(!empty($message))
        echo '<div id="message" class="updated"><p>' . $message . '</p></div>';
}


/**
 * Renders the ui of the Database Cleanup widget
 *
 * @since 1.0.0
 */
function wuco_widget_database_cleanup(){
    global $wucoDatabaseCleanup;
    $wucoDatabaseCleanup->renderForm();
}