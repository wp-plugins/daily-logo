<?php
/***************************************************
INCLUDES
 ***************************************************/

require_once( __DIR__ . '/daily-logo-constants.php' );

/***************************************************
DATABASE FUNCTIONS
 ***************************************************/

/**
 * Retrieve rows
 *
 * @return mixed
 */
function daily_logo_get_rows() {
	global $wpdb;
	$table_name = $wpdb->prefix . DLP_DB_TABLE;

	// Retrieve rows
	$rows = $wpdb->get_results( 'SELECT * FROM ' . $table_name . ' ORDER BY logo_year DESC, logo_month DESC, logo_day DESC' );

	return $rows;
}

/**
 * Show rows
 *
 * @param $rows
 */
function daily_logo_show_rows( $rows ) {
    $i = 0;

    // Loop over rows
    foreach ( $rows as $row ) {
        ?>
        <tr id="logo-<?php echo $row->id ?>" <?php echo ( $i % 2 == 0 ) ? 'class="alternate"' : '' ?> valign="top">
            <td class="label-column"><?php echo $row->logo_name ?></td>
            <td class="date-column"><?php echo $row->logo_year ?>-<?php echo daily_logo_format_digit_date( $row->logo_month ) ?>-<?php echo daily_logo_format_digit_date( $row->logo_day ) ?></td>
	        <td class="label-column"><?php echo ( ! empty( $row->logo_link ) ? __( 'yes', DLP_PREFIX ) : __( '-', DLP_PREFIX ) ) ?></td>
	        <td class="label-column">
		        <?php echo ( ! empty( $row->logo_image ) ? '<img src="' . $row->logo_image . '" alt="' . $row->logo_name . '" class="daily-image" />' : __( '-', DLP_PREFIX ) ) ?>
	        </td>
	        <td class="label-column">
		        <?php echo ( ! empty( $row->logo_image_alternative ) ? '<img src="' . $row->logo_image_alternative . '" alt="' . $row->logo_name . '" class="daily-image" />' : __( '-', DLP_PREFIX ) ) ?>
	        </td>
	        <td class="label-column"><?php echo ( ! empty( $row->logo_class ) ? $row->logo_class : '-' ) ?></td>
            <td class="options-column">
                <a href="javascript:void(0)" class="modify_row" onclick="modify_row(<?php echo $row->id ?>)"><?php _e( 'Modify', DLP_PREFIX ) ?></a>&nbsp;
                <a href="javascript:void(0)" class="delete_row" onclick="delete_row(<?php echo $row->id ?>)"><?php _e( 'Delete', DLP_PREFIX ) ?></a>
            </td>
        </tr>
		<?php
        $i++;
    }
}

/**
 * Format digit date appending the leading 0
 *
 * @param $value
 *
 * @return string
 */
function daily_logo_format_digit_date($value) {
	return $value < 10 ? "0" . $value : $value;
}

/**
 * Insert row
 *
 * @param $blog_id
 * @param $name
 * @param $year
 * @param $month
 * @param $day
 * @param $link
 * @param $target
 * @param $image
 * @param $image_alternative
 * @param $class
 *
 * @return mixed
 */
function daily_logo_insert_row( $blog_id, $name, $year, $month, $day, $link, $target, $image, $image_alternative, $class ) {
	global $wpdb;
	$table_name = $wpdb->prefix . DLP_DB_TABLE;

	// Insert row
	$rows_affected = $wpdb->insert(
		$table_name,
		array(
            'blog_id' => $blog_id,
			'logo_name' => $name,
			'logo_year' => $year,
			'logo_month' => $month,
			'logo_day' => $day,
			'logo_link' => $link,
            'logo_target' => $target,
			'logo_image' => $image,
            'logo_image_alternative' => $image_alternative,
			'logo_class' => $class
		)
	);
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $rows_affected );

	// Return affected db rows
	return $rows_affected;
}

/**
 * Modify row
 *
 * @param $id
 * @param $blog_id
 * @param $name
 * @param $year
 * @param $month
 * @param $day
 * @param $link
 * @param $target
 * @param $image
 * @param $image_alternative
 * @param $class
 */
function daily_logo_modify_row( $id, $blog_id, $name, $year, $month, $day, $link, $target, $image, $image_alternative, $class ) {
	global $wpdb;
	$table_name = $wpdb->prefix . DLP_DB_TABLE;

	// Update row
	$rows_affected = $wpdb->update(
		$table_name,
		array(
			'blog_id' => $blog_id,
			'logo_name' => $name,
			'logo_year' => $year,
			'logo_month' => $month,
			'logo_day' => $day,
			'logo_link' => $link,
			'logo_target' => $target,
			'logo_image' => $image,
			'logo_image_alternative' => $image_alternative,
			'logo_class' => $class,
		),
		array( 'id' => $id ),
		array(
			'%d',
			'%s',
			'%d',
			'%d',
			'%d',
			'%s',
			'%d',
			'%s',
			'%s',
			'%s'
		),
		array( '%d' )
	);

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $rows_affected );

	// Return affected db rows
	return $rows_affected;
}

/**
 * Update DB option
 */
function daily_logo_update_option_data() {
	// Get rows
	$rows = daily_logo_get_rows();
	$logo_array = array();

	// Loop over rows
	$logo = NULL;
	foreach ( $rows as $row ) {
		// Create Logo object
		$logo = new Daily_Logo( $row );

		// Add Logo object to logos array (only current or future logos)
		$logo_date = new DateTime( $logo->year . '-' . $logo->month . '-' . $logo->day );
		$now_date = new DateTime();
		$interval = intval( $now_date->diff( $logo_date )->format( '%R%a' ) );

		// Add only daily and future logos
		if ( $interval >= -1 ) $logo_array[] = $logo;
	}

	// Manage DB option
	if ( get_option( DLP_OPTION_DATA ) !== false ) {
		// The option already exists, update it
		update_option( DLP_OPTION_DATA, $logo_array );
	}
	else {
		// The option hasn't been added yet, add it
		add_option( DLP_OPTION_DATA, $logo_array );
	}
}