<?php
    class DAORegistros {
        private $conn;
    
        // Constructor para establecer la conexión
        public function __construct($connection) {
            $this->conn = $connection;
        }
    
        public function obtenerRegistrosHorasTrabajadas($conn){
            $queryHorasTrabajadas = "SELECT r.IdRegistro, r.IdUsuario, (u.Nombre + ' ' + u.Apellidos) AS nombrecompleto, r.Tipo, r.FechaHora, r.Incidencia 
                FROM TRegistro r 
                JOIN TUsuarios u ON u.IdUsuario = r.IdUsuario";
            
            $resultHoras = sqlsrv_query($conn, $queryHorasTrabajadas);

            if ($resultHoras === false) {
                die(print_r(sqlsrv_errors(), true));
            }
            return $resultHoras;
        }

        public function obtenerRegistrosFechaHora($conn, $filtroFechaHora, $filtroNombreCompleto){
            $query = "SELECT r.IdRegistro, r.IdUsuario, (u.Nombre + ' ' + u.Apellidos) AS nombrecompleto, r.Tipo, r.FechaHora, r.Incidencia 
                FROM TRegistro r 
                JOIN TUsuarios u ON u.IdUsuario = r.IdUsuario";

            // Agregar el filtro de fecha y hora si se ha proporcionado
            if (!empty($filtroFechaHora)) {
                // Ajustar el formato de fecha y hora para que coincida con el de la base de datos
                $filtroFechaHora = date('Y-m-d H:i:s', strtotime($filtroFechaHora));
                $query .= " WHERE CONVERT(varchar, r.FechaHora, 120) LIKE '%$filtroFechaHora%'";
            }

            if (!empty($filtroNombreCompleto)) {
                // Agregar el filtro de nombre completo si se ha proporcionado
                $query .= " AND (u.Nombre + ' ' + u.Apellidos) LIKE '%$filtroNombreCompleto%'";
            }

            // Ordenar por usuario y fecha/hora
            $query .= " ORDER BY r.FechaHora DESC";

            $result = sqlsrv_query($conn, $query);

            if ($result === false) {
                die(print_r(sqlsrv_errors(), true));
            }else{
                return $result;
            }
        
        }

        // Función para cerrar la conexión (opcional)
        public function cerrarConexion() {
            sqlsrv_close($this->conn);
        }
    }