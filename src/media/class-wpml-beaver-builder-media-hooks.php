<?php

class WPML_Beaver_Builder_Media_Hooks implements IWPML_Action {

	const KEY = 'beaver-builder';

	/** @var $sitepress */
	private $sitepress;

	/**
	 * WPML_Beaver_Builder_Media_Hooks constructor.
	 *
	 * @param $sitepress
	 */
	public function __construct( $sitepress ) {
		$this->sitepress = $sitepress;
	}

	public function add_hooks() {
		add_filter( 'wmpl_pb_get_media_updaters', array( $this, 'add_media_updater' ) );
	}

	/**
	 * @param IWPML_PB_Media_Update[] $updaters
	 *
	 * @return IWPML_PB_Media_Update[]
	 */
	public function add_media_updater( $updaters ) {
		if ( ! array_key_exists( self::KEY, $updaters ) ) {
			$updaters[ self::KEY ] = $this->create_media_updater();
		}

		return $updaters;
	}

	/** @return WPML_Page_Builders_Update_Media */
	private function create_media_updater() {
		return new WPML_Page_Builders_Update_Media(
			new WPML_Page_Builders_Update( new WPML_Beaver_Builder_Data_Settings() ),
			new WPML_Translation_Element_Factory( $this->sitepress ),
			new WPML_Beaver_Builder_Media_Node_Iterator( new WPML_Beaver_Builder_Media_Node_Provider() )
		);
	}
}
