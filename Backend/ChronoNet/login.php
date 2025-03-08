
<?php
session_start();

include 'Conexion.php';

// Verificar si se enviaron datos por POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['usuario']) && isset($_POST['contrasena'])) {
    // Obtener datos del formulario
    $usuario = trim($_POST['usuario']);
    $contrasena = trim($_POST['contrasena']);

    // Validar datos
    if (empty($usuario) || empty($contrasena)) {
        $_SESSION['error_message'] = "Usuario o contraseña vacíos";
        header("Location: index.php");
        exit();
    }

    // Establecer la conexión
    $conn = sqlsrv_connect($serverName, $connectionOptions);

    if (!$conn) {
        // En caso de error en la conexión
        die(print_r(sqlsrv_errors(), true));
    }

    // Realizar consulta para verificar la existencia del usuario y la contraseña
    $query = "SELECT * FROM TUsuarios WHERE Login=? AND Codigo=?";
    $params = array($usuario, $contrasena);
    $result = sqlsrv_query($conn, $query, $params);

    if ($result === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Procesar resultados
    $data = array();
    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
        $data[] = $row;
    }

    // Cerrar la conexión
    sqlsrv_close($conn);

    // Verificar si las credenciales son válidas
    if (count($data) > 0) {
        // Inicio de sesión exitoso, almacenar información del usuario en la sesión
        $_SESSION['usuario'] = array(
            'nombre' => $usuario,
            // Otros datos del usuario si es necesario
        );

        header("Location: Usuarios.php");
        exit();
    } else {
        // Credenciales inválidas, mostrar un mensaje de error
        $_SESSION['error_message'] = "Credenciales inválidas";
        header("Location: index.php");
        exit();
    }
} else {
    // Si no se enviaron datos por POST, volver al login
    header("Location: index.php");
    exit();
}
?>
