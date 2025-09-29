<?php
/**
 * Bloque Gutenberg Hero.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'init', 'pool_de_elias_register_hero_block' );

function pool_de_elias_register_hero_block() {
    wp_register_script(
        'pool-de-elias-hero-block',
        PDE_THEME_URL . '/assets/js/hero-block.js',
        [ 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor', 'wp-i18n' ],
        PDE_THEME_VERSION
    );

    register_block_type(
        'pde/hero',
        [
            'editor_script'   => 'pool-de-elias-hero-block',
            'render_callback' => 'pool_de_elias_render_hero_block',
            'attributes'      => [
                'title' => [ 'type' => 'string', 'default' => __( 'Pool de Elias', PDE_TEXTDOMAIN ) ],
                'subtitle' => [ 'type' => 'string', 'default' => __( 'Competiciones de pool profesionales', PDE_TEXTDOMAIN ) ],
                'ctaText' => [ 'type' => 'string', 'default' => __( 'Explorar competiciones', PDE_TEXTDOMAIN ) ],
                'ctaUrl' => [ 'type' => 'string', 'default' => home_url( '/competiciones' ) ],
                'mediaId' => [ 'type' => 'number', 'default' => 0 ],
                'overlay' => [ 'type' => 'number', 'default' => 0.4 ],
                'layout' => [ 'type' => 'string', 'default' => 'center' ],
                'height' => [ 'type' => 'string', 'default' => 'md' ],
                'showCompetitionCTA' => [ 'type' => 'boolean', 'default' => true ],
            ],
        ]
    );
}

function pool_de_elias_render_hero_block( $attributes ) {
    $title    = isset( $attributes['title'] ) ? $attributes['title'] : '';
    $subtitle = isset( $attributes['subtitle'] ) ? $attributes['subtitle'] : '';
    $cta_text = isset( $attributes['ctaText'] ) ? $attributes['ctaText'] : '';
    $cta_url  = isset( $attributes['ctaUrl'] ) ? $attributes['ctaUrl'] : '';
    $media_id = isset( $attributes['mediaId'] ) ? (int) $attributes['mediaId'] : 0;
    $overlay  = isset( $attributes['overlay'] ) ? floatval( $attributes['overlay'] ) : 0.3;
    $layout   = isset( $attributes['layout'] ) ? $attributes['layout'] : 'center';
    $height   = isset( $attributes['height'] ) ? $attributes['height'] : 'md';
    $show_cta = ! empty( $attributes['showCompetitionCTA'] );

    $background = $media_id ? wp_get_attachment_image_url( $media_id, 'full' ) : PDE_THEME_URL . '/assets/img/hero-default.svg';

    ob_start();
    ?>
    <section class="pde-hero pde-hero--<?php echo esc_attr( $layout ); ?> pde-hero--<?php echo esc_attr( $height ); ?>" style="background-image:url('<?php echo esc_url( $background ); ?>');">
        <span class="pde-hero__overlay" style="opacity:<?php echo esc_attr( $overlay ); ?>"></span>
        <div class="pde-hero__content">
            <?php if ( $title ) : ?><h1><?php echo esc_html( $title ); ?></h1><?php endif; ?>
            <?php if ( $subtitle ) : ?><p><?php echo esc_html( $subtitle ); ?></p><?php endif; ?>
            <?php if ( $cta_text ) : ?>
                <a class="pde-button pde-button--primary" href="<?php echo esc_url( $cta_url ); ?>"><?php echo esc_html( $cta_text ); ?></a>
            <?php endif; ?>
            <?php if ( $show_cta ) : ?>
                <a class="pde-button pde-button--ghost" href="<?php echo esc_url( home_url( '/competiciones' ) ); ?>"><?php esc_html_e( 'Ver competiciones', PDE_TEXTDOMAIN ); ?></a>
            <?php endif; ?>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

