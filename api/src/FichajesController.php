<?php

require_once __DIR__ . '/../config/db.php'; // Ruta al archivo de conexión
require_once __DIR__ . '/DAOTRegistros.php'; // Ruta al modelo de registros
require_once __DIR__ . '/DAOTControlHoras.php'; // Ruta al modelo de control de horas
class FichajesController
{
    private $daoRegistros;
    private $dAOTControlHoras;

    public function __construct()
    {
        $this->daoRegistros = new DAOTRegistros();
        $this->dAOTControlHoras = new DAOTControlHoras();
    }

    // Obtener fichajes nuevos
    public function obtenerFichajesNuevos()
    {
        try {
            $fichajesNuevos = $this->daoRegistros->obtenerRegistrosNoSincronizados();
            echo json_encode($fichajesNuevos);
        } catch (Exception $ex) {
            http_response_code(500);
            echo json_encode(["error" => "Error al obtener fichajes nuevos: " . $ex->getMessage()]);
        }
    }

    public function ping()
    {
        try {
            $db = new Database();
            $conn = $db->connect();

            if ($conn) {
                echo json_encode(["status" => "OK", "message" => "API y base de datos disponibles"]);
            } else {
                http_response_code(500);
                echo json_encode(["status" => "ERROR", "message" => "API disponible, pero sin conexión a la base de datos"]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => "ERROR", "message" => "Error al conectar con la base de datos", "error" => $e->getMessage()]);
        }
    }


    // Confirmar sincronización
    public function confirmarSincronizacion($idsFichajes)
    {
        try {
            $this->daoRegistros->marcarRegistrosComoRecogidos($idsFichajes);
            echo json_encode(["message" => "Sincronización confirmada"]);
        } catch (Exception $ex) {
            http_response_code(500);
            echo json_encode(["error" => "Error al confirmar sincronización: " . $ex->getMessage()]);
        }
    }

    // Obtener registros con usuarios
    public function obtenerRegistros()
    {
        try {
            $registros = $this->daoRegistros->obtenerRegistrosConUsuarios();
            echo json_encode($registros);
        } catch (Exception $ex) {
            http_response_code(500);
            echo json_encode(["error" => "Error al obtener registros: " . $ex->getMessage()]);
        }
    }

    // Obtener total de horas
    public function obtenerTotalHoras($token)
    {
        try {
            $totalHoras = $this->dAOTControlHoras->obtenerTotalHorasPorToken($token);
            if ($totalHoras !== null) {
                echo json_encode($totalHoras);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Token no encontrado"]);
            }
        } catch (Exception $ex) {
            http_response_code(500);
            echo json_encode(["error" => "Error al obtener el total de horas: " . $ex->getMessage()]);
        }
    }

    public function guardarRegistros()
    {
        // Obtener los datos enviados en el cuerpo de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);

        if (!is_array($data) || empty($data)) {
            http_response_code(400); // Código de error 400 Bad Request
            echo json_encode(["error" => "No se enviaron registros o el formato es incorrecto."]);
            return;
        }

        try {
            $db = new Database();
            $conn = $db->connect();

            $query = "
                INSERT INTO TRegistro (IdUsuario, FechaHora, Tipo, Incidencia, Token, TotalHoras)
                VALUES (:idUsuario, :fechaHora, :tipo, :incidencia, :token, :totalHoras)
            ";

            $stmt = $conn->prepare($query);

            foreach ($data as $registro) {
                // Extraer los campos necesarios
                $idUsuario = $registro['IdUsuario'] ?? null;
                $fechaHora = $registro['FechaHora'] ?? null;
                $tipo = $registro['Tipo'] ?? null;
                $incidencia = $registro['Incidencia'] ?? null;
                $token = $registro['Token'] ?? null;
                $totalHoras = $registro['TotalHoras'] ?? null;

                // Convertir FechaHora a formato compatible con MySQL (si es necesario)
                $fechaHora = date('Y-m-d H:i:s', strtotime($fechaHora));

                // Validar que los datos necesarios están presentes
                if (empty($idUsuario) || empty($fechaHora) || !isset($tipo)) {
                    file_put_contents('php://stderr', "Registro incompleto: " . print_r($registro, true) . "\n", FILE_APPEND);
                    continue; // Saltar registros incompletos
                }

                // Verificar el valor de Tipo para depuración
                file_put_contents('php://stderr', "Procesando registro con Tipo: $tipo\n", FILE_APPEND);

                // Vincular los valores a la consulta
                $stmt->bindParam(':idUsuario', $idUsuario);
                $stmt->bindParam(':fechaHora', $fechaHora);
                $stmt->bindParam(':tipo', $tipo);
                $stmt->bindParam(':incidencia', $incidencia);
                $stmt->bindParam(':token', $token);

                // Verificar si TotalHoras es nulo y manejarlo correctamente
                if ($totalHoras === null) {
                    $stmt->bindValue(':totalHoras', null, PDO::PARAM_NULL);
                } else {
                    $stmt->bindParam(':totalHoras', $totalHoras);
                }

                try {
                    // Ejecutar la consulta
                    $stmt->execute();
                    file_put_contents('php://stderr', "Registro insertado correctamente\n", FILE_APPEND);
                } catch (PDOException $e) {
                    // Capturar cualquier error al insertar
                    file_put_contents('php://stderr', "Error al insertar registro: " . $e->getMessage() . "\n", FILE_APPEND);
                }
            }

            echo json_encode(["success" => true, "message" => "Registros guardados correctamente."]);
        } catch (Exception $ex) {
            http_response_code(500);
            echo json_encode(["error" => "Error al guardar los registros.", "details" => $ex->getMessage()]);
        }
    }


}
