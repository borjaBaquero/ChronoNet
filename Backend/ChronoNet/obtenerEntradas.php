<?php
// Obtener el ID del usuario y la fecha de la solicitud AJAX
$userId = $_GET['userId'];
$fecha = $_GET['fecha'];

// Realizar la consulta SQL para obtener la última entrada del usuario en la fecha especificada
// Aquí debes ejecutar tu consulta SQL, reemplazando 'TU_TABLA' con el nombre real de tu tabla de registros
$query = "SELECT TOP 1 * FROM TU_TABLA WHERE UserId = '$userId' AND CONVERT(DATE, Fecha) = '$fecha' ORDER BY Fecha DESC";

// Ejecutar la consulta SQL y obtener el resultado
// Aquí debes ejecutar tu consulta y obtener el resultado, reemplazando esta parte con tu código real
// La siguiente línea es solo un ejemplo de cómo podrías ejecutar la consulta utilizando mysqli
$resultado = $mysqli->query($query);

// Verificar si se encontró algún registro
if ($resultado->num_rows > 0) {
    // Obtener el registro de la base de datos
    $fila = $resultado->fetch_assoc();
    
    // Convertir el resultado a JSON y devolverlo como respuesta
    echo json_encode($fila);
} else {
    // Si no se encuentra ningún registro, devolver una respuesta vacía o un mensaje de error, según sea necesario
    echo "No se encontraron registros para el usuario $userId en la fecha $fecha";
}

// Cerrar la conexión, si es necesario
$mysqli->close();
?>