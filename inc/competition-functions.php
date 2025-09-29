<?php
// Función para inscribir jugadores en una competición
function pool_de_elias_register_player_in_competition($player_id, $competition_id) {
    $competition = get_post($competition_id);
    
    if ($competition) {
        // Asignar competición al jugador
        add_post_meta($player_id, 'competitions', $competition_id);
        update_user_meta($player_id, 'points', 10); // Puntos por entrar
        
        // Asignar puntuación inicial al jugador
        update_post_meta($competition_id, 'player_' . $player_id, 10);
    }
}
?>
