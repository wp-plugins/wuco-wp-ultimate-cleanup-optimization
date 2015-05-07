<?php

/**
 * Class wucoDatabaseCleanup
 * 
 * @since 1.0
 */
class wucoDatabaseCleanup {

    function __construct(){

        $this->action = 'database_cleanup';
        $this->event = 'wuco_event_' . $this->action;

        global $wpdb;
        $this->intervals = array('daily', 'weekly', 'monthly');
        $this->groups = array(
            array(
                'id' => 'post',
                'name' => __('Posts', 'wuco')
            ),
            array(
                'id' => 'comment',
                'name' => __('Comments', 'wuco')
            ),
            array(
                'id' => 'meta',
                'name' => __('Meta data', 'wuco')
            ),
            array(
                'id' => 'other',
                'name' => __('Other data', 'wuco')
            )
        );
        $this->entryTypes = array(
            'post_revision' => array(
                'name_singular' => __('revision', 'wuco'),
                'name_plural' => __('revisions', 'wuco'),
                'descr' => __('Records of each saved draft or published update', 'wuco'),
                'sql' => "FROM $wpdb->posts WHERE post_type = 'revision'",
                'group' => 'post'
            ),
            'post_draft' => array(
                'name_singular' => __('draft', 'wuco'),
                'name_plural' => __('drafts', 'wuco'),
                'descr' => __('Posts that are saved but not published', 'wuco'),
                'sql' => "FROM $wpdb->posts WHERE post_status = 'draft'",
                'group' => 'post',
            ),
            'post_autodraft' => array(
                'name_singular' => __('auto draft', 'wuco'),
                'name_plural' => __('auto drafts', 'wuco'),
                'descr' => __('Drafts that are saved automatically', 'wuco'),
                'sql' => "FROM $wpdb->posts WHERE post_status = 'auto-draft'",
                'group' => 'post'
            ),
            'post_trash' => array(
                'name_singular' => __('trashed post', 'wuco'),
                'name_plural' => __('trashed posts', 'wuco'),
                'descr' => __('Posts that have been moved to trash', 'wuco'),
                'sql' => "FROM $wpdb->posts WHERE post_status = 'trash'",
                'group' => 'post'
            ),
            'comment_spam' => array(
                'name_singular' => __('spam comment', 'wuco'),
                'name_plural' => __('spam comments', 'wuco'),
                'descr' => __('Comments that have been marked as spam', 'wuco'),
                'sql' => "FROM $wpdb->comments WHERE comment_approved = 'spam'",
                'group' => 'comment'
            ),
            'comment_trash' => array(
                'name_singular' => __('trashed comment', 'wuco'),
                'name_plural' => __('trashed comments', 'wuco'),
                'descr' => __('Comments that have been moved to trash', 'wuco'),
                'sql' => "FROM $wpdb->comments WHERE comment_approved = 'trash'",
                'group' => 'comment'
            ),
            'comment_moderate' => array(
                'name_singular' => __('moderated comment', 'wuco'),
                'name_plural' => __('moderated comments', 'wuco'),
                'descr' => __('Comments waiting to be approved'),
                'sql' => "FROM $wpdb->comments WHERE comment_approved = '0'",
                'group' => 'comment'
            ),
            'comment_pingback' => array(
                'name_singular' => __('pingback', 'wuco'),
                'name_plural' => __('pingbacks', 'wuco'),
                'descr' => __('Comments that are created automatically when someone links to your posts', 'wuco'),
                'sql' => "FROM $wpdb->comments WHERE comment_type = 'pingback'",
                'group' => 'comment'
            ),
            'comment_trackback' => array(
                'name_singular' => __('trackback', 'wuco'),
                'name_plural' => __('trackbacks', 'wuco'),
                'descr' => __('Comments that are created when someone notifies you they have linked to your post', 'wuco'),
                'sql' => "FROM $wpdb->comments WHERE comment_type = 'trackback'",
                'group' => 'comment'
            ),
            'meta_post' => array(
                'name_singular' => __('post meta', 'wuco'),
                'name_plural' => __('post meta', 'wuco'),
                'descr' => __('Orphaned meta data that links to the posts that do not exist', 'wuco'),
                'sql' => "meta FROM $wpdb->postmeta meta LEFT JOIN $wpdb->posts posts ON posts.ID = meta.post_id WHERE posts.ID IS NULL",
                'group' => 'meta'
            ),
            'meta_comment' => array(
                'name_singular' => __('comment meta', 'wuco'),
                'name_plural' => __('comment meta', 'wuco'),
                'descr' => __('Orphaned meta data that links to the comments that do not exist', 'wuco'),
                'sql' => "meta FROM $wpdb->commentmeta meta LEFT JOIN $wpdb->comments comments ON comments.comment_ID = meta.comment_id WHERE comments.comment_ID IS NULL",
                'group' => 'meta'
            ),
            'transient' => array(
                'name_singular' => __('transient data', 'wuco'),
                'name_plural' => __('transient data', 'wuco'),
                'descr' => __('Temporary data that might be stored in the database'),
                'sql' => "FROM $wpdb->options WHERE option_name LIKE '_site_transient_browser_%' OR option_name LIKE '_site_transient_timeout_browser_%' OR option_name LIKE '_transient_feed_%' OR option_name LIKE '_transient_timeout_feed_%'",
                'group' => 'other'
            )
        );

        add_action('init', array($this, 'init'));
    }

