<?php
get_header();
?>
<section class="pde-section">
    <h1 class="pde-section__title"><?php the_archive_title(); ?></h1>
    <?php if ( have_posts() ) : ?>
        <?php while ( have_posts() ) : the_post(); ?>
            <article <?php post_class( 'pde-card' ); ?>>
                <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                <p class="pde-meta-list"><span><?php echo esc_html( get_the_date() ); ?></span></p>
                <div class="pde-content"><?php the_excerpt(); ?></div>
                <a class="pde-button pde-button--secondary" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Leer más', PDE_TEXTDOMAIN ); ?></a>
            </article>
        <?php endwhile; ?>
        <?php the_posts_pagination(); ?>
    <?php else : ?>
        <div class="pde-card"><p><?php esc_html_e( 'No hay contenido disponible.', PDE_TEXTDOMAIN ); ?></p></div>
    <?php endif; ?>
</section>
<?php get_footer(); ?>
