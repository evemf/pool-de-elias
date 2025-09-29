<?php
/**
 * Lógica de negocio de competiciones.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function pool_de_elias_get_competition_points_table( $competition_id, $subcompetition_id = 0 ) {
    $table = get_post_meta( $competition_id, '_pde_points_table', true );
    if ( ! is_array( $table ) || empty( $table ) ) {
        $table = pool_de_elias_get_default_points_table();
    }

    if ( $subcompetition_id ) {
        $sub_table = get_post_meta( $subcompetition_id, '_pde_points_table', true );
        if ( is_array( $sub_table ) && ! empty( $sub_table ) ) {
            foreach ( $sub_table as $position => $points ) {
                $table[ $position ] = $points;
            }
        }
    }

    ksort( $table );

    return $table;
}

function pool_de_elias_is_user_eligible( $user_id, $competition_id ) {
    $level             = pool_de_elias_get_user_level( $user_id );
    $competition_level = get_post_meta( $competition_id, '_pde_level_target', true );
    $sex_restriction   = get_post_meta( $competition_id, '_pde_sex_restriction', true );
    $age_min           = (int) get_post_meta( $competition_id, '_pde_age_min', true );
    $age_max           = (int) get_post_meta( $competition_id, '_pde_age_max', true );
    $event_date        = get_post_meta( $competition_id, '_pde_event_date', true );

    $user_meta   = pool_de_elias_get_user_profile_data( $user_id );
    $user_level  = $user_meta['level'];
    $user_sex    = strtolower( (string) $user_meta['sex'] );
    $user_age    = pool_de_elias_calculate_age( $user_meta['birthdate'], $event_date ?: 'now' );

    $eligible = true;
    $messages = [];

    if ( $competition_level && 'abierta' !== $competition_level && $competition_level !== $user_level ) {
        $eligible   = false;
        $messages[] = __( 'Tu nivel no coincide con el requerido.', PDE_TEXTDOMAIN );
    }

    if ( 'm' === $sex_restriction && 'm' !== $user_sex ) {
        $eligible   = false;
        $messages[] = __( 'La competición es solo para hombres.', PDE_TEXTDOMAIN );
    }

    if ( 'f' === $sex_restriction && 'f' !== $user_sex ) {
        $eligible   = false;
        $messages[] = __( 'La competición es solo para mujeres.', PDE_TEXTDOMAIN );
    }

    if ( $age_min && $user_age && $user_age < $age_min ) {
        $eligible   = false;
        $messages[] = __( 'No alcanzas la edad mínima.', PDE_TEXTDOMAIN );
    }

    if ( $age_max && $user_age && $user_age > $age_max ) {
        $eligible   = false;
        $messages[] = __( 'Superas la edad máxima permitida.', PDE_TEXTDOMAIN );
    }

    return [
        'eligible' => $eligible,
        'messages' => $messages,
    ];
}

function pool_de_elias_competition_has_capacity( $competition_id, $subcompetition_id = 0 ) {
    $capacity = (int) ( $subcompetition_id ? get_post_meta( $subcompetition_id, '_pde_sub_capacity', true ) : get_post_meta( $competition_id, '_pde_capacity', true ) );
    if ( ! $capacity ) {
        return true;
    }

    global $wpdb;
    $table = pool_de_elias_get_players_table();

    $count = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT COUNT(*) FROM {$table} WHERE competition_id = %d AND subcompetition_id = %d AND status IN ('approved','pending')",
            $competition_id,
            $subcompetition_id
        )
    );

    return (int) $count < $capacity;
}

function pool_de_elias_register_enrollment( $user_id, $competition_id, $subcompetition_id = 0 ) {
    if ( ! current_user_can( 'pool_enroll_competition', $competition_id ) ) {
        return new WP_Error( 'forbidden', __( 'No tienes permiso para inscribirte.', PDE_TEXTDOMAIN ) );
    }

    $eligibility = pool_de_elias_is_user_eligible( $user_id, $competition_id );
    if ( ! $eligibility['eligible'] ) {
        return new WP_Error( 'not_eligible', implode( ' ', $eligibility['messages'] ) );
    }

    if ( ! pool_de_elias_competition_has_capacity( $competition_id, $subcompetition_id ) ) {
        return new WP_Error( 'no_capacity', __( 'La competición ha alcanzado su cupo.', PDE_TEXTDOMAIN ) );
    }

    $status = apply_filters( 'pool_de_elias_default_enrollment_status', 'pending', $user_id, $competition_id, $subcompetition_id );

    $enrollment_id = pool_de_elias_upsert_enrollment( $user_id, $competition_id, $subcompetition_id, $status );

    if ( ! $enrollment_id ) {
        return new WP_Error( 'db_error', __( 'No se pudo registrar la inscripción.', PDE_TEXTDOMAIN ) );
    }

    do_action( 'pool_de_elias_enrollment_created', $enrollment_id, $user_id, $competition_id, $subcompetition_id, $status );

    return $enrollment_id;
}

function pool_de_elias_calculate_points_for_positions( $competition_id, $subcompetition_id, $results ) {
    $points_table = pool_de_elias_get_competition_points_table( $competition_id, $subcompetition_id );

    foreach ( $results as $user_id => $position ) {
        $points = isset( $points_table[ $position ] ) ? $points_table[ $position ] : 0;
        pool_de_elias_store_result( $competition_id, $subcompetition_id, $user_id, $position, $points );
    }

    pool_de_elias_clear_rankings_cache( $competition_id );
}

function pool_de_elias_get_user_results_grouped( $user_id ) {
    $scores = pool_de_elias_get_user_scores( $user_id );
    $grouped = [];

    foreach ( $scores as $row ) {
        $competition_id = (int) $row['competition_id'];
        if ( ! isset( $grouped[ $competition_id ] ) ) {
            $grouped[ $competition_id ] = [
                'competition' => get_post( $competition_id ),
                'total'       => 0,
                'entries'     => [],
            ];
        }

        $grouped[ $competition_id ]['total']    += floatval( $row['points'] );
        $grouped[ $competition_id ]['entries'][] = $row;
    }

    return $grouped;
}

add_action( 'save_post_competition', 'pool_de_elias_clear_rankings_cache' );
add_action( 'save_post_subcompetition', 'pool_de_elias_clear_rankings_cache' );
