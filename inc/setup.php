<?php
/**
 * Configuración del tema: soportes, menús, etc.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function pool_de_elias_theme_setup() {
    load_theme_textdomain( PDE_TEXTDOMAIN, PDE_THEME_DIR . '/languages' );

    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'align-wide' );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'html5', [ 'search-form', 'gallery', 'caption', 'style', 'script' ] );
    add_theme_support( 'custom-logo', [
        'height'      => 120,
        'width'       => 120,
        'flex-width'  => true,
        'flex-height' => true,
    ] );
    add_theme_support( 'woocommerce' );

    register_nav_menus(
        [
            'primary' => __( 'Menú principal', PDE_TEXTDOMAIN ),
            'footer'  => __( 'Menú de pie', PDE_TEXTDOMAIN ),
        ]
    );

    add_theme_support( 'editor-styles' );
    add_editor_style( 'assets/css/editor.css' );
}
add_action( 'after_setup_theme', 'pool_de_elias_theme_setup' );

/**
 * Clases personalizadas al body.
 */
function pool_de_elias_body_class( $classes ) {
    if ( is_page_template( 'page-dashboard.php' ) ) {
        $classes[] = 'pde-dashboard';
    }

    if ( is_user_logged_in() ) {
        $classes[] = 'pde-logged-in';
    }

    return $classes;
}
add_filter( 'body_class', 'pool_de_elias_body_class' );

/**
 * Ajuste del excerpt para CPT.
 */
function pool_de_elias_excerpt_support_for_competitions() {
    add_post_type_support( 'competition', 'excerpt' );
    add_post_type_support( 'subcompetition', 'excerpt' );
}
add_action( 'init', 'pool_de_elias_excerpt_support_for_competitions' );
