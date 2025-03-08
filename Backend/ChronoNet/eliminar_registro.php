<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    // Si no está autenticado, redirigir al formulario de inicio de sesión
    header("Location: index.php");
    exit();
}

include 'Conexion.php';

// Verificar si se ha recibido el parámetro 'id' en la URL
if(isset($_GET['id'])) {
    // Obtener el IdRegistro del parámetro 'id'
    $idRegistro = $_GET['id'];

    // Realizar la consulta para obtener la información del registro a partir del IdRegistro
    $queryRegistro = "SELECT * FROM TRegistro WHERE IdRegistro = ?";
    $params = array($idRegistro);
    $resultRegistro = sqlsrv_query($conn, $queryRegistro, $params);

    if ($resultRegistro === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Verificar si se encontró el registro
    if(sqlsrv_has_rows($resultRegistro)) {
        // Obtener los datos del registro
        $registro = sqlsrv_fetch_array($resultRegistro, SQLSRV_FETCH_ASSOC);
        $idUsuario = $registro['IdUsuario'];
        $nombre = $registro['IdRegistro']; // Reemplaza 'Nombre' con el nombre de la columna que almacena el nombre del registro

        // Procesar la eliminación del registro si se envió la confirmación
        if(isset($_POST['confirmarEliminar'])) {
            // Realizar la eliminación del registro en la base de datos
            $queryEliminar = "DELETE FROM TRegistro WHERE IdRegistro = ?";
            $paramsEliminar = array($idRegistro);
            $resultEliminar = sqlsrv_query($conn, $queryEliminar, $paramsEliminar);

            if ($resultEliminar === false) {
                die(print_r(sqlsrv_errors(), true));
            } else {
                // Redirigir a la página de inicio o a donde lo necesites después de eliminar el registro
                header("Location: Registros.php");
                exit();
            }
        }
?>

        <!-- Mostrar el formulario de confirmación de eliminación del registro -->
        <head>
            <link rel="stylesheet" href="src/style_home.css">
        </head>
        <body>
            <div id="contenedor-form">
            <div id="formulario-edit-user">
            <h2>Eliminar Registro</h2>
            <p>¿Estás seguro de que deseas eliminar el registro "<?php echo $nombre; ?>"?</p>
                <form action="" method="post">
                    <input type="hidden" name="idRegistro" value="<?php echo $idRegistro; ?>">
                    <button class="botones-form" type="submit" name="confirmarEliminar">Confirmar</button>
                    <a href="Registros.php" class="botones-form">Cancelar</a>
                </form>
            </div>
            </div>
        </body>

<?php
    } else {
        // Si no se encuentra el registro, mostrar un mensaje de error
        echo "<p>No se encontró el registro.</p>";
    }

    // Liberar los recursos
    sqlsrv_free_stmt($resultRegistro);
} else {
    // Si no se recibió el parámetro 'id', mostrar un mensaje de error
    echo "<p>Parámetro 'id' no encontrado en la URL.</p>";
}
?>
