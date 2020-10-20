<?php
if(!defined('ABSPATH')) die('You are not allowed to call this page directly.');

class FrmAPISettingsController{

    public static function load_hooks() {
        if ( is_admin() ) {
			add_action( 'frm_add_settings_section', 'FrmAPISettingsController::add_settings_section' );

			add_action( 'wp_ajax_frmapi_insert_json', 'FrmAPISettingsController::default_json' );
			add_action( 'wp_ajax_frmapi_test_connection', 'FrmAPISettingsController::test_connection' );

			add_filter( 'frm_before_save_api_action', 'FrmAPISettingsController::clear_raw_before_save' );
        }

		add_action( 'frm_registered_form_actions', 'FrmAPISettingsController::register_actions' );
		add_action( 'frm_trigger_api_action', 'FrmAPISettingsController::trigger_api', 10, 3 );
    }

    public static function register_actions( $actions ) {
		self::enqueue_admin_js();
        $actions['api'] = 'FrmAPIAction';

        include_once( FrmAPIAppHelper::path() . '/models/FrmAPIAction.php' );

        return $actions;
    }

	/**
	 * Enqueue the JS for the settings page
	 *
	 * @since 1.02
	 */
	private static function enqueue_admin_js() {
		if ( self::is_form_settings_page() ) {
			wp_enqueue_script( 'frmapi_admin', FrmAPIAppHelper::plugin_url() . '/js/frmapi-admin.js', array( 'jquery' ) );
		}
	}

	/**
	 * Check if the current page is the form settings page
	 *
	 * @since 1.02
	 *
	 * @return bool
	 */
	private static function is_form_settings_page() {
		$is_form_settings_page = false;

		$page = FrmAppHelper::simple_get( 'page', 'sanitize_title' );
		$action = FrmAppHelper::simple_get( 'frm_action', 'sanitize_title' );

		if ( $page === 'formidable' && $action === 'settings' ) {
			$is_form_settings_page = true;
		}

		return $is_form_settings_page;
	}

    public static function add_settings_section($sections) {
        if ( !isset($sections['api']) ){
			$sections['api'] = array(
				'class'    => 'FrmAPISettingsController',
				'function' => 'show_api_key',
				'name'     => 'API',
				'icon'     => 'frm_feed_icon frm_icon_font',
			);
        }
        return $sections;
    }

	public static function show_api_key() {
		$api_key = self::get_api_key();
		require_once( FrmAPIAppHelper::path() . '/views/settings/api_key.php' );
	}

	private static function get_api_key() {
		$api_key = get_option('frm_api_key');
		if ( ! $api_key ) {
			$api_key = FrmAPIAppHelper::generate( 4, 4 );
			update_option( 'frm_api_key', $api_key );
		}
		return $api_key;
	}

	public static function default_json() {
		$form_id = absint( $_POST['form_id'] );

		$entry = array(
			'item_id' => '[id]',
			'key'     => '[key]',
			'form_id' => $form_id,
			//'updated_by' => '[updated-by]',
			'post_id' => '[post_id]',
			'created_at' => '[created_at format=\'Y-m-d H:i:s\']',
			'updated_at' => '[updated_at format=\'Y-m-d H:i:s\']',
			'is_draft' => '[is_draft]',
		);
		$meta = FrmEntriesController::show_entry_shortcode(array(
			'format' => 'array',
			'user_info' => false, 'default_email' => true,
			'form_id' => $form_id,
		));

		$entry_array = $entry + (array) $meta;

		if ( version_compare( phpversion(), '5.4', '>=' ) ) {
			echo json_encode( $entry_array, JSON_PRETTY_PRINT );
		} else {
			echo json_encode( $entry_array, true );
		}
		wp_die();
	}

