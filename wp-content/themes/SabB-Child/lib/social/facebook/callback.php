<?php


require_once(dirname(dirname(__FILE__)) . '/constants.php' );



require_once('facebook.php' );
require_once(dirname(dirname(__FILE__)) . '/utils.php' );



$client_id = get_option('ProjectTheme_facebook_app_id');
$secret_key = get_option('ProjectTheme_facebook_app_secret');



if(isset($_GET['code'])) {
  $code = $_GET['code'];
  $client_id = get_option('ProjectTheme_facebook_app_id');
  $secret_key = get_option('ProjectTheme_facebook_app_secret');
  parse_str(sc_curl_get_contents("https://graph.facebook.com/oauth/access_token?" .
    'client_id=' . $client_id . '&redirect_uri=' . urlencode(get_bloginfo('template_url') . '/lib/social/facebook/callback.php') .
    '&client_secret=' .  $secret_key .
    '&code=' . urlencode($code)));
    
  $signature = social_connect_generate_signature($access_token); 


?>
<html>
<head>
<script>


function init() {
  window.opener.wp_social_connect({'action' : 'social_connect', 'social_connect_provider' : 'facebook',
    'social_connect_signature' : '<?php echo $signature ?>',
    'social_connect_access_token' : '<?php echo $access_token ?>'});
    
  window.close();
}
</script>
</head>
<body onLoad="init();">
</body> 
</html>
<?php

} else {
  $redirect_uri = urlencode(get_bloginfo('template_url') . '/lib/social/facebook/callback.php');
  wp_redirect('https://graph.facebook.com/oauth/authorize?client_id=' . $client_id . '&redirect_uri=' . $redirect_uri . '&scope=email');
}
?>
