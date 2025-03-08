<?php
// Cargar las clases de PHPExcel
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

include 'Conexion.php';

// Crear un nuevo objeto Spreadsheet (libro de cálculo)
$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load("src/Registro-jornada.xlsx");

// Obtener la hoja de trabajo activa (normalmente la primera hoja)
$sheet = $spreadsheet->getActiveSheet();

// Obtener los datos del formulario
$usuarioSeleccionado = $_POST['usuarios'];
$mesSeleccionado = $_POST['meses'];
$anioSeleccionado = $_POST['anios'];
$numeroMes = obtenerNumeroMes($mesSeleccionado);

//RELLENAR CAMPOS FIJOS
$sheet->setCellValue('C3', "MANCHEGOS LABORATORIOS DE PROTESIS DENTAL S.L");

$sheet->setCellValue('B4', "B13389531");

$fechaActual = new DateTime();
$sheet->setCellValue('G6', $fechaActual->format('d/m/Y'));

// Obtener el primer día del mes y el último día del mes
if ($numeroMes < 10) {
    $numeroMes = '0' . $numeroMes; // Agregar un cero delante del número del mes si es menor que 10
}
$primerDiaMes = new DateTime($anioSeleccionado . '-' . $numeroMes . '-01');

$primerDiaMes = new DateTime($anioSeleccionado . '-' . $numeroMes . '-01');
$ultimoDiaMes = new DateTime($primerDiaMes->format('Y-m-t'));

// Obtener el día del mes y el último día del mes
$primerDia = $primerDiaMes->format('d');
$ultimoDia = $ultimoDiaMes->format('d');

// Establecer el rango de fechas en la celda C6
$sheet->setCellValue('C6', $primerDia . ' al ' . $ultimoDia . ' de ' . $mesSeleccionado . ' de ' . $anioSeleccionado);

//RELLENAR APARTADO DE USUARIOS
$queryUsuarios = "SELECT * FROM TUsuarios WHERE IdUsuario = " . $usuarioSeleccionado;
$resultUsuarios = sqlsrv_query($conn, $queryUsuarios);

if ($resultUsuarios === false) {
    die(print_r(sqlsrv_errors(), true));
}

while ($rowUsuarios = sqlsrv_fetch_array($resultUsuarios, SQLSRV_FETCH_ASSOC)) {
    $nombreCompleto = $rowUsuarios['Nombre'] . " " . $rowUsuarios['Apellidos'];
    $sheet->setCellValue('G3', $nombreCompleto);
    $sheet->setCellValue('G4', $rowUsuarios['Login']);
}


//RELLENAR REGISTROS

date_default_timezone_set('Europe/Madrid');

// Función para obtener el número de mes a partir del nombre del mes
function obtenerNumeroMes($messeleccionado) {
    switch ($messeleccionado) {
        case 'Enero':
            return 1;
        case 'Febrero':
            return 2;
        case 'Marzo':
            return 3;
        case 'Abril':
            return 4;
        case 'Mayo':
            return 5;
        case 'Junio':
            return 6;
        case 'Julio':
            return 7;
        case 'Agosto':
            return 8;
        case 'Septiembre':
            return 9;
        case 'Octubre':
            return 10;
        case 'Noviembre':
            return 11;
        case 'Diciembre':
            return 12;
        default:
            return "Mes no válido";
    }
}


