<?php

/**
 * @group media
 */
class Test_WPML_Beaver_Builder_Media_Node_Gallery extends OTGS_TestCase {

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
				(object) array(
					'id'          => $original_id,
					'alt'         => 'alt',
					'caption'     => 'caption',
					'description' => 'description',
					'title'       => 'title',
					'src'         => 'url',
					'link'        => 'url',
				),
			),
		);

		$expected_node = (object) array(
			'photos' => array( $translated_id ),
			'photo_data' => array(
				(object) array(
					'id'          => $translated_id,
					'alt'         => $target_lang . ' alt',
					'caption'     => $target_lang . ' caption',
					'description' => $target_lang . ' description',
					'title'       => $target_lang . ' title',
					'src'         => $target_lang . ' url',
					'link'        => $target_lang . ' url',
				),
			),
		);

		\WP_Mock::userFunction( 'wp_prepare_attachment_for_js', array(
			'args'   => array( $translated_id ),
			'return' => array(
				'alt'         => $target_lang . ' alt',
				'caption'     => $target_lang . ' caption',
				'description' => $target_lang . ' description',
				'title'       => $target_lang . ' title',
				'url'         => $target_lang . ' url',
			),
		));

		$translate_helper = $this->get_translate_helper();
		$translate_helper->method( 'translate_id' )->with( $original_id, $target_lang )->willReturn( $translated_id );

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

		$node = (object) array(
			'photos' => array( $original_id ),
			'photo_data' => array(
				(object) array(
					'id'          => $original_id,
					'alt'         => 'alt',
					'caption'     => 'caption',
					'description' => 'description',
					'title'       => 'title',
					'src'         => 'url',
					'link'        => 'url',
				),
			),
		);

		$translate_helper = $this->get_translate_helper();
		$translate_helper->method( 'translate_id' )->with( $original_id, $target_lang )->willReturn( $original_id );

		$subject = $this->get_subject( $translate_helper );

		$this->assertEquals( $node, $subject->translate( $node, $source_lang, $target_lang ) );
	}

	private function get_subject( $translate_helper ) {
		return new WPML_Beaver_Builder_Media_Node_Gallery( $translate_helper );
	}

	private function get_translate_helper() {
		return $this->getMockBuilder( 'WPML_Page_Builders_Media_Translate_Helper' )
		            ->setMethods( array( 'translate_id', 'translate_image_url' ) )
		            ->disableOriginalConstructor()->getMock();
	}
}
