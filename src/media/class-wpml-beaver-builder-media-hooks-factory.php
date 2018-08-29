<?php

class WPML_Beaver_Builder_Media_Hooks_Factory implements IWPML_Backend_Action_Loader {

	public function create() {
		global $sitepress;

		return new WPML_Beaver_Builder_Media_Hooks( $sitepress );
	}
}