    function init(){
        add_action('admin_post_' . $this->action, array($this, 'submitForm'));
        add_action($this->event, array($this, 'runScheduledCleanup'));
        add_filter('wuco_admin_message', array($this, 'displayMessage'), 10, 3);
    }

    function renderForm(){
        $title = __('Database Cleanup', 'wuco');
//        $description = __('Remove the clutter from your database by deleting the obsolete and useless data');
        $description = '';

        $scheduled = get_option('wuco_' . $this->action . '_schedule_types');
        $scheduled = !empty($scheduled) ? $scheduled : array();

        $content = '<form id="wuco-form-' . $this->action . '" method="post" action="' . admin_url('admin-post.php') . '">';
        $content .= '<input type="hidden" name="action" value="' . $this->action . '" />';
        $content .= wp_nonce_field($this->action, $this->action . '_nonce', true, false);
        $content .= '<ul class="wuco-checkbox-list">';

        foreach($this->groups as $group){
            $totalEntries = 0;
            $typeNames = array();

            $items = '<ul>';
            foreach($this->entryTypes as $id => $type){
                if($type['group'] == $group['id']){
                    $entries = $this->countEntries($id);
                    $totalEntries += $entries;
                    $typeNames[] = ucfirst($type['name_plural']);
                    $class = $entries > 0 ? '' : 'empty';

                    $items .= '<li><div class="display-table">';
                    $items .= '<div class="checkbox"><input type="checkbox" name="types[]" value="' . $id . '" /></div>';
                    $items .= '<div class="number"><span class="wuco-badge ' . $class . '">' . $entries . '</span></div>';
                    $items .= '<div class="name"><h5>' . ucfirst($type['name_plural']) .'</h5><p class="description">' . $type['descr'] . '</p></div>';
                    $items .= '<div class="scheduled">';
                    if(in_array($id, $scheduled))
                        $items .= '<div class="dashicons dashicons-clock" title="' . __('Scheduled for the next automatic cleanup', 'wuco') . '"></div>';
                    $items .= '</div>';
                    $items .= '</div></li>';
                }
            }
            $items .= '</ul>';

            $class = $totalEntries > 0 ? '' : 'empty';

            $content .= '<li>';
            $content .= '<div class="display-table">';
            $content .= '<div class="checkbox"><input type="checkbox" name="groups[]" value="' . $group['id'] . '" /></div>';
            $content .= '<div class="number"><span class="wuco-badge ' . $class . '">' . $totalEntries . '</span></div>';
            $content .= '<div class="name"><h4>' . $group['name'] . '</h4><p class="description">' . implode(', ', $typeNames) . '</p></div>';
            $content .= '</div>';
            $content .= $items;
            $content .= '</li>';
        }

        $content .= '</ul>';

        $content .= $this->renderControls();

        $content .= '</form>';

        // @todo Move this part to wuco_widget_database_cleanup()
        $widget = new wucoAdminWidget($this->action, $title, $description, $content);
        $widget->displayWidget();
    }


    function renderControls(){

        $content = '<div class="wuco-admin-widget-controls wuco-row">';

        $content .= '<div class="alignleft"><input type="submit" class="wuco-admin-widget-submit button button-primary button-large right" name="run_cleanup" value="' . __('Clean up now', 'wuco') . '" /></div>';

        $content .= '<div class="alignright">';

        $schedule = wp_next_scheduled($this->event);
        if(!$schedule){
            $intervals = wp_get_schedules();
            $options = array();
            foreach($intervals as $key => $value){
                if(in_array($key, $this->intervals)){
                    $options[] = '<option value="' . $key . '">' . $value['display'] . '</option>';
                }
            }
            $content .= '<select name="schedule_interval">' . implode('', $options) . '</select>';
            $content .= '<button class="wuco-admin-widget-submit button button-secondary button-large right" name="schedule_cleanup">' . __('Schedule cleanup', 'wuco') . '</button>';
        } else {
            $content .= '<span>' . sprintf(__('Next cleanup is scheduled on %s', 'wuco'), get_date_from_gmt(date_i18n('Y-m-d H:i:s', $schedule), get_option('date_format'))) . '</span>';
            $content .= '<button class="wuco-admin-widget-submit button button-secondary button-large right" name="unschedule_cleanup">' . __('Unschedule cleanup', 'wuco') . '</button>';
        }
        $content .= '</div>';

        $content .= '</div>';

        return $content;
    }


