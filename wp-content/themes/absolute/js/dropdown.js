jQuery(document).ready(function() {

    jQuery('.main-menu ul li').has('ul').addClass('hasDownChild');
    jQuery('.main-menu ul ul li').has('ul').addClass('hasRightChild');

    jQuery('.main-menu ul li').hover(function() {
        jQuery(this).find('ul:first').css({visibility:"visible",display:"none"}).fadeIn(300);
    },
    function() {
        jQuery(this).find('ul:first').hide();
    });
});