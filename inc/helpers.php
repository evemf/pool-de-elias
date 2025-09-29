<?php
/**
 * Funciones auxiliares y bootstrap común.
 *
 * @package PoolDeElias
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Devuelve la configuración persistida con opción a valor por defecto.
 *
 * @param string $key     Clave de la opción.
 * @param mixed  $default Valor por defecto.
 *
 * @return mixed
 */
function pool_de_elias_get_setting( $key, $default = '' ) {
    $settings = get_option( 'pool_de_elias_settings', [] );
    if ( 'raw' === $key ) {
        return $settings;
    }
    return isset( $settings[ $key ] ) ? $settings[ $key ] : $default;
}

/**
 * Actualiza la configuración global del tema.
 *
 * @param string $key   Clave.
 * @param mixed  $value Valor.
 */
function pool_de_elias_update_setting( $key, $value ) {
    $settings         = get_option( 'pool_de_elias_settings', [] );
    $settings[ $key ] = $value;
    update_option( 'pool_de_elias_settings', $settings );
}

/**
 * Tabla de puntos por defecto.
 *
 * @return array
 */
function pool_de_elias_get_default_points_table() {
    $settings_table = pool_de_elias_get_setting( 'points_table', '' );
    if ( $settings_table ) {
        $decoded = json_decode( $settings_table, true );
        if ( is_array( $decoded ) && ! empty( $decoded ) ) {
            return array_map( 'intval', $decoded );
        }
    }

    $default = [
        1 => 25,
        2 => 18,
        3 => 15,
        4 => 12,
        5 => 10,
        6 => 8,
        7 => 6,
        8 => 4,
        9 => 2,
        10 => 1,
    ];

    /**
     * Filtro para sobreescribir los puntos por defecto.
     */
    return apply_filters( 'pool_de_elias_default_points_table', $default );
}

/**
 * Calcula la edad del usuario según fecha (Y-m-d).
 *
 * @param string $birthdate Fecha de nacimiento.
 * @param string $reference Referencia, por defecto hoy.
 *
 * @return int|null
 */
function pool_de_elias_calculate_age( $birthdate, $reference = 'now' ) {
    if ( empty( $birthdate ) ) {
        return null;
    }

    try {
        $birth = new DateTime( $birthdate );
        $ref   = new DateTime( $reference );
        return (int) $birth->diff( $ref )->y;
    } catch ( Exception $e ) {
        return null;
    }
}

/**
 * Lista de páginas obligatorias: slug => [título, plantilla, shortcode].
 *
 * @return array
 */
function pool_de_elias_required_pages() {
    return [
        'acceder'     => [ __( 'Acceder', PDE_TEXTDOMAIN ), 'page-login.php', '[pool_de_elias_login_form]' ],
        'registro'    => [ __( 'Registro', PDE_TEXTDOMAIN ), 'page-register.php', '[pool_de_elias_register_form]' ],
        'mi-perfil'   => [ __( 'Mi perfil', PDE_TEXTDOMAIN ), 'page-profile.php', '[pool_de_elias_profile]' ],
        'dashboard'   => [ __( 'Panel', PDE_TEXTDOMAIN ), 'page-dashboard.php', '[pool_de_elias_dashboard]' ],
        'competiciones' => [ __( 'Competiciones', PDE_TEXTDOMAIN ), 'page-competitions.php', '[pool_de_elias_competitions]' ],
    ];
}

/**
 * Crea o repara las páginas necesarias.
 */
function pool_de_elias_create_or_fix_pages() {
    foreach ( pool_de_elias_required_pages() as $slug => $data ) {
        list( $title, $template, $shortcode ) = $data;

        $page = get_page_by_path( $slug );

        if ( ! $page ) {
            $page_id = wp_insert_post(
                [
                    'post_type'    => 'page',
                    'post_status'  => 'publish',
                    'post_title'   => $title,
                    'post_name'    => $slug,
                    'post_content' => $shortcode,
                ]
            );

            if ( ! is_wp_error( $page_id ) ) {
                update_post_meta( $page_id, '_wp_page_template', $template );
            }

            continue;
        }

        // Forzar plantilla.
        update_post_meta( $page->ID, '_wp_page_template', $template );

        if ( $shortcode && strpos( (string) $page->post_content, $shortcode ) === false ) {
            wp_update_post(
                [
                    'ID'           => $page->ID,
                    'post_content' => trim( $page->post_content . "\n\n" . $shortcode ),
                ]
            );
        }
    }
}

