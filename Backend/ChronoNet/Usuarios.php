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
    <title>Usuarios</title>
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

        <div id="Usuarios" class="tab-content">
            <h2 class="posicionamiento-textos">Usuarios:</h2>
            <p class="posicionamiento-textos">Desde este apartado puedes visualizar los usuarios en formato tabla, también puedes editarlos o crear nuevos usuarios.</p>

            <?php
            require_once 'dao/DAOUsuarios.php';

            $usuariosPorPagina = 10;
            $paginaUsuarios = isset($_GET['pagina_usuarios']) ? intval($_GET['pagina_usuarios']) : 1;
            $offsetUsuarios = ($paginaUsuarios - 1) * $usuariosPorPagina;
            
            $daoUsuarios = new DAOUsuarios($conn);

            $usuarios = $daoUsuarios->obtenerUsuariosConPaginacion($offsetUsuarios, $usuariosPorPagina);

            // Crear la tabla HTML de usuarios
            echo '<table class="tablas-listado-user" border="1">';
            echo '<tr id="header-row"><th class="my-cell">ID</th><th class="my-cell">Usuario</th><th class="my-cell">Codigo</th><th class="my-cell">Nombre</th><th class="my-cell">Apellidos</th><th class="my-cell">Administrador</th><th class="my-cell"></th><th class="my-cell"></th></tr>';

            // Recorrer los resultados de usuarios y agregar cada fila a la tabla
            foreach($usuarios as $rowUsuarios){
                $adminBool = ($rowUsuarios['AdminBool'] == "1") ? "SI" : "NO";
                echo '<tr>';
                echo '<td class="td-list">' . htmlspecialchars($rowUsuarios['IdUsuario']) . '</td>';
                echo '<td class="td-list">' . htmlspecialchars($rowUsuarios['Login']) . '</td>';
                echo '<td class="td-list">' . htmlspecialchars($rowUsuarios['Codigo']) . '</td>';
                echo '<td class="td-list">' . htmlspecialchars($rowUsuarios['Nombre']) . '</td>';
                echo '<td class="td-list">' . htmlspecialchars($rowUsuarios['Apellidos']) . '</td>';
                echo '<td class="td-list">' . htmlspecialchars($adminBool) . '</td>';
                echo '<td class="td-list"><a href="editar_usuario.php?id=' . htmlspecialchars($rowUsuarios['IdUsuario']) . '"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path fill="#062f6d" d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1v32c0 8.8 7.2 16 16 16h32zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z"/></svg></a></td>';
                echo '<td class="td-list"><a onclick="return confirmarEliminar(' . htmlspecialchars($rowUsuarios['IdUsuario']) . ')" href="eliminar_usuario.php?id=' .htmlspecialchars($rowUsuarios['IdUsuario']) . '">';
                echo '<img style="width:20px;margin-left:50%; transform:translateX(-50%);" src="src/eliminar.png"></a></td>';
                echo '</tr>';
            }


            // while ($rowUsuarios = sqlsrv_fetch_array($resultUsuarios, SQLSRV_FETCH_ASSOC)) {
            //     $adminBool = ($rowUsuarios['AdminBool'] == "1") ? "SI" : "NO";
            
            //     echo '<tr>';
            //     echo '<td class="td-list">' . htmlspecialchars($rowUsuarios['IdUsuario']) . '</td>';
            //     echo '<td class="td-list">' . htmlspecialchars($rowUsuarios['Login']) . '</td>';
            //     echo '<td class="td-list">' . htmlspecialchars($rowUsuarios['Codigo']) . '</td>';
            //     echo '<td class="td-list">' . htmlspecialchars($rowUsuarios['Nombre']) . '</td>';
            //     echo '<td class="td-list">' . htmlspecialchars($rowUsuarios['Apellidos']) . '</td>';
            //     echo '<td class="td-list">' . htmlspecialchars($adminBool) . '</td>';
            //     echo '<td class="td-list"><a href="editar_usuario.php?id=' . htmlspecialchars($rowUsuarios['IdUsuario']) . '"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path fill="#062f6d" d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1v32c0 8.8 7.2 16 16 16h32zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z"/></svg></a></td>';
            //     echo '<td class="td-list"><a onclick="return confirmarEliminar(' . htmlspecialchars($rowUsuarios['IdUsuario']) . ')" href="eliminar_usuario.php?id=' .htmlspecialchars($rowUsuarios['IdUsuario']) . '">';
            //     echo '<img style="width:20px;margin-left:50%; transform:translateX(-50%);" src="src/eliminar.png"></a></td>';
            //     echo '</tr>';
            // }

            echo '</table>';

            // Calcular el número total de usuarios para la paginación
            $queryTotalUsuarios = "SELECT COUNT(*) AS totalUsuarios FROM TUsuarios";
            $resultTotalUsuarios = sqlsrv_query($conn, $queryTotalUsuarios);
            $totalUsuarios = sqlsrv_fetch_array($resultTotalUsuarios)['totalUsuarios'];

            // Calcular el número total de páginas para usuarios
            $totalPaginasUsuarios = ceil($totalUsuarios / $usuariosPorPagina);

            // Mostrar los enlaces de paginación para usuarios
            echo '<div class="paginacion">';
            for ($i = 1; $i <= $totalPaginasUsuarios; $i++) {
                if ($i == $paginaUsuarios) {
                    echo '<span class="current">' . $i . '</span> ';
                } else {
                    echo '<a href="?pagina_usuarios=' . $i . '">' . $i . '</a> ';
                }
            }
            echo '</div>';

            // // Liberar los recursos de usuarios
            // sqlsrv_free_stmt($resultUsuarios);
            // sqlsrv_free_stmt($resultTotalUsuarios);


            ?>

            <!-- Botón para abrir el formulario popup -->
            <button id="crearUsuarioBtn" class="crear-usuario-button" onclick="mostrarFormulario()">Crear Nuevo Usuario</button>

            <script>
                // Función para mostrar el formulario popup
                function mostrarFormulario() {
                    document.getElementById('formularioPopup').style.display = 'block';
                }

                // Función para cerrar el formulario popup
                function cerrarFormulario() {
                    document.getElementById('formularioPopup').style.display = 'none';
                }

                // Función para confirmar la eliminación de un usuario
                function confirmarEliminar(idUsuario) {
                    return confirm('¿Estás seguro de que quieres eliminar este usuario?');
                }
            </script>

            <!-- Formulario popup -->
            <div id="formularioPopup" class="popup-form" style="display: none;">
                <form id="nuevoUsuarioForm" action="procesar_formulario.php" method="post" onsubmit="return validarFormulario()">
                    <label for="nuevoUsuario">DNI:</label>
                    <input type="text" name="nuevoUsuario" required pattern="\d+" maxlength="8" minlength="8" title="Solo se permiten números">
                    <br><br>
                    
                    <label for="nuevoCodigo">Código:</label>
                    <input type="text" name="nuevoCodigo" required pattern="\d+" maxlength="4" minlength="4" title="Solo se permiten números">
                    <br><br>
                    
                    <label for="nuevoNombre">Nombre:</label>
                    <input type="text" name="nuevoNombre" required pattern="[A-Za-z\s]+" maxlength="50" title="Solo se permiten letras y espacios">
                    <br><br>
                    
                    <label for="nuevoApellidos">Apellidos:</label>
                    <input type="text" name="nuevoApellidos" required pattern="[A-Za-z\s]+" maxlength="100" title="Solo se permiten letras y espacios">
                    <br><br>
                    
                    <label for="nuevoAdministrador">Administrador:</label>
                    <input type="checkbox" name="nuevoAdministrador">
                    <br><br>
                    
                    <button type="submit" name="submitForm">Guardar</button>
                    <button type="button" onclick="cerrarFormulario()">Cancelar</button>
                </form>
            </div>

        </div>
    </body>
</html>