<?php

require_once __DIR__ . '/src/FichajesController.php'; // Para las rutas relacionadas con Fichajes
require_once __DIR__ . '/src/UserController.php'; // Para las rutas relacionadas con Usuarios

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

switch ($path) {
    // Endpoint para verificar o crear usuarios
    case '/api/usuarios/verificar':
        if ($method === 'POST') {
            $controller = new UserController();
            $controller->verificarOCrearUsuario();
        }
        break;

    // Endpoint para guardar registros
    case '/api/registros':
        if ($method === 'POST') {
            $controller = new FichajesController();
            $controller->guardarRegistros();
        }
        break;

    // Endpoint para obtener nuevos fichajes
    case '/api/fichajes/nuevos':
        if ($method === 'GET') {
            $controller = new FichajesController();
            $controller->obtenerFichajesNuevos();
        }
        break;

    // Endpoint para el ping
    case '/api/ping':
        if ($method === 'GET') {
            $controller = new FichajesController();
            $controller->ping();
        }
        break;

    // Endpoint para confirmar sincronización
    case '/api/fichajes/confirmarSincronizacion':
        if ($method === 'POST') {
            $controller = new FichajesController();
            $data = json_decode(file_get_contents('php://input'), true);
            $controller->confirmarSincronizacion($data['idsFichajes'] ?? []);
        }
        break;

    // Endpoint para obtener registros con usuarios
    case '/api/fichajes/obtenerRegistros':
        if ($method === 'GET') {
            $controller = new FichajesController();
            $controller->obtenerRegistros();
        }
        break;

    // Endpoint para obtener total de horas por token
    case '/api/fichajes/obtenerTotalHoras':
        if ($method === 'GET') {
            $controller = new FichajesController();
            $token = $_GET['token'] ?? null;
            $controller->obtenerTotalHoras($token);
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(["error" => "Endpoint no encontrado."]);
        break;
}
