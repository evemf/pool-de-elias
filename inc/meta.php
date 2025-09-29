<?php
/**
 * Metaboxes y metadatos registrados para CPTs.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function pool_de_elias_register_meta() {
    $competition_meta = [
        '_pde_event_date'        => 'string',
        '_pde_status'            => 'string',
        '_pde_level_target'      => 'string',
        '_pde_sex_restriction'   => 'string',
        '_pde_age_min'           => 'integer',
        '_pde_age_max'           => 'integer',
        '_pde_capacity'          => 'integer',
        '_pde_location'          => 'string',
        '_pde_organizer'         => 'string',
        '_pde_points_table'      => 'array',
    ];

    foreach ( $competition_meta as $key => $type ) {
        register_post_meta(
            'competition',
            $key,
            [
                'show_in_rest'  => true,
                'single'        => true,
                'type'          => $type,
                'auth_callback' => function() {
                    return current_user_can( 'edit_competitions' );
                },
            ]
        );
    }

    $sub_meta = [
        '_pde_sub_parent'     => 'integer',
        '_pde_sub_format'     => 'string',
        '_pde_sub_capacity'   => 'integer',
        '_pde_sub_schedule'   => 'string',
        '_pde_points_table'   => 'array',
    ];

    foreach ( $sub_meta as $key => $type ) {
        register_post_meta(
            'subcompetition',
            $key,
            [
                'show_in_rest'  => true,
                'single'        => true,
                'type'          => $type,
                'auth_callback' => function() {
                    return current_user_can( 'edit_subcompetitions' );
                },
            ]
        );
    }
}
add_action( 'init', 'pool_de_elias_register_meta' );

function pool_de_elias_add_competition_metaboxes() {
    add_meta_box( 'pool_de_elias_competition_details', __( 'Detalles de la competición', PDE_TEXTDOMAIN ), 'pool_de_elias_render_competition_meta_box', 'competition', 'normal', 'default' );
    add_meta_box( 'pool_de_elias_subcompetition_details', __( 'Detalles de la subcompetición', PDE_TEXTDOMAIN ), 'pool_de_elias_render_subcompetition_meta_box', 'subcompetition', 'normal', 'default' );
}
add_action( 'add_meta_boxes', 'pool_de_elias_add_competition_metaboxes' );

function pool_de_elias_render_competition_meta_box( $post ) {
    wp_nonce_field( 'pool_de_elias_save_competition_meta', 'pool_de_elias_competition_nonce' );

    $event_date      = get_post_meta( $post->ID, '_pde_event_date', true );
    $status          = get_post_meta( $post->ID, '_pde_status', true );
    $level_target    = get_post_meta( $post->ID, '_pde_level_target', true );
    $sex_restriction = get_post_meta( $post->ID, '_pde_sex_restriction', true );
    $age_min         = get_post_meta( $post->ID, '_pde_age_min', true );
    $age_max         = get_post_meta( $post->ID, '_pde_age_max', true );
    $capacity        = get_post_meta( $post->ID, '_pde_capacity', true );
    $location        = get_post_meta( $post->ID, '_pde_location', true );
    $organizer       = get_post_meta( $post->ID, '_pde_organizer', true );
    $points_table    = (array) get_post_meta( $post->ID, '_pde_points_table', true );

    if ( empty( $points_table ) ) {
        $points_table = pool_de_elias_get_default_points_table();
    }

    ?>
    <p>
        <label for="pde_event_date"><strong><?php esc_html_e( 'Fecha de celebración', PDE_TEXTDOMAIN ); ?></strong></label>
        <input type="datetime-local" id="pde_event_date" name="pde_event_date" value="<?php echo esc_attr( $event_date ); ?>" class="widefat" />
    </p>
    <p>
        <label for="pde_status"><strong><?php esc_html_e( 'Estado', PDE_TEXTDOMAIN ); ?></strong></label>
        <select id="pde_status" name="pde_status" class="widefat">
            <?php
            $statuses = [
                'draft'   => __( 'Borrador', PDE_TEXTDOMAIN ),
                'open'    => __( 'Abierta', PDE_TEXTDOMAIN ),
                'closed'  => __( 'Cerrada', PDE_TEXTDOMAIN ),
                'finished'=> __( 'Finalizada', PDE_TEXTDOMAIN ),
            ];
            foreach ( $statuses as $key => $label ) {
                printf( '<option value="%1$s" %2$s>%3$s</option>', esc_attr( $key ), selected( $status, $key, false ), esc_html( $label ) );
            }
            ?>
        </select>
    </p>
    <p>
        <label for="pde_level_target"><strong><?php esc_html_e( 'Nivel objetivo', PDE_TEXTDOMAIN ); ?></strong></label>
        <select id="pde_level_target" name="pde_level_target" class="widefat">
            <?php
            $levels = pool_de_elias_default_competition_blueprints();
            $levels = array_merge( [ 'abierta' => __( 'Abierta', PDE_TEXTDOMAIN ) ], $levels );
            foreach ( $levels as $slug => $label ) {
                printf( '<option value="%1$s" %2$s>%3$s</option>', esc_attr( $slug ), selected( $level_target, $slug, false ), esc_html( $label ) );
            }
            ?>
        </select>
    </p>
    <p>
        <label for="pde_sex_restriction"><strong><?php esc_html_e( 'Restricción de sexo', PDE_TEXTDOMAIN ); ?></strong></label>
        <select id="pde_sex_restriction" name="pde_sex_restriction" class="widefat">
            <?php
            $sex_options = [
                'all' => __( 'Libre', PDE_TEXTDOMAIN ),
                'm'   => __( 'Solo hombres', PDE_TEXTDOMAIN ),
                'f'   => __( 'Solo mujeres', PDE_TEXTDOMAIN ),
            ];
            foreach ( $sex_options as $key => $label ) {
                printf( '<option value="%1$s" %2$s>%3$s</option>', esc_attr( $key ), selected( $sex_restriction, $key, false ), esc_html( $label ) );
            }
            ?>
        </select>
    </p>
    <div class="pde-meta-grid">
        <p>
            <label for="pde_age_min"><?php esc_html_e( 'Edad mínima', PDE_TEXTDOMAIN ); ?></label>
            <input type="number" id="pde_age_min" name="pde_age_min" value="<?php echo esc_attr( $age_min ); ?>" min="0" />
        </p>
        <p>
            <label for="pde_age_max"><?php esc_html_e( 'Edad máxima', PDE_TEXTDOMAIN ); ?></label>
            <input type="number" id="pde_age_max" name="pde_age_max" value="<?php echo esc_attr( $age_max ); ?>" min="0" />
        </p>
        <p>
            <label for="pde_capacity"><?php esc_html_e( 'Cupo de participantes', PDE_TEXTDOMAIN ); ?></label>
            <input type="number" id="pde_capacity" name="pde_capacity" value="<?php echo esc_attr( $capacity ); ?>" min="0" />
        </p>
    </div>
    <p>
        <label for="pde_location"><?php esc_html_e( 'Ubicación', PDE_TEXTDOMAIN ); ?></label>
        <input type="text" id="pde_location" name="pde_location" value="<?php echo esc_attr( $location ); ?>" class="widefat" />
    </p>
    <p>
        <label for="pde_organizer"><?php esc_html_e( 'Organizador', PDE_TEXTDOMAIN ); ?></label>
        <input type="text" id="pde_organizer" name="pde_organizer" value="<?php echo esc_attr( $organizer ); ?>" class="widefat" />
    </p>
    <fieldset>
        <legend><?php esc_html_e( 'Tabla de puntos por posición', PDE_TEXTDOMAIN ); ?></legend>
        <?php foreach ( $points_table as $position => $points ) : ?>
            <p>
                <label>
                    <?php printf( esc_html__( 'Posición %d', PDE_TEXTDOMAIN ), (int) $position ); ?>
                    <input type="number" step="1" name="pde_points_table[<?php echo esc_attr( $position ); ?>]" value="<?php echo esc_attr( $points ); ?>" />
                </label>
            </p>
        <?php endforeach; ?>
    </fieldset>
    <?php
}

function pool_de_elias_render_subcompetition_meta_box( $post ) {
    wp_nonce_field( 'pool_de_elias_save_subcompetition_meta', 'pool_de_elias_subcompetition_nonce' );

    $parent_id   = get_post_meta( $post->ID, '_pde_sub_parent', true );
    $format      = get_post_meta( $post->ID, '_pde_sub_format', true );
    $capacity    = get_post_meta( $post->ID, '_pde_sub_capacity', true );
    $schedule    = get_post_meta( $post->ID, '_pde_sub_schedule', true );
    $points      = (array) get_post_meta( $post->ID, '_pde_points_table', true );

    $competitions = get_posts(
        [
            'post_type'      => 'competition',
            'posts_per_page' => -1,
            'post_status'    => [ 'publish', 'draft' ],
        ]
    );

    if ( empty( $points ) ) {
        $points = pool_de_elias_get_default_points_table();
    }
    ?>
    <p>
        <label for="pde_sub_parent"><?php esc_html_e( 'Competición padre', PDE_TEXTDOMAIN ); ?></label>
        <select id="pde_sub_parent" name="pde_sub_parent" class="widefat">
            <?php foreach ( $competitions as $competition ) : ?>
                <option value="<?php echo esc_attr( $competition->ID ); ?>" <?php selected( $parent_id, $competition->ID ); ?>><?php echo esc_html( $competition->post_title ); ?></option>
            <?php endforeach; ?>
        </select>
    </p>
    <p>
        <label for="pde_sub_format"><?php esc_html_e( 'Formato', PDE_TEXTDOMAIN ); ?></label>
        <select id="pde_sub_format" name="pde_sub_format" class="widefat">
            <?php
            $formats = [
                'league'  => __( 'Liga', PDE_TEXTDOMAIN ),
                'knockout'=> __( 'Eliminación directa', PDE_TEXTDOMAIN ),
                'groups'  => __( 'Grupos + KO', PDE_TEXTDOMAIN ),
            ];
            foreach ( $formats as $key => $label ) {
                printf( '<option value="%1$s" %2$s>%3$s</option>', esc_attr( $key ), selected( $format, $key, false ), esc_html( $label ) );
            }
            ?>
        </select>
    </p>
    <p>
        <label for="pde_sub_capacity"><?php esc_html_e( 'Cupo', PDE_TEXTDOMAIN ); ?></label>
        <input type="number" id="pde_sub_capacity" name="pde_sub_capacity" value="<?php echo esc_attr( $capacity ); ?>" min="0" />
    </p>
    <p>
        <label for="pde_sub_schedule"><?php esc_html_e( 'Horarios', PDE_TEXTDOMAIN ); ?></label>
        <textarea id="pde_sub_schedule" name="pde_sub_schedule" class="widefat" rows="3"><?php echo esc_textarea( $schedule ); ?></textarea>
    </p>
    <fieldset>
        <legend><?php esc_html_e( 'Tabla de puntos específica', PDE_TEXTDOMAIN ); ?></legend>
        <?php foreach ( $points as $position => $value ) : ?>
            <p>
                <label>
                    <?php printf( esc_html__( 'Posición %d', PDE_TEXTDOMAIN ), (int) $position ); ?>
                    <input type="number" step="1" name="pde_sub_points_table[<?php echo esc_attr( $position ); ?>]" value="<?php echo esc_attr( $value ); ?>" />
                </label>
            </p>
        <?php endforeach; ?>
        <p class="description"><?php esc_html_e( 'Deja campos vacíos para heredar los valores de la competición.', PDE_TEXTDOMAIN ); ?></p>
    </fieldset>
    <?php
}

function pool_de_elias_save_competition_meta( $post_id ) {
    if ( ! isset( $_POST['pool_de_elias_competition_nonce'] ) || ! wp_verify_nonce( $_POST['pool_de_elias_competition_nonce'], 'pool_de_elias_save_competition_meta' ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! current_user_can( 'edit_competition', $post_id ) ) {
        return;
    }

    $meta = [
        '_pde_event_date'      => isset( $_POST['pde_event_date'] ) ? sanitize_text_field( wp_unslash( $_POST['pde_event_date'] ) ) : '',
        '_pde_status'          => isset( $_POST['pde_status'] ) ? sanitize_text_field( wp_unslash( $_POST['pde_status'] ) ) : 'draft',
        '_pde_level_target'    => isset( $_POST['pde_level_target'] ) ? sanitize_text_field( wp_unslash( $_POST['pde_level_target'] ) ) : 'abierta',
        '_pde_sex_restriction' => isset( $_POST['pde_sex_restriction'] ) ? sanitize_text_field( wp_unslash( $_POST['pde_sex_restriction'] ) ) : 'all',
        '_pde_age_min'         => isset( $_POST['pde_age_min'] ) ? intval( $_POST['pde_age_min'] ) : 0,
        '_pde_age_max'         => isset( $_POST['pde_age_max'] ) ? intval( $_POST['pde_age_max'] ) : 0,
        '_pde_capacity'        => isset( $_POST['pde_capacity'] ) ? intval( $_POST['pde_capacity'] ) : 0,
        '_pde_location'        => isset( $_POST['pde_location'] ) ? sanitize_text_field( wp_unslash( $_POST['pde_location'] ) ) : '',
        '_pde_organizer'       => isset( $_POST['pde_organizer'] ) ? sanitize_text_field( wp_unslash( $_POST['pde_organizer'] ) ) : '',
    ];

    foreach ( $meta as $key => $value ) {
        update_post_meta( $post_id, $key, $value );
    }

    $points = isset( $_POST['pde_points_table'] ) ? (array) $_POST['pde_points_table'] : [];
    $sanitized_points = [];
    foreach ( $points as $position => $value ) {
        $position = (int) $position;
        $sanitized_points[ $position ] = intval( $value );
    }
    update_post_meta( $post_id, '_pde_points_table', $sanitized_points );
}
add_action( 'save_post_competition', 'pool_de_elias_save_competition_meta' );

function pool_de_elias_save_subcompetition_meta( $post_id ) {
    if ( ! isset( $_POST['pool_de_elias_subcompetition_nonce'] ) || ! wp_verify_nonce( $_POST['pool_de_elias_subcompetition_nonce'], 'pool_de_elias_save_subcompetition_meta' ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! current_user_can( 'edit_subcompetition', $post_id ) ) {
        return;
    }

    $parent = isset( $_POST['pde_sub_parent'] ) ? intval( $_POST['pde_sub_parent'] ) : 0;
    update_post_meta( $post_id, '_pde_sub_parent', $parent );
    wp_update_post( [ 'ID' => $post_id, 'post_parent' => $parent ] );

    update_post_meta( $post_id, '_pde_sub_format', isset( $_POST['pde_sub_format'] ) ? sanitize_text_field( wp_unslash( $_POST['pde_sub_format'] ) ) : '' );
    update_post_meta( $post_id, '_pde_sub_capacity', isset( $_POST['pde_sub_capacity'] ) ? intval( $_POST['pde_sub_capacity'] ) : 0 );
    update_post_meta( $post_id, '_pde_sub_schedule', isset( $_POST['pde_sub_schedule'] ) ? wp_kses_post( wp_unslash( $_POST['pde_sub_schedule'] ) ) : '' );

    $points = isset( $_POST['pde_sub_points_table'] ) ? (array) $_POST['pde_sub_points_table'] : [];
    $sanitized_points = [];
    foreach ( $points as $position => $value ) {
        $position = (int) $position;
        if ( '' === $value ) {
            continue;
        }
        $sanitized_points[ $position ] = intval( $value );
    }
    update_post_meta( $post_id, '_pde_points_table', $sanitized_points );
}
add_action( 'save_post_subcompetition', 'pool_de_elias_save_subcompetition_meta' );

