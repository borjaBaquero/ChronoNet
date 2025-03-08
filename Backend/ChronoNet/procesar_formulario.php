<?php
include 'Conexion.php';

if(isset($_POST['submitForm'])) {
    // Obtener los datos del formulario
    $nuevoUsuario = $_POST['nuevoUsuario'];
    $nuevoCodigo = $_POST['nuevoCodigo'];
    $nuevoNombre = $_POST['nuevoNombre'];
    $nuevoApellidos = $_POST['nuevoApellidos'];
    $nuevoAdministrador = isset($_POST['nuevoAdministrador']) ? 1 : 0; // 1 si está marcado, 0 si no


    $maxLengthUsuario = 8;
    $maxLengthCodigo = 4;

    if (strlen($nuevoUsuario) > $maxLengthUsuario) {
        echo "<script>alert('El campo Dni debe tener como máximo $maxLengthUsuario caracteres.'); window.location.href='Usuarios.php';</script>";
        exit();
    }
    if (strlen($nuevoCodigo) > $maxLengthCodigo) {
        echo "<script>alert('El campo Código debe tener como máximo $maxLengthCodigo caracteres.'); window.location.href='Usuarios.php';</script>";
        exit();
    }
    // Consulta SQL para verificar si el código ya existe
    $codigoExistenteQuery = "SELECT COUNT(*) as count FROM TUsuarios WHERE Codigo = ?";
    $codigoExistenteParams = array($nuevoCodigo);

    //dni verificacion
    $dniExistenteQuery = "SELECT COUNT(*) as count FROM TUsuarios WHERE Login = ?";
    $dniExistenteParams = array($nuevoUsuario);

    // Preparar y ejecutar la consulta
    $codigoExistenteStmt = sqlsrv_query($conn, $codigoExistenteQuery, $codigoExistenteParams);

    // Obtener el resultado de la consulta
    $codigoExistenteResult = sqlsrv_fetch_array($codigoExistenteStmt, SQLSRV_FETCH_ASSOC);

    $dniExistenteStmt = sqlsrv_query($conn, $dniExistenteQuery, $dniExistenteParams);
    $dniExistenteResult = sqlsrv_fetch_array($dniExistenteStmt, SQLSRV_FETCH_ASSOC);

    // Verificar si el código ya existe
    if ($codigoExistenteResult['count'] > 0) {
        // El código ya existe, mostrar mensaje y salir
        echo "<script>alert('El código de usuario ya existe. Usuario no creado.'); window.location.href='Usuarios.php';</script>";
        exit();
    }else if($dniExistenteResult['count'] > 0){
        echo "<script>alert('El dni de usuario ya existe. Usuario no creado.'); window.location.href='Usuarios.php';</script>";
        exit();
    }

    // // Modificar la columna "Codigo" a VARCHAR
    // $alterTableQuery = "ALTER TABLE TUsuarios ALTER COLUMN Codigo VARCHAR(10)";

    // // Ejecutar la consulta de modificación
    // $alterTableStmt = sqlsrv_query($conn, $alterTableQuery);

    // if ($alterTableStmt === false) {
    //     die(print_r(sqlsrv_errors(), true));
    // }

    // Consulta SQL para insertar un nuevo usuario
    $insertQuery = "INSERT INTO TUsuarios (Login, Password, Codigo, Nombre, Apellidos, AdminBool) VALUES (?, ?, ?, ?, ?, ?)";

    // Parámetros para la consulta
    $insertParams = array($nuevoUsuario, 'pass', $nuevoCodigo, $nuevoNombre, $nuevoApellidos, $nuevoAdministrador);

    // Preparar y ejecutar la consulta de inserción
    $insertStmt = sqlsrv_query($conn, $insertQuery, $insertParams);

    // Verificar si la consulta de inserción fue exitosa
    if ($insertStmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Cerrar la conexión
    sqlsrv_close($conn);

    // Redirigir o realizar otras acciones después del éxito
    echo "<script>alert('Usuario creado satisfactoriamente.'); window.location.href='Usuarios.php';</script>";
    exit();
}


?>
