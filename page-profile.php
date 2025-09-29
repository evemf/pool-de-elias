<?php
/* Template Name: Mi Perfil */
get_header();
?>
<section class="pde-section">
    <div class="pde-card">
        <?php
        while ( have_posts() ) {
            the_post();
            the_content();
        }
        ?>
    </div>
</section>
<?php get_footer(); ?>
