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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="src/style_home.css">
    <title>Registros</title>
    <link rel="icon" href="src/icon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="src/icon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.25.0/themes/prism.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

</head>

<body>

<div class="tabs">
    <!-- Asegúrate de que la etiqueta link para el logo esté antes de las pestañas -->
    <div class="logo">
    <img src="src/deltanet-white.png" width="200px">
    </div>

    <div class="tab" onclick="window.location.href='Usuarios.php'">
        <i class="fas fa-users"></i> Usuarios <!-- Icono para Usuarios -->
    </div>
    <div class="tab" onclick="window.location.href='Registros.php'">
    <i class="fas fa-clipboard-list"></i> Registros <!-- Icono para Registros -->
    </div>
    <div class="tab" onclick="window.location.href='Partes.php'">
    <i class="fas fa-tools"></i> Partes<!-- Icono para Partes -->
    </div>
    <div class="tab" onclick="window.location.href='Perfil.php'"><p class="usuario-nombre"><?php echo "<b>Usuario Activo: </b>" . $_SESSION['usuario']['nombre']; ?></p></div>
    <div class="tab" onclick="window.location.href='Perfil.php'"><b>Versión: 1.20</b><br/>DNET-RRHH</div>
    <div class="tab" style="cursor: pointer; color: red;" onclick="cerrarSesion()">Cerrar Sesión</div>
</div>

<div id="Registros" class="tab-content">
    <h2 class="posicionamiento-textos">Registros</h2>
    <p class="posicionamiento-textos">Listado de registros que permite filtrar y descargarlos en formato xlsx.</p>

    <?php
    require_once 'dao/DAORegistros.php';

    $daoRegistros = new DAORegistros($conn);
    
    $resultHoras = $daoRegistros->obtenerRegistrosHorasTrabajadas($conn);


$horasPausa = 0;
$minutosPausa = 0;

echo '<script>';
echo 'document.addEventListener("DOMContentLoaded", function() {';
while ($row = sqlsrv_fetch_array($resultHoras, SQLSRV_FETCH_ASSOC)) {
    // Utilizar el ID del usuario como el valor del option
    $idRegistroHoras = $row['IdRegistro'];
    $idUsuarioHoras = $row['IdUsuario'];
    $tipo = $row['Tipo'];
    $fechaHoraSalida = $row['FechaHora'];

    if ($tipo == 1) {
        
        // Obtener la fecha de la salida actual
        $fechaSalida = $fechaHoraSalida->format('Y-m-d H:i:s.u');
        $fechaEntradaFunc = obtenerHoraEntrada($idUsuarioHoras, $fechaSalida, $conn);
        $fechaPausaFunc = null;
        $fechaReanudaFunc = null;
        $fechaPausaFunc = obtenerHoraPausa($idUsuarioHoras, $fechaSalida, $conn);
        $fechaReanudaFunc = obtenerHoraReanudar($idUsuarioHoras, $fechaSalida, $conn);


                // Extraer solo las horas de la fecha y hora de entrada
                // Verificar y asignar las horas solo si las fechas no son nulas
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

                // Aquí asignas el valor de horas trabajadas a la celda correspondiente por su ID de registro
                echo "var horasTrab = '$totalHorasFormateadas:$totalMinutosFormateados';";
                echo "var cell = document.getElementById('$idRegistroHoras');";
                echo "if (cell) {";
                echo "    cell.textContent = horasTrab;";
                echo "}";

                // Crea un array asociativo para almacenar la información del registro actual
                $registro = array(
                    'idRegistro' => $idRegistroHoras,
                    'horas' => $totalHoras,
                    'minutos' => $totalMinutos
                );

                // Agrega el array del registro al array principal
                $registros[] = $registro;
        }
}

$registros_json = json_encode($registros);

