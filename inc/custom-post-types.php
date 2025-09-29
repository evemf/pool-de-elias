<?php
/**
 * Tipos de contenido personalizados.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function pool_de_elias_register_post_types() {

    register_post_type(
        'competition',
        [
            'labels' => [
                'name'               => __( 'Competiciones', PDE_TEXTDOMAIN ),
                'singular_name'      => __( 'Competición', PDE_TEXTDOMAIN ),
                'add_new'            => __( 'Añadir nueva', PDE_TEXTDOMAIN ),
                'add_new_item'       => __( 'Añadir nueva competición', PDE_TEXTDOMAIN ),
                'edit_item'          => __( 'Editar competición', PDE_TEXTDOMAIN ),
                'new_item'           => __( 'Nueva competición', PDE_TEXTDOMAIN ),
                'view_item'          => __( 'Ver competición', PDE_TEXTDOMAIN ),
                'search_items'       => __( 'Buscar competiciones', PDE_TEXTDOMAIN ),
                'not_found'          => __( 'No se encontraron competiciones', PDE_TEXTDOMAIN ),
                'not_found_in_trash' => __( 'No hay competiciones en la papelera', PDE_TEXTDOMAIN ),
                'all_items'          => __( 'Todas las competiciones', PDE_TEXTDOMAIN ),
            ],
            'public'             => true,
            'has_archive'        => 'competiciones',
            'menu_icon'          => 'dashicons-awards',
            'supports'           => [ 'title', 'editor', 'thumbnail', 'excerpt' ],
            'rewrite'            => [ 'slug' => 'competicion', 'with_front' => false ],
            'show_in_rest'       => true,
            'capability_type'    => [ 'competition', 'competitions' ],
            'map_meta_cap'       => true,
        ]
    );

    register_post_type(
        'subcompetition',
        [
            'labels' => [
                'name'          => __( 'Subcompeticiones', PDE_TEXTDOMAIN ),
                'singular_name' => __( 'Subcompetición', PDE_TEXTDOMAIN ),
                'add_new_item'  => __( 'Añadir subcompetición', PDE_TEXTDOMAIN ),
                'edit_item'     => __( 'Editar subcompetición', PDE_TEXTDOMAIN ),
            ],
            'public'             => true,
            'has_archive'        => false,
            'menu_icon'          => 'dashicons-chart-line',
            'supports'           => [ 'title', 'editor', 'excerpt' ],
            'rewrite'            => false,
            'show_in_rest'       => true,
            'capability_type'    => [ 'subcompetition', 'subcompetitions' ],
            'map_meta_cap'       => true,
        ]
    );
}
add_action( 'init', 'pool_de_elias_register_post_types', 10 );

/**
 * Reescritura personalizada para subcompeticiones.
 */
function pool_de_elias_subcompetition_link( $permalink, $post ) {
    if ( 'subcompetition' !== $post->post_type ) {
        return $permalink;
    }

    $parent_id = (int) get_post_meta( $post->ID, '_pde_sub_parent', true );
    if ( ! $parent_id ) {
        $parent_id = (int) $post->post_parent;
    }

    if ( ! $parent_id ) {
        return home_url( user_trailingslashit( 'competicion/' . $post->post_name ) );
    }

    $parent = get_post( $parent_id );
    if ( ! $parent ) {
        return $permalink;
    }

    return home_url( user_trailingslashit( 'competicion/' . $parent->post_name . '/' . $post->post_name ) );
}
add_filter( 'post_type_link', 'pool_de_elias_subcompetition_link', 10, 2 );

/**
 * Asegura reescrituras adicionales para subcompeticiones.
 */
function pool_de_elias_subcompetition_rewrite_rules() {
    add_rewrite_rule(
        '^competicion/([^/]+)/([^/]+)/?$',
        'index.php?post_type=subcompetition&name=$matches[2]',
        'top'
    );
}
add_action( 'init', 'pool_de_elias_subcompetition_rewrite_rules', 5 );
