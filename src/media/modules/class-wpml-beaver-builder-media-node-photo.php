<?php

class WPML_Beaver_Builder_Media_Node_Photo extends WPML_Beaver_Builder_Media_Node {

	public function translate( $node_data, $source_lang, $target_lang ) {
		$translated_id = $this->translate_helper->translate_id( $node_data->photo, $target_lang );

		if ( $translated_id !== $node_data->photo ) {
			$node_data->photo     = $translated_id;
			$node_data->photo_src = $this->translate_helper->translate_image_url( $node_data->photo_src, $source_lang, $target_lang );
			$node_data->data      = wp_prepare_attachment_for_js( $translated_id );
		}

		return $node_data;
	}
}
