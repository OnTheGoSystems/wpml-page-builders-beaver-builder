<?php

/**
 * Class Test_WPML_Beaver_Builder_Register_Strings
 * @group page-builders
 * @group beaver-builder
 * @group wpmlcore-6732
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
				(object) array(
					'type'     => 'module',
					'settings' => $settings,
					'node'     => $node_id,
					'parent'   => null,
				),
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
			                         $string->get_name(),
			                         1,
			                         $string->get_wrap_tag()
		                         );

		$data_settings = $this->get_data_settings( $beaver_builder_field_data );

		$subject = new WPML_Beaver_Builder_Register_Strings( $translatable_nodes, $data_settings, $string_registration_mock );
		$subject->register_strings( $post, $package );
	}

	/**
	 * @test
	 * @group wpmlcore-6331
	 */
	public function it_should_sort_modules_before_register_strings() {
		list( $name, $post, $package ) = $this->get_post_and_package( 'Beaver builder' );

		$beaver_builder_field_data = $this->get_meta_data_for_sort_modules();

		\WP_Mock::wpFunction( 'get_post_meta', array(
			'times'  => 1,
			'args'   => array( $post->ID, '_fl_builder_data', false ),
			'return' => $beaver_builder_field_data,
		));

		$get_strings_map = array(
			array( 'module-1', $this->get_module_settings( 1 ), array( new WPML_PB_String( 'Text #1', 'name-1', 'title 1', 'LINE' ) ) ),
			array( 'module-2', $this->get_module_settings( 2 ), array( new WPML_PB_String( 'Text #2', 'name-2', 'title 2', 'LINE' ) ) ),
			array( 'module-3', $this->get_module_settings( 3 ), array( new WPML_PB_String( 'Text #3', 'name-3', 'title 3', 'LINE' ) ) ),
			array( 'module-4', $this->get_module_settings( 4 ), array( new WPML_PB_String( 'Text #4', 'name-4', 'title 4', 'LINE' ) ) ),
			array( 'module-5', $this->get_module_settings( 5 ), array( new WPML_PB_String( 'Text #5', 'name-5', 'title 5', 'LINE' ) ) ),
		);

		$translatable_nodes = $this->getMockBuilder( 'WPML_Beaver_Builder_Translatable_Nodes' )
			->setMethods( array( 'get' ) )
			->disableOriginalConstructor()->getMock();
		$translatable_nodes->method( 'get' )->willReturnMap( $get_strings_map );

		$string_registration_mock = $this->getMockBuilder( 'WPML_PB_String_Registration' )
			->setMethods( array( 'register_string' ) )
			->disableOriginalConstructor()->getMock();
		$string_registration_mock->expects( $this->exactly( 5 ) )
		                         ->method( 'register_string' )
		                         ->withConsecutive(
			                         array( $post->ID, 'Text #1', 'LINE', 'title 1', 'name-1' ),
			                         array( $post->ID, 'Text #2', 'LINE', 'title 2', 'name-2' ),
			                         array( $post->ID, 'Text #3', 'LINE', 'title 3', 'name-3' ),
			                         array( $post->ID, 'Text #4', 'LINE', 'title 4', 'name-4' ),
			                         array( $post->ID, 'Text #5', 'LINE', 'title 5', 'name-5' )
		                         );

		$data_settings = $this->get_data_settings( $beaver_builder_field_data );

		$subject = new WPML_Beaver_Builder_Register_Strings( $translatable_nodes, $data_settings, $string_registration_mock );
		$subject->register_strings( $post, $package );
	}

	private function get_data_settings( array $beaver_builder_field_data ) {
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

		return $data_settings;
	}

	private function get_meta_data_for_sort_modules() {
		return array(
			array(
				'row-a'          => (object) array(
					'node'     => 'row-a',
					'type'     => 'row',
					'parent'   => null,
					'position' => 1,
					'settings' => '',
				),
				'column-group-a' => (object) array(
					'node'     => 'column-group-a',
					'type'     => 'column-group',
					'parent'   => 'row-a',
					'position' => 2,
					'settings' => '',
				),
				'column-a'       => (object) array(
					'node'     => 'column-a',
					'type'     => 'column',
					'parent'   => 'column-group-a',
					'position' => 0,
					'settings' => (object) array(),
				),
				'module-4'       => (object) array(
					'node'     => 'module-4',
					'type'     => 'module',
					'parent'   => 'column-a',
					'position' => 1,
					'settings' => $this->get_module_settings( 4 ),
				),
				'module-5'       => (object) array(
					'node'     => 'module-5',
					'type'     => 'module',
					'parent'   => 'column-a',
					'position' => 2,
					'settings' => $this->get_module_settings( 5 ),
				),
				'module-3'       => (object) array(
					'node'     => 'module-3',
					'type'     => 'module',
					'parent'   => 'column-a',
					'position' => 0,
					'settings' => $this->get_module_settings( 3 ),
				),
				'column-b'       => (object) array(
					'node'     => 'column-b',
					'type'     => 'column',
					'parent'   => 'column-group-b',
					'position' => 0,
					'settings' => '',
				),
				'column-group-b' => (object) array(
					'node'     => 'column-group-b',
					'type'     => 'column-group',
					'parent'   => 'row-a',
					'position' => 0,
					'settings' => '',
				),
				'module-1'       => (object) array(
					'node'     => 'module-1',
					'type'     => 'module',
					'parent'   => 'column-b',
					'position' => 0,
					'settings' => $this->get_module_settings( 1 ),
				),
				'row-b'          => (object) array(
					'node'     => 'row-b',
					'type'     => 'row',
					'parent'   => null,
					'position' => 0,
					'settings' => '',
				),
				'column-group-c' => (object) array(
					'node'     => 'column-group-c',
					'type'     => 'column-group',
					'parent'   => 'row-b',
					'position' => 1,
					'settings' => '',
				),
				'column-c'       => (object) array(
					'node'     => 'column-c',
					'type'     => 'column',
					'parent'   => 'column-group-c',
					'position' => 0,
					'settings' => '',
				),
				'column-group-d' => (object) array(
					'node'     => 'column-group-d',
					'type'     => 'column-group',
					'parent'   => 'row-b',
					'position' => 0,
					'settings' => '',
				),
				'column-d'       => (object) array(
					'node'     => 'column-d',
					'type'     => 'column',
					'parent'   => 'column-group-d',
					'position' => 0,
					'settings' => '',
				),
				'module-2'       => (object) array(
					'node'     => 'module-2',
					'type'     => 'module',
					'parent'   => 'column-e',
					'position' => 0,
					'settings' => $this->get_module_settings( 2 ),
				),
				'column-group-e' => (object) array(
					'node'     => 'column-group-e',
					'type'     => 'column-group',
					'parent'   => 'row-a',
					'position' => 1,
					'settings' => '',
				),
				'column-e'       => (object) array(
					'node'     => 'column-e',
					'type'     => 'column',
					'parent'   => 'column-group-e',
					'position' => 0,
					'settings' => '',
				),
			),
		);
	}

	/**
	 * @param int $location
	 *
	 * @return stdClass
	 */
	private function get_module_settings( $location ) {
		/**
		 * We need to provide the same objects so it's accepted by
		 * argument matcher handler of `WPML_Beaver_Builder_Translatable_Nodes::get`'s mock
		 */
		static $settings = array();

		if ( ! isset( $settings[ $location ] ) ) {
			$settings[ $location ] = (object) array(
				'text' => 'Text #' . $location,
				'type' => 'rich-text',
			);
		}

		return $settings[ $location ];
	}
}