    public static function test_connection() {
		$url     = FrmAppHelper::get_param( 'url', '', 'post', 'sanitize_text_field' );
		$api_key = FrmAppHelper::get_param( 'key', '', 'post', 'sanitize_text_field' );

		$headers = array( 'X-Hook-Test' => 'true');
		if ( ! empty( $api_key ) ) {
			$api_key = FrmAPIAppController::prepare_basic_auth_key( $api_key );
			$headers['Authorization'] = 'Basic ' . base64_encode( $api_key );
		}

		$body = json_encode( array('test' => true) );

		$arg_array = array(
			'body'      => $body,
			'timeout'   => FrmAPIAppController::$timeout,
			'headers' => $headers,
		);

		$resp = wp_remote_post( trim( $url ), $arg_array );
		$body = wp_remote_retrieve_body( $resp );

		self::check_for_wp_error( $resp, $body );
		self::check_json_response( $body );
		self::check_response_headers( $resp );

		wp_die();
    }

	/**
	 * @since 1.08
	 */
	private static function check_for_wp_error( $resp, $body ) {
		if ( ! is_wp_error( $resp ) && $body != 'error' ) {
			return;
		}

		$message = __( 'You had an error communicating with that API.', 'frmapi' );
		if ( is_wp_error( $resp ) ) {
			$message .= ' '. $resp->get_error_message();
		}
		echo esc_html( $message );

		wp_die();
	}

	/**
	 * @since 1.08
	 */
	private static function check_json_response( $body ) {
		$json_res = json_decode( $body, true );
		if ( null === $json_res ) {
			return;
		}

		$has_error = is_array( $json_res ) && isset( $json_res['error'] );
		if ( $has_error ) {
			if ( is_array( $json_res['error'] ) ) {
				foreach ( $json_res['error'] as $e ) {
					print_r( $e );
				}
			} else {
				echo esc_html( $json_res['error'] );
			}
			echo esc_html( self::test_connection_warning() );
		} else {
			self::check_message_in_response( $json_res );
		}

		wp_die();
	}

	/**
	 * @since 1.08
	 */
	private static function check_message_in_response( $json_res ) {
		if ( ! is_array( $json_res ) ) {
			echo esc_html( $json_res );
			return;
		}

		foreach ( $json_res as $k => $e ) {
			if ( is_array( $e ) && isset( $e['code'] ) && isset( $e['message'] ) ) {
				$message = $e['message'];
			} else if ( is_array( $e ) ) {
				$message = implode( '- ', $e );
			} else if ( $k == 'success' && $e ) {
				$message = '';
				self::show_success();
			} else {
				$message = $e . ' ';
			}

			echo esc_html( $message );
			unset( $k, $e, $message );
		}
	}

	/**
	 * @since 1.08
	 */
	private static function check_response_headers( $resp ) {
		$has_code = isset( $resp['response'] ) && isset( $resp['response']['code'] );
		if ( ! $has_code ) {
			self::show_success();
			return;
		}

		if ( strpos( $resp['response']['code'], '20' ) === 0 ) {
			if ( isset( $resp['response']['message'] ) ) {
				echo esc_html( $resp['response']['message'] );
			} else {
				self::show_success();
			}
		} else {
			printf(
				esc_html__( 'There was a %1$s error: %2$s', 'formidable' ),
				$resp['response']['code'],
				$resp['response']['message']
			);
			echo esc_html( self::test_connection_warning() );
		}
	}

	/**
	 * @since 1.08
	 */
	private static function show_success() {
		esc_html_e( 'Good to go!', 'frmapi' );
	}

	/**
	 * @since 1.08
	 */
	private static function test_connection_warning() {
		return __( 'In many cases, the test connection lets you know if the link is reachable. The receiving API may not return a successful response when the data is not included. Please submit the form to test with data.', 'frmapi' );
	}

	public static function trigger_api( $action, $entry, $form ) {
		FrmAPIAppController::send_webhooks( $entry, $action );
	}

	public static function clear_raw_before_save( $settings ) {
		$has_raw_data = isset( $settings['data_format'] ) && ! empty( $settings['data_format'] );
		if ( $has_raw_data && $settings['format'] != 'raw' ) {
			$settings['data_format'] = '';
		}
		return $settings;
	}

	/**
	 * @deprecated 1.08 - Use the javascript template instead of ajax.
	 */
	public static function data_row() {
		_deprecated_function( __METHOD__, '1.08', 'Javascript only' );
	}
}
