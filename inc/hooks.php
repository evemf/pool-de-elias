<?php
/**
 * Hooks personalizados para el tema "Pool de Elias"
 */

// Agregar una clase CSS personalizada al body según el tipo de página
function pool_de_elias_body_class($classes) {
    if (is_page('profile')) {
        $classes[] = 'profile-page';
    }
    if (is_page('competitions')) {
        $classes[] = 'competitions-page';
    }
    return $classes;
}
add_filter('body_class', 'pool_de_elias_body_class');

// Modificar el título de la página si es una competencia
function pool_de_elias_competition_title($title) {
    if (is_singular('competition')) {
        $title .= ' | Pool de Elias';
    }
    return $title;
}
add_filter('wp_title', 'pool_de_elias_competition_title');

// Mostrar un mensaje de éxito en el panel de administración
function pool_de_elias_admin_notice() {
    if (isset($_GET['activated']) && is_admin()) {
        echo '<div class="updated"><p><strong>¡Tema Pool de Elias activado exitosamente!</strong></p></div>';
    }
}
add_action('admin_notices', 'pool_de_elias_admin_notice');

// Agregar un mensaje personalizado en el footer del panel de administración
function pool_de_elias_custom_admin_footer() {
    echo 'Desarrollado por Evelia Molina para Pool de Elias';
}
add_filter('admin_footer_text', 'pool_de_elias_custom_admin_footer');

// Personalizar el enlace de la página de inicio sin romper otras rutas
function pool_de_elias_custom_home_url( $url, $path, $orig_scheme, $blog_id ) {
    // Solo alteramos la URL cuando no se solicita un path concreto.
    if ( '' === $path || '/' === $path ) {
        $home_base = untrailingslashit( get_option( 'home' ) );
        return trailingslashit( $home_base . '/home' );
    }

    return $url;
}
add_filter( 'home_url', 'pool_de_elias_custom_home_url', 10, 4 );
?>
