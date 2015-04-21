<?php

/**
 * Class wucoAdminWidget
 *
 * @since 1.0.0
 */
class wucoAdminWidget{

    public $widget;
    public $widgetId;
    public $widgetTitle;
    public $widgetDescription;
    public $widgetContent;

    function __construct($id, $title, $description, $content){
        $this->widgetId = $id;
        $this->widgetTitle = $title;
        $this->widgetDescription = $description;
        $this->widgetContent = $content;
    }

    function displayWidget($echo = true){
        $this->widget = '<section id="wuco-admin-widget-' . $this->widgetId . '" class="wuco-admin-widget">';

        $this->widget .= '<header class="wuco-admin-widget-header">';
        $this->widget .= '<h3>' . $this->widgetTitle . '</h3>';
        $this->widget .= !empty($this->widgetDescription) ? '<p class="description">' . $this->widgetDescription . '</p>' : '';
        $this->widget .= '</header>';

        $this->widget .= '<div class="wuco-admin-widget-content">' . $this->widgetContent . '</div>';

        $this->widget .= '</section>';

        if($echo)
            echo $this->widget;
        else
            return $this->widget;
    }
}