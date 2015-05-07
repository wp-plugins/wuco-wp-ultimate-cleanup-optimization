<?php
/**
 * Plugin Name: WUCO - WP Ultimate Cleanup & Optimization
 * Description: Cleans up your database removing the useless entries such as post revisions, spam comments, orphaned meta, transient data, etc.
 * Version: 2.0
 * Author: pranacoder
 * License: GPL3
 */

// Prevents direct access to the file
defined('ABSPATH') or die();

// @todo This doesn't look good
require_once(ABSPATH . 'wp-admin/includes/plugin.php');

$pluginData = get_plugin_data(__FILE__);
define('WUCO_VERSION', $pluginData['Version']);

define('WUCO_PATH', plugin_dir_path(__FILE__));
define('WUCO_URL', plugin_dir_url(__FILE__));

require_once(WUCO_PATH . 'lib/wuco-admin-panel.php');
require_once(WUCO_PATH . 'lib/wuco-admin-widget.php');
require_once(WUCO_PATH . 'lib/wuco-plugin-status.php');
require_once(WUCO_PATH . 'lib/wuco-database-cleanup.php');

require_once(WUCO_PATH . 'includes/functions.php');
require_once(WUCO_PATH . 'includes/widgets.php');


/**
 * Performs actions on plugin activation
 *
 * @since 1.0
 */
function wucoPluginActivation(){
    if(get_option('wuco_version') != WUCO_VERSION){

        update_option('wuco_version', WUCO_VERSION);

//        exit(wp_redirect(admin_url('admin.php?page=wuco-about')));
    }
}

add_action('activated_plugin', 'wucoPluginActivation');


/**
 * Registers style and scripts. Localizes scripts
 *
 * @since 1.0
 */
function wucoAssetsRegister(){
    wp_register_style('wuco-admin-style', WUCO_URL . 'css/style.css');

    wp_register_script('wuco-admin-app', WUCO_URL . 'js/app.js', array('jquery', 'jquery-ui-widget'));

    $i18n = array(
        'selectAll' => __('Select all', 'wuco'),
        'selectNone' => __('Select none', 'wuco')
    );

    wp_localize_script('wuco-admin-app', 'wuco_i18n', $i18n);
}

add_action('init', 'wucoAssetsRegister');


/**
 * Enqueues styles and scripts
 *
 * @since 1.0
 */
function wucoAssetsEnqueueAdmin(){
    wp_enqueue_style('wuco-admin-style');

    wp_enqueue_script('wuco-admin-app');
}

add_action('admin_enqueue_scripts', 'wucoAssetsEnqueueAdmin');


/**
 * Adds custom WP-Cron intervals
 *
 * @param array $schedules
 * @return array
 *
 * @since 2.0
 */
function wucoCronAddIntervals($schedules){
    $schedules['weekly'] = array(
        'interval' => 60 * 60 * 24 * 7,
        'display' => __('Once Weekly', 'wuco')
    );

    $schedules['monthly'] = array(
        'interval' => 60 * 60 * 24 * 30,
        'display' => __('Once Monthly', 'wuco')
    );

    return $schedules;
}

add_filter('cron_schedules', 'wucoCronAddIntervals');


/**
 * Automatically reloads the plugin page
 *
 * @since 2.0
 */
function wucoReloadPage(){
    if(empty($_REQUEST['page']) || $_REQUEST['page'] != 'wuco')
        return;

    if(empty($_REQUEST['reload']))
        return;

    $uri = $_SERVER['REQUEST_URI'];
    $uri = str_replace('reload=1', '', $uri);

    wp_redirect($uri);
    exit;
}

add_action('admin_init', 'wucoReloadPage');