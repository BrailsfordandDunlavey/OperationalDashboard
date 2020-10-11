<?php

if ( ! defined( 'ABSPATH' ) ) {
    die( 'You are not allowed to call this page directly.' );
}

/**
 * @since 3.0
 */
class FrmProFieldNumber extends FrmFieldNumber {

	protected function field_settings_for_type() {
		$settings = parent::field_settings_for_type();

		$settings['autopopulate'] = true;
		$settings['calc'] = true;
		$settings['unique'] = true;
		$settings['read_only'] = true;
		$settings['prefix']    = true;

		FrmProFieldsHelper::fill_default_field_display( $settings );
		return $settings;
	}

	/**
	 * @since 4.05
	 */
	protected function builder_text_field( $name = '' ) {
		$html  = FrmProFieldsHelper::builder_page_prepend( $this->field );
		$field = parent::builder_text_field( $name );
		return str_replace( '[input]', $field, $html );
	}

	protected function prepare_display_value( $value, $atts ) {

		$new_val = array();
		$value = array_filter( (array) $value, 'strlen' );

		foreach ( $value as $v ) {
			if ( strpos( $v, $atts['sep'] ) ) {
				$v = explode( $atts['sep'], $v );
			}

			foreach ( (array) $v as $n ) {
				if ( ! isset( $atts['decimal'] ) ) {
					$num = explode( '.', $n );
					$atts['decimal'] = isset( $num[1] ) ? strlen( $num[1] ) : 0;
				}

				if ( is_numeric( $n ) ) {
					$n = number_format( $n, $atts['decimal'], $atts['dec_point'], $atts['thousands_sep'] );
				}

				$new_val[] = $n;
			}

			unset( $v );
		}
		$new_val = array_filter( (array) $new_val, 'strlen' );

		return implode( $atts['sep'], $new_val );
	}

	protected function fill_default_atts( &$atts ) {
		$defaults = array(
			'dec_point' => '.',
			'thousands_sep' => '',
			'sep'       => ', ',
		);
		$atts = wp_parse_args( $atts, $defaults );
	}

	protected function prepare_import_value( $value, $atts ) {
		if ( is_numeric( $value ) ) {
			$value = (string) $value;
		}
		return $value;
	}
}
