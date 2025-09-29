<?php
/* Template Name: Dashboard Pool */
if ( ! is_user_logged_in() ) {
    wp_safe_redirect( home_url( '/acceder' ) );
    exit;
}

get_header();
?>
<section class="pde-section">
    <?php
    while ( have_posts() ) {
        the_post();
        echo '<div class="pde-dashboard-wrapper">';
        the_content();
        echo '</div>';
    }
    ?>
</section>
<?php get_footer(); ?>