    function submitForm(){

        check_admin_referer($this->action, $this->action . '_nonce');

        // The following is a fallback for the non-js browsers where the nested checkboxes are not checked
        // automatically if the parent checkbox is checked
        if(!empty($_REQUEST['groups'])){
            $groups = $_REQUEST['groups'];
            $types = array();
            foreach($this->groups as $group){
                $groupId = $group['id'];
                if(in_array($groupId, $groups)){
                    foreach($this->entryTypes as $typeId => $type){
                        if($type['group'] == $groupId)
                            $types[] = $typeId;
                    }
                }
            }

            $types = array_unique(array_merge($_REQUEST['types'], $types));
            $_REQUEST['types'] = $types;
        }

        $vars = array(
            'status' => 'error'
        );

        if(isset($_REQUEST['run_cleanup'])){
            $vars['action'] = $this->action . '_run';
            if(!empty($_REQUEST['types'])){
                $vars['removed'] = $this->runCleanup($_REQUEST['types']);
                $vars['status'] = 'success';
            }
        }

        elseif(isset($_REQUEST['schedule_cleanup'])){
            $vars['action'] = $this->action . '_schedule';
            if(!empty($_REQUEST['types'])) {
                $this->scheduleCleanup($_REQUEST['schedule_interval'], $_REQUEST['types']);
                $vars['status'] = 'success';
                $vars['reload'] = 1;
            }
        }

        elseif(isset($_REQUEST['unschedule_cleanup'])){
            $vars['action'] = $this->action . '_unschedule';
            $this->unscheduleCleanup();
            $vars['status'] = 'success';
        }

        wp_redirect(admin_url('admin.php?page=wuco&' . http_build_query($vars)));
        exit;

    }


    function runCleanup($types){
        if(empty($types))
            return false;

        global $wpdb;

        // Reversing the $types in order to delete the orphaned meta entries first
        $types = array_reverse($types);
        $deleted = array('total' => 0);

        foreach($types as $type){
            $result = $wpdb->query("DELETE " . $this->entryTypes[$type]['sql']);
            $group = $this->entryTypes[$type]['group'];
            $deleted['total'] += $result;
            $deleted[$group] += $result;
            $deleted[$type] += $result;
        }

        // Deleting posts and comments directly from the database may leave orphaned meta.
        // We have to delete it as well, but we need to do it separately from deleting the rest of the meta trash as if it never existed.
        if($deleted['post'] > 0)
            $wpdb->query("DELETE " . $this->entryTypes['meta_post']['sql']);

        if($deleted['comment'] > 0)
            $wpdb->query("DELETE " . $this->entryTypes['meta_comment']['sql']);

        $this->updateInfo($deleted);

        wuco_plugin_update_status($this->action . '_time', time());
        wuco_plugin_update_status($this->action . '_type', 'manual');

        return $deleted['total'];
    }


    function runScheduledCleanup(){
        $types = get_option('wuco_' . $this->action . '_schedule_types');

        $this->runCleanup($types);

        wuco_plugin_update_status($this->action . '_type', 'automatic');
    }

    function scheduleCleanup($interval, $types){

        if(!wp_next_scheduled($this->event)){
            wp_schedule_event(time(), $interval, $this->event);
        }

        update_option('wuco_' . $this->action . '_schedule_types', $types);
    }


    function unscheduleCleanup(){
        $time = wp_next_scheduled($this->event);
        if($time !== false)
            wp_unschedule_event($time, $this->event);

        delete_option('wuco_' . $this->action . '_schedule_types');
    }


    function displayMessage($message, $status, $action){
        if($status == 'success'){
            switch($action){
                case $this->action . '_run':
                    $deleted = $_REQUEST['removed'];
                    $message = sprintf(__('You have successfully cleaned out <b>%d</b> entries from your database', 'wuco'), $deleted);
                    break;

                case $this->action . '_schedule':
                    $message = __('The cleanup has been successfully scheduled!', 'wuco');
                    break;

                case $this->action . '_unschedule':
                    $message = __('The cleanup has been successfully unscheduled!', 'wuco');
                    break;
            }
        }

        return $message;
    }


    function countEntries($type){
        global $wpdb;
        $number = $wpdb->get_var("SELECT COUNT(*) " . $this->entryTypes[$type]['sql']);
        return $number;
    }


    function updateInfo($deleted){
        $optionName = 'wuco_' . $this->action . '_done';
        $done = get_option($optionName);

        if(!empty($done) && is_array($done)){
            $last = array();
            foreach($deleted as $key => $value){
                $done['total'][$key] += $value;
                $last[$key] = $value;
            }
            $done['last'] = $last;
        }

        else {
            $done['total'] = $deleted;
            $done['last'] = $deleted;
        }

        update_option($optionName, $done);
    }
}

global $wucoDatabaseCleanup;
$wucoDatabaseCleanup = new wucoDatabaseCleanup();