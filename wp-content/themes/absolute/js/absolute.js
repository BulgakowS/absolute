/* ABSOLUTE JAVASCRIPT */
jQuery(document).ready(function() {
    jQuery('.entry-content *:last-child').addClass('last');
    jQuery('.comment-content *:last-child').addClass('last');

    jQuery('.portfolio-icon').hover(function() {
        var currId = jQuery(this).attr("id");
        jQuery('.portfolio-icon[id!=' + currId + ']').find('img').animate({ opacity: 0.5}, 500);
    }, function() {
        jQuery('.portfolio-icon img').animate({ opacity: 1.0}, 500);
    });
});