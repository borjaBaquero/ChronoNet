using System;
using System.Data.SqlServerCe;
using System.Data.SqlClient;
using System.Data;
using System.Windows;
using Microsoft.Data.Sqlite;
using System.IO;

namespace CL_CHRONO.Datos
{
    public class DBConnection : IDisposable
    {
        private SqliteConnection conexion;

        public void Dispose()
        {
            if (conexion.State != System.Data.ConnectionState.Closed)
            {
                conexion.Close();
            }
        }
        public DBConnection()
        {
            string directorioDatosLocal = Environment.GetFolderPath(Environment.SpecialFolder.LocalApplicationData);
            string nombreDirectorioAplicacion = "CL_CHRONO_USERS";
            string rutaDirectorioAplicacion = Path.Combine(directorioDatosLocal, nombreDirectorioAplicacion);
            if (!Directory.Exists(rutaDirectorioAplicacion))
            {
            }
            Directory.CreateDirectory(rutaDirectorioAplicacion);
            string nombreArchivoBaseDatos = "Sadyfi_users.db";
            string rutaArchivoBaseDatos = Path.Combine(rutaDirectorioAplicacion, nombreArchivoBaseDatos);
            conexion = new SqliteConnection($"Data Source={rutaArchivoBaseDatos};");
            Conectar();
            CrearTablas();
        }

        public void Conectar()
        {
            if (conexion.State != System.Data.ConnectionState.Open)
            {
                conexion.Open();
            }
        }

        public void CrearTablas()
        {
            // Crear las tablas necesarias
            string crearTablaRegistros = @"CREATE TABLE IF NOT EXISTS TRegistro (
                                            idRegistro INTEGER PRIMARY KEY AUTOINCREMENT,
                                            Tipo INTEGER NOT NULL,
                                            FechaHora DATETIME NOT NULL,
                                            Incidencia TEXT NOT NULL,
                                            Recogido INTEGER NOT NULL,
                                            idUsuario INTEGER NOT NULL,
                                            Token TEXT NOT NULL,
                                            FOREIGN KEY(idUsuario) REFERENCES TUsuario(idUsuario)
                                        );";

            string crearTablaUsuarios = @"CREATE TABLE IF NOT EXISTS TUsuario (
                                            idUsuario INTEGER PRIMARY KEY AUTOINCREMENT,
                                            DNI TEXT,
                                            Codigo TEXT NOT NULL,
                                            Nombre TEXT,
                                            Apellidos TEXT,
                                            AdminBool INTEGER 
                                        );";


            string crearTablaEmpresa = @"CREATE TABLE IF NOT EXISTS TEmpresa (
                                            idEmpresa INTEGER PRIMARY KEY AUTOINCREMENT,
                                            Nombre TEXT NOT NULL,
                                            CodEmpresa TEXT NOT NULL
                                        );";

            string crearTablaTControlHoras = @"CREATE TABLE IF NOT EXISTS TControlHoras (
                                            IdControlHoras INTEGER PRIMARY KEY AUTOINCREMENT,
                                            TotalHoras INTEGER NOT NULL,
                                            Fecha DATE NOT NULL,
                                            Token TEXT NOT NULL
                                        );";
            using (var comando = new SqliteCommand(crearTablaUsuarios, conexion))
            {
                comando.ExecuteNonQuery();
            }
            string verificarusuarioadmin = @"select count(*) from tusuario where nombre = 'administrador';";
            using (var comando = new SqliteCommand(verificarusuarioadmin, conexion))
            {
                int count = Convert.ToInt32(comando.ExecuteScalar());
                if (count == 0)
                {
                    string crear = @"insert into TUsuario (DNI, Codigo, Nombre, Apellidos, AdminBool) 
                                         values ('11111111', '1111', 'administrador', 'admin', 1);";
                    using (var comandoo = new SqliteCommand(crear, conexion))
                    {
                        comandoo.ExecuteNonQuery();
                    }
                }
            }
            using (var comando = new SqliteCommand(crearTablaRegistros, conexion))
            {
                comando.ExecuteNonQuery();
            }

            using (var comando = new SqliteCommand(crearTablaEmpresa, conexion))
            {
                comando.ExecuteNonQuery();
            }
            string verificarEmpresa = @"SELECT COUNT(*) FROM TEmpresa WHERE Nombre = 'Sadyfi';";
            using (var comando = new SqliteCommand(verificarEmpresa, conexion))
            {
                int count = Convert.ToInt32(comando.ExecuteScalar());
                if (count == 0)
                {
                    string crear = @"INSERT INTO TEmpresa (Nombre, CodEmpresa) 
                                         VALUES ('Sadyfi', 'SAD-');";
                    using (var comandoo = new SqliteCommand(crear, conexion))
                    {
                        comandoo.ExecuteNonQuery();
                    }
                }
            }
            using (var comando = new SqliteCommand(crearTablaTControlHoras, conexion))
            {
                comando.ExecuteNonQuery();
            }
        }

        public SqliteConnection GetConnection()
        {
            return conexion;
        }
    }
}