echo '});';
echo '</script>';

    // Definir el filtro de fecha y hora si se ha enviado en el formulario
    $filtroFechaHora = isset($_POST['filtroFechaHora']) ? $_POST['filtroFechaHora'] : '';
    $filtroFechaHoraFinal = isset($_POST['filtroFechaHoraFinal']) ? $_POST['filtroFechaHoraFinal'] : '';
    $filtroNombreCompleto = isset($_POST['filtroNombreCompleto']) ? $_POST['filtroNombreCompleto'] : '';


    //lamada a la funcion del dao
    $result = $daoRegistros->obtenerRegistrosFechaHora($conn, $filtroFechaHora, $filtroNombreCompleto);

 
    echo '<meta charset="UTF-8"><br><br>';
    echo '<form id="filtroForm" method="post" action="descargar_registros.php">';
    echo '<div id="filtros" style="display: flex; flex-wrap: wrap; justify-content: space-between;">';

    // Agregar los campos de búsqueda para cada columna
    echo '<div style="flex: 0 0 30%;">';
    echo '    <label for="filtroNombreCompleto">Nombre Completo:</label>';
    echo '    <select id="filtroNombreCompleto" name="filtroNombreCompleto" class="filtroCampo">';
    echo '        <option value="">Selecciona un nombre...</option>';

    //OTRA FUNCION
    // Realizar la consulta para obtener los nombres completos de los usuarios con su ID
    $queryUsuarios = "SELECT IdUsuario, (Nombre + ' ' + Apellidos) AS NombreCompleto FROM TUsuarios";
    $resultUsuarios = sqlsrv_query($conn, $queryUsuarios);

    if ($resultUsuarios === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Iterar sobre los resultados y mostrar cada nombre completo como una opción en el select
    while ($row = sqlsrv_fetch_array($resultUsuarios, SQLSRV_FETCH_ASSOC)) {
        // Utilizar el ID del usuario como el valor del option
        echo '<option value="' . $row['NombreCompleto'] . '">' . $row['NombreCompleto'] . '</option>';
    }

    // Liberar los recursos de la consulta
    sqlsrv_free_stmt($resultUsuarios);

    echo '    </select>';
    echo '</div>';
    echo '<div style="flex: 0 0 30%;"><label for="filtroTipo">Tipo:</label> <input type="text" id="filtroTipo" class="filtroCampo" name="filtroTipo" value=""></div>';
    echo '<div style="flex: 0 0 30%;"><label for="filtroIncidencia">Incidencia:</label> <input type="text" id="filtroIncidencia" class="filtroCampo" name="filtroIncidencia" value=""></div>';
    echo '<div style="flex: 0 0 30%;"><label for="filtroFechaHora">Fecha Inicial:</label>';
    echo '<input type="date" id="filtroFechaHora" class="filtroCampo" name="filtroFechaHora" value=""></div>';
    echo '<input type="hidden" name="registros" value="<?php echo htmlspecialchars($registros_json); ?>">';
    
    echo '<div style="flex: 0 0 30%;"><label for="filtroFechaHoraFinal">Fecha Final:</label>';
    echo '<input type="date" id="filtroFechaHoraFinal" class="filtroCampo" name="filtroFechaHoraFinal" value=""></div>';

    echo '<div style="flex: 0 0 30%;"><button type="submit" name="descargar" class="descargar-btn">Descargar Registros</button><button style="margin-left:20px;" type="reset" onclick="resetFiltros()" class="reset-btn">Reset</button></div>';

    echo '</div>';
    echo '</form>';
    echo '<button id="crearRegistroBtn" class="crear-usuario-button" onclick="mostrarFormularioRegistro()">Nuevo Registro</button>';
    echo '<table class="tablas-listado" border="1">';
    echo '<tr id="header-row"><th class="my-cell">ID Registro</th><th class="my-cell">Nombre</th><th class="my-cell">Tipo</th><th class="my-cell">Fecha y Hora</th><th class="my-cell">Incidencia</th><th class="my-cell" id="horas-trab">Horas</th><th class="my-cell"></th></tr>';

// Recorrer los resultados y agregar cada fila a la tabla
while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
    echo '<tr>';
    echo '<td class="td-list">' . $row['IdRegistro'] . '</td>';
    echo '<td class="td-list" value="' . $row['IdUsuario'] . '">' . $row['nombrecompleto']  . '</td>';

    $tipo = $row['Tipo'];
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

    echo '<td class="td-list">' . $tipoTexto . '</td>';
    echo '<td class="td-list">' . $row['FechaHora']->format('Y-m-d H:i:s') . '</td>';
    echo '<td class="td-list">' . $row['Incidencia'] . '</td>';
    echo '<td class="td-list" id="' . $row['IdRegistro'] . '"> </td>';
    echo '<td class="td-list"><a href="eliminar_registro.php?id=' . $row['IdRegistro'] . '">';
    echo '<img style="width:20px;margin-left:50%; transform:translateX(-50%);" src="src/eliminar.png"></a></td>';
    echo '</tr>';
}

echo '</table>';

    

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
        }else{
            $fechaHoraPausa = null;
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
        }else{
            $fechaHoraReanuda = null;
        }
    
        return $fechaHoraReanuda;
    }
    



// Agregar el filtro de fecha y hora si se ha proporcionado
if (!empty($filtroFechaHora)) {
// Ajustar el formato de fecha y hora para que coincida con el de la base de datos
$filtroFechaHora = date('Y-m-d H:i:s', strtotime($filtroFechaHora));
$query .= " WHERE CONVERT(varchar, r.FechaHora, 120) LIKE '%$filtroFechaHora%'";
}

