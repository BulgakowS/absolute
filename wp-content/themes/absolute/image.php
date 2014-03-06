<?php
/**
 * @package Absolute
 */
?>
<?php global $absolute_options; ?>
<?php get_header(); ?>
<?php if($absolute_options['sidebar_layout'] == 'one-left-sidebar'): ?>
<?php get_sidebar(1); ?>
<?php endif; ?>
<div class="container">
    <?php if(have_posts()) : ?>
        <?php while(have_posts()) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php printf(esc_attr__('Permalink to %s', 'absolute'), the_title_attribute('echo=0')); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
                <div class="entry-meta">
                    <?php absolute_posted_on(); ?>
                    <?php if(comments_open() && ! post_password_required()) : ?>
                    <?php comments_popup_link(__('Reply', 'absolute'), _x('1 Comment', 'comments number', 'absolute'), _x('% Comments', 'comments number', 'absolute'), 'entry-comments'); ?>
                    <?php edit_post_link(__('Edit', 'absolute')); ?>
                    <?php endif; ?>
                </div>
                <div class="entry-content">
                    <div class="entry-attachment">
                        <?php
                            $attachments = array_values(get_children(array('post_parent' => $post->post_parent, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID')));
                            foreach($attachments as $k => $attachment) {
                                if($attachment->ID == $post->ID) break;
                            }
                            $k++;
                            if(count($attachments) > 1) {
                                if(isset($attachments[$k ]))
                                    $next_attachment_url = get_attachment_link($attachments[$k]->ID);
                                else
                                    $next_attachment_url = get_attachment_link($attachments[0]->ID);
                            } else {
                                $next_attachment_url = wp_get_attachment_url();
                            }
                        ?>
                        <a href="<?php echo esc_url($next_attachment_url); ?>" title="<?php the_title_attribute(); ?>" rel="attachment"><?php $attachment_size = apply_filters('absolute_attachment_size', ABSOLUTE_CONTENT_WIDTH); echo wp_get_attachment_image($post->ID, array($attachment_size, 1024)); ?></a>
                        <?php if(!empty($post->post_excerpt)) : ?>
                        <div class="entry-caption">
                            <?php the_excerpt(); ?>
                        </div>
                        <?php endif; ?>
                        <div class="entry-description">
                            <?php the_content(); ?>
                            <?php wp_link_pages(array('before' => '<div class="page-link clearfix"><span class="pages-title">'.__('Pages:','absolute').'</span>', 'after' => '</div>', 'link_before' => '<span>', 'link_after' => '</span>')); ?>
                        </div>
                    </div>
                </div>
            </article>
            <div class="single-navigation">
                <span class="nav-previous"><?php previous_image_link(false, __('Previous' ,'absolute')); ?></span>
                <span class="nav-next"><?php next_image_link(false, __('Next' ,'absolute')); ?></span>
            </div>
            <?php comments_template(); ?>
        <?php endwhile; ?>
    <?php else: ?>
    <article id="post-0" <?php post_class(); ?>>
        <h2 class="entry-title"><?php _e('Nothing Found', 'absolute'); ?></h2>
        <div class="entry-content">
            <p><?php _e( 'Apologies, but no results were found for the requested archive. Perhaps searching will help find a related post.', 'absolute' ); ?></p>
            <p><?php get_search_form(); ?></p>
        </div>
    </article>
    <?php endif; ?>
</div>
<?php if($absolute_options['sidebar_layout'] == 'one-right-sidebar'): ?>
<?php get_sidebar(1); ?>
<?php endif; ?>
<?php get_footer(); ?>