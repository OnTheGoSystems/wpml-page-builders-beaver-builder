<?php

/**
 * Class Test_WPML_Beaver_Builder_Translatable_Nodes
 * @group page-builders
 * @group beaver-builder
 */
class Test_WPML_Beaver_Builder_Translatable_Nodes extends OTGS_TestCase {

	/**
	 * @dataProvider node_data_provider
	 */
	public function test_get( $type, $field, $expected_title, $expected_editor_type ) {

		\WP_Mock::wpPassthruFunction( '__' );

		$node_id = rand();
		$settings = (object) array( 'type' => $type, $field => rand_str() );

		$subject = new WPML_Beaver_Builder_Translatable_Nodes();
		$strings = $subject->get( $node_id, $settings );
		$this->assertCount( 1, $strings );
		$string = $strings[0];
		$this->assertEquals( $settings->$field, $string->get_value() );
		$this->assertEquals( $field . '-' . $settings->type . '-' . $node_id, $string->get_name() );
		$this->assertEquals( $expected_title, $string->get_title() );
		$this->assertEquals( $expected_editor_type, $string->get_editor_type() );
	}

	public function node_data_provider() {

		return array(
			array( 'rich-text', 'text', 'Text Editor', 'VISUAL'),
			array( 'html', 'html', 'HTML', 'VISUAL'),
			array( 'button', 'text', 'Button', 'LINE'),
			array( 'heading', 'heading', 'Heading', 'LINE'),
			array( 'cta', 'title', 'Call to Action: Heading', 'LINE'),
			array( 'cta', 'text', 'Call to Action: Text', 'VISUAL'),
			array( 'cta', 'btn_text', 'Call to Action: Button text', 'LINE'),
		);
	}

	public function test_update() {

		$node_id = rand();
		$settings = (object) array( 'type' => 'rich-text', 'text' => rand_str() );
		$translation = rand_str();

		$string = new WPML_PB_String( $translation, 'text-rich-text-' . $node_id, 'anything', 'anything' );

		$subject = new WPML_Beaver_Builder_Translatable_Nodes();
		$settings = $subject->update( $node_id, $settings, $string );

		$this->assertEquals( $translation, $settings->text );
	}

}