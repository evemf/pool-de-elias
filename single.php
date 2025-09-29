<?php
/**
 * Plantilla para mostrar el contenido de una entrada individual o competición.
 */
get_header(); ?>

<div class="container">
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

        <article class="post-detail">
            <header class="post-header">
                <h1 class="post-title"><?php the_title(); ?></h1>
                <p class="post-meta">
                    <?php echo get_the_date(); ?>
                </p>
            </header>

            <div class="post-content">
                <?php the_content(); ?>
            </div>

            <footer class="post-footer">
                <?php
                // Mostrar el botón de retroceso a la lista de competiciones
                echo '<a href="' . get_post_type_archive_link( 'competition' ) . '" class="back-to-competitions">' . __( 'Volver a Competiciones', 'pool-de-elias' ) . '</a>';
                ?>
            </footer>
        </article>

    <?php endwhile; endif; ?>
</div>

<?php get_footer(); ?>
