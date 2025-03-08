<?php
class Database {
    private $host = "127.0.0.1";
    private $db_name = "db_chrononet";
    private $username = "user_db";
    private $password = "kM0xa^156";
    private $port = "3306"; // Puerto predeterminado de MySQL
    public $conn;

    public function connect() {
        $this->conn = null;
        try {
            // Conexión a la base de datos con el puerto especificado
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->db_name};charset=utf8mb4";
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            // No expongas detalles del error en producción
            error_log("Error de conexión: " . $exception->getMessage());
            echo "Error de conexión a la base de datos.";
        }
        return $this->conn;
    }
}
