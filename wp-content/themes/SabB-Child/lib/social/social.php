<?php
/*****************************************************************************
*
*	copyright(c) - sitemile.com - ProjectTheme
*	More Info: http://sitemile.com/p/project
*	Coder: Saioc Dragos Andrei
*	Email: andreisaioc@gmail.com
*
******************************************************************************/
	 
	 include 'utils.php';
	 
	 function ProjectTheme_render_login_page_uri(){
	?>
	<input type="hidden" id="social_connect_login_form_uri" value="<?php echo site_url( 'wp-login.php', 'login_post' ); ?>" />
	<?php
	}


/****************************************************************************************
*
*	Pricerr Theme - fnc
*
*****************************************************************************************/
	 
	function ProjectTheme_display_social_login()
	{
		
		$images_url = get_bloginfo('template_url') . "/lib/social/img/";
		$ProjectTheme_enable_facebook_login = get_option('ProjectTheme_enable_facebook_login');
		$ProjectTheme_enable_twitter_login 	= get_option('ProjectTheme_enable_twitter_login');
		
		if($ProjectTheme_enable_facebook_login == "yes" or $ProjectTheme_enable_twitter_login == "yes"):
		?>
        
        	<p>
					<label><?php _e('Connect with:','ProjectTheme'); ?></label>
        
        
        <?php
		
		 if( $ProjectTheme_enable_facebook_login == "yes" ) : ?>
				<a href="#" title="Facebook" class="social_connect_login_facebook "><img class="sclk" alt="Facebook" src="<?php echo $images_url . 'facebook_32.png' ?>" /></a>
			<?php endif; ?>
			<?php if( $ProjectTheme_enable_twitter_login == "yes" ) : ?>
				<a href="#" title="Twitter" class="social_connect_login_twitter "><img class="sclk" alt="Twitter" src="<?php echo $images_url . 'twitter_32.png' ?>" /></a>
			<?php endif; 
		
		
		?>
        
        <div id="social_connect_facebook_auth">
		<input type="hidden" name="client_id" value="<?php echo get_option( 'ProjectTheme_facebook_app_id' ); ?>" />
		<input type="hidden" name="redirect_uri" value="<?php echo urlencode( get_bloginfo('template_url') . '/lib/social/facebook/callback.php' ); ?>" />
		</div>
		<div id="social_connect_twitter_auth"><input type="hidden" name="redirect_uri" value="<?php echo( get_bloginfo('template_url') . '/lib/social/twitter/connect.php' ); ?>" /></div>
        
        </p>
        <?php
		
		endif;
		
	}

