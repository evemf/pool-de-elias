<?php
/**
 * Plantilla para mostrar los archivos de contenido (como la lista de competiciones)
 */
get_header(); ?>

<div class="container">
    <header class="page-header">
        <h1 class="page-title"><?php _e( 'Competiciones', 'pool-de-elias' ); ?></h1>
    </header>

    <?php if ( have_posts() ) : ?>

        <div class="competitions-list">
            <?php
            // Iniciar el loop de WordPress
            while ( have_posts() ) : the_post(); ?>

                <article class="competition-item">
                    <header class="competition-header">
                        <h2 class="competition-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h2>
                        <p class="competition-date">
                            <?php echo get_the_date(); ?>
                        </p>
                    </header>

                    <div class="competition-excerpt">
                        <?php the_excerpt(); ?>
                    </div>

                    <footer class="competition-footer">
                        <a href="<?php the_permalink(); ?>" class="read-more"><?php _e( 'Ver más', 'pool-de-elias' ); ?></a>
                    </footer>
                </article>

            <?php endwhile; ?>

        </div>

        <!-- Navegación de Páginas -->
        <div class="pagination">
            <?php
            the_posts_pagination( array(
                'prev_text' => __( 'Anterior', 'pool-de-elias' ),
                'next_text' => __( 'Siguiente', 'pool-de-elias' ),
            ) );
            ?>
        </div>

    <?php else : ?>

        <p><?php _e( 'No se han encontrado competiciones.', 'pool-de-elias' ); ?></p>

    <?php endif; ?>
</div>

<?php get_footer(); ?>
