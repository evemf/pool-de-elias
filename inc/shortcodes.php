<?php
/**
 * Shortcodes personalizados para el tema "Pool de Elias"
 */

// Shortcode para el formulario de registro con selector de nivel
function pool_de_elias_register_form_shortcode() {
    ob_start();
    ?>
    <form method="post" action="">
        <input type="text" name="username" placeholder="Nombre de usuario" required>
        <input type="email" name="email" placeholder="Correo electrónico" required>
        <input type="password" name="password" placeholder="Contraseña" required>

        <!-- Selector de nivel -->
        <label for="level">Selecciona tu nivel:</label>
        <select name="level" id="level">
            <option value="Novel" selected>Novel</option>
            <option value="Promesa">Promesa</option>
            <option value="Experto">Experto</option>
            <option value="Máster">Máster</option>
        </select>

        <input type="submit" value="Registrarse">
    </form>
    <?php

    // Procesar el registro si se envía el formulario
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['username']) && !empty($_POST['email']) && !empty($_POST['password'])) {
        $username = sanitize_text_field($_POST['username']);
        $email = sanitize_email($_POST['email']);
        $password = sanitize_text_field($_POST['password']);
        $level = sanitize_text_field($_POST['level']); // Obtener el nivel seleccionado

        // Si no se selecciona un nivel, asignar 'Novel' por defecto
        if (empty($level)) {
            $level = 'Novel';
        }
        
        // Crear usuario
        $userdata = array(
            'user_login'    => $username,
            'user_email'    => $email,
            'user_pass'     => $password,
            'role'          => 'player', // Asignar el rol 'player'
        );
        
        $user_id = wp_insert_user($userdata);
        
        if (!is_wp_error($user_id)) {
            // Asignar el nivel seleccionado al jugador
            update_user_meta($user_id, 'level', $level);
            
            // Redirigir al perfil después del registro
            wp_redirect(home_url('/profile'));
            exit;
        } else {
            echo '<p>Hubo un error al registrar al usuario. Por favor, inténtalo nuevamente.</p>';
        }
    }
    
    return ob_get_clean();
}
add_shortcode('pool_de_elias_register_form', 'pool_de_elias_register_form_shortcode');

// Shortcode para el formulario de login
function pool_de_elias_login_form_shortcode() {
    ob_start();
    ?>
    <form method="post" action="">
        <input type="text" name="username" placeholder="Nombre de usuario" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <input type="submit" value="Iniciar sesión">
    </form>
    <?php

    // Procesar el login si se envía el formulario
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['username']) && !empty($_POST['password'])) {
        $username = sanitize_text_field($_POST['username']);
        $password = sanitize_text_field($_POST['password']);
        
        $creds = array(
            'user_login'    => $username,
            'user_password' => $password,
            'remember'      => true,
        );
        
        $user = wp_signon($creds, false);
        
        if (is_wp_error($user)) {
            echo '<p>Credenciales incorrectas, por favor intenta de nuevo.</p>';
        } else {
            // Redirigir al perfil después de iniciar sesión
            wp_redirect(home_url('/profile'));
            exit;
        }
    }
    
    return ob_get_clean();
}
add_shortcode('pool_de_elias_login_form', 'pool_de_elias_login_form_shortcode');

// Shortcode para mostrar el perfil del jugador
function pool_de_elias_profile_shortcode() {
    ob_start();
    $current_user = wp_get_current_user();

    if ( $current_user->ID == 0 ) {
        return '<p>No estás registrado ni has iniciado sesión.</p>';
    }

    // Obtener información del jugador
    $level = get_user_meta($current_user->ID, 'level', true);
    $competitions = get_user_meta($current_user->ID, 'competitions', true);
    
    // Mostrar información del perfil
    ?>
    <h2>Perfil de <?php echo esc_html($current_user->user_login); ?></h2>
    <p><strong>Email:</strong> <?php echo esc_html($current_user->user_email); ?></p>
    <p><strong>Nivel:</strong> <?php echo esc_html($level); ?></p>
    <p><strong>Competiciones:</strong> <?php echo $competitions ? implode(', ', $competitions) : 'Ninguna competición registrada.'; ?></p>
    <?php

    return ob_get_clean();
}
add_shortcode('pool_de_elias_profile', 'pool_de_elias_profile_shortcode');
?>
