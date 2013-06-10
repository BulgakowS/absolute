/*
 * Usage: Everywhere
 */

function update_selects(){
	jQuery('.select select').each(function(){
		jQuery(this).parent().next().text(jQuery(this).val());
	});
}
function init_switchers(){
	jQuery('.switchbox').each(function(){
		if(jQuery(this).is(':checked')){
			jQuery(this).prev().css("left","-=44px");
			jQuery(this).next().css("backgroundColor", "rgb(162,198,109)");
			jQuery(this).next().find('.rail').css("left","-=35px");
		}
	});
}
function init_selects(){
	jQuery('.select').each(function(index, element){
		jQuery(element).after('<div class="select-value"></div>');
	});
}
function add_host_handler() {
		var select = jQuery("select[name='default-lang']").parent().clone();
		jQuery(select).find('select').attr("name","lang[]");
		var hidden = false;
		if( ! jQuery('#use-hosts').length )
			hidden = " hidden";
		else
			hidden = ( jQuery('#use-hosts').is(':checked') ) ? "" :  " hidden";
		jQuery(this).after('<div class="lang-wrapper"><input class="host-options'+ hidden+'" type="text" name="host[]" value="" placeholder="' + options_page_vars['host_placeholder'] + '"/><input class="square-button del-button" type="button" name="del-host" value="' +
			options_page_vars['btn_delete'] + '"/></div>');
		jQuery(this).next().css("display","none");
		jQuery(this).next().prepend(select);
		jQuery(this).next().show('fast');
		jQuery(select).after('<div class="select-value"></div>');
		jQuery(select).find('select').click( function() {jQuery(this).parent().next().text(jQuery(this).val());});
		jQuery(select).find('select').keyup( function(e) {
			if(e.keyCode == 13){
				jQuery(this).parent().next().text(jQuery(this).val());
				update_selects();
			}
		});
		update_selects();		
}
	
jQuery(document).ready(function() {
	
	init_switchers(); 
	init_selects();
	
	update_selects();
	
	// select handlers
	jQuery('.select select').click( function() {
        jQuery(this).parent().next().text(jQuery(this).val());
    });
	jQuery('.select select').keyup( function(e) {
		if(e.keyCode == 13){
			jQuery(this).parent().next().text(jQuery(this).val());
			update_selects();
		}    
    });
    jQuery('.select select').change( function(e) {
        update_selects();
    });
	
    // add new host
    jQuery('#add-host').bind( 'click.add_host', add_host_handler );
	
	// submit button
    jQuery('#submit-options').click( function() {
        if(jQuery('#use-hosts').is(':checked')) return confirm(options_page_vars['confirm_msg']);
    });
	
	// switchboxes handler
	jQuery('.switchbox').click( function(e) {
		if(jQuery(this).is(':checked')){
			jQuery(this).prev().animate({left:'-=44px'},200,"swing");
			jQuery(this).next().animate({backgroundColor: 'rgb(162,198,109)'},200,"swing");
			jQuery(this).next().find('.rail').animate({left:'-=35px'},200,"swing");
			 
		}else{
			jQuery(this).prev().animate({left:'+=44px'},200,"swing");
			jQuery(this).next().animate({backgroundColor: 'rgb(218,117,101)'},200,"swing");
			jQuery(this).next().find('.rail').animate({left:'+=35px'},200,"swing");
		} 
	});
	
	// delete button
	jQuery('.lang-wrapper .del-button').live('click',function(){
		var that = this;
		jQuery(that).parent().hide('fast',function(){ jQuery(that).parent().detach(); });
	});
	
	// don't show hosts if use_hosts not cheked
	jQuery('#use-hosts').click( function(e) {
		if(jQuery(this).is(':checked')){			
			jQuery('.host-options').css('display','inline-block');
			jQuery('.host-options').animate({width:'250px',opacity:'100'},300,"swing");		 
		}else{
			jQuery('.host-options').animate({width:'0px',opacity:'0'},300,"swing",function(){
				jQuery('.host-options').css('display','none');
			});
		} 
	});
	
});