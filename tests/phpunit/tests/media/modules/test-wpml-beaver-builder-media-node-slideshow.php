<?php

/**
 * @group media
 */
class Test_WPML_Beaver_Builder_Media_Node_Slideshow extends OTGS_TestCase {

	/**
	 * @test
	 */
	public function it_should_translate() {
		$source_lang   = 'en';
		$target_lang   = 'fr';
		$original_id   = mt_rand( 1, 10 );
		$translated_id = mt_rand( 11, 20 );

		$node = (object) array(
			'photos' => array( $original_id ),
			'photo_data' => array(
				$original_id => (object) array(
					'caption'    => 'caption',
					'largeURL'   => 'largeURL',
					'x3largeURL' => 'x3largeURL',
					'thumbURL'   => 'thumbURL',
				),
			),
		);

		$expected_node = (object) array(
			'photos' => array( $translated_id ),
			'photo_data' => array(
				$translated_id => (object) array(
					'caption'    => $target_lang . 'caption',
					'largeURL'   => $target_lang . 'largeURL',
					'x3largeURL' => $target_lang . 'x3largeURL',
					'thumbURL'   => $target_lang . 'thumbURL',
				),
			),
		);

		\WP_Mock::userFunction( 'wp_prepare_attachment_for_js', array(
			'args'   => array( $translated_id ),
			'return' => array(
				'caption' => $target_lang . 'caption',
			),
		));

		$media_translate = $this->get_media_translate();
		$media_translate->method( 'translate_id' )->with( $original_id, $target_lang )->willReturn( $translated_id );
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
	public function it_should_not_translate_if_the_image_is_not_translated() {
		$source_lang = 'en';
		$target_lang = 'fr';
		$original_id = mt_rand( 1, 10 );

		$node = (object) array(
			'photos' => array( $original_id ),
			'photo_data' => array(
				$original_id => (object) array(
					'caption'    => 'caption',
					'largeURL'   => 'largeURL',
					'x3largeURL' => 'x3largeURL',
					'thumbURL'   => 'thumbURL',
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
			array( (object) array( 'photos_property_not_set' ) ),
			array( (object) array( 'photos' => 'not_an_array' ) ),
		);
	}

	private function get_subject( $media_translate ) {
		return new WPML_Beaver_Builder_Media_Node_Slideshow( $media_translate );
	}

	private function get_media_translate() {
		return $this->getMockBuilder( 'WPML_Page_Builders_Media_Translate' )
		            ->setMethods( array( 'translate_id', 'translate_image_url' ) )
		            ->disableOriginalConstructor()->getMock();
	}
}
