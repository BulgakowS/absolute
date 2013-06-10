/*
 * Usage: Everywhere
 */

jQuery(document).ready(function($) {

        if(post_vars != null){
            _post_langs = $.parseJSON(post_vars);
        }

        $( '.edit-menu-item-title').each( function() {
            $(this).add_language_fields();
        });
		
});


jQuery.fn.add_language_fields = function() {

    for(i = 1; i < _post_langs['langs'].length; i++){
        item = jQuery(this).clone().addClass('secondary-language-title');

        menu_item_id = item.attr('name').replace('menu-item-title[','').replace(']','');
        item.attr('id', 'stella-'+item.attr('id')+'-'+_post_langs['langs'][i][0]).attr('name', 'stella-menu-item-title[' + _post_langs['langs'][i][0] + '][' + menu_item_id + ']');
        title = menu_strings.titles[parseInt(menu_item_id)];
        if ( title )
            title = title[_post_langs['langs'][i][0]];

        item.val( title );

        p = jQuery(this).parent().parent().clone();
        p.find('label').attr('for', item.attr('id') ).html(  _post_langs['langs'][i][1] + ' ' + menu_strings.navigation_label ).append(jQuery('<br/>')).append(item);

        p.insertAfter( jQuery(this).parent().parent() );
    }
}

