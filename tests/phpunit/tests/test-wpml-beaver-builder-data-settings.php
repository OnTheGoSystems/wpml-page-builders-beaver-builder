<?php

/**
 * Class Test_WPML_Beaver_Builder_Data_Settings
 *
 * @group page-builders
 * @group beaver-builder
 */
class Test_WPML_Beaver_Builder_Data_Settings extends OTGS_TestCase {

	/**
	 * @test
	 */
	public function it_gets_meta_field() {
		$subject = new WPML_Beaver_Builder_Data_Settings();
		$this->assertEquals( '_fl_builder_data', $subject->get_meta_field() );
	}

	/**
	 * @test
	 */
	public function it_gets_node_id_field() {
		$subject = new WPML_Beaver_Builder_Data_Settings();
		$this->assertEquals( 'node', $subject->get_node_id_field() );
	}

	/**
	 * @test
	 */
	public function it_gets_field_to_copy() {
		$fields_to_copy = array( '_fl_builder_draft_settings', '_fl_builder_data_settings', '_fl_builder_enabled' );

		$subject = new WPML_Beaver_Builder_Data_Settings();
		$this->assertEquals( $fields_to_copy, $subject->get_fields_to_copy() );
	}

	/**
	 * @test
	 */
	public function it_converts_data_to_array() {
		$data = array(
			'id' => mt_rand(),
			'something' => rand_str( 10 ),
		);

		$subject = new WPML_Beaver_Builder_Data_Settings();
		$this->assertEquals( $data, $subject->convert_data_to_array( $data ) );
	}

	/**
	 * @test
	 * @group wpmlcore-6774
	 */
	public function it_prepares_data_for_saving() {
		$data = [
			'gt5s65g365' => (object) [
				'node'     => 'gt5s65g365',
				'settings' => (object) [
					'text' => 'My text in object',
					'data' => [
						'text1' => 'My text in array',
					]
				]
			]
		];

		\WP_Mock::userFunction( 'wp_slash', [
			'times'      => 1,
			'args'       => [ 'gt5s65g365' ],
			'return_arg' => true,
		] );

		\WP_Mock::userFunction( 'wp_slash', [
			'times'      => '1+',
			'args'       => [ 'My text in object' ],
			'return_arg' => true,
		] );

		\WP_Mock::userFunction( 'wp_slash', [
			'times'      => 1,
			'args'       => [ 'My text in array' ],
			'return_arg' => true,
		] );

		$subject = new WPML_Beaver_Builder_Data_Settings();
		$this->assertEquals( $data, $subject->prepare_data_for_saving( $data ) );
	}

	/**
	 * @test
	 */
	public function it_gets_pb_name() {
		$subject = new WPML_Beaver_Builder_Data_Settings();
		$this->assertEquals( 'Beaver builder', $subject->get_pb_name() );
	}

	/**
	 * @test
	 */
	public function it_gets_field_to_save() {
		$fields_to_copy = array( '_fl_builder_data', '_fl_builder_draft' );

		$subject = new WPML_Beaver_Builder_Data_Settings();
		$this->assertEquals( $fields_to_copy, $subject->get_fields_to_save() );
	}
}