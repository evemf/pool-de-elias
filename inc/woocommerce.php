<?php
/**
 * Integración básica con WooCommerce.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function pool_de_elias_woocommerce_active() {
    return class_exists( 'WooCommerce' );
}

function pool_de_elias_cart_count_fragment( $fragments ) {
    if ( ! pool_de_elias_woocommerce_active() ) {
        return $fragments;
    }

    $count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;

    ob_start();
    ?>
    <span class="pde-cart-count"><?php echo esc_html( $count ); ?></span>
    <?php
    $fragments['span.pde-cart-count'] = ob_get_clean();

    return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'pool_de_elias_cart_count_fragment' );

/**
 * Devuelve HTML del mini carrito.
 */
function pool_de_elias_render_header_cart() {
    if ( ! pool_de_elias_woocommerce_active() ) {
        return '';
    }

    ob_start();
    ?>
    <a class="pde-header-cart" href="<?php echo esc_url( wc_get_cart_url() ); ?>" aria-label="<?php esc_attr_e( 'Ver carrito', PDE_TEXTDOMAIN ); ?>">
        <span class="dashicons dashicons-cart"></span>
        <span class="pde-cart-count"><?php echo esc_html( WC()->cart ? WC()->cart->get_cart_contents_count() : 0 ); ?></span>
    </a>
    <div class="pde-mini-cart" aria-hidden="true">
        <?php woocommerce_mini_cart(); ?>
    </div>
    <?php
    return ob_get_clean();
}
