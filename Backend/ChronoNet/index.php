<!-- index.php -->
<?php
session_start();

// Obtener el mensaje de error, si existe
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';
unset($_SESSION['error_message']); // Limpiar el mensaje de error después de mostrarlo
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="src/icon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="src/icon.ico" type="image/x-icon">
</head>
<body id="login-body">
    <div class="container">
        <form action="login.php" method="post" class="login-form">
        <img src="src/Logo.png" width="200px">
        <h2>Iniciar Sesión</h2>
            <!-- Mostrar mensaje de error -->
            <?php if (!empty($error_message)): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <label for="usuario">Usuario:</label>
            <input type="text" name="usuario" required>
            <label for="contrasena">Contraseña (solo números):</label>
            <input type="password" name="contrasena" pattern="[0-9]+" title="Ingrese solo números" required>
            <button type="submit" class="login-button">Iniciar Sesión</button>
        </form>
    </div>
</body>
</html>
