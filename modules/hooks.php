<?php 

add_filter( 'wp_head', 'wcb_wp_head' ) ;


function wcb_wp_head( $columns ) {
    $botAppId = get_post_meta( $id, 'bot_appId', true );

	echo '
	<script> window.fbMessengerPlugins = window.fbMessengerPlugins || {
init: function() {
FB.init({ appId:"'.$botAppId.'", xfbml: true, version: "v2.6" });
FB.Event.subscribe(\'send_to_messenger\', function(response) {
if (response.event == \'clicked\') {
jQuery(\'.locker_container\').removeClass(\'locker_container\');
jQuery(\'.content_overlap\').replaceWith(\'\');
jQuery(\'.locker_container .inner_locker\').fadeIn();
};
});
},
callable: []
};
 
  </script>
	';
}
 

?>