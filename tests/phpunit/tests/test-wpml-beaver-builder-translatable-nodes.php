<?php

/**
 * Class Test_WPML_Beaver_Builder_Translatable_Nodes
 * @group page-builders
 * @group beaver-builder
 */
class Test_WPML_Beaver_Builder_Translatable_Nodes extends OTGS_TestCase {

	/**
	 * @dataProvider node_data_provider
	 */
	public function test_get( $type, $field, $expected_title, $expected_editor_type ) {
		$wrap_tag = 'h2';

		\WP_Mock::wpPassthruFunction( '__' );

		$node_id  = mt_rand( 1, 100 );
		$settings = (object) array( 'type' => $type, $field => rand_str() );
		if ( 'heading' === $type ) {
			$settings->tag = $wrap_tag;
		}

		$subject = new WPML_Beaver_Builder_Translatable_Nodes();
		$strings = $subject->get( $node_id, $settings );
		$this->assertCount( 1, $strings );
		$string = $strings[0];
		$this->assertEquals( $settings->$field, $string->get_value() );
		$this->assertEquals( $field . '-' . $settings->type . '-' . $node_id, $string->get_name() );
		$this->assertEquals( $expected_title, $string->get_title() );
		$this->assertEquals( $expected_editor_type, $string->get_editor_type() );
		if ( 'heading' === $type ) {
			$this->assertEquals( $wrap_tag, $string->get_wrap_tag() );
		}
	}

	public function node_data_provider() {

		return array(
			array( 'button', 'text', 'Button: Text', 'LINE' ),
			array( 'button', 'link', 'Button: Link', 'LINK' ),

			array( 'heading', 'heading', 'Heading', 'LINE' ),
			array( 'heading', 'link', 'Heading: Link', 'LINK' ),

			array( 'html', 'html', 'HTML', 'VISUAL' ),

			array( 'photo', 'link_url', 'Photo: Link', 'LINK' ),

			array( 'rich-text', 'text', 'Text Editor', 'VISUAL' ),

			array( 'callout', 'title', 'Callout: Heading', 'LINE' ),
			array( 'callout', 'text', 'Callout: Text', 'VISUAL' ),
			array( 'callout', 'cta_text', 'Callout: Call to action text', 'LINE' ),
			array( 'callout', 'link', 'Callout: Link', 'LINK' ),

			array( 'contact-form', 'name_placeholder', 'Contact Form: Name Field Placeholder', 'LINE' ),
			array( 'contact-form', 'subject_placeholder', 'Contact Form: Subject Field Placeholder', 'LINE' ),
			array( 'contact-form', 'email_placeholder', 'Contact Form: Email Field Placeholder', 'LINE' ),
			array( 'contact-form', 'phone_placeholder', 'Contact Form: Phone Field Placeholder', 'LINE' ),
			array( 'contact-form', 'message_placeholder', 'Contact Form: Your Message Placeholder', 'LINE' ),
			array( 'contact-form', 'terms_checkbox_text', 'Contact Form: Checkbox Text', 'LINE' ),
			array( 'contact-form', 'terms_text', 'Contact Form: Terms and Conditions', 'VISUAL' ),
			array( 'contact-form', 'success_message', 'Contact Form: Success Message', 'VISUAL' ),
			array( 'contact-form', 'btn_text', 'Contact Form: Button Text', 'LINE' ),
			array( 'contact-form', 'success_url', 'Contact Form: Redirect Link', 'LINK' ),

			array( 'cta', 'title', 'Call to Action: Heading', 'LINE' ),
			array( 'cta', 'text', 'Call to Action: Text', 'VISUAL' ),
			array( 'cta', 'btn_text', 'Call to Action: Button text', 'LINE' ),
			array( 'cta', 'btn_link', 'Call to Action: Button link', 'LINK' ),

			array( 'subscribe-form', 'terms_checkbox_text', 'Subscribe form: Checkbox Text', 'LINE' ),
			array( 'subscribe-form', 'terms_text', 'Subscribe form: Terms and Conditions', 'VISUAL' ),
			array( 'subscribe-form', 'custom_subject', 'Subscribe form: Notification Subject', 'LINE' ),
			array( 'subscribe-form', 'success_message', 'Subscribe form: Success Message', 'VISUAL' ),
			array( 'subscribe-form', 'btn_text', 'Subscribe form: Button Text', 'LINE' ),
			array( 'subscribe-form', 'success_url', 'Subscribe form: Redirect Link', 'LINK' ),

			array( 'icon', 'text', 'Icon: Text', 'VISUAL' ),
			array( 'icon', 'link', 'Icon: Link', 'LINK' ),

			array( 'map', 'address', 'Map: Address', 'LINE' ),

			array( 'testimonials', 'heading', 'Testimonial: Heading', 'LINE' ),

			array( 'numbers', 'before_number_text', 'Number Counter: Text before number', 'LINE' ),
			array( 'numbers', 'after_number_text', 'Number Counter: Text after number', 'LINE' ),
			array( 'numbers', 'number_prefix', 'Number Counter: Number Prefix', 'LINE' ),
			array( 'numbers', 'number_suffix', 'Number Counter: Number Suffix', 'LINE' ),

			array( 'post-grid', 'no_results_message', 'Posts: No Results Message', 'VISUAL' ),
			array( 'post-grid', 'more_btn_text', 'Posts: Button Text', 'LINE' ),
			array( 'post-grid', 'terms_list_label', 'Posts: Terms Label', 'LINE' ),
			array( 'post-grid', 'more_link_text', 'Posts: More Link Text', 'LINE' ),

			array( 'post-slider', 'more_link_text', 'Posts Slider: More Link Text', 'LINE' ),
		);
	}

	public function test_update() {
		$node_id     = mt_rand( 1, 100 );
		$settings    = (object) array( 'type' => 'rich-text', 'text' => rand_str() );
		$translation = rand_str();

		$string = new WPML_PB_String( $translation, 'text-rich-text-' . $node_id, 'anything', 'anything' );

		$subject  = new WPML_Beaver_Builder_Translatable_Nodes();
		$settings = $subject->update( $node_id, $settings, $string );

		$this->assertEquals( $translation, $settings->text );
	}

}