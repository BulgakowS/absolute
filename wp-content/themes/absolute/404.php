<?php
/**
 * @package Absolute
 */
?>
<?php global $absolute_options; ?>
<?php get_header(); ?>
<div class="full-container">
    <article id="post-0" <?php post_class(); ?>>
        <h2 class="entry-title">
            <?php $cur_lang = stella_get_current_lang(); ?>
            <?php if ($cur_lang == 'ru'): ?> Страница не найдена.<?php endif; ?>
            <?php if ($cur_lang == 'uk'): ?> Сторінку не знайдено. <?php endif; ?>
        </h2>
        <div class="entry-content">
            <h3><a href="<?php bloginfo('url') ?>" title="<?php bloginfo('name') ?> <?php bloginfo('description') ?>">
                   <?php if ($cur_lang == 'ru'): ?> На главную<?php endif; ?>
                    <?php if ($cur_lang == 'uk'): ?> На головну <?php endif; ?>
                </a></h3>
        </div>
    </article>
</div>
<?php get_footer(); ?>