<?php
/* Template Name: Listado de competiciones */
get_header();
?>
<section class="pde-section">
    <?php
    while ( have_posts() ) {
        the_post();
        the_content();
    }
    ?>
</section>
<?php get_footer(); ?>
