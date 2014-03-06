<?php
/**
 * @package Absolute
 */
?>
<div id="comments">
    <?php if(post_password_required()): ?>
        <p class="nopassword"><?php _e('This post is password protected. Enter the password to view any comments.', 'absolute'); ?></p>
    </div>
    <?php return; endif; ?>

    <?php if(have_comments()): ?>
        <table class="comment-title-container tablayout"><tr>
        <td style="width:50%; text-align:left;">
            <h2 class="comments-title">
                <?php printf(_n('One comment', '%1$s comments', get_comments_number(), 'absolute'), number_format_i18n(get_comments_number())); ?>
            </h2>
        </td>
        <?php if(comments_open()): ?>
        <td style="width:50%; text-align:right;">
            <h4 class="comments-write-link"><a href="#respond"><?php _e('Leave a comment','absolute'); ?></a></h4>
        </td>
        <?php endif; ?>
        </tr></table>

        <?php if(get_comment_pages_count() > 1 && get_option('page_comments')): ?>
        <div class="comment-navigation single-navigation">
            <span class="nav-previous"><?php previous_comments_link(__('Older Comments', 'absolute')); ?></span>
            <span class="nav-next"><?php next_comments_link(__('Newer Comments', 'absolute')); ?></span>
        </div>
        <?php endif; ?>

        <ul class="commentlist">
            <?php wp_list_comments(array('callback' => 'absolute_comment')); ?>
        </ul>
    <?php elseif(!comments_open() && !is_page() && post_type_supports(get_post_type(), 'comments')): ?>
        <p class="nocomments"><?php _e('Comments are closed.', 'absolute'); ?></p>
    <?php endif; ?>
	
    <?php if ( comments_open() ): ?>
	<h2> Facebook </h2> 
	<!-- FB Comments -->
	<div class="fb-comments" data-href="<?php _e(get_home_url()); ?>" data-numposts="10" data-colorscheme="light" data-width="700"></div>
	<!-- /FB Comments -->

	<h2> Вконтакте </h2> 
	<!-- VK Comments -->	
	<div id="vk_comments"></div>
	<script type="text/javascript"> VK.Widgets.Comments("vk_comments", {limit: 10, width: "700", attach: "*"}); </script>
	<!-- /VK Comments -->
    <?php endif; ?>
    <?php comment_form(); ?>


</div>