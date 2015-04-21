<?php

/**
 * Class wucoDatabaseCleanup
 * 
 * @since 1.0.0
 */
class wucoDatabaseCleanup {

    function __construct(){

        $this->action = 'database_cleanup';
        $this->entryTypes = array(
            array(
                'group' => 'posts',
                'label' => __('Posts', 'wuco'),
                'types' => array(
                    array(
                        'id' => 'post_revision',
                        'name_singular' => __('revision', 'wuco'),
                        'name_plural' => __('revisions', 'wuco'),
                        'descr' => __('Records of each saved draft or published update', 'wuco'),
                    ),
                    array(
                        'id' => 'post_draft',
                        'name_singular' => __('draft', 'wuco'),
                        'name_plural' => __('drafts', 'wuco'),
                        'descr' => __('Posts that are saved but not published', 'wuco')
                    ),
                    array(
                        'id' => 'post_autodraft',
                        'name_singular' => __('auto draft', 'wuco'),
                        'name_plural' => __('auto drafts', 'wuco'),
                        'descr' => __('Drafts that are saved automatically', 'wuco')
                    ),
                    array(
                        'id' => 'post_trash',
                        'name_singular' => __('trashed post', 'wuco'),
                        'name_plural' => __('trashed posts', 'wuco'),
                        'descr' => __('Posts that have been moved to trash', 'wuco')
                    )
                )
            ),
            array(
                'group' => 'comments',
                'label' => __('Comments', 'wuco'),
                'types' => array(
                    array(
                        'id' => 'comment_spam',
                        'name_singular' => __('spam comment', 'wuco'),
                        'name_plural' => __('spam comments', 'wuco'),
                        'descr' => __('Comments that have been marked as spam', 'wuco')
                    ),
                    array(
                        'id' => 'comment_trash',
                        'name_singular' => __('trashed comment', 'wuco'),
                        'name_plural' => __('trashed comments', 'wuco'),
                        'descr' => __('Comments that have been moved to trash', 'wuco')
                    ),
                    array(
                        'id' => 'comment_moderate',
                        'name_singular' => __('moderated comment', 'wuco'),
                        'name_plural' => __('moderated comments', 'wuco'),
                        'descr' => __('Comments waiting to be approved')
                    ),
                    array(
                        'id' => 'comment_pingback',
                        'name_singular' => __('pingback', 'wuco'),
                        'name_plural' => __('pingbacks', 'wuco'),
                        'descr' => __('Comments that are created automatically when someone links to your posts', 'wuco')
                    ),
                    array(
                        'id' => 'comment_trackback',
                        'name_singular' => __('trackback', 'wuco'),
                        'name_plural' => __('trackbacks', 'wuco'),
                        'descr' => __('Comments that are created when someone notifies you they have linked to your post', 'wuco')
                    )
                )
            ),
            array(
                'group' => 'meta',
                'label' => __('Meta data', 'wuco'),
                'types' => array(
                    array(
                        'id' => 'meta_post',
                        'name_singular' => __('post meta', 'wuco'),
                        'name_plural' => __('post meta', 'wuco'),
                        'descr' => __('Orphaned meta data that links to the posts that do not exist', 'wuco')
                    ),
                    array(
                        'id' => 'meta_comment',
                        'name_singular' => __('comment meta', 'wuco'),
                        'name_plural' => __('comment meta', 'wuco'),
                        'descr' => __('Orphaned meta data that links to the comments that do not exist', 'wuco')
                    )
                )
            ),
            array(
                'group' => 'other',
                'label' => __('Other data', 'wuco'),
                'types' => array(
                    array(
                        'id' => 'transient',
                        'name_singular' => __('transient data', 'wuco'),
                        'name_plural' => __('transient data', 'wuco'),
                        'descr' => __('Temporary data that might be stored in the database')
                    )
                )
            ),
        );

        add_action('init', array($this, 'init'));
    }

    function init(){
        add_action('admin_post_' . $this->action, array($this, 'submitForm'));
        add_filter('wuco_admin_message_updated_' . $this->action, array($this, 'messageUpdated'));
    }

