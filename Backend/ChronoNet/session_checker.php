<?php
session_start();

// Verifica si la sesión está activa
if (!isset($_SESSION['usuario'])) {
    // El usuario no ha iniciado sesión, redirige al formulario de inicio de sesión
    header("Location: login.php");
    exit();
}
?>