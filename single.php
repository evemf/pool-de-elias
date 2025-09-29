<?php
get_header();
?>
<section class="pde-section">
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
        <article <?php post_class( 'pde-card' ); ?>>
            <h1><?php the_title(); ?></h1>
            <p class="pde-meta-list"><span><?php echo esc_html( get_the_date() ); ?></span> · <span><?php the_author(); ?></span></p>
            <div class="pde-content"><?php the_content(); ?></div>
            <?php the_post_navigation(); ?>
        </article>
    <?php endwhile; endif; ?>
</section>
<?php get_footer(); ?>
