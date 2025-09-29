<?php
/**
 * Página del Dashboard (visible solo para usuarios logueados)
 */

// Asegurarse de que el usuario está logueado
if ( ! is_user_logged_in() ) {
    wp_redirect( home_url() );
    exit;
}

$current_user = wp_get_current_user();
?>

<?php get_header(); // Cargar la cabecera ?>

<div class="dashboard-container">

    <!-- Panel de Información del Jugador -->
    <section class="user-info">
        <div class="user-card">
            <h2>Bienvenido, <?php echo $current_user->user_login; ?>!</h2>
            <p><strong>Email:</strong> <?php echo $current_user->user_email; ?></p>
            <p><strong>Nivel:</strong> <?php echo get_user_meta($current_user->ID, 'level', true); ?></p>
        </div>
    </section>

    <!-- Competiciones del Jugador -->
    <section class="competitions">
        <h2>Competiciones Disponibles</h2>
        <p>A continuación, podrás inscribirte en las competiciones disponibles según tu nivel.</p>

        <div class="competitions-list">
            <?php
            // Obtener el nivel del jugador
            $level = get_user_meta($current_user->ID, 'level', true);

            // Obtener las competiciones del jugador según su nivel
            $competitions = get_posts(array(
                'post_type' => 'competition',
                'posts_per_page' => -1,
                'title' => $level . ' Competición' // Competiciones del mismo nivel
            ));

            // Si el jugador tiene competiciones
            if ($competitions) {
                foreach ($competitions as $competition) {
                    ?>
                    <div class="competition-card">
                        <h3><?php echo $competition->post_title; ?></h3>
                        <p><strong>Fecha:</strong> <?php echo get_the_date('', $competition); ?></p>
                        <p><?php echo wp_trim_words($competition->post_content, 20); ?></p>
                        <a href="<?php echo get_permalink($competition->ID); ?>" class="btn-competition">Ver más</a>
                    </div>
                    <?php
                }
            } else {
                echo '<p>No hay competiciones disponibles para tu nivel.</p>';
            }
            ?>
        </div>
    </section>

</div>

<?php get_footer(); // Cargar el pie de página ?>
