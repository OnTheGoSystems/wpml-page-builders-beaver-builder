<?php

class WPML_Beaver_Builder_Media_Node_Provider {

	/** @var WPML_Page_Builders_Media_Translate_Helper $translate_helper */
	private $translate_helper;

	/** @var WPML_Beaver_Builder_Media_Node[] */
	private $nodes = array();

	/**
	 * @param string $type
	 *
	 * @return WPML_Beaver_Builder_Media_Node
	 */
	public function get( $type ) {
		if ( ! array_key_exists( $type, $this->nodes ) ) {
			switch ( $type ) {
				case 'photo':
					$node = new WPML_Beaver_Builder_Media_Node_Photo( $this->get_translation_helper() );
					break;

				case 'gallery':
					$node = new WPML_Beaver_Builder_Media_Node_Gallery( $this->get_translation_helper() );
					break;

				default:
					$node = null;
			}

			$this->nodes[ $type ] = $node;
		}

		return $this->nodes[ $type ];
	}

	/** @return WPML_Page_Builders_Media_Translate_Helper */
	private function get_translation_helper() {
		global $sitepress;

		if ( ! $this->translate_helper ) {
			$this->translate_helper = new WPML_Page_Builders_Media_Translate_Helper(
				new WPML_Translation_Element_Factory( $sitepress ),
				new WPML_Media_Image_Translate( $sitepress, new WPML_Media_Attachment_By_URL_Factory() )
			);
		}

		return $this->translate_helper;
	}
}
