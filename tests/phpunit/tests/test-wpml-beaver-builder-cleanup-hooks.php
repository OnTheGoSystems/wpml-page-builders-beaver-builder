<?php

/**
 * @group wpmlcore-6211
 */
class Test_WPML_Beaver_Builder_Cleanup_Hooks extends OTGS_TestCase {

	/**
	 * @test
	 */
	public function it_should_add_hooks() {
		$subject = new WPML_Beaver_Builder_Cleanup_Hooks();
		\WP_Mock::expectActionAdded( 'wpml_delete_unused_package_strings', array( $subject, 'delete_block_layout_string' ) );
		$subject->add_hooks();
	}

	/**
	 * @test
	 */
	public function it_should_NOT_delete_strings_if_NO_gutenberg_package() {
		$package_data = array(
			'kind'    => 'Beaver Builder',
			'name'    => 123,
			'post_id' => 234,
		);

		$package       = $this->get_package();
		$package->kind = $package_data['kind'];

		\WP_Mock::onFilter( 'wpml_st_get_post_string_packages' )
			->with( array(), $package_data['post_id'] )
			->reply( array( $package ) );

		\WP_Mock::userFunction( 'do_action', array( 'times' => 0 ) );

		$subject = new WPML_Beaver_Builder_Cleanup_Hooks();

		$subject->delete_block_layout_string( $package_data );
	}

	/**
	 * @test
	 */
	public function it_should_NOT_delete_strings_if_NOT_a_layout() {
		$package_data = array(
			'kind'    => WPML_Gutenberg_Integration::PACKAGE_ID,
			'name'    => 123,
			'post_id' => 234,
		);

		$string = (object) array(
			'title' => 'paragraph',
			'id'    => 1111,
		);

		$package       = $this->get_package();
		$package->kind = $package_data['kind'];
		$package->name = $package_data['name'];
		$package->method( 'get_package_strings' )->willReturn( array( $string ) );

		\WP_Mock::onFilter( 'wpml_st_get_post_string_packages' )
		        ->with( array(), $package_data['post_id'] )
		        ->reply( array( $package ) );

		\WP_Mock::userFunction( 'do_action', array( 'times' => 0 ) );

		$subject = new WPML_Beaver_Builder_Cleanup_Hooks();

		$subject->delete_block_layout_string( $package_data );
	}

	/**
	 * @test
	 */
	public function it_should_delete_package_if_unique_string() {
		$package_data = array(
			'kind'    => WPML_Gutenberg_Integration::PACKAGE_ID,
			'name'    => 123,
			'post_id' => 234,
		);

		$string = (object) array(
			'title' => 'fl-builder/layout',
			'id'    => 1111,
		);

		$package       = $this->get_package();
		$package->kind = $package_data['kind'];
		$package->name = $package_data['name'];
		$package->method( 'get_package_strings' )->willReturn( array( $string ) );

		\WP_Mock::onFilter( 'wpml_st_get_post_string_packages' )
			->with( array(), $package_data['post_id'] )
			->reply( array( $package ) );

		\WP_Mock::expectAction( 'wpml_delete_package', $package_data['name'], $package_data['kind'] );

		$subject = new WPML_Beaver_Builder_Cleanup_Hooks();

		$subject->delete_block_layout_string( $package_data );
	}

	/**
	 * @test
	 */
	public function it_should_delete_one_string_only() {
		$package_data = array(
			'kind'    => WPML_Gutenberg_Integration::PACKAGE_ID,
			'name'    => 123,
			'post_id' => 234,
		);

		$string_layout = (object) array(
			'title' => 'fl-builder/layout',
			'id'    => 1111,
		);

		$string_other = (object) array(
			'title' => 'paragraph',
			'id'    => 1112,
		);

		$package       = $this->get_package();
		$package->kind = $package_data['kind'];
		$package->name = $package_data['name'];
		$package->method( 'get_package_strings' )->willReturn( array( $string_layout, $string_other ) );

		\WP_Mock::onFilter( 'wpml_st_get_post_string_packages' )
			->with( array(), $package_data['post_id'] )
			->reply( array( $package ) );

		\WP_Mock::expectAction( 'wpml_st_delete_all_string_data', $string_layout->id );

		$subject = new WPML_Beaver_Builder_Cleanup_Hooks();

		$subject->delete_block_layout_string( $package_data );
	}

	private function get_package() {
		return $this->getMockBuilder( 'WPML_Package' )
			->setMethods( array( 'get_package_strings' ) )
			->getMock();
	}
}

if ( ! class_exists( 'IWPML_Action' ) ) {
	interface IWPML_Action {}
}

if ( ! class_exists( 'WPML_Gutenberg_Integration' ) ) {
	interface WPML_Gutenberg_Integration {
		const PACKAGE_ID = 'Gutenberg';
	}
}