if (!empty($filtroNombreCompleto)) {
// Agregar el filtro de nombre completo si se ha proporcionado
$query .= " AND (u.Nombre + ' ' + u.Apellidos) LIKE '%$filtroNombreCompleto%'";
}

// Ordenar por usuario y fecha/hora
$query .= " ORDER BY r.IdUsuario, r.FechaHora";

    // Liberar los recursos
    sqlsrv_free_stmt($result);

    ?>

    <!-- Formulario popup Registro-->
    <div id="formularioPopupRegistro" class="popup-form">
    <form id="nuevoUsuarioForm" action="procesar_formulario_registro.php" method="post">
        <label for="usuarios">Selecciona un usuario:</label>
        <select id="usuarios" name="usuarios" class="select-partes">
            <?php
            // Realizar la consulta para obtener los nombres completos de los usuarios con su ID
            $queryUsuarios = "SELECT IdUsuario, (Nombre + ' ' + Apellidos) AS NombreCompleto FROM TUsuarios";
            $resultUsuarios = sqlsrv_query($conn, $queryUsuarios);

            if ($resultUsuarios === false) {
                die(print_r(sqlsrv_errors(), true));
            }

            // Iterar sobre los resultados y mostrar cada nombre completo como una opción en el select
            while ($row = sqlsrv_fetch_array($resultUsuarios, SQLSRV_FETCH_ASSOC)) {
                // Utilizar el ID del usuario como el valor del option
                echo '<option value="' . $row['IdUsuario'] . '">' . $row['NombreCompleto'] . '</option>';
            }

            // Liberar los recursos de la consulta
            sqlsrv_free_stmt($resultUsuarios);
            ?>
        </select>
        <br><br>
        <label for="fechaHora">Fecha y Hora:</label>
        <input type="datetime-local" id="fechaHora" name="fechaHora" class="select-partes">
        <br><br>
        <label for="incidencia">Incidencia:</label>
        <select id="incidencia" name="incidencia" class="select-partes">
            <option value="SI">SI</option>
            <option value="NO">NO</option>
        </select>
        <br><br>
        <label for="tipo">Tipo:</label>
        <select id="tipo" name="tipo" class="select-partes">
            <option value="0">Entrada</option>
            <option value="2">Pausa</option>
            <option value="3">Reanudar</option>
            <option value="1">Salida</option>
        </select>
        <br><br>
        <button type="submit" name="submitForm">Guardar</button>
        <button type="button" onclick="cerrarFormularioRegistro()">Cancelar</button>
    </form>
</div>
</div>

<script>
    function mostrarFormulario() {
        var formularioPopup = document.getElementById('formularioPopup');
        formularioPopup.style.display = 'block';
    }

    function cerrarFormulario() {
        var formularioPopup = document.getElementById('formularioPopup');
        formularioPopup.style.display = 'none';
    }

    function mostrarFormularioRegistro() {
        var formularioPopupRegistro = document.getElementById('formularioPopupRegistro');
        formularioPopupRegistro.style.display = 'block';
    }

    function cerrarFormularioRegistro() {
        var formularioPopupRegistro = document.getElementById('formularioPopupRegistro');
        formularioPopupRegistro.style.display = 'none';
    }

    function openTab(tabName) {
        // Oculta todos los contenidos de pestañas
        var tabContents = document.getElementsByClassName('tab-content');
        for (var i = 0; i < tabContents.length; i++) {
            tabContents[i].style.display = 'none';
        }

        // Muestra el contenido de la pestaña seleccionada
        document.getElementById(tabName).style.display = 'block';
    }

    function cerrarSesion() {
        alert('La sesión se ha cerrado correctamente');
        window.location.href = "index.php?cerrar_sesion=true";
    }
</script>

<script>
    // Definir variables globales para los campos de entrada
    var filtroNombreCompletoInput;
    var filtroTipoInput;
    var filtroFechaHoraInput;
    var filtroFechaHoraFinalInput;
    var filtroIncidenciaInput;

