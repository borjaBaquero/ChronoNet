<?php
include_once 'session_checker.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página con Pestañas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .tabs {
            display: flex;
            background-color: #062f6d;
            padding: 10px;
        }

        .tab {
            flex: 1;
            text-align: center;
            padding: 10px;
            color: #fff;
            cursor: pointer;
        }

        .tab-content {
            display: none;
            padding: 20px;
        }
    </style>
</head>
<body>

<div class="tabs">
    <div class="tab" onclick="openTab('Inicio')">Inicio</div>
    <div class="tab" onclick="openTab('Perfil')">Perfil</div>
    <div class="tab" onclick="openTab('Configuracion')">Configuración</div>
</div>

<div id="Inicio" class="tab-content">
    <h2>Contenido de la pestaña Inicio</h2>
    <p>Bienvenido a la página de inicio.</p>
</div>

<div id="Perfil" class="tab-content">
    <h2>Contenido de la pestaña Perfil</h2>
    <p>Aquí puedes ver tu perfil.</p>
</div>

<div id="Configuracion" class="tab-content">
    <h2>Contenido de la pestaña Configuración</h2>
    <p>Configura tus preferencias aquí.</p>
</div>

<script>
    function openTab(tabName) {
        // Oculta todos los contenidos de pestañas
        var tabContents = document.getElementsByClassName('tab-content');
        for (var i = 0; i < tabContents.length; i++) {
            tabContents[i].style.display = 'none';
        }

        // Muestra el contenido de la pestaña seleccionada
        document.getElementById(tabName).style.display = 'block';
    }
</script>

</body>
</html>
