<?php
/*
 * Plugin Name: Plugin Modification Date
 * Description: Displays Plugin Updation time. Useful when you have multiple plugins, and you are not sure which plugin is last modified.
 * Version: 1.0.1
 * Author: kiranpatil353, clarionwpdeveloper
 * License: GPLv2
 */
//Class
class Pmd_Plugin_Modification_Date {

    private $options = array();
	
	// set up initial actions
    public function __construct() {
        add_filter('manage_plugins_columns', array($this, 'pmd_plugins_columns'));
        add_action('admin_head-plugins.php', array($this, 'pmd_column_css_styles'));
        add_action('manage_plugins_custom_column', array($this, 'pmd_activated_columns'), 10, 3);
    }
	
	// create column
    public function pmd_plugins_columns($columns) {
		
        $columns['last_modified_date'] = __('Last Modified', 'pmdate');
        return $columns;
    }
// Main Processing of plugin files based on recursive modification time 
    public function pmd_activated_columns($column_name, $plugin_file, $plugin_data) {
        $first = '';
        $plugin_base_name = plugin_basename($plugin_file);
		//Plugins folder path 
        $plugins_url = ABSPATH . 'wp-content/plugins/';
		// extract plugin folder name 
        if (isset($plugin_base_name)) {
            $arr = explode("/", $plugin_base_name, 2);
            $first = $arr[0];
        }
        if ($first != '') {

            if (is_dir($plugins_url . "/" . $first)) {
                echo date("F d Y H:i:s.", $this->pmd_folder_modification_time($plugins_url . "/" . $first));
            } else {
                echo date("F d Y H:i:s.", filemtime($plugins_url . "/" . $first));
            }
        }
    }
	// column style
    public function pmd_column_css_styles() {
        ?>
        <style>#last_modified_date{ width: 18%; }</style>
        <?php

    }
// Recursive folder file modification time 
    public function pmd_folder_modification_time($dir) {
        $foldermtime = 0;

        $flags = FilesystemIterator::SKIP_DOTS | FilesystemIterator::CURRENT_AS_FILEINFO;
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, $flags));

        while ($it->valid()) {
            if (($filemtime = $it->current()->getMTime()) > $foldermtime) {
                $foldermtime = $filemtime;
            }
            $it->next();
        }

        return $foldermtime ? : false;
    }

}
// Initiate the plugin. 
$GLOBALS['pmd_plugin_modification_date'] = new Pmd_Plugin_Modification_Date;
