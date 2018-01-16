<?php

/**
 * Class Test_WPML_Beaver_Builder_Translatable_Nodes
 * @group page-builders
 * @group beaver-builder
 */
class Test_WPML_Beaver_Builder_Update_Translations extends OTGS_TestCase {

	public function test_update(  ) {

		$node_id = rand();
		$translated_post_id = rand();
		$original_post_id = rand();
		$original_post = (object) array( 'ID' => $original_post_id );
		$lang = rand_str( 2 );
		$translation = rand_str();
		$string_translations = array(
			'text-rich-text-' . $node_id => array(
				$lang => array(
					'status' => 10,
					'value' => $translation
				)
			)
		);
		$settings = (object) array( 'type' => 'rich-text', 'text' => rand_str() );
		$translated_settings = (object) array( 'type' => 'rich-text', 'text' => $translation );

		$beaver_builder_field_data = array(
			(object) array( 'type' => 'module', 'settings' => $settings, 'node' => $node_id ),
		);
		$translated_beaver_builder_field_data = array(
			(object) array( 'type' => 'module', 'settings' => $translated_settings, 'node' => $node_id ),
		);

		\WP_Mock::wpFunction( 'get_post_meta', array(
			'times'  => 1,
			'args'   => array( $original_post_id, '_fl_builder_data', true ),
			'return' => $beaver_builder_field_data,
		) );
		foreach( array( '_fl_builder_data', '_fl_builder_draft') as $meta_key ) {
			\WP_Mock::wpFunction( 'update_post_meta', array(
				'times' => 1,
				'args'  => array( $translated_post_id, $meta_key, $translated_beaver_builder_field_data ),
			) );
		}

		$this->add_copy_meta_fields_checks( $translated_post_id, $original_post_id );

		$translatable_nodes_mock = $this->getMockBuilder( 'WPML_Beaver_Builder_Translatable_Nodes' )
		                                ->setMethods( array( 'update' ) )
		                                ->getMock();
		$translatable_nodes_mock->expects( $this->once() )
		                        ->method( 'update' )
		                        ->with( $node_id, $settings )
		                        ->willReturn( $translated_settings );

		$data_settings = $this->getMockBuilder( 'WPML_Beaver_Builder_Data_Settings' )
		                      ->disableOriginalConstructor()
		                      ->getMock();

		$data_settings->method( 'get_meta_field' )
		              ->willReturn( '_fl_builder_data' );

		$data_settings->method( 'get_node_id_field' )
		              ->willReturn( 'node' );

		$data_settings->method( 'get_fields_to_copy' )
		              ->willReturn( array( '_fl_builder_draft_settings', '_fl_builder_data_settings', '_fl_builder_enabled' ) );

		$data_settings->method( 'convert_data_to_array' )
		              ->with( $beaver_builder_field_data )
		              ->willReturn( $beaver_builder_field_data );

		$data_settings->method( 'prepare_data_for_saving' )
		              ->with( $beaver_builder_field_data )
		              ->willReturn( $beaver_builder_field_data );

		$data_settings->method( 'get_fields_to_save' )
		              ->willReturn( array( '_fl_builder_data', '_fl_builder_draft' ) );

		$subject = new WPML_Beaver_Builder_Update_Translation( $translatable_nodes_mock, $data_settings );
		$subject->update( $translated_post_id, $original_post, $string_translations, $lang );
	}

	private function add_copy_meta_fields_checks( $translated_post_id, $original_post_id ) {
		foreach( array( '_fl_builder_data_settings', '_fl_builder_draft_settings', '_fl_builder_enabled') as $meta_key ) {
			$value = rand_str();
			\WP_Mock::wpFunction( 'get_post_meta', array(
				'times'  => 1,
				'args'   => array( $original_post_id, $meta_key, true ),
				'return' => $value,
			) );
			\WP_Mock::wpFunction( 'update_post_meta', array(
				'times' => 1,
				'args'  => array( $translated_post_id, $meta_key, $value ),
			) );
		}

	}

}