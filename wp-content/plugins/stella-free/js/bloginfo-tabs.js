/*
 * Usage: Everywhere
 */
jQuery(document).ready(function($) {
	if( bloginfo_langs != null ){
		var bloginfo_vars = $.parseJSON(bloginfo_langs);		
	}
	if( $('#blogname').length )
	{
		$('form table.form-table').before('<div id="bloginfo-tabs"></div>');
		$('#bloginfo-tabs').prepend('<div class="tabs-nav"><ul></ul></div>');

		for(i = 0; i < bloginfo_vars['langs'].length;i++){
			// tab navigation elements 
			if( i == 0 ) $('#bloginfo-tabs .tabs-nav ul').append('<li><a href="#bloginfo-tab-'+ bloginfo_vars['langs'][i][0]+'">' + bloginfo_vars['langs'][i][1] + '<span class="default-label"> ( ' + bloginfo_vars['default_str'] + ' )</span></a></li>');
			else $('#bloginfo-tabs .tabs-nav ul').append('<li><a href="#bloginfo-tab-'+ bloginfo_vars['langs'][i][0]+'">' + bloginfo_vars['langs'][i][1] + '</a></li>');
			// tab elements
			$('#bloginfo-tabs').append('<div id="bloginfo-tab-' + bloginfo_vars['langs'][i][0] + '"><table class="form-table"></table></div>');
			if( i == 0) {
				var blogname_elem = $('#blogname').parent().parent().detach();
				var blogdescription_elem = $('#blogdescription').parent().parent().detach();
				$('#bloginfo-tab-' + bloginfo_vars['langs'][i][0] + ' table').append(blogname_elem);
				$('#bloginfo-tab-' + bloginfo_vars['langs'][i][0] + ' table').append(blogdescription_elem);
			}else{
				var blogname_elem = $('#blogname-' + bloginfo_vars['langs'][i][0] ).parent().parent().detach();
				var blogdescription_elem = $('#blogdescription-' + bloginfo_vars['langs'][i][0] ).parent().parent().detach();
				$('#bloginfo-tab-' + bloginfo_vars['langs'][i][0] + ' table').append(blogname_elem);
				$('#bloginfo-tab-' + bloginfo_vars['langs'][i][0] + ' table').append(blogdescription_elem);
			}
		}
		$('#bloginfo-tabs').append('<div class="tabs-separator"></div>');
	}
	$('#bloginfo-tabs').tabs(); 
});
