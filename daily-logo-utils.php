<?php
/***************************************************
INCLUDES
 ***************************************************/

require_once( __DIR__ . '/daily-logo-constants.php' );

/***************************************************
UTILS FUNCTIONS
 ***************************************************/

/**
 * Search logo by date
 *
 * @param int $year
 * @param int $month
 * @param int $day
 *
 * @return Daily_Logo
 */
function daily_logo_search_logo( $year, $month, $day ) {
	// Retrieve rows from DB option
	$rows = get_option( DLP_OPTION_DATA );

	// Loop over rows
	$logo = NULL;
	if ( ! empty( $rows ) ) {
		foreach ( $rows as $row ) {
			// Check logo date
			if ( $row->year == $year && $row->month == $month && $row->day == $day ) {
				// Date logo founded
				$logo = $row;
			}
		}	
	}	

	return $logo;
}

/**
 * Create HTML snippet for displaying logo
 *
 * @param Daily_Logo $logo
 * @param boolean $alternative
 *
 * @return string
 */
function daily_logo_create_logo_snippet( $logo, $alternative = FALSE ) {
	// Get logo snippet
	$snippet = daily_logo_get_logo_snippet( $logo, $alternative );

	// With custom logo replace template placeholders
	if ( ! empty( $logo ) ) {
		// Check conditional blocks
		if ( strpos( $snippet, '[?]##HAS_IMAGE##[?]' ) !== false ) {
			$snippet_query_position = strpos( $snippet, '[?]##HAS_IMAGE##[?]' );
			$snippet_query_length = strlen( '[?]##HAS_IMAGE##[?]' );
			$snippet_switch_position = strpos( $snippet, '[:]' );
			$snippet_switch_length = strlen( '[:]' );
			$snippet_end_position = strpos( $snippet, '[;]' );
			$snippet_end_length = strlen( '[;]' );

			// Get snippet for true/false value
			$snippet_condition_true = substr( $snippet, $snippet_query_position + $snippet_query_length, $snippet_switch_position - ( $snippet_query_position + $snippet_query_length ) );
			$snippet_condition_false = substr( $snippet, $snippet_switch_position + $snippet_switch_length, $snippet_end_position - ( $snippet_switch_position + $snippet_switch_length ) );

			// Get snippet for all the condition
			$snippet_condition_all = substr( $snippet, $snippet_query_position, $snippet_end_position + $snippet_end_length - $snippet_query_position );

			// Manage the right condition
			if ( ( ! $alternative && ! empty( $logo->image ) ) || ( $alternative && ! empty( $logo->image_alternative ) ) ) $snippet = str_replace( $snippet_condition_all, $snippet_condition_true, $snippet );
			else $snippet = str_replace( $snippet_condition_all, $snippet_condition_false, $snippet );
		}

		// Manage ##LINK## placeholder
		if ( ! empty( $logo->link ) ) $snippet = str_replace( '##LINK##', $logo->link, $snippet );
		else $snippet = str_replace( '##LINK##', 'javascript:void(0)', $snippet );

		// Manage ##NAME## placeholder
		if ( ! empty( $logo->name ) ) $snippet = str_replace( '##NAME##', $logo->name, $snippet );
		else $snippet = str_replace( '##NAME##', '', $snippet );

		// Manage ##TARGET## placeholder
		if ( $logo->target === 1 ) $snippet = str_replace( '##TARGET##', '_blank', $snippet );
		else $snippet = str_replace( '##TARGET##', '_self', $snippet );

		// Manage ##CLASS## placeholder
		if ( ! empty( $logo->class ) ) $snippet = str_replace( '##CLASS##', $logo->class, $snippet );
		else $snippet = str_replace( '##CLASS##', '', $snippet);

		// Manage ##IMAGE## placeholder
		if ( ! $alternative && ! empty( $logo->image ) ) $snippet = str_replace( '##IMAGE##', $logo->image, $snippet );
		else if ( $alternative && ! empty( $logo->image_alternative ) ) $snippet = str_replace( '##IMAGE##', $logo->image_alternative, $snippet );
		else $snippet = str_replace( '##IMAGE##', '', $snippet );
	}

	return $snippet;
}

/**
 * Get HTML snippet for displaying logo
 *
 * @param Daily_Logo $logo
 * @param boolean $alternative
 *
 * @return string
 */
function daily_logo_get_logo_snippet( $logo, $alternative = FALSE ) {
	// Get settings options
	$options = get_option( DLP_OPTION_SETTINGS );

	if ( ! empty ( $logo ) ) {
		// Retrieve custom logo template
		if ($alternative) $snippet = ( ! empty( $options->alternative_template_with_logo ) ) ? $options->alternative_template_with_logo : DLP_ALTERNATIVE_TEMPLATE_DEFAULT;
		else $snippet = ( ! empty( $options->template_with_logo ) ) ? $options->template_with_logo : DLP_TEMPLATE_DEFAULT;
	}
	else {
		// Retrieve standard logo template
		if ($alternative) $snippet = ( ! empty( $options->alternative_template_without_logo ) ) ? $options->alternative_template_without_logo : DLP_STANDARD_ALTERNATIVE_TEMPLATE_DEFAULT;
		else $snippet = ( ! empty( $options->template_without_logo ) ) ? $options->template_without_logo : DLP_STANDARD_TEMPLATE_DEFAULT;
	}

	return $snippet;
}

/**
 * Display today logo
 *
 * @return string
 */
function daily_logo_show_today() {
	// Get today
	$year = date( 'Y' );
	$month = date( 'm' );
	$day = date( 'd' );

	// Search logo for today
	$logo = daily_logo_search_logo( $year, $month, $day );

	// Return logo snippet
	echo daily_logo_create_logo_snippet( $logo );
}
add_action( 'daily_logo_show_today', 'daily_logo_show_today' );

/**
 * Display today logo
 *
 * @return string
 */
function daily_logo_show_today_alternative() {
	// Get today
	$year = date( 'Y' );
	$month = date( 'm' );
	$day = date( 'd' );

	// Search logo for today
	$logo = daily_logo_search_logo( $year, $month, $day );

	// Return logo snippet
	echo daily_logo_create_logo_snippet( $logo, TRUE );
}
add_action( 'daily_logo_show_today_alternative', 'daily_logo_show_today_alternative' );

/**
 * Display date logo
 *
 * @param $year
 * @param $month
 * @param $day
 *
 * @return string
 */
function daily_logo_show_date( $year, $month, $day ) {
	// Search logo for date
	$logo = daily_logo_search_logo( $year, $month, $day );

	// Return logo snippet
	echo daily_logo_create_logo_snippet( $logo );
}
add_action( 'daily_logo_show_date', 'daily_logo_show_date', 10, 3 );

/**
 * Display date logo
 *
 * @param $year
 * @param $month
 * @param $day
 *
 * @return string
 */
function daily_logo_show_date_alternative( $year, $month, $day ) {
	// Search logo for date
	$logo = daily_logo_search_logo( $year, $month, $day );

	// Return logo snippet
	echo daily_logo_create_logo_snippet( $logo, TRUE );
}
add_action( 'daily_logo_show_date_alternative', 'daily_logo_show_date_alternative', 10, 3 );
