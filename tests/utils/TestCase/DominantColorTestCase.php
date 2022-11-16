<?php

namespace PerformanceLab\Tests\TestCase;

use WP_UnitTestCase;

abstract class DominantColorTestCase extends WP_UnitTestCase {
	/**
	 * Data provider for test_get_dominant_color_GD.
	 *
	 * @return array
	 */
	public function provider_get_dominant_color() {
		return array(
			'animated_gif'  => array(
				'image_path'            => TESTS_PLUGIN_DIR . '/tests/testdata/modules/images/dominant-color/animated.gif',
				'expected_color'        => array( '874e4e', 'df7f7f' ),
				'expected_transparency' => true,
			),
			'red_jpg'       => array(
				'image_path'            => TESTS_PLUGIN_DIR . '/tests/testdata/modules/images/dominant-color/red.jpg',
				'expected_color'        => array( 'ff0000', 'fe0000' ),
				'expected_transparency' => false,
			),
			'green_jpg'     => array(
				'image_path'            => TESTS_PLUGIN_DIR . '/tests/testdata/modules/images/dominant-color/green.jpg',
				'expected_color'        => array( '00ff00', '00ff01', '02ff01' ),
				'expected_transparency' => false,
			),
			'white_jpg'     => array(
				'image_path'            => TESTS_PLUGIN_DIR . '/tests/testdata/modules/images/dominant-color/white.jpg',
				'expected_color'        => array( 'ffffff' ),
				'expected_transparency' => false,
			),

			'red_gif'       => array(
				'image_path'            => TESTS_PLUGIN_DIR . '/tests/testdata/modules/images/dominant-color/red.gif',
				'expected_color'        => array( 'ff0000' ),
				'expected_transparency' => false,
			),
			'green_gif'     => array(
				'image_path'            => TESTS_PLUGIN_DIR . '/tests/testdata/modules/images/dominant-color/green.gif',
				'expected_color'        => array( '00ff00' ),
				'expected_transparency' => false,
			),
			'white_gif'     => array(
				'image_path'            => TESTS_PLUGIN_DIR . '/tests/testdata/modules/images/dominant-color/white.gif',
				'expected_color'        => array( 'ffffff' ),
				'expected_transparency' => false,
			),
			'trans_gif'     => array(
				'image_path'            => TESTS_PLUGIN_DIR . '/tests/testdata/modules/images/dominant-color/trans.gif',
				'expected_color'        => array( '5a5a5a', '020202' ),
				'expected_transparency' => true,
			),

			'red_png'       => array(
				'image_path'            => TESTS_PLUGIN_DIR . '/tests/testdata/modules/images/dominant-color/red.png',
				'expected_color'        => array( 'ff0000' ),
				'expected_transparency' => false,
			),
			'green_png'     => array(
				'image_path'            => TESTS_PLUGIN_DIR . '/tests/testdata/modules/images/dominant-color/green.png',
				'expected_color'        => array( '00ff00' ),
				'expected_transparency' => false,
			),
			'white_png'     => array(
				'image_path'            => TESTS_PLUGIN_DIR . '/tests/testdata/modules/images/dominant-color/white.png',
				'expected_color'        => array( 'ffffff' ),
				'expected_transparency' => false,
			),
			'trans_png'     => array(
				'image_path'            => TESTS_PLUGIN_DIR . '/tests/testdata/modules/images/dominant-color/trans.png',
				'expected_color'        => array( '000000' ),
				'expected_transparency' => true,
			),

			'red_webp'      => array(
				'image_path'            => TESTS_PLUGIN_DIR . '/tests/testdata/modules/images/dominant-color/red.webp',
				'expected_color'        => array( 'ff0000' ),
				'expected_transparency' => false,
			),
			'green_webp'    => array(
				'image_path'            => TESTS_PLUGIN_DIR . '/tests/testdata/modules/images/dominant-color/green.webp',
				'expected_color'        => array( '00ff00' ),
				'expected_transparency' => false,
			),
			'white_webp'    => array(
				'image_path'            => TESTS_PLUGIN_DIR . '/tests/testdata/modules/images/dominant-color/white.webp',
				'expected_color'        => array( 'ffffff' ),
				'expected_transparency' => false,
			),
			'trans_webp'    => array(
				'image_path'            => TESTS_PLUGIN_DIR . '/tests/testdata/modules/images/dominant-color/trans.webp',
				'expected_color'        => array( '000000' ),
				'expected_transparency' => true,
			),
			'balloons_webp' => array(
				'image_path'            => TESTS_PLUGIN_DIR . '/tests/testdata/modules/images/balloons.webp',
				'expected_color'        => array( 'c1bbb9', 'c3bdbd' ),
				'expected_transparency' => false,
			),
		);
	}

	/**
	 * Data provider for test_get_dominant_color_GD.
	 *
	 * @return array
	 */
	public function provider_get_dominant_color_invalid_images() {
		return array(
			'tiff' => array(
				'image_path'            => TESTS_PLUGIN_DIR . '/tests/testdata/modules/images/dominant-color/test-image.tiff',
				'expected_color'        => array( 'dfdfdf' ),
				'expected_transparency' => true,
			),
			'bmp'  => array(
				'image_path'            => TESTS_PLUGIN_DIR . '/tests/testdata/modules/images/dominant-color/test-image.bmp',
				'expected_color'        => array( 'dfdfdf' ),
				'expected_transparency' => true,
			),
		);
	}

	/**
	 * Data provider for test_get_dominant_color_GD.
	 *
	 * @return array
	 */
	public function provider_get_dominant_color_none_images() {
		return array(
			'svg' => array(
				'files_path' => TESTS_PLUGIN_DIR . '/tests/testdata/modules/images/dominant-color/video-play.svg',
			),
			'pdf' => array(
				'files_path' => TESTS_PLUGIN_DIR . '/tests/testdata/modules/images/dominant-color/wordpress-gsoc-flyer.pdf',
			),
			'mp4' => array(
				'files_path' => TESTS_PLUGIN_DIR . '/tests/testdata/modules/images/dominant-color/small-video.mp4',
			),
		);
	}
}
