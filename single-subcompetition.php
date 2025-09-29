<?php
get_header();
if ( have_posts() ) :
    while ( have_posts() ) : the_post();
        $competition_id = (int) get_post_meta( get_the_ID(), '_pde_sub_parent', true );
        $competition    = $competition_id ? get_post( $competition_id ) : null;
        $results        = pool_de_elias_get_subcompetition_results( get_the_ID() );
        ?>
        <section class="pde-section">
            <div class="pde-card">
                <h1><?php the_title(); ?></h1>
                <?php if ( $competition ) : ?>
                    <p><a href="<?php echo esc_url( get_permalink( $competition ) ); ?>">&larr; <?php esc_html_e( 'Volver a la competición', PDE_TEXTDOMAIN ); ?></a></p>
                <?php endif; ?>
                <div class="pde-content">
                    <?php the_content(); ?>
                </div>
                <?php if ( is_user_logged_in() ) : ?>
                    <form method="post" class="pde-form pde-form--enroll">
                        <?php wp_nonce_field( 'pde_enroll', 'pde_enroll_nonce' ); ?>
                        <input type="hidden" name="pde_competition_id" value="<?php echo esc_attr( $competition_id ); ?>">
                        <input type="hidden" name="pde_subcompetition_id" value="<?php echo esc_attr( get_the_ID() ); ?>">
                        <button type="submit" class="pde-button pde-button--primary"><?php esc_html_e( 'Inscribirme', PDE_TEXTDOMAIN ); ?></button>
                    </form>
                <?php endif; ?>
            </div>
            <div class="pde-card">
                <h2><?php esc_html_e( 'Clasificación', PDE_TEXTDOMAIN ); ?></h2>
                <?php if ( $results ) : ?>
                    <table class="pde-table">
                        <thead><tr><th><?php esc_html_e( 'Jugador', PDE_TEXTDOMAIN ); ?></th><th><?php esc_html_e( 'Puntos', PDE_TEXTDOMAIN ); ?></th></tr></thead>
                        <tbody>
                            <?php foreach ( $results as $row ) :
                                $user = get_userdata( $row['user_id'] );
                                ?>
                                <tr>
                                    <td><?php echo esc_html( $user ? $user->display_name : __( 'Jugador', PDE_TEXTDOMAIN ) ); ?></td>
                                    <td><?php echo esc_html( number_format_i18n( $row['points'] ) ); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <p><?php esc_html_e( 'No hay resultados registrados todavía.', PDE_TEXTDOMAIN ); ?></p>
                <?php endif; ?>
            </div>
        </section>
        <?php
    endwhile;
endif;
get_footer();
