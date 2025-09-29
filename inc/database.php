<?php
/**
 * Tablas personalizadas y accesos de bajo nivel.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function pool_de_elias_get_players_table() {
    global $wpdb;
    return $wpdb->prefix . 'pool_players_competitions';
}

function pool_de_elias_get_results_table() {
    global $wpdb;
    return $wpdb->prefix . 'pool_results';
}

function pool_de_elias_install_database_tables() {
    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    $players_table = pool_de_elias_get_players_table();
    $results_table = pool_de_elias_get_results_table();

    $sql_players = "CREATE TABLE {$players_table} (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id BIGINT UNSIGNED NOT NULL,
        competition_id BIGINT UNSIGNED NOT NULL,
        subcompetition_id BIGINT UNSIGNED DEFAULT 0,
        status VARCHAR(20) NOT NULL DEFAULT 'pending',
        notes TEXT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY unique_enrollment (user_id, competition_id, subcompetition_id),
        KEY user_idx (user_id),
        KEY competition_idx (competition_id),
        KEY subcompetition_idx (subcompetition_id)
    ) {$charset_collate};";

    $sql_results = "CREATE TABLE {$results_table} (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        competition_id BIGINT UNSIGNED NOT NULL,
        subcompetition_id BIGINT UNSIGNED NOT NULL,
        user_id BIGINT UNSIGNED NOT NULL,
        position INT UNSIGNED DEFAULT 0,
        points DECIMAL(10,2) NOT NULL DEFAULT 0,
        score_json LONGTEXT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY unique_result (subcompetition_id, user_id),
        KEY comp_idx (competition_id),
        KEY sub_idx (subcompetition_id),
        KEY user_idx (user_id)
    ) {$charset_collate};";

    dbDelta( $sql_players );
    dbDelta( $sql_results );
}

/**
 * Inserta o actualiza una inscripción.
 */
function pool_de_elias_upsert_enrollment( $user_id, $competition_id, $subcompetition_id = 0, $status = 'pending', $notes = '' ) {
    global $wpdb;

    $table = pool_de_elias_get_players_table();

    $data = [
        'user_id'          => (int) $user_id,
        'competition_id'   => (int) $competition_id,
        'subcompetition_id'=> (int) $subcompetition_id,
        'status'           => sanitize_text_field( $status ),
        'notes'            => wp_kses_post( $notes ),
    ];

    $format = [ '%d', '%d', '%d', '%s', '%s' ];

    $existing_id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$table} WHERE user_id = %d AND competition_id = %d AND subcompetition_id = %d", $user_id, $competition_id, $subcompetition_id ) );

    if ( $existing_id ) {
        $wpdb->update( $table, $data, [ 'id' => $existing_id ], $format, [ '%d' ] );
        return (int) $existing_id;
    }

    $wpdb->insert( $table, $data, $format );

    return (int) $wpdb->insert_id;
}

/**
 * Obtiene las inscripciones de un usuario.
 */
function pool_de_elias_get_user_enrollments( $user_id ) {
    global $wpdb;

    $table = pool_de_elias_get_players_table();

    return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table} WHERE user_id = %d", $user_id ), ARRAY_A );
}

/**
 * Guarda un resultado.
 */
function pool_de_elias_store_result( $competition_id, $subcompetition_id, $user_id, $position, $points, $score = [] ) {
    global $wpdb;

    $table = pool_de_elias_get_results_table();

    $data = [
        'competition_id'   => (int) $competition_id,
        'subcompetition_id'=> (int) $subcompetition_id,
        'user_id'          => (int) $user_id,
        'position'         => (int) $position,
        'points'           => floatval( $points ),
        'score_json'       => wp_json_encode( $score ),
    ];

    $format = [ '%d', '%d', '%d', '%d', '%f', '%s' ];

    $existing = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$table} WHERE subcompetition_id = %d AND user_id = %d", $subcompetition_id, $user_id ) );

    if ( $existing ) {
        $wpdb->update( $table, $data, [ 'id' => $existing ], $format, [ '%d' ] );
        return (int) $existing;
    }

    $wpdb->insert( $table, $data, $format );

    return (int) $wpdb->insert_id;
}

function pool_de_elias_get_user_scores( $user_id ) {
    global $wpdb;

    $table = pool_de_elias_get_results_table();

    return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table} WHERE user_id = %d", $user_id ), ARRAY_A );
}

function pool_de_elias_get_competition_rankings( $competition_id ) {
    $transient_key = pool_de_elias_rankings_transient_key( $competition_id );

    $cached = get_transient( $transient_key );
    if ( false !== $cached ) {
        return $cached;
    }

    global $wpdb;
    $table = pool_de_elias_get_results_table();

    $results = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT user_id, SUM(points) as total_points FROM {$table} WHERE competition_id = %d GROUP BY user_id ORDER BY total_points DESC",
            $competition_id
        ),
        ARRAY_A
    );

    set_transient( $transient_key, $results, HOUR_IN_SECONDS );

    return $results;
}

function pool_de_elias_get_subcompetition_results( $subcompetition_id ) {
    global $wpdb;
    $table = pool_de_elias_get_results_table();

    return $wpdb->get_results(
        $wpdb->prepare(
            "SELECT user_id, position, points FROM {$table} WHERE subcompetition_id = %d ORDER BY position ASC",
            $subcompetition_id
        ),
        ARRAY_A
    );
}
