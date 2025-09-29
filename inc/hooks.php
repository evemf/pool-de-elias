<?php
/**
 * Hooks personalitzats per al tema "Pool de Elias"
 * (AQUÍ no redefinim pool_de_elias_body_class per evitar redeclare:
 *   ja existeix a inc/setup.php; només li apliquem el filtre.)
 */

// -----------------------------------------
// body_class: només afegim el filtre; la funció viu a inc/setup.php
// -----------------------------------------
add_filter( 'body_class', 'pool_de_elias_body_class' );

// -----------------------------------------
// Títol per a single 'competition'
// -----------------------------------------
if ( ! function_exists( 'pool_de_elias_competition_title' ) ) {
	function pool_de_elias_competition_title( $title ) {
		if ( is_singular( 'competition' ) ) {
			$title .= ' | Pool de Elias';
		}
		return $title;
	}
}
// Nota: wp_title està deprecat; en sites moderns és millor 'document_title_parts'.
// Mantinc el teu hook per compatibilitat amb el que ja tenies:
add_filter( 'wp_title', 'pool_de_elias_competition_title' );

// Alternativa moderna (descomenta si vols substituir l’anterior):
/*
add_filter( 'document_title_parts', function( $parts ) {
	if ( is_singular( 'competition' ) ) {
		$parts['site'] = 'Pool de Elias';
	}
	return $parts;
} );
*/

// -----------------------------------------
// Admin notice en activar el tema
// -----------------------------------------
if ( ! function_exists( 'pool_de_elias_admin_notice' ) ) {
	function pool_de_elias_admin_notice() {
		if ( isset( $_GET['activated'] ) && is_admin() ) {
			echo '<div class="updated"><p><strong>¡Tema Pool de Elias activado exitosamente!</strong></p></div>';
		}
	}
}
add_action( 'admin_notices', 'pool_de_elias_admin_notice' );

// -----------------------------------------
// Footer personalitzat al tauler
// -----------------------------------------
if ( ! function_exists( 'pool_de_elias_custom_admin_footer' ) ) {
	function pool_de_elias_custom_admin_footer() {
		echo 'Desenvolupat per Evelia Molina per a Pool de Elias';
	}
}
add_filter( 'admin_footer_text', 'pool_de_elias_custom_admin_footer' );

// -----------------------------------------
// home_url: redirigeix només la home neta cap a /home
// (sense afectar rutes amb path)
// -----------------------------------------
if ( ! function_exists( 'pool_de_elias_custom_home_url' ) ) {
	function pool_de_elias_custom_home_url( $url, $path, $orig_scheme, $blog_id ) {
		// Només alterem quan no es demana un path concret
		if ( '' === $path || '/' === $path ) {
			$home_base = untrailingslashit( get_option( 'home' ) );
			return trailingslashit( $home_base . '/home' );
		}
		return $url;
	}
}
add_filter( 'home_url', 'pool_de_elias_custom_home_url', 10, 4 );
