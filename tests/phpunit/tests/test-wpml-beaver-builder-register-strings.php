<?php

/**
 * Class Test_WPML_Beaver_Builder_Register_Strings
 * @group page-builders
 * @group beaver-builder
 */
class Test_WPML_Beaver_Builder_Register_Stings extends WPML_PB_TestCase2 {

	public function test_register_strings() {
		list( $name, $post, $package ) = $this->get_post_and_package( 'Beaver builder' );
		$node_id  = rand();
		$settings = array( rand_str() => rand_str() );
		$string   = new WPML_PB_String( rand_str(), rand_str(), rand_str(), rand_str() );
		$strings  = array( $string );

		$beaver_builder_field_data = array(
			array(
				(object) array( 'type' => 'module', 'settings' => $settings, 'node' => $node_id ),
			)
		);

		\WP_Mock::wpFunction( 'get_post_meta', array(
			'times'  => 1,
			'args'   => array( $post->ID, '_fl_builder_data', false ),
			'return' => $beaver_builder_field_data,
		) );

		WP_Mock::expectAction( 'wpml_start_string_package_registration', $package );
		WP_Mock::expectAction( 'wpml_delete_unused_package_strings', $package );

		$translatable_nodes = $this->getMockBuilder( 'WPML_Beaver_Builder_Translatable_Nodes' )
		                                ->setMethods( array( 'get' ) )
		                                ->disableOriginalConstructor()
		                                ->getMock();
		$translatable_nodes->expects( $this->once() )
		                        ->method( 'get' )
		                        ->with( $node_id, $settings )
		                        ->willReturn( $strings );

		$string_registration_mock = \Mockery::mock( 'WPML_PB_String_Registration' );
		$string_registration_mock->shouldReceive( 'register_string' )
		                         ->once()
		                         ->with(
			                         $post->ID,
			                         $string->get_value(),
			                         $string->get_editor_type(),
			                         $string->get_title(),
			                         $string->get_name()
		                         );

		$data_settings = $this->getMockBuilder( 'WPML_Beaver_Builder_Data_Settings' )
		                      ->disableOriginalConstructor()
		                      ->getMock();

		$data_settings->method( 'get_meta_field' )
		              ->willReturn( '_fl_builder_data' );

		$data_settings->method( 'get_node_id_field' )
		              ->willReturn( 'node' );

		$data_settings->method( 'convert_data_to_array' )
		              ->with( $beaver_builder_field_data )
		              ->willReturn( $beaver_builder_field_data );

		$subject = new WPML_Beaver_Builder_Register_Strings( $translatable_nodes, $data_settings, $string_registration_mock );
		$subject->register_strings( $post, $package );
	}

}