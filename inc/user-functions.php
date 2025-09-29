<?php
/**
 * Gestión de usuarios (registro, login, perfil).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function pool_de_elias_process_register() {
    if ( empty( $_POST['pde_register_nonce'] ) || ! wp_verify_nonce( $_POST['pde_register_nonce'], 'pde_register_user' ) ) {
        return;
    }

    $email       = isset( $_POST['pde_email'] ) ? sanitize_email( wp_unslash( $_POST['pde_email'] ) ) : '';
    $password    = isset( $_POST['pde_password'] ) ? $_POST['pde_password'] : '';
    $first_name  = isset( $_POST['pde_first_name'] ) ? sanitize_text_field( wp_unslash( $_POST['pde_first_name'] ) ) : '';
    $last_name   = isset( $_POST['pde_last_name'] ) ? sanitize_text_field( wp_unslash( $_POST['pde_last_name'] ) ) : '';
    $sex         = isset( $_POST['pde_sex'] ) ? sanitize_text_field( wp_unslash( $_POST['pde_sex'] ) ) : 'other';
    $birthdate   = isset( $_POST['pde_birthdate'] ) ? sanitize_text_field( wp_unslash( $_POST['pde_birthdate'] ) ) : '';
    $level       = isset( $_POST['pde_level'] ) ? sanitize_text_field( wp_unslash( $_POST['pde_level'] ) ) : 'novel';
    $privacy     = ! empty( $_POST['pde_privacy'] );

    if ( empty( $email ) || empty( $password ) || ! $privacy ) {
        wp_die( esc_html__( 'Completa todos los campos obligatorios y acepta la política de privacidad.', PDE_TEXTDOMAIN ) );
    }

    if ( email_exists( $email ) ) {
        wp_die( esc_html__( 'El correo ya está registrado.', PDE_TEXTDOMAIN ) );
    }

    $user_id = wp_insert_user(
        [
            'user_login' => $email,
            'user_email' => $email,
            'user_pass'  => $password,
            'role'       => 'player',
            'first_name' => $first_name,
            'last_name'  => $last_name,
        ]
    );

    if ( is_wp_error( $user_id ) ) {
        wp_die( esc_html( $user_id->get_error_message() ) );
    }

    $allowed_levels = [ 'novel', 'promesa', 'experto', 'master' ];
    $allowed_sex    = [ 'm', 'f', 'other' ];

    if ( ! in_array( $sex, $allowed_sex, true ) ) {
        $sex = 'other';
    }

    if ( ! in_array( $level, $allowed_levels, true ) ) {
        $level = 'novel';
    }

    update_user_meta( $user_id, 'pool_sex', $sex );
    update_user_meta( $user_id, 'pool_birthdate', $birthdate );
    update_user_meta( $user_id, 'pool_level', $level );

    wp_new_user_notification( $user_id, null, 'both' );

    wp_set_current_user( $user_id );
    wp_set_auth_cookie( $user_id );

    wp_safe_redirect( home_url( '/dashboard' ) );
    exit;
}
add_action( 'init', 'pool_de_elias_process_register' );

function pool_de_elias_process_login() {
    if ( empty( $_POST['pde_login_nonce'] ) || ! wp_verify_nonce( $_POST['pde_login_nonce'], 'pde_login_user' ) ) {
        return;
    }

    $username = isset( $_POST['pde_login'] ) ? sanitize_text_field( wp_unslash( $_POST['pde_login'] ) ) : '';
    $password = isset( $_POST['pde_login_pass'] ) ? $_POST['pde_login_pass'] : '';

    $user = wp_signon(
        [
            'user_login'    => $username,
            'user_password' => $password,
            'remember'      => true,
        ]
    );

    if ( is_wp_error( $user ) ) {
        wp_die( esc_html( $user->get_error_message() ) );
    }

    wp_safe_redirect( home_url( '/dashboard' ) );
    exit;
}
add_action( 'init', 'pool_de_elias_process_login' );

function pool_de_elias_process_profile_update() {
    if ( empty( $_POST['pde_profile_nonce'] ) || ! wp_verify_nonce( $_POST['pde_profile_nonce'], 'pde_save_profile' ) ) {
        return;
    }

    if ( ! is_user_logged_in() ) {
        return;
    }

    $user_id = get_current_user_id();

    $first_name = sanitize_text_field( wp_unslash( $_POST['pde_first_name'] ) );
    $last_name  = sanitize_text_field( wp_unslash( $_POST['pde_last_name'] ) );
    $sex        = sanitize_text_field( wp_unslash( $_POST['pde_sex'] ) );
    $birthdate  = sanitize_text_field( wp_unslash( $_POST['pde_birthdate'] ) );
    $level      = sanitize_text_field( wp_unslash( $_POST['pde_level'] ) );
    $allowed_levels = [ 'novel', 'promesa', 'experto', 'master' ];
    $allowed_sex    = [ 'm', 'f', 'other' ];

    if ( ! in_array( $sex, $allowed_sex, true ) ) {
        $sex = 'other';
    }

    if ( ! in_array( $level, $allowed_levels, true ) ) {
        $level = 'novel';
    }

    wp_update_user(
        [
            'ID'         => $user_id,
            'first_name' => $first_name,
            'last_name'  => $last_name,
        ]
    );

    update_user_meta( $user_id, 'pool_sex', $sex );
    update_user_meta( $user_id, 'pool_birthdate', $birthdate );
    update_user_meta( $user_id, 'pool_level', $level );

    wp_safe_redirect( add_query_arg( 'updated', 'true', home_url( '/mi-perfil' ) ) );
    exit;
}
add_action( 'init', 'pool_de_elias_process_profile_update' );

function pool_de_elias_process_enrollment_action() {
    if ( empty( $_POST['pde_enroll_nonce'] ) || ! wp_verify_nonce( $_POST['pde_enroll_nonce'], 'pde_enroll' ) ) {
        return;
    }

    if ( ! is_user_logged_in() ) {
        wp_die( esc_html__( 'Debes iniciar sesión para inscribirte.', PDE_TEXTDOMAIN ) );
    }

    $user_id           = get_current_user_id();
    $competition_id    = isset( $_POST['pde_competition_id'] ) ? intval( $_POST['pde_competition_id'] ) : 0;
    $subcompetition_id = isset( $_POST['pde_subcompetition_id'] ) ? intval( $_POST['pde_subcompetition_id'] ) : 0;

    if ( ! $competition_id ) {
        wp_die( esc_html__( 'Competición no válida.', PDE_TEXTDOMAIN ) );
    }

    $result = pool_de_elias_register_enrollment( $user_id, $competition_id, $subcompetition_id );

    if ( is_wp_error( $result ) ) {
        wp_die( esc_html( $result->get_error_message() ) );
    }

    do_action( 'pool_de_elias_enrollment_confirmed', $result );

    wp_safe_redirect( add_query_arg( 'enrolled', 'true', get_permalink( $competition_id ) ) );
    exit;
}
add_action( 'init', 'pool_de_elias_process_enrollment_action' );
