<?php

class WPML_Beaver_Builder_Update_Media extends WPML_Page_Builders_Update_Media {

	/**
	 * @param array $data_array
	 *
	 * @return array
	 */
	protected function translate_media_in_modules( array $data_array ) {
		foreach ( $data_array as &$data ) {
			if ( is_array( $data ) ) {
				$data = $this->translate_media_in_modules( $data );
			} elseif ( is_object( $data ) && isset( $data->type ) && 'module' === $data->type ) {
				$data->settings = $this->update_media_in_node( $data->settings );
			}
		}

		return $data_array;
	}

	private function update_media_in_node( $settings ) {
		$media_types = array(
			'photo',
			'gallery',
		);

		if ( isset( $settings->type ) && in_array( $settings->type, $media_types, true ) ) {
			// @todo: Implement image URL conversion
		}

		return $settings;
	}
}
