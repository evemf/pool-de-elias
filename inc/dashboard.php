<?php
/**
 * Renderizado del dashboard privado.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function pool_de_elias_render_dashboard() {
    if ( current_user_can( 'manage_pool_results' ) ) {
        pool_de_elias_render_admin_dashboard();
        return;
    }

    pool_de_elias_render_player_dashboard();
}

function pool_de_elias_render_player_dashboard() {
    $user_id      = get_current_user_id();
    $profile      = pool_de_elias_get_user_profile_data( $user_id );
    $enrollments  = pool_de_elias_get_user_enrollments( $user_id );
    $grouped      = pool_de_elias_get_user_results_grouped( $user_id );
    $available    = pool_de_elias_get_available_competitions_for_user( $user_id );

    ?>
    <div class="pde-dashboard">
        <aside class="pde-dashboard__sidebar" aria-label="<?php esc_attr_e( 'Menú del panel', PDE_TEXTDOMAIN ); ?>">
            <ul>
                <li><a href="#perfil"><?php esc_html_e( 'Mi perfil', PDE_TEXTDOMAIN ); ?></a></li>
                <li><a href="#inscripciones"><?php esc_html_e( 'Mis inscripciones', PDE_TEXTDOMAIN ); ?></a></li>
                <li><a href="#explorar"><?php esc_html_e( 'Explorar competiciones', PDE_TEXTDOMAIN ); ?></a></li>
                <li><a href="#resultados"><?php esc_html_e( 'Resultados y puntos', PDE_TEXTDOMAIN ); ?></a></li>
            </ul>
        </aside>
        <div class="pde-dashboard__content">
            <section id="perfil" class="pde-card">
                <h2><?php esc_html_e( 'Mi perfil', PDE_TEXTDOMAIN ); ?></h2>
                <dl class="pde-definition">
                    <div><dt><?php esc_html_e( 'Nombre completo', PDE_TEXTDOMAIN ); ?></dt><dd><?php echo esc_html( $profile['first_name'] . ' ' . $profile['last_name'] ); ?></dd></div>
                    <div><dt><?php esc_html_e( 'Email', PDE_TEXTDOMAIN ); ?></dt><dd><?php echo esc_html( $profile['email'] ); ?></dd></div>
                    <div><dt><?php esc_html_e( 'Nivel', PDE_TEXTDOMAIN ); ?></dt><dd><?php echo esc_html( ucfirst( $profile['level'] ) ); ?></dd></div>
                </dl>
                <p><a class="pde-button" href="<?php echo esc_url( home_url( '/mi-perfil' ) ); ?>"><?php esc_html_e( 'Editar perfil', PDE_TEXTDOMAIN ); ?></a></p>
            </section>

            <section id="inscripciones" class="pde-card">
                <h2><?php esc_html_e( 'Mis inscripciones', PDE_TEXTDOMAIN ); ?></h2>
                <?php if ( empty( $enrollments ) ) : ?>
                    <p><?php esc_html_e( 'Todavía no te has inscrito en ninguna competición.', PDE_TEXTDOMAIN ); ?></p>
                <?php else : ?>
                    <table class="pde-table">
                        <thead>
                            <tr>
                                <th><?php esc_html_e( 'Competición', PDE_TEXTDOMAIN ); ?></th>
                                <th><?php esc_html_e( 'Subcompetición', PDE_TEXTDOMAIN ); ?></th>
                                <th><?php esc_html_e( 'Estado', PDE_TEXTDOMAIN ); ?></th>
                                <th><?php esc_html_e( 'Fecha', PDE_TEXTDOMAIN ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $enrollments as $row ) :
                                $competition    = get_post( $row['competition_id'] );
                                $subcompetition = $row['subcompetition_id'] ? get_post( $row['subcompetition_id'] ) : null;
                                ?>
                                <tr>
                                    <td><a href="<?php echo esc_url( get_permalink( $competition ) ); ?>"><?php echo esc_html( get_the_title( $competition ) ); ?></a></td>
                                    <td><?php echo $subcompetition ? esc_html( get_the_title( $subcompetition ) ) : '—'; ?></td>
                                    <td><?php echo esc_html( ucfirst( $row['status'] ) ); ?></td>
                                    <td><?php echo esc_html( mysql2date( get_option( 'date_format' ), $row['created_at'] ) ); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </section>

            <section id="explorar" class="pde-card">
                <h2><?php esc_html_e( 'Competiciones disponibles', PDE_TEXTDOMAIN ); ?></h2>
                <?php if ( empty( $available ) ) : ?>
                    <p><?php esc_html_e( 'No hay competiciones abiertas para tu perfil actualmente.', PDE_TEXTDOMAIN ); ?></p>
                <?php else : ?>
                    <div class="pde-competition-grid">
                        <?php foreach ( $available as $competition ) : ?>
                            <?php pool_de_elias_render_competition_card( $competition->ID, [ 'show_subs' => true ] ); ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>

            <section id="resultados" class="pde-card">
                <h2><?php esc_html_e( 'Resultados y puntos', PDE_TEXTDOMAIN ); ?></h2>
                <?php if ( empty( $grouped ) ) : ?>
                    <p><?php esc_html_e( 'Aún no tienes resultados registrados.', PDE_TEXTDOMAIN ); ?></p>
                <?php else : ?>
                    <?php foreach ( $grouped as $competition_id => $data ) : ?>
                        <article class="pde-result-block">
                            <header>
                                <h3><?php echo esc_html( get_the_title( $competition_id ) ); ?></h3>
                                <p><?php printf( esc_html__( 'Total: %s puntos', PDE_TEXTDOMAIN ), number_format_i18n( $data['total'] ) ); ?></p>
                            </header>
                            <ul>
                                <?php foreach ( $data['entries'] as $entry ) : ?>
                                    <li>
                                        <?php
                                        $sub = get_post( $entry['subcompetition_id'] );
                                        printf(
                                            '%1$s — %2$s %3$s',
                                            esc_html( $sub ? $sub->post_title : __( 'General', PDE_TEXTDOMAIN ) ),
                                            esc_html__( 'Posición', PDE_TEXTDOMAIN ),
                                            esc_html( $entry['position'] )
                                        );
                                        ?>
                                        <span class="pde-pill"><?php printf( esc_html__( '+%s pts', PDE_TEXTDOMAIN ), number_format_i18n( $entry['points'] ) ); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </section>
        </div>
    </div>
    <?php
}

function pool_de_elias_render_admin_dashboard() {
    $competitions_count    = wp_count_posts( 'competition' );
    $subcompetitions_count = wp_count_posts( 'subcompetition' );
    $competitions_total    = $competitions_count && isset( $competitions_count->publish ) ? $competitions_count->publish : 0;
    $subcompetitions_total = $subcompetitions_count && isset( $subcompetitions_count->publish ) ? $subcompetitions_count->publish : 0;

    ?>
    <div class="pde-dashboard pde-dashboard--admin">
        <aside class="pde-dashboard__sidebar">
            <ul>
                <li><a href="<?php echo esc_url( admin_url( 'edit.php?post_type=competition' ) ); ?>"><?php esc_html_e( 'Competiciones', PDE_TEXTDOMAIN ); ?></a></li>
                <li><a href="<?php echo esc_url( admin_url( 'edit.php?post_type=subcompetition' ) ); ?>"><?php esc_html_e( 'Subcompeticiones', PDE_TEXTDOMAIN ); ?></a></li>
                <li><a href="<?php echo esc_url( admin_url( 'admin.php?page=pool-de-elias-settings' ) ); ?>"><?php esc_html_e( 'Ajustes Pool de Elias', PDE_TEXTDOMAIN ); ?></a></li>
            </ul>
        </aside>
        <div class="pde-dashboard__content">
            <section class="pde-card">
                <h2><?php esc_html_e( 'Resumen ejecutivo', PDE_TEXTDOMAIN ); ?></h2>
                <div class="pde-stats">
                    <div class="pde-stat">
                        <span class="pde-stat__number"><?php echo esc_html( $competitions_total ); ?></span>
                        <span class="pde-stat__label"><?php esc_html_e( 'Competiciones activas', PDE_TEXTDOMAIN ); ?></span>
                    </div>
                    <div class="pde-stat">
                        <span class="pde-stat__number"><?php echo esc_html( $subcompetitions_total ); ?></span>
                        <span class="pde-stat__label"><?php esc_html_e( 'Subcompeticiones publicadas', PDE_TEXTDOMAIN ); ?></span>
                    </div>
                </div>
            </section>
            <section class="pde-card">
                <h2><?php esc_html_e( 'Próximas competiciones', PDE_TEXTDOMAIN ); ?></h2>
                <?php
                $upcoming = new WP_Query(
                    [
                        'post_type'      => 'competition',
                        'posts_per_page' => 5,
                        'meta_key'       => '_pde_event_date',
                        'orderby'        => 'meta_value',
                        'order'          => 'ASC',
                        'meta_query'     => [
                            [
                                'key'     => '_pde_event_date',
                                'value'   => current_time( 'mysql' ),
                                'compare' => '>=',
                                'type'    => 'DATETIME',
                            ],
                        ],
                    ]
                );
                ?>
                <?php if ( $upcoming->have_posts() ) : ?>
                    <ul class="pde-list">
                        <?php while ( $upcoming->have_posts() ) : $upcoming->the_post(); ?>
                            <li>
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                <span><?php echo esc_html( get_post_meta( get_the_ID(), '_pde_event_date', true ) ); ?></span>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else : ?>
                    <p><?php esc_html_e( 'Sin competiciones próximas.', PDE_TEXTDOMAIN ); ?></p>
                <?php endif; ?>
                <?php wp_reset_postdata(); ?>
            </section>
        </div>
    </div>
    <?php
}

function pool_de_elias_get_available_competitions_for_user( $user_id ) {
    $profile = pool_de_elias_get_user_profile_data( $user_id );

    $args = [
        'post_type'      => 'competition',
        'posts_per_page' => -1,
        'meta_query'     => [
            'relation' => 'AND',
            [
                'relation' => 'OR',
                [
                    'key'   => '_pde_level_target',
                    'value' => $profile['level'],
                ],
                [
                    'key'   => '_pde_level_target',
                    'value' => 'abierta',
                ],
            ],
            [
                'key'   => '_pde_status',
                'value' => 'open',
            ],
        ],
    ];

    return get_posts( $args );
}

function pool_de_elias_render_competition_card( $competition_id, $args = [] ) {
    $defaults = [
        'show_subs' => false,
    ];
    $args = wp_parse_args( $args, $defaults );

    $level      = get_post_meta( $competition_id, '_pde_level_target', true );
    $event_date = get_post_meta( $competition_id, '_pde_event_date', true );
    $status     = get_post_meta( $competition_id, '_pde_status', true );
    ?>
    <article class="pde-card pde-card--competition">
        <header>
            <h3><a href="<?php echo esc_url( get_permalink( $competition_id ) ); ?>"><?php echo esc_html( get_the_title( $competition_id ) ); ?></a></h3>
            <span class="pde-pill"><?php echo esc_html( ucfirst( $level ?: 'abierta' ) ); ?></span>
        </header>
        <p><?php echo wp_trim_words( get_post_field( 'post_content', $competition_id ), 18 ); ?></p>
        <ul class="pde-meta-list">
            <?php if ( $event_date ) : ?><li><strong><?php esc_html_e( 'Fecha', PDE_TEXTDOMAIN ); ?></strong> <?php echo esc_html( $event_date ); ?></li><?php endif; ?>
            <li><strong><?php esc_html_e( 'Estado', PDE_TEXTDOMAIN ); ?></strong> <?php echo esc_html( ucfirst( $status ) ); ?></li>
        </ul>
        <p><a class="pde-button pde-button--secondary" href="<?php echo esc_url( get_permalink( $competition_id ) ); ?>"><?php esc_html_e( 'Ver detalles', PDE_TEXTDOMAIN ); ?></a></p>

        <?php if ( $args['show_subs'] ) :
            $subs = get_posts(
                [
                    'post_type'      => 'subcompetition',
                    'posts_per_page' => -1,
                    'meta_key'       => '_pde_sub_parent',
                    'meta_value'     => $competition_id,
                ]
            );
            if ( $subs ) : ?>
                <div class="pde-subcompetitions">
                    <h4><?php esc_html_e( 'Subcompeticiones', PDE_TEXTDOMAIN ); ?></h4>
                    <?php foreach ( $subs as $sub ) : ?>
                        <div class="pde-sub-card">
                            <span><?php echo esc_html( $sub->post_title ); ?></span>
                            <?php if ( is_user_logged_in() ) : ?>
                                <form method="post" class="pde-form pde-form--enroll">
                                    <?php wp_nonce_field( 'pde_enroll', 'pde_enroll_nonce' ); ?>
                                    <input type="hidden" name="pde_competition_id" value="<?php echo esc_attr( $competition_id ); ?>">
                                    <input type="hidden" name="pde_subcompetition_id" value="<?php echo esc_attr( $sub->ID ); ?>">
                                    <button type="submit" class="pde-button pde-button--primary"><?php esc_html_e( 'Inscribirme', PDE_TEXTDOMAIN ); ?></button>
                                </form>
                            <?php else : ?>
                                <a class="pde-button pde-button--ghost" href="<?php echo esc_url( home_url( '/acceder' ) ); ?>"><?php esc_html_e( 'Accede para inscribirte', PDE_TEXTDOMAIN ); ?></a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; endif; ?>
    </article>
    <?php
}

