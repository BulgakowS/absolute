<?php
/**
 * Template Name: Portfolio
 * @package Absolute
 */
?>
<?php global $absolute_options; ?>
<?php get_header(); ?>
<div class="full-container">
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <?php
        if(trim(get_the_post_thumbnail($post->ID)) != ''):
			$imgId = get_post_thumbnail_id($post->ID);
            $imgsrc = wp_get_attachment_image_src($imgId,'full',false);
            $img = $imgsrc[0];
    ?>
        <h1 class="entry-title title_header" style="background-image: url(<?php echo $img; ?>); background-repeat: no-repeat; background-position: left center;"><span class="title_header_text"><?php the_title(); ?></span></h1>
    <?php else: ?>
        <?php if (!is_front_page()): ?> 
            <h1 class="entry-title"><?php the_title(); ?></h1>
        <?php endif; ?>
    <?php endif; ?>
    <?php
        $queryarg = array('post_type'=>'post', 'posts_per_page'=>999999);
        query_posts($queryarg);
    ?>
    <?php if(have_posts()) : ?>
        <div class="entry-content <?php absolute_portfolio_class(); ?>">
            <?php $count = 1; ?>
            <?php while(have_posts()) : the_post(); ?>
                <?php if(trim(get_the_post_thumbnail($post->ID)) != ''): ?>
                    <dl class='gallery-item portfolio-item' id="portfolio-item-<?php the_ID(); ?>">
                        <dt class='gallery-icon portfolio-icon' id="portfolio-icon-<?php the_ID(); ?>">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('absolute-portfolio-image',array('title'	=> '')); ?>
                            </a>
                            <dd class="wp-caption-text gallery-caption" id="portfolio-title-<?php the_ID(); ?>"><?php the_title(); ?></dd>
                        </dt>
                    </dl>
                    <?php if($count % $absolute_options['portfolio_columns'] == 0) echo '<br style="clear: both" />'; $count++; ?>
                <?php endif; ?>
            <?php endwhile; ?>
            <?php wp_reset_query(); ?>
        </div>
    <?php else: ?>
        <div class="entry-content">
            <p><?php _e( 'Apologies, but no results were found for the requested archive. Perhaps searching will help find a related post.', 'absolute' ); ?></p>
            <p><?php get_search_form(); ?></p>
        </div>
    <?php endif; ?>
    </article>
</div>
<?php get_footer(); ?>