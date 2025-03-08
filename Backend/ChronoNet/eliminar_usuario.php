<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    // Si no está autenticado, redirigir al formulario de inicio de sesión
    header("Location: index.php");
    exit();
}

// Obtener el nombre de usuario de la sesión
$nombreUsuario = isset($_SESSION['usuario']) ? $_SESSION['usuario']['nombre'] : '';

include 'Conexion.php';

// Verificar si se ha recibido el parámetro 'id' en la URL
if(isset($_GET['id'])) {
    // Obtener el IdUsuario del parámetro 'id'
    $idUsuario = $_GET['id'];

    // Realizar la consulta para obtener la información del usuario a partir del IdUsuario
    $queryUsuario = "SELECT * FROM TUsuarios WHERE IdUsuario = ?";
    $params = array($idUsuario);
    $resultUsuario = sqlsrv_query($conn, $queryUsuario, $params);

    if ($resultUsuario === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Verificar si se encontró el usuario
    if(sqlsrv_has_rows($resultUsuario)) {
        // Obtener los datos del usuario
        $usuario = sqlsrv_fetch_array($resultUsuario, SQLSRV_FETCH_ASSOC);
        $login = $usuario['Login'];
        $codigo = $usuario['Codigo'];
        $nombre = $usuario['Nombre'];
        $apellidos = $usuario['Apellidos'];
        $adminBool = $usuario['AdminBool'];

        // Procesar la eliminación del usuario si se envió la confirmación
        if(isset($_POST['confirmarEliminar'])) {
            // Realizar la eliminación del usuario en la base de datos
            $queryEliminar = "DELETE FROM TUsuarios WHERE IdUsuario = ?";
            $paramsEliminar = array($idUsuario);
            $resultEliminar = sqlsrv_query($conn, $queryEliminar, $paramsEliminar);

            if ($resultEliminar === false) {
                die(print_r(sqlsrv_errors(), true));
            } else {
                // Redirigir a la página de inicio o a donde lo necesites después de eliminar el usuario
                header("Location: Usuarios.php");
                exit();
            }
        }
?>

        <!-- Mostrar el formulario de confirmación de eliminación del usuario -->
        <head>
            <link rel="stylesheet" href="src/style_home.css">
        </head>
        <body>
            <div id="contenedor-form">
            <div id="formulario-edit-user">
            <h2>Eliminar Usuario</h2>
            <p>¿Estás seguro de que deseas eliminar al usuario "<?php echo $nombre . " " . $apellidos; ?>"?</p>
                <form action="" method="post">
                    <input type="hidden" name="idUsuario" value="<?php echo $idUsuario; ?>">
                    <button class="botones-form" type="submit" name="confirmarEliminar">Confirmar</button>
                    <a href="Usuarios.php" class="botones-form">Cancelar</a>
                </form>
            </div>
            </div>
        </body>

<?php
    } else {
        // Si no se encuentra el usuario, mostrar un mensaje de error
        echo "<p>No se encontró el usuario.</p>";
    }

    // Liberar los recursos
    sqlsrv_free_stmt($resultUsuario);
} else {
    // Si no se recibió el parámetro 'id', mostrar un mensaje de error
    echo "<p>Parámetro 'id' no encontrado en la URL.</p>";
}
?>