// Función para crear un objeto Date a partir de una cadena de fecha
function createDateFromFormat(dateString) {
    var parts = dateString.split(' ')[0].split('-');
    var year = parseInt(parts[0]);
    var month = parseInt(parts[1]) - 1; // Los meses en JavaScript van de 0 a 11
    var day = parseInt(parts[2]);

    // Crear el objeto Date con los componentes extraídos y establecer las horas, minutos y segundos en 00:00:00
    var date = new Date(year, month, day, 0, 0, 0);

    return date;
}


    function rellenarHoras(){
        // Obtener todas las filas de la tabla, excluyendo la primera (encabezado)
        var filas = document.querySelectorAll('.tablas-listado tr:not(#header-row)');
        
        // Iterar sobre las filas
        filas.forEach(function(fila) {
            // Obtener el tipo de la fila
            var tipo = fila.cells[2].textContent.trim();
            
            if (tipo.toLowerCase() === 'salida') {
                console.log("SALIDA");
                var idUser = fila.cells[1].getAttribute('value');
                var fechahorafila = fila.cells[3].textContent.trim();
                //console.log(fechahorafila);
                console.log(idUser);

            } else {
                console.log("NO");
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Llamar a la función rellenarHoras() cuando se cargue la página
        rellenarHoras();
    });


    function aplicarFiltros() {
    // Obtener los valores de cada campo de búsqueda
    var filtroNombreCompleto = filtroNombreCompletoInput.value.toLowerCase();
    var filtroTipo = filtroTipoInput.value.toLowerCase();
    var filtroFechaHora = filtroFechaHoraInput.value.toLowerCase();
    var filtroFechaHoraFinal = filtroFechaHoraFinalInput.value.toLowerCase();
    var filtroIncidencia = filtroIncidenciaInput.value.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");;

    // Obtener todas las filas de la tabla, excluyendo la primera (encabezado)
    var filas = document.querySelectorAll('.tablas-listado tr:not(#header-row)');

    // Iterar sobre las filas
    filas.forEach(function(fila) {
        var textoFila = fila.textContent.toLowerCase();
        var cumpleFiltros =
            textoFila.includes(filtroNombreCompleto) &&
            textoFila.includes(filtroTipo) &&
            textoFila.includes(filtroIncidencia + " ");

        // Verificar si filtroFechaHoraFinal está lleno y la fila es menor que el rango
        if (filtroFechaHora && filtroFechaHoraFinal) {
            var filtroFechaHoraDate = createDateFromFormat(filtroFechaHora);
            var filtroFechaHoraFinalDate = createDateFromFormat(filtroFechaHoraFinal);

            // Obtener la fecha de la fila (suponiendo que esté en la cuarta celda)
            var filaFecha = fila.cells[3].textContent; // Suponiendo que la fecha está en la cuarta celda (índice 3) de la fila
            var filaFechaDate = createDateFromFormat(filaFecha);

            // Verificar si la fecha de la fila está dentro del rango
            if (!isNaN(filaFechaDate) && filtroFechaHoraDate <= filaFechaDate && filaFechaDate <= filtroFechaHoraFinalDate) {
                console.log("Cumple filtros de fecha");
            } else {
                console.log("No cumple filtros de fecha");
                cumpleFiltros = false; // Si la fecha no cumple, entonces la fila no cumple con los filtros
            }
        } else if (filtroFechaHora) {
            var filtroFechaHoraDate = createDateFromFormat(filtroFechaHora).setHours(0, 0, 0, 0);
            var filaFecha = fila.cells[3].textContent;
            var filaFechaDate = createDateFromFormat(filaFecha).setHours(0, 0, 0, 0);

            if (!isNaN(filaFechaDate) && filaFechaDate === filtroFechaHoraDate) {
                console.log("Cumple filtro de fecha específica");
            } else {
                console.log("No cumple filtro de fecha específica");
                cumpleFiltros = false;
            }
        }

        // Establecer la visualización de la fila
        fila.style.display = cumpleFiltros ? '' : 'none';
    });
}


    // Función para resetear los filtros
    function resetFiltros() {
        // Limpiar el valor de cada campo de entrada
        filtroNombreCompletoInput.value = '';
        filtroTipoInput.value = '';
        filtroFechaHoraInput.value = '';
        filtroFechaHoraFinalInput.value = '';
        filtroIncidenciaInput.value = '';

        // Restablecer el valor del campo de selección a su opción predeterminada
        var selectFiltroNombreCompleto = document.getElementById('filtroNombreCompleto');
        selectFiltroNombreCompleto.selectedIndex = 0;

        // Añadir un espacio al campo de tipo si está vacío
        if (filtroTipoInput.value.trim() === '') {
            filtroTipoInput.value = ' ';
        }

        // Aplicar los filtros después de resetear
        aplicarFiltros();
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Obtener las referencias a los campos de búsqueda
        filtroNombreCompletoInput = document.getElementById('filtroNombreCompleto');
        filtroTipoInput = document.getElementById('filtroTipo');
        filtroFechaHoraInput = document.getElementById('filtroFechaHora');
        filtroFechaHoraFinalInput = document.getElementById('filtroFechaHoraFinal');
        filtroIncidenciaInput = document.getElementById('filtroIncidencia');

        // Llamar a aplicarFiltros() para actualizar la lista al cargar la página
        aplicarFiltros();

        // Agregar un evento 'input' a cada campo de búsqueda
        var camposFiltro = document.querySelectorAll('.filtroCampo');
        camposFiltro.forEach(function(filtroInput) {
            filtroInput.addEventListener('input', function() {
                aplicarFiltros();
            });
        });
    });
</script>


</body>
</html>