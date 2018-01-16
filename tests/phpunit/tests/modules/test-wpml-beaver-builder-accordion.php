<?php

/**
 * Class Test_WPML_Beaver_Builder_Accordion
 * @group page-builders
 * @group beaver-builder
 */
class Test_WPML_Beaver_Builder_Accordion extends OTGS_TestCase {

	public function setUp() {
		parent::setUp();
		\WP_Mock::wpPassthruFunction( 'esc_html__' );

		$this->settings = (object) array(
			'items' => array(
				(object) array(
					'label' => 'label1',
					'content' => 'content1'
				),
				(object) array(
					'label' => 'label2',
					'content' => 'content2'
				),
			)
		);

	}

	public function test_get() {
		$node_id = rand( 1, 1000 );
		$strings = array();

		$subject = new WPML_Beaver_Builder_Accordion();
		$strings = $subject->get( $node_id, $this->settings, $strings );

		$this->assertCount( 4, $strings );

		foreach ( $this->settings->items as $index => $item ) {
			$this->assertEquals( $item->label, $strings[ $index * 2 ]->get_value() );
			$this->assertEquals( md5( $item->label ) . '-label-' . $node_id, $strings[ $index * 2 ]->get_name() );
			$this->assertEquals( 'Accordion Item Label', $strings[ $index * 2 ]->get_title() );
			$this->assertEquals( 'LINE', $strings[ $index * 2 ]->get_editor_type() );

			$this->assertEquals( $item->content, $strings[ $index * 2 + 1 ]->get_value() );
			$this->assertEquals( md5( $item->content ) . '-content-' . $node_id, $strings[ $index * 2 + 1 ]->get_name() );
			$this->assertEquals( 'Accordion Item Content', $strings[ $index * 2 + 1 ]->get_title() );
			$this->assertEquals( 'VISUAL', $strings[ $index * 2 + 1 ]->get_editor_type() );
		}
	}

	public function test_update() {

		$node_id = rand( 1, 1000 );

		$subject = new WPML_Beaver_Builder_Accordion();

		foreach ( $this->settings->items as $item ) {

			foreach ( array( 'label', 'content' ) as $field ) {
				$translation = rand_str();
				$string      = new WPML_PB_String(
					$translation,
					md5( $item->$field ) . '-' . $field . '-' . $node_id,
					'Anything',
					'Anything'
				);
				$subject->update( $node_id, $this->settings, $string );

				$this->assertEquals( $translation, $item->$field );
			}
		}

	}
}
