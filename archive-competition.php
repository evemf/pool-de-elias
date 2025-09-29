<?php
get_header();
?>
<section class="pde-section">
    <h1 class="pde-section__title"><?php post_type_archive_title(); ?></h1>
    <p class="pde-section__subtitle"><?php esc_html_e( 'Consulta todas las competiciones disponibles y filtra por nivel o categoría desde el administrador.', PDE_TEXTDOMAIN ); ?></p>
    <div class="pde-competition-grid">
        <?php if ( have_posts() ) : ?>
            <?php while ( have_posts() ) : the_post(); ?>
                <?php pool_de_elias_render_competition_card( get_the_ID() ); ?>
            <?php endwhile; ?>
        <?php else : ?>
            <p><?php esc_html_e( 'No hay competiciones en este momento.', PDE_TEXTDOMAIN ); ?></p>
        <?php endif; ?>
    </div>
    <?php the_posts_pagination(); ?>
</section>
<?php get_footer(); ?>
