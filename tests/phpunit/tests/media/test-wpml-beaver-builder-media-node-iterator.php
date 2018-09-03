<?php

/**
 * @group media
 */
class Test_WPML_Beaver_Builder_Media_Node_Iterator extends OTGS_TestCase {

	/**
	 * @test
	 */
	public function it_should_translate() {
		$lang        = 'fr';
		$source_lang = 'en';

		$photo_data = (object) array(
			'type' => 'photo',
		);

		$gallery_data = (object) array(
			'type' => 'gallery',
		);

		$data = array(
			array(
				(object) array(
					'type'     => 'module',
					'settings' => $photo_data,
				),
			),
			(object) array(
				'type'     => 'module',
				'settings' => $gallery_data,
			),
			// Not supported module
			(object) array(
				'type' => 'module',
				'settings' => (object) array(
					'type' => 'not-supported'
				)
			),
			// Not translated modules
			(object) array(),
			(object) array( 'type' => 'not a module' ),
			(object) array( 'type' => 'module' ),
			(object) array( 'type' => 'module', 'settings' => (object) array() ),
		);

		$translated_photo_data   = array( 'translated photo data' );
		$translated_gallery_data = array( 'translated gallery data' );

		$expected_data = array(
			array(
				(object) array(
					'type'     => 'module',
					'settings' => $translated_photo_data,
				),
			),
			(object) array(
				'type'     => 'module',
				'settings' => $translated_gallery_data,
			),
			// Not supported module
			(object) array(
				'type' => 'module',
				'settings' => (object) array(
					'type' => 'not-supported'
				)
			),
			// Not translated modules
			(object) array(),
			(object) array( 'type' => 'not a module' ),
			(object) array( 'type' => 'module' ),
			(object) array( 'type' => 'module', 'settings' => (object) array() ),
		);

		$node_photo = $this->get_node();
		$node_photo->method( 'translate' )->with( $photo_data, $lang, $source_lang )
			->willReturn( $translated_photo_data );

		$node_gallery = $this->get_node();
		$node_gallery->method( 'translate' )->with( $gallery_data, $lang, $source_lang )
			->willReturn( $translated_gallery_data );

		$node_provider = $this->get_node_provider();
		$node_provider->method( 'get' )->willReturnMap(
			array(
				array( 'photo', $node_photo ),
				array( 'gallery', $node_gallery ),
				array( 'not-supported', null ),
			)
		);

		$subject = $this->get_subject( $node_provider );

		$this->assertEquals( $expected_data, $subject->translate( $data, $lang, $source_lang ) );
	}

	private function get_subject( $node_provider ) {
		return new WPML_Beaver_Builder_Media_Node_Iterator( $node_provider );
	}

	private function get_node_provider() {
		return $this->getMockBuilder( 'WPML_Beaver_Builder_Media_Node_Provider' )
			->setMethods( array( 'get' ) )->disableOriginalConstructor()->getMock();
	}

	private function get_node() {
		return $this->getMockBuilder( 'WPML_Beaver_Builder_Media_Node' )
		            ->setMethods( array( 'translate' ) )->disableOriginalConstructor()->getMock();
	}
}

if ( ! interface_exists( 'IWPML_PB_Media_Node_Iterator' ) ) {
	interface IWPML_PB_Media_Node_Iterator {}
}