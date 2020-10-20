<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

class FrmProSimpleBlocksController {

	/**
	 * Adds View values to info sent to block JS
	 *
	 * @param $script_vars
	 *
	 * @return mixed
	 */
	public static function block_editor_assets() {
		$version = FrmAppHelper::plugin_version();

		wp_register_script(
			'formidable-view-selector',
			FrmProAppHelper::plugin_url() . '/js/frm_blocks.js',
			array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-components', 'wp-editor' ),
			$version,
			true
		);

		$script_vars = array(
			'views'        => self::get_views_options(),
			'show_counts'  => FrmProDisplaysHelper::get_show_counts(),
			'view_options' => FrmProDisplaysHelper::get_frm_options_for_views(),
			'name'         => FrmAppHelper::get_menu_name() . ' ' . __( 'Views', 'formidable-pro' ),
			'calc'         => self::get_calc_forms(),
		);

		wp_localize_script( 'formidable-view-selector', 'formidable_view_selector', $script_vars );
		if ( function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( 'formidable-view-selector', 'formidable-pro', FrmProAppHelper::plugin_path() . '/languages' );
		}
	}

	/**
	 * Returns an array of Views options with name as the label and the id as the value, sorted by label
	 *
	 * @return array
	 */
	private static function get_views_options() {
		$views         = FrmProDisplay::getAll( array(), 'post_title' );
		$views_options = array_map( 'self::set_view_options', $views );
		$views_options = array_reverse( $views_options );

		return $views_options;
	}

	/**
	 * For a View, returns an array with the title as label and id as value
	 *
	 * @param $view
	 *
	 * @return array
	 */
	private static function set_view_options( $view ) {
		return array(
			'label' => $view->post_title,
			'value' => $view->ID,
		);
	}

	/**
	 * Returns a filtered list of form options with the name as label and the id as value, sorted by label.
	 * Get all total fields and calculated fields.
	 *
	 * @return array
	 *
	 * @since 4.05
	 */
	private static function get_calc_forms() {
		$where = array(
			'or'            => 1,
			'type'          => 'total',
			array(
				'field_options like'     => '"calc";s:',
				'field_options not like' => '"calc";s:0:',
			),
		);
		$calc_forms = FrmDb::get_col( 'frm_fields', $where, 'form_id' );
		$calc_forms = array_unique( (array) $calc_forms );

		return self::get_forms( $calc_forms );
	}

	private static function get_forms( $ids ) {
		$forms = FrmForm::getAll(
			array(
				'is_template' => 0,
				'status'      => 'published',
				'id'          => $ids,
			),
			'name'
		);
		return self::set_form_options( $forms );
	}

	/**
	 * Returns an array for a form with name as label and id as value
	 *
	 * @param $form
	 *
	 * @return array
	 *
	 * @since 4.05
	 */
	private static function set_form_options( $forms ) {
		$list   = array();
		$parent = array();
		foreach ( $forms as $form ) {
			if ( isset( $form->parent_form_id ) && ! empty( $form->parent_form_id ) ) {
				$parent[] = $form->parent_form_id;
			} else {
				$list[ $form->id ] = array(
					'label' => $form->name,
					'value' => $form->id,
				);
			}
		}

		if ( ! empty( $parent ) ) {
			$parent = array_diff( $parent, array_keys( $list ) );
			if ( ! empty( $parent ) ) {
				$parents = self::get_forms( $parent );
				$list += $parents;
			}
		}

		$list = array_values( $list );
		return $list;
	}

	/**
	 * Registers simple View block
	 */
	public static function register_simple_view_block() {
		if ( ! is_callable( 'register_block_type' ) ) {
			return;
		}

		if ( is_admin() ) {
			// register back-end scripts
			add_action( 'enqueue_block_editor_assets', 'FrmProSimpleBlocksController::block_editor_assets' );
		}

		register_block_type(
			'formidable/simple-view',
			array(
				'attributes'      => array(
					'viewId'          => array(
						'type' => 'string',
					),
					'filter'          => array(
						'type' => 'string',
						'default' => 'limited',
					),
					'useDefaultLimit' => array(
						'type'    => 'boolean',
						'default' => false,
					),
					'className'   => array(
						'type'    => 'string',
					),
				),
				'editor_style'    => 'formidable',
				'editor_script'   => 'formidable-view-selector',
				'render_callback' => 'FrmProSimpleBlocksController::simple_view_render',
			)
		);

		register_block_type(
			'formidable/calculator',
			array(
				'attributes'      => array(
					'formId'      => array(
						'type' => 'string',
					),
					'title'       => array(
						'type' => 'string',
					),
					'description' => array(
						'type' => 'string',
					),
					'minimize'    => array(
						'type' => 'string',
					),
					'className'   => array(
						'type' => 'string',
					),
				),
				'editor_style'    => 'formidable',
				'editor_script'   => 'formidable-form-selector',
				'render_callback' => 'FrmSimpleBlocksController::simple_form_render',
			)
		);
	}

	/**
	 * Renders a View given the specified attributes.
	 *
	 * @param $attributes
	 *
	 * @return string
	 */
	public static function simple_view_render( $attributes ) {
		if ( ! isset( $attributes['viewId'] ) ) {
			return '';
		}

		$params = array_filter( $attributes );

		$in_editor = strrpos( $_SERVER['REQUEST_URI'], 'context=edit' );
		if ( $in_editor && isset( $params['useDefaultLimit'] ) && ( $params['useDefaultLimit'] ) ) {
			$params['limit'] = 20;
		}
		unset( $params['useDefaultLimit'] );

		$params['id'] = $params['viewId'];
		unset( $params['viewId'] );

		return FrmProDisplaysController::get_shortcode( $params );
	}
}
