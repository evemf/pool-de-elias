<?php
get_header();
if ( have_posts() ) :
    while ( have_posts() ) :
        the_post();
        $competition_id = get_the_ID();
        $event_date     = get_post_meta( $competition_id, '_pde_event_date', true );
        $status         = get_post_meta( $competition_id, '_pde_status', true );
        $location       = get_post_meta( $competition_id, '_pde_location', true );
        $organizer      = get_post_meta( $competition_id, '_pde_organizer', true );
        $level          = get_post_meta( $competition_id, '_pde_level_target', true );
        $rankings       = pool_de_elias_get_competition_rankings( $competition_id );
        ?>
        <section class="pde-hero pde-hero--left" style="background-image:url('<?php echo esc_url( get_the_post_thumbnail_url( $competition_id, 'full' ) ?: PDE_THEME_URL . '/assets/img/hero-default.svg' ); ?>');">
            <span class="pde-hero__overlay" style="opacity:.5"></span>
            <div class="pde-hero__content">
                <h1><?php the_title(); ?></h1>
                <?php if ( $event_date ) : ?><p><?php printf( esc_html__( 'Fecha: %s', PDE_TEXTDOMAIN ), esc_html( mysql2date( get_option( 'date_format' ), $event_date ) ) ); ?></p><?php endif; ?>
                <a class="pde-button pde-button--primary" href="<?php echo esc_url( home_url( '/registro' ) ); ?>"><?php esc_html_e( 'Únete a la comunidad', PDE_TEXTDOMAIN ); ?></a>
            </div>
        </section>
        <section class="pde-section">
            <div class="pde-card">
                <h2><?php esc_html_e( 'Descripción', PDE_TEXTDOMAIN ); ?></h2>
                <div class="pde-content">
                    <?php the_content(); ?>
                </div>
                <ul class="pde-meta-list">
                    <?php if ( $level ) : ?><li><strong><?php esc_html_e( 'Nivel objetivo:', PDE_TEXTDOMAIN ); ?></strong> <?php echo esc_html( ucfirst( $level ) ); ?></li><?php endif; ?>
                    <?php if ( $location ) : ?><li><strong><?php esc_html_e( 'Ubicación:', PDE_TEXTDOMAIN ); ?></strong> <?php echo esc_html( $location ); ?></li><?php endif; ?>
                    <?php if ( $organizer ) : ?><li><strong><?php esc_html_e( 'Organizador:', PDE_TEXTDOMAIN ); ?></strong> <?php echo esc_html( $organizer ); ?></li><?php endif; ?>
                    <li><strong><?php esc_html_e( 'Estado:', PDE_TEXTDOMAIN ); ?></strong> <?php echo esc_html( ucfirst( $status ) ); ?></li>
                </ul>
            </div>
            <div class="pde-card">
                <h2><?php esc_html_e( 'Subcompeticiones', PDE_TEXTDOMAIN ); ?></h2>
                <?php
                $subs = get_posts(
                    [
                        'post_type'      => 'subcompetition',
                        'posts_per_page' => -1,
                        'meta_key'       => '_pde_sub_parent',
                        'meta_value'     => $competition_id,
                    ]
                );
                ?>
                <?php if ( $subs ) : ?>
                    <div class="pde-subcompetitions">
                        <?php foreach ( $subs as $sub ) :
                            $format   = get_post_meta( $sub->ID, '_pde_sub_format', true );
                            $schedule = get_post_meta( $sub->ID, '_pde_sub_schedule', true );
                            ?>
                            <div class="pde-sub-card">
                                <div>
                                    <h3><?php echo esc_html( $sub->post_title ); ?></h3>
                                    <?php if ( $format ) : ?><p><?php printf( esc_html__( 'Formato: %s', PDE_TEXTDOMAIN ), esc_html( $format ) ); ?></p><?php endif; ?>
                                    <?php if ( $schedule ) : ?><p><?php echo esc_html( $schedule ); ?></p><?php endif; ?>
                                </div>
                                <?php if ( is_user_logged_in() ) : ?>
                                    <form method="post" class="pde-form pde-form--enroll">
                                        <?php wp_nonce_field( 'pde_enroll', 'pde_enroll_nonce' ); ?>
                                        <input type="hidden" name="pde_competition_id" value="<?php echo esc_attr( $competition_id ); ?>">
                                        <input type="hidden" name="pde_subcompetition_id" value="<?php echo esc_attr( $sub->ID ); ?>">
                                        <button type="submit" class="pde-button pde-button--primary"><?php esc_html_e( 'Inscribirme', PDE_TEXTDOMAIN ); ?></button>
                                    </form>
                                <?php else : ?>
                                    <a class="pde-button pde-button--secondary" href="<?php echo esc_url( home_url( '/acceder' ) ); ?>"><?php esc_html_e( 'Acceder para inscribirme', PDE_TEXTDOMAIN ); ?></a>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <p><?php esc_html_e( 'Aún no se han configurado subcompeticiones.', PDE_TEXTDOMAIN ); ?></p>
                <?php endif; ?>
            </div>
            <div class="pde-card">
                <h2><?php esc_html_e( 'Ranking provisional', PDE_TEXTDOMAIN ); ?></h2>
                <?php if ( $rankings ) : ?>
                    <table class="pde-table">
                        <thead>
                            <tr>
                                <th><?php esc_html_e( 'Jugador', PDE_TEXTDOMAIN ); ?></th>
                                <th><?php esc_html_e( 'Puntos', PDE_TEXTDOMAIN ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $rankings as $row ) :
                                $user = get_userdata( $row['user_id'] );
                                ?>
                                <tr>
                                    <td><?php echo esc_html( $user ? $user->display_name : __( 'Jugador', PDE_TEXTDOMAIN ) ); ?></td>
                                    <td><?php echo esc_html( number_format_i18n( $row['total_points'] ) ); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <p><?php esc_html_e( 'Todavía no hay resultados registrados.', PDE_TEXTDOMAIN ); ?></p>
                <?php endif; ?>
            </div>
        </section>
        <?php
    endwhile;
endif;
get_footer();
