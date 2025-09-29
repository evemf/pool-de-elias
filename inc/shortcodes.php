<?php
/**
 * Shortcodes front-end.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function pool_de_elias_register_form_shortcode() {
    if ( is_user_logged_in() ) {
        return '<p>' . esc_html__( 'Ya estás registrado.', PDE_TEXTDOMAIN ) . '</p>';
    }

    ob_start();
    ?>
    <form method="post" class="pde-form pde-form--register">
        <?php wp_nonce_field( 'pde_register_user', 'pde_register_nonce' ); ?>
        <div class="pde-grid">
            <p>
                <label for="pde_first_name"><?php esc_html_e( 'Nombre', PDE_TEXTDOMAIN ); ?></label>
                <input type="text" id="pde_first_name" name="pde_first_name" required>
            </p>
            <p>
                <label for="pde_last_name"><?php esc_html_e( 'Apellidos', PDE_TEXTDOMAIN ); ?></label>
                <input type="text" id="pde_last_name" name="pde_last_name" required>
            </p>
        </div>
        <p>
            <label for="pde_email"><?php esc_html_e( 'Correo electrónico', PDE_TEXTDOMAIN ); ?></label>
            <input type="email" id="pde_email" name="pde_email" required>
        </p>
        <p>
            <label for="pde_password"><?php esc_html_e( 'Contraseña', PDE_TEXTDOMAIN ); ?></label>
            <input type="password" id="pde_password" name="pde_password" minlength="8" required>
        </p>
        <div class="pde-grid">
            <p>
                <label for="pde_sex"><?php esc_html_e( 'Sexo', PDE_TEXTDOMAIN ); ?></label>
                <select id="pde_sex" name="pde_sex">
                    <option value="m"><?php esc_html_e( 'Masculino', PDE_TEXTDOMAIN ); ?></option>
                    <option value="f"><?php esc_html_e( 'Femenino', PDE_TEXTDOMAIN ); ?></option>
                    <option value="other"><?php esc_html_e( 'Otro', PDE_TEXTDOMAIN ); ?></option>
                </select>
            </p>
            <p>
                <label for="pde_birthdate"><?php esc_html_e( 'Fecha de nacimiento', PDE_TEXTDOMAIN ); ?></label>
                <input type="date" id="pde_birthdate" name="pde_birthdate" required>
            </p>
        </div>
        <p>
            <label for="pde_level"><?php esc_html_e( 'Nivel', PDE_TEXTDOMAIN ); ?></label>
            <select id="pde_level" name="pde_level">
                <option value="novel" selected><?php esc_html_e( 'Novel', PDE_TEXTDOMAIN ); ?></option>
                <option value="promesa"><?php esc_html_e( 'Promesa', PDE_TEXTDOMAIN ); ?></option>
                <option value="experto"><?php esc_html_e( 'Experto', PDE_TEXTDOMAIN ); ?></option>
                <option value="master"><?php esc_html_e( 'Máster', PDE_TEXTDOMAIN ); ?></option>
            </select>
        </p>
        <p class="pde-form__privacy">
            <label>
                <input type="checkbox" name="pde_privacy" required>
                <?php printf( esc_html__( 'Acepto la %s.', PDE_TEXTDOMAIN ), '<a href="' . esc_url( get_privacy_policy_url() ) . '">' . esc_html__( 'política de privacidad', PDE_TEXTDOMAIN ) . '</a>' ); ?>
            </label>
        </p>
        <p>
            <button type="submit" class="pde-button pde-button--primary"><?php esc_html_e( 'Crear cuenta', PDE_TEXTDOMAIN ); ?></button>
        </p>
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode( 'pool_de_elias_register_form', 'pool_de_elias_register_form_shortcode' );

function pool_de_elias_login_form_shortcode() {
    if ( is_user_logged_in() ) {
        return '<p>' . esc_html__( 'Ya has iniciado sesión.', PDE_TEXTDOMAIN ) . '</p>';
    }

    ob_start();
    ?>
    <form method="post" class="pde-form pde-form--login">
        <?php wp_nonce_field( 'pde_login_user', 'pde_login_nonce' ); ?>
        <p>
            <label for="pde_login"><?php esc_html_e( 'Correo electrónico', PDE_TEXTDOMAIN ); ?></label>
            <input type="email" id="pde_login" name="pde_login" required>
        </p>
        <p>
            <label for="pde_login_pass"><?php esc_html_e( 'Contraseña', PDE_TEXTDOMAIN ); ?></label>
            <input type="password" id="pde_login_pass" name="pde_login_pass" required>
        </p>
        <p>
            <button type="submit" class="pde-button pde-button--primary"><?php esc_html_e( 'Acceder', PDE_TEXTDOMAIN ); ?></button>
        </p>
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode( 'pool_de_elias_login_form', 'pool_de_elias_login_form_shortcode' );

function pool_de_elias_profile_shortcode() {
    if ( ! is_user_logged_in() ) {
        return '<p>' . esc_html__( 'Necesitas acceder para ver tu perfil.', PDE_TEXTDOMAIN ) . '</p>';
    }

    $user_id = get_current_user_id();
    $profile = pool_de_elias_get_user_profile_data( $user_id );
    $updated = isset( $_GET['updated'] );

    ob_start();
    ?>
    <?php if ( $updated ) : ?>
        <div class="pde-notice pde-notice--success"><?php esc_html_e( 'Perfil actualizado correctamente.', PDE_TEXTDOMAIN ); ?></div>
    <?php endif; ?>
    <form method="post" class="pde-form pde-form--profile">
        <?php wp_nonce_field( 'pde_save_profile', 'pde_profile_nonce' ); ?>
        <div class="pde-grid">
            <p>
                <label for="pde_first_name"><?php esc_html_e( 'Nombre', PDE_TEXTDOMAIN ); ?></label>
                <input type="text" id="pde_first_name" name="pde_first_name" value="<?php echo esc_attr( $profile['first_name'] ); ?>" required>
            </p>
            <p>
                <label for="pde_last_name"><?php esc_html_e( 'Apellidos', PDE_TEXTDOMAIN ); ?></label>
                <input type="text" id="pde_last_name" name="pde_last_name" value="<?php echo esc_attr( $profile['last_name'] ); ?>" required>
            </p>
        </div>
        <p>
            <label for="pde_email"><?php esc_html_e( 'Email', PDE_TEXTDOMAIN ); ?></label>
            <input type="email" id="pde_email" value="<?php echo esc_attr( $profile['email'] ); ?>" disabled>
        </p>
        <div class="pde-grid">
            <p>
                <label for="pde_sex"><?php esc_html_e( 'Sexo', PDE_TEXTDOMAIN ); ?></label>
                <select id="pde_sex" name="pde_sex">
                    <option value="m" <?php selected( $profile['sex'], 'm' ); ?>><?php esc_html_e( 'Masculino', PDE_TEXTDOMAIN ); ?></option>
                    <option value="f" <?php selected( $profile['sex'], 'f' ); ?>><?php esc_html_e( 'Femenino', PDE_TEXTDOMAIN ); ?></option>
                    <option value="other" <?php selected( $profile['sex'], 'other' ); ?>><?php esc_html_e( 'Otro', PDE_TEXTDOMAIN ); ?></option>
                </select>
            </p>
            <p>
                <label for="pde_birthdate"><?php esc_html_e( 'Fecha de nacimiento', PDE_TEXTDOMAIN ); ?></label>
                <input type="date" id="pde_birthdate" name="pde_birthdate" value="<?php echo esc_attr( $profile['birthdate'] ); ?>" required>
            </p>
        </div>
        <p>
            <label for="pde_level"><?php esc_html_e( 'Nivel', PDE_TEXTDOMAIN ); ?></label>
            <select id="pde_level" name="pde_level">
                <option value="novel" <?php selected( $profile['level'], 'novel' ); ?>><?php esc_html_e( 'Novel', PDE_TEXTDOMAIN ); ?></option>
                <option value="promesa" <?php selected( $profile['level'], 'promesa' ); ?>><?php esc_html_e( 'Promesa', PDE_TEXTDOMAIN ); ?></option>
                <option value="experto" <?php selected( $profile['level'], 'experto' ); ?>><?php esc_html_e( 'Experto', PDE_TEXTDOMAIN ); ?></option>
                <option value="master" <?php selected( $profile['level'], 'master' ); ?>><?php esc_html_e( 'Máster', PDE_TEXTDOMAIN ); ?></option>
            </select>
        </p>
        <p>
            <button type="submit" class="pde-button pde-button--primary"><?php esc_html_e( 'Guardar cambios', PDE_TEXTDOMAIN ); ?></button>
        </p>
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode( 'pool_de_elias_profile', 'pool_de_elias_profile_shortcode' );

function pool_de_elias_dashboard_shortcode() {
    if ( ! is_user_logged_in() ) {
        return '<p>' . esc_html__( 'Debes iniciar sesión para acceder al panel.', PDE_TEXTDOMAIN ) . '</p>';
    }

    ob_start();
    pool_de_elias_render_dashboard();
    return ob_get_clean();
}
add_shortcode( 'pool_de_elias_dashboard', 'pool_de_elias_dashboard_shortcode' );

function pool_de_elias_competitions_shortcode( $atts = [] ) {
    $atts = shortcode_atts(
        [
            'level' => '',
        ],
        $atts,
        'pool_de_elias_competitions'
    );

    $args = [
        'post_type'      => 'competition',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
    ];

    if ( ! empty( $atts['level'] ) ) {
        $args['meta_query'] = [
            [
                'key'   => '_pde_level_target',
                'value' => sanitize_text_field( $atts['level'] ),
            ],
        ];
    }

    $query = new WP_Query( $args );

    ob_start();
    ?>
    <div class="pde-competition-grid">
        <?php if ( $query->have_posts() ) : ?>
            <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                <?php pool_de_elias_render_competition_card( get_the_ID() ); ?>
            <?php endwhile; ?>
        <?php else : ?>
            <p><?php esc_html_e( 'No hay competiciones disponibles.', PDE_TEXTDOMAIN ); ?></p>
        <?php endif; ?>
    </div>
    <?php
    wp_reset_postdata();

    return ob_get_clean();
}
add_shortcode( 'pool_de_elias_competitions', 'pool_de_elias_competitions_shortcode' );
