<?php get_header(); ?>

<!-- Zona Hero -->
<section class="hero" style="background-image: url('<?php echo esc_url(get_theme_mod('hero_background_image')); ?>');">
    <div class="hero-content">
        <h1><?php echo esc_html(get_theme_mod('hero_title', 'Bienvenido a Pool de Elias')); ?></h1>
        <p><?php echo esc_html(get_theme_mod('hero_subtitle', '¡La mejor plataforma de competiciones de billar!')); ?></p>
        <a href="#shop" class="btn-hero" style="background-color: <?php echo esc_attr(get_theme_mod('hero_button_color', '#F1A13C')); ?>;">
            <?php echo esc_html(get_theme_mod('hero_button_text', 'Jugar')); ?>
        </a>
    </div>
</section>

<!-- Sección de Tienda -->
<section id="shop" class="shop">
    <div class="container">
        <h2>Tiendas de Accesorios de Billar</h2>
        <p>Encuentra los mejores productos para mejorar tu juego.</p>

        <!-- Aquí puedes agregar los productos de tu tienda -->
        <div class="product-list">
            <div class="product-item">
                <img src="path-to-product-image.jpg" alt="Cuesport Ball" />
                <h3>Cuesport Ball</h3>
                <p>Precio: $20</p>
            </div>
            <div class="product-item">
                <img src="path-to-product-image.jpg" alt="Cuesport Stick" />
                <h3>Cuesport Stick</h3>
                <p>Precio: $50</p>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
