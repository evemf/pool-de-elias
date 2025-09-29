<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php bloginfo( 'name' ); ?></title>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<header>
    <div class="container">
        <!-- Logo -->
        <div class="logo">
            <?php 
            if ( has_custom_logo() ) {
                the_custom_logo(); 
            } else {
                // Si no hay logotipo personalizado, usamos el logo predeterminado
                echo '<img src="' . get_template_directory_uri() . '/assets/img/logo.png" alt="Pool de Elias Logo" style="max-height: 80px;">';
            }
            ?>
        </div>

        <!-- Barra de Navegación -->
        <nav class="main-nav">
            <?php
            wp_nav_menu( array(
                'theme_location' => 'primary',
                'container' => false,
                'menu_class' => 'menu',
            ) );
            ?>
        </nav>

        <!-- Zona de Login/Registro -->
        <div class="auth-buttons">
            <?php if ( ! is_user_logged_in() ) : ?>
                <a href="<?php echo esc_url( home_url('/login') ); ?>" class="btn-login">Login</a>
                <a href="<?php echo esc_url( home_url('/register') ); ?>" class="btn-register">Registro</a>
            <?php else : ?>
                <a href="<?php echo esc_url( home_url('/dashboard') ); ?>" class="btn-dashboard">Dashboard</a>
                <a href="<?php echo esc_url( wp_logout_url( home_url('/') ) ); ?>" class="btn-logout">Logout</a>
            <?php endif; ?>
        </div>
    </div>
</header>
