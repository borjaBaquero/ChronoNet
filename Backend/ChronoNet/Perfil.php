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

<div id="Perfil" class="tab-content">
    <h2 class="posicionamiento-textos">Perfil:</h2>
    <p class="posicionamiento-textos">Desde este apartado puedes visualizar la información principal sobre tu usuario. También podrás acceder a la ayuda al usuario, registros e integraciones.</p><br/>
    <div id="bloque-perfil-usuario" class="posicionamiento-textos">
    <h2 >Datos Principales</h2>
        <?php
            // Realizar la consulta de usuarios con LIMIT para la paginación
            $queryPerfil = "SELECT * FROM TUsuarios tu WHERE tu.Login LIKE '". $_SESSION['usuario']['nombre'] ."'" ;
            $resultUsuarios = sqlsrv_query($conn, $queryPerfil);

            if ($resultUsuarios === false) {
                die(print_r(sqlsrv_errors(), true));
            }

            // Recorrer los resultados de usuarios y agregar cada fila a la tabla
            while ($rowUsuarios = sqlsrv_fetch_array($resultUsuarios, SQLSRV_FETCH_ASSOC)) {
                $adminBool = ($rowUsuarios['AdminBool'] == "1") ? "SI" : "NO";
                $codigo = $rowUsuarios['Codigo'];
                $nombreCompleto = $rowUsuarios['Nombre'] . " " . $rowUsuarios['Apellidos'];
                $idPerfilUsuario = $rowUsuarios['IdUsuario'];
                $usuario = $rowUsuarios['Login'];
            }
            echo "<p><span class='titulo'>Identificador: </span><span class='contenido'>" . $idPerfilUsuario . "</span></p>";
            echo "<p><span class='titulo'>Usuario de Acceso: </span><span class='contenido'>" . $usuario . "</span><br/>";
            echo "<p><span class='titulo'>Nombre Completo: </span><span class='contenido'>" . $nombreCompleto . "</span></p>";
            echo "<p><span class='titulo'>Tipo Administrador: </span><span class='contenido'>" . $adminBool . "</span></p>";
            echo "<p><span class='titulo'>Código Usuario: </span><span class='contenido'>" . $codigo . "</span></p>";
            
        ?>
    </div>

    <div id="bloque-perfil-usuario" class="posicionamiento-textos">
    <h2 >Ayuda</h2>
    <p><span class='titulo'>Versión: </span><span class='contenido'>1.20</span></p>
    <p><span class='titulo'>Solución: </span><span class='contenido'>[DNET-RRHH/DELTANET]</span></p>
    <p><span class='titulo'>Novedades de la Versión: </span></p>
    <ul>
    <li class='contenido'>Creación de partes de horas sobre usuarios. Permitiendo la selección del usuario correspondiente, mes y año.</li>
    <li class='contenido'>Modificaciones en el apartado Perfil de Usuarios, indicando los diferentes datos sobre el usuario que ha hecho login. </li>
    <li class='contenido'>Mejoras en el control de acceso y sesión del usuario.</li>
    </ul>
    <hr>
    <p><a href="#"class='titulo'>Contacto Telefónico</a></p>
    <p><a href="#"class='titulo'>Contacto Vía Email</a></p>
    </div>
    <div id="bloque-perfil-usuario" class="posicionamiento-textos">
        <h2>Integraciones</h2>

        <div class="container-integraciones">
        <div class="block">
            <img src="src/swagger_logo.png" alt="Imagen">
            <h2>SWAGGER</h2>
            <p>API para integración con la base de datos</p>
        </div>
        <div class="block">
            <img src="src/phpoffice logo.jpg" alt="Otra Imagen">
            <h2>PHP Office</h2>
            <p>Librería para trabajar con Excel, Word, PowerPoint...</p>
        </div>
    </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.25.0/prism.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.25.0/components/prism-log.min.js"></script>

    <style>
    #bloque-perfil-usuario {
        background-color: #f8f9fa; /* Color de fondo */
        padding: 20px; /* Espacio alrededor del contenido */
        border-radius: 5px; /* Bordes redondeados */
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); /* Sombra ligera */
    }

    .titulo {
        font-weight: bold; /* Texto en negrita para los títulos */
        color: #062f6d; /* Color del texto del título */
        font-size: 1.2em; /* Tamaño de fuente más grande */
        margin-bottom: 5px; /* Espacio inferior entre los títulos y los contenidos */
    }

    .contenido {
        color: #666; /* Color del texto del contenido */
        margin-bottom: 10px; /* Espacio inferior entre los contenidos */
    }

    .visor {
        height: 400px; /* Altura deseada del visor */
        overflow-y: scroll; /* Agrega una barra de desplazamiento vertical si es necesario */
        border: 1px solid #ccc; /* Borde para resaltar el área del visor */
        padding: 10px; /* Espacio interno para mejorar la legibilidad */
        box-sizing: border-box; /* Incluye el padding en el cálculo del tamaño */
    }

    .linea {
        white-space: pre-line; /* Mantiene el formato de las líneas, incluidos los saltos de línea */
        margin-bottom: 5px; /* Espacio entre líneas */
    }

    .container-integraciones {
                display: flex;
                justify-content: space-between;
                align-items: stretch; /* Los contenedores tendrán la misma altura */
                margin-bottom: 20px;
            }

            .container-integraciones .block {
                background-color: #fff;
                width: 48%;
                padding: 20px;
                box-sizing: border-box;
                text-align: center;
                height: 100%; /* Ocupa toda la altura del contenedor */
            }

            .container-integraciones .block img {
                max-width: 100%;
                height: auto;
                max-height: 100px; /* Altura máxima para las imágenes */
                display: block;
                margin: 0 auto;
                margin-bottom: 20px;
            }

            @media(max-width: 768px) {
                .container-integraciones {
                    flex-direction: column;
                }

                .container-integraciones .block {
                    width: 100%;
                    margin-bottom: 20px;
                }
            }
    </style>
</div>

</body>
</html>