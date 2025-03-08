<?php

$serverName = "OrdenadorBorja"; // El nombre de tu servidor SQL Server
$connectionOptions = array(
    "Database" => "Sadyfi",
    "CharacterSet" => "UTF-8"
);

// Establecer la conexión con autenticación de Windows (no es necesario Uid ni PWD)
$conn = sqlsrv_connect($serverName, $connectionOptions);

if (!$conn) {
    // En caso de error en la conexión
    die(print_r(sqlsrv_errors(), true));
}

?>