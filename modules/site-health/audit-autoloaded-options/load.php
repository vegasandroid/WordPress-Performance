<?php
/**
 * Module Name: Audit Autoloaded Options
 * Description: Adds a check for autoloaded options in Site Health status.
 * Experimental: Yes
 *
 * @package performance-lab
 * @since 1.0.0
 */

/**
 * Adds test to site health.
 *
 * @since 1.0.0
 *
 * @param array $tests Site Health Tests.
 * @return array
 */
function perflab_aao_add_autoloaded_options_test( $tests ) {
	$tests['direct']['autoloaded_options'] = array(
		'label' => __( 'Autoloaded options', 'performance-lab' ),
		'test'  => 'perflab_aao_autoloaded_options_test',
	);
	return $tests;
}
add_filter( 'site_status_tests', 'perflab_aao_add_autoloaded_options_test' );

/**
 * Callback for autoloaded_options test.
 *
 * @since 1.0.0
 *
 * @return array
 */
function perflab_aao_autoloaded_options_test() {

	$autoloaded_options_size  = perflab_aao_autoloaded_options_size();
	$autoloaded_options_count = count( wp_load_alloptions() );

	$base_description = __( 'Autoloaded options are configuration settings for plugins and themes that are automatically loaded with every page load in WordPress. Having too many autoloaded options can slow down your site.', 'performance-lab' );

	$result = array(
		'label'       => esc_html__( 'Autoloaded options are acceptable', 'performance-lab' ),
		'status'      => 'good',
		'badge'       => array(
			'label' => esc_html__( 'Performance', 'performance-lab' ),
			'color' => 'blue',
		),
		'description' => sprintf(
			/* translators: 1. Number of autoloaded options. 2. Autoloaded options size. */
			'<p>' . esc_html( $base_description ) . ' ' . esc_html__( 'Your site has %1$s autoloaded options (size: %2$s) in the options table, which is acceptable.', 'performance-lab' ) . '</p>',
			$autoloaded_options_count,
			size_format( $autoloaded_options_size )
		),
		'actions'     => '',
		'test'        => 'autoloaded_options',
	);

	/**
	 * Filters max bytes threshold to trigger warning in Site Health.
	 *
	 * @since 1.0.0
	 *
	 * @param int $limit Autoloaded options threshold size. Default 800000.
	 */
	$limit = apply_filters( 'perflab_aao_autoloaded_options_limit_size_in_bytes', 800000 );

	if ( $autoloaded_options_size < $limit ) {
		return $result;
	}

	$result['status']         = 'critical';
	$result['badge']['color'] = 'red';
	$result['label']          = esc_html__( 'Autoloaded options could affect performance', 'performance-lab' );
	$result['description']    = sprintf(
		/* translators: 1. Number of autoloaded options. 2. Autoloaded options size. */
		'<p>' . esc_html( $base_description ) . ' ' . esc_html__( 'Your site has %1$s autoloaded options (size: %2$s) in the options table, which could cause your site to be slow. You can reduce the number of autoloaded options by cleaning up your site\'s options table.', 'performance-lab' ) . '</p>',
		$autoloaded_options_count,
		size_format( $autoloaded_options_size )
	) . perflab_aao_autoloaded_options_table();

	/**
	 * Filters description to be shown on Site Health warning when threshold is met.
	 *
	 * @since 1.0.0
	 *
	 * @param string $description Description message when autoloaded options bigger than threshold.
	 */
	$result['description'] = apply_filters( 'perflab_aao_autoloaded_options_limit_description', $result['description'] );

	$result['actions'] = sprintf(
	/* translators: 1: HelpHub URL. 2: Link description. */
		'<p><a target="_blank" href="%1$s">%2$s</a></p>',
		esc_url( __( 'https://wordpress.org/support/article/optimization/#database-tuning', 'performance-lab' ) ),
		esc_html__( 'More info about performance optimization', 'performance-lab' )
	);

	/**
	 * Filters actionable information to tackle the problem. It can be a link to an external guide.
	 *
	 * @since 1.0.0
	 *
	 * @param string $actions Call to Action to be used to point to the right direction to solve the issue.
	 */
	$result['actions'] = apply_filters( 'perflab_aao_autoloaded_options_action_to_perform', $result['actions'] );
	return $result;
}

/**
 * Calculate total amount of autoloaded data.
 *
 * @since 1.0.0
 *
 * @return int autoloaded data in bytes.
 */
function perflab_aao_autoloaded_options_size() {
	global $wpdb;
	return (int) $wpdb->get_var( 'SELECT SUM(LENGTH(option_value)) FROM ' . $wpdb->prefix . 'options WHERE autoload = \'yes\'' );
}

/**
 * Fetches autoload top list.
 *
 * @since n.e.x.t
 *
 * @return array Autoloaded data as option names and their sizes.
 */
function perflab_aao_query_autoloaded_options() {
	global $wpdb;

	$option_threshold = apply_filters( 'perflab_aao_autoloaded_options_table_threshold', 100 );

	var_dump( $wpdb->options );

	return $wpdb->get_results( $wpdb->prepare( "SELECT option_name, LENGTH(option_value) AS option_value_length FROM {$wpdb->options} WHERE autoload='yes' AND LENGTH(option_value) > %d ORDER BY option_value_length DESC LIMIT 20", $option_threshold ) );
}

/**
 * Gets formatted autoload options table.
 *
 * @since n.e.x.t
 *
 * @return string HTML formatted table.
 */
function perflab_aao_get_autoloaded_options_table() {
	$autoload_summary = perflab_aao_query_autoloaded_options();

	$html_table = sprintf(
		'<table class="widefat striped"><thead><tr><th scope="col">%s</th><th scope="col">%s</th></tr></thead><tbody>',
		esc_html__( 'Option Name', 'performance-lab' ),
		esc_html__( 'Size (bytes)', 'performance-lab' )
	);

	foreach ( $autoload_summary as $value ) {
		$html_table .= sprintf( '<tr><td>%s</td><td>%s</td></tr>', $value->option_name, $value->option_value_length );
	}
	$html_table .= '</tbody></table>';

	return $html_table;
}
