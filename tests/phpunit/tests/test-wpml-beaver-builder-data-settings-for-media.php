<?php

/**
 * Class Test_WPML_Beaver_Builder_Data_Settings_For_Media
 *
 * @group page-builders
 * @group beaver-builder
 */
class Test_WPML_Beaver_Builder_Data_Settings_For_Media extends OTGS_TestCase {

	/**
	 * @test
	 */
	public function fields_to_copy_should_be_empty() {
		$subject = new WPML_Beaver_Builder_Data_Settings_For_Media();
		$this->assertEquals( [], $subject->get_fields_to_copy() );
	}

}
