<?php
// Evitar acceso directo
if ( ! defined('ABSPATH') ) exit;

/* -----------------------------------------------------------
 * CARGA DE ESTILOS Y SCRIPTS
 * ----------------------------------------------------------- */
function pool_de_elias_enqueue_assets() {
    // style.css (raíz) ya importa assets/css/style.css
    wp_enqueue_style( 'pool-de-elias-style', get_template_directory_uri() . '/style.css', [], null );
    wp_enqueue_script( 'pool-de-elias-js', get_template_directory_uri() . '/assets/js/main.js', ['jquery'], null, true );
}
add_action( 'wp_enqueue_scripts', 'pool_de_elias_enqueue_assets' );

/* -----------------------------------------------------------
 * SOPORTES DEL TEMA
 * ----------------------------------------------------------- */
add_theme_support('post-thumbnails');
add_theme_support('title-tag');

function pool_de_elias_custom_logo_support() {
    add_theme_support('custom-logo', [
        'height'      => 100,
        'width'       => 100,
        'flex-height' => true,
        'flex-width'  => true,
    ]);
}
add_action('after_setup_theme', 'pool_de_elias_custom_logo_support');

/* -----------------------------------------------------------
 * MENÚS Y WIDGETS
 * ----------------------------------------------------------- */
function pool_de_elias_register_menus() {
    register_nav_menus([
        'primary' => 'Menú Principal',
        'footer'  => 'Menú de Pie de Página',
    ]);
}
add_action('init', 'pool_de_elias_register_menus');

function pool_de_elias_register_widgets() {
    register_sidebar([
        'name'          => 'Sidebar Principal',
        'id'            => 'sidebar-main',
        'before_widget' => '<section class="widget">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ]);
}
add_action('widgets_init', 'pool_de_elias_register_widgets');

/* ===========================================================
 * PÁGINAS REQUERIDAS (SLUG => [TÍTULO, PLANTILLA, CONTENIDO])
 * =========================================================== */
function pool_de_elias_required_pages() {
    return [
        'register'     => ['Registro',      'page-register.php',     '[pool_de_elias_register_form]'],
        'login'        => ['Login',         'page-login.php',        '[pool_de_elias_login_form]'],
        'profile'      => ['Perfil',        'page-profile.php',      '[pool_de_elias_profile]'],
        'dashboard'    => ['Dashboard',     'page-dashboard.php',    '[pool_de_elias_dashboard]'],
        'competitions' => ['Competiciones', 'page-competitions.php', '[pool_de_elias_competitions]'],
    ];
}

/**
 * Crear o reparar páginas (no imprime nada).
 * Asigna plantilla vía _wp_page_template y añade el shortcode si falta.
 */
function pool_de_elias_create_or_fix_pages() {
    foreach ( pool_de_elias_required_pages() as $slug => $info ) {
        list($title, $template, $content) = $info;

        // ¿Existe por slug?
        $page = get_page_by_path($slug, OBJECT, 'page');

        if ( ! $page ) {
            $page_id = wp_insert_post([
                'post_type'    => 'page',
                'post_title'   => $title,
                'post_name'    => $slug,
                'post_status'  => 'publish',
                'post_content' => $content,
            ]);
            if ( ! is_wp_error($page_id) ) {
                update_post_meta($page_id, '_wp_page_template', $template);
            }
        } else {
            // Asegurar plantilla asignada
            update_post_meta($page->ID, '_wp_page_template', $template);
            // Asegurar que el shortcode esté presente sin machacar contenido existente
            if ( $content && strpos((string)$page->post_content, $content) === false ) {
                wp_update_post([
                    'ID'           => $page->ID,
                    'post_content' => trim($page->post_content . "\n\n" . $content),
                ]);
            }
        }
    }
}

/* ===========================================================
 * MENÚ PRINCIPAL (creación y asignación)
 * =========================================================== */
function pool_de_elias_create_menu() {
    if ( wp_get_nav_menu_object('Menú Principal') ) return;

    $menu_id = wp_create_nav_menu('Menú Principal');

    $items = [
        ['Inicio',        home_url('/')],
        ['Competiciones', home_url('/competitions')],
        ['Registro',      home_url('/register')],
        ['Login',         home_url('/login')],
        ['Dashboard',     home_url('/dashboard')],
    ];
    foreach ( $items as $it ) {
        wp_update_nav_menu_item( $menu_id, 0, [
            'menu-item-title'  => $it[0],
            'menu-item-url'    => $it[1],
            'menu-item-status' => 'publish',
        ]);
    }

    $locs = get_theme_mod('nav_menu_locations');
    if ( ! is_array($locs) ) $locs = [];
    $locs['primary'] = $menu_id;
    set_theme_mod('nav_menu_locations', $locs);
}

/* ===========================================================
 * SEMILLA DE COMPETICIONES POR NIVEL (CPT 'competition')
 * =========================================================== */
function pool_de_elias_seed_competitions() {
    // Asegúrate de registrar el CPT 'competition' en inc/custom-post-types.php
    $levels = ['Novel', 'Promesa', 'Experto', 'Máster'];

    foreach ( $levels as $level ) {
        // slug “nivel-competicion” sin acentos
        $slug = sanitize_title($level . ' Competicion');

        $existing = new WP_Query([
            'post_type'      => 'competition',
            'name'           => $slug,          // busca por post_name
            'posts_per_page' => 1,
            'post_status'    => 'any',
        ]);

        if ( ! $existing->have_posts() ) {
            wp_insert_post([
                'post_type'    => 'competition',
                'post_status'  => 'publish',
                'post_title'   => $level . ' Competición',
                'post_name'    => $slug,
                'post_content' => 'Competición para jugadores del nivel ' . $level . '.',
            ]);
        }
        wp_reset_postdata();
    }
}

/* ===========================================================
 * HOOKS DE ACTIVACIÓN Y “AUTOFIX” EN ADMIN
 * =========================================================== */
add_action('after_switch_theme', function () {
    pool_de_elias_create_or_fix_pages();
    pool_de_elias_create_menu();
    pool_de_elias_seed_competitions();
    flush_rewrite_rules(); // evitar 404 con nuevas rutas
});

// Reparación silenciosa en admin por si falló en activación (no imprime nada)
add_action('admin_init', 'pool_de_elias_create_or_fix_pages');
