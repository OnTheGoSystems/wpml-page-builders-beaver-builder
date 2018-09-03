<?php

abstract class WPML_Beaver_Builder_Media_Node {

	/** @var WPML_Page_Builders_Media_Translate_Helper $translate_helper */
	protected $translate_helper;

	public function __construct( WPML_Page_Builders_Media_Translate_Helper $translation_helper ) {
		$this->translate_helper = $translation_helper;
	}

	abstract function translate( $node_data, $source_lang, $target_lang );
}