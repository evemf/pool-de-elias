<?php
/**
 * Roles y capacidades.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function pool_de_elias_get_capabilities_matrix() {
    $caps = [
        'competition'    => [
            'edit_competition',
            'read_competition',
            'delete_competition',
            'edit_competitions',
            'edit_others_competitions',
            'publish_competitions',
            'read_private_competitions',
            'delete_competitions',
            'delete_private_competitions',
            'delete_published_competitions',
            'delete_others_competitions',
            'edit_private_competitions',
            'edit_published_competitions',
        ],
        'subcompetition' => [
            'edit_subcompetition',
            'read_subcompetition',
            'delete_subcompetition',
            'edit_subcompetitions',
            'edit_others_subcompetitions',
            'publish_subcompetitions',
            'read_private_subcompetitions',
            'delete_subcompetitions',
            'delete_private_subcompetitions',
            'delete_published_subcompetitions',
            'delete_others_subcompetitions',
            'edit_private_subcompetitions',
            'edit_published_subcompetitions',
        ],
    ];

    $caps['global'] = [
        'manage_pool_results',
        'validate_pool_enrollments',
        'manage_pool_points_tables',
    ];

    return $caps;
}

function pool_de_elias_install_roles() {
    $caps = pool_de_elias_get_capabilities_matrix();

    // Jugador: capacidades básicas.
    add_role(
        'player',
        __( 'Jugador', PDE_TEXTDOMAIN ),
        [
            'read'                   => true,
            'upload_files'           => false,
            'edit_posts'             => false,
            'pool_view_dashboard'    => true,
            'pool_enroll_competition'=> true,
        ]
    );

    // Gestor de competiciones.
    $manager_caps = [ 'read' => true, 'upload_files' => true ];
    foreach ( $caps['competition'] as $cap ) {
        $manager_caps[ $cap ] = true;
    }
    foreach ( $caps['subcompetition'] as $cap ) {
        $manager_caps[ $cap ] = true;
    }
    foreach ( $caps['global'] as $cap ) {
        $manager_caps[ $cap ] = true;
    }

    add_role( 'competition_manager', __( 'Gestor de competiciones', PDE_TEXTDOMAIN ), $manager_caps );

    // Asegurar que administrador y gestor tengan todas.
    $roles_to_update = [ 'administrator', 'competition_manager' ];
    foreach ( $roles_to_update as $role_slug ) {
        $role = get_role( $role_slug );
        if ( ! $role ) {
            continue;
        }

        foreach ( $caps as $group ) {
            foreach ( $group as $cap ) {
                $role->add_cap( $cap );
            }
        }

        $role->add_cap( 'pool_manage_settings' );
    }
}

/**
 * Map meta cap para permisos finos (por ahora delega en capacidades básicas).
 */
function pool_de_elias_map_meta_cap( $caps, $cap, $user_id, $args ) {
    switch ( $cap ) {
        case 'pool_manage_settings':
            $caps = [ 'manage_options' ];
            break;
        case 'pool_enroll_competition':
        case 'pool_view_dashboard':
            $caps = [ 'read' ];
            break;
    }

    return $caps;
}
add_filter( 'map_meta_cap', 'pool_de_elias_map_meta_cap', 10, 4 );

