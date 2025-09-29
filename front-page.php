<?php
get_header();
?>
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
    <?php if ( has_blocks( get_the_content() ) ) : ?>
        <?php the_content(); ?>
    <?php else : ?>
        <section class="pde-hero pde-hero--center" style="background-image:url('<?php echo esc_url( PDE_THEME_URL . '/assets/img/hero-default.svg' ); ?>');">
            <span class="pde-hero__overlay" style="opacity:.45"></span>
            <div class="pde-hero__content">
                <h1><?php echo esc_html( get_bloginfo( 'name' ) ); ?></h1>
                <p><?php echo esc_html( get_bloginfo( 'description' ) ); ?></p>
                <a class="pde-button pde-button--primary" href="<?php echo esc_url( home_url( '/competiciones' ) ); ?>"><?php esc_html_e( 'Ver competiciones', PDE_TEXTDOMAIN ); ?></a>
            </div>
        </section>
        <section class="pde-section pde-section--light">
            <div class="pde-card pde-shop-placeholder">
                <h2 class="pde-section__title"><?php esc_html_e( 'Tienda oficial', PDE_TEXTDOMAIN ); ?></h2>
                <p><?php esc_html_e( 'Próximamente podrás comprar equipamiento y merchandising exclusivo.', PDE_TEXTDOMAIN ); ?></p>
                <?php echo do_shortcode( '[products limit="4" columns="4"]' ); ?>
            </div>
        </section>
        <section class="pde-section">
            <h2 class="pde-section__title"><?php esc_html_e( 'Competiciones destacadas', PDE_TEXTDOMAIN ); ?></h2>
            <?php echo do_shortcode( '[pool_de_elias_competitions]' ); ?>
        </section>
    <?php endif; ?>
<?php endwhile; endif; ?>
<?php get_footer(); ?>