/****************************************************************************************
*
*	Pricerr Theme - fnc
*
*****************************************************************************************/
	
	function ProjectTheme_social_connect_process_login( $is_ajax = false ){
	if ( isset( $_REQUEST[ 'redirect_to' ] ) && $_REQUEST[ 'redirect_to' ] != '' ) {
		$redirect_to = $_REQUEST[ 'redirect_to' ];
		// Redirect to https if user wants ssl
		if ( isset( $secure_cookie ) && $secure_cookie && false !== strpos( $redirect_to, 'wp-admin') )
			$redirect_to = preg_replace( '|^http://|', 'https://', $redirect_to );
	} else {
		$redirect_to = admin_url();
	}
	$redirect_to = apply_filters( 'social_connect_redirect_to', $redirect_to );

	$social_connect_provider = $_REQUEST[ 'social_connect_provider' ];
	$sc_provider_identity_key = 'social_connect_' . $social_connect_provider . '_id';
	$sc_provided_signature =  $_REQUEST[ 'social_connect_signature' ];

	switch( $social_connect_provider ) {
		case 'facebook':
		social_connect_verify_signature( $_REQUEST[ 'social_connect_access_token' ], $sc_provided_signature, $redirect_to );
		$fb_json = json_decode( sc_curl_get_contents("https://graph.facebook.com/me?access_token=" . $_REQUEST[ 'social_connect_access_token' ]) );
		$sc_provider_identity = $fb_json->{ 'id' };
		$sc_email = $fb_json->{ 'email' };
		$sc_first_name = $fb_json->{ 'first_name' };
		$sc_last_name = $fb_json->{ 'last_name' };
		$sc_profile_url = $fb_json->{ 'link' };
		$sc_name = $sc_first_name . ' ' . $sc_last_name;
		$user_login = strtolower( $sc_first_name.$sc_last_name );
		break;

		case 'twitter':
		$sc_provider_identity = $_REQUEST[ 'social_connect_twitter_identity' ];
		social_connect_verify_signature( $sc_provider_identity, $sc_provided_signature, $redirect_to );
		$sc_name = $_REQUEST[ 'social_connect_name' ];
		$names = explode(" ", $sc_name );
		$sc_first_name = $names[0];
		$sc_last_name = $names[1];
		$sc_screen_name = $_REQUEST[ 'social_connect_screen_name' ];
		$sc_profile_url = '';
		// Get host name from URL
		$site_url = parse_url( site_url() );
		$sc_email = 'tw_' . md5( $sc_provider_identity ) . '@' . $site_url['host'];
		$user_login = $sc_screen_name;
		break;

		case 'google':
		$sc_provider_identity = $_REQUEST[ 'social_connect_openid_identity' ];
		social_connect_verify_signature( $sc_provider_identity, $sc_provided_signature, $redirect_to );
		$sc_email = $_REQUEST[ 'social_connect_email' ];
		$sc_first_name = $_REQUEST[ 'social_connect_first_name' ];
		$sc_last_name = $_REQUEST[ 'social_connect_last_name' ];
		$sc_profile_url = '';
		$sc_name = $sc_first_name . ' ' . $sc_last_name;
		$user_login = strtolower( $sc_first_name.$sc_last_name );
		break;

		case 'yahoo':
		$sc_provider_identity = $_REQUEST[ 'social_connect_openid_identity' ];
		social_connect_verify_signature( $sc_provider_identity, $sc_provided_signature, $redirect_to );
		$sc_email = $_REQUEST[ 'social_connect_email' ];
		$sc_name = $_REQUEST[ 'social_connect_name' ];
		$sc_username = $_REQUEST[ 'social_connect_username' ];
		$sc_profile_url = '';
		if ( $sc_name == '') {
			if ( $sc_username == '') {
				$names = explode("@", $sc_email );
				$sc_name = $names[0];
				$sc_first_name = $sc_name;
				$sc_last_name = '';
			} else {
				$names = explode(" ", $sc_username );
				$sc_first_name = $names[0];
				$sc_last_name = $names[1];
			}
		} else {
			$names = explode(" ", $sc_name );
			$sc_first_name = $names[0];
			$sc_last_name = $names[1];
		}
		$user_login = strtolower( $sc_first_name.$sc_last_name );
		break;

		case 'wordpress':
		$sc_provider_identity = $_REQUEST[ 'social_connect_openid_identity' ];
		social_connect_verify_signature( $sc_provider_identity, $sc_provided_signature, $redirect_to );
		$sc_email = $_REQUEST[ 'social_connect_email' ];
		$sc_name = $_REQUEST[ 'social_connect_name' ];
		$sc_profile_url = '';
		if ( trim( $sc_name ) == '') {
			$names = explode("@", $sc_email );
			$sc_name = $names[0];
			$sc_first_name = $sc_name;
			$sc_last_name = '';
		} else {
			$names = explode(" ", $sc_name );
			$sc_first_name = $names[0];
			$sc_last_name = $names[1];
		}
		$user_login = strtolower( $sc_first_name.$sc_last_name );
		break;
	}

	// Cookies used to display welcome message if already signed in recently using some provider
	setcookie("social_connect_current_provider", $social_connect_provider, time()+3600, SITECOOKIEPATH, COOKIE_DOMAIN, false, true );

	// Get user by meta
	$user_id = social_connect_get_user_by_meta( $sc_provider_identity_key, $sc_provider_identity );
	if ( $user_id ) {
		$user_data  = get_userdata( $user_id );
		$user_login = $user_data->user_login;
	} elseif ( $user_id = email_exists( $sc_email ) ) { // User not found by provider identity, check by email
		update_user_meta( $user_id, $sc_provider_identity_key, $sc_provider_identity );

		$user_data  = get_userdata( $user_id );
		$user_login = $user_data->user_login;

	} else { // Create new user and associate provider identity
		if ( username_exists( $user_login ) )
			$user_login = apply_filters( 'social_connect_username_exists', strtolower("sc_". md5( $social_connect_provider . $sc_provider_identity ) ) );

		$userdata = array( 'user_login' => $user_login, 'user_email' => $sc_email, 'first_name' => $sc_first_name, 'last_name' => $sc_last_name, 'user_url' => $sc_profile_url, 'user_pass' => wp_generate_password() );

		// Create a new user
		$user_id = wp_insert_user( $userdata );

		if ( $user_id && is_integer( $user_id ) )
			update_user_meta( $user_id, $sc_provider_identity_key, $sc_provider_identity );
	}

	wp_set_auth_cookie( $user_id );

	do_action( 'social_connect_login', $user_login );

	if ( $is_ajax )
		echo '{"redirect":"' . $redirect_to . '"}';
	else
		wp_safe_redirect( $redirect_to );
	exit();
}


/****************************************************************************************
*
*	Pricerr Theme - fnc
*
*****************************************************************************************/
	
	add_action( 'login_form_social_connect', 	'ProjectTheme_social_connect_process_login');
	add_filter('login_form', 					'ProjectTheme_display_social_login');
	add_filter('register_form', 					'ProjectTheme_display_social_login');
	add_action( 'wp_footer', 					'ProjectTheme_render_login_page_uri' );
	
?>