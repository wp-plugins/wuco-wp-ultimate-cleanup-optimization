<?php

// Prevents direct access to the file
defined('ABSPATH') or die();


/**
 * Displays a WordPress admin message
 *
 * @since 2.0
 */
function wuco_message(){
    if(empty($_REQUEST['action']) || empty($_REQUEST['status']))
        return;

    $message = ''; $class = '';

    if($_REQUEST['status'] == 'success'){
        $message = __('The settings have been updated', 'wuco');
        $class = 'updated';
    }

    elseif($_REQUEST['status'] == 'error'){
        $message = __('An error occurred! Please try again', 'wuco');
        $class = 'error';
    }

    $message = apply_filters('wuco_admin_message', $message, $_REQUEST['status'], $_REQUEST['action']);

    if(!empty($message))
        echo '<div id="message" class="' . $class . '"><p>' . $message . '</p></div>';
}


/**
 * Retrieves plugin status data
 *
 * @param null $key
 * @return bool|mixed|void
 *
 * @since 2.0
 */
function wuco_plugin_get_status($key = null){
    global $wucoPluginStatus;
    $status = $wucoPluginStatus->getStatus($key);
    return $status;
}


/**
 * Updates plugin status data
 *
 * @param $key
 * @param $value
 * @return bool
 *
 * @since 2.0
 */
function wuco_plugin_update_status($key, $value){
    global $wucoPluginStatus;
    $updated = $wucoPluginStatus->updateStatus($key, $value);
    return $updated;
}