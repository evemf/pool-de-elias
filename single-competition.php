<?php
/**
 * Plantilla para mostrar una competición individual
 */
get_header(); ?>

<div class="container">
    <?php
    // Iniciar el loop de WordPress para obtener los datos de la competición
    if ( have_posts() ) : 
        while ( have_posts() ) : 
            the_post(); ?>

            <article class="competition-details">
                <header class="competition-header">
                    <h1 class="competition-title"><?php the_title(); ?></h1>
                    <p class="competition-date">
                        <?php echo get_the_date(); ?> 
                        <!-- Fecha de la competición -->
                    </p>
                </header>

                <div class="competition-description">
                    <?php the_content(); ?>
                    <!-- Aquí se muestra el contenido completo de la competición -->
                </div>

                <div class="competition-meta">
                    <h3><?php _e( 'Detalles Adicionales', 'pool-de-elias' ); ?></h3>
                    <ul>
                        <li><strong><?php _e( 'Nivel:', 'pool-de-elias' ); ?></strong> <?php echo get_post_meta(get_the_ID(), 'level', true); ?></li>
                        <li><strong><?php _e( 'Categoría:', 'pool-de-elias' ); ?></strong> <?php echo get_the_term_list(get_the_ID(), 'competition_category', '', ', '); ?></li>
                        <li><strong><?php _e( 'Ubicación:', 'pool-de-elias' ); ?></strong> <?php echo get_post_meta(get_the_ID(), 'location', true); ?></li>
                        <li><strong><?php _e( 'Puntuación Inicial:', 'pool-de-elias' ); ?></strong> <?php echo get_post_meta(get_the_ID(), 'initial_points', true); ?></li>
                    </ul>
                </div>

                <div class="competition-players">
                    <h3><?php _e( 'Jugadores Inscritos', 'pool-de-elias' ); ?></h3>
                    <?php
                    // Obtener los jugadores inscritos en la competición
                    $players = get_post_meta(get_the_ID(), 'players', true);
                    if ($players) {
                        echo '<ul>';
                        foreach ($players as $player_id) {
                            echo '<li>' . get_the_title($player_id) . '</li>';  // Mostrar el nombre de cada jugador
                        }
                        echo '</ul>';
                    } else {
                        echo '<p>' . __( 'No hay jugadores inscritos aún.', 'pool-de-elias' ) . '</p>';
                    }
                    ?>
                </div>

                <footer class="competition-footer">
                    <a href="<?php echo get_post_type_archive_link( 'competition' ); ?>" class="back-to-competitions">
                        <?php _e( 'Volver a Competiciones', 'pool-de-elias' ); ?>
                    </a>
                </footer>
            </article>

        <?php endwhile;
    else :
        echo '<p>' . __( 'No se ha encontrado ninguna competición.', 'pool-de-elias' ) . '</p>';
    endif;
    ?>
</div>

<?php get_footer(); ?>
