<?php
/**
 * Funciones principales del tema Pool de Elias.
 *
 * @package PoolDeElias
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Constantes globales del tema.
define( 'PDE_THEME_VERSION', '1.0.0' );
define( 'PDE_THEME_DIR', get_template_directory() );
define( 'PDE_THEME_URL', get_template_directory_uri() );
define( 'PDE_TEXTDOMAIN', 'pool-de-elias' );

do_action( 'pool_de_elias_before_bootstrap' );

require_once PDE_THEME_DIR . '/inc/helpers.php';
require_once PDE_THEME_DIR . '/inc/setup.php';
require_once PDE_THEME_DIR . '/inc/assets.php';
require_once PDE_THEME_DIR . '/inc/roles.php';
require_once PDE_THEME_DIR . '/inc/database.php';
require_once PDE_THEME_DIR . '/inc/custom-taxonomies.php';
require_once PDE_THEME_DIR . '/inc/custom-post-types.php';
require_once PDE_THEME_DIR . '/inc/meta.php';
require_once PDE_THEME_DIR . '/inc/woocommerce.php';
require_once PDE_THEME_DIR . '/inc/hooks.php';
require_once PDE_THEME_DIR . '/inc/competition-functions.php';
require_once PDE_THEME_DIR . '/inc/user-functions.php';
require_once PDE_THEME_DIR . '/inc/shortcodes.php';
require_once PDE_THEME_DIR . '/inc/dashboard.php';
require_once PDE_THEME_DIR . '/inc/rest-api.php';
require_once PDE_THEME_DIR . '/inc/blocks/hero-block.php';

/**
 * Rutina de activación del tema.
 */
function pool_de_elias_activate_theme() {
    pool_de_elias_install_roles();
    pool_de_elias_install_database_tables();
    pool_de_elias_seed_default_terms();
    pool_de_elias_create_or_fix_pages();
    pool_de_elias_ensure_primary_menu();
    pool_de_elias_seed_default_competitions();
    flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'pool_de_elias_activate_theme' );

/**
 * Reparación silenciosa cada vez que se entra en el administrador.
 */
function pool_de_elias_admin_repair_tasks() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    pool_de_elias_create_or_fix_pages();
    pool_de_elias_seed_default_terms();
    pool_de_elias_ensure_primary_menu();
}
add_action( 'admin_init', 'pool_de_elias_admin_repair_tasks' );

/**
 * Comando WP-CLI para recalcular rankings.
 */
if ( defined( 'WP_CLI' ) && WP_CLI ) {
    WP_CLI::add_command( 'pool-de-elias recalc', 'pool_de_elias_recalculate_rankings_command' );
}