function pool_de_elias_ensure_primary_menu() {
    $menu = wp_get_nav_menu_object( 'Menú Pool de Elias' );
    if ( ! $menu ) {
        $menu_id = wp_create_nav_menu( 'Menú Pool de Elias' );

        $items = [
            [ __( 'Inicio', PDE_TEXTDOMAIN ), home_url( '/' ) ],
            [ __( 'Competiciones', PDE_TEXTDOMAIN ), home_url( '/competiciones' ) ],
            [ __( 'Tienda', PDE_TEXTDOMAIN ), function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/tienda' ) ],
            [ __( 'Acceder', PDE_TEXTDOMAIN ), home_url( '/acceder' ) ],
            [ __( 'Mi panel', PDE_TEXTDOMAIN ), home_url( '/dashboard' ) ],
        ];

        foreach ( $items as $item ) {
            wp_update_nav_menu_item(
                $menu_id,
                0,
                [
                    'menu-item-title'  => $item[0],
                    'menu-item-url'    => esc_url( $item[1] ),
                    'menu-item-status' => 'publish',
                ]
            );
        }

        $menu = wp_get_nav_menu_object( $menu_id );
    }

    if ( $menu ) {
        $locations            = get_theme_mod( 'nav_menu_locations', [] );
        $locations['primary'] = $menu->term_id;
        set_theme_mod( 'nav_menu_locations', $locations );
    }
}

/**
 * Datos base para crear competiciones iniciales.
 *
 * @return array
 */
function pool_de_elias_default_competition_blueprints() {
    return [
        'novel'   => __( 'Noveles', PDE_TEXTDOMAIN ),
        'promesa' => __( 'Promesas', PDE_TEXTDOMAIN ),
        'experto' => __( 'Expertos', PDE_TEXTDOMAIN ),
        'master'  => __( 'Másters', PDE_TEXTDOMAIN ),
    ];
}

/**
 * Semilla de competiciones base.
 */
function pool_de_elias_seed_default_competitions() {
    $blueprints = pool_de_elias_default_competition_blueprints();

    foreach ( $blueprints as $slug => $label ) {
        $existing = get_page_by_path( $slug, OBJECT, 'competition' );

        if ( $existing ) {
            continue;
        }

        $post_id = wp_insert_post(
            [
                'post_type'    => 'competition',
                'post_status'  => 'publish',
                'post_title'   => sprintf( __( 'Competición %s', PDE_TEXTDOMAIN ), $label ),
                'post_name'    => $slug,
                'post_content' => __( 'Competición generada automáticamente para empezar a trabajar el calendario.', PDE_TEXTDOMAIN ),
                'meta_input'   => [
                    '_pde_competition_level' => $slug,
                    '_pde_status'            => 'open',
                ],
            ]
        );

        if ( is_wp_error( $post_id ) ) {
            continue;
        }

        wp_set_object_terms( $post_id, $slug, 'competition_level' );

        // Crear subcompeticiones placeholder.
        for ( $i = 1; $i <= 7; $i++ ) {
            $sub_id = wp_insert_post(
                [
                    'post_type'    => 'subcompetition',
                    'post_status'  => 'publish',
                    'post_title'   => sprintf( __( 'Prueba %1$s #%2$d', PDE_TEXTDOMAIN ), $label, $i ),
                    'post_name'    => $slug . '-sub-' . $i,
                    'post_parent'  => $post_id,
                    'meta_input'   => [
                        '_pde_sub_parent' => $post_id,
                    ],
                ]
            );

            if ( ! is_wp_error( $sub_id ) ) {
                update_post_meta( $sub_id, '_pde_points_table', [] );
            }
        }
    }
}

/**
 * Genera o actualiza los términos base.
 */
function pool_de_elias_seed_default_terms() {
    $levels = [
        'novel'   => __( 'Novel', PDE_TEXTDOMAIN ),
        'promesa' => __( 'Promesa', PDE_TEXTDOMAIN ),
        'experto' => __( 'Experto', PDE_TEXTDOMAIN ),
        'master'  => __( 'Máster', PDE_TEXTDOMAIN ),
    ];

    foreach ( $levels as $slug => $label ) {
        if ( ! term_exists( $slug, 'competition_level' ) ) {
            wp_insert_term( $label, 'competition_level', [ 'slug' => $slug ] );
        }
    }

    $types = [
        'femenina' => __( 'Femenina', PDE_TEXTDOMAIN ),
        'junior'   => __( 'Júnior (<23)', PDE_TEXTDOMAIN ),
        'senior'   => __( 'Senior (>50)', PDE_TEXTDOMAIN ),
        'mixta'    => __( 'Mixta', PDE_TEXTDOMAIN ),
    ];

    foreach ( $types as $slug => $label ) {
        if ( ! term_exists( $slug, 'competition_type' ) ) {
            wp_insert_term( $label, 'competition_type', [ 'slug' => $slug ] );
        }
    }
}

/**
 * Devuelve el nivel del usuario (user meta), o null.
 *
 * @param int $user_id ID usuario.
 *
 * @return string
 */
function pool_de_elias_get_user_level( $user_id ) {
    $level = get_user_meta( $user_id, 'pool_level', true );
    return $level ? $level : 'novel';
}

/**
 * Obtiene metadatos relevantes del perfil del usuario.
 *
 * @param int $user_id Usuario.
 *
 * @return array
 */
function pool_de_elias_get_user_profile_data( $user_id ) {
    $user = get_userdata( $user_id );

    return [
        'id'         => $user_id,
        'email'      => $user ? $user->user_email : '',
        'first_name' => get_user_meta( $user_id, 'first_name', true ),
        'last_name'  => get_user_meta( $user_id, 'last_name', true ),
        'sex'        => get_user_meta( $user_id, 'pool_sex', true ),
        'birthdate'  => get_user_meta( $user_id, 'pool_birthdate', true ),
        'level'      => pool_de_elias_get_user_level( $user_id ),
    ];
}

/**
 * Clave de transiente para rankings.
 */
function pool_de_elias_rankings_transient_key( $competition_id = 0 ) {
    return 'pde_rank_' . (int) $competition_id;
}

/**
 * Limpia la caché de rankings.
 */
function pool_de_elias_clear_rankings_cache( $competition_id = 0 ) {
    delete_transient( pool_de_elias_rankings_transient_key( $competition_id ) );
}

/**
 * Comando WP-CLI.
 */
function pool_de_elias_recalculate_rankings_command() {
    global $wpdb;

    $competition_ids = $wpdb->get_col( "SELECT DISTINCT competition_id FROM {$wpdb->prefix}pool_players_competitions" );

    if ( empty( $competition_ids ) ) {
        WP_CLI::success( __( 'No hay competiciones que recalcular.', PDE_TEXTDOMAIN ) );
        return;
    }

    foreach ( $competition_ids as $competition_id ) {
        pool_de_elias_clear_rankings_cache( $competition_id );
    }

    WP_CLI::success( __( 'Caché de rankings limpiada.', PDE_TEXTDOMAIN ) );
}

