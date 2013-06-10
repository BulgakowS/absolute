/*
 * Usage: Everywhere
 */

jQuery(document).ready(function($) {  
		// *bad programming style. must be fixed by sending array of language from php
				
		if( $('#poststuff').length ){
			
			if( post_vars != null ){
				var _post_vars = $.parseJSON(post_vars);
			}
			// hide screen options checkboxeses
			hide_checkboxes( _post_vars['post_type'] +'-postimagediv', _post_vars);
			hide_checkboxes('excerpt', _post_vars);
			hide_checkboxes('post-in', _post_vars);
			
			var id_prefix = 'post-in-';
			// move elements inside one div
			$('#titlediv').before('<div id="post-tabs"><div id="default-lang-editor"></div></div>');
			$('#titlediv').detach().appendTo($('#default-lang-editor'));
			$('#postdivrich').detach().appendTo($('#default-lang-editor'));
			$('#postexcerpt').detach().appendTo($('#default-lang-editor'));
			// finding all elemets with id = post-in-"lang"
			$('div#poststuff div').each(function(indx, element){
				var idStr = element.id;
				if(idStr.substring(0,id_prefix.length)==id_prefix) {
					$(element).detach().appendTo($('#post-tabs'));		
					// clean metaboxes
					$(element).find('.handlediv').detach();
					$(element).find('.hndle').detach();
					element.className = "";
				}
			});
			// excerpts
			for(i = 1; i < _post_vars['langs'].length; i++){
				$('#excerpt-'+_post_vars['langs'][i][0]).detach().appendTo($('#post-in-'+_post_vars['langs'][i][0]));
			}
			// add navigation
			var nav_html = '<div class="tabs-nav"><ul><li><a href="#default-lang-editor">' + _post_vars['langs'][0][1] + '<span class="default-label"> ( ' + _post_vars['default_str'] + ' )</span></a></li>';
			for(i = 1; i < _post_vars['langs'].length; i++){
				nav_html+= '<li><a href="#' + id_prefix + _post_vars['langs'][i][0] + '">' + _post_vars['langs'][i][1] + '</a></li>';
			}
			nav_html += '</ul></div>';
			$('#default-lang-editor').before(nav_html);
			
			// turning tabs on
			$('#post-tabs').tabs();	
			
			// hide checkboxes
			$('#postexcerpt-hide').click( function(e){
			update_from_checkboxes('excerpt','postexcerpt', _post_vars);
			});
			$('#postimagediv-hide').click( function(e){
				update_from_checkboxes( _post_vars['post_type'] +'-postimagediv', 'postimagediv', _post_vars);
			});
			update_from_checkboxes( 'excerpt','postexcerpt', _post_vars );
			update_from_checkboxes( _post_vars['post_type'] +'-postimagediv', 'postimagediv', _post_vars );
		}
}); 

function update_from_checkboxes( checkbox_id_prefix, metabox_id_prefix, _post_vars ){
	if(jQuery( '#' + metabox_id_prefix + '-hide').is(':checked')){
		for(i = 1; i < _post_vars['langs'].length; i++){
			//jQuery( '#' + checkbox_id_prefix + '-' + _post_vars['langs'][i][0] + '-hide').attr('checked', true);
			jQuery( '#' + checkbox_id_prefix + '-' + _post_vars['langs'][i][0]).css('display','block');
		}
	}else{
		for(i = 1; i < _post_vars['langs'].length; i++){
			jQuery( '#' + checkbox_id_prefix + '-' +_post_vars['langs'][i][0]).css('display','none');
		}
	}
}

function hide_checkboxes( checkbox_id_prefix, _post_vars ){
	for(i = 1; i < _post_vars['langs'].length; i++){
		jQuery( 'label[for=' + checkbox_id_prefix + '-' + _post_vars['langs'][i][0] + '-hide]' ).css('display','none');
	}
}
