<?php
/**
 * Registro y carga de assets.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function pool_de_elias_enqueue_assets() {
    wp_enqueue_style( 'dashicons' );
    wp_enqueue_style( 'pool-de-elias-main', get_stylesheet_uri(), [], PDE_THEME_VERSION );
    wp_enqueue_style( 'pool-de-elias-components', PDE_THEME_URL . '/assets/css/style.css', [ 'pool-de-elias-main' ], PDE_THEME_VERSION );

    wp_enqueue_script( 'pool-de-elias-js', PDE_THEME_URL . '/assets/js/main.js', [ 'jquery' ], PDE_THEME_VERSION, true );

    wp_localize_script(
        'pool-de-elias-js',
        'poolDeElias',
        [
            'restUrl'     => esc_url_raw( rest_url( 'pde/v1' ) ),
            'nonce'       => wp_create_nonce( 'wp_rest' ),
            'i18n'        => [
                'enrolled'     => __( 'Te has inscrito correctamente.', PDE_TEXTDOMAIN ),
                'confirmLeave' => __( '¿Seguro que deseas abandonar esta subcompetición?', PDE_TEXTDOMAIN ),
            ],
            'currentUser' => get_current_user_id(),
        ]
    );
}
add_action( 'wp_enqueue_scripts', 'pool_de_elias_enqueue_assets' );

function pool_de_elias_enqueue_editor_assets() {
    wp_enqueue_style( 'pool-de-elias-editor', PDE_THEME_URL . '/assets/css/editor.css', [], PDE_THEME_VERSION );
}
add_action( 'enqueue_block_editor_assets', 'pool_de_elias_enqueue_editor_assets' );
