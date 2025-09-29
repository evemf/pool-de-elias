<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<header class="site-header" role="banner">
    <div class="site-header__inner">
        <div class="site-logo" aria-label="<?php esc_attr_e( 'Inicio', PDE_TEXTDOMAIN ); ?>">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                <?php
                if ( has_custom_logo() ) {
                    the_custom_logo();
                } else {
                    echo '<span class="site-title">' . esc_html( get_bloginfo( 'name' ) ) . '</span>';
                }
                ?>
            </a>
        </div>
        <nav class="site-nav" aria-label="<?php esc_attr_e( 'Menú principal', PDE_TEXTDOMAIN ); ?>">
            <?php
            wp_nav_menu(
                [
                    'theme_location' => 'primary',
                    'menu_class'     => 'primary-menu',
                    'container'      => false,
                    'fallback_cb'    => '__return_empty_string',
                ]
            );
            ?>
        </nav>
        <div class="site-header__actions">
            <?php if ( is_user_logged_in() ) : ?>
                <a class="pde-button pde-button--secondary" href="<?php echo esc_url( home_url( '/dashboard' ) ); ?>">
                    <span class="dashicons dashicons-admin-users" aria-hidden="true"></span>
                    <span><?php esc_html_e( 'Mi panel', PDE_TEXTDOMAIN ); ?></span>
                </a>
                <a class="pde-button" href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>"><?php esc_html_e( 'Salir', PDE_TEXTDOMAIN ); ?></a>
            <?php else : ?>
                <a class="pde-button pde-button--secondary" href="<?php echo esc_url( home_url( '/acceder' ) ); ?>"><?php esc_html_e( 'Acceder', PDE_TEXTDOMAIN ); ?></a>
                <a class="pde-button pde-button--primary" href="<?php echo esc_url( home_url( '/registro' ) ); ?>"><?php esc_html_e( 'Registrarme', PDE_TEXTDOMAIN ); ?></a>
            <?php endif; ?>
            <?php echo pool_de_elias_render_header_cart(); ?>
        </div>
    </div>
</header>
<main id="content" class="site-content" role="main">
