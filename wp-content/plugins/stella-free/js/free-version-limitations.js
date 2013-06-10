/*
 * Usage: Free
 */
jQuery(document).ready(function($) {
	
	if( jQuery('.lang-wrapper').length > 1 ){
		jQuery('#add-host').unbind('click', add_host_handler);
	}

	$('#about-rosetta-full').cycle({ 
		fx:      'scrollLeft', 
		speed:    500, 
		timeout:  5000 
	});
	
	$('#about-rosetta-full').click(function () {
		$(window.location).attr('href', 'http://store.theme.fm/plugins/rosetta/');
    });
	
	// add_host click event
	jQuery('#add-host').bind( 'click', function(){
		if( jQuery('.lang-wrapper').length > 0 ){
			jQuery('#add-host').unbind('click', add_host_handler);
		}
	});
	
	// delete click event
	$('.lang-wrapper .del-button').live('click',function(){
		if( $('.lang-wrapper').length <= 2 ){
			jQuery('#add-host').bind( 'click', add_host_handler );
		}
	});
});