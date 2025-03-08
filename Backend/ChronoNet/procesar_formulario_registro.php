<?php
include 'Conexion.php';

if(isset($_POST['submitForm'])) {
    // Obtener los datos del formulario
    $idUsuario = $_POST['usuarios'];
    $tipo = $_POST['tipo'];
    $fechaHora = $_POST['fechaHora'];
    $incidencia = $_POST['incidencia'];

    // Formatear la fecha y hora
    $fechaHoraFormateada = date('d-m-Y H:i:s', strtotime($fechaHora));

    // Verificar si ya existe un registro para ese usuario, ese día y del mismo tipo
    $checkQuery = "SELECT COUNT(*) as count FROM TRegistro WHERE IdUsuario = ? AND Tipo = ? AND FechaHora = ?";
    
    // Parámetros para la consulta de verificación
    $checkParams = array($idUsuario, $tipo, $fechaHoraFormateada);

    // Ejecutar la consulta de verificación
    $checkStmt = sqlsrv_query($conn, $checkQuery, $checkParams);

    // Obtener el resultado de la consulta de verificación
    $checkResult = sqlsrv_fetch_array($checkStmt, SQLSRV_FETCH_ASSOC);

    // Verificar si ya existe un registro para ese usuario, ese día y del mismo tipo
    if ($checkResult['count'] > 0) {
        // Ya existe un registro para ese usuario, ese día y del mismo tipo, mostrar mensaje y salir
        echo "<script>alert('Ya existe un registro para este usuario, este día y del mismo tipo. El registro no fue importado.'); window.location.href='Registros.php';</script>";
        exit();
    }

    // Consulta SQL para insertar un nuevo registro
    $insertQuery = "INSERT INTO TRegistro (IdUsuario, Tipo, FechaHora, Incidencia) VALUES (?, ?, ?, ?)";

    // Parámetros para la consulta de inserción
    $insertParams = array($idUsuario, $tipo, $fechaHoraFormateada, $incidencia);

    // Preparar y ejecutar la consulta de inserción
    $insertStmt = sqlsrv_query($conn, $insertQuery, $insertParams);

    // Verificar si la consulta de inserción fue exitosa
    if ($insertStmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Cerrar la conexión
    sqlsrv_close($conn);

    // Redirigir o realizar otras acciones después del éxito
    echo "<script>alert('Registro guardado satisfactoriamente.'); window.location.href='Registros.php';</script>";
    exit();
}
?>
