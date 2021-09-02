<?php

namespace WPML\PB\BeaverBuilder\Hooks;

use WPML\LIB\WP\OnActionMock;

/**
 * @group hooks
 * @group editor
 */
class TestEditor extends \OTGS_TestCase {

	use OnActionMock;

	const ORIGINAL_POST_ID = 123;
	const TRANSLATION_POST_ID = 456;

	public function setUp() {
		parent::setUp();
		$this->setUpOnAction();
	}

	public function tearDown() {
		unset( $_POST['fl_builder_data'], $_POST['post_id'] );
		$this->tearDownOnAction();
		parent::tearDown();
	}

	/**
	 * @test
	 */
	public function itReturnsTrueIfAlreadyTranslatingWithNativeEditor() {
		$subject = new Editor();
		$subject->add_hooks();

		$this->assertTrue( $this->runFilter( 'wpml_pb_is_editing_translation_with_native_editor', true, self::TRANSLATION_POST_ID ) );
	}

	/**
	 * @test
	 */
	public function itReturnsTrueIfTranslatingWithBeaverBuilderNativeEditor() {
		$_POST = [
			'fl_builder_data' => [
				'action'  => 'save_layout',
				'post_id' => (string) self::TRANSLATION_POST_ID,
			],
		];

		$subject = new Editor();
		$subject->add_hooks();

		$this->assertTrue( $this->runFilter( 'wpml_pb_is_editing_translation_with_native_editor', false, self::TRANSLATION_POST_ID ) );
	}

	/**
	 * @test
	 */
	public function itReturnsFalseIfEditingOriginalWithBeaverBuilderNativeEditor() {
		$_POST = [
			'fl_builder_data' => [
				'action'  => 'save_layout',
				'post_id' => (string) self::ORIGINAL_POST_ID,
			],
		];

		$subject = new Editor();
		$subject->add_hooks();

		$this->assertFalse( $this->runFilter( 'wpml_pb_is_editing_translation_with_native_editor', false, self::TRANSLATION_POST_ID ) );
	}
}
