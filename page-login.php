<?php
/* Template Name: Login Player */
get_header();

// Comprobar si el usuario ya está logueado
if ( is_user_logged_in() ) {
    echo '<p>Ya estás logueado. <a href="' . home_url( '/dashboard' ) . '">Ir al Dashboard</a></p>';
} else {
    // Verificar si el formulario de login ha sido enviado
    if ( isset( $_POST['login_player'] ) ) {
        // Recoger las credenciales
        $username = sanitize_text_field( $_POST['username'] );
        $password = sanitize_text_field( $_POST['password'] );

        // Intentar autenticar al usuario
        $user = wp_signon( array(
            'user_login'    => $username,
            'user_password' => $password,
            'remember'      => true
        ) );

        // Si hay un error al loguearse
        if ( is_wp_error( $user ) ) {
            echo '<p style="color: red;">' . $user->get_error_message() . '</p>';
        } else {
            // Redirigir al Dashboard tras iniciar sesión con éxito
            wp_redirect( home_url( '/dashboard' ) );
            exit;
        }
    }

    // Mostrar formulario de login
    ?>
    <form action="" method="POST">
        <label for="username">Username:</label>
        <input type="text" name="username" placeholder="Username" required><br>

        <label for="password">Password:</label>
        <input type="password" name="password" placeholder="Password" required><br>

        <input type="submit" name="login_player" value="Login">
    </form>
    <?php
}

get_footer();
?>