    function renderForm(){
        $title = __('Database Cleanup', 'wuco');
        $description = __('Remove the clutter from your database by deleting the obsolete and useless data');

        $content = '<form id="wuco-form-' . $this->action . '" method="post" action="' . admin_url('admin-post.php') . '">';
        $content .= '<input type="hidden" name="action" value="' . $this->action . '" />';
        $content .= wp_nonce_field($this->action, $this->action . '_nonce', true, false);
        $content .= '<ul class="wuco-checkbox-list">';

        foreach($this->entryTypes as $group){
            $totalEntries = 0;
            $typeNames = array();

            $items = '<ul>';
            foreach($group['types'] as $type){
                $entries = $this->countEntries($type['id']);
                $totalEntries += $entries;
                $typeNames[] = ucfirst($type['name_plural']);
                $class = $entries > 0 ? '' : 'empty';

                $items .= '<li><div class="display-table">';
                $items .= '<div class="checkbox"><input type="checkbox" name="' . $type['id'] . '" value="1" /></div>';
                $items .= '<div class="number"><span class="wuco-badge ' . $class . '">' . $entries . '</span></div>';
                $items .= '<div class="name"><h5>' . ucfirst($type['name_plural']) .'</h5><p class="description">' . $type['descr'] . '</p></div>';
                $items .= '</div></li>';
            }
            $items .= '</ul>';

            $class = $totalEntries > 0 ? '' : 'empty';

            $content .= '<li>';
            $content .= '<div class="display-table">';
            $content .= '<div class="checkbox"><input type="checkbox" name="' . $group['group'] . '" value="1" /></div>';
            $content .= '<div class="number"><span class="wuco-badge ' . $class . '">' . $totalEntries . '</span></div>';
            $content .= '<div class="name"><h4>' . $group['label'] . '</h4><p class="description">' . implode(', ', $typeNames) . '</p></div>';
//        $content .= '<div class="collapse-handle"></div>';
            $content .= '</div>';
            $content .= $items;
            $content .= '</li>';
        }

        $content .= '</ul>';

        $content .= '<div class="wuco-admin-widget-actions wuco-row">';
        $content .= '<div class="alignleft"><label><input type="checkbox" name="backup" value="1" checked />' . __('I swear I have a backup copy of my database', 'wuco') . '</label></div>';
        $content .= '<div class="alignright"><input type="submit" class="wuco-admin-widget-submit button button-primary button-large right" value="' . __('Clean up', 'wuco') . '" /><span class="spinner" style="display: none;"></span></div>';
        $content .= '</div>';

        $content .= '</form>';

        $widget = new wucoAdminWidget($this->action, $title, $description, $content);
        $widget->displayWidget();
    }

    function submitForm(){

        check_admin_referer($this->action, $this->action . '_nonce');

        // The following is a fallback for the non-js browsers where the nested checkboxes are not checked
        // automatically if the parent checkbox is checked
        foreach($this->entryTypes as $group){
            $id = $group['group'];
            if(!empty($_REQUEST[$id])){
                foreach($group['types'] as $type){
                    $_REQUEST[$type['id']] = true;
                }
            }
        }

        global $wpdb;
        $deleted = array('total' => 0);


        // Cleaning up the meta

        if(!empty($_REQUEST['meta_post'])){
            $result = $wpdb->query("DELETE meta FROM $wpdb->postmeta meta LEFT JOIN $wpdb->posts posts ON posts.ID = meta.post_id WHERE posts.ID IS NULL");
            $deleted['total'] += $result;
            $deleted['meta'] += $result;
            $deleted['meta_post'] += $result;
        }

        if(!empty($_REQUEST['meta_comment'])){
            $result = $wpdb->query("DELETE meta FROM $wpdb->commentmeta meta LEFT JOIN $wpdb->comments comments ON comments.comment_ID = meta.comment_id WHERE comments.comment_ID IS NULL");
            $deleted['total'] += $result;
            $deleted['meta'] += $result;
            $deleted['meta_comment'] += $result;
        }


        // Cleaning up the posts

        if(!empty($_REQUEST['post_revision'])){
            $result = $wpdb->query("DELETE FROM $wpdb->posts WHERE post_type = 'revision'");
            $deleted['total'] += $result;
            $deleted['post'] += $result;
            $deleted['post_revision'] += $result;
        }

        if(!empty($_REQUEST['post_draft'])){
            $result = $wpdb->query("DELETE FROM $wpdb->posts WHERE post_status = 'draft'");
            $deleted['total'] += $result;
            $deleted['post'] += $result;
            $deleted['post_draft'] += $result;
        }

        if(!empty($_REQUEST['post_autodraft'])){
            $result = $wpdb->query("DELETE FROM $wpdb->posts WHERE post_status = 'auto-draft'");
            $deleted['total'] += $result;
            $deleted['post'] += $result;
            $deleted['post_autodraft'] += $result;
        }

        if(!empty($_REQUEST['post_trash'])){
            $result = $wpdb->query("DELETE FROM $wpdb->posts WHERE post_status = 'trash'");
            $deleted['total'] += $result;
            $deleted['post'] += $result;
            $deleted['post_trash'] += $result;
        }


        // Cleaning up the comments

        if(!empty($_REQUEST['comment_spam'])){
            $result = $wpdb->query("DELETE FROM $wpdb->comments WHERE comment_approved = 'spam'");
            $deleted['total'] += $result;
            $deleted['comment'] += $result;
            $deleted['comment_spam'] += $result;
        }

        if(!empty($_REQUEST['comment_trash'])){
            $result = $wpdb->query("DELETE FROM $wpdb->comments WHERE comment_approved = 'trash'");
            $deleted['total'] += $result;
            $deleted['comment'] += $result;
            $deleted['comment_trash'] += $result;
        }

        if(!empty($_REQUEST['comment_moderate'])){
            $result = $wpdb->query("DELETE FROM $wpdb->comments WHERE comment_approved = '0'");
            $deleted['total'] += $result;
            $deleted['comment'] += $result;
            $deleted['comment_moderate'] += $result;
        }

        if(!empty($_REQUEST['comment_pingback'])){
            $result = $wpdb->query("DELETE FROM $wpdb->comments WHERE comment_type = 'pingback'");
            $deleted['total'] += $result;
            $deleted['comment'] += $result;
            $deleted['comment_pingback'] += $result;
        }

        if(!empty($_REQUEST['comment_trackback'])){
            $result = $wpdb->query("DELETE FROM $wpdb->comments WHERE comment_type = 'trackback'");
            $deleted['total'] += $result;
            $deleted['comment'] += $result;
            $deleted['comment_trackback'] += $result;
        }


        // Cleaning up miscellaneous data

        if(!empty($_REQUEST['transient'])){
            $result = $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_site_transient_browser_%' OR option_name LIKE '_site_transient_timeout_browser_%' OR option_name LIKE '_transient_feed_%' OR option_name LIKE '_transient_timeout_feed_%'");
            $deleted['total'] += $result;
            $deleted['other'] += $result;
            $deleted['transient'] += $result;
        }


        // Deleting posts and comments directly from the database may leave orphaned meta.
        // We have to delete it as well, but we need to do it separately from deleting the rest of the meta trash as if it never existed.
        if($deleted['post'] > 0)
            $wpdb->query("DELETE meta FROM $wpdb->postmeta meta LEFT JOIN $wpdb->posts posts ON posts.ID = meta.post_id WHERE posts.ID IS NULL");

        if($deleted['comment'] > 0)
            $wpdb->query("DELETE meta FROM $wpdb->commentmeta meta LEFT JOIN $wpdb->comments comments ON comments.comment_ID = meta.comment_id WHERE comments.comment_ID IS NULL");


        $this->updateInfo($deleted);

        wp_redirect(admin_url('admin.php?page=wuco&action=' . $this->action . '&updated=1'));
        exit;

    }

