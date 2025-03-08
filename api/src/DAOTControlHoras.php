<?php

require_once __DIR__ . '/../config/db.php'; // Ruta al archivo de conexión

class DAOTControlHoras
{
    public function obtenerTotalHorasPorToken($token)
    {
        $query = "SELECT TotalHoras FROM TControlHoras WHERE Token = ?";

        $db = new DBConnection();
        $conn = $db->getConnection();

        $stmt = $conn->prepare($query);
        $stmt->execute([$token]);
        $result = $stmt->fetch(PDO::FETCH_COLUMN);

        return $result !== false ? (float)$result : null;
    }
}
