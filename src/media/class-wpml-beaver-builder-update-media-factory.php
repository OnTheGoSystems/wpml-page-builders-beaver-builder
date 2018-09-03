<?php

class WPML_Beaver_Builder_Update_Media_Factory implements IWPML_PB_Media_Update_Factory {

	public function create() {
		global $sitepress;

		return new WPML_Page_Builders_Update_Media(
			new WPML_Page_Builders_Update( new WPML_Beaver_Builder_Data_Settings() ),
			new WPML_Translation_Element_Factory( $sitepress ),
			new WPML_Beaver_Builder_Media_Node_Iterator( new WPML_Beaver_Builder_Media_Node_Provider() )
		);
	}
}
