<?php

namespace WPML\PB\BeaverBuilder\Modules;

/**
 * @group module
 */
class TestModuleWithItemsFromConfig extends \OTGS_TestCase {

	/**
	 * @test
	 */
	public function itShouldGetData() {
		$itemsField = 'slides';
		$items      = [ 'some module items' ];

		$settings = (object) [
			$itemsField => $items,
		];

		$config = [
			[
				'field'       => 'title',
				'type'        => 'The slide title',
				'editor_type' => 'LINE',
			],
			[
				'field'       => 'link',
				'type'        => 'The slide link',
				'editor_type' => 'LINK',
			],
		];


		$subject = new ModuleWithItemsFromConfig( $itemsField, $config );

		$this->assertEquals( $items, $subject->get_items( $settings ) );

		$this->assertEquals(
			[ 'title', 'link' ],
			$subject->get_fields()
		);

		// Title field
		$this->assertEquals( 'The slide title', $subject->get_title( 'title' ) );
		$this->assertEquals( 'LINE', $subject->get_editor_type( 'title' ) );

		// Link field
		$this->assertEquals( 'The slide link', $subject->get_title( 'link' ) );
		$this->assertEquals( 'LINK', $subject->get_editor_type( 'link' ) );
	}
}
