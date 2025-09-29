<?php
/**
 * Funciones para crear taxonomías personalizadas
 */

// Crear taxonomía para "Nivel" de las competiciones
function pool_de_elias_create_level_taxonomy() {
    $args = array(
        'hierarchical' => true,
        'labels' => array(
            'name' => 'Niveles',
            'singular_name' => 'Nivel',
            'search_items' => 'Buscar Niveles',
            'all_items' => 'Todos los Niveles',
            'parent_item' => 'Nivel Principal',
            'parent_item_colon' => 'Nivel Principal:',
            'edit_item' => 'Editar Nivel',
            'update_item' => 'Actualizar Nivel',
            'add_new_item' => 'Añadir Nuevo Nivel',
            'new_item_name' => 'Nuevo Nivel',
            'menu_name' => 'Nivel',
        ),
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array( 'slug' => 'nivel' ),
    );
    register_taxonomy( 'level', array( 'competition' ), $args );
}
add_action( 'init', 'pool_de_elias_create_level_taxonomy' );

// Crear taxonomía para "Edad"
function pool_de_elias_create_age_taxonomy() {
    $args = array(
        'hierarchical' => false,
        'labels' => array(
            'name' => 'Edades',
            'singular_name' => 'Edad',
            'search_items' => 'Buscar Edades',
            'all_items' => 'Todas las Edades',
            'parent_item' => 'Edad Principal',
            'parent_item_colon' => 'Edad Principal:',
            'edit_item' => 'Editar Edad',
            'update_item' => 'Actualizar Edad',
            'add_new_item' => 'Añadir Nueva Edad',
            'new_item_name' => 'Nueva Edad',
            'menu_name' => 'Edad',
        ),
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array( 'slug' => 'edad' ),
    );
    register_taxonomy( 'age', array( 'competition' ), $args );
}
add_action( 'init', 'pool_de_elias_create_age_taxonomy' );

// Crear taxonomía para "Equipos"
function pool_de_elias_create_team_taxonomy() {
    $args = array(
        'hierarchical' => true,
        'labels' => array(
            'name' => 'Equipos',
            'singular_name' => 'Equipo',
            'search_items' => 'Buscar Equipos',
            'all_items' => 'Todos los Equipos',
            'parent_item' => 'Equipo Principal',
            'parent_item_colon' => 'Equipo Principal:',
            'edit_item' => 'Editar Equipo',
            'update_item' => 'Actualizar Equipo',
            'add_new_item' => 'Añadir Nuevo Equipo',
            'new_item_name' => 'Nuevo Equipo',
            'menu_name' => 'Equipo',
        ),
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array( 'slug' => 'equipo' ),
    );
    register_taxonomy( 'team', array( 'competition' ), $args );
}
add_action( 'init', 'pool_de_elias_create_team_taxonomy' );
?>
