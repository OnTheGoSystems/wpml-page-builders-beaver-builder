<?php

/**
 * @group media
 */
class Test_WPML_Beaver_Builder_Media_Node_Content_Slider extends OTGS_TestCase {

	/**
	 * @test
	 */
	public function it_should_translate() {
		$source_lang     = 'en';
		$target_lang     = 'fr';
		$original_id_1   = mt_rand( 1, 10 );
		$translated_id_1 = mt_rand( 11, 20 );
		$original_id_2   = mt_rand( 101, 110 );
		$translated_id_2 = mt_rand( 111, 120 );

		$node = (object) array(
			'slides' => array(
				(object) array(
					'bg_photo_src' => 'bg src',
					'fg_photo_src' => 'fg src',
					'r_photo_src'  => 'r src',
					'bg_photo'     => '',
					'fg_photo'     => '',
				),
				(object) array(
					'r_photo_src' => '',
					'bg_photo'    => $original_id_1,
					'fg_photo'    => $original_id_2,
					'r_photo'     => $original_id_1,
				),
			),
		);

		$expected_node = (object) array(
			'slides' => array(
				(object) array(
					'bg_photo_src' => $target_lang . 'bg src',
					'fg_photo_src' => $target_lang . 'fg src',
					'r_photo_src'  => $target_lang . 'r src',
					'bg_photo'     => '',
					'fg_photo'     => '',
				),
				(object) array(
					'r_photo_src' => '',
					'bg_photo'    => $translated_id_1,
					'fg_photo'    => $translated_id_2,
					'r_photo'     => $translated_id_1,
				),
			),
		);

		$id_map = array(
			array( $original_id_1, $target_lang, $translated_id_1 ),
			array( $original_id_2, $target_lang, $translated_id_2 ),
		);

		$media_translate = $this->get_media_translate();
		$media_translate->method( 'translate_id' )->willReturnMap( $id_map );
		$media_translate->method( 'translate_image_url' )
		                ->willReturnCallback( function( $url, $lang_to, $lang_from ) use ( $target_lang, $source_lang ) {
			                if ( $lang_to === $target_lang && $lang_from === $source_lang ) {
				                return $target_lang . $url;
			                }

			                return $url;
		                });

		$subject = $this->get_subject( $media_translate );

		$this->assertEquals( $expected_node, $subject->translate( $node, $target_lang, $source_lang ) );
	}

	/**
	 * @test
	 */
	public function it_should_not_translate_if_the_slide_is_not_translated() {
		$source_lang = 'en';
		$target_lang = 'fr';
		$original_id = mt_rand( 1, 10 );

		$node = (object) array(
			'slides' => array(
				(object) array(
					'bg_photo' => $original_id,
				),
			),
		);

		$expected_node = clone $node;

		$media_translate = $this->get_media_translate();
		$media_translate->method( 'translate_id' )->with( $original_id, $target_lang )->willReturn( $original_id );

		$subject = $this->get_subject( $media_translate );

		$this->assertEquals( $expected_node, $subject->translate( $node, $target_lang, $source_lang ) );
	}

	/**
	 * @test
	 * @dataProvider dp_invalid_node
	 *
	 * @param stdClass $node
	 */
	public function it_should_not_translate_if_photos_property_is_invalid( $node ) {
		$source_lang = 'en';
		$target_lang = 'fr';

		$expected_node = clone $node;

		$media_translate = $this->get_media_translate();
		$media_translate->expects( $this->never() )->method( 'translate_id' );

		$subject = $this->get_subject( $media_translate );

		$this->assertEquals( $expected_node, $subject->translate( $node, $target_lang, $source_lang ) );
	}

	public function dp_invalid_node() {
		return array(
			array( (object) array( 'slides_property_not_set' ) ),
			array( (object) array( 'slides' => 'not_an_array' ) ),
		);
	}

	private function get_subject( $media_translate ) {
		return new WPML_Beaver_Builder_Media_Node_Content_Slider( $media_translate );
	}

	private function get_media_translate() {
		return $this->getMockBuilder( 'WPML_Page_Builders_Media_Translate' )
		            ->setMethods( array( 'translate_id', 'translate_image_url' ) )
		            ->disableOriginalConstructor()->getMock();
	}
}
