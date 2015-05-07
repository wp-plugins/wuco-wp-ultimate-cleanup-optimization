<?php

/**
 * Class wucoPluginStatus
 * 
 * @since 20
 */
class wucoPluginStatus {
    
    function __construct(){
        $this->option = 'wuco_plugin_status';
    }
    
    function getStatus($key = null){
        $status = get_option($this->option);
        
        if(empty($status))
            return false;
        
        if(empty($key))
            return $status;
        else {
            if(!empty($status[$key]))
                return $status[$key];
            else
                return false;
        }
    }
    
    function updateStatus($key, $value){
        $status = get_option($this->option);
        $status[$key] = $value;

        $updated = update_option($this->option, $status);
        return $updated;
    }
}

global $wucoPluginStatus;
$wucoPluginStatus = new wucoPluginStatus();