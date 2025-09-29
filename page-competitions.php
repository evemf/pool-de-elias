<?php
/**
 * Template Name: Competitions Page
 * Descripción: Listado público de competiciones con búsqueda, ordenación y acciones de inscripción.
 */

if ( ! defined('ABSPATH') ) exit;

get_header();

// Parámetros de búsqueda/orden
$search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
$order  = isset($_GET['order']) && in_array($_GET['order'], ['asc','desc'], true) ? $_GET['order'] : 'desc';

// Paginación
$paged = max( 1, get_query_var('paged') ? get_query_var('paged') : ( get_query_var('page') ? get_query_var('page') : 1 ) );

// Query de competiciones
$args = [
    'post_type'      => 'competition',
    'post_status'    => 'publish',
    's'              => $search,
    'orderby'        => 'date',
    'order'          => $order,
    'posts_per_page' => 9,
    'paged'          => $paged,
];

$query = new WP_Query($args);
?>

<div class="container competitions-page">

    <header class="section-header">
        <h1><?php echo esc_html( get_the_title() ); ?></h1>
        <p class="section-subtitle">Explora y apúntate a las competiciones de Pool de Elias.</p>
    </header>

    <!-- Filtros básicos -->
    <form class="pde-filters" method="get" action="<?php echo esc_url( get_permalink() ); ?>">
        <div class="filters-row">
            <div class="filter-item">
                <label for="pde-search">Buscar</label>
                <input type="text" id="pde-search" name="s" value="<?php echo esc_attr($search); ?>" placeholder="Nombre, descripción…">
            </div>
            <div class="filter-item">
                <label for="pde-order">Orden</label>
                <select id="pde-order" name="order">
                    <option value="desc" <?php selected($order, 'desc'); ?>>Más recientes primero</option>
                    <option value="asc"  <?php selected($order, 'asc');  ?>>Más antiguas primero</option>
                </select>
            </div>
            <div class="filter-item filter-actions">
                <button type="submit" class="btn-primary">Aplicar</button>
                <a class="btn-secondary" href="<?php echo esc_url( get_permalink() ); ?>">Limpiar</a>
            </div>
        </div>
    </form>

    <?php if ( $query->have_posts() ) : ?>
        <div class="competitions-grid">
            <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                <?php
                $competition_id = get_the_ID();

                // Meta opcionales (si los guardas en tu flujo)
                $level        = get_post_meta( $competition_id, '_pde_level', true );         // 'Novel', 'Promesa', 'Experto', 'Máster' (si aplicas)
                $event_date   = get_post_meta( $competition_id, '_pde_date', true );          // YYYY-MM-DD o texto
                $distance     = get_post_meta( $competition_id, '_pde_distance', true );      // Distancia de partidas (si aplicas)
                $players      = get_post_meta( $competition_id, '_pde_players', true );       // array de user IDs
                $players      = is_array($players) ? $players : [];
                $joined       = false;

                if ( is_user_logged_in() ) {
                    $current_user_id = get_current_user_id();
                    $joined = in_array( $current_user_id, $players, true );
                }
                ?>
                <article <?php post_class('competition-card'); ?>>
                    <div class="competition-card__header">
                        <h2 class="competition-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h2>
                        <p class="competition-meta">
                            <span class="meta-item">
                                <strong>Fecha:</strong>
                                <?php echo $event_date ? esc_html($event_date) : esc_html( get_the_date() ); ?>
                            </span>
                            <?php if ( $level ) : ?>
                                <span class="meta-item"><strong>Nivel:</strong> <?php echo esc_html($level); ?></span>
                            <?php endif; ?>
                            <?php if ( $distance ) : ?>
                                <span class="meta-item"><strong>Distancia:</strong> <?php echo esc_html($distance); ?></span>
                            <?php endif; ?>
                            <span class="meta-item">
                                <strong>Inscritos:</strong> <?php echo (int) count($players); ?>
                            </span>
                        </p>
                    </div>

                    <div class="competition-card__body">
                        <div class="competition-excerpt">
                            <?php
                            if ( has_excerpt() ) {
                                the_excerpt();
                            } else {
                                echo wp_kses_post( wpautop( wp_trim_words( get_the_content(), 25, '…' ) ) );
                            }
                            ?>
                        </div>

                        <?php if ( ! empty( $players ) ) : ?>
                            <div class="competition-players">
                                <strong>Jugadores inscritos:</strong>
                                <ul>
                                    <?php
                                    // Mostrar hasta 8 jugadores, luego “+N”
                                    $max_show = 8;
                                    $shown = 0;
                                    foreach ( $players as $uid ) {
                                        $user = get_user_by( 'id', (int) $uid );
                                        if ( ! $user ) continue;
                                        $shown++;
                                        if ( $shown > $max_show ) break;
                                        echo '<li>' . esc_html( $user->display_name ?: $user->user_login ) . '</li>';
                                    }
                                    $resto = count($players) - $max_show;
                                    if ( $resto > 0 ) {
                                        echo '<li>+' . (int) $resto . ' más</li>';
                                    }
                                    ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="competition-card__footer">
                        <a class="btn-link" href="<?php the_permalink(); ?>">Ver detalles</a>

                        <?php if ( is_user_logged_in() ) : ?>
                            <?php if ( ! $joined ) : ?>
                                <form class="inline-form" method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
                                    <input type="hidden" name="action" value="pde_join_competition">
                                    <input type="hidden" name="competition_id" value="<?php echo (int) $competition_id; ?>">
                                    <?php wp_nonce_field( 'pde_join_' . $competition_id, 'pde_nonce' ); ?>
                                    <button type="submit" class="btn-primary">Inscribirme</button>
                                </form>
                            <?php else : ?>
                                <form class="inline-form" method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
                                    <input type="hidden" name="action" value="pde_leave_competition">
                                    <input type="hidden" name="competition_id" value="<?php echo (int) $competition_id; ?>">
                                    <?php wp_nonce_field( 'pde_leave_' . $competition_id, 'pde_nonce' ); ?>
                                    <button type="submit" class="btn-secondary">Salir</button>
                                </form>
                            <?php endif; ?>
                        <?php else : ?>
                            <a class="btn-secondary" href="<?php echo esc_url( home_url('/login') ); ?>">Inicia sesión para inscribirte</a>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>

        <!-- Paginación -->
        <nav class="pagination">
            <?php
            echo paginate_links( [
                'total'   => (int) $query->max_num_pages,
                'current' => (int) $paged,
                'mid_size'=> 2,
                'prev_text' => '← Anterior',
                'next_text' => 'Siguiente →',
            ] );
            ?>
        </nav>

    <?php else : ?>
        <p>No hay competiciones que coincidan con tu búsqueda.</p>
    <?php endif; wp_reset_postdata(); ?>
</div>

<?php get_footer(); ?>