    function messageUpdated($message){
        $done = get_option('wuco_' . $this->action . '_done');

        if(empty($done) || !is_array($done))
            return false;

        $doneLastTotal = $done['last']['total'];
        if(empty($doneLastTotal))
            return false;

        $message = sprintf(__('Great job! You have successfully cleaned out <b>%d</b> entries from your database'), $doneLastTotal);
        return $message;
    }

    function countEntries($type){
        global $wpdb;
        $number = 0;

        switch($type){

            case 'post_revision':
                $number = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = 'revision'");
                break;

            case 'post_draft':
                $number = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'draft'");
                break;

            case 'post_autodraft':
                $number = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'auto-draft'");
                break;

            case 'post_trash':
                $number = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'trash'");
                break;

            case 'comment_spam':
                $number = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = 'spam'");
                break;

            case 'comment_trash':
                $number = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = 'trash'");
                break;

            case 'comment_moderate':
                $number = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = '0'");
                break;

            case 'comment_pingback':
                $number = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_type = 'pingback'");
                break;

            case 'comment_trackback':
                $number = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_type = 'trackback'");
                break;

            case 'meta_post':
                $number = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->postmeta meta LEFT JOIN $wpdb->posts posts ON posts.ID = meta.post_id WHERE posts.ID IS NULL");
                break;

            case 'meta_comment':
                $number = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->commentmeta meta LEFT JOIN $wpdb->comments comments ON comments.comment_ID = meta.comment_id WHERE comments.comment_ID IS NULL");
                break;

            case 'transient':
                $number = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->options WHERE option_name LIKE '_site_transient_browser_%' OR option_name LIKE '_site_transient_timeout_browser_%' OR option_name LIKE '_transient_feed_%' OR option_name LIKE '_transient_timeout_feed_%'");
                break;
        }

        return $number;
    }

    function getEntryTypes(){
        return $this->entryTypes;
    }

    function updateInfo($deleted){
        $optionName = 'wuco_' . $this->action . '_done';

        $done = get_option($optionName);

        if(!empty($done) && is_array($done)){
            foreach($deleted as $key => $value){
                $done['total'][$key] += $value;
                $done['last'][$key] = $value;
            }
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