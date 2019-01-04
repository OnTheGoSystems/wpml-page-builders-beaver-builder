<?php

class Test_WPML_Beaver_Builder_Cleanup_Hooks_Factory extends OTGS_TestCase {

	/**
	 * @test
	 */
	public function it_should_load_on_backend_and_frontend() {
		$subject = new WPML_Beaver_Builder_Cleanup_Hooks_Factory();
		$this->assertInstanceOf( 'IWPML_Backend_Action_Loader', $subject );
		$this->assertInstanceOf( 'IWPML_Frontend_Action_Loader', $subject );
	}

	/**
	 * @test
	 */
	public function it_should_create_and_return_an_instance() {
		$subject = new WPML_Beaver_Builder_Cleanup_Hooks_Factory();
		$this->assertInstanceOf( 'WPML_Beaver_Builder_Cleanup_Hooks', $subject->create() );
	}
}
