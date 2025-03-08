<?php

require_once __DIR__ . '/../config/db.php';

class UserController
{
    public function verificarOCrearUsuario()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!is_array($data) || empty($data)) {
            http_response_code(400);
            echo json_encode(["error" => "No se enviaron datos o el formato es incorrecto."]);
            return;
        }

        try {
            // Filtrar los campos necesarios
            $dni = $data['Dni'] ?? null;
            $nombre = $data['Nombre'] ?? null;
            $apellidos = $data['Apellidos'] ?? null;
            $codigo = $data['Codigo'] ?? null;
            $adminBool = $data['AdminBool'] ?? null;
            $token = $data['Token'] ?? null;

            // Validar campos requeridos
            if (empty($dni) || empty($nombre) || empty($apellidos) || empty($codigo) || !isset($adminBool) || empty($token)) {
                http_response_code(400);
                echo json_encode(["error" => "Datos incompletos"]);
                return;
            }

            // Extraer el prefijo del Token (hasta el primer guion '-')
            $tokenPrefix = substr($token, 0, strpos($token, '-') + 1);
            file_put_contents('php://stderr', "Token Prefix: $tokenPrefix\n", FILE_APPEND);

            $db = new Database();
            $conn = $db->connect();

            // Usar el Prefijo del Token para buscar la empresa
            $queryEmpresa = "SELECT IdEmpresa FROM tempresa WHERE CodEmpresa = :codEmpresa";
            $stmtEmpresa = $conn->prepare($queryEmpresa);
            $stmtEmpresa->bindParam(':codEmpresa', $tokenPrefix);
            $stmtEmpresa->execute();
            $empresa = $stmtEmpresa->fetch(PDO::FETCH_ASSOC);

            if (!$empresa) {
                http_response_code(404);
                echo json_encode(["error" => "No se encontró una empresa con el Token proporcionado."]);
                return;
            }

            $idEmpresa = $empresa['IdEmpresa'];

            // Verificar si el usuario ya existe
            $queryUsuario = "SELECT IdUsuario, IdEmpresa FROM TUsuarios WHERE Login = :dni";
            $stmtUsuario = $conn->prepare($queryUsuario);
            $stmtUsuario->bindParam(':dni', $dni);
            $stmtUsuario->execute();
            $usuario = $stmtUsuario->fetch(PDO::FETCH_ASSOC);

            if ($usuario) {
                // Si el usuario ya existe, verificar si el IdEmpresa ha cambiado
                if ($usuario['IdEmpresa'] != $idEmpresa) {
                    $queryActualizarEmpresa = "UPDATE TUsuarios SET IdEmpresa = :idEmpresa WHERE IdUsuario = :idUsuario";
                    $stmtActualizar = $conn->prepare($queryActualizarEmpresa);
                    $stmtActualizar->bindParam(':idEmpresa', $idEmpresa);
                    $stmtActualizar->bindParam(':idUsuario', $usuario['IdUsuario']);
                    $stmtActualizar->execute();
                }

                echo json_encode(["IdUsuario" => $usuario['IdUsuario']]);
                return;
            }

            // Si el usuario no existe, crearlo
            $queryInsertar = "
                INSERT INTO TUsuarios (Login, Codigo, Nombre, Apellidos, AdminBool, IdEmpresa) 
                VALUES (:dni, :codigo, :nombre, :apellidos, :adminBool, :idEmpresa)
            ";
            $stmtInsertar = $conn->prepare($queryInsertar);

            $stmtInsertar->bindParam(':dni', $dni);
            $stmtInsertar->bindParam(':codigo', $codigo);
            $stmtInsertar->bindParam(':nombre', $nombre);
            $stmtInsertar->bindParam(':apellidos', $apellidos);
            $stmtInsertar->bindParam(':adminBool', $adminBool, PDO::PARAM_BOOL);
            $stmtInsertar->bindParam(':idEmpresa', $idEmpresa);

            $stmtInsertar->execute();

            // Obtener el IdUsuario del usuario recién creado
            $idUsuario = $conn->lastInsertId();

            echo json_encode(["IdUsuario" => $idUsuario]);
        } catch (Exception $ex) {
            http_response_code(500);
            echo json_encode(["error" => "Error al verificar o crear el usuario.", "details" => $ex->getMessage()]);
        }
    }

}