// Registro de entrada
function registroEntrada($numero_mes, $usuarioSeleccionado, $anioSeleccionado, $conn, $sheet){
    $queryRegistro = "SELECT * FROM TRegistro WHERE IdUsuario = '$usuarioSeleccionado' AND YEAR(FechaHora) = $anioSeleccionado AND MONTH(FechaHora) = $numero_mes AND Tipo = 0";

    $resultado = sqlsrv_query($conn, $queryRegistro);

    if ($resultado === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    while ($rowRegistro = sqlsrv_fetch_array($resultado, SQLSRV_FETCH_ASSOC)) {
        $fechaHora = $rowRegistro['FechaHora']->format('Y-m-d H:i:s'); // Formatear la fecha y hora
        $dia = (int) $rowRegistro['FechaHora']->format('d'); // Obtener el día del mes como un número entero
        $dia += 8; // Sumar 8 días
        $hora = $rowRegistro['FechaHora']->format('H:i:s'); // Obtener la hora formateada

        // Escribir los datos en las celdas correspondientes
        $sheet->setCellValue("B{$dia}", $hora);
    }
}

// Registro de pausa
function registroPausa($numero_mes, $usuarioSeleccionado, $anioSeleccionado, $conn, $sheet){
    $queryRegistro = "SELECT * FROM TRegistro WHERE IdUsuario = '$usuarioSeleccionado' AND YEAR(FechaHora) = $anioSeleccionado AND MONTH(FechaHora) = $numero_mes AND Tipo = 2";

    $resultado = sqlsrv_query($conn, $queryRegistro);

    if ($resultado === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    while ($rowRegistro = sqlsrv_fetch_array($resultado, SQLSRV_FETCH_ASSOC)) {
        $fechaHora = $rowRegistro['FechaHora']->format('Y-m-d H:i:s'); // Formatear la fecha y hora
        $dia = (int) $rowRegistro['FechaHora']->format('d'); // Obtener el día del mes como un número entero
        $dia += 8; // Sumar 8 días
        $hora = $rowRegistro['FechaHora']->format('H:i:s'); // Obtener la hora formateada
        $incidencia = $rowRegistro['Incidencia'];

        // Escribir los datos en las celdas correspondientes
        $sheet->setCellValue("C{$dia}", $hora);
        $sheet->setCellValue("D{$dia}", $incidencia); // Columna D para incidencias
    }
}

// Registro de reanudación
function registroReanuda($numero_mes, $usuarioSeleccionado, $anioSeleccionado, $conn, $sheet){
    $queryRegistro = "SELECT * FROM TRegistro WHERE IdUsuario = '$usuarioSeleccionado' AND YEAR(FechaHora) = $anioSeleccionado AND MONTH(FechaHora) = $numero_mes AND Tipo = 3";

    $resultado = sqlsrv_query($conn, $queryRegistro);

    if ($resultado === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    while ($rowRegistro = sqlsrv_fetch_array($resultado, SQLSRV_FETCH_ASSOC)) {
        $fechaHora = $rowRegistro['FechaHora']->format('Y-m-d H:i:s'); // Formatear la fecha y hora
        $dia = (int) $rowRegistro['FechaHora']->format('d'); // Obtener el día del mes como un número entero
        $dia += 8; // Sumar 8 días
        $hora = $rowRegistro['FechaHora']->format('H:i:s'); // Obtener la hora formateada

        // Escribir los datos en las celdas correspondientes
        $sheet->setCellValue("E{$dia}", $hora);
    }
}

// Registro de salida
function registroSalida($numero_mes, $usuarioSeleccionado, $anioSeleccionado, $conn, $sheet){
    $queryRegistro = "SELECT * FROM TRegistro WHERE IdUsuario = '$usuarioSeleccionado' AND YEAR(FechaHora) = $anioSeleccionado AND MONTH(FechaHora) = $numero_mes AND Tipo = 1";

    $resultado = sqlsrv_query($conn, $queryRegistro);

    if ($resultado === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    while ($rowRegistro = sqlsrv_fetch_array($resultado, SQLSRV_FETCH_ASSOC)) {
        $fechaHora = $rowRegistro['FechaHora']->format('Y-m-d H:i:s'); // Formatear la fecha y hora
        $dia = (int) $rowRegistro['FechaHora']->format('d'); // Obtener el día del mes como un número entero
        $dia += 8; // Sumar 8 días
        $hora = $rowRegistro['FechaHora']->format('H:i:s'); // Obtener la hora formateada

        // Escribir los datos en las celdas correspondientes
        $sheet->setCellValue("F{$dia}", $hora);
    }
}


$numero_mes = obtenerNumeroMes($mesSeleccionado);

// Lógica para registrar la entrada, pausa, reanudación y salida
registroEntrada($numero_mes, $usuarioSeleccionado, $anioSeleccionado, $conn, $sheet);
registroPausa($numero_mes, $usuarioSeleccionado, $anioSeleccionado, $conn, $sheet);
registroReanuda($numero_mes, $usuarioSeleccionado, $anioSeleccionado, $conn, $sheet);
registroSalida($numero_mes, $usuarioSeleccionado, $anioSeleccionado, $conn, $sheet);

$total_horas = 0;
for ($i = 9; $i <= 42; $i++) {
    $entrada = $sheet->getCell('B' . $i)->getValue();
    $salida = $sheet->getCell('F' . $i)->getValue();
    $pausa = $sheet->getCell('C' . $i)->getValue();
    $reanudar = $sheet->getCell('E' . $i)->getValue();

    if ($entrada != "" && $salida != "") {
        // Convertir entrada y salida a objetos DateTime
        $entrada_dt = DateTime::createFromFormat('H:i:s', $entrada);
        $salida_dt = DateTime::createFromFormat('H:i:s', $salida);
        
        // Calcular la diferencia en segundos
        $diferencia_segundos = $salida_dt->getTimestamp() - $entrada_dt->getTimestamp();
        
        // Si pausa y reanudar no están vacíos, calcular su diferencia
        if ($pausa != "" && $reanudar != "") {
            // Convertir pausa y reanudar a objetos DateTime
            $pausa_dt = DateTime::createFromFormat('H:i:s', $pausa);
            $reanudar_dt = DateTime::createFromFormat('H:i:s', $reanudar);
            
            // Calcular la cantidad de segundos entre pausa y reanudar
            $segundos_pausa = $reanudar_dt->getTimestamp() - $pausa_dt->getTimestamp();
            
            // Restar la cantidad de segundos de pausa y reanudar de la diferencia total
            $diferencia_segundos -= $segundos_pausa;
        }
        
        // Sumar la diferencia en segundos al total de horas
        $total_horas += $diferencia_segundos;
        
        // Convertir la diferencia en segundos a horas, minutos y segundos
        $horas = floor($diferencia_segundos / 3600);
        $minutos = floor(($diferencia_segundos % 3600) / 60);
        $segundos = $diferencia_segundos % 60;
        
        // Formatear la diferencia
        $diferencia_formateada = sprintf('%02d:%02d:%02d', $horas, $minutos, $segundos);

        $sheet->setCellValue("G" . $i, $diferencia_formateada);
    }
}

// Convertir el total de horas en segundos a horas, minutos y segundos
$horas_total = floor($total_horas / 3600);
$minutos_total = floor(($total_horas % 3600) / 60);
$segundos_total = $total_horas % 60;

// Formatear el total de horas
$total_formateado = sprintf('%02d:%02d:%02d', $horas_total, $minutos_total, $segundos_total);

// Si las horas totales superan las 24, ajustamos el formato
if ($horas_total >= 24) {
    $dias = floor($horas_total / 24);
    $horas_total = $horas_total % 24;
    $total_formateado = sprintf('%02d días %02d:%02d:%02d', $dias, $horas_total, $minutos_total, $segundos_total);
}

$sheet->setCellValue("C43", $total_formateado);

// Guardar el archivo como un nuevo documento
$writer = new Xlsx($spreadsheet);
$filePath = 'src/Registro-jornada-modificado_' . $nombreCompleto . '_' . $mesSeleccionado . '_' . $anioSeleccionado . '.xlsx';

// // Establecer los permisos adecuados para el directorio y el archivo
// $dirPath = dirname($filePath);
// if (!file_exists($dirPath)) {
//     // Si el directorio no existe, intenta crearlo con permisos 0777
//     mkdir($dirPath, 0777, true);
// } else {
//     // Si el directorio ya existe, asegúrate de que tenga permisos 0777
//     chmod($dirPath, 0777);
// }

// Guardar el archivo
$writer->save($filePath);

// Cambiar los permisos del archivo generado
chmod($filePath, 0777);

// Definir las cabeceras para forzar la descarga
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
header('Content-Length: ' . filesize($filePath));

// Leer el archivo y enviar su contenido al navegador
readfile($filePath);

// Finalizar el script después de la descarga
exit;

?>
