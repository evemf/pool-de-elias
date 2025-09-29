<?php
/**
 * Taxonomías personalizadas.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function pool_de_elias_register_taxonomies() {
    register_taxonomy(
        'competition_level',
        [ 'competition' ],
        [
            'labels'            => [
                'name'          => __( 'Niveles', PDE_TEXTDOMAIN ),
                'singular_name' => __( 'Nivel', PDE_TEXTDOMAIN ),
            ],
            'public'            => true,
            'hierarchical'      => false,
            'show_admin_column' => true,
            'show_in_rest'      => true,
            'rewrite'           => [ 'slug' => 'nivel' ],
        ]
    );

    register_taxonomy(
        'competition_type',
        [ 'competition' ],
        [
            'labels'            => [
                'name'          => __( 'Tipos', PDE_TEXTDOMAIN ),
                'singular_name' => __( 'Tipo', PDE_TEXTDOMAIN ),
            ],
            'public'            => true,
            'hierarchical'      => false,
            'show_admin_column' => true,
            'show_in_rest'      => true,
            'rewrite'           => [ 'slug' => 'tipo-competicion' ],
        ]
    );

    register_taxonomy(
        'competition_location',
        [ 'competition' ],
        [
            'labels'            => [
                'name'          => __( 'Ubicaciones', PDE_TEXTDOMAIN ),
                'singular_name' => __( 'Ubicación', PDE_TEXTDOMAIN ),
            ],
            'public'            => true,
            'hierarchical'      => true,
            'show_admin_column' => true,
            'show_in_rest'      => true,
            'rewrite'           => [ 'slug' => 'ubicacion-competicion' ],
        ]
    );
}
add_action( 'init', 'pool_de_elias_register_taxonomies', 9 );

