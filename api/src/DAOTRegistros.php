<?php

require_once __DIR__ . '/../config/db.php';

class DAOTRegistros
{
    public function obtenerRegistrosNoSincronizados()
    {
        $results = [];
        $query = "SELECT idRegistro, Tipo, FechaHora, Incidencia, idUsuario, Token FROM TRegistro";

        $db = new Database(); // Cambiado de DBConnection a Database
        $conn = $db->connect();

        $stmt = $conn->prepare($query);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $row) {
            $results[] = $row;
        }

        return $results;
    }

    public function marcarRegistrosComoRecogidos($idsFichajes)
    {
        if (empty($idsFichajes)) {
            return;
        }

        $placeholders = implode(',', array_fill(0, count($idsFichajes), '?'));
        $query = "UPDATE TRegistro SET Recogido = 1 WHERE idRegistro IN ($placeholders)";

        $db = new Database(); // Cambiado de DBConnection a Database
        $conn = $db->connect();

        $stmt = $conn->prepare($query);
        $stmt->execute($idsFichajes);
    }

    public function obtenerRegistrosConUsuarios()
    {
        $results = [];
        $query = "
            SELECT TRegistro.idRegistro, TRegistro.Tipo, TRegistro.FechaHora, TRegistro.Incidencia, 
                   TRegistro.Recogido, TRegistro.Token, TUsuario.Dni, TUsuario.Nombre, 
                   TUsuario.Apellidos, TUsuario.Codigo 
            FROM TRegistro 
            INNER JOIN TUsuario ON TRegistro.idUsuario = TUsuario.idUsuario";

        $db = new Database(); // Cambiado de DBConnection a Database
        $conn = $db->connect();

        $stmt = $conn->prepare($query);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $row) {
            $results[] = $row;
        }

        return $results;
    }
}
