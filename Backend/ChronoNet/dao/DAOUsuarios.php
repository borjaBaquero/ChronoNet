<?php
    class DAOUsuarios {
        private $conn;
    
        // Constructor para establecer la conexión
        public function __construct($connection) {
            $this->conn = $connection;
        }
    
        // Función para obtener usuarios con paginación
        public function obtenerUsuariosConPaginacion($offset, $usuariosPorPagina) {
            $queryUsuarios = "
                WITH UsuariosCTE AS (
                    SELECT *,
                           ROW_NUMBER() OVER (ORDER BY IdUsuario ASC) AS RowNum
                    FROM TUsuarios
                )
                SELECT *
                FROM UsuariosCTE
                WHERE RowNum BETWEEN ? AND ?
            ";
    
            $paramsUsuarios = array($offset + 1, $offset + $usuariosPorPagina);
            $resultUsuarios = sqlsrv_query($this->conn, $queryUsuarios, $paramsUsuarios);
    
            if ($resultUsuarios === false) {
                die(print_r(sqlsrv_errors(), true));
            }
    
            $usuarios = [];
            while ($row = sqlsrv_fetch_array($resultUsuarios, SQLSRV_FETCH_ASSOC)) {
                $usuarios[] = $row;
            }

            sqlsrv_free_stmt($resultUsuarios);
            
            return $usuarios;
        }
    
        // Función para cerrar la conexión (opcional)
        public function cerrarConexion() {
            sqlsrv_close($this->conn);
        }
    }