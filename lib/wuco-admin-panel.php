<?php

/**
 * Class wucoAdminPanel
 *
 * @since 1.0.0
 */
class wucoAdminPanel{

    function __construct(){
        $this->slug = 'wuco';
        $this->template_dir = WUCO_PATH . 'templates/';

        add_action('admin_menu', array(&$this, 'addMenuPrimary'), 0);
//        add_action('admin_menu', array(&$this, 'addMenuSecondary'), 100);
    }

    function addMenuPrimary(){
        add_menu_page(__('WUCO - WordPress Ultimate Cleanup & Optimization', 'wuco'), __('WUCO', 'wuco'), 'manage_options', $this->slug, array(&$this, 'addPage'), null);
//        add_submenu_page($this->slug, __('WUCO Dashboard', 'wuco'), __('Dashboard', 'wuco'), 'manage_options', $this->slug, array(&$this, 'addPage'));
    }

    function addMenuSecondary(){
        add_submenu_page($this->slug, __('WUCO About', 'wuco'), __('About', 'wuco'), 'manage_options', $this->slug . '-about', array(&$this, 'addPage'));
    }

    function addPage(){
        $page = $_REQUEST['page'];

        if($page == $this->slug)
            $page = $this->slug . '-dashboard';

        if(file_exists($this->template_dir . $page . '.php'))
            require_once($this->template_dir . $page . '.php');
    }
}

$wucoAdminPanel = new wucoAdminPanel();