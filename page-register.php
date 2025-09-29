<?php
/* Template Name: Register Player */
get_header();

// Comprobar si el usuario ya está logueado
if ( is_user_logged_in() ) {
    echo 'Ya estás registrado.';
} else {
    // Verificar si el formulario de registro ha sido enviado
    if ( isset( $_POST['register_player'] ) ) {
        // Recoger y sanitizar los datos del formulario
        $username  = sanitize_text_field( $_POST['username'] );
        $password  = sanitize_text_field( $_POST['password'] );
        $level     = sanitize_text_field( $_POST['level'] );
        $sex       = sanitize_text_field( $_POST['sex'] );
        $birthdate = sanitize_text_field( $_POST['birthdate'] );

        // Verificar si el username ya existe
        if ( username_exists( $username ) ) {
            echo '<p style="color: red;">Este nombre de usuario ya está en uso. Elige otro.</p>';
        } else {
            // Crear el usuario
            $user_id = wp_create_user( $username, $password, $username . '@pooldeelias.com' );

            if ( is_wp_error( $user_id ) ) {
                // Si hubo un error al crear el usuario
                echo '<p style="color: red;">Error al crear el usuario. Inténtalo nuevamente.</p>';
            } else {
                // Asignar el nivel, sexo y fecha de nacimiento como metadatos
                update_user_meta( $user_id, 'level', $level );
                update_user_meta( $user_id, 'sex', $sex );
                update_user_meta( $user_id, 'birthdate', $birthdate );

                // Redirigir a la página de perfil o dashboard del usuario
                wp_redirect( home_url( '/dashboard' ) );
                exit;
            }
        }
    }

    // Mostrar formulario de registro
    ?>
    <form action="" method="POST">
        <label for="username">Username:</label>
        <input type="text" name="username" placeholder="Username" required><br>

        <label for="password">Password:</label>
        <input type="password" name="password" placeholder="Password" required><br>

        <label for="level">Nivel:</label>
        <select name="level">
            <option value="novel" selected>Novel</option>
            <option value="promesa">Promesa</option>
            <option value="experto">Experto</option>
            <option value="master">Máster</option>
        </select><br>

        <label for="sex">Sexo:</label>
        <select name="sex">
            <option value="male">Male</option>
            <option value="female">Female</option>
        </select><br>

        <label for="birthdate">Fecha de Nacimiento:</label>
        <input type="date" name="birthdate" required><br>

        <input type="submit" name="register_player" value="Registrar">
    </form>
    <?php
}

get_footer();
?>
