<?php
/**
 * Tests for dominant-color module.
 *
 * @since 1.2.0
 *
 * @package performance-lab
 * @group   dominant-color
 */

use PerformanceLab\Tests\TestCase\DominantColorTestCase;

class Dominant_Color_Image_Editor_Imageick_Test extends DominantColorTestCase {
	public function set_up() {
		parent::set_up(); // TODO: Change the autogenerated stub.

		add_filter(
			'wp_image_editors',
			static function () {
				return array( 'Dominant_Color_Image_Editor_Imagick' );
			},
			100
		);
	}

	/**
	 * Test if the function returns the correct color.
	 *
	 * @dataProvider provider_get_dominant_color
	 *
	 * @covers Dominant_Color_Image_Editor_GD::get_dominant_color
	 */
	public function test_get_dominant_color( $image_path, $expected_color, $is_wp_error ) {

		$attachment_id = $this->factory->attachment->create_upload_object( $image_path );
		wp_maybe_generate_attachment_metadata( get_post( $attachment_id ) );
		$color = dominant_color_get_dominant_color( $attachment_id );
		if ( ! $is_wp_error ) {
			$this->assertContains( $color, $expected_color );
		} else {
			$this->assertInstanceOf( 'WP_Error', $color );
		}
	}

	/**
	 * Test if the function returns the correct color.
	 *
	 * @dataProvider provider_get_has_transparency
	 *
	 * @covers ::dominant_color_has_transparency
	 */
	public function test_dominant_color_get_has_transparency( $image_path, $expected_tranasparency ) {
		$attachment_id = $this->factory->attachment->create_upload_object( $image_path );
		wp_maybe_generate_attachment_metadata( get_post( $attachment_id ) );
		$this->assertEquals( $expected_tranasparency, dominant_color_has_transparency( $attachment_id ) );
	}
}
