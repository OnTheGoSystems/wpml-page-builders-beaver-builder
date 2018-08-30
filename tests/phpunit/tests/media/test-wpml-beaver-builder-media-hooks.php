<?php

/**
 * @group media
 */
class Test_WPML_Beaver_Builder_Media_Hooks extends OTGS_TestCase {

	public function setUp() {
		parent::setUp();
		$this->getMockBuilder( 'WPML_Page_Builders_Update' )->getMock();
		$this->getMockBuilder( 'WPML_Page_Builders_Update_Media' )->getMock();
	}

	/**
	 * @test
	 */
	public function it_should_implement_iwpml_action() {
		$this->assertInstanceOf( 'IWPML_Action', $this->get_subject() );
	}

	/**
	 * @test
	 */
	public function it_should_add_hooks() {
		$subject = $this->get_subject();
		\WP_Mock::expectFilterAdded( 'wmpl_pb_get_media_updaters', array( $subject, 'add_media_updater' ) );
		$subject->add_hooks();
	}

	/**
	 * @test
	 */
	public function it_should_add_media_updater_only_once() {
		$updaters = array(
			'some-plugin' => $this->getMockBuilder( 'IWPML_PB_Media_Update' )->getMock(),
		);

		$subject = $this->get_subject();

		$filtered_updaters = $subject->add_media_updater( $updaters );
		$this->check_updaters( $updaters, $filtered_updaters );
		$filtered_updaters = $subject->add_media_updater( $filtered_updaters );
		$this->check_updaters( $updaters, $filtered_updaters );
	}

	private function check_updaters( $updaters, $filtered_updaters ) {
		$this->assertCount( 2, $filtered_updaters );
		$this->assertSame( $updaters['some-plugin'], $filtered_updaters['some-plugin'] );
		$this->assertInstanceOf(
			'WPML_Page_Builders_Update_Media',
			$filtered_updaters[ WPML_Beaver_Builder_Media_Hooks::KEY ]
		);
	}

	private function get_subject() {
		return new WPML_Beaver_Builder_Media_Hooks( $this->get_sitepress() );
	}

	private function get_sitepress() {
		return $this->getMockBuilder( 'SitePress' )->disableOriginalConstructor()->getMock();
	}
}

if ( ! class_exists( 'IWPML_Action' ) ) {
	interface IWPML_Action {}
}