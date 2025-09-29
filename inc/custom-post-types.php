<?php
// Crear tipo de contenido para Competiciones
function pool_de_elias_create_competition_post_type() {
    register_post_type( 'competition',
        array(
            'labels' => array(
                'name' => 'Competitions',
                'singular_name' => 'Competition',
                'add_new_item' => 'Add New Competition',
                'edit_item' => 'Edit Competition',
            ),
            'public' => true,
            'has_archive' => true,
            'supports' => array( 'title', 'editor', 'thumbnail' ),
            'rewrite' => array( 'slug' => 'competitions' ),
        )
    );
}
add_action( 'init', 'pool_de_elias_create_competition_post_type' );

// Crear tipo de contenido para Jugadores
function pool_de_elias_create_player_post_type() {
    register_post_type( 'player',
        array(
            'labels' => array(
                'name' => 'Players',
                'singular_name' => 'Player',
                'add_new_item' => 'Add New Player',
                'edit_item' => 'Edit Player',
            ),
            'public' => false, // Solo para administración
            'supports' => array( 'title', 'editor', 'custom-fields' ),
            'rewrite' => array( 'slug' => 'players' ),
        )
    );
}
add_action( 'init', 'pool_de_elias_create_player_post_type' );
?>
