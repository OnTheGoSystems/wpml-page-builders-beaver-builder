<?php

/**
 * @group media
 */
class Test_WPML_Beaver_Builder_Media_Node_Photo extends OTGS_TestCase {

	/**
	 * @test
	 */
	public function it_should_translate() {
		$source_lang     = 'en';
		$target_lang     = 'fr';
		$original_id     = mt_rand( 1, 10 );
		$translated_id   = mt_rand( 11, 20 );
		$original_url    = 'http://example.org/dog.jpg';
		$translated_url  = 'http://example.org/chien.jpg';
		$original_data   = array( 'original data' );
		$translated_data = array( 'translated data' );

		$node = (object) array(
			'photo'     => $original_id,
			'photo_src' => $original_url,
			'data'      => $original_data,
		);

		$expected_node = (object) array(
			'photo'     => $translated_id,
			'photo_src' => $translated_url,
			'data'      => $translated_data,
		);

		\WP_Mock::userFunction( 'wp_prepare_attachment_for_js', array(
			'args'   => array( $translated_id ),
			'return' => $translated_data,
		));

		$translate_helper = $this->get_translate_helper();
		$translate_helper->method( 'translate_id' )->with( $original_id, $target_lang )->willReturn( $translated_id );
		$translate_helper->method( 'translate_image_url' )
		                 ->with( $original_url, $source_lang, $target_lang )->willReturn( $translated_url );

		$subject = $this->get_subject( $translate_helper );

		$this->assertEquals( $expected_node, $subject->translate( $node, $source_lang, $target_lang ) );
	}

	/**
	 * @test
	 */
	public function it_should_not_translate_if_the_image_is_not_translated() {
		$source_lang   = 'en';
		$target_lang   = 'fr';
		$original_id   = mt_rand( 1, 10 );
		$original_url  = 'http://example.org/dog.jpg';
		$original_data = array( 'original data' );

		$node = (object) array(
			'photo'     => $original_id,
			'photo_src' => $original_url,
			'data'      => $original_data,
		);

		$translate_helper = $this->get_translate_helper();
		$translate_helper->method( 'translate_id' )->with( $original_id, $target_lang )->willReturn( $original_id );
		$translate_helper->expects( $this->never() )->method( 'translate_image_url' );

		$subject = $this->get_subject( $translate_helper );

		$this->assertEquals( $node, $subject->translate( $node, $source_lang, $target_lang ) );
	}

	private function get_subject( $translate_helper ) {
		return new WPML_Beaver_Builder_Media_Node_Photo( $translate_helper );
	}

	private function get_translate_helper() {
		return $this->getMockBuilder( 'WPML_Page_Builders_Media_Translate_Helper' )
			->setMethods( array( 'translate_id', 'translate_image_url' ) )
			->disableOriginalConstructor()->getMock();
	}
}
