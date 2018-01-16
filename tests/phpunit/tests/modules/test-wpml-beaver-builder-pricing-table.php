<?php

/**
 * Class Test_WPML_Beaver_Builder_Pricing_Table
 * @group page-builders
 * @group beaver-builder
 */
class Test_WPML_Beaver_Builder_Pricing_Table extends OTGS_TestCase {

	const FIELDS_PER_PRICING_COLUMN = 7;

	public function setUp() {
		parent::setUp();
		\WP_Mock::wpPassthruFunction( 'esc_html__' );

		$this->settings = (object) array(
			'pricing_columns' => array(
				(object) array(
					'title'       => 'title1',
					'button_text' => 'button1',
					'button_url'  => 'http://my-site.com',
					'features'    => array( 'feature1-1', 'feature1-2' ),
					'price'       => 'price1',
					'duration'    => 'duration1',
				),
				(object) array(
					'title'       => 'title2',
					'button_text' => 'button2',
					'button_url'  => 'http://my-site.com',
					'features'    => array( 'feature2-1', 'feature2-2' ),
					'price'       => 'price2',
					'duration'    => 'duration2',
				),
			)
		);

	}

	public function test_get() {
		$node_id = rand( 1, 1000 );
		$strings = array();

		$subject = new WPML_Beaver_Builder_Pricing_Table();
		$strings = $subject->get( $node_id, $this->settings, $strings );

		$this->assertCount( 2 * self::FIELDS_PER_PRICING_COLUMN, $strings );

		foreach ( $this->settings->pricing_columns as $index => $item ) {
			$this->assertEquals( $item->title, $strings[ $index * self::FIELDS_PER_PRICING_COLUMN ]->get_value() );
			$this->assertEquals( md5( $item->title ) . '-title-' . $node_id, $strings[ $index * self::FIELDS_PER_PRICING_COLUMN ]->get_name() );
			$this->assertEquals( 'Pricing table: Title', $strings[ $index * self::FIELDS_PER_PRICING_COLUMN ]->get_title() );
			$this->assertEquals( 'LINE', $strings[ $index * self::FIELDS_PER_PRICING_COLUMN ]->get_editor_type() );

			$this->assertEquals( $item->button_text, $strings[ $index * self::FIELDS_PER_PRICING_COLUMN + 1 ]->get_value() );
			$this->assertEquals( md5( $item->button_text ) . '-button_text-' . $node_id, $strings[ $index * self::FIELDS_PER_PRICING_COLUMN + 1 ]->get_name() );
			$this->assertEquals( 'Pricing table: Button text', $strings[ $index * self::FIELDS_PER_PRICING_COLUMN + 1 ]->get_title() );
			$this->assertEquals( 'LINE', $strings[ $index * self::FIELDS_PER_PRICING_COLUMN + 1 ]->get_editor_type() );

			$this->assertEquals( $item->button_url, $strings[ $index * self::FIELDS_PER_PRICING_COLUMN + 2 ]->get_value() );
			$this->assertEquals( md5( $item->button_url ) . '-button_url-' . $node_id, $strings[ $index * self::FIELDS_PER_PRICING_COLUMN + 2 ]->get_name() );
			$this->assertEquals( 'Pricing table: Button link', $strings[ $index * self::FIELDS_PER_PRICING_COLUMN + 2 ]->get_title() );
			$this->assertEquals( 'LINK', $strings[ $index * self::FIELDS_PER_PRICING_COLUMN + 2 ]->get_editor_type() );

			$this->assertEquals( $item->features[0], $strings[ $index * self::FIELDS_PER_PRICING_COLUMN + 3 ]->get_value() );
			$this->assertEquals( md5( $item->features[0] ) . '-features0-' . $node_id, $strings[ $index * self::FIELDS_PER_PRICING_COLUMN + 3 ]->get_name() );
			$this->assertEquals( 'Pricing table: Feature', $strings[ $index * self::FIELDS_PER_PRICING_COLUMN + 3 ]->get_title() );
			$this->assertEquals( 'VISUAL', $strings[ $index * self::FIELDS_PER_PRICING_COLUMN + 3 ]->get_editor_type() );

			$this->assertEquals( $item->features[1], $strings[ $index * self::FIELDS_PER_PRICING_COLUMN + 4 ]->get_value() );
			$this->assertEquals( md5( $item->features[1] ) . '-features1-' . $node_id, $strings[ $index * self::FIELDS_PER_PRICING_COLUMN + 4 ]->get_name() );
			$this->assertEquals( 'Pricing table: Feature', $strings[ $index * self::FIELDS_PER_PRICING_COLUMN + 4 ]->get_title() );
			$this->assertEquals( 'VISUAL', $strings[ $index * self::FIELDS_PER_PRICING_COLUMN + 4 ]->get_editor_type() );

			$this->assertEquals( $item->price, $strings[ $index * self::FIELDS_PER_PRICING_COLUMN + 5 ]->get_value() );
			$this->assertEquals( md5( $item->price ) . '-price-' . $node_id, $strings[ $index * self::FIELDS_PER_PRICING_COLUMN + 5 ]->get_name() );
			$this->assertEquals( 'Pricing table: Price', $strings[ $index * self::FIELDS_PER_PRICING_COLUMN + 5 ]->get_title() );
			$this->assertEquals( 'LINE', $strings[ $index * self::FIELDS_PER_PRICING_COLUMN + 5 ]->get_editor_type() );

			$this->assertEquals( $item->duration, $strings[ $index * self::FIELDS_PER_PRICING_COLUMN + 6 ]->get_value() );
			$this->assertEquals( md5( $item->duration ) . '-duration-' . $node_id, $strings[ $index * self::FIELDS_PER_PRICING_COLUMN + 6 ]->get_name() );
			$this->assertEquals( 'Pricing table: Duration', $strings[ $index * self::FIELDS_PER_PRICING_COLUMN + 6 ]->get_title() );
			$this->assertEquals( 'LINE', $strings[ $index * self::FIELDS_PER_PRICING_COLUMN + 6 ]->get_editor_type() );

		}
	}

	public function test_update() {

		$node_id = rand( 1, 1000 );

		$subject = new WPML_Beaver_Builder_Pricing_Table();

		foreach ( $this->settings->pricing_columns as $item ) {

			foreach ( array( 'title', 'button_text', 'features', 'price', 'duration' ) as $field ) {
				if ( is_array( $item->$field ) ) {
					foreach ( $item->$field as $key => $value ) {
						$translation = rand_str();
						$string      = new WPML_PB_String(
							$translation,
							md5( $value ) . '-' . $field . $key . '-' . $node_id,
							'Anything',
							'Anything'
						);
						$subject->update( $node_id, $this->settings, $string );

						$value = $item->$field;
						$this->assertEquals( $translation, $value[ $key ] );
					}
				} else {
					$translation = rand_str();
					$string      = new WPML_PB_String(
						$translation,
						md5( $item->$field ) . '-' . $field . '-' . $node_id,
						'Anything',
						'Anything'
					);
					$subject->update( $node_id, $this->settings, $string );

					$this->assertEquals( $translation, $item->$field );
				}
			}
		}

	}

}
