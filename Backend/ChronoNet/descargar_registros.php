<?php
set_time_limit(3000);

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

if (isset($_POST['descargar'])) {
    // Incluir la librería de PhpSpreadsheet
    require 'vendor/autoload.php';

    include 'Conexion.php';

// Lógica para obtener registros filtrados (adaptar según tus necesidades)
$filtroNombreCompleto = $_POST['filtroNombreCompleto'] ?? '';
$filtroTipo = $_POST['filtroTipo'] ?? '';
$filtroFechaHora = $_POST['filtroFechaHora'] ?? '';
$filtroFechaHoraFinal = $_POST['filtroFechaHoraFinal'] ?? '';
$filtroIncidencia = $_POST['filtroIncidencia'] ?? '';


// Construir la consulta SQL con los filtros
$query = "SELECT r.IdRegistro, u.Nombre, u.Apellidos, r.Tipo, r.FechaHora, r.Incidencia FROM TRegistro r JOIN TUsuarios u ON u.IdUsuario = r.IdUsuario WHERE 1 = 1";

if (!empty($filtroNombreCompleto)) {
    $query .= " AND (u.Nombre + ' '+ u.Apellidos) LIKE '%$filtroNombreCompleto%'";
}

if (!empty($filtroTipo)) {
    // Convertir el filtro de tipo a minúsculas y eliminar los espacios adicionales
    $filtroTipo = strtolower(trim($filtroTipo));
    
    // Convertir el filtro de tipo de texto a un número entero usando un switch
    switch ($filtroTipo) {
        case 'entrada':
            $filtroTipoNumero = 0;
            break;
        case 'salida':
            $filtroTipoNumero = 1;
            break;
        case 'pausa':
            $filtroTipoNumero = 2;
            break;
        case 'reanudar':
            $filtroTipoNumero = 3;
            break;
        default:
            // Si el valor del filtro de tipo no coincide con ninguna opción válida, mostrar un mensaje de error o manejarlo según sea necesario
            echo "El filtro de tipo no es válido.";
            // También podrías ajustar el comportamiento aquí según tus necesidades
            exit(); // Salir del script en caso de un valor no válido
    }
    
    // Agregar el filtro de tipo convertido a número entero a la consulta SQL
    $query .= " AND r.Tipo = $filtroTipoNumero";
}

    if (!empty($filtroFechaHora) && !empty($filtroFechaHoraFinal)) {
        // Ambos filtros de fecha están llenos, agregar condiciones de rango a la consulta
        $filtroFechaHora = date('Y-m-d', strtotime($filtroFechaHora));
        $filtroFechaHoraFinal = date('Y-m-d', strtotime($filtroFechaHoraFinal));
        $query .= " AND CONVERT(DATE, r.FechaHora) BETWEEN '$filtroFechaHora' AND '$filtroFechaHoraFinal'";
    } else if (!empty($filtroFechaHora)) {
        // Solo el filtro de fecha inicial está lleno, comparar con un >=
        $filtroFechaHora = date('Y-m-d', strtotime($filtroFechaHora));
        $query .= " AND CONVERT(DATE, r.FechaHora) = '$filtroFechaHora'";
    } else if (!empty($filtroFechaHoraFinal)) {
        // Solo el filtro de fecha final está lleno, comparar con un <=
        $filtroFechaHoraFinal = date('Y-m-d', strtotime($filtroFechaHoraFinal));
        $query .= " AND CONVERT(DATE, r.FechaHora) = '$filtroFechaHoraFinal'";
    }

    if (!empty($filtroIncidencia)) {
        // Verificar si el filtro de incidencia es "SI" o "NO" y ajustar la consulta SQL en consecuencia
        if (strtolower($filtroIncidencia) === 'si') {
            $query .= " AND r.Incidencia = 'SI'";
        } elseif (strtolower($filtroIncidencia) === 'no') {
            $query .= " AND r.Incidencia = 'NO'";
        } else {
            // El valor del filtro de incidencia no es válido, mostrar un mensaje de error o manejarlo según sea necesario
            echo "El filtro de incidencia no es válido.";
            // También podrías ajustar el comportamiento aquí según tus necesidades
        }
    }

    // echo $query;
    // die;

    $result = sqlsrv_query($conn, $query);
    if ($result === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Guardar la fecha actual
    $fechaActual = date('Y-m-d');

    // Crear un nuevo libro de Excel
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Encabezados
    $headers = ['ID Registro', 'Nombre', 'Apellidos', 'Tipo', 'Fecha y Hora', 'Incidencia', 'Horas'];
    $column = 'A';

    foreach ($headers as $header) {
        $sheet->setCellValue($column . '1', $header);
        $column++;
    }

    // Llenar el archivo Excel con los registros obtenidos
    $row = 2;
    while ($registro = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {

        $tipo = $registro['Tipo'];
        $tipoTexto = '';
        switch ($tipo) {
            case 0:
                $tipoTexto = 'Entrada';
                break;
            case 1:
                $tipoTexto = 'Salida';
                break;
            case 2:
                $tipoTexto = 'Pausa';
                break;
            case 3:
                $tipoTexto = 'Reanudar';
                break;
        }

        $sheet->setCellValue('A' . $row, $registro['IdRegistro']);
        $sheet->setCellValue('B' . $row, $registro['Nombre']);
        $sheet->setCellValue('C' . $row, $registro['Apellidos']);
        $sheet->setCellValue('D' . $row, $tipoTexto);
        $sheet->setCellValue('E' . $row, $registro['FechaHora']->format('Y-m-d H:i:s'));
        $sheet->setCellValue('F' . $row, $registro['Incidencia']);

        
        $queryHorasTrabajadas = "SELECT r.IdRegistro, r.IdUsuario, (u.Nombre + ' '+ u.Apellidos) AS nombrecompleto, r.Tipo, r.FechaHora, r.Incidencia 
        FROM TRegistro r 
        JOIN TUsuarios u ON u.IdUsuario = r.IdUsuario";

if (!empty($filtroNombreCompleto)) {
    $queryHorasTrabajadas .= " AND (u.Nombre + ' '+ u.Apellidos) LIKE '%$filtroNombreCompleto%'";
}

if (!empty($filtroTipo)) {
    // Convertir el filtro de tipo a minúsculas y eliminar los espacios adicionales
    $filtroTipo = strtolower(trim($filtroTipo));
    
    // Convertir el filtro de tipo de texto a un número entero usando un switch
    switch ($filtroTipo) {
        case 'entrada':
            $filtroTipoNumero = 0;
            break;
        case 'salida':
            $filtroTipoNumero = 1;
            break;
        case 'pausa':
            $filtroTipoNumero = 2;
            break;
        case 'reanudar':
            $filtroTipoNumero = 3;
            break;
        default:
            // Si el valor del filtro de tipo no coincide con ninguna opción válida, mostrar un mensaje de error o manejarlo según sea necesario
            echo "El filtro de tipo no es válido.";
            // También podrías ajustar el comportamiento aquí según tus necesidades
            exit(); // Salir del script en caso de un valor no válido
    }
    
    // Agregar el filtro de tipo convertido a número entero a la consulta SQL
    $queryHorasTrabajadas .= " AND r.Tipo = $filtroTipoNumero";
}

    if (!empty($filtroFechaHora) && !empty($filtroFechaHoraFinal)) {
        // Ambos filtros de fecha están llenos, agregar condiciones de rango a la consulta
        $filtroFechaHora = date('Y-m-d', strtotime($filtroFechaHora));
        $filtroFechaHoraFinal = date('Y-m-d', strtotime($filtroFechaHoraFinal));
        $queryHorasTrabajadas .= " AND CONVERT(DATE, r.FechaHora) BETWEEN '$filtroFechaHora' AND '$filtroFechaHoraFinal'";
    } else if (!empty($filtroFechaHora)) {
        // Solo el filtro de fecha inicial está lleno, comparar con un >=
        $filtroFechaHora = date('Y-m-d', strtotime($filtroFechaHora));
        $queryHorasTrabajadas .= " AND CONVERT(DATE, r.FechaHora) = '$filtroFechaHora'";
    } else if (!empty($filtroFechaHoraFinal)) {
        // Solo el filtro de fecha final está lleno, comparar con un <=
        $filtroFechaHoraFinal = date('Y-m-d', strtotime($filtroFechaHoraFinal));
        $queryHorasTrabajadas .= " AND CONVERT(DATE, r.FechaHora) = '$filtroFechaHoraFinal'";
    }

    if (!empty($filtroIncidencia)) {
        // Verificar si el filtro de incidencia es "SI" o "NO" y ajustar la consulta SQL en consecuencia
        if (strtolower($filtroIncidencia) === 'si') {
            $queryHorasTrabajadas .= " AND r.Incidencia = 'SI'";
        } elseif (strtolower($filtroIncidencia) === 'no') {
            $queryHorasTrabajadas .= " AND r.Incidencia = 'NO'";
        } else {
            // El valor del filtro de incidencia no es válido, mostrar un mensaje de error o manejarlo según sea necesario
            echo "El filtro de incidencia no es válido.";
            // También podrías ajustar el comportamiento aquí según tus necesidades
        }
    }

        $resultHoras = sqlsrv_query($conn, $queryHorasTrabajadas);

        if ($resultHoras === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        $horasPausa = 0;
        $minutosPausa = 0;

        $totalHorasAcumuladas = 0;
        $totalMinutosAcumulados = 0;

        while ($rowReg = sqlsrv_fetch_array($resultHoras, SQLSRV_FETCH_ASSOC)) {
            // Utilizar el ID del usuario como el valor del option
            $idRegistroHoras = $rowReg['IdRegistro'];
            $idUsuarioHoras = $rowReg['IdUsuario'];
            $tipo = $rowReg['Tipo'];
            $fechaHoraSalida = $rowReg['FechaHora'];

            if ($tipo == 1) {
                // Obtener la fecha de la salida actual
                $fechaSalida = $fechaHoraSalida->format('Y-m-d H:i:s.u');
                $fechaEntradaFunc = obtenerHoraEntrada($idUsuarioHoras, $fechaSalida, $conn);
                $fechaPausaFunc = obtenerHoraPausa($idUsuarioHoras, $fechaSalida, $conn);
                $fechaReanudaFunc = obtenerHoraReanudar($idUsuarioHoras, $fechaSalida, $conn);

                        // Extraer solo las horas de la fecha y hora de entrada
                        if ($fechaEntradaFunc !== null) {
                            $horaEntrada = substr($fechaEntradaFunc, 11, 8);
                        } else {
                            $horaEntrada = ""; // O asignar un valor predeterminado
                        }
        
                        if ($fechaPausaFunc !== null) {
                            $horaPausa = substr($fechaPausaFunc, 11, 8);
                        } else {
                            $horaPausa = ""; // O asignar un valor predeterminado
                        }
        
                        if ($fechaReanudaFunc !== null) {
                            $horaReanuda = substr($fechaReanudaFunc, 11, 8);
                        } else {
                            $horaReanuda = ""; // O asignar un valor predeterminado
                        }
        
                        if ($fechaSalida !== null) {
                            $horaSalida = substr($fechaSalida, 11, 8);
                        } else {
                            $horaSalida = ""; // O asignar un valor predeterminado
                        }

                        // Obtener la fecha y hora formateada
                        $horaEntradaDt = formatearStringHoras($horaEntrada);
                        $horaSalidaDt = formatearStringHoras($horaSalida);
                        $horaPausaDt = formatearStringHoras($horaPausa);
                        $horaReanudaDt = formatearStringHoras($horaReanuda);

                        // Verificar si ambas fechas están en el formato correcto
                        if ($horaEntradaDt !== false && $horaSalidaDt !== false) {
                            // Crear objetos DateTime para las fechas de entrada y salida
                            $fechaHoraEntrada_dt = new DateTime($fechaEntradaFunc);
                            $fechaHoraSalida_dt = new DateTime($fechaSalida);

                            // Calcular la diferencia entre las fechas de entrada y salida
                            $diferencia = $fechaHoraSalida_dt->diff($fechaHoraEntrada_dt);

                            // Obtener la diferencia en horas y minutos
                            $horasJornada = $diferencia->format('%h');
                            $minutosJornada = $diferencia->format('%i');

                        }

                        // Verificar si ambas fechas están en el formato correcto
                        if ($horaPausaDt !== false && $horaReanudaDt !== false) {
                            // Crear objetos DateTime para las fechas de pausa y reanudación
                            $fechaPausaFunc_dt = new DateTime($fechaPausaFunc);
                            $fechaReanudaFunc_dt = new DateTime($fechaReanudaFunc);

                            // Calcular la diferencia entre las fechas de pausa y reanudación
                            $diferenciaPausa = $fechaReanudaFunc_dt->diff($fechaPausaFunc_dt);

                            // Obtener la diferencia en horas y minutos
                            $horasPausa = $diferenciaPausa->format('%h');
                            $minutosPausa = $diferenciaPausa->format('%i');

                            $totalHoras = $horasJornada - $horasPausa;
                            $totalMinutos = $minutosJornada - $minutosPausa;
        
                        }else{
                            $totalHoras = $horasJornada;
                            $totalMinutos = $minutosJornada;
                        }

                        // Ajustar los minutos si son negativos
                        if ($totalMinutos < 0) {
                            $totalHoras--;
                            $totalMinutos += 60;
                        }

                        // Formatear los valores de horas y minutos con dos dígitos
                        $totalHorasFormateadas = sprintf('%02d', $totalHoras);
                        $totalMinutosFormateados = sprintf('%02d', $totalMinutos);

                        $totalFormateado = $totalHorasFormateadas . ":" . $totalMinutosFormateados;

                        if($registro['IdRegistro'] == $idRegistroHoras){
                            $sheet->setCellValue('G' . $row, $totalFormateado);
                        }

                        $totalHorasAcumuladas += $totalHoras;
                        $totalMinutosAcumulados += $totalMinutos;
            }

        }

        $row++;
    }

    // Asegurarse de que los minutos no superen 60, sumándolos a las horas
    $totalHorasAcumuladas += intval($totalMinutosAcumulados / 60);
    $totalMinutosAcumulados %= 60;
    

    // Formatear los valores de horas y minutos acumulados con dos dígitos
    $totalHorasFormateadasAcumuladas = sprintf('%02d', $totalHorasAcumuladas);
    $totalMinutosFormateadosAcumulados = sprintf('%02d', $totalMinutosAcumulados);

    // Concatenar horas y minutos acumulados en una cadena formateada
    $totalFormateadoAcumulado = $totalHorasFormateadasAcumuladas . ":" . $totalMinutosFormateadosAcumulados;

    $row = $row + 2;

    //Asignar el total acumulado a la celda correspondiente en tu hoja de cálculo
    $sheet->setCellValue('A'. $row, "TOTAL HORAS: ");
    $sheet->setCellValue('B'. $row, $totalFormateadoAcumulado);
    

    $filename = "registros_$fechaActual.xlsx";

    // Configurar encabezados para la descarga
    header('Content-Type: application/vnd.ms-excel');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header('Cache-Control: max-age=0');

    // Enviar el archivo al cliente
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');

    // Finalizar ejecución
    exit();
}

function formatearStringHoras($hora) {
    // Separar la cadena de hora en sus componentes (horas, minutos, segundos)
    $componentesHora = explode(':', $hora);

    // Verificar si se obtuvieron los componentes correctamente
    if (count($componentesHora) === 3) {
        // Crear un objeto DateTime con los componentes
        $hora_dt = new DateTime();
        $hora_dt->setTime($componentesHora[0], $componentesHora[1], $componentesHora[2]);

        // Verificar si se creó correctamente el objeto DateTime
        if ($hora_dt !== false) {
            // Obtener la hora formateada en "hh:mm:ss"
            $horaFormateada = $hora_dt->format('H:i:s');
            return $horaFormateada;
        } else {
            // Manejar el caso en que la conversión falle
            return false;
        }
    } else {
        // Manejar el caso en que la cadena de hora no tenga el formato esperado
        return false;
    }
}
function obtenerHoraEntrada($idUsuarioHoras, $fechaSalida, $conn){
    // Inicializar la variable $fechaHoraEntrada
    $fechaHoraEntrada = null;

    // Consulta para obtener la última entrada del usuario en el mismo día que la salida actual
    $query = "SELECT TOP 1 FechaHora, IdUsuario
              FROM TRegistro 
              WHERE IdUsuario = $idUsuarioHoras 
              AND Tipo = 0 
              AND CONVERT(date, FechaHora) = '$fechaSalida'
              ORDER BY FechaHora DESC";

    // Ejecutar la consulta
    $result = sqlsrv_query($conn, $query);

    if ($result === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Verificar si hay resultados
    if (sqlsrv_has_rows($result)) {
        // Obtener la hora de entrada
        $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
        $fechaHoraEntrada = $row['FechaHora']->format('Y-m-d H:i:s.u');
    }

    return $fechaHoraEntrada;
}
function obtenerHoraPausa($idUsuarioHoras, $fechaSalida, $conn){
    // Inicializar la variable $fechaHoraEntrada
    $fechaHoraPausa = null;

    // Consulta para obtener la última entrada del usuario en el mismo día que la salida actual
    $query = "SELECT TOP 1 FechaHora, IdUsuario
              FROM TRegistro 
              WHERE IdUsuario = $idUsuarioHoras 
              AND Tipo = 2 
              AND CONVERT(date, FechaHora) = '$fechaSalida'
              ORDER BY FechaHora DESC";

    // Ejecutar la consulta
    $result = sqlsrv_query($conn, $query);

    if ($result === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Verificar si hay resultados
    if (sqlsrv_has_rows($result)) {
        // Obtener la hora de entrada
        $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
        $fechaHoraPausa = $row['FechaHora']->format('Y-m-d H:i:s.u');
    }

    return $fechaHoraPausa;
}
function obtenerHoraReanudar($idUsuarioHoras, $fechaSalida, $conn){
    // Inicializar la variable $fechaHoraEntrada
    $fechaHoraReanuda = null;

    // Consulta para obtener la última entrada del usuario en el mismo día que la salida actual
    $query = "SELECT TOP 1 FechaHora, IdUsuario
              FROM TRegistro 
              WHERE IdUsuario = $idUsuarioHoras 
              AND Tipo = 3 
              AND CONVERT(date, FechaHora) = '$fechaSalida'
              ORDER BY FechaHora DESC";

    // Ejecutar la consulta
    $result = sqlsrv_query($conn, $query);

    if ($result === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Verificar si hay resultados
    if (sqlsrv_has_rows($result)) {
        // Obtener la hora de entrada
        $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
        $fechaHoraReanuda = $row['FechaHora']->format('Y-m-d H:i:s.u');
    }

    return $fechaHoraReanuda;
}
?>
