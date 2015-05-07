<?php

// Prevents direct access to the file
defined('ABSPATH') or die();


/**
 * Renders the ui of the Database Cleanup widget
 *
 * @since 1.0
 */
function wuco_widget_database_cleanup(){
    global $wucoDatabaseCleanup;
    $wucoDatabaseCleanup->renderForm();
}


/**
 * Renders the UI of the Plugin Status widget
 *
 * @since 2.0
 */
function wuco_widget_plugin_status(){
    $title = __('Cleanup Status', 'wuco');

    // Database cleanup stats

    $content = '<table class="table-striped">';

    $content .= '<thead>';
    $content .= '<th>' . __('Entry type', 'wuco') . '</th>';
    $content .= '<th>' . __('Last time', 'wuco') . '</th>';
    $content .= '<th>' . __('Total', 'wuco') . '</th>';
    $content .= '</thead>';

    global $wucoDatabaseCleanup;
    $types = $wucoDatabaseCleanup->entryTypes;
    $stats = get_option('wuco_database_cleanup_done');

    foreach($types as $id => $type){
        $last = !empty($stats['last'][$id]) ? $stats['last'][$id] : 0;
        $total = !empty($stats['total'][$id]) ? $stats['total'][$id] : 0;

        $content .= '<tr>';
        $content .= '<td>' . ucfirst($type['name_plural']) . '</td>';
        $content .= '<td>' . $last . '</td>';
        $content .= '<td>' . $total . '</td>';
        $content .= '</tr>';
    }

    $content .= '</table>';


    // Database cleanup status

    $status = wuco_plugin_get_status();
    $dateFormat = get_option('date_format');
    $timeFormat = get_option('time_format');
    $default = 'N/A';

    $content .= '<table>';

    $content .= '<tr>';
    $content .= '<th scope="row">' . __('Previous cleanup type', 'wuco') .  '</th>';
    if(!empty($status['database_cleanup_type']))
        $cleanupType = $status['database_cleanup_type'] == 'automatic' ? __('Automatic', 'wuco') : __('Manual', 'wuco');
    else
        $cleanupType = $default;
    $content .= '<td>' . $cleanupType . '</td>';
    $content .= '</tr>';

    $content .= '<tr>';
    $content .= '<th scope="row">' . __('Previous cleanup', 'wuco') . '</th>';
    if(!empty($status['database_cleanup_time'])){
        $timestamp = $status['database_cleanup_time'];
        $previousDate = get_date_from_gmt(date_i18n('Y-m-d H:i:s', $timestamp), $dateFormat . ' ' . $timeFormat);
    } else {
        $previousDate = $default;
    }
    $content .= '<td>' . $previousDate . '</td>';
    $content .= '</tr>';

    $content .= '<tr>';
    $content .= '<th scope="row">' . __('Next cleanup', 'wuco') . '</th>';
    $timestamp = wp_next_scheduled('wuco_event_database_cleanup');
    if($timestamp !== false)
        $nextCleanup = get_date_from_gmt(date_i18n('Y-m-d H:i:s', $timestamp), $dateFormat . ' ' . $timeFormat);
    else
        $nextCleanup = $default;
    $content .= '<td>' . $nextCleanup . '</td>';
    $content .= '</tr>';

    $content .= '</table>';


    $widget = new wucoAdminWidget('plugin_status', $title, '', $content);
    $widget->displayWidget();
}
