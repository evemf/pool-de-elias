<?php
// Registro de jugador
function pool_de_elias_register_user() {
    if( isset($_POST['register_player']) ) {
        $username = sanitize_text_field($_POST['username']);
        $password = sanitize_text_field($_POST['password']);
        $level = sanitize_text_field($_POST['level']);
        $sex = sanitize_text_field($_POST['sex']);
        $birthdate = sanitize_text_field($_POST['birthdate']);
        
        // Crear un nuevo usuario
        $user_id = wp_create_user($username, $password, $username . '@pooldeelias.com');
        
        if (is_wp_error($user_id)) {
            echo 'Error al crear el usuario';
        } else {
            // Guardar nivel, sexo y fecha de nacimiento
            update_user_meta($user_id, 'level', $level);
            update_user_meta($user_id, 'sex', $sex);
            update_user_meta($user_id, 'birthdate', $birthdate);
            
            // Asignar competiciones al jugador después de la creación
            pool_de_elias_assign_competitions_to_player($user_id);

            // Redirigir al perfil
            wp_redirect(home_url('/perfil'));
            exit;
        }
    }
}
add_action('init', 'pool_de_elias_register_user');

// Función para el login de jugadores
function pool_de_elias_login_user() {
    if( isset($_POST['login_player']) ) {
        $username = sanitize_text_field($_POST['username']);
        $password = sanitize_text_field($_POST['password']);
        
        $user = wp_authenticate($username, $password);
        if (is_wp_error($user)) {
            echo 'Error de login';
        } else {
            wp_redirect(home_url('/perfil'));
            exit;
        }
    }
}
add_action('init', 'pool_de_elias_login_user');

// Asignar competiciones a un jugador al registrarse según su nivel
function pool_de_elias_assign_competitions_to_player($user_id) {
    // Obtener el nivel del jugador
    $level = get_user_meta($user_id, 'level', true);

    // Obtener las competiciones del nivel del jugador
    $args = array(
        'post_type' => 'competition',
        'posts_per_page' => -1,
        'title' => $level . ' Competición', // Competiciones del mismo nivel
    );
    $competitions = get_posts($args);

    // Asignar competiciones al jugador
    $competition_ids = array();
    foreach ($competitions as $competition) {
        $competition_ids[] = $competition->ID;
    }

    // Guardar las competiciones en los metadatos del usuario
    update_user_meta($user_id, 'competitions', $competition_ids);
}

// Función para mostrar el perfil de un jugador
function pool_de_elias_show_player_profile($user_id) {
    $user_info = get_userdata($user_id);
    $level = get_user_meta($user_id, 'level', true);
    $competitions = get_user_meta($user_id, 'competitions', true);

    $profile = '<h2>Perfil de ' . $user_info->user_login . '</h2>';
    $profile .= '<p><strong>Email:</strong> ' . $user_info->user_email . '</p>';
    $profile .= '<p><strong>Nivel:</strong> ' . $level . '</p>';
    $profile .= '<p><strong>Competiciones:</strong> ' . (empty($competitions) ? 'Ninguna competición registrada.' : implode(', ', $competitions)) . '</p>';
    return $profile;
}
