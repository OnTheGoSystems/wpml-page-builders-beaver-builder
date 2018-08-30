<?php

/**
 * @group media
 */
class Test_WPML_Beaver_Builder_Media_Node_Provider extends OTGS_TestCase {

	/**
	 * @test
	 * @dataProvider dp_node_types
	 *
	 * @param string $type
	 * @param string $class_name
	 */
	public function it_should_return_a_node_instance_and_cache_it( $type, $class_name ) {
		$GLOBALS['sitepress'] = $this->getMockBuilder( 'SitePress' )->disableOriginalConstructor()->getMock();
		$this->mock_external_classes();

		$subject = new WPML_Beaver_Builder_Media_Node_Provider();

		$this->assertInstanceOf( $class_name, $subject->get( $type ) );
		$this->assertSame( $subject->get( $type ), $subject->get( $type ) );
	}

	public function dp_node_types() {
		return array(
			'photo'   => array( 'photo', 'WPML_Beaver_Builder_Media_Node_Photo' ),
			'gallery' => array( 'gallery', 'WPML_Beaver_Builder_Media_Node_Gallery' ),
		);
	}

	private function mock_external_classes() {
		$this->getMockBuilder( 'WPML_Page_Builders_Media_Translate' )->getMock();
		$this->getMockBuilder( 'WPML_Translation_Element_Factory' )->getMock();
		$this->getMockBuilder( 'WPML_Media_Image_Translate' )->getMock();
		$this->getMockBuilder( 'WPML_Media_Attachment_By_URL_Factory' )->getMock();
	}
}