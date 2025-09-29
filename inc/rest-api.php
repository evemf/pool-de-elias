<?php
/**
 * Endpoints REST personalizados.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'rest_api_init', 'pool_de_elias_register_rest_routes' );

function pool_de_elias_register_rest_routes() {
    register_rest_route(
        'pde/v1',
        '/register',
        [
            'methods'             => 'POST',
            'callback'            => 'pool_de_elias_rest_register_user',
            'permission_callback' => function() {
                return get_option( 'users_can_register', true );
            },
        ]
    );

    register_rest_route(
        'pde/v1',
        '/competitions',
        [
            'methods'             => 'GET',
            'callback'            => 'pool_de_elias_rest_get_competitions',
            'permission_callback' => '__return_true',
        ]
    );

    register_rest_route(
        'pde/v1',
        '/competitions/(?P<id>\d+)',
        [
            'methods'             => 'GET',
            'callback'            => 'pool_de_elias_rest_get_competition',
            'permission_callback' => '__return_true',
        ]
    );

    register_rest_route(
        'pde/v1',
        '/competitions/(?P<id>\d+)/subs',
        [
            'methods'             => 'GET',
            'callback'            => 'pool_de_elias_rest_get_subcompetitions',
            'permission_callback' => '__return_true',
        ]
    );

    register_rest_route(
        'pde/v1',
        '/subs/(?P<id>\d+)/enroll',
        [
            'methods'             => 'POST',
            'callback'            => 'pool_de_elias_rest_enroll',
            'permission_callback' => function() {
                return is_user_logged_in();
            },
        ]
    );

    register_rest_route(
        'pde/v1',
        '/subs/(?P<id>\d+)/results',
        [
            'methods'             => 'POST',
            'callback'            => 'pool_de_elias_rest_store_results',
            'permission_callback' => function() {
                return current_user_can( 'manage_pool_results' );
            },
        ]
    );

    register_rest_route(
        'pde/v1',
        '/users/(?P<id>\d+)/scores',
        [
            'methods'             => 'GET',
            'callback'            => 'pool_de_elias_rest_get_user_scores',
            'permission_callback' => function( $request ) {
                $user_id = (int) $request['id'];
                return get_current_user_id() === $user_id || current_user_can( 'manage_pool_results' );
            },
        ]
    );
}

function pool_de_elias_rest_register_user( WP_REST_Request $request ) {
    $email = sanitize_email( $request['email'] );
    $pass  = $request['password'];

    if ( empty( $email ) || empty( $pass ) ) {
        return new WP_Error( 'missing_fields', __( 'Email y contraseña son obligatorios.', PDE_TEXTDOMAIN ), [ 'status' => 400 ] );
    }

    if ( email_exists( $email ) ) {
        return new WP_Error( 'exists', __( 'El usuario ya existe.', PDE_TEXTDOMAIN ), [ 'status' => 409 ] );
    }

    $user_id = wp_insert_user(
        [
            'user_login' => $email,
            'user_email' => $email,
            'user_pass'  => $pass,
            'role'       => 'player',
        ]
    );

    if ( is_wp_error( $user_id ) ) {
        return $user_id;
    }

    update_user_meta( $user_id, 'pool_level', 'novel' );

    return rest_ensure_response(
        [
            'id'    => $user_id,
            'email' => $email,
        ]
    );
}

function pool_de_elias_rest_get_competitions() {
    $query = new WP_Query(
        [
            'post_type'      => 'competition',
            'posts_per_page' => -1,
        ]
    );

    $data = [];
    foreach ( $query->posts as $post ) {
        $data[] = pool_de_elias_prepare_competition_for_rest( $post );
    }

    return rest_ensure_response( $data );
}

function pool_de_elias_rest_get_competition( WP_REST_Request $request ) {
    $post = get_post( (int) $request['id'] );
    if ( ! $post || 'competition' !== $post->post_type ) {
        return new WP_Error( 'not_found', __( 'Competición no encontrada.', PDE_TEXTDOMAIN ), [ 'status' => 404 ] );
    }

    return rest_ensure_response( pool_de_elias_prepare_competition_for_rest( $post ) );
}

function pool_de_elias_rest_get_subcompetitions( WP_REST_Request $request ) {
    $competition_id = (int) $request['id'];

    $subs = get_posts(
        [
            'post_type'      => 'subcompetition',
            'posts_per_page' => -1,
            'meta_key'       => '_pde_sub_parent',
            'meta_value'     => $competition_id,
        ]
    );

    $data = [];
    foreach ( $subs as $sub ) {
        $data[] = pool_de_elias_prepare_subcompetition_for_rest( $sub );
    }

    return rest_ensure_response( $data );
}

function pool_de_elias_rest_enroll( WP_REST_Request $request ) {
    $subcompetition_id = (int) $request['id'];
    $subcompetition    = get_post( $subcompetition_id );

    if ( ! $subcompetition || 'subcompetition' !== $subcompetition->post_type ) {
        return new WP_Error( 'not_found', __( 'Subcompetición no encontrada.', PDE_TEXTDOMAIN ), [ 'status' => 404 ] );
    }

    $competition_id = (int) get_post_meta( $subcompetition_id, '_pde_sub_parent', true );

    $result = pool_de_elias_register_enrollment( get_current_user_id(), $competition_id, $subcompetition_id );

    if ( is_wp_error( $result ) ) {
        return $result;
    }

    return rest_ensure_response( [ 'enrollment_id' => $result ] );
}

function pool_de_elias_rest_store_results( WP_REST_Request $request ) {
    $subcompetition_id = (int) $request['id'];
    $competition_id    = (int) get_post_meta( $subcompetition_id, '_pde_sub_parent', true );
    $results           = (array) $request['results'];

    if ( empty( $results ) ) {
        return new WP_Error( 'missing', __( 'No se enviaron resultados.', PDE_TEXTDOMAIN ), [ 'status' => 400 ] );
    }

    pool_de_elias_calculate_points_for_positions( $competition_id, $subcompetition_id, $results );

    return rest_ensure_response( [ 'status' => 'ok' ] );
}

function pool_de_elias_rest_get_user_scores( WP_REST_Request $request ) {
    $user_id = (int) $request['id'];

    return rest_ensure_response( pool_de_elias_get_user_results_grouped( $user_id ) );
}

function pool_de_elias_prepare_competition_for_rest( WP_Post $post ) {
    return [
        'id'          => $post->ID,
        'title'       => get_the_title( $post ),
        'content'     => apply_filters( 'the_content', $post->post_content ),
        'event_date'  => get_post_meta( $post->ID, '_pde_event_date', true ),
        'status'      => get_post_meta( $post->ID, '_pde_status', true ),
        'level'       => get_post_meta( $post->ID, '_pde_level_target', true ),
        'permalink'   => get_permalink( $post ),
    ];
}

function pool_de_elias_prepare_subcompetition_for_rest( WP_Post $post ) {
    return [
        'id'        => $post->ID,
        'title'     => get_the_title( $post ),
        'permalink' => get_permalink( $post ),
        'format'    => get_post_meta( $post->ID, '_pde_sub_format', true ),
        'capacity'  => (int) get_post_meta( $post->ID, '_pde_sub_capacity', true ),
    ];
}

