<?php

/**
 * @group media
 */
class Test_WPML_Beaver_Builder_Media_Hooks_Factory extends OTGS_TestCase {

	/**
	 * @test
	 */
	public function it_should_create_and_return_an_instance() {
		$GLOBALS['sitepress'] = $this->getMockBuilder( 'SitePress' )->getMock();
		$subject = new WPML_Beaver_Builder_Media_Hooks_Factory();
		$this->assertInstanceOf( 'WPML_Beaver_Builder_Media_Hooks', $subject->create() );
	}
}
