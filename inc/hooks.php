<?php
/**
 * Hooks adicionales: ajustes y correos.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'admin_menu', 'pool_de_elias_register_settings_page' );
add_action( 'admin_init', 'pool_de_elias_register_settings' );

function pool_de_elias_register_settings_page() {
    add_menu_page(
        __( 'Pool de Elias', PDE_TEXTDOMAIN ),
        __( 'Pool de Elias', PDE_TEXTDOMAIN ),
        'pool_manage_settings',
        'pool-de-elias-settings',
        'pool_de_elias_render_settings_page',
        'dashicons-groups',
        59
    );

    add_submenu_page(
        'pool-de-elias-settings',
        __( 'Ajustes', PDE_TEXTDOMAIN ),
        __( 'Ajustes', PDE_TEXTDOMAIN ),
        'pool_manage_settings',
        'pool-de-elias-settings',
        'pool_de_elias_render_settings_page'
    );
}

function pool_de_elias_register_settings() {
    register_setting( 'pool_de_elias', 'pool_de_elias_settings', 'pool_de_elias_sanitize_settings' );

    add_settings_section( 'pool_de_elias_points', __( 'Puntuaciones por defecto', PDE_TEXTDOMAIN ), '__return_false', 'pool-de-elias-settings' );
    add_settings_section( 'pool_de_elias_rules', __( 'Reglas de elegibilidad', PDE_TEXTDOMAIN ), '__return_false', 'pool-de-elias-settings' );
    add_settings_section( 'pool_de_elias_misc', __( 'Preferencias generales', PDE_TEXTDOMAIN ), '__return_false', 'pool-de-elias-settings' );

    add_settings_field( 'points_table', __( 'Tabla puntos (JSON)', PDE_TEXTDOMAIN ), 'pool_de_elias_field_points_table', 'pool-de-elias-settings', 'pool_de_elias_points' );
    add_settings_field( 'age_ranges', __( 'Rangos de edad', PDE_TEXTDOMAIN ), 'pool_de_elias_field_age_ranges', 'pool-de-elias-settings', 'pool_de_elias_rules' );
    add_settings_field( 'auto_approve', __( 'Auto aprobar inscripciones', PDE_TEXTDOMAIN ), 'pool_de_elias_field_auto_approve', 'pool-de-elias-settings', 'pool_de_elias_rules' );
    add_settings_field( 'legal_text', __( 'Texto legal', PDE_TEXTDOMAIN ), 'pool_de_elias_field_legal_text', 'pool-de-elias-settings', 'pool_de_elias_misc' );
}

function pool_de_elias_sanitize_settings( $input ) {
    $output = [];

    $output['points_table'] = isset( $input['points_table'] ) ? sanitize_textarea_field( $input['points_table'] ) : '';
    $output['age_ranges']   = isset( $input['age_ranges'] ) ? sanitize_text_field( $input['age_ranges'] ) : '';
    $output['auto_approve'] = ! empty( $input['auto_approve'] ) ? 1 : 0;
    $output['legal_text']   = isset( $input['legal_text'] ) ? wp_kses_post( $input['legal_text'] ) : '';

    return $output;
}

function pool_de_elias_field_points_table() {
    $value = pool_de_elias_get_setting( 'points_table', wp_json_encode( pool_de_elias_get_default_points_table() ) );
    printf( '<textarea name="pool_de_elias_settings[points_table]" rows="5" class="large-text code">%s</textarea>', esc_textarea( $value ) );
    echo '<p class="description">' . esc_html__( 'Proporciona un mapa posición => puntos en formato JSON.', PDE_TEXTDOMAIN ) . '</p>';
}

function pool_de_elias_field_age_ranges() {
    $value = pool_de_elias_get_setting( 'age_ranges', '' );
    printf( '<input type="text" name="pool_de_elias_settings[age_ranges]" value="%s" class="regular-text" />', esc_attr( $value ) );
    echo '<p class="description">' . esc_html__( 'Define rangos personalizados, ejemplo: junior:12-23,seniors:50-99', PDE_TEXTDOMAIN ) . '</p>';
}

function pool_de_elias_field_auto_approve() {
    $value = pool_de_elias_get_setting( 'auto_approve', 0 );
    printf( '<label><input type="checkbox" name="pool_de_elias_settings[auto_approve]" value="1" %s /> %s</label>', checked( 1, $value, false ), esc_html__( 'Aprobar inscripciones automáticamente', PDE_TEXTDOMAIN ) );
}

function pool_de_elias_field_legal_text() {
    $value = pool_de_elias_get_setting( 'legal_text', '' );
    printf( '<textarea name="pool_de_elias_settings[legal_text]" rows="5" class="large-text">%s</textarea>', esc_textarea( $value ) );
}

function pool_de_elias_render_settings_page() {
    if ( ! current_user_can( 'pool_manage_settings' ) ) {
        wp_die( esc_html__( 'No tienes permisos suficientes.', PDE_TEXTDOMAIN ) );
    }
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Ajustes Pool de Elias', PDE_TEXTDOMAIN ); ?></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields( 'pool_de_elias' );
            do_settings_sections( 'pool-de-elias-settings' );
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

/**
 * Modifica el estado por defecto de inscripciones según ajustes.
 */
add_filter( 'pool_de_elias_default_enrollment_status', function( $status ) {
    $auto = pool_de_elias_get_setting( 'auto_approve', 0 );
    return $auto ? 'approved' : $status;
} );

add_action( 'pool_de_elias_enrollment_created', 'pool_de_elias_notify_enrollment', 10, 5 );

function pool_de_elias_notify_enrollment( $enrollment_id, $user_id, $competition_id, $subcompetition_id, $status ) {
    $user        = get_userdata( $user_id );
    $competition = get_the_title( $competition_id );
    $sub         = $subcompetition_id ? get_the_title( $subcompetition_id ) : '';

    if ( ! $user ) {
        return;
    }

    $subject = sprintf( __( 'Inscripción en %s', PDE_TEXTDOMAIN ), $competition );
    $message = sprintf(
        "%s\n\n%s: %s\n%s: %s\n%s: %s",
        __( 'Gracias por inscribirte. A continuación se detalla el estado de tu solicitud.', PDE_TEXTDOMAIN ),
        __( 'Competición', PDE_TEXTDOMAIN ),
        $competition,
        __( 'Subcompetición', PDE_TEXTDOMAIN ),
        $sub ?: __( 'General', PDE_TEXTDOMAIN ),
        __( 'Estado', PDE_TEXTDOMAIN ),
        ucfirst( $status )
    );

    wp_mail( $user->user_email, $subject, $message );
}

