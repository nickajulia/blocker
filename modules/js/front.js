jQuery(document).ready( function($){
	$('.out_text, .locker_container button, .locker_container input[type="button"], .click2reveal').click(function(){
		$('.locker_container').css('height', 'auto');
		$('.locker_container').removeClass('locker_container');
		$('.content_overlap').replaceWith('');
		$('.locker_container .inner_locker').fadeIn();
	})
	
}) // global end
