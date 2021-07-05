<?php

namespace WPML\PB\BeaverBuilder\Hooks;

use WPML\LIB\WP\OnActionMock;

/**
 * @group hooks
 * @group editor
 */
class TestEditor extends \OTGS_TestCase {

	use OnActionMock;

	public function setUp() {
		parent::setUp();
		$this->setUpOnAction();
	}

	public function tearDown() {
		unset( $_POST['fl_builder_data'] );
		$this->tearDownOnAction();
		parent::tearDown();
	}

	/**
	 * @test
	 */
	public function itReturnsTrueIfAlreadyTranslatingWithNativeEditor() {
		$subject = new Editor();
		$subject->add_hooks();

		$this->assertTrue( $this->runFilter( 'wpml_pb_is_editing_translation_with_native_editor', true ) );
	}

	/**
	 * @test
	 */
	public function itReturnsTrueIfTranslatingWithBeaverBuilderNativeEditor() {
		$_POST = [
			'fl_builder_data' => [
				'action' => 'save_layout',
			],
		];

		$subject = new Editor();
		$subject->add_hooks();

		$this->assertTrue( $this->runFilter( 'wpml_pb_is_editing_translation_with_native_editor', false ) );
	}
}
