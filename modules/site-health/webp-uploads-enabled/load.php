<?php
/**
 * Module Name: Web Uploads Support
 * Description: Add WebP support check in Site Health checks.
 * Experimental: No
 *
 * @package performance-lab
 * @since 1.0.0
 */

/**
 * Adds tests to site health.
 *
 * @since 1.0.0
 *
 * @param array $tests Site Health Tests.
 * @return array
 */
function webp_uploads_add_is_webp_supported_test( $tests )
{
	$tests['direct']['webp_supported']  = array(
		'label' => esc_html__( 'WebP Support', 'performance-lab' ),
		'test'  => 'webp_uploads_check_webp_supported_test',
	);
	return $tests;
}
add_filter( 'site_status_tests', 'webp_uploads_add_is_webp_supported_test' );

/**
 * Callback for webp_enabled test.
 *
 * @since 1.0.0
 *
 * @return array
 */
function webp_uploads_check_webp_supported_test() {
	$result = array(
		'label'       => __( 'Your site supports WebP' ),
		'status'      => 'good',
		'badge'       => array(
			'label' => __( 'Performance' ),
			'color' => 'blue',
		),
		'description' => sprintf(
			'<p>%s</p>',
			__( 'WebP image format is used by WordPress to improve the performance of your site by generating smaller images than it usually could with JPEG format. This means your pages will load faster and consume less bandwidth from users.' )
		),
		'actions'     => '',
		'test'        => 'is_webp_uploads_enabled',
	);

	$gd_supported = false;
	if ( function_exists( 'gd_info' ) ) {
		$gd = gd_info();
		if ( isset( $gd[ 'WebP Support' ] ) && $gd[ 'WebP Support' ] ) {
			$gd_supported = true;
		}
	}

	try {
		$formats = Imagick::queryFormats( '*' );

		if( in_array('WEBP', $formats ) ) {
			$gd_supported = true;
		}
	} catch (Exception $e) {
	}

	if ( ! $gd_supported ) {
		$result['status'] = 'critical';
		$result['label']  = __( 'Your site does not support WebP' );
		$result['actions'] = sprintf(
			'<p>%s</p>',
			/* translators: Accessibility text. */
			__( 'Please contact your host and ask them to add WebP support' )
		);
	}

	return $result;
}
