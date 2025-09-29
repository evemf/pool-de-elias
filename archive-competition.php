<?php
get_header();
?>

<div class="competitions-list">
    <h2>Competiciones</h2>
    
    <?php
    // Consulta personalizada para obtener las competiciones
    $args = array(
        'post_type' => 'competition',
        'posts_per_page' => -1, // Traer todas las competiciones
    );
    $competitions = new WP_Query($args);

    if ($competitions->have_posts()) :
        while ($competitions->have_posts()) : $competitions->the_post();
            ?>
            <div class="competition-card">
                <h3><?php the_title(); ?></h3>
                <p><strong>Fecha de inicio:</strong> <?php echo get_the_date(); ?></p>
                <p><?php the_excerpt(); ?></p>
                
                <h4>Jugadores:</h4>
                <ul>
                    <?php
                    // Consulta para obtener los jugadores de esta competición (aquí asumo que los jugadores están relacionados como un custom field o post-meta)
                    $players = get_post_meta(get_the_ID(), 'players', true); // Esto puede cambiar según tu implementación
                    if ($players) {
                        foreach ($players as $player_id) {
                            $player = get_post($player_id);
                            echo '<li>' . get_the_title($player->ID) . '</li>';
                        }
                    } else {
                        echo '<li>No hay jugadores registrados aún.</li>';
                    }
                    ?>
                </ul>
            </div>
            <?php
        endwhile;
    else :
        echo '<p>No hay competiciones disponibles.</p>';
    endif;

    wp_reset_postdata();
    ?>
</div>

<?php
get_footer();
?>
