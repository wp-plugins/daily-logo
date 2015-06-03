<?php
/*
Plugin Name: Daily logo
Description: Daily logo is a simple and flexible plugin which allow users to display a different header/logo in their site every day.
Author: Andrea Landonio
Author URI: http://www.andrealandonio.it
Text Domain: daily_logo
Domain Path: /languages/
Version: 1.0.0
License: GPL v3

Daily logo
Copyright (C) 2013-2014, Andrea Landonio - landonio.andrea@gmail.com

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/***************************************************
INCLUDES
 ***************************************************/

require_once( __DIR__ . '/daily-logo-constants.php' );
require_once( __DIR__ . '/daily-logo-settings.php' );
require_once( __DIR__ . '/daily-logo-utils.php' );

/***************************************************
PLUGIN ACTIVATION
 ***************************************************/

/**
 * Register activation hook
 */
function daily_logo_activation() {
	global $wpdb;
    $table_name = $wpdb->prefix . DLP_DB_TABLE;

    // If table does not exists, create it
    if ( $wpdb->get_var( "show tables like '$table_name'" ) != $table_name ) {

        $sql = "CREATE TABLE " . $table_name . " (
              id int(9) NOT NULL AUTO_INCREMENT,
              blog_id int(9) NOT NULL,
              logo_name varchar(255) NOT NULL,
              logo_day int(2) NOT NULL,
              logo_month int(2) NOT NULL,
              logo_year int(4) NOT NULL,
              logo_link varchar(255),
              logo_target int(1) NOT NULL,
              logo_image varchar(255),
              logo_image_alternative varchar(255),
              logo_class varchar(255),
              PRIMARY KEY (ID)
        )";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	    $wpdb->query($sql);
    }
}
register_activation_hook( WP_PLUGIN_DIR . '/daily-logo/daily-logo.php', 'daily_logo_activation' );

/***************************************************
PLUGIN DEACTIVATION
 ***************************************************/

/**
 * Register deactivation hook
 */
function daily_logo_deactivation() {
	global $wpdb;
    $table_name = $wpdb->prefix . DLP_DB_TABLE;

	// If table is empty, remove it
    $table_rows = $wpdb->get_var( 'SELECT COUNT(*) FROM ' . $table_name );

    if ( $table_rows == 0 ) {
        // Drop table
        $wpdb->query( 'DROP TABLE IF EXISTS ' . $table_name );

	    delete_option( DLP_OPTION_DATA );
	    delete_option( DLP_OPTION_SETTINGS );
    }
}
register_deactivation_hook( WP_PLUGIN_DIR . '/daily-logo/daily-logo.php', 'daily_logo_deactivation' );

/***************************************************
PLUGIN ACTIONS
 ***************************************************/

/**
 * Add menu settings
 */
function daily_logo_setting_menu() {
    // Register stylesheet
    wp_register_style( 'daily_logo_style', plugins_url( 'daily-logo/css/daily-logo.css' ) );
    wp_enqueue_style( 'daily_logo_style' );

    // Add menu pages
	$menu = add_menu_page( __( 'Daily logo' ), __( 'Daily logo' ), 'manage_options', DLP_MENU, 'daily_logo_menu_page' );
	$submenu = add_submenu_page( DLP_MENU, __( 'Settings' ), __( 'Settings' ), 'manage_options', DLP_MENU_SETTINGS, 'daily_logo_menu_page_settings' );

	// Add actions to enqueue style and scripts
	add_action( 'admin_print_styles-' . $menu, 'daily_logo_admin_custom_css' );
	add_action( 'admin_print_styles-' . $submenu, 'daily_logo_admin_custom_css' );

	add_action( 'admin_print_scripts-' . $menu, 'daily_logo_admin_custom_js' );
	add_action( 'admin_print_scripts-' . $submenu, 'daily_logo_admin_custom_js' );
}
add_action( 'admin_menu', 'daily_logo_setting_menu' );

/**
 * Enqueue styles
 */
function daily_logo_admin_custom_css() {
	// Enqueue date picker CSS
	// wp_enqueue_style( 'jquery-ui-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css' );

	// Enqueue thickbox CSS
	wp_enqueue_style( 'thickbox');
}

/**
 * Enqueue scripts
 */
function daily_logo_admin_custom_js() {
	wp_enqueue_script( 'jquery-ui-datepicker' );
	wp_enqueue_script( 'media-upload' );
	wp_enqueue_script( 'thickbox' );
	wp_enqueue_script( 'jquery-validate', plugins_url( 'daily-logo/js/jquery.validate.min.js' ), array('jquery'), '1.10.0', true );
	wp_register_script( 'daily_logo_script', plugins_url( 'daily-logo/js/daily-logo.js' ), array('jquery', 'media-upload', 'thickbox' ) );
	wp_enqueue_script( 'daily_logo_script' );
}
