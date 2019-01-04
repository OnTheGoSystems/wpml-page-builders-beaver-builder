<?php

/**
 * Class Test_WPML_Beaver_Builder_Register_Strings
 *
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 *
 * @group page-builders
 * @group beaver-builder
 */
class Test_WPML_Beaver_Builder_Integration_Factory extends OTGS_TestCase {

	/**
	 * @test
	 */
	public function it_creates() {
		global $sitepress;

		$bb_factory = new WPML_Beaver_Builder_Integration_Factory();

		$action_loader = \Mockery::mock('overload:WPML_Action_Filter_Loader');
		$action_loader->shouldReceive( 'load' )->with(
			array(
				'WPML_PB_Beaver_Builder_Handle_Custom_Fields_Factory',
				'WPML_Beaver_Builder_Media_Hooks_Factory',
				'WPML_Beaver_Builder_Cleanup_Hooks_Factory',
			)
		);

		\Mockery::mock('overload:AbsoluteLinks');
		\Mockery::mock('overload:WPML_Absolute_To_Permalinks');
		\Mockery::mock('overload:WPML_Translate_Link_Targets');
		\Mockery::mock('overload:WPML_PB_API_Hooks_Strategy');
		\Mockery::mock('overload:WPML_ST_String_Factory');

		$string_registration = \Mockery::mock('overload:WPML_PB_String_Registration');
		$factory = \Mockery::mock('overload:WPML_String_Registration_Factory');
		$factory->shouldReceive( 'create' )->andReturn( $string_registration );

		$sitepress = $this->getMockBuilder( 'SitePress' )
			->setMethods( array( 'get_active_languages' ) )
			->disableOriginalConstructor()
			->getMock();

		$bb_factory->create();

		$this->assertInstanceOf( 'WPML_Page_Builders_Integration', $bb_factory->create() );
	}
}