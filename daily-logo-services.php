<?php
/***************************************************
INCLUDES
 ***************************************************/

require_once( __DIR__ . '/daily-logo-constants.php' );
require_once( __DIR__ . '/daily-logo-database.php' );
require_once( __DIR__ . '/classes/daily-logo.php' );

/***************************************************
SERVICES FUNCTIONS
 ***************************************************/

/**
 * Get row service callback
 */
function daily_logo_get_row_callback() {
	global $wpdb;
	$table_name = $wpdb->prefix . DLP_DB_TABLE;

	// Check nonce
	if ( !wp_verify_nonce( $_REQUEST['nonce'], DLP_NONCE ) ) {
		die( 'No naughty business please' );
	}

	// Read parameters
	$id = sanitize_text_field( $_POST[ 'id' ] );

	// Get rows
	$rows = $wpdb->get_results( 'SELECT * FROM ' . $table_name . ' WHERE id = ' . $id );

	// Loop over rows
	$logo = NULL;
	foreach ( $rows as $row ) {
		// Create Logo object
		$logo = new Daily_Logo($row);
	}

	// Return JSON response
	wp_send_json($logo);
}
add_action( 'wp_ajax_daily_logo_get_row', 'daily_logo_get_row_callback' );
add_action( 'wp_ajax_nopriv_daily_logo_get_row', 'daily_logo_get_row_callback' );

/**
 * Save row service callback
 */
function daily_logo_save_row_callback() {
	// Check nonce
	if ( !wp_verify_nonce( $_REQUEST['nonce'], DLP_NONCE ) ) {
		die( 'No naughty business please' );
	}

	// Read data parameters
	$id = (int) sanitize_text_field( $_POST[ 'id' ] );
	$blog_id = (int) sanitize_text_field( $_POST[ 'blog_id' ] );
	$name = (string) sanitize_text_field( $_POST[ 'name' ] );
	$link = (string) sanitize_text_field( $_POST[ 'link' ] );
	$target = (int) sanitize_text_field( $_POST[ 'target' ] );
	$image = (string) sanitize_text_field( $_POST[ 'image' ] );
	$image_alternative = (string) sanitize_text_field( $_POST[ 'image_alternative' ] );
	$class = (string) sanitize_text_field( $_POST[ 'class' ] );

	// Convert date
    $date = (string) sanitize_text_field( $_POST[ 'date' ] );
    $year = substr( $date, 0, 4 );
    $month = substr( $date, 5, 2 );
    $day = substr( $date, 8, 2 );

	// Check id value to detect if action is an insert or an update
	if (is_int($id) && $id != 0) {
		// Modify row
		daily_logo_modify_row( $id, $blog_id, $name, $year, $month, $day, $link, $target, $image, $image_alternative, $class );
	}
	else {
		// Insert row
		daily_logo_insert_row( $blog_id, $name, $year, $month, $day, $link, $target, $image, $image_alternative, $class );
	}

	// Update rows in option DB field
	daily_logo_update_option_data();

	// Get rows
	$rows = daily_logo_get_rows();

	// Show rows in table
	daily_logo_show_rows( $rows );

	die();
}
add_action( 'wp_ajax_daily_logo_save_row', 'daily_logo_save_row_callback' );
add_action( 'wp_ajax_nopriv_daily_logo_save_row', 'daily_logo_save_row_callback' );

/**
 * Delete row service callback
 */
function daily_logo_remove_row_callback() {
    global $wpdb;
	$table_name = $wpdb->prefix . DLP_DB_TABLE;

	// Check nonce
	if ( !wp_verify_nonce( $_REQUEST['nonce'], DLP_NONCE ) ) {
		die( 'No naughty business please' );
	}

	// Read parameters
	$id = sanitize_text_field( $_POST[ 'id' ] );

	// Delete row
	$return = $wpdb->delete( $table_name, array( 'id' => $id ) );

	// Update rows in option DB field
	daily_logo_update_option_data();

	// Return JSON response
	wp_send_json($return);
}
add_action( 'wp_ajax_daily_logo_remove_row', 'daily_logo_remove_row_callback' );
add_action( 'wp_ajax_nopriv_daily_logo_remove_row', 'daily_logo_remove_row_callback' );
