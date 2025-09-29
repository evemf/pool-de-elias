<?php
/* Template Name: Profile Player */
get_header();

if (!is_user_logged_in()) {
    echo 'Por favor, inicie sesión para ver su perfil.';
} else {
    $user_id = get_current_user_id();
    $competitions = get_user_meta($user_id, 'competitions', true);
    $points = get_user_meta($user_id, 'points', true);

    echo '<h1>Mi Perfil</h1>';
    echo '<p>Puntos Totales: ' . $points . '</p>';
    
    if ($competitions) {
        echo '<ul>';
        foreach ($competitions as $competition) {
            echo '<li>' . get_the_title($competition) . ' - Puntos: ' . get_post_meta($competition, 'player_' . $user_id, true) . '</li>';
        }
        echo '</ul>';
    } else {
        echo '<p>No has participado en competiciones aún.</p>';
    }
}

get_footer();
?>
