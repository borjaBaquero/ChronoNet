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

        // Procesar la actualización del usuario si se envió el formulario
        if(isset($_POST['submitForm'])) {
            // Obtener los datos del formulario
            $loginNuevo = $_POST['login'];
            $codigoNuevo = $_POST['codigo'];
            $nombreNuevo = $_POST['nombre'];
            $apellidosNuevo = $_POST['apellidos'];
            $adminBoolNuevo = isset($_POST['adminBool']) ? '1' : '0';

            // Realizar la actualización del usuario en la base de datos
            $queryUpdate = "UPDATE TUsuarios SET Login = ?, Codigo = ?, Nombre = ?, Apellidos = ?, AdminBool = ? WHERE IdUsuario = ?";
            $paramsUpdate = array($loginNuevo, $codigoNuevo, $nombreNuevo, $apellidosNuevo, $adminBoolNuevo, $idUsuario);
            $resultUpdate = sqlsrv_query($conn, $queryUpdate, $paramsUpdate);

            if ($resultUpdate === false) {
                die(print_r(sqlsrv_errors(), true));
            }else{
                //Alerta si el usuario
                echo "<script>alert('Usuario actualizado correctamente.'); window.location.href = 'Usuarios.php';</script>";
                exit();
            }
            // Liberar los recursos
            sqlsrv_free_stmt($resultUpdate);
        }
?>

        <!-- Mostrar el formulario de edición con los datos del usuario -->
        <head>
            <link rel="stylesheet" href="src/style_home.css">
        </head>
        <body>
            <div id="contenedor-form">
            <div id="formulario-edit-user">
            <h2>Editar Usuario</h2>
                <form action="" method="post">
                    <input type="hidden" name="idUsuario" value="<?php echo $idUsuario; ?>">
                    <label for="login">DNI:</label>
                    <input type="text" name="login" value="<?php echo $login; ?>" required>
                    <br><br>
                    <label for="codigo">Código:</label>
                    <input type="text" name="codigo" value="<?php echo $codigo; ?>" required>
                    <br><br>
                    <label for="nombre">Nombre:</label>
                    <input type="text" name="nombre" value="<?php echo $nombre; ?>" required>
                    <br><br>
                    <label for="apellidos">Apellidos:</label>
                    <input type="text" name="apellidos" value="<?php echo $apellidos; ?>" required>
                    <br><br>
                    <label for="adminBool">Administrador:</label>
                    <input type="checkbox" name="adminBool" <?php if($adminBool == '1') echo 'checked'; ?>>
                    <br><br>
                    <button class="botones-form" style="width:69%;" type="submit" name="submitForm">Guardar Cambios</button>
                    <a class="botones-form" style="width:35%; text-decoration:none;" href="Usuarios.php">Volver</a>
                    <br/>
                    <button class="botones-form" style="width:100%;" onclick="goBack()">Restablecer</button>
                </form>
            </div>
            </div>
            <script>
                function goBack() {
                    window.location.href = "Usuarios.php";
                }
            </script>
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
